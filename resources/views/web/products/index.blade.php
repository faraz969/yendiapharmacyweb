@extends('web.layouts.app')

@section('title', 'Products')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header  text-white" style="background-color: #dc8423">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('products.index') }}">
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search products...">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Prescription Filter -->
                        <div class="mb-3">
                            <label class="form-label">Prescription</label>
                            <select name="prescription" class="form-select">
                                <option value="">All Products</option>
                                <option value="not_required" {{ request('prescription') == 'not_required' ? 'selected' : '' }}>OTC (No Prescription)</option>
                                <option value="required" {{ request('prescription') == 'required' ? 'selected' : '' }}>Prescription Required</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            Clear
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Products</h2>
                <div>
                    <label class="form-label me-2">Sort:</label>
                    <select class="form-select d-inline-block" style="width: auto;" onchange="window.location.href='{{ route('products.index') }}?sort=' + this.value + '&{{ http_build_query(request()->except('sort')) }}'">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
                    </select>
                </div>
            </div>

            @if($products->count() > 0)
                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-md-4">
                            <div class="card product-card h-100">
                                <div class="position-relative">
                                    @if($product->images && is_array($product->images) && count($product->images) > 0)
                                        <img src="{{ asset('storage/' . $product->images[0]) }}" class="product-image" alt="{{ $product->name }}">
                                    @else
                                        <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    @if($product->discount && $product->discount > 0)
                                        <span class="position-absolute top-0 start-0 m-2 px-2 py-1 text-white fw-bold" style="background: #ee7d09; border-radius: 5px; font-size: 0.75rem;">
                                            {{ \App\Models\Setting::formatPrice($product->discount) }} OFF
                                        </span>
                                    @endif
                                </div>
                                <div class="product-card-body">
                                    <h6 class="card-title" style="min-height: 48px;">{{ Str::limit($product->name, 50) }}</h6>
                                    <p class="text-muted small mb-2">{{ $product->category->name }}</p>
                                    @if($product->requires_prescription)
                                        <span class="badge-prescription mb-2">
                                            <i class="fas fa-prescription me-1"></i>Rx Required
                                        </span>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="product-price">{{ \App\Models\Setting::formatPrice($product->selling_price) }}</span>
                                        <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-cart-plus me-1"></i>ADD
                                            </button>
                                        </form>
                                    </div>
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-link btn-sm w-100 mt-2 text-decoration-none d-flex align-items-center justify-content-center" style="background: transparent; border: none; color: #158d43; padding: 8px;">
                                        View Details <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>No products found</h4>
                    <p class="text-muted">Try adjusting your filters</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

