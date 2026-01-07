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



class PunPaymentController extends Controller
{    
    public function payment_return_handle(Request $request){
	
		$nidcccc=0;
		$input=$request->All();
		
		//dd($input);
		
		if(isset($input['encData'])){
			if(($input['statusCode'] == 'EC301')  || ($input['statusCode'] == 'EC303')  || ($input['statusCode'] == 'EC304') || ($input['statusCode'] == 'EC305') || ($input['statusCode'] == 'EC306')  || ($input['statusCode'] == 'EC307')){				
				return Redirect::back()->withErrors(['msg' => 'Something went wrong please try again.']);				
			}
		}else{
			return Redirect::back()->withErrors(['msg' => 'Something went wrong please try again.']);
		}
		
		
		$method = "AES-128-CBC";
		
		//demo
		//$key = 'UATAxMgkyV3majMS';
		//$iv = 'UATAxMgkyV3majMS';

		//live
		$key = 'UxFA1OG4Qx8xOhtC';
		$iv = 'UxFA1OG4Qx8xOhtC';
	

		$decrypt_data=wb_payment_decrypt($input['encData'], $method, $key, $iv);	

		$paymentData = json_decode($decrypt_data);
		
//dd($paymentData);
	  $dbl_verification = "";
	  $is_double_verification = false;

	if(isset($paymentData->challandata) && isset($input['statusCode'])){
		 try {
			$pay_status = $input['statusCode'];
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='SC300'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='EC306') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='N' || $pay_status=='EC302')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
					$sttusdd=4;
				}else{	
					$sttusdd=3;
					//$sttusdd="";
				}
			$reff_no = $paymentData->challandata->deptRefNo;
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('*')
			->where('reff_no', '=', $reff_no)
			->get();
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';

			$pay_status = $input['statusCode'];
			$pament_gateway_refrence_no = $paymentData->challandata->receiptNo;
			$dept_id = $paymentData->challandata->ddoCode;
			$amount = $paymentData->challandata->totalAmt;
			
			if(isset($paymentData->challandata->challanDate) && $paymentData->challandata->challanDate){
				$challan_timestamp = date('Y-m-d H:i:s',strtotime($paymentData->challandata->challanDate));
			}else{
				$challan_timestamp = '';
			}
			if(isset($paymentData->challandata->bank_Res->dateOfPay) && $paymentData->challandata->bank_Res->dateOfPay){
				$bank_timestamp = date('Y-m-d H:i:s',strtotime($paymentData->challandata->bank_Res->dateOfPay));
			}else{
				$bank_timestamp = '';
			}
			$paymentconfirmationnumber = ($paymentData->challandata->bank_Res->CIN<>'')?$paymentData->challandata->bank_Res->CIN:$chkd[0]->payment_confirmation_number_cin;
			$bank_code = ($paymentData->challandata->bank_Res->BankRefNo<>'')?$paymentData->challandata->bank_Res->BankRefNo:$chkd[0]->bank_cd;
			$bank_reff_no = ($paymentData->challandata->bank_Res->BankRefNo<>'')?$paymentData->challandata->bank_Res->BankRefNo:$chkd[0]->bank_transaction_id;
			$partyname = $chkd[0]->deposited_by;
			$prn = '';;
			//$remarks = ($input["REMARKS"]<>'')?$input["REMARKS"]:$chkd[0]->bank_transaction_message;
			//$banktimestamp = $input["BANKTIMESTAMP"];
			$remarks = 'Payment';
			
			if(count($chkd) > 0){
				$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $pament_gateway_refrence_no, 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'bank_cd'      											=> $bank_code,
				   'deposited_by'      										=> $partyname,
				   'gtn'      												=> $prn,
				   'bank_transaction_message'      							=> $remarks,
				   'payment_confirmation_number_cin'      					=> $paymentconfirmationnumber,
				   'challan_amount'      									=> $amount,
				   'challan_ref_id_date'      								=> $challan_timestamp,
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
  

			//dd($request->all());

			$data = array(
			'Content-Type: application/json'
			);
		
			$rd = $request->url;
			$postData = $request->encData;

			$handler = curl_init();
			curl_setopt($handler, CURLOPT_URL, $rd);
			curl_setopt($handler, CURLOPT_POSTFIELDS, $postData);		
			curl_setopt($handler, CURLOPT_HTTPHEADER, $data);		
			curl_setopt($handler, CURLOPT_POST, true);
			curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
			$response_data = curl_exec($handler);
		
			$input = json_decode($response_data);
			
		//	dd($input);

		$method = "AES-128-CBC";
		
		//demo
		//$key = 'UATAxMgkyV3majMS';
		//$iv = 'UATAxMgkyV3majMS';

		//live
		$key = 'UxFA1OG4Qx8xOhtC';
		$iv = 'UxFA1OG4Qx8xOhtC';
	
		//dd($input);

		//dd($input->encData);

		$decrypt_data=wb_payment_decrypt($input->encData, $method, $key, $iv);	

		$paymentData = json_decode($decrypt_data);
		
		
				$pay_status = $input->statusCode;
				
								
				if(isset($pay_status)){
					if($pay_status == 'EC302'){				
						return Redirect::back();				
					}
				}
				
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='SC300'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='EC306') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='N' || $pay_status=='EC302')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
					$sttusdd=4;
				}else{	
					$sttusdd=3;
					//$sttusdd="";
				}
		
		
		//dd($paymentData->challandata->deptRefNo);
		
		$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('reff_no', '=', @$paymentData->challandata->deptRefNo)
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


