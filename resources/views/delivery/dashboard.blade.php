@extends('admin.layouts.app')

@section('title', 'Delivery Dashboard')
@section('page-title', 'Delivery Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Active Deliveries</h5>
                <h2 class="mb-0">{{ $stats['active_deliveries'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Delivered Today</h5>
                <h2 class="mb-0">{{ $stats['total_delivered_today'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Delivered This Month</h5>
                <h2 class="mb-0">{{ $stats['total_delivered_this_month'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Active Deliveries</h5>
            </div>
            <div class="card-body">
                @if($assignedOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Delivery Address</th>
                                    <th>Amount</th>
                                    <th>Assigned At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignedOrders as $order)
                                    <tr>
                                        <td><code>{{ $order->order_number }}</code></td>
                                        <td>
                                            <strong>{{ $order->customer_name }}</strong><br>
                                            <small class="text-muted">{{ $order->customer_phone }}</small>
                                        </td>
                                        <td>
                                            {{ $order->delivery_address }}
                                            @if($order->branch)
                                                <br><small class="text-muted">Branch: {{ $order->branch->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ \App\Models\Setting::formatPrice($order->total_amount) }}</td>
                                        <td>{{ $order->updated_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('delivery.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form action="{{ route('delivery.orders.mark-delivered', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark order #{{ $order->order_number }} as delivered?');">
                                                        <i class="fas fa-check"></i> Mark Delivered
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">No active deliveries assigned to you.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($deliveredOrders->count() > 0)
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Recent Deliveries</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Delivered At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deliveredOrders as $order)
                                <tr>
                                    <td><code>{{ $order->order_number }}</code></td>
                                    <td>
                                        <strong>{{ $order->customer_name }}</strong><br>
                                        <small class="text-muted">{{ $order->customer_phone }}</small>
                                    </td>
                                    <td>{{ \App\Models\Setting::formatPrice($order->total_amount) }}</td>
                                    <td>{{ $order->delivered_at ? $order->delivered_at->format('M d, Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('delivery.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

