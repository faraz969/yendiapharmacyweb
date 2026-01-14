@extends('web.layouts.app')

@section('title', 'Refund Request Details')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color:#dc8423;">
                    <h4 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Refund Request Details
                    </h4>
                    <a href="{{ route('user.refund-requests.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Refund Requests
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Refund Information</h5>
                            <p class="mb-1"><strong>Refund Number:</strong> {{ $refundRequest->refund_number }}</p>
                            <p class="mb-1"><strong>Order Number:</strong> 
                                <a href="{{ route('user.orders.show', $refundRequest->order_id) }}" class="text-decoration-none">
                                    {{ $refundRequest->order->order_number }}
                                </a>
                            </p>
                            <p class="mb-1"><strong>Refund Amount:</strong> 
                                <span class="text-success fw-bold">{{ \App\Models\Setting::formatPrice($refundRequest->refund_amount) }}</span>
                            </p>
                            <p class="mb-1">
                                <strong>Status:</strong> 
                                @php
                                    $badgeColors = [
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'rejected' => 'danger',
                                        'processed' => 'primary',
                                        'completed' => 'success',
                                    ];
                                    $color = $badgeColors[$refundRequest->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst($refundRequest->status) }}
                                </span>
                            </p>
                            <p class="mb-1"><strong>Created:</strong> {{ $refundRequest->created_at->format('M d, Y h:i A') }}</p>
                            @if($refundRequest->processed_at)
                                <p class="mb-1"><strong>Processed At:</strong> {{ $refundRequest->processed_at->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5>Refund Method</h5>
                            <p class="mb-1">
                                <strong>Method:</strong> 
                                <span class="badge bg-{{ $refundRequest->refund_method === 'mobile_money' ? 'info' : 'primary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $refundRequest->refund_method)) }}
                                </span>
                            </p>
                            
                            @if($refundRequest->refund_method === 'mobile_money')
                                <div class="alert alert-info mt-3">
                                    <h6><i class="fas fa-mobile-alt me-2"></i>Mobile Money Details</h6>
                                    <p class="mb-1"><strong>Provider:</strong> {{ $refundRequest->mobile_money_provider }}</p>
                                    <p class="mb-1"><strong>Number:</strong> {{ $refundRequest->mobile_money_number }}</p>
                                    <p class="mb-0"><strong>Name:</strong> {{ $refundRequest->mobile_money_name }}</p>
                                </div>
                            @else
                                <div class="alert alert-primary mt-3">
                                    <h6><i class="fas fa-university me-2"></i>Bank Account Details</h6>
                                    <p class="mb-1"><strong>Bank:</strong> {{ $refundRequest->bank_name }}</p>
                                    <p class="mb-1"><strong>Account Number:</strong> {{ $refundRequest->account_number }}</p>
                                    <p class="mb-1"><strong>Account Name:</strong> {{ $refundRequest->account_name }}</p>
                                    @if($refundRequest->account_type)
                                        <p class="mb-1"><strong>Account Type:</strong> {{ ucfirst($refundRequest->account_type) }}</p>
                                    @endif
                                    @if($refundRequest->branch_name)
                                        <p class="mb-0"><strong>Branch:</strong> {{ $refundRequest->branch_name }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($refundRequest->rejection_reason)
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-times-circle me-2"></i>Rejection Reason</h6>
                            <p class="mb-0">{{ $refundRequest->rejection_reason }}</p>
                        </div>
                    @endif

                    @if($refundRequest->admin_notes)
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Admin Notes</h6>
                            <p class="mb-0">{{ $refundRequest->admin_notes }}</p>
                        </div>
                    @endif

                    @if($refundRequest->transfer_code)
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i>Transfer Information</h6>
                            <p class="mb-1"><strong>Transfer Code:</strong> {{ $refundRequest->transfer_code }}</p>
                            @if($refundRequest->refund_reference)
                                <p class="mb-0"><strong>Reference:</strong> {{ $refundRequest->refund_reference }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Order Summary -->
                    <h5 class="mt-4">Order Summary</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($refundRequest->order->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product_name ?? ($item->product->name ?? 'N/A') }}
                                            @if($item->product_id)
                                                <br><small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ \App\Models\Setting::formatPrice($item->unit_price) }}</td>
                                        <td>{{ \App\Models\Setting::formatPrice($item->total_price) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <th>{{ \App\Models\Setting::formatPrice($refundRequest->order->subtotal) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Delivery Fee:</th>
                                    <th>{{ \App\Models\Setting::formatPrice($refundRequest->order->delivery_fee) }}</th>
                                </tr>
                                @if($refundRequest->order->discount > 0)
                                    <tr>
                                        <th colspan="3" class="text-end">Discount:</th>
                                        <th>-{{ \App\Models\Setting::formatPrice($refundRequest->order->discount) }}</th>
                                    </tr>
                                @endif
                                <tr>
                                    <th colspan="3" class="text-end">Total Amount:</th>
                                    <th>{{ \App\Models\Setting::formatPrice($refundRequest->order->total_amount) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

