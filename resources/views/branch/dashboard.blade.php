@extends('web.layouts.app')

@section('title', 'Branch Dashboard')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-building me-2"></i>Branch Dashboard - {{ $branch->name }}</h2>
        <div>
            <span class="badge bg-success">Active Branch</span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body">
                    <h3>{{ $stats['total'] }}</h3>
                    <p class="mb-0">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div class="card-body">
                    <h3>{{ $stats['pending'] }}</h3>
                    <p class="mb-0">Pending Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <div class="card-body">
                    <h3>{{ $stats['packed'] }}</h3>
                    <p class="mb-0">Packed</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                <div class="card-body">
                    <h3>{{ $stats['delivered'] }}</h3>
                    <p class="mb-0">Delivered</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <a href="{{ route('branch.prescriptions.index') }}" class="text-decoration-none">
                <div class="card text-center" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                    <div class="card-body">
                        <h3>{{ $stats['prescriptions_pending'] ?? 0 }}</h3>
                        <p class="mb-0">Pending Prescriptions</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('branch.item-requests.index') }}" class="text-decoration-none">
                <div class="card text-center" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white;">
                    <div class="card-body">
                        <h3>{{ $stats['item_requests_pending'] ?? 0 }}</h3>
                        <p class="mb-0">Item Requests</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-md-4">
            <a href="{{ route('branch.prescriptions.index') }}" class="btn btn-lg btn-outline-primary w-100">
                <i class="fas fa-prescription me-2"></i>View Prescriptions
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('branch.item-requests.index') }}" class="btn btn-lg btn-outline-info w-100">
                <i class="fas fa-inbox me-2"></i>View Item Requests
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('branch.dashboard') }}" class="btn btn-lg btn-outline-success w-100">
                <i class="fas fa-shopping-cart me-2"></i>View Orders
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Orders</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search orders..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->customer_phone }}</td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('branch.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

