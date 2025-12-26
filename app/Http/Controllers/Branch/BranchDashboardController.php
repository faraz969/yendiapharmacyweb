<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Prescription;
use App\Models\ItemRequest;
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
            'prescriptions_pending' => Prescription::where('branch_id', $branch->id)->where('status', 'pending')->count(),
            'item_requests_pending' => ItemRequest::where('branch_id', $branch->id)->where('status', 'pending')->count(),
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

    public function prescriptions(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isBranchStaff()) {
            abort(403, 'Access denied.');
        }

        $branch = $user->branch;
        
        $query = Prescription::with(['user', 'approvedBy'])
            ->where('branch_id', $branch->id);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('prescription_number', 'like', "%{$search}%")
                  ->orWhere('patient_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('doctor_name', 'like', "%{$search}%");
            });
        }

        $prescriptions = $query->latest()->paginate(20);
        $statuses = ['pending', 'approved', 'rejected'];

        return view('branch.prescriptions.index', compact('prescriptions', 'statuses', 'branch'));
    }

    public function showPrescription(Prescription $prescription)
    {
        $user = Auth::user();
        
        if (!$user->isBranchStaff() || $prescription->branch_id !== $user->branch_id) {
            abort(403, 'Access denied.');
        }

        $prescription->load(['user', 'approvedBy', 'branch', 'orders']);
        
        return view('branch.prescriptions.show', compact('prescription'));
    }

    public function itemRequests(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isBranchStaff()) {
            abort(403, 'Access denied.');
        }

        $branch = $user->branch;
        
        $query = ItemRequest::with(['user', 'processedBy'])
            ->where('branch_id', $branch->id);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $itemRequests = $query->latest()->paginate(20);
        $statuses = ['pending', 'in_progress', 'fulfilled', 'rejected', 'cancelled'];

        return view('branch.item-requests.index', compact('itemRequests', 'statuses', 'branch'));
    }

    public function showItemRequest(ItemRequest $itemRequest)
    {
        $user = Auth::user();
        
        if (!$user->isBranchStaff() || $itemRequest->branch_id !== $user->branch_id) {
            abort(403, 'Access denied.');
        }

        $itemRequest->load(['user', 'processedBy', 'branch']);
        
        return view('branch.item-requests.show', compact('itemRequest'));
    }

    public function approvePrescription(Request $request, Prescription $prescription)
    {
        $user = Auth::user();
        
        if (!$user->isBranchStaff() || $prescription->branch_id !== $user->branch_id) {
            abort(403, 'Access denied.');
        }

        if ($prescription->status !== 'pending') {
            return back()->with('error', 'Only pending prescriptions can be approved.');
        }

        $prescription->approve(Auth::id(), $request->notes);

        return back()->with('success', 'Prescription approved successfully.');
    }

    public function rejectPrescription(Request $request, Prescription $prescription)
    {
        $user = Auth::user();
        
        if (!$user->isBranchStaff() || $prescription->branch_id !== $user->branch_id) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($prescription->status !== 'pending') {
            return back()->with('error', 'Only pending prescriptions can be rejected.');
        }

        $prescription->reject(Auth::id(), $request->rejection_reason);

        return back()->with('success', 'Prescription rejected.');
    }

    public function updateItemRequestStatus(Request $request, ItemRequest $itemRequest)
    {
        $user = Auth::user();
        
        if (!$user->isBranchStaff() || $itemRequest->branch_id !== $user->branch_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,fulfilled,rejected,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $itemRequest->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $itemRequest->admin_notes,
            'rejection_reason' => $validated['rejection_reason'] ?? $itemRequest->rejection_reason,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Item request status updated successfully.');
    }
}
