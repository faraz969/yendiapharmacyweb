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
            --primary-color: #2563eb;
            --secondary-color: #10b981;
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
        
        /* Header */
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
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
            color: var(--primary-color);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.4);
        }
        
        .btn-success {
            background: var(--secondary-color);
            border: none;
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
            background: var(--text-dark);
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
            background: #f59e0b;
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
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-pills me-2"></i>Pharmacy Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}?prescription=not_required">OTC Products</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('order.tracking.index') }}">
                            <i class="fas fa-search"></i> Track Order
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart"></i> Cart
                            @php
                                $cartCount = count(session()->get('cart', []));
                            @endphp
                            @if($cartCount > 0)
                                <span class="cart-badge">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.orders.index') }}"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white ms-2" href="{{ route('register') }}">
                                Sign Up
                            </a>
                        </li>
                    @endauth
                </ul>
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
                    <h5><i class="fas fa-pills me-2"></i>Pharmacy Store</h5>
                    <p class="text-muted">Your trusted online pharmacy for all your healthcare needs.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-white-50">Products</a></li>
                        <li><a href="{{ route('cart.index') }}" class="text-white-50">Cart</a></li>
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
            <div class="text-center text-muted">
                <p>&copy; {{ date('Y') }} Pharmacy Store. All rights reserved.</p>
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

