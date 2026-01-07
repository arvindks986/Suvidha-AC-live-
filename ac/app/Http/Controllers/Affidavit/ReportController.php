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
use App\models\Affidavit\AffAgriculturalLand;
use App\models\Affidavit\AffNonAgriculturalLand;
use App\models\Affidavit\AffCommercialBuildings;
use App\models\Affidavit\AffResidentialBuildings;
use App\models\Affidavit\AffOtherImmovableAssets;

use App\models\Affidavit\AffSelfSpouseOccupation;
use App\models\Affidavit\AffDependantSourceOfIncome;
use App\models\Affidavit\AffGovtPublicCompanyContract;
use App\models\Affidavit\AffHufTrustContracts;
use App\models\Affidavit\AffPartnershipFirmContracts;
use App\models\Affidavit\AffPrivateCompanyContract;

use App\models\Affidavit\AffEducation;


use Exception;
use PDF;
use DB;
use Log;
use App\Classes\xssClean;
use Carbon\Carbon;

use App\commonModel;
use App\adminmodel\CandidateModel;

class ReportController extends Controller
{
	public function __construct(){   
        $this->commonModel = new commonModel();
		$this->CandidateModel = new CandidateModel();
    }
		
	public function part_a_detailed_report(Request $request)
    {
		
    	try {
			
			$data = array();
			
			$affidavit_id = '';
			
			if($request->affidavit_id){
				$affidavit_id = $request->affidavit_id;
				$affidavit_yes = 1;
			}else{
				$affidavit_id = Session::get('affidavit_id');
				$affidavit_yes = 0;
			}
			
			
			if($affidavit_id){
			/* $data = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();
				if($data->finalized == '1'){
					return redirect()->to('part-a-detailed-report')->with('success','Your Affidavit is allready Finalized.');
				} */
			}else{
				return redirect()->to('affidavitdashboard')->with('success','Please select State and constituency.');
			}
			
			
			
			$data['affidavit_yes'] = $affidavit_yes;
			
			//dd($affidavit_id);
			
	    	$cand_details = DB::table('aff_cand_details');
	    	//->where("aff_cand_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$cand_details->where("aff_cand_details.affidavit_id", $affidavit_id);
			}
	    	$cand_details = $cand_details->first();
			
			$data['cand_details'] = $cand_details;
			
			//dd($data);
			
			$year = getElectionYear();
			$destination_path ='uploads1/affidavit/qrcode/'.$year.'/ac/'.$cand_details->st_code.'/'.$cand_details->ac_no.'/';
			
			if (!file_exists($destination_path)) {
			  mkdir($destination_path, 0777, true);
			}
			
			$destination_path = $destination_path.$affidavit_id.'.png';		

			
			
			\QRCode::text($affidavit_id)->setOutfile($destination_path)->png();
			$data['qrcode'] = $destination_path;
			


			 $non_relation_details = DB::table('aff_m_relation_type')->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	 ->get();
	    	  $non_relation_details_count = DB::table('aff_m_relation_type')->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	   ->get()->toArray();
    $data['non_relation_details']=$non_relation_details;
         $data['non_relation_details_count']=count($non_relation_details);
			
			$social_media = DB::table('aff_cand_social_media');
	    	//->where("aff_cand_social_media.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$social_media->where("aff_cand_social_media.affidavit_id", $affidavit_id);
			}
	    	$social_media=$social_media->get();
			
			$data['social_media'] = $social_media;
			
			
			$pan_details = AffPanDetail::leftjoin("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
	    	->select("aff_pan_details.*", "aff_m_relation_type.relation_type");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$pan_details->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	//$pan_details = $pan_details->groupBy("aff_pan_details.pan")
	    	$pan_details = $pan_details->where('is_deleted','0')->orderBy("aff_pan_details.relation_type_code", "ASC")
	    	->get();
			

          $pan_details =DB::table('aff_pan_details')
			->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_pan_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_pan_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    }
				})
				->select("aff_pan_details.*", "aff_m_relation_type.relation_type")->get();



			$data['pan_details'] = $pan_details;
			
			
			$pending_cases = DB::table('aff_pending_criminal_cases')->where('is_deleted','0');
	    	//->where("aff_pending_criminal_cases.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$pending_cases->where("aff_pending_criminal_cases.affidavit_id", $affidavit_id);
			}
	    	$pending_cases = $pending_cases->get();
			
			$data['pending_cases'] = $pending_cases;
			
			$imprisonment_criminal = DB::table('aff_imprisonment_criminal_cases')->where('is_deleted','0');
	    	//->where("aff_imprisonment_criminal_cases.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$imprisonment_criminal->where("aff_imprisonment_criminal_cases.affidavit_id", $affidavit_id);
			}
	    	$imprisonment_criminal = $imprisonment_criminal->get();
			
			$data['imprisonment_criminal'] = $imprisonment_criminal;
			
			
		$cash_in_hand = DB::table('aff_cash_in_hand')
			

			 
			  
             // ->rightjoin("aff_m_relation_type", "aff_cash_in_hand.relation_type_code", "=", "aff_m_relation_type.relation_type_code")

             ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_cash_in_hand.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_cash_in_hand.affidavit_id','=', \DB::raw("'$affidavit_id'"));

				    }
				})

             ->leftjoin('aff_pan_details',[['aff_cash_in_hand.affidavit_id','aff_pan_details.affidavit_id']])


			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_cash_in_hand.cash_in_hand');
	    
			 if($affidavit_id){
			 	$cash_in_hand = $cash_in_hand->groupBy("aff_m_relation_type.relation_type_code")->get();
			 }
	    	//$cash_in_hand = $cash_in_hand->where('is_deleted','0');
			
			$data['cash_in_hand'] = $cash_in_hand;
			
			
			$bank_details = DB::table('aff_bank_details')
			->join("aff_m_deposit_type", "aff_bank_details.deposit_type_id", "=", "aff_m_deposit_type.deposit_type_id")
			->rightjoin('aff_pan_details',[['aff_bank_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_bank_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_bank_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_deposit_type.deposit_type','aff_bank_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$bank_details->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$bank_details = $bank_details->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get();
			
			$data['bank_details'] = $bank_details;
			
			
			$investment_details = DB::table('aff_investment_in_companies')
			->join("aff_m_company_investment_type", "aff_investment_in_companies.company_investment_type_id", "=", "aff_m_company_investment_type.company_investment_type_id")
			->rightjoin('aff_pan_details',[['aff_investment_in_companies.affidavit_id','aff_pan_details.affidavit_id'],['aff_investment_in_companies.relation_type_code','aff_pan_details.relation_type_code'],['aff_investment_in_companies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_company_investment_type.company_investment_type','aff_investment_in_companies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$investment_details->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$investment_details = $investment_details->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get();
			
			$data['investment_details'] = $investment_details;
			
			$savings_and_policies = DB::table('aff_savings_and_policies')
			->join("aff_m_saving_policies_type", "aff_savings_and_policies.saving_type_id", "=", "aff_m_saving_policies_type.saving_type_id")
			->rightjoin('aff_pan_details',[['aff_savings_and_policies.affidavit_id','aff_pan_details.affidavit_id'],['aff_savings_and_policies.relation_type_code','aff_pan_details.relation_type_code'],['aff_savings_and_policies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_saving_policies_type.saving_type','aff_savings_and_policies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$savings_and_policies->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$savings_and_policies = $savings_and_policies->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get();
			
			$data['savings_and_policies'] = $savings_and_policies;
			
			
			$loan_details = DB::table('aff_loan_details')
			->join("aff_m_loan_type", "aff_loan_details.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
			->rightjoin('aff_pan_details',[['aff_loan_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_loan_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_loan_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_loan_type.loan_type','aff_loan_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$loan_details->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$loan_details = $loan_details->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get();
			
			$data['loan_details'] = $loan_details;
			
			$vehicle_details = DB::table('aff_vehicle_details')
			->join("aff_m_vehicle_type", "aff_vehicle_details.vehicle_type_id", "=", "aff_m_vehicle_type.vehicle_type_id")
			->rightjoin('aff_pan_details',[['aff_vehicle_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_vehicle_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_vehicle_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_vehicle_type.vehicle_type','aff_vehicle_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$vehicle_details->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$vehicle_details = $vehicle_details->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get();
			
			$data['vehicle_details'] = $vehicle_details;
			
			
			
			$valuable_things_details = DB::table('aff_valuable_things_details')
			->join("aff_m_valuable_things", "aff_valuable_things_details.valuable_type_id", "=", "aff_m_valuable_things.valuable_type_id")
			->join("aff_m_valuable_weight", "aff_valuable_things_details.weight_unit_id", "=", "aff_m_valuable_weight.valuable_weight_id")
			->rightjoin('aff_pan_details',[['aff_valuable_things_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_valuable_things_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_valuable_things_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_valuable_things.valuable_type','aff_m_valuable_weight.valuable_weight','aff_valuable_things_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$valuable_things_details->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$valuable_things_details = $valuable_things_details->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get();
			
			$data['valuable_things_details'] = $valuable_things_details;
			
			
			$other_assets = DB::table('aff_other_assets')
			->rightjoin('aff_pan_details',[['aff_other_assets.affidavit_id','aff_pan_details.affidavit_id'],['aff_other_assets.relation_type_code','aff_pan_details.relation_type_code'],['aff_other_assets.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_other_assets.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$other_assets->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$other_assets = $other_assets->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get();
			
			$data['other_assets'] = $other_assets;
			
			
			$agricultural_land = AffAgriculturalLand::join("aff_m_property_type", "aff_agricultural_land_details.property_type_id", "=", "aff_m_property_type.property_type_id")
			->rightjoin('aff_pan_details',[['aff_agricultural_land_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_agricultural_land_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_agricultural_land_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
	    	->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_agricultural_land_details.*", "aff_m_property_type.property_type");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$agricultural_land->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$agricultural_land=$agricultural_land->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['agricultural_land'] = $agricultural_land;
			
			$non_agricultural_land = AffNonAgriculturalLand::join("aff_m_property_type", "aff_non_agricultural_land_details.property_type_id", "=", "aff_m_property_type.property_type_id")
			->rightjoin('aff_pan_details',[['aff_non_agricultural_land_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_non_agricultural_land_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_non_agricultural_land_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
	    	->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_non_agricultural_land_details.*", "aff_m_property_type.property_type");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$non_agricultural_land->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$non_agricultural_land=$non_agricultural_land->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['non_agricultural_land'] = $non_agricultural_land;
			
			$commercial_buildings = AffCommercialBuildings::join("aff_m_property_type", "aff_commercial_buildings_details.property_type_id", "=", "aff_m_property_type.property_type_id")
			->rightjoin('aff_pan_details',[['aff_commercial_buildings_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_commercial_buildings_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_commercial_buildings_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
	    	->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_commercial_buildings_details.*", "aff_m_property_type.property_type");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$commercial_buildings->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$commercial_buildings = $commercial_buildings->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['commercial_buildings'] = $commercial_buildings;
			
			$residential_buildings = AffResidentialBuildings::join("aff_m_property_type", "aff_residential_buildings_details.property_type_id", "=", "aff_m_property_type.property_type_id")
			->rightjoin('aff_pan_details',[['aff_residential_buildings_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_residential_buildings_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_residential_buildings_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
	    	->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_residential_buildings_details.*", "aff_m_property_type.property_type");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$residential_buildings->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$residential_buildings = $residential_buildings->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['residential_buildings'] = $residential_buildings;
			
			$other_immovable = AffOtherImmovableAssets::rightjoin('aff_pan_details',[['aff_other_immovable_assets.affidavit_id','aff_pan_details.affidavit_id'],['aff_other_immovable_assets.relation_type_code','aff_pan_details.relation_type_code'],['aff_other_immovable_assets.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_other_immovable_assets.*");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$other_immovable->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
	    	$other_immovable = $other_immovable->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['other_immovable'] = $other_immovable;
			
			
			
			
			$l_loan_details = DB::table('aff_l_loan_bank_finc_inst')
			->join("aff_m_loan_type", "aff_l_loan_bank_finc_inst.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
			->rightjoin('aff_pan_details',[['aff_l_loan_bank_finc_inst.affidavit_id','aff_pan_details.affidavit_id'],['aff_l_loan_bank_finc_inst.relation_type_code','aff_pan_details.relation_type_code'],['aff_l_loan_bank_finc_inst.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_loan_type.loan_type','aff_l_loan_bank_finc_inst.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$l_loan_details->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$l_loan_details = $l_loan_details->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['l_loan_details'] = $l_loan_details;
			
			$l_loan_individual = DB::table('aff_l_loan_individual_entity')
			->join("aff_m_loan_type", "aff_l_loan_individual_entity.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
			->rightjoin('aff_pan_details',[['aff_l_loan_individual_entity.affidavit_id','aff_pan_details.affidavit_id'],['aff_l_loan_individual_entity.relation_type_code','aff_pan_details.relation_type_code'],['aff_l_loan_individual_entity.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_loan_type.loan_type','aff_l_loan_individual_entity.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$l_loan_individual->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$l_loan_individual = $l_loan_individual->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['l_loan_individual'] = $l_loan_individual;
			
			$l_other_liabilities = DB::table('aff_l_other_liabilities')
			->rightjoin('aff_pan_details',[['aff_l_other_liabilities.affidavit_id','aff_pan_details.affidavit_id'],['aff_l_other_liabilities.relation_type_code','aff_pan_details.relation_type_code'],['aff_l_other_liabilities.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_l_other_liabilities.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$l_other_liabilities->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$l_other_liabilities = $l_other_liabilities->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['l_other_liabilities'] = $l_other_liabilities;
			
			$l_govt_dues = DB::table('aff_l_govt_dues')
			->join("aff_m_govt_dept_name", "aff_l_govt_dues.govt_dept_name_code", "=", "aff_m_govt_dept_name.govt_dept_name_code")
			->rightjoin('aff_pan_details',[['aff_l_govt_dues.affidavit_id','aff_pan_details.affidavit_id'],['aff_l_govt_dues.relation_type_code','aff_pan_details.relation_type_code'],['aff_l_govt_dues.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_l_govt_dues.*','aff_m_govt_dept_name.govt_dept_name');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$l_govt_dues->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$l_govt_dues = $l_govt_dues->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['l_govt_dues'] = $l_govt_dues;
			
			$l_liabilities_disputes = DB::table('aff_l_liabilities_disputes')
			->rightjoin('aff_pan_details',[['aff_l_liabilities_disputes.affidavit_id','aff_pan_details.affidavit_id'],['aff_l_liabilities_disputes.relation_type_code','aff_pan_details.relation_type_code'],['aff_l_liabilities_disputes.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_l_liabilities_disputes.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$l_liabilities_disputes->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$l_liabilities_disputes = $l_liabilities_disputes->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['l_liabilities_disputes'] = $l_liabilities_disputes;
			


			
			
			$source_of_income = AffDependantSourceOfIncome::rightjoin('aff_pan_details',[['aff_dependant_source_of_income.affidavit_id','aff_pan_details.affidavit_id'],['aff_dependant_source_of_income.relation_type_code','aff_pan_details.relation_type_code'],['aff_dependant_source_of_income.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_dependant_source_of_income.*");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$source_of_income->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$source_of_income = $source_of_income->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['source_of_income'] = $source_of_income;


			$govt_public_company = AffGovtPublicCompanyContract::rightjoin('aff_pan_details',[['aff_govt_public_company_contract.affidavit_id','aff_pan_details.affidavit_id'],['aff_govt_public_company_contract.relation_type_code','aff_pan_details.relation_type_code'],['aff_govt_public_company_contract.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_govt_public_company_contract.*");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$govt_public_company->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$govt_public_company = $govt_public_company->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['govt_public_company'] = $govt_public_company;
			
			$huf_trust = AffHufTrustContracts::rightjoin('aff_pan_details',[['aff_huf_trust_contracts.affidavit_id','aff_pan_details.affidavit_id'],['aff_huf_trust_contracts.relation_type_code','aff_pan_details.relation_type_code'],['aff_huf_trust_contracts.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_huf_trust_contracts.*");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$huf_trust->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$huf_trust = $huf_trust->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['huf_trust'] = $huf_trust;

			$partnership_firm = AffPartnershipFirmContracts::rightjoin('aff_pan_details',[['aff_partnership_firm_contracts.affidavit_id','aff_pan_details.affidavit_id'],['aff_partnership_firm_contracts.relation_type_code','aff_pan_details.relation_type_code'],['aff_partnership_firm_contracts.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_partnership_firm_contracts.*");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$partnership_firm->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$partnership_firm = $partnership_firm->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['partnership_firm'] = $partnership_firm;
			
			$private_company = AffPrivateCompanyContract::rightjoin('aff_pan_details',[['aff_private_company_contracts.affidavit_id','aff_pan_details.affidavit_id'],['aff_private_company_contracts.relation_type_code','aff_pan_details.relation_type_code'],['aff_private_company_contracts.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type","aff_private_company_contracts.*");
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$private_company->where("aff_pan_details.affidavit_id", $affidavit_id);
			}
			$private_company = $private_company->where('aff_pan_details.is_deleted','0')
			->orderBy("aff_m_relation_type.relation_type_code", "ASC")
	    	->get();
			
			$data['private_company'] = $private_company;


	    	$education = AffEducation::select("aff_cand_qualification.*");
			//->where("aff_cand_qualification.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$education->where("aff_cand_qualification.affidavit_id", $affidavit_id);
			}
			$education = $education->where('is_deleted','0')->get();
			
			$data['education'] = $education;
			
			
			$pending_cases_count = DB::table('aff_pending_criminal_cases')
	    	//->where("aff_pending_criminal_cases.candidate_id", Auth::user()->id)
	    	->where('aff_pending_criminal_cases.not_applicable',null);
			if($affidavit_id){
				$pending_cases_count->where("aff_pending_criminal_cases.affidavit_id", $affidavit_id);
			}
			$pending_cases_count = $pending_cases_count->where('is_deleted','0')->get();
			
			$data['pending_cases_count'] = $pending_cases_count;

			// dd($pending_cases_count);

			 $pending_cases_check = DB::table('aff_pending_criminal_cases')->where('is_deleted','0');
	    	//->where("aff_pending_criminal_cases.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$pending_cases_check->where("aff_pending_criminal_cases.affidavit_id", $affidavit_id);
			}
	    	$pending_cases_check = $pending_cases_check->get()->toArray();
	    	//dd($pending_cases_check[0]->fir_no);
			
			$data['pending_cases_check'] = $pending_cases_check;
			
			$imprisonment_criminal_count = DB::table('aff_imprisonment_criminal_cases')
	    	//->where("aff_imprisonment_criminal_cases.candidate_id", Auth::user()->id)
			->where('aff_imprisonment_criminal_cases.not_applicable',null);
	    	if($affidavit_id){
				$imprisonment_criminal_count->where("aff_imprisonment_criminal_cases.affidavit_id", $affidavit_id);
			}
			$imprisonment_criminal_count = $imprisonment_criminal_count->where('is_deleted','0')->get();
			
			$data['imprisonment_criminal_count'] = $imprisonment_criminal_count;
				$imprisonment_criminal_check = DB::table('aff_imprisonment_criminal_cases')->where('is_deleted','0');
	    	//->where("aff_imprisonment_criminal_cases.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$imprisonment_criminal_check->where("aff_imprisonment_criminal_cases.affidavit_id", $affidavit_id);
			}
	    	$imprisonment_criminal_check = $imprisonment_criminal_check->get()->toArray();
			
			$data['imprisonment_criminal_check'] = $imprisonment_criminal_check;


			
			//$candidate_id = Auth::user()->id;
			
			$movable_assets_total = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.total,t2.Other_Mov_Asset from aff_pan_details pdt
join  aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
select affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
cash_in_hand+ Bank_Deposit+investment+saving+outstanding+vehicle_amount+valuable_amount+other_amount as total
from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(ch.cash_in_hand,0)as cash_in_hand,IFNULL(b.Bank_Deposit, 0) AS Bank_Deposit,
IFNULL(c.investment, 0)AS investment, IFNULL(d.saving, 0)AS saving,
IFNULL(e.outstanding, 0) AS outstanding, IFNULL(f.vehicle_amount, 0) AS vehicle_amount,
IFNULL(g.valuable_amount, 0) AS valuable_amount,
IFNULL(h.other_amount, 0) AS other_amount
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(CASH_IN_HAND) as cash_in_hand
 FROM aff_cash_in_hand
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS ch ON a.affidavit_id = ch.affidavit_id
 AND a.RELATION_TYPE_CODE = ch.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS Bank_Deposit
 FROM aff_bank_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id = b.affidavit_id AND
 a.RELATION_TYPE_CODE = b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS investment
 FROM aff_investment_in_companies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id = c.affidavit_id AND
 a.RELATION_TYPE_CODE = c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS saving
 FROM aff_savings_and_policies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id = d.affidavit_id AND
 a.RELATION_TYPE_CODE = d.RELATION_TYPE_CODE
LEFT OUTER JOIN
 (SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(OUTSTANDING_AMOUNT, 0)), 0) AS outstanding
  FROM aff_loan_details where is_deleted = '0'
  GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id = e.affidavit_id AND
  a.RELATION_TYPE_CODE = e.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE, SUM(IFNULL(AMOUNT, 0)) AS vehicle_amount
 FROM aff_vehicle_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS f ON a.affidavit_id = f.affidavit_id AND
 a.RELATION_TYPE_CODE = f.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS valuable_amount
 FROM  aff_valuable_things_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS g ON a.affidavit_id = g.affidavit_id AND
 a.RELATION_TYPE_CODE = g.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(AMOUNT), 0) AS other_amount
 FROM  aff_other_assets where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS h ON a.affidavit_id = h.affidavit_id
AND a.RELATION_TYPE_CODE = h.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE,
a.NAME,ch.cash_in_hand, b.Bank_Deposit,c.investment,d.saving,e.outstanding,f.vehicle_amount,
g.valuable_amount,h.other_amount)t1
 )x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Mov_Asset,0) as Other_Mov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Mov_Asset
from aff_other_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t2
on pdt.affidavit_id=t2.affidavit_id and pdt.RELATION_TYPE_CODE=t2.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.is_deleted='0'
GROUP BY pdt.affidavit_id, pdt.RELATION_TYPE_CODE");
			
			
			//dd($movable_assets_total);
			
			
			$data['movable_assets_total'] = $movable_assets_total;
			
			
			$immoveable_assets_total = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.purcahse_price_self_acquired_immov, x2.Investment_Immov,x3.self_acquired_Assets_Value, x4.Inherited_assets_Value,t5.Other_Immov_Asset from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE, NAME,
agri_purchae_cost+nonagri_purchae_cost+com_purchae_cost+res_purchae_cost as
purcahse_price_self_acquired_immov  from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_purchae_cost,0) as agri_purchae_cost,
IFNULL(c.nonagri_purchae_cost,0) as nonagri_purchae_cost,
IFNULL(d.com_purchae_cost,0) as com_purchae_cost,IFNULL(e.res_purchae_cost,0) as res_purchae_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as agri_purchae_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as nonagri_purchae_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as com_purchae_cost
from aff_commercial_buildings_details  where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as res_purchae_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_purchae_cost,
c.nonagri_purchae_cost,d.com_purchae_cost,e.res_purchae_cost) t1)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_invest+nonagri_invest+com_invest+res_invest as Investment_Immov
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_invest,0) as agri_invest,
 IFNULL(c.nonagri_invest,0) as nonagri_invest,
 IFNULL(d.com_invest,0) as com_invest,IFNULL(e.res_invest,0) as res_invest
 FROM  aff_pan_details AS a
 JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as agri_invest from aff_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as nonagri_invest from aff_non_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(
SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as com_invest
from aff_commercial_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as res_invest
from aff_residential_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_invest,c.nonagri_invest,d.com_invest,e.res_invest
)t2
)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as self_acquired_Assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b
ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
 from aff_commercial_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_current_cost,
c.nonagri_current_cost,d.com_current_cost,e.res_current_cost
)t3)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as Inherited_assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
 from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
from aff_commercial_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id
AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_current_cost,c.nonagri_current_cost,
d.com_current_cost,e.res_current_cost
)t4)x4
on pdt.affidavit_id=x4.affidavit_id and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Immov_Asset,0) as Other_Immov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Immov_Asset
from aff_other_immovable_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t5
on pdt.affidavit_id=t5.affidavit_id and pdt.RELATION_TYPE_CODE=t5.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.is_deleted='0'
GROUP BY  pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			//dd($immoveable_assets_total);
			
			
			$data['immoveable_assets_total'] = $immoveable_assets_total;
			
			
			//dd($data['immoveable_assets_total']);
			
			$liabilites_total = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.is_deleted='0'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total'] = $liabilites_total;



/////////////////////////////////////

			$bank_details = DB::table('aff_bank_details')
			->join("aff_m_deposit_type", "aff_bank_details.deposit_type_id", "=", "aff_m_deposit_type.deposit_type_id")
			->rightjoin('aff_pan_details',[['aff_bank_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_bank_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_bank_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_deposit_type.deposit_type','aff_bank_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$bank_details->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 1)->where("aff_bank_details.is_deleted", 0);
			}
	    	$bank_details = $bank_details->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();

			//DB::raw("SUM(aff_bank_details.amount) as paidsum")
//dd($bank_details);
$data['bank_details_1'] = $bank_details;

						$bank_details_2 = DB::table('aff_bank_details')
			->join("aff_m_deposit_type", "aff_bank_details.deposit_type_id", "=", "aff_m_deposit_type.deposit_type_id")
			->rightjoin('aff_pan_details',[['aff_bank_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_bank_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_bank_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_deposit_type.deposit_type','aff_bank_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$bank_details_2->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 2)->where("aff_bank_details.is_deleted", 0);
			}
	    	$bank_details_2 = $bank_details_2->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			//dd($bank_details_2);
			$data['bank_details_2'] = $bank_details_2;


						$bank_details_3 = DB::table('aff_bank_details')
			->join("aff_m_deposit_type", "aff_bank_details.deposit_type_id", "=", "aff_m_deposit_type.deposit_type_id")
			->rightjoin('aff_pan_details',[['aff_bank_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_bank_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_bank_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_deposit_type.deposit_type','aff_bank_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$bank_details_3->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 3)->where("aff_bank_details.is_deleted", 0);
			}
	    	$bank_details_3 = $bank_details_3->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			//dd($bank_details_2);
			$data['bank_details_3'] = $bank_details_3;




						$bank_details_4 = DB::table('aff_bank_details')
			->join("aff_m_deposit_type", "aff_bank_details.deposit_type_id", "=", "aff_m_deposit_type.deposit_type_id")
			->rightjoin('aff_pan_details',[['aff_bank_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_bank_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_bank_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_deposit_type.deposit_type','aff_bank_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$bank_details_4->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 4)->where("aff_bank_details.is_deleted", 0);
			}
	    	$bank_details_4 = $bank_details_4->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			//dd($bank_details_2);
			$data['bank_details_4'] = $bank_details_4;
			
			//dd($bank_details_4);



						$bank_details_5 = DB::table('aff_bank_details')
			->join("aff_m_deposit_type", "aff_bank_details.deposit_type_id", "=", "aff_m_deposit_type.deposit_type_id")
			->rightjoin('aff_pan_details',[['aff_bank_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_bank_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_bank_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_deposit_type.deposit_type','aff_bank_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$bank_details_5->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 5)->where("aff_bank_details.is_deleted", 0);
			}
	    	$bank_details_5 = $bank_details_5->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			//dd($bank_details_2);
			$data['bank_details_5'] = $bank_details_5;

			$bank_details_6 = DB::table('aff_bank_details')
			->join("aff_m_deposit_type", "aff_bank_details.deposit_type_id", "=", "aff_m_deposit_type.deposit_type_id")
			->rightjoin('aff_pan_details',[['aff_bank_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_bank_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_bank_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_deposit_type.deposit_type','aff_bank_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$bank_details_6->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 6)->where("aff_bank_details.is_deleted", 0);
			}
	    	$bank_details_6 = $bank_details_6->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			//dd($bank_details_2);
			$data['bank_details_6'] = $bank_details_6;

			/////////////////////////////////////////////////////////////



$investment_details_1 = DB::table('aff_investment_in_companies')
			->join("aff_m_company_investment_type", "aff_investment_in_companies.company_investment_type_id", "=", "aff_m_company_investment_type.company_investment_type_id")
			->rightjoin('aff_pan_details',[['aff_investment_in_companies.affidavit_id','aff_pan_details.affidavit_id'],['aff_investment_in_companies.relation_type_code','aff_pan_details.relation_type_code'],['aff_investment_in_companies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_company_investment_type.company_investment_type','aff_investment_in_companies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$investment_details_1->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 1)->where("aff_investment_in_companies.is_deleted", 0);
			}
	    	$investment_details_1 = $investment_details_1->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['investment_details_1'] = $investment_details_1;


$investment_details_2 = DB::table('aff_investment_in_companies')
			->join("aff_m_company_investment_type", "aff_investment_in_companies.company_investment_type_id", "=", "aff_m_company_investment_type.company_investment_type_id")
			->rightjoin('aff_pan_details',[['aff_investment_in_companies.affidavit_id','aff_pan_details.affidavit_id'],['aff_investment_in_companies.relation_type_code','aff_pan_details.relation_type_code'],['aff_investment_in_companies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_company_investment_type.company_investment_type','aff_investment_in_companies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$investment_details_2->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 2)->where("aff_investment_in_companies.is_deleted", 0);
			}
	    	$investment_details_2 = $investment_details_2->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['investment_details_2'] = $investment_details_2;
			

$investment_details_3 = DB::table('aff_investment_in_companies')
			->join("aff_m_company_investment_type", "aff_investment_in_companies.company_investment_type_id", "=", "aff_m_company_investment_type.company_investment_type_id")
			->rightjoin('aff_pan_details',[['aff_investment_in_companies.affidavit_id','aff_pan_details.affidavit_id'],['aff_investment_in_companies.relation_type_code','aff_pan_details.relation_type_code'],['aff_investment_in_companies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_company_investment_type.company_investment_type','aff_investment_in_companies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$investment_details_3->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 3)->where("aff_investment_in_companies.is_deleted", 0);
			}
	    	$investment_details_3 = $investment_details_3->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['investment_details_3'] = $investment_details_3;


$investment_details_4 = DB::table('aff_investment_in_companies')
			->join("aff_m_company_investment_type", "aff_investment_in_companies.company_investment_type_id", "=", "aff_m_company_investment_type.company_investment_type_id")
			->rightjoin('aff_pan_details',[['aff_investment_in_companies.affidavit_id','aff_pan_details.affidavit_id'],['aff_investment_in_companies.relation_type_code','aff_pan_details.relation_type_code'],['aff_investment_in_companies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_company_investment_type.company_investment_type','aff_investment_in_companies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$investment_details_4->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 4)->where("aff_investment_in_companies.is_deleted", 0);
			}
	    	$investment_details_4 = $investment_details_4->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['investment_details_4'] = $investment_details_4;

$investment_details_5 = DB::table('aff_investment_in_companies')
			->join("aff_m_company_investment_type", "aff_investment_in_companies.company_investment_type_id", "=", "aff_m_company_investment_type.company_investment_type_id")
			->rightjoin('aff_pan_details',[['aff_investment_in_companies.affidavit_id','aff_pan_details.affidavit_id'],['aff_investment_in_companies.relation_type_code','aff_pan_details.relation_type_code'],['aff_investment_in_companies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_company_investment_type.company_investment_type','aff_investment_in_companies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$investment_details_5->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 5)->where("aff_investment_in_companies.is_deleted", 0);
			}
	    	$investment_details_5 = $investment_details_5->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['investment_details_5'] = $investment_details_5;


$investment_details_6 = DB::table('aff_investment_in_companies')
			->join("aff_m_company_investment_type", "aff_investment_in_companies.company_investment_type_id", "=", "aff_m_company_investment_type.company_investment_type_id")
			->rightjoin('aff_pan_details',[['aff_investment_in_companies.affidavit_id','aff_pan_details.affidavit_id'],['aff_investment_in_companies.relation_type_code','aff_pan_details.relation_type_code'],['aff_investment_in_companies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_company_investment_type.company_investment_type','aff_investment_in_companies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$investment_details_6->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 6)->where("aff_investment_in_companies.is_deleted", 0);
			}
	    	$investment_details_6 = $investment_details_6->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['investment_details_6'] = $investment_details_6;

			////////////////////////////////////////////////////////////////


$savings_and_policies_1 = DB::table('aff_savings_and_policies')
			->join("aff_m_saving_policies_type", "aff_savings_and_policies.saving_type_id", "=", "aff_m_saving_policies_type.saving_type_id")
			->rightjoin('aff_pan_details',[['aff_savings_and_policies.affidavit_id','aff_pan_details.affidavit_id'],['aff_savings_and_policies.relation_type_code','aff_pan_details.relation_type_code'],['aff_savings_and_policies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_saving_policies_type.saving_type','aff_savings_and_policies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$savings_and_policies_1->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 1)->where("aff_savings_and_policies.is_deleted", 0);
			}
	    	$savings_and_policies_1 = $savings_and_policies_1->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['savings_and_policies_1'] = $savings_and_policies_1;

			$savings_and_policies_2 = DB::table('aff_savings_and_policies')
			->join("aff_m_saving_policies_type", "aff_savings_and_policies.saving_type_id", "=", "aff_m_saving_policies_type.saving_type_id")
			->rightjoin('aff_pan_details',[['aff_savings_and_policies.affidavit_id','aff_pan_details.affidavit_id'],['aff_savings_and_policies.relation_type_code','aff_pan_details.relation_type_code'],['aff_savings_and_policies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_saving_policies_type.saving_type','aff_savings_and_policies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$savings_and_policies_2->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 2)->where("aff_savings_and_policies.is_deleted", 0);
			}
	    	$savings_and_policies_2 = $savings_and_policies_2->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['savings_and_policies_2'] = $savings_and_policies_2;

			$savings_and_policies_3 = DB::table('aff_savings_and_policies')
			->join("aff_m_saving_policies_type", "aff_savings_and_policies.saving_type_id", "=", "aff_m_saving_policies_type.saving_type_id")
			->rightjoin('aff_pan_details',[['aff_savings_and_policies.affidavit_id','aff_pan_details.affidavit_id'],['aff_savings_and_policies.relation_type_code','aff_pan_details.relation_type_code'],['aff_savings_and_policies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_saving_policies_type.saving_type','aff_savings_and_policies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$savings_and_policies_3->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 3)->where("aff_savings_and_policies.is_deleted", 0);
			}
	    	$savings_and_policies_3 = $savings_and_policies_3->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['savings_and_policies_3'] = $savings_and_policies_3;
			
$savings_and_policies_4 = DB::table('aff_savings_and_policies')
			->join("aff_m_saving_policies_type", "aff_savings_and_policies.saving_type_id", "=", "aff_m_saving_policies_type.saving_type_id")
			->rightjoin('aff_pan_details',[['aff_savings_and_policies.affidavit_id','aff_pan_details.affidavit_id'],['aff_savings_and_policies.relation_type_code','aff_pan_details.relation_type_code'],['aff_savings_and_policies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_saving_policies_type.saving_type','aff_savings_and_policies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$savings_and_policies_4->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 4)->where("aff_savings_and_policies.is_deleted", 0);
			}
	    	$savings_and_policies_4 = $savings_and_policies_4->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['savings_and_policies_4'] = $savings_and_policies_4;



    $savings_and_policies_5 = DB::table('aff_savings_and_policies')
			->join("aff_m_saving_policies_type", "aff_savings_and_policies.saving_type_id", "=", "aff_m_saving_policies_type.saving_type_id")
			->rightjoin('aff_pan_details',[['aff_savings_and_policies.affidavit_id','aff_pan_details.affidavit_id'],['aff_savings_and_policies.relation_type_code','aff_pan_details.relation_type_code'],['aff_savings_and_policies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_saving_policies_type.saving_type','aff_savings_and_policies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$savings_and_policies_5->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 5)->where("aff_savings_and_policies.is_deleted", 0);
			}
	    	$savings_and_policies_5 = $savings_and_policies_5->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['savings_and_policies_5'] = $savings_and_policies_5;

			$savings_and_policies_6 = DB::table('aff_savings_and_policies')
			->join("aff_m_saving_policies_type", "aff_savings_and_policies.saving_type_id", "=", "aff_m_saving_policies_type.saving_type_id")
			->rightjoin('aff_pan_details',[['aff_savings_and_policies.affidavit_id','aff_pan_details.affidavit_id'],['aff_savings_and_policies.relation_type_code','aff_pan_details.relation_type_code'],['aff_savings_and_policies.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_saving_policies_type.saving_type','aff_savings_and_policies.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$savings_and_policies_6->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 6)->where("aff_savings_and_policies.is_deleted", 0);
			}
	    	$savings_and_policies_6 = $savings_and_policies_6->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['savings_and_policies_6'] = $savings_and_policies_6;
			///////////////////////////////////////////////////////////
	$loan_details_1 = DB::table('aff_loan_details')
			->join("aff_m_loan_type", "aff_loan_details.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
			->rightjoin('aff_pan_details',[['aff_loan_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_loan_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_loan_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_loan_type.loan_type','aff_loan_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$loan_details_1->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 1)->where("aff_loan_details.is_deleted", 0);
			}
	    	$loan_details_1 = $loan_details_1->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['loan_details_1'] = $loan_details_1;

			$loan_details_2 = DB::table('aff_loan_details')
			->join("aff_m_loan_type", "aff_loan_details.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
			->rightjoin('aff_pan_details',[['aff_loan_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_loan_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_loan_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_loan_type.loan_type','aff_loan_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$loan_details_2->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 2)->where("aff_loan_details.is_deleted", 0);
			}
	    	$loan_details_2 = $loan_details_2->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['loan_details_2'] = $loan_details_2;

			$loan_details_3 = DB::table('aff_loan_details')
			->join("aff_m_loan_type", "aff_loan_details.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
			->rightjoin('aff_pan_details',[['aff_loan_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_loan_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_loan_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_loan_type.loan_type','aff_loan_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$loan_details_3->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 3)->where("aff_loan_details.is_deleted", 0);
			}
	    	$loan_details_3 = $loan_details_3->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['loan_details_3'] = $loan_details_3;
			
			$loan_details_4 = DB::table('aff_loan_details')
			->join("aff_m_loan_type", "aff_loan_details.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
			->rightjoin('aff_pan_details',[['aff_loan_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_loan_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_loan_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_loan_type.loan_type','aff_loan_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$loan_details_4->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 4)->where("aff_loan_details.is_deleted", 0);
			}
	    	$loan_details_4 = $loan_details_4->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['loan_details_4'] = $loan_details_4;

			$loan_details_5 = DB::table('aff_loan_details')
			->join("aff_m_loan_type", "aff_loan_details.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
			->rightjoin('aff_pan_details',[['aff_loan_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_loan_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_loan_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_loan_type.loan_type','aff_loan_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$loan_details_5->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 5)->where("aff_loan_details.is_deleted", 0);
			}
	    	$loan_details_5 = $loan_details_5->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['loan_details_5'] = $loan_details_5;

			$loan_details_6 = DB::table('aff_loan_details')
			->join("aff_m_loan_type", "aff_loan_details.loan_type_id", "=", "aff_m_loan_type.loan_type_id")
			->rightjoin('aff_pan_details',[['aff_loan_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_loan_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_loan_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_loan_type.loan_type','aff_loan_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$loan_details_6->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 6)->where("aff_loan_details.is_deleted", 0);
			}
	    	$loan_details_6 = $loan_details_6->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['loan_details_6'] = $loan_details_6;


//////////////////////////////////////////////////////////////////



$vehicle_details_1 = DB::table('aff_vehicle_details')
			->join("aff_m_vehicle_type", "aff_vehicle_details.vehicle_type_id", "=", "aff_m_vehicle_type.vehicle_type_id")
			->rightjoin('aff_pan_details',[['aff_vehicle_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_vehicle_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_vehicle_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_vehicle_type.vehicle_type','aff_vehicle_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$vehicle_details_1->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 1)->where("aff_vehicle_details.is_deleted", 0);
			}
	    	$vehicle_details_1 = $vehicle_details_1->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['vehicle_details_1'] = $vehicle_details_1;

			$vehicle_details_2 = DB::table('aff_vehicle_details')
			->join("aff_m_vehicle_type", "aff_vehicle_details.vehicle_type_id", "=", "aff_m_vehicle_type.vehicle_type_id")
			->rightjoin('aff_pan_details',[['aff_vehicle_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_vehicle_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_vehicle_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_vehicle_type.vehicle_type','aff_vehicle_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$vehicle_details_2->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 2)->where("aff_vehicle_details.is_deleted", 0);
			}
	    	$vehicle_details_2 = $vehicle_details_2->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['vehicle_details_2'] = $vehicle_details_2;

			$vehicle_details_3 = DB::table('aff_vehicle_details')
			->join("aff_m_vehicle_type", "aff_vehicle_details.vehicle_type_id", "=", "aff_m_vehicle_type.vehicle_type_id")
			->rightjoin('aff_pan_details',[['aff_vehicle_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_vehicle_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_vehicle_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_vehicle_type.vehicle_type','aff_vehicle_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$vehicle_details_3->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 3)->where("aff_vehicle_details.is_deleted", 0);
			}
	    	$vehicle_details_3 = $vehicle_details_3->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['vehicle_details_3'] = $vehicle_details_3;

			$vehicle_details_4 = DB::table('aff_vehicle_details')
			->join("aff_m_vehicle_type", "aff_vehicle_details.vehicle_type_id", "=", "aff_m_vehicle_type.vehicle_type_id")
			->rightjoin('aff_pan_details',[['aff_vehicle_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_vehicle_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_vehicle_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_vehicle_type.vehicle_type','aff_vehicle_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$vehicle_details_4->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 4)->where("aff_vehicle_details.is_deleted", 0);
			}
	    	$vehicle_details_4 = $vehicle_details_4->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['vehicle_details_4'] = $vehicle_details_4;

			$vehicle_details_5 = DB::table('aff_vehicle_details')
			->join("aff_m_vehicle_type", "aff_vehicle_details.vehicle_type_id", "=", "aff_m_vehicle_type.vehicle_type_id")
			->rightjoin('aff_pan_details',[['aff_vehicle_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_vehicle_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_vehicle_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_vehicle_type.vehicle_type','aff_vehicle_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$vehicle_details_5->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 5)->where("aff_vehicle_details.is_deleted", 0);
			}
	    	$vehicle_details_5 = $vehicle_details_5->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['vehicle_details_5'] = $vehicle_details_5;

			$vehicle_details_6 = DB::table('aff_vehicle_details')
			->join("aff_m_vehicle_type", "aff_vehicle_details.vehicle_type_id", "=", "aff_m_vehicle_type.vehicle_type_id")
			->rightjoin('aff_pan_details',[['aff_vehicle_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_vehicle_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_vehicle_details.is_deleted',DB::Raw('0')]])			
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_vehicle_type.vehicle_type','aff_vehicle_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$vehicle_details_6->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 6)->where("aff_vehicle_details.is_deleted", 0);
			}
	    	$vehicle_details_6 = $vehicle_details_6->orderBy("aff_m_relation_type.relation_type_code", "ASC")->get()->toArray();
			
			$data['vehicle_details_6'] = $vehicle_details_6;

///////////////////////////////////////////////////////////////////////////////////////////			

$valuable_things_details_1 = DB::table('aff_valuable_things_details')
			->join("aff_m_valuable_things", "aff_valuable_things_details.valuable_type_id", "=", "aff_m_valuable_things.valuable_type_id")
			->join("aff_m_valuable_weight", "aff_valuable_things_details.weight_unit_id", "=", "aff_m_valuable_weight.valuable_weight_id")
			->rightjoin('aff_pan_details',[['aff_valuable_things_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_valuable_things_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_valuable_things_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_valuable_things.valuable_type','aff_m_valuable_weight.valuable_weight','aff_valuable_things_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$valuable_things_details_1->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 1)->where("aff_valuable_things_details.is_deleted", 0);
			}
	    	$valuable_things_details_1 = $valuable_things_details_1->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['valuable_things_details_1'] = $valuable_things_details_1;
			




			$valuable_things_details_2 = DB::table('aff_valuable_things_details')
			->join("aff_m_valuable_things", "aff_valuable_things_details.valuable_type_id", "=", "aff_m_valuable_things.valuable_type_id")
			->join("aff_m_valuable_weight", "aff_valuable_things_details.weight_unit_id", "=", "aff_m_valuable_weight.valuable_weight_id")
			->rightjoin('aff_pan_details',[['aff_valuable_things_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_valuable_things_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_valuable_things_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_valuable_things.valuable_type','aff_m_valuable_weight.valuable_weight','aff_valuable_things_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$valuable_things_details_2->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 2)->where("aff_valuable_things_details.is_deleted", 0);
			}
	    	$valuable_things_details_2 = $valuable_things_details_2->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['valuable_things_details_2'] = $valuable_things_details_2;





			$valuable_things_details_3 = DB::table('aff_valuable_things_details')
			->join("aff_m_valuable_things", "aff_valuable_things_details.valuable_type_id", "=", "aff_m_valuable_things.valuable_type_id")
			->join("aff_m_valuable_weight", "aff_valuable_things_details.weight_unit_id", "=", "aff_m_valuable_weight.valuable_weight_id")
			->rightjoin('aff_pan_details',[['aff_valuable_things_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_valuable_things_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_valuable_things_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_valuable_things.valuable_type','aff_m_valuable_weight.valuable_weight','aff_valuable_things_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$valuable_things_details_3->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 3)->where("aff_valuable_things_details.is_deleted", 0);
			}
	    	$valuable_things_details_3 = $valuable_things_details_3->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();




			$valuable_things_details_4 = DB::table('aff_valuable_things_details')
			->join("aff_m_valuable_things", "aff_valuable_things_details.valuable_type_id", "=", "aff_m_valuable_things.valuable_type_id")
			->join("aff_m_valuable_weight", "aff_valuable_things_details.weight_unit_id", "=", "aff_m_valuable_weight.valuable_weight_id")
			->rightjoin('aff_pan_details',[['aff_valuable_things_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_valuable_things_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_valuable_things_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_valuable_things.valuable_type','aff_m_valuable_weight.valuable_weight','aff_valuable_things_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$valuable_things_details_4->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 4)->where("aff_valuable_things_details.is_deleted", 0);
			}
	    	$valuable_things_details_4 = $valuable_things_details_4->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['valuable_things_details_4'] = $valuable_things_details_4;
			
			$data['valuable_things_details_3'] = $valuable_things_details_3;





			$valuable_things_details_5 = DB::table('aff_valuable_things_details')
			->join("aff_m_valuable_things", "aff_valuable_things_details.valuable_type_id", "=", "aff_m_valuable_things.valuable_type_id")
			->join("aff_m_valuable_weight", "aff_valuable_things_details.weight_unit_id", "=", "aff_m_valuable_weight.valuable_weight_id")
			->rightjoin('aff_pan_details',[['aff_valuable_things_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_valuable_things_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_valuable_things_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_valuable_things.valuable_type','aff_m_valuable_weight.valuable_weight','aff_valuable_things_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$valuable_things_details_5->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 5)->where("aff_valuable_things_details.is_deleted", 0);
			}
	    	$valuable_things_details_5 = $valuable_things_details_5->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['valuable_things_details_5'] = $valuable_things_details_5;





			$valuable_things_details_6 = DB::table('aff_valuable_things_details')
			->join("aff_m_valuable_things", "aff_valuable_things_details.valuable_type_id", "=", "aff_m_valuable_things.valuable_type_id")
			->join("aff_m_valuable_weight", "aff_valuable_things_details.weight_unit_id", "=", "aff_m_valuable_weight.valuable_weight_id")
			->rightjoin('aff_pan_details',[['aff_valuable_things_details.affidavit_id','aff_pan_details.affidavit_id'],['aff_valuable_things_details.relation_type_code','aff_pan_details.relation_type_code'],['aff_valuable_things_details.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_m_valuable_things.valuable_type','aff_m_valuable_weight.valuable_weight','aff_valuable_things_details.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$valuable_things_details_6->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 6)->where("aff_valuable_things_details.is_deleted", 0);
			}
	    	$valuable_things_details_6 = $valuable_things_details_6->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['valuable_things_details_6'] = $valuable_things_details_6;

///////////////////////////////////////////////////////////////////////////////////////////
$other_assets_1 = DB::table('aff_other_assets')
			->rightjoin('aff_pan_details',[['aff_other_assets.affidavit_id','aff_pan_details.affidavit_id'],['aff_other_assets.relation_type_code','aff_pan_details.relation_type_code'],['aff_other_assets.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_other_assets.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$other_assets_1->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 1)->where("aff_other_assets.is_deleted", 0);
			}
	    	$other_assets_1 = $other_assets_1->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['other_assets_1'] = $other_assets_1;

			$other_assets_2 = DB::table('aff_other_assets')
			->rightjoin('aff_pan_details',[['aff_other_assets.affidavit_id','aff_pan_details.affidavit_id'],['aff_other_assets.relation_type_code','aff_pan_details.relation_type_code'],['aff_other_assets.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_other_assets.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$other_assets_2->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 2)->where("aff_other_assets.is_deleted", 0);
			}
	    	$other_assets_2 = $other_assets_2->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['other_assets_2'] = $other_assets_2;

			$other_assets_3 = DB::table('aff_other_assets')
			->rightjoin('aff_pan_details',[['aff_other_assets.affidavit_id','aff_pan_details.affidavit_id'],['aff_other_assets.relation_type_code','aff_pan_details.relation_type_code'],['aff_other_assets.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_other_assets.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$other_assets_3->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 3)->where("aff_other_assets.is_deleted", 0);
			}
	    	$other_assets_3 = $other_assets_3->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['other_assets_3'] = $other_assets_3;

			$other_assets_4 = DB::table('aff_other_assets')
			->rightjoin('aff_pan_details',[['aff_other_assets.affidavit_id','aff_pan_details.affidavit_id'],['aff_other_assets.relation_type_code','aff_pan_details.relation_type_code'],['aff_other_assets.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_other_assets.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$other_assets_4->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 4)->where("aff_other_assets.is_deleted", 0);
			}
	    	$other_assets_4 = $other_assets_4->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['other_assets_4'] = $other_assets_4;
			$other_assets_5 = DB::table('aff_other_assets')
			->rightjoin('aff_pan_details',[['aff_other_assets.affidavit_id','aff_pan_details.affidavit_id'],['aff_other_assets.relation_type_code','aff_pan_details.relation_type_code'],['aff_other_assets.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_other_assets.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$other_assets_5->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 5)->where("aff_other_assets.is_deleted", 0);
			}
	    	$other_assets_5 = $other_assets_5->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['other_assets_5'] = $other_assets_5;

			$other_assets_6 = DB::table('aff_other_assets')
			->rightjoin('aff_pan_details',[['aff_other_assets.affidavit_id','aff_pan_details.affidavit_id'],['aff_other_assets.relation_type_code','aff_pan_details.relation_type_code'],['aff_other_assets.is_deleted',DB::Raw('0')]])
			->join("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
			->select('aff_pan_details.name',"aff_m_relation_type.relation_type",'aff_other_assets.*');
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$other_assets_6->where("aff_pan_details.affidavit_id", $affidavit_id)->where("aff_m_relation_type.relation_type_code", 6)->where("aff_other_assets.is_deleted", 0);
			}
	    	$other_assets_6 = $other_assets_6->orderBy("aff_m_relation_type.relation_type_code", "ASC")
			->get()->toArray();
			
			$data['other_assets_6'] = $other_assets_6;

////////////////////////////////////////////////////////////////////////////////////////////

$agricultural_land_test = DB::table('aff_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    }
				})

			 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type", 
 DB::raw('group_concat(aff_agricultural_land_details.location SEPARATOR " | " ) as location'),
     	DB::raw('group_concat(aff_agricultural_land_details.survey_number SEPARATOR "|" ) as survey_number'),
     	DB::raw('group_concat(aff_agricultural_land_details.inherited_property SEPARATOR " | " ) as inherited_property'),
     	DB::raw('group_concat(aff_agricultural_land_details.area SEPARATOR " | " ) as area'),
     	DB::raw('group_concat(aff_agricultural_land_details.cost_at_purchase_time SEPARATOR " | " ) as cost_at_purchase_time'),
			 DB::raw('group_concat(aff_agricultural_land_details.investment_on_land SEPARATOR " | " ) as investment_on_land'),
DB::raw('group_concat(aff_agricultural_land_details.approx_current_market_value SEPARATOR " | " ) as approx_current_market_value'))

		->groupBy("aff_m_relation_type.relation_type_code")->get();


			
			$data['agricultural_land_test'] = $agricultural_land_test;



$non_agricultural_land_test = DB::table('aff_non_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_non_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_non_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    }
				})

			 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type", 
 DB::raw('group_concat(aff_non_agricultural_land_details.location SEPARATOR " | " ) as location'),
     	DB::raw('group_concat(aff_non_agricultural_land_details.survey_number SEPARATOR "|" ) as survey_number'),
     	DB::raw('group_concat(aff_non_agricultural_land_details.inherited_property SEPARATOR " | " ) as inherited_property'),
     	DB::raw('group_concat(aff_non_agricultural_land_details.area SEPARATOR " | " ) as area'),
     	DB::raw('group_concat(aff_non_agricultural_land_details.cost_at_purchase_time SEPARATOR " | " ) as cost_at_purchase_time'),
			 DB::raw('group_concat(aff_non_agricultural_land_details.investment_on_land SEPARATOR " | " ) as investment_on_land'),
DB::raw('group_concat(aff_non_agricultural_land_details.approx_current_market_value SEPARATOR " | " ) as approx_current_market_value'))

		->groupBy("aff_m_relation_type.relation_type_code")->get();

       $data['non_agricultural_land_test'] = $non_agricultural_land_test;


       $commercial_buildings_test = DB::table('aff_commercial_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_commercial_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_commercial_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    }
				})

			 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type", 
 DB::raw('group_concat(aff_commercial_buildings_details.location SEPARATOR " | " ) as location'),
     	DB::raw('group_concat(aff_commercial_buildings_details.survey_number SEPARATOR "|" ) as survey_number'),
     	DB::raw('group_concat(aff_commercial_buildings_details.inherited_property SEPARATOR " | " ) as inherited_property'),
     	DB::raw('group_concat(aff_commercial_buildings_details.area SEPARATOR " | " ) as area'),
     	DB::raw('group_concat(aff_commercial_buildings_details.built_up_area SEPARATOR " | " ) as built_up_area'),

     	DB::raw('group_concat(aff_commercial_buildings_details.investment_on_buildings SEPARATOR " | " ) as investment_on_buildings'),
			 DB::raw('group_concat(aff_commercial_buildings_details.date_of_purchase SEPARATOR " | " ) as date_of_purchase'),

DB::raw('group_concat(aff_commercial_buildings_details.approx_current_market_value SEPARATOR " | " ) as approx_current_market_value'))

		->groupBy("aff_m_relation_type.relation_type_code")->get();

       $data['commercial_buildings_test'] = $commercial_buildings_test;


         $residential_buildings_test = DB::table('aff_residential_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_residential_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_residential_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    }
				})

			 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type", 
        DB::raw('group_concat(aff_residential_buildings_details.location SEPARATOR " | " ) as location'),
     	DB::raw('group_concat(aff_residential_buildings_details.survey_number SEPARATOR "|" ) as survey_number'),
     	DB::raw('group_concat(aff_residential_buildings_details.inherited_property SEPARATOR " | " ) as inherited_property'),
     	DB::raw('group_concat(aff_residential_buildings_details.area SEPARATOR " | " ) as area'),
     	DB::raw('group_concat(aff_residential_buildings_details.built_up_area SEPARATOR " | " ) as built_up_area'),
     	
     	DB::raw('group_concat(aff_residential_buildings_details.investment_on_buildings SEPARATOR " | " ) as investment_on_buildings'),
		DB::raw('group_concat(aff_residential_buildings_details.date_of_purchase SEPARATOR " | " ) as date_of_purchase'),
		DB::raw('group_concat(aff_residential_buildings_details.cost_at_purchase_time SEPARATOR " | " ) as cost_at_purchase_time'),

			 

DB::raw('group_concat(aff_residential_buildings_details.approx_current_market_value SEPARATOR " | " ) as approx_current_market_value'))->groupBy("aff_m_relation_type.relation_type_code")->get();

			 //dd($residential_buildings_test);

       $data['residential_buildings_test'] = $residential_buildings_test;


         $other_immovable_test = DB::table('aff_other_immovable_assets')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_other_immovable_assets.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_other_immovable_assets.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    }
				})

			 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type", 
             DB::raw('group_concat(aff_other_immovable_assets.brief_details SEPARATOR " | " ) as brief_details'),
     	
			

            DB::raw('group_concat(aff_other_immovable_assets.amount SEPARATOR " | " ) as amount'))

		->groupBy("aff_m_relation_type.relation_type_code")->get();

       $data['other_immovable_test'] = $other_immovable_test;



/////////////////////////////////////////////////////////////////////////////

          $l_loan_details = DB::table('aff_l_loan_bank_finc_inst')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_loan_bank_finc_inst.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_loan_bank_finc_inst.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_l_loan_bank_finc_inst.is_deleted','=', \DB::raw("0"));
				    }
				})
			 ->leftjoin("aff_m_loan_type", "aff_l_loan_bank_finc_inst.loan_type_id", "=", "aff_m_loan_type.loan_type_id")

			 ->select("aff_m_relation_type.relation_type", 
             DB::raw('group_concat(aff_l_loan_bank_finc_inst.bank_inst_name SEPARATOR " | " ) as bank_inst_name'),
     	
			DB::raw('group_concat(aff_l_loan_bank_finc_inst.loan_account_type SEPARATOR " | " ) as loan_account_type'),

			DB::raw('group_concat(aff_m_loan_type.loan_type SEPARATOR " | " ) as loan_type'),
			DB::raw("SUM(outstanding_amount) as totalamount"),

            DB::raw('group_concat(aff_l_loan_bank_finc_inst.outstanding_amount SEPARATOR " | " ) as outstanding_amount'))

		->groupBy("aff_m_relation_type.relation_type_code")->get();

		//dd($l_loan_details);

       $data['l_loan_details'] = $l_loan_details;

       $l_loan_individual = DB::table('aff_l_loan_individual_entity')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_loan_individual_entity.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_loan_individual_entity.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_l_loan_individual_entity.is_deleted','=', \DB::raw("0"));
				    }
				})
			 ->leftjoin("aff_m_loan_type", "aff_l_loan_individual_entity.loan_type_id", "=", "aff_m_loan_type.loan_type_id")

			 ->select("aff_m_relation_type.relation_type", 
             DB::raw('group_concat(aff_l_loan_individual_entity.individual_entity_name SEPARATOR " | " ) as individual_entity_name'),
     	
			DB::raw('group_concat(aff_m_loan_type.loan_type SEPARATOR " | " ) as loan_type'),

			DB::raw('group_concat(aff_l_loan_individual_entity.outstanding_amount SEPARATOR " | " ) as outstanding_amount'),

			

            DB::raw('group_concat(aff_l_loan_individual_entity.loan_account_type SEPARATOR " | " ) as loan_account_type'))

		->groupBy("aff_m_relation_type.relation_type_code")->get();
	//	dd($l_loan_individual);

       $data['l_loan_individual'] = $l_loan_individual;

   $l_other_liabilities = DB::table('aff_l_other_liabilities')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_other_liabilities.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_other_liabilities.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_l_other_liabilities.is_deleted','=', \DB::raw("0"));
				    }
				})
			 

			 ->select("aff_m_relation_type.relation_type", 
             DB::raw('group_concat(aff_l_other_liabilities.authority_name SEPARATOR " | " ) as authority_name'),
     	
			DB::raw('group_concat(aff_l_other_liabilities.details SEPARATOR " | " ) as details'),

			
            DB::raw('group_concat(aff_l_other_liabilities.amount SEPARATOR " | " ) as amount'))

		->groupBy("aff_m_relation_type.relation_type_code")->get();

		//dd($l_loan_details);

       $data['l_other_liabilities'] = $l_other_liabilities;



     $l_govt_dues_trasport = DB::table('aff_l_govt_dues')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_govt_dues.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_govt_dues.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_l_govt_dues.govt_dept_name_code','=', \DB::raw("5"));
				    	$join->on('aff_l_govt_dues.is_deleted','=', \DB::raw("0"));
				    }
				})
			 ->leftjoin("aff_m_govt_dept_name", "aff_l_govt_dues.govt_dept_name_code", "=", "aff_m_govt_dept_name.govt_dept_name_code")

			 ->select("aff_m_relation_type.relation_type", "aff_l_govt_dues.*",
             DB::raw('group_concat(aff_l_govt_dues.due_details SEPARATOR " | " ) as due_details'),
     	
            DB::raw('group_concat(aff_l_govt_dues.amount SEPARATOR " | " ) as amount'))
			 

		  ->groupBy("aff_m_relation_type.relation_type_code")->get();

		//dd($l_govt_dues_trasport);

       	
			$data['l_govt_dues_trasport'] = $l_govt_dues_trasport;


			 $l_govt_dues_accommodation = DB::table('aff_l_govt_dues')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_govt_dues.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_govt_dues.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_l_govt_dues.govt_dept_name_code','=', \DB::raw("1"));
				    		$join->on('aff_l_govt_dues.is_deleted','=', \DB::raw("0"));
				    }
				})
			 ->leftjoin("aff_m_govt_dept_name", "aff_l_govt_dues.govt_dept_name_code", "=", "aff_m_govt_dept_name.govt_dept_name_code")

			 ->select("aff_m_relation_type.relation_type", "aff_l_govt_dues.*",
             DB::raw('group_concat(aff_l_govt_dues.due_details SEPARATOR " | " ) as due_details'),
     	
            DB::raw('group_concat(aff_l_govt_dues.amount SEPARATOR " | " ) as amount'))
			 
          ->where("aff_m_relation_type.relation_type_code",'1')->get();
		 // ->groupBy("aff_m_relation_type.relation_type_code")->get();

		//dd($l_govt_dues_trasport);

       	
			$data['l_govt_dues_accommodation'] = $l_govt_dues_accommodation;
		//	dd($l_govt_dues_accommodation);
    		

 $l_govt_dues_incometax = DB::table('aff_l_govt_dues')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_govt_dues.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_govt_dues.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_l_govt_dues.govt_dept_name_code','=', \DB::raw("6"));
				    		$join->on('aff_l_govt_dues.is_deleted','=', \DB::raw("0"));
				    }
				})
			 ->leftjoin("aff_m_govt_dept_name", "aff_l_govt_dues.govt_dept_name_code", "=", "aff_m_govt_dept_name.govt_dept_name_code")

			 ->select("aff_m_relation_type.relation_type", "aff_l_govt_dues.*",
             DB::raw('group_concat(aff_l_govt_dues.due_details SEPARATOR " | " ) as due_details'),
     	
            DB::raw('group_concat(aff_l_govt_dues.amount SEPARATOR " | " ) as amount'))
			 

		  ->groupBy("aff_m_relation_type.relation_type_code")->get();

		//dd($l_govt_dues_trasport);

       	
			$data['l_govt_dues_incometax'] = $l_govt_dues_incometax;
    		

        $l_govt_dues_gstdues = DB::table('aff_l_govt_dues')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_govt_dues.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_govt_dues.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_l_govt_dues.govt_dept_name_code','=', \DB::raw("10"));
				    		$join->on('aff_l_govt_dues.is_deleted','=', \DB::raw("0"));
				    }
				})
			 ->leftjoin("aff_m_govt_dept_name", "aff_l_govt_dues.govt_dept_name_code", "=", "aff_m_govt_dept_name.govt_dept_name_code")

			 ->select("aff_m_relation_type.relation_type", "aff_l_govt_dues.*",
             DB::raw('group_concat(aff_l_govt_dues.due_details SEPARATOR " | " ) as due_details'),
     	
            DB::raw('group_concat(aff_l_govt_dues.amount SEPARATOR " | " ) as amount'))
			 

		  ->groupBy("aff_m_relation_type.relation_type_code")->get();

		//dd($l_govt_dues_trasport);

       	
			$data['l_govt_dues_gstdues'] = $l_govt_dues_gstdues;
    	
    	$l_govt_dues_anyother = DB::table('aff_l_govt_dues')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_govt_dues.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_govt_dues.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_l_govt_dues.govt_dept_name_code','=', \DB::raw("11"));
				    		$join->on('aff_l_govt_dues.is_deleted','=', \DB::raw("0"));
				    }
				})
			 ->leftjoin("aff_m_govt_dept_name", "aff_l_govt_dues.govt_dept_name_code", "=", "aff_m_govt_dept_name.govt_dept_name_code")

			 ->select("aff_m_relation_type.relation_type", "aff_l_govt_dues.*",
             DB::raw('group_concat(aff_l_govt_dues.due_details SEPARATOR " | " ) as due_details'),
     	
            DB::raw('group_concat(aff_l_govt_dues.amount SEPARATOR " | " ) as amount'))
			 

		  ->groupBy("aff_m_relation_type.relation_type_code")->get();

		//dd($l_govt_dues_trasport);

       	
			$data['l_govt_dues_anyother'] = $l_govt_dues_anyother;
    		
         $l_govt_dues_muncipal = DB::table('aff_l_govt_dues')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_govt_dues.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_govt_dues.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_l_govt_dues.govt_dept_name_code','=', \DB::raw("8"));
				    		$join->on('aff_l_govt_dues.is_deleted','=', \DB::raw("0"));
				    }
				})
			 ->leftjoin("aff_m_govt_dept_name", "aff_l_govt_dues.govt_dept_name_code", "=", "aff_m_govt_dept_name.govt_dept_name_code")

			 ->select("aff_m_relation_type.relation_type", "aff_l_govt_dues.*",
             DB::raw('group_concat(aff_l_govt_dues.due_details SEPARATOR " | " ) as due_details'),
     	
            DB::raw('group_concat(aff_l_govt_dues.amount SEPARATOR " | " ) as amount'))
			 

		  ->groupBy("aff_m_relation_type.relation_type_code")->get();

		
         $data['l_govt_dues_muncipal'] = $l_govt_dues_muncipal;




/////////////////////////////////////////////////////////////////////////////////////////////



         $abcd = DB::table('aff_self_spouse_occupation')

       ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_self_spouse_occupation.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_self_spouse_occupation.affidavit_id','=', \DB::raw("'$affidavit_id'"));

				    }
				    	
				   
				})
			
			->select('aff_m_relation_type.relation_type_code','aff_self_spouse_occupation.*')
	    	     ->whereIn('aff_self_spouse_occupation.relation_type_code',[1,2])
			->groupBy("aff_m_relation_type.relation_type_code")->get();
			
	    	$data['abcd'] = $abcd;
//dd($abcd,$l_govt_dues_muncipal);







         
			$movable_assets_total_test1 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.total,t2.Other_Mov_Asset from aff_pan_details pdt
join  aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
select affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
cash_in_hand+ Bank_Deposit+investment+saving+outstanding+vehicle_amount+valuable_amount+other_amount as total
from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(ch.cash_in_hand,0)as cash_in_hand,IFNULL(b.Bank_Deposit, 0) AS Bank_Deposit,
IFNULL(c.investment, 0)AS investment, IFNULL(d.saving, 0)AS saving,
IFNULL(e.outstanding, 0) AS outstanding, IFNULL(f.vehicle_amount, 0) AS vehicle_amount,
IFNULL(g.valuable_amount, 0) AS valuable_amount,
IFNULL(h.other_amount, 0) AS other_amount
FROM aff_pan_details AS a

JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(CASH_IN_HAND) as cash_in_hand
 FROM aff_cash_in_hand
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS ch ON a.affidavit_id = ch.affidavit_id
 AND a.RELATION_TYPE_CODE = ch.RELATION_TYPE_CODE

LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS Bank_Deposit
 FROM aff_bank_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id = b.affidavit_id AND
 a.RELATION_TYPE_CODE = b.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS investment
 FROM aff_investment_in_companies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id = c.affidavit_id AND
 a.RELATION_TYPE_CODE = c.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id,RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS saving
 FROM aff_savings_and_policies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id = d.affidavit_id AND
 a.RELATION_TYPE_CODE = d.RELATION_TYPE_CODE
LEFT OUTER JOIN

 (SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(OUTSTANDING_AMOUNT, 0)), 0) AS outstanding
  FROM aff_loan_details where is_deleted = '0'
  GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id = e.affidavit_id AND
  a.RELATION_TYPE_CODE = e.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, SUM(IFNULL(AMOUNT, 0)) AS vehicle_amount
 FROM aff_vehicle_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS f ON a.affidavit_id = f.affidavit_id AND
 a.RELATION_TYPE_CODE = f.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS valuable_amount
 FROM  aff_valuable_things_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS g ON a.affidavit_id = g.affidavit_id AND
 a.RELATION_TYPE_CODE = g.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(AMOUNT), 0) AS other_amount
 FROM  aff_other_assets where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS h ON a.affidavit_id = h.affidavit_id
AND a.RELATION_TYPE_CODE = h.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE,
a.NAME,ch.cash_in_hand, b.Bank_Deposit,c.investment,d.saving,e.outstanding,f.vehicle_amount,
g.valuable_amount,h.other_amount)t1
 )x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Mov_Asset,0) as Other_Mov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Mov_Asset
from aff_other_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t2
on pdt.affidavit_id=t2.affidavit_id and pdt.RELATION_TYPE_CODE=t2.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='1'
GROUP BY  pdt.RELATION_TYPE_CODE ");
			
			
			//dd($movable_assets_total_test1);
			
			
			$data['movable_assets_total_test1'] = $movable_assets_total_test1;


			$movable_assets_total_test2 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.total,t2.Other_Mov_Asset from aff_pan_details pdt
join  aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
select affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
cash_in_hand+ Bank_Deposit+investment+saving+outstanding+vehicle_amount+valuable_amount+other_amount as total
from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(ch.cash_in_hand,0)as cash_in_hand,IFNULL(b.Bank_Deposit, 0) AS Bank_Deposit,
IFNULL(c.investment, 0)AS investment, IFNULL(d.saving, 0)AS saving,
IFNULL(e.outstanding, 0) AS outstanding, IFNULL(f.vehicle_amount, 0) AS vehicle_amount,
IFNULL(g.valuable_amount, 0) AS valuable_amount,
IFNULL(h.other_amount, 0) AS other_amount
FROM aff_pan_details AS a

JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(CASH_IN_HAND) as cash_in_hand
 FROM aff_cash_in_hand
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS ch ON a.affidavit_id = ch.affidavit_id
 AND a.RELATION_TYPE_CODE = ch.RELATION_TYPE_CODE

LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS Bank_Deposit
 FROM aff_bank_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id = b.affidavit_id AND
 a.RELATION_TYPE_CODE = b.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS investment
 FROM aff_investment_in_companies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id = c.affidavit_id AND
 a.RELATION_TYPE_CODE = c.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id,RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS saving
 FROM aff_savings_and_policies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id = d.affidavit_id AND
 a.RELATION_TYPE_CODE = d.RELATION_TYPE_CODE
LEFT OUTER JOIN

 (SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(OUTSTANDING_AMOUNT, 0)), 0) AS outstanding
  FROM aff_loan_details where is_deleted = '0'
  GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id = e.affidavit_id AND
  a.RELATION_TYPE_CODE = e.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, SUM(IFNULL(AMOUNT, 0)) AS vehicle_amount
 FROM aff_vehicle_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS f ON a.affidavit_id = f.affidavit_id AND
 a.RELATION_TYPE_CODE = f.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS valuable_amount
 FROM  aff_valuable_things_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS g ON a.affidavit_id = g.affidavit_id AND
 a.RELATION_TYPE_CODE = g.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(AMOUNT), 0) AS other_amount
 FROM  aff_other_assets where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS h ON a.affidavit_id = h.affidavit_id
AND a.RELATION_TYPE_CODE = h.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE,
a.NAME,ch.cash_in_hand, b.Bank_Deposit,c.investment,d.saving,e.outstanding,f.vehicle_amount,
g.valuable_amount,h.other_amount)t1
 )x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Mov_Asset,0) as Other_Mov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Mov_Asset
from aff_other_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t2
on pdt.affidavit_id=t2.affidavit_id and pdt.RELATION_TYPE_CODE=t2.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='2'
GROUP BY  pdt.RELATION_TYPE_CODE ");
			
			
			//dd($movable_assets_total_test);
			
			
			$data['movable_assets_total_test2'] = $movable_assets_total_test2;

			$movable_assets_total_test3 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.total,t2.Other_Mov_Asset from aff_pan_details pdt
join  aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
select affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
cash_in_hand+ Bank_Deposit+investment+saving+outstanding+vehicle_amount+valuable_amount+other_amount as total
from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(ch.cash_in_hand,0)as cash_in_hand,IFNULL(b.Bank_Deposit, 0) AS Bank_Deposit,
IFNULL(c.investment, 0)AS investment, IFNULL(d.saving, 0)AS saving,
IFNULL(e.outstanding, 0) AS outstanding, IFNULL(f.vehicle_amount, 0) AS vehicle_amount,
IFNULL(g.valuable_amount, 0) AS valuable_amount,
IFNULL(h.other_amount, 0) AS other_amount
FROM aff_pan_details AS a

JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(CASH_IN_HAND) as cash_in_hand
 FROM aff_cash_in_hand
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS ch ON a.affidavit_id = ch.affidavit_id
 AND a.RELATION_TYPE_CODE = ch.RELATION_TYPE_CODE

LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS Bank_Deposit
 FROM aff_bank_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id = b.affidavit_id AND
 a.RELATION_TYPE_CODE = b.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS investment
 FROM aff_investment_in_companies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id = c.affidavit_id AND
 a.RELATION_TYPE_CODE = c.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id,RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS saving
 FROM aff_savings_and_policies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id = d.affidavit_id AND
 a.RELATION_TYPE_CODE = d.RELATION_TYPE_CODE
LEFT OUTER JOIN

 (SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(OUTSTANDING_AMOUNT, 0)), 0) AS outstanding
  FROM aff_loan_details where is_deleted = '0'
  GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id = e.affidavit_id AND
  a.RELATION_TYPE_CODE = e.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, SUM(IFNULL(AMOUNT, 0)) AS vehicle_amount
 FROM aff_vehicle_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS f ON a.affidavit_id = f.affidavit_id AND
 a.RELATION_TYPE_CODE = f.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS valuable_amount
 FROM  aff_valuable_things_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS g ON a.affidavit_id = g.affidavit_id AND
 a.RELATION_TYPE_CODE = g.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(AMOUNT), 0) AS other_amount
 FROM  aff_other_assets where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS h ON a.affidavit_id = h.affidavit_id
AND a.RELATION_TYPE_CODE = h.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE,
a.NAME,ch.cash_in_hand, b.Bank_Deposit,c.investment,d.saving,e.outstanding,f.vehicle_amount,
g.valuable_amount,h.other_amount)t1
 )x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Mov_Asset,0) as Other_Mov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Mov_Asset
from aff_other_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t2
on pdt.affidavit_id=t2.affidavit_id and pdt.RELATION_TYPE_CODE=t2.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='3'
GROUP BY  pdt.RELATION_TYPE_CODE ");
			
			
			//dd($movable_assets_total_test);
			
			
			$data['movable_assets_total_test3'] = $movable_assets_total_test3;

			$movable_assets_total_test4 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.total,t2.Other_Mov_Asset from aff_pan_details pdt
join  aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
select affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
cash_in_hand+ Bank_Deposit+investment+saving+outstanding+vehicle_amount+valuable_amount+other_amount as total
from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(ch.cash_in_hand,0)as cash_in_hand,IFNULL(b.Bank_Deposit, 0) AS Bank_Deposit,
IFNULL(c.investment, 0)AS investment, IFNULL(d.saving, 0)AS saving,
IFNULL(e.outstanding, 0) AS outstanding, IFNULL(f.vehicle_amount, 0) AS vehicle_amount,
IFNULL(g.valuable_amount, 0) AS valuable_amount,
IFNULL(h.other_amount, 0) AS other_amount
FROM aff_pan_details AS a

JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(CASH_IN_HAND) as cash_in_hand
 FROM aff_cash_in_hand
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS ch ON a.affidavit_id = ch.affidavit_id
 AND a.RELATION_TYPE_CODE = ch.RELATION_TYPE_CODE

LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS Bank_Deposit
 FROM aff_bank_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id = b.affidavit_id AND
 a.RELATION_TYPE_CODE = b.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS investment
 FROM aff_investment_in_companies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id = c.affidavit_id AND
 a.RELATION_TYPE_CODE = c.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id,RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS saving
 FROM aff_savings_and_policies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id = d.affidavit_id AND
 a.RELATION_TYPE_CODE = d.RELATION_TYPE_CODE
LEFT OUTER JOIN

 (SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(OUTSTANDING_AMOUNT, 0)), 0) AS outstanding
  FROM aff_loan_details where is_deleted = '0'
  GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id = e.affidavit_id AND
  a.RELATION_TYPE_CODE = e.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, SUM(IFNULL(AMOUNT, 0)) AS vehicle_amount
 FROM aff_vehicle_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS f ON a.affidavit_id = f.affidavit_id AND
 a.RELATION_TYPE_CODE = f.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS valuable_amount
 FROM  aff_valuable_things_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS g ON a.affidavit_id = g.affidavit_id AND
 a.RELATION_TYPE_CODE = g.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(AMOUNT), 0) AS other_amount
 FROM  aff_other_assets where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS h ON a.affidavit_id = h.affidavit_id
AND a.RELATION_TYPE_CODE = h.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE,
a.NAME,ch.cash_in_hand, b.Bank_Deposit,c.investment,d.saving,e.outstanding,f.vehicle_amount,
g.valuable_amount,h.other_amount)t1
 )x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Mov_Asset,0) as Other_Mov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Mov_Asset
from aff_other_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t2
on pdt.affidavit_id=t2.affidavit_id and pdt.RELATION_TYPE_CODE=t2.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='4'
GROUP BY  pdt.RELATION_TYPE_CODE ");
			
			
			//dd($movable_assets_total_test);
			
			
			$data['movable_assets_total_test4'] = $movable_assets_total_test4;

			$movable_assets_total_test5 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.total,t2.Other_Mov_Asset from aff_pan_details pdt
join  aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
select affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
cash_in_hand+ Bank_Deposit+investment+saving+outstanding+vehicle_amount+valuable_amount+other_amount as total
from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(ch.cash_in_hand,0)as cash_in_hand,IFNULL(b.Bank_Deposit, 0) AS Bank_Deposit,
IFNULL(c.investment, 0)AS investment, IFNULL(d.saving, 0)AS saving,
IFNULL(e.outstanding, 0) AS outstanding, IFNULL(f.vehicle_amount, 0) AS vehicle_amount,
IFNULL(g.valuable_amount, 0) AS valuable_amount,
IFNULL(h.other_amount, 0) AS other_amount
FROM aff_pan_details AS a

JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(CASH_IN_HAND) as cash_in_hand
 FROM aff_cash_in_hand
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS ch ON a.affidavit_id = ch.affidavit_id
 AND a.RELATION_TYPE_CODE = ch.RELATION_TYPE_CODE

LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS Bank_Deposit
 FROM aff_bank_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id = b.affidavit_id AND
 a.RELATION_TYPE_CODE = b.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS investment
 FROM aff_investment_in_companies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id = c.affidavit_id AND
 a.RELATION_TYPE_CODE = c.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id,RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS saving
 FROM aff_savings_and_policies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id = d.affidavit_id AND
 a.RELATION_TYPE_CODE = d.RELATION_TYPE_CODE
LEFT OUTER JOIN

 (SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(OUTSTANDING_AMOUNT, 0)), 0) AS outstanding
  FROM aff_loan_details where is_deleted = '0'
  GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id = e.affidavit_id AND
  a.RELATION_TYPE_CODE = e.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, SUM(IFNULL(AMOUNT, 0)) AS vehicle_amount
 FROM aff_vehicle_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS f ON a.affidavit_id = f.affidavit_id AND
 a.RELATION_TYPE_CODE = f.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS valuable_amount
 FROM  aff_valuable_things_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS g ON a.affidavit_id = g.affidavit_id AND
 a.RELATION_TYPE_CODE = g.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(AMOUNT), 0) AS other_amount
 FROM  aff_other_assets where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS h ON a.affidavit_id = h.affidavit_id
AND a.RELATION_TYPE_CODE = h.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE,
a.NAME,ch.cash_in_hand, b.Bank_Deposit,c.investment,d.saving,e.outstanding,f.vehicle_amount,
g.valuable_amount,h.other_amount)t1
 )x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Mov_Asset,0) as Other_Mov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Mov_Asset
from aff_other_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t2
on pdt.affidavit_id=t2.affidavit_id and pdt.RELATION_TYPE_CODE=t2.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='5'
GROUP BY  pdt.RELATION_TYPE_CODE ");
			
			
			//dd($movable_assets_total_test);
			
			
			$data['movable_assets_total_test5'] = $movable_assets_total_test5;

			$movable_assets_total_test6 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.total,t2.Other_Mov_Asset from aff_pan_details pdt
join  aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
select affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
cash_in_hand+ Bank_Deposit+investment+saving+outstanding+vehicle_amount+valuable_amount+other_amount as total
from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(ch.cash_in_hand,0)as cash_in_hand,IFNULL(b.Bank_Deposit, 0) AS Bank_Deposit,
IFNULL(c.investment, 0)AS investment, IFNULL(d.saving, 0)AS saving,
IFNULL(e.outstanding, 0) AS outstanding, IFNULL(f.vehicle_amount, 0) AS vehicle_amount,
IFNULL(g.valuable_amount, 0) AS valuable_amount,
IFNULL(h.other_amount, 0) AS other_amount
FROM aff_pan_details AS a

JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(CASH_IN_HAND) as cash_in_hand
 FROM aff_cash_in_hand
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS ch ON a.affidavit_id = ch.affidavit_id
 AND a.RELATION_TYPE_CODE = ch.RELATION_TYPE_CODE

LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS Bank_Deposit
 FROM aff_bank_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id = b.affidavit_id AND
 a.RELATION_TYPE_CODE = b.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS investment
 FROM aff_investment_in_companies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id = c.affidavit_id AND
 a.RELATION_TYPE_CODE = c.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id,RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS saving
 FROM aff_savings_and_policies where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id = d.affidavit_id AND
 a.RELATION_TYPE_CODE = d.RELATION_TYPE_CODE
LEFT OUTER JOIN

 (SELECT affidavit_id, RELATION_TYPE_CODE, IFNULL(SUM(IFNULL(OUTSTANDING_AMOUNT, 0)), 0) AS outstanding
  FROM aff_loan_details where is_deleted = '0'
  GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id = e.affidavit_id AND
  a.RELATION_TYPE_CODE = e.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE, SUM(IFNULL(AMOUNT, 0)) AS vehicle_amount
 FROM aff_vehicle_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS f ON a.affidavit_id = f.affidavit_id AND
 a.RELATION_TYPE_CODE = f.RELATION_TYPE_CODE
 LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(IFNULL(AMOUNT, 0)), 0) AS valuable_amount
 FROM  aff_valuable_things_details where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS g ON a.affidavit_id = g.affidavit_id AND
 a.RELATION_TYPE_CODE = g.RELATION_TYPE_CODE
LEFT OUTER JOIN

(SELECT affidavit_id, RELATION_TYPE_CODE,IFNULL(SUM(AMOUNT), 0) AS other_amount
 FROM  aff_other_assets where is_deleted = '0'
 GROUP BY affidavit_id, RELATION_TYPE_CODE) AS h ON a.affidavit_id = h.affidavit_id
AND a.RELATION_TYPE_CODE = h.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE,
a.NAME,ch.cash_in_hand, b.Bank_Deposit,c.investment,d.saving,e.outstanding,f.vehicle_amount,
g.valuable_amount,h.other_amount)t1
 )x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Mov_Asset,0) as Other_Mov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Mov_Asset
from aff_other_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t2
on pdt.affidavit_id=t2.affidavit_id and pdt.RELATION_TYPE_CODE=t2.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='6'
GROUP BY  pdt.RELATION_TYPE_CODE ");
			
			
			//dd($movable_assets_total_test);
			
			
			$data['movable_assets_total_test6'] = $movable_assets_total_test6;



			$immoveable_assets_total_test1 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.purcahse_price_self_acquired_immov, x2.Investment_Immov,x3.self_acquired_Assets_Value, x4.Inherited_assets_Value,t5.Other_Immov_Asset from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE, NAME,
agri_purchae_cost+nonagri_purchae_cost+com_purchae_cost+res_purchae_cost as
purcahse_price_self_acquired_immov  from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_purchae_cost,0) as agri_purchae_cost,
IFNULL(c.nonagri_purchae_cost,0) as nonagri_purchae_cost,
IFNULL(d.com_purchae_cost,0) as com_purchae_cost,IFNULL(e.res_purchae_cost,0) as res_purchae_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as agri_purchae_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as nonagri_purchae_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as com_purchae_cost
from aff_commercial_buildings_details  where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as res_purchae_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_purchae_cost,
c.nonagri_purchae_cost,d.com_purchae_cost,e.res_purchae_cost) t1)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_invest+nonagri_invest+com_invest+res_invest as Investment_Immov
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_invest,0) as agri_invest,
 IFNULL(c.nonagri_invest,0) as nonagri_invest,
 IFNULL(d.com_invest,0) as com_invest,IFNULL(e.res_invest,0) as res_invest
 FROM  aff_pan_details AS a
 JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as agri_invest from aff_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as nonagri_invest from aff_non_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(
SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as com_invest
from aff_commercial_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as res_invest
from aff_residential_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_invest,c.nonagri_invest,d.com_invest,e.res_invest
)t2
)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as self_acquired_Assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b
ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
 from aff_commercial_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_current_cost,
c.nonagri_current_cost,d.com_current_cost,e.res_current_cost
)t3)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as Inherited_assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
 from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
from aff_commercial_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id
AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_current_cost,c.nonagri_current_cost,
d.com_current_cost,e.res_current_cost
)t4)x4
on pdt.affidavit_id=x4.affidavit_id and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Immov_Asset,0) as Other_Immov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Immov_Asset
from aff_other_immovable_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t5
on pdt.affidavit_id=t5.affidavit_id and pdt.RELATION_TYPE_CODE=t5.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='1'
GROUP BY  pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			//dd($immoveable_assets_total_test1);
			
			
			$data['immoveable_assets_total_test1'] = $immoveable_assets_total_test1;

				$immoveable_assets_total_test2 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.purcahse_price_self_acquired_immov, x2.Investment_Immov,x3.self_acquired_Assets_Value, x4.Inherited_assets_Value,t5.Other_Immov_Asset from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE, NAME,
agri_purchae_cost+nonagri_purchae_cost+com_purchae_cost+res_purchae_cost as
purcahse_price_self_acquired_immov  from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_purchae_cost,0) as agri_purchae_cost,
IFNULL(c.nonagri_purchae_cost,0) as nonagri_purchae_cost,
IFNULL(d.com_purchae_cost,0) as com_purchae_cost,IFNULL(e.res_purchae_cost,0) as res_purchae_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as agri_purchae_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as nonagri_purchae_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as com_purchae_cost
from aff_commercial_buildings_details  where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as res_purchae_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_purchae_cost,
c.nonagri_purchae_cost,d.com_purchae_cost,e.res_purchae_cost) t1)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_invest+nonagri_invest+com_invest+res_invest as Investment_Immov
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_invest,0) as agri_invest,
 IFNULL(c.nonagri_invest,0) as nonagri_invest,
 IFNULL(d.com_invest,0) as com_invest,IFNULL(e.res_invest,0) as res_invest
 FROM  aff_pan_details AS a
 JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as agri_invest from aff_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as nonagri_invest from aff_non_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(
SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as com_invest
from aff_commercial_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as res_invest
from aff_residential_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_invest,c.nonagri_invest,d.com_invest,e.res_invest
)t2
)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as self_acquired_Assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b
ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
 from aff_commercial_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_current_cost,
c.nonagri_current_cost,d.com_current_cost,e.res_current_cost
)t3)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as Inherited_assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
 from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
from aff_commercial_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id
AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_current_cost,c.nonagri_current_cost,
d.com_current_cost,e.res_current_cost
)t4)x4
on pdt.affidavit_id=x4.affidavit_id and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Immov_Asset,0) as Other_Immov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Immov_Asset
from aff_other_immovable_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t5
on pdt.affidavit_id=t5.affidavit_id and pdt.RELATION_TYPE_CODE=t5.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='2'
GROUP BY  pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			//dd($immoveable_assets_total_test1);
			
			
			$data['immoveable_assets_total_test2'] = $immoveable_assets_total_test2;

				$immoveable_assets_total_test3 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.purcahse_price_self_acquired_immov, x2.Investment_Immov,x3.self_acquired_Assets_Value, x4.Inherited_assets_Value,t5.Other_Immov_Asset from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE, NAME,
agri_purchae_cost+nonagri_purchae_cost+com_purchae_cost+res_purchae_cost as
purcahse_price_self_acquired_immov  from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_purchae_cost,0) as agri_purchae_cost,
IFNULL(c.nonagri_purchae_cost,0) as nonagri_purchae_cost,
IFNULL(d.com_purchae_cost,0) as com_purchae_cost,IFNULL(e.res_purchae_cost,0) as res_purchae_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as agri_purchae_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as nonagri_purchae_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as com_purchae_cost
from aff_commercial_buildings_details  where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as res_purchae_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_purchae_cost,
c.nonagri_purchae_cost,d.com_purchae_cost,e.res_purchae_cost) t1)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_invest+nonagri_invest+com_invest+res_invest as Investment_Immov
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_invest,0) as agri_invest,
 IFNULL(c.nonagri_invest,0) as nonagri_invest,
 IFNULL(d.com_invest,0) as com_invest,IFNULL(e.res_invest,0) as res_invest
 FROM  aff_pan_details AS a
 JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as agri_invest from aff_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as nonagri_invest from aff_non_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(
SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as com_invest
from aff_commercial_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as res_invest
from aff_residential_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_invest,c.nonagri_invest,d.com_invest,e.res_invest
)t2
)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as self_acquired_Assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b
ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
 from aff_commercial_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_current_cost,
c.nonagri_current_cost,d.com_current_cost,e.res_current_cost
)t3)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as Inherited_assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
 from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
from aff_commercial_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id
AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_current_cost,c.nonagri_current_cost,
d.com_current_cost,e.res_current_cost
)t4)x4
on pdt.affidavit_id=x4.affidavit_id and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Immov_Asset,0) as Other_Immov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Immov_Asset
from aff_other_immovable_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t5
on pdt.affidavit_id=t5.affidavit_id and pdt.RELATION_TYPE_CODE=t5.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='3'
GROUP BY  pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			//dd($immoveable_assets_total_test1);
			
			
			$data['immoveable_assets_total_test3'] = $immoveable_assets_total_test3;

				$immoveable_assets_total_test4 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.purcahse_price_self_acquired_immov, x2.Investment_Immov,x3.self_acquired_Assets_Value, x4.Inherited_assets_Value,t5.Other_Immov_Asset from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE, NAME,
agri_purchae_cost+nonagri_purchae_cost+com_purchae_cost+res_purchae_cost as
purcahse_price_self_acquired_immov  from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_purchae_cost,0) as agri_purchae_cost,
IFNULL(c.nonagri_purchae_cost,0) as nonagri_purchae_cost,
IFNULL(d.com_purchae_cost,0) as com_purchae_cost,IFNULL(e.res_purchae_cost,0) as res_purchae_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as agri_purchae_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as nonagri_purchae_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as com_purchae_cost
from aff_commercial_buildings_details  where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as res_purchae_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_purchae_cost,
c.nonagri_purchae_cost,d.com_purchae_cost,e.res_purchae_cost) t1)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_invest+nonagri_invest+com_invest+res_invest as Investment_Immov
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_invest,0) as agri_invest,
 IFNULL(c.nonagri_invest,0) as nonagri_invest,
 IFNULL(d.com_invest,0) as com_invest,IFNULL(e.res_invest,0) as res_invest
 FROM  aff_pan_details AS a
 JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as agri_invest from aff_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as nonagri_invest from aff_non_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(
SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as com_invest
from aff_commercial_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as res_invest
from aff_residential_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_invest,c.nonagri_invest,d.com_invest,e.res_invest
)t2
)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as self_acquired_Assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b
ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
 from aff_commercial_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_current_cost,
c.nonagri_current_cost,d.com_current_cost,e.res_current_cost
)t3)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as Inherited_assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
 from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
from aff_commercial_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id
AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_current_cost,c.nonagri_current_cost,
d.com_current_cost,e.res_current_cost
)t4)x4
on pdt.affidavit_id=x4.affidavit_id and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Immov_Asset,0) as Other_Immov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Immov_Asset
from aff_other_immovable_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t5
on pdt.affidavit_id=t5.affidavit_id and pdt.RELATION_TYPE_CODE=t5.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='4'
GROUP BY  pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			//dd($immoveable_assets_total_test1);
			
			
			$data['immoveable_assets_total_test4'] = $immoveable_assets_total_test4;

				$immoveable_assets_total_test5 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.purcahse_price_self_acquired_immov, x2.Investment_Immov,x3.self_acquired_Assets_Value, x4.Inherited_assets_Value,t5.Other_Immov_Asset from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE, NAME,
agri_purchae_cost+nonagri_purchae_cost+com_purchae_cost+res_purchae_cost as
purcahse_price_self_acquired_immov  from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_purchae_cost,0) as agri_purchae_cost,
IFNULL(c.nonagri_purchae_cost,0) as nonagri_purchae_cost,
IFNULL(d.com_purchae_cost,0) as com_purchae_cost,IFNULL(e.res_purchae_cost,0) as res_purchae_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as agri_purchae_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as nonagri_purchae_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as com_purchae_cost
from aff_commercial_buildings_details  where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as res_purchae_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_purchae_cost,
c.nonagri_purchae_cost,d.com_purchae_cost,e.res_purchae_cost) t1)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_invest+nonagri_invest+com_invest+res_invest as Investment_Immov
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_invest,0) as agri_invest,
 IFNULL(c.nonagri_invest,0) as nonagri_invest,
 IFNULL(d.com_invest,0) as com_invest,IFNULL(e.res_invest,0) as res_invest
 FROM  aff_pan_details AS a
 JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as agri_invest from aff_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as nonagri_invest from aff_non_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(
SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as com_invest
from aff_commercial_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as res_invest
from aff_residential_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_invest,c.nonagri_invest,d.com_invest,e.res_invest
)t2
)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as self_acquired_Assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b
ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
 from aff_commercial_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_current_cost,
c.nonagri_current_cost,d.com_current_cost,e.res_current_cost
)t3)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as Inherited_assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
 from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
from aff_commercial_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id
AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_current_cost,c.nonagri_current_cost,
d.com_current_cost,e.res_current_cost
)t4)x4
on pdt.affidavit_id=x4.affidavit_id and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Immov_Asset,0) as Other_Immov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Immov_Asset
from aff_other_immovable_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t5
on pdt.affidavit_id=t5.affidavit_id and pdt.RELATION_TYPE_CODE=t5.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='5'
GROUP BY  pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			//dd($immoveable_assets_total_test1);
			
			
			$data['immoveable_assets_total_test5'] = $immoveable_assets_total_test5;

				$immoveable_assets_total_test6 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.purcahse_price_self_acquired_immov, x2.Investment_Immov,x3.self_acquired_Assets_Value, x4.Inherited_assets_Value,t5.Other_Immov_Asset from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE, NAME,
agri_purchae_cost+nonagri_purchae_cost+com_purchae_cost+res_purchae_cost as
purcahse_price_self_acquired_immov  from
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_purchae_cost,0) as agri_purchae_cost,
IFNULL(c.nonagri_purchae_cost,0) as nonagri_purchae_cost,
IFNULL(d.com_purchae_cost,0) as com_purchae_cost,IFNULL(e.res_purchae_cost,0) as res_purchae_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as agri_purchae_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as nonagri_purchae_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as com_purchae_cost
from aff_commercial_buildings_details  where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(COST_AT_PURCHASE_TIME,0)) as res_purchae_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_purchae_cost,
c.nonagri_purchae_cost,d.com_purchae_cost,e.res_purchae_cost) t1)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_invest+nonagri_invest+com_invest+res_invest as Investment_Immov
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_invest,0) as agri_invest,
 IFNULL(c.nonagri_invest,0) as nonagri_invest,
 IFNULL(d.com_invest,0) as com_invest,IFNULL(e.res_invest,0) as res_invest
 FROM  aff_pan_details AS a
 JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
 LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as agri_invest from aff_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_LAND,0)) as nonagri_invest from aff_non_agricultural_land_details where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(
SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as com_invest
from aff_commercial_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(INVESTMENT_ON_BUILDINGS,0)) as res_invest
from aff_residential_buildings_details where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE
-- where a.CAND_CCODE=1
GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_invest,c.nonagri_invest,d.com_invest,e.res_invest
)t2
)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as self_acquired_Assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b
ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
 from aff_commercial_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details where is_deleted = '0'
and INHERITED_PROPERTY = 'No'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.agri_current_cost,
c.nonagri_current_cost,d.com_current_cost,e.res_current_cost
)t3)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,
agri_current_cost+nonagri_current_cost+com_current_cost+res_current_cost as Inherited_assets_Value
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.agri_current_cost,0) as agri_current_cost,
IFNULL(c.nonagri_current_cost,0) as nonagri_current_cost,
IFNULL(d.com_current_cost,0) as com_current_cost,IFNULL(e.res_current_cost,0) as res_current_cost
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as agri_current_cost
from aff_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE ) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as nonagri_current_cost
 from aff_non_agricultural_land_details where is_deleted = '0'
and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id
AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as com_current_cost
from aff_commercial_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS d ON a.affidavit_id=d.affidavit_id
AND a.RELATION_TYPE_CODE=d.RELATION_TYPE_CODE LEFT OUTER JOIN
(SELECT affidavit_id, RELATION_TYPE_CODE,SUM(IFNULL(APPROX_CURRENT_MARKET_VALUE,0)) as res_current_cost
from aff_residential_buildings_details  where is_deleted = '0' and INHERITED_PROPERTY = 'Yes'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS e ON a.affidavit_id=e.affidavit_id
AND a.RELATION_TYPE_CODE=e.RELATION_TYPE_CODE

GROUP BY a.affidavit_id,a.RELATION_TYPE_CODE,mr.RELATION_TYPE,a.NAME,b.agri_current_cost,c.nonagri_current_cost,
d.com_current_cost,e.res_current_cost
)t4)x4
on pdt.affidavit_id=x4.affidavit_id and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Immov_Asset,0) as Other_Immov_Asset
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Immov_Asset
from aff_other_immovable_assets where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)t5
on pdt.affidavit_id=t5.affidavit_id and pdt.RELATION_TYPE_CODE=t5.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='6'
GROUP BY  pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			//dd($immoveable_assets_total_test1);
			
			
			$data['immoveable_assets_total_test6'] = $immoveable_assets_total_test6;




    $l_9A = DB::table('aff_self_spouse_occupation')
       ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_self_spouse_occupation.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_self_spouse_occupation.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    		$join->on('aff_self_spouse_occupation.is_deleted','=', \DB::raw("0"));
				    	
				    }
				})
			
			->select('aff_m_relation_type.relation_type_code',DB::raw('group_concat(aff_self_spouse_occupation.occupation SEPARATOR "," ) as occupation'));
	    	
			$l_9A = $l_9A->groupBy("aff_m_relation_type.relation_type_code")->get()->toArray();
	    	$data['l_9A'] = $l_9A;
	    //	dd($l_9A);



$l_9B = DB::table('aff_dependant_source_of_income')
       ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_dependant_source_of_income.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_dependant_source_of_income.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    		$join->on('aff_dependant_source_of_income.is_deleted','=', \DB::raw("0"));
				    	
				    }
				})
			
			->select('aff_m_relation_type.relation_type_code',DB::raw('group_concat(aff_dependant_source_of_income.source_of_income SEPARATOR "," ) as source_of_income'));
	    	
			$l_9B = $l_9B->groupBy("aff_m_relation_type.relation_type_code")->get();
			//dd($l_9B);
	    	$data['l_9B'] = $l_9B;

	    	$l_9Bsource = DB::table('aff_dependant_source_of_income')
       ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_dependant_source_of_income.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_dependant_source_of_income.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_dependant_source_of_income.is_deleted','=', \DB::raw("0"));
				    	
				    }
				})
			
			->select('aff_m_relation_type.relation_type_code',DB::raw('group_concat(aff_dependant_source_of_income.source_of_income SEPARATOR "," ) as source_of_income'));
	    	
			$l_9Bsource = $l_9Bsource->whereIN('aff_m_relation_type.relation_type_code',[3,4,5,6])->get();
			//dd($l_9Bsource);
	    	$data['l_9Bsource'] = $l_9Bsource;

$l_9Bs = DB::table('aff_dependant_source_of_income')
               ->join("aff_m_relation_type", "aff_dependant_source_of_income.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
              
			
			->select('aff_m_relation_type.relation_type_code',DB::raw('group_concat(aff_dependant_source_of_income.source_of_income SEPARATOR "," ) as source_of_income'));
			 if($affidavit_id){
				$l_9Bs->where("aff_dependant_source_of_income.affidavit_id", $affidavit_id);
				$l_9Bs->where("aff_dependant_source_of_income.is_deleted", 0);
			}
	    	
			$l_9Bs = $l_9Bs->whereIN('aff_m_relation_type.relation_type_code',[3,4,5,6])->groupBy("aff_m_relation_type.relation_type_code")->get();
			//dd($l_9Bs);
	    	$data['l_9Bs'] = $l_9Bs;	





$l_9C = DB::table('aff_govt_public_company_contract')
       ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_govt_public_company_contract.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_govt_public_company_contract.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    		$join->on('aff_govt_public_company_contract.is_deleted','=', \DB::raw("0"));
				    	
				    }
				})
			
			->select('aff_m_relation_type.relation_type_code',DB::raw('group_concat(aff_govt_public_company_contract.govt_public_company SEPARATOR "," ) as govt_public_company'),DB::raw('group_concat(aff_govt_public_company_contract.details SEPARATOR "," ) as details'));
	    	
			$l_9C = $l_9C->groupBy("aff_m_relation_type.relation_type_code")->get();
			//dd($l_9B);
	    	$data['l_9C'] = $l_9C;

	    $l_9Cs = DB::table('aff_govt_public_company_contract')
              ->join("aff_m_relation_type", "aff_govt_public_company_contract.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
              
			
			->select('aff_m_relation_type.relation_type_code',DB::raw('group_concat(aff_govt_public_company_contract.govt_public_company SEPARATOR "," ) as govt_public_company'),DB::raw('group_concat(aff_govt_public_company_contract.details SEPARATOR "," ) as details'));
			if($affidavit_id){
				$l_9Cs->where("aff_govt_public_company_contract.affidavit_id", $affidavit_id);
				$l_9Cs->where('aff_govt_public_company_contract.is_deleted',0);
			}
	    	
			$l_9Cs = $l_9Cs->whereIN('aff_m_relation_type.relation_type_code',[3,4,5,6])->groupBy("aff_m_relation_type.relation_type_code")->get();
			//dd($l_9Bs);
	    	$data['l_9Cs'] = $l_9Cs;

	    	$l_9D = DB::table('aff_huf_trust_contracts')
              ->join("aff_m_relation_type", "aff_huf_trust_contracts.relation_type_code", "=", "aff_m_relation_type.relation_type_code")

               
			->select('aff_m_relation_type.relation_type_code',DB::raw('group_concat(aff_huf_trust_contracts.huf_trust_contracts SEPARATOR "," ) as huf_trust_contracts'),DB::raw('group_concat(aff_huf_trust_contracts.details SEPARATOR "," ) as details'));
			if($affidavit_id){
				$l_9D->where("aff_huf_trust_contracts.affidavit_id", $affidavit_id);
				$l_9D->where('aff_huf_trust_contracts.is_deleted',0);
			}
			
	    	
			$l_9D = $l_9D->orderBy("aff_m_relation_type.relation_type_code")->get();
			
	    	$data['l_9D'] = $l_9D;

	    	$l_9_private_company = DB::table('aff_private_company_contracts')
              ->join("aff_m_relation_type", "aff_private_company_contracts.relation_type_code", "=", "aff_m_relation_type.relation_type_code")

              
			
			->select('aff_m_relation_type.relation_type_code',DB::raw('group_concat(aff_private_company_contracts.name_private_company SEPARATOR "," ) as name_private_company'),DB::raw('group_concat(aff_private_company_contracts.details SEPARATOR "," ) as details'));

			if($affidavit_id){
				$l_9_private_company->where("aff_private_company_contracts.affidavit_id", $affidavit_id);
					$l_9_private_company->where("aff_private_company_contracts.is_deleted", '0');
			}
	    	
			$l_9_private_company = $l_9_private_company->whereIN('aff_m_relation_type.relation_type_code',[1,2,3,4,5,6])->orderBy("aff_m_relation_type.relation_type_code")->get();
			//dd($l_9Bs);
	    	$data['l_9_private_company'] = $l_9_private_company;






             $l_9_partnership_firm = DB::table('aff_partnership_firm_contracts')
              ->join("aff_m_relation_type", "aff_partnership_firm_contracts.relation_type_code", "=", "aff_m_relation_type.relation_type_code")

             
			
			->select('aff_m_relation_type.relation_type_code',DB::raw('group_concat(aff_partnership_firm_contracts.name_partnership_firm SEPARATOR "," ) as name_partnership_firm'),DB::raw('group_concat(aff_partnership_firm_contracts.details SEPARATOR "," ) as details'));

			 if($affidavit_id){
				$l_9_partnership_firm->where("aff_partnership_firm_contracts.affidavit_id", $affidavit_id);
				$l_9_partnership_firm->where("aff_partnership_firm_contracts.is_deleted", 0);
			}
	    	
			$l_9_partnership_firm = $l_9_partnership_firm->whereIN('aff_m_relation_type.relation_type_code',[1,2,3,4,5,6])->orderBy("aff_m_relation_type.relation_type_code")->get();
			//dd($l_9Bs);
	    	$data['l_9_partnership_firm'] = $l_9_partnership_firm;	

	    	$education = AffEducation::select("aff_cand_qualification.*");
			//->where("aff_cand_qualification.candidate_id", Auth::user()->id);
			if($affidavit_id){
				$education->where("aff_cand_qualification.affidavit_id", $affidavit_id);
			}
			$education = $education->where('is_deleted','0')->get();
			
			$data['education'] = $education;



	$liabilites_total_1 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE, rtt.RELATION_TYPE_CODE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='1'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_1'] = $liabilites_total_1;

			$liabilites_total_2 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE, rtt.RELATION_TYPE_CODE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='2'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_2'] = $liabilites_total_2;

			$liabilites_total_3 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE, rtt.RELATION_TYPE_CODE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='3'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_3'] = $liabilites_total_3;


			$liabilites_total_4 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE, rtt.RELATION_TYPE_CODE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='4'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_4'] = $liabilites_total_4;

			$liabilites_total_5 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE, rtt.RELATION_TYPE_CODE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='5'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_5'] = $liabilites_total_5;

			$liabilites_total_6 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE, rtt.RELATION_TYPE_CODE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='6'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_6'] = $liabilites_total_6;

			$total_govt_dues = DB::table('aff_l_govt_dues')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_l_govt_dues.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_l_govt_dues.affidavit_id','=', \DB::raw("'$affidavit_id'"));
             }
				})
			 ->leftjoin("aff_m_govt_dept_name", "aff_l_govt_dues.govt_dept_name_code", "=", "aff_m_govt_dept_name.govt_dept_name_code")

			 ->select("aff_m_relation_type.relation_type", "aff_l_govt_dues.*",
             DB::raw('group_concat(aff_l_govt_dues.due_details SEPARATOR " | " ) as due_details' ),
     	
            DB::raw('group_concat(aff_l_govt_dues.amount SEPARATOR " | " ) as amount'),

            DB::raw('SUM(amount) as totalamount') )
			 
       //->whereIN('aff_m_govt_dept_name.govt_dept_name_code',[1,5,6,8,10,11])
		  ->groupBy("aff_m_relation_type.relation_type_code")->get();

		//dd($total_govt_dues);

       	
			$data['total_govt_dues'] = $total_govt_dues;




//////////////////////////// agricultural_land start ////////////////////////////////////////// 

     	$agricultural_land_test_1 = DB::table('aff_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'1')->get();

			$data['agricultural_land_test_1'] = $agricultural_land_test_1;

			$agricultural_land_test_2 = DB::table('aff_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'2')->get();

			$data['agricultural_land_test_2'] = $agricultural_land_test_2;


			$agricultural_land_test_3 = DB::table('aff_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'3')->get();

			$data['agricultural_land_test_3'] = $agricultural_land_test_3;

			$agricultural_land_test_4 = DB::table('aff_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'4')->get();

			$data['agricultural_land_test_4'] = $agricultural_land_test_4;


			$agricultural_land_test_5 = DB::table('aff_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'5')->get();

			$data['agricultural_land_test_5'] = $agricultural_land_test_5;


			$agricultural_land_test_6 = DB::table('aff_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'6')->get();

			$data['agricultural_land_test_6'] = $agricultural_land_test_6;

//dd($agricultural_land_test_1);









/////////////////////////////// End agricultural_land ////////////////////////////////////////


//////////////////////////// Non agricultural_land start ////////////////////////////////////////// 

     	$non_agricultural_land_test_1 = DB::table('aff_non_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_non_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_non_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_non_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_non_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'1')->get();

			$data['non_agricultural_land_test_1'] = $non_agricultural_land_test_1;

			$non_agricultural_land_test_2 = DB::table('aff_non_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_non_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_non_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    		$join->on('aff_non_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_non_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'2')->get();

			$data['non_agricultural_land_test_2'] = $non_agricultural_land_test_2;


			$non_agricultural_land_test_3 = DB::table('aff_non_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_non_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_non_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    		$join->on('aff_non_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_non_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'3')->get();

			$data['non_agricultural_land_test_3'] = $non_agricultural_land_test_3;

			$non_agricultural_land_test_4 = DB::table('aff_non_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_non_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_non_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    		$join->on('aff_non_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_non_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'4')->get();

			$data['non_agricultural_land_test_4'] = $non_agricultural_land_test_4;


			$non_agricultural_land_test_5 = DB::table('aff_non_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_non_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_non_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    		$join->on('aff_non_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_non_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'5')->get();

			$data['non_agricultural_land_test_5'] = $non_agricultural_land_test_5;


			$non_agricultural_land_test_6 = DB::table('aff_non_agricultural_land_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_non_agricultural_land_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_non_agricultural_land_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    		$join->on('aff_non_agricultural_land_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_non_agricultural_land_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'6')->get();

			$data['non_agricultural_land_test_6'] = $non_agricultural_land_test_6;

//dd($agricultural_land_test_1);









/////////////////////////////// End Non agricultural_land ////////////////////////////////////////





//////////////////////////// Commercial Building start ////////////////////////////////////////// 

     	$aff_commercial_buildings_details_1 = DB::table('aff_commercial_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_commercial_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_commercial_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    		$join->on('aff_commercial_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_commercial_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'1')->get();

			$data['aff_commercial_buildings_details_1'] = $aff_commercial_buildings_details_1;

			$aff_commercial_buildings_details_2 = DB::table('aff_commercial_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_commercial_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_commercial_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_commercial_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_commercial_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'2')->get();

			$data['aff_commercial_buildings_details_2'] = $aff_commercial_buildings_details_2;


			$aff_commercial_buildings_details_3 = DB::table('aff_commercial_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_commercial_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_commercial_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_commercial_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_commercial_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'3')->get();

			$data['aff_commercial_buildings_details_3'] = $aff_commercial_buildings_details_3;

			$aff_commercial_buildings_details_4 = DB::table('aff_commercial_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_commercial_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_commercial_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_commercial_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_commercial_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'4')->get();

			$data['aff_commercial_buildings_details_4'] = $aff_commercial_buildings_details_4;


			$aff_commercial_buildings_details_5 = DB::table('aff_commercial_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_commercial_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_commercial_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_commercial_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_commercial_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'5')->get();

			$data['aff_commercial_buildings_details_5'] = $aff_commercial_buildings_details_5;


			$aff_commercial_buildings_details_6 = DB::table('aff_commercial_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_commercial_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_commercial_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_commercial_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_commercial_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'6')->get();

			$data['aff_commercial_buildings_details_6'] = $aff_commercial_buildings_details_6;

//dd($agricultural_land_test_1);









/////////////////////////////// End Commercial Building ////////////////////////////////////////



//////////////////////////// Residential Building start ////////////////////////////////////////// 

     	$aff_residential_buildings_details_1 = DB::table('aff_residential_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_residential_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_residential_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_residential_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_residential_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'1')->get();

			$data['aff_residential_buildings_details_1'] = $aff_residential_buildings_details_1;

			$aff_residential_buildings_details_2 = DB::table('aff_residential_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_residential_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_residential_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_residential_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_residential_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'2')->get();

			$data['aff_residential_buildings_details_2'] = $aff_residential_buildings_details_2;


			$aff_residential_buildings_details_3 = DB::table('aff_residential_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_residential_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_residential_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_residential_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_residential_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'3')->get();

			$data['aff_residential_buildings_details_3'] = $aff_residential_buildings_details_3;

			$aff_residential_buildings_details_4 = DB::table('aff_residential_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_residential_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_residential_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_residential_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_residential_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'4')->get();

			$data['aff_residential_buildings_details_4'] = $aff_residential_buildings_details_4;


			$aff_residential_buildings_details_5 = DB::table('aff_residential_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_residential_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_residential_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_residential_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_residential_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'5')->get();

			$data['aff_residential_buildings_details_5'] = $aff_residential_buildings_details_5;


			$aff_residential_buildings_details_6 = DB::table('aff_residential_buildings_details')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_residential_buildings_details.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_residential_buildings_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_residential_buildings_details.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_residential_buildings_details.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'6')->get();

			$data['aff_residential_buildings_details_6'] = $aff_residential_buildings_details_6;

//dd($agricultural_land_test_1);









/////////////////////////////// End Residential Building ////////////////////////////////////////

////////////////////////////////// Start Other interst in property///////////////////////////

 


         	$aff_other_immovable_assets_1 = DB::table('aff_other_immovable_assets')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_other_immovable_assets.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_other_immovable_assets.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_other_immovable_assets.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_other_immovable_assets.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'1')->get();

			$data['aff_other_immovable_assets_1'] = $aff_other_immovable_assets_1;



			$aff_other_immovable_assets_2 = DB::table('aff_other_immovable_assets')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_other_immovable_assets.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_other_immovable_assets.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_other_immovable_assets.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_other_immovable_assets.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'2')->get();

			$data['aff_other_immovable_assets_2'] = $aff_other_immovable_assets_2;


			$aff_other_immovable_assets_3 = DB::table('aff_other_immovable_assets')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_other_immovable_assets.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_other_immovable_assets.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_other_immovable_assets.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_other_immovable_assets.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'3')->get();

			$data['aff_other_immovable_assets_3'] = $aff_other_immovable_assets_3;

			$aff_other_immovable_assets_4 = DB::table('aff_other_immovable_assets')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_other_immovable_assets.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_other_immovable_assets.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_other_immovable_assets.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_other_immovable_assets.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'4')->get();

			$data['aff_other_immovable_assets_4'] = $aff_other_immovable_assets_4;


			$aff_other_immovable_assets_5 = DB::table('aff_other_immovable_assets')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_other_immovable_assets.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_other_immovable_assets.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_other_immovable_assets.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_other_immovable_assets.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'5')->get();

			$data['aff_other_immovable_assets_5'] = $aff_other_immovable_assets_5;


			$aff_other_immovable_assets_6 = DB::table('aff_other_immovable_assets')

			 ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
				{
				    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_other_immovable_assets.relation_type_code');
				    if($affidavit_id){
				    	$join->on('aff_other_immovable_assets.affidavit_id','=', \DB::raw("'$affidavit_id'"));
				    	$join->on('aff_other_immovable_assets.is_deleted','=', \DB::raw("0"));
				    }
				})
 ->select("aff_m_relation_type.relation_type","aff_m_relation_type.relation_type","aff_other_immovable_assets.*" )
 
		->where("aff_m_relation_type.relation_type_code","=",'6')->get();

			$data['aff_other_immovable_assets_6'] = $aff_other_immovable_assets_6;

////////////////////////////////// End Other interst in property///////////////////////////



////////////////////////////////    Liblity      ///////////////////////////////////////

	$liabilites_total_test1 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='1'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_test1'] = $liabilites_total_test1;

$liabilites_total_test2 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='2'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_test2'] = $liabilites_total_test2;

			$liabilites_total_test3 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='3'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_test3'] = $liabilites_total_test3;

			$liabilites_total_test4 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='4'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_test4'] = $liabilites_total_test4;

			$liabilites_total_test5 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='5'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_test5'] = $liabilites_total_test5;

			$liabilites_total_test6 = DB::select("select pdt.affidavit_id,pdt.NAME, rtt.RELATION_TYPE,x1.Govt_dues,x2.Total_Loan,x3.Other_Amt,
x4.Other_Amt_Dispute from aff_pan_details pdt
join aff_m_relation_type rtt on rtt.RELATION_TYPE_CODE=pdt.RELATION_TYPE_CODE
left outer join
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_GovtDues,0) as Govt_dues
FROM   aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_GovtDues from aff_l_govt_dues where is_deleted = '0' 
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
)x1
on pdt.affidavit_id=x1.affidavit_id and pdt.RELATION_TYPE_CODE=x1.RELATION_TYPE_CODE
left outer join
(SELECT affidavit_id, RELATION_TYPE_CODE,RELATION_TYPE,NAME,BankLoan_Amt+OtherLoan_Amt as Total_Loan
from
(
SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,
IFNULL(b.BankLoan_Amt,0) as BankLoan_Amt,IFNULL(c.OtherLoan_Amt,0) as OtherLoan_Amt
FROM aff_pan_details AS a
JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as BankLoan_Amt
from aff_l_loan_bank_finc_inst where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(OUTSTANDING_AMOUNT,0)) as OtherLoan_Amt
from aff_l_loan_individual_entity where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS c ON a.affidavit_id=c.affidavit_id AND a.RELATION_TYPE_CODE=c.RELATION_TYPE_CODE
GROUP BY a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,b.BankLoan_Amt,c.OtherLoan_Amt
)t2)x2
on pdt.affidavit_id=x2.affidavit_id and pdt.RELATION_TYPE_CODE=x2.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Other_Amt_Liabilites,0) as Other_Amt
FROM  aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Other_Amt_Liabilites
from aff_l_other_liabilities where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x3
on pdt.affidavit_id=x3.affidavit_id and pdt.RELATION_TYPE_CODE=x3.RELATION_TYPE_CODE
left outer join
(SELECT a.affidavit_id, a.RELATION_TYPE_CODE,mr.RELATION_TYPE, a.NAME,IFNULL(b.Amt_Dispute,0) as Other_Amt_Dispute
FROM   aff_pan_details AS a JOIN aff_m_relation_type mr on a.RELATION_TYPE_CODE=mr.RELATION_TYPE_CODE
LEFT OUTER JOIN
(SELECT affidavit_id,RELATION_TYPE_CODE,SUM(IFNULL(AMOUNT,0)) as Amt_Dispute
from aff_l_liabilities_disputes where is_deleted = '0'
GROUP BY affidavit_id, RELATION_TYPE_CODE) AS b ON a.affidavit_id=b.affidavit_id
AND a.RELATION_TYPE_CODE=b.RELATION_TYPE_CODE)x4
on pdt.affidavit_id=x4.affidavit_id
and pdt.RELATION_TYPE_CODE=x4.RELATION_TYPE_CODE
where pdt.affidavit_id='$affidavit_id' and pdt.RELATION_TYPE_CODE='6'
GROUP BY pdt.affidavit_id,pdt.RELATION_TYPE_CODE");
			
			$data['liabilites_total_test6'] = $liabilites_total_test6;








///////////////////////////////////////////////////////////////////////////////////////////////// 







///////////////////////////////////////////////////////////////////////////////////////////

//dd($education);
			// $pan_details =DB::table('aff_pan_details')
			// ->rightjoin('aff_m_relation_type', function($join) use($affidavit_id)
			// 	{
			// 	    $join->on('aff_m_relation_type.relation_type_code', '=', 'aff_pan_details.relation_type_code');
			// 	    if($affidavit_id){
			// 	    	$join->on('aff_pan_details.affidavit_id','=', \DB::raw("'$affidavit_id'"));
			// 	    }
			// 	})->get();
			// dd($pan_details);

///////////////////////////////////////////////////////////////////////////////
									
			//dd($data);
			
			$data['pdf'] = '0';
						
			//dd($data);
			
			if($request->pdf=='yes'){
				
				//dd($data);
				
				$data['pdf'] = '1';
				//$datas['abcd'] = $abcd;
				
				$pdf = PDF::loadView('affidavit.reports.part_a_detailed_report_pdf', compact('data',));
				
				return $pdf->download('Detailed Report.pdf');
			}
			
			
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			
	    	return view('affidavit.reports.part_a_detailed_report', ['data'=>$data, 'user_data'=>$d, 'education'=>$education]);
    	   }catch (Exception $e){

    	   	//dd($e);
    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
            Session::flash('status',0);
      		Session::flash('flash-message', "Something went wrong, please try again after sometime.");
    		return Redirect::back();
		}
    }
	
	
}
