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
                        <h5>{{ $page_titel }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form>
                                <div class="row g-4">
                                    @if($is_setup == 0)
                                        <div class="col-md-12" align="center">  
                                            <img src="<?php echo $qrcode; ?>" style="max-width: 175px;"><br>
                						    <div style="margin-top: 10px;">Account token (Key):<br><strong><?php echo $secret; ?></strong></div>
                						    <p><strong>Scan the QR Code</strong></p>
                                        </div>
                                        
                                        <input type="hidden" id="secret" value="<?php echo $secret; ?>">
                                       
                                        <div class="col-md-12">
                                            <x-input type="text" name="code" id="code" placeholder="Enter the 2FA code from your 2FA app here..." value="" />
                                        </div>
                                        
                                        <div class="col-md-12" align="center">
                                            <p style="text-align: justify;">
            							        <span>The first step is to download the Google Authenticator app for your Android or iOS device. If you need help getting started, please see </span>
            							        <a href="http://support.google.com/accounts/bin/answer.py?hl=en&amp;answer=1066447" target="_blank" rel="noopener noreferrer">Google's Support Page.</a>
            							    </p>
            							    <p style="text-align: justify;">
            							        <span>If you do not have access to the Play Store or App Store, there are other options for getting Google Authenticator:</span> <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en_IN&gl=US" target="_blank">Android Download</a>, <a href="https://chrome.google.com/webstore/detail/authenticator/bhghoamapcdpbohphigoooaddinpkbai?hl=en" target="_blank" rel="noopener noreferrer">Google Chrome Plugin</a>, or <a href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" target="_blank" rel="noopener noreferrer">iTunes App Store.</a>
            							    </p>
                                            <p class="tip" style="text-align: justify;"><i>The token will not be shown again after 2FA is enabled. If you have multiple devices, add your account token to all of them before clicking enable. (Note: Your Account Token will change each time you reload your browser.)</i></p>
                                        </div>
                                    @else
                                        <div class="col-md-12" align="center">
                                            <h5 class="card-title">Use Google's Android or iPhone app for adding token-based 2FA</h5>
                                            
                                            <h5 class="card-title" style="margin-top: 20px;">-: Protected action :-</h4>
                                            <p style="margin-bottom: 0.5rem;">Update Profile</p>
                                            <!--<p style="margin-bottom: 0.5rem;">Transfer Earning</p>-->
                                            <p style="margin-bottom: 0.5rem;">Withdrawal Confirmation</p>
                                        </div>
                                        
                                        <div class="col-md-12" align="center" style="color: #8bc34a; margin-top: 10px;"><h5 class="card-title">Google Auth Enabled</h5></div>
                                    @endif
                                </div>
                            </form>
                        </div>    
                    </div>
                    @if($is_setup == 0)
                    <div class="card-footer">
                        <center>
                            <button type="submit" class="btn btn-primary btn-submit">Enable</button>
                        </center>
                    </div>   
                    @endif    
                </div>
            </div>  
            <div class="col-md-2"></div>  
        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/secure-account.js"></script>
@endsection
