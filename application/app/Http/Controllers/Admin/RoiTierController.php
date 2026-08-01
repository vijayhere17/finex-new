<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoiTierMaster;
use Log;

class RoiTierController extends Controller
{
    //
    public function index()
    {
        $page_titel = 'ROI Tier Master';

        $tiers = RoiTierMaster::orderBy('min_amount', 'asc')->get();

        return view('admin.roi-tier-master')->with(['page_titel'=>$page_titel, 'tiers'=>$tiers])->toJS();
    }

    public function addTier(Request $request)
    {
        try {
            $v = Validator::make($request->all(), [
                'min_amount' => 'required|numeric',
                'daily_percent' => 'required|numeric',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false,'error_code'=>'INVALID_REQUEST_DATA'), 200);
            }

            $object = new RoiTierMaster;
            $object->min_amount = $request->get('min_amount');
            $object->max_amount = $request->get('max_amount') !== null && $request->get('max_amount') !== '' ? $request->get('max_amount') : null;
            $object->daily_percent = $request->get('daily_percent');
            $object->is_active = 1;
            $object->save();

            return response()->json(array('success'=>true,'error_code'=>''), 200);
        } catch(Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=>'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }

    public function updateTier(Request $request, $id)
    {
        try {
            $v = Validator::make($request->all(), [
                'min_amount' => 'required|numeric',
                'daily_percent' => 'required|numeric',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false,'error_code'=>'INVALID_REQUEST_DATA'), 200);
            }

            $object = RoiTierMaster::find($id);

            if($object == null)
            {
                return response()->json(array('success'=>false,'error_code'=>'NOT_FOUND'), 200);
            }

            $object->min_amount = $request->get('min_amount');
            $object->max_amount = $request->get('max_amount') !== null && $request->get('max_amount') !== '' ? $request->get('max_amount') : null;
            $object->daily_percent = $request->get('daily_percent');
            $object->is_active = $request->get('is_active', $object->is_active);
            $object->save();

            return response()->json(array('success'=>true,'error_code'=>''), 200);
        } catch(Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=>'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
}
