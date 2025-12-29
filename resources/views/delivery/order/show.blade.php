@extends('admin.layouts.app')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('content')
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
        <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : 'primary' }} fs-6">
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
        @if($order->branch)
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Branch:</strong> {{ $order->branch->name }}
                </div>
            </div>
        @endif
        @if($order->deliveryZone)
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Delivery Zone:</strong> {{ $order->deliveryZone->name }}
                </div>
            </div>
        @endif
    </div>
</div>

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

@if($order->status === 'out_for_delivery')
<div class="card mb-3">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('delivery.orders.mark-delivered', $order->id) }}" method="POST" onsubmit="return confirm('Mark order #{{ $order->order_number }} as delivered?');">
            @csrf
            <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-check-circle me-2"></i>Mark as Delivered
            </button>
        </form>
    </div>
</div>
@endif

<div class="mt-3">
    <a href="{{ route('delivery.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>
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

