<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'deliveryZone', 'prescription'])
            ->latest()
            ->paginate(8);

        return view('web.user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $order->load(['items.product', 'items.batch', 'prescription', 'deliveryZone', 'approvedBy', 'packedBy', 'deliveredBy', 'refundRequest']);

        return view('web.user.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow cancellation if order is pending or approved
        if (!in_array($order->status, ['pending', 'approved'])) {
            return redirect()->route('user.orders.show', $order)
                ->with('error', 'Order cannot be cancelled. Only pending or approved orders can be cancelled.');
        }

        // Check if order has been paid - if so, redirect to refund request
        if ($order->payment_status === 'paid') {
            return redirect()->route('user.refund-requests.create', $order);
        }

        // Cancel unpaid order
        $oldStatus = $order->status;
        $order->update([
            'status' => 'cancelled',
        ]);

        // Notify status change
        $order->notifyStatusChange($oldStatus);

        return redirect()->route('user.orders.show', $order)
            ->with('success', 'Order cancelled successfully.');
    }
}
