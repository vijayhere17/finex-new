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
						<label for="field-1" class="col-sm-3 control-label">Instant Withdrawal Status</label>
						<div class="col-sm-5">
							<select class="form-control" id="pw_status" name="pw_status">
							    <option value="0" <?php if($object->pw_status == 0) { echo 'selected'; } ?>>On</option>
							    <option value="1" <?php if($object->pw_status == 1) { echo 'selected'; } ?>>Off</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Instant Withdrawal Weekly Limit</label>
						<div class="col-sm-5">
							<input name="pw_weekly_limit" type="number" class="form-control" id="pw_weekly_limit" value="{{ $object->pw_weekly_limit }}">
						</div>
					</div>
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Instant Minimum Withdrawal</label>
						<div class="col-sm-5">
							<input name="pw_minimum" type="number" class="form-control" id="pw_minimum" value="{{ $object->pw_min_amount }}">
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-5">
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
	<script src="{{ URL::asset('assets/admin/js/custom/pw-settings.js') }}"></script>
@endsection