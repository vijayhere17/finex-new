<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TurnoverRewardMaster;
use App\Models\TurnoverRewardAchiever;
use App\Models\User;
use App\Models\EarningWallet;
use Log;

class TurnoverRewardController extends Controller
{
    //
    public function index()
    {
        $page_titel = 'Reward Master';

        $rewards = TurnoverRewardMaster::where('milestone_order', '<=', 7)->orderBy('milestone_order', 'asc')->get();

        return view('admin.turnover-reward-master')->with(['page_titel'=>$page_titel, 'rewards'=>$rewards])->toJS();
    }

    public function addReward(Request $request)
    {
        try {
            $v = Validator::make($request->all(), [
                'milestone_order' => 'required|integer|unique:turnover_reward_masters,milestone_order',
                'required_directs' => 'required|integer|min:0',
                'required_team' => 'required|integer|min:0',
                'required_self_business' => 'required|numeric|min:0',
                'required_team_business' => 'required|numeric|min:0',
                'weekly_salary' => 'required|numeric|min:0',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false,'error_code'=>'INVALID_REQUEST_DATA'), 200);
            }

            $object = new TurnoverRewardMaster;
            $object->milestone_order = $request->get('milestone_order');
            $object->title = $request->get('title') ?: ('Reward '.$request->get('milestone_order'));
            $object->required_directs = $request->get('required_directs');
            $object->required_team = $request->get('required_team');
            $object->required_self_business = $request->get('required_self_business');
            $object->required_team_business = $request->get('required_team_business');
            $object->turnover_amount = $request->get('required_team_business');
            $object->weekly_salary = $request->get('weekly_salary');
            $object->cash_reward = $request->get('weekly_salary');
            $object->save();

            return response()->json(array('success'=>true,'error_code'=>''), 200);
        } catch(Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=>'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }

    public function updateReward(Request $request, $id)
    {
        try {
            $v = Validator::make($request->all(), [
                'required_directs' => 'required|integer|min:0',
                'required_team' => 'required|integer|min:0',
                'required_self_business' => 'required|numeric|min:0',
                'required_team_business' => 'required|numeric|min:0',
                'weekly_salary' => 'required|numeric|min:0',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false,'error_code'=>'INVALID_REQUEST_DATA'), 200);
            }

            $object = TurnoverRewardMaster::find($id);

            if($object == null)
            {
                return response()->json(array('success'=>false,'error_code'=>'NOT_FOUND'), 200);
            }

            if($request->filled('title'))
            {
                $object->title = $request->get('title');
            }
            $object->required_directs = $request->get('required_directs');
            $object->required_team = $request->get('required_team');
            $object->required_self_business = $request->get('required_self_business');
            $object->required_team_business = $request->get('required_team_business');
            $object->turnover_amount = $request->get('required_team_business');
            $object->weekly_salary = $request->get('weekly_salary');
            $object->cash_reward = $request->get('weekly_salary');
            $object->save();

            return response()->json(array('success'=>true,'error_code'=>''), 200);
        } catch(Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=>'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }

    public function indexAchievers()
    {
        $page_titel = 'Reward Achievers';

        $achievers = TurnoverRewardAchiever::with('member', 'reward')->orderBy('created_at', 'desc')->get();

        return view('admin.turnover-reward-achievers')->with(['page_titel'=>$page_titel, 'achievers'=>$achievers])->toJS();
    }

    public function indexWeeklySalary()
    {
        $page_titel = 'Weekly Reward Salary';

        $achievers = TurnoverRewardAchiever::with('member', 'reward')
            ->where('status', 0)
            ->orderBy('return_date', 'asc')
            ->get();

        $salary_logs = EarningWallet::where('earning_type', 7)
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(200)
            ->get();

        return view('admin.reward-weekly-salary')->with([
            'page_titel' => $page_titel,
            'achievers' => $achievers,
            'salary_logs' => $salary_logs,
        ])->toJS();
    }

    public function indexLockedReward()
    {
        $page_titel = 'Locked Reward Bonus';

        $members = User::where(function ($q) {
                $q->where('locked_reward_bonus', '>', 0)
                  ->orWhere('unlocked_reward_bonus', '>', 0)
                  ->orWhere('expired_reward_bonus', '>', 0);
            })
            ->orderBy('locked_reward_lock_date', 'desc')
            ->get();

        $unlock_logs = EarningWallet::where('earning_type', 10)
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(200)
            ->get();

        return view('admin.locked-reward-report')->with([
            'page_titel' => $page_titel,
            'members' => $members,
            'unlock_logs' => $unlock_logs,
        ])->toJS();
    }
}
