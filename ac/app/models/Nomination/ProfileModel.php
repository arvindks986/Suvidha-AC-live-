<?php namespace App\models\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class ProfileModel extends Model
{

  protected $table = 'profile'; 
  public $fillable = ['name', 'hname', 'vname', 'alias_name', 'alias_hname', 'father_name', 'father_hname', 'father_vname', 'category', 'email', 'mobile', 'pan_number', 'dob', 'address', 'haddress', 'address_2', 'address_2_hindi', 'epic_no', 'part_no', 'serial_no', 'state', 'district', 'ac', 'gender', 'added_create_at', 'created_at', 'added_update_at', 'updated_at', 'created_by', 'updated_by','candidate_id', 'email_otp', 'mobile_otp', 'is_verified_email_otp', 'is_verified_mobile_otp', 'alias_vname'];

    public static function get_candidate_profile(){	  
    $object = ProfileModel::where('candidate_id', Auth::id())->first();
    if(!$object){
      return false;
    }
      return $object->toArray();
	}
	
	public static function email_otp_save($email, $otp){
      $object = ProfileModel::firstorNew(['candidate_id' => Auth::id()]);
      $object->candidate_id  = Auth::id();
      $object->email = $email;
      $object->email_otp = $otp;
	  $object->is_verified_email_otp = null;
      $object->save();
	  return $object['id'];
    }
  
  
    public static function mobile_otp_save($mob, $otp){
      $object = ProfileModel::firstorNew(['candidate_id' => Auth::id()]);
      $object->candidate_id  = Auth::id();
      $object->mobile = $mob;
      $object->mobile_otp = $otp;
      $object->is_verified_mobile_otp = null;
      $object->save();
	  return $object['id'];
    }
  
  public static function add_nomination_personal_detail($data = array()){

	  
      $object = ProfileModel::firstorNew(['candidate_id' => Auth::id()]);
      $object->candidate_id  = Auth::id();
      $object->name = $data['name'];
      $object->email = $data['email'];
      $object->mobile = $data['mobile']; 
      $object->hname = $data['hname']; 
      $object->vname = $data['vname']; 
      $object->alias_name = $data['alias_name']; 
      $object->alias_hname = $data['alias_hname']; 
      $object->father_name = $data['father_name']; 
      $object->father_hname      = $data['father_hname']; 
      $object->father_vname = $data['father_vname']; 
      $object->alias_vname = $data['alias_vname']; 
      $object->category = $data['category']; 
      $object->pan_number = $data['pan_number'];
      if(!empty($data['dob'])){ 
        $object->dob = date('Y-m-d', strtotime($data['dob'])); 
      }
      $object->age = ($data['age'])?(int)$data['age']:0; 
      $object->address = $data['address']; 
      $object->haddress      = $data['haddress']; 
      $object->vaddress = $data['vaddress']; 
      $object->epic_no = $data['epic_no']; 
      $object->part_no = $data['part_no']; 
      $object->serial_no = $data['serial_no']; 
      $object->state = $data['state']; 
      $object->district = $data['district']; 
      $object->ac = $data['ac']; 
      $object->gender = $data['gender'];
      $object->save();
	  return $object['id'];
	  
  }
  
  //for NFD
  public static function get_nominations_by_mobile($mobile){
    $result =  ProfileModel::join("profile", "profile.id", "=", "")->join('m_election_details as election',[
      ['election.ST_CODE','=','nomination_application.st_code']
    ])->join('m_state','m_state.ST_CODE','=','nomination_application.st_code')->join('m_ac',[
      ['m_ac.ST_CODE','=','nomination_application.st_code'],
      ['m_ac.AC_NO','=','nomination_application.ac_no'],
    ])->where('mobile', $mobile)->where('election.election_status','1')->selectRaw("nomination_application.*, CONCAT(election.ELECTION_TYPE,'-',election.YEAR) as election_name, m_state.ST_NAME as st_name, m_ac.AC_NAME as ac_name, finalize")->groupBy('nomination_application.id')->get()->toArray();
    return $result;
  }

  #################### RO Modal ######################

  public static function get_candidate_profile_ro($id){
    $object = ProfileModel::where('candidate_id', $id)->first();
    if(!$object){
      return false;
    }
      return $object->toArray();
	}

  ####################### End RO Modal ###############

}