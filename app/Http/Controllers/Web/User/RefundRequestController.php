<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Verify order is paid
        if ($order->payment_status !== 'paid') {
            return redirect()->route('user.orders.show', $order)
                ->with('error', 'Refund can only be requested for paid orders.');
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'approved'])) {
            return redirect()->route('user.orders.show', $order)
                ->with('error', 'Refund can only be requested for pending or approved orders.');
        }

        // Check if refund request already exists
        if ($order->refundRequest) {
            return redirect()->route('user.orders.show', $order)
                ->with('info', 'A refund request already exists for this order.');
        }

        return view('web.user.refund-requests.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'refund_method' => 'required|in:mobile_money,bank_transfer',
            
            // Mobile Money fields
            'mobile_money_provider' => 'required_if:refund_method,mobile_money|string|max:255',
            'mobile_money_number' => 'required_if:refund_method,mobile_money|string|max:20',
            'mobile_money_name' => 'required_if:refund_method,mobile_money|string|max:255',
            
            // Bank Account fields
            'bank_name' => 'required_if:refund_method,bank_transfer|string|max:255',
            'account_number' => 'required_if:refund_method,bank_transfer|string|max:50',
            'account_name' => 'required_if:refund_method,bank_transfer|string|max:255',
            'account_type' => 'nullable|string|max:50',
            'branch_name' => 'nullable|string|max:255',
        ]);

        // Verify order is paid
        if ($order->payment_status !== 'paid') {
            return back()->with('error', 'Refund can only be requested for paid orders.');
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'approved'])) {
            return back()->with('error', 'Refund can only be requested for pending or approved orders.');
        }

        // Check if refund request already exists
        if ($order->refundRequest) {
            return back()->with('error', 'A refund request already exists for this order.');
        }

        try {
            DB::beginTransaction();

            // Create refund request
            $refundRequest = RefundRequest::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'refund_number' => RefundRequest::generateRefundNumber(),
                'refund_amount' => $order->total_amount,
                'refund_method' => $validated['refund_method'],
                'mobile_money_provider' => $validated['mobile_money_provider'] ?? null,
                'mobile_money_number' => $validated['mobile_money_number'] ?? null,
                'mobile_money_name' => $validated['mobile_money_name'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_name' => $validated['account_name'] ?? null,
                'account_type' => $validated['account_type'] ?? null,
                'branch_name' => $validated['branch_name'] ?? null,
                'status' => 'pending',
            ]);

            // Create admin notification for new refund request
            \App\Models\Notification::create([
                'for_admin' => true,
                'refund_request_id' => $refundRequest->id,
                'title' => 'New Refund Request',
                'message' => "New refund request #{$refundRequest->refund_number} for order #{$order->order_number}. Amount: " . \App\Models\Setting::formatPrice($refundRequest->refund_amount),
                'type' => 'warning',
                'link' => route('admin.refund-requests.show', $refundRequest->id),
                'is_active' => true,
                'is_read' => false,
            ]);

            // Cancel the order
            $oldStatus = $order->status;
            $order->update([
                'status' => 'cancelled',
            ]);

            // Notify status change
            $order->notifyStatusChange($oldStatus);

            DB::commit();

            return redirect()->route('user.orders.show', $order)
                ->with('success', 'Refund request submitted successfully. Your order has been cancelled.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund request creation error', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'An error occurred while creating the refund request. Please try again.');
        }
    }

    public function index()
    {
        $refundRequests = RefundRequest::where('user_id', Auth::id())
            ->with(['order', 'processedBy'])
            ->latest()
            ->paginate(10);

        return view('web.user.refund-requests.index', compact('refundRequests'));
    }

    public function show(RefundRequest $refundRequest)
    {
        // Ensure the refund request belongs to the authenticated user
        if ($refundRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $refundRequest->load(['order.items.product', 'processedBy']);

        return view('web.user.refund-requests.show', compact('refundRequest'));
    }
}

