@extends('web.layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-lock text-primary me-2"></i>Reset Password
                    </h2>

                    <p class="text-center text-muted mb-4">
                        Enter your registered phone number to receive an OTP
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

                    <form method="POST" action="{{ route('password.reset.generate') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" required autofocus
                                   placeholder="Enter your phone number">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-paper-plane me-2"></i>Send OTP
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

