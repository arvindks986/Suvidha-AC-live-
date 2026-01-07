<?php namespace App\models\Candidate;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class AbsentVoterModel extends Model
{
    
    protected $table = 'absent_voters';

    public $fillable = ['auth_mobile','otp'];

    public static function add_absent_voter($data = array()){
        $object = AbsentVoterModel::firstOrNew([
            'auth_mobile'   => $data['mobile']
        ]);
        $object->otp = $data['otp'];
        if($object->tracking_id == ''){
            $object->tracking_id = time();
        }
        return $object->save();
    }

    public static function unique_with_epic($data = array()){
        $object = AbsentVoterModel::where('epic_no', $data['epic_no'])->where('status', 1)->count();
        return $object;
    }

    public static function unique_with_mobile($data = array()){
        $object = AbsentVoterModel::where('auth_mobile', $data['auth_mobile'])->where('status', 1)->count();
        return $object;
    }

    public static function update_absentee_voter_details($data = array()){
        $object = AbsentVoterModel::where('auth_mobile', $data['auth_mobile'])->where('status', 0)->first();
        if(!$object){
            return false;
        }
        $unique_string = "ABS".strtoupper(str_random(5)).$object->id;
        $object->tracking_id = $unique_string;
        $object->epic_no = $data['epic_no'];
        $object->name = $data['name'];
        $object->father_name = $data['father_name'];
        $object->address = $data['address'];
        $object->house_no = $data['house_no'];
        $object->age = $data['age'];
        $object->mobile = $data['mobile'];
        $object->st_code = $data['st_code'];
        $object->ac_no = $data['ac_no'];
        $object->ps_no = $data['ps_no'];
        $object->dist_no = $data['dist_no'];
        $object->pincode = $data['pincode'];
        $object->village = $data['village'];
        $object->tehsil = $data['tehsil'];
        $object->serial_number = $data['serial_number'];
        $object->is_pwd = $data['is_pwd'];
        $object->same_address = $data['same_address'];
        $object->new_address = $data['new_address'];
        $object->new_village = $data['new_village'];
        $object->new_tehsil = $data['new_tehsil'];
        $object->new_pincode = $data['new_pincode'];
        $object->new_st_code = $data['new_st_code'];
        $object->new_dist_no = $data['new_dist_no'];
        $object->new_house_no = $data['new_house_no'];
        $object->request_type = 1;
        $results = $object->save();
        if($results){
            return $unique_string;
        }else{
            return false;
        }
    }

    public static function get_absentee_voter($tracking_id){
        $object = AbsentVoterModel::where('tracking_id',$tracking_id)->first();
        if(!$object){
            return false;
        }
        return $object->toArray();
    }
    

    public static function check_otp_time($data = array()){
        $date1 = date('Y-m-d H:i:s');
        $object = AbsentVoterModel::select('updated_at')->where('auth_mobile',$data['mobile'])->first();
        if(!$object){
            return false;
        }
        $date2      = $object->updated_at;
        $to_time    = strtotime($date2);
        $from_time  = strtotime($date1);
        return abs($to_time - $from_time);
    }

    public static function verify_otp($data = array()){
        return AbsentVoterModel::select('id')->where('auth_mobile',$data['mobile'])->where('otp',$data['otp'])->count();
    }
    
    public static function update_status($tracking_id){
        $object = AbsentVoterModel::where('tracking_id',$tracking_id)->first();
        if(!$object){
            return false;
        }
        $object->status = 1;
        return $object->save();
    }
}