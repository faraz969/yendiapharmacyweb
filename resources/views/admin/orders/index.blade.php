@extends('admin.layouts.app')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>All Orders</h5>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by order number, customer name, or phone..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
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
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
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
                            <td>
                                <strong>{{ $order->customer_name }}</strong><br>
                                <small class="text-muted">{{ $order->customer_phone }}</small>
                            </td>
                            <td>{{ $order->items->count() }} item(s)</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
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
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No orders found.</td>
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
@endsection

