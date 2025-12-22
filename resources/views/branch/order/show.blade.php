@extends('web.layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-invoice me-2"></i>Order #{{ $order->order_number }}</h2>
        <a href="{{ route('branch.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product->name }}</strong>
                                            @if($item->product->requires_prescription)
                                                <span class="badge bg-warning ms-2">Prescription Required</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                            <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                            @if($order->customer_email)
                                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Delivery Address:</strong></p>
                            <p>{{ $order->delivery_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->prescription)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Prescription</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Prescription Number:</strong> {{ $order->prescription->prescription_number }}</p>
                        @if($order->prescription->doctor_name)
                            <p><strong>Doctor:</strong> {{ $order->prescription->doctor_name }}</p>
                        @endif
                        @if($order->prescription->file_path)
                            <a href="{{ Storage::url($order->prescription->file_path) }}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-file-pdf me-2"></i>View Prescription
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }} ms-2">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Branch:</strong><br>
                        {{ $order->branch->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Order Date:</strong><br>
                        {{ $order->created_at->format('M d, Y h:i A') }}
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->delivery_fee > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Fee:</span>
                            <span>${{ number_format($order->delivery_fee, 2) }}</span>
                        </div>
                    @endif
                    @if($order->discount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount:</span>
                            <span>-${{ number_format($order->discount, 2) }}</span>
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>${{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                    @if($order->payment_status)
                        <div class="mt-3">
                            <strong>Payment Status:</strong>
                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} ms-2">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            @if($order->notes)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Notes</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

