<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserStaked;
use App\Models\DailyRoiLog;
use App\Models\LevelRoiLog;
use App\Models\AutoUpgradeLog;
use App\Models\SpilloverLog;
use App\Models\EarningWallet;
use App\Models\WithdrawalLog;
use App\Services\DirectRoiService;
use App\Services\AutoUpgradeService;

/**
 * Finex user-panel pages for the new compensation plan.
 */
class FinexPanelController extends Controller
{
    public function mySlots()
    {
        $page_titel = 'My Slots';
        $userId = Auth::id();
        $slots = UserStaked::where('member_id', $userId)->orderBy('id', 'desc')->get();

        return view('users.finex.my-slots', compact('page_titel', 'slots'));
    }

    public function dailyRoiHistory()
    {
        $page_titel = 'Daily ROI History';
        $rows = DailyRoiLog::where('member_id', Auth::id())->orderBy('roi_date', 'desc')->orderBy('id', 'desc')->paginate(50);

        return view('users.finex.daily-roi-history', compact('page_titel', 'rows'));
    }

    public function levelRoiIncome()
    {
        $page_titel = 'Level ROI Income';
        $rows = LevelRoiLog::where('member_id', Auth::id())
            ->with(['fromUser' => function ($q) {
                $q->select('id', 'username', 'firstname', 'lastname');
            }])
            ->orderBy('roi_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('users.finex.level-roi-history', compact('page_titel', 'rows'));
    }

    public function autoUpgrade()
    {
        $page_titel = 'Auto Upgrade';
        $user = Auth::user();
        $rows = AutoUpgradeLog::where('member_id', $user->id)->orderBy('id', 'desc')->paginate(50);
        $balance = (float) ($user->auto_upgrade_balance ?? 0);

        return view('users.finex.auto-upgrade', compact('page_titel', 'rows', 'balance'));
    }

    public function transactions()
    {
        $page_titel = 'Transactions';
        $rows = EarningWallet::where('member_id', Auth::id())->orderBy('id', 'desc')->paginate(50);

        return view('users.finex.transactions', compact('page_titel', 'rows'));
    }

    public function genealogy()
    {
        $page_titel = 'Genealogy';
        // Reuse unilevel downline report view data shape
        return redirect('/downline-report/A');
    }

    public function wallet()
    {
        return redirect('/earning-wallet');
    }
}
