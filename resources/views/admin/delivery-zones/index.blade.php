@extends('admin.layouts.app')

@section('title', 'Delivery Zones')
@section('page-title', 'Delivery Zones')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>All Delivery Zones</h5>
        <a href="{{ route('admin.delivery-zones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Zone
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Delivery Fee</th>
                        <th>Min Order</th>
                        <th>Est. Hours</th>
                        <th>Orders</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zones as $zone)
                        <tr>
                            <td>{{ $zone->id }}</td>
                            <td><strong>{{ $zone->name }}</strong></td>
                            <td>{{ \App\Models\Setting::formatPrice($zone->delivery_fee) }}</td>
                            <td>{{ \App\Models\Setting::formatPrice($zone->min_order_amount ?? 0) }}</td>
                            <td>{{ $zone->estimated_delivery_hours ?? 'N/A' }} hrs</td>
                            <td>{{ $zone->orders()->count() }}</td>
                            <td>
                                @if($zone->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.delivery-zones.show', $zone->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.delivery-zones.edit', $zone->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.delivery-zones.destroy', $zone->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this zone?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No delivery zones found. <a href="{{ route('admin.delivery-zones.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $zones->links() }}
        </div>
    </div>
</div>
@endsection

