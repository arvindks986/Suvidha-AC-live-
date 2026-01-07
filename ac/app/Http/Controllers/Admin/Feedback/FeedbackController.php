<?php
namespace App\Http\Controllers\Admin\Feedback;

use App\Admin;
use App\adminmodel\SurveyMaster;
use App\adminmodel\SurveyResponse;
use App\adminmodel\SurveyResponseNew;
use App\commonModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Session;
use DB;
use App\models\Admin\PollingStation;
use App\models\Feedback\Booth_Feedback_Answer;

class FeedbackController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
	public $restricted_ps = ['91','99','128','129','189'];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
                return redirect('login')->with(Auth::logout());
            }
            $this->userId = \Auth::id(); // you can access user id here

            return $next($request);
        });
		$this->commonModel = new commonModel();
		$this->polling     = new PollingStation();
    }

    public function index(Request $request)
    {
		$users = Session::get('admin_login_details');
		$user_data = $users;
		if($user_data->designation == 'DEO'){
			$apps = $this->polling->get_polling_stations([
                                    'st_code' => 'S24',
									'ac_no' => '228',
									'paginate' => false,
									'restricted_ps' => $this->restricted_ps,
								]);
		}elseif($user_data->designation == 'ROAC'){
			$apps = $this->polling->get_polling_stations([
                                    'st_code' => $user_data->st_code,
									'ac_no' => $user_data->ac_no,
									'paginate' => false,
									'restricted_ps' => $this->restricted_ps,
								]);
		}
        
		$ps_no = '';
        $user = Auth::user()->id;
        $udesig = Auth::user()->designation;
        if (Auth::user()->st_code) {
            $ustate = trim($this->commonModel->getstatebystatecode(Auth::user()->st_code)->ST_NAME);
        } else {
            $ustate = "";
        }

        $uplace = Auth::user()->placename;
        $uname = Auth::user()->name;

        $formtype = 1;
        $apid = 0;
        $mdid = 0;
        $status = 0;
        $filter = "(st_code = '" . $user_data->st_code . "' and ac_no = '" . $user_data->ac_no . "' and ps_no = '" . $ps_no . "' and (";
        $mods = DB::table('polling_station_officer')
            ->select('name', 'role_id', 'id')
            ->whereRaw($filter . 'role_id = 33 or role_id = 35))')->get();


		return view('admin.feedback.feedback')->with(compact('formtype', 'user_data', 'ps_no', 'apps', 'mods', 'apid', 'mdid', 'status', 'user', 'uname', 'udesig', 'ustate', 'uplace'));
    }

    public function ajaxGetModule(Request $request)
    {
        $app_details = explode("_", $request->appid);
        $request->app_id = '';
        $part_no = $app_details[1];
        $ps_no = $app_details[2];
        $st_code = $app_details[3];
        $ac_no = $app_details[4];


        $filter = "(st_code = '".$st_code."' and ac_no = '".$ac_no."' and ps_no = '".$ps_no."' and (";
        $modules = DB::table('polling_station_officer')
        ->select('name','role_id','id')
        ->whereRaw($filter.'role_id = 33 or role_id = 35))')->get();
        
        $msg = "";
        foreach ($modules as $module) {
            $msg .= $module->id . "||" . $module->name . "##";
        }
        return response()->json(array('msg' => $msg), 200);
    }

    public function commonResponse(Request $request)
    {
		$reqlist = $request->all();
        $apid = 0;
        $mdid = 0;
		$users = Session::get('admin_login_details');
		$user_data = $users;
        $stime = now();
        $user = Auth::user()->id;
		$udesig = Auth::user()->designation;
		$app_details = explode("_",$request->appid);
		$apid = $request->app_id = $app_details[0];
		$part_no = $app_details[1];
        $ps_no = $app_details[2];
        $st_code = $app_details[3];
        $ac_no = $app_details[4];

        if (Auth::user()->st_code) {
            $ustate = trim($this->commonModel->getstatebystatecode(Auth::user()->st_code)->ST_NAME);
        } else {
            $ustate = "";
        }
        
        $uplace = Auth::user()->placename;
        $uname = Auth::user()->name;
        $olvl = Auth::user()->officerlevel;
		if($user_data->designation == 'DEO'){
			$apps = $this->polling->get_polling_stations([
                                    'st_code' => 'S24',
									'ac_no' => '228',
									'paginate' => false,
									'restricted_ps' => $this->restricted_ps,
								]);
		}elseif($user_data->designation == 'ROAC'){
			$apps = $this->polling->get_polling_stations([
								'st_code' => $user_data->st_code,
								'ac_no' => $user_data->ac_no,
								'paginate' => false,
								'restricted_ps' => $this->restricted_ps,
							]);
		}
        $filter = "(st_code = '" . $st_code . "' and ac_no = '" . $ac_no . "' and ps_no = '" . $ps_no . "' and (";
        $mods = DB::table('polling_station_officer')
            ->select('name', 'role_id', 'id')
            ->whereRaw($filter . 'role_id = 33 or role_id = 35))')->get();


        if ($request->formtype == 1) {
            $apid = $request->appid;
            $mdid = $request->moduleid;
            $usrid = $user;
            $status = 0;
            if ($request->appid > 6) {
                $srec = Booth_Feedback_Answer::where('app_id', $apid)->where('user_id', $request->moduleid)->first();
                $formtype = 2;
                if ($srec) {
                    $q1 = $srec->q1;
                    $q2 = $srec->q2;
                    $q3 = $srec->q3;
                    $q4 = $srec->q4;
                    $q5 = $srec->q5;
                    $q61 = $srec->q6_1;
                    $q62 = $srec->q6_2;
                    $q63 = $srec->q6_3;
                    $q64 = $srec->q6_4;
                    $stime = $srec->created_at;
                    $orec = 1;
                } else {
                    $q1 = 1;
                    $q2 = 1;
                    $q3 = 1;
                    $q4 = "";
                    $q5 = "";
                    $q61 = 0;
                    $q62 = 0;
                    $q63 = 0;
                    $q64 = 0;

                    $orec = 0;
                }
                return view('admin.feedback.feedback')->with(compact('formtype', 'ps_no', 'user_data','apps', 'mods', 'orec', 'apid', 'mdid', 'usrid', 'q1', 'q2', 'q3', 'q4', 'q5', 'q61', 'q62', 'q63', 'q64', 'q65', 'q7', 'status', 'uname', 'udesig', 'ustate', 'uplace', 'stime'));
            }
        } elseif ($request->formtype == 2) {
            $rules = [
                'q1' => 'required|numeric|digits:1|min:1|max:2',
                'q2' => 'required|numeric|digits:1|min:1|max:2',
                'q3' => 'required|numeric|digits:1|min:1|max:2',
                'q6a' => 'required|numeric|digits:1|min:1|max:5',
                'q6b' => 'required|numeric|digits:1|min:1|max:5',
                'q6c' => 'required|numeric|digits:1|min:1|max:5',
                'q6d' => 'required|numeric|digits:1|min:1|max:5',

            ];
            $messages = [
                'q1' => 'Please select proper value for Question # 1',
                'q2' => 'Please select proper value for Question # 2',
                'q3' => 'Please select proper value for Question # 3',
                'q6a' => 'Please select proper value for Question # 6.1',
                'q6b' => 'Please select proper value for Question # 6.2',
                'q6c' => 'Please select proper value for Question # 6.3',
                'q6d' => 'Please select proper value for Question # 6.4',

            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->passes()) {
                $srp = new Booth_Feedback_Answer;
                $srp->app_id = $request->appid;
                $srp->user_id = $request->moduleid;
                $srp->officer_id = $user;
				$srp->officerlevel = $olvl;
				$srp->st_code = $user_data->st_code;
				$srp->ac_no = $user_data->ac_no;
				$srp->ps_no = $ps_no;
				$srp->part_no = $part_no;
                $srp->q1 = $request->q1;
                $srp->q2 = $request->q2;
                $srp->q3 = $request->q3;
                $srp->q4 = strip_tags($request->q4);
                $srp->q5 = strip_tags($request->q5);
                $srp->q6_1 = $request->q6a;
                $srp->q6_2 = $request->q6b;
                $srp->q6_3 = $request->q6c;
                $srp->q6_4 = $request->q6d;
                $srp->save();

                $apid = $request->appid;
                $mdid = $request->moduleid;
                $usrid = $user;
                $q1 = $srp->q1;
                $q2 = $srp->q2;
                $q3 = $srp->q3;
                $q4 = $srp->q4;
                $q5 = $srp->q5;
                $q61 = $srp->q6_1;
                $q62 = $srp->q6_2;
                $q63 = $srp->q6_3;
                $q64 = $srp->q6_4;
                $orec = 1;
                $status = 1;
                $formtype = 2;
                return view('admin.feedback.feedback')->with(compact('formtype', 'user_data', 'ps_no', 'apps', 'mods', 'orec', 'apid', 'mdid', 'usrid', 'q1', 'q2', 'q3', 'q4', 'q5', 'q61', 'q62', 'q63', 'q64', 'q65', 'q7', 'status', 'uname', 'udesig', 'ustate', 'uplace', 'stime'));
            } else {
                return redirect()->back()->withErrors($validator, 'error')->withInput();
            }
        }
    } //END commonResponse

} //END CLASS
