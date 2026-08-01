@extends('admin.master')
@section('content')
<ol class="breadcrumb bc-3"><li><a href="{{URL::to('/')}}/admin/home">Home</a></li><li class="active"><strong>{{ $page_titel }}</strong></li></ol>
<div class="panel panel-primary"><div class="panel-heading"><div class="panel-title">{{ $page_titel }}</div></div>
<div class="panel-body table-responsive">
<table class="table table-bordered table-striped">
<thead><tr><th>ID</th><th>Username</th><th>Status</th><th>Current</th><th>Next</th><th>ROI %</th><th>Auto Upgrade $</th></tr></thead>
<tbody>
@foreach($members as $m)
<tr>
<td>{{ $m->id }}</td><td>{{ $m->username }}</td><td>{{ $m->activation_status }}</td>
<td>{{ $m->current_slot }}</td><td>{{ $m->next_slot }}</td>
<td>{{ $m->direct_roi_percent }}%</td><td>${{ $m->auto_upgrade_balance }}</td>
</tr>
@endforeach
</tbody></table>
{{ $members->links() }}
</div></div>
@endsection
@section('jscontent')
@endsection
