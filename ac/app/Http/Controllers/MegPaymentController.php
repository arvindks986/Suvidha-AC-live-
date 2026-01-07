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



class MegPaymentController extends Controller
{    
    public function payment_return_handle(Request $request){
	
	$nidcccc=0;
	
	$input=$request->All();	
	
	
	//dd($input);
	
	
	if(isset($input['GRN']) && isset($input['DEPARTMENT_ID'])){
		 try {
			$pay_status = $input["STATUS"];
			$grn = $input["GRN"];
			$dept_id = $input["DEPARTMENT_ID"];
			$amount = $input["AMOUNT"];
			$office_code = "CEO000";
			$reff_no = $input["DEPARTMENT_ID"];
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Y'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='P') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='N' || $pay_status=='F')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
					$sttusdd=4;
				}else{
					
				$url = 'https://megepayment.gov.in/challan/models/frmgetgrn.php';
				
				$fields = array(
					'DEPARTMENT_ID' => urlencode($dept_id),
					'AMOUNT' => urlencode($amount),
					'OFFICE_CODE' => urlencode($office_code)
					
				);
				$fields_string="";
				foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
				rtrim($fields_string, '&');
				$ch = curl_init();

				curl_setopt($ch,CURLOPT_URL, $url);
				curl_setopt($ch,CURLOPT_POST, count($fields));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
				curl_setopt($ch,CURLOPT_HEADER, FALSE);
				curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
				//curl_setopt($ch,CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

				curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
				curl_setopt($ch, CURLOPT_NOBODY,false);  
				
				// Get the Verification Response
				$get_grn_result = curl_exec($ch);
				if($get_grn_result){
					$res_str = $get_grn_result;
					$res_body = explode("$",$res_str);
					if(count($res_body)>0){
						if($res_body[15]=='STATUS'){
							$pay_status = $res_body[16];
							if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Y'){
								$sttusdd=1;
							} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='P') {
								$sttusdd=2;
							} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='N' || $pay_status=='F')) {
								$sttusdd=3;
							}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
								$sttusdd=4;
							}else{
								
								//Get Cin If Status Again Blank
								
								$url = 'https://megepayment.gov.in/challan/models/frmgetgrn.php';
				
								$fields = array(
									'DEPARTMENT_ID' => urlencode($dept_id),
									'AMOUNT' => urlencode($amount),
									'OFFICE_CODE' => urlencode($office_code),
									'ACTION_CODE' => urlencode('GETCIN'),
									'SUB_SYSTEM' => urlencode('ESUVIDHA')
									
								);
								$fields_string="";
								foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
								rtrim($fields_string, '&');
								$ch = curl_init();

								curl_setopt($ch,CURLOPT_URL, $url);
								curl_setopt($ch,CURLOPT_POST, count($fields));
								curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
								curl_setopt($ch,CURLOPT_HEADER, FALSE);
								curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
								//curl_setopt($ch,CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
								curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

								curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
								curl_setopt($ch, CURLOPT_NOBODY,false);  
								
								// Get the Verification Response
								header('Content-Type: text/html');
								$get_cin_result = curl_exec($ch);
								echo $get_cin_result;die;
								curl_close($ch);
							}

						}
				}
				curl_close($ch);
			}
								
		}
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('id', 'st_code', 'ac_no', 'candidate_id')
			->where('reff_no', '=', $reff_no)
			->get();
			//dd($chkd);
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
			
			
			
			//dd($input);
			
			$pay_status = $input["STATUS"];
			$pament_gateway_refrence_no = $input["GRN"];
			$dept_id = $input["DEPARTMENT_ID"];
			$amount = $input["AMOUNT"];
			$office_code = "CEO000";
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
			
			
			if(count($chkd) > 0){
				$myvar = DB::connection('mysql')->table('payment_details_common')
				//->where('candidate_id', '=', \Auth::id())
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $pament_gateway_refrence_no, 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'bank_cd'      											=> $bank_code,
				   'payment_confirmation_number_cin'      					=> $paymentconfirmationnumber,
				   'challan_amount'      									=> $amount,
				   'bank_transtimestamp'      								=> $pay_date,
				   'challan_url'      										=> $challan_url,
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
	->where('st_code', 'S15')
	->get();
	 //dd($myData);
	$office_code = "CEO000";
	
	$url = 'https://megepayment.gov.in/challan/models/frmgetgrn.php';
	$i=1;
	
	//echo "<br>".count($myData)." -- Value In Table<br>";
	
	if(count($myData) > 0){
	  foreach($myData as $valdaata){  
	  
			//echo "<br>".$valdaata->reff_no."<br>";
			
			if(!empty($valdaata->challan_ref_id)){
			$dept_id = $valdaata->reff_no;
			$amount = $valdaata->challan_amount;

			$fields = array(
					'DEPARTMENT_ID' => urlencode($dept_id),
					'AMOUNT' => urlencode($amount),
					'OFFICE_CODE' => urlencode($office_code)
					
				);
			$fields_string="";
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');
			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch,CURLOPT_HEADER, FALSE);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
			//curl_setopt($ch,CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
			curl_setopt($ch, CURLOPT_NOBODY,false);  
			
			// Get the Verification Response
			$get_grn_result = curl_exec($ch);
			
		echo "<br>";	
		echo "<pre>"; print_r($get_grn_result);
		echo " -- Curl Response<br>";
		if(!empty($get_grn_result) && ($get_grn_result!='Error Occured!')){			
		  try {
			
			$res_str = $get_grn_result;
			$res_body = explode("$",$res_str);
			$pament_gateway_refrence_no = $res_body[4];
			$dept_id = $res_body[2];
			$amount = $res_body[6];
			$office_code = "CEO000";
			$reff_no = $dept_id;
			
			if($res_body[14]){
				$pay_date = date('Y-m-d H:i:s',strtotime($res_body[14]));
			}else{
				$pay_date = '';
			}
			
			$paymentconfirmationnumber = $res_body[10];
			$bank_code = $res_body[7];
			$bank_reff_no = $dept_id;
			$pay_status = $res_body[16];
			//dd($pay_status);
			if(!empty($pay_status) && ($pay_status!='') && $pay_status=='Y'){
				$sttusdd=1;
			} else if(!empty($pay_status) && ($pay_status!='null') && ($pay_status=='P')) {
				$sttusdd=2;
			}
			
			$challan_url = '';
			//dd($res_body);
		echo '<br>'.$pay_status.'<--<br>';
		//echo $status_from_bank; die;			
		if(!empty($pament_gateway_refrence_no) && ($pament_gateway_refrence_no!='null') && (!empty($bank_reff_no))  &&(!empty($pay_date)))	{
			
			$myvar = DB::connection('mysql')->table('payment_details_common')
				//->where('candidate_id', '=', \Auth::id())
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $pament_gateway_refrence_no, 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
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
			 $messageEmail =   "Your payment reference number $reff_no  payment with Aasam Election Commission status changed into success"." ".__('finalize.nom_num')."\n\n ".__('finalize.Thank');
			if(!empty($mob[0]->email)){
				$subject ='Your payment status changed to success from pending';	
				$to_email = $mob[0]->email;
				$body= $messageEmail;
				$body.= "\n ". __('finalize.eci') ;
				$header = "From:ECI Candidate Portal <rti@eci.gov.in>\r\n" ;
				mail($to_email, $subject, $body, $header);
			}
			
			if(!empty($mob[0]->mobile)){
				$message =   "Your payment reference number $reff_no  payment with Aasam Election Commission status changed into success"."\n\n ".__('finalize.Thank');
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
  
  public function payment_verification_cin(Request $request){ 
	$myData = DB::connection('mysql')
	->table('payment_details_common')
	->select('*')
	->where('reff_no', $request->pstcin)
	//->where('st_code', 'S03')
	->get();
	 //dd($myData);
	$office_code = "CEO000";
	
	$url = 'https://megepayment.gov.in/challan/models/frmgetgrn.php';
	$i=1;

	if(count($myData) > 0){
	  foreach($myData as $valdaata){  
	  
			//echo "<br>".$valdaata->reff_no."<br>";
			
			if(!empty($valdaata->challan_ref_id)){
			$dept_id = $valdaata->reff_no;
			$amount = $valdaata->challan_amount;
		
			$fields = array(
				'DEPARTMENT_ID' => urlencode($dept_id),
				'AMOUNT' => urlencode($amount),
				'OFFICE_CODE' => urlencode($office_code),
				'ACTION_CODE' => urlencode('GETCIN'),
				'SUB_SYSTEM' => urlencode('ESUVIDHA')
				
			);
			$fields_string="";
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');
			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch,CURLOPT_HEADER, FALSE);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
			//curl_setopt($ch,CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
			curl_setopt($ch, CURLOPT_NOBODY,false);  
			
			// Get the Verification Response
			header('Content-Type: text/html');
			$get_cin_result = curl_exec($ch);
			echo $get_cin_result;die;
			curl_close($ch);
		$i++;
		}
	}
  }
  }
  
  
      public function payment_verification_meg(Request $request){
	  
		$data['paydata'] =  DB::connection('mysql')
		->table('payment_details_common')
		->select('*')
		->where('st_code', '=', 'S15')
		//->whereIn('bank_transaction_status', array(1,2,3))
		->get();
		return view('nomination/payment-verification-meg', $data);
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


