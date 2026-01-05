@extends('admin.layouts.app')

@section('title', 'Create Insurance Company')
@section('page-title', 'Create Insurance Company')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Create New Insurance Company</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.insurance-companies.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.insurance-companies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Company
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

