<?php namespace App\models\Candidate;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class CounterAffidavitLogModel extends Model
{
    
    protected $table = 'candidate_counteraffidavit_detail_log';

    public static function clone_record($nom_id){
    
    	date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");

        $data = DB::table('candidate_counteraffidavit_detail')->select('*')->where('nom_id',$nom_id)->get();
        

        if($data){
            $results = [];
            foreach ($data as $key => $value) {
                $results[$key] = $value;
            }
            
            $update_record = [
                'log_updated_at'         => $datetime,
                'log_updated_by'        => Auth::user()->officername,
            ];
            
        	CounterAffidavitLogModel::insert(array_merge($results,$update_record));
        }

    }
    
}