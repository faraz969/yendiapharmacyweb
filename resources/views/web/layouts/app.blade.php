<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pharmacy E-Commerce') - Your Trusted Pharmacy</title>
    <link rel="icon" type="image/x-icon" href="{{ \App\Models\Setting::getFavicon() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Geist Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
            font-family: "Inter", "Geist Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
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
        
        /* Mobile Responsive Styles */
        @media (max-width: 767.98px) {
            .top-utility-bar {
                font-size: 0.75rem;
                padding: 0.5rem 0 !important;
            }
            
            .top-utility-bar .row > div {
                margin-bottom: 0.5rem;
            }
            
            .top-utility-bar .text-center,
            .top-utility-bar .text-end {
                text-align: center !important;
            }
            
            .main-header {
                padding: 1rem 0 !important;
            }
            
            .main-header .row {
                margin: 0;
            }
            
            .main-header .col-md-2,
            .main-header .col-md-7,
            .main-header .col-md-3 {
                padding: 0.5rem 0.75rem;
            }
            
            .main-header .navbar-brand img {
                height: 50px !important;
            }
            
            .search-bar-container {
                flex-direction: column !important;
                border-radius: 8px !important;
            }
            
            .search-category-dropdown {
                border-right: none !important;
                border-bottom: 1px solid #e5e7eb !important;
                width: 100% !important;
            }
            
            .search-input-wrapper {
                width: 100% !important;
            }
            
            .search-icon-wrapper {
                border-left: none !important;
                border-top: 1px solid #e5e7eb !important;
                width: 100% !important;
                text-align: center !important;
            }
            
            .main-navbar .col-md-3,
            .main-navbar .col-md-9 {
                padding: 0.5rem 0.75rem;
            }
            
            .main-navbar .col-md-3 {
                border-right: none !important;
                border-bottom: 1px solid #e5e7eb !important;
                margin-bottom: 0.5rem;
            }
            
            .main-navbar .nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .main-navbar .nav-link {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.875rem;
            }
            
            .main-navbar .collapse {
                border-top: 1px solid #e5e7eb;
                margin-top: 0.5rem;
            }
            
            .main-navbar .nav.flex-column .nav-link {
                padding: 0.75rem 1rem !important;
                font-size: 0.95rem;
            }
            
            .main-navbar .nav.flex-column .nav-link:hover {
                background-color: #f9fafb;
            }
            
            .main-navbar .fa-chevron-down {
                transition: transform 0.3s ease;
            }
            
            .main-header .search-bar-form {
                margin: 0.5rem 0 !important;
            }
            
            .main-header .d-flex.gap-2 {
                gap: 0.5rem !important;
            }
            
            .main-header .d-flex.gap-4 {
                gap: 1rem !important;
            }
        }
        
        @media (max-width: 575.98px) {
            .main-header .navbar-brand img {
                height: 40px !important;
            }
            
            .main-header .col-6 {
                padding: 0.25rem 0.5rem;
            }
            
            .search-bar-container {
                border-radius: 6px !important;
            }
            
            .search-category-dropdown,
            .search-input-wrapper,
            .search-icon-wrapper {
                padding: 8px 12px !important;
            }
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
            color: var(--text-dark) !important;
            padding: 1rem 1.5rem !important;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .main-navbar .nav-link:hover {
            color: var(--green-color) !important;
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
        
        /* Pagination Styles */
        .pagination {
            margin: 1.5rem 0;
        }
        
        .pagination .page-link {
            color: var(--green-color);
            border-color: #dee2e6;
            padding: 0.5rem 0.75rem;
        }
        
        .pagination .page-link:hover {
            color: #0f6b32;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--green-color);
            border-color: var(--green-color);
            color: white;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: auto;
            background-color: #fff;
            border-color: #dee2e6;
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
            display: flex;
            flex-direction: column;
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
            flex-shrink: 0;
        }
        
        .product-card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        
        .product-card-body .mt-2,
        .product-card-body .mt-3 {
            margin-top: auto !important;
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
        
        /* New Search Bar Styles */
        .search-bar-form {
            width: 100%;
        }
        
        .search-bar-container:focus-within {
            border-color: #158d43 !important;
            box-shadow: 0 0 0 2px rgba(21, 141, 67, 0.1);
        }
        
        .search-category-dropdown select:focus {
            outline: none;
        }
        
        .search-input-wrapper input:focus {
            outline: none;
        }
        
        .search-icon-wrapper button:hover {
            color: #158d43 !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Utility Bar -->
    <div class="top-utility-bar bg-light border-bottom py-2 d-none d-md-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <ul class="list-inline mb-0 small">
                        @php
                            $aboutPage = \App\Models\Page::where('slug', 'about-us')->where('is_active', true)->first();
                        @endphp
                        
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
                       
                        
                        <li class="list-inline-item"><a href="{{ route('order.tracking.index') }}" class="text-decoration-none text-muted">Order Tracking</a></li>
                    </ul>
                </div>
                <div class="col-md-4 text-center">
                    <p class="mb-0 small text-muted">{{ \App\Models\Setting::getTopbarTagline() }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="small text-muted me-3">Need help? Call Us: <strong>{{ \App\Models\Setting::getContactPhone() }}</strong></span>
                    
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Top Bar -->
    <div class="top-utility-bar bg-light border-bottom py-2 d-block d-md-none">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-2">
                    <span class="small text-muted">Need help? Call: <strong>{{ \App\Models\Setting::getContactPhone() }}</strong></span>
                </div>
                <div class="col-12">
                    <ul class="list-inline mb-0 small text-center">
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
                        <li class="list-inline-item"><a href="{{ route('order.tracking.index') }}" class="text-decoration-none text-muted">Order Tracking</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header bg-white border-bottom py-3">
        <div class="container">
            <div class="row align-items-center g-0">
                <!-- Logo -->
                <div class="col-6 col-md-2 col-lg-2">
                    <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                        <img src="{{ \App\Models\Setting::getHeaderLogo() }}" alt="YENDIA Pharmacy" style="height: 79px; width: auto; margin-right: 10px;" class="img-fluid">
                       
                    </a>
                </div>
                
                <!-- Search Bar -->
                <div class="col-12 col-md-7 col-lg-7 order-3 order-md-2">
                    <form action="{{ route('products.index') }}" method="GET" class="search-bar-form" style="margin: 0 10px;">
                        <div class="search-bar-container" style="display: flex; align-items: center; background: white; border: 1px solid #a5d6a7; border-radius: 8px; padding: 0; overflow: hidden;">
                            <!-- All Categories Dropdown -->
                            <div class="search-category-dropdown" style="position: relative; padding: 10px 15px; border-right: 1px solid #e5e7eb;">
                                <select name="category" class="form-select border-0 shadow-none" style="padding: 0; font-size: 0.95rem; color: #1e3a8a; background: transparent; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"%23999\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 0 center; background-size: 16px; padding-right: 25px;">
                                    <option value="">All Categories</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <!-- Search Input -->
                            <div class="search-input-wrapper" style="flex: 1; position: relative;">
                                <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Search for items..." value="{{ request('search') }}" style="padding: 10px 15px; font-size: 0.95rem; background: transparent;">
                            </div>
                            <!-- Search Icon -->
                            <div class="search-icon-wrapper" style="padding: 10px 15px; border-left: 1px solid #e5e7eb;">
                                <button type="submit" class="btn border-0 shadow-none p-0" style="background: transparent; color: #999;">
                                    <i class="fas fa-search" style="font-size: 1rem;"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- User Actions -->
                <div class="col-6 col-md-3 col-lg-3 order-2 order-md-3">
                    <div class="d-flex align-items-center justify-content-end gap-2 gap-md-4">
                        @auth
                            @php
                                $unreadNotifications = \App\Models\Notification::where('user_id', Auth::id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            <a href="{{ route('user.notifications.index') }}" class="text-decoration-none d-flex align-items-center position-relative" style="color: #4b5563;">
                                <i class="fas fa-bell" style="font-size: 1.25rem; color: #4b5563;"></i>
                                @if($unreadNotifications > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background-color: #158d43; font-size: 0.65rem; padding: 2px 6px; margin-left: -8px; margin-top: -5px;">{{ $unreadNotifications }}</span>
                                @endif
                                <span class="ms-2 d-none d-md-inline" style="color: #4b5563; font-size: 0.95rem; font-weight: 500;">Notifications</span>
                            </a>
                        @endauth
                       
                        <a href="{{ route('cart.index') }}" class="text-decoration-none d-flex align-items-center position-relative" style="color: #4b5563;">
                            <i class="fas fa-shopping-cart" style="font-size: 1.25rem; color: #4b5563;"></i>
                            @php
                                $cartCount = count(session()->get('cart', []));
                            @endphp
                            @if($cartCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background-color: #158d43; font-size: 0.65rem; padding: 2px 6px; margin-left: -8px; margin-top: -5px;">{{ $cartCount }}</span>
                            @endif
                            <span class="ms-2 d-none d-md-inline" style="color: #4b5563; font-size: 0.95rem; font-weight: 500;">Cart</span>
                        </a>
                        @auth
                            <div class="dropdown">
                                <a href="#" class="text-decoration-none d-flex align-items-center dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #4b5563;">
                                    <i class="fas fa-user" style="font-size: 1.25rem; color: #4b5563;"></i>
                                    <span class="ms-2 d-none d-md-inline" style="color: #4b5563; font-size: 0.95rem; font-weight: 500;">Account</span>
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
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.notifications.index') }}">
                                                <i class="fas fa-bell me-2"></i>Notifications
                                                @php
                                                    $unreadCount = \App\Models\Notification::where('user_id', Auth::id())
                                                        ->where('is_read', false)
                                                        ->count();
                                                @endphp
                                                @if($unreadCount > 0)
                                                    <span class="badge bg-danger float-end">{{ $unreadCount }}</span>
                                                @endif
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
                            <a href="{{ route('login') }}" class="text-decoration-none d-flex align-items-center" style="color: #4b5563;">
                                <i class="fas fa-user" style="font-size: 1.25rem; color: #4b5563;"></i>
                                <span class="ms-2 d-none d-md-inline" style="color: #4b5563; font-size: 0.95rem; font-weight: 500;">Account</span>
                            </a>
                        @endauth
                        
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Navigation Bar -->
    <nav class="main-navbar" style="background: white !important; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; padding-top:10px; padding-bottom:10px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-3" style="border-right: 1px solid #e5e7eb;">
                    <div class="dropdown">
                        <button class="btn w-100 text-start py-2 py-md-3 fw-bold text-white dropdown-toggle" type="button" id="browseCategoriesDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: var(--green-color); border: none;">
                            <i class="fas fa-bars me-2"></i>Browse All Categories
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="browseCategoriesDropdown" style="padding-right:5px;padding-left:5px;">
                            <li><a class="dropdown-item" href="{{ route('products.index') }}">All Products</a></li>
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories as $category)
                                    <li style="border-bottom:1px solid #e5e7eb;"><a class="dropdown-item" href="{{ route('products.index', ['category' => $category->id]) }}">{{ $category->name }}</a></li>
                                @endforeach
                            @else
                                <li><a class="dropdown-item" href="{{ route('products.index') }}?prescription=not_required">OTC Products</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                <!-- Desktop Navigation -->
                <div class="col-md-9 d-none d-md-block">
                    <ul class="nav mb-0">
                        <li class="nav-item"><a href="{{ route('home') }}" class="nav-link" style="color: var(--text-dark) !important;">Home</a></li>
                       
                           
                        
                            <li class="nav-item"><a href="https://yendiapharmacy.com/about/" class="nav-link" style="color: var(--text-dark) !important;">About</a></li>
                        
                        
                        @if(isset($navbarCategories) && $navbarCategories->count() > 0)
                            @foreach($navbarCategories as $category)
                                <li class="nav-item">
                                    <a href="{{ route('products.index', ['category' => $category->id]) }}" class="nav-link" style="color: var(--text-dark) !important;">{{ $category->name }}</a>
                                </li>
                            @endforeach
                        @endif
                        <li class="nav-item"><a href="https://yendiapharmacy.com/services/" class="nav-link" style="color: var(--text-dark) !important;">Marketing</a></li>
                        <li class="nav-item"><a href="https://yendiapharmacy.com/why-parter-us/" class="nav-link" style="color: var(--text-dark) !important;">Why Partner Us?</a></li>

                        
                        <li class="nav-item"><a href="https://yendiapharmacy.com/contact/" class="nav-link" style="color: var(--text-dark) !important;">Contact</a></li>
                    </ul>
                </div>
                
                <!-- Mobile Navigation Toggle Button -->
                <div class="col-12 d-md-none mt-2">
                    <button class="btn w-100 text-start fw-bold text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavMenu" aria-expanded="false" aria-controls="mobileNavMenu" style="background: var(--green-color); border: none;">
                        <i class="fas fa-bars me-2"></i>Menu
                        <i class="fas fa-chevron-down float-end mt-1"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Collapsible Navigation Menu -->
            <div class="collapse d-md-none" id="mobileNavMenu">
                <div class="mt-3 pb-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link py-2" style="color: var(--text-dark) !important; border-bottom: 1px solid #e5e7eb;">
                                <i class="fas fa-home me-2"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="https://yendiapharmacy.com/about/" class="nav-link py-2" style="color: var(--text-dark) !important; border-bottom: 1px solid #e5e7eb;">
                                <i class="fas fa-info-circle me-2"></i>About
                            </a>
                        </li>
                        @if(isset($navbarCategories) && $navbarCategories->count() > 0)
                            @foreach($navbarCategories as $category)
                                <li class="nav-item">
                                    <a href="{{ route('products.index', ['category' => $category->id]) }}" class="nav-link py-2" style="color: var(--text-dark) !important; border-bottom: 1px solid #e5e7eb;">
                                        <i class="fas fa-tag me-2"></i>{{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                        <li class="nav-item">
                            <a href="https://yendiapharmacy.com/services/" class="nav-link py-2" style="color: var(--text-dark) !important; border-bottom: 1px solid #e5e7eb;">
                                <i class="fas fa-bullhorn me-2"></i>Marketing
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="https://yendiapharmacy.com/why-parter-us/" class="nav-link py-2" style="color: var(--text-dark) !important; border-bottom: 1px solid #e5e7eb;">
                                <i class="fas fa-handshake me-2"></i>Why Partner Us?
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="https://yendiapharmacy.com/contact/" class="nav-link py-2" style="color: var(--text-dark) !important;">
                                <i class="fas fa-envelope me-2"></i>Contact
                            </a>
                        </li>
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

    <!-- Vendor/Manufacturer Logos Section -->
    @php
        $vendors = \App\Models\Vendor::where('is_active', true)->whereNotNull('logo')->get();
    @endphp
    @if($vendors->count() > 0)
        <section class="vendor-logos-section py-4" style="background: #f8f9fa; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">
            <div class="container">
                <div class="vendor-logos-container" style="overflow-x: auto; overflow-y: hidden; white-space: nowrap; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                    <div class="d-flex align-items-center gap-4" style="display: inline-flex;">
                        @foreach($vendors as $vendor)
                            <div class="vendor-logo-item" style="flex: 0 0 auto; text-align: center; min-width: 150px;">
                                <img src="{{ asset('storage/' . $vendor->logo) }}" alt="{{ $vendor->name }}" style="max-height: 60px; max-width: 150px; object-fit: contain; filter: grayscale(100%); opacity: 0.7; transition: all 0.3s;">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        
        <style>
            .vendor-logos-container::-webkit-scrollbar {
                display: none;
            }
            
            .vendor-logo-item img:hover {
                filter: grayscale(0%) !important;
                opacity: 1 !important;
                transform: scale(1.1);
            }
            
            /* Auto-scroll animation */
            @keyframes scroll {
                0% {
                    transform: translateX(0);
                }
                100% {
                    transform: translateX(-50%);
                }
            }
            
            .vendor-logos-container > div {
                animation: scroll 30s linear infinite;
            }
            
            .vendor-logos-container:hover > div {
                animation-play-state: paused;
            }
        </style>
    @endif

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
                                <a href="{{ $appStoreUrl }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none" style="padding-top:10px; display: inline-block;">
                                    <img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us.svg?style=flat" alt="Download on the App Store" style="height: 40px; width: auto; display: block;" onerror="this.onerror=null; this.src='https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg';">
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
                            <li><a href="https://yendiapharmacy.com/about/" class="text-white-50">About Us</a></li>
                        @endif
                        @if($privacyPage)
                            <li><a href="{{ route('pages.show', $privacyPage->slug) }}" class="text-white-50">Privacy Policy</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <p class="text-muted">
                        <i class="fas fa-phone me-2"></i>{{ \App\Models\Setting::getContactPhone() }}<br>
                        <i class="fas fa-envelope me-2"></i>{{ \App\Models\Setting::getContactEmail() }}
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
    
    <script>
        // Mobile menu toggle chevron rotation
        document.addEventListener('DOMContentLoaded', function() {
            const mobileNavToggle = document.querySelector('[data-bs-target="#mobileNavMenu"]');
            const chevronIcon = mobileNavToggle?.querySelector('.fa-chevron-down');
            
            if (mobileNavToggle && chevronIcon) {
                // Listen to Bootstrap collapse events
                const mobileNavMenu = document.getElementById('mobileNavMenu');
                if (mobileNavMenu) {
                    mobileNavMenu.addEventListener('show.bs.collapse', function() {
                        chevronIcon.style.transform = 'rotate(180deg)';
                    });
                    mobileNavMenu.addEventListener('hide.bs.collapse', function() {
                        chevronIcon.style.transform = 'rotate(0deg)';
                    });
                }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>

