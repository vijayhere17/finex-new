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
                        <h5>DMC Achievement</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Rank</th>
                                        <th>Left DMC Agent</th>
                                        <th>Right DMC Agent</th>
                                        <!--<th>Target Days</th>-->
                                        <th>Daily</th>
                                        <th>Days</th>
                                        <th>Status</th>
                                        <th>Achieve Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $left_pair = $left_dmc; $right_pair = $right_dmc; @endphp
                                    @foreach($ranks as $rank)
                                    @php
                                        $dmcCon = app('App\Http\Controllers\Users\DMCController');
                                        $status = $dmcCon->findachievement($user_id, $rank->id);
                                        
                                        if($rank->target_days > 0){
                                            if($rank->left_dmc <= $left_dmc_f){
                                                $show_left_pair  = 0;
                                                $left_pair -= $rank->left_dmc;
                                            }else if($rank->left_dmc > $left_dmc_f){
                                                $show_left_pair  = $rank->left_dmc-$left_dmc_f;
                                                $left_pair = 0;
                                            }
                                            
                                            if($rank->right_dmc <= $right_dmc_f){
                                                $show_right_pair  = 0;
                                                $right_pair -= $rank->right_dmc;
                                            }else if($rank->right_dmc > $right_dmc_f){
                                                $show_right_pair  = $rank->right_dmc-$right_dmc_f;
                                                $right_pair = 0;
                                            }
                                        }
                                        else{
                                            if($rank->left_dmc <= $left_pair){
                                                $show_left_pair  = 0;
                                                $left_pair -= $rank->left_dmc;
                                            }else if($rank->left_dmc > $left_pair){
                                                $show_left_pair  = $rank->left_dmc-$left_pair;
                                                $left_pair = 0;
                                            }
                                            
                                            if($rank->right_dmc <= $right_pair){
                                                $show_right_pair  = 0;
                                                $right_pair -= $rank->right_dmc;
                                            }else if($rank->right_dmc > $right_pair){
                                                $show_right_pair  = $rank->right_dmc-$right_pair;
                                                $right_pair = 0;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $rank->id }}</td>
                                        <td>{{ $rank->rank }}</td>
                                        <td>{{ $rank->left_dmc }} | ({{ $show_left_pair }} Remaining)</td>
                                        <td>{{ $rank->right_dmc }} | ({{ $show_right_pair }} Remaining)</td>
                                        <!-- <td>
                                            @if($rank->target_days > 0)
                                                {{ $rank->target_days }} Days
                                            @else
                                                Life Time
                                            @endif
                                        </td> -->
                                        <td>${{ $rank->daily }}</td>
                                        <td>
                                            @if($status == null)
                                                {{ $rank->days }} Days
                                            @else
                                                {{ $status->days }} Remaining
                                            @endif      
                                        </td>
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
