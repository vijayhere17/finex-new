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
                            <h2 class="mb-0">Earning Transfer to DMC Wallet</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <!-- [ form-element ] start -->
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Earning Transfer to DMC Wallet</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form>
                                <div class="row g-4">
                                    
                                    <div class="col-md-12">
                                        <div class="card bg-primary available-balance-card">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <p class="mb-0 text-white text-opacity-75">Available Balance</p>
                                                        <h4 class="mb-0 text-white balance">{{ $balance }}</h4>
                                                    </div>
                                                    <div class="avtar">
                                                        <i class="ti ti-arrows-left-right f-18"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <x-input type="text" name="amount" id="amount" placeholder="Transfer Amount" value="{{ $transfer_amount }}"/>
                                    </div>
                                    <div class="col-md-12" style="display: none;">
                                        <x-input type="text" name="otp" id="otp" placeholder="One - Time Password" value="346789"/>
                                    </div>
                                </div>
                            </form>
                        </div>    
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12 col-xxl-12" id="divtimer" style="display: none;">
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                                    <use xlink:href="#custom-calendar-1"></use>
                                </svg>
                                <div style="font-size: 18px;"> Next transfer open on -> <span id="dmc_timer" style="font-weight: 900;">00:00:00</span></div>
                            </div>
                        </div>    
                        <center id="divbutton" style="display: none;">
                            <!--<button type="submit" class="btn btn-warning btn-otp-submit" style="width: 100%;">Get OTP</button>-->
                            <button type="submit" class="btn btn-primary btn-submit" style="width: 100%;">Submit</button>
                        </center>
                    </div>   
                </div>
            </div>  
            <div class="col-md-2"></div>  
        </div>
    </div>
</div>

<!-- 1350 minutes (one day minute) -->

@endsection
@section('jscontent')
@if($next_transfer != null)
    <script>
        $("#divtimer").show(); $("#divbutton").hide();
    
        var countDownDate = new Date("<?php echo date('M d, Y 00:00:00', strtotime($next_transfer. ' + 10080 minutes')); ?>").getTime();
        
        var x = setInterval(function() {
            
            var now = new Date().getTime();
            
            var distance = countDownDate - now;
    
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
            document.getElementById("dmc_timer").innerHTML = days+' Days '+hours+ ":"+minutes+":"+seconds;
    
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("dmc_timer").innerHTML = "0 Days 00:00:00";
                $("#divtimer").hide(); $("#divbutton").show();
            }
        }, 1000);
    </script>
@else    
    <script>$("#divtimer").hide(); $("#divbutton").show();</script>
@endif

<script src="{{ URL::to('/') }}/assets/js/users/earning-transfer.0.3.js"></script>

<script>setInterval(function(){ document.getElementById("amount").readOnly = true; }, 10); </script>
@endsection
