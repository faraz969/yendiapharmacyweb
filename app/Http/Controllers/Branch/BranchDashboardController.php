<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is branch staff
        if (!$user->isBranchStaff()) {
            abort(403, 'Access denied. You must be assigned to a branch.');
        }

        $branch = $user->branch;
        
        // Get orders for this branch
        $query = Order::with(['items.product', 'user'])
            ->where('branch_id', $branch->id);

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
        
        // Statistics
        $stats = [
            'total' => Order::where('branch_id', $branch->id)->count(),
            'pending' => Order::where('branch_id', $branch->id)->where('status', 'pending')->count(),
            'approved' => Order::where('branch_id', $branch->id)->where('status', 'approved')->count(),
            'packed' => Order::where('branch_id', $branch->id)->where('status', 'packed')->count(),
            'delivered' => Order::where('branch_id', $branch->id)->where('status', 'delivered')->count(),
        ];

        $statuses = ['pending', 'approved', 'rejected', 'packing', 'packed', 'out_for_delivery', 'delivered', 'cancelled'];

        return view('branch.dashboard', compact('orders', 'statuses', 'stats', 'branch'));
    }

    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Check if user is branch staff and order belongs to their branch
        if (!$user->isBranchStaff() || $order->branch_id !== $user->branch_id) {
            abort(403, 'Access denied.');
        }

        $order->load(['user', 'items.product', 'prescription', 'approvedBy', 'packedBy', 'deliveredBy', 'deliveryZone', 'branch']);
        
        return view('branch.order.show', compact('order'));
    }
}
