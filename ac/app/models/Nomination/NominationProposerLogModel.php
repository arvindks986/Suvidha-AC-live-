<?php namespace App\models\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;
use App\models\Nomination\{NominationProposerModel,NominationPoliceCaseModel, ProfileModel};  

class NominationProposerLogModel extends Model
{

  protected $table = 'nomination_application_proposer_logs';

  public $fillable = ['id','nom_app_proposer_id','candidate_id', 'nomination_id', 'log_added_updated_at', 'log_updated_at', 's_no', 'serial_no', 'part_no', 'fullname', 'date'];

  public static function add_proposer($data = array()){

    $object = new NominationProposerLogModel();
    $object->candidate_id = \Auth::id(); 
    $object->nomination_id = $data['nomination_id']; 
    $object->s_no = ($data['s_no'])?$data['s_no']:''; 
    $object->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
    $object->part_no = ($data['part_no'])?$data['part_no']:''; 
    $object->fullname = ($data['fullname'])?$data['fullname']:'';  
    $object->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
    $object->signature = ($data['signature'])?$data['signature']:''; 
    return $object->save();
  }

  public static function get_proposers($id){
    $data = [];
    $results = NominationProposerLogModel::where([
      'candidate_id' => \Auth::id(),
      'nomination_id' => $id
    ])->get()->toArray();
    return $results;
  }

  public static function delete_proposer($nomination_id){
    NominationProposerLogModel::where([
      'candidate_id' => \Auth::id(),
      'nomination_id' => $nomination_id
    ])->delete();
  }

  ###################################### RO Modal For the Same #################################

  public static function add_proposer_ro($data = array()){

    $object = new NominationProposerLogModel();
    $object->candidate_id = $data['nomination_id']; 
    $object->nomination_id = $data['nomination_id']; 
    $object->s_no = ($data['s_no'])?$data['s_no']:''; 
    $object->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
    $object->part_no = ($data['part_no'])?$data['part_no']:''; 
    $object->fullname = ($data['fullname'])?$data['fullname']:'';  
    $object->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
    $object->signature = ($data['signature'])?$data['signature']:''; 
    return $object->save();
  }

  public static function get_proposers_ro($id){
    $data = [];
    $results = NominationProposerLogModel::where([
      'nomination_id' => $id
    ])->get()->toArray();
    return $results;
  }

  public static function delete_proposer_ro($nomination_id){
    NominationProposerLogModel::where([
      'nomination_id' => $nomination_id
    ])->delete();
  }

  ################################## End RO Modal ##############################################
  
}