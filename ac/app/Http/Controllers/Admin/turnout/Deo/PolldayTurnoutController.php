<?php

namespace App\Http\Controllers\Admin\turnout\Deo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use \PDF;
use App\commonModel;
use App\models\Admin\turnout\Deo\PollDayModel;
use App\models\Admin\turnout\Deo\ElectorModel;
use App\models\Admin\polling_station\PollingStationModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;
use App\models\Admin\DistrictModel;
use App\Classes\xssClean;
use App\models\Admin\turnout\TurnoutModel;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class PolldayTurnoutController extends Controller
{

  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/turnout/estimate-poll-percent';
  public $action_ac     = 'eci/turnout/estimate-poll-percent/state/ac';
  public $view_path     = "admin.turnout";
  public $deo_pswise_action        = 'acdeo/turnout/DeoPsWiseDetails';

  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->PollingStationModel = new PollingStationModel();
    $this->turnout = new TurnoutModel;
    $this->xssClean = new xssClean;
    $this->middleware(function ($request, $next) {
      if (Auth::user() && Auth::user()->role_id == '26') {
        $this->action_state  = str_replace('eci', 'eci-agent', $this->action_state);
        $this->action_ac     = str_replace('eci', 'eci-agent', $this->action_ac);
      }
      return $next($request);
    });
  }

  public function report_state(Request $request)
  {
    if (Auth::user()->role_id == '4') {
      $this->action_state  = 'acceo/turnout/estimate-poll-percent';
      $this->action_ac     = 'acceo/turnout/estimate-poll-percent/state/ac';
    }

    $data = [];
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];
    $data['phases'] = PhaseModel::get_phases();
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

    //$data['phase']    = $default_phase;

    $data['state'] = NULL;
    if ($request->has('state')) {
      $data['state'] = base64_decode($request->state);
      $request_array[] = 'state=' . $request->state;
    }

    if ($data['state'] == '') {
      if (Auth::user()->role_id == '4') {
        $data['state'] = Auth::user()->st_code;
      }
    }

    //set title
    $title_array  = [];
    $data['heading_title'] = 'Estimate Poll Percent';
    if (isset($from_date) && isset($from_to)) {
      $data['heading_title'] .= ' between ' . date('d-M-Y', strtotime($from_date)) . ' to ' . date('d-M-Y', strtotime($from_to));
    }
    // if($data['phase']){
    //   $title_array[] = "Phase: ".$data['phase'];
    // }
    if ($data['state']) {
      $state_object = StateModel::get_state_by_code($data['state']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }
    $data['filter_buttons'] = $title_array;

    $filter_for_state = [
      'phase' => $data['phase']
    ];

    $states = StateModel::get_pc_states_with_filter($filter_for_state);

    $data['states'] = [];
    //STATE LIST STARTS
    foreach ($states as $result) {

      //FOR CEO 
      if (Auth::user()->role_id == '4') {
        $st_object = StateModel::get_state_by_code(Auth::user()->st_code);
        $data['states'][] = [
          'code' => Auth::user()->st_code,
          'name' => $st_object['ST_NAME'],
        ];
      } else {
        //FOR ECI
        $data['states'][] = [
          'code' => base64_encode($result->ST_CODE),
          'name' => $result->ST_NAME,
        ];
      }
    }
    //STATE LIST ENDS

    $data['filter']   = implode('&', array_merge($request_array));
    //end set title

    //buttons
    $data['buttons']    = [];
    if (Auth::user()->role_id == '7') {
      $data['buttons'][]  = [
        'name' => "All States Report",
        'href' =>  url($this->action_state),
        'target' => false
      ];
    }
    $data['buttons'][]  = [
      'name' => 'Export Excel',
      'href' =>  url($this->action_state . '/excel') . '?' . implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Export Pdf',
      'href' =>  url($this->action_state . '/pdf') . '?' . implode('&', $request_array),
      'target' => true
    ];

    $data['action']         = url($this->action_state);

    $results                = [];

    $filter_election = [
      'state'         => $data['state'],
      'phase'         => $data['phase'],
    ];

    //CEO RECORD
    if (Auth::user()->role_id == '4') {

      $object    = PollDayModel::get_reports([
        'phase'     => $data['phase'],
        'state'     => Auth::user()->st_code,
        'group_by'  => 'state',
        'order_by'  => 'state'
      ]);
    } else {
      //ECI RECORDS
      $object    = PollDayModel::get_reports([
        'phase'     => $data['phase'],
        'group_by'  => 'state',
        'order_by'  => 'state'
      ]);
    }


    foreach ($object as $result) {

      $filter_data = [
        'state'         => $result->st_code,
        'phase'         => $data['phase']
      ];

      $individual_filter_array = [];
      if ($data['phase']) {
        $individual_filter_array['phase'] = 'phase=' . $data['phase'];
      }
      $individual_filter_array['state'] = 'state=' . base64_encode($result->st_code);
      $individual_filter    = implode('&', $individual_filter_array);

      $old_percentage = ElectorModel::get_sum([
        'state'         => $result->st_code,
        'phase'         => $data['phase'],
        'group_by'      => 'state',
        'year'          => 2014
      ]);

      $results[] = [
        'label'                 => $result->st_name,
        'filter'                => $individual_filter,
        "est_total_round1"      => $result->est_total_round1,
        "est_total_round2"      => $result->est_total_round2,
        "est_total_round3"      => $result->est_total_round3,
        "est_total_round4"      => $result->est_total_round4,
        "est_total_round5"      => $result->est_total_round5,
        'close_of_poll'         => $result->close_of_poll,
        "est_total"             => $result->est_total,
        "total_record"          => $result->total_record,
        "old_total_percentage"  => $old_percentage,
        "total_percentage"      => $result->total_percentage,
        "difference"            => ROUND($result->total_percentage - $old_percentage, 2),
        "const_no"              => $result->const_no,
        "const"                 => $result->const,
        "st_code"               => $result->st_code,
        "href"                  => url($this->action_ac) . "?" . $individual_filter
      ];
    }

    $total_filter = [
      'state'         => $data['state'],
      'phase'         => $data['phase']
    ];
    $data['number_of_voting'] =  PollDayModel::get_average_sum($total_filter);

    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();

    $data['heading_title_with_all'] = $data['heading_title'];


    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path . '.estimated.report_state', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }


  public function report_ac(Request $request)
  {
    try {
      $user = Auth::user();
      $request->merge(['st_code' => base64_encode($user->st_code)]);
      if (Auth::user()->role_id == '5') {
        $this->action_state  = 'acdeo/turnout/estimate-poll-percent';
        $this->action_ac     = 'acdeo/turnout/estimate-poll-percent/state/ac';
      }

      $data = [];
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
      $ele_details = $ele_details[0];

      $default_phase = $ele_details->PHASE_NO;
      $default_phase_no = PhaseModel::get_current_phase();

      $request_array = [];
      $data['phases'] = NULL;

      $data['phase'] = NULL;
      if ($request->has('phase')) {
        if ($request->phase != 'all') {
          $data['phase'] = $request->phase;
        }
        $request_array[] =  'phase=' . $request->phase;
      } else {
        $data['phase']    = $default_phase_no;
        $request_array[]  =  'phase=' . $default_phase_no;
      }

      $data['state'] = NULL;
      if ($request->has('state')) {
        //valid a state is exist in the current filter phase
        $is_state_valid = StateModel::get_pc_states_with_filter([
          'state' => base64_decode($request->state),
          'phase' => $data['phase']
        ]);

        if (count($is_state_valid) > 0) {
          $data['state'] = base64_decode($request->state);
          $request_array[] = 'state=' . $request->state;
        }
      }

      if (Auth::user()->designation == 'DEO') {
        $data['state'] = Auth::user()->st_code;
        $data['dist_no'] = Auth::user()->dist_no;
      }

      $title_array  = [];
      $data['heading_title'] = 'Estimate Poll Percentage';


      if ($data['state']) {
        $state_object = StateModel::get_state_by_code($data['state']);
        $district_object = DistrictModel::get_district(['st_code' => Auth::user()->st_code, 'dist_no' => Auth::user()->dist_no]);
        if ($state_object) {
          $title_array[0]  = "State: " . $state_object['ST_NAME'];
          $title_array[1]  = "District: " . $district_object['dist_name'];
        }
      }


      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        'phase'   => $data['phase'],
        'dist_no' => $data['dist_no']
      ];

      $states = StateModel::get_pc_states_with_filter($filter_for_state);
      $data['states'] = [];
      //STATE LISTR STARTS

      //FOR DEO 
      if (Auth::user()->role_id == '5') {
        $st_object = StateModel::get_state_by_code(Auth::user()->st_code);
        $dt_object = DistrictModel::get_district(['st_code' => Auth::user()->st_code, 'dist_no' => Auth::user()->dist_no]);
        $data['states'][] = [
          'code' => base64_encode($st_object['ST_CODE']),
          'name' => $st_object['ST_NAME'],
        ];
        $data['dist'][] = [
          'code' => base64_encode($dt_object['dist_no']),
          'name' => $dt_object['dist_name'],
        ];
      }
      //STATE LIST ENDS

      $data['phases'] = PhaseModel::get_phases([]);

      $data['filter']   = implode('&', array_merge($request_array));

      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_ac . '/excel') . '?' . implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_ac . '/pdf') . '?' . implode('&', $request_array),
        'target' => true
      ];

      $data['action']         = url($this->action_ac);

      $data['consituencies']  = AcModel::get_records([
        'state'         => Auth::user()->st_code,
        'dist_no'       => Auth::user()->dist_no,
        'phase'         => $data['phase'],
      ]);

      $results                = [];

      $filter_election = [
        'state'         => Auth::user()->st_code,
        'dist_no'       => Auth::user()->dist_no,
        'phase'         => $data['phase'],
        'group_by'      => 'ac_no',
        'order_by'      => 'ac_no'
      ];
      $object         = PollDayModel::get_reports($filter_election);
      foreach ($object as $result) {

        $individual_filter    = implode('&', array_merge($request_array, [
          'ac_no' => 'ac_no=' . $result->const_no,
        ]));

        $filter = [
          'st_code'       => $result->st_code,
          'ac_no'         => $result->ac_no,
          'election_id'   => '',
          'const_type'    => 'AC',
          'phase_no'      => $result->phase_no,
          'pc_no'         => '',
        ];

        $ac_name    = '';
        $get_ac     = AcModel::get_record([
          'state'         => Auth::user()->st_code,
          'dist_no'       => Auth::user()->dist_no,
          'ac_no'   => $result->ac_no
        ]);

        if ($get_ac) {
          $ac_name = $get_ac['ac_name'];
        }

        $state_name = '';
        $state_object = StateModel::get_state_by_code($result->st_code);
        if ($state_object) {
          $state_name = $state_object['ST_NAME'];
        }

        $is_boothapp_present = $this->turnout->check_turnout_exempted($filter);
        if ($is_boothapp_present == 0) {
          $is_boothapp_present = $this->turnout->check_turnout_entry_enable($filter);
        }

        $results[] = [
          "id"                    => $result->id,
          'label'                 => $state_name,
          'const_no'              => $result->const_no,
          'const'                 => $ac_name,
          'filter'                => $individual_filter,
          "est_total_round1"      => $result->est_total_round1,
          "est_total_round2"      => $result->est_total_round2,
          "est_total_round3"      => $result->est_total_round3,
          "est_total_round4"      => $result->est_total_round4,
          "est_total_round5"      => $result->est_total_round5,
          'close_of_poll'         => $result->close_of_poll,
          "est_total"             => $result->est_total,
          "total_record"          => $result->total_record,
          "total_percentage"      => $result->total_percentage,
          "st_code"               => $result->st_code,
          "href"                  => 'javascript:void(0)',
          'phase_no'              =>  !empty($result->phase_no) ? $result->phase_no : '',
          'is_boothapp_present'   => $is_boothapp_present
        ];
      }

      if ($data['dist_no']) {
        $group_by = 'dist_no';
      } else {
        $group_by = NULL;
      }

      $total_filter = [
        'state'         => $data['state'],
        'dist_no'       => $data['dist_no'],
        'phase'         => $data['phase'],
        'group_by'      => $group_by
      ];

      $data['number_of_voting'] =  PollDayModel::get_average_sum($total_filter);

      $data['results']    =   $results;

      $data['default_phase_no'] = $default_phase_no;

      $data['user_data']  =   Auth::user();

      $data['heading_title_with_all'] = $data['heading_title'];

      if ($request->has('is_excel')) {
        if (isset($title_array) && count($title_array) > 0) {
          $data['heading_title'] .= "- " . implode(', ', $title_array);
        }
        return $data;
      }
      // dd($data);
      return view($this->view_path . '.estimated.deo.report_ac', $data);
    } catch (\Exception $e) {
      return Redirect::to('/internalserver');
    }
  }

  public function export_excel_report_state(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_state($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    // $export_data[] = ['State', 'Round1 %(Poll Start to 9:00 AM)','Round2 %(Poll Start to 11:00 AM)','Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)','Round5 %(Poll Start to 5:00 PM)','Latest Updated %'];
    //$export_data[] = ['State', 'Turnout % (2014)', 'Latest Updated Poll %(2019)','Change from 2014'];
    $export_data[] = ['State', 'Latest Updated Poll %'];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        // ($lis['est_total_round1'])?$lis['est_total_round1']:'0',
        // ($lis['est_total_round2'])?$lis['est_total_round2']:'0',
        // ($lis['est_total_round3'])?$lis['est_total_round3']:'0',
        // ($lis['est_total_round4'])?$lis['est_total_round4']:'0',
        // ($lis['est_total_round5'])?$lis['est_total_round5']:'0',
        //($lis['old_total_percentage'])?$lis['old_total_percentage']:'0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
        //($lis['difference'])?$lis['difference']:'0',
      ];
    }

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    \Excel::create($name_excel . '_' . date('d-m-Y') . '_' . time(), function ($excel) use ($export_data) {
      $excel->sheet('Sheet1', function ($sheet) use ($export_data) {
        $sheet->mergeCells('A1:B1');
        $sheet->cell('A1', function ($cell) {
          $cell->setAlignment('center');
          $cell->setFontWeight('bold');
        });
        $sheet->fromArray($export_data, null, 'A1', false, false);
      });
    })->export('xls');
  }

  public function export_pdf_report_state(Request $request)
  {
    $data = $this->report_state($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.estimated.report_state_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }



  //export AC's
  public function export_excel_report_ac(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_ac($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $headings[] = [];
    //$export_data[] = ['State', 'PC No' ,'PC Name','AC No' ,'AC Name','Turnout % (2014)', 'Round1 %(Poll Start to 9:00 AM)','Round2 %(Poll Start to 11:00 AM)','Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)','Round5 %(Poll Start to 5:00 PM)', 'Latest Updated Poll %(2019)','Change from 2014'];

    $export_data[] = ['State', 'AC No', 'AC Name', 'Round1 %(Poll Start to 9:00 AM)', 'Round2 %(Poll Start to 11:00 AM)', 'Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)', 'Round5 %(Poll Start to 5:00 PM)', 'Close Of Poll %', 'Latest Updated Poll %'];

    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['const_no'],
        $lis['const'],
        //($lis['old_total_percentage'])?$lis['old_total_percentage']:'0',
        ($lis['est_total_round1']) ? $lis['est_total_round1'] : '0',
        ($lis['est_total_round2']) ? $lis['est_total_round2'] : '0',
        ($lis['est_total_round3']) ? $lis['est_total_round3'] : '0',
        ($lis['est_total_round4']) ? $lis['est_total_round4'] : '0',
        ($lis['est_total_round5']) ? $lis['est_total_round5'] : '0',
        ($lis['close_of_poll']) ? $lis['close_of_poll'] : '0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
        //($lis['difference'])?$lis['difference']:'0',
      ];
    }

    // $export_data[] = [
    //   $data['totals']['label'],
    //   '',
    //   '',
    //   '',
    //   '',
    //   ($data['totals']['est_total_round1'])?$data['totals']['est_total_round1']:'0',
    //   ($data['totals']['est_total_round2'])?$data['totals']['est_total_round2']:'0',
    //   ($data['totals']['est_total_round3'])?$data['totals']['est_total_round3']:'0',
    //   ($data['totals']['est_total_round4'])?$data['totals']['est_total_round4']:'0',
    //   ($data['totals']['est_total_round5'])?$data['totals']['est_total_round5']:'0',
    //   ($data['totals']['total_percentage'])?$data['totals']['total_percentage']:'0',
    // ];

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');

    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:I1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_ac(Request $request)
  {
    $data = $this->report_ac($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.estimated.report_ac_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }


  public function estimated_turnout_change(Request $request)
  {

    $user = Auth::user();
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
    $ele_details = $ele_details[0];

    $validator = \Validator::make(
      $request->all(),
      [
        'est_turnout' => 'required|numeric|between:0,99.99',
        'rounds' => 'required',
      ],
      [
        'est_turnout.numeric' => 'Please enter numeric value',
        'est_turnout.required' => 'Please enter voter',
        'est_turnout.between' => 'Please enter valid value (99.99)',
        'rounds' => 'Please select rounds',
      ]
    );

    if ($validator->fails()) {
      return \Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $id =  $request->input('id');
    $acno =  $request->input('acno');
    $distno =  $request->input('distno');
    $est_turnout = $this->xssClean->clean_input($request->input('est_turnout'));
    $rounds = $this->xssClean->clean_input($request->input('rounds'));


    if ($rounds == 1)
      $st = array(
        'updated_at' => date("Y-m-d H:i:s"),
        'added_update_at' => date("Y-m-d"),
        'updated_by' => $d->officername,
        'est_turnout_round1' => $est_turnout,
        'update_device_round1' => 'web',
        'update_at_round1' => date("Y-m-d H:i:s"),
        'est_turnout_total' => $est_turnout
      );
    elseif ($rounds == 2)
      $st = array(
        'updated_at' => date("Y-m-d H:i:s"),
        'added_update_at' => date("Y-m-d"),
        'updated_by' => $d->officername,
        'est_turnout_round2' => $est_turnout,
        'update_device_round2' => 'web',
        'update_at_round2' => date("Y-m-d H:i:s"),
        'est_turnout_total' => $est_turnout
      );
    elseif ($rounds == 3)
      $st = array(
        'updated_at' => date("Y-m-d H:i:s"),
        'added_update_at' => date("Y-m-d"),
        'updated_by' => $d->officername,
        'est_turnout_round3' => $est_turnout,
        'update_device_round3' => 'web',
        'update_at_round3' => date("Y-m-d H:i:s"),
        'est_turnout_total' => $est_turnout
      );
    elseif ($rounds == 4)
      $st = array(
        'updated_at' => date("Y-m-d H:i:s"),
        'added_update_at' => date("Y-m-d"),
        'updated_by' => $d->officername,
        'est_turnout_round4' => $est_turnout,
        'update_device_round4' => 'web',
        'update_at_round4' => date("Y-m-d H:i:s"),
        'est_turnout_total' => $est_turnout
      );
    elseif ($rounds == 5)
      $st = array(
        'updated_at' => date("Y-m-d H:i:s"),
        'added_update_at' => date("Y-m-d"),
        'updated_by' => $d->officername,
        'est_turnout_round5' => $est_turnout,
        'update_device_round5' => 'web',
        'update_at_round5' => date("Y-m-d H:i:s"),
        'est_turnout_total' => $est_turnout
      );
    elseif ($rounds == 6)
      $st = array(
        'updated_at' => date("Y-m-d H:i:s"),
        'added_update_at' => date("Y-m-d"),
        'updated_by' => $d->officername,
        'close_of_poll' => $est_turnout,
        'updated_device_close_of_poll' => 'web',
        'updated_at_close_of_poll' => date("Y-m-d H:i:s"),
        'est_turnout_total' => $est_turnout,
        'est_poll_close' => 1
      );
    $i = DB::table('pd_scheduledetail')->where('id', $id)
      ->where('ac_no', $acno)->update($st);

    $lists = DB::table('pd_scheduledetail')
      ->where('st_code', $ele_details->ST_CODE)
      ->where('ac_no', $acno)->where('id', $id)->first();
    if (!isset($lists)) {
      $lists = DB::table('pd_scheduledetail')
        ->where('st_code', $ele_details->ST_CODE)
        ->where('ac_no', $acno)->first();
    }

    $ele = getcdacelector($ele_details->ST_CODE, $acno, $ele_details->ELECTION_ID);

    $electors_total = 0;
    $est_voter = 0;
    if (isset($ele)) {
      $electors_total = $ele->electors_total;
    }


    if (isset($lists) and $lists->est_turnout_round1 != 0)
      $latest_updated = $lists->est_turnout_round1;
    if (isset($lists) and $lists->est_turnout_round2 != 0)
      $latest_updated = $lists->est_turnout_round2;
    if (isset($lists) and $lists->est_turnout_round3 != 0)
      $latest_updated = $lists->est_turnout_round3;
    if (isset($lists) and $lists->est_turnout_round4 != 0)
      $latest_updated = $lists->est_turnout_round4;
    if (isset($lists) and $lists->est_turnout_round5 != 0)
      $latest_updated = $lists->est_turnout_round5;
    if (isset($lists) and $lists->close_of_poll != 0)
      $latest_updated = $lists->close_of_poll;

    $est_voter = round(($electors_total * $latest_updated / 100), 0);
    $st1 = array(
      'updated_at' => date("Y-m-d H:i:s"),
      'added_update_at' => date("Y-m-d"),
      'updated_by' => $d->officername,
      'est_turnout_total' => $latest_updated,
      'electors_total' => $electors_total,
      'est_voters' => $est_voter
    );

    $i = DB::table('pd_scheduledetail')->where('id', $id)->update($st1);
    \Session::flash('success_mes', 'Voter Turnout successfully added');
    return Redirect::to('acdeo/turnout/estimate-poll-percent/state/ac');
  }

  public function DeoPsWiseDetails(Request $request)
  {

    // echo "<pre>";print_r($request->all());die;
    $data = [];

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


    //end set title
    $data['user_data']  =   Auth::user();

    if (Auth::user()->designation == 'DEO') {
      $data['state'] = Auth::user()->st_code;
      $data['dist_no'] = Auth::user()->dist_no;
    }


    $xss = new xssClean;
    //CHECKING REQUEST VARIABES STARTS
    if ($request->has('ac_id')) {


      $validator = Validator::make($request->all(), [
        'ac_id'          => 'required|numeric',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $ac_id    = $xss->clean_input($request->ac_id);


      $filter_election = [
        'state'         => $data['state'],
        'ac_no'         => $ac_id,
        'dist_no'       => $data['dist_no'],
      ];


      $request_array[] =  'state=' . $data['state'];
      $request_array[] =  'ac_id=' . $ac_id;

      $statename = getstatebystatecode($data['state']);
      $acame = getacbyacno($data['state'], $ac_id);


      $title_array[] = "State: " . $statename->ST_NAME;
      $title_array[] = "AC: " . $acame->AC_NAME;

      $data['consituencies']  = AcModel::get_records([
        'state'         => $data['state'],
        'dist_no'         => $data['dist_no'],
      ]);


      $data['filter_buttons'] = $title_array;

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->deo_pswise_action . '/excel') . '?' . implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->deo_pswise_action . '/pdf') . '?' . implode('&', $request_array),
        'target' => true
      ];


      $data['action']         = url($this->deo_pswise_action);

      $results                = [];

      $object         = PollingStationModel::get_ps_data($filter_election);
      $data['is_finalize']  = PollingStationModel::get_ps_finalize_data($filter_election);
      $data['is_definalize']  = PollingStationModel::get_ps_finalize_ceo_data($filter_election);

      $data['results']    =   $object;
    } else {

      $data['buttons']    = [];
      $data['action']         = url($this->deo_pswise_action);
      $data['results'] = [];

      $data['is_finalize']  = NULL;
      $data['is_definalize'] = NULL;

      $data['consituencies']  = AcModel::get_records([
        'state'         => $data['state'],
        'dist_no'         => $data['dist_no'],
      ]);
    }


    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }

      return $data;
    }

    //  echo "<pre>";print_r($data);die;
    return view($this->view_path . '.polling_station.DeoPsWiseDetails', $data);
  }



  public function DeoPsWiseDetailsExcel(Request $request)
  {

    set_time_limit(6000);
    $data = $this->DeoPsWiseDetails($request->merge(['is_excel' => 1]));

    $export_data = [];
    $headings[] = [$data['heading_title']];
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

    $totalvalues = [];
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
  }



  public function DeoPsWiseDetailsPdf(Request $request)
  {
    $data = $this->DeoPsWiseDetails($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.polling_station.DeoPsWiseDetailsPdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }


  public function DeoPsWiseDetailsUpdate(Request $request)
  {

    $users = Session::get('admin_login_details');
    $user = Auth::user();
    if (session()->has('admin_login')) {
      $uid = $user->id;

      $user_data = $this->commonModel->getunewserbyuserid($uid);

      $cur_time    = Carbon::now();

      //dd($ElectorsDetails);

      $cur_time  = Carbon::now();
      $st_code = $user_data->st_code;
      $st_name = $user_data->placename;
      //dd($AllPartyList);


      $validator = Validator::make($request->all(), [
        'electors_male'     => 'required|numeric|min:0|integer|between:0,9999',
        'electors_female'   => 'required|numeric|min:0|integer|between:0,9999',
        'electors_other'    => 'required|numeric|min:0|integer|between:0,9999',
        'electors_total'    => 'required|numeric|min:0|integer|between:0,9999',
        'voter_male'        => 'required|numeric|min:0|integer|between:0,9999',
        'voter_female'      => 'required|numeric|min:0|integer|between:0,9999',
        'voter_other'       => 'required|numeric|min:0|integer|between:0,9999',
        'voter_total'       => 'required|numeric|min:0|integer|between:0,9999',
        'ac_no'             => 'required|numeric',

      ]);


      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }


      $xss = new xssClean;

      $request              = $request->all();
      $electors_male        = $xss->clean_input($request['electors_male']);
      $electors_female      = $xss->clean_input($request['electors_female']);
      $electors_other       = $xss->clean_input($request['electors_other']);
      $electors_total       = $xss->clean_input($request['electors_total']);
      $voter_male           = $xss->clean_input($request['voter_male']);
      $voter_female         = $xss->clean_input($request['voter_female']);
      $voter_other          = $xss->clean_input($request['voter_other']);
      $voter_total          = $xss->clean_input($request['voter_total']);
      $psno                 = $xss->clean_input($request['psnoinput']);
      $ccode                = $xss->clean_input($request['psccode']);
      $ac_no                = $xss->clean_input($request['ac_no']);

      //ELECTORS DATA MATCHING STARTS
      /*if($voter_male > $electors_male){
         
           return Redirect::back()->with('error', 'Male Voter Data Should Be Equal or Less than Electors Male Data.');

          }

          if($voter_female > $electors_female){
         
            return Redirect::back()->with('error', 'Female Voter Data Should Be Equal or Less than Electors Female Data.');

          }

          if($voter_other > $electors_other){
         
           return Redirect::back()->with('error', 'Other Voter Data Should Be Equal or Less than Electors Other Data.');

          }

          if($voter_total > $electors_total){
         
           return Redirect::back()->with('error', 'Total Voter Data Should Be Equal or Less than Electors Total Data.');

          }*/

      if ($electors_male + $electors_female + $electors_other != $electors_total) {


        return Redirect::back()->with('error', 'Data Mismatch in Electors Data.');
      }
      //ELECTORS DATA MATCHING ENDS

      //VOTERS DATA MATCHING STARTS
      if ($voter_male + $voter_female + $voter_other != $voter_total) {

        return Redirect::back()->with('error', 'Data Mismatch in Voters Data.');
      }
      //VOTERS DATA MATCHING ENDS


      $update_fields = array(
        'electors_male'      => $electors_male,
        'electors_female'    => $electors_female,
        'electors_other'     => $electors_other,
        'electors_total'     => $electors_total,
        'voter_male'         => $voter_male,
        'voter_female'       => $voter_female,
        'voter_other'        => $voter_other,
        'voter_total'        => $voter_total,

      );

      $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no, 'PS_NO' => $psno, 'CCODE' => $ccode];

      $Data = DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);

      return Redirect::back()->with('error', 'Polling Station Data Updated Successfully !');
    } else {
      return redirect('/admin-login');
    }
  }
}  // end class