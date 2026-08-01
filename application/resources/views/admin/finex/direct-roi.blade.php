@extends('admin.master')
@section('content')
<ol class="breadcrumb bc-3"><li><a href="{{URL::to('/')}}/admin/home">Home</a></li><li class="active"><strong>{{ $page_titel }}</strong></li></ol>
<div class="panel panel-primary"><div class="panel-heading"><div class="panel-title">{{ $page_titel }}</div></div>
<div class="panel-body table-responsive">
<table class="table table-bordered table-striped">
<thead><tr><th>ID</th><th>Username</th><th>Qualified Directs</th><th>Daily ROI %</th><th>Status</th><th>Slot</th></tr></thead>
<tbody>
@foreach($members as $m)
<tr>
<td>{{ $m->id }}</td><td>{{ $m->username }}</td>
<td>{{ $m->qualified_active_directs }}</td>
<td>{{ $m->direct_roi_percent }}%</td>
<td>{{ $m->activation_status }}</td>
<td>{{ $m->current_slot }}</td>
</tr>
@endforeach
</tbody></table>
{{ $members->links() }}
</div></div>
@endsection
@section('jscontent')
@endsection
