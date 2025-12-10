<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'approved_orders' => Order::where('status', 'approved')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
        ];

        $recent_orders = Order::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recent_orders'));
    }

    public function users()
    {
        $users = User::with('roles')->paginate(20);
        return view('admin.users.index', compact('users'));
    }
}
