@extends('admin.layouts.app')

@section('title', 'Insurance Request Details')
@section('page-title', 'Insurance Request Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Request #{{ $insuranceRequest->request_number }}</h5>
                <span class="badge bg-{{ $insuranceRequest->status === 'approved' ? 'success' : ($insuranceRequest->status === 'rejected' ? 'danger' : ($insuranceRequest->status === 'order_created' ? 'info' : 'warning')) }}">
                    {{ ucfirst(str_replace('_', ' ', $insuranceRequest->status)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Customer Name:</strong> {{ $insuranceRequest->customer_name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Phone:</strong> {{ $insuranceRequest->customer_phone }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Email:</strong> {{ $insuranceRequest->customer_email ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Insurance Company:</strong> {{ $insuranceRequest->insuranceCompany->name }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Insurance Number:</strong> {{ $insuranceRequest->insurance_number }}
                    </div>
                    <div class="col-md-6">
                        <strong>Branch:</strong> {{ $insuranceRequest->branch->name }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Submitted By:</strong> {{ $insuranceRequest->user->name ?? 'Guest' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Created:</strong> {{ $insuranceRequest->created_at->format('M d, Y H:i') }}
                    </div>
                </div>

                <!-- Insurance Card Images -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Card Front:</strong><br>
                        <img src="{{ Storage::url($insuranceRequest->card_front_image) }}" 
                             alt="Card Front" 
                             class="img-fluid mt-2" 
                             style="max-width: 100%; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div class="col-md-6">
                        <strong>Card Back:</strong><br>
                        <img src="{{ Storage::url($insuranceRequest->card_back_image) }}" 
                             alt="Card Back" 
                             class="img-fluid mt-2" 
                             style="max-width: 100%; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>

                @if($insuranceRequest->prescription_image)
                <div class="mb-3">
                    <strong>Prescription:</strong><br>
                    <img src="{{ Storage::url($insuranceRequest->prescription_image) }}" 
                         alt="Prescription" 
                         class="img-fluid mt-2" 
                         style="max-width: 100%; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                @endif

                <!-- Items -->
                <div class="mb-3">
                    <strong>Requested Items:</strong>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($insuranceRequest->items as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->notes ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($insuranceRequest->admin_notes)
                    <div class="mb-3">
                        <strong>Admin Notes:</strong>
                        <p>{{ $insuranceRequest->admin_notes }}</p>
                    </div>
                @endif
                @if($insuranceRequest->rejection_reason)
                    <div class="alert alert-danger">
                        <strong>Rejection Reason:</strong> {{ $insuranceRequest->rejection_reason }}
                    </div>
                @endif

                @if($insuranceRequest->order)
                    <div class="alert alert-info">
                        <strong>Order Created:</strong> 
                        <a href="{{ route('admin.orders.show', $insuranceRequest->order) }}">
                            {{ $insuranceRequest->order->order_number }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        @if($insuranceRequest->status === 'pending')
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    @if(session('unavailable_items'))
                        <div class="alert alert-warning mb-3">
                            <strong>The following products are out of stock or not available:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach(session('unavailable_items') as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                            <p class="mb-0 mt-2">Do you still wish to approve this request?</p>
                        </div>
                        <form action="{{ route('admin.insurance-requests.approve', session('insurance_request_id', $insuranceRequest->id)) }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="force_approve" value="1">
                            <div class="mb-2">
                                <label for="admin_notes" class="form-label">Admin Notes (optional)</label>
                                <textarea name="admin_notes" id="admin_notes" class="form-control" rows="2">{{ session('admin_notes') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-check me-2"></i>Yes, Approve Anyway
                            </button>
                            <a href="{{ route('admin.insurance-requests.show', $insuranceRequest) }}" class="btn btn-secondary w-100">
                                <i class="fas fa-times me-2"></i>No, Keep Pending
                            </a>
                        </form>
                    @else
                        <form action="{{ route('admin.insurance-requests.approve', $insuranceRequest->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-2">
                                <label for="admin_notes" class="form-label">Admin Notes (optional)</label>
                                <textarea name="admin_notes" id="admin_notes" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i>Approve Request
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.insurance-requests.reject', $insuranceRequest->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i>Reject Request
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($insuranceRequest->status === 'approved' && !$insuranceRequest->order)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Creation</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Note:</strong> The customer will create the order themselves. They will be notified and can select delivery type and address from their account.
                    </div>
                    <p class="text-muted small mb-0">
                        Once approved, the customer can access this request from their account and create an order with delivery details.
                    </p>
                </div>
            </div>
        @endif

        @if($insuranceRequest->approvedBy)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Approval Info</h6>
                </div>
                <div class="card-body">
                    <p><strong>Approved By:</strong> {{ $insuranceRequest->approvedBy->name }}</p>
                    @if($insuranceRequest->approved_at)
                        <p><strong>Approved At:</strong> {{ $insuranceRequest->approved_at->format('M d, Y H:i') }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.insurance-requests.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Insurance Requests
    </a>
</div>

@push('scripts')
<script>
document.getElementById('delivery_type').addEventListener('change', function() {
    const deliveryFields = document.getElementById('deliveryFields');
    if (this.value === 'delivery') {
        deliveryFields.style.display = 'block';
        document.getElementById('delivery_zone_id').required = true;
        document.getElementById('delivery_address').required = true;
    } else {
        deliveryFields.style.display = 'none';
        document.getElementById('delivery_zone_id').required = false;
        document.getElementById('delivery_address').required = false;
    }
});
</script>
@endpush
@endsection

