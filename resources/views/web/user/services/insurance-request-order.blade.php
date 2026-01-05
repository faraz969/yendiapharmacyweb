@extends('web.layouts.app')

@section('title', 'Create Order - Insurance Request')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-shopping-cart me-2" style="color:#dc8423;"></i>Create Order from Insurance Request
    </h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header" style="background-color:#dc8423; color: white;">
                    <h5 class="mb-0">Request #{{ $request->request_number }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Insurance Company:</strong> {{ $request->insuranceCompany->name }}</p>
                    <p><strong>Items:</strong> {{ $request->items->count() }} item(s)</p>
                    <p class="text-success"><strong>Note:</strong> Items are covered by insurance. You only pay the delivery fee.</p>
                </div>
            </div>

            <form action="{{ route('user.services.insurance-request-order.store', $request->id) }}" method="POST" id="orderForm" onsubmit="return validateForm()">
                @csrf

                <!-- Delivery Type Selection -->
                <div class="card mb-4">
                    <div class="card-header text-white" style="background-color:#dc8423;">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Delivery Type</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 {{ old('delivery_type', 'delivery') == 'delivery' ? 'border-primary' : '' }}" style="cursor: pointer;" onclick="selectDeliveryType('delivery')">
                                    <div class="card-body text-center">
                                        <input type="radio" name="delivery_type" id="delivery_type_delivery" value="delivery" 
                                               {{ old('delivery_type', 'delivery') == 'delivery' ? 'checked' : '' }} 
                                               onchange="toggleDeliveryForm()" style="display: none;">
                                        <i class="fas fa-truck fa-3x mb-3" style="color: #158D43;"></i>
                                        <h5>Delivery</h5>
                                        <p class="text-muted mb-0">We'll deliver to your address</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 {{ old('delivery_type', 'delivery') == 'pickup' ? 'border-primary' : '' }}" style="cursor: pointer;" onclick="selectDeliveryType('pickup')">
                                    <div class="card-body text-center">
                                        <input type="radio" name="delivery_type" id="delivery_type_pickup" value="pickup" 
                                               {{ old('delivery_type', 'delivery') == 'pickup' ? 'checked' : '' }} 
                                               onchange="toggleDeliveryForm()" style="display: none;">
                                        <i class="fas fa-hand-holding fa-3x mb-3" style="color: #158D43;"></i>
                                        <h5>Pickup</h5>
                                        <p class="text-muted mb-0">Collect from the branch</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Address Section -->
                <div id="deliverySection" style="display: {{ old('delivery_type', 'delivery') == 'delivery' ? 'block' : 'none' }};">
                    <div class="card mb-4">
                        <div class="card-header text-white" style="background-color:#dc8423;">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Delivery Address</h5>
                        </div>
                        <div class="card-body">
                            @if($addresses->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label">Select Saved Address</label>
                                    <select name="delivery_address_id" id="delivery_address_id" class="form-select" onchange="toggleAddressInput()">
                                        <option value="">-- Select Saved Address --</option>
                                        @foreach($addresses as $address)
                                            <option value="{{ $address->id }}" data-zone="{{ $address->delivery_zone_id }}">
                                                {{ $address->address }} - {{ $address->deliveryZone->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="text-center mb-3">
                                    <span class="text-muted">OR</span>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Delivery Zone <span class="text-danger">*</span></label>
                                <select name="delivery_zone_id" id="delivery_zone_id" class="form-select @error('delivery_zone_id') is-invalid @enderror" required>
                                    <option value="">-- Select Delivery Zone --</option>
                                    @foreach($deliveryZones as $zone)
                                        <option value="{{ $zone->id }}" data-fee="{{ $zone->delivery_fee }}">
                                            {{ $zone->name }} - {{ \App\Models\Setting::formatPrice($zone->delivery_fee) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('delivery_zone_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="addressInputSection">
                                <label class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                <textarea name="delivery_address" id="delivery_address" class="form-control @error('delivery_address') is-invalid @enderror" rows="3" placeholder="Enter your delivery address">{{ old('delivery_address') }}</textarea>
                                @error('delivery_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card mb-4">
                    <div class="card-header text-white" style="background-color:#dc8423;">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items ({{ $request->items->count() }}):</span>
                            <span class="text-success"><strong>FREE (Covered by Insurance)</strong></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="deliveryFeeRow" style="display: {{ old('delivery_type', 'delivery') == 'delivery' ? 'flex' : 'none' }};">
                            <span>Delivery Fee:</span>
                            <span id="deliveryFeeAmount">{{ \App\Models\Setting::formatPrice(0) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="totalAmount">{{ \App\Models\Setting::formatPrice(0) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('user.services.insurance-requests.show', $request->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Create Order & Proceed to Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function selectDeliveryType(type) {
    document.getElementById('delivery_type_' + type).checked = true;
    document.querySelectorAll('[onclick*="selectDeliveryType"]').forEach(card => {
        card.classList.remove('border-primary');
    });
    event.currentTarget.classList.add('border-primary');
    toggleDeliveryForm();
}

function toggleDeliveryForm() {
    const deliveryTypeRadio = document.querySelector('input[name="delivery_type"]:checked');
    if (!deliveryTypeRadio) {
        return; // Exit if no delivery type is selected
    }
    
    const deliveryType = deliveryTypeRadio.value;
    const deliverySection = document.getElementById('deliverySection');
    const deliveryFeeRow = document.getElementById('deliveryFeeRow');
    const deliveryZoneId = document.getElementById('delivery_zone_id');
    const deliveryAddress = document.getElementById('delivery_address');
    const deliveryAddressId = document.getElementById('delivery_address_id');
    
    if (deliveryType === 'delivery') {
        if (deliverySection) deliverySection.style.display = 'block';
        if (deliveryFeeRow) deliveryFeeRow.style.display = 'flex';
        if (deliveryZoneId) deliveryZoneId.required = true;
        if (deliveryAddress) {
            deliveryAddress.required = !(deliveryAddressId && deliveryAddressId.value);
        }
    } else {
        if (deliverySection) deliverySection.style.display = 'none';
        if (deliveryFeeRow) deliveryFeeRow.style.display = 'none';
        if (deliveryZoneId) deliveryZoneId.required = false;
        if (deliveryAddress) deliveryAddress.required = false;
    }
    updateTotal();
}

function toggleAddressInput() {
    const addressId = document.getElementById('delivery_address_id').value;
    const addressInput = document.getElementById('delivery_address');
    
    if (addressId) {
        addressInput.required = false;
        addressInput.value = '';
        const selectedOption = document.querySelector(`#delivery_address_id option[value="${addressId}"]`);
        if (selectedOption) {
            document.getElementById('delivery_zone_id').value = selectedOption.dataset.zone || '';
            updateTotal();
        }
    } else {
        addressInput.required = true;
    }
}

function updateTotal() {
    const deliveryTypeRadio = document.querySelector('input[name="delivery_type"]:checked');
    if (!deliveryTypeRadio) {
        return; // Exit if no delivery type is selected
    }
    
    const deliveryType = deliveryTypeRadio.value;
    let total = 0;
    
    if (deliveryType === 'delivery') {
        const zoneSelect = document.getElementById('delivery_zone_id');
        if (zoneSelect && zoneSelect.selectedIndex >= 0) {
            const selectedOption = zoneSelect.options[zoneSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                total = parseFloat(selectedOption.dataset.fee || 0);
            }
        }
    }
    
    const deliveryFeeAmount = document.getElementById('deliveryFeeAmount');
    const totalAmount = document.getElementById('totalAmount');
    if (deliveryFeeAmount) {
        deliveryFeeAmount.textContent = '{{ \App\Models\Setting::getCurrencySymbol() }}' + total.toFixed(2);
    }
    if (totalAmount) {
        totalAmount.textContent = '{{ \App\Models\Setting::getCurrencySymbol() }}' + total.toFixed(2);
    }
}

function validateForm() {
    const deliveryTypeRadio = document.querySelector('input[name="delivery_type"]:checked');
    if (!deliveryTypeRadio) {
        alert('Please select a delivery type.');
        return false;
    }
    
    const deliveryType = deliveryTypeRadio.value;
    
    if (deliveryType === 'delivery') {
        const deliveryZoneId = document.getElementById('delivery_zone_id');
        const deliveryAddressId = document.getElementById('delivery_address_id');
        const deliveryAddress = document.getElementById('delivery_address');
        
        if (!deliveryZoneId || !deliveryZoneId.value) {
            alert('Please select a delivery zone.');
            return false;
        }
        
        if ((!deliveryAddressId || !deliveryAddressId.value) && (!deliveryAddress || !deliveryAddress.value.trim())) {
            alert('Please select a saved address or enter a delivery address.');
            return false;
        }
    }
    
    return true;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const deliveryZoneId = document.getElementById('delivery_zone_id');
    if (deliveryZoneId) {
        deliveryZoneId.addEventListener('change', updateTotal);
    }
    
    // Ensure a delivery type is selected on page load
    const deliveryTypeRadio = document.querySelector('input[name="delivery_type"]:checked');
    if (!deliveryTypeRadio) {
        // If none is checked, check the default (delivery)
        const defaultRadio = document.getElementById('delivery_type_delivery');
        if (defaultRadio) {
            defaultRadio.checked = true;
        }
    }
    
    toggleDeliveryForm();
});
</script>
@endpush
@endsection

