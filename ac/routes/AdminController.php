<?php
    namespace App\Http\Controllers\Admin;
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
    use Excel;
    use Mail;
    use App\commonModel;
    use App\Helpers\SmsgatewayHelper;
    use App\Classes\xssClean;
class AdminController extends Controller
  {
    
    public function __construct()
        { 
         $this->commonModel = new commonModel();
         $this->xssClean = new xssClean;
        }

    public function change_database(Request $request){

        if(!$request->has('database')){
            Session::flash('error_mes','Please choose a election1.');
            return \Redirect::back();
        }


       $m_election_history = DB::connection("mysql_database_history")->table("m_election_history")->where("id", $request->database)->first();
       
        if(!$m_election_history){
            Session::flash('error_mes','Please choose the correct election.');
            return \Redirect::back();
        }
        Session::put('DB_id',$request->database);
        Session::put('DB_DATABASE',$m_election_history->db_name);
        return \Redirect::back();
    }
 
    
    public function index(){
    
         
        $data                           = [];

        $data['cdatabase'] = '';
         
         $elec_details=get_election_history_details('AC');
          

        $data['skip_password_network']  = \App\models\Admin\OfficerModel::skip_password_network();
        $data['action']                 = url("/auth/login/two_step_login1");
        $data['elec_details']           = $elec_details;

        $setting = \App\models\Admin\SettingModel::get_setting_cache();
        $users=Session::get('admin_login_details');
        $user = Auth::user();
        if(session()->has('admin_login')){ 
            return Redirect::to('/adminhome');
        }else{  
            if(!empty($setting['two_step_login']) && $setting['two_step_login'] == 1){
                return view('admin.auth.web.login', $data);
            }else{
                return view('welcome1', $data);
            } 
        }
    
    }


    public function two_step_login1(Request $request){
        $skip_password_network = \App\models\Admin\OfficerModel::skip_password_network();

        $data   = [];
        $rules  = [
            'username' => 'required',
            'lcaptcha' => 'required|captcha',
        ];

        if($skip_password_network){
            $rules  = [
                'username' => 'required',
                'lcaptcha' => 'required|captcha',
            ];
        }else{
            $rules  = [
                'username' => 'required',
                'password' =>  'required|min:4',
                'lcaptcha' => 'required|captcha',
            ];
        }
       
        $messages = [
            'username.required'   => 'Please enter a valid username.',
            'password'            => 'Please enter a valid password.',
            'lcaptcha.required'   => 'Please enter valid captcha code.',
            'lcaptcha.captcha'    => 'Please enter the valid captcha.',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()){
            Session::flash('error_mes','Please enter the correct credentials.');
            return Redirect::back()->withInput($request->all())->withErrors($validator);
        }

        $username = $request->username;
        $password = $request->password;

        $data                   = [];
        $data['officername']    = ($username)?$username:'';
        if($skip_password_network){

        }else{            
            $data['password']   = ($password)?$password:'';
        }
        $data['is_active']      = 1;


        if(!\App\models\Admin\OfficerModel::authenticate($data)){
            Session::flash('error_mes','Please enter the correct credentials.');
            return Redirect::back();
        }

        \Session::put('username', $data['officername']);
        if($skip_password_network){
        
        }else{
            \Session::put('password', $data['password']);
        }
        
        return redirect("/auth/login/verify/pin");

    }

    public function two_step_pin(Request $request){
        $data = [];
        $data['action'] = url("/auth/login/two_step_login2");
        return view('admin.auth.web.pin', $data);
    }

    public function two_step_login2 (Request $request){
        $skip_password_network = \App\models\Admin\OfficerModel::skip_password_network();
        $data   = [];
        $rules  = [
            'pin' => 'required|pin',
        ];
        $messages = [
            'required'   => 'Please enter a valid pin.',
            'pin' => "please enter a valid 4 digits pin."
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()){
            Session::flash('error_mes','please enter a valid 4 digits pin.');
            return Redirect::back()->withInput($request->all())->withErrors($validator);
        }

        if($skip_password_network){
            if(!\Session::has('username')){
                Session::flash('error_mes','Your session has been expire. Please enter the ');
                return redirect("/officer-login");
            }
        }else{
            if(!\Session::has('username') || !\Session::has('password')){
                Session::flash('error_mes','Your session has been expire. Please enter the ');
                return redirect("/officer-login");
            }
        }

        $pin    = $request->pin;

        $data                   = [];
        $data['officername']    = \Session::get('username');

        if($skip_password_network){

        }else{            
            $data['password']       = \Session::get('password');
        }

        $data['two_step_pin']   = $request->pin;//decrypt
        $data['is_active']      = 1;

        if(!\App\models\Admin\OfficerModel::attempt_login($data)){
            Session::flash('error_mes','Please enter the correct pin.');
            return Redirect::back();
        }

        setcookie('client_ip', $_SERVER['REMOTE_ADDR'], time() + (86400 * 30), "/");
         return Redirect::to('/adminhome');
    }

    //end of login 





    public function ajax_login_step1(Request $request){
        

        $skip_password_network = \App\models\Admin\OfficerModel::skip_password_network();

        $data   = [];
        $rules  = [
            'username' => 'required',
            'lcaptcha' => 'required|captcha',
        ];

        if($skip_password_network){
            $rules  = [
                'username' => 'required',
                'lcaptcha' => 'required|captcha',
            ];
        }else{
            $rules  = [
                'username' => 'required',
                'password' =>  'required|min:4',
                'lcaptcha' => 'required|captcha',
            ];
        }
       
        $messages = [
            'username.required'   => 'Please enter a valid username.',
            'password'            => 'Please enter a valid password.',
            'lcaptcha.required'   => 'Please enter valid captcha code.',
            'lcaptcha.captcha'    => 'Please enter the valid captcha.',
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()){
            return \Response::json([
                'status' => false,
                'errors' => $validator->errors()->getMessageBag()
            ]);
        }

        $username = $this->xssClean->clean_input(Check_Input($request->username));
        $password = $this->xssClean->clean_input(Check_Input($request->password));

        $data                   = [];
        $data['officername']    = ($username)?$username:'';
        if($skip_password_network){

        }else{            
            $data['password']       = ($password)?$password:'';
        }
        $data['is_active']      = 1;


        if(!\App\models\Admin\OfficerModel::authenticate($data)){
            return \Response::json([
                "status"    => false,
                "message"   => "Please enter the correct credentials." 
            ]);
        }

        \Session::put('username', $data['officername']);
        if($skip_password_network){
        
        }else{
            \Session::put('password', $data['password']);
        }
        
        return \Response::json([
            "status"    => true,
            "message"   => "Authenticate Successfully"
        ]);

    }

    public function ajax_login_step2(Request $request){
        $skip_password_network = \App\models\Admin\OfficerModel::skip_password_network();
        $data   = [];
        $rules  = [
            'pin' => 'required|pin',
        ];
        $messages = [
            'required'   => 'Please enter a valid pin.',
            'pin' => "please enter a valid 4 digits pin."
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()){
            return \Response::json([
                'status' => false,
                'errors' => $validator->errors()->getMessageBag()
            ]);
        }

        $pin = $this->xssClean->clean_input(Check_Input($request->pin));

        $data                   = [];
        $data['officername']    = \Session::get('username');

        if($skip_password_network){

        }else{            
            $data['password']       = \Session::get('password');
        }

        $data['two_step_pin']   = $request->pin;//decrypt
        $data['is_active']      = 1;

        if(!\App\models\Admin\OfficerModel::attempt_login($data)){
            return \Response::json([
                "status"    => false,
                "message"   => "Please enter the correct pin." 
            ]);
        }

        setcookie('client_ip', $_SERVER['REMOTE_ADDR'], time() + (86400 * 30), "/");
            
        return \Response::json([
            "status"    => true,
            "message"   => "Please enter the correct credentials." 
        ]);

    }
    
    public function postlogin(Request $request1) 
        {
        $request = (object) $request1->all();
        // dd($request);
        $this->validate(
            $request1, 
                [
                  'username' => 'required',
                  'password' => 'required|min:8',
                  'lcaptcha' => 'required|captcha',
                ],
                [
                  'username.required' => 'Please enter valid username',
                  'password.required' => 'Please enter Valid Password',
                  'password.min' => 'Please enter Valid Password',
                  //'password.regex' => 'Please enter Valid Password',
                  'lcaptcha.required'=>'Please enter valid captcha code.',
                  'lcaptcha.captcha'=>'Invalid captcha code.',
                ]);
        $username = $this->xssClean->clean_input(Check_Input($request->username));
        $password = $this->xssClean->clean_input(Check_Input($request->password));
       if (auth()->guard('admin')->attempt(['officername' => $username, 'password' => $password,'is_active'=>'1']))  
                    { 

                      $user_data=Auth()->guard('admin')->user();
                    //if( $user_data->login_flag==0){
                        Session::flash('sucess_message', 'You Are Successfully Logged In'); 
                        $login_history = array('officer_id'=>$user_data->id, 'officer_login_id'=>$user_data->officername,
                                        'ipaddress'=>$_SERVER['REMOTE_ADDR'], 'login_date'=>Date('Y-m-d H:i:s'));
                        $this->commonModel->insertData('officer_history', $login_history);
                        // $n=array('login_flag'=>1);
                        //   $this->commonModel->updatedata('officer_login','id', $user_data->id,$n); 

                         Session::put('admin_login_details', $user_data);
                         Session::put('logged_DB_id', $user_data->id);
                         Session::put('admin_login',true);
                        return Redirect::to('/adminhome'); 
                    //     }
                    // else {
                    //       \Session::flash('data_username', 'This username allready login please logout.');
                    //       return Redirect::to('/logout');
                    // } 
             
                  }
              else{
                  \Session::flash('data_username', 'Invalid Credentials.');
                  return Redirect::to('/officer-login');
              }

        }
     // public function otpverification(Request $request)
     //          {
     //             if ($request->session()->has('admin_username') && $request->session()->has('auth_token')) {
     //              return view('admin.otp');
     //             }
     //            else {
     //              return Redirect::to('/officer-login'); 
     //            }       

     //          }
    // public function resendotp(Request $request)
    //       {
    //             $mobile_no = Session::get('mobile_no');
    //             $token = Session::get('auth_token');
    //             $login_id = Session::get('login_id');
    //            // echo $login_id;  echo $token; echo "-".$mobile_no; die;
    //        // if(!empty($mobile_no) && !empty($token) && !empty($login_id)) {
    //            if(!empty($token) && !empty($login_id)) {
    //             $date = Carbon::now();
    //             $currentTime = $date->format('Y-m-d H:i:s');
    //             $mobile_no = Session::get('mobile_no');
    //             $token = Session::get('auth_token');
    //             $mobile_otp = "123456"; //rand(100000,999999);
                
    //             $mobileotp = array(
    //                 'mobile_otp' => $mobile_otp,
    //                 'otp_time' => $currentTime,
    //             );
    //             $updatetoken = DB::table('officer_login')->where('id', $login_id)->where('auth_token', $token)->update($mobileotp);
    //             if($mobile_no!=""){
    //               $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." for ECI Candidate Portal. Please enter the OTP to proceed.Your OTP will be valid till 10 minutes.Do not share this OTP Team ECI";
    //                    //$response = SmsgatewayHelper::sendOtpSMS($mob_message, $mobile_no);
    //                     }  
    //              return view('admin.otp');
    //         }
    //         else{
    //             return Redirect::to('/officer-login'); 
    //         } 
    //       }
    // public function verifyloginotp(Request $request)
    //       {  
    //         $mobile_no = Session::get('mobile_no');
    //         $token = Session::get('auth_token');
    //         $login_id = Session::get('login_id');
    //         $admin_username = Session::get('admin_username');
    //       //if(!empty($mobile_no) && !empty($token) && !empty($login_id)) {
    //         if(!empty($token) && !empty($login_id)) {
    //         $post = $request->all();

    //         $this->validate($request, [
    //             'mobile_otp' => 'required',
    //         ]);
    //         $mobile_no = Session::get('mobile_no');
    //         $password = Session::get('password');
    //         $token = Session::get('auth_token');
    //         $mobile_otp=$request->mobile_otp;
            
    //        // $credentials = $request->only($phone, pass_decrypt($password));
    //         $matchotp = DB::table('officer_login')->where('id', $login_id)->where('mobile_otp', $mobile_otp)->where('auth_token', $token)->first();
             
    //         if($matchotp) {
    //             $date = Carbon::now();
    //             $date->modify('-5 minutes');
    //             $formatted_date = $date->format('Y-m-d H:i:s');
    //             $otptime = DB::table('officer_login')->where('otp_time','>=',$formatted_date)->where('id', $login_id)->where('mobile_otp', $post['mobile_otp'])->first();
                 
    //             if($otptime) {    
    //             if (auth()->guard('admin')->attempt(['officername' => $admin_username, 'password' => $password]))  
    //               {  echo "chala";
    //            // $user_data =$this->commonModel->selectone('officer_master','officer_id',$otptime->officer_id); 
    //             $user_data=Auth()->guard('admin')->user();
    //            // dd($user_data);
    //             Session::flash('sucess_message', 'You Are Successfully Logged In'); 
    //             $request->session()->forget('password');
    //             $request->session()->forget('auth_token');
    //             $request->session()->forget('login_id');
    //             $request->session()->forget('admin_username');
    //             //$request->session()->forget('officer_id');
    //             $request->session()->forget('mobile_no');
                 
    //             $login_history = array('officer_id'=>$user_data->id,
    //                             'officer_login_id'=>$user_data->officername,
    //                             'ipaddress'=>request()->ip(),
    //                             'login_date'=>Date('Y-m-d H:i:s'));
    //             $this->commonModel->insertData('officer_history', $login_history); 
    //              Session::put('admin_login_details', $user_data);
    //              Session::put('logged_DB_id', $user_data->id);
    //              Session::put('admin_login',true);
    //             return Redirect::to('/adminhome');
 
    //             }
    //             }
    //             else{
    //                 \Session::flash('opterror', 'OTP has been expired, please resend otp.');
    //                     return Redirect::to('/otpverification');
                   
    //             }
    //         }
    //         else{
    //         \Session::flash('opterror', 'OTP does not match. Please resend otp to your mobile.');
    //             return Redirect::to('/otpverification');
                
    //         }
    //     }
    //     else{
    //         return redirect('/officer-login');
    //     }
    //       }
   
    // public function logout(){ 
    //          $user = Auth::user();
    //          dd($user);
    //          $uid=$users->id; 
    //          $n=array('login_flag'=>1);
    //                       $this->commonModel->updatedata('officer_login','id', $uid,$n);
    //         Auth::logout();
    //         Session::flush();       
    //         return Redirect::to('/officer-login');               
           
    //     }


        // forgot password dated - 2019-03-20
    public function get_forgot(Request $request){
        $data = [];
        if(Session::has('admin_login')){ 
            return Redirect::to('/adminhome');
        }
        $data['get_election_form'] = \Common::get_election_form($request);
        return view('admin/auth/forgot', $data);
    }

    public function post_forgot(Request $request){

        $data   = [];
        $rules  = [];
        /*if(preg_match("/^[0-9]+$/i", $request->email)){ 
            $request->merge(['mobile' => $request->email]);
            $data['mobile']     = $request->email;
            $rules['mobile']    = 'required|mobile|exists:officer_login,Phone_no';
        }else{
            $data['email']      = $request->email;
            $rules['email']     = 'required|email|exists:officer_login,email';
        }*/

        $data['mobile']     = $request->mobile;
        $rules['mobile']    = 'required|mobile|exists:officer_login,Phone_no';

        $data['lcaptcha']   = $request->lcaptcha;
        $rules['lcaptcha']  = 'required|captcha';

        $messages = [
            'required'          => 'Please enter valid email/mobile number',
            'lcaptcha.required' =>'Please enter valid captcha code.',
            'lcaptcha.captcha'  =>'Invalid captcha code.',
            'mobile'            => 'Please enter valid mobile number',
            'mobile.exists'     => 'Mobile does not exists in our database.',
            'email.exists'      => 'Email does not exists in our database.'
        ];

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails())
        {
            return Redirect::back()->withInput($request->all())->withErrors($validator);
        }

        //check time if OTP sent before 1 minute
        $check_otp_time = \App\models\Admin\OfficerModel::check_otp_time($request);
        if($check_otp_time > 60){
            $result = false;
            $otp = rand(111111,999999);
            if($request->has('mobile')){

                $result = \App\models\Admin\OfficerModel::update_otp_by_mobile($request->mobile,$otp);                  
                if($result){
                    $this->send_otp($request->mobile,$otp);
                    if(trim($result->email) != ''){
                        $this->send_email($request->email, $otp);
                    }
                    return Redirect::to('/forgot/enter-otp/'.$result->otp_verify_by_string);
                }
            } 
           
        }else{
            Session::flash('status', 0);
            Session::flash('flash-message','You can only reset password once in a minute.');
        }
        return Redirect::back();
    }

    public function get_otp($token = ''){
        $data = [];
        if($token == ''){
            Session::flash('status', 0);
            Session::flash('flash-message','Session expired. Please try again.');
            return Redirect::to('/forgot');
        }

        $get_user = \App\models\Admin\OfficerModel::get_user_by_token($token);
        if(!$get_user){
            Session::flash('status', 0);
            Session::flash('flash-message','Session expired. Please try again.');
            return Redirect::to('/forgot');
        }

        Session::put('forgot-token', $token);
        $data['action']     = url('/forgot/otp/verifying');
        $data['resend_otp'] = url('/forgot/resend');
        $data['mobile']     = $get_user['Phone_no'];
        $data['mobile_otp'] = $get_user['mobile_otp'];
        $data['email']      = $get_user['email'];
        $data['otp_time']   = $get_user['otp_time'];
        return view('admin/auth/get_otp', $data);
    }

    public function verify_otp(\App\Http\Requests\Admin\Profile\VerifyOtpRequest $request){
        $data = [];
        $filter_data = [];
        $filter_data['otp']     = $request->otp;
        $filter_data['mobile']  = $request->mobile;
        
        if(!Session::has('forgot-token')){
            Session::flash('status', 0);
            Session::flash('flash-message','Session Expired. Please forgot password again.');
            return Redirect::to(url('/forgot'));
        }

        $result = \App\models\Admin\OfficerModel::get_user($filter_data);
        if(!$result){
            Session::flash('status', 0);
            Session::flash('flash-message','Please provide valid 6 digit otp.');
            return Redirect::back();
        }
        return Redirect::to(url('/forgot/new/'.Session::get('forgot-token')));
    }

    public function resend_otp(Request $request){
        $json = [];
        if(!Session::has('forgot-token')){
            Session::flash('status', 0);
            Session::flash('flash-message','Session Expired. Please forgot password again.');
            $json['redirect'] = url('/forgot');
        }

        $get_user = \App\models\Admin\OfficerModel::get_user_by_token(Session::get('forgot-token'));
        if(!$get_user){
            Session::flash('status', 0);
            Session::flash('flash-message','Session Expired. Please forgot password again.');
            $json['redirect'] = url('/forgot');
        }
        $request->merge([
            'email' => $get_user['email'],
            'mobile' => $get_user['Phone_no']
        ]);
        //check time if OTP sent before 1 minute
        $check_otp_time = \App\models\Admin\OfficerModel::check_otp_time($request);
        if($check_otp_time <= 60){
            Session::flash('status', 0);
            Session::flash('flash-message','You can only request for otp once in a minute.');
            $json['success'] = true;
            return \Response::json($json);
        }

        $otp = rand(111111,999999); 
        $result = \App\models\Admin\OfficerModel::update_otp_by_mobile($get_user['Phone_no'],$otp);   
        if($result){
            $this->send_otp($get_user['Phone_no'],$otp);
            $status = 1;
            $message = "OTP has been sent to your mobile number."; 
        }
        Session::flash('status', $status);
        Session::flash('flash-message',$message);
        return \Response::json($json);
    }

    public function send_email($email, $otp){

        $html = "Dear Sir/Madam,";
        $html .= " \n\n";
        $html .= "your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed:";
        $html .= " \n\n";
        $html .= "Do not share this OTP.";
        $html .= " \n\n";
        $html .= "Thanks \n\n";
        $html .= "Suvidha Team \n\n";
        $html = strip_tags($html);
        $subject  = "Reset Password";
        try {
           
                mail($email, $subject,$html,'suvidha.eci.gov.in');
           
            $status = 1;
            $message = "Reset instruction has been sent on your email. Kindly check your email.";  
        } catch (\Exception $e) {   
            $status = 0;
            $message = 'Server encounted an issue while sending email. Please try again.';
        }
        Session::flash('status', $status);
        Session::flash('flash-message',$message);
        Session::forget('forgot-token');
    }

    public function send_otp($mobile, $otp){
        try{
            $message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed. Do not share this OTP Team ECI";
            $response = SmsgatewayHelper::gupshup($mobile,$message);
        }catch(\Exception $e){

        }
    }

    public function enter_new_password($id, $election_id = '', Request $request){

        $data = [];
        if(Session::has('admin_login')){ 
            return Redirect::to('/adminhome');
        }
        $data['action'] = url('/forgot/post-new');
        $get_user = \App\models\Admin\OfficerModel::get_user_by_token($id);
        if(!$get_user){ 
            Session::flash('status', 0);
            Session::flash('flash-message','Token is invalid. Please forgot password again.');
            return Redirect::to('/forgot');
        }
        Session::put('forgot-token',$id);
        return view('admin/auth/new_password', $data);
    }

    public function update_forgot(\App\Http\Requests\Admin\Profile\PasswordRequest $request){
        $data = [];
        if(Session::has('admin_login')){ 
            return Redirect::to('/adminhome');
        }
        if(!Session::has('forgot-token')){
            Session::flash('status', 0);
            Session::flash('flash-message','Session Expired. Please forgot password again.');
            return Redirect::to('/forgot');
        }
        $data['token']      = Session::get('forgot-token');
        $data['password']   = $request->password;
        try{
            $get_user = \App\models\Admin\OfficerModel::update_password($data);
        }catch(\Exception $e){
            Session::flash('status', 0);
            Session::flash('flash-message','Server is down. Please try again after sometime.');
            return Redirect::back();
        }
        if(!$get_user){ 
            Session::flash('status', 0);
            Session::flash('flash-message','Please try again.');
            return Redirect::back();
        }
        Session::forget('forgot-token');
        Session::flash('status', 1);
        Session::flash('flash-message','Password has been updated successfully. Please login to continue');
        return Redirect::to('/officer-login');

    }

    public function get_whilte_list($id = 0){
        if($id == 0){
            dd(file_get_contents("https://widget.freshworks.com"));
        }else{
            dd(file_get_contents("https://widget.freshworks.com/widgets/44000000524.js"));
        }
    }
    
} // end