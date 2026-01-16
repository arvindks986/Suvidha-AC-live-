<?php namespace App\models\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth, Session;
use App\models\Nomination\{NominationProposerModel,NominationPoliceCaseModel, ProfileModel};  
use App\Classes\xssClean;
class NominationApplicationModel extends Model
{
	
  protected $table = 'nomination_application'; 
  public $fillable = ['id', 'is_apply_prescrutiny', 'prescrutiny_apply_datetime', 'recognized_type', 'unrecognized_type', 'candidate_id', 'st_code', 'election_type', 'pc_no', 'ac_no', 'added_create_at', 'created_at', 'added_update_at', 'updated_at', 'created_by', 'updated_by','qrcode', 'name', 'email', 'mobile', 'hname', 'vname', 'alias_name', 'alias_hname', 'father_name', 'father_hname', 'father_vname', 'category', 'pan_number', 'dob', 'age', 'address', 'haddress', 'vaddress', 'epic_no', 'part', 'serial', 'gender', 'resident_ac_no', 'epic_no_proposer_serch', 'party_id', 'party_id2', 'election_type_id','img_count','img_time'];
 
  public static function get_last_nomination_application(){
    return NominationApplicationModel::where('candidate_id', Auth::id())->latest('id')->first()->toArray();
  }

  public static function get_nomination_application($id){ 
    
    $object = NominationApplicationModel::where('candidate_id', Auth::id())->find($id);
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
  public static function count_nomination_application($data = array()){ 
    $sql = NominationApplicationModel::where([
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
	 $sql->where('finalize', '!=', 3);
	 return $sql->count();
  }
  
  public static function add_nomination_application($data = array()){ 
     $xss = new xssClean; 
    if(!empty($data['nomination_id']) && isset($data['nomination_id'])){
		
        $object = NominationApplicationModel::where('candidate_id', Auth::id())->find(decrypt_String($data['nomination_id']));
        $object->st_code = $xss->clean_input($data['st_code']); 
        $object->ac_no = $xss->clean_input($data['ac_no']); 
        $object->updated_at = date('Y-m-d h:i:s'); 
        $object->step = 1; 
        $object->election_id = $xss->clean_input($data['eid']); 
        $object->election_type_id = $data['election_id']; 
		
    }else{
        
		$object = new NominationApplicationModel();
        $object->candidate_id = Auth::id();
        $object->st_code = $xss->clean_input($data['st_code']); 
        $object->ac_no = $xss->clean_input($data['ac_no']); 
		$object->election_id = $xss->clean_input($data['eid']); 
        $object->election_type_id = $data['election_id']; 
        $object->updated_at = date('Y-m-d h:i:s'); 
        $object->step = 1; 
        $object->nomination_no = strtoupper("NOM-".time());
    
	}
	//echo Auth::id(); die;
   // $candidate = ProfileModel::find(Auth::id());	
	$candidate = ProfileModel::where('candidate_id', Auth::id())->first();	
	//echo "<pre>"; print_r(   $candidate ); die;	
    if($candidate){
    
		$object->name = $xss->clean_input($candidate['name']);
        $object->email = $xss->clean_input($candidate['email']);
        $object->mobile = $xss->clean_input($candidate['mobile']); 
        $object->hname = $xss->clean_input($candidate['hname']); 
        $object->vname = $xss->clean_input($candidate['vname']); 
        $object->alias_name = $xss->clean_input($candidate['alias_name']); 
        $object->alias_hname = $xss->clean_input($candidate['alias_hname']); 
        $object->father_name = $xss->clean_input($candidate['father_name']); 
        $object->father_hname      = $xss->clean_input($candidate['father_hname']); 
        $object->father_vname =$xss->clean_input( $candidate['father_vname']); 
        $object->category = $xss->clean_input($candidate['category']); 
        $object->pan_number = $xss->clean_input($candidate['pan_number']);
        $object->dob = $candidate['dob']; 
        $object->age = $candidate['age']; 
        $object->address = $xss->clean_input($candidate['address']); 
        $object->haddress      = $xss->clean_input($candidate['haddress']); 
        $object->vaddress = $xss->clean_input($candidate['vaddress']); 
        $object->part = $xss->clean_input($candidate['part_no']); 
        $object->serial = $xss->clean_input($candidate['serial_no']); 
		$object->nomination_type = 1; 
        $object->updated_at = date('Y-m-d h:i:s'); 
        $object->gender = $candidate['gender'];
    
	}
	
    if(!$object->save()){
        return false;
    }
	
    \Session::put('nomination_id', $object->id);
    return true;
  }

  public static function add_nomination_part1($data = array()){ 
   $xss = new xssClean; 
  //dd($data) ; 
    //$object = NominationApplicationModel::where('finalize','0')->where('candidate_id', Auth::id())->find($data['nomination_id']);
    $object = NominationApplicationModel::where('candidate_id', Auth::id())->find($data['nomination_id']);
    $object->recognized_party = $data['recognized_party']; 
    $object->legislative_assembly = $data['legislative_assembly']; 
    $object->name =  $xss->clean_input($data['name']); 
    $object->father_name =  $xss->clean_input($data['father_name']); 
    $object->address =  $xss->clean_input($data['address']); 
    $object->serial_no =  $xss->clean_input($data['serial_no']); 
    $object->part_no =  $xss->clean_input($data['part_no']); 
    $object->resident_ac_no = $data['resident_ac_no']; 
    $object->epic_no_proposer_serch = $data['epic_no_proposer_serch']; 
    $object->proposer_name =  $xss->clean_input($data['proposer_name']); 
    $object->proposer_serial_no =  $xss->clean_input($data['proposer_serial_no']); 
    $object->proposer_part_no =  $xss->clean_input($data['proposer_part_no']); 
    $object->proposer_assembly = $data['proposer_assembly']; 
    $object->apply_date = date('Y-m-d', strtotime($data['apply_date'])); 
	$object->updated_at = date('Y-m-d h:i:s'); 
	$object->step = 2;         
    $object->image      = $data['image_name']; 
    return $object->save();
  }

  public static function add_nomination_part2($data = array()){
  $xss = new xssClean; 
    //dd($data,"dsds");
    //$object = NominationApplicationModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
    $object = NominationApplicationModel::where('candidate_id' , Auth::id())->find($data['nomination_id']);
	//echo "<pre>"; print_r($object ); die; unrecognized_type
    $object->recognized_party = $data['recognized_party']; 
    $object->legislative_assembly = $data['legislative_assembly']; 
    $object->name =  $xss->clean_input($data['name']); 
    $object->father_name =  $xss->clean_input($data['father_name']); 
    $object->address =  $xss->clean_input($data['address']); 
    $object->serial_no =  $xss->clean_input($data['serial_no']); 
    $object->part_no =  $xss->clean_input($data['part_no']); 
    $object->resident_ac_no =  $xss->clean_input($data['resident_ac_no']); 
    $object->apply_date = date('Y-m-d', strtotime($data['apply_date'])); 
	$object->updated_at = date('Y-m-d h:i:s'); 
	$object->step = 2;         
    $object->image      = $data['image_name']; 
    return $object->save();
  }

  public static function add_nomination_part3($data = array()){  //echo "<pre>"; print_r($data); die;
	
	$un='';
	$rec=''; 
	if(!empty($data['unrecognized_type'])){
	 $un=$data['unrecognized_type'];	
	}
	if(!empty($data['recognized_type'])){
	 $rec=$data['recognized_type'];	
	}
	if(!empty($data['not_applicable'])){
	 $chk=$data['not_applicable'];	
	} else {
	 $chk='off';	
	}
	$prty2=0;
	if(!empty($data['party_id2'])){
	 $prty2=$data['party_id2'];	
	}
	
	//echo $data['party_id']; die;
	
	//echo $chk; die;
	//echo "<br>".$un."<br>";
	//echo "<br>".$rec."<br>";
	//die;
  
   // $object = NominationApplicationModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
    $object = NominationApplicationModel::where('candidate_id' , Auth::id())->find($data['nomination_id']);
    $object->age      = $data['age']; 
    $object->party_id = $data['party_id']; 
    $object->party_id2 = $prty2; 
    $object->suggest_symbol_1 = $data['suggest_symbol_1']; 
    $object->suggest_symbol_2 = $data['suggest_symbol_2']; 
    $object->suggest_symbol_3 = $data['suggest_symbol_3']; 
    $object->language = $data['language']; 
    $object->unrecognized_type = $un; 
    $object->recognized_type = $rec; 
    $object->not_applicable = $chk; 
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
  
   public static function add_nomination_part3a($data = array()){	//echo "<pre>"; print_r(   $data ); die;	
    $xss = new xssClean;
	function checkdata($data){
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
   
    //$object                         = NominationApplicationModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
    $object                         = NominationApplicationModel::where('candidate_id' , Auth::id())->find($data['nomination_id']);
    $object->have_police_case       =  $xss->clean_input(checkdata($data['have_police_case']));
    $object->profit_under_govt      =  $xss->clean_input(checkdata($data['profit_under_govt']));
    $object->office_held            =  ($data['office_held'])?$xss->clean_input($data['office_held']):''; 
    $object->court_insolvent        =  $xss->clean_input(checkdata($data['court_insolvent']));
    $object->discharged_insolvency  =  ($data['discharged_insolvency'])?$xss->clean_input($data['discharged_insolvency']):''; 
    $object->allegiance_to_foreign_country  = $xss->clean_input( checkdata($data['allegiance_to_foreign_country'])); 
    $object->country_detail                 = ($data['country_detail'])?$xss->clean_input($data['country_detail']):'';
    $object->disqualified_section8A         = $xss->clean_input(checkdata($data['disqualified_section8A']));
    $object->disqualified_period       = ($data['disqualified_period'])?$xss->clean_input($data['disqualified_period']):''; 
    $object->disloyalty_status         = $xss->clean_input(checkdata($data['disloyalty_status']));
    $object->date_of_dismissal         = ($data['date_of_dismissal'])?date('Y-m-d', strtotime($data['date_of_dismissal'])):''; 
    $object->subsiting_gov_taken       = $xss->clean_input(checkdata($data['subsiting_gov_taken']));
    $object->subsitting_contract       = ($data['subsitting_contract'])?$xss->clean_input($data['subsitting_contract']):''; 
    $object->managing_agent                   = $xss->clean_input(checkdata($data['managing_agent'])); 
	$object->updated_at				   = date('Y-m-d h:i:s'); 
	$object->step 					   = 4;         
    $object->gov_detail                       = ($data['gov_detail'])?$xss->clean_input($data['gov_detail']):''; 
    $object->disqualified_by_comission_10Asec = $xss->clean_input(checkdata($data['disqualified_by_comission_10Asec']));
    $object->date_of_disqualification         = ($data['date_of_disqualification'])?date('Y-m-d', strtotime($data['date_of_disqualification'])):'';  
    $object->date_of_disloyal                 = ($data['date_of_disloyal'])?date('Y-m-d', strtotime($data['date_of_disloyal'])):'';  
   
    $object->save();
	return $object['id'];
  }
  
 
  public static function add_affidavit($data = array()){
    //$object = NominationApplicationModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
    $object = NominationApplicationModel::where('candidate_id' , Auth::id())->find($data['nomination_id']);
    $object->affidavit = $data['affidavit_name'];
	$object->updated_at	= date('Y-m-d h:i:s'); 
	$object->step 	    = 5;         
    $object->save();
    return $object['id'];
  }
  
   public static function add_Signed_affidavit($data = array()){ 
	$object = NominationApplicationModel::where('candidate_id' , Auth::id())->find($data['nomination_id']);   
    $object->is_apply_prescrutiny = 1;
    $object->prescrutiny_apply_datetime = date('Y-m-d h:i:s');
    $object->application_path = $data['application_path'];
    $object->application_type = $data['application_type'];
	$object->updated_at	= date('Y-m-d h:i:s'); 
	$object->save();
    return $object['id'];
  }

  public static function get_nomination($id){
    $data = [];
    $object = NominationApplicationModel::join('profile','profile.candidate_id','=','nomination_application.candidate_id')->where('nomination_application.id',$id)->select('nomination_application.*','nomination_application.id as nomination_id')->first();
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
  
  public static function add_qrcode($data){
    $object = NominationApplicationModel::where('candidate_id' , Auth::id())->find($data['nomination_id']);
    $object->qrcode = $data['qrcode_path'];
    return $object->save();
  }

  public static function finalize_nomination($id){

    $object = NominationApplicationModel::where('candidate_id' , Auth::id())->find($id);
    $object->finalize = 1;
    $object->is_apply_prescrutiny = 1;
    $object->prescrutiny_status = 1;
    $object->application_type = 2;
	$object->updated_at	= date('Y-m-d h:i:s');
	$object->is_apply_prescrutiny =1;
	$object->prescrutiny_apply_datetime=date('Y-m-d H:i:s', time());
    return $object->save();

    // $candidate_detail = ProfileModel::get_candidate_profile();
    // $nomination_details = NominationApplicationModel::get_nomination_application($id);
    // dd($nomination_details);
    // $propoer_results = NominationProposerModel::get_proposers($id);
    // $police_cases = NominationPoliceCaseModel::get_police_cases($id);

    // if($nomination_details['recognized_party'] == ''){
    //   $party_symbol = 'N';
    // }else{
    //   $party_detail = PartyModel::get_party($nomination_details['party_id'], $nomination_details);
    //   dd($party_detail);
    //   $party_symbol = 'S';
    // }

    // $candidate_id = DB::table("candidate_personal_detail")->insertGetId([
    //   "cand_name" => $candidate_detail['name'], 
    //   "cand_hname" => $candidate_detail['name_hindi'], 
    //   "cand_vname" => $candidate_detail['vernacular_name'], 
    //   "cand_alias_name" => $candidate_detail['alias_name'], 
    //   "cand_alias_hname" => $candidate_detail['alias_name_hindi'], 
    //   "candidate_father_name" => $candidate_detail['father_name'], 
    //   "cand_fhname" => $candidate_detail['father_name_hindi'], 
    //   "cand_email" => $candidate_detail['email'], 
    //   "cand_mobile" => $candidate_detail['mobile'], 
    //   "cand_gender" => $candidate_detail['gender'], 
    //   "candidate_residence_address" => $candidate_detail['address_1'], 
    //   "candidate_residence_addressh" => $candidate_detail['address_1_hindi'], 
    //   "candidate_temporary_address" => $candidate_detail['address_2'], 
    //   "candidate_residence_districtno" => $candidate_detail['district'], 
    //   "candidate_residence_pincode" => '', 
    //   "candidate_residence_acno" => $candidate_detail['ac'], 
    //   "candidate_residance_part_no" => $candidate_detail['part_no'], 
    //   "candidate_native_address" => $candidate_detail['address_1'], 
    //   "candidate_native_districtno" => $candidate_detail['district'], 
    //   "candidate_native_pcno" => '', 
    //   "candidate_native_acno" => $candidate_detail['ac'], 
    //   "candidate_native_part_no" => $candidate_detail['part_no'], 
    //   "candidate_residence_stcode" => $candidate_detail['state'], 
    //   "candidate_temporary_stcode" => $candidate_detail['state'], 
    //   "cand_qualification" => '', 
    //   "cand_age" => date('Y') - date('Y', strtotime($candidate_detail['dob'])), 
    //   "cand_category" => $candidate_detail['category'], 
    //   "cand_cast" => '', 
    //   "cand_cast_state" => $candidate_detail['state'],
    //   "cand_panno" => $candidate_detail['pan_number'], 
    //   "cand_nickname" => $candidate_detail['name'], 
    //   "cand_epic_no" => $candidate_detail['epic_no'], 
    //   "cand_dob" => $candidate_detail['dob'],
    //   "cand_image"  => $nomination_details['image']
    // ]);


    // DB::table("candidate_nomination_detail")->insert([
    //   'candidate_id' => $candidate_id, 
    //   'qrcode' => $nomination_details[''], 
    //   'party_id' => $nomination_details['party_id'], 
    //   'symbol_id' => 0, 
    //   'election_id' => $nomination_details['election_id'], 
    //   'date_of_submit' => $nomination_details['apply_date'], 
    //   'ac_no' => $nomination_details['ac_no'], 
    //   'pc_no' => 0, 
    //   'st_code' => $nomination_details['st_code'], 
    //   'district_no' => $candidate_detail['district'], 
    //   'cand_sl_no' => 0, 
    //   'transactiontime' => date('Y-m-d H:i:s'), 
    //   'nomination_papersrno' => 0,
    //   //extra field 'party_type' => '',
    //   'cand_party_type' => $party_type, 
    //   'sug_symbol1' => $nomination_details['suggest_symbol_1'], 
    //   'sug_symbol2' => $nomination_details['suggest_symbol_2'], 
    //   'sug_symbol3' => $nomination_details['suggest_symbol_3'], 
    //   'affidavit_public' => $nomination_details['affidavit'], 
    //   'finalize_by_candidate' => 1,
    //   'proposer_name' => $nomination_details['proposer_name'], 
    //   'proposer_slno' => $nomination_details['proposer_serial_no'], 
    //   'proposer_partno' => $nomination_details['proposer_part_no'], 
    //   'proposer_stcode' => $nomination_details['st_code'], 
    //   'proposer_acno' => $nomination_details['proposer_assembly'], 
    //   'proposer_pcno' => 0, 
    //   'finalize' => 1, 
    //   'profit_under_govt' => $nomination_details['profit_under_govt'], 
    //   'office_held' => $nomination_details['office_held'], 
    //   'court_insolvent' => $nomination_details['court_insolvent'], 
    //   'discharged_insolvency' => $nomination_details['discharged_insolvency'], 
    //   'allegiance_to_foreign_country' => $nomination_details['allegiance_to_foreign_country'], 
    //   'country_details' => $nomination_details['country_detail'], 
    //   'disqualified_section8A' => $nomination_details['disqualified_section8A'], 
    //   'disqualified_period' => $nomination_details['disqualified_period'], 
    //   'disloyalty_status' => $nomination_details['disloyalty_status'], 
    //   'date_of_dismissal' => $nomination_details['date_of_dismissal'], 
    //   'subsiting_gov_taken' => $nomination_details['subsiting_gov_taken'], 
    //   'subsitting_contract' => $nomination_details['subsitting_contract'], 
    //   'managing_agent' => $nomination_details['managing_agent'], 
    //   'gov_details' => $nomination_details['gov_detail'], 
    //   'disqualified_by_comission_10Asec' => $nomination_details['disqualified_by_comission_10Asec'], 
    //   'date_of_disqualification'  => $nomination_details['date_of_disqualification'],  
    //   'election_type_id'          => $nomination_details['gov_detail'], 
    //   'state_phase_no'            => $nomination_details['gov_detail'], 
    //   'm_election_detail_ccode'   => $nomination_details['gov_detail'], 
    //   'apply_online'              => 1
    // ]);

  }

  public static function get_nominations(){
    $result =  NominationApplicationModel::join('m_election_details as election',[
      ['election.ST_CODE','=','nomination_application.st_code']
    ])->join('m_state','m_state.ST_CODE','=','nomination_application.st_code')->join('m_ac',[
      ['m_ac.ST_CODE','=','nomination_application.st_code'],
      ['m_ac.AC_NO','=','nomination_application.ac_no'],
    ])->where('candidate_id' , Auth::id())->where('election.election_status','1')->selectRaw("nomination_application.*, CONCAT(election.ELECTION_TYPE,'-',election.YEAR) as election_name, m_state.ST_NAME as st_name, m_ac.AC_NAME as ac_name, finalize, nomination_no")->groupBy('nomination_application.id')->orderBy('id','desc')->get()->toArray();
    return $result;
  }
   public static function get_nominations_cust($stt, $acc){
    $result =  NominationApplicationModel::join('m_election_details as election',[
      ['election.ST_CODE','=','nomination_application.st_code']
    ])->join('m_state','m_state.ST_CODE','=','nomination_application.st_code')->join('m_ac',[
      ['m_ac.ST_CODE','=','nomination_application.st_code'],
      ['m_ac.AC_NO','=','nomination_application.ac_no'],
    ])
	->where('candidate_id' , Auth::id())
	->where('nomination_application.st_code', $stt)
	->where('nomination_application.ac_no', $acc)
	->where('election.election_status','1')
	->selectRaw("nomination_application.*, CONCAT(election.ELECTION_TYPE,'-',election.YEAR) as election_name, m_state.ST_NAME as st_name, m_ac.AC_NAME as ac_name, finalize, nomination_no")
	->groupBy('nomination_application.id')
	->orderBy('id','desc')
	->get()
	->toArray();
    return $result;
  }

  ################################# Same Modals For RO #########################

  public static function add_nomination_part3_ro($data = array()){  //echo "<pre>"; print_r($data); die;
	
    $un='';
    $rec=''; 
    if(!empty($data['unrecognized_type'])){
     $un=$data['unrecognized_type'];	
    }
    if(!empty($data['recognized_type'])){
     $rec=$data['recognized_type'];	
    }
    if(!empty($data['not_applicable'])){
     $chk=$data['not_applicable'];	
    } else {
     $chk='off';	
    }
    $prty2=0;
    if(!empty($data['party_id2'])){
     $prty2=$data['party_id2'];	
    }
    
    //echo $data['party_id']; die;
    
    //echo $chk; die;
    //echo "<br>".$un."<br>";
    //echo "<br>".$rec."<br>";
    //die;
    
     // $object = NominationApplicationModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
      $object = NominationApplicationModel::find($data['nomination_id']);
      $object->age      = $data['age']; 
      $object->party_id = $data['party_id']; 
      $object->party_id2 = $prty2; 
      $object->suggest_symbol_1 = $data['suggest_symbol_1']; 
      $object->suggest_symbol_2 = $data['suggest_symbol_2']; 
      $object->suggest_symbol_3 = $data['suggest_symbol_3']; 
      $object->language = $data['language']; 
      $object->unrecognized_type = $un; 
      $object->recognized_type = $rec; 
      $object->not_applicable = $chk; 
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

     public static function add_nomination_part3a_ro($data = array()){	//echo "<pre>"; print_r(   $data ); die;	
   
      function checkdata($data){
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
       
        //$object                         = NominationApplicationModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
        $object                         = NominationApplicationModel::where('candidate_id' , $data['candidate_id'])->find($data['nomination_id']);
        $object->have_police_case       =  checkdata($data['have_police_case']);
      $object->profit_under_govt      =  checkdata($data['profit_under_govt']);
      $object->office_held            =  ($data['office_held'])?$data['office_held']:''; 
      $object->court_insolvent        =  checkdata($data['court_insolvent']);
      $object->discharged_insolvency  =  ($data['discharged_insolvency'])?$data['discharged_insolvency']:''; 
      $object->allegiance_to_foreign_country  =  checkdata($data['allegiance_to_foreign_country']); 
      $object->country_detail                 = ($data['country_detail'])?$data['country_detail']:'';
      $object->disqualified_section8A         = checkdata($data['disqualified_section8A']);
      $object->disqualified_period       = ($data['disqualified_period'])?$data['disqualified_period']:''; 
      $object->disloyalty_status         = checkdata($data['disloyalty_status']);
      $object->date_of_dismissal         = ($data['date_of_dismissal'])?date('Y-m-d', strtotime($data['date_of_dismissal'])):''; 
      $object->subsiting_gov_taken       = checkdata($data['subsiting_gov_taken']);
      $object->subsitting_contract       = ($data['subsitting_contract'])?$data['subsitting_contract']:''; 
      $object->managing_agent                   = checkdata($data['managing_agent']); 
    $object->updated_at				   = date('Y-m-d h:i:s'); 
    $object->step 					   = 4;         
      $object->gov_detail                       = ($data['gov_detail'])?$data['gov_detail']:''; 
      $object->disqualified_by_comission_10Asec = checkdata($data['disqualified_by_comission_10Asec']);
      $object->date_of_disqualification         = ($data['date_of_disqualification'])?date('Y-m-d', strtotime($data['date_of_disqualification'])):'';  
      $object->date_of_disloyal                 = ($data['date_of_disloyal'])?date('Y-m-d', strtotime($data['date_of_disloyal'])):'';  
      $object->save();
    return $object['id'];
      }

      public static function get_nomination_application_ro($id){ 
        $object = NominationApplicationModel::find($id);
        if(!$object){
          return false;
        }
        return $object->toArray();
      }
    
      public static function add_nomination_application_ro($data = array()){

      if(!empty($data['nomination_id']) && isset($data['nomination_id'])){
          $object = NominationApplicationModel::where('candidate_id', $data['candidate_id'])->find(($data['nomination_id']));
          $object->st_code = $data['st_code']; 
          $object->ac_no = $data['ac_no']; 
          $object->updated_at = date('Y-m-d h:i:s'); 
          $object->step = 1; 
          $object->election_id = $data['eid']; 
          $object->election_type_id = $data['election_id'];
      }else{
          $object = new NominationApplicationModel();
          $object->candidate_id = $data['candidate_id'];
          $object->st_code = $data['st_code']; 
          $object->ac_no = $data['ac_no']; 
      $object->election_id = $data['eid']; 
          $object->election_type_id = $data['election_id']; 
          $object->updated_at = date('Y-m-d h:i:s'); 
          $object->step = 1; 
          $object->nomination_no = strtoupper("NOM-".time());
    }
    //echo Auth::id(); die;
     // $candidate = ProfileModel::find(Auth::id());	
    $candidate = ProfileModel::where('candidate_id', $data['candidate_id'])->first();	
    //echo "<pre>"; print_r(   $candidate ); die;	
      if($candidate){
      
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
      $object->nomination_type = 1; 
          $object->updated_at = date('Y-m-d h:i:s'); 
          $object->gender = $candidate['gender'];
      
    }
    
      if(!$object->save()){
          return false;
      }
    
      \Session::put('nomination_id', $object->id);
      return true;
    }

    public static function add_nomination_part1_ro($data = array()){    
      //$object = NominationApplicationModel::where('finalize','0')->where('candidate_id', Auth::id())->find($data['nomination_id']);
      $object = NominationApplicationModel::where('candidate_id', $data['candidate_id'])->find($data['nomination_id']);
      $object->recognized_party = $data['recognized_party']; 
      $object->legislative_assembly = $data['legislative_assembly']; 
      $object->name = $data['name']; 
      $object->father_name = $data['father_name']; 
      $object->address = $data['address']; 
      $object->serial_no = $data['serial_no']; 
      $object->part_no = $data['part_no']; 
      $object->resident_ac_no = $data['resident_ac_no']; 
      $object->epic_no_proposer_serch = $data['epic_no_proposer_serch']; 
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

    public static function add_nomination_part2_ro($data = array()){ 
      //$object = NominationApplicationModel::where('finalize','0')->where('candidate_id' , Auth::id())->find($data['nomination_id']);
      $object = NominationApplicationModel::where('candidate_id' , $data['candidate_id'])->find($data['nomination_id']);
    //echo "<pre>"; print_r($object ); die; unrecognized_type
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

  ##################################### End Modals #############################
}