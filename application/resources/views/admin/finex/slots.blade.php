@extends('admin.master')
@section('content')
<ol class="breadcrumb bc-3"><li><a href="{{URL::to('/')}}/admin/home">Home</a></li><li class="active"><strong>{{ $page_titel }}</strong></li></ol>
<div class="panel panel-primary"><div class="panel-heading"><div class="panel-title">{{ $page_titel }}</div></div>
<div class="panel-body table-responsive">
<table class="table table-bordered table-striped">
<thead><tr><th>ID</th><th>Name</th><th>Amount</th><th>Cap</th><th>ptype</th></tr></thead>
<tbody>
@foreach($packages as $p)
<tr><td>{{ $p->id }}</td><td>{{ $p->name }}</td><td>${{ $p->amount }}</td><td>{{ $p->cap_multiplier }}x</td><td>{{ $p->ptype }}</td></tr>
@endforeach
</tbody></table>
</div></div>
@endsection
@section('jscontent')
@endsection
