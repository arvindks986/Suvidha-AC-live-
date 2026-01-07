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

class EciExgratiaController extends Controller {

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
				
				$pcArr = $this->ExGratiaEciModel->getPcByst($d->st_code);
				$disArr = DistrictModel::where('ST_CODE',$d->st_code)->get();
				$data['districtlist'] = $disArr;
				$data['pcArr'] = $pcArr;


                if ($request->has('_token')) {
					
                    $validatedData = $request->validate([
                        'election_type' => 'required',
                        'election_year' => 'required',
                        'st_code' => 'required',
                        'dist_no' => 'required',
                        'applicant_name' => 'required',
                        'applicant_address' => 'required',
                        'contact_no' => 'required',
                        'exgratia_pending' => 'required',
                        'accident_date' => 'required',
                        'injury_details' => 'required',
                        'accident_place' => 'required',
                        'accident_reason' => 'required',
                        'reason_for_pending' => 'required',
                    ]);
					
					if(!empty($request->election_type)){
						if(($request->election_type==1 || $request->election_type==2) && empty($request->ac_no)){
							return Redirect::back()->with('error_msg', 'Please select ac');
						}
						if(($request->election_type==3 || $request->election_type==4) && empty($request->pc_no)){
							return Redirect::back()->with('error_msg', 'Please select pc');
						}
					}

                    $newRecord = new ExGratiaEciModel();
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
                    $newRecord->exgratia_pending = $request->exgratia_pending;
                    $newRecord->contact_no = $request->contact_no;
                    $newRecord->accident_date = date('Y-m-d',strtotime($request->accident_date));
                    $newRecord->accident_place = $request->accident_place;
                    $newRecord->injury_details = $request->injury_details;
                    $newRecord->injury_description = $request->injury_description;
                    $newRecord->accident_reason = $request->accident_reason;
                    $newRecord->reason_for_pending = $request->reason_for_pending;

                    $newRecord->save();
                    if (isset($newRecord->id) && $newRecord->id > 0) {
                        DB::commit();
                        return Redirect('acceo/mis/list-exgratia')->with('success_msg','Form submitted successfully.');
                    } else {
                        DB::rollback();
                        return Redirect::back()->with('error_msg', 'Sorry! something went wrong,please try again.');
                    }
                }


                return view('admin.mis.eci.ex-gratia.add-exgratia', $data);
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
				
				
				
				$allcases = array();
				$garantedcases = array();
				$rejectedcases = array();
				$pendingcases = array();
				
				if($user->role_id==50){
					$allcases = $this->ExGratiaEciModel->getAllCases($d->st_code,"");
					$garantedcases = $this->ExGratiaEciModel->getAllCases($d->st_code,'granted');
					$rejectedcases = $this->ExGratiaEciModel->getAllCases($d->st_code,'rejected');
					$pendingcases = $this->ExGratiaEciModel->getAllCases($d->st_code,'pending');
				}else{
					$allcases = $this->ExGratiaEciModel->getAllECICases("");
					$garantedcases = $this->ExGratiaEciModel->getAllECICases('granted');
					$rejectedcases = $this->ExGratiaEciModel->getAllECICases('rejected');
					$pendingcases = $this->ExGratiaEciModel->getAllECICases('pending');
				}
				//echo "<pre>";print_r($allcases);die;

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

                return view('admin.mis.eci.ex-gratia.list-exgratia', $data);
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
				$listGratia = ExGratiaEciModel::where('id',decrypt($id))->first();
				$data['listData'] = $listGratia;
				
				$ac_list = array();
				if(isset($listGratia)){
					if($listGratia->election_type){
						if($listGratia->election_type ==1 || $listGratia->election_type==2){
							$ac_list = $this->ExGratiaEciModel->getAcByst($listGratia->st_code, $listGratia->dist_no);
						}
					}
				}
				
				$data['ac_list'] = $ac_list;
				//echo "<pre>";print_r($listGratia);die;
				
				$disArr = array();
				$acArr = array();
				$pcArr = array();
				
				$pcArr = $this->ExGratiaEciModel->getPcByst($d->st_code);
				$disArr = DistrictModel::where('ST_CODE',$d->st_code)->get();
				$data['districtlist'] = $disArr;
				$data['pcArr'] = $pcArr;

				$st_name = "";
				if(!empty($listGratia)){
					$st_details = StateModel::where('ST_CODE',$listGratia->st_code)->first();
					if(!empty($st_details)){
						$st_name = $st_details->ST_NAME;
					}
				}
				
				$data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = $st_name;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;

                return view('admin.mis.eci.ex-gratia.edit-exgratia', $data);
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
					
                    $validatedData = $request->validate([
                        'election_type' => 'required',
                        'election_year' => 'required',
                        'st_code' => 'required',
                        'dist_no' => 'required',
                        'applicant_name' => 'required',
                        'applicant_address' => 'required',
                        'contact_no' => 'required',
                        'exgratia_pending' => 'required',
                        'accident_date' => 'required',
                        'accident_place' => 'required',
                        'injury_details' => 'required',
                        'accident_reason' => 'required',
                        'reason_for_pending' => 'required',
                    ]);
					
					if(!empty($request->election_type)){
						if(($request->election_type==1 || $request->election_type==2) && empty($request->ac_no)){
							return Redirect::back()->with('error_msg', 'Please select ac');
						}
						if(($request->election_type==3 || $request->election_type==4) && empty($request->pc_no)){
							return Redirect::back()->with('error_msg', 'Please select pc');
						}
					}
					//echo decrypt($request->fid);die;
					
					$newRecord = ExGratiaEciModel::find(decrypt($request->fid));
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
                    $newRecord->exgratia_pending = $request->exgratia_pending;
                    $newRecord->contact_no = $request->contact_no;
                    $newRecord->accident_date = date('Y-m-d',strtotime($request->accident_date));
                    $newRecord->accident_place = $request->accident_place;
                    $newRecord->accident_reason = $request->accident_reason;
					$newRecord->injury_details = $request->injury_details;
                    $newRecord->injury_description = $request->injury_description;
                    $newRecord->reason_for_pending = $request->reason_for_pending;

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
				$listGratia = ExGratiaEciModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
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
				
				
				$pdf = \PDF::loadView('admin.mis.eci.ex-gratia.list-exgratia-pdf', $data, [], $setting_pdf);
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
				if($user->role_id==50){
					$listGratia = $this->ExGratiaEciModel->getAllCases($d->st_code,"");
				}else{
					$listGratia = $this->ExGratiaEciModel->getAllECICases("");
				}

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
				return view('admin.mis.eci.ex-gratia.list-exgratia-report', $data);
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
				if($user->role_id==50){
					$listGratia = $this->ExGratiaEciModel->getAllCases($d->st_code,"");
				}else{
					$listGratia = $this->ExGratiaEciModel->getAllECICases("");
				}

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
				
				$pdf = \PDF::loadView('admin.mis.eci.ex-gratia.list-exgratia-pdf', $data, [], $setting_pdf);
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
				$listGratia = $this->ExGratiaEciModel->getAllCases($d->st_code,"");
				
				$elections = array("1"=>"AC-General","2"=>"AC-BYE","3"=>"PC-General","4"=>"PC-BYE");
				$injury = array("1"=>"Injury","2"=>"Death","3"=>"Permanent disability");
				$reason = array("1"=>"Health Issue","2"=>"Due to voilent act","3"=>"Any other");
		
				set_time_limit(6000);
				$export_data = [];
				$title = 'Ex gratia report';
				$export_data[] = [$title];
				$headings[] = [];

				$export_data[] = ['Sl.No.','Name of State and District','Name of Election','Name of State who has to pay ex-gratia compensation',
				'Name of polling personnel','Address of the applicant','Contact No','Ex-gratia Pending','Reasons for pending','Date of injury/Death',
				'Place of injury/Death','Deatils Of Death/Injury For Pending Cases Due for payment',
				'Reason Of Death/Injury For Pending Cases Due for payment','Date Applied'];
				
			 
				foreach ($listGratia as $k=>$v) {
						$dist=DistrictModel::where('ST_CODE',$v->st_code)->where('DIST_NO',$v->dist_no)->first();
						//dd($dist);
						$export_data[] = [
											  ++$k,
											  $v->ST_NAME .'-'. (!empty($dist))?$dist->DIST_NAME:'',
											  $v->election_year .'-'. (!empty($v->election_type))?$elections[$v->election_type]:'',
											  $v->ST_NAME,
											  $v->applicant_name,
											  $v->applicant_address,
											  $v->contact_no,
											  ucfirst($v->exgratia_pending),
											  $v->reason_for_pending,
											  date('d-M-Y',strtotime($v->accident_date)),
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
        $acdata = $this->ExGratiaEciModel->getAcByst($stcode, $district);
        return $acdata;
    }

    public function getConstPC(request $request) {
        $pcdata = $this->ExGratiaEciModel->getPcByst($stcode);
        return $pcdata;
    }
	
	
	public function countReportExgratia(Request $request){
		
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
				$data = array("role"=>$user->role_id,"state"=>$d->st_code,"dist"=>$d->dist_no);
				$listGratia = $this->ExGratiaEciModel->getAllECICountReport($data);

				$data['listData'] = $listGratia;
				
				$data['user_data'] = $d;
                $data['ele_details'] = $ele_details;
                $data['st_code'] = $d->st_code;
                $data['st_name'] = $d->st_code;
                $data['dist_no'] = $d->dist_no;
                $data['dist_name'] = '';
                $data['lists'] = $list;
				return view('admin.mis.eci.ex-gratia.exgratia-count-report', $data);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
	}
	
	public function countReportExgratiaPdf(Request $request){
		
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
				$data = array("role"=>$user->role_id,"state"=>$d->st_code,"dist"=>$d->dist_no);
				$listGratia = $this->ExGratiaEciModel->getAllECICountReport($data);

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
				
				$pdf = \PDF::loadView('admin.mis.eci.ex-gratia.count-report-exgratia-pdf', $data, [], $setting_pdf);
				return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
	}
	

    //
}

// end class
