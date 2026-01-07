<?php namespace App\models\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

use App\models\Nomination\{NominationProposerModel,NominationPoliceCaseModel, ProfileModel}; 

class ProfilelogModel extends Model
{

  protected $table = 'profile_logs'; 
  public $fillable = ['profile_id', 'name', 'hname', 'vname', 'alias_name', 'alias_hname', 'father_name', 'father_hname', 'father_vname', 'category', 'email', 'mobile', 'pan_number', 'dob', 'address', 'haddress', 'address_2', 'address_2_hindi', 'epic_no', 'part_no', 'serial_no', 'state', 'district', 'ac', 'gender', 'added_create_at', 'created_at', 'added_update_at', 'updated_at', 'created_by', 'updated_by','candidate_id', 'log_added_updated_at', 'log_updated_at'];

   


  public static function add_nomination_personal_detail($data = array()){
	 $object = ProfileModel::where('candidate_id', Auth::id())->first();	
	 if(!empty($object)){	
	 $staff = $object->replicate();
	 $staff = $staff->toArray();
	 $staff['profile_id'] =$object['id'];
	 $staff['log_added_updated_at'] = date('Y-m-d');
	 $staff['log_updated_at'] =       date('Y-m-d h:i:s');		
	 ProfilelogModel::firstOrCreate($staff);
	 }
	}
}