@extends('admin.layouts.app')

@section('title', 'Vendor Details')
@section('page-title', 'Vendor Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $vendor->name }}</h5>
                <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Contact Person:</strong> {{ $vendor->contact_person ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        @if($vendor->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Email:</strong> {{ $vendor->email ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Phone:</strong> {{ $vendor->phone ?? 'N/A' }}
                    </div>
                </div>
                @if($vendor->address)
                    <div class="mb-3">
                        <strong>Address:</strong><br>
                        {{ $vendor->address }}
                    </div>
                @endif
                @if($vendor->tax_id)
                    <div class="mb-3">
                        <strong>Tax ID:</strong> {{ $vendor->tax_id }}
                    </div>
                @endif
                @if($vendor->notes)
                    <div class="alert alert-info">
                        <strong>Notes:</strong> {{ $vendor->notes }}
                    </div>
                @endif
            </div>
        </div>

        @if($vendor->purchaseOrders->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Purchase Orders ({{ $vendor->purchaseOrders->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendor->purchaseOrders as $po)
                                    <tr>
                                        <td><code>{{ $po->po_number }}</code></td>
                                        <td>{{ $po->order_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $po->status === 'received' ? 'success' : ($po->status === 'pending' ? 'warning' : 'info') }}">
                                                {{ ucfirst($po->status) }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($po->total_amount, 2) }}</td>
                                        <td>
                                            <a href="{{ route('admin.purchase-orders.show', $po->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
</div>

<div class="mt-3">
    <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Vendors
    </a>
</div>
@endsection

