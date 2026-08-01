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
                        <h5>Update Your Profile Details.</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form>
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <x-input type="text" name="username" id="username" placeholder="Username" value="data" />
                                    </div>
                                    <div class="col-md-12">
                                        <x-input type="text" name="firstname" id="firstname" placeholder="Firstname" value="" />
                                    </div>
                                    <div class="col-md-12">
                                        <x-input type="text" name="lastname" id="lastname" placeholder="Lastname" value=""/>
                                    </div>
                                    <div class="col-md-12">
                                        <x-input type="email" name="email" id="email" placeholder="Email" value="" />
                                    </div>
                                    @if($user->kit_id > 0)
                                        @if($user->is_authenticator == 1)
                                            <div class="col-md-12">
                                                <x-input type="text" name="otp" id="otp" placeholder="Google 2FA Code" value=""/>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </form>
                        </div>    
                    </div>
                    <div class="card-footer">
                        <center>
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
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/my-profile.1.3.js"></script>
@endsection
