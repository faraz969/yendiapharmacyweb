@extends('admin.layouts.app')

@section('title', 'Refund Requests')
@section('page-title', 'Refund Requests')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>All Refund Requests</h5>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.refund-requests.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Refund #, Order #, Customer name or phone..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date (Single)</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
            <div class="row g-3 align-items-end mt-2">
                <div class="col-md-3">
                    <label class="form-label small">Date Range: From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Date Range: To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.refund-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Refund #</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refundRequests as $refundRequest)
                        <tr>
                            <td><code>{{ $refundRequest->refund_number }}</code></td>
                            <td>
                                <a href="{{ route('admin.orders.show', $refundRequest->order_id) }}">
                                    <code>{{ $refundRequest->order->order_number }}</code>
                                </a>
                            </td>
                            <td>
                                {{ $refundRequest->order->customer_name }}<br>
                                <small class="text-muted">{{ $refundRequest->order->customer_phone }}</small>
                            </td>
                            <td>{{ \App\Models\Setting::formatPrice($refundRequest->refund_amount) }}</td>
                            <td>
                                @if($refundRequest->refund_method === 'mobile_money')
                                    <span class="badge bg-info">
                                        {{ $refundRequest->mobile_money_provider }}<br>
                                        {{ $refundRequest->mobile_money_number }}
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        {{ $refundRequest->bank_name }}<br>
                                        {{ $refundRequest->account_number }}
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
                            <td>{{ $refundRequest->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.refund-requests.show', $refundRequest) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No refund requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $refundRequests->links() }}
        </div>
    </div>
</div>
@endsection

