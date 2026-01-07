<?php  
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class AffidavitelogModel extends Model
{
    
    protected $table = 'candidate_affidavit_detail_log';

    public static function clone_record($nom_id){

    	date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");

        $data = DB::table('candidate_affidavit_detail')->where('nom_id',$nom_id)->first();
        

        if($data){
            $results = [];
            foreach ($data as $key => $value) {
                $results[$key] = $value;
            }
              
            $update_record = [
                'log_updated_at'         => $datetime,
                'log_updated_by'        => Auth::user()->officername,
            ];
             
        	AffidavitelogModel::insert(array_merge($results,$update_record));
        }

    }
    
}