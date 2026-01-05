@extends('admin.layouts.app')

@section('title', 'Insurance Company Details')
@section('page-title', 'Insurance Company Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $company->name }}</h5>
                <a href="{{ route('admin.insurance-companies.edit', $company->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Company Name:</strong> {{ $company->name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        @if($company->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Created:</strong> {{ $company->created_at->format('M d, Y h:i A') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Updated:</strong> {{ $company->updated_at->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>
        </div>

        @if($company->insuranceRequests->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Insurance Requests ({{ $company->insuranceRequests->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Request Number</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company->insuranceRequests as $request)
                                    <tr>
                                        <td><code>{{ $request->request_number }}</code></td>
                                        <td>{{ $request->customer_name }}</td>
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
                                            <a href="{{ route('admin.insurance-requests.show', $request->id) }}" class="btn btn-sm btn-primary">
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
        @else
            <div class="card">
                <div class="card-body text-center text-muted">
                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                    <p>No insurance requests found for this company.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.insurance-companies.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Insurance Companies
    </a>
</div>
@endsection

