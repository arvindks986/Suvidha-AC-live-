<?php
    
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//INCLUDING FACADES
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

//INCLUDING MODELS
use App\commonModel;
use App\UserLogin;

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

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;
  
    
class TempUserController extends Controller
  {
    
    public function __construct(){ 
         $this->commonModel = new commonModel();
    }

    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;
    
    //LOGIN FUNCTION STARTS HERE
	
	public function dummy_user_login(){
		
		Session::put('DB_DATABASE', 'boothapp_test');
        Config::set('database.connections.mysql.database', 'boothapp_test');
        DB::reconnect('boothapptest');
        DB::purge('boothapptest');
        DB::setDefaultConnection('boothapptest');
		
		$data                           = [];

        $data['cdatabase'] = '';
         //1=>officer,2=>candidate
         $elec_details=get_election_history_details('AC','2');
          

        $data['elec_details']           = $elec_details;

        return view('auth.dummy-user-auth.login', $data); 
	}
	
    public function postlogin(Request $request){

      //REGISTRATION TRY CATCH STARTS HERE
        try{
         
           $this->validate($request,[
                    'mobile'          => 'required|regex:/^\S*$/u|numeric|digits:10',
                    'captcha'         => 'required|captcha',
                  ],[
                    'mobile.required' => 'Please enter your valid mobile number', 
                    'mobile.min'      => 'Mobile number must be 10 digits',
                    'mobile.numeric'  => 'Please enter your valid mobile number',
                  ]);

           $xss = new xssClean;
        
           // Get user record
           $mobile = $xss->clean_input($request['mobile']);
          
          //CHECKING USER EXIST OR NOT STARTS
          //CHECKING MOBILE NUMBER
          $mobile_exist = UserLogin::where('mobile','=',$mobile)->first();
          if(!$mobile_exist){
              
              //IF USER COMES FIRST TIME OTP SEND STARTS
              $values = array(
                            'mobile' => $mobile,
                            'password' =>bcrypt($mobile),
                            'registration_type'=>'1',
                            'permission_request_status'=>'0',
                            'login_access'=>'1'
                          );

              $LastInsertId = UserLogin::create($values);
              $LastInsertId = $LastInsertId->id;

              $code        = Hash::make(str_random(10));
              $date        = Carbon::now();
              $currentTime = $date->format('Y-m-d H:i:s');
              $otp         = $this->generate_otp();
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

             return Redirect('/candidate-mobileotp/'.base64_encode($mobile))->with('success', 'OTP send on your mobile number.');
              //USER COMES FIRST TIME OTP SEND ENDS

          }else{

              //EXIST USER STARTS
              $user_where = ['mobile'=>$mobile];
              $userexist = UserLogin::where($user_where)
                         //->whereNull('deleted_at')
                         ->first();

              //CHECKING MAXIMUM ATTEMPT FOR OTP STARTS
              $attempts = $userexist->otp_attempt;
              //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS STARTS
              if($attempts > 2){
                 
                UserLogin::where($user_where)
                ->update([
                          'otp_attempt'             =>  '0',
                          'otp'                     =>  '',
                          //'ipaddress'               =>  request()->ip(),
                          //'request_resource_type'   =>  $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');

                      ]);
                return Redirect('/')->with('error', 'Reached maximum attempts');

              }else{
                  $this->otp_attempt($userexist->id, $attempts+1);
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
            

            if($diff>60){
                $otp = $this->generate_otp();
               // $otp = 123456;
                //SAVING OTP INTO DATABASE STARTS

                 UserLogin::where($user_where)
                //->whereNull('deleted_at')
                ->update([
                    'otp'                     => $otp,
                    'otp_time'                => Carbon::now(),
                    'otp_attempt'             => '0',
                    //'ipaddress'               => request()->ip(),
                    //'request_resource_type'   => $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');

                ]);

                //SAVING OTP INTO DATABASE ENDS

                 $message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed.Do not share this OTP Team ECI.";
                //$this->sendmessage($mobile,$message);
                SmsgatewayHelper::gupshup($mobile,$message);


                return Redirect('/candidate-mobileotp/'.base64_encode($mobile))->with('success', 'OTP send on your mobile number.');
             }else{
                    //return 'Can Send only 1 OTP per minute.';
                    return Redirect('/candidate-mobileotp/'.base64_encode($mobile))->with('success', 'Can Send only 1 OTP per minute');
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

          $mobile = base64_decode($request->mobile);
          
          return view('auth.dummy-user-auth.otp',['mobile'=>$mobile]);

          }catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
      }
      //OTP PAGE TRY CATCH ENDS HERE       

    }
    //OTP PAGE FUNCTION ENDS HERE 


    //LOGIN STARTS HERE
    public function customlogin(Request $request)
    {
        // Check validation
        try{

            $validator = Validator::make($request->all(), [ 
                'mobile' => 'required|regex:/^\S*$/u|numeric|digits:10',
                'otp'    => 'required|regex:/^\S*$/u|numeric|digits:6',
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

        //CLIENT IP ADDRESS
        //$user_ipaddress = $request->getClientIp();
        
        //$user = UserLogin::where('mobile', $request->get('mobile'))->first();
        
        ///DB::connection()->enableQueryLog();
        $user_where = ['mobile'=>$mobile];
        $otpuser = UserLogin::where($user_where)
                   ->first();
        
        //MATCHING OTP WITH DB STARTS
        if($otpuser->otp != $otp){

            //CHECKING MAXIMUM ATTEMPT FOR OTP STARTS
            $attempts = $otpuser->otp_attempt;
            //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS STARTS
            if($attempts > 2){
               
               UserLogin::where($user_where)
              ->update([
                        //'is_login'                =>  '0',
                        'otp_attempt'             =>  '0',
                         //'is_verified'             =>  '1',
                        'otp'                     =>  '',
                        //'ipaddress'               =>  request()->ip(),
                        //'request_resource_type'   =>  $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');

                    ]);
              return Redirect('/login')->with('success', 'Reached maximum OTP attempts. Request for new OTP.');
            }else{

                $this->otp_attempt($otpuser->id, $attempts+1);
                return Redirect('/candidate-mobileotp/'.base64_encode($mobile))->with('error', 'Invalid OTP');
            }
            //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS ENDS
            //CHECKING MAXIMUM ATTEMPT FOR OTP ENDS

        }
        //MATCHING OTP WITH DB ENDS
        
        //SETTING IS_LOGIN FILED IN USERS TABLE TO 1 STARTS
        UserLogin::where($user_where)
              ->update([
                        //'is_login'                =>  '1',
                        'otp_attempt'             =>  '0',
                        //'is_verified'             =>  '1',
                        'otp'                     =>  '',
                        //'ipaddress'               =>  request()->ip(),
                        //'request_resource_type'   =>  $request->server('HTTP_USER_AGENT'),//$request->header('User-Agent');
                    ]);
        //SETTING IS_LOGIN FILED IN USERS TABLE TO 1 ENDS
        
        //IF ELSE CONDITION FOR OTP MATCH STARTS
        if ($otpuser->otp == $otp) {
            
            $user = UserLogin::where('mobile',$request->mobile)->first();
            
            //LOGIN AS AUTH OF LARAVEL
            $sessiondata = Auth::loginUsingId($user->id);
            //dd(Auth::loginUsingId()->id);

            if($sessiondata){

                $user_data=Auth()->user();
                Auth::guard('web')->setUser($user_data);
                // change stop

                //dd($user_data);
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

               return Redirect::to('/candidate-home');
            }

         } else {
                 
                return Redirect('/candidate-mobileotp/'.base64_encode($mobile))->with('error', 'Invalid OTP');
                 //return view('welcome',['mobile_number' =>$mobile_number,'otperror' => "Invalid OTP"]);
                }//IF ELSE CONDITION FOR OTP MATCH ENDS

        }catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
         }
            
        
    }
    //LOGIN ENDS HERE

    //RESEND OTP FUNCTION STARTS
    public function resendotp(Request $request)
    {  
      //RESEND OTP FUNCTION TRY CATCH BLOCK STARTS
       try{
            $xss    = new xssClean;
            

            $validator = Validator::make($request->all(), [
                'mobile' => 'required|regex:/^\S*$/u|numeric|digits:10',
            ]);

            if ($validator->fails()) {
               return Redirect::back()
               ->withErrors($validator)
               ->withInput();          
            }

            $mobile = $xss->clean_input($request->mobile);

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
                $otp = $this->generate_otp();
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
                  
                $message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed.Do not share this OTP Team ECI.";

                //$this->sendmessage(request('mobile'),$message);
                SmsgatewayHelper::gupshup(request('mobile'),$message);
                $data = 2; //2 means  = OTP successfully Send.
                return $data;
             }else{
                    $data = 3; //3 means  = Can Send only 1 OTP per minute.
                    return $data;
            }
            
            $attempts = $reotpattempt->otp_attempt;
            //SETTING OTP TO NULL AFTER 3 FAILED ATTEMPTS STARTS
            if($attempts > 2){
               
               UserLogin::where($user_where)
              //->whereNull('deleted_at')
              ->update([
                        //'is_login'                =>  '0',
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
  

  public function logout(){ 
            Auth::logout();
            Session::flush();       
            return Redirect::to('/login');               
           
    }


    /*public function changepassword() {
         
     if(Auth::check()){
            
            $user = Auth::user();
            $uid=$user->officer_id;
            $d=$this->commonModel->getuserbyuserid($uid);
            return view('changepassword', ['user_data' => $d]);

         }else{

              return redirect('/login');
            }
         
    }*/

    
    /*public function changeprofile() {
      if(Auth::check()){

          $user = Auth::user();
          $uid=$user->officer_id;
          $d=$this->commonModel->getuserbyuserid($uid);
          
          return view('changeprofile', ['user_data' => $d]);

       }else {
            return redirect('/login');
            }
            
      }*/

    /*public function validatechangeprofile(Request $request){

      if(Auth::check()){
          $user = Auth::user();
          $uid=$user->officer_id; 
          $pass=$user->password; 

          $this->validate($request,[
                    'name' => 'required|min:8',
                    'phone_no' => 'required|min:10|numeric',
                    'email' => 'required|email',
                  ],[
                    'name.required' => 'Please enter your name',
                    'name.min' => 'Name must be at least 8 characters.', 
                    'phone_no.required' => 'Please enter your valid mobile number', 
                    'phone_no.min' => 'Mobile number must be 10 digits',
                    'phone_no.numeric' => 'Please enter your valid mobile number',
                    'email.email' => 'Please add valid email address',
                    'email.required' => 'Please add valid email address',
                  ]);

        $name = trim($request->input('name'));
        $phone_no = trim($request->input('phone_no')); 
        $email = trim($request->input('email'));  
        
        $profile_master = array('name'=>$name,'email'=>$email,'Phone_no'=>$phone_no); 
        $profile = array('name'=>$name,'email'=>$email); 
        $i = DB::table('officer_master')->where('officer_id', $uid)->update($profile_master);
        $i = DB::table('officer_login')->where('officer_id', $uid)->update($profile);
            
        \Session::flash('success_admin', 'Profile successfully change'); 
            return Redirect::to('changeprofile');
         
       
      }else {
          return redirect('/login');
        }

    }*/
        


 

} 