@extends('web.layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-4">Your Health, Our Priority</h1>
                <p class="lead mb-4">Get all your prescription and over-the-counter medications delivered to your doorstep.</p>
                <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Shop Now
                </a>
            </div>
            <div class="col-md-6 text-center">
                <i class="fas fa-pills" style="font-size: 200px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Categories -->
    @if($featuredCategories->count() > 0)
        <section class="mb-5">
            <h2 class="mb-4">Shop by Category</h2>
            <div class="row g-4">
                @foreach($featuredCategories as $category)
                    <div class="col-md-4 col-lg-2">
                        <a href="{{ route('products.category', $category->id) }}" class="text-decoration-none">
                            <div class="category-card">
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                                @else
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-folder" style="font-size: 3rem; color: white; opacity: 0.5;"></i>
                                    </div>
                                @endif
                                <div class="category-overlay">
                                    <h5 class="mb-0">{{ $category->name }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Featured Products -->
    @if($featuredProducts->count() > 0)
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Featured Products</h2>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row g-4">
                @foreach($featuredProducts as $product)
                    <div class="col-md-3">
                        <div class="card product-card">
                                @if($product->images && is_array($product->images) && count($product->images) > 0)
                                    <img src="{{ asset('storage/' . $product->images[0]) }}" class="product-image" alt="{{ $product->name }}">
                            @else
                                <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="product-card-body">
                                <h6 class="card-title">{{ Str::limit($product->name, 50) }}</h6>
                                <p class="text-muted small mb-2">{{ $product->category->name }}</p>
                                @if($product->requires_prescription)
                                    <span class="badge-prescription mb-2">
                                        <i class="fas fa-prescription me-1"></i>Rx Required
                                    </span>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="product-price">${{ number_format($product->selling_price, 2) }}</span>
                                    <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>
                                </div>
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Features -->
    <section class="mb-5">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="p-4">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                    <h5>Fast Delivery</h5>
                    <p class="text-muted">Quick and reliable delivery to your doorstep</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4">
                    <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                    <h5>Authentic Products</h5>
                    <p class="text-muted">100% genuine medications from licensed pharmacies</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4">
                    <i class="fas fa-user-md fa-3x text-info mb-3"></i>
                    <h5>Expert Consultation</h5>
                    <p class="text-muted">Get advice from qualified pharmacists</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

