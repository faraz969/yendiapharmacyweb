@extends('admin.layouts.app')

@section('title', 'Import Purchase Orders')
@section('page-title', 'Import Purchase Orders from CSV')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-file-csv me-2"></i>Import Purchase Orders from CSV</h5>
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
                        <form action="{{ route('admin.purchase-orders.import.store') }}" method="POST" enctype="multipart/form-data">
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
                                    <i class="fas fa-upload me-2"></i>Import Purchase Orders
                                </button>
                                <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary">
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
                                <li><code>vendor_id</code> - Vendor ID (must exist and be active)</li>
                                <li><code>order_date</code> - Order date (YYYY-MM-DD format)</li>
                                <li><code>product_sku</code> or <code>product_id</code> - Product identifier (at least one required)</li>
                                <li><code>quantity</code> - Quantity (positive integer)</li>
                                <li><code>unit_cost</code> - Unit cost (non-negative number)</li>
                            </ul>
                        </div>
                        <div class="alert alert-info">
                            <strong>Optional Columns:</strong>
                            <ul class="mb-0 mt-2">
                                <li><code>expected_delivery_date</code> - Expected delivery date (YYYY-MM-DD)</li>
                                <li><code>notes</code> - Purchase order notes</li>
                            </ul>
                        </div>
                        <div class="alert alert-warning">
                            <strong>How it works:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Rows with the same vendor_id, order_date, expected_delivery_date, and notes are grouped into a single purchase order</li>
                                <li>Each group creates one purchase order with multiple line items</li>
                                <li>Example: 5 rows with same vendor/date → 1 purchase order with 5 items</li>
                                <li>Empty rows are skipped</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-download me-2"></i>Download Template</h6>
                        <p class="text-muted">Download a sample CSV template with the required columns and example data.</p>
                        <a href="{{ route('admin.purchase-orders.import.template') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-download me-2"></i>Download Template
                        </a>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-building me-2"></i>Active Vendors</h6>
                        <ul class="list-unstyled mb-0" style="max-height: 200px; overflow-y: auto;">
                            @forelse($vendors as $vendor)
                                <li><i class="fas fa-warehouse me-2 text-primary"></i>{{ $vendor->name }} (ID: {{ $vendor->id }})</li>
                            @empty
                                <li class="text-muted">No vendors found. <a href="{{ route('admin.vendors.create') }}">Create one</a></li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-box me-2"></i>Sample Product SKUs</h6>
                        <ul class="list-unstyled mb-0" style="max-height: 200px; overflow-y: auto;">
                            @forelse($products->take(10) as $product)
                                <li><code>{{ $product->sku }}</code> - {{ Str::limit($product->name, 25) }}</li>
                            @empty
                                <li class="text-muted">No products found.</li>
                            @endforelse
                            @if($products->count() > 10)
                                <li class="text-muted"><small>... and {{ $products->count() - 10 }} more</small></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
