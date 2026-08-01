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
	<li>
		<a href="javascript://">Withdrawal Requests</a>
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
				<div class="panel-options"></div>
			</div>
			<div class="panel-body">
				<div name="form1" class="form-horizontal form-groups-bordered validate" id="form1" role="form">
				    <div class="form-group">
						<div class="col-sm-12">
							<table id="tblData" border="0" width="100%" align="center" class="table table-bordered datatable">
								<thead>
									<tr class="seprator">
										<th>#</th>
										<th>Withdrawal Request</th>
										<th>Withdrawal Mode</th>
										<th>Wallet</th>
										<th>Name</th>
										<th>Amount ($)</th>
										<th>Admin Charge ($)</th>
										<th>Net Amount ($)</th>
										<th>Coin Rate ($)</th>
										<th>Payable Coin</th>
										<th>Wallet Address</th>
										<th>Txn. Hash</th>
										<th>&nbsp;</th>
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
	<script src="{{ URL::asset('assets/admin/js/custom/withdrawal-report.0.9.js') }}"></script>
	
	<div class="modal fade" id="myModal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" onclick="closeapprovedtxnModel();">&times;</button>
                    <h4 class="modal-title">Update Transaction Hash</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="hdnwithid" value="0">
                    <div name="form1" class="form-horizontal form-groups-bordered validate" id="form1" role="form">
    					<div class="form-group">
    						<label for="field-1" class="col-sm-3 control-label">Transaction Hash</label>
    						<div class="col-sm-9">
    							<input name="hash" type="text" class="form-control" id="hash">
    						</div>
    					</div>
    				</div>
                </div>
                <div class="modal-footer">
                    <input name="SUBMIT" type="submit" class="btn btn-success" id="btnSubmit" value="Submit" onclick="getSubmitTxnHash();">
                </div>
            </div>
        </div>
    </div>
@endsection
