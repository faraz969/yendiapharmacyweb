<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Generate OTP for password reset
     */
    public function generateOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        try {
            // Check if user exists with this phone number
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                // For security, don't reveal if user exists or not
                // Return success message even if user doesn't exist
                return response()->json([
                    'success' => true,
                    'message' => 'If the phone number is registered, an OTP will be sent.',
                ]);
            }

            // Generate and send OTP
            $result = $this->otpService->generateOtp($user->phone);

            if ($result['success']) {
                // Store phone number in cache for verification (expires in 10 minutes)
                Cache::put('password_reset_phone_' . $user->phone, true, now()->addMinutes(10));

                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'code' => $result['code'] ?? null,
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Password reset OTP generation error', [
                'phone' => $request->phone,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating OTP. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        try {
            // Check if user exists
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number or code.',
                ], 422);
            }

            // Verify OTP
            $result = $this->otpService->verifyOtp($user->phone, $request->code);

            if ($result['success']) {
                // Store verification token in cache (expires in 15 minutes)
                $verificationToken = bin2hex(random_bytes(32));
                Cache::put('password_reset_token_' . $verificationToken, $user->phone, now()->addMinutes(15));

                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully',
                    'verification_token' => $verificationToken,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'code' => $result['code'] ?? null,
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Password reset OTP verification error', [
                'phone' => $request->phone,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying OTP. Please try again.',
            ], 500);
        }
    }

    /**
     * Reset password after OTP verification
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'verification_token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            // Get phone number from verification token
            $phone = Cache::get('password_reset_token_' . $request->verification_token);

            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired verification token. Please request a new OTP.',
                ], 422);
            }

            // Find user
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete verification token
            Cache::forget('password_reset_token_' . $request->verification_token);

            Log::info('Password reset successful', [
                'user_id' => $user->id,
                'phone' => $phone,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Password reset error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resetting password. Please try again.',
            ], 500);
        }
    }
}

