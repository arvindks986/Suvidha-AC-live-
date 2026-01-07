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

class Form21ReportACController extends Controller {
	
	public $view_path     = "admin.countingReport.formReport";
	public $aro           = "aro";
	public $ropc          = "admin.countingReport.formReport";
	public $eci           = "eci";
	public $ceo           = "admin.countingReport.formReport";
    protected $userId;
	
    public function __construct() {
		$this->middleware(['auth:admin','auth']);
        $this->middleware('eci');
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
            }
            $this->userId = \Auth::id(); // you can access user id here

            return $next($request);
        });
    }
  
	public function form21Report(Request $request)
	{
	  $user_data = Auth::user();
	  $heading_title = 'Form 21C/D Download';
	  $state=strip_tags(trim($request->state_code));
	  $result=DB::select(DB::raw("SELECT COUNT(AC.ac_no) AS TOTALAC,AC.st_code 
	  AS STATE,AC.ac_no AS AC_NO,AC.ac_name AS AC_NAME FROM winning_leading_candidate AS AC LEFT JOIN 
	  counting_form21_detail AS FRM ON AC.st_code=FRM.st_code AND AC.ac_no=FRM.ac_no GROUP BY AC.st_code 
	  ORDER BY AC.st_code"));
	  return view($this->view_path.'.eci-form21-report', ['user_data'=>$user_data,
	  'result'=>$result,'state'=>$state,'heading_title'=>$heading_title]);
	}

	
}  // end class
	
