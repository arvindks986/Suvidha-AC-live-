<?php namespace App\Http\Controllers\Admin\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB, Common, Validator, Response;
use App\models\Admin\{StateModel, AcModel, PollingStation, DistrictModel, OtpModel};
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use GuzzleHttp\Client;


class CommonController extends Controller{

    public function __construct(){
        $this->xssClean = new xssClean;
    }
public static function get_cache($code, $data = []){
        if (\Cache::has($code)){
            return \Cache::get($code);
        }
        return false;
    }

    public static function generate_cache($code, $data = []){
        return \Cache::rememberForever($code, function () use ($data) {
            return $data;
        });
    }

    public static function get_code_for_cache($code, $filter = []){
        $name   = [];
        $name[] = 't_'.$code;
        $name[] = 'e_id_'.Auth::user()->election_id;
        if(!empty($filter['st_code'])){
            $name[] = 'st_'.$filter['st_code'];
        }
        if(!empty($filter['dist_no'])){
            $name[] = 'dist_'.$filter['dist_no'];
        }
        if(!empty($filter['ac_no'])){
            $name[] = 'ac_'.$filter['ac_no'];
        }
        if(!empty($filter['ps_no'])){
            $name[] = 'ps_'.$filter['ps_no'];
        }
        if(!empty($filter['group_by'])){
            $name[] = 'g_'.$filter['group_by'];
        }
        return implode('-',$name);
    }
    public function index(Request $request){
        $data = [];
        $i = 0;
        $results = \DB::select("SHOW processlist");
        foreach ($results as $key => $value) {
            if($value->Command=='Sleep' || $value->Time > 60){
               \DB::select("KILL ".$value->Id);
               $i++;
            }
        }

        $data['heading_title'] = "Sleep session cleared ". $i;
        return view('admin/common/clear-sleep-session', $data);
    }

    public static function get_request_filter($request){
        $ac_no      = NULL;
        $st_code    = NULL;
        $ps_no      = NULL;
        $role_id    = NULL;
        $dist_no    = NULL;
        $restricted_ps = ['91','99','128','129','189'];
        $filter     = Common::get_auth_filter();

        if($request->has('st_code')){
            $st_code = $request->st_code;
        }
        if($request->has('dist_no')){
            $dist_no = $request->dist_no;
        }
        if($request->has('ac_no')){
            $ac_no = $request->ac_no;
        }
        if($request->has('ps_no')){
            $ps_no = $request->ps_no;
        }
        if($request->has('role_id')){
            $role_id = $request->role_id;
        }
  
        if($filter['role_id'] == '19'){
            $ac_no      = $filter['ac_no'];
            $dist_no    = $filter['dist_no'];
            $st_code    = $filter['st_code'];
        }else if($filter['role_id'] == '5'){
            $dist_no    = $filter['dist_no'];
            $st_code    = $filter['st_code'];
        }else if($filter['role_id'] == '4'){
            $st_code    = $filter['st_code'];
        }else{

        }

        return [
            'st_code'   => $st_code,
            'dist_no'   => $dist_no,
            'ac_no'     => $ac_no,
            'ps_no'     => $ps_no,
            'role_id'   => $role_id
        ];
    }

    public static function get_form_filters($filter_by = [],$request){
        $data           = [];
        $filter         = Common::get_auth_filter();
        $request_filter = Common::get_request_filter($request);
        $ac_no          = $request_filter['ac_no'];
        $st_code        = $request_filter['st_code'];
        $dist_no        = $request_filter['dist_no'];
        $ps_no          = $request_filter['ps_no'];
        $role_id        = $request_filter['role_id'];
        $request_filter['state'] = $request_filter['st_code'];

        //states
        if(!empty($filter_by['st_code'])){ 
            $states = [];
            if(!in_array(Auth::user()->role_id,['19'])){
                $state_filter = $filter;
            }else{
                $state_filter = [
                    'st_code' => $request_filter['st_code']
                ];
            }
            $states_results = StateModel::get_states($state_filter);
            foreach($states_results as $iterate_state){
                $is_active = false;
                if($st_code == $iterate_state['ST_CODE']){
                    $is_active = true;
                }
                $states[] = [
                    'id'        => $iterate_state['ST_CODE'],
                    'name'      => $iterate_state['ST_NAME'],
                    'active'    => $is_active
                ];
            }
            $data[]   = [
                'id'        => 'st_code',
                'name'      => 'State',
                'results'   => $states,
            ];
        }

        //acs
        if(!empty($filter_by['dist_no'])){
            $dists = [];
            if($st_code){
                if(in_array(Auth::user()->role_id,['19','5'])){
                    $dist_filter = $filter;
                }else{
                    $dist_filter = [
                        'st_code' => $request_filter['st_code'],
                    ];
                }
                $dists_results = DistrictModel::get_districts($dist_filter);
                foreach($dists_results as $iterate_dist){
                    $is_active = false;
                    if($dist_no == $iterate_dist['dist_no']){
                        $is_active = true;
                    }
                    $dists[] = [
                        'id' => $iterate_dist['dist_no'],
                        'name' => $iterate_dist['dist_no'].'-'.$iterate_dist['dist_name'],
                        'active'  => $is_active
                    ];
                }
            }
            $data[]   = [
                'id'      => 'dist_no',
                'name'      => 'District',
                'results'   => $dists,
            ];
        }

        //acs
        if(!empty($filter_by['ac_no'])){
            $acs = [];
            if($st_code){
                if(in_array(Auth::user()->role_id,['19'])){
                    $ac_filter = $filter;
                }else{
                    $ac_filter = [
                        'state' => $request_filter['st_code'],
                        'dist_no' => $request_filter['dist_no'],
                    ];
                }
                $acs_results = AcModel::get_records($ac_filter);
                
                foreach($acs_results as $iterate_ac){
                    $is_active = false;
                    if($ac_no == $iterate_ac['ac_no']){
                        $is_active = true;
                    }
                    $acs[] = [
                        'id' => $iterate_ac['ac_no'],
                        'name' => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
                        'active'  => $is_active
                    ];
                }
            }
            $data[]   = [
                'id'      => 'ac_no',
                'name'      => 'AC',
                'results'   => $acs,
            ];
        }

        //polling station
        if(!empty($filter_by['ps_no'])){
            $pss = [];
            if($st_code && $ac_no){
                $ps_results = PollingStation::get_polling_stations([
                    'st_code'       => $st_code,
                    'ac_no'         => $ac_no,
                    'restricted_ps' => []
                ]);
                foreach($ps_results as $iterate_ps){
                    $is_active = false;
                    if($ps_no == $iterate_ps['PS_NO']){
                        $is_active = true;
                    }
                    $pss[] = [
                        'id'        => $iterate_ps['PS_NO'],
                        'name'      => $iterate_ps['PS_NO'].'-'.$iterate_ps['PS_NAME_EN'],
                        'active'    => $is_active
                    ];
                }
            }
            $data[]   = [
                'id'      => 'ps_no',
                'name'      => 'Polling Station',
                'results'   => $pss,
            ];
        }

        if(!empty($filter_by['designation'])){
            //role filter
              $role_id = 0;
              if($request->has('role_id')){
                $role_id = $request->role_id;
              }
              $roles   = [];
              $roles[] = [
                'name'  => 'BLO',
                'id'      => 33
              ];
              $roles[] = [
                'name'  => 'PO',
                'id'      => 34
              ];
              $roles[] = [
                'name'  => 'PRO',
                'id'      => 35,
              ];
              foreach ($roles as $role_iterate) {
                $is_role_active = false;
                if($role_id == $role_iterate['id']){
                  $is_role_active = true;
                }
                $role_types[] = [
                  'name'  => $role_iterate['name'],
                  'id'      => $role_iterate['id'],
                  'active'  => $is_role_active
                ];
              }
              $data[] = [
                'id'      => 'role_id',
                'name'    => 'Designation',
                'results' => $role_types
              ];
        }
        

        return $data;
    }

    public static function encrypt_string($string){
        return Crypt::encryptString($string);
    }

    public static function decrypt_string($string){
        return Crypt::decryptString($string);
    }

    public static function generate_url($path){
        $url    = '/';
        $filter = Common::get_auth_filter();
        $url    .= $filter['base'];
        return url($url.'/'.$path);
    }

    public static function get_auth_filter(){
        $ac_no      = '';
        $dist_no    = '';
        $st_code    = '';
        $base       = '';
        $role_id    = Auth::user()->role_id;
        if(Auth::user() && $role_id == '19'){
            $ac_no    = Auth::user()->ac_no;
            $st_code  = Auth::user()->st_code;
            $dist_no  = Auth::user()->dist_no;
            $base     = 'roac';
        }else if(Auth::user() && $role_id == '5'){
            $st_code  = Auth::user()->st_code;
            $dist_no  = Auth::user()->dist_no;
            $base     = 'acdeo';
        }else if(Auth::user() && $role_id == '4'){
            $st_code  = Auth::user()->st_code;
            $base     = 'acceo';
        }else if(Auth::user() && $role_id == '27'){
            $base     = 'eci-index';
        }else if(Auth::user() && $role_id == '37'){
            $base     = 'maintenance';
        }else if(Auth::user() && $role_id == '50'){
            $base     = 'seczonal';
        }
		else{
            $base     = 'eci';
        }
        if(Auth::user() && !Session::has('DB_DATABASE')){
            $base     = 'central';
        }
        return [
            'ac_no'     => $ac_no,
            'dist_no'   => $dist_no,
            'st_code'   => $st_code,
            'base'      => $base,
            'role_id'   => $role_id
        ];
    }

    public static function get_election_form($request){
        $data                   = [];
        $data['elec_details']   = get_election_history_details('AC');
        return view('admin.common.get_election_form', $data);
    }

    public static function header($request){
        $menus  = [];
        $menus[]  = [
            'name' => 'Home',
            'href' => Common::generate_url('dashboard'),
            'child' => [],
            'sort_order' => 0,
        ];


        //common for all
        $child  = [];
        $child[]  = [
            'name' => 'Database Tables',
            'href' => Common::generate_url('table'),
            'child' => [],
            'sort_order' => 0,
        ];
        $child[]  = [
            'name' => 'Reset Password/Pin',
            'href' => Common::generate_url('officer/reset-password'),
            'child' => [],
            'sort_order' => 1,
        ];
        $menus[]  = [
            'name' => 'Services',
            'href' => 'javascript:void(0)',
            'child' => $child
        ];

        //common for all
        $child  = [];
        $child[]  = [
            'name' => 'Change Password',
            'href' => url('profile/password'),
            'child' => [],
            'sort_order' => 0,
        ];
        $child[]  = [
            'name' => 'Change Pin',
            'href' => url('profile/pin'),
            'child' => [],
            'sort_order' => 1,
        ];
        $child[]  = [
            'name' => 'Logout',
            'href' => url('logout'),
            'child' => [],
            'sort_order' => 2,
        ];
        $menus[]  = [
            'name' => 'Account',
            'href' => 'javascript:void(0)',
            'child' => $child
        ];
        return $menus;
    }

    public static function get_election_data(){
        $elec_details = [];
        foreach(get_election_history_details('AC') as $iterate_election){
            $elec_details[] = [
                'id' => $iterate_election->id,
                'name' => $iterate_election->description,
                'elect_type' => $iterate_election->elect_type,
                'db_name'    => $iterate_election->db_name
            ];
        }
        return $elec_details;
    }


     // centeralize otp for all
  public static function get_otp($code, $redirect_url, Request $request){
    $data                   = [];
    $data['heading_title']  = "Enter the mobile number.";
    $data['mobile'] = '';
    $data['otp']    = '';
    $data['code']           = $code;
    $data['redirect_url']   = $redirect_url;
    $data['action'] = url('common/send-otp');
    $data['action_verify_otp'] = url('common/verify_otp');
    return view('admin.common.get_otp', $data);
  }

  public function send_otp(Request $request){
    $data = array();    
    $validator = Validator::make($request->all(),['mobile' => 'required|mobile', 'code' => 'required'],['mobile'=>'please enter a valid mobile number']);
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
      'otp'    => $otp,
      'code'   => $this->xssClean->clean_input($request->code),
    ];


    $check_otp_time = OtpModel::check_otp_time($data);
    if($check_otp_time && $check_otp_time <= 60){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "You can only request for otp once in a minute."]
      ]);
    }

    OtpModel::add_otp($data);
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
    $validator = Validator::make($request->all(),['mobile' => 'required|mobile','otp' => 'required|alpha_num', 'code' => 'required'],['mobile'=>'please enter a valid mobile number']);
    if ($validator->fails())
    {
      return Response::json([
        'success' => false,
        'errors' => $validator->getMessageBag()->toArray()
      ]);
    }
    $data = [
      'mobile'  => $this->xssClean->clean_input($request->mobile),
      'otp'     => $this->xssClean->clean_input($request->otp),
      'code'    => $this->xssClean->clean_input($request->code),
    ];
    $is_verify = OtpModel::verify_otp($data);
    if(!$is_verify){
      return Response::json([
        'success' => false,
        'errors' => ["warning" => "Please enter a valid otp"]
      ]);
    }
    Session::put("otp_verify", strtotime(date('Y-m-d H:i:s')));
    Session::put("otp_mobile", $request->mobile);

    return Response::json([
      'success' => true,
      'mobile' => $request->mobile
    ]);
  }

  public static function make_directory($folder_path){
    $path = config("public_config.upload_folder");
    foreach (explode('/',$folder_path) as $iterate_path) {
        $path .= '/'.$iterate_path;
        if (!file_exists($path)) {
          mkdir($path, 0777, true);
        }
    }
  }
   
   //epic number search
  /*Code is replaced on 13-01-2022 by below function*/
  /*public function search_by_epic_cdac(Request $request){
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
    $i = 0;
    while(isset($elector_information) && $elector_information->response->numFound == 0 && $i < 3){
        $elector_information = $this->get_cdac_file(['url' => $url]);
        $i++;
    }

    if(isset($elector_information) && $elector_information->response->numFound){
        $data = (array)$elector_information->response->docs[0];
        $pass_key = $this->get_pass_key($data['st_code'].$epic_no);
        $addres_url = "https://evp.ecinet.in/mservices/api/EVP/GetEVPElectorDetails?EPIC_NO=".$epic_no."&Pass_key=".$pass_key."&st_code=".$data['st_code']."&ac_no=".$data['ac_no'];
        $address_information = $this->get_cdac_file(['url' => $addres_url]);
        $j = 0;
        while(!isset($address_information) && $j < 3){
            $address_information = $this->get_cdac_file(['url' => $url]);
            $j++;
        }

        if(isset($address_information)){
            $pwd_status = str_replace(',','',trim($address_information->PwdStatus));
            $pwd_status = str_replace(' ','_',$pwd_status);

            $data['is_pwd'] = 0;
            if(in_array($pwd_status, ['VISUALLY_IMPAIRED','LOCOMOTOR_DISABLED','SPEECH_HEARING_DISABLED'])){
              $data['is_pwd'] = 1;
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
  }*/

  public function GetHash($input, $key) {
    try{
        $hash = hash('sha512', $input.$key);
        return $hash;
      } catch (Exception $ex) {
               return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
    }

  public function search_by_epic_cdac(Request $request){
    $data = array();    
    //$validator = Validator::make($request->all(),['epic_no' => 'required|min:10']);
    
    $rules = [
        'epic_no' => 'required',
        'epic_no' => 'required|alpha_dash|min:7'
    ];
    $messages = [
        'epic_no.required' => 'Please Enter valid Epic Number.',
        'epic_no.alpha_dash' => 'Please Enter valid Epic Number.',
    ];
    $validator = Validator::make($request->all(), $rules, $messages);
    
    if ($validator->fails())
    {
      return Response::json([
        'success' => false,
        'message' =>  __('step1.Epic_error')
      ]);

    }
          $epic_no  = $request->epic_no;   
            
          // New Code By Kishore
          $key = '3ac7a40b1bc6fc790b34d7256';
          //$key = 'ABCD1234#123521GISTECIKEY';
          $pass_key = $this->GetHash($epic_no, $key);


          $url = 'https://electoralsearch.in/api/search?passKey='.$pass_key.'&search_type='.'epic'.'&epic_no='.$epic_no.'';
       

         // Old Code By Waseem
        //$pass_key = $this->get_pass_key($epic_no);
        //$url      = "https://electoralsearch.in/VoterSearch/SASSearch?epic_no=".$epic_no."&search_type=epic&pass_key=".$pass_key;
    
        
        //return $url;
        //echo  Session::get('locale');
        $elector_information = $this->get_cdac_file(['url' => $url]);
        //print_r($elector_information);die;
        //return $elector_information;
        
        
        $i = 0;
        while(isset($elector_information) && $elector_information->response->numFound == 0 && $i < 3){
           // $elector_information = $this->get_cdac_file(['url' => $url]);
            $i++;
        }

    if(isset($elector_information) && $elector_information->response->numFound){
        $data = (array)$elector_information->response->docs[0];
        //echo "<pre>"; print_r($data); exit;
        //$pass_key = $this->get_pass_key($data['st_code'].$epic_no);




       // $addres_url = "https://evp.ecinet.in/mservices/api/EVP/GetEVPElectorDetails?EPIC_NO=".$epic_no."&Pass_key=".$pass_key."&st_code=".$data['st_code']."&ac_no=".$data['ac_no'];





/*

        $address_information = $this->getEpicDetails($data['epic_no'],$data['st_code']);

       // echo $addres_url;exit;
        // $j = 0;
        // while(!isset($address_information) && $j < 3){
        //     $address_information = $this->get_cdac_file(['url' => $url]);
        //     $j++;
        // }

        if(isset($address_information)){
            $pwd_status = str_replace(',','',trim(@$address_information->PwdStatus));
            $pwd_status = str_replace(' ','_',$pwd_status);

            $data['is_pwd'] = 0;
            if(in_array($pwd_status, ['VISUALLY_IMPAIRED','LOCOMOTOR_DISABLED','SPEECH_HEARING_DISABLED'])){
              $data['is_pwd'] = 1;
            }

        }else{
                $msg='';    
                if( Session::get('locale') == 'hi') {
                $msg= __('step1.Epic_error'); 
                } else {
                 $msg= __('step1.Epic_error'); 
                }
                
            return Response::json([
              'success' => false,
              'message' =>$msg
            ]);
            
        }








        */

      $detail_by_epic = [
        'success' => true,
        //'address' => (array)$address_information,
        'basic'   => $data
      ];
      Session::put('detail_by_epic',$detail_by_epic);
      return Response::json($detail_by_epic);
    }else{
        
        $msg2='';   
        if( Session::get('locale') == 'hi') {
             $msg2= __('step1.Epic_error'); 
        } else {
          $msg2= __('step1.Epic_error');   /// Here
        }
        
      return Response::json([
        'success' => false,
        'message' => $msg2
      ]);
    }
  }

  private function get_pass_key($epic_no){
    //$key  = "ABCD1234#123521GISTECIKEY";
    $key  = "3ac7a40b1bc6fc790b34d7256";
    $hash = strtoupper(hash('sha512', $epic_no.$key));
    return $hash; 
  }




     public  function authorization()
    {


      


$pub_key_string='-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlwuS6k0mtUggfJYFwhol
Ncq2pqglDMTpEYCeB3w//+9dIEV454ZYnhVfGCzFwTwjDdztCzNoohLssMy13gN0
MVjjfXltj7g5OWyeLdmvfPpWxlMxqvr73THu1jq9Dnrx6JHY9v+RCt06yeRQpLmu
4g6hfifx6ARrlTncHH/3v1L6N541vcQswc2SIK1hj27ywJfaISzvWPy6HAIiSTuQ
iamuK7Fz4AdyquTzz0E24ujEjgbOsxRX9+ROK+/riOUxsvEUGxJB8+KRWvvKjPiR
nqjGg8pBki39yLXcCBqkuCqlpcJn1Svs0IcBxmHVIhV7NsEfdGl1Ol7aVuQJXslX
FwIDAQAB
-----END PUBLIC KEY-----';



       // $common_for_all = Utility::common_for_all();
         $X_API_KEY = 'VPORTAL-C02D80A2B1Z5A943D4F1TX106D38C33F';
        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => ' application/json',
                'X-API-KEY' => $X_API_KEY,
                 "cache-control" => "no-cache",            ]
        ]);

        $url = 'https://nvspservices.ecinet.in/api/login/Authenticate2'; //aauth url

         $arr["UserName"] = 'VPortalUser';
         $arr["Password"] = '^oT#rP0rT@l*%U$eR97!#';

        // echo $arr["UserName"].' '.$pub_key_string; die;
          openssl_public_encrypt($arr["UserName"],$UserName,$pub_key_string);
          $UserNameEnc = base64_encode($UserName);  
           //echo "<pre>"; print_r($arr); exit;  
          openssl_public_encrypt($arr["Password"],$Password,$pub_key_string);
          $PasswordEnc = base64_encode($Password);


          



         
        $arr["UserName"]        = $UserNameEnc;
        $arr["Password"]        = $PasswordEnc;


        $data = [
            "UserName"=>$arr["UserName"],
            "Password"=>$arr["Password"],        ];
        $response = $client->post($url, ['form_params' => $data]);
        
        return $response = json_decode($response->getBody(), true);
    }



public function getEpicDetails($epic,$st_code) { 
                 // dd($req->epic);
        $epic_no = $epic; // $epic;
        $st_code = $st_code; //$st_code;
       //dd($epic_no.$st_code)
        $input =  $epic_no . $st_code; //$epic_code;
        $X_API_KEY = 'VPORTAL-C02D80A2B1Z5A943D4F1TX106D38C33F';
        $key= 'b24dG&5I$We!dlf@*d2sDf83k';
         $hash = hash('sha512', $input.$key);
        //$key = "3ac7a40b1bc6fc790b34d7256";
         //$common_for_all = Utility::common_for_all();
       // $key = $common_for_all["hash_key"];//        $hash = hash('sha512', $input.$key);
        //dd($hash);
         
        $key_res = $this->authorization();
        //echo "<pre>"; print_r($key_res); exit;
        $key_res = $key_res["token"];
        $client = new Client([
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        "Authorization" => $key_res,
                        'Accept' => ' application/json',
                        'X-API-KEY' => $X_API_KEY,
                         "cache-control" => "no-cache",
                    ]
                ]);      
        $url = "https://nvspservices.ecinet.in/api/FormsAPI/GetElectorsDetails?st_code=$st_code&epic_no=$epic_no";
        $response = $client->get($url);
        return $response = json_decode($response->getBody(), true);  
               

        
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
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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
} // end