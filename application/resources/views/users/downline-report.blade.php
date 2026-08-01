@extends('users.master')
@section('extra')
<style>
    .form-check {
        margin-bottom: -0.5rem;
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
                            <li class="breadcrumb-item"><a href="javascript:">Referral & Downline</a></li>
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
                        <h5>Downline Report</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <form>
                            <div class="form-group mb-0">
                                <div class="row mb-2">
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <select class="form-control" name="level" id="level" onchange="oTable.draw()">
                                                <option value="0">All Levels</option>
                                                @for($i = 1; $i <= 25; $i++)
                                                    <option value="{{ $i }}">Level {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>    
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <select class="form-control" name="paidsearch" id="paidsearch" onchange="oTable.draw()">
                                                <option value="">All</option>
                                                <option value="1">Active User</option>
                                                <option value="0">Inactive User</option>
                                            </select>
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
                                        <th>User Details</th>
                                        <th>Activation On</th>
                                        <th>Total Topup</th>
                                        <th>Status</th>
                                        <th>Registered Date</th>
                                        <th>Referral Details</th>
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
<script src="{{ URL::to('/') }}/assets/js/users/downline-report.1.1.js"></script>
@endsection
