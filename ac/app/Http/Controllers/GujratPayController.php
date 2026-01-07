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



class GujratPayController extends Controller
{    
	
	public function payment_gujrat_verification(Request $request){
		
		$key='q4UOLnbuVc0mP8Jf634f1zCGVy2pf9lj';
		$iv='q4UOLnbuVc0mP8Jf'; 
		$enc_method = "AES-128-CBC";  
		
		$st="Not Available";
		
		if(isset($_REQUEST['encdata'])){
			$finalResponse=openssl_decrypt($_REQUEST['encdata'], $enc_method, $key, $options=0, $iv);	
			$exp=explode("|", $finalResponse);
			


			//dd($exp);
				
					$my=[];
					foreach($exp as $key=>$val){ 
					$ssss=explode("=", $val);
						if($ssss[0]!='challan_url'){
						  if(!empty($ssss[0]) && (!empty($ssss[0]))){	
							$data[$ssss[0]]=$ssss[1];
							array_push($my, $data);
						  }	
						}
					}
					$farray = end($my);
					
					
					
					$reff_no='';
					if(!empty($farray['Transaction_id'])){
					 $reff_no=$farray['Transaction_id'];	
					}
					
					$User_id='';
					if(!empty($farray['User_id'])){
					 $User_id=$farray['User_id'];	
					}
					
					
			
			
			
			$Bank_ref_no='';
			if(!empty($farray['Bank_ref_no'])){
			 $bank_code=$farray['Bank_ref_no'];	
			}
			
			$pament_gateway_refrence_no='';
			if(!empty($farray['Dlr_ref_no'])){
			 $pament_gateway_refrence_no=$farray['Dlr_ref_no'];	
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
			
			$Bank_name='';
			if(!empty($farray['Bank_name'])){
			 $Bank_name=$farray['Bank_name'];	
			}
			
			
			$paymentconfirmationnumber='';
			$paymentconfirmationnumber='';
			if(!empty($farray['Cin'])){
			 $paymentconfirmationnumber=$farray['Cin'];	
			}
			
			$redirect=1;
			
			$sttusdd=0;
			if(!empty($farray['Cin']) && ($farray['Cin']!='') && ($farray['Cin']!=null && ($farray['Cin']!='null'))){
			 $sttusdd=1;
			} else {
			 $sttusdd=2;	
			}
			
			if(!empty($farray['Status']) && ($farray['Status']=='Success' or $farray['Status']=='success' or $farray['Status']=='SUCCESS')){
			 $sttusdd=1;
			}
			
			
			
			
			if(!empty($farray['Status']) && ($farray['Status']=='FAIL' or $farray['Status']=='fail' or $farray['Status']=='Failure' or $farray['Status']=='FAILURE')){
			 $redirect=0;	
			 $sttusdd=3;
			} 
			
			if(!empty($farray['Status']) && ($farray['Status']=='Pending' or $farray['Status']=='BOOKED')){
			 $sttusdd=2;
					
					$status_desc='';
					if(!empty($farray['Status_desc'])){
					 $status_desc=$farray['Status_desc'];	
					}
					
			}
			
						
			$st=$farray['Status'];
			
			$Amount=0;
			if(!empty($farray['Amount'])){
			 $Amount=$farray['Amount'];	
			}
			
			$pay_date='';
			if(!empty($farray['Pymnt_date'])){
			 $pay_date=$farray['Pymnt_date'];	
			}
			
			
			
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('id', 'st_code', 'ac_no', 'candidate_id')
			->where('reff_no', '=', $reff_no)
			->get();
			//echo "<br>";
			//echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
			
			if(count($chkd) > 0){
				
				$myvar = DB::connection('mysql')->table('payment_details_common')
				//->where('candidate_id', '=', \Auth::id())
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      									=> $pament_gateway_refrence_no,  
				   'bank_transaction_message'      							=> $status_desc,
				   'bank_ref_id'      										=> $bank_reff_no,
				   'bank_cd'      											=> $Bank_name,
				   'payment_confirmation_number_cin'      					=> $paymentconfirmationnumber,
				   //'bank_transtimestamp'      							=> $pay_date,
				   'bank_transtimestamp'      								=> date('Y-m-d H:i:s', time()),
				   'challan_amount'      									=> $Amount,
				   //'txn_amount'      										=> $Amount,
				   'bank_transaction_status'  		  						=> $sttusdd, 
				   'updated_at'  		  									=> date('Y-m-d H:i:s', time()), 
				]);	
					
			}
		}
		 Session::flash('is_payment', $st);
		 return redirect('nomination/pay-ver');	
		
	}
	
	
	public function payment_verification_Gujrat(Request $request){
	  
		$data['paydata'] =  DB::connection('mysql')
		->table('payment_details_common')
		->select('*')
		->where('st_code', '=', 'S06')
		->whereNotIn('bank_transaction_status', array(4))
		->get();
		return view('nomination/payment-verification', $data);
	}	
	
	
	

    public function payment_return_handle_gujrat(Request $request){
		
		
	
		$key='q4UOLnbuVc0mP8Jf634f1zCGVy2pf9lj';
		$iv='q4UOLnbuVc0mP8Jf'; 
		$enc_method = "AES-128-CBC";  
		//$rd='https://cybertreasuryuat.gujarat.gov.in/CyberTreasury_UAT/connectDept?service=DeptPortalConnection'; //UAT
		$rd="https://cybertreasury.gujarat.gov.in/CyberTreasury/connectDept?service=DeptPortalConnection";  // Live
		
			
			if(isset($_REQUEST['encdata'])){
				$finalResponse=openssl_decrypt($_REQUEST['encdata'], $enc_method, $key, $options=0, $iv);	
				
					$exp=explode("|", $finalResponse);
				
					$my=[];
					foreach($exp as $key=>$val){ 
					$ssss=explode("=", $val);
						if($ssss[0]!='challan_url'){
						  if(!empty($ssss[0]) && (!empty($ssss[0]))){	
							$data[$ssss[0]]=$ssss[1];
							array_push($my, $data);
						  }	
						}
					}
					$farray = end($my);
					
					//echo "<pre>"; print_r($farray); die;
					
					
					$reff_no='';
					if(!empty($farray['Transaction_id'])){
					 $reff_no=$farray['Transaction_id'];	
					}
					
					$User_id='';
					if(!empty($farray['User_id'])){
					 $User_id=$farray['User_id'];	
					}
					
					
			
			
			
			$Bank_ref_no='';
			if(!empty($farray['Bank_ref_no'])){
			 $bank_code=$farray['Bank_ref_no'];	
			}
			
			$pament_gateway_refrence_no='';
			if(!empty($farray['Dlr_ref_no'])){
			 $pament_gateway_refrence_no=$farray['Dlr_ref_no'];	
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
			
			$Bank_name='';
			if(!empty($farray['Bank_name'])){
			 $Bank_name=$farray['Bank_name'];	
			}
			
			
			$paymentconfirmationnumber='';
			$paymentconfirmationnumber='';
			if(!empty($farray['Cin'])){
			 $paymentconfirmationnumber=$farray['Cin'];	
			}
			
			$redirect=1;
			
			$sttusdd=0;
			if(!empty($farray['Cin']) && ($farray['Cin']!='') && ($farray['Cin']!=null && ($farray['Cin']!='null'))){
			 $sttusdd=1;
			} else {
			 $sttusdd=2;	
			}
			
			if(!empty($farray['Status']) && ($farray['Status']=='Success' or $farray['Status']=='success' or $farray['Status']=='SUCCESS')){
			 $sttusdd=1;
			}
			
			if(!empty($farray['Status']) && ($farray['Status']=='FAIL' or $farray['Status']=='fail' or $farray['Status']=='Failure' or $farray['Status']=='FAILURE')){
			 $redirect=0;	
			 $sttusdd=3;
			} 
			
			if(!empty($farray['Status']) && ($farray['Status']=='Pending' or $farray['Status']=='BOOKED')){
			 $sttusdd=2;
					
					$status_desc='';
					if(!empty($farray['Status_desc'])){
					 $status_desc=$farray['Status_desc'];	
					}
					
			}
			
			
			$Amount=0;
			if(!empty($farray['Amount'])){
			 $Amount=$farray['Amount'];	
			}
			
			$pay_date='';
			if(!empty($farray['Pymnt_date'])){
			 $pay_date=$farray['Pymnt_date'];	
			}
			
			
			
			$chkd = DB::connection('mysql')
			->table('payment_details_common')
			->select('id', 'st_code', 'ac_no', 'candidate_id')
			->where('reff_no', '=', $reff_no)
			->get();
			//echo "<br>";
			//echo "<pre>"; print_r($chkd).'-- $chkd value object<br>';
			
			if(count($chkd) > 0){
				
				$myvar = DB::connection('mysql')->table('payment_details_common')
				//->where('candidate_id', '=', \Auth::id())
				->where('reff_no', '=', $reff_no)
				->update([
				   'challan_ref_id'      						=> $pament_gateway_refrence_no, 
				   'bank_transaction_message'      				=> $status_desc,
				   'bank_ref_id'      							=> $bank_reff_no,
				   'bank_cd'      								=> $Bank_name,
				   'payment_confirmation_number_cin'      		=> $paymentconfirmationnumber,
				   'bank_transtimestamp'      					=> date('Y-m-d H:i:s', time()),
				   'challan_amount'      						=> $Amount,
				   'bank_transaction_status'  		  			=> $sttusdd, 
				   'updated_at'  		  						=> date('Y-m-d H:i:s', time()), 
				]);	
					
			}		
				
					
					
					
					//echo $reff_no.'-'.$User_id; die;
					
				$chkd = DB::connection('mysql')
				->table('payment_details_common')
				->select('*')
				->where('reff_no', '=', $reff_no)
				->get();
					//echo "<pre/>";  print_r($chkd); die;
				if(count($chkd) > 0 ){	
					
					$nomdata = DB::connection('mysql')
					->table('nomination_application')
					->select('id','nomination_no','candidate_id', 'st_code', 'ac_no')
					->where('st_code', '=', $chkd[0]->st_code)
					->where('ac_no', '=',   $chkd[0]->ac_no)
					->where('candidate_id', '=',   $chkd[0]->candidate_id)
					->get();	
					
				   $mid=''; $nidcccc='';
				   if(count($nomdata) > 0){
					foreach($nomdata as $getid){
						$mid.=$getid->id.',';
					} 
					$nidcccc = substr($mid, 0, -1); 
					
				  }
				  
				  if($redirect==1){
					Session::flash('is_payment',"yes");
				  }	else {
					 Session::flash('is_payment',"no"); 
				  }
					
					
				    echo "<br>".$nidcccc."<br>";
				    return redirect('nomination/prev?query='.encrypt_string($nidcccc).'&id='.$nidcccc.'&data='.encrypt_string($nidcccc));
				}
			}		
			
			
			
			if(isset($_REQUEST['enc_data'])){
			$decrypt=openssl_decrypt($_REQUEST['enc_data'], $enc_method, $key, $options=0, $iv);	
			
			$exp=explode("|", $decrypt);
		
			$my=[];
			foreach($exp as $key=>$val){ 
			$ssss=explode("=", $val);
				if($ssss[0]!='challan_url'){
				  if(!empty($ssss[0]) && (!empty($ssss[0]))){	
					$data[$ssss[0]]=$ssss[1];
					array_push($my, $data);
				  }	
				}
			}
			$farray = end($my);
			$reff_no='';
			
			if(!empty($farray['Transaction_id'])){
			 $reff_no=$farray['Transaction_id'];	
			}
			$User_id='';
			if(!empty($farray['User_id'])){
			 $User_id=$farray['User_id'];	
			}
			
			$datasss=DB::connection('mysql')
						->table('payment_details_common')
						->select('*')
						->where('reff_no', '=', $reff_no)
						->get(); 

			if(count($datasss)>0){
			?>	 
				<form method="POST" name="second" action="<?php echo $rd; ?>">
				<input type="hidden" name="Dept_call" value="second">
				<input type="hidden" name="token_valid" value="true">
				<input type="hidden" name="Transaction_id" value="<?php echo $reff_no ?>">
				<input type="hidden" name="User_id" value="<?php echo $User_id; ?>"> 
				</form>
				<script>
				document.second.submit();
				</script>
		<?php 	
			} else {
				die("Invalid Call");
				
			}
		}
  } 
}


