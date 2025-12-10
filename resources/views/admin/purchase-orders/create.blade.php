@extends('admin.layouts.app')

@section('title', 'Create Purchase Order')
@section('page-title', 'Create Purchase Order')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Create Purchase Order</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.purchase-orders.store') }}" method="POST" id="poForm">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="vendor_id" class="form-label">Vendor <span class="text-danger">*</span></label>
                    <select class="form-select @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id" required>
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('order_date') is-invalid @enderror" id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                    @error('order_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="expected_delivery_date" class="form-label">Expected Delivery</label>
                    <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}">
                    @error('expected_delivery_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Items</h6>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                        <i class="fas fa-plus me-2"></i>Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="items-container">
                        <!-- Items will be added here dynamically -->
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Purchase Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemCount = 0;

function addItem() {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="row mb-3 item-row" data-index="${itemCount}">
            <div class="col-md-5">
                <select class="form-select product-select" name="items[${itemCount}][product_id]" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-unit="{{ $product->purchase_unit }}">
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="items[${itemCount}][quantity]" placeholder="Qty" min="1" required>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" name="items[${itemCount}][unit_cost]" placeholder="Cost" min="0" required>
                </div>
            </div>
            <div class="col-md-2">
                <span class="unit-display text-muted"></span>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemCount++;
    
    // Add event listener for product selection
    const select = container.querySelector(`[data-index="${itemCount - 1}"] .product-select`);
    select.addEventListener('change', function() {
        const unit = this.options[this.selectedIndex].dataset.unit;
        const row = this.closest('.item-row');
        row.querySelector('.unit-display').textContent = unit || '';
    });
}

function removeItem(btn) {
    btn.closest('.item-row').remove();
}

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});
</script>
@endsection

