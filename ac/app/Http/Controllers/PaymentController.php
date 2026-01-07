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



class PaymentController extends Controller
{    
	public function checkdata(){
			$name_excel = time();
			$setting_pdf = [
			  'margin_top'        => 40,      
			  'margin_bottom'     => 10,    
			];
			$data['alld'] =  DB::connection('mysql')
			->table('nomination_application')
			->select('*')
			->get();
			 $pdf = \PDF::loadView('checkdata', $data, [], $setting_pdf);
			 return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
	}


    public function payment_return_handle(Request $request){
	
	$key='E(*x5lcyam%$.9dx';
	$iv='E(*x5lcyam%$.9dx';
	$enc_method = "AES-128-CBC";		
	
	$nidcccc=0;
	
	$input=$request->All();	
	if(isset($input['encdata'])){
		 try {
			 $ru=$input['encdata'];
			
			 $decrypt=openssl_decrypt($ru, $enc_method, $key, $options=0, $iv);		
			
		
			
			$exp=explode("|", $decrypt);
		
			$my=[];
			foreach($exp as $key=>$val){ 
			$ssss=explode("=", $val);
				if($ssss[0]!='challan_url'){
				  if(!empty($ssss[0]) && (!empty($ssss[0]))){	
					$data[$ssss[0]]=$ssss[1];
					array_push($my, $data);
				  }	
				} else {
					$challan_url = str_replace("challan_url=", "", $val);
					$data['challan_url']=$challan_url;
					array_push($my, $data);
				}	
			}
			$farray = end($my);
			
			
			
			$reff_no='';
			if(!empty($farray['reff_no'])){
			 $reff_no=$farray['reff_no'];	
			}
			
			
			
			$bank_code='';
			if(!empty($farray['bank_code'])){
			 $bank_code=$farray['bank_code'];	
			}
			
			$pament_gateway_refrence_no='';
			if(!empty($farray['grn'])){
			 $pament_gateway_refrence_no=$farray['grn'];	
			}
			$status_from_bank='';
			if(!empty($farray['status_code'])){
			 $status_from_bank=$farray['status_code'];	
			}
			$status_desc='';
			if(!empty($farray['status_desc'])){
			 $status_desc=$farray['status_desc'];	
			}
			$bank_reff_no='';
			if(!empty($farray['bank_reff_no'])){
			 $bank_reff_no=$farray['bank_reff_no'];	
			}
			$paymentconfirmationnumber='';
			$paymentconfirmationnumber='';
			if(!empty($farray['cin'])){
			 $paymentconfirmationnumber=$farray['cin'];	
			}
			
			
			$sttusdd=0;
			if(!empty($farray['cin']) && ($farray['cin']!='') && ($farray['cin']!=null && ($farray['cin']!='null'))){
			 $sttusdd=1;
			} else {
			 $sttusdd=2;	
			}
			if(!empty($farray['status_code']) && (($farray['status_code']=='Success') or ($farray['status_code']=='success'))){
			 $sttusdd=1;
			}
			
			
			$pay_date='';
			if(!empty($farray['pay_date'])){
			 $pay_date=$farray['pay_date'];	
			}
			$checkSum='';
			if(!empty($farray['checkSum'])){
			 $checkSum=$farray['checkSum'];	
			}
			$challan_url='';
			if(!empty($farray['challan_url'])){
			 $challan_url=$farray['challan_url'];	
			}
			
			
			$chkd = DB::connection('mysql')
			->table('payment_details_bihar')
			->select('id', 'st_code', 'ac_no', 'candidate_id')
			->where('reff_no', '=', $reff_no)
			->get();
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
			
			if(count($chkd) > 0){
				$myvar = DB::connection('mysql')->table('payment_details_bihar')
				//->where('candidate_id', '=', \Auth::id())
				->where('reff_no', '=', $reff_no)
				->update([
				   'pament_gateway_refrence_no_grn'      					=> $pament_gateway_refrence_no, 
				   'status_from_bank_status_code'      						=> $status_from_bank, 
				   'status_desc'      										=> $status_desc,
				   'bank_reff_no'      										=> $bank_reff_no,
				   'bank_code'      										=> $bank_code,
				   'paymentconfirmationnumber_cin'      					=> $paymentconfirmationnumber,
				   'pay_date_time'      									=> $pay_date,
				   'challan_url'      										=> $challan_url,
				   'checkSum'      											=> $checkSum,
				   'status'  		  										=> $sttusdd, 
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
					//$this->sendEmail($mob[0]->email, $messageEmail, $subject);	
					}
					
					echo "<br>";
				  if(!empty($mob[0]->mobile)){	
					 echo $message =   __('finalize.Dear') . " " .$mob[0]->name. " ". __('finalize.your_onlinie') ."   ". __('finalize.has_been_success') ." ". date('d-m-Y') . " ".__('finalize.for_online')." ".$state .', '. $ac . __('finalize.track');				
					 echo "<br>";
					// $this->sendSMS($mob[0]->mobile, $message); 
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
	->table('payment_details_bihar')
	->select('*')
	->whereIn('status', array(2,3))
	->get();
	 
	
	
	$rd='https://e-receipt.bihar.gov.in/brcs/doublepaygateway';	
	
	$key='E(*x5lcyam%$.9dx';
	$iv='E(*x5lcyam%$.9dx';
	$enc_method = "AES-128-CBC";	 
	
	$i=1;
	
	echo "<br>".count($myData)." -- Value In Table<br>";
	
	if(count($myData) > 0){
	  foreach($myData as $valdaata){  
	  
			echo "<br>".$valdaata->reff_no."<br>";
			
			if(!empty($valdaata->pament_gateway_refrence_no_grn)){
			 $vv='reff_no='.trim($valdaata->reff_no).'|'.'grn='.trim($valdaata->pament_gateway_refrence_no_grn);
			 $ttrimst=trim($vv);
			 $checkSum=md5($ttrimst);
			 $url=$ttrimst.'|'.'checkSum='.$checkSum;
			} else {
			 $vv='reff_no='.trim($valdaata->reff_no);
			 $ttrimst=trim($vv);
			 $checkSum=md5($ttrimst);
			 $url=$ttrimst.'|'.'checkSum='.$checkSum;
			}
			$encdata=openssl_encrypt($url, $enc_method, $key, $options=0, $iv);		
			///////////****************************///////////////////////////
			
		
			$postData = array(
			"encdata" => $encdata,
			"merchant_code" => "ELCDEPT",
			"source" => "ograss",
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
		echo "<pre>"; print_r($response_data);
		echo " -- Curl Response<br>";
		if(!empty($response_data) && ($response_data!='Error Occured!')){			
			
		 $res=openssl_decrypt($response_data, $enc_method, $key, $options=0, $iv);		
		 $exp=explode("|", $res);
		//echo "<pre>"; print_r($exp); 
		
		echo "<br>";	
		echo "<pre>".$i.'-'; print_r($exp);
		echo " -- After Decrypt Token<br>";
		
		
		 //////////////////////////////
		 
		  try {
			$my=[];
			foreach($exp as $keykeykey=>$valvalval){ 
			$ssss=explode("=", $valvalval);
				if($ssss[0]!='challan_url'){
				  if(!empty($ssss[0]) && (!empty($ssss[0]))){	
					$data[$ssss[0]]=$ssss[1];
					array_push($my, $data);
				  }	
				} else {
					$challan_url = str_replace("challan_url=", "", $valvalval);
					$data['challan_url']=$challan_url;
					array_push($my, $data);
				}	
			}
			$farray = end($my);
		 
			
			
			$reff_no='';
			if(!empty($farray['reff_no'])){
			 $reff_no=$farray['reff_no'];	
			}
			$bank_code='';
			if(!empty($farray['bank_code'])){
			 $bank_code=$farray['bank_code'];	
			}
			
			$pament_gateway_refrence_no='';
			if(!empty($farray['grn'])){
			 $pament_gateway_refrence_no=$farray['grn'];	
			}
			$status_from_bank='';
			if(!empty($farray['status_code'])){
			 $status_from_bank=$farray['status_code'];	
			}
			$status_desc='';
			if(!empty($farray['status_desc'])){
			 $status_desc=$farray['status_desc'];	
			}
			$bank_reff_no='';
			if(!empty($farray['bank_reff_no'])){
			 $bank_reff_no=$farray['bank_reff_no'];	
			}
			$paymentconfirmationnumber='';
			$paymentconfirmationnumber='';
			if(!empty($farray['cin'])){
			 $paymentconfirmationnumber=$farray['cin'];	
			}
			$sttusdd=0;
			if(!empty($farray['cin']) && ($farray['cin']!='') && ($farray['cin']!=null && ($farray['cin']!='null'))){
			 $sttusdd=1;
			} else {
			 $sttusdd=2;	
			}
			
			if(!empty($farray['status_code']) && (($farray['status_code']=='Success') or ($farray['status_code']=='success'))){
			 $sttusdd=1;
			}
			
			$pay_date='';
			if(!empty($farray['pay_date'])){
			 $pay_date=$farray['pay_date'];	
			}
			$checkSum='';
			if(!empty($farray['checkSum'])){
			 $checkSum=$farray['checkSum'];	
			}
			$challan_url='';
			if(!empty($farray['challan_url'])){
			 $challan_url=$farray['challan_url'];	
			}
			
			
		echo '<br>'.$status_from_bank.'<--<br>';
		//echo $status_from_bank; die;			
		if(!empty($pament_gateway_refrence_no) && ($pament_gateway_refrence_no!='null') && (($status_from_bank=='Success') or ($status_from_bank=='success')) && (!empty($status_desc)) && (!empty($bank_reff_no))  &&(!empty($pay_date)) &&(!empty($challan_url)) &&(!empty($checkSum)))	{
			
			$myvar = DB::connection('mysql')->table('payment_details_bihar')
			->where('reff_no', '=', $reff_no)
			->update([
			   'pament_gateway_refrence_no_grn'      					=> $pament_gateway_refrence_no, 
			   'status_from_bank_status_code'      						=> $status_from_bank, 
			   'status_desc'      										=> $status_desc,
			   'bank_reff_no'      										=> $bank_reff_no,
			   'bank_code'      										=> $bank_code,
			   'paymentconfirmationnumber_cin'      					=> $paymentconfirmationnumber,
			   'pay_date_time'      									=> $pay_date,
			   'challan_url'      										=> $challan_url,
			   'checkSum'      											=> $checkSum,
			   'status'  		  										=> $sttusdd, 
			   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
			]);	
			
			
		
			$mob = DB::connection('mysql')
			->table('profile')
			->select('name', 'mobile', 'email')
			->where('candidate_id', '=', $valdaata->candidate_id)
			->get();
			
			
			if(count($mob) > 0 ){
			 $messageEmail =   "Your payment reference number $reff_no  payment with Bihar Election Commission status changed into success"." ".__('finalize.nom_num')."\n\n ".__('finalize.Thank');
			if(!empty($mob[0]->email)){
				$subject ='Your payment status changed to success from pending';	
				$to_email = $mob[0]->email;
				$body= $messageEmail;
				$body.= "\n ". __('finalize.eci') ;
				$header = "From:ECI Candidate Portal <rti@eci.gov.in>\r\n" ;
				//mail($to_email, $subject, $body, $header);
			}
			
			if(!empty($mob[0]->mobile)){
				$message =   "Your payment reference number $reff_no  payment with Bihar Election Commission status changed into success"."\n\n ".__('finalize.Thank');
				//SmsgatewayHelper::gupshup($mob[0]->mobile, $message);
			}
			
			/*$datasss =  DB::connection('mysql')
			->table('officer_login')
			->select('*')
			->where('st_code', '=', Session::get('st_code'))
			->where('ac_no', '=', Session::get('ac_no'))
			->where('designation', '=', 'ROAC')
			->get();	
				if(count($datasss) > 0){
			      if(isset($datasss[0]->email) && isset($datasss[0]->Phone_no)){
				$rmes =   __('finalize.Dear'). " " .$datasss[0]->officername. ", ".__('finalize.sec_money2'). $get_nominattion_detail['nomination_no']." ".__('finalize.nom_num');$emss =   __('finalize.Dear'). " " .$datasss[0]->officername. ",\n\n ".__('finalize.sec_money2'). $get_nominattion_detail['nomination_no']." ".__('finalize.nom_num')."\n\n ".__('finalize.Thank');
				 $rsub =__('finalize.secure_money');	
				 $rbody= $emss;
				 $rbody.= "\n ". __('finalize.eci') ;
				 $header = "From:ECI Candidate Portal <rti@eci.gov.in>\r\n" ;
				 mail($datasss[0]->email, $rsub, $rbody, $header);
				 SmsgatewayHelper::gupshup($datasss[0]->Phone_no, $rmes);	  
			    }
			  }*/


			  
			 }
			}  
			
			if($status_from_bank=='Invalid' or $status_from_bank=='Failure'){
				$myvar = DB::connection('mysql')->table('payment_details_bihar')
				->where('reff_no', '=', $reff_no)
				->update([
				'status'  		  										=> 4, 
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


