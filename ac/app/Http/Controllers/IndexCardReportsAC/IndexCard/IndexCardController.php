<?php

namespace App\Http\Controllers\IndexCardReportsAC\IndexCard;

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
    use \Excel;
    use MPDF;
    use App;
    use App\commonModel;  
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\ACCEOModel;
	use App\adminmodel\ACCEOReportModel;
	use App\models\Admin\VoterModel;
    use App\Classes\xssClean;
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Crypt;
	
	use App\models\indexcard\IndexCardReport;

class IndexCardController extends Controller
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
			   case '5':
                   $this->middleware('deo');
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
    
    
    public function indexcard(Request $request){
		
		$stateList = array();
       $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
        
        $session['election_detail'] = array();
        $session['election_detail']['st_code'] = $user->st_code;
        $session['election_detail']['st_name'] = $user->placename;
        $user_data = $d;
		
		$user_data = $d;
    	//echo "<pre>"; print_r($session['election_detail']); die;
        if($user->role_id == '4')
		{
			$acList = DB::table('m_ac')
        				->select(['AC_NO','AC_NAME'])
        				->where('ST_CODE',$session['election_detail']['st_code'])
        				->get()->toArray();
        	// echo "<pre>"; print_r($pcList); die;
        
			return view('IndexCardReports.IndexCardDataACWise.indexcardacselect', compact('session','acList','user_data','sched'));
			
		}elseif(($user->role_id == '7') || ($user->role_id == '27'))
		{
			$stateList = DB::table('m_state')
        				->select('m_state.ST_CODE','m_state.ST_NAME')
						->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
						->where('m_election_details.CONST_TYPE','AC')
						->where('m_election_details.CURRENTELECTION','Y')
						->orderBy('m_state.ST_NAME','ASC')
						->groupBy('m_state.ST_CODE')
        				->get()->toArray();		
        
			return view('IndexCardReports.IndexCardDataACWise.indexcardselectstateandac', compact('session','stateList','user_data','sched'));	
						
        }elseif($user->designation == 'ROAC'){
            $rRequest = array(
                '_token' => Session::token(),
                'st_code' => $user->st_code,
                'ac' => $user->ac_no
            );
            $request->request->add($rRequest);
 
            return $this->getindexcarddata($request);
   
        }
        
    }
   
   
    public function ajaxpccall(Request $request){
			
					$acList = DB::table('m_ac')
        				->select(['m_ac.AC_NO','m_ac.AC_NAME'])
						->join('m_election_details', function($join){
							$join->on('m_ac.ST_CODE','m_election_details.ST_CODE')
								->on('m_ac.AC_NO','m_election_details.CONST_NO');
						}
						)
						->where('m_election_details.CONST_TYPE','AC')
						->where('m_election_details.CURRENTELECTION','Y')
        				->where('m_ac.ST_CODE',$request->st_code);
						
						if(Auth::user()->designation == 'DEO'){
							$acList->where('m_ac.DIST_NO_HDQTR',Auth::user()->dist_no);
						}
						
						$acList = $acList->orderBy('m_ac.AC_NO','ASC')
        				->get()->toArray();
			
			?>
				<option value="">Select AC</option>
			<?php
				foreach ($acList as $acLists) {
					?>
			<option value="<?php echo $acLists->AC_NO; ?>"><?php echo $acLists->AC_NO.'-'.$acLists->AC_NAME; ?></option>
			<?php
				}
    }
   

 public function getindexcarddata(Request $request, $ac = 0 , $st_code = 0){
	 
	 //echo $st_code.'-'.$ac;
	// echo '<pre>'; print_r($request->all());  die;
	
	$stateList = array();
	
	$getIndexCardDataACWise = 	$getIndexCardDataCandidatesVotesACWise = $acinfo = $acList = array();
	
	
    	$session = $request->session()->all();
        $user = Auth::user();
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $d=$this->commonModel->getunewserbyuserid($uid);
            
           $session['election_detail'] = array();
           $session['election_detail']['st_code'] = $user->st_code;
		   
		   $ele_details = array();
		   
		   $postfix = '';
		   
		if($user->designation == 'ROAC'){ 
			$st_code 	= $user->st_code;
			$ac 		= $user->ac_no;
			
			$ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
			$prefix 	= 'roac';
		}else if($user->designation == 'CEO'){
			$st_code 	= $user->st_code;
			
			if($ac == 0){	
			
				$ac 		= $request->ac_no;
			}else{				
				$ac 		= $ac;
			}	
			$prefix 	= 'acceo';
			$postfix 	= '/'.$ac;
			
			$stateList = DB::table('m_state')
        				->select('m_state.ST_CODE','m_state.ST_NAME')
						->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
						->where('m_election_details.CONST_TYPE','AC')
						->where('m_election_details.CURRENTELECTION','Y')
						->where('m_state.ST_CODE',$st_code)
						->orderBy('m_state.ST_NAME','ASC')
						->groupBy('m_state.ST_CODE')
        				->get()->toArray();
			$acList = DB::table('m_ac')
        				->select(['m_ac.AC_NO','m_ac.AC_NAME'])
						->join('m_election_details', function($join){
							$join->on('m_ac.ST_CODE','m_election_details.ST_CODE')
								->on('m_ac.AC_NO','m_election_details.CONST_NO');
						}
						)
						->where('m_election_details.CONST_TYPE','AC')
						->where('m_election_details.CURRENTELECTION','Y')
        				->where('m_ac.ST_CODE',$st_code)
        				->get()->toArray();
		}else if($user->designation == 'DEO'){
			$st_code 	= $user->st_code;
			$dist_no 	= $user->dist_no;
			
			if($ac == 0){	
			
				$ac 		= $request->ac_no;
			}else{				
				$ac 		= $ac;
			}	
			$prefix 	= 'acdeo';
			$postfix 	= '/'.$ac;
			
			$stateList = DB::table('m_state')
        				->select('m_state.ST_CODE','m_state.ST_NAME')
						->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
						->where('m_election_details.CONST_TYPE','AC')
						->where('m_election_details.CURRENTELECTION','Y')
						->where('m_state.ST_CODE',$st_code)
						->orderBy('m_state.ST_NAME','ASC')
						->groupBy('m_state.ST_CODE')
        				->get()->toArray();
			$acList = DB::table('m_ac')
        				->select(['m_ac.AC_NO','m_ac.AC_NAME'])
						->join('m_election_details', function($join){
							$join->on('m_ac.ST_CODE','m_election_details.ST_CODE')
								->on('m_ac.AC_NO','m_election_details.CONST_NO');
						}
						)
						->where('m_election_details.CONST_TYPE','AC')
						->where('m_election_details.CURRENTELECTION','Y')
        				->where('m_ac.ST_CODE',$st_code)
        				->where('m_ac.DIST_NO_HDQTR',$dist_no)
        				->get()->toArray();
				
		}else if($user->role_id == '27'){
			
			if(($st_code == 0) && ($ac == 0)){		
				$st_code 	= $request->st_code;
				$ac 		= $request->ac_no;
			}else{
				$st_code 	= $st_code;
				$ac 		= $ac;
			}
			$postfix 	= '/'.$ac.'/'.$st_code;
			$prefix 	= 'eci-index';
			
			
			$stateList = DB::table('m_state')
        				->select('m_state.ST_CODE','m_state.ST_NAME')
						->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
						->where('m_election_details.CONST_TYPE','AC')
						->where('m_election_details.CURRENTELECTION','Y')
						->orderBy('m_state.ST_NAME','ASC')
						->groupBy('m_state.ST_CODE')
        				->get()->toArray();
						
		}else if($user->role_id == '7'){
			if(($st_code == 0) && ($ac == 0)){		
				$st_code 	= $request->st_code;
				$ac 		= $request->ac_no;
			}else{
				$st_code 	= $st_code;
				$ac 		= $ac;
			}
			$prefix 	= 'eci';
			$postfix 	= '/'.$ac.'/'.$st_code;
			
			
			$stateList = DB::table('m_state')
        				->select('m_state.ST_CODE','m_state.ST_NAME')
						->join('m_election_details','m_state.ST_CODE','m_election_details.ST_CODE')
						->where('m_election_details.CONST_TYPE','AC')
						->where('m_election_details.CURRENTELECTION','Y')
						->orderBy('m_state.ST_NAME','ASC')
						->groupBy('m_state.ST_CODE')
        				->get()->toArray();
			
			
			
		}
    	$election_detail = $session['election_detail'];
        $user_data = $d;
        		
		if(($st_code != null) && ($ac != null)){
		
		//echo $st_code.'-'.$ac; die;
		
		
    	$getIndexCardDataACWise = $this->getIndexCardDataACWise($st_code, $ac);
		
		$getIndexCardDataCandidatesVotesACWise = $this->getIndexCardDataCandidatesVotesACWise($st_code, $ac);
				
		$acinfo = DB::table('m_district AS dpm')
                    ->select('mac.AC_NO','dpm.DIST_NAME as DIST_NAME_EN','mac.AC_NAME','mac.AC_TYPE')
                  ->join('m_ac As mac', function($join){
					  $join->on('dpm.DIST_NO','mac.DIST_NO_HDQTR')
					      ->on('dpm.ST_CODE','mac.ST_CODE');
				  })
                  ->where('dpm.ST_CODE',$st_code)
                  ->where('mac.AC_NO',$ac)
                  ->first();
				  
		$acList = DB::table('m_ac')
        				->select(['m_ac.AC_NO','m_ac.AC_NAME'])
						->join('m_election_details', function($join){
							$join->on('m_ac.ST_CODE','m_election_details.ST_CODE')
								->on('m_ac.AC_NO','m_election_details.CONST_NO');
						}
						)
						->where('m_election_details.CONST_TYPE','AC')
						->where('m_election_details.CURRENTELECTION','Y')
        				->where('m_ac.ST_CODE',$request->st_code);
						
						if(Auth::user()->designation == 'DEO'){
							$acList->where('m_ac.DIST_NO_HDQTR',Auth::user()->dist_no);
						}
						
						$acList = $acList->orderBy('m_ac.AC_NO','ASC')
        				->get()->toArray();        
		}else{
		
		}
		
		$data = [];
		$data['heading_title']  = 'Finalize Indexcard Entry';
		$data['filter_buttons'] = [];
		$filter = [];
		$filter['ac_no']    = \Auth::user()->ac_no;
		$filter['st_code']  = \Auth::user()->st_code;
		//$filter['year']       = getElectionYear(); 
		$data['is_finalize']  = VoterModel::get_finalize_pc($filter);
		
		//echo '<pre>'; print_r($acinfo); die;
		
		
		$uncontested = DB::table('m_election_details')->select('uncontested')->where('st_code',$st_code)->where('CONST_NO',$ac)->first();
		
		$uncontested = @$uncontested->uncontested;

		if($request->path() == "$prefix/indexcardacpdf$postfix"){
			$pdf=PDF::loadView
			('IndexCardReports.IndexCardDataACWise.indexcardreportacpdf',compact('getIndexCardDataCandidatesVotesACWise','getIndexCardDataACWise','stateList','acList','st_code','ac','user_data','acinfo','uncontested'));
			return $pdf->download('IndexCardACReport.pdf');
			
		}else if($request->path() == "$prefix/indexcardacexcel$postfix"){
			
			
			return Excel::download(new IndexCardReport($getIndexCardDataCandidatesVotesACWise, $getIndexCardDataACWise,$st_code,$ac,$user_data,$acinfo), 'IndexCard Report.xlsx');
			
			
		}else{
				return view('IndexCardReports/IndexCardDataACWise/indexcardreportac',compact('getIndexCardDataCandidatesVotesACWise','getIndexCardDataACWise','acList','st_code','ac','user_data','acinfo','data','stateList','uncontested'));
		}
    }  




   public function getIndexCardDataACWise($st_code, $ac){
        
        $bt = 'counting_master_'.strtolower($st_code);
           
                  $fWhere = array(
                        'ec.st_code'   => $st_code,
                        'ec.ac_no'     => $ac,
                    );
            
            $electorData = DB::table('electors_cdac AS ec')
                          ->select(array(
								  'ovi.general_male_voters AS male_voter',
                            	  'ovi.general_female_voters AS female_voter',
                            	  'ovi.general_other_voters AS other_voter',
                            	  'ovi.nri_male_voters AS nri_male_voters',
                            	  'ovi.nri_female_voters AS nri_female_voters',
                            	  'ovi.nri_other_voters AS nri_other_voters',
                            	  'ovi.test_votes_49_ma AS test_votes_49_ma',
                                'ovi.votes_not_retreived_from_evm AS votes_not_retreived_from_evm',
                                'ovi.votes_counted_from_evm AS votes_counted_from_evm',
                                'ovi.votes_counted_from_vvpat AS votes_counted_from_vvpat',
                                'ovi.rejected_votes_due_2_other_reason AS rejected_votes_due_2_other_reason',
                                'ovi.service_postal_votes_under_section_8 AS service_postal_votes_under_section_8',

                                'ovi.service_postal_votes_gov AS service_postal_votes_gov',
                                'rm.rejected_votes AS postal_votes_rejected',
                                'ovi.proxy_votes AS proxy_votes',
                                'rm.tended_votes AS tendered_votes',

                                'ovi.total_polling_station_s_i_t_c AS total_polling_station_s_i_t_c',
                                'ovi.date_of_repoll AS date_of_repoll',
                                'ovi.no_poll_station_where_repoll AS no_poll_station_where_repoll',
                                'ovi.is_by_or_countermanded_election AS is_by_or_countermanded_election',
                                'ovi.reasons_for_by_or_countermanded_election AS reasons_for_by_or_countermanded_election',
                                'ovi.finalize_by_ceo AS finalize_by_ceo',
                                'ovi.finalize AS finalize_by_ro',
                                'ovi.finalize_by_eci AS finalize_by_eci',
                                'ovi.date_of_finalize_by_ro AS finalize_by_ro_date',
                                'ovi.date_of_finalize_by_ceo AS finalize_by_ceo_date',

                            DB::raw('SUM(ec.gen_electors_male) AS gen_m'),
                            DB::raw("SUM(ec.service_male_electors) AS ser_m"),
                            DB::raw("SUM(ec.nri_male_electors) AS nri_m"), 

                            DB::raw("SUM(ec.gen_electors_female) AS gen_f"),
                            DB::raw("SUM(ec.service_female_electors) AS ser_f"),
                            DB::raw("SUM(ec.nri_female_electors) AS nri_f"),

                            DB::raw("SUM(ec.gen_electors_other) AS gen_o"),
                            DB::raw("SUM(ec.nri_third_electors) AS nri_o"),
                            DB::raw("SUM(ec.service_third_electors) AS ser_o"),

                            DB::raw("SUM(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other) AS gen_t"),
                            DB::raw("SUM(ec.service_male_electors+ec.service_female_electors+ec.service_third_electors) AS ser_t"),
                            DB::raw("SUM(ec.nri_male_electors + ec.nri_female_electors+ ec.nri_third_electors) AS nri_t"),
                        ))
                        ->leftJoin('electors_cdac_other_information as ovi',function($query){
                           $query->on('ovi.st_code','ec.st_code')
                                   ->on('ovi.ac_no','ec.ac_no');                                  
						   })
						->leftJoin('round_master as rm',function($query){
							   $query->on('rm.st_code','ec.st_code')
									   ->on('rm.ac_no','ec.ac_no');                                  
						   })
                        ->where(array(
                            'ec.st_code' => $st_code,
                            'ec.ac_no'   => $ac,
                           // 'ec.year'    => getElectionYear()
                        ))
                        ->groupBy('ec.ac_no')                       
                        ->first();


                          DB::enableQueryLog();

         $indexCardDataACs = DB::table($bt.' AS A')
                           ->select(
                                DB::raw("SUM(A.postalballot_vote) AS totalpostal_votes"),
                                DB::raw("SUM(A.total_vote - A.postalballot_vote) AS totalevm_votes"),
                                DB::raw("SUM(A.total_vote) AS total_votes")
                                
                            )
                            ->where(array(
                                'A.ac_no'    => $ac                        
                            ))
                            ->groupBy('A.ac_no')
                            ->first();
							


        $indexCardDataACNota = DB::table($bt.' AS A')
                           ->select('A.postalballot_vote as postel_nota',
						   DB::raw("(A.total_vote - A.postalballot_vote) AS nota_evm"),
						   'A.total_vote as total_nota')
                           ->where(array(                        
                                'A.party_id'=>'1180',
								'A.ac_no'    => $ac  
                            ))
                            ->first();

	
	
		/* $indexCardDatas = DB::select("SELECT 
		SUM(CASE WHEN B.cand_gender = 'male' THEN 1 ELSE 0 END) AS male,
		SUM(CASE WHEN B.cand_gender = 'female' THEN 1 ELSE 0 END) AS female,
		SUM(CASE WHEN B.cand_gender = 'third' THEN 1 ELSE 0 END) AS third,
		SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS wdmale,
		SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS wdfemale,
		SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'third' THEN 1 ELSE 0 END) AS wdthird,
		SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS rejmale,
		SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS rejfemale,
		SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'third' THEN 1 ELSE 0 END) AS rejthird ,
		
		SUM(CASE WHEN A.application_status = '6' AND A.finalaccepted = '1' AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS acpmale,
		SUM(CASE WHEN A.application_status = '6' AND A.finalaccepted = '1' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS acpfemale,
		SUM(CASE WHEN A.application_status = '6' AND A.finalaccepted = '1' AND B.cand_gender = 'third' THEN 1 ELSE 0 END)  AS acpthird
		from
		(select candidate_id,max(application_status) as application_status,finalaccepted from candidate_nomination_detail where `st_code` = '$st_code' AND `ac_no` = '$ac' AND `application_status` != '11' and `candidate_id` NOT IN (4503) group by candidate_id) A INNER JOIN `candidate_personal_detail` AS `B` ON `A`.`candidate_id` = `B`.`candidate_id`"); */
		

		$indexCardDatas = App\models\Admin\CandidateModel::get_count_nominated($st_code,$ac);


		//echo '<pre>'; print_r($indexCardDatas); die;

            $indexCardDatasDf = DB::select("SELECT cp.ac_no,
            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as actotalvotes FROM `".$bt."` as cp1 
            where cp1.party_id != 1180 and cp1.ac_no = cp.ac_no and cp.ac_no =cp1.ac_no and C.cand_gender = 'male' GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) as fdmale,

            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as actotalvotes FROM `".$bt."` as cp1 
            where cp1.party_id != 1180 and cp1.ac_no = cp.ac_no and  C.cand_gender = 'female' GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) as fdfemale, 

            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as actotalvotes FROM `".$bt."` as cp1 
            where cp1.party_id != 1180 and cp1.ac_no = cp.ac_no and  C.cand_gender = 'third' GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) as fdthird,


            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as actotalvotes FROM `".$bt."` as cp1 
            where cp1.party_id != 1180 and cp1.ac_no = cp.ac_no GROUP BY cp1.`ac_no` ),5) < .16666 THEN 1 ELSE 0 END) as fd


            FROM `".$bt."` as cp
            join candidate_personal_detail as C on C.candidate_id = cp.candidate_id
            WHERE cp.candidate_id NOT IN (select candidate_id from winning_leading_candidate as w1 where w1.ac_no = cp.ac_no) 
            AND cp.party_id != 1180 and cp.ac_no = ".$ac);


			
			$candidateData = array();

            $candidateData = array_merge($candidateData,array(

                                'c_nom_m_t'=> $indexCardDatas['nom_male'],
                                'c_nom_f_t'=> $indexCardDatas['nom_female'],
                                'c_nom_o_t'=> $indexCardDatas['nom_third'],
                                'c_nom_all_t'=> $indexCardDatas['nom_total'],

                                'c_wd_m_t'=> $indexCardDatas['with_male'],
                                'c_wd_f_t'=> $indexCardDatas['with_female'],
                                'c_wd_o_t'=> $indexCardDatas['with_third'],

                                 'c_rej_m_t'=> $indexCardDatas['rej_male'],
                                'c_rej_f_t'=> $indexCardDatas['rej_female'],
                                'c_rej_o_t'=> $indexCardDatas['rej_third'],

                                 'c_acp_m_t'=> $indexCardDatas['cont_male'],
                                'c_acp_f_t'=> $indexCardDatas['cont_female'],
                                'c_acp_o_t'=> $indexCardDatas['cont_third'],

                                ));


			   $candidateData = array_merge($candidateData,array(

                                'c_fd_m_t'=> $indexCardDatasDf[0]->fdmale,
                                'c_fd_f_t'=> $indexCardDatasDf[0]->fdfemale,
                                'c_fd_o_t'=> $indexCardDatasDf[0]->fdthird,
                                'c_fd_t'=> $indexCardDatasDf[0]->fd,
                                

                                ));

 
						$candidateData = (object) $candidateData;

						$pollDateInfoacwise = DB::table('m_schedule as ms')
                                ->select('ms.DATE_POLL','ms.DATE_COUNT','ms.DT_ISS_NOM','ms.DT_PRESS_ANNC','wlc.result_declared_date','mres.DATE_POLL as DATE_REPOLL','mres.DT_ISS_NOM as RE_DT_ISS_NOM','mres.DT_PRESS_ANNC as RE_DT_PRESS_ANNC','mres.DATE_COUNT as RE_DATE_COUNT')
                                ->join('m_election_details as med','med.SCHEDULEID','ms.SCHEDULEID')
                                ->join('winning_leading_candidate as wlc', function($join){
                                    $join->on('wlc.st_code', 'med.ST_CODE')
                                            ->on('wlc.ac_no', 'med.CONST_NO');
                                })
								->leftJoin('m_reschedule as mres',[['mres.SCHEDULEID','ms.SCHEDULEID'],['med.ST_CODE','mres.st_code'],['med.CONST_NO','mres.ac_no']])
                                ->where(array(
                                        'med.ST_CODE' => $st_code,
                                        'med.CONST_NO'    => $ac,
                                        'med.CONST_TYPE'    => "AC",
                                        'med.CURRENTELECTION'   => 'Y'
                                 ))
                                ->first();
 

					if(@$electorData->total_polling_station_s_i_t_c > 0){
						$avg_elec_polling_stn = round(($electorData->gen_m +  $electorData->gen_f + $electorData->gen_o + $electorData->nri_m + $electorData->nri_f + $electorData->nri_o + $electorData->ser_m + $electorData->ser_f + $electorData->ser_o)/$electorData->total_polling_station_s_i_t_c);
					}else{
						$avg_elec_polling_stn = 0;
					}

 $data=array(
            
            'ac_no'                          => $ac,
			 
			"c_nom_m_t"                     =>$candidateData->c_nom_m_t,
			"c_nom_f_t"                     => $candidateData->c_nom_f_t,
			"c_nom_o_t"                     =>$candidateData->c_nom_o_t,
			"c_nom_a_t"                     =>$candidateData->c_nom_all_t,

			"c_nom_w_m"                     =>$candidateData->c_wd_m_t,
			"c_nom_w_f"                     =>$candidateData->c_wd_f_t,
			"c_nom_w_o"                     =>$candidateData->c_wd_o_t, 
			"c_nom_w_t"                     =>$candidateData->c_wd_m_t  + $candidateData->c_wd_f_t +$candidateData->c_wd_o_t,

			"c_nom_r_m"                     =>$candidateData->c_rej_m_t,
			"c_nom_r_f"                     =>$candidateData->c_rej_f_t,
			"c_nom_r_o"                     =>$candidateData->c_rej_o_t,
			"c_nom_r_a"                     =>$candidateData->c_rej_m_t + $candidateData->c_rej_f_t + $candidateData->c_rej_o_t,

			"c_nom_co_m"                     =>$candidateData->c_acp_m_t,
			"c_nom_co_f"                     =>$candidateData->c_acp_f_t,
			"c_nom_co_o"                     =>$candidateData->c_acp_o_t,
			'c_nom_co_t'                     =>$candidateData->c_acp_m_t + $candidateData->c_acp_f_t +$candidateData->c_acp_o_t,

			"c_nom_fd_m"                     =>$candidateData->c_fd_m_t, 
			"c_nom_fd_f"                     =>$candidateData->c_fd_f_t, 
			"c_nom_fd_o"                     =>$candidateData->c_fd_o_t, 
			"c_nom_fd_t"                     =>$candidateData->c_fd_t, 
			 		 
             'e_nri_m'                        => @$electorData->nri_m,
             'e_nri_f'                        => @$electorData->nri_f,
             'e_nri_o'                        => @$electorData->nri_o,
             'e_nri_t'                        => @$electorData->nri_m + @$electorData->nri_f + @$electorData->nri_o,
             'e_gen_m'                        => @$electorData->gen_m,
             'e_gen_f'                        => @$electorData->gen_f,
             'e_gen_o'                        => @$electorData->gen_o,
             'e_gen_t'                        => @$electorData->gen_m +  @$electorData->gen_f + @$electorData->gen_o,
             'e_ser_m'                        => @$electorData->ser_m,
             'e_ser_f'                        => @$electorData->ser_f,
             'e_ser_o'                        => @$electorData->ser_o,
             'e_ser_t'                        => @$electorData->ser_f + @$electorData->ser_m + @$electorData->ser_o,
             'e_all_t_m'                      => @$electorData->nri_m + @$electorData->gen_m + @$electorData->ser_m,
             'e_all_t_f'                      => @$electorData->nri_f + @$electorData->gen_f + @$electorData->ser_f,
             'e_all_t_o'                      => @$electorData->nri_o + @$electorData->gen_o + @$electorData->ser_o, 
             "e_all_t"                        => @$electorData->gen_m +  @$electorData->gen_f + @$electorData->gen_o + @$electorData->ser_f + @$electorData->ser_m + @$electorData->ser_o + @$electorData->nri_m + @$electorData->nri_f + @$electorData->nri_o, 
			 
			 
			"vt_gen_m"                     =>@$electorData->male_voter ? : 0,
			"vt_gen_f"                     =>@$electorData->female_voter ? : 0,
			"vt_gen_o"                     =>@$electorData->other_voter ? : 0,
			"vt_gen_t"                     =>@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter,

			"vt_nri_m"                     =>@$electorData->nri_male_voters ? : 0,
			"vt_nri_f"                     =>@$electorData->nri_female_voters ? : 0,
			"vt_nri_o"                     =>@$electorData->nri_other_voters ? : 0,
			"vt_nri_t"                     =>@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters,

			"vt_m_t"                     =>@$electorData->male_voter+@$electorData->nri_male_voters,
			"vt_f_t"                     =>@$electorData->female_voter+@$electorData->nri_female_voters,
			"vt_o_t"                     =>@$electorData->other_voter+@$electorData->nri_other_voters,
			"vt_all_t"                     =>@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters,
			 		 
			"t_votes_evm"               		=>@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters + @$electorData->test_votes_49_ma,
			"mock_poll_evm"       				=>   @$electorData->test_votes_49_ma ? :0,
			"not_retrieved_vote_evm"                => @$electorData->votes_not_retreived_from_evm ? :0,
			"votes_counted_from_evm"                => @$electorData->votes_counted_from_evm ? :0,
			"votes_counted_from_vvpat"                => @$electorData->votes_counted_from_vvpat ? :0,
			"r_votes_evm"                		=> @$electorData->rejected_votes_due_2_other_reason ? :0,
			"nota_vote_evm"       			=>   @$indexCardDataACNota->nota_evm,
			"all_reject_on_evm"       			=> @$electorData->test_votes_49_ma + @$electorData->votes_not_retreived_from_evm + @$electorData->rejected_votes_due_2_other_reason + @$indexCardDataACNota->nota_evm,
			"v_votes_evm_all"                     	=> (@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters) - ( @$electorData->votes_not_retreived_from_evm + @$electorData->rejected_votes_due_2_other_reason + @$indexCardDataACNota->nota_evm),
			
			"postal_vote_ser_u"                     => @$electorData->service_postal_votes_under_section_8 ? :0,
			"postal_vote_ser_o"                     => @$electorData->service_postal_votes_gov ? :0,			
			"postal_vote_rejected"                  => @$electorData->postal_votes_rejected ? :0,
			"postal_vote_nota"       				=>   @$indexCardDataACNota->postel_nota ? :0,
			"postal_vote_r_nota"  					=>    @$indexCardDataACNota->postel_nota + @$electorData->postal_votes_rejected,
            "postal_valid_votes"                    => ((@$electorData->service_postal_votes_under_section_8 + @$electorData->service_postal_votes_gov)- (@$indexCardDataACNota->postel_nota + @@$electorData->postal_votes_rejected)),
			
			
			"total_votes_polled"                    =>(@$electorData->service_postal_votes_under_section_8 + @$electorData->service_postal_votes_gov) + (@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters + @$electorData->test_votes_49_ma), 
			"total_not_count_votes"       =>  @$electorData->test_votes_49_ma + @$electorData->votes_not_retreived_from_evm + @$electorData->rejected_votes_due_2_other_reason + @$indexCardDataACNota->nota_evm +  @$indexCardDataACNota->postel_nota + @$electorData->postal_votes_rejected,
			"total_valid_votes"                     =>((@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter+@$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters) - ( @$electorData->votes_not_retreived_from_evm + @$electorData->rejected_votes_due_2_other_reason + @$indexCardDataACNota->nota_evm) + (@$electorData->service_postal_votes_under_section_8 + @$electorData->service_postal_votes_gov)- (@$indexCardDataACNota->postel_nota + @$electorData->postal_votes_rejected)),
			"total_votes_nota"       =>    @$indexCardDataACNota->total_nota,
			
			'proxy_votes'                   => @$electorData->proxy_votes ? : 0,
            'tendered_votes'                 => @$electorData->tendered_votes ? : 0,          
           "total_no_polling_station"         => @$electorData->total_polling_station_s_i_t_c ? : 0,		   
            "avg_elec_polling_stn"            => $avg_elec_polling_stn,
            'dt_poll'                         => @$pollDateInfoacwise->DATE_REPOLL ? @$pollDateInfoacwise->DATE_REPOLL : @$pollDateInfoacwise->DATE_POLL,
            'date_of_repoll'                  => @$electorData->date_of_repoll,
            'dt_poll_reasion'                  => @$electorData->no_poll_station_where_repoll,
            //"dt_counting"                     => @$pollDateInfoacwise->DATE_COUNT,
            "dt_counting"                     => @$pollDateInfoacwise->RE_DATE_COUNT ? @$pollDateInfoacwise->RE_DATE_COUNT : @$pollDateInfoacwise->DATE_COUNT,
            "DT_PRESS_ANNC"                     => @$pollDateInfoacwise->RE_DT_PRESS_ANNC ? @$pollDateInfoacwise->RE_DT_PRESS_ANNC : @$pollDateInfoacwise->DT_PRESS_ANNC,
            "DT_ISS_NOM"                     => @$pollDateInfoacwise->RE_DT_ISS_NOM ? @$pollDateInfoacwise->RE_DT_ISS_NOM : @$pollDateInfoacwise->DT_ISS_NOM,
            "dt_declare"                     =>  @$pollDateInfoacwise->result_declared_date,
            "flag_bye_counter"                 => @$electorData->is_by_or_countermanded_election ? : 0,
            "flag_bye_counter_reason"         => @$electorData->reasons_for_by_or_countermanded_election ? : '', 
			"finalize_by_ceo" 		   =>   @$electorData->finalize_by_ceo ? : 0,
			"finalize_by_ro" 		   =>   @$electorData->finalize_by_ro ? : 0,
			"finalize_by_eci" 		   =>   @$electorData->finalize_by_eci ? : 0,
			"finalize_by_ro_date" 	   =>   @$electorData->finalize_by_ro_date,
			"finalize_by_ceo_date" 	   =>   @$electorData->finalize_by_ceo_date			
 );


     
		//echo '<pre>'; print_r($data); die;        
		return $data;
 
    }
      
    public function getIndexCardDataCandidatesVotesACWise($st_code, $ac){
 
	//echo $st_code.' '.$ac; die;

    	$gTable = "counting_master_".strtolower($st_code)." AS cm";
		
		//echo $gTable; die;
		
    	$count = 0;
	    		$bWhere = array(
	    			'A.st_code' 			=> $st_code,
	    			'A.ac_no' 				=> $ac,
	    			'A.application_status' 	=> 6,
	    			'A.finalaccepted'       => 1
	    		);
	    		$bSelect = array(
	    			'A.candidate_id',
	    			'A.party_id',
	    			'A.symbol_id',
	    			'A.election_id',	    			
	    			'A.ac_no',
	    			'A.st_code',
	    			'B.cand_name',
	    			'B.cand_gender',
	    			'B.cand_age',
	    			'B.cand_category',
	    			'C.PARTYABBRE',
	    			'C.PARTYNAME',
	    			'C.PARTYTYPE',
	    			'D.symbol_no',
	    			'D.SYMBOL_DES',
	    			'cm.postalballot_vote',
	    			'cm.total_vote'
	    		);
	    		DB::enableQueryLog();

	    		$responseFronCountingPC = DB::table('candidate_nomination_detail AS A')
	    									->select($bSelect)
	    									->join('candidate_personal_detail AS B','A.candidate_id','B.candidate_id')
	    									->join('m_party AS C','A.party_id','C.ccode')
	    									->join('m_symbol AS D','A.symbol_id','D.symbol_no')
	    									->join($gTable,[['cm.candidate_id', 'A.candidate_id'],['cm.ac_no', 'A.ac_no']])
	    									->where($bWhere)
											->where('A.party_id', '!=' ,'1180')
											->where('A.symbol_id', '!=' , '200')
	    									->orderBy('cm.total_vote','DESC')
											->orderBy('A.new_srno','ASC')
	    									->get()->toArray();
	    		//$queue = DB::getQueryLog();

				return $responseFronCountingPC;
    		
    }

   
    
}