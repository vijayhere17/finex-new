@php use Illuminate\Support\Facades\Auth; @endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $page_titel }}</title>

    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- [Favicon] icon -->
    <link rel="icon" type="image/png" href="{{ URL::to('/') }}/assets/images/logo-lg.png">
     <!-- [Font] Family -->
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

    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/plugins/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/common/alert/cute-style.css" >

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/bg-animation.css" >

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/gold-theme.css" >

    <style>
        .pc-header .pc-head-link .pc-h-badge {
            position: absolute;
            top: 7px;
            right: 7px;
            border-radius: 50%;
            font-size: 5px;
            z-index: 9;
        }
        
        .blockUI {
            z-index: 10000 !important;
        }

        .pc-header .user-avtar {
            width: 25px;
            border-radius: 50%;
        }

        /* Finex text logo (replaces old image logo) */
        .fx-brand {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            text-decoration: none !important;
            line-height: 1.05;
        }
        .fx-brand-name {
            font-weight: 800;
            font-size: 1.55rem;
            letter-spacing: 0.02em;
            color: var(--gt-gold-1, #f8ce4e);
        }
        .fx-brand-tag {
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gt-gold-2, #e6ad1f);
            margin-top: 2px;
        }
        .auth-main .fx-brand { align-items: center; }
        .auth-main .fx-brand-name { font-size: 2rem; }
        .auth-main .fx-brand-tag { font-size: 0.7rem; letter-spacing: 0.14em; }
    </style>    
    @section('extra')
    @show
</head>
<body data-pc-preset="preset-8" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
    
    <div class="glow-container">
       <div class="ball"></div>
       <div class="ball" style="--delay:-12s;--size:0.35;--speed:25s;"></div>
       <div class="ball" style="--delay:-10s;--size:0.3;--speed:15s;"></div> 
    </div>

    <input type="hidden" value="{{ URL::to('/') }}" id="basePath"/>
    <input type="hidden" id="token" value="{{ csrf_token() }}"/>

    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <!--<video preload="none" class="background-video" autoplay muted loop>-->
        <!--    <source src="{{ URL::to('/') }}/assets/menu-bg.mp4" type="video/mp4">-->
        <!--    <source src="{{ URL::to('/') }}/assets/menu-bg.mp4" type="video/ogg">-->
        <!--</video>-->
        
        <div class="navbar-wrapper">
            <div class="m-header">
                <div class="b-brand text-primary">
                   <a href="{{ URL::to('/') }}/dashboard" class="fx-brand">
                        <span class="fx-brand-name">Finex</span>
                        <span class="fx-brand-tag">Trade Smart Confident</span>
                   </a>
                </div>
            </div>
            
            <div class="navbar-content">
                <div class="card pc-user-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="{{ URL::to('/') }}/assets/images/user/avatar-1.jpg" alt="user-image" class="user-avtar wid-45 rounded-circle" />
                            </div>
                            <div class="flex-grow-1 ms-3 me-2">
                                <h6 class="mb-0">{{ obscureAddress(Auth::user()->username) }}</h6>
                                <small>{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</small>
                            </div>
                            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-sort-outline"></use>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <ul class="pc-navbar">
                    <li class="pc-item pc-caption">
                        <label>Navigation</label>
                    </li>

                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/dashboard" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-status-up"></use></svg></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/buy-robo" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-cpu-charge"></use></svg></span>
                            <span class="pc-mtext">Slot Activation</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/my-slots" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-notification-status"></use></svg></span>
                            <span class="pc-mtext">My Slots</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/my-referral" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-profile-2user-outline"></use></svg></span>
                            <span class="pc-mtext">Direct Referrals</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/downline-report/A" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-profile-2user-outline"></use></svg></span>
                            <span class="pc-mtext">Genealogy</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/daily-roi-history" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-dollar-square"></use></svg></span>
                            <span class="pc-mtext">Daily ROI History</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/level-roi-income" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-dollar-square"></use></svg></span>
                            <span class="pc-mtext">Level ROI Income</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/auto-upgrade" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-cpu-charge"></use></svg></span>
                            <span class="pc-mtext">Auto Upgrade</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/transactions" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-notification-status"></use></svg></span>
                            <span class="pc-mtext">Transactions</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/earning-wallet" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-dollar-square"></use></svg></span>
                            <span class="pc-mtext">Wallet</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/new-withdrawal" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-direct-inbox"></use></svg></span>
                            <span class="pc-mtext">Withdraw</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ URL::to('/') }}/update-profile" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-setting-outline"></use></svg></span>
                            <span class="pc-mtext">Profile</span>
                        </a>
                    </li>
                    <li class="pc-item pc-hasmenu">
                        <a href="javascript:" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-24-support"></use></svg></span>
                            <span class="pc-mtext">Support</span>
                            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="pc-submenu">
                            <li class="pc-item"><a class="pc-link" href="{{ URL::to('/') }}/create-ticket">Create Ticket</a></li>
                            <li class="pc-item"><a class="pc-link" href="{{ URL::to('/') }}/ticket/I/Open-Ticket">Open Ticket</a></li>
                            <li class="pc-item"><a class="pc-link" href="{{ URL::to('/') }}/ticket/II/On-Hold-Ticket">On-Hold Ticket</a></li>
                            <li class="pc-item"><a class="pc-link" href="{{ URL::to('/') }}/ticket/III/Close-Ticket">Close Ticket</a></li>
                        </ul>
                    </li>
                    <li class="pc-item mb-3">
                        <a href="{{ URL::to('/') }}/sign-out" class="pc-link">
                            <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-logout-1-outline"></use></svg></span>
                            <span class="pc-mtext">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- [ Sidebar Menu ] end --> 

    <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper"> 
            <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none m-0 trig-drp-search" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-search-normal-1"></use>
                            </svg>
                        </a>
                        <div class="dropdown-menu pc-h-dropdown drp-search">
                            <form class="px-3 py-2">
                                <input type="search" class="form-control border-0 shadow-none" placeholder="Search here. . ." />
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- [Mobile Media Block end] -->
            <div class="ms-auto">
                <ul class="list-unstyled">
                    <!-- <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sun-1"></use>
                            </svg>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                            <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-moon"></use>
                                </svg>
                                <span>Dark</span>
                            </a>
                            <a href="#!" class="dropdown-item" onclick="layout_change('light')">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-sun-1"></use>
                                </svg>
                                <span>Light</span>
                            </a>
                        </div>
                    </li> -->

                   <!--<li class="pc-h-item">-->
                   <!--      <a href="javascript:" class="pc-head-link me-0" data-bs-toggle="offcanvas" data-bs-target="#announcement" aria-controls="announcement">-->
                   <!--         <svg class="pc-icon">-->
                   <!--             <use xlink:href="#custom-flash"></use>-->
                   <!--         </svg>-->
                   <!--     </a>-->
                   <!-- </li>-->

                    <!-- <li class="dropdown pc-h-item">
                        <a href="javascript:" class="pc-head-link me-0" data-bs-target="#connectmodal" data-bs-toggle="modal">
                            <i class="fas fa-wallet"></i>
                            <span class="badge bg-danger pc-h-badge" id="wallet_status">&nbsp;</span>
                        </a>
                    </li> -->
            
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                            <img id="wallet" src="{{ URL::to('/') }}/assets/images/d-wallet.png" alt="user-image" class="user-avtar" />
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header d-flex align-items-center justify-content-between">
                                <h5 class="m-0">Connect Wallet</h5>
                            </div>
                            <div class="dropdown-body">
                                <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                                    <div class="d-flex mb-1">
                                        <div class="flex-shrink-0">
                                            <img src="{{ URL::to('/') }}/assets/images/bnb.svg" alt="user-image" class="user-avtar wid-35" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 wallet"></h6>
                                            <span class="main_balance">0.00000000 BNB</span>
                                        </div>
                                    </div>
                                
                                    <hr class="border-secondary border-opacity-50" />
                                    <div class="connect-wallet">  
                                        <div class="d-grid mb-3">
                                            <a href="javascript:connectwallet()" class="btn btn-primary">
                                                <svg class="pc-icon me-2">
                                                    <use xlink:href="#custom-logout-1-outline"></use>
                                                </svg>
                                                Connect Wallet
                                            </a>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div> 
        </div>    
    </header>

    <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement" aria-labelledby="announcementLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="announcementLabel">What's new announcement?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <!--<p class="text-span">Today</p>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                        <div class="badge bg-light-success f-12">Big News</div>
                        <p class="mb-0 text-muted">2 min ago</p>
                        <span class="badge dot bg-warning"></span>
                    </div>

                    <h5 class="mb-3">Able Pro is Redesigned</h5>
                    <p class="text-muted">Able Pro is completely renowed with high aesthetics User Interface.</p>
                    <img src="{{ URL::to('/') }}/assets/images/layout/img-announcement-1.png" alt="img" class="img-fluid mb-3" />

                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid">
                                <a class="btn btn-outline-secondary" href="https://1.envato.market/c/1289604/275988/4415?subId1=phoenixcoded&amp;u=https%3A%2F%2Fthemeforest.net%2Fitem%2Fable-pro-responsive-bootstrap-4-admin-template%2F19300403" target="_blank">Check Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
        </div>
    </div>
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    @yield('content')
    <!-- [ Main Content ] end -->
    
    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col my-1">
                    <p class="m-0">Copyright &#169; {{ date("Y") }} Finex. All Rights Reserved · <a href="https://www.finex.world" target="_blank" rel="noopener">www.finex.world</a></p>
                </div>
            </div>
        </div>
    </footer> 

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- [Page Specific JS] start -->
    <script src="{{ URL::to('/') }}/assets/js/plugins/apexcharts.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pages/dashboard-default.js"></script>
    <!-- [Page Specific JS] end -->

    <!-- Required Js -->
    <script src="{{ URL::to('/') }}/assets/js/plugins/popper.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/simplebar.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/bootstrap.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/fonts/custom-font.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pcoded.0.1.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/feather.min.js"></script> 

    <script src="{{ URL::to('/') }}/assets/js/plugins/jquery.dataTables.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/dataTables.bootstrap5.min.js"></script>

    <script src="{{ URL::to('/') }}/assets/common/alert/cute-alert.js"></script>

    <script src="{{ URL::to('/') }}/assets/common/js/QuickRequest.min.js"></script>

    <script src="{{ URL::to('/') }}/assets/common/js/web3.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/ethers-v4.min.js"></script>
    
    <script src="{{ URL::to('/') }}/assets/common/js/jquery.blockUI.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/common.0.8.js"></script>

    @yield('jscontent')
</body>
</html>
