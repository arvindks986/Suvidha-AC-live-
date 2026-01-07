<?php 
namespace App\models\Admin\mparty;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class MpartyLogModel extends Model
{
    
    protected $table = 'm_party_logs';

    public static function clone_record($ccode){

    	date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");
        $table = "m_party";
        $data = DB::table($table)->select('*')->where('CCODE',$ccode)->first();
        if($data){
            $results = [];
            foreach ($data as $key => $value) {
                $results[$key] = $value;
            }
            $update_record = [
                'log_updated_at'         => $datetime,
                'log_updated_by'        => Auth::user()->officername,
            ];
        	MpartyLogModel::insert(array_merge($results,$update_record));
        }

    }
    
} 