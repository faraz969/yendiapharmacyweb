@extends('admin.layouts.app')

@section('title', 'Import Products')
@section('page-title', 'Import Products from CSV')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-file-csv me-2"></i>Import Products from CSV</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('import_errors') && is_array(session('import_errors')) && count(session('import_errors')) > 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle me-2"></i>Import completed with errors ({{ count(session('import_errors')) }} errors):</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <ul class="mb-0 mt-2" style="max-height: 400px; overflow-y: auto; font-size: 0.9rem;">
                    @foreach(session('import_errors') as $error)
                        <li class="mb-1">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-times-circle me-2"></i>Validation Errors:</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-upload me-2"></i>Upload CSV File</h6>
                        <form action="{{ route('admin.products.import.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="csv_file" class="form-label">CSV File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('csv_file') is-invalid @enderror" 
                                       id="csv_file" name="csv_file" accept=".csv,.txt" required>
                                @error('csv_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Maximum file size: 10MB. Supported formats: CSV, TXT
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Import Products
                                </button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>CSV Format Instructions</h6>
                        <div class="alert alert-info">
                            <strong>Required Columns:</strong>
                            <ul class="mb-0 mt-2">
                                <li><code>category_name</code> - Category name (must exist in system)</li>
                                <li><code>name</code> - Product name (required)</li>
                                <li><code>selling_price</code> - Selling price (required, must be > 0)</li>
                            </ul>
                        </div>
                        <div class="alert alert-info">
                            <strong>Optional Columns:</strong>
                            <ul class="mb-0 mt-2">
                                <li><code>description</code> - Product description</li>
                                <li><code>sku</code> - SKU (auto-generated if not provided)</li>
                                <li><code>barcode</code> - Barcode</li>
                                <li><code>cost_price</code> - Cost price (default: 0)</li>
                                <li><code>discount</code> - Discount amount (default: 0)</li>
                                <li><code>purchase_unit</code> - Purchase unit: box, pack, bottle, piece (default: box)</li>
                                <li><code>selling_unit</code> - Selling unit: tablet, capsule, ml, piece (default: tablet)</li>
                                <li><code>conversion_factor</code> - Conversion factor (default: 1)</li>
                                <li><code>requires_prescription</code> - true/false (default: false)</li>
                                <li><code>prescription_notes</code> - Prescription notes</li>
                                <li><code>min_stock_level</code> - Minimum stock level (default: 0)</li>
                                <li><code>max_stock_level</code> - Maximum stock level</li>
                                <li><code>track_expiry</code> - true/false (default: true)</li>
                                <li><code>track_batch</code> - true/false (default: true)</li>
                                <li><code>is_active</code> - true/false (default: true)</li>
                            </ul>
                        </div>
                        <div class="alert alert-warning">
                            <strong>Important Notes:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Category names must match exactly with existing categories in the system</li>
                                <li>SKU and barcode must be unique (if provided)</li>
                                <li>Boolean values can be: true, false, 1, 0, yes, no, y, n, on, off</li>
                                <li>Empty rows will be skipped</li>
                                <li>If SKU is not provided, it will be auto-generated</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-download me-2"></i>Download Template</h6>
                        <p class="text-muted">Download a sample CSV template with all columns and example data.</p>
                        <a href="{{ route('admin.products.import.template') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-download me-2"></i>Download Template
                        </a>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-list me-2"></i>Available Categories</h6>
                        <ul class="list-unstyled mb-0">
                            @forelse($categories as $category)
                                <li><i class="fas fa-folder me-2 text-primary"></i>{{ $category->name }}</li>
                            @empty
                                <li class="text-muted">No categories found. <a href="{{ route('admin.categories.create') }}">Create one</a></li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

