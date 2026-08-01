@extends('admin.master')
@section('content')
<ol class="breadcrumb bc-3"><li><a href="{{URL::to('/')}}/admin/home">Home</a></li><li class="active"><strong>{{ $page_titel }}</strong></li></ol>
<div class="panel panel-primary"><div class="panel-heading"><div class="panel-title">{{ $page_titel }}</div></div>
<div class="panel-body table-responsive">
<table class="table table-bordered table-striped">
<thead><tr><th>ID</th><th>Member</th><th>From</th><th>Level</th><th>%</th><th>Amount</th><th>Date</th></tr></thead>
<tbody>
@foreach($rows as $r)
<tr>
<td>{{ $r->id }}</td><td>{{ $r->member_id }}</td><td>{{ $r->from_id }}</td>
<td>L{{ $r->level }}</td><td>{{ $r->percent }}%</td><td>${{ $r->amount }}</td><td>{{ $r->roi_date }}</td>
</tr>
@endforeach
</tbody></table>
{{ $rows->links() }}
</div></div>
@endsection
@section('jscontent')
@endsection
