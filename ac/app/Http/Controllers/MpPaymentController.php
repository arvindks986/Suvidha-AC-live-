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



class MpPaymentController extends Controller
{    
    public function payment_return_handle(Request $request){
		
		DB::reconnect('suivhdalivetest');
        DB::purge('suivhdalivetest');
        DB::setDefaultConnection('suivhdalivetest');
		Session::put('DB_DATABASE', DB::connection()->getDatabaseName());
        Config::set('database.connections.suivhdalivetest.database', DB::connection()->getDatabaseName());
		
		
	//dd(session()->all());
	$input=$request->All();
	
	//dd($input);
	
	$decrypt = '';
		
	if(isset($input["enc_data"])){
		$decrypt = decrypt_mp($input["enc_data"]);
	}elseif(isset($input["encdata"])){
		$decrypt = decrypt_mp($input["encdata"]);
	}
	
	//dd($decrypt);
		
	$arrData = explode("|",$decrypt);

	//dd($arrData);
	
		$paymentData = array();
		
		foreach($arrData as $key=>$value){
			
			$raw = explode("=",$value);
			
			if(isset($raw[1])){
				$paymentData[$raw[0]] = $raw[1];
			}else{
				\Session::put('error', $decrypt);
				return Redirect::to('/pay-ver-mp');
			}


			
			
		}
		
		//dd($paymentData);
	
	
	if(isset($paymentData['crn']) && isset($paymentData['status'])){
		 try {
			$pay_status = $paymentData["status"];
			
			if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Success'){
					$sttusdd=1;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && $pay_status=='Pending') {
					$sttusdd=2;
				} else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='Failure')) {
					$sttusdd=3;
				}else if(!empty($pay_status) && ($pay_status!='') && ($pay_status!=null && ($pay_status!='null')) && ($pay_status=='A')) {
					$sttusdd=4;
				}else{	
					$sttusdd=2;
				}
				
			//dd($sttusdd);
				
			$reff_no = $paymentData["urn"];
			$chkd = DB::connection('suivhdalivetest')->table('payment_details_common')
			->select('*')
			->where('reff_no', '=', $reff_no)
			->get();
			echo "<br>";
			echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
//dd();
			$pay_status = $paymentData["status"];
			

				$challan_timestamp = date('Y-m-d H:i:s',strtotime($paymentData["Transaction_date_time"]));
				$bank_timestamp = date('Y-m-d H:i:s',strtotime($paymentData["Transaction_date_time"]));

			$paymentconfirmationnumber = ($paymentData["cin"]<>'')?$paymentData["cin"]:$chkd[0]->payment_confirmation_number_cin;
			$bank_code = $paymentData["brn"];
			$bank_reff_no = ($paymentData["cin"]<>'')?$paymentData["cin"]:$chkd[0]->bank_transaction_id;
			$partyname = $chkd[0]->deposited_by;
			$urn = $paymentData["urn"];
			if(isset($paymentData["Challan_number"])){
				$Challan_number = $paymentData["Challan_number"];
			}elseif(isset($paymentData["challan_number"])){
				$Challan_number = $paymentData["challan_number"];
			}
			
			
			$remarks = $paymentData["status_desc"];
			
			if(count($chkd) > 0){
				$myvar = DB::connection('suivhdalivetest')->table('payment_details_common')
				->where('reff_no', '=', $reff_no)
				->update([ 
				   'status_from_bank_status_code'      						=> $pay_status, 
				   'bank_transaction_id'      								=> $bank_reff_no,
				   'bank_cd'      											=> $bank_code,
				   'deposited_by'      										=> $partyname,
				   'gtn'      												=> $Challan_number,
				   'bank_transaction_message'      							=> $remarks,
				   'payment_confirmation_number_cin'      					=> $paymentconfirmationnumber,
				   'challan_ref_id_date'      								=> $challan_timestamp,
				   'bank_transtimestamp'      								=> $bank_timestamp,
				   'challan_ref_id'      									=> $urn,
				   'bank_transaction_status'  		  						=> $sttusdd, 
				   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);		
	
			$nomdata = DB::connection('suivhdalivetest')->table('nomination_application')
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

			//   dd(123);
		    $nidcccc = substr($mid, 0, -1);  
			echo "<br>".$nidcccc."-1<br>";
			
			$nomination_no = $nomdata[0]->nomination_no;
			$candidate_id = $nomdata[0]->candidate_id; 
			$s = $nomdata[0]->st_code; 
			$a = $nomdata[0]->ac_no; 
			
			
			
			
			$mob = DB::connection('suivhdalivetest')->table('profile')
			->select('name', 'mobile', 'email')
			->where('candidate_id', '=', $candidate_id)
			->get();
			//echo $chkd[0]->candidate_id;
			
			//dd($mob);
			
$sessiondata = Auth::loginUsingId($chkd[0]->candidate_id);

//dd($sessiondata);
$user_data=Auth()->user();


Auth::guard('web')->setUser($user_data);
//dd($user_data);
Session::put('login_details', $user_data);
Session::put('logged_id', $user_data->id);
Session::put('user_login',true);
			
			
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
		   
		  // dd(session()->all());
			  Session::flash('is_payment',"yes");
			  echo "<br>".$nidcccc."<br>";
			  //dd(encrypt_string($nidcccc));
			  //return redirect('dashboard-nomination-new');
			  return Redirect::to('nomination/prev?query='.encrypt_string($nidcccc).'&id='.$nidcccc.'&data='.encrypt_string($nidcccc));
			 // dd(1);
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
  
  
  	public function payment_verification_mp(Request $request){
	  
		$data['paydata'] =  DB::connection('mysql')
		->table('payment_details_common')
		->select('*')
		->where('st_code', '=', 'S12')
		//->whereIn('bank_transaction_status', array(1,2,3))
		->get();
		return view('nomination/payment-verification-mp', $data);
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


