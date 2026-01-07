<?php

namespace App\Http\Controllers\Admin\ECIReportSearch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\adminmodel\ReportModel;
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
use App\models\Admin\OfficerModel;

class ECIReportSearchController extends Controller
{	
	public function __construct() {
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('eci');
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
        $this->PM = new ReportModel();
    }

    protected function guard() {
        return Auth::guard();
    }

    public function ReportSearchByECI(Request $request){
    	if (Auth::check()):
    		$user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
           
            $getData = array();
            $search_by = $request->search_by;
           
            if($search_by !="0" && $search_by!=""){
            $key = $request->get('search');
            
                $results = array();
                if($request->search_by =='1') {
                    $results = DB::table('permission_request as a')
                        ->join('user_login as b','b.id','=','a.user_id')
                        ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                        ->join('permission_type as d','a.permission_type_id','=','d.id')
                        ->join('permission_master as m','m.id','=','d.permission_type_id')
                        ->join('m_state as e','e.ST_CODE','=','a.st_code')
                        ->join('m_party as p','a.party_id','=','p.CCODE')
                        ->leftjoin('permission_request_comment as prm','prm.permission_request_id','=','a.id')
                        ->join('m_ac as g',function ($join){
                            $join->on('g.AC_NO','=','a.ac_no')
                                 ->on('g.ST_CODE', '=', 'a.st_code');
                        })
                        ->join('m_district as h',function ($join){
                            $join->on('h.DIST_NO','=','a.dist_no')
                                 ->on('h.ST_CODE', '=', 'a.st_code');
                        })
                        
                        ->leftjoin('officer_login as ol', 'ol.id','=','a.created_by')

                      ->select('p.partyname','a.reference_id','a.date_time_start','a.date_time_end','approved_status','a.cancel_status','a.permission_mode','a.added_at as subdate','a.st_code as permisssionState','a.dist_no as permisssionDist','a.ac_no as permisssionAC','a.user_created_by as userCreatedBy','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','a.id as permission_id','b.id as login_id','prm.comment','g.AC_NAME','h.DIST_NAME','ol.name as assignedoffice_name','d.role_id as permission_type_role_id');

                    $results->orWhere('ud.mobileno',$key);
                    $results->orWhere('a.reference_id',$key);
                } elseif($request->search_by=='2') {

                    $results = DB::table('permission_request as a')
                            ->join('user_login as b','b.id','=','a.user_id')
                            ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                            ->join('permission_type as d','a.permission_type_id','=','d.id')
                            ->join('permission_master as m','m.id','=','d.permission_type_id')
                            ->join('m_state as e','e.ST_CODE','=','a.st_code')
                            ->join('m_party as p','a.party_id','=','p.CCODE')
                            ->leftjoin('permission_request_comment as prm','prm.permission_request_id','=','a.id')

                            ->join('permission_assigned_auth as paa', 'paa.permission_request_id','=','a.id')
                            ->join('authority_masters as am','am.id','=','paa.authority_id')
                            ->join('authority_masters_mapping as amm','amm.authority_masters_id','=','am.id')

                            ->join('m_ac as g',function ($join){
                                $join->on('g.AC_NO','=','a.ac_no')
                                     ->on('g.ST_CODE', '=', 'a.st_code');
                            })

                            ->join('m_district as h',function ($join){
                                $join->on('h.DIST_NO','=','a.dist_no')
                                     ->on('h.ST_CODE', '=', 'a.st_code');
                            })

                            ->leftjoin('officer_login as ol', 'ol.id','=','a.created_by')

                          ->select('p.partyname','a.reference_id','a.date_time_start','a.date_time_end','approved_status','a.cancel_status','a.permission_mode','a.added_at as subdate','a.st_code as permisssionState','a.dist_no as permisssionDist','a.ac_no as permisssionAC','a.user_created_by as userCreatedBy','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','a.id as permission_id','b.id as login_id','prm.comment','g.AC_NAME','h.DIST_NAME','am.st_code as nodal_st','am.name as nodal_name','am.department as nodal_department','am.designation as nodal_designation','am.mobile as nodal_mobile','am.email as nodal_email','am.address as nodal_address','amm.dist_no as nodal_dist','amm.ac_no as nodal_ac_no','amm.pc_no as nodal_pc_no','ol.name as assignedoffice_name','d.role_id as permission_type_role_id');

                   $results->orWhere('am.mobile',$key);
                }

                	$getData = $results->get()->toArray();
                }
    		      return view('admin.ac.eci.permission_reports.reports',['user_data' => $d,'results'=>$getData,'search_by'=>$search_by]);
    	else:
    		Auth::logout();
    	endif;	
    }

    
public static function getOfficerNameWithAC($st,$dist,$ac,$type){
        $getData = OfficerModel::where(['st_code'=>$st,'dist_no'=>$dist,'ac_no'=>$ac,'role_id'=>$type])->first();
        if(!empty($getData)){
            return $getData->officername;
        }
    }

  public static function getOfficerNameWithDist($st,$dist,$type){
        $getData = OfficerModel::where(['st_code'=>$st,'dist_no'=>$dist,'role_id'=>$type])->first();
        if(!empty($getData)){
            return $getData->officername;
        }
    }

   public static function getOfficerNameWithState($st,$type){
        $getData = OfficerModel::where(['st_code'=>$st,'role_id'=>$type])->first();
        if(!empty($getData)){
            return $getData->officername;
        }
    } 

}
