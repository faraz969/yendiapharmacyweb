<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Notification;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['user', 'items.product', 'branch']);

        // If branch staff, filter by their branch
        if ($user->isBranchStaff()) {
            $query->where('branch_id', $user->branch_id);
        }

        // Filter by branch
        if ($request->has('branch_id') && $request->branch_id !== '') {
            $query->where('branch_id', $request->branch_id);
        }

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

        // Export to Excel
        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportToExcel($query);
        }

        $orders = $query->latest()->paginate(20);
        $statuses = ['pending', 'approved', 'rejected', 'packing', 'packed', 'out_for_delivery', 'delivered', 'cancelled'];
        $branches = Branch::active()->orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'statuses', 'branches'));
    }

    private function exportToExcel($query)
    {
        $orders = $query->with(['branch', 'items.product'])->get();
        
        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'Order Number',
                'Branch',
                'Customer Name',
                'Customer Phone',
                'Customer Email',
                'Items Count',
                'Subtotal',
                'Delivery Fee',
                'Discount',
                'Total Amount',
                'Payment Status',
                'Payment Method',
                'Status',
                'Order Date',
                'Delivery Address'
            ]);

            // Data rows
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->branch ? $order->branch->name : 'N/A',
                    $order->customer_name,
                    $order->customer_phone,
                    $order->customer_email ?? 'N/A',
                    $order->items->count(),
                    number_format($order->subtotal, 2),
                    number_format($order->delivery_fee, 2),
                    number_format($order->discount, 2),
                    number_format($order->total_amount, 2),
                    $order->payment_status ?? 'N/A',
                    $order->payment_method ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $order->status)),
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->delivery_address
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Order $order)
    {
        $user = Auth::user();
        
        // If branch staff, ensure order belongs to their branch
        if ($user->isBranchStaff() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Access denied. This order does not belong to your branch.');
        }
        
        $order->load(['user', 'items.product', 'prescription', 'approvedBy', 'packedBy', 'deliveredBy', 'deliveryZone', 'branch']);
        return view('admin.orders.show', compact('order'));
    }

    public function approve(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // If branch staff, ensure order belongs to their branch
        if ($user->isBranchStaff() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Access denied. This order does not belong to your branch.');
        }
        
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be approved.');
        }

        $oldStatus = $order->status;
        $order->approve(Auth::id(), $request->notes);
        // Load user relationship for SMS notification
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }
        $order->notifyStatusChange($oldStatus);

        return back()->with('success', 'Order approved successfully.');
    }

    public function reject(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // If branch staff, ensure order belongs to their branch
        if ($user->isBranchStaff() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Access denied. This order does not belong to your branch.');
        }
        
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $oldStatus = $order->status;
        $order->reject(Auth::id(), $request->rejection_reason);
        // Load user relationship for SMS notification
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }
        $order->notifyStatusChange($oldStatus);

        return back()->with('success', 'Order rejected.');
    }

    public function pack(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // If branch staff, ensure order belongs to their branch
        if ($user->isBranchStaff() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Access denied. This order does not belong to your branch.');
        }
        
        if (!in_array($order->status, ['approved', 'packing'])) {
            return back()->with('error', 'Order must be approved before packing.');
        }

        $oldStatus = $order->status;
        $order->markAsPacked(Auth::id());
        // Load user relationship for SMS notification
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }
        $order->notifyStatusChange($oldStatus);

        return back()->with('success', 'Order marked as packed.');
    }

    public function deliver(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // If branch staff, ensure order belongs to their branch
        if ($user->isBranchStaff() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Access denied. This order does not belong to your branch.');
        }
        
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

        $oldStatus = $order->status;
        $order->assignForDelivery($request->delivery_person_id);
        // Load user relationship for SMS notification
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }
        $order->notifyStatusChange($oldStatus);

        return back()->with('success', 'Order assigned for delivery.');
    }

    public function markDelivered(Order $order)
    {
        $user = Auth::user();
        
        // If branch staff, ensure order belongs to their branch
        if ($user->isBranchStaff() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Access denied. This order does not belong to your branch.');
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

        return back()->with('success', 'Order marked as delivered.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // If branch staff, ensure order belongs to their branch
        if ($user->isBranchStaff() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Access denied. This order does not belong to your branch.');
        }
        
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,packing,packed,out_for_delivery,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);
        $order->refresh(); // Refresh to get updated status
        // Load user relationship for SMS notification
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }
        $order->notifyStatusChange($oldStatus);

        return back()->with('success', 'Order status updated.');
    }

    public function destroy(Order $order)
    {
        $user = Auth::user();
        
        // Only full admins can delete orders, not branch staff
        if ($user->isBranchStaff()) {
            abort(403, 'Access denied. Only full admins can delete orders.');
        }
        
        // Prevent deletion of paid orders (optional safety check)
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Cannot delete orders that have been paid. Please cancel or refund the order instead.');
        }
        
        try {
            // Delete related order items first
            $order->items()->delete();
            
            // Delete the order
            $order->delete();
            
            return redirect()->route('admin.orders.index')
                ->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }
}
