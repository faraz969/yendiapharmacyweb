@extends('web.layouts.app')

@section('title', $page->meta_title ?? $page->title)

@section('meta')
@if($page->meta_description)
    <meta name="description" content="{{ $page->meta_description }}">
@endif
@endsection

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h1 class="mb-4">{{ $page->title }}</h1>
            <div class="page-content">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</div>

<style>
    .page-content {
        line-height: 1.8;
        color: #333;
    }
    
    .page-content h1,
    .page-content h2,
    .page-content h3,
    .page-content h4,
    .page-content h5,
    .page-content h6 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .page-content p {
        margin-bottom: 1.5rem;
    }
    
    .page-content ul,
    .page-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }
    
    .page-content li {
        margin-bottom: 0.5rem;
    }
    
    .page-content a {
        color: #4caf50;
        text-decoration: none;
    }
    
    .page-content a:hover {
        text-decoration: underline;
    }
    
    .page-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1.5rem 0;
    }
</style>
@endsection

