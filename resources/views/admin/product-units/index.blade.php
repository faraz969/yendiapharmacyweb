@extends('admin.layouts.app')

@section('title', 'Product Units')
@section('page-title', 'Product Units')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-ruler-combined me-2"></i>Purchase & Selling Units</h5>
        <a href="{{ route('admin.product-units.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Unit
        </a>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Manage units available when creating products. Units marked as <strong>Purchase</strong> appear in purchase unit dropdowns,
            <strong>Selling</strong> in selling unit dropdowns, and <strong>Both</strong> in both.
        </p>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Used For</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                        <tr>
                            <td>{{ $unit->id }}</td>
                            <td>{{ $unit->name }}</td>
                            <td>
                                @if($unit->type === 'purchase')
                                    <span class="badge bg-primary">Purchase</span>
                                @elseif($unit->type === 'selling')
                                    <span class="badge bg-info">Selling</span>
                                @else
                                    <span class="badge bg-success">Both</span>
                                @endif
                            </td>
                            <td>{{ $unit->sort_order }}</td>
                            <td>
                                @if($unit->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.product-units.edit', $unit->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.product-units.destroy', $unit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this unit?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No units found. <a href="{{ route('admin.product-units.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $units->links() }}
        </div>
    </div>
</div>
@endsection
