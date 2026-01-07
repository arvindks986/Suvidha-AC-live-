<?php  
namespace App\Http\Controllers\Admin\counting;
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
use \MPDF;
use \PDF;
use App\commonModel;
use App\adminmodel\Electioncurrentelection;
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use Illuminate\Support\Facades\Schema;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use App\models\Counting\BoothCountingModel;
use App\adminmodel\ACCountingModel;
use App\models\Counting\UsercountingModel;  
use App\models\Admin\StateModel;
use App\models\Admin\AcModel;
use App\models\Admin\DistrictModel;
use App\models\Counting\BoothDistricts;
use App\models\Counting\BoothCountingTableModel;


use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;


class BoothCountingStatusReportController  extends Controller{

    public $base          = 'eci';
    public $folder        = 'counting';
    public $action_state  = 'eci/counting/counting_status';
    public $action_ac     = 'eci/counting/counting_status/state/ac';
    public $view_path     = "admin.counting.reports";

    public function __construct(){
        $this->middleware(['auth:admin','auth']);
        //$this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->boothcounting=new BoothCountingModel;
        $this->users=new UsercountingModel;
        $this->CountingModel = new ACCountingModel();
        
        if(!Auth::check()){ 
          return redirect('/officer-login');
        }
    }

    protected function guard(){
      return Auth::guard('admin');
    }
    
    //ECI COUNTING RESULT DATA REPORT STARTS
    public function BoothCountingStatusReport(Request $request){  
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
                          
              $EciCountingSelectData = "SELECT ST_CODE,ST_NAME,TOTAL_AC,COUNTING_STARTED,RESULT_DECLARED,
CONCAT(ROUND((RESULT_DECLARED/TOTAL_AC*100),2),'%') AS PERCENTAGE 
FROM(
SELECT s.ST_CODE,s.ST_NAME, COUNT(`e`.`CONST_NO`)TOTAL_AC,
COUNT(IF(w.lead_cand_name!='null' AND w.lead_cand_name!='',ac.AC_NAME,NULL))COUNTING_STARTED,
COUNT(IF(w.status='1',ac.AC_NAME,NULL))RESULT_DECLARED
FROM `m_election_details` AS `e` 
LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`)
LEFT JOIN winning_leading_candidate AS w ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' 
AND `election_status`=1 AND e.`election_id`=".$user->election_id."
WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user->election_id." 
GROUP BY 1) result ORDER BY ST_NAME";
            
             $EciCountingStatusReport = DB::select($EciCountingSelectData);

             return view($this->view_path.'.CountingStatusReport',['user_data' => $user_data,'EciCountingStatusReport' => $EciCountingStatusReport]);

               
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
    public function BoothCountingStatusExcel(Request $request){  
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
              
              $EciCountingSelectData ="SELECT ST_CODE,ST_NAME,TOTAL_AC,COUNTING_STARTED,RESULT_DECLARED,
              CONCAT(ROUND((RESULT_DECLARED/TOTAL_AC*100),2),'%') AS PERCENTAGE 
              FROM(
              SELECT s.ST_CODE,s.ST_NAME, COUNT(`e`.`CONST_NO`)TOTAL_AC,
              COUNT(IF(w.lead_cand_name!='null' AND w.lead_cand_name!='',ac.AC_NAME,NULL))COUNTING_STARTED,
              COUNT(IF(w.status='1',ac.AC_NAME,NULL))RESULT_DECLARED
              FROM `m_election_details` AS `e` 
              LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
              LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`)
              LEFT JOIN winning_leading_candidate AS w ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' 
              AND `election_status`=1 AND e.`election_id`=".$user->election_id."
              WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user->election_id." 
              GROUP BY 1) result ORDER BY ST_NAME";
                          
              $EciCountingData = DB::select($EciCountingSelectData);

              $headings[]=[];
              $TotalAc= 0;
              $TotalCountingStarted = 0;
              $TotalDeclared = 0;

              foreach ($EciCountingData as $CountingData) {

                if($CountingData->COUNTING_STARTED ==''){
                   
                    $CountingData->COUNTING_STARTED = '0';

                 }

                 if($CountingData->RESULT_DECLARED ==''){
                   
                    $CountingData->RESULT_DECLARED = '0';

                 }

                 $export_data[] = [
                                  $CountingData->ST_NAME,
                                  $CountingData->TOTAL_AC,
                                  $CountingData->COUNTING_STARTED,
                                  $CountingData->RESULT_DECLARED,
                                  $CountingData->PERCENTAGE,
                ];

                
        $TotalAc              += $CountingData->TOTAL_AC;
                $TotalCountingStarted += $CountingData->COUNTING_STARTED;
                $TotalDeclared        += $CountingData->RESULT_DECLARED;
                          
                          }

              

              $name_excel = 'AC_CountingStatusExcel_'.trim($st_name).'_'.$cur_time;
              return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');



//               \Excel::create('AC_CountingStatusExcel_'.trim($st_name).'_'.$cur_time, function($excel) { 
//               $excel->sheet('Sheet1', function($sheet) {

//               $user = Auth::user();   

//               $EciCountingSelectData ="SELECT ST_CODE,ST_NAME,TOTAL_AC,COUNTING_STARTED,RESULT_DECLARED,
// CONCAT(ROUND((RESULT_DECLARED/TOTAL_AC*100),2),'%') AS PERCENTAGE 
// FROM(
// SELECT s.ST_CODE,s.ST_NAME, COUNT(`e`.`CONST_NO`)TOTAL_AC,
// COUNT(IF(w.lead_cand_name!='null' AND w.lead_cand_name!='',ac.AC_NAME,NULL))COUNTING_STARTED,
// COUNT(IF(w.status='1',ac.AC_NAME,NULL))RESULT_DECLARED
// FROM `m_election_details` AS `e` 
// LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
// LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`)
// LEFT JOIN winning_leading_candidate AS w ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' 
// AND `election_status`=1 AND e.`election_id`=".$user->election_id."
// WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user->election_id." 
// GROUP BY 1) result ORDER BY ST_NAME";
            
//              $EciCountingData = DB::select($EciCountingSelectData);
//             //dd($PcCeoCountingData);  

//               $arr  = array();
//         $TotalAc= 0;
//         $TotalCountingStarted = 0;
//         $TotalDeclared = 0;
            
//               $user = Auth::user();
//               foreach ($EciCountingData as $CountingData) {

//                 if($CountingData->COUNTING_STARTED ==''){
                   
//                     $CountingData->COUNTING_STARTED = '0';

//                  }

//                  if($CountingData->RESULT_DECLARED ==''){
                   
//                     $CountingData->RESULT_DECLARED = '0';

//                  }

//                  $data =  array(
//                                   $CountingData->ST_NAME,
//                                   $CountingData->TOTAL_AC,
//                                   $CountingData->COUNTING_STARTED,
//                                   $CountingData->RESULT_DECLARED,
//                                   $CountingData->PERCENTAGE,
//                                 );
//         $TotalAc              += $CountingData->TOTAL_AC;
//                 $TotalCountingStarted += $CountingData->COUNTING_STARTED;
//                 $TotalDeclared        += $CountingData->RESULT_DECLARED;
//                           array_push($arr, $data);
//                            // }
//                           }
//          $totalvalues = array('Total',$TotalAc,$TotalCountingStarted,$TotalDeclared);
//                 // print_r($totalvalues);die;
//                   array_push($arr,$totalvalues);
//               $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
//                                'State', 'No Of ACs','Counting Started in ACs', 'Result Declared in ACs', '% Of Results'
//                              )

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
        //ECI COUNTING RESULT EXCEL DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI COUNTING RESULT EXCEL DATA REPORT FUNCTION ENDS
  
  //ECI COUNTING RESULT DATA PDF REPORT STARTS
    public function BoothCountingStatusPdf(Request $request){  
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
                          
              $EciCountingSelectData = "SELECT ST_CODE,ST_NAME,TOTAL_AC,COUNTING_STARTED,RESULT_DECLARED,
CONCAT(ROUND((RESULT_DECLARED/TOTAL_AC*100),2),'%') AS PERCENTAGE 
FROM(
SELECT s.ST_CODE,s.ST_NAME, COUNT(`e`.`CONST_NO`)TOTAL_AC,
COUNT(IF(w.lead_cand_name!='null' AND w.lead_cand_name!='',ac.AC_NAME,NULL))COUNTING_STARTED,
COUNT(IF(w.status='1',ac.AC_NAME,NULL))RESULT_DECLARED
FROM `m_election_details` AS `e` 
LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`)
LEFT JOIN winning_leading_candidate AS w ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' 
AND `election_status`=1 AND e.`election_id`=".$user->election_id."
WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user->election_id." 
GROUP BY 1) result ORDER BY ST_NAME";
            
             $EciCountingStatusReportPdf = DB::select($EciCountingSelectData);

             $pdf = PDF::loadView($this->view_path.'.CountingStatusReportPdf',['user_data' => $user_data,'EciCountingStatusReportPdf' =>$EciCountingStatusReportPdf]);
                        return $pdf->download('AC_EciCountingStatusReportPdf_'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view($this->view_path.'.CountingStatusReportPdf');             
               
            }else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI COUNTING RESULT DATA PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI COUNTING RESULT DATA PDF REPORT FUNCTION ENDS


    //PC CEO COUNTING RESULT DATA REPORT STARTS
    public function BoothCountingStatusCeo(Request $request){
      //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;

              if(!empty(Auth::user()->dist_no)){
                $dist_no = Auth::user()->dist_no;
              }else{ $dist_no = null;}

              if(!empty($request->state)){
                        $st_code =$request->state;
              }else{ $st_code = Auth::user()->st_code;}


              if(Auth::user()->role_id == '5'){

              $this->action_state  = 'acdeo/counting/counting_status';
              $this->action_ac     = 'acdeo/counting/counting_status/state/ac';

              $st_object = StateModel::get_state_by_code($st_code);
              $dt_object = DistrictModel::get_district(['st_code'=>$st_code, 'dist_no'=>$dist_no]);
            }

              if(Auth::user()->role_id == 5){

                $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
                                          IF(lead_cand_name!='null' OR lead_cand_name != '','STARTED','NOT STARTED') AS counting ,
                                          IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
                                          FROM winning_leading_candidate w RIGHT JOIN m_ac a ON w.st_code=a.ST_CODE 
                                          AND w.ac_no=a.AC_NO RIGHT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 WHERE a.ST_CODE='".$st_code."' AND e.`election_id`=".$user->election_id." AND a.DIST_NO_HDQTR='".$dist_no."' ";

              }elseif(Auth::user()->role_id == 4 || Auth::user()->role_id == 7){

                $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
IF(lead_cand_name!='null' OR lead_cand_name != '','STARTED','NOT STARTED') AS counting ,
IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
FROM `m_election_details` AS `e`
 JOIN `m_ac` AS `a` ON (`a`.`AC_NO` = `e`.`CONST_NO` AND `a`.`ST_CODE` = `e`.`ST_CODE`)
 JOIN winning_leading_candidate AS w ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` 
AND `CONST_TYPE`='AC' AND `election_status`=1 
WHERE a.ST_CODE='".$st_code."' AND e.`election_id`=".$user->election_id."";

              }
                          

              
            
              $CountingStatus = DB::select($PcCeoCountingSelectData);


              return view($this->view_path.'.CountingStatus',['user_data' => $user_data,'CountingStatus' =>$CountingStatus]);
            

               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //PC CEO COUNTING RESULT DATA REPORT FUNCTION ENDS
  
  
  //PC CEO COUNTING RESULT DATA EXCEL REPORT STARTS
    public function BoothCountingStatusCeoExcel(Request $request){
     
      //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;
              $user = Auth::user();   
                
              if(!empty(Auth::user()->dist_no)){
              $dist_no = Auth::user()->dist_no;
            }else{ $dist_no = null;}
            
            if(!empty($request->state)){
                      $st_code =$request->state;
            }else{ $st_code = Auth::user()->st_code;}

            if(Auth::user()->role_id == '5'){

            $this->action_state  = 'acdeo/counting/counting_status';
            $this->action_ac     = 'acdeo/counting/counting_status/state/ac';

            $st_object = StateModel::get_state_by_code($st_code);
            $dt_object = DistrictModel::get_district(['st_code'=>$st_code, 'dist_no'=>$dist_no]);
          }

            if(Auth::user()->role_id == 5){

              $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
                                        IF(lead_cand_name!='null' OR lead_cand_name != '','STARTED','NOT STARTED') AS counting ,
                                        IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
                                        FROM winning_leading_candidate w RIGHT JOIN m_ac a ON w.st_code=a.ST_CODE 
                                        AND w.ac_no=a.AC_NO RIGHT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 WHERE a.ST_CODE='".$st_code."' AND e.`election_id`=".$user->election_id." AND a.DIST_NO_HDQTR='".$dist_no."' ";

            }elseif(Auth::user()->role_id == 4 || Auth::user()->role_id == 7){

              $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
IF(lead_cand_name!='null' OR lead_cand_name != '','STARTED','NOT STARTED') AS counting ,
IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
FROM `m_election_details` AS `e`
JOIN `m_ac` AS `a` ON (`a`.`AC_NO` = `e`.`CONST_NO` AND `a`.`ST_CODE` = `e`.`ST_CODE`)
JOIN winning_leading_candidate AS w ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` 
AND `CONST_TYPE`='AC' AND `election_status`=1 
WHERE a.ST_CODE='".$st_code."' AND e.`election_id`=".$user->election_id."";

            }
          
           $PcCeoCountingData = DB::select($PcCeoCountingSelectData);
          // dd($PcCeoCountingData);  

            $arr  = array();
          
            $user = Auth::user();
            $export_data = [];
            $headings[] = [];
            foreach ($PcCeoCountingData as $CountingData) {
              
              $export_data[] = [
                $CountingData->ano,
                $CountingData->aac,
                $CountingData->counting,
                $CountingData->res_declare,
              ];

              //  $data =  array(
              //                   $CountingData->ano,
              //                   $CountingData->aac,
              //                   $CountingData->counting,
              //                   $CountingData->res_declare,
              //                 );
              //           array_push($arr, $data);
                        //  }
                        }
                        $date = date('Y-m-d-H:i:s');
                        $name_excel = $date.'-counting-status-report';

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


                        // $data= json_decode(json_encode($arr), true);
                        
                        // $type='csv';
                        // return \Excel::create($date.'-counting-status-report', function($excel) use ($data) {
                        //       $excel->sheet('mySheet', function($sheet) use ($data)
                        //       {
                        //           $sheet->fromArray($data);
                        //       });
                        //   })->download($type);
               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //PC CEO COUNTING RESULT DATA EXCEL REPORT  TRY CATCH BLOCK ENDS
        
    }
    //PC CEO COUNTING RESULT DATA EXCEL REPORT FUNCTION ENDS
  
  //PC CEO COUNTING RESULT PDF DATA REPORT STARTS
    public function BoothCountingStatusCeoPdf(Request $request){  
      //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $cur_time    = Carbon::now();
              if(!empty(Auth::user()->dist_no)){
                $dist_no = Auth::user()->dist_no;
              }else{ $dist_no = null;}

              if(!empty($request->state)){
                        $st_code =$request->state;
              }else{ $st_code = Auth::user()->st_code;}
              $st_name = $user_data->placename;
                          

              if(Auth::user()->role_id == '5'){

              $this->action_state  = 'acdeo/counting/counting_status';
              $this->action_ac     = 'acdeo/counting/counting_status/state/ac';

              $st_object = StateModel::get_state_by_code($st_code);
              $dt_object = DistrictModel::get_district(['st_code'=>$st_code, 'dist_no'=>$dist_no]);
            }

              if(Auth::user()->role_id == 5){

                $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
                                          IF(lead_cand_name!='null' OR lead_cand_name != '','STARTED','NOT STARTED') AS counting ,
                                          IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
                                          FROM winning_leading_candidate w RIGHT JOIN m_ac a ON w.st_code=a.ST_CODE 
                                          AND w.ac_no=a.AC_NO RIGHT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 WHERE a.ST_CODE='".$st_code."' AND e.`election_id`=".$user->election_id." AND a.DIST_NO_HDQTR='".$dist_no."' ";

              }elseif(Auth::user()->role_id == 4 || Auth::user()->role_id == 7){

                $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
IF(lead_cand_name!='null' OR lead_cand_name != '','STARTED','NOT STARTED') AS counting ,
IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
FROM `m_election_details` AS `e`
 JOIN `m_ac` AS `a` ON (`a`.`AC_NO` = `e`.`CONST_NO` AND `a`.`ST_CODE` = `e`.`ST_CODE`)
 JOIN winning_leading_candidate AS w ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO`
AND `CONST_TYPE`='AC' AND `election_status`=1 
WHERE a.ST_CODE='".$st_code."' AND e.`election_id`=".$user->election_id."";

              }
            
              $CountingStatus = DB::select($PcCeoCountingSelectData);


              $pdf = PDF::loadView($this->view_path.'.CountingStatusPdf',['user_data' => $user_data,'CountingStatus' =>$CountingStatus]);
            return $pdf->download('CountingStatusPdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
            return view($this->view_path.'.CountingStatusPdf');
            

               
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //PC CEO COUNTING RESULT PDF DATA REPORT TRY CATCH BLOCK ENDS
        
    }
    //PC CEO COUNTING RESULT PDF DATA REPORT FUNCTION ENDS

}    