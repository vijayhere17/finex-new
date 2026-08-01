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
                        <h5>My Referrals List</h5>
                    </div>
                    <div class="card-body table-border-style">
                        
                        <form>
                            <div class="form-group mb-0">
                                <div class="row mb-2">
                                    <div class="col-lg-4">
                                        <div class="border card p-3">
                                            <div class="form-check">
                                                <input type="radio" name="paid_search" class="form-check-input input-primary" id="alluser" value="" checked onclick="oTable.draw();">
                                                <label class="form-check-label d-block" for="alluser">
                                                    <span><span class="h5 d-block">All User</span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="border card p-3">
                                            <div class="form-check">
                                                <input type="radio" name="paid_search" class="form-check-input input-primary" id="paiduser" value="1" onclick="oTable.draw();">
                                                <label class="form-check-label d-block" for="paiduser">
                                                    <span><span class="h5 d-block">Active User</span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="border card p-3">
                                            <div class="form-check">
                                                <input type="radio" name="paid_search" class="form-check-input input-primary" id="unpaiduser" value="0" onclick="oTable.draw();">
                                                <label class="form-check-label d-block" for="unpaiduser">
                                                    <span><span class="h5 d-block">Inactive User</span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <div class="table-responsive">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User Name</th>
                                        <th>Activation On</th>
                                        <th>Total Topup</th>
                                        <th>Status</th>
                                        <th>Registered Date</th>
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
<script src="{{ URL::to('/') }}/assets/js/users/my-referral.0.4.js"></script>
@endsection
