<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;
use Common;

class PhaseModel extends Model
{
    protected $table = 'm_election_details';

    public static function get_phases($filter = array()){
     
        $sql = PhaseModel::join('boothapp_enable_acs', [
            ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code'],
            ['m_election_details.CONST_NO','=','boothapp_enable_acs.ac_no'],
        ])->join("m_state","m_state.ST_CODE","=","m_election_details.ST_CODE")->join("m_district",[
            ["m_district.ST_CODE","=","boothapp_enable_acs.ST_CODE"],
            ["m_district.DIST_NO","=","boothapp_enable_acs.dist_no"]
        ])->join("m_ac",[
            ["m_ac.ST_CODE","=","boothapp_enable_acs.ST_CODE"],
            ["m_ac.AC_NO","=","boothapp_enable_acs.ac_no"]
        ]);

        $sql->selectRaw('boothapp_enable_acs.st_code, boothapp_enable_acs.dist_no, boothapp_enable_acs.ac_no, PHASE_NO as phase_no, ST_NAME as st_name, DIST_NAME as dist_name, AC_NAME as ac_name');

        $sql->where("CONST_TYPE","AC");

        if(!empty($filter['phase_no'])){
          $sql->where('PHASE_NO',$filter['phase_no']);
        }

        if(!empty($filter['st_code'])){
           $sql->where('m_election_details.ST_CODE',$filter['st_code']);
        }

        if(!empty($filter['dist_no'])){
          $sql->where('boothapp_enable_acs.dist_no',$filter['dist_no']);
        }

        if(!empty($filter['ac_no'])){
          $sql->where('CONST_NO',$filter['ac_no']);
        }

        if(!empty($filter['group_by'])){
            if($filter['group_by'] == 'st_code'){
                $sql->groupBy('m_election_details.ST_CODE');
            }else if($filter['group_by'] == 'ac_no'){
                $sql->groupBy('CONST_NO');
            }else if($filter['group_by'] == 'phase_no'){
                $sql->groupBy('PHASE_NO');
            }else{
                $sql->groupBy(DB::raw("m_election_details.ST_CODE, CONST_NO"));
            }
        }

        return $sql->get()->toArray();

    }


    public static function get_phase_date($filter = array()){
        $sql = PhaseModel::join('boothapp_enable_acs', [
            ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code'],
            ['m_election_details.CONST_NO','=','boothapp_enable_acs.ac_no'],
        ])->join('m_schedule', [
            ['m_election_details.ScheduleID','=','m_schedule.SCHEDULEID']
        ]);
        if(!empty($filter['phase_no'])){
            $sql->where('m_election_details.PHASE_NO', $filter['phase_no']);
        }
        $result = $sql->orderBy('m_schedule.DATE_POLL', 'DESC')->select('m_schedule.DATE_POLL')->first();
        if(!$result){
            return date("Y-m-d");
        }
        return $result->DATE_POLL;
    }

}