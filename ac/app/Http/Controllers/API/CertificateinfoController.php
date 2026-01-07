<?php
//#####################Vinay###################
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use App\commonModel;
use App\adminmodel\CEOModel;
use App\adminmodel\CEOApplicationModel;
use App\Helpers\SmsgatewayHelper;
use App\adminmodel\Addsearches;
use App\adminmodel\OfficerLogin;
use App\adminmodel\CandidateModel;
use QrCode;

class CertificateinfoController extends Controller {

    /** 
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware(['auth:admin', 'auth']);
        //$this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->ceomodel = new CEOModel();
        $this->ceoapplicationmodel = new CEOApplicationModel();
    }



	public function applicationDetails(request $request){
		Addsearches::on('mysql2')->create($request->all());
		
		$applicationid = $request->get('appid');
           
            $getDetailView = $this->ceoapplicationmodel->getAppdetailsById($applicationid);
	
			
			if(!empty($getDetailView) > 0 ){
					
				$ch =explode(',',$getDetailView->channel_id);
				foreach ($ch as  $value) {
						$Channel[] =  $this->ceoapplicationmodel->getChannelNamebyId($value)->channel_name;
					}	
				
				$getChannelNamebyId = implode(', ', $Channel);
				//$getChannelNamebyId = $this->ceoapplicationmodel->getChannelNamebyId($getDetailView->channel_id);
				
				$getPartyDetailsbyId = $this->ceoapplicationmodel->getPartyDetailsbyId($getDetailView->political_party_id, $getDetailView->political_party_type_id);
				
				$Party = ($getPartyDetailsbyId->PARTYTYPE == 'S')?'(State Party)':(($getPartyDetailsbyId->PARTYTYPE == 'N')?'(National Party)':'');
				
				$getStateDetailsbyStId = $this->ceoapplicationmodel->getStateDetailbyStId($getDetailView->detailstate_id);
							
				$getDisttDetailbyStId = $this->ceoapplicationmodel->getDisttDetailbyStId($getDetailView->detailstate_id, $getDetailView->detaildistt_id);
				
				$DetailView['qrcode'] = $getDetailView->qrcode;
				$DetailView['formid'] = $getDetailView->reference_no;
				$DetailView['Advertisement Title'] =  $getDetailView->title_of_ad;
				$DetailView['Channel Name'] = ucfirst($getChannelNamebyId->channel_name);
				$DetailView['Telecast Date'] = date("d F Y",strtotime($getDetailView->date_of_telecast_from)) .' to '. date("d F Y",strtotime($getDetailView->date_of_telecast_to));
				$DetailView['Duration of Advertisement'] = $getDetailView->duration;
				$DetailView['Party Name'] = 'Political Party '.$getPartyDetailsbyId->PARTYNAME . $Party;
				$DetailView['Party Address'] = $getDetailView->detailadd.', '.$getDisttDetailbyStId->DIST_NAME. ', '.$getStateDetailsbyStId->ST_NAME;
				$DetailView['Applied Date'] = $getDetailView->created_at;
				$DetailView['Aproved By'] = $getDetailView->approved_by;
				$DetailView['Aproved By State'] = $getDetailView->detail_state_name;
				$DetailView['Aproved By District'] = $getDetailView->detail_distt_name;
				$DetailView['Aproval date'] = $getDetailView->date_of_certification;
				$DetailView['Media Status code'] = $getDetailView->ad_status;
				$DetailView['Media Status'] = $getDetailView->media_status;
				$ctype = array('Printed','Telecast','Broadcast','Social Media');
				$DetailView['channel_type'] = $ctype[$getDetailView->channel_type];
				$success['appdetails'] = $DetailView;
				
				$success['success'] =  'true';
				
			}else
			{

				$success['appdetails'] = 'No Record';
				$success['success'] =  'false';
			}
			
            
			
			
			return response()->json($success);
        
	} 
	
	
	public function qrapplicationDetails(request $request){
		//echo 'test' ; exit;
			Addsearches::on('mysql2')->create($request->all());
           $qrcode = $request->get('qrcode');
		   
            $getDetailView = $this->ceoapplicationmodel->getAppdetailsByQRcode($qrcode);
	
			
			if(!empty($getDetailView) > 0 ){
					
				$ch =explode(',',$getDetailView->channel_id);
				foreach ($ch as  $value) {
						$Channel[] =  $this->ceoapplicationmodel->getChannelNamebyId($value)->channel_name;
					}	
				
				$getChannelNamebyId = implode(', ', $Channel);
				
				$getPartyDetailsbyId = $this->ceoapplicationmodel->getPartyDetailsbyId($getDetailView->political_party_id, $getDetailView->political_party_type_id);
				
				$Party = ($getPartyDetailsbyId->PARTYTYPE == 'S')?'(State Party)':(($getPartyDetailsbyId->PARTYTYPE == 'N')?'(National Party)':'');
				
				$getStateDetailsbyStId = $this->ceoapplicationmodel->getStateDetailbyStId($getDetailView->detailstate_id);
							
				$getDisttDetailbyStId = $this->ceoapplicationmodel->getDisttDetailbyStId($getDetailView->detailstate_id, $getDetailView->detaildistt_id);

				$DetailView['qrcode'] = $getDetailView->qrcode;
				$DetailView['formid'] = $getDetailView->reference_no;
				$DetailView['Advertisement Title'] =  $getDetailView->title_of_ad;
				$DetailView['Channel Name'] = ucfirst($getChannelNamebyId->channel_name);
				$DetailView['Telecast Date'] = date("d F Y",strtotime($getDetailView->date_of_telecast_from)) .' to '. date("d F Y",strtotime($getDetailView->date_of_telecast_to));
				$DetailView['Duration of Advertisement'] = $getDetailView->duration;
				$DetailView['Party Name'] = 'Political Party '.$getPartyDetailsbyId->PARTYNAME . $Party;
				$DetailView['Party Address'] = $getDetailView->detailadd.', '.$getDisttDetailbyStId->DIST_NAME. ', '.$getStateDetailsbyStId->ST_NAME;
				$DetailView['Applied Date'] = $getDetailView->created_at;
				$DetailView['Aproved By'] = $getDetailView->approved_by;
				$DetailView['Aproved By State'] = $getDetailView->detail_state_name;
				$DetailView['Aproved By District'] = $getDetailView->detail_distt_name;
				$DetailView['Aproval date'] = $getDetailView->date_of_certification;
				$DetailView['Media Status code'] = $getDetailView->ad_status;
				$DetailView['Media Status'] = $getDetailView->media_status;
				$ctype = array('Printed','Telecast','Broadcast','Social Media');
				$DetailView['channel_type'] = $ctype[$getDetailView->channel_type];
				$success['appdetails'] = $DetailView;
				
			$success['success'] =  'true';
				
			}else
			{

				$success['appdetails'] = 'No Record';
				$success['success'] =  'false';
			}
			
			return response()->json($success);
        
	} 
	
	
	
	public function searchapplicationDetails(request $request){
		//echo 'test' ; exit;
		Addsearches::on('mysql2')->create($request->all());
          if($request->get('appid'))
			  $success = $this->applicationDetails($request);
		   
		   else{
            $getDetailView = $this->ceoapplicationmodel->getAppdetailsBySearch($request);
	
			
			if(!empty($getDetailView) > 0 ){
					
				$ch =explode(',',$getDetailView->channel_id);
				foreach ($ch as  $value) {
						$Channel[] =  $this->ceoapplicationmodel->getChannelNamebyId($value)->channel_name;
					}	
				
				$getChannelNamebyId = implode(', ', $Channel);
				
				$getPartyDetailsbyId = $this->ceoapplicationmodel->getPartyDetailsbyId($getDetailView->political_party_id, $getDetailView->political_party_type_id);
				
				$Party = ($getPartyDetailsbyId->PARTYTYPE == 'S')?'(State Party)':(($getPartyDetailsbyId->PARTYTYPE == 'N')?'(National Party)':'');
				
				$getStateDetailsbyStId = $this->ceoapplicationmodel->getStateDetailbyStId($getDetailView->detailstate_id);
							
				$getDisttDetailbyStId = $this->ceoapplicationmodel->getDisttDetailbyStId($getDetailView->detailstate_id, $getDetailView->detaildistt_id);
				
				$DetailView['qrcode'] = $getDetailView->qrcode;
				$DetailView['formid'] = $getDetailView->reference_no;
				$DetailView['Advertisement Title'] =  $getDetailView->title_of_ad;
				$DetailView['Channel Name'] = ucfirst($getChannelNamebyId->channel_name);
				$DetailView['Telecast Date'] = date("d F Y",strtotime($getDetailView->date_of_telecast_from)) .' to '. date("d F Y",strtotime($getDetailView->date_of_telecast_to));
				$DetailView['Duration of Advertisement'] = $getDetailView->duration;
				$DetailView['Party Name'] = 'Political Party '.$getPartyDetailsbyId->PARTYNAME . $Party;
				$DetailView['Party Address'] = $getDetailView->detailadd.', '.$getDisttDetailbyStId->DIST_NAME. ', '.$getStateDetailsbyStId->ST_NAME;
				$DetailView['Applied Date'] = $getDetailView->created_at;
				$DetailView['Aproved By'] = $getDetailView->approved_by;
				$DetailView['Aproved By State'] = $getDetailView->detail_state_name;
				$DetailView['Aproved By District'] = $getDetailView->detail_distt_name;
				$DetailView['Aproval date'] = $getDetailView->date_of_certification;
				$DetailView['Media Status code'] = $getDetailView->ad_status;
				$DetailView['Media Status'] = $getDetailView->media_status;
				$ctype = array('Printed','Telecast','Broadcast','Social Media');
				$DetailView['channel_type'] = $ctype[$getDetailView->channel_type];
				$success['appdetails'] = $DetailView;
				
			$success['success'] =  'true';
				
			}else
			{

				$success['appdetails'] = 'No Record';
				$success['success'] =  'false';
			}
			
			
        
		} 
		return $success;
	}
	


	public function candidateapplicationlist(request $request){
		//echo 'test' ; exit;
		Addsearches::on('mysql2')->create($request->all());
          if($request->get('appid'))
			  $success = $this->applicationDetails($request);
		   
		   else{
		   		$CandidateDetails = CandidateModel::find($request->get('cand_id')); 

            $getalllist = $this->ceoapplicationmodel->candidateapplicationlist($CandidateDetails->cand_mobile);
	
			
			if(!empty($getalllist) > 0 ){

				foreach ($getalllist as  $getDetailView) {
					
				$ch =explode(',',$getDetailView->channel_id);
				foreach ($ch as  $value) {
						$Channel[] =  $this->ceoapplicationmodel->getChannelNamebyId($value)->channel_name;
					}	
				
				$getChannelNamebyId = implode(', ', $Channel);
				
				$getPartyDetailsbyId = $this->ceoapplicationmodel->getPartyDetailsbyId($getDetailView->political_party_id, $getDetailView->political_party_type_id);
				
				$Party = ($getPartyDetailsbyId->PARTYTYPE == 'S')?'(State Party)':(($getPartyDetailsbyId->PARTYTYPE == 'N')?'(National Party)':'');
				
				$getStateDetailsbyStId = $this->ceoapplicationmodel->getStateDetailbyStId($getDetailView->detailstate_id);
							
				$getDisttDetailbyStId = $this->ceoapplicationmodel->getDisttDetailbyStId($getDetailView->detailstate_id, $getDetailView->detaildistt_id);
				
				$DetailView['qrcode'] = $getDetailView->qrcode;
				$DetailView['formid'] = $getDetailView->reference_no;
				$DetailView['Advertisement Title'] =  $getDetailView->title_of_ad;
				$DetailView['Channel Name'] = ucfirst($getChannelNamebyId->channel_name);
				$DetailView['Telecast Date'] = date("d F Y",strtotime($getDetailView->date_of_telecast_from)) .' to '. date("d F Y",strtotime($getDetailView->date_of_telecast_to));
				$DetailView['Duration of Advertisement'] = $getDetailView->duration;
				$DetailView['Party Name'] = 'Political Party '.$getPartyDetailsbyId->PARTYNAME . $Party;
				$DetailView['Party Address'] = $getDetailView->detailadd.', '.$getDisttDetailbyStId->DIST_NAME. ', '.$getStateDetailsbyStId->ST_NAME;
				$DetailView['Applied Date'] = $getDetailView->created_at;
				$DetailView['Aproved By'] = $getDetailView->approved_by;
				$DetailView['Aproved By State'] = $getDetailView->detail_state_name;
				$DetailView['Aproved By District'] = $getDetailView->detail_distt_name;
				$DetailView['Aproval date'] = $getDetailView->date_of_certification;
				$DetailView['Media Status code'] = $getDetailView->ad_status;
				$DetailView['Media Status'] = $getDetailView->media_status;
				$ctype = array('Printed','Telecast','Broadcast','Social Media');
				$DetailView['channel_type'] = $ctype[$getDetailView->channel_type];
				$success['appdetails'][$getDetailView->reference_no] = $DetailView;
				
				}
			$success['success'] =  'true';
			}else
			{

				$success['appdetails'] = 'No Record';
				$success['success'] =  'false';
			}
			
			
        
		} 
		return $success;
	}
	

	public function currenteapplicationlist(request $request){
		//echo 'test' ; exit;
		Addsearches::on('mysql2')->create($request->all());
          if($request->get('appid'))
			  $success = $this->applicationDetails($request);
		   
		   else{
		   		$officers = officerLogin::find($request->get('officer_id')); 

		   	if($officers->designation == "CEO")
            	$getalllist = $this->ceoapplicationmodel->getCEOcurrentaddlist($officers->st_code);
			if($officers->designation == "DEO")
            	$getalllist = $this->ceoapplicationmodel->getDEOcurrentaddlist($officers->dist_no);
	
			
			if(!empty($getalllist) > 0 ){

				foreach ($getalllist as  $getDetailView) {
					
				$ch =explode(',',$getDetailView->channel_id);
				foreach ($ch as  $value) {
						$Channel[] =  $this->ceoapplicationmodel->getChannelNamebyId($value)->channel_name;
					}	
				
				$getChannelNamebyId = implode(', ', $Channel);
				
				$getPartyDetailsbyId = $this->ceoapplicationmodel->getPartyDetailsbyId($getDetailView->political_party_id, $getDetailView->political_party_type_id);
				
				$Party = ($getPartyDetailsbyId->PARTYTYPE == 'S')?'(State Party)':(($getPartyDetailsbyId->PARTYTYPE == 'N')?'(National Party)':'');
				
				$getStateDetailsbyStId = $this->ceoapplicationmodel->getStateDetailbyStId($getDetailView->detailstate_id);
							
				$getDisttDetailbyStId = $this->ceoapplicationmodel->getDisttDetailbyStId($getDetailView->detailstate_id, $getDetailView->detaildistt_id);
				
				$DetailView['qrcode'] = $getDetailView->qrcode;
				$DetailView['formid'] = $getDetailView->reference_no;
				$DetailView['Advertisement Title'] =  $getDetailView->title_of_ad;
				$DetailView['Channel Name'] = ucfirst($getChannelNamebyId->channel_name);
				$DetailView['Telecast Date'] = date("d F Y",strtotime($getDetailView->date_of_telecast_from)) .' to '. date("d F Y",strtotime($getDetailView->date_of_telecast_to));
				$DetailView['Duration of Advertisement'] = $getDetailView->duration;
				$DetailView['Party Name'] = 'Political Party '.$getPartyDetailsbyId->PARTYNAME . $Party;
				$DetailView['Party Address'] = $getDetailView->detailadd.', '.$getDisttDetailbyStId->DIST_NAME. ', '.$getStateDetailsbyStId->ST_NAME;
				$DetailView['Applied Date'] = $getDetailView->created_at;
				$DetailView['Aproved By'] = $getDetailView->approved_by;
				$DetailView['Aproved By State'] = $getDetailView->detail_state_name;
				$DetailView['Aproved By District'] = $getDetailView->detail_distt_name;
				$DetailView['Aproval date'] = $getDetailView->date_of_certification;
				$DetailView['Media Status code'] = $getDetailView->ad_status;
				$DetailView['Media Status'] = $getDetailView->media_status;
				$ctype = array('Printed','Telecast','Broadcast','Social Media');
				$DetailView['channel_type'] = $ctype[$getDetailView->channel_type];
				$success['appdetails'][$getDetailView->reference_no] = $DetailView;
				
				}
			$success['success'] =  'true';
			}else
			{

				$success['appdetails'] = 'No Record';
				$success['success'] =  'false';
			}
			
			
        
		} 
		return $success;
	}

	
	public function formidapplicationDetails(request $request){
		//echo 'test' ; exit;
		      Addsearches::on('mysql2')->create($request->all());    
            $getDetailView = $this->ceoapplicationmodel->getAppdetailsByformId($request->get('formid'));
	
			
			if(!empty($getDetailView) > 0 ){
				$ctype = array('Printed','Telecast','Broadcast','Social Media');

				
				
					$ch =explode(',',$getDetailView->channel_id);
				foreach ($ch as  $value) {
						$Channel[] =  $this->ceoapplicationmodel->getChannelNamebyId($value)->channel_name;
					}	
				
				$getChannelNamebyId = implode(', ', $Channel);
				//$getChannelNamebyId = $this->ceoapplicationmodel->getChannelNamebyId($getDetailView->channel_id);
				$Party = '';				
				if($getDetailView->political_party_type_id){
				$getPartyDetailsbyId = $this->ceoapplicationmodel->getPartyDetailsbyId($getDetailView->political_party_id, $getDetailView->political_party_type_id);
				

				$Party = ($getPartyDetailsbyId->PARTYTYPE == 'S')?'(State Party)':(($getPartyDetailsbyId->PARTYTYPE == 'N')?'(National Party)':'');
				}
				// echo $getChannelNamebyId;
				// die();
				$getStateDetailsbyStId = $this->ceoapplicationmodel->getStateDetailbyStId($getDetailView->detailstate_id);
							
				$getDisttDetailbyStId = $this->ceoapplicationmodel->getDisttDetailbyStId($getDetailView->detailstate_id, $getDetailView->detaildistt_id);
				
				$DetailView['qrcode'] = $getDetailView->qrcode;
				$DetailView['formid'] = $getDetailView->reference_no;
				$DetailView['Advertisement Title'] =  $getDetailView->title_of_ad;
				$DetailView['Channel Name'] = ucwords($getChannelNamebyId);
				$DetailView['Telecast Date'] = date("d F Y",strtotime($getDetailView->date_of_telecast_from)) .' to '. date("d F Y",strtotime($getDetailView->date_of_telecast_to));
				$DetailView['Duration of Advertisement'] = $getDetailView->duration;
				$DetailView['Party Name'] = ($Party)?'Political Party '.$getPartyDetailsbyId->PARTYNAME . $Party:'';
				$DetailView['Party Address'] = $getDetailView->detailadd.', '.$getDisttDetailbyStId->DIST_NAME. ', '.$getStateDetailsbyStId->ST_NAME;
				$DetailView['Applied Date'] = $getDetailView->created_at;
				$DetailView['Aproved By'] = $getDetailView->approved_by;
				$DetailView['Aproved By State'] = $getDetailView->detail_state_name;
				$DetailView['Aproved By District'] = $getDetailView->detail_distt_name;
				$DetailView['Aproval date'] = $getDetailView->date_of_certification;
				$DetailView['Media Status code'] = $getDetailView->ad_status;
				$DetailView['Media Status'] = $getDetailView->media_status;
				$ctype = array('Printed','Telecast','Broadcast','Social Media');
				$DetailView['channel_type'] = $ctype[$getDetailView->channel_type];

				$success['appdetails'] = $DetailView;
				
			$success['success'] =  'true';
				
			}else
			{

				$success['appdetails'] = 'No Record';
				$success['success'] =  'false';
			}
			
		
		return $success;
	}











public function addsearchList(request $request){ 

        $getDetailView = AddSearches::where('media_admin_id' , $request->get('media_admin_id') )->paginate(10); 

		if(count($getDetailView) > 0 ){

			$DetailView = array();

			foreach($getDetailView as $getDetailViewk){

			$DetailView['Media Admin Id'] = $getDetailViewk->media_admin_id;
			$DetailView['Qr Code'] = $getDetailViewk->qrcode;
			$DetailView['Form Id'] = $getDetailViewk->form_id;
			$DetailView['Valid From'] = $getDetailViewk->valid_from;
			$DetailView['Valid to'] = $getDetailViewk->valid_to;
			$DetailView['Approval Date'] = $getDetailViewk->approval_date;
			$DetailView['State'] = $getDetailViewk->state;
			$DetailView['District'] = $getDetailViewk->district;
			$DetailView['Applicant Name'] = $getDetailViewk->applicant_name;
			$DetailView['Updated At'] = $getDetailViewk->updated_at;
			$DetailView['Created At'] = $getDetailViewk->created_at;
			$success['addsearchlisting'][] = $DetailView;

			}

		}else
		{
		$success['addsearchlisting'] = 'No Record';
		}
		$success['success'] =  'true';

		return response()->json($success);

	}

	public function SendSms(request $request){



        $getDetailView = OfficerLogin::where('Phone_no' , $request->get('mobile') )->firstOrFail();

        $otp = rand(123000, 999999);
        $message = "$otp is your OTP for Pre Media.";

       //  if(!empty($request->Phone_no)){
       //  $response = SmsgatewayHelper::sendOtpSMS($message, $request->Phone_no);
       //  $result = explode(',',$response);

       // $success = array();
       //  if($result[0]==402){ //die("hi");
       //  $success['message'] = "Message is sent on this mobile no ".$request->Phone_no;
       //  } else{
       //  $success['message'] = "Some problem in mobile or API";
       //  }

       //  $success['success'] =  'true';
       //  }


       // $getDetailView->update(['api_otp' => $otp]);
       //  return $success;

        }



        public function getToken(request $request){



            $getChannelNamebyId = $this->ceoapplicationmodel->getProfileOtp($request->get('Phone_no'),$request->get('api_otp'));

            $mobile_token = md5(rand(1234, 999999));

            //echo "<pre>"; print_r($getChannelNamebyId); die("In Controller");

        $getDetailView = officerLogin::where('Phone_no' , $request->get('Phone_no') )->where('api_otp' , $request->get('api_otp') )->firstOrFail();


        $getDetailView = OfficerLogin::where('Phone_no' , $request->get('Phone_no') )->firstOrFail();

        $getDetailView->update(['mobile_token' => $mobile_token]);

        if(!empty($getDetailView) > 0 ){
                    
                //$getChannelNamebyId = $this->ceoapplicationmodel->getChannelNamebyId($getDetailView->channel_id);



                $DetailView['accesstoken'] = $mobile_token;
                $DetailView['NAME'] = $getChannelNamebyId->name;
                $DetailView['Mobile No'] = $getChannelNamebyId->Phone_no;
                $DetailView['Designation'] = $getChannelNamebyId->designation;

                $DetailView['State'] = $getChannelNamebyId->StateName;
                $DetailView['District'] = $getChannelNamebyId->DistrictName;
                $DetailView['AC'] = $getChannelNamebyId->ACNAME;
                
                $success['appdetails'] = $DetailView;
                
            }else
            {
                $success['appdetails'] = 'No Record';
            }
            $success['success'] =  'true';
            
            return response()->json($success);

        }
	
	

}