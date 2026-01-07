<?php

namespace App\Http\Controllers\Admin\BoothCountingReport;

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
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use Maatwebsite\Excel\Facades\Excel;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;

date_default_timezone_set('Asia/Kolkata');

class GazzeteReportController extends Controller {

    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
                return redirect('login')->with(Auth::logout());
            }
            $user = Auth::user();
            $this->middleware('eci');
            return $next($request);
        });
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }
	
	public function getPdfView() {

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
                $module = $this->commonModel->getallmodule();
                
                $get_win_candidate = DB::table('winning_leading_candidate')
                        ->select('st_name','st_hname','ac_no','ac_name','ac_hname','lead_cand_name','lead_cand_hname','lead_cand_party','lead_cand_hparty','status')->where('status', '1')
                        ->get()->toArray();
                
                $newArr = array();
                if(count($get_win_candidate) >0){
                    foreach($get_win_candidate as $k=>$v){
                        $newArr[$k]['st_name'] = $v->st_name;
                        $newArr[$k]['st_hname'] = $v->st_hname;
                        $newArr[$k]['ac_name'] = $v->ac_no.'- '.$v->ac_name;
                        $newArr[$k]['ac_hname'] = $v->ac_no.'- '.$v->ac_hname;
                        $newArr[$k]['lead_cand_name'] = $v->lead_cand_name;
                        $newArr[$k]['lead_cand_hname'] = $v->lead_cand_hname;
                        $newArr[$k]['lead_cand_party'] = $v->lead_cand_party;
                        $newArr[$k]['lead_cand_hparty'] = $v->lead_cand_hparty;
                    }
                }
                $english_arr = array();        
                $hindi_arr = array();   
                
                $hindi_arr = $this->group_by($newArr,'st_hname');
                $english_arr = $this->group_by($newArr,'st_name');
                
              
       
                return view('admin.booth-counting-report.gazzeted-report.winning-candidate', ['hindiarr' => $hindi_arr,'engarr'=>$english_arr,'user_data'=>$user,'ele_details' => $ele_details, 'user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_phase' => $list_phase,'list' => $list, 'list_electionid' => $list_electionid]);    
                
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    function group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
    }

    function getDownloadPdf() {
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
                $module = $this->commonModel->getallmodule();
				
                $get_win_candidate = DB::table('winning_leading_candidate')
                        ->select('st_name','st_hname','ac_no','ac_name','ac_hname','lead_cand_name','lead_cand_hname','lead_cand_party','lead_cand_hparty','status')->where('status', '1')
                        ->get()->toArray();
                
                $newArr = array();
                if(count($get_win_candidate) >0){
                    foreach($get_win_candidate as $k=>$v){
                        $newArr[$k]['st_name'] = $v->st_name;
                        $newArr[$k]['st_hname'] = $v->st_hname;
                        $newArr[$k]['ac_name'] = $v->ac_no.'- '.$v->ac_name;
                        $newArr[$k]['ac_hname'] = $v->ac_no.'- '.$v->ac_hname;
                        $newArr[$k]['lead_cand_name'] = $v->lead_cand_name;
                        $newArr[$k]['lead_cand_hname'] = $v->lead_cand_hname;
                        $newArr[$k]['lead_cand_party'] = $v->lead_cand_party;
                        $newArr[$k]['lead_cand_hparty'] = $v->lead_cand_hparty;
                    }
                }
                $english_arr = array();        
                $hindi_arr = array();   
                
                $hindi_arr = $this->group_by($newArr,'st_hname');
                $english_arr = $this->group_by($newArr,'st_name');
                $date = date('Y-m-d');
                $pdf = PDF::loadView('admin.booth-counting-report.gazzeted-report.winning-candidate-pdf', ['hindiarr' => $hindi_arr,'engarr'=>$english_arr,'user_data'=>$user,'ele_details' => $ele_details, 'user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_phase' => $list_phase,'list' => $list, 'list_electionid' => $list_electionid]);
                return $pdf->download($date . '-winning-candidates-details' . '.' . 'pdf');
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
}

// end class
