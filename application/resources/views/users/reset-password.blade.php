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
                            <li class="breadcrumb-item"><a href="javascript:">My Account</a></li>
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
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Reset Your Login Password.</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form>
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <x-input type="password" name="old_password" id="v" placeholder="Old Password" value="" />
                                    </div>
                                    <div class="col-md-12">
                                        <x-input type="password" name="new_password" id="new_password" placeholder="New Password" value="" />
                                    </div>
                                    <div class="col-md-12">
                                        <x-input type="password" name="con_password" id="con_password" placeholder="Confirm Password" value=""/>
                                    </div>
                                </div>
                            </form>
                        </div>    
                    </div>
                    <div class="card-footer">
                        <center>
                            <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                        </center>
                    </div>   
                </div>
            </div>  
            <div class="col-md-2"></div>  
        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/reset-password.js"></script>
@endsection
