<?php

namespace App\Http\Controllers\Admin\turnout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use App\commonModel;
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\ReportModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\StateModel;

// use Maatwebsite\Excel\Excel;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Classes\secureCode;

//END OF POLL FINALISE MODAL
use App\models\Admin\EndOfPollFinaliseModel;
use App\models\Admin\PhaseModel;

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;

date_default_timezone_set('Asia/Kolkata');


class EndOfPollFinalisedController extends Controller
{

  public $folder        = 'eci';
  public $action_state  = 'eci/turnout/EndOfPollFinalised';
  public $action_ac     = 'eci/turnout/EndOfPollFinalisedList';
  public $view_path     = "admin.turnout";
  public $commonModel     = null;
  public $ECIModel     = null;
  public $voting_model     = null;
  public $EopFinalisedModal     = null;
  public $xssClean     = null;


  //USING TRAIT FOR COMMON FUNCTIONS
  use CommonTraits;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware(['auth:admin', 'auth']);
    $this->middleware('clean_url');
    //$this->middleware('clean_request');
    $this->commonModel = new commonModel();
    $this->ECIModel = new ECIModel();
    $this->voting_model = new PollDayModel();
    $this->EopFinalisedModal = new EndOfPollFinaliseModel();
    $this->xssClean = new xssClean;
    $this->middleware(function ($request, $next) {
      return $next($request);
    });

    if (!Auth::user()) {
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
    return Auth::guard();
  }




  //END OF POLL FINALSED REPORT STARTS
  public function EndOfPollFinalised(Request $request)
  {
    // END OF POLL FINALSED REPORT TRY CATCH BLOCK STARTS

    $users = Session::get('admin_login_details');
    $user = Auth::user();

    if (session()->has('admin_login')) {

      //CHECKING FOR USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if (Auth::user()->role_id == '4') {

        $this->action_state  = 'acceo/turnout/EndOfPollFinalised';
        $this->action_ac     = 'acceo/turnout/EndOfPollFinalisedList';
      }

      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);

      $list_record = $this->ECIModel->getallelectionphasewise();

      $list_state = $this->ECIModel->listcurrentelectionstate();

      $list_phase = $this->ECIModel->listcurrentelectionphase();

      $list_electionid = $this->ECIModel->getallelectionbyid();

      $list = $this->ECIModel->listelectiontype();

      $module = $this->commonModel->getallmodule();

      $cur_time    = Carbon::now();

      $cur_time  = Carbon::now();
      $st_code = $user_data->st_code;
      $st_name = $user_data->placename;
      //dd($AllPartyList);

      $data['user_data']  =   Auth::user();

      //SETTING STATE VARIABLE TO NULL IN STARTING
      $data['state'] = NULL;

      //CHECKING IF THE REQUEST CONTAINS THE STATE DATA OR NOT
      if ($request->has('state')) {
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state=' . $request->state;
      }

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
          $data['phase'] = $this->xssClean->clean_input($request->phase);
        }

        $request_array[] =  'phase=' . $this->xssClean->clean_input($request->phase);
      } else {
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase=' . $default_phase;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'End of Poll AC Finalised';

      //GET STATE NAME BY STATE CODE STARTS
      if ($data['state']) {
        $state_object = StateModel::get_state_by_code($data['state']);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
        }
      }
      //GET STATE NAME BY STATE CODE ENDS

      $data['filter_buttons'] = $title_array;

      //SETTING STATE CODE IF USER IS CEO
      if (Auth::user()->role_id == '4') {
        $data['state']  = Auth::user()->st_code;
      }


      //LISTING ALL STATES FOR DATABASE RESULTS
      $states = StateModel::get_states();
      $data['states'] = [];

      foreach ($states as $result) {

        //FOR CEO 
        if (Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code) {
          $data['states'][] = [
            'st_code' => $result->ST_CODE,
            'name' => $result->ST_NAME,
          ];
        }

        //FOR ECI
        if (Auth::user()->role_id == '7') {
          $data['states'][] = [
            'st_code' => $result->ST_CODE,
            'name'    => $result->ST_NAME,
          ];
        }
      }
      //GET STATE NAME BY STATE CODE ENDS

      $data['filter']   = implode('&', array_merge($request_array));

      $data['phases'] = PhaseModel::get_phases();

      // if($data['phase']){
      //   $title_array[] = "Phase: ".$data['phase'];
      // }

      $data['filter_buttons'] = $title_array;

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


      $statewise_results = [];

      if (Auth::user()->role_id == '4') {

        $filter_election = [
          'st_code'         => Auth::user()->st_code,
          'election_type'           => $data['election_type'],
          'phase'           => $data['phase'],
          'order_by'        => 'state',
          'group_by'        => 'state',
        ];
      }

      if (Auth::user()->role_id == '7' || Auth::user()->role_id == '26') {

        $filter_election = [
          'election_type'         => $data['election_type'],
          'phase'         => $data['phase'],
          'group_by'      => 'state',
          'order_by'      => 'state',
        ];
      }

      //foreach ($data['states'] as $state_result) {

      $object_states = EndOfPollFinaliseModel::get_eop_finalise_data($filter_election);

      foreach ($object_states as $result) {


        $individual_filter_array          = [];
        $individual_filter_array['state'] = 'state=' . $result->st_code;
        $individual_filter_array['phase'] = 'phase=' . $data['phase'];
        $individual_filter_array['election_type'] = 'election_type=' . $data['election_type'];
        $individual_filter                = implode('&', $individual_filter_array);

        $results[] = [
          'label'               => $result->state_name,
          "total_const"         => $result->total_const,
          "const_finalised"     => $result->const_finalised,
          "href"                => url($this->action_ac) . "?" . $individual_filter
        ];
      }

      //}


      $data['results']    =   $results;

      $data['heading_title_with_all'] = $data['heading_title'];


      if ($request->has('is_excel')) {
        if (isset($title_array) && count($title_array) > 0) {
          $data['heading_title'] .= "- " . implode(', ', $title_array);
        }

        return $data;
      }

      return view($this->view_path . '.end_of_poll.EciEndOfPollFinalised', $data);

      /*
        return view('admin.pc.eci.EciEndOfPollFinalised',['user_data' => $user_data,'EciEndOfPollFinalised' => $EciEndOfPollFinalised]);*/
    } else {
      return redirect('/admin-login');
    }


    /*try{}catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');

    }*/
    //ECI END OF POLL FINALSED REPORT TRY CATCH BLOCK ENDS

  }
  //ECI END OF POLL FINALSED REPORT FUNCTION ENDS

  //END OF POLL FINALSED EXCEL REPORT STARTS
  public function EndOfPollFinalisedExcel(Request $request)
  {
    //END OF POLL FINALSED EXCEL REPORT TRY CATCH BLOCK STARTS
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $d = $this->commonModel->getunewserbyuserid($uid);

        $list_record = $this->ECIModel->getallelectionphasewise();

        $list_state = $this->ECIModel->listcurrentelectionstate();

        $list_phase = $this->ECIModel->listcurrentelectionphase();

        $list_electionid = $this->ECIModel->getallelectionbyid();

        $list = $this->ECIModel->listelectiontype();

        $module = $this->commonModel->getallmodule();

        $cur_time    = Carbon::now();

        set_time_limit(6000);
        $data = $this->EndOfPollFinalised($request->merge(['is_excel' => 1]));
        $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));


        $export_data[] = ['State', 'Total Acs', 'Ac Finalised'];
        $headings[] = [];
        $TotalConst = 0;
        $TotalFinaliseConst = 0;
        foreach ($data['results'] as $result) {

          if ($result['label'] == '') {

            $result['label'] = '0';
          }

          if ($result['total_const'] == '') {

            $result['total_const'] = '0';
          }

          if ($result['const_finalised'] == '') {

            $result['const_finalised'] = '0';
          }

          $export_data[] = [
            $result['label'],
            $result['total_const'],
            $result['const_finalised'],
          ];




          $TotalConst             +=   $result['total_const'];
          $TotalFinaliseConst     +=   $result['const_finalised'];
        }


        return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . $cur_time . '.xlsx');

        //   \Excel::create($name_excel.'_'.$cur_time, function($excel) use($data) { 
        //   $excel->sheet('Sheet1', function($sheet) use($data) {

        //    $arr  = array();
        //    $TotalConst = 0;
        //    $TotalFinaliseConst = 0;


        //   $user = Auth::user();

        //   foreach ($data['results'] as $result) {

        //      if($result['label'] ==''){

        //        $result['label'] = '0';

        //      }

        //      if($result['total_const'] ==''){

        //         $result['total_const'] = '0';

        //      }

        //      if($result['const_finalised'] ==''){

        //         $result['const_finalised'] = '0';

        //      }


        //      $exceldata =  array(

        //               $result['label'],
        //               $result['total_const'],
        //               $result['const_finalised'],

        //                     );

        //     $TotalConst             +=   $result['total_const'];
        //     $TotalFinaliseConst     +=   $result['const_finalised'];

        //               array_push($arr, $exceldata);
        //                // }
        //               }

        //    $totalvalues = array('Total',$TotalConst,$TotalFinaliseConst);
        //     // print_r($totalvalues);die;
        //       array_push($arr,$totalvalues);
        //       $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
        //                    'State', 'Total Acs', 'Ac Finalised'
        //            )

        //        );

        //      });

        // })->export('xls');

      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    // END OF POLL FINALSED EXCEL REPORT TRY CATCH BLOCK ENDS

  }
  // END OF POLL FINALSED EXCEL REPORT FUNCTION ENDS

  //END OF POLL FINALSED PDF REPORT STARTS
  public function EndOfPollFinalisedPdf(Request $request)
  {
    //END OF POLL FINALSED PDF REPORT TRY CATCH BLOCK STARTS
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $list_record = $this->ECIModel->getallelectionphasewise();

        $list_state = $this->ECIModel->listcurrentelectionstate();

        $list_phase = $this->ECIModel->listcurrentelectionphase();

        $list_electionid = $this->ECIModel->getallelectionbyid();

        $list = $this->ECIModel->listelectiontype();

        $module = $this->commonModel->getallmodule();

        $cur_time    = Carbon::now();


        $cur_time  = Carbon::now();
        $st_code = $user_data->st_code;
        $st_name = $user_data->placename;

        $data = $this->EndOfPollFinalised($request->merge(['is_excel' => 1]));

        /*$pdf = \PDF::loadView($this->view_path.'.EciEndOfPollFinalisedPdf',$data);
              return $pdf->download($EciEndOfPollFinalisedPdf.'_'.date('d-m-Y').'_'.time().'.pdf');*/
        $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

        $pdf = PDF::loadView($this->view_path . '.end_of_poll..EciEndOfPollFinalisedPdf', ['user_data' => $data['user_data'], 'data' => $data['results'], 'heading_title' => $data['heading_title'], 'phase' => $data['phase']]);
        return $pdf->download($name_excel . trim($st_name) . '_Today_' . $cur_time . '.pdf');
        return view($this->view_path . '.end_of_poll.EciEndOfPollFinalisedPdf');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //END OF POLL FINALSED PDF REPORT TRY CATCH BLOCK ENDS

  }
  //END OF POLL FINALSED PDF REPORT FUNCTION ENDS



  //END OF POLL FINALSED LIST REPORT STARTS
  public function EndOfPollFinalisedList(Request $request)
  {
    // END OF POLL FINALSED LIST REPORT TRY CATCH BLOCK STARTS

    $users = Session::get('admin_login_details');
    $user = Auth::user();

    if (session()->has('admin_login')) {

      //CHECKING FOR USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if (Auth::user()->role_id == '4') {

        $this->action_state  = 'acceo/turnout/EndOfPollFinalisedList';
        $this->action_ac     = 'acceo/turnout/EndOfPollFinalisedList';
      }
      //CHECKING FOR USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if (Auth::user()->role_id == '7' || Auth::user()->role_id == '26') {

        $this->action_state  = 'eci/turnout/EndOfPollFinalisedList';
        $this->action_ac     = 'eci/turnout/EndOfPollFinalisedList';
      }

      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);

      $list_record = $this->ECIModel->getallelectionphasewise();

      $list_state = $this->ECIModel->listcurrentelectionstate();

      $list_phase = $this->ECIModel->listcurrentelectionphase();

      $list_electionid = $this->ECIModel->getallelectionbyid();

      $list = $this->ECIModel->listelectiontype();

      $module = $this->commonModel->getallmodule();

      $cur_time    = Carbon::now();

      $cur_time  = Carbon::now();
      $st_code = $user_data->st_code;
      $st_name = $user_data->placename;
      //dd($AllPartyList);

      $data['user_data']  =   Auth::user();

      $default_phase = PhaseModel::get_current_phase();

      $request_array = [];

      //SETTING STATE VARIABLE TO NULL IN STARTING
      $data['state'] = NULL;

      //CHECKING IF THE REQUEST CONTAINS THE STATE DATA OR NOT
      if ($request->has('state')) {
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state=' . $request->state;
      }

      $data['election_type'] = NULL;
      if ($request->has('election_type')) {
        $data['election_type'] = $request->election_type;
        $request_array[] =  'election_type=' . $request->election_type;
      }



      /*if($data['phase']==1){      
            $data['phase']    = 1;
            $data['phases'] =  [];
          }*/

      //set title
      $title_array  = [];
      $data['heading_title'] = 'End of Poll AC Finalised List';

      //GET STATE NAME BY STATE CODE STARTS
      if ($data['state']) {
        $state_object = StateModel::get_state_by_code($data['state']);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
        }
      }
      //GET STATE NAME BY STATE CODE ENDS


      //SETTING STATE CODE IF USER IS CEO
      if (Auth::user()->role_id == '4') {
        $data['state']  = Auth::user()->st_code;
      }
      //SETTING STATE CODE IF USER IS ECI
      if ($request->has('state')) {
        $data['state'] = $request->state;
        $state_object = StateModel::get_state_by_code($data['state']);
        if ($state_object) {
          $title_array[]  = "State: " . $state_object['ST_NAME'];
        }
      }
      // if($data['phase']){
      //   $title_array[] = "Phase: ".$data['phase'];
      // }

      //LISTING ALL STATES FOR DATABASE RESULTS
      $states = StateModel::get_states();
      $data['states'] = [];

      $filter_for_phases = [
        'election_type' => $data['election_type'],
        'state' => $data['state']
      ];

      $data['phases'] = PhaseModel::get_phases($filter_for_phases);

      // $data['phases'] = PhaseModel::get_phases();
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



      foreach ($states as $result) {

        //FOR CEO 
        if (Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code) {
          $data['states'][] = [
            'st_code' => $result->ST_CODE,
            'name' => $result->ST_NAME,
          ];
        }

        //FOR ECI
        if (Auth::user()->role_id == '7' || Auth::user()->role_id == '26') {
          $data['states'][] = [
            'st_code' => $result->ST_CODE,
            'name'    => $result->ST_NAME,
          ];
        }
      }
      //GET STATE NAME BY STATE CODE ENDS

      $data['filter']   = implode('&', array_merge($request_array));

      $data['filter_buttons'] = $title_array;

      //buttons
      $data['buttons']    = [];
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


      if (Auth::user()->role_id == '4') {

        $filter_election = [
          'st_code'         => Auth::user()->st_code,
          'election_type'           => $data['election_type'],
          'phase'           => $data['phase'],
          'order_by'        => 'ac_no',
          'group_by'        => 'ac_no',
        ];
      }

      if (Auth::user()->role_id == '7' || Auth::user()->role_id == '26') {

        if ($data['state']) {
          $filter_election = [
            'election_type'         => $data['election_type'],
            'phase'         => $data['phase'],
            'st_code'       => $data['state'],
            'group_by'      => 'ac_no',
            'order_by'      => 'ac_no',
          ];
        } else {
          $filter_election = [
            'election_type'         => $data['election_type'],
            'phase'         => $data['phase'],
            'st_code'       => $data['state'],
            'group_by'      => 'state',
            'order_by'      => 'state',
          ];
        }
      }


      $object_states = EndOfPollFinaliseModel::get_eop_finalise_list($filter_election);
      //dd($object_states);
      foreach ($object_states as $result) {

        $results[] = [
          'label'                => $result->state_name,
          "const_no"             => $result->const_no,
          "const"                => $result->const,
          "finalized_const"      => $result->finalized_const
        ];
      }

      $data['results']    =   $results;
      $data['heading_title_with_all'] = $data['heading_title'];

      return view($this->view_path . '.end_of_poll.EciEndOfPollFinalisedList', $data);

      /*
        return view('admin.pc.eci.EciEndOfPollFinalised',['user_data' => $user_data,'EciEndOfPollFinalised' => $EciEndOfPollFinalised]);*/
    } else {
      return redirect('/admin-login');
    }


    /*try{}catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');

    }*/
    //ECI END OF POLL FINALSED LIST REPORT TRY CATCH BLOCK ENDS

  }
  //ECI END OF POLL FINALSED LIST REPORT FUNCTION ENDS



  //END OF POLL FINALSED LIST EXCEL REPORT STARTS
  public function EndOfPollFinalisedListExcel(Request $request)
  {
    //END OF POLL FINALSED EXCEL REPORT TRY CATCH BLOCK STARTS
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $d = $this->commonModel->getunewserbyuserid($uid);

        $list_record = $this->ECIModel->getallelectionphasewise();

        $list_state = $this->ECIModel->listcurrentelectionstate();

        $list_phase = $this->ECIModel->listcurrentelectionphase();

        $list_electionid = $this->ECIModel->getallelectionbyid();

        $list = $this->ECIModel->listelectiontype();

        $module = $this->commonModel->getallmodule();

        $cur_time    = Carbon::now();

        set_time_limit(6000);
        $data = $this->EndOfPollFinalisedList($request->merge(['is_excel' => 1]));
        $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

        $export_data[] = ['State', 'AC No', 'AC Name', 'Ac Finalised'];
        $headings[] = [];

        foreach ($data['results'] as $result) {

          if ($result['label'] == '') {

            $result['label'] = '0';
          }

          if ($result['const_no']  == '') {

            $result['const_no']  = '0';
          }


          if ($result['finalized_const'] == '') {

            $result['finalized_const'] = '0';
          }

          $export_data[] = [
            $result['label'],
            $result['const_no'],
            $result['const'],
            $result['finalized_const'],



          ];
        }


        return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . $cur_time . '.xlsx');



        //   \Excel::create($name_excel.'_'.$cur_time, function($excel) use($data) { 
        //   $excel->sheet('Sheet1', function($sheet) use($data) {

        //    $arr  = array();

        //   $user = Auth::user();

        //   foreach ($data['results'] as $result) {

        //      if($result['label'] ==''){

        //        $result['label'] = '0';

        //      }

        //      if($result['const_no']  ==''){

        //         $result['const_no']  = '0';

        //      }


        //      if($result['finalized_const'] ==''){

        //         $result['finalized_const'] = '0';

        //      }


        //      $exceldata =  array(

        //               $result['label'],
        //               $result['const_no'],
        //               $result['const'],
        //               $result['finalized_const'],

        //                     );

        //               array_push($arr, $exceldata);
        //                // }
        //               }

        //       $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
        //                    'State', 'AC No', 'AC Name','Ac Finalised'
        //            )

        //        );

        //      });

        // })->export('xls');

      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    // END OF POLL FINALSED EXCEL REPORT TRY CATCH BLOCK ENDS

  }
  // END OF POLL FINALSED EXCEL REPORT FUNCTION ENDS


  //END OF POLL FINALSED LIST PDF REPORT STARTS
  public function EndOfPollFinalisedListPdf(Request $request)
  {
    //END OF POLL FINALSED LIST PDF REPORT TRY CATCH BLOCK STARTS
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $list_record = $this->ECIModel->getallelectionphasewise();

        $list_state = $this->ECIModel->listcurrentelectionstate();

        $list_phase = $this->ECIModel->listcurrentelectionphase();

        $list_electionid = $this->ECIModel->getallelectionbyid();

        $list = $this->ECIModel->listelectiontype();

        $module = $this->commonModel->getallmodule();

        $cur_time    = Carbon::now();


        $cur_time  = Carbon::now();
        $st_code = $user_data->st_code;
        $st_name = $user_data->placename;

        $data = $this->EndOfPollFinalisedList($request->merge(['is_excel' => 1]));

        /*$pdf = \PDF::loadView($this->view_path.'.EciEndOfPollFinalisedPdf',$data);
              return $pdf->download($EciEndOfPollFinalisedPdf.'_'.date('d-m-Y').'_'.time().'.pdf');*/
        $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

        $pdf = PDF::loadView($this->view_path . '.end_of_poll..EciEndOfPollFinalisedListPdf', ['user_data' => $data['user_data'], 'filter_buttons' => $data['filter_buttons'], 'results' => $data['results'], 'heading_title' => $data['heading_title'], 'phase' => $data['phase']]);
        return $pdf->download($name_excel . trim($st_name) . '_Today_' . $cur_time . '.pdf');
        return view($this->view_path . '.end_of_poll.EciEndOfPollFinalisedListPdf');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //END OF POLL FINALSED LIST PDF REPORT TRY CATCH BLOCK ENDS

  }
  //END OF POLL FINALSED LIST PDF REPORT FUNCTION ENDS






}  // end class