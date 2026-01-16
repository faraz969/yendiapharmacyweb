<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\Order;
use App\Services\PaystackTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

    /**
     * Get count of pending refund requests for sidebar badge
     */
    public function getNewCount()
    {
        $count = RefundRequest::where('status', 'pending')->count();

        return response()->json([
            'count' => $count,
        ]);
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

        try {
            DB::beginTransaction();

            // Approve the refund request
            $refundRequest->approve(Auth::id(), $request->admin_notes);

            // Process the transfer via Paystack
            $transferService = new PaystackTransferService();
            
            // Map mobile money provider to Paystack bank code
            $bankCodeMap = [
                'MTN' => 'MTN',
                'Vodafone' => 'VOD',
                'VOD' => 'VOD',
                'AirtelTigo' => 'ATL',
                'ATL' => 'ATL',
            ];

            $recipientCode = $refundRequest->recipient_code;
            
            // Create recipient if not exists
            if (!$recipientCode) {
                if ($refundRequest->refund_method === 'mobile_money') {
                    $bankCode = $bankCodeMap[$refundRequest->mobile_money_provider] ?? $refundRequest->mobile_money_provider;
                    
                    $recipientData = [
                        'name' => $refundRequest->mobile_money_name,
                        'account_number' => $refundRequest->mobile_money_number,
                        'bank_code' => $bankCode,
                        'currency' => 'GHS',
                    ];
                    
                    $recipientResult = $transferService->createRecipient('mobile_money', $recipientData);
                    
                    if (!$recipientResult['success']) {
                        throw new \Exception('Failed to create transfer recipient: ' . ($recipientResult['message'] ?? 'Unknown error'));
                    }
                    
                    $recipientCode = $recipientResult['recipient_code'];
                    $refundRequest->update([
                        'recipient_code' => $recipientCode,
                        'bank_code' => $bankCode,
                    ]);
                } elseif ($refundRequest->refund_method === 'bank_transfer') {
                    // For bank transfers, fetch banks list and match by name
                    $banksResult = $transferService->getBanks('GHS');
                    
                    if (!$banksResult['success']) {
                        throw new \Exception('Failed to fetch banks list: ' . ($banksResult['message'] ?? 'Unknown error'));
                    }
                    
                    $banks = $banksResult['data'];
                    $bankCode = null;
                    
                    // Try to find bank by name
                    foreach ($banks as $bank) {
                        if (stripos($bank['name'], $refundRequest->bank_name) !== false || 
                            stripos($refundRequest->bank_name, $bank['name']) !== false) {
                            $bankCode = $bank['code'];
                            break;
                        }
                    }
                    
                    if (!$bankCode) {
                        throw new \Exception("Bank code not found for: {$refundRequest->bank_name}. Please verify the bank name.");
                    }
                    
                    // Resolve account number first (optional but recommended)
                    $accountResult = $transferService->resolveAccountNumber(
                        $refundRequest->account_number,
                        $bankCode,
                        'GHS'
                    );
                    
                    $accountName = $refundRequest->account_name;
                    if ($accountResult['success'] && isset($accountResult['data']['account_name'])) {
                        $accountName = $accountResult['data']['account_name'];
                    }
                    
                    $recipientData = [
                        'name' => $accountName,
                        'account_number' => $refundRequest->account_number,
                        'bank_code' => $bankCode,
                        'currency' => 'GHS',
                    ];
                    
                    $recipientResult = $transferService->createRecipient('ghipss', $recipientData);
                    
                    if (!$recipientResult['success']) {
                        throw new \Exception('Failed to create transfer recipient: ' . ($recipientResult['message'] ?? 'Unknown error'));
                    }
                    
                    $recipientCode = $recipientResult['recipient_code'];
                    $refundRequest->update([
                        'recipient_code' => $recipientCode,
                        'bank_code' => $bankCode,
                        'account_name' => $accountName, // Update with resolved name if available
                    ]);
                }
            }

            // Generate transfer reference
            $transferReference = $transferService->generateTransferReference();
            
            // Initiate transfer
            $transferResult = $transferService->initiateTransfer(
                $recipientCode,
                $refundRequest->refund_amount,
                "Refund for Order #{$refundRequest->order->order_number}",
                $transferReference
            );

            if (!$transferResult['success']) {
                throw new \Exception('Failed to initiate transfer: ' . ($transferResult['message'] ?? 'Unknown error'));
            }

            // Update refund request with transfer details
            $refundRequest->update([
                'status' => 'processed',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'refund_reference' => $transferResult['reference'],
                'transfer_code' => $transferResult['transfer_code'] ?? null,
            ]);

            DB::commit();

            return back()->with('success', 'Refund request approved and transfer initiated successfully. Transfer Code: ' . ($transferResult['transfer_code'] ?? 'N/A'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund approval and transfer error', [
                'refund_request_id' => $refundRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
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

