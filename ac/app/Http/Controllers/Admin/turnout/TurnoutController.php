<?php

namespace App\Http\Controllers\Admin\turnout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\commonModel;
use App\Classes\xssClean;
use App\Helpers\LogNotification;
use App\models\Admin\PhaseModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\turnout\TurnoutModel;
use App\models\EstimatedEntryLog;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TurnoutController extends Controller
{
  public $base    = 'roac';
  public $folder  = 'turnout';
  public $action    = 'roac/turnout/';
  public $view_path = "admin.turnout.ro";
  public $commonModel = null;
  public $xssClean = null;
  public $turnout = null;

  public function __construct()
  {
    $this->middleware('adminsession');
    $this->middleware(['auth:admin', 'auth']);
    $this->middleware('ro');
    $this->commonModel = new commonModel();
    $this->xssClean = new xssClean;
    $this->turnout = new TurnoutModel;
    if (!Auth::check()) {
      return redirect('/officer-login');
    }
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  protected function guard()
  {
    return Auth::guard('admin');
  }


  public function estimate_turnout_entry()
  {
    $data  = [];
    $user = Auth::user();

    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, 'AC');
    //dd($ele_details);
    $seched = getschedulebyid($ele_details->ScheduleID);
    $st = getstatebystatecode($ele_details->ST_CODE);
    $ac = getacbyacno($ele_details->ST_CODE, $ele_details->CONST_NO);

    $data['user_data']      = $d;
    $data['ele_details']    = $ele_details;
    $data['st_code']        = $ele_details->ST_CODE;
    $data['ac_no']          = $ele_details->CONST_NO;
    $data['ac_name']        = $ac->AC_NAME;
    $data['st_name']        = $st->ST_NAME;
    $data['seched']        = $seched;

    $filter = [
      'st_code'       => $ele_details->ST_CODE,
      'ac_no'         => $ele_details->CONST_NO,
      'election_id'   => $ele_details->ELECTION_ID,
      'const_type'    => $ele_details->CONST_TYPE,
      'phase_no'      => $ele_details->PHASE_NO,
      'pc_no'         => '',
    ];
    $lists = $this->turnout->get_scheduledetail($filter);
    $estimated_time = $this->turnout->get_scheduletime($filter);

    $exempted = $this->turnout->check_turnout_exempted($filter);
    if ($exempted == 0) {
      $exempted = $this->turnout->check_turnout_entry_enable($filter);
    }

    if (isset($estimated_time) && $estimated_time) {
      $data['est_val'] = 1;
    } else {
      $data['est_val'] = 0;
    }
    $master = $this->turnout->get_schedulemaster($filter);
    $total_total  = 0;

    $totalturnout_per = 0;

    if (isset($lists) && $lists) {
      $totalturnout_per = round(($lists->est_turnout_total), 2);
    }
    $data['lists'] = $lists;
    $data['estimated_time'] = $estimated_time;
    $data['totalturnout_per'] = $totalturnout_per;
    $data['master'] = $master;
    $data['exempted'] = $exempted;
    $data['timestamp'] = date('Y-m-d H:i:s');
    // dd($data);
    return view($this->view_path . '.estimate-turnout-entry', $data);
  } // end function

  public function estimated_entry(Request $request)
  {
    try {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, 'AC');
      $a = PhaseModel::get_phase($ele_details->PHASE_NO);
      // if (isset($a->DATE_POLL) && $a->DATE_POLL != date('Y-m-d')) {
      //   Session::flash('error_mes', 'Poll Day is Over. any activity after poll day not allowed.');
      //   return Redirect::back()->withInput($request->all())->withErrors(['error' => 'Poll Day is Over. any activity after poll day not allowed.']);
      // } else if (!isset($a->DATE_POLL)) {
      //   Session::flash('error_mes', 'Unable to get Phase Poll date');
      //   return Redirect::back()->withInput($request->all())->withErrors(['error' => 'Unable to get Phase Poll date']);
      // }
      $filter = [
        'st_code'       => $ele_details->ST_CODE,
        'ac_no'         => $ele_details->CONST_NO,
        'election_id'   => $ele_details->ELECTION_ID,
        'const_type'    => $ele_details->CONST_TYPE,
        'pc_no'         => '',
      ];
      $lists1 = $this->turnout->get_scheduledetail($filter);
      $ele = $this->turnout->getcdacelectorsdetails($filter);
      $allow = false;
      if ($request->has('ceorequest') && $request->input('ceorequest') == 1) {
        $allow = true;
      } else if ($request->has('ecirequest') && $request->input('ecirequest') == 1) {
        $allow = true;
      } else if ($request->input('roundno') == 1 && ((int)date("H") == 9 && (int)date("i") >= 0 && (int)date("i") <= 30) && (int)date("H") < 11) {
        $allow = true;
      } else if ($request->input('roundno') == 2 && ((int)date("H") == 11 && (int)date("i") >= 0 && (int)date("i") <= 30) && (int)date("H") < 13) {
        $allow = true;
      } else if ($request->input('roundno') == 3 && ((int)date("H") == 13 && (int)date("i") >= 0 && (int)date("i") <= 30) && (int)date("H") < 15) {
        $allow = true;
      } else if ($request->input('roundno') == 4 && ((int)date("H") == 15 && (int)date("i") >= 0 && (int)date("i") <= 30) && (int)date("H") < 17) {
        $allow = true;
      } else if ($request->input('roundno') == 5 && (int)date("H") == 17 && (int)date("i") >= 0 && (int)date("i") <= 30) {
        $allow = true;
      } else if ($request->input('roundno') == 6 && ((int)date("H") >= 17 && (int)date("i") >= 0) && ((int)date("H") <= 23 && (int)date("i") <= 59)) {
        if ((int)date("H") == 17 && (int)date("i") < 40) {
          $allow = false;
        } else {
          $allow = true;
        }
      }

      if ($request->input('roundno') == 1) {
        if ($allow) {
          $this->updateVtRoundOne($request, $lists1, $ele_details, $d, $ele);
        } else {
          Session::flash('error_mes', 'Time Slot is expired for this entry.');
          return Redirect::back()->withInput($request->all())->withErrors(['error' => 'Time Slot is expired for this entry.']);
        }
      } elseif ($request->input('roundno') == 2) {
        if ($allow) {
          $this->updateVtRoundTwo($request, $lists1, $ele_details, $d, $ele);
        } else {
          Session::flash('error_mes', 'Time Slot is expired for this entry.');
          return Redirect::back()->withInput($request->all())->withErrors(['error' => 'Time Slot is expired for this entry.']);
        }
      } elseif ($request->input('roundno') == 3) {
        if ($allow) {
          $this->updateVtRoundThree($request, $lists1, $ele_details, $d, $ele);
        } else {
          Session::flash('error_mes', 'Time Slot is expired for this entry.');
          return Redirect::back()->withInput($request->all())->withErrors(['error' => 'Time Slot is expired for this entry.']);
        }
      } elseif ($request->input('roundno') == 4) {
        if ($allow) {
          $this->updateVtRoundFour($request, $lists1, $ele_details, $d, $ele);
        } else {
          Session::flash('error_mes', 'Time Slot is expired for this entry.');
          return Redirect::back()->withInput($request->all())->withErrors(['error' => 'Time Slot is expired for this entry.']);
        }
      } elseif ($request->input('roundno') == 5) {
        if ($allow) {
          $this->updateVtRoundFive($request, $lists1, $ele_details, $d, $ele);
        } else {
          Session::flash('error_mes', 'Time Slot is expired for this entry.');
          return Redirect::back()->withInput($request->all())->withErrors(['error' => 'Time Slot is expired for this entry.']);
        }
      } elseif ($request->input('roundno') == 6) {
        if ($allow) {
          $this->updateVtRoundSix($request, $lists1, $ele_details, $d, $filter, $ele, $a);
        } else {
          Session::flash('error_mes', 'Time Slot is expired for this entry.');
          return Redirect::back()->withInput($request->all())->withErrors(['error' => 'Time Slot is expired for this entry.']);
        }
      }

      /**
       * Logger Code Start
       */

      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $modify = (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) ? 'Modify' : 'Added';
        $ErrorMessage['TransectionAction'] = 'Estimated Turnout Entry ' . $modify;
        $ErrorMessage['TransectionStatus'] = 'SUCCESS';
        $ErrorMessage['LogDescription'] = 'Estimated Turnout Entry is ' . $modify . ' for round ' . $request->input('roundno') . ' AC NO ' . $filter['ac_no'] . ' ST CODE ' . $filter['st_code'];
        LogNotification::LogInfo($ErrorMessage);
      }


      /**
       * Logger Code End
       */
      Session::flash('success_mes', 'Voter Turnout successfully added');
      return Redirect::to('roac/turnout/estimate-turnout-entry');
    } catch (Exception $e) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Estimated Turnout Entry ';
        $ErrorMessage['TransectionStatus'] = 'FAILED';
        $ErrorMessage['LogDescription'] = $e;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function updateVtRoundOne($request, $lists1, $ele_details, $d, $ele)
  {
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round1'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round1_confrim|same:est_turnout_round1_confrim',
        'est_turnout_round1_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round1.numeric' => 'Please enter numeric value',
        'est_turnout_round1.required' => 'Please enter voter',
        'est_turnout_round1.between' => 'Please enter valid value (99.99)',
        'est_turnout_round1.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round1.same'   => 'The estimated Percentage entered does not match with the confirmation percentage entered.'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_round1 = $this->xssClean->clean_input($request->input('est_turnout_round1'));
    // This code is commented out only becouse now modification of percentage is not allowed
    // if ($est_turnout_round1 < $lists1->est_turnout_total) {
    //   Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous Percentage');
    //   return Redirect::to('roac/turnout/estimate-turnout-entry');
    // }
    if (($est_turnout_round1 > $lists1->est_turnout_round2) && $lists1->est_turnout_round2 != 0) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be grater than the next round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $electors_total = (isset($ele)) ? $ele->electors_total : 0;
    $est_voter = 0;
    $est_voter = round(($electors_total * $est_turnout_round1 / 100), 0);

    $st = array(
      'est_voters' => $est_voter,
      'round1_voter_total' => $est_voter,
      'updated_at' => date("Y-m-d H:i:s"),
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'est_turnout_round1' => $est_turnout_round1,
      'update_device_round1' => 'web',
      'update_at_round1' => date("Y-m-d H:i:s"),
      'est_turnout_total' => $est_turnout_round1
    );

    DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);

    /****************************** Publish data on ECI request *******************************************/
    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round1,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    /****************************** Publish data on ECI request *******************************************/
    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round1,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    /****************************** Publish data on ECI request *******************************************/
    $state_percentage =  PollDayModel::get_average_sum([
      'election_type'         => $ele_details->ELECTION_TYPEID,
      'state'         => $ele_details->ST_CODE,
      'phase'         => $ele_details->PHASE_NO,
      'group_by'      => 'state'
    ]);
    EstimatedEntryLog::create([
      'scheduleid' => $ele_details->PHASE_NO,
      'st_code' => $ele_details->ST_CODE,
      'ac_no' => $ele_details->CONST_NO,
      'round' => $request->input('roundno'),
      'percentage' => $est_turnout_round1,
      'state_percentage' => $state_percentage,
      'updatedby' => $d->officername,
    ]);
    Session::flash('success_mes', 'Voter Turnout successfully added');
  }

  public function updateVtRoundTwo($request, $lists1, $ele_details, $d, $ele)
  {
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round2'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round2_confrim|same:est_turnout_round2_confrim',
        'est_turnout_round2_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round2.numeric' => 'Please enter numeric value',
        'est_turnout_round2.required' => 'Please enter voter',
        'est_turnout_round2.between' => 'Please enter valid value (99.99)',
        'est_turnout_round2.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round2.same'   => 'The Estimated Poll Turnout% and Confirmation Estimated Poll Turnout% do not match'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }
    $id =  $request->input('id');
    $est_turnout_round2 = $this->xssClean->clean_input($request->input('est_turnout_round2'));
    if ($est_turnout_round2 < $lists1->est_turnout_round1) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('roac/turnout/estimate-turnout-entry');
    }
    if (($est_turnout_round2 > $lists1->est_turnout_round3) && $lists1->est_turnout_round3 != 0) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be grater than the next round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $electors_total = (isset($ele)) ? $ele->electors_total : 0;
    $est_voter = 0;
    $est_voter = round(($electors_total * $est_turnout_round2 / 100), 0);

    $st = array(
      'est_voters' => $est_voter,
      'round2_voter_total' => $est_voter,
      'updated_at' => date("Y-m-d H:i:s"),
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'est_turnout_round2' => $est_turnout_round2,
      'update_device_round2' => 'web',
      'update_at_round2' => date("Y-m-d H:i:s"),
      'est_turnout_total' => $est_turnout_round2
    );
    DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);

    /****************************** Publish data on ECI request *******************************************/
    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round2,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    /****************************** Publish data on ECI request *******************************************/
    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round2,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    $state_percentage =  PollDayModel::get_average_sum([
      'election_type'         => $ele_details->ELECTION_TYPEID,
      'state'         => $ele_details->ST_CODE,
      'phase'         => $ele_details->PHASE_NO,
      'group_by'      => 'state'
    ]);
    EstimatedEntryLog::create([
      'scheduleid' => $ele_details->PHASE_NO,
      'st_code' => $ele_details->ST_CODE,
      'ac_no' => $ele_details->CONST_NO,
      'round' => $request->input('roundno'),
      'percentage' => $est_turnout_round2,
      'state_percentage' => $state_percentage,
      'updatedby' => $d->officername,
    ]);
    /****************************** Publish data on ECI request *******************************************/
    Session::flash('success_mes', 'Voter Turnout successfully added');
  }

  public function updateVtRoundThree($request, $lists1, $ele_details, $d, $ele)
  {
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round3'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round3_confrim|same:est_turnout_round3_confrim',
        'est_turnout_round3_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round3.numeric' => 'Please enter numeric value',
        'est_turnout_round3.required' => 'Please enter voter',
        'est_turnout_round3.between' => 'Please enter valid value (99.99)',
        'est_turnout_round3.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round3.same'   => 'The Estimated Poll Turnout% and Confirmation Estimated Poll Turnout% do not match'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_round3 = $this->xssClean->clean_input($request->input('est_turnout_round3'));
    if (($est_turnout_round3 < $lists1->est_turnout_round2) || ($est_turnout_round3 < $lists1->est_turnout_round1)) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('roac/turnout/estimate-turnout-entry');
    }
    if (($est_turnout_round3 > $lists1->est_turnout_round4) && $lists1->est_turnout_round4 != 0) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be grater than the next round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $electors_total = (isset($ele)) ? $ele->electors_total : 0;
    $est_voter = 0;
    $est_voter = round(($electors_total * $est_turnout_round3 / 100), 0);

    $st = array(
      'est_voters' => $est_voter,
      'round3_voter_total' => $est_voter,
      'updated_at' => date("Y-m-d H:i:s"),
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'est_turnout_round3' => $est_turnout_round3,
      'update_device_round3' => 'web',
      'update_at_round3' => date("Y-m-d H:i:s"),
      'est_turnout_total' => $est_turnout_round3
    );

    DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
    Session::flash('success_mes', 'Voter Turnout successfully added');
    /****************************** Publish data on ECI request *******************************************/
    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round3,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    /****************************** Publish data on ECI request *******************************************/
    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round3,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    $state_percentage =  PollDayModel::get_average_sum([
      'election_type'         => $ele_details->ELECTION_TYPEID,
      'state'         => $ele_details->ST_CODE,
      'phase'         => $ele_details->PHASE_NO,
      'group_by'      => 'state'
    ]);
    EstimatedEntryLog::create([
      'scheduleid' => $ele_details->PHASE_NO,
      'st_code' => $ele_details->ST_CODE,
      'ac_no' => $ele_details->CONST_NO,
      'round' => $request->input('roundno'),
      'percentage' => $est_turnout_round3,
      'state_percentage' => $state_percentage,
      'updatedby' => $d->officername,
    ]);
    /****************************** Publish data on ECI request *******************************************/
    Session::flash('success_mes', 'Voter Turnout successfully added');
  }

  public function updateVtRoundFour($request, $lists1, $ele_details, $d, $ele)
  {
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round4'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round4_confrim|same:est_turnout_round4_confrim',
        'est_turnout_round4_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round4.numeric' => 'Please enter numeric value',
        'est_turnout_round4.required' => 'Please enter voter',
        'est_turnout_round4.between' => 'Please enter valid value (99.99)',
        'est_turnout_round4.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round4.same'   => 'The Estimated Poll Turnout% and Confirmation Estimated Poll Turnout% do not match'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_round4 = $this->xssClean->clean_input($request->input('est_turnout_round4'));
    if (($est_turnout_round4 < $lists1->est_turnout_round3) || ($est_turnout_round4 < $lists1->est_turnout_round2) || ($est_turnout_round4 < $lists1->est_turnout_round1)) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('roac/turnout/estimate-turnout-entry');
    }
    if (($est_turnout_round4 > $lists1->est_turnout_round5) && $lists1->est_turnout_round5 != 0) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be grater than the next round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $electors_total = (isset($ele)) ? $ele->electors_total : 0;
    $est_voter = 0;
    $est_voter = round(($electors_total * $est_turnout_round4 / 100), 0);

    $st = array(
      'est_voters' => $est_voter,
      'round4_voter_total' => $est_voter,
      'updated_at' => date("Y-m-d H:i:s"),
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'est_turnout_round4' => $est_turnout_round4,
      'update_device_round4' => 'web',
      'update_at_round4' => date("Y-m-d H:i:s"),
      'est_turnout_total' => $est_turnout_round4
    );
    DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);

    /****************************** Publish data on ECI request *******************************************/
    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round4,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    /****************************** Publish data on ECI request *******************************************/
    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round4,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    $state_percentage =  PollDayModel::get_average_sum([
      'election_type'         => $ele_details->ELECTION_TYPEID,
      'state'         => $ele_details->ST_CODE,
      'phase'         => $ele_details->PHASE_NO,
      'group_by'      => 'state'
    ]);
    EstimatedEntryLog::create([
      'scheduleid' => $ele_details->PHASE_NO,
      'st_code' => $ele_details->ST_CODE,
      'ac_no' => $ele_details->CONST_NO,
      'round' => $request->input('roundno'),
      'percentage' => $est_turnout_round4,
      'state_percentage' => $state_percentage,
      'updatedby' => $d->officername,
    ]);
    /****************************** Publish data on ECI request *******************************************/

    Session::flash('success_mes', 'Voter Turnout successfully added');
  }

  public function updateVtRoundFive($request, $lists1, $ele_details, $d, $ele)
  {
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_round5'          => 'required|numeric|between:0,99.99|required_with:est_turnout_round5_confrim|same:est_turnout_round5_confrim',
        'est_turnout_round5_confrim'  => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_round5.numeric' => 'Please enter numeric value',
        'est_turnout_round5.required' => 'Please enter voter',
        'est_turnout_round5.between' => 'Please enter valid value (99.99)',
        'est_turnout_round5.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_round5.same'   => 'The Estimated Poll Turnout% and Confirmation Estimated Poll Turnout% do not match'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_round5 = $this->xssClean->clean_input($request->input('est_turnout_round5'));
    if (($est_turnout_round5 < $lists1->est_turnout_round4) || ($est_turnout_round5 < $lists1->est_turnout_round3) || ($est_turnout_round5 < $lists1->est_turnout_round2) || ($est_turnout_round5 < $lists1->est_turnout_round1)) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('roac/turnout/estimate-turnout-entry');
    }
    if (($est_turnout_round5 > $lists1->close_of_poll) && $lists1->close_of_poll != 0) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be grater than the next round Percentage');
      return Redirect::to('aro/voting/estimate-turnout-entry');
    }
    $electors_total = (isset($ele)) ? $ele->electors_total : 0;
    $est_voter = 0;
    $est_voter = round(($electors_total * $est_turnout_round5 / 100), 0);

    $st = array(
      'est_voters' => $est_voter,
      'round5_voter_total' => $est_voter,
      'updated_at' => date("Y-m-d H:i:s"),
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'est_turnout_round5' => $est_turnout_round5,
      'update_device_round5' => 'web',
      'update_at_round5' => date("Y-m-d H:i:s"),
      'est_turnout_total' => $est_turnout_round5
    );

    DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);

    /****************************** Publish data on ECI request *******************************************/
    if (!empty($request->input('ecirequest')) && $request->input('ecirequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'modification_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round5,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    /****************************** Publish data on ECI request *******************************************/
    /****************************** Publish data on CEO request *******************************************/
    if (!empty($request->input('ceorequest')) && $request->input('ceorequest') == 1) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
      $upd_fld = 'missed_status_round' . $request->input('roundno');
      DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
      //Create log upon publish
      DB::table('pd_voter_turnout_request_log')->insert([
        'request_from' => $ele_details->ST_CODE,
        'ac_no' => $ele_details->CONST_NO,
        'phase_no' => $ele_details->PHASE_NO,
        'round_no' => $request->input('roundno'),
        'updated_turnout' => $est_turnout_round5,
        'updated_by' => $d->officername,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    $state_percentage =  PollDayModel::get_average_sum([
      'election_type'         => $ele_details->ELECTION_TYPEID,
      'state'         => $ele_details->ST_CODE,
      'phase'         => $ele_details->PHASE_NO,
      'group_by'      => 'state'
    ]);
    EstimatedEntryLog::create([
      'scheduleid' => $ele_details->PHASE_NO,
      'st_code' => $ele_details->ST_CODE,
      'ac_no' => $ele_details->CONST_NO,
      'round' => $request->input('roundno'),
      'percentage' => $est_turnout_round5,
      'state_percentage' => $state_percentage,
      'updatedby' => $d->officername,
    ]);
    /****************************** Publish data on ECI request *******************************************/
    Session::flash('success_mes', 'Voter Turnout successfully added');
  }

  public function updateVtRoundSix($request, $lists1, $ele_details, $d, $filter, $ele, $scheduledata)
  {
    $validator = Validator::make(
      $request->all(),
      [
        'est_turnout_end'             => 'required|numeric|between:0,99.99|required_with:est_turnout_end_confrim|same:est_turnout_end_confrim',
        'est_turnout_end_confrim'     => 'required|numeric|between:0,99.99'
      ],
      [
        'est_turnout_end.numeric' => 'Please enter numeric value',
        'est_turnout_end.required' => 'Please enter voter',
        'est_turnout_end.between' => 'Please enter valid value (99.99)',
        'est_turnout_end.required_with' => 'Please enter valid value (99.99) Confirmation Estimated Poll Turnout%',
        'est_turnout_end.same'   => 'The Estimated Poll Turnout% and Confirmation Estimated Poll Turnout% do not match'
      ]
    );

    if ($validator->fails()) {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $est_turnout_end = $this->xssClean->clean_input($request->input('est_turnout_end'));
    if (($est_turnout_end < $lists1->est_turnout_round5) || ($est_turnout_end < $lists1->est_turnout_round4) || ($est_turnout_end < $lists1->est_turnout_round3) || ($est_turnout_end < $lists1->est_turnout_round2) || ($est_turnout_end < $lists1->est_turnout_round1)) {
      Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the previous round Percentage');
      return Redirect::to('roac/turnout/estimate-turnout-entry');
    }
    $electors_total = (isset($ele)) ? $ele->electors_total : 0;
    $est_voter = 0;
    $est_voter = round(($electors_total * $est_turnout_end / 100), 0);

    $st = array(
      'est_voters' => $est_voter,
      'end_voter_total' => $est_voter,
      'updated_at' => date("Y-m-d H:i:s"),
      'update_at_final' => date("Y-m-d H:i:s"),
      'update_device_final' => 'web',
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'close_of_poll' => $est_turnout_end,
      'updated_device_close_of_poll' => 'web',
      'updated_at_close_of_poll' => date("Y-m-d H:i:s"),
      'est_turnout_total' => $est_turnout_end,
      'modification_status_round6' => 0, // this will immediately block edit box for round 6 if user update and publish voter trun out percentage 
    );

    if ($scheduledata->DATE_POLL == date('Y-m-d')) {
      $st['close_of_poll_voters'] = $est_voter;
      $st['close_of_poll_percent'] = $est_turnout_end;
    }

    if (((int)date("H") >= 19)) {
      $st['est_poll_close'] = 1;
    }
    if (((int)date("H") >= 19) && $request->input('ecirequest', 0) == 1 && $scheduledata->DATE_POLL == date('Y-m-d')) {
      if (($est_turnout_end < $lists1->close_of_poll) && $lists1->close_of_poll != 0) {
        Session::flash('error_mes', 'Cummulative Percentage entered should not be less than the current round Percentage');
        return Redirect::to('aro/voting/estimate-turnout-entry');
      }
    }

    DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);

    /****************************** Publish data on CEO request *******************************************/

    if (((int)date("H") >= 19 && (int)date("i") >= 0)) {
      DB::table('pd_scheduledetail_publish')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update($st);
    }
    $upd_fld = 'missed_status_round' . $request->input('roundno');
    DB::table('pd_scheduledetail')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->update([$upd_fld => 0]);
    //Create log upon publish
    $lists1 = $this->turnout->get_scheduledetail($filter);
    $latest_updated1 = 0;
    if (isset($lists1) and $lists1->est_turnout_round1 != 0)
      $latest_updated1 = $lists1->est_turnout_round1;
    if (isset($lists1) and $lists1->est_turnout_round2 != 0)
      $latest_updated1 = $lists1->est_turnout_round2;
    if (isset($lists1) and $lists1->est_turnout_round3 != 0)
      $latest_updated1 = $lists1->est_turnout_round3;
    if (isset($lists1) and $lists1->est_turnout_round4 != 0)
      $latest_updated1 = $lists1->est_turnout_round4;
    if (isset($lists1) and $lists1->est_turnout_round5 != 0)
      $latest_updated1 = $lists1->est_turnout_round5;
    if (isset($lists1) and $lists1->close_of_poll != 0)
      $latest_updated1 = $lists1->close_of_poll;

    DB::table('pd_voter_turnout_request_log')->insert([
      'request_from' => $ele_details->ST_CODE,
      'ac_no' => $ele_details->CONST_NO,
      'phase_no' => $ele_details->PHASE_NO,
      'round_no' => $request->input('roundno'),
      'updated_turnout' => $latest_updated1,
      'updated_by' => $d->officername,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $state_percentage =  PollDayModel::get_average_sum([
      'election_type'         => $ele_details->ELECTION_TYPEID,
      'state'         => $ele_details->ST_CODE,
      'phase'         => $ele_details->PHASE_NO,
      'group_by'      => 'state'
    ]);
    EstimatedEntryLog::create([
      'scheduleid' => $ele_details->PHASE_NO,
      'st_code' => $ele_details->ST_CODE,
      'ac_no' => $ele_details->CONST_NO,
      'round' => $request->input('roundno'),
      'percentage' => $latest_updated1,
      'state_percentage' => $state_percentage,
      'updatedby' => $d->officername,
    ]);
  }
}  // end class  //accepted_candidate  
