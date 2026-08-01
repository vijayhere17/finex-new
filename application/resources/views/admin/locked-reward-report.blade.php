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
				<div class="panel-title">Member Locked / Unlocked / Expired Reward</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Member</th>
								<th>Locked</th>
								<th>Unlocked</th>
								<th>Expired</th>
								<th>Lock Date</th>
								<th>Expiry Date</th>
								<th>Remaining Days</th>
							</tr>
						</thead>
						<tbody>
							@foreach($members as $m)
							@php
								$remaining = 0;
								if(!empty($m->locked_reward_expiry_date) && (float)$m->locked_reward_bonus > 0) {
									$remaining = max(0, (int) ceil((strtotime($m->locked_reward_expiry_date) - time()) / 86400));
								}
							@endphp
							<tr>
								<td>{{ obscureAddress($m->username) }}</td>
								<td>${{ number_format($m->locked_reward_bonus, 2) }}</td>
								<td>${{ number_format($m->unlocked_reward_bonus, 2) }}</td>
								<td>${{ number_format($m->expired_reward_bonus, 2) }}</td>
								<td>{{ $m->locked_reward_lock_date ? date('d/m/Y H:i:s', strtotime($m->locked_reward_lock_date)) : '-' }}</td>
								<td>{{ $m->locked_reward_expiry_date ? date('d/m/Y H:i:s', strtotime($m->locked_reward_expiry_date)) : '-' }}</td>
								<td>{{ $remaining }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">Recent Locked Reward Unlocks (No Fee / 100%)</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Member ID</th>
								<th>Description</th>
								<th>Amount</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							@foreach($unlock_logs as $log)
							<tr>
								<td>{{ $log->member_id }}</td>
								<td>{{ $log->description }}</td>
								<td>${{ number_format($log->amount, 4) }}</td>
								<td>{{ date('d/m/Y H:i:s', strtotime($log->created_at)) }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<br />
@endsection
@section('jscontent')
@endsection
