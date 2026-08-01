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

            <div class="col-md-6 col-lg-4">
                <div class="card bg-gray-800 dropbox-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="text-white bmyt_balance">0.00000000 BMYT</h5>
                            <h4 class="text-white ">Balance</h4>
                        </div>
                        <div class="mb-3">
                            <div class="avtar avtar-s">
                                <img src="{{ URL::to('/') }}/assets/images/icon.png" alt="user-image" class="user-avtar wid-35" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body bg-light-primary text-center" style="padding-bottom: 3px;">
                        <div class="avtar avtar-s bg-primary">
                            <i class="ti ti-wallet f-20 text-white"></i>
                        </div>
                        <h5 class="mb-1 mt-2">Wallet Address</h5>
                        <p class="text-muted mt-2"><a href="https://ecroxscan.com/address/{{ $wallet_address }}" target="_blank">{{ $wallet_address }}</a></p>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>BMYT Wallet</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Txn. Hash</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Amount</th>
                                        <th>Txn. Date</th>
                                    </tr>
                                </thead>
                                <tbody id="listtxn">
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
<script src="{{ URL::to('/') }}/assets/js/users/bmyt-wallet.0.4.js"></script>
@endsection
