<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(20);
        $statuses = ['pending', 'approved', 'rejected', 'packing', 'packed', 'out_for_delivery', 'delivered', 'cancelled'];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'prescription', 'approvedBy', 'packedBy', 'deliveredBy', 'deliveryZone']);
        return view('admin.orders.show', compact('order'));
    }

    public function approve(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be approved.');
        }

        $order->approve(Auth::id(), $request->notes);

        return back()->with('success', 'Order approved successfully.');
    }

    public function reject(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $order->reject(Auth::id(), $request->rejection_reason);

        return back()->with('success', 'Order rejected.');
    }

    public function pack(Request $request, Order $order)
    {
        if (!in_array($order->status, ['approved', 'packing'])) {
            return back()->with('error', 'Order must be approved before packing.');
        }

        $order->markAsPacked(Auth::id());

        return back()->with('success', 'Order marked as packed.');
    }

    public function deliver(Request $request, Order $order)
    {
        if ($order->status !== 'packed') {
            return back()->with('error', 'Order must be packed before delivery.');
        }

        $request->validate([
            'delivery_person_id' => 'required|exists:users,id',
        ]);

        $deliveryPerson = User::find($request->delivery_person_id);
        if (!$deliveryPerson->hasRole('delivery_person')) {
            return back()->with('error', 'Selected user is not a delivery person.');
        }

        $order->assignForDelivery($request->delivery_person_id);

        return back()->with('success', 'Order assigned for delivery.');
    }

    public function markDelivered(Order $order)
    {
        if ($order->status !== 'out_for_delivery') {
            return back()->with('error', 'Order must be out for delivery.');
        }

        $order->markAsDelivered();

        return back()->with('success', 'Order marked as delivered.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,packing,packed,out_for_delivery,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated.');
    }
}
