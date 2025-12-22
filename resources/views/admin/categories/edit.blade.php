@extends('admin.layouts.app')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Category</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Category Image</label>
                @if($category->image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" style="max-width: 200px; border-radius: 5px;">
                    </div>
                @endif
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Leave empty to keep current image. Max size: 2MB</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Margin Section -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-percent me-2"></i>Pricing Margin</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Set a default margin for products in this category. This can be used to automatically calculate selling prices based on cost prices.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="margin_type" class="form-label">Margin Type</label>
                                <select class="form-control @error('margin_type') is-invalid @enderror" id="margin_type" name="margin_type">
                                    <option value="">None</option>
                                    <option value="fixed" {{ old('margin_type', $category->margin_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                                    <option value="percentage" {{ old('margin_type', $category->margin_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                </select>
                                @error('margin_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="margin_value" class="form-label">Margin Value</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="margin_symbol">$</span>
                                    <input type="number" step="0.01" class="form-control @error('margin_value') is-invalid @enderror" id="margin_value" name="margin_value" value="{{ old('margin_value', $category->margin_value) }}" min="0">
                                </div>
                                @error('margin_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted" id="margin_help">
                                    Enter the margin amount or percentage
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Category
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const marginType = document.getElementById('margin_type');
    const marginSymbol = document.getElementById('margin_symbol');
    const marginHelp = document.getElementById('margin_help');
    
    marginType.addEventListener('change', function() {
        if (this.value === 'fixed') {
            marginSymbol.textContent = '$';
            marginHelp.textContent = 'Enter the fixed amount to add to cost price (e.g., 5.00 adds $5.00)';
        } else if (this.value === 'percentage') {
            marginSymbol.textContent = '%';
            marginHelp.textContent = 'Enter the percentage to add to cost price (e.g., 20 adds 20%)';
        } else {
            marginSymbol.textContent = '$';
            marginHelp.textContent = 'Enter the margin amount or percentage';
        }
    });
    
    // Trigger on page load if value is already set
    if (marginType.value) {
        marginType.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection

