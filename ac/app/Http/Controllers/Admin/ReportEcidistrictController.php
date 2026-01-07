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
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportEcidistrictController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('eci');
        $this->commonModel = new commonModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

    public function districtreport() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $statevalue = StateModel::get_states();
            return view('admin.ac.eci.reportdistrict', ['state' =>0,'election'=>0,'datefilter'=>0,'statevalue' => $statevalue, 'user_data' => $d]);
        }
    }

    public function districtwisereport(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $cur_time = Carbon::now();
           
            $name_excel = 'District wise report'.'_'.$cur_time;
            $headings[] = ['State Name','District Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogress', 'Pending', 'Cancel'];
             $datevalue = $req->input('datefilter');
            if(!empty($datevalue))
            {
            $dates = explode("~", $datevalue);
            $dte1 = $dates[0];
            $dte2 = $dates[1];
            }
            $data = DB::table('permission_request as a')
                               ->join('m_state as st','a.st_code','=','st.ST_CODE')
                               ->join('m_district as dt',function($join)
                               {
                                   $join->on('a.st_code','=','dt.ST_CODE')
                                        ->on('a.dist_no','=','dt.DIST_NO');
                               })
                               ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                               ->select('st.ST_NAME','dt.DIST_NAME',DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                              
                               ->groupBy('a.dist_no');
                               if(!empty($_REQUEST['elect']))
                               {
                                    $data->where('med.ELECTION_TYPEID',$_REQUEST['elect']);
                               }
                               if(!empty($_REQUEST['state']))
                               {
                                    $data->where('a.st_code',$_REQUEST['state']);
                               }
                               if(!empty($_REQUEST['datefilter']))
                               {
                                    $data->whereBetween('a.created_at',[$dte1,$dte2]);
                               }
                               $data->get();
                                $result=$data->get()->toArray();
            if ($req->input('excel')) {
                return Excel::download(new ExcelExport($headings, $result), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                
            } else {
                $pdf = PDF::loadView('admin.ac.eci.reportdistrictlist', ['user_data' => $d, 'records' => $result]);
                    return $pdf->download('report' . $cur_time . '.pdf');
            }
        }
    }
    
    public function districtwisereportview(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $cur_time = Carbon::now();
            $statevalue = StateModel::get_states();
            $datevalue = $req->input('datefilter');
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
            if($req->method() == 'POST')
                {
                $data = DB::table('permission_request as a')
                               ->join('m_state as st','a.st_code','=','st.ST_CODE')
                               ->join('m_district as dt',function($join)
                               {
                                   $join->on('a.st_code','=','dt.ST_CODE')
                                        ->on('a.dist_no','=','dt.DIST_NO');
                               })
                               ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                               ->select('a.st_code','a.dist_no','st.ST_NAME','dt.DIST_NAME',DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                              
                               ->groupBy('a.dist_no','a.st_code');
                               if(!empty($_REQUEST['elect']))
                               {
                                    $data->where('med.ELECTION_TYPEID',$_REQUEST['elect']);
                               }
                               if(!empty($_REQUEST['state']))
                               {
                                    $data->where('a.st_code',$_REQUEST['state']);
                               }
                               if(!empty($_REQUEST['datefilter']))
                               {
                                    $data->whereBetween('a.created_at',[$dte1,$dte2]);
                               }
                               $data->get();
                                $result=$data->get()->toArray();
                                return view('admin.ac.eci.reportdistrict', ['election'=>$_REQUEST['elect'],'state' => $_REQUEST['state'], 'user_data' => $d,'datereport'=>$result,'datefilter'=>$dt,'statevalue' => $statevalue]);
                } 
                else {
              return view('admin.ac.eci.reportdistrict', ['state' =>0,'election'=>0,'datefilter'=>0,'statevalue' => $statevalue, 'user_data' => $d]);
            }
        }
    }

}

// end class