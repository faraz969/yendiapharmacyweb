@extends('web.layouts.app')

@section('title', 'Track Your Order')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-search text-primary me-2"></i>Track Your Order
                    </h2>
                    <p class="text-center text-muted mb-4">Enter your order number and phone number to track your order status</p>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('order.tracking.track') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="order_number" class="form-label">Order Number</label>
                            <input type="text" class="form-control @error('order_number') is-invalid @enderror" 
                                   id="order_number" name="order_number" 
                                   value="{{ old('order_number') }}" 
                                   placeholder="e.g., ORD-2025-000001" required autofocus>
                            @error('order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                   id="customer_phone" name="customer_phone" 
                                   value="{{ old('customer_phone') }}" 
                                   placeholder="Phone number used during checkout" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Track Order
                        </button>
                    </form>

                    @auth
                        <div class="text-center mt-4">
                            <p class="mb-0">Or view all your orders in 
                                <a href="{{ route('user.orders.index') }}" class="text-primary">My Orders</a>
                            </p>
                        </div>
                    @else
                        <div class="text-center mt-4">
                            <p class="mb-0">
                                <a href="{{ route('login') }}" class="text-primary">Login</a> to view all your orders
                            </p>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

