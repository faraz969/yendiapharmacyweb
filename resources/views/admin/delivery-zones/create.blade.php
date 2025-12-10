@extends('admin.layouts.app')

@section('title', 'Create Delivery Zone')
@section('page-title', 'Create Delivery Zone')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Create New Delivery Zone</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.delivery-zones.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Zone Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="delivery_fee" class="form-label">Delivery Fee <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control @error('delivery_fee') is-invalid @enderror" id="delivery_fee" name="delivery_fee" value="{{ old('delivery_fee', 0) }}" min="0" required>
                        </div>
                        @error('delivery_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="min_order_amount" class="form-label">Min Order Amount (Free Delivery)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control @error('min_order_amount') is-invalid @enderror" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', 0) }}" min="0">
                        </div>
                        <small class="form-text text-muted">Orders above this amount get free delivery</small>
                        @error('min_order_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="estimated_delivery_hours" class="form-label">Estimated Delivery Hours</label>
                        <input type="number" class="form-control @error('estimated_delivery_hours') is-invalid @enderror" id="estimated_delivery_hours" name="estimated_delivery_hours" value="{{ old('estimated_delivery_hours') }}" min="1">
                        @error('estimated_delivery_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Zone
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

