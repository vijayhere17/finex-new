@extends('admin.master')
@section('content')
<ol class="breadcrumb bc-3"><li><a href="{{URL::to('/')}}/admin/home">Home</a></li><li class="active"><strong>{{ $page_titel }}</strong></li></ol>
<div class="panel panel-primary"><div class="panel-heading"><div class="panel-title">{{ $page_titel }}</div></div>
<div class="panel-body table-responsive">
<table class="table table-bordered table-striped">
<thead><tr><th>ID</th><th>Member</th><th>Stake</th><th>%</th><th>Amount</th><th>Date</th><th>Day</th></tr></thead>
<tbody>
@foreach($rows as $r)
<tr>
<td>{{ $r->id }}</td><td>{{ $r->member_id }}</td><td>{{ $r->stake_id }}</td>
<td>{{ $r->roi_percent }}%</td><td>${{ $r->amount }}</td><td>{{ $r->roi_date }}</td><td>{{ $r->day_number }}</td>
</tr>
@endforeach
</tbody></table>
{{ $rows->links() }}
</div></div>
@endsection
@section('jscontent')
@endsection
