@extends('admin.layouts.app')

@section('title', 'Edit Notification')
@section('page-title', 'Edit Notification')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Notification</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.notifications.update', $notification->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $notification->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" required>{{ old('message', $notification->message) }}</textarea>
                @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="info" {{ old('type', $notification->type) == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="success" {{ old('type', $notification->type) == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="warning" {{ old('type', $notification->type) == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="error" {{ old('type', $notification->type) == 'error' ? 'selected' : '' }}>Error</option>
                            <option value="promotion" {{ old('type', $notification->type) == 'promotion' ? 'selected' : '' }}>Promotion</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <input type="number" class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" value="{{ old('priority', $notification->priority) }}" min="0" max="100">
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Higher priority notifications appear first (0-100)</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                @if($notification->image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($notification->image) }}" alt="Current image" style="max-width: 200px; max-height: 150px; border-radius: 5px;">
                    </div>
                @endif
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Leave empty to keep current image. Max size: 5MB</small>
            </div>

            <div class="mb-3">
                <label for="link" class="form-label">Link URL</label>
                <input type="url" class="form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link', $notification->link) }}" placeholder="https://example.com">
                @error('link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $notification->start_date ? $notification->start_date->format('Y-m-d\TH:i') : '') }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $notification->end_date ? $notification->end_date->format('Y-m-d\TH:i') : '') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $notification->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Notification
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

