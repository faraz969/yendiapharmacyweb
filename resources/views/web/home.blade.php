@extends('web.layouts.app')

@section('title', 'Home')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<!-- Hero Banner Section -->
@if($banners->count() > 0)
    <section class="hero-banner-section mb-4" style="margin-top:20px; border-radius:30px; margin-left:8px; margin-right:8px; overflow: hidden; position: relative; min-height: 450px;">
        <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                @foreach($banners as $index => $banner)
                    @php
                        $bannerImage = $banner && $banner->image ? Storage::url($banner->image) : null;
                    @endphp
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" style="background: linear-gradient(135deg, rgba(21, 141, 67, 0.1) 0%, rgba(21, 141, 67, 0.05) 100%), url('{{ $bannerImage }}') center center/cover no-repeat; padding: 80px 0; position: relative; min-height: 450px;">
                        <div class="container position-relative" style="z-index: 2;">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h1 class="display-3 fw-bold mb-3" style="color: #158d43; line-height: 1.2; text-shadow: 1px 1px 2px rgba(255,255,255,0.8);">
                                        {{ $banner->title ?? 'Fresh & Healthy' }}<br>
                                    </h1>
                                    <p class="lead mb-4" style="color: #333; font-weight: 500; text-shadow: 1px 1px 2px rgba(255,255,255,0.8);">
                                        {{ $banner->description ?? 'Save up to 50% off on your first order' }}
                                    </p>
                                    <form class="subscribe-form mb-3" style="max-width: 450px;">
                                        <div class="subscribe-input-group" style="display: flex; background: white; border-radius: 50px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                            <div class="input-wrapper" style="flex: 1; display: flex; align-items: center; padding: 12px 20px;">
                                                <i class="fas fa-paper-plane me-2" style="color: #999; font-size: 0.9rem;"></i>
                                                <input type="email" class="form-control border-0 shadow-none" placeholder="Your email address" required style="padding: 0; background: transparent; color: #333; font-size: 0.95rem;">
                                            </div>
                                            <button type="submit" class="btn border-0 px-4" style="background: #158d43; color: white; font-weight: 500; border-radius: 50px 50px 50px 50px; white-space: nowrap;">Subscribe</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Overlay for better text readability -->
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, rgba(21, 141, 67, 0.1) 0%, rgba(21, 141, 67, 0.05) 50%, transparent 100%); z-index: 1;"></div>
                    </div>
                @endforeach
            </div>
            @if($banners->count() > 1)
                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev" style="width: 5%;">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next" style="width: 5%;">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                <!-- Carousel Indicators -->
                <div class="carousel-indicators" style="bottom: 20px;">
                    @foreach($banners as $index => $banner)
                        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
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
                    <form class="subscribe-form mb-3" style="max-width: 450px;">
                        <div class="subscribe-input-group" style="display: flex; background: white; border-radius: 50px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <div class="input-wrapper" style="flex: 1; display: flex; align-items: center; padding: 12px 20px;">
                                <i class="fas fa-paper-plane me-2" style="color: #999; font-size: 0.9rem;"></i>
                                <input type="email" class="form-control border-0 shadow-none" placeholder="Your email address" required style="padding: 0; background: transparent; color: #333; font-size: 0.95rem;">
                            </div>
                            <button type="submit" class="btn border-0 px-4" style="background: #158d43; color: white; font-weight: 500; border-radius: 0 50px 50px 0; white-space: nowrap;">Subscribe</button>
                        </div>
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
                
                
                <!-- Category Icons Container -->
                <div class="category-icons-wrapper" style="position: relative; overflow: visible; padding: 20px 0;">
                    <div class="category-icons-container d-flex gap-4 justify-content-center flex-wrap" id="categoryIconsContainer">
                        @foreach($featuredCategories as $index => $category)
                            <a href="{{ route('products.category', $category->id) }}" class="text-decoration-none category-icon-link" style="flex: 0 0 auto; text-align: center; transition: transform 0.3s; padding: 10px;">
                                <div class="category-icon-wrapper" style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                                    <!-- Round Icon -->
                                    <div class="category-icon-circle" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; border: 3px solid #a5d6a7;">
                                        @if($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="width: 70px; height: 70px; object-fit: contain; border-radius: 50%;">
                                        @else
                                            <i class="fas fa-folder" style="font-size: 2.5rem; color: #66bb6a;"></i>
                                        @endif
                                    </div>
                                    <!-- Category Name -->
                                    <h6 class="category-name mb-0 fw-semibold" style="color: #333; font-size: 0.9rem; max-width: 100px; text-align: center; line-height: 1.2;">
                                        {{ $category->name }}
                                    </h6>
                                    <!-- Item Count -->
                                    <p class="category-count mb-0 text-muted" style="font-size: 0.8rem; color: #666;">
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
            .category-icon-link:hover {
                text-decoration: none;
            }
            
            .category-icon-link:hover .category-icon-circle {
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(21, 141, 67, 0.3);
                z-index: 10;
                position: relative;
            }
            
            .category-icon-link:hover .category-name {
                color: #158d43;
            }
            
            .category-icon-wrapper {
                position: relative;
            }
        </style>
    @endif

    <!-- Marketing Banners Section -->
    @if(isset($marketingBanners) && $marketingBanners->count() > 0)
        <section class="marketing-banners-section mb-5 py-4">
            <div class="container">
                <div class="row g-4 justify-content-center">
                    @foreach($marketingBanners->take(3) as $marketingBanner)
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="marketing-banner-card" style="background-color: {{ $marketingBanner->background_color ?? '#f5f5f5' }}; border-radius: 15px; overflow: hidden; padding: 25px 20px; position: relative; height: 200px; display: flex; align-items: center; transition: transform 0.3s, box-shadow 0.3s;">
                                <div class="row align-items-center w-100 g-0">
                                    <!-- Left Side: Text Content -->
                                    <div class="col-6">
                                        <h3 class="mb-2 fw-bold" style="color: #1f2937; font-size: 1rem; line-height: 1.3;">
                                            {{ $marketingBanner->title }}
                                        </h3>
                                        @if($marketingBanner->description)
                                            <p class="mb-3 text-muted" style="font-size: 0.85rem; line-height: 1.5;">
                                                {{ Str::limit($marketingBanner->description, 50) }}
                                            </p>
                                        @endif
                                        <a href="{{ $marketingBanner->link ?? '#' }}" class="btn text-white fw-semibold d-inline-flex align-items-center" style="background-color: #158d43; border: none; border-radius: 8px; padding: 8px 16px; font-size: 0.9rem; transition: all 0.3s; white-space: nowrap;">
                                            <span style="white-space: nowrap;">{{ $marketingBanner->button_text ?? 'Shop Now' }}</span>
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                    <!-- Right Side: Product Image -->
                                    <div class="col-6 text-center">
                                        @if($marketingBanner->image)
                                            <img src="{{ asset('storage/' . $marketingBanner->image) }}" alt="{{ $marketingBanner->title }}" style="max-width: 100%; max-height: 180px; object-fit: contain;">
                                        @else
                                            <div style="width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; color: #999;">
                                                <i class="fas fa-image fa-3x"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        
        <style>
            .marketing-banner-card {
                width: 100%;
            }
            
            .marketing-banner-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
            }
            
            .marketing-banner-card .btn {
                white-space: nowrap !important;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .marketing-banner-card .btn:hover {
                background-color: #0f6b32 !important;
                transform: translateX(5px);
            }
        </style>
    @endif

    <!-- Popular Products Section -->
    @if($featuredProducts->count() > 0)
        <section class="popular-products-section mb-5 py-4">
            <div class="container">
                <!-- Section Title -->
                
                
                <!-- Category Filter Tabs -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="product-filters d-flex gap-3 flex-wrap">
                        <button class="btn btn-sm px-3 py-2 product-filter-btn active" data-category="all" style="border: none; background: transparent; color: #666; font-weight: 500; border-bottom: 2px solid transparent;">
                            All
                        </button>
                        @foreach($featuredCategories->take(9) as $filterCategory)
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
                                // Determine badge label - only show discount badge
                                $badgeLabel = null;
                                $badgeColor = null;
                                if ($product->discount && $product->discount > 0) {
                                    $badgeLabel = \App\Models\Setting::formatPrice($product->discount) . ' OFF';
                                    $badgeColor = '#ee7d09';
                                }
                            @endphp
                            <div class="product-card-wrapper" style="flex: 0 0 auto; width: 280px; height: 100%;" data-category-id="{{ $product->category_id }}">
                                <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none" style="color: inherit; display: flex; height: 100%;">
                                    <div class="card product-card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer; display: flex; flex-direction: column; width: 100%;">
                                        <!-- Product Image with Badge -->
                                        <div class="position-relative" style="height: 220px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
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
                                        <div class="card-body p-3" style="display: flex; flex-direction: column; flex-grow: 1;">
                                            <!-- Category -->
                                            <p class="text-muted small mb-1" style="font-size: 0.75rem; color: #999;">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                            
                                            <!-- Product Name -->
                                            <h6 class="card-title mb-2 fw-bold" style="font-size: 0.95rem; color: #333; line-height: 1.3; min-height: 38px;">
                                                {{ Str::limit($product->name, 60) }}
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
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <span class="fw-bold" style="font-size: 1.1rem; color: #158d43;">
                                                {{ \App\Models\Setting::formatPrice($product->selling_price) }}
                                            </span>
                                            <form action="{{ route('cart.add') }}" method="POST" class="d-inline" onclick="event.stopPropagation();">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-sm px-3" style="border-radius: 20px; background: #158d43; border-color: #158d43; color: white;">
                                                    <i class="fas fa-shopping-cart me-1"></i>Add
                                                </button>
                                            </form>
                                        </div>
                                        
                                        @if($product->requires_prescription)
                                            <div class="mt-2" style="margin-top: auto !important;">
                                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                                    <i class="fas fa-prescription me-1"></i>Rx Required
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                </a>
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
            
            .product-card-wrapper {
                height: 100%;
            }
            
            .product-card-wrapper > a {
                display: flex;
                height: 100%;
                text-decoration: none;
                color: inherit;
            }
            
            .product-card-wrapper > a:hover {
                text-decoration: none;
                color: inherit;
            }
            
            .product-card-wrapper .product-card {
                display: flex !important;
                flex-direction: column !important;
                min-height: 480px;
            }
            
            .product-card-wrapper .product-card .card-body {
                display: flex !important;
                flex-direction: column !important;
                flex-grow: 1 !important;
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
                            
                            // Filter products
                            const productCards = document.querySelectorAll('.product-card-wrapper');
                            productCards.forEach(card => {
                                if (categoryId === 'all') {
                                    card.style.display = 'block';
                                } else {
                                    const cardCategoryId = card.getAttribute('data-category-id');
                                    if (cardCategoryId === categoryId) {
                                        card.style.display = 'block';
                                    } else {
                                        card.style.display = 'none';
                                    }
                                }
                            });
                        });
                    });
                }
            });
        </script>
    @endif

    <style>
        /* Banner Carousel Styles */
        .hero-banner-section .carousel-indicators {
            margin-bottom: 20px;
        }
        
        .hero-banner-section .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(21, 141, 67, 0.3);
            border: none;
            margin: 0 5px;
        }
        
        .hero-banner-section .carousel-indicators button.active {
            background-color: #158d43;
        }
        
        .hero-banner-section .carousel-control-prev,
        .hero-banner-section .carousel-control-next {
            opacity: 0.7;
        }
        
        .hero-banner-section .carousel-control-prev:hover,
        .hero-banner-section .carousel-control-next:hover {
            opacity: 1;
        }
        
        /* Subscribe Form Styles */
        .subscribe-input-group:focus-within {
            box-shadow: 0 4px 12px rgba(21, 141, 67, 0.2) !important;
        }
        
        .subscribe-input-group input:focus {
            outline: none;
        }
        
        .subscribe-input-group button:hover {
            background: #0f6b32 !important;
        }
    </style>

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

