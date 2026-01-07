<?php namespace App\models\Candidate;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class CandidateLogModel extends Model
{
    
    protected $table = 'candidate_logs';

    public static function clone_record($nom_id){

    	date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");

        $data = DB::table('candidate_nomination_detail')->select('nom_id','candidate_id','party_id','symbol_id','election_id','ac_no','pc_no','st_code','district_no','party_type','cand_party_type','application_status')->where('nom_id',$nom_id)->first();
         
         $canddata = DB::table('candidate_personal_detail')->select('*')->where('candidate_id',$data->candidate_id)->first();

        if($data){
            $results = [];
            foreach ($data as $key => $value) {
                $results[$key] = $value;
            }
             $results1 = [];
            foreach ($canddata as $key => $value) {
                $results1[$key] = $value;
            }
            $update_record = [
                'log_updated_at'         => $datetime,
                'log_updated_by'        => Auth::user()->officername,
            ];
            $results2=array_merge($results,$results1);
        	CandidateLogModel::insert(array_merge($results2,$update_record));
        }

    }
    
}