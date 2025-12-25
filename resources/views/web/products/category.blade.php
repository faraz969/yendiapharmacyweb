@extends('web.layouts.app')

@section('title', $category->name)

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-12">
            <h2>{{ $category->name }}</h2>
            @if($category->description)
                <p class="text-muted">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    @if($products->count() > 0)
        <div class="row g-4">
            @foreach($products as $product)
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
                            @if($product->requires_prescription)
                                <span class="badge-prescription mb-2">
                                    <i class="fas fa-prescription me-1"></i>Rx Required
                                </span>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="product-price">{{ \App\Models\Setting::formatPrice($product->selling_price) }}</span>
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

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4>No products in this category</h4>
        </div>
    @endif
</div>
@endsection

