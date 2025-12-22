@extends('admin.layouts.app')

@section('title', 'Branches')
@section('page-title', 'Branches')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-building me-2"></i>All Branches</h5>
        <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Branch
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Phone</th>
                        <th>Manager</th>
                        <th>Orders</th>
                        <th>Staff</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr>
                            <td>{{ $branch->id }}</td>
                            <td><strong>{{ $branch->name }}</strong></td>
                            <td>{{ Str::limit($branch->address, 30) }}</td>
                            <td>{{ $branch->city ?? 'N/A' }}</td>
                            <td>{{ $branch->phone ?? 'N/A' }}</td>
                            <td>{{ $branch->manager_name ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $branch->orders_count ?? 0 }}</span></td>
                            <td><span class="badge bg-secondary">{{ $branch->staff_count ?? 0 }}</span></td>
                            <td>
                                @if($branch->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.branches.show', $branch) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this branch?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-building fa-3x mb-3 d-block"></i>
                                No branches found. <a href="{{ route('admin.branches.create') }}">Create one now</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $branches->links() }}
        </div>
    </div>
</div>
@endsection

