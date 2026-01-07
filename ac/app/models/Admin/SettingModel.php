<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Cache;
use App\Classes\xssClean;

class SettingModel extends Model
{
     protected $table = 'setting';

    protected $fillable = ['code','key','value','serialized'];

    public $timestamps = false;
	
    public static function add_record($code, $request){
        $clean_input = new xssClean();
        SettingModel::where('code', $code)->delete();

        foreach ($request->except('_token') as $key => $value) {
            $is_serialize = 0 ;
            if(is_array($value)){
                $value          = serialize($value);
                $is_serialize   = 1;
            }else{
                $value = $clean_input->clean_input($value);
            }
            SettingModel::insert([
                'code'  => $clean_input->clean_input($code),
                'key'   => $clean_input->clean_input($key),
                'value'         => $value,
                'serialized'    => $is_serialize
            ]);
        }

    }

    public static function get_records($code){
        $data = [];
        $results = SettingModel::where('code',$code)->get();
        foreach ($results as $key => $result) {
            if($result->serialized == 1){
                $data[$result->key] = unserialize($result->value);
            }else{
                $data[$result->key] = $result->value;
            }
        }
        //Cache::forever('cache_setting',serialize($data));
        return $data;
    }

    public static function get_setting_cache(){
        SettingModel::generate_cache();
        $data =  SettingModel::get_records('setting');
        return $data;
    }

    public static function generate_cache(){
        SettingModel::get_records('setting');
    }
	
	public static function get_first_result($code){
        $object = SettingModel::where('code',$code)->first();
        if(!$object){
            return false;
        }
        return $object->toArray();
    }
	
	 public static function add_broadcast($data = array()){
        $object = SettingModel::firstOrNew([
            'code'          => 'config',
            'key'           => 'message',
        ]);
        $object->value         = $data['message'];
        $object->serialized    = 0;
        $object->save();
    }
    
}
