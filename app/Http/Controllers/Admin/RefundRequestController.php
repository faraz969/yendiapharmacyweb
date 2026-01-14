<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RefundRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = RefundRequest::with(['order', 'user', 'processedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('refund_number', 'like', "%{$search}%")
                      ->orWhereHas('order', function($orderQuery) use ($search) {
                          $orderQuery->where('order_number', 'like', "%{$search}%");
                      })
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        $refundRequests = $query->latest()->paginate(20)->appends($request->query());
        $statuses = ['pending', 'approved', 'rejected', 'processed', 'completed'];

        return view('admin.refund-requests.index', compact('refundRequests', 'statuses'));
    }

    public function show(RefundRequest $refundRequest)
    {
        $refundRequest->load(['order.items.product', 'user', 'processedBy']);
        return view('admin.refund-requests.show', compact('refundRequest'));
    }

    public function approve(Request $request, RefundRequest $refundRequest)
    {
        if ($refundRequest->status !== 'pending') {
            return back()->with('error', 'Only pending refund requests can be approved.');
        }

        $refundRequest->approve(Auth::id(), $request->admin_notes);

        return back()->with('success', 'Refund request approved. You can now process the refund.');
    }

    public function reject(Request $request, RefundRequest $refundRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($refundRequest->status !== 'pending') {
            return back()->with('error', 'Only pending refund requests can be rejected.');
        }

        $refundRequest->reject(Auth::id(), $request->rejection_reason);

        return back()->with('success', 'Refund request rejected.');
    }

    public function markAsProcessed(Request $request, RefundRequest $refundRequest)
    {
        if (!in_array($refundRequest->status, ['approved', 'pending'])) {
            return back()->with('error', 'Only approved or pending refund requests can be marked as processed.');
        }

        $refundRequest->markAsProcessed(Auth::id(), $request->refund_reference);

        return back()->with('success', 'Refund marked as processed.');
    }

    public function markAsCompleted(RefundRequest $refundRequest)
    {
        if ($refundRequest->status !== 'processed') {
            return back()->with('error', 'Only processed refund requests can be marked as completed.');
        }

        $refundRequest->markAsCompleted();

        return back()->with('success', 'Refund marked as completed.');
    }
}

