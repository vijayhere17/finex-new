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
                            <li class="breadcrumb-item"><a href="javascript:">Tourism</a></li>
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
                        <h5>{{ $page_titel }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form>
                                <div class="row g-4">
                                    <div class="col-sm-6"> 
                                        <x-select name="tourtype" id="tourtype" label="Tour Type" :options="['' => 'Select Tour Type', 'I' => 'Internation Package(4 night 5 days)', 'D' => 'Domastic Package(3 night 4 days)']"/>
                                    </div>
                                    
                                    <div class="col-sm-6"> 
                                        <x-select name="doctype" id="doctype" label="Document Type" :options="['' => 'Document Type', 'passport' => 'Passport (For Internation)', 'aadhar-card' => 'Aadhar Card (For Domastic)', 'driving-licence' => 'Driving Licence (For Domastic)']"/>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-input type="file" name="front_doc" id="front_doc" placeholder="Document Front Image" value="" />
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-input type="file" name="front_doc" id="back_doc" placeholder="Document Back Image" value="" />
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-input type="date" name="from_date" id="from_date" placeholder="From Date" value="" />
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-input type="date" name="to_date" id="to_date" placeholder="Return Date" value="" />
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="d-flex mt-1 justify-content-between align-items-center">
                                            <x-checkbox type="checkbox" name="terms" id="terms" ischeck="0" label="I accept the below Terms and Conditions" />
                                        </div>
                                    </div>    
                                    
                                    <div class="col-md-12">
                                        <ul style="list-style:none">
                                            <li>1. You have to inform prior when you want to redeem the voucher.</li>
                                            <!-- <li>2. For Goa Package, traveling cost is not included. You have to reach the hotel by own. Company will credit Rs. 1600/- to your wallet.</li> -->
                                            <li>2. If you want to visit other place which is not included into the package, you have to pay for it externally.</li>
                                            <li>3. If you reach to hotel before checkin time, you must have to follow the instructions of hotel staff.</li>
                                            <li>4. If you have children (age above 5 years), you must have to inform and pay to company before.</li>
                                        </ul>
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
        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/create-voucher.js"></script>
@endsection
