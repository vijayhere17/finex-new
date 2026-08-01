<!DOCTYPE html PUBLIC "//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<title>Admin Panel - {{ env('APP_NAME') }}</title>

		<link rel="shortcut icon" href="{{ URL::to('/') }}/assets/favicon.png" />

		<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css') }}"  id="style-resource-1">

		<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/css/font-icons/entypo/css/entypo.css') }}"  id="style-resource-2">

		<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/css/font-icons/entypo/css/animation.css') }}"  id="style-resource-3">

		<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/css/neon.css') }}"  id="style-resource-5">

		<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/css/custom.css') }}"  id="style-resource-6">

		<script src="{{ URL::asset('assets/swapadmin/js/jquery-1.10.2.min.js') }}"></script>

		<!-- Loading Mask  -->
		<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/js/loading-mask/jquery.mloading.css') }}">

		<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/js/jvectormap/jquery-jvectormap-1.2.2.css') }}"  id="style-resource-1">

		<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/js/rickshaw/rickshaw.min.css') }}"  id="style-resource-2">
		
		<style>
		    .tile-stats .num {
                font-size: 18px;
                font-weight: bold;
            }
		</style>

		@section('extra')
		@show
	</head>

	<body class="page-body page-fade">

		<div class="page-container">

			<div class="sidebar-menu">

				<header class="logo-env">

					<!-- logo -->
					<div class="logo">
						<a href="{{URL::to('/')}}/swapadmin/home">
						    <img src="{{ URL::asset('assets/images/brand-logonew.png') }}" alt="" style="height: 40px;"/>
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
						<a href="{{ URL::to('/') }}/swapadmin/home">
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
								<a href="{{ URL::to('/') }}/swapadmin/change-password">
									<span class="title">Change Login Password</span>
								</a> 
							</li> 
						</ul> 
					</li>

					<li class="has-sub root-level"> 
						<a href="{{ URL::to('/') }}/swapadmin/coin-rate-set">
							<i class="entypo-cc-sa"></i>
							<span class="title">Rate Management</span>
						</a> 
					</li>
					
					<li class="has-sub root-level"> 
						<a href="{{ URL::to('/') }}/swapadmin/swap-logs">
							<i class="entypo-shuffle"></i>
							<span class="title">Swap Report</span>
						</a> 
					</li>
					
					<li class="has-sub root-level"> 
						<a href="{{ URL::to('/') }}/swapadmin/liqudity-withdrawal">
							<i class="entypo-archive"></i>
							<span class="title">Liqudity Withdrawal</span>
						</a> 
					</li>
					
					<li class="has-sub root-level"> 
						<a href="{{ URL::to('/') }}/swapadmin/logout">
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
									<img src="{{ URL::asset('assets/swapadmin/images/thumb-1.png') }}" alt="" class="img-circle" />
									Welcome <span style="text-transform: capitalize;">{{ Auth::user()->username }}</span>
								</a>
								<ul class="dropdown-menu">
									<!-- Reverse Caret -->
									<li class="caret"></li>
									<!-- Profile sub-links -->
									<li>
										<a href="{{URL::to('/')}}/swapadmin/change-password">
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
								<a href="javascript:onConnect()" class="connect-btn" style="background: indigo; padding: 10px; color: #fff; border-radius: 10px; font-weight: bold;">Conenct Wallet</a>
								<a href="javascript:onDisconnect()" class="connected-btn sel_address" style="display: none; background: #5ab77a; padding: 10px; color: #fff; border-radius: 10px; font-weight: bold;"></a>
							</li>
							<li class="sep"></li>
							<li>
								<a href="{{URL::to('/')}}/swapadmin/c">
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

		<script src="{{ URL::asset('assets/swapadmin/js/gsap/main-gsap.js') }}" id="script-resource-1"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js') }}" id="script-resource-2"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/bootstrap.min.js') }}" id="script-resource-3"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/joinable.js') }}" id="script-resource-4"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/resizeable.js') }}" id="script-resource-5"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/neon-api.js') }}" id="script-resource-6"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/jvectormap/jquery-jvectormap-1.2.2.min.js') }}" id="script-resource-7"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/jquery.validate.min.js') }}" id="script-resource-7"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/jvectormap/jquery-jvectormap-europe-merc-en.js') }}" id="script-resource-8"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/jquery.sparkline.min.js') }}" id="script-resource-9"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/rickshaw/vendor/d3.v3.js') }}" id="script-resource-10"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/rickshaw/rickshaw.min.js') }}" id="script-resource-11"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/raphael-min.js') }}" id="script-resource-12"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/morris.min.js') }}" id="script-resource-13"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/toastr.js') }}" id="script-resource-14"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/neon-custom.js') }}" id="script-resource-16"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/neon-demo.js') }}" id="script-resource-17"></script>

		<script src="{{ URL::asset('assets/swapadmin/js/bootstrap-datepicker.js') }}" id="script-resource-11"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/wysihtml5/wysihtml5-0.4.0pre.min.js') }}" id="script-resource-7"></script>
		<script src="{{ URL::asset('assets/swapadmin/js/wysihtml5/bootstrap-wysihtml5.js') }}" id="script-resource-8"></script>

		<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/js/wysihtml5/bootstrap-wysihtml5.css') }}"  id="style-resource-1">

		<!-- Loading Mask  -->
		<script type="text/javascript" src="{{ URL::asset('assets/web3modal/web3.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('assets/web3modal/index.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('assets/web3modal/index.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('assets/web3modal/provider.index.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('assets/web3modal/fortmatic.js') }}"></script>
        
		<script src="https://cdn.jsdelivr.net/gh/rmunate/PHP2JS@4/src/JS/QuickRequest/QuickRequest.min.js"></script>
		<script src="{{ URL::to('/') }}/assets/jquery/jquery.blockUI.js"></script>
		<script type="text/javascript" src="{{ URL::asset('assets/swapadmin/js/loading-mask/jquery.mloading.js') }}"></script>
		
		<script src="{{ URL::asset('assets/web3modal/conenct.0.1.js') }}"></script>
        
		<script src="{{ URL::asset('assets/swapadmin/js/custom/common.js') }}"></script>
		
		<script>
        const swap_abi = [{"inputs":[{"internalType":"contract IERC20","name":"_tokenA","type":"address"},{"internalType":"contract IERC20","name":"_tokenB","type":"address"},{"internalType":"uint256","name":"_usdtRate","type":"uint256"},{"internalType":"uint256","name":"_alcRate","type":"uint256"},{"internalType":"address","name":"_systemAddress","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":false,"internalType":"uint256","name":"amountA","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"amountB","type":"uint256"}],"name":"LiquidityAdded","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"swapper","type":"address"},{"indexed":true,"internalType":"address","name":"tokenFrom","type":"address"},{"indexed":true,"internalType":"address","name":"tokenTo","type":"address"},{"indexed":false,"internalType":"uint256","name":"amountIn","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"amountOut","type":"uint256"}],"name":"Swap","type":"event"},{"inputs":[{"internalType":"uint256","name":"amountA","type":"uint256"},{"internalType":"uint256","name":"amountB","type":"uint256"}],"name":"addLiquidity","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"alcRate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"allSwaps","outputs":[{"internalType":"address","name":"user","type":"address"},{"internalType":"address","name":"tokenFrom","type":"address"},{"internalType":"address","name":"tokenTo","type":"address"},{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"uint256","name":"amountOut","type":"uint256"},{"internalType":"uint256","name":"timestamp","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"string","name":"token","type":"string"}],"name":"calculate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"claimCoin","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_token","type":"address"},{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"claimTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"numerator","type":"uint256"},{"internalType":"uint256","name":"denominator","type":"uint256"},{"internalType":"address","name":"tokenAddr","type":"address"}],"name":"division","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"getAllSwaps","outputs":[{"components":[{"internalType":"address","name":"user","type":"address"},{"internalType":"address","name":"tokenFrom","type":"address"},{"internalType":"address","name":"tokenTo","type":"address"},{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"uint256","name":"amountOut","type":"uint256"},{"internalType":"uint256","name":"timestamp","type":"uint256"}],"internalType":"struct ALCSwap.SwapTransaction[]","name":"","type":"tuple[]"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"user","type":"address"}],"name":"getUserSwaps","outputs":[{"components":[{"internalType":"address","name":"user","type":"address"},{"internalType":"address","name":"tokenFrom","type":"address"},{"internalType":"address","name":"tokenTo","type":"address"},{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"uint256","name":"amountOut","type":"uint256"},{"internalType":"uint256","name":"timestamp","type":"uint256"}],"internalType":"struct ALCSwap.SwapTransaction[]","name":"","type":"tuple[]"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_alcRate","type":"uint256"}],"name":"setALCRate","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_address","type":"address"}],"name":"setSystemAddress","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_usdtRate","type":"uint256"}],"name":"setUSDTRate","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"string","name":"token","type":"string"},{"internalType":"uint256","name":"expectedOutput","type":"uint256"},{"internalType":"uint256","name":"deadline","type":"uint256"}],"name":"swap","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"systemAddress","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenA","outputs":[{"internalType":"contract IERC20","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"tokenB","outputs":[{"internalType":"contract IERC20","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"usdtRate","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"},{"internalType":"uint256","name":"","type":"uint256"}],"name":"userSwaps","outputs":[{"internalType":"address","name":"user","type":"address"},{"internalType":"address","name":"tokenFrom","type":"address"},{"internalType":"address","name":"tokenTo","type":"address"},{"internalType":"uint256","name":"amountIn","type":"uint256"},{"internalType":"uint256","name":"amountOut","type":"uint256"},{"internalType":"uint256","name":"timestamp","type":"uint256"}],"stateMutability":"view","type":"function"}],
              coin_abi = [{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"constant":true,"inputs":[],"name":"_decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address[]","name":"_user","type":"address[]"},{"internalType":"uint256","name":"value","type":"uint256"}],"name":"airdrop","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"basePercent","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burn","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"getOwner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"maxBurning","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address[]","name":"_user","type":"address[]"},{"internalType":"uint256[]","name":"value","type":"uint256[]"}],"name":"multisender","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"renounceOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalBurning","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"}],            
              coin_contract = '0x73e0D982Bdf7552884E2BEdB5B3C3f182e2AcD90',
              usdt_contract = '0xc2132D05D31c914a87C6611C10748AEb04B58e8F';
              swap_contract = '0xD580572dd3954A484b7817928438C6a65699BE28';
        </script>

		@yield('jscontent')
	</body>
</html>
