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



class UpPaymentController extends Controller
{    
    public function payment_return_handle(Request $request){
	
	$nidcccc=0;
	
	  $input=$request->All();	

	//dd($input);


	  $dbl_verification = "";
	  $is_double_verification = false;

	if(isset($input['challan_no']) && isset($input['Status'])){
		 try {
			$pay_status = base64_decode($input["Status"]);
			//dd($pay_status);
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Success'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Pending') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='N' || $pay_status=='Failure')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='Abort')) {
					$sttusdd=4;
				}else{	
					$sttusdd="";
				}
			$reff_no = base64_decode($input['challan_no']);
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('*')
			->where('challan_ref_id', '=', $reff_no)
			->get();
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
//dd();
			$pay_status = base64_decode($input["Status"]);
			//$pament_gateway_refrence_no = $input["challan_no"];
			//$dept_id = $input["trans_id"];
			$amount = base64_decode($input["amount"]);
			
			/* if(isset($input['CHALLANTIMESTAMP']) && $input['CHALLANTIMESTAMP']){
				$challan_timestamp = date('Y-m-d H:i:s',strtotime($input['CHALLANTIMESTAMP']));
			}else{
				$challan_timestamp = '';
			} */
			if(isset($input['Bank_date']) && $input['Bank_date']){
				$bank_timestamp = date('Y-m-d H:i:s');
			}else{
				$bank_timestamp = date('Y-m-d H:i:s');
			}
			$paymentconfirmationnumber = ($input["ref_no"]<>'')?base64_decode($input["ref_no"]):$chkd[0]->payment_confirmation_number_cin;
			$bank_code = ($input["Bank_Id"]<>'')?base64_decode($input["Bank_Id"]):$chkd[0]->bank_cd;
			$bank_reff_no = ($input["ref_no"]<>'')?base64_decode($input["ref_no"]):$chkd[0]->bank_transaction_id;
			$partyname = $chkd[0]->deposited_by;
			$prn = '';;
			//$remarks = ($input["REMARKS"]<>'')?$input["REMARKS"]:$chkd[0]->bank_transaction_message;
			//$banktimestamp = $input["BANKTIMESTAMP"];
			$remarks = 'Payment';
			
			if(count($chkd) > 0){
				$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('challan_ref_id', '=', $reff_no)
				->update([ 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'bank_cd'      											=> $bank_code,
				   'deposited_by'      										=> $partyname,
				   'gtn'      												=> $prn,
				   'bank_transaction_message'      							=> $remarks,
				   'payment_confirmation_number_cin'      					=> $paymentconfirmationnumber,
				   'challan_amount'      									=> $amount,
				   //'challan_ref_id_date'      								=> $challan_timestamp,
				   'bank_transtimestamp'      								=> $bank_timestamp,
				   'bank_transaction_status'  		  						=> $sttusdd, 
				   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);		
			    
			$nomdata = DB::connection('mysql')
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
			
			
			
			
			$mob = DB::connection('mysql')
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
			  //return redirect()->back();
			}
			
	} else {
		\Session::put('error', 'Some issue, Please try letter');
		return redirect()->back();
	} 
  } 
 
  
  public function payment_verification(Request $request){ 
  

			$input = $request->all();
			
			$challan_ref_id = $input['challan_ref_id'];
			$amount = $input['amount'];


		$soapUrl = "http://nicupws.up.nic.in/rajkoshdoubleverification.asmx?op=DoubleVerifyParticular";

				$xml_post_string ='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><DoubleVerifyParticular xmlns="http://tempuri.org/"><TransNo>'.$challan_ref_id.'</TransNo><Amount>'.$amount.'</Amount></DoubleVerifyParticular></soap:Body></soap:Envelope>';

				$headers = array(
				"POST /rajkoshdoubleverification.asmx HTTP/1.1",
				"Host: nicupws.up.nic.in",
				"Content-Type: text/xml; charset=utf-8",
				"Content-Length: ".strlen($xml_post_string)
				); 

				$url = $soapUrl;

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

				$data = (json_decode(json_encode($parser))->DoubleVerifyParticularResponse->DoubleVerifyParticularResult);
				 
				 
				$data1 = explode('|',$data);


				$val1 = explode('=',$data1[1]);
				$val2 = explode('=',$data1[0]);

			$pay_status = $val1[1];
			$challan_ref_id = $val2[1];
				
				
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Success'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Pending') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='N' || $pay_status=='Failure')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='Abort')) {
					$sttusdd=4;
				}else{	
					$sttusdd="";
				}

		
		//dd($paymentData->challandata->deptRefNo);
		
		$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('challan_ref_id', '=', $challan_ref_id)
				->update([
				   'bank_transaction_status'  		  						=> $sttusdd, 
				   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);	
		
		
		//dd($myvar);
		
		return Redirect::back();
		
		
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
  
}


