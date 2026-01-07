<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\adminmodel\DeoPcPermissionModel;
use Illuminate\Http\Request;
use Session; 
use App\commonModel;
use App\adminmodel\CandidateModel;
use App\adminmodel\ROPCModel;
use App\Classes\xssClean;
use PDF;
use Carbon\Carbon;
use App\Helpers\SendNotification;
use App\Helpers\SmsgatewayHelper;
use App\Http\helpers;
use App\Helpers\LogNotification;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class DeoPCPermissionController extends Controller {
    public $commonModel = null;
    public $xssClean = null;
    public $PM = null;
    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('deo');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->PM = new DeoPcPermissionModel();
    }
    
    
     public function AgentCreation(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
          
            return view('admin.ac.deo.Permission.Agent', ['user_data' => $d,'ele_details' => $ele_details]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddAgent(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
 try {
            if(config('public_config.permission_log'))
                     { 

                        $message=array();
                        $message['eventTime']= date('Y-m-d H:i:s');
                        $message['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
                        $message['UserType']=$user->officername ?? '';
                        $message['MobNo']= $user->Phone_no ?? '';
                        $message['UserName']= $user->name ?? '';
                        $message['applicationType']= 'Permission';                        
                        $message['Module']= 'SUVIDHA';                        
                        $message['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
                        $message['TransectionAction']= 'Data Submit';
                      $message['LogDescription']= 'Agent Add Successfully';
                      $message['TransectionStatus']= 'SUCCESS';
                      LogNotification::LogInfo($message);
                      }

            if (isset($_POST['addag'])) {


                $rules = [
                    'uname' => 'required|regex:/(^[a-zA-Z0-9_\-\s]+$)/',
//                    'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'desig' => 'required',
                    'email' => 'required|email:rfc,dns',
                    'mb' => 'required|numeric|digits:10',
                    'pass' => 'required|min:6',
                    'pincode' => 'required|min:6',
                     
//                    'address'=>'required|not_regex:/([<>@$%?]+)/',
                ];
                $messages = [
                    'uname.required' => ' Name field is required.',
                    'uname.regex' => 'Please Enter only alphanumeric character.',
//                    'address.required' => ' Address field is required.',
//                    'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'mb.required' => ' Mobile no is required.',
                    'mb.digits' => 'Please Enter valid Mobile Number.',
//                    'dept.required' => 'Departemnt is required',
//                    'dept.regex' => 'Please Enter only alphanumeric character.',
                    'desig.required' => 'Designation is required Field',
                    'desig.regex' => 'Please Enter only alphanumeric character.',
                    'email.required' => 'Email is required',
                    'pass.required' => 'Password Field is required',
                    'pass.min' => 'Min length of password is 6',
                    'pincode.required' => 'Pincode Field is required',
                    'pincode.min' => 'Min length of Pincode is 6',
                     
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {

                    $randnum = rand(1, 99);
//                    $officerid = 'ROFC' . $d->st_code . $d->ac_no;
//                $pass = bcrypt($officerid);
                    if (!empty($req->desig)) {
                        $designation = strip_tags($req->desig);
                    }
                    if (!empty($req->uname)) {
                        $uname = strip_tags($req->uname);
                    }
                    if (!empty($req->mb)) {
                        $mb = strip_tags($req->mb);
                    }
                    if (!empty($req->email)) {
                        $email = strip_tags($req->email);
                    }
                    if (!empty($req->pass)) {
                        $pass = hash('sha256',$req->pass);
                    }
                    if (!empty($req->pincode)) {
                        $pin = bcrypt($req->pincode);
                    }
                    else{
                        $pin = bcrypt(123456);
                    }
                    //$pin = bcrypt(1234);
//                if(!empty($req->dept))
//                {
//                    $department= strip_tags($req->dept);
//                }
                    $where = array('Phone_no' => $mb);
                    $chckloc = DB::table('officer_login')->where($where)->count();
                    if ($chckloc == 0) {
                        $data = array('two_step_pin'=>$pin,'parent_id'=>$d->id,'officername' => $mb, 'designation' => $designation, 'name' => $uname, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no,  'Phone_no' => $mb, 'email' => $email, 'role_id' => '24', 'officerlevel' => 'DEO-OFFICE', 'password' => $pass,'password_flag'=>1,'pass_flag'=>1);
                        $result = $this->PM->insertdata('officer_login', $data);
                        if ($result == 1) {
                            return redirect('/acdeo/viewagent')->with('message', 'Successfully Created');
                        } else {
                            return redirect()->back()->with('message', 'Not Created');
                        }
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered  mobile no is already Exist!')->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            }
//            return view('admin.ac.ro.permission.Agent', ['user_data' => $d]);
        
}

        catch (Exception $e){
                if(config('public_config.permission_log'))
           { $message=array();
            $message['eventTime']= date('Y-m-d H:i:s');
    $message['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
    $message['UserType']=$user->officername ?? '';
    $message['MobNo']= $user->Phone_no ?? '';
    $message['UserName']= $user->name ?? '';
    $message['applicationType']= 'Permission';                        
    $message['Module']= 'SUVIDHA';                        
    $message['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    $message['TransectionAction']= 'Data Submit';
              $message['LogDescription']= 'Something went to wrong '.$e->getMessage();
              $message['TransectionStatus']= 'Failed';
            LogNotification::LogInfo($message);
             }
            
         }
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewAgent(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $where = array('st_code' => $d->st_code,'dist_no'=>$d->dist_no);
            $getAgentList = $this->PM->getAgentList($where);
            return view('admin.ac.deo.Permission.ViewAgentList', ['user_data' => $d], ['getAgentList' => $getAgentList],['ele_details' => $ele_details]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditAgent(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            if (isset($_POST['editag'])) {
                $rules = [
                    'uname' => 'required|regex:/(^[a-zA-Z0-9_\-\s]+$)/',
//                    'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'email' => 'required|email:rfc,dns',
                    'mb' => 'required|numeric|digits:10',
//                    'pass'=>'required|min:6',
//                    'address'=>'required|not_regex:/([<>@$%?]+)/',
                ];
                $messages = [
                    'uname.required' => ' Name field is required.',
                    'uname.regex' => 'Please Enter only alphanumeric character.',
//                    'address.required' => ' Address field is required.',
//                    'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'mb.required' => ' Mobile no is required.',
                    'mb.digits' => 'Please Enter valid Mobile Number.',
//                    'dept.required' => 'Departemnt is required',
//                    'dept.regex' => 'Please Enter only alphanumeric character.',
                    'desig.required' => 'Designation is required Field',
                    'desig.regex' => 'Please Enter only alphanumeric character.',
                    'email.required' => 'Email is required',
//                    'pass.required'=>'Password Field is required',
//                    'pass.min'=>'Min length of password is 6',
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    if (!empty($req->desig)) {
                        $designation = strip_tags($req->desig);
                    }
                    if (!empty($req->uname)) {
                        $uname = strip_tags($req->uname);
                    }
                    if (!empty($req->mb)) {
                        $mb = strip_tags($req->mb);
                    }
                    if (!empty($req->email)) {
                        $email = strip_tags($req->email);
                    }
//                if(!empty($req->pass))
//                {
//                    $pass= bcrypt(strip_tags($req->pass));
//                }
//                if(!empty($req->dept))
//                {
//                    $department= strip_tags($req->dept);
//                }
                    $where = array('Phone_no' => $mb);
                    $chckloc = DB::table('officer_login')->where($where)->count();
                   // if ($chckloc == 0) {
                    $data = array('designation' => $designation, 'name' => $uname, 'Phone_no' => $mb, 'email' => $email);
                    $where = array('id' => $req->id, 'role_id' => $req->role_id);
                    $update = $this->PM->updatetable('officer_login', $where, $data);
                    return redirect()->back()->with('message', 'Successfully Updated');
                   /*  } else {
                        return redirect()->back()->with('chckmessage', 'Entered  mobile no is already Exist!')->withInput();
                    } */
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            } else {
                $getAgentDetails = $this->PM->getAgentDetails(Crypt::decryptString($req->id));
//                print_r($getAgentDetails);die;
                return view('admin.ac.deo.Permission.EditAgentList', ['user_data' => $d], ['getAgentList' => $getAgentDetails],['ele_details' => $ele_details]);
            }
        } else {
            return redirect('/officer-login');
        }
    }
     public function EditAgentStatus(Request $req) {
         if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $data = explode('#', $req->status);
            $status = $data[0];
            $id = $data[1];
            if ($status == 1) {
                $where = array('id' => $id, 'role_id' => '24');
                $cond = array('is_active' => '0');
                $res = $this->PM->updatetable('officer_login', $where, $cond);
                if ($res == 1) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $where = array('id' => $id, 'role_id' => '24');
                $cond = array('is_active' => '1');
                $res = $this->PM->updatetable('officer_login', $where, $cond);
                if ($res == 1) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } 
        else {
            return redirect('/officer-login');
        }
    }
    
    
    public function PermissionCount() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
             
//                $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
//                $seched=getschedulebyid($ele_details->ScheduleID);
//                $sechdul=checkscheduledetails($seched);
//                echo '<pre/>';
//                print_r($d);die;
            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no);
            $allrecord = $this->PM->totalPermissionReport($where);
             $where1 = array($d->st_code, $d->dist_no);
           $totalPermissionReport = $this->PM->totalPermissionReportData($where1);
           $getallac=DB::table('m_ac')->select('AC_NO','AC_NAME')->where('ST_CODE',$d->st_code)->where('DIST_NO_HDQTR',$d->dist_no)->get()->toArray();
//           echo '<pre/>';
//           print_r($totalPermissionReport);die;
            return view('admin.ac.deo.Permission.PermissionReport', ['user_data' => $d,'ele_details' => $ele_details,'allrecord' => $allrecord,'totalPermissionReport'=>$totalPermissionReport,'getallac'=>$getallac]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionCountDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);

            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no);
           
            $allrecord = $this->PM->totalPermissionReport($where);
            $where1 = array($d->st_code, $d->dist_no);
            if ($req->statusid != 'NULL') {
                if($req->statusid == '22')
                {
                    $totalReportDetails=$this->PM->totalPermissionReportData($where1);
                
                }
                else if($req->statusid == '01')
                {
                    $totalReportDetails=$this->PM->totalPendingReportDetails($where1);
                }
                else
                {
                    $totalReportDetails = $this->PM->totalReportDetails($where1, $req->statusid);
                     
                }
                return $totalReportDetails;
//                return view('admin.ac.ro.Permission.AllPendingReport', ['user_data' => $d,'allrecord'=>$allrecord,'totalReportDetails'=>$totalReportDetails]);
            }
        } else {
            return redirect('/officer-login');
        }
    }

     public function PermissionDetailsView(Request $req)
    {
//         echo 'ok';die;
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
        $id=$req->id;
        $loc_id=$req->loc_id;
        $getNodaldetails = $this->PM->getNodaldetails($id);
        $getRodetails = $this->PM->getRodetails1($id,$req->status);
//        print_r($getRodetails);die;
//        $getDetailsview = $this->PM->getDetails($id,$loc_id);
        $getDetailsview = $this->PM->getDetails($id, $loc_id);
             if(empty($getDetailsview))
             {
                 $getDetailsview = $this->PM->getIntraDetails($id,$loc_id);
             }
        return view('admin.ac.deo.Permission.AcceptPermissiondetails')->with(array('user_data' => $d,'ele_details' => $ele_details, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails));
        } else {
            return redirect('/officer-login');
        } 
    }
    
    public function generatePDF(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $data = ['title' => 'Welcome to HDTuto.com'];
           $id= $req->id;
            $data1= explode('&',$id);
            $p_id=$data1[0];
            $status=$data1[1];
           $prmsndetails = DB::table('permission_request')->select('ac_no', 'pc_no', 'dist_no','assigned_police_st_id')->where('id', $p_id)->first();
            $getDetailsview = $this->PM->getDetails($p_id, $status);
            if (empty($getDetailsview) && !empty($prmsndetails->pc_no)) {
                $getDetailsview = $this->PM->getIntraDetails($p_id, $status);
            } else {
                $getDetailsview = $this->PM->getIntradistDetails($p_id, $status);
            }
            $getRodetails = $this->PM->getRodetails($p_id);
            
            
            if (!empty($prmsndetails)) {
                if (!empty($prmsndetails->ac_no)) {
                    $allac = explode(',', $prmsndetails->ac_no);
                    $allac_name = DB::table('m_ac')->select('AC_NAME')->whereIn('AC_NO', $allac)->where('st_code', $d->st_code)->get()->toArray();
                }
                if (!empty($prmsndetails->assigned_police_st_id)) {
                    $allps = explode(',', $prmsndetails->assigned_police_st_id);
                    $allps_name = DB::table('police_station_master')->select('police_st_name')->whereIn('id', $allps)->get()->toArray();
                }
                if(!empty($allac_name) && !empty($allps_name))
                {
                 $pdf = PDF::loadView('admin.ac.deo.Permission.Reciept', ['user_data' => $d,'allac_name'=>$allac_name,'allps_name'=>$allps_name,'getDetails' => $getDetailsview, 'getRodetails' => $getRodetails]);
                }
                else
                {
            $pdf = PDF::loadView('admin.ac.deo.Permission.Reciept', ['user_data' => $d,'getDetails' => $getDetailsview, 'getRodetails' => $getRodetails]);
            }
            }
            return $pdf->download('mypdf.pdf');
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function GetAllACPermission(Request $req)
    {
       if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel); 
            $acid= $req->acid;
            $allrecord=$this->PM->getAllAcRecord($d->st_code,$acid,$d->dist_no);
            return $allrecord;
            
             } else {
            return redirect('/officer-login');
        }
    }
    
    
    //All access
     public function allMasters() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            return view('admin.ac.deo.Permission.Masters', ['user_data' => $d]);
        } else {
            return redirect('/officer-login');
        }
    }

// end index function

    public function OfflinePermission(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);

            $getrodetails = $this->PM->getLoginUserdetails($d->id);
 
            if ($req->view == '0') {
                if (!empty($req->permsn_id)) {
                    $getPermissionDetails = DB::table('permission_required_doc')
                                    ->select('*')->where('permission_id', $req->permsn_id)->where('st_code', $d->st_code)->get()->toArray();
                    if (!empty($getPermissionDetails)) {
//                    print_r($getPermissionDetails);die;
                        return $getPermissionDetails;
                    } else {
                        return '0';
                    }
                }
                $user_details_police = $this->PM->user_details_police($req->stcode, $req->district, $req->ac_no);
                return $user_details_police;
            } else {
                if($d->role_id != 24)
                {
                $permission_type = DB::table('permission_type as a')
                                ->join('permission_master as m', 'm.id', '=', 'a.permission_type_id')
                                ->select('m.permission_name as pname', 'a.*', 'a.id as permsn_id', 'm.officer_role_id')
                                ->where('a.status', '1')
                                ->whereIn('a.role_id',[$d->role_id])
                                ->where('a.st_code', $d->st_code)->get()->toArray();
                }
                else{
                    $permission_type = DB::table('permission_type as a')
                                ->join('permission_master as m', 'm.id', '=', 'a.permission_type_id')
                                ->select('m.permission_name as pname', 'a.*', 'a.id as permsn_id', 'm.officer_role_id')
                                ->where('a.status', '1')
                                ->where('a.role_id',5)
                                ->where('a.st_code', $d->st_code)->get()->toArray();
                }
                $getAllUserType = $this->PM->getAllUserType();
                $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
                 $allParty =DB::table('m_party')->select('*')
                ->where('CCODE', '<>', '1180')
                ->where('PARTYSYM', '<>', '-1')
                ->where('deleteflag', 'N')
                ->orderBy('PARTYNAME')->get()->toArray();
                $getAllAC=DB::table('m_ac')->select('AC_NO','AC_NAME')->where('ST_CODE',$d->st_code)->where('DIST_NO_HDQTR',$d->dist_no)->get()->toArray();
                return view('admin.ac.deo.Permission.OfflinePermissionApply')->with(array('user_data' => $d, 'getrodetails' => $getrodetails, 'showpage' => 'permission', 'permission_type' => $permission_type,'getAllUserType' => $getAllUserType,'allParty' => $allParty,'getAllPC'=>$getAllPC,'getAllAC'=>$getAllAC));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function getUserDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $usermb = $req->mb_no;
            $chck = DB::table('user_login')->where('mobile', $usermb)->count();
            $chck1 = DB::table('user_data')->where('mobileno', $usermb)->count();
            $chckparty = DB::table('user_login')->where('mobile', $usermb)->whereNotNull('party_id')->where('party_id', '!=', 0)->select('party_id')->count();
            $chckrole = DB::table('user_login')->where('mobile', $usermb)->whereNotNull('role_id')->select('role_id')->count();
//            echo $chckrole;die;
//            echo $chck;die;
            if ($chck != 0 && $chck1 == 0) {

                if ($chckparty != 0 && $chckrole != 0) {
//                    echo '1';die;
                    $res = $this->PM->getLoginCandDetails($usermb);
                } else {
//                    echo '2';die;
                    $res = $this->PM->getLoginappCandDetails($usermb);
                }
//                $res = $this->PM->getUserDetails($usermb);
            } else if ($chck != 0 && $chck1 != 0) {
                if ($chckparty != 0 && $chckrole != 0) {
//                    echo '3';die;
                    $res = $this->PM->getUserDetails($usermb);
                } else {
//                    echo '4';die;
                    $res = $this->PM->getUserappDetails($usermb);
                }
//                $res = $this->PM->getLoginCandDetails($usermb);
            }
            if (!empty($res)) {
//                print_r($res);die;
                return $res;
            } else {
                echo 'No record';
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function UserDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            
            $time = Carbon::now()->timestamp;
            $rules=[];
            $ptypeid=[];
            $messages=[];
            $getDistName = getdistrictbydistrictno($d->st_code, $d->dist_no);
            $getStateName = getstatebystatecode($d->st_code);
            $document = $req->input('doc');
//            echo '<pre/>';
//            print_r($req->all());die;
              try {
            if(config('public_config.permission_log'))
                     { 

                        $message=array();
                        $message['eventTime']= date('Y-m-d H:i:s');
    $message['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
    $message['UserType']=$user->officername ?? '';
    $message['MobNo']= $user->Phone_no ?? '';
    $message['UserName']= $user->name ?? '';
    $message['applicationType']= 'Permission';                        
    $message['Module']= 'SUVIDHA';                        
    $message['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    $message['TransectionAction']= 'Data Submit';
                      $message['LogDescription']= 'Permission Add Successfully';
                      $message['TransectionStatus']= 'SUCCESS';
                      LogNotification::LogInfo($message);
                      }
 
                if (!empty($req->permission_type) && $req->permission_type != 0) {
                    
                    $ptype = $req->permission_type;
                    $ptypeid = explode('#', $ptype);
                    if (!empty($ptypeid) && $ptypeid[1] == 3 || $ptypeid[1] == 6) {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                            //'user_email' => 'required|email',
                            'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'gender' => 'required',
                            'dob' =>'required|date|before:-18 Years',
                            'address' => 'required|not_regex:/([<>@$%?]+)/',
                            'state' => 'required',
                            'district' => 'required',
                           // 'ac_no' => 'required',
                           // 'police_station' => 'required|not_in:0',
                            'permission_type' => 'required|not_in:0',
                            'user_type' => 'required|not_in:0',
                            'stdate' => 'required',
                            'enddate' => 'required',
                            'subdate' => 'required',
                            'permsndoc.*.p_doc' => 'mimes:pdf',
                            'political_party' => 'required|not_in:0'
                        ];
                        $messages = [
                            'user_mb.required' => ' Mobile field is required.',
                            'user_mb.digits' => 'Please Enter valid Mobile Number.',
                            //'user_email.required' => ' Email field is required.',
                            //'user_email.email' => 'Please Enter valid Email',
                            'fathers_name.required' => 'Fathers Name is required.',
                            'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                            'user_name.required' => 'Name is required',
                            'user_name.regex' => 'Please Enter only alphanumeric character.',
                            'gender.required' => 'Gender is required Field',
                            'dob.required' => 'DOB is required',
                            'address.required' => 'Address is required',
                            'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                            'state.required' => 'State is Required field',
                            'district.required' => 'District is Required field',
                            //'ac_no.required' => 'AC Required field',
                            //'police_station.required' => 'Police Station is Required field',
                            'permission_type.required' => 'Permission Type is Required field',
                            'user_type' => 'User Type Required field',
                            'stdate.required' => 'Select start date',
                            'enddate.required' => 'Select end date',
                            'subdate.required' => 'Submission date is Required',
                            'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                            'political_party.required' => 'Please Select Political Party'
                        ];
                    }
                else if(!empty($ptypeid) && $ptypeid[1] == 8)
                {
                    $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                            //'user_email' => 'required|email',
                            'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'gender' => 'required',
                            'dob' =>'required|date|before:-18 Years',
                            'address' => 'required|not_regex:/([<>@$%?]+)/',
                            'state' => 'required',
                            'district' => 'required',
//                            'ac_no' => 'required',
//                            'police_station' => 'required|not_in:0',
                            'permission_type' => 'required|not_in:0',
                            'user_type' => 'required|not_in:0',
                            'stdate' => 'required',
                            'enddate' => 'required',
                            'subdate' => 'required',
                            'permsndoc.*.p_doc' => 'mimes:pdf',
                            'political_party' => 'required|not_in:0'
                        ];
                        $messages = [
                            'user_mb.required' => ' Mobile field is required.',
                            'user_mb.digits' => 'Please Enter valid Mobile Number.',
                           // 'user_email.required' => ' Email field is required.',
                            //'user_email.email' => 'Please Enter valid Email',
                            'fathers_name.required' => 'Fathers Name is required.',
                            'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                            'user_name.required' => 'Name is required',
                            'user_name.regex' => 'Please Enter only alphanumeric character.',
                            'gender.required' => 'Gender is required Field',
                            'dob.required' => 'DOB is required',
                            'address.required' => 'Address is required',
                            'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                            'state.required' => 'State is Required field',
                            'district.required' => 'District is Required field',
//                            'ac_no.required' => 'AC Required field',
//                            'police_station.required' => 'Police Station is Required field',
                            'permission_type.required' => 'Permission Type is Required field',
                            'user_type' => 'User Type Required field',
                            'stdate.required' => 'Select start date',
                            'enddate.required' => 'Select end date',
                            'subdate.required' => 'Submission date is Required',
                            'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                            'political_party.required' => 'Please Select Political Party'
                        ];
                }
                else {
                    
                    $rules = [
                        'user_mb' => 'required|numeric|digits:10',
                        //'user_email' => 'required|email',
                        'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'gender' => 'required',
                        'dob' =>'required|date|before:-18 Years',
                        'address' => 'required|not_regex:/([<>@$%?]+)/',
                        'state' => 'required',
                        'district' => 'required',
                        //'ac_no' => 'required',
                       // 'police_station' => 'required|not_in:0',
                        'permission_type' => 'required|not_in:0',
                       // 'location' => 'required|not_in:0',
                        'user_type' => 'required|not_in:0',
                        'stdate' => 'required',
                        'enddate' => 'required',
                        'subdate' => 'required',
                        'permsndoc.*.p_doc' => 'mimes:pdf',
                        'political_party' => 'required|not_in:0'
                    ];
                    $messages = [
                        'user_mb.required' => ' Mobile field is required.',
                        'user_mb.digits' => 'Please Enter valid Mobile Number.',
                       // 'user_email.required' => ' Email field is required.',
                       // 'user_email.email' => 'Please Enter valid Email',
                        'fathers_name.required' => ' Fathers Name is required.',
                        'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                        'user_name.required' => 'Name is required',
                        'user_name.regex' => 'Please Enter only alphanumeric character.',
                        'gender.required' => 'Gender is required Field',
                        'dob.required' => 'DOB is required',
                        'address.required' => 'Address is required',
                        'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'state.required' => 'State is Required field',
                        'district.required' => 'District is Required field',
                        //'ac_no.required' => 'AC is Required field',
                       // 'police_station.required' => 'Police Station is Required field',
                        'permission_type.required' => 'Permission Type is Required field',
                       // 'location' => 'Location is Required field',
                        'user_type' => 'User Type is Required field',
                        'stdate.required' => 'Select start date',
                        'enddate.required' => 'Select end date',
                        'subdate.required' => 'Submission date is Required',
                        'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                        'political_party.required' => 'Please Select Political Party'
                    ];

                    if ($req->location == 'other') {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                        //'user_email' => 'required|email',
                        'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'gender' => 'required',
                        'dob' =>'required|date|before:-18 Years',
                        'address' => 'required|not_regex:/([<>@$%?]+)/',
                        'state' => 'required',
                        'district' => 'required',
                        //'ac_no' => 'required',
                        //'police_station' => 'required|not_in:0',
                        'permission_type' => 'required|not_in:0',
                       // 'location' => 'required|not_in:0',
                        'user_type' => 'required|not_in:0',
                        'stdate' => 'required',
                        'enddate' => 'required',
                        'subdate' => 'required',
                        'permsndoc.*.p_doc' => 'mimes:pdf',
                        'political_party' => 'required|not_in:0',
                            'other' => 'required',
                        ];
                        $messages = [
                             'user_mb.required' => ' Mobile field is required.',
                        'user_mb.digits' => 'Please Enter valid Mobile Number.',
                       // 'user_email.required' => ' Email field is required.',
                      //  'user_email.email' => 'Please Enter valid Email',
                        'fathers_name.required' => ' Fathers Name is required.',
                        'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                        'user_name.required' => 'Name is required',
                        'user_name.regex' => 'Please Enter only alphanumeric character.',
                        'gender.required' => 'Gender is required Field',
                        'dob.required' => 'DOB is required',
                        'address.required' => 'Address is required',
                        'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'state.required' => 'State is Required field',
                        'district.required' => 'District is Required field',
                        //'ac_no.required' => 'AC is Required field',
                        //'police_station.required' => 'Police Station is Required field',
                        'permission_type.required' => 'Permission Type is Required field',
                        //'location' => 'Location is Required field',
                        'user_type' => 'User Type is Required field',
                        'stdate.required' => 'Select start date',
                        'enddate.required' => 'Select end date',
                        'subdate.required' => 'Submission date is Required',
                        'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                        'political_party.required' => 'Please Select Political Party',
                            'other.required' => 'Please Enter Other location name',
                        ];
                    }
                }
                }
                else {
                    
                    $rules = [
                        'user_mb' => 'required|numeric|digits:10',
                        //'user_email' => 'required|email',
                        'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'gender' => 'required',
                       'dob' =>'required|date|before:-18 Years',
                        'address' => 'required|not_regex:/([<>@$%?]+)/',
                        'state' => 'required',
                        'district' => 'required',
                       // 'ac_no' => 'required',
                       // 'police_station' => 'required|not_in:0',
                        'permission_type' => 'required|not_in:0',
                       // 'location' => 'required|not_in:0',
                        'user_type' => 'required|not_in:0',
                        'stdate' => 'required',
                        'enddate' => 'required',
                        'subdate' => 'required',
                        'permsndoc.*.p_doc' => 'mimes:pdf',
                        'political_party' => 'required|not_in:0'
                    ];
                    $messages = [
                        'user_mb.required' => ' Mobile field is required.',
                        'user_mb.digits' => 'Please Enter valid Mobile Number.',
                        //'user_email.required' => ' Email field is required.',
                        //'user_email.email' => 'Please Enter valid Email',
                        'fathers_name.required' => ' Fathers Name is required.',
                        'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                        'user_name.required' => 'Name is required',
                        'user_name.regex' => 'Please Enter only alphanumeric character.',
                        'gender.required' => 'Gender is required Field',
                        'dob.required' => 'DOB is required',
                        'address.required' => 'Address is required',
                        'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'state.required' => 'State is Required field',
                        'district.required' => 'District is Required field',
                        //'ac_no.required' => 'AC is Required field',
                       // 'police_station.required' => 'Police Station is Required field',
                        'permission_type.required' => 'Permission Type is Required field',
                        //'location' => 'Location is Required field',
                        'user_type' => 'User Type is Required field',
                        'stdate.required' => 'Select start date',
                        'enddate.required' => 'Select end date',
                        'subdate.required' => 'Submission date is Required',
                        'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                        'political_party.required' => 'Please Select Political Party'
                    ];

                    if ($req->location == 'other') {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                       // 'user_email' => 'required|email',
                        'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'gender' => 'required',
                        'dob' =>'required|date|before:-18 Years',
                        'address' => 'required|not_regex:/([<>@$%?]+)/',
                        'state' => 'required',
                        'district' => 'required',
                       // 'ac_no' => 'required',
                       // 'police_station' => 'required|not_in:0',
                        'permission_type' => 'required|not_in:0',
                       // 'location' => 'required|not_in:0',
                        'user_type' => 'required|not_in:0',
                        'stdate' => 'required',
                        'enddate' => 'required',
                        'subdate' => 'required',
                        'permsndoc.*.p_doc' => 'mimes:pdf',
                        'political_party' => 'required|not_in:0',
                            'other' => 'required',
                        ];
                        $messages = [
                             'user_mb.required' => ' Mobile field is required.',
                        'user_mb.digits' => 'Please Enter valid Mobile Number.',
                        //'user_email.required' => ' Email field is required.',
                        //'user_email.email' => 'Please Enter valid Email',
                        'fathers_name.required' => ' Fathers Name is required.',
                        'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                        'user_name.required' => 'Name is required',
                        'user_name.regex' => 'Please Enter only alphanumeric character.',
                        'gender.required' => 'Gender is required Field',
                        'dob.required' => 'DOB is required',
                        'address.required' => 'Address is required',
                        'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'state.required' => 'State is Required field',
                        'district.required' => 'District is Required field',
                        //'ac_no.required' => 'AC is Required field',
                        //'police_station.required' => 'Police Station is Required field',
                        'permission_type.required' => 'Permission Type is Required field',
                       // 'location' => 'Location is Required field',
                        'user_type' => 'User Type is Required field',
                        'stdate.required' => 'Select start date',
                        'enddate.required' => 'Select end date',
                        'subdate.required' => 'Submission date is Required',
                        'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                        'political_party.required' => 'Please Select Political Party',
                            'other.required' => 'Please Enter Other location name',
                        ];
                    }
                }
//                echo '<pre/>';
//                print_r($rules);die;
                $type='Permission';
                $user_mb = strip_tags($req->user_mb);
                $user_name = strip_tags($req->user_name);
                $user_email = strip_tags($req->user_email);
                $fathers_name = strip_tags($req->fathers_name);
                $user_type = strip_tags($req->user_type);
                $gender = strip_tags($req->gender);
                $dob = date('Y-m-d', strtotime(strip_tags($req->dob)));
                $state = strip_tags($req->state);
                $district = strip_tags($req->district);
                if (!empty($req->ac_no)) {
                $acall = $req->ac_no;
                $ac = implode(',', $acall);
                } else {
                    $ac = '0';
                }
                if(!empty($req->pc))
                {
                $pc=strip_tags($req->pc);
                }
                else
                {
                    $pc='0';
                }
                if (!empty($req->police_station)) {
                    $allpolice_station = $req->police_station;
                    $police_station = implode(',', $allpolice_station);
                } else {
                    $police_station = '0';
                }
                $address = strip_tags($req->address);
                if(!empty($ptypeid[0]))
                {
                    $permission_type = strip_tags($ptypeid[0]);
                }
                if(!empty($req->location))
                {
                $location = strip_tags($req->location);
                }
                else
                {
                    $location = '0';
                }
                $party = strip_tags($req->political_party);
//                date('Y-m-d H:i:s', strtotime($date)); 
                $stdate = date('Y-m-d H:i:s', strtotime(strip_tags($req->stdate)));
                $enddate = date('Y-m-d H:i:s', strtotime(strip_tags($req->enddate)));
                $subdate = date('Y-m-d H:i:s', strtotime(strip_tags($req->subdate)));
                $validator = Validator::make($req->all(), $rules, $messages);

$nodaldetailsdeo = DB::table('authority_masters as a')
                     ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                    ->select('a.id', 'a.name')
                     ->where('a.st_code', $state)
                     ->where('a.dist_no', $district)
                     ->where('a.pc_no', $pc)
                     ->where('b.is_active', 1)
                     // ->where('auth_type_id', $allauthdata)
                       ->count();


                if ($validator->passes()) {
                    $other = 'NULL';
                    if (!empty($req->other)) {
                        $other = strip_tags($req->other);
                    }
                    $doc_data = $req->file('permsndoc');
                    $doc_name = '';
                    
                    if (!empty($doc_data)) {
                        sort($doc_data);
                        for ($i = 0; $i < count($doc_data); $i++) {
                            if (!empty($doc_data[$i])) {
                                $doc_name .= 'uploads1/userdoc/permission-document/'.$d->election_id.'/'.$d->st_code.'/'.preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $doc_data[$i]['p_doc']->getClientOriginalName()) . ',';
                                $format = preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $doc_data[$i]['p_doc']->getClientOriginalName());
                                $destinationPath3 = public_path('/uploads1/userdoc/permission-document/'.$d->election_id.'/'.$d->st_code.'/');
                                $doc_data[$i]['p_doc']->move($destinationPath3, $format);
                            }
                        }
                    }
                    $getuserloginid=DB::table('user_login')->select('id','party_id','role_id','permission_request_status')->where('mobile',$user_mb)->get()->first();
                    $getUserdata=DB::table('user_data')->where('mobileno',$user_mb)->count();
                    if (!empty($getuserloginid)) {
//                        echo 'find';die;
                        try
                        {
                            DB::beginTransaction();
                        if ($getuserloginid->role_id == 0 && $getuserloginid->party_id == 0) {
                            $login_data = array('role_id' => $user_type, 'party_id' => $party, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where = array('id' => strip_tags($getuserloginid->id));
                            $insert = $this->PM->updatetable('user_login', $where, $login_data);
                        }

                        if (!empty($getUserdata) && $getUserdata > 0) {
                            if ($getuserloginid->permission_request_status == 1) {
                                $user_data = array('address' => $address, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                                $wheredata = array('user_login_id' => strip_tags($getuserloginid->id));
                                $result = $this->PM->updatetable('user_data', $wheredata, $user_data);
                            } else {
                                $user_data = array('name' => $user_name, 'fathers_name' => $fathers_name, 'email' => $user_email, 'dob' => $dob, 'address' => $address, 'state_id' => $state, 'district_id' => $district, 'ac_id' => $ac, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                                $wheredata = array('user_login_id' => strip_tags($getuserloginid->id));
                                $result = $this->PM->updatetable('user_data', $wheredata, $user_data);
                            }
                        } else {
                            $user_data = array('user_login_id' => strip_tags($getuserloginid->id), 'party_id' => $party, 'name' => $user_name, 'fathers_name' => $fathers_name, 'email' => $user_email, 'mobileno' => $user_mb, 'gender' => $gender, 'dob' => $dob, 'address' => $address, 'state_id' => $state, 'district_id' => $district, 'ac_id' => $ac, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'),'election_id'=>$d->election_id);
                            $result = $this->PM->insertdata('user_data', $user_data);
                        }

                        $permission_data = array('fileserver_dir'=>'uploads1','user_id' => strip_tags($getuserloginid->id), 'party_id' => $party, 'st_code' => $state, 'dist_no' => $district, 'ac_no' => $ac,'pc_no'=>$pc ,'permission_type_id' => $permission_type, 'required_files' => $doc_name, 'location_id' => $location, 'Other_location' => $other, 'date_time_start' => $stdate, 'date_time_end' => $enddate, 'assigned_police_st_id' => $police_station, 'approved_status' => '0', 'user_created_by' => '2','added_at' =>$subdate, 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id,'election_id'=>$d->election_id);
                        $p_data = DB::table('permission_request')->insertGetId($permission_data);
                        if (!empty($p_data) && $p_data != '') {
                            $loginprequest = array('permission_request_status' => '1');
                            $wherelog = array('id' => strip_tags($getuserloginid->id));
                            $updatelog = $this->PM->updatetable('user_login', $wherelog, $loginprequest);
                            $referenceid=array('reference_id'=>$ele_details[0]->ELECTION_ID.$ele_details[0]->CONST_TYPE.$p_data);
                            $whereid = array('id'=>$p_data);
                            $updatepermsnreq = $this->PM->updatetable('permission_request',$whereid,$referenceid);
                            if(!empty($document) && count($document) != 0)
                            {
                            foreach($document as $docdata)
                            {
                            $data1 = DB::table('permission_required_doc')
                                            ->select('authority_type_id')
                                            ->where('id',$docdata['doc_id'])
                                            ->where('st_code', $d->st_code)
                                            ->first();
                                              $data6 = DB::table('officer_login')
                                            ->select('parent_id', 'officerlevel')
                                            ->where('id', $d->id)
                                            ->where('st_code', $d->st_code)
                                            ->get();
                                         


                                            if($data6[0]->officerlevel == 'DEO-OFFICE'){

                                                $created= $data6[0]->parent_id;

                                            }
                                            else{
                                                $created= $d->id;
                                            }
                            $allauthid = explode(',', $data1->authority_type_id);
                            //print_r($nodalid);die;
                            if(!empty($document) && count($allauthid) != 0)
                                    {
                                        foreach($allauthid as $allauthdata)
                                        {
//                                            dd($allauthdata);
                                            if($allauthdata != 'cand01')
                                            {

                            if($nodaldetailsdeo !=''){

                            if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('pc_no', $pc)
                                            ->where('a.dist_no', $district)
                                            ->where('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            ->where('b.pc_no', $pc)
                                            ->where('a.dist_no', $district)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->where('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('a.dist_no', $district)
                                             //->where('pc_no', $pc)
                                            //->whereIn('ac_no', $acall)
                                            ->where('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                             //->whereIn('b.ac_no', $acall)
                                             ->where('a.dist_no', $district)
                                             //->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->where('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        }
                    }
                    else{
                       if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('pc_no', $pc)
                                            ->where('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            ->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->where('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                             //->where('pc_no', $pc)
                                            ->whereIn('ac_no', $acall)
                                            ->where('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                             ->whereIn('b.ac_no', $acall)
                                             //->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->where('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } 
                    }
                            //$nodaldetails = array_merge($nodaldetails1,$nodaldetails2);
                    $nodaldetails = $nodaldetails2;
                           // dd($nodaldetails);
//                            echo '</pre>';
//                            print_r($nodaldetails);die;
//                            if(!empty($ptypeid))
//                            {
//                                if ($ptypeid[1] != 3 && $ptypeid[1] != 6 && $ptypeid[1] != 8) {
                                    if (!empty($nodaldetails)) {
                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                            $nodaldata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                             $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();
                                            if (!empty($getStateName) && !empty($getDistName)) {
                                                $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getDistName->DIST_NAME . ',' . $getStateName->ST_NAME;
                                            
//                                            if (!empty($fcm_id)) {
////                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
//                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
//                                            } 
                                            }
                                        }
                                    }
                                    }
                                             else
                                            {
                                                $nodaldata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_data, 'authority_id' => 'cand01', 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                            }
                                        }
                                    }
                                }
                                $permission_doc_id = $req->permission_type;
                                $permission_doc_id = explode('#', $permission_doc_id);
                                $permission_doc_id = $permission_doc_id[0];
                                 $data1 = DB::table('permission_required_doc as a')
                                            ->select('a.authority_type_id')
                                            ->where('permission_id', $permission_doc_id)
                                            ->where('st_code', $d->st_code)
                                            ->get()->toArray();
                                            $data6 = DB::table('officer_login')
                                            ->select('parent_id', 'officerlevel')
                                            ->where('id', $d->id)
                                            ->where('st_code', $d->st_code)
                                            ->get();
                                         


                                            if($data6[0]->officerlevel == 'DEO-OFFICE'){

                                                $created= $data6[0]->parent_id;

                                            }
                                            else{
                                                $created= $d->id;
                                            }
                                 if(!empty($data1) && count($data1)!= 0)
                                 {
                              foreach($data1 as $doc_auth)
                              {
                            $allauthdata = explode(',', $doc_auth->authority_type_id);
                           if($nodaldetailsdeo !=''){
if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('pc_no', $pc)
                                            ->where('a.dist_no', $district)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            ->where('b.pc_no', $pc)
                                            ->where('a.dist_no', $district)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('a.dist_no', $district)
                                             //->where('pc_no', $pc)
                                             //->whereIn('ac_no', $acall)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            // ->whereIn('b.ac_no', $acall)
                                             ->where('a.dist_no', $district)
                                             //->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        }

 }
else{
    if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            ->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                              ->where('pc_no', $pc)
                                              ->whereIn('ac_no', $acall)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                             ->whereIn('b.ac_no', $acall)
                                             // ->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                           ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        }
}
                           // $nodaldetails = array_merge($nodaldetails1,$nodaldetails2);
                            $nodaldetails = $nodaldetails2;
                            //dd($acall);
                                    if (!empty($nodaldetails)) {
                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                            $nodaldata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);if (!empty($getStateName) && !empty($getDistName)) {
                                            $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();   
                                            $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getDistName->DIST_NAME . ',' . $getStateName->ST_NAME;
                                            
//                                            if (!empty($fcm_id)) {
////                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
//                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
//                                            }
                                            }
                                        }
                                    }
                            }
                                 }
                            }
                            else
                            {
                                
                                $permission_doc_id = $req->permission_type;
                                $permission_doc_id = explode('#', $permission_doc_id);
                                $permission_doc_id = $permission_doc_id[0];
                                 $data1 = DB::table('permission_required_doc as a')
                                            ->select('a.authority_type_id')
                                            ->where('permission_id', $permission_doc_id)
                                            ->where('st_code', $d->st_code)
                                            ->get()->toArray();
                                            $data6 = DB::table('officer_login')
                                            ->select('parent_id', 'officerlevel')
                                            ->where('id', $d->id)
                                            ->where('st_code', $d->st_code)
                                            ->get();
                                         


                                            if($data6[0]->officerlevel == 'DEO-OFFICE'){

                                                $created= $data6[0]->parent_id;

                                            }
                                            else{
                                                $created= $d->id;
                                            }
                                 if(!empty($data1) && count($data1)!= 0)
                                 {
                              foreach($data1 as $doc_auth)
                              {
                            $allauthdata = explode(',', $doc_auth->authority_type_id);
                         if($nodaldetailsdeo !=''){
                          if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                             ->where('pc_no', $pc)
                                             ->where('a.dist_no', $district)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                             ->where('b.pc_no', $pc)
                                             ->where('a.dist_no', $district)
                                             ->where('b.created_by',$created)
                                            ->where('b.is_active', 1)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                             //->where('pc_no', $pc)
                                             ->where('a.dist_no', $district)
                                            //->whereIn('ac_no', $acall)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            //->whereIn('b.ac_no', $acall)
                                           //->where('b.pc_no', $pc)
                                           ->where('a.dist_no', $district)
                                           ->where('b.created_by',$created)
                                            ->where('b.is_active', 1)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } }
                        else
                        {
                            if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                             ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                             ->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            // ->where('pc_no', $pc)
                                            ->whereIn('ac_no', $acall)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                           ->whereIn('b.ac_no', $acall)
                                          //->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        }
                        }
                            //$nodaldetails = array_merge($nodaldetails1,$nodaldetails2);
                        $nodaldetails = $nodaldetails2;
                             //dd('aky');
                                    if (!empty($nodaldetails)) {
                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                            $nodaldata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);if (!empty($getStateName) && !empty($getDistName)) {
                                            $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();    
                                            $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getDistName->DIST_NAME . ',' . $getStateName->ST_NAME;
                                            
//                                            if (!empty($fcm_id)) {
////                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
//                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
//                                            }
                                            }
                                        }
                                    }
                            }
                                 }
                            }
                            DB::commit();
                                    if($user_mb!='') {
                        if($user_type == 2)
                                {
                                  $mob_message="Your permission request has been received with the DEO, to track the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                                }
                                else
                                {
                                    $mob_message="Your permission request has been received with the DEO, to track the status visit website- suvidha.eci.gov.in";
                                }
                        $response = SmsgatewayHelper::gupshup($user_mb,$mob_message);
                      }
//                      if($d->Phone_no!='') {
//                         $permsn_details = DB::table('permission_request as a')
//                                           ->join('permission_type as b','b.id','=','a.permission_type_id')
//                                           ->join('permission_master as c','c.id','=','b.permission_type_id')
//                                           ->where('a.id',$p_data)
//                                           ->select('a.reference_id','a.added_at','c.permission_name')
//                                           ->get()->first();
//                        $mob_message="A New Request has been received for ".$permsn_details->permission_name. "-".$permsn_details->reference_id." ".$permsn_details->added_at;
//                        $response = SmsgatewayHelper::gupshup($d->Phone_no,$mob_message);
//                      }
                            return redirect()->back()->with('message', 'Successfully Permission applied with Reference Id '.$ele_details[0]->ELECTION_ID.$ele_details[0]->CONST_TYPE.$p_data);
                        } else {
                            return redirect()->back()->with('message', 'Permission not applied');
                        }
                        }catch (Exception $e) {
                        DB::rollBack();
                        return $e;
                }
                    } else {
//                         echo 'notfind';die;
                        try
                        {
                            DB::beginTransaction();
                        $login_data = array('name' => $user_name, 'email' => $user_email, 'party_id' => $party, 'mobile' => $user_mb, 'role_id' => $user_type, 'permission_request_status' => '1', 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'),'election_id'=>$d->election_id);

                        $insertid = DB::table('user_login')->insertGetId($login_data);
                        if (!empty($insertid) && $insertid != '') {
                            $user_data = array('user_login_id' => $insertid, 'name' => $user_name, 'party_id' => $party, 'fathers_name' => $fathers_name, 'email' => $user_email, 'mobileno' => $user_mb, 'gender' => $gender, 'dob' => $dob, 'address' => $address, 'state_id' => $state, 'district_id' => $district, 'ac_id' => $ac, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'),'election_id'=>$d->election_id);
                            $result = $this->PM->insertdata('user_data', $user_data);
                            if ($result == 1) {
                                $permission_data = array('fileserver_dir'=>'uploads1','user_id' => $insertid, 'party_id' => $party, 'st_code' => $state, 'dist_no' => $district, 'ac_no' => $ac,'pc_no'=>$pc, 'permission_type_id' => $permission_type, 'required_files' => $doc_name, 'location_id' => $location, 'Other_location' => $other, 'date_time_start' => $stdate, 'date_time_end' => $enddate, 'assigned_police_st_id' => $police_station, 'approved_status' => '0', 'user_created_by' => '2','added_at' =>$subdate, 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id,'election_id'=>$d->election_id);
                                $p_data = DB::table('permission_request')->insertGetId($permission_data);
                                if (!empty($p_data) && $p_data != '') {
                                    $loginprequest = array('permission_request_status' => '1');
                                    $wherelog = array('id' => strip_tags($insertid));
                                    $updatelog = $this->PM->updatetable('user_login', $wherelog, $loginprequest);
                                    
                                    $referenceid=array('reference_id'=>$ele_details[0]->ELECTION_ID.$ele_details[0]->CONST_TYPE.$p_data);
                                    $whereid = array('id'=>$p_data);
                                    $updatepermsnreq = $this->PM->updatetable('permission_request',$whereid,$referenceid);
                                   if(!empty($document) && count($document) != 0)
                                    {
                                    foreach($document as $docdata)
                                    {
                                    $data1 = DB::table('permission_required_doc')
                                                    ->select('authority_type_id')
                                                    ->where('id',$docdata['doc_id'])
                                                    ->where('st_code', $d->st_code)
                                                    ->first();
                                                    $data6 = DB::table('officer_login')
                                            ->select('parent_id', 'officerlevel')
                                            ->where('id', $d->id)
                                            ->where('st_code', $d->st_code)
                                            ->get();
                                         


                                            if($data6[0]->officerlevel == 'DEO-OFFICE'){

                                                $created= $data6[0]->parent_id;

                                            }
                                            else{
                                                $created= $d->id;
                                            }
                                    $allauthid = explode(',', $data1->authority_type_id);
                                    //print_r($nodalid);die;
                                    if(count($allauthid) != 0)
                                    {
                                        foreach($allauthid as $allauthdata)
                                        {
//                                            dd($allauthdata);
                                            if($allauthdata != 'cand01')
                                            {
                                  if($nodaldetailsdeo !=''){

                                  if ($ac == '0' || $ac == 'NULL') {
                                    $nodaldetails1 = DB::table('authority_masters as a')
                                                    ->select('a.id', 'a.name')
                                                    ->where('st_code', $state)
                                                    ->where('a.dist_no', $district)
                                                    ->where('pc_no', $d->pc_no)
                                                    ->where('auth_type_id', $allauthdata)
                                                    ->get()->toArray();
                                    $nodaldetails2 = DB::table('authority_masters as a')
                                                    ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                    ->select('a.id', 'a.name')
                                                    ->where('a.st_code', $state)
                                                    ->where('a.dist_no', $district)
                                                    ->where('b.pc_no', $d->pc_no)
                                                    ->where('b.created_by',$created)
                                                    ->where('b.is_active', 1)
                                                    ->where('b.auth_type_id', $allauthdata)
                                                    ->groupBy('b.authority_masters_id')
                                                    ->get()->toArray();
                                } else {
                                    $nodaldetails1 = DB::table('authority_masters as a')
                                                    ->select('a.id', 'a.name')
                                                    ->where('st_code', $state)
                                                    ->where('a.dist_no', $district)
                                                    //->where('pc_no', $d->pc_no)
                                                    // ->whereIn('ac_no', $acall)
                                                    
                                                    ->where('auth_type_id', $allauthdata)
                                                    ->get()->toArray();
                                    $nodaldetails2 = DB::table('authority_masters as a')
                                                    ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                    ->select('a.id', 'a.name')
                                                      //->whereIn('b.ac_no', $acall)
                                                      ->where('a.dist_no', $district)
                                                     //->where('b.pc_no', $d->pc_no)
                                                      ->where('b.created_by',$created)
                                                    ->where('b.is_active', 1)
                                                    ->where('b.auth_type_id', $allauthdata)
                                                    ->groupBy('b.authority_masters_id')
                                                    ->get()->toArray();
                                } }
                                else{
                                    if ($ac == '0' || $ac == 'NULL') {
                                    $nodaldetails1 = DB::table('authority_masters as a')
                                                    ->select('a.id', 'a.name')
                                                    ->where('st_code', $state)
                                                    ->where('pc_no', $d->pc_no)
                                                    ->where('auth_type_id', $allauthdata)
                                                    ->get()->toArray();
                                    $nodaldetails2 = DB::table('authority_masters as a')
                                                    ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                    ->select('a.id', 'a.name')
                                                    ->where('a.st_code', $state)
                                                    ->where('b.pc_no', $d->pc_no)
                                                    ->where('b.is_active', 1)
                                                    ->where('b.created_by',$created)
                                                    ->where('b.auth_type_id', $allauthdata)
                                                    ->groupBy('b.authority_masters_id')
                                                    ->get()->toArray();
                                } else {
                                    $nodaldetails1 = DB::table('authority_masters as a')
                                                    ->select('a.id', 'a.name')
                                                    ->where('st_code', $state)
                                                    //->where('pc_no', $d->pc_no)
                                                     ->whereIn('ac_no', $acall)
                                                    ->where('auth_type_id', $allauthdata)
                                                    ->get()->toArray();
                                    $nodaldetails2 = DB::table('authority_masters as a')
                                                    ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                    ->select('a.id', 'a.name')
                                                      ->whereIn('b.ac_no', $acall)
                                                    // ->where('b.pc_no', $d->pc_no)
                                                    ->where('b.is_active', 1)
                                                   ->where('b.created_by',$created)
                                                    ->where('b.auth_type_id', $allauthdata)
                                                    ->groupBy('b.authority_masters_id')
                                                    ->get()->toArray();
                                }
                                }
                            //$nodaldetails = array_merge($nodaldetails1,$nodaldetails2);
                            $nodaldetails = $nodaldetails2;
//                                    if(!empty($ptypeid))
//                                    {
//                                        if ($ptypeid[1] != 3 && $ptypeid[1] != 6 && $ptypeid[1] != 8) {
                                            if (!empty($nodaldetails)) {
                                                for ($i = 0; $i < count($nodaldetails); $i++) {
                                                    $nodaldata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                    $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                                    if (!empty($getStateName) && !empty($getDistName)) {
                                                        $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();
                                                $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getDistName->DIST_NAME . ',' . $getStateName->ST_NAME;
                                            
//                                            if (!empty($fcm_id)) {
////                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
//                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
//                                            } 
                                                    }
                                                }
                                            }
                                            }
                                             else
                                            {
                                                $nodaldata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_data, 'authority_id' => 'cand01', 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                            }
                                        }
                                    }
                                        }
                                        $permission_doc_id = $req->permission_type;
                                $permission_doc_id = explode('#', $permission_doc_id);
                                $permission_doc_id = $permission_doc_id[0];
                                 $data1 = DB::table('permission_required_doc as a')
                                            ->select('a.authority_type_id')
                                            ->where('permission_id', $permission_doc_id)
                                            ->where('st_code', $d->st_code)
                                            ->get()->toArray();
                                            $data6 = DB::table('officer_login')
                                            ->select('parent_id', 'officerlevel')
                                            ->where('id', $d->id)
                                            ->where('st_code', $d->st_code)
                                            ->get();
                                         


                                            if($data6[0]->officerlevel == 'DEO-OFFICE'){

                                                $created= $data6[0]->parent_id;

                                            }
                                            else{
                                                $created= $d->id;
                                            }
                                 if(!empty($data1) && count($data1)!= 0)
                                 {
                              foreach($data1 as $doc_auth)
                              {
                            $allauthdata = explode(',', $doc_auth->authority_type_id);
                          if($nodaldetailsdeo !=''){
                           if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('a.dist_no', $district)
                                            ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            ->where('b.pc_no', $pc)
                                            ->where('a.dist_no', $district)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('a.dist_no', $district)
                                            // ->where('pc_no', $pc)
                                            // ->whereIn('ac_no', $acall)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                             // ->whereIn('b.ac_no', $acall)
                                              ->where('a.dist_no', $district)
                                             //->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } }
                        else
                        {
                             if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            ->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            // ->where('pc_no', $pc)
                                             ->whereIn('ac_no', $acall)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                              ->whereIn('b.ac_no', $acall)
                                             //->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        }
                        }
                            //$nodaldetails = array_merge($nodaldetails1,$nodaldetails2);
                            $nodaldetails = $nodaldetails2;
                                    if (!empty($nodaldetails)) {
                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                            $nodaldata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);if (!empty($getStateName) && !empty($getDistName)) {
                                            $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();    
                                            $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getDistName->DIST_NAME . ',' . $getStateName->ST_NAME;
                                            
//                                            if (!empty($fcm_id)) {
////                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
//                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
//                                            }
                                            }
                                        }
                                    }
                            }
                                 }
                                    }
                                    else
                            {
                                
                                $permission_doc_id = $req->permission_type;
                                $permission_doc_id = explode('#', $permission_doc_id);
                                $permission_doc_id = $permission_doc_id[0];
                                 $data1 = DB::table('permission_required_doc as a')
                                            ->select('a.authority_type_id')
                                            ->where('permission_id', $permission_doc_id)
                                            ->where('st_code', $d->st_code)
                                            ->get()->toArray();
                                            $data6 = DB::table('officer_login')
                                            ->select('parent_id', 'officerlevel')
                                            ->where('id', $d->id)
                                            ->where('st_code', $d->st_code)
                                            ->get();
                                         


                                            if($data6[0]->officerlevel == 'DEO-OFFICE'){

                                                $created= $data6[0]->parent_id;

                                            }
                                            else{
                                                $created= $d->id;
                                            }
                                 if(!empty($data1) && count($data1)!= 0)
                                 {
                              foreach($data1 as $doc_auth)
                              {
                            $allauthdata = explode(',', $doc_auth->authority_type_id);
                          if($nodaldetailsdeo !=''){
                          if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('pc_no', $pc)
                                            ->where('a.dist_no', $district)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            ->where('b.pc_no', $pc)
                                            ->where('a.dist_no', $district)
                                            ->where('b.created_by',$created)
                                            ->where('b.is_active', 1)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('a.dist_no', $district)
                                             //->where('pc_no', $pc)
                                            //  ->whereIn('ac_no', $acall)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                           //   ->whereIn('b.ac_no', $acall)
                                            //->where('b.pc_no', $pc)
                                            ->where('a.dist_no', $district)
                                            ->where('b.created_by',$created)
                                            ->where('b.is_active', 1)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } }
                        else
                        {
                            if ($ac == '0' || $ac == 'NULL') {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            ->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        } else {
                            $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                             //->where('pc_no', $pc)
                                              ->whereIn('ac_no', $acall)
                                            ->whereIn('auth_type_id', $allauthdata)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                              ->whereIn('b.ac_no', $acall)
                                           // ->where('b.pc_no', $pc)
                                            ->where('b.is_active', 1)
                                            ->where('b.created_by',$created)
                                            ->whereIn('b.auth_type_id', $allauthdata)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                        }
                        }
                            //$nodaldetails = array_merge($nodaldetails1,$nodaldetails2);
                            $nodaldetails = $nodaldetails2;
                                    if (!empty($nodaldetails)) {
                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                            $nodaldata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);if (!empty($getStateName) && !empty($getDistName)) {
                                            $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();
                                            $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getDistName->DIST_NAME . ',' . $getStateName->ST_NAME;
                                            
//                                            if (!empty($fcm_id)) {
////                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
//                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
//                                            } 
                                            }
                                        }
                                    }
                            }
                                 }
                            }
                                    DB::commit();
                                            if($user_mb!='') {
                        if($user_type == 2)
                                {
                                  $mob_message="Your permission request has been received with the DEO, to track the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                                }
                                else
                                {
                                    $mob_message="Your permission request has been received with the DEO, to track the status visit website- suvidha.eci.gov.in";
                                }
                        $response = SmsgatewayHelper::gupshup($user_mb,$mob_message);
                      }
//                      if($d->Phone_no!='') {
//                         $permsn_details = DB::table('permission_request as a')
//                                           ->join('permission_type as b','b.id','=','a.permission_type_id')
//                                           ->join('permission_master as c','c.id','=','b.permission_type_id')
//                                           ->where('a.id',$p_data)
//                                           ->select('a.reference_id','a.added_at','c.permission_name')
//                                           ->get()->first();
//                        $mob_message="A New Request has been received for ".$permsn_details->permission_name. "-".$permsn_details->reference_id." ".$permsn_details->added_at;
//                        $response = SmsgatewayHelper::gupshup($d->Phone_no,$mob_message);
//                      }
                                    return redirect()->back()->with('message', 'Successfully permission applied with Reference Id '.$ele_details[0]->ELECTION_ID.$ele_details[0]->CONST_TYPE.$p_data);
                                } else {
                                    return redirect()->back()->with('message', 'Permission not applied');
                                }
                            } else {
                                return redirect()->back()->with('message', 'Some Error Occured!!!');
                            }
                        } else {
                            return redirect()->back()->with('message', 'Some Error Occured!!');
                        }
                        }catch (Exception $e) {
                        DB::rollBack();
                        return $e;
                }
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            
//        $permission_type=DB::table('permission_type')->where('status','1')->get();
//        return view('admin.ro.Permission.ApplyOfflinePermission')->with(array('user_data'=>$d,'showpage'=>'permission','permission_type'=>$permission_type,'user_details_police'=>$user_details_police));
      
}

        catch (Exception $e){
                if(config('public_config.permission_log'))
           { $message=array();
            $message['eventTime']= date('Y-m-d H:i:s');
    $message['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
    $message['UserType']=$user->officername ?? '';
    $message['MobNo']= $user->Phone_no ?? '';
    $message['UserName']= $user->name ?? '';
    $message['applicationType']= 'Permission';                        
    $message['Module']= 'SUVIDHA';                        
    $message['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    $message['TransectionAction']= 'Data Submit';
              $message['LogDescription']= 'Something went to wrong '.$e->getMessage();
              $message['TransectionStatus']= 'Failed';
            LogNotification::LogInfo($message);
             }
            
         }


        } else {
            return redirect('/officer-login');
        }
    }

    public function AllPermissionRequest() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no,$d->id, $d->officerlevel);
            if($d->role_id == 5)
            {
                if($d->st_code == 'U01' || $d->st_code == 'U02' || $d->st_code == 'U03' || $d->st_code == 'U04' || $d->st_code == 'U05' || $d->st_code == 'U06' || $d->st_code == 'U07' || $d->st_code == 'S16' )
                {
                        $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no,$d->role_id);
                }
                else
                {
                    $permissionDetails = $this->PM->getintraPermissionDetails($d->st_code, $d->dist_no,$d->role_id);
                }
            }
            else
            {
                $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no,$d->role_id);
            }


           // dd($permissionDetails);
//            return view('admin.ac.deo.Permission.AllpermissionRequest', ['user_data' => $d], ['permissionDetails' => $permissionDetails],['ele_details' => $ele_details]);
              return view('admin.ac.deo.Permission.AllpermissionRequest')->with(array('user_data' => $d,'permissionDetails' => $permissionDetails,'ele_details' => $ele_details));
            
            } else {
            return redirect('/officer-login');
        }
    }

    public function getpermissiondetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $id = decrypt($req->id);
            $getallpermsndetails = DB::table('permission_request')->select('st_code', 'ac_no', 'assigned_police_st_id', 'approved_status', 'location_id', 'cancel_status')->where('id', $id)->get()->first();
            if (!empty($getallpermsndetails)) {
                if (!empty($getallpermsndetails->ac_no)) {
                    $allac = explode(',', $getallpermsndetails->ac_no);
                    $allac_name = DB::table('m_ac')->select('AC_NAME')->whereIn('AC_NO', $allac)->where('st_code', $getallpermsndetails->st_code)->get()->toArray();
                }
                if (!empty($getallpermsndetails->assigned_police_st_id)) {
                    $allps = explode(',', $getallpermsndetails->assigned_police_st_id);
                    $allps_name = DB::table('police_station_master')->select('police_st_name')->whereIn('id', $allps)->get()->toArray();
                }
                $prmsndetails = DB::table('permission_request')->select('ac_no', 'pc_no', 'dist_no')->where('id', $id)->first();
                $getDetailsview = $this->PM->getDetails($id, $getallpermsndetails->location_id);
                if (empty($getDetailsview) && !empty($prmsndetails->pc_no)) {
                    $getDetailsview = $this->PM->getIntraDetails($id, $getallpermsndetails->location_id);
                } else {
                    $getDetailsview = $this->PM->getIntradistDetails($id, $getallpermsndetails->location_id);
                }
                $getRodetails = $this->PM->getRodetails($id);
                $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no);
                $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no, $d->ac_no);
                $getNodaldetails = $this->PM->getNodaldetails($id);
                 $canddoc = $data=DB::table('permission_assigned_auth as a')
                        ->select('a.*')
                        ->where('a.permission_request_id',$id)
                        ->where('a.authority_id','cand01')
                        ->get()->toArray();

                        
                if (!empty($allps) && !empty($allac_name)) {
                    if ($getallpermsndetails->approved_status == 0 && $getallpermsndetails->cancel_status == 0) {
                        return view('admin.ac.deo.Permission.Permissiondetails')->with(array('user_data' => $d, 'allps_name' => $allps_name, 'allac_name' => $allac_name, 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'canddoc'=>$canddoc, 'getRodetails' => $getRodetails));
                    } else if ($getallpermsndetails->approved_status == 1 && $getallpermsndetails->cancel_status == 0) {
                        return view('admin.ac.deo.Permission.Permissiondetails')->with(array('user_data' => $d, 'allps_name' => $allps_name, 'allac_name' => $allac_name, 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'canddoc'=>$canddoc, 'getRodetails' => $getRodetails));
                    } else if ($getallpermsndetails->approved_status == 2 || $getallpermsndetails->cancel_status == 1 || $getallpermsndetails->cancel_status == 0) {

                        return view('admin.ac.deo.Permission.AcceptPermissiondetails')->with(array('user_data' => $d, 'allps_name' => $allps_name, 'allac_name' => $allac_name, 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'canddoc'=>$canddoc, 'getRodetails' => $getRodetails));
                    } else if ($getallpermsndetails->approved_status == 3 || $getallpermsndetails->cancel_status == 1 || $getallpermsndetails->cancel_status == 0) {
                        return view('admin.ac.deo.Permission.RejectPermissiondetails')->with(array('user_data' => $d, 'allps_name' => $allps_name, 'allac_name' => $allac_name, 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'canddoc'=>$canddoc, 'getRodetails' => $getRodetails));
                    }
                } else {
                    if ($getallpermsndetails->approved_status == 0 && $getallpermsndetails->cancel_status == 0) {
                        return view('admin.ac.deo.Permission.Permissiondetails')->with(array('user_data' => $d, 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'canddoc'=>$canddoc, 'getRodetails' => $getRodetails));
                    } else if ($getallpermsndetails->approved_status == 1 && $getallpermsndetails->cancel_status == 0) {
                        return view('admin.ac.deo.Permission.Permissiondetails')->with(array('user_data' => $d, 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'canddoc'=>$canddoc, 'getRodetails' => $getRodetails));
                    } else if ($getallpermsndetails->approved_status == 2 || $getallpermsndetails->cancel_status == 1 || $getallpermsndetails->cancel_status == 0) {

                        return view('admin.ac.deo.Permission.AcceptPermissiondetails')->with(array('user_data' => $d, 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'canddoc'=>$canddoc, 'getRodetails' => $getRodetails));
                    } else if ($getallpermsndetails->approved_status == 3 || $getallpermsndetails->cancel_status == 1 || $getallpermsndetails->cancel_status == 0) {
                        return view('admin.ac.deo.Permission.RejectPermissiondetails')->with(array('user_data' => $d, 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'canddoc'=>$canddoc, 'getRodetails' => $getRodetails));
                    }
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function UploadNodaldoc(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $time = Carbon::now()->timestamp;
            $p_id = strip_tags($req->p_req_id);
            $auth_id = strip_tags($req->auth_id);
//            if (!empty($_POST['savenodal'])) {
            $allCountAssignAuth = DB::table('permission_assigned_auth')
                            ->where('permission_request_id', $p_id)->count();
            $rules = [
                'nodal-document' => 'required|mimes:pdf',
            ];
            $messages = [
                'nodal-document.required' => 'This field is required.',
                'nodal-document.mimes' => 'Please upload only pdf documents.',
            ];
            $validator = Validator::make($req->all(), $rules, $messages);
            if ($validator->passes()) {
                // when file is selected for upload
                if ($req->hasFile('nodal-document')) {
                    $image = $req->file('nodal-document');
//                    $scanPhysicalDoc = $d->st_code . '_' . $time . '_' . $image->getClientOriginalName();
                    $scanPhysicalDoc = 'uploads1/Nodal-Uploaddocument/'.$d->election_id.'/'.$d->st_code.'/' . trim($p_id).'/'.preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                    $format = preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                    $destinationPath3 = public_path('/uploads1/Nodal-Uploaddocument/'.$d->election_id.'/'.$d->st_code.'/' . trim($p_id));
                    $image->move($destinationPath3, $format);
                    $data = array('file' => $scanPhysicalDoc, 'accept_status' => 1,'fileserver_dir'=>'uploads1');
                    $where = array('permission_request_id' => $p_id, 'authority_id' => $auth_id);
                    $res = $this->PM->updatetable('permission_assigned_auth', $where, $data);
                    if ($res == 1) {
                        $allCountApprove = DB::table('permission_assigned_auth')
                                ->where('permission_request_id', $p_id)
                                ->where('accept_status', '1')
                                ->count();
                        if ($allCountAssignAuth == $allCountApprove) {
                            $data = array('approved_status' => 1, 'updated_by' => $d->id);
                            $where = array('id' => $p_id);
                            $res1 = $this->PM->updatetable('permission_request', $where, $data);
                            if ($res1 == 1) {
                                return redirect()->back()->with('message', 'Successfully Uploaded');
                            } else {
                                return redirect()->back()->with('message', 'Some Error Occured!');
                            }
                        }
                        return redirect()->back()->with('message', 'Successfully Uploaded');
                    }else {
                        return redirect()->back()->with('message', 'Some Error Occured!');
                    }
                }
            } else {
                return redirect()->back()->withErrors($validator, 'error')->withInput();
//                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function UpdateAction(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $p_id = strip_tags($req->p_id);
            $time = Carbon::now()->timestamp;
            $user_mb = DB::table('permission_request as a')
                                           ->join('user_login as b','b.id','=','a.user_id')
                                           ->where('a.id',$p_id)
                                           ->select('b.mobile')
                                           ->get()->first();
//            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no);
            $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no, $d->ac_no,$d->role_id);
            if (!empty($req->accept)) {
                $rules = [
                    'comment' => 'required',
                    'rofile' => 'required|mimes:pdf'
                ];
                $messages = [
                    'comment.required' => 'Comment field is required.',
                    'rofile.required' => 'Document is Required',
                    'rofile.mimes' => 'Please upload only pdf document'
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
//                    if ($req->ro_status == 1) {
                    $scanPhysicalDoc = 'NULL';
                    if ($req->hasFile('rofile')) {
//                            echo $req->hasFile('rofile');die;
                        $image = $req->file('rofile');
//                        $scanPhysicalDoc = $d->st_code . '_' . $time . '_' . $image->getClientOriginalName();
                        $scanPhysicalDoc = 'uploads1/RO-Uploaddocument/'.$d->election_id.'/'.$d->st_code.'/'. trim($p_id).'/'.preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                        $format = preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                        $destinationPath3 = public_path('/uploads1/RO-Uploaddocument/'.$d->election_id.'/'.$d->st_code.'/' . trim($p_id));
                        $image->move($destinationPath3, $format);
                    }
                    $insertdata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_id, 'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'user_created_by' => '2', 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                    $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                    if ($res == 1) {
                        $data = array('approved_status' => '2', 'updated_by' => $d->id);
                        $where = array('id' => $p_id);
                        $update = $this->PM->updatetable('permission_request', $where, $data);
                         if($user_mb->mobile!='') {
                        $mob_message="Your permission has been processed, to check the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                        $response = SmsgatewayHelper::gupshup($user_mb->mobile,$mob_message);
                      }
                        return redirect('/acdeo/allPermissionRequest')->with('message', 'Successfully Accepted!');
                    }
//                    } else {
//                        return redirect()->back()->with('error', 'Not Accepted by Nodals');
//                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            } else if (!empty($req->reject)) {

                $rules = [
                    'comment' => 'required',
//                    'rofile' => 'required|mimes:pdf'
                ];
                $messages = [
                    'comment.required' => 'Comment field is required.',
//                    'rofile.required' => 'Document is Required',
//                    'rofile.mimes' => 'Please upload only pdf document'
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $scanPhysicalDoc = 'NULL';
                    if ($req->hasFile('rofile')) {
                        $image = $req->file('rofile');
//                        $scanPhysicalDoc = $image->getClientOriginalName();
                        $scanPhysicalDoc = 'uploads1/RO-Uploaddocument/'.$d->election_id.'/'.$d->st_code.'/'. trim($p_id).'/'.preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                        $format = preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                        $destinationPath3 = public_path('/uploads1/RO-Uploaddocument/'.$d->election_id.'/'.$d->st_code.'/' . trim($p_id));
                        $image->move($destinationPath3, $format);
                    }
                    $insertdata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_id, 'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                    $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                    if ($res == 1) {
                        $data = array('approved_status' => '3', 'updated_by' => $d->id);
                        $where = array('id' => $p_id);
                        $update = $this->PM->updatetable('permission_request', $where, $data);
                         if($user_mb->mobile!='') {
                        $mob_message="Your permission has been processed, to check the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                        $response = SmsgatewayHelper::gupshup($user_mb->mobile,$mob_message);
                      }
                        return redirect('/acdeo/allPermissionRequest')->with('message', 'Successfully Rejected!');
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            }
            else if (!empty($req->cancel)) {

                $rules = [
                    'comment' => 'required',
//                    'rofile' => 'required|mimes:pdf'
                ];
                $messages = [
                    'comment.required' => 'Comment field is required.',
//                    'rofile.required' => 'Document is Required',
//                    'rofile.mimes' => 'Please upload only pdf document'
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $scanPhysicalDoc = 'NULL';
                    if ($req->hasFile('rofile')) {
                        $image = $req->file('rofile');
//                        $scanPhysicalDoc = $image->getClientOriginalName();
                        $scanPhysicalDoc = 'uploads1/RO-Uploaddocument/'.$d->election_id.'/'.$d->st_code.'/'. trim($p_id).'/'.preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                        $format = preg_replace('/[^a-zA-Z0-9\.]/i','_',$d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                        $destinationPath3 = public_path('/uploads1/RO-Uploaddocument/'.$d->election_id.'/'.$d->st_code.'/' . trim($p_id));
                        $image->move($destinationPath3, $format);
                    }
                    $insertdata = array('fileserver_dir'=>'uploads1','permission_request_id' => $p_id,'ro_cancel_status'=>1 ,'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                    $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                    if ($res == 1) {
                        $data = array('cancel_status' => '1', 'updated_by' => $d->id);
                        $where = array('id' => $p_id);
                        $update = $this->PM->updatetable('permission_request', $where, $data);
                         if($user_mb->mobile!='') {
                        $mob_message="Your permission has been processed, to check the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                        $response = SmsgatewayHelper::gupshup($user_mb->mobile,$mob_message);
                      }
                        return redirect('/acdeo/allPermissionRequest')->with('message', 'Successfully Cancelled!');
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            }
            else {
                echo 'download';
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddPS() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
//            $getAllAC=$this->PM->getAllAC($d->dist_no);
            return view('admin.ac.deo.Permission.AddPoliceStation')->with(array('user_data' => $d,'getAllPC'=>$getAllPC));
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function getAllAC(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $pcno=$req->pc_id;
            $getAllAC=$this->PM->getAllAC($pcno,$d->st_code,$d->dist_no);
            return $getAllAC;
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddPSData(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
//            echo '<pre/>';
//            print_r($user);
//            print_r($d);die;
//            $uid=$user->id;
            if (!empty($_POST['AddPS'])) {
                $rules = [
                    'ps_name' => 'required|regex:/(^[ A-Za-z0-9]+$)/',
                    'ps_addr' => 'required|not_regex:/([<>@$%?]+)/',
                    'ps_imb' => 'required|numeric|digits:10',
                    'ps_smb' => 'required|numeric|digits:10',
                    'acno' => 'required|not_in:0',
                    'pc' => 'required|not_in:0',
                    'uname' => 'required|regex:/(^[ A-Za-z]+$)/',
                ];
                $messages = [
                    'ps_name.required' => 'Name field is required.',
                    'ps_name.regex' => 'Please Enter only alphanumeric character.',
                    'ps_addr.required' => 'Address field is required.',
                    'ps_addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'ps_imb.required' => 'PS Incharge no is required.',
                    'ps_imb.digits' => 'Please Enter valid Mobile Number.',
                    'ps_smb.required' => 'Police Staion Mobile No is required.',
                    'ps_smb.digits' => 'Please Enter valid Mobile Number.',
                    'acno.required'=>'Please select Ac',
                    'acno.not_in' =>'Please select AC',
                    'pc.required' =>'Please select PC',
                    'pc.not_in' =>'Please select PC',
                    'uname.required' => 'Name field is required.',
                    'uname.regex' => 'Please Enter only alphanumeric character.',
                ];
               
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $ps_smb = "NULL";
                    if (!empty($_POST['ps_name']) && !empty($_POST['ps_addr']) && !empty($_POST['ps_imb'])) {
                        $ps_name = strip_tags($_POST['ps_name']);
                        $ps_addr = strip_tags($_POST['ps_addr']);
                        $ps_imb = strip_tags($_POST['ps_imb']);
                        $uname = strip_tags($_POST['uname']);
                        if(!empty($_POST['acno']))
                        {
                            $acno=$_POST['acno'];
                        }
                        else
                        {
                            $acno = 0;
                        }
                        if (!empty($_POST['ps_smb'])) {
                            $ps_smb = strip_tags($_POST['ps_smb']);
                        }
                        $where = array('st_code' => $d->st_code, 'ac_no' =>$acno, 'police_st_name' => $ps_name, 'police_station_address' => $ps_addr, 'incharge_name' => $uname);
                        $checkps = DB::table('police_station_master')->where($where)->count();
                        if ($checkps == 0) {
                            $data = array('st_code' => $d->st_code, 'ac_no' =>$acno, 'incharge_name' => $uname, 'police_st_name' => $ps_name, 'police_st_incharge_no' => $ps_imb, 'police_station_no' => $ps_smb, 'police_station_address' => $ps_addr, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                            $result = $this->PM->insertdata('police_station_master', $data);
                            if ($result == 1) {
                                //                        return redirect()->back()->with('message', 'Successfully Added!');
                                return redirect('/acdeo/viewps')->with('message', 'Successfully Added!');
                            } else {
                                return redirect()->back()->with('message', 'Some Error Occured');
                            }
                        } else {
                            return redirect()->back()->with('chckmessage', 'Entered Police Station name is already Exist!')->withInput();
                        }
                    }
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewPS(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
            return view('admin.ac.deo.Permission.ViewPoliceStaion')->with(array('user_data' => $d, 'getAllPC' => $getAllPC));
        } else {
            return redirect('/officer-login');
        }
    }
    public function getallACPS(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            
            $acno=$req->acid;
            $where = array('st_code' => $d->st_code, 'ac_no' => $acno);
            $getAllPSData = $this->PM->getAllPSData($where);
            return $getAllPSData;
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditPS(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);

            if (!empty($_POST['UpdatePS'])) {
                $rules = [
                    'ps_name' => 'required|regex:/(^[ A-Za-z0-9]+$)/',
                    'ps_addr' => 'required|not_regex:/([<>@$%?]+)/',
                    'ps_imb' => 'required|numeric|digits:10',
                    'ps_smb' => 'required|numeric|digits:10',
                    'uname' => 'required|regex:/(^[ A-Za-z]+$)/',
                ];
                $messages = [
                    'ps_name.required' => 'Name field is required.',
                    'ps_name.regex' => 'Please Enter only alphanumeric character.',
                    'ps_addr.required' => 'Address field is required.',
                    'ps_addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'ps_imb.required' => 'PS Incharge no is required.',
                    'ps_imb.digits' => 'Please Enter valid Mobile Number.',
                    'ps_smb.required' => 'Police Staion Mobile No is required.',
                    'ps_smb.digits' => 'Please Enter valid Mobile Number.',
                    'uname.required' => 'Name field is required.',
                    'uname.regex' => 'Please Enter only alphanumeric character.',
                ];

                $validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->passes()) {
                    $ps_smb = "NULL";
                    if (!empty($_POST['ps_name']) && !empty($_POST['ps_addr']) && !empty($_POST['ps_imb'])) {
                        $ps_name = strip_tags($_POST['ps_name']);
                        $ps_addr = strip_tags($_POST['ps_addr']);
                        $ps_imb = strip_tags($_POST['ps_imb']);
                        $uname = strip_tags($_POST['uname']);
                        if (!empty($_POST['ps_smb'])) {
                            $ps_smb = strip_tags($_POST['ps_smb']);
                        }
                        $where = array('st_code' => $d->st_code, 'ac_no' => $d->ac_no, 'police_st_name' => $ps_name, 'police_station_address' => $ps_addr, 'incharge_name' => $uname);
                        $checkps = DB::table('police_station_master')->where($where)->count();
                        if ($checkps == 0) {
                            $data = array('incharge_name' => $uname, 'police_st_name' => $ps_name, 'police_st_incharge_no' => $ps_imb, 'police_station_no' => $ps_smb, 'police_station_address' => $ps_addr, 'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where = array('id' => $_POST['psid']);
                            $result = $this->PM->updatetable('police_station_master', $where, $data);
//                        if ($result == 1) {
                            return redirect()->back()->with('message', 'Successfully Updated!');
                        } else {
                            return redirect()->back()->with('chckmessage', 'Entered Police Station name is already Exist!')->withInput();
                        }

//                        } else {
//                            return redirect()->back()->with('message', 'Some Error Occured');
//                        }
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error');
                }
            } else {
                $p_id = $request->id;
                $getpsdetails = $this->PM->getpsdetails($p_id);
                return view('admin.ac.deo.Permission.EditPoliceStation')->with(array('user_data' => $d, 'showpage' => 'permission', 'getpsdetails' => $getpsdetails));
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    
 public function AddAuthority() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
           // aa$ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->dist_no, $d->id, $d->officerlevel);
            $authority = $this->PM->getAuthority($d->st_code);
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
//            print_r($d);die;
           //aa return view('admin.ac.deo.Permission.AddAuthority')->with(array('user_data' => $d, 'getAllPC' => $getAllPC, 'authority' => $authority));
            return view('admin.ac.deo.Permission.AddAuthority')->with(array('user_data' => $d, 'authority' => $authority));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddAuthorityData(Request $request) {
       // dd($request-all());
        if (Auth::check()) {
           // DB::connection()->enableQueryLog();
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
             //aa$ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $ele_details = $this->commonModel->election_detailsac($d->st_code,$d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
//            echo '<pre/>';
             //dd($ele_details[0]->CONST_NO);
            // print_r($request->all());die;
            $rules = [  
                'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                'email' => 'required|email',
                'addr' => 'required|not_regex:/([<>@$%?]+)/',
                'mb' => 'required|numeric|digits:10',
//                'eno' => 'required|numeric|digits:16',
                'authid' => 'required|not_in:0',
                /*'acno' => 'required|not_in:0',
                'pc' => 'required|not_in:0'*/
            ];
            $messages = [
                'name.required' => ' Name field is required.',
                'name.regex' => 'Please Enter only alphanumeric character.',
                'addr.required' => ' Address field is required.',
                'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                'mb.required' => ' Mobile no is required.',
                'mb.digits' => 'Please Enter valid Mobile Number.',
                'dept.required' => 'Departemnt is required',
                'dept.regex' => 'Please Enter only alphanumeric character.',
                'desig.required' => 'Designation is required Field',
                'desig.regex' => 'Please Enter only alphanumeric character.',
                'email.required' => 'Email is required',
//                'eno.required' => 'Epic No is required',
//                'eno.digits' => 'Epic number must be of 16 digits',
                'authid.required' => 'Select Approving Authority',
                /*'acno.required'=>'Please select Ac',
                'acno.not_in' =>'Please select AC',*/
                /*'pc.required' =>'Please select PC',
                'pc.not_in' =>'Please select PC',*/
            ];
          // dd($ele_details);
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->passes()) {
                if (!empty($request->authid)) {
                    $authid = strip_tags($request->authid);
                }
                if (!empty($request->name)) {
                    $name = strip_tags($request->name);
                }
                if (!empty($request->dept)) {
                    $dept = strip_tags($request->dept);
                }
                if (!empty($request->desig)) {
                    $desig = strip_tags($request->desig);
                }
                if (!empty($request->mb)) {
                    $mb = strip_tags($request->mb);
                }
                if (!empty($request->email)) {
                    $email = strip_tags($request->email);
                }
                if (!empty($request->addr)) {
                    $addr = strip_tags($request->addr);
                }
                 if (!empty($request->CONST_NO)) {
                    $pc = strip_tags($request->CONST_NO);
                }
                 else  {
                    $pc = '0';
                }
                if (!empty($request->acno)) {
                    $ac = strip_tags($request->acno);
                } 
                else{
                    $ac = $ele_details[0]->CONST_NO;
                }
                $checkexisttype = DB::table('authority_masters_mapping')->where(array('auth_type_id' => $authid, 'created_by' => $d->id, 'is_active' => 1))->count();
                if ($checkexisttype == 0) {

                 $checkAuthmb = DB::table('authority_masters')->where('mobile', $mb)->count();
                if ($checkAuthmb == 0) {
                    
                     //$data = array('st_code' => $d->st_code, 'name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                      $data = array('st_code' => $d->st_code, 'auth_type_id'=> $authid, 'dist_no' => $d->dist_no, 'ac_no' =>'', 'pc_no' => '', 'auth_type_id' => $authid, 'name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                    
                    $result = DB::table('authority_masters')->insertGetId($data);
                     //$mapdata = array('authority_masters_id' => $result, 'dist_no' => $d->dist_no, 'auth_type_id' => $authid, 'created_by' => $d->id,);
                    $mapdata = array('authority_masters_id' => $result, 'dist_no' => $d->dist_no, 'ac_no' =>$ac, 'pc_no' => $pc, 'auth_type_id' => $authid, 'created_by' => $d->id,);
                     $mapresult = $this->PM->insertdata('authority_masters_mapping', $mapdata);
                    if (!empty($result) && !empty($mapresult)) {
                        return redirect('/acdeo/viewauthority')->with('message', 'Successfully Added');
                    } else {
                        return redirect()->back()->with('message', 'Some error occured');
                    }
                } else {

                   
                       

                    $chckexistuser = DB::table('authority_masters_mapping as a')
                            ->join('authority_masters as b','b.id','=','a.authority_masters_id')
                            //aa->where(array('a.dist_no' => $d->dist_no, 'a.ac_no' => $ac, 'a.pc_no' => $pc,'b.mobile'=>$mb))->count();
                            ->where(array('a.dist_no' => $d->dist_no,'a.ac_no' => $ac,'b.mobile'=>$mb,'b.created_by' => $d->id))->count();
//                  $chckexistuser1 = DB::table('authority_masters')->where(array('dist_no' => $d->dist_no, 'ac_no' => $ac, 'pc_no' => $pc, 'auth_type_id' => $authid,'mobile'=>$mb))->count();
//                  echo $chckexistuser.'#'. $chckexistuser1;die;
//                    print_r(array('dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'auth_type_id' => $authid));die;
                  $checkAuthmbst = DB::table('authority_masters')->where('mobile', $mb)->where('st_code', $d->st_code)->count();
                      
                   if ($chckexistuser == 0) {
                    if ($checkAuthmbst != 0) {
                        $getauthid = DB::table('authority_masters')->select('id')->where('mobile', $mb)->first();
                        if (!empty($getauthid)) {
                           //aa $mapdata = array('authority_masters_id' => $getauthid->id, 'dist_no' => $d->dist_no, 'ac_no' => $ac, 'pc_no' => $pc, 'auth_type_id' => $authid, 'created_by' => $d->id,);
                            $mapdata = array('authority_masters_id' => $getauthid->id, 'dist_no' => $d->dist_no, 'ac_no' => $ac, 'auth_type_id' => $authid, 'created_by' => $d->id,);
                            $mapresult = $this->PM->insertdata('authority_masters_mapping', $mapdata);
                            if (!empty($mapresult)) {
                                return redirect('/acdeo/viewauthority')->with('message', 'Successfully Added');
                            } else {
                                return redirect()->back()->with('message', 'Some error occured');
                            }
                        }
                    }
                    else {
                        return redirect()->back()->with('chckmessage', 'Entered Mobile No is already Exist! in Other State');
                    }

                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered Authority is already Exist!')->withInput();
                    }
                }
            } else {
                return redirect()->back()->with('chckmessage', 'Entered Authority type is already Exist!')->withInput();
            }
            } else {
                return redirect()->back()->withErrors($validator, 'error')->withInput();
            }
        } else {
            return redirect('/officer-login');
        }
    }

public function ViewAuthority(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
             DB::connection()->enableQueryLog();
            $d = $this->commonModel->getunewserbyuserid($user->id);
             
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->dist_no, $d->id, $d->officerlevel);
           
            $getAllACAuthorityData1 = $this->PM->getAllACAuthorityData1($d->id,$d->st_code, $d->dist_no);

             
             return view('admin.ac.deo.Permission.ViewAuthority')->with(array('user_data' => $d, 'getAllACAuthorityData1' =>$getAllACAuthorityData1));
           
        } else {
            return redirect('/officer-login');
        }
    }
     
    public function EditAuthority(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            if (!empty($_POST['submit'])) {
                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'email' => 'required|email',
                    'addr' => 'required|not_regex:/([<>@$%?]+)/',
                    'mb' => 'required|numeric|digits:10',
//                    'eno' => 'required|numeric|digits:16',
                    'authid' => 'required|not_in:0'
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphanumeric character.',
                    'addr.required' => ' Address field is required.',
                    'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'mb.required' => ' Mobile no is required.',
                    'mb.digits' => 'Please Enter valid Mobile Number.',
                    'dept.required' => 'Departemnt is required',
                    'dept.regex' => 'Please Enter only alphanumeric character.',
                    'desig.required' => 'Designation is required Field',
                    'desig.regex' => 'Please Enter only alphanumeric character.',
                    'email.required' => 'Email is required',
//                    'eno.required' => 'Epic No is required',
//                    'eno.digits' => 'Epic number must be of 16 digits',
                    'authid.required' => 'Select Approving Authority'
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $authid=null;
                    $name=null;
                    $dept=null;
                    $desig=null;
                    $mb=null;
                    $email=null;
                    $addr=null;
                    $eno=null;
                    if (!empty($req->authid)) {
                        $authid = strip_tags($req->authid);
                    }
                    if (!empty($req->name)) {
                        $name = strip_tags($req->name);
                    }
                    if (!empty($req->dept)) {
                        $dept = strip_tags($req->dept);
                    }
                    if (!empty($req->desig)) {
                        $desig = strip_tags($req->desig);
                    }
                    if (!empty($req->mb)) {
                        $mb = strip_tags($req->mb);
                    }
                    if (!empty($req->email)) {
                        $email = strip_tags($req->email);
                    }
                    if (!empty($req->addr)) {
                        $addr = strip_tags($req->addr);
                    }
                    if (!empty($req->eno)) {
                        $eno = strip_tags($req->eno);
                    }
                    $getallnodaldetails = DB::table('authority_masters')->where('mobile',$mb)->count();
                    if ($getallnodaldetails == 0) {
                            //$data = array('name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            //aa$data = array('ac_no'=>$req->ac,'pc_no'=>$req->pc,'dist_no'=>$req->dist,'auth_type_id'=>$req->authid, 'name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $data = array('ac_no'=>$req->ac,'dist_no'=>$req->dist,'auth_type_id'=>$req->authid, 'name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $cond = array('id' => $_POST['nodal_id']);
                            $result = $this->PM->updatetable('authority_masters', $cond, $data);
                            return redirect()->back()->with('message', 'Successfully Updated');
                        
                    } else {
                            //$data = array('name' => $name, 'department' => $dept, 'designation' => $desig,'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            //aa$data = array('ac_no'=>$req->ac,'pc_no'=>$req->pc,'dist_no'=>$req->dist,'auth_type_id'=>$req->authid, 'name' => $name, 'department' => $dept, 'designation' => $desig,'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                              $data = array('ac_no'=>$req->ac,'dist_no'=>$req->dist,'auth_type_id'=>$req->authid, 'name' => $name, 'department' => $dept, 'designation' => $desig,'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $cond = array('id' => $_POST['nodal_id']);
                            $result = $this->PM->updatetable('authority_masters', $cond, $data);
                            //update mapping table
                            $getnodalid = DB::table('authority_masters')->where('mobile',$mb)->select('id')->first();
                            $getnodalmapid=DB::table('authority_masters_mapping')->where(array('ac_no'=>$req->ac,'pc_no'=>$req->pc,'dist_no'=>$req->dist,'auth_type_id'=>$req->authid,'is_active'=>1,'created_by'=>$d->id))->select('authority_masters_id','id')->first();
                            //aa$getnodalmapid=DB::table('authority_masters_mapping')->where(array('ac_no'=>$req->ac,'pc_no'=>$req->pc,'dist_no'=>$req->dist,'auth_type_id'=>$req->authid,'is_active'=>1,'created_by'=>$d->id))->select('authority_masters_id','id')->first();
                            if($getnodalid->id != $getnodalmapid->authority_masters_id)
                            {
                            $data1 = array('authority_masters_id' =>$getnodalid->id);
                            $cond1 = array('id'=>$getnodalmapid->id);
                            $result1 = $this->PM->updatetable('authority_masters_mapping', $cond1, $data1);
                            }
                            return redirect('/acdeo/viewauthority')->with('message', 'All details will be Updated if Mobile Number is Different,If Mobile Number already exist then except Mobile number all details will be Updated.');
//                            return redirect()->back()->with('message', 'All details will be Updated if Mobile Number is Different,If Mobile Number already exist then except Mobile number all details will be Updated.');
                           // redirect()->back()->with('message', 'Except Autority Type All data is successfully Updated beacuse Entered Authority is already Exist!')->withInput();
                        }
                } else {
                    return redirect()->back()->withErrors($validator, 'error');
                }
            } else {
                 $data = explode('&', $req->id);
                 $nodal_id = Crypt::decryptString($data[0]);
                 $nodal_auth = Crypt::decryptString($data[1]);
                /*$pc=$data[2];
                $ac=$data[3];*/
                $authority = $this->PM->getAuthority($d->st_code);
                //aa$getAuthorityDetails = $this->PM->getAuthorityDetails($nodal_id,$nodal_auth,$d->dist_no,$pc,$ac,$d->id);
                $getAuthorityDetails = $this->PM->getAuthorityDetails($nodal_id,$nodal_auth,$d->dist_no,$d->id);
                //aaif (!empty($getAuthorityDetails[0]->ac_no) || !empty($getAuthorityDetails[0]->pc_no) || !empty($getAuthorityDetails[0]->dist_no) || !empty($getAuthorityDetails[0]->auth_type_id))
                 if (!empty($getAuthorityDetails[0]->dist_no) || !empty($getAuthorityDetails[0]->auth_type_id)) {
                    if ($getAuthorityDetails[0]->ac_no == $d->ac_no) {
                        $authtype = DB::table('authority_masters as a')->select('a.auth_type_id', 'b.name as auth_type_name')
                                        ->join('authority_type as b', 'a.auth_type_id', '=', 'b.id')
                                        ->where('a.id', $nodal_id)->get()->first();
                        if($authtype->auth_type_id != $nodal_auth)
                        {
                            $authtype = DB::table('authority_masters_mapping as a')->select('a.auth_type_id', 'b.name as auth_type_name')
                                        ->join('authority_type as b', 'a.auth_type_id', '=', 'b.id')
                                        ->where('a.authority_masters_id', $nodal_id)->where('auth_type_id', $nodal_auth)->get()->first();
                        }
                    } else {
                        $authtype = DB::table('authority_masters_mapping as a')->select('a.auth_type_id', 'b.name as auth_type_name')
                                        ->join('authority_type as b', 'a.auth_type_id', '=', 'b.id')
                                        ->where('a.authority_masters_id', $nodal_id)->where('auth_type_id', $nodal_auth)->get()->first();
                    }
                } else {
                    $authtype = DB::table('authority_masters_mapping as a')->select('a.auth_type_id', 'b.name as auth_type_name')
                                    ->join('authority_type as b', 'a.auth_type_id', '=', 'b.id')
                                    ->where('authority_masters_id', $nodal_id)->where('auth_type_id', $nodal_auth)->get()->first();
                }
                return view('admin.ac.deo.Permission.EditAuthority')->with(array('user_data' => $d,'authtype' =>$authtype, 'getAuthorityDetails' => $getAuthorityDetails, 'authority' => $authority));
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function EditAuthorityStatus(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $data = explode('#', $req->status);
            $status = $data[0];

            $id = $data[1];
            if ($status == 1) {
//                echo 'ok';die;
                $where = array('authority_masters_id' => $id, 'created_by' => $d->id);
                $findauth = DB::table('authority_masters_mapping')->where($where)->select('*')->get()->toArray();
                if (!empty($findauth)) {
                    if(count($findauth) == 1) {
                        $cond = array('is_active' => '0');
                        $res = $this->PM->updatetable('authority_masters_mapping', $where, $cond);
                        if ($res == 1) {
                            return 1;
                        } else {
                            return 0;
                        }
                    }
                }
                else {
                    $getdata=DB::table('authority_masters')->select('*')->where('id',$id)->get()->toArray();
                    if(!empty($getdata))
                    {
                       $authdata=array('authority_masters_id'=>$id,'dist_no'=>$getdata[0]->dist_no,'ac_no'=>$getdata[0]->ac_no,'pc_no'=>$getdata[0]->pc_no,'auth_type_id'=>$getdata[0]->auth_type_id,'created_by'=>$d->id);
                       $insetdata=$this->PM->insertdata('authority_masters_mapping',$authdata);
                       
                       if($insetdata == 1)
                       {
                           $authmasterdata=array('dist_no'=>'NULL','ac_no'=>'NULL','pc_no'=>'NULL','auth_type_id'=>'NULL');
                       $idcond=array('id'=>$id);
                       $updateauthmaster=$this->PM->updatetable('authority_masters',$idcond,$authmasterdata);
                           $cond = array('is_active' => '0');
                        $res = $this->PM->updatetable('authority_masters_mapping', $where, $cond);
                        if ($res == 1) {
                            return 1;
                        } else {
                            return 0;
                        }
                       }
                    }
                    }
            } else {
//                echo 'okK';die;
                $where = array('authority_masters_id' => $id, 'created_by' => $d->id);
                $findauth = DB::table('authority_masters_mapping')->where($where)->select('*')->get()->toArray();
                if(!empty($findauth)) {
                    if (count($findauth) == 1) {
                        $cond = array('is_active' => '1');
                        $res = $this->PM->updatetable('authority_masters_mapping', $where, $cond);
                        if ($res == 1) {
                            return 1;
                        } else {
                            return 0;
                        }
                    }
                }
                else {
                       $getdata=DB::table('authority_masters')->select('*')->where('id',$id)->get()->toArray();
                    if(!empty($getdata))
                    {
                       $authdata=array('authority_masters_id'=>$id,'dist_no'=>$getdata[0]->dist_no,'ac_no'=>$getdata[0]->ac_no,'pc_no'=>$getdata[0]->pc_no,'auth_type_id'=>$getdata[0]->auth_type_id,'created_by'=>$d->id);
                       $insetdata=$this->PM->insertdata('authority_masters_mapping',$authdata);
                       
                       if($insetdata == 1)
                       {
                           $authmasterdata=array('dist_no'=>'NULL','ac_no'=>'NULL','pc_no'=>'NULL','auth_type_id'=>'NULL');
                       $idcond=array('id'=>$id);
                       $updateauthmaster=$this->PM->updatetable('authority_masters',$idcond,$authmasterdata);
                           $cond = array('is_active' => '0');
                        $res = $this->PM->updatetable('authority_masters_mapping', $where, $cond);
                        if ($res == 1) {
                            return 1;
                        } else {
                            return 0;
                        }
                       }
                    }
                    }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    //location Master
    public function AddLocation() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
            return view('admin.ac.deo.Permission.AddLocation')->with(array('user_data' => $d, 'getAllPC' => $getAllPC));
        } else {
            return redirect('/officer-login');
        }
    }
    public function AddLocationinsert(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            if (!empty($_POST['submit'])) {
                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'addr' => 'required|not_regex:/([<>@$%?]+)/',
                    'acno' => 'required|not_in:0',
                    'pc' => 'required|not_in:0'
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphanumeric character.',
                    'addr.required' => ' Address field is required.',
                    'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'acno.required'=>'Please select Ac',
                    'acno.not_in' =>'Please select AC',
                    'pc.required' =>'Please select PC',
                    'pc.not_in' =>'Please select PC'
                ];
                $validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->passes()) {
                    $location_name = strip_tags($request['name']);
                    $address = strip_tags($request['addr']);
                    if (!empty($request->pc)) {
                    $pc = strip_tags($request->pc);
                    }
                    if (!empty($request->acno)) {
                        $ac = strip_tags($request->acno);
                    }
                    /*$array = array();

                       $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($request['addr']).'&key=AIzaSyDfT2Iqt4yvPmQSGRJVApQUHdbv5XR_R-8&callback=initMap');

                       $geo = json_decode($geo,true);

        

                      if ($geo['status'] == 'OK')

                      {

                      $latitude = $geo['results'][0]['geometry']['location']['lat'];

                      $longitude = $geo['results'][0]['geometry']['location']['lng'];

                      $arrayvalue = array('lat'=> $latitude ,'lng'=>$longitude);

                      $latitude_loc = $arrayvalue['lat'];

                      $longitude_loc =  $arrayvalue['lng'];

                      

                      }

                      else

                      {

                      return redirect()->back()->with('chckmessage', 'Enter Correct Address');

                       } */
                    $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'location_name' => $location_name, 'location_details' => $address);
                    $chckloc = DB::table('location_master')->where($where)->count();
                    if ($chckloc == 0) {
                        $data = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $ac, 'pc_no' => $pc,'latitude' =>'00.0000','longitude'=> '00.0000', 'created_by' => $d->id, 'location_name' => $location_name, 'location_details' => $address, 'status' => 1, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                        DB::table('location_master')->insert($data);
                        return redirect('/acdeo/viewaddlocation')->with('message', 'Successfully Added');
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered Location name and address is already Exist!')->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function viewaddlocation() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
            $getAllPermsDatas = $this->PM->getlocationmaster($d->st_code, $d->ac_no);
            //print_r($getAllPermsDatas);
            //exit;
            return view('admin.ac.deo.Permission.viewaddlocation')->with(array('user_data' => $d, 'getAllPC' => $getAllPC, 'getAllPermsDatas' => $getAllPermsDatas));
        } else {
            return redirect('/officer-login');
        }
    }
    public function getallACloc(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            
            $acno=$req->acid;
           
            $getAlllocDatas = $this->PM->getlocationmaster($d->st_code,$acno);
            return $getAlllocDatas;
        } else {
            return redirect('/officer-login');
        }
    }

    public function locationeditpermsn(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);

            $location_editid = $request->id;
            //print_r($d);exit;
            $getAllPermsDatas = $this->PM->getlocationeditmaster($location_editid);
            return view('admin.ac.deo.Permission.Editaddlocation')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAllPermsDatas' => $getAllPermsDatas));
        } else {
            return redirect('/officer-login');
        }
    }

    public function updateLocationval(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            $users = Session::get('admin_login_details');
            $user = Auth::user();
            if (!empty($_POST['submit'])) {
                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'addr' => 'required|not_regex:/([<>@$%?]+)/',
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphanumeric character.',
                    'addr.required' => ' Address field is required.',
                    'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                ];
                $validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->passes()) {
                    $location_name = strip_tags($request['name']);
                    $location_detail = strip_tags($request['addr']);
                    /*$array = array();

                       $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($request['addr']).'&key=AIzaSyDfT2Iqt4yvPmQSGRJVApQUHdbv5XR_R-8&callback=initMap');

                       $geo = json_decode($geo,true);

        

                      if ($geo['status'] == 'OK')

                      {

                      $latitude = $geo['results'][0]['geometry']['location']['lat'];

                      $longitude = $geo['results'][0]['geometry']['location']['lng'];

                      $arrayvalue = array('lat'=> $latitude ,'lng'=>$longitude);

                      $latitude_loc = $arrayvalue['lat'];

                      $longitude_loc =  $arrayvalue['lng'];

                      

                      }

                      else

                      {

                      return redirect()->back()->with('chckmessage', 'Enter Correct Address');

                       } */
                    $valid = strip_tags($request['updateid']);
                    $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'location_name' => $location_name, 'location_details' => $location_detail);
                    $chckloc = DB::table('location_master')->where($where)->count();
                    if ($chckloc == 0) {
                        $updateid = array('id' => $valid);
                        $data = array('location_name' => $location_name, 'location_details' => $location_detail,'latitude' =>'00.0000','longitude'=>'00.0000', 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $getAllPermsDat = $this->PM->updatetable('location_master', $updateid, $data);
                        $getAllPermsDatas = $this->PM->getlocationmaster($d->st_code, $d->ac_no);

                        return redirect()->back()->with('message', 'Successfully Updated');
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered Location name and address is already Exist!')->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    // map integration

    public function getlocationList(Request $request) {
        $state = $request->input('stcode');
        $ac = $request->input('ac');
        $getACLists = DB::table('location_master')->where('ST_CODE', $state)
                ->where('AC_NO', '=', $ac)
                ->get();
        return json_encode($getACLists);
    }

    public function getlatlongs(Request $request) {
        $locationid = $request->input('locationid');
        $locationdetails = DB::table('location_master')->where('id', $locationid)
                ->get();
        return json_encode($locationdetails);
    }
    
    public function getPS(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            
            $acno=$req->ac_id;
            $stcode=$req->st_code;
//            echo $acno. $stcode;die;
            $getallps=$this->PM->getallps($acno,$stcode);
            return $getallps;
            } else {
            return redirect('/officer-login');
        }
    }
    
     public function getlocation(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            
            $acno=$req->ac_id;
            $stcode=$req->st_code;
            $dist=$req->dist;
            $pcno=$req->pc_id;
            if($pcno != 0)
            {
                $getlocation=DB::table('location_master')
                        ->select('location_name','id')
                        ->where('st_code',$stcode)
//                        ->where('dist_no',$dist)
                        ->where('ac_no',$acno)
                        ->where('pc_no',$pcno)
                        ->get()->toArray();
            }
            else
            {
                $getlocation=DB::table('location_master')
                        ->select('location_name','id')
                        ->where('st_code',$stcode)
//                        ->where('dist_no',$dist)
                        ->where('ac_no',$acno)
//                        ->where('pc_no',$pcno)
                        ->get()->toArray();
            }
            return $getlocation;
            } else {
            return redirect('/officer-login');
        }
    }
    
    public function getalldistrict(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no,$d->dist_no, $d->id, $d->officerlevel);
            
            $stcode=$req->stcode;
            $getAllDist = $this->PM->getAllDist($d->st_code);
            return $getAllDist;
        } else {
            return redirect('/officer-login');
        }
    }
    
    //PCI Master
    public function AddPCI() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $authority = $this->PM->getAuthority($d->st_code);
            $getAllPC = $this->PM->getAllPC($d->st_code, $d->dist_no);
            $getPCIrole = DB::table('role_master')->select('role_id','role_name','role_description','role_level')->where('role_level',12)->get();
            return view('admin.ac.deo.Permission.AddPCI')->with(array('user_data' => $d, 'getAllPC' => $getAllPC, 'authority' => $authority,'getPCIrole'=>$getPCIrole));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddPCIData(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
//            echo '<pre/>';
//            print_r($request->all());die;
            $rules = [
                'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                'email' => 'required|email',
                'addr' => 'required|not_regex:/([<>@$%?]+)/',
                'mb' => 'required|numeric|digits:10',
                'pname' => 'required|not_in:0',
            ];
            $messages = [
                'name.required' => ' Name field is required.',
                'name.regex' => 'Please Enter only alphanumeric character.',
                'addr.required' => ' Address field is required.',
                'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                'mb.required' => ' Mobile no is required.',
                'mb.digits' => 'Please Enter valid Mobile Number.',
                'mb.numeric' => 'Please Enter valid Mobile Number.',
                'dept.required' => 'Departemnt is required',
                'dept.regex' => 'Please Enter only alphanumeric character.',
                'desig.required' => 'Designation is required Field',
                'desig.regex' => 'Please Enter only alphanumeric character.',
                'email.required' => 'Email is required',
                'pname.required' => 'Select role field is required.',
                'pname.not_in' => 'Select role field is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->passes()) {
                if (!empty($request->name)) {
                    $name = strip_tags($request->name);
                }
                if (!empty($request->dept)) {
                    $dept = strip_tags($request->dept);
                }
                if (!empty($request->desig)) {
                    $desig = strip_tags($request->desig);
                }
                if (!empty($request->mb)) {
                    $mb = strip_tags($request->mb);
                }
                if (!empty($request->email)) {
                    $email = strip_tags($request->email);
                }
                if (!empty($request->addr)) {
                    $addr = strip_tags($request->addr);
                }
                $role_id = $request->pname;
                $pin = bcrypt(1234);
                $pass = bcrypt('demo@1234');
                $checkexistpci=DB::table('pci_masters')->where(array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'role_id' => $role_id,'status'=>1))->count();
                $checkAuthmb = DB::table('pci_masters')->where(array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'mobile' => $mb))->count();
                $checkAuthemail = DB::table('pci_masters')->where(array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'email' => $email))->count();
                if ($checkexistpci == 0) {
                if ($checkAuthmb == 0) {
                    if ($checkAuthemail == 0) {
                    $data = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no,'role_id' =>$role_id, 'name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                    $result = DB::table('pci_masters')->insertGetId($data);
                    $username = 'PCI'.$d->st_code.'D'.$d->dist_no.$result;
                    if (!empty($result)) {
                        $data1 = array('two_step_pin' => $pin, 'officername' => $username, 'designation' => $desig, 'name' => $name, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no,'Phone_no' => $mb, 'email' => $email, 'role_id' =>$role_id, 'officerlevel' => 'PCI', 'password' => $pass, 'election_id' => $d->election_id);
                        $lastinsertid = DB::table('officer_login')->insertGetId($data1);
                        if(!empty($lastinsertid))
                        {
                            $data = array('username' => $username,'officer_login_id' => $lastinsertid,'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where = array('id' =>$result);
                            $update = $this->PM->updatetable('pci_masters', $where, $data);
                        }
                        return redirect('/acdeo/viewpci')->with('message', 'Successfully Added');
                    } else {
                        return redirect()->back()->with('message', 'Some error occured');
                    }
                    }else {

                    return redirect()->back()->with('chckmessage', 'Entered Email is already Exist!')->withInput();
                    
                    }
                } else {

                    return redirect()->back()->with('chckmessage', 'Entered Mobile No is already Exist!')->withInput();
                }
                }
                else {

                    return redirect()->back()->with('chckmessage', 'Entered PCI already exist and is active')->withInput();
                }
            } else {
                return redirect()->back()->withErrors($validator, 'error')->withInput();
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewPCI(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $getAllPCIData = DB::table('pci_masters as a')
                    ->join('role_master as b','b.role_id','=','a.role_id')
                    ->where('a.st_code',$d->st_code)
                    ->where('a.dist_no',$d->dist_no)
                    ->select('a.*','b.role_name','a.id as pci_id')
                    ->paginate(5);
            return view('admin.ac.deo.Permission.ViewPCI')->with(array('user_data' => $d, 'getAllPCIData' => $getAllPCIData));
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditPCI(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            if (isset($_POST['editpci'])) {
                 $rules = [
                'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                'email' => 'required|email',
                'addr' => 'required|not_regex:/([<>@$%?]+)/',
                'mb' => 'required|numeric|digits:10',
                'pname' => 'required|not_in:0',
            ];
            $messages = [
                'name.required' => ' Name field is required.',
                'name.regex' => 'Please Enter only alphanumeric character.',
                'addr.required' => ' Address field is required.',
                'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                'mb.required' => ' Mobile no is required.',
                'mb.digits' => 'Please Enter valid Mobile Number.',
                'mb.numeric' => 'Please Enter valid Mobile Number.',
                'dept.required' => 'Departemnt is required',
                'dept.regex' => 'Please Enter only alphanumeric character.',
                'desig.required' => 'Designation is required Field',
                'desig.regex' => 'Please Enter only alphanumeric character.',
                'email.required' => 'Email is required',
                'pname.required' => 'Select role field is required.',
                'pname.not_in' => 'Select role field is required.',
            ];
                $validator = Validator::make($req->all(), $rules, $messages);
                    if ($validator->passes()) {
                        if (!empty($req->name)) {
                        $name = strip_tags($req->name);
                    }
                    if (!empty($req->dept)) {
                        $dept = strip_tags($req->dept);
                    }
                    if (!empty($req->desig)) {
                        $desig = strip_tags($req->desig);
                    }
                    if (!empty($req->mb)) {
                        $mb = strip_tags($req->mb);
                    }
                    if (!empty($req->email)) {
                        $email = strip_tags($req->email);
                    }
                    if (!empty($req->addr)) {
                        $addr = strip_tags($req->addr);
                    }
                    $role_id = $req->pname;
//                    $username = 'PCI'.$d->st_code.'D'.$d->dist_no.$role_id;
                    $username = 'PCI'.$d->st_code.'D'.$d->dist_no.$req->pci_id;
                    $where = array('mobile' => $mb);
                    $chckloc = DB::table('pci_masters')->where($where)->count();
                    $chckemail = DB::table('pci_masters')->where('email',$email)->count();
                    if ($chckloc == 0) {
                        if($chckemail == 0)
                        {
                        $data = array('role_id'=>$role_id,'name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr,'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $where = array('id' => $req->pci_id);
                        $update = $this->PM->updatetable('pci_masters', $where, $data);
                        if($update == 1)
                        {
                            $data1 = array('officername' =>$username,'designation' => $desig, 'name' => $name, 'Phone_no' => $mb, 'email' => $email, 'role_id' =>$role_id,);
                            $where = array('officername' => $req->pci_uname);
                            $update = $this->PM->updatetable('officer_login', $where, $data1);
                        }
                        return redirect()->back()->with('message', 'Successfully Updated');
                        }
                        else
                        {
                            $data = array('role_id'=>$role_id,'name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb,'address' => $addr,'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where = array('id' => $req->pci_id);
                            $update = $this->PM->updatetable('pci_masters', $where, $data);
                            if($update == 1)
                            {
                                $data1 = array('officername' =>$username,'designation' => $desig, 'name' => $name, 'Phone_no' => $mb, 'email' => $email, 'role_id' =>$role_id,);
                                $where = array('officername' => $req->pci_uname);
                                $update = $this->PM->updatetable('officer_login', $where, $data1);
                            }
                            return redirect()->back()->with('message', 'Mobile no and Email should be unique to update the data successfully');
                        }
                    } else {
                        if($chckemail == 0)
                        {
                        $data = array('username' =>$username,'role_id'=>$role_id,'name' => $name, 'department' => $dept, 'designation' => $desig,'email' => $email, 'address' => $addr,'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $where = array('id' => $req->pci_id);
                        $update = $this->PM->updatetable('pci_masters', $where, $data);
                        if($update == 1)
                        {
                            $data1 = array('officername' =>$username,'designation' => $desig, 'name' => $name,'email' => $email, 'role_id' =>$role_id,);
                            $where = array('officername' => $req->pci_uname);
                            $update = $this->PM->updatetable('officer_login', $where, $data1);
                        }
                        return redirect()->back()->with('message', 'Mobile no and Email should be unique to update the data successfully');
//                        return redirect()->back()->with('chckmessage', 'Entered  mobile no is already Exist!')->withInput();
                        }
                        else
                        {
                            $data = array('username' =>$username,'role_id'=>$role_id,'name' => $name, 'department' => $dept, 'designation' => $desig,'address' => $addr,'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $where = array('id' => $req->pci_id);
                        $update = $this->PM->updatetable('pci_masters', $where, $data);
                        if($update == 1)
                        {
                            $data1 = array('officername' =>$username,'designation' => $desig, 'name' => $name,'email' => $email, 'role_id' =>$role_id,);
                            $where = array('officername' => $req->pci_uname);
                            $update = $this->PM->updatetable('officer_login', $where, $data1);
                        }
                        return redirect()->back()->with('message', 'Mobile no and Email should be unique to update the data successfully');
//                        return redirect()->back()->with('chckmessage', 'Entered  mobile no is already Exist!')->withInput();
                        }
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            } else {
                $getAllPCIData = DB::table('pci_masters as a')
                    ->join('role_master as b','b.role_id','=','a.role_id')
                    ->where('a.st_code',$d->st_code)
                    ->where('a.dist_no',$d->dist_no)
                    ->where('a.id',$req->id)
                    ->select('a.*','b.role_name')
                    ->get();
                $getPCIrole = DB::table('role_master')->select('role_id','role_name','role_description','role_level')->where('role_level',12)->get();
                return view('admin.ac.deo.Permission.EditPCI', ['user_data' => $d], ['getAllPCIData' => $getAllPCIData,'getPCIrole'=>$getPCIrole], ['ele_details' => $ele_details]);
            }
        } else {
            return redirect('/officer-login');
        }
    }
     public function EditPCIStatus(Request $req) {
       if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $data = explode('#', $req->status);
            $status = $data[0];
            $id = $data[1];
            $role_id = $data[2];
            $ofcr_login_id = $data[3];
            $where = array('id' => $id);
            if($status == 0)
            {
              $chckusr = DB::table('pci_masters')->where(array('st_code'=>$d->st_code,'dist_no'=>$d->dist_no,'role_id'=>$role_id ,'status'=>1))->count();
              if($chckusr == 0)
              {
                $cond = array('status' => '1'); 
                $res = $this->PM->updatetable('pci_masters', $where, $cond);
                $data1 = array('is_active' =>1);
                $where1 = array('id' => $ofcr_login_id);
                $update = $this->PM->updatetable('officer_login', $where1, $data1);
                
                $data2 = array('status' =>1);
                $where2 = array('pci_role_id' => $role_id,'st_code'=>$d->st_code,'dist_id'=>$d->dist_no);
                $updateptype = $this->PM->updatetable('pci_assignment', $where2, $data2);
                if ($res == 1) {
                    return 1;
                } else {
                    return 0;
                }
              }
              else
              {
                  return 2;
              }
            }
            else
            {
                $cond = array('status' => '0');
                $res = $this->PM->updatetable('pci_masters', $where, $cond);
                
                $data1 = array('is_active' =>0);
                $where1 = array('id' => $ofcr_login_id);
                $update = $this->PM->updatetable('officer_login', $where1, $data1);
                
                $data2 = array('status' =>0);
                $where2 = array('pci_role_id' => $role_id,'st_code'=>$d->st_code,'dist_id'=>$d->dist_no);
                $updateptype = $this->PM->updatetable('pci_assignment', $where2, $data2);
                if ($res == 1) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }
     public function AssignToPCI(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $getAllPermissiontype = $this->PM->getAllPermissiontype($d->role_id,$d->st_code,$d->dist_no);
            $getrole = $this->PM->getofficerlevel();
            return view('admin.ac.deo.Permission.AssignTOPCI')->with(array('user_data' => $d,'permissionDetails' => $getAllPermissiontype,'getrole'=>$getrole));
        } else {
            return redirect('/officer-login');
        }
    }
    public function AssignToPCIData(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $getAllPermissiontype = $this->PM->getAllPermissiontype($d->role_id,$d->st_code,$d->dist_no);
            $getrole = $this->PM->getofficerlevel();
                 $rules = [
                'pname' => 'required|not_in:0',
                'ofcrlevel' => 'required|not_in:0',
            ];
            $messages = [
                'pname.required' => 'Select role field is required.',
                'pname.not_in' => 'Select role field is required.',
                'ofcrlevel.required' => 'Select role field is required.',
                'ofcrlevel.not_in' => 'Select Assign to level field is required.',
            ];
                $validator = Validator::make($req->all(), $rules, $messages);
                    if ($validator->passes()) {
                        $pci_role_id = $req->ofcrlevel;
                        $ptype_id = $req->pname;
//                        $data = array('pci_role_status'=>1,'pci_role_id'=>$pci_role_id,'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
//                        $where = array('id' => $ptype_id,'st_code'=>$d->st_code);
//                        $update = $this->PM->updatetable('permission_type', $where, $data);
                        $data = array('permission_type_id'=>$ptype_id,'status'=>1,'pci_role_id'=>$pci_role_id,'created_by' => $d->id,'st_code'=>$d->st_code,'dist_id'=>$d->dist_no);
                        $insetdata=$this->PM->insertdata('pci_assignment',$data);
                        if($insetdata == 1)
                        {
                        return redirect()->back()->with('message', 'Successfully Assign to permission cell Incharge');
                        }
                        else
                        {
                            return redirect()->back()->with('message', 'Some error occured');
                        }
                    }
                    else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            return view('admin.ac.deo.Permission.AssignTOPCI')->with(array('user_data' => $d,'permissionDetails' => $getAllPermissiontype));
       }else {
            return redirect('/officer-login');
        }
    }
     public function ViewAssignToPCI(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $getAllPermissiontype = $this->PM->getAssignAllPermissiontype($d->st_code,$d->dist_no);
            $getrole = $this->PM->getofficerlevel();
            return view('admin.ac.deo.Permission.ViewAssignTOPCI')->with(array('user_data' => $d,'permissionDetails' => $getAllPermissiontype));
       }else {
            return redirect('/officer-login');
        }
    }
    public function GetPCIDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $getpcidetails = DB::table('pci_assignment as a')
                    ->join('officer_login as b','b.role_id','=','a.pci_role_id')
                    ->join('pci_masters as c','c.officer_login_id','=','b.id')
                    ->join('role_master as r','a.pci_role_id','=','r.role_id')
                    ->where('b.st_code',$d->st_code)
                    ->where('b.dist_no',$d->dist_no)
                    ->where('a.permission_type_id',$req->p_id)
                    ->where('c.status',1)
                    ->select('b.officerlevel','c.name','c.mobile','c.department','a.pci_role_id','r.role_name')
                    ->get();
            return $getpcidetails;
        } else {
            return redirect('/officer-login');
        }
    }
     public function EditAssignToPCI(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
           if (isset($_POST['updatepermsn'])) {
                 $rules = [
                'pname' => 'required|not_in:0',
                'ofcrlevel' => 'required|not_in:0',
            ];
            $messages = [
                'pname.required' => 'Select role field is required.',
                'pname.not_in' => 'Select role field is required.',
                'ofcrlevel.required' => 'Select role field is required.',
                'ofcrlevel.not_in' => 'Select Assign to level field is required.',
            ];
                $validator = Validator::make($req->all(), $rules, $messages);
                    if ($validator->passes()) {
                        $pci_role_id = $req->ofcrlevel;
                        $ptype_id = $req->pname;
                        $data = array('status'=>1,'pci_role_id'=>$pci_role_id,'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $where = array('permission_type_id' => $ptype_id,'st_code'=>$d->st_code,'dist_id'=>$d->dist_no);
                        $update = $this->PM->updatetable('pci_assignment', $where, $data);
                        return redirect()->back()->with('message', 'Successfully Assign to permission cell Incharge');
                    }
                    else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
//            return view('admin.ac.deo.Permission.ViewAssignTOPCI')->with(array('user_data' => $d,'permissionDetails' => $getAllPermissiontype));
            }
            else
            {
                $getPermissiontype = DB::table('permission_master as a')
                            ->join('pci_assignment as b','a.id','=','b.permission_type_id')
                            ->join('role_master as c','b.pci_role_id','=','c.role_id')
                            ->where('b.permission_type_id',$req->id)
                            ->select('a.permission_name','b.id as ptype_id','b.pci_role_id','c.role_name')
                            ->first();
                $getrole = $this->PM->getofficerlevel();
                return view('admin.ac.deo.Permission.EditAssignTOPCI')->with(array('user_data' => $d,'permissionDetails' => $getPermissiontype,'getrole'=>$getrole));
            }
            
            }else {
            return redirect('/officer-login');
        }
    }
    public function GetMobile(Request $request) {
        
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $checkAuthmb = DB::table('authority_masters as a')
                ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                ->select('a.*', 'b.auth_type_id')
                ->where('a.mobile', $request->mobileno)->first();
             
            return response(['status'=>(($checkAuthmb) ? true : false),'result'=>$checkAuthmb]);
        } else {
            return response(['status'=>false],401);
        }
    }
}