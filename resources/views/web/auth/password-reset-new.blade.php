@extends('web.layouts.app')

@section('title', 'New Password')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-key text-primary me-2"></i>Create New Password
                    </h2>

                    <p class="text-center text-muted mb-4">
                        Enter your new password
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.reset.reset') }}">
                        @csrf
                        <input type="hidden" name="verification_token" value="{{ $verification_token }}">

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required autofocus>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Password must be at least 6 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-save me-2"></i>Reset Password
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-primary">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

