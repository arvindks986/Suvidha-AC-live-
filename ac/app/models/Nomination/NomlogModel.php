<?php namespace App\models\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth, Session;
use App\models\Nomination\{NominationProposerModel,NominationPoliceCaseModel, ProfileModel}; 

class NomlogModel extends Model
{
  protected $table = 'nomination_application_logs';
  public $fillable = ['id', 'candidate_id', 'nomination_application_id', 'st_code', 'election_type', 'pc_no', 'ac_no', 'added_create_at', 'created_at', 'added_update_at', 'updated_at', 'created_by', 'updated_by','qrcode'];
 
  public static function get_last_nomination_application(){
    return NomlogModel::where('candidate_id', Auth::id())->latest('id')->first()->toArray();
  }

  public static function get_nomination_application($id){
    $object = NomlogModel::where('candidate_id', Auth::id())->find($id);
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
  public static function count_nomination_application($data = array()){ 
  
    $sql = NomlogModel::where([
        'candidate_id' => Auth::id(),
        'st_code' => $data['st_code'], 
        'ac_no' => $data['ac_no'],
    ]);
	
	
	
	
	if(isset($data['nomination_id'])){ 
		//$req  = decrypt_String($data['nomination_id']); 
		//$sql->where('id', '!=', $req);
    }
	
    if(Session::has('nomination_id')){ 
        $sql->where('id', '!=', Session::get('nomination_id'));
    }
	return $sql->count();
  }
  
  public static function add_nomination_application($nid, $data = array()){ 
		
        $object = new NomlogModel();
        $object->nomination_application_id = $nid;
        $object->candidate_id = Auth::id();
        $object->st_code = $data['st_code']; 
        $object->ac_no = $data['ac_no']; 
        $object->election_id = $data['election_id']; 
        $object->updated_at = date('Y-m-d h:i:s'); 
        $object->step = 1; 
        $object->nomination_no = strtoupper("NOM-".time());    
		//echo Auth::id(); die;
		$candidate = ProfileModel::where('candidate_id', Auth::id())->first();	
		
		if($candidate){ 
			$object->nomination_application_id = $nid;
			$object->name = $candidate['name'];
			$object->email = $candidate['email'];
			$object->mobile = $candidate['mobile']; 
			$object->hname = $candidate['hname']; 
			$object->vname = $candidate['vname']; 
			$object->alias_name = $candidate['alias_name']; 
			$object->alias_hname = $candidate['alias_hname']; 
			$object->father_name = $candidate['father_name']; 
			$object->father_hname      = $candidate['father_hname']; 
			$object->father_vname = $candidate['father_vname']; 
			$object->category = $candidate['category']; 
			$object->pan_number = $candidate['pan_number'];
			$object->dob = $candidate['dob']; 
			$object->age = $candidate['age']; 
			$object->address = $candidate['address']; 
			$object->haddress      = $candidate['haddress']; 
			$object->vaddress = $candidate['vaddress']; 
			$object->epic_no = $candidate['epic_no']; 
			$object->part = $candidate['part_no']; 
			$object->serial = $candidate['serial_no']; 
			$object->updated_at = date('Y-m-d h:i:s'); 
			$object->log_added_updated_at = date('Y-m-d h:i:s'); 
			$object->log_updated_at = date('Y-m-d h:i:s'); 
			$object->gender = $candidate['gender'];
		}
		if(!$object->save()){
			return false;
		}
    return true;
  }

  public static function add_nomination_part1($nid, $data = array()){
    
    $object = NomlogModel::where('finalize','0')->where('candidate_id', Auth::id())->where('nomination_application_id', $nid)->first();
	if(!empty( $object )){
	//echo "<pre>"; print_r($object); die;
    $object->recognized_party = $data['recognized_party']; 
    $object->legislative_assembly = $data['legislative_assembly']; 
    $object->name = $data['name']; 
    $object->father_name = $data['father_name']; 
    $object->address = $data['address']; 
    $object->serial_no = $data['serial_no']; 
    $object->part_no = $data['part_no']; 
    $object->resident_ac_no = $data['resident_ac_no']; 
    $object->proposer_name = $data['proposer_name']; 
    $object->proposer_serial_no = $data['proposer_serial_no']; 
    $object->proposer_part_no = $data['proposer_part_no']; 
    $object->proposer_assembly = $data['proposer_assembly']; 
    $object->apply_date = date('Y-m-d', strtotime($data['apply_date'])); 
	$object->updated_at = date('Y-m-d h:i:s'); 
	$object->step = 2;         
    $object->image      = $data['image_name']; 
    return $object->save();
	}
  }

  public static function add_nomination_part2($nid, $data = array()){ 
    $object = NomlogModel::where('finalize','0')->where('candidate_id' , Auth::id())->where('nomination_application_id', $nid)->first();
	if(!empty($object)){
    $object->recognized_party = $data['recognized_party']; 
    $object->legislative_assembly = $data['legislative_assembly']; 
    $object->name = $data['name']; 
    $object->father_name = $data['father_name']; 
    $object->address = $data['address']; 
    $object->serial_no = $data['serial_no']; 
    $object->part_no = $data['part_no']; 
    $object->resident_ac_no = $data['resident_ac_no']; 
    $object->apply_date = date('Y-m-d', strtotime($data['apply_date'])); 
	$object->updated_at = date('Y-m-d h:i:s'); 
	$object->step = 2;         
    $object->image      = $data['image_name']; 
    return $object->save();
	}
  }

  public static function add_nomination_part3($nid, $data = array()){ 
    $object = NomlogModel::where('finalize','0')->where('candidate_id' , Auth::id())->where('nomination_application_id', $nid)->first();
	if(!empty( $object )){
    $object->age      = $data['age']; 
    $object->party_id = $data['party_id']; 
    $object->suggest_symbol_1 = $data['suggest_symbol_1']; 
    $object->suggest_symbol_2 = $data['suggest_symbol_2']; 
    $object->suggest_symbol_3 = $data['suggest_symbol_3']; 
    $object->language = $data['language']; 
    $object->category = $data['category']; 
    $object->part3_date = ($data['part3_date'])?date('Y-m-d', strtotime($data['part3_date'])):''; 
    $object->part3_cast_state           = $data['part3_cast_state'];
    $object->part3_address              = $data['part3_address'];
    $object->part3_legislative_state    = $data['part3_legislative_state'];
	$object->updated_at = date('Y-m-d h:i:s'); 
	$object->step = 3;         
    $object->save();
	return $object['id'];
	}
   }
  
   public static function add_nomination_part3a($nid, $data = array()){	
   
	function checkdataLog($data){
		if(empty($data)){
			return '0'; 
		} 
		if($data=='yes' || $data=='Yes'){
			return '1'; 
		}   
		if($data=='no' || $data=='No'){
			return '2';
		} 	
    }	
   
    //$object = NomlogModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
	$object = NomlogModel::where('finalize','0')->where('candidate_id' , Auth::id())->where('nomination_application_id', $nid)->first();
	if(!empty( $object )){
    $object->have_police_case       =  checkdataLog($data['have_police_case']);
    $object->profit_under_govt      =  checkdataLog($data['profit_under_govt']);
    $object->office_held            =  ($data['office_held'])?$data['office_held']:''; 
    $object->court_insolvent        =  checkdataLog($data['court_insolvent']);
    $object->discharged_insolvency  =  ($data['discharged_insolvency'])?$data['discharged_insolvency']:''; 
    $object->allegiance_to_foreign_country  =  checkdataLog($data['allegiance_to_foreign_country']); 
    $object->country_detail                 = ($data['country_detail'])?$data['country_detail']:'';
    $object->disqualified_section8A         = checkdataLog($data['disqualified_section8A']);
    $object->disqualified_period       = ($data['disqualified_period'])?$data['disqualified_period']:''; 
    $object->disloyalty_status         = checkdataLog($data['disloyalty_status']);
    $object->date_of_dismissal         = ($data['date_of_dismissal'])?date('Y-m-d', strtotime($data['date_of_dismissal'])):''; 
    $object->subsiting_gov_taken       = checkdataLog($data['subsiting_gov_taken']);
    $object->subsitting_contract       = ($data['subsitting_contract'])?$data['subsitting_contract']:''; 
    $object->managing_agent                   = checkdataLog($data['managing_agent']); 
	$object->updated_at				   = date('Y-m-d h:i:s'); 
	$object->step 					   = 4;         
    $object->gov_detail                       = ($data['gov_detail'])?$data['gov_detail']:''; 
    $object->disqualified_by_comission_10Asec = checkdataLog($data['disqualified_by_comission_10Asec']);
    $object->date_of_disqualification         = ($data['date_of_disqualification'])?date('Y-m-d', strtotime($data['date_of_disqualification'])):'';  
    $object->date_of_disloyal                 = ($data['date_of_disloyal'])?date('Y-m-d', strtotime($data['date_of_disloyal'])):'';  
    return $object->save();
	}
  }
  
 
  public static function add_affidavit($nid, $data = array()){
    $object = NomlogModel::where('finalize','0')->where('candidate_id' , Auth::id())->where('nomination_application_id', $nid)->first();
	if(!empty( $object )){
    $object->affidavit = $data['affidavit_name'];
	$object->updated_at	= date('Y-m-d h:i:s'); 
	$object->step 	    = 5;         
    return $object->save();
	}
  }

  public static function get_nomination($id){
    $data = [];
    $object = NomlogModel::join('profile','profile.candidate_id','=','nomination_application.candidate_id')->where('nomination_application.id',$id)->select('nomination_application.*','nomination_application.id as nomination_id')->first();
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
  public static function add_qrcode($data){
    $object = NomlogModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
	if(!empty( $object )){
    $object->qrcode = $data['qrcode_path'];
    return $object->save();
	}
  }
  public static function finalize_nomination($id){	  
    $object = NomlogModel::where('finalize','0')->where('candidate_id' , Auth::id())->where('nomination_application_id', $id)->first();
	if(!empty($object)){
     $object->finalize=1;
	 $object->updated_at=date('Y-m-d h:i:s');
	 $object->is_apply_prescrutiny=1;
	 $object->prescrutiny_apply_datetime=date('Y-m-d H:i:s', time());
     return $object->save();
	}
  }

  ################################### RO Modal For the Same ##########################

  public static function add_nomination_part2_ro($nid, $data = array()){
    $object = NomlogModel::where('finalize','0')->where('candidate_id' , $data['candidate_id'])->where('nomination_application_id', $nid)->first();
	if(!empty($object)){
    $object->recognized_party = $data['recognized_party']; 
    $object->legislative_assembly = $data['legislative_assembly']; 
    $object->name = $data['name']; 
    $object->father_name = $data['father_name']; 
    $object->address = $data['address']; 
    $object->serial_no = $data['serial_no']; 
    $object->part_no = $data['part_no']; 
    $object->resident_ac_no = $data['resident_ac_no']; 
    $object->apply_date = date('Y-m-d', strtotime($data['apply_date'])); 
	$object->updated_at = date('Y-m-d h:i:s'); 
	$object->step = 2;         
    $object->image      = $data['image_name']; 
    return $object->save();
	}
  }

    public static function add_nomination_application_ro($nid, $data = array()){		
      $object = new NomlogModel();
      $object->nomination_application_id = $nid;
      $object->candidate_id = Auth::id();
      $object->st_code = $data['st_code']; 
      $object->ac_no = $data['ac_no']; 
      $object->election_id = $data['election_id']; 
      $object->updated_at = date('Y-m-d h:i:s'); 
      $object->step = 1; 
      $object->nomination_no = strtoupper("NOM-".time());     
  //echo Auth::id(); die;
  $candidate = ProfileModel::where('candidate_id', $data['candidate_id'])->first();	

  if($candidate){ 
    $object->nomination_application_id = $nid;
    $object->name = $candidate['name'];
    $object->email = $candidate['email'];
    $object->mobile = $candidate['mobile']; 
    $object->hname = $candidate['hname']; 
    $object->vname = $candidate['vname']; 
    $object->alias_name = $candidate['alias_name']; 
    $object->alias_hname = $candidate['alias_hname']; 
    $object->father_name = $candidate['father_name']; 
    $object->father_hname      = $candidate['father_hname']; 
    $object->father_vname = $candidate['father_vname']; 
    $object->category = $candidate['category']; 
    $object->pan_number = $candidate['pan_number'];
    $object->dob = $candidate['dob']; 
    $object->age = $candidate['age']; 
    $object->address = $candidate['address']; 
    $object->haddress      = $candidate['haddress']; 
    $object->vaddress = $candidate['vaddress']; 
    $object->epic_no = $candidate['epic_no']; 
    $object->part = $candidate['part_no']; 
    $object->serial = $candidate['serial_no']; 
    $object->updated_at = date('Y-m-d h:i:s'); 
    $object->log_added_updated_at = date('Y-m-d h:i:s'); 
    $object->log_updated_at = date('Y-m-d h:i:s'); 
    $object->gender = $candidate['gender'];
  }
  if(!$object->save()){
    return false;
  }
  return true;
  }

  public static function add_nomination_part3_ro($nid, $data = array()){ 
  $object = NomlogModel::where('finalize','0')->where('candidate_id' , $data['candidate_id'])->where('nomination_application_id', $nid)->first();
  if(!empty( $object )){
  $object->age      = $data['age']; 
  $object->party_id = $data['party_id']; 
  $object->suggest_symbol_1 = $data['suggest_symbol_1']; 
  $object->suggest_symbol_2 = $data['suggest_symbol_2']; 
  $object->suggest_symbol_3 = $data['suggest_symbol_3']; 
  $object->language = $data['language']; 
  $object->category = $data['category']; 
  $object->part3_date = ($data['part3_date'])?date('Y-m-d', strtotime($data['part3_date'])):''; 
  $object->part3_cast_state           = $data['part3_cast_state'];
  $object->part3_address              = $data['part3_address'];
  $object->part3_legislative_state    = $data['part3_legislative_state'];
  $object->updated_at = date('Y-m-d h:i:s'); 
  $object->step = 3;         
  $object->save();
  return $object['id'];
  }
  }

  public static function add_nomination_part3a_ro($nid, $data = array()){	

  function checkdataLog($data){
    if(empty($data)){
      return '0'; 
    } 
    if($data=='yes' || $data=='Yes'){
      return '1'; 
    }   
    if($data=='no' || $data=='No'){
      return '2';
    } 	
    }	
  
    //$object = NomlogModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
  $object = NomlogModel::where('finalize','0')->where('candidate_id' , $data['candidate_id'])->where('nomination_application_id', $nid)->first();
  if(!empty( $object )){
    $object->have_police_case       =  checkdataLog($data['have_police_case']);
  $object->profit_under_govt      =  checkdataLog($data['profit_under_govt']);
  $object->office_held            =  ($data['office_held'])?$data['office_held']:''; 
  $object->court_insolvent        =  checkdataLog($data['court_insolvent']);
  $object->discharged_insolvency  =  ($data['discharged_insolvency'])?$data['discharged_insolvency']:''; 
  $object->allegiance_to_foreign_country  =  checkdataLog($data['allegiance_to_foreign_country']); 
  $object->country_detail                 = ($data['country_detail'])?$data['country_detail']:'';
  $object->disqualified_section8A         = checkdataLog($data['disqualified_section8A']);
  $object->disqualified_period       = ($data['disqualified_period'])?$data['disqualified_period']:''; 
  $object->disloyalty_status         = checkdataLog($data['disloyalty_status']);
  $object->date_of_dismissal         = ($data['date_of_dismissal'])?date('Y-m-d', strtotime($data['date_of_dismissal'])):''; 
  $object->subsiting_gov_taken       = checkdataLog($data['subsiting_gov_taken']);
  $object->subsitting_contract       = ($data['subsitting_contract'])?$data['subsitting_contract']:''; 
  $object->managing_agent                   = checkdataLog($data['managing_agent']); 
  $object->updated_at				   = date('Y-m-d h:i:s'); 
  $object->step 					   = 4;         
  $object->gov_detail                       = ($data['gov_detail'])?$data['gov_detail']:''; 
  $object->disqualified_by_comission_10Asec = checkdataLog($data['disqualified_by_comission_10Asec']);
  $object->date_of_disqualification         = ($data['date_of_disqualification'])?date('Y-m-d', strtotime($data['date_of_disqualification'])):'';  
  $object->date_of_disloyal                 = ($data['date_of_disloyal'])?date('Y-m-d', strtotime($data['date_of_disloyal'])):'';  
  return $object->save();
  }
  }

  #################################### End RO Modal ##################################
}