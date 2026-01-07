<?php

namespace App\Http\Controllers\Admin\turnout;

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
use App\models\Admin\EndOfPollModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;
use App\Classes\xssClean;
//current

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class PolldayEndOfPollController extends Controller
{

  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/turnout/end-of-poll';
  public $action_ac     = 'eci/turnout/end-of-poll/state/ac';
  public $view_path     = "admin.turnout";
  public $commonModel     = null;
  public $xssClean     = null;

  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->xssClean = new xssClean;
    $this->middleware(function ($request, $next) {

      return $next($request);
    });

    if (!Auth::user()) {
      return redirect('/officer-login');
    }
  }

  public function report_state(Request $request)
  {

    $data = [];
    $data['number_of_voting'] = 0;
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];
    $data['election_type'] = NULL;
    if ($request->has('election_type')) {
      $data['election_type'] = $request->election_type;
      $request_array[] =  'election_type=' . $request->election_type;
    }



    /*if($data['phase']==1){      
		$data['phase']    = 1;
		$data['phases'] =  [];
		}*/

    $data['state'] = NULL;
    if ($request->has('state')) {
      $data['state'] = base64_decode($request->state);
      $request_array[] = 'state=' . $request->state;
    }

    //set title
    $title_array  = [];
    $data['heading_title'] = 'End of Poll';
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

    $filter_for_phases = [
      'election_type' => $data['election_type'],
      'state' => $data['state']
    ];

    $data['phases'] = PhaseModel::get_phases($filter_for_phases);
    // $data['phases'] = PhaseModel::get_phases();
    $data['phase'] = NULL;
    if ($request->has('phase')) {
      if ($request->phase != 'all') {
        $data['phase'] = $this->xssClean->clean_input($request->phase);
      }
      $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);;
    } else {
      $data['phase']    = $default_phase;
      $request_array[]  =  'phase=' . $default_phase;
    }

    $data['filter']   = implode('&', array_merge($request_array));
    //end set title

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]  = [
      'name' => 'All State Report',
      'href' =>  url($this->action_state),
      'target' => false
    ];
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
      'election_type'         => $data['election_type'],
      'phase'         => $data['phase'],
    ];


    $object_states = EndOfPollModel::get_reports($filter_election);

    foreach ($object_states as $result) {

      $filter_data = [
        'state'         => $result->st_code,
        'phase'         => $data['phase'],
        'election_type'         => $data['election_type'],
        //'year'          => 2019,
        'group_by'      => 'state'
      ];

      $individual_filter_array = [];
      if ($data['phase']) {
        $individual_filter_array['phase'] = 'phase=' . $data['phase'];
      }
      if ($data['election_type']) {
        $individual_filter_array['election_type'] = 'election_type=' . $data['election_type'];
      }
      $individual_filter_array['state'] = 'state=' . base64_encode($result->st_code);
      $individual_filter    = implode('&', $individual_filter_array);

      //get total electors
      $object_elector  = EndOfPollModel::get_total_elector($filter_data);

      $object_voter    = EndOfPollModel::get_percentage_2019($filter_data);

      $results[] = [
        'label'               => $result->st_name,
        'filter'              => $individual_filter,
        "st_code"             => $result->st_code,
        "ac_no"               => $result->ac_no,
        "old_total_male"      => $object_elector['old_total_male'],
        "old_total_female"    => $object_elector['old_total_female'],
        "old_total_other"     => $object_elector['old_total_other'],
        "old_total"           => $object_elector['old_total'],
        "total_male"          => $object_voter['total_voter_male'],
        "total_female"        => $object_voter['total_voter_female'],
        "total_other"         => $object_voter['total_voter_other'],
        "total"               => $object_voter['total_voter_total'],
        "total_percentage"    => $object_voter['total_percentage'],
        "href"                => url($this->action_ac) . "?" . $individual_filter
      ];
    }

    $total_filter = [
      'election_type'         => $data['election_type'],
      'phase'         => $data['phase'],
      'group_by'      => 'national'
    ];



    //calculate total
    $total_object =  EndOfPollModel::get_reports($total_filter);

    if (count($total_object) > 0) {
      $result           = $total_object[0];
      $object_elector   = EndOfPollModel::get_total_elector([
        'election_type'     => $data['election_type'],
        'phase'     => $data['phase'],
        'group_by'  => 'national',
        //'year'          => 2019
      ]);

      $object_voter    = EndOfPollModel::get_percentage_2019([
        'election_type'     => $data['election_type'],
        'phase'     => $data['phase'],
        'group_by'  => 'national',
      ]);

      $data['totals'] = [
        'label'               => 'Total',
        'filter'              => '',
        "st_code"             => $result->st_code,
        "ac_no"               => $result->ac_no,
        "old_total_male"      => $object_elector['old_total_male'],
        "old_total_female"    => $object_elector['old_total_female'],
        "old_total_other"     => $object_elector['old_total_other'],
        "old_total"           => $object_elector['old_total'],
        "total_male"          => $object_voter['total_voter_male'],
        "total_female"        => $object_voter['total_voter_female'],
        "total_other"         => $object_voter['total_voter_other'],
        "total"               => $object_voter['total_voter_total'],
        "total_percentage"    => $object_voter['total_percentage'],
        "href"                => ''
      ];

      $data['number_of_voting'] = ($object_voter['total_percentage']) ? $object_voter['total_percentage'] : 0;
    }

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



    return view($this->view_path . '.end_of_poll.state', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }


  public function report_ac(Request $request)
  {

    $data = [];
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];

    $data['election_type'] = NULL;
    if ($request->has('election_type')) {
      $data['election_type'] = $request->election_type;
      $request_array[] =  'election_type=' . $request->election_type;
    }

    


    //     if($data['phase']==1){      
    // $data['phase']    = 1;
    // $data['phases'] =  [];
    //  }

    $data['state'] = NULL;
    if ($request->has('state')) {

      //valid a state is exist in the current filter phase
      $is_state_valid = StateModel::get_pc_states_with_filter([
        'state' => base64_decode($request->state),
        'election_type' => $data['election_type']
      ]);

      if (count($is_state_valid) > 0) {
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state=' . $request->state;
      }
    }
    $data['stateEncript'] = $request->state;

    if (Auth::user()->designation == 'CEO') {
      $data['state'] = Auth::user()->st_code;
    }

    //set title
    $title_array  = [];
    $data['heading_title'] = 'End of Poll';

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

    $filter_for_phases = [
      'election_type' => $data['election_type'],
      'state' => $data['state']
    ];

    $data['phases'] = PhaseModel::get_phases($filter_for_phases);

    //$data['phases'] = PhaseModel::get_phases();

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
      'name' => 'Back',
      'href' =>  url($this->action_state) . '?' . 'phase=' . $data['phase'],
      'target' => false
    ];

    $data['action']         = url($this->action_ac);

    $data['consituencies']  = PcModel::get_records([
      'state'         => $data['state'],
      'election_type'         => $data['election_type'],
      'phase'         => $data['phase']
    ]);

    $results                = [];

    $filter_election = [
      'state'         => $data['state'],
      'election_type'         => $data['election_type'],
      'phase'         => $data['phase'],
      'group_by'      => 'ac_no',
      'order_by'      => 'ac_no'
    ];

    $object         = EndOfPollModel::get_reports($filter_election);

    foreach ($object as $result) {

      $individual_filter    = implode('&', array_merge($request_array));

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

      //get total electors
      $object_elector  = EndOfPollModel::get_total_elector([
        'ac_no'     => $result->ac_no,
        'state'     => $result->st_code,
        'election_type'     => $data['election_type'],
        'phase'     => $data['phase'],
        'group_by'  => 'ac_no',
        //'year'          => 2019
      ]);

      $object_voter    = EndOfPollModel::get_percentage_2019([
        'ac_no'     => $result->ac_no,
        'state'     => $result->st_code,
        'election_type'     => $data['election_type'],
        'phase'     => $data['phase'],
        'group_by'  => 'ac_no',
      ]);

      $results[] = [
        'label'               => $result->st_name,
        'filter'              => $individual_filter,
        "st_code"             => $result->st_code,
        "ac_no"               => $result->ac_no,
        "ac_name"             => $ac_name,
        "old_total_male"      => $object_elector['old_total_male'],
        "old_total_female"    => $object_elector['old_total_female'],
        "old_total_other"     => $object_elector['old_total_other'],
        "old_total"           => $object_elector['old_total'],
        "total_male"          => $object_voter['total_voter_male'],
        "total_female"        => $object_voter['total_voter_female'],
        "total_other"         => $object_voter['total_voter_other'],
        "total"               => $object_voter['total_voter_total'],
        "total_percentage"    => $object_voter['total_percentage'],
        "href"                => 'javascript:void(0)'
      ];
    }


    //calculate total
    if ($data['state']) {
      $group_by = 'state';
    } else {
      $group_by = 'national';
    }


    $data['number_of_voting'] =  0;

    //total

    $object_elector   = EndOfPollModel::get_total_elector([
      'election_type'     => $data['election_type'],
      'phase'     => $data['phase'],
      'group_by'  => $group_by,
      'state'     => $data['state'],
      //'year'      => 2019
    ]);

    $object_voter    = EndOfPollModel::get_percentage_2019([
      'election_type'     => $data['election_type'],
      'phase'     => $data['phase'],
      'group_by'  => $group_by,
      'state'     => $data['state'],
    ]);

    $data['totals'] = [
      'label'               => 'Total',
      'filter'              => '',
      "pc_no"               => '',
      "pc_name"             => '',
      "ac_no"               => '',
      "ac_name"             => '',
      "st_code"             => '',
      "ac_no"               => '',
      "old_total_male"      => $object_elector['old_total_male'],
      "old_total_female"    => $object_elector['old_total_female'],
      "old_total_other"     => $object_elector['old_total_other'],
      "old_total"           => $object_elector['old_total'],
      "total_male"          => $object_voter['total_voter_male'],
      "total_female"        => $object_voter['total_voter_female'],
      "total_other"         => $object_voter['total_voter_other'],
      "total"               => $object_voter['total_voter_total'],
      "total_percentage"    => $object_voter['total_percentage'],
      "href"                => ''
    ];

    $data['number_of_voting'] = ($object_voter['total_percentage']) ? $object_voter['total_percentage'] : 0;


    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();

    $data['heading_title_with_all'] = $data['heading_title'];

    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path . '.end_of_poll.ac', $data);

    try {
    } catch (\Exception $e) {
      return Redirect::to('/eci/dashboard');
    }
  }

  public function export_excel_report_state(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_state($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['', 'Electors', '', '', '', 'Voters', '', '', '', ''];

    $export_data[] = ['State', 'Male', 'Female', 'Other', 'Total', 'Male', 'Female', 'Other', 'Total', 'Total Percentage'];
    $headings[] = [];

    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        ($lis['old_total_male']) ? $lis['old_total_male'] : '0',
        ($lis['old_total_female']) ? $lis['old_total_female'] : '0',
        ($lis['old_total_other']) ? $lis['old_total_other'] : '0',
        ($lis['old_total']) ? $lis['old_total'] : '0',
        ($lis['total_male']) ? $lis['total_male'] : '0',
        ($lis['total_female']) ? $lis['total_female'] : '0',
        ($lis['total_other']) ? $lis['total_other'] : '0',
        ($lis['total']) ? $lis['total'] : '0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
      ];
    }

    $export_data[] = [
      $data['totals']['label'],
      ($data['totals']['old_total_male']) ? $data['totals']['old_total_male'] : '0',
      ($data['totals']['old_total_female']) ? $data['totals']['old_total_female'] : '0',
      ($data['totals']['old_total_other']) ? $data['totals']['old_total_other'] : '0',
      ($data['totals']['old_total']) ? $data['totals']['old_total'] : '0',

      ($data['totals']['total_male']) ? $data['totals']['total_male'] : '0',
      ($data['totals']['total_female']) ? $data['totals']['total_female'] : '0',
      ($data['totals']['total_other']) ? $data['totals']['total_other'] : '0',
      ($data['totals']['total']) ? $data['totals']['total'] : '0',
      ($data['totals']['total_percentage']) ? $data['totals']['total_percentage'] : '0',
    ];

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:J1');
    //       $sheet->mergeCells('B2:E2');
    //       $sheet->mergeCells('F2:I2');
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
    $pdf = \PDF::loadView($this->view_path . '.end_of_poll.state_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }


  //export AC's
  public function export_excel_report_ac(Request $request)
  {

    set_time_limit(6000);
    $data = $this->report_ac($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['', '', '', 'Electors', '', '', '', 'Voters', '', '', '', ''];

    $export_data[] = ['State', 'ac no', 'ac name', 'Male', 'Female', 'Other', 'Total', 'Male', 'Female', 'Other', 'Total', 'Total Percentage'];
    $headings[] = [];

    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['ac_no'],
        $lis['ac_name'],
        ($lis['old_total_male']) ? $lis['old_total_male'] : '0',
        ($lis['old_total_female']) ? $lis['old_total_female'] : '0',
        ($lis['old_total_other']) ? $lis['old_total_other'] : '0',
        ($lis['old_total']) ? $lis['old_total'] : '0',
        ($lis['total_male']) ? $lis['total_male'] : '0',
        ($lis['total_female']) ? $lis['total_female'] : '0',
        ($lis['total_other']) ? $lis['total_other'] : '0',
        ($lis['total']) ? $lis['total'] : '0',
        ($lis['total_percentage']) ? $lis['total_percentage'] : '0',
      ];
    }

    $export_data[] = [
      $data['totals']['label'],
      $data['totals']['ac_no'],
      $data['totals']['ac_name'],
      ($data['totals']['old_total_male']) ? $data['totals']['old_total_male'] : '0',
      ($data['totals']['old_total_female']) ? $data['totals']['old_total_female'] : '0',
      ($data['totals']['old_total_other']) ? $data['totals']['old_total_other'] : '0',
      ($data['totals']['old_total']) ? $data['totals']['old_total'] : '0',
      ($data['totals']['total_male']) ? $data['totals']['total_male'] : '0',
      ($data['totals']['total_female']) ? $data['totals']['total_female'] : '0',
      ($data['totals']['total_other']) ? $data['totals']['total_other'] : '0',
      ($data['totals']['total']) ? $data['totals']['total'] : '0',
      ($data['totals']['total_percentage']) ? $data['totals']['total_percentage'] : '0',
    ];

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');

    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:L1');
    //       $sheet->mergeCells('D2:G2');
    //       $sheet->mergeCells('H2:K2');
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
    $pdf = \PDF::loadView($this->view_path . '.end_of_poll.ac_pdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }
}  // end class