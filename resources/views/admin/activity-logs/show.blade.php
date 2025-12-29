@extends('admin.layouts.app')

@section('title', 'Activity Log Details')

@section('page-title', 'Activity Log Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Activity Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">ID</th>
                        <td>{{ $activityLog->id }}</td>
                    </tr>
                    <tr>
                        <th>User</th>
                        <td>
                            @if($activityLog->user)
                                <a href="{{ route('admin.users.edit', $activityLog->user) }}">
                                    {{ $activityLog->user->name }} ({{ $activityLog->user->email }})
                                </a>
                            @else
                                <span class="text-muted">Guest/System</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Action</th>
                        <td>
                            <span class="badge bg-{{ $activityLog->action == 'login' ? 'success' : ($activityLog->action == 'logout' ? 'warning' : 'info') }}">
                                {{ ucfirst($activityLog->action) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $activityLog->description }}</td>
                    </tr>
                    @if($activityLog->model_type)
                    <tr>
                        <th>Related Model</th>
                        <td>
                            {{ class_basename($activityLog->model_type) }} #{{ $activityLog->model_id }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th>IP Address</th>
                        <td><code>{{ $activityLog->ip_address }}</code></td>
                    </tr>
                    <tr>
                        <th>User Agent</th>
                        <td><small>{{ $activityLog->user_agent }}</small></td>
                    </tr>
                    <tr>
                        <th>URL</th>
                        <td><a href="{{ $activityLog->url }}" target="_blank">{{ $activityLog->url }}</a></td>
                    </tr>
                    <tr>
                        <th>HTTP Method</th>
                        <td><span class="badge bg-secondary">{{ $activityLog->method }}</span></td>
                    </tr>
                    <tr>
                        <th>Date & Time</th>
                        <td>
                            {{ $activityLog->created_at->format('F d, Y h:i:s A') }}
                            <br>
                            <small class="text-muted">{{ $activityLog->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                </table>

                @if($activityLog->properties)
                <div class="mt-4">
                    <h6>Additional Properties</h6>
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT) }}</code></pre>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Logs
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

