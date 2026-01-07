<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\adminmodel\CeoPcPermissionModel;
use Illuminate\Http\Request;
use Session;
use App\commonModel;
use App\adminmodel\CandidateModel;
use App\adminmodel\ROPCModel;
use App\Classes\xssClean;
use Carbon\Carbon;
use App\Helpers\SmsgatewayHelper;
use App\Helpers\LogNotification;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PDF;

class CeoPCPermissionController extends Controller
{
    public $commonModel = null;
    public $xssClean = null;
    public $PM = null;

    public function __construct()
    {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->PM = new CeoPcPermissionModel();
    }

    public function allMasters()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            return view('admin.ac.ceo.Permission.Masters', ['user_data' => $d]);
        } else {
            return redirect('/officer-login');
        }
    }

    //permission Master
    public function AddPermission()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $getAuthType = $this->PM->getAuthType($d->st_code);
            $getAllPermissiontype = $this->PM->getAllPermissiontype();
            $getrole = $this->PM->getofficerlevel();
            $getasignperms = DB::table('permission_type')
                ->select('permission_type_id')
                ->where('status', 1)
                ->where('st_code', $d->st_code)
                ->get()->toArray();
            $getasignperm = array_column($getasignperms, 'permission_type_id');

            return view('admin.ac.ceo.Permission.AddPermission')->with(array('user_data' => $d, 'getrole' => $getrole, 'showpage' => 'permission', 'getasignperm' => $getasignperm, 'getAuthType' => $getAuthType, 'getAllPermissiontype' => $getAllPermissiontype));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddPermissionData(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            $document = $request->input('doc');
            $files = $request->file('doc');
            $rules = [];
            $message = [];
            $authority_id = 0;
            $visible_type = NULL;
            $time = Carbon::now()->timestamp;
            $pname = null;


            try {
                if (config('public_config.permission_log')) {

                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'SUCCESS';
                    $message['LogDescription'] = 'Permission Add Successfully';

                    LogNotification::LogInfo($message);
                }

                if (!empty($_POST['pname'])) {
                    $pname = strip_tags($_POST['pname']);
                }
                if (!empty($_POST['restriction_day'])) {
                    $restriction_day = strip_tags($_POST['restriction_day']);
                }
                if (!empty($_POST['ofcrlevel'])) {
                    $ofcrlevel = strip_tags($_POST['ofcrlevel']);
                }


                if (!empty($document)) {
                    $rules = [
                        'pname' => 'required|not_in:0',
                        //                    'auth_name' => 'required',
                        'doc.*.Dname' => 'required|not_regex:/([<>@$%?#]+)/',
                        //                    'doc.*.fsize' => 'required|numeric',
                        //'doc.*.format' => 'required|mimes:pdf',
                        'ofcrlevel' => 'required|not_in:0',
                        'doc.*.approvalauthority' => 'required|not_in:0',
                        'restriction_day' => 'required|not_in:0'
                    ];
                    foreach ($request->input('doc') as $key => $row) {
                        if (isset($row['chck']) ? 1 : 0 == '1') {
                            $rules['doc.' . $key . '.format'] = 'required|mimes:pdf';
                        }
                    }
                } else {
                    $rules = [
                        'pname' => 'required|regex:/(^[ A-Za-z]+$)/',
                    ];
                }
                $messages = [
                    'pname.not_in' => 'Permission name field is required.',
                    'pname.required' => 'Permission name field is required',
                    //                'auth_name.required' => 'Select Authority type is required.',
                    'doc.*.Dname.required' => 'Document name is required',
                    'doc.*.Dname.not_regex' => 'These special character are not allowed(<>@$%?#).',
                    //                'doc.*.fsize.required' => 'File size required',
                    //                'doc.*.fsize.numeric' => 'Please enter only numeric value for file size.',
                    //'doc.*.format.required' => 'Please Upload required Document',
                    'doc.*.format.mimes' => 'Please Upload only (.pdf) document',
                    'ofcrlevel' => 'Please select Assigned Level',
                    'ofcrlevel' => 'Please select Assigned Level',
                    'doc.*.approvalauthority.required' => 'Select Authority type is required.',
                    'doc.*.approvalauthority.not_in' => 'Select Authority type is required.',
                    'restriction_day.not_in' => 'Permission Validity Day ',
                    'restriction_day.required' => 'Permission Validity Day '
                ];
                foreach ($request->input('doc') as $key => $row) {
                    if (isset($row['chck']) ? 1 : 0 == '1') {
                        $messages['doc.' . $key . '.format.required'] = 'Please Upload required Document';
                    }
                }

                $validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->passes()) {
                    $visible_type = $request->input('visible_type');
                    if (!empty($visible_type)) {
                        $visible_type = implode(',', $visible_type);
                    }
                    $auth_id = $request->input('auth_name');
                    if (!empty($auth_id)) {
                        $authtype = implode(',', $auth_id);
                    }
                    $chckid = DB::table('permission_type')
                        ->where('permission_type_id', $pname)
                        ->where('st_code', $d->st_code)->count();
                    if (!empty($request->restriction_day)) {
                        $restriction_day = strip_tags($request->restriction_day);
                    }
                    if (isset($_POST['daterestriction'])) {
                        $daterestrict = $_POST['daterestriction'];
                    } else {
                        $daterestrict = 0;
                    }
                    if ($chckid == 0) {
                        $data = array("role_id" => $ofcrlevel, "permission_type_id" => $pname, "visible_type" => $visible_type, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'status' => 1, 'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $result = DB::table('permission_type')->insertGetId($data);

                        if (!empty($result) && $result != '') {
                            for ($i = 0; $i < count($document); $i++) {

                                $dname = 'NULL';
                                $reqaproval = 'NULL';
                                $chck = 0;
                                $format = 'NULL';
                                if (!empty($document[$i]['Dname'])) {
                                    $dname = strip_tags($document[$i]['Dname']);
                                }
                                //                        if (!empty($document[$i]['fsize'])) {
                                //                            $fsize = strip_tags($document[$i]['fsize']);
                                //                        }
                                if (!empty($document[$i]['chck'])) {
                                    $chck = strip_tags($document[$i]['chck']);
                                }
                                if (!empty($document[$i]['approvalauthority'])) {
                                    $allauthority = $document[$i]['approvalauthority'];
                                    $reqaproval = implode(',', $allauthority);
                                }
                                if (!empty($files) && isset($files[$i])) {
                                    $format = 'uploads1/permission-document/' . $d->election_id . '/' . $d->st_code . '/' . preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $files[$i]['format']->getClientOriginalName());
                                    $format1 = preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $files[$i]['format']->getClientOriginalName());
                                    $destinationPath3 = public_path('/uploads1/permission-document/' . $d->election_id . '/' . $d->st_code);
                                    $files[$i]['format']->move($destinationPath3, $format1);
                                }
                                $data1 = array('authority_type_id' => $reqaproval, 'fileserver_dir' => 'uploads1', "permission_type_id" => $pname, 'permission_id' => $result, 'doc_name' => $dname, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'required_status' => $chck, 'file_name' => $format, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                $pdata = DB::table('permission_required_doc')->insert($data1);
                            }
                            $dataddy = array('st_code' => $d->st_code, "permission_type_id" => $pname, "restriction_day" => $restriction_day, 'restriction_status' => $daterestrict, 'modified_at' => $d->id, 'added_at' => date('Y-m-d'), 'modified_by' => $d->id, 'created_at' => date('Y-m-d H:i:s'));
                            $resultdys = $this->PM->insertdata('restriction_day_master', $dataddy);

                            return redirect('/acceo/viewpermsn')->with('message', 'Successfully Added');
                        }
                        return redirect('/acceo/viewpermsn')->with('message', 'Some error occured');
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered Permission Name is already Exist!')->withInput();
                    }
                }
                return redirect()->back()->withErrors($validator, 'error')->withInput();
            } catch (Exception $e) {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'Failed';
                    $message['LogDescription'] = 'Something went to wrong ' . $e->getMessage();

                    LogNotification::LogInfo($message);
                }
            }
        } else {
            return redirect('/officer-login');
        }
        //return response()->json(['error'=>$validator->errors()->all()]);
    }

    public function ViewPerms()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            //$getAllPermsData = $this->PM->getAllPermsData($d->st_code);
            $a = $this->PM->getAllPermsData($d->st_code);
            $getAllPermsData = [];
            foreach ($a as $key => $item) {
                $getAllPermsData[$key]['enc_p_id'] = Crypt::encryptString($item->p_id);
                $getAllPermsData[$key]['p_id'] = $item->p_id;
                $getAllPermsData[$key]['pname'] = $item->pname;
            }

            return view('admin.ac.ceo.Permission.ViewPermission')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAllPermsData' => $getAllPermsData));
        } else {
            return redirect('/officer-login');
        }
    }

    public function GetdocDetails(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $permsn_id = Crypt::decryptString($request->p_id);
            if (!empty($permsn_id)) {
                $getdocDetails = DB::table('permission_required_doc as a')
                    ->join('permission_type as b', 'b.id', '=', 'a.permission_id')
                    ->join('role_master as m', 'm.role_id', '=', 'b.role_id')
                    ->select('a.*', 'm.role_name')
                    ->where('a.permission_id', $permsn_id)
                    ->where('a.st_code', $d->st_code)
                    ->get()->toArray();
                $detailsdata = array();
                foreach ($getdocDetails as $data) {
                    $getcanddoc = DB::table('permission_required_doc as a')->select('a.authority_type_id')->where('a.id', $data->id)->first();
                    $getcanddoc = explode(',', $getcanddoc->authority_type_id);
                    $canddoc = "";
                    if (!empty($getcanddoc) && in_array("cand01", $getcanddoc)) {
                        $canddoc = 'Applicant';
                    }
                    $getauthDetails = DB::table('permission_required_doc as a')
                        ->join('authority_type as c', DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"), ">", \DB::raw("'0'"))
                        ->select(DB::raw("GROUP_CONCAT(DISTINCT c.name SEPARATOR ',') as 'auth_name'"))
                        ->where('a.id', $data->id)->first();
                    $detailsdata[] = array(
                        'id' => $data->id,
                        'permission_id_enc' => Crypt::encryptString($data->permission_id),
                        'permission_id'  =>  $data->permission_id,
                        'permission_type_id'  => $data->permission_type_id,
                        'authority_type_id'  => $data->authority_type_id,
                        'doc_name'  => $data->doc_name,
                        'doc_size'  => $data->doc_size,
                        'st_code'  => $data->st_code,
                        'required_status'  => $data->required_status,
                        'file_name'  => $data->file_name,
                        'fileserver_dir'  => $data->fileserver_dir,
                        'status'  => $data->status,
                        'auth_name' => $getauthDetails->auth_name,
                        'canddoc_name' => $canddoc,
                    );
                }
                //dd($detailsdata);
                if (!empty($detailsdata)) {
                    //return ['permission_id' => Crypt::encryptString($detailsdata[0]->permission_id), 'data' => $detailsdata];
                    //                        $getPermissionDetails[0]=$getdocDetails;
                    //                        $getPermissionDetails[1]=$getauthDetails;
                    //                        print_r($getPermissionDetails);die;
                    return $detailsdata;
                } else {
                    return '0';
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditPrmsn(Request $request)
    {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $time = Carbon::now()->timestamp;
            $maxDays = 75;
            if (!empty($_POST['UpdatePermission'])) {
                
                $document = $request->input('doc');
                $file = $request->file('doc');
                $rules = [];
                $message = [];
                $auth_id = '';
                $restriction_day = '';
                $authority_id = 0;
                $ofcrlevel = '';
                $visible_type = NULL;
                if (!empty($_POST['pname'])) {
                    $pname = strip_tags($_POST['pname']);
                }
                if (!empty($_POST['pname_id'])) {
                    $pname_id = strip_tags($_POST['pname_id']);
                }
                if (!empty($request->restriction_day)) {
                    $restriction_day = strip_tags($request->restriction_day);
                }
                if (!empty($_POST['ofcrlevel'])) {
                    $ofcrlevel = strip_tags($_POST['ofcrlevel']);
                }
                if (!empty($_POST['police'])) {
                    $police_id = strip_tags($_POST['police']);
                    $auth_id .= $police_id . ',';
                }
                if (!empty($_POST['fd'])) {
                    $fd_id = strip_tags($_POST['fd']);
                    $auth_id .= $fd_id . ',';
                }
                if (!empty($_POST['rd'])) {
                    $rd_id = strip_tags($_POST['rd']);
                    $auth_id .= $rd_id . ',';
                }
                if (!empty($_POST['pwd'])) {
                    $pwd_id = strip_tags($_POST['pwd']);
                    $auth_id .= $pwd_id . ',';
                }
                if ($request->File('doc')) {
                    //                    echo '<pre/>';
                    //                    print_r($file);
                    //                    print_r($document);
                    //                    die;
                    $rules = [
                        'pname' => 'required|not_regex:/([<>@$%?#]+)/',
                        'doc.*.Dname' => 'required|not_regex:/([<>@$%?#]+)/',
                        //                        'auth_name' => 'required',
                        //                        'doc.*.fsize' => 'required|numeric',
                        'doc.*.format' => 'mimes:pdf',
                        'doc.*.approvalauthority' => 'required|not_in:0',
                        'restriction_day' => 'required',

                    ];
                    $messages = [
                        'pname.not_regex' => 'Please enter only alphanumeric value.',
                        'pname.required' => 'Permission name field is required',
                        'doc.*.Dname.required' => 'Document name is required',
                        'doc.*.Dname.not_regex' => 'These special character are not allowed(<>@$%?#).',
                        //                        'auth_name.required' => 'Select Authority type is required.',
                        //                    'doc.*.fsize.required' => 'File size required',
                        //                    'doc.*.fsize.numeric' => 'Please enter only numeric value for file size.',
                        //                        'doc.*.format.required' => 'Please Upload required Document',
                        'doc.*.format.mimes' => 'Please Upload only (.pdf) document',
                        'doc.*.approvalauthority.required' => 'Select Authority type is required.',
                        'doc.*.approvalauthority.not_in' => 'Select Authority type is required.',
                        'restriction_day' => 'Please Enter Day field is required',
                    ];
                    $validator = Validator::make($request->all(), $rules, $messages);

                    if ($validator->passes()) {
                        $visible_type = $request->input('visible_type');
                        if (!empty($visible_type)) {
                            $visible_type = implode(',', $visible_type);
                        }
                        //                    $auth_id = $request->input('auth_name');
                        //                    if (!empty($auth_id)) {
                        //                        $authtype = implode(',', $auth_id);
                        //                    }
                        $getrole_id = DB::table('permission_type')->select('role_id')->where('id', $request->p_id)->first();
                        $getrole_id = DB::table('permission_type')->select('role_id', 'visible_type')->where('id', $request->p_id)->first();

                        if ($getrole_id->role_id != $ofcrlevel) {
                            $updatepermission = DB::table('permission_type')->where('id', $request->p_id)->update(array('role_id' => $ofcrlevel));
                        }

                        if ($getrole_id->visible_type != $visible_type) {

                            //dd($getrole_id->visible_type)
                            $updatepermission = DB::table('permission_type')->where('id', $request->p_id)->update(array('visible_type' => $visible_type));
                        }
                        $data = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'status' => 1, 'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        //                $result = DB::table('permission_type')->insertGetId($data);
                        $where = array('id' => $request->p_id);
                        $result = $this->PM->updatetable('permission_type', $where, $data); 

                        $data22 = array('permission_type_id' => $pname_id, "restriction_day" => $restriction_day, 'modified_by' => $d->id,  'modified_at' => date('Y-m-d H:i:s'));
                        $wherea = array('permission_type_id' => $request->pname_id, 'st_code' => $d->st_code);

                        $updaterestriction_day =DB::table('restriction_day_master') 
                        ->where('permission_type_id',$request->pname_id)
                        ->where('st_code',$d->st_code)
                        ->first();
                        if($updaterestriction_day == null){

                            $datarestriction = array('st_code' => $d->st_code, "permission_type_id"=>$pname_id,"restriction_day"=>$restriction_day, 'restriction_status' => '0', 'modified_at' => $d->id, 'added_at' => date('Y-m-d'),'modified_by'=>$d->id ,'created_at' => date('Y-m-d H:i:s'));
                            $resultdatarestriction = $this->PM->insertdata('restriction_day_master', $datarestriction);
                
                            
                         }
                         else{
                            $result22 = $this->PM->updatetable('restriction_day_master', $wherea, $data22);
                         }

                       //$result22 = $this->PM->updatetable('restriction_day_master', $where, $data22);
                        //                if (!empty($result) && $result != '') {
                        for ($i = 0; $i < count($document); $i++) {
                            $doc_id = 0;
                            $dname = 'NULL';
                            //                        $fsize = 'NULL';
                            $chck = 0;
                            $format = 'NULL';
                            $reqaproval = 'NULL';
                            if (!empty($document[$i]['doc_id'])) {
                                $doc_id = strip_tags($document[$i]['doc_id']);
                                //                            echo $doc_id;die;
                            }
                            if (!empty($document[$i]['Dname'])) {
                                $dname = strip_tags($document[$i]['Dname']);
                            }
                            //                        if (!empty($document[$i]['fsize'])) {
                            //                            $fsize = strip_tags($document[$i]['fsize']);
                            //                        }
                            if (!empty($document[$i]['chck'])) {
                                $chck = strip_tags($document[$i]['chck']);
                            }
                            if (!empty($document[$i]['approvalauthority'])) {
                                $allauthority = $document[$i]['approvalauthority'];
                                $reqaproval = implode(',', $allauthority);
                            }
                            if (!empty($file[$i])) {
                                $format = 'uploads1/permission-document/' . $d->election_id . '/' . trim($d->st_code) . '/' . preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $file[$i]['format']->getClientOriginalName());
                                $format1 = preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $file[$i]['format']->getClientOriginalName());
                                $destinationPath3 = public_path('/uploads1/permission-document/' . $d->election_id . '/' . trim($d->st_code));
                                $file[$i]['format']->move($destinationPath3, $format1);
                                $getdocid = DB::table('permission_required_doc')->select('id')->where('id', $doc_id)->get()->toArray();
                                //                        print_r($getdocid);die;
                                if (!empty($getdocid)) {
                                    $data1 = array('authority_type_id' => $reqaproval, 'fileserver_dir' => 'uploads1', 'doc_name' => $dname, 'st_code' => $d->st_code, 'required_status' => $chck, 'file_name' => $format, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                                    $where1 = array('id' => $doc_id, 'permission_id' => $request->p_id);
                                    $pdata = $this->PM->updatetable('permission_required_doc', $where1, $data1);
                                } else {
                                    $data1 = array('authority_type_id' => $reqaproval, 'fileserver_dir' => 'uploads1', 'permission_id' => $request->p_id, 'permission_type_id' => $request->p_type_id, 'doc_name' => $dname, 'st_code' => $d->st_code, 'required_status' => $chck, 'file_name' => $format, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                    $pdata = DB::table('permission_required_doc')->insert($data1);
                                }
                            } else {
                                $getdocid = DB::table('permission_required_doc')->select('id')->where('id', $doc_id)->get()->toArray();
                                //                        print_r($getdocid);die;
                                if (!empty($getdocid)) {
                                    $data1 = array('authority_type_id' => $reqaproval, 'fileserver_dir' => 'uploads1', 'doc_name' => $dname, 'st_code' => $d->st_code, 'required_status' => $chck, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                                    $where1 = array('id' => $doc_id, 'permission_id' => $request->p_id);
                                    $pdata = $this->PM->updatetable('permission_required_doc', $where1, $data1);
                                } else {
                                    $data1 = array('authority_type_id' => $reqaproval, 'fileserver_dir' => 'uploads1', 'permission_id' => $request->p_id, 'permission_type_id' => $request->p_type_id, 'doc_name' => $dname,  'st_code' => $d->st_code, 'required_status' => $chck, 'file_name' => $format, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                    $pdata = DB::table('permission_required_doc')->insert($data1);
                                }
                            }
                        }

                        return redirect()->back()->with('message', 'Successfully Updated');
                    }
                    return redirect()->back()->withErrors($validator, 'error');
                } else {
                    $rules = [
                        'pname' => 'required|not_regex:/([<>@$%?#]+)/',
                        'doc.*.approvalauthority' => 'required|not_in:0'
                        //                        'doc.*.Dname' => 'required|not_regex:/([<>@$%?#]+)/',
                        //                        'auth_name' => 'required',
                        ////                        'doc.*.fsize' => 'required|numeric',
                        //                        'doc.*.format' => 'required|mimes:pdf',
                    ];

                    $messages = [
                        'pname.not_regex' => 'Please enter only alphanumeric value.',
                        'pname.required' => 'Permission name field is required',
                        'doc.*.approvalauthority.required' => 'Select Authority type is required.',
                        'doc.*.approvalauthority.not_in' => 'Select Authority type is required.',
                        //                    'doc.*.Dname.required' => 'Document name is required',
                        //                    'doc.*.Dname.not_regex' => 'These special character are not allowed(<>@$%?#).',
                        //                        'auth_name.required' => 'Select Authority type is required.',
                        //                    'doc.*.fsize.required' => 'File size required',
                        //                    'doc.*.fsize.numeric' => 'Please enter only numeric value for file size.',
                        //                    'doc.*.format.required' => 'Please Upload required Document',
                        //                    'doc.*.format.mimes' => 'Please Upload only (.pdf) document',
                    ];
                    $validator = Validator::make($request->all(), $rules, $messages);

                    if ($validator->passes()) {
                        $visible_type = $request->input('visible_type');
                        if (!empty($visible_type)) {
                            $visible_type = implode(',', $visible_type);
                        }
                        //                     $auth_id = $request->input('auth_name');
                        //                    if (!empty($auth_id)) {
                        //                        $authtype = implode(',', $auth_id);
                        //                    }


                        $getrole_id = DB::table('permission_type')->select('role_id')->where('id', $request->p_id)->first();
                        $getrole_id = DB::table('permission_type')->select('role_id', 'visible_type')->where('id', $request->p_id)->first();
                        //dd($getrole_id);
                        if ($getrole_id->role_id != $ofcrlevel) {
                            $updatepermission = DB::table('permission_type')->where('id', $request->p_id)->update(array('role_id' => $ofcrlevel));
                        }
                        if ($getrole_id->visible_type != $visible_type) {
                            $updatepermission = DB::table('permission_type')->where('id', $request->p_id)->update(array('visible_type' => $visible_type));
                        }
                        $data = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'status' => 1, 'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $where = array('id' => $request->p_id);
                        $result = $this->PM->updatetable('permission_type', $where, $data);                         
                        $data22 = array('permission_type_id' => $pname_id, "restriction_day" => $restriction_day, 'modified_by' => $d->id,  'modified_at' => date('Y-m-d H:i:s'));
                        $wherea = array('permission_type_id' => $request->pname_id, 'st_code' => $d->st_code);
                        $updaterestriction_day =DB::table('restriction_day_master') 
                        ->where('permission_type_id',$request->pname_id)
                        ->where('st_code',$d->st_code)
                        ->first();
                        if($updaterestriction_day == null){

                            $datarestriction = array('st_code' => $d->st_code, "permission_type_id"=>$pname_id,"restriction_day"=>$restriction_day, 'restriction_status' => '0', 'modified_at' => $d->id, 'added_at' => date('Y-m-d'),'modified_by'=>$d->id ,'created_at' => date('Y-m-d H:i:s'));
                            $resultdatarestriction = $this->PM->insertdata('restriction_day_master', $datarestriction);
                
                            
                         }
                         else{
                            $result22 = $this->PM->updatetable('restriction_day_master', $wherea, $data22);
                         }


                       // $result22 = $this->PM->updatetable('restriction_day_master', $wherea, $data22);
                        //                if (!empty($result) && $result != '') {
                        for ($i = 0; $i < count($document); $i++) {
                            $doc_id = 0;
                            $dname = 'NULL';
                            $chck = 0;
                            $format = 'NULL';
                            $reqaproval = 'NULL';
                            if (!empty($document[$i]['doc_id'])) {
                                $doc_id = strip_tags($document[$i]['doc_id']);
                            }
                            if (!empty($document[$i]['Dname'])) {
                                $dname = strip_tags($document[$i]['Dname']);
                            }
                            if (!empty($document[$i]['chck'])) {
                                $chck = strip_tags($document[$i]['chck']);
                            }
                            if (!empty($document[$i]['approvalauthority'])) {
                                $allauthority = $document[$i]['approvalauthority'];
                                $reqaproval = implode(',', $allauthority);
                            }
                            $getdocid = DB::table('permission_required_doc')->select('id')->where('id', $doc_id)->get()->toArray();
                            //                        print_r($getdocid);die;
                            if (!empty($getdocid)) {
                                $data1 = array('authority_type_id' => $reqaproval, 'fileserver_dir' => 'uploads1', 'doc_name' => $dname,  'st_code' => $d->st_code, 'required_status' => $chck,  'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                                $where1 = array('id' => $doc_id, 'permission_id' => $request->p_id);
                                $pdata = $this->PM->updatetable('permission_required_doc', $where1, $data1);
                            } else {
                                $data1 = array('authority_type_id' => $reqaproval, 'fileserver_dir' => 'uploads1', 'permission_id' => $request->p_id, 'permission_type_id' => $request->p_type_id, 'doc_name' => $dname, 'st_code' => $d->st_code, 'required_status' => $chck, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                $pdata = DB::table('permission_required_doc')->insert($data1);
                            }
                        }
                        return redirect()->back()->with('message', 'Successfully Updated');
                    }
                    return redirect()->back()->withErrors($validator, 'error');
                }
            } else {
                $p_id = Crypt::decryptString($request->id);

                $getpermsndetails = $this->PM->getpermsndetails($p_id);
                $DayPermissiontype = DB::table('restriction_day_master as a')
                    ->select('a.permission_type_id', 'a.restriction_day')
                    ->join('permission_type as m', 'a.permission_type_id', '=', 'm.permission_type_id')
                    //->where('authority_masters_id', $req->id)
                    ->where('m.id', $p_id)
                    ->where('a.st_code', $d->st_code)
                    ->where('m.st_code', $d->st_code)
                    ->get()->first();
                //dd($DayPermissiontype);

                $getrole = $this->PM->getofficerlevel();
                $getpermsndocdetails = $this->PM->getpermsndocdetails($p_id);
                $detailsdata = array();
                foreach ($getpermsndocdetails as $data) {
                    $getauthDetails = DB::table('permission_required_doc as a')
                        ->join('authority_type as c', DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"), ">", DB::raw("'0'"))
                        ->select(DB::raw("GROUP_CONCAT(DISTINCT c.name SEPARATOR ',') as 'auth_name'"))
                        ->where('a.id', $data->id)->first();

                    $detailsdata[] = array(
                        'doc_id' => $data->id,
                        'permission_id'  =>  $data->permission_id,
                        'permission_type_id'  => $data->permission_type_id,
                        'authority_type_id'  => $data->authority_type_id,
                        'doc_name'  => $data->doc_name,
                        'doc_size'  => $data->doc_size,
                        'st_code'  => $data->st_code,
                        'required_status'  => $data->required_status,
                        'file_name'  => $data->file_name,
                        'fileserver_dir'  => $data->fileserver_dir,
                        'status'  => $data->status,
                        'auth_name' => $getauthDetails->auth_name,
                    );
                }

                $getAuthType = $this->PM->getAuthType($d->st_code);
                return view('admin.ac.ceo.Permission.EditPermission')->with(array('user_data' => $d, 'getrole' => $getrole, 'showpage' => 'permission', 'getpermsndetails' => $getpermsndetails, 'DayPermissiontype' => $DayPermissiontype, 'maxDays' => $maxDays, 'getpermsndocdetails' => $detailsdata, 'getAuthType' => $getAuthType));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    //Autority Master
    public function AddAuthority()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $getAuthType = DB::table('authority_type')->select('name', 'id')->where('st_code', $d->st_code)->get()->toArray();
            return view('admin.ac.ceo.Permission.AddAuthority')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAuthType' => $getAuthType));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddAuthorityData(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


            try {



                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'SUCCESS';
                    $message['LogDescription'] = 'Authority Add Successfully';

                    LogNotification::LogInfo($message);
                }

                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphabetical character.',
                ];
                $validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->passes()) {

                    if (!empty($request->name)) {
                        $name = strip_tags($request->name);
                    }
                    $where = array('st_code' => $d->st_code, 'name' => $name);
                    $chckAuth = DB::table('authority_type')->where($where)->count();
                    if ($chckAuth == 0) {
                        $data = array('st_code' => $d->st_code, 'name' => $name, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                        $result = $this->PM->insertdata('authority_type', $data);
                        if ($result) {
                            return redirect('/acceo/viewauthority')->with('message', 'Successfully Added');
                        } else {
                            return redirect()->back()->with('message', 'Some error occured');
                        }
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered Authority Name is already Exist!')->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            } catch (Exception $e) {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'Failed';
                    $message['LogDescription'] = 'Something went to wrong ' . $e->getMessage();

                    LogNotification::LogInfo($message);
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }
    public function RemovePermsn(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $prmsn_id = $req->permsn_id;
            $doc_id = $req->doc_id;
            $where = array('id' => $doc_id, 'permission_id' => $prmsn_id);
            $res = $this->PM->deletedata('permission_required_doc', $where);
            if ($res == 1) {
                return 1;
            } else {
                return 0;
            }
            //            echo $req->permsn_id;die;
            //            $where = array('st_code' => $d->st_code);
            //            $getAllAuthorityData = $this->PM->getAllAuthorityTypeData($where);
            //            return view('admin.ac.ceo.Permission.ViewAuthority')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAllAuthorityData' => $getAllAuthorityData));
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewAuthority(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $where = array('st_code' => $d->st_code);
            $getAllAuthorityData = $this->PM->getAllAuthorityTypeData($where);
            return view('admin.ac.ceo.Permission.ViewAuthority')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAllAuthorityData' => $getAllAuthorityData));
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditAuthority(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            if (!empty($_POST['submit'])) {
                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphabetical character.',
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {

                    if (!empty($req->name)) {
                        $name = strip_tags($req->name);
                    }
                    $where = array('st_code' => $d->st_code, 'name' => $name);
                    $chckAuth = DB::table('authority_type')->where($where)->count();
                    if ($chckAuth == 0) {
                        $data = array('name' => $name, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $where = array('id' => strip_tags($req->auth_id));
                        $result = $this->PM->updatetable('authority_type', $where, $data);
                        //                    if ($result == 1) {
                        return redirect()->back()->with('message', 'Successfully Updated');
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered Authority Name is already Exist!')->withInput();
                    }
                    //                    } else {
                    //                        return redirect()->back()->with('message', 'Some error occured');
                    //                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error');
                }
            } else {
                $where = array('st_code' => $d->st_code, 'id' => Crypt::decryptString($req->id));
                $getAuthorityDetails = $this->PM->getAuthorityTypeDetails($where);
                return view('admin.ac.ceo.Permission.EditAuthority')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAuthorityDetails' => $getAuthorityDetails));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionCount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $where = array('st_code' => $d->st_code);
            $allrecord = $this->PM->totalPermissionReport($where);
            //            print_r($allrecord);die;
            return view('admin.ac.ceo.Permission.PermissionReport', ['user_data' => $d, 'allrecord' => $allrecord, 'ele_details' => $ele_details]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionCountDetails(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            //            echo $req->statusid;die;
            $where1 = array($d->st_code);
            if ($req->statusid != 'NULL') {
                if ($req->statusid == '22') {
                    $totalReportDetails = $this->PM->totalPermissionReportData($where1);
                } else if ($req->statusid == '01') {
                    $totalReportDetails = $this->PM->totalPendingReportDetails($where1);
                } else {
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
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $id = $req->id;
            $loc_id = $req->loc_id;
            $getNodaldetails = $this->PM->getNodaldetails($id, $d->id);
            $getRodetails = $this->PM->getRodetails($id, $req->status);
            //        print_r($getRodetails);die;
            //            $getDetailsview = $this->PM->getDetails($id, $loc_id);
            $getDetailsview = $this->PM->getDetails($id, $loc_id);
            if (empty($getDetailsview)) {
                $getDetailsview = $this->PM->getceopermsnDetails($id, $loc_id);
            } else {
                $getDetailsview = $this->PM->getInrtaDetails($id, $loc_id);
            }
            return view('admin.ac.ceo.Permission.ReportView')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails, 'ele_details' => $ele_details));
        } else {
            return redirect('/officer-login');
        }
    }

    public function generatePDF(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $data = ['title' => 'Welcome to HDTuto.com'];
            $id = $req->id;
            $data1 = explode('&', $id);
            $p_id = $data1[0];
            $status = $data1[1];
            $getDetailsview = $this->PM->getceopermsnDetails($p_id, $status);
            //            $getNodaldetails = $this->PM->getNodaldetails($id);
            $getRodetails = $this->PM->getCEOdetails($p_id);
            //            $getNodaldetails = $this->PM->getNodaldetails($id);
            //            $getRodetails = $this->PM->getRodetails($id);
            //   $pdf = PDF::loadView('admin.ac.ceo.permission.PermissionDetailsPDF', ['getDetails' => $getDetailsview]);
            $pdf = PDF::loadView('admin.ac.ceo.Permission.Reciept', ['getDetails' => $getDetailsview, 'getRodetails' => $getRodetails, 'ele_details' => $ele_details]);

            return $pdf->download('mypdf.pdf');
        } else {
            return redirect('/officer-login');
        }
    }

    public function AgentCreation(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            return view('admin.ac.ceo.Permission.Agent', ['user_data' => $d]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddAgent(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            try {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'SUCCESS';
                    $message['LogDescription'] = 'Add Agent Successfully';

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
                        'uname.regex' => 'Please Enter only alphabetical character.',
                        //                    'address.required' => ' Address field is required.',
                        //                    'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'mb.required' => ' Mobile no is required.',
                        'mb.digits' => 'Please Enter valid Mobile Number.',
                        //                    'dept.required' => 'Departemnt is required',
                        //                    'dept.regex' => 'Please Enter only alphabetical  character.',
                        'desig.required' => 'Designation is required Field',
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
                            //$pass = bcrypt(strip_tags($req->pass));
                            $pass = hash('sha256', $req->pass);
                        }

                        if (!empty($req->pincode)) {
                            $pin = bcrypt($req->pincode);
                        } else {
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
                            $data = array('two_step_pin' => $pin, 'parent_id' => $d->id, 'officername' => $mb, 'designation' => $designation, 'name' => $uname, 'st_code' => $d->st_code, 'Phone_no' => $mb, 'email' => $email, 'role_id' => '23', 'officerlevel' => 'CEO-OFFICE', 'password' => $pass, 'password_flag' => 1, 'pass_flag' => 1);
                            $result = $this->PM->insertdata('officer_login', $data);
                            if ($result == 1) {
                                return redirect('/acceo/viewagent')->with('message', 'Successfully Created');
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

            } catch (Exception $e) {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'Failed';
                    $message['LogDescription'] = 'Something went to wrong ' . $e->getMessage();

                    LogNotification::LogInfo($message);
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewAgent(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $where = array('st_code' => $d->st_code);
            $getAgentList = $this->PM->getAgentList($where);
            return view('admin.ac.ceo.Permission.ViewAgentList', ['user_data' => $d], ['getAgentList' => $getAgentList]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditAgent(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
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
                    'uname.regex' => 'Please Enter only alphabetical character.',
                    //                    'address.required' => ' Address field is required.',
                    //                    'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'mb.required' => ' Mobile no is required.',
                    'mb.digits' => 'Please Enter valid Mobile Number.',
                    //                    'dept.required' => 'Departemnt is required',
                    //                    'dept.regex' => 'Please Enter only alphabetical  character.',
                    'desig.required' => 'Designation is required Field',
                    'desig.regex' => 'Please Enter only alphabetical character.',
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
                    /*  if ($chckloc == 0) { */
                    $data = array('designation' => $designation, 'name' => $uname, 'Phone_no' => $mb, 'email' => $email);
                    $where = array('id' => $req->id, 'role_id' => $req->role_id);
                    $update = $this->PM->updatetable('officer_login', $where, $data);
                    return redirect()->back()->with('message', 'Successfully Updated');
                    /* } else {
                        return redirect()->back()->with('chckmessage', 'Entered  mobile no is already Exist!')->withInput();
                    } */
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            } else {
                $getAgentDetails = $this->PM->getAgentDetails(Crypt::decryptString($req->id));
                //                print_r($getAgentDetails);die;
                return view('admin.ac.ceo.Permission.EditAgentList', ['user_data' => $d], ['getAgentList' => $getAgentDetails]);
            }
        } else {
            return redirect('/officer-login');
        }
    }
    public function EditAgentStatus(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $data = explode('#', $req->status);
            $status = $data[0];
            $id = $data[1];
            if ($status == 1) {
                $where = array('id' => $id, 'role_id' => '23');
                $cond = array('is_active' => '0');
                $res = $this->PM->updatetable('officer_login', $where, $cond);
                if ($res == 1) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $where = array('id' => $id, 'role_id' => '23');
                $cond = array('is_active' => '1');
                $res = $this->PM->updatetable('officer_login', $where, $cond);
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

    public function EditRestriction(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $restrictdata = DB::table('restriction_master')->select('*')->where('st_code', $d->st_code)->first();
            //            print_r($restrictdata);die;
            return view('admin.ac.ceo.Permission.EditdateRestriction', ['user_data' => $d, 'restrictdata' => $restrictdata]);
        } else {
            return redirect('/officer-login');
        }
    }
    public function updatedaterestriction(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            if (isset($_POST['daterestriction'])) {
                $daterestrict = $_POST['daterestriction'];
            } else {
                $daterestrict = 0;
            }
            $where = array('st_code' => $d->st_code);
            $cond = array('restriction_status' => $daterestrict, 'modified_at' => date('Y-m-d H:i:s'), 'modified_by' => $d->id);
            $update = $this->PM->updatetable('restriction_master', $where, $cond);
            return redirect()->back()->with('message', 'Successfully Updated');
        } else {
            return redirect('/officer-login');
        }
    }

    //Offline permission ccd
    public function OfflinePermission(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $getceodetails = $this->PM->getLoginUserdetails($d->id);
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
                if ($d->role_id == 4) {
                    $permission_type = DB::table('permission_type as a')
                        ->join('permission_master as m', 'm.id', '=', 'a.permission_type_id')
                        ->select('m.permission_name as pname', 'a.*', 'a.id as permsn_id', 'm.officer_role_id')
                        ->where('a.status', '1')
                        ->where('role_id', $d->role_id)
                        ->where('a.st_code', $d->st_code)->get()->toArray();
                } else {
                    $permission_type = DB::table('permission_type as a')
                        ->join('permission_master as m', 'm.id', '=', 'a.permission_type_id')
                        ->select('m.permission_name as pname', 'a.*', 'a.id as permsn_id', 'm.officer_role_id')
                        ->where('a.status', '1')
                        ->where('role_id', 4)
                        ->where('a.st_code', $d->st_code)->get()->toArray();
                }


                $loccond = array('st_code' => $d->st_code);
                $getAllDist = $this->PM->getAllDist($d->st_code);
                //                $getAllLocation = $this->PM->getAllLocation($loccond);
                $getAllUserType = $this->PM->getAllUserType();
                $allParty = DB::table('m_party')->select('*')
                    ->where('CCODE', '<>', '1180')
                    ->where('PARTYSYM', '<>', '-1')
                    ->where('deleteflag', 'N')
                    ->orderBy('PARTYNAME')->get()->toArray();

                return view('admin.ac.ceo.Permission.OfflinePermissionApply')->with(array('user_data' => $d, 'getAllDist' => $getAllDist, 'getrodetails' => $getceodetails, 'showpage' => 'permission', 'permission_type' => $permission_type, 'getAllUserType' => $getAllUserType, 'allParty' => $allParty));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function getUserDetails(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
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

    public function UserDetails(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $time = Carbon::now()->timestamp;
            $rules = [];
            $ptypeid = null;
            $messages = [];
            $getDistName = getdistrictbydistrictno($d->st_code, $d->dist_no);
            $getStateName = getstatebystatecode($d->st_code);
            $document = $req->input('doc');

            try {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'SUCCESS';
                    $message['LogDescription'] = 'Offline Permission Add Successfully';

                    LogNotification::LogInfo($message);
                }

                if (!empty($req->permission_type) && $req->permission_type != 0) {

                    $ptype = $req->permission_type;
                    $ptypeid = explode('#', $ptype);
                    if (!empty($ptypeid) && $ptypeid[1] == 3 || $ptypeid[1] == 6) {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                            'user_email' => 'required|email:rfc,dns',
                            'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'gender' => 'required',
                            'dob' => 'required|date|before:-18 years',
                            'address' => 'required|not_regex:/([<>@$%?]+)/',
                            'state' => 'required',
                            //                            'district' => 'required|not_in:0',
                            //                            'ac' => 'required|not_in:0',
                            //                            'pc' => 'required|not_in:0',
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
                            'user_email.required' => ' Email field is required.',
                            'user_email.email' => 'Please Enter valid Email',
                            'fathers_name.required' => ' This is required.',
                            'fathers_name.regex' => 'Please Enter only alphabetical  character.',
                            'user_name.required' => 'Name is required',
                            'user_name.regex' => 'Please Enter only alphabetical  character.',
                            'gender.required' => 'Gender is required Field',
                            'dob.required' => 'DOB is required',
                            'address.required' => 'Address is required',
                            'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                            'state.required' => 'Required field',
                            'district.required' => 'Required field',
                            'ac.required' => 'Required field',
                            'pc.required' => 'Required field',
                            'police_station.required' => 'police_station is Required',
                            'permission_type.required' => 'permission_type is Required',
                            'user_type' => 'user_type is Required field',
                            'stdate.required' => 'Strat date is Required',
                            'enddate.required' => 'End date is Required',
                            'subdate.required' => 'Submission date is Required',
                            'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                            'political_party.required' => 'Please Select Political Party'
                        ];
                    } else if (!empty($ptypeid) && $ptypeid[1] == 8) {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                            'user_email' => 'required|email:rfc,dns',
                            'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'gender' => 'required',
                            'dob' => 'required|date|before:-18 years',
                            'address' => 'required|not_regex:/([<>@$%?]+)/',
                            'state' => 'required',
                            //                            'district' => 'required|not_in:0',
                            //                            'ac' => 'required|not_in:0',
                            //                            'pc' => 'required|not_in:0',
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
                            'user_email.required' => ' Email field is required.',
                            'user_email.email' => 'Please Enter valid Email',
                            'fathers_name.required' => 'Fathers Name is required.',
                            'fathers_name.regex' => 'Please Enter only alphabetical  character.',
                            'user_name.required' => 'Name is required',
                            'user_name.regex' => 'Please Enter only alphabetical  character.',
                            'gender.required' => 'Gender is required Field',
                            'dob.required' => 'DOB is required',
                            'address.required' => 'Address is required',
                            'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                            'state.required' => 'State is Required field',
                            'district.required' => 'District is Required field',
                            'pc.required' => 'PC is Required field',
                            //                            'ac_no.required' => 'AC Required field',
                            //                            'police_station.required' => 'Police Station is Required field',
                            'permission_type.required' => 'Permission Type is Required field',
                            'user_type' => 'User Type Required field',
                            'stdate.required' => 'Strat date is Required',
                            'enddate.required' => 'End date is Required',
                            'subdate.required' => 'Submission date is Required',
                            'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                            'political_party.required' => 'Please Select Political Party'
                        ];
                    } else {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                            'user_email' => 'required|email:rfc,dns',
                            'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'gender' => 'required',
                            'dob' => 'required|date|before:-18 years',
                            'address' => 'required|not_regex:/([<>@$%?]+)/',
                            'state' => 'required',
                            //                            'district' => 'required|not_in:0',
                            //                            'ac' => 'required|not_in:0',
                            //                            'pc' => 'required|not_in:0',
                            //                            'police_station' => 'required|not_in:0',
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
                            'user_email.required' => ' Email field is required.',
                            'user_email.email' => 'Please Enter valid Email',
                            'fathers_name.required' => ' This is required.',
                            'fathers_name.regex' => 'Please Enter only alphabetical  character.',
                            'user_name.required' => 'Name is required',
                            'user_name.regex' => 'Please Enter only alphabetical  character.',
                            'gender.required' => 'Gender is required Field',
                            'dob.required' => 'DOB is required',
                            'address.required' => 'Address is required',
                            'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                            'state.required' => 'Required field',
                            'district.required' => 'Required field',
                            'ac.required' => 'Required field',
                            'police_station.required' => 'police Station is Required',
                            'permission_type.required' => 'Permission Type is Required ',
                            //'location' => 'Location is Required ',
                            'user_type' => 'User Type Required',
                            'stdate.required' => 'Strat date is Required',
                            'enddate.required' => 'End date is Required',
                            'subdate.required' => 'Submission date is Required',
                            'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                            'political_party.required' => 'Please Select Political Party'
                        ];

                        if ($req->location == 'other') {
                            $rules = [
                                'user_mb' => 'required|numeric|digits:10',
                                'user_email' => 'required|email:rfc,dns',
                                'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                                'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                                'gender' => 'required',
                                'dob' => 'required|date|before:-18 years',
                                'address' => 'required|not_regex:/([<>@$%?]+)/',
                                'state' => 'required',
                                //                                'district' => 'required|not_in:0',
                                //                                'ac' => 'required|not_in:0',
                                //                                'pc' => 'required|not_in:0',
                                //                                'police_station' => 'required|not_in:0',
                                'permission_type' => 'required|not_in:0',
                                //'location' => 'required|not_in:0',
                                'user_type' => 'required|not_in:0',
                                'stdate' => 'required',
                                'enddate' => 'required',
                                'subdate' => 'required',
                                'permsndoc.*.p_doc' => 'mimes:pdf',
                                'political_party' => 'required|not_in:0',
                                //'other' => 'required',
                            ];
                            $messages = [
                                'user_mb.required' => ' Mobile field is required.',
                                'user_mb.digits' => 'Please Enter valid Mobile Number.',
                                'user_email.required' => ' Email field is required.',
                                'user_email.email' => 'Please Enter valid Email',
                                'fathers_name.required' => ' This is required.',
                                'fathers_name.regex' => 'Please Enter only alphabetical  character.',
                                'user_name.required' => 'Name is required',
                                'user_name.regex' => 'Please Enter only alphabetical  character.',
                                'gender.required' => 'Gender is required Field',
                                'dob.required' => 'DOB is required',
                                'address.required' => 'Address is required',
                                'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                                'state.required' => 'Required field',
                                'district.required' => 'Required field',
                                'ac.required' => 'Required field',
                                'police_station.required' => 'police Station is Required',
                                'permission_type.required' => 'Permission Type is Required ',
                                //'location' => 'Location is Required ',
                                'user_type' => 'User Type Required',
                                'stdate.required' => 'Strat date is Required',
                                'enddate.required' => 'End date is Required',
                                'subdate.required' => 'Submission date is Required',
                                'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                                'political_party.required' => 'Please Select Political Party',
                                //'other.required' => 'Please Enter Other location name',
                            ];
                        }
                    }
                } else {
                    $rules = [
                        'user_mb' => 'required|numeric|digits:10',
                        'user_email' => 'required|email:rfc,dns',
                        'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'gender' => 'required',
                        'dob' => 'required|date|before:-18 years',
                        'address' => 'required|not_regex:/([<>@$%?]+)/',
                        'state' => 'required',
                        //                        'district' => 'required|not_in:0',
                        //                        'ac' => 'required|not_in:0',
                        //                        'pc' => 'required|not_in:0',
                        //                        'police_station' => 'required|not_in:0',
                        'permission_type' => 'required|not_in:0',
                        //'location' => 'required|not_in:0',
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
                        'user_email.required' => ' Email field is required.',
                        'user_email.email' => 'Please Enter valid Email',
                        'fathers_name.required' => ' This is required.',
                        'fathers_name.regex' => 'Please Enter only alphabetical  character.',
                        'user_name.required' => 'Name is required',
                        'user_name.regex' => 'Please Enter only alphabetical  character.',
                        'gender.required' => 'Gender is required Field',
                        'dob.required' => 'DOB is required',
                        'address.required' => 'Address is required',
                        'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'state.required' => 'Required field',
                        'district.required' => 'Required field',
                        'ac.required' => 'Required field',
                        'police_station.required' => 'police Station is Required',
                        'permission_type.required' => 'Permission Type is Required ',
                        ///'location' => 'Location is Required ',
                        'user_type' => 'User Type Required',
                        'stdate.required' => 'Strat date is Required',
                        'enddate.required' => 'End date is Required',
                        'subdate.required' => 'Submission date is Required',
                        'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                        'political_party.required' => 'Please Select Political Party'
                    ];

                    if ($req->location == 'other') {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                            'user_email' => 'required|email:rfc,dns',
                            'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'gender' => 'required',
                            'dob' => 'required|date|before:-18 years',
                            'address' => 'required|not_regex:/([<>@$%?]+)/',
                            'state' => 'required',
                            //                            'district' => 'required|not_in:0',
                            //                            'ac' => 'required|not_in:0',
                            //                            'pc' => 'required|not_in:0',
                            //                            'police_station' => 'required|not_in:0',
                            'permission_type' => 'required|not_in:0',
                            // 'location' => 'required|not_in:0',
                            'user_type' => 'required|not_in:0',
                            'stdate' => 'required',
                            'enddate' => 'required',
                            'subdate' => 'required',
                            'permsndoc.*.p_doc' => 'mimes:pdf',
                            'political_party' => 'required|not_in:0',
                            // 'other' => 'required',
                        ];
                        $messages = [
                            'user_mb.required' => ' Mobile field is required.',
                            'user_mb.digits' => 'Please Enter valid Mobile Number.',
                            'user_email.required' => ' Email field is required.',
                            'user_email.email' => 'Please Enter valid Email',
                            'fathers_name.required' => ' This is required.',
                            'fathers_name.regex' => 'Please Enter only alphabetical  character.',
                            'user_name.required' => 'Name is required',
                            'user_name.regex' => 'Please Enter only alphabetical  character.',
                            'gender.required' => 'Gender is required Field',
                            'dob.required' => 'DOB is required',
                            'address.required' => 'Address is required',
                            'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                            'state.required' => 'Required field',
                            'district.required' => 'Required field',
                            'ac.required' => 'Required field',
                            'police_station.required' => 'police Station is Required',
                            'permission_type.required' => 'Permission Type is Required ',
                            //'location' => 'Location is Required ',
                            'user_type' => 'User Type Required',
                            'stdate.required' => 'Strat date is Required',
                            'enddate.required' => 'End date is Required',
                            'subdate.required' => 'Submission date is Required',
                            'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                            'political_party.required' => 'Please Select Political Party',
                            //'other.required' => 'Please Enter Other location name',
                        ];
                    }
                }
                //                print_r($rules);die;
                $type = 'Permission';
                $user_mb = strip_tags($req->user_mb);
                $user_name = strip_tags($req->user_name);
                $user_email = strip_tags($req->user_email);
                $fathers_name = strip_tags($req->fathers_name);
                $user_type = strip_tags($req->user_type);
                $gender = strip_tags($req->gender);
                $dob = date('Y-m-d', strtotime(strip_tags($req->dob)));
                $state = strip_tags($req->state);
                $district = strip_tags($req->district);
                //                $ac = strip_tags($req->ac);
                //                $police_station = strip_tags($req->police_station);
                if (!empty($req->ac)) {
                    $ac = strip_tags($req->ac);
                } else {
                    $ac = '0';
                }
                if (!empty($req->police_station)) {
                    $police_station = strip_tags($req->police_station);
                } else {
                    $police_station = '0';
                }
                $address = strip_tags($req->address);
                if (!empty($ptypeid[0])) {
                    $permission_type = strip_tags($ptypeid[0]);
                }
                if (!empty($req->location)) {
                    $location = strip_tags($req->location);
                } else {
                    $location = '0';
                }
                $party = strip_tags($req->political_party);
                //                date('Y-m-d H:i:s', strtotime($date)); 
                $stdate = date('Y-m-d H:i:s', strtotime(strip_tags($req->stdate)));
                $enddate = date('Y-m-d H:i:s', strtotime(strip_tags($req->enddate)));
                $subdate = date('Y-m-d H:i:s', strtotime(strip_tags($req->subdate)));
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $other = 'NULL';
                    if (!empty($req->other)) {
                        $other = strip_tags($req->other);
                    }
                    $doc_data = $req->file('permsndoc');
                    $doc_name = '';

                    if (!empty($doc_data)) {
                        sort($doc_data);
                        for ($i = 0; $i <= count($doc_data); $i++) {
                            if (!empty($doc_data[$i])) {
                                $doc_name .= 'uploads1/userdoc/permission-document/' . $d->election_id . '/' . $d->st_code . '/' . preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $doc_data[$i]['p_doc']->getClientOriginalName()) . ',';
                                $format = preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $doc_data[$i]['p_doc']->getClientOriginalName());
                                $destinationPath3 = public_path('/uploads1/userdoc/permission-document/' . $d->election_id . '/' . $d->st_code . '/');
                                $doc_data[$i]['p_doc']->move($destinationPath3, $format);
                            }
                        }
                    }
                    $getuserloginid = DB::table('user_login')->select('id', 'party_id', 'role_id', 'permission_request_status')->where('mobile', $user_mb)->get()->first();
                    $getUserdata = DB::table('user_data')->where('mobileno', $user_mb)->count();
                    if (!empty($getuserloginid)) {
                        //                        echo 'find';die;
                        try {
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
                                $user_data = array('user_login_id' => strip_tags($getuserloginid->id), 'party_id' => $party, 'name' => $user_name, 'fathers_name' => $fathers_name, 'email' => $user_email, 'mobileno' => $user_mb, 'gender' => $gender, 'dob' => $dob, 'address' => $address, 'state_id' => $state, 'district_id' => $district, 'ac_id' => $ac, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'election_id' => $d->election_id);
                                $result = $this->PM->insertdata('user_data', $user_data);
                            }

                            $permission_data = array('fileserver_dir' => 'uploads1', 'user_id' => strip_tags($getuserloginid->id), 'party_id' => $party, 'st_code' => $state, 'dist_no' => $district, 'ac_no' => $ac, 'permission_type_id' => $permission_type, 'required_files' => $doc_name, 'location_id' => $location, 'Other_location' => $other, 'date_time_start' => $stdate, 'date_time_end' => $enddate, 'assigned_police_st_id' => $police_station, 'approved_status' => '0', 'user_created_by' => '2', 'added_at' => $subdate, 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id, 'election_id' => $d->election_id);
                            $p_data = DB::table('permission_request')->insertGetId($permission_data);
                            if (!empty($p_data) && $p_data != '') {
                                $loginprequest = array('permission_request_status' => '1');
                                $wherelog = array('id' => strip_tags($getuserloginid->id));
                                $updatelog = $this->PM->updatetable('user_login', $wherelog, $loginprequest);

                                $referenceid = array('reference_id' => $ele_details[0]->ELECTION_ID . $ele_details[0]->CONST_TYPE . $p_data);
                                $whereid = array('id' => $p_data);
                                $updatepermsnreq = $this->PM->updatetable('permission_request', $whereid, $referenceid);
                                if (!empty($document) && count($document) != 0) {
                                    foreach ($document as $docdata) {
                                        $data1 = DB::table('permission_required_doc')
                                            ->select('authority_type_id')
                                            ->where('id', $docdata['doc_id'])
                                            ->where('st_code', $d->st_code)
                                            ->first();
                                        $data6 = DB::table('officer_login')
                                            ->select('parent_id', 'officerlevel')
                                            ->where('id', $d->id)
                                            ->where('st_code', $d->st_code)
                                            ->get();



                                        if ($data6[0]->officerlevel == 'CEO-OFFICE') {

                                            $created = $data6[0]->parent_id;
                                        } else {
                                            $created = $d->id;
                                        }
                                        $allauthid = explode(',', $data1->authority_type_id);
                                        //                            dd($allauthid);
                                        if (count($allauthid) != 0) {
                                            foreach ($allauthid as $allauthdata) {
                                                //                                            dd($allauthdata);
                                                if ($allauthdata != 'cand01') {
                                                    $nodaldetails = DB::table('authority_masters as a')
                                                        ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                        ->select('a.id', 'a.name')
                                                        ->where('a.st_code', $state)
                                                        ->where('b.created_by', $created)
                                                        ->where(array('b.dist_no' => 0, 'b.pc_no' => 0, 'b.ac_no' => 0, 'b.created_by' => $created, 'b.is_active' => 1))
                                                        ->where('b.auth_type_id', $allauthdata)
                                                        ->groupBy('b.authority_masters_id')
                                                        ->first();
                                                    dd($nodaldetails);
                                                    if (!empty($nodaldetails)) {
                                                        //                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                                        $nodaldata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_data, 'authority_id' => $nodaldetails->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                        $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                                        if (!empty($getStateName)) {
                                                            $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails->id)->first();
                                                            $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getStateName->ST_NAME;

                                                            //                                            if (!empty($fcm_id)) {
                                                            ////                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
                                                            //                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails->id);
                                                            //                                            }
                                                        }
                                                        //                                        }
                                                    }
                                                } else {
                                                    $nodaldata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_data, 'authority_id' => 'cand01', 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                    $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
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



                                        if ($data6[0]->officerlevel == 'CEO-OFFICE') {

                                            $created = $data6[0]->parent_id;
                                        } else {
                                            $created = $d->id;
                                        }
                                        if (!empty($data1) && count($data1) != 0) {
                                            foreach ($data1 as $doc_auth) {
                                                $allauthid = explode(',', $doc_auth->authority_type_id);
                                                $nodaldetails = DB::table('authority_masters as a')
                                                    ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                    ->select('a.id', 'a.name')
                                                    ->where('a.st_code', $state)
                                                    //->where('b.created_by', $created)
                                                    ->where(array('b.dist_no' => 0, 'b.pc_no' => 0, 'b.ac_no' => 0, 'b.created_by' => $created, 'b.is_active' => 1))
                                                    ->whereIn('b.auth_type_id', $allauthid)
                                                    ->groupBy('b.authority_masters_id')
                                                    ->get()->toArray();

                                                if (!empty($nodaldetails)) {
                                                    for ($i = 0; $i < count($nodaldetails); $i++) {
                                                        $nodaldata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                        $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                                        if (!empty($getStateName)) {
                                                            $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();
                                                            $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getStateName->ST_NAME;

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
                                } else {

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



                                    if ($data6[0]->officerlevel == 'CEO-OFFICE') {

                                        $created = $data6[0]->parent_id;
                                    } else {
                                        $created = $d->id;
                                    }
                                    if (!empty($data1) && count($data1) != 0) {
                                        foreach ($data1 as $doc_auth) {
                                            $allauthid = explode(',', $doc_auth->authority_type_id);
                                            $nodaldetails = DB::table('authority_masters as a')
                                                ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                ->select('a.id', 'a.name')
                                                ->where('a.st_code', $state)
                                                ->where('b.created_by', $created)
                                                ->where(array('b.dist_no' => 0, 'b.pc_no' => 0, 'b.ac_no' => 0, 'b.created_by' => $created, 'b.is_active' => 1))
                                                ->whereIn('b.auth_type_id', $allauthid)
                                                ->groupBy('b.authority_masters_id')
                                                ->get()->toArray();

                                            if (!empty($nodaldetails)) {
                                                for ($i = 0; $i < count($nodaldetails); $i++) {
                                                    $nodaldata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                    $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                                    if (!empty($getStateName)) {
                                                        $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();
                                                        $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getStateName->ST_NAME;

                                                        //                                            if (!empty($fcm_id)) {
                                                        ////                                          SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
                                                        //                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
                                                        //                                            }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                DB::commit();
                                if ($user_mb != '') {
                                    if ($user_type == 2) {
                                        $mob_message = "Your permission request has been received with the CEO, to track the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                                    } else {
                                        $mob_message = "Your permission request has been received with the DEO, to track the status visit website- suvidha.eci.gov.in";
                                    }
                                    $response = SmsgatewayHelper::gupshup($user_mb, $mob_message);
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
                                return redirect()->back()->with('message', 'Successfully Permission applied with Reference Id ' . $ele_details[0]->ELECTION_ID . $ele_details[0]->CONST_TYPE . $p_data);
                            } else {
                                return redirect()->back()->with('message', 'Permission not applied');
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            return $e;
                        }
                    } else {
                        //                         echo 'notfind';die;
                        try {
                            DB::beginTransaction();
                            $login_data = array('name' => $user_name, 'email' => $user_email, 'party_id' => $party, 'mobile' => $user_mb, 'role_id' => $user_type, 'permission_request_status' => '1', 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'election_id' => $d->election_id);

                            $insertid = DB::table('user_login')->insertGetId($login_data);
                            if (!empty($insertid) && $insertid != '') {
                                $user_data = array('user_login_id' => $insertid, 'name' => $user_name, 'party_id' => $party, 'fathers_name' => $fathers_name, 'email' => $user_email, 'mobileno' => $user_mb, 'gender' => $gender, 'dob' => $dob, 'address' => $address, 'state_id' => $state, 'district_id' => $district, 'ac_id' => $ac, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'election_id' => $d->election_id);
                                $result = $this->PM->insertdata('user_data', $user_data);
                                if ($result == 1) {
                                    $permission_data = array('fileserver_dir' => 'uploads1', 'user_id' => $insertid, 'party_id' => $party, 'st_code' => $state, 'dist_no' => $district, 'ac_no' => $ac, 'permission_type_id' => $permission_type, 'required_files' => $doc_name, 'location_id' => $location, 'Other_location' => $other, 'date_time_start' => $stdate, 'date_time_end' => $enddate, 'assigned_police_st_id' => $police_station, 'approved_status' => '0', 'user_created_by' => '2', 'added_at' => $subdate, 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id, 'election_id' => $d->election_id);
                                    $p_data = DB::table('permission_request')->insertGetId($permission_data);
                                    if (!empty($p_data) && $p_data != '') {
                                        $loginprequest = array('permission_request_status' => '1');
                                        $wherelog = array('id' => strip_tags($insertid));
                                        $updatelog = $this->PM->updatetable('user_login', $wherelog, $loginprequest);

                                        $referenceid = array('reference_id' => $ele_details[0]->ELECTION_ID . $ele_details[0]->CONST_TYPE . $p_data);
                                        $whereid = array('id' => $p_data);
                                        $updatepermsnreq = $this->PM->updatetable('permission_request', $whereid, $referenceid);
                                        if (!empty($document) && count($document) != 0) {
                                            foreach ($document as $docdata) {
                                                $data1 = DB::table('permission_required_doc')
                                                    ->select('authority_type_id')
                                                    ->where('id', $docdata['doc_id'])
                                                    ->where('st_code', $d->st_code)
                                                    ->first();
                                                $data6 = DB::table('officer_login')
                                                    ->select('parent_id', 'officerlevel')
                                                    ->where('id', $d->id)
                                                    ->where('st_code', $d->st_code)
                                                    ->get();



                                                if ($data6[0]->officerlevel == 'CEO-OFFICE') {

                                                    $created = $data6[0]->parent_id;
                                                } else {
                                                    $created = $d->id;
                                                }
                                                $allauthid = explode(',', $data1->authority_type_id);
                                                if (count($allauthid) != 0) {
                                                    foreach ($allauthid as $allauthdata) {
                                                        if ($allauthdata != 'cand01') {
                                                            $nodaldetails = DB::table('authority_masters as a')
                                                                ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                                ->select('a.id', 'a.name')
                                                                ->where('a.st_code', $state)
                                                                ->where('b.created_by', $created)
                                                                ->where(array('b.dist_no' => 0, 'b.pc_no' => 0, 'b.ac_no' => 0, 'b.created_by' => $created, 'b.is_active' => 1))
                                                                ->where('b.auth_type_id', $allauthdata)
                                                                ->groupBy('b.authority_masters_id')
                                                                ->first();

                                                            //                                    echo '<pre/>';
                                                            //                                    print_r($nodaldetails);die;
                                                            //                                    if(!empty($ptypeid))
                                                            //                                    {
                                                            //                                        if ($ptypeid[1] != 3 && $ptypeid[1] != 6 && $ptypeid[1] != 8) {
                                                            if (!empty($nodaldetails)) {
                                                                //                                                for ($i = 0; $i < count($nodaldetails); $i++) {
                                                                $nodaldata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_data, 'authority_id' => $nodaldetails->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                                $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                                                if (!empty($getStateName)) {
                                                                    $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails->id)->first();
                                                                    $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getStateName->ST_NAME;

                                                                    //                                            if (!empty($fcm_id)) {
                                                                    ////                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
                                                                    //                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails->id);
                                                                    //                                            } 
                                                                }

                                                                //                                                }
                                                            }
                                                        } else {
                                                            $nodaldata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_data, 'authority_id' => 'cand01', 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                                        }
                                                    }
                                                }
                                                //                                        }
                                                //                                    }
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



                                            if ($data6[0]->officerlevel == 'CEO-OFFICE') {

                                                $created = $data6[0]->parent_id;
                                            } else {
                                                $created = $d->id;
                                            }
                                            if (!empty($data1) && count($data1) != 0) {
                                                foreach ($data1 as $doc_auth) {
                                                    $allauthid = explode(',', $doc_auth->authority_type_id);
                                                    $nodaldetails = DB::table('authority_masters as a')
                                                        ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                        ->select('a.id', 'a.name')
                                                        ->where('a.st_code', $state)
                                                        ->where('b.created_by', $created)
                                                        ->where(array('b.dist_no' => 0, 'b.pc_no' => 0, 'b.ac_no' => 0, 'b.created_by' => $created, 'b.is_active' => 1))
                                                        ->whereIn('b.auth_type_id', $allauthid)
                                                        ->groupBy('b.authority_masters_id')
                                                        ->get()->toArray();

                                                    if (!empty($nodaldetails)) {
                                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                                            $nodaldata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                                            if (!empty($getStateName)) {
                                                                $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();
                                                                $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getStateName->ST_NAME;

                                                                //                                            if (!empty($fcm_id)) {
                                                                ////                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
                                                                //                                                SendNotification::send_notification_fcm('New Permission received', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
                                                                //                                            }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else {

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



                                            if ($data6[0]->officerlevel == 'CEO-OFFICE') {

                                                $created = $data6[0]->parent_id;
                                            } else {
                                                $created = $d->id;
                                            }
                                            if (!empty($data1) && count($data1) != 0) {
                                                foreach ($data1 as $doc_auth) {
                                                    $allauthid = explode(',', $doc_auth->authority_type_id);
                                                    $nodaldetails = DB::table('authority_masters as a')
                                                        ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                                                        ->select('a.id', 'a.name')
                                                        ->where('a.st_code', $state)
                                                        ->where('b.created_by', $created)
                                                        ->where(array('b.dist_no' => 0, 'b.pc_no' => 0, 'b.ac_no' => 0, 'b.created_by' => $created, 'b.is_active' => 1))
                                                        ->whereIn('b.auth_type_id', $allauthid)
                                                        ->groupBy('b.authority_masters_id')
                                                        ->get()->toArray();

                                                    if (!empty($nodaldetails)) {
                                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                                            $nodaldata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                                            if (!empty($getStateName)) {
                                                                $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();
                                                                $msg = 'New permission received has been received at ' . Carbon::now() . ' From ' . $getStateName->ST_NAME;

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
                                        if ($user_mb != '') {
                                            if ($user_type == 2) {
                                                $mob_message = "Your permission request has been received with the CEO, to track the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                                            } else {
                                                $mob_message = "Your permission request has been received with the DEO, to track the status visit website- suvidha.eci.gov.in";
                                            }
                                            $response = SmsgatewayHelper::gupshup($user_mb, $mob_message);
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
                                        return redirect()->back()->with('message', 'Successfully permission applied with Reference Id ' . $ele_details[0]->ELECTION_ID . $ele_details[0]->CONST_TYPE . $p_data);
                                    } else {
                                        return redirect()->back()->with('message', 'Permission not applied');
                                    }
                                } else {
                                    return redirect()->back()->with('message', 'Some Error Occured!!!');
                                }
                            } else {
                                return redirect()->back()->with('message', 'Some Error Occured!!');
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            return $e;
                        }
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }

                //        $permission_type=DB::table('permission_type')->where('status','1')->get();
                //        return view('admin.ro.Permission.ApplyOfflinePermission')->with(array('user_data'=>$d,'showpage'=>'permission','permission_type'=>$permission_type,'user_details_police'=>$user_details_police));

            } catch (Exception $e) {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'Failed';
                    $message['LogDescription'] = 'Something went to wrong ' . $e->getMessage();

                    LogNotification::LogInfo($message);
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function getalldistrict(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            $stcode = $req->stcode;
            $getAllDist = $this->PM->getAllDist($d->st_code);
            //            print_r($getAllDist);die;
            return $getAllDist;
        } else {
            return redirect('/officer-login');
        }
    }
    public function AllPermissionRequest()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $permissionDetails = $this->PM->getPermissionDetails($d->id, $d->st_code, $d->role_id);
            return view('admin.ac.ceo.Permission.AllpermissionRequest')->with(array('user_data' => $d, 'permissionDetails' => $permissionDetails, 'ele_details' => $ele_details));
        } else {
            return redirect('/officer-login');
        }
    }

    public function getpermissiondetails(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $data = explode('&', $req->id);
            $id = $data[0];
            $status = $data[1];
            $cancel = $data[2];
            $locid = $data[3];

            $getDetailsview = $this->PM->getceopermsnDetails($id, $locid);
            //$permissionDetails = $this->PM->getPermissionDetails($d->id, $d->st_code, $d->role_id);
            $getNodaldetails = $this->PM->getNodaldetails($id, $d->id);
            $getRodetails = $this->PM->getCEOdetails($id);
            $canddoc = $data = DB::table('permission_assigned_auth as a')
                ->select('a.*')
                ->where('a.permission_request_id', $id)
                ->where('a.authority_id', 'cand01')
                ->get()->toArray();
            //dd($getNodaldetails);

            if ($status == 0 && $cancel == 0) {
                return view('admin.ac.ceo.Permission.Permissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'canddoc' => $canddoc, 'ele_details' => $ele_details));
            } else if ($status == 1 && $cancel == 0) {
                return view('admin.ac.ceo.Permission.Permissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'canddoc' => $canddoc, 'ele_details' => $ele_details));
            } else if ($status == 2  || ($cancel == 1 || $cancel == 0)) {
                return view('admin.ac.ceo.Permission.AcceptPermissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'canddoc' => $canddoc, 'getRodetails' => $getRodetails, 'ele_details' => $ele_details));
            } else if ($status == 3 || ($cancel == 1 || $cancel == 0)) {
                return view('admin.ac.ceo.Permission.RejectPermissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'canddoc' => $canddoc, 'getRodetails' => $getRodetails, 'ele_details' => $ele_details));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function UploadNodaldoc(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
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
                    $scanPhysicalDoc = 'uploads1/Nodal-Uploaddocument/' . $d->election_id . '/' . $d->st_code . '/' . trim($p_id) . '/' . preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                    $format = preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                    $destinationPath3 = public_path('/uploads1/Nodal-Uploaddocument/' . $d->election_id . '/' . $d->st_code . '/' . trim($p_id));
                    $image->move($destinationPath3, $format);
                    $data = array('file' => $scanPhysicalDoc, 'accept_status' => 1, 'fileserver_dir' => 'uploads1');
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

    public function UpdateAction(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $p_id = strip_tags($req->p_id);
            $time = Carbon::now()->timestamp;
            $user_mb = DB::table('permission_request as a')
                ->join('user_login as b', 'b.id', '=', 'a.user_id')
                ->where('a.id', $p_id)
                ->select('b.mobile')
                ->get()->first();
            //            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no);
            $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no, $d->ac_no, $d->role_id);

            try {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'SUCCESS';
                    $message['LogDescription'] = 'Action Taken Successfully';

                    LogNotification::LogInfo($message);
                }

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
                            $scanPhysicalDoc = 'uploads1/RO-Uploaddocument/' . $d->election_id . '/' . $d->st_code . '/' . trim($p_id) . '/' . preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                            $format = preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                            $destinationPath3 = public_path('/uploads1/RO-Uploaddocument/' . $d->election_id . '/' . $d->st_code . '/' . trim($p_id));
                            $image->move($destinationPath3, $format);
                        }
                        $insertdata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_id, 'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'user_created_by' => '2', 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                        $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                        if ($res == 1) {
                            $data = array('approved_status' => '2', 'updated_by' => $d->id);
                            $where = array('id' => $p_id);
                            $update = $this->PM->updatetable('permission_request', $where, $data);
                            if ($user_mb->mobile != '') {
                                $mob_message = "Your permission has been processed, to check the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                                $response = SmsgatewayHelper::gupshup($user_mb->mobile, $mob_message);
                            }
                            return redirect('/acceo/allPermissionRequest')->with('message', 'Successfully Accepted!');
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
                            $scanPhysicalDoc = 'uploads1/RO-Uploaddocument/' . $d->election_id . '/' . $d->st_code . '/' . trim($p_id) . '/' . preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                            $format = preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                            $destinationPath3 = public_path('/uploads1/RO-Uploaddocument/' . $d->election_id . '/' . $d->st_code . '/' . trim($p_id));
                            $image->move($destinationPath3, $format);
                        }
                        $insertdata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_id, 'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                        $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                        if ($res == 1) {
                            $data = array('approved_status' => '3', 'updated_by' => $d->id);
                            $where = array('id' => $p_id);
                            $update = $this->PM->updatetable('permission_request', $where, $data);
                            if ($user_mb->mobile != '') {
                                $mob_message = "Your permission has been processed, to check the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                                $response = SmsgatewayHelper::gupshup($user_mb->mobile, $mob_message);
                            }
                            return redirect('/acceo/allPermissionRequest')->with('message', 'Successfully Rejected!');
                        }
                    } else {
                        return redirect()->back()->withErrors($validator, 'error')->withInput();
                    }
                } else if (!empty($req->cancel)) {

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
                            $scanPhysicalDoc = 'uploads1/RO-Uploaddocument/' . $d->election_id . '/' . $d->st_code . '/' . trim($p_id) . '/' . preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                            $format = preg_replace('/[^a-zA-Z0-9\.]/i', '_', $d->st_code . '_' . $time . '_' . $image->getClientOriginalName());
                            $destinationPath3 = public_path('/uploads1/RO-Uploaddocument/' . $d->election_id . '/' . $d->st_code . '/' . trim($p_id));
                            $image->move($destinationPath3, $format);
                        }
                        $insertdata = array('fileserver_dir' => 'uploads1', 'permission_request_id' => $p_id, 'ro_cancel_status' => 1, 'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                        $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                        if ($res == 1) {
                            $data = array('cancel_status' => '1', 'updated_by' => $d->id);
                            $where = array('id' => $p_id);
                            $update = $this->PM->updatetable('permission_request', $where, $data);
                            if ($user_mb->mobile != '') {
                                $mob_message = "Your permission has been processed, to check the status download the suvidha candidate android app from here https://goo.gl/YGoMmM and visit the website- suvidha.eci.gov.in";
                                $response = SmsgatewayHelper::gupshup($user_mb->mobile, $mob_message);
                            }
                            return redirect('/acceo/allPermissionRequest')->with('message', 'Successfully Cancelled!');
                        }
                    } else {
                        return redirect()->back()->withErrors($validator, 'error')->withInput();
                    }
                } else {
                    echo 'download';
                }
            } catch (Exception $e) {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'Failed';
                    $message['LogDescription'] = 'Something went to wrong ' . $e->getMessage();

                    LogNotification::LogInfo($message);
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function getAllPC(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $allpc = DB::table('dist_pc_mapping')->select('PC_NO', 'PC_NAME_EN')->where(array('DIST_NO' => $req->dist, 'ST_CODE' => $req->st_code))->groupBy('PC_NO')->get()->toArray();
            return $allpc;
        } else {
            return redirect('/officer-login');
        }
    }
    public function getAllAC(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            if (isset($req->pc_id)) {
                $allac = DB::table('m_ac')->select('AC_NO', 'AC_NAME')->where(array('DIST_NO_HDQTR' => $req->dist, 'ST_CODE' => $req->st_code, 'PC_NO' => $req->pc_id))->get()->toArray();
            } else {
                $allac = DB::table('m_ac')->select('AC_NO', 'AC_NAME')->where(array('DIST_NO_HDQTR' => $req->dist, 'ST_CODE' => $d->st_code))->get()->toArray();
            }
            return $allac;
        } else {
            return redirect('/officer-login');
        }
    }
    public function getAllPoliceStation(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $allac = DB::table('police_station_master')->select('id', 'police_st_name')->where(array('ST_CODE' => $req->st_code, 'ac_no' => $req->ac))->get()->toArray();
            return $allac;
        } else {
            return redirect('/officer-login');
        }
    }
    public function getlocation(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $allloc = DB::table('location_master')->select('id', 'location_name')->where(array('st_code' => $req->st_code, 'ac_no' => $req->ac, 'dist_no' => $req->dist, 'pc_no' => $req->pc_id))->get()->toArray();
            return $allloc;
        } else {
            return redirect('/officer-login');
        }
    }

    //Autority Master
    public function AddNodal()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
            $authority = $this->PM->getAuthority($d->st_code);
            //            print_r($d);die;
            return view('admin.ac.ceo.Permission.AddNodal')->with(array('user_data' => $d, 'ele_details' => $ele_details, 'showpage' => 'permission', 'authority' => $authority, 'ele_details' => $ele_details));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddNodalData(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

            $rules = [
                'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                'email' => 'required',
                'addr' => 'required|not_regex:/([<>@$%?]+)/',
                'mb' => 'required|numeric|digits:10',
                //                'eno' => 'required|numeric|digits:16',
                'authid' => 'required|not_in:0'
            ];
            $messages = [
                'name.required' => ' Name field is required.',
                'name.regex' => 'Please Enter only alphabetical  character.',
                'addr.required' => ' Address field is required.',
                'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                'mb.required' => ' Mobile no is required.',
                'mb.digits' => 'Please Enter valid Mobile Number.',
                'dept.required' => 'Departemnt is required',
                'dept.regex' => 'Please Enter only alphabetical  character.',
                'desig.required' => 'Designation is required Field',
                'desig.regex' => 'Please Enter only alphabetical  character.',
                'email.required' => 'Email is required',
                //                'eno.required' => 'Epic No is required',
                //                'eno.digits' => 'Epic number must be of 16 digits',
                'authid.required' => 'Select Approving Authority'
            ];
            $validator = Validator::make($request->all(), $rules, $messages);


            try {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'SUCCESS';
                    $message['LogDescription'] = 'Nodal Add Successfully';

                    LogNotification::LogInfo($message);
                }

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
                    $checkexisttype = DB::table('authority_masters_mapping')->where(array('auth_type_id' => $authid, 'created_by' => $d->id, 'is_active' => 1))->count();
                    if ($checkexisttype == 0) {

                        $checkAuthmb = DB::table('authority_masters')->where('mobile', $mb)->count();

                        if ($checkAuthmb == 0) {

                            $chckexistuser1 = DB::table('authority_masters_mapping as a')
                                ->where(array('a.auth_type_id' => $authid, 'a.created_by' => $d->id, 'is_active' => 1))->count();
                            if ($chckexistuser1 == 0) {
                                $data = array('st_code' => $d->st_code, 'auth_type_id' => $authid, 'name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                $result = DB::table('authority_masters')->insertGetId($data);
                                $mapdata = array('authority_masters_id' => $result, 'auth_type_id' => $authid, 'created_by' => $d->id,);
                                $mapresult = $this->PM->insertdata('authority_masters_mapping', $mapdata);
                                if (!empty($result) && !empty($mapresult)) {
                                    return redirect('/acceo/viewnodal')->with('message', 'Successfully Added');
                                } else {
                                    return redirect()->back()->with('message', 'Some error occured');
                                }
                            } else {
                                return redirect()->back()->with('chckmessage', 'Entered Authority is already Exist!')->withInput();
                            }
                        } else {

                            $chckexistuser = DB::table('authority_masters_mapping as a')
                                ->join('authority_masters as b', 'b.id', '=', 'a.authority_masters_id')
                                ->where(array('b.mobile' => $mb, 'b.created_by' => $d->id))->count();
                            $checkAuthmbst = DB::table('authority_masters')->where('mobile', $mb)->where('st_code', $d->st_code)->count();

                            if ($chckexistuser == 0) {
                                if ($checkAuthmbst != 0) {
                                    $getauthid = DB::table('authority_masters')->select('id')->where('mobile', $mb)->first();
                                    if (!empty($getauthid)) {
                                        $mapdata = array('authority_masters_id' => $getauthid->id, 'auth_type_id' => $authid, 'created_by' => $d->id,);
                                        $mapresult = $this->PM->insertdata('authority_masters_mapping', $mapdata);
                                        if (!empty($mapresult)) {
                                            return redirect('/acceo/viewnodal')->with('message', 'Successfully Added');
                                        } else {
                                            return redirect()->back()->with('message', 'Some error occured');
                                        }
                                    }
                                } else {
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
            } catch (Exception $e) {
                if (config('public_config.permission_log')) {
                    $message = array();
                    $message['MobNo'] = Auth::user()->officername ?? '';
                    $message['applicationType'] = 'WebApp';
                    $message['Module'] = 'ENCORE';
                    $message['TransectionType'] = 'Permission';
                    $message['TransectionAction'] = 'Data Submit';
                    $message['TransectionStatus'] = 'Failed';
                    $message['LogDescription'] = 'Something went to wrong ' . $e->getMessage();

                    LogNotification::LogInfo($message);
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewNodal(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

            $getAllAuthorityData = $this->PM->getAllAuthorityData1($d->id);
            return view('admin.ac.ceo.Permission.ViewNodal')->with(array('user_data' => $d, 'ele_details' => $ele_details, 'showpage' => 'permission', 'getAllAuthorityData' => $getAllAuthorityData, 'ele_details' => $ele_details));
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditNodal(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

            if (!empty($_POST['submit'])) {
                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'email' => 'required|email:rfc,dns',
                    'addr' => 'required|not_regex:/([<>@$%?]+)/',
                    'mb' => 'required|numeric|digits:10',
                    //                    'eno' => 'required|numeric|digits:16',
                    'authid' => 'required|not_in:0'
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphabetical  character.',
                    'addr.required' => ' Address field is required.',
                    'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'mb.required' => ' Mobile no is required.',
                    'mb.digits' => 'Please Enter valid Mobile Number.',
                    'dept.required' => 'Departemnt is required',
                    'dept.regex' => 'Please Enter only alphabetical  character.',
                    'desig.required' => 'Designation is required Field',
                    'desig.regex' => 'Please Enter only alphabetical  character.',
                    'email.required' => 'Email is required',
                    //                    'eno.required' => 'Epic No is required',
                    //                    'eno.digits' => 'Epic number must be of 16 digits',
                    'authid.required' => 'Select Approving Authority'
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $authid = null;
                    $name = null;
                    $dept = null;
                    $desig = null;
                    $mb = null;
                    $email = null;
                    $addr = null;
                    $eno = null;
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

                    $getallnodaldetails = DB::table('authority_masters')->where('mobile', $mb)->count();
                    if ($getallnodaldetails == 0) {
                        $data = array('name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $cond = array('id' => $_POST['nodal_id']);
                        $result = $this->PM->updatetable('authority_masters', $cond, $data);
                        return redirect()->back()->with('message', 'Successfully Updated');
                    } else {
                        $data = array('name' => $name, 'department' => $dept, 'designation' => $desig, 'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $cond = array('id' => $_POST['nodal_id']);
                        $result = $this->PM->updatetable('authority_masters', $cond, $data);
                        return redirect()->back()->with('message', 'All details will be Updated if Mobile Number is Different,If Mobile Number already exist then except Mobile number all details will be Updated.');
                        // redirect()->back()->with('message', 'Except Autority Type All data is successfully Updated beacuse Entered Authority is already Exist!')->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error');
                }
            } else {
                $data = explode('&', $req->id);
                $nodal_id = Crypt::decryptString($data[0]);
                $nodal_auth = Crypt::decryptString($data[1]);
                $authority = $this->PM->getAuthority($d->st_code);
                $getAuthorityDetails = $this->PM->getAuthorityDetails($nodal_id);
                $authtype = DB::table('authority_masters_mapping as a')->select('a.auth_type_id', 'b.name as auth_type_name')
                    ->join('authority_type as b', 'a.auth_type_id', '=', 'b.id')
                    ->where('authority_masters_id', $nodal_id)->where('auth_type_id', $nodal_auth)->get()->first();

                return view('admin.ac.ceo.Permission.EditNodal')->with(array('user_data' => $d, 'ele_details' => $ele_details, 'authtype' => $authtype, 'getAuthorityDetails' => $getAuthorityDetails, 'authority' => $authority, 'ele_details' => $ele_details));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditNodalStatus(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

            $data = explode('#', $req->status);
            $status = $data[0];
            $id = $data[1];
            $auth_type_id = $data[2];
            if ($status == 1) {
                $where = array('authority_masters_id' => $id, 'auth_type_id' => $auth_type_id, 'created_by' => $d->id);
                $findauth = DB::table('authority_masters_mapping')->where($where)->select('*')->get()->toArray();
                if (!empty($findauth)) {
                    if (count($findauth) == 1) {
                        $cond = array('is_active' => '0');
                        $res = $this->PM->updatetable('authority_masters_mapping', $where, $cond);
                        if ($res == 1) {
                            return 1;
                        } else {
                            return 0;
                        }
                    }
                }
            } else {
                $chckusr = DB::table('authority_masters_mapping')->where(array('auth_type_id' => $auth_type_id, 'is_active' => 1, 'created_by' => $d->id))->count();
                if ($chckusr == 0) {
                    $where = array('authority_masters_id' => $id, 'created_by' => $d->id, 'auth_type_id' => $auth_type_id,);
                    $findauth = DB::table('authority_masters_mapping')->where($where)->select('*')->get()->toArray();
                    if (!empty($findauth)) {
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
                } else {
                    return 2;
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }


    public function PermissionDayRestriction(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);


            $getAllPermissiontype = $this->PM->getAllPermissiontype();

            $getasignperms = DB::table('restriction_day_master')
                ->select('permission_type_id')
                ->where('st_code', $d->st_code)
                ->get()->toArray();

            $getasignperm = array_column($getasignperms, 'permission_type_id');
            //dd($getasignperm);
            return view('admin.ac.ceo.Permission.PermissionDayRestriction')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAllPermissiontype' => $getAllPermissiontype, 'getasignperm' => $getasignperm,));
        } else {
            return redirect('/officer-login');
        }
    }
    public function PermissionDayRestrictionData(Request $request)
    {
        //dd($request->all());
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);

            if (!empty($_POST['pname'])) {
                $pname = strip_tags($_POST['pname']);
            }
            if (!empty($_POST['restriction_day'])) {
                $restriction_day = strip_tags($_POST['restriction_day']);
            }

            $rules = [
                'pname' => 'required|not_in:0',
                // 'auth_name' => 'required',
                // 'doc.*.fsize' => 'required|numeric',
                'restriction_day' => 'required|not_in:0'
            ];

            $messages = [
                'pname.not_in' => 'Permission name field is required.',
                'pname.required' => 'Permission name field is required',
                'restriction_day.not_in' => 'Permission Validity Day ',
                'restriction_day.required' => 'Permission Validity Day '
                // 'ofcrlevel' => 'Please select Assigned Level',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->passes()) {

                if (!empty($request->pname)) {
                    $pname = strip_tags($request->pname);
                }
                if (!empty($request->restriction_day)) {
                    $restriction_day = strip_tags($request->restriction_day);
                }
                if (isset($_POST['daterestriction'])) {
                    $daterestrict = $_POST['daterestriction'];
                } else {
                    $daterestrict = 0;
                }


                $data = array('st_code' => $d->st_code, "permission_type_id" => $pname, "restriction_day" => $restriction_day, 'restriction_status' => $daterestrict, 'modified_at' => $d->id, 'added_at' => date('Y-m-d'), 'modified_by' => $d->id, 'created_at' => date('Y-m-d H:i:s'));
                $result = $this->PM->insertdata('restriction_day_master', $data);

                if ($result) {
                    return redirect('/acceo/PermissionDayRestriction')->with('message', 'Successfully Added');
                } else {
                    return redirect()->back()->with('message', 'Some error occured');
                }
            } else {
                return redirect()->back()->withErrors($validator, 'error')->withInput();
            }
        } else {
            return redirect('/officer-login');
        }
    }


    public function ViewDateRestrict(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);

            $where = array('st_code' => $d->st_code);
            $getViewDateRestrictData = $this->PM->getViewDateRestrictData($where);

            return view('admin.ac.ceo.Permission.ViewDateRestriction')->with(array('user_data' => $d, 'showpage' => 'permission', 'ViewDateRestrictData' => $getViewDateRestrictData));
        } else {
            return redirect('/officer-login');
        }
    }


    public function EditDateRestrict(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);

            if (!empty($_POST['submit'])) {



                $rules = [
                    'pname' => 'required|not_in:0',
                    'restriction_day' => 'required',
                ];
                $messages = [
                    'pname.not_in' => 'Permission name field is required.',
                    'pname.required' => 'Permission name field is required',
                    'restriction_day' => 'Please Enter Day field is required',

                ];

                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {

                    if (!empty($req->pname)) {
                        $pname = strip_tags($req->pname);
                    }
                    if (!empty($req->restriction_day)) {
                        $restriction_day = strip_tags($req->restriction_day);
                    }

                    $data = array('permission_type_id' => $pname, "restriction_day" => $restriction_day, 'modified_by' => $d->id,  'modified_at' => date('Y-m-d H:i:s'));
                    $where = array('id' => $req->permission_id);

                    $result = $this->PM->updatetable('restriction_day_master', $where, $data);


                    if ($result) {

                        return redirect('/acceo/ViewDateRestrict')->with('message', 'Successfully Updated');
                    } else {
                        return redirect()->back()->with('message', 'Some error occured');
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error');
                }
            } else {
                $getDayRestrictDetails = $this->PM->getDayRestrictDetails(Crypt::decryptString($req->id));

                $DayPermissiontype = DB::table('restriction_day_master as a')
                    ->select('a.permission_type_id', 'm.permission_name')
                    ->join('permission_master as m', 'a.permission_type_id', '=', 'm.id')
                    //->where('authority_masters_id', $req->id)
                    ->where('a.id', Crypt::decryptString($req->id))
                    ->get()->first();

                return view('admin.ac.ceo.Permission.EditdaysRestriction')->with(array('user_data' => $d, 'getDayRestrictDetails' => $getDayRestrictDetails, 'DayPermissiontype' => $DayPermissiontype));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function DeleteDateRestrict(Request $req)
    {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $DayPermissiondelete = DB::delete('delete from restriction_day_master where id = ?', [$req->id]);
            return redirect('/acceo/ViewDateRestrict')->with('message', 'Record deleted successfully');
        } else {
            return redirect('/officer-login');
        }
    }
    public function GetMobile(Request $request)
    {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);

            $checkAuthmb = DB::table('authority_masters as a')
                ->leftjoin('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
                ->select('a.*', 'b.auth_type_id')
                ->where('a.mobile', $request->mobileno)->first();
            //dd($checkAuthmb);
            return response(['status' => (($checkAuthmb) ? true : false), 'result' => $checkAuthmb]);
        } else {
            return response(['status' => false], 401);
        }
    }
}
