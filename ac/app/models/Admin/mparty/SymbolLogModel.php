<?php 
namespace App\models\Admin\mparty;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class SymbolLogModel extends Model
{
    
    protected $table = 'm_symbol_logs';

    public static function clone_record($sysno){

    	date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");
        $table = "m_symbol";
        $data = DB::table($table)->select('*')->where('SYMBOL_NO',$sysno)->first();
        if($data){
            $results = [];
            foreach ($data as $key => $value) {
                $results[$key] = $value;
            }
            $update_record = [
                'log_updated_at'         => $datetime,
                'log_updated_by'        => Auth::user()->officername,
            ];
        	SymbolLogModel::insert(array_merge($results,$update_record));
        }

    }
    
} 