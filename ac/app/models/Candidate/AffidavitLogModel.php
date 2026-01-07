<?php namespace App\models\Candidate;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class AffidavitLogModel extends Model
{
    
    protected $table = 'affidavit_logs';

    public static function clone_record($nom_id){
    
    	date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");

        $data = DB::table('candidate_affidavit_detail')->select('*')->where('nom_id',$nom_id)->first();
        

        if($data){
            $results = [];
            foreach ($data as $key => $value) {
                $results[$key] = $value;
            }
            
            $update_record = [
                'log_updated_at'         => $datetime,
                'log_updated_by'        => Auth::user()->officername,
            ];
            
        	AffidavitLogModel::insert(array_merge($results,$update_record));
        }

    }
    
}