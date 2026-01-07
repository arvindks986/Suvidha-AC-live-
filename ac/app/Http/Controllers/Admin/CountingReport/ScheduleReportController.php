<?php
namespace App\Http\Controllers\Admin\CountingReport;

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
use Excel;
use App\commonModel;  
use App\models\Admin\ReportModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;

class ScheduleReportController extends Controller {

	public $view_path     = "admin.countingReport.scheduleReport.eci";
	public $aro           = "aro";
	public $ropc          = "admin.countingReport.scheduleReport.ropc";
	public $eci           = "eci";
	public $ceo           = "admin.countingReport.scheduleReport.eco";
    protected $userId;

    protected $election_id; 
   
	
    public function __construct() {
		$this->middleware(['auth:admin','auth']);
        $this->middleware('eci');
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
            }
            $this->userId = \Auth::id(); // you can access user id here
            $this->election_id  = Auth::user()->election_id;

            return $next($request);
        });
    }
  
	public function scheduleReport(Request $request)
	{


	  $user_data = Auth::user();
	  $heading_title = 'Scheduled Rounds Report';
	  /*$data['m_state']=DB::table('m_state')
					  ->join('m_election_details',[
				        ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
				      ])
				      ->where('m_election_details.CONST_TYPE','AC')
				      ->where('election_status','1')
				      ->where('m_election_details.ELECTION_ID',$this->election_id)
				      ->orderBy('ST_NAME','ASC')->groupBy('m_state.ST_CODE')->get();*/	

		$data['m_state']= $statevalue = StateModel::get_states(); 

	  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,ac_no AS AC_NO FROM round_master ORDER BY st_code,ac_no"));	
	  $state=strip_tags(trim($request->state_code));
	  $ac_no=strip_tags(trim($request->ac_no)); 
	  $ac="";
	  $state_query="";
	  $urlpdf='0/0';
	  $urlexcel='0/0';  
	  if(isset($state) && isset($ac_no) && $state!="")
	  {			
           if($state)  
		   {
			 //echo $state; die;
			 $state_query=" WHERE st_code='$state'"; 
		   }
		   if(isset($ac_no) && $ac_no!=0)
		   {
			  $ac=" AND ac_no=$ac_no";
		   }
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,pc_no AS PC_NO,ac_no AS AC_NO FROM round_master$state_query$ac ORDER BY st_code,ac_no"));
        $urlpdf=$state.'/'.$ac_no;
	    $urlexcel=$state.'/'.$ac_no;	
	    }

	  return view($this->view_path.'.eci-schedule-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'urlpdf'=>$urlpdf,'urlexcel'=>$urlexcel,'ac_no'=>$ac_no,'heading_title'=>$heading_title]);
	}
	
	public function scheduleReportPDF($state="",$ac_no="")
	{
	  $user_data =   Auth::user();	 
	  $heading_title = 'Schedule Round Report PDF';
	  $state_name=DB::table('m_state')->where('ST_CODE',$state)->first();	
      $ac_name=DB::table('m_ac')->where('ST_CODE',$state)->where('AC_NO',$ac_no)->first();
	  $state_query="";
	  $ac="";	  
	  if($state)
		{
	    $state_query=" WHERE st_code='$state'";
		}
	  if($ac_no!="" && $ac_no!=0)
		{
	     $ac=" AND ac_no=$ac_no";
		}
      $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,ac_no AS AC_NO FROM round_master$state_query$ac ORDER BY st_code,ac_no"));	
	  $date = date('Y-m-d-H:i:s');
	  $pdf = PDF::loadView($this->view_path.'.eci-schedule-report-pdf', compact('result','data','ac_no','state','user_data','heading_title'));
	  return $pdf->download($date.'-eci-schedule-report.pdf');
	}
	
	public function scheduleReportExcel($state="",$ac_no="")
	{
	  $user_data =   Auth::user();
	  $heading_title = 'Schedule Report Excel';  
      $state_query="";
	  $ac="";	  
	  if($state)
		{
		$state_query=" WHERE st_code='$state'";
		}
	 if($ac_no!="" && $ac_no!=0)
		 {
	     $ac=" AND ac_no=$ac_no";
		 }
      $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,ac_no AS AC_NO FROM round_master$state_query$ac ORDER BY st_code,ac_no"));	
	  $dataResult=[];
	  for($i=0;$i<count($result);$i++)
		{
			$completed_round=completeRound($result[$i]->STATE,$result[$i]->AC_NO);
			$pending=$result[$i]->S_ROUND-$completed_round;
			$val=($completed_round)?($completed_round):'0';
			$result_pending=($pending)?($pending):'0';
			$dataResult[$i]['S.No']=$i+1;
			$dataResult[$i]['State Name']=getstatebystatecode($result[$i]->STATE)->ST_NAME;
			$dataResult[$i]['AC Name']=getacbyacno($result[$i]->STATE,$result[$i]->AC_NO)->AC_NAME;
			$dataResult[$i]['AC No']=$result[$i]->AC_NO;
			$dataResult[$i]['Scheduled Rounds']=$result[$i]->S_ROUND;
			$dataResult[$i]['Completed Rounds']=$val;
			$dataResult[$i]['Pending Rounds']=$result_pending;
		}
	  
	  $data= json_decode(json_encode($dataResult), true);
      $date = date('Y-m-d-H:i:s');
	  $type='csv';
      return Excel::create($date.'-eci-schedule-report', function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);
	}
	
	public function acList($s_code="")
	{
		$loggedin_userid = Auth::user()->user_id;
		if($s_code)
		{
		$ac=DB::table('m_ac as ac')
		        ->leftjoin('m_election_details as election',[
		              ['election.CONST_NO', '=','ac.AC_NO'],
		              ['election.ST_CODE', '=','ac.ST_CODE'],
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