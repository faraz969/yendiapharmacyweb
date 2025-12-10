@extends('admin.layouts.app')

@section('title', 'Delivery Zone Details')
@section('page-title', 'Delivery Zone Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $deliveryZone->name }}</h5>
                <a href="{{ route('admin.delivery-zones.edit', $deliveryZone->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
            </div>
            <div class="card-body">
                @if($deliveryZone->description)
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p>{{ $deliveryZone->description }}</p>
                    </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Delivery Fee:</strong> ${{ number_format($deliveryZone->delivery_fee, 2) }}
                    </div>
                    <div class="col-md-4">
                        <strong>Min Order (Free Delivery):</strong> ${{ number_format($deliveryZone->min_order_amount ?? 0, 2) }}
                    </div>
                    <div class="col-md-4">
                        <strong>Est. Delivery:</strong> {{ $deliveryZone->estimated_delivery_hours ?? 'N/A' }} hours
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    @if($deliveryZone->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        @if($deliveryZone->orders->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Orders ({{ $deliveryZone->orders->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deliveryZone->orders->take(10) as $order)
                                    <tr>
                                        <td><code>{{ $order->order_number }}</code></td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : 'info' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
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
    <a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Zones
    </a>
</div>
@endsection

