<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    /**
     * Handle Paystack webhook events
     */
    public function handle(Request $request)
    {
        $event = $request->input('event');
        $data = $request->input('data');

        Log::info('Paystack webhook received', [
            'event' => $event,
            'data' => $data,
        ]);

        // Handle transfer events
        if (in_array($event, ['transfer.success', 'transfer.failed', 'transfer.reversed'])) {
            $this->handleTransferEvent($event, $data);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Handle transfer webhook events
     */
    private function handleTransferEvent($event, $data)
    {
        if (!isset($data['reference'])) {
            Log::warning('Transfer webhook missing reference', ['data' => $data]);
            return;
        }

        // Find refund request by transfer reference
        $refundRequest = RefundRequest::where('refund_reference', $data['reference'])
            ->orWhere('transfer_code', $data['transfer_code'] ?? null)
            ->first();

        if (!$refundRequest) {
            Log::warning('Refund request not found for transfer', [
                'reference' => $data['reference'],
                'transfer_code' => $data['transfer_code'] ?? null,
            ]);
            return;
        }

        try {
            switch ($event) {
                case 'transfer.success':
                    $refundRequest->update([
                        'status' => 'completed',
                    ]);
                    Log::info('Refund transfer successful', [
                        'refund_request_id' => $refundRequest->id,
                        'transfer_code' => $data['transfer_code'] ?? null,
                    ]);
                    break;

                case 'transfer.failed':
                    // Keep status as processed but log the failure
                    // Admin can manually handle failed transfers
                    Log::error('Refund transfer failed', [
                        'refund_request_id' => $refundRequest->id,
                        'transfer_code' => $data['transfer_code'] ?? null,
                        'gateway_response' => $data['gateway_response'] ?? null,
                    ]);
                    break;

                case 'transfer.reversed':
                    // Transfer was reversed - update status
                    Log::warning('Refund transfer reversed', [
                        'refund_request_id' => $refundRequest->id,
                        'transfer_code' => $data['transfer_code'] ?? null,
                    ]);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error handling transfer webhook', [
                'refund_request_id' => $refundRequest->id ?? null,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

