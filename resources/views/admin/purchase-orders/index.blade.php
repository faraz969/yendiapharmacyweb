@extends('admin.layouts.app')

@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>All Purchase Orders</h5>
        <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create PO
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.purchase-orders.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by PO number..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="vendor_id" class="form-select">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
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
                        <th>PO #</th>
                        <th>Vendor</th>
                        <th>Order Date</th>
                        <th>Expected Delivery</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                        <tr>
                            <td><code>{{ $po->po_number }}</code></td>
                            <td>{{ $po->vendor->name }}</td>
                            <td>{{ $po->order_date->format('M d, Y') }}</td>
                            <td>{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ \App\Models\Setting::formatPrice($po->total_amount) }}</td>
                            <td>
                                <span class="badge bg-{{ $po->status === 'received' ? 'success' : ($po->status === 'pending' ? 'warning' : 'info') }}">
                                    {{ ucfirst($po->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.purchase-orders.show', $po->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No purchase orders found. <a href="{{ route('admin.purchase-orders.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $purchaseOrders->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

