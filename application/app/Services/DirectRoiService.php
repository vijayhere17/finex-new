<?php

namespace App\Services;

use App\Models\User;

/**
 * Direct ROI Income — percentage calculator (display / storage only).
 *
 * Daily ROI % = min(qualified_active_directs, 12)
 * Qualified = direct referral with activation_status = 'active' (has an active slot).
 *
 * Does NOT credit wallet income. Reuse refresh() / calculatePercent() in the
 * future daily distribution module.
 */
class DirectRoiService
{
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_ACTIVE = 'active';

    /**
     * Count direct referrals who have an active slot.
     */
    public function countQualifiedActiveDirects(int $userId): int
    {
        return (int) User::where('referral_id', '=', $userId)
            ->where('activation_status', '=', self::STATUS_ACTIVE)
            ->count();
    }

    /**
     * Map qualified direct count → daily ROI percent (1% each, max 12%).
     */
    public function calculatePercent(int $qualifiedDirects): float
    {
        $perDirect = (float) config('income.direct_roi.percent_per_direct', 1);
        $max = (float) config('income.direct_roi.max_percent', 12);

        if ($qualifiedDirects <= 0) {
            return 0.0;
        }

        return (float) min($qualifiedDirects * $perDirect, $max);
    }

    /**
     * Recalculate and persist qualified directs + ROI % on the user row.
     * Returns ['qualified_active_directs' => int, 'direct_roi_percent' => float].
     */
    public function refresh(int $userId): array
    {
        $user = User::find($userId);

        if ($user == null) {
            return [
                'qualified_active_directs' => 0,
                'direct_roi_percent' => 0.0,
            ];
        }

        $count = $this->countQualifiedActiveDirects($userId);
        $percent = $this->calculatePercent($count);

        $user->qualified_active_directs = $count;
        $user->direct_roi_percent = $percent;
        $user->save();

        return [
            'qualified_active_directs' => $count,
            'direct_roi_percent' => $percent,
        ];
    }

    /**
     * Mark member as slot-active, then refresh their sponsor's Direct ROI %.
     * Call after a successful slot / package activation.
     */
    public function markActiveAndRefreshSponsor(User $member): void
    {
        if ($member->activation_status !== self::STATUS_ACTIVE) {
            $member->activation_status = self::STATUS_ACTIVE;
            $member->save();
        }

        // Member's own ROI depends on their directs — still refresh for stored accuracy.
        $this->refresh((int) $member->id);

        if ($member->referral_id > 0) {
            $this->refresh((int) $member->referral_id);
        }
    }

    /**
     * Read stored values, refreshing once if columns look unset / stale on first load.
     */
    public function getDisplayStats(User $user, bool $refresh = true): array
    {
        if ($refresh) {
            return $this->refresh((int) $user->id);
        }

        return [
            'qualified_active_directs' => (int) ($user->qualified_active_directs ?? 0),
            'direct_roi_percent' => (float) ($user->direct_roi_percent ?? 0),
        ];
    }
}
