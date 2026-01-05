@extends('admin.layouts.app')

@section('title', 'Insurance Requests')
@section('page-title', 'Insurance Requests')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>All Insurance Requests</h5>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.insurance-requests.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by request number, customer name, phone, or insurance number..." value="{{ request('search') }}">
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
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Insurance Company</th>
                        <th>Insurance #</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td><code>{{ $request->request_number }}</code></td>
                            <td>{{ $request->customer_name }}</td>
                            <td>{{ $request->customer_phone }}</td>
                            <td>{{ $request->insuranceCompany->name }}</td>
                            <td>{{ $request->insurance_number }}</td>
                            <td>{{ $request->items->count() }}</td>
                            <td>
                                @php
                                    $badgeColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'order_created' => 'info',
                                    ];
                                    $color = $badgeColors[$request->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.insurance-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No insurance requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection

