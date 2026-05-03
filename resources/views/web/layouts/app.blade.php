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
        
        /* Top Utility Bar (layout: tagline left, links right — original light colors) */
        .top-utility-bar {
            font-size: 0.8125rem;
        }
        
        .top-bar-premium {
            background: #158d43;
            border-bottom: 1px solid #e5e7eb !important;
            color: white;
        }
        
        .top-bar-premium a {
            color: white;
        }
        
        .top-bar-premium a:hover {
            color: #fff !important;
            opacity: 0.92;
        }
        
        .top-utility-bar.top-bar-premium a:hover {
            color: #fff !important;
            opacity: 0.92;
        }
        
        .top-bar-premium .top-bar-sep {
            color: rgba(255, 255, 255, 0.55);
            margin: 0 0.35rem;
            user-select: none;
        }
        
        .top-utility-bar:not(.top-bar-premium) a:hover {
            color: var(--primary-color) !important;
        }
        
        /* Fixed top: utility bar + main header */
        .site-header-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        }
        
        .site-header-fixed .main-header {
            box-shadow: none;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .site-header-fixed .dropdown-menu {
            z-index: 1055;
        }
        
        .site-header-spacer {
            width: 100%;
            pointer-events: none;
        }
        
        .btn-mobile-nav-toggle {
            color: #1f2937;
            padding: 0.35rem 0.5rem;
            line-height: 1;
            border: none;
            background: transparent;
        }
        
        .btn-mobile-nav-toggle:hover,
        .btn-mobile-nav-toggle:focus {
            color: var(--green-color);
            background: rgba(21, 141, 67, 0.08);
        }
        
        .offcanvas-nav .nav-link {
            color: var(--text-dark);
            padding: 0.65rem 0;
            border-bottom: 1px solid #f3f4f6;
            font-weight: 500;
        }
        
        .offcanvas-nav .nav-link:hover {
            color: var(--green-color);
        }
        
        .offcanvas-nav .offcanvas-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #9ca3af;
            margin: 1rem 0 0.35rem;
            font-weight: 600;
        }
        
        .offcanvas-nav .offcanvas-section-title:first-child {
            margin-top: 0;
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 767.98px) {
            .top-utility-bar {
                font-size: 0.72rem;
                padding: 0.4rem 0 !important;
            }
            
            .top-utility-bar .row > div {
                margin-bottom: 0.25rem;
            }
            
            .top-utility-bar .text-center,
            .top-utility-bar .text-end {
                text-align: center !important;
            }
            
            .main-header {
                padding: 0.5rem 0 0.6rem !important;
            }
            
            .main-header .row {
                margin: 0;
            }
            
            .main-header .navbar-brand img {
                height: 44px !important;
            }
            
            /* Single compact horizontal search row on mobile */
            .header-search-pill {
                border-radius: 10px !important;
            }
            
            .search-bar-container.header-search-pill {
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                align-items: stretch !important;
                padding: 2px 4px 2px 2px !important;
            }
            
            .search-category-dropdown {
                border-right: 1px solid #e5e7eb !important;
                border-bottom: none !important;
                width: auto !important;
                flex: 0 0 34%;
                max-width: 34%;
                min-width: 0;
                
            }
            
            .search-category-dropdown .form-select {
                font-size: 0.72rem !important;
                padding-right: 1.1rem !important;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
            
            .search-input-wrapper {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                width: auto !important;
            }
            
            .search-input-wrapper input {
                padding: 6px 8px !important;
                font-size: 0.85rem !important;
            }
            
            .search-icon-wrapper {
                border-left: none !important;
                border-top: none !important;
                width: auto !important;
                flex: 0 0 auto;
                text-align: center !important;
                padding: 2px 4px 2px 0 !important;
            }
            
            .search-bar-container .btn-search-primary {
                padding: 0.32rem 0.65rem !important;
                font-size: 0.78rem !important;
                border-radius: 8px !important;
            }
            
            .main-header .search-bar-form {
                margin: 0.35rem 0 0 !important;
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
            
            .search-category-dropdown {
                flex: 0 0 32%;
                max-width: 32%;
            }
            
            .search-category-dropdown .form-select {
                font-size: 0.68rem !important;
            }
            
            .search-bar-container .btn-search-primary {
                padding: 0.3rem 0.5rem !important;
                font-size: 0.72rem !important;
            }
        }
        
        /* Main Header */
        .main-header {
            box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        }
        
        .header-search-pill {
            border: 1px solid #a5d6a7 !important;
            border-radius: 999px !important;
            overflow: visible !important;
            background: #fff !important;
        }
        
        .header-search-pill .search-category-dropdown {
            border-right: 1px solid #e5e7eb !important;
        }
        
        .btn-search-primary {
            background: var(--green-color) !important;
            color: #fff !important;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1.35rem !important;
            border-radius: 999px !important;
            border: none !important;
            white-space: nowrap;
            transition: background 0.2s, transform 0.15s;
        }
        
        .btn-search-primary:hover {
            background: #0f6b32 !important;
            color: #fff !important;
        }
        
        .cart-badge-header {
            background-color: var(--green-color) !important;
        }
        
        .browse-categories-btn {
            background: transparent !important;
            color: var(--text-dark) !important;
            border: none !important;
            font-weight: 700 !important;
        }
        
        .browse-categories-btn:hover,
        .browse-categories-btn:focus {
            color: var(--green-color) !important;
            background: rgba(21, 141, 67, 0.08) !important;
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
            background: #fff !important;
        }
        
        .main-navbar .nav-link {
            color: var(--text-dark) !important;
            padding: 0.65rem 1rem !important;
            font-weight: normal;
            transition: color 0.2s;
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
            
            color: black;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }
        .footer a{
            text-decoration:none;
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
            box-shadow: 0 0 0 2px rgba(21, 141, 67, 0.12);
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

        .header-search-suggestions {
            position: absolute;
            left: 0;
            right: 0;
            top: 100%;
            margin-top: 4px;
            background: #fff;
            border: 1px solid #a5d6a7;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            max-height: 320px;
            overflow-y: auto;
            z-index: 1050;
            display: none;
        }

        .header-search-suggestions.is-open {
            display: block;
        }

        .header-search-suggestions button {
            display: block;
            width: 100%;
            text-align: left;
            padding: 10px 14px;
            border: none;
            background: transparent;
            font-size: 0.9rem;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
        }

        .header-search-suggestions button:last-child {
            border-bottom: none;
        }

        .header-search-suggestions button:hover,
        .header-search-suggestions button:focus {
            background: #e8f5e9;
            outline: none;
        }

        .header-search-suggestions .hint {
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #888;
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $contactPhone = \App\Models\Setting::getContactPhone();
        $whatsappDigits = preg_replace('/\D+/', '', $contactPhone ?? '');
    @endphp

    <div id="siteHeaderFixed" class="site-header-fixed">
    <!-- Top Utility Bar -->
    <div class="top-utility-bar top-bar-premium py-2 d-none d-md-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <p class="mb-0 small d-flex align-items-center gap-2">
                        <i class="fas fa-circle-info opacity-75"></i>
                        <span>{{ \App\Models\Setting::getTopbarTagline() }}</span>
                    </p>
                </div>
                <div class="col-md-7 text-md-end">
                    <ul class="list-inline mb-0 small text-md-end">
                        @auth
                            @if(Auth::user()->isBranchStaff())
                                <li class="list-inline-item"><a href="{{ route('branch.dashboard') }}" class="text-decoration-none">Branch Dashboard</a></li>
                            @else
                                <li class="list-inline-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none">My Account</a></li>
                            @endif
                        @else
                            <li class="list-inline-item"><a href="{{ route('login') }}" class="text-decoration-none">My Account</a></li>
                        @endauth
                        <li class="list-inline-item"><span class="top-bar-sep">|</span></li>
                        <li class="list-inline-item"><a href="{{ route('order.tracking.index') }}" class="text-decoration-none">Order Tracking</a></li>
                        <li class="list-inline-item"><span class="top-bar-sep">|</span></li>
                        <li class="list-inline-item"><span class="text-white-50">Need help? Call Us: <strong class="text-white">{{ $contactPhone }}</strong></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Top Bar -->
    <div class="top-utility-bar top-bar-premium py-2 d-block d-md-none">
        <div class="container">
            <div class="row g-2 align-items-center">
                <div class="col-12 text-center small">
                    <span class="d-inline-flex align-items-center gap-2 justify-content-center">
                        <i class="fas fa-circle-info opacity-75"></i>
                        {{ \App\Models\Setting::getTopbarTagline() }}
                    </span>
                </div>
                <div class="col-12">
                    <ul class="list-inline mb-0 small text-center">
                        @auth
                            @if(Auth::user()->isBranchStaff())
                                <li class="list-inline-item"><a href="{{ route('branch.dashboard') }}" class="text-decoration-none">Branch Dashboard</a></li>
                            @else
                                <li class="list-inline-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none">My Account</a></li>
                            @endif
                        @else
                            <li class="list-inline-item"><a href="{{ route('login') }}" class="text-decoration-none">My Account</a></li>
                        @endauth
                        <li class="list-inline-item"><span class="top-bar-sep">|</span></li>
                        <li class="list-inline-item"><a href="{{ route('order.tracking.index') }}" class="text-decoration-none">Order Tracking</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header bg-white py-md-3 py-1">
        <div class="container">
            <div class="row align-items-center g-0 gx-md-2">
                <!-- Mobile menu (opens sidebar) -->
                <div class="col-auto d-md-none ps-1 pe-0">
                    <button class="btn btn-mobile-nav-toggle rounded-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainNavOffcanvas" aria-controls="mainNavOffcanvas" aria-label="Open menu">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                </div>
                <!-- Logo -->
                <div class="col col-md-2 col-lg-2 ps-md-0">
                    <a class="navbar-brand d-flex align-items-center mb-0" href="{{ route('home') }}">
                        <img src="{{ \App\Models\Setting::getHeaderLogo() }}" alt="YENDIA Pharmacy" style="height: 65px; width: auto; max-width: 100%;" class="img-fluid">
                    </a>
                </div>
                
                <!-- Search Bar -->
                <div class="col-12 col-md-6 col-lg-6 order-3 order-md-2 px-2 px-md-3">
                    <form action="{{ route('products.index') }}" method="GET" class="search-bar-form" style="margin: 0;" data-suggestions-url="{{ route('products.search.suggestions') }}">
                        <div class="search-bar-container header-search-pill" style="display: flex; align-items: center;">
                            <!-- All Categories Dropdown -->
                            <div class="search-category-dropdown" style="position: relative; padding: 8px 14px;">
                                <select name="category" id="headerSearchCategory" class="form-select border-0 shadow-none" style="padding: 0; font-size: 0.9rem; color: #1e3a8a; background: transparent; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"%23999\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 0 center; background-size: 16px; padding-right: 25px;">
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
                                <input type="text" name="search" id="headerSearchInput" class="form-control border-0 shadow-none" placeholder="Search for items..." value="{{ request('search') }}" autocomplete="off" style="padding: 10px 14px; font-size: 0.95rem; background: transparent;">
                                <div id="headerSearchSuggestions" class="header-search-suggestions" role="listbox" aria-label="Search suggestions"></div>
                            </div>
                            <!-- Search button -->
                            <div class="search-icon-wrapper border-0" style="padding: 4px 6px 4px 0;">
                                <button type="submit" class="btn btn-search-primary">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- User Actions -->
                <div class="col-auto col-md-4 col-lg-4 order-2 order-md-3 pe-1 pe-md-2">
                    <div class="d-flex align-items-center justify-content-end gap-1 gap-md-3 flex-nowrap">
                        @if($whatsappDigits !== '')
                            <a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none d-none d-lg-flex align-items-center" style="color: #4b5563;">
                                <i class="fab fa-whatsapp" style="font-size: 1.35rem; color: #25d366;"></i>
                                <span class="ms-2 d-flex flex-column lh-sm">
                                    <span class="small text-muted" style="font-size: 0.7rem;">WhatsApp Us</span>
                                    <span class="fw-semibold" style="font-size: 0.85rem;">{{ $contactPhone }}</span>
                                </span>
                            </a>
                        @endif
                        @auth
                            @php
                                $unreadNotifications = \App\Models\Notification::where('user_id', Auth::id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            <div class="dropdown">
                                <a href="#" class="text-decoration-none d-flex align-items-center dropdown-toggle position-relative" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #4b5563;">
                                    <i class="fas fa-user" style="font-size: 1.25rem; color: #4b5563;"></i>
                                    @if($unreadNotifications > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge-header" style="font-size: 0.65rem; padding: 2px 6px; margin-left: -8px; margin-top: -5px;">{{ $unreadNotifications }}</span>
                                    @endif
                                    <span class="ms-2 d-none d-md-inline fw-semibold" style="color: #4b5563; font-size: 0.95rem;">Account</span>
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
                                            <a class="dropdown-item" href="{{ route('user.refund-requests.index') }}">
                                                <i class="fas fa-money-bill-wave me-2"></i>Refund Requests
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
                                        <h6 class="dropdown-header">Services</h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.services.insurance') }}">
                                            <i class="fas fa-shield-alt me-2"></i>Insurance Request
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.services.insurance-requests') }}">
                                            <i class="fas fa-list me-2"></i>My Insurance Requests
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.services.prescription') }}">
                                            <i class="fas fa-prescription me-2"></i>Send Prescription
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.services.item-request') }}">
                                            <i class="fas fa-inbox me-2"></i>Request Item
                                        </a>
                                    </li>
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
                                <span class="ms-2 d-none d-md-inline fw-semibold" style="color: #4b5563; font-size: 0.95rem;">Account</span>
                            </a>
                        @endauth
                        <a href="{{ route('cart.index') }}" class="text-decoration-none d-flex align-items-center position-relative" style="color: #4b5563;">
                            <i class="fas fa-shopping-cart" style="font-size: 1.25rem; color: #4b5563;"></i>
                            @php
                                $cartCount = count(session()->get('cart', []));
                            @endphp
                            @if($cartCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge-header" style="font-size: 0.65rem; padding: 2px 6px; margin-left: -8px; margin-top: -5px;">{{ $cartCount }}</span>
                            @else
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge-header" style="font-size: 0.65rem; padding: 2px 6px; margin-left: -8px; margin-top: -5px;">0</span>
                            @endif
                            <span class="ms-2 d-none d-md-inline fw-semibold" style="color: #4b5563; font-size: 0.95rem;">Cart</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    </div>
    <div id="siteHeaderSpacer" class="site-header-spacer" aria-hidden="true"></div>

    <!-- Main Navigation Bar (desktop only; mobile uses offcanvas from header) -->
    <nav class="main-navbar d-none d-md-block" style="background: white !important; border-color: #e5e7eb !important; padding-top: 8px; padding-bottom: 8px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3" style="border-right: 1px solid #e5e7eb;">
                    <div class="dropdown">
                        <button class="btn browse-categories-btn w-100 text-start py-2 py-md-3 dropdown-toggle" type="button" id="browseCategoriesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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
                <div class="col-md-9">
                    <ul class="nav mb-0 flex-wrap align-items-center">
                        <li class="nav-item"><a href="{{ route('home') }}" class="nav-link">Home</a></li>
                        <li class="nav-item"><a href="https://yendiapharmacy.com/about/" class="nav-link">About</a></li>
                        @if(isset($navbarCategories) && $navbarCategories->count() > 0)
                            @foreach($navbarCategories as $category)
                                <li class="nav-item">
                                    <a href="{{ route('products.index', ['category' => $category->id]) }}" class="nav-link">{{ $category->name }}</a>
                                </li>
                            @endforeach
                        @endif
                        <li class="nav-item"><a href="https://yendiapharmacy.com/services/" class="nav-link">Marketing</a></li>
                        <li class="nav-item"><a href="https://yendiapharmacy.com/why-parter-us/" class="nav-link">Why Partner Us?</a></li>
                        <li class="nav-item"><a href="https://yendiapharmacy.com/contact/" class="nav-link">Contact</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile: slide-in navigation -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mainNavOffcanvas" aria-labelledby="mainNavOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="mainNavOffcanvasLabel" style="color: var(--green-color);">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body offcanvas-nav overflow-y-auto">
            <p class="offcanvas-section-title">Categories</p>
            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('products.index') }}"><i class="fas fa-th-large me-2 text-muted"></i>All Products</a>
                @if(isset($categories) && $categories->count() > 0)
                    @foreach($categories as $category)
                        <a class="nav-link" href="{{ route('products.index', ['category' => $category->id]) }}"><i class="fas fa-chevron-right me-2 small text-muted"></i>{{ $category->name }}</a>
                    @endforeach
                @else
                    <a class="nav-link" href="{{ route('products.index') }}?prescription=not_required">OTC Products</a>
                @endif
            </nav>
            <p class="offcanvas-section-title">Explore</p>
            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-home me-2 text-muted"></i>Home</a>
                <a class="nav-link" href="https://yendiapharmacy.com/about/"><i class="fas fa-info-circle me-2 text-muted"></i>About</a>
                @if(isset($navbarCategories) && $navbarCategories->count() > 0)
                    @foreach($navbarCategories as $category)
                        <a class="nav-link" href="{{ route('products.index', ['category' => $category->id]) }}"><i class="fas fa-tag me-2 text-muted"></i>{{ $category->name }}</a>
                    @endforeach
                @endif
                <a class="nav-link" href="https://yendiapharmacy.com/services/"><i class="fas fa-bullhorn me-2 text-muted"></i>Marketing</a>
                <a class="nav-link" href="https://yendiapharmacy.com/why-parter-us/"><i class="fas fa-handshake me-2 text-muted"></i>Why Partner Us?</a>
                <a class="nav-link" href="https://yendiapharmacy.com/contact/"><i class="fas fa-envelope me-2 text-muted"></i>Contact</a>
                <a class="nav-link" href="{{ route('order.tracking.index') }}"><i class="fas fa-truck me-2 text-muted"></i>Order Tracking</a>
            </nav>
        </div>
    </div>

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
                    <p class="text-black-50">Your trusted online pharmacy for all your healthcare needs.</p>
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
                        <li><a href="{{ route('home') }}" class="text-black-50">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-black-50">Products</a></li>
                        <li><a href="{{ route('cart.index') }}" class="text-black-50">Cart</a></li>
                        @php
                            $pages = \App\Models\Page::where('is_active', true)->orderBy('title', 'asc')->get();
                        @endphp
                        @foreach($pages as $page)
                            <li><a href="{{ route('pages.show', $page->slug) }}" class="text-black-50">{{ $page->title }}</a></li>
                        @endforeach
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
            <div class="text-center" style="background-color: #158d43;
    color: white;">
                <p>&copy; {{ \App\Models\Setting::getCopyrightYear() }} YENDIA Pharmacy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var searchForm = document.querySelector('.search-bar-form[data-suggestions-url]');
            var searchInput = document.getElementById('headerSearchInput');
            var suggestionsEl = document.getElementById('headerSearchSuggestions');
            var categorySelect = document.getElementById('headerSearchCategory');
            var debounceTimer = null;
            var lastController = null;

            function hideSuggestions() {
                if (suggestionsEl) {
                    suggestionsEl.classList.remove('is-open');
                    suggestionsEl.innerHTML = '';
                }
            }

            function showSuggestions(html) {
                if (!suggestionsEl) return;
                suggestionsEl.innerHTML = html;
                suggestionsEl.classList.toggle('is-open', suggestionsEl.childNodes.length > 0);
            }

            if (searchForm && searchInput && suggestionsEl && searchForm.dataset.suggestionsUrl) {
                var urlBase = searchForm.dataset.suggestionsUrl;

                searchInput.addEventListener('input', function() {
                    var q = (searchInput.value || '').trim();
                    if (debounceTimer) clearTimeout(debounceTimer);
                    if (lastController) lastController.abort();

                    if (q.length < 2) {
                        hideSuggestions();
                        return;
                    }

                    debounceTimer = setTimeout(function() {
                        lastController = new AbortController();
                        var params = new URLSearchParams({ q: q });
                        if (categorySelect && categorySelect.value) {
                            params.set('category', categorySelect.value);
                        }
                        fetch(urlBase + '?' + params.toString(), {
                            signal: lastController.signal,
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(function(r) { return r.json(); })
                            .then(function(items) {
                                if (!Array.isArray(items) || items.length === 0) {
                                    showSuggestions('<div class="hint">No matching products</div>');
                                    return;
                                }
                                var html = items.map(function(item) {
                                    var label = String(item.label || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
                                    var href = String(item.url || '#').replace(/"/g, '&quot;');
                                    return '<button type="button" role="option" class="header-search-suggestion-item" data-url="' + href + '">' + label + '</button>';
                                }).join('');
                                showSuggestions(html);
                            })
                            .catch(function() {
                                hideSuggestions();
                            });
                    }, 280);
                });

                suggestionsEl.addEventListener('click', function(e) {
                    var btn = e.target.closest('.header-search-suggestion-item');
                    if (btn && btn.dataset.url) {
                        window.location.href = btn.dataset.url;
                    }
                });

                searchInput.addEventListener('blur', function() {
                    setTimeout(hideSuggestions, 200);
                });

                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') hideSuggestions();
                });

                categorySelect && categorySelect.addEventListener('change', function() {
                    searchInput.dispatchEvent(new Event('input'));
                });
            }
        });
    </script>
    <script>
        (function () {
            function syncSiteHeaderSpacer() {
                var fixed = document.getElementById('siteHeaderFixed');
                var spacer = document.getElementById('siteHeaderSpacer');
                if (!fixed || !spacer) return;
                spacer.style.height = fixed.offsetHeight + 'px';
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', syncSiteHeaderSpacer);
            } else {
                syncSiteHeaderSpacer();
            }
            window.addEventListener('resize', syncSiteHeaderSpacer);
            window.addEventListener('load', syncSiteHeaderSpacer);
        })();
    </script>
    
    @stack('scripts')
</body>
</html>

