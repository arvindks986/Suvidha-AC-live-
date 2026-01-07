<?php namespace App\Http\Controllers\Candidate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use DB, Validator, Config, Session, Common, Response;
use \PDF;
use App\Http\Controllers\Candidate\CommonController;
use App\Helpers\SmsgatewayHelper;
use App\models\Candidate\AbsentVoterModel;
use App\models\Common\{AcModel, DistrictModel, StateModel};
use App\Classes\xssClean;


class AbsentVoterController extends Controller {

  public function __construct(){  
    $this->xssClean = new xssClean;
  }

  public function absent_voter_form_12d(Request $request){

    if(!Session::has('otp_verify')){
      return Redirect::to("absentee-voters/get-otp-form");
    }

    $data                   = [];
    $data['heading_title']  = "Form 12D";

    $data['header'] = CommonController::get_header($request);
    $data['footer'] = CommonController::get_footer($request);
    $data['bredcrumbs'] = [];
    $data['bredcrumbs'][] = [
      'name'  => 'Absentee Voter Form',
      'href'  => url('absentee-voters/absentee-voter-form-12d')
    ];
    $data['load_by_epic']           = url('absentee-voters/load-by-epic');
    $data['post_12d_form']          = url('absentee-voters/post-absentee-voter-form-12d');
    $data['generate_passkey_url']   = url('absentee-voters/generate-passkey-url');
    $data['start_session']          = url('absentee-voters/start_session');
    $data['action']       = '';
    $data['name']         = '';
    $data['mobile']       = '';
    $data['epic_no']      = '';
    $data['father_name']  = '';
    $data['gender']       = '';
    $data['age']          = '';
    $data['address']      = '';
    $data['new_address']  = '';
    $data['ps_no']        = '';
    $data['ps_name']      = '';
    $data['state']        = '';
    $data['dist_no']      = '';
    $data['ac_no']        = '';
    $data['states']       = [];
    $data['same_address'] = 1;
    $data['district']     = '';
    $data['ac']           = [];

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



    return view('candidate.absent-voter.absent_voter_form', $data);


  }

  public function post_absentee_voter(Request $request){

    if(!Session::has('mobile')){
      return Response::json([
        'success' => true,
        'redirect_to' => url("absentee-voters/get-otp-form")
      ]);
    }

    $rules = [
      'address'     => 'required|max:255',
      'father_name' => 'required|max:255',
      'house_no'    => 'required|max:255',
      'village'     => 'required|max:255',
      'tehsil'      => 'required|max:255',
      'pincode'       => 'required|numeric',
      'same_address'  => 'required|in:1,0',
      'address'       => 'required',
      'is_pwd'        => 'required|in:1,0',
      'age'           => 'required|numeric',
      'part_no'       => 'required|numeric',
      'new_address'   => 'required_if:same_address,0|max:255',
      'new_house_no'  => 'required_if:same_address,0|max:255',
      'new_village'   => 'required_if:same_address,0|max:255',
      'new_tehsil'    => 'required_if:same_address,0|max:255',
      'new_pincode'   => 'required_if:same_address,0',
      'new_st_code'   => 'required_if:same_address,0|max:255',
      'new_dist_no'   => 'required_if:same_address,0|max:255',
      'st_code'       => 'required',
      'ac_no'         => 'required',
      'epic_no'       => 'required',
    ];
    $messages = [
      'mobile' => 'please enter a valid mobile number',
      'required_if' => "The field is required when postal address is not same."
    ];

    $validator = Validator::make($request->all(),$rules,$messages);
    if ($validator->fails())
    {
      return Response::json([
        'success' => false,
        'errors' => $validator->getMessageBag()->toArray()
      ]);
    }

    $detail_by_epic = Session::get('detail_by_epic');

    if(isset($detail_by_epic['basic']['name'])){
      $name = $detail_by_epic['basic']['name'];
    }else{
      $name = $request->name;
    }

    if(isset($detail_by_epic['basic']['rln_name'])){
      $father_name = $detail_by_epic['basic']['rln_name'];
    }else{
      $father_name = $request->father_name;
    }

    if(isset($detail_by_epic['basic']['house_no'])){
      $house_no = $detail_by_epic['basic']['house_no'];
    }else{
      $house_no = $request->house_no;
    }

    if(isset($detail_by_epic['address']['Address'])){
      $address = $detail_by_epic['address']['Address'];
    }else{
      $address = $request->address;
    }

    if(isset($detail_by_epic['basic']['age'])){
      $age = $detail_by_epic['basic']['age'];
    }else{
      $age = $request->age;
    }

    if(isset($detail_by_epic['address']['MOBILE_NO'])){
      $mobile = $detail_by_epic['address']['MOBILE_NO'];
    }else{
      $mobile = $request->mobile;
    }

    if(isset($detail_by_epic['basic']['ps_no'])){
      $part_no = $detail_by_epic['basic']['ps_no'];
    }else{
      $part_no = $request->part_no;
    }

    if(isset($detail_by_epic['basic']['dist_no'])){
      $dist_no = $detail_by_epic['basic']['dist_no'];
    }else{
      $dist_no = $request->dist_no;
    }

    if(isset($detail_by_epic['basic']['slno_inpart'])){
      $slno_inpart = $detail_by_epic['basic']['slno_inpart'];
    }else{
      $slno_inpart = $request->serial_number;
    }

    if(isset($detail_by_epic['address']['C_PIN_CODE'])){
      $pincode = $detail_by_epic['address']['C_PIN_CODE'];
    }else{
      $pincode = $request->pincode;
    }

    if(isset($detail_by_epic['address']['C_VILLAGE'])){
      $village = $detail_by_epic['address']['C_VILLAGE'];
    }else{
      $village = $request->village;
    }

    if(isset($detail_by_epic['address']['C_STREET_AREA'])){
      $tehsil = $detail_by_epic['address']['C_STREET_AREA'];
    }else{
      $tehsil = $request->tehsil;
    }

    if(isset($detail_by_epic['basic']['st_code'])){
      $st_code = $detail_by_epic['basic']['st_code'];
    }else{
      $st_code = $request->st_code;
    }

    if(isset($detail_by_epic['basic']['ac_no'])){
      $ac_no = $detail_by_epic['basic']['ac_no'];
    }else{
      $ac_no = $request->ac_no;
    }

    if(isset($detail_by_epic['basic']['epic_no'])){
      $epic_no = $detail_by_epic['basic']['epic_no'];
    }else{
      $epic_no = $request->epic_no;
    }

    if(isset($detail_by_epic['basic']['is_pwd'])){
      $is_pwd = $detail_by_epic['basic']['is_pwd'];
    }else{
      $is_pwd = $request->is_pwd;
    }

    if($age < 80 && $is_pwd == 0){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "As per electoral roll details, you are not eligible for to fill FORM-12D."]
      ]);
    }

    $data = [
      'auth_mobile' => Session::get('mobile'),
      'epic_no'     => $this->xssClean->strip_tags($epic_no),
      'name'        => $this->xssClean->strip_tags($name),
      'father_name' => $this->xssClean->strip_tags($father_name),
      'address'     => $this->xssClean->strip_tags($address),
      'house_no'    => $this->xssClean->strip_tags($house_no),
      'age'         => $this->xssClean->clean_input($age),
      'mobile'      => $this->xssClean->clean_input($mobile),
      'st_code'     => $this->xssClean->clean_input($st_code),
      'ac_no'       => $this->xssClean->clean_input($ac_no),
      'ps_no'       => $this->xssClean->clean_input($part_no),
      'dist_no'     => $this->xssClean->clean_input($dist_no),
      'pincode'     => $this->xssClean->clean_input($pincode),
      'village'     => $this->xssClean->strip_tags($village),
      'tehsil'        => $this->xssClean->strip_tags($tehsil),
      'serial_number' => $this->xssClean->clean_input($slno_inpart),
      'is_pwd'        => $this->xssClean->clean_input($is_pwd),
      'same_address'  => $this->xssClean->clean_input($request->same_address),
      'new_address'   => $this->xssClean->strip_tags($request->new_address),
      'new_house_no'    => $this->xssClean->strip_tags($request->new_house_no),
      'new_village'   => $this->xssClean->strip_tags($request->new_village),
      'new_tehsil'    => $this->xssClean->strip_tags($request->new_tehsil),
      'new_pincode'   => $this->xssClean->strip_tags($request->new_pincode),
      'new_st_code'   => $this->xssClean->strip_tags($request->new_st_code),
      'new_dist_no'   => $this->xssClean->strip_tags($request->new_dist_no),
    ];

    $is_valid_absentee = AbsentVoterModel::unique_with_mobile($data);
    if($is_valid_absentee){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "The given mobile number is already fill a Form-12D. Please try with different mobile number."]
      ]);
    }
    
    $is_valid_absentee = AbsentVoterModel::unique_with_epic($data);
    if($is_valid_absentee){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "You already fill the Form-12D."]
      ]);
    }

    $tracking_id = AbsentVoterModel::update_absentee_voter_details($data);
    if(!$tracking_id){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "Please try again."]
      ]);
    }
    
    return Response::json([
      'success' => true,
      'redirect_to' => url("absentee-voters/absentee-voter-pdf/".$tracking_id)
    ]);
  }

  public function absentee_voter_pdf($id, Request $request){
    $data                   = [];
    $data['heading_title']  = "Request Submitted";
    $data['header'] = CommonController::get_header($request);
    $data['footer'] = CommonController::get_footer($request);
    $data['bredcrumbs'] = [];
    $get_absentee     = AbsentVoterModel::get_absentee_voter($id);
    if(!$get_absentee){
      return redirect("absentee-voters/get-otp-form");
    }
    $data['name']         = $get_absentee['name'];
    $data['mobile']       = $get_absentee['mobile'];
    $data['father_name']  = $get_absentee['father_name'];
    $data['age']          = $get_absentee['age'];
    $data['address']      = $get_absentee['address'];
    $data['part_no']      = $get_absentee['ps_no'];

    $data['resident']       = $get_absentee['village'];
    $data['village']        = $get_absentee['village'];
    $data['house_number']   = $get_absentee['village'];
    $data['mohalla']        = $get_absentee['tehsil'];
    $data['town']           = $get_absentee['tehsil'];
    $data['tehsil']         = $get_absentee['tehsil'];



    $data['serial_number']  = $get_absentee['serial_number'];
    $data['pincode']        = $get_absentee['pincode'];
    $data['new_pincode']    = $get_absentee['pincode'];
    $data['new_address']    = $get_absentee['address'];
    $data['new_village']    = $get_absentee['village'];
    $data['new_mohalla']    = $get_absentee['village'];
    $data['new_tehsil']     = $get_absentee['tehsil'];
    $data['new_st_code']    = $get_absentee['st_code'];
    $data['new_dist_no']    = $get_absentee['dist_no'];
    $data['new_house_no']   = $get_absentee['house_no'];

    if($get_absentee['same_address'] == 0){
      $data['new_pincode']  = $get_absentee['new_pincode'];
      $data['new_address']  = $get_absentee['new_address'];
      $data['new_village']  = $get_absentee['new_village'];
      $data['new_mohalla']  = $get_absentee['new_village'];
      $data['new_tehsil']   = $get_absentee['new_tehsil'];
      $data['new_st_code']  = $get_absentee['new_st_code'];
      $data['new_dist_no']  = $get_absentee['new_dist_no'];
      $data['new_house_no']    = $get_absentee['new_house_no'];
    }

    $data['dist_name']     = '';
    $dist_object = DistrictModel::get_district([
      'st_code' => $get_absentee['st_code'],
      'dist_no' => $get_absentee['dist_no']
    ]);

    if($dist_object){
      $data['dist_name'] = $dist_object['district_name'];
    }

    $data['new_dist_name']     = '';
    $dist_object = DistrictModel::get_district([
      'st_code' => $data['new_st_code'],
      'dist_no' => $data['new_dist_no']
    ]);
    if($dist_object){
      $data['new_dist_name'] = $dist_object['district_name'];
    }

    $data['st_name']     = '';
    $state_object = StateModel::get_state($get_absentee['st_code']);
    if($state_object){
      $data['st_name'] = $state_object->ST_NAME;
    }

    $data['new_st_name']     = '';
    $state_object = StateModel::get_state($data['new_st_code']);
    if($state_object){
      $data['new_st_name'] = $state_object->ST_NAME;
    }

    $data['ac_name']     = '';
    $ac_object = AcModel::get_record([
      'state' => $get_absentee['st_code'],
      'ac_no' => $get_absentee['ac_no']
    ]);
    if($ac_object){
      $data['ac_name'] = $ac_object['ac_name'];
    }

    $data['post_12d_submit'] = url('absentee-voters/submit-12d/'.$id);

    return view('candidate.absent-voter.absent_voter_form_preview', $data);
  }

  public function submit_12d($id, Request $request){
    $data                   = [];
    $data['heading_title']  = "Request Submitted";
    $data['header'] = CommonController::get_header($request);
    $data['footer'] = CommonController::get_footer($request);
    $data['bredcrumbs'] = [];
    $data['message']  = "Your request has been submitted successfully. Your reference id is ".$id;
    $get_absentee     = AbsentVoterModel::update_status($id);
    if(!$get_absentee){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "Please try again."]
      ]);
    }
    Session::forget("otp_verify");
    return Response::json([
      'success' => true,
      'redirect_to' => url("absentee-voters/success-12d/".$id)
    ]);
  }

  public function success_12d($id, Request $request){
    $data                   = [];
    $data['heading_title']  = "Request Submitted";
    $data['header'] = CommonController::get_header($request);
    $data['footer'] = CommonController::get_footer($request);
    $data['bredcrumbs'] = [];
    $data['message']  = "Your request has been submitted successfully. Your reference id is ".$id;
    return view('candidate.absent-voter.absent_voter_pdf', $data);
  }



  public function send_otp_form(Request $request){

    $data                   = [];
    $data['heading_title']  = "Enter the mobile number.";
    $data['header'] = CommonController::get_header($request);
    $data['footer'] = CommonController::get_footer($request);
    $data['bredcrumbs'] = [];
    $data['bredcrumbs'][] = [
      'name'  => 'Verify Mobile',
      'href'  => url('absentee-voters/get-otp-form')
    ];
    $data['mobile'] = '';
    $data['otp']    = '';
    $data['action'] = url('absentee-voters/send-otp');
    $data['action_verify_otp'] = url('absentee-voters/verify_otp');
    return view('candidate.absent-voter.send_otp_form', $data);
  }

  public function send_otp(Request $request){
    $data = array();    
    $validator = Validator::make($request->all(),['mobile' => 'required|mobile'],['mobile'=>'please enter a valid mobile number']);
    if ($validator->fails())
    {
      return Response::json([
        'success' => false,
        'errors' => $validator->getMessageBag()->toArray()
      ]);
    }
    $otp = rand(111111,999999);
    $data = [
      'mobile' => $this->xssClean->clean_input($request->mobile),
      'otp'    => $otp
    ];


    $check_otp_time = AbsentVoterModel::check_otp_time($data);
    if($check_otp_time && $check_otp_time <= 60){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "You can only request for otp once in a minute."]
      ]);
    }

    AbsentVoterModel::add_absent_voter($data);
    try{
      $message = "Dear Sir/Madam, your OTP is ".$data['otp']." for Form - 12D. Please enter the OTP to proceed. Do not share this OTP Team ECI";
      $response = SmsgatewayHelper::gupshup($data['mobile'],$message);
      Session::put('mobile', $request->mobile);
    }catch(\Exception $e){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "Please try again"]
      ]);
    }

    return Response::json([
      'success' => true,
    ]);
  }

  public function verify_otp(Request $request){
    $data = array();    
    $validator = Validator::make($request->all(),['mobile' => 'required|mobile','otp' => 'required|alpha_num'],['mobile'=>'please enter a valid mobile number']);
    if ($validator->fails())
    {
      return Response::json([
        'success' => false,
        'errors' => $validator->getMessageBag()->toArray()
      ]);
    }
    $data = [
      'mobile' => $this->xssClean->clean_input($request->mobile),
      'otp'    => $this->xssClean->clean_input($request->otp)
    ];
    $is_verify = AbsentVoterModel::verify_otp($data);
    if(!$is_verify){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "Please enter a valid otp"]
      ]);
    }
    Session::put("otp_verify", strtotime(date('Y-m-d H:i:s')));
    return Response::json([
      'success' => true,
      'redirect_to' => url("absentee-voters/absentee-voter-form-12d")
    ]);
  }


  public function load_detail_by_epic(Request $request){
    $data = array();    
    $validator = Validator::make($request->all(),['epic_no' => 'required|alpha_num|min:10']);
    if ($validator->fails())
    {
      return Response::json([
        'success' => false,
        'message' => $validator->getMessageBag()->first()
      ]);

    }
    $epic_no  = $request->epic_no;
    $pass_key = $this->get_pass_key($epic_no);
    $url      = "https://electoralsearch.in/VoterSearch/SASSearch?epic_no=".$epic_no."&search_type=epic&pass_key=".$pass_key;

    $elector_information = $this->get_cdac_file(['url' => $url]);

    if(isset($elector_information) && $elector_information->response->numFound){
      $data = (array)$elector_information->response->docs[0];

      $pass_key = $this->get_pass_key($data['st_code'].$epic_no);
      $addres_url = "https://evp.ecinet.in/mservices/api/EVP/GetEVPElectorDetails?EPIC_NO=".$epic_no."&Pass_key=".$pass_key."&st_code=".$data['st_code']."&ac_no=".$data['ac_no'];

      $address_information = $this->get_cdac_file(['url' => $addres_url]);

      if(isset($address_information)){
        $pwd_status = str_replace(',','',trim($address_information->PwdStatus));
        $pwd_status = str_replace(' ','_',$pwd_status);

        $data['is_pwd'] = 0;
        if(in_array($pwd_status, ['VISUALLY_IMPAIRED','LOCOMOTOR_DISABLED','SPEECH_HEARING_DISABLED'])){
          $data['is_pwd'] = 1;
        }

        if(!in_array($pwd_status, ['VISUALLY_IMPAIRED','LOCOMOTOR_DISABLED','SPEECH_HEARING_DISABLED']) && $data['age'] < 80){
          return Response::json([
            'success' => false,
            'message' => "As per electoral roll details, you are not eligible for to fill FORM-12D."
          ]);
        }

      }else{
        return Response::json([
          'success' => false,
          'message' => "Please try again."
        ]);
      }

      $detail_by_epic = [
        'success' => true,
        'address' => (array)$address_information,
        'basic'   => $data
      ];
      Session::put('detail_by_epic',$detail_by_epic);
      return Response::json($detail_by_epic);
    }else{
      return Response::json([
        'success' => false,
        'message' => "Please try again."
      ]);
    }
  }

  private function get_pass_key($epic_no){
    $key  = "ABCD1234#123521GISTECIKEY";
    $hash = strtoupper(hash('sha512', $epic_no.$key));
    return $hash; 
  }


  public function get_cdac_file($data = array()){
     $method = "GET";
     $header = array(
         "cache-control"=>"no-cache",
         "content-type"=>"application/json",
     );
     $url = $data['url'];
     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_TIMEOUT, 5000000000000);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000000000000);
     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $data = curl_exec($ch);
     curl_close($ch);
     return json_decode($data);
  }



  public function generate_passkey_url(Request $request){
    $validator = Validator::make($request->all(),['st_code' => 'required','epic_no' => 'required']);
    if ($validator->fails()){
      return Response::json([
        'success' => false,
        'message' => "Please try again."
      ]);
    }
    $epic_no  = $request->epic_no;
    $st_code  = $request->st_code;
    $pass_key = $this->get_pass_key($st_code.$epic_no);
    return Response::json([
      'success' => true,
      'pass_key' => $pass_key
    ]);
  }

  public function start_session(Request $request){
    $validator = Validator::make($request->all(),['basic' => 'required','address' => 'required']);
    if ($validator->fails()){
      return Response::json([
        'success' => false,
        'message' => "Please try again."
      ]);
    }
    $detail_by_epic = [
      'success' => true,
      'address' => $request->address,
      'basic'   => $request->basic
    ];
    Session::put('detail_by_epic',$detail_by_epic);
    return Response::json([
      'success' => true,
      'message' => "Please try again."
    ]);
  }


}  // end class