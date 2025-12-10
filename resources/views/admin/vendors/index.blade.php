@extends('admin.layouts.app')

@section('title', 'Vendors')
@section('page-title', 'Vendors')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-truck me-2"></i>All Vendors</h5>
        <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Vendor
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Purchase Orders</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->id }}</td>
                            <td><strong>{{ $vendor->name }}</strong></td>
                            <td>{{ $vendor->contact_person ?? 'N/A' }}</td>
                            <td>{{ $vendor->email ?? 'N/A' }}</td>
                            <td>{{ $vendor->phone ?? 'N/A' }}</td>
                            <td>{{ $vendor->purchaseOrders()->count() }}</td>
                            <td>
                                @if($vendor->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vendor?');">
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
                            <td colspan="8" class="text-center">No vendors found. <a href="{{ route('admin.vendors.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $vendors->links() }}
        </div>
    </div>
</div>
@endsection

