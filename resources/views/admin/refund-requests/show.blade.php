@extends('admin.layouts.app')

@section('title', 'Refund Request Details')
@section('page-title', 'Refund Request Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Refund Request #{{ $refundRequest->refund_number }}</h5>
                <span class="badge bg-{{ $refundRequest->status === 'pending' ? 'warning' : ($refundRequest->status === 'approved' ? 'info' : ($refundRequest->status === 'rejected' ? 'danger' : ($refundRequest->status === 'processed' ? 'primary' : 'success'))) }}">
                    {{ ucfirst($refundRequest->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Order Number:</strong> 
                        <a href="{{ route('admin.orders.show', $refundRequest->order_id) }}">
                            {{ $refundRequest->order->order_number }}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <strong>Refund Amount:</strong> 
                        <span class="text-success fw-bold">{{ \App\Models\Setting::formatPrice($refundRequest->refund_amount) }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Customer Name:</strong> {{ $refundRequest->order->customer_name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Phone:</strong> {{ $refundRequest->order->customer_phone }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Refund Method:</strong> 
                        <span class="badge bg-{{ $refundRequest->refund_method === 'mobile_money' ? 'info' : 'primary' }}">
                            {{ ucfirst(str_replace('_', ' ', $refundRequest->refund_method)) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Created:</strong> {{ $refundRequest->created_at->format('M d, Y H:i') }}
                    </div>
                </div>

                @if($refundRequest->refund_method === 'mobile_money')
                    <div class="alert alert-info">
                        <h6><i class="fas fa-mobile-alt me-2"></i>Mobile Money Details</h6>
                        <p class="mb-1"><strong>Provider:</strong> {{ $refundRequest->mobile_money_provider }}</p>
                        <p class="mb-1"><strong>Number:</strong> {{ $refundRequest->mobile_money_number }}</p>
                        <p class="mb-0"><strong>Name:</strong> {{ $refundRequest->mobile_money_name }}</p>
                    </div>
                @else
                    <div class="alert alert-primary">
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

                @if($refundRequest->admin_notes)
                    <div class="mb-3">
                        <strong>Admin Notes:</strong>
                        <p>{{ $refundRequest->admin_notes }}</p>
                    </div>
                @endif

                @if($refundRequest->rejection_reason)
                    <div class="alert alert-danger">
                        <strong>Rejection Reason:</strong> {{ $refundRequest->rejection_reason }}
                    </div>
                @endif

                @if($refundRequest->refund_reference)
                    <div class="alert alert-info">
                        <strong>Transfer Reference:</strong> {{ $refundRequest->refund_reference }}
                    </div>
                @endif

                @if($refundRequest->transfer_code)
                    <div class="alert alert-info">
                        <strong>Transfer Code:</strong> {{ $refundRequest->transfer_code }}
                    </div>
                @endif

                @if($refundRequest->recipient_code)
                    <div class="alert alert-info">
                        <strong>Recipient Code:</strong> {{ $refundRequest->recipient_code }}
                    </div>
                @endif

                @if($refundRequest->processed_at)
                    <div class="mb-3">
                        <strong>Processed At:</strong> {{ $refundRequest->processed_at->format('M d, Y H:i') }}
                        @if($refundRequest->processedBy)
                            by {{ $refundRequest->processedBy->name }}
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Details -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Order Details</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
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
                                <th colspan="3">Subtotal:</th>
                                <th>{{ \App\Models\Setting::formatPrice($refundRequest->order->subtotal) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3">Delivery Fee:</th>
                                <th>{{ \App\Models\Setting::formatPrice($refundRequest->order->delivery_fee) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3">Discount:</th>
                                <th>{{ \App\Models\Setting::formatPrice($refundRequest->order->discount) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3">Total Amount:</th>
                                <th>{{ \App\Models\Setting::formatPrice($refundRequest->order->total_amount) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        @if($refundRequest->status === 'pending')
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.refund-requests.approve', $refundRequest->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <label for="admin_notes" class="form-label">Admin Notes (optional)</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-2"></i>Approve Refund Request
                        </button>
                    </form>

                    <form action="{{ route('admin.refund-requests.reject', $refundRequest->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i>Reject Refund Request
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($refundRequest->status === 'approved')
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Transfers are automatically processed when a refund request is approved. 
                If the transfer was not initiated, you can manually process it using the form below.
            </div>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Manually Process Refund (Fallback)</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.refund-requests.mark-processed', $refundRequest->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="refund_reference" class="form-label">Refund Reference (optional)</label>
                            <input type="text" name="refund_reference" id="refund_reference" class="form-control" placeholder="Enter refund reference from payment gateway">
                            <small class="form-text text-muted">Reference number from payment gateway after processing refund</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check me-2"></i>Mark as Processed
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($refundRequest->status === 'processed')
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-check-double me-2"></i>Complete Refund</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.refund-requests.mark-completed', $refundRequest->id) }}" method="POST">
                        @csrf
                        <p class="text-muted">Mark this refund as completed after confirming the refund has been received by the customer.</p>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check-double me-2"></i>Mark as Completed
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.refund-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Refund Requests
        </a>
        @if(!in_array($refundRequest->status, ['processed', 'completed']))
        <form action="{{ route('admin.refund-requests.destroy', $refundRequest) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this refund request? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-2"></i>Delete Refund Request
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

