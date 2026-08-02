@extends('users.master')
@section('extra')
<style>
    /* Finex dark cards: Bootstrap table-striped defaults to near-black text (#131920). */
    .fx-slots-table {
        --bs-table-color: #f6efdd;
        --bs-table-bg: transparent;
        --bs-table-border-color: #2e2920;
        --bs-table-striped-color: #f6efdd;
        --bs-table-striped-bg: rgba(248, 206, 78, 0.06);
        --bs-table-hover-color: #ffffff;
        --bs-table-hover-bg: rgba(248, 206, 78, 0.12);
        color: #f6efdd;
    }
    .fx-slots-table thead th {
        color: #f8ce4e !important;
        border-bottom-color: #2e2920 !important;
        white-space: nowrap;
    }
    .fx-slots-table tbody td {
        color: #f6efdd !important;
        border-bottom-color: #2e2920 !important;
        vertical-align: middle;
    }
    .fx-slots-table .badge-active {
        background: rgba(40, 167, 69, 0.2);
        color: #7dffa0;
        border: 1px solid rgba(40, 167, 69, 0.45);
    }
    .fx-slots-table .badge-done {
        background: rgba(156, 148, 136, 0.2);
        color: #cfc6b6;
        border: 1px solid rgba(156, 148, 136, 0.45);
    }
    .fx-slots-meta {
        color: #9c9488;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }
    .fx-tx {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.78rem;
        color: #e6ad1f;
        word-break: break-all;
    }
</style>
@endsection
@section('content')
<div class="pc-container"><div class="pc-content">
    <div class="page-header mb-3">
        <h2 class="mb-0">{{ $page_titel }}</h2>
        <p class="fx-slots-meta mb-0">
            {{ $slots->count() }} slot record(s) — includes every activation (manual + auto-upgrade).
        </p>
    </div>
    <div class="card"><div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 fx-slots-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Slot</th>
                    <th>Amount</th>
                    <th>ROI Paid</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Tx / Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($slots as $row)
                @php
                    $slotNo = $row->slot_number;
                    if (!$slotNo) {
                        $amounts = config('income.slot_amounts', []);
                        $idx = array_search((float) $row->paid_amount, array_map('floatval', $amounts), true);
                        $slotNo = ($idx === false) ? null : ($idx + 1);
                    }
                    $tx = $row->chain_tx_hash ?? null;
                    $explorer = rtrim((string) config('blockchain.explorer_tx_url', 'https://testnet.bscscan.com/tx/'), '/');
                @endphp
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $slotNo ? 'Slot '.$slotNo : '—' }}</td>
                    <td>${{ number_format((float) $row->paid_amount, 2) }}</td>
                    <td>${{ number_format((float) ($row->total_roi_paid ?? 0), 2) }}</td>
                    <td>{{ (int) ($row->roi_days_paid ?? 0) }} / {{ (int) ($row->max_roi_days ?? 300) }}</td>
                    <td>
                        @if((int) $row->is_deleted === 1)
                            <span class="badge badge-done px-2 py-1 rounded">Completed</span>
                        @else
                            <span class="badge badge-active px-2 py-1 rounded">Active</span>
                        @endif
                    </td>
                    <td>
                        @if($tx)
                            <div class="fx-tx">
                                <a href="{{ $explorer }}/{{ $tx }}" target="_blank" rel="noopener" style="color:inherit;">
                                    {{ substr($tx, 0, 10) }}…{{ substr($tx, -6) }}
                                </a>
                            </div>
                        @endif
                        <div style="color:#9c9488;font-size:0.8rem;">{{ $row->created_at }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4" style="color:#9c9488 !important;">
                        No slots activated yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
</div></div>
@endsection
@section('jscontent')
@endsection
