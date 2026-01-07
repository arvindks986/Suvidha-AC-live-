<?php namespace App\models\Admin\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth, Session;

class NominationProposerModel extends Model
{

  protected $table = 'nomination_application_proposer';

 

  public $fillable = [  'id','candidate_id','nomination_id','st_code','ac_no','pc_no','election_id',
          's_no','serial_no','part_no','fullname','date','signature','added_create_at','created_at',
          'added_update_at','updated_at','created_by','updated_by'];

  public static function add_proposer($data = array()){
         
        $object = NominationProposerModel::where('s_no',$data['s_no'])->where('candidate_id',$data['candidate_id'])->where('nomination_id',$data['nomination_id'])->first();
    
    if(isset($object ) and ($object )){  //dd($object);
     
          $object->s_no = ($data['s_no'])?$data['s_no']:''; 
          $object->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
          $object->part_no = ($data['part_no'])?$data['part_no']:''; 
          $object->fullname = ($data['fullname'])?$data['fullname']:'';  
          $object->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
          $object->signature = ($data['signature'])?$data['signature']:''; 
          $object->updated_at = date('Y-m-d H:i:s');
          $object->added_update_at =date('Y-m-d');
          $object->updated_by =\Auth::user()->officername;
          $object->st_code   = $data['st_code'];
          $object->ac_no   = $data['ac_no'];
          $object->election_id   = $data['election_id'];
         }
    else {
          $object = new NominationProposerModel();
          $object->candidate_id = $data['candidate_id']; 
          $object->nomination_id = $data['nomination_id']; 
          $object->s_no = ($data['s_no'])?$data['s_no']:''; 
          $object->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
          $object->part_no = ($data['part_no'])?$data['part_no']:''; 
          $object->fullname = ($data['fullname'])?$data['fullname']:'';  
          $object->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
          $object->signature = ($data['signature'])?$data['signature']:''; 
          $object->created_at = date('Y-m-d H:i:s');
          $object->added_create_at =date('Y-m-d');
          $object->created_by =\Auth::user()->officername;
          $object->st_code   = $data['st_code'];
          $object->ac_no   = $data['ac_no'];
          $object->election_id   = $data['election_id'];
        }
          $object->save();
  }
public static function add_proposerroac($data = array()){   
    $object = NominationProposerModel::where('s_no',$data['s_no'])->where('candidate_id',$data['candidate_id'])->where('nomination_id',$data['nomination_id'])->first();
    // dd($object);
    //print_r($data); 
    if(isset($object ) and ($object )){  //dd($object);
        $object->s_no = ($data['s_no'])?$data['s_no']:''; 
        $object->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
        $object->part_no = ($data['part_no'])?$data['part_no']:''; 
        $object->fullname = ($data['fullname'])?$data['fullname']:'';  
        $object->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
        $object->signature = ($data['signature'])?$data['signature']:''; 
          $object->updated_at = date('Y-m-d H:i:s');
          $object->added_update_at =date('Y-m-d');
          $object->updated_by =\Auth::user()->officername;
          $object->st_code   = $data['st_code'];
          $object->ac_no   = $data['ac_no'];
          $object->election_id   = $data['election_id'];
         
           return $object->save();
  }
    else {
          $object = new NominationProposerModel();
          $object->candidate_id = $data['candidate_id']; 
          $object->nomination_id = $data['nomination_id']; 
          $object->s_no = ($data['s_no'])?$data['s_no']:''; 
          $object->serial_no = ($data['serial_no'])?$data['serial_no']:''; 
          $object->part_no = ($data['part_no'])?$data['part_no']:''; 
          $object->fullname = ($data['fullname'])?$data['fullname']:'';  
          $object->date =  ($data['date'])?date('Y-m-d', strtotime($data['date'])):'';
          $object->signature = ($data['signature'])?$data['signature']:''; 
          $object->created_at = date('Y-m-d H:i:s');
          $object->added_create_at =date('Y-m-d');
          $object->created_by =\Auth::user()->officername;
          $object->st_code   = $data['st_code'];
          $object->ac_no   = $data['ac_no'];
          $object->election_id   = $data['election_id'];
          return $object->save();
    }
    
  }

  public static function get_proposers($id){
    $data = [];
    $results = NominationProposerModel::where([
      //'candidate_id' => \Session::get('auth_id'),
      'nomination_id' => $id
    ])->get()->toArray();
    return $results;
  }

  public static function delete_proposer($nomination_id){
    NominationProposerModel::where([
      //'candidate_id' => \Session::get('auth_id'),
      'nomination_id' => $nomination_id
    ])->delete();
  }

}