<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;
use App\commonModel;
use App\models\{
    States,
    Districts,
    AC
};
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\adminmodel\CandidateApiModel;
use App\adminmodel\RoPcPermissionModel;

class PermissionEncoreAdminAPI extends Controller {

    public $secret = 'ENCORE@Admin2021';
    public $successStatus = 200;
    public $UnsuccessStatus = 204;
    public $passStatus = 1;
    public $failsStatus = 0;
    public $sizeimage = '5000000';
    public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;

    public function __construct(Request $request = null) {
        $this->PM = new RoPcPermissionModel();
        $header = $request->headers->all();
        if (empty($header['secret'][0])) {
            print_r(json_encode(array('code' => $this->unauthorizedStatus, 'status' => $this->failsStatus, 'message' => 'Unauthorized access not allowed!')));
            die;
        } else {
            $secret = $header['secret'][0];
            if ($this->secret != $secret) {
                print_r(json_encode(array('code' => $this->unauthorizedStatus, 'status' => $this->failsStatus, 'message' => 'Unauthorized access not allowed!')));
                die;
            }
        }
    }

    public function permissionlisting(Request $request) {
        try {
            $input = $request->All();
            $validator = Validator::make($request->all(), [
//                            'state_code'=>'required',
//                            'ac_no' => 'required',
//                            'dist_code' => 'required',
                        'accessToken' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                            'code' => $this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message' => 'All fields is required.'
                ]);
            }
            $accessToken = trim($input['accessToken']);
            $officer = DB::table('officer_login')->where('accesstoken', '=', $accessToken)->get()->count();
            if ($officer > 0) {
                $officer_id = DB::table('officer_login')->where('accesstoken', '=', $accessToken)->select('id','st_code','ac_no','dist_no','pc_no')->first();
                $data = DB::table('permission_request as a')
                        ->join('user_login as b', 'b.id', '=', 'a.user_id')
                         ->join('user_role as ur', 'b.role_id', '=', 'ur.role_id')
                        ->join('user_data as ud', 'ud.user_login_id', '=', 'a.user_id')
                        ->join('permission_type as d', 'a.permission_type_id', '=', 'd.id')
                        ->join('permission_master as m', 'm.id', '=', 'd.permission_type_id')
                        ->join('m_state as e', 'e.ST_CODE', '=', 'a.st_code')
                        ->join('m_party as p', 'a.party_id', '=', 'p.CCODE')
                        ->join('m_district as f', function ($join) {
                            $join->on('f.DIST_NO', '=', 'a.dist_no')
                            ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g', function ($join) {
                    $join->on('g.AC_NO', '=', 'a.ac_no')
                    ->on('g.ST_CODE', '=', 'a.st_code');
                });
                $data->select('a.id as permission_id','ur.role_name','p.PARTYNAME', 'ud.name', 'm.permission_name as pname', 'a.permission_mode', 'a.approved_status', 'a.cancel_status', 'a.added_at as subdate', 'ud.gender', 'b.mobile', 'e.ST_NAME', 'f.DIST_NAME', 'g.AC_NAME');
                $data->where(['a.st_code' => $officer_id->st_code,'a.ac_no'=>$officer_id->ac_no,'a.dist_no'=>$officer_id->dist_no]);
                $data->distinct('a.id');

                $data1 = $data->get()->toArray();
                $result = array();
                $permissionlisting= array();
                $Permissioncountlist= array();
                if(!empty($data1))
                {
                foreach($data1 as $permsnlist)
                {
                    if($permsnlist->approved_status == 0){ $status = 'Pending'; }
                    elseif($permsnlist->approved_status == 1){ $status = 'Inprocess'; }
                    elseif($permsnlist->approved_status == 2){ $status = 'Accept'; }
                    elseif($permsnlist->approved_status == 3){ $status = 'Reject'; }
                    
                    if($permsnlist->permission_mode == 0){ $pmode = 'Offline'; }
                    elseif($permsnlist->permission_mode == 1){ $pmode = 'Online'; }
                    $permissionlisting[] = array(
                        'perm_id' => $permsnlist->permission_id,
                        'applicant_type' => $permsnlist->role_name,
                        'PARTYNAME' => $permsnlist->PARTYNAME,
                        'applicant_name' => $permsnlist->name,
                        'perm_type' => $permsnlist->pname,
                        'perm_mode' => $pmode,
                        'status' => $status,
                        'perm_submit_date' => $permsnlist->subdate,
                        'applicant_gender' => $permsnlist->gender,
                    );
                }
                }
                
                $Permissioncountlist = DB::table('permission_request as a')
                                ->select(DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as Total'))
                                ->where(['a.st_code' => $officer_id->st_code,'a.ac_no'=>$officer_id->ac_no,'a.dist_no'=>$officer_id->dist_no])
                                ->get()->toArray();
                
                $result = array(
                    'permissionlisting'=>$permissionlisting,
                    'Permissioncountlist'=>$Permissioncountlist
                );
                
                return response()->json(['code' => $this->successStatus, 'status' => $this->passStatus, 'success' => true, 'result' => $result]);
            } else {
                return response()->json(['success' => false, 'message' => "accessToken entered is not correct or you are already logout"]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => $this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
        }
    }
    
    public function getpermissiondetails(Request $request) {
        try {
            $input = $request->All();
            $validator = Validator::make($request->all(), [
                        'permission_id' => 'required',
                        'accessToken' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                            'code' => $this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'message' => 'All fields is required.'
                ]);
            }
            $accessToken = trim($input['accessToken']);
            $id = trim($input['permission_id']);
            $officer = DB::table('officer_login')->where('accesstoken', '=', $accessToken)->get()->count();
            if ($officer > 0) {
                $officer_id = DB::table('officer_login')->where('accesstoken', '=', $accessToken)->select('id','st_code','ac_no','dist_no','pc_no','role_id')->first();
                $getallpermsndetails=DB::table('permission_request')->select('assigned_police_st_id','approved_status','location_id','cancel_status')->where('id',$id)->get()->first();
            if(!empty($getallpermsndetails))
            {
                if(!empty($getallpermsndetails->assigned_police_st_id))
            {
                $allps= explode(',', $getallpermsndetails->assigned_police_st_id);
                $allps_name=DB::table('police_station_master')->select('police_st_name')->whereIn('id',$allps)->get()->toArray();
            }
            $getDetailsview = $this->PM->getDetails($id, $getallpermsndetails->location_id);

            $where = array('st_code' => $officer_id->st_code,'ac_no'=>$officer_id->ac_no,'dist_no'=>$officer_id->dist_no);
            $permissionDetails = $this->PM->getPermissionDetails($officer_id->st_code, $officer_id->dist_no, $officer_id->ac_no,$officer_id->role_id);
            $getNodaldetails = $this->PM->getNodaldetails($id);
             $getRodetails = $this->PM->getRodetails($id);
             $canddoc = $data=DB::table('permission_assigned_auth as a')
                        ->select('a.*')
                        ->where('a.permission_request_id',$id)
                        ->where('a.authority_id','cand01')
                        ->get()->toArray();
          if(!empty($allps_name ))
             {
            return response()->json(['code' => $this->successStatus, 'status' => $this->passStatus, 'success' => true, 'allps_name'=>$allps_name ,'getDetails' => $getDetailsview,'canddoc'=>$canddoc, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails]);
            }
             else
             {
                 return response()->json(['code' => $this->successStatus, 'status' => $this->passStatus, 'success' => true,'getDetails' => $getDetailsview,'canddoc'=>$canddoc, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails]);
            
             }

                
            }
            }
            else {
                return response()->json(['success' => false, 'message' => "accessToken entered is not correct or you are already logout"]);
            }
            
        } catch (Exception $ex) {
            return response()->json(['code' => $this->UnsuccessStatus, 'status' => $this->failsStatus, 'success' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
        }
    }

}
