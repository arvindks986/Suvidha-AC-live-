<?php

namespace App\Http\Controllers\Admin\turnout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\commonModel;
use App\models\Admin\MissedTurnoutModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;

use App\Exports\ExcelExport;
use App\Helpers\LogNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class MissingTurnoutController extends Controller
{

  public $base              = 'ro';
  public $folder            = 'eci';
  public $action            = 'eci/turnout/get_missed';
  public $action_missed_ac  = "eci/turnout/list-schedule/state/ac/missed";
  public $view_path         = "admin.turnout";
  public $commonModel     = null;

  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      return $next($request);
    });
  }

  public function get_missed(Request $request)
  {

    $data = [];
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];

    $data['election_type'] = NULL;
    if ($request->has('election_type')) {
      $data['election_type'] = $request->election_type;
      $request_array[] =  'election_type=' . $request->election_type;
    }



    $data['state_encrypted'] = null;
    $data['state'] = NULL;
    if ($request->has('state')) {

      //valid a state is exist in the current filter phase
      $is_state_valid = StateModel::get_pc_states_with_filter([
        'state' => base64_decode($request->state),
        'election_type' => $data['election_type'],
      ]);

      if (count($is_state_valid) > 0) {
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state=' . $request->state;
      }
      $data['state_encrypted'] = $request->state;
    }

    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }

    $data['ac_no'] = NULL;
    if ($request->has('ac_no')) {
      $data['ac_no']    = $request->ac_no;
      $request_array[]  = 'ac_no=' . $request->ac_no;
    }

    if ($request->has('round')) {
      $data['round']  = $request->round;
      $request_array[]  = 'round=' . $request->round;
    } else {
      $data['round']  = 0;
    }


    //set title
    $title_array  = [];
    $data['heading_title'] = "Ac's Not filled report";

    // if($data['phase']){
    //   $title_array[] = "Phase: ".$data['phase'];
    // }

    if ($data['state']) {
      $state_object = StateModel::get_state_by_code($data['state']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }

    if ($data['ac_no'] && $data['state']) {
      $ac_object = AcModel::get_record([
        'state' => $data['state'],
        'ac_no' => $data['ac_no']
      ]);
      if ($ac_object) {
        $title_array[] = "Consituency: " . $ac_object['ac_name'];
      }
    }

    $data['filter_buttons'] = $title_array;

    $filter_for_state = [
      'election_type' => $data['election_type'],
    ];

    $states = StateModel::get_pc_states_with_filter($filter_for_state);

    $data['states'] = [];
    foreach ($states as $result) {
      $data['states'][] = [
        'code' => base64_encode($result->ST_CODE),
        'name' => $result->ST_NAME,
      ];
    }

    $filter_for_phases = [
      'election_type' => $data['election_type'],
      'state' => $data['state']
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

    $data['filter']   = implode('&', array_merge($request_array));
    //end set title

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

    $data['consituencies']  = PcModel::get_records([
      'election_type' => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase']
    ]);

    $results                = [];

    $filter_election = [
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'ac_no'         => $data['ac_no'],
      'group_by'      => 'ac_no',
      'order_by'      => 'ac_no',
      'round'         => $data['round'],
      'level'      => 'ceo'
    ];



    if ($data['round']) {

      $object         = MissedTurnoutModel::get_reports($filter_election);

      //dd($object);

      foreach ($object as $result) {


        $individual_filter    = implode('&', array_merge($request_array, [
          'ac_no' => 'ac_no=' . $result->ac_no,
        ]));

        $ac_name    = '';
        $get_ac     = AcModel::get_record([
          'state' => $result->st_code,
          'ac_no' => $result->ac_no
        ]);

        if ($get_ac) {
          $ac_name = $get_ac['ac_name'];
        }

        $state_name = '';
        $state_object = StateModel::get_state_by_code($result->st_code);
        if ($state_object) {
          $state_name = $state_object['ST_NAME'];
        }

        $results[] = [
          'label'                 => $state_name,
          'ac_no'                 => $result->ac_no,
          'ac_name'               => $ac_name,
          'filter'                => $individual_filter,
          "st_code"               => $result->st_code,
          "name"                  => $result->name,
          "Phone_no"              => $result->Phone_no,
          "est_turnout_round1"    => $result->est_turnout_round1,
          "est_turnout_round2"    => $result->est_turnout_round2,
          "est_turnout_round3"    => $result->est_turnout_round3,
          "est_turnout_round4"    => $result->est_turnout_round4,
          "est_turnout_round5"    => $result->est_turnout_round5,
          "missed_status_round1"  => $result->missed_status_round1,
          "missed_status_round2"  => $result->missed_status_round2,
          "missed_status_round3"  => $result->missed_status_round3,
          "missed_status_round4"  => $result->missed_status_round4,
          "missed_status_round5"  => $result->missed_status_round5,
          "missed_status_round6"  => $result->missed_status_round6,
          "modification_status_round1"  => $result->modification_status_round1,
          "modification_status_round2"  => $result->modification_status_round2,
          "modification_status_round3"  => $result->modification_status_round3,
          "modification_status_round4"  => $result->modification_status_round4,
          "modification_status_round5"  => $result->modification_status_round5,
          "modification_status_round6"  => $result->modification_status_round6,
          "href"                  => 'javascript:void(0)'
        ];
      }
    }

    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();

    //if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
    //   return $data;
    // }

    $data['heading_title_with_all'] = $data['heading_title'];

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path . '.missed.report_missed', $data);

    try {
    } catch (Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }



  public function get_enable_acs_for_update(Request $request)
  {
    $data = [];
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];

    $data['election_type'] = NULL;
    if ($request->has('election_type')) {
      $data['election_type'] = $request->election_type;
      $request_array[] =  'election_type=' . $request->election_type;
    }



    $data['state'] = NULL;

    if (Auth::user()->designation == 'ECI') {
      $data['state'] = base64_decode($request->has('state'));
    }

    if ($request->has('state')) {

      //valid a state is exist in the current filter phase
      $is_state_valid = StateModel::get_ac_states_with_filter_for_close_poll([
        'state' => base64_decode($request->state),
        'election_type' => $data['election_type']
      ]);

      if (count($is_state_valid) > 0) {
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state=' . $request->state;
      }
    }

    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }

    $data['ac_no'] = NULL;
    if ($request->has('ac_no')) {
      $data['ac_no']    = $request->ac_no;
      $request_array[]  = 'ac_no=' . $request->ac_no;
    }

    if ($request->has('round')) {
      $data['round']  = $request->round;
      $request_array[]  = 'round=' . $request->round;
    } else {
      $data['round']  = 0;
    }


    //set title
    $title_array  = [];
    $data['heading_title'] = "Ac's Not filled report";

    // if($data['phase']){
    //   $title_array[] = "Phase: ".$data['phase'];
    // }

    if ($data['state']) {
      $state_object = StateModel::get_state_by_code($data['state']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }

    if ($data['ac_no'] && $data['state']) {
      $ac_object = AcModel::get_record([
        'state' => $data['state'],
        'ac_no' => $data['ac_no']
      ]);
      if ($ac_object) {
        $title_array[] = "Consituency: " . $ac_object['ac_name'];
      }
    }

    $data['filter_buttons'] = $title_array;

    $filter_for_state = [
      'election_type' => $data['election_type']
    ];

    // $states = StateModel::get_pc_states_with_filter($filter_for_state);
    $states = StateModel::get_ac_states_with_filter_for_close_poll($filter_for_state);

    $filter_for_phases = [
      'election_type' => $data['election_type'],
      'state' => $data['state']
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

    $data['states'] = [];
    foreach ($states as $result) {
      $data['states'][] = [
        'code' => base64_encode($result->ST_CODE),
        'name' => $result->ST_NAME,
      ];
    }

    $data['filter']   = implode('&', array_merge($request_array));
    //end set title

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

    $data['action']         = url('eci/turnout/get-enable-eci-acs');

    $data['consituencies']  = PcModel::get_records([
      'election_type' => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase']
    ]);

    $results                = [];

    $filter_election = [
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'ac_no'         => $data['ac_no'],
      'group_by'      => 'ac_no',
      'order_by'      => 'ac_no',
      'round'         => $data['round'],
      'level'      => 'eci'
    ];
    //dd($request->all());
    //dd('test');

    if ($data['round']) {

      $object         = MissedTurnoutModel::get_reports($filter_election);

      // dd($object);

      foreach ($object as $result) {


        $individual_filter    = implode('&', array_merge($request_array, [
          'ac_no' => 'ac_no=' . $result->ac_no,
        ]));

        $ac_name    = '';
        $get_ac     = AcModel::get_record([
          'state' => $result->st_code,
          'ac_no' => $result->ac_no
        ]);

        if ($get_ac) {
          $ac_name = $get_ac['ac_name'];
        }

        $state_name = '';
        $state_object = StateModel::get_state_by_code($result->st_code);
        if ($state_object) {
          $state_name = $state_object['ST_NAME'];
        }

        $results[] = [
          'label'                     => $state_name,
          'ac_no'                     => $result->ac_no,
          'ac_name'                   => $ac_name,
          'filter'                    => $individual_filter,
          "st_code"                   => $result->st_code,
          "name"                      => $result->name,
          "Phone_no"                  => $result->Phone_no,
          "est_turnout_round1"    => $result->est_turnout_round1,
          "est_turnout_round2"    => $result->est_turnout_round2,
          "est_turnout_round3"    => $result->est_turnout_round3,
          "est_turnout_round4"    => $result->est_turnout_round4,
          "est_turnout_round5"    => $result->est_turnout_round5,
          "est_turnout_round6"    => $result->est_turnout_total,
          "missed_status_round1"  => $result->missed_status_round1,
          "missed_status_round2"  => $result->missed_status_round2,
          "missed_status_round3"  => $result->missed_status_round3,
          "missed_status_round4"  => $result->missed_status_round4,
          "missed_status_round5"  => $result->missed_status_round5,
          "missed_status_round6"  => $result->missed_status_round6,
          "modification_status_round1"  => $result->modification_status_round1,
          "modification_status_round2"  => $result->modification_status_round2,
          "modification_status_round3"  => $result->modification_status_round3,
          "modification_status_round4"  => $result->modification_status_round4,
          "modification_status_round5"  => $result->modification_status_round5,
          "modification_status_round6"  => $result->modification_status_round6,
          "est_poll_close"  => $result->est_poll_close,
          "href"                        => 'javascript:void(0)'
        ];
      }
    }

    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();
    $data['heading_title_with_all'] = $data['heading_title'];

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }


    return view($this->view_path . '.missed.enable_for_modification', $data);

    try {
    } catch (Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }

  public function enbale_modified_acs(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $state_code = $request->input('st_code');
        $phase_no = $request->input('phase_no');
        $round_no = $request->input('round_no');
        $ac_no = $request->input('ac_no');
        $data_option = $request->input('data_option');
        if ($data_option == 'on') {
          $flagval = 1;
          $message = 'enabled';
        } else {
          $message = 'disabled';
          $flagval = 0;
        }

        if (!empty($phase_no) && !empty($round_no) && !empty($ac_no)) {
          $missed_flag = 'modification_status_round' . $round_no;
          DB::table('pd_scheduledetail')->where('st_code', $state_code)->where('ac_no', $ac_no)->update([$missed_flag => $flagval]);
          if (config("public_config.vt_log")) {
            $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
            $ErrorMessage['applicationType'] = 'WebApp';
            $ErrorMessage['Module'] = 'ENCORE';
            $ErrorMessage['TransectionType'] = 'VoterTurnout';
            $ErrorMessage['TransectionAction'] = 'Estimated Turnout Entry Modification';
            $ErrorMessage['TransectionStatus'] = 'SUCCESS';
            $ErrorMessage['LogDescription'] = 'Estimated Turnout Entry Modification is ' . $message . ' for round ' . $round_no . ' AC NO ' . $ac_no . ' ST CODE ' . $state_code . ' Phase ' . $phase_no;
            LogNotification::LogInfo($ErrorMessage);
          }
          Session::flash('success_mes', 'Option ' . $message . ' successfully.');
          return Redirect::back();
        } else {
          if (config("public_config.vt_log")) {
            $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
            $ErrorMessage['applicationType'] = 'WebApp';
            $ErrorMessage['Module'] = 'ENCORE';
            $ErrorMessage['TransectionType'] = 'VoterTurnout';
            $ErrorMessage['TransectionAction'] = 'Estimated Turnout Entry Modification';
            $ErrorMessage['TransectionStatus'] = 'FAILED';
            $ErrorMessage['LogDescription'] = 'Some data is missing';
            LogNotification::LogInfo($ErrorMessage);
          }
          Session::flash('error_mes', 'Please try again');
          return Redirect::back();
        }
      } else {
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Estimated Turnout Entry Modification';
          $ErrorMessage['TransectionStatus'] = 'FAILED';
          $ErrorMessage['LogDescription'] = 'User is not admin';
          LogNotification::LogInfo($ErrorMessage);
        }
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Estimated Turnout Entry Modification';
        $ErrorMessage['TransectionStatus'] = 'FAILED';
        $ErrorMessage['LogDescription'] = $ex;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }


  //missed ac's export
  public function export_excel_report_ac_missed(Request $request)
  {

    set_time_limit(6000);
    $data = $this->get_missed($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['State', 'AC No', 'AC Name', 'ARO Name', 'ARO Mobile No'];

    $headings[] = [];

    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['ac_no'],
        $lis['ac_name'],
        $lis['name'],
        $lis['Phone_no'],

      ];
    }

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:G1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_ac_missed(Request $request)
  {
    $data = $this->get_missed($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.missed.report_missed_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }


  //waseem missed turnout report

  public function get_missed_ac(Request $request)
  {


    $data = [];
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];


    $data['election_type'] = NULL;
    if ($request->has('election_type')) {
      $data['election_type'] = $request->election_type;
      $request_array[] =  'election_type=' . $request->election_type;
    }


    $filter_for_phases = [
      'election_type' => $data['election_type']
    ];

    $data['phases'] = PhaseModel::get_phases($filter_for_phases);
    //$data['phases'] = PhaseModel::get_phases();

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

    $data['state'] = NULL;
    if ($request->has('state')) {

      //valid a state is exist in the current filter phase
      $is_state_valid = StateModel::get_pc_states_with_filter([
        'state' => base64_decode($request->state),
        'election_type' => $data['election_type'],
        'phase' => $data['phase']
      ]);

      if (count($is_state_valid) > 0) {
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state=' . $request->state;
      }
    }

    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }

    $data['ac_no'] = NULL;
    if ($request->has('ac_no')) {
      $data['ac_no']    = $request->ac_no;
      $request_array[]  = 'ac_no=' . $request->ac_no;
    }

    //filled
    $data['filters_by'] = [
      [
        'id' => 0,
        'name' => 'All'
      ],
      [
        'id' => 1,
        'name' => 'Filled'
      ],
      [
        'id' => 2,
        'name' => 'Not Filled'
      ],
    ];
    $data['filter_by'] = NULL;
    if ($request->has('filter_by')) {
      $data['filter_by']  = $request->filter_by;
      $request_array[]    = 'filter_by=' . $request->filter_by;
    }

    //set title
    $title_array  = [];
    $data['heading_title'] = "Ac's Not filled report";

    // if($data['phase']){
    //   $title_array[] = "Phase: ".$data['phase'];
    // }

    if ($data['state']) {
      $state_object = StateModel::get_state_by_code($data['state']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }

    if ($data['ac_no'] && $data['state']) {
      $ac_object = AcModel::get_record([
        'state' => $data['state'],
        'ac_no' => $data['ac_no']
      ]);
      if ($ac_object) {
        $title_array[] = "Consituency: " . $ac_object['ac_name'];
      }
    }

    $data['filter_buttons'] = $title_array;

    $filter_for_state = [
      'election_type' => $data['election_type'],
      'phase' => $data['phase']
    ];

    $states = StateModel::get_pc_states_with_filter($filter_for_state);

    $data['states'] = [];
    foreach ($states as $result) {
      $data['states'][] = [
        'code' => base64_encode($result->ST_CODE),
        'name' => $result->ST_NAME,
      ];
    }

    $data['filter']   = implode('&', array_merge($request_array));
    //end set title

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]  = [
      'name' => 'Export Excel',
      'href' =>  url($this->action_missed_ac . '/excel') . '?' . implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Export Pdf',
      'href' =>  url($this->action_missed_ac . '/pdf') . '?' . implode('&', $request_array),
      'target' => true
    ];

    $data['action']         = url($this->action_missed_ac);

    $data['consituencies']  = AcModel::get_records([
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase']
    ]);

    $results                = [];

    $filter_election = [
      'state'         => $data['state'],
      'election_type' => $data['election_type'],
      'phase'         => $data['phase'],
      'ac_no'         => $data['ac_no'],
      'group_by'      => 'ac_no',
      'order_by'      => 'ac_no',
      'filter_by'     => $data['filter_by']
    ];

    $object       = MissedTurnoutModel::get_missed_reports($filter_election);
    $time         = "23:59";
    // $time         = date("H:i");
    if ($data['phase']) {
      $phase_detail   = PhaseModel::get_phase($data['phase']);
      if (isset($phase_detail) && $phase_detail->DATE_POLL == date('Y-m-d')) {
        $time         = date("H:i");
      }
    }



    foreach ($object as $key => $result) {

      $ac_name    = '';
      $get_ac     = AcModel::get_record([
        'state' => $result->st_code,
        'ac_no' => $result->ac_no
      ]);
      if ($get_ac) {
        $ac_name = $get_ac['ac_name'];
      }

      $individual_filter    = implode('&', array_merge($request_array, [
        'ac_no' => 'ac_no=' . $result->ac_no,
      ]));

      $missed_1 = 'Not Open';
      $missed_2 = 'Not Open';
      $missed_3 = 'Not Open';
      $missed_4 = 'Not Open';
      $missed_5 = 'Not Open';
      $mis_1 = false;
      $mis_2 = false;
      $mis_3 = false;
      $mis_4 = false;
      $mis_5 = false;

      if ($result->est_total_round1 == 0) {
        $missed_1 = 'Not Filled';
        $mis_1 = true;
      }

      if ($result->est_total_round2 == 0) {
        $missed_2 = 'Not Filled';
        $mis_2 = true;
      }

      if ($result->est_total_round3 == 0) {
        $missed_3 = 'Not Filled';
        $mis_3 = true;
      }
      if ($result->est_total_round4 == 0) {
        $missed_4 = 'Not Filled';
        $mis_4    = true;
      }

      if ($result->est_total_round5 == 0) {
        $missed_5 = 'Not Filled';
        $mis_5    = true;
      }

      if ($time <= '17:00' && $mis_5) {
        $missed_5 = 'Not Filled';
      }
      if ($time <= '15:00' && $mis_4) {
        $missed_4 = 'Not Filled';
      }
      if ($time <= '13:00' && $mis_3) {
        $missed_3 = 'Not Filled';
      }
      if ($time <= '11:00' && $mis_3) {
        $missed_2 = 'Not Filled';
      }
      if ($time <= '9:00' && $mis_3) {
        $missed_1 = 'Not Filled';
      }


      $results[] = [
        'label'                 => $result->st_name,
        'ac_no'                 => $result->ac_no,
        'ac_name'               => $ac_name,
        'filter'                => $individual_filter,
        "est_total_round1"      => ($result->est_total_round1) ? $result->est_total_round1 : $missed_1,
        "est_total_round2"      => ($result->est_total_round2) ? $result->est_total_round2 : $missed_2,
        "est_total_round3"      => ($result->est_total_round3) ? $result->est_total_round3 : $missed_3,
        "est_total_round4"      => ($result->est_total_round4) ? $result->est_total_round4 : $missed_4,
        "est_total_round5"      => ($result->est_total_round5) ? $result->est_total_round5 : $missed_5,
        "close_of_poll"         => $result->close_of_poll,
        "est_total"             => $result->est_total,
        "total_record"          => $result->total_record,
        "total_percentage"      => $result->total_percentage,
        "st_code"               => $result->st_code,
        "href"                  => 'javascript:void(0)',
      ];
    }
    $data['results'] = $results;

    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();

    //if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
    //   return $data;
    // }

    $data['heading_title_with_all'] = $data['heading_title'];

    if ($request->has('is_data')) {
      return $data;
    }

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }


    return view($this->view_path . '.missed.report_ac_missed', $data);
  }

  //missed ac's export
  public function export_excel_report_missed(Request $request)
  {

    set_time_limit(6000);
    $data = $this->get_missed_ac($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['State', 'AC No', 'AC Name', 'Round1 %(Poll Start to 9:00 AM)', 'Round2 %(Poll Start to 11:00 AM)', 'Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)', 'Round5 %(Poll Start to 5:00 PM)', 'Latest Updated %'];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['ac_no'],
        $lis['ac_name'],
        ($lis['est_total_round1']) ? $lis['est_total_round1'] : '0',
        ($lis['est_total_round2']) ? $lis['est_total_round2'] : '0',
        ($lis['est_total_round3']) ? $lis['est_total_round3'] : '0',
        ($lis['est_total_round4']) ? $lis['est_total_round4'] : '0',
        ($lis['est_total_round5']) ? $lis['est_total_round5'] : '0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
      ];
    }

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    \Excel::create($name_excel . '_' . date('d-m-Y') . '_' . time(), function ($excel) use ($export_data) {
      $excel->sheet('Sheet1', function ($sheet) use ($export_data) {
        $sheet->mergeCells('A1:L1');
        $sheet->cell('A1', function ($cell) {
          $cell->setAlignment('center');
          $cell->setFontWeight('bold');
        });
        $sheet->fromArray($export_data, null, 'A1', false, false);
      });
    })->export('xls');
  }

  public function export_pdf_report_missed(Request $request)
  {
    $data = $this->get_missed_ac($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.missed.report_ac_missed_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }
}  // end class