@extends('web.layouts.app')

@section('title', 'Order Confirmed')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="mb-3">Order Placed Successfully!</h2>
                    <p class="text-muted mb-4">Thank you for your order. We'll process it shortly.</p>
                    
                    <div class="alert alert-info">
                        <strong>Order Number:</strong> <code>{{ $order->order_number }}</code>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Details</h5>
                        </div>
                        <div class="card-body text-start">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Customer:</strong> {{ $order->customer_name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Phone:</strong> {{ $order->customer_phone }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Delivery Address:</strong><br>
                                {{ $order->delivery_address }}
                            </div>
                            <hr>
                            <div class="table-responsive">
                                <table class="table">
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
                                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                                <td>${{ number_format($item->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td><strong>${{ number_format($order->subtotal, 2) }}</strong></td>
                                        </tr>
                                        @if($order->delivery_fee > 0)
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Delivery Fee:</strong></td>
                                                <td><strong>${{ number_format($order->delivery_fee, 2) }}</strong></td>
                                            </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="mt-3">
                                <strong>Status:</strong>
                                <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Continue Shopping
                        </a>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-file-invoice me-2"></i>View Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

