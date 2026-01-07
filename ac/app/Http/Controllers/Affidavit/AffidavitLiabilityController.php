<?php

namespace App\Http\Controllers\Affidavit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\models\Affidavit\AffPanDetail;
use App\models\Affidavit\AffLoanBankFincInst;
use App\models\Affidavit\AffLoanType;
use App\models\Affidavit\AffLoanIndividualEntity;
use App\models\Affidavit\AffGovtDues;
use App\models\Affidavit\AffGovtDeptName;
use App\models\Affidavit\AffOtherLiabilities;
use App\models\Affidavit\AffLiabilitiesDisputes;
use App\models\Affidavit\AffCandDetail;
use Session;
use Exception;
use Log;
use App\Classes\xssClean;
use Carbon\Carbon;

use App\commonModel;
use App\adminmodel\CandidateModel;
use App\models\Nomination\NominationApplicationModel;
use App\Helpers\LogNotification;

class AffidavitLiabilityController extends Controller
{
	public function __construct(){   
        $this->commonModel = new commonModel();
		$this->CandidateModel = new CandidateModel();
    }
	
    public function Liabilities()
    {


          
      $user=Auth::user();
		$nomID=(Session::get('nomination_id'));
		$message=array();
        $message['MobNo']= $user->mobile ?? '';
	    $message['applicationType']= 'WebApp';
	    $message['Module']= 'SUVIDHA';
	    $message['TransectionType']= 'Online Nomination';
        $message['TransectionAction']= 'Affidavit Immovable Assets Details ';
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
			$data =  $data->where('is_deleted','0')->orderBy("aff_pan_details.relation_type_code", "ASC")
	    	->get();

	    	$loan_details = AffLoanBankFincInst::leftjoin("aff_m_loan_type", "aff_l_loan_bank_finc_inst.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
	    	->select("aff_l_loan_bank_finc_inst.*", "aff_m_loan_type.loan_type")
	    	->where("aff_l_loan_bank_finc_inst.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_l_loan_bank_finc_inst.relation_type_code", "ASC")
	    	->get();

	    	$indi_loan_details = AffLoanIndividualEntity::leftjoin("aff_m_loan_type", "aff_l_loan_individual_entity.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
	    	->select("aff_l_loan_individual_entity.*", "aff_m_loan_type.loan_type")
	    	->where("aff_l_loan_individual_entity.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_l_loan_individual_entity.relation_type_code", "ASC")
	    	->get();

	    	$govt_dues = AffGovtDues::leftjoin("aff_m_govt_dept_name", "aff_l_govt_dues.govt_dept_name_code", "=", "aff_m_govt_dept_name.govt_dept_name_code")
	    	->select("aff_l_govt_dues.*", "aff_m_govt_dept_name.govt_dept_name")
	    	->where("aff_l_govt_dues.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_l_govt_dues.relation_type_code", "ASC")
	    	->get();

	    	$other_details = AffOtherLiabilities::where("aff_l_other_liabilities.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_l_other_liabilities.relation_type_code", "ASC")
	    	->get();

	    	$other_disputes = AffLiabilitiesDisputes::where("aff_l_liabilities_disputes.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_l_liabilities_disputes.relation_type_code", "ASC")
	    	->get();

	    	/*echo "<pre>";
	    	print_r($data);die;*/
	    	$loan_type = AffLoanType::get();
	    	$govt_dept = AffGovtDeptName::get();
			
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);

				if(config('public_config.nomination_log'))
           {
	          $message['LogDescription']= 'Affidavit ID: '.$affidavit_id.$nomination_num;
	          $message['TransectionStatus']= 'SUCCESS';

	        LogNotification::LogInfo($message);
	         }

			

	    	return view('affidavit.affidavit_liabilities', ['data'=>$data, 'user_data'=>$d, 'loan_details'=>$loan_details,'loan_type'=>$loan_type,'indi_loan_details'=>$indi_loan_details,'govt_dues'=>$govt_dues,'govt_dept'=>$govt_dept,'other_details'=>$other_details,'other_disputes'=>$other_disputes]);
    	} 
    	catch (Exception $e) 
    	{

    			if(config('public_config.nomination_log'))
		           {
		          $message['LogDescription']= 'Someting Wrong '. $e->getMessage();
		          $message['TransectionStatus']= 'Failed';

		        LogNotification::LogInfo($message);
		         }
    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
            Session::flash('status',0);
      		Session::flash('flash-message', "Something went wrong, please try again after sometime.");
    		//return redirect('AffidavitDashboard');
		}
    }

    public function save_loan_bank(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->loan_type))
    	{
    		$joint_account_with = "";
    		$joint_account_with_name = "";
    		$joint_other_name = "";
    		try {
    			if(!empty($request->joint))
    			{
	    			$joint_id = array();
					$joint_name = array();
	    			foreach($request->joint as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$joint_account_with = implode(', ', $joint_id);
	    			$joint_account_with_name = implode(', ', $joint_name);
    			}
				
				if(!empty($request->joint_other)){
	    			$joint_other_name = "with ".$joint_account_with_name.",".$request->joint_other;
				}else{
					$joint_other_name = "with ".$joint_account_with_name;
				}

	    		$insert = new AffLoanBankFincInst;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->bank_inst_name = $xss->clean_input($request->bank_inst_name);
	            $insert->loan_type_id = $xss->clean_input($request->loan_type);
	            $insert->other_loan_type = $xss->clean_input($request->loan_type_other);
	            $insert->loan_account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = $joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->outstanding_amount = $xss->clean_input($request->amount);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id,
	            				"joint_account_with" =>$joint_account_with,
	            				"joint_account_with_name" =>$joint_account_with_name,
	            				"joint_other" =>$request->joint_other
	        	);
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

    public function update_loan_bank(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->loan_type) && !empty($request->loan_id))
    	{
    		$joint_account_with = "";
    		$joint_account_with_name = "";
    		$joint_other_name = "";
    		try {
    			if(!empty($request->joint))
    			{
	    			$joint_id = array();
					$joint_name = array();
	    			foreach($request->joint as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$joint_account_with = implode(', ', $joint_id);
	    			$joint_account_with_name = implode(', ', $joint_name);
	   
				}
				
				if(!empty($request->joint_other)){
	    			$joint_other_name = "with ".$joint_account_with_name.",".$request->joint_other;
				}else{
					$joint_other_name = "with ".$joint_account_with_name;
				}

	    		$insert = AffLoanBankFincInst::find($request->loan_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->bank_inst_name = $xss->clean_input($request->bank_inst_name);
	            $insert->loan_type_id = $xss->clean_input($request->loan_type);
	            $insert->other_loan_type = $xss->clean_input($request->loan_type_other);
	            $insert->loan_account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = $joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->outstanding_amount = $xss->clean_input($request->amount);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id,
	            				"joint_account_with" =>$joint_account_with,
	            				"joint_account_with_name" =>$joint_account_with_name,
	            				"joint_other" =>$request->joint_other
	        	);
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

    public function delete_loan_bank(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->loan_id))
    	{
    		try {
	    		//AffLoanBankFincInst::destroy($request->loan_id);
				AffLoanBankFincInst::where('id',$request->loan_id)->update(['is_deleted' => '1']);
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

    public function save_indi_loan_bank(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->loan_type))
    	{
    		$joint_account_with = "";
    		$joint_account_with_name = "";
    		$joint_other_name = "";
    		try {
    			if(!empty($request->joint))
    			{
	    			$joint_id = array();
					$joint_name = array();
	    			foreach($request->joint as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$joint_account_with = implode(', ', $joint_id);
	    			$joint_account_with_name = implode(', ', $joint_name);
				}
				
				if(!empty($request->joint_other)){
	    			$joint_other_name = "with ".$joint_account_with_name.",".$request->joint_other;
    			}else{
					$joint_other_name = "with ".$joint_account_with_name;
				}

	    		$insert = new AffLoanIndividualEntity;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->individual_entity_name = $xss->clean_input($request->individual_entity_name);
	            $insert->loan_type_id = $xss->clean_input($request->loan_type);
	            $insert->other_loan_type = $xss->clean_input($request->loan_type_other);
	            $insert->loan_account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = $joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->outstanding_amount = $xss->clean_input($request->amount);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id,
	            				"joint_account_with" =>$joint_account_with,
	            				"joint_account_with_name" =>$joint_account_with_name,
	            				"joint_other" =>$request->joint_other
	        	);
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

    public function update_indi_loan_bank(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->loan_type) && !empty($request->loan_id))
    	{
    		$joint_account_with = "";
    		$joint_account_with_name = "";
    		$joint_other_name = "";
    		try {
    			if(!empty($request->joint))
    			{
	    			$joint_id = array();
					$joint_name = array();
	    			foreach($request->joint as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$joint_account_with = implode(', ', $joint_id);
	    			$joint_account_with_name = implode(', ', $joint_name);
				}
				
				if(!empty($request->joint_other)){
	    			$joint_other_name = "with ".$joint_account_with_name.",".$request->joint_other;
    			}else{
					$joint_other_name = "with ".$joint_account_with_name;
				}

	    		$insert = AffLoanIndividualEntity::find($request->loan_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->individual_entity_name = $xss->clean_input($request->individual_entity_name);
	            $insert->loan_type_id = $xss->clean_input($request->loan_type);
	            $insert->other_loan_type = $xss->clean_input($request->loan_type_other);
	            $insert->loan_account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = $joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->outstanding_amount = $xss->clean_input($request->amount);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id,
	            				"joint_account_with" =>$joint_account_with,
	            				"joint_account_with_name" =>$joint_account_with_name,
	            				"joint_other" =>$request->joint_other
	        	);
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

    public function delete_indi_loan_bank(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->loan_id))
    	{
    		try {
	    		//AffLoanIndividualEntity::destroy($request->loan_id);
				AffLoanIndividualEntity::where('id',$request->loan_id)->update(['is_deleted' => '1']);
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

    public function save_govt_due(Request $request)
    {
    	$xss = new xssClean;

    	if( !empty($request->rel_type_id) && !empty($request->govt_dept_name_code))
    	{
    		try {

	    		$insert = new AffGovtDues;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->govt_dept_name_code = $xss->clean_input($request->govt_dept_name_code);
	            $insert->other_dept = $xss->clean_input($request->other_dept);
	            $insert->due_details = $xss->clean_input($request->due_details);
	            $insert->amount = $xss->clean_input($request->amount);
	            $insert->is_government_accomodation = $xss->clean_input($request->is_government_accomodation);
	            $insert->government_accomodation_address = $xss->clean_input($request->government_accomodation_address);
	            $insert->telephone_charges = $xss->clean_input($request->telephone_charges);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id,"telephone_charges" =>Carbon::parse($request->telephone_charges)->format('d/m/Y'));
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


    public function save_govt_due_image(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->govt_due_id))
    	{
    		try {
    			$image_url = "";
    			$images_fileName = "";
	            if($request->hasFile('file'))
	            {
	            	$old_file = AffGovtDues::find($request->govt_due_id);
	                if($old_file)
	                {
	                    if(!empty($old_file->no_dues_file))
	                    {
	                        $old = base_path().'/public/affidavit/uploads/govt_dues_liabitilies/'.$old_file->no_dues_file;
	                        if(file_exists($old))
	                        {
	                            unlink($old);
	                        }
	                    }
	                }

	                $images = $request->file('file');
	                $images_fileName = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME)."-".date('Ymdhis').'.'.$images->getClientOriginalExtension();
	                $images->move(base_path().'/public/affidavit/uploads/govt_dues_liabitilies', $images_fileName);
	                $image_url = url('/')."/affidavit/uploads/govt_dues_liabitilies/".$images_fileName;

		    		$insert = AffGovtDues::find($request->govt_due_id);
		            $insert->no_dues_file = $images_fileName;
		            $insert->save();

		            $result = array("image_url" =>$image_url);
		        	echo json_encode($result);
	            }
	            else
	            	echo 0;

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

    public function update_govt_due(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->govt_due_id) && !empty($request->govt_dept_name_code))
    	{
    		try {

	    		$insert = AffGovtDues::find($request->govt_due_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->govt_dept_name_code = $xss->clean_input($request->govt_dept_name_code);
	            $insert->other_dept = $xss->clean_input($request->other_dept);
	            $insert->due_details = $xss->clean_input($request->due_details);
	            $insert->amount = $xss->clean_input($request->amount);
	            $insert->is_government_accomodation = $xss->clean_input($request->is_government_accomodation);
	            $insert->government_accomodation_address = $xss->clean_input($request->government_accomodation_address);
	            $insert->telephone_charges = $xss->clean_input($request->telephone_charges);
	            if($request->govt_dept_name_code!=1)
	            	$insert->no_dues_file = "";
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id,"telephone_charges" =>Carbon::parse($request->telephone_charges)->format('d/m/Y'));
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

    public function delete_govt_due(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffGovtDues::destroy($request->id);	
				AffGovtDues::where('id',$request->id)->update(['is_deleted' => '1']);
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

    public function save_other_liabilities(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->asset_type))
    	{
    		try {
	    		$insert = new AffOtherLiabilities;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->authority_name = $xss->clean_input($request->asset_type);
	            $insert->details = $xss->clean_input($request->brief_details);
	            $insert->amount = $xss->clean_input($request->other_amount);
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
    public function update_other_liabilities(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->asset_type) && !empty($request->other_id))
    	{
    		try {
	    		$insert = AffOtherLiabilities::find($request->other_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->authority_name = $xss->clean_input($request->asset_type);
	            $insert->details = $xss->clean_input($request->brief_details);
	            $insert->amount = $xss->clean_input($request->other_amount);
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
    
    public function delete_other_liabilities(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->other_id))
    	{
    		try {
	    		//AffOtherLiabilities::destroy($request->other_id);
				AffOtherLiabilities::where('id',$request->other_id)->update(['is_deleted' => '1']);
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

    public function save_other_disputes_liabilities(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->asset_type))
    	{
    		try {
	    		$insert = new AffLiabilitiesDisputes;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->authority_name = $xss->clean_input($request->asset_type);
	            $insert->details = $xss->clean_input($request->brief_details);
	            $insert->amount = $xss->clean_input($request->other_amount);
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
    public function update_other_disputes_liabilities(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->asset_type) && !empty($request->other_id))
    	{
    		try {
	    		$insert = AffLiabilitiesDisputes::find($request->other_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->authority_name = $xss->clean_input($request->asset_type);
	            $insert->details = $xss->clean_input($request->brief_details);
	            $insert->amount = $xss->clean_input($request->other_amount);
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
    
    public function delete_other_disputes_liabilities(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->other_id))
    	{
    		try {
	    		//AffLiabilitiesDisputes::destroy($request->other_id);
				AffLiabilitiesDisputes::where('id',$request->other_id)->update(['is_deleted' => '1']);	            
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
