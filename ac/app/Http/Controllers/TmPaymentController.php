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



class TmPaymentController extends Controller
{    
    public function payment_return_handle(Request $request){
	
	$nidcccc=0;
	$key = 'secretKeyIfhrmst';
	$iv = 'secretInitIfhrms';
	
	
	$input=$request->All();	
	
	if(isset($input['enc_data'])){
		 try {
			$ru=$input['enc_data'];
			$decrypt=\App\Helpers\AesCipher::decrypt($key, $iv, $ru);		
			$exp=explode("|", $decrypt);
			echo "<pre>";print_r($exp);

			$pay_status = "";
			$pay_status_cl = $exp[1];
			if(!empty($pay_status_cl)){
				$pay_status_bd = explode("=",$pay_status_cl);
				if(count($pay_status_bd)>0){
					$pay_status = $pay_status_bd[1];
				}
			}
			
			$amount = "";
			$amount_cl = $exp[5];
			if(!empty($amount_cl)){
				$amount_bd = explode("=",$amount_cl);
				if(count($amount_bd)>0){
					$amount = $amount_bd[1];
				}
			}
			
			$pay_date = "";
			$monthArr=array("Jan"=>"1","Feb"=>"2","Mar"=>"3","Apr"=>"4","May"=>"5","Jun"=>"6","Jul"=>"7","Aug"=>"8","Sep"=>"9","Oct"=>"10","Nov"=>"11","Dec"=>"12");
			$pay_date_cl = $exp[6];
			if(!empty($pay_date_cl)){
				$pay_date_bd = explode("=",$pay_date_cl);
				if(count($pay_date_bd)>0){
					$pay_date = $pay_date_bd[1];
					if(!empty($pay_date)){
						$dt_bd = explode(" ",$pay_date);
						$ndt_bd = explode("/",$dt_bd[0]);
						$month_num = $monthArr[$ndt_bd[1]];
						$month_num = ($month_num<10)?'0'.$month_num:$month_num;
						$pay_date = $ndt_bd[2].'-'.$month_num.'-'.$ndt_bd[0].' '.$dt_bd[1];
					}
				}
			}
			
			$pay_mode = "";
			$pay_mode_cl = $exp[7];
			if(!empty($pay_mode_cl)){
				$pay_mode_bd = explode("=",$pay_mode_cl);
				if(count($pay_mode_bd)>0){
					$pay_mode = $pay_mode_bd[1];
				}
			}
			
			
			$bank_code = "";
			$bank_code_cl = $exp[4];
			if(!empty($bank_code_cl)){
				$bank_code_bd = explode("=",$bank_code_cl);
				if(count($bank_code_bd)>0){
					$bank_code = $bank_code_bd[1];
				}
			}
			
			$bank_reff_no = "";
			$bank_reff_no_cl = $exp[2];
			if(!empty($bank_reff_no_cl)){
				$bank_reff_no_bd = explode("=",$bank_reff_no_cl);
				if(count($bank_reff_no_bd)>0){
					$bank_reff_no = $bank_reff_no_bd[1];
				}
			}
			
			$pament_gateway_refrence_no = "";
			$pament_gateway_refrence_no_cl = $exp[0];
			if(!empty($pament_gateway_refrence_no_cl)){
				$pament_gateway_refrence_no_bd = explode("=",$pament_gateway_refrence_no_cl);
				if(count($pament_gateway_refrence_no_bd)>0){
					$pament_gateway_refrence_no = $pament_gateway_refrence_no_bd[1];
				}
			}
			
			$reff_no = "";
			$reff_no_cl = $exp[3];
			if(!empty($reff_no_cl)){
				$pay_status_bd = explode("=",$reff_no_cl);
				if(count($pay_status_bd)>0){
					$reff_no = $pay_status_bd[1];
				}
			}
			
			$sttusdd = "";
			if(!empty($pay_status) && !empty($pay_status_cl) && $pay_status=='Success'){
				$sttusdd = 1;
			}else if(!empty($pay_status) && !empty($pay_status_cl) && $pay_status=='Pending'){
				$sttusdd = 2;
			}else if(!empty($pay_status) && !empty($pay_status_cl) && $pay_status=='Failure'){
				$sttusdd = 3;
			}
			
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('id', 'st_code', 'ac_no', 'candidate_id')
			->where('reff_no', '=', $reff_no)
			->get();
			//dd($chkd);
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
			
			
			if(count($chkd) > 0){
				$myvar = DB::connection('mysql')->table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $pament_gateway_refrence_no, 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'bank_cd'      											=> $bank_code,
				   'challan_amount'      									=> $amount,
				   'bank_transtimestamp'      								=> $pay_date,
				   'payment_mode'      										=> $pay_mode,
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
			  //dd($e->getMessage());
			  //return redirect()->back();
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
	->where('st_code', 'S22')
	->get();

	$i=1;

	echo "<br>".count($myData)." -- Value In Table<br>";
	$hod = "";
	$key = 'secretKeyIfhrmst';
	$iv = 'secretInitIfhrms';
	$challan_no = '';
	if(count($myData) > 0){
		
	  $monthArr=array("Jan"=>"1","Feb"=>"2","Mar"=>"3","Apr"=>"4","May"=>"5","Jun"=>"6","Jul"=>"7","Aug"=>"8","Sep"=>"9","Oct"=>"10","Nov"=>"11","Dec"=>"12");
	  foreach($myData as $valdaata){  
			$nom_dist_no = "";
			$get_dist_byac = DB::connection('mysql')->table('m_ac')->select('DIST_NO_HDQTR')->where('AC_NO', '=', $valdaata->ac_no)->first();
			if(!empty($get_dist_byac)){
				$nom_dist_no = $get_dist_byac->DIST_NO_HDQTR;
			}
			
			if(!empty($nom_dist_no)){
				$payment_office_code =  DB::connection('mysql')
							->table('payment_tamilnadu_dist_mapping')
							->select('*')
							->where('dist_no', '=', $nom_dist_no)//6
							->get();
				if(count($payment_office_code)>0){
					$hod = $payment_office_code[0]->hod;
				}else{
					$hod = "";
				}
			}else{
				$hod = "";
			}

			if(!empty($valdaata->challan_ref_id)){
			$dept_id = $valdaata->reff_no;
			$reff_no = $valdaata->reff_no;
			
			$code_str = 'dept_tran_ref_no='.$dept_id.'|hod='.$hod;
			$encdata=\App\Helpers\AesCipher::encrypt($key, $iv, $code_str);
			$url =  'https://www.karuvoolam.tn.gov.in/challan/deptchallan/api/'.$encdata;

			// Create a new cURL resource
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_SSL_VERIFYPEER =>false,
			));

			$response = curl_exec($curl);
			curl_close($curl);
		if(!empty($response) && $response != false){
		  try {
			$response = json_decode($response);
			$challan_no = $response->challanno;
			$pament_gateway_refrence_no = $response->challanno;
			$pay_status = $response->paymentsatus;
			$bank_code = $response->gateway;
			$bank_reff_no = $response->bankref;
			$amount = $response->totalamt;
			$pay_dt = $response->challanDate;
			if(!empty($pay_dt)){
				$ndt_bd = explode("-",$pay_dt);
				$month_num = $monthArr[$ndt_bd[1]];
				$month_num = ($month_num<10)?'0'.$month_num:$month_num;
				$pay_date = $ndt_bd[2].'-'.$month_num.'-'.$ndt_bd[0];
			}else{
				$pay_date = '';
			}
			$sttusdd = "";
			if(!empty($pay_status) &&  $pay_status=='Success'){
				$sttusdd = 1;
			}else if(!empty($pay_status)  && $pay_status=='Pending'){
				$sttusdd = 2;
			}else if(!empty($pay_status)  && $pay_status=='Failure'){
				$sttusdd = 3;
			}
			//echo $pament_gateway_refrence_no.'=='.$pay_status.'=='.$sttusdd; die;
		if(!empty($dept_id) && ($pay_status!='null') && $pament_gateway_refrence_no<>'')	{
			
			$myvar = DB::connection('mysql')->table('payment_details_common')
				//->where('candidate_id', '=', \Auth::id())
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $pament_gateway_refrence_no, 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_cd'      											=> $bank_code,
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'challan_amount'      									=> $amount,
				   'bank_transtimestamp'      								=> $pay_date,
				   'bank_transaction_status'  		  						=> $sttusdd, 
				   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);		
			
			
		
			$mob = DB::connection('mysql')
			->table('profile')
			->select('name', 'mobile', 'email')
			->where('candidate_id', '=', $valdaata->candidate_id)
			->get();
			
			
			if(count($mob) > 0  && $sttusdd ==1){
			 $messageEmail =   "Your payment reference number $reff_no  payment with Tamilnadu Election Commission status changed into success"." ".__('finalize.nom_num')."\n\n ".__('finalize.Thank');
			if(!empty($mob[0]->email)){
				$subject ='Your payment status changed to success from pending';	
				$to_email = $mob[0]->email;
				$body= $messageEmail;
				$body.= "\n ". __('finalize.eci') ;
				$header = "From:ECI Candidate Portal <rti@eci.gov.in>\r\n" ;
				mail($to_email, $subject, $body, $header);
			}
			
			if(!empty($mob[0]->mobile)){
				$message =   "Your payment reference number $reff_no  payment with Tamilnadu Election Commission status changed into success"."\n\n ".__('finalize.Thank');
				SmsgatewayHelper::gupshup($mob[0]->mobile, $message);
			}
			
			 }
			}  
			
			if($pay_status=='Failure' or $pay_status=='F'){
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


