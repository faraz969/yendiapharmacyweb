@extends('web.layouts.app')

@section('title', 'Delivery Addresses')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-map-marker-alt text-primary me-2"></i>Delivery Addresses
        </h2>
        <a href="{{ route('user.addresses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Address
        </a>
    </div>

    @if($addresses->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>You don't have any saved addresses yet. 
            <a href="{{ route('user.addresses.create') }}" class="alert-link">Add your first address</a> to make checkout faster!
        </div>
    @else
        <div class="row">
            @foreach($addresses as $address)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 {{ $address->is_default ? 'border-primary' : '' }}">
                        <div class="card-body">
                            @if($address->is_default)
                                <span class="badge bg-primary mb-2">Default Address</span>
                            @endif
                            @if($address->label)
                                <h5 class="card-title">{{ $address->label }}</h5>
                            @endif
                            <p class="card-text">
                                <strong>{{ $address->contact_name }}</strong><br>
                                {{ $address->contact_phone }}<br>
                                @if($address->contact_email)
                                    {{ $address->contact_email }}<br>
                                @endif
                                {{ $address->full_address }}
                            </p>
                            <div class="btn-group" role="group">
                                <a href="{{ route('user.addresses.edit', $address) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                @if(!$address->is_default)
                                    <form action="{{ route('user.addresses.set-default', $address) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-star me-1"></i>Set as Default
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('user.addresses.destroy', $address) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this address?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

