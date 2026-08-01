<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DailyRoiLog;
use App\Models\LevelRoiLog;
use App\Models\AutoUpgradeLog;
use App\Models\SpilloverLog;
use App\Models\UserStaked;
use App\Models\StakeMaster;

class FinexAdminController extends Controller
{
    public function slotManagement()
    {
        $page_titel = 'Slot Management';
        $packages = StakeMaster::where('ptype', 2)->where('is_admin', 0)->where('is_travel', 0)->orderBy('amount')->get();
        return view('admin.finex.slots', compact('page_titel', 'packages'));
    }

    public function directRoiMonitor()
    {
        $page_titel = 'Direct ROI Monitoring';
        $members = User::where('activation_status', 'active')
            ->orderByDesc('direct_roi_percent')
            ->orderByDesc('qualified_active_directs')
            ->paginate(50);
        return view('admin.finex.direct-roi', compact('page_titel', 'members'));
    }

    public function dailyRoiLogs()
    {
        $page_titel = 'Daily ROI Logs';
        $rows = DailyRoiLog::orderByDesc('id')->paginate(50);
        return view('admin.finex.daily-roi-logs', compact('page_titel', 'rows'));
    }

    public function levelRoiLogs()
    {
        $page_titel = 'Level ROI Logs';
        $rows = LevelRoiLog::orderByDesc('id')->paginate(50);
        return view('admin.finex.level-roi-logs', compact('page_titel', 'rows'));
    }

    public function autoUpgradeLogs()
    {
        $page_titel = 'Auto Upgrade Logs';
        $rows = AutoUpgradeLog::orderByDesc('id')->paginate(50);
        return view('admin.finex.auto-upgrade-logs', compact('page_titel', 'rows'));
    }

    public function spilloverLogs()
    {
        $page_titel = 'Spillover Logs';
        $rows = SpilloverLog::orderByDesc('id')->paginate(50);
        return view('admin.finex.spillover-logs', compact('page_titel', 'rows'));
    }

    public function memberSlotStatus()
    {
        $page_titel = 'Member Slot Status';
        $members = User::orderByDesc('id')->paginate(50);
        return view('admin.finex.member-slots', compact('page_titel', 'members'));
    }
}
