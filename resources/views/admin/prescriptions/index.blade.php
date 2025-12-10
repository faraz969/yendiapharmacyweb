@extends('admin.layouts.app')

@section('title', 'Prescriptions')
@section('page-title', 'Prescriptions')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-prescription me-2"></i>All Prescriptions</h5>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.prescriptions.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search by prescription number, doctor name, or patient name..." value="{{ request('search') }}">
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
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Prescription #</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $prescription)
                        <tr>
                            <td><code>{{ $prescription->prescription_number }}</code></td>
                            <td>{{ $prescription->patient_name }}</td>
                            <td>{{ $prescription->doctor_name }}</td>
                            <td>{{ $prescription->prescription_date->format('M d, Y') }}</td>
                            <td>{{ $prescription->user->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $badgeColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                    $color = $badgeColors[$prescription->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst($prescription->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.prescriptions.show', $prescription->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No prescriptions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $prescriptions->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

