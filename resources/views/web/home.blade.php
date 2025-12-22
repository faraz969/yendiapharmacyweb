@extends('web.layouts.app')

@section('title', 'Home')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<!-- Hero Banner Section -->
@if($banners->count() > 0)
    @php
        $firstBanner = $banners->first();
        $bannerImage = $firstBanner && $firstBanner->image ? Storage::url($firstBanner->image) : null;
    @endphp
    <section class="hero-banner-section mb-4" style="background: linear-gradient(135deg, rgba(21, 141, 67, 0.1) 0%, rgba(21, 141, 67, 0.05) 100%), url('{{ $bannerImage }}') center center/cover no-repeat; padding: 80px 0; position: relative; overflow: hidden; min-height: 450px;">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-3 fw-bold mb-3" style="color: #158d43; line-height: 1.2; text-shadow: 1px 1px 2px rgba(255,255,255,0.8);">
                        {{ $firstBanner->title ?? 'Fresh & Healthy' }}<br>
                        <span style="color: #ee7d09;">Big Discounts</span>
                    </h1>
                    <p class="lead mb-4" style="color: #333; font-weight: 500; text-shadow: 1px 1px 2px rgba(255,255,255,0.8);">
                        {{ $firstBanner->description ?? 'Save up to 50% off on your first order' }}
                    </p>
                    <form class="d-flex mb-3" style="max-width: 400px;">
                        <input type="email" class="form-control rounded-0 rounded-start" placeholder="Your email address" required style="background: rgba(255,255,255,0.95);">
                        <button type="submit" class="btn rounded-0 rounded-end px-4" style="background: #158d43; border-color: #158d43; color: white;">Subscribe</button>
                    </form>
                </div>
            </div>
            @if($banners->count() > 1)
                <div class="text-center mt-4">
                    @foreach($banners as $index => $banner)
                        <span class="banner-dot {{ $index === 0 ? 'active' : '' }}" data-bs-target="#bannerCarousel" data-bs-slide-to="{{ $index }}" style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: {{ $index === 0 ? '#158d43' : 'rgba(21, 141, 67, 0.3)' }}; margin: 0 5px; cursor: pointer; transition: all 0.3s;"></span>
                    @endforeach
                </div>
            @endif
        </div>
        <!-- Overlay for better text readability -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, rgba(21, 141, 67, 0.1) 0%, rgba(21, 141, 67, 0.05) 50%, transparent 100%); z-index: 1;"></div>
    </section>
@else
    <!-- Default Hero Banner -->
    <section class="hero-banner-section mb-4" style="background: linear-gradient(135deg, rgba(21, 141, 67, 0.1) 0%, rgba(21, 141, 67, 0.05) 100%); padding: 80px 0; position: relative; overflow: hidden; min-height: 450px;">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-3 fw-bold mb-3" style="color: #158d43; line-height: 1.2;">
                        Your Health<br>
                        <span style="color: #ee7d09;">Our Priority</span>
                    </h1>
                    <p class="lead mb-4" style="color: #666;">
                        Save up to 50% off on your first order. Get all your prescription and over-the-counter medications delivered to your doorstep.
                    </p>
                    <form class="d-flex mb-3" style="max-width: 400px;">
                        <input type="email" class="form-control rounded-0 rounded-start" placeholder="Your email address" required>
                        <button type="submit" class="btn rounded-0 rounded-end px-4" style="background: #158d43; border-color: #158d43; color: white;">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endif

<div class="container">
    <!-- Featured Categories Section -->
    @if($featuredCategories->count() > 0)
        <section class="featured-categories-section mb-5 py-4">
            <div class="container">
                <!-- Section Title -->
                <h2 class="mb-4 fw-bold" style="color: #1a237e; font-size: 2rem;">Featured Categories</h2>
                
                <!-- Category Filter Tabs -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="category-filters d-flex gap-3 flex-wrap">
                        <button class="btn btn-sm px-3 py-2 category-filter-btn active" data-category="all" style="border: none; background: transparent; color: #666; font-weight: 500; border-bottom: 2px solid transparent;">
                            All Categories
                        </button>
                        @foreach($featuredCategories->take(4) as $filterCategory)
                            <button class="btn btn-sm px-3 py-2 category-filter-btn" data-category="{{ $filterCategory->id }}" style="border: none; background: transparent; color: #666; font-weight: 500; border-bottom: 2px solid transparent;">
                                {{ $filterCategory->name }}
                            </button>
                        @endforeach
                    </div>
                    <!-- Navigation Arrows -->
                    <div class="category-nav-arrows d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary rounded-circle category-scroll-btn" id="categoryScrollLeft" style="width: 40px; height: 40px; border: 1px solid #ddd;">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary rounded-circle category-scroll-btn" id="categoryScrollRight" style="width: 40px; height: 40px; border: 1px solid #ddd;">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Category Cards Container -->
                <div class="category-cards-wrapper" style="position: relative; overflow: hidden;">
                    <div class="category-cards-container d-flex gap-3" id="categoryCardsContainer" style="overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                        @foreach($featuredCategories as $index => $category)
                            @php
                                $colors = ['#e8f5e9', '#fff9c4', '#e8f5e9', '#fce4ec', '#fff9c4', '#e8f5e9', '#fce4ec', '#fff9c4', '#e8f5e9', '#fce4ec'];
                                $bgColor = $colors[$index % count($colors)];
                            @endphp
                            <a href="{{ route('products.category', $category->id) }}" class="text-decoration-none category-card-link" style="flex: 0 0 auto; width: 200px;">
                                <div class="category-card h-100" style="background: {{ $bgColor }}; border-radius: 15px; padding: 20px; text-align: center; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer; height: 100%;">
                                    <div class="category-image-wrapper mb-3" style="height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        @if($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        @else
                                            <i class="fas fa-folder" style="font-size: 4rem; color: #158d43; opacity: 0.5;"></i>
                                        @endif
                                    </div>
                                    <h5 class="category-name mb-2 fw-bold" style="color: #333; font-size: 1rem; margin-bottom: 8px;">{{ $category->name }}</h5>
                                    <p class="category-count mb-0 text-muted" style="font-size: 0.875rem; color: #666;">
                                        {{ $category->products_count ?? 0 }} items
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        
        <style>
            .category-cards-container::-webkit-scrollbar {
                display: none;
            }
            
            .category-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            }
            
            .category-filter-btn.active {
                color: #158d43 !important;
                border-bottom-color: #158d43 !important;
                font-weight: 600 !important;
            }
            
            .category-filter-btn:hover {
                color: #158d43 !important;
            }
        </style>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('categoryCardsContainer');
                const scrollLeftBtn = document.getElementById('categoryScrollLeft');
                const scrollRightBtn = document.getElementById('categoryScrollRight');
                
                if (container && scrollLeftBtn && scrollRightBtn) {
                    scrollLeftBtn.addEventListener('click', function() {
                        container.scrollBy({ left: -220, behavior: 'smooth' });
                    });
                    
                    scrollRightBtn.addEventListener('click', function() {
                        container.scrollBy({ left: 220, behavior: 'smooth' });
                    });
                    
                    // Filter buttons
                    document.querySelectorAll('.category-filter-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.querySelectorAll('.category-filter-btn').forEach(b => b.classList.remove('active'));
                            this.classList.add('active');
                            // You can add filtering logic here if needed
                        });
                    });
                }
            });
        </script>
    @endif

    <!-- Popular Products Section -->
    @if($featuredProducts->count() > 0)
        <section class="popular-products-section mb-5 py-4">
            <div class="container">
                <!-- Section Title -->
                <h2 class="mb-4 fw-bold" style="color: #1a237e; font-size: 2rem;">Popular Products</h2>
                
                <!-- Category Filter Tabs -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="product-filters d-flex gap-3 flex-wrap">
                        <button class="btn btn-sm px-3 py-2 product-filter-btn active" data-category="all" style="border: none; background: transparent; color: #666; font-weight: 500; border-bottom: 2px solid transparent;">
                            All
                        </button>
                        @foreach($featuredCategories->take(6) as $filterCategory)
                            <button class="btn btn-sm px-3 py-2 product-filter-btn" data-category="{{ $filterCategory->id }}" style="border: none; background: transparent; color: #666; font-weight: 500; border-bottom: 2px solid transparent;">
                                {{ $filterCategory->name }}
                            </button>
                        @endforeach
                    </div>
                    <!-- Navigation Arrows -->
                    <div class="product-nav-arrows d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary rounded-circle product-scroll-btn" id="productScrollLeft" style="width: 40px; height: 40px; border: 1px solid #ddd;">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary rounded-circle product-scroll-btn" id="productScrollRight" style="width: 40px; height: 40px; border: 1px solid #ddd;">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Product Cards Container -->
                <div class="product-cards-wrapper" style="position: relative; overflow: hidden;">
                    <div class="product-cards-container d-flex gap-3" id="productCardsContainer" style="overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                        @foreach($featuredProducts as $index => $product)
                            @php
                                // Calculate discount if cost_price is higher than selling_price
                                $hasDiscount = $product->cost_price > $product->selling_price;
                                $discountPercent = $hasDiscount ? round((($product->cost_price - $product->selling_price) / $product->cost_price) * 100) : 0;
                                
                                // Determine badge label
                                $badgeLabel = null;
                                $badgeColor = null;
                                if ($index % 5 == 0) {
                                    $badgeLabel = 'Hot';
                                    $badgeColor = '#ff6b9d';
                                } elseif ($index % 5 == 1) {
                                    $badgeLabel = 'Sale';
                                    $badgeColor = '#4285f4';
                                } elseif ($index % 5 == 2) {
                                    $badgeLabel = 'New';
                                    $badgeColor = '#158d43';
                                } elseif ($hasDiscount && $discountPercent > 0) {
                                    $badgeLabel = '-' . $discountPercent . '%';
                                    $badgeColor = '#ee7d09';
                                }
                            @endphp
                            <div class="product-card-wrapper" style="flex: 0 0 auto; width: 280px;">
                                <div class="card product-card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s;">
                                    <!-- Product Image with Badge -->
                                    <div class="position-relative" style="height: 220px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        @if($product->images && is_array($product->images) && count($product->images) > 0)
                                            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain; padding: 15px;">
                                        @else
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                        @endif
                                        @if($badgeLabel)
                                            <span class="position-absolute top-0 start-0 m-2 px-2 py-1 text-white fw-bold" style="background: {{ $badgeColor }}; border-radius: 5px; font-size: 0.75rem;">
                                                {{ $badgeLabel }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Card Body -->
                                    <div class="card-body p-3">
                                        <!-- Category -->
                                        <p class="text-muted small mb-1" style="font-size: 0.75rem; color: #999;">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                        
                                        <!-- Product Name -->
                                        <h6 class="card-title mb-2 fw-bold" style="font-size: 0.95rem; color: #333; line-height: 1.3; min-height: 38px;">
                                            <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark">
                                                {{ Str::limit($product->name, 60) }}
                                            </a>
                                        </h6>
                                        
                                        <!-- Rating -->
                                        <div class="mb-2">
                                            @php
                                                $rating = 4.0; // Default rating, you can add this field to products table later
                                            @endphp
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($rating))
                                                    <i class="fas fa-star text-warning" style="font-size: 0.75rem;"></i>
                                                @elseif($i - 0.5 <= $rating)
                                                    <i class="fas fa-star-half-alt text-warning" style="font-size: 0.75rem;"></i>
                                                @else
                                                    <i class="far fa-star text-warning" style="font-size: 0.75rem;"></i>
                                                @endif
                                            @endfor
                                            <span class="text-muted small ms-1" style="font-size: 0.7rem;">({{ $rating }})</span>
                                        </div>
                                        
                                        <!-- Brand -->
                                        <p class="text-muted small mb-2" style="font-size: 0.75rem; color: #999;">
                                            By <span class="fw-semibold">YENDIA Pharmacy</span>
                                        </p>
                                        
                                        <!-- Price and Add Button -->
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div>
                                                <span class="fw-bold" style="font-size: 1.1rem; color: #158d43;">
                                                    ${{ number_format($product->selling_price, 2) }}
                                                </span>
                                                @if($hasDiscount && $product->cost_price > $product->selling_price)
                                                    <span class="text-muted text-decoration-line-through ms-2" style="font-size: 0.85rem;">
                                                        ${{ number_format($product->cost_price, 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-sm px-3" style="border-radius: 20px; background: #158d43; border-color: #158d43; color: white;">
                                                    <i class="fas fa-shopping-cart me-1"></i>Add
                                                </button>
                                            </form>
                                        </div>
                                        
                                        @if($product->requires_prescription)
                                            <div class="mt-2">
                                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                                    <i class="fas fa-prescription me-1"></i>Rx Required
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        
        <style>
            .product-cards-container::-webkit-scrollbar {
                display: none;
            }
            
            .product-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
            }
            
            .product-filter-btn.active {
                color: #158d43 !important;
                border-bottom-color: #158d43 !important;
                font-weight: 600 !important;
            }
            
            .product-filter-btn:hover {
                color: #158d43 !important;
            }
        </style>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const productContainer = document.getElementById('productCardsContainer');
                const productScrollLeftBtn = document.getElementById('productScrollLeft');
                const productScrollRightBtn = document.getElementById('productScrollRight');
                
                if (productContainer && productScrollLeftBtn && productScrollRightBtn) {
                    productScrollLeftBtn.addEventListener('click', function() {
                        productContainer.scrollBy({ left: -300, behavior: 'smooth' });
                    });
                    
                    productScrollRightBtn.addEventListener('click', function() {
                        productContainer.scrollBy({ left: 300, behavior: 'smooth' });
                    });
                    
                    // Filter buttons
                    document.querySelectorAll('.product-filter-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.querySelectorAll('.product-filter-btn').forEach(b => b.classList.remove('active'));
                            this.classList.add('active');
                            const categoryId = this.getAttribute('data-category');
                            // You can add filtering logic here if needed
                        });
                    });
                }
            });
        </script>
    @endif

    <!-- Features -->
    <section class="mb-5">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="p-4">
                    <i class="fas fa-shipping-fast fa-3x mb-3" style="color: #158d43;"></i>
                    <h5>Fast Delivery</h5>
                    <p class="text-muted">Quick and reliable delivery to your doorstep</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4">
                    <i class="fas fa-shield-alt fa-3x mb-3" style="color: #158d43;"></i>
                    <h5>Authentic Products</h5>
                    <p class="text-muted">100% genuine medications from licensed pharmacies</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4">
                    <i class="fas fa-user-md fa-3x mb-3" style="color: #ee7d09;"></i>
                    <h5>Expert Consultation</h5>
                    <p class="text-muted">Get advice from qualified pharmacists</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

