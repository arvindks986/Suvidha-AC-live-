<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\commonModel;
use DB;
use Carbon\Carbon;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExcelExport;
use Validator;
use PDF;
class CeoPermissionReportController extends Controller {
    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
    }
    public function TimeWiseReport(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $distvalue = DB::table('m_district')->where('ST_CODE', $d->st_code)->select('ST_CODE','DIST_NO','DIST_NAME')->get();
            
                $data=DB::table('permission_request as a')
                       ->join('user_login as b','a.user_id','=','b.id')
                       ->join('user_data as ud','ud.user_login_id','=','b.id')
                       ->join('user_role as c','b.role_id','=','c.role_id')
                       ->join('permission_type as d','a.permission_type_id','=','d.id')
                       ->join('permission_master as m','m.id','=','d.permission_type_id')
                       ->select('a.reference_id','ud.name','m.permission_name as pname','a.permission_mode','c.role_name','a.added_at','a.approved_status')
                       ->whereIn('a.approved_status',[0,1])
                       ->where('a.st_code',$d->st_code)
                      ->get()->toArray();
            return view('admin.ac.ceo.Permission.CeoPermissionReport', ['record'=>$data,'time'=>0,'ac_no'=>0,'dist_no'=>0,'user_data' => $d,'distvalue' => $distvalue]);
        }
    }
    public function ReportTimes(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $cur_time = Carbon::now();
            $name_excel = 'timewise report'.'_'.$cur_time;
                 $dtSub = new DateTime($req->time.'hours');
                 $date = $dtSub->format('Y-m-d H:m:s');
                  $data=DB::table('permission_request as a')
                       ->join('user_login as b','a.user_id','=','b.id')
                       ->join('user_data as ud','ud.user_login_id','=','b.id')
                       ->join('user_role as c','b.role_id','=','c.role_id')
                       ->join('permission_type as d','a.permission_type_id','=','d.id')
                       ->join('permission_master as m','m.id','=','d.permission_type_id')
                       ->select('a.reference_id','ud.name','m.permission_name as pname','a.permission_mode','c.role_name','a.added_at','a.approved_status')
                       ->whereIn('a.approved_status',[0,1])
                       ->where('a.st_code',$d->st_code);
                       if($req->dist != 'all' && $req->dist != 0)
                       {
                       $data->where('a.dist_no',$req->dist);
                       }
                       if($req->ac != 'all' && $req->ac!= 0)
                       {
                       $data->where('a.ac_no',$req->ac);
                       }
                       if($req->time != '0')
                       {
                       $data->whereBetween('a.date_time_start',[$cur_time , $date]);
                       }
                       $data1=$data->get()->toArray();
             if ($req->input('excel')) {
                   
                    $headings[] = ['Reference_id','Applicant_name', 'Permission_type', 'Permission_mode','Applicant_Type','DateTime of Submission','Status'];
                                    
                                    $arr = array();
                                    foreach ($data1 as $record_data) {
                                        if ($record_data->permission_mode == 0) {
                                            $record_data->permission_mode = 'Offline';
                                        }
                                        else
                                        {
                                            $record_data->permission_mode = 'Online';
                                        }
                                        if($record_data->approved_status == 0)
                                        {
                                            $status = $record_data->approved_status = 'Pending';
                                        }else
                                        {
                                            $status = $record_data->approved_status = 'Inprogress';
                                        }
                                        
                                        $data = array(
                                            $record_data->reference_id,
                                            $record_data->name,
                                            $record_data->pname,
                                            $record_data->permission_mode,
                                            $record_data->role_name,
                                            $record_data->added_at,
                                            $status,
                                        );
                                        array_push($arr, $data);
                                        $status="";
                                    }
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
               
             }else
             {
                 
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.timewisereport', ['user_data' => $d, 'records' => $data1]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                 
             }
             
        }
        else {
            return redirect('/officer-login');
        }
    }
    
    
    public function PermissionSummaryReport(Request $req)
    {
        if (Auth::check()) {
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $distvalue = DB::table('m_district')->where('ST_CODE', $d->st_code)->select('ST_CODE','DIST_NO','DIST_NAME')->get();
        $cur_time = Carbon::now();
        if($req->method() == 'POST')
        {
            $rules = [
                    'dist' => 'required|not_in:0',
                    'ac' => 'required|not_in:0',
                ];
                $messages = [
                    'dist.required' => 'District is required.',
                    'ac.required' => 'AC is Required',
                    'dist.not_in' => 'Please select District.',
                    'ac.not_in' => 'Please select AC',
                ];
             $validator = Validator::make($req->all(), $rules, $messages);
             if ($validator->passes()) {
                if($req->dist != 'all' && $req->ac != 'all')
                 {
                $data=DB::table('permission_request as a')
                        ->join('m_district as f',function ($join){
                            $join->on('f.DIST_NO','=','a.dist_no')
                                 ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g',function ($join){
                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('a.dist_no','a.ac_no','f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start >= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start < \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
                        ->where('a.st_code',$d->st_code)
                        ->where('a.dist_no','!=',0)
                        ->where('a.ac_no','!=','NULL')
                        ->where('a.dist_no','!=','')
                        ->where('a.ac_no','!=',0)
                        ->where('a.dist_no','!=','NULL')
                        ->where('a.ac_no','!=','')
						->where('a.cancel_status','!=',1)
                        ->where('a.dist_no',$req->dist)
                        ->where('a.ac_no',$req->ac)
                        ->groupBy('a.ac_no')
                ->get()->toArray();
                 }
                 else if($req->dist == 'all' && $req->ac == 'all'){
                     
                     $data=DB::table('permission_request as a')
                        ->join('m_district as f',function ($join){
                            $join->on('f.DIST_NO','=','a.dist_no')
                                 ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g',function ($join){
                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('a.dist_no','a.ac_no','f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start >= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start < \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
						 ->where('a.cancel_status','!=',1)
                        ->where('a.st_code',$d->st_code)
                        ->groupBy('a.ac_no')
                ->get()->toArray();
                 }
                 else
                 {
                     $data=DB::table('permission_request as a')
                        ->join('m_district as f',function ($join){
                            $join->on('f.DIST_NO','=','a.dist_no')
                                 ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g',function ($join){
                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('a.dist_no','a.ac_no','f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start >= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start < \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
                        ->where('a.st_code',$d->st_code)
                        ->where('a.dist_no','!=',0)
                        ->where('a.dist_no','!=','')
                        ->where('a.dist_no','!=','NULL')
						 ->where('a.cancel_status','!=',1)
                        ->where('a.dist_no',$req->dist)
                        ->groupBy('a.ac_no')
                        ->get()->toArray();
                 }
                return view('admin.ac.ceo.Permission.permissionsummaryreport', ['user_data' => $d,'distvalue' => $distvalue,'dist_no' => $req->dist,'ac_no' => $req->ac,'summarydata'=>$data]);
             }
             else
             {
                 return redirect()->back()->withErrors($validator, 'error')->withInput();
             }
        }
        else
        {
            $summarydata=DB::table('permission_request as a')
                        ->join('m_district as f',function ($join){
                            $join->on('f.DIST_NO','=','a.dist_no')
                                 ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g',function ($join){
                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('a.dist_no','a.ac_no','f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start >= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start < \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
						 ->where('a.cancel_status','!=',1)
                        ->where('a.st_code',$d->st_code)
                        ->groupBy('a.ac_no')
                ->get()->toArray();
            return view('admin.ac.ceo.Permission.permissionsummaryreport', ['user_data' => $d,'distvalue' => $distvalue,'summarydata'=>$summarydata]);
        }
        }
        else {
            return redirect('/officer-login');
        }
    }
    
    public function PermissionSummaryReportdownload(Request $req)
    {
         if (Auth::check()) {
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
         $cur_time = Carbon::now();
         $name_excel = 'Permission_Summary_report'.'_'.$cur_time;
        if(!empty($req->ac) && !empty($req->dist) && $req->ac != '0' &&  $req->dist != '0')
            {
            if($req->dist != 'all' && $req->ac != 'all')
                 {
                $data=DB::table('permission_request as a')
                        ->join('m_district as f',function ($join){
                            $join->on('f.DIST_NO','=','a.dist_no')
                                 ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g',function ($join){
                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start >= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start < \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
                        ->where('a.st_code',$d->st_code)
                        ->where('a.dist_no','!=',0)
                        ->where('a.ac_no','!=','NULL')
                        ->where('a.dist_no','!=','')
                        ->where('a.ac_no','!=',0)
                        ->where('a.dist_no','!=','NULL')
                        ->where('a.ac_no','!=','')
						 ->where('a.cancel_status','!=',1)
                        ->where('a.dist_no',$req->dist)
                        ->where('a.ac_no',$req->ac)
                        ->groupBy('a.ac_no')
                ->get()->toArray();
                 }
                 else if($req->dist == 'all' && $req->ac == 'all'){
                     
                     $data=DB::table('permission_request as a')
                        ->join('m_district as f',function ($join){
                            $join->on('f.DIST_NO','=','a.dist_no')
                                 ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g',function ($join){
                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start >= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start < \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
						 ->where('a.cancel_status','!=',1)
                        ->where('a.st_code',$d->st_code)
                        ->groupBy('a.ac_no')
                ->get()->toArray();
                 }
                 else
                 {
                     $data=DB::table('permission_request as a')
                        ->join('m_district as f',function ($join){
                            $join->on('f.DIST_NO','=','a.dist_no')
                                 ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g',function ($join){
                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start >= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start < \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
                        ->where('a.st_code',$d->st_code)
                        ->where('a.dist_no','!=',0)
                        ->where('a.dist_no','!=','')
                        ->where('a.dist_no','!=','NULL')
						 ->where('a.cancel_status','!=',1)
                        ->where('a.dist_no',$req->dist)
                        ->groupBy('a.ac_no')
                        ->get()->toArray();
                 }
                if ($req->type == 'excel') {
                   $headings[] = ['District','AC','Pending_within_time', 'Pending_beyond_time', 'Accepted','Inprogress','Rejected','total'];
                   return Excel::download(new ExcelExport($headings,$data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                 }
                else
                {
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.permissionsummaryreportPDF', ['user_data' => $d, 'records' => $data]);
                       return $pdf->download('report' . $cur_time . '.pdf');

                }
            }
            else 
            {
                $data=DB::table('permission_request as a')
                        ->join('m_district as f',function ($join){
                            $join->on('f.DIST_NO','=','a.dist_no')
                                 ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g',function ($join){
                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
                        ->select('f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start >= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!=1 && date_time_start < \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
						 ->where('a.cancel_status','!=',1)
                        ->where('a.st_code',$d->st_code)
                        ->groupBy('a.ac_no')
                ->get()->toArray();
                if ($req->type == 'excel') {
                $headings[] = ['District','AC','Pending_within_time', 'Pending_beyond_time', 'Accepted','Inprogress','Rejected','total'];
                return Excel::download(new ExcelExport($headings,$data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
              }
                else
                {
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.permissionsummaryreportPDF', ['user_data' => $d, 'records' => $data]);
                       return $pdf->download('report' . $cur_time . '.pdf');

                }
            }
    }
    
    else {
            return redirect('/officer-login');
        }
    }
    
    public function ReportTimesview(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $cur_time = Carbon::now();
             $distvalue = DB::table('m_district')->where('ST_CODE', $d->st_code)->select('ST_CODE','DIST_NO','DIST_NAME')->get();
           if($req->method() == 'POST')
           {
            $dtSub = new DateTime($req->time.'hours');
            $date = $dtSub->format('Y-m-d H:m:s');
             $data1=DB::table('permission_request as a')
                       ->join('user_login as b','a.user_id','=','b.id')
                       ->join('user_data as ud','ud.user_login_id','=','b.id')
                       ->join('user_role as c','b.role_id','=','c.role_id')
                       ->join('permission_type as d','a.permission_type_id','=','d.id')
                       ->join('permission_master as m','m.id','=','d.permission_type_id')
                       ->select('a.reference_id','ud.name','m.permission_name as pname','a.permission_mode','c.role_name','a.added_at','a.approved_status')
                       ->whereIn('a.approved_status',[0,1])
                       ->where('a.st_code',$d->st_code);
                       if($req->dist != 'all' && $req->dist != 0)
                       {
                       $data1->where('a.dist_no',$req->dist);
                       }
                       if($req->ac != 'all' && $req->ac!= 0)
                       {
                       $data1->where('a.ac_no',$req->ac);
                       }
                       if($req->time != '0')
                       {
                       $data1->whereBetween('a.date_time_start',[$cur_time , $date]);
                       }
                       $data=$data1->get()->toArray();
               
               return view('admin.ac.ceo.Permission.CeoPermissionReport', ['report'=>$data,'user_data' => $d,'distvalue' => $distvalue,'time'=>$req->time,'ac_no'=>$req->ac,'dist_no'=>$req->dist]);
           }else
           {
                $data=DB::table('permission_request as a')
                       ->join('user_login as b','a.user_id','=','b.id')
                       ->join('user_data as ud','ud.user_login_id','=','b.id')
                       ->join('user_role as c','b.role_id','=','c.role_id')
                       ->join('permission_type as d','a.permission_type_id','=','d.id')
                       ->join('permission_master as m','m.id','=','d.permission_type_id')
                       ->select('a.reference_id','ud.name','m.permission_name as pname','a.permission_mode','c.role_name','a.added_at','a.approved_status')
                       ->whereIn('a.approved_status',[0,1])
                       ->where('a.st_code',$d->st_code)
                      ->get()->toArray();
                 return view('admin.ac.ceo.Permission.CeoPermissionReport', ['report'=>$data,'time'=>0,'ac_no'=>0,'dist_no'=>0,'user_data' => $d,'distvalue' => $distvalue]);
           }
        }
        else {
            return redirect('/officer-login');
        }
    }
    
    public function CustomTimeWiseReport(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $distvalue = DB::table('m_district')->where('ST_CODE', $d->st_code)->select('ST_CODE','DIST_NO','DIST_NAME')->get();
            if($req->method() == 'GET')
            {
            return view('admin.ac.ceo.Permission.CustomCeoPermissionReport', ['user_data' => $d,'distvalue' => $distvalue]);
            }
            else{
                $cur_time = Carbon::now();
                $cur_time1 = Carbon::now();
            $name_excel = 'timewise report'.'_'.$cur_time;
            $rules = [
                    'dist' => 'required|not_in:0',
                    'ac' => 'required|not_in:0',
                    'time' => 'required|numeric|min:1|max:100'
                ];
                $messages = [
                    'dist.required' => 'District is required.',
                    'ac.required' => 'AC is Required',
                    'time.required' => 'Time is required',
                    'dist.not_in' => 'Please select District.',
                    'ac.not_in' => 'Please select AC',
                    'time.numeric' => 'Please select correct time',
                    'time.min' => 'Please select time value greater than 0',
                    'time.max' => 'Please select time value less or equal than 100',
                ];
             $validator = Validator::make($req->all(), $rules, $messages);
             if ($validator->passes()) {
                 $dtSub = $cur_time1->subHours($req->time);
                 $date = $dtSub->format('Y-m-d H:m:s');
                 if($req->dist != 'all' && $req->ac != 'all')
                 {
                 $data1=DB::table('permission_request as a')
                                        ->join('user_login as b','a.user_id','=','b.id')
                                        ->join('user_data as ud','ud.user_login_id','=','b.id')
                                        ->join('user_role as c','b.role_id','=','c.role_id')
                                        ->join('permission_type as d','a.permission_type_id','=','d.id')
                                        ->join('permission_master as m','m.id','=','d.permission_type_id')
                                        ->select('a.reference_id','ud.name','m.permission_name as pname','a.permission_mode','c.role_name','a.added_at','a.approved_status')
                                        ->where('a.st_code',$d->st_code)
                                        ->where('a.dist_no',$req->dist)
                                        ->where('a.ac_no',$req->ac)
                                        ->whereIn('a.approved_status',[0,1])
                                        ->whereBetween('a.added_at',[$date,$cur_time])
                                        ->get()->toArray();
                 }
                 else if($req->dist == 'all' && $req->ac == 'all')
                 {
                     $data1=DB::table('permission_request as a')
                                        ->join('user_login as b','a.user_id','=','b.id')
                                        ->join('user_data as ud','ud.user_login_id','=','b.id')
                                        ->join('user_role as c','b.role_id','=','c.role_id')
                                        ->join('permission_type as d','a.permission_type_id','=','d.id')
                                        ->join('permission_master as m','m.id','=','d.permission_type_id')
                                        ->select('a.reference_id','ud.name','m.permission_name as pname','a.permission_mode','c.role_name','a.added_at','a.approved_status')
                                        ->where('a.st_code',$d->st_code)
//                                        ->where('a.dist_no',$req->dist)
//                                        ->where('a.ac_no',$req->ac)
                                        ->whereIn('a.approved_status',[0,1])
                                        ->whereBetween('a.added_at',[$date,$cur_time])
                                        ->get()->toArray();
                 }
                 else
                 {
                     $data1=DB::table('permission_request as a')
                                        ->join('user_login as b','a.user_id','=','b.id')
                                        ->join('user_data as ud','ud.user_login_id','=','b.id')
                                        ->join('user_role as c','b.role_id','=','c.role_id')
                                        ->join('permission_type as d','a.permission_type_id','=','d.id')
                                        ->join('permission_master as m','m.id','=','d.permission_type_id')
                                        ->select('a.reference_id','ud.name','m.permission_name as pname','a.permission_mode','c.role_name','a.added_at','a.approved_status')
                                        ->where('a.st_code',$d->st_code)
                                        ->where('a.dist_no',$req->dist)
//                                        ->where('a.ac_no',$req->ac)
                                        ->whereIn('a.approved_status',[0,1])
                                        ->whereBetween('a.added_at',[$date,$cur_time])
                                        ->get()->toArray();
                 }
             if ($req->input('excel')) {
                  if (!empty($_REQUEST['dist']) && ( !empty($_REQUEST['ac'])) && ( !empty($_REQUEST['time']))) {
                   
                    $headings[] = ['Reference_id','Applicant_name', 'Permission_type', 'Permission_mode','Applicant_Type','DateTime of Submission','Status'];
                                    
                                    $arr = array();
                                    foreach ($data1 as $record_data) {
                                        if ($record_data->permission_mode == 0) {
                                            $record_data->permission_mode = 'Offline';
                                        }
                                        else
                                        {
                                            $record_data->permission_mode = 'Online';
                                        }
                                        if($record_data->approved_status = 0)
                                        {
                                            $record_data->approved_status = 'Pending';
                                        }else
                                        {
                                            $record_data->approved_status = 'Inprogress';
                                        }
                                        
                                        $data = array(
                                            $record_data->reference_id,
                                            $record_data->name,
                                            $record_data->pname,
                                            $record_data->permission_mode,
                                            $record_data->role_name,
                                            $record_data->added_at,
                                            $record_data->approved_status,
                                        );
                                        array_push($arr, $data);
                                    }
//                                    Excel::create($name_excel, function($excel) use($arr) {
//
//                                    $excel->sheet('Sheetname', function($sheet) use($arr) {
//
//                                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
//                                    'Reference_id','Applicant_name', 'Permission_type', 'Permission_mode','Applicant_Type','DateTime of Submission','Status'
//                                        )
//                                );
//
//                                    });
//
//})->export('xlsx');
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                  }
             }else
             {
                 if (!empty($_REQUEST['dist']) && ( !empty($_REQUEST['ac'])) && ( !empty($_REQUEST['time']))) {
                    $pdf = PDF::loadView('admin.ac.ceo.Permission.customtimewisereport', ['user_data' => $d, 'records' => $data1]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                  }
             }
             }
             else
             {
                 return redirect()->back()->withErrors($validator, 'error')->withInput();
             }
            }
        }
    }
    
     public function PermissionSummaryReportDetails(Request $req)
{
    if (Auth::check()) {
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $cur_time = Carbon::now();
        $distvalue = DB::table('m_district')->where('ST_CODE', $d->st_code)->select('ST_CODE','DIST_NO','DIST_NAME')->get();
       
       $cur_time = Carbon::now();
        $data = DB::table('permission_request as a')
                   ->join('m_state as st','a.st_code','=','st.ST_CODE')
                    ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                    ->join('user_login as b','b.id','=','a.user_id')
                    ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                    ->join('permission_type as d','a.permission_type_id','=','d.id')
                   ->join('permission_master as m','m.id','=','d.permission_type_id')
                    ->join('m_party as p','a.party_id','=','p.CCODE')
                    ->join('user_role as c','b.role_id','=','c.role_id')
                    ->leftjoin('m_district as f',function ($join){
                        $join->on('f.DIST_NO','=','a.dist_no')
                             ->on('f.ST_CODE', '=', 'a.st_code');
                    })
                    ->leftjoin('m_ac as g',function ($join){
                        $join->on('g.AC_NO','=','a.ac_no')
                             ->on('g.ST_CODE', '=', 'a.st_code');
                    })
                     ->where('a.st_code',$d->st_code)
                    ->where('a.dist_no',$req->dist)
                    ->where('a.ac_no',$req->ac)
                    ->where('a.dist_no','!=',0)
                    ->where('a.ac_no','!=','NULL')
                    ->where('a.dist_no','!=','')
                    ->where('a.ac_no','!=',0)
                    ->where('a.dist_no','!=','NULL')
                    ->where('a.ac_no','!=','')
                    ->select('a.party_id','m.permission_name as pname','c.role_name','ud.name','p.PARTYNAME','f.DIST_NAME','g.AC_NAME','st.ST_NAME','a.reference_id','a.approved_status','a.cancel_status','a.permission_mode','a.added_at');
                     
                    if($req->type == '0')
                    {
                         $data->where('approved_status','=',0)->where('date_time_start', '>=', $cur_time);
                    }
                    elseif($req->type == '1')
                    {
                        $data->where('approved_status','=',0)->where('date_time_start', '<', $cur_time);
                    }
                    elseif($req->type == '2')
                    {
                        $data->where('approved_status','=',2);
                    }
                    elseif($req->type == '3')
                    {
                        $data->where('approved_status','=',1);
                    }
                    elseif($req->type == '4')
                    {
                        $data->where('approved_status','=',3);
                    }
                    $data->get();
                    $result=$data->get()->toArray();
                        return view('admin.ac.ceo.Permission.permissionsummaryreportdetails', ['distvalue' => $distvalue,'ac'=>$req->ac,'user_data' => $d,'countdetails'=>$result]);
    }
    else
    {
        return redirect('/officer-login');
    }
}
}

