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
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'delivery_address_id' => 'nullable|exists:delivery_addresses,id',
            'customer_name' => 'required_without:delivery_address_id|string|max:255',
            'customer_phone' => 'required_without:delivery_address_id|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_address' => 'required_without:delivery_address_id|string',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // If using saved address, get the address data
        $deliveryAddressId = null;
        if ($request->delivery_address_id && Auth::check()) {
            $savedAddress = DeliveryAddress::where('user_id', Auth::id())
                ->findOrFail($request->delivery_address_id);
            
            $validated['customer_name'] = $savedAddress->contact_name;
            $validated['customer_phone'] = $savedAddress->contact_phone;
            $validated['customer_email'] = $savedAddress->contact_email;
            $validated['delivery_address'] = $savedAddress->full_address;
            $deliveryAddressId = $savedAddress->id;
        }

        // Get delivery_zone_id from request (may not be in validated if null)
        $deliveryZoneId = $request->input('delivery_zone_id');

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

        return DB::transaction(function () use ($validated, $deliveryAddressId, $deliveryZoneId) {
            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            // Calculate delivery fee
            $deliveryFee = 0;
            if ($deliveryZoneId) {
                $deliveryZone = DeliveryZone::find($deliveryZoneId);
                if ($deliveryZone) {
                    $deliveryFee = $deliveryZone->calculateDeliveryFee($subtotal);
                }
            }

            $totalAmount = $subtotal + $deliveryFee;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(), // Will be null if not authenticated (but route requires auth)
                'branch_id' => $validated['branch_id'],
                'delivery_address_id' => $deliveryAddressId,
                'delivery_zone_id' => $deliveryZoneId,
                'order_number' => OrderHelper::generateOrderNumber(),
                'status' => 'pending',
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'delivery_address' => $validated['delivery_address'],
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
