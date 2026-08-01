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

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/gold-theme.css" >

    <style>
        #particles-js {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        body {
            margin: 0;
            padding: 0;
            overflow: scroll; 
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

    <div id="particles-js"></div> <!-- Particles background -->

    <input type="hidden" value="{{ URL::to('/') }}" id="basePath"/>
    <input type="hidden" id="token" value="{{ csrf_token() }}"/>
    
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <div class="auth-main">
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

                        <h4 class="text-center f-w-500 mb-3">Forgot Your Account password</h4>

                        <x-input type="text" name="username" id="username" placeholder="Enter username" value="" />

                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-primary btn-submit">Submit</button>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mt-4">
                            <h6 class="f-w-500 mb-0">Already forgot password?</h6> <a href="{{ URL::to('/') }}/sign-in" class="link-primary">Sign In</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Required Js -->
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/popper.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/simplebar.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/bootstrap.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/fonts/custom-font.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pcoded.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/feather.min.js"></script>

    <script src="{{ URL::to('/') }}/assets/common/js/ScrollMagic.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/particles.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/particles_set.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/alert/cute-alert.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/jquery.blockUI.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/common.0.2.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/users/forgot-password.js"></script>
</body>
</html>