<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OtpService
{
    private $apiKey;
    private $senderId;
    private $baseUrl = 'https://sms.arkesel.com/api/otp';

    public function __construct()
    {
        $this->apiKey = config('services.arkesel.api_key', 'dHZMQkJXSUNqS3dSUEpQb3htdmg');
        $this->senderId = config('services.arkesel.sender_id', 'Yendia');
    }

    /**
     * Generate OTP and send via SMS
     *
     * @param string $phoneNumber Phone number (with country code, e.g., 233544919953)
     * @param int $expiry Expiry time in minutes (default: 5)
     * @param int $length OTP length (default: 6)
     * @return array Response from API
     */
    public function generateOtp($phoneNumber, $expiry = 5, $length = 6)
    {
        if (empty($this->apiKey) || empty($phoneNumber)) {
            Log::warning('OTP not generated: Missing API key or phone number');
            return [
                'success' => false,
                'code' => '1001',
                'message' => 'Missing required field'
            ];
        }

        try {
            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            if (!$formattedPhone) {
                Log::warning("OTP not generated: Invalid phone number format: {$phoneNumber}");
                return [
                    'success' => false,
                    'code' => '1005',
                    'message' => 'Invalid phone number'
                ];
            }

            // Prepare fields for POST request
            $fields = [
                'expiry' => $expiry,
                'length' => $length,
                'medium' => 'sms',
                'message' => 'This is the code for password reset from yendiapharmacy, %otp_code%',
                'number' => $formattedPhone,
                'sender_id' => $this->senderId,
                'type' => 'numeric',
            ];

            // Build POST data
            $postvars = '';
            foreach ($fields as $key => $value) {
                $postvars .= $key . "=" . $value . "&";
            }
            $postvars = rtrim($postvars, '&');

            $response = Http::timeout(10)
                ->withHeaders([
                    'api-key' => $this->apiKey,
                ])
                ->asForm()
                ->post($this->baseUrl . '/generate', $fields);

            $result = $response->json();

            if ($response->successful() && isset($result['code'])) {
                $code = $result['code'];
                
                if ($code === '1000') {
                    Log::info('OTP generated successfully', [
                        'phone' => $formattedPhone,
                        'ussd_code' => $result['ussd_code'] ?? null,
                    ]);
                    return [
                        'success' => true,
                        'code' => $code,
                        'message' => $result['message'] ?? 'OTP is being processed for delivery',
                        'ussd_code' => $result['ussd_code'] ?? null,
                    ];
                } else {
                    Log::error('OTP generation failed', [
                        'phone' => $formattedPhone,
                        'code' => $code,
                        'response' => $result,
                    ]);
                    return [
                        'success' => false,
                        'code' => $code,
                        'message' => $result['message'] ?? $this->getErrorMessage($code),
                    ];
                }
            } else {
                Log::error('OTP generation failed: Invalid response', [
                    'phone' => $formattedPhone,
                    'response' => $result,
                ]);
                return [
                    'success' => false,
                    'code' => '1011',
                    'message' => 'Internal error',
                ];
            }
        } catch (\Exception $e) {
            Log::error('OTP generation exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'code' => '1011',
                'message' => 'Internal error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify OTP code
     *
     * @param string $phoneNumber Phone number (with country code)
     * @param string $code OTP code to verify
     * @return array Response from API
     */
    public function verifyOtp($phoneNumber, $code)
    {
        if (empty($this->apiKey) || empty($phoneNumber) || empty($code)) {
            Log::warning('OTP verification failed: Missing API key, phone number, or code');
            return [
                'success' => false,
                'code' => '1101',
                'message' => 'Missing required field'
            ];
        }

        try {
            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            if (!$formattedPhone) {
                Log::warning("OTP verification failed: Invalid phone number format: {$phoneNumber}");
                return [
                    'success' => false,
                    'code' => '1102',
                    'message' => 'Invalid phone number'
                ];
            }

            // Prepare fields for POST request
            $fields = [
                'api_key' => $this->apiKey,
                'code' => $code,
                'number' => $formattedPhone,
            ];

            $response = Http::timeout(10)
                ->withHeaders([
                    'api-key' => $this->apiKey,
                ])
                ->asForm()
                ->post($this->baseUrl . '/verify', $fields);

            $result = $response->json();

            if ($response->successful() && isset($result['code'])) {
                $responseCode = $result['code'];
                
                if ($responseCode === '1100') {
                    Log::info('OTP verified successfully', [
                        'phone' => $formattedPhone,
                    ]);
                    return [
                        'success' => true,
                        'code' => $responseCode,
                        'message' => $result['message'] ?? 'Successful',
                    ];
                } else {
                    Log::warning('OTP verification failed', [
                        'phone' => $formattedPhone,
                        'code' => $responseCode,
                        'response' => $result,
                    ]);
                    return [
                        'success' => false,
                        'code' => $responseCode,
                        'message' => $result['message'] ?? $this->getVerificationErrorMessage($responseCode),
                    ];
                }
            } else {
                Log::error('OTP verification failed: Invalid response', [
                    'phone' => $formattedPhone,
                    'response' => $result,
                ]);
                return [
                    'success' => false,
                    'code' => '1106',
                    'message' => 'Internal error',
                ];
            }
        } catch (\Exception $e) {
            Log::error('OTP verification exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'code' => '1106',
                'message' => 'Internal error: ' . $e->getMessage(),
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

    /**
     * Get error message for OTP generation codes
     *
     * @param string $code
     * @return string
     */
    private function getErrorMessage($code)
    {
        $messages = [
            '1001' => 'Validation error (Missing required field)',
            '1002' => 'Message must contain a slot for otp code, like %otp_code%',
            '1003' => 'This Sender ID have Blocked By Administrator',
            '1004' => 'SMS gateway not active or credential not found',
            '1005' => 'Invalid phone number',
            '1006' => 'OTP is not allowed in your Country',
            '1007' => 'Insufficient balance',
            '1008' => 'Insufficient balance',
            '1009' => 'You can not send more than 500 characters using voice medium',
            '1011' => 'Internal error',
        ];

        return $messages[$code] ?? 'Unknown error';
    }

    /**
     * Get error message for OTP verification codes
     *
     * @param string $code
     * @return string
     */
    private function getVerificationErrorMessage($code)
    {
        $messages = [
            '1101' => 'Validation error (Missing required field)',
            '1102' => 'Invalid phone number',
            '1103' => 'Invalid phone number',
            '1104' => 'Invalid code',
            '1105' => 'Code has expired',
            '1106' => 'Internal error',
        ];

        return $messages[$code] ?? 'Unknown error';
    }
}

