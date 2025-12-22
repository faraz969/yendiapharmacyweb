@extends('admin.layouts.app')

@section('title', 'Branch Details')
@section('page-title', 'Branch Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>{{ $branch->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Address:</strong><br>
                        {{ $branch->address }}<br>
                        @if($branch->city)
                            {{ $branch->city }}, 
                        @endif
                        @if($branch->state)
                            {{ $branch->state }}
                        @endif
                        @if($branch->postal_code)
                            {{ $branch->postal_code }}
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Contact:</strong><br>
                        @if($branch->phone)
                            <i class="fas fa-phone"></i> {{ $branch->phone }}<br>
                        @endif
                        @if($branch->email)
                            <i class="fas fa-envelope"></i> {{ $branch->email }}
                        @endif
                    </div>
                </div>

                @if($branch->latitude && $branch->longitude)
                    <div class="mb-3">
                        <strong>Location:</strong><br>
                        Latitude: {{ $branch->latitude }}, Longitude: {{ $branch->longitude }}
                    </div>
                @endif

                @if($branch->description)
                    <div class="mb-3">
                        <strong>Description:</strong><br>
                        {{ $branch->description }}
                    </div>
                @endif

                @if($branch->opening_time || $branch->closing_time)
                    <div class="mb-3">
                        <strong>Operating Hours:</strong><br>
                        @if($branch->opening_time && $branch->closing_time)
                            {{ \Carbon\Carbon::parse($branch->opening_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($branch->closing_time)->format('g:i A') }}
                        @endif
                    </div>
                @endif

                @if($branch->manager_name)
                    <hr>
                    <h6>Manager Information</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Name:</strong><br>
                            {{ $branch->manager_name }}
                        </div>
                        @if($branch->manager_phone)
                            <div class="col-md-4">
                                <strong>Phone:</strong><br>
                                {{ $branch->manager_phone }}
                            </div>
                        @endif
                        @if($branch->manager_email)
                            <div class="col-md-4">
                                <strong>Email:</strong><br>
                                {{ $branch->manager_email }}
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-3">
                    <span class="badge {{ $branch->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $branch->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Total Orders:</strong>
                    <span class="badge bg-info float-end">{{ $branch->orders_count ?? 0 }}</span>
                </div>
                <div class="mb-3">
                    <strong>Staff Members:</strong>
                    <span class="badge bg-secondary float-end">{{ $branch->staff_count ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-edit me-2"></i>Edit Branch
                </a>
                <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary w-100">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

