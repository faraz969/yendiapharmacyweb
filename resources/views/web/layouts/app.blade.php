<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pharmacy E-Commerce') - Your Trusted Pharmacy</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #158d43;
            --secondary-color: #ee7d09;
            --green-color: #158d43;
            --orange-color: #ee7d09;
            --danger-color: #ef4444;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background-color: #ffffff;
        }
        
        /* Top Utility Bar */
        .top-utility-bar {
            font-size: 0.875rem;
        }
        
        .top-utility-bar a:hover {
            color: var(--primary-color) !important;
        }
        
        /* Main Header */
        .main-header {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .main-header .form-select,
        .main-header .form-control {
            border: 1px solid #ddd;
        }
        
        .main-header .btn-success {
            background: var(--green-color);
            border-color: var(--green-color);
        }
        
        .main-header .btn-success:hover {
            background: #0f6b32;
            border-color: #0f6b32;
        }
        
        /* Main Navigation */
        .main-navbar {
            background: var(--green-color) !important;
        }
        
        .main-navbar .nav-link {
            color: white !important;
            padding: 1rem 1.5rem !important;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .main-navbar .nav-link:hover {
            background: rgba(0,0,0,0.1);
        }
        
        .main-navbar .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Badge Styles */
        .badge {
            font-size: 0.65rem;
            padding: 0.25em 0.5em;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        
        /* Product Cards */
        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #f3f4f6;
        }
        
        .product-card-body {
            padding: 1.5rem;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--green-color);
        }
        
        .btn-primary {
            background: var(--green-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #0f6b32;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(21, 141, 67, 0.4);
        }
        
        .btn-success {
            background: var(--green-color);
            border: none;
        }
        
        .btn-success:hover {
            background: #0f6b32;
        }
        
        /* Category Cards */
        .category-card {
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            height: 200px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .category-card:hover {
            transform: scale(1.05);
        }
        
        .category-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .category-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            padding: 1.5rem;
        }
        
        /* Footer */
        .footer {
            background: var(--green-color);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }
        
        /* Cart */
        .cart-item {
            border-bottom: 1px solid #e5e7eb;
            padding: 1.5rem 0;
        }
        
        /* Badges */
        .badge-prescription {
            background: var(--orange-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        
        /* Search */
        .search-box {
            position: relative;
        }
        
        .search-box input {
            border-radius: 25px;
            padding: 0.75rem 3rem 0.75rem 1.5rem;
            border: 2px solid #e5e7eb;
        }
        
        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: var(--green-color);
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }
        
        .search-box button:hover {
            background: #0f6b32;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Utility Bar -->
    <div class="top-utility-bar bg-light border-bottom py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <ul class="list-inline mb-0 small">
                        @php
                            $aboutPage = \App\Models\Page::where('slug', 'about-us')->where('is_active', true)->first();
                        @endphp
                        @if($aboutPage)
                            <li class="list-inline-item"><a href="{{ route('pages.show', $aboutPage->slug) }}" class="text-decoration-none text-muted">About Us</a></li>
                        @else
                            <li class="list-inline-item"><a href="{{ route('home') }}#about" class="text-decoration-none text-muted">About Us</a></li>
                        @endif
                        <li class="list-inline-item"><span class="text-muted">|</span></li>
                        @auth
                            @if(Auth::user()->isBranchStaff())
                                <li class="list-inline-item"><a href="{{ route('branch.dashboard') }}" class="text-decoration-none text-muted">Branch Dashboard</a></li>
                            @else
                                <li class="list-inline-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none text-muted">My Account</a></li>
                            @endif
                        @else
                            <li class="list-inline-item"><a href="{{ route('login') }}" class="text-decoration-none text-muted">My Account</a></li>
                        @endauth
                        <li class="list-inline-item"><span class="text-muted">|</span></li>
                       
                        <li class="list-inline-item"><span class="text-muted">|</span></li>
                        <li class="list-inline-item"><a href="{{ route('order.tracking.index') }}" class="text-decoration-none text-muted">Order Tracking</a></li>
                    </ul>
                </div>
                <div class="col-md-4 text-center">
                    <p class="mb-0 small text-muted">Super Value Deals - Save more with coupons</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="small text-muted me-3">Need help? Call Us: <strong>+1 800 900</strong></span>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header bg-white border-bottom py-3">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-md-2">
                    <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                        <img src="{{ \App\Models\Setting::getHeaderLogo() }}" alt="YENDIA Pharmacy" style="height: 50px; width: auto; margin-right: 10px;">
                       
                    </a>
                </div>
                
                <!-- Search Bar -->
                <div class="col-md-6">
                    <form action="{{ route('products.index') }}" method="GET" class="d-flex">
                        <select class="form-select rounded-0 rounded-start" name="category" style="max-width: 150px;">
                            <option value="">All Categories</option>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <input type="text" name="search" class="form-control rounded-0" placeholder="Search for items..." value="{{ request('search') }}">
                        <button type="submit" class="btn rounded-0 rounded-end" style="background: var(--green-color); border-color: var(--green-color); color: white;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- User Actions -->
                <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-end">
                       
                        <a href="{{ route('cart.index') }}" class="text-decoration-none text-dark me-4 position-relative">
                            <i class="fas fa-shopping-cart fs-5"></i>
                            @php
                                $cartCount = count(session()->get('cart', []));
                            @endphp
                            @if($cartCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">{{ $cartCount }}</span>
                            @endif
                        </a>
                        @auth
                            <div class="dropdown me-4">
                                <a href="#" class="text-decoration-none text-dark dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle fs-5"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li>
                                        <h6 class="dropdown-header">{{ Auth::user()->name }}</h6>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if(Auth::user()->isBranchStaff())
                                        <li>
                                            <a class="dropdown-item" href="{{ route('branch.dashboard') }}">
                                                <i class="fas fa-building me-2"></i>Branch Dashboard
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                                <i class="fas fa-tachometer-alt me-2"></i>My Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.orders.index') }}">
                                                <i class="fas fa-shopping-bag me-2"></i>My Orders
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.addresses.index') }}">
                                                <i class="fas fa-map-marker-alt me-2"></i>My Addresses
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.profile.index') }}">
                                                <i class="fas fa-user me-2"></i>My Profile
                                            </a>
                                        </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-decoration-none text-dark me-4">
                                <i class="fas fa-user-circle fs-5"></i>
                            </a>
                        @endauth
                        <div class="ms-3">
                            <i class="fas fa-phone me-2" style="color: var(--green-color);"></i>
                            <div class="d-inline-block">
                                <div class="fw-bold small" style="color: var(--green-color);">1900 - 888</div>
                                <div class="small text-muted">24/7 Support Center</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Navigation Bar -->
    <nav class="main-navbar bg-success">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <button class="btn w-100 text-start py-3 fw-bold text-white" style="background: rgba(0,0,0,0.1);">
                        <i class="fas fa-bars me-2"></i>Browse All Categories
                    </button>
                </div>
                <div class="col-md-9">
                    <ul class="nav mb-0">
                        <li class="nav-item"><a href="#" class="nav-link text-white">Deals</a></li>
                        <li class="nav-item"><a href="{{ route('home') }}" class="nav-link text-white">Home</a></li>
                        @php
                            $aboutPage = \App\Models\Page::where('slug', 'about-us')->where('is_active', true)->first();
                        @endphp
                        @if($aboutPage)
                            <li class="nav-item"><a href="{{ route('pages.show', $aboutPage->slug) }}" class="nav-link text-white">About</a></li>
                        @else
                            <li class="nav-item"><a href="{{ route('home') }}#about" class="nav-link text-white">About</a></li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Shop <i class="fas fa-chevron-down small"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('products.index') }}">All Products</a></li>
                                @if(isset($navbarCategories) && $navbarCategories->count() > 0)
                                    @foreach($navbarCategories as $category)
                                        <li><a class="dropdown-item" href="{{ route('products.index', ['category' => $category->id]) }}">{{ $category->name }}</a></li>
                                    @endforeach
                                @else
                                    <li><a class="dropdown-item" href="{{ route('products.index') }}?prescription=not_required">OTC Products</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="nav-item"><a href="{{ route('home') }}#contact" class="nav-link text-white">Contact</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ \App\Models\Setting::getFooterLogo() }}" alt="YENDIA Pharmacy" style="height: 40px; width: auto; margin-right: 10px;">
                    </div>
                    <p class="text-white-50">Your trusted online pharmacy for all your healthcare needs.</p>
                    @php
                        $appStoreUrl = \App\Models\Setting::getAppStoreUrl();
                        $playStoreUrl = \App\Models\Setting::getPlayStoreUrl();
                    @endphp
                    @if($appStoreUrl || $playStoreUrl)
                        <div class="mt-3 d-flex gap-2">
                            @if($appStoreUrl)
                                <a href="{{ $appStoreUrl }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                    <img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83&releaseDate=1289433600" alt="Download on the App Store" style="height: 40px; width: auto;">
                                </a>
                            @endif
                            @if($playStoreUrl)
                                <a href="{{ $playStoreUrl }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                    <img src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png" alt="Get it on Google Play" style="height: 60px; width: auto;">
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-white-50">Products</a></li>
                        <li><a href="{{ route('cart.index') }}" class="text-white-50">Cart</a></li>
                        @php
                            $aboutPage = \App\Models\Page::where('slug', 'about-us')->where('is_active', true)->first();
                            $privacyPage = \App\Models\Page::where('slug', 'privacy-policy')->where('is_active', true)->first();
                        @endphp
                        @if($aboutPage)
                            <li><a href="{{ route('pages.show', $aboutPage->slug) }}" class="text-white-50">About Us</a></li>
                        @endif
                        @if($privacyPage)
                            <li><a href="{{ route('pages.show', $privacyPage->slug) }}" class="text-white-50">Privacy Policy</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <p class="text-muted">
                        <i class="fas fa-phone me-2"></i>+1 234 567 8900<br>
                        <i class="fas fa-envelope me-2"></i>info@pharmacystore.com
                    </p>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center text-white-50">
                <p>&copy; {{ \App\Models\Setting::getCopyrightYear() }} YENDIA Pharmacy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>

