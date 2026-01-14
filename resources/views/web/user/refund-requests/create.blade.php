@extends('web.layouts.app')

@section('title', 'Request Refund')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header text-white" style="background-color:#dc8423;">
                    <h4 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Request Refund
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <i class="fas fa-money-bill-wave fa-4x text-success mb-3"></i>
                        <h5>Request Refund for Order #{{ $order->order_number }}</h5>
                        <p class="text-muted">Refund Amount: <strong>{{ \App\Models\Setting::formatPrice($order->total_amount) }}</strong></p>
                        <p class="text-muted">Please provide your refund details to cancel this paid order</p>
                    </div>

                    <form action="{{ route('user.refund-requests.store', $order) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Refund Method <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 refund-method-card" data-method="mobile_money" style="cursor: pointer; border: 2px solid #dee2e6;">
                                        <div class="card-body text-center">
                                            <i class="fas fa-mobile-alt fa-3x mb-3 text-info"></i>
                                            <h6>Mobile Money</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 refund-method-card" data-method="bank_transfer" style="cursor: pointer; border: 2px solid #dee2e6;">
                                        <div class="card-body text-center">
                                            <i class="fas fa-university fa-3x mb-3 text-primary"></i>
                                            <h6>Bank Transfer</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="refund_method" id="refund_method" value="mobile_money" required>
                            @error('refund_method')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mobile Money Fields -->
                        <div id="mobile_money_fields">
                            <h6 class="mb-3"><i class="fas fa-mobile-alt me-2"></i>Mobile Money Details</h6>
                            <div class="mb-3">
                                <label for="mobile_money_provider" class="form-label">Provider <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('mobile_money_provider') is-invalid @enderror" 
                                       id="mobile_money_provider" name="mobile_money_provider" 
                                       placeholder="MTN, Vodafone, AirtelTigo, etc." 
                                       value="{{ old('mobile_money_provider') }}" required>
                                @error('mobile_money_provider')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="mobile_money_number" class="form-label">Mobile Money Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('mobile_money_number') is-invalid @enderror" 
                                       id="mobile_money_number" name="mobile_money_number" 
                                       placeholder="Enter your mobile money number" 
                                       value="{{ old('mobile_money_number') }}" required>
                                @error('mobile_money_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="mobile_money_name" class="form-label">Account Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('mobile_money_name') is-invalid @enderror" 
                                       id="mobile_money_name" name="mobile_money_name" 
                                       placeholder="Name on mobile money account" 
                                       value="{{ old('mobile_money_name') }}" required>
                                @error('mobile_money_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Bank Account Fields -->
                        <div id="bank_transfer_fields" style="display: none;">
                            <h6 class="mb-3"><i class="fas fa-university me-2"></i>Bank Account Details</h6>
                            <div class="mb-3">
                                <label for="bank_name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                       id="bank_name" name="bank_name" 
                                       placeholder="Enter bank name" 
                                       value="{{ old('bank_name') }}">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="account_number" class="form-label">Account Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                       id="account_number" name="account_number" 
                                       placeholder="Enter account number" 
                                       value="{{ old('account_number') }}">
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="account_name" class="form-label">Account Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                                       id="account_name" name="account_name" 
                                       placeholder="Name on bank account" 
                                       value="{{ old('account_name') }}">
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="account_type" class="form-label">Account Type (Optional)</label>
                                <input type="text" class="form-control @error('account_type') is-invalid @enderror" 
                                       id="account_type" name="account_type" 
                                       placeholder="Savings, Current, etc." 
                                       value="{{ old('account_type') }}">
                                @error('account_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="branch_name" class="form-label">Branch Name (Optional)</label>
                                <input type="text" class="form-control @error('branch_name') is-invalid @enderror" 
                                       id="branch_name" name="branch_name" 
                                       placeholder="Bank branch name" 
                                       value="{{ old('branch_name') }}">
                                @error('branch_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Submit Refund Request
                            </button>
                            <a href="{{ route('user.orders.show', $order) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const methodCards = document.querySelectorAll('.refund-method-card');
        const refundMethodInput = document.getElementById('refund_method');
        const mobileMoneyFields = document.getElementById('mobile_money_fields');
        const bankTransferFields = document.getElementById('bank_transfer_fields');

        methodCards.forEach(card => {
            card.addEventListener('click', function() {
                const method = this.dataset.method;
                
                // Update hidden input
                refundMethodInput.value = method;
                
                // Update card borders
                methodCards.forEach(c => {
                    c.style.border = '2px solid #dee2e6';
                });
                this.style.border = '2px solid #28a745';
                
                // Show/hide fields
                if (method === 'mobile_money') {
                    mobileMoneyFields.style.display = 'block';
                    bankTransferFields.style.display = 'none';
                    // Make mobile money fields required
                    document.getElementById('mobile_money_provider').required = true;
                    document.getElementById('mobile_money_number').required = true;
                    document.getElementById('mobile_money_name').required = true;
                    // Make bank fields not required
                    document.getElementById('bank_name').required = false;
                    document.getElementById('account_number').required = false;
                    document.getElementById('account_name').required = false;
                } else {
                    mobileMoneyFields.style.display = 'none';
                    bankTransferFields.style.display = 'block';
                    // Make bank fields required
                    document.getElementById('bank_name').required = true;
                    document.getElementById('account_number').required = true;
                    document.getElementById('account_name').required = true;
                    // Make mobile money fields not required
                    document.getElementById('mobile_money_provider').required = false;
                    document.getElementById('mobile_money_number').required = false;
                    document.getElementById('mobile_money_name').required = false;
                }
            });
        });

        // Set initial state
        if (refundMethodInput.value === 'mobile_money') {
            methodCards[0].style.border = '2px solid #28a745';
        } else {
            methodCards[1].style.border = '2px solid #28a745';
        }
    });
</script>
@endpush
@endsection

