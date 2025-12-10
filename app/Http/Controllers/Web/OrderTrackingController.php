<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('web.order-tracking.index');
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->where('customer_phone', $request->customer_phone)
            ->with(['items.product', 'prescription', 'deliveryZone'])
            ->first();

        if (!$order) {
            return back()->with('error', 'Order not found. Please check your order number and phone number.');
        }

        return view('web.order-tracking.show', compact('order'));
    }
}

