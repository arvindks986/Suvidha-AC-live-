<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class VoterInfoPollStatusModel extends Model
{
  protected $table = 'tbl_voter_info_poll_status';

  protected $connection = 'booth_revamp';

  
  public static function get_voter_count($filter = array()){

    $sql = VoterInfoPollStatusModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info_poll_status.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info_poll_status.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info_poll_status.ps_no"],
    ])->select("tbl_voter_info_poll_status.id");

    $sql->where("polling_station.booth_app_excp", 0);

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_voter_info_poll_status.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_voter_info_poll_status.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_voter_info_poll_status.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereRaw("tbl_voter_info_poll_status.gender is NULL OR tbl_voter_info_poll_status.gender = 'O'");
      }else{
        $sql->where('tbl_voter_info_poll_status.gender',$filter['gender']);
      }
    }

    if(!empty($filter['time_between'])){
      $date = date('Y-m-d');
      if($date > '2020-02-08'){
        $date = '2020-02-08';
      }
      $in_time  = $date.' '.date('H:i:s', strtotime("-30 minutes", strtotime($filter['time_between'])));
      $out_time = $date.' '.date('H:i:s', strtotime($filter['time_between']));
      $sql->whereRaw("tbl_voter_info_poll_status.in_out_time >= '".$in_time."' and tbl_voter_info_poll_status.in_out_time <= '".$out_time."'");
    }

    if(!empty($filter['is_cumulative'])){
      $date = date('Y-m-d');
      if($date > '2020-02-08'){
        $date = '2020-02-08';
      }
      $in_time  = $date.' '.date('H:i:s', strtotime($filter['is_cumulative']));
      $sql->whereRaw("tbl_voter_info_poll_status.in_out_time <= '".$in_time."'");
    }

    $sql->whereIn('tbl_voter_info_poll_status.user_type', ['34','35']);

    $sql->where('tbl_voter_info_poll_status.row_status','A');

    return $sql->count(DB::raw('DISTINCT tbl_voter_info_poll_status.epic_no'));

  }

  public static function get_voters($filter = array()){

    $sql = VoterInfoPollStatusModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info_poll_status.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info_poll_status.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info_poll_status.ps_no"],
    ])->selectRaw("tbl_voter_info_poll_status.id, tbl_voter_info_poll_status.serial_no, tbl_voter_info_poll_status.epic_no, tbl_voter_info_poll_status.in_out_time, tbl_voter_info_poll_status.scan_type, tbl_voter_info_poll_status.st_code, tbl_voter_info_poll_status.ac_no, tbl_voter_info_poll_status.ps_no, tbl_voter_info_poll_status.gender, tbl_voter_info_poll_status.age");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_voter_info_poll_status.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_voter_info_poll_status.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_voter_info_poll_status.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereNull('tbl_voter_info_poll_status.gender');
      }else{
        $sql->where('tbl_voter_info_poll_status.gender',$filter['gender']);
      }
    }

    if(!empty($filter['time_between'])){
      $date = date('Y-m-d');
      if($date > '2020-02-08'){
        $date = '2020-02-08';
      }
      $in_time  = $date.' '.date('H:i:s', strtotime("-30 minutes", strtotime($filter['time_between'])));
      $out_time = $date.' '.date('H:i:s', strtotime($filter['time_between']));
      $sql->whereRaw("tbl_voter_info_poll_status.in_out_time >= '".$in_time."' and tbl_voter_info_poll_status.in_out_time <= '".$out_time."'");
    }

    if(!empty($filter['is_cumulative'])){
      $date = date('Y-m-d');
      if($date > '2020-02-08'){
        $date = '2020-02-08';
      }
      $in_time  = $date.' '.date('H:i:s', strtotime($filter['is_cumulative']));
      $sql->whereRaw("tbl_voter_info_poll_status.in_out_time <= '".$in_time."'");
    }

    $sql->whereIn('tbl_voter_info_poll_status.user_type', ['34','35']);

    $sql->where('tbl_voter_info_poll_status.row_status','A');

    return $sql->get();

  }

  //writen again
  public static function get_elector_by_age($filter = array()){

    $sql = VoterInfoPollStatusModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info_poll_status.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info_poll_status.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info_poll_status.ps_no"],
    ])->selectRaw("tbl_voter_info_poll_status.id, tbl_voter_info_poll_status.serial_no, tbl_voter_info_poll_status.epic_no, tbl_voter_info_poll_status.in_out_time, tbl_voter_info_poll_status.scan_type, tbl_voter_info_poll_status.st_code, tbl_voter_info_poll_status.ac_no, tbl_voter_info_poll_status.ps_no, tbl_voter_info_poll_status.gender, tbl_voter_info_poll_status.age");

    $sql->where('CONST_TYPE','AC');


    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_voter_info_poll_status.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_voter_info_poll_status.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_voter_info_poll_status.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      $sql->where('tbl_voter_info_poll_status.gender',$filter['gender']);
    }

    if(!empty($filter['age_between'])){
      $sql->whereBetween('tbl_voter_info_poll_status.age',explode('-',$filter['age_between']));
    }
    
    $sql->whereIn('tbl_voter_info_poll_status.user_type', ['34','35']);

    $sql->where('tbl_voter_info_poll_status.row_status','A');

    return $sql->count(DB::raw('DISTINCT tbl_voter_info_poll_status.epic_no'));
  }


  //in one query
  public static function get_age_group($filter = array()){
    $data = [];
    $sub_sql_array = [];
    if(!empty($filter['age_gap'])){
      $i = 0;
      foreach($filter['age_gap'] as $itr_age){
        $age_gap = explode('-', $itr_age);
        $sub_sql_array[] = "COUNT(IF(age >= ".$age_gap[0]." AND age <= ".$age_gap[1].",1,NULL)) as age".$i;
        $i++;
        $data[$i] = 0;
      }
    }

    $sub_sql = implode(',',$sub_sql_array);

    $sql = VoterInfoPollStatusModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info_poll_status.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info_poll_status.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info_poll_status.ps_no"],
    ])->selectRaw($sub_sql);

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_voter_info_poll_status.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_voter_info_poll_status.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_voter_info_poll_status.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      $sql->where('tbl_voter_info_poll_status.gender',$filter['gender']);
    }

    $sql->whereIn('tbl_voter_info_poll_status.user_type', ['34','35']);

    $sql->where('tbl_voter_info_poll_status.row_status','A');

    $result = $sql->first();

    if($result){
      $data = array_values($result->toArray());
    }

    return $data;
  }

  public static function get_cumulative_time_data($filter = array()){
    $data = [];
    $sub_sql_array = [];
    if(!empty($filter['is_cumulative'])){
      $i = 0;
      $date = PhaseModel::get_phase_date($filter);

      foreach($filter['is_cumulative'] as $itr_cum){
        $in_time  = $date.' '.date('H:i:s', strtotime($itr_cum));
        $sub_sql_array[] = "COUNT(IF(in_out_time<= '".$in_time."',1,NULL)) as time".$i;
        $data[$i] = 0;
        $i++;
      }
    }

    $sub_sql = implode(',',$sub_sql_array);

    $sql = VoterInfoPollStatusModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info_poll_status.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info_poll_status.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info_poll_status.ps_no"],
    ])->selectRaw($sub_sql);

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_voter_info_poll_status.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_voter_info_poll_status.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_voter_info_poll_status.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      $sql->where('tbl_voter_info_poll_status.gender',$filter['gender']);
    }

    $sql->whereIn('tbl_voter_info_poll_status.user_type', ['34','35']);

    $sql->where('tbl_voter_info_poll_status.row_status','A');

    $result = $sql->first();

    if($result){
      $data = array_values($result->toArray());
    }

    return $data;
  }

   public static function get_voters_by_time($filter = array()){
    $data = [];
    $sub_sql_array = [];
    if(!empty($filter['is_cumulative'])){
      $i = 0;
      $date = PhaseModel::get_phase_date($filter);

      foreach($filter['is_cumulative'] as $itr_cum){
        $fix_in_time  = $date.' '.date('H:i:s', strtotime($itr_cum));
        $fix_out_time = $date.' '. date('H:i:s', strtotime("+30 minutes", strtotime($itr_cum)));
        $sub_sql_array[] = "COUNT(IF(in_out_time > '".$fix_in_time."' AND in_out_time <= '".$fix_out_time."',1,NULL)) as timedata".$i;
        $data[$i] = 0;
        $i++;
      }
    }

    if(!empty($filter['is_time_slap'])){
      $i = 0;
      $date = PhaseModel::get_phase_date($filter);

      foreach($filter['is_time_slap'] as $itr_cum){
        $fix_in_time  = $date.' '.date('H:i:s', strtotime($itr_cum));
        $fix_out_time = $date.' '. date('H:i:s', strtotime("+120 minutes", strtotime($itr_cum)));
        $sub_sql_array[] = "COUNT(IF(in_out_time > '".$fix_in_time."' AND in_out_time <= '".$fix_out_time."',1,NULL)) as timedata".$i;
        $data[$i] = 0;
        $i++;
      }
    }

    $sub_sql = implode(',',$sub_sql_array);


    $sql = VoterInfoPollStatusModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info_poll_status.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info_poll_status.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info_poll_status.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info_poll_status.ps_no"],
    ])->selectRaw($sub_sql);

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_voter_info_poll_status.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_voter_info_poll_status.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_voter_info_poll_status.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      $sql->where('tbl_voter_info_poll_status.gender',$filter['gender']);
    }

    $sql->whereIn('tbl_voter_info_poll_status.user_type', ['34','35']);

    $sql->where('tbl_voter_info_poll_status.row_status','A');

    $result = $sql->first();

    if($result){
      $data = array_values($result->toArray());
    }

    return $data;
  }

  

}