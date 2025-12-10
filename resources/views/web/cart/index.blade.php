@extends('web.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h2>

    @if(count($cartItems) > 0)
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @foreach($cartItems as $item)
                            <div class="cart-item">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        @if($item['product']->images && is_array($item['product']->images) && count($item['product']->images) > 0)
                                            <img src="{{ asset('storage/' . $item['product']->images[0]) }}" class="img-fluid rounded" alt="{{ $item['product']->name }}">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 80px; height: 80px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="mb-1">{{ $item['product']->name }}</h6>
                                        <p class="text-muted small mb-0">{{ $item['product']->category->name }}</p>
                                        @if($item['product']->requires_prescription)
                                            <span class="badge-prescription">
                                                <i class="fas fa-prescription me-1"></i>Rx Required
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <span class="fw-bold">${{ number_format($item['price'], 2) }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <form action="{{ route('cart.update', $item['product']->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="quantity" class="form-control form-control-sm" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->track_batch ? $item['product']->total_stock : 999 }}" onchange="this.form.submit()">
                                        </form>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">${{ number_format($item['total'], 2) }}</span>
                                            <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST" class="d-inline ms-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear your cart?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-2"></i>Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span class="fw-bold">${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Delivery Fee:</span>
                            <span class="text-muted">Calculated at checkout</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold text-primary fs-5">${{ number_format($total, 2) }}</span>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted mb-4">Start shopping to add items to your cart</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Shop Now
            </a>
        </div>
    @endif
</div>
@endsection

