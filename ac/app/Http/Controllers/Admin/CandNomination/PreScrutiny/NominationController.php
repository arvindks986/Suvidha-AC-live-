<?php 
namespace App\Http\Controllers\Admin\CandNomination\PreScrutiny;
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
use App\models\Nomination\ProfileModel;
use App\models\Nomination\NominationApplicationModel;
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
       // $this->middleware('auth');
  
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
	//echo "<pre>"; print_r($data); die;

    return view('nomination/apply-nomination-step-1',$data);

  }


  private function get_form($id,$request,$data = array()){
    
    $object = ProfileModel::get_candidate_profile();

  
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
	//echo "<pre>"; print_r($request->all()); die("--pp");
    DB::beginTransaction();
    try{  
      $result         = ProfileModel::add_nomination_personal_detail($request->all());
    }
    catch(\Exception $e){
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    Session::flash('flash-message',"Personal details has been updated successfully.");

    if($request->has('save_only')){
      return Redirect::back();
    }

    return redirect('/nomination/apply-nomination-step-2');

  }


  public function apply_nomination_step_2($id = 0, Request $request)
  {
   
    if(!ProfileModel::get_candidate_profile()){
      Session::flash('status',0);
      Session::flash('flash-message',"To apply nomination, please fill your personal detail first.");
      return redirect('nomination/apply-nomination-step-1');
    } 
    Session::forget('nomination_id');
	
	if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid'])){
	   $id =  $_REQUEST['nid']; 
	   $idnon =  decrypt_String($_REQUEST['nid']); 
	   $this->convertIntoSession($idnon);
	}
	
	
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

    
    $data['href_back'] = url('nomination/apply-nomination-step-1');
    $data['href_skip'] = url('nomination/apply-nomination-step-3');

    $data['heading_title'] = "Select Election Detail";
    $data['action']        = url('nomination/apply-nomination-step-2/post');
	
	
	
    $data = $this->get_step2_form($id, $request, $data);

	
    return view('nomination/apply-nomination-step-2',$data);

  }

  private function get_step2_form($id,$request,$data = array()){ 
	
    //nomination validation id
	
    if($id != '0'){
      $data['nomination_id']  = $id;
	  
      $id                     = decrypt_String($id);	  
      $object                 = NominationApplicationModel::get_nomination_application($id);
      $data['reference_id']               = $object['nomination_no'];
      $data['href_download_application']  = url("nomination/download/".$data['nomination_id']);
    }
	
    //end nomination validation

    $data['states'] = [];
    $states = StateModel::get_states();
    foreach ($states as $key => $state_iterage) {
      $data['states'][] = [
        'st_code'    => $state_iterage->st_code,
        'st_name'    => $state_iterage->st_name,
        'encoded'    => base64_encode($state_iterage->st_code),
        'election_type_id'  => $state_iterage->election_type_id,
        'election_type'     => $state_iterage->election_type,
        'election_id'       => $state_iterage->election_id,
      ];
    }
	
	
    $data['election_types'] = [];
    $election_types = ElectionModel::get_election_types();
    foreach ($election_types as $key => $election_iterage) {
      $data['election_types'][] = [
        'election_id'      => $election_iterage->ELECTION_ID,
        'election_type_id' => $election_iterage->ELECTION_TYPEID,
        'pc_no'            => $election_iterage->CONST_NO,
        'st_code'          => $election_iterage->ST_CODE,
        'name'             => $election_iterage->ELECTION_TYPE.'-'.$election_iterage->YEAR,
      ];
    }

	//echo "<pre>"; print_r($data); die("data");
    $data['acs'] = [];
    $acs = AcModel::get_acs();
    foreach ($acs as $key => $ac_iterage) {
      $data['acs'][] = [
        'ac_no'      => $ac_iterage->ac_no,
        'ac_name'    => $ac_iterage->ac_no.'-'.$ac_iterage->ac_name,
        'st_code'    => $ac_iterage->st_code,
        'election_id' => $ac_iterage->election_id,
        'encoded'    => base64_encode($ac_iterage->pc_no),
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
      $data['election_id']  = $object['election_id']; 
    }else{
      $data['election_id']  = ''; 
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
	  
	//echo Session::get('nomination_id'); die("PP"); 
	
	$req  = $request->all();  
	//echo "<pre>"; print_r( $req ); die;	
    $count_nomination = NominationApplicationModel::count_nomination_application($request->all());	
	$check='';
	if(isset($req['nomination_id'])){ 
		$check  = decrypt_String($req['nomination_id']); 
	}	
	if(empty($check)){ 
    if($count_nomination >= 4){
      Session::flash('status',0);
      Session::flash("flash-message","You already filled 4 application on this AC.");
      return Redirect::back()->withInput($request->all());
    }
    }
	
	
	//die("PPP KK PP");
    DB::beginTransaction();
    try{
      $result   = NominationApplicationModel::add_nomination_application($request->all());
      //QR code
      if(!$request->has('nomination_id')){
        $data             = NominationApplicationModel::get_nomination(Session::get('nomination_id'));
        $st_code          = $data['st_code'];
        $year             = date('Y');
        $ac_no            = $data['ac_no'];
        $election_name    = 'E'.$data['election_id'];
        $destination_path = FileModel::get_file_path('uploads/qrcode/'.$year.'/ac/'.$election_name.'/'.$st_code.'/'.$ac_no).'/'.$data['id'].'.png';
        \QRCode::text($data['nomination_no'])->setOutfile($destination_path)->png();
        $data['qrcode_path']  = $destination_path;
        $data['qrcode']       = url($destination_path);
        NominationApplicationModel::add_qrcode($data);
      }
      //end QR code
   }
    catch(\Exception $e){ //print_r( $e->getMessage()); die;
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message', $e->getMessage());
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    if($request->has('save_only')){
      Session::flash('flash-message',"Election Details has been updated successfully.");
      return redirect("nomination/apply-nomination-step-2/".encrypt_string(Session::get('nomination_id')));
    }
    return redirect('/nomination/apply-nomination-step-3');

  }

  public function apply_nomination_step_3($id = 0, Request $request){ //echo $_REQUEST['nid']; die;
    //recognized_party
	
	if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid'])){
	   $id =  decrypt_String($_REQUEST['nid']); 
	   $this->convertIntoSession($id);
	}
	
	
	
	
	
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
    $data['heading_title'] = "Form 2B - Nomination Paper ";
    $data['action'] = url('nomination/apply-nomination-step-3/post-part-1');

    $data['href_back'] = url('nomination/apply-nomination-step-2');
    $data['href_skip'] = url('nomination/apply-nomination-step-4');
    $data['href_file_upload'] = url('nomination/upload');
	//echo "<pre>";print_r(Session::get('nomination_id')); die;
    //nomination validation id
	

    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
	//echo "<pre>"; print_r($user_nomination); die("--nid");
    if(!$user_nomination){
       return redirect("nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nomination/apply-nomination-step-2"); 
    }

    $data['reference_id']               = $user_nomination['nomination_no'];
    $data['href_download_application']  = url("nomination/download/".encrypt_string($id)); 
    
    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation
	
    $data = array_merge($data, $user_nomination);
	//echo "<pre>"; print_r($data); die;
    
	$data = $this->get_step3_form($id, $request, $data);
	
    return view('nomination/apply-nomination-step-3',$data);

  }
  //non_recognized_proposers
  //recognized_party
  private function get_step3_form($id,$request,$data = array()){
	  
	//echo "<pre>"; print_r($data); die("--nid");
    $series='';
    $series=$data['serial_no'];
    $part_no=$data['part_no'];
	
	
	$users = ProfileModel::get_candidate_profile();
    $data = array_merge($data, $users);
	
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
	if($data['recognized_party']=='0'){
	 $data['recognized_party']='recognized';	
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

    if($request->old('part_no')){
      $data['part_no']  = $request->old('part_no');
    }else{
      $data['part_no']  = $data['part_no']; 
    }

    if($request->old('resident_ac_no')){
      $data['resident_ac_no']  = $request->old('resident_ac_no');
    }else if(isset($object) && $object){
      $data['resident_ac_no']  = $object['resident_ac_no']; 
    }else{
      $data['resident_ac_no']  = $data['ac_no']; 
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
				->select('candidate_id', 'nomination_id', 'serial_no', 'part_no', 'fullname', 'signature','date')
				->where('candidate_id', '=', \Auth::id())
				->where('nomination_id', '=', $data['nomination_id'])
				->get()
				->toArray();	
				
	
	
	if(isset($nom) && !empty($nom) && (count($nom)>0)){		
		for($i =0; $i < (count($nom)); $i++) { 
		  $non_recognized_proposers[] = [
			  'candidate_id'    => \Auth::id(),
			  'nomination_id'   => $data['nomination_id'],
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
          'candidate_id'    => \Auth::id(),
          'nomination_id'   => $data['nomination_id'],
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
	//echo "<pre>"; print_r($request->all()); die;
    $get_nominattion_detail = NominationApplicationModel::get_nomination_application($request->nomination_id);
    if(!$get_nominattion_detail){
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
	if($request['recognized_party']=='not-recognized'){
	$request['recognized_party']='2';
	}
    if($request['recognized_party']=='recognized'){
	$request['recognized_party']='0';
	}	
	//echo "<pre>"; print_r($request->all());  die;
	//echo $request['recognized_party']; die;
    $request->merge(['image_name' => $request->image]);
    DB::beginTransaction();
    try{  
      if($request->recognized_party == '0'){  
        $result   = NominationApplicationModel::add_nomination_part1($request->all());
      }else{  
        NominationApplicationModel::add_nomination_part2($request->all());
        NominationProposerModel::delete_proposer($request->nomination_id);
        foreach($request->non_recognized_proposers as $non_recognized_proposer){
          NominationProposerModel::add_proposer($non_recognized_proposer);
        }
      }
    }
    catch(\Exception $e){ //print_r( $e->getMessage()); die;
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    if($request->has('save_only')){
      Session::flash('flash-message',"Part I/II updated successfully.");
      return Redirect::back();
    }
    return redirect('/nomination/apply-nomination-step-4');

  }


  public function apply_nomination_step_4($id = 0, Request $request){
	  
	if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid'])){
	   $id =  decrypt_String($_REQUEST['nid']); 
	   $this->convertIntoSession($id);  
	}
	
	  
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
    $data['heading_title'] = "Form 2B - Nomination Paper";
    $data['action'] = url('nomination/apply-nomination-step-4/post');

    $data['href_back'] = url('nomination/apply-nomination-step-3');
    $data['href_skip'] = url('nomination/apply-nomination-step-5');


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
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nomination/apply-nomination-step-2"); 
    }

    $data['reference_id']               = $user_nomination['nomination_no'];
    $data['href_download_application']  = url("nomination/download/".encrypt_string($id));

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    $data['recognized_party'] = $user_nomination['recognized_party'];

    $data = array_merge($data, $user_nomination);
    $data = $this->get_step4_form($id, $request, $data);
	
    return view('nomination/apply-nomination-step-4',$data);
  }


  public function get_step4_form($id, $request, $data = []){

    $candidate = ProfileModel::get_candidate_profile();
    $data['categories'] = [
      ['id' => 'sc', 'name' => 'SC'],
      ['id' => 'st', 'name' => 'ST'],
      ['id' => 'general', 'name' => 'GENERAL'],
    ];      

    if($request->old('category')){
      $data['category']  = $request->old('category');
    }else{
      $data['category']  = $data['category']; 
    }

    $data['states'] = [];
    $states = StateModel::orderBy('ST_NAME','ASC')->get();
    foreach ($states as $key => $state_iterage) {
      $data['states'][] = [
        'st_code'    => $state_iterage->ST_CODE,
        'st_name'    => $state_iterage->ST_NAME,
      ];
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
    $parties = PartyModel::get_parties($party_filter);
    foreach ($parties as $iterate_party) {
      $data['parties'][] = [
        'party_id'  => $iterate_party['party_id'],
        'name'      => $iterate_party['name']
      ];
    }
    return $data;
  }


  public function save_step_4(NominationPart3Request $request){

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
      NominationApplicationModel::add_nomination_part3($request->all());
    }
    catch(\Exception $e){
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
    return redirect('/nomination/apply-nomination-step-5');
  }

  //save step 5
  public function apply_nomination_step_5($id = 0, Request $request){
	
	if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid'])){
	   $id =  decrypt_String($_REQUEST['nid']); 
	   $this->convertIntoSession($id);  
	}
		
	  
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
    $data['heading_title'] = "Form 2B - Nomination Paper";
    $data['action'] = url('nomination/apply-nomination-step-5/post');

    $data['href_back'] = url('nomination/apply-nomination-step-4');
    $data['href_skip'] = url('nomination/apply-nomination-step-6');

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
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nomination/apply-nomination-step-2"); 
    }

    $data['reference_id']               = $user_nomination['nomination_no'];
    $data['href_download_application']  = url("nomination/download/".encrypt_string($id));

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
	
	
    $data = $this->get_step5_form($id, $request, $data);
	
	return view('nomination/apply-nomination-step-5',$data);
  }


  public function get_step5_form($id, $request, $data = []){
    if($id>0){
      $object = NominationApplicationModel::get_nomination_application($id);
    }

    $data['yes_no_lists'] = [
      ['id' => '', 'name' => 'Select'],
      ['id' => 'yes', 'name' => 'Yes'],
      ['id' => 'no', 'name' => 'no'],
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
      $data['police_cases']  = NominationPoliceCaseModel::get_police_cases($object['id']); 
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
          if(!$value || trim($value) == ''){
            $errors[$key][$second_key] = "You can't leave this blank.";
            $is_error = true;
          }else{
            $errors[$key][$second_key] = false;
          }
        }
      }
	 // echo "<pre>"; print_r($request->all()); die("Testing");	
      if(count($errors)>0){
        $request->merge(['custom_errors' => $errors]);
      }

      if($is_error){
        Session::flash('flash-message','Please check your form data.');
        return Redirect::back()->withInput($request->all());
      }
    }
	//echo "<pre>"; print_r($request->all()); die;
    DB::beginTransaction();
    try{
	  	//
      NominationApplicationModel::add_nomination_part3a($request->all());
      NominationPoliceCaseModel::delete_police_case($request->nomination_id);
      if($request->have_police_case == 'yes'){
        foreach($request->police_case as $iterate_police){ 
		  if(isset($iterate_police['state'])){
			$iterate_police['st_code']=str_replace('state', 'st_code',    $iterate_police['state']);  
		  } //echo "<pre>"; print_r($iterate_police); die;
          NominationPoliceCaseModel::add_police_case($iterate_police, $request->nomination_id);
        }
      }
	//die;	
    }
    catch(\Exception $e){ print_r( $e->getMessage()); //die;
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
	//print_r($request->all()); die("Testing");
    DB::commit();
    Session::flash('status',1);
    if($request->has('save_only')){
      Session::flash('flash-message',"PART IIIA updated successfully.");
      return Redirect::back();
    }
    return redirect('/nomination/apply-nomination-step-6');
  }

  public function apply_nomination_step_6($id = 0, Request $request){
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

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nomination/apply-nomination-step-2"); 
    }

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
      NominationApplicationModel::add_affidavit($values);
    }
    catch(\Exception $e){
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    Session::flash('flash-message',"Kindly verify the below detail and finalize the nomination.");
    return redirect('/nomination/apply-nomination-finalize');
  }
  
  public function apply_nomination_finalize($id = 0, Request $request){
	
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
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nomination/apply-nomination-step-2"); 
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
    $data['affidavit']  = url($data['affidavit']);

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


  public function save_nomination_finalize($id = 0, Request $request){
    DB::beginTransaction();
    try{
      NominationApplicationModel::finalize_nomination($request->nomination_id);
    }
    catch(\Exception $e){
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    Session::flash('flash-message',"Your application has been added successfully.");
    return redirect('/nomination/my-nominations');
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
				$datet = explode(" ", $res['appoinment_scheduled_datetime']);
				$datesss.= $datet[0].'+++'.substr($datet[1], 0, -3).'***'; 
			}
		}
	}
	$data['datesss']=substr($datesss, 0, -3);
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
			$timedd = substr($datetiime[1], 0, -3);
			$daydd  = date('D', strtotime($datedd));
			$ampm   =  date('A', strtotime($nom[0]->appoinment_scheduled_datetime));			
		}
		$data['appoinment_scheduled_datetime_one'] = $nom[0]->appoinment_scheduled_datetime;
		$data['appoinment_scheduled_date_one'] = $datedd;
		$data['appoinment_scheduled_time_one'] = $timedd;
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
	$name_excel = time();

    $setting_pdf = [
      'margin_top'        => 40,        // Set the page margins for the new document.
      'margin_bottom'     => 10,    
    ];
	
    $pdf = \PDF::loadView('nomination/download-scheduled', $data, [], $setting_pdf);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  } 
  
  
  public function confirm_schedule_appointment(Request $Request){ 
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
				$datet = explode(" ", $res['appoinment_scheduled_datetime']);
				$datesss.= $datet[0].'+++'.substr($datet[1], 0, -3).'***'; 
			}
		}
	}
	$data['datesss']=substr($datesss, 0, -3);
	$nom=DB::connection('mysql')->table('nomination_application')
		->select('*')
		->where('id', '=', $_REQUEST['id'])
		->get()
		->toArray();
		
	$start_end = $this->getStartEndDateNomination($nom[0]->st_code, $nom[0]->ac_no);
	//echo "<pre>"; print_r($start_end); die;
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
		$data['ACNO'] =   $nom[0]->ac_no;
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
		//echo "<pre>"; print_r($data); die;
		$datedd = $timedd  = $daydd = $ampm = '';
		if(isset($nom[0]->appoinment_scheduled_datetime)){
			$datetiime=explode(" ", $nom[0]->appoinment_scheduled_datetime);
			$datedd =  date("d-m-Y", strtotime($datetiime[0]));
			$timedd =  substr($datetiime[1], 0, -3);
			$daydd  =  date('D', strtotime($datedd));
			$ampm   =  date('A', strtotime($nom[0]->appoinment_scheduled_datetime));			
		}
		$data['appoinment_scheduled_datetime_one'] = $nom[0]->appoinment_scheduled_datetime;
		$data['appoinment_scheduled_date_one'] = $datedd;
		$data['appoinment_scheduled_time_one'] = $timedd;
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
	
	return view('nomination/confirm-schedule-appointment', $data);
  } 
  
  public function cancel_nomination(Request $request){
	$input = $request->All();
	
	$myvar = DB::connection('mysql')->table('nomination_application')
	->where('id', $input['nom_id'])
	->update([
	"appoinment_status" =>2
	]);	
	
	
	$myvar = DB::connection('mysql')->table('candidate_online_appointment')
	->where('nom_id', $input['nom_id'])
	->update([
	"status" =>2
	]);	
	
	$nom=DB::connection('mysql')->table('candidate_online_appointment')
	->select('*')
	->where('nom_id', '=', $input['nom_id'])
	->get()
	->toArray();	
	$msg='';		
	$isUpdate = DB::connection('mysql')->table('candidate_appointment_logs')
	->insert([
	"appointment_id" =>$nom[0]->id,
	"nom_id" =>$input['nom_id'],
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
	Session::flash('flash-message',"Appointment cancelled successfully");
	Session::flash('is_scheduled',"cancel");
	return redirect('nomination/confirm-schedule-appointment?query=?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id='.$input['nom_id'].'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9');
    }
	
  public function save_confirm_schedule_appointment(Request $request){
	$input = $request->All();
	$getdata =   DB::connection('mysql')
						->table('nomination_application')
						->select('*')
						->where('id', '=', $input['id'])
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
	
	//echo "<pre>"; print_r($input);	

	$adate  = explode("***", $input['daytime']);
	$sch  = explode("___", $adate['0']);
	$time = $sch[0];
	$day =  $sch[1];

	
	$appdate =  $adate['1'];
	$appdatetime =  $adate['1'].' '.$time.':00';

	$myvar = DB::connection('mysql')->table('nomination_application')
	->where('id', $input['id'])
	->update([
	"is_appoinment_scheduled" =>1,
	"appoinment_status" =>null,
	"appoinment_scheduled_datetime"=> $appdatetime,
	]);	
	
	$mydatata   =DB::connection('mysql')->table('nomination_application')
				->select('*')
				->where('id', '=', $input['id'])
				->get()
				->toArray();
	//echo $mydatata[0]->st_code;die;
	//echo "<pre>"; print_r($mydatata); die;	


	$nom    =   DB::connection('mysql')->table('candidate_online_appointment')
				->select('*')
				->where('nom_id', '=', $input['id'])
				->get()
				->toArray();
	
	
	
	$msg='';
	if(count($nom)==0){
		$isUpdate = DB::connection('mysql')->table('candidate_online_appointment')
		->insert([
		"nom_id" =>$input['id'],
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
		"nom_id" =>$input['id'],
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
		->where('nom_id', $input['id'])
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
	Session::flash('is_scheduled',"yes");
	
	
	return redirect('nomination/confirm-schedule-appointment?query=?query=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9&id='.$input['id'].'&data=eyJpdiI6InpaYVpGeG5IajV0c2syS1RVb1B1K3c9PSIsInZhbHVlIjoiU3RKVnRMOUgyeTltRXU3OVFzd2Q3QT09IiwibWFjIjoiNWVjZjY5Y2ZlMWU1OTBjZTJmNWQyYjJlMTI3OGVjY2RkNDhlMmJmYmZmMjgzOGQyODQ2MWFjMzQ3ZjcyMzRjMyJ9');
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
        'st_code' => $result['st_code'],
        'ac_no' => $result['ac_no'],
        'status'        => $status,
        'updated_at'    =>$result['updated_at'],
        'step'       	=>$result['step'],
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

  public function apply_pre_scrutiny(Request $request){
	$input = $request->All();
	//echo "<pre>"; print_r($input); die;
	$rules = [
          'selectRadioButton' => 'required'
    ];
    $messages = [
          'selectRadioButton.required' => 'Please select nomination!'
    ];	
	Validator($input, $rules, $messages)->validate();
    
	$msg='';
	
	DB::connection('mysql')->table('nomination_application')
	->where('id', $input['selectRadioButton'])
	->update([
	"is_apply_prescrutiny" =>1,
	"prescrutiny_apply_datetime"=> date('Y-m-d H:i:s', time()),
	]);	
	
	/*$isUpdate = DB::connection('mysql')->table('prescrutiny_history')
	->insert([
	"nom_id" =>$input['selectRadioButton'],
	"candidate_id"=>\Auth::id(),
	"st_code" =>$input['st_code'],
	"ac" =>$input['ac'],
	"created_at"=> date('Y-m-d H:i:s', time()),
	"created_by"=> \Auth::id(),
	]); */
	
	Session::flash('flash-message',"Prescrutiny applied successfully");
	return redirect('nomination/submit-for-pre-scrutiny');
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
    
    $data['profileimg']  = url($data['image']); 
    $data['qr_code']      = url($data['qrcode']);
    $data['apply_date']  = date('d/m/Y', strtotime($data['apply_date'])); 
    $data['non_recognized_proposers']   = NominationProposerModel::get_proposers($data['id']);  
    $data['police_cases']               = NominationPoliceCaseModel::get_police_cases($data['nomination_id']); 
    $data['affidavit']  = url($data['affidavit']);
    return view('nomination/view-nomination',$data);
  }


  public function download_nomination($id, Request $request){
	
    $id = decrypt_string($id);
	
	
    $user_nomination = NominationApplicationModel::get_nomination_application($id);
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
	$data['qrcode'] =='';
    if(!empty($data['image'])){
    $data['profileimg']  =  url($data['image']); 
	} else {
	$data['profileimg']  =  '#'; 
	}
	if(isset($data['qrcode'])){
    $data['qrcode']  =  url($data['qrcode']); 
	} else {
	$data['qrcode']  =  '#'; 
	}
	
    $data['apply_date']  = date('d/m/Y', strtotime($data['apply_date'])); 
    $data['non_recognized_proposers']   = NominationProposerModel::get_proposers($data['id']);  
    $data['police_cases']               = NominationPoliceCaseModel::get_police_cases($data['nomination_id']); 
	if(!empty($data['affidavit'])){
    $data['affidavit']  =  url($data['affidavit']); 
	} else {
	$data['affidavit']  =  '#'; 
	}
    //$data['affidavit']      = url($data['affidavit']);
    $data['nomination_no']  = $data['nomination_no'];

    $name_excel = time();

    $setting_pdf = [
      'margin_top'        => 80,        // Set the page margins for the new document.
      'margin_bottom'     => 10,    
    ];
	
    $pdf = \PDF::loadView('nomination/download-nomination',$data, [], $setting_pdf);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');

  }

  public function upload_files(Request $request){

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

    $destination_path = 'candprofile/'.$year.'/ac/'.$election_name.'/'.$st_code .'/'. $ac_no;
    $common = new \App\Http\Controllers\Common\CandidatOnlineNomination();
    $results = $common->upload($request, 2048, 'image', $destination_path);
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
    $common = new \App\Http\Controllers\Common\CandidatOnlineNomination();
    $results = $common->upload($request, 10240, 'pdf', $destination_path);
    return $results;
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
  public function convertIntoSession($ids){
	return \Session::put('nomination_id', $ids);
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
  public function get_nomination_start_end_date(Request $request){
	$input = $request->all();
	$schid  = DB::connection('mysql')
	->table('pd_scheduledetail')
	->select('scheduleid')
	->where('st_code', '=', $input['sId'])
	->where('ac_no', '=', $input['ac'])
	->value('scheduleid');
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
  public function getStartEndDateNomination($st, $ac){
	$schid  = DB::connection('mysql')
	->table('pd_scheduledetail')
	->select('scheduleid')
	->where('st_code', '=', $st)
	->where('ac_no', '=', $ac)
	->value('scheduleid');
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
}