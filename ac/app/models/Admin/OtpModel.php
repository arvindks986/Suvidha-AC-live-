<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class OtpModel extends Model
{
    
    protected $table = 'otp_verification';

    public $fillable = ['mobile','otp','code'];

    public static function add_otp($data = array()){
        $object = OtpModel::firstOrNew([
            'mobile'        => $data['mobile'],
            'code'   => $data['code'],
        ]);
        $object->otp = $data['otp'];
        return $object->save();
    }

    public static function check_otp_time($data = array()){
        $date1 = date('Y-m-d H:i:s');
        $object = OtpModel::select('updated_at')->where('mobile',$data['mobile'])->where('code',$data['code'])->first();
        if(!$object){
            return false;
        }
        $date2      = $object->updated_at;
        $to_time    = strtotime($date2);
        $from_time  = strtotime($date1);
        return abs($to_time - $from_time);
    }

    public static function verify_otp($data = array()){
        return OtpModel::select('id')->where('mobile',$data['mobile'])->where('otp',$data['otp'])->where('code',$data['code'])->count();
    }
    
}