<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->hasRole('delivery_person')) {
                abort(403, 'Access denied. Only delivery persons can access this page.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get assigned orders
        $assignedOrders = Order::where('delivered_by', $user->id)
            ->where('status', 'out_for_delivery')
            ->with(['items.product', 'branch', 'user'])
            ->latest()
            ->get();
        
        // Get delivered orders (recent)
        $deliveredOrders = Order::where('delivered_by', $user->id)
            ->where('status', 'delivered')
            ->with(['items.product', 'branch', 'user'])
            ->latest()
            ->take(10)
            ->get();
        
        // Statistics
        $stats = [
            'active_deliveries' => $assignedOrders->count(),
            'total_delivered_today' => Order::where('delivered_by', $user->id)
                ->where('status', 'delivered')
                ->whereDate('delivered_at', today())
                ->count(),
            'total_delivered_this_month' => Order::where('delivered_by', $user->id)
                ->where('status', 'delivered')
                ->whereMonth('delivered_at', now()->month)
                ->whereYear('delivered_at', now()->year)
                ->count(),
        ];
        
        return view('delivery.dashboard', compact('assignedOrders', 'deliveredOrders', 'stats'));
    }

    public function markDelivered(Order $order)
    {
        $user = Auth::user();
        
        // Ensure order is assigned to this delivery person
        if ($order->delivered_by !== $user->id) {
            abort(403, 'Access denied. This order is not assigned to you.');
        }
        
        if ($order->status !== 'out_for_delivery') {
            return back()->with('error', 'Order must be out for delivery.');
        }
        
        $oldStatus = $order->status;
        $order->markAsDelivered();
        
        // Load user relationship for SMS notification
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }
        $order->notifyStatusChange($oldStatus);
        
        return back()->with('success', 'Order marked as delivered successfully.');
    }

    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Ensure order is assigned to this delivery person
        if ($order->delivered_by !== $user->id) {
            abort(403, 'Access denied. This order is not assigned to you.');
        }
        
        $order->load(['items.product', 'items.batch', 'prescription', 'branch', 'user', 'deliveryZone']);
        
        return view('delivery.order.show', compact('order'));
    }
}

