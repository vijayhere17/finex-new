@php
    $dashboardCon = app('App\Http\Controllers\Users\DashboardController');
    $earning =  0; // $dashboardCon->mytotalearning();
    $displayCurrentSlot = ($current_slot > 0) ? 'Slot '.$current_slot : 'Not Activated';
    $displayNextSlot = ($next_slot > 0) ? 'Slot '.$next_slot : 'All Slots Activated';
    $displayNextAmount = ($next_slot_amount > 0) ? '$'.number_format($next_slot_amount, 0) : '—';
    $displayActivationStatus = ucfirst($activation_status ?? 'registered');
@endphp
@extends('users.master')
@section('extra')
<style>
    .slot-hero {
        background: linear-gradient(120deg, #a5731c, #e6ad1f 55%, #f8ce4e);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        color: #0d0b07;
        margin-bottom: 1.25rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }
    .slot-hero h2 {
        margin: 0;
        font-weight: 700;
        color: #0d0b07;
    }
    .slot-hero p {
        margin: 0.35rem 0 0;
        opacity: 0.85;
        font-weight: 500;
    }

    .slot-progress {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .slot-progress-step {
        flex: 1 1 140px;
        background: var(--gt-card-bg, #1a1610);
        border: 1px solid var(--gt-card-border, #2e2920);
        border-radius: 12px;
        padding: 0.9rem 1rem;
        min-width: 140px;
    }
    .slot-progress-step .step-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--gt-text-muted, #9c9488);
        margin-bottom: 0.25rem;
    }
    .slot-progress-step .step-value {
        font-weight: 700;
        color: var(--gt-heading, #f6efdd);
    }
    .slot-progress-step.is-done {
        border-color: rgba(47, 191, 113, 0.55);
    }
    .slot-progress-step.is-done .step-value { color: #2fbf71; }
    .slot-progress-step.is-current {
        border-color: rgba(77, 171, 247, 0.7);
        box-shadow: 0 0 0 1px rgba(77, 171, 247, 0.25);
    }
    .slot-progress-step.is-current .step-value { color: #4dabf7; }
    .slot-progress-step.is-locked .step-value { color: var(--gt-text-muted, #9c9488); }

    .slot-card {
        border-radius: 14px;
        border: 1px solid var(--gt-card-border, #2e2920);
        background: var(--gt-card-bg, #1a1610);
        height: 100%;
        transition: border-color 0.2s ease, transform 0.2s ease;
    }
    .slot-card .card-body { padding: 1.15rem; }
    .slot-card .slot-title {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
        color: var(--gt-heading, #f6efdd);
    }
    .slot-card .slot-meta {
        font-size: 0.8rem;
        color: var(--gt-text-muted, #9c9488);
        margin-bottom: 0.15rem;
    }
    .slot-card .slot-amount {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
        color: var(--gt-gold-1, #f8ce4e);
    }
    .slot-badge {
        display: inline-block;
        border-radius: 999px;
        padding: 0.25rem 0.7rem;
        font-size: 0.75rem;
        font-weight: 700;
        margin-bottom: 0.9rem;
    }

    /* Ready To Activate */
    .slot-card.state-ready {
        border-color: #4dabf7;
        box-shadow: 0 0 0 1px rgba(77, 171, 247, 0.35);
    }
    .slot-card.state-ready .slot-badge {
        background: rgba(77, 171, 247, 0.18);
        color: #4dabf7;
    }
    .btn-slot-ready {
        background: #4dabf7;
        border-color: #4dabf7;
        color: #0d0b07;
        font-weight: 700;
        width: 100%;
        border-radius: 10px;
    }
    .btn-slot-ready:hover {
        background: #74c0fc;
        border-color: #74c0fc;
        color: #0d0b07;
    }

    /* Purchased */
    .slot-card.state-purchased {
        border-color: rgba(47, 191, 113, 0.65);
        background: rgba(47, 191, 113, 0.08);
    }
    .slot-card.state-purchased .slot-badge {
        background: rgba(47, 191, 113, 0.2);
        color: #2fbf71;
    }
    .btn-slot-purchased {
        width: 100%;
        border-radius: 10px;
        font-weight: 700;
        background: rgba(47, 191, 113, 0.2);
        border-color: rgba(47, 191, 113, 0.45);
        color: #2fbf71;
    }

    /* Locked */
    .slot-card.state-locked {
        border-color: #3a352c;
        opacity: 0.72;
    }
    .slot-card.state-locked .slot-badge {
        background: rgba(156, 148, 136, 0.18);
        color: #9c9488;
    }
    .btn-slot-locked {
        width: 100%;
        border-radius: 10px;
        font-weight: 700;
        background: rgba(156, 148, 136, 0.12);
        border-color: #3a352c;
        color: #9c9488;
    }

    .slot-info-panel .list-group-item {
        background: transparent;
        color: var(--gt-text, #ece8df);
        border-color: var(--gt-border, #2e2920);
    }

    .slot-hidden-radios { display: none; }
</style>
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('/') }}/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript:">Slot Activation</a></li>
                            <li class="breadcrumb-item" aria-current="page">{{ $page_titel }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- Hero -->
        <div class="slot-hero">
            <h2>Slot Activation</h2>
            <p>Choose your next eligible slot to activate.</p>
        </div>

        <!-- Progress Section -->
        <div class="slot-progress">
            <div class="slot-progress-step is-done">
                <div class="step-label">Registration</div>
                <div class="step-value"><i class="ti ti-check"></i> Completed</div>
            </div>
            <div class="slot-progress-step is-current">
                <div class="step-label">Slot Activation</div>
                <div class="step-value">Current Step</div>
            </div>
            <div class="slot-progress-step is-locked">
                <div class="step-label">ROI Earnings</div>
                <div class="step-value">Locked</div>
            </div>
            <div class="slot-progress-step is-locked">
                <div class="step-label">Level Income</div>
                <div class="step-value">Locked</div>
            </div>
        </div>

        <div class="row">
            <!-- Slot Cards -->
            <div class="col-lg-8">
                <div class="row g-3">
                    {{-- Hidden radios keep existing payment JS package selection intact --}}
                    <div class="slot-hidden-radios">
                        @foreach($slots as $slot)
                            @if(!empty($slot->stake_id))
                                <input type="radio"
                                       name="package"
                                       id="package_{{ $slot->stake_id }}"
                                       stakeid="{{ $slot->stake_id }}"
                                       stakeamount="{{ $slot->amount }}"
                                       slotnumber="{{ $slot->slot_number }}"
                                       cap="{{ $slot->cap_multiplier }}"
                                       minamount="{{ $slot->amount }}"
                                       maxamount="{{ $slot->amount }}"
                                       data-state="{{ $slot->state }}">
                            @endif
                        @endforeach
                    </div>

                    @foreach($slots as $slot)
                        @php
                            $stateClass = 'state-'.$slot->state;
                            $isReady = $slot->state === 'ready';
                            $isPurchased = $slot->state === 'purchased';
                            $isLocked = $slot->state === 'locked';
                        @endphp
                        <div class="col-6 col-md-4 col-xl-3">
                            <div class="card slot-card {{ $stateClass }} mb-0">
                                <div class="card-body d-flex flex-column">
                                    <div class="slot-title">Slot {{ $slot->slot_number }}</div>
                                    <div class="slot-meta">Investment</div>
                                    <div class="slot-amount">${{ number_format($slot->amount, 0) }}</div>

                                    @if($isReady)
                                        <span class="slot-badge">Ready To Activate</span>
                                        <button type="button"
                                                class="btn btn-slot-ready btn-activate-slot mt-auto"
                                                data-stakeid="{{ $slot->stake_id }}"
                                                data-amount="{{ $slot->amount }}"
                                                data-slot="{{ $slot->slot_number }}"
                                                @if(empty($slot->stake_id)) disabled title="Package not configured" @endif>
                                            Activate
                                        </button>
                                    @elseif($isPurchased)
                                        <span class="slot-badge">Purchased</span>
                                        <button type="button" class="btn btn-slot-purchased mt-auto" disabled>Activated</button>
                                    @else
                                        <span class="slot-badge"><i class="ti ti-lock"></i> Locked</span>
                                        <button type="button" class="btn btn-slot-locked mt-auto" disabled>Locked</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Information + Payment Panel -->
            <div class="col-lg-4 mt-3 mt-lg-0">
                <div class="card slot-info-panel">
                    <div class="card-header">
                        <h5 class="mb-0">Activation Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>USDT (BEP20) Live Rate : </strong>$<span>{{ $coin_rate }}</span>
                        </div>

                        <ul class="list-group list-group-flush product-check-list mb-3">
                            <li class="list-group-item enable d-flex justify-content-between">
                                <span>Current Slot</span>
                                <span class="fw-bold" id="txt_current_slot">{{ $displayCurrentSlot }}</span>
                            </li>
                            <li class="list-group-item enable d-flex justify-content-between">
                                <span>Next Slot</span>
                                <span class="fw-bold text-info" id="txt_next_slot">{{ $displayNextSlot }}</span>
                            </li>
                            <li class="list-group-item enable d-flex justify-content-between">
                                <span>Investment Amount</span>
                                <span class="fw-bold text-warning" id="txt_amount">{{ $displayNextAmount }}</span>
                            </li>
                            <li class="list-group-item enable d-flex justify-content-between">
                                <span>Activation Status</span>
                                <span class="fw-bold" id="txt_activation_status">{{ $displayActivationStatus }}</span>
                            </li>
                            <li class="list-group-item enable d-flex justify-content-between">
                                <span>Qualified Active Directs</span>
                                <span class="fw-bold text-info" id="txt_qualified_directs">{{ (int) $qualified_active_directs }}</span>
                            </li>
                            <li class="list-group-item enable d-flex justify-content-between">
                                <span>Current Daily ROI %</span>
                                <span class="fw-bold text-success" id="txt_apy">{{ number_format((float) $direct_roi_percent, 0) }}%</span>
                            </li>
                            <li class="list-group-item enable d-flex justify-content-between">
                                <span>Estimated Maximum Return</span>
                                <span class="fw-bold" id="txt_cap">0x</span>
                            </li>
                        </ul>

                        {{-- Keep existing USDT payment option / blockchain flow --}}
                        <div class="price-check border rounded p-3 mb-3">
                            <div class="form-check">
                                <input type="radio" name="paymentmode" class="form-check-input input-primary" id="payment_alc" data="1" contract="0x55d398326f99059fF775485246999027B3197955" decimal="18" value="1" checked>
                                <label class="form-check-label d-block" for="payment_alc">
                                    <span class="h5 mb-0 d-block">Pay USDT (BEP20)</span>
                                </label>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush product-check-list mb-3">
                            <li class="list-group-item enable d-flex justify-content-between">
                                <span>Payable USDT</span>
                                <span class="fw-bold" id="txt_payable">0.00000000</span>
                            </li>
                        </ul>

                        <div id="amount_error" class="text-danger mb-2" style="display: none; font-weight: 600;"></div>

                        {{-- Selected slot amount (no manual typing) --}}
                        <input type="hidden" id="topup_amount" name="topup_amount" value="{{ $next_slot_amount > 0 ? $next_slot_amount : '' }}">

                        <button type="button" class="btn btn-primary btn-submit" style="width: 100%;" @if($next_slot <= 0) disabled @endif>
                            @if($next_slot > 0)
                                Activate Slot {{ $next_slot }} — Pay ${{ number_format($next_slot_amount, 0) }}
                            @else
                                All Slots Activated
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscontent')

<script>
window.directRoiPercent = {{ (float) $direct_roi_percent }};
window.slotMeta = {
    current_slot: {{ (int) $current_slot }},
    next_slot: {{ (int) $next_slot }},
    next_slot_amount: {{ (float) $next_slot_amount }},
    activation_status: @json($activation_status),
    qualified_active_directs: {{ (int) $qualified_active_directs }},
    direct_roi_percent: {{ (float) $direct_roi_percent }}
};
</script>
<script src="{{ URL::to('/') }}/assets/js/users/buy-bot.0.16.js?v=3"></script>
<script>
    connectwallet();

    // Pre-select the next eligible slot radio (if available).
    (function () {
        var nextAmount = window.slotMeta.next_slot_amount;
        if (!nextAmount) { return; }
        $("input[name=package]").each(function () {
            if (parseFloat($(this).attr('stakeamount')) === parseFloat(nextAmount) && $(this).attr('data-state') === 'ready') {
                $(this).prop('checked', true);
                if (typeof getcalculation === 'function') { getcalculation(); }
            }
        });
    })();
</script>
@endsection
