@extends('admin.layouts.app')

@section('title', 'View Page')
@section('page-title', 'View Page')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>{{ $page->title }}</h5>
        <div>
            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="btn btn-info btn-sm">
                <i class="fas fa-eye me-2"></i>View on Site
            </a>
            <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Slug:</strong> <code>{{ $page->slug }}</code>
            </div>
            <div class="col-md-6">
                <strong>Status:</strong>
                @if($page->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </div>
        </div>
        
        @if($page->meta_title)
            <div class="mb-3">
                <strong>Meta Title:</strong> {{ $page->meta_title }}
            </div>
        @endif
        
        @if($page->meta_description)
            <div class="mb-3">
                <strong>Meta Description:</strong> {{ $page->meta_description }}
            </div>
        @endif
        
        <div class="mb-3">
            <strong>Content:</strong>
            <div class="border rounded p-3 mt-2" style="background: #f8f9fa; min-height: 200px;">
                {!! $page->content ?: '<em class="text-muted">No content</em>' !!}
            </div>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <div>
                <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Page
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

