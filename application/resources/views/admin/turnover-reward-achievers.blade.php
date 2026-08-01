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
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Member</th>
								<th>Reward</th>
								<th>Weekly Salary</th>
								<th>Directs</th>
								<th>Team</th>
								<th>Self Biz</th>
								<th>Team Biz</th>
								<th>Leg 1</th>
								<th>Leg 2</th>
								<th>Leg 3</th>
								<th>Status</th>
								<th>Next Pay</th>
								<th>Weeks Paid</th>
								<th>Achieved On</th>
							</tr>
						</thead>
						<tbody>
							@foreach($achievers as $a)
							<tr>
								<td>{{ $a->member ? obscureAddress($a->member->username) : $a->member_id }}</td>
								<td>{{ $a->reward ? ($a->reward->title ?: ('#'.$a->reward->milestone_order)) : $a->reward_id }}</td>
								<td>${{ number_format($a->weekly_salary > 0 ? $a->weekly_salary : $a->cash_reward, 2) }}</td>
								<td>{{ $a->directs_count }}</td>
								<td>{{ $a->team_count }}</td>
								<td>${{ number_format($a->self_business, 2) }}</td>
								<td>${{ number_format($a->team_business, 2) }}</td>
								<td>${{ number_format($a->leg1_business, 2) }}</td>
								<td>${{ number_format($a->leg2_business, 2) }}</td>
								<td>${{ number_format($a->leg3_business, 2) }}</td>
								<td>
									@if((int)$a->status === 0)
										<span class="label label-success">Active Salary</span>
									@else
										<span class="label label-default">Superseded</span>
									@endif
								</td>
								<td>{{ $a->return_date ? date('d/m/Y', strtotime($a->return_date)) : '-' }}</td>
								<td>{{ $a->weeks_paid }}</td>
								<td>{{ date('d/m/Y H:i:s', strtotime($a->created_at)) }}</td>
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
