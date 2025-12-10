@extends('web.layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-tachometer-alt text-primary me-2"></i>My Dashboard
    </h2>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-shopping-bag me-2"></i>Total Orders
                    </h5>
                    <h2 class="mb-0">{{ $stats['total_orders'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-clock me-2"></i>Pending Orders
                    </h5>
                    <h2 class="mb-0">{{ $stats['pending_orders'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-check-circle me-2"></i>Delivered Orders
                    </h5>
                    <h2 class="mb-0">{{ $stats['delivered_orders'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Recent Orders
            </h5>
        </div>
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>{{ $order->items->count() }} item(s)</td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        @if($order->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($order->status === 'approved')
                                            <span class="badge bg-info">Approved</span>
                                        @elseif($order->status === 'packing')
                                            <span class="badge bg-primary">Packing</span>
                                        @elseif($order->status === 'packed')
                                            <span class="badge bg-secondary">Packed</span>
                                        @elseif($order->status === 'out_for_delivery')
                                            <span class="badge bg-primary">Out for Delivery</span>
                                        @elseif($order->status === 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @elseif($order->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="badge bg-dark">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <p class="text-muted">You haven't placed any orders yet.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('user.orders.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-list me-2"></i>View All Orders
        </a>
    </div>
</div>
@endsection

