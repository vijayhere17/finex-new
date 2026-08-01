@extends('swapadmin.master')
@section('title', '')
@section('extra')
@endsection
@section('content')
<ol class="breadcrumb bc-3">
	<li>
		<a href="{{URL::to('/')}}/swapadmin/home"><i class="entypo-home"></i>Home</a>
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
						<label for="field-1" class="col-sm-4 control-label">EDU Rate ($)</label>
						<div class="col-sm-2">
							<input name="afx_c_rate" type="number" class="form-control" id="afx_c_rate" readonly>
						</div>
						<div class="col-sm-2">
							<input name="afx_rate" type="number" class="form-control" id="afx_rate">
						</div>
						<div class="col-sm-4">
						    <input name="SUBMIT" type="submit" class="btn btn-blue" id="sellubmit" value="Update EDU Rate">
						</div>
					</div>

					<div class="form-group">
						<label for="field-1" class="col-sm-4 control-label">USDT Rate ($)</label>
						<div class="col-sm-2">
							<input name="usdt_c_rate" type="number" class="form-control" id="usdt_c_rate" readonly>
						</div>
						<div class="col-sm-2">
							<input name="usdt_rate" type="number" class="form-control" id="usdt_rate">
						</div>
						<div class="col-sm-4">
						    <input name="SUBMIT" type="submit" class="btn btn-warning" id="btnSubmit" value="Update USDT Rate">
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
	<script src="{{ URL::asset('assets/swapadmin/js/custom/coin-rate.0.5.js') }}"></script>
@endsection