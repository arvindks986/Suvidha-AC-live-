<?php

namespace App\Http\Controllers\Admin;

use App\adminmodel\ReportModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use App\commonModel;
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class PermissiontypeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('eci');
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
        $this->PM = new ReportModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }
public function report() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $statevalue = StateModel::get_states();
            return view('admin.ac.eci.report', ['election'=>0,'datefilter'=>0,'state'=>0,'statevalue' => $statevalue, 'user_data' => $d]);
        }
    }
    public function partywise(Request $req) {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $cur_time    = Carbon::now();
            $data = DB::table('permission_request as a')
                    ->join('permission_type as t','t.id','=','a.permission_type_id')
                    ->join('permission_master as m','m.id','=','t.permission_type_id')
                    ->join('m_party as mp','mp.CCODE','=','a.party_id')
                    ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                    ->select('a.party_id','PARTYNAME','permission_name',DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                    ->groupBy('permission_name','PARTYNAME');
                    if(!empty($_REQUEST['elect']))
                    {
                         $data->where('med.ELECTION_TYPEID',$_REQUEST['elect']);
                    } 
                    $data->get();
                    $result=$data->get()->toArray();
           
            if($req->method() == 'POST')
            {
                if ($req->input('excel')) {
                     $name_excel = 'party wise report'.'_'.$cur_time;
                     $headings[] = ['Party Name', 'Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogess', 'Pending', 'Cancel'];
                    return Excel::download(new ExcelExport($headings, $result), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                }
                else
                {
                    return view('admin.ac.eci.partywisepermissionreport', ['election'=>$_REQUEST['elect'],'user_data' => $d,'partyreport'=>$result]);
                }
            }
            else
            {
                return view('admin.ac.eci.partywisepermissionreport', ['election'=>0,'user_data' => $d,'partyreport'=>$result]);
            }
        } else {
            return redirect('/officer-login');
        }
    }
public function permissiontypes(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $cur_time = Carbon::now();
           
            $name_excel = 'District wise report'.'_'.$cur_time;
            $headings[] = ['State Name','Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
             $datevalue = $req->input('datefilter');
            if(!empty($datevalue))
            {
            $dates = explode("~", $datevalue);
            $dte1 = $dates[0];
            $dte2 = $dates[1];
            }
            $data = DB::table('permission_request as a')
                    ->join('permission_type as t','t.id','=','a.permission_type_id')
                    ->join('permission_master as m','m.id','=','t.permission_type_id')
                    ->join('m_state as sm','sm.ST_CODE','=','a.st_code')       
                    ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                    ->select('sm.ST_NAME','permission_name',DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                    ->groupBy('permission_name');
                    if(!empty($_REQUEST['elect']))
                    {
                         $data->where('med.ELECTION_TYPEID',$_REQUEST['elect']);
                    } 
                    if(!empty($_REQUEST['state']))
                        {
                          $data->where('a.st_code',$_REQUEST['state']);
                        }
                    $data->get();
                    $result=$data->get()->toArray();
            if ($req->input('excel')) {
                return Excel::download(new ExcelExport($headings, $result), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                
            } else {
                $pdf = PDF::loadView('admin.ac.eci.reportpagetotal_perm', ['user_data' => $d, 'records' => $result]);
                    return $pdf->download('report' . $cur_time . '.pdf');
            }
        }
    }
    public function permissiontype(Request $req) {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $statevalue = StateModel::get_states();
            $datevalue = $req->input('datefilter');
            $statecode =$req->input('state');
            if(!empty($datevalue))
            {
            $dates = explode("~", $datevalue);
            $dte1 = $dates[0];
            $dte2 = $dates[1];
            }
            $dt=0;
            if(!empty($_REQUEST['datefilter']))
            {
                $dt = $_REQUEST['datefilter'];
            }
           $cur_time    = Carbon::now();
           $data = DB::table('permission_request as a')
                    ->join('permission_type as t','t.id','=','a.permission_type_id')
                    ->join('permission_master as m','m.id','=','t.permission_type_id')
                    ->join('m_state as sm','sm.ST_CODE','=','a.st_code') 
                    ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                    ->select('sm.ST_NAME','permission_name',DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                    ->groupBy('permission_name');
                    if(!empty($_REQUEST['elect']))
                    {
                         $data->where('med.ELECTION_TYPEID',$_REQUEST['elect']);
                    } 
                    if(!empty($_REQUEST['state']))
                        {
                          $data->where('a.st_code',$_REQUEST['state']);
                        }
                    $data->get();
                    $result=$data->get()->toArray();
           
            if($req->method() == 'POST')
            {
                if ($req->input('excel')) {
                     $name_excel = 'Permission wise report'.'_'.$cur_time;
                     $headings[] = ['Permission Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogess', 'Pending', 'Cancel'];
                    return Excel::download(new ExcelExport($headings, $result), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                }
                else
                {
                    return view('admin.ac.eci.permissionwisereport', ['election'=>$_REQUEST['elect'],'user_data' => $d,'permissionwisereport'=>$result,'datefilter'=>$dt,'statevalue' => $statevalue,'state'=>$statecode]);
                }
            }
            else
            {
                return view('admin.ac.eci.permissionwisereport', ['election'=>0,'user_data' => $d,'permissionwisereport'=>$result,'datefilter'=>0,'statevalue' => $statevalue,'state'=>0]);
            }
            
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function partywisedetails(Request $req) {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $details = request()->segments();
            $ele = $details[2];
            $pid = $details[3];
            $pname = $details[4];
            $status = $details[5];
            $data = DB::table('permission_request as a')
//                    ->join('permission_type as t','t.id','=','a.permission_type_id')
//                    ->join('permission_master as m','m.id','=','t.permission_type_id')
//                    ->join('m_party as mp','mp.CCODE','=','a.party_id')
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
                    ->select('a.party_id','m.permission_name as pname','c.role_name','ud.name','p.PARTYNAME','f.DIST_NAME','g.AC_NAME','st.ST_NAME','a.reference_id','a.approved_status','a.cancel_status','a.permission_mode','a.added_at');
                    if(!empty($ele) && $ele != '0')
                    {
                         $data->where('med.ELECTION_TYPEID',$ele);
                    }
                    if(!empty($pid) && $pid != '0')
                    {
                         $data->where('a.party_id',$pid);
                    }
                    if(!empty($pname))
                    {
                         $data->where('m.permission_name',$pname);
                    }
                     if($status != '6' && $status != '5')
                    {
                        $data->where('a.approved_status',$status)->where('a.cancel_status',0);
                    }
                    elseif($status == '5')
                    {
                        $data->where('a.cancel_status',1);
                    }
                    $data->get();
                    $result=$data->get()->toArray();
                return view('admin.ac.eci.partywisepermissionreportdetails', ['election'=>0,'user_data' => $d,'partyreport'=>$result]);
           
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function permissionwisedetails(Request $req) {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
           $cur_time    = Carbon::now();
           $details = request()->segments();
            $ele = $details[2];
            $pname = $details[3];
            $status = $details[4];
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
                    ->select('a.party_id','m.permission_name as pname','c.role_name','ud.name','p.PARTYNAME','f.DIST_NAME','g.AC_NAME','st.ST_NAME','a.reference_id','a.approved_status','a.cancel_status','a.permission_mode','a.added_at');
                    if(!empty($ele) && $ele != '0')
                    {
                         $data->where('med.ELECTION_TYPEID',$ele);
                    }
                    if(!empty($pname))
                    {
                         $data->where('m.permission_name',$pname);
                    }
                     if($status != '6' && $status != '5')
                    {
                        $data->where('a.approved_status',$status)->where('a.cancel_status',0);
                    }
                    elseif($status == '5')
                    {
                        $data->where('a.cancel_status',1);
                    }
                    $data->get();
                    $result=$data->get()->toArray();
                    return view('admin.ac.eci.permissionwisepermissionreportdetails', ['election'=>$ele,'user_data' => $d,'permissionwisereport'=>$result]);
               
        } else {
            return redirect('/officer-login');
        }
    }
}

// end class