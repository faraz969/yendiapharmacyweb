<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function profitLoss(Request $request)
    {
        // Get today's date range
        $date = $request->has('date') ? Carbon::parse($request->date) : Carbon::today();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Total Revenue (delivered orders)
        $revenue = Order::where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('total_amount');

        // Total Refunds (rejected and cancelled orders)
        $refunds = Order::whereIn('status', ['rejected', 'cancelled'])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('total_amount');

        // Calculate Cost of Goods Sold (COGS)
        // Get all delivered orders for today
        $deliveredOrders = Order::where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->with('items.product')
            ->get();

        $cogs = 0;
        foreach ($deliveredOrders as $order) {
            foreach ($order->items as $item) {
                // Use cost_price from product if available, otherwise calculate from batches
                if ($item->product && $item->product->cost_price) {
                    $cogs += $item->product->cost_price * $item->quantity;
                } else {
                    // Fallback: use 70% of selling price as cost estimate
                    $cogs += ($item->unit_price * 0.7) * $item->quantity;
                }
            }
        }

        // Calculate Gross Profit
        $grossProfit = $revenue - $cogs;

        // Calculate Net Profit (after refunds)
        $netProfit = $grossProfit - $refunds;

        // Get order statistics
        $orderStats = [
            'total_orders' => Order::whereBetween('created_at', [$startOfDay, $endOfDay])->count(),
            'delivered_orders' => Order::where('status', 'delivered')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count(),
            'rejected_orders' => Order::where('status', 'rejected')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count(),
            'pending_orders' => Order::where('status', 'pending')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count(),
        ];

        // Get branch-wise breakdown (if needed)
        $branchBreakdown = Order::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->select('branch_id', DB::raw('SUM(total_amount) as revenue'), DB::raw('COUNT(*) as order_count'))
            ->with('branch')
            ->groupBy('branch_id')
            ->get();

        return view('admin.reports.profit-loss', compact(
            'revenue',
            'refunds',
            'cogs',
            'grossProfit',
            'netProfit',
            'orderStats',
            'branchBreakdown',
            'date'
        ));
    }
}

