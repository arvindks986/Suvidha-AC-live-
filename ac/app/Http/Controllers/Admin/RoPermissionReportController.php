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
class RoPermissionReportController extends Controller {
    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
    }
    public function TimeWiseReport(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $distvalue = DB::table('m_district')->where('ST_CODE', $d->st_code)->select('ST_CODE','DIST_NO','DIST_NAME')->first();
            $acvalue = DB::table('m_ac')->select('AC_NO','AC_NAME')->where(array('DIST_NO_HDQTR'=>$d->dist_no,'ST_CODE'=>$d->st_code,'AC_NO'=>$d->ac_no))->first();
        return view('admin.ac.ro.Permission.RoPermissionReport', ['user_data' => $d,'distvalue' => $distvalue,'acdata'=>$acvalue]);
        }
    }
    public function ReportTimes(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $cur_time = Carbon::now();
            $name_excel = 'timewise report'.'_'.$cur_time;
            $rules = [
                    'dist' => 'required|not_in:0',
                    'ac' => 'required|not_in:0',
                    'time' => 'required|not_in:0'
                ];
                $messages = [
                    'dist.required' => 'District is required.',
                    'ac.required' => 'AC is Required',
                    'time.required' => 'Time is required',
                    'dist.not_in' => 'Please select District.',
                    'ac.not_in' => 'Please select AC',
                    'time.not_in' => 'Please select Time'
                ];
             $validator = Validator::make($req->all(), $rules, $messages);
             if ($validator->passes()) {
                 $dtSub = new DateTime($req->time.'hours');
                 $date = $dtSub->format('Y-m-d H:m:s');
//                 dd($date);
                 $data1=DB::table('permission_request as a')
                                        ->join('user_login as b','a.user_id','=','b.id')
                                        ->join('user_data as ud','ud.user_login_id','=','b.id')
                                        ->join('user_role as c','b.role_id','=','c.role_id')
                                        ->join('permission_type as d','a.permission_type_id','=','d.id')
                                        ->join('permission_master as m','m.id','=','d.permission_type_id')
                                        ->select('a.reference_id','ud.name','m.permission_name as pname','a.permission_mode','c.role_name','a.added_at','a.approved_status')
                                        ->where('a.st_code',$d->st_code)
                                        ->where('a.dist_no',$d->dist_no)
                                        ->where('a.ac_no',$d->ac_no)
                                        ->whereIn('a.approved_status',[0,1])
                                        ->whereBetween('a.date_time_end',[$date,$cur_time])
                                        ->get()->toArray();
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
                                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                  }
             }else
             {
                 if (!empty($_REQUEST['dist']) && ( !empty($_REQUEST['ac'])) && ( !empty($_REQUEST['time']))) {
                    $pdf = PDF::loadView('admin.ac.ro.Permission.timewisereport', ['user_data' => $d, 'records' => $data1]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                  }
             }
             }
             else
             {
                 return redirect()->back()->withErrors($validator, 'error')->withInput();
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
            
            $name_excel = 'Permission_Summary_report'.'_'.$cur_time;
//            $rules = [
//                    'time' => 'required|not_in:0',
//                ];
//                $messages = [
//                    'time.required' => 'Please select Time interval',
//                    'time.not_in' => 'Please select Time interval.',
//                ];
//             $validator = Validator::make($req->all(), $rules, $messages);
//             if ($validator->passes()) {
                $data=DB::table('permission_request as a')
                        ->join('m_district as f',function ($join){
                            $join->on('f.DIST_NO','=','a.dist_no')
                                 ->on('f.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_ac as g',function ($join){
                            $join->on('g.AC_NO','=','a.ac_no')
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
//                        ->join('m_ac as g',function ($join){
//                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
//                                 ->on('g.ST_CODE', '=', 'a.st_code');
//                        })
                        ->select('f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && date_time_end <= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && date_time_end >= \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
                        ->where('a.st_code',$d->st_code)
                        ->where('a.ac_no',$d->ac_no)
                        ->where('a.dist_no','!=',0)
                        ->where('a.ac_no','!=','NULL')
                        ->where('a.dist_no','!=','')
                        ->where('a.ac_no','!=',0)
                        ->where('a.dist_no','!=','NULL')
                        ->where('a.ac_no','!=','')
//                        ->groupBy('a.dist_no')
                        ->groupBy('a.ac_no')
                //->groupBy('approved_status')
                ->get()->toArray();
//                dd($data);
             if ($req->input('excel')) {
                    $headings[] = ['District','AC','Pending_within_time', 'Pending_beyond_time', 'Accepted','Inprogress','Rejected','total'];
                    return Excel::download(new ExcelExport($headings,$data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                  }
             else
             {
                 $pdf = PDF::loadView('admin.ac.ro.Permission.permissionsummaryreportPDF', ['user_data' => $d, 'records' => $data]);
                    return $pdf->download('report' . $cur_time . '.pdf');
                    
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
                            $join->on('g.AC_NO','=','a.ac_no')
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
//                        ->join('m_ac as g',function ($join){
//                            $join->on(\DB::raw("FIND_IN_SET(g.AC_NO,a.ac_no)"),">",\DB::raw("'0'"))
//                                 ->on('g.ST_CODE', '=', 'a.st_code');
//                        })
                        ->select('f.DIST_NAME',DB::raw("GROUP_CONCAT(DISTINCT g.AC_NAME SEPARATOR ',') as 'ac_name'"),
                         DB::raw('sum(CASE WHEN (approved_status = 0 && date_time_end <= \''.$cur_time.'\')  THEN 1 ELSE 0 END) as Pending_within_time'),
                        DB::raw('sum(CASE WHEN (approved_status = 0 && date_time_end >= \''.$cur_time.'\')  THEN 1  ELSE 0 END) as Pending_beyond_time'),
                        DB::raw('sum(CASE WHEN approved_status = 2 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN approved_status = 1 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN approved_status = 3 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total')
                        )
                        ->where('a.st_code',$d->st_code)
                        ->where('a.ac_no',$d->ac_no)
                        ->where('a.dist_no','!=',0)
                        ->where('a.ac_no','!=','NULL')
                        ->where('a.dist_no','!=','')
                        ->where('a.ac_no','!=',0)
                        ->where('a.dist_no','!=','NULL')
                        ->where('a.ac_no','!=','')
//                        ->groupBy('a.dist_no')
                        ->groupBy('a.ac_no')
                //->groupBy('approved_status')
                ->get()->toArray();
            return view('admin.ac.ro.Permission.permissionsummaryreport', ['user_data' => $d,'distvalue' => $distvalue,'summarydata'=>$summarydata]);
        }
        }
        else {
            return redirect('/officer-login');
        }
    }
}

