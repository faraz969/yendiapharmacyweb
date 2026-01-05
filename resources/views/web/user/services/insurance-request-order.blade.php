@extends('web.layouts.app')

@section('title', 'Create Order - Insurance Request')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-shopping-cart me-2" style="color:#dc8423;"></i>Create Order from Insurance Request
    </h2>

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

            <form action="{{ route('user.services.insurance-request-order.store', $request->id) }}" method="POST" id="orderForm">
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
    const deliveryType = document.querySelector('input[name="delivery_type"]:checked').value;
    const deliverySection = document.getElementById('deliverySection');
    const deliveryFeeRow = document.getElementById('deliveryFeeRow');
    
    if (deliveryType === 'delivery') {
        deliverySection.style.display = 'block';
        deliveryFeeRow.style.display = 'flex';
        document.getElementById('delivery_zone_id').required = true;
        document.getElementById('delivery_address').required = !document.getElementById('delivery_address_id').value;
    } else {
        deliverySection.style.display = 'none';
        deliveryFeeRow.style.display = 'none';
        document.getElementById('delivery_zone_id').required = false;
        document.getElementById('delivery_address').required = false;
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
    const deliveryType = document.querySelector('input[name="delivery_type"]:checked')?.value;
    let total = 0;
    
    if (deliveryType === 'delivery') {
        const zoneSelect = document.getElementById('delivery_zone_id');
        const selectedOption = zoneSelect.options[zoneSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            total = parseFloat(selectedOption.dataset.fee || 0);
        }
    }
    
    document.getElementById('deliveryFeeAmount').textContent = '{{ \App\Models\Setting::getCurrencySymbol() }}' + total.toFixed(2);
    document.getElementById('totalAmount').textContent = '{{ \App\Models\Setting::getCurrencySymbol() }}' + total.toFixed(2);
}

document.getElementById('delivery_zone_id').addEventListener('change', updateTotal);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDeliveryForm();
});
</script>
@endpush
@endsection

