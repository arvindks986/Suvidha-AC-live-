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

class CEOFormDownloadACController  extends Controller {

	public $view_path     = "admin.countingReport.form21c";
	public $aro           = "aro";
	public $ropc          = "admin.countingReport.form21c";
	public $eci           = "eci";
	public $ceo           = "admin.countingReport.form21c.ceo";
    protected $userId;
    public $election_id; 
	
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
  
	public function form21Download(Request $request)
	{
	  $user_data = Auth::user();
	  $state = $user_data->st_code;
	  $heading_title = 'Form 21C/D Download';
	  $ac_no= trim($request->ac_no);

	  $data['m_ac']=DB::table('m_ac')
	                ->leftjoin('m_election_details as election',[
                          ['election.CONST_NO', '=','m_ac.AC_NO'],
                          ['election.ST_CODE', '=','m_ac.ST_CODE'],
                    ])
                    ->where('election.CONST_TYPE', '=', 'AC')
                    ->where('election.ELECTION_ID', '=', $this->election_id)
				    ->where('m_ac.ST_CODE',$state)
				    ->orderBy('m_ac.AC_NO','ASC')
				    ->get();
	  
	  $result=DB::select(DB::raw("SELECT AC.st_code AS STATE,AC.ac_no AS AC_NO,AC.ac_name AS 
	  AC_NAME,FRM.form21_path AS FROM21C FROM winning_leading_candidate AS AC LEFT JOIN 
	  counting_form21_detail AS FRM ON AC.st_code=FRM.st_code AND AC.ac_no=FRM.ac_no WHERE AC.st_code='$state' 
	  ORDER BY AC.st_code,AC.ac_no"));

	  if(isset($ac_no) && $ac_no!="")
	  { 
      $result=DB::select(DB::raw("SELECT AC.st_code AS STATE,AC.ac_no AS AC_NO,AC.ac_name AS 
	  AC_NAME,FRM.form21_path AS FROM21C FROM winning_leading_candidate AS AC LEFT JOIN 
	  counting_form21_detail AS FRM ON AC.st_code=FRM.st_code AND AC.ac_no=FRM.ac_no WHERE AC.st_code='$state' 
	  AND AC.ac_no='$ac_no' ORDER BY AC.st_code,AC.ac_no"));
        }
	  return view($this->view_path.'.ceo-form21-download-ac-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'ac_no'=>$ac_no,'heading_title'=>$heading_title]);
	}

	
	public function acList($s_code="")
	{
		$loggedin_userid = Auth::user()->user_id;
		if($s_code)
		{
		$ac=DB::table('m_ac as ac')
                ->select('ac.AC_NO AS ac_no','ac.AC_NAME AS ac_name')
                ->leftjoin('m_election_details as election',[
                          ['election.CONST_NO', '=','m_ac.AC_NO'],
                          ['election.ST_CODE', '=','m_ac.ST_CODE'],
                    ])
                ->where('election.CONST_TYPE', '=', 'AC')
                ->where('election.ELECTION_ID', '=', $this->election_id)
				->where('ac.ST_CODE', '=', $s_code)
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