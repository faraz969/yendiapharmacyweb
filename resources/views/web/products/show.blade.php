@extends('web.layouts.app')

@section('title', $product->name)

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            @if($product->images && is_array($product->images) && count($product->images) > 0)
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($product->images as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" style="height: 500px; object-fit: cover;" alt="{{ $product->name }}">
                            </div>
                        @endforeach
                    </div>
                    @if(count($product->images) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    @endif
                </div>
            @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 500px;">
                    <i class="fas fa-image fa-5x text-muted"></i>
                </div>
            @endif
        </div>

        <div class="col-md-6">
            <h1 class="mb-3">{{ $product->name }}</h1>
            <p class="text-muted mb-3">{{ $product->category->name }}</p>
            
            @if($product->requires_prescription)
                <div class="alert alert-warning">
                    <i class="fas fa-prescription me-2"></i><strong>Prescription Required</strong>
                    @if($product->prescription_notes)
                        <p class="mb-0 mt-2">{{ $product->prescription_notes }}</p>
                    @endif
                </div>
            @endif

            <div class="mb-4">
                <h2 class="text-primary mb-0">{{ \App\Models\Setting::formatPrice($product->selling_price) }}</h2>
                @if($product->track_batch)
                    <p class="text-muted mb-0">
                        <i class="fas fa-check-circle text-success me-1"></i>
                        In Stock: {{ number_format($product->total_stock) }} {{ $product->selling_unit }}(s)
                    </p>
                @endif
            </div>

            @if($product->description)
                <div class="mb-4">
                    <h5>Description</h5>
                    <p>{{ $product->description }}</p>
                </div>
            @endif

            <div class="mb-4">
                <h5>Product Details</h5>
                <ul class="list-unstyled">
                    <li><strong>SKU:</strong> <code>{{ $product->sku }}</code></li>
                    @if($product->barcode)
                        <li><strong>Barcode:</strong> {{ $product->barcode }}</li>
                    @endif
                    <li><strong>Unit:</strong> {{ ucfirst($product->selling_unit) }}</li>
                </ul>
            </div>

            @if($product->track_batch && $product->total_stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $product->total_stock }}" required>
                            <small class="text-muted">Max: {{ $product->total_stock }} {{ $product->selling_unit }}(s)</small>
                        </div>
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </form>
            @elseif(!$product->track_batch)
                <form action="{{ route('cart.add') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Out of Stock
                </div>
            @endif
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-5">
            <h3 class="mb-4">Related Products</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col-md-3">
                        <div class="card product-card">
                                @if($relatedProduct->images && is_array($relatedProduct->images) && count($relatedProduct->images) > 0)
                                    <img src="{{ asset('storage/' . $relatedProduct->images[0]) }}" class="product-image" alt="{{ $relatedProduct->name }}">
                            @else
                                <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="product-card-body">
                                <h6 class="card-title">{{ Str::limit($relatedProduct->name, 40) }}</h6>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="product-price">{{ \App\Models\Setting::formatPrice($relatedProduct->selling_price) }}</span>
                                    <a href="{{ route('products.show', $relatedProduct->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

