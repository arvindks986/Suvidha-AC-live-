<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\adminmodel\CeoPcPermissionModel;
use Illuminate\Http\Request;
use Session;
use DB;
use App\commonModel;
use App\adminmodel\CandidateModel;
use App\adminmodel\ROPCModel;
use App\Classes\xssClean;
use PDF;

class ACCeoPermissionController extends Controller {

    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->PM = new CeoPcPermissionModel();
    }

    public function allMasters() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            return view('admin.pc.ceo.Permission.Masters', ['user_data' => $d]);
        } else {
            return redirect('/officer-login');
        }
    }

    //permission Master
    public function AddPermission() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $getAuthType = $this->PM->getAuthType($d->st_code);
            $getAllPermissiontype = $this->PM->getAllPermissiontype();
//            echo '<pre/>';
//            print_r($getAuthType);die;
            return view('admin.pc.ceo.Permission.AddPermission')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAuthType' => $getAuthType, 'getAllPermissiontype' => $getAllPermissiontype));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddPermissionData(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
//            echo '<pre/>';
//            print_r($request->all());die;
            $document = $request->input('doc');
            $file = $request->file('doc');
            $rules = [];
            $message = [];
            $authority_id = 0;

//            echo $authtype;die;
            if (!empty($_POST['pname'])) {
                $pname = strip_tags($_POST['pname']);
//                echo $pname;die;
            }

            if (!empty($document)) {
                $rules = [
                    'pname' => 'required|not_in:0',
                    'auth_name' => 'required',
                    'doc.*.Dname' => 'required|not_regex:/([<>@$%?#]+)/',
//                    'doc.*.fsize' => 'required|numeric',
                    'doc.*.format' => 'required|mimes:pdf',
                ];
            } else {
                $rules = [
                    'pname' => 'required|regex:/(^[ A-Za-z]+$)/',
                ];
            }
            $messages = [
                'pname.not_in' => 'Permission name field is required.',
                'pname.required' => 'Permission name field is required',
                'auth_name.required' => 'Select Authority type is required.',
                'doc.*.Dname.required' => 'Document name is required',
                'doc.*.Dname.not_regex' => 'These special character are not allowed(<>@$%?#).',
//                'doc.*.fsize.required' => 'File size required',
//                'doc.*.fsize.numeric' => 'Please enter only numeric value for file size.',
                'doc.*.format.required' => 'Please Upload required Document',
                'doc.*.format.mimes' => 'Please Upload only (.pdf) document',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->passes()) {
                $auth_id = $request->input('auth_name');
                if (!empty($auth_id)) {
                    $authtype = implode(',', $auth_id);
                }
                $chckid=DB::table('permission_type')
                        ->where('permission_type_id',$pname)
                        ->where('st_code',$d->st_code)->count();
                if($chckid == 0)
                {
                $data = array("permission_type_id"=>$pname,"authority_type_id" => $authtype, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'status' => 1, 'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
               
//                $result = $this->PM->updatetable('permission_type', $where, $data);
                $result = DB::table('permission_type')->insertGetId($data);
                if (!empty($result) && $result != '') {
                    for ($i = 0; $i < count($document); $i++) {
                        $dname = 'NULL';
//                        $fsize = 'NULL';
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
                        if (!empty($file)) {
                            $format = strip_tags($file[$i]['format']->getClientOriginalName());
                            $destinationPath3 = public_path('/uploads/permission-document/' . trim($d->st_code));
                            $file[$i]['format']->move($destinationPath3, $format);
                        }
                        $data1 = array("permission_type_id"=>$pname,'permission_id' => $result, 'doc_name' => $dname, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'required_status' => $chck, 'file_name' => $format, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                        $pdata = DB::table('permission_required_doc')->insert($data1);
                    }
                }
                return redirect('/pcceo/viewpermsn')->with('message', 'Successfully Added');
                }
                else
                {
                    return redirect()->back()->with('chckmessage', 'Entered Permission Name is already Exist!')->withInput();
                }
            }
            return redirect()->back()->withErrors($validator, 'error')->withInput();
        } else {
            return redirect('/officer-login');
        }
        //return response()->json(['error'=>$validator->errors()->all()]);
    }

    public function ViewPerms() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            $getAllPermsData = $this->PM->getAllPermsData($d->st_code);
//            echo '<pre/>';
//            print_r($getAllPermsData);die;
            return view('admin.pc.ceo.Permission.ViewPermission')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAllPermsData' => $getAllPermsData));
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function GetdocDetails(Request $request)
    {
         if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        $permsn_id=$request->p_id;
        if (!empty($permsn_id)) {
                    $getPermissionDetails = DB::table('permission_required_doc as a')
                            ->join('permission_type as b','b.id','=','a.permission_id')
                            ->join('authority_type as c',\DB::raw("FIND_IN_SET(c.id,b.authority_type_id)"),">",\DB::raw("'0'"))
                            ->select('a.*',DB::raw("GROUP_CONCAT(DISTINCT c.name SEPARATOR ',') as 'auth_name'"))->where('a.permission_id', $permsn_id)->where('a.st_code',$d->st_code)->get()->toArray();
                    if (!empty($getPermissionDetails)) {
//                    print_r($getPermissionDetails);die;
                        return $getPermissionDetails;
                    } else {
                        return '0';
                    }
                }
                } else {
            return redirect('/officer-login');
        }
    }

    public function EditPrmsn(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
           
            if (!empty($_POST['UpdatePermission'])) {
//                 echo '<pre/>';
//            print_r($request->all());die;
                $document = $request->input('doc');
                $file = $request->file('doc');
                $rules = [];
                $message = [];
                $auth_id = '';
                $authority_id = 0;
                if (!empty($_POST['pname'])) {
                    $pname = strip_tags($_POST['pname']);
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
                        'auth_name' => 'required',
//                        'doc.*.fsize' => 'required|numeric',
                        'doc.*.format' => 'mimes:pdf',
                    ];
                    $messages = [
                        'pname.not_regex' => 'Please enter only alphanumeric value.',
                        'pname.required' => 'Permission name field is required',
                        'doc.*.Dname.required' => 'Document name is required',
                        'doc.*.Dname.not_regex' => 'These special character are not allowed(<>@$%?#).',
                        'auth_name.required' => 'Select Authority type is required.',
//                    'doc.*.fsize.required' => 'File size required',
//                    'doc.*.fsize.numeric' => 'Please enter only numeric value for file size.',
//                        'doc.*.format.required' => 'Please Upload required Document',
                        'doc.*.format.mimes' => 'Please Upload only (.pdf) document',
                    ];
                     $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->passes()) {
                    $auth_id = $request->input('auth_name');
                    if (!empty($auth_id)) {
                        $authtype = implode(',', $auth_id);
                    }
                    $data = array("authority_type_id" => $authtype, 'permission_name' => $pname, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'status' => 1, 'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
//                $result = DB::table('permission_type')->insertGetId($data);
                    $where = array('id' => $request->p_id);
                    $result = $this->PM->updatetable('permission_type', $where, $data);
//                if (!empty($result) && $result != '') {
                    for ($i = 0; $i < count($document); $i++) {
                        $doc_id = 0;
                        $dname = 'NULL';
//                        $fsize = 'NULL';
                        $chck = 0;
                        $format = 'NULL';
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
                        if (!empty($file[$i])) {
                                $format = strip_tags($file[$i]['format']->getClientOriginalName());
                                $destinationPath3 = public_path('/uploads/permission-document/' . trim($d->st_code));
                                $file[$i]['format']->move($destinationPath3, $format);
                                $getdocid = DB::table('permission_required_doc')->select('id')->where('id', $doc_id)->get()->toArray();
//                        print_r($getdocid);die;
                        if (!empty($getdocid)) {
                            $data1 = array('doc_name' => $dname, 'st_code' => $d->st_code, 'required_status' => $chck, 'file_name' => $format, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where1 = array('id' => $doc_id, 'permission_id' => $request->p_id);
                            $pdata = $this->PM->updatetable('permission_required_doc', $where1, $data1);
                        } else {
                            $data1 = array('permission_id' => $request->p_id, 'doc_name' => $dname, 'st_code' => $d->st_code, 'required_status' => $chck, 'file_name' => $format, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                            $pdata = DB::table('permission_required_doc')->insert($data1);
                        }
                        }
                        else
                        {
                            $getdocid = DB::table('permission_required_doc')->select('id')->where('id', $doc_id)->get()->toArray();
//                        print_r($getdocid);die;
                        if (!empty($getdocid)) {
                            $data1 = array('doc_name' => $dname, 'st_code' => $d->st_code, 'required_status' => $chck, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where1 = array('id' => $doc_id, 'permission_id' => $request->p_id);
                            $pdata = $this->PM->updatetable('permission_required_doc', $where1, $data1);
                        } else {
                            $data1 = array('permission_id' => $request->p_id, 'doc_name' => $dname,  'st_code' => $d->st_code, 'required_status' => $chck, 'file_name' => $format, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
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
//                        'doc.*.Dname' => 'required|not_regex:/([<>@$%?#]+)/',
                        'auth_name' => 'required',
////                        'doc.*.fsize' => 'required|numeric',
//                        'doc.*.format' => 'required|mimes:pdf',
                    ];

                    $messages = [
                        'pname.not_regex' => 'Please enter only alphanumeric value.',
                        'pname.required' => 'Permission name field is required',
//                    'doc.*.Dname.required' => 'Document name is required',
//                    'doc.*.Dname.not_regex' => 'These special character are not allowed(<>@$%?#).',
                        'auth_name.required' => 'Select Authority type is required.',
//                    'doc.*.fsize.required' => 'File size required',
//                    'doc.*.fsize.numeric' => 'Please enter only numeric value for file size.',
//                    'doc.*.format.required' => 'Please Upload required Document',
//                    'doc.*.format.mimes' => 'Please Upload only (.pdf) document',
                    ];
                     $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->passes()) {
                     $auth_id = $request->input('auth_name');
                    if (!empty($auth_id)) {
                        $authtype = implode(',', $auth_id);
                    }
                    $data = array("authority_type_id" => $authtype, 'permission_name' => $pname, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'status' => 1, 'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
//                $result = DB::table('permission_type')->insertGetId($data);
                    $where = array('id' => $request->p_id);
                    $result = $this->PM->updatetable('permission_type', $where, $data);
//                if (!empty($result) && $result != '') {
                    for ($i = 0; $i < count($document); $i++) {
                        $doc_id = 0;
                        $dname = 'NULL';
//                        $fsize = 'NULL';
                        $chck = 0;
                        $format = 'NULL';
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
//                        if (!empty($file)) {
//                            $format = strip_tags($file[$i]['format']->getClientOriginalName());
//                            $destinationPath3 = public_path('/uploads/permission-document/' . trim($result));
//                            $file[$i]['format']->move($destinationPath3, $format);
//                        }
                        $getdocid = DB::table('permission_required_doc')->select('id')->where('id', $doc_id)->get()->toArray();
//                        print_r($getdocid);die;
                        if (!empty($getdocid)) {
                            $data1 = array('doc_name' => $dname,  'st_code' => $d->st_code, 'required_status' => $chck,  'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where1 = array('id' => $doc_id, 'permission_id' => $request->p_id);
                            $pdata = $this->PM->updatetable('permission_required_doc', $where1, $data1);
                        } else {
                            $data1 = array('permission_id' => $request->p_id, 'doc_name' => $dname, 'st_code' => $d->st_code, 'required_status' => $chck,'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                            $pdata = DB::table('permission_required_doc')->insert($data1);
                        }
                    }
               return redirect()->back()->with('message', 'Successfully Updated');
               
                }
                   return redirect()->back()->withErrors($validator, 'error');  
                }
               
            } else {
                $p_id = $request->id;
//                echo $p_id;die;
                $getpermsndetails = $this->PM->getpermsndetails($p_id);

                $authtypeid = $getpermsndetails[0]->authority_type_id;
//               $atypeid= explode(',', $authtypeid);
                $getpermsndocdetails = $this->PM->getpermsndocdetails($p_id);
                $getAuthType = $this->PM->getAuthType($d->st_code);
                return view('admin.pc.ceo.Permission.EditPermission')->with(array('user_data' => $d, 'showpage' => 'permission', 'getpermsndetails' => $getpermsndetails, 'getpermsndocdetails' => $getpermsndocdetails, 'getAuthType' => $getAuthType));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    //Autority Master
    public function AddAuthority() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $getAuthType = DB::table('authority_type')->select('name', 'id')->where('st_code', $d->st_code)->get()->toArray();
            return view('admin.pc.ceo.Permission.AddAuthority')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAuthType' => $getAuthType));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddAuthorityData(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
//            echo '<pre/>';
//            print_r($d);die;
            $rules = [
                'name' => 'required|regex:/(^[ A-Za-z]+$)/',
            ];
            $messages = [
                'name.required' => ' Name field is required.',
                'name.regex' => 'Please Enter only alphanumeric character.',
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
                        return redirect('/pcceo/viewauthority')->with('message', 'Successfully Added');
                    } else {
                        return redirect()->back()->with('message', 'Some error occured');
                    }
                } else {
                    return redirect()->back()->with('chckmessage', 'Entered Authority Name is already Exist!')->withInput();
                }
            } else {
                return redirect()->back()->withErrors($validator, 'error')->withInput();
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
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $prmsn_id=$req->permsn_id;
            $doc_id=$req->doc_id;
            $where=array('id'=>$doc_id,'permission_id'=>$prmsn_id);
            $res=$this->PM->deletedata('permission_required_doc',$where);
            if($res == 1)
            {
                return 1;
            }
            else
            {
                return 0;
            }
//            echo $req->permsn_id;die;
//            $where = array('st_code' => $d->st_code);
//            $getAllAuthorityData = $this->PM->getAllAuthorityTypeData($where);
//            return view('admin.pc.ceo.Permission.ViewAuthority')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAllAuthorityData' => $getAllAuthorityData));
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewAuthority(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $where = array('st_code' => $d->st_code);
            $getAllAuthorityData = $this->PM->getAllAuthorityTypeData($where);
            return view('admin.pc.ceo.Permission.ViewAuthority')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAllAuthorityData' => $getAllAuthorityData));
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditAuthority(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            if (!empty($_POST['submit'])) {
                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphanumeric character.',
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
                $where = array('st_code' => $d->st_code, 'id' => $req->id);
                $getAuthorityDetails = $this->PM->getAuthorityTypeDetails($where);
                return view('admin.pc.ceo.Permission.EditAuthority')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAuthorityDetails' => $getAuthorityDetails));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionCount() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $where = array('st_code' => $d->st_code);
            $allrecord = $this->PM->totalPermissionReport($where);
//            print_r($allrecord);die;
            return view('admin.pc.ceo.Permission.PermissionReport', ['user_data' => $d, 'allrecord' => $allrecord]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionCountDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
//            echo $req->statusid;die;
            $where1 = array($d->st_code);
            if ($req->statusid != 'NULL') {
                $totalReportDetails = $this->PM->totalReportDetails($where1, $req->statusid);
//                print_r($totalReportDetails);die;
                return $totalReportDetails;
//                return view('admin.pc.ro.Permission.AllPendingReport', ['user_data' => $d,'allrecord'=>$allrecord,'totalReportDetails'=>$totalReportDetails]);
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionDetailsView(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $id = $req->id;
            $loc_id = $req->loc_id;
            $getNodaldetails = $this->PM->getNodaldetails($id);
            $getRodetails = $this->PM->getRodetails($id, $req->status);
//        print_r($getRodetails);die;
            $getDetailsview = $this->PM->getDetails($id, $loc_id);
            return view('admin.pc.ceo.Permission.AcceptPermissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails));
        } else {
            return redirect('/officer-login');
        }
    }

    public function generatePDF(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $data = ['title' => 'Welcome to HDTuto.com'];
            $id = $req->id;
            $getDetailsview = $this->PM->getDetails($id);
            $getNodaldetails = $this->PM->getNodaldetails($id);
            $getRodetails = $this->PM->getRodetails($id);
            $pdf = PDF::loadView('admin.pc.ceo.permission.PermissionDetailsPDF', ['getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails]);
//            $pdf = PDF::loadView('admin.pc.ro.Permission.Reciept',['getDetails'=>$getDetailsview]);

            return $pdf->download('mypdf.pdf');
        } else {
            return redirect('/officer-login');
        }
    }

}
