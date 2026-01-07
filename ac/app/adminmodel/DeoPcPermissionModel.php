<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model; 
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DeoPcPermissionModel extends Model 
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
    
     public function getAgentList($where)
    {
        $data=DB::table('officer_login')
                ->select('*')
                ->where('role_id','=','24')
                ->where($where)
                ->get()->toArray();
        return $data;
    }
    public function getAgentDetails($id)
    {
        $data=DB::table('officer_login')
                ->select('*')
                ->where('role_id','=','24')
                ->where('id',$id)
                ->get()->toArray();
        return $data;
    }
    
    
     public function totalPermissionReport($where)
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as l','a.user_id','=','l.id')
                ->select(DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as Total'))
                ->where($where)
                ->where('l.role_id','!=','NULL')
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
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                 ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                
                ->where(array('a.st_code'=>$where[0],'a.dist_no'=>$where[1]))
                ->where('approved_status',$status)
                ->where('a.cancel_status','0')
                ->get()->toArray();
return $data;
    }
    public function totalPermissionReportData($where)
    {
         $data=DB::table('permission_request as a')
                
                ->join('user_login as b','a.user_id','=','b.id')
                  ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                  ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                
                ->where(array('a.st_code'=>$where[0],'a.dist_no'=>$where[1]))
                ->get()->toArray();
return $data;
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
                
                ->where(array('a.st_code'=>$where[0],'a.dist_no'=>$where[1]))
                ->where('a.approved_status',0)
                 ->where('a.cancel_status','0')
                ->get()->toArray();
return $data;
    }
    
    public function getNodaldetails($id)
    {
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
     public function getRodetails1($id,$status)
    {
        $data=DB::table('permission_request as a');
//                if($status == 2 || $status == 3)
//                {
                $data->join('permission_request_comment as b','a.id','=','b.permission_request_id');
//                }
                
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
      public function getIntraDetails($id,$locid)
    {
//         echo $id;die;
//         DB::enableQueryLog();
 
        $data=DB::table('permission_request as a')
                ->join('user_login as b','b.id','=','a.user_id')
                ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                 ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->join('m_state as e','e.ST_CODE','=','a.st_code')
                ->join('m_party as p','a.party_id','=','p.CCODE')
                ->join('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                })
                ->join('m_pc as g',function ($join){
                    $join->on('g.PC_NO','=','a.pc_no')
                         ->on('g.ST_CODE', '=', 'a.st_code');
                });
                if($locid != 'other' && $locid != '0')
                {
                $data->join('location_master as l',function($join){
                    $join->on('l.id','=','a.location_id');
//                       ->on('l.st_code','=','a.st_code')
//                       ->on('l.dist_no','=','a.dist_no')
//                       ->on('l.ac_no','=','a.ac_no');
                });
                }
                
                if($locid != 'other' && $locid != '0')
                {
                $data->select('p.PARTYNAME','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.PC_NAME','ud.*','l.location_name','a.id as permission_id','b.id as login _id');
                }
                else {
                    $data->select('p.PARTYNAME','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.PC_NAME','ud.*','a.id as permission_id','b.id as login _id');
                }
                $data->where('a.id',$id);  
//                $data->distinct('a.id');
                
                $result=$data->get()->toArray();
//                dd($result);
//                dd(DB::getQueryLOg());
        return $result;
    }
     public function getIntradistDetails($id,$locid)
    {
//         echo $id;die;
//         DB::enableQueryLog();
$data=DB::table('permission_request as a')
                ->join('user_login as b','b.id','=','a.user_id')
                ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('role_master as g','d.role_id','=','g.role_id')
                 ->join('permission_master as m','m.id','=','d.permission_type_id')
                 ->join('m_party as p','a.party_id','=','p.CCODE')
                ->join('m_state as e','e.ST_CODE','=','a.st_code')
                ->leftJoin('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                })
                
                     
                ->leftJoin('m_ac as ad',function ($join){
                    $join->on('ad.AC_NO','=','a.ac_no')
                         ->on('ad.ST_CODE', '=', 'a.st_code');
                });
                
                if($locid != 'other' && $locid != '0')
                {
                $data->join('location_master as l',function($join){
                    $join->on('l.id','=','a.location_id');
//                       ->on('l.st_code','=','a.st_code')
//                       ->on('l.dist_no','=','a.dist_no')
//                       ->on('l.ac_no','=','a.ac_no');
                });
                }
                
                if($locid != 'other' && $locid != '0')
                {
                $data->select('p.PARTYNAME','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','g.role_name AS Approval_name','b.mobile','e.ST_NAME','f.DIST_NAME', 'ad.AC_NAME','ud.*','l.location_name','a.id as permission_id','d.visible_type','d.role_id','b.id as login _id');
                }
                else {
                    $data->select('p.PARTYNAME','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','g.role_name AS Approval_name','b.mobile','e.ST_NAME','f.DIST_NAME', 'ad.AC_NAME','ud.*','a.id as permission_id','d.visible_type','d.role_id','b.id as login _id');
                }
                $data->where('a.id',$id);
                $result=$data->get()->toArray();
       /*  $data=DB::table('permission_request as a')
                ->join('user_login as b','b.id','=','a.user_id')
                ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                 ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->join('m_state as e','e.ST_CODE','=','a.st_code')
                ->join('m_party as p','a.party_id','=','p.CCODE')
                ->join('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                });
                
                if($locid != 'other' && $locid != '0')
                {
                $data->join('location_master as l',function($join){
                    $join->on('l.id','=','a.location_id');
//                       ->on('l.st_code','=','a.st_code')
//                       ->on('l.dist_no','=','a.dist_no')
//                       ->on('l.ac_no','=','a.ac_no');
                });
                }
                
                if($locid != 'other' && $locid != '0')
                {
                $data->select('p.PARTYNAME','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','ud.*','l.location_name','a.id as permission_id','b.id as login _id');
                }
                else {
                    $data->select('p.PARTYNAME','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','ud.*','a.id as permission_id','b.id as login _id');
                }
                $data->where('a.id',$id);
//                $data->distinct('a.id');
                
                $result=$data->get()->toArray(); */
//                dd($result);
//                dd(DB::getQueryLOg());

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
                ->join('m_party as p','a.party_id','=','p.CCODE')
                ->join('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                })
                ->join('m_ac as g',function ($join){
                    $join->on('g.AC_NO','=','a.ac_no')
                         ->on('g.ST_CODE', '=', 'a.st_code');
                });
                if($locid != 'other' && $locid != '0')
                {
                $data->join('location_master as l',function($join){
                    $join->on('l.id','=','a.location_id');
//                       ->on('l.st_code','=','a.st_code')
//                       ->on('l.dist_no','=','a.dist_no')
//                       ->on('l.ac_no','=','a.ac_no');
                });
                }
                
                if($locid != 'other' && $locid != '0')
                {
                $data->select('p.PARTYNAME','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','l.location_name','a.id as permission_id','b.id as login _id');
                }
                else {
                    $data->select('p.PARTYNAME','a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','a.id as permission_id','b.id as login _id');
                }
                $data->where('a.id',$id);
//                $data->distinct('a.id');
                
                $result=$data->get()->toArray();
//                dd($result);
//                dd(DB::getQueryLOg());
        return $result;
    }
    public function getAllAcRecord($st,$ac,$dist)
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
//                        ->join('m_ac as d',function ($join){
//                            $join->on('d.AC_NO','=','a.ac_no')
//                                 ->on('d.ST_CODE', '=', 'a.st_code');
//                        })
                        ->select('b.ST_NAME','c.DIST_NAME')
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
    public function getPermissionDetails($stcode,$distno,$role)
    {
        // DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('role_master as g','d.role_id','=','g.role_id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                  ->join('m_state as e','e.ST_CODE','=','a.st_code')
                 ->leftjoin('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                })
                
                ->leftjoin('m_ac as ad',function ($join){
                    $join->on('ad.AC_NO','=','a.ac_no')
                         ->on('ad.ST_CODE', '=', 'a.st_code');
                })
                ->select('a.*','ud.name','c.role_name','m.permission_name as pname',
                'g.role_name AS Approval_name','a.id as permission_id','e.ST_NAME','f.DIST_NAME',
                'ad.AC_NAME', 'b.id as login _id')
                ->where(array('a.st_code'=>$stcode,'a.dist_no'=>$distno))
		->where('d.role_id',$role)
         ->orWhere(function ($query) use ($stcode,$distno) {
                    $query->where('d.visible_type', 'like', '%DEO%')
                          ->where('a.st_code', '=', $stcode)
                          ->where('a.dist_no', '=', $distno);
                })
        ->orderBy('a.id', 'DESC')
                ->get()->toArray();
       /*  $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
                ->where(array('a.st_code'=>$stcode,'a.dist_no'=>$distno))
                ->where('d.role_id',$role)
                ->whereNotIn('d.permission_type_id',DB::table('pci_assignment')
                ->where(array('status'=>1,'dist_id'=>$distno, 'st_code'=>$stcode))->select('permission_type_id'))
//                ->where('d.pci_role_id',0)
//                 ->where('d.pci_role_status',0)
                ->get()->toArray(); */
         //dd(DB::getQueryLog());
        return $data;
    }
    public function getintraPermissionDetails($stcode,$distno,$role)
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('role_master as g','d.role_id','=','g.role_id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                 ->join('m_state as e','e.ST_CODE','=','a.st_code')
                 ->leftjoin('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                })
                
                ->leftjoin('m_ac as ad',function ($join){
                    $join->on('ad.AC_NO','=','a.ac_no')
                         ->on('ad.ST_CODE', '=', 'a.st_code');
                })
                ->select('a.*','ud.name','c.role_name','m.permission_name as pname','e.ST_NAME','f.DIST_NAME','ad.AC_NAME', 'g.role_name AS Approval_name','a.id as permission_id','b.id as login _id')
                ->where(array('a.st_code'=>$stcode,'a.dist_no'=>$distno))
		         ->where('d.role_id',$role)
                 ->orWhere(function ($query) use ($stcode,$distno) {
                    $query->where('d.visible_type', 'like', '%DEO%')
                          ->where('a.st_code', '=', $stcode)
                          ->where('a.dist_no', '=', $distno);
                })
               // ->orWhere('d.visible_type', 'like', '%DEO%')
               ->orderBy('a.id', 'DESC')

      
                ->get()->toArray();
        /* $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
                ->where(array('a.st_code'=>$stcode,'a.dist_no'=>$distno))
                ->where('d.role_id',$role)
                ->whereNotIn('d.permission_type_id',DB::table('pci_assignment')->where(array('status'=>1,'dist_id'=>$distno, 'st_code'=>$stcode))->select('permission_type_id'))
//                ->where('d.pci_role_id',0)
//                 ->where('d.pci_role_status',0)
                ->get()->toArray(); */
        //dd(DB::getQueryLog());
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
   
    public function getAllPermsData($where)
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_type as a')
                ->leftjoin('permission_required_doc as b','a.id','=','b.permission_id')
                ->join('permission_master as m','m.id','=','a.permission_type_id')
                ->join('authority_type as c',\DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"),">",\DB::raw("'0'"))
                ->where('a.created_by',$where)
//                ->select('a.*','a.id as permsn_id','b.*','b.id as doc_id','c.*','c.id as auth_id')
                ->select(DB::raw("GROUP_CONCAT(DISTINCT b.doc_name SEPARATOR ',') as 'doc_name'"),DB::raw("GROUP_CONCAT(DISTINCT c.name SEPARATOR ',') as 'auth_name'"),'a.id','m.permission_name as pname')
                ->groupBy('a.id','m.permission_name')
                ->get()->toArray();
       // dd(DB::getQueryLog());
        return $data;
    }
   public function getAllACAuthorityData1($created,$stcode,$dist)
    {
       /* ak $data1=DB::table('authority_masters as a')
                ->join('authority_type as c','a.auth_type_id','=','c.id')
                ->select('a.*','c.name as auth_type_name2','a.id as nodal_id')
                ->where('a.created_by',$created)
                 ->where('a.dist_no',$dist)
                ->where('a.ac_no',$acno)
                ->where('a.pc_no',$pc)
                ->get()->toArray();*/
        return DB::table('authority_masters as a')
                ->join('authority_masters_mapping as m','a.id','=','m.authority_masters_id')
                ->join('authority_type as b','m.auth_type_id','=','b.id')
                ->select('a.*','b.name as auth_type_name1','a.id as nodal_id','m.*')
                ->Where('m.created_by',$created)
                ->where('m.dist_no',$dist)
                //ak ->where('m.ac_no',$acno)
                //ak ->where('m.pc_no',$pc)
                ->get()->toArray();
      // $data= array_merge($data1,$data2);
        // $data= array_merge($data1);
        // return $data;
    }
    public function getpermsndetails($id)
    {
        $data=DB::table('permission_type as a')
//            ->join('permission_required_doc as b','a.id','=','b.permission_id')
            ->join('authority_type as c',\DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"),">",\DB::raw("'0'"))
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
   
  
    public function getAllAuthorityData($stcode,$distno,$acno)
    {
        $data=DB::table('authority_masters as a')
                ->join('authority_type as b','a.auth_type_id','=','b.id')
                ->select('a.*','b.name as auth_type_name','a.id as nodal_id')
                ->where(array('a.st_code'=>$stcode,'a.dist_no'=>$distno,'a.ac_no'=>$acno))
                ->get()->toArray();
        return $data;
    }
    public function getAuthorityDetails($id,$atypeid,$dist,$cid)
    {
        $data=DB::table('authority_masters as a')
                ->join('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
               ->select('a.st_code','a.id as nodal_id','a.auth_type_id','a.name','a.department','a.designation','a.mobile','a.email','a.address','a.status','b.*')
               ->where('a.id',$id)
                // $pc,$ac, ak ->where(array('b.dist_no'=>$dist,'b.ac_no'=>$ac,'b.pc_no'=>$pc,'b.auth_type_id'=>$atypeid,'b.created_by'=>$cid))

                ->where(array('b.dist_no'=>$dist,'b.auth_type_id'=>$atypeid,'b.created_by'=>$cid))
               ->get()->toArray();
       return $data;
    }
   public function getACAuthorityDetails($id)
    {
        $data=DB::table('authority_masters as a')
                ->select('a.*','a.id as nodal_id')
                ->where('a.id',$id)
//                ->where('a.pc_no',$cond['pc_no'])
                ->get()->toArray();
        return $data;
    }
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
    
   
    public function getAllLocation($cond)
    {
        $data=DB::table('location_master')
                ->select('*')
                ->where($cond)
                ->get()->toArray();
        return $data;
                
    }
    
    public function getAllPC($st,$dist)
    {
         $data=DB::table('dist_pc_mapping')
                ->select('PC_NO','PC_NAME_EN')
                ->where('ST_CODE',$st)
                 ->where('DIST_NO',$dist)
                 ->distinct()
                ->get()->toArray();
        return $data;
    }
    public function getAllAC($pc,$st,$dist)
    {
        $data=DB::table('m_ac')
                ->select('AC_NO','AC_NAME')
                ->where('ST_CODE',$st)
                ->where('PC_NO',$pc)
                ->where('DIST_NO_HDQTR',$dist)
                ->get()->toArray();
        return $data;
    }
    
     public function getAllACAuthorityData($stcode,$acno)
    {
        $data=DB::table('authority_masters as a')
                ->join('authority_type as b','a.auth_type_id','=','b.id')
                ->select('a.*','b.name as auth_type_name','a.id as nodal_id')
                ->where('a.st_code',$stcode)
                ->where('a.ac_no',$acno)
                ->get()->toArray();
        return $data;
    }
    public function getallps($ac,$st)
    {
         $data = DB::table('police_station_master')
                    ->select('police_st_name','id')
                    ->where('st_code',$st )
                    ->whereIn('ac_no',$ac)
                    ->get()->toArray();
		return $data;
    }
   
   public function getRodetails($id)
    {
        $data=DB::table('permission_request as a')
                ->join('permission_request_comment as b','a.id','=','b.permission_request_id')
                ->join('officer_login as c','c.id','=','b.created_by')
                ->select('b.*','a.approved_status','a.cancel_status','c.role_id')
                ->where('a.id',$id)
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
    
    public function getPCIPermissionDetails($stcode,$distno,$role)
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('pci_assignment as p','p.permission_type_id','=','d.permission_type_id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
                ->where(array('a.st_code'=>$stcode,'a.dist_no'=>$distno))
                ->where('p.dist_id',$distno)
                ->where('p.pci_role_id',$role)
                ->where('p.status',1)
                ->get()->toArray();
        //dd(DB::getQueryLog());
        return $data;
    }
    public function getAllPermissiontype($role,$st,$dist)
    {
        $data=DB::table('permission_master as a')
                ->join('permission_type as b','a.id','=','b.permission_type_id')
                ->where('b.role_id',$role)
                ->where('b.st_code',$st)
                ->whereNotIn('b.permission_type_id',DB::table('pci_assignment')->where(array('st_code'=>$st,'dist_id'=>$dist))->select('permission_type_id'))
                ->select('a.permission_name','b.id as ptype_id','b.permission_type_id')
                ->get()->toArray();
            return $data;
    }
    public function getofficerlevel()
    {
        $data=DB::table('role_master')
                ->where('role_level',12)
                ->select('role_id','role_name','role_level','role_description')
                ->get()->toArray();
        return $data;
    }
    public function getAssignAllPermissiontype($st,$dist)
    {
        $data=DB::table('permission_master as a')
                ->join('pci_assignment as b','a.id','=','b.permission_type_id')
                ->where('b.st_code',$st)
                ->where('b.dist_id',$dist)
                ->select('a.permission_name','b.id as ptype_id','b.permission_type_id')
                ->get()->toArray();
            return $data;
    }
}
