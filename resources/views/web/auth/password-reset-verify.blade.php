@extends('web.layouts.app')

@section('title', 'Verify OTP')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-shield-alt text-primary me-2"></i>Enter OTP
                    </h2>

                    <p class="text-center text-muted mb-4">
                        We sent a code to <strong>{{ $phone }}</strong>
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

                    <form method="POST" action="{{ route('password.reset.verify.post') }}">
                        @csrf
                        <input type="hidden" name="phone" value="{{ $phone }}">

                        <div class="mb-3">
                            <label for="code" class="form-label">OTP Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror text-center" 
                                   id="code" name="code" value="{{ old('code') }}" required autofocus
                                   placeholder="Enter 6-digit OTP" maxlength="6" style="font-size: 24px; letter-spacing: 8px;">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-check me-2"></i>Verify OTP
                        </button>
                    </form>

                    <div class="text-center mb-3">
                        <form method="POST" action="{{ route('password.reset.resend') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="phone" value="{{ $phone }}">
                            <button type="submit" class="btn btn-link p-0">
                                Didn't receive the code? <strong>Resend OTP</strong>
                            </button>
                        </form>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('password.reset.request') }}" class="text-primary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

