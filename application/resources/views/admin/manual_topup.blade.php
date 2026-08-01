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
		<a href="javascript://">Stake Master</a>
	</li>
	<li class="active">
		<strong>Manual Stake</strong>
	</li>
</ol>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">Manual Stake</div>
			</div>
			<div class="panel-body">
				<div name="form1" class="form-horizontal form-groups-bordered validate" id="form1" role="form">
					
				    <input type="hidden" id="hdnMemberId" value="" />
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Wallet To</label>
						<div class="col-sm-5">
							<input name="assigned_to" type="text" class="form-control" id="assigned_to" data-validate="required" placeholder="Wallet ID" onChange="verifyReferral();" style="margin-bottom: 15px;">
							<span id="membername"></span>
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Topup Amount</label>
						<div class="col-sm-5">
							<input name="amount" type="number" class="form-control" id="amount"  data-validate="required" placeholder="Amount">
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Topup Type</label>
						<div class="col-sm-5">
							<select id="topup_type" class="form-control">
							    <option value="">Select</option>
								<option value="0">Normal Topup</option>
								<!--<option value="1">Leader Topup (Only Topup)</option>-->
								<!--<option value="2">ROI Topup (Topup & Daily ROI)</option>-->
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Description </label>
						<div class="col-sm-5">
							<input name="description" type="text" class="form-control" id="description"  data-validate="required" placeholder="Description">
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
	<script src="{{ URL::asset('assets/admin/js/custom/manaul-topup.js') }}"></script>
	
	<script>
	    function verifyReferral()
	    {
            var assigned_to = $("#assigned_to").val();
            
            if(assigned_to != '') 
            {
                var reqObj = {
                    _token: $("#token").val(),
                    sponsor_id: assigned_to
                };
            
                showMask();
            
                $.ajax({
                    type: 'POST',
                    url: BASEPATH + "/check-sponsor-id",
                    data: reqObj,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            $("#membername").text(result.name);
                        } else {
                            showError(result.error);
                        }
                        hideMask();
                    }
                });
            }
        }
	</script>
@endsection