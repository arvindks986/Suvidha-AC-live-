<?php namespace App\models\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class NominationModel extends Model
{

  protected $table = 'nomination_personal_detail';

  public $fillable = ['name', 'name_hindi', 'vernacular_name', 'alias_name', 'alias_name_hindi', 'father_name', 'father_name_hindi', 'category', 'email', 'mobile', 'pan_number', 'dob', 'address_1', 'address_1_hindi', 'address_2', 'address_2_hindi', 'epic_no', 'part_no', 'serial_no', 'state', 'district', 'ac', 'gender', 'added_create_at', 'created_at', 'added_update_at', 'updated_at', 'created_by', 'updated_by','candidate_id'];

  public static function get_candidate_profile(){

    $object = NominationModel::where('candidate_id', Auth::id())->first();
    if(!$object){
      return false;
    }
    return $object->toArray();

	}


  public static function add_nomination_personal_detail($data = array()){
      $object = NominationModel::firstorNew(['candidate_id' => Auth::id()]);
      $object->candidate_id  = Auth::id();
      $object->name = $data['name'];
      $object->email = $data['email'];
      $object->mobile = $data['mobile']; 
      $object->name_hindi = $data['name_hindi']; 
      $object->vernacular_name = $data['vernacular_name']; 
      $object->alias_name = $data['alias_name']; 
      $object->alias_name_hindi = $data['alias_name_hindi']; 
      $object->father_name = $data['father_name']; 
      $object->father_name_hindi      = $data['father_name_hindi']; 
      $object->father_name_vernacular = $data['father_name_vernacular']; 
      $object->category = $data['category']; 
      $object->pan_number = $data['pan_number'];
      if(!empty($data['dob'])){ 
        $object->dob = date('Y-m-d', strtotime($data['dob'])); 
      }
      $object->age = ($data['age'])?(int)$data['age']:0; 
      $object->address_1 = $data['address_1']; 
      $object->address_1_hindi      = $data['address_1_hindi']; 
      $object->address_1_vernacular = $data['address_1_vernacular']; 
      $object->epic_no = $data['epic_no']; 
      $object->part_no = $data['part_no']; 
      $object->serial_no = $data['serial_no']; 
      $object->state = $data['state']; 
      $object->district = $data['district']; 
      $object->ac = $data['ac']; 
      $object->gender = $data['gender'];
      return $object->save();
  }

  //for NFD
  public static function get_nominations_by_mobile($mobile){
    $result =  NominationModel::join("nomination_personal_detail", "nomination_personal_detail.id", "=", "")->join('m_election_details as election',[
      ['election.ST_CODE','=','nomination_application.st_code']
    ])->join('m_state','m_state.ST_CODE','=','nomination_application.st_code')->join('m_ac',[
      ['m_ac.ST_CODE','=','nomination_application.st_code'],
      ['m_ac.AC_NO','=','nomination_application.ac_no'],
    ])->where('mobile', $mobile)->where('election.election_status','1')->selectRaw("nomination_application.*, CONCAT(election.ELECTION_TYPE,'-',election.YEAR) as election_name, m_state.ST_NAME as st_name, m_ac.AC_NAME as ac_name, finalize")->groupBy('nomination_application.id')->get()->toArray();
    return $result;
  }

}