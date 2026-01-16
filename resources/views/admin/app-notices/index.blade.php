@extends('admin.layouts.app')

@section('title', 'App Notices')
@section('page-title', 'App Notices')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>All App Notices</h5>
        <a href="{{ route('admin.app-notices.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Notice
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Heading</th>
                        <th>Image</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date Range</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices as $notice)
                        <tr>
                            <td>{{ $notice->id }}</td>
                            <td>
                                <strong>{{ $notice->title }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($notice->content, 50) }}</small>
                            </td>
                            <td>
                                @if($notice->image)
                                    <img src="{{ Storage::url($notice->image) }}" alt="{{ $notice->title }}" style="width: 100px; height: 60px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <div style="width: 100px; height: 60px; background: #e9ecef; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $notice->priority }}</span>
                            </td>
                            <td>
                                @if($notice->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($notice->start_date || $notice->end_date)
                                    <small>
                                        @if($notice->start_date)
                                            From: {{ $notice->start_date->format('M d, Y') }}<br>
                                        @endif
                                        @if($notice->end_date)
                                            To: {{ $notice->end_date->format('M d, Y') }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">No date range</span>
                                @endif
                            </td>
                            <td>{{ $notice->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.app-notices.edit', $notice->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.app-notices.destroy', $notice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notice?');">
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
                            <td colspan="8" class="text-center">No notices found. <a href="{{ route('admin.app-notices.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $notices->links() }}
        </div>
    </div>
</div>
@endsection

