<?php namespace App\models\Candidate;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class CandidateCriminalLogModel extends Model
{
    
    protected $table = 'candidate_criminaluploads_log';

    public static function clone_record($candidate_id){
    
    	date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");

        $data = DB::table('candidate_criminaluploads')->select('*')->where('candidate_id',$candidate_id)->first();
        

        if($data){
            $results = [];
            foreach ($data as $key => $value) {
                $results[$key] = $value;
            }
            
            $update_record = [
                'log_updated_at'         => $datetime,
                'log_updated_by'        => Auth::user()->officername,
            ];
            
        	CandidateCriminalLogModel::insert(array_merge($results,$update_record));
        }

    }
    
}