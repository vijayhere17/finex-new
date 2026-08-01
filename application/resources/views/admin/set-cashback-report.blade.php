@extends('admin.master')
@section('title', '')
@section('extra')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/responsive/1.0.2/css/dataTables.responsive.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"/>
<style>
	.dataTables_length {
        display: none;
	}
	.dt-buttons {
		margin-top: 16px;
	}
</style>
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
						<label for="field-1" class="col-sm-3 control-label">Member To</label>
						<div class="col-sm-5">
							<input name="assigned_to" type="text" class="form-control" id="assigned_to" data-validate="required" placeholder="Member ID" onChange="verifyReferral();"> <span id="membername"></span>
						</div>
					</div>
					
					<div class="form-group">
					    <div class="col-sm-offset-3 col-sm-5">
    					    <input name="SUBMIT" type="submit" class="btn btn-black" id="btnSubmit" value="Submit">
    					</div>
				    </div>
				    
				    <div class="form-group">
				        <label for="field-1" class="col-sm-12 control-label" style="font-size: 15px; text-align: left;">Cashback Users</label> 
				    </div>   
				    
				    <div class="form-group">
					    <div class="col-sm-12">
    					    <table id="tblData" border="0" width="100%" align="center" class="table table-bordered datatable">
								<thead>
									<tr class="seprator">
										<th>#</th>
										<th>Username</th>
										<th>Name</th>
										<th>Email</th>
										<th>Mobile</th>
										<th>Total Stake ($)</th>
										<th>Activation On</th>
										<th>Sign Up On</th>
										<th>Process On</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
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
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/responsive/1.0.2/js/dataTables.responsive.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
	<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
	<script type="text/javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
	<script src="{{ URL::asset('assets/admin/js/custom/set-cashback-report.js') }}"></script>
	<script src="{{ URL::asset('assets/admin/js/custom/cashback-users.js') }}"></script>
@endsection