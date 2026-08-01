@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
    $walletFull = $user->username ?: ($user->wallet_addr ?? '');
    $walletShort = $walletFull ? obscureAddress($walletFull) : '—';
    $displayName = trim(($user->firstname ?? '').' '.($user->lastname ?? ''));
    if ($displayName === '') {
        $displayName = $walletShort;
    }
    $referralLink = URL::to('/').'/sign-up?ref='.urlencode($walletFull);
    $avatarUrl = URL::to('/').'/assets/images/user/avatar-1.jpg';
@endphp
@extends('users.master')
@section('extra')
<style>
    .fx-stat-card {
        border-radius: 14px;
        border: 1px solid var(--gt-card-border, #2e2920);
        background: var(--gt-card-bg, #1a1610);
        height: 100%;
    }
    .fx-stat-card .fx-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--gt-text-muted, #9c9488);
        margin-bottom: 0.35rem;
    }
    .fx-stat-card .fx-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--gt-heading, #f6efdd);
        margin: 0;
    }
    .fx-stat-card .fx-value.text-gold { color: #f8ce4e; }
    .fx-hero {
        background: linear-gradient(120deg, #a5731c, #e6ad1f 55%, #f8ce4e);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        color: #0d0b07;
        margin-bottom: 1.25rem;
    }
    .fx-hero h2 { margin: 0; font-weight: 800; color: #0d0b07; }
    .fx-hero p { margin: 0.35rem 0 0; opacity: 0.85; font-weight: 500; }

    .fx-profile-card, .fx-referral-card {
        border-radius: 14px;
        border: 1px solid var(--gt-card-border, #2e2920);
        background: var(--gt-card-bg, #1a1610);
        height: 100%;
    }
    .fx-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(230, 173, 31, 0.55);
        box-shadow: 0 0 18px rgba(230, 173, 31, 0.25);
    }
    .fx-copy-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(0,0,0,0.25);
        border: 1px solid var(--gt-border, #2e2920);
        border-radius: 10px;
        padding: 0.55rem 0.75rem;
    }
    .fx-copy-row .fx-mono {
        flex: 1;
        min-width: 0;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--gt-heading, #f6efdd);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .fx-copy-btn {
        flex-shrink: 0;
        border: 1px solid rgba(230, 173, 31, 0.45);
        background: rgba(230, 173, 31, 0.12);
        color: #f8ce4e;
        border-radius: 8px;
        padding: 0.3rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
    }
    .fx-copy-btn:hover {
        background: rgba(230, 173, 31, 0.22);
        color: #fff;
    }
    .fx-card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--gt-heading, #f6efdd);
        margin-bottom: 0.85rem;
    }
    .fx-muted {
        color: var(--gt-text-muted, #9c9488);
        font-size: 0.8rem;
        margin-bottom: 0.35rem;
    }
</style>
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="fx-hero">
            <h2>Welcome, {{ $user->firstname ?: 'Trader' }}</h2>
            <p>Finex Slot Plan — activate slots, grow directs, earn Daily ROI up to 12%.</p>
        </div>

        {{-- Profile + Referral --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="card fx-profile-card mb-0">
                    <div class="card-body">
                        <div class="fx-card-title">Profile</div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $avatarUrl }}" alt="avatar" class="fx-avatar">
                            <div class="min-w-0">
                                <div class="fw-bold text-truncate" style="color: var(--gt-heading, #f6efdd); font-size: 1.05rem;">
                                    {{ $displayName }}
                                </div>
                                <div class="fx-muted mb-0">
                                    Status: <span class="text-warning">{{ ucfirst($fx->activation_status) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="fx-muted">Wallet Address</div>
                        <div class="fx-copy-row">
                            <span class="fx-mono" title="{{ $walletFull }}">{{ $walletShort }}</span>
                            <button type="button" class="fx-copy-btn" data-copy="{{ $walletFull }}" @disabled(!$walletFull)>
                                <i class="ti ti-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card fx-referral-card mb-0">
                    <div class="card-body">
                        <div class="fx-card-title">Referral</div>
                        <p class="fx-muted mb-2">Share your link to invite directs and unlock Daily ROI %.</p>
                        <div class="fx-muted">Referral Link</div>
                        <div class="fx-copy-row">
                            <span class="fx-mono" title="{{ $referralLink }}">{{ $referralLink }}</span>
                            <button type="button" class="fx-copy-btn" data-copy="{{ $referralLink }}" @disabled(!$walletFull)>
                                <i class="ti ti-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Current Slot</div>
                    <p class="fx-value">{{ $fx->current_slot > 0 ? 'Slot '.$fx->current_slot : 'None' }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Next Slot</div>
                    <p class="fx-value text-info">{{ $fx->next_slot > 0 ? 'Slot '.$fx->next_slot.' ($'.$fx->next_slot_amount.')' : 'Complete' }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Activation Status</div>
                    <p class="fx-value">{{ ucfirst($fx->activation_status) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Qualified Directs</div>
                    <p class="fx-value">{{ $fx->qualified_active_directs }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Current Daily ROI %</div>
                    <p class="fx-value text-gold">{{ number_format($fx->direct_roi_percent, 0) }}%</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Today's ROI</div>
                    <p class="fx-value">${{ number_format($fx->today_roi, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Total ROI</div>
                    <p class="fx-value">${{ number_format($fx->total_roi, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Level ROI Income</div>
                    <p class="fx-value">${{ number_format($fx->level_roi, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Auto Upgrade Wallet</div>
                    <p class="fx-value text-gold">${{ number_format($fx->auto_upgrade_balance, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Available Wallet</div>
                    <p class="fx-value">${{ number_format($fx->available_wallet, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Total Withdrawn</div>
                    <p class="fx-value">${{ number_format($fx->total_withdrawn, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Quick Action</div>
                    <a href="{{ URL::to('/') }}/buy-robo" class="btn btn-primary btn-sm mt-1">Activate Next Slot</a>
                </div></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script>
(function () {
    function copyText(text) {
        if (!text) { return Promise.reject(); }
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        return new Promise(function (resolve, reject) {
            var area = document.createElement('textarea');
            area.value = text;
            area.setAttribute('readonly', '');
            area.style.position = 'fixed';
            area.style.left = '-9999px';
            document.body.appendChild(area);
            area.select();
            try {
                document.execCommand('copy') ? resolve() : reject();
            } catch (e) {
                reject(e);
            } finally {
                document.body.removeChild(area);
            }
        });
    }

    document.querySelectorAll('.fx-copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var value = btn.getAttribute('data-copy') || '';
            copyText(value).then(function () {
                if (typeof successalert === 'function') {
                    successalert('Copied successfully!');
                } else {
                    btn.textContent = 'Copied';
                    setTimeout(function () { btn.innerHTML = '<i class="ti ti-copy"></i> Copy'; }, 1200);
                }
            }).catch(function () {
                if (typeof erroralert === 'function') {
                    erroralert('Unable to copy');
                }
            });
        });
    });
})();
</script>
@endsection
