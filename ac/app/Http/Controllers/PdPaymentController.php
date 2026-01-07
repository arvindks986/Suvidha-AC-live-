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



class PdPaymentController extends Controller
{    
    public function payment_return_handle(Request $request){
	
	$nidcccc=0;
	
	$input=$request->All();	
	if(isset($input['GRN']) && isset($input['DEPARTMENT_ID'])){
		 try {
			$pay_status = $input["STATUS"];

			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Y'){
					$sttusdd=1;
			} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='P') {
				$sttusdd=2;
			} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='N' || $pay_status=='F')) {
				$sttusdd=3;
			}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
				$sttusdd='';
			}
			
			$reff_no = $input["DEPARTMENT_ID"];
			
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('id', 'st_code', 'ac_no', 'candidate_id')
			->where('reff_no', '=', $reff_no)
			->get();
			//dd($chkd);
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
				
			$amount = $input["AMOUNT"];
			$pay_status = $input["STATUS"];
			$pament_gateway_refrence_no = $input["GRN"];
			$dept_id = $input["DEPARTMENT_ID"];
			$dept_code = $input["DEPT_CODE"];
			$reff_no = $input["DEPARTMENT_ID"];
			if($input['TRANSCOMPLETIONDATETIME']){
				$pay_date = date('Y-m-d H:i:s',strtotime($input['TRANSCOMPLETIONDATETIME']));
			}else{
				$pay_date = '';
			}
			$paymentconfirmationnumber = $input["BANKCIN"];
			$bank_code = $input["BANKCODE"];
			$bank_reff_no = $input["DEPARTMENT_ID"];
			$challan_url = "";
			$checksum = $input["RESPONSE_CHKSUM"];
			
			
			if(count($chkd) > 0){
				$myvar = DB::connection('mysql')->table('payment_details_common')
				//->where('candidate_id', '=', \Auth::id())
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $pament_gateway_refrence_no, 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'dept_cd'      											=> $dept_code,
				   'bank_cd'      											=> $bank_code,
				   'payment_confirmation_number_cin'      					=> $paymentconfirmationnumber,
				   'challan_amount'      									=> $amount,
				   'bank_transtimestamp'      								=> $pay_date,
				   'challan_url'      										=> $challan_url,
				   'checksum'      											=> $checksum,
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
	->where('st_code', 'U07')
	->get();
	$i=1;

	if(count($myData) > 0){
	  foreach($myData as $valdaata){  
			if(!empty($valdaata->challan_ref_id)){
			$dept_id = $valdaata->reff_no;
			$url = 'https://gras.py.gov.in/checkGRNApi.php';
			// Create a new cURL resource
			$ch = curl_init($url);
			// Setup request to send json via POST
			$data = array(
				'grn' => $dept_id
			);
			$payload = json_encode($data);
			// Attach encoded JSON string to the POST fields
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			// Set the content type to application/json
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			// Return response instead of outputting
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Execute the POST request
			$result = curl_exec($ch);
			// Close cURL resource
			curl_close($ch);
			$input = json_decode($result);
		if($input<>''){	
		  try {
			
			$amount = $input[0]->amount;
			$pay_status = $input[0]->status;
			$pament_gateway_refrence_no = $input[0]->grn;
			$dept_id = $input[0]->department_id;
			$dept_code = $input[0]->dept_code;
			$reff_no = $input[0]->department_id;
			if($input[0]->transcompletiondatetime){
				$pay_date = date('Y-m-d H:i:s',strtotime($input[0]->transcompletiondatetime));
			}else{
				$pay_date = '';
			}
			$paymentconfirmationnumber = $input[0]->bankcin;
			$bank_code = $input[0]->bankcode;
			$bank_reff_no = $input[0]->department_id;
			$challan_url = '';
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Y'){
					$sttusdd=1;
			} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='P') {
				$sttusdd=2;
			} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='N' || $pay_status=='F')) {
				$sttusdd=3;
			}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
				$sttusdd='';
			}

		if(!empty($dept_id) && ($dept_id!='null'))	{
			
			$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $pament_gateway_refrence_no, 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'dept_cd'      											=> $dept_code,
				   'bank_cd'      											=> $bank_code,
				   'payment_confirmation_number_cin'      					=> $paymentconfirmationnumber,
				   'challan_amount'      									=> $amount,
				   'bank_transtimestamp'      								=> $pay_date,
				   'challan_url'      										=> $challan_url,
				   'bank_transaction_status'  		  						=> $sttusdd, 
				   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);		
			
			
		
			$mob = DB::connection('mysql')
			->table('profile')
			->select('name', 'mobile', 'email')
			->where('candidate_id', '=', $valdaata->candidate_id)
			->get();
			
			
			if(count($mob) > 0  && $sttusdd ==1){
			 $messageEmail =   "Your payment reference number $reff_no  payment with Puducherry Election Commission status changed into success"." ".__('finalize.nom_num')."\n\n ".__('finalize.Thank');
			if(!empty($mob[0]->email)){
				$subject ='Your payment status changed to success from pending';	
				$to_email = $mob[0]->email;
				$body= $messageEmail;
				$body.= "\n ". __('finalize.eci') ;
				$header = "From:ECI Candidate Portal <rti@eci.gov.in>\r\n" ;
				mail($to_email, $subject, $body, $header);
			}
			
			if(!empty($mob[0]->mobile)){
				$message =   "Your payment reference number $reff_no  payment with Puducherry Election Commission status changed into success"."\n\n ".__('finalize.Thank');
				SmsgatewayHelper::gupshup($mob[0]->mobile, $message);
			}
			
			 }
			}  
			
			if($pay_status=='F' or $pay_status=='N'){
				$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([
				'bank_transaction_status'  		  							=> 3, 
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


