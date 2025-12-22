@extends('admin.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>All Notifications</h5>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Notification
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date Range</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr>
                            <td>{{ $notification->id }}</td>
                            <td>
                                <strong>{{ $notification->title }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($notification->message, 50) }}</small>
                            </td>
                            <td>
                                @php
                                    $badgeColors = [
                                        'info' => 'bg-info',
                                        'success' => 'bg-success',
                                        'warning' => 'bg-warning',
                                        'error' => 'bg-danger',
                                        'promotion' => 'bg-primary',
                                    ];
                                    $color = $badgeColors[$notification->type] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $color }}">{{ ucfirst($notification->type) }}</span>
                            </td>
                            <td>{{ $notification->priority }}</td>
                            <td>
                                @if($notification->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($notification->start_date || $notification->end_date)
                                    <small>
                                        @if($notification->start_date)
                                            From: {{ $notification->start_date->format('M d, Y') }}<br>
                                        @endif
                                        @if($notification->end_date)
                                            To: {{ $notification->end_date->format('M d, Y') }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">No date range</span>
                                @endif
                            </td>
                            <td>{{ $notification->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notification?');">
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
                            <td colspan="8" class="text-center">No notifications found. <a href="{{ route('admin.notifications.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection

