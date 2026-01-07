<?php   
namespace App\Http\Controllers\Admin\mlc;
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
use App\models\Common\{StateModel, FileModel, AcModel, DistrictModel, PartyModel, SymbolModel, ElectionModel};
use MPDF;
use App\commonModel;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use Illuminate\Support\Facades\Crypt;
use App\models\Admin\ApplicantsModel;
use App\models\Nomination\NominationModel;
use App\models\Admin\Nomination\NominationApplicationModel;
use App\models\Admin\Nomination\NominationProposerModel;
use App\models\Admin\Nomination\NominationPoliceCaseModel;
//use App\Http\Requests\Admin\Nomination\{NominationPart12Request, NominationPart3Request, NominationPart3aRequest};

class MlcroController extends Controller
{

    public $upload_folder   = '';
    public $base            = 'roac';
    public $folder  = 'mlc';
    public $action    = 'roac/';
    public $view_path = "admin.mlc";
    public function __construct()
    {   
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('mlc');
        $this->commonModel = new commonModel();
        $this->Applicants= new ApplicantsModel();
        $this->xssClean = new xssClean;
        if(!Auth::check()){ 
            return redirect('/officer-login');
        }
         

    }
    public function index(Request $request) {      
        $data  = [];
        // $data['action']               = url('mlc/ro/nomination/apply-nomination-step-1/post');
        // $data['nomination_page']      = url('mlc/ro/nomination/apply-nomination-step-2');
        // $data['href_new_nomination']  = url('mlc/ro/nomination');
        $data['encrypt_id']           = '';
        
        $data['nomination_id'] = 0;

        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);  
        
        $results=NominationApplicationModel::where('st_code',$user->st_code) 
        ->where('application_type','4')->get()->toarray(); 

        $data['user_data']=$d;
        $data['results']=$results;  
        $data['heading_title']="List of All online MLC Nomination"; 
        
        //dd($data);
        return view($this->view_path.'.list-all-applicant', $data);            


}  // end index function

 

public function candidateinformation(Request $request)
{
    $data  = [];
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);  
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
    if($ele_details=='') 
    {
        \Session::flash('error_mes', 'Election has not assigned');
        return Redirect::to('/logout');
    }
    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
    if($check_finalize->finalized_ac=='1') 
    {
        \Session::flash('error_mes', 'Ac is Finalized');
        return Redirect::to('/roac/dashboard');
    }
    $seched=getschedulebyid($ele_details->ScheduleID);
    if($seched['DATE_POLL']<date("Y-m-d")) {
        \Session::flash('error_mes', 'Poll Date Completed');
        return Redirect::to('/roac/listnomination');  

    }

    $nomination_no = decrypt_string($request->nom_id);

    $user_nomination =NominationApplicationModel::where('nomination_no', $nomination_no)
    ->where('st_code', $ele_details->ST_CODE)
    ->where('ac_no', $ele_details->CONST_NO)->first();


//dd($user_nomination);  
    if(!$user_nomination){
        \Session::flash('error_mes', 'invalid  Nomination No.');
        return Redirect::to('/roac/qrscan');  
    }

// if($user_nomination['finalize'] == 0){
//     \Session::flash('error_mes', 'Your nomination is incomplete. Please visit nearest NFD to complete your nomination.');
//     return Redirect::to('/roac/qrscan');  
// }

    $user_nomination = $user_nomination->toarray();

    Session::put("otp_mobile", $user_nomination['mobile']);
    $data['action']               = url('roac/nomination/apply-nomination-step-1/post');
    $data['nomination_page']      = url('roac/nomination/apply-nomination-step-2');
    $data['href_new_nomination']  = url('roac/nomination');
    $data['encrypt_id']           = encrypt_string($user_nomination['id']);
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    $form_data      = $nom_nfd_form->get_form(0, $request, $data);
    $data = array_merge($form_data, $data);


    $data['nomination_id'] = $user_nomination['id'];



    $data['nomination'] = NominationApplicationModel::get_nomination($user_nomination['id']);

    $data['profileimg']  = url($user_nomination['image']); 
    $data['apply_date']  = date('d/m/Y', strtotime($user_nomination['apply_date'])); 
    $data['non_recognized_proposers'] =NominationProposerModel::where('nomination_id', $user_nomination['id'])
    ->where('candidate_id',$user_nomination['candidate_id'])
    ->get()->toarray();  
    $data['police_cases']  = NominationPoliceCaseModel::where('nomination_id', $user_nomination['id'])
    ->where('candidate_id',$user_nomination['candidate_id'])
    ->get()->toarray();  
    $data['affidavit']  = url($user_nomination['affidavit']);

//dd($data);

//end nomination validation
    $data['apply_ac']= getacbyacno($user_nomination['st_code'],$user_nomination['ac_no']);
    $data['apply_state']= getstatebystatecode($user_nomination['st_code']);
    $data['partyd']= getpartybyid($user_nomination['party_id']);   
//$symb= getsymbolbyid($user_nomination->symbol_id);
    $data['state_res']= getstatebystatecode($user_nomination['home_st_code']);
    $data['distict_res']= getdistrictbydistrictno($user_nomination['home_st_code'],$user_nomination['home_dist_no']);
    $data['ac_res']= getacbyacno($user_nomination['home_st_code'],$user_nomination['home_ac_no']);

    $data['prop_ac']= getacbyacno($ele_details->ST_CODE,$user_nomination['proposer_assembly']);

    $loginst= getstatebystatecode($ele_details->ST_CODE);
    $loginac= getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);

    $data['user_data']=$d;
    $data['ele_details']=$ele_details;
    $data['nomDetails']=$user_nomination;
    $data['heading_title']="Candidte Information"; 

    $data['st_name']=$loginst->ST_NAME;
    $data['ac_name']=$loginac->AC_NAME;




    //dd($data);
    return view($this->view_path.'.candidateinformationac', $data);   

}  // end  function  

public function candidatevalidation(Request $request)
{
    $data  = [];
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);  
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
     

    if(empty($request->input('nom_id')))
    {
        $oldnom_id =$this->xssClean->clean_input($request->old('nom_id'));
        $oldcandidate_id =$this->xssClean->clean_input($request->old('candidate_id'));
        $nomination_no =$this->xssClean->clean_input($request->old('nomination_no'));
    }
    else { 
        $oldnom_id =$this->xssClean->clean_input($request->input('nom_id'));
        $oldcandidate_id =$this->xssClean->clean_input($request->input('candidate_id'));
        $nomination_no =$this->xssClean->clean_input($request->input('nomination_no'));
    }
// echo $nom_id; dd($nomination_no);
    if ($oldnom_id=='' || $nomination_no=='') { 
        \Session::flash('error_mes', 'Session Expair Please Try Again!');
        return Redirect::to('/roac/qrscan');
    }

    $user_nomination =NominationApplicationModel::where('nomination_no', $nomination_no)
    ->where('st_code', $ele_details->ST_CODE)
    ->where('ac_no', $ele_details->CONST_NO)->first()->toarray();

    if(!$user_nomination){
        \Session::flash('error_mes', 'invalid  Nomination No.');
        return Redirect::to('/roac/qrscan');  
    }
//dd($user_nomination);
    $data['police_cases']  = NominationPoliceCaseModel::where('nomination_id', $user_nomination['id'])
    ->where('candidate_id',$user_nomination['candidate_id'])
    ->get()->toarray();  
//dd($data['police_cases']);

    $nomshares = DB::table('candidate_nomination_detail') 
    ->where('st_code', $ele_details->ST_CODE)
    ->where('ac_no', $ele_details->CONST_NO)
    ->where('old_candidate_id', $oldcandidate_id)
    ->where('old_nom_id', $oldnom_id)
    ->where('nomination_no', $nomination_no)
    ->first();
    $candshares = DB::table('candidate_personal_detail') 
    ->where('old_candidate_id', $oldcandidate_id)->first(); 

    $party=$this->commonModel->getparty($user_nomination['party_id']);
    if($party->PARTYSYM!=0) 
        $symbol_id=$party->PARTYSYM;
    else
        $symbol_id="200";

    if($party->PARTYTYPE=="S"){ 
        $partyDetails = DB::table('m_party')
        ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
        ->where('m_party.PARTYTYPE','=','S')
        ->where('d_party.ST_CODE','=',$ele_details->ST_CODE)
        ->where('m_party.CCODE','=',$party->CCODE)
        ->select('m_party.*')->first();
        if(isset($partyDetails)){
            $partytype = $party->PARTYTYPE;
        }
        else{
            $partytype ='U';
        }
    } 
    else {
        $partytype = $party->PARTYTYPE;
    }

    $g = DB::table('candidate_nomination_detail')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->get();

    $mslno=$g->max('cand_sl_no'); $mslno++;

    if($user_nomination['have_police_case']=='no') $is_crem=0; else $is_crem=1;

    $non_recognized_proposers =NominationProposerModel::where('nomination_id', $user_nomination['id'])
    ->where('candidate_id',$user_nomination['candidate_id'])
    ->get()->toarray();  

    $candPersonalData = array(
        'cand_name'=>ucwords($user_nomination['name']),
        'cand_hname'=>$user_nomination['hname'],
        'cand_vname'=>$user_nomination['vname'],
        'cand_alias_name'=>$user_nomination['alias_name'],
        'cand_alias_hname'=>$user_nomination['alias_hname'],
        'cand_alias_vname'=>$user_nomination['alias_vname'],
        'candidate_father_name'=>ucwords($user_nomination['father_name']),
        'cand_fhname'=>$user_nomination['father_hname'],
        'cand_fvname'=>$user_nomination['father_vname'],
        'cand_email'=>$user_nomination['email'],
        'cand_mobile'=>$user_nomination['mobile'],
        'cand_gender'=>$user_nomination['gender'],
        'candidate_residence_address'=>$user_nomination['address'],
        'candidate_residence_addressh'=>$user_nomination['haddress'],
        'candidate_residence_addressv'=>$user_nomination['vaddress'],
        'candidate_residence_stcode'=>$user_nomination['home_st_code'],
        'candidate_residence_districtno'=>$user_nomination['home_dist_no'],
        'candidate_residence_acno'=>$user_nomination['home_ac_no'], //resident_ac_no
        'cand_category'=>$user_nomination['category'],
        'cand_age'=>$user_nomination['age'],
        'cand_panno'=>$user_nomination['pan_number'],
        'cand_qualification'=>'',
        'cand_cast'=>'',
        'cand_cast_state'=>$user_nomination['part3_cast_state'],
        'cand_cast_area'=>$user_nomination['part3_address'],
        'cand_dob'=>$user_nomination['dob'],
        'cand_image'=>$user_nomination['image'],
        'cand_apply_date'=>$user_nomination['apply_date'],
        'cand_nickname'=>$user_nomination['alias_name'],
        'cand_epic_no'=>$user_nomination['have_police_case'],
        'is_criminal'=>$user_nomination['have_police_case'],
        'name_of_language'=>$user_nomination['language'],
        'election_id'=>$ele_details->ELECTION_ID,
        'old_candidate_id'=>$oldcandidate_id,
        'created_by'=>$d->officername,
        'created_at'=>date('Y-m-d H:i:s'),
        'added_create_at'=>date('Y-m-d'),
    );
//dd($candPersonalData);
    if(!isset($candshares)){
        $n = DB::table('candidate_personal_detail')->insert($candPersonalData);  
        $cid = DB::getPdo()->lastInsertId();
    }
    else{
        DB::table('candidate_personal_detail')
        ->where('candidate_id', $candshares->candidate_id)
        ->update($candPersonalData);
        $cid=$candshares->candidate_id;
    }
    $candNomData = array(
        'election_id'=>$ele_details->ELECTION_ID,
        'party_id'=>$user_nomination['party_id'],
        'cand_sl_no'=>$mslno,
        'new_srno'=>$mslno,
        'symbol_id'=>$symbol_id,
        'ac_no'=>$ele_details->CONST_NO,
        'st_code'=>$ele_details->ST_CODE,
        'candidate_id'=>$cid,
        'district_no'=>$d->dist_no,
        'date_of_submit'=>date('Y-m-d'),
        'qrcode'=>$user_nomination['qrcode'],
        'created_by'=>$d->officername,
        'created_at'=>date('Y-m-d H:i:s'),
        'added_create_at'=>date('Y-m-d'),
        'application_status'=>'2',
        'cand_party_type'=>$partytype,
        'scheduleid'=>$ele_details->ScheduleID,
        'election_type_id'=>$ele_details->ELECTION_TYPEID,
        'state_phase_no'=>$ele_details->StatePHASE_NO,
        'm_election_detail_ccode'=>$ele_details->CCODE,
        'party_type'=>$user_nomination['recognized_party'],
        'sug_symbol1'=>$user_nomination['suggest_symbol_1'],
        'sug_symbol2'=>$user_nomination['suggest_symbol_2'],
        'sug_symbol3'=>$user_nomination['suggest_symbol_3'],
        'finalize_by_candidate'=>$user_nomination['finalize'],
        'proposer_name'=>$user_nomination['proposer_name'],
        'proposer_slno'=>$user_nomination['proposer_serial_no'],
        'proposer_partno'=>$user_nomination['proposer_part_no'],
        'proposer_stcode'=>$ele_details->ST_CODE,
        'proposer_acno'=>$user_nomination['proposer_assembly'],
        'nomination_type'=>$user_nomination['nomination_type'],
        'old_candidate_id'=>$oldcandidate_id,
        'old_nom_id'=>$oldnom_id,
        'nomination_no'=>$nomination_no,
        'part3_date'=>$user_nomination['part3_date'], 
        'have_police_case'=>$user_nomination['have_police_case'], 
        'profit_under_govt'=>$user_nomination['profit_under_govt'], 
        'office_held'=>$user_nomination['office_held'],
        'court_insolvent'=>$user_nomination['court_insolvent'],
        'discharged_insolvency'=>$user_nomination['discharged_insolvency'],
        'allegiance_to_foreign_country'=>$user_nomination['allegiance_to_foreign_country'],
        'country_details'=>$user_nomination['country_detail'], 
        'disqualified_section8A'=>$user_nomination['disqualified_section8A'], 
        'disqualified_period'=>$user_nomination['disqualified_period'],
        'disloyalty_status'=>$user_nomination['disloyalty_status'],
        'date_of_dismissal'=>$user_nomination['date_of_dismissal'],
        'subsiting_gov_taken'=>$user_nomination['subsiting_gov_taken'],
        'subsitting_contract'=>$user_nomination['subsitting_contract'],
        'managing_agent'=>$user_nomination['managing_agent'],
        'gov_details'=>$user_nomination['gov_detail'],
        'disqualified_by_comission_10Asec'=>$user_nomination['disqualified_by_comission_10Asec'],
        'date_of_disqualification'=>$user_nomination['date_of_disqualification'],
        'date_of_disloyal'=>$user_nomination['date_of_disloyal']
    );

    if(!isset($nomshares)){
        $n = DB::table('candidate_nomination_detail')->insert($candNomData);
        $nom_id=DB::getPdo()->lastInsertId();
    }
    else{
        DB::table('candidate_nomination_detail')
        ->where('nom_id', $nomshares->nom_id)
        ->update($candNomData);
        $nom_id=$nomshares->candidate_id;
    }
    if($user_nomination['affidavit']!='') {
    $getAffidavitDetails = getById('candidate_affidavit_detail','nom_id',$nom_id); 
    $affidavitName = "Affidavite Form 26";
    if(!empty($getAffidavitDetails) ){
        $updateNomDetail = DB::update('update candidate_affidavit_detail set affidavit_path ="'.$user_nomination['affidavit'].'" where nom_id = ' .$nom_id);

    }else{
        DB::table('candidate_affidavit_detail')->insert([
            ['candidate_id' => $cid, 
            'nom_id' => $nom_id, 
            'affidavit_name' => $affidavitName, 
            'affidavit_path' =>$user_nomination['affidavit'], 
            'created_by' => $d->officername, 
            'updated_by' => $d->officername, 
            'created_by'=>$d->officername,
            'created_at'=>date('Y-m-d H:i:s'),
            'added_create_at'=>date('Y-m-d'),
            'ac_no'=>$ele_details->CONST_NO,
            'st_code'=>$ele_details->ST_CODE,
            'added_update_at'=>date('Y-m-d')]
        ]);
    }
} // end affidavite
    if(isset($data['police_cases'])){

        foreach($data['police_cases'] as $police){
            $record = DB::table('candidate_criminal_case') 
            ->where('old_candidate_id',$police['candidate_id'])
            ->where('old_nom_id',$police['nomination_id'])
            ->where('case_no',$police['case_no'])
            ->where('nom_id', $nom_id)
            ->where('candidate_id', $cid)->first();
            $crim_data = array(
                'candidate_id'=>$cid,
                'nom_id'=>$nom_id,
                'old_candidate_id'=>$police['candidate_id'],
                'old_nom_id'=>$police['nomination_id'],
                'created_at'=>date('Y-m-d H:i:s'),
                'added_update_at'=>date('Y-m-d'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'created_by'=>$d->officername,
                'updated_by'=>$d->officername, 
                'case_no'=>$police['case_no'],
                'police_station'=>$police['police_station'],
                'state_code'=>$police['case_st_code'],
                'district_no'=>$police['case_dist_no'],
                'convicted_des'=>$police['convicted_des'],
                'date_of_conviction'=>$police['date_of_conviction'],
                'court_name'=>$police['court_name'],
                'punishment_imposed'=>$police['punishment_imposed'],
                'date_of_release'=>$police['date_of_release'],
                'revision_against_conviction'=>$police['revision_against_conviction'],
                'revision_appeal_date'=>$police['revision_appeal_date'],
                'revision_appeal_court'=>$police['rev_court_name'],
                'revision_status'=>$police['status'], 
                'revision_disposal_date'=>$police['revision_disposal_date'],
                'revision_order_description'=>$police['revision_order_description'],
                'election_id'=>$ele_details->ELECTION_ID
            );
            if(!isset($record)){
                $n = DB::table('candidate_criminal_case')->insert($crim_data);
            }
            else{
                DB::table('candidate_criminal_case')
                ->where('old_candidate_id',$police['candidate_id'])
                ->where('old_nom_id',$police['nomination_id'])
                ->where('case_no',$police['case_no'])
                ->where('nom_id', $nom_id)

                ->where('candidate_id', $cid)
                ->update($crim_data);
            }
} // end foreach

}  // end if 

if(isset($non_recognized_proposers)){

    foreach($non_recognized_proposers as $proposers){
        $record = DB::table('candidate_proposer_details') 
        ->where('old_candidate_id',$proposers['candidate_id'])
        ->where('old_nom_id',$proposers['nomination_id'])
        ->where('nom_id', $nom_id)->where('nomination_prop_id', $proposers['id'])
        ->where('candidate_id', $cid)->first();
        $prop_data = array(
            'candidate_id'=>$cid,
            'nom_id'=>$nom_id,
            'old_candidate_id'=>$proposers['candidate_id'],
            'old_nom_id'=>$proposers['nomination_id'],
            'created_by'=>$d->officername,
            'proposer_name'=>$proposers['fullname'],
            'proposer_slno'=>$proposers['serial_no'],
            'serial_no'=>$proposers['s_no'],
            'proposer_partno'=>$proposers['part_no'],
            'proposer_stcode'=>$ele_details->ST_CODE,
            'proposer_acno'=>$ele_details->CONST_NO,
            'proposer_date'=>$proposers['date'],
            'nomination_prop_id'=>$proposers['id'],
            'election_id'=>$ele_details->ELECTION_ID
        );
        if(!isset($record)){
            $n = DB::table('candidate_proposer_details')->insert($prop_data);
        }
        else{
            DB::table('candidate_proposer_details')
            ->where('old_candidate_id',$proposers['candidate_id'])
            ->where('old_nom_id',$proposers['nomination_id'])
            ->where('nomination_prop_id', $proposers['id'])
            ->where('nom_id', $nom_id)
            ->where('candidate_id', $cid)
            ->update($prop_data);
        }
} // end foreach

}  // end if 



$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'application_status','applied','verified',request()->ip(),'NA','N/A','2','Complete',date("Y-m-d"));

$shares=$this->Applicants->nominationinformation($nom_id);  

if(isset($shares)){
    $state=$this->commonModel->getstatebystatecode($shares->st_code);
    $ac=$this->commonModel->getacbyacno($shares->st_code,$shares->ac_no);
    $data['state']=$state;
    $data['ac']=$ac;
    $data['st_name']=$state->ST_NAME;
    $data['ac_name']=$ac->AC_NAME;
}

$data['user_data']=$d;
$data['ele_details']=$ele_details;
$data['caddata']=$shares;
$data['heading_title']="Candidte Information"; 
 
    return redirect('roac/decisionbyro?nom_id='.encrypt_string($nom_id));

    //return view($this->view_path.'.decisionbyro', $data); 

}  // end  function 

  public function decisionbyro(Request $request) {
                $data  = [];
                $user = Auth::user();
                $d=$this->commonModel->getunewserbyuserid($user->id);  
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
                $nom_id = decrypt_string($request->nom_id);
                $shares=$this->Applicants->nominationinformation($nom_id);  

            if(isset($shares)){
                $state=$this->commonModel->getstatebystatecode($shares->st_code);
                $ac=$this->commonModel->getacbyacno($shares->st_code,$shares->ac_no);
                $data['state']=$state;
                $data['ac']=$ac;
                $data['st_name']=$state->ST_NAME;
                $data['ac_name']=$ac->AC_NAME;
            }

                $data['user_data']=$d;
                $data['ele_details']=$ele_details;
                $data['caddata']=$shares;
                $data['heading_title']="Candidte Information"; 
                return view($this->view_path.'.decisionbyro', $data); 

}  // end  function 

public function decisionvalidate(Request $request)
{
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $input = $request->all(); 
    $validator = Validator::make($request->all(), 
        [
            'nomination_srno' => 'required',
            'nomination_submittedby'=>'required',
        ],
        [
            'nomination_srno.required' => 'Please enter Nomination Sr.number', 
            'nomination_submittedby.required'=>'Please select submitted by', 
        ]);

    if ($validator->fails()) { 
        return redirect::back()
        ->withErrors($validator)
        ->withInput();
    }
 

    $candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
    $nom_id =$this->xssClean->clean_input($request->input('nom_id')); 
    $nomination_srno = $this->xssClean->clean_input($request->input('nomination_srno'));
    $nomination_hour = $this->xssClean->clean_input($request->input('nomination_hour'));
    $nomination_date = $this->xssClean->clean_input($request->input('nomination_date'));  
    $nomination_submittedby = $this->xssClean->clean_input($request->input('nomination_submittedby'));
    if ($nom_id=='' || $nom_id==0) { 
        \Session::flash('error_mes', 'Session Expair Please Try Again!');
        return Redirect::to('/roac/qrscan');
    }

    $nom_data = array('nomination_papersrno'=>$nomination_srno,
                        'rosubmit_time'=>$nomination_hour,
                        'rosubmit_date'=>date("Y-m-d",strtotime($nomination_date)),
                        'nomination_submittedby'=>$nomination_submittedby,
                        'updated_at'=>date("Y-m-d",strtotime($nomination_date)) ." ".$nomination_hour,
                        'updated_by'=>$d->officername); 

    $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($nom_data);

    $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'nomination_papersrno','NA',$nomination_srno,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
    $shares=$this->Applicants->nominationinformation($nom_id);    

    if(isset($shares)){
        $state=$this->commonModel->getstatebystatecode($shares->st_code);
        $ac=$this->commonModel->getacbyacno($shares->st_code,$shares->ac_no);
        $data['state']=$state;
        $data['ac']=$ac;
        $data['st_name']=$state->ST_NAME;
        $data['ac_name']=$ac->AC_NAME;
    } 

    $data['user_data']=$d;
    $data['caddata']=$shares;
    $data['heading_title']="Print Receipt"; 
    return redirect('roac/finalreceipt?nom_id='.encrypt_string($nom_id));
    return view($this->view_path.'.finalreceipt', $data); 

}  // end  function  print_receipt

public function finalreceipt(Request $request) {
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
    $Schedule=getschedulebyid($ele_details->ScheduleID);
     
     $nom_id = decrypt_string($request->nom_id); 
     
    if ($nom_id=='' || $nom_id==0) { 
        \Session::flash('error_mes', 'Session Expair Please Try Again!');
        return Redirect::to('/roac/qrscan');
    }

    
    $shares=$this->Applicants->nominationinformation($nom_id);    

    if(isset($shares)){
        $state=$this->commonModel->getstatebystatecode($shares->st_code);
        $ac=$this->commonModel->getacbyacno($shares->st_code,$shares->ac_no);
        $data['state']=$state;
        $data['ac']=$ac;
        $data['st_name']=$state->ST_NAME;
        $data['ac_name']=$ac->AC_NAME;
    } 

    $data['user_data']=$d;
    $data['caddata']=$shares;
    $data['heading_title']="Print Receipt"; 
    $data['ele_details']=$ele_details;
    $data['Schedule']=$Schedule;
    $data['scrutiny_date']=date("d-m-Y",strtotime($Schedule['DT_SCR_NOM']));
    //dd($data);
    return view($this->view_path.'.finalreceipt', $data); 

}  // end  function  print_receipt


public function decisionvalidatel(Request $request) {
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $input = $request->all();  dd($input);
    if(empty($request->input('nom_id')))
    {
        $candidate_id = $this->xssClean->clean_input($request->old('candidate_id'));
        $nom_id =$this->xssClean->clean_input($request->old('nom_id')); 
        $nomination_srno = $this->xssClean->clean_input($request->old('nomination_srno'));
        $nomination_hour = $this->xssClean->clean_input($request->old('nomination_hour'));
        $nomination_date = $this->xssClean->clean_input($request->old('nomination_date'));  
        $nomination_submittedby = $this->xssClean->clean_input($request->old('nomination_submittedby'));
    }
    else { 
        $candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
        $nom_id =$this->xssClean->clean_input($request->input('nom_id')); 
        $nomination_srno = $this->xssClean->clean_input($request->input('nomination_srno'));
        $nomination_hour = $this->xssClean->clean_input($request->input('nomination_hour'));
        $nomination_date = $this->xssClean->clean_input($request->input('nomination_date'));  
        $nomination_submittedby = $this->xssClean->clean_input($request->input('nomination_submittedby'));
    }
    if ($nom_id=='' || $nom_id==0) { 
        \Session::flash('error_mes', 'Session Expair Please Try Again!');
        return Redirect::to('/roac/listallapplicant');
    }
    $shares=$this->Applicants->nominationinformation($nom_id);    

    if(isset($shares)){
        $state=$this->commonModel->getstatebystatecode($shares->st_code);
        $ac=$this->commonModel->getacbyacno($shares->st_code,$shares->ac_no);
        $data['state']=$state;
        $data['ac']=$ac;
        $data['st_name']=$state->ST_NAME;
        $data['ac_name']=$ac->AC_NAME;
    }


    $nom_data = array('nomination_papersrno'=>$nomination_srno,
        'rosubmit_time'=>$nomination_hour,
        'rosubmit_date'=>date("Y-m-d",strtotime($nomination_date)),
        'nomination_submittedby'=>$nomination_submittedby,
        'updated_at'=>date("Y-m-d",strtotime($nomination_date)) ." ".$nomination_hour,
        'updated_by'=>$d->officername); 

    $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($nom_data);




    $data['user_data']=$d;
    $data['caddata']=$shares;
    $data['heading_title']="Print Receipt"; 


    $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'nomination_papersrno','NA',$nomination_srno,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));

    return view($this->view_path.'.finalreceipt', $data); 

}  //F end  function  print_receipt

public function print_receipt(Request $request)
{ 
   //dd($request->input());
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);

    $validator = Validator::make($request->all(), 
        [
            'scrutiny_time' => 'required',
            'scrutiny_date'=>'required',
        ],
        [
            'scrutiny_time.required' => 'Please enter valid time ',
            'scrutiny_date.required' => 'Please enter valid date', 
        ]);

    if ($validator->fails()) { 
      //  return redirect('/roac/decisionvalidatel')
        return Redirect::back()
        ->withErrors($validator)
        ->withInput();
    }
    $candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
    $scrutiny_time = $this->xssClean->clean_input($request->input('scrutiny_time'));
    $scrutiny_date = $this->xssClean->clean_input($request->input('scrutiny_date'));
    $nom_id = $this->xssClean->clean_input($request->input('nom_id'));

    $place = $this->xssClean->clean_input($request->input('place'));
    $fdate = $this->xssClean->clean_input($request->input('fdate'));
    if ($nom_id=='' || $nom_id==0) { 
        \Session::flash('error_mes', 'Session Expair Please Try Again!');
        return Redirect::to('/roac/listallapplicant');
    }
    $nom_data =   array('scrutiny_time'=>$scrutiny_time,
        'scrutiny_date'=>$scrutiny_date,
        'place'=>$place,
        'fdate'=>$fdate,
        'application_status'=>'3'); 

    $i = DB::table('candidate_nomination_detail')->where('nom_id',$nom_id)->update($nom_data);


    $shares=$this->Applicants->nominationinformation($nom_id);    

    $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',
        $nom_id,'scrutiny_date','NA',
        $scrutiny_date,request()->ip(),'NA','N/A','3',
        'Complete',date("Y-m-d"));

    $ac=getacbyacno($shares->st_code,$shares->ac_no);
    $state=getstatebystatecode($shares->st_code);

    $sub="Your Nomination Application Status change";

    $Mob_otp="Dear ".$shares->cand_name." your Nomination Application of the constituency ".$ac->AC_NAME." of state ".$state->ST_NAME." is Receipt Generated by returning officer";

    $html ="<html>
    <body>
    <p>Dear ".$shares->cand_name.",<br/><br/></p>
    <p>Your Nomination Application Serial No. of nomination paper <b> ".$shares->nomination_papersrno."</b> ,  Form is Submitted on date ".date("d/m/Y")." for the General / Bye elections of the constituency <b> ".$ac->AC_NAME."</b> of state  <b> ".$state->ST_NAME."</b>. Your Scutiny Date <b> ".date("d-m-Y",strtotime($shares->scrutiny_date))."</b> and time <b> ".$shares->scrutiny_time."</b> is <b>Receipt Generated </b>by returning officer</p>
    <p><br/><br/>Regards,<br/>
    Returning Officer. <br>
    Election Commission of India<br/>
    </p>
    </body>
    </html>";  

//CandidateECIMail($cand->cand_email,$cand->cand_name,$html);
//$response = SmsgatewayHelper::sendOtpSMS($Mob_otp,$cand->cand_mobile); 

    $data['user_data']=$d;
    $data['caddata']=$shares;
    $data['heading_title']="Receipt Generated"; 
//$data['nomination']=$nom; 
    $data['state']=$state;
    $data['ac']=$ac;

    $data['st_name']=$state->ST_NAME;
    $data['ac_name']=$ac->AC_NAME;

    return view($this->view_path.'.printreceipt', $data);     


}  // end  function   
public function nomination_receipt_print(Request $request)
{ 
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $nom_id = $this->xssClean->clean_input($request->nom_id);
    $shares=$this->Applicants->nominationinformation($nom_id);    
    $ac=getacbyacno($shares->st_code,$shares->ac_no);
    $state=getstatebystatecode($shares->st_code);

    $data['user_data']=$d;
    $data['caddata']=$shares;
    $data['heading_title']="Print Receipt"; 
    $data['state']=$state;
    $data['ac']=$ac;
    $data['st_name']=$state->ST_NAME;
    $data['ac_name']=$ac->AC_NAME;
    $data['ac_no']=$ac->AC_NO; 
    $name_excel = 'printreceipt-'.$shares->st_code."-ac_no-".$shares->ac_no.'-'.time();
    $data['file_name']=$name_excel; 
    $data['ref_no']  =time();


    $data['user']=\Auth::user()->officername;
    $data['print_date']=date('d-m-Y H:i:a');
    $data['name']=\Auth::user()->name;

    $setting_pdf = [
        'margin_top'        =>76,  
        'margin_bottom'     =>10,
        'margin_left'       =>10,  
        'margin_right'      =>10,
        'show_warnings'     => false,    
// 'orientation'       => 'portlet',    
    ];
//return view($this->view_path.'.downloadprintreceipt', $data);   
    $pdf = \MPDF::loadView($this->view_path.'.downloadprintreceipt',$data,[], $setting_pdf);
    return $pdf->download($name_excel.'.pdf');    


}  // end  function  

//========================================================================================================================

public function apply_nomination_step_1($id = 0, Request $request)
      {    
        $data                   = [];
      //  $data['is_active']     = 'nomination';
        $data['heading_title'] = "Candidate Nomination Details";
        $data['breadcrumbs'] = "Candidate Nomination Details";  
       // $data['action']        = url('nfd/nomination/apply-nomination-step-2/post');
        $data['action']               = url('roac/nomination/apply-nomination-step-1/post');
        $data['nomination_page']      = url('roac/nomination/apply-nomination-step-2');
        $data['href_new_nomination']  = url('roac/nomination');
        $data['encrypt_id']           = '';
        $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);  
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
    if($ele_details=='') 
    {
        \Session::flash('error_mes', 'Election has not assigned');
        return Redirect::to('/logout');
    }
    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
    if($check_finalize->finalized_ac=='1') 
    {
        \Session::flash('error_mes', 'Ac is Finalized');
        return Redirect::to('/roac/dashboard');
    }
    $seched=getschedulebyid($ele_details->ScheduleID);
    if($seched['DATE_POLL']<date("Y-m-d")) {
        \Session::flash('error_mes', 'Poll Date Completed');
        return Redirect::to('/roac/listnomination');  

    }
        $data['user_data']   =$d;
        $data['ele_details']   =$ele_details;
      //   $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
        $form_data      =$this->get_form($id, $request, $data);
        $data = array_merge($form_data, $data);
       // dd($data);
        return view($this->view_path.'.nomination.apply-nomination-step-1',$data);
      }






public function save_step_1(Request $request){
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    return $nom_nfd_form->save_step_1($request);
}

public function apply_nomination_step_2($id = 0, Request $request){
    Session::forget('nomination_id');
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    $data                   = [];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Select Election Detail";
    $data['action']        = url('roac/nomination/apply-nomination-step-2/post');
    $data = $nom_nfd_form->get_step2_form($id, $request, $data);
    return view('admin/nfd/nomination/apply-nomination-step-2',$data);
}

public function save_step_2(Request $request){
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    $response       = $nom_nfd_form->save_step_2($request);
    $decoded_response = json_decode($response->getContent(), true);
    if($decoded_response['status'] == false){
        return $response;
    }
    return \Response::json([
        'status' => true,
        'redirect' => url('roac/nomination/apply-nomination-step-3')
    ]);
}

public function apply_nomination_step_3($id = 0, Request $request){

    $data                   = [];
    $data['is_active']      = 'nomination';
    $data['heading_title']  = "Form 2B - Nomination Paper ";
    $data['action']         = url('roac/nomination/apply-nomination-step-3/post-part-1');
    $data['href_file_upload'] = url('roac/nomination/upload');
//nomination validation id
    if($id == 0){
        if(!Session::has('nomination_id')){
            Session::flash('flash-message','please apply again.');
            return redirect("roac/qrscan"); 
        }
        $id = Session::get('nomination_id');
    }
    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
        return redirect("roac/qrscan");
    }

    $data['nomination_id'] = $user_nomination['id'];
//end nomination validation
    $data = array_merge($data, $user_nomination);
    $nom_nfd_form       = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    $data               = $nom_nfd_form->get_step3_form($id, $request, $data);
    $data['user_data']  = Auth::user();
    $data['breadcrumbs'] = [];
    return view('admin/nfd/nomination/apply-nomination-step-3',$data);

}

public function save_step_3(NominationPart12Request $request){
    $nom_nfd_form       = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    return $nom_nfd_form->save_step_3($request);

}


public function apply_nomination_step_4($id = 0, Request $request){
    $data                  = [];
    $data['breadcrumbs']    = [];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Form 2B - Nomination Paper";
    $data['action'] = url('roac/nomination/apply-nomination-step-4/post');
//nomination validation id
    if($id == 0){
        if(!Session::has('nomination_id')){
            Session::flash('flash-message','please apply again.');
            return redirect("roac/qrscan"); 
        }
        $id = Session::get('nomination_id');
    }
    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
        return redirect("roac/qrscan");
    }
    if($user_nomination['finalize'] == 1){
        Session::flash('flash-message','You can not edit this nomination.');
        return redirect("roac/qrscan"); 
    }
    $data['nomination_id'] = $user_nomination['id'];
//end nomination validation
    $data['recognized_party'] = $user_nomination['recognized_party'];
    $data           = array_merge($data, $user_nomination);
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    $data           = $nom_nfd_form->get_step4_form($id, $request, $data);
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/apply-nomination-step-4',$data);
}

public function save_step_4(NominationPart3Request $request){
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    return $nom_nfd_form->save_step_4($request);
}

//save step 5
public function apply_nomination_step_5($id = 0, Request $request){
    $data                  = [];
    $data['breadcrumbs']   = [];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Form 2B - Nomination Paper";
    $data['action'] = url('roac/nomination/apply-nomination-step-5/post');

//nomination validation id
    if($id == 0){
        if(!Session::has('nomination_id')){
            Session::flash('flash-message','please apply again.');
            return redirect("roac/qrscan"); 
        }
        $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
        return redirect("roac/qrscan");
    }
    if($user_nomination['finalize'] == 1){
        Session::flash('flash-message','You can not edit this nomination.');
        return redirect("roac/qrscan"); 
    }

    $data['nomination_id'] = $user_nomination['id'];
//end nomination validation

    $data['recognized_party'] = $user_nomination['recognized_party'];

    $data = array_merge($data, $user_nomination);
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    $data           = $nom_nfd_form->get_step5_form($id, $request, $data);
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/apply-nomination-step-5',$data);
}

public function save_step_5(NominationPart3aRequest $request){
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    return $nom_nfd_form->save_step_5($request);
}


public function apply_nomination_step_6($id = 0, Request $request){
    $data                  = [];
    $data['breadcrumbs']    = [];
    $data['is_active']        = 'nomination';
    $data['heading_title']    = "Upload Affidavit";
    $data['action']           = url('roac/nomination/apply-nomination-step-6/post');
    $data['href_file_upload'] = url('roac/nomination/upload-affidavit');

//nomination validation id
    if($id == 0){
        if(!Session::has('nomination_id')){
            Session::flash('flash-message','please apply again.');
            return redirect("roac/qrscan"); 
        }
        $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
        return redirect("roac/qrscan");
    }
    if($user_nomination['finalize'] == 1){
        Session::flash('flash-message','You can not edit this nomination.');
        return redirect("roac/qrscan"); 
    }

    $data['nomination_id'] = $user_nomination['id'];
    if($request->old('affidavit')){
        $data['affidavit']  = $request->old('affidavit');
    }else if(isset($object)){
        $data['affidavit']  = $object['affidavit']; 
    }else{
        $data['affidavit']  = '';
    }
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/apply-nomination-step-6',$data);
}

public function save_step_6(Request $request){
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    return $nom_nfd_form->save_step_6($request);
}

public function upload_files(Request $request){
    $nom_nfd_form       = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    return $nom_nfd_form->upload_files($request);
}

public function upload_affidavit(Request $request){
    $nom_nfd_form       = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    return $nom_nfd_form->upload_affidavit($request);
}

public function download_nomination($id, Request $request){
    $nom_nfd_form       = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    return $nom_nfd_form->download_nomination($request);
}




public function apply_nomination_finalize($id = 0, Request $request){

//nomination validation id
    if($id == 0){
        if(!Session::has('nomination_id')){
            Session::flash('flash-message','please apply again.');
            return redirect("roac/qrscan"); 
        }
        $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
        return redirect("roac/qrscan");
    }

    if($user_nomination['finalize'] == 1){
        Session::flash('flash-message','You can not edit this nomination.');
        return redirect("roac/qrscan"); 
    }
    $data['nomination_id'] = $user_nomination['id'];
//end nomination validation

    $data = NominationApplicationModel::get_nomination($user_nomination['id']);
    $data['breadcrumbs']    = [];
    $data['is_active']      = 'nomination';
    $data['heading_title']  = "Nomination Detail";
    $data['action']         = url('roac/nomination/apply-nomination-finalize/post');

    $data['st_name'] = '';
    $state_object = StateModel::get_state($data['st_code']);
    if($state_object){
        $data['st_name'] = $state_object->ST_NAME;
    }

    $data['states'] = [];
    $states = StateModel::get_states();
    foreach ($states as $key => $state_iterage) {
        $data['states'][] = [
            'st_code'    => $state_iterage->st_code,
            'st_name'    => $state_iterage->st_name,
        ];
    }

    $data['districts'] = [];
    $districts = DistrictModel::get_districts();
    foreach ($districts as $key => $district_iterage) {
        $data['districts'][] = [
            'district_no'     => $district_iterage->district_no,
            'district_name'   => $district_iterage->district_name,
            'st_code'         => $district_iterage->st_code,
            'encoded'         => base64_encode($district_iterage->district_no),
        ];
    }

    $data['profileimg']  = url($data['image']); 
    $data['apply_date']  = date('d/m/Y', strtotime($data['apply_date'])); 
    $data['non_recognized_proposers']   = NominationProposerModel::get_proposers($data['id']);  
    $data['police_cases']               = NominationPoliceCaseModel::get_police_cases($data['nomination_id']); 
    $data['affidavit']  = url($data['affidavit']);

//QR code
    $st_code          = $data['st_code'];
    $year             = date('Y');
    $ac_no            = $data['ac_no'];
    $election_name    = 'E'.$data['election_id'];
    $this->upload_folder = config("public_config.upload_folder");
    $destination_path = FileModel::get_file_path($this->upload_folder.'/qrcode/'.$year.'/ac/'.$election_name.'/'.$st_code.'/'.$ac_no).'/'.$data['id'].'.png';
    \QRCode::text(url("/nomination-status/".$data['id']))->setOutfile($destination_path)->png();
    $data['qrcode_path']  = $destination_path;
    $data['qrcode']       = url($destination_path);
    NominationApplicationModel::add_qrcode($data);
//end QR code
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/apply-nomination-finalize',$data);
}


public function save_nomination_finalize($id = 0, Request $request){
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    return $nom_nfd_form->save_nomination_finalize($id, $request);
}


public function get_form($id,$request,$data = array()){
    
    $object = NominationApplicationModel::get_nomination_application($id);
   
    if($request->old('name')){
      $data['name']  = $request->old('name');
    }else if(isset($object) && ($object)){
      $data['name']  = $object['name']; 
    }else{
      $data['name']  = ''; 
    }

    if($request->old('email')){
      $data['email']  = $request->old('email');
   }else if(isset($object) && ($object)){
      $data['email']  = $object['email']; 
    }else{
      $data['email']  = ''; 
    }

    $data['mobile']  = Session::get('otp_mobile'); 
    
    if($request->old('name_hindi')){
      $data['name_hindi']  = $request->old('name_hindi');
   }else if(isset($object) && ($object)){
      $data['name_hindi']  = $object['name_hindi']; 
    }else{
      $data['name_hindi']  = ''; 
    }

    if($request->old('vernacular_name')){
      $data['vernacular_name']  = $request->old('vernacular_name');
   }else if(isset($object) && ($object)){
      $data['vernacular_name']  = $object['vernacular_name']; 
    }else{
      $data['vernacular_name']  = ''; 
    }

    if($request->old('alias_name')){
      $data['alias_name']  = $request->old('alias_name');
   }else if(isset($object) && ($object)){
      $data['alias_name']  = $object['alias_name']; 
    }else{
      $data['alias_name']  = ''; 
    }

    if($request->old('alias_name_hindi')){
      $data['alias_name_hindi']  = $request->old('alias_name_hindi');
   }else if(isset($object) && ($object)){
      $data['alias_name_hindi']  = $object['alias_name_hindi']; 
    }else{
      $data['alias_name_hindi']  = ''; 
    }

    if($request->old('father_name')){
      $data['father_name']  = $request->old('father_name');
   }else if(isset($object) && ($object)){
      $data['father_name']  = $object['father_name']; 
    }else{
      $data['father_name']  = ''; 
    }

    if($request->old('father_name_hindi')){
      $data['father_name_hindi']  = $request->old('father_name_hindi');
   }else if(isset($object) && ($object)){
      $data['father_name_hindi']  = $object['father_name_hindi']; 
    }else{
      $data['father_name_hindi']  = ''; 
    }

    if($request->old('father_name_vernacular')){
      $data['father_name_vernacular']  = $request->old('father_name_vernacular');
   }else if(isset($object) && ($object)){
      $data['father_name_vernacular']  = $object['father_name_vernacular']; 
    }else{
      $data['father_name_vernacular']  = ''; 
    }

    if($request->old('pan_number')){
      $data['pan_number']  = $request->old('pan_number');
   }else if(isset($object) && ($object)){
      $data['pan_number']  = $object['pan_number']; 
    }else{
      $data['pan_number']  = ''; 
    }



    $data['states'] = [];
    $states = StateModel::orderBy('ST_NAME','ASC')->get();
    foreach ($states as $key => $state_iterage) {
      $data['states'][] = [
        'st_code'    => $state_iterage->ST_CODE,
        'st_name'    => $state_iterage->ST_NAME,
      ];
    }

    $data['districts'] = [];
    $districts = DistrictModel::orderByRaw('ST_CODE, DIST_NO ASC')->get();
    foreach ($districts as $key => $district_iterage) {
      $data['districts'][] = [
        'district_no'     => $district_iterage->DIST_NO,
        'district_name'   => $district_iterage->DIST_NO.'-'.$district_iterage->DIST_NAME,
        'st_code'         => $district_iterage->ST_CODE,
        'encoded'         => base64_encode($district_iterage->DIST_NO),
      ];
    }

    $data['acs'] = [];
    $acs = AcModel::get();
    foreach ($acs as $key => $ac_iterage) {
      $data['acs'][] = [
        'ac_no'         => $ac_iterage->AC_NO,
        'ac_name'       => $ac_iterage->AC_NO.'-'.$ac_iterage->AC_NAME,
        'st_code'       => $ac_iterage->ST_CODE,
        'district_no'   => $ac_iterage->DIST_NO_HDQTR,
        'encoded'       => base64_encode($ac_iterage->AC_NO),
      ];
    } 

    $data['categories'] = [
      ['id' => 'sc', 'name' => 'SC'],
      ['id' => 'st', 'name' => 'ST'],
      ['id' => 'general', 'name' => 'GENERAL'],
    ];      

    if($request->old('category')){
      $data['category']  = $request->old('category');
   }else if(isset($object) && ($object)){
      $data['category']  = $object['category']; 
    }else{
      $data['category']  = ''; 
    }

    if($request->old('dob')){
      $data['dob']  = $request->old('dob');
   }else if(isset($object) && ($object)){
      $data['dob']  = $object['dob']; 
    }else{
      $data['dob']  = ''; 
    }

    if($request->old('age')){
      $data['age']  = $request->old('age');
   }else if(isset($object) && ($object)){
      $data['age']  = $object['age']; 
    }else{
      $data['age']  = ''; 
    }

    if($request->old('gender')){
      $data['gender']  = $request->old('gender');
   }else if(isset($object) && ($object)){
      $data['gender']  = $object['gender']; 
    }else{
      $data['gender']  = ''; 
    }

    if($request->old('address_1')){
      $data['address_1']  = $request->old('address_1');
   }else if(isset($object) && ($object)){
      $data['address_1']  = $object['address_1']; 
    }else{
      $data['address_1']  = ''; 
    }

    if($request->old('address_1_hindi')){
      $data['address_1_hindi']  = $request->old('address_1_hindi');
   }else if(isset($object) && ($object)){
      $data['address_1_hindi']  = $object['address_1_hindi']; 
    }else{
      $data['address_1_hindi']  = ''; 
    }

    if($request->old('address_1_vernacular')){
      $data['address_1_vernacular']  = $request->old('address_1_vernacular');
   }else if(isset($object) && ($object)){
      $data['address_1_vernacular']  = $object['address_1_vernacular']; 
    }else{
      $data['address_1_vernacular']  = ''; 
    }


    if($request->old('address_2')){
      $data['address_2']  = $request->old('address_2');
   }else if(isset($object) && ($object)){
      $data['address_2']  = $object['address_2']; 
    }else{
      $data['address_2']  = ''; 
    }

    if($request->old('address_2_hindi')){
      $data['address_2_hindi']  = $request->old('address_2_hindi');
   }else if(isset($object) && ($object)){
      $data['address_2_hindi']  = $object['address_2_hindi']; 
    }else{
      $data['address_2_hindi']  = ''; 
    }

    if($request->old('district')){
      $data['district']  = $request->old('district');
   }else if(isset($object) && ($object)){
      $data['district']  = $object['district']; 
    }else{
      $data['district']  = ''; 
    }

    if($request->old('state')){
      $data['state']  = $request->old('state');
   }else if(isset($object) && ($object)){
      $data['state']  = $object['state']; 
    }else{
      $data['state']  = ''; 
    }

    if($request->old('ac')){
      $data['ac']  = $request->old('ac');
   }else if(isset($object) && ($object)){
      $data['ac']  = $object['ac']; 
    }else{
      $data['ac']  = ''; 
    }

    if($request->old('epic_no')){
      $data['epic_no']  = $request->old('epic_no');
   }else if(isset($object) && ($object)){
      $data['epic_no']  = $object['epic_no']; 
    }else{
      $data['epic_no']  = ''; 
    }

    if($request->old('serial_no')){
      $data['serial_no']  = $request->old('serial_no');
   }else if(isset($object) && ($object)){
      $data['serial_no']  = $object['serial_no']; 
    }else{
      $data['serial_no']  = ''; 
    }

    if($request->old('part_no')){
      $data['part_no']  = $request->old('part_no');
   }else if(isset($object) && ($object)){
      $data['part_no']  = $object['part_no']; 
    }else{
      $data['part_no']  = ''; 
    }

    return $data;
  }
}