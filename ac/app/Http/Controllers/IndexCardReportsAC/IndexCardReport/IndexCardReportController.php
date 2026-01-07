<?php

namespace App\Http\Controllers\IndexCardReportsAC\IndexCardReport;

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
    use Excel;
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
    
	use App\models\indexcard\OtherAbbreviationsAndDescription;
	use App\models\indexcard\ListOfSuccessfulCandidates;
	use App\models\indexcard\ListOfPoliticalPartiesParticipated;
	use App\models\indexcard\PerformanceOfPoliticalParties;
	use App\models\indexcard\PerformanceOfWomenCandidates;
	use App\models\indexcard\CandidateDataSummary;
	use App\models\indexcard\DetailedResults;
	use App\models\indexcard\AcWiseNoOfElectors;
	use App\models\indexcard\AcWiseVotersInformation;
	use App\models\indexcard\AcWiseCandidateDataSummary;
	use App\models\indexcard\ListOfSuccessfulCandidatesB;
	use App\models\indexcard\ConstituencyWiseDetailedResult;
	
	ini_set("memory_limit","1500M");
    set_time_limit('6000');
    ini_set("pcre.backtrack_limit", "50000000");
	
	ini_set('max_execution_time', 0);
    ini_set('memory_limit', '-1');

class IndexCardReportController extends Controller
{
  public function __construct(){    
  
	$this->middleware(['auth:admin', 'auth']);
       $this->middleware(function (Request $request, $next) {
           if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
           }

           $user = Auth::user();
           switch ($user->role_id) {
               case '7':
                   $this->middleware('eci');
                   break;
               case '4':
                   $this->middleware('ceo');
                   break;
               case '18':
                   $this->middleware('ro');
                   break;
			  case '27':
                   $this->middleware('eci_index');
                   break;   
				   
               default:
                   $this->middleware('eci');
           }
           return $next($request);
       });
 
        $this->middleware('adminsession');
        $this->commonModel = new commonModel();
        $this->ceomodel = new ACCEOModel();
        $this->acceoreportModel = new ACCEOReportModel();
        $this->xssClean = new xssClean;
    }
	protected function guard(){
        return Auth::guard();
    }
    
    
	public function otherAbbreviationsAndDescription(Request $request,$st_code){
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
                
        $user_data = $d;
		
		if($user->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}

		 if($request->path() == "$prefix/other-abbreviations-and-description-pdf/$st_code"){
			$pdf = PDF::loadView('IndexCardReports.IndexCardReports.other-abbreviations-and-description-pdf', compact('user_data','st_code'));
			
			 // code for verified pdf check and upload
			
			if(verifyreport(1, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'other-abbreviations-and-description'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '1',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  //Code end for verify and download
			
			
			
			return $pdf->download('1 - Other Abbreviations And Description.pdf');
		}else if($request->path() == "$prefix/other-abbreviations-and-description-xls/$st_code"){
			return \Excel::download(new OtherAbbreviationsAndDescription, '1 - Other Abbreviations And Description.xlsx');
		}else{
			return view("IndexCardReports.IndexCardReports.other-abbreviations-and-description", compact('user_data','st_code'));
		}
    }
	
	public function listOfSuccessfulCandidates(Request $request,$st_code){
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
        $user_data = $d;
		

		$dataCaddidateWise = DB::table('winning_leading_candidate as wlc')
		->select('wlc.st_name','wlc.ac_no','wlc.ac_name','AC_TYPE','cpd.cand_name as lead_cand_name','cpd.cand_gender','cpd.cand_age','cpd.cand_category','wlc.lead_party_abbre','ms.SYMBOL_DES','wlc.lead_total_vote','wlc.trail_total_vote','wlc.margin','rpd.cand_name as trail_cand_name','rpd.cand_gender as trail_cand_gender','wlc.trail_party_abbre')
		->join('candidate_nomination_detail as cnd','wlc.candidate_id','cnd.candidate_id')
		->join('candidate_personal_detail as cpd','cnd.candidate_id','cpd.candidate_id')		
		->join('candidate_personal_detail as rpd','rpd.candidate_id','wlc.trail_candidate_id')		
		->join('m_symbol as ms','cnd.symbol_id','ms.SYMBOL_NO')
		->join('m_ac',[['m_ac.st_code','wlc.st_code'],['m_ac.ac_no','wlc.ac_no']])
		->where(array(
			'wlc.st_code' => $st_code,
			'cnd.application_status' => '6',
			'cnd.finalaccepted' => '1'
		))
		->where('cnd.symbol_id','!=','200')
		->groupBy('wlc.candidate_id')
		->orderBy('cnd.ac_no','asc')
		->get()->toArray();
		
		
		
		$dataPartyWise = DB::table('winning_leading_candidate as wlc')
		->join('candidate_personal_detail as rpd','rpd.candidate_id','wlc.candidate_id')	
		->select('st_name','lead_cand_party',DB::Raw("count('wlc.candidate_id') as total_seats"), DB::Raw("SUM(CASE WHEN rpd.cand_gender = 'male' THEN 1 ELSE 0 END) as male"), DB::Raw("SUM(CASE WHEN rpd.cand_gender = 'female' THEN 1 ELSE 0 END) as female"), DB::Raw("SUM(CASE WHEN rpd.cand_gender = 'third' THEN 1 ELSE 0 END) as third"))
		->where(array(
			'st_code' => $st_code,
			//'election_id' => '1'
		))
		->groupBy('lead_cand_party')
		->orderBy('lead_cand_party','asc')
		->get()->toArray();
		
		//echo '<pre>'; print_r($dataPartyWise); die;
		
		if($user->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}

		 if($request->path() == "$prefix/list-of-successful-candidates-pdf/$st_code"){
			$pdf = PDF::loadView('IndexCardReports.IndexCardReports.list-of-successful-candidates-pdf', compact('user_data','dataCaddidateWise','dataPartyWise','st_code'));
			
			 // code for verified pdf check and upload report no 2
				if(verifyreport(2, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'list-of-successful-candidates'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '2',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  
				  //Code end for verify and download
				
			return $pdf->download('2-List of Successful Candidates.pdf');
		}else if($request->path() == "$prefix/list-of-successful-candidates-xls/$st_code"){
		
					
			return Excel::download(new ListOfSuccessfulCandidates($dataCaddidateWise,$dataPartyWise), '2-List of Successful Candidates.xlsx');		
					
		}else{		
			return view("IndexCardReports.IndexCardReports.list-of-successful-candidates", compact('user_data','dataCaddidateWise','dataPartyWise','st_code'));
		}
    }
	
	public function listOfPoliticalPartiesParticipated(Request $request,$st_code){
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
                
        $user_data = $d;
		
		$dataParty = DB::table('m_party')
		->select('m_party.CCODE','m_party.PARTYTYPE','m_party.PARTYABBRE','m_party.PARTYNAME','cnd.cand_party_type')
		->join('candidate_nomination_detail as cnd','cnd.party_id', '=', 'm_party.CCODE')		
		->join('m_election_details as med',function($join){
			$join->on('med.st_code', '=', 'cnd.st_code')
			     ->on('med.CONST_NO', '=', 'cnd.ac_no');
		})
		->where('m_party.CCODE','!=', '1180')
		->where('m_party.CCODE','!=', '743')
		->where(array(
			'med.CONST_TYPE' => 'AC',
			'med.CURRENTELECTION' => 'Y',
			//'med.ELECTION_ID' => '1',
			'cnd.application_status' => '6',
			'cnd.finalaccepted' => '1',
			'cnd.st_code' => $st_code
		))
		->groupBy('m_party.CCODE')
		->orderBy('m_party.PARTYTYPE', 'ASC')
		->orderBy('cnd.cand_party_type', 'ASC')
		->orderBy('m_party.PARTYABBRE', 'ASC')
		->get();
		
		
		//dd($dataParty);
		
		$dataArray = array();
		foreach($dataParty as $key){
			$dataArray[$key->PARTYTYPE.'-'.$key->cand_party_type][] = array(
				'PARTYTYPE' 		=> $key->PARTYTYPE,
				'PARTYABBRE' 		=> $key->PARTYABBRE,
				'PARTYNAME' 		=> $key->PARTYNAME
			);
		}		
		//echo '<pre>'; print_r($dataArray); die;
		
		if($user->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}

		 if($request->path() == "$prefix/list-of-political-parties-participated-pdf/$st_code"){
			$pdf = PDF::loadView('IndexCardReports.IndexCardReports.list-of-political-parties-participated-pdf', compact('user_data','dataArray','st_code'));
			
			 // code for verified pdf check and upload report no 3
				if(verifyreport(3, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'list-of-political-parties-participated'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '3',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  
				  //Code end for verify and download
				
			return $pdf->download('3-List Of Political Parties Participated.pdf');
		}else if($request->path() == "$prefix/list-of-political-parties-participated-xls/$st_code"){
		
			return Excel::download(new ListOfPoliticalPartiesParticipated($dataArray), '3-List Of Political Parties Participated.xlsx');		

		}else{
			return view("IndexCardReports.IndexCardReports.list-of-political-parties-participated", compact('user_data','dataArray','st_code'));
		}
    }
	
	public function candidateDataSummary(Request $request,$st_code){
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
                
        $user_data = $d;
		
		//$st_code = 'S01';
		
		$dataAcType = DB::table('m_ac')
		->select('AC_TYPE',DB::Raw("count('AC_NO') as seats"))
		->JOIN('m_election_details as med',[['med.st_code','m_ac.ST_CODE'],['med.CONST_NO','m_ac.AC_NO']])
		->where(array(
			'm_ac.ST_CODE' => $st_code,
			'med.CONST_TYPE' => 'AC',
			'med.CURRENTELECTION' => 'Y'
		))
		->groupBy('AC_TYPE')
		->get()->toArray();
		
		 $candatawise = App\models\Admin\CandidateCountModel::get_count_by_status_category($st_code);
		 
		//echo '<pre>'; print_r($candatawise); echo '</pre>'; 

		$table='counting_master_'.strtolower(trim($st_code));


		$dfdata = DB::select("SELECT
           TEMP1.CATEGORY,TEMP1.fdmale AS fdmale,
           TEMP1.fdfemale AS fdfemale,TEMP1.fdthird AS fdthird,TEMP1.FD AS fd
           FROM
           (
           SELECT TEMP.*
           FROM (
           SELECT M.AC_TYPE as category,C.cand_gender,cp.ac_no,
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM $table as cp1
           where cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no and  C.cand_gender = 'male' 
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fdmale,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM $table as cp1
           where cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no and  C.cand_gender = 'female' 
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fdfemale,
            
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM $table as cp1
           where cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no and  C.cand_gender = 'third'
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fdthird,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes
           FROM $table as cp1
           where cp1.party_id != 1180 and cp1.ac_no = cp.ac_no
           GROUP BY cp1.ac_no ),5) < .16666 THEN 1 ELSE 0 END) as fd
            
           FROM  $table cp ,m_ac M,candidate_personal_detail  C
           WHERE cp.candidate_id not in(select candidate_id from winning_leading_candidate as w1
           where w1.ac_no = cp.ac_no and w1.st_code = '$st_code')
           AND cp.party_id != '1180'
           AND cp.ac_no=M.AC_NO
           AND C.cand_gender IN ('male','female','third')
           and C.candidate_id = cp.candidate_id
           AND M.AC_NO=cp.ac_no 
           AND M.ST_CODE='$st_code'
           GROUP By M.AC_TYPE
           )TEMP
           )TEMP1;");
		$dfdata = json_decode( json_encode($dfdata), true);

		//echo '<pre>'; print_r($candatawise); echo '</pre>';
		
		$dfdataarray = array();
		foreach($dfdata as $data){
			$dfdataarray[$data['category']] = [
				'category'      => $data['category'],
				'male'      => $data['fdmale'],
				'female'    => $data['fdfemale'],
				'third'     => $data['fdthird'], 
				'total'     => $data['fd'], 
			];
		}
		
		$acdataarray = array();
		foreach($dataAcType as $data){
			$acdataarray[$data->AC_TYPE] = [
				'category'      => $data->AC_TYPE,
				'seats'      	=> $data->seats, 
			];
		}
		
		//echo '<pre>'; print_r($acdataarray); die;
		if($user->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}

		 if($request->path() == "$prefix/candidate-data-summary-pdf/$st_code"){
			$pdf = PDF::loadView('IndexCardReports.IndexCardReports.candidate-data-summary-pdf', compact('user_data','acdataarray','dfdataarray','candatawise','st_code'));
			
			// code for verified pdf check and upload report no 9
				if(verifyreport(9, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'candidate-data-summary'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '9',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  
				  //Code end for verify and download
				
			return $pdf->download('9-Candidate Data Summary.pdf');
		}else if($request->path() == "$prefix/candidate-data-summary-xls/$st_code"){
		
					
		return Excel::download(new CandidateDataSummary($acdataarray,$dfdataarray,$candatawise), '9-Candidate Data Summary.xlsx');	
					
			
		}else{		
			return view("IndexCardReports.IndexCardReports.candidate-data-summary", compact('user_data','acdataarray','dfdataarray','candatawise','st_code'));
		}
    }
	
	public function detailedResults(Request $request,$st_code){
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);               
        $user_data = $d;
		
		//$st_code = 'S01';
		$table='counting_master_'.strtolower(trim($st_code));
		
		$data = DB::Select("SELECT TEMP.*,
		(SELECT SUM(total_vote) as total_vote FROM $table ccil
		where ccil.ac_no=TEMP.ac_no  group by TEMP.ac_no )as total_votes,

		(SELECT SUM(gen_electors_male + gen_electors_female + gen_electors_other + nri_male_electors + nri_female_electors + nri_third_electors + service_male_electors + service_female_electors + service_third_electors) as TOTAL_ELECT_VOTE  from electors_cdac cdac
		WHERE TEMP.AC_NO=cdac.ac_no and cdac.st_code = '$st_code' group by TEMP.AC_NO )AS total_electors
		FROM
		(
		select mp.AC_NO,mp.AC_NAME,AC_TYPE,cpd.cand_name, if(cpd.cand_gender='M','',cpd.cand_gender) AS cand_gender,
		cpd.cand_age, cpd.cand_category, p.PARTYABBRE AS party_abbre,
		ms.SYMBOL_DES,
		cci.postalballot_vote as postal,
		cci.total_vote as cand_total_vote
		from $table cci, candidate_personal_detail cpd,
		candidate_nomination_detail cnd left join m_symbol ms on cnd.symbol_id = ms.SYMBOL_NO,
		 m_ac mp,m_party p
		where cnd.candidate_id = cpd.candidate_id
		and cnd.candidate_id = cci.candidate_id
		and cnd.application_status = '6' 
		and cnd.finalaccepted = '1'
		and mp.ST_CODE = '$st_code'
		and cci.ac_no = mp.AC_NO
		and p.CCODE = cnd.party_id
		group by mp.AC_NO, cci.candidate_id
		ORDER BY mp.AC_NO ASC, cci.total_vote desc
		)TEMP");
		
		$dataArr = array();


				foreach($data as $raw){
						$type ='';
		if($raw->AC_TYPE != 'GEN') { $type = '('.$raw->AC_TYPE.')';  } 
					$dataArr['<b>'.$raw->AC_NO.' - '.$raw->AC_NAME.' '.$type.'&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;TOTAL ELECTORS &emsp;&emsp;</b>'.$raw->total_electors.''][] = array(
					
						'AC_NO' => $raw->AC_NO,
						'AC_NAME' => $raw->AC_NAME.' '.$type,
						'cand_name' => $raw->cand_name,
						'cand_gender' => $raw->cand_gender,
						'cand_age' => $raw->cand_age,
						'cand_category' => $raw->cand_category,
						'party_abbre' => $raw->party_abbre,
						'SYMBOL_DES' => ($raw->SYMBOL_DES)?$raw->SYMBOL_DES:'NOTA',
						'general_vote' => ($raw->cand_total_vote - $raw->postal),
						'postal_vote' => $raw->postal,
						'cand_total_vote' => $raw->cand_total_vote,
						'total_votes' => $raw->total_votes,
						'total_electors' => $raw->total_electors					
					);					
				}
				
		$all_state_Data = DB::select("SELECT sum(`postalballot_vote`) as all_state_postal,sum(`total_vote`) as all_state_total FROM $table");


		$st=getstatebystatecode($st_code);

		$state_name = $st->ST_NAME;
		
		//echo '<pre>'; print_r($all_state_Data); die;
		if($user->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}

		 if($request->path() == "$prefix/detailed-results-pdf/$st_code"){
			$pdf = PDF::loadView('IndexCardReports.IndexCardReports.detailed-results-pdf', compact('user_data','dataArr','all_state_Data','st_code'));
			
			// code for verified pdf check and upload report no 8
				if(verifyreport(10, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'detailed-results'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '10',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  
				  //Code end for verify and download
				
			return $pdf->download('10-Detailed Results.pdf');
		}else if($request->path() == "$prefix/detailed-results-xls/$st_code"){
		
				
			return Excel::download(new DetailedResults($dataArr,$all_state_Data,$state_name), '10-Detailed Results.xlsx');	
		}else{
			return view("IndexCardReports.IndexCardReports.detailed-results", compact('user_data','dataArr','all_state_Data','st_code'));
		}
    }   
	// public function setPaper($paper, $orientation = 'portrait'){
    //     $this->paper = $paper;
    //     $this->orientation = $orientation;
    //     $this->dompdf->setPaper($paper, $orientation);
    //     return $this;
    // }
	
	public function performanceOfWomenCandidates(Request $request,$st_code){
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);               
        $user_data = $d;
		
		//$st_code = 'S01';
		$table='counting_master_'.strtolower(trim($st_code));
		
		$data = DB::select("SELECT TEMP.*,
			CASE WHEN wlc.candidate_id=TEMP.candidate_id then 'W'
			WHEN (TEMP.total_get_vote/TEMP.total_valid_votes) < 0.16666 THEN 'DF' ELSE 'L' END AS 'FINAL_STATUS'
			FROM
			(
			SELECT MP.AC_NO, MP.AC_NAME, MP.AC_TYPE, A.`new_srno`,A.candidate_id,
			C.`cand_name`, P.PARTYABBRE, A.cand_party_type,
			(select total_vote from $table as cp where cp.candidate_id = A.candidate_id and cp.ac_no = A.`ac_no`) as total_get_vote,
			(select sum(total_vote) from $table as cp where cp.ac_no = A.ac_no and cp.party_id != '1180') as total_valid_votes,

			(select sum(total_vote) from $table as cp where cp.ac_no = A.ac_no ) as total_votes_polled,

			(select sum(electors_total + electors_service) from electors_cdac where electors_cdac.ac_no = A.ac_no AND electors_cdac.st_code = '$st_code') as e_all_t
			FROM candidate_nomination_detail AS A
			JOIN candidate_personal_detail AS C ON A.`candidate_id` = C.`candidate_id`
			JOIN m_party AS P ON P.CCODE = A.party_id
			JOIN m_ac AS MP ON MP.AC_NO = A.`ac_no`
			JOIN m_election_details AS med ON med.CONST_NO = A.`ac_no`
			WHERE C.`cand_gender` = 'female' AND med.CONST_TYPE = 'AC' and med.CURRENTELECTION = 'Y'
			and A.application_status = '6'
			AND A.finalaccepted = '1'
			and A.st_code = '$st_code'
			and MP.ST_CODE = '$st_code'
			GROUP BY A.ac_no, A.candidate_id
			ORDER BY A.ac_no, A.`new_srno` ASC
			)TEMP left join winning_leading_candidate wlc
			on wlc.candidate_id=TEMP.candidate_id");	
				
        $dataArray = array();
		foreach($data as $key){
			$type ='';
		if($key->AC_TYPE != 'GEN') { $type = '('.$key->AC_TYPE.')';  } 
			
			$dataArray[$key->AC_NO.' '.$key->AC_NAME.' '.$type][] = array(
				'AC_NO' 					=> $key->AC_NO,
				'AC_NAME' 					=> $key->AC_NAME,
				
				'srno' 						=> $key->new_srno,
				'candidate_name' 			=> $key->cand_name,
				'party_abbre' 				=> $key->PARTYABBRE,
				'PARTYTYPE' 				=> $key->cand_party_type,
				'candidate_votes' 			=> $key->total_get_vote,
				'total_electors' 			=> $key->e_all_t,
				'total_votes' 				=> $key->total_valid_votes,				
				'status' 					=> $key->FINAL_STATUS,
				'total_votes_polled'		=> $key->total_votes_polled		
			);
		}
		
		//echo '<pre>'; print_r($dataArray); die;	

		$st=getstatebystatecode($st_code);

		$state_name = $st->ST_NAME;
	
		if($user->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}

             //$customPaper = array(0,0,567.00,283.80);
            
		 if($request->path() == "$prefix/performance-of-women-candidates-pdf/$st_code"){
			//$pdf = PDF::loadView('IndexCardReports.IndexCardReports.performance-of-women-candidates-pdf', compact('user_data','dataArray','st_code'));
			//$pdf = PDF::loadView('IndexCardReports.IndexCardReports.performance-of-women-candidates-pdf', ['user_data' => $user_data,'st_code'=>$st_code,'dataArray'=> $dataArray]);

			$pdf = MPDF::loadView('IndexCardReports.IndexCardReports.performance-of-women-candidates-pdf', compact('user_data','dataArray','st_code'));
             //return $pdf->stream('Ro.scrunity-report.pdf');
    
			//$pdf->setPaper([0, 0, 685.98, 396.85], 'landscape');
			// $pdf;
			
			// code for verified pdf check and upload report no 7
				if(verifyreport(7, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'performance-of-women-candidates'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '7',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  
				  //Code end for verify and download
				
			return $pdf->download('7-Individual Performance Of Women Candidate.pdf');
		}else if($request->path() == "$prefix/performance-of-women-candidates-xls/$st_code"){
		
					
			$dataArray = json_decode( json_encode($dataArray), true);
				
			return Excel::download(new PerformanceOfWomenCandidates($dataArray,$state_name), '7-Individual Performance Of Women Candidate.xlsx');
			
		}else{
			return view("IndexCardReports.IndexCardReports.performance-of-women-candidates", compact('user_data','dataArray','st_code'));
		}
    }   
	
	
	
	public function performanceOfPoliticalParties(Request $request,$st_code){
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);               
        $user_data = $d;
		
		//$st_code = 'S01';
		$table='counting_master_'.strtolower(trim($st_code));
		
	$data = DB::select("select `cnd`.`party_id`,`m_party`.`PARTYTYPE`, `cnd`.`cand_party_type`, `m_party`.`PARTYABBRE`,
	(select COUNT(cp.candidate_id) from $table as cp inner join `candidate_nomination_detail` as cnd2 on `cnd2`.`candidate_id` = `cp`.`candidate_id` and `cnd2`.`ac_no` = `cp`.`ac_no` where `cnd2`.`application_status` = '6' and `cnd2`.`finalaccepted` = '1' and  cnd2.party_id = `cnd`.`party_id`) as contested,
	
	(select COUNT(DISTINCT w.candidate_id) from $table as cp LEFT JOIN `winning_leading_candidate` w ON cp.candidate_id=w.candidate_id where cp.party_id = `cnd`.`party_id`) as won,
	
	(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes
	FROM $table AS cp1 WHERE cp1.party_id != 1180 and  cp1.ac_no = cp.ac_no GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) AS fd FROM $table AS cp inner join `candidate_nomination_detail` as cnd2 on `cnd2`.`candidate_id` = `cp`.`candidate_id` and `cnd2`.`ac_no` = `cp`.`ac_no`  where `cnd2`.`application_status` = '6' and `cnd2`.`finalaccepted` = '1' and  cnd2.party_id = `cnd`.`party_id` AND `cp`.`candidate_id` != (SELECT candidate_id FROM winning_leading_candidate AS w1 WHERE w1.ac_no = cp.ac_no AND w1.st_code = '$st_code')  GROUP BY cnd2.party_id) AS fd,
	
	(select SUM(cp.total_vote) from $table as cp inner join `candidate_nomination_detail` as cnd2 on `cnd2`.`candidate_id` = `cp`.`candidate_id` and `cnd2`.`ac_no` = `cp`.`ac_no` where `cnd2`.`application_status` = '6' and `cnd2`.`finalaccepted` = '1' and  cnd2.party_id = `cnd`.`party_id` AND cnd2.ac_no = cp.ac_no and cnd2.st_code = '$st_code') as vote_secured_by_party,
	
	(select SUM(cp.total_vote) from $table as cp where party_id != '1180') as total_valid_votes,
	(select SUM(cp.total_vote) from $table as cp) as total_votes,
	(SELECT SUM(total_vote)  FROM `candidate_nomination_detail` cnd2
	join $table as cp on cp.ac_no = cnd2.ac_no
	 WHERE cnd2.party_id = `cnd`.`party_id` AND cnd2.application_status = '6' AND cnd2.finalaccepted = '1' and cnd2.st_code = '$st_code') as contests_total_votes
	 from `m_party` 
	 inner join `candidate_nomination_detail` as `cnd` on `cnd`.`party_id` = `m_party`.`CCODE` 
	 inner join `m_election_details` as `med` on `med`.`st_code` = `cnd`.`st_code` and `med`.`CONST_NO` = `cnd`.`ac_no` where (`med`.`CONST_TYPE` = 'AC' and `med`.`CURRENTELECTION` = 'Y' and `cnd`.`application_status` = '6' and `cnd`.`finalaccepted` = '1' and `cnd`.`st_code` = '$st_code') group by `cnd`.`party_id` order by `m_party`.`PARTYTYPE` asc,cnd.cand_party_type ASC,`m_party`.`PARTYABBRE` asc");	
		
		//dd($data);
		
        $dataArray = array();
		foreach($data as $key){
			$dataArray[$key->PARTYTYPE.'-'.$key->cand_party_type][] = array(
				'PARTYTYPE' 				=> $key->PARTYTYPE,
				'PARTYABBRE' 				=> $key->PARTYABBRE,
				'contested' 				=> $key->contested,
				'won' 						=> $key->won,
				'fd' 						=> $key->fd,
				'vote_secured_by_party' 	=> $key->vote_secured_by_party,
				'total_valid_votes' 		=> $key->total_valid_votes,
				'total_votes' 				=> $key->total_votes,
				'contests_total_votes' 		=> $key->contests_total_votes,
				
			);
		}
		
		//echo '<pre>'; print_r($dataArray); die;	

		$state_name = 'Andhra Pradesh';
	
		if($user->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}

		 if($request->path() == "$prefix/performance-of-political-parties-pdf/$st_code"){
			$pdf = PDF::loadView('IndexCardReports.IndexCardReports.performance-of-political-parties-pdf', compact('user_data','dataArray','st_code'));
			
			 // code for verified pdf check and upload report no 5
				if(verifyreport(5, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'performance-of-political-parties'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
				  //$file->move($destination_path,$file_name);
				  //dd($destination_path);


                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '5',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  
				  //Code end for verify and download
				
			return $pdf->download('5-Performance of Political Parties.pdf');
		}else if($request->path() == "$prefix/performance-of-political-parties-xls/$st_code"){		
			
			
			return Excel::download(new PerformanceOfPoliticalParties($dataArray), '5-Performance of Political Parties.xlsx');
				
		}else{
			return view("IndexCardReports.IndexCardReports.performance-of-political-parties", compact('user_data','dataArray','st_code'));
		}
    }   
	
	
	
	public function noofelectors(request $request, $st_code){

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);

        $session['election_detail'] = array();

        $user_data = $d;
		$y = getElectionYear();
        $st_code = $st_code;


    $electorsdata = DB::select("SELECT m_ac.`AC_NO`,m_ac.`AC_NAME`,m_ac.`AC_TYPE`,
		SUM(ec.gen_electors_male+ec.nri_male_electors) AS gen_male,
		SUM(ec.gen_electors_female+ec.nri_female_electors) AS gen_female,
		SUM(ec.gen_electors_other+ec.nri_third_electors) AS gen_third,
		SUM(ec.gen_electors_male+ec.nri_male_electors+ec.gen_electors_female+ec.nri_female_electors+ec.gen_electors_other+ec.nri_third_electors) AS gen_total,
		SUM(ec.service_male_electors) AS service_male,
		SUM(ec.service_female_electors) AS service_female,
		SUM(ec.service_male_electors + ec.service_female_electors) AS service_total,
		SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors) AS grand_male,
		SUM(ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors) AS grand_female,
		SUM(ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS grand_third,
		SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors+ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors
		+ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS grand_total,
		SUM(ec.nri_male_electors) AS nri_male,
		SUM(ec.nri_female_electors) AS nri_female,
		SUM(ec.nri_third_electors) AS nri_third,
		SUM(ec.nri_male_electors+ec.nri_female_electors+ec.nri_third_electors) AS nri_total
		FROM electors_cdac AS ec
		INNER JOIN m_election_details AS med ON med.st_code = ec.ST_CODE AND med.CONST_NO = ec.ac_no
		INNER JOIN m_ac ON m_ac.`AC_NO` = ec.`ac_no` AND m_ac.`ST_CODE` = ec.`st_code`
		WHERE ec.YEAR = '$y' AND  med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y' AND  ec.st_code = '$st_code'
		GROUP BY m_ac.`AC_NO`");
		
		
		$electorsdata_total = DB::select("SELECT
		SUM(ec.gen_electors_male+ec.nri_male_electors) AS gen_male,
		SUM(ec.gen_electors_female+ec.nri_female_electors) AS gen_female,
		SUM(ec.gen_electors_other+ec.nri_third_electors) AS gen_third,
		SUM(ec.gen_electors_male+ec.nri_male_electors+ec.gen_electors_female+ec.nri_female_electors+ec.gen_electors_other+ec.nri_third_electors) AS gen_total,
		SUM(ec.service_male_electors) AS service_male,
		SUM(ec.service_female_electors) AS service_female,
		SUM(ec.service_male_electors + ec.service_female_electors) AS service_total,
		SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors) AS grand_male,
		SUM(ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors) AS grand_female,
		SUM(ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS grand_third,
		SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors+ec.gen_electors_female+
		  ec.service_female_electors+ec.nri_female_electors+ec.gen_electors_other+
		   ec.nri_third_electors+ec.service_third_electors) AS grand_total,
		SUM(ec.nri_male_electors) AS nri_male,
		SUM(ec.nri_female_electors) AS nri_female,
		SUM(ec.nri_third_electors) AS nri_third,
		SUM(ec.nri_male_electors+ec.nri_female_electors+ec.nri_third_electors) AS nri_total
		FROM electors_cdac AS ec
		INNER JOIN m_election_details AS med ON med.st_code = ec.ST_CODE AND med.CONST_NO = ec.ac_no
		INNER JOIN m_ac ON m_ac.`AC_NO` = ec.`ac_no` AND m_ac.`ST_CODE` = ec.`st_code`
		WHERE ec.YEAR = '$y' AND  med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y' AND  ec.st_code = '$st_code'");

//dd($electorsdata);

                  if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                  }else if($user->designation == 'CEO'){
                            $prefix     = 'pcceo';
                  }else if($user->role_id == '27'){
                          $prefix     = 'eci-index';
                  }else if($user->role_id == '7'){
                          $prefix     = 'eci';
                  }

                if($request->path() == "$prefix/ac-wise-no-of-electors/$st_code"){
                    return view('IndexCardReports.IndexCardReports.ac-wise-no-of-electors',
                      compact('user_data','electorsdata','st_code','electorsdata_total'));
                }elseif($request->path() == "$prefix/ac-wise-no-of-electors-pdf/$st_code"){
					
                $pdf=PDF::loadView('IndexCardReports.IndexCardReports.ac-wise-no-of-electors-pdf',compact('user_data','electorsdata','st_code','electorsdata_total'));
				// code for report verify and download report no 11
				if(verifyreport(12, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'ac-wise-no-of-electors'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
	

                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '12',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  //code for verify report ends here
                return $pdf->download('11 - AC Wise Number Of Electors.pdf');
                }elseif($request->path() == "$prefix/ac-wise-no-of-electors-excel/$st_code"){
				
					return Excel::download(new AcWiseNoOfElectors($electorsdata,$electorsdata_total), '11 - AC Wise Number Of Electors.xlsx');

                 

               }
    }
	
	
	public function votersinformation(request $request, $st_code){

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);

        $session['election_detail'] = array();

        $user_data = $d;
		$y = getElectionYear();
		
       $stcode =strtolower($st_code);
		
		//dd($st_code);


    $electorsdata = DB::select("SELECT m_ac.`AC_NO`,m_ac.`AC_NAME`,m_ac.`AC_TYPE`,
		SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors) AS grand_male,
		SUM(ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors) AS grand_female,
		SUM(ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS grand_third,
		SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors+ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors
		+ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS grand_total,
		SUM(ec.nri_male_electors+ec.nri_female_electors+ec.nri_third_electors) AS nri_total,
		SUM(ec.service_male_electors + ec.service_female_electors) AS service_total,
		
		
		SUM(ecoi.general_male_voters + ecoi.nri_male_voters) AS male_voter,
		SUM(ecoi.general_female_voters + ecoi.nri_female_voters) AS female_voter,
		SUM(ecoi.general_other_voters + ecoi.nri_other_voters) AS third_voter,		
		SUM(ecoi.service_postal_votes_under_section_8 + ecoi.service_postal_votes_gov) AS postal,		
		SUM(ecoi.test_votes_49_ma) AS test_votes,		
		SUM(ecoi.general_male_voters + ecoi.nri_male_voters + ecoi.general_female_voters + ecoi.nri_female_voters + ecoi.general_other_voters + ecoi.nri_other_voters + ecoi.service_postal_votes_under_section_8 + ecoi.service_postal_votes_gov + ecoi.test_votes_49_ma) AS total_voter,
		SUM(ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters) AS nri_voter,
		rm.rejected_votes AS postal_rejected,
		ifnull(SUM(ecoi.test_votes_49_ma + ecoi.rejected_votes_due_2_other_reason),0) AS rejected_votes,
		(select sum(cm.total_vote) as total_valid_votes from counting_master_$stcode as cm where cm.ac_no = ec.ac_no and party_id != '1180') as total_valid_votes,
		(select cm.total_vote as nota_votes from counting_master_$stcode as cm where cm.ac_no = ec.ac_no and party_id = '1180') as nota_votes,
		rm.tended_votes AS tended_votes
				
		FROM electors_cdac AS ec
		INNER JOIN m_election_details AS med ON med.st_code = ec.ST_CODE AND med.CONST_NO = ec.ac_no
		left JOIN electors_cdac_other_information AS ecoi ON ecoi.st_code = ec.ST_CODE AND ecoi.ac_no = ec.ac_no
		INNER JOIN round_master AS rm ON rm.st_code = ec.ST_CODE AND rm.ac_no = ec.ac_no
		INNER JOIN m_ac ON m_ac.`AC_NO` = ec.`ac_no` AND m_ac.`ST_CODE` = ec.`st_code`
		WHERE ec.YEAR = '$y' AND  med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y' AND  ec.st_code = '$stcode'
		GROUP BY m_ac.`AC_NO`");
		
		
		$electorsdata_total = DB::select("SELECT
		SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors) AS grand_male,
		SUM(ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors) AS grand_female,
		SUM(ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS grand_third,
		SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors+ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors
		+ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS grand_total,
		SUM(ec.nri_male_electors+ec.nri_female_electors+ec.nri_third_electors) AS nri_total,
		SUM(ec.service_male_electors + ec.service_female_electors) AS service_total,
		
		
		SUM(ecoi.general_male_voters + ecoi.nri_male_voters) AS male_voter,
		SUM(ecoi.general_female_voters + ecoi.nri_female_voters) AS female_voter,
		SUM(ecoi.general_other_voters + ecoi.nri_other_voters) AS third_voter,		
		SUM(ecoi.service_postal_votes_under_section_8 + ecoi.service_postal_votes_gov) AS postal,	
		SUM(ecoi.test_votes_49_ma) AS test_votes,
		SUM(ecoi.general_male_voters + ecoi.nri_male_voters + ecoi.general_female_voters + ecoi.nri_female_voters + ecoi.general_other_voters + ecoi.nri_other_voters + ecoi.service_postal_votes_under_section_8 + ecoi.service_postal_votes_gov + ecoi.test_votes_49_ma) AS total_voter,
		SUM(ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters) AS nri_voter,
		SUM(rm.rejected_votes) AS postal_rejected,
		ifnull(SUM(ecoi.test_votes_49_ma + ecoi.rejected_votes_due_2_other_reason),0) AS rejected_votes,
		(select sum(cm.total_vote) as total_valid_votes from counting_master_$stcode as cm where party_id != '1180') as total_valid_votes,
		(select sum(cm.total_vote) as nota_votes from counting_master_$stcode as cm where cm.party_id = '1180') as nota_votes,
		SUM(rm.tended_votes) AS tended_votes
				
		FROM electors_cdac AS ec
		INNER JOIN m_election_details AS med ON med.st_code = ec.ST_CODE AND med.CONST_NO = ec.ac_no
		left JOIN electors_cdac_other_information AS ecoi ON ecoi.st_code = ec.ST_CODE AND ecoi.ac_no = ec.ac_no
		INNER JOIN round_master AS rm ON rm.st_code = ec.ST_CODE AND rm.ac_no = ec.ac_no
		INNER JOIN m_ac ON m_ac.`AC_NO` = ec.`ac_no` AND m_ac.`ST_CODE` = ec.`st_code`
		WHERE ec.YEAR = '$y' AND  med.CONST_TYPE = 'AC' AND  med.CURRENTELECTION = 'Y' AND  ec.st_code = '$stcode'");
		

//dd($electorsdata);

                  if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                  }else if($user->designation == 'CEO'){
                            $prefix     = 'pcceo';
                  }else if($user->role_id == '27'){
                          $prefix     = 'eci-index';
                  }else if($user->role_id == '7'){
                          $prefix     = 'eci';
                  }

                if($request->path() == "$prefix/ac-wise-voters-information/$st_code"){
                    return view('IndexCardReports.IndexCardReports.ac-wise-voters-information',
                      compact('user_data','electorsdata','st_code','electorsdata_total'));
                }elseif($request->path() == "$prefix/ac-wise-voters-information-pdf/$st_code"){
					
                $pdf=PDF::loadView('IndexCardReports.IndexCardReports.ac-wise-voters-information-pdf',compact('user_data','electorsdata','st_code','electorsdata_total'));
				// code for report verify and download report no 11
				if(verifyreport(13, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'ac-wise-voters-information'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
	

                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '13',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  //code for verify report ends here
                return $pdf->download('12 - AC Wise Voters Information.pdf');
                }elseif($request->path() == "$prefix/ac-wise-voters-information-excel/$st_code"){
				
					return Excel::download(new AcWiseVotersInformation($electorsdata,$electorsdata_total), '12 - AC Wise Voters Information.xlsx');

               }
    }
	
	
	
	
	public function acwisecandidatedatasummary(request $request, $st_code){
		
		ini_set("pcre.backtrack_limit", "50000000");
	
	ini_set('max_execution_time', 0);
    ini_set('memory_limit', '-1');

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
                
        $user_data = $d;
				
		$cat = ['general','sc','st'];
		
		$dataAcType = DB::table('m_ac')
		->select('AC_TYPE','AC_NO','AC_NAME')
		->JOIN('m_election_details as med',[['med.st_code','m_ac.ST_CODE'],['med.CONST_NO','m_ac.AC_NO']])
		->where(array(
			'm_ac.ST_CODE' => $st_code,
			'med.CONST_TYPE' => 'AC',
			'med.CURRENTELECTION' => 'Y'
		))
		->orderBy('AC_NO')
		->get()->toArray();

//dd($candatawise);	


                  if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                  }else if($user->designation == 'CEO'){
                            $prefix     = 'pcceo';
                  }else if($user->role_id == '27'){
                          $prefix     = 'eci-index';
                  }else if($user->role_id == '7'){
                          $prefix     = 'eci';
                  }

                if($request->path() == "$prefix/ac-wise-candidate-data-summary/$st_code"){
                    return view('IndexCardReports.IndexCardReports.ac-wise-candidate-data-summary',
                      compact('user_data','dataAcType','st_code','cat'));
                }elseif($request->path() == "$prefix/ac-wise-candidate-data-summary-pdf/$st_code"){
					
                $pdf=PDF::loadView('IndexCardReports.IndexCardReports.ac-wise-candidate-data-summary-pdf',compact('user_data','dataAcType','st_code','cat'));
				// code for report verify and download report no 11
				if(verifyreport(14, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'ac-wise-candidate-data-summary'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
	

                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '14',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  //code for verify report ends here
                return $pdf->download('13 - AC Wise Candidate data Summary.pdf');
                }elseif($request->path() == "$prefix/ac-wise-candidate-data-summary-excel/$st_code"){
				
				return Excel::download(new AcWiseCandidateDataSummary($dataAcType,$cat,$st_code), '13 - AC Wise Candidate data Summary.xlsx');

               }
    }
	
	
	
		public function constituencywisedetailedresult(request $request, $st_code){

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
                
        $user_data = $d;
				
		
		$stcode =strtolower($st_code);

		
		                $candidateData = DB::select("SELECT TEMP.*,
						(SELECT SUM(ccil.general_male_voters + ccil.general_female_voters + ccil.general_other_voters + ccil.nri_male_voters + ccil.nri_female_voters + ccil.nri_other_voters + ccil.service_postal_votes_under_section_8 + ccil.service_postal_votes_gov) as total_vote FROM electors_cdac_other_information ccil
						where ccil.st_code = '$st_code' AND ccil.ac_no=TEMP.ac_no  group by TEMP.ac_no )as total_votes,
						(SELECT SUM(gen_electors_male + gen_electors_female + gen_electors_other + nri_male_electors + nri_female_electors + nri_third_electors + service_male_electors + service_female_electors + service_third_electors) as TOTAL_ELECT_VOTE  from electors_cdac cdac
						WHERE TEMP.AC_NO=cdac.ac_no and cdac.st_code = '$st_code' group by TEMP.AC_NO )AS total_electors
						FROM
						(
						select mp.ST_CODE,mp.AC_NO,mp.AC_NAME,cpd.cand_name,if(cpd.cand_gender='M','',cpd.cand_gender) AS cand_gender,
						cpd.cand_age, cpd.cand_category, cci.party_abbre,
						ms.SYMBOL_DES,(cci.total_vote - cci.postalballot_vote) as general,
						cci.postalballot_vote as postal,
						cci.total_vote as cand_total_vote,
						(select new_srno from candidate_nomination_detail cnd2 where cnd2.ac_no=cci.ac_no
						and cnd2.application_status='6' and cnd2.finalaccepted=1 and cnd2.candidate_id = cci.candidate_id and cnd2.st_code = '$st_code')as new_srno
						from counting_master_$stcode cci, candidate_personal_detail cpd,
						candidate_nomination_detail cnd join m_symbol ms on cnd.symbol_id = ms.SYMBOL_NO,
						m_ac mp
						where cci.candidate_id = cpd.candidate_id
						and cnd.candidate_id = cci.candidate_id
						and cnd.application_status = '6'
						and cnd.finalaccepted = '1'
						and cnd.ST_CODE =  mp.ST_CODE
						and cci.ac_no = mp.AC_NO
						and cnd.ST_CODE =  '$st_code'						
						group by mp.ST_CODE,mp.AC_NO, cci.candidate_id
						ORDER BY mp.ST_CODE,mp.AC_NO,new_srno ASC
						)TEMP");
		

							
						
						foreach($candidateData as $raw){
					
					$dataArr[$raw->AC_NO.' . '.$raw->AC_NAME.' &#160;<b> ( Total Electors &#160;</b>'.$raw->total_electors.' )'][] = array(
					
						
						'PC_NAME' => $raw->AC_NAME,
						'cand_name' => $raw->cand_name,
						'cand_gender' => $raw->cand_gender,
						'cand_age' => $raw->cand_age,
						'cand_category' => $raw->cand_category,
						'party_abbre' => $raw->party_abbre,
						'SYMBOL_DES' => $raw->SYMBOL_DES,
						'general_vote' => $raw->general,
						'postal_vote' => $raw->postal,
						'cand_total_vote' => $raw->cand_total_vote,
						'total_votes' => $raw->total_votes,
						'total_electors' => $raw->total_electors
					
					);
					
				}
				
				
				$all_Data = DB::select("SELECT sum(total_vote-postalballot_vote) as all_evm,sum(`postalballot_vote`) as all_postal,sum(`total_vote`) as all_total FROM counting_master_$stcode");
				
				//dd($dataArr);
						
						


                  if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                  }else if($user->designation == 'CEO'){
                            $prefix     = 'pcceo';
                  }else if($user->role_id == '27'){
                          $prefix     = 'eci-index';
                  }else if($user->role_id == '7'){
                          $prefix     = 'eci';
                  }

                if($request->path() == "$prefix/constituency-wise-detailed-result/$st_code"){
                    return view('IndexCardReports.IndexCardReports.constituency-wise-detailed-result',
                      compact('user_data','dataArr','all_Data','st_code'));
                }elseif($request->path() == "$prefix/constituency-wise-detailed-result-pdf/$st_code"){
					
                $pdf=PDF::loadView('IndexCardReports.IndexCardReports.constituency-wise-detailed-result-pdf',compact('user_data','dataArr','all_Data','st_code'));
				// code for report verify and download report no 11
				if(verifyreport(15, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'constituency-wise-detailed-result'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
	

                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '15',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  //code for verify report ends here
                return $pdf->download('15 - Constituency wise detailed Result.pdf');
                }elseif($request->path() == "$prefix/constituency-wise-detailed-result-excel/$st_code"){
				
				return Excel::download(new ConstituencyWiseDetailedResult($dataArr), '15 - Constituency wise detailed Result.xlsx');

               }
    }
	
	
	public function listofsuccessfulcandidatesb(request $request, $st_code){

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
                
        $user_data = $d;
				
		$stcode =strtolower($st_code);
		
		 $arraydata = DB::select("SELECT m.st_code,`m`.`AC_TYPE`, `m`.`AC_NAME`, `m`.`AC_NO`, SUM(total_vote) as TotalVote, `cpd`.`cand_name` as Cand_Name, `cpd`.`cand_category` as cand_category, `winn`.`lead_party_abbre` as Party_Abbre,`SYMBOL_DES` as Party_symbol, `winn`.`margin`
      FROM winning_leading_candidate AS winn INNER JOIN m_ac AS m ON m.st_code = winn.st_code AND m.ac_no = winn.ac_no INNER JOIN candidate_nomination_detail AS cond ON cond.candidate_id = winn.candidate_id JOIN candidate_personal_detail AS cpd ON cpd.candidate_id = winn.candidate_id AND cond.application_status = 6 AND finalaccepted = 1 INNER JOIN counting_master_$stcode AS counting ON  winn.ac_no = counting.ac_no INNER JOIN m_symbol AS symbol ON cond.symbol_id = symbol.SYMBOL_NO where winn.st_code = '$st_code' GROUP BY m.st_code,m.ac_no order by m.ac_no asc");

		//dd($arraydata);	


                  if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                  }else if($user->designation == 'CEO'){
                            $prefix     = 'pcceo';
                  }else if($user->role_id == '27'){
                          $prefix     = 'eci-index';
                  }else if($user->role_id == '7'){
                          $prefix     = 'eci';
                  }

                if($request->path() == "$prefix/list-of-successful-candidates-b/$st_code"){
                    return view('IndexCardReports.IndexCardReports.list-of-successful-candidates-b',
                      compact('user_data','arraydata','st_code'));
                }elseif($request->path() == "$prefix/list-of-successful-candidates-b-pdf/$st_code"){
					
                $pdf=PDF::loadView('IndexCardReports.IndexCardReports.list-of-successful-candidates-b-pdf',compact('user_data','arraydata','st_code'));
				// code for report verify and download report no 11
				if(verifyreport(16, $st_code) != 0 && verifyreport(7777, $st_code) != 0){
        
                  $file_name = 'list-of-successful-candidates-b'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
				  $year         = date('Y');
				   if (!file_exists('uploads1/statistical_report/'.'/'.$year.'/'.$st_code)) {
						mkdir('uploads1/statistical_report/'.'/'.$year.'/'.$st_code, 0777, true);
				   }
				
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/statistical_report/'.'/'.$year.'/'.$st_code;
	

                  $pdf->save(public_path($destination_path.'/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '16',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);
				  
				  }
				  //code for verify report ends here
                return $pdf->download('16 - List Of the Successful Candidate (B).pdf');
                }elseif($request->path() == "$prefix/list-of-successful-candidates-b-excel/$st_code"){
				
				return Excel::download(new ListOfSuccessfulCandidatesB($arraydata), '16 - List Of the Successful Candidate (B).xlsx');

               }
    }
		
}