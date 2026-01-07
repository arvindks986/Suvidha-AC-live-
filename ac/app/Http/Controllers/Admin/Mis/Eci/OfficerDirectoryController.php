<?php

namespace App\Http\Controllers\Admin\Mis\Eci;

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
use App\Helpers\SmsgatewayHelper;
use Maatwebsite\Excel\Facades\Excel;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;
use App\models\AC;
use App\models\Admin\Mis\ExGratiaEciModel;
use App\models\Common\{
    AcModel,
    DistrictModel,
    StateModel
};
use App\Exports\ExcelExport;

date_default_timezone_set('Asia/Kolkata');

class OfficerDirectoryController extends Controller {

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
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
                return redirect('login')->with(Auth::logout());
            }

            $user = Auth::user();
            switch ($user->role_id) {
                case '4':
                    $this->middleware('ceo');
                    break;
				case '5':
                    $this->middleware('deo');
                    break;
				case '7':
                    $this->middleware('eci');
                    break;
                case '50':
                    $this->middleware('seczonal');
                    break;
                default:
                    $this->middleware('eci');
            }
            return $next($request);
        });

        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
        $this->ExGratiaEciModel = new ExGratiaEciModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

  
	public function officerList(Request $request){
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
				
				$officerlist =DB::table('officer_login')->whereIn('role_id', [4,5,19,20,50,41])->get();
				//dd($officerlist);
				$data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = $d->st_code;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;
                $data['officer_list'] = $officerlist;
				return view('admin.mis.eci.officer-directory.list-officer', $data);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
	}
	
	function viewOfficerDetails($id){
		if(Auth::check()){
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
          $decryptedid = decrypt($id);
		  
            $getofficerdetails =DB::table('officer_login')->where('id',$decryptedid)->first();
			//dd($getofficerdetails);
                return view('admin.mis.eci.officer-directory.view-profile')->with(array('user_data' => $d, 'showpage' => 'officer-profile', 'getofficerdetails' => $getofficerdetails));
            }
          else {
                return redirect('/officer-login');
                }
	}
    //
}

// end class
