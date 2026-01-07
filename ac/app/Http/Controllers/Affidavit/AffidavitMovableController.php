<?php

namespace App\Http\Controllers\Affidavit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\models\Affidavit\AffCandDetail;
use App\models\Affidavit\AffPanDetail;
use App\models\Affidavit\AffRelationTypeModel;
use App\models\Affidavit\AffCashInHand;
use App\models\Affidavit\AffDepositType;
use App\models\Affidavit\AffBankDetails;
use App\models\Affidavit\AffInvestmentInCompanies;
use App\models\Affidavit\AffCompanyInvestmentType;
use App\models\Affidavit\AffSavingAndPolicy;
use App\models\Affidavit\AffSavingPolicyType;
use App\models\Affidavit\AffLoanDetails;
use App\models\Affidavit\AffLoanType;
use App\models\Affidavit\AffVehicleDetails;
use App\models\Affidavit\AffVehicleType;
use App\models\Affidavit\AffValuableThingsDetails;
use App\models\Affidavit\AffValuableWeight;
use App\models\Affidavit\AffValuableThings;
use App\models\Affidavit\AffOtherAssets;
use Session;
use Exception;
use Log;
use App\Classes\xssClean;
use Carbon\Carbon;

use App\commonModel;
use App\adminmodel\CandidateModel;

class AffidavitMovableController extends Controller
{
	public function __construct(){   
        $this->commonModel = new commonModel();
		$this->CandidateModel = new CandidateModel();
    }
	
    public function AffidavitDashboard(){
    	//dd(Auth::user());
    	return view('affidavit.affidavitdashboard');
    }
	

    public function MovableAssets()
    {
		
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
	    	->leftjoin('aff_cash_in_hand', function($join)
					{
					    $join->on('aff_cash_in_hand.relation_type_code', '=', 'aff_pan_details.relation_type_code');
					    $join->on('aff_cash_in_hand.affidavit_id','=', 'aff_pan_details.affidavit_id');
					    $join->on('aff_cash_in_hand.id','=', 'aff_pan_details.id');
					})
	    	->select("aff_pan_details.name", "aff_cash_in_hand.cash_in_hand as cash", "aff_m_relation_type.relation_type", "aff_cash_in_hand.id", "aff_pan_details.candidate_id", "aff_pan_details.relation_type_code");
			if($affidavit_id){
				$data->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$data =  $data->where('is_deleted','0')->orderBy("aff_pan_details.relation_type_code", "ASC")
	    	->get();
			/* echo '<pre>';
			print_r($data);
			die(); */
	    	$bank_details = AffBankDetails::leftjoin("aff_m_deposit_type", "aff_bank_details.deposit_type_id", "=", "aff_m_deposit_type.deposit_type_id")
	    	->select("aff_bank_details.*", "aff_m_deposit_type.deposit_type")
	    	->where("aff_bank_details.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_bank_details.relation_type_code", "ASC")
	    	->get();

	    	$company_details = AffInvestmentInCompanies::leftjoin("aff_m_company_investment_type", "aff_investment_in_companies.company_investment_type_id", "=", "aff_m_company_investment_type.company_investment_type_id")
	    	->select("aff_investment_in_companies.*", "aff_m_company_investment_type.company_investment_type")
	    	->where("aff_investment_in_companies.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_investment_in_companies.relation_type_code", "ASC")
	    	->get();


	    	$saving_details = AffSavingAndPolicy::leftjoin("aff_m_saving_policies_type", "aff_savings_and_policies.saving_type_id", "=", "aff_m_saving_policies_type.saving_type_id")
	    	->select("aff_savings_and_policies.*", "aff_m_saving_policies_type.saving_type")
	    	->where("aff_savings_and_policies.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_savings_and_policies.relation_type_code", "ASC")
	    	->get();

	    	$loan_details = AffLoanDetails::leftjoin("aff_m_loan_type", "aff_loan_details.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
	    	->select("aff_loan_details.*", "aff_m_loan_type.loan_type")
	    	->where("aff_loan_details.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_loan_details.relation_type_code", "ASC")
	    	->get();

	    	$vehicle_details = AffVehicleDetails::leftjoin("aff_m_vehicle_type", "aff_vehicle_details.vehicle_type_id", "=", "aff_m_vehicle_type.vehicle_type_id")
	    	->select("aff_vehicle_details.*", "aff_m_vehicle_type.vehicle_type")
	    	->where("aff_vehicle_details.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_vehicle_details.relation_type_code", "ASC")
	    	->get();

	    	$jewellery_details = AffValuableThingsDetails::leftjoin("aff_m_valuable_things", "aff_valuable_things_details.valuable_type_id", "=", "aff_m_valuable_things.valuable_type_id")
	    	->leftjoin("aff_m_valuable_weight", "aff_valuable_things_details.weight_unit_id", "=", "aff_m_valuable_weight.valuable_weight_id")
	    	->select("aff_valuable_things_details.*", "aff_m_valuable_things.valuable_type", "aff_m_valuable_weight.valuable_weight")
	    	->where("aff_valuable_things_details.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_valuable_things_details.relation_type_code", "ASC")
	    	->get();

	    	$other_details = AffOtherAssets::where("aff_other_assets.affidavit_id", $affidavit_id)
			->where('is_deleted','0')
	    	->orderBy("aff_other_assets.relation_type_code", "ASC")
	    	->get();

	    	$deposit_type = AffDepositType::get();
	    	$company_investment_type = AffCompanyInvestmentType::get();
	    	$saving_type = AffSavingPolicyType::get();
	    	$loan_type = AffLoanType::get();
	    	$vehicle_type = AffVehicleType::get();
	    	$valuable_things = AffValuableThings::get();
	    	$valuable_weight = AffValuableWeight::get();


			//$data  = [];
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			//$data['user_data']=$d;
			
			//dd($data);


	    	/*echo "<pre>";
	    	print_r($data);die;*/
	    	return view('affidavit.affidavit_movable_assets', ['data'=>$data,'user_data'=>$d, 'deposit_type'=>$deposit_type, 'bank_details'=>$bank_details, 'company_investment_type'=>$company_investment_type, 'company_details'=>$company_details, 'saving_type'=>$saving_type, 'saving_details'=>$saving_details, 'loan_details'=>$loan_details, 'loan_type'=>$loan_type, 'vehicle_details'=>$vehicle_details, 'vehicle_type'=>$vehicle_type, 'jewellery_details'=>$jewellery_details, 'valuable_things'=>$valuable_things, 'valuable_weight'=>$valuable_weight, 'other_details'=>$other_details]);
    	  }catch (Exception $e) 
    	{
    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
            Session::flash('status',0);
      		Session::flash('flash-message', "Something went wrong, please try again after sometime.");
    		//return redirect('AffidavitDashboard');
		}

    }

    public function update_cash(Request $request)
    {

    	if(!empty($request->id) && !empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->cash))
    	{
    		try {
	    		$cash = AffCashInHand::where("affidavit_id", Session::get('affidavit_id'))
	    		->where("id", $request->id)
	    		->update(array('cash_in_hand' => $request->cash));
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

    public function save_deposit(Request $request)
    {
    	$xss = new xssClean;
    	if( !empty($request->rel_type_id) && !empty($request->bank_name))
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
	    					$joint_other_name = $joint_account_with_name.",".$request->joint_other;
					}else{
						$joint_other_name = $joint_account_with_name;
					}
    			

	    		$insert = new AffBankDetails;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->bank_name = $xss->clean_input($request->bank_name);
	            $insert->branch_address = $xss->clean_input($request->branch_address);
	            $insert->deposit_type_id = $xss->clean_input($request->deposit_type);
	            $insert->deposit_type_other = $xss->clean_input($request->deposit_other);
	            $insert->account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->deposit_date = $xss->clean_input($request->deposit_date);
	            $insert->amount = $xss->clean_input($request->amount);
	            $insert->added_create_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$insert->id,
	            				"joint_account_with" =>$joint_account_with,
	            				"joint_account_with_name" =>$joint_account_with_name,
	            				"joint_other" =>$request->joint_other,
	            				"deposit_date" =>Carbon::parse($request->deposit_date)->format('d/m/Y'),
	            				"deposit_date_edit" =>Carbon::parse($request->deposit_date)->format('Y-m-d')
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


    public function update_deposit(Request $request)
    {
    	$xss = new xssClean;
    	if( !empty($request->rel_type_id) && !empty($request->bank_name) && !empty($request->bank_id))
    	{
    		$joint_account_with = "";
    		$joint_account_with_name = "";
    		$joint_other_name = "";
			
			//dd($request->all());
			
			
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
						//dd(1);
	    					$joint_other_name = $joint_account_with_name.",".$request->joint_other;
					}else{
						//dd(2);
						$joint_other_name = $joint_account_with_name;
					}
					
					//dd($joint_other_name);
    			

	    		$insert = AffBankDetails::find($request->bank_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->bank_name = $xss->clean_input($request->bank_name);
	            $insert->branch_address = $xss->clean_input($request->branch_address);
	            $insert->deposit_type_id = $xss->clean_input($request->deposit_type);
	            $insert->deposit_type_other = $xss->clean_input($request->deposit_other);
	            $insert->account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = "with ".$joint_other_name;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->deposit_date = $xss->clean_input($request->deposit_date);
	            $insert->amount = $xss->clean_input($request->amount);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$request->bank_id,
	            				"joint_account_with" =>$joint_account_with,
	            				"joint_account_with_name" =>$joint_account_with_name,
	            				"joint_other" =>$request->joint_other,
	            				"deposit_date" =>Carbon::parse($request->deposit_date)->format('d/m/Y'),
	            				"deposit_date_edit" =>Carbon::parse($request->deposit_date)->format('Y-m-d')
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

    public function delete_deposit(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->bank_id))
    	{
    		try {
	    		//AffBankDetails::destroy($request->bank_id);	            
	    		AffBankDetails::where('id',$request->bank_id)->update(['is_deleted' => '1']);	            
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

    public function save_investment(Request $request)
    {
    	$xss = new xssClean;

    	if( !empty($request->rel_type_id) && !empty($request->company))
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
	    					$joint_other_name = $joint_account_with_name.",".$request->joint_other;
					}else{
						$joint_other_name = $joint_account_with_name;
					}
    			

	    		$insert = new AffInvestmentInCompanies;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->company = $xss->clean_input($request->company);
	            $insert->company_investment_type_id = $xss->clean_input($request->invest_type);
	            $insert->company_investment_type_other = $xss->clean_input($request->ins_deposit_other);
	            $insert->number_of_units = $xss->clean_input($request->number_of_units);
	            $insert->account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->amount = $xss->clean_input($request->amount);
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

    public function update_investment(Request $request)
    {
    	$xss = new xssClean;
    	
    	if(!empty($request->rel_type_id) && !empty($request->company) && !empty($request->company_id))
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
	    			$joint_other_name = $joint_account_with_name.",".$request->joint_other;
				}else{
					$joint_other_name = $joint_account_with_name;
				}
    			

	    		$insert = AffInvestmentInCompanies::find($request->company_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->company = $xss->clean_input($request->company);
	            $insert->company_investment_type_id = $xss->clean_input($request->invest_type);
	            $insert->company_investment_type_other = $xss->clean_input($request->ins_deposit_other);
	            $insert->number_of_units = $xss->clean_input($request->number_of_units);
	            $insert->account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->amount = $xss->clean_input($request->amount);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$request->company_id,
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


    public function delete_investment(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->bank_id))
    	{
    		try {
	    		//AffInvestmentInCompanies::destroy($request->bank_id);
				AffInvestmentInCompanies::where('id',$request->bank_id)->update(['is_deleted' => '1']);
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

    public function save_savings(Request $request)
    {
    	$xss = new xssClean;

    	if( !empty($request->rel_type_id) && !empty($request->company))
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
					$joint_other_name = $joint_account_with_name.",".$request->joint_other;
				}else{
					$joint_other_name = $joint_account_with_name;
    			}

	    		$insert = new AffSavingAndPolicy;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->company = $xss->clean_input($request->company);
	            $insert->saving_type_id = $xss->clean_input($request->saving_type);
	            $insert->saving_type_other = $xss->clean_input($request->saving_type_other);
	            $insert->account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->amount = $xss->clean_input($request->amount);
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

    public function update_savings(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->rel_type_id) && !empty($request->saving_id) && !empty($request->company))
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
					$joint_other_name = $joint_account_with_name.",".$request->joint_other;
				}else{
					$joint_other_name = $joint_account_with_name;
    			}

	    		$insert = AffSavingAndPolicy::find($request->saving_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->company = $xss->clean_input($request->company);
	            $insert->saving_type_id = $xss->clean_input($request->saving_type);
	            $insert->saving_type_other = $xss->clean_input($request->saving_type_other);
	            $insert->account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->amount = $xss->clean_input($request->amount);
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

    public function delete_savings(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->bank_id))
    	{
    		try {
	    		//AffSavingAndPolicy::destroy($request->bank_id);            
	    		AffSavingAndPolicy::where('id',$request->bank_id)->update(['is_deleted' => '1']);            
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

    public function save_loan(Request $request)
    {
    	$xss = new xssClean;

    	if( !empty($request->rel_type_id) && !empty($request->loan_type))
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
					$joint_other_name = $joint_account_with_name.",".$request->joint_other;
				}else{
					$joint_other_name = $joint_account_with_name;
    			}

	    		$insert = new AffLoanDetails;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->loan_type_id = $xss->clean_input($request->loan_type);
	            $insert->loan_type_other = $xss->clean_input($request->loan_type_other);
	            $insert->loan_account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->loan_to = $xss->clean_input($request->loan_to);
	            $insert->nature_of_loan = $xss->clean_input($request->nature_of_loan);
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

    public function update_loan(Request $request)
    {
    	$xss = new xssClean;

    	if( !empty($request->rel_type_id) && !empty($request->loan_type) && !empty($request->loan_id))
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
					$joint_other_name = $joint_account_with_name.",".$request->joint_other;
				}else{
					$joint_other_name = $joint_account_with_name;
    			}

	    		$insert = AffLoanDetails::find($request->loan_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->loan_type_id = $xss->clean_input($request->loan_type);
	            $insert->loan_type_other = $xss->clean_input($request->loan_type_other);
	            $insert->loan_account_type = $xss->clean_input($request->account_type);
	            $insert->joint_account_with = $joint_account_with;
	            $insert->joint_account_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name = $xss->clean_input($request->joint_other);
	            $insert->loan_to = $xss->clean_input($request->loan_to);
	            $insert->nature_of_loan = $xss->clean_input($request->nature_of_loan);
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

    public function delete_loan(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->loan_id))
    	{
    		try {
	    		//AffLoanDetails::destroy($request->loan_id);	            
	    		AffLoanDetails::where('id',$request->loan_id)->update(['is_deleted' => '1']);	            
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

    public function save_vehicle(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->vehicle_type))
    	{
    		try {
	    		$insert = new AffVehicleDetails;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->vehicle_type_id = $xss->clean_input($request->vehicle_type);
	            $insert->vehicle_type_other = $xss->clean_input($request->vehicle_type_other);
	            $insert->make = $xss->clean_input($request->make);
	            $insert->registration_no = $xss->clean_input($request->registration_no);
	            $insert->year_of_purchase = $xss->clean_input($request->year_of_purchase);
	            $insert->amount = $xss->clean_input($request->amount);
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

    public function update_vehicle(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->vehicle_type) && !empty($request->vehicle_id))
    	{
    		try {
	    		$insert = AffVehicleDetails::find($request->vehicle_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->vehicle_type_id = $xss->clean_input($request->vehicle_type);
	            $insert->vehicle_type_other = $xss->clean_input($request->vehicle_type_other);
	            $insert->make = $xss->clean_input($request->make);
	            $insert->registration_no = $xss->clean_input($request->registration_no);
	            $insert->year_of_purchase = $xss->clean_input($request->year_of_purchase);
	            $insert->amount = $xss->clean_input($request->amount);
	            $insert->updated_at = Carbon::now();
	            $insert->added_update_at = Carbon::now();
	            $insert->save();

	            $result = array("id" =>$request->vehicle_id);
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

    public function delete_vehicle(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->vehicle_id))
    	{
    		try {
	    		//AffVehicleDetails::destroy($request->vehicle_id);
				AffVehicleDetails::where('id',$request->vehicle_id)->update(['is_deleted' => '1']);
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

    public function save_jewellery(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->jewel_type))
    	{
    		try {
	    		$insert = new AffValuableThingsDetails;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->valuable_type_id = $xss->clean_input($request->jewel_type);
	            $insert->valuable_type_other = $xss->clean_input($request->val_type_other);
	            $insert->weight = $xss->clean_input($request->weight_value);
	            $insert->weight_unit_id = $xss->clean_input($request->val_weight);
	            $insert->weight_unit_other = $xss->clean_input($request->weight_type_other);
	            $insert->amount = $xss->clean_input($request->jewellery_amount);
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

    public function update_jewellery(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->jewel_type) && !empty($request->jewel_id))
    	{
    		try {
	    		$insert = AffValuableThingsDetails::find($request->jewel_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->valuable_type_id = $xss->clean_input($request->jewel_type);
	            $insert->valuable_type_other = $xss->clean_input($request->val_type_other);
	            $insert->weight = $xss->clean_input($request->weight_value);
	            $insert->weight_unit_id = $xss->clean_input($request->val_weight);
	            $insert->weight_unit_other = $xss->clean_input($request->weight_type_other);
	            $insert->amount = $xss->clean_input($request->jewellery_amount);
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

    public function delete_jewellery(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->jewellery))
    	{
    		try {
	    		//AffValuableThingsDetails::destroy($request->jewellery);
				AffValuableThingsDetails::where('id',$request->jewellery)->update(['is_deleted' => '1']);
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

    public function save_other(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->asset_type))
    	{
    		try {
	    		$insert = new AffOtherAssets;
	            $insert->affidavit_id = Session::get('affidavit_id');
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->asset_type = $xss->clean_input($request->asset_type);
	            $insert->brief_details = $xss->clean_input($request->brief_details);
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
    public function update_other(Request $request)
    {
    	$xss = new xssClean;

    	if(!empty($request->cand_id) && !empty($request->rel_type_id) && !empty($request->asset_type) && !empty($request->other_id))
    	{
    		try {
	    		$insert = AffOtherAssets::find($request->other_id);
	            $insert->candidate_id = $xss->clean_input($request->cand_id);
	            $insert->relation_type_code = $xss->clean_input($request->rel_type_id);
	            $insert->asset_type = $xss->clean_input($request->asset_type);
	            $insert->brief_details = $xss->clean_input($request->brief_details);
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
    
    public function delete_other(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->other_id))
    	{
    		try {
	    		//AffOtherAssets::destroy($request->other_id);
				AffOtherAssets::where('id',$request->other_id)->update(['is_deleted' => '1']);
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
