@extends('admin.layouts.app')

@section('title', 'Item Request Details')
@section('page-title', 'Item Request Details')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Request #{{ $itemRequest->request_number }}</h5>
                    <span class="badge bg-{{ $itemRequest->status === 'fulfilled' ? 'success' : ($itemRequest->status === 'rejected' ? 'danger' : ($itemRequest->status === 'pending' ? 'warning' : 'info')) }}">
                        {{ ucfirst(str_replace('_', ' ', $itemRequest->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Item Name:</strong> {{ $itemRequest->item_name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Quantity:</strong> {{ $itemRequest->quantity }}
                        </div>
                    </div>
                    @if($itemRequest->description)
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p>{{ $itemRequest->description }}</p>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer Name:</strong> {{ $itemRequest->customer_name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Customer Phone:</strong> {{ $itemRequest->customer_phone }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer Email:</strong> {{ $itemRequest->customer_email ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Requested By:</strong> {{ $itemRequest->user->name ?? 'Guest' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Request Date:</strong> {{ $itemRequest->created_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                    @if($itemRequest->admin_notes)
                        <div class="mb-3">
                            <strong>Admin Notes:</strong>
                            <p>{{ $itemRequest->admin_notes }}</p>
                        </div>
                    @endif
                    @if($itemRequest->rejection_reason)
                        <div class="alert alert-danger">
                            <strong>Rejection Reason:</strong> {{ $itemRequest->rejection_reason }}
                        </div>
                    @endif
                    @if($itemRequest->processedBy)
                        <div class="mb-3">
                            <strong>Processed By:</strong> {{ $itemRequest->processedBy->name }}
                            @if($itemRequest->processed_at)
                                <br><strong>Processed At:</strong> {{ $itemRequest->processed_at->format('M d, Y H:i') }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('branch.item-requests.update-status', $itemRequest->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                @foreach(['pending', 'in_progress', 'fulfilled', 'rejected', 'cancelled'] as $status)
                                    <option value="{{ $status }}" {{ $itemRequest->status == $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Admin Notes</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3">{{ $itemRequest->admin_notes }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Rejection Reason (if rejecting)</label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="2">{{ $itemRequest->rejection_reason }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('branch.item-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Item Requests
        </a>
    </div>
@endsection

