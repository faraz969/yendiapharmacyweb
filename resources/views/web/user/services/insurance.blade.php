@extends('web.layouts.app')

@section('title', 'Insurance Request')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-shield-alt me-2" style="color:#dc8423;"></i>Insurance Request
    </h2>

    <div class="row">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('user.services.insurance.store') }}" method="POST" enctype="multipart/form-data" id="insuranceForm">
                        @csrf

                        <!-- Insurance Company -->
                        <div class="mb-4">
                            <h5 class="mb-3">1. Select Your Insurance Company</h5>
                            <select name="insurance_company_id" class="form-select @error('insurance_company_id') is-invalid @enderror" required>
                                <option value="">Select Insurance Company...</option>
                                @foreach($insuranceCompanies as $company)
                                    <option value="{{ $company->id }}" {{ old('insurance_company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('insurance_company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Personal Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">2. Personal Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" value="{{ old('customer_name', Auth::user()->name) }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_phone" class="form-label">Telephone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                           id="customer_phone" name="customer_phone" value="{{ old('customer_phone', Auth::user()->phone ?? '') }}" required>
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                           id="customer_email" name="customer_email" value="{{ old('customer_email', Auth::user()->email) }}">
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="insurance_number" class="form-label">Insurance Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('insurance_number') is-invalid @enderror" 
                                           id="insurance_number" name="insurance_number" value="{{ old('insurance_number') }}" required>
                                    @error('insurance_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Branch Selection -->
                        <div class="mb-4">
                            <h5 class="mb-3">3. Select Branch</h5>
                            <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                <option value="">Select Branch...</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Insurance Card Images -->
                        <div class="mb-4">
                            <h5 class="mb-3">4. Insurance Card Images</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="card_front_image" class="form-label">Front of Card <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('card_front_image') is-invalid @enderror" 
                                           id="card_front_image" name="card_front_image" accept="image/*" required>
                                    @error('card_front_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Take a picture or upload image</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="card_back_image" class="form-label">Back of Card <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('card_back_image') is-invalid @enderror" 
                                           id="card_back_image" name="card_back_image" accept="image/*" required>
                                    @error('card_back_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Take a picture or upload image</small>
                                </div>
                            </div>
                        </div>

                        <!-- Prescription -->
                        <div class="mb-4">
                            <h5 class="mb-3">5. Upload Prescription (Optional)</h5>
                            <input type="file" class="form-control @error('prescription_image') is-invalid @enderror" 
                                   id="prescription_image" name="prescription_image" accept="image/*">
                            @error('prescription_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Take a picture or upload image</small>
                        </div>

                        <!-- Items -->
                        <div class="mb-4">
                            <h5 class="mb-3">6. Enter Your Items</h5>
                            <div id="itemsContainer">
                                <div class="item-row mb-3 border p-3 rounded">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                            <select class="form-control product-select" name="items[0][product_id]" data-item-index="0" style="width: 100%;">
                                                <option value="">Search or type custom product...</option>
                                            </select>
                                            <input type="text" class="form-control custom-product-input mt-2" name="items[0][product_name]" placeholder="Or enter custom product name" required>
                                            <small class="text-muted">Select from our products or enter a custom product name</small>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="items[0][quantity]" value="1" min="1" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Notes</label>
                                            <input type="text" class="form-control" name="items[0][notes]">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-item" style="display:none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" id="addItemBtn">
                                <i class="fas fa-plus me-2"></i>Add More Items
                            </button>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let itemCount = 1;
let products = [];

// Fetch products for dropdown
fetch('{{ url("/api/products") }}?per_page=1000')
    .then(response => response.json())
    .then(data => {
        products = data.data.data || [];
        initializeSelect2();
    })
    .catch(error => {
        console.error('Error loading products:', error);
        initializeSelect2();
    });

function initializeSelect2() {
    document.querySelectorAll('.product-select').forEach(select => {
        if (!$(select).hasClass('select2-hidden-accessible')) {
            $(select).select2({
                placeholder: 'Search or type custom product...',
                allowClear: true,
                tags: true,
                data: products.map(p => ({
                    id: p.id,
                    text: p.name
                }))
            }).on('select2:select', function(e) {
                const itemIndex = $(this).data('item-index');
                const customInput = document.querySelector(`input[name="items[${itemIndex}][product_name]"]`);
                if (e.params.data.id && typeof e.params.data.id === 'number') {
                    // Product selected from dropdown - set product_name and hide input
                    customInput.value = e.params.data.text;
                    customInput.style.display = 'none';
                    customInput.removeAttribute('required');
                } else {
                    // Custom text entered - show input and clear select
                    customInput.value = e.params.data.text;
                    customInput.style.display = 'block';
                    customInput.setAttribute('required', 'required');
                    $(this).val(null).trigger('change');
                }
            }).on('select2:clear', function() {
                const itemIndex = $(this).data('item-index');
                const customInput = document.querySelector(`input[name="items[${itemIndex}][product_name]"]`);
                customInput.value = '';
                customInput.style.display = 'block';
                customInput.setAttribute('required', 'required');
            });
        }
    });
}

document.getElementById('addItemBtn').addEventListener('click', function() {
    const container = document.getElementById('itemsContainer');
    const newItem = document.createElement('div');
    newItem.className = 'item-row mb-3 border p-3 rounded';
    newItem.innerHTML = `
        <div class="row">
            <div class="col-md-5">
                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                <select class="form-control product-select" name="items[${itemCount}][product_id]" data-item-index="${itemCount}" style="width: 100%;">
                    <option value="">Search or type custom product...</option>
                </select>
                <input type="text" class="form-control custom-product-input mt-2" name="items[${itemCount}][product_name]" placeholder="Or enter custom product name" required>
                <small class="text-muted">Select from our products or enter a custom product name</small>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="items[${itemCount}][quantity]" value="1" min="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Notes</label>
                <input type="text" class="form-control" name="items[${itemCount}][notes]">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    
    // Initialize Select2 for the new item
    setTimeout(() => {
        initializeSelect2();
    }, 100);
    
    itemCount++;
    
    // Show remove buttons for all items
    document.querySelectorAll('.remove-item').forEach(btn => btn.style.display = 'block');
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const itemRow = e.target.closest('.item-row');
        const select = itemRow.querySelector('.product-select');
        if ($(select).hasClass('select2-hidden-accessible')) {
            $(select).select2('destroy');
        }
        itemRow.remove();
        // Hide remove buttons if only one item left
        if (document.querySelectorAll('.item-row').length === 1) {
            document.querySelectorAll('.remove-item').forEach(btn => btn.style.display = 'none');
        }
    }
});

// Form validation - ensure product_name is always provided
document.getElementById('insuranceForm').addEventListener('submit', function(e) {
    let isValid = true;
    const errors = [];
    
    document.querySelectorAll('.item-row').forEach((row, index) => {
        const productSelect = row.querySelector(`select[name="items[${index}][product_id]"]`);
        const productNameInput = row.querySelector(`input[name="items[${index}][product_name]"]`);
        
        if (!productSelect || !productNameInput) return;
        
        const productId = productSelect.value;
        let productName = productNameInput.value.trim();
        
        // If product_id is selected but product_name is empty, get it from the select
        if (productId && !productName) {
            const selectedOption = productSelect.querySelector(`option[value="${productId}"]`);
            if (selectedOption) {
                productName = selectedOption.textContent.trim();
                productNameInput.value = productName;
            }
        }
        
        // Ensure product_name is always set
        if (!productName) {
            isValid = false;
            errors.push(`Item ${index + 1}: Please select a product or enter a custom product name.`);
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert(errors.join('\n'));
        return false;
    }
    
    // Allow form to submit normally
    return true;
});
</script>
@endpush
@endsection

