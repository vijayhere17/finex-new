@extends('users.master')
@section('extra')
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
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
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <!-- [ form-element ] start -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Salary Achievement</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="row g-3 mt-0">
                            <div class="col-sm-4">
                                <div class="bg-body p-3 rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-primary rounded-circle">
                                                <span class="visually-hidden">Power Leg (60%)</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="mb-0">Power Leg (60%)</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0">$ {{ $leg_data['leg_1_business'] }} <small class="text-muted">{{ $leg_data['leg_1_username'] }}</small></h6>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-body p-3 rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-warning rounded-circle">
                                                <span class="visually-hidden">Power Leg (20%)</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="mb-0">Power Leg (20%)</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0">$ {{ $leg_data['leg_2_business'] }} <small class="text-muted">{{ $leg_data['leg_2_username'] }}</small>
                                    </h6>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-body p-3 rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-success rounded-circle">
                                                <span class="visually-hidden">Power Leg (20%)</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="mb-0">Power Leg (20%)</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0">$ {{ $leg_data['leg_3_business'] }} <small class="text-muted">{{ $leg_data['leg_3_username'] }}</small>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive mt-4">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Rank</th>
                                        <th>Business</th>
                                        <th>Weekly Bonus</th>
                                        <th>Instead Reward</th>
                                        <th>Status</th>
                                        <th>Achieve Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allsalary as $rank)
                                    
                                    @php
                                        $salaryCon = app('App\Http\Controllers\Users\SalaryController');
                                        $status = $salaryCon->getstatus($user_id, $rank->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $rank->id }}</td>
                                        <td>{{ $rank->rank }}</td>
                                        <td>${{ $rank->business }}</td>
                                        <td>${{ $rank->bonus }}</td>
                                        <td>${{ $rank->instead_reward }}</td>
                                        <td>
                                            @if($status == null)
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-success">Achieve</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($status == null)
                                                --/--/---- --:--:--
                                            @else
                                                {{ date("d/m/Y H:i:s", strtotime($status->created_at)) }}
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
