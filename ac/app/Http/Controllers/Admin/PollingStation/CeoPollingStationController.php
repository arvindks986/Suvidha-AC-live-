<?php

namespace App\Http\Controllers\Admin\PollingStation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use \PDF;
use App\commonModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;
use App\adminmodel\ACCEOModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use App\models\Admin\turnout\TurnoutModel;
//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Helpers\LogNotification;
use App\models\Admin\polling_station\ExemptedAcWithPollingstationCheckModel;
//POLLING STATION MODELS
use App\models\Admin\polling_station\PollingStationModel;
use App\Services\PsWiseElectorsDetailsService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//current

class CeoPollingStationController extends Controller
{

  public $action        = 'acceo/turnout/CeoPsWiseDetails';
  public $actionElectoralDetails = 'acceo/turnout/PsWiseElectoralDetails';
  public $view_path     = "admin.ac.ceo";


  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->PollingStationModel = new PollingStationModel();
    $this->turnout = new TurnoutModel;
    $this->ceomodel = new ACCEOModel();
    if (!Auth::user()) {
      return redirect('/officer-login');
    }
  }

  public function getAcFinalizeList()
  {

    $userInfo = Auth::user();
    $st_code = $userInfo->st_code;
    $data['user_data'] = $userInfo;
    $data['heading_title'] = 'Turnout Publish Status List Ac Wise';
    $get_all_data = DB::table('pd_scheduledetail')
      ->join('m_ac', function ($join) {
        $join->on('pd_scheduledetail.ac_no', '=', 'm_ac.ac_no')
          ->on('m_ac.st_code', '=', 'pd_scheduledetail.st_code');
      })
      ->where('pd_scheduledetail.st_code', $st_code)
      ->orderBy('pd_scheduledetail.ac_no', 'asc')
      ->select('pd_scheduledetail.*', 'm_ac.ac_name')
      ->get();
    $data['get_all_data'] = $get_all_data;
    return view($this->view_path . '.polling_station.ceo-publish-turnout', $data);
  }

  function getFinializeStatus($st_code, $ac_no)
  {
    if (!empty($st_code) && !empty($ac_no)) {
      $get_data = DB::table('polling_station')->where('st_code', $st_code)->where('ac_no', $ac_no)->first();
      return $get_data;
    }
  }


  public function CeoPsWiseDetails(Request $request)
  {

    $data = [];

    $xss = new xssClean;
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];

    $data['phases'] = PhaseModel::get_phases();

    //set title
    $title_array  = [];
    $data['heading_title'] = "PS Wise Voter Turnout";

    $data['ac_id'] = NULL;
    if ($request->has('ac_id')) {
      $data['ac_id'] = $request->ac_id;
    }


    $data['election_type'] = NULL;
    if ($request->has('election_type')) {
      $data['election_type'] = $request->election_type;
      $request_array[] =  'election_type=' . $request->election_type;
    }


    $filter_for_phases = [
      'election_type' => $data['election_type']
    ];

    $data['phases'] = PhaseModel::get_phases($filter_for_phases);
    $data['phase'] = NULL;
    if ($request->has('phase')) {
      if ($request->phase != 'all') {
        $data['phase'] = $request->phase;
      }
      $request_array[] =  'phase=' . $request->phase;
    } else {
      $data['phase']    = $default_phase;
      $request_array[]  =  'phase=' . $default_phase;
    }


    //end set title
    $data['user_data']  =   Auth::user();

    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }

    $data['dist_no'] = '';

    $ac_id    = $xss->clean_input($request->ac_id);
    if ($ac_id) {
      $dist_no = DB::table('m_ac')->where('ST_CODE', $data['state'])->where('AC_NO', $ac_id)->first();
      if ($dist_no) {
        $data['dist_no'] = $dist_no->DIST_NO_HDQTR;
      }
    }

    $filter_election = [
      'state'         => $data['state'],
      'ac_no'         => $ac_id,
    ];



    $request_array[] =  'state=' . $data['state'];
    $request_array[] =  'ac_id=' . $ac_id;

    $statename = getstatebystatecode($data['state']);
    $acame = getacbyacno($data['state'], $ac_id);


    $data['consituencies']  = AcModel::get_records([
      'state'         => $data['state'],
      // 'phase'       => $data['phase']
    ]);

    //CHECKING REQUEST VARIABES STARTS
    if ($request->has('ac_id')) {

      $validator = Validator::make($request->all(), [
        //'ac_id'          => 'required|numeric',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $title_array[] = "State: " . $statename->ST_NAME;
      $title_array[] = "AC: " . $acame->AC_NAME;


      $data['filter_buttons'] = $title_array;

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action . '/excel') . '?' . implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action . '/pdf') . '?' . implode('&', $request_array),
        'target' => true
      ];


      $data['action']         = url($this->action);

      $results                = [];
      $ac_data         = PollingStationModel::get_ac_data($filter_election);
      $object         = PollingStationModel::get_ps_data($filter_election);
      //$data['is_finalize']  = PollingStationModel::get_ps_finalize_data_ceo($filter_election);
      $data['is_definalize']  = PollingStationModel::get_ps_finalize_ceo_data($filter_election);
      $data['ac_data'] = $ac_data;

      $is_finalize  = PollingStationModel::get_ps_finalize_data_ceo($filter_election);
      if (count($is_finalize) > 0) {
        foreach ($is_finalize as $k => $v) {
          if ($v->ps_finalize == 0) {
            $data['is_finalize'] = 0;
          } else {
            $data['is_finalize'] = 1;
          }
        }
      } else {
        $data['is_finalize'] = 0;
      }

      $is_finalize_deo  = PollingStationModel::get_ps_finalize_data_deo($filter_election);
      if (count($is_finalize_deo) > 0) {
        foreach ($is_finalize_deo as $k => $v) {
          if ($v->deo_ps_finalize == 0) {
            $data['is_finalize_deo'] = 0;
          } else {
            $data['is_finalize_deo'] = 1;
          }
        }
      } else {
        $data['is_finalize_deo'] = 0;
      }

      $filter = [
        'st_code'       => $data['state'],
        'ac_no'         => $ac_id
      ];

      $lists = $this->turnout->get_scheduledetail($filter);


      $data['results']    =   $object;

      $data['lists'] = $lists;
    } else {


      $filter = [
        'st_code'       => $data['state'],
        'ac_no'         => $ac_id
      ];

      $lists = $this->turnout->get_scheduledetail($filter);


      $data['is_finalize'] = 0;
      $data['is_finalize_deo'] = 0;

      $data['lists'] = $lists;


      $data['buttons']    = [];
      $data['action']         = url($this->action);
      $data['results'] = [];

      $data['is_finalize']  = NULL;
      $data['is_definalize'] = NULL;

      $data['consituencies']  = AcModel::get_records([
        'state'         => $data['state'],
      ]);
    }


    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }

      return $data;
    }


    return view($this->view_path . '.polling_station.CeoPsWiseDetails', $data);
  }


  //EXCEL REPORT STARTS
  public function CeoPsWiseDetailsExcel(Request $request)
  {

    set_time_limit(6000);
    $data = $this->CeoPsWiseDetails($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['PS No', 'PS Name', 'Location Type', 'PS Type', 'Electors Male', 'Electors Female', 'Electors Other', 'Electors Total', 'Voter Male', 'Voter Female', 'Voter Other', 'Voter Total'];

    $arr  = array();
    $TotalElectorMale = 0;
    $TotalElectorFeMale = 0;
    $TotalElectorOther = 0;
    $TotalElector = 0;
    $TotalVoterMale = 0;
    $TotalVoterFeMale = 0;
    $TotalVoterOther = 0;
    $TotalVoter = 0;
    $totalvalues[] = [];
    $headings[] = [];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        ($lis->PS_NO) ? ($lis->PS_NO) : '0',
        ($lis->PS_NAME_EN) ? ($lis->PS_NAME_EN) : '0',
        ($lis->LOCN_TYPE) ? ($lis->LOCN_TYPE) : '0',
        ($lis->PS_TYPE) ? ($lis->PS_TYPE) : '0',
        ($lis->electors_male) ? ($lis->electors_male) : '0',
        ($lis->electors_female) ? ($lis->electors_female) : '0',
        ($lis->electors_other) ? ($lis->electors_other) : '0',
        ($lis->electors_total) ? ($lis->electors_total) : '0',
        ($lis->voter_male) ? ($lis->voter_male) : '0',
        ($lis->voter_female) ? ($lis->voter_female) : '0',
        ($lis->voter_other) ? ($lis->voter_other) : '0',
        ($lis->voter_total) ? ($lis->voter_total) : '0',

      ];

      $TotalElectorMale   += $lis->electors_male;
      $TotalElectorFeMale += $lis->electors_female;
      $TotalElectorOther  += $lis->electors_other;
      $TotalElector       += $lis->electors_total;
      $TotalVoterMale     += $lis->voter_male;
      $TotalVoterFeMale   += $lis->voter_female;
      $TotalVoterOther    += $lis->voter_other;
      $TotalVoter         += $lis->voter_total;

      $totalvalues = array('Total', '', '', '', $TotalElectorMale, $TotalElectorFeMale, $TotalElectorOther, $TotalElector, $TotalVoterMale, $TotalVoterFeMale, $TotalVoterOther, $TotalVoter);
    }
    $export_data[] = $totalvalues;

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');

    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:L1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }
  //EXCEL REPORT ENDS


  public function CeoPsWiseDetailsPdf(Request $request)
  {
    $data = $this->CeoPsWiseDetails($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.polling_station.CeoPsWiseDetailsPdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }




  


  public function CeoPsDefinalizeUpdate(Request $request)
  {
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $request     = $request->all();
      $ac_no = $request['ac_no'];
  
      $update_fields = array(
        'ps_finalize_date'   => NULL,
        'ps_finalize'        => 0,
        'ro_ps_finalize'        => 0,
        'deo_ps_finalize'        => 0,
      );
  
      $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no];
      DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
      if(config("public_config.vt_log")){
        $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
        $ErrorMessage['applicationType']= 'WebApp';
        $ErrorMessage['Module']= 'ENCORE';
        $ErrorMessage['TransectionType']= 'VoterTurnout';
        $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout CEO Definalize';
        $ErrorMessage['TransectionStatus']= 'Success';
        $ErrorMessage['LogDescription']= 'Polling Station Wise Voter Tournout is Definalize by CEO for AC '.$ac_no;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back()->with('error', 'Polling Station Data definalized Successfully !');
    }  catch (Exception $e) {
      if(config("public_config.vt_log")){
        $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
        $ErrorMessage['applicationType']= 'WebApp';
        $ErrorMessage['Module']= 'ENCORE';
        $ErrorMessage['TransectionType']= 'VoterTurnout';
        $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout CEO Definalize';
        $ErrorMessage['TransectionStatus']= 'FAILED';
        $ErrorMessage['LogDescription']= $e;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back()->with('error', 'Polling Station Data definalized failed !');
    }
  }


  public function CeoPsFinalizeUpdate(Request $request)
  {
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $request     = $request->all();
      $ac_no = $request['ac_no'];
  
      $update_fields = array(
        'ps_finalize_date'   => now(),
        'ps_finalize'        => 1,
      );
  
      $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no];
      DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
      if(config("public_config.vt_log")){
        $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
        $ErrorMessage['applicationType']= 'WebApp';
        $ErrorMessage['Module']= 'ENCORE';
        $ErrorMessage['TransectionType']= 'VoterTurnout';
        $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout CEO Finalize';
        $ErrorMessage['TransectionStatus']= 'Success';
        $ErrorMessage['LogDescription']= 'Polling Station Wise Voter Tournout is Finalize by CEO for AC '.$ac_no;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back()->with('error', 'Polling Station Data finalized Successfully !');
    } catch (Exception $e) {
      if(config("public_config.vt_log")){
        $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
        $ErrorMessage['applicationType']= 'WebApp';
        $ErrorMessage['Module']= 'ENCORE';
        $ErrorMessage['TransectionType']= 'VoterTurnout';
        $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout CEO Finalize';
        $ErrorMessage['TransectionStatus']= 'FAILED';
        $ErrorMessage['LogDescription']= $e;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back()->with('error', 'Polling Station Data finalized failed !');
    }
  }


  public function finalize_turnout(Request $request)
  {
    
    $data  = [];
    $user = Auth::user();
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_detailsac($d->st_code, $request->ac_no, $request->dist_no, $d->id, 'AC');
    $data['user_data']      = $d;
    $data['ele_details']    = $ele_details;
    $ro_finalize = '';
    $deo_finalize = '';
    try {
      /* Checking if RO and DEO Finalized all PS data or not */
      $get_check_ro_deo_status = $this->getFinializeStatus($d->st_code, $request->ac_no);
      $ac_name = getacname($d->st_code, $request->ac_no);
      if ($ac_name) {
        $ac_name = $ac_name->AC_NAME;
      }
      if ($get_check_ro_deo_status) {
        $ro_finalize = $get_check_ro_deo_status->ro_ps_finalize;
        $deo_finalize = $get_check_ro_deo_status->deo_ps_finalize;

        if ($ro_finalize != 1) {
          Session::flash('error_mes', 'RO does not finalized details for ' . $ac_name . 'AC');
          if(config("public_config.vt_log")){
            $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
            $ErrorMessage['applicationType']= 'WebApp';
            $ErrorMessage['Module']= 'ENCORE';
            $ErrorMessage['TransectionType']= 'VoterTurnout';
            $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout Publish';
            $ErrorMessage['TransectionStatus']= 'FAILED';
            $ErrorMessage['LogDescription']= 'RO does not finalized details for ' . $ac_name . 'AC';
            LogNotification::LogInfo($ErrorMessage);
          }
          return Redirect::back();
        }
        if ($deo_finalize != 1) {
          Session::flash('error_mes', 'DEO does not finalized details for ' . $ac_name . 'AC');
          if(config("public_config.vt_log")){
            $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
            $ErrorMessage['applicationType']= 'WebApp';
            $ErrorMessage['Module']= 'ENCORE';
            $ErrorMessage['TransectionType']= 'VoterTurnout';
            $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout Publish';
            $ErrorMessage['TransectionStatus']= 'FAILED';
            $ErrorMessage['LogDescription']= 'DEO does not finalized details for ' . $ac_name . 'AC';
            LogNotification::LogInfo($ErrorMessage);
          }
          return Redirect::back();
        }
      }
      /* Checking if RO and DEO Finalized all PS data or not */
      $total_voter = DB::table('pd_scheduledetail')->select('total', 'electors_total', 'est_voters', 'est_turnout_total')
        ->where('st_code', $ele_details->ST_CODE)
        ->where('ac_no', $ele_details->CONST_NO)
        ->where('election_id', $ele_details->ELECTION_ID)->first();

      $filter_election = [
        'state'         => $d->st_code,
        'ac_no'         => $request->ac_no,
      ];
      $ac_data         = PollingStationModel::get_ac_data($filter_election);
      $isExempted = ExemptedAcWithPollingstationCheckModel::where('st_code', $ele_details->ST_CODE)
      ->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->count();
      if($isExempted == 0){
        $ps_check = PollingStationModel::where('voter_total', 0)
          ->where('st_code', $ele_details->ST_CODE)
          ->where('ac_no', $ele_details->CONST_NO)
          ->first();
        if ($ps_check) {
          Session::flash('error_mes', 'Please Enter Voters in All Polling Stations!!!');
          if(config("public_config.vt_log")){
            $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
            $ErrorMessage['applicationType']= 'WebApp';
            $ErrorMessage['Module']= 'ENCORE';
            $ErrorMessage['TransectionType']= 'VoterTurnout';
            $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout Publish';
            $ErrorMessage['TransectionStatus']= 'FAILED';
            $ErrorMessage['LogDescription']= 'Please Enter Voters in All Polling Stations!!! for AC NO '. $ele_details->CONST_NO;
            LogNotification::LogInfo($ErrorMessage);
          }
          return Redirect::back();
        }
      }

      if (@$ac_data->electors_total != $total_voter->electors_total) {
        Session::flash('error_mes', 'Total Polling Station wise Electors entered did not matched with the ac wise Electors. Please verify.');
        if(config("public_config.vt_log")){
          $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
          $ErrorMessage['applicationType']= 'WebApp';
          $ErrorMessage['Module']= 'ENCORE';
          $ErrorMessage['TransectionType']= 'VoterTurnout';
          $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout Publish';
          $ErrorMessage['TransectionStatus']= 'FAILED';
          $ErrorMessage['LogDescription']= 'Total Polling Station wise Electors entered did not matched with the ac wise Electors. Please verify.';
          LogNotification::LogInfo($ErrorMessage);
        }
        return Redirect::back();
      }

      if (@$ac_data->voter_total == 0) {
        Session::flash('error_mes', 'You Can not Published with 0 Voters!');
        if(config("public_config.vt_log")){
          $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
          $ErrorMessage['applicationType']= 'WebApp';
          $ErrorMessage['Module']= 'ENCORE';
          $ErrorMessage['TransectionType']= 'VoterTurnout';
          $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout Publish';
          $ErrorMessage['TransectionStatus']= 'FAILED';
          $ErrorMessage['LogDescription']= 'You Can not Published with 0 Voters!';
          LogNotification::LogInfo($ErrorMessage);
        }
        return Redirect::back();
      }
      if (!empty($ac_data)) {
        $est_voters = $total_voter->est_turnout_total;
        if ($est_voters > round(((@$ac_data->voter_total / @$ac_data->electors_total) * 100), 2)) {

          Session::flash('error_mes', 'Please verify, End of Poll percentage is showing less then estimated close of poll percentage.');
          if(config("public_config.vt_log")){
            $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
            $ErrorMessage['applicationType']= 'WebApp';
            $ErrorMessage['Module']= 'ENCORE';
            $ErrorMessage['TransectionType']= 'VoterTurnout';
            $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout Publish';
            $ErrorMessage['TransectionStatus']= 'FAILED';
            $ErrorMessage['LogDescription']=  'Please verify, End of Poll percentage is showing less then estimated close of poll percentage.';
            LogNotification::LogInfo($ErrorMessage);
          }
          return Redirect::back();
        }
      }

      
      $st = array(
        'updated_at' => date("Y-m-d H:i:s"),
        'added_update_at' => date("Y-m-d"),
        'updated_by' => $d->officername,
        'ceo_finalize' => '1'
      );

      DB::table('pd_schedulemaster')
        ->where('st_code', $ele_details->ST_CODE)
        ->where('ac_no', $ele_details->CONST_NO)
        ->where('election_id', $ele_details->ELECTION_ID)->update($st);

      $st1 = array(
        'updated_at' => date("Y-m-d H:i:s"),
        'updated_at_finalize' => date("Y-m-d H:i:s"),
        'added_update_at' => date("Y-m-d"),
        'updated_by' => $d->officername,
        'is_est_entry_allow' => '1',
        'est_poll_close' => 1,
        'est_voters' => @$total_voter->total,
        'est_turnout_total' => round(((@$ac_data->voter_total / @$ac_data->electors_total) * 100), 2),
        'close_of_poll' => round(((@$ac_data->voter_total / @$ac_data->electors_total) * 100), 2),
        'end_of_poll_finalize' => '1'
      );

      DB::table('pd_scheduledetail')
        ->where('st_code', $ele_details->ST_CODE)
        ->where('ac_no', $ele_details->CONST_NO)
        ->where('election_id', $ele_details->ELECTION_ID)->update($st1);

      //Code for publishing data in to mobile app table `pd_scheduledetail_publish`
      DB::table('pd_scheduledetail_publish')
        ->where('st_code', $ele_details->ST_CODE)
        ->where('ac_no', $ele_details->CONST_NO)
        ->where('election_id', $ele_details->ELECTION_ID)->update($st1);

      Session::flash('success_mes', 'Turnout successfully Published');
      if(config("public_config.vt_log")){
        $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
        $ErrorMessage['applicationType']= 'WebApp';
        $ErrorMessage['Module']= 'ENCORE';
        $ErrorMessage['TransectionType']= 'VoterTurnout';
        $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout Publish';
        $ErrorMessage['TransectionStatus']= 'SUCCESS';
        $ErrorMessage['LogDescription']=  'Turnout successfully Published';
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back();
    } catch (Exception $e) {
      if(config("public_config.vt_log")){
        $ErrorMessage['MobNo']= Auth::user()->officername ?? '';
        $ErrorMessage['applicationType']= 'WebApp';
        $ErrorMessage['Module']= 'ENCORE';
        $ErrorMessage['TransectionType']= 'VoterTurnout';
        $ErrorMessage['TransectionAction']= 'Polling Station Wise Voter Tournout Publish';
        $ErrorMessage['TransectionStatus']= 'FAILED';
        $ErrorMessage['LogDescription']=  $e;
        LogNotification::LogInfo($ErrorMessage);
      }
      Session::flash('error_mes', 'Please try again.');
      return Redirect::back();
    }
  }  // end   function 

  function finalize_all_turnout(Request $request)
  {

    $data  = [];
    $user = Auth::user();
    $d = $this->commonModel->getunewserbyuserid($user->id);

    $form_data = $request->all_ac_data;
    if (!empty($form_data) && isset($form_data)) {
      $form_data_exp = explode(",", $form_data);
      $ro_finalize = '';
      $deo_finalize = '';
      $ceo_finalize = '';
      if (count($form_data_exp)) {
        foreach ($form_data_exp as $k => $v) {
          $get_data_val = explode("_", $v);
          $ac_no = $get_data_val[0];
          $dist_no = $get_data_val[1];
          if (!empty($ac_no) && !empty($dist_no)) {
            /* Checking if RO and DEO Finalized all PS data or not */
            $get_check_ro_deo_status = $this->getFinializeStatus($d->st_code, $ac_no);
            $ac_name = getacname($d->st_code, $ac_no);
            if ($ac_name) {
              $ac_name = $ac_name->AC_NAME;
            }
            if ($get_check_ro_deo_status) {
              $ro_finalize = $get_check_ro_deo_status->ro_ps_finalize;
              $deo_finalize = $get_check_ro_deo_status->deo_ps_finalize;
              $ceo_finalize = $get_check_ro_deo_status->ps_finalize;

              if ($ro_finalize != 1) {
                Session::flash('error_mes', 'RO does not finalized details for ' . $ac_name . 'AC');
                return Redirect::back();
              }
              if ($deo_finalize != 1) {
                Session::flash('error_mes', 'DEO does not finalized details for ' . $ac_name . 'AC');
                return Redirect::back();
              }
              if ($ceo_finalize != 1) {
                Session::flash('error_mes', 'CEO does not finalized details for ' . $ac_name . 'AC');
                return Redirect::back();
              }
            }
            /* Checking if RO and DEO Finalized all PS data or not */

            $ele_details = $this->commonModel->election_detailsac($d->st_code, $ac_no, $dist_no, $d->id, 'AC');
            $data['user_data']      = $d;
            $data['ele_details']    = $ele_details;

            try {

              $total_voter = DB::table('pd_scheduledetail')->select('total', 'electors_total', 'est_voters', 'est_turnout_total')
                ->where('st_code', $ele_details->ST_CODE)
                ->where('ac_no', $ele_details->CONST_NO)
                ->where('election_id', $ele_details->ELECTION_ID)->first();

              $filter_election = [
                'state'         => $d->st_code,
                'ac_no'         => $ac_no,
              ];


              $ac_data         = PollingStationModel::get_ac_data($filter_election);


              $ps_check = PollingStationModel::where('voter_total', 0)
                ->where('st_code', $ele_details->ST_CODE)
                ->where('ac_no', $ele_details->CONST_NO)
                ->first();


              if ($ps_check) {

                Session::flash('error_mes', 'Please Enter Voters in All Polling Stations For ' . $ac_name . 'AC');
                return Redirect::back();
              }


              if (@$ac_data->electors_total != $total_voter->electors_total) {

                Session::flash('error_mes', 'Total Polling Station wise Electors entered did not matched with the ac wise Electors. Please verify ' . $ac_name . 'AC');
                return Redirect::back();
              }


              if (@$ac_data->voter_total == 0) {
                Session::flash('error_mes', 'You Can not Published with 0 Voters ' . $ac_name . 'AC');
                return Redirect::back();
              }
              if (!empty($ac_data)) {


                $est_voters = $total_voter->est_turnout_total;
                if ($est_voters > round(((@$ac_data->voter_total / @$ac_data->electors_total) * 100), 2)) {

                  Session::flash('error_mes', 'Please verify, End of Poll percentage is showing less then estimated close of poll percentage ' . $ac_name . 'AC');
                  return Redirect::back();
                }
              }
              $st = array(
                'updated_at' => date("Y-m-d H:i:s"),
                'added_update_at' => date("Y-m-d"),
                'updated_by' => $d->officername,
                'ceo_finalize' => '1'
              );

              $i = DB::table('pd_schedulemaster')
                ->where('st_code', $ele_details->ST_CODE)
                ->where('ac_no', $ele_details->CONST_NO)
                ->where('election_id', $ele_details->ELECTION_ID)->update($st);

              $st1 = array(
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_at_finalize' => date("Y-m-d H:i:s"),
                'added_update_at' => date("Y-m-d"),
                'updated_by' => $d->officername,
                'is_est_entry_allow' => '1',
                'est_poll_close' => 1,
                'est_voters' => @$total_voter->total,
                'est_turnout_total' => round(((@$ac_data->voter_total / @$ac_data->electors_total) * 100), 2),
                'close_of_poll' => round(((@$ac_data->voter_total / @$ac_data->electors_total) * 100), 2),
                'end_of_poll_finalize' => '1'
              );

              $i = DB::table('pd_scheduledetail')
                ->where('st_code', $ele_details->ST_CODE)
                ->where('ac_no', $ele_details->CONST_NO)
                ->where('election_id', $ele_details->ELECTION_ID)->update($st1);

              //Code for publishing data in to mobile app table `pd_scheduledetail_publish`
              $i = DB::table('pd_scheduledetail_publish')
                ->where('st_code', $ele_details->ST_CODE)
                ->where('ac_no', $ele_details->CONST_NO)
                ->where('election_id', $ele_details->ELECTION_ID)->update($st1);

              Session::flash('success_mes', 'Turnout successfully Published');
              return Redirect::back();
              //return Redirect::to('roac/turnout/schedule-entry');
            } catch (\Exception $e) {
              Session::flash('error_mes', 'Please try again.');
              return Redirect::back();
            }
          }
        }
      }
    }
  }

  public function getCeoPsWiseElectoralDetails(Request $request)
  {
    return PsWiseElectorsDetailsService::getPSWiseElectorsDetails($request, $this->actionElectoralDetails, $this->view_path . '.polling_station.PsWiseElectoralDetails');
  }

  //EXCEL REPORT STARTS
  public function getCeoPsWiseElectoralDetailsExcel(Request $request)
  {
    $data =  PsWiseElectorsDetailsService::getPSWiseElectorsDetails($request->merge(['is_excel' => 1]), $this->actionElectoralDetails, $this->view_path . '.polling_station.PsWiseElectoralDetails');

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['AC No', 'AC Name', 'PS No', 'PS Name', 'Location Type', 'PS Type', 'Electors Male', 'Electors Female', 'Electors Other', 'Electors Total'];

    $arr  = array();
    $TotalElectorMale = 0;
    $TotalElectorFeMale = 0;
    $TotalElectorOther = 0;
    $TotalElector = 0;
    $totalvalues[] = [];
    $headings[] = [];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        ($lis->acno) ? ($lis->acno) : '0',
        ($lis->acn) ? ($lis->acn) : '-',
        ($lis->PS_NO) ? ($lis->PS_NO) : '0',
        ($lis->PS_NAME_EN) ? ($lis->PS_NAME_EN) : '-',
        ($lis->LOCN_TYPE) ? ($lis->LOCN_TYPE) : '0',
        ($lis->PS_TYPE) ? ($lis->PS_TYPE) : '0',
        ($lis->electors_male) ? ($lis->electors_male) : '0',
        ($lis->electors_female) ? ($lis->electors_female) : '0',
        ($lis->electors_other) ? ($lis->electors_other) : '0',
        ($lis->electors_total) ? ($lis->electors_total) : '0',

      ];

      $TotalElectorMale   += $lis->electors_male;
      $TotalElectorFeMale += $lis->electors_female;
      $TotalElectorOther  += $lis->electors_other;
      $TotalElector       += $lis->electors_total;

      $totalvalues = array('Total', '', '', '', '', '', $TotalElectorMale, $TotalElectorFeMale, $TotalElectorOther, $TotalElector);
    }
    $export_data[] = $totalvalues;

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');

  }
  //EXCEL REPORT ENDS


  public function getCeoPsWiseElectoralDetailsPdf(Request $request)
  {
    $data =  PsWiseElectorsDetailsService::getPSWiseElectorsDetails($request->merge(['is_excel' => 1]), $this->actionElectoralDetails, $this->view_path . '.polling_station.PsWiseElectoralDetails');
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.polling_station.PsWiseElectoralDetailsPdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }
}  // end class
