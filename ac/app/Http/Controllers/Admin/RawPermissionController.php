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
use Carbon\Carbon;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
class RawPermissionController extends Controller {

    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->PM = new CeoPcPermissionModel();
    }



	//----------------------------Divya-------------------------------------// 
	
	public function ceoreport()
	{
	  if (Auth::check()) 
	  {
	  $user = Auth::user();
	  $d=$this->commonModel->getunewserbyuserid($user->id);
	  $user_data = $d;
	  $st_code = $d->st_code;
	   $cur_time    = Carbon::now();
		  $name_excel = 'permission raw report'.'_'.$cur_time;
		  $headings[] = ['Permission ID','State Name','District Name','AC Name','User Name','Permission Type', 'User Type','Party Name','Date of Submission','Action Date','Event Start Date','Event End Date','Permission Mode','Previous Status','Current Status','Comment'];
	  
//		return Excel::create('report', function($excel) use ($d) {
//		$excel->sheet('mySheet', function($sheet) use ($d)
//	    {
	  $st_code = $d->st_code;
			  $distname='';
			 $acname1='';
	  $allrecord=  $data=DB::table('permission_request as a')
			  ->join('user_login as b','a.user_id','=','b.id')
			  ->join('user_data as ud','ud.user_login_id','=','b.id')
			  ->join('user_role as c','b.role_id','=','c.role_id')
			  ->join('permission_type as d','a.permission_type_id','=','d.id')
			  ->join('permission_master as m','m.id','=','d.permission_type_id')
			  ->join('m_party as mp','mp.CCODE','=','a.party_id')
			  ->join('m_state as ms','ms.ST_CODE','=','a.st_code')
			  ->join('permission_request_comment as pc','a.id','=','pc.permission_request_id')
			  ->select('a.*','ud.name','c.role_name','mp.PARTYNAME','ms.ST_NAME','m.permission_name as pname','a.id as permission_id','b.id as login _id','pc.permission_request_id','pc.comment')
			  ->where('a.st_code',$st_code)
			  ->get()->toArray();
	  $arr  = array();
	  foreach($allrecord as $excelrecord)
	  {	
	  $uservalue = DB::table('user_data')
			  ->select('*')
			  ->where('user_login_id',$excelrecord->user_id) 
			  ->first();
	  $stvalue = array('ST_CODE'=>$excelrecord->st_code);
	  $datastate =DB::table('m_state')->select('ST_NAME')->where($stvalue)->first();
	  if($excelrecord->dist_no != 0 && $excelrecord->dist_no != '' )
	  {
	  $datavalue = array('ST_CODE'=>$excelrecord->st_code,'DIST_NO'=>$excelrecord->dist_no);
			  $g = DB::table('m_district')->select('DIST_NAME')->where($datavalue)->first();
			  if(!empty($g))
			  {
				  $distname = $g->DIST_NAME;
			  }
	  }
	  if($excelrecord->ac_no != 0 &&$excelrecord->ac_no != '' )
	  {
	  $acvalue = array('ST_CODE'=>$excelrecord->st_code,'AC_NO'=>$excelrecord->ac_no);
			  $acname = DB::table('m_ac')->select('AC_NAME')->where($acvalue)->first();
				  if(!empty($acname))
				  {
					  $acname1 = $acname->AC_NAME;
				  }
	  }
	  
		if($excelrecord->cancel_status== 1)
		 {
			 $cancelstatus = 'Cancel';
		 }
		 else if($excelrecord->cancel_status == 0)
		 {
		 if($excelrecord->approved_status == 0)
		 {
			 $cancelstatus = 'Pending';
		 }
		 else if($excelrecord->approved_status == 1)
		 {
			$cancelstatus = 'Inprogress';
		 }
		 else if($excelrecord->approved_status == 2)
		 {
			 $cancelstatus = 'Accepted';
		 }
		 else if($excelrecord->approved_status == 3)
		 {
			 $cancelstatus = 'Rejected';
		 }
		 }
		if($excelrecord->permission_mode== 0)
		 {
			 $pmode = 'Offline';
		 }
		 else if($excelrecord->permission_mode == 1)
		 {
			 $pmode  = 'Online';
		 }
		 if($excelrecord->approved_status == 0)
		 {
			 $status = 'Pending';
		 }
		 else if($excelrecord->approved_status == 1)
		 {
			$status = 'Inprogress';
		 }
		 else if($excelrecord->approved_status == 2)
		 {
			 $status = 'Accepted';
		 }
		 else if($excelrecord->approved_status == 3)
		 {
			 $status = 'Rejected';
		 }
				 
		  $data =  array(
		  $excelrecord->reference_id,
		  $datastate->ST_NAME,
					  $distname,
					  $acname1,
//			$g->DIST_NAME,
//			$acname->AC_NAME,
		  $uservalue->name,
		  $excelrecord->pname,
		  $excelrecord->role_name,
		  $excelrecord->PARTYNAME,
		  $excelrecord->added_at,
		  $excelrecord->updated_at,
		  $excelrecord->date_time_start,
		  $excelrecord->date_time_end,
		  $pmode,
		  $status,
		  $cancelstatus,
	      $excelrecord->comment,
		  );
	   array_push($arr, $data);
	   $acname1='';
	   $distname='';
	  }
			  return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
//		$sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
//		'Permission ID','State Name','District Name','AC Name','User Name','Permission Type', 'User Type','Party Name','Date of Submission','Action Date','Event Start Date','Event End Date','Permission Mode','Previous Status','Current Status'
//		)
//		);
//	    });
//		})->download();
	  
	  }
	  else 
	  {
	  return redirect('/officer-login');
	  }  
	}
		
		public function permissionrawview()
	{
	  if (Auth::check()) 
	  {
	  $user = Auth::user();
	  $d=$this->commonModel->getunewserbyuserid($user->id);
	  $user_data = $d;
	  $st_code = $d->st_code;
	   $cur_time    = Carbon::now();
		  $name_excel = 'permission raw report'.'_'.$cur_time;
		 
	  $st_code = $d->st_code;
			  $distname='';
			 $acname1='';
	  $allrecord=  $data=DB::table('permission_request as a')
			  ->join('user_login as b','a.user_id','=','b.id')
			  ->join('user_data as ud','ud.user_login_id','=','b.id')
			  ->join('user_role as c','b.role_id','=','c.role_id')
			  ->join('permission_type as d','a.permission_type_id','=','d.id')
			  ->join('permission_master as m','m.id','=','d.permission_type_id')
			  ->join('m_party as mp','mp.CCODE','=','a.party_id')
			  ->join('m_state as ms','ms.ST_CODE','=','a.st_code')
			  ->join('permission_request_comment as pc','a.id','=','pc.permission_request_id')
			  ->select('a.*','ud.name','c.role_name','mp.PARTYNAME','ms.ST_NAME','m.permission_name as pname','a.id as permission_id','b.id as login _id','pc.permission_request_id','pc.comment')
			  ->where('a.st_code',$st_code)
			  ->get()->toArray();
	  $arr  = array();
	  foreach($allrecord as $excelrecord)
	  {	
	  $uservalue = DB::table('user_data')
			  ->select('*')
			  ->where('user_login_id',$excelrecord->user_id) 
			  ->first();
	  $stvalue = array('ST_CODE'=>$excelrecord->st_code);
	  $datastate =DB::table('m_state')->select('ST_NAME')->where($stvalue)->first();
	  if($excelrecord->dist_no != 0 && $excelrecord->dist_no != '' )
	  {
	  $datavalue = array('ST_CODE'=>$excelrecord->st_code,'DIST_NO'=>$excelrecord->dist_no);
			  $g = DB::table('m_district')->select('DIST_NAME')->where($datavalue)->first();
			  if(!empty($g))
			  {
				  $distname = $g->DIST_NAME;
			  }
	  }
	  if($excelrecord->ac_no != 0 &&$excelrecord->ac_no != '' )
	  {
	  $acvalue = array('ST_CODE'=>$excelrecord->st_code,'AC_NO'=>$excelrecord->ac_no);
			  $acname = DB::table('m_ac')->select('AC_NAME')->where($acvalue)->first();
				  if(!empty($acname))
				  {
					  $acname1 = $acname->AC_NAME;
				  }
	  }
	  
		if($excelrecord->cancel_status== 1)
		 {
			 $cancelstatus = 'Cancel';
		 }
		 else if($excelrecord->cancel_status == 0)
		 {
		 if($excelrecord->approved_status == 0)
		 {
			 $cancelstatus = 'Pending';
		 }
		 else if($excelrecord->approved_status == 1)
		 {
			$cancelstatus = 'Inprogress';
		 }
		 else if($excelrecord->approved_status == 2)
		 {
			 $cancelstatus = 'Accepted';
		 }
		 else if($excelrecord->approved_status == 3)
		 {
			 $cancelstatus = 'Rejected';
		 }
		 }
		if($excelrecord->permission_mode== 0)
		 {
			 $pmode = 'Offline';
		 }
		 else if($excelrecord->permission_mode == 1)
		 {
			 $pmode  = 'Online';
		 }
		 if($excelrecord->approved_status == 0)
		 {
			 $status = 'Pending';
		 }
		 else if($excelrecord->approved_status == 1)
		 {
			$status = 'Inprogress';
		 }
		 else if($excelrecord->approved_status == 2)
		 {
			 $status = 'Accepted';
		 }
		 else if($excelrecord->approved_status == 3)
		 {
			 $status = 'Rejected';
		 }
				 
		  $data =  array(
					  'reference_id'=>$excelrecord->reference_id,
					  'ST_NAME'=>$datastate->ST_NAME,
					  'DIST_NAME'=>$distname,
					  'AC_NAME'=>$acname1,
					  'name'=>$uservalue->name,
					  'pname'=>$excelrecord->pname,
					  'role_name'=>$excelrecord->role_name,
					  'PARTYNAME'=>$excelrecord->PARTYNAME,
					  'added_at'=>$excelrecord->added_at,
					  'updated_at'=>$excelrecord->updated_at,
					  'date_time_start'=>$excelrecord->date_time_start,
					  'date_time_end'=>$excelrecord->date_time_end,
					  'pmode'=>$pmode,
					  'status'=>$status,
					  'cancelstatus'=>$cancelstatus,
					  'comment'=>$excelrecord->comment,
	  
		  );
	   array_push($arr, $data);
	   $acname1='';
	   $distname='';
	  }
			 $object = json_decode(json_encode($arr));
		  return view('admin.ac.ceo.Permission.permissionrawreport', ['user_data' => $d,'rawreport'=>$object]);
	  
	  }
	  else 
	  {
	  return redirect('/officer-login');
	  }  
	}
}