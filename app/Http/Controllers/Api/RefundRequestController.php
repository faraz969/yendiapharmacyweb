<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundRequestController extends Controller
{
    /**
     * Create a refund request for a paid order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'refund_method' => 'required|in:mobile_money,bank_transfer',
            
            // Mobile Money fields (required if refund_method is mobile_money)
            'mobile_money_provider' => 'required_if:refund_method,mobile_money|string|max:255',
            'mobile_money_number' => 'required_if:refund_method,mobile_money|string|max:20',
            'mobile_money_name' => 'required_if:refund_method,mobile_money|string|max:255',
            
            // Bank Account fields (required if refund_method is bank_transfer)
            'bank_name' => 'required_if:refund_method,bank_transfer|string|max:255',
            'account_number' => 'required_if:refund_method,bank_transfer|string|max:50',
            'account_name' => 'required_if:refund_method,bank_transfer|string|max:255',
            'account_type' => 'nullable|string|max:50',
            'branch_name' => 'nullable|string|max:255',
        ]);

        try {
            // Get the order
            $order = Order::where('user_id', Auth::id())
                ->findOrFail($validated['order_id']);

            // Verify order is paid
            if ($order->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund can only be requested for paid orders.',
                ], 400);
            }

            // Check if order can be cancelled
            if (!in_array($order->status, ['pending', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund can only be requested for pending or approved orders.',
                ], 400);
            }

            // Check if refund request already exists
            if ($order->refundRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'A refund request already exists for this order.',
                    'data' => $order->refundRequest,
                ], 400);
            }

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

            return response()->json([
                'success' => true,
                'message' => 'Refund request submitted successfully. Your order has been cancelled.',
                'data' => $refundRequest->load('order'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund request creation error', [
                'order_id' => $validated['order_id'] ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the refund request. Please try again.',
            ], 500);
        }
    }

    /**
     * Get refund requests for the authenticated user
     */
    public function index(Request $request)
    {
        $refundRequests = RefundRequest::where('user_id', Auth::id())
            ->with(['order', 'processedBy'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $refundRequests,
        ]);
    }

    /**
     * Get a specific refund request
     */
    public function show($id)
    {
        $refundRequest = RefundRequest::where('user_id', Auth::id())
            ->with(['order.items.product', 'processedBy'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $refundRequest,
        ]);
    }
}

