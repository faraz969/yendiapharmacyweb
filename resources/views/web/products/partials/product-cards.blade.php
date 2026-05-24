@foreach($products as $product)
    <div class="col-md-4">
        <div class="card product-card h-100">
            <div class="position-relative">
                @include('web.partials.product-image', ['product' => $product, 'class' => 'product-image'])
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
