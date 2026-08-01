@extends('users.master')
@section('content')
<div class="pc-container"><div class="pc-content">
    <div class="page-header mb-3">
        <h2 class="mb-0">{{ $page_titel }}</h2>
        <p class="text-muted mb-0 mt-1">
            Level ROI is income you earn from your <strong>downline’s Daily ROI</strong>
            (L1 = 12% … L12 = 1%). Unlock: N active directs unlock Level N.
        </p>
    </div>
    <div class="card"><div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Level</th>
                    <th>From ID</th>
                    <th>%</th>
                    <th>Base ROI</th>
                    <th>Income</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    <td>{{ $row->roi_date }}</td>
                    <td>L{{ $row->level }}</td>
                    <td>
                        @if($row->fromUser)
                            {{ obscureAddress($row->fromUser->username) }}
                            <small class="text-muted">(#{{ $row->from_id }})</small>
                        @else
                            #{{ $row->from_id }}
                        @endif
                    </td>
                    <td>{{ number_format($row->percent, 0) }}%</td>
                    <td>${{ number_format($row->base_amount, 4) }}</td>
                    <td>${{ number_format($row->amount, 4) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        No Level ROI income yet.<br>
                        <small class="text-muted">
                            This page shows income <strong>you received</strong> from downline Daily ROI.
                            Your own Daily ROI appears under Daily ROI History. Sponsor must be active
                            with enough qualified directs (L1 needs ≥1 active direct).
                        </small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $rows->links() }}</div>
    </div></div>
</div></div>
@endsection
@section('jscontent')
@endsection
