<?php 

namespace App\Http\Controllers\Admin\RoundWiseReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Validator;
use Config;
use \PDF,Excel;
use App\commonModel;
use App\models\Admin\ReportModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;

class RoundWiseReportController extends Controller {
  
    public $view_path     = "admin.countingReport.scheduleReport";
    public $aro           = "aro";
    public $ro            = "ro";
    public $eci           = "eci";
    public $ceo           = "ceo";

     public $election_id; 
    
    public function __construct(){

		 $this->commonModel  = new commonModel();
        $this->report_model = new ReportModel();
         $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
            }
            $this->userId = \Auth::id(); // you can access user id here
            $this->election_id  = Auth::user()->election_id;

            return $next($request);
        });
    }
  
  public function index(){  
	  $data=array();
	  $condidateData =array();	
	  $user  =   Auth::user();
	  $data['user_data']  =   Auth::user();
	 // echo "<pre>"; print_r($data); die;	 
	  $user_type=null;
	  $sIdl='NotSet';
	  $dIdl=0;
	  $wIdl=0;
	  $disabled='';	
	  $state_id='';	
	  $pc_no='';	
	  $acData='';
	  $ac_no='';	
	  $disabledAc='';	
	  $acDataDeo='';	
	  $url='';		

	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==7 ))
	  {
		$disabled='';
		$user_type='ECI';
		$state_id='';
        $pc_no='';	
		$ac_no='';
		$url='eci';
	  }
	
	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==4 ))
	  {  
		$disabled='disabled';
		$user_type='CEO'; 
		$state_id=$data['user_data']['st_code'];	
		$pc_no=''; 
		$ac_no='';
		$url='acceo';
	  }
	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==5 ))
	  {	
		$disabled='disabled';
		$user_type='DEO'; 
		$state_id=$data['user_data']['st_code'];
		$dist_no=$data['user_data']['dist_no']; 
		$ac_no=$data['user_data']['ac_no'];

		$acDataDeo = DB::table("m_ac")
		->leftjoin('m_election_details as election',[
              ['election.CONST_NO', '=','m_ac.AC_NO'],
              ['election.ST_CODE', '=','m_ac.ST_CODE'],
        ])
		->select('m_ac.AC_NO','m_ac.AC_NAME', 'm_ac.DIST_NO_HDQTR', 'm_ac.ST_CODE')
		->where('m_ac.ST_CODE', '=', $data['user_data']['st_code'])
		->where('m_ac.DIST_NO_HDQTR', '=', $data['user_data']['dist_no'])
		->where('election.CONST_TYPE', '=', 'AC')
		->where('election.ELECTION_ID', '=', $this->election_id)
		->orderBy('m_ac.AC_NO', 'ASC')
		->orderBy('m_ac.AC_NAME', 'ASC')
		->get(); 
		
		if(isset($acDataDeo) && ((count($acDataDeo)) > 0 ))	{ 
			$acDataDeo = $acDataDeo;
			$stateAc=$acDataDeo[0]->ST_CODE;
		$acstr='';	
		foreach($acDataDeo as $deoAc){
			$acstr.=$deoAc->AC_NO.',';	
		}
		$deoAc = substr($acstr, 0, -1); 
		
		$tablepr=strtolower($data['user_data']['st_code']);
	 	 $query = "select candidate_id, candidate_name, party_abbre  from counting_master_$tablepr where ac_no in ($deoAc)"; 
		$condidateDeo = DB::select($query);

		} else {
			$acDataDeo = array();
		}
		$url='acdeo';

	  }
	  
	 
	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==19 ))
	  {	
		$disabled='disabled';
		$user_type='ARO'; 
		$state_id=$data['user_data']['st_code'];
		$pc_no=$data['user_data']['pc_no']; 
		$ac_no=$data['user_data']['ac_no']; 
		$url='roac';
	  }
	
		/*$state = DB::table('m_state')
		->join('m_election_details',[
		        ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
		      ])
	    ->where('m_election_details.CONST_TYPE','AC')
	     ->where('election_status','1')
	     ->where('m_election_details.ELECTION_ID',$this->election_id)
		->orderBy('ST_NAME', 'ASC')
		->get();*/	

		$state = StateModel::get_states();


		$get_Pc_data = DB::table("m_pc")
		->select('PC_NO','PC_NAME')
		->where('ST_CODE', '=', $data['user_data']['st_code'])
		->orderBy('PC_NAME', 'ASC')
		->get(); 


		if(isset($get_Pc_data) && ((count($get_Pc_data)) > 0 ))	{
			$get_Pc_data = $get_Pc_data;
		} else {
			$get_Pc_data = array();
		}	
		
		$acData = DB::table("m_ac")
		->leftjoin('m_election_details as election',[
              ['election.CONST_NO', '=','m_ac.AC_NO'],
              ['election.ST_CODE', '=','m_ac.ST_CODE'],
        ])
		->select('m_ac.AC_NO','m_ac.AC_NAME', 'm_ac.ST_CODE')
		->where('m_ac.ST_CODE', '=', $data['user_data']['st_code'])
		->where('election.CONST_TYPE', '=', 'AC')
		->where('election.ELECTION_ID', '=', $this->election_id)
		->orderBy('m_ac.AC_NO', 'ASC')
		->orderBy('m_ac.AC_NAME', 'ASC')		
		->get(); 
		
		if(isset($acData) && ((count($acData)) > 0 ))	{ 
			$acData = $acData;
			$stateAc=$acData[0]->ST_CODE;
		} else {
			$acData = array();
		}
   
      if(isset($data['user_data']['st_code']) && ((!empty($data['user_data']['st_code']))) )	{
		$tablepr=strtolower($data['user_data']['st_code']);
		$condidateData = DB::table("counting_master_$tablepr")
		->select('candidate_id','candidate_name','party_abbre')
		->orderBy('id', 'ASC')
		->groupBy('candidate_id')
		->get();
		if(isset($condidateData) && ((count($condidateData)) > 0 ))	{
			$condidateData = $condidateData;
		} else {
			$condidateData = array();
		}
	 }

	if(isset($data['user_data']['st_code']) && ((!empty($data['user_data']['st_code'])) && ((!empty($data['user_data']['ac_no']))))){
		$tablepr=strtolower($data['user_data']['st_code']);
		$condidateDataAc = DB::table("counting_master_$tablepr")
		->select('candidate_id','candidate_name','party_abbre')
		->where('ac_no', '=', $data['user_data']['ac_no'])
		->orderBy('id', 'ASC')
		->groupBy('candidate_id')
		->get();
		if(isset($condidateDataAc) && ((count($condidateDataAc)) > 0 ))	{
			$condidateDataAc = $condidateDataAc;
		} else {
			$condidateDataAc = array();
		}
	}
	
	if(!empty($stateAc)){
		$state_id =  $stateAc; 
	} else {
		$state_id =  $state;
	}
	
	

	return view('admin.countingReport.round-wise-report', $data, compact('data', 'state', 'disabled', 'user_type', 'state_id', 'pc_no', 'ac_no', 'get_Pc_data', 'acData', 'condidateData', 'disabledAc', 'condidateDataAc', 'dist_no', 'acDataDeo', 'condidateDeo', 'url'));
  }
  
	public function getMatchedPcByStateId(Request $request){
	  
		$input=$request->All();
		$st_code=$input['st_code'];
		$get_pc_data = DB::table("m_pc")
		->select('PC_NO','PC_NAME')
		->where('ST_CODE', '=', $st_code)
		->orderBy('PC_NAME', 'ASC')
		->get(); 
		
		$pcList = array();
		$arra = array();	
		if(count($get_pc_data)>0)
	    {
				foreach ($get_pc_data as $each_record) {
				  $pcList[$each_record->PC_NO] = $each_record->PC_NAME;
				}
	    } else {
			return '0';
		}
		if($pcList==0)
		{
			 return 0;
		}
		else
		{
			foreach($pcList as $dcode => $dval) { 
				 $arra['PC_NO'][]=$dcode;
				 $arra['PC_NAME'][]=$dval;
			}
		}	
		return json_encode( $arra );	
	}
	public function getMatchedAc(Request $request){
	  
		$input=$request->All();
		$st_code=$input['st_code'];
		//$pcId=$input['pcId'];
		
		$get_ac_data = DB::table("m_ac")
		->leftjoin('m_election_details as election',[
              ['election.CONST_NO', '=','m_ac.AC_NO'],
              ['election.ST_CODE', '=','m_ac.ST_CODE'],
        ])
		->select('m_ac.AC_NO','m_ac.AC_NAME')
		->where('m_ac.ST_CODE', '=', $st_code)
		->where('election.CONST_TYPE', '=', 'AC')
		->where('election.ELECTION_ID', '=', $this->election_id)
		->orderBy('m_ac.AC_NO', 'ASC')
		->orderBy('m_ac.AC_NAME', 'ASC')
		->get(); 
		
		$acList = array();
		$arraAc = array();	
		if(count($get_ac_data)>0)
	    {
				foreach ($get_ac_data as $each_record) {
				  $acList[$each_record->AC_NO] = $each_record->AC_NAME;
				}
	    } else {
			return '0';
		}
		if($acList==0)
		{
			 return 0;
		}
		else
		{
			foreach($acList as $dcode => $dval) { 
				 $arraAc['AC_NO'][]=$dcode;
				 $arraAc['AC_NAME'][]=$dval;
			}
		}	
		return json_encode( $arraAc );	
	}

	public function getCondidfateListAcWise(Request $request){
	  
		$input=$request->All();
	
		$st_code=$input['stateok'];
		$ac=implode(',',$input['ac']);
		$tablepr=strtolower($st_code);
		
		$cndAc='';
		
		$exp=explode("_", $ac);
		if($ac=='000' or $exp[0]=='DEO'){
			$cndAc =	"";
		}else{
			$cndAc =	"where AC_NO in " .'('.$ac .')';
		}
	 	 $query = "select * from counting_master_$tablepr   $cndAc"; 
		$result = DB::select($query);
		
		$cList = array();
		$arraAc = array();	
		if(count($result)>0)
	    {
				foreach ($result as $each_record) {
				  $cList[$each_record->candidate_id] = $each_record->candidate_name.'('.$each_record->party_abbre.')';
				}
	    } else {
			return '0';
		}
		if($cList==0)
		{
			 return 0;
		}
		else
		{
			foreach($cList as $dcode => $dval) { 
				 $arraAc['candidate_id'][]=$dcode;
				 $arraAc['cParty'][]=$dval;
			}
		}	
		return json_encode( $arraAc );	
	}

	
	public function getCondidfateListpkpk(Request $request){
	  
		$input=$request->All();
		return $st_code=implode(',',$input['stateok']);
		$tablepr=strtolower($st_code);
		$condidate = DB::table("counting_master_$tablepr")
		->select('candidate_id','candidate_name','party_abbre')
		//->where('pc_no', '=', $pcId)
		->orderBy('id', 'ASC')
		->groupBy('candidate_id')
		->limit('1')
		->get(); 
		return $condidate;
		$cList = array();
		$arraAc = array();	
		if(count($condidate)>0)
	    {
				foreach ($condidate as $each_record) {
				  $cList[$each_record->candidate_id] = $each_record->candidate_name.'('.$each_record->party_abbre.')';
				}
	    } else {
			return '0';
		}
		if($cList==0)
		{
			 return 0;
		}
		else
		{
			foreach($cList as $dcode => $dval) { 
				 $arraAc['candidate_id'][]=$dcode;
				 $arraAc['cParty'][]=$dval;
			}
		}	
		return json_encode( $arraAc );	
	}

	public function getCompleteResult(Request $request){
	  
		$input=$request->All();
		//echo "<pre>"; print_r($input); die;
		$st_code=implode(',',$input['st_code']);
		//$pc=implode(',',$input['pc']);
		$ac=implode(',',$input['ac']);
		$condidate=implode(',',$input['condidate']);
		
		$LoginData['user_data']  =   Auth::user();
		$tablepr=strtolower($st_code);	

		$cnd=''; $AC_NAME='';  $candidate_name='';

		if(($condidate=='000') && ($ac=='000')){
			$cnd =	" ";
			$candidate_name='All Candidate';
			$AC_NAME="All AC";
		}
		if(($ac=='000') && ($condidate!='000')){
			$cnd =	"where candidate_id in " .'('.$condidate .')';
			$candidate_name = DB::table("counting_master_$tablepr")->select('candidate_name')->where('candidate_id', $condidate)->first()->candidate_name;
			$AC_NAME="All AC";
		} 
		$imp1 = explode("_", $ac);
		if(($ac!='000') && ($condidate=='000') && ($imp1[0]!="DEO")){
			$cnd =	"where AC_NO in " .'('.$ac .')';
			$candidate_name='All Candidate';
			$AC_NAME = DB::table('m_ac')->select('AC_NAME')->where('ST_CODE', $st_code)->where('AC_NO', '=', $ac)->first()->AC_NAME;
		
		} 

		$imp = explode("_", $ac);
		if(($ac!='000') && ($condidate!='000') && ($imp[0]!="DEO")){
			$cnd =	"where AC_NO in " .'('.$ac .')'." and candidate_id in " .'('.$condidate .')';
			$AC_NAME = DB::table('m_ac')->select('AC_NAME')->where('ST_CODE', $st_code)->where('AC_NO', '=', $ac)->first()->AC_NAME; 
			$candidate_name = DB::table("counting_master_$tablepr")->select('candidate_name')->where('candidate_id', $condidate)->first()->candidate_name;	
		}
		
		if((isset($imp[0]) && ($imp[0]=="DEO"))){
			
			$acDataDeo = DB::table("m_ac")
			->select('AC_NO','AC_NAME', 'DIST_NO_HDQTR', 'ST_CODE')
			->where('ST_CODE', '=', $tablepr)
			->where('DIST_NO_HDQTR', '=', $imp[1])
			->orderBy('AC_NO', 'ASC')
			->orderBy('AC_NAME', 'ASC')
			->get(); 
		
			if(isset($acDataDeo) && ((count($acDataDeo)) > 0 ))	{ 
				$acDataDeo = $acDataDeo;
				$stateAc=$acDataDeo[0]->ST_CODE;
				$acstr='';	
				foreach($acDataDeo as $deoAc){
					$acstr.=$deoAc->AC_NO.',';	
				}
				$deoAc = substr($acstr, 0, -1); 	
				
				if(($condidate!='000')){
				 $cnd =	"where AC_NO in " .'('.$deoAc .')'." and candidate_id in " .'('.$condidate .')';
				 $candidate_name = DB::table("counting_master_$tablepr")->select('candidate_name')->where('candidate_id', $condidate)->first()->candidate_name;	
				 $AC_NAME = DB::table('m_ac')->select('AC_NAME')->where('ST_CODE', $st_code)->where('AC_NO', '=', $deoAc)->first()->AC_NAME; 
				}
				if(($condidate=='000')){
				 $cnd =	"where AC_NO in " .'('.$deoAc .')';
				 $AC_NAME="All AC";
				}
			}
		$ac='000';
		}
		
		$tablepr=strtolower($st_code);
	 	$query = "select * from counting_master_$tablepr   $cnd"; 
		$result = DB::select($query);

		 $state_name='';
		 $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $st_code)->first()->ST_NAME;
		
		
		
		if(!isset($input['download'])){ 
			return  view('admin.countingReport.round-wise-report-ajax-result', compact('result', 'st_code', 'state_name', 'ac', 'AC_NAME', 'candidate_name'));
		} else {
			ini_set("pcre.backtrack_limit", "100000");
			$pdf = PDF::loadView('admin.countingReport.round-wise-report-pdf', compact('result', 'st_code', 'LoginData', 'state_name', 'ac', 'AC_NAME', 'candidate_name'));
			return $pdf->download('Round-Wise-Voting-Report.pdf');	
		}
	}
	
	public function getMaxRound($st, $ac){
		if( $ac =='000'){
			 $query = "select max(scheduled_round) as scheduled_round from round_master where st_code='".$st."'"; 
				$result = DB::select($query);
				if(count( $result ) > 0 ){
					return $result[0]->scheduled_round;
				} else {
					return 0;
				}
		} else {
				$query = "select scheduled_round from round_master where st_code='".$st."' and ac_no='".$ac."'"; 
				$result = DB::select($query);
				if(count( $result ) > 0 ){
					return $result[0]->scheduled_round;
				} else {
					return 0;
				}
		}	
	}

	
	public function getAc($state, $ac){
	  $acName = DB::table("m_ac")
		->select('AC_NAME')
		->where('ST_CODE', '=', $state)
		->where('AC_NO', '=',  $ac)
		->orderBy('AC_NO', 'ASC')
		->orderBy('AC_NAME', 'ASC')
		->get(); 
	if(count( $acName ) > 0 ){
	 return $acName[0]->AC_NAME;
	} else {
	 return 'NA';
	}		
	}		
	function csvDownload(REQUEST $request) {
	    $input=$request->All();
		
		$st_code=implode(',',$input['st_code']);
		//$pc=implode(',',$input['pc']);
		$ac=implode(',',$input['ac']);
		$condidate=implode(',',$input['condidate']);
		$LoginData['user_data']  =   Auth::user();


		$cnd='';
		$exp=explode("_", $ac);


		if(($condidate=='000') && ($ac=='000')){
			$cnd =	" "; 
		}

		if(($ac=='000') && ($condidate!='000')){ 
			$cnd =	"where candidate_id in " .'('.$condidate .')';
		}
		if(($ac!='000') && ($condidate=='000')){
			$cnd =	"where AC_NO in " .'('.$ac .')';
		}
		if(($ac!='000') && ($condidate!='000')){
			$cnd =	"where AC_NO in " .'('.$ac .')'." and candidate_id in " .'('.$condidate .')';
		}
		$imp = explode("_", $ac); 
		if((isset($imp[0]) && ($imp[0]=="DEO"))){
			$tablepr=strtolower($st_code);	
			$acDataDeo = DB::table("m_ac")
			->select('AC_NO','AC_NAME', 'DIST_NO_HDQTR', 'ST_CODE')
			->where('ST_CODE', '=', $tablepr)
			->where('DIST_NO_HDQTR', '=', $imp[1])
			->orderBy('AC_NO', 'ASC')
			->orderBy('AC_NAME', 'ASC')
			->get(); 
		
			if(isset($acDataDeo) && ((count($acDataDeo)) > 0 ))	{ 
				$acDataDeo = $acDataDeo;
				$stateAc=$acDataDeo[0]->ST_CODE;
				$acstr='';	
				foreach($acDataDeo as $deoAc){
					$acstr.=$deoAc->AC_NO.',';	
				}
				$deoAc = substr($acstr, 0, -1); 	
				
				if(($condidate!='000')){
				 $cnd =	"where AC_NO in " .'('.$deoAc .')'." and candidate_id in " .'('.$condidate .')';
				}
				if(($condidate=='000')){
				 $cnd =	"where AC_NO in " .'('.$deoAc .')';
				}
			}
			$ac='000';
		}
		//echo $st_code .'-'. $ac; die("Test");


		$maxRound=0;	
		$maxRound = $this->getMaxRound($st_code,  $ac); 

		$tablepr=strtolower($st_code);
	 	$query = "select * from counting_master_$tablepr $cnd"; 
		$result = DB::select($query);

		 $content=''; $rem='sdbjsdfbsjdfhfsd';
		 if (count($result) > 0 ) {
			 $file = "Round-Wise-EVM-Votes.csv";
			 header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			 header('Content-Disposition: attachment; filename='.$file);
			
			 //$content .= "test1,test1,test3\n";
			// $content .= "testtest,ttesttest2,testtest3\n";
			
			$content = "Sr. No,State,AC,AC No.,Party,Condidate,";
			$countData=array();
			
			$b = 0;
			$dataok=''; $mdataok=''; $rnd=0;
			 for($m=1; $m<=$maxRound; $m++){  $rnd++;
				$dataok .= " R$m,"; 
			 }  
			$mdataok=substr($dataok , 0, -1);
			if($rnd==0){
			$mdataok='Round';
			}
			
			$content.= "$mdataok, Total EVM Votes\n";
			$rem=$b;

               $i = 1;  $ok=array(); $final=''; $first='';
               foreach ($result as $k => $dval) { 
				  $mDatadata = (array)$dval;

				  $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $st_code)->first()->ST_NAME;
               //   $pc_name = DB::table('m_pc')->select('PC_NAME')->where('ST_CODE', $st_code)->where('PC_NO', '=', $pc)->first()->PC_NAME;
				  $ac_name = $this->getAc($st_code, $mDatadata['ac_no']);
				  
				  
				  $st=''; $acname=''; $acnnom=0; $partyname=''; $partabbre=''; $cndname='';		
					
					$st   =  str_replace(',', '', $state_name);
					if( $st =='') {  $st = 'NA'; }
					$acname   		=  str_replace(',', '', $ac_name);
					if( $acname =='') {  $acname = 'NA'; }
					$acnnom   		=  str_replace(',', '', $mDatadata['ac_no']);
					if( $acnnom =='') {  $acnnom = 'NA'; }
					$partyname 		=  str_replace(',', '', $mDatadata['party_name']);
					if( $partyname =='') {  $partyname = 'NA'; }
					$partabbre  	=  str_replace(',', '', $mDatadata['party_abbre']);
					if( $partabbre =='') {  $partabbre = 'NA'; }
					$cndname    	=  str_replace(',', '', $mDatadata['candidate_name']);		
					if( $cndname =='') {  $cndname = 'NA'; }

					$first.= "$i,$st,$acname($acnnom),$acnnom,$partyname($partabbre),$cndname,"; 
					
					
				
						$j=0;  $countData=array(); $mdatat=array();
						for($k=1; $k<=$maxRound; $k++){     	
							$dataok = 'round'.$k;
							$j=$j+1; 
							array_push($countData, $j);  
						}						
						$b = 0;  
						
						
						
								$total_votes=0; $p=0;  $ffirst='';$second=''; $fseond=$fokData=''; $third=''; $isRound=0;
								for($k=1; $k<=$maxRound; $k++){ 
		
								 $dataok = 'round'.$k;

									 $p++; $isRound++;
									 $second .= $mDatadata[$dataok].',';	
									 $total_votes=$total_votes + $mDatadata[$dataok];
									 $third=$total_votes."\n";		
											
								}
								
								 $remain = $rem-$p;  $fourth='';
								 if($remain==0){
								  $fourth="\n";		
								 }
								if($total_votes==0){
								  $third="\n";		
								}
								


								 $first.= $second.$third.$fourth; 
					
                   $i++; 

			   	
               } 
			   $finalData='';	
			   $finalData=$content.$first;	
			   echo $finalData;

			   
                  
				

           }
       }
   

}  