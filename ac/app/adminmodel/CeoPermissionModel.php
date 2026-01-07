<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
    
class CeoPermissionModel extends Model 
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function deletedata($table,$where)
    {
        $now = Carbon::now();
        $data=DB::table($table)
                ->where($where)
              ->update(['deleted_at' => $now ]);
        return $data;
    }


    
    public function insertdata($table,$data)
    {
        $data=DB::table($table)->insert($data);
        return $data;
    }
    
    public function updatetable($table,$where,$content)
    {
//        DB::enableQueryLog();
        $data=DB::table($table)
                ->where($where)
                ->update($content);
//        dd(DB::getQueryLog());
        return $data;
    }
    
//    public function getAllPermsData($stcode)
//    {
////        DB::enableQueryLog();
//        $data=DB::table('permission_type as a')
//                ->join('permission_master as m','m.id','=','a.permission_type_id')
//                ->leftjoin('permission_required_doc as b','a.id','=','b.permission_id')
//                ->join('authority_type as c',\DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"),">",\DB::raw("'0'"))
//                ->where('a.st_code',$stcode)
//                ->whereNull('b.deleted_at')
////                ->select('a.*','a.id as permsn_id','b.*','b.id as doc_id','c.*','c.id as auth_id')
//                ->select(DB::raw("GROUP_CONCAT(DISTINCT b.file_name SEPARATOR ',') as 'doc_name'"),DB::raw("GROUP_CONCAT(DISTINCT c.name SEPARATOR ',') as 'auth_name'"),'a.id as p_id','m.permission_name as pname')
//                ->groupBy('a.id')
//                
//                ->get()->toArray();
////        dd(DB::getQueryLog());
//        return $data;
//    }
    
    public function getAllPermsData($stcode)
    {
         $data=DB::table('permission_type as a')
                ->join('permission_master as m','m.id','=','a.permission_type_id')
                 ->where('a.st_code',$stcode)
                ->select('a.id as p_id','m.permission_name as pname')
                ->get()->toArray();
//        dd(DB::getQueryLog());
        return $data;
    }
    public function getpermsndetails($id)
    {
        $data=DB::table('permission_type as a')
//            ->join('permission_required_doc as b','a.id','=','b.permission_id')
                ->join('permission_master as m','m.id','=','a.permission_type_id')
            ->join('authority_type as c',\DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"),">",\DB::raw("'0'"))
            ->select('a.*','c.*','c.id as auth_id')
            ->select('a.id as p_id','a.authority_type_id',DB::raw("GROUP_CONCAT(c.name SEPARATOR ',') as 'auth_name'"),'m.permission_name as pname')
            ->where('a.id',$id)    
            ->groupBy('a.id','m.permission_name','a.authority_type_id')
            ->get()->toArray();
        return $data;
    }
    public function getpermsndocdetails($id)
    {
//        DB::enableQueryLog();
        $data=DB::table('permission_required_doc as a')
                ->select('a.*','a.id as doc_id')
                ->where('permission_id',$id)
                ->whereNull('deleted_at')
            ->get()->toArray();
//        dd(DB::getQueryLog());
        return $data;
    }
    public function totalPermissionReport($where)
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_request')
                ->select(DB::raw('sum(CASE WHEN approved_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'))
                ->where($where)
                //->groupBy('approved_status')
                ->get();
        //dd(DB::getQueryLog());
        return $data;
    }
    public function totalReportDetails($where,$status)
    {
//        print_r($where);die;
        $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','b.name','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
                
                ->where('a.st_code',$where)
                ->where('approved_status',$status)
                ->get()->toArray();
return $data;
    }
    
    
    public function getAllAuthorityTypeData($where)
    {
        $data=DB::table('authority_type')
                ->select('*')
                ->where($where)
                ->get()->toArray();
        return $data;
    }
    public function getAuthorityTypeDetails($where)
    {
        $data=DB::table('authority_type')
                ->select('*')
                ->where($where)
                ->get()->toArray();
        return $data;
    }
    
     public function getNodaldetails($id)
    {
//        DB::enableQueryLog();
        $data=DB::table('permission_assigned_auth as a')
                ->join('authority_masters as b','a.authority_id','=','b.id')
                ->join('authority_type as c','b.auth_type_id','=','c.id')
                ->select('a.*','b.*','c.name as auth_name')
                ->where('a.permission_request_id',$id)
                ->get()->toArray();
//        dd(DB::getQueryLog());
        return $data;
    }
    public function getRodetails($id,$status)
    {
        $data=DB::table('permission_request as a');
                if($status == 2 || $status == 3)
                {
                $data->join('permission_request_comment as b','a.id','=','b.permission_request_id');
                }
                
                if($status == 2 || $status == 3)
                {
                 $data->select('b.*','a.approved_status');
                }
                else
                {
                    $data->select('a.approved_status');
                }
                $data->where('a.id',$id);
                $result=$data->get()->toArray();
        return $result;
    }
     public function getDetails($id,$locid)
    {
//         echo $id;die;
//         DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','b.id','=','a.user_id')
                ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->join('m_state as e','e.ST_CODE','=','a.st_code')
                ->join('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                })
                ->join('m_ac as g',function ($join){
                    $join->on('g.AC_NO','=','a.ac_no')
                         ->on('g.ST_CODE', '=', 'a.st_code');
                });
                if($locid != 'other')
                {
                $data->join('location_master as l',function($join){
                    $join->on('l.id','=','a.location_id')
                       ->on('l.st_code','=','a.st_code')
                       ->on('l.dist_no','=','a.dist_no')
                       ->on('l.ac_no','=','a.ac_no');
                });
                }
                
                if($locid != 'other')
                {
                $data->select('a.*','b.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','l.location_name','a.id as permission_id','b.id as login _id');
                }
                else {
                    $data->select('a.*','b.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','a.id as permission_id','b.id as login _id');
                }
                $data->where('a.id',$id);
//                $data->distinct('a.id');
                
                $result=$data->get()->toArray();
//                dd($result);
//                dd(DB::getQueryLOg());
        return $result;
    }
    
    public function getAuthType($stcode)
    {
        $data=DB::table('authority_type')
                ->select('*')
                ->where('st_code',$stcode)
                ->get()->toArray();
            return $data;
    }
    
    public function getAllPermissiontype()
    {
        $data=DB::table('permission_master')
                ->select('*')
                ->get()->toArray();
            return $data;
    }
}
