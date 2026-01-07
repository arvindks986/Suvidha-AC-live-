<?php 
namespace App\Http\Controllers\Admin\ConstituencyWiseReport;
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

class ConstituencyWiseReportController extends Controller {
  
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
           
            $this->election_id  = Auth::user()->election_id;

            return $next($request);
        });

         
    }
  
public function index(){ 
  
	$data=array();
	 $data['user_data']  =   Auth::user();		
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


		
		$get_Pc_data = DB::table("m_ac")
		->leftjoin('m_election_details as election',[
						              ['election.CONST_NO', '=','m_ac.AC_NO'],
						              ['election.ST_CODE', '=','m_ac.ST_CODE'],
						        ])
		->select('AC_NO','AC_NAME')
		->where('m_ac.ST_CODE', '=', $data['user_data']['st_code'])
		->where('election.CONST_TYPE', '=', 'AC')
		->where('election.ELECTION_ID', '=', $this->election_id)
		->orderBy('m_ac.AC_NO', 'ASC')
		->get(); 
		if(isset($get_Pc_data) && ((count($get_Pc_data)) > 0 ))	{
			$get_Pc_data = $get_Pc_data;
		} else {
			$get_Pc_data = array();
		}
		
	  }
	 
	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==5 ))
	  {
		$disabled='disabled';
		$user_type='DEO'; 
		$state_id=$data['user_data']['st_code'];
		$pc_no=$data['user_data']['pc_no']; 
		$ac_no=''; 
		$url='acdeo';
		
		
		$get_Pc_data = DB::table("m_ac")
		->leftjoin('m_election_details as election',[
		              ['election.CONST_NO', '=','m_ac.AC_NO'],
		              ['election.ST_CODE', '=','m_ac.ST_CODE'],
		        ])
		->select('m_ac.AC_NO','m_ac.AC_NAME', 'm_ac.DIST_NO_HDQTR', 'm_ac.ST_CODE', 'm_ac.AC_TYPE')
		->where('m_ac.ST_CODE', '=', $data['user_data']['st_code'])
		->where('DIST_NO_HDQTR', '=', $data['user_data']['dist_no'])
		->where('election.CONST_TYPE', '=', 'AC')
		->where('election.ELECTION_ID', '=', $this->election_id)
		->orderBy('m_ac.AC_NO', 'ASC')
		->orderBy('m_ac.AC_NAME', 'ASC')
		->get(); 
		if(isset($get_Pc_data) && ((count($get_Pc_data)) > 0 ))	{
			$get_Pc_data = $get_Pc_data;
		} else {
			$get_Pc_data = array();
		}
		 //echo "<pre>"; print_r($get_Pc_data); die;
	  } 

	  if(isset($data['user_data']['role_id']) && ($data['user_data']['role_id']==19 ))
	  {	
		$disabled='disabled';
		$user_type='ARO';
		$state_id=$data['user_data']['st_code'];
		$pc_no=$data['user_data']['pc_no']; 
		$ac_no=$data['user_data']['ac_no']; 
		$url='roac';

		
		$get_Pc_data = DB::table("m_ac")
		->leftjoin('m_election_details as election',[
              ['election.CONST_NO', '=','m_ac.AC_NO'],
              ['election.ST_CODE', '=','m_ac.ST_CODE'],
        ])
		->select('m_ac.AC_NO','m_ac.AC_NAME', 'm_ac.DIST_NO_HDQTR', 'm_ac.ST_CODE', 'm_ac.AC_TYPE')
		->where('m_ac.ST_CODE', '=', $data['user_data']['st_code'])
		->where('m_ac.DIST_NO_HDQTR', '=', $data['user_data']['dist_no'])
		->where('election.CONST_TYPE', '=', 'AC')
		->where('election.ELECTION_ID', '=', $this->election_id)
		->where('ac_no', '=', $data['user_data']['ac_no'])
		->orderBy('m_ac.AC_NO', 'ASC')
		->orderBy('m_ac.AC_NAME', 'ASC')
		->get(); 
		if(isset($get_Pc_data) && ((count($get_Pc_data)) > 0 ))	{
			$get_Pc_data = $get_Pc_data;
		} else {
			$get_Pc_data = array();
		}
		
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

	    $state	= StateModel::get_states();

        if(isset($data['user_data']['st_code']) && ((!empty($data['user_data']['st_code']))) )	{
		$tablepr=strtolower($data['user_data']['st_code']);
		$condidateData = DB::table("counting_master_$tablepr")
		->select('nom_id','candidate_id','candidate_name','party_abbre')
		->where('pc_no', '=',  $data['user_data']['pc_no'])
		->orderBy('id', 'ASC')
		->groupBy('candidate_id')
		->get();
		if(isset($condidateData) && ((count($condidateData)) > 0 ))	{
			$condidateData = $condidateData;
		} else {
			$condidateData = array();
		} 
	 }	
	 
		
		
	 //echo "<pre>"; print_r($get_Pc_data); die;
	 return view('admin.ConstituencyWiseReport.constituency-wise-report', $data, compact('data', 'state', 'disabled', 'user_type', 'state_id', 'pc_no', 'ac_no', 'get_Pc_data',  'condidateData', 'disabledAc', 'url'));
  }
  
	public function getMatchedPcByStateId(Request $request){
		
		$input=$request->All();
		$st_code=$input['st_code'];
		$get_pc_data = DB::table("m_ac")
					   ->leftjoin('m_election_details as election',[
						              ['election.CONST_NO', '=','m_ac.AC_NO'],
						              ['election.ST_CODE', '=','m_ac.ST_CODE'],
						        ])
		                ->select('m_ac.AC_NO','m_ac.AC_NAME')
						->where('m_ac.ST_CODE', '=', $st_code)
						->where('election.CONST_TYPE', '=', 'AC')
						->where('election.ELECTION_ID', '=', $this->election_id)
						->orderBy('m_ac.AC_NO', 'ASC')
						->get(); 
		
		$pcList = array();
		$arra = array();	
		if(count($get_pc_data)>0)
	    {
				foreach ($get_pc_data as $each_record) {
				  $pcList[$each_record->AC_NO] = $each_record->AC_NAME;
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
				 $arra['AC_NO'][]=$dcode;
				 $arra['AC_NAME'][]=$dval;
			}
		}	
		return json_encode( $arra );	
	}
	public function getMatchedAc(Request $request){
	  
		$input=$request->All();
		$st_code=$input['st_code'];
		$pcId=$input['pcId'];
		
		$get_ac_data = DB::table("m_ac")
		->leftjoin('m_election_details as election',[
              ['election.CONST_NO', '=','m_ac.AC_NO'],
              ['election.ST_CODE', '=','m_ac.ST_CODE'],
        ])
		->select('AC_NO','AC_NAME')
		->where('m_ac.ST_CODE', '=', $st_code)
		->where('m_ac.PC_NO', '=', $pcId)
		->where('election.CONST_TYPE', '=', 'AC')
		->where('election.ELECTION_ID', '=', $this->election_id)
		->orderBy('m_ac.AC_NO', 'ASC')
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
	
	public function getCondidfateListpkpk(Request $request){
	  
		$input=$request->All();
		$st_code=implode(',',$input['stateok']);
		$tablepr=strtolower($st_code);
		$pcId=$input['pcId'];
		$condidate = DB::table("counting_master_$tablepr")
		->select('nom_id','candidate_id','candidate_name','party_abbre')
		->where('pc_no', '=', $pcId)
		->orderBy('id', 'ASC')
		->groupBy('candidate_id')
		->get(); 
		
		$cList = array();
		$arraAc = array();	
		if(count($condidate)>0)
	    {
				foreach ($condidate as $each_record) {
				  $cList[$each_record->candidate_id] = $each_record->nom_id.'-'.$each_record->candidate_name.'('.$each_record->party_abbre.')';
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
		
		$result_counting='';  $result_type='';
		$input=$request->All();
		//echo "<pre>"; print_r($input); die;
		if(isset($input['download'])){
			$st_code=explode(',',$input['st_code']['0']);
			$st='';
			foreach($st_code as $stt){
			$st.=	"'$stt',";
			}
			$st_code=substr($st, 0, -1);	
		} else { 
			$st_code=''; $stateData=array();		
			$stateData =$input['st_code'];
			$st='';
			foreach($input['st_code'] as $stt){
			$st.=	"'$stt',";
			}
			$st_code=substr($st, 0, -1);
		}
		
		
		$pc='';
		if(isset($input['pc']) && (!empty($input['pc'])) && ($input['pc']=='000')) { 
			$pc='000';	
		} else {
			$pc=implode(',',$input['pc']);
		}
		$result_counting_text=''; $result_type_text='';
		if(isset($input['result_counting_text'])){
		$result_counting_text= $input['result_counting_text'];
		}
		if(isset($input['result_type_text'])){
		$result_type_text= $input['result_type_text'];
		}
		$result_counting= $input['result_counting'];
		$result_type= $input['result_type'];  // Pass to blade to show result only
		$LoginData['user_data']  =   Auth::user();
		
		
		$cnd=''; $candidate_name=''; $AC_NAME=''; 	$user='';
		
		if(!empty($input['user_type'])){
		$user =  $input['user_type'];
		}
		
		$result_status='';
		if($result_counting==000){
		 $result_status=''; 	
		}
		
		$status_in_table='';
		if($result_counting==1){
		$result_status='1';	
		$status_in_table =	"and  status = '1'";	
		}
		if($result_counting==2){
		$result_status='0';		
		$status_in_table =	"and status = '0'";			
		}
		
		
		
		$stQuery='';
		if($st_code=='000'){
			$stQuery='';		
		} else { 
			$stQuery =	" st_code in " .'('.$st_code.')';	
		}
		
		
		
		
		
		$pcQuery='';
		if($pc=='000'){
		$pcQuery='';		
		} else {
		  $pcQuery =	" and ac_no in " .'('.$pc.')';	
		}
	
		
		
		//echo "<pre>"; print_r($stateData); die;
		$sum=''; $sumData=''; $result=array();
		 $query = "SELECT * from  winning_leading_candidate where $stQuery $pcQuery $status_in_table order by leading_id asc"; 
		$result = DB::select($query);
		
		$state_name='';
		$state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $input['st_code']['0'])->first()->ST_NAME;
		
		if(!isset($input['download'])){
			return  view('admin.ConstituencyWiseReport.constituency-wise-report-ajax-result', compact('result', 'stateData', 'result_counting', 'result_type', 'LoginData', 'result_counting_text', 'result_type_text', 'user'));
		} else {
			$pdf = PDF::loadView('admin.ConstituencyWiseReport.constituency-wise-report-pdf', compact('result', 'stateData', 'result_counting', 'result_type', 'LoginData', 'result_counting_text', 'result_type_text', 'user', 'state_name'));
			return $pdf->download('Constituency-Wise-Voting-Report.pdf');	
		}
	}
	
	public function getPc($state, $pc){
	$acName = DB::table("m_pc")
	->select('PC_NAME')
	->where('ST_CODE', '=', $state)
	->where('PC_NO', '=',  $pc)
	->get(); 
	if(count( $acName ) > 0 ){
		return $acName[0]->PC_NAME;
	} else {
		return 'NA';
	}		
	}

	public function getMaxRound($st, $pc){
		
				$query = "select max(scheduled_round) as scheduled_round from round_master where st_code='".$st."' and pc_no in ($pc)"; 
				$result = DB::select($query);
				if(count( $result ) > 0 ){
					return $result[0]->scheduled_round;
				} else {
					return 0;
				}
	}

		
	function csvDownload(REQUEST $request) { 
		
	    $result_counting='';  $result_type='';
		$input=$request->All();
		//echo "<pre>"; print_r($input); die;
		if(isset($input['download'])){
			$st_code=explode(',',$input['st_code']['0']);
			$st='';
			foreach($st_code as $stt){
			$st.=	"'$stt',";
			}
			$st_code=substr($st, 0, -1);	
		} else { 
			$st_code=''; $stateData=array();		
			$stateData =$input['st_code'];
			$st='';
			foreach($input['st_code'] as $stt){
			$st.=	"'$stt',";
			} 
			$st_code=substr($st, 0, -1); 
		}
		
		
		$pc='';
		if(isset($input['pc']) && (!empty($input['pc'])) && ($input['pc']=='000')) { 
			$pc='000';	
		} else {
			$pc=implode(',',$input['pc']);
		}
		
		$result_counting= $input['result_counting'];
		$result_type= $input['result_type'];  // Pass to blade to show result only
		$LoginData['user_data']  =   Auth::user();
		
		
		$cnd=''; $candidate_name=''; $AC_NAME=''; 	$user='';
		
		if(!empty($input['user_type'])){
		$user =  $input['user_type'];
		}
		
		$result_status='';
		if($result_counting==000){
		 $result_status=''; 	
		}
		
		$status_in_table='';
		if($result_counting==1){
		$result_status='1';	
		$status_in_table =	"and  status = '1'";	
		}
		if($result_counting==2){
		$result_status='0';		
		$status_in_table =	"and status = '0'";			
		}
		
		
		
		$stQuery='';
		if($st_code=='000'){
			$stQuery='';		
		} else { 
			$stQuery =	" st_code in " .'('.$st_code.')';	
		}
		
		
		
		
		
		$pcQuery='';
		if($pc=='000'){
		$pcQuery='';		
		} else {
		  $pcQuery =	" and ac_no in " .'('.$pc.')';	
		}
	
		
		
		//echo "<pre>"; print_r($stateData); die;
		$sum=''; $sumData=''; $result=array();
		$query = "SELECT * from  winning_leading_candidate where $stQuery $pcQuery $status_in_table order by leading_id asc";
		$result = DB::select($query);
		
		
        

	
		//echo count($result); die;
		 $content=''; $rem='sdbjsdfbsjdfhfsd';
		 if (count($result) > 0 ) { 
			 $file = "PC-Result-Report.csv";
			 header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			 header('Content-Disposition: attachment; filename='.$file);
			
			
			
			$content = "Sr. No,State Name,AC Name,AC No.,Leading  Party,Leading Candidate,Margin,";
			$countData=array();
			
			
			$b = 0;
			 
			 $dataok=''; $mdataok=''; $rnd=0; $set='';
			if($result_type!=1){
				$dataok .= "Trailing Party, Trailing Candidate,"; 
			 }  
			$mdataok=substr($dataok , 0, -1);
			if(!empty($mdataok)){
			$set=$mdataok.',';				
			} else {
			$set='';		
			}
			
			$content.= "$set Result Status\n";
			$rem=$b;
			   $cndname='';	$ac_name=''; $lead_cand_party=''; $lead_cand_name=''; $trail_cand_party=''; $trail_cand_name='';
			   $margin=''; $pcno='';
			   
               $i = 1;  $ok=array(); $final=''; $first='';
               foreach ($result as $k => $dval) { // echo "<pre>"; print_r($dval); die;
				
				 
				  $cndname=str_replace(",", '-', $dval->st_name);
				  if( $cndname =='') {  $cndname = 'NA'; }	
				  $ac_name=str_replace(",", '-', $dval->ac_name);	
				   if( $ac_name =='') {  $ac_name = 'NA'; }	
				  $lead_cand_party=str_replace(",", '-', $dval->lead_cand_party);	
				  if( $lead_cand_party =='') {  $lead_cand_party = 'NA'; }	
				  $lead_cand_name=str_replace(",", '-', $dval->lead_cand_name);	
				  if( $lead_cand_name =='') {  $lead_cand_name = 'NA'; }	
				  $trail_cand_party=str_replace(",", '-', $dval->trail_cand_party);	
				  if( $trail_cand_party =='') {  $trail_cand_party = 'NA'; }	
				  $trail_cand_name=str_replace(",", '-', $dval->trail_cand_name);	
				  if( $trail_cand_name =='') {  $trail_cand_name = 'NA'; }	
				  $margin=	$dval->margin;
				  $winner='';
				  if(($dval->status=='1') && ($dval->margin!='0')){
					$winner="(WINNER)";  
				  }
				  
				
				  if( $margin =='') {  $margin = '0'; }	
				  $pcno=$dval->ac_no; 
				  if( $pcno =='') {  $pcno = '0'; }	
				  
					$status='';
					if($dval->status==1){
					$status='Result Declared';	
					}
					if($dval->status==0){
					$status='Result In Progress';	
					}
				  
					$resuktok=''; $setresult='';
					$first.=  "$i, $cndname, $ac_name,$pcno,$lead_cand_party,$lead_cand_name $winner, $margin,"; 
					if($result_type!=1){
						$resuktok .= "$trail_cand_party, $trail_cand_name,"; 
					}  
					$resuktok=substr($resuktok , 0, -1);
					if(!empty($resuktok)){
					$setresult=$resuktok.',';							
					} else {
					$setresult='';				
					}
					 
					$first.= "$setresult $status\n";
					
					//die("Test");	
					
                   $i++; 

			   	
               }    
			   $finalData='';	
			   $finalData=$content.$first;	
			   echo $finalData;
                  
				

           }
       }
   

}  