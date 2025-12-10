<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'deliveryZone'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'delivered_orders' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
        ];

        return view('web.user.dashboard', compact('orders', 'stats'));
    }
}
