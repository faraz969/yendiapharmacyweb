<?php

namespace App\Http\Controllers\Web\Auth;

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
     * Show password reset form (enter phone)
     */
    public function showResetForm()
    {
        return view('web.auth.password-reset');
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
                return back()->with('success', 'If the phone number is registered, an OTP will be sent.');
            }

            // Generate and send OTP
            $result = $this->otpService->generateOtp($user->phone);

            if ($result['success']) {
                // Store phone number in cache for verification (expires in 10 minutes)
                Cache::put('password_reset_phone_' . $user->phone, true, now()->addMinutes(10));

                return redirect()->route('password.reset.verify')->with([
                    'success' => $result['message'],
                ])->withInput(['phone' => $user->phone]);
            } else {
                return back()->withErrors(['phone' => $result['message']])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Password reset OTP generation error', [
                'phone' => $request->phone,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['phone' => 'An error occurred while generating OTP. Please try again.'])->withInput();
        }
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyForm(Request $request)
    {
        $phone = $request->old('phone');
        
        if (!$phone) {
            return redirect()->route('password.reset.request')->withErrors(['phone' => 'Please enter your phone number first.']);
        }

        return view('web.auth.password-reset-verify', ['phone' => $phone]);
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
                return back()->withErrors(['code' => 'Invalid phone number or code.'])->withInput();
            }

            // Verify OTP
            $result = $this->otpService->verifyOtp($user->phone, $request->code);

            if ($result['success']) {
                // Store verification token in cache (expires in 15 minutes)
                $verificationToken = bin2hex(random_bytes(32));
                Cache::put('password_reset_token_' . $verificationToken, $user->phone, now()->addMinutes(15));

                return redirect()->route('password.reset.new')->with([
                    'success' => 'OTP verified successfully',
                ])->with('verification_token', $verificationToken);
            } else {
                return back()->withErrors(['code' => $result['message']])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Password reset OTP verification error', [
                'phone' => $request->phone,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['code' => 'An error occurred while verifying OTP. Please try again.'])->withInput();
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        try {
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return back()->withErrors(['phone' => 'Invalid phone number.'])->withInput();
            }

            $result = $this->otpService->generateOtp($user->phone);

            if ($result['success']) {
                return back()->with('success', 'OTP has been resent successfully.');
            } else {
                return back()->withErrors(['phone' => $result['message']])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Password reset OTP resend error', [
                'phone' => $request->phone,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['phone' => 'An error occurred while resending OTP. Please try again.'])->withInput();
        }
    }

    /**
     * Show new password form
     */
    public function showNewPasswordForm(Request $request)
    {
        $verificationToken = $request->session()->get('verification_token') ?? $request->query('token');

        if (!$verificationToken) {
            return redirect()->route('password.reset.request')->withErrors(['token' => 'Invalid or expired verification token. Please request a new OTP.']);
        }

        return view('web.auth.password-reset-new', ['verification_token' => $verificationToken]);
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
                return back()->withErrors(['password' => 'Invalid or expired verification token. Please request a new OTP.'])->withInput();
            }

            // Find user
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                return back()->withErrors(['password' => 'User not found.'])->withInput();
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

            return redirect()->route('login')->with('success', 'Password has been reset successfully. You can now login with your new password.');
        } catch (\Exception $e) {
            Log::error('Password reset error', [
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['password' => 'An error occurred while resetting password. Please try again.'])->withInput();
        }
    }
}

