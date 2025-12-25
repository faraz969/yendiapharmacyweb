@extends('web.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Checkout</h2>

    <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}" required>
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email') }}">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Branch Selection -->
                <div class="card mb-4">
                    <div class="card-header" style="background: var(--green-color); color: white;">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Select Branch</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Choose a branch for order processing <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                <option value="">-- Select Branch --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }} - {{ $branch->city ?? $branch->address }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Select the branch nearest to your delivery address</small>
                        </div>
                    </div>
                </div>

                <!-- Saved Addresses (if authenticated) -->
                @auth
                    @if($savedAddresses->isNotEmpty())
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Saved Addresses</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($savedAddresses as $address)
                                        <div class="col-md-6 mb-3">
                                            <div class="card {{ $address->is_default ? 'border-primary' : '' }}">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="delivery_address_id" 
                                                               id="address_{{ $address->id }}" value="{{ $address->id }}"
                                                               {{ $address->is_default ? 'checked' : '' }}
                                                               onchange="toggleAddressForm({{ $address->id }})">
                                                        <label class="form-check-label" for="address_{{ $address->id }}">
                                                            <strong>{{ $address->label ?: 'Address #' . $address->id }}</strong>
                                                            @if($address->is_default)
                                                                <span class="badge bg-primary ms-2">Default</span>
                                                            @endif
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $address->contact_name }}, {{ $address->contact_phone }}<br>
                                                                {{ $address->full_address }}
                                                            </small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="delivery_address_id" 
                                                           id="address_new" value="" checked
                                                           onchange="toggleAddressForm('new')">
                                                    <label class="form-check-label" for="address_new">
                                                        <strong>Use New Address</strong>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth

                <!-- Delivery Information -->
                <div class="card mb-4" id="deliveryForm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Delivery Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Delivery Zone</label>
                            <select name="delivery_zone_id" class="form-select @error('delivery_zone_id') is-invalid @enderror" id="delivery_zone">
                                <option value="">Select Delivery Zone</option>
                                @foreach($deliveryZones as $zone)
                                    <option value="{{ $zone->id }}" data-fee="{{ $zone->delivery_fee }}" data-min="{{ $zone->min_order_amount ?? 0 }}">
                                        {{ $zone->name }} - {{ \App\Models\Setting::formatPrice($zone->delivery_fee) }} 
                                        @if($zone->min_order_amount)
                                            (Free delivery over {{ \App\Models\Setting::formatPrice($zone->min_order_amount) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('delivery_zone_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Delivery Address <span class="text-danger">*</span></label>
                            <textarea name="delivery_address" class="form-control @error('delivery_address') is-invalid @enderror" rows="3" required>{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Latitude (Optional)</label>
                                    <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Longitude (Optional)</label>
                                    <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prescription Upload -->
                @if($requiresPrescription)
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-prescription me-2"></i>Prescription Required</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Your order contains prescription medications. Please upload a valid prescription.
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Doctor Name</label>
                                        <input type="text" name="doctor_name" class="form-control" value="{{ old('doctor_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Patient Name</label>
                                        <input type="text" name="patient_name" class="form-control" value="{{ old('patient_name') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prescription Date</label>
                                <input type="date" name="prescription_date" class="form-control" value="{{ old('prescription_date') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prescription File <span class="text-danger">*</span></label>
                                <input type="file" name="prescription_file" class="form-control @error('prescription_file') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="form-text text-muted">Upload PDF or image (Max 5MB)</small>
                                @error('prescription_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Order Items -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item['product']->name }}</strong>
                                                @if($item['product']->requires_prescription)
                                                    <span class="badge-prescription ms-2">Rx</span>
                                                @endif
                                            </td>
                                            <td>{{ $item['quantity'] }}</td>
                                            <td>{{ \App\Models\Setting::formatPrice($item['price']) }}</td>
                                            <td>{{ \App\Models\Setting::formatPrice($item['total']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span class="fw-bold">{{ \App\Models\Setting::getCurrencySymbol() }}<span id="subtotal">{{ number_format($subtotal, 2) }}</span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Delivery Fee:</span>
                            <span class="fw-bold">{{ \App\Models\Setting::getCurrencySymbol() }}<span id="delivery_fee">{{ number_format($deliveryFee, 2) }}</span></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold text-primary fs-4">{{ \App\Models\Setting::getCurrencySymbol() }}<span id="total">{{ number_format($total, 2) }}</span></span>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-check me-2"></i>Place Order
                        </button>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('delivery_zone').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const fee = parseFloat(selected.dataset.fee || 0);
    const minOrder = parseFloat(selected.dataset.min || 0);
    const subtotal = parseFloat({{ $subtotal }});
    
    let deliveryFee = fee;
    if (minOrder > 0 && subtotal >= minOrder) {
        deliveryFee = 0;
    }
    
    document.getElementById('delivery_fee').textContent = deliveryFee.toFixed(2);
    document.getElementById('total').textContent = (subtotal + deliveryFee).toFixed(2);
});
</script>
@endpush
@endsection

