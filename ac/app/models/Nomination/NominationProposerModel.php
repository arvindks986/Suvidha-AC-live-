<?php namespace App\models\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;
use App\models\Nomination\{NominationProposerModel,NominationPoliceCaseModel, ProfileModel, NominationProposerLogModel}; 

class NominationProposerModel extends Model
{

  protected $table = 'nomination_application_proposer';

  public $fillable = ['id','candidate_id', 'nomination_id', 's_no', 'serial_no', 'part_no', 'fullname', 'date', 'epic_no_proposer_serch_part_2', 'status'];

  public static function add_proposer($data = array()){

    $object = new NominationProposerModel();
    $object->candidate_id = \Auth::id(); 
    $object->nomination_id = $data['nomination_id']; 
    $object->status = 1; 
    $object->epic_no_proposer_serch_part_2 = $data['epic_no_proposer_serch_part_2']; 
    $object->s_no = ($data['s_no'])?$data['s_no']:''; 
    $object->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
    $object->part_no = ($data['part_no'])?$data['part_no']:''; 
    $object->fullname = ($data['fullname'])?$data['fullname']:'';  
    $object->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
    $object->signature = ($data['signature'])?$data['signature']:''; 
	
    $object->save();
	
	/*$obj = new NominationProposerLogModel();
    $obj->candidate_id = \Auth::id(); 
    $obj->nom_app_proposer_id = $object['id']; 
    $obj->nomination_id = $data['nomination_id']; 
    $obj->s_no = ($data['s_no'])?$data['s_no']:''; 
    $obj->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
    $obj->part_no = ($data['part_no'])?$data['part_no']:''; 
    $obj->fullname = ($data['fullname'])?$data['fullname']:'';  
    $obj->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
    $obj->signature = ($data['signature'])?$data['signature']:''; 
	$obj->log_added_updated_at	= date('Y-m-d');
	$obj->log_updated_at	= date('Y-m-d h:i:s');		
	$obj->save();
	*/
	return true;
  }

  public static function get_proposers($id){
    $data = [];
    $results = NominationProposerModel::where([
      'candidate_id' => \Auth::id(),
      'nomination_id' => $id,
      'status' => 1
    ])
	->orderBy('id', 'DESC')
	->limit(10)
	->get()
	->toArray();
    return $results;
  }

  public static function delete_proposer($nomination_id){
    NominationProposerModel::where([
      'candidate_id' => \Auth::id(),
      'nomination_id' => $nomination_id
    ])->update([
		"status" => 2,
	]);	
  } 
  public static function add_delete_proposer($nomination_id){
    $object = NominationProposerModel::where('nomination_id', $nomination_id)->get();
	foreach( $object as $ddta) {
		
		 $staff = $ddta->replicate();
		 $staff = $ddta->toArray();
		 unset($staff['id']);
		 // echo ""; print_r($staff); die;
		 $staff['nom_app_proposer_id'] = $ddta['id'];
		 $staff['log_added_updated_at'] = date('Y-m-d');
		 $staff['log_updated_at'] = date('Y-m-d h:i:s');	
		 NominationProposerLogModel::firstOrCreate($staff);
	}
  }	
  
  ############################# RO Modal for the same ############################

  public static function add_proposer_ro($data = array()){

    $object = new NominationProposerModel();
    $object->candidate_id = $data['candidate_id'];
    $object->nomination_id = $data['nomination_id']; 
    $object->status = 1; 
    $object->epic_no_proposer_serch_part_2 = $data['epic_no_proposer_serch_part_2']; 
    $object->s_no = ($data['s_no'])?$data['s_no']:''; 
    $object->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
    $object->part_no = ($data['part_no'])?$data['part_no']:''; 
    $object->fullname = ($data['fullname'])?$data['fullname']:'';  
    $object->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
    $object->signature = ($data['signature'])?$data['signature']:''; 
	
    $object->save();
	
	/*$obj = new NominationProposerLogModel();
    $obj->candidate_id = \Auth::id(); 
    $obj->nom_app_proposer_id = $object['id']; 
    $obj->nomination_id = $data['nomination_id']; 
    $obj->s_no = ($data['s_no'])?$data['s_no']:''; 
    $obj->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
    $obj->part_no = ($data['part_no'])?$data['part_no']:''; 
    $obj->fullname = ($data['fullname'])?$data['fullname']:'';  
    $obj->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
    $obj->signature = ($data['signature'])?$data['signature']:''; 
	$obj->log_added_updated_at	= date('Y-m-d');
	$obj->log_updated_at	= date('Y-m-d h:i:s');		
	$obj->save();
	*/
	return true;
  }

  public static function get_proposers_ro($id){
    $data = [];
    $results = NominationProposerModel::where([
        'nomination_id' => $id,
        'status' => 1
      ])
    ->orderBy('id', 'ASC')
    ->limit(10)
    ->get()
    ->toArray();
      return $results;
  }

  public static function delete_proposer_ro($nomination_id){
    NominationProposerModel::where([
      'nomination_id' => $nomination_id
    ])->update([
		"status" => 2,
	]);	
  } 
  public static function add_delete_proposer_ro($nomination_id){
    $object = NominationProposerModel::where('nomination_id', $nomination_id)->get();
	foreach( $object as $ddta) {
		 $staff = $ddta->replicate();
		 $staff = $ddta->toArray();
		 unset($staff['id']);
		 // echo ""; print_r($staff); die;
		 $staff['nom_app_proposer_id'] = $ddta['id'];
		 $staff['log_added_updated_at'] = date('Y-m-d');
		 $staff['log_updated_at'] = date('Y-m-d h:i:s');	
		 NominationProposerLogModel::firstOrCreate($staff);
	}
	}	

  ################################ End Ro Modal For the Same #####################
}