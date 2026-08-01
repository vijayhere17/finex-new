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
                            <li class="breadcrumb-item"><a href="javascript:">24/7 Support</a></li>
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
                        <h5>Create Ticket</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form>
                                <div class="row g-4">
                                    <div class="col-sm-12"> 
                                        <x-select name="type" id="type" label="Ticket Type" :options="['' => 'Select Ticket Type', 'General Help' => 'General Help', 'Profile Update' => 'Profile Update', 'Topup ID' => 'Topup ID', 'Reward Achievement' => 'Reward Achievement', 'Withdrawal' => 'Withdrawal', 'Others' => 'Others']"/>
                                    </div>
                                    <div class="col-md-12">
                                        <x-input type="text" name="title" id="title" placeholder="Title" value="" />
                                    </div>
                                    <div class="col-md-12">
                                        <x-input type="text" name="desc" id="desc" placeholder="Message" value=""/>
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
<script src="{{ URL::to('/') }}/assets/js/users/create-ticket.js"></script>
@endsection
