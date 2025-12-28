<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $apiKey;
    private $senderId;
    private $baseUrl = 'https://sms.arkesel.com/sms/api';

    public function __construct()
    {
        $this->apiKey = config('services.arkesel.api_key', 'dHZMQkJXSUNqS3dSUEpQb3htdmg');
        $this->senderId = config('services.arkesel.sender_id', 'Yendia');
    }

    /**
     * Send SMS to a phone number
     *
     * @param string $to Phone number (with country code, e.g., 233544919953)
     * @param string $message SMS message
     * @return array Response from API
     */
    public function sendSms($to, $message)
    {
        if (empty($this->apiKey) || empty($to) || empty($message)) {
            Log::warning('SMS not sent: Missing API key, phone number, or message');
            return ['success' => false, 'message' => 'SMS configuration incomplete'];
        }

        try {
            // Format phone number (remove leading + if present, ensure it starts with country code)
            $phoneNumber = $this->formatPhoneNumber($to);
            
            if (!$phoneNumber) {
                Log::warning("SMS not sent: Invalid phone number format: {$to}");
                return ['success' => false, 'message' => 'Invalid phone number format'];
            }

            $response = Http::timeout(10)->get($this->baseUrl, [
                'action' => 'send-sms',
                'api_key' => $this->apiKey,
                'to' => $phoneNumber,
                'from' => $this->senderId,
                'sms' => $message,
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['code']) && $result['code'] === 'ok') {
                Log::info('SMS sent successfully', [
                    'to' => $phoneNumber,
                    'balance' => $result['balance'] ?? null,
                ]);
                return [
                    'success' => true,
                    'message' => $result['message'] ?? 'SMS sent successfully',
                    'balance' => $result['balance'] ?? null,
                ];
            } else {
                Log::error('SMS sending failed', [
                    'to' => $phoneNumber,
                    'response' => $result,
                ]);
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to send SMS',
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS sending exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number for Arkesel API
     * Arkesel expects numbers without + sign, starting with country code
     *
     * @param string $phoneNumber
     * @return string|null Formatted phone number or null if invalid
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Remove leading +
        $cleaned = ltrim($cleaned, '+');
        
        // If it doesn't start with country code, assume it's local and add Ghana code (233)
        if (!empty($cleaned) && strlen($cleaned) >= 9) {
            // If it starts with 0, replace with country code
            if (substr($cleaned, 0, 1) === '0') {
                $cleaned = '233' . substr($cleaned, 1);
            }
            // If it doesn't start with country code and is 9 digits, add 233
            elseif (strlen($cleaned) === 9 && substr($cleaned, 0, 1) !== '2') {
                $cleaned = '233' . $cleaned;
            }
            
            return $cleaned;
        }
        
        return null;
    }
}

