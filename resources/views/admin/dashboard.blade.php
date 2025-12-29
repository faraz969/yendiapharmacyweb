@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@if($isBranchStaff ?? false)
    <div class="alert alert-info mb-4">
        <i class="fas fa-building me-2"></i>
        <strong>Branch Staff Dashboard</strong> - You are viewing data for <strong>{{ $branch->name ?? 'your branch' }}</strong>
    </div>
@endif
<div class="row">
    <div class="col-md-3">
        <div class="stat-card blue">
            <div class="stat-label">Total Products</div>
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <i class="fas fa-cube fa-2x" style="opacity: 0.3; float: right; margin-top: -40px;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <i class="fas fa-file-invoice fa-2x" style="opacity: 0.3; float: right; margin-top: -40px;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="stat-label">Pending Orders</div>
            <div class="stat-value">{{ number_format($stats['pending_orders']) }}</div>
            <i class="fas fa-clock fa-2x" style="opacity: 0.3; float: right; margin-top: -40px;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card yellow">
            <div class="stat-label">Today Sales</div>
            <div class="stat-value">{{ \App\Models\Setting::formatPrice($stats['today_sales'] ?? 0) }}</div>
            <i class="fas fa-calendar-day fa-2x" style="opacity: 0.3; float: right; margin-top: -40px;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card purple">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">{{ \App\Models\Setting::formatPrice($stats['total_revenue']) }}</div>
            <i class="fas fa-dollar-sign fa-2x" style="opacity: 0.3; float: right; margin-top: -40px;"></i>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_orders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ \App\Models\Setting::formatPrice($order->total_amount) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

