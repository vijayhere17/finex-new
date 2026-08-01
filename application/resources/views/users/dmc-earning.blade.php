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
                            <li class="breadcrumb-item"><a href="javascript:">Stake & Earning</a></li>
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

            <div class="col-md-6 col-xl-4">
                <div class="card social-widget-card bg-info">
                    <div class="card-body p-3">
                        <span class="m-t-10">Total Credit</span>
                        <h3 class="text-white m-0 cradit">0.00000000</h3>
                        <i class="fab fa-bitcoin" style="margin-top: -7px;"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card social-widget-card bg-danger">
                    <div class="card-body p-3">
                        <span class="m-t-10">Total Debit</span>
                        <h3 class="text-white m-0 debit">0.00000000</h3>
                        <i class="fab fa-bitcoin" style="margin-top: -7px;"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card bg-primary available-balance-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-0 text-white text-opacity-75">Available Balance</p>
                                <h4 class="mb-0 text-white balance">0.00000000</h4>
                            </div>
                            <div class="avtar">
                                <i class="ti ti-arrows-left-right f-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
            
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Stake Earning</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>BMYT Rate</th>
                                        <th>Earning BMYT</th>
                                        <th>Txn. Type</th>
                                        <th>Txn. Date</th>
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
<script src="{{ URL::to('/') }}/assets/js/users/dmc-earning.0.2.js"></script>
@endsection
