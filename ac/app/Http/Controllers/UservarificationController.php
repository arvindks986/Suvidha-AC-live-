<?php
		namespace App\Http\Controllers;
		use Illuminate\Http\Request;
		use App\User;
		use App\user_master;
		use App\m_entity_master;
		use DB;
		use App\Helpers\SmsgatewayHelper;
		use Redirect;
		use Session;
		use App\Models\state_master;
		use App\Models\state_user_history;
		use Carbon\Carbon;
		use Illuminate\Support\Facades\Hash;
class UservarificationController extends Controller
{
     
    public function index($id)
    {    
		
		$id=base64_decode($id);
                
		$rec=getById('officer_login','id',$id);
		
		if(!empty($rec)){ 
					return view('admin.update_profile',['rec'=>$rec]);
			}else{ 
				Session::flash('autheroor', 'Password is already created Please enter password to login in system. In case you do not remember click on forgot password to reset '); 
			     return redirect('/login');
		    }
		 
    }
	 
	public function otpvarify($uid,$otp)
	{
		$uid=$uid;
		$userinfo = DB::table('officer_login')
				->where('officer_login.id', '=', $uid)->where('officer_login.mobile_otp', '=', $otp)
				->first();
		if(!empty($userinfo->id))
		{
			$value=1;	
		 
		}else{ $value=0;  }
		return json_encode($value);
		
    }
	
	 
	public function updateuserpass($uid,$password,$pin)
		{   
		

     	$data_act = array(
			'password'=>hash('sha256',$password),
			'two_step_pin'=>bcrypt($pin),
			'mobile_otp'=>'',
			'is_active'=>1
			);
			DB::table("officer_login")->where('id',$uid)->update($data_act); 
		 
		$value='User Updated Sucessfully'; 
		return json_encode($value);	
		
	   }
	
	public function resendotp($uid)
	{
		$encodeuid=$uid;
		$userinfo=getById('officer_login','id',$encodeuid); 
		//$userid= $userinfo->id;
		//dd($userinfo);			
		//if(count($userinfo)>0)
		if($userinfo)
				{	
				$date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s'); 
                $code = Hash::make(str_random(10));
                $mobile_otp =rand(100000,999999);
				$record = array('password'=>'','mobile_otp' => $mobile_otp,'otp_time' => $currentTime,'auth_token' => $code);
				$n = DB::table('officer_login')->where('id', $uid)->update($record);
				
			    if($userinfo->Phone_no!=""){
					
					//$mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." for ECI Suvidha Portal. Please enter the OTP to proceed.Your OTP will be valid till 10 minutes.Do not share this OTP Team ICT";
					
					
					$mob_message = $mobile_otp." is OTP to login for Encore Portal. Your username is ".$userinfo->officername.". Activation link has been sent to your email. ECI";
					
					
					$response = SmsgatewayHelper::gupshup($userinfo->Phone_no,$mob_message);
				    return 1;
					
				}else{
					return 0;
				}
			
				}else{
					return 3;
				}	
		
	}
	 
	
	public function updatepassword($email_id,$password,$otp)
    {
		//$decode_emailid=base64_decode($email_id);
		$decode_emailid=$email_id;
		$userinfo = DB::table('user_login')
					->where('email', '=', $decode_emailid)
				->where('OTP', '=', $otp)
				->first();
				
		if(count($userinfo)>0)
		{	
			$data_act = array('password'=>bcrypt($password));
			DB::table("user_login")->where('email',$decode_emailid)->update($data_act); 
			$value=1; 
		}
		else{
			$value=0; 
		} 
	return $value;		
	
				
	
    }
	
 	public function sendotp(Request $request)
	{
		
		
		if(!\Session::has('username') || !\Session::has('password')){
                Session::flash('error_mes','Your session has been expire. Please try again!! ');
                return redirect("/officer-login");
            }
		
		
		$userinfo=getById('officer_login','officername',Session::get('username')); 
	
		/* if($userinfo->Phone_no != $request->mobile){
                Session::flash('error_mes','Please enter correct mobile number.');
                return redirect("/auth/login/verify/pin");
            } */
		
		
		
		if($userinfo)
				{	
				$date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s'); 
                $code = Hash::make(str_random(10));
                $mobile_otp =rand(100000,999999);
				$record = array('mobile_otp' => $mobile_otp,'otp_time' => $currentTime,'auth_token' => $code);
				$n = DB::table('officer_login')->where('id', $userinfo->id)->update($record);
				
			    if($userinfo->Phone_no!=""){
					
				
				
					$mob_message = "Dear User, Your OTP to login to the ENCORE Portal is ".$mobile_otp.", ECI.";
					
					
					$response = SmsgatewayHelper::gupshup($userinfo->Phone_no,$mob_message);
					
				    return 1;
					
				}
			
				}else{
					return 0;
				}	
		
	}

    
}
