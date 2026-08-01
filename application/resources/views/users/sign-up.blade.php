@php
    if (isset($_REQUEST["ref"])) 
    { 
        $refer = $_REQUEST["ref"];
    } 
    else 
    {
        $refer = '';
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $page_titel }}</title>

    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- [Favicon] icon -->
    <link rel="icon" type="image/png" href="{{ URL::to('/') }}/assets/images/logo-lg.png"> <!-- [Font] Family -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/inter/inter.css" id="main-font-link" />

    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/tabler-icons.min.css" >

    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/feather.css" >

    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/fontawesome.css" >

    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/material.css" >

    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/style.css" id="main-style-link" >
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/style-preset.css" >

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/common/alert/cute-style.css" >

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/bg-animation.css" >

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/common/css/unicons.css">

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/gold-theme.css" >

    <style>
        .toggle-password {
            float: right;
            cursor: pointer;
            margin-right: 20px;
            margin-top: -33px;
        }
        .fx-brand {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none !important;
            line-height: 1.05;
        }
        .fx-brand-name {
            font-weight: 800;
            font-size: 2rem;
            letter-spacing: 0.02em;
            color: var(--gt-gold-1, #f8ce4e);
        }
        .fx-brand-tag {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--gt-gold-2, #e6ad1f);
            margin-top: 2px;
        }

        .message {
            position: absolute;
            top: -200px;
            left: 50%;
            transform: translate(-50%, 0%);
            width: 300px;
            background: white;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            font-weight: 300;
            color: #2c2928;
            opacity: 0;
            transition: top 0.3s cubic-bezier(0.31, 0.25, 0.5, 1.5), opacity 0.2s ease-in-out;
        }
        .message .check {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translate(-50%, -50%) scale(4);
            width: 120px;
            height: 110px;
            background: #e6ad1f;
            color: #0d0b07;
            font-size: 3.8rem;
            padding-top: 10px;
            border-radius: 50%;
            opacity: 0;
            transition: transform 0.2s 0.25s cubic-bezier(0.31, 0.25, 0.5, 1.5), opacity 0.1s 0.25s ease-in-out;
        }
        .message .scaledown {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        .message p {
            font-size: 1.1rem;
            margin: 25px 0px;
            padding: 0;
        }
        .message p:nth-child(2) {
            font-size: 2.3rem;
            margin: 40px 0px 0px 0px;
        }
        .message #ok {
            position: relative;
            color: #0d0b07;
            border: 0;
            background: #e6ad1f;
            width: 100%;
            height: 50px;
            border-radius: 6px;
            font-size: 1.2rem;
            font-weight: 600;
            transition: background 0.2s ease;
            outline: none;
        }
        .message #ok:hover {
            background: #f8ce4e;
        }
        .message #ok:active {
            background: #a5731c;
        }

        .comein {
            top: 150px;
            opacity: 1;
        }
    </style>
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-8" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
    
    <div class="glow-container">
       <div class="ball"></div>
       <div class="ball" style="--delay:-12s;--size:0.35;--speed:25s;"></div>
       <div class="ball" style="--delay:-10s;--size:0.3;--speed:15s;"></div> 
    </div>

    <input type="hidden" value="{{ URL::to('/') }}" id="basePath"/>
    <input type="hidden" id="token" value="{{ csrf_token() }}"/>
    
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <div class="auth-main" id="auth-main">
        <div class="auth-wrapper v1">
            <div class="auth-form">
                <div class="card my-5">
                    <div class="card-body">
                        <div class="text-center">
                           <x-logo />
                        </div>

                        <div class="saprator my-3">
                            <span>Finex</span>
                        </div>

                        <h4 class="text-center f-w-500 mb-3">Create new account</h4>

                        <div class="alert alert-success text-center py-2 mb-3">
    <strong>🎉 Registration is FREE!</strong><br>
    Activate your first <strong>Slot</strong> after registration to start earning.
</div>

                        <div class="row">
                            <div class="col-sm-12">
                                <x-input type="text" name="sponsor_id" id="sponsor_id" placeholder="Sponsor ID" value="" />
                                <div class="mb-3" id="sponsor_name" style="color: #4680ff; font-weight: 900;"></div>
                            </div>    
                          
                            <div class="col-sm-6" style="display: none;"> 
                                <x-select name="leg" id="leg" label="Select Leg" :options="['L' => 'Left', 'R' => 'Right']"/>
                            </div>

                            <div class="col-sm-6" style="display: none;">
                                <x-input type="text" name="firstname" id="firstname" placeholder="Firstname" value="" />
                            </div>    
                          
                            <div class="col-sm-6" style="display: none;"> 
                                <x-input type="text" name="lastname" id="lastname" placeholder="Lastname" value="" />
                            </div>
                            
                            <div class="col-sm-12" style="display: none;"> 
                                <x-input type="text" name="email" id="email" placeholder="Email" value="" />
                            </div>
                            
                            <div class="col-sm-12">
                                <x-input type="text" name="userwallet" id="userwallet" placeholder="Connect wallet address" value="" />
                            </div>  
                            
                            <div class="col-sm-12">
                                <div class="d-flex mt-1 justify-content-between align-items-center">
                                    <x-checkbox type="checkbox" name="terms" id="terms" ischeck="0" label="I agree to all the Terms & Condition"/>
                                </div>
                            </div>    
                        </div>    

                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-info btn-connect">Connect Wallet</button>
                            <button type="button" class="btn btn-primary btn-submit" style="display: none;">
    Create Free Account
</button>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mt-4">
                            <h6 class="f-w-500 mb-0">Already have an account?</h6> <a href="{{ URL::to('/') }}/sign-in" class="link-primary">Sign In</a>
                        </div>                       
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='message'>
        <div class='check'>&#10004;</div>
        <p>Success</p>
        <p>Dear user, <br> You have successfully signed up with <b>Finex</b>. Login your account and explore your future!</p>
        <button id='ok'>OK</button>
    </div>

    <!-- Required Js -->
    <script src="{{ URL::to('/') }}/assets/common/js/jquery.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/popper.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/simplebar.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/bootstrap.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/fonts/custom-font.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pcoded.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/feather.min.js"></script>

    <script src="{{ URL::to('/') }}/assets/common/js/ScrollMagic.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/particles.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/particles_set.0.3.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/alert/cute-alert.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/web3.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/ethers-v4.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/jquery.blockUI.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/modernizr.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/common.0.8.js"></script>
    <script>
        window.REG_FEE_CONFIG = {
            to_address: @json($to_address ?? config('income.deposit_wallet')),
            usdt_con_addr: @json($usdt_con_addr ?? config('income.usdt_contract')),
            usdt_con_abi: @json($usdt_con_abi ?? '[]'),
            registration_fee: 0
        };
    </script>
    <script src="{{ URL::to('/') }}/assets/js/users/sign-up.0.8.js?v=3"></script>

    <script>
        $("#sponsor_id").val("<?php echo $refer; ?>").change();
        
        $(".toggle-password").click(function() {
            
            $(this).toggleClass("fa-eye fa-eye-slash");
            
            input = $(this).parent().find("input");
            
            if (input.attr("type") == "password") {
                
                input.attr("type", "text");
                
            } else {
                
                input.attr("type", "password");
                
            }
        });
    </script>
</body>
</html>