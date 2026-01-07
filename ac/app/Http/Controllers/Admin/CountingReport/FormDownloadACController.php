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

class FormDownloadACController extends Controller {
	
	public $view_path     = "admin.countingReport.form21c";
	public $aro           = "aro";
	public $ropc          = "admin.countingReport.form21c";
	public $eci           = "eci";
	public $ceo           = "admin.countingReport.form21c";
    protected $userId;
    protected $election_id;
	
    public function __construct() {
		$this->middleware(['auth:admin','auth']);
        $this->middleware('eci');
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
            }
            $this->userId =      \Auth::id(); // you can access user id here
            $this->election_id = \Auth::user()->election_id;

            return $next($request);
        });
    }
  
	public function form21Download(Request $request)
	{
	  $user_data = Auth::user();
	  $heading_title = 'Form 21C/D Download';

	  /*$data['m_state']=DB::table('m_state')
	  				->join('m_election_details',[
			          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
			         ])
	  				->where('m_election_details.CONST_TYPE','AC')
			        ->where('m_election_details.election_status','1')
		            ->where('m_election_details.ELECTION_ID',$this->election_id)
				    ->orderBy('m_state.ST_NAME','ASC')
				    ->get();*/

		$data['m_state'] = StateModel::get_states();

      $result=DB::select(DB::raw("SELECT AC.st_code AS STATE,AC.ac_no AS AC_NO,AC.ac_name AS AC_NAME,FRM.form21_path AS FROM21C 
				FROM winning_leading_candidate AS AC LEFT JOIN counting_form21_detail AS FRM ON 
				AC.st_code=FRM.st_code AND AC.ac_no=FRM.ac_no 
				ORDER BY AC.st_code,AC.ac_no"));
	  $state=strip_tags(trim($request->state_code));
	  $ac_no=strip_tags(trim($request->ac_no));
	  $state_query="";
	  $ac=""; 
	  if(isset($state) && isset($ac_no) && $state!="")
	  {	  
            if($state) 
			 {
				$state_query=" WHERE AC.ST_CODE='$state'";  
		     }
		     if($ac_no!=0)
			 {
			  $ac=" AND AC.AC_NO=$ac_no";
		     }       
	  $result=DB::select(DB::raw("SELECT AC.st_code AS STATE,AC.ac_no AS AC_NO,AC.ac_name AS AC_NAME,FRM.form21_path AS FROM21C 
				FROM winning_leading_candidate AS AC LEFT JOIN counting_form21_detail AS FRM ON 
				AC.st_code=FRM.st_code AND AC.ac_no=FRM.ac_no
				$state_query$ac ORDER BY AC.st_code,AC.ac_no"));	
	  }
	  return view($this->view_path.'.eci-form21c-download-ac-report', ['user_data'=>$user_data,'data'=>$data,'result'=>$result,'state'=>$state,'ac_no'=>$ac_no,'heading_title'=>$heading_title]);
	}
	
	public function acList($s_code="",$pc_no="")
	{
		$loggedin_userid = Auth::user()->user_id;
		if($pc_no)
		{
		$ac=DB::table('m_ac as ac')
                ->select('ac.AC_NO AS ac_no','ac.AC_NAME AS ac_name')
				->where('ac.ST_CODE', '=', $s_code)
				->where('ac.PC_NO', '=', $pc_no)
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