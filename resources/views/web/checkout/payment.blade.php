@extends('web.layouts.app')

@section('title', 'Payment - Checkout')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header  text-white" style="background-color:#dc8423;">
                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Complete Payment</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Order #{{ $order->order_number }}</strong><br>
                        Total Amount: <strong>{{ \App\Models\Setting::formatPrice($order->total_amount) }}</strong>
                    </div>

                    <div class="mb-4">
                        <h5>Order Summary</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ \App\Models\Setting::formatPrice($item->unit_price) }}</td>
                                            <td>{{ \App\Models\Setting::formatPrice($item->total_price) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                        <td><strong>{{ \App\Models\Setting::formatPrice($order->subtotal) }}</strong></td>
                                    </tr>
                                    @if($order->delivery_fee > 0)
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Delivery Fee:</strong></td>
                                        <td><strong>{{ \App\Models\Setting::formatPrice($order->delivery_fee) }}</strong></td>
                                    </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>{{ \App\Models\Setting::formatPrice($order->total_amount) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <form id="paymentForm">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="{{ $order->customer_email ?? (auth()->check() ? auth()->user()->email : '') }}" 
                                   required>
                            <small class="form-text text-muted">We'll send payment confirmation to this email</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="payButton">
                            <i class="fas fa-lock me-2"></i>Pay {{ \App\Models\Setting::formatPrice($order->total_amount) }} with Paystack
                        </button>
                    </form>

                    <div id="paymentStatus" class="mt-3" style="display: none;"></div>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('checkout.index') }}" class="btn btn-link">
                    <i class="fas fa-arrow-left me-2"></i>Back to Checkout
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Paystack Inline JS -->
<script src="https://js.paystack.co/v2/inline.js"></script>
<script>
// Check if PaystackPop is loaded
if (typeof PaystackPop === 'undefined') {
    console.error('Paystack Popup library failed to load');
}
</script>

@push('scripts')
<script>
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const orderId = {{ $order->id }};
    const payButton = document.getElementById('payButton');
    const paymentStatus = document.getElementById('paymentStatus');
    
    // Disable button
    payButton.disabled = true;
    payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    paymentStatus.style.display = 'none';
    
    // Initialize payment
    fetch('{{ route("paystack.initialize") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            order_id: orderId,
            email: email
        })
    })
    .then(response => {
        // Check if response is ok
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to initialize payment');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data) {
            const paymentReference = data.data.reference;
            
            // Use traditional Paystack Popup method (this was working before)
            try {
                const handler = PaystackPop.setup({
                    key: '{{ config("services.paystack.public_key") }}',
                    email: email,
                    amount: {{ $order->total_amount * 100 }}, // Amount in kobo
                    ref: paymentReference,
                    callback: function(response) {
                        // Verify payment after successful transaction
                        verifyPayment(paymentReference);
                    },
                    onClose: function() {
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="fas fa-lock me-2"></i>Pay {{ \App\Models\Setting::formatPrice($order->total_amount) }} with Paystack';
                        paymentStatus.style.display = 'block';
                        paymentStatus.className = 'alert alert-warning';
                        paymentStatus.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Payment window closed. Please try again.';
                    }
                });
                
                handler.openIframe();
            } catch (error) {
                console.error('Paystack setup error:', error);
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-lock me-2"></i>Pay ${{ number_format($order->total_amount, 2) }} with Paystack';
                paymentStatus.style.display = 'block';
                paymentStatus.className = 'alert alert-danger';
                paymentStatus.innerHTML = '<i class="fas fa-times-circle me-2"></i><strong>Error:</strong> Failed to initialize payment popup. Please try again.';
            }
        } else {
            payButton.disabled = false;
            payButton.innerHTML = '<i class="fas fa-lock me-2"></i>Pay ${{ number_format($order->total_amount, 2) }} with Paystack';
            paymentStatus.style.display = 'block';
            paymentStatus.className = 'alert alert-danger';
            paymentStatus.innerHTML = '<i class="fas fa-times-circle me-2"></i><strong>Error:</strong> ' + (data.message || 'Failed to initialize payment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        payButton.disabled = false;
        payButton.innerHTML = '<i class="fas fa-lock me-2"></i>Pay ${{ number_format($order->total_amount, 2) }} with Paystack';
        paymentStatus.style.display = 'block';
        paymentStatus.className = 'alert alert-danger';
        paymentStatus.innerHTML = '<i class="fas fa-times-circle me-2"></i><strong>Error:</strong> ' + (error.message || 'An error occurred. Please try again.');
    });
});

function verifyPayment(reference) {
    const paymentStatus = document.getElementById('paymentStatus');
    paymentStatus.style.display = 'block';
    paymentStatus.className = 'alert alert-info';
    paymentStatus.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying payment...';
    
    fetch('{{ route("paystack.verify") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            reference: reference
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            paymentStatus.className = 'alert alert-success';
            paymentStatus.innerHTML = '<i class="fas fa-check-circle me-2"></i>Payment successful! Redirecting...';
            
            // Redirect to success page
            setTimeout(() => {
                window.location.href = '{{ route("checkout.success", $order->id) }}';
            }, 2000);
        } else {
            paymentStatus.className = 'alert alert-danger';
            paymentStatus.innerHTML = '<i class="fas fa-times-circle me-2"></i>' + (data.message || 'Payment verification failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        paymentStatus.className = 'alert alert-danger';
        paymentStatus.innerHTML = '<i class="fas fa-times-circle me-2"></i>An error occurred while verifying payment.';
    });
}
</script>
@endpush
@endsection

