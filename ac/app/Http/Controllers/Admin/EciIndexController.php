<?php
    namespace App\Http\Controllers\Admin;
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
	use App\models\Admin\IndexCardFinalize;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;

    

class EciIndexController extends Controller
{

   public function __construct(){   
        $this->commonModel = new commonModel();
    }

   public function dashboard(){ 
        
    $user = \Auth::user();
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
           
            $module=$this->commonModel->getallmodule();
			
			$object_states = IndexCardFinalize::get_states();
			
			$sql_raw = "SELECT COUNT(p.AC_NO) AS total_ac, COUNT(IF(w.finalize='1',1,NULL)) AS FinalizeRo,COUNT(IF(w.finalize_by_ceo='1',1,NULL)) AS FinalizeCeo
			FROM m_ac AS p  LEFT JOIN electors_cdac_other_information AS w ON (p.AC_NO = w.ac_no AND p.ST_CODE = w.st_code) LEFT JOIN m_election_details AS med ON (p.ST_CODE = med.ST_CODE AND p.AC_NO = med.CONST_NO) INNER JOIN m_state AS s ON (p.ST_CODE = s.ST_CODE)
			WHERE med.CONST_TYPE = 'AC' AND med.election_status != 0 limit 1";

			$data = DB::select($sql_raw);
			
			if($data[0]->FinalizeRo > 0){
				$ro_progress_bar = (int)round(($data[0]->FinalizeRo/$data[0]->total_ac)*100);
			}else{
				$ro_progress_bar = 0;
			}
			if($data[0]->FinalizeCeo > 0){
				$ceo_progress_bar = (int)round(($data[0]->FinalizeCeo/$data[0]->total_ac)*100);
			}else{
				$ceo_progress_bar = 0;
			}
			
			$data = array();
			
			$data['ro'] = $ro_progress_bar;
			$data['ceo'] = $ceo_progress_bar;
			
			
			$stateList = DB::table('m_state')
			->select('m_state.ST_NAME','srvd.is_verified','srvd.verifiat_date')
			->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
			->leftjoin('statical_report_verification_details as srvd',[['m_state.ST_CODE','srvd.ST_CODE'],['srvd.report_no',DB::Raw('7777')]])
			->where('m_election_details.CONST_TYPE','AC')
			->where('m_election_details.ELECTION_TYPE','GENERAL')
			->orderBy('m_state.ST_NAME','ASC')
			->groupBy('m_state.ST_CODE')
			->get()->toArray();
			
			$publishReportList = DB::table('m_state')
			->select('m_state.ST_NAME','srvd.is_verified','srvd.verifiat_date')
			->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
			->leftjoin('statical_report_verification_details as srvd',[['m_state.ST_CODE','srvd.ST_CODE'],['srvd.report_no',DB::Raw('8888')]])
			->where('m_election_details.CONST_TYPE','AC')
			->where('m_election_details.ELECTION_TYPE','GENERAL')
			->orderBy('m_state.ST_NAME','ASC')
			->groupBy('m_state.ST_CODE')
			->get()->toArray();
			
			//dd($stateList);
			
             return view('IndexCardReports.dashboard', ['user_data' => $d,'results' => $object_states,'data' => $data,'stateList' => $stateList,'publishReportList' => $publishReportList]);
             
  
  
        }   // end dashboard function
		
	 

}  // end class