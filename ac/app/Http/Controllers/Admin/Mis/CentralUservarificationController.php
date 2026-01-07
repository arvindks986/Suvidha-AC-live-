<?php
		namespace App\Http\Controllers\Admin\Mis;
		use App\Http\Controllers\Controller;
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
class CentralUservarificationController extends Controller
{
     
    public function index($id)
    {    
		
		$id=decrypt_string($id);
		$rec=getById('officer_login','id',$id);
		 //dd($rec);
		if(!empty($rec)){ 
				return view('admin.mis.account-activate.update_profile',['rec'=>$rec]);
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
	
	 
	public function updateuserpass($uid,$password,$name='')
		{   
     	$data_act = array('password'=>$password, 'is_active'=>1);
			DB::table("officer_login")->where('id',$uid)->update($data_act); 
		 
		$value='User Updated Sucessfully'; 
		return json_encode($value);	
	   }
	
	public function resendotp($uid)
	{
		$encodeuid=$uid;
		$userinfo=getById('officer_login','id',$encodeuid); 
		$userid= $userinfo->id;
					
		if(count($userinfo)>0)
				{	
				$date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s'); 
                $code = Hash::make(str_random(10));
                $mobile_otp =rand(100000,999999);
				$record = array('password'=>'','mobile_otp' => $mobile_otp,'otp_time' => $currentTime,'auth_token' => $code);
				$n = DB::table('officer_login')->where('id', $uid)->update($record);
			    if($userinfo->Phone_no!=""){
					$mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." for ECI Suvidha Portal. Please enter the OTP to proceed.Your OTP will be valid till 10 minutes.Do not share this OTP Team ICT";
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
			$data_act = array('password'=>$password);
			DB::table("user_login")->where('email',$decode_emailid)->update($data_act); 
			$value=1; 
		}
		else{
			$value=0; 
		} 
	return $value;		
	
				
	
    }
	
 

    
}
