<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<link rel="shortcut icon" href="{{ URL::to('/') }}/assets/favicon.png" /> 

	<title>Login - : {{ env('APP_NAME') }}</title>

	<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css') }}"  id="style-resource-1">
	<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/css/font-icons/entypo/css/entypo.css') }}"  id="style-resource-2">
	<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/css/font-icons/entypo/css/animation.css') }}"  id="style-resource-3">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic"  id="style-resource-4">
	<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/css/neon.css') }}"  id="style-resource-5">
	<link rel="stylesheet" href="{{ URL::asset('assets/swapadmin/css/custom.css') }}"  id="style-resource-6">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	
	<script src="{{ URL::asset('assets/swapadmin/js/jquery-1.10.2.min.js') }}"></script>

	<style>
		.errorDiv{
		    background: red;
			line-height: 36px;
			border-radius: 8px;
			color: #fff;
		}
	</style>
</head>
<body class="page-body login-page is-lockscreen login-form-fall">

<div class="login-container">

    <div class="login-header">

		<div class="login-content">
		  	<!-- progress bar indicator -->
			<div class="login-progressbar-indicator">
				<h3>0%</h3>
				<span>logging in...</span>
			</div>
	    </div>

    </div>

	<div class="login-form" id="login-form">

		<div class="login-content">

			<div name="form_lockscreen" id="form_lockscreen">
				<input type="hidden" value="{{ URL::to('/') }}" id="basePath"/>
				<input type="hidden" id="token" value="<?php echo csrf_token() ?>"/>

				<div class="form-group lockscreen-input">
					<div class="lockscreen-thumb">
					  <img src="{{ URL::asset('assets/swapadmin/images/lockscreen-user.png') }}" width="140" height="137" class="img-circle" />
					  <div class="lockscreen-progress-indicator">0%</div>
				    </div>

					<div class="lockscreen-details">
						<h4>Administrator</h4>
						<span data-login-text="logging in..." id="login_text">logged off</span>
					</div>
				</div>

				<div id="errorDiv" class="form-group errorDiv" style="display:none;">
					<span id="errorMsg">
						Error goes here...
					</span>
				</div>

		        <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-addon"> <i class="entypo-user"></i> </div>
                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" autocomplete="off" />
                  </div>
				</div>
		          <br>
				<div class="form-group">
	              <div class="input-group">
                    <div class="input-group-addon"> <i class="entypo-key"></i> </div>
		            <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off" />
                  </div>
		        </div>
			    <div class="form-group">
					<button id="btnLogin" type="submit" class="btn btn-primary btn-block btn-login" style="text-align: center;">
						Log in
						<i class="entypo-login"></i>
					</button>
				</div>
		    </div>
	    </div>
	</div>

</div>

<script src="{{ URL::asset('assets/swapadmin/js/gsap/main-gsap.js') }}" id="script-resource-1"></script>
<script src="{{ URL::asset('assets/swapadmin/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js') }}" id="script-resource-2"></script>
<script src="{{ URL::asset('assets/swapadmin/js/bootstrap.min.js') }}" id="script-resource-3"></script>
<script src="{{ URL::asset('assets/swapadmin/js/joinable.js') }}" id="script-resource-4"></script>
<script src="{{ URL::asset('assets/swapadmin/js/resizeable.js') }}" id="script-resource-5"></script>
<script src="{{ URL::asset('assets/swapadmin/js/neon-api.js') }}" id="script-resource-6"></script>
<script src="{{ URL::asset('assets/swapadmin/js/jquery.validate.min.js') }}" id="script-resource-7"></script>
<script src="{{ URL::asset('assets/swapadmin/js/neon-login.js') }}" id="script-resource-8"></script>
<script src="{{ URL::asset('assets/swapadmin/js/neon-custom.js') }}" id="script-resource-9"></script>
<script src="{{ URL::asset('assets/swapadmin/js/neon-demo.js') }}" id="script-resource-10"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{ URL::asset('assets/swapadmin/js/custom/common.js') }}"></script>
<script src="{{ URL::asset('assets/swapadmin/js/custom/login.1.0.js') }}"></script>

</body>
</html>