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

class CEOScheduleReportController extends Controller {

	public $view_path     = "admin.countingReport.scheduleReport.ceo";
	public $aro           = "aro";
	public $ropc          = "ropc";
	public $eci           = "eci";
	public $ceo           = "ceo";
    protected $userId;
    protected $election_id;
	
    public function __construct() {	
		$this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');
	    $this->commonModel = new commonModel();
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
	  $state = $user_data->st_code;
	  $ac_no= trim($request->ac_no);
	     //echo $ac_no; die;
	  $data['m_ac']=DB::table('m_ac')
	  ->leftjoin('m_election_details as election',[
		              ['election.CONST_NO', '=','m_ac.AC_NO'],
		              ['election.ST_CODE', '=','m_ac.ST_CODE'],
		        ])
	  ->where('m_ac.ST_CODE',$state)
	  ->where('election.CONST_TYPE', '=', 'AC')
	  ->where('election.ELECTION_ID', '=', $this->election_id)
	  ->orderBy('m_ac.AC_NO','ASC')->get();
	  $heading_title = 'Scheduled Rounds Report';	  
	  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,ac_no AS AC_NO FROM round_master WHERE st_code='$state' order by ac_no"));
	  $urlpdf='0';
	  $urlexcel='0';	
	  $ac="";
	  if(isset($ac_no) && $ac_no!="")
	  { 
         if($ac_no!=0)  
		   {
			
			 $ac=" AND ac_no=$ac_no"; 
		   }
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,ac_no AS AC_NO FROM round_master WHERE st_code='$state'$ac order by ac_no"));
        $urlpdf=$ac_no;
	    $urlexcel=$ac_no;
	    }
	  return view($this->view_path.'.ceo-schedule-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'urlpdf'=>$urlpdf,'urlexcel'=>$urlexcel,'ac_no'=>$ac_no,'heading_title'=>$heading_title]);
	}

	public function scheduleReportPDF($ac_no="")
	{
	  $user_data =   Auth::user();
	  $state = $user_data->st_code;
	  $heading_title = 'Scheduled Rounds Report PDF';
      $ac_name=DB::table('m_ac')->where('ST_CODE',$state)->where('AC_NO',$ac_no)->first();	  
	  if($ac_no!=0 && $ac_no!="")
	  {
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND ac_no=$ac_no order by ac_no"));
	  }
	  else
	  {
		  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,ac_no AS AC_NO FROM round_master WHERE st_code='$state' order by ac_no")); 
	  }
	   $date = date('Y-m-d-H:i:s');
	  $pdf = PDF::loadView($this->view_path.'.ceo-schedule-report-pdf', compact('result','data','ac_no','state','ac_name','user_data','heading_title'));
	  return $pdf->download($date.'-'.$state.'-ceo-schedule-report.pdf');
	}
	
	public function scheduleReportExcel($ac_no="")
	{
	  $user_data = Auth::user();
	  $state = $user_data->st_code;
	  $data['heading_title'] = 'Schedule Report Excel';
	  if($ac_no!=0 && $ac_no!="")
	  {
        $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,ac_no AS AC_NO FROM round_master WHERE st_code='$state' AND ac_no=$ac_no order by ac_no"));
	  }
	  else
	  {
		  $result=DB::select(DB::raw("SELECT scheduled_round AS S_ROUND,st_code AS STATE,ac_no AS AC_NO FROM round_master WHERE st_code='$state' order by ac_no")); 
	  }
	  $dataResult=[];
	  for($i=0;$i<count($result);$i++)
		{
			$completed_round=completeRound($result[$i]->STATE,$result[$i]->AC_NO);
			$pending=$result[$i]->S_ROUND-$completed_round;
	        $val=($completed_round)?($completed_round):'0';
			$result_pending=($pending)?($pending):'0';
				
			$dataResult[$i]['S.No']=$i+1;
			$dataResult[$i]['AC Name']=getacbyacno($result[$i]->STATE,$result[$i]->AC_NO)->AC_NAME;
			$dataResult[$i]['AC No']=$result[$i]->AC_NO;
			$dataResult[$i]['Scheduled Rounds']=$result[$i]->S_ROUND;
			$dataResult[$i]['Completed Rounds']=$val;
			$dataResult[$i]['Pending Rounds']=$result_pending;
		}
	  
	  $data= json_decode(json_encode($dataResult), true);
      $date = date('Y-m-d-H:i:s');
	  $type='csv';
      return Excel::create($date.'-'.$state.'-eci-schedule-report', function($excel) use ($data) {
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
        $myData.='<select name="ac_no" id="acno" class="form-control" required>';
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
		$myData='<select name="ac_no" id="acno" class="form-control" required>
							<option value="">---Please Select---</option>
							<option value="0">Select All</option>
				</select>';
		return $myData;
		}
	}

	
}  // end class