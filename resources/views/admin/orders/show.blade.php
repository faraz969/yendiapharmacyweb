@extends('admin.layouts.app')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('content')
@if(Auth::user()->isBranchStaff() && $order->branch)
    <div class="alert alert-info mb-4">
        <i class="fas fa-building me-2"></i>
        <strong>Branch:</strong> {{ $order->branch->name }}
    </div>
@endif
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
                        <strong>Order Type:</strong>
                        @if(($order->delivery_type ?? 'delivery') === 'pickup')
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-hand-holding"></i> Pickup
                            </span>
                        @else
                            <span class="badge bg-primary">
                                <i class="fas fa-truck"></i> Delivery
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($order->branch)
                            <strong>Branch:</strong> {{ $order->branch->name }}
                        @endif
                    </div>
                </div>
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
                @if(($order->delivery_type ?? 'delivery') === 'pickup')
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-hand-holding me-2"></i>
                        <strong>Pickup Order:</strong> Customer will collect this order from the branch.
                        @if($order->branch)
                            <br><strong>Pickup Location:</strong> {{ $order->branch->name }}
                            @if($order->branch->address)
                                <br>{{ $order->branch->address }}
                            @endif
                        @endif
                    </div>
                @else
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
                                    <td>{{ \App\Models\Setting::formatPrice($item->unit_price) }}</td>
                                    <td>{{ \App\Models\Setting::formatPrice($item->total_price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td><strong>{{ \App\Models\Setting::formatPrice($order->subtotal) }}</strong></td>
                            </tr>
                            @if($order->delivery_fee > 0)
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Delivery Fee:</strong></td>
                                    <td><strong>{{ \App\Models\Setting::formatPrice($order->delivery_fee) }}</strong></td>
                                </tr>
                            @endif
                            @if($order->discount > 0)
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                                    <td><strong>-{{ \App\Models\Setting::formatPrice($order->discount) }}</strong></td>
                                </tr>
                            @endif
                            <tr class="table-primary">
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>{{ \App\Models\Setting::formatPrice($order->total_amount) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delivery Person Information (Only for Delivery Orders) -->
        @if(($order->delivery_type ?? 'delivery') === 'delivery' && $order->deliveredBy)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-truck me-2"></i>Delivery Person</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $order->deliveredBy->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> {{ $order->deliveredBy->email }}</p>
                        </div>
                    </div>
                    @if($order->deliveredBy->phone)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> {{ $order->deliveredBy->phone }}</p>
                            </div>
                        </div>
                    @endif
                    @if($order->deliveredBy->branch)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Branch:</strong> <span class="badge bg-info">{{ $order->deliveredBy->branch->name }}</span></p>
                            </div>
                        </div>
                    @endif
                    @if($order->status === 'out_for_delivery')
                        <div class="alert alert-info mb-0 mt-2">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Status:</strong> Order is currently out for delivery with this person.
                        </div>
                    @elseif($order->status === 'delivered' && $order->delivered_at)
                        <div class="alert alert-success mb-0 mt-2">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Delivered:</strong> {{ $order->delivered_at->format('M d, Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

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
                    @if($order->prescription->patient_name)
                        <p><strong>Patient:</strong> {{ $order->prescription->patient_name }}</p>
                    @endif
                    @if($order->prescription->file_path)
                        <div class="mt-3">
                            <div class="prescription-preview mb-3" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #f9f9f9; max-width: 100%;">
                                <img src="{{ Storage::url($order->prescription->file_path) }}" 
                                     alt="Prescription Preview" 
                                     class="img-fluid" 
                                     style="max-width: 100%; height: auto; border-radius: 4px;"
                                     id="prescriptionImage">
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="printPrescription('{{ Storage::url($order->prescription->file_path) }}')">
                                <i class="fas fa-print me-2"></i>Print Prescription
                            </button>
                        </div>
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
                    @if(($order->delivery_type ?? 'delivery') === 'pickup')
                        <!-- Mark Ready for Pickup -->
                        <form action="{{ route('admin.orders.mark-ready-pickup', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-hand-holding me-2"></i>Mark Ready for Pickup
                            </button>
                        </form>
                    @else
                        <!-- Assign Delivery -->
                        <form action="{{ route('admin.orders.deliver', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label for="delivery_person_id" class="form-label">Delivery Person <span class="text-danger">*</span></label>
                                <select name="delivery_person_id" id="delivery_person_id" class="form-select" required>
                                    <option value="">Select Delivery Person</option>
                                    @if(isset($deliveryPersons) && $deliveryPersons->count() > 0)
                                        @foreach($deliveryPersons as $person)
                                            <option value="{{ $person->id }}">
                                                {{ $person->name }}
                                                @if($person->branch)
                                                    ({{ $person->branch->name }})
                                                @endif
                                                - {{ $person->active_deliveries_count ?? 0 }} active delivery(ies)
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No delivery persons available</option>
                                    @endif
                                </select>
                                @if(!isset($deliveryPersons) || $deliveryPersons->count() === 0)
                                    <div class="alert alert-warning mt-2 mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>No delivery persons found!</strong> 
                                        @if($order->branch_id)
                                            No delivery persons are assigned to this order's branch ({{ $order->branch->name ?? 'N/A' }}).
                                        @endif
                                        <br>
                                        Please <a href="{{ route('admin.users.create') }}" target="_blank">create a delivery person</a> 
                                        and assign them the "delivery_person" role{{ $order->branch_id ? ' and assign them to the branch' : '' }}.
                                    </div>
                                @else
                                    <small class="form-text text-muted">
                                        Shows active delivery count for each person
                                    </small>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-truck me-2"></i>Assign for Delivery
                            </button>
                        </form>
                    @endif
                @elseif($order->status === 'out_for_delivery')
                    @if(($order->delivery_type ?? 'delivery') === 'pickup')
                        <!-- Mark Collected (Pickup) -->
                        <form action="{{ route('admin.orders.mark-collected', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle me-2"></i>Mark as Collected
                            </button>
                        </form>
                    @else
                        <!-- Mark Delivered -->
                        <form action="{{ route('admin.orders.mark-delivered', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle me-2"></i>Mark as Delivered
                            </button>
                        </form>
                    @endif
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

<div class="mt-3 d-flex justify-content-between">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Orders
    </a>
    @if(!Auth::user()->isBranchStaff() && $order->payment_status !== 'paid')
    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete order #{{ $order->order_number }}? This action cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-2"></i>Delete Order
        </button>
    </form>
    @endif
</div>

@push('scripts')
<script>
    function printPrescription(imageUrl) {
        var printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Please allow popups to print the prescription');
            return;
        }
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <title>Print Prescription</title>
                    <style>
                        @media print {
                            @page {
                                margin: 0;
                            }
                            body {
                                margin: 0;
                                padding: 0;
                            }
                        }
                        body {
                            margin: 0;
                            padding: 20px;
                            text-align: center;
                            font-family: Arial, sans-serif;
                        }
                        img {
                            max-width: 100%;
                            height: auto;
                            display: block;
                            margin: 0 auto;
                        }
                    </style>
                </head>
                <body>
                    <img src="${imageUrl}" alt="Prescription" id="prescriptionImg">
                    <script>
                        (function() {
                            var img = document.getElementById('prescriptionImg');
                            img.onload = function() {
                                setTimeout(function() {
                                    window.print();
                                    setTimeout(function() {
                                        window.close();
                                    }, 100);
                                }, 250);
                            };
                            img.onerror = function() {
                                alert('Failed to load prescription image. Please try again.');
                                window.close();
                            };
                            // If image is already loaded (cached)
                            if (img.complete && img.naturalHeight !== 0) {
                                img.onload();
                            }
                        })();
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
@endpush
@endsection

