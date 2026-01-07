<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\AC;
use App\models\PwdPickAndDrop;
use App\models\PwdVolunteer;
use App\models\PwdWheelChair;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PwdController extends Controller
{
    public function __construct()
    {
    }

    function roDashboard(Request $request)
    {
        $data['user_data'] = Auth::user();
        $data['wheel_chair'] = PwdWheelChair::where('st_code', $data['user_data']['st_code'])->where('ac_no', $data['user_data']['ac_no'])->count();
        $data['pick_drop'] = PwdPickAndDrop::where('st_code', $data['user_data']['st_code'])->where('ac_no', $data['user_data']['ac_no'])->count();
        $data['volunteer'] = PwdVolunteer::where('st_code', $data['user_data']['st_code'])->where('ac_no', $data['user_data']['ac_no'])->count();
        return view('admin.pwd.ro-dashboard', $data);
    }

    function updateRemarksRequest(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'referenceid' => 'required',
                'for' => 'required|in:WheelChair,Volunteer,PickDrop',
                'remarks' => 'required',
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }

            if ($request->input('for') == 'WheelChair') {
                $data = PwdWheelChair::where('referenceid', $request->input('referenceid'))->first();
                $data->remarks = $request->input('remarks');
                $data->update();
            } else  if ($request->input('for') == 'Volunteer') {
                PwdVolunteer::where('referenceid', $request->input('referenceid'))->update(['remarks' => $request->input('remaks')]);
            } else  if ($request->input('for') == 'PickDrop') {
                PwdPickAndDrop::where('referenceid', $request->input('referenceid'))->update(['remarks' => $request->input('remaks')]);
            }
            Session::flash(
                'success_mes',
                'Remarks is updated'
            );
            return Redirect::back();
        } catch (Exception $e) {
            Log::error($e);
            Session::flash('error_mes', 'Remarks not updated due to internal error');
            return Redirect::back();
        }
    }

    function deoDashboard(Request $request)
    {
        $data['user_data'] = Auth::user();
        $acs = AC::where('st_code', $data['user_data']['st_code'])->where('DIST_NO_HDQTR', $data['user_data']['dist_no'])->pluck('ac_no');
        $data['wheel_chair'] = PwdWheelChair::where('st_code', $data['user_data']['st_code'])->whereIn('ac_no', $acs)->count();
        $data['pick_drop'] = PwdPickAndDrop::where('st_code', $data['user_data']['st_code'])->whereIn('ac_no', $acs)->count();
        $data['volunteer'] = PwdVolunteer::where('st_code', $data['user_data']['st_code'])->whereIn('ac_no', $acs)->count();
        return view('admin.pwd.deo-dashboard', $data);
    }

    function ceoDashboard(Request $request)
    {
        $data['user_data'] = Auth::user();
        $data['wheel_chair'] = PwdWheelChair::where('st_code', $data['user_data']['st_code'])->count();
        $data['pick_drop'] = PwdPickAndDrop::where('st_code', $data['user_data']['st_code'])->count();
        $data['volunteer'] = PwdVolunteer::where('st_code', $data['user_data']['st_code'])->count();
        return view('admin.pwd.acceo-dashboard', $data);
    }

    function wheelChairRequest(Request $request)
    {
        $data['user_data'] = Auth::user();
        $data['requests'] = PwdWheelChair::with(['ac' => function ($q) use ($data) {
            $q->where('ST_CODE', $data['user_data']['st_code']);
        }])->where('st_code', $data['user_data']['st_code'])->where(function ($q) use ($data) {
            if ($data['user_data']['role_id'] == '19') {
                $q->where('ac_no', $data['user_data']['ac_no']);
            } else if ($data['user_data']['role_id'] == '5') {
                $acs = AC::where('st_code', $data['user_data']['st_code'])->where('DIST_NO_HDQTR', $data['user_data']['dist_no'])->pluck('ac_no');
                $q->whereIn('ac_no', $acs);
            }
        })->orderBy('ps_no')->get();
        return view('admin.pwd.wheel-chair', $data);
    }

    function pickDropRequest(Request $request)
    {
        $data['user_data'] = Auth::user();
        $data['requests'] = PwdPickAndDrop::with(['ac' => function ($q) use ($data) {
            $q->where('ST_CODE', $data['user_data']['st_code']);
        }])->where('st_code', $data['user_data']['st_code'])->where(function ($q) use ($data) {
            if ($data['user_data']['role_id'] == '19') {
                $q->where('ac_no', $data['user_data']['ac_no']);
            } else if ($data['user_data']['role_id'] == '5') {
                $acs = AC::where('st_code', $data['user_data']['st_code'])->where('DIST_NO_HDQTR', $data['user_data']['dist_no'])->pluck('ac_no');
                $q->whereIn('ac_no', $acs);
            }
        })->orderBy('ps_no')->get();
        return view('admin.pwd.pick-drop', $data);
    }

    function volunteerRequest(Request $request)
    {
        $data['user_data'] = Auth::user();
        $data['requests'] = PwdVolunteer::with(['ac' => function ($q) use ($data) {
            $q->where('ST_CODE', $data['user_data']['st_code']);
        }])->where('st_code', $data['user_data']['st_code'])->where(function ($q) use ($data) {
            if ($data['user_data']['role_id'] == '19') {
                $q->where('ac_no', $data['user_data']['ac_no']);
            } else if ($data['user_data']['role_id'] == '5') {
                $acs = AC::where('st_code', $data['user_data']['st_code'])->where('DIST_NO_HDQTR', $data['user_data']['dist_no'])->pluck('ac_no');
                $q->whereIn('ac_no', $acs);
            }
        })->orderBy('id', 'desc')->get();
        return view('admin.pwd.volunteer', $data);
    }
}
