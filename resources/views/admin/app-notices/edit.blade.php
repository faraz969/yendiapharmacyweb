@extends('admin.layouts.app')

@section('title', 'Edit App Notice')
@section('page-title', 'Edit App Notice')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit App Notice</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.app-notices.update', $appNotice->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            @if($appNotice->image)
            <div class="mb-3">
                <label class="form-label">Current Image</label>
                <div>
                    <img src="{{ Storage::url($appNotice->image) }}" alt="{{ $appNotice->title }}" style="max-width: 300px; max-height: 200px; border-radius: 5px;">
                </div>
            </div>
            @endif

            <div class="mb-3">
                <label for="title" class="form-label">Heading <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $appNotice->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content', $appNotice->content) }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">This text will be displayed in the popup notice</small>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image (Optional)</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Leave empty to keep current image. Max size: 5MB. Formats: JPEG, PNG, JPG, GIF, WEBP</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <input type="number" class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" value="{{ old('priority', $appNotice->priority) }}" min="0">
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Higher priority notices will be shown first</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $appNotice->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <small class="form-text text-muted">Only active notices will be shown to users</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date (Optional)</label>
                        <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $appNotice->start_date ? $appNotice->start_date->format('Y-m-d\TH:i') : '') }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Notice will only show after this date</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date (Optional)</label>
                        <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $appNotice->end_date ? $appNotice->end_date->format('Y-m-d\TH:i') : '') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Notice will stop showing after this date</small>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.app-notices.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Notice
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

