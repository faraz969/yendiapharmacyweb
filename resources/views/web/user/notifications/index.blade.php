@extends('web.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-bell me-2" style="color:#dc8423;"></i>Notifications
                    @if($unreadCount > 0)
                        <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                    @endif
                </h2>
                <div>
                    @if($notifications->count() > 0)
                        <form action="{{ route('user.notifications.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-check-double me-1"></i>Mark All as Read
                            </button>
                        </form>
                        <form action="{{ route('user.notifications.clear-all') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all notifications?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>Clear All
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($notifications->count() > 0)
                <div class="card shadow">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ !$notification->is_read ? 'bg-light' : '' }}" style="border-left: 4px solid {{ $notification->is_read ? 'transparent' : ($notification->type === 'success' ? '#28a745' : ($notification->type === 'error' ? '#dc3545' : ($notification->type === 'warning' ? '#ffc107' : '#17a2b8'))) }};">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <h6 class="mb-0 me-2">
                                                    @if($notification->type === 'success')
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                    @elseif($notification->type === 'error')
                                                        <i class="fas fa-times-circle text-danger me-2"></i>
                                                    @elseif($notification->type === 'warning')
                                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                                    @else
                                                        <i class="fas fa-info-circle text-info me-2"></i>
                                                    @endif
                                                    {{ $notification->title }}
                                                </h6>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary">New</span>
                                                @endif
                                            </div>
                                            <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                                @if($notification->order)
                                                    | <a href="{{ route('user.orders.show', $notification->order_id) }}" class="text-decoration-none">View Order</a>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <div class="btn-group-vertical btn-group-sm">
                                                @if(!$notification->is_read)
                                                    <form action="{{ route('user.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Mark as read">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('user.notifications.destroy', $notification->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">You have no notifications.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

