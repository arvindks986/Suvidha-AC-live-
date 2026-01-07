<?php

namespace App\Http\Controllers\Admin\OpDashboard;

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
use App\models\Admin\OpDashboard\OfficerHistoryModel;
use App\models\Admin\OpDashboard\OfficerDetailsModel;
use App\models\Admin\OpDashboard\OperationalLogModel;
use App\Helpers\SmsgatewayHelper;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;
use App\models\AC;
use App\models\Common\{AcModel, DistrictModel, StateModel};

date_default_timezone_set('Asia/Kolkata');

class EciOpDashController extends Controller {

    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $election_id;

    public function __construct() {
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('eci');

        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
        $this->OfficerHistoryModel = new OfficerHistoryModel();
        $this->OfficerDetailsModel = new OfficerDetailsModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

	
	public function getOpDasboard(Request $request) {

		$valid_passwords = array ("mario" => "carbonell");
		$valid_users = array_keys($valid_passwords);

		$user = @$_SERVER['PHP_AUTH_USER'];
		$pass = @$_SERVER['PHP_AUTH_PW'];

		$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

		if (!$validated) {
		  header('WWW-Authenticate: Basic realm="My Realm"');
		  header('HTTP/1.0 401 Unauthorized');
		  die ("Not authorized");
		}

		$users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($uid);
                $list_record = $this->ECIModel->getallelectionphasewise();
                $list_phase = $this->ECIModel->listcurrentelectionphase();
                $list_electionid = $this->ECIModel->getallelectionbyid();
                $list = $this->ECIModel->listelectiontype();
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
                $st = getstatebystatecode($d->st_code);
                $dist = getdistrictbydistrictno($d->st_code, $d->dist_no);
                $st_code = $d->st_code;
                $module = $this->commonModel->getallmodule();
				
				$number_blocks = [
					[
						'title' => 'Currently Logged In Users',
						'number' => OfficerDetailsModel::where('login_flag','=', 1)->count()
					],
					[
						'title' => 'Users Logged In Today',
						'number' => OfficerHistoryModel::whereDate('login_time', today())->count()
					],
					[
						'title' => 'Users Logged In Last 7 Days',
						'number' => OfficerHistoryModel::whereDate('login_time', '>', today()->subDays(7))->count()
					],
					[
						'title' => 'Users Logged In Last 30 Days',
						'number' => OfficerHistoryModel::whereDate('login_time', '>', today()->subDays(30))->count()
					],
				];
				
				$officer_active_blocks = [
					[
						'title' => 'Total Officer',
						'number' => OfficerDetailsModel::whereIn('role_id', [4,5,6,17,18,19,20])->count()
					],
					[
						'title' => 'Password Activated',
						'number' => OfficerDetailsModel::where('password','!=','')->whereIn('role_id', [4,5,6,17,18,19,20])->count()
					],
					[
						'title' => 'Password Not Activated',
						'number' => OfficerDetailsModel::where('password', '=', '')->whereIn('role_id', [4,5,6,17,18,19,20])->count()
					],
				];

				$list_blocks = [
					[
						'title' => 'Last Logged In Users',
						'entries' => OfficerHistoryModel::orderBy('login_time', 'desc')
							->take(1000)
							->get(),
					]
				];
				
				$pass_list_blocks = [
					[
						'title' => 'Password Changed',
						'number' => OfficerDetailsModel::where('pass_flag','=',1)->whereIn('role_id', [4,5,6,17,18,19,20])->count()
					],
					[
						'title' => 'Users Not Changed Password',
						'number' => OfficerDetailsModel::where('pass_flag','=',0)->whereIn('role_id', [4,5,6,17,18,19,20])->count()
					],
					[
						'title' => 'Pin Activated',
						'number' => OfficerDetailsModel::where('two_step_pin','!=','')->whereIn('role_id', [4,5,6,17,18,19,20])->count()
					],
					[
						'title' => 'Pin Not Activated',
						'number' => OfficerDetailsModel::where('two_step_pin','=','')->whereIn('role_id', [4,5,6,17,18,19,20])->count()
					],
				];
				
				
				
				  
				$master_table_blocks = [
					[
						'title' => 'State Master Data',
						'entries' => StateModel::orderBy('ST_CODE', 'ASC')->get()
					],
					[
						'title' => 'District Master Data',
						'entries' => OfficerDetailsModel::get_district_data()
					],
					[
						'title' => 'AC Master Data',
						'entries' => OfficerDetailsModel::get_ac_data()
					],
					[
						'title' => 'PC Master Data',
						'entries' => OfficerDetailsModel::get_pc_data()
					]
				];
				
				$total_filter = ['state'=> '','phase'=> '1'];
				$voter_turnout_blocks = [
					[
						'title' => 'Estimated Poll Percent',
						'number' => OfficerDetailsModel::get_average_sum($total_filter),
						'url' => 'eci/turnout/estimate-poll-percent',
					],
					[
						'title' => 'End of Poll',
						'number' => OfficerDetailsModel::get_average_sum($total_filter),
						'url' => 'eci/turnout/end-of-poll'
					],
					[
						'title' => 'End of Poll Percent',
						'number' => OfficerDetailsModel::get_average_sum($total_filter),
						'url' => 'eci/turnout/end-of-poll-percent'
					]
				];
				
				$nomination_finalized = OfficerDetailsModel::phasewise_nomination_finalized();
				$roll_master = OfficerDetailsModel::get_all_role();
				$roll_wise_count = OfficerDetailsModel::get_all_role_wise();
				
				//dd($roll_wise_count);
				$get_officer_count = OfficerDetailsModel::select('id','role_id','officername','designation','is_active','name')->get();

				//$today=date("Y-m-d");
				//dd($today);
				$data['number_blocks'] = $number_blocks; 
				$data['officer_active_blocks'] = $officer_active_blocks; 
				$data['list_blocks'] = $list_blocks; 
				$data['pass_list_blocks'] = $pass_list_blocks; 
				$data['master_table_blocks'] = $master_table_blocks;
				$data['phasewise_nomination_finalized'] = $nomination_finalized;
				$data['voter_turnout'] = $voter_turnout_blocks;
				$data['get_officer_count'] = $get_officer_count;
				$data['roll_master'] = $roll_master;
				$data['roll_wise_count'] = $roll_wise_count;
				//dd($voter_turnout_blocks);
					

				$data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['dist_no'] = $d->dist_no;
                //$data['dist_name'] = $dist->DIST_NAME;
                $data['lists'] = $list;
				$data['statelist'] = StateModel::get();
				$data['aclist'] = array();
				//echo $chart_dates;die;
				
                //echo '<pre>';print_r($datewise_list);die;

                return view('admin.op-dashboard.eci.op_dashboard', $data);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
	
	public function changeUserStatus(Request $request){
		$users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
			if(!empty($request->ox)){
				$sts_to_update = ($request->sts=='Inactive')?1:0;
				$uid = $request->ox;
				$get_update = DB::table('officer_login')->where('id', $uid)->update(['is_active' => $sts_to_update]);

				if($get_update){
					$object         = new OperationalLogModel();
					$object->activity     = 'Status Changed';
					$object->updated_user_id      =  $uid;
					$object->status_from = ($request->sts=='Inactive')?0:1;
					$object->status_to = $sts_to_update;
					$object->save();
					echo 'Y';die;
				}else{
					echo 'N';die;
				}
			}else{
				
			}
		} else {
            return redirect('/officer-login');
        }
	}
	
	public function update_by_ajax(Request $request){
        if(!Auth::user()){
            return \Response::json([
                'status'            => 0,
                'login_required'    => true,
                'message'           => "Please login to continue"
            ]);
        }

        $validator = Validator::make($request->all(),[
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required'
        ],[]);

        if ($validator->fails()){
            return \Response::json([
                'status' => false,
                'errors' => $validator->errors()->getMessageBag()
            ]);
        }
			
        //Session::put("new_password",$request->password);
        $uid = $request->ox;
		$get_update = DB::table('officer_login')->where('id', $uid)->update(['password' => $request->password_confirmation]);
		if($get_update){
			$object         = new OperationalLogModel();
			$object->activity     = 'Password Reset';
			$object->updated_user_id   =  $uid;
			$object->save();
			return \Response::json([
            'status'    => true,
            'message'   => "Password Updated successfully"
			]);
		}else{
			return \Response::json([
                'status' => false,
                'errors' => 'Failure! please try again.'
            ]);
		}
    }
    
	public function updatepin_by_ajax(Request $request){
        if(!Auth::user()){
            return \Response::json([
                'status'            => 0,
                'login_required'    => true,
                'message'           => "Please login to continue"
            ]);
        }

        $validator = Validator::make($request->all(),[
            'pin'              => 'required|confirmed',
            'pin_confirmation' => 'required'
        ],[]);

        if ($validator->fails()){
            return \Response::json([
                'status' => false,
                'errors' => $validator->errors()->getMessageBag()
            ]);
        }
			
        //Session::put("new_pin",$request->pin);
        $uid = $request->ox;
		$get_update = DB::table('officer_login')->where('id', $uid)->update(['two_step_pin' => bcrypt($request->pin_confirmation)]);
		if($get_update){
			$object         = new OperationalLogModel();
			$object->activity     = 'Pin Reset';
			$object->updated_user_id   =  $uid;
			$object->save();
			return \Response::json([
            'status'    => true,
            'message'   => "Pin Updated successfully"
			]);
		}else{
			return \Response::json([
                'status' => false,
                'errors' => 'Failure! please try again.'
            ]);
		}
    }
}

// end class
