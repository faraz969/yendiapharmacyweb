<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isBranchStaff = $user->isBranchStaff();
        
        // Base query for orders
        $ordersQuery = Order::query();
        
        // If branch staff, filter by their branch
        if ($isBranchStaff) {
            $ordersQuery->where('branch_id', $user->branch_id);
        }
        
        // Calculate today's sales (delivered orders from today)
        $todaySalesQuery = (clone $ordersQuery)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today());
        
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => (clone $ordersQuery)->count(),
            'pending_orders' => (clone $ordersQuery)->where('status', 'pending')->count(),
            'approved_orders' => (clone $ordersQuery)->where('status', 'approved')->count(),
            'delivered_orders' => (clone $ordersQuery)->where('status', 'delivered')->count(),
            'today_sales' => $todaySalesQuery->sum('total_amount'),
            'total_revenue' => (clone $ordersQuery)->where('status', 'delivered')->sum('total_amount'),
        ];

        $recent_orders = $ordersQuery->with('user')->latest()->take(10)->get();
        $branch = $isBranchStaff ? $user->branch : null;

        return view('admin.dashboard', compact('stats', 'recent_orders', 'isBranchStaff', 'branch'));
    }

    public function users()
    {
        $users = User::with('roles')->paginate(20);
        return view('admin.users.index', compact('users'));
    }
}
