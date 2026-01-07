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

class BoothCountingSummaryReportController  extends Controller{

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
    public function BoothCountingSummaryReport(Request $request){  
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
                          
              $EciCountingSelectData = "SELECT ST_CODE,ST_NAME,TOTAL_AC,COUNTING_STARTED,RESULT_DECLARED,NOT_DECLARED,
CONCAT(ROUND((RESULT_DECLARED/TOTAL_AC*100),2),'%') AS PERCENTAGE,total_round 
FROM(
SELECT s.ST_CODE,s.ST_NAME, COUNT(`e`.`CONST_NO`)TOTAL_AC,
COUNT(IF(w.lead_cand_name!='null' AND w.lead_cand_name!='',ac.AC_NAME,NULL))COUNTING_STARTED,
COUNT(IF(w.status='1',ac.AC_NAME,NULL))RESULT_DECLARED,
COUNT(IF(w.status='0',ac.AC_NAME,NULL))NOT_DECLARED,
SUM(r.scheduled_round) AS total_round
FROM `m_election_details` AS `e` 
LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`)
LEFT JOIN `round_master` AS `r` ON (`r`.`AC_NO` = `e`.`CONST_NO` AND `r`.`ST_CODE` = `e`.`ST_CODE`)
LEFT JOIN winning_leading_candidate AS w ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' 
AND `election_status`=1 AND e.`election_id`=".$user->election_id."
WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user->election_id." 
GROUP BY 1) result ORDER BY ST_NAME";
            
             $EciCountingStatusReport = DB::select($EciCountingSelectData);
			 	 
				 
			$dataArr = array();	 
			foreach($EciCountingStatusReport as $data){
				
				$st_code = strtolower($data->ST_CODE);
				
				$query = "SELECT SUM(temp.total) as total FROM (SELECT  `complete_round` AS total FROM `counting_master_$st_code` GROUP BY ac_no) AS temp";
				$total_round_completed = DB::select($query);
				
				$query2 = "SELECT SUM(temp.total) as total FROM (SELECT (CASE WHEN (scheduled_round = '0') Then 1 ELSE 0 END) as total FROM `round_master` where st_code = '$st_code' GROUP BY ac_no) AS temp";
				$not_scheduled_round = DB::select($query2);
				
				$query3 = "SELECT SUM(temp.total) as total 
				FROM (SELECT  (CASE WHEN (complete_round < scheduled_round) Then 1 ELSE 0 END) as total FROM `counting_master_$st_code` as cm
				join round_master on round_master.ac_no = cm.ac_no and round_master.st_code = '$st_code'
				GROUP BY round_master.ac_no
				) AS temp";
				$total_round_pending = DB::select($query3);
				
				//dd($not_start);
				
				$dataArr[] =(object)[
					"ST_CODE" => $data->ST_CODE,
					"ST_NAME" => $data->ST_NAME,
					"TOTAL_AC" => $data->TOTAL_AC,
					"COUNTING_STARTED" => $data->COUNTING_STARTED,
					"RESULT_DECLARED" => $data->RESULT_DECLARED,
					"NOT_DECLARED" => $data->NOT_DECLARED,
					"PERCENTAGE" => $data->PERCENTAGE,
					"total_round" => $data->total_round,
					"total_round_completed" => @$total_round_completed[0]->total,
					"total_round_pending" => @$total_round_pending[0]->total,
				];
			} 
			
		
			 
 $max_round_pending=DB::select(DB::raw("SELECT temp.st_code, ST_NAME as st_name, temp.ac_no,AC_NAME as ac_name, pendinground  FROM
(
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s03` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's03')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s06` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's06')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s27` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's27')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s10` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's10')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s11` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's11')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s12` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's12')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s13` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's13')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s16` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's16')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s17` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's17')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_u07` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 'u07')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s20` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's20')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s22` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's22')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s25` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's25')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s28` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's28')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s29` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's29')
) AS temp
join m_state on m_state.st_code = temp.st_code
join m_ac on m_ac.st_code = temp.st_code and m_ac.ac_no = temp.ac_no
 GROUP BY temp.st_code, temp.ac_no+0  ORDER BY pendinground DESC LIMIT 10"));
			// dd($max_round_pending);
			 
			 
			 
			 
			 $query = "SELECT * from  winning_leading_candidate where status = '0' ";
		//$query .= " order by leading_id asc"; 
		$query .= " order by st_code,ac_no desc"; 
		$result = DB::select($query);
			 
			 
		//dd($result);	 
			 
			 

             return view($this->view_path.'.CountingSummaryReport',['user_data' => $user_data,'EciCountingStatusReport' => $dataArr,'result'=>$result,'max_round_pending'=>$max_round_pending]);

               
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
    public function BoothCountingSummaryExcel(Request $request){  
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
    public function BoothCountingSummaryPdf(Request $request){  
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
                          
              $EciCountingSelectData = "SELECT ST_CODE,ST_NAME,TOTAL_AC,COUNTING_STARTED,RESULT_DECLARED,NOT_DECLARED,
CONCAT(ROUND((RESULT_DECLARED/TOTAL_AC*100),2),'%') AS PERCENTAGE,total_round 
FROM(
SELECT s.ST_CODE,s.ST_NAME, COUNT(`e`.`CONST_NO`)TOTAL_AC,
COUNT(IF(w.lead_cand_name!='null' AND w.lead_cand_name!='',ac.AC_NAME,NULL))COUNTING_STARTED,
COUNT(IF(w.status='1',ac.AC_NAME,NULL))RESULT_DECLARED,
COUNT(IF(w.status='0',ac.AC_NAME,NULL))NOT_DECLARED,
SUM(r.scheduled_round) AS total_round
FROM `m_election_details` AS `e` 
LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`)
LEFT JOIN `round_master` AS `r` ON (`r`.`AC_NO` = `e`.`CONST_NO` AND `r`.`ST_CODE` = `e`.`ST_CODE`)
LEFT JOIN winning_leading_candidate AS w ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' 
AND `election_status`=1 AND e.`election_id`=".$user->election_id."
WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user->election_id." 
GROUP BY 1) result ORDER BY ST_NAME";
            
             $EciCountingStatusReport = DB::select($EciCountingSelectData);
			 	 
				 
			$dataArr = array();	 
			foreach($EciCountingStatusReport as $data){
				
				$st_code = strtolower($data->ST_CODE);
				
				$query = "SELECT SUM(temp.total) as total FROM (SELECT  `complete_round` AS total FROM `counting_master_$st_code` GROUP BY ac_no) AS temp";
				$total_round_completed = DB::select($query);
				
				$query2 = "SELECT SUM(temp.total) as total FROM (SELECT (CASE WHEN (scheduled_round = '0') Then 1 ELSE 0 END) as total FROM `round_master` where st_code = '$st_code' GROUP BY ac_no) AS temp";
				$not_scheduled_round = DB::select($query2);
				
				$query3 = "SELECT SUM(temp.total) as total 
				FROM (SELECT  (CASE WHEN (complete_round < scheduled_round) Then 1 ELSE 0 END) as total FROM `counting_master_$st_code` as cm
				join round_master on round_master.ac_no = cm.ac_no and round_master.st_code = '$st_code'
				GROUP BY round_master.ac_no
				) AS temp";
				$total_round_pending = DB::select($query3);
				
				//dd($not_start);
				
				$dataArr[] =(object)[
					"ST_CODE" => $data->ST_CODE,
					"ST_NAME" => $data->ST_NAME,
					"TOTAL_AC" => $data->TOTAL_AC,
					"COUNTING_STARTED" => $data->COUNTING_STARTED,
					"RESULT_DECLARED" => $data->RESULT_DECLARED,
					"NOT_DECLARED" => $data->NOT_DECLARED,
					"PERCENTAGE" => $data->PERCENTAGE,
					"total_round" => $data->total_round,
					"total_round_completed" => @$total_round_completed[0]->total,
					"total_round_pending" => @$total_round_pending[0]->total,
				];
			} 
			
			//dd($dataArr);
			 
			 
 $max_round_pending=DB::select(DB::raw("SELECT temp.st_code, ST_NAME as st_name, temp.ac_no,AC_NAME as ac_name, pendinground  FROM
(
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s03` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's03')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s06` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's06')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s27` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's27')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s10` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's10')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s11` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's11')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s12` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's12')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s13` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's13')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s16` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's16')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s17` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's17')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_u07` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 'u07')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s20` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's20')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s22` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's22')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s25` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's25')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s28` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's28')
UNION
(SELECT t1.complete_round,t1.ac_no, t2.scheduled_round,t2.st_code, (cast(t2.scheduled_round as signed) - cast(t1.complete_round  as signed)) AS pendinground FROM `counting_master_s29` t1, round_master t2 WHERE t1.ac_no=t2.ac_no and t2.st_code = 's29')
) AS temp
join m_state on m_state.st_code = temp.st_code
join m_ac on m_ac.st_code = temp.st_code and m_ac.ac_no = temp.ac_no
 GROUP BY temp.st_code, temp.ac_no+0  ORDER BY pendinground DESC LIMIT 10"));
			// dd($max_round_pending);
			 
			 
			 
			 
			 $query = "SELECT * from  winning_leading_candidate where status = '0' ";
		//$query .= " order by leading_id asc"; 
		$query .= " order by st_code,ac_no desc"; 
		$result = DB::select($query);
			 
			 
		//dd($result);	 
			 
			 

            // return view($this->view_path.'.CountingSummaryReport',['user_data' => $user_data,'EciCountingStatusReport' => $dataArr,'result'=>$result,'max_round_pending'=>$max_round_pending]);
			 
			 
             $pdf = PDF::loadView($this->view_path.'.CountingSummaryReportPdf',['user_data' => $user_data,'EciCountingStatusReport' => $dataArr,'result'=>$result,'max_round_pending'=>$max_round_pending]);
                        return $pdf->download('CountingSummaryReportPdf'.trim($st_name).'_Today_'.$cur_time.'.pdf');
                        return view($this->view_path.'.CountingSummaryReportPdf');             
               
            }else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI COUNTING RESULT DATA PDF REPORT TRY CATCH BLOCK ENDS
        
    }
    //ECI COUNTING RESULT DATA PDF REPORT FUNCTION ENDS

}    