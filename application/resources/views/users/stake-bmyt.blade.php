@php
    $dashboardCon = app('App\Http\Controllers\Users\DashboardController');
    $earning =  $dashboardCon->mytotalearning();
@endphp
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
                            <li class="breadcrumb-item"><a href="javascript:">Staking & Wallet</a></li>
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

            <div class="col-md-6 col-lg-5">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                @foreach($packages as $data)
                                <div class="price-check border rounded p-3 mb-3">
                                    <div class="form-check">
                                        <input <?php if($data->amount > 10){ if($earning < $data->amount){ echo 'disabled="disabled"'; } } ?>  type="radio" name="package" class="form-check-input input-primary" id="package_{{ $data->id }}" stakeid="{{ $data->id }}" stakeamount="{{ $data->amount }}" bonus="{{ $data->bonus }}" stakecoin="{{ $data->coin }}" staketype="{{ $data->ptype }}" percantage="{{ $data->percantage }}" style="margin-top: 12.5px; margin-right: 10px;" onclick="getcalculationbmyt();">
                                        <label class="form-check-label d-block" for="package_{{ $data->id }}">
                                            <span class="row align-items-center">
                                                <span class="col-6">
                                                    <span class="h5 mb-0 d-block">Bonus {{ $data->bonus }}%</span>
                                                    <span class="text-muted mb-0">Referral Incentive {{ $data->direct_ref }}%</span>
                                                </span>
                                                <span class="col-6 text-end">
                                                    <span class="price-price h4">{{ $data->name }} <span class="text-muted text-sm"> / 25 Months</span></span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div> 
                                @endforeach 
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush product-check-list">
                            <li class="list-group-item enable">Stake Amount : $<span id="txt_amount">0.00</span></li>
                            <li class="list-group-item enable">Bonus : $<span id="txt_bonus">0.00</span></li>
                            <li class="list-group-item enable">BMYT Rate : $<span>{{ $coin_rate }}</span></li>
                            <li class="list-group-item enable">Stake BMYT Coin : <span id="txt_stake_bmyt">0.00000000</span></li>
                            <li class="list-group-item enable"><span id="stype">Monthly</span> Release Stake : <span id="txt_release_bmyt">0.00000000</span></li>
                        </ul>
                        <hr>
                        <button type="submit" class="btn btn-primary btn-submit" style="width: 100%;">Stake Now</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/stake-bmyt.0.27.js"></script>
@endsection
