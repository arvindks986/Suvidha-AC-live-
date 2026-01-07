<?php
namespace App\Http\Controllers\Admin;
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
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

use App\models\Admin\ElectionScheduleModel;
  //INCLUDING CLASSES
use App\Classes\xssClean;
use App\Classes\secureCode;
use Common;

date_default_timezone_set('Asia/Kolkata');
    

class ElectionScheduleController extends Controller
{   

  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/ElectionScheduleState';
  public $action_ac     = 'eci/ElectionScheduleState/state';
  public $form_action   = 'eci/ElectionScheduleState';
  public $view_path     = "admin.election_schedule";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

    $this->commonModel  = new commonModel();
    $this->ECIModel = new ECIModel();

    $this->middleware(function ($request, $next) {
      if(Auth::user() && Auth::user()->role_id=='26'){
        $this->action_state  = str_replace('eci','eci-agent',$this->action_state);
        $this->action_ac     = str_replace('eci','eci-agent',$this->action_ac);

      }
      return $next($request);
    });
  }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
    */

    protected function guard(){
        return Auth::guard();
    }

    
    //AC ECI ELECTION SCHEDULE MAIN DATA REPORT STARTS
    public function ElectionScheduleState(Request $request){  
      //AC ECI ELECTION SCHEDULE MAIN DATA REPORT TRY CATCH BLOCK STARTS
       try{

        //CHECKING AUTH SESSION EXIST OR NOT STARTS
        if (empty(Auth::check())) {
          return Redirect('/')->with('error', 'You Are Not Logged In.');
        }
        //CHECKING AUTH SESSION EXIST OR NOT ENDS 

        //INPUT CLEANN CLASSES STARTS
        $xss    = new xssClean;
        $secure = new secureCode;
        //INPUT CLEANN CLASSES ENDS 

        //SETTING VARIABLES FOR SENDING RESULTS ON NEXT PAGE STARTS
        $data = [];
       
        //$default_phase = PhaseModel::get_current_phase();
        //$default_phase = 'all';

        $request_array = []; 
        $data['phases'] = PhaseModel::get_phases();
        $data['phase'] = NULL;
        if($request->has('phase')){
          if($request->phase != 'all'){
            $data['phase'] = $xss->clean_input($request->phase);
          }
          $request_array[] =  'phase='.$xss->clean_input($request->phase);;
        }/*else{
          $data['phase']    = $default_phase;
          $request_array[]  =  'phase='.$default_phase; 
        }*/
        
        //CHECKING IF THE REQUEST CONTAINS THE STATE DATA OR NOT
        $data['state'] = NULL;
        if($request->has('st_code')){
          $data['state'] = $request->st_code;
          $request_array[] = 'st_code='.$request->st_code;
        }

        if($data['state']=='')
        {
            if(Auth::user()->role_id == '4'){
             $data['state']=Auth::user()->st_code;
             }
        }

        //SETTING VARIABLES FOR SENDING RESULTS ON NEXT PAGE ENDS 

        
        //CHECKING FOR USER TYPE AND SETTING VARIABLES FOR IT STARTS
        if(Auth::user()->role_id == '4'){
          $this->action_state  = 'acceo/ElectionScheduleState';
          $this->action_pc     = 'acceo/ElectionScheduleState/state';
          $this->form_action   = 'acceo/ElectionScheduleState';
          
          //FORM NAME FOR NEXT PAGE
          $data['heading_title_form'] = 'List Of Election Schedule';
        }

        
        //GETTING LOGGED IN USERS DATA FROM AUTH 
        $data['user_data']  =   Auth::user();

        //SETTING TITILE OF THE PAGE 
        $title_array  = [];
        $data['heading_title']      = 'List Of Election Schedule';


        //GET STATE NAME BY STATE CODE STARTS
        if($data['state']){
          $state_object = StateModel::get_state_by_code($data['state']);
          if($state_object){
            $title_array[]  = "State: ".$state_object['ST_NAME'];
          }
        }
        //GET STATE NAME BY STATE CODE ENDS

        
        $data['filter_buttons'] = $title_array;

        $filter_for_state = [
          'phase' => $data['phase']
        ];

        
        //SETTING STATE CODE IF USER IS CEO
        if(Auth::user()->role_id == '4'){
          $data['state']  = Auth::user()->st_code;
        }

        
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
        $data['form_action']    = url($this->form_action);

        $results                = [];

        $filter_election = [
          'state'         => $data['state'],
          'phase'         => $data['phase'],
        ];
      
        //ECI
        if(Auth::user()->role_id == '7'){

             $filter_election = [
              'phase'     => $data['phase'],
              'state'     => $data['state'],
              'group_by'  => 'state',
              'order_by'  => 'state'
            ];
          }


        //STATE NODAL POLICE OFFICER
        if(Auth::user()->role_id == '4'){

           $filter_election = [
            'phase'     => $data['phase'],
            'state'     => Auth::user()->st_code,
            'group_by'  => 'state',
            'order_by'  => 'state'
          ];
        }


      $object   = ElectionScheduleModel::state_schedule($filter_election);

      foreach ($object as $result) {

            $individual_filter_array = [];
            $individual_filter_array['state'] = 'st_code='.$result['st_code'];
            $individual_filter_array['phase'] = 'phase='.$result['sid'];
            $individual_filter    = implode('&', $individual_filter_array);
                

            //checking dates for election events
            
            //START NOMINATION DATE DIFF
            $start_nomi_class   = ElectionScheduleModel::date_diff($result['start_nomi_date']);

            //LAST NOMINATION DATE DIFF
            $last_nomi_class   = ElectionScheduleModel::date_diff($result['last_nomi_date']);

            //SCRUTINY DATE DIFF
            $scr_date_class   = ElectionScheduleModel::date_diff($result['dt_nomi_scr']);
            
            //LAST WIDRAWL DATE DIFF
            $wid_date_class   = ElectionScheduleModel::date_diff($result['last_wid_date']);

            //POLL DATE DIFF
            $poll_date_class   = ElectionScheduleModel::date_diff($result['poll_date']);

            //COUNT DATE DIFF
            $count_date_class   = ElectionScheduleModel::date_diff($result['count_date']);

            //COMPLETE DATE DIFF
            $comp_date_class   = ElectionScheduleModel::date_diff($result['complete_date']);
                

                $results[] = [
                  'label'                    => $result['state'],
                  'st_code'                  => $result['st_code'],
                  'sid'                      => $result['sid'],
                  'acs'                      => $result['acs'],
                  'start_nomi_class'         => $start_nomi_class,
                  'start_nomi_date'          => $result['start_nomi_date'],
                  'last_nomi_class'          => $last_nomi_class,
                  'last_nomi_date'           => $result['last_nomi_date'],
                  'nomi_scr_class'           => $scr_date_class,
                  'dt_nomi_scr'              => $result['dt_nomi_scr'],
                  'last_wid_class'           => $wid_date_class,
                  'last_wid_date'            => $result['last_wid_date'],
                  'poll_date_class'          => $poll_date_class,
                  'poll_date'                => $result['poll_date'],
                  'count_date_class'         => $count_date_class,
                  'count_date'               => $result['count_date'],
                  'complete_date_class'      => $comp_date_class,
                  'complete_date'            => $result['complete_date'],
                  'href'                     => url($this->action_ac)."?".$individual_filter
                ];      

            } 


     $data['filter_action'] = Common::generate_url("booth-app-revamp/officers");
     $form_filter_array = [
       'st_code' => true,
        'dist_no' => true,
        'ac_no' => true,
        'ps_no' => false,
        'designation' => true,
       /*'allowed_acs'     => $this->allowed_acs,
       'allowed_st_code' => $this->allowed_st_code,
       'allowed_dist_no' => $this->allowed_dist_no,*/
     ];
     if($request->has('st_code')){
            $data['st_code'] = $request->st_code;
      }elseif(!empty($user_data->st_code)){
        $data['st_code'] = $user_data->st_code;
      }else{
        $data['st_code'] = '';
      }
    
     $form_filters = Common::get_form_filters($form_filter_array, $request);
     $data['form_filters'] = $form_filters;


    $data['results']   = $results;

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path.'.ElectionScheduleState', $data);
      //  return view('admin.pc.policediary.weapon_details', ['user_data' => $user]);       
               
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //AC ECI ELECTION SCHEDULE MAIN DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //AC ECI ELECTION SCHEDULE MAIN DATA REPORT FUNCTION ENDS


     //AC ECI ELECTION SCHEDULE MAIN DATA excel function starts
      public function ElectionScheduleStateExcel(Request $request){

        set_time_limit(6000);
        $data = $this->ElectionScheduleState($request->merge(['is_excel' => 1]));

        $export_data = [];
        $export_data[] = [$data['heading_title']];

        $export_data[] = ['State','Poll Events (Phase)','Total ACs in Phase','Date of Issue of Gazette Notification','Last Date For Making Nominations','Date for Scrutiny of Nominations','Last Date For Withdrawl of Candidature','Date Of Poll','Date Of Counting','Date Of Completion'];
        $headings[] = [];
        foreach ($data['results'] as $lis) {

         $export_data[] = [
          $lis['label'],
          $lis['sid'],
          $lis['acs'],
          GetReadableDateFormat($lis['start_nomi_date']),
          GetReadableDateFormat($lis['last_nomi_date']),
          GetReadableDateFormat($lis['dt_nomi_scr']),
          GetReadableDateFormat($lis['last_wid_date']),
          GetReadableDateFormat($lis['poll_date']),
          GetReadableDateFormat($lis['count_date']),
          GetReadableDateFormat($lis['complete_date']),

        ];
      }

      $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
      return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

      // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
      //   $excel->sheet('Sheet1', function($sheet) use($export_data) {
      //     $sheet->mergeCells('A1:K1');
      //     $sheet->cell('A1', function($cell) {
      //       $cell->setAlignment('center');
      //       $cell->setFontWeight('bold');
      //     });
      //     $sheet->fromArray($export_data,null,'A1',false,false);
      //   });
      // })->export('xls');



      }
  //AC ECI ELECTION SCHEDULE MAIN DATA report excel function ends


  //AC ECI ELECTION SCHEDULE MAIN DATA report pdf function ends
public function ElectionScheduleStatePdf(Request $request){
  set_time_limit(6000);
  $data = $this->ElectionScheduleState($request->merge(['is_excel' => 1]));
  $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
  $pdf = \PDF::loadView($this->view_path.'.ElectionScheduleStatePdf',$data);
  return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
}
  //AC ECI ELECTION SCHEDULE MAIN DATA report pdf function ends


  //AC ECI ELECTION SCHEDULE MAIN DATA AC WISE FUNCTION STARTS
  public function ElectionScheduleAc(Request $request){

     //INPUT CLEANN CLASSES STARTS
        $xss    = new xssClean;
        $secure = new secureCode;
        //INPUT CLEANN CLASSES ENDS

    if(Auth::user()->role_id == '4'){

        $this->action_state  = 'acceo/ElectionScheduleState';
        $this->action_ac     = 'acceo/ElectionScheduleState/state';
      }
    
      $data = [];
      //$default_phase = PhaseModel::get_current_phase();
      //$default_phase = 'all';

      $request_array = []; 
      $data['phases'] = PhaseModel::get_phases();

      $data['phase'] = NULL;
      if($request->has('phase')){
        if($xss->clean_input($request->phase) != 'all'){
          $data['phase'] = $xss->clean_input($request->phase);
        }
        $request_array[] =  'phase='.$xss->clean_input($request->phase);
      }/*else{
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase='.$default_phase; 
      }*/

      //CHECKING IF THE REQUEST CONTAINS THE STATE DATA OR NOT
        $data['state'] = NULL;
        if($request->has('st_code')){
          $data['state'] = $request->st_code;
          $request_array[] = 'st_code='.$request->st_code;
        }

        if($data['state']=='')
        {
            if(Auth::user()->role_id == '4'){
             $data['state']=Auth::user()->st_code;
             }
        }


      //set title
      $title_array  = [];
      $data['heading_title'] = 'List Of Election Schedule';

      // if($data['phase']){
      //   $title_array[] = "Phase: ".$data['phase'];
      // }

      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }


      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        'phase' => $data['phase']
      ];

      $states = StateModel::get_pc_states_with_filter($filter_for_state); 

      $data['states'] = [];
      //STATE LISTR STARTS

      //FOR CEO 
      if(Auth::user()->role_id == '4'){
        $st_object = StateModel::get_state_by_code(Auth::user()->st_code);
        
        $data['states'][] = [
          'code' => base64_encode($st_object['ST_CODE']),
          'name' => $st_object['ST_NAME'],
        ];
        
      }else {
       //FOR ECI
        foreach($states as $result){
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
        'href' =>  url($this->action_ac.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_ac.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];

      $data['action']         = url($this->action_ac);

      $data['consituencies']  = AcModel::get_records([
        'state'         => $data['state'],
        'phase'         => $data['phase']
      ]);

      $results                = [];

      $filter_election = [
          'state'         => $data['state'],
          'phase'         => $data['phase'],
        ];
      
        //ECI
        if(Auth::user()->role_id == '7'){

             $filter_election = [
              'phase'     => $data['phase'],
              'state'     => $data['state'],
              'group_by'  => 'ac_no',
              'order_by'  => 'ac_no'
            ];
          }


        //STATE NODAL POLICE OFFICER
        if(Auth::user()->role_id == '4'){

           $filter_election = [
            'phase'     => $data['phase'],
            'state'     => Auth::user()->st_code,
            'group_by'  => 'ac_no',
            'order_by'  => 'ac_no'
          ];
        }

      $object   = ElectionScheduleModel::ac_schedule($filter_election);

      foreach ($object as $result) {

            $individual_filter_array = [];
            $individual_filter_array['state'] = 'st_code='.$result['st_code'];
            $individual_filter_array['phase'] = 'phase='.$result['sid'];
            $individual_filter    = implode('&', $individual_filter_array);
                
            //checking dates for election events
            
            //START NOMINATION DATE DIFF
            $start_nomi_class   = ElectionScheduleModel::date_diff($result['start_nomi_date']);

            //LAST NOMINATION DATE DIFF
            $last_nomi_class   = ElectionScheduleModel::date_diff($result['last_nomi_date']);

            //SCRUTINY DATE DIFF
            $scr_date_class   = ElectionScheduleModel::date_diff($result['dt_nomi_scr']);
            
            //LAST WIDRAWL DATE DIFF
            $wid_date_class   = ElectionScheduleModel::date_diff($result['last_wid_date']);

            //POLL DATE DIFF
            $poll_date_class   = ElectionScheduleModel::date_diff($result['poll_date']);

            //COUNT DATE DIFF
            $count_date_class   = ElectionScheduleModel::date_diff($result['count_date']);

            //COMPLETE DATE DIFF
            $comp_date_class   = ElectionScheduleModel::date_diff($result['complete_date']);

               
                $results[] = [
                  'label'                    => $result['state'],
                  'st_code'                  => $result['st_code'],
                  'sid'                      => $result['sid'],
                  'const_name'               => $result['const_name'],
                  'const_no'                 => $result['const_no'],
                  'start_nomi_class'         => $start_nomi_class,
                  'start_nomi_date'          => $result['start_nomi_date'],
                  'last_nomi_class'          => $last_nomi_class,
                  'last_nomi_date'           => $result['last_nomi_date'],
                  'nomi_scr_class'           => $scr_date_class,
                  'dt_nomi_scr'              => $result['dt_nomi_scr'],
                  'last_wid_class'           => $wid_date_class,
                  'last_wid_date'            => $result['last_wid_date'],
                  'poll_date_class'          => $poll_date_class,
                  'poll_date'                => $result['poll_date'],
                  'count_date_class'         => $count_date_class,
                  'count_date'               => $result['count_date'],
                  'complete_date_class'      => $comp_date_class,
                  'complete_date'            => $result['complete_date'],
                  'href'                     => url($this->action_ac)."?".$individual_filter
                ];      

            } 



     $data['filter_action'] = Common::generate_url("booth-app-revamp/officers");
     $form_filter_array = [
       'st_code' => true,
        'dist_no' => true,
        'ac_no' => true,
        'ps_no' => false,
        'designation' => true,
       /*'allowed_acs'     => $this->allowed_acs,
       'allowed_st_code' => $this->allowed_st_code,
       'allowed_dist_no' => $this->allowed_dist_no,*/
     ];
     if($request->has('st_code')){
            $data['st_code'] = $request->st_code;
      }elseif(!empty($user_data->st_code)){
        $data['st_code'] = $user_data->st_code;
      }else{
        $data['st_code'] = '';
      }
    
     $form_filters = Common::get_form_filters($form_filter_array, $request);
     $data['form_filters'] = $form_filters;

    $data['user_data']  =   Auth::user();
    $data['results']   = $results;

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }
 
    return view($this->view_path.'.ElectionScheduleAc', $data);

  }
   //AC ECI ELECTION SCHEDULE MAIN DATA AC WISE FUNCTION ENDS


  //AC ECI ELECTION SCHEDULE MAIN DATA excel function starts
      public function ElectionScheduleAcExcel(Request $request){

        set_time_limit(6000);
        $data = $this->ElectionScheduleAc($request->merge(['is_excel' => 1]));

        $export_data = [];
        $export_data[] = [$data['heading_title']];
        $headings[]=[];
        $export_data[] = ['State','Poll Events (Phase)','ACs No','ACs Name','Date of Issue of Gazette Notification','Last Date For Making Nominations','Date for Scrutiny of Nominations','Last Date For Withdrawl of Candidature','Date Of Poll','Date Of Counting','Date Of Completion'];

        foreach ($data['results'] as $lis) {

         $export_data[] = [
          $lis['label'],
          $lis['sid'],
          $lis['const_no'],
          $lis['const_name'],
          GetReadableDateFormat($lis['start_nomi_date']),
          GetReadableDateFormat($lis['last_nomi_date']),
          GetReadableDateFormat($lis['dt_nomi_scr']),
          GetReadableDateFormat($lis['last_wid_date']),
          GetReadableDateFormat($lis['poll_date']),
          GetReadableDateFormat($lis['count_date']),
          GetReadableDateFormat($lis['complete_date']),

        ];
      }

      $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

      return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

      // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
      //   $excel->sheet('Sheet1', function($sheet) use($export_data) {
      //     $sheet->mergeCells('A1:L1');
      //     $sheet->cell('A1', function($cell) {
      //       $cell->setAlignment('center');
      //       $cell->setFontWeight('bold');
      //     });
      //     $sheet->fromArray($export_data,null,'A1',false,false);
      //   });
      // })->export('xls');

      }
  //AC ECI ELECTION SCHEDULE AC DATA report excel function ends


  //AC ECI ELECTION SCHEDULE  AC DATA report pdf function ends
public function ElectionScheduleAcPdf(Request $request){
  set_time_limit(6000);
  $data = $this->ElectionScheduleAc($request->merge(['is_excel' => 1]));
  $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
  $pdf = \PDF::loadView($this->view_path.'.ElectionScheduleAcPdf',$data);
  return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
}
  //AC ECI ELECTION SCHEDULE AC DATA report pdf function ends
    

}  // end class