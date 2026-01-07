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

class CeoOpDashController extends Controller {

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
        $this->middleware('ceo');

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
		$users = Session::get('admin_login_details');
        $user = Auth::user();
		//dd($user);
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
				
				
				
				$nomination_finalized = OfficerDetailsModel::phasewise_nomination_finalized();
				$roll_master = OfficerDetailsModel::get_all_role();
				$roll_wise_count = OfficerDetailsModel::get_all_role_wise();
				
				//dd($roll_wise_count);
				$get_officer_count = OfficerDetailsModel::select('id','role_id','officername','designation','is_active','name')->whereIn('role_id',array('5','19','36'))->where('st_code',$users->st_code)->get();
				$data['roll_master'] = $roll_master;
				$data['roll_wise_count'] = $roll_wise_count;
				$data['get_officer_count'] = $get_officer_count;
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

                return view('admin.op-dashboard.ceo.op_dashboard', $data);
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
		$get_update = DB::table('officer_login')->where('id', $uid)->update(['password' => $request->password_confirmation,'pass_flag'=>'1']);
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
