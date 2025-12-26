@extends('admin.layouts.app')

@section('title', 'Item Requests')
@section('page-title', 'Item Requests')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-inbox me-2"></i>All Item Requests</h5>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.item-requests.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by request number, item name, customer name, or phone..." value="{{ request('search') }}">
                </div>
                @if(isset($branches) && $branches)
                <div class="col-md-2">
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
                <div class="col-md-2">
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
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Request #</th>
                        <th>Item Name</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Quantity</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemRequests as $request)
                        <tr>
                            <td><code>{{ $request->request_number }}</code></td>
                            <td>{{ $request->item_name }}</td>
                            <td>{{ $request->customer_name }}</td>
                            <td>{{ $request->customer_phone }}</td>
                            <td>{{ $request->quantity }}</td>
                            <td>{{ $request->branch->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $badgeColors = [
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'fulfilled' => 'success',
                                        'rejected' => 'danger',
                                        'cancelled' => 'secondary',
                                    ];
                                    $color = $badgeColors[$request->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.item-requests.show', $request->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No item requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $itemRequests->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

