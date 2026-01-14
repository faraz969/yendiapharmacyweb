@extends('web.layouts.app')

@section('title', 'My Refund Requests')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-money-bill-wave me-2" style="color:#dc8423;"></i>My Refund Requests
    </h2>

    <div class="card shadow">
        <div class="card-body">
            @if($refundRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Refund Number</th>
                                <th>Order Number</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($refundRequests as $refundRequest)
                                <tr>
                                    <td><strong>{{ $refundRequest->refund_number }}</strong></td>
                                    <td>
                                        <a href="{{ route('user.orders.show', $refundRequest->order_id) }}" class="text-decoration-none">
                                            {{ $refundRequest->order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ \App\Models\Setting::formatPrice($refundRequest->refund_amount) }}</td>
                                    <td>
                                        @if($refundRequest->refund_method === 'mobile_money')
                                            <span class="badge bg-info">
                                                {{ $refundRequest->mobile_money_provider }}<br>
                                                <small>{{ $refundRequest->mobile_money_number }}</small>
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                {{ $refundRequest->bank_name }}<br>
                                                <small>{{ $refundRequest->account_number }}</small>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeColors = [
                                                'pending' => 'warning',
                                                'approved' => 'info',
                                                'rejected' => 'danger',
                                                'processed' => 'primary',
                                                'completed' => 'success',
                                            ];
                                            $color = $badgeColors[$refundRequest->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">
                                            {{ ucfirst($refundRequest->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $refundRequest->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('user.refund-requests.show', $refundRequest) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $refundRequests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-money-bill-wave fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Refund Requests</h5>
                    <p class="text-muted">You haven't submitted any refund requests yet.</p>
                    <a href="{{ route('user.orders.index') }}" class="btn" style="background-color:#dc8423; color:white;">
                        <i class="fas fa-shopping-bag me-2"></i>View My Orders
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

