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



class HpPaymentController extends Controller
{    
    public function payment_return_handle(Request $request){
	
	

	
	$input=$request->All();
	
	//dd($input);
	
	if(isset($input['encdata'])){
		 try {
			

			$exp = decrypt_himachal($input['encdata']);
	

			$xml_tags = explode("|",$exp);

			
				$my=[];
					foreach($xml_tags as $key=>$val){ 
					$ssss=explode("=", $val);
					
					//dd($ssss);
					
						  if(!empty($ssss[0]) && (!empty($ssss[0]))){	
							$data[$ssss[0]]=$ssss[1];
							array_push($my, $data);
						  }	
				
					}
				$farray = end($my);
			

			$pay_status = $farray["StatusCd"];
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='1'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='2') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='0')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
					$sttusdd=4;
				}else{	
					$sttusdd=3;
				}
			$reff_no = $farray["AppRefNo"];
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('*')
			->where('reff_no', '=', $reff_no)
			->get();
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';

		
			$pament_gateway_refrence_no = $farray["DeptRefNo"];
			$amount = $farray["Amount"];
			
			if(isset($farray['Payment_date']) && $farray['Payment_date']){
				
				
				$dd = substr($farray['Payment_date'], 0, 2);
				$mm = substr($farray['Payment_date'], 2, 2);
				$yy = substr($farray['Payment_date'], 4, 4);
				$time = substr($farray['Payment_date'], 8, 8);
							
				$datetime = $yy.$mm.$dd.$time;

					$challan_timestamp = date("Y-m-d H:i:s", strtotime($datetime));
						
			 
			}else{
				$challan_timestamp = '';
			}
			if(isset($farray['Payment_date']) && $farray['Payment_date']){
								$dd = substr($farray['Payment_date'], 0, 2);
				$mm = substr($farray['Payment_date'], 2, 2);
				$yy = substr($farray['Payment_date'], 4, 4);
				$time = substr($farray['Payment_date'], 8, 8);
							
				$datetime = $yy.$mm.$dd.$time;

					$bank_timestamp = date("Y-m-d H:i:s", strtotime($datetime));
			}else{
				$bank_timestamp = '';
			}
			$paymentconfirmationnumber = ($farray["BankCIN"]<>'')?$farray["BankCIN"]:$chkd[0]->payment_confirmation_number_cin;
			$bank_code = ($farray["BankName"]<>'')?$farray["BankName"]:$chkd[0]->bank_cd;
			$bank_reff_no = ($farray["EchTxnId"]<>'')?$farray["EchTxnId"]:$chkd[0]->bank_transaction_id;
			$partyname = $chkd[0]->deposited_by;
			$prn = '';;
			$remarks = $farray["Status"];
			//$banktimestamp = $input["BANKTIMESTAMP"];
			//$remarks = 'Payment';
			
			if(count($chkd) > 0){
				$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $bank_reff_no, 
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
  
  
  	public function payment_verification_hp(Request $request){
	  
		$data['paydata'] =  DB::connection('mysql')
		->table('payment_details_common')
		->select('*')
		->where('st_code', '=', 'S08')
		//->whereIn('bank_transaction_status', array(1,2,3))
		->get();
		return view('nomination/payment-verification-hp', $data);
	}
  
  
  
  
  public function payment_verification(Request $request){ 
  

			$rd = $request->url;
			
			$postData = [
				'encdata' => $request->encdata
			];
			

			$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => $rd,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS => $postData,
				  CURLOPT_HTTPHEADER => array(
					'Cookie: ASP.NET_SessionId=2nl210aymbosnmafycgrymex'
				  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				//echo $response;
			
			
			$xml_tags = explode("|",$response);

			
				$my=[];
					foreach($xml_tags as $key=>$val){ 
					$ssss=explode("=", $val);
					
					//dd($ssss);
					
						  if(!empty($ssss[0]) && (!empty($ssss[0]))){	
							$data[$ssss[0]]=$ssss[1];
							array_push($my, $data);
						  }	
				
					}
				$farray = end($my);
			
			//dd($farray);
			

			$pay_status = $farray["TXN_STAT"];
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='1'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='2') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='0')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
					$sttusdd=4;
				}else{	
					$sttusdd=3;
				}
		
		
			$remarks = $farray["STAT_DESC"];
			$grn = $farray["HimGrn_no"];
			$bank_reff_no = $farray["Bank_ref_no"];
		
		//dd($paymentData->challandata->deptRefNo);
		
		$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('reff_no', '=', $farray["App_ref_no"])
				->update([
				   'challan_ref_id'      									=> $grn, 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'bank_transaction_message'      							=> $remarks,
				   'bank_transtimestamp'      								=> date('Y-m-d H:i:s', strtotime($farray["Bank_ref_Datetime"])),
				   'bank_transaction_status'  		  						=> $sttusdd, 
				   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);	
		
		
		//dd($myvar);
		
		return Redirect::back();
  
  
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


