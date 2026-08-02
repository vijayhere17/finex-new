@extends('users.master')
@section('content')
<div class="pc-container"><div class="pc-content">
    <div class="page-header mb-3">
        <h2 class="mb-0" style="color:#f6efdd;">{{ $page_titel }}</h2>
        <p style="color:#f8ce4e;font-weight:700;margin:0.35rem 0 0;">
            Showing {{ $slots->count() }} record(s) for member #{{ $memberId }}
        </p>
    </div>

    <div class="card" style="background:#1a1610;border:1px solid #2e2920;">
        <div class="card-body table-responsive">
            @if($slots->isEmpty())
                <p style="color:#f6efdd;text-align:center;padding:2rem 0;margin:0;">
                    No slots found for your login (member #{{ $memberId }}).
                    If you see rows in phpMyAdmin under another <code>member_id</code>, you are logged in as a different user.
                </p>
            @else
            <table style="width:100%;border-collapse:collapse;color:#f6efdd;">
                <thead>
                    <tr style="color:#f8ce4e;border-bottom:1px solid #2e2920;">
                        <th style="padding:0.75rem;text-align:left;">#</th>
                        <th style="padding:0.75rem;text-align:left;">Slot</th>
                        <th style="padding:0.75rem;text-align:left;">Amount</th>
                        <th style="padding:0.75rem;text-align:left;">ROI Paid</th>
                        <th style="padding:0.75rem;text-align:left;">Days</th>
                        <th style="padding:0.75rem;text-align:left;">Status</th>
                        <th style="padding:0.75rem;text-align:left;">Date</th>
                        <th style="padding:0.75rem;text-align:left;">Tx</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slots as $row)
                    @php
                        $slotNo = $row->slot_number;
                        if (!$slotNo) {
                            $amounts = config('income.slot_amounts', []);
                            $idx = array_search((float) $row->paid_amount, array_map('floatval', $amounts), true);
                            $slotNo = ($idx === false) ? null : ($idx + 1);
                        }
                        $tx = $row->chain_tx_hash ?? '';
                        $explorer = rtrim((string) config('blockchain.explorer_tx_url', 'https://testnet.bscscan.com/tx/'), '/');
                        $bg = $loop->even ? 'rgba(248,206,78,0.07)' : 'transparent';
                    @endphp
                    <tr style="background:{{ $bg }};border-bottom:1px solid #2e2920;color:#f6efdd;">
                        <td style="padding:0.75rem;color:#f6efdd;">{{ $loop->iteration }}</td>
                        <td style="padding:0.75rem;color:#f6efdd;font-weight:700;">{{ $slotNo ? 'Slot '.$slotNo : '—' }}</td>
                        <td style="padding:0.75rem;color:#f8ce4e;">${{ number_format((float) $row->paid_amount, 2) }}</td>
                        <td style="padding:0.75rem;color:#f6efdd;">${{ number_format((float) ($row->total_roi_paid ?? 0), 2) }}</td>
                        <td style="padding:0.75rem;color:#f6efdd;">{{ (int) ($row->roi_days_paid ?? 0) }} / {{ (int) ($row->max_roi_days ?? 300) }}</td>
                        <td style="padding:0.75rem;color:{{ ((int)$row->is_deleted === 1) ? '#cfc6b6' : '#7dffa0' }};">
                            {{ ((int) $row->is_deleted === 1) ? 'Completed' : 'Active' }}
                        </td>
                        <td style="padding:0.75rem;color:#9c9488;font-size:0.85rem;">{{ $row->created_at }}</td>
                        <td style="padding:0.75rem;color:#e6ad1f;font-size:0.78rem;word-break:break-all;">
                            @if($tx)
                                <a href="{{ $explorer }}/{{ ltrim($tx, '/') }}" target="_blank" rel="noopener" style="color:#e6ad1f;">
                                    {{ substr($tx, 0, 10) }}…{{ substr($tx, -6) }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div></div>
@endsection
@section('jscontent')
@endsection
