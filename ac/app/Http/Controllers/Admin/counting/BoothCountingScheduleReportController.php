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
use Validator;
use Config;
use PDF;
// use Excel;
use App\commonModel;  
use App\models\Admin\ReportModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class BoothCountingScheduleReportController extends Controller {

	public $view_path     = "admin.counting.reports.eci";
	public $aro           = "aro";
	public $ropc          = "admin.countingReport.scheduleReport.ropc";
	public $eci           = "eci";
	public $ceo           = "admin.countingReport.scheduleReport.eco";
    protected $userId;

    protected $election_id; 
   
	
    public function __construct() {
		$this->middleware(['auth:admin','auth']);
        //$this->middleware('eci');
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
            }
            $this->userId = \Auth::id(); // you can access user id here
            $this->election_id  = Auth::user()->election_id;

            return $next($request);
        });
    }
	
	public function BoothCounting_main_ScheduleReport(Request $request)
	{

	  $user_data = Auth::user();
	  $heading_title = 'Scheduled Rounds Report';
	 /* $data['m_state']=DB::table('m_state')
					  ->join('m_election_details',[
				        ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
				      ])
				      ->where('m_election_details.CONST_TYPE','AC')
				      ->where('election_status','1')
				      ->where('m_election_details.ELECTION_ID',$this->election_id)
				      ->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE')->get();*/

		$data['m_state']= $statevalue = StateModel::get_states(); 

	//   $result=DB::select(DB::raw("SELECT r.scheduled_round AS S_ROUND,s.ST_CODE AS STATE,ac.AC_NO AS AC_NO FROM `m_election_details` AS `e` LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN round_master AS r ON e.st_code=r.st_code AND r.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user_data->election_id." WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user_data->election_id." ORDER BY e.st_code,ac.AC_NO"));	
	  
	$sql = "SELECT s.ST_CODE AS STATE, s.ST_NAME,  
	COUNT(ac.AC_NO) AS ac_no_count, 
	SUM(CASE WHEN r.postal_total_votes > 0 THEN 1 ELSE 0 END) AS  postal_total_votes_publish,
	SUM(CASE WHEN crfv.finalize_type = 2 THEN 1 ELSE 0 END) AS  finalize_type_publish
	FROM `m_election_details` AS `e` 
	LEFT JOIN `counting_result_finalize_verification` AS `crfv` ON (`crfv`.`AC_NO` = `e`.`CONST_NO` AND `crfv`.`ST_CODE` = `e`.`ST_CODE` AND `crfv`.`finalize_type` = 2) 
	LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
	LEFT JOIN `m_ac` AS `ac` ON ( `ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) 
	LEFT JOIN round_master AS r ON e.st_code = r.st_code AND r.ac_no = e.`CONST_NO` AND `election_status` = 1 
	WHERE `e`.`election_status` = 1
	GROUP BY `STATE` ORDER BY e.st_code";
	if(Auth::user()->officername == 'ECIECI2'){
		$result=DB::select(DB::raw($sql));
	}else{
		$result=DB::select(DB::raw("SELECT SUM(r.scheduled_round) AS S_ROUND, s.ST_CODE AS STATE, s.ST_CODE, COUNT(ac.AC_NO) AS ac_no_count FROM `m_election_details` AS `e` LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN `m_ac` AS `ac` ON ( `ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN round_master AS r ON e.st_code = r.st_code AND r.ac_no = e.`CONST_NO` AND `CONST_TYPE` = 'AC' AND `election_status` = 1 AND e.`election_id` = $user_data->election_id WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = $user_data->election_id GROUP BY `STATE` ORDER BY e.st_code"));
	}
	  $state=strip_tags(trim($request->state_code));
	  $ac_no=strip_tags(trim($request->ac_no)); 
	  $ac="";
	  $state_query="";
	  $urlpdf='0/0';
	  $urlexcel='0/0';  
	  
	 // dd($state);
	  
	  
	  if(isset($state) && isset($ac_no) && $state!="")
	  {			
           if($state)  
		   {
			 //echo $state; die;
			 $state_query=" WHERE e.st_code='$state'"; 
		   }
		   if(isset($ac_no) && $ac_no!=0)
		   {
			  $ac=" AND ac.ac_no=$ac_no";
		   }
        $result=DB::select(DB::raw("SELECT r.scheduled_round AS S_ROUND,s.ST_CODE AS STATE,ac.AC_NO AS AC_NO,r.pc_no AS PC_NO FROM `m_election_details` AS `e` LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN round_master AS r ON e.st_code=r.st_code AND r.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user_data->election_id." $state_query $ac and `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user_data->election_id."  ORDER BY e.st_code,ac.AC_NO"));
        
		$urlpdf=$state.'/'.$ac_no;
	    $urlexcel=$state.'/'.$ac_no;	

	  }

	  if($request->has('is_export')){
		  return ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'urlpdf'=>$urlpdf,'urlexcel'=>$urlexcel,'ac_no'=>$ac_no,'heading_title'=>$heading_title];
	  }
	//   dd(['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'urlpdf'=>$urlpdf,'urlexcel'=>$urlexcel,'ac_no'=>$ac_no,'heading_title'=>$heading_title]);
	  return view($this->view_path.'.eci-main-schedule-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'urlpdf'=>$urlpdf,'urlexcel'=>$urlexcel,'ac_no'=>$ac_no,'heading_title'=>$heading_title]);
	}
	
	
		public function BoothCountingSchedulePDF_main(Request $request)
	{
	  $user_data =   Auth::user();	 
	  $heading_title = 'Schedule Round Report PDF';
	  $state='';
	  $ac_no='';
	  $result = $this->BoothCounting_main_ScheduleReport($request->merge([ 'is_export'=>'1' ]))['result'];
	  $date = date('Y-m-d-H:i:s');
	  $pdf = PDF::loadView($this->view_path.'.eci-schedule-report-main-pdf', compact('result','ac_no','state','user_data','heading_title'));
	  return $pdf->download($date.'-eci-schedule-report.pdf');
	}
	
	public function BoothCountingScheduleExcel_main(Request $request)
	{
	  $user_data =   Auth::user();
	  $heading_title = 'Schedule Report Excel';  
      $state_query="";
	  $ac="";	  
	  $result = $this->BoothCounting_main_ScheduleReport($request->merge([ 'is_export'=>'1' ]))['result'];
	  $dataResult=[];
	  if(Auth::user()->officername == 'ECIECI2'){
		  $headings[]=['S No','State','Total ACs','Total Round Setup Done By ACs','Round Setup Pending By ACs', ' Total Postal Publish By ACs','Finalize Publish By ACs'];
		}else{
			$headings[]=['S No','State','Total ACs','Total Round Setup Done By ACs','Round Setup Pending By ACs'];
	  }	
	  $export_data[]=[];
	  $final_total_acs=0; 
	 $final_acscheduledRound=0;
	 $final_total_pending_ac=0;
	 $grand_total_postal_total_votes_publish = 0;
                                $grand_total_finalize_type_publish = 0;
	  for($i=0;$i<count($result);$i++)
		{
			$total_ac = !empty($result[$i]->ac_no_count) ? $result[$i]->ac_no_count : '0';
			$acscheduledRound=completeRound_ac_total($result[$i]->STATE);
			$pending_ac = abs($total_ac-$acscheduledRound);

			$final_total_acs=$final_total_acs+$total_ac;
			$final_acscheduledRound=$final_acscheduledRound+$acscheduledRound;
			$final_total_pending_ac=$final_total_pending_ac+$pending_ac;
			if(Auth::user()->officername == 'ECIECI2'){
				$grand_total_postal_total_votes_publish  += $result[$i]->postal_total_votes_publish;
				$grand_total_finalize_type_publish += $result[$i]->finalize_type_publish;

			}
			if(Auth::user()->officername == 'ECIECI2'){
				$export_data[] = [
	
				$i+1,
				getstatebystatecode($result[$i]->STATE)->ST_NAME,
				$total_ac,
				$acscheduledRound,
				($pending_ac != 0) ? $pending_ac : '0',
				$result[$i]->postal_total_votes_publish,
				$result[$i]->finalize_type_publish,
					
					
		  
				  ];

			}else{
				$export_data[] = [
	
					$i+1,
					getstatebystatecode($result[$i]->STATE)->ST_NAME,
					$total_ac,
					$acscheduledRound,
					($pending_ac != 0) ? $pending_ac : '0',
					  ];
			}


		}

		if(Auth::user()->officername == 'ECIECI2'){
			$export_data[]=[
				'Total',
				'',
				$final_total_acs,
				$final_acscheduledRound,
				$final_total_pending_ac,
				$grand_total_postal_total_votes_publish,
				$grand_total_finalize_type_publish,
			];

		}else{
			$export_data[]=[
				'Total',
				'',
				$final_total_acs,
				$final_acscheduledRound,
				$final_total_pending_ac,
			];

		}
	  
	  $data= json_decode(json_encode($dataResult), true);
      $date = date('Y-m-d-H:i:s');
	  $type='csv';
	$name_excel=$date.'-eci-schedule-report';
	  
	  return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

    //   return Excel::create($date.'-eci-schedule-report', function($excel) use ($data) {
    //         $excel->sheet('mySheet', function($sheet) use ($data)
    //         {
    //             $sheet->fromArray($data);
    //         });
    //     })->download($type);
	}
  
	public function BoothCountingScheduleReport(Request $request)
	{


	  $user_data = Auth::user();
	  $heading_title = 'Scheduled Rounds Report';
	 /* $data['m_state']=DB::table('m_state')
					  ->join('m_election_details',[
				        ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
				      ])
				      ->where('m_election_details.CONST_TYPE','AC')
				      ->where('election_status','1')
				      ->where('m_election_details.ELECTION_ID',$this->election_id)
				      ->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE')->get();*/

		$data['m_state']= $statevalue = StateModel::get_states(); 

	//   $result=DB::select(DB::raw("SELECT r.scheduled_round AS S_ROUND,s.ST_CODE AS STATE,ac.AC_NO AS AC_NO FROM `m_election_details` AS `e` LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN round_master AS r ON e.st_code=r.st_code AND r.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user_data->election_id." WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user_data->election_id." ORDER BY e.st_code,ac.AC_NO"));	
	  $sql = "SELECT  r.scheduled_round AS S_ROUND, s.ST_CODE AS STATE, s.ST_NAME, ac.AC_NO, ac.ac_name, 
	  (CASE WHEN r.postal_total_votes > 0 THEN 'Yes' ELSE 'No' END) AS  postal_total_votes_publish,
	  (CASE WHEN crfv.finalize_type = 2 THEN 'Yes' ELSE 'No' END) AS  finalize_type_publish
	  FROM `m_election_details` AS `e` 
	  LEFT JOIN `counting_result_finalize_verification` AS `crfv` ON (`crfv`.`AC_NO` = `e`.`CONST_NO` AND `crfv`.`ST_CODE` = `e`.`ST_CODE` AND `crfv`.`finalize_type` = 2) 
	  LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
	  LEFT JOIN `m_ac` AS `ac` ON ( `ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) 
	  LEFT JOIN round_master AS r ON e.st_code = r.st_code AND r.ac_no = e.`CONST_NO` AND `election_status` = 1 
	  WHERE `e`.`election_status` = 1
	  GROUP BY `STATE`, ac.`AC_NO` ORDER BY e.st_code";
		if (Auth::user()->officername == 'ECIECI2') {
			$result=DB::select(DB::raw($sql));	
		}else{
			$result=DB::select(DB::raw("SELECT r.scheduled_round AS S_ROUND,s.ST_CODE AS STATE,ac.AC_NO AS AC_NO FROM `m_election_details` AS `e` LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN round_master AS r ON e.st_code=r.st_code AND r.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user_data->election_id." WHERE `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user_data->election_id." ORDER BY e.st_code,ac.AC_NO"));	
		}
	  $state=strip_tags(trim($request->state_code));
	  $ac_no=strip_tags(trim($request->ac_no)); 
	  $ac="";
	  $state_query="";
	  $urlpdf='0/0';
	  $urlexcel='0/0';  
	  if(isset($state) && isset($ac_no) && $state!="" && $state!="0")
	  {		
		if (Auth::user()->officername == 'ECIECI2') {
			if($state)  
			{
			  //echo $state; die;
			  $state_query=" e.st_code='$state' AND "; 
			}
			if(isset($ac_no) && $ac_no!=0)
			{
			   $ac=" ac.ac_no=$ac_no AND";
			}
			$sql = "SELECT  r.scheduled_round AS S_ROUND, s.ST_CODE AS STATE, s.ST_NAME, ac.AC_NO, ac.ac_name, 
			 (CASE WHEN r.postal_total_votes > 0 THEN 'Yes' ELSE 'No' END) AS  postal_total_votes_publish,
			 (CASE WHEN crfv.finalize_type = 2 THEN 'Yes' ELSE 'No' END) AS  finalize_type_publish
			 FROM `m_election_details` AS `e` 
			 LEFT JOIN `counting_result_finalize_verification` AS `crfv` ON (`crfv`.`AC_NO` = `e`.`CONST_NO` AND `crfv`.`ST_CODE` = `e`.`ST_CODE` AND `crfv`.`finalize_type` = 2) 
			 LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
			 LEFT JOIN `m_ac` AS `ac` ON ( `ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) 
			 LEFT JOIN round_master AS r ON e.st_code = r.st_code AND r.ac_no = e.`CONST_NO` AND `election_status` = 1 
			 WHERE ".$state_query.$ac."`e`.`election_status` = 1
			 GROUP BY `STATE`, ac.`AC_NO` ORDER BY ac.`AC_NO`";
			 $result=DB::select(DB::raw($sql));
		}else{
			if($state)  
			{
			  //echo $state; die;
			  $state_query="where e.st_code='$state'"; 
			}
			if(isset($ac_no) && $ac_no!=0)
			{
			   $ac=" AND ac.ac_no=$ac_no ";
			}
			$result=DB::select(DB::raw("SELECT r.scheduled_round AS S_ROUND,s.ST_CODE AS STATE,ac.AC_NO AS AC_NO,r.pc_no AS PC_NO FROM `m_election_details` AS `e` LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN round_master AS r ON e.st_code=r.st_code AND r.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user_data->election_id." $state_query $ac and `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user_data->election_id."  ORDER BY e.st_code,ac.AC_NO"));

		}
        
        // dd($result);
		$urlpdf=$state.'/'.$ac_no;
	    $urlexcel=$state.'/'.$ac_no;	
	  }


    //   echo "<pre>";print_r($urlexcel);die;
	  return view($this->view_path.'.eci-schedule-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'urlpdf'=>$urlpdf,'urlexcel'=>$urlexcel,'ac_no'=>$ac_no,'heading_title'=>$heading_title]);
	}
	
	public function BoothCountingSchedulePDF($state="",$ac_no="")
	{
	  $user_data =   Auth::user();	 
	  $heading_title = 'Schedule Round Report PDF';
	  $state_name=DB::table('m_state')->where('ST_CODE',$state)->first();	
      $ac_name=DB::table('m_ac')->where('ST_CODE',$state)->where('AC_NO',$ac_no)->first();
	  $state_query="";
	  $ac="";	  
	  if($state)
		{
	    $state_query=" WHERE e.st_code='$state'";
		}
	  if($ac_no!="" && $ac_no!=0)
		{
	     $ac=" AND ac.ac_no=$ac_no";
		}
      $result=DB::select(DB::raw("SELECT r.scheduled_round AS S_ROUND,s.ST_CODE AS STATE,ac.AC_NO AS AC_NO,r.pc_no AS PC_NO FROM `m_election_details` AS `e` LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN round_master AS r ON e.st_code=r.st_code AND r.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user_data->election_id." $state_query $ac and `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user_data->election_id."  ORDER BY e.st_code,ac.AC_NO"));	
	  $date = date('Y-m-d-H:i:s');
	  $pdf = PDF::loadView($this->view_path.'.eci-schedule-report-pdf', compact('result','date','ac_no','state','user_data','heading_title'));
	  return $pdf->download($date.'-eci-schedule-report.pdf');
	}
	
	public function BoothCountingScheduleExcel($state="",$ac_no="")
	{
		
	  $user_data =   Auth::user();
	  $heading_title = 'Schedule Report Excel';  
      $state_query="";
	  $ac="";	  
	//   if($state)
	// 	{
	// 	$state_query=" WHERE e.st_code='$state'";
	// 	}
	//  if($ac_no!="" && $ac_no!=0)
	// 	 {
	//      $ac=" AND ac.ac_no=$ac_no";
	// 	 }
    //   $result=DB::select(DB::raw("SELECT r.scheduled_round AS S_ROUND,s.ST_CODE AS STATE,ac.AC_NO AS AC_NO,r.pc_no AS PC_NO FROM `m_election_details` AS `e` LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN round_master AS r ON e.st_code=r.st_code AND r.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user_data->election_id." $state_query $ac and `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user_data->election_id."  ORDER BY e.st_code,ac.AC_NO"));	
	if (Auth::user()->officername == 'ECIECI2') {
		if($state)  
		{
		  //echo $state; die;
		  $state_query=" e.st_code='$state' AND "; 
		}
		if(isset($ac_no) && $ac_no!=0)
		{
		   $ac=" ac.ac_no=$ac_no AND";
		}
		$sql = "SELECT  r.scheduled_round AS S_ROUND, s.ST_CODE AS STATE, s.ST_NAME, ac.AC_NO, ac.ac_name, 
		 (CASE WHEN r.postal_total_votes > 0 THEN 'Yes' ELSE 'No' END) AS  postal_total_votes_publish,
		 (CASE WHEN crfv.finalize_type = 2 THEN 'Yes' ELSE 'No' END) AS  finalize_type_publish
		 FROM `m_election_details` AS `e` 
		 LEFT JOIN `counting_result_finalize_verification` AS `crfv` ON (`crfv`.`AC_NO` = `e`.`CONST_NO` AND `crfv`.`ST_CODE` = `e`.`ST_CODE` AND `crfv`.`finalize_type` = 2) 
		 LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) 
		 LEFT JOIN `m_ac` AS `ac` ON ( `ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) 
		 LEFT JOIN round_master AS r ON e.st_code = r.st_code AND r.ac_no = e.`CONST_NO` AND `election_status` = 1 
		 WHERE ".$state_query.$ac."`e`.`election_status` = 1
		 GROUP BY `STATE`, ac.`AC_NO` ORDER BY ac.`AC_NO`";
		 $result=DB::select(DB::raw($sql));
	}else{
		if($state)  
		{
		  //echo $state; die;
		  $state_query="where e.st_code='$state'"; 
		}
		if(isset($ac_no) && $ac_no!=0)
		{
		   $ac=" AND ac.ac_no=$ac_no ";
		}
		$result=DB::select(DB::raw("SELECT r.scheduled_round AS S_ROUND,s.ST_CODE AS STATE,ac.AC_NO AS AC_NO,r.pc_no AS PC_NO FROM `m_election_details` AS `e` LEFT JOIN `m_state` AS `s` ON (`s`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN `m_ac` AS `ac` ON (`ac`.`AC_NO` = `e`.`CONST_NO` AND `ac`.`ST_CODE` = `e`.`ST_CODE`) LEFT JOIN round_master AS r ON e.st_code=r.st_code AND r.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 AND e.`election_id`=".$user_data->election_id." $state_query $ac and `e`.`CONST_TYPE` = 'AC' AND `e`.`election_status` = 1 AND `e`.`ELECTION_ID` = ".$user_data->election_id."  ORDER BY e.st_code,ac.AC_NO"));

	}  
	
	$dataResult=[];
	  if (Auth::user()->officername == 'ECIECI2') {
		$headings[]=['S.No','State Name','AC no & name','Total Scheduled Rounds','Total Completed Rounds','Total Pending Rounds',' Postal Votes Publish','Postal Votes Finalize'];
	}else{
		  $headings[]=['S.No','State Name','AC no & name','Total Scheduled Rounds','Total Completed Rounds','Total Pending Rounds'];
	  }
	 $export_data[]=[]; 
	  for($i=0;$i<count($result);$i++)
		{
			$completed_round=completeRound($result[$i]->STATE,$result[$i]->AC_NO);
			$pending=$result[$i]->S_ROUND-$completed_round;
			$val=($completed_round)?($completed_round):'0';
			$result_pending=($pending)?($pending):'0';

			if (Auth::user()->officername == 'ECIECI2') {
				$export_data[]=[
					$i+1,
					getstatebystatecode($result[$i]->STATE)->ST_NAME,
					$result[$i]->AC_NO.'-'.getacbyacno($result[$i]->STATE,$result[$i]->AC_NO)->AC_NAME,
					
					$result[$i]->S_ROUND,
					$val,
					$result_pending,
					$result[$i]->postal_total_votes_publish,
					$result[$i]->finalize_type_publish,
				];
			}else{
				$export_data[]=[
					$i+1,
					getstatebystatecode($result[$i]->STATE)->ST_NAME,
					$result[$i]->AC_NO.'-'.getacbyacno($result[$i]->STATE,$result[$i]->AC_NO)->AC_NAME,
					
					$result[$i]->S_ROUND,
					$val,
					$result_pending,
				];

			}
			// $dataResult[$i]['S.No']=$i+1;
			// $dataResult[$i]['State Name']=getstatebystatecode($result[$i]->STATE)->ST_NAME;
			// $dataResult[$i]['AC Name']=getacbyacno($result[$i]->STATE,$result[$i]->AC_NO)->AC_NAME;
			// $dataResult[$i]['AC No']=$result[$i]->AC_NO;
			// $dataResult[$i]['Scheduled Rounds']=$result[$i]->S_ROUND;
			// $dataResult[$i]['Completed Rounds']=$val;
			// $dataResult[$i]['Pending Rounds']=$result_pending;
		}
	  
	  $data= json_decode(json_encode($dataResult), true);
      $date = date('Y-m-d-H:i:s');
	  $type='csv';

	  $name_excel=$date.'-eci-schedule-report';
	  return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

    //   return Excel::create($date.'-eci-schedule-report', function($excel) use ($data) {
    //         $excel->sheet('mySheet', function($sheet) use ($data)
    //         {
    //             $sheet->fromArray($data);
    //         });
    //     })->download($type);



	}
	
	public function acList($s_code="")
	{
		$loggedin_userid = Auth::user()->user_id;
		
		
		if($s_code)
		{
			
			
		$ac=DB::table('m_ac as ac')
		         ->join('m_election_details as election',[
		              ['election.CONST_NO', '=','ac.AC_NO'],
		              ['election.ST_CODE', '=','ac.ST_CODE']
		        ])
                ->select('ac.AC_NO AS ac_no','ac.AC_NAME AS ac_name')
				->where('ac.ST_CODE', '=', $s_code)
				->where('election.CONST_TYPE', '=', 'AC')
				->where('election.ELECTION_ID', '=', $this->election_id)
				->orderByRaw('ac.AC_NO')
				->get();
					
			
				
		$myData='';
        $myData.='<select name="ac_no" id="acno" class="form-control" required >';
        $myData.='<option value="">---Please Select---</option>';   
		$myData.='<option value="0">Select All</option>';   
        foreach($ac as $data)
		{
        $myData.='<option value="'.$data->ac_no.'">'.$data->ac_no.' -'.$data->ac_name.'</option>';    
        }
        $myData.='</select>';
        return $myData;
	    }
		else
		{
		$myData='';
		$myData='<select name="ac_no" id="acno" class=" form-control" required>
							<option value="">---Please Select---</option>
							<option value="0">Select All</option>
				</select>';
		return $myData;
		}
	}

	
}  // end class