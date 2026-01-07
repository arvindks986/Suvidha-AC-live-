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
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, TblPollSummaryModel, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, JsonFile, TblPwdVoterModel, OfficerAssignmentPsModel,TblProDiaryModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;

//current

class BevmCompareController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $filter_role_id  = NULL;
  public $is_activated    = NULL;
  public $base            = 'roac';
  public $restricted_ps   = [];
  public $allowed_acs = [];
  public $allowed_dist_no = [];
  public $allowed_st_code = [];
  public $cache = true;
  public $action_state  = 'eci/booth-app-revamp/evm-comparision/state';
  public $action_ac     = 'eci/booth-app-revamp/evm-comparision/state/ac';

  public function __construct(Request $request){
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      if(in_array(Auth::user()->st_code,$this->allowed_st_code) && in_array(Auth::user()->ac_no,$this->allowed_acs) && in_array(Auth::user()->dist_no,$this->allowed_dist_no)){

      }
      $default_values = Common::get_request_filter($request);
      $this->ac_no    = $default_values['ac_no'];
      $this->st_code  = $default_values['st_code'];
      $this->ps_no    = $default_values['ps_no'];
      $this->phase_no    = $default_values['phase_no'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id  = $default_values['role_id'];
      $this->base     = $default_values['base'];

      $object_setting         = Common::get_allowed_acs($request);
      $this->allowed_st_code  = $object_setting['allowed_st_code'];
      $this->allowed_dist_no  = $object_setting['allowed_dist_no'];
      $this->allowed_acs      = $object_setting['allowed_acs'];

      return $next($request);
    });
  }
  
  //evm_comparision_state_report 
  public function evm_comparision_state_report(Request $request){
    $data                   = [];
    $data['results'] = [];
    $title_array  = [];
    $request_array = [];
    $data['user_data']  =   Auth::user();

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

  $filter = [
    'phase_no'         => $this->phase_no,
    'st_code'         => $this->st_code,
    'allowed_st_code' => $this->allowed_st_code
  ];

  $this->action_state =Common::generate_url('booth-app-revamp/evm-comparision/state');
  $this->action_ac =Common::generate_url('booth-app-revamp/evm-comparision/state/ac');

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

    //SETTING TITILE OF THE PAGE 
  $data['heading_title']  = 'Booth App - EVM Comparison Report';
  $data['filter_buttons'] = $title_array;
  $data['filter']         = implode('&', array_merge($request_array));
  $data['action']         = url($this->action_state);

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

  $states_results = StateModel::get_states($filter);
  foreach ($states_results as $key => $iterate_state) {

    $filter_for_voters = [
      'st_code' => $iterate_state->ST_CODE,
      'phase_no' => $this->phase_no	
    ];

    $po_sum = TblProDiaryModel::get_po_sum($filter_for_voters);
	$evm_sum = TblProDiaryModel::get_evm_sum($filter_for_voters);

    $individual_filter_array          = [];

    $individual_filter_array['st_code'] = 'st_code='.$iterate_state['ST_CODE'];

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

    if($iterate_state['ST_CODE'] == ''){
      $href= '';
    }else {
      $href=url($this->action_ac)."?".$individual_filter;
    }

    $data['results'][] = [
      'st_name'         => $iterate_state['ST_NAME'],
      'no_of_vote'      => (int)$po_sum['no_of_vote'],
      'no_of_vote_evm'  => (int)$evm_sum['no_of_vote_evm'],
      'href'             => $href,
    ];

  }



    //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/evm-comparision/state");
  
  $form_filter_array = [
    'phase_no'     => true,
    'st_code'     => true,
    'ac_no'       => false,
    'ps_no'       => false,
    'designation' => false,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);
  $data['form_filters'] = $form_filters;
  $data['heading_title_with_all'] = 'Booth App - EVM Comparison Report';
  $data['drill_level'] = 1;

  if($request->has('is_excel')){
    if(isset($title_array) && count($title_array)>0){
      $data['heading_title'] .= "- ".implode(', ', $title_array);
    }
    return $data;
  }

  return view($this->view.'.Reports.evm-comparision-state', $data);

}
  //evm_comparision_state_report ends

  //evm_comparision_state_report excel function starts
public function evm_comparision_state_report_excel(Request $request){

  set_time_limit(6000);
  $data = $this->evm_comparision_state_report($request->merge(['is_excel' => 1]));
  $export_data = [];
  $export_data[] = [$data['heading_title']];

  $export_data[] = ['State/UT Name','Total PO','Total EVM Votes'];

  $total_no_of_vote = '0';
  $total_no_of_vote_evm = '0';

  foreach ($data['results'] as $lis) {

    $export_data[] = [

      $lis['st_name'],
      ($lis['no_of_vote'])?$lis['no_of_vote']:'0',
      ($lis['no_of_vote_evm'])?$lis['no_of_vote_evm']:'0',
      
    ];

    $total_no_of_vote +=   $lis['no_of_vote'];
    $total_no_of_vote_evm +=   $lis['no_of_vote_evm'];
    

  }

  $totalvalues = array('Total',$total_no_of_vote,$total_no_of_vote_evm);
  array_push($export_data,$totalvalues);

  $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

  \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    $excel->sheet('Sheet1', function($sheet) use($export_data) {
      $sheet->mergeCells('A1:C1');
      $sheet->cell('A1', function($cell) {
        $cell->setAlignment('center');
        $cell->setFontWeight('bold');
      });
      $sheet->fromArray($export_data,null,'A1',false,false);
    });
  })->export('xls');

}
  //evm_comparision_state_report excel function ends

  //evm_comparision_state_report pdf function ends
public function evm_comparision_state_report_pdf(Request $request){
  set_time_limit(6000);
  $data = $this->evm_comparision_state_report($request->merge(['is_excel' => 1]));
  $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
  $pdf = \PDF::loadView($this->view.'.Reports.evm-comparision-state-pdf',$data);
  return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
}
  //evm_comparision_state_report pdf function ends

  //evm_comparision_ac_report starts
public function evm_comparision_ac_report(Request $request){
  $data                   = [];
  $data['results'] = [];

  $request_array = [];
  $data['user_data']  =   Auth::user();

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

$filter = [
  'phase_no'         => $this->phase_no,
  'st_code'         => $this->st_code,
  'allowed_st_code' => $this->allowed_st_code
];

$this->action_state =Common::generate_url('booth-app-revamp/evm-comparision/state');
$this->action_ac =Common::generate_url('booth-app-revamp/evm-comparision/state/ac');

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

    //SETTING TITILE OF THE PAGE 
$data['heading_title']  = 'Booth App - EVM Comparison Report';
$data['filter_buttons'] = $title_array;
$data['filter']   = implode('&', array_merge($request_array));
$data['action']         = url($this->action_ac);


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

$filter_for_voters = [
  'phase_no' => $this->phase_no,
  'st_code' => $this->st_code,
  'dist_no' => $this->dist_no,
  'ac_no' => $this->ac_no,
];

    //STATE LOOP STARTS
foreach ($data['states'] as $state_result) {


    //STATE NAME
  if($state_result){
    $state_name = $state_result['name'];
  }

  $filter_election = [
    'phase_no' => $this->phase_no,
    'st_code' => $this->st_code,
    'dist_no' => $this->dist_no,
    'ac_no' => $this->ac_no,
  ];


      //AC LOOP STARTS
  $acs_results = AcModel::get_acs($filter_election);

  foreach ($acs_results as $key => $iterate_ac) {

    $state_name = '';
            //PS COUNT
    $filter_for_voters = [
      'phase_no' => $this->phase_no,
      'st_code' => $this->st_code,
      'dist_no' => $this->dist_no,
      'ac_no' => $iterate_ac['ac_no'],
    ];
	$po_sum = TblProDiaryModel::get_po_sum($filter_for_voters);
	$evm_sum = TblProDiaryModel::get_evm_sum($filter_for_voters);
    $st_name = '';
    $state_object = StateModel::get_state_by_code($iterate_ac['st_code']);
    if($state_object){
      $st_name = $state_object['ST_NAME'];
    }

    $individual_filter_array          = [];
    $individual_filter_array['st_code'] = 'st_code='.$state_result['st_code'];

    if($this->dist_no){
      $individual_filter_array['dist_no'] = 'dist_no='.$this->dist_no;
    }
    if($this->ac_no){
      $individual_filter_array['ac_no'] = 'ac_no='.$this->ac_no;
    }else{
      $individual_filter_array['ac_no'] = 'ac_no='.$iterate_ac['ac_no'];
    }
    if($this->phase_no){
      $individual_filter_array['phase_no'] = 'phase_no='.$this->phase_no;
    }
    $individual_filter                = implode('&', $individual_filter_array);

            //SETTING DATABASE RESULTS FOR 
    $data['results'][] = [
      'st_name'         => $st_name,
      'const_no'        => $iterate_ac['ac_no'],
      'const_name'      => $iterate_ac['ac_name'],
      'no_of_vote'      => $po_sum['no_of_vote'],
      'no_of_vote_evm'  => $evm_sum['no_of_vote_evm'],
      'href'            => url($this->action_ac)."/ps?".$individual_filter,
    ]; 

            }//AC LOOP ENDS

    }//STATE LOOP ENDS

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/evm-comparision/state/ac");
    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'ac_no'       => true,
      'ps_no'       => false,
      'designation' => false,
    ];

    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters'] = $form_filters;
    $data['user_data']  =   Auth::user();
    $data['heading_title_with_all'] = 'EVM Comparison Report';
    $data['drill_level'] = 2;

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view.'.Reports.evm-comparision-ac', $data);
  }
  //evm_comparision_ac_report ends

  //evm_comparision_ac_report excel function starts
  public function evm_comparision_ac_report_excel(Request $request){

    set_time_limit(6000);
    $data = $this->evm_comparision_ac_report($request->merge(['is_excel' => 1]));
    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['State/UT Name','Const No','Const Name','Total PO','Total EVM Votes'];

    $total_no_of_vote = '0';
    $total_no_of_vote_evm = '0';

    foreach ($data['results'] as $lis) {

      $export_data[] = [

        $lis['st_name'],
        $lis['const_no'],
        $lis['const_name'],
        ($lis['no_of_vote'])?$lis['no_of_vote']:'0',
        ($lis['no_of_vote_evm'])?$lis['no_of_vote_evm']:'0',
        
      ];

      $total_no_of_vote +=   $lis['no_of_vote'];
      $total_no_of_vote_evm +=   $lis['no_of_vote_evm'];
      

    }

    $totalvalues = array('Total',$total_no_of_vote,$total_no_of_vote_evm);
    array_push($export_data,$totalvalues);

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
      $excel->sheet('Sheet1', function($sheet) use($export_data) {
        $sheet->mergeCells('A1:E1');
        $sheet->cell('A1', function($cell) {
          $cell->setAlignment('center');
          $cell->setFontWeight('bold');
        });
        $sheet->fromArray($export_data,null,'A1',false,false);
      });
    })->export('xls');

  }
  //evm_comparision_ac_report excel function ends

  //evm_comparision_ac_report pdf function ends
  public function evm_comparision_ac_report_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->evm_comparision_ac_report($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view.'.Reports.evm-comparision-ac-pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  //evm_comparision_ac_report pdf function ends

  
   //evm_comparision_ps_report starts
  public function evm_comparision_ps_report(Request $request){

    $data                   = [];
    $data['results'] = [];

    $request_array = [];
    $data['user_data']  =   Auth::user();

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

  $is_activated           = NULL;
  if($request->has('is_vote')){
    $is_activated = $request->is_vote;
    $request_array[] = 'is_vote='.$this->is_activated;
  }

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

    //SETTING TITILE OF THE PAGE 
  $this->action_state =Common::generate_url('booth-app-revamp/evm-comparision/state');
  $this->action_ac =Common::generate_url('booth-app-revamp/evm-comparision/state/ac');
  $data['heading_title']  = 'Booth App - EVM Comparison Report';
  $data['filter_buttons'] = $title_array;
  $data['filter']   = implode('&', array_merge($request_array));
  $data['action']         = url($this->action_ac);


    //buttons
  $data['buttons']    = [];
  //SETTING BUTTONS FOR REPORTS STARTS
  $data['buttons']    = [];
  $data['buttons'][]  = [
    'name' => 'Export Excel',
    'href' =>  url($this->action_ac.'/ps/excel').'?'.implode('&', $request_array),
    'target' => true
  ];
  $data['buttons'][]  = [
    'name' => 'Export Pdf',
    'href' =>  url($this->action_ac.'/ps/pdf').'?'.implode('&', $request_array),
    'target' => true
  ];
    //SETTING BUTTONS FOR REPORTS ENDS

  $filter = [
    'phase_no'        => $this->phase_no,
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
    'is_vote'        => $is_activated
  ];

  $polling_stations = PollingStation::get_polling_stations($filter);

    //$polling_stations = PollingStation::get_polling_stations($filter);

  foreach ($polling_stations as $key => $iterate_p_s) {
    $ps_no = $iterate_p_s['PS_NO'];
    $filter_for_voters = array_merge($filter,['ps_no' => $iterate_p_s['PS_NO']]);
	$po_sum = TblProDiaryModel::get_po_sum($filter_for_voters);
	$evm_sum = TblProDiaryModel::get_evm_sum($filter_for_voters);

	  //dd($filter_for_voters);

    $st_name = '';
    $state_object = StateModel::get_state_by_code($iterate_p_s['ST_CODE']);
    if($state_object){
      $st_name = $state_object['ST_NAME'];
    }

    $ac_name = '';
    $ac_object = AcModel::get_ac(['phase_no' => $this->phase_no,'state' => $iterate_p_s['ST_CODE'], 'ac_no' => $iterate_p_s['AC_NO']]);
    if($ac_object){
      $ac_name = $ac_object['ac_name'];
    }


    $data['results'][] = [
      'st_name'         => $st_name,
      'const_no'        => $iterate_p_s['AC_NO'],
      'const_name'      => $ac_name,
      'ps_no'           => $iterate_p_s['PS_NO'],
      'ps_name'         => $iterate_p_s['PS_NAME_EN'],
      'no_of_vote'      => $po_sum['no_of_vote'],
      'no_of_vote_evm'  => $evm_sum['no_of_vote_evm'],
      'href'      => 'javascript:void(0)'
    ];

  }


//form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/evm-comparision/state/ac/ps");

  $form_filter_array = [
    'phase_no'     => true,
    'st_code'     => true,
    'ac_no'       => true,
    'ps_no'       => true,
    'designation' => false
  ];

  $form_filters = Common::get_form_filters($form_filter_array, $request);

  $data['form_filters'] = $form_filters;
  $data['user_data']  =   Auth::user();
  $data['heading_title_with_all'] = 'EVM Comparison Report';
  $data['drill_level'] = 3;

  if($request->has('is_excel')){
    if(isset($title_array) && count($title_array)>0){
      $data['heading_title'] .= "- ".implode(', ', $title_array);
    }
    return $data;
  }

  return view($this->view.'.Reports.evm-comparision-ps', $data);
}
  //evm_comparision_ps_report ends


  //evm_comparision_ps_report excel function starts
public function evm_comparision_ps_report_excel(Request $request){

  set_time_limit(6000);
  $data = $this->evm_comparision_ps_report($request->merge(['is_excel' => 1]));
  
  $export_data = [];
  $export_data[] = [$data['heading_title']];

  $export_data[] = ['State/UT Name','Const No','Const Name','PS No','PS Name','Total PO','Total EVM Votes'];

  $total_no_of_vote = '0';
  $total_no_of_vote_evm = '0';

  foreach ($data['results'] as $lis) {

    $export_data[] = [

      $lis['st_name'],
      $lis['const_no'],
      $lis['const_name'],
      $lis['ps_no'],
      $lis['ps_name'],
      ($lis['no_of_vote'])?$lis['no_of_vote']:'0',
      ($lis['no_of_vote_evm'])?$lis['no_of_vote_evm']:'0',
      
    ];

    $total_no_of_vote +=   $lis['no_of_vote'];
    $total_no_of_vote_evm +=   $lis['no_of_vote_evm'];
    

  }

  $totalvalues = array('Total','','','','',$total_no_of_vote,$total_no_of_vote_evm);
  array_push($export_data,$totalvalues);

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
  //evm_comparision_ps_report excel function ends

  //evm_comparision_ps_report pdf function ends
public function evm_comparision_ps_report_pdf(Request $request){
  set_time_limit(6000);
  $data = $this->evm_comparision_ps_report($request->merge(['is_excel' => 1]));
  $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
  $pdf = \PDF::loadView($this->view.'.Reports.evm-comparision-ps-pdf',$data);
  return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
}
  //evm_comparision_ps_report pdf function ends


}  // end class