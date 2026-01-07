<?php

namespace App\Http\Controllers\Affidavit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\models\Affidavit\AffPanDetail;
use App\models\Affidavit\AffCandDetail;
use App\models\Affidavit\AffSelfSpouseOccupation;
use App\models\Affidavit\AffDependantSourceOfIncome;
use App\models\Affidavit\AffGovtPublicCompanyContract;
use App\models\Affidavit\AffHufTrustContracts;
use App\models\Affidavit\AffPartnershipFirmContracts;
use App\models\Affidavit\AffPrivateCompanyContract;
use Session;
use Exception;
use Log;
use App\Classes\xssClean;
use Carbon\Carbon;

use App\commonModel;
use App\adminmodel\CandidateModel;

use App\models\Nomination\NominationApplicationModel;
use App\Helpers\LogNotification;

class AffidavitProfessionController extends Controller
{
	public function __construct(){   
        $this->commonModel = new commonModel();
		$this->CandidateModel = new CandidateModel();
    }
	
    public function AffidavitDashboard(){
    	//dd(Auth::user());
    	return view('affidavit.affidavitdashboard');
    }

    public function Profession()
    {


        $user=Auth::user();
    	$message=array();
		$nomID=(Session::get('nomination_id'));
        $message['MobNo']= $user->mobile ?? '';
	    $message['applicationType']= 'WebApp';
	    $message['Module']= 'SUVIDHA';
	    $message['TransectionType']= 'Online Nomination';
        $message['TransectionAction']= 'Affidavit Liabilities Details ';
       if(isset($nomID) && !empty($nomID))
        {
        $get_nominattion_detail = NominationApplicationModel::get_nomination_application($nomID); 
	    $nomination_num= ' Nomination_Num: '.$get_nominattion_detail['nomination_no'];
	    }else{
	    	$nomination_num='';
	    }









		if(Session::get('affidavit_id')){
			$data = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();
			if($data->finalized == '1' && Auth::user()->role_id != '19'){
				return redirect()->to('part-a-detailed-report')->with('success','Your Affidavit is allready Finalized.');
			}
		}else{
			return redirect()->to('affidavitdashboard')->with('success','Please select State and constituency.');
		}
		
		
    	try {
    		$affidavit_id = Session::get('affidavit_id');
	    	$data = AffPanDetail::leftjoin("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
	    	->select("aff_pan_details.name", "aff_m_relation_type.relation_type", "aff_pan_details.id", "aff_pan_details.candidate_id", "aff_pan_details.relation_type_code");
	    	if($affidavit_id){
				$data->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$data =  $data->where('is_deleted','0')->orderBy("aff_pan_details.relation_type_code", "ASC")->get();

	    	$self_spouse_details = AffSelfSpouseOccupation::where("affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("relation_type_code", "ASC")
	    	->get();

	    	$dependant_income = AffDependantSourceOfIncome::where("affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("relation_type_code", "ASC")
	    	->get();
	    	
	    	$govt_public = AffGovtPublicCompanyContract::where("affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("relation_type_code", "ASC")
	    	->get();
	    	
	    	$huf_trsut = AffHufTrustContracts::where("affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("relation_type_code", "ASC")
	    	->get();
	    	
	    	$partnership = AffPartnershipFirmContracts::where("affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("relation_type_code", "ASC")
	    	->get();
	    	
	    	
	    	$private = AffPrivateCompanyContract::where("affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("relation_type_code", "ASC")
	    	->get();
			
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);

				if(config('public_config.nomination_log'))
           {
	          $message['LogDescription']= 'Affidavit ID: '.$affidavit_id. $nomination_num;
	          $message['TransectionStatus']= 'SUCCESS';

	        LogNotification::LogInfo($message);
	         }
	    	
	    	
	    	return view('affidavit.affidavit_profession', ['data'=>$data, 'user_data'=>$d, 'self_spouse_details'=>$self_spouse_details, 'dependant_income'=>$dependant_income, 'govt_public'=>$govt_public, 'huf_trsut'=>$huf_trsut, 'partnership'=>$partnership, 'private'=>$private]);
    	} 
    	catch (Exception $e) 
    	{

    		if(config('public_config.nomination_log'))
           {
	          $message['LogDescription']= 'Something went wrong: '.$e->getMessage();
	          $message['TransectionStatus']= 'Failed';

	        LogNotification::LogInfo($message);
	         }
    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
            Session::flash('status',0);
      		Session::flash('flash-message', "Something went wrong, please try again after sometime.");
    		//return redirect('AffidavitDashboard');
		}
    }

    public function save_self_spouse(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->occupation))
    	{
    		try {
	    		$insert = new AffSelfSpouseOccupation;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->occupation = $xss->clean_input($request->occupation);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function update_self_spouse(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->ss_id))
    	{
    		try 
    		{
    			$insert = AffSelfSpouseOccupation::find($request->ss_id);
	            $insert->occupation = $xss->clean_input($request->occupation);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function delete_self_spouse(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffSelfSpouseOccupation::destroy($request->id);
				AffSelfSpouseOccupation::where('id',$request->id)->update(['is_deleted' => '1']);
	        	echo 1;
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function save_dependent_income(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->source_of_income))
    	{
    		try {
	    		$insert = new AffDependantSourceOfIncome;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->source_of_income = $xss->clean_input($request->source_of_income);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
    
    public function update_dependent_income(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->depen_id))
    	{
    		try 
    		{
    			$insert = AffDependantSourceOfIncome::find($request->depen_id);
	            $insert->source_of_income = $xss->clean_input($request->source_of_income);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
    public function delete_dependent_income(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffDependantSourceOfIncome::destroy($request->id);
				AffDependantSourceOfIncome::where('id',$request->id)->update(['is_deleted' => '1']);
	        	echo 1;
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function save_govt_public(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->govt_public_company))
    	{
    		try {
	    		$insert = new AffGovtPublicCompanyContract;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->govt_public_company = $xss->clean_input($request->govt_public_company);
	            $insert->details = $xss->clean_input($request->details);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
        public function update_govt_public(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->govt_public_id))
    	{
    		try 
    		{
    			$insert = AffGovtPublicCompanyContract::find($request->govt_public_id);
	            $insert->govt_public_company = $xss->clean_input($request->govt_public_company);
	            $insert->details = $xss->clean_input($request->details);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
    public function delete_govt_public(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffGovtPublicCompanyContract::destroy($request->id);
				AffGovtPublicCompanyContract::where('id',$request->id)->update(['is_deleted' => '1']);
	        	echo 1;
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function save_huf(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->huf_trust_contracts))
    	{
    		try {
	    		$insert = new AffHufTrustContracts;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->huf_trust_contracts = $xss->clean_input($request->huf_trust_contracts);
	            $insert->details = $xss->clean_input($request->details);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
    
    public function update_huf(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->huf_id))
    	{
    		try 
    		{
    			$insert = AffHufTrustContracts::find($request->huf_id);
	            $insert->huf_trust_contracts = $xss->clean_input($request->huf_trust_contracts);
	            $insert->details = $xss->clean_input($request->details);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function delete_huf(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffHufTrustContracts::destroy($request->id);
				AffHufTrustContracts::where('id',$request->id)->update(['is_deleted' => '1']);
	        	echo 1;
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function save_partner(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->name_partnership_firm))
    	{
    		try {
	    		$insert = new AffPartnershipFirmContracts;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->name_partnership_firm = $xss->clean_input($request->name_partnership_firm);
	            $insert->details = $xss->clean_input($request->details);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
    
    public function update_partner(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->huf_id))
    	{
    		try 
    		{
    			$insert = AffPartnershipFirmContracts::find($request->huf_id);
	            $insert->name_partnership_firm = $xss->clean_input($request->name_partnership_firm);
	            $insert->details = $xss->clean_input($request->details);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function delete_partner(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffPartnershipFirmContracts::destroy($request->id);
				AffPartnershipFirmContracts::where('id',$request->id)->update(['is_deleted' => '1']);
	        	echo 1;
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function save_private(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->name_private_company))
    	{
    		try {
	    		$insert = new AffPrivateCompanyContract;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->name_private_company = $xss->clean_input($request->name_private_company);
	            $insert->details = $xss->clean_input($request->details);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
    
    public function update_private(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->private_id))
    	{
    		try 
    		{
    			$insert = AffPrivateCompanyContract::find($request->private_id);
	            $insert->name_private_company = $xss->clean_input($request->name_private_company);
	            $insert->details = $xss->clean_input($request->details);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function delete_private(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffPrivateCompanyContract::destroy($request->id);
				AffPrivateCompanyContract::where('id',$request->id)->update(['is_deleted' => '1']);
	        	echo 1;
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
}
