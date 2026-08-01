<?php

namespace App\Services;

use App\Models\SpilloverLog;
use App\Models\User;
use App\Models\UserStaked;
use Illuminate\Support\Facades\Log;

/**
 * Spillover — when a member completes an earning cycle and the immediate
 * sponsor is not eligible, place under the next eligible active upline.
 * Preserves original referral_uplines history via spillover_logs.
 */
class SpilloverService
{
    /**
     * Called when a stake hits max days or earning limit.
     */
    public function onEarningCycleComplete(User $member, ?UserStaked $stake = null, string $reason = 'earning cycle complete'): void
    {
        // Only spill when ALL active stakes are closed
        $open = UserStaked::where('member_id', $member->id)->where('is_deleted', 0)->count();
        if ($open > 0) {
            return;
        }

        $sponsorId = (int) $member->referral_id;
        if ($sponsorId <= 0) {
            return;
        }

        $sponsor = User::find($sponsorId);
        if ($sponsor == null) {
            return;
        }

        if ($this->isEligibleSponsor($sponsor)) {
            return; // stay under current sponsor
        }

        $next = $this->findNextEligibleUpline($sponsor);
        if ($next == null || (int) $next->id === $sponsorId) {
            SpilloverLog::create([
                'member_id' => $member->id,
                'from_sponsor_id' => $sponsorId,
                'to_sponsor_id' => null,
                'reason' => $reason.' — no eligible upline found',
            ]);
            return;
        }

        $fromId = $sponsorId;
        $member->referral_id = $next->id;
        // Rebuild referral_uplines: next id + next's uplines
        $uplines = trim(($next->id).','.($next->referral_uplines ?? ''), ',');
        $member->referral_uplines = $uplines;
        $member->save();

        SpilloverLog::create([
            'member_id' => $member->id,
            'from_sponsor_id' => $fromId,
            'to_sponsor_id' => $next->id,
            'reason' => $reason.' — sponsor not eligible; moved to next active upline',
        ]);

        Log::info('Spillover: member '.$member->id.' from '.$fromId.' to '.$next->id);
    }

    public function isEligibleSponsor(User $sponsor): bool
    {
        if (($sponsor->activation_status ?? '') !== DirectRoiService::STATUS_ACTIVE) {
            return false;
        }

        // Must have at least Slot 1 active (kit / current_slot)
        if ((int) ($sponsor->kit_id ?? 0) <= 0 && (int) ($sponsor->current_slot ?? 0) <= 0) {
            return false;
        }

        return true;
    }

    public function findNextEligibleUpline(User $fromSponsor): ?User
    {
        $id = (int) $fromSponsor->referral_id;
        $guard = 0;

        while ($id > 0 && $guard < 50) {
            $user = User::find($id);
            if ($user == null) {
                return null;
            }
            if ($this->isEligibleSponsor($user)) {
                return $user;
            }
            $id = (int) $user->referral_id;
            $guard++;
        }

        return null;
    }
}
