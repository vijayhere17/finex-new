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
				<div class="panel-title">Existing ROI Tiers</div>
			</div>
			<div class="panel-body">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Min Amount ($)</th>
							<th>Max Amount ($, blank = no limit)</th>
							<th>Daily Percent (%)</th>
							<th>Active</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach($tiers as $tier)
						<tr class="roi-tier-row" data-id="{{ $tier->id }}">
							<td><input type="number" step="0.01" name="min_amount" class="form-control" value="{{ $tier->min_amount }}"></td>
							<td><input type="number" step="0.01" name="max_amount" class="form-control" value="{{ $tier->max_amount }}"></td>
							<td><input type="number" step="0.001" name="daily_percent" class="form-control" value="{{ $tier->daily_percent }}"></td>
							<td>
								<select name="is_active" class="form-control">
									<option value="1" @if($tier->is_active) selected @endif>Yes</option>
									<option value="0" @if(!$tier->is_active) selected @endif>No</option>
								</select>
							</td>
							<td><button type="button" class="btn btn-black roi-tier-update-btn">Update</button></td>
						</tr>
						@endforeach
					</tbody>
				</table>
				<div id="roiTierMsg"></div>
			</div>
		</div>

		<div class="panel panel-primary" data-collapsed="0">
			<div class="panel-heading">
				<div class="panel-title">Add New ROI Tier</div>
			</div>
			<div class="panel-body">
				<form id="roiTierAddForm" class="form-horizontal form-groups-bordered">
					<div class="form-group">
						<label class="col-sm-3 control-label">Min Amount ($)</label>
						<div class="col-sm-5">
							<input name="min_amount" type="number" step="0.01" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Max Amount ($, blank = no limit)</label>
						<div class="col-sm-5">
							<input name="max_amount" type="number" step="0.01" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Daily Percent (%)</label>
						<div class="col-sm-5">
							<input name="daily_percent" type="number" step="0.001" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-5">
							<button type="submit" class="btn btn-black">Add Tier</button>
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

document.getElementById('roiTierAddForm').addEventListener('submit', function(e) {
	e.preventDefault();
	var f = e.target;
	postJson('{{ URL::to("/admin/add-roi-tier") }}', {
		min_amount: f.min_amount.value,
		max_amount: f.max_amount.value,
		daily_percent: f.daily_percent.value
	}, function(res) {
		if(res.success) { location.reload(); } else { document.getElementById('roiTierMsg').innerText = 'Error: ' + (res.error_code || 'FAILED'); }
	});
});

document.querySelectorAll('.roi-tier-update-btn').forEach(function(btn) {
	btn.addEventListener('click', function() {
		var row = btn.closest('.roi-tier-row');
		var id = row.getAttribute('data-id');
		postJson('{{ URL::to("/admin/update-roi-tier") }}/' + id, {
			min_amount: row.querySelector('[name="min_amount"]').value,
			max_amount: row.querySelector('[name="max_amount"]').value,
			daily_percent: row.querySelector('[name="daily_percent"]').value,
			is_active: row.querySelector('[name="is_active"]').value
		}, function(res) {
			document.getElementById('roiTierMsg').innerText = res.success ? 'Updated successfully.' : ('Error: ' + (res.error_code || 'FAILED'));
		});
	});
});
</script>
@endsection
