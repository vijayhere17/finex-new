@extends('users.master')
@section('content')
<div class="pc-container"><div class="pc-content">
    <div class="page-header mb-3"><h2 class="mb-0">{{ $page_titel }}</h2></div>
    <div class="card"><div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Slot</th>
                    <th>Amount</th>
                    <th>ROI Paid</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($slots as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->slot_number ? 'Slot '.$row->slot_number : '—' }}</td>
                    <td>${{ number_format($row->paid_amount, 2) }}</td>
                    <td>${{ number_format($row->total_roi_paid ?? 0, 2) }}</td>
                    <td>{{ (int)($row->roi_days_paid ?? 0) }} / {{ (int)($row->max_roi_days ?? 300) }}</td>
                    <td>{{ ((int)$row->is_deleted === 1) ? 'Completed' : 'Active' }}</td>
                    <td>{{ $row->created_at }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">No slots activated yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
</div></div>
@endsection
@section('jscontent')
@endsection
