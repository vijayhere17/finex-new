@php
    $dashboardCon = app('App\Http\Controllers\Users\DashboardController');
    $earning =  0; //$dashboardCon->mytotalearning();
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
                            <li class="breadcrumb-item"><a href="javascript:">Topup Activation</a></li>
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

            <div class="col-md-6 col-lg-5" style="display: none;">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                @foreach($packages as $data)
                                <div class="price-check border rounded p-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" name="package" class="form-check-input input-primary" id="package_{{ $data->id }}" stakeid="{{ $data->id }}" stakeamount="{{ $data->amount }}" style="margin-top: 12.5px; margin-right: 10px;" onclick="getcalculation();">
                                        <label class="form-check-label d-block" for="package_{{ $data->id }}">
                                            <span class="row align-items-center">
                                                <span class="col-6">
                                                    <span class="h5 mb-0 d-block">Daily {{ $data->percantage }}%</span>
                                                    <span class="text-muted mb-0">Referral Incentive {{ $data->direct_ref }}%</span>
                                                </span>
                                                <span class="col-6 text-end">
                                                    <span class="price-price h4">{{ $data->name }} <span class="text-muted text-sm"> / {{ $data->months }} Months</span></span>
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
            
            <div class="col-md-3"></div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        
                        <div class="alert alert-info">
                            <strong>USDT (BEP20) Live Rate : </strong>$<span>{{ $coin_rate }}</span>
                        </div>
                        
                        <div class="alert alert-warning">
                            <strong>EWallet Balance : </strong>$<span class="ew_balance">0.00</span>
                        </div>

                        <div class="col-md-12">
                            <x-input type="text" name="topup_amount" id="topup_amount" placeholder="Topup Amount" value="" />
                        </div>
                        
                        <div class="col-md-12">
                            <x-input type="text" name="username" id="username" placeholder="Topup User Wallet" value="" />
                            <div class="mb-3" id="membername" style="color: #4680ff; font-weight: 900;"></div>
                        </div>
                        
                        <div id="amount_error" class="text-danger mb-2" style="display: none; font-weight: 600;"></div>

                        <ul class="list-group list-group-flush product-check-list">
                            <li class="list-group-item enable">Daily Return : <span id="txt_daily">0.00%</span></li>
                            <li class="list-group-item enable">Daily Income : $<span id="txt_daily_income">0.00</span></li>
                            <li class="list-group-item enable">1 to 25 Level Earning Bonus</li>
                            <li class="list-group-item enable">Much More Benefits.</li>
                            <li class="list-group-item">Note : Enter a investment amount only $50 multiple</li>
                        </ul>
                    
                        <hr>
                        
                        <button type="submit" class="btn btn-primary btn-submit" style="width: 100%;">Buy Now</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/buy-bot-wallet.0.4.js"></script>
<script>
    // connectwallet();
    
    jQuery('#username').on('change', function(e) 
    {
        var username = $("#username").val();
        
        if(username != '') 
        {
            var reqObj = {
                _token: $("#token").val(),
                sponsor_id: username
            };
        
            blockui();
        
            $.ajax({
                type: 'POST',
                url: BASEPATH + "/check-sponsor-id",
                data: reqObj,
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        $("#membername").text(result.name);
                    } else {
                        erroralert(result.error);
                    }
                    unblockui();
                }
            });
        }
    });
</script>
@endsection
