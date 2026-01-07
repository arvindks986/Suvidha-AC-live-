<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoPcPermissionModel extends Model 
{ 
    public function insertdata($table,$data)
    {
        $data=DB::table($table)->insert($data);
        return $data;
    }
    public function getAuthority($stcode)
    {
        $data=DB::table('authority_type as a')
                ->select('a.*')
                 ->where(array('a.st_code'=>$stcode))
                ->get()->toArray();
        return $data;
    }
    public function getLoginUserdetails($uid)
    {				
		$data = DB::table('officer_login as a')
                        ->join('m_state as b','a.st_code','=','b.ST_CODE')
                        ->join('m_district as c',function ($join){
                            $join->on('c.DIST_NO','=','a.dist_no')
                                 ->on('c.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as d',function ($join){
                            $join->on('d.AC_NO','=','a.ac_no')
                                 ->on('d.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('b.ST_NAME','c.DIST_NAME','d.AC_NAME')
                        ->where('a.id',$uid )
                        ->first();
		return $data;
    }
     public function getLoginUserpcdetails($uid)
    {				
		$data = DB::table('officer_login as a')
                        ->join('m_state as b','a.st_code','=','b.ST_CODE')
                        ->join('m_district as c',function ($join){
                            $join->on('c.DIST_NO','=','a.dist_no')
                                 ->on('c.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as d',function ($join){
                            $join->on('d.PC_NO','=','a.pc_no')
                                 ->on('d.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('b.ST_NAME','c.DIST_NAME','d.PC_NAME')
                        ->where('a.id',$uid )
                        ->first();
		return $data;
    }
    public function getPermissionDetails($stcode,$distno,$acno,$role)
    {
         DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('role_master as g','d.role_id','=','g.role_id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->join('m_state as e','e.ST_CODE','=','a.st_code')
                ->leftJoin('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                })
                
                ->leftJoin('m_ac as ad',function ($join){
                    $join->on('ad.AC_NO','=','a.ac_no')
                         ->on('ad.ST_CODE', '=', 'a.st_code');
                })
                
                ->select('a.*','ud.name','f.DIST_NAME','ad.AC_NAME', 'g.role_name AS Approval_name','d.visible_type','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
                ->where(array('a.st_code'=>$stcode,'a.dist_no'=>$distno,'a.ac_no'=>$acno))
                ->whereIn('d.role_id',[0,$role])
                ->orWhere(function ($query) use ($stcode,$distno) {
                    $query->where('d.visible_type', 'like', '%ROAC%')
                          ->where('a.st_code', '=', $stcode)
                          ->where('a.dist_no', '=', $distno);
                }) 
                ->orderBy('a.id', 'DESC')
                ->get()->toArray();
         //dd(DB::getQueryLog());
        return $data;
    }
     public function getDetails($id,$locid)
    {
//         echo $id;die;
         // DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','b.id','=','a.user_id')
                ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                 ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->join('m_state as e','e.ST_CODE','=','a.st_code')
                ->join('role_master as rg','d.role_id','=','rg.role_id')
                //->join('user_role as c','b.role_id','=','c.role_id')
                ->join('m_party as p','a.party_id','=','p.CCODE')
                ->join('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                })
                ->join('m_ac as g',function ($join){
                    $join->on('g.AC_NO','=','a.ac_no')
                         ->on('g.ST_CODE', '=', 'a.st_code');
                });
                if($locid != 'other' && $locid !=0)
                {
                $data->join('location_master as l',function($join){
                    $join->on('l.id','=','a.location_id'); 
                });
                }
                
                if($locid != 'other' &&  $locid !=0)
                {
                $data->select('p.PARTYNAME','d.role_id','rg.role_name AS Approval_name','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','l.location_name','a.id as permission_id','b.id as login _id');
                }
                else {
                    $data->select('p.PARTYNAME','d.role_id','rg.role_name AS Approval_name','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','a.id as permission_id','b.id as login _id');
                } 
                $data->where('a.id',$id);
                //$data->distinct('a.id');                
                $result=$data->get()->toArray();
                // dd($result);
               //  dd(DB::getQueryLOg());
        return $result;
    }
    
    public function updateData($table,$where,$record)
    {
//        DB::enableQueryLog();
        $data=DB::table($table)->where($where)->update($record);
        return $data;
    }
   
    public function getAllAuthority($id)
    {
        $data=DB::table('authority_type')
                ->select('*')->whereIn('id',$id)->get()->toArray();
        return $data;
    }
	
	    public function getlocationmaster($st,$ac)
    {
        $data=DB::table('location_master')
                ->where('st_code',$st)
				->where('ac_no',$ac)
                ->select('*')->get()->toArray();
        return $data;
    }
	
		    public function getlocationeditmaster($id)
    {
        $data=DB::table('location_master')
                ->where('id',$id)
                ->select('*')->get()->toArray();
        return $data;
    }
    
    public function getAllnodal($id)
    {
        $data=DB::table('authority_masters')
                ->select('authority_masters.id','authority_masters.name')
                ->where('auth_type_id',$id)
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
         $data1=DB::table('permission_assigned_auth as a')
                ->join('authority_masters as b','a.authority_id','=','b.id')
                ->join('authority_masters_mapping as e','e.authority_masters_id','=','b.id')
                ->join('authority_type as c','e.auth_type_id','=','c.id')
                ->select('a.*','b.*','c.name as auth_name')
                ->where('a.permission_request_id',$id)
                ->get()->toArray();
         $temp_array = array();
        $i = 0; 
        $key_array = array(); 
        $arraymerge = array_merge($data,$data1);
         foreach($arraymerge as $val) { 
        if (!in_array($val->authority_id, $key_array)) { 
            $key_array[$i] = $val->authority_id; 
            $temp_array[$i] = $val; 
        } 
        $i++; 
    } 
        return $temp_array;
    }
//    public function getNodaldetails($id)
//    {
//        $data=DB::table('authority_masters as a')
//                ->select('a.*','b.name as auth_name')
//                ->whereIn('auth_type_id',$id)
//                ->join('authority_type as b','a.auth_type_id','=','b.id')
//                ->get()->toArray();
//        return $data;
//    }
    public function getAllPSData($where)
    {
        $data=DB::table('police_station_master')
                ->select('*')
                ->where($where)
                ->get()->toArray();
        return $data;
    }
    public function getpsdetails($id)
    {
        $data=DB::table('police_station_master')
                ->where('id',$id)
                ->select('*')->get()->toArray();
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
    public function getAllPermsData($where)
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_type as a')
                ->leftjoin('permission_required_doc as b','a.id','=','b.permission_id')
                ->join('permission_master as m','m.id','=','a.permission_type_id')
                ->join('authority_type as c',DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"),">",DB::raw("'0'"))
                ->where('a.created_by',$where)
//                ->select('a.*','a.id as permsn_id','b.*','b.id as doc_id','c.*','c.id as auth_id')
                ->select(DB::raw("GROUP_CONCAT(DISTINCT b.doc_name SEPARATOR ',') as 'doc_name'"),DB::raw("GROUP_CONCAT(DISTINCT c.name SEPARATOR ',') as 'auth_name'"),'a.id','m.permission_name as pname')
                ->groupBy('a.id','m.permission_name')
                ->get()->toArray();
       // dd(DB::getQueryLog());
        return $data;
    }
    public function getpermsndetails($id)
    {
        $data=DB::table('permission_type as a')
//            ->join('permission_required_doc as b','a.id','=','b.permission_id')
            ->join('authority_type as c',DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"),">",DB::raw("'0'"))
                ->join('permission_master as m','m.id','=','a.permission_type_id')
//            ->select('a.*','c.*','c.id as auth_id')
            ->select('a.id as p_id','m.permission_name as pname','a.authority_type_id',DB::raw("GROUP_CONCAT(c.name SEPARATOR ',') as 'auth_name'"))
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
            ->get()->toArray();
//        dd(DB::getQueryLog());
        return $data;
    }
    public function getRodetails($id)
    {
        $data=DB::table('permission_request as a')
                ->join('permission_request_comment as b','a.id','=','b.permission_request_id')
                ->select('b.*','a.approved_status','a.cancel_status')
                ->where('a.id',$id)
                ->get()->toArray();
        return $data;
    }
    public function getRodetails1($id,$status)
    {
        $data=DB::table('permission_request as a');
//                if($status == 2 || $status == 3)
//                {
                $data->join('permission_request_comment as b','a.id','=','b.permission_request_id');
//                }
//                
//                if($status == 2 || $status == 3)
//                {
                 $data->select('b.*','a.approved_status','a.cancel_status');
//                }
//                else
//                {
//                    $data->select('a.approved_status');
//                }
                $data->where('a.id',$id);
                $result=$data->get()->toArray();
        return $result;
    }
    
    public function getAllAuthorityData($stcode,$distno,$acno)
    {
        $data=DB::table('authority_masters as a')
                ->join('authority_type as b','a.auth_type_id','=','b.id')
                ->select('a.*','b.name as auth_type_name','a.id as nodal_id')
                ->where(array('a.st_code'=>$stcode,'a.dist_no'=>$distno,'a.ac_no'=>$acno))
                ->get()->toArray();
        return $data;
    }
    public function getAllAuthorityData1($created)
    {
        $data=DB::table('authority_masters as a')
                ->join('authority_masters_mapping as m','a.id','=','m.authority_masters_id')
                ->join('authority_type as b','m.auth_type_id','=','b.id')
                ->select('a.*','b.name as auth_type_name1','a.id as nodal_id','m.*')
                ->Where('m.created_by',$created)
                ->get()->toArray();
        return $data;
        //return $data;
    }
    public function getAuthorityDetails($id)
    {
        $data=DB::table('authority_masters as a')
                ->select('a.*','a.id as nodal_id')
                ->where('a.id',$id)
                ->get()->toArray();
        return $data;
    }
//    public function getAuthorityDetails($cond,$id)
//    {
//        $data=DB::table('authority_masters as a')
//                ->join('authority_type as b','a.auth_type_id','=','b.id')
//                ->select('a.*','b.name as auth_type_name','a.id as nodal_id')
//                ->where('a.id',$id)
//                ->where('a.st_code',$cond['st_code'])
//                ->where('a.dist_no',$cond['dist_no'])
//                ->where('a.ac_no',$cond['ac_no'])
//                ->where('a.pc_no',$cond['pc_no'])
//                ->get()->toArray();
//        return $data;
//    }
    public function getUserDetails($mb)
    {
        $data=DB::table('user_login as a')
                ->join('user_data as b','a.id','=','b.user_login_id')
                ->join('user_role as c','c.role_id','=','a.role_id')
                ->join('m_party as p','p.CCODE','=','a.party_id')
                ->select('a.*','b.*','c.role_name','a.id as login_id','p.PARTYNAME','b.name as user_name')
                ->where('a.mobile',$mb)
                ->get()->toArray();
        return $data;
    }
    public function getUserappDetails($mb)
    {
        $data=DB::table('user_login as a')
                ->join('user_data as b','a.id','=','b.user_login_id')
//                ->join('user_role as c','c.role_id','=','a.role_id')
                ->select('a.*','b.*','a.id as login_id','b.name as user_name')
                ->where('a.mobile',$mb)
                ->get()->toArray();
        return $data;
    }
    
    public function getLoginCandDetails($mb)
    {
        $data=DB::table('user_login as a')
//                ->join('user_data as b','a.id','=','b.user_login_id')
                ->join('user_role as c','c.role_id','=','a.role_id')
                 ->join('m_party as p','p.CCODE','=','a.party_id')
                ->select('a.*','c.role_name','a.id as login_id','p.PARTYNAME')
                ->where('a.mobile',$mb)
                ->get()->toArray();
        return $data;
    }
    
    public function getLoginappCandDetails($mb)
    {
        $data=DB::table('user_login as a')
//                ->join('user_data as b','a.id','=','b.user_login_id')
//                ->join('user_role as c','c.role_id','=','a.role_id')
                ->select('a.*','a.id as login_id')
                ->where('a.mobile',$mb)
                ->get()->toArray();
        return $data;
    }
    
    public function user_details_police($stcode,$district,$ac)
    {
        $data=DB::table('police_station_master as a')
                ->join('m_state as b', 'a.ST_CODE','=','b.ST_CODE')
                ->join('m_ac as g',function ($join){
                    $join->on('g.AC_NO','=','a.ac_no')
                         ->on('g.ST_CODE', '=', 'b.ST_CODE');
                })
                ->where('a.ST_CODE',$stcode)
                ->where('a.ac_no',$ac)
                 ->get()->toArray();
                return $data;
                
    }
    
    public function getallpolicestation($where)
    {
        $data=DB::table('police_station_master as a')
                ->select('*')
                ->where($where)
                ->get()->toArray();
        return $data;
    }
    public function getAllUserType()
    {
        $data=DB::table('user_role')
                ->select('*')
                ->where('role_level','=','2')
                ->get()->toArray();
        return $data;
    }
    
    public function getAgentList($where)
    {
        $data=DB::table('officer_login')
                ->select('*')
                ->where('role_id','=','21')
                ->where($where)
                ->get()->toArray();
        return $data;
    }
    public function getAgentDetails($id)
    {
        $data=DB::table('officer_login')
                ->select('*')
                ->where('role_id','=','21')
                ->where('id',$id)
                ->get()->toArray();
        return $data;
    }
    
    public function getAllLocation($cond)
    {
        $data=DB::table('location_master')
                ->select('*')
                ->where($cond)
                ->get()->toArray();
        return $data;
                
    }
    
    public function totalPermissionReport($st,$dist,$ac,$role_id)
    {
        //DB::enableQueryLog();
         if($role_id == 20 || $role_id == 21)
        {
        $data=DB::table('permission_request as a')
                ->join('permission_type as b','a.permission_type_id','=','b.id')
                ->join('user_login as l','a.user_id','=','l.id')
                ->select(DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as Total'))
                ->where(array('a.st_code'=>$st,'a.dist_no'=>$dist,'a.ac_no'=>$ac))
                ->whereIn('b.role_id',array(0,20))
                ->where('l.role_id','!=','NULL')
                //->groupBy('approved_status')
                ->get();
        //dd(DB::getQueryLog());
        return $data;
        }
    }
    public function totalReportDetails($where,$status)
    {
//        print_r($where);die;
         if($where[3] == 20 || $where[3] == 21)
        {
        $data=DB::table('permission_request as a')
               
                ->join('user_login as b','a.user_id','=','b.id')
                ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                 ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                
                ->where(array('a.st_code'=>$where[0],'a.dist_no'=>$where[1],'a.ac_no'=>$where[2]))
                ->whereIn('d.role_id',array(0,20))
                ->where('approved_status',$status)
                ->where('a.cancel_status','0')
                ->get()->toArray();
return $data;
        }
    }
    public function totalPermissionReportData($where)
    {
         if($where[3] == 20 || $where[3] == 21)
        {
         $data=DB::table('permission_request as a')
                
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                  ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                
                ->where(array('a.st_code'=>$where[0],'a.dist_no'=>$where[1],'a.ac_no'=>$where[2]))
                 ->whereIn('d.role_id',array(0,20))
                ->get()->toArray();
return $data;
        }
    }
    
    public function totalPendingReportDetails($where)
    {
         $data=DB::table('permission_request as a')
                 ->join('permission_master as m','m.id','=','a.permission_type_id')
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                 ->where(array('a.st_code'=>$where[0],'a.dist_no'=>$where[1],'a.ac_no'=>$where[2]))
                ->whereIn('a.approved_status',array(0,1))
                ->whereIn('d.role_id',array(0,$where[3]))
                 ->where('a.cancel_status','0')
                ->get()->toArray();
return $data;
    }
    
     public function getAllDist($st)
    {
         $data = DB::table('m_district')
                    ->select('DIST_NO','DIST_NAME')
                    ->where('ST_CODE',$st )
                    ->get()->toArray();
		return $data;
    }
}