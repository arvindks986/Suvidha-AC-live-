<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
class ScheduleDetailModel extends Model
{
    
    protected $table = 'pd_scheduledetail';

    public static function get_record($id){

        $object = ScheduleDetailModel::select('pd_scheduleid', 'scheduleid', 'st_code', 'pc_no', 'ac_no', 'insert_time', 'round1_voter_male', 'round1_voter_female', 'round1_voter_other', 'round1_voter_total', 'round2_voter_male', 'round2_voter_female', 'round2_voter_other', 'round2_voter_total', 'round3_voter_male', 'round3_voter_female', 'round3_voter_other', 'round3_voter_total', 'round4_voter_male', 'round4_voter_female', 'round4_voter_other', 'round4_voter_total', 'round5_voter_male', 'round5_voter_female', 'round5_voter_other', 'round5_voter_total', 'end_voter_male', 'end_voter_female', 'end_voter_other', 'end_voter_total', 'total_male', 'total_female', 'total_other', 'total', 'est_turnout_round1', 'est_turnout_round2', 'est_turnout_round3', 'est_turnout_round4', 'est_turnout_round5', 'est_turnout_total', 'ac_election', 'added_create_at', 'created_at', 'added_update_at', 'updated_at', 'created_by', 'updated_by', 'update_at_round1', 'update_at_round2', 'update_at_round3', 'update_at_round4', 'update_at_round5', 'update_at_final', 'update_device_round1', 'update_device_round2', 'update_device_round3', 'update_device_round4', 'update_device_round5', 'update_device_final', 'close_of_poll', 'updated_at_close_of_poll', 'updated_device_close_of_poll', 'est_poll_close', 'electors_total', 'est_voters', 'end_of_poll_finalize')->find($id);
        if(!$object){
        	return false;
        }
        return $object->toArray();

    }
    
}