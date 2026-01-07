<?php

namespace App\Http\Controllers\Admin\turnout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\commonModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\PollDayComparisionModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;
use App\Classes\xssClean;

use App\Exports\ExcelExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class EstimateComparisionController extends Controller
{

  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/turnout/estimate-poll-percent';
  public $action_ac     = 'eci/turnout/estimate-poll-percent-comparision/state/ac';
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



  public function report_comparision(Request $request)
  {

    if (Auth::user()->role_id == '4') {

      $this->action_state  = 'acceo/turnout/estimate-poll-percent';
      $this->action_ac     = 'acceo/turnout/estimate-poll-percent-comparision/state/ac';
    }

    $data = [];
    $default_phase = PhaseModel::get_current_phase();
    $state_name = '';
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
    $data['state_encrypted'] = $request->state;


    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }


    //set title
    $title_array  = [];
    $data['heading_title'] = 'Estimated Voter Turnout Comparison Report';

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
      'election_type' => $data['election_type'],
      // 'phase' => $data['phase']
    ];

    $states = StateModel::get_pc_states_with_filter($filter_for_state);

    $data['states'] = [];
    //STATE LISTR STARTS

    $filter_for_phases = [
      'election_type' => $data['election_type'],
      'state' => $data['state']
    ];

    $data['phases'] = PhaseModel::get_phases($filter_for_phases);

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
    /*  $data['buttons'][]  = [
        'name' => "State Wise Report",
        'href' =>  url($this->action_state),
        'target' => false
      ]; */
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

    $data['buttons'][]  = [
      'name' => 'Export Pdf on Poll Day',
      'href' =>  url($this->action_ac . '/pdf-color') . '?' . implode('&', $request_array),
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

    $est_total_round1 = 0;
    $est_total_round2 = 0;
    $est_total_round3 = 0;
    $est_total_round4 = 0;
    $est_total_round5 = 0;
    $close_of_poll    = 0;
    $est_total        = 0;
    $total_percentage = 0;
    $electors_total = 0;
    $est_voters = 0;

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

      $old_percentage = PollDayComparisionModel::old_percentage([
        'st_code'         => $result->st_code,
        'election_type' => $data['election_type'],
        'phase'         => $data['phase'],
        'ac_no'         => $result->const_no,
        'group_by'      => 'ac_no'
      ]);

      if (@$old_percentage->est_total_turnout) {
        $old_percentage = $old_percentage->est_total_turnout;
      } else {
        $old_percentage = 0;
      }

      //dd($old_percentage);

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
        "electors_total"        => $result->electors_total,
        "est_voters"            => $result->est_voters,
        "old_total_percentage"  => $old_percentage,
        "total_percentage"      => $result->total_percentage,
        "difference"            => ROUND($result->total_percentage - $old_percentage, 2),
        "st_code"               => $result->st_code,
        "href"                  => 'javascript:void(0)'
      ];


      $electors_total += $result->electors_total;
      $est_voters += $result->est_voters;
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
    $old_per =  DB::table('previous_election_details')->select('loksabha', 'assembly')
      ->where('st_code', $data['state'])
      ->where('scheduledid', $data['phase'])->first();

    $statetotal = array();
    // dd($old_per);

    if (isset($data['state']) && ($data['state'])) {
      $statetotal[] = [
        'label'                 => ($state_name) ?: 'State-sub',
        'const_no'              => '-',
        'const'                 => '',
        'filter'                => '',
        "est_total"             => $data['number_of_voting'],
        "electors_total"        => $electors_total,
        "est_voters"            => $est_voters,
        "total_record"          => '',
        "loksabha"        => @$old_per->loksabha,
        "assembly"        => @$old_per->assembly,
        "total_percentage"      => '',
        //"difference"            => ROUND($data['number_of_voting'] - $old_per_average, 2),
        "st_code"               => '',
        "href"                  => 'javascript:void(0)'
      ];
    }

    //dd($results);


    $poll_date = DB::table('m_schedule')->select('DATE_POLL')->where('SCHEDULEID', $data['phase'])->first();

    $data['results']    =   $results;
    $data['statetotal']    =   $statetotal;
    $data['poll_date']    =   @$poll_date->DATE_POLL;

    //dd($data['poll_date']);

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

    return view($this->view_path . '.comparision.estimate_comparision', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }


  //export AC's
  public function export_excel_report_comparision(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_comparision($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['State', 'AC No', 'AC Name', 'Previous Election Turnout (in %)', '2021 Estimated Turnout (in %)', 'Change from Previous Election'];
    $headings[] = [];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['const_no'],
        $lis['const'],
        ($lis['old_total_percentage']) ? $lis['old_total_percentage'] : '0',
        ($lis['est_total']) ? $lis['est_total'] : '0',
        ($lis['difference']) ? $lis['difference'] : '0',
      ];
    }

    //dd($export_data);
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

  public function export_pdf_report_comparision(Request $request)
  {
    $data = $this->report_comparision($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = Pdf::loadView($this->view_path . '.comparision.estimate_comparision_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }

  public function export_pdf_report_comparision_color(Request $request)
  {
    $data = $this->report_comparision($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = Pdf::loadView($this->view_path . '.comparision.estimate_comparision_color_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }
}  // end class