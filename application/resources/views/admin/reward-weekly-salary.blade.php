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
				<div class="panel-title">Active Weekly Salary Schedules (Highest Reward Only)</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Member</th>
								<th>Reward</th>
								<th>Weekly Salary</th>
								<th>Next Pay</th>
								<th>Last Paid</th>
								<th>Weeks Paid</th>
							</tr>
						</thead>
						<tbody>
							@foreach($achievers as $a)
							<tr>
								<td>{{ $a->member ? obscureAddress($a->member->username) : $a->member_id }}</td>
								<td>{{ $a->reward ? ($a->reward->title ?: ('#'.$a->reward->milestone_order)) : $a->reward_id }}</td>
								<td>${{ number_format($a->weekly_salary, 2) }}</td>
								<td>{{ $a->return_date ? date('d/m/Y', strtotime($a->return_date)) : '-' }}</td>
								<td>{{ $a->last_paid_at ? date('d/m/Y H:i:s', strtotime($a->last_paid_at)) : '-' }}</td>
								<td>{{ $a->weeks_paid }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">Recent Reward Salary Wallet Credits</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Member ID</th>
								<th>Description</th>
								<th>Amount</th>
								<th>Txn</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							@foreach($salary_logs as $log)
							<tr>
								<td>{{ $log->member_id }}</td>
								<td>{{ $log->description }}</td>
								<td>${{ number_format($log->amount, 4) }}</td>
								<td>{{ $log->txn_type == 1 ? 'Credit' : ($log->txn_type == 3 ? 'Flush' : 'Debit') }}</td>
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
