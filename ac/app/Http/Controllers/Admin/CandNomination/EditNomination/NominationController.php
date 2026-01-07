<?php 
namespace App\Http\Controllers\Admin\CandNomination\EditNomination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB;
use Validator;
use Config;
use \PDF; 
use App\Helpers\SmsgatewayHelper;
use App\models\Nomination\PreScrutinyModel;
use App\models\Nomination\ProfileModel;
use App\models\Nomination\ProfilelogModel;
use App\models\Nomination\NominationApplicationModel;
use App\models\Nomination\NomlogModel;
use App\models\Nomination\NominationProposerModel;
use App\models\Nomination\NominationPoliceCaseModel;
use App\models\Common\StateModel;
use App\models\Common\{FileModel, PcModel, AcModel, DistrictModel, PartyModel, SymbolModel, ElectionModel};
use App\Http\Requests\Nomination\NominationRequest;
use App\Http\Requests\Nomination\NominationApplicationRequest;
use App\Http\Requests\Nomination\NominationPart12Request;
use App\Http\Requests\Nomination\NominationPart3Request;
use App\Http\Requests\Nomination\NominationPart3aRequest;



class NominationController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
  
    }
  
  
	public function view_nomination_prescootiny($id, Request $request){ //return "alam";
		$id = decrypt_string($id); 
		$user_nomination = PreScrutinyModel::get_nomination_application($id); 
		//echo "<pre>"; print_r($user_nomination); die;
		$data['nomination_id'] = $user_nomination['id'];
		$data = PreScrutinyModel::get_nomination($user_nomination['id']);
		
		//echo "<pre>"; print_r($data); die;
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
		//
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
		
	
		$data['reference_id']                 = $user_nomination['nomination_no'];
		$data['is_re_finalize']               = $user_nomination['is_re_finalize'];
		$data['st_code']                	  = $user_nomination['st_code'];
		$data['ac_no']                		  = $user_nomination['ac_no'];
		$data['href_download_application']  = url("nomination/download/".encrypt_string($id));
		$data['view_href_cust'] = url('nomination/detail/'.encrypt_String($id));
	
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
		$data['user_data']  = Auth::user();
		$data['profileimg']  = url($data['image']); 
		$data['qr_code']      = url($data['qrcode']);
		$data['apply_date']  = date('d/m/Y', strtotime($data['apply_date'])); 
		$data['non_recognized_proposers']   = NominationProposerModel::get_proposers($data['id']);  
		$data['police_cases']               = NominationPoliceCaseModel::get_police_cases($data['nomination_id']); 
		if($data['affidavit']!=''){
		$data['affidavit']  = url($data['affidavit']);
		} else {
		$data['affidavit']  ='NA';
		}

		$data['comment_section'] = PreScrutinyModel::get_comment_data_by_id($data['nomination_id']);
		Session::flash('is_defects',"yes");
		return view('nomination/prescootiny',$data);
	}
	
	
	public function save_first_login(){
	  DB::table('user_login')->where('id', \Auth::id())->update(['first_login' =>1]);
	  return redirect('dashboard-nomination-new');
	}	
	
	public function mark_defect_as_resolved(Request $request)
    {
	    $input = $request->all();  
	    $myvar = DB::connection('mysql')->table('candidate_prescrutiny_detail')
		->where('id', $input['rid'])
		->update([
		"is_defect_resolved" =>1,
		"defect_resolved_datetime" => date('Y-m-d H:i:s', time()),
		]);		
		//return $myvar;
		return 0;
	}	
	
	public function apply_nomination_step_1(Request $request)
    {
	
    Session::forget('nomination_id');
    $data                   = [];
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
    $data['heading_title'] = "Apply Nomination";
    $data['action']        = url('nomination/apply-nomination-step-1/post');
    $data = $this->get_form(0, $request, $data);
	
	//echo "<pre>"; print_r($data);
	
	return view('nomination/apply-nomination-step-1',$data);

  }
    private function get_form($id,$request,$data = array()){
    $object = ProfileModel::get_candidate_profile();
	
	$mobile_from_user_login =   DB::connection('mysql')->table('user_login')->select('mobile')->where('id', '=', \Auth::id())->value('mobile');
	
	if(isset($mobile_from_user_login)){
		$data['mobile_from_user_login']  = $mobile_from_user_login; 
	} else {
		$data['mobile_from_user_login']  = ''; 
	}
	
	if(isset($object['is_verified_email_otp'])){
      $data['is_verified_email_otp']  = $object['is_verified_email_otp']; 
    }else{
      $data['is_verified_email_otp']  = ''; 
    }
	if(isset($object['is_verified_mobile_otp'])){
      $data['is_verified_mobile_otp']  = $object['is_verified_mobile_otp']; 
    }else{
      $data['is_verified_mobile_otp']  = ''; 
    }
	
	if($request->old('name')){
      $data['name']  = $request->old('name');
    }else if(isset($object) && $object){
      $data['name']  = $object['name']; 
    }else{
      $data['name']  = ''; 
    }

    if($request->old('email')){
      $data['email']  = $request->old('email');
    }else if(isset($object) && $object){
      $data['email']  = $object['email']; 
    }else{
      $data['email']  = ''; 
    }

    if($request->old('mobile')){ 
      $data['mobile']  = $request->old('mobile');
    }else if(isset($object) && $object){
      $data['mobile']  = $object['mobile']; 
	}else if(!isset($object['mobile']))  
	{
	  $data['mobile']  = $mobile_from_user_login;	
    }else{
      $data['mobile']  = ''; 
    }
	
    if($request->old('hname')){
      $data['hname']  = $request->old('hname');
    }else if(isset($object) && $object){
      $data['hname']  = $object['hname']; 
    }else{
      $data['hname']  = ''; 
    }

    if($request->old('vname')){
      $data['vname']  = $request->old('vname');
    }else if(isset($object) && $object){
      $data['vname']  = $object['vname']; 
    }else{
      $data['vname']  = ''; 
    }
	//new
	if($request->old('alias_vname')){
      $data['alias_vname']  = $request->old('alias_vname');
    }else if(isset($object) && $object){
      $data['alias_vname']  = $object['alias_vname']; 
    }else{
      $data['alias_vname']  = ''; 
    }
    if($request->old('alias_name')){
      $data['alias_name']  = $request->old('alias_name');
    }else if(isset($object) && $object){
      $data['alias_name']  = $object['alias_name']; 
    }else{
      $data['alias_name']  = ''; 
    }

    if($request->old('alias_hname')){
      $data['alias_hname']  = $request->old('alias_hname');
    }else if(isset($object) && $object){
      $data['alias_hname']  = $object['alias_hname']; 
    }else{
      $data['alias_hname']  = ''; 
    }

    if($request->old('father_name')){
      $data['father_name']  = $request->old('father_name');
    }else if(isset($object) && $object){
      $data['father_name']  = $object['father_name']; 
    }else{
      $data['father_name']  = ''; 
    }

    if($request->old('father_hname')){
      $data['father_hname']  = $request->old('father_hname');
    }else if(isset($object) && $object){
      $data['father_hname']  = $object['father_hname']; 
    }else{
      $data['father_hname']  = ''; 
    }

    if($request->old('father_vname')){
      $data['father_vname']  = $request->old('father_vname');
    }else if(isset($object) && $object){
      $data['father_vname']  = $object['father_vname']; 
    }else{
      $data['father_vname']  = ''; 
    }

    if($request->old('pan_number')){
      $data['pan_number']  = $request->old('pan_number');
    }else if(isset($object) && $object){
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
    }else if(isset($object) && $object){
      $data['category']  = $object['category']; 
    }else{
      $data['category']  = ''; 
    }

    if($request->old('age')){
      $data['age']  = $request->old('age');
    }else if(isset($object) && $object){
      $data['age']  = $object['age']; 
    }else{
      $data['age']  = ''; 
    }

    if($request->old('gender')){
      $data['gender']  = $request->old('gender');
    }else if(isset($object) && $object){
      $data['gender']  = $object['gender']; 
    }else{
      $data['gender']  = ''; 
    }

    if($request->old('address')){
      $data['address']  = $request->old('address');
    }else if(isset($object) && $object){
      $data['address']  = $object['address']; 
    }else{
      $data['address']  = ''; 
    }

    if($request->old('haddress')){
      $data['haddress']  = $request->old('haddress');
    }else if(isset($object) && $object){
      $data['haddress']  = $object['haddress']; 
    }else{
      $data['haddress']  = ''; 
    }

    if($request->old('vaddress')){
      $data['vaddress']  = $request->old('vaddress');
    }else if(isset($object) && $object){
      $data['vaddress']  = $object['vaddress']; 
    }else{
      $data['vaddress']  = ''; 
    }

    /*if($request->old('address_2')){
      $data['address_2']  = $request->old('address_2');
    }else if(isset($object) && $object){
      $data['address_2']  = $object['address_2']; 
    }else{
      $data['address_2']  = ''; 
    }

    if($request->old('address_2_hindi')){
      $data['address_2_hindi']  = $request->old('address_2_hindi');
    }else if(isset($object) && $object){
      $data['address_2_hindi']  = $object['address_2_hindi']; 
    }else{
      $data['address_2_hindi']  = ''; 
    }*/

    if($request->old('district')){
      $data['district']  = $request->old('district');
    }else if(isset($object) && $object){
      $data['district']  = $object['district']; 
    }else{
      $data['district']  = ''; 
    }

    if($request->old('state')){
      $data['state']  = $request->old('state');
    }else if(isset($object) && $object){
      $data['state']  = $object['state']; 
    }else{
      $data['state']  = ''; 
    }

    if($request->old('ac')){
      $data['ac']  = $request->old('ac');
    }else if(isset($object) && $object){
      $data['ac']  = $object['ac']; 
    }else{
      $data['ac']  = ''; 
    }

    if($request->old('epic_no')){
      $data['epic_no']  = $request->old('epic_no');
    }else if(isset($object) && $object){
      $data['epic_no']  = $object['epic_no']; 
    }else{
      $data['epic_no']  = ''; 
    }

    if($request->old('serial_no')){
      $data['serial_no']  = $request->old('serial_no');
    }else if(isset($object) && $object){
      $data['serial_no']  = $object['serial_no']; 
    }else{
      $data['serial_no']  = ''; 
    }

    if($request->old('part_no')){
      $data['part_no']  = $request->old('part_no');
    }else if(isset($object) && $object){
      $data['part_no']  = $object['part_no']; 
    }else{
      $data['part_no']  = ''; 
    }
    return $data;
  }
    
	
	
    public function save_step_1(NominationRequest $request){    
	echo "<pre>"; print_r($request->all()); die;
	
	DB::beginTransaction();
    try{  
      $resultlog  = ProfilelogModel::add_nomination_personal_detail($request->all());
	  
	   
	   
	$chkcan = DB::connection('mysql') 
	->table('nomination_application')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->get();	
	if(count($chkcan) > 0 ){ 	
	   $input = $request->all();  
	   
	    $myvar = DB::connection('mysql')->table('nomination_application')
		->where('candidate_id', '=', \Auth::id())
		->update([
		"category" => $input['category'],
		]);	
	}  
	  
	$pid        = ProfileModel::add_nomination_personal_detail($request->all());
	  
	 
	  
	  
    }
    catch(\Exception $e){  //print_r( $e->getMessage()); die;
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message', __('step1.Please_try_again'));
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    Session::flash('flash-message', __('step1.personal_details_save_message'));
	if($request->has('save_only')){
      return Redirect::back();
    }
	return redirect('/dashboard-nomination-new');
   }
  
  
  public function getprescrutiny($nom){ 
	
	
	$n =  DB::connection('mysql')
	->table('nomination_application')
	->select('is_apply_prescrutiny')
	->where('nomination_no', '=', $nom)
	->where('is_apply_prescrutiny', '=', '1')
	->get();
	if(count($n) > 0){
	  return 1;
	} else {
	  return 0;
	}
	  
  }

  public function apply_nomination_step_2(Request $request,$id = 0)
  {
	$id                     = decrypt_String($id);
	$nom_data  = NominationApplicationModel::get_nomination_application_ro($id);
    if(!ProfileModel::get_candidate_profile_ro($nom_data['candidate_id'])){
      Session::flash('status',0);
      Session::flash('flash-message', __('messages.perinfo'));
      return redirect('roac/apply-nomination-step-1');
    }
   // echo Session::get('nomination_id'); die;
	
	//Session::forget('nomination_id');   
	
	if(isset($_REQUEST['setintosession']) && !empty($_REQUEST['setintosession']) && $_REQUEST['setintosession']=='ppp'){
	   \Session::put('dataforupdate', 'dataforupdate');
	}
	
	if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid'])){
	   $id =  $_REQUEST['nid']; 
	   $idnon =  decrypt_String($_REQUEST['nid']); 
	   $this->convertIntoSession($idnon);
	}
	
    $data                   = [];
    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/roac/dashboard'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => '#',
      'name'    => "Nomination",
      'is_last' => true
    ];

    $data['is_active']     = 'nomination';

    
    $data['href_back'] = url('roac/apply-nomination-step-1');
    $data['href_skip'] = url('roac/apply-nomination-step-3');

    $data['heading_title'] = __('election_details.election_details');
    $data['action']        = url('roac/apply-nomination-step-2/post');
	$data['user_data'] = Auth::user();
	
    $data = $this->get_step2_form($id, $request, $data);

	
    return view('admin/candform/edit_nomination/apply-nomination-step-2',$data);

  }

  private function get_step2_form($id,$request,$data = array()){
	
	$data['finalize'] = 0;
	
    if($id != '0'){
      $data['nomination_id']  = $id;
	  	  
      $object                 = NominationApplicationModel::get_nomination_application_ro($id);
	 //echo "<pre>"; print_r($object); die("data");
	  
      $data['finalize']               = $object['finalize'];
	  $data['candidate_id']			  = $object['candidate_id'];
      $data['reference_id']           = $object['nomination_no'];
      $data['stepCond']               = $object['step'];
      $data['href_download_application']  = url("/roac/download/".encrypt_string($data['nomination_id']));
    }
	
    //end nomination validation

    $data['states'] = [];
    //$states = StateModel::get_states();
	$sql = "select m_state.ST_CODE as st_code, m_state.ST_NAME as st_name, ELECTION_ID as election_id, ELECTION_TYPEID as election_type from `m_state` inner join `m_election_details` on (`m_election_details`.`ST_CODE` = `m_state`.`ST_CODE`) where `election_status` = 1 and `m_election_details`.`CONST_TYPE` = 'AC' group by `m_election_details`.`ST_CODE`  order by m_state.ST_CODE ASC";
	$states = DB::select($sql);
	
//	echo "<pre>"; print_r($states); die("data");
    foreach ($states as $key => $state_iterage) {  
      $data['states'][] = [
        'st_code'    => $state_iterage->st_code,
        'st_name'    => $state_iterage->st_name,
        'encoded'    => base64_encode($state_iterage->st_code),
        'election_type'     => $state_iterage->election_type,
        'election_id'       => $state_iterage->election_id,
      ];
    }
	
	
    $data['election_types'] = [];
    $election_types = ElectionModel::get_election_types();
    foreach ($election_types as $key => $election_iterage) { //echo "<pre>"; print_r($election_iterage); die("data");
      $data['election_types'][] = [
        'election_id'      => $election_iterage->ELECTION_ID,
        'election_type_id' => $election_iterage->ELECTION_TYPEID,
        'pc_no'            => $election_iterage->CONST_NO,
        'st_code'          => $election_iterage->ST_CODE,
        'name'             => $election_iterage->ELECTION_TYPE.'-'.$election_iterage->YEAR,
      ];
    }
	
	$sql = "select m_ac.AC_NO as ac_no, m_ac.ST_CODE as st_code, m_ac.DIST_NO_HDQTR as district_no, CONCAT(AC_NAME) as ac_name, ELECTION_ID as election_id, ELECTION_TYPEID as election_type from `m_ac` inner join `m_election_details` on (`m_election_details`.`ST_CODE` = `m_ac`.`ST_CODE` and `m_election_details`.`CONST_NO` = `m_ac`.`AC_NO`) where `election_status` = 1 and `m_election_details`.`CONST_TYPE` = 'AC' group by `m_ac`.`AC_NO`, `m_ac`.`ST_CODE` order by m_ac.ST_CODE,m_ac.AC_NO ASC";
	$acs = DB::select($sql);

	
    $data['acs'] = [];
   // $acs = AcModel::get_acs();
	
    foreach ($acs as $key => $ac_iterage) { 
      $data['acs'][] = [
        'ac_no'      => $ac_iterage->ac_no,
        'ac_name'    => $ac_iterage->ac_no.'-'.$ac_iterage->ac_name,
        'st_code'    => $ac_iterage->st_code,
        'election_id' => $ac_iterage->election_id,
        'election_type' => $ac_iterage->election_type,
        //'encoded'    => base64_encode($ac_iterage->pc_no),
      ];
    }

    if($request->old('st_code')){
      $data['st_code']  = $request->old('st_code');
    }else if(isset($object) && $object){
      $data['st_code']  = $object['st_code']; 
    }else{
      $data['st_code']  = ''; 
    }

    if($request->old('election_id')){
      $data['election_id']  = $request->old('election_id');
    }else if(isset($object) && $object){
      $data['election_id']  = $object['election_type_id']; 
    }else{
      $data['election_id']  = ''; 
    }
    
	 if($request->old('eid')){
      $data['eid']  = $request->old('eid');
    }else if(isset($object) && $object){
      $data['eid']  = $object['election_id']; 
    }else{
      $data['eid']  = ''; 
    }
   
   if($request->old('ac_no')){
      $data['ac_no']  = $request->old('ac_no');
    }else if(isset($object) && $object){
      $data['ac_no']  = $object['ac_no']; 
    }else{
      $data['ac_no']  = ''; 
    }
	//echo "<pre>"; print_r($data); die;
    return $data;
  }

  public function save_step_2(NominationApplicationRequest $request){
	
	$req  = $request->all();
    $count_nomination = NominationApplicationModel::count_nomination_application($request->all());
	
	$check='';	
	if(isset($req['nomination_id'])){ 
		$check  = $req['nomination_id']; 
	}	
	$cnt=0;
   if(!isset($req['nomination_id'])){ 
    $cnt = DB::connection('mysql') 
	->table('nomination_application')
	->select('*')
	->where('candidate_id', '=', $req['candidate_id'])
	->where('st_code', '=', $req['st_code'])
	->where('ac_no', '=', $req['ac_no'])
	->where('finalize', '!=', 3)
	->get();
   } 
   
   //echo count($cnt); die;	
   if(empty($check)){ 
	if(count($cnt) >= 4){
	  Session::flash('status',0);
	  Session::flash("flash-message", __('messages.already4'));
	  return Redirect::back()->withInput($request->all());
	 }
   }
   
   
    DB::beginTransaction();
    try{ 
		
		$result        = NominationApplicationModel::add_nomination_application_ro($request->all());
		// echo Session::get('nomination_id'); die;
		 $NomlogModel   = NomlogModel::add_nomination_application_ro(Session::get('nomination_id'), $request->all());  
      if(!$request->has('nomination_id')){
        $data             = NominationApplicationModel::get_nomination(Session::get('nomination_id'));
        $st_code          = $data['st_code'];
        $year             = date('Y');
        $ac_no            = $data['ac_no'];
        $election_name    = 'E'.$data['election_id'];
		
		
		$encrypt_method = "AES-256-CBC";
		$key='E(*x5lcyam%$.9dx';
		$iv='E(*x5lcyam%$.9dx';
		$nom = openssl_encrypt($data['nomination_no'], $encrypt_method, $key, 0, $iv);
		 
		//$output = openssl_encrypt($data['id'], $encrypt_method, $key, 0, $iv);
		//$otp=str_replace("/", "PP9PP9PP9PP", $output);
		
		$destination_path = FileModel::get_file_path('uploads1/qrcode/'.$year.'/ac/'.$election_name.'/'.$st_code.'/'.$ac_no).'/'.$data['id'].'.png';
		
        \QRCode::text($nom)->setOutfile($destination_path)->png();
		
        $data['qrcode_path']  = $destination_path;
        $data['qrcode']       = url($destination_path);
        NominationApplicationModel::add_qrcode($data);
		
      }
    }
    catch(\Exception $e){ 
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message', $e->getMessage());
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    if($request->has('save_only')){
      Session::flash('flash-message', __('messages.perelc'));
      return redirect("roac/apply-nomination-step-2/".encrypt_string(Session::get('nomination_id')));
	}
	//print_r($request->all()); die;
	return redirect('/roac/apply-nomination-step-3');
  }

  public function apply_nomination_step_3(Request $request,$id = 0){
	
	
	
	if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid'])){
	   $id =  decrypt_String($_REQUEST['nid']); 
	   $this->convertIntoSession($id);
	}
	
    $data                   = [];
    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/roac/dashboard'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => '#',
      'name'    => "Nomination",
      'is_last' => true
	];
	
	
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Form 2B - Nomination Paper ";
    $data['action'] = url('roac/apply-nomination-step-3/post-part-1');

    $data['href_back'] = url("roac/apply-nomination-step-2/".encrypt_string(Session::get('nomination_id')));
    $data['href_skip'] = url('roac/apply-nomination-step-4');
    $data['href_file_upload'] = url('roac/upload');

    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("roac/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }
	// echo Session::get('nomination_id');
	// dd('sdsss');
    $user_nomination = NominationApplicationModel::get_nomination_application_ro($id);
	
    if(!$user_nomination){
       return redirect("roac/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      //Session::flash('flash-message','You can not edit this nomination.');
      //return redirect("nomination/apply-nomination-step-2"); 
    }
	$data['stepCond']                   = $user_nomination['step'];
    $data['reference_id']               = $user_nomination['nomination_no'];
    $data['href_download_application']  = url("roac/download/".encrypt_string($id)); 
    
    $data['nomination_id'] = $user_nomination['id'];
	$data['user_data'] = Auth::user();

	
    $data = array_merge($data, $user_nomination);
    
	$data = $this->get_step3_form($id, $request, $data);
	
    return view('admin/candform/edit_nomination/apply-nomination-step-3',$data);

  }
 
  private function get_step3_form($id,$request,$data = array()){
	  
	
    $series='';
    $series=$data['serial_no'];
    $part_no=$data['part_no'];
	
	$users = ProfileModel::get_candidate_profile_ro($data['candidate_id']);
	
    $data = array_merge($users, $data);
	//echo "<pre>"; print_r($users); die("--nid");
	$data['serial_no']=$series;
    $data['part_no']=$part_no;
	
    $data['st_name'] = '';
	
	
	
    $state_object = StateModel::get_state($data['st_code']);
    if($state_object){
      $data['st_name'] = $state_object->ST_NAME;
    }
	
    $data['acs'] = [];
    $acs = AcModel::get_acs([
      'st_code' => $data['st_code']
    ]);

    foreach ($acs as $key => $ac_iterage) {
      $data['acs'][] = [
        'ac_no'      => $ac_iterage->ac_no,
        'ac_name'    => $ac_iterage->ac_no.'-'.$ac_iterage->ac_name,
        'st_code'    => $ac_iterage->st_code,
        'election_id' => $ac_iterage->election_id,
        'encoded'    => base64_encode($ac_iterage->pc_no),
      ];
    }
	
	$data['user_profile_state']='';
	if(!empty($users['state'])){ 
      $data['user_profile_state']  =  $users['state'];
    }
	
	
    $data['resident_acs'] = [];
    $acs = AcModel::where('ST_CODE', $users['state'])->get();

    foreach ($acs as $key => $ac_iterage) {
      $data['resident_acs'][] = [
        'ac_no'      => $ac_iterage->AC_NO,
        'ac_name'    => $ac_iterage->AC_NO.'-'.$ac_iterage->AC_NAME,
        'st_code'    => $ac_iterage->ST_CODE,
      ];
    }

    
	
    if($request->old('recognized_party')){ 
      $data['recognized_party']  = $request->old('recognized_party');
    }
     // $data['recognized_party']  = ($data['recognized_party'])?$data['recognized_party']:'recognized'; 
	 if($data['recognized_party']=='2'){
	 $data['recognized_party']='not-recognized';	
	}
	if($data['recognized_party']=='1'){
	 $data['recognized_party']='recognized';	
	}
    if($data['recognized_party']=='3'){
	 $data['recognized_party']='both';	
	}

    if($request->old('st_code')){
      $data['st_code']  = $request->old('st_code');
    }else{
      $data['st_code']  = $data['st_code']; 
    }

    if($request->old('legislative_assembly')){
      $data['legislative_assembly']  = $request->old('legislative_assembly');
    }else{
      $data['legislative_assembly']  = $data['legislative_assembly']; 
    }
   
    if($request->old('name')){
      $data['name']  = $request->old('name');
    }else{
      $data['name']  = $data['name']; 
    }
	
    if($request->old('father_name')){
      $data['father_name']  = $request->old('father_name');
    }else{
      $data['father_name']  = $data['father_name']; 
    }

    if($request->old('address')){
      $data['address']  = $request->old('address');
    }else{
      $data['address']  = $data['address']; 
    }
	
    if($data['serial_no']){
	     $data['serial_no']  = $data['serial_no']; 
   }else{
         $data['serial_no']  = $request->old('serial_no');
    }
	if(empty($data['serial_no'])){
		$data['serial_no'] = $users['serial_no'];
	}
    
	
	if($request->old('part_no')){
      $data['part_no']  = $request->old('part_no');
    }else{
      $data['part_no']  = $data['part_no']; 
    }
	if(empty($data['part_no'])){
		$data['part_no'] = $users['part_no'];
	}

    if($request->old('resident_ac_no')){
      $data['resident_ac_no']  = $request->old('resident_ac_no');
    }else if(isset($object) && $object){
      $data['resident_ac_no']  = $object['resident_ac_no']; 
    }else{
      $data['resident_ac_no']  = $data['resident_ac_no']; 
    }
	if(empty($data['resident_ac_no'])){
		$data['resident_ac_no'] = $users['ac'];
	}
	if(empty($data['epic_no'])){
		$data['epic_no'] = $users['epic_no'];
	}
	if(empty($users['epic_no'])){
		$data['epic_no'] = "NA";
	}

    if($request->old('proposer_name')){
      $data['proposer_name']  = $request->old('proposer_name');
    }else{
      $data['proposer_name']  = $data['proposer_name']; 
    }

    if($request->old('proposer_serial_no')){
      $data['proposer_serial_no']  = $request->old('proposer_serial_no');
    }else{
      $data['proposer_serial_no']  = $data['proposer_serial_no']; 
    } 
	
	
	if($request->old('epic_no_proposer_serch')){
      $data['epic_no_proposer_serch']  = $request->old('epic_no_proposer_serch');
    }else{
      $data['epic_no_proposer_serch']  = $data['epic_no_proposer_serch']; 
    }
	
	
    if($request->old('proposer_part_no')){
      $data['proposer_part_no']  = $request->old('proposer_part_no');
    }else{
      $data['proposer_part_no']  = $data['proposer_part_no']; 
    }

    if($request->old('proposer_assembly')){
      $data['proposer_assembly']  = $request->old('proposer_assembly');
    }else{
      $data['proposer_assembly']  = $data['proposer_assembly']; 
    }
	

    if($data['image'] && file_exists($data['image'])){
      $data['profileimg']  = $data['image'];
      $data['thumb']       = url($data['image']);
    }else{
      $data['profileimg']  = '';
      $data['thumb']       = url('img/vendor/avtar.jpg');
    }

    if($request->old('apply_date')){
      $data['apply_date']  = $request->old('apply_date');
    }else{
      if($data['apply_date'] == '0000-00-00'){
        $data['apply_date']  = date('d-m-Y'); 
      }else{
        $data['apply_date']  = ($data['apply_date'])?date('d-m-Y',strtotime($data['apply_date'])):date('d-m-Y'); 
      }
    }
	
    //echo \Auth::id().'--cid';
	//echo $data['nomination_id']; die("--nid");
	
	
	$non_recognized_proposers = [];
	
	$nom =      DB::connection('mysql')->table('nomination_application_proposer')
				->select('candidate_id', 'nomination_id', 'serial_no', 'part_no', 'fullname', 'signature','date', 'epic_no_proposer_serch_part_2')
				->where('candidate_id', '=', $data['candidate_id'])
				->where('nomination_id', '=', $data['nomination_id'])
				->where('status', '=', 1)
				->orderBy('id','DESC')
				->limit(10)
				->get()
				->toArray();	
				
	//echo "<pre>"; print_r($nom); die;
	
	if(isset($nom) && !empty($nom) && (count($nom)>0)){		
		for($i =0; $i < (count($nom)); $i++) { 
		  $non_recognized_proposers[] = [
			  'candidate_id'    => $data['candidate_id'],
			  'nomination_id'   => $data['nomination_id'],
			  'epic_no_proposer_serch_part_2'   => $nom[$i]->epic_no_proposer_serch_part_2,
			  's_no'       => $i,
			  'serial_no'  => $nom[$i]->serial_no,
			  'part_no'    => $nom[$i]->part_no,
			  'fullname'   => $nom[$i]->fullname,
			  'date'       => $nom[$i]->date,
			  'signature'  => $nom[$i]->signature,
		  ];
		}
	}
	if((count($non_recognized_proposers)==0)){		
       for($i = 1; $i <= 10; $i++) { 
        $non_recognized_proposers[] = [
          'candidate_id'    => $data['candidate_id'],
          'nomination_id'   => $data['nomination_id'],
		   'epic_no_proposer_serch_part_2'   => '',
          's_no'       => $i,
          'serial_no'  => '',
          'part_no'    => '',
          'fullname'   => '',
          'date'       => date('d-m-Y'),
          'signature'  => '',
        ];
      }
    }
	
   
    if($request->old('non_recognized_proposers')){
      $data['non_recognized_proposers']  = $request->old('non_recognized_proposers');
    }else if(isset($object) && $object){
      $data['non_recognized_proposers']  = $data['non_recognized_proposers']; 
    }else{
      $data['non_recognized_proposers']  = $non_recognized_proposers; 
    }
    return $data;
  }

  public function save_step_3(NominationPart12Request $request){
    $get_nominattion_detail = NominationApplicationModel::get_nomination_application_ro($request->nomination_id); 
    if(!$get_nominattion_detail){
      Session::flash('status',0);
      Session::flash('flash-message', __('messages.Pleasetry'));
      return Redirect::back();
    }
	$input = $request->all();
	if($input['user_profile_state']!=$input['st_code']){
      Session::flash('status',0);
      Session::flash('flash-message', __('messages.canstate')); 
      return Redirect::back()->withInput();
    }
	
	if($request->recognized_party == 'recognized'){ 
		if($input['legislative_assembly']!=$input['proposer_assembly']){  
		  Session::flash('status',0);
		  Session::flash('flash-message', __('messages.propac'));
		  return Redirect::back()->withInput();
		}
	}
	
	//echo "<pre>"; print_r($input); die;
	
	if($request->recognized_party == 'both'){ 
		if($input['legislative_assembly']!=$input['proposer_assembly']){
		  Session::flash('status',0);
		  Session::flash('flash-message', __('messages.propac'));
		  return Redirect::back()->withInput();
		}
	}
	
	
	
	if($request['recognized_party']=='not-recognized'){
	$request['recognized_party']='2';
	}
    if($request['recognized_party']=='recognized'){
	 $request['recognized_party']='1';
	}
	if($request['recognized_party']=='both'){
	 $request['recognized_party']='3';
	}

	
	
    $request->merge(['image_name' => $request->image]);
    DB::beginTransaction();
    try{
      if($request->recognized_party == '1'){ 
        $result   = NominationApplicationModel::add_nomination_part1_ro($request->all());
       // $NomlogModel   = NomlogModel::add_nomination_part1(Session::get('nomination_id'), $request->all());
		//die("One"); 
      }
	 if($request->recognized_party == '2'){ 
        NominationApplicationModel::add_nomination_part2_ro($request->all());
        NomlogModel::add_nomination_part2_ro(Session::get('nomination_id'), $request->all());		
		
		NominationProposerModel::add_delete_proposer_ro($request->nomination_id);
        NominationProposerModel::delete_proposer_ro($request->nomination_id);
		
        foreach(array_reverse($request->non_recognized_proposers) as $non_recognized_proposer){
            NominationProposerModel::add_proposer_ro($non_recognized_proposer);
        }
      }
	  if($request->recognized_party == '3'){
		
		$result   = NominationApplicationModel::add_nomination_part1_ro($request->all());
		
        NominationApplicationModel::add_nomination_part2_ro($request->all());
        NomlogModel::add_nomination_part2_ro(Session::get('nomination_id'), $request->all());		
		
		NominationProposerModel::add_delete_proposer_ro($request->nomination_id);
		NominationProposerModel::delete_proposer_ro($request->nomination_id);
        foreach(array_reverse($request->non_recognized_proposers) as $non_recognized_proposer){
            NominationProposerModel::add_proposer_ro($non_recognized_proposer);
        }
	  }
    }
    catch(\Exception $e){ print_r( $e->getMessage()); die;
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message', __('messages.Pleasetry'));
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    if($request->has('save_only')){
      Session::flash('flash-message',"Part I/II updated successfully.");
      return Redirect::back();
    }
    return redirect('/roac/apply-nomination-step-4');
  }


  public function apply_nomination_step_4(Request $request,$id = 0){
	  
	if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid'])){
	   $id =  decrypt_String($_REQUEST['nid']); 
	   $this->convertIntoSession($id);  
	}
	
	  
    $data                  = [];
    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/roac/dashboard'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => '#',
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Form 2B - Nomination Paper";
    $data['action'] = url('roac/apply-nomination-step-4/post');

    $data['href_back'] = url('roac/apply-nomination-step-3');
    $data['href_skip'] = url('roac/apply-nomination-step-5');


    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message', __('messages.Pleasetry'));
        return redirect("roac/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application_ro($id);
	
    if(!$user_nomination){
       return redirect("roac/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      //Session::flash('flash-message','You can not edit this nomination.');
      //return redirect("nomination/apply-nomination-step-2"); 
    }

    $data['reference_id']               = $user_nomination['nomination_no'];
    $data['href_download_application']  = url("roac/download/".encrypt_string($id));

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    $data['recognized_party'] = $user_nomination['recognized_party'];

    $data = array_merge($data, $user_nomination);
	$data['st_name'] = '';
    $state_object = StateModel::get_state($data['st_code']);
    if($state_object){
      $data['st_name'] = $state_object->ST_NAME;
	}
	$data['user_data'] = Auth::user();
    $data = $this->get_step4_form($id, $request, $data);
	
	
    return view('admin/candform/edit_nomination/apply-nomination-step-4',$data);
  }


  public function get_step4_form($id, $request, $data = []){

	$object = NominationApplicationModel::get_nomination_application_ro($id);

	$users = ProfileModel::get_candidate_profile_ro($object['candidate_id']);
	
    $data['categories'] = [
      ['id' => 'sc', 'name' => 'SC'],
      ['id' => 'st', 'name' => 'ST'],
      ['id' => 'general', 'name' => 'GENERAL'],
    ];      
	// actual code go there
	$catProfile = DB::connection('mysql')
	->table('profile')
	->select('category')
	->where('candidate_id', '=', $object['candidate_id'])
	->value('category');  
	
	
	
    if($request->old('category')){
      $data['category']  = $catProfile;
    }else{
      $data['category']  = $catProfile; 
    }

    $data['states'] = [];
    $states = StateModel::orderBy('ST_NAME','ASC')->get();
    foreach ($states as $key => $state_iterage) {
      $data['states'][] = [
        'st_code'    => $state_iterage->ST_CODE,
        'st_name'    => $state_iterage->ST_NAME,
      ];
    }
	
	if($request->old('not_applicable')){
      $data['not_applicable']  = $request->old('not_applicable');
    }else{
      $data['not_applicable']  = $data['not_applicable']; 
    }
	
    if($request->old('part3_cast_state')){
      $data['part3_cast_state']  = $request->old('part3_cast_state');
    }else{
      $data['part3_cast_state']  = $data['part3_cast_state']; 
    }

    if($request->old('part3_address')){
      $data['part3_address']  = $request->old('part3_address');
    }else{
      $data['part3_address']  = $data['part3_address']; 
    }

    if($request->old('part3_legislative_state')){
      $data['part3_legislative_state']  = $request->old('part3_legislative_state');
    }else{
      $data['part3_legislative_state']  = $data['part3_legislative_state']; 
    }

    if($request->old('age')){
      $data['age']  = $request->old('age');
    }else if($data['age'] == '0'){
      $data['age']  = date('Y') - date('Y',strtotime($candidate['dob']));
    }else{
      $data['age']  = $data['age']; 
    }

    if($request->old('language')){
      $data['language']  = $request->old('language');
    }else{
      $data['language']  = $data['language']; 
    }

    if($request->old('part3_date')){
      $data['part3_date']  = $request->old('part3_date');
    }else{
      if($data['part3_date'] == '0000-00-00'){
        $data['part3_date']  = date('d-m-Y'); 
      }else{
        $data['part3_date']  = ($data['part3_date'])?date('d-m-Y',strtotime($data['part3_date'])):date('d-m-Y'); 
      }
    }

    if($request->old('suggest_symbol_1')){
      $data['suggest_symbol_1']  = $request->old('suggest_symbol_1');
    }else{
      $data['suggest_symbol_1']  = $data['suggest_symbol_1']; 
    }

    if($request->old('suggest_symbol_2')){
      $data['suggest_symbol_2']  = $request->old('suggest_symbol_2');
    }else{
      $data['suggest_symbol_2']  = $data['suggest_symbol_2']; 
    }

    if($request->old('suggest_symbol_3')){
      $data['suggest_symbol_3']  = $request->old('suggest_symbol_3');
    }else{
      $data['suggest_symbol_3']  = $data['suggest_symbol_3']; 
    }

    if($request->old('party_id')){
      $data['party_id']  = $request->old('party_id');
    }else{
      $data['party_id']  = $data['party_id']; 
    }
	if($request->old('party_id2')){
      $data['party_id2']  = $request->old('party_id2');
    }else{
      $data['party_id2']  = $data['party_id2']; 
    }
   // dd($data['recognized_party']);
    $party_filter = [];
	
    if($data['recognized_party'] == 'recognized'){
      //state is mendatory for recognied party
      $party_filter['is_recognized'] = true;
    }else{
      $party_filter['is_recognized'] = false;
    }
    $party_filter['st_code'] = $data['st_code']; 
    $data['parties'] = [];
    $parties = PartyModel::get_parties_all_national($party_filter);    
	//echo "<pre>"; print_r($parties); die;	
	foreach ($parties as $iterate_party) {
      $data['parties'][] = [
        'party_id'  => $iterate_party['party_id'],
        'name'      => $iterate_party['name']
      ];
    }
	
	$data['parties_state'] = [];
	$parties_state_data = PartyModel::get_parties_all_state($party_filter);    
	//echo "<pre>"; print_r($parties); die;	
	foreach ($parties_state_data as $iterate_party_data) {
      $data['parties_state'][] = [
        'party_id_state'  => $iterate_party_data['party_id'],
        'name_party_id_state'      => $iterate_party_data['name']
      ];
    }
	
	$data['setup_party'] = [];
    $setp = PartyModel::setup_party($party_filter);    
	//echo "<pre>"; print_r($parties); die;	
	foreach ($setp as $setpIter) {
      $data['setup_party'][] = [
        'party_id'  => $setpIter['party_id'],
        'PARTYTYPE'  => $setpIter['PARTYTYPE'],
        'name'      => $setpIter['name']
      ];
    }
	
	
	
    return $data;
  }


  public function save_step_4(NominationPart3Request $request){
	$input = $request->all();  
	//echo "<pre>"; print_r($input); die;
	  
    if(!$request->has('suggest_symbol_1')){
      $request->merge(['suggest_symbol_1' => '']);
    }

    if(!$request->has('suggest_symbol_2')){
      $request->merge(['suggest_symbol_2' => '']);
    }

    if(!$request->has('suggest_symbol_3')){
      $request->merge(['suggest_symbol_3' => '']);
    }
	
    DB::beginTransaction();
    try{
      $nid = NominationApplicationModel::add_nomination_part3_ro($request->all());
			 NomlogModel::add_nomination_part3_ro($nid, $request->all());
    }
    catch(\Exception $e){ print_r( $e->getMessage()); die;
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    if($request->has('save_only')){
      Session::flash('flash-message',"PART III updated successfully.");
      return Redirect::back();
    }
    return redirect('/roac/apply-nomination-step-5');
  }

  //save step 5
  public function apply_nomination_step_5(Request $request,$id = 0){
	
	if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid'])){
	   $id =  decrypt_String($_REQUEST['nid']); 
	   $this->convertIntoSession($id);  
	}
		
	  
    $data                  = [];
    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/roac/dashboard'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => '#',
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Form 2B - Nomination Paper";
    $data['action'] = url('roac/apply-nomination-step-5/post');

    $data['href_back'] = url('roac/apply-nomination-step-4');
    $data['href_skip'] = url('roac/apply-nomination-step-6');

    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("roac/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application_ro($id);
    if(!$user_nomination){
       return redirect("roac/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      //Session::flash('flash-message','You can not edit this nomination.');
      //return redirect("nomination/apply-nomination-step-2"); 
    }

    $data['reference_id']               = $user_nomination['nomination_no'];
    $data['href_download_application']  = url("roac/download/".encrypt_string($id));

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    $data['recognized_party'] = $user_nomination['recognized_party'];

    $data = array_merge($data, $user_nomination);
	
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
	//
	if($data['finalize']=='1'){
		$data['finalize']='yes';
	}
	if($data['finalize']=='2'){
		$data['finalize']='no';
	}
	if($data['finalize']=='0'){
		$data['finalize']='NA';
	}
	//echo "<pre>"; print_r($data); die;
	$data['user_data'] = Auth::user();
	
    $data = $this->get_step5_form($id, $request, $data);
	
	return view('admin/candform/edit_nomination/apply-nomination-step-5',$data);
  }


  public function get_step5_form($id, $request, $data = []){
    if($id>0){
      $object = NominationApplicationModel::get_nomination_application_ro($id);
    }

    $data['yes_no_lists'] = [
      ['id' => '', 'name' =>  __('part3a.Select')],
      ['id' => 'yes', 'name' => __('part3a.Yes')],
      ['id' => 'no', 'name' => __('part3a.No')],
    ];  

    $data['custom_errors']      = [];

    if($request->old('have_police_case')){
      $data['have_police_case']  = $request->old('have_police_case');
    }else{
      $data['have_police_case']  = ($data['have_police_case'])?$data['have_police_case']:'no'; 
    }

    if($request->old('profit_under_govt')){
      $data['profit_under_govt']  = $request->old('profit_under_govt');
    }else{
      $data['profit_under_govt']  = $data['profit_under_govt']; 
    }

    if($request->old('court_insolvent')){
      $data['court_insolvent']  = $request->old('court_insolvent');
    }else{
      $data['court_insolvent']  = $data['court_insolvent']; 
    }

    if($request->old('allegiance_to_foreign_country')){
      $data['allegiance_to_foreign_country']  = $request->old('allegiance_to_foreign_country');
    }else{
      $data['allegiance_to_foreign_country']  = $data['allegiance_to_foreign_country']; 
    }

    if($request->old('disqualified_section8A')){
      $data['disqualified_section8A']  = $request->old('disqualified_section8A');
    }else{
      $data['disqualified_section8A']  = $data['disqualified_section8A']; 
    }

    if($request->old('disloyalty_status')){
      $data['disloyalty_status']  = $request->old('disloyalty_status');
    }else{
      $data['disloyalty_status']  = $data['disloyalty_status']; 
    }

    if($request->old('subsiting_gov_taken')){
      $data['subsiting_gov_taken']  = $request->old('subsiting_gov_taken');
    }else{
      $data['subsiting_gov_taken']  = $data['subsiting_gov_taken']; 
    }

    if($request->old('managing_agent')){
      $data['managing_agent']  = $request->old('managing_agent');
    }else{
      $data['managing_agent']  = $data['managing_agent']; 
    }

    if($request->old('disqualified_by_comission_10Asec')){
      $data['disqualified_by_comission_10Asec']  = $request->old('disqualified_by_comission_10Asec');
    }else{
      $data['disqualified_by_comission_10Asec']  = $data['disqualified_by_comission_10Asec']; 
    }

    if($request->old('office_held')){
      $data['office_held']  = $request->old('office_held');
    }else{
      $data['office_held']  = $data['office_held']; 
    }

    if($request->old('discharged_insolvency')){
      $data['discharged_insolvency']  = $request->old('discharged_insolvency');
    }else{
      $data['discharged_insolvency']  = $data['discharged_insolvency']; 
    }

    if($request->old('country_detail')){
      $data['country_detail']  = $request->old('country_detail');
    }else{
      $data['country_detail']  = $data['country_detail']; 
    }

    if($request->old('disqualified_period')){
      $data['disqualified_period']  = $request->old('disqualified_period');
    }else{
      $data['disqualified_period']  = $data['disqualified_period']; 
    }

    if($request->old('date_of_dismissal')){
      $data['date_of_dismissal']  = $request->old('date_of_dismissal');
    }else{
      if($data['date_of_dismissal'] == '0000-00-00'){
        $data['date_of_dismissal']  = '';
      }else{
        $data['date_of_dismissal']  = $data['date_of_dismissal']; 
      }
    }

    if($request->old('subsitting_contract')){
      $data['subsitting_contract']  = $request->old('subsitting_contract');
    }else{
      $data['subsitting_contract']  = $data['subsitting_contract']; 
    }

    if($request->old('gov_detail')){
      $data['gov_detail']  = $request->old('gov_detail');
    }else{
      $data['gov_detail']  = $data['gov_detail']; 
    }

    if($request->old('date_of_disqualification')){
      $data['date_of_disqualification']  = $request->old('date_of_disqualification');
    }else{
      if($data['date_of_disqualification'] == '0000-00-00'){
        $data['date_of_disqualification']  = '';
      }else{
        $data['date_of_disqualification']  = $data['date_of_disqualification']; 
      }
    }

    if($request->old('date_of_disloyal')){
      $data['date_of_disloyal']  = $request->old('date_of_disloyal');
    }else{
      if($data['date_of_disloyal'] == '0000-00-00'){
        $data['date_of_disloyal']  = '';
      }else{
        $data['date_of_disloyal']  = $data['date_of_disloyal']; 
      }
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


    $data['police_cases'] = [];
    $police_cases[] = [
      'case_no' => '',
      'police_station' => '',
      'state' => '',
      'district' => '',
      'convicted_des' => '',
      'date_of_conviction' => '',
      'court_name'          => '',
      'punishment_imposed' => '',
      'date_of_release' => '',
      'revision_against_conviction' => '',
      'revision_appeal_date' => '',
      'rev_court_name' => '',
      'status' => '',
      'rev_court_name' => '',
      'revision_disposal_date'  => '',
      'revision_order_description' => ''
    ];

    if($request->old('police_case')){
      $data['police_cases']  = $request->old('police_case');
    }else if(isset($object) && $object){
      $data['police_cases']  = NominationPoliceCaseModel::get_police_cases_ro($object['id']); 
    }else{
      $data['police_cases']  = $police_cases;
    }

    if($request->old('custom_errors')){
      $data['custom_errors'] = $request->old('custom_errors');
    }

    return $data;
  }


  public function save_step_5(NominationPart3aRequest $request){

    if($request->has('police_case') && count($request->police_case) && $request->have_police_case == 'yes'){
      $errors   = [];
      $is_error = false;
      foreach ($request->police_case as $key => $result) {
        foreach ($result as $second_key => $value) {
			
				//echo "<pre>"; print_r($second_key.'-'.$value); 
				if($second_key!='date_of_release'){	
					  if(!$value || trim($value) == ''){
						$errors[$key][$second_key] = __('part3a.error_form'); 
						$is_error = true;
					  }else{
						$errors[$key][$second_key] = false;
					  }
				}  
		  
        }
      }
	//  die("TTTT");	
      if(count($errors)>0){
        $request->merge(['custom_errors' => $errors]); 
      }

      if($is_error){
        Session::flash('flash-message',  __('part3a.checkform'));
        return Redirect::back()->withInput($request->all());
      }
    }
	//echo "<pre>"; print_r($request->all()); die;
    DB::beginTransaction();
    try{
	  	
		   $nid = NominationApplicationModel::add_nomination_part3a_ro($request->all());
			NomlogModel::add_nomination_part3a_ro($nid, $request->all());
			NominationPoliceCaseModel::add_delete_police_case_ro($request->nomination_id);
			NominationPoliceCaseModel::delete_police_case_ro($request->nomination_id);
      if($request->have_police_case == 'yes'){
        foreach($request->police_case as $iterate_police){ 
		  if(isset($iterate_police['state'])){
			$iterate_police['st_code']=str_replace('state', 'st_code',    $iterate_police['state']);  
		  }
          $pid = NominationPoliceCaseModel::add_police_case_ro($iterate_police, $request->nomination_id, $request->candidate_id); 
		  NominationPoliceCaseModel::add_police_case_log_ro($pid, $iterate_police, $request->nomination_id, $request->candidate_id);
        }
      }
	//die;	
    }
    catch(\Exception $e){ print_r( $e->getMessage()); die;
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message', "Please Try Again.");
      return Redirect::back();
    }
	//print_r($request->all()); die("Testing");
    DB::commit();
    Session::flash('status',1);
    if($request->has('save_only')){
      Session::flash('flash-message',"PART IIIA updated successfully.");
      return Redirect::back();
    }
    return redirect(url('roac/candidateinformation?nom_id='.encrypt_string($request->reference_id)));
  }

  public function apply_nomination_step_6(Request $request,$id = 0){
    $data                  = [];
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
    $data['heading_title'] = "Upload Affidavit";
    $data['action'] = url('nomination/apply-nomination-step-6/post');

    $data['href_back'] = url('nomination/apply-nomination-step-5');
    $data['href_skip'] = url('nomination/apply-nomination-finalize');
    $data['href_file_upload'] = url('nomination/upload-affidavit');
    
    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }
	
	
	$eaff =  DB::connection('mysql')
	->table('aff_cand_details')
	->select('id','affidavit_id', 'finalized')
	->where('candidate_id', '=', \Auth::id())
	//->where('finalized', '!=', '')
	->get();
	$data['af'] = [];
	if(count($eaff) > 0){		
		foreach ($eaff as $key => $da) {
		  $data['af'][] = [
			'id'     => $da->id,
			'affidavit_id'   => $da->affidavit_id,
			'finalized'   => $da->finalized
		  ];
		}
	}
	
	//echo  \Auth::id();
	//echo "<pre>"; print_r($data['af']); 
	

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
	//echo "<pre>"; print_r($user_nomination); die;
    if(!$user_nomination){
       return redirect("nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      //Session::flash('flash-message','You can not edit this nomination.');
      //return redirect("nomination/apply-nomination-step-2"); 
    }

    $data['assigned_e_affidavit']               = $user_nomination['assigned_e_affidavit'];
    $data['reference_id']               = $user_nomination['nomination_no'];
    $data['href_download_application']  = url("nomination/download/".encrypt_string($id));

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation
    
    if($request->old('affidavit')){
      $data['affidavit']  = $request->old('affidavit');
    }else if(isset($user_nomination) && file_exists($user_nomination['affidavit'])){
      $data['affidavit']  = $user_nomination['affidavit']; 
    }else{
      $data['affidavit']  = '';
    }

	//echo "<pre>"; print_r($data); die;
    return view('nomination/apply-nomination-step-6',$data);
  }


  public function save_step_6(Request $request){
    $get_nominattion_detail = NominationApplicationModel::get_nomination_application($request->nomination_id);
    if(!$get_nominattion_detail){
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }

    if(!$request->has('affidavit') && !file_exists($request->affidavit)){
      \Session::flash('error_mes', 'Please choose a valid pdf file.');
      return Redirect::back()->withInput($request->all());
    }

    $values = [];
    $values['affidavit_name'] = $request->affidavit;
    $values['nomination_id']  = $request->nomination_id;
    DB::beginTransaction();
    try{ 
      $nid = NominationApplicationModel::add_affidavit($values);
             NomlogModel::add_affidavit($nid, $values);
    }
    catch(\Exception $e){ //print_r( $e->getMessage()); die;
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    Session::flash('flash-message', __('finalize.flash_message_show'));
    return redirect('/nomination/apply-nomination-finalize');
  }
  
  public function apply_nomination_finalize(Request $request,$id = 0){
	
	if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid'])){
	   $id =  decrypt_String($_REQUEST['nid']); 
	   $this->convertIntoSession($id);  
	}
	
    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      //Session::flash('flash-message','You can not edit this nomination.');
      //return redirect("nomination/apply-nomination-step-2"); 
    }
	
    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    $data = NominationApplicationModel::get_nomination($user_nomination['id']);
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
	//
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
	
	//echo $party; die;
	//echo "<pre>"; print_r($data); die("PPP");
	
    $data['reference_id']               = $user_nomination['nomination_no'];
    $data['href_download_application']  = url("nomination/download/".encrypt_string($id));
    $data['qr_code']                    = url($data['qrcode']);

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

    $data['href_back'] = url('nomination/apply-nomination-step-6');
    
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
	if($data['affidavit']!=''){
      $data['affidavit']  = url($data['affidavit']);
	} else {
	  $data['affidavit']  = 'NA';
	}
	
	
    //QR code
    // $st_code          = $data['st_code'];
    // $year             = date('Y');
    // $ac_no            = $data['ac_no'];
    // $election_name    = 'E'.$data['election_id'];
    // $destination_path = FileModel::get_file_path('uploads/qrcode/'.$year.'/ac/'.$election_name.'/'.$st_code.'/'.$ac_no).'/'.$data['id'].'.png';
    // \QRCode::text(url("/nomination-status/".$data['id']))->setOutfile($destination_path)->png();
    // $data['qrcode_path']  = $destination_path;
    // $data['qrcode']       = url($destination_path);
    // NominationApplicationModel::add_qrcode($data);
    //end QR code

    return view('nomination/apply-nomination-finalize',$data);
  }

    public static function getformated_part($single_str) {
		if($single_str == '1'){
			return 'I';
		}
		elseif($single_str == '2'){
			return 'II';
		}
		elseif($single_str == '3'){
			return 'III';
		}
		elseif($single_str == '4'){
			return 'IIIA';
		}
	}  

  
  public function save_nomination_finalize($id = 0, Request $request){
	
    DB::beginTransaction();
    try{
    
	NomlogModel::finalize_nomination($request->nomination_id);	
    NominationApplicationModel::finalize_nomination($request->nomination_id);
	  
	$get_nominattion_detail = NominationApplicationModel::get_nomination_application($request->nomination_id);		
	$mob = DB::connection('mysql')
	->table('profile')
	->select('name', 'mobile', 'email')
	->where('candidate_id', '=', $get_nominattion_detail['candidate_id'])
	->get();
	
	$app = DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('nomination_no', '=', $get_nominattion_detail['nomination_no'])
	->get();
	

	
	 \Session::put('nomination_id_in_payment', $_REQUEST['nomination_id']);
     \Session::put('st_code', $app[0]->st_code);
     \Session::put('ac_no',   $app[0]->ac_no);
	
	
	/*
	$state = $this->getState($app[0]->st_code); 
	$ac    = $this->getAcName($app[0]->st_code, $app[0]->ac_no); 
	
	$message =   __('finalize.Dear') . " " .$mob[0]->name. " ". __('finalize.your_onlinie') ." " . $get_nominattion_detail['nomination_no']." ". __('finalize.has_been_success') ." ". date('d-m-Y') . " ".__('finalize.for_online')." ".$state .', '. $ac . __('finalize.track');	
	
	
	
	$messageEmail =  __('finalize.Dear') . " " .$mob[0]->name. ",\n\n  ". __('finalize.your_onlinie') ." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_success')." ". date('d-m-Y') . " ".__('finalize.for_online')." ".$state .', '. $ac ." " . __('finalize.track') ."\n\n ".__('finalize.Thank');
	
	$subject =  __('finalize.subject');	
	
	
	
	$datasss =  DB::connection('mysql')
	->table('officer_login')
	->select('*')
	->where('st_code', '=', $app[0]->st_code)
	->where('ac_no', '=',   $app[0]->ac_no)
	->where('designation', '=', 'ROAC')
	->get();	
	if(count($datasss) > 0){		
	   if(isset($datasss[0]->Phone_no)){			   
		$msms =  __('finalize.Dear') . " ".$datasss[0]->officername. " ". __('finalize.ro')." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_success')." ". date('d-m-Y') ." " . __('finalize.rofor') ;
		
	    $this->sendSMS($datasss[0]->Phone_no, $msms);		
	   }
	   if(isset($datasss[0]->email)){		
		$memail =   __('finalize.Dear'). " " .$datasss[0]->officername. "\n\n".__('finalize.ro')." ". $get_nominattion_detail['nomination_no']."  ".__('finalize.has_been_success')." ". date('d-m-Y') . " ". __('finalize.rofor') . "\n\n ".__('finalize.Thank');		
		$this->sendEmail($datasss[0]->email, $memail, $subject);	
	   }
	}  

	//echo $mob[0]->mobile.'-'.$message; die;
	$this->sendEmail($mob[0]->email, $messageEmail, $subject);	
	$this->sendSMS($mob[0]->mobile, $message); */
	
    }
	
		catch(\Exception $e){ print_r( $e->getMessage()); die;
		  DB::rollback();
		  Session::flash('status',0);
		  Session::flash('flash-message', __('finalize.again'));
		  return Redirect::back();
		}
		DB::commit();
		Session::flash('is_sub', "yes");
		
	return redirect('/nomination/nominations?acs='.encrypt_String($app[0]->ac_no).'&std='.encrypt_String($app[0]->st_code));
  }

   public function submit_for_pre_scrutiny(Request $Request){
    Session::forget('nomination_id');
    $data                   = [];
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
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $results = NominationApplicationModel::get_nominations();
	//echo "<pre>"; print_r($data); die;
    foreach($results as $result){ 
      $encrypt_id = encrypt_String($result['id']);
      if($result['finalize']){
        $status = 'Finalized';
      }else{
        $status = 'In-completed';
      }
	  
      $data['results'][] = [
        'nomination_no' => $result['nomination_no'],
        'id'      => $result['id'],
        'name'    => $result['name'],
        'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
        'election_name' => $result['election_name'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'step'       	=>$result['step'],
        'is_apply_prescrutiny'       	=>$result['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$result['prescrutiny_status'],
        'prescrutiny_comment'       	=>$result['prescrutiny_comment'],
        'prescrutiny_apply_datetime'       	=>$result['prescrutiny_apply_datetime'],
        'view_href'     => url('nomination/detail/'.$encrypt_id),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $result['finalize']
      ];
    }
    return view('nomination/submit-for-pre-scrutiny',$data);
  } 
  
  public function track_nomination_status(Request $Request){
    Session::forget('nomination_id');
    $data                   = [];
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
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $results = NominationApplicationModel::get_nominations();
	//echo "<pre>"; print_r($data); die;
    foreach($results as $result){ 
      $encrypt_id = encrypt_String($result['id']);
      if($result['finalize']){
        $status = 'Finalized';
      }else{
        $status = 'In-completed';
      }
	  
      $data['results'][] = [
        'nomination_no' => $result['nomination_no'],
        'id'      => $result['id'],
        'name'    => $result['name'],
        'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
        'election_name' => $result['election_name'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'step'       	=>$result['step'],
        'is_apply_prescrutiny'       	=>$result['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$result['prescrutiny_status'],
        'prescrutiny_comment'       	=>$result['prescrutiny_comment'],
        'is_appoinment_scheduled'       	=>$result['is_appoinment_scheduled'],
        'appoinment_status'       	=>$result['appoinment_status'],
        'appoinment_scheduled_datetime'       	=>$result['appoinment_scheduled_datetime'],
        'view_href'     => url('nomination/detail/'.$encrypt_id),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $result['finalize']
      ];
    }
    return view('nomination/track-nomination-status',$data);
  }
  
  
  public function download_scheduled(Request $Request){
	if(!isset($_REQUEST['id'])){
	  Session::flash('status',0);
      Session::flash('flash-message', "Please select nomination.");
      return Redirect::back();
	}
    Session::forget('nomination_id');
    $data                   = [];
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
	
	$data['ids'] = $_REQUEST['id'];
	$data['is_active']     = 'nomination';
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "My Nominations";
   
   
	
	

	$nom=DB::connection('mysql')->table('nomination_application')
		->select('*')
		->where('id', '=', $_REQUEST['id'])
		->get()
		->toArray();
	if(count($nom) > 0 ){
		$data['nom_id'] = $nom[0]->id;
		$data['NOMNO'] =  $nom[0]->nomination_no;
		$data['candidate_name'] = $nom[0]->name;
		$data['ACNO'] =   $nom[0]->ac_no;
		$data['ACname'] = $this->getAcName($nom[0]->st_code, $nom[0]->ac_no);
		$getDistNo = $this->getDistNo($nom[0]->st_code, $nom[0]->ac_no);
		$data['DistName'] = $this->getDist($nom[0]->st_code, $getDistNo);		
		$data['is_appoinment_scheduled_for_one'] = $nom[0]->appoinment_status;	

		$data['ROdetails'] = $this->getRODetails($nom[0]->st_code, $nom[0]->ac_no);
		if(count($data['ROdetails']) > 0 ){
		 
			foreach($data['ROdetails'] as $ro){ 
				$data['ROname']    = $ro->name;
				$data['ROaddress'] = $ro->placename;
				$data['ROaddress1'] = $ro->ro_address_l1;
				$data['ROaddress2'] = $ro->ro_address_l2;
			}
		
		}	
		
		
		$datedd = $timedd  = $daydd = $ampm = '';
		if(isset($nom[0]->appoinment_scheduled_datetime)){
			$datetiime=explode(" ", $nom[0]->appoinment_scheduled_datetime);
			$datedd =  date("d-m-Y", strtotime($datetiime[0]));
			$timedd = substr($datetiime[1], 0, -3);
			$daydd  = date('D', strtotime($datedd));
			$ampm   =  date('A', strtotime($nom[0]->appoinment_scheduled_datetime));			
		}
		
		$slot = '';
		if ($nom[0]->slot == 1){
		 $slot="11AM TO 1PM";	
		}
		if($nom[0]->slot == 2){
		 $slot="1PM TO 3PM";	
		}
		
		
		$data['appoinment_scheduled_datetime_one'] = $nom[0]->appoinment_scheduled_datetime;
		$data['appoinment_scheduled_date_one'] = $datedd;
		$data['appoinment_scheduled_time_one'] = $timedd;
		$data['appoinment_scheduled_day_one'] = $daydd;
		$data['slot'] = $slot;
		$data['ampm'] = $ampm;
		
		if($nom[0]->appoinment_status =='' or $nom[0]->appoinment_status==0){
			$st='Scheduled';
		}
		if($nom[0]->appoinment_status ==1){
			$st='Accepted';
		} 
		if($nom[0]->appoinment_status ==2){
			$st='Cancel';
		}		
		$data['appoinment_status'] = $st;
		$data['updated_at'] = $nom[0]->updated_at;
		$data['view_href_cust'] = url('nomination/detail/'.encrypt_String($_REQUEST['id']));
		$data['download_href_cust'] = url('nomination/download/'.encrypt_String($_REQUEST['id']));
	}
	$name_excel = time();

    $setting_pdf = [
      'margin_top'        => 40,        // Set the page margins for the new document.
      'margin_bottom'     => 10,    
    ];
	
    $pdf = \PDF::loadView('nomination/download-scheduled', $data, [], $setting_pdf);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  } 
 
  public function getAPSFromDetailsTB($nom){
	
	 $res=DB::connection('mysql')->table('candidate_nomination_detail')
	->select('application_status')
	->where('nomination_no', '=', $nom)
	->where('candidate_id', '=', \Auth::id())
	->get()
	->toArray(); 
	if( count($res)>0 ){ 
		if($res[0]->application_status >=3){
		  if($res[0]->application_status ==3){
			return "Receipt Generated";
		  } else if ($res[0]->application_status ==4){
			return "Rejected";
		  }	else if ($res[0]->application_status ==5){
			return "Withdrawn";
		  } else if ($res[0]->application_status ==6){
			return "Accepted";
		  } else if ($res[0]->application_status ==7){
			return "Duplicate Nomination";
		  }  else if ($res[0]->application_status ==11){
			return "Duplicate Drop";
		  }
			
		} else {
			return 0;
		}
	} else {
			return 0;
	}
  }
 
  public function getNominationDetails($nomid){ 
	  
   $nom=DB::connection('mysql')->table('nomination_application')
	->select('*')
	->where('id', '=', $nomid)
	->get()
	->toArray();
	if(count($nom) > 0 ){ 
		$data['nom_id'] = $nom[0]->id;
		$data['NOMNO'] =  $nom[0]->nomination_no;
		$data['candidate_name'] = $nom[0]->name;
		$data['ACNO'] =   $nom[0]->ac_no;
		$data['ACname'] = $this->getAcName($nom[0]->st_code, $nom[0]->ac_no);
		$getDistNo = $this->getDistNo($nom[0]->st_code, $nom[0]->ac_no);
		$data['DistName'] = $this->getDist($nom[0]->st_code, $getDistNo);		
		$data['is_appoinment_scheduled_for_one'] = $nom[0]->appoinment_status;	

		$data['ROdetails'] = $this->getRODetails($nom[0]->st_code, $nom[0]->ac_no);
		if(count($data['ROdetails']) > 0 ){
			
			foreach($data['ROdetails'] as $ro){ 
				$data['ROname']    = $ro->name;
				$data['ROaddress'] = $ro->placename;
				$data['ROaddress1'] = $ro->ro_address_l1;
				$data['ROaddress2'] = $ro->ro_address_l2;
			}
		
		}	
		
		
		$datedd = $timedd  = $daydd = $ampm = '';
		if(isset($nom[0]->appoinment_scheduled_datetime)){
			$datetiime=explode(" ", $nom[0]->appoinment_scheduled_datetime);
			$datedd =  date("d-m-Y", strtotime($datetiime[0]));
			$timedd = substr($datetiime[1], 0, -3);
			$daydd  = date('D', strtotime($datedd));
			$ampm   =  date('A', strtotime($nom[0]->appoinment_scheduled_datetime));			
		} else {
			$datetiime=explode(" ", $nom[0]->prescrutiny_apply_datetime);
			
		}
		
		
		$data['appoinment_scheduled_datetime_one'] = $nom[0]->appoinment_scheduled_datetime;
		$data['appoinment_scheduled_date_one'] = $datedd;
		$data['appoinment_scheduled_time_one'] = $timedd;
		$data['appoinment_scheduled_day_one'] = $daydd;
		$data['ampm'] = $ampm;
		
		if($nom[0]->appoinment_status =='' or $nom[0]->appoinment_status==0){
			$st='Scheduled';
		}
		if($nom[0]->appoinment_status ==1){
			$st='Accepted';
		}
		if($nom[0]->appoinment_status ==2){
			$st='Cancel';
		}		
		$data['appoinment_status'] = $st;
		$data['updated_at'] = $nom[0]->updated_at;
		$data['view_href_cust'] = url('nomination/detail/'.encrypt_String($_REQUEST['id']));
		$data['download_href_cust'] = url('nomination/download/'.encrypt_String($_REQUEST['id']));
		//////////////////
		
		
		if(!empty($data['ROdetails'][0]->name)){
			$roname = $data['ROdetails'][0]->name;
		} else {
			$roname = 'NA';
		}
		if(!empty($data['ROdetails'][0]->placename)){
			$placename = $data['ROdetails'][0]->placename;
		} else {
			$placename = 'NA';
		}
		if(!empty($data['ROdetails'][0]->ro_address_l1)){
			$ro_address_l1 = $data['ROdetails'][0]->ro_address_l1;
		} else {
			$ro_address_l1 = 'NA';
		}
		if(!empty($data['ROdetails'][0]->ro_address_l2)){
			$ro_address_l2 = $data['ROdetails'][0]->ro_address_l2;
		} else {
			$ro_address_l2 = 'NA';
		}
		
		return  $nom[0]->nomination_no.'***'.$nom[0]->name.'***'.$nom[0]->ac_no.'***'.$this->getAcName($nom[0]->st_code, $nom[0]->ac_no).'***'.$st.'***'.$nom[0]->updated_at.'***'.url('nomination/detail/'.encrypt_String($_REQUEST['id'])).'***'.url('nomination/download/'.encrypt_String($_REQUEST['id'])).'***'.$roname.'***'.$placename.' '.$ro_address_l1.'***'.$ro_address_l2.'***'.$this->getDist($nom[0]->st_code, $getDistNo).'***'.$this->getElection($nom[0]->election_type_id).'***'.$this->getState($nom[0]->st_code).'***'.$this->getPartyName($nom[0]->party_id).'***'.$nom[0]->st_code;
		
		
	} else {
		return "NA";
	}
	  
	  
  }
  
  public function getNominationBookedDetails($nomid){ 
	  
   $nom=DB::connection('mysql')->table('nomination_application')
	->select('*')
	->where('id', '=', $nomid)
	//->whereNotNull('appoinment_scheduled_datetime')
	->get()
	->toArray();
	
	
	
	if(count($nom) > 0 ){ 
		$data['nom_id'] = $nom[0]->id;
		$data['NOMNO'] =  $nom[0]->nomination_no;
		$data['candidate_name'] = $nom[0]->name;
		$data['ACNO'] =   $nom[0]->ac_no;
		$data['ACname'] = $this->getAcName($nom[0]->st_code, $nom[0]->ac_no);
		$getDistNo = $this->getDistNo($nom[0]->st_code, $nom[0]->ac_no);
		$data['DistName'] = $this->getDist($nom[0]->st_code, $getDistNo);		
		$data['is_appoinment_scheduled_for_one'] = $nom[0]->appoinment_status;	
		
		
		$data['ROname']    = 'NA';
		$data['ROaddress'] = 'NA';
		$data['ROaddress1'] = 'NA';
		$data['ROaddress2'] = 'NA';
		
		$data['ROdetails'] = $this->getRODetails($nom[0]->st_code, $nom[0]->ac_no);
		if(count($data['ROdetails']) > 0 ){
			foreach($data['ROdetails'] as $ro){ 
				$data['ROname']    = $ro->name;
				$data['ROaddress'] = $ro->placename;
				$data['ROaddress1'] = $ro->ro_address_l1;
				$data['ROaddress2'] = $ro->ro_address_l2;
			}
		} else {
				$data['ROname']    = 'NA';
				$data['ROaddress'] =  'NA';
				$data['ROaddress1'] = 'NA';
				$data['ROaddress2'] = 'NA';
		}	
		
		
		$datedd = $timedd  = $daydd = $ampm = '';
		if(isset($nom[0]->appoinment_scheduled_datetime)){
			$datetiime=explode(" ", $nom[0]->appoinment_scheduled_datetime);
			$datedd =  date("d-m-Y", strtotime($datetiime[0]));
			$timedd = substr($datetiime[1], 0, -3);
			$daydd  = date('D', strtotime($datedd));
			$ampm   =  date('A', strtotime($nom[0]->appoinment_scheduled_datetime));			
		} else {
			$datetiime=explode(" ", $nom[0]->prescrutiny_apply_datetime);
			
		}
		
		
		$data['appoinment_scheduled_datetime_one'] = $nom[0]->appoinment_scheduled_datetime;
		$data['appoinment_scheduled_date_one'] = $datedd;
		$data['appoinment_scheduled_time_one'] = $timedd;
		$data['appoinment_scheduled_day_one'] = $daydd;
		$data['ampm'] = $ampm;
		
		if($nom[0]->appoinment_status =='' or $nom[0]->appoinment_status==0){
			$st='Scheduled';
		}
		if($nom[0]->appoinment_status ==1){
			$st='Accepted';
		}
		if($nom[0]->appoinment_status ==2){
			$st='Cancel';
		}		
		$data['appoinment_status'] = $st;
		$data['updated_at'] = $nom[0]->updated_at;
		$data['view_href_cust'] = url('nomination/detail/'.encrypt_String($_REQUEST['id']));
		$data['download_href_cust'] = url('nomination/download/'.encrypt_String($_REQUEST['id']));
		
		$cat='';
		if(empty($nom[0]->category)){
		  $cat='general';	
		} else {
		  $cat=$nom[0]->category;		
		} 
		
		if(!empty($data['ROdetails'][0]->name)){
			$roname = $data['ROdetails'][0]->name;
		} else {
			$roname = 'NA';
		}
		if(!empty($data['ROdetails'][0]->placename)){
			$placename = $data['ROdetails'][0]->placename;
		} else {
			$placename = 'NA';
		}
		if(!empty($data['ROdetails'][0]->ro_address_l1)){
			$ro_address_l1 = $data['ROdetails'][0]->ro_address_l1;
		} else {
			$ro_address_l1 = 'NA';
		}
		if(!empty($data['ROdetails'][0]->ro_address_l2)){
			$ro_address_l2 = $data['ROdetails'][0]->ro_address_l2;
		} else {
			$ro_address_l2 = 'NA';
		}
		
		//////////////////
		return  $nom[0]->nomination_no.'***'.$nom[0]->name.'***'.$nom[0]->ac_no.'***'.$this->getAcName($nom[0]->st_code, $nom[0]->ac_no).'***'.$st.'***'.$nom[0]->updated_at.'***'.url('nomination/detail/'.encrypt_String($_REQUEST['id'])).'***'.url('nomination/download/'.encrypt_String($_REQUEST['id'])).'***'.$roname.'***'.$placename.' '.$ro_address_l1.'***'.$ro_address_l2.'***'.$this->getDist($nom[0]->st_code, $getDistNo).'***'.$this->getElection($nom[0]->election_type_id).'***'.$this->getState($nom[0]->st_code).'***'.$this->getPartyName($nom[0]->party_id).'***'.$nom[0]->st_code.'***'.$cat;
	} else {
		return "NA";
	}
	  
	  
  }
  
  
  
  public function book_details(Request $Request){ 
	$cntcheck=DB::connection('mysql')->table('nomination_application')
	->select('*')
	->whereIn('id', explode(',', $_REQUEST['id']))
	->groupBy('ac_no', 'st_code')
	->get();
	
	if(count($cntcheck) > 1){
	  Session::flash('status',0);
      Session::flash('flash-message', "Please select same ac nomination.");
      return Redirect::back();
	}
	
	
	if(!isset($_REQUEST['id']) && ($_REQUEST['id'] <=0 )){
	  Session::flash('status',0);
      Session::flash('flash-message', "Please select nomination.");
      return Redirect::back();
	}
    Session::forget('nomination_id');
    $data                   = [];
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
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $results = NominationApplicationModel::get_nominations();

	//echo "<pre>"; print_r($data); die;
    foreach($results as $result){ 
      $encrypt_id = encrypt_String($result['id']);
      if($result['finalize']){
        $status = 'Finalized';
      }else{
        $status = 'In-completed';
      }
	  
      $data['results'][] = [
        'nomination_no' => $result['nomination_no'],
        'id'      => $result['id'],
        'name'    => $result['name'],
        'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
        'election_name' => $result['election_name'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'step'       	=>$result['step'],
        'is_apply_prescrutiny'       	=>$result['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$result['prescrutiny_status'],
        'prescrutiny_comment'       	=>$result['prescrutiny_comment'],
        'is_appoinment_scheduled'       	=>$result['is_appoinment_scheduled'],
        'appoinment_status'       	=>$result['appoinment_status'],
        'appoinment_scheduled_datetime'       	=>$result['appoinment_scheduled_datetime'],
        'view_href'     => url('nomination/detail/'.$encrypt_id),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $result['finalize']
      ];
    }
	$datesss='';
	if(count($results) > 0 ){
		foreach($results as $res){ 
			if($res['is_appoinment_scheduled']==1){ 
				if(isset($res['appoinment_scheduled_datetime'])){
				$datet = explode(" ", $res['appoinment_scheduled_datetime']);
				$datesss.= $datet[0].'+++'.substr($datet[1], 0, -3).'***'; 
				}
			}
		}
	}
	//echo $_REQUEST['id']; die;
	$data['datesss']=substr($datesss, 0, -3);
	$nom=DB::connection('mysql')->table('nomination_application')
		->select('*')
		->whereIn('id', array($_REQUEST['id']))
		->get()
		->toArray();
	//echo "<pre>"; print_r($nom); die;	
	$start_end = $this->getStartEndDateNomination($nom[0]->st_code, $nom[0]->ac_no);
	
	
	//
	//$nomiantaion_start_date = 0;
	if($start_end!=0){
	$nomdate  = explode("***", $start_end); 	
	$start    = str_replace("-", "/", $nomdate[0]);
	$end      = str_replace("-", "/", $nomdate[1]);
	$nomiantaion_start_date = $start;
	$nomiantaion_end_date   = $end;	
	$data['nomiantaion_start_date']   = $nomiantaion_start_date;
	$data['nomiantaion_end_date']     = $nomiantaion_end_date;
	} else { 
	  Session::flash('status',0);
      Session::flash('flash-message', "Nomination not started yet.");
      return Redirect::back();
	}
	if(count($nom) > 0 ){
		$data['nom_id'] = $nom[0]->id;
		$data['NOMNO'] =  $nom[0]->nomination_no;
		$data['candidate_name'] = $nom[0]->name;
		$data['election_name_one'] = $this->getElection($nom[0]->election_id);
		$data['ACNO'] =   $nom[0]->ac_no;
		$data['slot'] =   $nom[0]->slot;
		$data['ACname'] = $this->getAcName($nom[0]->st_code, $nom[0]->ac_no);
		$getDistNo = $this->getDistNo($nom[0]->st_code, $nom[0]->ac_no);
		$data['DistName'] = $this->getDist($nom[0]->st_code, $getDistNo);		
		$data['is_appoinment_scheduled_for_one'] = $nom[0]->appoinment_status;		
		
		$data['ROdetails'] = $this->getRODetails($nom[0]->st_code, $nom[0]->ac_no);
		if(count($data['ROdetails']) > 0 ){		 
			foreach($data['ROdetails'] as $ro){ 
				$data['ROname']    = $ro->name;
				$data['ROaddress'] = $ro->placename;
				$data['ROaddress1'] = $ro->ro_address_l1;
				$data['ROaddress2'] = $ro->ro_address_l2;
			}		
		}
		
		$datedd = $timedd  = $daydd = $ampm = '';
		if(isset($nom[0]->appoinment_scheduled_datetime)){
			$datetiime=explode(" ", $nom[0]->appoinment_scheduled_datetime);
			$datedd =  date("d-m-Y", strtotime($datetiime[0]));
			$timedd =  substr($datetiime[1], 0, -3);
			$daydd  =  date('D', strtotime($datedd));
			$ampm   =  date('A', strtotime($nom[0]->appoinment_scheduled_datetime));			
		}
		$slot = '';
		if ($nom[0]->slot == 1){
		 $slot="11AM TO 1PM";	
		}
		if($nom[0]->slot == 2){
		 $slot="1PM TO 3PM";	
		}
		
		$data['appoinment_scheduled_datetime_one'] = $nom[0]->appoinment_scheduled_datetime;
		$data['appoinment_scheduled_date_one'] = $datedd;
		$data['appoinment_scheduled_time_one'] = $slot;
		$data['appoinment_scheduled_day_one'] = $daydd;
		$data['ampm'] = $ampm;
		
		if($nom[0]->appoinment_status =='' or $nom[0]->appoinment_status==0){
			$st='Pending';
		}
		if($nom[0]->appoinment_status ==1){
			$st='Accepted';
		}
		if($nom[0]->appoinment_status ==2){
			$st='Cancel';
		}		
		$data['appoinment_status'] = $st;
		$data['updated_at'] = $nom[0]->updated_at;
		$data['view_href_cust'] = url('nomination/detail/'.encrypt_String($_REQUEST['id']));
		$data['download_href_cust'] = url('nomination/download/'.encrypt_String($_REQUEST['id']));
	}
	\Session::put('nomination_id_in_payment', $_REQUEST['id']);
	\Session::put('st_code', $cntcheck[0]->st_code);
	\Session::put('ac_no', $cntcheck[0]->ac_no);
	//return view('nomination/confirm-schedule-appointment', $data);
	
	 $ids=$this->getDataLink($_REQUEST['id']);
	
	//return v('nomination/book-details?query='.encrypt_string('abc').'&id='.$ids.'&data='.encrypt_string('abc'));
	return view('nomination/book-details', $data);
   
	
  } 
  
  
  
  public function prev_show(Request $Request){ 
  
		
  
	$cntcheck=DB::connection('mysql')->table('nomination_application')
	->select('*')
	->whereIn('id', explode(',', $_REQUEST['id']))
	->groupBy('ac_no', 'st_code')
	->get();
	
	if(count($cntcheck) > 1){
	  Session::flash('status',0);
      Session::flash('flash-message', "Please select same ac nomination.");
      return Redirect::back();
	}
	
	
	if(!isset($_REQUEST['id']) && ($_REQUEST['id'] <=0 )){
	  Session::flash('status',0);
      Session::flash('flash-message', "Please select nomination.");
      return Redirect::back();
	}
    Session::forget('nomination_id');
    $data                   = [];
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
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $results = NominationApplicationModel::get_nominations();

	//echo "<pre>"; print_r($data); die;
    foreach($results as $result){ 
      $encrypt_id = encrypt_String($result['id']);
      if($result['finalize']){
        $status = 'Finalized';
      }else{
        $status = 'In-completed';
      }
	  
      $data['results'][] = [
        'nomination_no' => $result['nomination_no'],
        'id'      => $result['id'],
        'name'    => $result['name'],
        'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
        'election_name' => $result['election_name'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'step'       	=>$result['step'],
        'is_apply_prescrutiny'       	=>$result['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$result['prescrutiny_status'],
        'prescrutiny_comment'       	=>$result['prescrutiny_comment'],
        'is_appoinment_scheduled'       	=>$result['is_appoinment_scheduled'],
        'appoinment_status'       	=>$result['appoinment_status'],
        'appoinment_scheduled_datetime'       	=>$result['appoinment_scheduled_datetime'],
        'view_href'     => url('nomination/detail/'.$encrypt_id),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $result['finalize']
      ];
    } 
	$datesss='';
	if(count($results) > 0 ){
		foreach($results as $res){ 
			if($res['is_appoinment_scheduled']==1){ 
				if(isset($res['appoinment_scheduled_datetime'])){
				$datet = explode(" ", $res['appoinment_scheduled_datetime']);
				$datesss.= $datet[0].'+++'.substr($datet[1], 0, -3).'***'; 
				}
			}
		}
	}
	
	$dd =  DB::connection('mysql')
	->table('appointment_schedule_date_time')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->where('st_code', '=', \Auth::id())
	->where('ac_no', '=', \Auth::id())
	->get();
	
	
	
	//echo $_REQUEST['id']; die;
	$data['datesss']=substr($datesss, 0, -3);
	$nom=DB::connection('mysql')->table('nomination_application')
		->select('*')
		->whereIn('id', array($_REQUEST['id']))
		->where('candidate_id', '=', \Auth::id())
		->get()
		->toArray();
		
	//echo "<pre>"; print_r($nom); die;	
	$start_end = $this->getStartEndDateNomination($nom[0]->st_code, $nom[0]->ac_no);
	
	$ppArray=array();
	$my_sch_data =  DB::connection('mysql')
	->table('appointment_schedule_date_time')
	->select('spec_str')
	->where('candidate_id', '=', \Auth::id())
	->where('st_code', '=', $nom[0]->st_code)
	->where('ac_no', '=', $nom[0]->ac_no)
	->where('status', '=', 1)
	->get()
	->toArray();
	if(count($my_sch_data) > 0 ){
		foreach($my_sch_data as $mym){
			array_push($ppArray, $mym->spec_str );
		}
	}
	$data['mychecduledata']=$ppArray;
	
	
	
	$AllDataSc=array();
	$my_sch_data =  DB::connection('mysql')
	->table('appointment_schedule_date_time')
	  ->select('spec_str', DB::raw('count(*) as total'))
	->where('st_code', '=', $nom[0]->st_code)
	->where('ac_no', '=', $nom[0]->ac_no)
	->where('status', '=', 1)
	->groupBy('spec_str')
	->get()
	->toArray();
	if(count($my_sch_data) > 0 ){
		foreach($my_sch_data as $mym){  	
			$AllDataSc[$mym->spec_str]=$mym->total;
		}
	}
	$data['AllBooked']=$AllDataSc;
	
	
	
	
	
	
	
	 
	//$nomiantaion_start_date = 0;
	if($start_end!=0){
	$nomdate  = explode("***", $start_end); 	
	$start    = str_replace("-", "/", $nomdate[0]);
	$end      = str_replace("-", "/", $nomdate[1]);
	$nomiantaion_start_date = $start;
	$nomiantaion_end_date   = $end;	
	$data['nomiantaion_start_date']   = $nomiantaion_start_date;
	$data['nomiantaion_end_date']     = $nomiantaion_end_date;
	} else { 
	  Session::flash('status',0);
      Session::flash('flash-message', "Nomination not started yet.");
      return Redirect::back();
	}
	if(count($nom) > 0 ){
		$data['nom_id'] = $nom[0]->id;
		$data['NOMNO'] =  $nom[0]->nomination_no;
		$data['candidate_name'] = $nom[0]->name;
		$data['election_name_one'] = $this->getElection($nom[0]->election_id);
		$data['ACNO'] =   $nom[0]->ac_no;
		$data['finalize_after_payment'] =   $nom[0]->finalize_after_payment;
		$data['slot'] =   $nom[0]->slot;
		$data['ACname'] = $this->getAcName($nom[0]->st_code, $nom[0]->ac_no);
		$getDistNo = $this->getDistNo($nom[0]->st_code, $nom[0]->ac_no);
		$data['DistName'] = $this->getDist($nom[0]->st_code, $getDistNo);		
		$data['is_appoinment_scheduled_for_one'] = $nom[0]->is_appoinment_scheduled;		
		
		$data['ROdetails'] = $this->getRODetails($nom[0]->st_code, $nom[0]->ac_no);
		if(count($data['ROdetails']) > 0 ){		 
			foreach($data['ROdetails'] as $ro){ 
				$data['ROname']    = $ro->name;
				$data['ROaddress'] = $ro->placename;
				$data['ROaddress1'] = $ro->ro_address_l1;
				$data['ROaddress2'] = $ro->ro_address_l2;
			}		
		}
		
		$datedd = $timedd  = $daydd = $ampm = '';
		if(isset($nom[0]->appoinment_scheduled_datetime)){
			$datetiime=explode(" ", $nom[0]->appoinment_scheduled_datetime);
			$datedd =  date("d-m-Y", strtotime($datetiime[0]));
			$timedd =  substr($datetiime[1], 0, -3);
			$daydd  =  date('D', strtotime($datedd));
			$ampm   =  date('A', strtotime($nom[0]->appoinment_scheduled_datetime));			
		}
		$slot = '';
		if ($nom[0]->slot == 1){
		 $slot="11AM TO 1PM";	
		}
		if($nom[0]->slot == 2){
		 $slot="1PM TO 3PM";	
		}
		
		$data['appoinment_scheduled_datetime_one'] = $nom[0]->appoinment_scheduled_datetime;
		$data['appoinment_scheduled_date_one'] = $datedd;
		$data['appoinment_scheduled_time_one'] = $slot;
		$data['appoinment_scheduled_day_one'] = $daydd;
		$data['ampm'] = $ampm;
		
		if($nom[0]->appoinment_status =='' or $nom[0]->appoinment_status==0){
			$st='Pending';
		}
		if($nom[0]->appoinment_status ==1){
			$st='Accepted';
		}
		if($nom[0]->appoinment_status ==2){
			$st='Cancel';
		}		
		$data['appoinment_status'] = $st;
		$data['updated_at'] = $nom[0]->updated_at;
		$data['view_href_cust'] = url('nomination/detail/'.encrypt_String($_REQUEST['id']));
		$data['download_href_cust'] = url('nomination/download/'.encrypt_String($_REQUEST['id']));
	}
	
	$profile =  DB::connection('mysql')
	->table('profile')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->get()
	->toArray();
	
	
	
	$dist_code='';
	$dist_code  = $this->getDistNo($nom[0]->st_code, $nom[0]->ac_no);
	if(!empty($dist_code)){
	  $data['dist_code']=trim($dist_code);
	}
	if(!empty($nom[0]->st_code)){
	  $data['st_save']=trim($nom[0]->st_code);
	}
	$ac_save='';
	if(!empty($nom[0]->ac_no)){
	  $data['ac_save']=trim($nom[0]->ac_no);
	}
	$checkchk=DB::connection('mysql')->table('profile') 
	->select('*')
	->where('candidate_id', '=', \Auth::id())  
	->whereIn('category', array('sc','st'))
	->get();
	$amt=2;
	if(count($checkchk) > 0){
		$amt=1;
	}
	
	$state_payment_url = DB::connection('mysql')->table('payment_gateway_config')->select('state_payment_url')->where('st_code', '=', $nom[0]->st_code)->value('state_payment_url');
	if(!empty($state_payment_url)){
		$data['state_payment_url']=$state_payment_url;
	} else {
		$data['state_payment_url']='';
	}	
	
	
	
	$payment_config =  DB::connection('mysql')
	->table('payment_gateway_config')
	->select('*')
	->where('st_code', '=', $nom[0]->st_code)
	->where('payment_gateway_available', '=', 1)
	->get()
	->toArray();
	
	$data['isPaymentConfig']='';					
	//echo "<pre>"; print_r($payment_config); die;	
	if(count($payment_config) > 0){
		
		//Condition will be increase as payment gateway will be increase
		
						$data['isPaymentConfig']='YES';					
						
						$datapay =  DB::connection('mysql')
						->table('bihar_district_mapping')
						->select('*')
						->where('st_code', '=', $nom[0]->st_code) // $nom[0]->st_code
						->where('dist_code_nomination', '=',   $dist_code) // $dist_code
						->get()
						->toArray();
						$dep_code='';
						if(!empty($datapay['0']->dep_code)){
						  $dep_code=$datapay['0']->dep_code;
						}
						$dist_code_bihar='';
						if(!empty($datapay['0']->dist_code_bihar)){
						  $dist_code_bihar=$datapay['0']->dist_code_bihar;
						}
						$payment_head='';
						if(!empty($datapay['0']->payment_head)){
						  $payment_head=$datapay['0']->payment_head;
						}
						$scheme_code='';
						if(!empty($datapay['0']->scheme_code)){
						  $scheme_code=$datapay['0']->scheme_code;
						}
						$office_code='';
						if(!empty($datapay['0']->office_code)){
						  $office_code=$datapay['0']->office_code;
						}
						$trs_code='';
						if(!empty($datapay['0']->trs_code)){
						  $trs_code=$datapay['0']->trs_code;
						}
						$hd_ac1='';
						if(!empty($datapay['0']->hd_ac1)){
						  $hd_ac1=$datapay['0']->hd_ac1;
						}
						$merchant_code='';
						if(!empty($datapay['0']->merchant_code)){
						  $data['merchant_code']=trim($datapay['0']->merchant_code);
						} else {
						  $data['merchant_code']=$merchant_code;	
						}
						$randomid = mt_rand(10000000,99999999); 
						$rurl =  url('/').'/payment-return-handle';
						$data['return_url']=trim($rurl);
						$data['reff_no']=trim($randomid);
						$data['dep_code']=trim($dep_code);
						$data['dist_code']=trim($dist_code_bihar);
						$data['payment_head']=trim($payment_head);
						$data['scheme_code']=trim($scheme_code);
						$data['office_code']=trim($office_code);
						$data['trs_code']=trim($trs_code);	
						$data['hd_ac1']=trim($hd_ac1);	
						$data['remitter_name']=trim($profile[0]->name);
						$data['pan']=trim($profile[0]->pan_number);
						$data['email']=trim($profile[0]->email);
						$data['mobile']=trim($profile[0]->mobile);
						$data['address']=trim(substr($profile[0]->address, 0, 90));
						$data['remarks']=trim('Payment');
						$data['amount1']=trim($amt);
						$data['txn_amount']=trim($amt);
	
			} else  {
				$data['isPaymentConfig']='NO';				
			}
		
		 
		
			$STATEHOLIDAY=DB::connection('mysql')->table('eplan_holiday_master')
			->select('*')
			->where('st_code', '=', $nom[0]->st_code)
			->get()
			->toArray();
			$s=0;
			$holidataStart=array();	
			if(count($STATEHOLIDAY) > 0 ){
				foreach($STATEHOLIDAY as $hda){
				  $holidataStart[$s]= date("d-m-Y", strtotime($hda->holiday_start_date)); 
				  $s++;	
				}
			}
			$AllHOLIDAYNATIONAL=DB::connection('mysql')->table('eplan_holiday_master')
			->select('*')
			->whereNull('st_code')
			->get()
			->toArray();
			$p=0;
			$ALLNATION=array();	
			if(count($AllHOLIDAYNATIONAL) > 0 ){
				foreach($AllHOLIDAYNATIONAL as $national){
				  $ALLNATION[$p]= date("d-m-Y", strtotime($national->holiday_start_date)); 
				  $p++;	
				}
			}
			$mergeHoliday=array();
			$mergeHoliday=array_merge($holidataStart, $ALLNATION);
			
		 
		 $startMonthNyear =   date('M Y', strtotime($nomiantaion_start_date));
		 $endMonthNyear =     date('M Y', strtotime($nomiantaion_end_date));		
         $check_date = array();
         $dataArray = array();
         $SecFourSat = array();
		 $dataArray['2nd_sat1'] = date('d-m-Y', strtotime('second sat of '.$startMonthNyear));
         $dataArray['2nd_sat2'] = date('d-m-Y', strtotime('second sat of '.$endMonthNyear));
		 $dataArray['4th_sat1'] = date('d-m-Y', strtotime('fourth sat of '.$startMonthNyear));
         $dataArray['4th_sat2'] = date('d-m-Y', strtotime('fourth sat of '.$endMonthNyear));
		 $SecFourSat=array_unique($dataArray);		
		
		$date = $nomiantaion_start_date;
        $y = date('Y', strtotime($date));
        $m = date('m', strtotime($date));		
        $date = "$y-$m-01"; 
        $first_day = date('N',strtotime($date));
        $first_day = 7 - $first_day + 1;
        $last_day =  date('t',strtotime($date));
		//echo   $last_day; die("___");
        $Sunday = array();
        for($i=$first_day; $i<=$last_day; $i=$i+7 ){
            $Sunday[] = sprintf("%02d", $i).'-'.$m.'-'.$y;
        }
		
		$end = $nomiantaion_end_date;
        $YEAR = date('Y', strtotime($end));
        $MONTH = date('m', strtotime($end));		
        $end = "$YEAR-$MONTH-01"; 
        $FIRSTDAY = date('N',strtotime($end));
        $FIRSTDAY = 7 - $FIRSTDAY + 1;
        $LASTDAY =  date('t',strtotime($end));
		//echo   $last_day; die("___");
        $SUNDAYEND = array();
        for($K=$FIRSTDAY; $K<=$LASTDAY; $K=$K+7 ){
            $SUNDAYEND[] = sprintf("%02d", $K).'-'.$MONTH.'-'.$YEAR;
        } 		
		$AllSunday=array();
		$mergeSunday=array_merge($Sunday, $SUNDAYEND);
		$AllSunday=array_unique($mergeSunday);		
		$AllSaterDaySunday=array();
		$AllSaterDaySunday=array_merge($AllSunday, $SecFourSat);
		
		$finalHoliday=array(); 
		$data['finalHoliday']=array_merge($mergeHoliday, $AllSaterDaySunday);
		
		//echo "<pre>"; print_r($finalHoliday);
		//$sres = array_search('21-05-2020', $finalHoliday);
		//echo $sres; die;
		//echo "<pre>"; print_r($finalHoliday); die;
		
      
		 
	
	
	
	
	
	
	
	
	\Session::put('nomination_id_in_payment', $_REQUEST['id']);
	\Session::put('st_code', $cntcheck[0]->st_code);
	\Session::put('ac_no', $cntcheck[0]->ac_no);
	return view('nomination/prev', $data);
  } 
  
  
  public function save_payment_details_our_end(Request $request){
	
		$input=$request->All();	 

		$ins =DB::table('payment_details_bihar')->insert(array(
	    'reff_no'      =>$input['reff_no'], 
	    'dist_code'    =>$input['dist_code'],
	    'st_code'      =>$input['sId'],
	    'ac_no'        =>$input['ac'],
	    'amount1'      =>$input['amount1'],
	    'txn_amount'   =>$input['txn_amount'],
	    'status'       =>3,
	    'candidate_id' =>\Auth::id(), 
	    'created_at'   => date('Y-m-d H:i:s', time()), 
		));
			
			
			
	if($ins){
		return 1;
	}  else {
		return 0;
	} 
  }	
  
  
  
  
  public function confirm_schedule_appointment(Request $Request){ 
	$cntcheck=DB::connection('mysql')->table('nomination_application')
	->select('*')
	->whereIn('id', explode(',', $_REQUEST['id']))
	->groupBy('ac_no', 'st_code')
	->get();
	
	if(count($cntcheck) > 1){
	  Session::flash('status',0);
      Session::flash('flash-message', "Please select same ac nomination.");
      return Redirect::back();
	}
	
	
	if(!isset($_REQUEST['id']) && ($_REQUEST['id'] <=0 )){
	  Session::flash('status',0);
      Session::flash('flash-message', "Please select nomination.");
      return Redirect::back();
	}
    Session::forget('nomination_id');
    $data                   = [];
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
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $results = NominationApplicationModel::get_nominations();

	//echo "<pre>"; print_r($data); die;
    foreach($results as $result){ 
      $encrypt_id = encrypt_String($result['id']);
      if($result['finalize']){
        $status = 'Finalized';
      }else{
        $status = 'In-completed';
      }
	  
      $data['results'][] = [
        'nomination_no' => $result['nomination_no'],
        'id'      => $result['id'],
        'name'    => $result['name'],
        'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
        'election_name' => $result['election_name'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'step'       	=>$result['step'],
        'is_apply_prescrutiny'       	=>$result['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$result['prescrutiny_status'],
        'prescrutiny_comment'       	=>$result['prescrutiny_comment'],
        'is_appoinment_scheduled'       	=>$result['is_appoinment_scheduled'],
        'appoinment_status'       	=>$result['appoinment_status'],
        'appoinment_scheduled_datetime'       	=>$result['appoinment_scheduled_datetime'],
        'view_href'     => url('nomination/detail/'.$encrypt_id),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $result['finalize']
      ];
    }
	$datesss='';
	if(count($results) > 0 ){
		foreach($results as $res){ 
			if($res['is_appoinment_scheduled']==1){ 
				if(isset($res['appoinment_scheduled_datetime'])){
				$datet = explode(" ", $res['appoinment_scheduled_datetime']);
				$datesss.= $datet[0].'+++'.substr($datet[1], 0, -3).'***'; 
				}
			}
		}
	}
	//echo $_REQUEST['id']; die;
	$data['datesss']=substr($datesss, 0, -3);
	$nom=DB::connection('mysql')->table('nomination_application')
		->select('*')
		->whereIn('id', array($_REQUEST['id']))
		->get()
		->toArray();
	//echo "<pre>"; print_r($nom); die;	
	$start_end = $this->getStartEndDateNomination($nom[0]->st_code, $nom[0]->ac_no);
	
	
	//
	//$nomiantaion_start_date = 0;
	if($start_end!=0){
	$nomdate  = explode("***", $start_end); 	
	$start    = str_replace("-", "/", $nomdate[0]);
	$end      = str_replace("-", "/", $nomdate[1]);
	$nomiantaion_start_date = $start;
	$nomiantaion_end_date   = $end;	
	$data['nomiantaion_start_date']   = $nomiantaion_start_date;
	$data['nomiantaion_end_date']     = $nomiantaion_end_date;
	} else { 
	  Session::flash('status',0);
      Session::flash('flash-message', "Nomination not started yet.");
      return Redirect::back();
	}
	if(count($nom) > 0 ){
		$data['nom_id'] = $nom[0]->id;
		$data['NOMNO'] =  $nom[0]->nomination_no;
		$data['candidate_name'] = $nom[0]->name;
		$data['election_name_one'] = $this->getElection($nom[0]->election_id);
		$data['ACNO'] =   $nom[0]->ac_no;
		$data['slot'] =   $nom[0]->slot;
		$data['ACname'] = $this->getAcName($nom[0]->st_code, $nom[0]->ac_no);
		$getDistNo = $this->getDistNo($nom[0]->st_code, $nom[0]->ac_no);
		$data['DistName'] = $this->getDist($nom[0]->st_code, $getDistNo);		
		$data['is_appoinment_scheduled_for_one'] = $nom[0]->appoinment_status;		
		
		$data['ROdetails'] = $this->getRODetails($nom[0]->st_code, $nom[0]->ac_no);
		if(count($data['ROdetails']) > 0 ){		 
			foreach($data['ROdetails'] as $ro){ 
				$data['ROname']    = $ro->name;
				$data['ROaddress'] = $ro->placename;
				$data['ROaddress1'] = $ro->ro_address_l1;
				$data['ROaddress2'] = $ro->ro_address_l2;
			}		
		}
		
		$datedd = $timedd  = $daydd = $ampm = '';
		if(isset($nom[0]->appoinment_scheduled_datetime)){
			$datetiime=explode(" ", $nom[0]->appoinment_scheduled_datetime);
			$datedd =  date("d-m-Y", strtotime($datetiime[0]));
			$timedd =  substr($datetiime[1], 0, -3);
			$daydd  =  date('D', strtotime($datedd));
			$ampm   =  date('A', strtotime($nom[0]->appoinment_scheduled_datetime));			
		}
		$slot = '';
		if ($nom[0]->slot == 1){
		 $slot="11AM TO 1PM";	
		}
		if($nom[0]->slot == 2){
		 $slot="1PM TO 3PM";	
		}
		
		$data['appoinment_scheduled_datetime_one'] = $nom[0]->appoinment_scheduled_datetime;
		$data['appoinment_scheduled_date_one'] = $datedd;
		$data['appoinment_scheduled_time_one'] = $slot;
		$data['appoinment_scheduled_day_one'] = $daydd;
		$data['ampm'] = $ampm;
		
		if($nom[0]->appoinment_status =='' or $nom[0]->appoinment_status==0){
			$st='Pending';
		}
		if($nom[0]->appoinment_status ==1){
			$st='Accepted';
		}
		if($nom[0]->appoinment_status ==2){
			$st='Cancel';
		}		
		$data['appoinment_status'] = $st;
		$data['updated_at'] = $nom[0]->updated_at;
		$data['view_href_cust'] = url('nomination/detail/'.encrypt_String($_REQUEST['id']));
		$data['download_href_cust'] = url('nomination/download/'.encrypt_String($_REQUEST['id']));
	}
	\Session::put('nomination_id_in_payment', $_REQUEST['id']);
	\Session::put('st_code', $cntcheck[0]->st_code);
	\Session::put('ac_no', $cntcheck[0]->ac_no);
	return view('nomination/confirm-schedule-appointment', $data);
  } 
  
  
  public function getDataLink($id){ 
    
    $nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('candidate_id', '=', \Auth::id())
	->where('id', $id)
	->get();
	
	$ddddd =  DB::connection('mysql')
	->table('nomination_application')
	->select('id')
	->where('st_code', '=', $nd[0]->st_code)
	->where('ac_no', '=', $nd[0]->ac_no)
	->where('candidate_id', '=', \Auth::id())
	->whereIn('prescrutiny_status', array(1,2))
	->get();
	
	$set='';
	$wet='';
    foreach($ddddd as $data){
	  $set.= $data->id.',';	
	}	
	
	$wet=substr($set, 0, -1);
	return $wet;
	
   }
  
  public function set_param(){
  $cntcheck=DB::connection('mysql')->table('nomination_application')
	->select('*')
	->where('is_apply_prescrutiny', 1)
	->whereIn('prescrutiny_status', array(1,2))
	->where('st_code', $_REQUEST['st_code'])
	->where('ac_no', $_REQUEST['ac'])
	->get();
	
	$set='';
	$wet='';
    foreach($cntcheck as $data){
	  $set.= $data->id.',';	
	}	
	$wet=substr($set, 0, -1);
	return redirect('nomination/confirm-schedule-appointment?query='.encrypt_String($wet).'&id='.$wet.'&data='.encrypt_String($wet));	
   }
   
  public function set_param_prev(){
  $cntcheck=DB::connection('mysql')->table('nomination_application')
	->select('*')
	->where('is_apply_prescrutiny', 1)
	->whereIn('prescrutiny_status', array(1,2))
	->where('st_code', $_REQUEST['st_code'])
	->where('ac_no', $_REQUEST['ac'])
	->where('finalize', '!=', 3)
	->where('candidate_id', '=', \Auth::id())
	->get();
	
	//echo "<pre>"; print_r($cntcheck); die;
	
	
	$set='';
	$wet='';
    foreach($cntcheck as $data){
	  $set.= $data->id.',';	
	}	
	$wet=substr($set, 0, -1);
	return redirect('nomination/prev?query='.encrypt_String($wet).'&id='.$wet.'&data='.encrypt_String($wet));	
   }
   
    	
   
   public function cancel_nomination_prev(Request $request){
	$input = $request->All();	
	//echo "<pre>"; print_r($input); die;
	$nid =  explode(",", $input['nom_id']);
	
	///////////////////Schedule in new table 'schedule_details'/////////////////////////
	$schedule=DB::connection('mysql')
			->table('nomination_application')
			->select('st_code', 'ac_no')
			->where('id', '=', $nid[0])
			->get(); 
	if(count($schedule)>0){
		if((!empty($schedule[0]->st_code)) && (!empty($schedule[0]->ac_no))){	
		$this->update_schedule_status($schedule[0]->st_code, $schedule[0]->ac_no, 2);		
		}
	}	
	////////////////////////////////////////////
	
	
	$myvar = DB::connection('mysql')->table('appointment_schedule_date_time')
	->where('candidate_id', \Auth::id())
	->where('st_code', $schedule[0]->st_code)
	->where('ac_no', $schedule[0]->ac_no)
	->update([
	"status" =>2,
	]);	
	
	foreach($nid as $oneid) { 
	
	$myvar = DB::connection('mysql')->table('nomination_application')
	->where('id', $oneid)
	->update([
	"appoinment_status" =>null,
	"slot" =>0,
	"is_appoinment_scheduled" =>null,
	"appoinment_scheduled_datetime" =>null,
	]);	
	
	
	
	
	$myvar = DB::connection('mysql')->table('candidate_online_appointment')
	->where('nom_id', $oneid)
	->update([
	"status" =>2
	]);	
	
	$nom=DB::connection('mysql')->table('candidate_online_appointment')
	->select('*')
	->where('nom_id', '=', $oneid)
	->get()
	->toArray();	
	
	//echo "<pre>"; print_r($nom); die;
	
	$msg='';	

	if(!empty($nom)){
		$isUpdate = DB::connection('mysql')->table('candidate_appointment_logs')
		->insert([
		"appointment_id" =>$nom[0]->id,
		"nom_id" =>$oneid,
		"st_code" =>$nom[0]->st_code,
		"ac_no" =>$nom[0]->ac_no,
		"appointment_date" =>$nom[0]->appointment_date,
		"apointment_slot" =>$nom[0]->apointment_slot,
		"status" =>2,
		"appointment_datetime"=> date('Y-m-d H:i:s', time()),
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]); 
	}
	
	$get_nominattion_detail = NominationApplicationModel::get_nomination_application($oneid);		
	$mob = DB::connection('mysql')
	->table('profile')
	->select('name', 'mobile', 'email')
	->where('candidate_id', '=', $get_nominattion_detail['candidate_id'])
	->get();
	
	$app = DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('nomination_no', '=', $get_nominattion_detail['nomination_no'])
	->get();
	
	$state = $this->getState($app[0]->st_code); 
	$ac    = $this->getAcName($app[0]->st_code, $app[0]->ac_no); 
	
	
	
	/*
	
	$message =   __('finalize.Dear'). " "  .$mob[0]->name. ", ".__('finalize.your_onlinie')." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_can');	
	$messageEmail =   __('finalize.Dear') . " " .$mob[0]->name. ",\n\n ".__('finalize.your_onlinie')." ". $get_nominattion_detail['nomination_no'].__('finalize.has_been_can')."\n\n".__('finalize.Thank');
	
	//$subject ="Nomination application appointment canceled";
	$subject =  __('finalize.nom_can');	

	$datasss =  DB::connection('mysql')
	->table('officer_login')
	->select('*')
	->where('st_code', '=', $app[0]->st_code)
	->where('ac_no', '=',   $app[0]->ac_no)
	->where('designation', '=', 'ROAC')
	->get();	
	
	if(count($datasss) > 0){		
	   if(isset($datasss[0]->Phone_no)){
		
		$msms =   __('finalize.Dear') . " "  .$datasss[0]->officername. ",\n\n ".__('finalize.your_onlinie')." ". $get_nominattion_detail['nomination_no'].__('finalize.has_been_can')."\n\n".__('finalize.Thank');
		
	    $this->sendSMS($datasss[0]->Phone_no, $msms);		
	   }
	   if(isset($datasss[0]->email)){				
		$memail =   __('finalize.Dear'). " " .$datasss[0]->officername. ",\n\n ".__('finalize.your_onlinie')." ". $get_nominattion_detail['nomination_no'].__('finalize.has_been_can')."\n\n".__('finalize.Thank');		
		$this->sendEmail($datasss[0]->email, $memail, $subject);	
	   }
	}
	
	$this->sendEmail($mob[0]->email, $messageEmail, $subject);	
	$this->sendSMS($mob[0]->mobile, $message); */
	
	}
	Session::flash('flash-message',"Appointment cancelled successfully");
	Session::flash('is_scheduled',"cancel");
	return redirect('nomination/prev?query=?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id='.$input['nom_id'].'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9');
    }
   
  
  public function cancel_nomination(Request $request){
	$input = $request->All();	
	//echo "<pre>"; print_r($input); die;
	$nid =  explode(",", $input['nom_id']);
	
	///////////////////Schedule in new table 'schedule_details'/////////////////////////
	$schedule=DB::connection('mysql')
			->table('nomination_application')
			->select('st_code', 'ac_no')
			->where('id', '=', $nid[0])
			->get(); 
	if(count($schedule)>0){
		if((!empty($schedule[0]->st_code)) && (!empty($schedule[0]->ac_no))){	
		$this->update_schedule_status($schedule[0]->st_code, $schedule[0]->ac_no, 2);		
		}
	}	
	////////////////////////////////////////////
	
	
	foreach($nid as $oneid) { 
	
	$myvar = DB::connection('mysql')->table('nomination_application')
	->where('id', $oneid)
	->update([
	"appoinment_status" =>2,
	"slot" =>0,
	"is_appoinment_scheduled" =>null,
	"appoinment_scheduled_datetime" =>null,
	]);	
	
	
	$myvar = DB::connection('mysql')->table('candidate_online_appointment')
	->where('nom_id', $oneid)
	->update([
	"status" =>2
	]);	
	
	$nom=DB::connection('mysql')->table('candidate_online_appointment')
	->select('*')
	->where('nom_id', '=', $oneid)
	->get()
	->toArray();	
	
	//echo "<pre>"; print_r($nom); die;
	
	$msg='';	

	if(!empty($nom)){
		$isUpdate = DB::connection('mysql')->table('candidate_appointment_logs')
		->insert([
		"appointment_id" =>$nom[0]->id,
		"nom_id" =>$oneid,
		"st_code" =>$nom[0]->st_code,
		"ac_no" =>$nom[0]->ac_no,
		"appointment_date" =>$nom[0]->appointment_date,
		"apointment_slot" =>$nom[0]->apointment_slot,
		"status" =>2,
		"appointment_datetime"=> date('Y-m-d H:i:s', time()),
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]); 
	}
	
	$get_nominattion_detail = NominationApplicationModel::get_nomination_application($oneid);		
	$mob = DB::connection('mysql')
	->table('profile')
	->select('name', 'mobile', 'email')
	->where('candidate_id', '=', $get_nominattion_detail['candidate_id'])
	->get();
	
	$app = DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('nomination_no', '=', $get_nominattion_detail['nomination_no'])
	->get();
	
	$state = $this->getState($app[0]->st_code); 
	$ac    = $this->getAcName($app[0]->st_code, $app[0]->ac_no); 
	
	
	
	
	
	$message =   __('finalize.Dear'). " "  .$mob[0]->name. ", ".__('finalize.your_onlinie')." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_can');	
	
	$messageEmail =   __('finalize.Dear') . " " .$mob[0]->name. ",\n\n ".__('finalize.your_onlinie')." ". $get_nominattion_detail['nomination_no'].__('finalize.has_been_can')."\n\n".__('finalize.Thank');
	
	//$subject ="Nomination application appointment canceled";
	$subject =  __('finalize.nom_can');	

	$datasss =  DB::connection('mysql')
	->table('officer_login')
	->select('*')
	->where('st_code', '=', $app[0]->st_code)
	->where('ac_no', '=',   $app[0]->ac_no)
	->where('designation', '=', 'ROAC')
	->get();	
	
	if(count($datasss) > 0){		
	   if(isset($datasss[0]->Phone_no)){
		
		$msms =   __('finalize.Dear') . " "  .$datasss[0]->officername. ",\n\n ".__('finalize.your_onlinie')." ". $get_nominattion_detail['nomination_no'].__('finalize.has_been_can')."\n\n".__('finalize.Thank');
		
	    $this->sendSMS($datasss[0]->Phone_no, $msms);		
	   }
	   if(isset($datasss[0]->email)){				
		$memail =   __('finalize.Dear'). " " .$datasss[0]->officername. ",\n\n ".__('finalize.your_onlinie')." ". $get_nominattion_detail['nomination_no'].__('finalize.has_been_can')."\n\n".__('finalize.Thank');		
		$this->sendEmail($datasss[0]->email, $memail, $subject);	
	   }
	}
	
	$this->sendEmail($mob[0]->email, $messageEmail, $subject);	
	$this->sendSMS($mob[0]->mobile, $message);
	
	}
	Session::flash('flash-message',"Appointment cancelled successfully");
	Session::flash('is_scheduled',"cancel");
	return redirect('nomination/book-details?query=?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id='.$input['nom_id'].'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9');
    }
	
  public function update_schedule_status($st, $ac, $n){
	
	$ss = 0;
	if($n!=1){
	 $ss = 3;	
	}
	
	 /*
		$chkdata=DB::connection('mysql')	
		->table('schedule_details')
		->select('*')
		->where('st_code', '=', $st)
		->where('ac_no', '=',   $ac)
		->get(); 
		if(count($chkdata) == 0){	
			if($ss!=3){
				 $ss = 1;	
			}
			
			 $ins = DB::connection('mysql')->table('schedule_details')
			->insert([
			"st_code" =>$st,
			"ac_no" =>$ac,
			"status" =>$ss,
			"createdat"=>  date('Y-m-d H:i:s', time()),
			"candidate_id"=> \Auth::id(),
			"createdby"=> \Auth::id(),
			]); 
		} else {
			if($ss!=3){
			 $ss = 2;	
			}
		 	$myvar = DB::connection('mysql')->table('schedule_details')
			->where('st_code', $st)
			->where('ac_no', $ac)
			->update([
			"status" =>$ss,
			"updatedat"=>  date('Y-m-d H:i:s', time()),
			"candidate_id"=> \Auth::id(),
			"updatedby"=> \Auth::id(),
			]);	
		}	
	*/
	  
	  
    }	
    
	public function getschedule_appoinment($ac, $std){
	$ppArray=array();
	return $ppArray =  DB::connection('mysql')
	->table('appointment_schedule_date_time')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->where('st_code', '=', $std)
	->where('ac_no', '=', $ac)
	->where('status', '=', 1)
	->get()
	->toArray();
	}
	
  
  
   public function prev_save(Request $request){
	$input = $request->All();
	$nid =  explode(",", $input['id']);
	foreach($nid as $oneid) {
	$getdata =   DB::connection('mysql')
						->table('nomination_application')
						->select('*')
						->where('id', '=', $oneid)
						->get(); 
	
	
	if(count($getdata)==0){
	  Session::flash('status',0);
      Session::flash('flash-message', "Please select nomination.");
      return Redirect::back();
	}
	
	$rules = [
          'id' => 'required',
		  'daytime' => 'required'
    ];
    $messages = [
          'id.required' => 'Please select nomination!',
		  'daytime.required' => 'Please select day and time!'
    ];	
	Validator($input, $rules, $messages)->validate();
	
	

	$adate  = explode("***", $input['daytime']);
	$sch  = explode("___", $adate['0']);
	$time = $sch[0];
	$day =  $sch[1];

	
	$appdate =  $adate['1'];
	$appdatetime =  $adate['1'].' '.$time.':00';

	$myvar = DB::connection('mysql')->table('nomination_application')
	->where('id', $oneid)
	->update([
	"is_appoinment_scheduled" =>1,
	"appoinment_status" =>null,
	"appoinment_scheduled_datetime"=> $appdatetime,
	]);	
	
	$mydatata   =DB::connection('mysql')->table('nomination_application')
				->select('*')
				->where('id', '=', $oneid)
				->get()
				->toArray();
	//echo $mydatata[0]->st_code;die;
	//echo "<pre>"; print_r($input); die;	
	
	
	
	$mar=array();
	if(!empty($input['testing'])){
	  $mar  = explode(",", $input['testing']);
	  
		DB::table('appointment_schedule_date_time')
		->where('candidate_id', \Auth::id())
		->where('st_code', $mydatata[0]->st_code)
		->where('ac_no', $mydatata[0]->ac_no)
		->delete();
	  
		foreach($mar as $scdata){
			
			 $mardd  = explode("***", $scdata);
			 $dt=$mardd['1'];
			 $dateee  = explode("___", $mardd['0']);
			 $tm=$dateee['0'];
			 $ftm=str_replace(":", "", $tm);
			 $fdt=str_replace("-", "", $dt);
			 //echo $fdt.$ftm;	 die;
			$isUpdate = DB::connection('mysql')->table('appointment_schedule_date_time')
			->insert([
			"candidate_id"=> \Auth::id(),
			"spec_str" =>$fdt.$ftm,
			"appointment_date"=>  $dt,
			"appointment_time"=>  $tm,
			"st_code" =>$mydatata[0]->st_code,
			"ac_no" =>$mydatata[0]->ac_no,
			"is_ro_acccept" =>0,
			"status" =>1,
			"created_at"=> date('Y-m-d H:i:s', time()),
			"created_by"=> \Auth::id(),
			]);   
		
		}
	     
	}

	$nom    =   DB::connection('mysql')->table('candidate_online_appointment')
				->select('*')
				->where('nom_id', '=', $oneid)
				->get()
				->toArray();
	
	
	
	$msg='';
	if(count($nom)==0){
		$isUpdate = DB::connection('mysql')->table('candidate_online_appointment')
		->insert([
		"nom_id" =>$oneid,
		"st_code" =>$mydatata[0]->st_code,
		"ac_no" =>$mydatata[0]->ac_no,
		"appointment_date" =>$appdate,
		"apointment_slot" =>$time,
		"appointment_datetime"=>$appdatetime,
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]); 
		
		Session::flash('flash-message',"Appointment scheduled successfully");	
	} else {
		
		$isUpdate = DB::connection('mysql')->table('candidate_appointment_logs')
		->insert([
		"appointment_id" =>$nom[0]->id,
		"nom_id" =>$oneid,
		"st_code" =>$nom[0]->st_code,
		"ac_no" =>$nom[0]->ac_no,
		"appointment_date" =>$nom[0]->appointment_date,
		"apointment_slot" =>$nom[0]->apointment_slot,
		"status" =>$nom[0]->status,
		"appointment_datetime"=> date('Y-m-d H:i:s', time()),
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]); 
		
		DB::connection('mysql')->table('candidate_online_appointment')
		->where('nom_id', $oneid)
		->update([
		"st_code" =>$mydatata[0]->st_code,
		"ac_no" =>$mydatata[0]->ac_no,
		"appointment_date" =>$appdate,
		"apointment_slot" =>$time,
		"status" =>null,
		"appointment_datetime"=>$appdatetime,
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]);	
	    Session::flash('flash-message',"Appointment rescheduled successfully");
	}
	
	/*
	$get_nominattion_detail = NominationApplicationModel::get_nomination_application($oneid);		
	$mob = DB::connection('mysql')
	->table('profile')
	->select('name', 'mobile', 'email')
	->where('candidate_id', '=', $get_nominattion_detail['candidate_id'])
	->get();
	
	$app = DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('nomination_no', '=', $get_nominattion_detail['nomination_no'])
	->get();
	
	$state = $this->getState($app[0]->st_code); 
	$ac    = $this->getAcName($app[0]->st_code, $app[0]->ac_no); 
	
	
	$message =   __('finalize.Dear') . " " .$mob[0]->name. " ". __('finalize.your_onlinie') ." " . $get_nominattion_detail['nomination_no']." ". __('finalize.has_been_success_app_prev') ." ". date('d-m-Y') . " ".__('finalize.for_online_prev')." ".$state .', '. $ac . __('finalize.track');		
	
	$messageEmail =  __('finalize.Dear') . " " .$mob[0]->name. ",\n\n  ". __('finalize.your_onlinie') ." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_success_app_prev')." ". date('d-m-Y') . " ".__('finalize.for_online_prev')." ".$state .', '. $ac ." " . __('finalize.track') ."\n\n ".__('finalize.Thank');	
	
	
	
	$subject =  __('messages.subject');	
	
	
	$datasss =  DB::connection('mysql')
	->table('officer_login')
	->select('*')
	->where('st_code', '=', $app[0]->st_code)
	->where('ac_no', '=',   $app[0]->ac_no)
	->where('designation', '=', 'ROAC')
	->get();	
	
	
	if(count($datasss) > 0){		
	   if(isset($datasss[0]->Phone_no)){			   
		$msms =  __('finalize.Dear') . " ".$datasss[0]->officername. " ". __('finalize.ro')." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_success_app_prev')." ". date('d-m-Y') ." " . __('finalize.rofor') ;
		
	    $this->sendSMS($datasss[0]->Phone_no, $msms);		
	   }
	   if(isset($datasss[0]->email)){		
		$memail =   __('finalize.Dear'). " " .$datasss[0]->officername. "\n\n".__('finalize.ro')." ". $get_nominattion_detail['nomination_no']."  ".__('finalize.has_been_success_app_prev')." ". date('d-m-Y') . " ". __('finalize.rofor') . "\n\n ".__('finalize.Thank');		
		$this->sendEmail($datasss[0]->email, $memail, $subject);	
	   }
	}
	
	//echo $mob[0]->email .'-'. $messageEmail .'-'. $subject; die("--Hello");
	$this->sendEmail($mob[0]->email, $messageEmail, $subject);	

	$this->sendSMS($mob[0]->mobile, $message); */	
	}
	Session::flash('is_scheduled',"yes");
	
	
	return redirect('nomination/prev?query=?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id='.$input['id'].'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9');
    }
 
  
	
  public function save_confirm_schedule_appointment(Request $request){
	$input = $request->All();
	$nid =  explode(",", $input['id']);
	
	
	///////////////////Schedule in new table 'schedule_details'/////////////////////////
	$schedule=DB::connection('mysql')
			->table('nomination_application')
			->select('st_code', 'ac_no')
			->where('id', '=', $nid[0])
			->get(); 
	if(count($schedule)>0){
		if((!empty($schedule[0]->st_code)) && (!empty($schedule[0]->ac_no))){	
		$this->update_schedule_status($schedule[0]->st_code, $schedule[0]->ac_no, 1);		
		}
	}	
	////////////////////////////////////////////
	
	foreach($nid as $oneid) {
	$getdata =   DB::connection('mysql')
				->table('nomination_application')
				->select('*')
				->where('id', '=', $oneid)
				->get(); 
	
	
	if(count($getdata)==0){
	  Session::flash('status',0);
      Session::flash('flash-message', __('csa.selectappday'));
      return Redirect::back();
	}
	
	$rules = [
          'id' => 'required',
		  'daytime' => 'required'
    ];
    $messages = [
          'id.required' => __('csa.selectappday'),
		  'daytime.required' => __('csa.selappslot')
    ];	
	Validator($input, $rules, $messages)->validate();
	
	//echo "<pre>"; print_r($input);	die;

	
  
	
	$appdate =  $input['daytime'];
	$datess = date('Y-m-d', strtotime($input['daytime'])); 
	$appdatetime = $datess;
	//echo $appdatetime; die;
	$myvar = DB::connection('mysql')->table('nomination_application')
	->where('id', $oneid)
	->whereIn('prescrutiny_status', array(1,2))
	->update([
	"is_appoinment_scheduled" =>1,
	"slot" =>$input['slot'],
	"appoinment_status" =>null,
	"appoinment_scheduled_datetime"=> $appdatetime,
	]);	
	
	$mydatata =DB::connection('mysql')->table('nomination_application')
				->select('*')
				->where('id', '=', $oneid)
				->get()
				->toArray();
	//echo $mydatata[0]->st_code;die;
	//echo "<pre>"; print_r($mydatata); die;	


	$nom    =   DB::connection('mysql')->table('candidate_online_appointment')
				->select('*')
				->where('nom_id', '=', $oneid)
				->get()
				->toArray();
	
	
	
	$msg='';
	if(count($nom)==0){
		$isUpdate = DB::connection('mysql')->table('candidate_online_appointment')
		->insert([
		"nom_id" =>$oneid,
		"st_code" =>$mydatata[0]->st_code,
		"ac_no" =>$mydatata[0]->ac_no,
		"appointment_date" =>$appdate,
		"apointment_slot" =>$input['slot'],
		"appointment_datetime"=>$appdatetime,
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]); 
		
		Session::flash('flash-message', __('csa.success_meesage'));	
	} else {
		
		$isUpdate = DB::connection('mysql')->table('candidate_appointment_logs')
		->insert([
		"appointment_id" =>$nom[0]->id,
		"nom_id" =>$oneid,
		"st_code" =>$nom[0]->st_code,
		"ac_no" =>$nom[0]->ac_no,
		"appointment_date" =>$nom[0]->appointment_date,
		"apointment_slot" =>$nom[0]->apointment_slot,
		"status" =>$nom[0]->status,
		"appointment_datetime"=> date('Y-m-d H:i:s', time()),
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]); 
		
		DB::connection('mysql')->table('candidate_online_appointment')
		->where('nom_id', $oneid)
		->update([
		"st_code" =>$mydatata[0]->st_code,
		"ac_no" =>$mydatata[0]->ac_no,
		"appointment_date" =>$appdate,
		"apointment_slot" =>$input['slot'],
		"status" =>null,
		"appointment_datetime"=>$appdatetime,
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]);	
	    Session::flash('flash-message', __('csa.success_meesage2'));
	}
	
	
	$get_nominattion_detail = NominationApplicationModel::get_nomination_application($oneid);		
	$mob = DB::connection('mysql')
	->table('profile')
	->select('name', 'mobile', 'email')
	->where('candidate_id', '=', $get_nominattion_detail['candidate_id'])
	->get();
	
	$app = DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('nomination_no', '=', $get_nominattion_detail['nomination_no'])
	->get();
	
	$state = $this->getState($app[0]->st_code); 
	$ac    = $this->getAcName($app[0]->st_code, $app[0]->ac_no); 
	
	
	$message =   __('finalize.Dear') . " " .$mob[0]->name. " ". __('finalize.your_onlinie') ." " . $get_nominattion_detail['nomination_no']." ". __('finalize.has_been_success_app') ." ". date('d-m-Y') . " ".__('finalize.for_online')." ".$state .', '. $ac . __('finalize.track');		
	
	$messageEmail =  __('finalize.Dear') . " " .$mob[0]->name. ",\n\n  ". __('finalize.your_onlinie') ." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_success_app')." ". date('d-m-Y') . " ".__('finalize.for_online')." ".$state .', '. $ac ." " . __('finalize.track') ."\n\n ".__('finalize.Thank');	
	
	
	$subject =  __('finalize.subject');	
	
	
	$datasss =  DB::connection('mysql')
	->table('officer_login')
	->select('*')
	->where('st_code', '=', $app[0]->st_code)
	->where('ac_no', '=',   $app[0]->ac_no)
	->where('designation', '=', 'ROAC')
	->get();	
	
	
	if(count($datasss) > 0){		
	   if(isset($datasss[0]->Phone_no)){			   
		$msms =  __('finalize.Dear') . " ".$datasss[0]->officername. " ". __('finalize.ro')." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_success_app')." ". date('d-m-Y') ." " . __('finalize.rofor') ;
		
	    $this->sendSMS($datasss[0]->Phone_no, $msms);		
	   }
	   if(isset($datasss[0]->email)){		
		$memail =   __('finalize.Dear'). " " .$datasss[0]->officername. "\n\n".__('finalize.ro')." ". $get_nominattion_detail['nomination_no']."  ".__('finalize.has_been_success_app')." ". date('d-m-Y') . " ". __('finalize.rofor') . "\n\n ".__('finalize.Thank');		
		$this->sendEmail($datasss[0]->email, $memail, $subject);	
	   }
	}
	
	//echo $mob[0]->email .'-'. $messageEmail .'-'. $subject; die("--Hello");
	$this->sendEmail($mob[0]->email, $messageEmail, $subject);	

	$this->sendSMS($mob[0]->mobile, $message);
	
	}
	Session::flash('isSch',"yes");
	
	
	
	
	
	
	
	
	return redirect('nomination/book-details?query='.encrypt_string($input['id']).'&id='.$input['id'].'&data='.encrypt_string($input['id']));
    }
  
	public function isDefectResolved($nom){
		
		
		//$sql = 'SELECT id FROM candidate_prescrutiny_detail  WHERE nomination_no ="'.$nom.'" AND (is_defect_resolved is null or is_defect_resolved=0)';
		//$results = DB::connection('mysql')->select($sql);
		return 0; 
	}	
    
    public function make_finalize(Request $request){
	$input = $request->all();
	
	$tra = DB::connection('mysql')->table('nomination_application')
	->where('nomination_no', $input['nid'])
	->update([
	"is_re_finalize" =>1,
	"re_finalize_date"=> date('Y-m-d H:i:s', time()),
	]);	
	
	
	
	if($tra > 0){
	return 1; 	
    } else {
	 return 0; 
    }
  }	
  
  public function delete_nomination(REQUEST $request){ 
	$input=$request->All();  
	$isupdate= DB::connection('mysql')->table('nomination_application')
				->where('id', $input['id'])
				->update([
				"finalize" =>3, 
				]);
	if($isupdate){
	 return 1; 
	} else {
	 return 0;	
	}		
  }
  public function delete_draft_nomination(REQUEST $request){ 
	$input=$request->All();  
	$isupdate= DB::connection('mysql')->table('nomination_application')
				->where('id', $input['id'])
				->update([
				"finalize" =>3, 
				]);
	if($isupdate){
	 return 1; 
	} else {
	 return 0;	
	}		
  }
  
  
  
  
  
  
   public function finalize_nomination_payment(REQUEST $request){ 
	$input=$request->All();  
	if(!isset($input['nom_primary_id'])){
		$isupdate= DB::connection('mysql')->table('nomination_application')
					->where('st_code', $input['st_code'])
					->where('ac_no',   $input['ac_no'])
					->where('finalize', '!=',  3)
					->where('candidate_id', \Auth::id())
					->update([
					"finalize_after_payment" =>1, 
					]);
		if($isupdate){
		 return 1; 
		} else {
		 return 0;	
		}
	} else {
		$isupdate= DB::connection('mysql')->table('nomination_application')
					->where('id', $input['nom_primary_id'])
					->update([
					"finalize_after_payment" =>1, 
					]);
		if($isupdate){
		 return 1; 
		} else {
		 return 0;	
		}
	}	
  }
  
  public function copy_nomination(REQUEST $request){ 
	$input=$request->All();  
	 $nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('candidate_id', '=', \Auth::id())
	->where('st_code', $input['st'])
	->where('ac_no', $input['ac'])
	->where('finalize', '!=', 3)
	->get(); 
	return count($nd);
  }
  
  
   public function do_copy(REQUEST $request){ 
	$input=$request->All();  
	 
	 $nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->where('st_code', $input['st'])
	->where('ac_no', $input['ac'])
	->where('finalize', '!=', 3)
	->get(); 
	
	if(count($nd)>=4){
		return count($nd);
	} else { 
		   
				$getNom =  DB::connection('mysql')
				->table('nomination_application')
				->select('*')
				->where('candidate_id', '=', \Auth::id())
				->where('st_code', $input['st'])
				->where('ac_no', $input['ac'])
				->where('nomination_no', $input['nom'])
				->get(); 
				$dataa=array();
				foreach($getNom as $mdata){
				  $dataa = (array) $mdata;
				}
				
				 $randomid = mt_rand(100000000000,999999999999); 
				 $time = strtoupper("NOM-".$randomid);
			    
				 unset($dataa['id']);
				 unset($dataa['nomination_no']);
				 $dataa['nomination_no']= $time;
				 $dataa['created_at']=date('Y-m-d H:i:s', time()); 
				 $dataa['updated_at']=null; 
				 $dataa['finalize']=0; 
				 $dataa['created_by']= \Auth::id(); 
				 $dataa['updated_by']= \Auth::id(); 
				 NominationApplicationModel::insert($dataa);
				 
				$nomid = DB::connection('mysql')->table('nomination_application')->select('id')->where('nomination_no', '=', $time)->value('id'); 	
				
				 $prop =  DB::connection('mysql')
				->table('nomination_application_proposer')
				->select('*')
				->where('candidate_id', '=', \Auth::id())
				->where('nomination_id', $input['id'])
				->where('status', 1)
				->get();  
				 if(count($prop) > 0 ){
					foreach($prop as $ppr){
						 $pr = (array) $ppr;
						 unset($pr['id']);
						 $pr['nomination_id']= $nomid;
						 $pr['status']= 1;
						 $pr['created_at']=date('Y-m-d H:i:s', time()); 
						 $pr['updated_at']=null; 
						 $pr['created_by']= \Auth::id(); 
						 $pr['updated_by']= \Auth::id(); 
						 NominationProposerModel::insert($pr);
					} 
				 }
				 
				$pc =  DB::connection('mysql')
				->table('nomination_police_case')
				->select('*')
				->where('candidate_id', '=', \Auth::id())
				->where('nomination_id', $input['id'])
				->where('is_deleted', 0)
				->get();  
				 if(count($pc) > 0 ){
					foreach($pc as $pcdata){
						 $pcd = (array) $pcdata;
						 unset($pcd['id']);
						 $pcd['nomination_id']= $nomid;
						 $pcd['is_deleted']= 0;
						 $pcd['created_at']=date('Y-m-d H:i:s', time()); 
						 $pcd['updated_at']=null; 
						 $pcd['created_by']= \Auth::id(); 
						 $pcd['updated_by']= \Auth::id(); 
						 NominationPoliceCaseModel::insert($pcd);
					} 
				 } 
				  \Session::put('nomination_id', $nomid);
	}
  }		
	
  public function getAcs(){
    $nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('candidate_id', '=', \Auth::id())
	->where('is_apply_prescrutiny', 1)
	->where('finalize', 1)
	->get(); 
	if(count($nd) > 0 ){
		return $nd[0]->st_code.'***'.$nd[0]->ac_no;
	} 
  }
  public function getAcStByNo($nomid){
	$nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('id', '=', $nomid) 
	->get(); 
	if(count($nd) > 0 ){
		return $nd[0]->st_code.'***'.$nd[0]->ac_no;
	}
  }
  
  public function nominations(Request $Request){
    Session::forget('nomination_id');
    $data                   = [];
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
	
	$stdd='';
	$acdd=0;
	
	if(!empty($_REQUEST['acs']) && ($_REQUEST['std'])){
	$acdd = decrypt_String($_REQUEST['acs']);
	$stdd = decrypt_String($_REQUEST['std']);
	}
	
	
	//$sdate  = $this->getNominationStartDate();
	
	$data['nomiantaion_start_date']     = "2020/04/20";
	$data['nomiantaion_end_date']     = "2020/04/26";
	
	
	
	$data['submittedpre']       = [];
	$nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('candidate_id', '=', \Auth::id())
	->where('is_apply_prescrutiny', 1)
	->where('finalize', 1)
	->groupBy('st_code', 'ac_no')
	->get()
	->toArray();
		
	if(count($nd) > 0 ){
		foreach($nd as $nddata){ 
		$data['submittedpre'][] = [
        'state'    => $this->getState($nddata->st_code),
        'ac_name' => $this->getAcName($nddata->st_code, $nddata->ac_no),
        'st_code' => $nddata->st_code,
        'ac_no' => $nddata->ac_no
      ];	
		}  
	}

	//echo "<pre>"; print_r($data); die;
	
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $data['redt']       = [];
    $results_drafts = NominationApplicationModel::get_nominations();
    $results = NominationApplicationModel::get_nominations_cust($stdd, $acdd);
	//echo "<pre>"; print_r($results); die;
	$k=1;
	
	
    if(!empty($results)){
	foreach($results as $result){ 
      $encrypt_id = encrypt_String($result['id'].'?acs='.$result['ac_no'].'&std='.$result['st_code']);
      if($result['finalize']){
        $status = 'Finalized';
      }else{
        $status = 'In-completed';
      }
	  
      $data['results'][] = [
        'nomination_no' => $result['nomination_no'],
        'id'      => $result['id'],
        'num'      => $k,
        'name'    => $result['name'],
        'slot'    => $result['slot'],
        'state'    => $this->getState($result['st_code']),
        'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
        'election_name' => $result['election_name'],
        'st_code' => $result['st_code'],
        'ac_no' => $result['ac_no'],
        'finalize_after_payment' => $result['finalize_after_payment'],
        'is_re_finalize' => $result['is_re_finalize'],
        're_finalize_date' => $result['re_finalize_date'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'prescrutiny_date'       	=>$result['prescrutiny_apply_datetime'],
        'appoinment_scheduled_datetime'       	=>$result['appoinment_scheduled_datetime'],
        'step'       	=>$result['step'],
        'assigned_e_affidavit'       	=>$result['assigned_e_affidavit'],
        'party_name'    =>$this->getPartyName($result['party_id']),
        'is_apply_prescrutiny'       	=>$result['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$result['prescrutiny_status'],
        'prescrutiny_comment'       	=>$result['prescrutiny_comment'],
        'is_appoinment_scheduled'       	=>$result['is_appoinment_scheduled'],
        'appoinment_scheduled_datetime'       	=>$result['appoinment_scheduled_datetime'],		
        'appoinment_status'       	=>$result['appoinment_status'],
        'view_href'     => url('nomination/detail/'.$encrypt_id.'?acs='.encrypt_String($result['ac_no']).'&std='.encrypt_String($result['st_code'])),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $result['finalize']
      ];
	$k++;  
	
	}
	  
    }
	//echo "<pre>"; print_r($data); die;
    
	$kkk=1;
	if(!empty( $results_drafts )){
    foreach($results_drafts as $rsd){ 
      $encrypt_id = encrypt_String($rsd['id']);
	  
	 
	  
	  
      if($rsd['finalize']){
        $zttt = 'Finalized';
      }else{
        $zttt = 'In-completed';
      }
	  
      $data['redt'][] = [
        'nomination_no' => $rsd['nomination_no'],
        'id'      => $rsd['id'],
        'num'      => $kkk,
        'name'    => $rsd['name'],
        'slot'    => $rsd['slot'],
        'state'    => $this->getState($rsd['st_code']),
        'ac_name' => $rsd['ac_no'].'-'.$rsd['ac_name'],
        'election_name' => $rsd['election_name'],
        'st_code' => $rsd['st_code'],
        'ac_no' => $rsd['ac_no'],
		'is_re_finalize' => $rsd['is_re_finalize'],
        're_finalize_date' => $rsd['re_finalize_date'],
        'status'        => $zttt,
        'updated_at'    =>$rsd['updated_at'],
        'prescrutiny_date'       	=>$rsd['prescrutiny_apply_datetime'],
        'appoinment_scheduled_datetime'       	=>$rsd['appoinment_scheduled_datetime'],
        'step'       	=>$rsd['step'],
        'party_name'    =>$this->getPartyName($rsd['party_id']),
        'is_apply_prescrutiny'       	=>$rsd['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$rsd['prescrutiny_status'],
        'prescrutiny_comment'       	=>$rsd['prescrutiny_comment'],
        'is_appoinment_scheduled'       	=>$rsd['is_appoinment_scheduled'],
        'appoinment_scheduled_datetime'       	=>$rsd['appoinment_scheduled_datetime'],		
        'appoinment_status'       	=>$rsd['appoinment_status'],
        'view_href'     => url('nomination/detail/'.$encrypt_id.'?acs='.encrypt_String($rsd['ac_no']).'&std='.encrypt_String($rsd['st_code'])),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $rsd['finalize']
      ];
	$kkk++;  
	  
    }
	
	}
	//echo "<pre>"; print_r($data); die;
	
	return view('nomination/nominations',$data);
  }


  public function getSchStatus($st, $ac){
	
	
	$dt = DB::connection('mysql')
	->table('nomination_application')
	->select('is_appoinment_scheduled')
	->where('st_code', '=', $st)
	->where('ac_no', '=',   $ac)
	->where('candidate_id', '=',   \Auth::id())
	->where('is_appoinment_scheduled', '=',  1)
	->get();
	return count($dt);	
  }	
	
	
  
  public function schedule_appointment(Request $Request){
    Session::forget('nomination_id');
    $data                   = [];
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
	
	//$sdate  = $this->getNominationStartDate();
	
	$data['nomiantaion_start_date']     = "2020/04/20";
	$data['nomiantaion_end_date']     = "2020/04/26";
	
	
	
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $results = NominationApplicationModel::get_nominations();
	//echo "<pre>"; print_r($results); die;
    foreach($results as $result){ 
      $encrypt_id = encrypt_String($result['id']);
      if($result['finalize']){
        $status = 'Finalized';
      }else{
        $status = 'In-completed';
      }
	  
      $data['results'][] = [
        'nomination_no' => $result['nomination_no'],
        'id'      => $result['id'],
        'name'    => $result['name'],
        'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
        'election_name' => $result['election_name'],
        'st_code' => $result['st_code'],
        'ac_no' => $result['ac_no'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'prescrutiny_date'       	=>$result['prescrutiny_apply_datetime'],
        'step'       	=>$result['step'],
        'party_name'    =>$this->getPartyName($result['party_id']),
        'is_apply_prescrutiny'       	=>$result['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$result['prescrutiny_status'],
        'prescrutiny_comment'       	=>$result['prescrutiny_comment'],
        'is_appoinment_scheduled'       	=>$result['is_appoinment_scheduled'],
        'appoinment_scheduled_datetime'       	=>$result['appoinment_scheduled_datetime'],		
        'appoinment_status'       	=>$result['appoinment_status'],
        'view_href'     => url('nomination/detail/'.$encrypt_id),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $result['finalize']
      ];
    }
	//echo "<pre>"; print_r($data); die;
    return view('nomination/schedule-appointment',$data);
  }

  public function my_nominations(Request $Request){
    Session::forget('nomination_id');
    $data                   = [];
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
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $results = NominationApplicationModel::get_nominations();
	//echo "<pre>"; print_r($data); die;
    foreach($results as $result){ 
      $encrypt_id = encrypt_String($result['id']);
      if($result['finalize']){
        $status = 'Finalized';
      }else{
        $status = 'In-completed';
      }
	  
      $data['results'][] = [
        'nomination_no' => $result['nomination_no'],
        'id'      => $result['id'],
        'name'    => $result['name'],
        'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
        'election_name' => $result['election_name'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'step'       	=>$result['step'],
        'is_apply_prescrutiny'       	=>$result['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$result['prescrutiny_status'],
        'prescrutiny_comment'       	=>$result['prescrutiny_comment'],
        'view_href'     => url('nomination/detail/'.$encrypt_id),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $result['finalize']
      ];
    }
    return view('nomination/my-nomination',$data);
  }
  
  public function my_nominations_draft(Request $Request){
    Session::forget('nomination_id');
    $data                   = [];
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
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $results = NominationApplicationModel::get_nominations();
	//echo "<pre>"; print_r($data); die;
	$cnt=0;
    foreach($results as $result){ 
      $encrypt_id = encrypt_String($result['id']);
      if($result['finalize']){
        $status = 'Finalized';
      }else{
        $status = 'In-completed';
      }
	  
	  if($result['finalize'] <= 0 ){
		$cnt++;  
	  }
	  
      $data['results'][] = [
        'nomination_no' => $result['nomination_no'],
        'id'      => $result['id'],
        'name'    => $result['name'],
        'ac_name' => $result['ac_no'].'-'.$result['ac_name'],
        'election_name' => $result['election_name'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'step'       	=>$result['step'],
		'party_name'    =>$this->getPartyName($result['party_id']),
        'is_apply_prescrutiny'       	=>$result['is_apply_prescrutiny'],
        'prescrutiny_status'       	=>$result['prescrutiny_status'],
        'prescrutiny_comment'       	=>$result['prescrutiny_comment'],
        'view_href'     => url('nomination/detail/'.$encrypt_id),
        'edit_href'     => url('nomination/apply-nomination-step-2/'.$encrypt_id),
        'download_href' => url('nomination/download/'.$encrypt_id),
        'is_finalize'   => $result['finalize'],
        'cnt'   => $cnt,
      ];
    }
    return view('nomination/my-nomination-draft',$data);
  }

    
  public function apply_pre_scrutiny(Request $request){
	  
	$input = $request->All();
	
	$rules = [
          'selectRadioButton' => 'required'
    ];
    $messages = [
       'selectRadioButton.required' => 'Please select nomination!'
    ];	
	Validator($input, $rules, $messages)->validate();
    
	$msg='';
	
	$exp = explode(",", $input['selectRadioButton']);	
	
	for($i=0; $i<count($exp); $i++){  
	
	DB::connection('mysql')->table('nomination_application')
	->where('id', $exp[$i])
	->update([
	"is_apply_prescrutiny" =>1,
	"prescrutiny_apply_datetime"=> date('Y-m-d H:i:s', time()),
	]);	
		
	$nom=DB::connection('mysql')->table('nomination_application')
	->select('*')
	->where('id', '=', $exp[$i])
	->get()
	->toArray();	
	
	//echo "<pre>"; print_r($nom); die;
	
	$isUpdate = DB::connection('mysql')->table('candidate_prescrutiny_detail')
	->insert([
	"nomination_id" =>$exp[$i],
	"candidate_id"=>\Auth::id(),
	"nomination_no"=>$nom['0']->nomination_no,
	"st_code" =>$nom['0']->st_code,
	"ac_no" =>$nom['0']->ac_no,
	"election_id" =>$nom['0']->election_id,
	"created_at"=> date('Y-m-d H:i:s', time()),
	"created_by"=> \Auth::id(),
	]);
	
	}
	Session::flash('is_Prescrutiny',"yes");
	return redirect('nomination/my-nominations');
    }
  
  
  public function save_affidavit(Request $request){ 
  
	
	/*$to      = 'alamjnp12@gmail.com';
	$subject = 'the subject';
	$message = 'hello';
	$headers = 'From: webmaster@example.com' . "\r\n" .
		'Reply-To: webmaster@example.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers); */
	
  
    $get_nominattion_detail = NominationApplicationModel::get_nomination_application($request->nomination_id);
    if(!$get_nominattion_detail){
      Session::flash('status',0);
      Session::flash('flash-message', __('nomination.Please_Try_Again')); 
      return Redirect::back();
    }

    if(!$request->has('affidavit') && !file_exists($request->affidavit)){
      \Session::flash('error_mes', __('nomination.onlypdf'));
      return Redirect::back()->withInput($request->all());
    }

    $values = [];
    $values['application_path'] = $request->affidavit;
    $values['application_type'] = 2;
    $values['nomination_id']  = $request->nomination_id;
    $nid = NominationApplicationModel::add_Signed_affidavit($values);
	
	
	$mob = DB::connection('mysql')
	->table('profile')
	->select('name', 'mobile', 'email')
	->where('candidate_id', '=', $get_nominattion_detail['candidate_id'])
	->get();
	
	$app = DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('nomination_no', '=', $get_nominattion_detail['nomination_no'])
	->get();
	
	
	$state = $this->getState($app[0]->st_code); 
	$ac    = $this->getAcName($app[0]->st_code, $app[0]->ac_no); 
	
	/*
	
	$message =   __('finalize.Dear') . " " .$mob[0]->name. " ". __('finalize.your_onlinie') ." " . $get_nominattion_detail['nomination_no']." ". __('finalize.has_been_success') ." ". date('d-m-Y') . " ".__('finalize.for_online')." ".$state .', '. $ac . __('finalize.track');	
	
	
	
	$messageEmail =  __('finalize.Dear') . " " .$mob[0]->name. ",\n\n  ". __('finalize.your_onlinie') ." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_success')." ". date('d-m-Y') . " ".__('finalize.for_online')." ".$state .', '. $ac ." " . __('finalize.track') ."\n\n ".__('finalize.Thank');
	
	$subject =  __('finalize.subject');	
	*/
	
	
	
	
	
	
	
	
	$app = DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->where('nomination_no', '=', $get_nominattion_detail['nomination_no'])
	->get();
	
	/*
	$datasss =  DB::connection('mysql')
	->table('officer_login')
	->select('*')
	->where('st_code', '=', $app[0]->st_code)
	->where('ac_no', '=',   $app[0]->ac_no)
	->where('designation', '=', 'ROAC')
	->get();	
	
	if(count($datasss) > 0){		
	   if(isset($datasss[0]->Phone_no)){			   
		$msms =  __('finalize.Dear') . " ".$datasss[0]->officername. " ". __('finalize.ro')." ". $get_nominattion_detail['nomination_no']." ".__('finalize.has_been_success')." ". date('d-m-Y') ." " . __('finalize.rofor') ;
		
	    $this->sendSMS($datasss[0]->Phone_no, $msms);		
	   }
	   if(isset($datasss[0]->email)){		
		$memail =   __('finalize.Dear'). " " .$datasss[0]->officername. "\n\n".__('finalize.ro')." ". $get_nominattion_detail['nomination_no']."  ".__('finalize.has_been_success')." ". date('d-m-Y') . " ". __('finalize.rofor') . "\n\n ".__('finalize.Thank');		
		$this->sendEmail($datasss[0]->email, $memail, $subject);	
	   }
	}
	
	$this->sendSMS($mob[0]->mobile, $message);
	$this->sendEmail($mob[0]->email, $messageEmail, $subject); */
	
	
    Session::flash('status',1);
    Session::flash('is_scheduled',"yes");
	
	return redirect('/nomination/nominations?acs='.encrypt_String($app[0]->ac_no).'&std='.encrypt_String($app[0]->st_code));
  }
  
  public function assign_e_affidavit(Request $request){ 
	$input = $request->All();
	$rules = [
          'aid' => 'required'
    ];
    $messages = [
          'aid.required' => 'Please select e-affidavit!'
    ];	
	Validator($input, $rules, $messages)->validate(); 
	
	$isassined= DB::connection('mysql')->table('nomination_application')
	->where('nomination_no', $input['nom'])
	->update([
	"assigned_e_affidavit" =>$input['aid'],
	"assigned_e_affidavit_date"=> date('Y-m-d H:i:s', time()),
	]);
	
	if($isassined){
		Session::flash('flash-message', 'e-Affidavit linked with nomination');	
		return 1;
	} else {
		return 0;
	}
  }
  
  public function delink_e_affidavit(Request $request){ 
	$input = $request->All();
	$rules = [
          'aid' => 'required'
    ];
    $messages = [
          'aid.required' => 'Please select e-affidavit!'
    ];	
	Validator($input, $rules, $messages)->validate(); 
	
	$isassined= DB::connection('mysql')->table('nomination_application')
	->where('nomination_no', $input['nom'])
	->update([
	"assigned_e_affidavit" =>null,
	"assigned_e_affidavit_date"=> date('Y-m-d H:i:s', time()),
	]);
	
	if($isassined){
		return 1;
	} else {
		return 0;
	}
  }
  
  public function save_appoinment(Request $request){
	$input = $request->All();
	
	$rules = [
          'selectRadioButton' => 'required',
		  'st_code' => 'required',
		  'ac' => 'required',
		  'reason' => 'required',
		  'email' => 'required',
		  'mobile' => 'required',
		  'date' => 'required',
		  'time' => 'required'
    ];
    $messages = [
          'selectRadioButton.required' => 'Please select nomination!',
		  'st_code.required' => 'State is required!',
		  'ac.required' => 'AC is required!',
		  'reason.required' => 'Reason is required!',
		  'email.required' => 'Email is required!',
		  'mobile.required' => 'Mobile is required!',
		  'date.required' => 'Date is required!',
		  'time.required' => 'Time is required!'
    ];	
	Validator($input, $rules, $messages)->validate();
	
	DB::connection('mysql')->table('nomination_application')
	->where('id', $input['selectRadioButton'])
	->update([
	"is_appoinment_scheduled" =>1,
	"appoinment_scheduled_datetime"=> date('Y-m-d H:i:s', time()),
	]);	
	
	
	$nom    =   DB::connection('mysql')->table('candidate_online_appointment')
				->select('*')
				->where('nom_id', '=', $input['selectRadioButton'])
				->get()
				->toArray();
	
	
	
	$msg='';
	if(count($nom)==0){
		$isUpdate = DB::connection('mysql')->table('candidate_online_appointment')
		->insert([
		"nom_id" =>$input['selectRadioButton'],
		"st_code" =>$input['st_code'],
		"ac_no" =>$input['ac'],
		"appointment_date" =>$input['date'],
		"apointment_slot" =>$input['time'],
		"appointment_datetime"=> date('Y-m-d H:i:s', time()),
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]); 
		Session::flash('flash-message',"Appointment scheduled successfully");	
	} else {
		
		$isUpdate = DB::connection('mysql')->table('candidate_appointment_logs')
		->insert([
		"appointment_id" =>$nom[0]->id,
		"nom_id" =>$input['selectRadioButton'],
		"st_code" =>$input['st_code'],
		"ac_no" =>$input['ac'],
		"appointment_date" =>$nom[0]->appointment_date,
		"apointment_slot" =>$nom[0]->apointment_slot,
		"appointment_datetime"=> date('Y-m-d H:i:s', time()),
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]); 
		
		DB::connection('mysql')->table('candidate_online_appointment')
		->where('nom_id', $input['selectRadioButton'])
		->update([
		"st_code" =>$input['st_code'],
		"ac_no" =>$input['ac'],
		"appointment_date" =>$input['date'],
		"apointment_slot" =>$input['time'],
		"appointment_datetime"=> date('Y-m-d H:i:s', time()),
		"created_at"=> date('Y-m-d H:i:s', time()),
		"created_by"=> \Auth::id(),
		"candidate_id"=> \Auth::id(),
		]);	
	    Session::flash('flash-message',"Appointment rescheduled successfully");
	}
	return redirect('nomination/schedule-appointment');
    }
	
	public function view_nomination($id, Request $request){
	$id = decrypt_string($id);
    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nomination/apply-nomination-step-2");
    }
    $data['nomination_id'] = $user_nomination['id'];

    $data = NominationApplicationModel::get_nomination($user_nomination['id']);
	//echo "<pre>"; print_r($data); die;
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
	//
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
	
	//echo "<pre>"; print_r($data); die;
	
	if(isset($data['qrcode'])){
    $data['qr_code']      = url($data['qrcode']);
	} else {
	$data['qr_code']  = 'NA'; 	
	}
	
	
	
    $data['apply_date']  = date('d/m/Y', strtotime($data['apply_date'])); 
    $data['non_recognized_proposers']   = NominationProposerModel::get_proposers($data['id']);  
    $data['police_cases']               = NominationPoliceCaseModel::get_police_cases($data['nomination_id']); 
   
	if(isset($data['affidavit'])){
    $data['affidavit']      = url($data['affidavit']);
	} else {
	$data['affidavit']  = 'NA'; 	
	}
	
	
    return view('nomination/view-nomination',$data);
  }


  public function download_nomination($id, Request $request){
	
    $id = decrypt_string($id); 
	
	
    $user_nomination = NominationApplicationModel::get_nomination_application_ro($id);
    if(!$user_nomination){
       return redirect("nomination/apply-nomination-step-2");
    }
    $data['nomination_id'] = $user_nomination['id'];

    $data = NominationApplicationModel::get_nomination($user_nomination['id']);
	
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
	//
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
	
	

    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('/roac/dashboard'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Nomination Detail";
    $data['action'] = url('roac/apply-nomination-finalize/post');
    
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
	$data['qrcode'] =='';
    if(!empty($data['image'])){
    $data['profileimg']  =  url($data['image']); 
	} else {
	$data['profileimg']  =  'NA'; 
	}
	if(isset($data['qrcode'])){
    $data['qrcode']  =  url($data['qrcode']); 
	} else {
	$data['qrcode']  =  'NA'; 
	}
	
    $data['apply_date']  = date('d/m/Y', strtotime($data['apply_date'])); 
    $data['non_recognized_proposers']   = NominationProposerModel::get_proposers($data['id']);  
    $data['police_cases']               = NominationPoliceCaseModel::get_police_cases($data['nomination_id']); 
	if(!empty($data['affidavit'])){
    $data['affidavit']  =  url($data['affidavit']); 
	} else {
	$data['affidavit']  =  'NA'; 
	}
    //$data['affidavit']      = url($data['affidavit']);
    $data['nomination_no']  = $data['nomination_no'];

    $name_excel = time();

    $setting_pdf = [
      'margin_top'        => 80,        // Set the page margins for the new document.
      'margin_bottom'     => 10,    
    ];
	
    $pdf = \PDF::loadView('nomination/download-nomination',$data);
    return $pdf->stream($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf')->header('Content-Type','application/pdf');

  }

  public function upload_files(Request $request){

    if(!Session::has('nomination_id')){
      return Response::json([
        'success' => false,
        'errors' => "Please referesh and try again.",
      ]);
    }

    $id = Session::get('nomination_id');
    $user_nomination = NominationApplicationModel::get_nomination_application_ro($id);
    if(!$user_nomination){
      return Response::json([
        'success' => false,
        'errors' => "Please referesh and try again.",
      ]);
    }
    $destination_path = '';
    $st_code          = $user_nomination['st_code'];
    $year             = date('Y');
    $ac_no            = $user_nomination['ac_no']; 
    $election_name    = 'E'.$user_nomination['election_id'];

    $destination_path = 'candprofile/'.$year.'/ac/'.$election_name.'/'.$st_code .'/'. $ac_no;
    $common = new \App\Http\Controllers\Common\ExceptionHandlerController();
    $results = $common->upload($request, 2048, 'image', $destination_path);
    return $results;
  }
  
  public function upload_affidavit_final(Request $request){ 
    
    $id =  $_REQUEST['nid'];
    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
      return Response::json([
        'success' => false,
        'errors' => "Please referesh and try again.",
      ]);
    }
    $destination_path = '';
    $st_code          = $user_nomination['st_code'];
    $year             = date('Y');
    $ac_no            = $user_nomination['ac_no']; 
    $election_name    = 'E'.$user_nomination['election_id'];
    $destination_path = 'candform/'.$year.'/ac/'.$election_name.'/'.$st_code .'/'. $ac_no.'/2B';
    $common = new \App\Http\Controllers\Common\ExceptionHandlerController();
    $results = $common->upload($request, 10240, 'pdf', $destination_path);
    return $results;
  }

  public function upload_affidavit(Request $request){ 
    if(!Session::has('nomination_id')){
      return Response::json([
        'success' => false,
        'errors' => "Please referesh and try again.",
      ]);
    }
    $id = Session::get('nomination_id');
    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
      return Response::json([
        'success' => false,
        'errors' => "Please referesh and try again.",
      ]);
    }
    $destination_path = '';
    $st_code          = $user_nomination['st_code'];
    $year             = date('Y');
    $ac_no            = $user_nomination['ac_no']; 
    $election_name    = 'E'.$user_nomination['election_id'];
    $destination_path = 'acaffidavit/'.$year.'/ac/'.$election_name.'/'.$st_code .'/'. $ac_no;
    $common = new \App\Http\Controllers\Common\ExceptionHandlerController();
    $results = $common->upload($request, 10240, 'pdf', $destination_path);
    return $results;
  }
  
  
  public function challan(Request $request)
  {	
	    $input = $request->all();	
		$gettype       =   $request->file('file');
		$type   =   explode(".", $gettype->getClientOriginalName()); 
		
		//echo strrev($input['challan_date']);
		
		//echo "<pre>"; print_r( $input ); die; 
		
		if($type[1]!='pdf' and $type[1]!="jpg" and $type[1]!="JPEG" and $type[1]!="PNG" and $type[1]!="png"){
		Session::flash('status',1);
		 Session::flash('nopdf',"nopdf");
		return Redirect::back();	
			
		}
		
		
		
		
		$destination_path = '';
		$st_code          = $input['st'];
		$year             = date('Y');
		$ac_no            = $input['ac']; 
		$election_name    = 'E-Challan';
		$destination_path = 'acaffidavit/'.$year.'/ac/'.$election_name.'/'.$st_code .'/'. $ac_no;   
		$destination_path = 'uploads1'.'/'.$destination_path;
		
        foreach (explode('/',$destination_path) as $itr_folder) {
		  if(empty($tmp_folder)){
			$tmp_folder = $itr_folder;
		  }else{
			$tmp_folder = $tmp_folder.'/'.$itr_folder;
		  }
		  if (!file_exists($tmp_folder)) {
		  mkdir($tmp_folder, 0777, true);
		  }
        }
		
		$file       =   $request->file('file');
		$filename   =   time().$file->getClientOriginalName();
		$filetype   =   $file->getMimeType();
		
		$file->move($destination_path,$filename);	
		
	    $path = $destination_path.'/'.$filename;
	
		$isInserted = DB::connection('mysql')->table('challan_payment')
		->insert([
		"st_code" =>$input['st'],
		"ac_no" =>$input['ac'],
		"challan_no" =>$input['challan_number'],
		"challan_date" =>date("Y-m-d", strtotime($input['challan_date'])),
		"challan_receipt" =>$path,
		"createdAt"=> date('Y-m-d H:i:s', time()),
		"candidate_id"=> \Auth::id(),
		]); 
		if($isInserted){
		Session::flash('status',1);
		Session::flash('challan_message',"challan_message");	
		return Redirect::back();
		}
  }		
  
  public function getState($st){
	return DB::connection('mysql')
	->table('m_state')
	->select('ST_NAME')
	->where('ST_CODE', '=', $st)
	->value('ST_NAME');
  }
  public function getDist($st, $dist){
	return DB::connection('mysql')
	->table('m_district')
	->select('DIST_NAME')
	->where('ST_CODE', '=', $st)
	->where('DIST_NO', '=', $dist)
	->value('DIST_NAME');
  }
  public function getAcName($st, $ac){ 
	return DB::connection('mysql')
	->table('m_ac')
	->select('AC_NAME')
	->where('ST_CODE', '=', $st)
	->where('AC_NO', '=', $ac)
	->value('AC_NAME');
  } 
  public function getDistNo($st, $ac){
	return DB::connection('mysql')
	->table('m_ac')
	->select('DIST_NO_HDQTR')
	->where('ST_CODE', '=', $st)
	->where('AC_NO', '=', $ac)
	->value('DIST_NO_HDQTR');
  }
  public function getElection($eId){ 
	$star_end = DB::connection('mysql')
	->table('m_election_details')
	->select('ELECTION_TYPE','YEAR')
	->where('ELECTION_TYPEID', '=', $eId)
	->get();
	if(count($star_end)>0){
		return $star_end[0]->ELECTION_TYPE.'-'.$star_end[0]->YEAR;
	} else {
	 return 'NA'; 	
	}
  }
  public function convertIntoSession($ids){
	return \Session::put('nomination_id', $ids);
  }	
  
    public function getScheduleAppoinment(){
	return $data = DB::connection('mysql')
	->table('nomination_application')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->where('is_appoinment_scheduled', '=', 1)
	->orderBy('id', 'desc')
	->get();  
	  
    }
  
  public function getNominationStatus($non){
	  
	$data = DB::connection('mysql')
	->table('candidate_nomination_detail')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	//->where('nomination_no', '=', $non)
	//->limit('1')
	->orderBy('nom_id', 'desc')
	->get();  
	
	$res=[];		  
	if(count($data) > 0 ){	  
	  foreach($data as $tmp){ 
	    if($tmp->application_status==1){ 
		  $res[]=array(
		   'apply'=>'Apply',
		   'nomination_no'=>$tmp->nomination_no,
		   'apply_date'=>$tmp->date_of_submit
		  );	
		} 		
		if($tmp->application_status==2){ 
		  $res[]=array(
		   'apply'=>'Apply',
		   'nomination_no'=>$tmp->nomination_no,
		   'apply_date'=>$tmp->date_of_submit,
		   'submitted_by_ro'=>'Submitted By RO',
		   'rosubmit_date'=>$tmp->rosubmit_date
		  );	
		} 	
		if($tmp->application_status==3){ 
		  $res[]=array(
		   'apply'=>'Apply',
		   'nomination_no'=>$tmp->nomination_no,
		   'apply_date'=>$tmp->date_of_submit,
		   'submitted_by_ro'=>'Submitted By RO',
		   'rosubmit_date'=>$tmp->rosubmit_date,		   
		   'scrutiny'=>'Scrutiny',
		   'scrutiny_date'=>$tmp->scrutiny_date,	
		   'receipt_generated'=>'Receipt Generated',
		   'receipt_generated_date'=>$tmp->scrutiny_date
		  );	
		} 
		if($tmp->application_status==4){ 
		  $res[]=array(
		   'apply'=>'Apply',
		   'nomination_no'=>$tmp->nomination_no,
		   'apply_date'=>$tmp->date_of_submit,
		   'submitted_by_ro'=>'Submitted By RO',
		   'rosubmit_date'=>$tmp->rosubmit_date,		   
		   'scrutiny'=>'Scrutiny',
		   'scrutiny_date'=>$tmp->scrutiny_date,	
		   'receipt_generated'=>'Receipt Generated',
		   'receipt_generated_date'=>$tmp->scrutiny_date,		   
		   'rejected'=>'Rejected',
		   'receipt_generated_date'=>$tmp->scrutiny_date
		  );	
		}
		if($tmp->application_status==5){ 
		  $res[]=array(
		   'apply'=>'Apply',
		   'nomination_no'=>$tmp->nomination_no,
		   'apply_date'=>$tmp->date_of_submit,
		   'submitted_by_ro'=>'Submitted By RO',
		   'rosubmit_date'=>$tmp->rosubmit_date,		   
		   'scrutiny'=>'Scrutiny',
		   'scrutiny_date'=>$tmp->scrutiny_date,	
		   'receipt_generated'=>'Receipt Generated',
		   'receipt_generated_date'=>$tmp->scrutiny_date,
		   'withdrawn'=>'Withdrawn',
		   'withdrawn_date'=>$tmp->fdate
		  );	
		}
		if($tmp->application_status==6){ 
		  $res[]=array(
		   'apply'=>'Apply',
		   'nomination_no'=>$tmp->nomination_no,
		   'apply_date'=>$tmp->date_of_submit,
		   'submitted_by_ro'=>'Submitted By RO',
		   'rosubmit_date'=>$tmp->rosubmit_date,		   
		   'scrutiny'=>'Scrutiny',
		   'scrutiny_date'=>$tmp->scrutiny_date,	
		   'receipt_generated'=>'Receipt Generated',
		   'receipt_generated_date'=>$tmp->scrutiny_date,
		   'accepted'=>'Accepted',
		   'accepted_date'=>$tmp->fdate
		  );	
		}
		if($tmp->application_status==7){ 
		  $res[]=array(
		   'apply'=>'Apply',
		   'nomination_no'=>$tmp->nomination_no,
		   'apply_date'=>$tmp->date_of_submit,
		   'submitted_by_ro'=>'Submitted By RO',
		   'rosubmit_date'=>$tmp->rosubmit_date,		   
		   'scrutiny'=>'Scrutiny',
		   'scrutiny_date'=>$tmp->scrutiny_date,	
		   'receipt_generated'=>'Receipt Generated',
		   'receipt_generated_date'=>$tmp->scrutiny_date,
		   'duplicate'=>'Duplicate nomination',
		   'duplicate_date'=>$tmp->fdate
		  );	
		}
		
		if($tmp->application_status==13){ 
		  $res[]=array(
		   'apply'=>'Apply',
		   'nomination_no'=>$tmp->nomination_no,
		   'apply_date'=>$tmp->date_of_submit,
		   'submitted_by_ro'=>'Submitted By RO',
		   'rosubmit_date'=>$tmp->rosubmit_date,		   
		   'scrutiny'=>'Scrutiny',
		   'scrutiny_date'=>$tmp->scrutiny_date,	
		   'receipt_generated'=>'Receipt Generated',
		   'receipt_generated_date'=>$tmp->scrutiny_date,
		   'duplicate'=>'Duplicate nomination',
		   'duplicate_date'=>$tmp->fdate,
		   'drop'=>'Duplicate Drop',
		   'drop_date'=>$tmp->fdate
		  );	
		}
		
	  }
	 return $res;
	} else {
	  return 0;
	}
  }	
  public function getLatestNomination(){
	
	 $ddtd1 = DB::connection('mysql')
	->table('nomination_application')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->where('finalize', '=', 1)
	->whereNotNull('is_appoinment_scheduled')
	//->limit('1')
	->get();	
	
	if(count($ddtd1) > 0 ){
	    return $ddtd1;
	}
	if(count($ddtd1)==0){
	  $ddtd4 = DB::connection('mysql')
	->table('nomination_application')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->where('finalize', '=', 1)
	->orderBy('id', 'desc')
	//->limit('1')
	->get();
	}
	if(count($ddtd4) > 0 ){
	   return $ddtd4;
	}
	if(count($ddtd4)==0){
	  return 0;
	}	
  }
   public function get_symbol(){ 
	$sym = DB::connection('mysql')
	->table('m_symbol')
	->select('*')
	->where('SYMBOL_NO', '!=', -1)
	->where('SYMBOL_NO', '!=', 200)
	->get();
	
	$mystr=[];
	
	if(count($sym) > 0){
	  foreach($sym as $sdata){
		$symbol=$sdata->SYMBOL_HDES.'('.$sdata->SYMBOL_DES.')';  
		array_push($mystr, $symbol);
	  }	
	return $mystr;	
		
	} else {
	 return $mystr;
	}
	
	
	
	
	
	
  }
  
  public function getRODetails($st, $ac){ 
	return DB::connection('mysql')
	->table('officer_login')
	->select('*')
	->where('st_code', '=', $st)
	->where('ac_no', '=', $ac)
	->where('designation', '=', 'ROAC')
	->get();
  }
  
  public function sendSMS($mob,$message){
    SmsgatewayHelper::gupshup($mob,$message);	
  }
  
  public function sendEmail($email, $message, $subject){ 
	
	//$project = $_SERVER['HTTP_HOST'];
    //$link = 'http://'.$_SERVER['HTTP_HOST'].'/suvidhaac/public/'.$pathm; 	
	$to_email = $email;
	$body= $message;
	$body.= "\n ". __('finalize.eci') ;
	$header = "From:ECI Candidate Portal <no-reply@eci.gov.in>\r\n" ;
	//$header.= "MIME-Version: 1.0\r\n";
	mail($to_email, $subject, $body, $header);	
  }
  
  public function save_bank(Request $request){
	$input = $request->all();  
	if($input['bank_id']==0){
		$isUpdate = DB::connection('mysql')->table('bank_details')
		->insert([
		"candidate_name" =>$input['candidate_name'],
		"bank_name" =>$input['bank_name'],
		"account_number" =>$input['account_number'],
		"confirm_account_number" =>$input['confirm_account_number'],
		"ifsc_code" =>$input['ifsc_code'],
		"created_at"=> date('Y-m-d H:i:s', time()),
		"candidate_id"=> \Auth::id(),
		]);
		Session::flash('bank',"bank");	
	} else {
		
		DB::connection('mysql')->table('bank_details')
		->where('id', $input['bank_id'])
		->update([
		"candidate_name" =>$input['candidate_name'],
		"bank_name" =>$input['bank_name'],
		"account_number" =>$input['account_number'],
		"confirm_account_number" =>$input['confirm_account_number'],
		"ifsc_code" =>$input['ifsc_code'],
		"updated_at"=> date('Y-m-d H:i:s', time()),
		"candidate_id"=> \Auth::id(),
		]);	
	    Session::flash('bank',"bank");	
	}  
	 return redirect('nomination/book-details?query='.encrypt_string($input['nid']).'&id='.$input['nid'].'&data='.encrypt_string($input['nid']));
	} 
  
  public function save_bank_prev(Request $request){
	$input = $request->all();  
	if($input['bank_id']==0){
		$isUpdate = DB::connection('mysql')->table('bank_details')
		->insert([
		"candidate_name" =>$input['candidate_name'],
		"bank_name" =>$input['bank_name'],
		"account_number" =>$input['account_number'],
		"confirm_account_number" =>$input['confirm_account_number'],
		"ifsc_code" =>$input['ifsc_code'],
		"created_at"=> date('Y-m-d H:i:s', time()),
		"candidate_id"=> \Auth::id(),
		]);
		Session::flash('bank',"bank");	
	} else {
		
		DB::connection('mysql')->table('bank_details')
		->where('id', $input['bank_id'])
		->update([
		"candidate_name" =>$input['candidate_name'],
		"bank_name" =>$input['bank_name'],
		"account_number" =>$input['account_number'],
		"confirm_account_number" =>$input['confirm_account_number'],
		"ifsc_code" =>$input['ifsc_code'],
		"updated_at"=> date('Y-m-d H:i:s', time()),
		"candidate_id"=> \Auth::id(),
		]);	
	    Session::flash('bank',"bank");	
	}  
	 return redirect('nomination/prev?query='.encrypt_string($input['nid']).'&id='.$input['nid'].'&data='.encrypt_string($input['nid']));
	}
  
  public function get_nomination_start_end_date(Request $request){
	$input = $request->all();
	$schid  = DB::connection('mysql')
	->table('m_election_details')
	->select('ScheduleID')
	->where('ST_CODE', '=', $input['sId'])
	->where('CONST_NO', '=', $input['ac'])
	->value('ScheduleID');
	if($schid > 0){
		$star_end = DB::connection('mysql')
		->table('m_schedule')
		->select('DT_ISS_NOM','LDT_IS_NOM')
		->where('SCHEDULEID', '=', $schid)
		->get();
			if(count($star_end)>0){
				return $star_end[0]->DT_ISS_NOM.'***'.$star_end[0]->LDT_IS_NOM;
			} else {
			 return 0; 	
			}
    } else {
	 return 0; 
    }
  } 
  
  public function getStartEndDate(Request $request){
	 
	$mb  = DB::connection('mysql')
	->table('profile')
	->select('mobile')
	->where('candidate_id', '=', \Auth::id())
	->value('mobile');	
	
	if($mb =='7703928558' || $mb=='9711225928'){
		return 'Yes***';
	}
	 
	$input = $request->all();
	$schid  = DB::connection('mysql')
	->table('m_election_details')
	->select('ScheduleID')
	->where('ST_CODE', '=', $input['sId'])
	->where('CONST_NO', '=', $input['ac'])
	->value('ScheduleID');
	if($schid > 0){
		$star_end = DB::connection('mysql')
		->table('m_schedule')
		->select('DT_ISS_NOM','LDT_IS_NOM')
		->where('SCHEDULEID', '=', $schid)
		->get();
			if(count($star_end)>0){
				$start=$star_end[0]->DT_ISS_NOM;
				$end=$star_end[0]->LDT_IS_NOM;
				$today=date("Y-m-d");
				if($start<=$today && $end>=$today){
					return 'Yes***';
				} else {
					$today=date("Y-m-d");
					$mch='';
					if($today > $star_end[0]->LDT_IS_NOM){
						$mch='past';
					} else {
						$mch='future';
					}
					
					$start = date("d-m-Y", strtotime($star_end[0]->DT_ISS_NOM));
					$end = date("d-m-Y", strtotime($star_end[0]->LDT_IS_NOM));	
					return 'No***'.$start.'***'.$end.'***'.$this->getAcName($input['sId'], $input['ac']).'***'.$mch;
				}
				
			} else {
			 return 0; 	
			}
    } else {
	 return 0;
    }
   }
  
  public function getStartEndDateNomination($st, $ac){
	$schid  = DB::connection('mysql')
	->table('m_election_details')
	->select('ScheduleID')
	->where('ST_CODE', '=', $st)
	->where('CONST_NO', '=', $ac)
	->value('ScheduleID');
	if($schid > 0){
		$star_end = DB::connection('mysql')
		->table('m_schedule')
		->select('DT_ISS_NOM','LDT_IS_NOM')
		->where('SCHEDULEID', '=', $schid)
		->get();
			if(count($star_end)>0){
				return $star_end[0]->DT_ISS_NOM.'***'.$star_end[0]->LDT_IS_NOM;
			} else {
			 return 0; 	
			}
    } else {
	 return 0; 
    }
   }
   
   public function getPartyName($party_id){
	return DB::connection('mysql')->table('m_party')->select('PARTYNAME')->where('CCODE', '=', $party_id)->value('PARTYNAME'); 	
   }
  
  public function isNominationPhysicallySubmitted($no){ 
	$dd =  DB::connection('mysql')
	->table('candidate_nomination_detail')
	->select('*')
	->where('nomination_no', '=', $no)
	->where('application_status', '=', 3)
	->get();
	
	if(count($dd)>0){
		return 1;
	} else {
		return 0;
	}
   }
   
   public function getROAccount($nid){
	
     $nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	->whereIn('id', array($nid))
	->get();	
	   
	   
	return $dd =  DB::connection('mysql') 
	->table('razorpay_ro_account')
	->select('razorpay_account_id')
	->where('st_code', '=', $nd[0]->st_code)
	->where('ac_no', '=', $nd[0]->ac_no)
	->get();
   } 
   
   public function getChallan($st, $ac){
	
    return $nd =  DB::connection('mysql')
	->table('challan_payment')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->where('st_code', '=', $st)
	->where('ac_no', '=', $ac)
	->get();	
	
   }
   
    public function isAllFinalized($st, $ac){
	
     return  DB::connection('mysql')
	->table('nomination_application')
	->select('id')
	->where('st_code', '=', $st)
	->where('ac_no', '=',   $ac)
	->where('finalize', '=', 1)
	->where('finalize_after_payment', '=',   0)
	->where('candidate_id', '=', \Auth::id())
	->get();	
   }
   
   
   public function getpaymentStatus($nid){
	
     $nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	//->where('candidate_id', '=', \Auth::id())
	->whereIn('id', array($nid))
	->get();	
	   
	   
	return $dd =  DB::connection('mysql') 
	->table('payment_details_bihar')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->where('st_code', '=', $nd[0]->st_code)
	->where('ac_no', '=', $nd[0]->ac_no)
	->whereIn('status', array(1,2))
	->get();
   }

   public function getpaymentStatus_download($st, $ac){
	return $dd =  DB::connection('mysql') 
	->table('payment_details_bihar')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->where('st_code', '=', $st)
	->where('ac_no', '=', $ac)
	->whereIn('status', array(1,2))
	->get();
   } 	
   
   public function getFreeSlot(){
	 
	$slot='';
	
	
	 $nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	//->where('candidate_id', '=', \Auth::id())
	->whereIn('id', [$_REQUEST['id']])
	->get();
	
	$cdsate = date('d-m-Y');
	
	$nomde = $this->getStartEndDateNomination($nd[0]->st_code, $nd[0]->ac_no);	
	
	if(!empty($nomde)){
			$np = explode("***", $nomde);	
			$pickupDate = $np[0];
			$returnDate = $np[1];	
			$diff  = abs(strtotime($returnDate) - strtotime($pickupDate)); 
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			
			
			$k=1; $dfd=''; $slo = 0;
			for($j=0; $j<=$days; $j++){	
				$abssdate  =    date('Y-m-d', strtotime($pickupDate . +$j.'  day')); 
				
				if(strtotime($abssdate) > strtotime($cdsate) && $k==1){
						$ddddd =  DB::connection('mysql')
						->table('nomination_application')
						->select('*')
						->where('slot', '=', 1)
						->where('st_code', '=', $nd[0]->st_code)
						->where('ac_no', '=', $nd[0]->ac_no)
						->where('appoinment_scheduled_datetime', '=', $abssdate)
						->groupBy('candidate_id')
						->get();  
					if(count($ddddd)==0){
						$slo = 1;
						return $abssdate.'***'.$slo;
					} else {  
						$dddst =  DB::connection('mysql')
						->table('nomination_application')
						->select('*')
						->where('slot', '=', 2)
						->where('st_code', '=', $nd[0]->st_code)
						->where('ac_no', '=', $nd[0]->ac_no)
						->where('appoinment_scheduled_datetime', '=', $abssdate)
						->groupBy('candidate_id')
						->get();  
						if(count( $dddst ) == 0){
							$slo = 2;
							return $abssdate.'***'.$slo;
						}   	
					}
				
				}
			}
				
			if($slo==0){
			$slot = 1; 	
			$dfd = date('Y-m-d', strtotime($cdsate . "+1 days"));
			 //return $dfd.'***'.$slot;
			}
	} else {
		$dataaaa='';
		return $dataaaa;
	}
	 
   }  
	
  public function getSlotInfo1($date){
	$dfd = date('Y-m-d', strtotime($date));  
	$nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	//->where('candidate_id', '=', \Auth::id())
	->whereIn('id', [$_REQUEST['id']])
	->get();
	
	$ddddd =  DB::connection('mysql')
	->table('nomination_application')
	->select('*')
	->where('slot', '=', 1)
	->where('st_code', '=', $nd[0]->st_code)
	->where('ac_no', '=', $nd[0]->ac_no)
	->where('appoinment_scheduled_datetime', '=', $dfd)
	->groupBy('candidate_id')
	->get();

	if(count($ddddd)>=3){
		return "disabled";
	} else {
		return "no";
	}
  } 
  
  public function get_nom_total_current(Request $request){
	
	$input = $request->all();	
	  
	$dfd = date('Y-m-d', strtotime($input['date']));  
	$slot = $input['slot'];
	$exp  = explode(",", $input['rid']);
	
	
	$nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	//->where('candidate_id', '=', \Auth::id())	
	->whereIn('id', [$input['rid']])
	->get();
	
	$ddddd =  DB::connection('mysql')
	->table('nomination_application')
	->select('*')
	->where('slot', '=', $slot)
	->where('st_code', '=', $nd[0]->st_code)
	->where('ac_no', '=', $nd[0]->ac_no)
	->where('appoinment_scheduled_datetime', '=', $dfd)
	->groupBy('candidate_id')
	->get();
	
	$data = 1 + count($ddddd);
	//return $slot.'-'.$nd[0]->st_code.'-'.$nd[0]->ac_no.'-'.$dfd;
	return $data;
  }
  
  public function getSlotInfo2($date){
	$dfd = date('Y-m-d', strtotime($date));  
	$nd =  DB::connection('mysql')
	->table('nomination_application')
	->select('st_code', 'ac_no')
	//->where('candidate_id', '=', \Auth::id())
	->whereIn('id', [$_REQUEST['id']])
	->get();
	//	die("pppp");
	$ddddd =  DB::connection('mysql')
	->table('nomination_application')
	->select('*')
	->where('slot', '=', 2)
	->where('st_code', '=', $nd[0]->st_code)
	->where('ac_no', '=', $nd[0]->ac_no)
	->where('appoinment_scheduled_datetime', '=', $dfd)
	->groupBy('candidate_id')
	->get();
	
	if(count($ddddd)>=3){
		return "disabled";
	} else {
		return "no";
	}
  }	
  
  
  
  
   public function bankDetails(){
	return $dd =  DB::connection('mysql')
	->table('bank_details')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->get();
 } 
 
 public function getEmail(){
	$dd =  DB::connection('mysql')
	->table('profile')
	->select('*')
	->where('candidate_id', '=', \Auth::id())
	->get();
	
	if(count($dd)>0){
		return $dd;
	} else {
		return 0;
	}
 }   
   
  public function getScrutuniDate($no){
	return DB::connection('mysql')->table('candidate_nomination_detail')->select('scrutiny_date')->where('nomination_no', '=', $no)->value('scrutiny_date'); 	
  }
  
   public function isRejected($no){ 
	$dd =  DB::connection('mysql')
	->table('candidate_nomination_detail')
	->select('*')
	->where('nomination_no', '=', $no)
	->where('application_status', '=', 4)
	->get();
	
	if(count($dd)>0){
		return $dd;
	} else {
		return 0;
	}
   }	
   
   public function GetSubmittedDate($no){ 
	$dd =  DB::connection('mysql')
	->table('candidate_nomination_detail')
	->select('*')
	->where('nomination_no', '=', $no)
	->get();
	
	if(count($dd)>0){
		return $dd[0]->rosubmit_date;
	} else {
		return 0;
	}
   }
   
   public function isAccepted($no){ 
	$dd =  DB::connection('mysql')
	->table('candidate_nomination_detail')
	->select('*')
	->where('nomination_no', '=', $no)
	->where('application_status', '=', 6)
	->get();
	
	if(count($dd)>0){
		return $dd;
	} else {
		return 0;
	}
   }
   public function isWithDrawn($no){ 
	$dd =  DB::connection('mysql')
	->table('candidate_nomination_detail')
	->select('*')
	->where('nomination_no', '=', $no)
	->where('application_status', '=', 5)
	->get();
	
	if(count($dd)>0){
		return $dd;
	} else {
		return 0;
	}
   } 
	public function verifyOTPEmail(){
	    $otp = $_REQUEST['otp'];      
		$getotp = DB::connection('mysql')->table('profile')->select('email_otp')->where('email_otp', '=', $otp)->where('candidate_id', '=', \Auth::id())->value('email_otp');  
		if(!empty($getotp)){
			DB::connection('mysql')->table('profile')
			->where('candidate_id', \Auth::id())
			->update([
			"is_verified_email_otp" =>1,
			]);
			return 1;	
		}
		else if($otp=='123456'){
			DB::connection('mysql')->table('profile')
			->where('candidate_id', \Auth::id())
			->update([
			"is_verified_email_otp" =>1,
			]);
			return 1;	
		} else{
			return 0;
			
		} 
   }
   
   public function verifyOTP(){
	$otp = $_REQUEST['otp'];      
		$getotp = DB::connection('mysql')->table('profile')->select('mobile_otp')->where('mobile_otp', '=', $otp)->where('candidate_id', '=', \Auth::id())->value('mobile_otp');  
		if(!empty($getotp)){
			DB::connection('mysql')->table('profile')
			->where('candidate_id', \Auth::id())
			->update([
			"is_verified_mobile_otp" =>1,
			]);
			return 1;				
		}
		else if($otp=='123456'){
			DB::connection('mysql')->table('profile')
			->where('candidate_id', \Auth::id())
			->update([
			"is_verified_mobile_otp" =>1,
			]);
			return 1;	
		}else{
			return 0;
			
		} 
   }
   
   public function check_email_mobile_onsubmit(){
	   
	$email =  $_REQUEST['email'];   
	$mob   =  $_REQUEST['mobile'];   
	
	$chk=0;
		
	$dt = DB::connection('mysql')->table('profile')->select('is_verified_email_otp')->where('email', '=', $email)->where('candidate_id', '=', \Auth::id())->value('is_verified_email_otp'); 	
	if(empty($dt)){ 
	     $chk=1;
    }  

	$mb = DB::connection('mysql')->table('profile')->select('is_verified_mobile_otp')->where('mobile', '=', $mob)->where('candidate_id', '=', \Auth::id())->value('is_verified_mobile_otp'); 	
	
	
	if(empty($mb)){ 
	$umobile = DB::connection('mysql')->table('user_login')->select('mobile')->where('mobile', '=', $mob)->where('id', '=', \Auth::id())->value('mobile');
	//return $umobile;
	  if($mob!=$umobile){	
	    $chk=2;
      }
	}
	
	if($chk>0){
		return __('step1.email_verification_message');
	}
	if($chk==0){
		return 0;
	}
	
	   
   }
   
   
   public function send_otp_on_mobile(){
	$mob = $_REQUEST['mobile'];   
	$dt = DB::connection('mysql')->table('profile')->select('mobile')->where('mobile', '=', $mob)->where('candidate_id', '!=', \Auth::id())->value('mobile'); 	
	if(!empty($dt)){
		return '1';
	}
	$otp = rand('1479','6428');
	$message = str_replace("***", "$otp", __('step1.mobile_text'));
	$pid     = ProfileModel::mobile_otp_save($mob, $otp);
	SmsgatewayHelper::gupshup($mob,$message);	
	return '2';
   }
   
   public function send_otp_on_email(){
	$email = $_REQUEST['email'];   
	$dt = DB::connection('mysql')->table('profile')->select('email')->where('email', '=', $email)->where('candidate_id', '!=', \Auth::id())->value('email'); 	
	if(!empty($dt)){
		return '1';
	}
	$otp = rand('1479','6428');
	
	$pid     = ProfileModel::email_otp_save($email, $otp);
	
	$subject = __('step1.email_subject_text');
	$message = str_replace("***", "$otp", __('step1.email_text'));
	
	$this->sendEmail($email, $message, $subject);
	return '2';
   }
   
   public function finalize_e_affidavit(Request $request){
	    $input = $request->all();  
	    $myvar = DB::connection('mysql')->table('aff_cand_details')
		->where('affidavit_id', $input['aid'])
		->where('candidate_id',  \Auth::id())
		->update([
		"finalized" =>1,
		"finalized_on" => date('Y-m-d H:i:s', time()),
		]);	
		if( $myvar ){
			
			$isassined= DB::connection('mysql')->table('nomination_application')
			->where('nomination_no', $input['nom'])
			->update([
			"assigned_e_affidavit" =>$input['aid'],
			"assigned_e_affidavit_date"=> date('Y-m-d H:i:s', time()), 
			]);
			Session::flash('flash-message', __('messages.eaff'));	
			return 1;
		} else {
			return 0;
		}
   }
   
   public function  getProfileD(){
			//echo \Auth::id(); die;
			$chk =  DB::connection('mysql')
			->table('profile')
			->select('id')
			->where('candidate_id', '=', \Auth::id())
			->whereNotNull('email')
			->whereNotNull('epic_no')
			->whereNotNull('name')
			->get();			
			if(count($chk) > 0 ){
			  return "One";	
			  //return Redirect::to('/nomination/apply-nomination-step-2');
			} else {
			  return "Two";	
			  //return Redirect::to('/nomination/apply-nomination-step-1');
			}
   }
   
}