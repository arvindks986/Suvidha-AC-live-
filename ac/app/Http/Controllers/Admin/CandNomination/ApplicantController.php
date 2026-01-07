<?php   
namespace App\Http\Controllers\Admin\CandNomination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\models\Nomination\ProfileModel;
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
use App\models\Admin\CandidatecriminalModel

use Illuminate\Support\Facades\Crypt;
use App\models\Admin\ApplicantsModel;
use App\models\Admin\Nomination\PreScrutiny\PreScrutinyModel;
use App\models\Nomination\NominationModel;
use App\models\Nomination\NominationApplicationModel;
use App\models\Nomination\NominationProposerModel;
use App\models\Nomination\NominationPoliceCaseModel;
use App\Http\Requests\Admin\Nomination\{NominationPart12Request, NominationPart3Request, NominationPart3aRequest};
use App\Helpers\LogNotification;

class ApplicantController extends Controller
{

    public $upload_folder   = '';
    public $base            = 'roac';
    public $folder  = 'CandNomination';
    public $action    = 'roac/';
    public $view_path = "admin.candform";
    public function __construct()
    {   
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->Applicants= new ApplicantsModel();
        $this->xssClean = new xssClean;
        if(!Auth::check()){ 
            return redirect('/officer-login');
        }
        $this->middleware(function ($request, $next) {
            $this->upload_folder = config("public_config.upload_folder");
            return $next($request);
        });

    }
      
    public function index(Request $request) {

        $data  = [];
        Session::forget('otp_mobile');
        $data['action']               = url('roac/nomination/apply-nomination-step-1/post');
        $data['nomination_page']      = url('roac/nomination/apply-nomination-step-2');
        $data['href_new_nomination']  = url('roac/nomination');
        $data['encrypt_id']           = '';
        $data['status_filter']        = $request->status;
        $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);

        $form_data      = $nom_nfd_form->get_form(0, $request, $data);

        $data = array_merge($form_data, $data);
        $data['nomination_id'] = 0;

        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);  
        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
        if($ele_details=='') 
        {
            \Session::flash('error_mes', 'Election has not assigned');
            return Redirect::to('/logout');
        }
        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
        if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
            $cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
        }
        $cand_status='all';
        $cand_status = $request->input('cand_status');
        $fil_status = $data['status_filter']; 
        $result=NominationApplicationModel::select('nomination_application.*', 'm_ac.DIST_NO_HDQTR')->where('nomination_application.st_code', $ele_details->ST_CODE)
        ->join('m_ac', [['nomination_application.st_code', '=', 'm_ac.ST_CODE'], ['nomination_application.ac_no', '=', 'm_ac.AC_NO']])
        ->where('election_id', $ele_details->ELECTION_ID)
        ->where('nomination_application.ac_no', $ele_details->CONST_NO)
        ->where('finalize', '=', '1')
        ->where('finalize_after_payment','1')
        ->where('application_type','2');
        if($fil_status == 'cleared'){
            $result->where('is_physical_verification_done', '=', '1');
        }elseif($fil_status == 'pending'){
            $result->where('is_physical_verification_done', '=', '0');
        }
        $results = $result->orderBy('id', 'asc')->get()->toarray();
        $data['user_data']=$d;
        $data['ele_details']=$ele_details;
        $data['results']=$results;  
        $data['heading_title']="List of All online Nomination"; 
        $data['cand_finalize_ro']=$cand_finalize_ro;  

        $filter = [
            'ele_details'=>$ele_details
        ];

        $data['application_count'] = [
			'total_application'		=> PreScrutinyModel::get_count_application(array_merge($filter, ['fil_status'=> ''])),
			'application_done' 	=> PreScrutinyModel::get_count_application(array_merge($filter, ['fil_status'=> '1'])),
			'application_pending'	=> PreScrutinyModel::get_count_application(array_merge($filter, ['fil_status'=> '2'])),
		];

        if ($request->has('is_export')) {
            if (isset($title_array) && count($title_array) > 0) {
                $data['heading_title'] .= "- " . implode(', ', $title_array);
            }
            return $data;
		}
        
        return view($this->view_path.'.list-all-applicant', $data);	           
}  // end index function

public function list_of_applicatiant_pdf(Request $request){
    $data = $this->index($request->merge(['is_export' => 1]));
    $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.list-all-applicant-pdf',$data);
    return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
}


public function is_nomination_exist($nom_no){
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);  
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
    $result=DB::table('nomination_application')
        // ->where('st_code', $ele_details->ST_CODE)
        // ->where('election_id', $ele_details->ELECTION_ID)
        // ->where('ac_no', $ele_details->CONST_NO)
        ->where('nomination_no', $nom_no)
        ->where('is_physical_verification_done', '0')
        ->get()->count();
        return ($result>0) ? false : true; 
}

public function qrscanfunction($qr='')
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


    $st= getstatebystatecode($d->st_code);
    $ac= getacbyacno($d->st_code,$d->ac_no); 

    $data['user_data']=$d;
    $data['ele_details']=$ele_details;
    $data['heading_title']="Nomination Form Qr Scan"; 
    $data['st_name']=$st->ST_NAME;
    $data['ac_no']=$ac->AC_NO;
    $data['ac_name']=$ac->AC_NAME;
    return view($this->view_path.'.qrscan', $data);	


}  // end  function  
public function Verifyqrcode(Request $request)
{
    $data  = [];
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);  

    $validator = Validator::make($request->all(), 
        [
            'qrcode' => 'required',
        ],
        [
            'qrcode.required' => 'Please enter Nomination No.', 
        ]);

    if ($validator->fails()) { 
        return redirect::back()
        ->withErrors($validator)
        ->withInput();
    }
    $qrcode =$this->xssClean->clean_input($request->input('qrcode'));

    $user_nomination =DB::table('nomination_application')
    ->where('nomination_no', $qrcode)->first();

    if(!$user_nomination){
        \Session::flash('error_mes', 'invalid  Nomination No.');
        return Redirect::back();
    }


    return Redirect::to('/roac/candidateinformation?nom_id='.encrypt_string($qrcode)); 

}  // end  function 

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
    // dd($nomination_no);
    if(!$user_nomination){
        \Session::flash('error_mes', 'invalid  Nomination No.');
        return Redirect::to('/roac/qrscan');  
    }

    $user_nomination = $user_nomination->toarray();
    $id = $user_nomination['id'];
    $data = NominationApplicationModel::get_nomination($user_nomination['id']);
    Session::put("otp_mobile", $user_nomination['mobile']);
    $data['action']               = url('roac/nomination/apply-nomination-step-1/post');
    $data['nomination_page']      = url('roac/nomination/apply-nomination-step-2');
    $data['href_new_nomination']  = url('roac/nomination');
    $data['ele_details']          = $ele_details;
    $data['encrypt_id']           = encrypt_string($user_nomination['id']);
    $nom_nfd_form   = new \App\Http\Controllers\Admin\Nfd\NominationController($request);
    $form_data      = $nom_nfd_form->get_form(0, $request, $data);
    $data = array_merge($form_data, $data);
    $data['nomination_id'] = $user_nomination['id'];
    $data['user_data'] = Auth::user();

	if($data['have_police_case']=='1'){
		$data['have_police_case']='yes';
	}
	if($data['have_police_case']=='2'){
		$data['have_police_case']='no';
	}
	if($data['have_police_case']=='0'){
		$data['have_police_case']='NA';
	}
	//
	if($data['profit_under_govt']=='1'){
		$data['profit_under_govt']='yes';
	}
	if($data['profit_under_govt']=='2'){
		$data['profit_under_govt']='no';
	}
	if($data['profit_under_govt']=='0'){
		$data['profit_under_govt']='NA';
	}
	//
	if($data['court_insolvent']=='1'){
		$data['court_insolvent']='yes';
	}
	if($data['court_insolvent']=='2'){
		$data['court_insolvent']='no';
	}
	if($data['court_insolvent']=='0'){
		$data['court_insolvent']='NA';
	}
	//
	if($data['allegiance_to_foreign_country']=='1'){
		$data['allegiance_to_foreign_country']='yes';
	}
	if($data['allegiance_to_foreign_country']=='2'){
		$data['allegiance_to_foreign_country']='no';
	}
	if($data['allegiance_to_foreign_country']=='0'){
		$data['allegiance_to_foreign_country']='NA';
	}
	//
	if($data['disqualified_section8A']=='1'){
		$data['disqualified_section8A']='yes';
	}
	if($data['disqualified_section8A']=='2'){
		$data['disqualified_section8A']='no';
	}
	if($data['disqualified_section8A']=='0'){
		$data['disqualified_section8A']='NA';
	}
	//
	if($data['disloyalty_status']=='1'){
		$data['disloyalty_status']='yes';
	}
	if($data['disloyalty_status']=='2'){
		$data['disloyalty_status']='no';
	}
	if($data['disloyalty_status']=='0'){
		$data['disloyalty_status']='NA';
	}
	//
	if($data['subsiting_gov_taken']=='1'){
		$data['subsiting_gov_taken']='yes';
	}
	if($data['subsiting_gov_taken']=='2'){
		$data['subsiting_gov_taken']='no';
	}
	if($data['subsiting_gov_taken']=='0'){
		$data['subsiting_gov_taken']='NA';
	}
	//
	if($data['managing_agent']=='1'){
		$data['managing_agent']='yes';
	}
	if($data['managing_agent']=='2'){
		$data['managing_agent']='no';
	}
	if($data['managing_agent']=='0'){
		$data['managing_agent']='NA';
	}
	//
	if($data['disqualified_by_comission_10Asec']=='1'){
		$data['disqualified_by_comission_10Asec']='yes';
	}
	if($data['disqualified_by_comission_10Asec']=='2'){
		$data['disqualified_by_comission_10Asec']='no';
	}
	if($data['disqualified_by_comission_10Asec']=='0'){
		$data['disqualified_by_comission_10Asec']='NA';
    }
    
	if($data['finalize']=='1'){
		$data['finalize']='yes';
	}
	if($data['finalize']=='2'){
		$data['finalize']='no';
	}
	if($data['finalize']=='0'){
		$data['finalize']='NA';
	}
	
	$data['party_id'] =   DB::connection('mysql')->table('m_party')->select('PARTYNAME')->where('CCODE', '=', $data['party_id'])->value('PARTYNAME'); 	
	
    $data['reference_id']               = $user_nomination['nomination_no'];
    $data['href_download_application']  = url("nomination/download/".encrypt_string($id));


    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Nomination Detail";
    $data['action'] = url('nomination/apply-nomination-finalize/post');
    
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
    if(isset($data['image'])){
    $data['profileimg']  = url($data['image']); 
	} else {
	$data['profileimg']  = 'NA'; 	
	}
	if(isset($data['qrcode'])){
    $data['qr_code']      = url($data['qrcode']);
	} else {
	$data['qr_code']  = 'NA'; 	
	}
    $data['apply_date']  = date('d/m/Y', strtotime($data['apply_date']));
    $data['non_recognized_proposers']   = NominationProposerModel::where([
        'candidate_id'  => $data['candidate_id'],
        'nomination_id' => $data['id'],
        'status'        => 1
      ])->orderBy('id', 'desc')->get()->toArray();
    $data['profile_data'] = ProfileModel::where('candidate_id', $data['candidate_id'])->first();
    $data['police_cases']               = NominationPoliceCaseModel::get_police_cases_ro($data['nomination_id']);
   
	if(isset($data['affidavit'])){
    $data['affidavit']      = url($data['affidavit']);
	} else {
	$data['affidavit']  = 'NA'; 	
    }

    if($user_nomination['recognized_party'] == '1'){
        $party=$this->commonModel->getparty($user_nomination['party_id']); 
    }elseif($user_nomination['recognized_party'] == '2'){
        $party=$this->commonModel->getparty($user_nomination['party_id2']); 
    }else{
        $party=$this->commonModel->getparty($user_nomination['party_id']); 
    }

    if($party->PARTYSYM!=0) 
        $symbol_id=$party->PARTYSYM;
    else
        $symbol_id="200";
    $data['cand_party']  = $party;
    $data['symbol_id']  =  $symbol_id;
    
    if(!empty($user_nomination['assigned_e_affidavit'])){
        $affidavit_details = DB::table('aff_cand_details')->where('affidavit_id', $user_nomination['assigned_e_affidavit'])->first();
        $data['affidavitId'] = $affidavit_details->id;
    }else{
        $data['affidavitId'] = '';
    }
    $data['pan_number'] = $this->decrypt_pan($data['pan_number']);
    return view($this->view_path.'.candidateinformationac', $data);	  
}  // end  function  

protected function decrypt_pan($ency_value){
    // For Decrypt pan number
    try{
        $encoded = $ency_value;
		$decoded = "";
		 for( $i = 0; $i < strlen($encoded); $i++ ) {
			$b = ord($encoded[$i]);
			$a = $b ^ 123;
			$decoded .= chr($a);
		}
	      return $data['pan_number'] = $decoded;
    }catch(\Exception $e){
          return  $data['pan_number'] = '';
    }
}

public function getpaymentStatus($nid, $candid){
	
    $nd =  DB::table('nomination_application')
   ->select('st_code', 'ac_no')
   ->whereIn('id', array($nid))
   ->get();

   $payment_type = 'Online';
   $payment_opt_type = '';

   $detail =  DB::table('payment_details_common')
   ->select('*')
   ->where('candidate_id', '=', $candid)
   ->where('st_code', '=', $nd[0]->st_code)
   ->where('bank_transaction_status', '=', '1')
   ->get();
    
   if(count($detail)=='0'){
    $detail = DB::table('challan_payment')
    ->where('candidate_id', '=', $candid)
    ->where('st_code', '=', $nd[0]->st_code)
    ->where('challan_receipt', '!=', '')
    ->where('payByCash', '0')
    ->get();
    $payment_type = 'Challan';
   }

    if(count($detail)=='0'){
        $detail = DB::table('challan_payment')
        ->where('candidate_id', '=', $candid)
        ->where('st_code', '=', $nd[0]->st_code)
        ->where('payByCash', '1')
        ->where('pay_by_cash_paid', '1')
        ->get();

        $payment_opt = DB::table('challan_payment')
        ->where('candidate_id', '=', $candid)
        ->where('st_code', '=', $nd[0]->st_code)
        ->where('payByCash', '1')
        ->where('pay_by_cash_paid', '0')
        ->get();

        if(count($payment_opt)>0){
            $payment_opt_type = 'Pay By Cash';
        }

        if(count($detail)>0){ 
            $payment_type = 'Pay By Cash Paid';
        }else{
            $payment_type = 'Pending';
        }
    }


   return [
       'payment_type'   => $payment_type,
       'payment_opt_ro' => $payment_opt_type,
       'payment_detail' => count($detail)>0 ? $detail : []
   ];
  } 

public function candidatevalidation(Request $request)
{
    $data  = [];
    $user = Auth::user();


        $message=array();
        $message['MobNo']= $user->officername ?? '';
        $message['State_ID']= $user->st_code ?? '';
        $message['applicationType']= 'WebApp';
        $message['Module']= 'ENCORE';
        $message['TransectionType']= 'Physical Verification';
        $message['TransectionAction']= 'Physical Verification Receipt Generated';

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

    $user_nomination_profile = DB::table('profile')->where('candidate_id', $user_nomination['candidate_id'])->first();

    if(!$user_nomination){
        \Session::flash('error_mes', 'invalid  Nomination No.');
        return Redirect::to('/roac/qrscan');  
    }
    $data['police_cases']  = NominationPoliceCaseModel::where('nomination_id', $user_nomination['id'])
    ->where('candidate_id',$user_nomination['candidate_id'])
    ->where('is_deleted', '0')
    ->get()->toarray();  

    $nomshares = DB::table('candidate_nomination_detail') 
    ->where('st_code', $ele_details->ST_CODE)
    ->where('ac_no', $ele_details->CONST_NO)
    ->where('old_candidate_id', $oldcandidate_id)
    ->where('old_nom_id', $oldnom_id)
    ->where('nomination_no', $nomination_no)
    ->first();
    $candshares = DB::table('candidate_personal_detail') 
    ->where('old_candidate_id', $oldcandidate_id)->first(); 

    if($user_nomination['recognized_party'] == '1'){
        $party=$this->commonModel->getparty($user_nomination['party_id']); 
    }elseif($user_nomination['recognized_party'] == '2'){
        $party=$this->commonModel->getparty($user_nomination['party_id2']); 
    }else{
        $party=$this->commonModel->getparty($user_nomination['party_id']); 
    }
    
    if($party->PARTYSYM > 0) 
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
    if($user_nomination['have_police_case']=='1')
    {
        $is_criminal='1';
    }else{
        $is_criminal='0';
    }

    $non_recognized_proposers =NominationProposerModel::where('nomination_id', $user_nomination['id'])
    ->where('candidate_id',$user_nomination['candidate_id'])
    ->where('status', '1')
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
        'cand_email'=>!empty($user_nomination['email']) ? $user_nomination['email'] : '',
        'cand_mobile'=>$user_nomination['mobile'],
        'cand_gender'=>$user_nomination['gender'],
        'candidate_residence_address'=>$user_nomination_profile->address,
        'candidate_residence_addressh'=>$user_nomination_profile->haddress,
        'candidate_residence_addressv'=>$user_nomination_profile->vaddress,
        'candidate_residence_stcode'=>$user_nomination_profile->state,
        'candidate_residence_districtno'=>$user_nomination_profile->district,
        'candidate_residence_acno'=>$user_nomination_profile->ac,
        // 'candidate_residence_address'=>$user_nomination['address'],
        // 'candidate_residence_addressh'=>$user_nomination['haddress'],
        // 'candidate_residence_addressv'=>$user_nomination['vaddress'],
        // 'candidate_residence_stcode'=>$user_nomination['home_st_code'],
        // 'candidate_residence_districtno'=>$user_nomination['home_dist_no'],
        // 'candidate_residence_acno'=>$user_nomination['home_ac_no'],
        // 'cand_category'=>$user_nomination['category'],
        // 'cand_age'=>$user_nomination['age'],
        // 'cand_panno'=>$user_nomination['pan_number'],
        'cand_category'=>$user_nomination_profile->category,
        'cand_age'=>$user_nomination_profile->age,
        'cand_panno'=>$this->decrypt_pan($user_nomination_profile->pan_number),
        'cand_qualification'=>'',
        'cand_cast'=>'',
        'cand_cast_state'=>$user_nomination['part3_cast_state'],
        'cand_cast_area'=>$user_nomination['part3_address'],
        'cand_dob'=>$user_nomination['dob'],
        'cand_image'=>$user_nomination['image'],
        'cand_apply_date'=>$user_nomination['apply_date'],
        'cand_nickname'=>$user_nomination['alias_name'],
        'cand_epic_no'=>$user_nomination['have_police_case'],
        'is_criminal'=>''.$is_criminal.'',
        'name_of_language'=>$user_nomination['language'],
        'election_id'=>$ele_details->ELECTION_ID,
        'old_candidate_id'=>$oldcandidate_id,
        'created_by'=>$d->officername,
        'created_at'=>date('Y-m-d H:i:s'),
        'added_create_at'=>date('Y-m-d'),
         'candidate_residance_part_no'=>$user_nomination['part'],
        'candidate_residance_serial_no'=>$user_nomination['serial'],
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
    if($user_nomination['recognized_party'] == '1'){
        $party=$user_nomination['party_id']; 
    }elseif($user_nomination['recognized_party'] == '2'){
        $party=$user_nomination['party_id2']; 
    }else{
        $party=$user_nomination['party_id']; 
    }

    $candNomData = array(
        'election_id'=>$ele_details->ELECTION_ID,
        'party_id'=>$party,
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
        $nom_id=$nomshares->nom_id;
    }
/*    if($user_nomination['affidavit']!='') {
    $getAffidavitDetails = getById('candidate_affidavit_detail','nom_id',$nom_id); 
    $affidavitName = "Affidavite Form 26";
    if(!empty($getAffidavitDetails) ){
        $updateNomDetail = DB::update('update candidate_affidavit_detail set affidavit_path ="'.$user_nomination['affidavit'].'" where nom_id = ' .$nom_id);

    }else{
        DB::table('candidate_affidavit_detail')->insert([
            ['candidate_id' => $cid, 
            'nom_id' => $nom_id, 
            'affidavit_name' => $affidavitName, 
            // 'affidavit_path' =>$user_nomination['affidavit'],
            'affidavit_path' =>'', 
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
} */ // end affidavite
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




  
    // Insert criminal Antecedents  in candidate_criminalupload





$updateiscriminal_field = DB::update('update candidate_personal_detail set is_criminal ="' . $request->input('is_criminal') . '" where candidate_id = ' . $cid);

         if (request()->hasFile('affidavit')) {

            //Move Uploaded File
            $newfile = $ele_details->ST_CODE . '_' . $cid . '_' . date('Ymdhis');

            $fileNewName = $newfile . '.' . $request->file('affidavit')->getClientOriginalExtension();
            
            //edited by waseem paste it before move function
            if (!validate_pdf_file($request->file('affidavit'))) {
                 
              \Session::flash('error_mes', 'Only Pdf File uploaded');
              return Redirect::back()->withInput($request->all());
            }else{
                  
            }


            //end by waseem   
            $destinationPath = 'uploads4/criminaluploads/E' . $ele_details->ELECTION_ID . '/' . $ele_details->ST_CODE . '/' . $ele_details->CONST_NO;
            $file = $request->file('affidavit');
            $file->move($destinationPath, $fileNewName);
//dd($request->files,"bbbb");
            $affidavitName = "Criminal Affidavit";
            $affidavit_path = $destinationPath . '/' . $fileNewName;
            if (!file_exists($affidavit_path)) {
              \Session::flash('error_mes', 'File is not uploaded. Please try again.');
              return Redirect::back()->withInput($request->all());
            }
            $getAffidavitDetails = getById('candidate_criminaluploads', 'candidate_id', $cid);

            if (isset($getAffidavitDetails)  and ($getAffidavitDetails)) {
              if ($request->file('affidavit') != '') {

                $updateNomDetail = DB::update('update candidate_criminaluploads set path ="' . $affidavit_path . '" where candidate_id = ' . $cid);
              }
            } else {
              $insData = array(
                'election_id' => $ele_details->ELECTION_ID,
                'candidate_id' => $cid,
                'nom_id' => $nom_id,
                'name' => $affidavitName,
                'path' => $affidavit_path,
                'st_code' => $ele_details->ST_CODE,
                'ac_no' => $ele_details->CONST_NO,
                'created_by' => $d->officername,
                'created_at' => date('Y-m-d H:i:s'),
                'added_create_at' => date('Y-m-d'),

              );
              CandidatecriminalModel::create($insData);
            }
          }









    // End Section for criminal Antecedents

















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


                if(config('public_config.nomination_log'))
                    {
                  $message['LogDescription']= 'Nomination Num: '.$nomination_no ;
                  $message['TransectionStatus']= 'SUCCESS';

                LogNotification::LogInfo($message);
                 }



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
                $temp_nom_data = NominationApplicationModel::where('nomination_no', $shares->nomination_no)->first();
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
                $ro_scaned_time = DB::table('nomination_ro_submit_log')->where('nomination_no', $shares->nomination_no)->latest()->first();
                $data['ro_scaned_time'] = !empty($ro_scaned_time) ? $ro_scaned_time : '';
                $tem_data  = NominationProposerModel::where([
                    'candidate_id' => $temp_nom_data->candidate_id,
                    'nomination_id' => $temp_nom_data->id,
                    'status'        => '1'
                  ])->orderBy('id', 'desc')->get()->toArray();
                $data['non_recognized_proposers'] = array_filter($tem_data, function($key){
                    if(!empty($key['fullname'])){
                        return $key;
                    }
                });
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

    return view($this->view_path.'.finalreceipt', $data); 
}  // end  function  print_receipt


public function decisionvalidatel(Request $request) {
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $input = $request->all();  //dd($input);
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
// $response = SmsgatewayHelper::sendOtpSMS($Mob_otp,$cand->cand_mobile); 

    ##### Physical Verification Done Status####
    NominationApplicationModel::where('nomination_no', $shares->nomination_no)->update([
        'is_physical_verification_done' => '1'
    ]);
    ##### End Status Update #####

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
    $is_direct_print = $request->direct_print;
    $nom_id = $this->xssClean->clean_input($request->nom_id);
    if(empty($is_direct_print)){
        $shares=$this->Applicants->nominationinformation($nom_id);
    }else{
        $dcrypt_id = decrypt_string($nom_id);
        $shares=$this->Applicants->newnominationinformation($dcrypt_id);
    }
    
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

public function nomination_receipt_print_hindi(Request $request)
{   
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $is_direct_print = $request->direct_print;
    $nom_id = $this->xssClean->clean_input($request->nom_id);
    if(empty($is_direct_print)){
        $shares=$this->Applicants->nominationinformation($nom_id);
    }else{
        $dcrypt_id = decrypt_string($nom_id);
        $shares=$this->Applicants->newnominationinformation($dcrypt_id);
    }
    
    $ac=getacbyacno($shares->st_code,$shares->ac_no);
    $state=getstatebystatecode($shares->st_code);

    $data['user_data']=$d;
    $data['caddata']=$shares;
    $data['heading_title']="Print Receipt"; 
    $data['state']=$state;
    $data['ac']=$ac;
    $data['st_name']=$state->ST_NAME_HI;
    $data['ac_name']=$ac->AC_NAME_HI;
    $data['ac_no']=$ac->AC_NO; 
    $name_excel = 'printreceipt_hindi-'.$shares->st_code."-ac_no-".$shares->ac_no.'-'.time();
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
    $pdf = \MPDF::loadView($this->view_path.'.downloadprintreceipt_hindi',$data,[], $setting_pdf);
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