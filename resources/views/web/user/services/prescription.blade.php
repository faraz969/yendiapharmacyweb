@extends('web.layouts.app')

@section('title', 'Send Prescription')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-prescription me-2" style="color:#dc8423;"></i>Send Prescription
    </h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('user.services.prescription.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                <option value="">Select Branch...</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="patient_name" class="form-label">Patient Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('patient_name') is-invalid @enderror" 
                                       id="patient_name" name="patient_name" value="{{ old('patient_name', Auth::user()->name) }}" required>
                                @error('patient_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="doctor_name" class="form-label">Doctor Name</label>
                                <input type="text" class="form-control @error('doctor_name') is-invalid @enderror" 
                                       id="doctor_name" name="doctor_name" value="{{ old('doctor_name') }}">
                                @error('doctor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prescription_date" class="form-label">Prescription Date</label>
                                <input type="date" class="form-control @error('prescription_date') is-invalid @enderror" 
                                       id="prescription_date" name="prescription_date" value="{{ old('prescription_date') }}">
                                @error('prescription_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                       id="customer_phone" name="customer_phone" value="{{ old('customer_phone', Auth::user()->phone ?? '') }}" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                   id="customer_email" name="customer_email" value="{{ old('customer_email', Auth::user()->email) }}">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="prescription_image" class="form-label">Prescription Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('prescription_image') is-invalid @enderror" 
                                   id="prescription_image" name="prescription_image" accept="image/*" required>
                            @error('prescription_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Take a picture or upload image</small>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Prescription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

