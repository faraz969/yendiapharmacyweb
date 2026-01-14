<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackTransferService
{
    private $secretKey;
    private $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        
        if (empty($this->secretKey)) {
            Log::error('Paystack secret key is not configured');
        }
    }

    /**
     * Get list of banks for a currency
     */
    public function getBanks($currency = 'GHS', $type = null)
    {
        try {
            $url = "{$this->baseUrl}/bank";
            $params = ['currency' => $currency];
            
            if ($type) {
                $params['type'] = $type;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($url, $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to fetch banks',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack get banks error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while fetching banks',
            ];
        }
    }

    /**
     * Resolve account number (for NGN/GHS bank accounts)
     */
    public function resolveAccountNumber($accountNumber, $bankCode, $currency = 'GHS')
    {
        try {
            $url = "{$this->baseUrl}/bank/resolve";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($url, [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to resolve account number',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack resolve account error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while resolving account number',
            ];
        }
    }

    /**
     * Create transfer recipient
     */
    public function createRecipient($type, $data)
    {
        try {
            $url = "{$this->baseUrl}/transferrecipient";
            
            $payload = [
                'type' => $type,
            ];

            // Add fields based on recipient type
            if ($type === 'mobile_money') {
                $payload['name'] = $data['name'];
                $payload['account_number'] = $data['account_number'];
                $payload['bank_code'] = $data['bank_code']; // Telco code (MTN, VOD, ATL)
                $payload['currency'] = $data['currency'] ?? 'GHS';
            } elseif ($type === 'ghipss' || $type === 'nuban') {
                $payload['name'] = $data['name'];
                $payload['account_number'] = $data['account_number'];
                $payload['bank_code'] = $data['bank_code'];
                $payload['currency'] = $data['currency'] ?? 'GHS';
            } elseif ($type === 'authorization') {
                $payload['name'] = $data['name'];
                $payload['email'] = $data['email'];
                $payload['authorization_code'] = $data['authorization_code'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'data' => $responseData['data'] ?? [],
                    'recipient_code' => $responseData['data']['recipient_code'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to create transfer recipient',
                'errors' => $response->json()['errors'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Paystack create recipient error', [
                'error' => $e->getMessage(),
                'type' => $type,
                'data' => $data,
            ]);
            return [
                'success' => false,
                'message' => 'An error occurred while creating transfer recipient',
            ];
        }
    }

    /**
     * Generate transfer reference
     */
    public function generateTransferReference()
    {
        // Generate a v4 UUID with prefix
        $uuid = Str::uuid()->toString();
        return 'refund_' . str_replace('-', '_', $uuid);
    }

    /**
     * Initiate transfer
     */
    public function initiateTransfer($recipientCode, $amount, $reason, $reference = null)
    {
        try {
            $url = "{$this->baseUrl}/transfer";
            
            // Generate reference if not provided
            if (!$reference) {
                $reference = $this->generateTransferReference();
            }

            $payload = [
                'source' => 'balance',
                'amount' => $amount * 100, // Convert to kobo/pesewas
                'recipient' => $recipientCode,
                'reason' => $reason,
                'reference' => $reference,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'data' => $responseData['data'] ?? [],
                    'transfer_code' => $responseData['data']['transfer_code'] ?? null,
                    'status' => $responseData['data']['status'] ?? 'pending',
                    'reference' => $reference,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to initiate transfer',
                'errors' => $response->json()['errors'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Paystack initiate transfer error', [
                'error' => $e->getMessage(),
                'recipient_code' => $recipientCode,
                'amount' => $amount,
            ]);
            return [
                'success' => false,
                'message' => 'An error occurred while initiating transfer',
            ];
        }
    }

    /**
     * Verify transfer status
     */
    public function verifyTransfer($transferCode)
    {
        try {
            $url = "{$this->baseUrl}/transfer/{$transferCode}";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to verify transfer',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack verify transfer error', [
                'error' => $e->getMessage(),
                'transfer_code' => $transferCode,
            ]);
            return [
                'success' => false,
                'message' => 'An error occurred while verifying transfer',
            ];
        }
    }
}

