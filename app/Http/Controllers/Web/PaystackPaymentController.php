<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackPaymentController extends Controller
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
     * Initialize Paystack transaction
     */
    public function initialize(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'email' => 'required|email',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Verify order ownership if user is authenticated
        if (auth()->check() && $order->user_id !== null && $order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this order',
            ], 403);
        }

        // Check if order is already paid
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Order has already been paid',
            ], 400);
        }

        // Check if secret key is configured
        if (empty($this->secretKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway is not configured. Please contact support.',
            ], 500);
        }

        // Convert amount to kobo (Paystack uses smallest currency unit)
        $amountInKobo = (int)($order->total_amount * 100);

        try {
            $response = Http::withToken(trim($this->secretKey))
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/transaction/initialize', [
                'email' => $request->email,
                'amount' => $amountInKobo,
                'reference' => $order->order_number . '_' . time(),
                'callback_url' => route('paystack.callback'),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                ],
            ]);

            $data = $response->json();
            
            // Log response for debugging
            if (!$data || !isset($data['status'])) {
                Log::error('Paystack API Error: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from payment gateway',
                ], 500);
            }

            if ($data['status'] === true) {
                // Update order with payment reference
                $order->update([
                    'payment_reference' => $data['data']['reference'],
                    'payment_method' => 'paystack',
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'authorization_url' => $data['data']['authorization_url'],
                        'access_code' => $data['data']['access_code'],
                        'reference' => $data['data']['reference'],
                    ],
                ]);
            } else {
                $errorMessage = $data['message'] ?? 'Failed to initialize payment';
                Log::error('Paystack initialization failed: ' . $errorMessage);
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Paystack initialization error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while initializing payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify Paystack transaction
     */
    public function verify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        // Check if secret key is configured
        if (empty($this->secretKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway is not configured. Please contact support.',
            ], 500);
        }

        try {
            $response = Http::withToken(trim($this->secretKey))
                ->withHeaders([
                    'Cache-Control' => 'no-cache',
                ])
                ->get($this->baseUrl . '/transaction/verify/' . $request->reference);

            $data = $response->json();

            if ($data['status'] === true && $data['data']['status'] === 'success') {
                // Find order by payment reference
                $order = Order::where('payment_reference', $request->reference)->first();

                if ($order) {
                    // Verify amount matches
                    $amountPaid = $data['data']['amount'] / 100; // Convert from kobo
                    if (abs($amountPaid - $order->total_amount) < 0.01) {
                                // Update order payment status
                        $order->update([
                            'payment_status' => 'paid',
                            'paid_at' => now(),
                        ]);
                        
                        // Clear cart session
                        session()->forget('cart');

                        return response()->json([
                            'success' => true,
                            'message' => 'Payment verified successfully',
                            'order' => $order,
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Amount mismatch',
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order not found',
                    ], 404);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $data['message'] ?? 'Payment verification failed',
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Paystack verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying payment',
            ], 500);
        }
    }

    /**
     * Handle Paystack callback
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('checkout.index')
                ->with('error', 'Invalid payment reference');
        }

        // Verify the transaction
        $verifyResponse = $this->verify(new Request(['reference' => $reference]));

        if ($verifyResponse->getStatusCode() === 200) {
            $data = json_decode($verifyResponse->getContent(), true);
            if ($data['success']) {
                $order = Order::find($data['order']['id']);
                return redirect()->route('checkout.success', $order->id)
                    ->with('success', 'Payment successful! Your order has been placed.');
            }
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Payment verification failed. Please try again.');
    }
}
