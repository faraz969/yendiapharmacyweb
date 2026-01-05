@extends('web.layouts.app')

@section('title', 'Insurance Request Details')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-shield-alt me-2" style="color:#dc8423;"></i>Insurance Request Details
    </h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Request #{{ $request->request_number }}</h5>
                    <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : ($request->status === 'order_created' ? 'info' : 'warning')) }}">
                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Insurance Company:</strong> {{ $request->insuranceCompany->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Insurance Number:</strong> {{ $request->insurance_number }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer Name:</strong> {{ $request->customer_name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Phone:</strong> {{ $request->customer_phone }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Email:</strong> {{ $request->customer_email ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Branch:</strong> {{ $request->branch->name }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Created:</strong> {{ $request->created_at->format('M d, Y h:i A') }}
                        </div>
                        @if($request->approved_at)
                        <div class="col-md-6">
                            <strong>Approved:</strong> {{ $request->approved_at->format('M d, Y h:i A') }}
                        </div>
                        @endif
                    </div>

                    @if($request->admin_notes)
                    <div class="alert alert-info">
                        <strong>Admin Notes:</strong> {{ $request->admin_notes }}
                    </div>
                    @endif

                    @if($request->rejection_reason)
                    <div class="alert alert-danger">
                        <strong>Rejection Reason:</strong> {{ $request->rejection_reason }}
                    </div>
                    @endif

                    @if($request->order)
                    <div class="alert alert-success">
                        <strong>Order Created:</strong> 
                        <a href="{{ route('user.orders.show', $request->order) }}">
                            {{ $request->order->order_number }}
                        </a>
                    </div>
                    @endif

                    <!-- Insurance Card Images -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Card Front:</strong><br>
                            <img src="{{ Storage::url($request->card_front_image) }}" 
                                 alt="Card Front" 
                                 class="img-fluid mt-2" 
                                 style="max-width: 100%; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div class="col-md-6">
                            <strong>Card Back:</strong><br>
                            <img src="{{ Storage::url($request->card_back_image) }}" 
                                 alt="Card Back" 
                                 class="img-fluid mt-2" 
                                 style="max-width: 100%; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    @if($request->prescription_image)
                    <div class="mb-3">
                        <strong>Prescription:</strong><br>
                        <img src="{{ Storage::url($request->prescription_image) }}" 
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
                                    @foreach($request->items as $item)
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
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('user.services.insurance-requests') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Insurance Requests
        </a>
    </div>
</div>
@endsection

