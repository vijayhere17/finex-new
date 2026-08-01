@extends('users.master')
@section('extra')
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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Reward Progress</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="row g-3 mt-0">
                            <div class="col-sm-3">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0 text-muted">Active Directs</p>
                                    <h6 class="mb-0">{{ $progress['directs'] }}</h6>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0 text-muted">Active Team</p>
                                    <h6 class="mb-0">{{ $progress['team'] }}</h6>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0 text-muted">Self Business</p>
                                    <h6 class="mb-0">${{ number_format($progress['self_business'], 2) }}</h6>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0 text-muted">Team Business</p>
                                    <h6 class="mb-0">${{ number_format($progress['team_business'], 2) }}</h6>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-sm-4">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0">Leg 1 (40%) <small class="text-muted">{{ $leg_data['leg1_username'] }}</small></p>
                                    <h6 class="mb-0">$ {{ number_format($leg_data['leg1_business'], 2) }}</h6>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0">Leg 2 (30%) <small class="text-muted">{{ $leg_data['leg2_username'] }}</small></p>
                                    <h6 class="mb-0">$ {{ number_format($leg_data['leg2_business'], 2) }}</h6>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0">Leg 3 (30%) <small class="text-muted">{{ $leg_data['leg3_username'] }}</small></p>
                                    <h6 class="mb-0">$ {{ number_format($leg_data['leg3_business'], 2) }}</h6>
                                </div>
                            </div>
                        </div>

                        @if($active_achiever)
                        <div class="alert alert-success mt-3 mb-0">
                            Active Weekly Salary: <strong>${{ number_format($active_achiever->weekly_salary, 2) }}</strong>
                            &nbsp;|&nbsp; Next Pay: <strong>{{ $active_achiever->return_date ? date('d/m/Y', strtotime($active_achiever->return_date)) : '-' }}</strong>
                            &nbsp;|&nbsp; Weeks Paid: <strong>{{ $active_achiever->weeks_paid }}</strong>
                        </div>
                        @endif

                        <div class="table-responsive mt-4">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reward</th>
                                        <th>Direct</th>
                                        <th>Team</th>
                                        <th>Self Biz</th>
                                        <th>Team Biz</th>
                                        <th>Weekly Salary</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allrewards as $reward)
                                    <tr>
                                        <td>{{ $reward->milestone_order }}</td>
                                        <td>{{ $reward->title ?: ('Reward '.$reward->milestone_order) }}</td>
                                        <td>{{ $reward->required_directs }}</td>
                                        <td>{{ $reward->required_team }}</td>
                                        <td>${{ number_format($reward->required_self_business, 0) }}</td>
                                        <td>${{ number_format(($reward->required_team_business > 0 ? $reward->required_team_business : $reward->turnover_amount), 0) }}</td>
                                        <td>${{ number_format(($reward->weekly_salary > 0 ? $reward->weekly_salary : $reward->cash_reward), 0) }}</td>
                                        <td>
                                            @if(in_array($reward->id, $achieved_ids))
                                                <span class="badge bg-success">Achieved</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
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
@endsection
