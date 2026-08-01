@extends('users.master')
@section('content')
<div class="pc-container"><div class="pc-content">
    <div class="page-header mb-3"><h2 class="mb-0">{{ $page_titel }}</h2></div>
    <div class="card"><div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Stake</th>
                    <th>ROI %</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    <td>{{ $row->roi_date }}</td>
                    <td>{{ $row->day_number }}</td>
                    <td>${{ number_format($row->stake_amount, 2) }}</td>
                    <td>{{ number_format($row->roi_percent, 0) }}%</td>
                    <td>${{ number_format($row->amount, 4) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No Daily ROI yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $rows->links() }}</div>
    </div></div>
</div></div>
@endsection
@section('jscontent')
@endsection
