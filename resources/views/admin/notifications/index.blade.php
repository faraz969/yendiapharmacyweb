@extends('admin.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0"><i class="fas fa-bell me-2"></i>All Notifications</h5>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="notificationTypeToggle" 
                       {{ $filterType === 'received' ? 'checked' : '' }}
                       onchange="toggleNotificationType()">
                <label class="form-check-label" for="notificationTypeToggle">
                    <span id="toggleLabel">
                        {{ $filterType === 'received' ? 'Received Notifications' : 'Created Notifications' }}
                    </span>
                </label>
            </div>
        </div>
        @if($filterType === 'created')
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Notification
        </a>
        @endif
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
                        <tr class="{{ !$notification->is_read && $filterType === 'received' ? 'table-warning' : '' }}">
                            <td>{{ $notification->id }}</td>
                            <td>
                                <div class="d-flex align-items-start">
                                    @if($filterType === 'received' && !$notification->is_read)
                                        <span class="badge bg-danger me-2">New</span>
                                    @endif
                                    <div>
                                        <strong>{{ $notification->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($notification->message, 50) }}</small>
                                        @if($filterType === 'received')
                                            <br>
                                            <small class="text-muted">
                                                @if($notification->order_id)
                                                    <i class="fas fa-shopping-cart"></i> Order
                                                @elseif($notification->insurance_request_id)
                                                    <i class="fas fa-shield-alt"></i> Insurance Request
                                                @elseif($notification->refund_request_id)
                                                    <i class="fas fa-money-bill-wave"></i> Refund Request
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </div>
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
                            <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($filterType === 'created')
                                    <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @else
                                    @if(!$notification->is_read)
                                        <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark as read">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($notification->link)
                                        <a href="{{ $notification->link }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                @endif
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

<script>
function toggleNotificationType() {
    const toggle = document.getElementById('notificationTypeToggle');
    const type = toggle.checked ? 'received' : 'created';
    const url = new URL(window.location.href);
    url.searchParams.set('type', type);
    window.location.href = url.toString();
}
</script>
@endsection

