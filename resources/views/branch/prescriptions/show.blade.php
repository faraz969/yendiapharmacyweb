@extends('admin.layouts.app')

@section('title', 'Prescription Details')
@section('page-title', 'Prescription Details')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Prescription #{{ $prescription->prescription_number }}</h5>
                    <span class="badge bg-{{ $prescription->status === 'approved' ? 'success' : ($prescription->status === 'rejected' ? 'danger' : 'warning') }}">
                        {{ ucfirst($prescription->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Patient Name:</strong> {{ $prescription->patient_name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Doctor Name:</strong> {{ $prescription->doctor_name ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer Phone:</strong> {{ $prescription->customer_phone ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Customer Email:</strong> {{ $prescription->customer_email ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Prescription Date:</strong> {{ $prescription->prescription_date ? $prescription->prescription_date->format('M d, Y') : 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Submitted By:</strong> {{ $prescription->user->name ?? 'Guest' }}
                        </div>
                    </div>
                    @if($prescription->notes)
                        <div class="mb-3">
                            <strong>Notes:</strong>
                            <p>{{ $prescription->notes }}</p>
                        </div>
                    @endif
                    @if($prescription->rejection_reason)
                        <div class="alert alert-danger">
                            <strong>Rejection Reason:</strong> {{ $prescription->rejection_reason }}
                        </div>
                    @endif
                    @if($prescription->file_path)
                        <div class="mb-3">
                            <strong>Prescription File:</strong><br>
                            <a href="{{ Storage::url($prescription->file_path) }}" target="_blank" class="btn btn-primary mt-2">
                                <i class="fas fa-file-pdf me-2"></i>View Prescription
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @if($prescription->status === 'pending')
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('branch.prescriptions.approve', $prescription->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-2">
                                <label for="approve_notes" class="form-label">Notes (optional)</label>
                                <textarea name="notes" id="approve_notes" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i>Approve Prescription
                            </button>
                        </form>

                        <form action="{{ route('branch.prescriptions.reject', $prescription->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="2" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-2"></i>Reject Prescription
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($prescription->approvedBy)
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Approval Info</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Approved By:</strong> {{ $prescription->approvedBy->name }}</p>
                        @if($prescription->approved_at)
                            <p><strong>Approved At:</strong> {{ $prescription->approved_at->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('branch.prescriptions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Prescriptions
        </a>
    </div>
@endsection

