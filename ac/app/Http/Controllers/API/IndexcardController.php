<?php
namespace App\Http\Controllers\API;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;
use App\commonModel;
use Session;
use App\models\{States, Districts, AC};
use Mail;
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Input;
use Redirect;
use Carbon\Carbon;
use App\Helpers\SendNotification;
use Notification;
use Illuminate\Notifications\Notifiable;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Classes\xssClean;
use App\models\Admin\IndexCardFinalize;
use App\models\Admin\VoterModel;
use App\models\indexcard\IndexCardReport;
use \PDF;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
//use App\Http\Traits\CommonTraits;

class IndexcardController extends Controller
{
    public function __construct() {
        $this->xssClean = new xssClean;
        $this->commonModel = new commonModel();
        $this->ResponseMethod = new ResponseController;
        $this->bad_response = $this->ResponseMethod::HTTP_BAD_REQUEST;
        $this->ok_response = $this->ResponseMethod::HTTP_ACCEPTED; 
        $this->okStatus = "success";
        $this->errStatus = "error";
    }
	
	    //USING TRAIT FOR COMMON FUNCTIONS
   //use CommonTraits;

    public $successStatus = 200;
    public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;
    public $bad_response;
    public $ok_response;
    public $okStatus;
    public $errStatus;


 
public function listIndexcardFinalized(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                  
                    'election_id'   => 'required',

                  ],[
                    'election_id.required'   => 'Please enter election_id.', 
                  ]);

          if ($validator->fails()) { 

			  return response()->json($validator->errors(), $this->successStatus);
              //return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors(), $this->bad_response);
            }
         
			$st_code = $dist_no = $ac_no = '';
			
            $userInputs = $request->all();
			if(!empty($userInputs['st_code'])){
				$st_code = trim($userInputs['st_code']);
			}
			if(!empty($userInputs['dist_no'])){
				$dist_no = trim($userInputs['dist_no']);
			}
			if(!empty($userInputs['ac_no'])){
				$ac_no = trim($userInputs['ac_no']);
			}
			
			$filter_election = [
			'state'         => $st_code,
			'dist_no'       => $dist_no,
			'ac_no'         => $ac_no,
		  ];
		  
		 // dd($filter_election);
		  
			$data = [];
            $data['count'] = IndexCardFinalize::get_states($filter_election);
			
			
			//if($dist_no){
				$data['details'] = $this->get_reports($filter_election);
			//}
			
				if(isset($data)){
						
						$success['success'] =  true;
						$success['message'] = 'Data Get Successfully';
						$success['result'] = $data;
						return response()->json($success, $this->successStatus);

						//return $this->ResponseMethod->get_http_response($this->successStatus, $success, $this->ok_response);
									
            }else{
                $error['success'] =  false;
                $error['message'] = 'Records Not Found!!';
                return response()->json($error, $this->successStatus);
                //return $this->ResponseMethod->get_http_response($this->successStatus, $error, $this->ok_response);
            }
        } catch (Exception $ex) {
            return response()->json(encrypt(['success' => false,'error'=>'Internal Server Error']), $this->intservererrorStatus);
        }
}


public function get_reports($data = array()){
         
         $sql_raw = "p.AC_NO AS ac_no,
					p.AC_NAME AS ac_name,
					md.DIST_NO AS dist_no,
                    md.DIST_NAME AS dist_name,
					s.ST_NAME AS st_name,
                    s.ST_CODE AS st_code,                   
					IF(w.finalize_by_ro='1','true','false') AS finalize,
					IF(w.finalize_by_ceo='1','true','false') AS FinalizeCeo,
					IF(cf.finalized_ac='1','true','false') AS NominationFinalize,
					IF(cuf.status='1','true','false') AS CountingFinalize,
					w.date_of_finalize_by_ro,
					w.date_of_finalize_by_ceo";

        $sql = DB::table('m_ac as p')
		->join('candidate_finalized_ac as cf',[
              ['p.AC_NO', '=','cf.const_no'],
              ['p.ST_CODE', '=','cf.st_code'],
        ])
		->join('winning_leading_candidate as cuf',[
              ['p.AC_NO', '=','cuf.ac_no'],
              ['p.ST_CODE', '=','cuf.st_code'],
        ])
        ->leftjoin('electors_cdac_other_information as w',[
              ['p.AC_NO', '=','w.ac_no'],
              ['p.ST_CODE', '=','w.st_code'],
        ])
        ->leftjoin('m_election_details as med',[
              ['p.ST_CODE', '=','med.ST_CODE'],
              ['p.AC_NO', '=','med.CONST_NO'],
            
        ])
		->join('m_district as md',[
              ['p.ST_CODE', '=','md.ST_CODE'],
              ['p.DIST_NO_HDQTR', '=','md.DIST_NO'],
        ])
        ->join('m_state as s',[
              ['p.ST_CODE', '=','s.ST_CODE']
        ]);

        $sql->selectRaw($sql_raw);

        if(!empty($data['state'])){
          $sql->where("s.ST_CODE", $data['state']);
        }

        if(!empty($data['ac_no'])){
          $sql->where("p.AC_NO", $data['ac_no']);
        }
		
		if(!empty($data['dist_no'])){
          $sql->where("p.DIST_NO_HDQTR", $data['dist_no']);
        }


       $sql->where("med.CONST_TYPE", "AC");
       $sql->where("med.election_status", '!=','0');
     
       

        //$sql->whereRaw("p.PC_NO != 8 AND s.ST_CODE != 'S22'");

        $sql->orderByRaw("p.ST_CODE, p.AC_NO ASC");
        $sql->groupBy(DB::raw('p.AC_NO'));

        $query = $sql->get()->toArray();
     
        return $query;

    }
    

public function indexcard(Request $request){
	 
		try{
	  $validator = Validator::make($request->all(), [
                  
                    'election_id'   => 'required',
                    'st_code'   => 'required',
                    'ac_no'   => 'required',

                  ],[
                    'election_id.required'   => 'Please enter election_id.', 
                    'st_code.required'   => 'Please enter st_code.', 
                    'ac_no.required'   => 'Please enter ac_no.', 
                  ]);

          if ($validator->fails()) { 
			  return response()->json($validator->errors(), $this->successStatus);
              //return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors(), $this->bad_response);
            }
         

			$st_code = $ac_no = $ac = '';
			
            $userInputs = $request->all();
			if(!empty($userInputs['st_code'])){
				$st_code = trim($userInputs['st_code']);
			}

			if(!empty($userInputs['ac_no'])){
				$ac_no = trim($userInputs['ac_no']);
				$ac = trim($userInputs['ac_no']);
			}



	$stateList = array();
	
	$getIndexCardDataACWise = 	$getIndexCardDataCandidatesVotesACWise = $acinfo = $acList = array();
	
	
        
		  $ele_details = array();
		
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
				  
		 
			$pdf=PDF::loadView
			('IndexCardReports.IndexCardDataACWise.indexcardreportacpdf',compact('getIndexCardDataCandidatesVotesACWise','getIndexCardDataACWise','st_code','ac','acinfo'));
			

			$file_name = 'IndexCardReport.pdf';
                 
				  $year         = date('Y');
				   if (!file_exists('uploads1/indexcard/'.'/'.$year.'/'.$st_code.'/'.$ac)) {
						mkdir('uploads1/indexcard/'.'/'.$year.'/'.$st_code.'/'.$ac, 0777, true);
				   }
				  
                  
                  $ip = get_client_ip();
				  
				  $destination_path = 'uploads1/indexcard/'.'/'.$year.'/'.$st_code.'/'.$ac;


					$path = public_path($destination_path.'/'.$file_name);

                  $pdf->save($path);
				  
				  $path_url = url($destination_path.'/'.$file_name);
				  
				$success['success'] =  true;
				$success['message'] = 'Data Get Successfully';
				$success['result'] = $path_url;
				return response()->json($success, $this->successStatus);

				//return $this->ResponseMethod->get_http_response($this->successStatus, $success, $this->ok_response);  

		}else{
                $error['success'] =  false;
                $error['message'] = 'Records Not Found!!';
                return response()->json($error, $this->successStatus);
                //return $this->ResponseMethod->get_http_response($this->successStatus, $error, $this->ok_response);
            }
        } catch (Exception $ex) {
            return response()->json(encrypt(['success' => false,'error'=>'Internal Server Error']), $this->intservererrorStatus);
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


		$indexCardDatas = \App\models\Admin\CandidateModel::get_count_nominated($st_code,$ac);


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
                                ->select('ms.DATE_POLL','ms.DATE_COUNT','ms.DT_ISS_NOM','ms.DT_PRESS_ANNC','wlc.result_declared_date','mres.DATE_POLL as DATE_REPOLL','mres.DT_ISS_NOM as RE_DT_ISS_NOM','mres.DT_PRESS_ANNC as RE_DT_PRESS_ANNC')
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
            "dt_counting"                     => @$pollDateInfoacwise->DATE_COUNT,
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
	    									->join($gTable,'cm.candidate_id', 'A.candidate_id')
	    									->where($bWhere)
											->where('A.party_id', '!=' ,'1180')
											->where('A.symbol_id', '!=' , '200')
	    									->orderBy('cm.total_vote','DESC')
	    									->get()->toArray();
	    		//$queue = DB::getQueryLog();

				return $responseFronCountingPC;
    		
    }



}