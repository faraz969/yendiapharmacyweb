@extends('admin.layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Product</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-images me-2"></i>Media</h6>
                </div>
                <div class="card-body">
                    @if($product->images && is_array($product->images) && count($product->images) > 0)
                        <div class="mb-3">
                            <label class="form-label">Current Images</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($product->images as $index => $image)
                                    <div class="position-relative" style="width: 150px;">
                                        <img src="{{ Storage::url($image) }}" alt="Product Image" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                                        <input type="checkbox" name="remove_images[]" value="{{ $image }}" class="position-absolute top-0 end-0 m-1" title="Remove">
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Check images to remove</small>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="images" class="form-label">Add New Images (Max 5)</label>
                        <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple>
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Max size: 2MB per image</small>
                    </div>

                    @if($product->video)
                        <div class="mb-3">
                            <label class="form-label">Current Video</label>
                            <div>
                                <video controls style="max-width: 300px; max-height: 200px;">
                                    <source src="{{ Storage::url($product->video) }}" type="video/mp4">
                                </video>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="video" class="form-label">Replace Video</label>
                        <input type="file" class="form-control @error('video') is-invalid @enderror" id="video" name="video" accept="video/*">
                        @error('video')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Leave empty to keep current video</small>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Pricing</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="selling_price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ \App\Models\Setting::getCurrencySymbol() }}</span>
                                    <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required>
                                </div>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label">Cost Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ \App\Models\Setting::getCurrencySymbol() }}</span>
                                    <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required>
                                </div>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount" class="form-label">Discount Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ \App\Models\Setting::getCurrencySymbol() }}</span>
                                    <input type="number" step="0.01" class="form-control @error('discount') is-invalid @enderror" id="discount" name="discount" value="{{ old('discount', $product->discount) }}" min="0">
                                </div>
                                @error('discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Enter discount amount (e.g., 10.00 for {{ \App\Models\Setting::getCurrencySymbol() }}10 off)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unit Conversion -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Unit Conversion</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="purchase_unit" class="form-label">Purchase Unit <span class="text-danger">*</span></label>
                                <select class="form-select @error('purchase_unit') is-invalid @enderror" id="purchase_unit" name="purchase_unit" required>
                                    <option value="box" {{ old('purchase_unit', $product->purchase_unit) == 'box' ? 'selected' : '' }}>Box</option>
                                    <option value="pack" {{ old('purchase_unit', $product->purchase_unit) == 'pack' ? 'selected' : '' }}>Pack</option>
                                    <option value="bottle" {{ old('purchase_unit', $product->purchase_unit) == 'bottle' ? 'selected' : '' }}>Bottle</option>
                                    <option value="piece" {{ old('purchase_unit', $product->purchase_unit) == 'piece' ? 'selected' : '' }}>Piece</option>
                                </select>
                                @error('purchase_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="selling_unit" class="form-label">Selling Unit <span class="text-danger">*</span></label>
                                <select class="form-select @error('selling_unit') is-invalid @enderror" id="selling_unit" name="selling_unit" required>
                                    <option value="tablet" {{ old('selling_unit', $product->selling_unit) == 'tablet' ? 'selected' : '' }}>Tablet</option>
                                    <option value="capsule" {{ old('selling_unit', $product->selling_unit) == 'capsule' ? 'selected' : '' }}>Capsule</option>
                                    <option value="ml" {{ old('selling_unit', $product->selling_unit) == 'ml' ? 'selected' : '' }}>ML</option>
                                    <option value="piece" {{ old('selling_unit', $product->selling_unit) == 'piece' ? 'selected' : '' }}>Piece</option>
                                </select>
                                @error('selling_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="conversion_factor" class="form-label">Conversion Factor <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('conversion_factor') is-invalid @enderror" id="conversion_factor" name="conversion_factor" value="{{ old('conversion_factor', $product->conversion_factor) }}" min="1" required>
                                <small class="form-text text-muted">e.g., 1 box = 10 tablets</small>
                                @error('conversion_factor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescription -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-prescription me-2"></i>Prescription Requirements</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="requires_prescription" name="requires_prescription" {{ old('requires_prescription', $product->requires_prescription) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_prescription">Requires Prescription</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="prescription_notes" class="form-label">Prescription Notes</label>
                        <textarea class="form-control @error('prescription_notes') is-invalid @enderror" id="prescription_notes" name="prescription_notes" rows="2">{{ old('prescription_notes', $product->prescription_notes) }}</textarea>
                        @error('prescription_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Inventory -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-warehouse me-2"></i>Inventory Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="min_stock_level" class="form-label">Min Stock Level</label>
                                <input type="number" class="form-control @error('min_stock_level') is-invalid @enderror" id="min_stock_level" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" min="0">
                                @error('min_stock_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="max_stock_level" class="form-label">Max Stock Level</label>
                                <input type="number" class="form-control @error('max_stock_level') is-invalid @enderror" id="max_stock_level" name="max_stock_level" value="{{ old('max_stock_level', $product->max_stock_level) }}" min="0">
                                @error('max_stock_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="track_expiry" name="track_expiry" {{ old('track_expiry', $product->track_expiry) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="track_expiry">Track Expiry</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="track_batch" name="track_batch" {{ old('track_batch', $product->track_batch) ? 'checked' : '' }}>
                            <label class="form-check-label" for="track_batch">Track Batch</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-toggle-on me-2"></i>Status</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for category dropdown
    $('#category_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select Category',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
@endsection

