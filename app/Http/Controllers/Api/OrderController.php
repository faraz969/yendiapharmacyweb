<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DeliveryZone;
use App\Models\DeliveryAddress;
use App\Helpers\OrderHelper;
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
        $query = Order::with(['items.product', 'deliveryZone', 'prescription'])
            ->where('user_id', Auth::id())
            ->latest();

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
        if ($request->delivery_address_id) {
            $savedAddress = DeliveryAddress::where('user_id', Auth::id())
                ->findOrFail($request->delivery_address_id);
            
            $validated['customer_name'] = $savedAddress->contact_name;
            $validated['customer_phone'] = $savedAddress->contact_phone;
            $validated['customer_email'] = $savedAddress->contact_email;
            $validated['delivery_address'] = $savedAddress->full_address;
            $deliveryAddressId = $savedAddress->id;
        }

        return DB::transaction(function () use ($validated, $deliveryAddressId) {
            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            // Calculate delivery fee
            $deliveryFee = 0;
            if ($validated['delivery_zone_id']) {
                $deliveryZone = DeliveryZone::find($validated['delivery_zone_id']);
                if ($deliveryZone) {
                    $deliveryFee = $deliveryZone->calculateDeliveryFee($subtotal);
                }
            }

            $totalAmount = $subtotal + $deliveryFee;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'delivery_address_id' => $deliveryAddressId,
                'delivery_zone_id' => $validated['delivery_zone_id'],
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
