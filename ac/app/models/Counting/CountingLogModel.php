<?php namespace App\models\Counting;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class CountingLogModel extends Model
{
    
    protected $table = 'counting_logs';

    public static function clone_record($id, $st_code = ''){

    	date_default_timezone_set('Asia/Kolkata');
        $datetime = date("Y-m-d H:i:s");
        $table = "counting_master_".strtolower($st_code);
        $data = DB::table($table)->select('ac_no', 'pc_no','dist_no', 'nom_id', 'candidate_id', 'election_id', 'month', 'year', 'round1', 'round2', 'round3', 'round4', 'round5', 'round6', 'round7', 'round8', 'round9', 'round10', 'round11', 'round12', 'round13', 'round14', 'round15', 'round16', 'round17', 'round18', 'round19', 'round20', 'round21', 'round22', 'round23', 'round24', 'round25', 'round26', 'round27', 'round28', 'round29', 'round30', 'round31', 'round32', 'round33', 'round34', 'round35', 'round36', 'round37', 'round38', 'round39', 'round40', 'round41', 'round42', 'round43', 'round44', 'round45', 'round46', 'round47', 'round48', 'round49', 'round50', 'round51','round52','round53','round54','round55','round56','round57','round58','round59','round60','round61','round62','round63','round64','round65','round66','round67','round68','round69','round70','round71','round72','round73','round74','round75','round76','round77','round78','round79','round80','round81','round82','round83','round84','round85','round86','round87','round88','round89','round90','round91','round92','round93','round94','round95','round96','round97','round98','round99','round100','round101','round102','round103','round104','round105','round106','round107','round108','round109','round110','round111','round112','round113','round114','round115','round116','round117','round118','round119','round120','round121','round122','round123','round124','round125','round126','round127','round128','round129','round130', 'postalballot_vote', 'total_vote', 'created_at', 'added_create_at', 'created_by', 'updated_at', 'added_update_at', 'updated_by', 'transactiontime', 'complete_round', 'finalized_round','postalvote_update_at')->where('id',$id)->first();
        if($data){
            $results = [];
            foreach ($data as $key => $value) {
                $results[$key] = $value;
            }
            $update_record = [
                'st_code'               => $st_code,
                'table_primary_key'     => $id,
                'table_name'            => $table,
                'log_date_time'         => $datetime,
                'log_updated_by'        => Auth::user()->officername,
            ];
        	CountingLogModel::insert(array_merge($results,$update_record));
        }

    }
    
}