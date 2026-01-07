<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;
use App\commonModel;
use App\models\{States, Districts, AC};
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\adminmodel\CandidateApiModel;

class CandidateController extends Controller
{
    public function __construct() {
        $this->commonModel = new commonModel();
        $this->candidateModel = new CandidateApiModel();
    }

    public $successStatus = 200;
    public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;

    public function getElectionTypeDetails() {
        $electiontype = DB::table('election_master')->get();

        return Response::json($electiontype);
    }

    public function getStateByPhase(Request $request) {

        try{
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
            ]);
    
            if($validator->fails()){
                return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
            } 
    
            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            
            if(!empty($electiontypeid)){
                if(!empty($scheduleid)){
                $phase_details = DB::table('m_election_details')->groupby('ST_CODE')
                ->where('ScheduleID',$scheduleid)->where('ELECTION_TYPEID',$electiontypeid)->where('CONST_TYPE','=','AC')->get();
                }else{
                    $phase_details = DB::table('m_election_details')->groupby('ST_CODE')
                    ->where('ELECTION_TYPEID',$electiontypeid)->where('CONST_TYPE','=','AC')->get();
                }

                if(count($phase_details)>0){
                    $statelist = array();
                    foreach($phase_details as $state){
                        $statelist[] = array("statename"=>trim($this->commonModel->getstatebystatecode($state->ST_CODE)->ST_NAME),"statecode"=>$state->ST_CODE);
                    }
                    $success['success'] = true;
                    $success['statelist'] =$statelist;
                     
                }else{ 
                    $success['success'] = false;
                    $success['statelist'] = array();
                    return response()->json($success, $this->successStatus);
                }
                return response()->json($success, $this->successStatus);
            }
            } catch (Exception $ex) {
                return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            }
        }

   public function getAcListing(Request $request) {

    try{
        $validator = Validator::make($request->all(), [
            'electiontype' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
        } 

        $userInputs = $request->all();
        $scheduleid = trim($userInputs['electionphase']);
        $electiontypeid = trim($userInputs['electiontype']);
        $statecode = trim($userInputs['statecode']);
        // $ac_no = trim($userInputs['acno']);
        $ac_no = "";

        $pc_list = DB::table('m_election_details')->where('CONST_TYPE','=','AC')->where('ELECTION_TYPEID',$electiontypeid);

        if(!empty($statecode)){
            if(!empty($ac_no)){
                $pc_list->where('ST_CODE',$statecode)->where('CONST_NO',$ac_no);
            }else{
                $pc_list->where('ST_CODE',$statecode);
            }
        }else{
            $pc_list;
        }

        if(!empty($scheduleid)){
            $pc_list->where('ScheduleID',$scheduleid);
        }
            
        $pc_list_filtered = $pc_list->get();

        if(count($pc_list_filtered)>0){
            $aclisting = array();
            foreach($pc_list_filtered as $aclist){
                $aclisting[] = array("acname"=>trim($this->commonModel->getacbyacno($aclist->ST_CODE,$aclist->CONST_NO)->AC_NAME),"accode"=>($aclist->CONST_NO),
                "statename"=>trim($this->commonModel->getstatebystatecode($aclist->ST_CODE)->ST_NAME),"statecode"=>$aclist->ST_CODE);
            }
            $success['success'] = true;
            $success['statelist'] =$aclisting;
             
        }else{ 
            $success['success'] = false;
            $success['statelist'] = array();
            return response()->json($success, $this->successStatus);
        }
        return response()->json($success, $this->successStatus);

        } catch (Exception $ex) {
            return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
        }
    }

    public function getStatus(){

    $status = DB::table('m_status')->select('id','status')->get();

    return Response::json($status);
    }

    public function getCandidateList(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'page' => 'required'
            ]);
    
            if($validator->fails()){
                return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
            } 
    
            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            $statecode = trim($userInputs['statecode']);
            $ac_no = trim($userInputs['acno']);
            $status = trim($userInputs['status']);
            $page = trim($userInputs['page']);
			if($request->has('search')){
            $search_key = trim($userInputs['search']);
            }
            
			$totst = "";
            $total=$this->candidateModel->getnominationcnt($statecode,$ac_no,$totst,$search_key,$electiontypeid,$scheduleid);
            $totw=$this->candidateModel->getnominationcnt($statecode,$ac_no,'5',$search_key,$electiontypeid,$scheduleid); 
            $totr=$this->candidateModel->getnominationcnt($statecode,$ac_no,'4',$search_key,$electiontypeid,$scheduleid); 
            $totacc=$this->candidateModel->getnominationcnt($statecode,$ac_no,'6',$search_key,$electiontypeid,$scheduleid); 
            $totv=$this->candidateModel->getnominationcnt($statecode,$ac_no,'2',$search_key,$electiontypeid,$scheduleid); 
            $totrec=$this->candidateModel->getnominationcnt($statecode,$ac_no,'3',$search_key,$electiontypeid,$scheduleid);
            $tota=$this->candidateModel->getnominationcnt($statecode,$ac_no,'1',$search_key,$electiontypeid,$scheduleid);
            $totcontcand =$this->candidateModel->getnominationcntcontest($statecode,$ac_no,'6',$search_key,$electiontypeid,$scheduleid);
            // $countlist = array();
			

            $countlist = array("total"=>$total,"totalwithdraw"=>$totw,"totalreject"=>$totr,"totalaccepted"=>$totacc,
            "totalverify"=>$totv,"receiptgenerated"=>$totrec,"totalapplied"=>$tota,"contest_candidate"=>$totcontcand);

            if($status == '999'){
                $status = 6;
                $is_status = true;
                $cand_listing =$this->candidateModel->getnominationcontest($statecode,$ac_no,$status,$page,$search_key,$electiontypeid,$scheduleid);
            }else{
                $cand_listing =$this->candidateModel->getnomination($statecode,$ac_no,$status,$page,$search_key,$electiontypeid,$scheduleid);
                $is_status = false;
            }
           // return $cand_listing;
            if(count($cand_listing)>0){
                $candlisting = array();
                foreach($cand_listing as $candlist){
					
                    if($candlist->application_status==6 && $candlist->finalaccepted ==1 && $candlist->symbol_id !=200 && $candlist->finalize==1 && $candlist->party_id != 1180){
                        $contesting = true;
                     }else{
                        $contesting = false; 
                     }

                    if(!empty($candlist->cand_image)) {
                        $image_link = $candlist->cand_image;
                        $cand_image = url($image_link);
                    }else{ 
                        $cand_image = "";
                    }
                    if($is_status == true && !empty($ac_no) && $contesting == true){  
                        $candlisting[] = array("cont_name"=>trim($this->commonModel->getacbyacno($candlist->st_code,$candlist->ac_no)->AC_NAME),"statename"=>trim($this->commonModel->getstatebystatecode($candlist->st_code)->ST_NAME),"cand_id"=>$candlist->candidate_id,"nom_id"=>$candlist->nom_id,"cand_name"=>$candlist->new_srno.'. '.$candlist->cand_name,"party"=>trim(($this->commonModel->getparty($candlist->party_id))->PARTYNAME),
                    "status"=>trim(($this->commonModel->getnameBystatusid($candlist->application_status))),"nom_submit_date"=>$candlist->date_of_submit,"cand_image"=>$cand_image,"is_contesting"=>$contesting);
                    }else{
                        $candlisting[] = array("cont_name"=>trim($this->commonModel->getacbyacno($candlist->st_code,$candlist->ac_no)->AC_NAME),"statename"=>trim($this->commonModel->getstatebystatecode($candlist->st_code)->ST_NAME),"cand_id"=>$candlist->candidate_id,"nom_id"=>$candlist->nom_id,"cand_name"=>$candlist->cand_name,"party"=>trim(($this->commonModel->getparty($candlist->party_id))->PARTYNAME),
                    "status"=>trim(($this->commonModel->getnameBystatusid($candlist->application_status))),"nom_submit_date"=>$candlist->date_of_submit,"cand_image"=>$cand_image,"is_contesting"=>$contesting);
                    }
                    
                }
                $success['success'] = true;
                $success['countinglisting'] =$candlisting;
                $success['countlist'] =$countlist;
				$success['pdf_url'] = "";
            }else{ 
                $success['success'] = false;
                $success['countinglisting'] = array();
                $success['countlist'] =$countlist;
				$success['pdf_url'] = "";
                return response()->json($success, $this->successStatus);
            }
            return response()->json($success, $this->successStatus);
    
            } catch (Exception $ex) {
                return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            }
    }

    public function getCandidateDetails(Request $request) {

        try{
            $validator = Validator::make($request->all(), [
                'candidateid' => 'required',
                'nomid' => 'required',
            ]);
    
            if($validator->fails()){
                return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
            } 
    
            $userInputs = $request->all();
            $candidate_id = trim($userInputs['candidateid']);
            $nom_id = trim($userInputs['nomid']);

            $canddetails = DB::table('candidate_nomination_detail')
                    ->Join('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->where('candidate_nomination_detail.candidate_id',$candidate_id)->where('nom_id',$nom_id)->get();
            
            $cand_affi = DB::table('candidate_affidavit_detail')->where('candidate_id',$candidate_id)->where('nom_id',$nom_id)->get();

            $cand_count_affi = DB::table('candidate_counteraffidavit_detail')->where('candidate_id',$candidate_id)->where('nom_id',$nom_id)->get();

            if(count($canddetails)>0){

                if(count($cand_affi)>0){
                    $affid = array();
                    foreach($cand_affi as $affi){

                        if(!empty($affi)){
                            $link = url($affi->affidavit_path);
                            $name = $affi->affidavit_name;
                        }else{
                            $link = "";
                            $name = "";
                        }

                        $affid[] = array("affidavit_link"=>$link,"affidavit_name"=>$name);
                    }
                }else{
					$affid = array();
				}

                if(count($cand_count_affi)>0){
                    foreach($cand_count_affi as $affi_count){

                        if(!empty($affi_count)){
                            $link = url($affi_count->affidavit_path);
                            $name = $affi_count->affidavit_name;
                        }else{
                            $link = "";
                            $name = "";
                        }

                        $affid_count[] = array("affidavit_link"=>$link,"affidavit_name"=>$name);
                    }
                }else{
					$affid_count = array();
				}

                $details = array();
                foreach($canddetails as $cand){
					
					if($cand->application_status==6 && $cand->finalaccepted ==1 && $cand->symbol_id !=200 && $cand->finalize==1 && $cand->party_id != 1180){
                        $contesting = true;
                     }else{
                        $contesting = false;
                     }
					
					if(!empty($cand->cand_image)) {
                        $image_link = $cand->cand_image;
                        $cand_image = url($image_link);
                    }else{ 
                        $cand_image = "";
                    }
					
                    $details[] = array("cont_name"=>trim($this->commonModel->getacbyacno($cand->st_code,$cand->ac_no)->AC_NAME),"statename"=>trim($this->commonModel->getstatebystatecode($cand->st_code)->ST_NAME),"cont_name"=>trim($this->commonModel->getacbyacno($cand->st_code,$cand->ac_no)->AC_NAME),"statename"=>trim($this->commonModel->getstatebystatecode($cand->st_code)->ST_NAME),"cand_id"=>$cand->candidate_id,"nom_id"=>$cand->nom_id,"cand_name"=>$cand->cand_name,"cand_hname"=>$cand->cand_hname,"party"=>trim(($this->commonModel->getparty($cand->party_id))->PARTYNAME),
					"party_h"=>trim(($this->commonModel->getparty($cand->party_id))->PARTYHNAME),
                    "fathername"=>$cand->candidate_father_name,"fathername_h"=>$cand->cand_fhname,"age"=>$cand->cand_age,"gender"=>$cand->cand_gender,"address1"=>$cand->candidate_residence_address,"address2"=>$cand->candidate_residence_addressh,"cand_image"=>$cand_image,"nom_submit_date"=>$cand->date_of_submit,"is_contesting"=>$contesting);
                }

                $success['success'] = true;
                $success['details'] =$details;
                $success['counter_affidavit'] = $affid_count;
                $success['affidavit'] = $affid;
                 
            }else{ 
                $success['success'] = false;
                $success['details'] = array();
                $success['counter_affidavit'] = array();
                $success['affidavit'] = array();
                return response()->json($success, $this->successStatus);
            }
            return response()->json($success, $this->successStatus);
    
            } catch (Exception $ex) {
                return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            }
        }

	 public function getelectionschedul(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'st_code' => 'required',
                'ac_code' => 'nullable',
                'pc_code' => 'nullable'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
            }

            $userInputs = $request->all();
            $st_code = trim($userInputs['st_code']);
            $ac_code = trim($userInputs['ac_code']);
            $pc_code = trim($userInputs['pc_code']);

            $pcname = "";
            $acname = "";

            if(!empty($ac_code)){

            $phase = $this->candidateModel->getphasebystateac($st_code, $ac_code);
            $acname = trim($this->commonModel->getacbyacno($st_code, $ac_code)->AC_NAME);   
                
            if(!empty($phase)){
                $pc_code = $phase->PC_NO;
				$pcname = trim($this->commonModel->getpcbypcno($st_code, $pc_code)->PC_NAME);
                $getphase = DB::table('m_election_details')->where('ST_CODE', $st_code)->where('CONST_NO', $pc_code)->where('CONST_TYPE', 'PC')->first();
                $getschedual = $this->commonModel->getschedulebyid($getphase->ScheduleID);
            }}

            if(!empty($pc_code)){                
                $pcname = trim($this->commonModel->getpcbypcno($st_code, $pc_code)->PC_NAME);  
                $getphase = DB::table('m_election_details')->where('ST_CODE', $st_code)->where('CONST_NO', $pc_code)->where('CONST_TYPE', 'PC')->first();
                $getschedual = $this->commonModel->getschedulebyid($getphase->ScheduleID);
            }

            if (!empty($getschedual)) {
                $schedule = array(
                    "start_date_nomination"     => $getschedual->DT_ISS_NOM,
                    "last_date_noimnation"      => $getschedual->LDT_IS_NOM,
                    "scrootny_date_nomination"  => $getschedual->DT_SCR_NOM,
                    "withdrawl_date_nomination" => $getschedual->LDT_WD_CAN,
                    "actual_poll_date"          => $getschedual->DATE_POLL,
                    "actual_counting_date"      => $getschedual->DATE_COUNT,
                    "election_completition_date"=> $getschedual->DTB_EL_COM,
                    "press_announcement_date"   => $getschedual->DT_PRESS_ANNC,
                    "insertation_date"          => $getschedual->INSERTION_DATE,
                    "phase_no"                  => $getschedual->SCHEDULEID,
                    "statename"                 => trim($this->commonModel->getstatebystatecode($st_code)->ST_NAME),
                    "pcname"                    => $pcname,
					"pc_code"					=> (string)$pc_code,
                    "acname"                    => $acname,
                );

                $success['success']  = true;
                $success['schedual'] = $schedule;
                return response()->json($success, $this->successStatus);

            } else {
                $success['success']  = false;
                $success['schedual'] = (object)array();
                return response()->json($success, $this->successStatus);
            }
        } catch (Exception $ex) {
            return response()->json(['success' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
        }
    }
}
