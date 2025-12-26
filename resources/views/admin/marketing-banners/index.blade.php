@extends('admin.layouts.app')

@section('title', 'Marketing Banners')
@section('page-title', 'Marketing Banners')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-image me-2"></i>All Marketing Banners</h5>
        <a href="{{ route('admin.marketing-banners.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Marketing Banner
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Background Color</th>
                        <th>Button Text</th>
                        <th>Link</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                        <tr>
                            <td>{{ $banner->id }}</td>
                            <td>
                                @if($banner->image)
                                    <img src="{{ Storage::url($banner->image) }}" alt="{{ $banner->title }}" style="width: 100px; height: 60px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <div style="width: 100px; height: 60px; background: #e9ecef; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $banner->title }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $banner->background_color }}; color: {{ \Illuminate\Support\Str::startsWith($banner->background_color, '#') && hexdec(substr($banner->background_color, 1)) > 0x888888 ? '#fff' : '#000' }};">
                                    {{ $banner->background_color }}
                                </span>
                            </td>
                            <td>{{ $banner->button_text }}</td>
                            <td>
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" class="text-primary">
                                        <i class="fas fa-external-link-alt"></i> View Link
                                    </a>
                                @else
                                    <span class="text-muted">No link</span>
                                @endif
                            </td>
                            <td>{{ $banner->order }}</td>
                            <td>
                                @if($banner->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $banner->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.marketing-banners.edit', $banner->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.marketing-banners.destroy', $banner->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this marketing banner?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No marketing banners found. <a href="{{ route('admin.marketing-banners.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $banners->links() }}
        </div>
    </div>
</div>
@endsection

