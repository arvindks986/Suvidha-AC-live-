<?php
namespace App\Http\Controllers\Admin\CandNomination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use MPDF;
use App\commonModel;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use DB, Validator, Config, \PDF, Response;
use App\models\Admin\Nomination\{ProfileModel, NominationApplicationModel, NominationProposerModel, NominationPoliceCaseModel};
use App\models\Admin\Nomination\UserModel;
use App\models\Common\{StateModel, FileModel, PcModel, AcModel, DistrictModel, PartyModel, SymbolModel, ElectionModel};
use App\Http\Requests\Admin\Nomination\{NominationPart12Request, NominationPart3Request, NominationPart3aRequest};
 
class OnlineNominationController extends Controller
{

    public $upload_folder   = 'uploads1';
    public $base            = '/roac/nomination/';
    public $folder  = 'CandNomination';
    public $action    = 'roac/';
    public $view_path = "admin.candform.nomination";
    public function __construct()
    {   
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        
        $this->xssClean = new xssClean;
        if(!Auth::check()){ 
            return redirect('/officer-login');
        }
        //echo $this->upload_folder;
        $this->middleware(function ($request, $next) {
            $this->upload_folder = config("public_config.upload_folder");
           // echo  $this->upload_folder;
            return $next($request);
        });

    }

  public function apply_nomination_step_1($id = 0,Request $request)
        {     
              
              $data = [];
              $data['heading_title'] = "Candidate Nomination Details";
              $data['breadcrumbs'] = "Candidate Nomination Details";  
              $data['action']               = url('roac/nomination/apply-nomination-step-1/post');
              $data['nomination_page']      = url('roac/nomination/apply-nomination-step-2');
              $data['href_new_nomination']  = url('roac/listallapplicant');
              $data['encrypt_id']           = '';
              $mobile=$request->mobile;
              
              if($request->nom_id!=''){
                  $nomination_no = decrypt_string($request->nom_id);
                 // echo $nomination_no;
                  $nom=NominationApplicationModel::get_nomination($nomination_no);
                 //dd($nom);
                  $candidate_id=$nom['candidate_id'];
                  $mobile=$request->mobile;
                  
                }
              else{
                $nomination_no =0;
                $candidate_id=0;
              }
              //dd($mobile);
              $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);  
          $state=$this->commonModel->getstatebystatecode($d->st_code);
          $ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
          $data['st_name']=strtoupper($state->ST_CODE."-".$state->ST_NAME);
          $data['ac_name']=strtoupper($ac->AC_NO."-".$ac->AC_NAME);

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
              $data['nomination_id']   =$nomination_no;
              $form_data      =$this->get_form($candidate_id, $request, $data);
              $data = array_merge($form_data, $data);
              Session::put('otp_mobile',$data['mobile']);
             if($data['mobile']=='') $data['mobile']=$request->mobile;    
             // dd($data);
              return view($this->view_path.'.apply-nomination-step-1',$data);

        }


  private function get_form($id,$request,$data = array()){
   //echo $id;
 // dd(Session::get('otp_mobile'));
    if($request->mobile!='' and $id==0 ){ 
        $object = ProfileModel::get_cand_id_by_mobile($request->mobile);
      }
    elseif(Session::get('otp_mobile')!=''){  
        $object = ProfileModel::get_cand_id_by_mobile(Session::get('otp_mobile'));
      }
    else {  
          $object = ProfileModel::get_cand_id_by_candidate($id);
      }
   //dd($object);
    if($request->old('name')){
      $data['name']  = $request->old('name');
    }else if(isset($object) and ($object)){
      $data['name']  = $object['name']; 
    }else{
      $data['name']  = ''; 
    }

    if($request->old('email')){
      $data['email']  = $request->old('email');
    }else if(isset($object) and ($object)){
      $data['email']  = $object['email']; 
    }else{
      $data['email']  = ''; 
    }

    if($request->old('mobile')){
      $data['mobile']  = $request->old('mobile');
    }else if(isset($object) and ($object)){
      $data['mobile']  = $object['mobile']; 
    }else{
      $data['mobile']  = ''; 
    }

    if($request->old('hname')){
      $data['hname']  = $request->old('hname');
    }else if(isset($object) and ($object)){
      $data['hname']  = $object['hname']; 
    }else{
      $data['hname']  = ''; 
    }

    if($request->old('vname')){
      $data['vname']  = $request->old('vname');
    }else if(isset($object) and ($object)){
      $data['vname']  = $object['vname']; 
    }else{
      $data['vname']  = ''; 
    }

    if($request->old('alias_name')){
      $data['alias_name']  = $request->old('alias_name');
    }else if(isset($object) and ($object)){
      $data['alias_name']  = $object['alias_name']; 
    }else{
      $data['alias_name']  = ''; 
    }

    if($request->old('alias_hname')){
      $data['alias_hname']  = $request->old('alias_hname');
    }else if(isset($object) and ($object)){
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
    }else if(isset($object) and ($object)){
      $data['father_name']  = $object['father_name']; 
    }else{
      $data['father_name']  = ''; 
    }

    if($request->old('father_hname')){
      $data['father_hname']  = $request->old('father_hname');
    }else if(isset($object) and ($object)){
      $data['father_hname']  = $object['father_hname']; 
    }else{
      $data['father_hname']  = ''; 
    }

    if($request->old('father_vname')){
      $data['father_vname']  = $request->old('father_vname');
    }else if(isset($object) and ($object)){
      $data['father_vname']  = $object['father_vname']; 
    }else{
      $data['father_vname']  = ''; 
    }

    if($request->old('pan_number')){
      $data['pan_number']  = $request->old('pan_number');
    }else if(isset($object) and ($object)){
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
    }else if(isset($object) and ($object)){
      $data['category']  = $object['category']; 
    }else{
      $data['category']  = ''; 
    }

    if($request->old('age')){
      $data['age']  = $request->old('age');
    }else if(isset($object) and ($object)){
      $data['age']  = $object['age']; 
    }else{
      $data['age']  = ''; 
    }

    if($request->old('gender')){
      $data['gender']  = $request->old('gender');
    }else if(isset($object) and ($object)){
      $data['gender']  = $object['gender']; 
    }else{
      $data['gender']  = ''; 
    }

    if($request->old('address')){
      $data['address']  = $request->old('address');
    }else if(isset($object) and ($object)){
      $data['address']  = $object['address']; 
    }else{
      $data['address']  = ''; 
    }

    if($request->old('haddress')){
      $data['haddress']  = $request->old('haddress');
    }else if(isset($object) and ($object)){
      $data['haddress']  = $object['haddress']; 
    }else{
      $data['haddress']  = ''; 
    }

    if($request->old('vaddress')){
      $data['vaddress']  = $request->old('vaddress');
    }else if(isset($object) and ($object)){
      $data['vaddress']  = $object['vaddress']; 
    }else{
      $data['vaddress']  = ''; 
    }

    // if($request->old('address_2')){
    //   $data['address_2']  = $request->old('address_2');
    // }else if(isset($object) and ($object)){
    //   $data['address_2']  = $object['address_2']; 
    // }else{
    //   $data['address_2']  = ''; 
    // }

    // if($request->old('address_2_hindi')){
    //   $data['address_2_hindi']  = $request->old('address_2_hindi');
    // }else if(isset($object) and ($object)){
    //   $data['address_2_hindi']  = $object['address_2_hindi']; 
    // }else{
    //   $data['address_2_hindi']  = ''; 
    // }

    if($request->old('district')){
      $data['district']  = $request->old('district');
    }else if(isset($object) and ($object)){
      $data['district']  = $object['district']; 
    }else{
      $data['district']  = ''; 
    }

    if($request->old('state')){
      $data['state']  = $request->old('state');
    }else if(isset($object) and ($object)){
      $data['state']  = $object['state']; 
    }else{
      $data['state']  = ''; 
    }

    if($request->old('ac')){
      $data['ac']  = $request->old('ac');
    }else if(isset($object) and ($object)){
      $data['ac']  = $object['ac']; 
    }else{
      $data['ac']  = ''; 
    }

    if($request->old('epic_no')){
      $data['epic_no']  = $request->old('epic_no');
    }else if(isset($object) and ($object)){
      $data['epic_no']  = $object['epic_no']; 
    }else{
      $data['epic_no']  = ''; 
    }

    if($request->old('serial_no')){
      $data['serial_no']  = $request->old('serial_no');
    }else if(isset($object) and ($object)){
      $data['serial_no']  = $object['serial_no']; 
    }else{
      $data['serial_no']  = ''; 
    }

    if($request->old('part_no')){
      $data['part_no']  = $request->old('part_no');
    }else if(isset($object) and ($object)){
      $data['part_no']  = $object['part_no']; 
    }else{
      $data['part_no']  = ''; 
    }
    
    if($request->old('dob')){
      $data['dob']  = $request->old('dob');
    }else if(isset($object) and ($object)){
      $data['dob']  = $object['dob']; 
    }else{
      $data['dob']  = ''; 
    }
    return $data;
  }

  public function save_step_1(Request $request){
      // dd($request->input());
        $rules = [
            'name'              => 'required|min:3|max:255',
            'hname'             => 'required|min:3|max:255',
            'vname'             => 'required|min:3|max:255',
           // 'alias_name'      => 'required|min:3|max:255',
           // 'alias_hname'     => 'required|min:3|max:255',
            'father_name'       => 'required|min:3|max:255',
            'father_hname'      => 'required|min:3|max:255',
            'father_vname'      => 'required|min:3|max:255',
            'category'          => 'required|in:sc,st,general',
            'pan_number'        => 'required|size:10|pan',
            'age'               => 'required|integer|age',
            'address'           => 'required|min:3|max:255',
            'haddress'          => 'required|min:3|max:255',
            'vaddress'          => 'required|min:3|max:255',
            'part_no'           => 'required|integer|min:1',
            'serial_no'         => 'required|integer|min:1',
            'state'             => 'required|exists:m_state,ST_CODE',
            'district'          => 'required|exists:m_district,DIST_NO',
            'ac'                => 'required|exists:m_ac,AC_NO',
            'gender'            => 'required|in:male,female,third'
        ];

        $object = ProfileModel::get_cand_id_by_mobile($request->mobile);
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
    if ($validator->fails()) { 
                return redirect::back()
                ->withErrors($validator)
                ->withInput();
            }
            //dd($request->all());
//dd($request->mobile);
      if(Session::has('otp_mobile') and Session::get('otp_mobile')!=''){   
            $user = UserModel::add_user(['mobile' => Session::get('otp_mobile')]);
          }
        else{
              $user = UserModel::add_user(['mobile' => $request->mobile]); 
              Session::put('otp_mobile',$request->mobile);
        }
      if($request->nomination_id=='' || $request->nomination_id=='0')
            $request->merge(['candidate_id' => $user->id]);
      else
         $request->merge(['candidate_id' =>'0']);

    DB::beginTransaction();
        try{
                ProfileModel::add_nomination_personal_detail($request->all());
           }

      catch(\Exception $e){
         DB::rollback();

         \Session::flash('error_mes', 'Please try Again');
         return Redirect::back();
      }
     DB::commit();  
   
      Session::flash('status',1);
      Session::flash('flash-message',"Personal details has been updated successfully.");
      Session::flash('success_mes',"Personal details has been updated successfully.");

    if($request->has('save_only')){
      return Redirect::back();
    }
    if($request->nomination_id==0)
        return redirect($this->base.'apply-nomination-step-2');
    else
      return redirect($this->base.'apply-nomination-step-2/'.encrypt_string($request->nomination_id));

  }


  public function apply_nomination_step_2($id = 0, Request $request) {   // dd($request);
     
    Session::forget('nomination_id');

    $data                   = [];
    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "Nomination",
      'is_last' => true
    ];
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);  
          $state=$this->commonModel->getstatebystatecode($d->st_code);
          $ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
          $data['st_name']=strtoupper($state->ST_CODE."-".$state->ST_NAME);
          $data['ac_name']=strtoupper($ac->AC_NO."-".$ac->AC_NAME);

          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          $data['ele_details']=$ele_details;
          $data['user_data']=$d;
          $st=getstatebystatecode($d->st_code);
          $ac=getacbyacno($d->st_code,$d->ac_no);
      $data['election_id']=$ele_details->ELECTION_ID;
      $data['election_type_id']=$ele_details->ELECTION_TYPEID;
      $data['st_code']=$ele_details->ST_CODE;
      $data['name']=$ele_details->CONST_TYPE."-".$ele_details->ELECTION_TYPE."-".$ele_details->YEAR;
      
      $data['st']=$st;
      $data['ac']=$ac;
      //echo $id;
   // dd($data);
    $data['is_active']     = 'nomination';  
    $data['href_back'] = url('roac/nomination/apply-nomination-step-1');
    $data['href_skip'] = url('roac/nomination/apply-nomination-step-3');
    $data['heading_title'] = "Select Election Detail";
    $data['action']        = url('roac/nomination/apply-nomination-step-2/post');
     $data = $this->get_step2_form($id, $request, $data);
    if($id!=0 || $id!=''){
        $id = decrypt_String($id);  
        //dd($id);
        $nom=NominationApplicationModel::get_nomination($id);
        //dd($nom);
        $data['candidate_id']=$nom['candidate_id'];
      }
     //dd($data);
    return view($this->view_path.'.apply-nomination-step-2',$data);

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
    }else if(isset($object) and ($object)){
      $data['st_code']  = $object['st_code']; 
    }else{
      $data['st_code']  = ''; 
    }

    if($request->old('election_id')){
      $data['election_id']  = $request->old('election_id');
    }else if(isset($object) and ($object)){
      $data['election_id']  = $object['election_id']; 
    }else{
      $data['election_id']  = ''; 
    }

    if($request->old('ac_no')){
      $data['ac_no']  = $request->old('ac_no');
    }else if(isset($object) and ($object)){
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
    if ($validator->fails()) { 
                return redirect::back()
                ->withErrors($validator)
                ->withInput();
            }
   
      $request->merge(['nomination_type' =>'0']);
      $request->merge(['application_type' =>'2']);
      
    if(!$request->has('nomination_id')){
     $count_nomination = NominationApplicationModel::count_nomination_application($request->all());
          if($count_nomination >= 4){
           Session::flash('status',1);
           Session::flash('success_mes',"You already filled 4 application on this AC..");
             
          } 
         $user = UserModel::add_user([
                'mobile' => Session::get('otp_mobile')
              ]);
                  $request->merge(['candidate_id' => $user->id]); 
      } 

    DB::beginTransaction();
    try{ 
               
       $result   = NominationApplicationModel::add_nomination_application($request->all());
      // //QR code
      // if(!$request->has('nomination_id')){
      //   $data             = NominationApplicationModel::get_nomination(Session::get('nomination_id'));
      //   $st_code          = $data['st_code'];
      //   $year             = date('Y');
      //   $ac_no            = $data['ac_no'];
      //   $election_name    = 'E'.$data['election_id'];
      //   $destination_path = FileModel::get_file_path('uploads/qrcode/'.$year.'/ac/'.$election_name.'/'.$st_code.'/'.$ac_no).'/'.$data['id'].'.png';
      //   \QRCode::text($data['nomination_no'])->setOutfile($destination_path)->png();
      //   $data['qrcode_path']  = $destination_path;
      //   $data['qrcode']       = url($destination_path);
      //   NominationApplicationModel::add_qrcode($data);
      // }
      //end QR code
   
   }
    catch(\Exception $e){
         DB::rollback();

         \Session::flash('error_mes', 'Please try Again');
         return Redirect::back();
      }
     DB::commit();  
   
      Session::flash('status',1);
      Session::flash('success_mes',"Election Details has been updated successfully.");
     
    if($request->has('save_only')){
      return redirect("roac/nomination/apply-nomination-step-2/".encrypt_string(Session::get('nomination_id')));
    }
    return redirect('/roac/nomination/apply-nomination-step-3');

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
              'href'    => url(''),
              'name'    => "Nomination",
              'is_last' => true
            ];
            $data['is_active']     = 'nomination';
            $data['heading_title'] = "Form 2B - Nomination Paper ";
            $data['action'] = url('roac/nomination/apply-nomination-step-3/post-part-1');
            $data['href_file_upload'] = url('roac/nomination/upload');
            $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);  
          $state=$this->commonModel->getstatebystatecode($d->st_code);
          $ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
          $data['st_name']=strtoupper($state->ST_CODE."-".$state->ST_NAME);
          $data['ac_name']=strtoupper($ac->AC_NO."-".$ac->AC_NAME);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          $data['ele_details']=$ele_details;
          $data['user_data']=$d;
    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("roac/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);

    if(!$user_nomination){
       return redirect("roac/nomination/apply-nomination-step-2");
    }
    // if($user_nomination['finalize'] == 1){
    //   Session::flash('flash-message','You can not edit this nomination.');
    //   return redirect("roac/nomination/list"); 
    // }

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation

    $data = array_merge($data, $user_nomination);
    //dd($data);
    $data = $this->get_step3_form($id, $request, $data);
    $data['user_data']  = Auth::user();
   // dd($data);
    return view($this->view_path.'.apply-nomination-step-3',$data);
 
  }

 public function get_step3_form($id,$request,$data = array()){
    //echo $id;
    //dd($data);
    //dd($data['mobile']);
    $users = ProfileModel::get_cand_id_by_mobile($data['mobile']);
    //$users = NominationApplicationModel::get_nomination_application($id);
    // dd($users);
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
    }else if(isset($object)){
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
   // echo "hello";
    //dd($data);
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
          'candidate_id'    =>$data['candidate_id'],
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
    }else if(isset($object)){
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
          $a1=array('st_code'=>$request->st_code,'ac_no'=>$request->ac_no,
              'election_id' =>$request->election_id);
          $non_recognized_proposer=array_merge($non_recognized_proposer,$a1);
          NominationProposerModel::add_proposerroac($non_recognized_proposer);
        }
      }
    }
    catch(\Exception $e){
         DB::rollback();

         \Session::flash('error_mes', 'Please try Again');
         return Redirect::back();
      }
     DB::commit();  
   
      Session::flash('status',1);
      Session::flash('success_mes',"Nomination added successfully.");
    //   if($request->has('save_only')){
    //   return Redirect::back();
    // }
     
    if(Auth::user()->role_id == 19){
      return redirect('roac/nomination/apply-nomination-step-4');
    }
 

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
    $data['action'] = url('roac/nomination/apply-nomination-step-4/post');
    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("roac/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("roac/nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("roac/nomination/apply-nomination-step-2"); 
    }

    $data['nomination_id'] = $user_nomination['id'];
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);  
          $state=$this->commonModel->getstatebystatecode($d->st_code);
          $ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
          $data['st_name']=strtoupper($state->ST_CODE."-".$state->ST_NAME);
          $data['ac_name']=strtoupper($ac->AC_NO."-".$ac->AC_NAME);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          $data['ele_details']=$ele_details;
          $data['user_data']=$d;
    //end nomination validation

    $data['recognized_party'] = $user_nomination['recognized_party'];

    $data = array_merge($data, $user_nomination);
    $data = $this->get_step4_form($id, $request, $data);
    $data['user_data']  = Auth::user();
    //dd($data);
    return view($this->view_path.'.apply-nomination-step-4',$data);
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
     DB::beginTransaction();
    try{  
      NominationApplicationModel::add_nomination_part3roac($request->all());
    }
    catch(\Exception $e){
         DB::rollback();

         \Session::flash('error_mes', 'Please try Again');
         return Redirect::back();
      }
     DB::commit();  
   
      Session::flash('status',1);
      Session::flash('success_mes',"Nomination added successfully.");
      if($request->has('save_only')){
      return Redirect::back();
    }
     
    //Session::flash('flash-message',"Nomination added successfully.");
       return redirect('roac/nomination/apply-nomination-step-5');
    
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
      'href'    => url('roac/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Form 2B - Nomination Paper";
    $data['action'] = url('roac/nomination/apply-nomination-step-5/post');

    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("roac/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("roac/nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("roac/nomination/apply-nomination-step-2"); 
    }

    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation
    $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);  
          $state=$this->commonModel->getstatebystatecode($d->st_code);
          $ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
          $data['st_name']=strtoupper($state->ST_CODE."-".$state->ST_NAME);
          $data['ac_name']=strtoupper($ac->AC_NO."-".$ac->AC_NAME);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          $data['ele_details']=$ele_details;
          $data['user_data']=$d;

    $data['recognized_party'] = $user_nomination['recognized_party'];

    $data = array_merge($data, $user_nomination);
    $data = $this->get_step5_form($id, $request, $data);
    $data['user_data']  = Auth::user();
   //dd($data);
    return view($this->view_path.'.apply-nomination-step-5',$data);
  }


  public function get_step5_form($id, $request, $data = []){
   // echo $id;
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
    }else if(isset($object)){
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
    //dd($request->all());
     DB::beginTransaction();
    try{
      NominationApplicationModel::add_nomination_part3aroac($request->all());
        // NominationPoliceCaseModel::delete_police_case($request->nomination_id);
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

         \Session::flash('error_mes', 'Please try Again');
         return Redirect::back();
      }
     DB::commit();  
   //dd("hello");
      Session::flash('status',1);
      Session::flash('success_mes',"Nomination added successfully.");
    //Session::flash('flash-message',"Nomination added successfully.");
     // $nomination_res = NominationApplicationModel::get_nomination_application($request->nomination_id);
     // return redirect('roac/candidateinformation?nom_id='.encrypt_string($nomination_res['nomination_no']));
     return redirect('roac/nomination/apply-nomination-step-6');
    
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
      'href'    => url('roac/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']        = 'nomination';
    $data['heading_title']    = "Upload Affidavit";
    $data['action']           = url('roac/nomination/apply-nomination-step-6/post');
    $data['href_file_upload'] = url('roac/nomination/upload-affidavit');
    
    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("roac/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("roac/nomination/apply-nomination-step-2");
    }
    // if($user_nomination['finalize'] == 1){
    //   Session::flash('flash-message','You can not edit this nomination.');
    //   return redirect("roac/nomination/apply-nomination-step-2"); 
    // }
     
    $data['nomination_id'] = $user_nomination['id'];
    //end nomination validation
          $user = Auth::user();
          $d=$this->commonModel->getunewserbyuserid($user->id);  
          $state=$this->commonModel->getstatebystatecode($d->st_code);
          $ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
          $data['st_name']=strtoupper($state->ST_CODE."-".$state->ST_NAME);
          $data['ac_name']=strtoupper($ac->AC_NO."-".$ac->AC_NAME);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          $data['ele_details']=$ele_details;
          $data['user_data']=$d;

    if($request->old('affidavit')){
      $data['affidavit']  = $request->old('affidavit');
    }else if(isset($object)){
      $data['affidavit']  = $object['affidavit']; 
    }else{
      $data['affidavit']  = '';
    }
    $data['user_data']  = Auth::user();
    return view($this->view_path.'.apply-nomination-step-6',$data);
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
    if(Auth::user()->role_id == 19){
      return redirect('roac/nomination/apply-nomination-finalize');
    }
    return redirect('nfd/nomination/apply-nomination-finalize');
  }

  public function apply_nomination_finalize($id = 0, Request $request){
     //dd("hello");
    //nomination validation id
    if($id == 0){
      if(!Session::has('nomination_id')){
        Session::flash('flash-message','please apply again.');
        return redirect("roac/nomination/apply-nomination-step-2"); 
      }
      $id = Session::get('nomination_id');
    }

    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("roac/nomination/apply-nomination-step-2");
    }
    if($user_nomination['finalize'] == 1){
      Session::flash('flash-message','You can not edit this nomination.');
      return redirect("roac/nomination/apply-nomination-step-2"); 
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
    $data['action'] = url('roac/nomination/apply-nomination-finalize/post');
    
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
    $data['qr_code']       = url($destination_path);
    NominationApplicationModel::add_qrcode($data);
    //end QR code
    $data['user_data']  = Auth::user();
   //dd($data);
    return view($this->view_path.'.apply-nomination-finalize',$data);
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
    Session::flash('flash-message',"Your application has been added successfully.");
    if(Auth::user()->role_id == 19){
      $nomination_res = NominationApplicationModel::get_nomination_application($request->nomination_id);
      return redirect('roac/candidateinformation?nom_id='.encrypt_string($nomination_res['nomination_no']));
    }
    //return redirect('nfd/nomination/list');
  }

  

  public function view_nomination($id, Request $request){

    $id = decrypt_string($id);
    $user_nomination = NominationApplicationModel::get_nomination_application($id);
    if(!$user_nomination){
       return redirect("roac/nomination/apply-nomination-step-2");
    }
    $data['nomination_id'] = $user_nomination['id'];
    $data = NominationApplicationModel::get_nomination($user_nomination['id']);
    if($user_nomination['finalize'] == 1){
      $data['reference_id']               = $user_nomination['nomination_no'];
      $data['href_download_application']  = url("roac/nomination/download/".encrypt_string($id));
    }

    $data['breadcrumbs']    = [];
    $data['breadcrumbs'][]  = [
      'href'    => url('/'),
      'name'    => "<span class='icon icon-home'> </span>",
      'is_last' => false
    ];
    $data['breadcrumbs'][]  = [
      'href'    => url('roac/nomination'),
      'name'    => "Nomination",
      'is_last' => true
    ];
    $data['is_active']     = 'nomination';
    $data['heading_title'] = "Nomination Detail";
    $data['action'] = url('roac/nomination/apply-nomination-finalize/post');
    
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
    return view($this->view_path.'.view-nomination',$data);
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

    $pdf = \PDF::loadView('nomination/download-nomination',$data, [], $setting_pdf);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');

  }

}