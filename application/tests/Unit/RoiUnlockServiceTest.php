<?php

namespace Tests\Unit;

use App\Services\RoiUnlockService;
use PHPUnit\Framework\TestCase;

class RoiUnlockServiceTest extends TestCase
{
    public function test_required_referrals_uses_floor_division(): void
    {
        $svc = new RoiUnlockService();

        $this->assertSame(1, $svc->requiredReferrals(100, 100));
        $this->assertSame(2, $svc->requiredReferrals(260, 100));
        $this->assertSame(2, $svc->requiredReferrals(1300, 500));
        $this->assertSame(3, $svc->requiredReferrals(300, 100));
        $this->assertSame(0, $svc->requiredReferrals(0, 100));
    }

    public function test_unlocked_roi_caps_at_qualifying_times_package(): void
    {
        $svc = new RoiUnlockService();

        $this->assertSame(0.0, $svc->computeUnlockedRoi(100, 100, 0));
        $this->assertSame(100.0, $svc->computeUnlockedRoi(100, 100, 1));
        $this->assertSame(100.0, $svc->computeUnlockedRoi(260, 100, 1));
        $this->assertSame(200.0, $svc->computeUnlockedRoi(260, 100, 2));
        $this->assertSame(260.0, $svc->computeUnlockedRoi(260, 100, 3));
        $this->assertSame(300.0, $svc->computeUnlockedRoi(300, 100, 3));
    }
}
