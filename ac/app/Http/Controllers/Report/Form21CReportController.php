<?php

namespace App\Http\Controllers\Report;

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

class Form21CReportController extends Controller {

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
            $this->middleware('ro');
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

    /**
     * Form 21C
     */
    public function getForm21C() {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        //
        $pc_list = array();
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

                $acno = '';
                $ac_val = '';
                $ac_name = '';
                $nac_no = '';
                $ac_name1 = '';
                $get_win_candidate = array();
				$state_name = '';

                $acno = $user->ac_no;
                $stateid = $user->st_code;
				
				$state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $stateid)->first();
                if($state_name){
                    $state_name = $state_name->ST_NAME;
                }
				
                $ac_val = DB::table('m_ac')->select('AC_NO', 'AC_NAME','AC_TYPE')->where('ST_CODE', $stateid)->where('AC_NO', '=', $acno)->first();
                if($ac_val){
					if($ac_val->AC_TYPE<>'GEN'){
						$ac_name = $ac_val->AC_NO.'-'.$ac_val->AC_NAME.' ('.$ac_val->AC_TYPE.')';
						$nac_no = ($ac_val->AC_NO < 100) ? '0' . $ac_val->AC_NO : $ac_val->AC_NO;
						$ac_name1 = $nac_no."-".$ac_val->AC_NAME .'('.$ac_val->AC_TYPE.') ';
					}else{
						$ac_name = $ac_val->AC_NO.'-'.$ac_val->AC_NAME;
						$nac_no = ($ac_val->AC_NO < 100) ? '0' . $ac_val->AC_NO : $ac_val->AC_NO;
						$ac_name1 = $nac_no."-".$ac_val->AC_NAME ;
					}
                    
                }
                

                $get_win_candidate = DB::table('winning_leading_candidate as wincan')
                        ->leftJoin('candidate_personal_detail as can_perd', 'wincan.candidate_id', '=', 'can_perd.candidate_id')
                        ->select('wincan.lead_cand_name','wincan.status', 'wincan.lead_cand_party', 'can_perd.candidate_id','can_perd.candidate_residence_districtno','can_perd.candidate_residence_stcode', 'can_perd.candidate_residence_address')
                        ->where('wincan.st_code', '=', $stateid)->where('wincan.ac_no', '=', $acno)
                        ->first();
				$can_district='';$cand_state='';
				
				if($get_win_candidate){
					if($get_win_candidate->status=='1'){
                                            $can_district = DB::table('m_district')->select('DIST_NAME')->where('ST_CODE', '=', $get_win_candidate->candidate_residence_stcode)->where('DIST_NO', '=', $get_win_candidate->candidate_residence_districtno)->first();
                                            if($can_district){
                                                $can_district = $can_district->DIST_NAME;
                                            }
                                            $cand_state = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $get_win_candidate->candidate_residence_stcode)->first();
                                            if($cand_state){
                                                $cand_state = $cand_state->ST_NAME;
                                            }
					}
				}
                if($ele_details->ELECTION_TYPEID==4){
                    return view('admin.countingReport.form21c.form21d-report', ['ele_details' => $ele_details, 'user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_phase' => $list_phase, 'list_electionid' => $list_electionid, 'list' => $list,'ac_state'=>$state_name, 'acname' => $ac_name, 'ac_name1' => $ac_name1,'state' => $cand_state,'dist'=>$can_district, 'wincan' => $get_win_candidate]);
                }else{
                    return view('admin.countingReport.form21c.form21c-report', ['ele_details' => $ele_details, 'user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_phase' => $list_phase, 'list_electionid' => $list_electionid, 'list' => $list,'ac_state'=>$state_name, 'acname' => $ac_name, 'ac_name1' => $ac_name1,'state' => $cand_state,'dist'=>$can_district, 'wincan' => $get_win_candidate]);
                }
                
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getForm21CPdf() {
        if (Auth::check()) {
            try {
                $user = Auth::user();
                
                $acno = '';
                $ac_val = '';
                $ac_name = '';
                $nac_no = '';
                $ac_name1 = '';
                $get_win_candidate = array();

                $acno = $user->ac_no;
                $stateid = $user->st_code;
                
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($uid);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
				
                $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $stateid)->first();
                if($state_name){
                    $state_name = $state_name->ST_NAME;
                }
				
                $ac_val = DB::table('m_ac')->select('AC_NO', 'AC_NAME','AC_TYPE')->where('ST_CODE', $stateid)->where('AC_NO', '=', $acno)->first();
                if($ac_val){
					if($ac_val->AC_TYPE<>'GEN'){
						$ac_name = $ac_val->AC_NO.'-'.$ac_val->AC_NAME.' ('.$ac_val->AC_TYPE.')';
						$nac_no = ($ac_val->AC_NO < 100) ? '0' . $ac_val->AC_NO : $ac_val->AC_NO;
						$ac_name1 = $nac_no."-".$ac_val->AC_NAME .'('.$ac_val->AC_TYPE.') ';
					}else{
						$ac_name = $ac_val->AC_NO.'-'.$ac_val->AC_NAME;
						$nac_no = ($ac_val->AC_NO < 100) ? '0' . $ac_val->AC_NO : $ac_val->AC_NO;
						$ac_name1 = $nac_no."-".$ac_val->AC_NAME;
					}
                    
                }
           
                $get_win_candidate = DB::table('winning_leading_candidate as wincan')
                        ->leftJoin('candidate_personal_detail as can_perd', 'wincan.candidate_id', '=', 'can_perd.candidate_id')
                        ->select('wincan.lead_cand_name','wincan.status', 'wincan.lead_cand_party', 'can_perd.candidate_id','can_perd.candidate_residence_districtno','can_perd.candidate_residence_stcode', 'can_perd.candidate_residence_address')
                        ->where('wincan.st_code', '=', $stateid)->where('wincan.ac_no', '=', $acno)
                        ->first();
                $date = $acno.'-'.$ac_name1;
                $can_district='';$cand_state='';
				
                if($get_win_candidate){
                        //if($get_win_candidate->status=='1'){
                            $can_district = DB::table('m_district')->select('DIST_NAME')->where('ST_CODE', '=', $get_win_candidate->candidate_residence_stcode)->where('DIST_NO', '=', $get_win_candidate->candidate_residence_districtno)->first();
                            if($can_district){
                                $can_district = $can_district->DIST_NAME;
                            }
                            $cand_state = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $get_win_candidate->candidate_residence_stcode)->first();
                            if($cand_state){
                                $cand_state = $cand_state->ST_NAME;
                            }
                        //}
                }

                 if($ele_details->ELECTION_TYPEID==4){
                     $name_excel = 'Form20D-'.$user->st_code."_ac_no".$user->ac_no.'_'.date('d-m-Y').'_'.time();
                     $data['heading_title']  ='Generate Form20D';  
            }
           elseif($ele_details->ELECTION_TYPEID==3){
                    $name_excel = 'Form20C-'.$user->st_code."_ac_no".$user->ac_no.'_'.date('d-m-Y').'_'.time();
                   $data['heading_title']  ='Generate Form20C';
              }  

                  $data['file_name']=$name_excel; 
                 
                   $data['ref_no']  =time();

                    $log_data = array( 'st_code'=>$user->st_code,
                                          'election_id'=>$ele_details->ELECTION_ID,
                                          'election_typeid'=>$ele_details->ELECTION_TYPEID, 
                                          'pc_no'=>'0', 
                                          'ac_no'=>$user->ac_no, 
                                          'ps_no'=>'0',
                                          'doc_type'=>$data['heading_title'],
                                          'file_name'=>$name_excel.".pdf",
                                          'table_name'=>'',
                                          'table_primary_key'=>'0', 
                                          'log_date_time'=>date('Y-m-d H:i:a'),
                                          'added_create_at'=>date('Y-m-d'),
                                          'ref_no'=> $data['ref_no'],
                                          'created_by'=>\Auth::user()->officername);

                            \App\models\Counting\CountingPrintlogModel::clone_record($log_data);

                      $data['user']=\Auth::user()->officername;
                      $data['print_date']=date('d-m-Y H:i:a');

                if($ele_details->ELECTION_TYPEID==4){
                    $pdf = PDF::loadView('admin.countingReport.form21c.form21d-report-pdf', ['user_data' => $user,'ac_state'=>$state_name, 'acname' => $ac_name, 'ac_name1' => $ac_name1, 'state' => $cand_state,'dist'=>$can_district, 'wincan' => $get_win_candidate,'ref_no'=>$data['ref_no']]);
                    return $pdf->download( $name_excel.'.pdf');
                }else{
                    $pdf = PDF::loadView('admin.countingReport.form21c.form21c-report-pdf', ['user_data' => $user,'ac_state'=>$state_name, 'acname' => $ac_name, 'ac_name1' => $ac_name1, 'state' => $cand_state,'dist'=>$can_district, 'wincan' => $get_win_candidate,'ref_no'=>$data['ref_no']]);
                    return $pdf->download( $name_excel.'.pdf');
                }
                
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getForm21CUpload() {
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

                $acno = $user->ac_no;
                $stateid = $user->st_code;
                $acno = '';
                $ac_val = '';
                $ac_name = '';
                $nac_no = '';
                $ac_name1 = '';
                $get_win_candidate = array();

                $acno = $user->ac_no;
                $stateid = $user->st_code;
                $ac_val = DB::table('m_ac')->select('AC_NO', 'AC_NAME')->where('ST_CODE', $stateid)->where('AC_NO', '=', $acno)->first();
                if($ac_val){
                    $ac_name = $ac_val->AC_NAME . ' -' . $ac_val->AC_NO;
                    $nac_no = ($ac_val->AC_NO < 100) ? '0' . $ac_val->AC_NO : $ac_val->AC_NO;
                    $ac_name1 = $ac_val->AC_NAME . ' ' . $nac_no;
                }

                return view('admin.countingReport.form21c.form21c-upload', ['ele_details' => $ele_details, 'user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_phase' => $list_phase, 'list_electionid' => $list_electionid, 'list' => $list, 'acname' => $ac_name, 'ac_name1' => $ac_name1]);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function storeFile(Request $request) {
        $users = Session::get('admin_login_details');
        $user = Auth::user();

        if (Auth::check()) {
            $rules = ['form21' => 'required|max:2048|mimes:pdf'];

            $customMessages = [
                'required' => 'Please select file.',
                'max' => 'The file size is large use only 2 mb file.',
                'mimes' => 'Select only pdf file.',
            ];
            $this->validate($request, $rules, $customMessages);

            try {
                // Handle File Upload
                if ($request->hasFile('form21')) {
                    $filenameWithExt = $request->file('form21')->getClientOriginalName();
                    $extension = $request->file('form21')->getClientOriginalExtension();
                    if ($extension != 'pdf') {
                        session()->flash('emsg', 'Please select only pdf file.');
                        return redirect()->back();
                    }
                    $mime_type = $request->file('form21')->getClientMimeType();
                    if ($mime_type != 'application/pdf') {
                        session()->flash('emsg', 'Please select valid pdf file.');
                        return redirect()->back();
                    }
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $fileNameToStore = $user->st_code . '_AC' . $user->ac_no . '_' . time() . '.' . $extension;

                    $uid = $user->id;
                    $d = $this->commonModel->getunewserbyuserid($uid);
                    $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, 'AC');

                    //saving  data to database
                    $datetime = date('Y-m-d H:i:s');
                    $date = date('Y-m-d H:i:s');
                    if($ele_details){
                        
                        $file_path = '';

                        $file_path = '/uploads1/form' . $request->form_type . '/' . $ele_details->CONST_TYPE . '/' . date('Y') . '/' . $fileNameToStore;
                        
                        $check_exist = DB::table('counting_form21_detail')->select('id')->where('st_code', $user->st_code)->where('ac_no', '=', $user->ac_no)->where('form_type',$request->form_type)->get();

                        if(count($check_exist)>0){
                            DB::table('counting_form21_detail')->where('st_code', $user->st_code)->where('ac_no', $user->ac_no)->where('form_type',$request->form_type)
                            ->update([
                            'st_code' => $ele_details->ST_CODE, 'ac_no' => $user->ac_no, 'const_type' => $ele_details->CONST_TYPE,
                            'election_type_id' => $ele_details->ELECTION_TYPEID, 'election_id' => $ele_details->ELECTION_ID, 'form21_path' => $file_path, 'form_type' => $request->form_type,
                            'form21_uploaded_time' => $datetime,'added_update_at' => $date,'updated_at' => $datetime,
                            'updated_by' => $user->officername]);
                            session()->flash('smsg', 'Old File updated successfully.');
                        }else{
                            DB::table('counting_form21_detail')->insert([
                            'st_code' => $ele_details->ST_CODE, 'ac_no' => $user->ac_no, 'const_type' => $ele_details->CONST_TYPE,
                            'election_type_id' => $ele_details->ELECTION_TYPEID, 'election_id' => $ele_details->ELECTION_ID, 'form21_path' => $file_path, 'form_type' => $request->form_type,
                            'form21_uploaded_time' => $datetime,'added_update_at' => $date, 'created_at' => $datetime,
                            'created_by' => $user->officername
                            ]);
                            session()->flash('smsg', 'File uploaded successfully.');
                        }
                        //moving file to local storage
                        $request->file('form21')->move(public_path('/uploads1/form' . $request->form_type . '/' . $ele_details->CONST_TYPE . '/' . date('Y') . '/'), $fileNameToStore);
                    }else{
                        session()->flash('emsg', 'File not uploaded, please try again.');
                    }
                    return redirect()->back();
                } else {
                    session()->flash('emsg', 'Please select file.');
                    return redirect()->back();
                }
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

}

// end class
