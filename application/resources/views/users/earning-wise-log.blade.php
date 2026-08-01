@extends('users.master')
@section('extra')
<style>
    .earn-summary .bg-body { min-height: 88px; }
    .earn-summary h5 { word-break: break-word; }
    @media (max-width: 576px) {
        .earn-summary .bg-body { min-height: auto; padding: 12px !important; }
        .pc-content .page-header-title h2 { font-size: 1.15rem; }
    }
</style>
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('/') }}/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript:">Incentive Report</a></li>
                            <li class="breadcrumb-item" aria-current="page">{{ $page_titel }}</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">{{ $page_titel }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row earn-summary g-3 mb-3">
            @if((int)$logtype === 2)
                <div class="col-6 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Today's ROI</p>
                                <h5 class="mb-0 text-primary">${{ $today_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Total ROI</p>
                                <h5 class="mb-0 text-primary">${{ $total_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Package</p>
                                <h5 class="mb-0">{{ $package_name }} <small class="text-muted">${{ number_format($package_amount, 2) }}</small></h5>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif((int)$logtype === 4)
                <div class="col-6 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Today's Team Level ROI</p>
                                <h5 class="mb-0 text-primary">${{ $today_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Total Team Level ROI</p>
                                <h5 class="mb-0 text-primary">${{ $total_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Package</p>
                                <h5 class="mb-0">{{ $package_name }} <small class="text-muted">${{ number_format($package_amount, 2) }}</small></h5>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif((int)$logtype === 7)
                <div class="col-6 col-md-6">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Today's Reward Salary</p>
                                <h5 class="mb-0 text-primary">${{ $today_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Total Reward Salary</p>
                                <h5 class="mb-0 text-primary">${{ $total_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif((int)$logtype === 10)
                <div class="col-6 col-md-6">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Today's Unlock</p>
                                <h5 class="mb-0 text-primary">${{ $today_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Total Unlocked</p>
                                <h5 class="mb-0 text-primary">${{ $total_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-6 col-md-6">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Today</p>
                                <h5 class="mb-0 text-primary">${{ $today_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="bg-body p-3 rounded">
                                <p class="mb-0 text-muted">Total</p>
                                <h5 class="mb-0 text-primary">${{ $total_sum }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $history_title ?? $page_titel }}</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @if((int)$logtype === 2)
                                            <th>Package / Detail</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        @elseif((int)$logtype === 4)
                                            <th>Level / Member</th>
                                            <th>ROI Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        @elseif((int)$logtype === 7)
                                            <th>Reward</th>
                                            <th>Weekly Salary</th>
                                            <th>Status</th>
                                            <th>Paid Date</th>
                                        @elseif((int)$logtype === 10)
                                            <th>Referral / Package</th>
                                            <th>Unlock Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        @else
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/earning-wise-log.0.4.js"></script>
@endsection
