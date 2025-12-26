@extends('web.layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header  text-white d-flex justify-content-between align-items-center" style="background-color:#dc8423;">
                    <h4 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Order Details
                    </h4>
                    <a href="{{ route('user.orders.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                            <p class="mb-1"><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                            <p class="mb-1">
                                <strong>Status:</strong> 
                                @if($order->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($order->status === 'approved')
                                    <span class="badge bg-info">Approved</span>
                                @elseif($order->status === 'packing')
                                    <span class="badge bg-primary">Packing</span>
                                @elseif($order->status === 'packed')
                                    <span class="badge bg-secondary">Packed</span>
                                @elseif($order->status === 'out_for_delivery')
                                    <span class="badge bg-primary">Out for Delivery</span>
                                @elseif($order->status === 'delivered')
                                    <span class="badge bg-success">Delivered</span>
                                @elseif($order->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="badge bg-dark">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endif
                            </p>
                            @if($order->approved_at)
                                <p class="mb-1"><strong>Approved At:</strong> {{ $order->approved_at->format('M d, Y h:i A') }}</p>
                            @endif
                            @if($order->packed_at)
                                <p class="mb-1"><strong>Packed At:</strong> {{ $order->packed_at->format('M d, Y h:i A') }}</p>
                            @endif
                            @if($order->delivered_at)
                                <p class="mb-1"><strong>Delivered At:</strong> {{ $order->delivered_at->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5>Delivery Information</h5>
                            <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                            @if($order->customer_email)
                                <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                            @endif
                            <p class="mb-1"><strong>Address:</strong> {{ $order->delivery_address }}</p>
                            @if($order->deliveryZone)
                                <p class="mb-1"><strong>Delivery Zone:</strong> {{ $order->deliveryZone->name }}</p>
                            @endif
                        </div>
                    </div>

                    @if($order->prescription)
                        <div class="alert alert-info mb-4">
                            <h6><i class="fas fa-prescription me-2"></i>Prescription Information</h6>
                            <p class="mb-1"><strong>Prescription Number:</strong> {{ $order->prescription->prescription_number }}</p>
                            <p class="mb-1"><strong>Status:</strong> 
                                @if($order->prescription->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($order->prescription->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </p>
                            @if($order->prescription->doctor_name)
                                <p class="mb-1"><strong>Doctor:</strong> {{ $order->prescription->doctor_name }}</p>
                            @endif
                            @if($order->prescription->rejection_reason)
                                <p class="mb-0"><strong>Rejection Reason:</strong> {{ $order->prescription->rejection_reason }}</p>
                            @endif
                        </div>
                    @endif

                    <h5 class="mt-4">Order Items</h5>
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
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product->images && is_array($item->product->images) && count($item->product->images) > 0)
                                                    <img src="{{ asset('storage/' . $item->product->images[0]) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product->name }}</strong>
                                                    @if($item->batch)
                                                        <br><small class="text-muted">Batch: {{ $item->batch->batch_number }}</small>
                                                        @if($item->batch->expiry_date)
                                                            <br><small class="text-muted">Expiry: {{ $item->batch->expiry_date->format('M d, Y') }}</small>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
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
                                    <th>{{ \App\Models\Setting::formatPrice($order->subtotal) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Delivery Fee:</th>
                                    <th>{{ \App\Models\Setting::formatPrice($order->delivery_fee) }}</th>
                                </tr>
                                @if($order->discount > 0)
                                    <tr>
                                        <th colspan="3" class="text-end">Discount:</th>
                                        <th>-{{ \App\Models\Setting::formatPrice($order->discount) }}</th>
                                    </tr>
                                @endif
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>{{ \App\Models\Setting::formatPrice($order->total_amount) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($order->notes)
                        <div class="mt-3">
                            <h6>Order Notes:</h6>
                            <p class="text-muted">{{ $order->notes }}</p>
                        </div>
                    @endif

                    @if($order->rejection_reason)
                        <div class="alert alert-danger mt-3">
                            <h6><i class="fas fa-times-circle me-2"></i>Rejection Reason</h6>
                            <p class="mb-0">{{ $order->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

