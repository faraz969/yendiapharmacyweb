@extends('admin.layouts.app')

@section('title', 'Purchase Order Details')
@section('page-title', 'Purchase Order Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">PO #{{ $purchaseOrder->po_number }}</h5>
                <span class="badge bg-{{ $purchaseOrder->status === 'received' ? 'success' : ($purchaseOrder->status === 'pending' ? 'warning' : 'info') }}">
                    {{ ucfirst($purchaseOrder->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Vendor:</strong> {{ $purchaseOrder->vendor->name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Order Date:</strong> {{ $purchaseOrder->order_date->format('M d, Y') }}
                    </div>
                </div>
                @if($purchaseOrder->expected_delivery_date)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date->format('M d, Y') }}
                        </div>
                    </div>
                @endif
                @if($purchaseOrder->notes)
                    <div class="mb-3">
                        <strong>Notes:</strong> {{ $purchaseOrder->notes }}
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Items</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Received</th>
                                <th>Unit Cost</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }} <code>{{ $item->product->sku }}</code></td>
                                    <td>{{ $item->quantity }} {{ $item->product->purchase_unit }}</td>
                                    <td>{{ $item->received_quantity }} / {{ $item->quantity }}</td>
                                    <td>{{ \App\Models\Setting::formatPrice($item->unit_cost) }}</td>
                                    <td>{{ \App\Models\Setting::formatPrice($item->total_cost) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>{{ \App\Models\Setting::formatPrice($purchaseOrder->total_amount) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($purchaseOrder->status !== 'received' && $purchaseOrder->status !== 'cancelled')
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-box me-2"></i>Receive Items</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.purchase-orders.receive', $purchaseOrder->id) }}" method="POST">
                        @csrf
                        @foreach($purchaseOrder->items as $item)
                            @if($item->received_quantity < $item->quantity)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6>{{ $item->product->name }}</h6>
                                        <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Received Qty</label>
                                                <input type="number" class="form-control" name="items[{{ $loop->index }}][received_quantity]" 
                                                    value="{{ $item->received_quantity }}" 
                                                    min="0" max="{{ $item->quantity - $item->received_quantity }}" required>
                                                <small>Remaining: {{ $item->quantity - $item->received_quantity }}</small>
                                            </div>
                                            @if($item->product->track_batch)
                                                <div class="col-md-3">
                                                    <label class="form-label">Batch Number</label>
                                                    <input type="text" class="form-control" name="items[{{ $loop->index }}][batch_number]">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Expiry Date</label>
                                                    <input type="date" class="form-control" name="items[{{ $loop->index }}][expiry_date]">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Manufacturing Date</label>
                                                    <input type="date" class="form-control" name="items[{{ $loop->index }}][manufacturing_date]">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Receive Items
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Purchase Orders
    </a>
</div>
@endsection

