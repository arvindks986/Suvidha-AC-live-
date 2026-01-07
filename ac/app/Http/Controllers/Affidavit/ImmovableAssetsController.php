<?php

namespace App\Http\Controllers\Affidavit;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\models\States;
use App\models\Districts;
use App\models\Affidavit\AffCandDetail;
use App\models\Nomination\NominationApplicationModel;

use App\models\Affidavit\AffPanDetail;
use App\models\Affidavit\AffRelationTypeModel;
use App\models\Affidavit\AffCashInHand;
use App\models\Affidavit\AffDepositType;
use App\models\Affidavit\AffBankDetails;

use App\models\Affidavit\AffAgriculturalLand;
use App\models\Affidavit\AffNonAgriculturalLand;
use App\models\Affidavit\AffCommercialBuildings;
use App\models\Affidavit\AffResidentialBuildings;
use App\models\Affidavit\AffOtherImmovableAssets;
use App\models\Affidavit\AffPropertyType;

use App\Helpers\LogNotification;


use Exception;
use Log;
use App\Classes\xssClean;
use Carbon\Carbon;

use App\commonModel;
use App\adminmodel\CandidateModel;

class ImmovableAssetsController extends Controller
{
	public function __construct(){   
        $this->commonModel = new commonModel();
		$this->CandidateModel = new CandidateModel();
    }
	
	public function ImmovableAssets()
    {
		

           $user=Auth::user();
    	$message=array();
		$nomID=(Session::get('nomination_id'));
        $message['MobNo']= $user->mobile ?? '';
        $message['applicationType']= 'WebApp';
        $message['Module']= 'SUVIDHA';
        $message['TransectionType']= 'Online Nomination';
        $message['TransectionAction']= 'Affidavit Movable Assets Details ';
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
			
			//echo Session::get('affidavit_id');
		
			//echo '------------->'; die;
			
			$affidavit_id = Session::get('affidavit_id');
			
	    	$data = AffPanDetail::leftjoin("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
	    	->select("aff_pan_details.name", "aff_m_relation_type.relation_type","aff_pan_details.id", "aff_pan_details.candidate_id", "aff_pan_details.relation_type_code");
			if($affidavit_id){
				$data->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$data =  $data->where('is_deleted','0')->orderBy("aff_pan_details.relation_type_code", "ASC")->get();
						
			//dd($data);

	    	$agricultural_land = AffAgriculturalLand::leftjoin("aff_m_property_type", "aff_agricultural_land_details.property_type_id", "=", "aff_m_property_type.property_type_id")
	    	->select("aff_agricultural_land_details.*", "aff_m_property_type.property_type")
	    	//->where("aff_agricultural_land_details.candidate_id", Auth::user()->id)
			->where('affidavit_id',Session::get('affidavit_id'))
			->where('is_deleted','0')
	    	->orderBy("aff_agricultural_land_details.relation_type_code", "ASC")
	    	->get();
			
			$non_agricultural_land = AffNonAgriculturalLand::leftjoin("aff_m_property_type", "aff_non_agricultural_land_details.property_type_id", "=", "aff_m_property_type.property_type_id")
	    	->select("aff_non_agricultural_land_details.*", "aff_m_property_type.property_type")
	    	//->where("aff_non_agricultural_land_details.candidate_id", Auth::user()->id)
			->where('affidavit_id',Session::get('affidavit_id'))
			->where('is_deleted','0')
	    	->orderBy("aff_non_agricultural_land_details.relation_type_code", "ASC")
	    	->get();
			
			$commercial_buildings = AffCommercialBuildings::leftjoin("aff_m_property_type", "aff_commercial_buildings_details.property_type_id", "=", "aff_m_property_type.property_type_id")
	    	->select("aff_commercial_buildings_details.*", "aff_m_property_type.property_type")
	    	//->where("aff_commercial_buildings_details.candidate_id", Auth::user()->id)
			->where('affidavit_id',Session::get('affidavit_id'))
			->where('is_deleted','0')
	    	->orderBy("aff_commercial_buildings_details.relation_type_code", "ASC")
	    	->get();
			
			$residential_buildings = AffResidentialBuildings::leftjoin("aff_m_property_type", "aff_residential_buildings_details.property_type_id", "=", "aff_m_property_type.property_type_id")
	    	->select("aff_residential_buildings_details.*", "aff_m_property_type.property_type")
	    	//->where("aff_residential_buildings_details.candidate_id", Auth::user()->id)
			->where('affidavit_id',Session::get('affidavit_id'))
			->where('is_deleted','0')
	    	->orderBy("aff_residential_buildings_details.relation_type_code", "ASC")
	    	->get();
			
			$other_immovable = AffOtherImmovableAssets::select("aff_other_immovable_assets.*")
	    	//->where("aff_other_immovable_assets.candidate_id", Auth::user()->id)
			->where('affidavit_id',Session::get('affidavit_id'))
			->where('is_deleted','0')
	    	->orderBy("aff_other_immovable_assets.relation_type_code", "ASC")
	    	->get();

	    	$property_type = AffPropertyType::get();

	    	//echo "<pre>"; print_r($other_immovable);die; 
			
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			

			 if(config('public_config.nomination_log'))
		           {
		          $message['LogDescription']= 'Affidavit ID: '.$affidavit_id.$nomination_num;
		          $message['TransectionStatus']= 'SUCCESS';

		        LogNotification::LogInfo($message);
		         }

			
	    	return view('affidavit.ImmovableAssets.affidavit_immovable_assets', ['data'=>$data, 'user_data'=>$d, 'property_type'=>$property_type, 'agricultural_land'=>$agricultural_land, 'non_agricultural_land'=>$non_agricultural_land, 'commercial_buildings'=>$commercial_buildings, 'residential_buildings'=>$residential_buildings, 'other_immovable'=>$other_immovable]);
    	} 
    	 catch (Exception $e) 
    	{

    		 if(config('public_config.nomination_log'))
		           {
		          $message['LogDescription']= 'Something went to Wrong: ' .$e->getMessage();
		          $message['TransectionStatus']= 'Failed';

		        LogNotification::LogInfo($message);
		         }
    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
            Session::flash('status',0);
      		Session::flash('flash-message', "Something went wrong, please try again after sometime.");
    		//return redirect('AffidavitDashboard');
		}
 
    }
	
	public function save_agricultural_land(Request $request)
    {
    	$xss = new xssClean;
		
    	if( !empty($request->rel_type_id))
    	{
			//dd($request->all());
			
    		$property_joint_with = "";
    		$property_joint_with_name = "";
    		$joint_other_name = "";
    		try {
    			if(!empty($request->property_joint_with))
    			{
					
	    			$joint_id = array();
					$joint_name = array();
					//die($request->property_joint_with_name);
					
	    			foreach($request->property_joint_with as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$property_joint_with = implode(', ', $joint_id);
	    			$property_joint_with_name = implode(', ', $joint_name);
	   
				}
	   
	   
					if(!empty($request->joint_other_name)){
	    					$joint_other_name = $property_joint_with_name.",".$request->joint_other_name;
					}else{
						$joint_other_name = $property_joint_with_name;
					}
    			


				//die('yes1');

	    		$insert = new AffAgriculturalLand;
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
				$insert->affidavit_id			= Session::get('affidavit_id');
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->location 				= $xss->clean_input($request->location);
	            $insert->survey_number 			= $xss->clean_input($request->survey_number);
	            $insert->area 					= $xss->clean_input($request->area);
	            $insert->property_type_id 		= $xss->clean_input($request->property_type_id);
	            $insert->property_joint_with 	= $property_joint_with;
	            $insert->property_joint_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name 		= $xss->clean_input($request->joint_other_name);
	            $insert->inherited_property 	= $xss->clean_input($request->inherited_property);
	            $insert->date_of_purchase 		= $xss->clean_input($request->date_of_purchase);
	            $insert->cost_at_purchase_time 	= $xss->clean_input($request->cost_at_purchase_time);
	            $insert->investment_on_land 	= $xss->clean_input($request->investment_on_land);
	            $insert->approx_current_market_value = $xss->clean_input($request->approx_current_market_value);
	            $insert->added_create_at 		= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();

				//dd($insert);


	            $result = array("id" =>$insert->id,
	            				"property_joint_with" =>$property_joint_with,
	            				"property_joint_with_name" =>"with ".$joint_other_name,
	            				"joint_other_name" =>$request->joint_other_name,
								"date_of_purchase" => \Carbon\Carbon::parse($request->date_of_purchase)->format('d/m/Y')
	        	);
	        	echo json_encode($result);
    		 } catch (Exception $e) {
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}else 
    		echo 0;
    }
	
	
	public function update_agricultural_land(Request $request)
    {
    	$xss = new xssClean;
    	if( !empty($request->rel_type_id))
    	{
    		
			//dd($request->all());
			
			
    		$property_joint_with = "";
    		$property_joint_with_name = "";
    		$joint_other_name = "";
    		try {
    			if( !empty($request->property_joint_with) )
    			{
					
	    			$joint_id = array();
					$joint_name = array();
					
	    			foreach($request->property_joint_with as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$property_joint_with = implode(', ', $joint_id);
	    			$property_joint_with_name = implode(', ', $joint_name);
	   
				}
					if(!empty($request->joint_other_name)){
	    					$joint_other_name = $property_joint_with_name.",".$request->joint_other_name;
					}else{
						$joint_other_name = $property_joint_with_name;
					}
    			
				
				
				
				$insert = AffAgriculturalLand::find($request->id);
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->location 				= $xss->clean_input($request->location);
	            $insert->survey_number 			= $xss->clean_input($request->survey_number);
	            $insert->area 					= $xss->clean_input($request->area);
	            $insert->property_type_id 		= $xss->clean_input($request->property_type_id);
	            $insert->property_joint_with 	= $property_joint_with;
	            $insert->property_joint_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name 		= $xss->clean_input($request->joint_other_name);
	            $insert->inherited_property 	= $xss->clean_input($request->inherited_property);
	            $insert->date_of_purchase 		= $xss->clean_input($request->date_of_purchase);
	            $insert->cost_at_purchase_time 	= $xss->clean_input($request->cost_at_purchase_time);
	            $insert->investment_on_land 	= $xss->clean_input($request->investment_on_land);
	            $insert->approx_current_market_value = $xss->clean_input($request->approx_current_market_value);
	            $insert->updated_at 			= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();
				
	            $result = array("id" =>$insert->id,
	            				"property_joint_with" =>$property_joint_with,
	            				"property_joint_with_name" =>"with ".$joint_other_name,
	            				"joint_other_name" =>$request->joint_other_name,
								"date_of_purchase" => \Carbon\Carbon::parse($request->date_of_purchase)->format('d/m/Y')
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


	public function save_non_agricultural_land(Request $request)
    {
    	$xss = new xssClean;
		
		
		
    	if( !empty($request->rel_type_id))
    	{
			//dd($request->all());
			
    		$property_joint_with = "";
    		$property_joint_with_name = "";
    		$joint_other_name = "";
    		try {
    			if(!empty($request->property_joint_with))
    			{
					
	    			$joint_id = array();
					$joint_name = array();
					//die($request->property_joint_with_name);
					
	    			foreach($request->property_joint_with as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$property_joint_with = implode(', ', $joint_id);
	    			$property_joint_with_name = implode(', ', $joint_name);
				}
					if(!empty($request->joint_other_name)){
	    					$joint_other_name = $property_joint_with_name.",".$request->joint_other_name;
					}else{
						$joint_other_name = $property_joint_with_name;
					}
    			


				//die('yes1');

	    		$insert = new AffNonAgriculturalLand;
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
				$insert->affidavit_id			= Session::get('affidavit_id');
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->location 				= $xss->clean_input($request->location);
	            $insert->survey_number 			= $xss->clean_input($request->survey_number);
	            $insert->area 					= $xss->clean_input($request->area);
	            $insert->property_type_id 		= $xss->clean_input($request->property_type_id);
	            $insert->property_joint_with 	= $property_joint_with;
	            $insert->property_joint_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name 		= $xss->clean_input($request->joint_other_name);
	            $insert->inherited_property 	= $xss->clean_input($request->inherited_property);
	            $insert->date_of_purchase 		= $xss->clean_input($request->date_of_purchase);
	            $insert->cost_at_purchase_time 	= $xss->clean_input($request->cost_at_purchase_time);
	            $insert->investment_on_land 	= $xss->clean_input($request->investment_on_land);
	            $insert->approx_current_market_value = $xss->clean_input($request->approx_current_market_value);
	            $insert->added_create_at 		= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();

				$result = array("id" =>$insert->id,
	            				"property_joint_with" =>$property_joint_with,
	            				"property_joint_with_name" =>"with ".$joint_other_name,
	            				"joint_other_name" =>$request->joint_other_name,
								"date_of_purchase" => \Carbon\Carbon::parse($request->date_of_purchase)->format('d/m/Y')
	        	);
	        	echo json_encode($result);
    		 } catch (Exception $e) {
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}else 
    		echo 0;
    }


	public function update_non_agricultural_land(Request $request)
    {
    	$xss = new xssClean;
    	if( !empty($request->rel_type_id))
    	{
    		
			//dd($request->all());
			
			
    		$property_joint_with = "";
    		$property_joint_with_name = "";
    		$joint_other_name = "";
    		try {
    			if( !empty($request->property_joint_with) )
    			{
					
	    			$joint_id = array();
					$joint_name = array();
					
	    			foreach($request->property_joint_with as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$property_joint_with = implode(', ', $joint_id);
	    			$property_joint_with_name = implode(', ', $joint_name);
	   
				}
					if(!empty($request->joint_other_name)){
	    					$joint_other_name = $property_joint_with_name.",".$request->joint_other_name;
					}else{
						$joint_other_name = $property_joint_with_name;
					}
    			
				
				
				
				$insert = AffNonAgriculturalLand::find($request->id);
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->location 				= $xss->clean_input($request->location);
	            $insert->survey_number 			= $xss->clean_input($request->survey_number);
	            $insert->area 					= $xss->clean_input($request->area);
	            $insert->property_type_id 		= $xss->clean_input($request->property_type_id);
	            $insert->property_joint_with 	= $property_joint_with;
	            $insert->property_joint_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name 		= $xss->clean_input($request->joint_other_name);
	            $insert->inherited_property 	= $xss->clean_input($request->inherited_property);
	            $insert->date_of_purchase 		= $xss->clean_input($request->date_of_purchase);
	            $insert->cost_at_purchase_time 	= $xss->clean_input($request->cost_at_purchase_time);
	            $insert->investment_on_land 	= $xss->clean_input($request->investment_on_land);
	            $insert->approx_current_market_value = $xss->clean_input($request->approx_current_market_value);
	            $insert->updated_at 			= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();
				
	            $result = array("id" =>$insert->id,
	            				"property_joint_with" =>$property_joint_with,
	            				"property_joint_with_name" =>"with ".$joint_other_name,
	            				"joint_other_name" =>$request->joint_other_name,
								"date_of_purchase" => \Carbon\Carbon::parse($request->date_of_purchase)->format('d/m/Y')
	        	);
	        	echo json_encode($result);
    		 }catch (Exception $e){
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }




		public function save_commercial(Request $request)
		{
    	$xss = new xssClean;
		
		
		
    	if( !empty($request->rel_type_id))
    	{
			//dd($request->all());
			
    		$property_joint_with = "";
    		$property_joint_with_name = "";
    		$joint_other_name = "";
    		try {
    			if(!empty($request->property_joint_with))
    			{
					
	    			$joint_id = array();
					$joint_name = array();
					//die($request->property_joint_with_name);
					
	    			foreach($request->property_joint_with as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
					
					
	    			$property_joint_with = implode(', ', $joint_id);
	    			$property_joint_with_name = implode(', ', $joint_name);
					
					//die($property_joint_with_name);
				}
					if(!empty($request->joint_other_name)){
	    					$joint_other_name = $property_joint_with_name.",".$request->joint_other_name;
					}else{
						$joint_other_name = $property_joint_with_name;
					}
				


				//die('yes1');

	    		$insert = new AffCommercialBuildings;
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
				$insert->affidavit_id			= Session::get('affidavit_id');
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->location 				= $xss->clean_input($request->location);
	            $insert->survey_number 			= $xss->clean_input($request->survey_number);
	            $insert->area 					= $xss->clean_input($request->area);
	            $insert->built_up_area 			= $xss->clean_input($request->built_up_area);
	            $insert->property_type_id 		= $xss->clean_input($request->property_type_id);
	            $insert->property_joint_with 	= $property_joint_with;
	            $insert->property_joint_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name 		= $xss->clean_input($request->joint_other_name);
	            $insert->inherited_property 	= $xss->clean_input($request->inherited_property);
	            $insert->date_of_purchase 		= $xss->clean_input($request->date_of_purchase);
	            $insert->cost_at_purchase_time 	= $xss->clean_input($request->cost_at_purchase_time);
	            $insert->investment_on_buildings 	= $xss->clean_input($request->investment_on_buildings);
	            $insert->approx_current_market_value = $xss->clean_input($request->approx_current_market_value);
	            $insert->added_create_at 		= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();

				$result = array("id" =>$insert->id,
	            				"property_joint_with" =>$property_joint_with,
	            				"property_joint_with_name" =>"with ".$joint_other_name,
	            				"joint_other_name" =>$request->joint_other_name,
								"date_of_purchase" => \Carbon\Carbon::parse($request->date_of_purchase)->format('d/m/Y')
	        	);
	        	echo json_encode($result);
    		 } catch (Exception $e) {
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}else 
    		echo 0;
    }
	
	
	public function update_commercial(Request $request)
    {
    	$xss = new xssClean;
    	if( !empty($request->rel_type_id))
    	{
    		
			//dd($request->all());
			
			
    		$property_joint_with = "";
    		$property_joint_with_name = "";
    		$joint_other_name = "";
    		//try {
    			if( !empty($request->property_joint_with) )
    			{
					
	    			$joint_id = array();
					$joint_name = array();
					
	    			foreach($request->property_joint_with as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$property_joint_with = implode(', ', $joint_id);
	    			$property_joint_with_name = implode(', ', $joint_name);
				}
					if(!empty($request->joint_other_name)){
	    					$joint_other_name = $property_joint_with_name.",".$request->joint_other_name;
					}else{
						$joint_other_name = $property_joint_with_name;
					}
    			
				
				
				
				$insert = AffCommercialBuildings::find($request->id);
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->location 				= $xss->clean_input($request->location);
	            $insert->survey_number 			= $xss->clean_input($request->survey_number);
	            $insert->area 					= $xss->clean_input($request->area);
	            $insert->built_up_area 			= $xss->clean_input($request->built_up_area);
	            $insert->property_type_id 		= $xss->clean_input($request->property_type_id);
	            $insert->property_joint_with 	= $property_joint_with;
	            $insert->property_joint_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name 		= $xss->clean_input($request->joint_other_name);
	            $insert->inherited_property 	= $xss->clean_input($request->inherited_property);
	            $insert->date_of_purchase 		= $xss->clean_input($request->date_of_purchase);
	            $insert->cost_at_purchase_time 	= $xss->clean_input($request->cost_at_purchase_time);
	            $insert->investment_on_buildings 	= $xss->clean_input($request->investment_on_buildings);
	            $insert->approx_current_market_value = $xss->clean_input($request->approx_current_market_value);
	            $insert->updated_at 			= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();
				
	            $result = array("id" =>$insert->id,
	            				"property_joint_with" =>$property_joint_with,
	            				"property_joint_with_name" =>"with ".$joint_other_name,
	            				"joint_other_name" =>$request->joint_other_name,
								"date_of_purchase" => \Carbon\Carbon::parse($request->date_of_purchase)->format('d/m/Y')
	        	);
	        	echo json_encode($result);
    		 /* }catch (Exception $e){
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			} */
    	}
    	else 
    		echo 0;
    }
	
	
	
	public function save_residential(Request $request)
		{
    	$xss = new xssClean;
		
		
		
    	if( !empty($request->rel_type_id))
    	{
			//dd($request->all());
			
    		$property_joint_with = "";
    		$property_joint_with_name = "";
    		$joint_other_name = "";
    		try {
    			if(!empty($request->property_joint_with))
    			{
					
	    			$joint_id = array();
					$joint_name = array();
					//die($request->property_joint_with_name);
					
	    			foreach($request->property_joint_with as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
					
					
	    			$property_joint_with = implode(', ', $joint_id);
	    			$property_joint_with_name = implode(', ', $joint_name);
					
					//die($property_joint_with_name);
				}
					if(!empty($request->joint_other_name)){
	    					$joint_other_name = $property_joint_with_name.",".$request->joint_other_name;
					}else{
						$joint_other_name = $property_joint_with_name;
					}
				

	    		$insert = new AffResidentialBuildings;
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
				$insert->affidavit_id			= Session::get('affidavit_id');
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->location 				= $xss->clean_input($request->location);
	            $insert->survey_number 			= $xss->clean_input($request->survey_number);
	            $insert->area 					= $xss->clean_input($request->area);
	            $insert->built_up_area 			= $xss->clean_input($request->built_up_area);
	            $insert->property_type_id 		= $xss->clean_input($request->property_type_id);
	            $insert->property_joint_with 	= $property_joint_with;
	            $insert->property_joint_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name 		= $xss->clean_input($request->joint_other_name);
	            $insert->inherited_property 	= $xss->clean_input($request->inherited_property);
	            $insert->date_of_purchase 		= $xss->clean_input($request->date_of_purchase);
	            $insert->cost_at_purchase_time 	= $xss->clean_input($request->cost_at_purchase_time);
	            $insert->investment_on_buildings 	= $xss->clean_input($request->investment_on_buildings);
	            $insert->approx_current_market_value = $xss->clean_input($request->approx_current_market_value);
	            $insert->added_create_at 		= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();

				$result = array("id" =>$insert->id,
	            				"property_joint_with" =>$property_joint_with,
	            				"property_joint_with_name" =>"with ".$joint_other_name,
	            				"joint_other_name" =>$request->joint_other_name,
								"date_of_purchase" => \Carbon\Carbon::parse($request->date_of_purchase)->format('d/m/Y')
	        	);
	        	echo json_encode($result);
    		 } catch (Exception $e) {
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}else 
    		echo 0;
    }
	
	public function update_residential(Request $request)
    {
    	$xss = new xssClean;
    	if( !empty($request->rel_type_id))
    	{
    		
			//dd($request->all());
			
			
    		$property_joint_with = "";
    		$property_joint_with_name = "";
    		$joint_other_name = "";
    		try {
    			if( !empty($request->property_joint_with) )
    			{
					
	    			$joint_id = array();
					$joint_name = array();
					
	    			foreach($request->property_joint_with as $row)
	    			{
	    				$joint_split = explode("-", $row);
				    	array_push($joint_id, $joint_split[0]);
				    	array_push($joint_name, $joint_split[1]);
	    			}
	    			$property_joint_with = implode(', ', $joint_id);
	    			$property_joint_with_name = implode(', ', $joint_name);
				}
					if(!empty($request->joint_other_name)){
	    					$joint_other_name = $property_joint_with_name.",".$request->joint_other_name;
					}else{
						$joint_other_name = $property_joint_with_name;
					}
    			
				
				
				
				$insert = AffResidentialBuildings::find($request->id);
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->location 				= $xss->clean_input($request->location);
	            $insert->survey_number 			= $xss->clean_input($request->survey_number);
	            $insert->area 					= $xss->clean_input($request->area);
	            $insert->built_up_area 			= $xss->clean_input($request->built_up_area);
	            $insert->property_type_id 		= $xss->clean_input($request->property_type_id);
	            $insert->property_joint_with 	= $property_joint_with;
	            $insert->property_joint_with_name = "with ".$joint_other_name ;
	            $insert->joint_other_name 		= $xss->clean_input($request->joint_other_name);
	            $insert->inherited_property 	= $xss->clean_input($request->inherited_property);
	            $insert->date_of_purchase 		= $xss->clean_input($request->date_of_purchase);
	            $insert->cost_at_purchase_time 	= $xss->clean_input($request->cost_at_purchase_time);
	            $insert->investment_on_buildings 	= $xss->clean_input($request->investment_on_buildings);
	            $insert->approx_current_market_value = $xss->clean_input($request->approx_current_market_value);
	            $insert->updated_at 			= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();
				
	            $result = array("id" =>$insert->id,
	            				"property_joint_with" =>$property_joint_with,
	            				"property_joint_with_name" =>"with ".$joint_other_name,
	            				"joint_other_name" =>$request->joint_other_name,
	            				"date_of_purchase" => \Carbon\Carbon::parse($request->date_of_purchase)->format('d/m/Y')
	        	);
	        	echo json_encode($result);
    		 }catch (Exception $e){
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
	
	public function save_other_immovable(Request $request)
		{
    	$xss = new xssClean;

    	if( !empty($request->rel_type_id))
    	{
			//dd($request->all());
    		try {
	    		$insert = new AffOtherImmovableAssets;
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
				$insert->affidavit_id			= Session::get('affidavit_id');
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->brief_details 			= $xss->clean_input($request->brief_details);
	            $insert->amount 				= $xss->clean_input($request->amount);
	            $insert->added_create_at 		= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();

				$result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		 } catch (Exception $e) {
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}else 
    		echo 0;
    }
	
	
		public function update_other_immovable(Request $request)
    {
    	$xss = new xssClean;
    	if( !empty($request->rel_type_id))
    	{
    		
			//dd($request->all());
			

    		try {
    			
				
				
				$insert = AffOtherImmovableAssets::find($request->id);
	            $insert->candidate_id 			= $xss->clean_input($request->cand_id);
	            $insert->relation_type_code 	= $xss->clean_input($request->rel_type_id);
	            $insert->brief_details 			= $xss->clean_input($request->brief_details);
	            $insert->amount 				= $xss->clean_input($request->amount);
	            $insert->updated_at 			= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();
				
	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		 }catch (Exception $e){
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }
	
	public function delete_agricultural_land(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffAgriculturalLand::destroy($request->id);
				AffAgriculturalLand::where('id',$request->id)->update(['is_deleted' => '1']);
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
	
	
	public function delete_non_agricultural_land(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffNonAgriculturalLand::destroy($request->id);
				AffNonAgriculturalLand::where('id',$request->id)->update(['is_deleted' => '1']);
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
	
	public function delete_commercial(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffCommercialBuildings::destroy($request->id);
				AffCommercialBuildings::where('id',$request->id)->update(['is_deleted' => '1']);
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
	
	
	public function delete_residential(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffResidentialBuildings::destroy($request->id);
				AffResidentialBuildings::where('id',$request->id)->update(['is_deleted' => '1']);
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
	public function delete_other_immovable(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffOtherImmovableAssets::destroy($request->id);
				AffOtherImmovableAssets::where('id',$request->id)->update(['is_deleted' => '1']);
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
