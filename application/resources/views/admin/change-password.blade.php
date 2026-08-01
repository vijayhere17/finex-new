@extends('admin.master')
@section('title', '')
@section('extra')
@endsection
@section('content')
<ol class="breadcrumb bc-3">
	<li>
		<a href="{{URL::to('/')}}/admin/home"><i class="entypo-home"></i>Home</a>
	</li>
	<li>
		<a href="javascript://">Admin</a>
	</li>
	<li class="active">
		<strong>{{ $title }}</strong>
	</li>
</ol>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">{{ $title }}</div>
				<div class="panel-options"></div>
			</div>
			<div class="panel-body">
				<div name="form1" class="form-horizontal form-groups-bordered validate" id="form1" role="form">
					<div id="errorDiv" class="form-group errorDiv" style="display:none;">
						<span id="errorMsg">
							Error goes here...
						</span>
					</div>
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Current password </label>
						<div class="col-sm-5">
							<input name="old_password" type="password" class="form-control" id="old_password"  data-validate="required" data-message-required="Please enter current password.">
						</div>
					</div>
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">New password </label>
						<div class="col-sm-5">
							<input name="new_password" type="password" class="form-control" id="new_password" data-validate="required" data-message-required="Please enter new password.">
						</div>
					</div>
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Confirm password </label>
						<div class="col-sm-5">
							<input name="repeat_password" type="password" class="form-control" id="repeat_password" data-validate="required" data-message-required="Please confirm new password.">
						</div>
					</div>
					<div class="form-group"><div class="col-sm-offset-3 col-sm-5">
					  <input name="SUBMIT" type="submit" class="btn btn-blue" id="btnSubmit" value="Submit">
					</div>
				  </div>
			  </div>
			</div>
		</div>
	</div>
</div>
<br />
@endsection
@section('jscontent')
	<script src="{{ URL::asset('assets/admin/js/custom/change-password.js') }}"></script>
@endsection