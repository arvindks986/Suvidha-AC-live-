<?php 
namespace App\Http\Controllers\Admin\BoothAppRevamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use App\commonModel;  
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, TblPollSummaryModel, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, JsonFile, OfficerAssignmentModel,ExemptPollingModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;
use Excel;
use App\Http\Controllers\Admin\BoothAppRevamp\ExemptPollingController;
use App\models\Admin\PhaseModel;
//current

class ExemptController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $ps_no         = NULL;
  public $role_id       = 0;
  public $base          = 'ro';
  public $action_state  = 'eci/booth-app-revamp/exempt-turnout-report';
  public $action_ac     = 'eci/booth-app-revamp/exempt-turnout-report/state/ac';
  public $view_path     = "admin.booth-app-revamp";

  public function __construct(Request $request){
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      $default_values = Common::get_request_filter($request);
      $this->ac_no    = $default_values['ac_no'];
      $this->st_code  = $default_values['st_code'];
      $this->ps_no    = $default_values['ps_no'];
      $this->phase_no    = $default_values['phase_no'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id  = $default_values['role_id'];
      $this->base     = $default_values['base'];
      return $next($request);
    });
  }
  
//POLL TURNOUT STATE STARTS
public function exempt_turnout_report(Request $request){


    //CHECKING AUTH SESSION EXIST OR NOT STARTS
	if (empty(Auth::check())) {
   return Redirect('/')->with('error', 'You Are Not Logged In.');
 }
	//CHECKING AUTH SESSION EXIST OR NOT ENDS


	//SETTING VARIABLES FOR SENDING RESULTS ON NEXT PAGE STARTS
 $data = [];
 $request_array = [];
 $data['role_id']       = $this->role_id;
 if($this->phase_no){
  $request_array[]  =  'phase_no='.$this->phase_no;
}
if($this->st_code){
 $request_array[]  =  'st_code='.$this->st_code;
}
if($this->dist_no){
  $request_array[]  =  'dist_no='.$this->dist_no;
}
if($this->ac_no){
  $request_array[]  =  'ac_no='.$this->ac_no;
}

    //SETTING VARIABLES FOR SENDING RESULTS ON NEXT PAGE ENDS

	//CHECKING FOR USER TYPE AND SETTING VARIABLES FOR IT STARTS
$this->action_state =Common::generate_url('booth-app-revamp/exempt-turnout-report');
$this->action_ac =Common::generate_url('booth-app-revamp/exempt-turnout-report/state/ac');

	//GETTING LOGGED IN USERS DATA FROM AUTH 
$data['user_data']  =   Auth::user();

    //SETTING TITILE OF THE PAGE 
$title_array  = [];
$data['heading_title']  = 'Booth App - Exempted PS Wise Turnout Report';

//GET STATE NAME, DIST NAME AND AC NAMECODE STARTS
if($this->st_code){
  $state_object = StateModel::get_state_by_code($this->st_code);
  if($state_object){
    $title_array[]  = "State: ".$state_object['ST_NAME'];
  }
}
if($this->dist_no){
  $dist_object = DistrictModel::get_district([
    'dist_no' => $this->dist_no,
    'st_code' => $this->st_code
  ]);
  if($dist_object){
    $title_array[]  = "District: ".$dist_object['dist_name'];
  }
}
if($this->ac_no){
  $ac_object = AcModel::get_ac([
    'ac_no' => $this->ac_no,
    'state' => $this->st_code
  ]);
  if($ac_object){
    $title_array[]  = "AC: ".$ac_object['ac_name'];
  }
}
    //GET STATE NAME, DIST NAME AND AC NAME CODE ENDS

$data['filter_buttons'] = $title_array;

    //LISTING ALL STATES FOR DATABASE RESULTS
$states = StateModel::get_states();

$data['states'] = [];

foreach($states as $result){

        //FOR CEO 
        if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' => $result->ST_NAME,
          ];
        }
  
        //FOR ECI
        if(Auth::user()->role_id == '7'){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name'    => $result->ST_NAME,
          ];
        }

        //FOR DISTRICT NODAL OFFICER 
        if(Auth::user()->role_id == '5' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' =>    $result->ST_NAME,
          ];
        }


     }

$data['filter']   = implode('&', array_merge($request_array));

     //SETTING BUTTONS FOR REPORTS STARTS
$data['buttons']    = [];
$data['buttons'][]  = [
  'name' => 'Export Excel',
  'href' =>  url($this->action_state.'/excel').'?'.implode('&', $request_array),
  'target' => true
];
$data['buttons'][]  = [
  'name' => 'Export Pdf',
  'href' =>  url($this->action_state.'/pdf').'?'.implode('&', $request_array),
  'target' => true
];
    //SETTING BUTTONS FOR REPORTS ENDS

  $data['action']         = url($this->action_state);

  $results                = [];
  $results['grand_percentage'] = [];
  $results['poll_turnout_percentage'] = [];

  $statewise_results = [];
    //STATE LOOP STARTS
    foreach ($data['states'] as $state_result) {

    //STATE NAME
    if($state_result){
        $state_name = $state_result['name'];
    }

  $pollingModel  = new ExemptPollingController($request);  
  $filter = [
    'st_code'     => $this->st_code,
  ];  
  $poll_turnout  = $pollingModel->exempt_turnout_state_wise($request,$filter);

  $data['poll_turnout_percentage'] = $poll_turnout['poll_turnout_percentage'];
  $data['grand_percentage'] = $poll_turnout['grand_percentage'];

  foreach ($poll_turnout['voter_turnouts'] as $pto_data) {

     $individual_filter_array          = [];
          
     $individual_filter_array['st_code'] = 'st_code='.$state_result['st_code'];

    if($this->dist_no){
      $individual_filter_array['dist_no'] = 'dist_no='.$this->dist_no;
    }
    if($this->ac_no){
      $individual_filter_array['ac_no'] = 'ac_no='.$this->ac_no;
    }
    if($this->phase_no){
      $individual_filter_array['phase_no'] = 'phase_no='.$this->phase_no;
    }
    $individual_filter                = implode('&', $individual_filter_array);

      if($pto_data['st_code'] == ''){
          $href= '';
      }else {
        $href=url($this->action_ac)."?".$individual_filter;
      }

      $statewise_results[] = [
                        'label'          => $pto_data['st_name'],
                        'st_code'        => $pto_data['st_code'],
                        'is_state'       => 0,
                        'total_ps'       => $pto_data['total_ps'],
                        'total_exempted_ps' => $pto_data['total_exempted_ps'],
                        'round_1_total'  => $pto_data['round_1_total'],
                        'round_2_total'  => $pto_data['round_2_total'],
                        'round_3_total'  => $pto_data['round_3_total'],
                        'round_4_total'  => $pto_data['round_4_total'],
                        'round_5_total'  => $pto_data['round_5_total'],
                        'round_5_total'  => $pto_data['round_5_total'],
                        'rounds_total'   => $pto_data['round_1_total']+$pto_data['round_2_total']+$pto_data['round_3_total']+$pto_data['round_4_total']+$pto_data['round_5_total'],
                        'male'           => $pto_data['male'],
                        'female'         => $pto_data['female'],
                        'other'          => $pto_data['other'],
                        'total'          => $pto_data['total'],
                        'e_male'         => $pto_data['e_male'],
                        'e_female'       => $pto_data['e_female'],
                        'e_other'        => $pto_data['e_other'],
                        'e_total'        => $pto_data['e_total'],
                        'total_in_queue' => $pto_data['total_in_queue'],
                        'percentage'     => $pto_data['percentage'],
                        "href"           => $href,
                      ]; 

   }
  }//STATE LOOP ENDS
  $data['results']  =   $statewise_results;

  if($request->has('is_excel')){
    if(isset($title_array) && count($title_array)>0){
      $data['heading_title'] .= "- ".implode(', ', $title_array);
    }
    return $data;
  }


		//form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/exempt-turnout-report");

    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'dist_no'     => false,
      'ac_no'       => false, 
      'ps_no'       => false, 
      'designation'     => false,
    ];

    $form_filters = Common::get_form_filters($form_filter_array, $request);      
    $data['form_filters'] = $form_filters;
    return view($this->view.'.Reports.exempt.exempt_turnout_state_wise', $data);

  }
  //POLL TURNOUT STATE ENDS
    

 //POLL TURNOUT STATE EXCEL STARTS
  public function exempt_turnout_report_excel(Request $request){

    set_time_limit(6000);
    $data = $this->exempt_turnout_report($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['State Name','Total PS','Total Exempted PS','Round1 (Poll Start to 9:00 AM)','Round2 (Poll Start to 11:00 AM)','Round3 (Poll Start to 1:00 PM)','Round4 (Poll Start to 3:00 PM)','Round5 (Poll Start to 5:00 PM)','Latest Updated Poll'];

    foreach ($data['results'] as $lis) {

      $export_data[] = [

        $lis['label'],
        ($lis['total_ps'])?$lis['total_ps']:'0',
        ($lis['total_exempted_ps'])?$lis['total_exempted_ps']:'0',
        ($lis['round_1_total'])?$lis['round_1_total']:'0',
        ($lis['round_2_total'])?$lis['round_2_total']:'0',
        ($lis['round_3_total'])?$lis['round_3_total']:'0',
        ($lis['round_4_total'])?$lis['round_4_total']:'0',
        ($lis['round_5_total'])?$lis['round_5_total']:'0',
        ($lis['rounds_total'])?$lis['rounds_total']:'0',


      ];

    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
      $excel->sheet('Sheet1', function($sheet) use($export_data) {
        $sheet->mergeCells('A1:I1');
        $sheet->cell('A1', function($cell) {
          $cell->setAlignment('center');
          $cell->setFontWeight('bold');
        });
        $sheet->fromArray($export_data,null,'A1',false,false);
      });
    })->export('xls');

  }
 //POLL TURNOUT STATE EXCEL ENDS

  
 //POLL TURNOUT STATE PDF STARTS
  public function exempt_turnout_report_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->exempt_turnout_report($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.Reports.exempt.exempt_turnout_state_wise_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
 //POLL TURNOUT STATE PDF ENDS

 //POLL TURNOUT AC STARTS
  public function exempt_turnout_report_ac(Request $request){

   

    //CHECKING AUTH SESSION EXIST OR NOT STARTS
    if (empty(Auth::check())) {
      return Redirect('/')->with('error', 'You Are Not Logged In.');
    }

  //SETTING VARIABLES FOR SENDING RESULTS ON NEXT PAGE STARTS
    $data = [];
    $request_array = [];
    if($this->phase_no){
      $request_array[]  =  'phase_no='.$this->phase_no;
    }
    if($this->st_code){
     $request_array[]  =  'st_code='.$this->st_code;
   }
   if($this->dist_no){
    $request_array[]  =  'dist_no='.$this->dist_no;
  }
  if($this->ac_no){
    $request_array[]  =  'ac_no='.$this->ac_no;
  }

    //SETTING VARIABLES FOR SENDING RESULTS ON NEXT PAGE ENDS
  
  //CHECKING FOR USER TYPE AND SETTING VARIABLES FOR IT STARTS
  $this->action_state =Common::generate_url('booth-app-revamp/exempt-turnout-report/state');
  $this->action_ac =Common::generate_url('booth-app-revamp/exempt-turnout-report/state/ac');

  //GETTING LOGGED IN USERS DATA FROM AUTH 
  $data['user_data']  =   Auth::user();

    //SETTING TITILE OF THE PAGE 
  $title_array  = [];
  $data['heading_title']  = 'Booth App - Exempted PS Wise Turnout Report';

    //GET STATE NAME, DIST NAME AND AC NAMECODE STARTS
  if($this->st_code){
    $state_object = StateModel::get_state_by_code($this->st_code);
    if($state_object){
      $title_array[]  = "State: ".$state_object['ST_NAME'];
    }
  }
  if($this->dist_no){
    $dist_object = DistrictModel::get_district([
      'dist_no' => $this->dist_no,
      'st_code' => $this->st_code
    ]);
    if($dist_object){
      $title_array[]  = "District: ".$dist_object['dist_name'];
    }
  }
  if($this->ac_no){
    $ac_object = AcModel::get_ac([
      'ac_no' => $this->ac_no,
      'state' => $this->st_code
    ]);
    if($ac_object){
      $title_array[]  = "AC: ".$ac_object['ac_name'];
    }
  }
    //GET STATE NAME, DIST NAME AND AC NAME CODE ENDS

  $data['filter_buttons'] = $title_array;

    //LISTING ALL STATES FOR DATABASE RESULTS
  $states = StateModel::get_states();

  $data['states'] = [];

  foreach($states as $result){

        //FOR CEO 
        if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' => $result->ST_NAME,
          ];
        }
  
        //FOR ECI
        if(Auth::user()->role_id == '7'){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name'    => $result->ST_NAME,
          ];
        }

        //FOR DISTRICT NODAL OFFICER 
        if(Auth::user()->role_id == '5' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' =>    $result->ST_NAME,
          ];
        }

        //FOR RO OFFICER 
        if(Auth::user()->role_id == '19' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' =>    $result->ST_NAME,
          ];
        }


     }

  $data['filter']   = implode('&', array_merge($request_array));

     //SETTING BUTTONS FOR REPORTS STARTS
  $data['buttons']    = [];
  $data['buttons'][]  = [
    'name' => 'Export Excel',
    'href' =>  url($this->action_ac.'/excel').'?'.implode('&', $request_array),
    'target' => true
  ];
  $data['buttons'][]  = [
    'name' => 'Export Pdf',
    'href' =>  url($this->action_ac.'/pdf').'?'.implode('&', $request_array),
    'target' => true
  ];
    //SETTING BUTTONS FOR REPORTS ENDS

  $data['action']         = url($this->action_state);

  $results                = [];

  $acwise_results = [];
  $results                = [];
  $results['grand_percentage'] = [];
  $results['poll_turnout_percentage'] = [];
    //STATE LOOP STARTS
    foreach ($data['states'] as $state_result) {

    //STATE NAME
    if($state_result){
        $state_name = $state_result['name'];
    }

  $pollingModel  = new ExemptPollingController($request);   
  $filter = [
    'st_code'  => $this->st_code,
    'phase_no' => $this->phase_no,
    'ac_no'    => $this->ac_no,
    'dist_no'  => $this->dist_no,
  ]; 
  $poll_turnout =   $pollingModel->exempt_turnout_ac_wise($request, $filter);

  $data['poll_turnout_percentage'] = $poll_turnout['poll_turnout_percentage'];
  $data['grand_percentage'] = $poll_turnout['grand_percentage'];

  foreach ($poll_turnout['voter_turnouts'] as $pto_data) {

     $individual_filter_array          = [];
          
     $individual_filter_array['st_code'] = 'st_code='.$state_result['st_code'];

    if($this->dist_no){
      $individual_filter_array['dist_no'] = 'dist_no='.$this->dist_no;
    }
    if($this->ac_no){
      $individual_filter_array['ac_no'] = 'ac_no='.$this->ac_no;
    }else{
      $individual_filter_array['ac_no'] = 'ac_no='.$pto_data['ac_no'];
    }
    if($this->phase_no){
      $individual_filter_array['phase_no'] = 'phase_no='.$this->phase_no;
    }
    $individual_filter                = implode('&', $individual_filter_array);

    if($pto_data['st_code'] == '' && $pto_data['ac_no']==''){
       $href= '';
    }else{
      $href= url($this->action_ac)."/ps?".$individual_filter;
    }

      $acwise_results[] = [
                        'label'          => $pto_data['name'],
                        'st_code'        => $pto_data['st_code'],
                        'const_no'       => $pto_data['ac_no'],
                        'is_state'       => 0,
                        'total_ps'       => $pto_data['total_ps'],
                        'total_exempted_ps' => $pto_data['total_exempted_ps'],
                        'round_1_total'  => $pto_data['round_1_total'],
                        'round_2_total'  => $pto_data['round_2_total'],
                        'round_3_total'  => $pto_data['round_3_total'],
                        'round_4_total'  => $pto_data['round_4_total'],
                        'round_5_total'  => $pto_data['round_5_total'],
                        'round_5_total'  => $pto_data['round_5_total'],
                        'rounds_total'   => $pto_data['round_1_total']+$pto_data['round_2_total']+$pto_data['round_3_total']+$pto_data['round_4_total']+$pto_data['round_5_total'],
                        'male'           => $pto_data['male'],
                        'female'         => $pto_data['female'],
                        'other'          => $pto_data['other'],
                        'total'          => $pto_data['total'],
                        'e_male'         => $pto_data['e_male'],
                        'e_female'       => $pto_data['e_female'],
                        'e_other'        => $pto_data['e_other'],
                        'e_total'        => $pto_data['e_total'],
                        'total_in_queue' => $pto_data['total_in_queue'],
                        'percentage'     => $pto_data['percentage'],
                        "href"           => $href,
                      ]; 

   }
  }//STATE LOOP ENDS
  $data['results']  =   $acwise_results;

  if($request->has('is_excel')){
    if(isset($title_array) && count($title_array)>0){
      $data['heading_title'] .= "- ".implode(', ', $title_array);
    }
    return $data;
  }


    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/exempt-turnout-report/state/ac");

    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'dist_no'     => false,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation'     => false,
    ];

    $form_filters = Common::get_form_filters($form_filter_array, $request);      
    $data['form_filters'] = $form_filters;

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }
 
    return view($this->view.'.Reports.exempt.exempt_turnout_ac_wise', $data);

  }
 //POLL TURNOUT AC ENDS

 //POLL TURNOUT AC EXCEL STARTS
  public function exempt_turnout_report_ac_excel(Request $request){

    set_time_limit(6000);
    $data = $this->exempt_turnout_report_ac($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['AC No & Name','Total PS','Total Exempted PS','Round1 (Poll Start to 9:00 AM)','Round2 (Poll Start to 11:00 AM)','Round3 (Poll Start to 1:00 PM)','Round4 (Poll Start to 3:00 PM)','Round5 (Poll Start to 5:00 PM)','Latest Updated Poll'];

    foreach ($data['results'] as $lis) {

      $export_data[] = [

        $lis['label'],
        ($lis['total_ps'])?$lis['total_ps']:'0',
        ($lis['total_exempted_ps'])?$lis['total_exempted_ps']:'0',
        ($lis['round_1_total'])?$lis['round_1_total']:'0',
        ($lis['round_2_total'])?$lis['round_2_total']:'0',
        ($lis['round_3_total'])?$lis['round_3_total']:'0',
        ($lis['round_4_total'])?$lis['round_4_total']:'0',
        ($lis['round_5_total'])?$lis['round_5_total']:'0',
        ($lis['rounds_total'])?$lis['rounds_total']:'0',


      ];

    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
      $excel->sheet('Sheet1', function($sheet) use($export_data) {
        $sheet->mergeCells('A1:I1');
        $sheet->cell('A1', function($cell) {
          $cell->setAlignment('center');
          $cell->setFontWeight('bold');
        });
        $sheet->fromArray($export_data,null,'A1',false,false);
      });
    })->export('xls');

  }
 //POLL TURNOUT AC EXCEL ENDS

  
//POLL TURNOUT STATE PDF STARTS
  public function exempt_turnout_report_ac_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->exempt_turnout_report_ac($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.Reports.exempt.exempt_turnout_ac_wise_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  //POLL TURNOUT AC PDF  ENDS

  //POLL TURNOUT PS STARTS
  public function exempt_turnout_report_ps(Request $request){

    //CHECKING AUTH SESSION EXIST OR NOT STARTS
    if (empty(Auth::check())) {
      return Redirect('/')->with('error', 'You Are Not Logged In.');
    }
  //CHECKING AUTH SESSION EXIST OR NOT ENDS


  //SETTING VARIABLES FOR SENDING RESULTS ON NEXT PAGE STARTS
    $data = [];
    $request_array = [];
    if($this->phase_no){
      $request_array[]  =  'phase_no='.$this->phase_no;
    }
    if($this->st_code){
     $request_array[]  =  'st_code='.$this->st_code;
   }
   if($this->dist_no){
    $request_array[]  =  'dist_no='.$this->dist_no;
  }
  if($this->ac_no){
    $request_array[]  =  'ac_no='.$this->ac_no;
  }
  if($this->ps_no){
    $request_array[]  =  'ps_no='.$this->ps_no;
  }

    //SETTING VARIABLES FOR SENDING RESULTS ON NEXT PAGE ENDS
  
  //CHECKING FOR USER TYPE AND SETTING VARIABLES FOR IT STARTS
  $this->action_state =Common::generate_url('booth-app-revamp/exempt-turnout-report/state');
  $this->action_ac =Common::generate_url('booth-app-revamp/exempt-turnout-report/state/ac/ps');

  //GETTING LOGGED IN USERS DATA FROM AUTH 
  $data['user_data']  =   Auth::user();

    //SETTING TITILE OF THE PAGE 
  $title_array  = [];
  $data['heading_title']  = 'Booth App - Exempted PS Wise Turnout Report';

    //GET STATE NAME, DIST NAME AND AC NAMECODE STARTS
  if($this->st_code){
    $state_object = StateModel::get_state_by_code($this->st_code);
    if($state_object){
      $title_array[]  = "State: ".$state_object['ST_NAME'];
    }
  }
  if($this->dist_no){
    $dist_object = DistrictModel::get_district([
      'dist_no' => $this->dist_no,
      'st_code' => $this->st_code
    ]);
    if($dist_object){
      $title_array[]  = "District: ".$dist_object['dist_name'];
    }
  }
  if($this->ac_no){
    $ac_object = AcModel::get_ac([
      'ac_no' => $this->ac_no,
      'state' => $this->st_code
    ]);
    if($ac_object){
      $title_array[]  = "AC: ".$ac_object['ac_name'];
    }
  }
    //GET STATE NAME, DIST NAME AND AC NAME CODE ENDS

  $data['filter_buttons'] = $title_array;

    //LISTING ALL STATES FOR DATABASE RESULTS
  $states = StateModel::get_states();

  $data['states'] = [];

  foreach($states as $result){

        //FOR CEO 
        if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' => $result->ST_NAME,
          ];
        }
  
        //FOR ECI
        if(Auth::user()->role_id == '7'){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name'    => $result->ST_NAME,
          ];
        }

        //FOR DISTRICT NODAL OFFICER 
        if(Auth::user()->role_id == '5' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' =>    $result->ST_NAME,
          ];
        }

        //FOR RO OFFICER 
        if(Auth::user()->role_id == '19' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' =>    $result->ST_NAME,
          ];
        }
        
     }

  $data['filter']   = implode('&', array_merge($request_array));

     //SETTING BUTTONS FOR REPORTS STARTS
  $data['buttons']    = [];
  $data['buttons'][]  = [
    'name' => 'Export Excel',
    'href' =>  url($this->action_ac.'/excel').'?'.implode('&', $request_array),
    'target' => true
  ];
  $data['buttons'][]  = [
    'name' => 'Export Pdf',
    'href' =>  url($this->action_ac.'/pdf').'?'.implode('&', $request_array),
    'target' => true
  ];
    //SETTING BUTTONS FOR REPORTS ENDS

  $data['action']         = url($this->action_state);

  $results                = [];

  $pswise_results = [];
  $results                = [];
  $results['grand_percentage'] = [];
  $results['poll_turnout_percentage'] = [];
    //STATE LOOP STARTS
    foreach ($data['states'] as $state_result) {

    //STATE NAME
    if($state_result){
        $state_name = $state_result['name'];
    }

  $pollingModel  = new ExemptPollingController($request);   
  $filter = [
    'st_code'  => $this->st_code,
    'phase_no' => $this->phase_no,
    'ac_no'    => $this->ac_no,
    'ps_no'    => $this->ps_no,
    'exempt'   => 1,
  ];  
  $poll_turnout =   $pollingModel->exempt_turnout_ps_wise($request, $filter);

  $data['poll_turnout_percentage'] = $poll_turnout['poll_turnout_percentage'];
  $data['grand_percentage'] = $poll_turnout['grand_percentage'];

  foreach ($poll_turnout['voter_turnouts'] as $pto_data) {

     $individual_filter_array          = [];
          
     $individual_filter_array['st_code'] = 'st_code='.$state_result['st_code'];

    if($this->dist_no){
      $individual_filter_array['dist_no'] = 'dist_no='.$this->dist_no;
    }
    if($this->ac_no){
      $individual_filter_array['ac_no'] = 'ac_no='.$this->ac_no;
    }
    if($this->phase_no){
      $individual_filter_array['phase_no'] = 'phase_no='.$this->phase_no;
    }
    if($this->ps_no){
      $individual_filter_array['ps_no'] = 'ps_no='.$this->ps_no;
    }else{ $individual_filter_array['ps_no'] = 'ps_no='.$pto_data['ps_no']; }
    $individual_filter                = implode('&', $individual_filter_array);

    if($pto_data['ps_no']==''){
       $href= '';
    }else{
      $href= url($this->action_ac)."?".$individual_filter;
    }

      $pswise_results[] = [
                        'label'          => $pto_data['ps_name'],
                        'ps_name_and_no' => $pto_data['ps_name_and_no'],
                        'ps_no'          => $pto_data['ps_no'],
                        'is_state'       => 0,
                        'round_1_total'  => $pto_data['round_1_total'],
                        'round_2_total'  => $pto_data['round_2_total'],
                        'round_3_total'  => $pto_data['round_3_total'],
                        'round_4_total'  => $pto_data['round_4_total'],
                        'round_5_total'  => $pto_data['round_5_total'],
                        'round_5_total'  => $pto_data['round_5_total'],
                        'rounds_total'   => $pto_data['round_1_total']+$pto_data['round_2_total']+$pto_data['round_3_total']+$pto_data['round_4_total']+$pto_data['round_5_total'],
                        'male'           => $pto_data['male'],
                        'female'         => $pto_data['female'],
                        'other'          => $pto_data['other'],
                        'total'          => $pto_data['total'],
                        'e_male'         => $pto_data['e_male'],
                        'e_female'       => $pto_data['e_female'],
                        'e_other'        => $pto_data['e_other'],
                        'e_total'        => $pto_data['e_total'],
                        'total_in_queue' => $pto_data['total_in_queue'],
                        'percentage'     => $pto_data['percentage'],
                        "href"           => $href,
                      ]; 

   }
  }//STATE LOOP ENDS
  $data['results']  =   $pswise_results;

  if($request->has('is_excel')){
    if(isset($title_array) && count($title_array)>0){
      $data['heading_title'] .= "- ".implode(', ', $title_array);
    }
    return $data;
  }


    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/exempt-turnout-report/state/ac/ps");

    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'dist_no'     => false,
      'ac_no'       => true, 
      'ps_no'       => true, 
      'designation'     => false,
    ];

    $form_filters = Common::get_form_filters($form_filter_array, $request);      
    $data['form_filters'] = $form_filters;

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }
 
    return view($this->view.'.Reports.exempt.exempt_turnout_ps_wise', $data);

  }
 //POLL TURNOUT PS ENDS

 //POLL TURNOUT PS EXCEL STARTS
  public function exempt_turnout_report_ps_excel(Request $request){

    set_time_limit(6000);
    $data = $this->exempt_turnout_report_ps($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['PS No & Name','Round1 (Poll Start to 9:00 AM)','Round2 (Poll Start to 11:00 AM)','Round3 (Poll Start to 1:00 PM)','Round4 (Poll Start to 3:00 PM)','Round5 (Poll Start to 5:00 PM)','Latest Updated Poll'];

    foreach ($data['results'] as $lis) {

      $export_data[] = [

        $lis['ps_name_and_no'],
        ($lis['round_1_total'])?$lis['round_1_total']:'0',
        ($lis['round_2_total'])?$lis['round_2_total']:'0',
        ($lis['round_3_total'])?$lis['round_3_total']:'0',
        ($lis['round_4_total'])?$lis['round_4_total']:'0',
        ($lis['round_5_total'])?$lis['round_5_total']:'0',
        ($lis['rounds_total'])?$lis['rounds_total']:'0',


      ];

    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
      $excel->sheet('Sheet1', function($sheet) use($export_data) {
        $sheet->mergeCells('A1:G1');
        $sheet->cell('A1', function($cell) {
          $cell->setAlignment('center');
          $cell->setFontWeight('bold');
        });
        $sheet->fromArray($export_data,null,'A1',false,false);
      });
    })->export('xls');

  }
 //POLL TURNOUT PS EXCEL ENDS

  
//POLL TURNOUT PS PDF STARTS
  public function exempt_turnout_report_ps_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->exempt_turnout_report_ps($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.Reports.exempt.exempt_turnout_ps_wise_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  //POLL TURNOUT PS PDF  ENDS


}  // end class