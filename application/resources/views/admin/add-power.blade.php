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
		<a href="javascript://">Topup Master</a>
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
						<label for="field-1" class="col-sm-3 control-label">Member To</label>
						<div class="col-sm-5">
							<input name="assigned_to" type="text" class="form-control" id="assigned_to" data-validate="required" placeholder="Member ID" onChange="verifyReferral();"> 
							<span id="membername"></span>
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Power Amount</label>
						<div class="col-sm-5">
							<input name="amount" type="text" class="form-control" id="amount" data-validate="required" placeholder="Power Amount">
						</div>
					</div>
					
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
	<script src="{{ URL::asset('assets/admin/js/custom/add-power.js') }}"></script>
@endsection