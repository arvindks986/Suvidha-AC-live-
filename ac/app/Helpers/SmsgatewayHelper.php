<?php 
// Code within app\Helpers\Helper.php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class SmsgatewayHelper
{

	public static $username="ECISMS-ICT"; //username of the department
	public static $password="ict@1234567"; //password of the department
	public static $senderid="ecisms"; //senderid of the deparment
	public static $deptSecureKey= "93e36092-b1a0-4f0a-9084-4d0eb84f6744"; //departsecure key for encryption of message...
	
	//Function to send single sms	
	public static function sendSingleSMS($message, $mobileno){
		$encryp_password=sha1(trim(self::$password));
		$key=hash('sha512',trim(self::$username).trim(self::$senderid).trim($message).trim(self::$deptSecureKey));
		  
		 $data = array(
		 "username" => trim(self::$username),
		 "password" => trim($encryp_password),
		 "senderid" => trim(self::$senderid),
		 "content" => trim($message),
		 "smsservicetype" =>"singlemsg",
		 "mobileno" =>trim($mobileno),
		 "key" => trim($key)
		 );
		 //echo "<pre/>"; print_r($data);
		 $response = SmsgatewayHelper::post_to_url("https://msdgweb.mgov.gov.in/esms/sendsmsrequest",$data);
		 // $response = post_to_url("https://msdgweb.mgov.gov.in/esms/sendsmsrequest",$data); //calling post_to_url to send sms
		  return $response;
	 }
	
	//Function to send otpsms
	public static function sendOtpSMS($message, $mobileno){
		//echo 'UNAME->'. self::$username;
		$encryp_password=sha1(trim(self::$password));
		$key=hash('sha512',trim(self::$username).trim(self::$senderid).trim($message).trim(self::$deptSecureKey));
		 
		$data = array(
		"username" => trim(self::$username),
		"password" => trim($encryp_password),
		"senderid" => trim(self::$senderid),
		"content" => trim($message),
		"smsservicetype" =>"otpmsg",
		"mobileno" =>trim($mobileno),
		"key" => trim($key)
		);
		//echo "<pre/>"; print_r($data);die;
		$response = SmsgatewayHelper::post_to_url("https://msdgweb.mgov.gov.in/esms/sendsmsrequest",$data);
		//$response =post_to_url("https://msdgweb.mgov.gov.in/esms/sendsmsrequest",$data); //calling post_to_url to send otpsms
		return $response;
	}

	public static function post_to_url($url, $data) {
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
		curl_close($post);
	}
	
	/*

	public static function gupshup($mobile_number,$message){
		 $url= 'https://enterprise.smsgupshup.com/GatewayAPI/rest?';

		$data = array('method' => 'SendMessage',
					 'send_to' => trim($mobile_number),
					 'msg' => trim($message),
					 'msg_type' => 'TEXT',
					 'userid' => '2000184878',
					 'auth_scheme' => 'plain',
					 'password' => 'pVkyKGef',
					 'v' => '1.1',
					 'format' => 'text',);

		//$msg = http_build_query($data);
        //$url .= $msg;
		//$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_POST, count($data));
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//$result = curl_exec($ch);
		//dd($result);
		//curl_close($ch);
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


}

*/



	public static function gupshup($mobile_number,$message){
		  try {
        
        $base_url = 'https://eciapi.onex-aura.com/api/sms';
        $mobile_number = trim($mobile_number);
        $message = trim($message);
        
        $config_params = array('key' => 'ZxVV9veS', 'to' => $mobile_number, 'from' => 'ECIsms', 'body' => $message);
        $config_data = http_build_query($config_params);
        
        $config_url = $base_url . '?' . $config_data;    
        //dd($config_url);    
        $post = curl_init();
        curl_setopt($post, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($post, CURLOPT_HEADER, false);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($post, CURLOPT_POST, 1);
        curl_setopt($post, CURLOPT_POSTFIELDS, $mobile_number);
        curl_setopt($post, CURLOPT_URL, $config_url);
        $resp = curl_exec($post); //result from call
        curl_close($post);         if ($resp) {
                $data = json_decode($resp);
               /*  if ( trim( $data->status )  == 100 ) {
                $response = array( 'status' => 200, 'message' => 'Success' );
                echo json_encode($response);
                return true;
                } */
            }
          return false;
    } catch (\Throwable $th) {
            echo '<pre>'; print_r($th); echo '</pre>';
            return false;
        }


}

public static function encrypt_data($key,$iv,$data) {
	//return $data;
      $dataR = json_encode($data);
      $encrypted_data = openssl_encrypt($dataR, "aes-128-gcm", $key, $raw_output = false, $iv, $tag);
      return ["data"=>$encrypted_data,"sig"=>base64_encode($tag)];
    }
    public static function decrypt_data($key,$iv,$tag,$data) {
       $decrypted_data = openssl_decrypt($data, "aes-128-gcm", $key, $raw_output = false, $iv, base64_decode($tag));
      $decrypted_data = json_decode($decrypted_data);
      return (array) $decrypted_data;
    }



	

}