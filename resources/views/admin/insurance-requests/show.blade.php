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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Prescription:</strong>
                        <button type="button" class="btn btn-sm btn-primary" onclick="printPrescription('{{ Storage::url($insuranceRequest->prescription_image) }}')">
                            <i class="fas fa-print me-2"></i>Print Prescription
                        </button>
                    </div>
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
function printPrescription(imageUrl) {
    var printWindow = window.open('', '_blank');
    if (!printWindow) {
        alert('Please allow popups to print the prescription');
        return;
    }
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
            <head>
                <title>Print Prescription - {{ $insuranceRequest->request_number }}</title>
                <style>
                    @media print {
                        @page {
                            margin: 0;
                        }
                        body {
                            margin: 0;
                            padding: 0;
                        }
                    }
                    body {
                        margin: 0;
                        padding: 20px;
                        text-align: center;
                        font-family: Arial, sans-serif;
                    }
                    .header {
                        margin-bottom: 20px;
                        text-align: center;
                        border-bottom: 2px solid #ddd;
                        padding-bottom: 15px;
                    }
                    .info-section {
                        margin-bottom: 20px;
                        text-align: left;
                        padding: 15px;
                        background: #f9f9f9;
                        border-radius: 5px;
                    }
                    .info-section p {
                        margin: 5px 0;
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                        display: block;
                        margin: 20px auto;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                    }
                    .footer {
                        margin-top: 20px;
                        text-align: center;
                        font-size: 12px;
                        color: #666;
                        border-top: 1px solid #ddd;
                        padding-top: 15px;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>Insurance Request Prescription</h2>
                    <p><strong>Request Number:</strong> {{ $insuranceRequest->request_number }}</p>
                </div>
                <div class="info-section">
                    <p><strong>Customer Name:</strong> {{ $insuranceRequest->customer_name }}</p>
                    <p><strong>Phone:</strong> {{ $insuranceRequest->customer_phone }}</p>
                    <p><strong>Email:</strong> {{ $insuranceRequest->customer_email ?? 'N/A' }}</p>
                    <p><strong>Insurance Company:</strong> {{ $insuranceRequest->insuranceCompany->name }}</p>
                    <p><strong>Insurance Number:</strong> {{ $insuranceRequest->insurance_number }}</p>
                    <p><strong>Branch:</strong> {{ $insuranceRequest->branch->name }}</p>
                    <p><strong>Request Date:</strong> {{ $insuranceRequest->created_at->format('M d, Y H:i') }}</p>
                </div>
                <img src="${imageUrl}" alt="Prescription" id="prescriptionImg">
                <div class="footer">
                    <p>Printed on: ${new Date().toLocaleString()}</p>
                </div>
                <script>
                    (function() {
                        var img = document.getElementById('prescriptionImg');
                        img.onload = function() {
                            setTimeout(function() {
                                window.print();
                                setTimeout(function() {
                                    window.close();
                                }, 100);
                            }, 250);
                        };
                        img.onerror = function() {
                            alert('Failed to load prescription image. Please try again.');
                            window.close();
                        };
                        // If image is already loaded (cached)
                        if (img.complete && img.naturalHeight !== 0) {
                            img.onload();
                        }
                    })();
                <\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}

// Only add event listener if delivery_type element exists
var deliveryTypeElement = document.getElementById('delivery_type');
if (deliveryTypeElement) {
    deliveryTypeElement.addEventListener('change', function() {
        var deliveryFields = document.getElementById('deliveryFields');
        if (this.value === 'delivery') {
            if (deliveryFields) deliveryFields.style.display = 'block';
            var zoneField = document.getElementById('delivery_zone_id');
            var addressField = document.getElementById('delivery_address');
            if (zoneField) zoneField.required = true;
            if (addressField) addressField.required = true;
        } else {
            if (deliveryFields) deliveryFields.style.display = 'none';
            var zoneField = document.getElementById('delivery_zone_id');
            var addressField = document.getElementById('delivery_address');
            if (zoneField) zoneField.required = false;
            if (addressField) addressField.required = false;
        }
    });
}
</script>
@endpush
@endsection

