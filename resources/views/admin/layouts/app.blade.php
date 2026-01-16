<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Pharmacy Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #2c3e50;
            --sidebar-active: #3498db;
            --header-bg: #ffffff;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: white;
            padding: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--sidebar-active);
            color: white;
            padding-left: 25px;
        }
        
        .sidebar-menu i {
            width: 20px;
            margin-right: 10px;
        }
        
        .sidebar-menu .badge {
            float: right;
            background: #e74c3c;
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 20px;
            text-align: center;
        }
        
        .sidebar-menu .badge.hidden {
            display: none;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            transition: margin-top 0.3s ease-out;
        }
        
        .main-content.with-notification {
            margin-top: 48px;
        }
        
        .header {
            background: var(--header-bg);
            padding: 15px 30px;
            margin: -20px -20px 20px -20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .new-order-notification {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 12px 30px;
            z-index: 1001;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            font-weight: 600;
            animation: slideDown 0.3s ease-out;
        }
        
        .new-order-notification.show {
            display: flex;
        }
        
        .new-order-notification .badge {
            background: white;
            color: #f5576c;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-left: 5px;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
            }
            to {
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .new-order-notification {
                left: 0;
                flex-direction: column;
                gap: 10px;
            }
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card.blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .stat-card.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.yellow {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        }
        
        .stat-card.purple {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .btn-primary {
            background: var(--sidebar-active);
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .table {
            background: white;
        }
        
        .table thead {
            background: #f8f9fa;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-pills"></i> Pharmacy Admin</h4>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> Orders
                    <span class="badge" id="new-orders-badge" style="display: none;">0</span>
                </a>
            </li>
            @if(!Auth::user()->isBranchStaff())
            <li>
                <a href="{{ route('admin.reports.profit-loss') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Reports
                </a>
            </li>
            @endif
            @if(Auth::user()->isBranchStaff())
                {{-- Branch staff menu items --}}
                <li>
                    <a href="{{ route('branch.prescriptions.index') }}" class="{{ request()->routeIs('branch.prescriptions.*') ? 'active' : '' }}">
                        <i class="fas fa-prescription"></i> Prescriptions
                    </a>
                </li>
                <li>
                    <a href="{{ route('branch.item-requests.index') }}" class="{{ request()->routeIs('branch.item-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-inbox"></i> Item Requests
                    </a>
                </li>
            @endif
            
            @if(!Auth::user()->isBranchStaff())
                {{-- Only show these menu items for non-branch staff (admins/managers) --}}
                <li>
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fas fa-folder"></i> Categories
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fas fa-cube"></i> Products
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.vendors.index') }}" class="{{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> Vendors
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.purchase-orders.index') }}" class="{{ request()->routeIs('admin.purchase-orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i> Purchase Orders
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.prescriptions.index') }}" class="{{ request()->routeIs('admin.prescriptions.*') ? 'active' : '' }}">
                        <i class="fas fa-prescription"></i> Prescriptions
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.item-requests.index') }}" class="{{ request()->routeIs('admin.item-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-inbox"></i> Item Requests
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.insurance-companies.index') }}" class="{{ request()->routeIs('admin.insurance-companies.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt"></i> Insurance Companies
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.insurance-requests.index') }}" class="{{ request()->routeIs('admin.insurance-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt"></i> Insurance Requests
                        <span class="badge" id="new-insurance-requests-badge" style="display: none;">0</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.refund-requests.index') }}" class="{{ request()->routeIs('admin.refund-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave"></i> Refund Requests
                        <span class="badge" id="new-refund-requests-badge" style="display: none;">0</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.delivery-zones.index') }}" class="{{ request()->routeIs('admin.delivery-zones.*') ? 'active' : '' }}">
                        <i class="fas fa-map-marker-alt"></i> Delivery Zones
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.activity-logs.index') }}" class="{{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <i class="fas fa-history"></i> Activity Logs
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.branches.index') }}" class="{{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i> Branches
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                        <i class="fas fa-image"></i> Banners
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.marketing-banners.index') }}" class="{{ request()->routeIs('admin.marketing-banners.*') ? 'active' : '' }}">
                        <i class="fas fa-ad"></i> Marketing Banners
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pages.index') }}" class="{{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Pages
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.app-notices.index') }}" class="{{ request()->routeIs('admin.app-notices.*') ? 'active' : '' }}">
                        <i class="fas fa-mobile-alt"></i> App Notices
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('admin.profile.index') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> My Profile
                </a>
            </li>
            @if(Auth::user()->hasRole('delivery_person'))
            <li>
                <a href="{{ route('delivery.dashboard') }}" class="{{ request()->routeIs('delivery.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i> Delivery Dashboard
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

    <!-- New Notifications Bar -->
    <div class="new-order-notification" id="new-notifications-bar">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-bell"></i>
            <div class="d-flex align-items-center gap-3">
                <span id="new-orders-text" style="display: none;">
                    <strong>New Orders:</strong> <span class="badge" id="new-order-count-badge">0</span>
                </span>
                <span id="new-insurance-requests-text" style="display: none;">
                    <strong>New Insurance Requests:</strong> <span class="badge" id="new-insurance-requests-count-badge">0</span>
                </span>
                <span id="new-refund-requests-text" style="display: none;">
                    <strong>New Refund Requests:</strong> <span class="badge" id="new-refund-requests-count-badge">0</span>
                </span>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" onclick="document.getElementById('new-notifications-bar').classList.remove('show')" aria-label="Close"></button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            <div class="user-menu">
                @if(Auth::user()->isBranchStaff())
                    <span class="me-3"><i class="fas fa-building"></i> {{ Auth::user()->branch->name ?? 'Branch Staff' }}</span>
                @endif
                <span><i class="fas fa-user"></i> {{ Auth::user()->name }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Update all notification counts
        function updateAllNotificationCounts() {
            // Update Orders Count
            fetch('{{ route("admin.orders.new-count") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                // Update sidebar badge
                const badge = document.getElementById('new-orders-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
                
                // Update top bar notification
                const ordersText = document.getElementById('new-orders-text');
                const ordersCountBadge = document.getElementById('new-order-count-badge');
                if (ordersText && ordersCountBadge) {
                    if (data.count > 0) {
                        ordersCountBadge.textContent = data.count;
                        ordersText.style.display = 'inline';
                    } else {
                        ordersText.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching new orders count:', error);
            });

            // Update Insurance Requests Count
            fetch('{{ route("admin.insurance-requests.new-count") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                // Update sidebar badge
                const badge = document.getElementById('new-insurance-requests-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
                
                // Update top bar notification
                const insuranceText = document.getElementById('new-insurance-requests-text');
                const insuranceCountBadge = document.getElementById('new-insurance-requests-count-badge');
                if (insuranceText && insuranceCountBadge) {
                    if (data.count > 0) {
                        insuranceCountBadge.textContent = data.count;
                        insuranceText.style.display = 'inline';
                    } else {
                        insuranceText.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching new insurance requests count:', error);
            });

            // Update Refund Requests Count
            fetch('{{ route("admin.refund-requests.new-count") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                // Update sidebar badge
                const badge = document.getElementById('new-refund-requests-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
                
                // Update top bar notification
                const refundText = document.getElementById('new-refund-requests-text');
                const refundCountBadge = document.getElementById('new-refund-requests-count-badge');
                if (refundText && refundCountBadge) {
                    if (data.count > 0) {
                        refundCountBadge.textContent = data.count;
                        refundText.style.display = 'inline';
                    } else {
                        refundText.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching new refund requests count:', error);
            });

            // Show/hide notification bar based on any count
            setTimeout(() => {
                const notificationBar = document.getElementById('new-notifications-bar');
                const mainContent = document.querySelector('.main-content');
                const hasNewOrders = document.getElementById('new-orders-text')?.style.display === 'inline';
                const hasNewInsurance = document.getElementById('new-insurance-requests-text')?.style.display === 'inline';
                const hasNewRefund = document.getElementById('new-refund-requests-text')?.style.display === 'inline';
                
                if (notificationBar && mainContent) {
                    if (hasNewOrders || hasNewInsurance || hasNewRefund) {
                        notificationBar.classList.add('show');
                        mainContent.classList.add('with-notification');
                    } else {
                        notificationBar.classList.remove('show');
                        mainContent.classList.remove('with-notification');
                    }
                }
            }, 100);
        }
        
        // Update count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateAllNotificationCounts();
            
            // Update count every 30 seconds
            setInterval(updateAllNotificationCounts, 30000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>

