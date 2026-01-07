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
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, TblPollSummaryModel, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, JsonFile, OfficerAssignmentModel,OfficerAssignmentPsModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;
use Excel;
use App\Http\Controllers\Admin\BoothAppRevamp\PollingController;
use App\models\Admin\PhaseModel;
//current

class BoarPswiseController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $ps_no         = NULL;
  public $role_id       = 0;
  public $restricted_ps = [];
  public $allowed_acs = [];
  public $allowed_dist_no = [];
  public $allowed_st_code = [];
  public $base          = 'ro';
  public $action_state  = 'eci/booth-app-revamp/officer-assignment-ps-wise-report/state';
  public $action_ac     = 'eci/booth-app-revamp/officer-assignment-ps-wise-report/state/ac';
  public $view_path     = "admin.booth-app-revamp";

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


  //officer-assignment-ps-wise-report function ends
  public function officer_assignment_ps_wise(Request $request){

    //CHECKING AUTH SESSION EXIST OR NOT STARTS
  if (empty(Auth::check())) {
    return Redirect('/')->with('error', 'You Are Not Logged In.');
  }
  //CHECKING AUTH SESSION EXIST OR NOT ENDS

  $allowed_st_code = implode(', ', $this->allowed_st_code);
  $allowed_acs = implode(', ', $this->allowed_acs);

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
  $this->action_state =Common::generate_url('booth-app-revamp/officer-assignment-ps-wise-report');
  $this->action_ac =Common::generate_url('booth-app-revamp/officer-assignment-ps-wise-report/state/ac');

  //GETTING LOGGED IN USERS DATA FROM AUTH 
    $data['user_data']  =   Auth::user();
 
    //SETTING TITILE OF THE PAGE 
    $title_array  = [];
    $data['heading_title']  = 'Booth App - Officer Assignment Report';

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
   
   $statewise_results = [];
    
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

    
      //PO AND PRO LOOP STARTS
      $ps    =   PollingStation::get_polling_stations_count_ps_wise($filter_election);
   
         foreach ($ps as $ps_data) {

            $state_name = '';
            //PS COUNT
            $filter_ac = [
              'phase_no'=> $this->phase_no,
              'st_code' => $this->st_code,
              'dist_no' => $this->dist_no,
              'ac_no'   => $this->ac_no,
              'ps_no'   => $ps_data['PS_NO'],
            ];
            
 
            $po_pro = OfficerAssignmentPsModel::ps_po_pro_ac($filter_ac);
      
            $blo   = OfficerAssignmentPsModel::ps_blo_ac($filter_ac);

            $sm    = OfficerAssignmentPsModel::ps_sm_ac($filter_ac);
            
            if(!empty($blo)){
              $blo_count = $blo->total_blo;
            }else {$blo_count = 0;}

            if(!empty($sm)){
              $sm_count = $sm->total_sm;
            }else {$sm_count = 0;}

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

          //PS LOOP STARTS
      //foreach ($po_pro as $po_pro_data) {

            //SETTING DATABASE RESULTS FOR 
              $statewise_results[] = [
                        'label'        => $state_result['name'],
                        'st_code'      => $state_result['st_code'],
                        'is_state'     => 0,
                        'const_no'     => $po_pro->ac_no,
                        'const_name'   => $po_pro->ac_name,
                        'ps_no'        => $po_pro->ps_no,
                        'ps_name'      => $po_pro->ps_name,
                        'total_ps'     => $ps_data['total_ps'],
                        'total_pro'    => $po_pro->total_pro,
                        'total_po'     => $po_pro->total_po,
                        'total_blo'    => $blo_count,
                        'total_sm'     => $sm_count,
                        "href"         => url($this->action_ac)."?".$individual_filter,
                        'hrefps'       => Common::generate_url('booth-app-revamp/officer-assignment-ps-wise-report').'?st_code='.$state_result['st_code'].'&ac_no='.$po_pro->ac_no.'&phase_no='.@$this->phase_no
                      ]; 

         // }

            }//PO AND PRO LOOP ENDS

    }//STATE LOOP ENDS


    $results          = $statewise_results;
    $data['results']  =   $results;

    if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
     }


    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/officer-assignment-report");
    
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
    

    
  return view($this->view.'.Reports.officer-assignment-ps-wise-report', $data);

  }
  //officer-assignment-ps-wise-report function starts

   //officer-assignment-ps-wise-report excel function starts
    public function officer_assignment_ps_wise_excel(Request $request){

    set_time_limit(6000);
    $data = $this->officer_assignment_ps_wise($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['State/UT Name','CONST No','CONST Name','Total PS','BLO Assigned','PO Assigned','PRO Assigned','SM Assigned'];

    $TotalPs = '0';
    $TotalPs = '0';
    $TotalBlo = '0';
    $TotalPo = '0';
    $TotalPro = '0';
    $TotalSm = '0';
 
    foreach ($data['results'] as $lis) {

            $export_data[] = [
                                  
                                  $lis['label'],
                                  $lis['const_no'],
                                  $lis['const_name'],
                                  ($lis['total_ps'])?$lis['total_ps']:'0',
                                  ($lis['total_blo'])?$lis['total_blo']:'0',
                                  ($lis['total_po'])?$lis['total_po']:'0',
                                  ($lis['total_pro'])?$lis['total_pro']:'0',
                                  ($lis['total_sm'])?$lis['total_sm']:'0',
                                  
                            ];

            $TotalPs       +=   $lis['total_ps'];
            $TotalBlo      +=   $lis['total_blo'];
            $TotalPo       +=   $lis['total_po'];
            $TotalPro      +=   $lis['total_pro'];
            $TotalSm       +=   $lis['total_sm'];
    }

    $totalvalues = array('Total','','',$TotalPs,$TotalBlo,$TotalPo,$TotalPro,$TotalSm);
    array_push($export_data,$totalvalues);

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
  //officer-assignment-ps-wise-report excel function ends

  
  //officer-assignment-ps-wise-report pdf function ends
  public function officer_assignment_ps_wise_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->officer_assignment_ps_wise($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.Reports.officer-assignment-ps-wise-report-pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  //officer-assignment-ps-wise-report pdf function ends
	
 
}  // end class