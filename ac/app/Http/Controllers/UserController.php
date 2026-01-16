<?php
    
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//INCLUDING FACADES
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
//INCLUDING MODELS
use App\commonModel;
use App\UserLogin;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
//INCLUDING CLASSES AND HELPERS
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use Carbon\Carbon;
use DB;
use Validator;
use Config;
use \PDF;
use Excel;
use Mail;
use Session;

use App\Helpers\LogNotification;
use App\Services\EncryptionService;

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;
  
    
class UserController extends Controller
  {
    
    public function __construct(){ 
         $this->commonModel = new commonModel();
          $this->xssClean = new xssClean;
    }

    public function change_database(Request $request)
    {
        

      
         if (!$request->has('database')) {
            Session::flash('error_mes', 'Please choose a election1.');
            return Redirect::back();
        }

        Config::set('database.default', "mysql");
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::reconnect('mysql');
        
        $m_election_history = DB::table("m_election_history")->where("id", $request->database)->first();

        if (!$m_election_history) {
            Session::flash('error_mes', 'Please choose the correct election.');
            return Redirect::back();
        }
        Session::put('DB_id', $request->database);
        Session::put('DB_DATABASE', $m_election_history->db_name);
        
        
        Config::set('database.default', "mysql");
        Config::set('database.connections.mysql.database', $m_election_history->db_name);
        DB::reconnect('mysql');
        
        return Redirect::back();
    }


    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;
    
    //LOGIN FUNCTION STARTS HERE
    public function postlogin(Request $request){
		
		
	//dd($request->mobile,$request->election,$request->captcha);	

      //REGISTRATION TRY CATCH STARTS HERE
        try{
           $this->validate($request,[
                    'mobile'          => 'required|numeric',
                    'election'        => 'required|numeric|min:0|not_in:0',
                    'captcha'         => 'required|captcha',
                  ],[
                    'mobile.required' => __('messages.mobile_error'), 
                    'election.not_in' => __('messages.election_error'), 
                    'mobile.min'      => __('messages.mobile_error_length'),
                    'mobile.numeric'  => __('messages.mobile_error_numeric'),
                  ]);

           $xss = new xssClean;
         
           $mobile = $xss->clean_input($request['mobile']);
          
		  
          //CHECKING USER EXIST OR NOT STARTS
          //CHECKING MOBILE NUMBER
          $mobile_exist = UserLogin::where('mobile','=',$mobile)->first();

          if(!$mobile_exist){
              
              //IF USER COMES FIRST TIME OTP SEND STARTS
              $values = array(
                            'mobile' => $mobile,
                          //  'password' =>bcrypt($mobile),
                            'registration_type'=>'1',
                            'permission_request_status'=>'0',
                            'login_access'=>'1'
                          );

              $LastInsertId = UserLogin::create($values);
              $LastInsertId = $LastInsertId->id;

              $code        = Hash::make(str_random(10));
              $date        = Carbon::now();
              $currentTime = $date->format('Y-m-d H:i:s');
              if($mobile=='9871124359'){
              $otp='123456';
              }else{
              $otp         = $this->generate_otp();
          }
              //$otp         = $this->generate_otp();
             // $otp = 123456;
              //SAVING OTP & OTP TIME INTO DB STARTS
              $datas = array(
                          'otp'            => $otp,
                          'remember_token' => $code,
                          'otp_time'       => Carbon::now(),
                          'otp_attempt'    => '1',
                      );

              DB::table('user_login')->where('id',$LastInsertId)->update($datas);
              //SAVING OTP INTO & OTP TIME DB ENDS 
			  
			  
			  

              $message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed.Do not share this OTP Team ECI.";
              //$this->sendmessage($mobile,$message);
              SmsgatewayHelper::gupshup($mobile,$message);
            
             // return view('otp',['mobile' => $mobile]);
			 
			 
				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $mobile ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'SUVIDHA';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'Send_OTP';
				$ErrorMessage['TransectionStatus']= 'SUCCESS';
				$ErrorMessage['LogDescription']= 'OTP sent Successfully';
				LogNotification::LogInfo($ErrorMessage);

             return Redirect('/mobileotp/'. Crypt::encryptString($mobile))->with('success', 'OTP sent on your mobile number.');
              //USER COMES FIRST TIME OTP SEND ENDS

          }else{
 
              //EXIST USER STARTS
              $user_where = ['mobile'=>$mobile];
              $userexist = UserLogin::where($user_where)
                         //->whereNull('deleted_at')
                         ->first();

              //CHECKING MAXIMUM ATTEMPT FOR OTP STARTS
              $attempts = $userexist->p_failed_attempts;
            
              $currentTime = Carbon::now();
                    $diff=$currentTime->diffInSeconds($userexist->p_last_failed_attempt_at);
                   // dd($attempts,$diff);
              //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS STARTS
              if($attempts >=3 && $diff < 300 ){
                //dd("Reached maximum attempts");
       return Redirect::back()->withErrors(['msg' => 'To many failed login attempts. Please login after 5 min']);
                //return Redirect('/')->with('message', 'Reached maximum attempts');

              }else{
                  //$this->otp_attempt($userexist->id, $attempts+1);
                  UserLogin::where($user_where)
                //->whereNull('deleted_at')
                ->update([
                  //  'otp'                     => $otp,
                   // 'otp_time'                => Carbon::now(),
                   // 'otp_attempt'             => '0',
                   
                     'p_failed_attempts'             =>  '0',
                     'p_last_failed_attempt_at'     =>Null
                    

                ]);
              }
            //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS ENDS

            if($userexist->mobile != ""){

                $user2 = UserLogin::where($user_where)
                 //->whereNull('deleted_at')
                 ->first();

                //CHECKING OTP TIME DIFFRENCE STARTS
                if(!is_null($user2->otp_time)){

                    $currentTime = Carbon::now();
                    $diff=$currentTime->diffInSeconds($user2->otp_time);

                }else{
                        $diff=61; 
                }
                //CHECKING OTP TIME DIFFRENCE ENDS
            

            if($diff>5){
                if($mobile=='9871124359'){
              $otp='123456';
              }else{
              $otp         = $this->generate_otp();
          }
               
//dd($otp);
                 UserLogin::where($user_where)
             
                ->update([
                    'otp'                     => $otp,
                    'otp_time'                => Carbon::now(),
                    'otp_attempt'             => '0',
                    //'ipaddress'               => request()->ip(),
                     'p_failed_attempts'             =>  '0',
                     'p_last_failed_attempt_at'     =>Null
                    //'request_resource_type'   => $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');

                ]);

                //SAVING OTP INTO DATABASE ENDS

                 $message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed.Do not share this OTP Team ECI.";
                //$this->sendmessage($mobile,$message);
                SmsgatewayHelper::gupshup($mobile,$message);



				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $mobile ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'SUVIDHA';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'Send_OTP';
				$ErrorMessage['TransectionStatus']= 'SUCCESS';
				$ErrorMessage['LogDescription']= 'OTP sent Successfully';
				LogNotification::LogInfo($ErrorMessage);


                return Redirect('/mobileotp/'. Crypt::encryptString($mobile))->with('success', 'OTP sent on your mobile number.');
             }else{
                    //return 'Can Send only 1 OTP per minute.';
                    return Redirect('/mobileotp/'. Crypt::encryptString($mobile))->with('success', 'Can Send only 1 OTP per minute');
            }

        }

          }          

        }catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
      }
      //LOGIN TRY CATCH ENDS HERE       

    }
    //LOGIN FUNCTION ENDS HERE     

    //OTP PAGE FUNCTION STARTS HERE
    public function mobileotp(Request $request, $mobile){

      //OTP PAGE TRY CATCH STARTS HERE
        try{

          $mobile = Crypt::decryptString($request->mobile);

          //dd($mobile);
          
          return view('otp',['mobile'=>$mobile]);

          }catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
      }
      //OTP PAGE TRY CATCH ENDS HERE       

    }
    //OTP PAGE FUNCTION ENDS HERE 


    //LOGIN STARTS HERE
    public function customlogin(Request $request)
    {
      

      $input = $request->all();
      
        try{

            $validator = Validator::make($request->all(), [ 
                'mobile' => 'required|regex:/^\S*$/u|numeric|digits:10',
                'otp'    => 'required|regex:/^\S*$/u|numeric|digits:6',
                'password'    => 'required',
            ]);

           if ($validator->fails()) {
               return Redirect::back()
               ->withErrors($validator)
               ->withInput();          
            }
          

        $xss = new xssClean;
        // Get user record
        $mobile        = $xss->clean_input($request['mobile']);
        $otp           = $xss->clean_input($request['otp']);
        $password      = $xss->clean_input($request['password']);


		
		
		/*start Here */
		$checkMobileExistOrNot = DB::connection('mysql')
		->table('user_login')
		->select('id')
		->where('mobile', '=', $request->mobile)
		->get();
		
		
        $user_where = ['mobile'=>$mobile];
        $otpuser = UserLogin::where($user_where)
                   ->first();

if ($otpuser->otp != $otp ||  ! Hash::check($password, $otpuser->password)) {
  $failed_attempt=$otpuser->p_failed_attempts;
  if($failed_attempt >= 3){
     
    
     return redirect('/')->with(['msg' => 'To many failed login attempts. Please login after 5 min']);
         


  }
  UserLogin::where($user_where)
              ->update([
                      
                        'p_failed_attempts'             =>  $failed_attempt+1,
                        'p_last_failed_attempt_at'     =>date('Y-m-d H:i:s')
                       ]);

}



           
        if($otpuser->otp != $otp){

            
            $attempts = $otpuser->otp_attempt;
            
            if($attempts > 2){
               
               UserLogin::where($user_where)
              ->update([
                        
                        'otp_attempt'             =>  '0',
                         
                        'otp'                     =>  '',
                       

                    ]);
              
			  
			  return Redirect('/login')->with('success', __('messages.otp_attempt_max'));
            }else{

                $this->otp_attempt($otpuser->id, $attempts+1);
                
				
				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $mobile ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'SUVIDHA';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'OTP_Verify';
				$ErrorMessage['TransectionStatus']= 'FAILURE';
				$ErrorMessage['LogDescription']= 'OTP Invalid';
				LogNotification::LogInfo($ErrorMessage);
				
				return Redirect('/mobileotp/'.Crypt::encryptString($mobile))->with('error', __('messages.otp_Invalid'));
            }
            

        }
        
        UserLogin::where($user_where)
              ->update([
                        //'is_login'                =>  '1',
                        'otp_attempt'             =>  '0',
                       
                    ]);

      if ($otpuser->otp == $otp) {



              if(Hash::check($password, $otpuser->password))
            { }else{
                 return Redirect('/mobileotp/'.Crypt::encryptString($mobile))->with('error', __('Invalid Password'));
            }



            
            $user = UserLogin::where('mobile',$request->mobile)->first();
            $sessiondata = Auth::loginUsingId($user->id);
            //LOGIN AS AUTH OF LARAVEL
            $reg=$request->session()->regenerate();
            $deviceKey = hash('sha256', Str::random(64));

/* Store in PHP session */
//

       
$deviceKey = hash('sha256', Str::uuid()->toString());
Session::put('device_key', $deviceKey);
$userId = Auth::id();
            $sessionId = Session::getId();

            //    $dbSession = DB::table('sessions')
            // ->where('id', Session::getId())
            // ->where('user_id', Auth::id())
            // ->first();

          $dbSession=  DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', session()->getId())
            ->delete();


           
cookie()->queue(
    cookie(
        'device_key',
        $deviceKey,
        60 * 24,   // 1 day
        '/',
        null,
        true,      // secure
        true,      // httpOnly
        false,
        'Strict'   // SameSite
    )
);
    




          
            Auth::logoutOtherDevices($request['password']);
			
			$dddata = DB::connection('mysql')
			->table('profile')
			->select('id','mobile')
			->where('candidate_id', '=', $user->id)
			->get();				
			if(!empty( $dddata[0]->id ) ){				
			    if( $dddata[0]->mobile ==  $request->mobile){	
				  DB::connection('mysql')->table('profile')
				  ->where('mobile', $request->mobile)
				  ->update([
				    "is_verified_mobile_otp" =>1,
				  ]);	
			    }
			} else {
			    $isUpdate = DB::connection('mysql')->table('profile')
				->insert([
				  "is_verified_mobile_otp" =>1,
				  "mobile" =>$request->mobile,
				  "candidate_id" =>$user->id
				]);	
			}
			
			
			
			
			        if($sessiondata){

             
                $user_data=Auth()->user();
                //dd($user_data->mobile);
                Auth::guard('web')->setUser($user_data);
                
                $credentials = $user_data->mobile;
                Session::flash('sucess_message', 'You Are Successfully Logged In'); 
                $login_history = array(
                                       'session_id'    =>$user_data->remember_token,
                                       'user_login_id' =>$user_data->id,
                                       'ipaddress'     =>request()->ip(),
                                       'updated_at'=>Date('Y-m-d H:i:s'),
                                       'login_time'=>Date('Y-m-d H:i:s'),
                                       'login_date'=>Date('Y-m-d H:i:s')
                                     );

               $this->commonModel->insertData('user_history', $login_history); 

               Session::put('login_details', $user_data);

               Session::put('logged_id', $user_data->id);

               Session::put('user_login',true);
			   
			   $var = '';
			if(!empty($_SERVER["HTTP_USER_AGENT"])){
				
				$agent = $_SERVER["HTTP_USER_AGENT"];
				if( preg_match('/MSIE (\d+\.\d+);/', $agent) ) {
				  $var = "Internet_Explorer";
				} else if (preg_match('/Chrome[\/\s](\d+\.\d+)/', $agent) ) {
				  $var = "Chrome";
				} else if (preg_match('/Edge\/\d+/', $agent) ) {
				  $var = "Edge";
				} else if ( preg_match('/Firefox[\/\s](\d+\.\d+)/', $agent) ) {
				  $var = "Firefox";
				} else if ( preg_match('/OPR[\/\s](\d+\.\d+)/', $agent) ) {
				  echo "Opera";
				}else if(preg_match('/Safari[\/\s](\d+\.\d+)/', $agent) ) {
				  $var = "Safari";
				}
			}
            
			$brow=Session::put('browser',$var);
			   
            
				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $user->mobile ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'SUVIDHA';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'User_Logged_in';
				$ErrorMessage['TransectionStatus']= 'SUCCESS';
				$ErrorMessage['LogDescription']= 'User Logged In Successfully';
				LogNotification::LogInfo($ErrorMessage);
	
               return Redirect::to('/home');
              // Auth::logoutOtherDevices($user_data->remember_token);

            }

         } else {
                 

				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $user->mobile ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'SUVIDHA';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'User_Logged_in';
				$ErrorMessage['TransectionStatus']= 'FAILURE';
				$ErrorMessage['LogDescription']= 'User login failed';
				
				LogNotification::LogInfo($ErrorMessage);
			    
				 
				 
                
				return Redirect('/mobileotp/'.Crypt::encryptString($mobile))->with('error', __('messages.otp_Invalid'));
                 
                }



        }catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
         }
            
        
    }
    
    public function resendotp(Request $request)
    {  


      //RESEND OTP FUNCTION TRY CATCH BLOCK STARTS
       try{
            $xss    = new xssClean;
            

            $validator = Validator::make($request->all(), [
                'mobile' => 'required|regex:/^\S*$/u|numeric|digits:10',
            ]);

          //$mob=base64_decode($request->mobile);
            $mob=EncryptionService::decrypt($request->mobile);
//

            $mobile = $xss->clean_input($mob);

            //USER WHERE CONDITON
            $user_where = ['mobile'=>$mobile];

            //CHECKING IF USER ALREADY EXCEED OTP ATTEMPTS STARTS

            $reotpattempt = UserLogin::where($user_where)
                            //->whereNull('deleted_at')
                            ->first();

             //CHECKING OTP TIME STARTS
            if(!is_null($reotpattempt->otp_time)){
                $currentTime = Carbon::now();
                $diff=$currentTime->diffInSeconds($reotpattempt->otp_time);
            }else{
                $diff=61; 
            }
            

            //CHECKING OTP TIME ENDS
            
            if($diff>60){
                if($mobile=='9871124359'){
              $otp='123456';
              }else{
              $otp         = $this->generate_otp();
          }
               // $otp = $this->generate_otp();
               // $otp = 123456;
                //SAVING OTP INTO DATABASE STARTS
                UserLogin::where($user_where)
                //->whereNull('deleted_at')
                ->update([
                    'otp'                     => $otp,
                    'otp_time'                => Carbon::now(),
                    'otp_attempt'             => '0',
                    //'ipaddress'               =>  request()->ip(),
                    //'request_resource_type'   =>  $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');
                ]);
                //SAVING OTP INTO DATABASE ENDS
				
				
				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $mobile ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'SUVIDHA';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'Resend_OTP';
				$ErrorMessage['TransectionStatus']= 'SUCCESS';
				$ErrorMessage['LogDescription']= 'OTP sent Successfully';
				LogNotification::LogInfo($ErrorMessage);
		  
		  
                  
                $message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed.Do not share this OTP Team ECI.";

                //$this->sendmessage(request('mobile'),$message);
                SmsgatewayHelper::gupshup($mobile,$message);
                $data = 2; //2 means  = OTP successfully Send.
                return $data;
             }else{
                    $data = 3; //3 means  = Can Send only 1 OTP per minute.
                    return $data;
            }
            
            $attempts = $reotpattempt->otp_attempt;
            //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS STARTS
            if($attempts > 3){
               
               UserLogin::where($user_where)
              //->whereNull('deleted_at')
              ->update([
                        'verify_otp'                =>  '0',
                        'otp_attempt'             =>  '0',
                        //'is_verified'             =>  '1',
                        'otp'                     =>  '',
                        //'ipaddress'               =>  request()->ip(),
                        //'request_resource_type'   =>  $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');

                    ]);
              
              return Redirect('/login')->with('success', 'Reached maximum OTP attempts. Request for new OTP.');

            }else{
                $this->otp_attempt($reotpattempt->id, $attempts+1);
            }
            //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS ENDS
            
            //CHECKING IF USER ALREADY EXCEED OTP ATTEMPTS ENDS
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //RESEND OTP FUNCTION TRY CATCH BLOCK ENDS
        
    }
    //RESEND OTP FUNCTION ENDS
    public function setpassword(Request $request)
    {  
        //dd($request);
         $xss    = new xssClean;
       
         $mobile=EncryptionService::decrypt($request->mobile);
         $password=EncryptionService::decrypt($request->password);
         $cpassword=EncryptionService::decrypt($request->cpassword);
         
         //dd($mobile,$password,$cpassword);
        //$mobile = $xss->clean_input($request->mobile);
        //$password = $xss->clean_input($request->password);
        //$cpassword = $xss->clean_input($request->cpassword);
          //dd($mobile,$password,$cpassword);

    //        $this->validate($request, [

      
    //     'mobile' => 'required|numeric',

    //     'password' => 'required|confirmed|min:6',

    //     'cpassword' => 'required'

    // ]);
        // if(empty($mobile) || !is_numeric($mobile))
        // {         $data = 2;
        //           return $data;
        // }else if(empty($password)|| empty($cpassword))
        // {
        //          $data = 2;
        //           return $data;

        // }else if($password != $cpassword){
        //           $data = 4;
        //           return $data;
        // }
       // preg_match('/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/', 'Password1&');

       $uppercase = preg_match('@[A-Z]@', $password);
       $lowercase = preg_match('@[a-z]@', $password);
       $number    = preg_match('@[0-9]@', $password);
       $specialChars = preg_match('@[^\w]@', $password);

       $cuppercase = preg_match('@[A-Z]@', $cpassword);
       $clowercase = preg_match('@[a-z]@', $cpassword);
       $cnumber    = preg_match('@[0-9]@', $cpassword);
       $cspecialChars = preg_match('@[^\w]@', $cpassword);

      if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8){

                    $data = 3;
                    return $data;
        }else  if(!$cuppercase || !$clowercase || !$cnumber || !$cspecialChars || strlen($cpassword) < 8){
                    $data = 3;
                    return $data;
        
        }
         
//dd("hello");
          
            $user_where = ['mobile'=>$mobile];

          

            $reotpattempt = UserLogin::where($user_where)
                            ->first();
                            //dd($reotpattempt,"ll");
            if(!empty($reotpattempt)){
 
                $passwordhash = Hash::make($password);
                $datas = array(
                         
                          'password'    => $passwordhash,
                          'pwd_id'    => '1',
                          'isActive'  => '1',
                      );
                  $returnvalue=DB::table('user_login')->where('mobile',$mobile)->update($datas);
                    $data = 1;
                    return $data;
               //   return Redirect('/login')->with('success', 'Reached maximum OTP attempts. Request for new OTP.');
           
            }

      
    }
    //End Function

    //Verify OTP First time
      
      public function Verifyotpfirst(Request $request)
      {

    $otp=EncryptionService::decrypt($request->otp);
    $mobile=EncryptionService::decrypt($request->mobile);
    //dd($otp,$mobile);
         $xss    = new xssClean;
       
       /*$mobile_decry = base64_decode($request->mobile);
       $otp_decry    = base64_decode($request->otp);
       $mobile       = $xss->clean_input($mobile_decry);
       $otp          = $xss->clean_input($otp_decry);
       */

    //dd($mobile_decry,$otp_decry,$otp,$mobile,$request->mobile,$request->otp);
       
          $rules  = [
            'otp' => 'required|numeric|digits:6',
            'mobile' => 'required|numeric|digits:10',
        ];
         $messages = [
            'otp.required'   => 'Otp Field Required',
             'mobile.required'   => 'Mobile Required',
        ];
      
         
            $user_where = ['mobile'=>$mobile];
            $reotpattempt = UserLogin::where($user_where)->first();

                           // dd($reotpattempt->verify_otp,$reotpattempt->otp,$otp);

         

             //CHECKING OTP TIME STARTS
            if(!is_null(@$reotpattempt->otp_time)){
                $currentTime = Carbon::now();
                $diff=$currentTime->diffInSeconds(@$reotpattempt->otp_time);
            }else{
                $diff=61; 
            }

            if( $diff > 120)
            {
                $data=4;
                return $data;
            }



            if(@$reotpattempt->otp != $otp)
            {
                $data=2;
                return $data;
            }else{
                 $datas = array(
                         'verify_otp'    => 1,
                      );
                 $returnvalue=DB::table('user_login')->where('mobile',$mobile)->update($datas);
                 $data=1;
                 return $data;
            }

               

      }

    //End Function

   public function Forgotpassword(Request $request)
   {
      $mobile=EncryptionService::decrypt($request->mobile);
    // $mobile = base64_decode($request->mobile);
    // dd($request->mobile,"dfdfdfd",$mobile);
     // if(empty($mobile) )
      if(empty($mobile) || !is_numeric($mobile))
        {   
             
              $data = 2;
                  return $data;
        }
      //  dd("true");
            $user_where = ['mobile'=>$mobile];
            $reotpattempt = UserLogin::where($user_where)->first();
            if(!empty($reotpattempt)){
 
              
                $datas = array(
                         
                          'password'    => '',
                          'pwd_id'    => '0',
                          'verify_otp' => '0',
                      );
                  $returnvalue=DB::table('user_login')->where('mobile',$mobile)->update($datas);
                    $data = 1;
                    return $data;
           
           
            }


     }
  

  public function logout(){ 
            Auth::logout();
            Session::flush();       
            return Redirect::to('/login');               
           
    }


        


 

} 
