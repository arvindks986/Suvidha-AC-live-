<?php namespace App\models\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;
use App\models\Nomination\{NominationProposerModel, ProfileModel, NominationProposerLogModel}; 

class NominationPoliceCaselogModel extends Model
{

  protected $table = 'nomination_police_case_logs';
  public $fillable = ['nom_police_case_id', 'candidate_id', 'nomination_id', 'added_create_at', 'created_at', 'added_update_at', 'updated_at', 'created_by', 'updated_by', 'case_no', 'police_station', 'st_code', 'case_dist_no', 'convicted_des', 'date_of_conviction', 'court_name', 'punishment_imposed', 'date_of_release', 'revision_against_conviction', 'revision_appeal_date', 'rev_court_name', 'status', 'revision_disposal_date', 'revision_order_description', 'log_added_updated_at', 'log_updated_at'];
  
  public static function add_police_case($data = array(), $nomination_id){ //echo "<pre>"; print_r($data); die;
    $object = new NominationPoliceCaseModel();
    $object->candidate_id   = \Auth::id(); 
    $object->nomination_id  = $nomination_id; 
    $object->case_no        = $data['case_no']; 
    $object->police_station = $data['police_station']; 
    $object->st_code          = $data['st_code']; 
    $object->case_dist_no       = $data['district'];  
    $object->convicted_des      = $data['convicted_des'];  
    $object->date_of_conviction =  date('Y-m-d', strtotime($data['date_of_conviction']));
    $object->court_name         = $data['court_name'];
    $object->punishment_imposed           = $data['punishment_imposed'];
    $object->date_of_release              = date('Y-m-d', strtotime($data['date_of_release'])); 
    $object->revision_against_conviction  = $data['revision_against_conviction'];
    $object->revision_appeal_date         = date('Y-m-d', strtotime($data['revision_appeal_date']));
    $object->rev_court_name               = $data['rev_court_name'];
    $object->status                       = $data['status'];
    $object->revision_disposal_date       = date('Y-m-d', strtotime($data['revision_disposal_date']));
    $object->revision_order_description   = $data['revision_order_description'];
    return $object->save();
  }
  
  public static function delete_police_case($nomination_id){
    NominationPoliceCaseModel::where([
      'candidate_id' => \Auth::id(),
      'nomination_id' => $nomination_id
    ])->delete();
  }
   public static function add_delete_police_case($nomination_id){
    $object = NominationPoliceCaseModel::where('nomination_id', $nomination_id)->get();
	foreach( $object as $ddta) {
		 $staff = $ddta->replicate();
		 $staff = $ddta->toArray();
		 NominationProposerLogModel::firstOrCreate($staff);
	}
  }

  public static function get_police_cases($nomination_id){
    return NominationPoliceCaseModel::where([
      'candidate_id' => \Auth::id(),
      'nomination_id' => $nomination_id
    ])->get()->toArray();
  }

  ################################## RO Modal For the same ################################

  public static function add_police_case_ro($data = array(), $nomination_id){ //echo "<pre>"; print_r($data); die;
    $object = new NominationPoliceCaseModel();
    $object->nomination_id  = $nomination_id; 
    $object->case_no        = $data['case_no']; 
    $object->police_station = $data['police_station']; 
    $object->st_code          = $data['st_code']; 
    $object->case_dist_no       = $data['district'];  
    $object->convicted_des      = $data['convicted_des'];  
    $object->date_of_conviction =  date('Y-m-d', strtotime($data['date_of_conviction']));
    $object->court_name         = $data['court_name'];
    $object->punishment_imposed           = $data['punishment_imposed'];
    $object->date_of_release              = date('Y-m-d', strtotime($data['date_of_release'])); 
    $object->revision_against_conviction  = $data['revision_against_conviction'];
    $object->revision_appeal_date         = date('Y-m-d', strtotime($data['revision_appeal_date']));
    $object->rev_court_name               = $data['rev_court_name'];
    $object->status                       = $data['status'];
    $object->revision_disposal_date       = date('Y-m-d', strtotime($data['revision_disposal_date']));
    $object->revision_order_description   = $data['revision_order_description'];
    return $object->save();
  }
  
  public static function delete_police_case_ro($nomination_id){
    NominationPoliceCaseModel::where([
      'nomination_id' => $nomination_id
    ])->delete();
  }
   public static function add_delete_police_case_ro($nomination_id){
    $object = NominationPoliceCaseModel::where('nomination_id', $nomination_id)->get();
	foreach( $object as $ddta) {
		 $staff = $ddta->replicate();
		 $staff = $ddta->toArray();
		 NominationProposerLogModel::firstOrCreate($staff);
	}
  }

  public static function get_police_cases_ro($nomination_id){
    return NominationPoliceCaseModel::where([
      'nomination_id' => $nomination_id
    ])->get()->toArray();
  }

  #################################### End RO Modal #######################################

}