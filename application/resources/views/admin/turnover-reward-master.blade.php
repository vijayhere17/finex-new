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
				<div class="panel-title">Reward Qualification Master</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>Title</th>
								<th>Direct</th>
								<th>Team</th>
								<th>Self Biz</th>
								<th>Team Biz</th>
								<th>Weekly Salary</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($rewards as $reward)
							<tr class="reward-row" data-id="{{ $reward->id }}">
								<td>{{ $reward->milestone_order }}</td>
								<td><input type="text" name="title" class="form-control" value="{{ $reward->title }}"></td>
								<td><input type="number" name="required_directs" class="form-control" value="{{ $reward->required_directs }}"></td>
								<td><input type="number" name="required_team" class="form-control" value="{{ $reward->required_team }}"></td>
								<td><input type="number" step="0.01" name="required_self_business" class="form-control" value="{{ $reward->required_self_business }}"></td>
								<td><input type="number" step="0.01" name="required_team_business" class="form-control" value="{{ $reward->required_team_business > 0 ? $reward->required_team_business : $reward->turnover_amount }}"></td>
								<td><input type="number" step="0.01" name="weekly_salary" class="form-control" value="{{ $reward->weekly_salary > 0 ? $reward->weekly_salary : $reward->cash_reward }}"></td>
								<td><button type="button" class="btn btn-black reward-update-btn">Update</button></td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div id="rewardMsg"></div>
			</div>
		</div>

		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">Add New Reward</div>
			</div>
			<div class="panel-body">
				<form id="rewardAddForm" class="form-horizontal form-groups-bordered">
					<div class="form-group">
						<label class="col-sm-3 control-label">Milestone Order</label>
						<div class="col-sm-5">
							<input name="milestone_order" type="number" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Title</label>
						<div class="col-sm-5">
							<input name="title" type="text" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Required Directs</label>
						<div class="col-sm-5">
							<input name="required_directs" type="number" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Required Team</label>
						<div class="col-sm-5">
							<input name="required_team" type="number" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Self Business ($)</label>
						<div class="col-sm-5">
							<input name="required_self_business" type="number" step="0.01" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Team Business ($)</label>
						<div class="col-sm-5">
							<input name="required_team_business" type="number" step="0.01" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Weekly Salary ($)</label>
						<div class="col-sm-5">
							<input name="weekly_salary" type="number" step="0.01" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-5">
							<button type="submit" class="btn btn-black">Add Reward</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<br />
@endsection
@section('jscontent')
<script>
function csrfToken() {
	var el = document.getElementById('token');
	return el ? el.value : '';
}

function postJson(url, data, done) {
	fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': csrfToken(),
			'X-Requested-With': 'XMLHttpRequest'
		},
		body: JSON.stringify(data)
	}).then(function(r) { return r.json(); }).then(done).catch(function(e) {
		done({success: false, error_code: 'NETWORK_ERROR'});
	});
}

document.getElementById('rewardAddForm').addEventListener('submit', function(e) {
	e.preventDefault();
	var f = e.target;
	postJson('{{ URL::to("/admin/add-turnover-reward") }}', {
		milestone_order: f.milestone_order.value,
		title: f.title.value,
		required_directs: f.required_directs.value,
		required_team: f.required_team.value,
		required_self_business: f.required_self_business.value,
		required_team_business: f.required_team_business.value,
		weekly_salary: f.weekly_salary.value
	}, function(res) {
		if(res.success) { location.reload(); } else { document.getElementById('rewardMsg').innerText = 'Error: ' + (res.error_code || 'FAILED'); }
	});
});

document.querySelectorAll('.reward-update-btn').forEach(function(btn) {
	btn.addEventListener('click', function() {
		var row = btn.closest('.reward-row');
		var id = row.getAttribute('data-id');
		postJson('{{ URL::to("/admin/update-turnover-reward") }}/' + id, {
			title: row.querySelector('[name="title"]').value,
			required_directs: row.querySelector('[name="required_directs"]').value,
			required_team: row.querySelector('[name="required_team"]').value,
			required_self_business: row.querySelector('[name="required_self_business"]').value,
			required_team_business: row.querySelector('[name="required_team_business"]').value,
			weekly_salary: row.querySelector('[name="weekly_salary"]').value
		}, function(res) {
			document.getElementById('rewardMsg').innerText = res.success ? 'Updated successfully.' : ('Error: ' + (res.error_code || 'FAILED'));
		});
	});
});
</script>
@endsection
