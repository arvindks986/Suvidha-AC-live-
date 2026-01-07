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



class WbPaymentController extends Controller
{    
    public function payment_return_handle(Request $request){
	
	//$key='1234567890123456';//Demo server
	$key='EhA6g$hsd6Vcds@k';//Live Server
	//$iv='abcdefghijklmnop';//Demo server
	$iv='h6Vfsh42hxhjgdFi';//Live Server
	$enc_method = "aes-128-cbc";		

	$nidcccc=0;
	
	$input=$request->All();	
	if(isset($input['ENCDATA'])){
		 try {
			$ru=$input['ENCDATA'];
			//dd($ru);
			$decrypt_data=wb_payment_decrypt($ru, $enc_method, $key, $iv);		
			
			$xml_tags=$decrypt_data;
			//dd($xml_tags);
			$xml_tags = explode("|",$xml_tags);
			$tags_val = $xml_tags[0];
			$xmlval = simplexml_load_string($tags_val);
			$CHECKSUM = explode("=",$xml_tags[1])[1];
			$DEPT_CD = $xmlval->DEPT_CD;  
			$SERVICE_CD = $xmlval->SERVICE_CD;  
			$reff_no = $xmlval->DEPT_REF_NO;  
			$IDENTIFICATION_NO = $xmlval->IDENTIFICATION_NO;  
			$SERVICE_PROVIDER = $xmlval->SERVICE_PROVIDER;  
			$CHALLANREFID = $xmlval->CHALLANREFID;  
			$CHALLAN_AMOUNT = $xmlval->CHALLAN_AMOUNT;  
			$CHALLANREFID_DATE = $xmlval->CHALLANREFID_DATE;
			if($CHALLANREFID_DATE){
				$dt_body1 = explode(" ",$CHALLANREFID_DATE);
				$pay_dt1 = $dt_body1[0];
				$pay_time1 = $dt_body1[1];
				$dt_format1 = explode("/",$pay_dt1);
				$CHALLANREFID_DATE = $dt_format1[2].'-'.$dt_format1[1].'-'.$dt_format1[0].' '.$pay_time1;
			}
			$DEPOSITED_BY = $xmlval->DEPOSITED_BY;  
			$PAYMENT_MODE = $xmlval->PAYMENT_MODE;  
			$BANKTRANSACTIONID = $xmlval->BANKTRANSACTIONID;  
			$GTN = $xmlval->GTN;  
			$GCD = $xmlval->GCD;  
			$PMC = $xmlval->PMC;  
			$SCHEME = $xmlval->SCHEME;  
			$BANKTRANSACTIONSTATUS = $xmlval->BANKTRANSACTIONSTATUS;  
			$BANKTRANSACTIONMESSAGE = $xmlval->BANKTRANSACTIONMESSAGE;  
			$BANK_CD = $xmlval->BANK_CD;  
			$BANKTRANSTIMESTAMP = $xmlval->BANKTRANSTIMESTAMP;
			if(!empty($BANKTRANSTIMESTAMP) && $BANKTRANSTIMESTAMP<>'' && count($BANKTRANSTIMESTAMP)>0){
				$dt_body2 = explode(" ",$BANKTRANSTIMESTAMP);
				if(count($dt_body2)>1){
					$pay_dt2 = $dt_body2[0];
					$pay_time2 = $dt_body2[1];
					$dt_format2 = explode("/",$pay_dt2);
					$BANKTRANSTIMESTAMP = $dt_format2[2].'-'.$dt_format2[1].'-'.$dt_format2[0].' '.$pay_time2;
				}else{
					$BANKTRANSTIMESTAMP = $CHALLANREFID_DATE;
				}
			}else{
				$BANKTRANSTIMESTAMP = $CHALLANREFID_DATE;
			}
			
			$BANK_REF_ID = $xmlval->BANK_REF_ID;  
			
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('id', 'st_code', 'ac_no', 'candidate_id')
			->where('reff_no', '=', $reff_no)
			->get();
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
			
			$bank_status = 0;
			if($BANKTRANSACTIONSTATUS=='S' || $BANKTRANSACTIONSTATUS=='Success'){
				$bank_status = 1;
			}else if($BANKTRANSACTIONSTATUS=='P' || $BANKTRANSACTIONSTATUS=='I'){
				$bank_status = 2;
			}else if($BANKTRANSACTIONSTATUS=='F'){
				$bank_status = 3;
			}
			
			//echo $CHALLANREFID_DATE.'=='.$BANKTRANSTIMESTAMP;die;
			//dd($chkd);
			if(count($chkd) > 0){
				$myvar = DB::connection('mysql')->table('payment_details_common')
				//->where('candidate_id', '=', \Auth::id())
				->where('reff_no', '=', $reff_no)
				->update([
				   'dept_cd'      						=> $DEPT_CD, 
				   'service_cd'      					=> $SERVICE_CD, 
				   'identification_no'      			=> $IDENTIFICATION_NO, 
				   'service_provider'      				=> $SERVICE_PROVIDER, 
				   'challan_ref_id'      				=> $CHALLANREFID, 
				   'challan_amount'      				=> $CHALLAN_AMOUNT, 
				   'challan_ref_id_date'      			=> $CHALLANREFID_DATE, 
				   'deposited_by'      					=> $DEPOSITED_BY, 
				   'payment_mode'      					=> $PAYMENT_MODE, 
				   'bank_transaction_id'      			=> $BANKTRANSACTIONID,
				   'gtn'      							=> $GTN,
				   'gcd'      							=> $GCD,
				   'pmc'      							=> $PMC,
				   'scheme'      						=> $SCHEME,
				   'bank_transaction_status'      		=> $bank_status, 
				   'status_from_bank_status_code'      	=> $BANKTRANSACTIONSTATUS, 
				   'bank_transaction_message'      		=> $BANKTRANSACTIONMESSAGE,
				   'bank_cd'      						=> $BANK_CD,
				   'bank_transtimestamp'      			=> $BANKTRANSTIMESTAMP,
				   'bank_ref_id'      					=> $BANK_REF_ID,
				   'checksum'      						=> $CHECKSUM,
				   'updated_at'  		  				=> date('Y-m-d H:i:s', time()), 
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
			
			if(count($mob) > 0){
					
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
  
  
	  
	$myData = DB::connection('mysql')
	->table('payment_details_common')
	->select('*')
	->where('bank_transaction_status','<>',1)
	->where('st_code', 'S25')
	->get();

	$rd='https://wbifms.gov.in/GRIPS/ecepay.do';	
	$key='EhA6g$hsd6Vcds@k';//Live Server
	$iv='h6Vfsh42hxhjgdFi';//Live Server
	$enc_method = "aes-128-cbc";
	
	$i=1;
	
	echo "<br>".count($myData)." -- Value In Table<br>";
	
	if(count($myData) > 0){
	  foreach($myData as $valdaata){  
	  
			echo "<br>".$valdaata->reff_no."<br>";
			
			$xml_encdata = "<GRIPS_DEPT_DV_REQ><DEPT_CD>093</DEPT_CD><SERVICE_CD>401</SERVICE_CD><DEPT_REF_NO>".$valdaata->reff_no."</DEPT_REF_NO><IDENTIFICATION_NO>".$valdaata->identification_no."</IDENTIFICATION_NO><FIN_YEAR>2020</FIN_YEAR></GRIPS_DEPT_DV_REQ>";
			
			$wb_checksum = wb_hash256($xml_encdata);
			$xml_encdata .= '|CHECKSUM='.$wb_checksum;
			$encdata=wb_payment_encrypt($xml_encdata, $enc_method, $key, $iv);
			
			///////////****************************///////////////////////////
			$dept_cd='093';
		
			$postData = array(
			"ENCDATA" => $encdata,
			"DEPT_CD" => $dept_cd
			);
			
			$handler = curl_init();
			curl_setopt($handler, CURLOPT_URL, $rd);
			curl_setopt($handler, CURLOPT_POSTFIELDS, http_build_query($postData));		
			curl_setopt($handler, CURLOPT_POST, true);
			curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
			$response_data = curl_exec($handler);
			
			echo "<br>";	
		//echo "<pre>"; print_r($response_data);
		echo " -- Curl Response<br>";
		if(!empty($response_data) && ($response_data!='Occured!')){			
			
			$ru=$response_data;
			$decrypt_data=wb_payment_decrypt($ru, $enc_method, $key, $iv);	
			//dd($ru);
			
			$xml_tags=$decrypt_data;
			$xml_tags = explode("|",$xml_tags);
			$tags_val = $xml_tags[0];
			$xmlval = simplexml_load_string($tags_val);
			$checksum = explode("=",$xml_tags[1])[1];
			$DEPT_CD = $xmlval->DEPT_CD;  
			$SERVICE_CD = $xmlval->SERVICE_CD;  
			$reff_no = $xmlval->DEPT_REF_NO;  
			$IDENTIFICATION_NO = $xmlval->IDENTIFICATION_NO;  
			$SERVICE_PROVIDER = $xmlval->SERVICE_PROVIDER;  
			$CHALLANREFID = $xmlval->CHALLANREFID;  
			$CHALLAN_AMOUNT = $xmlval->CHALLAN_AMOUNT;  
			$CHALLANREFID_DATE = $xmlval->CHALLANREFID_DATE; 
			if($CHALLANREFID_DATE){
				$dt_body1 = explode(" ",$CHALLANREFID_DATE);
				$pay_dt1 = $dt_body1[0];
				$pay_time1 = $dt_body1[1];
				$dt_format1 = explode("/",$pay_dt1);
				$CHALLANREFID_DATE = $dt_format1[2].'-'.$dt_format1[1].'-'.$dt_format1[0].' '.$pay_time1;
			}
			$DEPOSITED_BY = $xmlval->DEPOSITED_BY;  
			$PAYMENT_MODE = $xmlval->PAYMENT_MODE;  
			$BANKTRANSACTIONID = $xmlval->BANKTRANSACTIONID;  
			$GTN = $xmlval->GTN;  
			$GCD = $xmlval->GCD;  
			$PMC = $xmlval->PMC;  
			$SCHEME = $xmlval->SCHEME;  
			$BANKTRANSACTIONSTATUS = $xmlval->BANKTRANSACTIONSTATUS;  
			$BANKTRANSACTIONMESSAGE = $xmlval->BANKTRANSACTIONMESSAGE;  
			$BANK_CD = $xmlval->BANK_CD;  
			$BANKTRANSTIMESTAMP = $xmlval->BANKTRANSTIMESTAMP;
			if($BANKTRANSTIMESTAMP){
				$dt_body2 = explode(" ",$BANKTRANSTIMESTAMP);
				$pay_dt2 = $dt_body2[0];
				$pay_time2 = $dt_body2[1];
				$dt_format2 = explode("/",$pay_dt2);
				$BANKTRANSTIMESTAMP = $dt_format2[2].'-'.$dt_format2[1].'-'.$dt_format2[0].' '.$pay_time2;
			}
			$BANK_REF_ID = $xmlval->BANK_REF_ID; 
		
		echo "<br>";	
		echo "<pre>".$i.'-'; print_r($exp);
		echo " -- After Decrypt Token<br>";
		$bank_status = 0;
		if($BANKTRANSACTIONSTATUS=='S' || $BANKTRANSACTIONSTATUS=='Success'){
			$bank_status = 1;
		}else if($BANKTRANSACTIONSTATUS=='P'){
			$bank_status = 2;
		}
		
		 //////////////////////////////
		 
		  try {		
			if(!empty($reff_no) && ($reff_no!='null') && (($BANKTRANSACTIONSTATUS=='Success') or ($BANKTRANSACTIONSTATUS=='success') or ($BANKTRANSACTIONSTATUS=='S')) && (!empty($BANKTRANSACTIONMESSAGE)) && (!empty($BANK_REF_ID))  &&(!empty($BANKTRANSACTIONID)) &&(!empty($CHALLANREFID_DATE)))	{
				
				$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([
				   'dept_cd'      						=> $DEPT_CD, 
				   'service_cd'      					=> $SERVICE_CD, 
				   'identification_no'      			=> $IDENTIFICATION_NO, 
				   'service_provider'      				=> $SERVICE_PROVIDER, 
				   'challan_ref_id'      				=> $CHALLANREFID, 
				   'challan_amount'      				=> $CHALLAN_AMOUNT, 
				   'challan_ref_id_date'      			=> $CHALLANREFID_DATE, 
				   'deposited_by'      					=> $DEPOSITED_BY, 
				   'payment_mode'      					=> $PAYMENT_MODE, 
				   'bank_transaction_id'      			=> $BANKTRANSACTIONID,
				   'gtn'      							=> $GTN,
				   'gcd'      							=> $GCD,
				   'pmc'      							=> $PMC,
				   'scheme'      						=> $SCHEME,
				   'bank_transaction_status'      		=> $bank_status, 
				   'status_from_bank_status_code'      	=> $BANKTRANSACTIONSTATUS,
				   'bank_transaction_message'      		=> $BANKTRANSACTIONMESSAGE,
				   'bank_cd'      						=> $BANK_CD,
				   'bank_transtimestamp'      			=> $BANKTRANSTIMESTAMP,
				   'bank_ref_id'      					=> $BANK_REF_ID,
				   'checksum'      						=> $checksum, 
				   'updated_at'  		  				=> date('Y-m-d H:i:s', time()), 
				]);	
			
			
		
			$mob = DB::connection('mysql')
			->table('profile')
			->select('name', 'mobile', 'email')
			->where('candidate_id', '=', $valdaata->candidate_id)
			->get();
			
			
			if(count($mob) > 0 ){
			 $messageEmail =   "Your payment reference number $reff_no  payment with West Bengal Election Commission status changed into success"." ".__('finalize.nom_num')."\n\n ".__('finalize.Thank');
			if(!empty($mob[0]->email)){
				$subject ='Your payment status changed to success from pending';	
				$to_email = $mob[0]->email;
				$body= $messageEmail;
				$body.= "\n ". __('finalize.eci') ;
				$header = "From:ECI Candidate Portal <rti@eci.gov.in>\r\n" ;
				mail($to_email, $subject, $body, $header);
			}
			
			if(!empty($mob[0]->mobile)){
				$message =   "Your payment reference number $reff_no  payment with West Bengal Election Commission status changed into success"."\n\n ".__('finalize.Thank');
				SmsgatewayHelper::gupshup($mob[0]->mobile, $message);
			}
			
		
			  
			 }
			}  
			
			if($BANKTRANSACTIONSTATUS=='F' or $BANKTRANSACTIONSTATUS=='Fail'){
				$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([
				'bank_transaction_status'  		  						=> 3, 
				'status_from_bank_status_code'  		  				=> $BANKTRANSACTIONSTATUS, 
				'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);	
			}
			
			} catch (\Exception $e) {
			  print_r( $e->getMessage());
			  
			}
		 
		 
		 //////////////////////////////
		 	
		
		}
		$i++;
		}
	}
  }
  
    public function getState($st){
	return DB::connection('mysql')
	->table('m_state')
	->select('ST_NAME')
	->where('ST_CODE', '=', $st)
	->value('ST_NAME');
  }

  public function getAcName($st, $ac){ 
	return DB::connection('mysql')
	->table('m_ac')
	->select('AC_NAME')
	->where('ST_CODE', '=', $st)
	->where('AC_NO', '=', $ac)
	->value('AC_NAME');
  } 
  
   public function getDistNo($st, $ac){
	return DB::connection('mysql')
	->table('m_ac')
	->select('DIST_NO_HDQTR')
	->where('ST_CODE', '=', $st)
	->where('AC_NO', '=', $ac)
	->value('DIST_NO_HDQTR');
  }
  
   public function sendSMS($mob,$message){
    SmsgatewayHelper::gupshup($mob,$message);	
  }
  
  public function sendEmail($email, $message, $subject){ 
	
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


