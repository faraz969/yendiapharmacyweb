@extends('admin.layouts.app')

@section('title', 'Edit Unit')
@section('page-title', 'Edit Unit')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Product Unit</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.product-units.update', $productUnit->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $productUnit->name) }}" required>
                <small class="form-text text-muted">This is what you will see in the product form dropdowns.</small>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Used For <span class="text-danger">*</span></label>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="purchase" {{ old('type', $productUnit->type) == 'purchase' ? 'selected' : '' }}>Purchase only</option>
                    <option value="selling" {{ old('type', $productUnit->type) == 'selling' ? 'selected' : '' }}>Selling only</option>
                    <option value="both" {{ old('type', $productUnit->type) == 'both' ? 'selected' : '' }}>Both purchase &amp; selling</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $productUnit->sort_order) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $productUnit->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.product-units.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
