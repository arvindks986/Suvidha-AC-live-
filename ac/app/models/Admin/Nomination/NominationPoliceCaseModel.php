<?php namespace App\models\Admin\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth, Session;
use App\models\Admin\Nomination\NominationApplicationModel;
class NominationPoliceCaseModel extends Model
{
   

  protected $table = 'nomination_police_case';
  public $fillable = ['candidate_id', 'nomination_id','st_code','ac_no','pc_no','election_id', 'added_create_at', 'created_at', 'added_update_at', 'updated_at', 'created_by', 'updated_by', 'case_no', 'police_station', 'case_st_code', 'case_dist_no', 'convicted_des', 'date_of_conviction', 'court_name', 'punishment_imposed', 'date_of_release', 'revision_against_conviction', 'revision_appeal_date', 'rev_court_name', 'status', 'revision_disposal_date', 'revision_order_description'];
  public static function add_police_case($data = array(), $nomination_id){
      //dd($data);
     $nom = NominationApplicationModel::get_nomination_application($nomination_id);
     $object = NominationPoliceCaseModel::where('case_no',$data['case_no'])->where('candidate_id',$nom['candidate_id'])->where('nomination_id',$nomination_id)->first();
    if(isset($object ) and ($object )){  
        $object->case_no        = $data['case_no']; 
        $object->police_station = $data['police_station']; 
        $object->case_st_code          = $data['case_st_code']; 
        $object->case_dist_no       = $data['case_dist_no']; 
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
        $object->st_code   = $data['st_code'];
        $object->ac_no   = $data['ac_no'];
        //$object->pc_no   = $data['pc_no'];
        $object->election_id   = $data['election_id'];
        $object->added_update_at = date('Y-m-d H:i:s');
        $object->updated_at =date('Y-m-d');
        $object->updated_by =\Auth::user()->officername;

      }
      else{
    
          $object = new NominationPoliceCaseModel();
          $object->candidate_id   = $nom['candidate_id']; 
          $object->nomination_id  = $nomination_id; 
          $object->case_no        = $data['case_no']; 
          $object->police_station = $data['police_station']; 
          $object->case_st_code          = $data['case_st_code']; 
        $object->case_dist_no       = $data['case_dist_no']; 
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
          $object->st_code   = $data['st_code'];
          $object->ac_no   = $data['ac_no'];
          //$object->pc_no   = $data['pc_no'];
          $object->election_id   = $data['election_id'];
          $object->created_at = date('Y-m-d H:i:s');
          $object->added_create_at =date('Y-m-d');
          $object->created_by =\Auth::user()->officername;
        }
        
        return $object->save();
  }

  public static function delete_police_case($nomination_id){
    NominationPoliceCaseModel::where([
      'candidate_id' => \Session::get('auth_id'),
      'nomination_id' => $nomination_id
    ])->delete();
  }


  public static function get_police_cases($nomination_id){
    return NominationPoliceCaseModel::where([
      //'candidate_id' => \Session::get('auth_id'),
      'nomination_id' => $nomination_id
    ])->get()->toArray();
  }

}