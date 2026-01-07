<?php
namespace App\Http\Controllers\Admin\Nfd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB, Validator, Config, \PDF, Response;
use App\models\Admin\Nomination\{ProfileModel, NominationApplicationModel, NominationProposerModel, NominationPoliceCaseModel};
use App\models\Admin\Nomination\UserModel;
use App\models\Common\{StateModel, FileModel, PcModel, AcModel, DistrictModel, PartyModel, SymbolModel, ElectionModel};
use App\Http\Requests\Admin\Nomination\{NominationPart12Request, NominationPart3Request, NominationPart3aRequest};


class NominationController extends Controller
{

  public $auth_id       = 0;
  public $upload_folder = '';

  public function __construct(Request $request){
    $this->middleware(function ($request, $next) {
      if(!Session::has('otp_mobile') || (Session::has('otp_mobile') & Session::get('otp_mobile') == '')){
        return redirect("nfd/nomination");
      }
      return $next($request);
    });
    $this->upload_folder = config("public_config.upload_folder");
  }


  public function get_nomination_by_mobile(Request $request){
    Session::forget('nomination_id');
    $data                   = [];
    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('nfd/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];

    $data['is_active']     = 'nomination';
    $data['heading_title'] = "My Nominations";
    $data['results']       = [];
    $results = NominationApplicationModel::get_nominations(Session::get('otp_mobile'));
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
        'view_href'     => url('nfd/nomination/detail/'.$encrypt_id),
        'edit_href'     => url('nfd/nomination/apply-nomination-step-2/'.$encrypt_id),
        'is_finalize'   => $result['finalize'],
        'download_href'      => url("nfd/nomination/download/".encrypt_string($result['id'])),
        'encrypt_id'  => $encrypt_id
      ];
    }
    $data['action']               = url('nfd/nomination/apply-nomination-step-1/post');
    $data['nomination_page']      = url('nfd/nomination/apply-nomination-step-2');
    $data['href_new_nomination']  = url('nfd/nomination');

    $data             = $this->get_form(0, $request, $data);
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/my-nomination',$data);

  }

  public function get_step_1($id,$request,$data = array()){
    $data             = $this->get_form(0, $request, $data);
    $data['action']               = url('nfd/nomination/apply-nomination-step-1/post');
    $data['nomination_page']      = url('nfd/nomination/apply-nomination-step-2');
    $data['href_new_nomination']  = url('nfd/nomination');
    return view('admin/nfd/nomination/form/step1',$data);
  }


  public function get_form($id,$request,$data = array()){
     
    $object = ProfileModel::get_cand_id_by_mobile(Session::get('otp_mobile'));
   
    if($request->old('name')){
      $data['name']  = $request->old('name');
    }elseif(isset($object) && ($object)){
      $data['name']  = $object['name']; 
    }else{
      $data['name']  = ''; 
    }

    if($request->old('email')){
      $data['email']  = $request->old('email');
   }elseif(isset($object) && ($object)){
      $data['email']  = $object['email']; 
    }else{
      $data['email']  = ''; 
    }

    $data['mobile']  = Session::get('otp_mobile'); 
    
    if($request->old('hname')){
      $data['hname']  = $request->old('hname');
   }elseif(isset($object) && ($object)){
      $data['hname']  = $object['hname']; 
    }else{
      $data['hname']  = ''; 
    }

    if($request->old('vname')){
      $data['vname']  = $request->old('vname');
   }elseif(isset($object) && ($object)){
      $data['vname']  = $object['vname']; 
    }else{
      $data['vname']  = ''; 
    }

    if($request->old('alias_name')){
      $data['alias_name']  = $request->old('alias_name');
   }elseif(isset($object) && ($object)){
      $data['alias_name']  = $object['alias_name']; 
    }else{
      $data['alias_name']  = ''; 
    }

    if($request->old('alias_hname')){
      $data['alias_hname']  = $request->old('alias_hname');
   }elseif(isset($object) && ($object)){
      $data['alias_hname']  = $object['alias_hname']; 
    }else{
      $data['alias_hname']  = ''; 
    }
     if($request->old('alias_vname')){
      $data['alias_vname']  = $request->old('alias_vname');
    }else if(isset($object) and ($object)){
      $data['alias_vname']  = $object['alias_vname']; 
    }else{
      $data['alias_vname']  = ''; 
    }
    if($request->old('father_name')){
      $data['father_name']  = $request->old('father_name');
   }elseif(isset($object) && ($object)){
      $data['father_name']  = $object['father_name']; 
    }else{
      $data['father_name']  = ''; 
    }

    if($request->old('father_hname')){
      $data['father_hname']  = $request->old('father_hname');
   }elseif(isset($object) && ($object)){
      $data['father_hname']  = $object['father_hname']; 
    }else{
      $data['father_hname']  = ''; 
    }

    if($request->old('father_vname')){
      $data['father_vname']  = $request->old('father_vname');
   }elseif(isset($object) && ($object)){
      $data['father_vname']  = $object['father_vname']; 
    }else{
      $data['father_vname']  = ''; 
    }

    if($request->old('pan_number')){
      $data['pan_number']  = $request->old('pan_number');
   }elseif(isset($object) && ($object)){
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
   }elseif(isset($object) && ($object)){
      $data['category']  = $object['category']; 
    }else{
      $data['category']  = ''; 
    }

    if($request->old('dob')){
      $data['dob']  = $request->old('dob');
   }elseif(isset($object) && ($object)){
      $data['dob']  = $object['dob']; 
    }else{
      $data['dob']  = ''; 
    }

    if($request->old('age')){
      $data['age']  = $request->old('age');
   }elseif(isset($object) && ($object)){
      $data['age']  = $object['age']; 
    }else{
      $data['age']  = ''; 
    }

    if($request->old('gender')){
      $data['gender']  = $request->old('gender');
   }elseif(isset($object) && ($object)){
      $data['gender']  = $object['gender']; 
    }else{
      $data['gender']  = ''; 
    }

    if($request->old('address')){
      $data['address']  = $request->old('address');
   }elseif(isset($object) && ($object)){
      $data['address']  = $object['address']; 
    }else{
      $data['address']  = ''; 
    }

    if($request->old('haddress')){
      $data['haddress']  = $request->old('haddress');
   }elseif(isset($object) && ($object)){
      $data['haddress']  = $object['haddress']; 
    }else{
      $data['haddress']  = ''; 
    }

    if($request->old('vaddress')){
      $data['vaddress']  = $request->old('vaddress');
   }elseif(isset($object) && ($object)){
      $data['vaddress']  = $object['vaddress']; 
    }else{
      $data['vaddress']  = ''; 
    }


   //  if($request->old('address_2')){
   //    $data['address_2']  = $request->old('address_2');
   // }elseif(isset($object) && ($object)){
   //    $data['address_2']  = $object['address_2']; 
   //  }else{
   //    $data['address_2']  = ''; 
   //  }

   //  if($request->old('address_2_hindi')){
   //    $data['address_2_hindi']  = $request->old('address_2_hindi');
   // }elseif(isset($object) && ($object)){
   //    $data['address_2_hindi']  = $object['address_2_hindi']; 
   //  }else{
   //    $data['address_2_hindi']  = ''; 
   //  }

    if($request->old('district')){
      $data['district']  = $request->old('district');
   }elseif(isset($object) && ($object)){
      $data['district']  = $object['district']; 
    }else{
      $data['district']  = ''; 
    }

    if($request->old('state')){
      $data['state']  = $request->old('state');
   }elseif(isset($object) && ($object)){
      $data['state']  = $object['state']; 
    }else{
      $data['state']  = ''; 
    }

    if($request->old('ac')){
      $data['ac']  = $request->old('ac');
   }elseif(isset($object) && ($object)){
      $data['ac']  = $object['ac']; 
    }else{
      $data['ac']  = ''; 
    }

    if($request->old('epic_no')){
      $data['epic_no']  = $request->old('epic_no');
   }elseif(isset($object) && ($object)){
      $data['epic_no']  = $object['epic_no']; 
    }else{
      $data['epic_no']  = ''; 
    }

    if($request->old('serial_no')){
      $data['serial_no']  = $request->old('serial_no');
   }elseif(isset($object) && ($object)){
      $data['serial_no']  = $object['serial_no']; 
    }else{
      $data['serial_no']  = ''; 
    }

    if($request->old('part_no')){
      $data['part_no']  = $request->old('part_no');
   }elseif(isset($object) && ($object)){
      $data['part_no']  = $object['part_no']; 
    }else{
      $data['part_no']  = ''; 
    }

    return $data;
  }

  public function save_step_1(Request $request){
    $rules = [
        'name'         => 'required|min:3|max:255',
        'hname'        => 'required|min:3|max:255',
        'vname'        => 'required|min:3|max:255',
        'alias_name'   => 'required|min:3|max:255',
        'alias_hname'  => 'required|min:3|max:255',
        'father_name'  => 'required|min:3|max:255',
        'father_hname' => 'required|min:3|max:255',
        'father_vname' => 'required|min:3|max:255',
        'category'     => 'required|in:sc,st,general',
        'pan_number'   => 'required|size:10|pan',
        'age'          => 'required|integer|age',
        'address'      => 'required|min:3|max:255',
        'haddress'     => 'required|min:3|max:255',
        'vaddress'     => 'required|min:3|max:255',
        'part_no'      => 'required|integer|min:1',
        'serial_no'    => 'required|integer|min:1',
        'state'        => 'required|exists:m_state,ST_CODE',
        'district'     => 'required|exists:m_district,DIST_NO',
        'ac'           => 'required|exists:m_ac,AC_NO',
        'gender'       => 'required|in:male,female,third'
    ];

    $object = ProfileModel::get_cand_id_by_mobile(Session::get('otp_mobile'));
    if($object){
        $id                 = $object['id'];            
        $rules['email']     = 'required|email|unique:profile,email,'.$id;
        $rules['mobile']    = 'required|mobile|unique:profile,mobile,'.$id;
        $rules['epic_no']   = 'required|unique:profile,epic_no,'.$id;
    }else{
      $rules['email']     = 'required|email|unique:profile';
      $rules['mobile']    = 'required|mobile|unique:profile';
      $rules['epic_no']   = 'required|unique:profile';
    }

    $validator = Validator::make($request->all(), $rules);
    if($validator->fails()) {
      return Response::json([
        'status' => false,
        'errors' => $validator->errors()->getMessageBag()
      ]);
    }

    
      $user = UserModel::add_user([
        'mobile' => Session::get('otp_mobile')
      ]);
      if($request->nomination_id=='' || $request->nomination_id=='0')
            $request->merge(['candidate_id' => $user->id]);
      else
         $request->merge(['candidate_id' =>'0']);

      ProfileModel::add_nomination_personal_detail($request->all());
    DB::beginTransaction();
    try{  }
    catch(\Exception $e){
      DB::rollback();
      return Response::json([
        'status' => false,
        'errors' => ["warning" => "Please refresh and try again."]
      ]);
    }
    DB::commit();
    return \Response::json([
      'status' => true,
    ]);

  }


  public function apply_nomination_step_2($id = 0, Request $request)
  {
    Session::forget('nomination_id');
    $data                   = [];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Select Election Detail";
    $data['action']        = url('nfd/nomination/apply-nomination-step-2/post');
    $data = $this->get_step2_form($id, $request, $data);
    return view('admin/nfd/nomination/apply-nomination-step-2',$data);

  }

  public function get_step2_form($id,$request,$data = array()){

    //nomination validation id
    if($id != '0'){
      $data['nomination_id']  = $id;
      $id                     = decrypt_String($id);
      $object                 = NominationApplicationModel::get_nomination_application($id);
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
   }elseif(isset($object) && ($object)){
      $data['st_code']  = $object['st_code']; 
    }else{
      $data['st_code']  = ''; 
    }

    if($request->old('election_id')){
      $data['election_id']  = $request->old('election_id');
   }elseif(isset($object) && ($object)){
      $data['election_id']  = $object['election_id']; 
    }else{
      $data['election_id']  = ''; 
    }

    if($request->old('ac_no')){
      $data['ac_no']  = $request->old('ac_no');
   }elseif(isset($object) && ($object)){
      $data['ac_no']  = $object['ac_no']; 
    }else{
      $data['ac_no']  = ''; 
    }

    return $data;
  }

  public function save_step_2(Request $request){
    $rules = [
        'st_code'         => 'required|exists:m_state,ST_CODE',
        'ac_no'           => 'required|exists:m_ac,AC_NO',
        'election_id'     => 'required|exists:m_election_details,ELECTION_ID',
    ];
    $validator = Validator::make($request->all(), $rules);
    if($validator->fails()) {
      return Response::json([
        'status' => false,
        'errors' => $validator->errors()->getMessageBag()
      ]);
    }

    try{
      if(!$request->has('nomination_id')){
                $user = UserModel::add_user([
                                'mobile' => Session::get('otp_mobile')
                                  ]);
                  $request->merge(['candidate_id' => $user->id]);
              }
      $request->merge(['nomination_type' =>'2']);
       $request->merge(['application_type' =>'2']);
      $count_nomination = NominationApplicationModel::count_nomination_application($request->all());
      if($count_nomination >= 4){
        return Response::json([
          'status' => false,
          'errors' => ["warning" => "You already filled 4 application on this AC."]
        ]);
      }
      $result   = NominationApplicationModel::add_nomination_application($request->all());
    }
    catch(\Exception $e){
      return Response::json([
        'status' => false,
        'errors' => ["warning" => "Please refresh and try again."]
      ]);
    }
    Session::flash('success_mes',"Election Details has been updated successfully.");
    return \Response::json([
      'status' => true,
       
      'redirect' => url('nfd/nomination/apply-nomination-step-3')
    ]);

  }

  public function apply_nomination_step_3($id = 0, Request $request){

    $data                   = [];
    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('nfd/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Form 2B - Nomination Paper ";
    $data['action'] = url('nfd/nomination/apply-nomination-step-3/post-part-1');
    $data['href_file_upload'] = url('nfd/nomination/upload');

    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("nfd/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nfd/nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nfd/nomination/list"); 
    }

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    $data = array_merge($data, $user_nomination);
    $data = $this->get_step3_form($id, $request, $data);
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/apply-nomination-step-3',$data);

  }

  public function get_step3_form($id,$request,$data = array()){
   // dd($data);
    $users = ProfileModel::get_cand_id_by_mobile(Session::get('otp_mobile'));
    $data = array_merge($data, $users);
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
    }else{
      $data['recognized_party']  = ($data['recognized_party'])?$data['recognized_party']:'1'; 
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

    if($request->old('serial_no')){
      $data['serial_no']  = $request->old('serial_no');
    }else{
      $data['serial_no']  = $data['serial_no']; 
    }

    if($request->old('part_no')){
      $data['part_no']  = $request->old('part_no');
    }else{
      $data['part_no']  = $data['part_no']; 
    }

    if($request->old('resident_ac_no')){
      $data['resident_ac_no']  = $request->old('resident_ac_no');
   }elseif(isset($object) && ($object)){
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
    $non_recognized_proposers1 =NominationProposerModel::get_proposers($data['nomination_id']);
    $non_recognized_proposers = [];
    if(isset($non_recognized_proposers1) and ($non_recognized_proposers1)) {
            foreach ( $non_recognized_proposers1 as $key => $non) {
             
                      $non_recognized_proposers[] = [
                          'candidate_id'    =>$non['candidate_id'],
                          'nomination_id'   => $non['nomination_id'],
                          's_no'       =>$non['s_no'],
                          'serial_no'  =>$non['serial_no'],
                          'part_no'    =>$non['part_no'],
                          'fullname'   =>$non['fullname'],
                          'date'       =>$non['date'],
                          'signature'  =>$non['signature'],
                          ];
           }
    }
    else {
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
   }elseif(isset($object) && ($object)){
      $data['non_recognized_proposers']  = $data['non_recognized_proposers']; 
    }else{
      $data['non_recognized_proposers']  = $non_recognized_proposers; 
    }

    return $data;
  }

  public function save_step_3(NominationPart12Request $request){

    $get_nominattion_detail = NominationApplicationModel::get_nomination_application($request->nomination_id);
    if(!$get_nominattion_detail){
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
 
    $request->merge(['image_name' => $request->image]);
    DB::beginTransaction();
    try{ 
      if($request->recognized_party == '1'){
        $result   = NominationApplicationModel::add_nomination_part1($request->all());
      }else{
        NominationApplicationModel::add_nomination_part2($request->all());
        //NominationProposerModel::delete_proposer($request->nomination_id);
        foreach($request->non_recognized_proposers as $non_recognized_proposer){
           $a1=array('st_code'=>$request->st_code,
                      'ac_no'=>$request->ac_no,
                      'election_id' =>$request->election_id);
          $non_recognized_proposer=array_merge($non_recognized_proposer,$a1);
          NominationProposerModel::add_proposer($non_recognized_proposer);
        }
      }
    }
    catch(\Exception $e){
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
    DB::commit();
    Session::flash('status',1);
    Session::flash('flash-message',"Nomination added successfully.");
     Session::flash('success_mes',"Nomination added successfully.");
    if(Auth::user()->role_id == 19){
      return redirect('roac/nomination/apply-nomination-step-4');
    }

    return redirect('nfd/nomination/apply-nomination-step-4');

  }


  public function apply_nomination_step_4($id = 0, Request $request){
     $data                  = [];
    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('nfd/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Form 2B - Nomination Paper";
    $data['action'] = url('nfd/nomination/apply-nomination-step-4/post');
    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("nfd/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nfd/nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nfd/nomination/apply-nomination-step-2"); 
    }

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    $data['recognized_party'] = $user_nomination['recognized_party'];

    $data = array_merge($data, $user_nomination);
    $data = $this->get_step4_form($id, $request, $data);
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/apply-nomination-step-4',$data);
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

    $party_filter = [];
    if($data['recognized_party'] == '1'){
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

    try{  
      NominationApplicationModel::add_nomination_part3($request->all());
    }
    catch(\Exception $e){
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
     Session::flash('status',1);
      Session::flash('success_mes',"Nomination added successfully.");
    if(Auth::user()->role_id == 19){
      return redirect('roac/nomination/apply-nomination-step-5');
    }
    return redirect('nfd/nomination/apply-nomination-step-5');
  }

  //save step 5
  public function apply_nomination_step_5($id = 0, Request $request){
    $data                  = [];
    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('nfd/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Form 2B - Nomination Paper";
    $data['action'] = url('nfd/nomination/apply-nomination-step-5/post');

    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("nfd/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nfd/nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nfd/nomination/apply-nomination-step-2"); 
    }

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    $data['recognized_party'] = $user_nomination['recognized_party'];

    $data = array_merge($data, $user_nomination);
    $data = $this->get_step5_form($id, $request, $data);
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/apply-nomination-step-5',$data);
  }


  public function get_step5_form($id, $request, $data = []){
    if($id>0){
      $object = NominationApplicationModel::get_nomination_application($id);
    }

    $data['yes_no_lists'] = [
      ['id' => '', 'name' => 'Select'],
      ['id' => '1', 'name' => 'Yes'],
      ['id' => '2', 'name' => 'no'],
    ];  

    $data['custom_errors']      = [];

    if($request->old('have_police_case')){
      $data['have_police_case']  = $request->old('have_police_case');
    }else{
      $data['have_police_case']  = ($data['have_police_case'])?$data['have_police_case']:'2'; 
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

    $data['police_cases'] = [];
    $police_cases[] = [
      'case_no' => '',
      'police_station' => '',
      'case_st_code' => '',
      'case_dist_no' => '',
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
   }elseif(isset($object) && ($object)){
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

    if($request->has('police_case') && count($request->police_case) && $request->have_police_case == '1'){
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

      if(count($errors)>0){
        $request->merge(['custom_errors' => $errors]);
      }

      if($is_error){
        Session::flash('flash-message','Please check your form data.');
        return Redirect::back()->withInput($request->all());
      }
    }

    DB::beginTransaction();
    try{
      NominationApplicationModel::add_nomination_part3a($request->all());
      //NominationPoliceCaseModel::delete_police_case($request->nomination_id);
      if($request->have_police_case == '1'){
        foreach($request->police_case as $iterate_police){
          $a1=array('st_code'=>$request->st_code,'ac_no'=>$request->ac_no,
              'election_id' =>$request->election_id);
          $iterate_police=array_merge($iterate_police,$a1);
          NominationPoliceCaseModel::add_police_case($iterate_police, $request->nomination_id);
        }
      } 
    }
    catch(\Exception $e){
      DB::rollback();
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
     DB::commit();
    Session::flash('status',1);
    Session::flash('success_mes',"Nomination added successfully.");

    if(Auth::user()->role_id == 19){
      return redirect('roac/nomination/apply-nomination-step-6');
    }

    return redirect('nfd/nomination/apply-nomination-step-6');
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
      'href'    => url('nfd/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']        = 'nomination';
    $data['heading_title']    = "Upload Affidavit";
    $data['action']           = url('nfd/nomination/apply-nomination-step-6/post');
    $data['href_file_upload'] = url('nfd/nomination/upload-affidavit');
    
    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("nfd/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nfd/nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nfd/nomination/apply-nomination-step-2"); 
    }

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    if($request->old('affidavit')){
      $data['affidavit']  = $request->old('affidavit');
   }elseif(isset($object) && ($object)){
      $data['affidavit']  = $object['affidavit']; 
    }else{
      $data['affidavit']  = '';
    }
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/apply-nomination-step-6',$data);
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
    Session::flash('success_mes',"Kindly verify the below detail and finalize the nomination.");
    if(Auth::user()->role_id == 19){
      return redirect('roac/nomination/apply-nomination-finalize');
    }
    return redirect('nfd/nomination/apply-nomination-finalize');
  }

  public function apply_nomination_finalize($id = 0, Request $request){

    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("nfd/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nfd/nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("nfd/nomination/apply-nomination-step-2"); 
    }
    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    $data = NominationApplicationModel::get_nomination($user_nomination['id']);


    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('nfd/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Nomination Detail";
    $data['action'] = url('nfd/nomination/apply-nomination-finalize/post');
    
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
    $destination_path = FileModel::get_file_path($this->upload_folder.'/qrcode/'.$year.'/ac/'.$election_name.'/'.$st_code.'/'.$ac_no).'/'.$data['id'].'.png';
    \QRCode::text(url("/nomination-status/".$data['id']))->setOutfile($destination_path)->png();
    $data['qrcode_path']  = $destination_path;
    $data['qrcode']       = url($destination_path);
    NominationApplicationModel::add_qrcode($data);
    //end QR code
    $data['user_data']  = Auth::user();
    //dd($data);
    return view('admin/nfd/nomination/apply-nomination-finalize',$data);
  }


  public function save_nomination_finalize($id = 0, Request $request){
    try{
      NominationApplicationModel::finalize_nomination($request->nomination_id);
    }
    catch(\Exception $e){
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
    Session::flash('status',1);
    Session::flash('success_mes',"Your application has been added successfully.");
    if(Auth::user()->role_id == 19){
      $nomination_res = NominationApplicationModel::get_nomination_application($request->nomination_id);
      return redirect('roac/candidateinformation?nom_id='.encrypt_string($nomination_res['nomination_no']));
    }
    return redirect('nfd/nomination/list');
  }

  

  public function view_nomination($id, Request $request){

    $id = decrypt_string($id);
    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nfd/nomination/apply-nomination-step-2");
    }
    $data['nomination_id'] = $user_nomination['id'];
    $data = NominationApplicationModel::get_nomination($user_nomination['id']);
    if($user_nomination['finalize'] == 1){
      $data['reference_id']               = $user_nomination['nomination_no'];
      $data['href_download_application']  = url("nfd/nomination/download/".encrypt_string($id));
    }

    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('nfd/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Nomination Detail";
    $data['action'] = url('nfd/nomination/apply-nomination-finalize/post');
    
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
    $data['user_data']  = Auth::user();
    return view('admin/nfd/nomination/view-nomination',$data);
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
    $common = new \App\Http\Controllers\Common\FileManager();
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
    $common = new \App\Http\Controllers\Common\FileManager();
    $results = $common->upload($request, 10240, 'pdf', $destination_path);
    return $results;
  }

  public function download_nomination($id, Request $request){

    $id = decrypt_string($id);
    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("nomination/apply-nomination-step-2");
    }
    $data['nomination_id'] = $user_nomination['id'];

    $data = NominationApplicationModel::get_nomination($user_nomination['id']);


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
    $data['affidavit']      = url($data['affidavit']);
    $data['nomination_no']  = $data['nomination_no'];

    $name_excel = time();

    $setting_pdf = [
      'margin_top'        => 80,        // Set the page margins for the new document.
      'margin_bottom'     => 10,    
    ];

    $pdf = \PDF::loadView('admin/nfd/nomination/download-nomination',$data, [], $setting_pdf);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');

  }

}