<?php
namespace App\Http\Traits;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Collection;

//INCLUDING FACADES
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Input;

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

//INCLUDING MODELS
use App\commonModel;
use App\UserLogin;

trait CommonTraits {


   //STATE NAME FETCHING STARTS
    public static function get_state_name($id) {
        
        //STATE NAME FETCHING TRY CATCH BLOCK STARTS
        try {
            $data = DB::table('state_master')->where('ST_CODE', '=', $id)->first();

            return $data->ST_NAME;

            } catch (Exception $ex) {
                 return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //STATE NAME FETCHING TRY CATCH BLOCK ENDS
        
    }
    //STATE NAME FETCHING ENDS

    
    //DISTRICT NAME FETCHING STARTS
    public static function get_district_name($id, $state) {
        //DISTRICT NAME FETCHING TRY CATCH BLOCK ENDS
        try {

            $data = DB::table('district_masters')->where('DIST_NO', '=', $id)->where('ST_CODE', '=', $state)->first();
            return $data->DIST_NAME_EN;

        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //DISTRICT NAME FETCHING TRY CATCH BLOCK ENDS
    }
    //DISTRICT NAME FETCHING STARTS
    
    //GENERATE OTP STARTS
    public static function generate_otp()
    {

        $string = '0123456789';
        $string_shuffled = str_shuffle($string);
        $password = substr($string_shuffled, 1, 6);
        return $password;
    }
    //GENERATE OTP ENDS

    //OTP ATTEMPT FUNCTION STARTS
    public static function otp_attempt($userid,$attempt_value)
    {  
      //OTP ATTEMPT FUNCTION TRY CATCH BLOCK STARTS
       try{
          
          DB::table('user_login')
          ->where('id', $userid)
          ->update(array('otp_attempt' => $attempt_value));
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //OTP ATTEMPT FUNCTION TRY CATCH BLOCK ENDS
        
    }
    //OTP ATTEMPT FUNCTION ENDS


    //MESSAGE SEND FUNCTION STARTS
    public static function sendmessage($mobile_number,$message)
    {  
      //MESSAGE SEND TRY CATCH BLOCK STARTS
       try{
        
        $xss = new xssClean;
        // Get user record
        $mobile_number = $xss->clean_input($mobile_number);
        $message       = $xss->clean_input($message);

        $url= 'https://enterprise.smsgupshup.com/GatewayAPI/rest?';
         
        $data = array('method' => 'SendMessage',
                        'send_to' => trim($mobile_number),
                        'msg' => trim($message),
                        'msg_type' => 'TEXT',
                        'userid' => '2000184878',
                        'auth_scheme' => 'plain',
                        'password' => 'Lb66nG',
                        'v' => '1.1',
                        'format' => 'text',);

         // $msg = http_build_query($data);
 
        //  $url .= $msg;
        //  $ch = curl_init();
        //  curl_setopt($ch, CURLOPT_URL, $url);
        //  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //  curl_setopt($ch, CURLOPT_POST, count($data));
          //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       //   $result = curl_exec($ch);
        //  curl_close($ch);
        $fields = '';
        foreach($data as $key => $value) {
           $fields .= $key . '=' . $value . '&';
        }
        rtrim($fields, '&');
        $post = curl_init();
        curl_setopt($post,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_POST, count($data));
        curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($post); //result from mobile seva server
        //dd($result); //output from server displayed
        curl_close($post);

       /* $username="ECISMS-ICT"; //username of the department
        $password="ict@1234567"; //password of the department
        $senderid="ecisms"; //senderid of the deparment
        $deptSecureKey= "93e36092-b1a0-4f0a-9084-4d0eb84f6744"; //departsecure key for encryption of message...

        $url= 'https://msdgweb.mgov.gov.in/esms/sendsmsrequest';

        $encryp_password=sha1(trim($password));
        $key=hash('sha512',trim($username).trim($senderid).trim($message).trim($deptSecureKey));
         
        $data = array(
        "username" => trim($username),
        "password" => trim($encryp_password),
        "senderid" => trim($senderid),
        "content" => trim($message),
        "smsservicetype" =>"otpmsg",
        "mobileno" =>trim($mobileno),
        "key" => trim($key)
        );

        $fields = '';
        foreach($data as $key => $value) {
           $fields .= $key . '=' . $value . '&';
        }
         rtrim($fields, '&');
         $post = curl_init();
        curl_setopt($post,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_POST, count($data));
        curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($post); //result from mobile seva server
        return $result; //output from server displayed
        curl_close($post);*/

         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //MESSAGE SEND TRY CATCH BLOCK ENDS
        
    }
    //CREATED MESSAGE SEND ENDS

    //GET ALL PARTY WITH PARTY TYPE FUNCTION STARTS
    public static function GetAllPartyListWithType(){  
    
    //GET ALL PARTY WITH PARTY TYPE FUNCTION TRY CATCH BLOCK STARTS
       try{

          $data = DB::table('m_party')->where('deleteflag','N')->orderBy('PARTYNAME', 'ASC')->get();
            return ($data);
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //GET ALL PARTY WITH PARTY TYPE FUNCTION TRY CATCH BLOCK ENDS
        
    }
    //GET ALL PARTY WITH PARTY TYPE FUNCTION ENDS
    

    //GET ALL PARTY WITH PARTY TYPE FUNCTION STARTS
    public static function GetAllPartySymbol(){  
    
    //GET ALL PARTY WITH PARTY TYPE FUNCTION TRY CATCH BLOCK STARTS
       try{

          $data = DB::table('m_symbol')->orderBy('SYMBOL_DES', 'ASC')->get();
            return ($data);
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //GET ALL PARTY WITH PARTY TYPE FUNCTION TRY CATCH BLOCK ENDS
        
    }
    //GET ALL PARTY WITH PARTY TYPE FUNCTION ENDS

    //GET ALL ELECTION SCHEDULE FUNCTION STARTS
    public static function GetAllElectionSchedule(){  
    
    //GET ALL ELECTION SCHEDULE FUNCTION TRY CATCH BLOCK STARTS
       try{

         // $data = DB::table('m_schedule')->orderBy('SCHEDULEID', 'ASC')->get();
          $data=DB::table('m_election_details')->groupBy('m_election_details.StatePHASE_NO')->get();
          return ($data);
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //GET ALL ELECTION SCHEDULE TYPE FUNCTION TRY CATCH BLOCK ENDS
        
    }
    //GET ALL ELECTION SCHEDULE TYPE FUNCTION ENDS
    

}