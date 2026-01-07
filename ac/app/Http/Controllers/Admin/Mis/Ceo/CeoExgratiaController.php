<?php

namespace App\Http\Controllers\Admin\Mis\Ceo;

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
use Illuminate\Support\Facades\Response;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;
use App\models\AC;
use App\models\Admin\Mis\ExGratiaModel;
use App\models\Admin\Mis\ExGratiaNoCasesModel;
use App\models\Common\{
    AcModel,
    DistrictModel,
    StateModel
};
use App\Exports\ExcelExport;

date_default_timezone_set('Asia/Kolkata');

class CeoExgratiaController extends Controller {

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
        $this->ExGratiaModel = new ExGratiaModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

    public function addExgratia(Request $request) {
        DB::beginTransaction();
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

                $data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = StateModel::where('ST_CODE',$d->st_code)->first()->ST_NAME;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;
				
				$disArr = array();
				$acArr = array();
				$pcArr = array();
				
				$pcArr = $this->ExGratiaModel->getPcByst($d->st_code);
				$disArr = DistrictModel::where('ST_CODE',$d->st_code)->get();
				$data['districtlist'] = $disArr;
				$data['pcArr'] = $pcArr;


                if ($request->has('_token')) {
					
					if(!empty($request->exgratia_pending) && $request->exgratia_pending=='granted'){
						
						$validatedData = $request->validate([
                        'election_type' => 'required','election_year' => 'required','st_code' => 'required','dist_no' => 'required',
                        'applicant_name' => 'required','applicant_address' => 'required','applicant_designation' => 'required',
                        'payment_amount' => 'required','date_of_payment' => 'required',
                        'exgratia_pending' => 'required','accident_date' => 'required',
                        'injury_details' => 'required','accident_place' => 'required','accident_reason' => 'required'
						]);
					}else if(!empty($request->exgratia_pending) && $request->exgratia_pending=='pending'){
						$validatedData = $request->validate([
                        'election_type' => 'required','election_year' => 'required','st_code' => 'required','dist_no' => 'required',
                        'applicant_name' => 'required','applicant_address' => 'required','applicant_designation' => 'required',
                        'exgratia_pending' => 'required',
						'accident_date' => 'required','injury_details' => 'required','accident_place' => 'required','accident_reason' => 'required',
                        'reason_for_pending' => 'required',
						]);
					}else{
						$validatedData = $request->validate([
                        'election_type' => 'required','election_year' => 'required','st_code' => 'required','dist_no' => 'required',
                        'applicant_name' => 'required','applicant_address' => 'required','applicant_designation' => 'required',
                        'exgratia_pending' => 'required',
						'accident_date' => 'required','injury_details' => 'required','accident_place' => 'required','accident_reason' => 'required'
						]);
					}
					
					if(!empty($request->election_type)){
						if(($request->election_type==1 || $request->election_type==2) && empty($request->ac_no)){
							return Redirect::back()->with('error_msg', 'Please select ac');
						}
						if(($request->election_type==3 || $request->election_type==4) && empty($request->pc_no)){
							return Redirect::back()->with('error_msg', 'Please select pc');
						}
					}
					
					

                    $newRecord = new ExGratiaModel();
					$newRecord->officer_id = $d->id;
					$newRecord->officer_role = $d->role_id;
					$newRecord->st_code = $request->st_code;
                    $newRecord->election_type = $request->election_type;
					if(!empty($request->election_type) && ($request->election_type==1 || $request->election_type==2)){
						$newRecord->ac_no = $request->ac_no;
					}
					if(!empty($request->election_type) && ($request->election_type==3 || $request->election_type==4)){
						$newRecord->pc_no = $request->pc_no;
					}
                    $newRecord->election_year = $request->election_year;
                    $newRecord->dist_no = $request->dist_no;
                    $newRecord->applicant_name = $request->applicant_name;
                    $newRecord->applicant_address = $request->applicant_address;
                    $newRecord->applicant_designation = $request->applicant_designation;
                    $newRecord->applicant_parent_department = $request->applicant_parent_department;
                    if(!empty($request->exgratia_pending) && $request->exgratia_pending=='granted'){
						$newRecord->date_of_payment = date('Y-m-d',strtotime($request->date_of_payment));
						$newRecord->payment_amount = $request->payment_amount;
					}else{
						$newRecord->date_of_payment = '';
						$newRecord->payment_amount = 0;
					}
					if(!empty($request->exgratia_pending) && ($request->exgratia_pending=='granted' || $request->exgratia_pending=='rejected')){
						$newRecord->date_of_action = date('Y-m-d',strtotime($request->date_of_action));
					}else{
						$newRecord->date_of_action = '';
					}
					
                    $newRecord->application_status = $request->exgratia_pending;
                    $newRecord->contact_no = $request->contact_no;
                    $newRecord->accident_date = date('Y-m-d',strtotime($request->accident_date));
                    $newRecord->accident_place = $request->accident_place;
                    $newRecord->injury_details = $request->injury_details;
                    $newRecord->injury_description = $request->injury_description;
                    $newRecord->accident_reason = $request->accident_reason;
                    $newRecord->reason_for_pending = $request->reason_for_pending;
                    $newRecord->case_details = $request->case_details;

                    $newRecord->save();
                    if (isset($newRecord->id) && $newRecord->id > 0) {
						//Updating no cases if user doing first entry
						$save_noncases = ExGratiaNoCasesModel::FirstOrNew(['st_code'=>$user->st_code]);
						$save_noncases->officer_id = $user->id;
						$save_noncases->st_code = $user->st_code;
						$save_noncases->nocases = 0;
						$save_noncases->record_date = Carbon::now();
						$return_id = $save_noncases->save();
						
                        DB::commit();
                        return Redirect('acceo/mis/list-exgratia')->with('success_msg','Form submitted successfully.');
                    } else {
                        DB::rollback();
                        return Redirect::back()->with('error_msg', 'Sorry! something went wrong,please try again.');
                    }
                }


                return view('admin.mis.ceo.ex-gratia.add-exgratia', $data);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function listExgratia() {
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
				
				
				
				$allcases = array();
				$garantedcases = array();
				$rejectedcases = array();
				$pendingcases = array();
				$noncases_data = array();
				
				$allcases = $this->ExGratiaModel->getAllCases($d->st_code);
				$garantedcases = $this->ExGratiaModel->getGrantedCases($d->st_code);
				$rejectedcases = $this->ExGratiaModel->getRejectedCases($d->st_code);
				$pendingcases = $this->ExGratiaModel->getPendingCases($d->st_code);
				//echo "<pre>";print_r($allcases);die;
				
				$noncases_data = ExGratiaNoCasesModel::where('st_code',$user->st_code)->first();
				$data['noncases_data'] = $noncases_data;
				//dd($noncases_data);

				$data['allcases'] = $allcases;
				$data['garantedcases'] = $garantedcases;
				$data['rejectedcases'] = $rejectedcases;
				$data['pendingcases'] = $pendingcases;
				
				$data['elections'] = array("1"=>"AC-General","2"=>"AC-BYE","3"=>"PC-General","4"=>"PC-BYE");
				$data['injury'] = array("1"=>"Injury","2"=>"Death","3"=>"Permanent disability");
				$data['reason'] = array("1"=>"Health Issue","2"=>"Due to voilent act","3"=>"Any other");
				
				$data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = $d->st_code;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;

                return view('admin.mis.ceo.ex-gratia.list-exgratia', $data);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
	
	public function editExgratia(Request $request,$id) {
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
				
				$listGratia = array();
				$listGratia = ExGratiaModel::where('id',decrypt($id))->first();
				$data['listData'] = $listGratia;
				
				//dd($data['listData']);
				
				$ac_list = array();
				if(isset($listGratia)){
					if($listGratia->election_type){
						if($listGratia->election_type ==1 || $listGratia->election_type==2){
							$ac_list = $this->ExGratiaModel->getAcByst($listGratia->st_code, $listGratia->dist_no);
						}
					}
				}
				
				$data['ac_list'] = $ac_list;
				//echo "<pre>";print_r($listGratia);die;
				
				$disArr = array();
				$acArr = array();
				$pcArr = array();
				
				$pcArr = $this->ExGratiaModel->getPcByst($d->st_code);
				$disArr = DistrictModel::where('ST_CODE',$d->st_code)->get();
				$data['districtlist'] = $disArr;
				$data['pcArr'] = $pcArr;

				$st_name = "";
				
				$st_details = StateModel::where('ST_CODE',$d->st_code)->first();
				if(!empty($st_details)){
					$st_name = $st_details->ST_NAME;
				}
				
				
				$data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = $st_name;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;

                return view('admin.mis.ceo.ex-gratia.edit-exgratia', $data);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
	
	public function updateExgratia(Request $request) {
        DB::beginTransaction();
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

                $data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = StateModel::where('ST_CODE',$d->st_code)->first()->ST_NAME;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;
				
				$disArr = array();
				$acArr = array();
				$pcArr = array();
				

                if ($request->has('_token')) {
					
                    if(!empty($request->exgratia_pending) && $request->exgratia_pending=='granted'){
						
						$validatedData = $request->validate([
                        'election_type' => 'required','election_year' => 'required','st_code' => 'required','dist_no' => 'required',
                        'applicant_name' => 'required','applicant_address' => 'required','applicant_designation' => 'required',
                        'payment_amount' => 'required','date_of_payment' => 'required',
                        'exgratia_pending' => 'required','accident_date' => 'required',
                        'injury_details' => 'required','accident_place' => 'required','accident_reason' => 'required'
						]);
					}else if(!empty($request->exgratia_pending) && $request->exgratia_pending=='pending'){
						$validatedData = $request->validate([
                        'election_type' => 'required','election_year' => 'required','st_code' => 'required','dist_no' => 'required',
                        'applicant_name' => 'required','applicant_address' => 'required','applicant_designation' => 'required',
                        'exgratia_pending' => 'required',
						'accident_date' => 'required','injury_details' => 'required','accident_place' => 'required','accident_reason' => 'required',
                        'reason_for_pending' => 'required',
						]);
					}else{
						$validatedData = $request->validate([
                        'election_type' => 'required','election_year' => 'required','st_code' => 'required','dist_no' => 'required',
                        'applicant_name' => 'required','applicant_address' => 'required','applicant_designation' => 'required',
                        'exgratia_pending' => 'required',
						'accident_date' => 'required','injury_details' => 'required','accident_place' => 'required','accident_reason' => 'required'
						]);
					}
					
					if(!empty($request->election_type)){
						if(($request->election_type==1 || $request->election_type==2) && empty($request->ac_no)){
							return Redirect::back()->with('error_msg', 'Please select ac');
						}
						if(($request->election_type==3 || $request->election_type==4) && empty($request->pc_no)){
							return Redirect::back()->with('error_msg', 'Please select pc');
						}
					}
					//echo decrypt($request->fid);die;
					
					$newRecord = ExGratiaModel::find(decrypt($request->fid));
                    $newRecord->election_type = $request->election_type;
					if(!empty($request->election_type) && ($request->election_type==1 || $request->election_type==2)){
						$newRecord->ac_no = $request->ac_no;
					}
					if(!empty($request->election_type) && ($request->election_type==3 || $request->election_type==4)){
						$newRecord->pc_no = $request->pc_no;
					}
                    $newRecord->election_year = $request->election_year;
                    $newRecord->dist_no = $request->dist_no;
                    $newRecord->applicant_name = $request->applicant_name;
                    $newRecord->applicant_address = $request->applicant_address;
					$newRecord->applicant_designation = $request->applicant_designation;
                    $newRecord->applicant_parent_department = $request->applicant_parent_department;
                    $newRecord->payment_amount = $request->payment_amount;
					if(!empty($request->exgratia_pending) && $request->exgratia_pending=='granted'){
						$newRecord->date_of_payment = date('Y-m-d',strtotime($request->date_of_payment));
						$newRecord->payment_amount = $request->payment_amount;
					}else{
						$newRecord->date_of_payment = '';
						$newRecord->payment_amount = 0;
					}
					if(!empty($request->exgratia_pending) && ($request->exgratia_pending=='granted' || $request->exgratia_pending=='rejected')){
						$newRecord->date_of_action = date('Y-m-d',strtotime($request->date_of_action));
					}else{
						$newRecord->date_of_action = '';
					}

                    $newRecord->application_status = $request->exgratia_pending;
                    $newRecord->contact_no = $request->contact_no;
                    $newRecord->accident_date = date('Y-m-d',strtotime($request->accident_date));
                    $newRecord->accident_place = $request->accident_place;
                    $newRecord->accident_reason = $request->accident_reason;
					$newRecord->injury_details = $request->injury_details;
                    $newRecord->injury_description = $request->injury_description;
                    $newRecord->reason_for_pending = $request->reason_for_pending;
					$newRecord->case_details = $request->case_details;

                    $newRecord->save();
                    if (isset($newRecord->id) && $newRecord->id > 0) {
                        DB::commit();
                        return Redirect('acceo/mis/list-exgratia')->with('success_msg','Form submitted successfully.');
                    } else {
                        DB::rollback();
                        return Redirect::back()->with('error_msg', 'Sorry! something went wrong,please try again.');
                    }
                }
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
	
	
	public function pdfExgratia() {
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
				ini_set('max_execution_time', 300);
				ini_set("memory_limit","850M");
				ini_set("pcre.backtrack_limit", "5000000");
				
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($uid);
                $list_record = $this->ECIModel->getallelectionphasewise();
                $list_phase = $this->ECIModel->listcurrentelectionphase();
                $list_electionid = $this->ECIModel->getallelectionbyid();
                $list = $this->ECIModel->listelectiontype();
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
				
				
				
				$listGratia = array();
				$listGratia = ExGratiaModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
							->leftJoin('m_district', 'm_district.DIST_NO', '=', 'mis_exgratia_details.dist_no')
							->where('m_district.st_code',$d->st_code)
							->where('mis_exgratia_details.st_code',$d->st_code)->select('mis_exgratia_details.*','m_state.ST_NAME','m_district.DIST_NAME')->get();
				//echo "<pre>";print_r($listGratia);die;

				$data['listData'] = $listGratia;
				
				$data['elections'] = array("1"=>"AC-General","2"=>"AC-BYE","3"=>"PC-General","4"=>"PC-BYE");
				$data['injury'] = array("1"=>"Injury","2"=>"Death","3"=>"Permanent disability");
				$data['reason'] = array("1"=>"Health Issue","2"=>"Due to voilent act","3"=>"Any other");
				
				$data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = $d->st_code;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;
				
				$name_excel = time();

				$setting_pdf = [
				  'margin_top'        => 40,        // Set the page margins for the new document.
				  'margin_bottom'     => 10,    
				];
				
				
				$pdf = \PDF::loadView('admin.mis.ceo.ex-gratia.list-exgratia-pdf', $data, [], $setting_pdf);
				return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
	
	public function reportExgratia(Request $request){
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
				
				
				
				$listGratia = array();
				$listGratia = ExGratiaModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
							->leftJoin('m_district', 'm_district.DIST_NO', '=', 'mis_exgratia_details.dist_no')
							->where('m_district.st_code',$d->st_code)
							->where('mis_exgratia_details.st_code',$d->st_code)->select('mis_exgratia_details.*','m_state.ST_NAME','m_district.DIST_NAME')->get();
				//echo "<pre>";print_r($listGratia);die;

				$data['listData'] = $listGratia;
				$data['elections'] = array("1"=>"AC-General","2"=>"AC-BYE","3"=>"PC-General","4"=>"PC-BYE");
				$data['injury'] = array("1"=>"Injury","2"=>"Death","3"=>"Permanent disability");
				$data['reason'] = array("1"=>"Health Issue","2"=>"Due to voilent act","3"=>"Any other");
				
				$data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = $d->st_code;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;
				return view('admin.mis.ceo.ex-gratia.list-exgratia-report', $data);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
	}
	
	public function reportExgratiaPdf(Request $request){
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
				
				
				
				$listGratia = array();
				$listGratia = ExGratiaModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
							->leftJoin('m_district', 'm_district.DIST_NO', '=', 'mis_exgratia_details.dist_no')
							->where('m_district.st_code',$d->st_code)
							->where('mis_exgratia_details.st_code',$d->st_code)->select('mis_exgratia_details.*','m_state.ST_NAME','m_district.DIST_NAME')->get();
				//echo "<pre>";print_r($listGratia);die;

				$data['listData'] = $listGratia;
				$data['elections'] = array("1"=>"AC-General","2"=>"AC-BYE","3"=>"PC-General","4"=>"PC-BYE");
				$data['injury'] = array("1"=>"Injury","2"=>"Death","3"=>"Permanent disability");
				$data['reason'] = array("1"=>"Health Issue","2"=>"Due to voilent act","3"=>"Any other");
				
				$data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = $d->st_code;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;

				$name_excel = time();

				$setting_pdf = [
				  'margin_top'        => 40,        // Set the page margins for the new document.
				  'margin_bottom'     => 10,    
				];
				
				$pdf = \PDF::loadView('admin.mis.ceo.ex-gratia.list-exgratia-pdf', $data, [], $setting_pdf);
				return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
	}
	
	public function reportExgratiaExcel(Request $request){
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
				
				
				
				$listGratia = array();
				$listGratia = ExGratiaModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
							->leftJoin('m_district', 'm_district.DIST_NO', '=', 'mis_exgratia_details.dist_no')
							->where('m_district.st_code',$d->st_code)
							->where('mis_exgratia_details.st_code',$d->st_code)->select('mis_exgratia_details.*','m_state.ST_NAME','m_district.DIST_NAME')->get();

				$elections = array("1"=>"AC-General","2"=>"AC-BYE","3"=>"PC-General","4"=>"PC-BYE");
				$injury = array("1"=>"Injury","2"=>"Death","3"=>"Permanent disability");
				$reason = array("1"=>"Health Issue","2"=>"Due to voilent act","3"=>"Any other");
		
				set_time_limit(6000);
				$export_data = [];
				$title = 'List of Ex-Gratia cases';
				$export_data[] = [$title];
				$headings[] = [];

				$export_data[] = ['Sl.No.','Name of State and District','Name of Election','Name of State who has to pay ex-gratia compensation',
				'Name of polling personnel','Address of the applicant','Applicant designation','Applicant Parent department','Payment amount','Date of Payment','Contact No','Ex-gratia Pending','Reasons for pending','Date of injury/Death',
				'Place of injury/Death','Deatils Of Death/Injury For Pending Cases Due for payment',
				'Reason Of Death/Injury For Pending Cases Due for payment','Date Applied'];
			 
				foreach ($listGratia as $k=>$v) {

						$export_data[] = [
											  ++$k,
											  $v->ST_NAME .'-'. $v->DIST_NAME,
											  $v->election_year .'-'. (!empty($v->election_type))?$elections[$v->election_type]:'',
											  $v->ST_NAME,
											  $v->applicant_name,
											  $v->applicant_address,
											  $v->applicant_designation,
											  $v->applicant_parent_department,
											  $v->payment_amount,
											  (!empty($v->date_of_payment))?date('d-M-Y',strtotime($v->date_of_payment)):'',
											  $v->contact_no,
											  ucfirst($v->exgratia_pending),
											  $v->reason_for_pending,
											  (!empty($v->accident_date))?date('d-M-Y',strtotime($v->accident_date)):'',
											  $v->accident_place,
											  (!empty($v->injury_details))?$injury[$v->injury_details]:'',
											  (!empty($v->accident_reason))?$reason[$v->accident_reason]:'',
											  date('d-M-Y',strtotime($v->created_at))
											  
										];
				}

		$name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $title));
		return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
						
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
	}
	
	
	public function getConst(request $request) {
        $district = $request->input('district');
        $stcode = $request->input('stcode');
        $acdata = $this->ExGratiaModel->getAcByst($stcode, $district);
        return $acdata;
    }

    public function getConstPC(request $request) {
        $pcdata = $this->ExGratiaModel->getPcByst($stcode);
        return $pcdata;
    }
	
	public function addNoPendingCases(request $request){
		$user = Auth::user();
        if (Auth::check()) {
			try{
				$caseid = $request->input('caseid');
				$remarks = $request->input('remarksval');
				$save_noncases = ExGratiaNoCasesModel::FirstOrNew(['st_code'=>$user->st_code]);
				$save_noncases->officer_id = $user->id;
				$save_noncases->st_code = $user->st_code;
				$save_noncases->remarks = $remarks;
				$save_noncases->nocases = ($caseid=='')?0:$caseid;
				$save_noncases->record_date = Carbon::now();
				$return_id = $save_noncases->save();
				if($return_id){
					return response()->json(['success' => true,'msg'=>'Record saved successfully']);
				}else{
					return response()->json(['success' => false,'msg'=>'Oops! please try again.']);
				}
				
			}catch (Exception $ex) {
					return Redirect('/internalerror')->with('error', 'Internal Server Error');
			}
		}else{
			return response()->json(['success' => false,'msg'=>'Session expired.']);
		}
	}
	

    //
}

// end class
