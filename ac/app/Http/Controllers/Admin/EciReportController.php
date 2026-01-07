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

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
  //INCLUDING CLASSES
use App\models\Admin\PhaseModel;
use App\models\Admin\StateModel;
use App\models\Admin\AcModel;
use App\Classes\xssClean;
use App\Classes\secureCode;


//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;

date_default_timezone_set('Asia/Kolkata');
    

class EciReportController extends Controller
{   

    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){   
  
        $this->middleware(['auth:admin','auth']);
    //$this->middleware('clean_url');
    //$this->middleware('eci');
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
       
    }
  
  

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
    */

    protected function guard(){
        return Auth::guard();
    }

   public function dashboard(){ 
        
        $users=Session::get('admin_login_details');
        $user = Auth::user();   
        if(session()->has('admin_login')){  
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
            $list_record=$this->ECIModel->getallelectionphasewise();
            $list_state=$this->ECIModel->listcurrentelectionstate();
            $list_phase=$this->ECIModel->listcurrentelectionphase();
            $list_electionid=$this->ECIModel->getallelectionbyid();
            $list=$this->ECIModel->listelectiontype();
           
            $module=$this->commonModel->getallmodule();
             return view('admin.ac.eci.dashboard', ['user_data' => $d,'module' => $module,'list_record' => $list_record,'list_state'=>$list_state,'list_phase'=>$list_phase,'list_electionid'=>$list_electionid,'list'=>$list]);
             
          }
          else {
              return redirect('/admin-login');
          }    
  
        }   // end dashboard function

/*    public function dashboard(){ 
        
        $users=Session::get('admin_login_details');
        $user = Auth::user();   
        if(session()->has('admin_login')){  
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
            $list_record=$this->ECIModel->getallelectionphasewise();
            $list_state=$this->ECIModel->listcurrentelectionstate();
            $list_phase=$this->ECIModel->listcurrentelectionphase();
            $list_electionid=$this->ECIModel->getallelectionbyid();
            $list=$this->ECIModel->listelectiontype();
           
            $module=$this->commonModel->getallmodule();
             return view('admin.pc.eci.dashboard', ['user_data' => $d,'module' => $module,'list_record' => $list_record,'list_state'=>$list_state,'list_phase'=>$list_phase,'list_electionid'=>$list_electionid,'list'=>$list]);
             
          }
          else {
              return redirect('/admin-login');
          }    
  
        }   // end dashboard function*/
    

    //ECI ACTIVE USERS REPORT STARTS
    public function EciActiveUsers(Request $request){  
      //ECI ACTIVE USERS REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

             /* $EciActiveUsersSelectData = "SELECT ST_NAME,total_user,active_users,ROUND(( active_users/total_user *100) ,2)AS                           percentage FROM (SELECT m.ST_NAME ,COUNT(*) total_user,COUNT(IF(PASSWORD!='' ,PASSWORD,NULL)) AS active_users FROM `officer_login` o LEFT JOIN m_state m ON m.ST_CODE=o.st_code LEFT JOIN m_election_details E ON E.ELECTION_ID=o.election_id AND E.st_code=o.st_code  WHERE `role_id`  IN ('4','5','19') AND o.`election_id`=".$user->election_id." AND o.`is_active`=1 AND E.election_status=1  GROUP BY 1) result";*/


  /*$EciActiveUsersSelectData = "SELECT ST_NAME, total_user, active_users, ROUND(( active_users/total_user *100) ,2)AS percentage
FROM (SELECT m.ST_NAME ,COUNT(DISTINCT officername) total_user,
COUNT(DISTINCT(IF(PASSWORD!='' ,officername,NULL))) AS active_users
FROM officer_login o  JOIN m_state m ON m.ST_CODE=o.st_code
JOIN m_election_details E ON E.ELECTION_ID=o.election_id AND E.st_code=o.st_code
WHERE role_id  IN ('4','5','19') AND o.election_id=".$user->election_id." AND o.is_active=1 AND E.election_status=1 GROUP BY 1) result";*/

$EciActiveUsersSelectData = "
SELECT ST_NAME, total_user, active_users, ROUND(( active_users/total_user *100) ,2)AS percentage
FROM (
SELECT o.st_code,m.st_name,COUNT(DISTINCT officername) total_user,o.is_active,
COUNT(DISTINCT(IF(PASSWORD!='' ,officername,NULL))) AS active_users
FROM officer_login o  JOIN m_state m ON m.ST_CODE=o.st_code
AND o.role_id  IN ('4','5','19')
AND o.election_id=".$user->election_id."
JOIN m_election_details E ON E.ELECTION_ID=o.election_id AND E.st_code=o.st_code
AND E.election_status='1'
GROUP BY o.st_code
)result";
            
             $EciActiveUsers = DB::select($EciActiveUsersSelectData);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($AllPartyList);

            return view('admin.ac.eci.EciActiveUsers',['user_data' => $user_data,'EciActiveUsers' => $EciActiveUsers]);
             
          
              
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI ACTIVE USERS REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI ACTIVE USERS REPORT FUNCTION ENDS

    //ECI ACTIVE USERS EXCEL REPORT STARTS
    public function EciActiveUsersReportExcel(Request $request){  
      //ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
             

              //test starts here 
              $EciActiveUsersSelectData =       "SELECT ST_NAME, total_user, active_users, ROUND(( active_users/total_user *100) ,2)AS percentage
              FROM (SELECT m.ST_NAME ,COUNT(DISTINCT officername) total_user,
              COUNT(DISTINCT(IF(PASSWORD!='' ,officername,NULL))) AS active_users
              FROM officer_login o  JOIN m_state m ON m.ST_CODE=o.st_code
              JOIN m_election_details E ON E.ELECTION_ID=o.election_id AND E.st_code=o.st_code
              WHERE role_id  IN ('4','5','19') AND o.election_id=".$user->election_id." AND o.is_active=1 AND E.election_status=1 GROUP BY 1) result";
                          
              $EciActiveUsersReportExcel = DB::select($EciActiveUsersSelectData);
              $TotalUser = 0;
              $ActiveUser = 0;
           
             $user = Auth::user();

             $export_data[] = ['State Name', 'Total Users', 'Active Users', '% Of Users'];
             $headings[] = [];

             foreach ($EciActiveUsersReportExcel as $ActiveUsers) {
                
                if($ActiveUsers->total_user ==''){
                  
                   $ActiveUsers->total_user = '0';

                }

                if($ActiveUsers->active_users ==''){
                  
                   $ActiveUsers->active_users = '0';

                }

                if($ActiveUsers->percentage ==''){
                  
                   $ActiveUsers->percentage = '0';

                }
                $TotalUser             +=   $ActiveUsers->total_user;
               $ActiveUser            +=   $ActiveUsers->active_users;

                $export_data[] = [
                  $ActiveUsers->ST_NAME,
                  $ActiveUsers->total_user,
                  $ActiveUsers->active_users,
                  $ActiveUsers->percentage,
                  
                  
        
                ];


                         }


              $name_excel = 'EciActiveUsersReportExcel'.'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

              //test end here

                          

//               \Excel::create('EciActiveUsersReportExcel'.'_'.$cur_time, function($excel)  { 
//               $excel->sheet('Sheet1', function($sheet)  {

//                 $user = Auth::user();

//              /* $EciActiveUsersSelectData = "SELECT ST_NAME,total_user,active_users,ROUND(( active_users/total_user *100) ,2)AS                           percentage FROM (SELECT m.ST_NAME ,COUNT(*) total_user,
//                                            COUNT(IF(PASSWORD!='' ,PASSWORD,NULL)) AS active_users
//                                            FROM `officer_login` o LEFT JOIN m_state m ON m.ST_CODE=o.st_code
//                                            LEFT JOIN m_election_details E ON E.ELECTION_ID=o.election_id AND E.st_code=o.st_code
//                                            WHERE `role_id`  IN ('4','5','19') AND o.`election_id`=".$user->election_id." AND o.`is_active`=1 AND E.election_status=1 GROUP BY 1) result";*/

//                                     $EciActiveUsersSelectData =       "SELECT ST_NAME, total_user, active_users, ROUND(( active_users/total_user *100) ,2)AS percentage
// FROM (SELECT m.ST_NAME ,COUNT(DISTINCT officername) total_user,
// COUNT(DISTINCT(IF(PASSWORD!='' ,officername,NULL))) AS active_users
// FROM officer_login o  JOIN m_state m ON m.ST_CODE=o.st_code
// JOIN m_election_details E ON E.ELECTION_ID=o.election_id AND E.st_code=o.st_code
// WHERE role_id  IN ('4','5','19') AND o.election_id=".$user->election_id." AND o.is_active=1 AND E.election_status=1 GROUP BY 1) result";
            
//              $EciActiveUsersReportExcel = DB::select($EciActiveUsersSelectData);
//             //dd($EciActiveUsers);  

//                $arr  = array();
//          $TotalUser = 0;
//                $ActiveUser = 0;
            
//               $user = Auth::user();
//               foreach ($EciActiveUsersReportExcel as $ActiveUsers) {
                 
//                  if($ActiveUsers->total_user ==''){
                   
//                     $ActiveUsers->total_user = '0';

//                  }

//                  if($ActiveUsers->active_users ==''){
                   
//                     $ActiveUsers->active_users = '0';

//                  }

//                  if($ActiveUsers->percentage ==''){
                   
//                     $ActiveUsers->percentage = '0';

//                  }

//                  $data =  array(
//                           $ActiveUsers->ST_NAME,
//                           $ActiveUsers->total_user,
//                           $ActiveUsers->active_users,
//                           $ActiveUsers->percentage,
//                                 );
        
//         $TotalUser             +=   $ActiveUsers->total_user;
//                 $ActiveUser            +=   $ActiveUsers->active_users;
        
//                           array_push($arr, $data);
//                            // }
//                           }
//          $totalvalues = array('Total',$TotalUser,$ActiveUser);
//                  // print_r($totalvalues);die;
//                  array_push($arr,$totalvalues);
//                 $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
//                                'State Name', 'Total Users', 'Active Users', '% Of Users'
//                        )

//                    );

//                  });

//             })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI ACTIVE USERS EXCEL REPORT FUNCTION ENDS
  
  
   //ECI ACTIVE USERS PDF REPORT STARTS
    public function EciActiveUsersPdf(Request $request){  
      //ECI ACTIVE USERS PDF REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

              /*$EciActiveUsersSelectData = "SELECT ST_NAME,total_user,active_users,ROUND(( active_users/total_user *100) ,2)AS                           percentage FROM (SELECT m.ST_NAME ,COUNT(*) total_user,
                                           COUNT(IF(PASSWORD!='' ,PASSWORD,NULL)) AS active_users
                                           FROM `officer_login` o LEFT JOIN m_state m ON m.ST_CODE=o.st_code
                                           LEFT JOIN m_election_details E ON E.ELECTION_ID=o.election_id AND E.st_code=o.st_code
                                           WHERE `role_id`  IN ('4','5','19') AND o.`election_id`=".$user->election_id." AND o.`is_active`=1 AND E.election_status=1  GROUP BY 1) result";*/

                              $EciActiveUsersSelectData = "SELECT ST_NAME, total_user, active_users, ROUND(( active_users/total_user *100) ,2)AS percentage FROM (SELECT m.ST_NAME ,COUNT(DISTINCT officername) total_user,
COUNT(DISTINCT(IF(PASSWORD!='' ,officername,NULL))) AS active_users
FROM officer_login o  JOIN m_state m ON m.ST_CODE=o.st_code
JOIN m_election_details E ON E.ELECTION_ID=o.election_id AND E.st_code=o.st_code
WHERE role_id  IN ('4','5','19') AND o.election_id=".$user->election_id." AND o.is_active=1 AND E.election_status=1 GROUP BY 1) result";

            
             $EciActiveUsersPdf = DB::select($EciActiveUsersSelectData);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($EciActiveUsersPdf);

             $pdf = PDF::loadView('admin.ac.eci.EciActiveUsersPdf',['user_data' => $user_data,'EciActiveUsersPdf' =>$EciActiveUsersPdf]);
                        return $pdf->download('AC_EciActiveUsersPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.ac.eci.EciActiveUsersPdf');  
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI ACTIVE USERS PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI ACTIVE USERS PDF REPORT FUNCTION ENDS


    //ECI NOMINATION DATA REPORT STARTS
    public function EciNominationReport(Request $request){  
      //ECI NOMINATION DATA REPORT TRY CATCH BLOCK STARTS
       try{


            
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
        
        //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
              $GetAllElectionSchedule = $this->GetAllElectionSchedule(1);
              Session::put('ScheduleList', $GetAllElectionSchedule);
              //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
             // dd($GetAllElectionSchedule);
             
             
                 $data = [];
      $data['number_of_voting'] = 0;
      $default_phase = $request->phase;
      //$default_phase = PhaseModel::get_current_phase();

      $request_array = []; 
      $data['phases'] = PhaseModel::get_phases();
      $data['phase'] = NULL;
      if($request->has('phase')){
        if($request->phase != 'all'){
          $data['phase'] = $request->phase;
        }
        $request_array[] =  'phase='.$request->phase;
      }else{
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase='.$default_phase; 
      }
      
      if($request->has('from')){
        if($request->from){
          $data['from'] = $request->from;
        }
        $request_array[] =  'from='.$request->from;
      }
      
       if($request->has('to')){
        if($request->to){
          $data['to'] = $request->to;
        }
        $request_array[] =  'to='.$request->to;
      }
        
        
        if($user->role_id == 4){
            $this->action_state = 'acceo/EciNominationReport';
            $request->state = $user->st_code;
        }else{
            $this->action_state = 'eci/EciNominationReport';
        }
        
        

      $data['state'] = NULL;
      if($request->state){
        $data['state'] = $request->state;
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Eci Nomination Report';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }
      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        'state' => $data['state'],
        'phase' => $data['phase']
      ];

//dd($filter_for_state);

      $states = StateModel::get_pc_states_with_filter($filter_for_state); 

      $data['states'] = [];
      foreach($states as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }
$from_date = $from_to = '';

    if($request->has('from') && $request->has('to')){
        $from_date  = date('Y-m-d',strtotime($request->from));
        $from_to        = date('Y-m-d',strtotime($request->to));
        $request_array[] = 'from='.$request->from;
        $request_array[] = 'to='.$request->to;
        $data['from'] = date('Y-m-d',strtotime($request->from));
        $data['to'] = date('Y-m-d',strtotime($request->to));
      }


      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state).'?excel=yes&'.implode('&', $request_array),
        'target' => true
      ];
     

      $data['action']         = url($this->action_state);

      $results                = [];

      
     // dd($data);
             
    

             $EciNominationSelectData = "SELECT s.ST_CODE,s.ST_NAME, COUNT(d.candidate_id) AS total_nomination,
                     COUNT(IF(application_status=6,d.nom_id,NULL)) AS accepted_status,COUNT(IF(cad.nom_id,cad.nom_id,NULL)) AS affidavit_count
                     FROM candidate_nomination_detail d     
                    join m_election_details medd on d.ST_CODE = medd.ST_CODE and d.ac_no = medd.const_no
                    left join candidate_affidavit_detail as cad on cad.nom_id = d.nom_id
                    right JOIN (SELECT m_state.* FROM `m_election_details` med join m_state on m_state.ST_CODE = med.ST_CODE group by m_state.ST_CODE)s on s.st_code =  d.st_code AND `application_status` != 11 and `party_id` != 1180 AND  d.ac_no !='' where s.ST_CODE != ''";
                    
                    if($request->election_type){
                        $EciNominationSelectData .=" and medd.ELECTION_TYPEID = $request->election_type";
                    }
                    
                    if($request->phase){
                        $EciNominationSelectData .=" and medd.ScheduleID = '$request->phase' ";
                    }
                    
                    if($request->state){
                        $EciNominationSelectData .=" and medd.ST_CODE = '$request->state' ";
                    }
                    
                    if($from_date && $from_to){
                      $EciNominationSelectData .=" and d.date_of_submit between '$from_date' and '$from_to'";
                    }
                    
                     $EciNominationSelectData .=" GROUP BY 1";
            
             $EciNominationReport = DB::select($EciNominationSelectData);
        
            $data['user_data']         = $user_data;
            $data['EciNominationReport']         = $EciNominationReport;
            
            if($request->excel == 'yes'){
            $export_data = [];
            $headings[] = [$data['heading_title']];
            $export_data[] = ['State', 'Total Nomination Applied', 'Affidavit Uploaded'];
            foreach ($EciNominationReport as $lis) {
              $export_data[] = [
                $lis->ST_NAME,
                 ($lis->total_nomination)?$lis->total_nomination:'0',
                 ($lis->affidavit_count)?$lis->affidavit_count:'0',
              ];
            }
          
        $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                   
            }
         

            return view('admin.ac.eci.EciNominationReport',$data);

            
            }else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI NOMINATION DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI NOMINATION DATA REPORT FUNCTION ENDS

    //ECI NOMINATION EXCEL DATA REPORT STARTS
    public function EciNominationExcelReport(Request $request){  
      //ECI NOMINATION EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
             
                          

              \Excel::create('EciNominationsReport'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

              $EciNominationSelectData = "SELECT s.ST_CODE,s.ST_NAME,COUNT(candidate_id) AS total_nomination,
                     COUNT(IF(application_status=6,nom_id,NULL)) AS accepted_status
                     FROM candidate_nomination_detail d RIGHT JOIN m_state s ON s.ST_CODE=d.st_code 
                     AND `application_status` != 11 AND `party_id` != 1180
                     AND  `ac_no` !=''
                     GROUP BY 1";
            
             $EciNominations = DB::select($EciNominationSelectData);
            //dd($EciActiveUsers);  

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciNominations as $NominationsData) {
                
                 if($NominationsData->total_nomination ==''){
                   
                    $NominationsData->total_nomination = '0';

                 }

                 if($NominationsData->accepted_status ==''){
                   
                    $NominationsData->accepted_status = '0';

                 }

                 $data =  array(
                          $NominationsData->ST_NAME,
                          $NominationsData->total_nomination,
                          $NominationsData->accepted_status,
                                );
                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'State Name', 'Total Nomination Applied', 'Total Accepted'
                             )

                   );

                 });

            })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI NOMINATION EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI NOMINATION EXCEL DATA REPORT FUNCTION ENDS

    //ECI COUNTING RESULT DATA REPORT STARTS
    public function EciCountingStatusReport(Request $request){  
      //ECI COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;
                          
              $EciCountingSelectData = "SELECT ST_NAME,TOTAL_AC,COUNTING_STARTED,RESULT_DECLARED,
CONCAT(ROUND((RESULT_DECLARED/TOTAL_AC*100),2),'%') AS PERCENTAGE FROM(
SELECT
s.ST_NAME,
COUNT(a.AC_NAME)TOTAL_AC,
COUNT(IF(lead_cand_name!='null' AND lead_cand_name!='',a.AC_NAME,NULL))COUNTING_STARTED,
COUNT(IF(STATUS='1',a.AC_NAME,NULL))RESULT_DECLARED
FROM winning_leading_candidate w 
LEFT JOIN m_ac a ON w.st_code=a.ST_CODE AND w.ac_no=a.AC_NO
LEFT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user->election_id."
LEFT JOIN m_state s ON s.ST_CODE=a.ST_CODE GROUP BY 1) result ORDER BY ST_NAME";
            
             $EciCountingStatusReport = DB::select($EciCountingSelectData);

             return view('admin.ac.eci.EciCountingStatusReport',['user_data' => $user_data,'EciCountingStatusReport' => $EciCountingStatusReport]);             
               
            }else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI COUNTING RESULT DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI COUNTING RESULT DATA REPORT FUNCTION ENDS


    //ECI COUNTING RESULT EXCEL DATA REPORT STARTS
    public function EciCountingExcelStatus(Request $request){  
      //ECI COUNTING RESULT EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code = $d->st_code;
              $st_name = $d->placename;
                          

              \Excel::create('AC_CountingStatusExcel_'.trim($st_name).'_'.$cur_time, function($excel) { 
              $excel->sheet('Sheet1', function($sheet) {

              $user = Auth::user();   

              $EciCountingSelectData ="SELECT ST_NAME,TOTAL_AC,COUNTING_STARTED,RESULT_DECLARED,
CONCAT(ROUND((RESULT_DECLARED/TOTAL_AC*100),2),'%') AS PERCENTAGE FROM(
SELECT
s.ST_NAME,
COUNT(a.AC_NAME)TOTAL_AC,
COUNT(IF(lead_cand_name!='null' AND lead_cand_name!='',a.AC_NAME,NULL))COUNTING_STARTED,
COUNT(IF(STATUS='1',a.AC_NAME,NULL))RESULT_DECLARED
FROM winning_leading_candidate w 
LEFT JOIN m_ac a ON w.st_code=a.ST_CODE AND w.ac_no=a.AC_NO
LEFT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user->election_id."
LEFT JOIN m_state s ON s.ST_CODE=a.ST_CODE GROUP BY 1) result ORDER BY ST_NAME";
            
             $EciCountingData = DB::select($EciCountingSelectData);
            //dd($PcCeoCountingData);  

              $arr  = array();
        $TotalAc= 0;
        $TotalCountingStarted = 0;
        $TotalDeclared = 0;
            
              $user = Auth::user();
              foreach ($EciCountingData as $CountingData) {

                if($CountingData->COUNTING_STARTED ==''){
                   
                    $CountingData->COUNTING_STARTED = '0';

                 }

                 if($CountingData->RESULT_DECLARED ==''){
                   
                    $CountingData->RESULT_DECLARED = '0';

                 }

                 $data =  array(
                                  $CountingData->ST_NAME,
                                  $CountingData->TOTAL_AC,
                                  $CountingData->COUNTING_STARTED,
                                  $CountingData->RESULT_DECLARED,
                                  $CountingData->PERCENTAGE,
                                );
        $TotalAc              += $CountingData->TOTAL_AC;
                $TotalCountingStarted += $CountingData->COUNTING_STARTED;
                $TotalDeclared        += $CountingData->RESULT_DECLARED;
                          array_push($arr, $data);
                           // }
                          }
         $totalvalues = array('Total',$TotalAc,$TotalCountingStarted,$TotalDeclared);
                // print_r($totalvalues);die;
                  array_push($arr,$totalvalues);
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'State', 'No Of ACs','Counting Started in ACs', 'Result Declared in ACs', '% Of Results'
                             )

                   );

                 });

            })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI COUNTING RESULT EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI COUNTING RESULT EXCEL DATA REPORT FUNCTION ENDS
  
  //ECI COUNTING RESULT DATA PDF REPORT STARTS
    public function EciCountingStatusReportPdf(Request $request){  
      //ECI COUNTING RESULT DATA PDF REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;
                          
              $EciCountingSelectData = "SELECT ST_NAME,TOTAL_AC,COUNTING_STARTED,RESULT_DECLARED,
CONCAT(ROUND((RESULT_DECLARED/TOTAL_AC*100),2),'%') AS PERCENTAGE FROM(
SELECT
s.ST_NAME,
COUNT(a.AC_NAME)TOTAL_AC,
COUNT(IF(lead_cand_name!='null' AND lead_cand_name!='',a.AC_NAME,NULL))COUNTING_STARTED,
COUNT(IF(STATUS='1',a.AC_NAME,NULL))RESULT_DECLARED
FROM winning_leading_candidate w 
LEFT JOIN m_ac a ON w.st_code=a.ST_CODE AND w.ac_no=a.AC_NO
LEFT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user->election_id."
LEFT JOIN m_state s ON s.ST_CODE=a.ST_CODE GROUP BY 1) result ORDER BY ST_NAME";
            
             $EciCountingStatusReportPdf = DB::select($EciCountingSelectData);

             $pdf = PDF::loadView('admin.ac.eci.EciCountingStatusReportPdf',['user_data' => $user_data,'EciCountingStatusReportPdf' =>$EciCountingStatusReportPdf]);
                        return $pdf->download('AC_EciCountingStatusReportPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.ac.eci.EciCountingStatusReportPdf');             
               
            }else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI COUNTING RESULT DATA PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI COUNTING RESULT DATA PDF REPORT FUNCTION ENDS
  

    //ECI PARTY RESULT DATA REPORT STARTS
    public function EciPartyData(Request $request){  
      //ECI PARTY DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   

          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data =$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

              $AllPartyList = $this->GetAllPartyListWithType();
              //dd($AllPartyList);

            return view('admin.ac.eci.EciPartyData',['user_data' => $user_data,'AllPartyList' => $AllPartyList]);
            //return view('admin.pc.ceo.pclist',['user_data' => $d,'ele_details' => $ele_details,'allPcList' => $allTypeCountArr]);
               
          }else {
                  return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PARTY DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI PARTY DATA REPORT FUNCTION ENDS

    //ECI PARTY EXCEL DATA REPORT STARTS
    public function EciPartyDataExcel(Request $request){  
      //ECI PARTYL EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code = $d->st_code;
              $st_name = $d->placename;
               
              $AllPartyList = $this->GetAllPartyListWithType();            
              $user = Auth::user();
              $export_data[] = ['Party Abbreviation', 'Party Name','Party Type'];
              $headings[] = [];

              foreach ($AllPartyList as $listdata) {

                $export_data[] = [
                  $listdata->PARTYABBRE,
                                 $listdata->PARTYNAME,
                                 $listdata->PARTYTYPE,
        
                ];

              }


              $name_excel = 'EciPartyData_'.trim($st_name).'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


            //   \Excel::create('EciPartyData_'.trim($st_name).'_'.$cur_time, function($excel) { 
            //   $excel->sheet('Sheet1', function($sheet) {

            //  $AllPartyList = $this->GetAllPartyListWithType();

            //   $arr  = array();
            
            //   $user = Auth::user();
            //   foreach ($AllPartyList as $listdata) {

            //      $data =  array(
            //                       $listdata->PARTYABBRE,
            //                       $listdata->PARTYNAME,
            //                       $listdata->PARTYTYPE,
            //                     );
            //               array_push($arr, $data);
            //                // }
            //               }
            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'Party Abbreviation', 'Party Name','Party Type'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PARTY EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI PARTY EXCEL DATA REPORT FUNCTION ENDS
  
  //ECI PARTY RESULT DATA PDF REPORT STARTS
    public function EciPartyDataPdf(Request $request){  
      //ECI PARTY DATA PDF REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   

          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data =$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

              $EciPartyDataPdf = $this->GetAllPartyListWithType();

              //dd($EciPartyDataPdf);
            $pdf = PDF::loadView('admin.ac.eci.EciPartyDataPdf',['user_data' => $user_data,'EciPartyDataPdf' =>$EciPartyDataPdf]);
                        return $pdf->download('AC_EciPartyDataPdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.ac.eci.EciPartyDataPdf'); 
               
          }else {
                  return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PARTY DATA PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI PARTY DATA PDF REPORT FUNCTION ENDS


    //ECI SYMBOL DATA REPORT STARTS
    public function EciSymbolData(Request $request){  
      //ECI SYMBOL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   

          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data =$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

              $AllSymbolList = DB::table('m_symbol')->orderBy('SYMBOL_NO', 'ASC')->get();//$this->GetAllPartySymbol();
              //dd($AllSymbolList);

            return view('admin.ac.eci.EciSymbolData',['user_data' => $user_data,'AllSymbolList' => $AllSymbolList]);
               
          }else {
                  return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI SYMBOL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI SYMBOL DATA REPORT FUNCTION ENDS

    //ECI SYMBOL EXCEL DATA REPORT STARTS
    public function EciSymbolDataExcel(Request $request){  
      //ECI PARTY AND SYMBOL EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code = $d->st_code;
              $st_name = $d->placename;
                     
              $AllSymbolList = DB::table('m_symbol')->orderBy('SYMBOL_NO', 'ASC')->get();//$this->GetAllPartySymbol();            
              $user = Auth::user();
              $export_data[] = ['Symbol Number', 'Symbol Name'];
              $headings[] = [];

              foreach ($AllSymbolList as $listdata) {

                $export_data[] = [
                  $listdata->SYMBOL_NO,
                  $listdata->SYMBOL_DES,
        
                ];

                          // }
              }
              $name_excel = 'EciSymbolData_'.trim($st_name).'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


            //   \Excel::create('EciSymbolData_'.trim($st_name).'_'.$cur_time, function($excel) { 
            //   $excel->sheet('Sheet1', function($sheet) {

            //  $AllSymbolList = DB::table('m_symbol')->orderBy('SYMBOL_NO', 'ASC')->get();//$this->GetAllPartySymbol();

            //   $arr  = array();
            
            //   $user = Auth::user();
            //   foreach ($AllSymbolList as $listdata) {

            //      $data =  array(
            //                       $listdata->SYMBOL_NO,
            //                       $listdata->SYMBOL_DES,
            //                     );
            //               array_push($arr, $data);
            //                // }
            //               }
            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'Symbol Number', 'Symbol Name'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI SYMBOL EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI SYMBOL EXCEL DATA REPORT FUNCTION ENDS
  
  //ECI SYMBOL DATA PDF REPORT STARTS
    public function EciSymbolDataPdf(Request $request){  
      //ECI SYMBOL DATA PDF REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   

          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data =$this->commonModel->getunewserbyuserid($uid);

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

              $EciSymbolDataPdf = DB::table('m_symbol')->orderBy('SYMBOL_NO', 'ASC')->get();
              //dd($EciSymbolDataPdf);
             $pdf = PDF::loadView('admin.ac.eci.EciSymbolDataPdf',['user_data' => $user_data,'EciSymbolDataPdf' =>$EciSymbolDataPdf]);
            return $pdf->download('AC_EciSymbolDataPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
            return view('admin.ac.eci.EciSymbolDataPdf'); 
               
          }else {
                  return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI SYMBOL DATA PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI SYMBOL DATA PDF REPORT FUNCTION ENDS
  
  //AC ECI ELECTION SCHEDULE DATA REPORT STARTS
    public function EciElectionSchedule(Request $request){  
      //AC ECI ELECTION SCHEDULE DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code     = $user_data->st_code;
              $st_name     = $user_data->placename;

              //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
              $GetAllElectionSchedule = $this->GetAllElectionSchedule();
              Session::put('ScheduleList', $GetAllElectionSchedule);
              //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
              //dd($GetAllElectionSchedule);
              
              $ScheduleData =   "SELECT e.ScheduleID AS sid, st.ST_NAME AS state,
                 e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                 a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,
                 s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                 s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                 FROM m_election_details e 
                 RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO
                 RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                 RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE
                 WHERE e.CONST_TYPE = 'AC' ORDER BY state, sid, cno";

              $ScheduleSelectData = DB::select($ScheduleData);
                  
              
              return view('admin.ac.eci.EciElectionSchedule',['user_data' => $user_data,'ScheduleSelectData' =>$ScheduleSelectData]);        
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //AC ECI ELECTION SCHEDULE DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //AC ECI ELECTION SCHEDULE DATA REPORT FUNCTION ENDS
  
  //AC ECI ELECTION SCHEDULE EXCEL DATA REPORT STARTS
    public function EciElectionScheduleExcel(Request $request){  
      //AC ECI ELECTION SCHEDULE EXCEL DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;
                  
              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code     = $user_data->st_code;
              $st_name     = $user_data->placename;             

               \Excel::create('EciElectionScheduleExcelData_'.trim($st_name).'_'.$cur_time, function($excel) use($st_code) { 
              $excel->sheet('Sheet1', function($sheet) use($st_code) {

              $ScheduleExcelData =  "SELECT e.ScheduleID AS sid, st.ST_NAME AS state,
                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                   a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,
                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                   FROM m_election_details e 
                   RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO
                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                   RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE
                   WHERE e.CONST_TYPE = 'AC' ORDER BY state, sid, cno";
            
             $ScheduleSelectExcelData = DB::select($ScheduleExcelData);
            //dd($ScheduleSelectExcelData);  

              $arr  = array();
            
              $user = Auth::user();
              foreach ($ScheduleSelectExcelData as $ScheduleData) {

                 $data =  array(
                                  $ScheduleData->sid,
                  $ScheduleData->state,
                                  $ScheduleData->nac,
                                  $ScheduleData->cno,
                                  GetReadableDate($ScheduleData->start_nomi_date),
                                  GetReadableDate($ScheduleData->last_nomi_date),
                                  GetReadableDate($ScheduleData->dt_nomi_scr),
                                  GetReadableDate($ScheduleData->last_wid_date),
                                  GetReadableDate($ScheduleData->poll_date),
                                );
                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'Phase No', 'State', 'AC Name','AC No', 'Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'
                             )

                   );

                 });

            })->export('xls');

            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //AC ECI ELECTION SCHEDULE EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //AC ECI ELECTION SCHEDULE EXCEL DATA REPORT FUNCTION ENDS
  
  //AC ECI PHASE INFO DATA PDF REPORT FUNCTION STARTS
    public function EciElectionSchedulePdf(Request $request){ 
      //AC ECI PHASE INFO DATA PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


              $EciElectionSchedulePdfSelect = "SELECT e.ScheduleID AS sid, st.ST_NAME AS state,
                   e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                   a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,
                   s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                   s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                   FROM m_election_details e 
                   RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO
                   RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                   RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE
                   WHERE e.CONST_TYPE = 'AC' ORDER BY state, sid, cno";

          
            // dd($EciPhaseNominationSelectData);
            
             $EciElectionSchedulePdf = DB::select($EciElectionSchedulePdfSelect);
                  

             $pdf = PDF::loadView('admin.ac.eci.EciElectionSchedulePdf',['user_data' => $user_data,'EciElectionSchedulePdf' =>$EciElectionSchedulePdf]);
                        return $pdf->download('AC_EciElectionSchedulePdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.ac.eci.EciElectionSchedulePdf');   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC PHASE INFO DATA PDF REPORT TRY CATCH ENDS HERE
    }
    //AC PHASE INFO DATA PDF REPORT FUNCTION ENDS
  
  //AC ECI ELECTION FILTER FUNCTION STARTS
    public function EciCustomReportFilter(Request $request){ 
      //AC ECI ELECTION FILTER TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'ScheduleList'   => 'nullable|numeric|regex:/^\S*$/u',
          'state'          => 'nullable|regex:/^\S*$/u',
                    /*'startDate'    => 'required|date',
                    'endDate'        => 'required|date|after_or_equal:startDate',*/
                    
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;

            $ScheduleList        = $xss->clean_input($request['ScheduleList']);
      $state_code          = $xss->clean_input($request['state']);
      
      if (!$ScheduleList) {
                 $ScheduleList = "";
            }else{
                $ScheduleList = $ScheduleList;
            }
            //STATE CODE
            if (!$state_code) {
                 $state_code = NULL;
            }else{
                $state_code = $state_code;
            }
            

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
          //dd($ScheduleList);
             return redirect('/eci/EciCustomReportFilterGet/'.base64_encode($state_code).'/'.base64_encode($ScheduleList));         
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI ELECTION FILTER TRY CATCH ENDS HERE
    }
    //AC ECI ELECTION FILTER FUNCTION ENDS
  
  //AC ECI ELECTION FILTER FUNCTION STARTS
    public function EciCustomReportFilterGet(Request $request, $state_code, $ScheduleList= null){ 
      //AC ECI ELECTION FILTER TRY CATCH STARTS HERE
      try{
          
           //$input = $request->all();
            //echo '<pre>'.print_r(base64_decode($ScheduleList));die;


          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 
      
            $xss    = new xssClean;
            $secure = new secureCode;

           
            $ScheduleList      = base64_decode($ScheduleList);
      $state_code        = base64_decode($state_code);

            //CHECKING URL VARIABLES FOR VALUES STARTS
            //PHASE NO
            if (!$ScheduleList) {
                 $ScheduleList = "";
            }else{
                $ScheduleList = $ScheduleList;
            }
            //STATE CODE
            if (!$state_code) {
                 $state_code = NULL;
            }else{
                $state_code = $state_code;
            }
           //CHECKING URL VARIABLES FOR VALUES ENDS
         
            
            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
      
      if(empty($ScheduleList)){
        
        //CHECKING FOR ALL STATE STARTS
                if($state_code == 'all'){
          
          $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno, e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' ORDER BY state, sid, cno";

                }else{
          
          $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno,e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' AND st.ST_CODE = '".$state_code."' ORDER BY state, sid, cno";
          
                }
                //CHECKING FOR ALL STATE ENDS
        
        
      }else{ 
      
          if($state_code == 'all'){
          
          $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno, e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' AND e.ScheduleID='".$ScheduleList."'
                    ORDER BY state, sid, cno";
          
        }else{
          
          $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno, e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' AND e.ScheduleID='".$ScheduleList."'
          AND st.ST_CODE = '".$state_code."'
                    ORDER BY state, sid, cno";
          
        }
                
      }
      
            //dd($FilterData);

              $FilterSelectData = DB::select($FilterData);
        
        //STATE NAME
        if($state_code != '' &&  $state_code != 'all'){

              $statelist = getstatebystatecode($state_code);
              $state     = $statelist->ST_NAME;

            }else{ $state = "";} 
        
        //PHASE DATES
            if($ScheduleList != ''){

              $PhaseInfo = getschedulebyid($ScheduleList);
            }else{ $PhaseInfo = "";}  
              
              //dd($PhaseInfo);       
              return view('admin.ac.eci.EciCustomReportFilterGet',['user_data' => $user_data,'FilterSelectData' =>$FilterSelectData,'ScheduleList'=>$ScheduleList,'state_code'=>$state_code,'phaseid'=>$ScheduleList,'PhaseInfo'=>$PhaseInfo,'state'=>$state]);
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI ELECTION FILTER TRY CATCH ENDS HERE
    }
    //AC ECI ELECTION FILTER FUNCTION ENDS
  
  //AC ECI ELECTION FILTER FUNCTION STARTS
    public function EciCustomReportFilterGetExcel(Request $request, $state_code, $ScheduleList= null){ 
      //AC ECI ELECTION FILTER TRY CATCH STARTS HERE
      try{
          
           //$input = $request->all();
            //echo '<pre>'.print_r(base64_decode($ScheduleList));die;


          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 
      
            $xss    = new xssClean;
            $secure = new secureCode;

          
             $ScheduleList      = base64_decode($ScheduleList);
            $state_code        = base64_decode($state_code);

            //CHECKING URL VARIABLES FOR VALUES STARTS
            //PHASE NO
            if (!$ScheduleList) {
                 $ScheduleList = "";
            }else{
                $ScheduleList = $ScheduleList;
            }
            //STATE CODE
            if (!$state_code) {
                 $state_code = NULL;
            }else{
                $state_code = $state_code;
            }
           //CHECKING URL VARIABLES FOR VALUES ENDS
          
          
            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);
  
            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
              
             $ScheduleList = Session::put('ScheduleList',$ScheduleList);  
            $state_code   = Session::put('state_code',$state_code);        

             \Excel::create('EciElectionScheduleFilterExcelData_'.$cur_time, function($excel) use($st_code) { 
              $excel->sheet('Sheet1', function($sheet)  {
              

            $ScheduleList = Session::get('ScheduleList');
            $state_code   = Session::get('state_code');
      
      if(empty($ScheduleList)){
        
        //CHECKING FOR ALL STATE STARTS
                if($state_code == 'all'){
          
          $FilterDataExcel =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno, e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' ORDER BY state, sid, cno";

                }else{
          
          $FilterDataExcel =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno,e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' AND st.ST_CODE = '".$state_code."' ORDER BY state, sid, cno";
          
                }
                //CHECKING FOR ALL STATE ENDS
        
        
      }else{ 
      
          if($state_code == 'all'){
          
          $FilterDataExcel =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno, e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' AND e.ScheduleID='".$ScheduleList."'
                    ORDER BY state, sid, cno";
          
        }else{
          
          $FilterDataExcel =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno, e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' AND e.ScheduleID='".$ScheduleList."'
          AND st.ST_CODE = '".$state_code."'
                    ORDER BY state, sid, cno";
          
        }
                
      }
            
             $ScheduleSelectExcelData = DB::select($FilterDataExcel);
             //dd($ScheduleSelectExcelData);  

              $arr  = array();
            
              $user = Auth::user();
              foreach ($ScheduleSelectExcelData as $ScheduleData) {

                 $data =  array(
                                  $ScheduleData->sid,
                  $ScheduleData->state,
                                  $ScheduleData->nac,
                                  $ScheduleData->cno,
                                  GetReadableDate($ScheduleData->start_nomi_date),
                                  GetReadableDate($ScheduleData->last_nomi_date),
                                  GetReadableDate($ScheduleData->dt_nomi_scr),
                                  GetReadableDate($ScheduleData->last_wid_date),
                                  GetReadableDate($ScheduleData->poll_date),
                                );
                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'Phase No', 'State','AC Name','AC No', 'Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'
                             )

                   );

                 });

            })->export('xls');

            }else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI ELECTION FILTER TRY CATCH ENDS HERE
    }
    //AC ECI ELECTION FILTER FUNCTION ENDS
  
  
  //AC ECI ELECTION FILTER PDF REPORT FUNCTION STARTS
    public function EciCustomReportFilterGetPdf(Request $request, $state_code, $ScheduleList= null){ 
      //AC ECI ELECTION FILTER PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 
      
            $xss    = new xssClean;
            $secure = new secureCode;

           
            $ScheduleList      = base64_decode($ScheduleList);
      $state_code        = base64_decode($state_code);

            //CHECKING URL VARIABLES FOR VALUES STARTS
            //PHASE NO
            if (!$ScheduleList) {
                 $ScheduleList = "";
            }else{
                $ScheduleList = $ScheduleList;
            }
            //STATE CODE
            if (!$state_code) {
                 $state_code = NULL;
            }else{
                $state_code = $state_code;
            }
           //CHECKING URL VARIABLES FOR VALUES ENDS
         
            
            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
      
      if(empty($ScheduleList)){
        
        //CHECKING FOR ALL STATE STARTS
                if($state_code == 'all'){
          
          $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno, e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' ORDER BY state, sid, cno";

                }else{
          
          $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno,e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' AND st.ST_CODE = '".$state_code."' ORDER BY state, sid, cno";
          
                }
                //CHECKING FOR ALL STATE ENDS
        
        
      }else{ 
      
          if($state_code == 'all'){
          
          $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno, e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' AND e.ScheduleID='".$ScheduleList."'
                    ORDER BY state, sid, cno";
          
        }else{
          
          $FilterData =   "SELECT e.ScheduleID AS sid,st.ST_NAME AS state,e.CONST_NO AS cno, e.CONST_TYPE AS ctype,a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr,s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state st ON st.ST_CODE = a.ST_CODE WHERE e.CONST_TYPE = 'AC' AND e.ScheduleID='".$ScheduleList."'
          AND st.ST_CODE = '".$state_code."'
                    ORDER BY state, sid, cno";
          
        }
                
      }
      
            //dd($FilterData);

              $FilterSelectData = DB::select($FilterData);
              
              //STATE NAME
        if($state_code != '' &&  $state_code != 'all'){

              $statelist = getstatebystatecode($state_code);
              $state     = $statelist->ST_NAME;

            }else{ $state = "";} 
        
       //PHASE DATES
      if($ScheduleList != ''){

        $PhaseInfo = getschedulebyid($ScheduleList);
      }else{ $PhaseInfo = "";} 
        
         $pdf = PDF::loadView('admin.ac.eci.EciCustomReportFilterGetPdf',['user_data' => $user_data,'FilterSelectData' =>$FilterSelectData,'PhaseInfo'=>$PhaseInfo,'state'=>$state,'phaseid'=>$ScheduleList]);
                        return $pdf->download('AC_EciCustomReportFilterGetPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.pc.eci.EciCustomReportFilterGetPdf');  
        
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI ELECTION FILTER PDF REPORT TRY CATCH ENDS HERE
    }
    //AC ECI ELECTION FILTER PDF REPORT FUNCTION ENDS
  
   //AC ECI NOMINATION STATE WISE REPORT FUNCTION STARTS
    public function EciNominationStateWiseReport(Request $request){ 
      //AC ECI NOMINATION STATE WISE REPORT TRY CATCH STARTS HERE
      try{
          
         
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
       
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
        
        //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
              $GetAllElectionSchedule = $this->GetAllElectionSchedule(1);
              Session::put('ScheduleList', $GetAllElectionSchedule);
              //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
             // dd($GetAllElectionSchedule);
             
             
                 $data = [];
      $data['number_of_voting'] = 0;
      $default_phase = $request->phase;
      //$default_phase = PhaseModel::get_current_phase();

      $request_array = []; 
      $data['phases'] = PhaseModel::get_phases();
      $data['phase'] = NULL;
      if($request->has('phase')){
        if($request->phase != 'all'){
          $data['phase'] = $request->phase;
        }
        $request_array[] =  'phase='.$request->phase;
      }else{
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase='.$default_phase; 
      }
      
      
      if($request->has('from')){
        if($request->from){
          $data['from'] = $request->from;
        }
        $request_array[] =  'from='.$request->from;
      }
      
       if($request->has('to')){
        if($request->to){
          $data['to'] = $request->to;
        }
        $request_array[] =  'to='.$request->to;
      }
        
        

        if($user->role_id == 4){
            $this->action_state = 'acceo/EciNominationStateWiseReport';
            $request->state = $user->st_code;
        }else{
             $this->action_state = 'eci/EciNominationStateWiseReport';
             $request->state = base64_decode($request->state);
        }



      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = $request->state;
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Eci Nomination State Wise Report';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }
      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        'state' => $data['state'],
        'phase' => $data['phase']
      ];

      /*$states = StateModel::get_pc_states_with_filter($filter_for_state); */
      $states = DB::table('m_state')->where('ST_CODE', Auth::user()->st_code)->get();  

      $data['states'] = [];
      foreach($states as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }
    $from_date = $from_to = '';

    if($request->has('from') && $request->has('to')){
        $from_date  = date('Y-m-d',strtotime($request->from));
        $from_to        = date('Y-m-d',strtotime($request->to));
        $request_array[] = 'from='.$request->from;
        $request_array[] = 'to='.$request->to;
        $data['from'] = date('Y-m-d',strtotime($request->from));
        $data['to'] = date('Y-m-d',strtotime($request->to)); 
      }


      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state).'?excel=yes&'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state).'?pdf=yes&'.implode('&', $request_array),
        'target' => true
      ];

      $data['action']         = url($this->action_state);

      $results                = [];

      
     // dd($data);
             
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $uid=$user->id;
         
            $user_data=$this->commonModel->getunewserbyuserid($uid);


              $EciNominationStateWiseData = "SELECT a.ST_CODE,a.AC_NO,a.AC_NAME, COUNT(c.nom_id) AS totalnomination,COUNT(IF(cad.nom_id,cad.nom_id,NULL)) AS affidavit_count
              FROM candidate_nomination_detail AS c  
              left join candidate_affidavit_detail as cad on cad.nom_id = c.nom_id            
              RIGHT JOIN m_ac a ON c.ac_no = a.AC_NO AND a.ST_CODE = c.st_code RIGHT JOIN m_state s ON a.ST_CODE = s.ST_CODE LEFT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND a.AC_NO = e.CONST_NO AND CONST_TYPE = 'AC' RIGHT JOIN m_schedule sh ON e.ScheduleID = sh.SCHEDULEID WHERE party_id != 1180 AND application_status != 11  AND e.`election_status`=1 AND e.`election_id`=$user->election_id";
            
            if($request->state){
                $stc = $data['state'];
                $EciNominationStateWiseData .=" and c.st_code = '$stc'";
            }

            if($request->election_type){
                $EciNominationStateWiseData .=" and e.ELECTION_TYPEID = $request->election_type";
            }
            
            if($request->phase){
                $EciNominationStateWiseData .=" and e.ScheduleID = '$request->phase' ";
            }
            
            if($from_date && $from_to){
              $EciNominationStateWiseData .=" and c.date_of_submit between '$from_date' and '$from_to'";
            }

              $EciNominationStateWiseData .=" GROUP BY c.ac_no ORDER BY a.AC_NO";

             $EciNominationStateWiseReport = DB::select($EciNominationStateWiseData);
                  
            //dd($EciNominationStateWiseReport);  



        $data['user_data'] = $user_data;
        $data['EciNominationStateWiseReport'] = $EciNominationStateWiseReport;
        $data['stcode'] = $data['state'];
        
        
        if($request->pdf == 'yes'){
                $pdf = PDF::loadView('admin.ac.eci.EciNominationStateWiseReportPdf',['user_data' => $user_data,'EciNominationStateWiseReport' =>$data]);
                return $pdf->download('EciNominationStateWiseReportPdf_Today_'.$cur_time.'.pdf');
            }
            
            if($request->excel == 'yes'){
            $export_data = [];
            $headings[] = [$data['heading_title']];
            $export_data[] = ['AC No', 'AC Name', 'Total Nomination','Affidavit Uploaded'];
            foreach ($EciNominationStateWiseReport as $lis) {
              $export_data[] = [
                $lis->AC_NO,
                $lis->AC_NAME,
                ($lis->totalnomination)?$lis->totalnomination:'0',
                ($lis->affidavit_count)?$lis->affidavit_count:'0'
              ];
            }
          
        $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                   
            }
        
        

             return view('admin.ac.eci.EciNominationStateWiseReport',$data);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI NOMINATION STATE WISE REPORT TRY CATCH ENDS HERE
    }
    //AC ECI NOMINATION STATE WISE REPORT FUNCTION ENDS


     //AC ECI NOMINATION STATE WISE REPORT pdf function ends
public function EciNominationStateWiseReportPdf(Request $request){
  set_time_limit(6000);
    $user = Auth::user();
    $uid=$user->id;
         
    $user_data=$this->commonModel->getunewserbyuserid($uid);
    $cur_time    = Carbon::now();
    $st_code     = $user_data->st_code;
    $st_name     = $user_data->placename;

  $EciNominationStateWiseReport = $this->EciNominationStateWiseReport($request->merge(['is_excel' => 1]));
  $pdf = PDF::loadView('admin.ac.eci.EciNominationStateWiseReportPdf',['user_data' => $user_data,'EciNominationStateWiseReport' =>$EciNominationStateWiseReport]);
  return $pdf->download('EciNominationStateWiseReportPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');

}
  //AC ECI NOMINATION STATE WISE REPORT pdf function ends
  
  //AC ECI NOMINATION STATE WISE REPORT FUNCTION STARTS
    public function EciNominationStateWiseExcelReport(Request $request, $stcode,$phase= null){ 
      //AC ECI NOMINATION STATE WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $stcode        = base64_decode($xss->clean_input($request['stcode']));
            $phase         = base64_decode($xss->clean_input($request['phase']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
             //PHASE CODE
            if (!$phase) {
                 $phase = NULL;
            }else{
                $phase = $phase;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


            $stcode   = Session::put('stcode',$stcode); 
            $phase   = Session::put('phase',$phase);  
                  
            \Excel::create('EciNominationStateWiseExcelReport'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

            $stcode   = Session::get('stcode');
            $phase   = Session::get('phase');
            $user = Auth::user();  

              if(empty($phase)){

              $EciNominationStateWiseExcel = "SELECT a.ST_CODE,a.AC_NO,a.AC_NAME, COUNT(c.nom_id) AS totalnomination FROM candidate_nomination_detail AS c LEFT JOIN m_ac a ON c.ac_no = a.AC_NO AND a.ST_CODE = c.st_code LEFT JOIN m_state s ON a.ST_CODE = s.ST_CODE LEFT JOIN m_election_details e ON e.st_code=c.st_code AND c.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC'  WHERE c.st_code  = '".$stcode."' AND party_id != 1180 AND application_status != 11  AND e.`election_status`=1 AND e.`election_id`=".$user->election_id." GROUP BY c.ac_no ORDER BY a.AC_NO";
            }else{

              $EciNominationStateWiseExcel = "SELECT a.ST_CODE,a.AC_NO,a.AC_NAME, COUNT(c.nom_id) AS totalnomination  FROM candidate_nomination_detail AS c  RIGHT JOIN m_ac a ON c.ac_no = a.AC_NO AND a.ST_CODE = c.st_code RIGHT JOIN m_state s ON a.ST_CODE = s.ST_CODE LEFT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND a.AC_NO = e.CONST_NO AND CONST_TYPE = 'AC' RIGHT JOIN m_schedule sh ON e.ScheduleID = sh.SCHEDULEID WHERE c.st_code = '".$stcode."' AND party_id != 1180 AND application_status != 11 AND sh.SCHEDULEID = '".$phase."'  AND e.`election_status`=1 AND e.`election_id`=".$user->election_id." GROUP BY c.ac_no ORDER BY a.AC_NO";

            }
            
             $EciNominationStateWiseExcelReport = DB::select($EciNominationStateWiseExcel);
            
          

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciNominationStateWiseExcelReport as $EciNominationStateWise) {
                 
                 if($EciNominationStateWise->AC_NO ==''){
                   
                    $EciNominationStateWise->AC_NO = '0';

                 }

                 if($EciNominationStateWise->AC_NAME ==''){
                   
                    $EciNominationStateWise->AC_NAME = '0';

                 }

                 if($EciNominationStateWise->totalnomination ==''){
                   
                    $EciNominationStateWise->totalnomination = '0';

                 }

                 $data =  array(

                         $EciNominationStateWise->AC_NO,
                         $EciNominationStateWise->AC_NAME,
                         $EciNominationStateWise->totalnomination,
                          
                          );

                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'AC No', 'AC Name', 'Total Nomination'
                       )

                   );

                 });

            })->export('xls');    

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI NOMINATION STATE WISE REPORT TRY CATCH ENDS HERE
    }
    //AC ECI NOMINATION STATE WISE REPORT FUNCTION ENDS
  
  
  //AC ECI NOMINATION AC WISE REPORT FUNCTION STARTS
    public function EciNominationAcWiseReport(Request $request){ 
      //AC ECI NOMINATION AC WISE REPORT TRY CATCH STARTS HERE
      try{
          
         $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
       
  

          $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
        
        //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
              $GetAllElectionSchedule = $this->GetAllElectionSchedule(1);
              Session::put('ScheduleList', $GetAllElectionSchedule);
              //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
             // dd($GetAllElectionSchedule);
             
             
                 $data = [];
      $data['number_of_voting'] = 0;
      $default_phase = $request->phase;
      //$default_phase = PhaseModel::get_current_phase();

      $request_array = []; 
      $data['phases'] = PhaseModel::get_phases();
      $data['phase'] = NULL;
      if($request->has('phase')){
        if($request->phase != 'all'){
          $data['phase'] = $request->phase;
        }
        $request_array[] =  'phase='.$request->phase;
      }else{
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase='.$default_phase; 
      }
      
      if($request->has('from')){
        if($request->from){
          $data['from'] = $request->from;
        }
        $request_array[] =  'from='.$request->from;
      }
      
       if($request->has('to')){
        if($request->to){
          $data['to'] = $request->to;
        }
        $request_array[] =  'to='.$request->to;
      }
        
        
          if($user->role_id == 4){
            $this->action_state = 'acceo/EciNominationAcWiseReport';
            $request->state = $user->st_code;
        }else{
             $this->action_state = 'eci/EciNominationAcWiseReport';
             $request->state = base64_decode($request->state);
        }
        
        
        

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = $request->state;
        $request_array[] = 'state='.$request->state;
      }
      
      
      
       $data['acno'] = NULL;
      if($request->has('acno')){
        $data['acno'] = base64_decode($request->acno);
        $request_array[] = 'acno='.$request->acno;
      }

//dd($data);


      //set title
      $title_array  = [];
      $data['heading_title'] = 'Eci Nomination Ac Wise Report';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }
      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        'state' => $data['state'],
        'phase' => $data['phase']
      ];

      //$states = StateModel::get_pc_states_with_filter($filter_for_state);
      $states = DB::table('m_state')->where('ST_CODE', Auth::user()->st_code)->get();  

      $data['states'] = [];
      foreach($states as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }
      
        $data['state'] = Auth::user()->st_code;
        $acs = AcModel::get_records($data); 
       // echo "<pre>";print_r($acs);die;
//dd($acs);
      $data['acs'] = [];
      foreach($acs as $result){

        $data['acs'][] = [
            'code' => base64_encode($result['ac_no']),
            'name' => $result['ac_no'].'-'.$result['ac_name'],
        ];
      }
      
      
      
      
     // dd($data);
      
      
      
        $from_date = $from_to = '';

    if($request->has('from') && $request->has('to')){
        $from_date  = date('Y-m-d',strtotime($request->from));
        $from_to        = date('Y-m-d',strtotime($request->to));
        $request_array[] = 'from='.$request->from;
        $request_array[] = 'to='.$request->to;
        $data['from'] = date('Y-m-d',strtotime($request->from));
        $data['to'] = date('Y-m-d',strtotime($request->to));
      }


      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state).'?excel=yes&'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state).'?pdf=yes&'.implode('&', $request_array),
        'target' => true
      ];

      $data['action']         = url($this->action_state);

      $results                = [];

      
    //  dd($acno);
             
    

$acno = '';
      
   $EciNominationAcWiseData = "SELECT cn.nom_id,cd.candidate_id,cd.cand_name,a.AC_NAME,cd.cand_gender,cn.date_of_submit,
       mp.PARTYNAME,sy.SYMBOL_DES,cad.affidavit_path FROM candidate_nomination_detail AS cn 
       left join candidate_affidavit_detail as cad on cad.nom_id = cn.nom_id    
       JOIN `candidate_personal_detail` cd ON cd.candidate_id = cn.candidate_id LEFT JOIN m_ac a ON cn.ac_no = a.AC_NO AND a.ST_CODE = cn.st_code LEFT JOIN m_state s ON a.ST_CODE = s.ST_CODE LEFT JOIN m_party mp ON cn.party_id = mp.CCODE LEFT JOIN m_symbol sy ON cn.symbol_id = sy.SYMBOL_NO LEFT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND a.AC_NO = e.CONST_NO AND CONST_TYPE = 'AC' AND cn.ac_no=e.CONST_NO WHERE cn.party_id != 1180 AND cn.application_status != 11   AND e.`election_status`=1 AND e.`election_id`=".$user->election_id." ";
       
       
       
            if($request->state){
            $stc = $data['state'];
            $EciNominationAcWiseData .=" and cn.st_code = '$stc'";
            }
            
            if($request->acno){
            $acno = $data['acno'];
            $EciNominationAcWiseData .=" and cn.ac_no = '$acno'";
            }

            if($request->election_type){
                $EciNominationAcWiseData .=" and e.ELECTION_TYPEID = $request->election_type";
            }
            
            if($request->phase){
                $EciNominationAcWiseData .=" and e.ScheduleID = '$request->phase' ";
            }
            
            if($from_date && $from_to){
              $EciNominationAcWiseData .=" and cn.date_of_submit between '$from_date' and '$from_to'";
            }
       
                $EciNominationAcWiseData .=" ORDER BY cn.nom_id";
       
             $EciNominationAcWiseReport = DB::select($EciNominationAcWiseData);

         
        $data['user_data'] = $user_data;
        $data['EciNominationAcWiseReport'] = $EciNominationAcWiseReport;
        $data['stcode'] = $data['state'];
        
        
            if($request->pdf == 'yes'){
                $pdf = PDF::loadView('admin.ac.eci.EciNominationAcWiseReportPdf',['user_data' => $user_data,'EciNominationAcWiseReport' =>$EciNominationAcWiseReport]);
                return $pdf->download('EciNominationAcWiseReportPdf_Today_'.$cur_time.'.pdf');
            }
            
            if($request->excel == 'yes'){
            $export_data = [];
            $headings[] = [$data['heading_title']];
            $export_data[] = ['Candidate Name','Gender', 'AC Name', 'Party Name', 'Affidavit','Applied Date'];
            foreach ($EciNominationAcWiseReport as $lis) {
              $export_data[] = [
                $lis->cand_name,
                $lis->cand_gender,
                $lis->AC_NAME,
                $lis->PARTYNAME,
                ($lis->affidavit_path)?'Affidavit Uploaded':'No Affidavit',
                date('d-m-Y', strtotime($lis->date_of_submit))
              ];
            }
          
        $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                   
            }
      

             return view('admin.ac.eci.EciNominationAcWiseReport',$data);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI NOMINATION AC WISE REPORT TRY CATCH ENDS HERE
    }
    //AC ECI NOMINATION AC WISE REPORT FUNCTION ENDS
  
  
  //AC ECI NOMINATION AC WISE REPORT FUNCTION STARTS
    public function EciNominationAcWiseExcelReport(Request $request, $stcode, $acno){ 
      //AC ECI NOMINATION AC WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $acno        = base64_decode($xss->clean_input($request['acno']));
            $stcode      = base64_decode($xss->clean_input($request['stcode']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
            //AC CODE
            if (!$acno) {
                 $acno = NULL;
            }else{
                $acno = $acno;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
      
           $acno   = Session::put('acno',$acno); 
            $stcode   = Session::put('stcode',$stcode);  
           
             \Excel::create('EciNominationAcWiseExcelReport'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

            $stcode   = Session::get('stcode');
            $acno   = Session::get('acno');
            $user = Auth::user();   

              $EciNominationAcWiseExcel = "SELECT cn.nom_id,cd.candidate_id,cd.cand_name,a.AC_NAME,
       mp.PARTYNAME,sy.SYMBOL_DES FROM candidate_nomination_detail AS cn LEFT JOIN `candidate_personal_detail` cd ON cd.candidate_id = cn.candidate_id LEFT JOIN m_ac a ON cn.ac_no = a.AC_NO AND a.ST_CODE = cn.st_code LEFT JOIN m_state s ON a.ST_CODE = s.ST_CODE LEFT JOIN m_party mp ON cn.party_id = mp.CCODE LEFT JOIN m_symbol sy ON cn.symbol_id = sy.SYMBOL_NO LEFT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND a.AC_NO = e.CONST_NO AND CONST_TYPE = 'AC' AND cn.ac_no=e.CONST_NO WHERE cn.st_code ='".$stcode."' AND cn.ac_no  = '".$acno."' AND cn.party_id != 1180 AND cn.application_status != 11   AND e.`election_status`=1 AND e.`election_id`=".$user->election_id." ORDER BY cn.nom_id";
            
             $EciNominationAcWiseExcelReport = DB::select($EciNominationAcWiseExcel);
            
          

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciNominationAcWiseExcelReport as $EciNominationAcWise) {
                 
                 if($EciNominationAcWise->cand_name ==''){
                   
                    $EciNominationAcWise->cand_name = '0';

                 }

                 if($EciNominationAcWise->AC_NAME ==''){
                   
                    $EciNominationAcWise->AC_NAME = '0';

                 }

                 if($EciNominationAcWise->PARTYNAME ==''){
                   
                    $EciNominationAcWise->PARTYNAME = '0';

                 }

                if($EciNominationAcWise->SYMBOL_DES ==''){
                   
                    $EciNominationAcWise->SYMBOL_DES = '0';

                 }

                 $data =  array(

                         $EciNominationAcWise->cand_name,
                         $EciNominationAcWise->AC_NAME,
                         $EciNominationAcWise->PARTYNAME,
                         $EciNominationAcWise->SYMBOL_DES,
                          
                          );

                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'Candidate Name', 'AC Name', 'Party Name', 'Symbol'
                       )

                   );

                 });

            })->export('xls');    
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI NOMINATION AC WISE REPORT TRY CATCH ENDS HERE
    }
    //AC ECI NOMINATION AC WISE REPORT FUNCTION ENDS


    //AC ECI NOMINATION STATE WISE REPORT pdf function ends
      public function EciNominationAcWiseReportPdf(Request $request){
        set_time_limit(6000);
          $user = Auth::user();
          $uid=$user->id;
               
          $user_data=$this->commonModel->getunewserbyuserid($uid);
          $cur_time    = Carbon::now();
          $st_code     = $user_data->st_code;
          $st_name     = $user_data->placename;

        $EciNominationAcWiseReport = $this->EciNominationAcWiseReport($request->merge(['is_excel' => 1]));
        $pdf = PDF::loadView('admin.ac.eci.EciNominationAcWiseReportPdf',['user_data' => $user_data,'EciNominationAcWiseReport' =>$EciNominationAcWiseReport]);
        return $pdf->download('EciNominationAcWiseReportPdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');

      }
        //AC ECI NOMINATION STATE WISE REPORT pdf function ends



  
  //AC ECI VIEW NOMINATION FUNCTION STARTS
    public function EciViewNomination(Request $request, $nom_id, $cand_id){ 
      //AC ECI VIEW NOMINATION TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $nom_id        = base64_decode($xss->clean_input($request['nom_id']));
            $cand_id       = base64_decode($xss->clean_input($request['cand_id']));

            //STATE CODE
            if (!$nom_id) {
                 $nom_id = NULL;
            }else{
                $nom_id = $nom_id;
            }
            //PC CODE
            if (!$cand_id) {
                 $cand_id = NULL;
            }else{
                $cand_id = $cand_id;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


             $EciViewNominationData = "SELECT cd.*,cn.*,mp.PARTYNAME,sy.SYMBOL_DES,s.ST_NAME,a.AC_NAME
       FROM `candidate_nomination_detail` AS cn RIGHT JOIN `candidate_personal_detail` cd ON cd.candidate_id = cn.candidate_id LEFT JOIN m_party mp ON cn.party_id = mp.CCODE LEFT JOIN m_symbol sy ON cn.symbol_id = sy.SYMBOL_NO LEFT JOIN m_ac a ON cn.ac_no = a.AC_NO AND a.ST_CODE = cn.st_code LEFT JOIN m_state s ON a.ST_CODE = s.ST_CODE WHERE cn.candidate_id='".$cand_id."' AND cn.nom_id= '".$nom_id."' AND cn.party_id != 1180 AND cn.application_status != 11";
          
             $EciViewNomination = DB::select($EciViewNominationData);
                    

             return view('admin.ac.eci.EciViewNomination',['user_data' => $user_data,'EciViewNomination' =>$EciViewNomination]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI VIEW NOMINATION TRY CATCH ENDS HERE
    }
    //AC ECI VIEW NOMINATION FUNCTION ENDS
  
   //AC ECI STATE PHASE WISE DATA STATE NAME FUNCTION STARTS
    public function EciNominationStatePhase(Request $request){ 
      //AC ECI STATE PHASE WISE DATA STATE NAME TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

           
           
      $validator = Validator::make($request->all(), [ 
                'ScheduleList'      => 'required|numeric',
               
            ]);
            
            if ($validator->fails()) {
               return Redirect::back()
               ->withErrors($validator)
               ->withInput();          
            }

            $ScheduleList       = $xss->clean_input($request['ScheduleList']);

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

             $EciNominationStatePhaseData = "SELECT  ms.ST_NAME AS state_name,e.ST_CODE AS state,e.ScheduleID AS sid, e.CONST_TYPE AS ctype, s.DT_ISS_NOM AS start_nomi_date, s.LDT_IS_NOM AS last_nomi_date,s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state ms ON ms.ST_CODE= e.ST_CODE WHERE e.CONST_TYPE= 'AC' AND e.ScheduleID= '".$ScheduleList."' GROUP BY e.ST_CODE ORDER BY sid, state";
            
             $EciNominationStatePhase = DB::select($EciNominationStatePhaseData);
                  
            //dd($EciNominationStatePhaseData);      

             return view('admin.ac.eci.EciNominationStatePhase',['user_data' => $user_data,'EciNominationStatePhase' =>$EciNominationStatePhase,'ScheduleList'=>$ScheduleList]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI STATE PHASE WISE DATA STATE NAME TRY CATCH ENDS HERE
    }
    //AC ECI STATE PHASE WISE DATA STATE NAME FUNCTION ENDS
  
   //AC ECI STATE PHASE WISE DATA STATE NAME FUNCTION STARTS
    public function EciNominationStatePhaseWise(Request $request){ 
      //AC ECI STATE PHASE WISE DATA STATE NAME TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

           
           
      $validator = Validator::make($request->all(), [ 
                'ScheduleList'      => 'required|numeric',
               
            ]);
            
            if ($validator->fails()) {
               return Redirect::back()
               ->withErrors($validator)
               ->withInput();          
            }

            $ScheduleList       = $xss->clean_input($request['ScheduleList']);

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

             $EciNominationStatePhaseData = "SELECT  ms.ST_NAME AS state_name,e.ST_CODE AS state,e.ScheduleID AS sid, e.CONST_TYPE AS ctype, s.DT_ISS_NOM AS start_nomi_date, s.LDT_IS_NOM AS last_nomi_date,s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date FROM m_election_details e RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID RIGHT JOIN m_state ms ON ms.ST_CODE= e.ST_CODE WHERE e.CONST_TYPE= 'AC' AND e.ScheduleID= '".$ScheduleList."' GROUP BY e.ST_CODE ORDER BY sid, state";
            
             $EciNominationStatePhase = DB::select($EciNominationStatePhaseData);
                  
            //dd($EciNominationStatePhaseData);      

             return view('admin.ac.eci.EciNominationStatePhase',['user_data' => $user_data,'EciNominationStatePhase' =>$EciNominationStatePhase,'ScheduleList'=>$ScheduleList]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI STATE PHASE WISE DATA STATE NAME TRY CATCH ENDS HERE
    }
    //AC ECI STATE PHASE WISE DATA STATE NAME FUNCTION ENDS
  
  //AC ECI STATE PHASE WISE EXCEL DATA STATE NAME FUNCTION STARTS
    public function EciNominationStatePhaseExcel(Request $request,$ScheduleList){ 
      //AC ECI STATE PHASE WISE EXCEL DATA STATE NAME TRY CATCH STARTS HERE

      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $ScheduleList        = base64_decode($xss->clean_input($request['ScheduleList']));
           

            //STATE CODE
            if (!$ScheduleList) {
                 $ScheduleList = NULL;
            }else{
                $ScheduleList = $ScheduleList;
            }
          

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


            $phase   = Session::put('phase',$ScheduleList); 
           
                  
            \Excel::create('EciNominationStatePhaseExcelReport'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

            $phase   = Session::get('phase');
          

              $EciNominationStatePhaseExcel = "SELECT  ms.ST_NAME AS state_name,e.ST_CODE AS state, e.ScheduleID AS sid, e.CONST_TYPE AS ctype, s.DT_ISS_NOM AS start_nomi_date, s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
               FROM m_election_details e 
               RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID 
               RIGHT JOIN m_state ms ON ms.ST_CODE= e.ST_CODE 
               WHERE e.CONST_TYPE= 'AC' AND e.ScheduleID= '".$phase."'
               GROUP BY e.ST_CODE ORDER BY sid, state";
            
             $EciNominationStatePhaseData = DB::select($EciNominationStatePhaseExcel);
           

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciNominationStatePhaseData as $ScheduleData) {

                 $data =  array(
                                  $ScheduleData->sid,
                                  $ScheduleData->state_name,
                                  $ScheduleData->state,
                                  $ScheduleData->start_nomi_date,
                                  $ScheduleData->last_nomi_date,
                                  $ScheduleData->dt_nomi_scr,
                                  $ScheduleData->last_wid_date,
                                  $ScheduleData->poll_date,
                                );
                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'Phase','State','State Code','Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'
                             )

                   );

                 });

            })->export('xls');  

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI STATE PHASE WISE EXCEL DATA STATE NAME TRY CATCH ENDS HERE
    }
    //AC ECI STATE PHASE WISE EXCEL DATA STATE NAME FUNCTION ENDS
  
   //AC ECI NOMINATION AC WISE REPORT FUNCTION STARTS
    public function EciNominationAcPhaseFilter(Request $request){ 
      //AC ECI NOMINATION AC WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $data = $request->all();

            $validator = Validator::make($data, [
              'ScheduleList'      =>'required|numeric',
              ],
              [
                'ScheduleList'=>'Please Select Phase!',
                'ScheduleList.numeric'=>'Phase Should be Numeric',
              ]);

            $stcode        = base64_decode($xss->clean_input($request['stcode']));
            $phase         = $xss->clean_input($request['ScheduleList']);

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
            //PHASE CODE
            if (!$phase) {
                 $phase = NULL;
            }else{
                $phase = $phase;
            }

            $uid=$user->id;
//dd($stcode);
            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


              $EciNominationAcPhaseWiseData = "SELECT a.ST_CODE,a.AC_NO,a.AC_NAME, COUNT(c.nom_id) AS totalnomination FROM candidate_nomination_detail AS c RIGHT JOIN m_ac a ON c.ac_no = a.AC_NO AND a.ST_CODE = c.st_code RIGHT JOIN m_state s ON a.ST_CODE = s.ST_CODE RIGHT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND a.AC_NO = e.CONST_NO AND CONST_TYPE = 'AC' RIGHT JOIN m_schedule sh ON e.ScheduleID = sh.SCHEDULEID WHERE c.st_code = '".$stcode."'  AND party_id != 1180 AND application_status != 11 AND sh.SCHEDULEID = '".$phase."' GROUP BY c.ac_no ORDER BY a.AC_NO";

          
            // dd($EciNominationAcPhaseWiseData);
            
             $EciNominationAcPhaseFilter = DB::select($EciNominationAcPhaseWiseData);
                  
            //dd($EciNominationPcPhaseData);      

             return view('admin.ac.eci.EciNominationAcPhaseFilter',['user_data' => $user_data,'EciNominationAcPhaseFilter' =>$EciNominationAcPhaseFilter,'stcode' =>$stcode,'phase' =>$phase]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI NOMINATION AC WISE REPORT TRY CATCH ENDS HERE
    }
    //AC ECI NOMINATION AC WISE REPORT FUNCTION ENDS
  
  //AC ECI NOMINATION AC WISE REPORT FUNCTION STARTS
    public function EciNominationAcPhaseFilterExcel(Request $request, $stcode,$phase){ 
      //AC ECI NOMINATION AC WISE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $stcode        = base64_decode($xss->clean_input($request['stcode']));
            $phase         = base64_decode($xss->clean_input($request['phase']));

            //STATE CODE
            if (!$stcode) {
                 $stcode = NULL;
            }else{
                $stcode = $stcode;
            }
             //PHASE CODE
            if (!$phase) {
                 $phase = NULL;
            }else{
                $phase = $phase;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


            $stcode   = Session::put('stcode',$stcode); 
            $phase   = Session::put('phase',$phase);  
                  
            \Excel::create('EciNominationAcPhaseWise'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

            $stcode   = Session::get('stcode');
            $phase   = Session::get('phase');

             $EciNominationAcPhaseWiseData = "SELECT a.ST_CODE,a.AC_NO,a.AC_NAME, COUNT(c.nom_id) AS totalnomination FROM candidate_nomination_detail AS c RIGHT JOIN m_ac a ON c.ac_no = a.AC_NO AND a.ST_CODE = c.st_code RIGHT JOIN m_state s ON a.ST_CODE = s.ST_CODE RIGHT JOIN m_election_details e ON s.ST_CODE = e.ST_CODE AND a.AC_NO = e.CONST_NO AND CONST_TYPE = 'AC' RIGHT JOIN m_schedule sh ON e.ScheduleID = sh.SCHEDULEID WHERE c.st_code = '".$stcode."'  AND party_id != 1180 AND application_status != 11 AND sh.SCHEDULEID = '".$phase."' GROUP BY c.ac_no ORDER BY a.AC_NO";
            
             $EciNominationAcPhaseWiseExcelReport = DB::select($EciNominationAcPhaseWiseData);
            
          

              $arr  = array();
            
              $user = Auth::user();
              foreach ($EciNominationAcPhaseWiseExcelReport as $EciNominationAcPhaseWise) {
                 
                 if($EciNominationAcPhaseWise->AC_NO ==''){
                   
                    $EciNominationAcPhaseWise->AC_NO = '0';

                 }

                 if($EciNominationAcPhaseWise->AC_NAME ==''){
                   
                    $EciNominationAcPhaseWise->AC_NAME = '0';

                 }

                 if($EciNominationAcPhaseWise->totalnomination ==''){
                   
                    $EciNominationAcPhaseWise->totalnomination = '0';

                 }

                 $data =  array(

                         $EciNominationAcPhaseWise->AC_NO,
                         $EciNominationAcPhaseWise->AC_NAME,
                         $EciNominationAcPhaseWise->totalnomination,
                          
                          );

                          array_push($arr, $data);
                           // }
                          }
              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'AC No', 'AC Name', 'Total Nomination'
                       )

                   );

                 });

            })->export('xls');    

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI NOMINATION AC WISE REPORT TRY CATCH ENDS HERE
    }
    //AC ECI NOMINATION AC WISE REPORT FUNCTION ENDS
  
  //AC ECI PHASE INFO DATA REPORT FUNCTION STARTS
    public function EciPhaseInfoData(Request $request){ 
      //AC ECI PHASE INFO DATA REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
      
      //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
            $GetAllElectionSchedule = $this->GetAllElectionSchedule();
            //dd($GetAllElectionSchedule);
            Session::put('ScheduleList', $GetAllElectionSchedule);
            //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
            //dd($GetAllElectionSchedule);


              $EciPhaseInfoDataSelect = "SELECT s.`ST_NAME`,s.`ST_CODE`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION,COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL,COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE,COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER,COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT,COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male,COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND `CONST_TYPE`='AC' AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1  ORDER BY 2";

          
            // dd($EciPhaseNominationSelectData);
            
             $EciPhaseInfoData = DB::select($EciPhaseInfoDataSelect);
                  
            //dd($EciPhaseInfoData);      

             return view('admin.ac.eci.EciPhaseInfoData',['user_data' => $user_data,'EciPhaseInfoData' =>$EciPhaseInfoData]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC PHASE INFO DATA REPORT TRY CATCH ENDS HERE
    }
    //AC PHASE INFO DATA REPORT FUNCTION ENDS
  
   //AC ECI PHASE INFO DATA REPORT EXCEL FUNCTION STARTS
    public function EciPhaseInfoDataExcel(Request $request){ 
      //AC ECI PHASE INFO DATA REPORT EXCEL TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

     

           
            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;


            $export_data[] = [ 'Phase','State', 'Total Nominations Filed', 'National Parties', 'State Parties',
            'Other Parties','Independent ','Male','Female','Others','Total Valid Nominations'];
            $headings[] = [];
            $user = Auth::user();
            $EciPhaseInfoDataCandWiseExcel = "SELECT s.`ST_NAME`,s.`ST_CODE`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION,COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL,COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE,COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER,COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT,COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male,COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND `CONST_TYPE`='AC' AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1  ORDER BY 2";
            $EciPhaseInfoDataCandWiseExcelData = DB::select($EciPhaseInfoDataCandWiseExcel);

            $TotalNomination = 0; 
            $TotalNational = 0;
            $TotalState = 0;
            $TotalOther= 0;
            $TotalIndependent = 0;
            $TotalMale = 0;
            $TotalFemale = 0;
            $TotalOthers= 0;
            $TotalValidNomination=0;

            foreach ($EciPhaseInfoDataCandWiseExcelData as $EciPhaseInfoDataCandWise) {
                 
              if($EciPhaseInfoDataCandWise->ST_NAME ==''){
                
                 $EciPhaseInfoDataCandWise->ST_NAME = '0';

              }

              if($EciPhaseInfoDataCandWise->TOTAL_NOMINATION ==''){
                
                 $EciPhaseInfoDataCandWise->TOTAL_NOMINATION = '0';

              }

              if($EciPhaseInfoDataCandWise->NATIONAL ==''){
                
                 $EciPhaseInfoDataCandWise->NATIONAL = '0';

              }

              if($EciPhaseInfoDataCandWise->STATE ==''){
                
                 $EciPhaseInfoDataCandWise->STATE = '0';

              }

              if($EciPhaseInfoDataCandWise->OTHER ==''){
                
                 $EciPhaseInfoDataCandWise->OTHER = '0';

              }

              if($EciPhaseInfoDataCandWise->INDEPENDENT ==''){
                
                 $EciPhaseInfoDataCandWise->INDEPENDENT = '0';

              }

              if($EciPhaseInfoDataCandWise->male ==''){
                
                 $EciPhaseInfoDataCandWise->male = '0';

              }

              if($EciPhaseInfoDataCandWise->female ==''){
                
                 $EciPhaseInfoDataCandWise->female = '0';

              }

              if($EciPhaseInfoDataCandWise->others ==''){
                
                 $EciPhaseInfoDataCandWise->others = '0';

              }

              if($EciPhaseInfoDataCandWise->total ==''){
                
                 $EciPhaseInfoDataCandWise->total = '0';

              }


              $export_data[] = [
                      $EciPhaseInfoDataCandWise->StatePHASE_NO,
                      $EciPhaseInfoDataCandWise->ST_NAME,
                      $EciPhaseInfoDataCandWise->TOTAL_NOMINATION,
                      $EciPhaseInfoDataCandWise->NATIONAL,
                      $EciPhaseInfoDataCandWise->STATE,
                      $EciPhaseInfoDataCandWise->OTHER,
                      $EciPhaseInfoDataCandWise->INDEPENDENT,
                      $EciPhaseInfoDataCandWise->male,
                      $EciPhaseInfoDataCandWise->female,
                      $EciPhaseInfoDataCandWise->others,
                      $EciPhaseInfoDataCandWise->total,
      
              ];

           


             $TotalNomination             +=   $EciPhaseInfoDataCandWise->TOTAL_NOMINATION;
             $TotalNational               +=   $EciPhaseInfoDataCandWise->NATIONAL;
             $TotalState                  +=   $EciPhaseInfoDataCandWise->STATE;
             $TotalOther                  +=   $EciPhaseInfoDataCandWise->OTHER;
             $TotalIndependent            +=   $EciPhaseInfoDataCandWise->INDEPENDENT;
             $TotalMale                   +=   $EciPhaseInfoDataCandWise->male;
             $TotalFemale                 +=   $EciPhaseInfoDataCandWise->female;
             $TotalOthers                 +=   $EciPhaseInfoDataCandWise->others;
             $TotalValidNomination        +=   $EciPhaseInfoDataCandWise->total;

                       }

                  


            $name_excel = 'AC_EciNominationExcel'.'_'.$cur_time;
            return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');






         
                  
            // \Excel::create('AC_EciNominationExcel'.'_'.$cur_time, function($excel)  { 
            //   $excel->sheet('Sheet1', function($sheet)  {

            //     $user = Auth::user();

            //  $EciPhaseInfoDataCandWiseExcel = "SELECT s.`ST_NAME`,s.`ST_CODE`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION,COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL,COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE,COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER,COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT,COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male,COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND `CONST_TYPE`='AC' AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1  ORDER BY 2";
           

            //  $EciPhaseInfoDataCandWiseExcelData = DB::select($EciPhaseInfoDataCandWiseExcel);
            
          

            //   $arr  = array();

            //   $TotalNomination = 0; 
            //   $TotalNational = 0;
            //   $TotalState = 0;
            //   $TotalOther= 0;
            //   $TotalIndependent = 0;
            //   $TotalMale = 0;
            //   $TotalFemale = 0;
            //   $TotalOthers= 0;
            //   $TotalValidNomination=0;
          
            //   $user = Auth::user();

            //   foreach ($EciPhaseInfoDataCandWiseExcelData as $EciPhaseInfoDataCandWise) {
                 
            //      if($EciPhaseInfoDataCandWise->ST_NAME ==''){
                   
            //         $EciPhaseInfoDataCandWise->ST_NAME = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->TOTAL_NOMINATION ==''){
                   
            //         $EciPhaseInfoDataCandWise->TOTAL_NOMINATION = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->NATIONAL ==''){
                   
            //         $EciPhaseInfoDataCandWise->NATIONAL = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->STATE ==''){
                   
            //         $EciPhaseInfoDataCandWise->STATE = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->OTHER ==''){
                   
            //         $EciPhaseInfoDataCandWise->OTHER = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->INDEPENDENT ==''){
                   
            //         $EciPhaseInfoDataCandWise->INDEPENDENT = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->male ==''){
                   
            //         $EciPhaseInfoDataCandWise->male = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->female ==''){
                   
            //         $EciPhaseInfoDataCandWise->female = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->others ==''){
                   
            //         $EciPhaseInfoDataCandWise->others = '0';

            //      }

            //      if($EciPhaseInfoDataCandWise->total ==''){
                   
            //         $EciPhaseInfoDataCandWise->total = '0';

            //      }


            //     $data =  array(

            //              $EciPhaseInfoDataCandWise->ST_NAME,
            //              $EciPhaseInfoDataCandWise->TOTAL_NOMINATION,
            //              $EciPhaseInfoDataCandWise->NATIONAL,
            //              $EciPhaseInfoDataCandWise->STATE,
            //              $EciPhaseInfoDataCandWise->OTHER,
            //              $EciPhaseInfoDataCandWise->INDEPENDENT,
            //              $EciPhaseInfoDataCandWise->male,
            //              $EciPhaseInfoDataCandWise->female,
            //              $EciPhaseInfoDataCandWise->others,
            //              $EciPhaseInfoDataCandWise->total,
                          
            //               );


            //     $TotalNomination             +=   $EciPhaseInfoDataCandWise->TOTAL_NOMINATION;
            //     $TotalNational               +=   $EciPhaseInfoDataCandWise->NATIONAL;
            //     $TotalState                  +=   $EciPhaseInfoDataCandWise->STATE;
            //     $TotalOther                  +=   $EciPhaseInfoDataCandWise->OTHER;
            //     $TotalIndependent            +=   $EciPhaseInfoDataCandWise->INDEPENDENT;
            //     $TotalMale                   +=   $EciPhaseInfoDataCandWise->male;
            //     $TotalFemale                 +=   $EciPhaseInfoDataCandWise->female;
            //     $TotalOthers                 +=   $EciPhaseInfoDataCandWise->others;
            //     $TotalValidNomination        +=   $EciPhaseInfoDataCandWise->total;

            //               array_push($arr, $data);
            //                // }
            //               }

            //     $totalvalues = array('Total',$TotalNomination,$TotalNational,$TotalState,$TotalOther, $TotalIndependent, $TotalMale,$TotalFemale,$TotalOthers,$TotalValidNomination);
            //     // print_r($totalvalues);die;
            //     array_push($arr,$totalvalues);

            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'State', 'Total Nominations Filed', 'National Parties', 'State Parties',
            //                    'Other Parties','Independent ','Male','Female','Others','Total Valid Nominations',
            //            )

            //        );

            //      });

            // })->export('xls');    

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI PHASE INFO DATA REPORT EXCEL REPORT TRY CATCH ENDS HERE
    }
    //AC ECI PHASE INFO DATA REPORT EXCEL REPORT FUNCTION ENDS
  
   //AC ECI PHASE INFO DATA PDF REPORT FUNCTION STARTS
    public function EciPhaseInfoDataPdf(Request $request){ 
      //AC ECI PHASE INFO DATA PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
            $GetAllElectionSchedule = $this->GetAllElectionSchedule();
            Session::put('ScheduleList', $GetAllElectionSchedule);
            //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
            //dd($GetAllElectionSchedule);


              $EciPhaseInfoDataSelect = "SELECT s.`ST_NAME`,s.`ST_CODE`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION,COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL,COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE,COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER,COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT,COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male,COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total,e.StatePHASE_NO FROM candidate_nomination_detail c JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id` JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.ac_no=e.`CONST_NO` AND `party_id`!=1180 AND `CONST_TYPE`='AC' AND e.`election_id`=".$user->election_id." LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1  ORDER BY 2";

          
            // dd($EciPhaseNominationSelectData);
            
             $EciPhaseInfoDataPdf = DB::select($EciPhaseInfoDataSelect);
                  
            //dd($EciNominationPcPhaseData);      

             $pdf = PDF::loadView('admin.ac.eci.EciPhaseInfoDataPdf',['user_data' => $user_data,'EciPhaseInfoDataPdf' =>$EciPhaseInfoDataPdf]);
                        return $pdf->download('EciPhaseInfoDataPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.ac.eci.EciPhaseInfoDataPdf');   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC PHASE INFO DATA PDF REPORT TRY CATCH ENDS HERE
    }
    //AC PHASE INFO DATA PDF REPORT FUNCTION ENDS
  
   //AC ECI PHASE INFO REPORT DATA BY PHASE ID FORM FUNCTION STARTS
    public function EciPhaseInfoDataCandWiseForm(Request $request){ 
      //AC ECI PHASE INFO REPORT DATA BY PHASE ID FORM TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'phaseid'   => 'nullable|numeric|regex:/^\S*$/u',
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;

            $phaseid        = $xss->clean_input($request['phaseid']);
         //dd($phaseid);
            
            if (!$phaseid) {
                 $phaseid = "";
            }else{
                $phaseid = $phaseid;
            }


            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
                  if(empty($phaseid)){
                     return redirect('/eci/EciPhaseInfoData/'); 

                  }else{
        
             return redirect('/eci/EciPhaseInfoDataCandWise/'.base64_encode($phaseid)); 
             }        
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI PHASE INFO REPORT DATA BY PHASE ID FORM TRY CATCH ENDS HERE
    }
    //AC ECI PHASE INFO REPORT DATA BY PHASE ID FORM FUNCTION ENDS
  
  //AC ECI PHASE CANDIDATE WISE INFO DATA REPORT FUNCTION STARTS
    public function EciPhaseInfoDataCandWise(Request $request,$phaseid){ 
      //AC ECI PHASE CANDIDATE WISE INFO DATA REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

            //STATE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }
//dd($phaseid);

              $EciPhaseInfoDataCandWiseData = "SELECT s.`ST_NAME`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,
                COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total,e.`StatePHASE_NO`
                FROM candidate_nomination_detail c
                JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id`
                JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.ac_no=e.`CONST_NO` AND e.`StatePHASE_NO`='".$phaseid."' AND `party_id`!=1180 AND `CONST_TYPE`='AC' AND e.`election_id`=".$user->election_id."
                LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1";

          
            // dd($EciPhaseNominationSelectData);
            
             $EciPhaseInfoDataCandWise = DB::select($EciPhaseInfoDataCandWiseData);
                  
            //dd($EciPhaseInfoDataCandWise);      

             return view('admin.ac.eci.EciPhaseInfoDataCandWise',['user_data' => $user_data,'EciPhaseInfoDataCandWise' =>$EciPhaseInfoDataCandWise,'phaseid'=>$phaseid]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC PHASE CANDIDATE WISE INFO DATA REPORT TRY CATCH ENDS HERE
    }
    //AC PHASE CANDIDATE WISE INFO DATA REPORT FUNCTION ENDS
  
  //AC PHASE CANDIDATE WISE INFO DATA EXCEL REPORT FUNCTION STARTS
    public function EciPhaseInfoDataCandWiseExcel(Request $request,$phaseid){ 
      //AC PHASE CANDIDATE WISE INFO DATA EXCEL REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

             //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid   = Session::put('phaseid',$phaseid);  
                  
            // \Excel::create('AC_EciValidNominationAcPhaseFilterExcel'.'_'.$cur_time, function($excel)  { 
            //   $excel->sheet('Sheet1', function($sheet)  {

            $phaseid   = Session::get('phaseid');
            $user = Auth::user(); 
             $export_data[] = ['State', 'Total Nominations Filed', 'National Parties', 'State Parties',
                               'Other Parties','Independent ','Male','Female','Others','Total Valid Nominations'];
             $headings[] = [];

             $EciPhaseInfoDataCandWiseExcel = "SELECT s.`ST_NAME`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,
                COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total,e.`StatePHASE_NO`
                FROM candidate_nomination_detail c
                JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id`
                JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.ac_no=e.`CONST_NO` AND e.`StatePHASE_NO`='".$phaseid."' AND `party_id`!=1180 AND `CONST_TYPE`='AC' AND e.`election_id`=".$user->election_id."
                LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1";
           

             $EciPhaseInfoDataCandWiseExcelData = DB::select($EciPhaseInfoDataCandWiseExcel);
            
          

              $arr  = array();
        $TotalNomination = 0; 
        $TotalNational = 0;
        $TotalState = 0;
        $TotalOther= 0;
        $TotalIndependent = 0;
        $TotalMale = 0;
        $TotalFemale = 0;
        $TotalOthers = 0;
        $TotalValidNomination=0;
            
              $user = Auth::user();

                $labelis= ['State','Phase', 'Total Nominations Filed', 'National Parties', 'State Parties',
                               'Other Parties','Independent ','Male','Female','Others','Total Valid Nominations'];
                 array_push($arr,$labelis);
              foreach ($EciPhaseInfoDataCandWiseExcelData as $EciPhaseInfoDataCandWise) {
                 
                 if($EciPhaseInfoDataCandWise->ST_NAME ==''){
                   
                    $EciPhaseInfoDataCandWise->ST_NAME = '0';

                 }
                  if($EciPhaseInfoDataCandWise->StatePHASE_NO ==''){
                   
                    $EciPhaseInfoDataCandWise->StatePHASE_NO = '0';

                 }

                 if($EciPhaseInfoDataCandWise->TOTAL_NOMINATION ==''){
                   
                    $EciPhaseInfoDataCandWise->TOTAL_NOMINATION = '0';

                 }

                 if($EciPhaseInfoDataCandWise->NATIONAL ==''){
                   
                    $EciPhaseInfoDataCandWise->NATIONAL = '0';

                 }

                 if($EciPhaseInfoDataCandWise->STATE ==''){
                   
                    $EciPhaseInfoDataCandWise->STATE = '0';

                 }

                 if($EciPhaseInfoDataCandWise->OTHER ==''){
                   
                    $EciPhaseInfoDataCandWise->OTHER = '0';

                 }

                 if($EciPhaseInfoDataCandWise->INDEPENDENT ==''){
                   
                    $EciPhaseInfoDataCandWise->INDEPENDENT = '0';

                 }

                 if($EciPhaseInfoDataCandWise->male ==''){
                   
                    $EciPhaseInfoDataCandWise->male = '0';

                 }

                 if($EciPhaseInfoDataCandWise->female ==''){
                   
                    $EciPhaseInfoDataCandWise->female = '0';

                 }
         
         if($EciPhaseInfoDataCandWise->others ==''){
                   
                    $EciPhaseInfoDataCandWise->others = '0';

                 }

                 if($EciPhaseInfoDataCandWise->total ==''){
                   
                    $EciPhaseInfoDataCandWise->total = '0';

                 }


                 $data =  array(

                         $EciPhaseInfoDataCandWise->ST_NAME,
                          $EciPhaseInfoDataCandWise->StatePHASE_NO,
                         $EciPhaseInfoDataCandWise->TOTAL_NOMINATION,
                         $EciPhaseInfoDataCandWise->NATIONAL,
                         $EciPhaseInfoDataCandWise->STATE,
                         $EciPhaseInfoDataCandWise->OTHER,
                         $EciPhaseInfoDataCandWise->INDEPENDENT,
                         $EciPhaseInfoDataCandWise->male,
                         $EciPhaseInfoDataCandWise->female,
             $EciPhaseInfoDataCandWise->others,
                         $EciPhaseInfoDataCandWise->total,
                          
                          );
        
         $TotalNomination             +=   $EciPhaseInfoDataCandWise->TOTAL_NOMINATION;
         $TotalNational               +=   $EciPhaseInfoDataCandWise->NATIONAL;
         $TotalState                  +=   $EciPhaseInfoDataCandWise->STATE;
         $TotalOther                  +=   $EciPhaseInfoDataCandWise->OTHER;
         $TotalIndependent            +=   $EciPhaseInfoDataCandWise->INDEPENDENT;
         $TotalMale                   +=   $EciPhaseInfoDataCandWise->male;
         $TotalFemale                 +=   $EciPhaseInfoDataCandWise->female;
         $TotalOthers                 +=   $EciPhaseInfoDataCandWise->others;
         $TotalValidNomination        +=   $EciPhaseInfoDataCandWise->total;

                          array_push($arr, $data);
                           // }
                          }
           $totalvalues = array('Total','',$TotalNomination,$TotalNational,$TotalState,$TotalOther,$TotalIndependent,$TotalMale,$TotalFemale,$TotalOthers,$TotalValidNomination);
                // print_r($totalvalues);die;
                  array_push($arr,$totalvalues);



                  $headings[] = ["AC_EciValidNominationAcPhaseFilterExcel_: ".date("d-m-Y")];
               $name_excel = "AC_EciValidNominationAcPhaseFilterExcel";
              return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'State', 'Total Nominations Filed', 'National Parties', 'State Parties',
            //                    'Other Parties','Independent ','Male','Female','Others','Total Valid Nominations',
            //            )

            //        );

            //      });

            // })->export('xls');    

             
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC PHASE CANDIDATE WISE INFO DATA EXCEL REPORT TRY CATCH ENDS HERE
    


    }
    //AC PHASE CANDIDATE WISE INFO DATA EXCEL REPORT FUNCTION ENDS
  
  //AC PHASE CANDIDATE WISE INFO DATA PDF REPORT FUNCTION STARTS
    public function EciPhaseInfoDataCandWisePdf(Request $request,$phaseid){ 
      //AC PHASE CANDIDATE WISE INFO DATA PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

             //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

             $EciPhaseInfoDataCandWiseData = "SELECT s.`ST_NAME`,COUNT(IF(application_status!=11,c.`candidate_id`,NULL)) TOTAL_NOMINATION, COUNT(IF(`cand_party_type`='N' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) NATIONAL, COUNT(IF(`cand_party_type`='S' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) STATE, COUNT(IF(`cand_party_type` IN ('U','0') AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) OTHER, COUNT(IF(`cand_party_type`='Z' AND application_status IN (5,6) AND `finalaccepted`=1,c.`candidate_id`,NULL)) INDEPENDENT, COUNT(IF(`cand_gender`='male' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) male, COUNT(IF(`cand_gender`='female' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) female,COUNT(IF(`cand_gender`='third' AND `cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) others,
                COUNT(IF(`cand_party_type` IN ('N','S','U','0','Z') AND application_status IN (5,6) AND `finalaccepted`=1,d.`candidate_id`,NULL)) total,e.`StatePHASE_NO`
                FROM candidate_nomination_detail c
                JOIN `candidate_personal_detail` d ON d.`candidate_id`=c.`candidate_id`
                JOIN `m_election_details` e ON e.ST_CODE=c.st_code AND c.ac_no=e.`CONST_NO` AND e.`StatePHASE_NO`='".$phaseid."' AND `party_id`!=1180 AND `CONST_TYPE`='AC' AND e.`election_id`=".$user->election_id."
                LEFT JOIN `m_state` s ON s.ST_CODE=c.st_code GROUP BY 1";
           

             $EciPhaseInfoDataCandWisePdf = DB::select($EciPhaseInfoDataCandWiseData);

             //PHASE DATES
            if($phaseid != ''){

              $PhaseInfo = getschedulebyid($phaseid);
            }else{ $PhaseInfo = "";}

             $pdf = PDF::loadView('admin.ac.eci.EciPhaseInfoDataCandWisePdf',['user_data' => $user_data,'EciPhaseInfoDataCandWisePdf' =>$EciPhaseInfoDataCandWisePdf,'phaseid'=>$phaseid,'PhaseInfo'=>$PhaseInfo]);
                        return $pdf->download('AC_EciPhaseInfoDataCandWisePdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.ac.eci.EciPhaseInfoDataCandWisePdf');  
                   
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC PHASE CANDIDATE WISE INFO DATA PDF REPORT TRY CATCH ENDS HERE
    }
    //AC PHASE CANDIDATE WISE INFO DATA PDF REPORT FUNCTION ENDS
  
  //AC ECI NOMINATION FINALIZED REPORT FUNCTION STARTS
    public function EciNominationFinalized(Request $request){ 
      //AC ECI NOMINATION FINALIZED REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
            $GetAllElectionSchedule = $this->GetAllElectionSchedule();
         //   dd($GetAllElectionSchedule);
            Session::put('ScheduleList', $GetAllElectionSchedule);
            //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
            //dd($GetAllElectionSchedule);


          /* $EciNominationFinalizedSelect = "SELECT e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' GROUP BY e.ScheduleID";
*/
           $EciNominationFinalizedSelect = "SELECT e.StatePHASE_NO AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' GROUP BY e.StatePHASE_NO";

            
             $EciNominationFinalized = DB::select($EciNominationFinalizedSelect);
                  
            //dd($EciNominationFinalized);      

             return view('admin.ac.eci.EciNominationFinalized',['user_data' => $user_data,'EciNominationFinalized' =>$EciNominationFinalized]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC NOMINATION FINALIZED REPORT TRY CATCH ENDS HERE
    }
    //AC NOMINATION FINALIZED REPORT FUNCTION ENDS
  
  //AC NOMINATION FINALIZED REPORT EXCEL REPORT STARTS
    public function EciNominationFinalizedExcel(Request $request){  
      //AC NOMINATION FINALIZED REPORT EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
             
              $export_data[] = ['Phase No.', 'No of Total ACs', 'Finalized ACs'];
             $headings[] = [];

             /* $EciNominationFinalizedExcelSelect = "SELECT e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' GROUP BY e.ScheduleID"; */

                $EciNominationFinalizedExcelSelect ="SELECT e.StatePHASE_NO AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' GROUP BY e.StatePHASE_NO";
              $EciNominationFinalizedExcelData = DB::select($EciNominationFinalizedExcelSelect);
             //dd($EciActiveUsers);  
 
               $arr  = array();
               $TotalAc = 0; 
               $TotalFinalized = 0;
             
               $user = Auth::user();
               foreach ($EciNominationFinalizedExcelData as $FinalizedData) {
                 
                  if($FinalizedData->sid ==''){
                    
                     $FinalizedData->sid = '0';
 
                  }
 
                  if($FinalizedData->total_ac ==''){
                    
                     $FinalizedData->total_ac = '0';
 
                  }
 
                  if($FinalizedData->finalized_ac ==''){
                    
                     $FinalizedData->finalized_ac = '0';
 
                  }
 
                  $export_data[] = [
                    $FinalizedData->sid,
                    $FinalizedData->total_ac,
                    $FinalizedData->finalized_ac,
          
                  ];

 
                 $TotalAc        += $FinalizedData->total_ac;
                 $TotalFinalized += $FinalizedData->finalized_ac;
 
                            // }
                           }


               $name_excel = 'AC_EciNominationFinalizedExcel'.'_'.$cur_time;
      return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


              \Excel::create('AC_EciNominationFinalizedExcel'.'_'.$cur_time, function($excel)  { 
              $excel->sheet('Sheet1', function($sheet)  {

           /*   $EciNominationFinalizedExcelSelect = "SELECT e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' GROUP BY e.ScheduleID";

*/
               $EciNominationFinalizedExcelSelect ="SELECT e.StatePHASE_NO AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' GROUP BY e.StatePHASE_NO";

            
             $EciNominationFinalizedExcelData = DB::select($EciNominationFinalizedExcelSelect);
            //dd($EciActiveUsers);  

              $arr  = array();
              $TotalAc = 0; 
              $TotalFinalized = 0;
            
              $user = Auth::user();
              foreach ($EciNominationFinalizedExcelData as $FinalizedData) {
                
                 if($FinalizedData->sid ==''){
                   
                    $FinalizedData->sid = '0';

                 }

                 if($FinalizedData->total_ac ==''){
                   
                    $FinalizedData->total_ac = '0';

                 }

                 if($FinalizedData->finalized_ac ==''){
                   
                    $FinalizedData->finalized_ac = '0';

                 }

                 $data =  array(
                          $FinalizedData->sid,
                          $FinalizedData->total_ac,
                          $FinalizedData->finalized_ac,
                                ); 

                $TotalAc        += $FinalizedData->total_ac;
                $TotalFinalized += $FinalizedData->finalized_ac;

                          array_push($arr, $data);
                           // }
                          }

                $totalvalues = array('Total',$TotalAc,$TotalFinalized);
                // print_r($totalvalues);die;
                  array_push($arr,$totalvalues);

              $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                               'Phase No.', 'No of Total ACs', 'Finalized ACs'
                             )

                   );

                 });

            })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //AC NOMINATION FINALIZED REPORT EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //AC NOMINATION FINALIZED REPORT EXCEL REPORT FUNCTION ENDS
  
  //AC ECI NOMINATION FINALIZED PDF REPORT FUNCTION STARTS
    public function EciNominationFinalizedPdf(Request $request){ 
      //AC ECI NOMINATION FINALIZED PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

           /*$EciNominationFinalizedSelect = "SELECT e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' GROUP BY e.ScheduleID";
*/
            $EciNominationFinalizedSelect ="SELECT e.StatePHASE_NO AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' GROUP BY e.StatePHASE_NO";

            
             $EciNominationFinalizedPdf = DB::select($EciNominationFinalizedSelect);
                  
            //dd($EciNominationFinalizedPdf);      

             $pdf = PDF::loadView('admin.ac.eci.EciNominationFinalizedPdf',['user_data' => $user_data,'EciNominationFinalizedPdf' =>$EciNominationFinalizedPdf]);
            return $pdf->download('AC_EciNominationFinalizedPdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
            return view('admin.ac.eci.EciNominationFinalizedPdf');     
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC NOMINATION FINALIZED PDF REPORT TRY CATCH ENDS HERE
    }
    //AC NOMINATION FINALIZED PDF REPORT FUNCTION ENDS
  
   //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID FORMFUNCTION STARTS
    public function EciNominationFinalizedByPhaseIdForm(Request $request){ 
      //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID FORM TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $validator = Validator::make($request->all(), [ 
                    'phaseid'   => 'nullable|numeric|regex:/^\S*$/u',
                ]);

            if ($validator->fails()) {
                   return Redirect::back()
                   ->withErrors($validator)
                   ->withInput();          
                }

            $xss = new xssClean;

            $phaseid        = $xss->clean_input($request['phaseid']);
         
            
            if (!$phaseid) {
                 $phaseid = "";
            }else{
                $phaseid = $phaseid;
            }


            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
                  
        
             return redirect('/eci/EciNominationFinalizedByPhaseId/'.base64_encode($phaseid));         
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID FORM TRY CATCH ENDS HERE
    }
    //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID FORM FUNCTION ENDS
  
  //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID REPORT FUNCTION STARTS
    public function EciNominationFinalizedByPhaseId(Request $request,$phaseid){ 
      //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

            //STATE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }


           $EciNominationFinalizedByPhaseIdSelect = "SELECT s.ST_CODE,s.ST_NAME AS state_name,e.StatePHASE_NO AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' AND e.StatePHASE_NO='".$phaseid."' GROUP BY s.ST_CODE";

            
             $EciNominationFinalizedByPhaseId = DB::select($EciNominationFinalizedByPhaseIdSelect);
                  
            //dd($EciNominationFinalizedByPhaseId);      

             return view('admin.ac.eci.EciNominationFinalizedByPhaseId',['user_data' => $user_data,'EciNominationFinalizedByPhaseId' =>$EciNominationFinalizedByPhaseId,'phaseid' => $phaseid]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID REPORT TRY CATCH ENDS HERE
    }
    //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID REPORT FUNCTION ENDS
  
   //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID EXCEL REPORT STARTS
    public function EciNominationFinalizedByPhaseIdExcel(Request $request,$phaseid){  
      //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
              
              $xss = new xssClean;
              $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

              //STATE CODE
              if (!$phaseid) {
                   $phaseid = NULL;
              }else{
                  $phaseid = $phaseid;
              }

              $phaseid = Session::put('phaseid',$phaseid); 
               
                          

              // \Excel::create('AC_EciNominationFinalizedByPhaseIdExcel'.'_'.$cur_time, function($excel)  { 
              // $excel->sheet('Sheet1', function($sheet)  {

                $phaseid = Session::get('phaseid');

              $EciNominationFinalizedByPhaseIdExcelSelect = "SELECT s.ST_CODE,s.ST_NAME AS state_name,e.StatePHASE_NO AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' AND e.StatePHASE_NO='".$phaseid."' GROUP BY s.ST_CODE";

            
             $EciNominationFinalizedByPhaseIdExcelData = DB::select($EciNominationFinalizedByPhaseIdExcelSelect);
            //dd($EciActiveUsers);  

              $arr  = array();
              $TotalAc = 0; 
              $TotalFinalized = 0;
              $labelis=["State Name","No of Total ACs","Finalized ACs"];
                 array_push($arr,$labelis);
     
              $user = Auth::user();
              foreach ($EciNominationFinalizedByPhaseIdExcelData as $FinalizedData) {
                
                 if($FinalizedData->state_name ==''){
                   
                    $FinalizedData->state_name = '0';

                 }

                 if($FinalizedData->total_ac ==''){
                   
                    $FinalizedData->total_ac = '0';

                 }

                 if($FinalizedData->finalized_ac ==''){
                   
                    $FinalizedData->finalized_ac = '0';

                 }

                 $data =  array(
                          $FinalizedData->state_name,
                          $FinalizedData->total_ac,
                          $FinalizedData->finalized_ac,
                                ); 

                $TotalAc        += $FinalizedData->total_ac;
                $TotalFinalized += $FinalizedData->finalized_ac;

                  array_push($arr, $data);
                         
                           // }
                          }
//array_push($arr,$labelis);
                $totalvalues = array('Total',$TotalAc,$TotalFinalized);
                // print_r($totalvalues);die;
                
                 
                  array_push($arr,$totalvalues);


                  $headings[] = ["Date: ".date("d-m-Y")];
               $name_excel = "Candidate_CA_Report";
              return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'State Name', 'No of Total ACs', 'Finalized ACs'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID EXCEL REPORT FUNCTION ENDS
  
  //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID PDF REPORT FUNCTION STARTS
    public function EciNominationFinalizedByPhaseIdPdf(Request $request,$phaseid){ 
      //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));

            //STATE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }


           $EciNominationFinalizedByPhaseIdSelect = "SELECT s.ST_CODE,s.ST_NAME AS state_name,e.StatePHASE_NO AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' AND e.StatePHASE_NO='".$phaseid."' GROUP BY s.ST_CODE";

            
             $EciNominationFinalizedByPhaseIdPdf = DB::select($EciNominationFinalizedByPhaseIdSelect);
                  
            //dd($EciNominationFinalizedByPhaseId); 

            //PHASE DATES
            if($phaseid != ''){

              $PhaseInfo = getschedulebyid($phaseid);
            }else{ $PhaseInfo = "";}     

             $pdf = PDF::loadView('admin.ac.eci.EciNominationFinalizedByPhaseIdPdf',['user_data' => $user_data,'EciNominationFinalizedByPhaseIdPdf' =>$EciNominationFinalizedByPhaseIdPdf,'phaseid' => $phaseid,'PhaseInfo'=>$PhaseInfo]);
                        return $pdf->download('AC_EciNominationFinalizedByPhaseIdPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.ac.eci.EciNominationFinalizedByPhaseIdPdf');  
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID PDF REPORT TRY CATCH ENDS HERE
    }
    //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID PDF REPORT FUNCTION ENDS
  
   //ECI AC NOMINATION FINALIZED AC DATA BY PHASE ID AND STATE CODE REPORT FUNCTION STARTS
    public function EciNominationFinalizedByStatePhaseId(Request $request,$phaseid,$statecode){ 
      //ECI AC NOMINATION FINALIZED AC DATA BY PHASE ID AND STATE CODE REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));
            $statecode         = base64_decode($xss->clean_input($request['statecode']));

            //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }
            //STATE CODE
            if (!$statecode) {
                 $statecode = NULL;
            }else{
                $statecode = $statecode;
            }


           $EciNominationFinalizedByPhaseIdState = "SELECT a.AC_NO,a.AC_NAME,s.ST_CODE,s.ST_NAME AS state_name,e.StatePHASE_NO AS sid,IF(c.finalized_ac='1','Yes','No') AS finalized_ac FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_ac a ON e.st_code =  a.ST_CODE AND a.AC_NO=e.CONST_NO WHERE c.CONST_TYPE='AC' AND e.StatePHASE_NO='".$phaseid."' AND s.ST_CODE='".$statecode."' ORDER BY a.AC_NO";

            
             $EciNominationFinalizedByStatePhaseId = DB::select($EciNominationFinalizedByPhaseIdState);
                  
            //dd($EciNominationFinalizedByStatePhaseId);      

             return view('admin.ac.eci.EciNominationFinalizedByStatePhaseId',['user_data' => $user_data,'EciNominationFinalizedByStatePhaseId' =>$EciNominationFinalizedByStatePhaseId,'phaseid' => $phaseid,'statecode' => $statecode]);   
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //ECI AC NOMINATION FINALIZED AC DATA BY PHASE ID AND STATE CODE REPORT TRY CATCH ENDS HERE
    }
    //ECI AC NOMINATION FINALIZED AC DATA BY PHASE ID AND STATE CODE REPORT FUNCTION ENDS


     //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID EXCEL REPORT STARTS
    public function EciNominationFinalizedByStatePhaseIdExcel(Request $request,$phaseid,$statecode){  
      //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID EXCEL REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $d=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
              
              $xss = new xssClean;

              $phaseid         = base64_decode($xss->clean_input($request['phaseid']));
              $statecode         = base64_decode($xss->clean_input($request['statecode']));

              //PHASE CODE
              if (!$phaseid) {
                   $phaseid = NULL;
              }else{
                  $phaseid = $phaseid;
              }
              //STATE CODE
              if (!$statecode) {
                   $statecode = NULL;
              }else{
                  $statecode = $statecode;
              }

              $phaseid = Session::put('phaseid',$phaseid); 
              $statecode = Session::put('statecode',$statecode); 
               
                          

              // \Excel::create('AC_EciNominationFinalizedByPhaseIdExcel'.'_'.$cur_time, function($excel)  { 
              // $excel->sheet('Sheet1', function($sheet)  {

                $phaseid = Session::get('phaseid');
                $statecode = Session::get('statecode');

              $EciNominationFinalizedByStatePhaseIdExcelSelect = "SELECT a.AC_NO,a.AC_NAME,s.ST_CODE,s.ST_NAME AS state_name,e.StatePHASE_NO AS sid,IF(c.finalized_ac='1','Yes','No') AS finalized_ac FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_ac a ON e.st_code =  a.ST_CODE AND a.AC_NO=e.CONST_NO WHERE c.CONST_TYPE='AC' AND e.StatePHASE_NO='".$phaseid."' AND s.ST_CODE='".$statecode."' ORDER BY a.AC_NO";

            
             $EciNominationFinalizedByStatePhaseIdExcelData = DB::select($EciNominationFinalizedByStatePhaseIdExcelSelect);
            //dd($EciActiveUsers);  

              $arr  = array();

              $labelis=["AC No","No of Total ACs","Finalized ACs"];
                 array_push($arr,$labelis);
            
              $user = Auth::user();
              foreach ($EciNominationFinalizedByStatePhaseIdExcelData as $FinalizedData) {
                
                 if($FinalizedData->AC_NO ==''){
                   
                    $FinalizedData->AC_NO = '0';

                 }

                 if($FinalizedData->AC_NAME ==''){
                   
                    $FinalizedData->AC_NAME = '0';

                 }

                 if($FinalizedData->finalized_ac ==''){
                   
                    $FinalizedData->finalized_ac = '0';

                 }

                 $data =  array(
                                $FinalizedData->AC_NO,
                                $FinalizedData->AC_NAME,
                                $FinalizedData->finalized_ac,
                                ); 
                          array_push($arr, $data);
                           // }
                          }

                          


                  $headings[] = ["Date: ".date("d-m-Y")];
               $name_excel = "AC_EciNominationFinalizedByPhaseIdExcel";
              return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
            //   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
            //                    'AC No', 'No of Total ACs', 'Finalized ACs'
            //                  )

            //        );

            //      });

            // })->export('xls');
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }
    //AC ECI NOMINATION FINALIZED AC DATA BY PHASE ID EXCEL REPORT FUNCTION ENDS


    //ECI AC NOMINATION FINALIZED AC DATA BY PHASE ID AND STATE CODE PDF REPORT FUNCTION STARTS
    public function EciNominationFinalizedByStatePhaseIdPdf(Request $request,$phaseid,$statecode){ 
      //ECI AC NOMINATION FINALIZED AC DATA BY PHASE ID AND STATE CODE PDF REPORT TRY CATCH STARTS HERE
      try{
          
          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $uid=$user->id;

            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;

            $phaseid         = base64_decode($xss->clean_input($request['phaseid']));
            $statecode         = base64_decode($xss->clean_input($request['statecode']));

            //PHASE CODE
            if (!$phaseid) {
                 $phaseid = NULL;
            }else{
                $phaseid = $phaseid;
            }
            //STATE CODE
            if (!$statecode) {
                 $statecode = NULL;
            }else{
                $statecode = $statecode;
            }


           $EciNominationFinalizedByPhaseIdState = "SELECT a.AC_NO,a.AC_NAME,s.ST_CODE,s.ST_NAME AS state_name,e.ScheduleID AS sid,IF(c.finalized_ac='1','Yes','No') AS finalized_ac FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code =  e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_state s ON e.st_code =  s.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE LEFT JOIN m_ac a ON e.st_code =  a.ST_CODE AND a.AC_NO=e.CONST_NO WHERE c.CONST_TYPE='AC' AND e.ScheduleID='".$phaseid."' AND s.ST_CODE='".$statecode."' ORDER BY a.AC_NO";

            
             $EciNominationFinalizedByStatePhaseIdPdf = DB::select($EciNominationFinalizedByPhaseIdState);
                  
            //dd($EciNominationFinalizedByStatePhaseId);  

            //STATE NAME 
            if($statecode != ''){

              $statelist = getstatebystatecode($statecode);
              $state     = $statelist->ST_NAME;

            }else{ $state = "";}

            //PHASE DATES
            if($phaseid != ''){

              $PhaseInfo = getschedulebyid($phaseid);
            }else{ $PhaseInfo = "";}



             $pdf = PDF::loadView('admin.ac.eci.EciNominationFinalizedByStatePhaseIdPdf',['user_data' => $user_data,'EciNominationFinalizedByStatePhaseIdPdf' =>$EciNominationFinalizedByStatePhaseIdPdf,'phaseid' => $phaseid,'state' => $state,'PhaseInfo'=>$PhaseInfo]);
                        return $pdf->download('AC_EciNominationFinalizedByStatePhaseIdPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view('admin.ac.eci.EciNominationFinalizedByStatePhaseIdPdf');     
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //ECI AC NOMINATION FINALIZED AC DATA BY PHASE ID AND STATE CODE PDF REPORT TRY CATCH ENDS HERE
    }
    //ECI AC NOMINATION FINALIZED AC DATA BY PHASE ID AND STATE CODE PDF REPORT FUNCTION ENDS



    

}  // end class