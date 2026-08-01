@extends('users.master')
@section('extra')
<style>
    .form-check {
        margin-bottom: -0.5rem;
    }
    
    @media (min-width: 992px) {
        .col-lg-2 {
            flex: 0 0 auto;
            width: 20%;
        }
    }
</style>
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
                            <li class="breadcrumb-item"><a href="javascript:">Referrals & Downline</a></li>
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
                        <h5>{{ $page_titel }}</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Level</th>
                                        <th>Required Referral</th>
                                        <th>Percentage</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alllevel as $level)
                                    <tr>
                                        <th>{{ $level->id }}</th>
                                        <th>Level - {{ $level->id }}</th>
                                        <th>{{ $level->id }} Direct</th>
                                        <th>{{ $level->per }} %</th>
                                        <th>
                                            @if($direct >= $level->id || $a_a_level >= $level->id)
                                                <b style="color: #49eafa;">Achieve</b>
                                            @else
                                                <b style="color: orange;">Pending</b>
                                            @endif
                                        </th>
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
