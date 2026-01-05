@extends('web.layouts.app')

@section('title', 'My Insurance Requests')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-shield-alt me-2" style="color:#dc8423;"></i>My Insurance Requests
    </h2>

    <div class="card shadow">
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Request Number</th>
                                <th>Insurance Company</th>
                                <th>Insurance Number</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td><strong>{{ $request->request_number }}</strong></td>
                                    <td>{{ $request->insuranceCompany->name }}</td>
                                    <td>{{ $request->insurance_number }}</td>
                                    <td>{{ $request->items->count() }} item(s)</td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($request->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($request->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($request->status === 'order_created')
                                            <span class="badge bg-info">Order Created</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('user.services.insurance-requests.show', $request->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No insurance requests found.</p>
                    <a href="{{ route('user.services.insurance') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Insurance Request
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

