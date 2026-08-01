<!DOCTYPE html PUBLIC "//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<title>Admin Panel - {{ env('APP_NAME') }}</title>

		<link rel="shortcut icon" href="{{ URL::to('/') }}/assets/images/logo-lg.png" />

		<link rel="stylesheet" href="{{ URL::asset('assets/admin/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css') }}"  id="style-resource-1">

		<link rel="stylesheet" href="{{ URL::asset('assets/admin/css/font-icons/entypo/css/entypo.css') }}"  id="style-resource-2">

		<link rel="stylesheet" href="{{ URL::asset('assets/admin/css/font-icons/entypo/css/animation.css') }}"  id="style-resource-3">

		<link rel="stylesheet" href="{{ URL::asset('assets/admin/css/neon.css') }}"  id="style-resource-5">

		<link rel="stylesheet" href="{{ URL::asset('assets/admin/css/custom.css') }}"  id="style-resource-6">

		<script src="{{ URL::asset('assets/admin/js/jquery-1.10.2.min.js') }}"></script>

		<!-- Loading Mask  -->
		<link rel="stylesheet" href="{{ URL::asset('assets/admin/js/loading-mask/jquery.mloading.css') }}">

		<link rel="stylesheet" href="{{ URL::asset('assets/admin/js/jvectormap/jquery-jvectormap-1.2.2.css') }}"  id="style-resource-1">

		<link rel="stylesheet" href="{{ URL::asset('assets/admin/js/rickshaw/rickshaw.min.css') }}"  id="style-resource-2">

		@section('extra')
		@show
	</head>

	<body class="page-body page-fade">

		<div class="page-container">

			<div class="sidebar-menu">

				<header class="logo-env">

					<!-- logo -->
					<div class="logo">
						<a href="{{URL::to('/')}}/admin/home">
						    <img src="{{ URL::asset('assets/images/logo-lg.png') }}" alt="" style="height: 80px;"/>
						</a>
					</div>
					<!-- logo collapse icon -->

					<div class="sidebar-collapse">
						<a href="#" class="sidebar-collapse-icon with-animation">
						<!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
							<i class="entypo-menu"></i>
						</a>
					</div>

					<!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
					<div class="sidebar-mobile-menu visible-xs">
						<a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
							<i class="entypo-menu"></i>
						</a>
					</div>

				</header>

				<ul id="main-menu" class="">
					
					<li id="search">
						<form method="get" action="javascript://">
							<input type="text" name="q" class="search-input" placeholder="Search something..." />
							<button type="submit"><i class="entypo-search"></i></button>
						</form>
					</li>
					
					<li class="has-sub root-level"> 
						<a href="{{ URL::to('/') }}/admin/home">
							<i class="entypo-gauge"></i>
							<span class="title">Dashboard</span>
						</a> 
					</li>

					<li class="has-sub root-level"> 
						<a href="javascript:">
							<i class="entypo-layout"></i>
							<span class="title">Admin Setting</span>
						</a> 
						<ul> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/change-password">
									<span class="title">Change Login Password</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/coin-rate-set">
									<span class="title">Update Coin Rate</span>
								</a> 
							</li> 
						</ul> 
					</li>

					<li class="has-sub root-level"> 
						<a href="{{ URL::to('/') }}/admin/member-report">
							<i class="entypo-users"></i>
							<span class="title">All Users List</span>
						</a> 
					</li>

					<li class="has-sub root-level"> 
						<a href="javascript:">
							<i class="entypo-newspaper"></i>
							<span class="title">Stake Requests</span>
						</a> 
						<ul> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/stake-request/0/Pending">
									<span class="title">Pending Request</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/stake-request/1/Processing">
									<span class="title">Processing Request</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/stake-request/2/Success">
									<span class="title">Success Request</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/stake-request/3/Faield">
									<span class="title">Failed Request</span>
								</a> 
							</li> 
						</ul> 
					</li>

					
					<li class="has-sub root-level"> 
						<a href="javascript:">
							<i class="entypo-chart-bar"></i>
							<span class="title">Stake Master</span>
						</a> 
						<ul> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/all-packages">
									<span class="title">Stake Package's</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/manual-topup">
									<span class="title">Manual Stake</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/user-staked-report">
									<span class="title">Stake Report</span>
								</a> 
							</li>
						</ul> 
					</li>
										
					<li class="has-sub root-level"> 
						<a href="javascript:">
							<i class="entypo-flash"></i>
							<span class="title">Fund Master</span>
						</a> 
						<ul> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/cradit-debit-master">
									<span class="title">Cradit & Debit Wallet</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/cradit-debit-report">
									<span class="title">History Of Cradit & Debit</span>
								</a> 
							</li> 
						</ul> 
					</li>

					
					<li class="has-sub root-level"> 
						<a href="javascript:">
							<i class="entypo-chart-bar"></i>
							<span class="title">Finex Plan</span>
						</a> 
						<ul> 
							<li><a href="{{ URL::to('/') }}/admin/finex-slots"><span class="title">Slot Management</span></a></li>
							<li><a href="{{ URL::to('/') }}/admin/finex-direct-roi"><span class="title">Direct ROI Monitoring</span></a></li>
							<li><a href="{{ URL::to('/') }}/admin/finex-daily-roi-logs"><span class="title">Daily ROI Logs</span></a></li>
							<li><a href="{{ URL::to('/') }}/admin/finex-level-roi-logs"><span class="title">Level ROI Logs</span></a></li>
							<li><a href="{{ URL::to('/') }}/admin/finex-auto-upgrade-logs"><span class="title">Auto Upgrade Logs</span></a></li>
							<li><a href="{{ URL::to('/') }}/admin/finex-spillover-logs"><span class="title">Spillover Logs</span></a></li>
							<li><a href="{{ URL::to('/') }}/admin/finex-member-slots"><span class="title">Member Slot Status</span></a></li>
							<li><a href="{{ URL::to('/') }}/admin/withdrawal-request/0/Pending-Withdrawals"><span class="title">Withdraw Requests</span></a></li>
						</ul> 
					</li>

<li class="has-sub root-level"> 
						<a href="javascript:">
							<i class="entypo-list"></i>
							<span class="title">Earning Report</span>
						</a> 
						<ul> 
							<li>
								<a href="{{ URL::to('/') }}/admin/earning-report/1/Direct Sponsor Income">
									<span class="title">Direct Sponsor Income</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/earning-report/2/Daily ROI">
									<span class="title">Daily ROI</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/earning-report/8/Booster Income">
									<span class="title">Booster Income</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/earning-report/4/Team Level ROI Income">
									<span class="title">Team Level ROI Income</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/earning-report/7/Reward Salary">
									<span class="title">Reward Salary</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/earning-report/10/Locked Reward Unlock">
									<span class="title">Locked Reward Unlock</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/earning-report/3/Cashback">
									<span class="title">Cashback</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/earning-report/9/Life Time">
									<span class="title">Life Time</span>
								</a>
							</li>
						</ul> 
					</li>

					<li class="has-sub root-level">
						<a href="javascript:">
							<i class="entypo-cog"></i>
							<span class="title">Income Settings</span>
						</a>
						<ul>
							<li>
								<a href="{{ URL::to('/') }}/admin/roi-tier-master">
									<span class="title">ROI Tier Master</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/turnover-reward-master">
									<span class="title">Reward Master</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/turnover-reward-achievers">
									<span class="title">Reward Achievers</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/reward-weekly-salary">
									<span class="title">Weekly Salary</span>
								</a>
							</li>
							<li>
								<a href="{{ URL::to('/') }}/admin/locked-reward-report">
									<span class="title">Locked / Unlocked / Expired</span>
								</a>
							</li>
						</ul>
					</li>
					
					<li class="has-sub root-level"> 
						<a href="{{ URL::to('/') }}/admin/outstanding-balance">
							<i class="entypo-newspaper"></i>
							<span class="title">Outstanding Balance</span>
						</a> 
					</li>
					
					<li class="has-sub root-level"> 
						<a href="javascript:">
							<i class="entypo-archive"></i>
							<span class="title">Withdrawal Requests</span>
						</a> 
						<ul> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/withdrawal-request/0/Pending">
									<span class="title">Pending Request</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/withdrawal-request/1/Processing">
									<span class="title">Processing Request</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/withdrawal-request/2/Success">
									<span class="title">Success Request</span>
								</a> 
							</li> 
							<li> 
								<a href="{{ URL::to('/') }}/admin/withdrawal-request/3/Faield">
									<span class="title">Failed Request</span>
								</a> 
							</li> 
						</ul> 
					</li>
					
					<li><a href="{{ URL::to('/') }}/admin/support-ticket"><i class="entypo-ticket"></i><span>Support Ticket</span></a></li>

					<li class="has-sub root-level"> 
						<a href="{{ URL::to('/') }}/admin/logout">
							<i class="entypo-logout"></i>
							<span class="title">Logout</span>
						</a> 
					</li>
				</ul>
			</div>

			<div class="main-content">
				<div class="row">
					<!-- Profile Info and Notifications -->
					<div class="col-md-6 col-sm-8 clearfix">
						<ul class="user-info pull-left pull-none-xsm">
							<!-- Profile Info -->
							<li class="profile-info dropdown"><!-- add class "pull-right" if you want to place this from right -->
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<img src="{{ URL::asset('assets/admin/images/thumb-1.png') }}" alt="" class="img-circle" />
									Welcome <span style="text-transform: capitalize;">{{ Auth::user()->username }}</span>
								</a>
								<ul class="dropdown-menu">
									<!-- Reverse Caret -->
									<li class="caret"></li>
									<!-- Profile sub-links -->
									<li>
										<a href="{{URL::to('/')}}/admin/change-password">
											<i class="entypo-user"></i>
										    Change Password
										</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
					<!-- Raw Links -->
					<div class="col-md-6 col-sm-4 clearfix hidden-xs">
						<ul class="list-inline links-list pull-right">
							<li>
								<a href="/">Live Site</a>
							</li>
							<li class="sep"></li>
							<li>
								<a href="{{URL::to('/')}}/admin/c">
									Log Out <i class="entypo-logout right"></i>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<hr />

				<input type="hidden" value="{{ URL::to('/') }}" id="basePath"/>
				<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}"/>

				@yield('content')

			    <!-- Footer -->
				<footer class="main">&copy;
					{{date("Y")}} <strong>Admin Panel | Powered By <?php echo $domain_name = $_SERVER['HTTP_HOST'];?>.</strong>
					<div id="google_translate_element" style="float:right;"></div>
				</footer>
				<!-- Chat Histories -->
			<!-- Chat Histories -->
		</div>

		<script src="{{ URL::asset('assets/admin/js/gsap/main-gsap.js') }}" id="script-resource-1"></script>
		<script src="{{ URL::asset('assets/admin/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js') }}" id="script-resource-2"></script>
		<script src="{{ URL::asset('assets/admin/js/bootstrap.min.js') }}" id="script-resource-3"></script>
		<script src="{{ URL::asset('assets/admin/js/joinable.js') }}" id="script-resource-4"></script>
		<script src="{{ URL::asset('assets/admin/js/resizeable.js') }}" id="script-resource-5"></script>
		<script src="{{ URL::asset('assets/admin/js/neon-api.js') }}" id="script-resource-6"></script>
		<script src="{{ URL::asset('assets/admin/js/jvectormap/jquery-jvectormap-1.2.2.min.js') }}" id="script-resource-7"></script>
		<script src="{{ URL::asset('assets/admin/js/jquery.validate.min.js') }}" id="script-resource-7"></script>
		<script src="{{ URL::asset('assets/admin/js/jvectormap/jquery-jvectormap-europe-merc-en.js') }}" id="script-resource-8"></script>
		<script src="{{ URL::asset('assets/admin/js/jquery.sparkline.min.js') }}" id="script-resource-9"></script>
		<script src="{{ URL::asset('assets/admin/js/rickshaw/vendor/d3.v3.js') }}" id="script-resource-10"></script>
		<script src="{{ URL::asset('assets/admin/js/rickshaw/rickshaw.min.js') }}" id="script-resource-11"></script>
		<script src="{{ URL::asset('assets/admin/js/raphael-min.js') }}" id="script-resource-12"></script>
		<script src="{{ URL::asset('assets/admin/js/morris.min.js') }}" id="script-resource-13"></script>
		<script src="{{ URL::asset('assets/admin/js/toastr.js') }}" id="script-resource-14"></script>
		<script src="{{ URL::asset('assets/admin/js/neon-custom.js') }}" id="script-resource-16"></script>
		<script src="{{ URL::asset('assets/admin/js/neon-demo.js') }}" id="script-resource-17"></script>

		<script src="{{ URL::asset('assets/admin/js/bootstrap-datepicker.js') }}" id="script-resource-11"></script>
		<script src="{{ URL::asset('assets/admin/js/wysihtml5/wysihtml5-0.4.0pre.min.js') }}" id="script-resource-7"></script>
		<script src="{{ URL::asset('assets/admin/js/wysihtml5/bootstrap-wysihtml5.js') }}" id="script-resource-8"></script>

		<link rel="stylesheet" href="{{ URL::asset('assets/admin/js/wysihtml5/bootstrap-wysihtml5.css') }}"  id="style-resource-1">

		<!-- Loading Mask  -->
		<script src="https://cdn.jsdelivr.net/gh/rmunate/PHP2JS@4/src/JS/QuickRequest/QuickRequest.min.js"></script>
		<script src="{{ URL::to('/') }}/assets/jquery/jquery.blockUI.js"></script>
		<script type="text/javascript" src="{{ URL::asset('assets/admin/js/loading-mask/jquery.mloading.js') }}"></script>
		<script src="{{ URL::asset('assets/admin/js/custom/common.js') }}"></script>

		@yield('jscontent')
	</body>
</html>
