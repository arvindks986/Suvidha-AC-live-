<?php

namespace App\Http\Controllers\Admin\turnout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\commonModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\ElectorModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;
use App\Classes\xssClean;

use App\Exports\ExcelExport;
use App\models\AC;
use App\models\Admin\ElectionModel;
use App\models\Admin\EndOfPollModel;
use App\models\EstimatedEntryLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PolldayTurnoutController extends Controller
{

  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/turnout/estimate-poll-percent';
  public $action_ac     = 'eci/turnout/estimate-poll-percent/state/ac';
  public $action_district     = 'eci/turnout/estimate-poll-percent/state/district';
  public $view_path     = "admin.turnout";
  public $commonModel     = null;
  public $voting_model     = null;
  public $xssClean     = null;

  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->xssClean = new xssClean;
    $this->middleware(function ($request, $next) {

      return $next($request);
    });
  }

  public function report_state(Request $request)
  {
    //dd(Auth::user());

    //CHECKING FOR USER TYPE AND SETTING VARIABLES FOR IT STARTS
    if (Auth::user()->role_id == '4') {

      $this->action_state  = 'acceo/turnout/estimate-poll-percent';
      $this->action_ac     = 'acceo/turnout/estimate-poll-percent/state/ac';
    }

    $data = [];
    $default_phase = PhaseModel::get_current_phase();

    //dd($default_phase);

    $request_array = [];

    $data['election_type'] = NULL;
    if ($request->has('election_type')) {
      $data['election_type'] = $this->xssClean->clean_input($request->election_type);
      $request_array[] =  'election_type=' . $this->xssClean->clean_input($request->election_type);
    }

    $filter_for_phases = [
      'election_type' => $data['election_type']
    ];

    $data['phases'] = PhaseModel::get_phases($filter_for_phases);

    $data['phase'] = NULL;
    if ($request->has('phase')) {
      if ($request->phase != 'all') {
        $data['phase'] = $this->xssClean->clean_input($request->phase);
      }
      $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);
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
      'phase' => $data['phase'],
      'election_type' => $data['election_type']
    ];

    $states = StateModel::get_pc_states_with_filter($filter_for_state);

    //dd($states);

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
        'election_type'     => $data['election_type'],
        'phase'     => $data['phase'],
        'state'     => Auth::user()->st_code,
        'group_by'  => 'state',
        'order_by'  => 'state'
      ]);
    } else {
      //ECI RECORDS
      $object    = PollDayModel::get_reports([
        'election_type'     => $data['election_type'],
        'phase'     => $data['phase'],
        'group_by'  => 'state',
        'order_by'  => 'state'
      ]);
    }

    foreach ($object as $result) {

      $filter_data = [
        'election_type'     => $data['election_type'],
        'state'         => $result->st_code,
        'phase'         => $data['phase']
      ];

      $individual_filter_array = [];
      if ($data['election_type']) {
        $individual_filter_array['election_type'] = 'election_type=' . $data['election_type'];
      }

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
      'election_type' => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase']
    ];
    $data['number_of_voting'] =  PollDayModel::get_average_sum($total_filter);

    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();

    $data['heading_title_with_all'] = $data['heading_title'];
    // if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
    //   return $data;
    // }

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path . '.estimated.report_state', $data);

    try {
    } catch (Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }


  public function report_ac(Request $request)
  {

    try {
      if (Auth::user()->role_id == '4') {
        $this->action_state  = 'acceo/turnout/estimate-poll-percent';
        $this->action_ac     = 'acceo/turnout/estimate-poll-percent/state/ac';
        $this->action_district = 'acceo/turnout/estimate-poll-percent/state/district';
      }

      $data = [];
      $data['election_type'] = NULL;
      $request_array = [];
      $default_phase = PhaseModel::get_current_phase();
      if ($request->has('election_type')) {
        $data['election_type'] = $this->xssClean->clean_input($request->election_type);
        $request_array[] =  'election_type=' . $this->xssClean->clean_input($request->election_type);
      }


      $filter_for_state = [
        'election_type' => $data['election_type']
      ];

      $states = StateModel::get_pc_states_with_filter($filter_for_state);

      $data['states'] = [];
      //STATE LISTR STARTS

      //FOR CEO 
      if (Auth::user()->role_id == '4') {
        $st_object = StateModel::get_state_by_code(Auth::user()->st_code);

        $data['states'][] = [
          'code' => base64_encode($st_object['ST_CODE']),
          'name' => $st_object['ST_NAME'],
        ];
      } else {
        //FOR ECI
        foreach ($states as $result) {
          $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
          ];
        }
      }
      //STATE LIST ENDS

      $data['state'] = NULL;
      if ($request->has('state')) {

        //valid a state is exist in the current filter phase
        $is_state_valid = StateModel::get_pc_states_with_filter([
          'state' => base64_decode($request->state)
        ]);

        if (count($is_state_valid) > 0) {
          $data['state'] = base64_decode($request->state);
          $request_array[] = 'state=' . $request->state;
        }

        $filter_for_phases = [
          'election_type' => $data['election_type'],
          'state' => $data['state'],
        ];

        $data['phases'] = PhaseModel::get_phases($filter_for_phases)->map(function ($item) {
          $temp = $item;
          if (Auth::user()->role_id == '7') {
            $temp->statePHASE_NO = $item->PHASE_NO;
          }
          return (object)$temp;
        });
      }

      $data['phase'] = NULL;
      if ($request->has('phase')) {
        if ($this->xssClean->clean_input($request->phase) != 'all') {
          $data['phase'] = $this->xssClean->clean_input($request->phase);
        }
        $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);
      } else {
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase=' . $default_phase;
      }

      if ($request->has('round')) {
        $data['round']  = $request->round;
        $request_array[]  = 'round=' . $request->round;
      } else {
        $data['round']  = 0;
      }

      if (Auth::user()->designation == 'CEO') {
        $data['state'] = Auth::user()->st_code;
      }


      //set title
      $title_array  = [];
      $data['heading_title'] = 'Estimated Poll Percentage';

      if ($data['state']) {
        $state_object = StateModel::get_state_by_code($data['state']);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
        }
      }


      $data['filter_buttons'] = $title_array;



      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => "State Wise Report",
        'href' =>  url($this->action_state),
        'target' => false
      ];
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
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase']
      ]);

      $results                = [];

      $filter_election = [
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'group_by'      => 'ac_no',
        'order_by'      => 'ac_no',
      ];

      $object         = PollDayModel::get_reports($filter_election);

      foreach ($object as $result) {

        $individual_filter    = implode('&', array_merge($request_array, [
          'ac_no' => 'ac_no=' . $result->const_no,
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

        $old_percentage = ElectorModel::get_sum([
          'state'         => $result->st_code,
          'election_type'         => $data['election_type'],
          'phase'         => $data['phase'],
          'ac_no'         => $result->const_no,
          'group_by'      => 'ac_no',
          'year'          => 2014
        ]);

        $results[] = [
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
          "old_total_percentage"  => $old_percentage,
          "total_percentage"      => $result->total_percentage,
          "difference"            => ROUND($result->total_percentage - $old_percentage, 2),
          "st_code"               => $result->st_code,
          "href"                  => 'javascript:void(0)'
        ];
        // dd($results);
      }


      if ($data['state']) {
        $group_by = 'state';
      } else {
        $group_by = NULL;
      }

      $total_filter = [
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'group_by'      => $group_by
      ];
      $data['number_of_voting'] =  PollDayModel::get_average_sum($total_filter);
      $data['number_of_voting1'] =  PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 1,
        'group_by'      => $group_by
      ]);

      $data['number_of_voting2'] =  PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 2,
        'group_by'      => $group_by
      ]);

      $data['number_of_voting3'] =  PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 3,
        'group_by'      => $group_by
      ]);

      $data['number_of_voting4'] =  PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 4,
        'group_by'      => $group_by
      ]);

      $data['number_of_voting5'] =  PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 5,
        'group_by'      => $group_by
      ]);

      $data['number_of_voting6'] =  PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 6,
        'group_by'      => $group_by
      ]);

      // dd($data);
      $results[] = [
        'label'                 => 'State Total',
        'const_no'              => '-',
        'const'                 => '',
        'filter'                => '',
        "est_total_round1"      => $data['number_of_voting1'],
        "est_total_round2"      => $data['number_of_voting2'],
        "est_total_round3"      => $data['number_of_voting3'],
        "est_total_round4"      => $data['number_of_voting4'],
        "est_total_round5"      => $data['number_of_voting5'],
        'close_of_poll'         => $data['number_of_voting6'],
        "est_total"             => $data['number_of_voting'],
        "total_record"          => '',
        "old_total_percentage"  => '',
        "total_percentage"      => '',
        "difference"            => '',
        "st_code"               => '',
        "href"                  => 'javascript:void(0)'
      ];

      //dd($data['number_of_voting1']);

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

      return view($this->view_path . '.estimated.report_ac', $data);
    } catch (Exception $e) {
      Log::error($e);
      return Redirect::to('/eci/dashboard');
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
    $headings[] = [];
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

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:B1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

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
    //$export_data[] = ['State', 'PC No' ,'PC Name','AC No' ,'AC Name','Turnout % (2014)', 'Round1 %(Poll Start to 9:00 AM)','Round2 %(Poll Start to 11:00 AM)','Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)','Round5 %(Poll Start to 5:00 PM)', 'Latest Updated Poll %(2019)','Change from 2014'];

    $export_data[] = ['State', 'AC No', 'AC Name', 'Round1 %(Poll Start to 9:00 AM)', 'Round2 %(Poll Start to 11:00 AM)', 'Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)', 'Round5 %(Poll Start to 5:00 PM)', 'Close Of Poll %', 'Latest Updated Poll %'];

    $headings[] = [];
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

  public function report_district(Request $request)
  {
    if (Auth::user()->role_id == '4') {
      $this->action_state  = 'acceo/turnout/estimate-poll-percent';
      $this->action_ac = 'acceo/turnout/estimate-poll-percent/state/ac';
      $this->action_district     = 'acceo/turnout/estimate-poll-percent/state/district';
    }
    $data = [];
    $default_phase = PhaseModel::get_current_phase();
    $request_array = [];
    $data['election_type'] = NULL;
    if ($request->has('election_type')) {
      $data['election_type'] = $this->xssClean->clean_input($request->election_type);
      $request_array[] =  'election_type=' . $this->xssClean->clean_input($request->election_type);
    }
    $filter_for_phases = [
      'election_type' => $data['election_type']
    ];
    $data['phases'] = PhaseModel::get_phases($filter_for_phases);
    $data['phase'] = NULL;
    if ($request->has('phase')) {
      if ($this->xssClean->clean_input($request->phase) != 'all') {
        $data['phase'] = $this->xssClean->clean_input($request->phase);
      }
      $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);
    } else {
      $data['phase']    = $default_phase;
      $request_array[]  =  'phase=' . $default_phase;
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

    if ($request->has('round')) {
      $data['round']  = $request->round;
      $request_array[]  = 'round=' . $request->round;
    } else {
      $data['round']  = 0;
    }
    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }
    //set title
    $title_array  = [];
    $data['heading_title'] = 'Estimated Poll Percentage';

    if ($data['state']) {
      $state_object = StateModel::get_state_by_code($data['state']);
      if ($state_object) {
        $title_array[]  = "State: " . $state_object['ST_NAME'];
      }
    }


    $data['filter_buttons'] = $title_array;

    $filter_for_state = [
      'election_type' => $data['election_type'],
      'phase' => $data['phase']
    ];

    $states = StateModel::get_pc_states_with_filter($filter_for_state);

    $data['states'] = [];
    //STATE LISTR STARTS

    //FOR CEO 
    if (Auth::user()->role_id == '4') {
      $st_object = StateModel::get_state_by_code(Auth::user()->st_code);

      $data['states'][] = [
        'code' => base64_encode($st_object['ST_CODE']),
        'name' => $st_object['ST_NAME'],
      ];
    } else {
      //FOR ECI
      foreach ($states as $result) {
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
    $data['buttons'][]  = [
      'name' => "State Wise Report",
      'href' =>  url($this->action_state),
      'target' => false
    ];
    $data['buttons'][]  = [
      'name' => 'Export Excel',
      'href' =>  url($this->action_district . '/excel') . '?' . implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'View Ac Wise',
      'href' =>  url($this->action_ac) . '?' . implode('&', $request_array),
      'target' => false
    ];

    $data['action']         = url($this->action_district);

    $data['consituencies']  = AcModel::get_records([
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase']
    ]);

    $results                = [];

    $filter_election = [
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'group_by'      => 'dist_no',
      'order_by'      => 'dist_no',
    ];

    $object         = PollDayModel::get_district_reports($filter_election);
    foreach ($object as $result) {

      $individual_filter    = implode('&', array_merge($request_array, [
        'dist_no' => 'dist_no=' . $result->dist_no,
      ]));

      $results[] = [
        'label'                 => $result->st_name,
        'dist_no'              => $result->dist_no,
        'dist'                 => $result->dist,
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
        "href"                  => 'javascript:void(0)'
      ];
    }


    if ($data['state']) {
      $group_by = 'state';
    } else {
      $group_by = NULL;
    }

    $total_filter = [
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'group_by'      => $group_by
    ];
    $data['number_of_voting'] =  PollDayModel::get_average_sum($total_filter);

    $data['number_of_voting1'] =  PollDayModel::get_average_sum_roundwise([
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'round'         => 1,
      'group_by'      => $group_by
    ]);

    $data['number_of_voting2'] =  PollDayModel::get_average_sum_roundwise([
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'round'         => 2,
      'group_by'      => $group_by
    ]);

    $data['number_of_voting3'] =  PollDayModel::get_average_sum_roundwise([
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'round'         => 3,
      'group_by'      => $group_by
    ]);

    $data['number_of_voting4'] =  PollDayModel::get_average_sum_roundwise([
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'round'         => 4,
      'group_by'      => $group_by
    ]);

    $data['number_of_voting5'] =  PollDayModel::get_average_sum_roundwise([
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'round'         => 5,
      'group_by'      => $group_by
    ]);

    $data['number_of_voting6'] =  PollDayModel::get_average_sum_roundwise([
      'election_type'         => $data['election_type'],
      'state'         => $data['state'],
      'phase'         => $data['phase'],
      'round'         => 6,
      'group_by'      => $group_by
    ]);


    $data['results']    =   $results;

    $data['user_data']  =   Auth::user();

    $data['heading_title_with_all'] = $data['heading_title'];

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path . '.estimated.report_district', $data);
  }

  public function export_excel_report_district(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_district($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['State', 'District No', 'District Name', 'Round1 %(Poll Start to 9:00 AM)', 'Round2 %(Poll Start to 11:00 AM)', 'Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)', 'Round5 %(Poll Start to 5:00 PM)', 'Close Of Poll %', 'Latest Updated Poll %'];

    $headings[] = [];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['dist_no'],
        $lis['dist'],
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


    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
  }

  public function voterTurnoutReportWithOldACPCData(Request $request)
  {
    try {
      $this->action_ac     = 'eci/turnout/vt-report-with-old-ac-and-pc-vt-data';
      $data = [];
      $default_phase = PhaseModel::get_current_phase();
      $request_array = [];
      $data['election_type'] = NULL;
      if ($request->has('election_type')) {
        $data['election_type'] = $this->xssClean->clean_input($request->election_type);
        $request_array[] =  'election_type=' . $this->xssClean->clean_input($request->election_type);
      }

      $data['phase'] = NULL;
      if ($request->has('phase')) {
        if ($this->xssClean->clean_input($request->phase) != 'all') {
          $data['phase'] = $this->xssClean->clean_input($request->phase);
        }
        $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);
      } else {
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase=' . $default_phase;
      }
      $filter_for_state = [
        'election_type' => $data['election_type']
      ];

      $states = StateModel::get_pc_states_with_filter($filter_for_state);

      $data['states'] = [];
      //STATE LISTR STARTS

      //FOR CEO 
      foreach ($states as $result) {
        $data['states'][] = [
          'code' => base64_encode($result->ST_CODE),
          'name' => $result->ST_NAME,
        ];
      }


      $data['state'] = 'S26';
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


      $phases = ElectionModel::where(function ($q) use ($data) {
        if ($data['state'] != NULL) {
          $q->where('st_code', $data['state']);
        }
      });
      if ($data['state'] != NULL) {
        $phases->groupBy('StatePHASE_NO');
      }
      $data['phases'] = $phases->get();

      // dd($data['phases']);
      //set title
      $title_array  = [];
      $data['heading_title'] = 'Voter Turnout With Legislative Assembly - 2018'; // and Loksabha Election - 2019 VT data';

      if ($data['state']) {
        $state_object = StateModel::get_state_by_code($data['state']);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
        }
      }


      $data['filter_buttons'] = $title_array;

      //STATE LIST ENDS

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
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
      $data['results'] = EndOfPollModel::with(['state', 'ac' => function ($q) use ($data) {
        $q->where('ST_CODE', $data['state']);
      }, 'legislativeAssemblyVt', 'loksabhaElectionVt'])->where('st_code', $data['state'])->where(function ($q) use ($data) {
        if ($data['phase']) {
          $q->where('scheduleid', $data['phase']);
        }
      })->get()->map(function ($item) {
        $scheduleid = $item->scheduleid;
        if (isset($item->legislativeAssemblyVt)) {
          $lavt_vt = number_format((float)(($item->legislativeAssemblyVt->total_voters / $item->legislativeAssemblyVt->total_electors) * 100), 2, '.', '');
          $levt_vt = 0; //number_format((float)(($item->loksabhaElectionVt->total_voters / $item->loksabhaElectionVt->total_electors) * 100), 2, '.', '');
          $change_in_percentage = ($item->est_turnout_total == 0) ? 0 : $item->est_turnout_total - $lavt_vt;
        } else {
          $lavt_vt = 0;
          $levt_vt = 0; //number_format((float)(($item->loksabhaElectionVt->total_voters / $item->loksabhaElectionVt->total_electors) * 100), 2, '.', '');
          $change_in_percentage = 0;
        }
        return [
          'id' => $item->id,
          'est_turnout_total' => ($item->est_turnout_total != 0) ? $item->est_turnout_total : 'N/A',
          'scheduleid' => $scheduleid,
          'electors_total' => $item->electors_total,
          'est_voters' => $item->est_voters,
          'ac_no' => $item->ac_no,
          'st_code' => $item->st_code,
          'ac_name' => $item->ac->AC_NAME,
          'st_name' => $item->state->ST_NAME,
          'lavt_vt' => $lavt_vt,
          'levt_vt' => $levt_vt,
          'change_in_percentage' => $change_in_percentage
        ];
      });
      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];
      if ($request->has('is_excel')) {
        if (isset($title_array) && count($title_array) > 0) {
          $data['heading_title'] .= "- " . implode(', ', $title_array);
        }
        return $data;
      }
      return view($this->view_path . '.vt-report-with-old-ac-and-pc-vt-data', $data);
    } catch (Exception $e) {
      dd($e);
      return Redirect::to('/internalserver');
    }
  }

  public function voterTurnoutReportWithOldACPCDataExcel(Request $request)
  {
    try {

      ini_set("memory_limit", "1500M");
      set_time_limit('360');
      ini_set("pcre.backtrack_limit", "10000000");
      $request->merge(['is_excel' => 1]);
      $data = $this->voterTurnoutReportWithOldACPCData($request);
      $export_data = [];
      // $export_data[] = ['State Code', 'State', 'Phase',  'AC No', 'AC Name',  'Loksabha Election - 2019', 'Legislative Assembly - 2018', 'Legislative Assembly - 2023', 'Change In Percentage'];
      $export_data[] = ['State Code', 'State', 'Phase',  'AC No', 'AC Name', 'Legislative Assembly - 2018', 'Legislative Assembly - 2023', 'Change In Percentage'];

      $headings[] = [];
      foreach ($data['results'] as $lis) {
        $export_data[] = [
          $lis['st_code'],
          $lis['st_name'],
          $lis['scheduleid'],
          $lis['ac_no'],
          $lis['ac_name'],
          // $lis['levt_vt'],
          $lis['lavt_vt'],
          $lis['est_turnout_total'],
          $lis['change_in_percentage'],
        ];
      }


      $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

      return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
    } catch (\Throwable $th) {
      return Redirect::to('/internalserver');
    }
  }

  public function voterTurnoutReportWithOldACPCDataPDF(Request $request)
  {
    try {

      ini_set("memory_limit", "1500M");
      set_time_limit('360');
      ini_set("pcre.backtrack_limit", "10000000");
      $request->merge(['is_excel' => 1]);
      $data = $this->voterTurnoutReportWithOldACPCData($request);
      $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
      $pdf = \PDF::loadView($this->view_path . '.vt-report-with-old-ac-and-pc-vt-data-pdf', $data);
      return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
    } catch (\Throwable $th) {
      return Redirect::to('/internalserver');
    }
  }

  public function endOfPollCloseOfPollReport(Request $request)
  {

    try {
      $this->action_ac     = 'eci/turnout/vt-end-of-poll-close-of-poll';
      $data = [];
      $request_array = [];
      $data['election_type'] = NULL;
      if ($request->has('election_type')) {
        $data['election_type'] = $this->xssClean->clean_input($request->election_type);
        $request_array[] =  'election_type=' . $this->xssClean->clean_input($request->election_type);
      }
      $data['state'] = NULL;
      $data['state_encoded'] = NULL;
      $data['state_name'] = NULL;
      $data['state_phase'] = NULL;
      if ($request->has('state')) {
        $data['state_encoded'] = $request->state;
        //valid a state is exist in the current filter phase
        $is_state_valid = StateModel::get_pc_states_with_filter([
          'state' => base64_decode($request->state),
        ]);
        if (count($is_state_valid) > 0) {
          $data['state'] = base64_decode($request->state);
          $request_array[] = 'state=' . $request->state;
        }
      }

      $filter_for_state = [
        'election_type' => $data['election_type'],
      ];
      $states = StateModel::get_pc_states_with_filter($filter_for_state);

      $data['states'] = [];
      //STATE LISTR STARTS
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
        if ($this->xssClean->clean_input($request->phase) != 'all') {
          $data['phase'] = $this->xssClean->clean_input($request->phase);
        }
        $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);
        $statephase = ElectionModel::where('ST_CODE', $data['state'])->where('ScheduleID', $request->phase)->first();
        if ($statephase) {
          $data['state_phase'] = $statephase->StatePHASE_NO;
        } else {
          $data['state_phase'] = 'N/A';
        }
      } else {
        $data['state_phase'] = 'All Phases';
      }
      $title_array  = [];
      $data['heading_title'] = 'Close Of Poll and End of Poll Report';

      if ($data['state']) {
        $state_object = StateModel::get_state_by_code($data['state']);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
          $data['state_name'] = $state_object['ST_NAME'];
        }
      }


      $data['filter_buttons'] = $title_array;
      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
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
      $data['results'] = EndOfPollModel::with(['phase' => function ($q) use ($data) {
        $q->where('ST_CODE', $data['state']);
      }, 'state', 'ac' => function ($q) use ($data) {
        $q->where('ST_CODE', $data['state']);
      }, 'ac.district'])->where('st_code', $data['state'])
        ->where(function ($q) use ($data) {
          if ($data['phase'] != null) {
            $q->where('scheduleid', $data['phase']);
          }
        })
        ->orderBy('ac_no')->get()->map(function ($item) {

          $updated_at_close_of_poll = ($item->updated_at_close_of_poll != null) ? Carbon::parse($item->updated_at_close_of_poll)->format('d M h:i a') : 'Entry missed by RO';
          if ($item->ac_no == 166 && $item->st_code == 'S06') {
            $updated_at_close_of_poll = '01 Dec 11:58 pm';
          }

          $end_of_poll_finalize = ($item->updated_at_finalize != null) ? Carbon::parse($item->updated_at_finalize)->format('d M h:i a') : 'Entry missed by RO';
          if ($item->ac_no == 2 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '02 Dec 04:40 pm';
          } else if ($item->ac_no == 79 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '02 Dec 05:12 pm';
          } else if ($item->ac_no == 84 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '02 Dec 04:12 pm';
          } else if ($item->ac_no == 85 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '02 Dec 04:12 pm';
          } else if ($item->ac_no == 102 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '02 Dec 04:25 pm';
          } else if ($item->ac_no == 103 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '02 Dec 04:25 pm';
          } else if ($item->ac_no == 159 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '02 Dec 04:12 pm';
          } else if ($item->ac_no == 166 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '02 Dec 01:12 pm';
          } else if ($item->ac_no == 180 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '02 Dec 04:25 pm';
          } else if ($item->ac_no == 37 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '06 Dec 03:30 pm';
          } else if ($item->ac_no == 52 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '06 Dec 03:50 pm	';
          } else if ($item->ac_no == 53 && $item->st_code == 'S06') {
            $end_of_poll_finalize = '06 Dec 03:50 pm	';
          }


          return [
            'st_code' => $item->st_code,
            'st_name' => $item->state->ST_NAME,
            'dist_no' => $item->ac->DIST_NO_HDQTR,
            'dist_name' => $item->ac->district->DIST_NAME,
            'ac_no' => $item->ac_no,
            'ac_name' => $item->ac->AC_NAME,
            'phase' => $item->phase->StatePHASE_NO,
            'close_of_poll' => $item->close_of_poll,
            'updated_at_close_of_poll' => $updated_at_close_of_poll,
            'est_turnout_total' => $item->close_of_poll,
            'end_of_poll_finalize' => $end_of_poll_finalize,
          ];
        });
      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];
      if ($request->has('is_excel')) {
        if (isset($title_array) && count($title_array) > 0) {
          $data['heading_title'] .= "- " . implode(', ', $title_array);
        }
        return $data;
      }
      // dd($data);
      return view($this->view_path . '.vt-end-of-poll-close-of-poll', $data);
    } catch (Exception $e) {
      return Redirect::to('/internalserver');
    }
  }

  public function endOfPollCloseOfPollReportExcel(Request $request)
  {
    try {

      ini_set("memory_limit", "1500M");
      set_time_limit('360');
      ini_set("pcre.backtrack_limit", "10000000");
      $request->merge(['is_excel' => 1]);
      $data = $this->endOfPollCloseOfPollReport($request);
      $export_data = [];
      $export_data[] = ['State Code', 'State', 'Phase', 'District No', 'District Name',  'AC No', 'AC Name',  'Close Of Poll Date Time', 'End of Poll Date Time'];

      $headings[] = [];
      foreach ($data['results'] as $lis) {
        $export_data[] = [
          $lis['st_code'],
          $lis['st_name'],
          $lis['phase'],
          $lis['dist_no'],
          $lis['dist_name'],
          $lis['ac_no'],
          $lis['ac_name'],
          $lis['updated_at_close_of_poll'],
          $lis['end_of_poll_finalize'],
        ];
      }


      $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

      return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
    } catch (\Throwable $th) {
      return Redirect::to('/internalserver');
    }
  }

  public function endOfPollCloseOfPollReportPDF(Request $request)
  {
    try {

      ini_set("memory_limit", "1500M");
      set_time_limit('360');
      ini_set("pcre.backtrack_limit", "10000000");
      $request->merge(['is_excel' => 1]);
      $data = $this->endOfPollCloseOfPollReport($request);
      $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
      $pdf = \PDF::loadView($this->view_path . '.vt-end-of-poll-close-of-poll-pdf', $data);
      return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
    } catch (\Throwable $th) {
      return Redirect::to('/internalserver');
    }
  }

  public function report_ac_new(Request $request)
  {
    try {
      $this->action_ac     = 'eci/turnout/estimate-poll-percent/state/ac/new';
      $data = [];
      $default_phase = PhaseModel::get_current_phase();
      $request_array = [];
      $data['election_type'] = NULL;
      if ($request->has('election_type')) {
        $data['election_type'] = $this->xssClean->clean_input($request->election_type);
        $request_array[] =  'election_type=' . $this->xssClean->clean_input($request->election_type);
      }


      $data['state'] = NULL;
      if ($request->has('state')) {
        //valid a state is exist in the current filter phase
        $is_state_valid = StateModel::get_pc_states_with_filter([
          'state' => base64_decode($request->state)
        ]);

        if (count($is_state_valid) > 0) {
          $data['state'] = base64_decode($request->state);
          $request_array[] = 'state=' . $request->state;
        }
      }

      if ($request->has('round')) {
        $data['round']  = $request->round;
        $request_array[]  = 'round=' . $request->round;
      } else {
        $data['round']  = 0;
      }

      if (Auth::user()->designation == 'CEO') {
        $data['state'] = Auth::user()->st_code;
      }


      //set title
      $title_array  = [];
      $data['heading_title'] = 'Estimated Poll Percentage';

      if ($data['state']) {
        $state_object = StateModel::get_state_by_code($data['state']);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
        }
      }


      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        'election_type' => $data['election_type']
      ];

      $states = StateModel::get_pc_states_with_filter($filter_for_state);
      $data['states'] = [];
      foreach ($states as $result) {
        $data['states'][] = [
          'code' => base64_encode($result->ST_CODE),
          'name' => $result->ST_NAME,
        ];
      }
      //STATE LISTR STARTS
      $filter_for_phases = [
        'election_type' => $data['election_type'],
        'state' => $data['state']
      ];
      $data['phases'] = PhaseModel::get_phases($filter_for_phases)->map(function ($item) {
        return (object) [
          'PHASE_NO' => $item->PHASE_NO,
          'statePHASE_NO' => $item->PHASE_NO,
          'ELECTION_ID' => $item->ELECTION_ID,
          'election_status' => $item->election_status,
          'ELECTION_TYPEID' => $item->ELECTION_TYPEID,
          'SCHEDULENO' => $item->SCHEDULENO,
          'DATE_POLL' => $item->DATE_POLL,
        ];
      });
      // dd($data['phases']);
      $data['phase'] = NULL;
      if ($request->has('phase')) {
        if ($this->xssClean->clean_input($request->phase) != 'all') {
          $data['phase'] = $this->xssClean->clean_input($request->phase);
        }
        $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);
      } else {
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase=' . $default_phase;
      }



      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => "State Wise Report",
        'href' =>  url($this->action_state),
        'target' => false
      ];
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
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase']
      ]);

      $results                = [];

      $filter_election = [
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'group_by'      => 'ac_no',
        'order_by'      => 'ac_no',
      ];

      $object         = PollDayModel::get_reports($filter_election);
      foreach ($object as $result) {

        $individual_filter    = implode('&', array_merge($request_array, [
          'ac_no' => 'ac_no=' . $result->const_no,
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

        $old_percentage = ElectorModel::get_sum([
          'state'         => $result->st_code,
          'election_type'         => $data['election_type'],
          'phase'         => $data['phase'],
          'ac_no'         => $result->const_no,
          'group_by'      => 'ac_no',
          'year'          => 2014
        ]);

        $results[] = [
          'label'                 => $state_name,
          'const_no'              => $result->const_no,
          'const'                 => $ac_name,
          'filter'                => $individual_filter,
          "est_total_round1"      => $result->est_total_round1,
          "est_total_round2"      => ($result->est_total_round2 != 0) ? $result->est_total_round2 : $result->est_total_round1,
          "est_total_round3"      => ($result->est_total_round3 != 0) ? $result->est_total_round3 : $result->est_total_round2,
          "est_total_round4"      => ($result->est_total_round4 != 0) ? $result->est_total_round4 : $result->est_total_round3,
          "est_total_round5"      => ($result->est_total_round5 != 0) ? $result->est_total_round5 : $result->est_total_round4,
          'close_of_poll'         => ($result->close_of_poll != 0) ? $result->close_of_poll : $result->est_total_round5,
          "est_total"             => $result->est_total,
          "total_record"          => $result->total_record,
          "old_total_percentage"  => $old_percentage,
          "total_percentage"      => $result->total_percentage,
          "difference"            => ROUND($result->total_percentage - $old_percentage, 2),
          "st_code"               => $result->st_code,
          "href"                  => 'javascript:void(0)'
        ];
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

      return view($this->view_path . '.estimated.report_ac', $data);
    } catch (Exception $e) {
      return Redirect::to('/internalserver');
    }
  }

  public function export_excel_report_ac_new(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_ac_new($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['State', 'AC No', 'AC Name', 'Round1 %(Poll Start to 9:00 AM)', 'Round2 %(Poll Start to 11:00 AM)', 'Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)', 'Round5 %(Poll Start to 5:00 PM)', 'Close Of Poll %', 'Latest Updated Poll %'];

    $headings[] = [];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['const_no'],
        $lis['const'],
        ($lis['est_total_round1']) ? $lis['est_total_round1'] : '0',
        ($lis['est_total_round2']) ? $lis['est_total_round2'] : '0',
        ($lis['est_total_round3']) ? $lis['est_total_round3'] : '0',
        ($lis['est_total_round4']) ? $lis['est_total_round4'] : '0',
        ($lis['est_total_round5']) ? $lis['est_total_round5'] : '0',
        ($lis['close_of_poll']) ? $lis['close_of_poll'] : '0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
      ];
    }

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
  }

  public function export_pdf_report_ac_new(Request $request)
  {
    $data = $this->report_ac_new($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.estimated.report_ac_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }

  public function estimateEntryLogs(Request $request)
  {
    try {
      $this->action_ac     = 'eci/turnout/estimate-entry-logs';
      $data = [];
      $request_array = [];
      $data['election_type'] = NULL;
      if ($request->has('election_type')) {
        $data['election_type'] = $this->xssClean->clean_input($request->election_type);
        $request_array[] =  'election_type=' . $this->xssClean->clean_input($request->election_type);
      }
      $data['state'] = NULL;
      $data['ac'] = NULL;
      $data['state_encoded'] = NULL;
      $data['state_name'] = NULL;
      $data['state_phase'] = NULL;
      $data['round'] = NULL;
      if ($request->has('state')) {
        $data['state_encoded'] = $request->state;
        //valid a state is exist in the current filter phase
        $is_state_valid = StateModel::get_pc_states_with_filter([
          'state' => base64_decode($request->state),
        ]);
        if (count($is_state_valid) > 0) {
          $data['state'] = base64_decode($request->state);
          $request_array[] = 'state=' . $request->state;
        }
      }

      if ($request->has('ac')) {
        $data['ac'] = $request->ac;
      }

      if ($request->has('round')) {
        $data['round'] = $request->round;
      }

      $filter_for_state = [
        'election_type' => $data['election_type'],
      ];
      $states = StateModel::get_pc_states_with_filter($filter_for_state);

      $data['states'] = [];
      //STATE LISTR STARTS
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
        if ($this->xssClean->clean_input($request->phase) != 'all') {
          $data['phase'] = $this->xssClean->clean_input($request->phase);
        }
        $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);
        $statephase = ElectionModel::where('ST_CODE', $data['state'])->where('ScheduleID', $request->phase)->first();
        if ($statephase) {
          $data['state_phase'] = $statephase->StatePHASE_NO;
        } else {
          $data['state_phase'] = 'N/A';
        }
      } else {
        $data['state_phase'] = 'All Phases';
      }
      $title_array  = [];
      $data['heading_title'] = 'Estimated Entry Logs';
      $data['acs'] = [];
      if ($data['state']) {
        $state_object = StateModel::get_state_by_code($data['state']);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
          $data['state_name'] = $state_object['ST_NAME'];
        }

        if ($data['phase'] != null) {
          $acs = EndOfPollModel::where('st_code', $data['state'])->where('scheduleid', $data['phase'])->pluck('ac_no');
          $data['acs'] = AC::where('ST_CODE', $data['state'])->whereIn('AC_NO', $acs)->get();
        }
      }


      $data['filter_buttons'] = $title_array;
      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
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
      $data['results'] = EstimatedEntryLog::with(['phase' => function ($q) use ($data) {
        $q->where('ST_CODE', $data['state']);
      }, 'state', 'ac' => function ($q) use ($data) {
        $q->where('ST_CODE', $data['state']);
      }])->where('st_code', $data['state'])
        ->where(function ($q) use ($data) {
          if ($data['phase'] != null) {
            $q->where('scheduleid', $data['phase']);
          }
          if ($data['round'] != null && $data['round'] != 'all') {
            $q->where('round', $data['round']);
          }
          if ($data['ac'] != null && $data['ac'] != 'all') {
            $q->where('ac_no', $data['ac']);
          }
        })
        ->orderBy('created_at')->get();
      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];
      if ($request->has('is_excel')) {
        if (isset($title_array) && count($title_array) > 0) {
          $data['heading_title'] .= "- " . implode(', ', $title_array);
        }
        return $data;
      }
      return view($this->view_path . '.estimate-entry-logs', $data);
    } catch (Exception $e) {
      return Redirect::to('/internalserver');
    }
  }

  public function export_excel_estimateEntryLogs(Request $request)
  {

    set_time_limit(6000);
    $data = $this->estimateEntryLogs($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = ['State', 'Phase', 'AC No', 'AC Name', 'Round', 'Round Percentage', 'State Percentage', 'Updated By', 'Date Time'];

    $headings[] = [];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['st_code'] . '-' . $lis['state']['ST_NAME'],
        $lis['phase']['StatePHASE_NO'],
        $lis['ac_no'],
        $lis['ac']['AC_NAME'],
        $lis['round'],
        $lis['percentage'],
        $lis['state_percentage'],
        $lis['updatedby'],
        $lis['created_at'],
      ];
    }

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
  }

  public function export_pdf_estimateEntryLogs(Request $request)
  {
    $data = $this->estimateEntryLogs($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.estimate-entry-logs-pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }

  public function voterTurnoutAfterRoundPercentageChangeReport(Request $request)
  {
    try {
      $this->action_ac     = 'eci/turnout/voter-turnout-after-round-percentage-change';
      $data = [];
      $request_array = [];
      $data['election_type'] = NULL;
      if ($request->has('election_type')) {
        $data['election_type'] = $this->xssClean->clean_input($request->election_type);
        $request_array[] =  'election_type=' . $this->xssClean->clean_input($request->election_type);
      }
      $data['state'] = NULL;
      $data['ac'] = NULL;
      $data['state_encoded'] = NULL;
      $data['state_name'] = NULL;
      $data['state_phase'] = NULL;
      if ($request->has('state')) {
        $data['state_encoded'] = $request->state;
        //valid a state is exist in the current filter phase
        $is_state_valid = StateModel::get_pc_states_with_filter([
          'state' => base64_decode($request->state),
        ]);
        if (count($is_state_valid) > 0) {
          $data['state'] = base64_decode($request->state);
          $request_array[] = 'state=' . $request->state;
        }
      }

      if ($request->has('ac')) {
        $data['ac'] = $request->ac;
      }

      $filter_for_state = [
        'election_type' => $data['election_type'],
      ];
      $states = StateModel::get_pc_states_with_filter($filter_for_state);

      $data['states'] = [];
      //STATE LISTR STARTS
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
        if ($this->xssClean->clean_input($request->phase) != 'all') {
          $data['phase'] = $this->xssClean->clean_input($request->phase);
        }
        $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);
        $statephase = ElectionModel::where('ST_CODE', $data['state'])->where('ScheduleID', $request->phase)->first();
        if ($statephase) {
          $data['state_phase'] = $statephase->StatePHASE_NO;
        } else {
          $data['state_phase'] = 'N/A';
        }
      } else {
        $data['state_phase'] = 'All Phases';
      }
      $title_array  = [];
      $data['heading_title'] = 'Voter Turnout After Round Percentage Change';
      $data['acs'] = [];
      if ($data['state']) {
        $state_object = StateModel::get_state_by_code($data['state']);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
          $data['state_name'] = $state_object['ST_NAME'];
        }
      }


      $data['filter_buttons'] = $title_array;
      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
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
      $result = [];

      $result['round1_per_exclude_missed_ac'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 1,
        'startTime' => '9:31',
        'endTime' => '10:59',
        'row' => 'first',
      ]);

      $result['round1_per_include_missed_ac'] = PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 1,
        'group_by'      => 'state'
      ]);

      $result['round1_missed_ac_count'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 1,
        'startTime' => '9:31',
        'endTime' => '10:59',
        'row' => 'all',
      ]);

      $result['round2_per_exclude_missed_ac'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 2,
        'startTime' => '11:31',
        'endTime' => '12:59',
        'row' => 'first',
      ]);
      $result['round2_per_include_missed_ac'] = PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 2,
        'group_by'      => 'state'
      ]);
      $result['round2_missed_ac_count'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 2,
        'startTime' => '11:31',
        'endTime' => '12:59',
        'row' => 'all',
      ]);

      $result['round3_per_exclude_missed_ac'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 3,
        'startTime' => '13:31',
        'endTime' => '14:59',
        'row' => 'first',
      ]);
      $result['round3_per_include_missed_ac'] = PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 3,
        'group_by'      => 'state'
      ]);

      $result['round3_missed_ac_count'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 3,
        'startTime' => '13:31',
        'endTime' => '14:59',
        'row' => 'all',
      ]);

      $result['round4_per_exclude_missed_ac'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 4,
        'startTime' => '15:31',
        'endTime' => '16:59',
        'row' => 'first',
      ]);
      $result['round4_per_include_missed_ac'] = PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 4,
        'group_by'      => 'state'
      ]);
      $result['round4_missed_ac_count'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 4,
        'startTime' => '15:31',
        'endTime' => '16:59',
        'row' => 'all',
      ]);

      $result['round5_per_exclude_missed_ac'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 5,
        'startTime' => '17:31',
        'endTime' => '18:59',
        'row' => 'first',
      ]);
      $result['round5_per_include_missed_ac'] = PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 5,
        'group_by'      => 'state'
      ]);

      $result['round5_missed_ac_count'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 5,
        'startTime' => '17:31',
        'endTime' => '18:59',
        'row' => 'all',
      ]);

      $result['round6_per_exclude_missed_ac'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 6,
        'startTime' => '19:00',
        'endTime' => '18:59',
        'row' => 'first',
      ]);
      $result['round6_per_include_missed_ac'] = PollDayModel::get_average_sum_roundwise([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'round'         => 6,
        'group_by'      => 'state'
      ]);

      $result['round6_missed_ac_count'] = EstimatedEntryLog::roundPerWithExcludedMissedAc([
        'state' => $data['state'],
        'phase' => $data['phase'],
        'round' => 5,
        'startTime' => '17:31',
        'endTime' => '18:59',
        'row' => 'all',
      ]);

      $result['final'] =  (float)PollDayModel::get_average_sum([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         =>  $data['phase'],
        'group_by'      => 'state'
      ]);
      // dd($result);

      $data['results']  =   $result;
      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];
      if ($request->has('is_excel')) {
        if (isset($title_array) && count($title_array) > 0) {
          $data['heading_title'] .= "- " . implode(', ', $title_array);
        }
        return $data;
      }
      return view($this->view_path . '.voter-turnout-after-round-percentage-change', $data);
    } catch (Exception $e) {
      return Redirect::to('/internalserver');
    }
  }

  public function voterTurnoutAfterRoundPercentageChangeReportExcel(Request $request)
  {

    set_time_limit(6000);
    $data = $this->voterTurnoutAfterRoundPercentageChangeReport($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = ['', 'VT %', 'Missing No. Of ACs	', 'Change of VT %	', 'Final VT %'];
    $headings[] = [];
    $export_data[] = [
      '09:30',
      $data['results']['round1_per_exclude_missed_ac'],
      $data['results']['round1_missed_ac_count'],
      number_format(($data['results']['round1_per_include_missed_ac'] - $data['results']['round1_per_exclude_missed_ac']), 2, '. ', ''),
      $data['results']['round1_per_include_missed_ac'],
    ];
    $export_data[] = [
      '11:30',
      $data['results']['round2_per_exclude_missed_ac'],
      $data['results']['round2_missed_ac_count'],
      number_format(($data['results']['round2_per_include_missed_ac'] - $data['results']['round2_per_exclude_missed_ac']), 2, '. ', ''),
      $data['results']['round2_per_include_missed_ac'],
    ];

    $export_data[] = [
      '01:30',
      $data['results']['round3_per_exclude_missed_ac'],
      $data['results']['round3_missed_ac_count'],
      number_format(($data['results']['round3_per_include_missed_ac'] - $data['results']['round3_per_exclude_missed_ac']), 2, '. ', ''),
      $data['results']['round3_per_include_missed_ac'],
    ];
    $export_data[] = [
      '03:30',
      $data['results']['round4_per_exclude_missed_ac'],
      $data['results']['round4_missed_ac_count'],
      number_format(($data['results']['round4_per_include_missed_ac'] - $data['results']['round4_per_exclude_missed_ac']), 2, '. ', ''),
      $data['results']['round4_per_include_missed_ac'],
    ];
    $export_data[] = [
      '05:30',
      $data['results']['round5_per_exclude_missed_ac'],
      $data['results']['round5_missed_ac_count'],
      number_format(($data['results']['round5_per_include_missed_ac'] - $data['results']['round5_per_exclude_missed_ac']), 2, '. ', ''),
      $data['results']['round5_per_include_missed_ac'],
    ];
    $export_data[] = [
      'State',
      $data['state_name'],
      'Final Voter Turnout %',
      '',
      $data['results']['final'],
    ];


    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
  }

  public function voterTurnoutAfterRoundPercentageChangeReportPdf(Request $request)
  {
    ini_set("memory_limit", "1500M");
    set_time_limit('360');
    ini_set("pcre.backtrack_limit", "10000000");
    $data = $this->voterTurnoutAfterRoundPercentageChangeReport($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.voter-turnout-after-round-percentage-change-pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }
}  // end class
