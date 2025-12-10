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
            ->paginate(10);

        return view('web.user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $order->load(['items.product', 'items.batch', 'prescription', 'deliveryZone', 'approvedBy', 'packedBy', 'deliveredBy']);

        return view('web.user.orders.show', compact('order'));
    }
}
