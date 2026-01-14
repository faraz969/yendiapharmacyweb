<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\DeliveryZone;
use App\Models\DeliveryAddress;
use App\Models\Notification;
use App\Helpers\OrderHelper;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // If user is branch staff, show only their branch orders
        // Otherwise, show only their own orders
        $query = Order::with(['items.product', 'deliveryZone', 'prescription', 'branch']);
        
        if ($user->isBranchStaff()) {
            // Branch staff can see all orders for their branch
            $query->where('branch_id', $user->branch_id);
        } else {
            // Regular users see only their own orders
            $query->where('user_id', $user->id);
        }
        
        $query->latest();

        $orders = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Custom validation: delivery_address is required for delivery orders unless delivery_address_id is provided
        $rules = [
            'branch_id' => 'required|exists:branches,id',
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address_id' => 'nullable|exists:delivery_addresses,id',
            'customer_name' => 'required_without:delivery_address_id|string|max:255',
            'customer_phone' => 'required_without:delivery_address_id|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ];
        
        // Add delivery_address validation conditionally
        if ($request->input('delivery_type') === 'delivery' && !$request->input('delivery_address_id')) {
            $rules['delivery_address'] = 'required|string';
        } else {
            $rules['delivery_address'] = 'nullable|string';
        }
        
        $validated = $request->validate($rules);

        $deliveryType = $validated['delivery_type'] ?? 'delivery';
        
        // If using saved address, get the address data (only for delivery orders)
        $deliveryAddressId = null;
        if ($deliveryType === 'delivery' && $request->delivery_address_id && Auth::check()) {
            $savedAddress = DeliveryAddress::where('user_id', Auth::id())
                ->findOrFail($request->delivery_address_id);
            
            $validated['customer_name'] = $savedAddress->contact_name;
            $validated['customer_phone'] = $savedAddress->contact_phone;
            $validated['customer_email'] = $savedAddress->contact_email;
            $validated['delivery_address'] = $savedAddress->full_address;
            $deliveryAddressId = $savedAddress->id;
        }

        // Get delivery_zone_id from request (only for delivery orders)
        $deliveryZoneId = ($deliveryType === 'delivery') ? $request->input('delivery_zone_id') : null;

        // Check if any product requires prescription
        $requiresPrescription = false;
        $prescriptionRequiredProducts = [];
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->requires_prescription) {
                $requiresPrescription = true;
                $prescriptionRequiredProducts[] = $product->name;
            }
        }

        // Validate prescription if required
        if ($requiresPrescription) {
            // Check if prescription images are provided (for mobile app, this might be sent as a flag or files)
            $hasPrescription = $request->has('prescription_images') || 
                              $request->hasFile('prescription_images') ||
                              $request->has('prescription_uploaded') ||
                              $request->input('prescription_uploaded', false);
            
            if (!$hasPrescription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prescription image(s) are required for the following products: ' . implode(', ', $prescriptionRequiredProducts),
                    'errors' => [
                        'prescription' => ['Prescription image(s) are required for prescription-required products.']
                    ]
                ], 422);
            }
        }

        return DB::transaction(function () use ($validated, $deliveryAddressId, $deliveryZoneId, $deliveryType, $request) {
            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            // Calculate delivery fee (only for delivery orders)
            $deliveryFee = 0;
            if ($deliveryType === 'delivery' && $deliveryZoneId) {
                $deliveryZone = DeliveryZone::find($deliveryZoneId);
                if ($deliveryZone) {
                    $deliveryFee = $deliveryZone->calculateDeliveryFee($subtotal);
                }
            }

            $totalAmount = $subtotal + $deliveryFee;

            // Get user_id - use sanctum guard for API authentication
            // The route is public to allow guest checkout, but we still check for authenticated users
            $userId = Auth::guard('sanctum')->id();

            // Create order
            $order = Order::create([
                'user_id' => $userId, // Will be null for guest users
                'branch_id' => $validated['branch_id'],
                'delivery_address_id' => $deliveryAddressId,
                'delivery_zone_id' => $deliveryZoneId,
                'delivery_type' => $deliveryType,
                'order_number' => OrderHelper::generateOrderNumber(),
                'status' => 'pending',
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'delivery_address' => $deliveryType === 'delivery' ? ($validated['delivery_address'] ?? null) : null,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => 0,
                'total_amount' => $totalAmount,
            ]);

            // Create order items
            foreach ($validated['items'] as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['price'],
                    'total_price' => $itemData['quantity'] * $itemData['price'],
                ]);
            }

            $order->load(['items.product', 'deliveryZone', 'prescription']);

            // Log the order placement activity
            ActivityLogService::logAction(
                'place_order',
                "Order #{$order->order_number} placed via mobile app with total amount: " . \App\Models\Setting::formatPrice($order->total_amount),
                $order,
                [
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'item_count' => count($validated['items']),
                    'branch_id' => $order->branch_id,
                    'source' => 'mobile_app',
                ],
                $request
            );

            // Create notification for user when order is placed
            // SMS will be sent after payment is confirmed
            if ($order->user_id) {
                $notificationMessage = "Your order #{$order->order_number} has been placed successfully. Total amount: " . \App\Models\Setting::formatPrice($order->total_amount);
                
                Notification::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'title' => 'Order Placed Successfully',
                    'message' => $notificationMessage,
                    'type' => 'success',
                    'link' => '/orders/' . $order->id,
                    'is_active' => true,
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $order,
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with(['items.product', 'items.batch', 'prescription', 'deliveryZone', 'approvedBy', 'packedBy', 'deliveredBy'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Cancel an order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->findOrFail($id);

        // Only allow cancellation if order is pending or approved
        if (!in_array($order->status, ['pending', 'approved'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled. Only pending or approved orders can be cancelled.',
            ], 400);
        }

        // Check if order has been paid - if so, require refund request
        if ($order->payment_status === 'paid') {
            // Check if refund request already exists
            if ($order->refundRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'A refund request already exists for this order.',
                    'refund_request_id' => $order->refundRequest->id,
                ], 400);
            }

            // Return response indicating refund request is needed
            return response()->json([
                'success' => false,
                'message' => 'This order has been paid. Please submit a refund request to cancel it.',
                'requires_refund_request' => true,
                'order_id' => $order->id,
            ], 400);
        }

        $oldStatus = $order->status;
        
        $order->update([
            'status' => 'cancelled',
        ]);

        // Notify status change (will send SMS)
        $order->notifyStatusChange($oldStatus);

        // Log the cancellation activity
        try {
            ActivityLogService::logAction(
                'cancel_order',
                "Order #{$order->order_number} cancelled by customer",
                $order,
                [
                    'order_number' => $order->order_number,
                    'previous_status' => $oldStatus,
                    'source' => 'mobile_app',
                ],
                request()
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log order cancellation activity', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order->fresh(['items.product', 'items.batch', 'prescription', 'deliveryZone', 'approvedBy', 'packedBy', 'deliveredBy']),
        ]);
    }

    /**
     * Track order by order number and phone
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function track(Request $request, $id)
    {
        $request->validate([
            'order_number' => 'required|string',
            'customer_phone' => 'required|string',
        ]);

        $order = Order::with(['items.product', 'prescription', 'deliveryZone'])
            ->where('order_number', $request->order_number)
            ->where('customer_phone', $request->customer_phone)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }
}
