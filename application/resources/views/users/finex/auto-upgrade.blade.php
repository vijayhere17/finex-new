@extends('users.master')
@section('content')
<div class="pc-container"><div class="pc-content">
    <div class="page-header mb-3">
        <h2 class="mb-0">{{ $page_titel }}</h2>
        <p class="text-muted mb-0">Balance: <strong>${{ number_format($balance, 2) }}</strong> — credits from 2nd &amp; 3rd directs auto-buy your next slot.</p>
    </div>
    <div class="card"><div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Event</th>
                    <th>Slot</th>
                    <th>Amount</th>
                    <th>Balance After</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    <td>{{ $row->created_at }}</td>
                    <td>{{ $row->event_type }}</td>
                    <td>{{ $row->slot_number ? 'Slot '.$row->slot_number : '—' }}</td>
                    <td>${{ number_format($row->amount, 4) }}</td>
                    <td>${{ number_format($row->balance_after, 4) }}</td>
                    <td>{{ $row->description }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No auto-upgrade activity yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $rows->links() }}</div>
    </div></div>
</div></div>
@endsection
@section('jscontent')
@endsection
