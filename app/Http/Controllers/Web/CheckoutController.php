<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\DeliveryZone;
use App\Models\Prescription;
use App\Models\Batch;
use App\Models\DeliveryAddress;
use App\Models\Branch;
use App\Models\Notification;
use App\Helpers\OrderHelper;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $cartItems = [];
        $subtotal = 0;
        $requiresPrescription = false;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active && !$product->is_expired) {
                $itemTotal = $item['quantity'] * $item['price'];
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $itemTotal,
                ];
                $subtotal += $itemTotal;
                
                if ($product->requires_prescription) {
                    $requiresPrescription = true;
                }
            }
        }

        $deliveryZones = DeliveryZone::where('is_active', true)->get();
        $defaultZone = $deliveryZones->first();
        $deliveryFee = $defaultZone ? $defaultZone->calculateDeliveryFee($subtotal) : 0;
        $total = $subtotal + $deliveryFee;

        // Get saved addresses if user is authenticated
        $savedAddresses = [];
        $defaultAddress = null;
        $user = null;
        if (Auth::check()) {
            $user = Auth::user()->load('profile');
            $savedAddresses = $user->deliveryAddresses()->latest()->get();
            $defaultAddress = $user->defaultDeliveryAddress;
        }

        // Get active branches for selection
        $branches = Branch::active()->ordered()->get();

        return view('web.checkout.index', compact('cartItems', 'subtotal', 'deliveryFee', 'total', 'deliveryZones', 'requiresPrescription', 'savedAddresses', 'defaultAddress', 'branches', 'user'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'delivery_address_id' => 'nullable|exists:delivery_addresses,id',
            'customer_name' => 'required_without:delivery_address_id|string|max:255',
            'customer_phone' => 'required_without:delivery_address_id|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'delivery_address' => 'required_without:delivery_address_id|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'prescription_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doctor_name' => 'nullable|string|max:255',
            'patient_name' => 'nullable|string|max:255',
            'prescription_date' => 'nullable|date',
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
            $validated['latitude'] = $savedAddress->latitude;
            $validated['longitude'] = $savedAddress->longitude;
            $deliveryAddressId = $savedAddress->id;
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $cartItems = [];
            $subtotal = 0;
            $requiresPrescription = false;

            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product && $product->is_active && !$product->is_expired) {
                    $itemTotal = $item['quantity'] * $item['price'];
                    $cartItems[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ];
                    $subtotal += $itemTotal;
                    
                    if ($product->requires_prescription) {
                        $requiresPrescription = true;
                    }
                }
            }

            // Calculate delivery fee
            $deliveryZone = null;
            $deliveryFee = 0;
            if ($request->delivery_zone_id) {
                $deliveryZone = DeliveryZone::find($request->delivery_zone_id);
                if ($deliveryZone) {
                    $deliveryFee = $deliveryZone->calculateDeliveryFee($subtotal);
                }
            }

            $total = $subtotal + $deliveryFee;

            // Handle prescription
            $prescription = null;
            if ($requiresPrescription && $request->hasFile('prescription_file')) {
                $filePath = $request->file('prescription_file')->store('prescriptions', 'public');
                
                $prescription = Prescription::create([
                    'user_id' => auth()->id(), // Can be null for guest checkout
                    'branch_id' => $validated['branch_id'],
                    'prescription_number' => OrderHelper::generatePrescriptionNumber(),
                    'doctor_name' => $validated['doctor_name'] ?? null,
                    'patient_name' => $validated['patient_name'] ?? $validated['customer_name'],
                    'prescription_date' => $validated['prescription_date'] ?? now(),
                    'customer_phone' => $validated['customer_phone'],
                    'customer_email' => $validated['customer_email'] ?? null,
                    'file_path' => $filePath,
                    'status' => 'pending',
                ]);
            }

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(), // Can be null for guest checkout
                'branch_id' => $validated['branch_id'],
                'prescription_id' => $prescription ? $prescription->id : null,
                'delivery_address_id' => $deliveryAddressId,
                'delivery_zone_id' => $request->delivery_zone_id,
                'order_number' => OrderHelper::generateOrderNumber(),
                'status' => 'pending',
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'delivery_address' => $validated['delivery_address'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => 0,
                'total_amount' => $total,
            ]);

            // Create order items and allocate batches (FIFO)
            foreach ($cartItems as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];
                $batchId = null;

                // Allocate from batches if tracking batches
                if ($product->track_batch) {
                    $availableBatches = $product->getAvailableBatches();
                    $remainingQty = $quantity;

                    foreach ($availableBatches as $batch) {
                        if ($remainingQty <= 0) break;

                        $qtyFromBatch = min($remainingQty, $batch->available_quantity);
                        $batch->reduceStock($qtyFromBatch);
                        $remainingQty -= $qtyFromBatch;

                        if (!$batchId) {
                            $batchId = $batch->id; // Use first batch for order item
                        }
                    }

                    if ($remainingQty > 0) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'batch_id' => $batchId,
                    'quantity' => $quantity,
                    'unit_price' => $item['price'],
                    'total_price' => $quantity * $item['price'],
                ]);
            }

            DB::commit();

            // Log the order placement activity
            ActivityLogService::logAction(
                'place_order',
                "Order #{$order->order_number} placed with total amount: " . \App\Models\Setting::formatPrice($order->total_amount),
                $order,
                [
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'item_count' => count($cartItems),
                    'branch_id' => $order->branch_id,
                ],
                $request
            );

            // Create notification for user when order is placed (if user is logged in)
            // SMS will be sent after payment is confirmed
            if ($order->user_id) {
                $notificationMessage = "Your order #{$order->order_number} has been placed successfully. Total amount: " . \App\Models\Setting::formatPrice($order->total_amount);
                
                Notification::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'title' => 'Order Placed Successfully',
                    'message' => $notificationMessage,
                    'type' => 'success',
                    'link' => route('user.orders.show', $order->id),
                    'is_active' => true,
                    'is_read' => false,
                ]);
            }

            // Don't clear cart yet - wait for payment confirmation
            // Return order ID for payment processing
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                ]);
            }

            // For web, redirect to payment page
            return redirect()->route('checkout.payment', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error placing order: ' . $e->getMessage());
        }
    }

    public function payment(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Order has already been paid.');
        }

        $order->load(['items.product', 'prescription']);
        return view('web.checkout.payment', compact('order'));
    }

    public function success(Order $order)
    {
        $order->load(['items.product', 'prescription']);
        
        // Clear cart if payment is successful
        if ($order->payment_status === 'paid') {
            session()->forget('cart');
        }
        
        return view('web.checkout.success', compact('order'));
    }
}
