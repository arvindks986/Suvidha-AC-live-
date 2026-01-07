<?php

namespace App\Http\Controllers\IndexCardReportsAC\IndexCardReport;

    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Carbon\Carbon;
	use App\models\Admin\{ElectionModel, StateModel};
	use App\models\Admin\IndexCardDeFinalizeModel;
    use DB;
    use Illuminate\Support\Facades\Hash;
    use Validator;
    use Config;
    use \PDF;
    use MPDF;
    use App;
    use App\commonModel;  
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\ACCEOModel;
	use App\adminmodel\ACCEOReportModel;
    use App\Classes\xssClean;
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Crypt;

class StatisticalReportListingController  extends Controller
{
	
		public $base          = '';
		public $folder        = '';
		public $action        = '';
		public $current_page  = '';
		public $ac_no         = 0;
		public $st_code       = 0;
		public $view_path     = "IndexCardReports.ByeElectionIndexCard";
		public $definalize_access = false;

    public function __construct(){
    $role_id = 0;
    $this->xssClean = new xssClean;
	$this->commonModel = new commonModel();
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        $role_id = Auth::user()->role_id;
         if($role_id == '27'){
          $this->base         = 'eci-index';
          $this->action       = 'eci-index/bye-election-verify-report/post';
          $this->current_page = 'eci-index/bye-election-verify-report';
          $this->definalize_action = 'eci-index/bye-election-verify-report/post';
          $this->view_path    = '';
        } else{
          $this->base         = 'eci';
          $this->action       = 'eci/bye-election-verify-report/post';
          $this->current_page = 'eci/bye-election-verify-report';
          $this->definalize_action = 'eci/bye-election-verify-report/post';
          $this->view_path    = '';
        }

        if(in_array($role_id,['7','27'])){
          $this->definalize_access = true;
        }

        return $next($request);
    });
  }

    public function statisticalreportlist(){

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
        
        $session['election_detail'] = array();
        
		
		$stateList = DB::table('m_state')
			->select('m_state.ST_CODE','m_state.ST_NAME')
			->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
			->where('m_election_details.CONST_TYPE','AC')
			//->where('m_election_details.election_id','1')
			->where('m_election_details.ELECTION_TYPE','GENERAL')
			->orderBy('m_state.ST_NAME','ASC')
			->groupBy('m_state.ST_CODE')
			->get()->toArray();
		
        $user_data = $d;
		
		
		//echo '<pre>'; print_r($stateList); die;

        return view("IndexCardReports.IndexCardReports.statistical-report-listing", compact('user_data','stateList'));
    }
	
	
	
	public function verifyallreport(Request $request){
		//dd($request);
       $report_no = $request->report_number;
	   $st_code = $request->st_code;
       $date = date('Y-m-d H:i:s');
	   $ip = get_client_ip();
       $number = 'ST'.rand(1000,9999);

       $updateData = [
        'is_verified' => '1',
        'verifiat_date' => $date,
       'report_sequence' => $number,
       ];
   
      $insertDatatologs = [
        'report_no' => $report_no,
        'download_time' => $date,
        'file_name' => $number,
		'st_code' => $st_code,
		'user_ip' => $ip,
      ];
	  
	   $insertData = [
        'report_no' => $report_no,
        'verifiat_date' => $date,
       'report_sequence' => $number,
	   'is_verified' => '1',
	   'st_code' => $st_code,
      ];
	  
	  
	  $data = DB::table('statical_report_verification_details')->select('st_code','report_no')
		 ->where('st_code',$st_code)
		 ->where('report_no', $report_no)
		 ->get();
		 
		 
		   if(count($data) > 0){
		  $query = DB::table('statical_report_verification_details')
                ->where('report_no', $report_no)
				->where('st_code', $st_code)
                ->update($updateData);
			}else{
		  $query = DB::table('statical_report_verification_details')
               
                ->insert($insertData);
				 }

     
			if($query){
				DB::table('statical_report_download_logs')->insert($insertDatatologs);
				$msg = 'Success';
			}else{
				$msg = 'Fail';
			}

			return response()->json(array('msg'=> $msg), 200);

    }
	
	
	
		 public function verifyreportcheckbox(Request $request){
		 

         //dd($request);
		 $st_code = $request->st_code;
		 $report_no = $request->report_no;
		 $is_verified = $request->is_verified;
		 $date = date('Y-m-d H:i:s');
		 
		 
		 $data = DB::table('statical_report_verification_details')->select('st_code','report_no')
		 ->where('st_code',$st_code)
		 ->where('report_no', $report_no)
		 ->get();
		 
		 
		 //echo "<pre>"; print_r($data); die;
		 
		 $insertData = [
          'is_verified' => $is_verified,
          'verifiat_date' => $date,
          'report_no' => $report_no,
		  'st_code' => $st_code,
		];
		
		$updateData = [
          'is_verified' => $is_verified,
		  'verifiat_date' => $date,
          
		];
	  
	  if(count($data) > 0){
		  $query = DB::table('statical_report_verification_details')
                ->where('report_no', $report_no)
				->where('st_code', $st_code)
                ->update($updateData);
				
		 
				
	  }else{
		  $query = DB::table('statical_report_verification_details')
               
                ->insert($insertData);
				
	
				
			
	  }
	  
	   if($query){
          $msg = 'Success';
          $queryinsert = DB::table('statical_report_verification_details_logs')
                
                ->insert($insertData);
        }else{
          $msg = 'Fail';
        }
		
		 return response()->json(array('msg'=> $msg), 200);
	 
    }
	
	
	
	
	
	// Bye Election Index-Card Report Start
	

	public function indexcardreportlist(Request $request){

        $data                   = [];
	  $filter                 = [];
	  $data['ac_no']          = NULL;
	  $data['st_code']        = NULL;
	  $data['election_id']    = NULL;
	  $data['custom_errors']  = [];
      if($request->has('election_id')){
        $data['election_id']       = $request->election_id;
        $filter['election_id']     = $data['election_id'];
      }

      if($request->has('ac_no')){
        $data['ac_no']       = $request->ac_no;
      } 

      if($request->has('st_code')){
        $data['st_code']       = $request->st_code;
      }     

      if(\Auth::user()->role_id == '18'){
        $data['ac_no']          = $this->ac_no;
        $data['st_code']        = $this->st_code;
        $filter['ac_no']        = $data['ac_no'];
        $filter['st_code']      = $data['st_code'];
      }

      if(\Auth::user()->role_id == '4'){
        $data['st_code']        = $this->st_code;
        $filter['st_code']      = $data['st_code'];
      }
      
      $data['action']         = url($this->action);
      $data['current_page']   = url($this->current_page);

      $data['heading_title']  = 'Index Card';
      $data['filter_buttons'] = [];

      //years
      $data['elections']      = [];
      $elections              = ElectionModel::get_current_elections();
      foreach ($elections as $key => $result) {
        $data['elections'][] = [
           'election_id'      => $result['ELECTION_ID'],
           'election_type'    => $result['ELECTION_TYPE'].'-'.$result['YEAR'],
        ];
      }

      $data['states'] = [];
      $states = StateModel::get_states_index_bye();
      foreach ($states as $key => $iterate_state) {
         $data['states'][] = [
           'st_code' => $iterate_state->ST_CODE,
           'st_name' => $iterate_state->ST_NAME,
         ];
      }

      $acs          = [];
      foreach (StateModel::get_acs_bye(['st_code' => $data['st_code']]) as $ac_result){
		  if(\Auth::user()->role_id == '18'){    
			if($ac_result['ac_no']==$this->ac_no){
				$acs[] = [
				  'ac_no'   => $ac_result['ac_no'],
				  'ac_name' => $ac_result['ac_name']
				];
			}		  
		  }else{
			  $acs[] = [
				  'ac_no'   => $ac_result['ac_no'],
				  'ac_name' => $ac_result['ac_name']
				];
		  }
      }
	  
	  //echo '<pre>'; print_r($acs); die;
	  	  
      $data['acs']      = $acs;
      $data['results']  = [];

      $results = StateModel::get_list(['ac_no' => $data['ac_no'], 'st_code' => $data['st_code']]);
     
	 
	  //dd($results);
	 
	 
       foreach ($results as $res_iterate) {
		  
		//dd($res_iterate->ac_no);

		  
        $data['results'][] = [
            'st_code'           => @$res_iterate->st_code,
            'ac_no'             => @$res_iterate->ac_no,
            'definalize_action' => url($this->definalize_action),
            'st_name'        => @$res_iterate->st_name,
            'ac_name'        => @$res_iterate->ac_name,
        ];

      } 
	  
      $data['user_data']  =   Auth::user();

      return view('IndexCardReports.ByeElectionIndexCard.bye_indexcard_list',$data);
  }
	
	
	public function byeverifyreportcheckbox(Request $request){
		 

         //dd($request);
		 $st_code = $request->st_code;
		 $ac_no = $request->ac_no;
		 $is_verified = $request->is_verified;
		 $date = date('Y-m-d H:i:s');
		 
		 
		 $data = DB::table('bye_election_report_verify')->select('st_code','ac_no')
		 ->where('st_code',$st_code)
		 ->where('ac_no', $ac_no)
		 ->get();
		 
		 
		 //echo "<pre>"; print_r($data); die;
		 
		 $insertData = [
          'is_verified' => $is_verified,
          'verifiat_date' => $date,
          'verified_by' => Auth::user()->officername,
          'ac_no' => $ac_no,
		  'st_code' => $st_code
		];
		
		$updateData = [
          'is_verified' => $is_verified,
		  'verifiat_date' => $date,
          'verified_by' => Auth::user()->officername
		];
	  
	  if(count($data) > 0){
		  $query = DB::table('bye_election_report_verify')
                ->where('ac_no', $ac_no)
				->where('st_code', $st_code)
                ->update($updateData);
				
		 
				
	  }else{
		  $query = DB::table('bye_election_report_verify')
               
                ->insert($insertData);
				
	
				
			
	  }
	  
	   if($query){
          $msg = 'Success';
          $queryinsert = DB::table('bye_election_report_verify_logs')->insert($insertData);
        }else{
          $msg = 'Fail';
        }
		
		 return response()->json(array('msg'=> $msg), 200);
	 
    }

}