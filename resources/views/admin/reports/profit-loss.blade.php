@extends('admin.layouts.app')

@section('title', 'Profit & Loss Report')
@section('page-title', 'Profit & Loss Report')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Profit & Loss Report</h5>
    </div>
    <div class="card-body">
        <!-- Date Range and Branch Filter -->
        <form method="GET" action="{{ route('admin.reports.profit-loss') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label for="branch_id" class="form-label">Select Branch</label>
                    <select name="branch_id" id="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches ?? [] as $branch)
                            <option value="{{ $branch->id }}" {{ (isset($branchId) && $branchId == $branch->id) ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.reports.profit-loss', array_filter(['start_date' => \Carbon\Carbon::today()->format('Y-m-d'), 'end_date' => \Carbon\Carbon::today()->format('Y-m-d'), 'branch_id' => $branchId ?? null])) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-calendar-day me-1"></i>Today
                        </a>
                        <a href="{{ route('admin.reports.profit-loss', array_filter(['start_date' => \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d'), 'end_date' => \Carbon\Carbon::now()->endOfWeek()->format('Y-m-d'), 'branch_id' => $branchId ?? null])) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-calendar-week me-1"></i>This Week
                        </a>
                        <a href="{{ route('admin.reports.profit-loss', array_filter(['start_date' => \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'), 'end_date' => \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'), 'branch_id' => $branchId ?? null])) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-calendar me-1"></i>This Month
                        </a>
                        <a href="{{ route('admin.reports.profit-loss', array_filter(['start_date' => \Carbon\Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => \Carbon\Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d'), 'branch_id' => $branchId ?? null])) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-calendar-alt me-1"></i>Last Month
                        </a>
                    </div>
                </div>
            </div>
        </form>

        @if(isset($selectedBranch) && $selectedBranch)
            <div class="alert alert-info mb-4">
                <i class="fas fa-building me-2"></i>
                <strong>Filtered by Branch:</strong> {{ $selectedBranch->name }}
            </div>
        @endif

        <div class="alert alert-light mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Report Period:</strong> {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
            @if($startDate->format('Y-m-d') === $endDate->format('Y-m-d'))
                <span class="badge bg-secondary ms-2">Single Day</span>
            @else
                <span class="badge bg-info ms-2">{{ $startDate->diffInDays($endDate) + 1 }} Days</span>
            @endif
            @if(isset($deliveredWithoutPaymentCount) && $deliveredWithoutPaymentCount > 0)
                <span class="badge bg-warning ms-2">{{ $deliveredWithoutPaymentCount }} delivered order(s) without payment status</span>
            @endif
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Revenue</h6>
                        <h3 class="mb-0">{{ \App\Models\Setting::formatPrice($revenue) }}</h3>
                        <small>From Delivered Orders</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6 class="card-title">Refunds</h6>
                        <h3 class="mb-0">{{ \App\Models\Setting::formatPrice($refunds) }}</h3>
                        <small>Rejected & Cancelled Orders</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title">Cost of Goods</h6>
                        <h3 class="mb-0">{{ \App\Models\Setting::formatPrice($cogs) }}</h3>
                        <small>Product Costs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                    <div class="card-body">
                        <h6 class="card-title">Net Profit</h6>
                        <h3 class="mb-0">{{ \App\Models\Setting::formatPrice($netProfit) }}</h3>
                        <small>After Refunds</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Profit Calculation</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Total Revenue</strong></td>
                                    <td class="text-end">{{ \App\Models\Setting::formatPrice($revenue) }}</td>
                                </tr>
                                <tr>
                                    <td>Cost of Goods Sold (COGS)</td>
                                    <td class="text-end text-danger">-{{ \App\Models\Setting::formatPrice($cogs) }}</td>
                                </tr>
                                <tr class="table-info">
                                    <td><strong>Gross Profit</strong></td>
                                    <td class="text-end"><strong>{{ \App\Models\Setting::formatPrice($grossProfit) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Refunds (Rejected/Cancelled)</td>
                                    <td class="text-end text-danger">-{{ \App\Models\Setting::formatPrice($refunds) }}</td>
                                </tr>
                                <tr class="table-{{ $netProfit >= 0 ? 'success' : 'danger' }}">
                                    <td><strong>Net Profit</strong></td>
                                    <td class="text-end"><strong>{{ \App\Models\Setting::formatPrice($netProfit) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Order Statistics</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Total Orders</td>
                                    <td class="text-end"><span class="badge bg-secondary">{{ $orderStats['total_orders'] }}</span></td>
                                </tr>
                                <tr>
                                    <td>Delivered Orders</td>
                                    <td class="text-end"><span class="badge bg-success">{{ $orderStats['delivered_orders'] }}</span></td>
                                </tr>
                                <tr>
                                    <td>Rejected Orders</td>
                                    <td class="text-end"><span class="badge bg-danger">{{ $orderStats['rejected_orders'] }}</span></td>
                                </tr>
                                <tr>
                                    <td>Cancelled Orders</td>
                                    <td class="text-end"><span class="badge bg-warning">{{ $orderStats['cancelled_orders'] }}</span></td>
                                </tr>
                                <tr>
                                    <td>Pending Orders</td>
                                    <td class="text-end"><span class="badge bg-info">{{ $orderStats['pending_orders'] }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branch Breakdown -->
        @if($branchBreakdown->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-building me-2"></i>Branch-wise Revenue</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Branch</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($branchBreakdown as $branch)
                                <tr>
                                    <td>{{ $branch->branch ? $branch->branch->name : 'N/A' }}</td>
                                    <td>{{ $branch->order_count }}</td>
                                    <td>{{ \App\Models\Setting::formatPrice($branch->revenue) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

