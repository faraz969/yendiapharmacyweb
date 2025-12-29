@extends('admin.layouts.app')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
@if(Auth::user()->isBranchStaff())
    <div class="alert alert-info mb-4">
        <i class="fas fa-building me-2"></i>
        <strong>Branch Staff View</strong> - You are viewing orders for <strong>{{ Auth::user()->branch->name ?? 'your branch' }}</strong>
    </div>
@endif
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>All Orders</h5>
        <div class="d-flex align-items-center gap-2">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="autoRefreshToggle" checked>
                <label class="form-check-label" for="autoRefreshToggle">
                    <i class="fas fa-sync-alt me-1"></i>Auto Refresh
                </label>
            </div>
            <span id="lastRefreshTime" class="text-muted small"></span>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by order number, customer name, or phone..." value="{{ request('search') }}">
                </div>
                @if(!Auth::user()->isBranchStaff())
                <div class="col-md-3">
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-12">
                    <a href="{{ route('admin.orders.index', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>Export to Excel
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        @if(!Auth::user()->isBranchStaff())
                        <th>Branch</th>
                        @endif
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><code>{{ $order->order_number }}</code></td>
                            @if(!Auth::user()->isBranchStaff())
                            <td>
                                @if($order->branch)
                                    <span class="badge bg-info">{{ $order->branch->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            @endif
                            <td>
                                <strong>{{ $order->customer_name }}</strong><br>
                                <small class="text-muted">{{ $order->customer_phone }}</small>
                            </td>
                            <td>{{ $order->items->count() }} item(s)</td>
                            <td>{{ \App\Models\Setting::formatPrice($order->total_amount) }}</td>
                            <td>
                                @php
                                    $badgeColors = [
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'rejected' => 'danger',
                                        'packing' => 'primary',
                                        'packed' => 'success',
                                        'out_for_delivery' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'secondary',
                                    ];
                                    $color = $badgeColors[$order->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if(!Auth::user()->isBranchStaff() && $order->payment_status !== 'paid')
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete order #{{ $order->order_number }}? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Order">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isBranchStaff() ? '7' : '8' }}" class="text-center">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    let lastOrderId = {{ $latestOrderId ?? 0 }};
    let lastOrderCount = {{ $orderCount ?? 0 }};
    let autoRefreshInterval = null;
    let refreshInterval = 30000; // 30 seconds
    let isAutoRefreshEnabled = true;
    
    // Function to play bell sound using Web Audio API
    function playBellSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            // Bell-like sound: two tones
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            // Fallback: simple beep
            console.log('Web Audio API not supported');
        }
    }
    
    // Function to check for new orders
    function checkForNewOrders() {
        if (!isAutoRefreshEnabled) return;
        
        const params = new URLSearchParams(window.location.search);
        params.append('_token', '{{ csrf_token() }}');
        
        fetch('{{ route("admin.orders.check-new") }}?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Check if there's a new order
            if (data.latest_order_id > lastOrderId || data.order_count > lastOrderCount) {
                // New order detected!
                playBellSound();
                
                // Show notification
                showNewOrderNotification(data.latest_order_number);
                
                // Refresh the page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
            
            // Update last known values
            lastOrderId = data.latest_order_id;
            lastOrderCount = data.order_count;
            
            // Update last refresh time
            updateLastRefreshTime();
        })
        .catch(error => {
            console.error('Error checking for new orders:', error);
        });
    }
    
    // Function to show notification
    function showNewOrderNotification(orderNumber) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-bell me-2"></i>
            <strong>New Order!</strong> Order #${orderNumber} has been received.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
    
    // Function to update last refresh time
    function updateLastRefreshTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const timeElement = document.getElementById('lastRefreshTime');
        if (timeElement) {
            timeElement.textContent = `Last checked: ${timeString}`;
        }
    }
    
    // Toggle auto-refresh
    const toggle = document.getElementById('autoRefreshToggle');
    if (toggle) {
        toggle.addEventListener('change', function() {
            isAutoRefreshEnabled = this.checked;
            if (isAutoRefreshEnabled) {
                startAutoRefresh();
            } else {
                stopAutoRefresh();
            }
        });
    }
    
    // Start auto-refresh
    function startAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        autoRefreshInterval = setInterval(checkForNewOrders, refreshInterval);
        updateLastRefreshTime();
    }
    
    // Stop auto-refresh
    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
        const timeElement = document.getElementById('lastRefreshTime');
        if (timeElement) {
            timeElement.textContent = '';
        }
    }
    
    // Initialize
    if (isAutoRefreshEnabled) {
        startAutoRefresh();
    }
    
    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        stopAutoRefresh();
    });
})();
</script>
@endpush
@endsection

