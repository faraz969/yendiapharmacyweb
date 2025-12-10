@extends('admin.layouts.app')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Order Information -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }} fs-6">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Customer Name:</strong> {{ $order->customer_name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Phone:</strong> {{ $order->customer_phone }}
                    </div>
                </div>
                @if($order->customer_email)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Email:</strong> {{ $order->customer_email }}
                        </div>
                    </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Delivery Address:</strong><br>
                        {{ $order->delivery_address }}
                    </div>
                </div>
                @if($order->deliveryZone)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Delivery Zone:</strong> {{ $order->deliveryZone->name }}
                        </div>
                    </div>
                @endif
                @if($order->notes)
                    <div class="alert alert-info">
                        <strong>Notes:</strong> {{ $order->notes }}
                    </div>
                @endif
                @if($order->rejection_reason)
                    <div class="alert alert-danger">
                        <strong>Rejection Reason:</strong> {{ $order->rejection_reason }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Items</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong>
                                        @if($item->product->requires_prescription)
                                            <span class="badge bg-warning text-dark">Rx</span>
                                        @endif
                                    </td>
                                    <td><code>{{ $item->product->sku }}</code></td>
                                    <td>{{ $item->quantity }} {{ $item->product->selling_unit }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td><strong>${{ number_format($order->subtotal, 2) }}</strong></td>
                            </tr>
                            @if($order->delivery_fee > 0)
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Delivery Fee:</strong></td>
                                    <td><strong>${{ number_format($order->delivery_fee, 2) }}</strong></td>
                                </tr>
                            @endif
                            @if($order->discount > 0)
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                                    <td><strong>-${{ number_format($order->discount, 2) }}</strong></td>
                                </tr>
                            @endif
                            <tr class="table-primary">
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Prescription -->
        @if($order->prescription)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-prescription me-2"></i>Prescription</h6>
                </div>
                <div class="card-body">
                    <p><strong>Prescription #:</strong> {{ $order->prescription->prescription_number ?? 'N/A' }}</p>
                    @if($order->prescription->doctor_name)
                        <p><strong>Doctor:</strong> {{ $order->prescription->doctor_name }}</p>
                    @endif
                    @if($order->prescription->file_path)
                        <a href="{{ Storage::url($order->prescription->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-file-pdf me-2"></i>View Prescription
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Order Actions -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
            </div>
            <div class="card-body">
                @if($order->status === 'pending')
                    <!-- Approve/Reject -->
                    <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <label for="approve_notes" class="form-label">Notes (optional)</label>
                            <textarea name="notes" id="approve_notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-2"></i>Approve Order
                        </button>
                    </form>

                    <form action="{{ route('admin.orders.reject', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i>Reject Order
                        </button>
                    </form>
                @elseif($order->status === 'approved' || $order->status === 'packing')
                    <!-- Pack -->
                    <form action="{{ route('admin.orders.pack', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-box me-2"></i>Mark as Packed
                        </button>
                    </form>
                @elseif($order->status === 'packed')
                    <!-- Assign Delivery -->
                    <form action="{{ route('admin.orders.deliver', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="delivery_person_id" class="form-label">Delivery Person <span class="text-danger">*</span></label>
                            <select name="delivery_person_id" id="delivery_person_id" class="form-select" required>
                                <option value="">Select Delivery Person</option>
                                @php
                                    $deliveryPersons = \App\Models\User::role('delivery_person')->get();
                                @endphp
                                @foreach($deliveryPersons as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-truck me-2"></i>Assign for Delivery
                        </button>
                    </form>
                @elseif($order->status === 'out_for_delivery')
                    <!-- Mark Delivered -->
                    <form action="{{ route('admin.orders.mark-delivered', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check-circle me-2"></i>Mark as Delivered
                        </button>
                    </form>
                @endif

                <!-- Status Update -->
                <hr>
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label for="status" class="form-label">Change Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $order->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $order->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="packing" {{ $order->status === 'packing' ? 'selected' : '' }}>Packing</option>
                            <option value="packed" {{ $order->status === 'packed' ? 'selected' : '' }}>Packed</option>
                            <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-sync me-2"></i>Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <strong>Created:</strong><br>
                        {{ $order->created_at->format('M d, Y H:i') }}
                    </li>
                    @if($order->approved_at)
                        <li class="mb-2">
                            <strong>Approved:</strong><br>
                            {{ $order->approved_at->format('M d, Y H:i') }}
                            @if($order->approvedBy)
                                <br><small class="text-muted">by {{ $order->approvedBy->name }}</small>
                            @endif
                        </li>
                    @endif
                    @if($order->packed_at)
                        <li class="mb-2">
                            <strong>Packed:</strong><br>
                            {{ $order->packed_at->format('M d, Y H:i') }}
                            @if($order->packedBy)
                                <br><small class="text-muted">by {{ $order->packedBy->name }}</small>
                            @endif
                        </li>
                    @endif
                    @if($order->delivered_at)
                        <li class="mb-2">
                            <strong>Delivered:</strong><br>
                            {{ $order->delivered_at->format('M d, Y H:i') }}
                            @if($order->deliveredBy)
                                <br><small class="text-muted">by {{ $order->deliveredBy->name }}</small>
                            @endif
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Orders
    </a>
</div>
@endsection

