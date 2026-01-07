<?php

namespace App\Http\Controllers\Admin\PollingStation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\commonModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;

//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Exports\ExcelExport;
use App\Helpers\LogNotification;
use App\Http\Controllers\Admin\turnout\MissingTurnoutController;
use App\models\AC;
use App\models\Admin\ElectorModel;
use App\models\Admin\EndOfPollFinaliseModel;
use Maatwebsite\Excel\Facades\Excel;

//POLLING STATION MODELS
use App\models\Admin\polling_station\PollingStationModel;
use App\models\Admin\PollingStation;
use App\models\Admin\turnout\TurnoutModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

//current

class EciPollingStationController extends Controller
{


  public $base          = 'ro';
  public $folder        = 'eci';
  public $action        = 'eci/turnout/EciPsWiseDetails';
  public $action_pc     = 'eci/turnout/EciPsWiseDetails/state';
  public $action_ac     = 'eci/turnout/EciPsWiseDetails/state/ac';
  public $action_ps     = 'eci/turnout/EciPsWiseDetails/state/ps';
  public $action_electors     = 'eci/turnout/fetchElectorsCountPanel';
  public $action_electors_ac     = 'eci/turnout/fetchACElectorsCountPanel';
  public $fetch_electors_pc     = 'eci/turnout/fecthGetGenderWiseElectorsCountForPC';
  public $fetch_electors_ac     = 'eci/turnout/fecthGetGenderWiseElectorsCountForAC';
  public $view_path     = "admin.ac.eci";
  public $commonModel     = null;
  public $voting_model     = null;
  public $PollingStationModel     = null;
  public $MissingTurnoutModel     = null;
  public $turnout     = null;


  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->PollingStationModel = new PollingStationModel();
    $this->MissingTurnoutModel = new MissingTurnoutController();
    $this->turnout = new TurnoutModel();
    if (!Auth::user()) {
      return redirect('/officer-login');
    }
  }



  //ALL PCS BY STATE CODE
  public function get_ac_list(Request $request)
  {

    $data['state'] = NULL;
    if ($request->has('state')) {
      $data['state'] = $request->state;
    }
    $election_id = Auth::user()->election_id;
    $AC_LIST = DB::table('m_ac')->join('m_election_details', [
      ['m_election_details.ST_CODE', '=', 'm_ac.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_ac.AC_NO']
    ])
      ->where('m_election_details.CONST_TYPE', 'AC')
      ->where('m_election_details.election_status', '1')
      ->where('m_election_details.ELECTION_ID', $election_id)
      ->where('m_ac.ST_CODE', '=', $data['state'])
      ->orderBy('m_ac.AC_NAME', 'ASC')
      ->get();

    if ($AC_LIST) {

      return response()->json(['error' => false, 'status' => 200, 'data' => $AC_LIST]);
    } else {
      return response()->json(['error' => true, 'status' => 401, 'data' => '']);
    }
  }


  public function EciPsWiseDetails(Request $request)
  {
    $data = [];
    $request_array = [];
    $data['phases'] = PhaseModel::get_phases();
    //set title
    $title_array  = [];
    $data['heading_title'] = "PS Wise Voter Turnout";
    $data['state'] = NULL;
    if ($request->has('state')) {
      $data['state'] = $request->state;
    }
    $data['ac_id'] = NULL;
    if ($request->has('ac_id')) {
      $data['ac_id'] = $request->ac_id;
    }
    $xss = new xssClean;
    //CHECKING REQUEST VARIABES STARTS
    if ($request->has('state') && $request->has('ac_id')) {

      $validator = Validator::make($request->all(), [
        'state'          => 'required|string',
        'ac_id'          => 'required|numeric',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $state    = $xss->clean_input($request->state);
      $ac_id    = $xss->clean_input($request->ac_id);


      $filter_election = [
        'state'         => $state,
        'ac_no'         => $ac_id,
      ];

      $request_array[] =  'state=' . $state;
      $request_array[] =  'ac_id=' . $ac_id;

      $statename = getstatebystatecode($state);
      $acame = getacbyacno($state, $ac_id);


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
      $object         = PollingStationModel::get_ps_data($filter_election);
      $data['results']    =   $object;
    } else {
      $data['buttons']    = [];
      $data['action']         = url($this->action);
      $data['results'] = [];
    }


    $data['user_data']  =   Auth::user();

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }

      return $data;
    }


    return view($this->view_path . '.polling_station.PsWiseDetails', $data);
  }


  //EXCEL REPORT STARTS
  public function EciPsWiseDetailsExcel(Request $request)
  {
    set_time_limit(6000);
    $data = $this->EciPsWiseDetails($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['PS No', 'PS Name', 'PS Type', 'Electors Male', 'Electors Female', 'Electors Other', 'Electors Total', 'Voter Male', 'Voter Female', 'Voter Other', 'Voter Total'];

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

      $totalvalues = array('Total', '', '', $TotalElectorMale, $TotalElectorFeMale, $TotalElectorOther, $TotalElector, $TotalVoterMale, $TotalVoterFeMale, $TotalVoterOther, $TotalVoter);
    }
    $export_data[] = $totalvalues;

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
  }
  //EXCEL REPORT ENDS


  public function EciPsWiseDetailsPdf(Request $request)
  {
    $data = $this->EciPsWiseDetails($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.polling_station.PsWiseDetailsPdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }

  public function getEciAcsListForMissedEntry(Request $request)
  {

    //AC CEO ESTIMATE POLL TRUNOUT MISSED AC  REPORT NEW TRY CATCH STARTS HERE
    try {

      $user = Auth::user();
      if ($user->officername != 'ECIECI2' && $user->officername != 'PLANDIV') {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
      }
      $st_code     = base64_decode($request->input('state', ''));
      $stateEncript = $request->input('state', '');
      $request->merge([
        'is_excel' => 1,
        'state' => ($st_code != 'all') ? base64_encode($st_code) : null
      ]);
      $data = $this->MissingTurnoutModel->get_enable_acs_for_update($request);
      $data['stateEncript'] =  $stateEncript;
      //buttons
      $data['buttons']    = [];

      $data['action']         = url('eci/turnout/EnableClosePollEntry');

      $results = [];
      foreach ($data['results'] as $key => $result) {
        $results[] = [
          'label'                     => $result['label'],
          'ac_no'                     => $result['ac_no'],
          'st_code'                   => $result['st_code'],
          'ac_name'                   => $result['ac_name'],
          'name'                      => $result['name'],
          'Phone_no'                  => $result['Phone_no'],
          "est_turnout_round1"        => $result['est_turnout_round1'],
          "est_turnout_round2"        => $result['est_turnout_round2'],
          "est_turnout_round3"        => $result['est_turnout_round3'],
          "est_turnout_round4"        => $result['est_turnout_round4'],
          "est_turnout_round5"        => $result['est_turnout_round5'],
          "missed_status_round1"      => $result['missed_status_round1'],
          "missed_status_round2"      => $result['missed_status_round2'],
          "missed_status_round3"      => $result['missed_status_round3'],
          "missed_status_round4"      => $result['missed_status_round4'],
          "missed_status_round5"      => $result['missed_status_round5'],
          "missed_status_round6"      => $result['missed_status_round6'],
          "modification_status_round1"  => $result['modification_status_round1'],
          "modification_status_round2"  => $result['modification_status_round2'],
          "modification_status_round3"  => $result['modification_status_round3'],
          "modification_status_round4"  => $result['modification_status_round4'],
          "modification_status_round5"  => $result['modification_status_round5'],
          "modification_status_round6"  => $result['modification_status_round6'],
          "est_poll_close"  => $result['est_poll_close'],
          'href'                      => 'javascript:void(0)',
        ];
      }


      $data['st_code'] = $st_code;
     
      $data['results'] = $results;
      $filter = [
        'ac_no'         => $data['ac_no'],
        'election_id'   => $user->election_id,
        'phase_no'      => $data['phase'],
        'pc_no'         => '',
      ];
      $estimated_time = $this->turnout->get_scheduletime($filter);
      $data['estimated_time'] = $estimated_time;
      $data['acs'] = AcModel::get_distinct_acs_with_state_name(['st_code' => $st_code, 'phase' => $data['phase']]);
      return view('admin.turnout.missed.enable-close-poll-entry', $data);
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function fetchElectorsCountPanel(Request $request)
  {
    $data = [];
    $request_array = [];
    $data['phases'] = PhaseModel::get_phases();
    //set title
    $title_array  = [];
    $filter_election = [];
    $data['heading_title'] = "Fetch PS Wise Electors Details From EROnet";
    $data['state'] = NULL;
    if ($request->has('state') && $request->input('state') != 'all') {
      $data['state'] = $request->state;
    }
    $data['ac_id'] = NULL;
    if ($request->has('ac_id') && $request->input('ac_id') != null) {
      $data['ac_id'] = $request->ac_id;
    }
    $xss = new xssClean;
    //CHECKING REQUEST VARIABES STARTS
    if ($request->has('state') && $request->input('state') != 'all') {
      $condition = ['state' => 'required|string'];
      if ($request->has('ac_id') && $request->input('ac_id') != null) {
        $condition['ac_id'] = 'required|numeric';
      }
      // dd($request->all());
      $validator = Validator::make($request->all(), $condition);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $state    = $xss->clean_input($request->state);
      $filter_election = [
        'state'         => $state
      ];
      $request_array[] =  'state=' . $state;
      $statename = getstatebystatecode($state);
      $title_array[] = "State: " . $statename->ST_NAME;
      if ($request->has('ac_id') && $request->input('ac_id') != null) {
        $ac_id    = $xss->clean_input($request->ac_id);
        $filter_election['ac_no'] = $ac_id;
        $request_array[] =  'ac_id=' . $ac_id;
        $acame = getacbyacno($state, $ac_id);
        $title_array[] = "AC: " . $acame->AC_NAME;
      }

      $data['filter_buttons'] = $title_array;

      //buttons
      $data['buttons']    = [];
    } else {
      $data['buttons']    = [];
    }
    $object         = PollingStationModel::get_ps_data($filter_election);
    $data['results']    =   $object;

    $data['action']         = url($this->action_electors);
    $data['fetch_electors_pc'] = url($this->fetch_electors_pc);
    $data['user_data']  =   Auth::user();
    $data['jobs_in_process']  =   PollingStationModel::getCurrentJobCount();
    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }
    return view($this->view_path . '.polling_station.PsWiseElectorsCountDetails', $data);
  }

  public function fetchACElectorsCountPanel(Request $request)
  {
    $data = [];
    $request_array = [];
    $data['phases'] = PhaseModel::get_phases();
    //set title
    $title_array  = [];
    $data['heading_title'] = "Fetch AC Wise Electors Details From EROnet";
    $data['state'] = NULL;
    if ($request->has('state') && $request->input('state') != 'all') {
      $data['state'] = $request->state;
    }
    $data['ac_id'] = NULL;
    if ($request->has('ac_id') && $request->input('ac_id') != null) {
      $data['ac_id'] = $request->ac_id;
    }
    $xss = new xssClean;
    //CHECKING REQUEST VARIABES STARTS
    $filter_election = [];
    if ($request->has('state') && $request->input('state') != 'all') {
      $condition = ['state' => 'required|string'];
      if ($request->has('ac_id') && $request->input('ac_id') != null) {
        $condition['ac_id'] = 'required|numeric';
      }
      // dd($request->all());
      $validator = Validator::make($request->all(), $condition);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $state    = $xss->clean_input($request->state);
      $filter_election = [
        'state'         => $state
      ];
      $request_array[] =  'state=' . $state;
      $statename = getstatebystatecode($state);
      $title_array[] = "State: " . $statename->ST_NAME;
      if ($request->has('ac_id') && $request->input('ac_id') != null) {
        $ac_id    = $xss->clean_input($request->ac_id);
        $filter_election['ac_no'] = $ac_id;
        $request_array[] =  'ac_id=' . $ac_id;
        $acame = getacbyacno($state, $ac_id);
        $title_array[] = "AC: " . $acame->AC_NAME;
      }

      $data['filter_buttons'] = $title_array;
    }
    $data['buttons']    = [];
    $object         = ElectorModel::getList($filter_election);
    $data['results']    =   $object;
    $data['action']         = url($this->action_electors_ac);
    $data['fetch_electors_ac'] = url($this->fetch_electors_ac);
    $data['user_data']  =   Auth::user();
    $data['jobs_in_process']  =   PollingStationModel::getCurrentJobCount();
    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }
    return view($this->view_path . '.polling_station.ACWiseElectorsCountDetails', $data);
  }

  public function EndOfPollFinalizeReport(Request $request)
  {
    ini_set("memory_limit", "1500M");
    set_time_limit('6000');
    ini_set("pcre.backtrack_limit", "50000000");
    $data = [];
    //set title
    $title_array  = [];
    $data['heading_title'] = "End Of Poll Finalize Report";

    $xss = new xssClean;
    $sql = "SELECT pdsd.scheduleid AS phase, pdsd.ELECTION_TYPEID, pdsd.st_code, state.st_name,
    (SELECT COUNT(*) FROM pd_scheduledetail WHERE st_code= pdsd.st_code AND scheduleid = pdsd.scheduleid) AS totalac,
    (SELECT COUNT(*) FROM pd_scheduledetail WHERE st_code= pdsd.st_code AND scheduleid = pdsd.scheduleid) AS totalac,
    (SELECT COUNT(DISTINCT(AC_NO)) FROM polling_station WHERE ST_CODE= pdsd.st_code AND scheduleid = pdsd.scheduleid AND ro_ps_finalize = '1') AS ro_finalize,
    (SELECT COUNT(DISTINCT(AC_NO)) FROM polling_station WHERE ST_CODE= pdsd.st_code AND scheduleid = pdsd.scheduleid AND deo_ps_finalize = '1') AS deo_finalize,
    (SELECT COUNT(DISTINCT(AC_NO)) FROM polling_station WHERE ST_CODE= pdsd.st_code AND scheduleid = pdsd.scheduleid AND ps_finalize = '1') AS ceo_finalize,
    (SELECT COUNT(*) FROM pd_scheduledetail WHERE ST_CODE= pdsd.st_code AND scheduleid = pdsd.scheduleid AND end_of_poll_finalize = '1') AS publish
    FROM pd_scheduledetail AS pdsd, m_state AS state
    WHERE state.st_code=pdsd.st_code
    GROUP BY pdsd.scheduleid, pdsd.st_code";
    $result = DB::select($sql);
    $data['results']    =   [];
    $data['buttons']    = [];
    if ($result) {
      $data['results']    =   $result;
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('eci/turnout/EndOfPollFinalizeReport/excel'),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Details Report Excel',
        'href' =>  url('eci/turnout/EndOfPollFinalizeReport/excel') . '?details=yes',
        'target' => true
      ];
    }


    $data['user_data']  =   Auth::user();
    $data['self']  =   $this;
    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }
    return view($this->view_path . '.polling_station.EndOfPollFinalizeReport', $data);
  }

  public function EndOfPollFinalizeDetailsReport(Request $request)
  {
    ini_set("memory_limit", "1500M");
    set_time_limit('6000');
    ini_set("pcre.backtrack_limit", "50000000");
    $data = [];
    //set title
    $title_array  = [];
    $data['heading_title'] = "End Of Poll Finalize Detail Report";
    $sql = "SELECT a.st_code,b.st_name,m.ac_no,m.ac_name, pdt.scheduleid,pdt.ELECTION_TYPEID,
    a.ro_ps_finalize,a.deo_ps_finalize,a.ps_finalize AS ceo_finalize,pdt.end_of_poll_finalize AS publish,
    pdt.close_of_poll, pdt.dist_no, dist.DIST_NAME
      FROM polling_station a,m_state b,m_election_details c,m_ac m,pd_schedulemaster pd,pd_scheduledetail pdt, m_district as dist
      WHERE a.ST_CODE=b.ST_CODE
      AND c.ST_CODE=a.ST_CODE
      AND c.CONST_NO=a.AC_NO
      AND c.CONST_TYPE='AC'
      AND m.ac_no=c.const_no
      AND m.st_code=b.st_code
      AND pd.st_code=b.st_code
      AND pd.ac_no=a.ac_no
      AND pdt.st_code=b.st_code
      AND pdt.ac_no=a.ac_no
      AND dist.DIST_NO = pdt.dist_no
      AND dist.ST_CODE = a.ST_CODE
      GROUP BY st_code,m.ac_no
      ORDER BY pdt.scheduleid, m.ST_CODE, m.ac_no";
    $result = DB::select($sql);
    $data['results']    =   [];

    $data['buttons']    = [];
    if ($result) {
      $data['results']    =   $result;
    }
    if (isset($title_array) && count($title_array) > 0) {
      $data['heading_title'] .= "- " . implode(', ', $title_array);
    }
    return $data;
  }

  public function EndOfPollFinalizeReportExcel(Request $request)
  {
    set_time_limit(6000);
    $headings[] = [];
    $export_data = [];
    if ($request->has('details') && $request->input('details') == 'yes') {
      $data = $this->EndOfPollFinalizeDetailsReport($request);
      $export_data[] = [$data['heading_title']];
      $export_data[] = ['Phase', 'Election Type', 'State Code', 'State', 'Dist No', 'Dist Name', 'AC No', 'AC Name', 'Close Of Poll', 'End Of Poll', 'RO Finalize Flag', 'RO Finalize', 'DEO Finalize Flag', 'DEO Finalize', 'CEO Finalize Flag', 'CEO Finalize', 'Published', 'Published'];
      foreach ($data['results'] as $lis) {
        $export_data[] = [
          ($lis->scheduleid) ? $this->getPhaseForReport($lis->scheduleid) : '-',
          ($lis->ELECTION_TYPEID) ? $this->getElectionType($lis->ELECTION_TYPEID) : '-',
          ($lis->st_code) ? ($lis->st_code) : '-',
          ($lis->st_name) ? ($lis->st_name) : '-',
          ($lis->dist_no) ? ($lis->dist_no) : '-',
          ($lis->DIST_NAME) ? ($lis->DIST_NAME) : '-',
          ($lis->ac_no) ? ($lis->ac_no) : '0',
          ($lis->ac_name) ? ($lis->ac_name) : '0',
          ($lis->close_of_poll) ? ($lis->close_of_poll) : '0',
          ($lis->ac_no) ? $this->getEndOfPoll($lis->ac_no, $lis->st_code) : '-',
          ($lis->ro_ps_finalize) ? ($lis->ro_ps_finalize) : '0',
          ($lis->ro_ps_finalize) ? 'Yes' : 'No',
          ($lis->deo_ps_finalize) ? ($lis->deo_ps_finalize) : '0',
          ($lis->deo_ps_finalize) ? 'Yes' : 'No',
          ($lis->ceo_finalize) ? ($lis->ceo_finalize) : '0',
          ($lis->ceo_finalize) ? 'Yes' : 'No',
          ($lis->publish) ? ($lis->publish) : '0',
          ($lis->publish) ? 'Yes' : 'No',


        ];
      }
    } else {
      $data = $this->EndOfPollFinalizeReport($request->merge(['is_excel' => 1]));
      $export_data[] = [$data['heading_title']];
      $export_data[] = ['Phase', 'Election Type', 'State Code', 'State', 'Total AC', 'RO Finalize', 'DEO Finalize', 'CEO Finalize', 'Total Published'];
      foreach ($data['results'] as $lis) {
        $export_data[] = [
          ($lis->phase) ? $this->getPhaseForReport($lis->phase) : '-',
          ($lis->ELECTION_TYPEID) ? $this->getElectionType($lis->ELECTION_TYPEID) : '-',
          ($lis->st_code) ? ($lis->st_code) : '-',
          ($lis->st_name) ? ($lis->st_name) : '-',
          ($lis->totalac) ? ($lis->totalac) : '0',
          ($lis->ro_finalize) ? ($lis->ro_finalize) : '0',
          ($lis->deo_finalize) ? ($lis->deo_finalize) : '0',
          ($lis->ceo_finalize) ? ($lis->ceo_finalize) : '0',
          ($lis->publish) ? ($lis->publish) : '0',
        ];
      }
    }
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
  }

  public function getPhaseForReport($phase)
  {
    switch ($phase) {
      default:
        return 'Phase ' . $phase;
        break;
    }
  }

  public function getElectionType($type)
  {
    switch ($type) {
      case '4':
        return 'BYE Election';
        break;
      case '3':
        return 'General Election';
        break;
      case '2':
        return 'BYE Election';
        break;
      case '1':
        return 'General Election';
        break;
      default:
        return $type;
        break;
    }
  }

  public function getEndOfPoll($ac_no, $st_code)
  {

    $data = DB::table("pd_scheduledetail")->select("total", "electors_total")->where('st_code', $st_code)->where('ac_no', $ac_no)->first();
    if ($data) {
      return ($data->electors_total) ? round(($data->total / $data->electors_total) * 100, 2) : '-';
    } else {
      return '-';
    }
  }

  public function AcECIPSElectoralDefinalzied(Request $request)
  {
    try {
      ini_set("memory_limit", "1500M");
      set_time_limit('6000');
      ini_set("pcre.backtrack_limit", "50000000");
      $user = Auth::user();
      if ($user->officername != 'ECIECI2' && $user->officername != 'PLANDIV') {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
      }
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $data = [];
      $data['user_data'] = $user_data;
      $data['election_type'] = ($request->has('election_type')) ?? $request->election_type;
      $data['phase'] = ($request->has('phase')) ? $request->phase : 1;
      $data['state'] = ($request->has('state') && $request->input('state') != 'all') ? $request->state : null;
      $data['results'] = [];
      $data['states'] = EndOfPollFinaliseModel::with('state')->orderBy('st_code')->groupBy('st_code')->get();
      $data['phases'] = PhaseModel::get_phases(['election_type' => $data['election_type'], 'state' => $data['state']]);
      // $sql = "SELECT sm.ac_no,sm.ST_CODE, ac.AC_NAME, s.ST_NAME FROM pd_schedulemaster AS sm 
      // JOIN m_ac AS ac ON (ac.AC_NO = sm.ac_no AND ac.ST_CODE = sm.ST_CODE)
      // JOIN m_state AS s ON (s.ST_CODE = sm.ST_CODE)
      // WHERE sm.schedule_id = ".$data['phase'];
      //   if ($data['state'] != null) {
      //     $sql .= "AND sm.ST_CODE = ".$data['state'];
      //   }
      $results = DB::table('pd_schedulemaster as sm')->join('m_ac as ac', function ($join) {
        $join->on('ac.AC_NO', '=', 'sm.ac_no');
        $join->on('ac.ST_CODE', '=', 'sm.ST_CODE');
      })->join('m_state as s', 's.ST_CODE', '=', 'sm.ST_CODE')
        ->where('sm.schedule_id', $data['phase'])
        ->where(function ($q) use ($data) {
          if ($data['state'] != null) {
            $q->where('sm.st_code', $data['state']);
          }
        })
        ->select(['sm.ac_no', 'sm.ST_CODE', 'ac.AC_NO', 'ac.ST_CODE', 'ac.AC_NAME', 's.ST_NAME'])
        ->orderBy('ac.ST_CODE')
        ->orderBy('ac.AC_NO')->get();
      $data['results'] = [];
      foreach ($results as $key => $item) {
        $temp = (array)$item;
        $total_ps = PollingStation::getAcPollingStationCount($item->ST_CODE, $item->AC_NO);
        $total_ps_finalized = PollingStation::getAcPollingStationFinalizedCount($item->ST_CODE, $item->AC_NO);
        $total_ps_enable_for_edit = PollingStation::getAcPollingStationEnableForEditCount($item->ST_CODE, $item->AC_NO);
        if ($request->has('excel') && $request->input('excel') == 'download') {
          $temp = [];
          $temp['ST_CODE'] = $item->ST_CODE;
          $temp['ST_NAME'] = $item->ST_NAME;
          $temp['AC_NO'] = $item->AC_NO;
          $temp['AC_NAME'] = $item->AC_NAME;
          $temp['ps_finalized'] = (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) ? 'Finalized' : 'Not Yet Finalize';
        } else {
          $temp['ps_finalized'] = (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) ? 1 : 0;
          $temp['show_enable_edit_btn'] = ($total_ps_enable_for_edit > 0) ? 1 : 0;
        }
        $data['results'][] = $temp;
      }
      $filter = [
        'st_code'       => $data['state'],
        'election_id'   => $user->election_id,
        'pc_no'         => '',
      ];
      if ($data['phase'] != 1) {
        $filter['phase_no'] = $data['phase'];
      }
      $estimated_time = $this->turnout->get_scheduletime($filter);
      $data['poll_date'] = $estimated_time->poll_date;
      $data['showDefinalizeAndEditEnableBtn'] = (date('Y-m-d') >= $estimated_time->poll_date) ? true : false;
      $data['heading_title'] = 'AC List with Polling Station Electorals Finalized Status';
      if ($request->has('excel') && $request->input('excel') == 'download') {
        $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], "Polling_Station_Electoral_Finalize_" . $data['state'] . "_Report"));
        $headings = [
          "State Code",
          "State Name",
          "AC No",
          "AC Name",
          "Status",
        ];
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report Imports';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report Imports done for state " . $data['state'];
        LogNotification::LogInfo($ErrorMessage);
        return Excel::download(new ExcelExport($headings, $data['results']), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
      } else {
        return view('admin.turnout.AcECIPSElectoralDefinalzied', $data);
      }
    } catch (Exception $ex) {
      dd($ex);
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report';
      $ErrorMessage['TransectionStatus'] = 'Failed';
      $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report failed ";
      LogNotification::LogInfo($ErrorMessage);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function AcECIPSElectoralDefinalziedUpdate(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'ac_no'   => 'required',
        'st_code'   => 'required',
        'disableEdit'   => 'required',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $data['user_data'] = $user_data;
      if ($request->disableEdit == 1) {
        $update = [
          'electors_enable_edit_by_eci' => 0,
          'electors_enable_edit_by_eci_datetime' => date('Y-m-d H:i:s', time())
        ];
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral disabled modification Option';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral disabled modification Option successfully for state " . $request->st_code . " AC " . $request->ac_no;
        LogNotification::LogInfo($ErrorMessage);
      } else {
        $update = [
          'electors_finalize_by_ro' => 0,
          'electors_finalize_by_ro_date' => date('Y-m-d H:i:s', time()),
          'electors_enable_edit_by_eci' => 1,
          'electors_enable_edit_by_eci_datetime' => date('Y-m-d H:i:s', time())
        ];
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral definalized and Enable Edit Option';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral definalized and Enable Edit Option successfully for state " . $request->st_code . " AC " . $request->ac_no;
        LogNotification::LogInfo($ErrorMessage);
      }
      PollingStation::where('ST_CODE', $request->st_code)->where('AC_NO', $request->ac_no)->update($update);
      $msg = ($request->disableEdit == 1) ? "Polling Station Electorals modification is disabled RO has to Finalize it Again from thier respected Account" : "Polling Station Electorals is definalized and Modification option is enabled";
      return redirect()->back()->with("success", $msg);
    } catch (Exception $ex) {
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral definalized and Enable Edit Option';
      $ErrorMessage['TransectionStatus'] = 'Failed';
      $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral definalized and Enable Edit Option failed for AC " . $request->ac_no;
      LogNotification::LogInfo($ErrorMessage);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }
}  // end class