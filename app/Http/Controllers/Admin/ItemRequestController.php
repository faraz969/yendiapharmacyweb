<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = ItemRequest::with(['user', 'processedBy', 'branch']);

        // If branch staff, filter by their branch
        if ($user->isBranchStaff()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('branch_id') && $request->branch_id !== '' && !$user->isBranchStaff()) {
            $query->where('branch_id', $request->branch_id);
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
        
        // Get branches for filter (only if admin)
        $branches = $user->isBranchStaff() ? null : \App\Models\Branch::active()->get();

        return view('admin.item-requests.index', compact('itemRequests', 'statuses', 'branches'));
    }

    public function show(ItemRequest $itemRequest)
    {
        // Check if branch staff can only see their branch requests
        $user = Auth::user();
        if ($user->isBranchStaff() && $itemRequest->branch_id !== $user->branch_id) {
            abort(403, 'You can only view item requests for your branch.');
        }
        
        $itemRequest->load(['user', 'processedBy', 'branch']);
        return view('admin.item-requests.show', compact('itemRequest'));
    }

    public function updateStatus(Request $request, ItemRequest $itemRequest)
    {
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
