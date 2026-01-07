<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB;
class ReportModel extends Model
{
//-----------------------------Divya-------------------------------------------------------//

   public function getAllRecordbydate($date1,$date2)
    {
        $data=DB::table('permission_request as a')
     
                ->join('user_login as b','a.user_id','=','b.id')
                ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                ->whereBetween('a.added_at',[$date1,$date2])
                ->get()->toArray();
        return $data;
    }

      public function totalPermissionReport()
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_request')
                ->select(DB::raw('sum(CASE WHEN approved_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'))
                ->where($where)
                //->groupBy('approved_status')
                ->get();
       // dd(DB::getQueryLog());
        return $data;
    }
	

	   public function getAlldistRecord($st,$dist)
      {
        $data=DB::table('permission_request as a') 
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                  ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                ->where(array('a.st_code'=>$st,'a.dist_no'=>$dist))
                ->get()->toArray();
        return $data;
      }
	  
	
	   public function getAllacRecord($st,$dist,$ac)
    {
        $data=DB::table('permission_request as a')
                
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                  ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                ->where(array('a.st_code'=>$st,'a.dist_no'=>$dist,'a.ac_no'=>$ac))
                ->get()->toArray();
        return $data;
    }

	
	   public function getpermissionRecord($st,$perm)
    {
        $data=DB::table('permission_request as a')
                
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                  ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                ->where(array('a.st_code'=>$st,'a.permission_type_id'=>$perm))
                ->get()->toArray();
        return $data;
    }
	public function getAllstateRecord($st)
    {
        $data=DB::table('permission_request as a')
                
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                  ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                ->where(array('a.st_code'=>$st))
                ->get()->toArray();
        return $data;
    }
		public function getstatename($st)
    {
		   //DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                 ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                ->where(array('a.st_code'=>$st))
                ->get()->toArray();
        return $data;
    }
		function getbystatename($stcode)
		{
		$getstate_name = DB::table('m_state')
                ->select('ST_NAME')
				->where('ST_CODE',$stcode) 
                ->get();
		return $getstate_name;
		}
		
	
		  function getdetailbyuserid($userid)
		{
		$getuserdetail = DB::table('user_data')
                ->select('*')
				->where('user_login_id',$userid) 
                ->get();
		return $getuserdetail;
		}
		
		function getbystatedistrictname($stcode,$distno)
		{
		$getstate_name = DB::table('m_district')
                ->select('DIST_NAME')
				->where('ST_CODE',$stcode) 
				->where('DIST_NO',$distno) 
                ->get();
		return $getstate_name;
		}
		function getbystateacname($stcode,$acno)
		{
		$getstate_name = DB::table('m_ac')
                ->select('AC_NAME')
				->where('ST_CODE',$stcode) 
				->where('AC_NO',$acno) 
                ->get();
		return $getstate_name;
		}
		
	    function getbylocation($stcode,$locationid)
		{
		$getstate_name = DB::table('location_master')
                ->select('*')
				->where('st_code',$stcode) 
				->where('id',$locationid) 
                ->get();
		return $getstate_name;
		}
		
		function getbylocat($locationid)
		{
		$getstate_name = DB::table('location_master')
                ->select('*') 
				->where('id',$locationid) 
                ->get();
		return $getstate_name;
		}
       public function getacceptdetails($id)
        {
        $result= DB::table('permission_request as a')
                 ->join('permission_request_comment as b','a.id','=','b.permission_request_id')
				->where('permission_request_id',$id)  
                ->get();
        return $result;
	
        }
		  function getpermisson()
		{
	    $data=DB::table('permission_type as a')
                ->join('permission_master as m','m.id','=','a.permission_type_id')
                ->select('m.*','a.id as permission_id','a.id as permissionid','a.permission_type_id as permission_type_id')

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
	
	    public function totalReportDetails($status)
    {
//        print_r($where);die;
        $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
                
                ->where('approved_status',$status)
                ->get()->toArray();
return $data;
    }
	
			public function getallrecords()
    {
		   //DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                
                 ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                ->get()->toArray();
        return $data;
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
                $data->select('a.*','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','l.location_name','a.id as permission_id','b.id as login _id');
                }
                else {
                    $data->select('a.*','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','a.id as permission_id','b.id as login _id');
                }
                $data->where('a.id',$id);
//                $data->distinct('a.id');
                
                $result=$data->get()->toArray();
//                dd($result);
//                dd(DB::getQueryLOg());
        return $result;
    }
		  function getpermn($stcode)
		{
	   $data=DB::table('permission_type as a')
                ->join('permission_master as m','m.id','=','a.permission_type_id')
                ->select('m.*','a.id as permission_id','a.id as permissionid','a.permission_type_id as permission_type_id')
                ->where(array('a.st_code'=>$stcode))
                ->get()->toArray();
        return $data;
		}
			
		
	  
}
