@extends('admin.layouts.app')

@section('title', 'Product Details')
@section('page-title', 'Product Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $product->name }}</h5>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>SKU:</strong> <code>{{ $product->sku }}</code>
                    </div>
                    <div class="col-md-6">
                        <strong>Barcode:</strong> {{ $product->barcode ?? 'N/A' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Category:</strong> {{ $product->category->name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        @if($product->is_expired)
                            <span class="badge bg-danger">Expired</span>
                        @elseif($product->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                @if($product->description)
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p>{{ $product->description }}</p>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Selling Price:</strong> ${{ number_format($product->selling_price, 2) }}
                    </div>
                    <div class="col-md-6">
                        <strong>Cost Price:</strong> ${{ number_format($product->cost_price, 2) }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Purchase Unit:</strong> {{ ucfirst($product->purchase_unit) }}
                    </div>
                    <div class="col-md-4">
                        <strong>Selling Unit:</strong> {{ ucfirst($product->selling_unit) }}
                    </div>
                    <div class="col-md-4">
                        <strong>Conversion:</strong> 1 {{ $product->purchase_unit }} = {{ $product->conversion_factor }} {{ $product->selling_unit }}s
                    </div>
                </div>

                @if($product->requires_prescription)
                    <div class="alert alert-warning">
                        <i class="fas fa-prescription me-2"></i><strong>Requires Prescription</strong>
                        @if($product->prescription_notes)
                            <p class="mb-0 mt-2">{{ $product->prescription_notes }}</p>
                        @endif
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Min Stock:</strong> {{ $product->min_stock_level ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <strong>Max Stock:</strong> {{ $product->max_stock_level ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <strong>Current Stock:</strong>
                        @if($product->track_batch)
                            {{ number_format($product->total_stock) }} {{ $product->selling_unit }}s
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Track Expiry:</strong>
                        @if($product->track_expiry)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Track Batch:</strong>
                        @if($product->track_batch)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($product->batches && $product->batches->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-boxes me-2"></i>Batches</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Batch #</th>
                                    <th>Quantity</th>
                                    <th>Available</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->batches as $batch)
                                    <tr>
                                        <td>{{ $batch->batch_number }}</td>
                                        <td>{{ $batch->quantity }} {{ $product->purchase_unit }}</td>
                                        <td>{{ $batch->available_quantity }} {{ $product->purchase_unit }}</td>
                                        <td>{{ $batch->expiry_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($batch->is_expired)
                                                <span class="badge bg-danger">Expired</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        @if($product->images && is_array($product->images) && count($product->images) > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-images me-2"></i>Images</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($product->images as $image)
                            <div class="col-6">
                                <img src="{{ Storage::url($image) }}" alt="Product Image" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if($product->video)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-video me-2"></i>Video</h6>
                </div>
                <div class="card-body">
                    <video controls style="width: 100%;">
                        <source src="{{ Storage::url($product->video) }}" type="video/mp4">
                    </video>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Products
    </a>
</div>
@endsection

