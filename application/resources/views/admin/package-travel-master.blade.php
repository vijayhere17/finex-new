@extends('admin.master')
@section('title', '')
@section('extra')
@endsection
@section('content')
<ol class="breadcrumb bc-3">
	<li>
		<a href="{{URL::to('/')}}/admin/home"><i class="entypo-home"></i>Home</a>
	</li>
	<li class="active">
		<strong>{{ $page_titel }}</strong>
	</li>
</ol>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">{{ $page_titel }}</div>
			</div>
			<div class="panel-body">
				<div name="form1" class="form-horizontal form-groups-bordered validate" id="form1" role="form">
					<input type="hidden" id="hdnMemberId" value="" />
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Package Name</label>
						<div class="col-sm-5">
							<input name="name" type="text" class="form-control" id="name" data-validate="required" placeholder="Package Name">
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Package Amount</label>
						<div class="col-sm-5">
							<input name="amount" type="num" class="form-control" id="amount" data-validate="required" placeholder="Package Amount">
						</div>
					</div>
					
					<input name="coin" type="hidden" id="coin" value="0">
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Percentage (%)</label>
						<div class="col-sm-5">
							<input name="percantage" type="num" class="form-control" id="percantage" data-validate="required" placeholder="Package Percantage">
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Months</label>
						<div class="col-sm-5">
							<input name="months" type="num" class="form-control" id="months" data-validate="required" placeholder="Package Months">
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Direct Referral</label>
						<div class="col-sm-5">
							<input name="direct_ref" type="num" class="form-control" id="direct_ref" data-validate="required" placeholder="Package Direct Referral">
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Locking</label>
						<div class="col-sm-5">
							<input name="locking" type="num" class="form-control" id="locking" data-validate="required" placeholder="Package Locking">
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Total DMC</label>
						<div class="col-sm-5">
							<input name="dmc" type="num" class="form-control" id="dmc" data-validate="required" placeholder="Total DMC">
						</div>
					</div>
					
					<input name="dmc_commission" type="hidden" id="dmc_commission" value="0">
					
					<input name="left_dmc" type="hidden" id="left_dmc" value="0">
					
					<input name="right_dmc" type="hidden" id="right_dmc" value="0">
					
					<div class="form-group">
					    <div class="col-sm-offset-3 col-sm-5">
    					    <input name="SUBMIT" type="submit" class="btn btn-black" id="btnSubmit" value="Submit">
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
	<script src="{{ URL::asset('assets/admin/js/custom/package-travel-master.0.1.js') }}"></script>
@endsection