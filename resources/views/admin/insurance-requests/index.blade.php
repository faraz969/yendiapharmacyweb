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
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Request #, Name, Phone, Insurance #..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Insurance Company</label>
                    <select name="insurance_company_id" class="form-select">
                        <option value="">All Companies</option>
                        @foreach($insuranceCompanies as $company)
                            <option value="{{ $company->id }}" {{ request('insurance_company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(isset($branches) && $branches)
                <div class="col-md-2">
                    <label class="form-label small">Branch</label>
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
                    <label class="form-label small">Status</label>
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
                    <label class="form-label small">Date (Single)</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}" title="Single date takes precedence over date range">
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
                    <a href="{{ route('admin.insurance-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>

        <div class="mb-3 d-flex justify-content-end">
            <a href="{{ route('admin.insurance-requests.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
        </div>

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

