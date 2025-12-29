<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function profitLoss(Request $request)
    {
        // Get date range - default to today if not provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($request->has('date')) {
            // Backward compatibility: if single date is provided, use it for both
            $date = Carbon::parse($request->date);
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
        } else {
            // Default to today
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        }

        // Ensure end date is not before start date
        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy()->endOfDay();
        }

        // Get branch filter
        $branchId = $request->get('branch_id');
        $selectedBranch = $branchId ? Branch::find($branchId) : null;

        // Base query with date filter
        $baseQuery = function($query) use ($startDate, $endDate, $branchId) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
        };

        // Total Revenue (delivered orders)
        // Include ALL delivered orders - if an order is delivered, it should be counted as revenue
        $revenueQuery = Order::where('status', 'delivered');
        $baseQuery($revenueQuery);
        $revenue = $revenueQuery->sum('total_amount');

        // Total Refunds (rejected and cancelled orders)
        // Only count refunds for orders that were actually paid (or have no payment status set)
        $refundsQuery = Order::whereIn('status', ['rejected', 'cancelled'])
            ->where(function($q) {
                $q->where('payment_status', 'paid')
                  ->orWhereNull('payment_status'); // Include orders without payment_status set
            });
        $baseQuery($refundsQuery);
        $refunds = $refundsQuery->sum('total_amount');

        // Calculate Cost of Goods Sold (COGS)
        // Get all delivered orders for the selected period
        $deliveredOrdersQuery = Order::where('status', 'delivered');
        $baseQuery($deliveredOrdersQuery);
        $deliveredOrders = $deliveredOrdersQuery->with('items.product')->get();

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
        $totalOrdersQuery = Order::query();
        $baseQuery($totalOrdersQuery);
        
        $deliveredOrdersCountQuery = Order::where('status', 'delivered');
        $baseQuery($deliveredOrdersCountQuery);
        
        $rejectedOrdersQuery = Order::where('status', 'rejected');
        $baseQuery($rejectedOrdersQuery);
        
        $cancelledOrdersQuery = Order::where('status', 'cancelled');
        $baseQuery($cancelledOrdersQuery);
        
        $pendingOrdersQuery = Order::where('status', 'pending');
        $baseQuery($pendingOrdersQuery);

        $orderStats = [
            'total_orders' => $totalOrdersQuery->count(),
            'delivered_orders' => $deliveredOrdersCountQuery->count(),
            'rejected_orders' => $rejectedOrdersQuery->count(),
            'cancelled_orders' => $cancelledOrdersQuery->count(),
            'pending_orders' => $pendingOrdersQuery->count(),
        ];

        // Get branch-wise breakdown (only if no branch filter is applied)
        $branchBreakdown = collect();
        if (!$branchId) {
            $branchBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'delivered')
                ->select('branch_id', DB::raw('SUM(total_amount) as revenue'), DB::raw('COUNT(*) as order_count'))
                ->with('branch')
                ->groupBy('branch_id')
                ->get();
        }

        // Debug info: Count delivered orders with and without payment_status
        $deliveredWithPayment = Order::where('status', 'delivered')
            ->where('payment_status', 'paid');
        $baseQuery($deliveredWithPayment);
        $deliveredWithPaymentCount = $deliveredWithPayment->count();
        
        $deliveredWithoutPayment = Order::where('status', 'delivered')
            ->where(function($q) {
                $q->whereNull('payment_status')
                  ->orWhere('payment_status', '!=', 'paid');
            });
        $baseQuery($deliveredWithoutPayment);
        $deliveredWithoutPaymentCount = $deliveredWithoutPayment->count();

        // Get all branches for the filter dropdown
        $branches = Branch::active()->orderBy('name')->get();

        return view('admin.reports.profit-loss', compact(
            'revenue',
            'refunds',
            'cogs',
            'grossProfit',
            'netProfit',
            'orderStats',
            'branchBreakdown',
            'startDate',
            'endDate',
            'branches',
            'branchId',
            'selectedBranch',
            'deliveredWithPaymentCount',
            'deliveredWithoutPaymentCount'
        ));
    }
}

