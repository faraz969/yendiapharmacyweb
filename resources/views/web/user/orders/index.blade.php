@extends('web.layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-shopping-bag text-primary me-2"></i>My Orders
    </h2>

    <div class="card shadow">
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
                                    <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
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
                                            <i class="fas fa-eye"></i> View Details
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
</div>
@endsection

