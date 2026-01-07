<?php namespace App\models\Admin\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth, Session;

class ProfileModel extends Model
{

  protected $table = 'profile';

  
  public $fillable = ['id', 'candidate_id','name','hname','vname','alias_name','alias_hname','alias_vname','father_name','father_hname',
  'father_vname','category','email','mobile','pan_number','dob','age','address','haddress','vaddress','epic_no','part_no','serial_no','state',
  'district','ac','gender','added_create_at','created_at','added_update_at','updated_at','created_by','updated_by'];


  public static function get_candidate_profile(){
    $object = ProfileModel::where('candidate_id', Session::get('auth_id'))->first();
    if(!$object){
      return false;
    }
    return $object->toArray();

	}


  public static function get_cand_id_by_mobile($mobile){
    $object = ProfileModel::where('mobile', $mobile)->first();
    if(!$object){
      return false;
    }
    return $object->toArray();
  }
   public static function get_cand_id_by_candidate($candidate_id){
    $object = ProfileModel::where('candidate_id', $candidate_id)->first();
    if(!$object){
      return false;
    }
    return $object->toArray();
  }

  public static function add_nomination_personal_detail($data = array()){  //dd(Auth::user());
    if(Session::get('otp_mobile')!='')
      $object = ProfileModel::firstorNew(['mobile' => Session::get('otp_mobile')]);
    else 
      $object = ProfileModel::firstorNew(['mobile' =>$data['mobile']]);
    //dd($object);
    if($data['candidate_id']!=0)
                  $object->candidate_id  = $data['candidate_id'];
      $object->name = $data['name'];
      $object->email = $data['email'];
      $object->mobile = $data['mobile']; 
      $object->hname = $data['hname']; 
      $object->vname = $data['vname']; 
      $object->alias_name = $data['alias_name']; 
      $object->alias_hname = $data['alias_hname']; 
      $object->alias_vname = $data['alias_vname']; 
      $object->father_name = $data['father_name']; 
      $object->father_hname = $data['father_hname']; 
      $object->father_vname = $data['father_vname']; 
      $object->category = $data['category']; 
      $object->pan_number = $data['pan_number'];
      if(!empty($data['dob'])){ 
        $object->dob = date('Y-m-d', strtotime($data['dob'])); 
      }
      $object->age = ($data['age'])?(int)$data['age']:0; 
      $object->address = $data['address']."jkkjk"; 
      $object->haddress      = $data['haddress']; 
      $object->vaddress = $data['vaddress']; 
      $object->epic_no = $data['epic_no']; 
      $object->part_no = $data['part_no']; 
      $object->serial_no = $data['serial_no']; 
      $object->state = $data['state']; 
      $object->district = $data['district']; 
      $object->ac = $data['ac']; 
      $object->gender = $data['gender'];
      $object->created_at = date('Y-m-d H:i:s');
      $object->added_create_at =date('Y-m-d');
      $object->created_by =Auth::user()->officername;
       //dd($object);
      return $object->save();
  }
 
 }