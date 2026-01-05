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
                    <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Create Order</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.insurance-requests.create-order', $insuranceRequest->id) }}" method="POST" id="createOrderForm">
                        @csrf
                        <div class="mb-3">
                            <label for="delivery_type" class="form-label">Delivery Type <span class="text-danger">*</span></label>
                            <select name="delivery_type" id="delivery_type" class="form-select" required>
                                <option value="">Select...</option>
                                <option value="delivery">Delivery</option>
                                <option value="pickup">Pickup</option>
                            </select>
                        </div>

                        <div id="deliveryFields" style="display: none;">
                            <div class="mb-3">
                                <label for="delivery_zone_id" class="form-label">Delivery Zone <span class="text-danger">*</span></label>
                                <select name="delivery_zone_id" id="delivery_zone_id" class="form-select">
                                    <option value="">Select Zone...</option>
                                    @foreach($deliveryZones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }} - {{ \App\Models\Setting::formatPrice($zone->delivery_fee) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="delivery_address" class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                <textarea name="delivery_address" id="delivery_address" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart me-2"></i>Create Order (Delivery Fee Only)
                        </button>
                        <small class="text-muted d-block mt-2">Note: Items are free with insurance. Customer only pays delivery fee.</small>
                    </form>
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

