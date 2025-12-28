@extends('admin.layouts.app')

@section('title', 'Profit & Loss Report')
@section('page-title', 'Profit & Loss Report')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Profit & Loss Report</h5>
    </div>
    <div class="card-body">
        <!-- Date Filter -->
        <form method="GET" action="{{ route('admin.reports.profit-loss') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="date" class="form-label">Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.reports.profit-loss', ['date' => \Carbon\Carbon::today()->format('Y-m-d')]) }}" class="btn btn-secondary w-100">
                        <i class="fas fa-calendar-day me-2"></i>Today
                    </a>
                </div>
            </div>
        </form>

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

