@extends('admin.master')
@section('title', '')
@section('extra')
@endsection
@section('content')
<input type="hidden" id="member_id" value="{{ $member->id }}"/>

<ol class="breadcrumb bc-3">
	<li>
		<a href="{{URL::to('/')}}/admin/home"><i class="entypo-home"></i>Home</a>
	</li>
	<li>
		<a href="javascript://">Admin</a>
	</li>
	<li>
		<a href="javascript://">All Users</a>
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
					<div id="errorDiv" class="form-group errorDiv" style="display:none;">
						<span id="errorMsg">
							Error goes here...
						</span>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Username</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="uesrname" value="{{ $member->username }}">
						</div>
					</div>
									
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">First Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="firstname" value="{{ $member->firstname }}">
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Last Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="lastname" value="{{ $member->lastname }}">
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">ID Status</label>
						<div class="col-sm-5">
							<select class="form-control" id="status">
								<option value="0" <?php if($member->status == 0){ echo 'selected'; } ?>>Un Block</option>
								<option value="1" <?php if($member->status == 1){ echo 'selected'; } ?>>Block</option>
                            </select>
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">Withdrawal Status</label>
						<div class="col-sm-5">
						    <select class="form-control" id="w_status">
								<option value="0" <?php if($member->w_status == 0){ echo 'selected'; } ?>>Active</option>
								<option value="1" <?php if($member->w_status == 1){ echo 'selected'; } ?>>In Active</option>
                            </select>
						</div>
					</div>
					
					<div class="form-group">
						<label for="field-1" class="col-sm-3 control-label">EWallet Topup</label>
						<div class="col-sm-5">
						    <select class="form-control" id="wt_status">
								<option value="0" <?php if($member->is_topup == 0){ echo 'selected'; } ?>>Off</option>
								<option value="1" <?php if($member->is_topup == 1){ echo 'selected'; } ?>>On</option>
                            </select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">Registration Fee</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" value="${{ number_format((float)($member->registration_fee ?? 1), 2) }}" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Locked Reward</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" value="${{ number_format((float)($member->locked_reward_bonus ?? 0), 2) }}" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Unlocked Reward</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" value="${{ number_format((float)($member->unlocked_reward_bonus ?? 0), 2) }}" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Expired Reward</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" value="${{ number_format((float)($member->expired_reward_bonus ?? 0), 2) }}" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Reward Expiry</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" value="{{ !empty($member->locked_reward_expiry_date) ? date('d/m/Y H:i:s', strtotime($member->locked_reward_expiry_date)) : '-' }}" readonly>
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
	<script src="{{ URL::asset('assets/admin/js/custom/edit-member.0.2.js') }}"></script>
@endsection