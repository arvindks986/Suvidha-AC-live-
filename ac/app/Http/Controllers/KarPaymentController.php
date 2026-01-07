<?php

namespace App\Http\Controllers;
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
use App\Helpers\SmsgatewayHelper;
use App\models\Nomination\PreScrutinyModel;
use App\models\Nomination\ProfileModel;
use App\models\Nomination\ProfilelogModel;
use App\models\Nomination\NominationApplicationModel;
use App\models\Nomination\NomlogModel;
use App\models\Nomination\NominationProposerModel;
use App\models\Nomination\NominationPoliceCaseModel;
use App\models\Common\StateModel;
use App\models\Common\{FileModel, PcModel, AcModel, DistrictModel, PartyModel, SymbolModel, ElectionModel};
use App\Http\Requests\Nomination\NominationRequest;
use App\Http\Requests\Nomination\NominationApplicationRequest;
use App\Http\Requests\Nomination\NominationPart12Request;
use App\Http\Requests\Nomination\NominationPart3Request;
use App\Http\Requests\Nomination\NominationPart3aRequest;

use App\Helpers\AesCipher;

class KarPaymentController extends Controller
{    

    public function payment_return_handle(Request $request){
	

	$nidcccc=0;
	
	$input=$request->All();	
	
	/* $response = ["encdata" => "VhrkkSQ5YXM+ZJ49L439AH75ljnPym7nksOBOmeGE3k1zh3p3I27DBto+fPy4m1HkvtOwIo8jUX5
		qOSC7hWFIpVHUP5g0gbhYzkQ+zFftnHht5NQnRpo7CtiA6sAoj7m04B3DadAknKL5Czg4tOQ45R3
		XxTxfFfUGCAwHMokan0zz665L92+ysnvNsJM+czqtrZyPdzJ3QMq5GwW4jvS3F9V1avEtv1bNn2X
		fFWO0P7DQ0/EAbIf0a2iGUg45DE/oIscG/sTdkCiapYFW9BsegLUAFl/tTfR0GWLsYAo7dI=",
	  "dept_code" => "34E",
	  "urlId" => "https://suvidha.eci.gov.in/suvidhaac/public/payment-return-handle-kar",
	  "connect" => "CLICK HERE"
	]; */


	dd($input);

	$res = $input['encdata'];
	
	
	//dd($res);
		
	//Generate a 128-bit encryption key
	
	$path = storage_path("key/KarnatakaPGSigner.jar");
	$decrypt = shell_exec("java -jar $path decrypt '$res'");
	
	//dd($decrypt);
	
	
	 $arrData = explode("|",$decrypt);
	
		$paymentData = array();
		
		foreach($arrData as $key=>$value){
			
			$raw = explode("=",$value);
			
			$paymentData[$raw[0]] = $raw[1];
			
		}
		
	//dd($paymentData);
	
		if(isset($paymentData['Bank_transaction_no']) && isset($paymentData['Status'])){
		 try {
			$pay_status = $paymentData["Status"];
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='10700066'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='10700092') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='10700068' || $pay_status=='10700098' || $pay_status=='10700103')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
					$sttusdd=4;
				}else{	
					$sttusdd="";
				}
				
			//dd($sttusdd);	

				
			$reff_no = $paymentData["challan_ref_no"];
			$chkd = DB::connection('suivhdalivetest')
			->table('payment_details_common')
			->select('*')
			->where('reff_no', '=', $reff_no)
			->get();
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
//dd();
			$pay_status = $paymentData["Status"];
			$pament_gateway_refrence_no = $paymentData["challan_ref_no"];
			$dept_id = $paymentData["challan_ref_no"];
			

				$challan_timestamp = $paymentData["trsn_timestamp"];

				$bank_timestamp = $paymentData["trsn_timestamp"];

			$paymentconfirmationnumber = ($paymentData["challan_ref_no"]<>'')?$paymentData["challan_ref_no"]:$chkd[0]->payment_confirmation_number_cin;
			$bank_code = $paymentData["Bank_name"];
			$bank_reff_no = ($paymentData["Bank_transaction_no"]<>'')?$paymentData["Bank_transaction_no"]:$chkd[0]->bank_transaction_id;
			$partyname = $chkd[0]->deposited_by;
			$prn = '';
			$remarks = 'Payment';
			
			if(count($chkd) > 0){
				$myvar = DB::connection('suivhdalivetest')
			->table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([ 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'bank_cd'      											=> $bank_code,
				   'deposited_by'      										=> $partyname,
				   'gtn'      												=> $prn,
				   'bank_transaction_message'      							=> $remarks,
				   'payment_confirmation_number_cin'      					=> $paymentconfirmationnumber,
				   'challan_ref_id_date'      								=> $challan_timestamp,
				   'bank_transtimestamp'      								=> $bank_timestamp,
				   'bank_transaction_status'  		  						=> $sttusdd, 
				   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);		
	
			$nomdata = DB::connection('suivhdalivetest')
			->table('nomination_application')
			->select('id','nomination_no','candidate_id', 'st_code', 'ac_no')
			->where('st_code', '=', $chkd[0]->st_code)
			->where('ac_no', '=',   $chkd[0]->ac_no)
			->where('candidate_id', '=',   $chkd[0]->candidate_id)
			->get();	
			
			echo "<br>";
			echo "<pre>"; print_r($nomdata).'-- $nomdata value object<br>';
			$mid='';
		   if(count($nomdata) > 0){
			foreach($nomdata as $getid){
				$mid.=$getid->id.',';
			}

			   
		    $nidcccc = substr($mid, 0, -1);  
			echo "<br>".$nidcccc."-1<br>";
			
			$nomination_no = $nomdata[0]->nomination_no;
			$candidate_id = $nomdata[0]->candidate_id; 
			$s = $nomdata[0]->st_code; 
			$a = $nomdata[0]->ac_no; 
			
			
			
			
			$mob = DB::connection('suivhdalivetest')
			->table('profile')
			->select('name', 'mobile', 'email')
			->where('candidate_id', '=', $candidate_id)
			->get();
			$state = $this->getState($s); 
			$ac    = $this->getAcName($s, $a); 
			
			if(count($mob) > 0 && $sttusdd ==1){
					if(!empty($mob[0]->email)){
					echo $messageEmail =  __('finalize.Dear') . " " .$mob[0]->name. ",\n\n  ". __('finalize.your_onlinie') ."   ".__('finalize.has_been_success')." ". date('d-m-Y') . " ".__('finalize.for_online')." ".$state .', '. $ac ." " . __('finalize.track') ."\n\n ".__('finalize.Thank');		
					 echo "<br>";
					$subject =  __('finalize.subject');			   
					$this->sendEmail($mob[0]->email, $messageEmail, $subject);	
					}
					
					echo "<br>";
				  if(!empty($mob[0]->mobile)){	
					 echo $message =   __('finalize.Dear') . " " .$mob[0]->name. " ". __('finalize.your_onlinie') ."   ". __('finalize.has_been_success') ." ". date('d-m-Y') . " ".__('finalize.for_online')." ".$state .', '. $ac . __('finalize.track');				
					 echo "<br>";
					 $this->sendSMS($mob[0]->mobile, $message); 
				  }
			 }
			
			
			}
	       }
			  Session::flash('is_payment',"yes");
			  echo "<br>".$nidcccc."<br>";
			  return redirect('nomination/prev?query='.encrypt_string($nidcccc).'&id='.$nidcccc.'&data='.encrypt_string($nidcccc));
			  
			} catch (\Exception $e) {
			  return  $e->getMessage();
			  \Session::put('error',$e->getMessage());
			  return redirect()->back();
			}
			
	} else {
		\Session::put('error', 'Some issue, Please try letter');
		return redirect()->back();
	} 
  } 
  
  public function payment_verification(Request $request){  
	$input = $request->all();
			
	// UAT 
	 $url = 'http://164.100.127.83/egras/grn_status.asmx';
			
	//Live
	$url = 'https://www.egras.tripura.gov.in/grn_status.asmx';

$Deptcode='ELE';
$Applicationnumber=$input['Applicationnumber'];

//UAT

/* 	$xml_post_string ='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><GetGrnDetails_identity xmlns="http://tempuri.org/"><identity>'.$Applicationnumber.'</identity><dept>'.$Deptcode.'</dept></GetGrnDetails_identity></soap:Body></soap:Envelope>';

$headers = array(
"POST /grn_status.asmx HTTP/1.1",
"Host: 164.100.127.83",
"Content-Type: text/xml; charset=utf-8",
"Content-Length: ".strlen($xml_post_string)
);  */

//Live

	$xml_post_string ='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><GetGrnDetails_identity xmlns="http://tempuri.org/"><identity>'.$Applicationnumber.'</identity><dept>'.$Deptcode.'</dept></GetGrnDetails_identity></soap:Body></soap:Envelope>';

$headers = array(
"POST /grn_status.asmx HTTP/1.1",
"Host: www.egras.tripura.gov.in",
"Content-Type: text/xml; charset=utf-8",
"Content-Length: ".strlen($xml_post_string)
); 

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				$response = curl_exec($ch); 
				curl_close($ch);
				
				$response1 = str_replace("<soap:Body>","",$response);
				$response2 = str_replace("</soap:Body>","",$response1);

				$parser = simplexml_load_string($response2);
				
				$data = (json_decode(json_encode($parser))->GetGrnDetails_identityResponse->GetGrnDetails_identityResult);
				$data1 = json_decode($data);
		
		//dd($response);
		
		
		 if(!empty($data1[0])){			
		  try {
			
			$pay_status = $data1[0]->Status;
			$grn = $data1[0]->GRN;
			$cin = $data1[0]->CIN;
			$reff_no = $Applicationnumber;
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Success'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='P') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='Not found' || $pay_status=='Fail')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
					$sttusdd=4;
				}
				
			//	dd($pay_status);
				
				
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('id', 'st_code', 'ac_no', 'candidate_id')
			->where('reff_no', '=', $reff_no)
			->get();
			//dd($chkd);
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
						
			//dd($input);

			if($data1[0]->BankdateTime){
				$pay_date = date('Y-m-d H:i:s',strtotime($data1[0]->BankdateTime));
			}else{
				$pay_date = '';
			}
		
			
			$challan_url = "";
			
			
			if(count($chkd) > 0){
				$myvar = DB::table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $grn, 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $cin,
				   'payment_confirmation_number_cin'      					=> $cin,
				   'bank_transtimestamp'      								=> $pay_date,
				   'bank_transaction_status'  		  						=> $sttusdd,
				   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);	
			}
			
			return Redirect::back();
			
			} catch (\Exception $e) {
			  print_r( $e->getMessage());
			  
			}	 
		 //////////////////////////////

  } 
  }
  
    public function payment_verification_kar(Request $request){
	  
		$data['paydata'] =  DB::connection('suivhdalivetest')
		->table('payment_details_common')
		->select('*')
		->where('st_code', '=', 'S10')
		//->whereIn('bank_transaction_status', array(1,2,3))
		->get();
		return view('nomination/payment-verification-kar', $data);
	}

  
    function getState($st){
	return DB::connection('mysql')
	->table('m_state')
	->select('ST_NAME')
	->where('ST_CODE', '=', $st)
	->value('ST_NAME');
  }

  function getAcName($st, $ac){ 
	return DB::connection('mysql')
	->table('m_ac')
	->select('AC_NAME')
	->where('ST_CODE', '=', $st)
	->where('AC_NO', '=', $ac)
	->value('AC_NAME');
  } 
  
   function getDistNo($st, $ac){
	return DB::connection('mysql')
	->table('m_ac')
	->select('DIST_NO_HDQTR')
	->where('ST_CODE', '=', $st)
	->where('AC_NO', '=', $ac)
	->value('DIST_NO_HDQTR');
  }
  
   function sendSMS($mob,$message){
    SmsgatewayHelper::gupshup($mob,$message);	
  }
  
  function sendEmail($email, $message, $subject){ 
	//$project = $_SERVER['HTTP_HOST'];
    //$link = 'http://'.$_SERVER['HTTP_HOST'].'/suvidhaac/public/'.$pathm; 	
	$to_email = $email;
	$body= $message;
	$body.= "\n ". __('finalize.eci') ;
	$header = "From:ECI Candidate Portal <rti@eci.gov.in>\r\n" ;
	//$header.= "MIME-Version: 1.0\r\n";
	mail($to_email, $subject, $body, $header);	
  }
  
  
  
  
  
  
  
  
  
  private static function fixKey($key) {
       
        if (strlen($key) < AesCipher::CIPHER_KEY_LEN) {
            //0 pad to len 16
            return str_pad("$key", AesCipher::CIPHER_KEY_LEN, "0");
        }
       
        if (strlen($key) > AesCipher::CIPHER_KEY_LEN) {
            //truncate to 16 bytes
            return substr($key, 0, AesCipher::CIPHER_KEY_LEN);
        }

        return $key;
    }

  
    static function encrypt($key, $iv, $data) {

        $encodedEncryptedData = @base64_encode(openssl_encrypt($data, AesCipher::OPENSSL_CIPHER_NAME, AesCipher::fixKey($key), OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        $encryptedPayload = $encodedEncryptedData.":".$encodedIV;

        return $encryptedPayload;
    }


    static function decrypt($key, $data) {

        $parts = explode(':', $data); //Separate Encrypted data from iv.
        $encrypted = $parts[0];
        $iv = $parts[1];
        $decryptedData = @openssl_decrypt(base64_decode($encrypted), AesCipher::OPENSSL_CIPHER_NAME, AesCipher::fixKey($key), OPENSSL_RAW_DATA, base64_decode($iv));

        return $decryptedData;
    }




 function GenerateURLCode($GRN)
{
  //$UserID = "elesuvid"; UAT
  $UserID = "eleceotr";  //Live
      // $baseUrl = "http://164.100.127.83/egras/GRNStatus.aspx"; // UAT
       $baseUrl = "https://www.egras.tripura.gov.in/GRNStatus.aspx";   // Live
       $plainText = trim($GRN).",".trim($UserID);// echo $plainText.'<br>';
       // Encrypt Text
       $key = $this->GenerateEncryptionKey(trim($GRN));
       $objAES = new AesCipher();
  $byte_array = unpack('C*', $key);
  $count =count($byte_array);
  // padding in  byte if key size is lesser than 16
if($count < 16)
{
for($i=$count+1; $i<=16;$i++)
{
array_push($byte_array,0);
}
}
//var_dump($byte_array);

$chars = array_map("chr", $byte_array);
$keyen = join($chars);
//var_dump($keyen);
$iv = utf8_encode($keyen);

   $encryption = AesCipher::encrypt($keyen ,$iv, $plainText); // output format-  base64_encrypted_code : iv
$encrypt_code = explode(":",$encryption)[0];
        $cipherText = urlencode($encrypt_code);
       // Input Text Creation for QR Code
       $url = $baseUrl . "?val=" . $cipherText . "&key=".$key;
      // echo "URL- ".$url;

//echo "<br> post";
 echo "
            <script>
              window.open('$url','_blank');
            </script>
        "; 

//header("Location: $url");
}


function GenerateEncryptionKey($GRN)
{
$var = $GRN;
$sum = 0;
for($i = 0; $i < mb_strlen($var, 'ASCII'); $i++)
{
$ord = ord($var[$i]);
$sum = $sum + $ord;
}
return $sum;
}
  
}


