<?php 
namespace App\Http\Controllers\Admin; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon; 
use \PDF;
use App\commonModel; 
use App\models\Admin\StateModel;
use App\Exports\ExcelExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportEcicategorywiseController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $commonModel = null;
    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('eci');
        $this->commonModel = new commonModel();
        $this->StateModel= new StateModel();
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
            $getAllPermissiontype = DB::table('permission_master')
                                ->select('*')
                                ->get()->toArray();
            $statevalue = StateModel::get_states();
            return view('admin.ac.eci.reportdistrict', ['state' =>0,'election'=>0,'datefilter'=>0,'statevalue' => $statevalue, 'getAllPermissiontype'=>$getAllPermissiontype,'user_data' => $d]);
        }
    }

    public function categorywisewisereport(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $cur_time = Carbon::now();
             $statevalueac = $this->StateModel->get_stateswithac();
            $getAllPermissiontype = DB::table('permission_master')
                                ->select('*')
                                ->get()->toArray();
            $name_excel = 'District wise report'.'_'.$cur_time;
            $headings[] = ['Permission Name', 'Chhattisgarh', 'Madhya Pradesh', 'Mizoram', 'Rajasthan', 'Telangana', 'Total request'];

             $datevalue = $req->input('datefilter');
              $elect='3';
            if(!empty($datevalue))
            {
            $dates = explode("~", $datevalue);
            $dte1 = $dates[0];
            $dte2 = $dates[1];
            }
            $data = DB::table('permission_request as a')
                               ->join('m_state as st','a.st_code','=','st.ST_CODE')
                               ->join('permission_master as pm','a.permission_type_id','=','pm.id')
                               
                               ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                             ->select( 'pm.permission_name', 'a.dist_no','st.ST_NAME',
                                DB::raw('SUM(CASE WHEN a.st_code = "S26" AND a.st_code = "S26" THEN 1 ELSE 0 END) AS PS26'),
                                DB::raw('SUM(CASE WHEN a.st_code = "S12" AND a.st_code = "S12" THEN 1 ELSE 0 END) AS PS12'),
                                DB::raw('SUM(CASE WHEN a.st_code = "S16" AND a.st_code = "S16" THEN 1 ELSE 0 END) AS PS16'),
                                DB::raw('SUM(CASE WHEN a.st_code = "S20" AND a.st_code = "S20" THEN 1 ELSE 0 END) AS PS20'),
                                DB::raw('SUM(CASE WHEN a.st_code = "S29" AND a.st_code = "S29" THEN 1 ELSE 0 END) AS PS29'),
                                
                                DB::raw('count(*) as Total'))
                              
                               ->groupBy('pm.permission_name');
                               if(!empty($_REQUEST['elect']))
                               {
                                    $data->where('med.ELECTION_TYPEID',$elect);
                               }
                               if(!empty($_REQUEST['pname']) && $_REQUEST['pname']!='all')
                               {
                                    $data->where('a.permission_type_id',$_REQUEST['pname']);
                               }
                               
                               $data->get();
                                $result=$data->get()->toArray();
                                //dd($result);
            if ($req->input('excel')) {
                return Excel::download(new ExcelExport($headings, $result), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                
            } else {
                $pdf = PDF::loadView('admin.ac.eci.reportpermission_cat_wisepdf', ['user_data' => $d,'datereport'=>$result,'statevalueac' => $statevalueac ]);
                    return $pdf->download('report' . $cur_time . '.pdf');
            }
        }
    }
    
    public function categorywisereportview(Request $req) {
           //dd($req->pname);
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $cur_time = Carbon::now();
            $statevalue = StateModel::get_states();
            $elect='3';
           
            $statevalueac = $this->StateModel->get_stateswithac();

      
            
             $getAllPermissiontype = DB::table('permission_master')->select('*')->get()->toArray();

             
              
                $data = DB::table('permission_request as a')
                               ->join('m_state as st','a.st_code','=','st.ST_CODE')
                               ->join('permission_master as pm','a.permission_type_id','=','pm.id')
                               
                               ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                             ->select('a.st_code', 'pm.permission_name', 'a.dist_no','st.ST_NAME',
                                DB::raw('SUM(CASE WHEN a.st_code = "S26" AND a.st_code = "S26" THEN 1 ELSE 0 END) AS PS26'),
                                DB::raw('SUM(CASE WHEN a.st_code = "S12" AND a.st_code = "S12" THEN 1 ELSE 0 END) AS PS12'),
                                DB::raw('SUM(CASE WHEN a.st_code = "S16" AND a.st_code = "S16" THEN 1 ELSE 0 END) AS PS16'),
                                DB::raw('SUM(CASE WHEN a.st_code = "S20" AND a.st_code = "S20" THEN 1 ELSE 0 END) AS PS20'),
                                DB::raw('SUM(CASE WHEN a.st_code = "S29" AND a.st_code = "S29" THEN 1 ELSE 0 END) AS PS29'),
                                
                                DB::raw('count(*) as Total'))
                              
                               ->groupBy('pm.permission_name');
                               if(!empty($_REQUEST['elect']))
                               {
                                    $data->where('med.ELECTION_TYPEID',$elect);
                               }
                               if(!empty($_REQUEST['pname']) && $_REQUEST['pname']!='all')
                               {
                                    $data->where('a.permission_type_id',$_REQUEST['pname']);
                               }
                               
                               $data->get();
                               $result=$data->get()->toArray();
                               return view('admin.ac.eci.reportpermission_cat_wise', ['election'=>$elect, 'user_data' => $d,'datereport'=>$result, 'getAllPermissiontype'=>$getAllPermissiontype,'pname' =>$req->pname,'statevalueac' => $statevalueac, 'statevalue' => $statevalue]);
                              
               
        }
    }

}

// end class