<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ComplaintMasters;

class RoLoginApi extends Controller
{
    public static $username = "ECISMS-ICT"; //username of the department
    public static $password = "ict@1234567"; //password of the department
    public static $senderid = "ecisms"; //senderid of the deparment
    public static $deptSecureKey = "93e36092-b1a0-4f0a-9084-4d0eb84f6744"; //departsecure key for encryption of message...
    public $successStatus = 200;
    
    public function ROLogin(Request $request){ 
       // return $request->all();
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|digits:10',
            'device_id' => 'required',
            'fcm_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => "Invalid data provided!"], 200);
        }
        $eciuser = User::where([['mobile_number', '=', request('mobile_number')]])->where('user_type', '=', 6)->first();
        if (isset($eciuser)) {
    
            $eciuser->OTP = $this->generate_otp();
            $otp = "Your OTP is - " . $eciuser->OTP;
            $eciuser->fcm_id = $request->fcm_id;
            $eciuser->password = bcrypt($request->mobile_number);
            $eciuser->device_id = $request->device_id;
            
            $eciuser->save();
            $encryp_password = sha1(trim(self::$password));
            $key = hash('sha512', trim(self::$username) . trim(self::$senderid) . trim($otp) . trim(self::$deptSecureKey));
            $data = array(
                "username" => trim(self::$username),
                "password" => trim($encryp_password),
                "senderid" => trim(self::$senderid),
                "content" => trim($otp),
                "smsservicetype" => "otpmsg",
                "mobileno" => trim($request->mobile_number),
                "key" => trim($key),
            );
            $fields = '';
            foreach ($data as $key => $value) {
                $fields .= $key . '=' . $value . '&';
            }
            rtrim($fields, '&');
            $post = curl_init();
            curl_setopt($post, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($post, CURLOPT_URL, 'https://msdgweb.mgov.gov.in/esms/sendsmsrequest');
            curl_setopt($post, CURLOPT_POST, count($data));
            curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
             $result = curl_exec($post);
            return response()->json(['success' => true], 200);
        }else{
            return response()->json(['success' => false, 'error' => "No such user exist"], 200);
        }

    }
    public function ROVerify(Request $request){
        $rouser = User::where([['mobile_number', '=', request('mobile_number')]])->where('user_type','=', 6)->first();
        //return $eciuser;
        if (isset($rouser)) {
            if($rouser->OTP==request('OTP')){
                $token = $rouser->createToken('MyApp')->accessToken;
                unset($rouser->OTP);
                return response()->json(['token' => $token,'success' => true,'user_details' => $rouser ], 200);
            }else{
                return response()->json(['success' => false, 'error' => "Wrong OTP"], 200);
            }
        }else{
            return response()->json(['success' => false, 'error' => "No such user exist"], 200); 
        }
    }
   
    
    public function generate_otp()
    {

        $string = '0123456789';
        $string_shuffled = str_shuffle($string);
        $password = substr($string_shuffled, 1, 4);
        return $password;
    }
}
