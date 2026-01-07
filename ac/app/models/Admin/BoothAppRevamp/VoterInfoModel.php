<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB, Cache;
use App\models\Admin\BoothAppRevamp\{PollingStation, PhaseModel};
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class VoterInfoModel extends Model
{
  protected $table = 'tbl_voter_info';

  protected $connection = 'booth_revamp';

  public static function get_vooter_list($data = array()){

    $sql = VoterInfoModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info.ps_no"],
    ])->selectRaw('tbl_voter_info.epic_no, tbl_voter_info.name_en, tbl_voter_info.gender, tbl_voter_info.voter_serial_no, tbl_voter_info.unique_generated_id, tbl_voter_info.id');

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_voter_info.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_voter_info.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_voter_info.ps_no',$filter['ps_no']);
    }

    $sql->orderByRaw("tbl_voter_info.st_code ASC")->groupBy('tbl_voter_info.unique_generated_id');

    if(!empty($data['paginate'])){
      return $sql->paginate(100);
    }else{
      return $sql->get();
    }
}


public static function is_seal_encrypted($data = array()){

  $sql = VoterInfoModel::where('tbl_voter_info.st_code',$data['st_code']);
  $sql->where('tbl_voter_info.ac_no',$data['ac_no']);
  $sql->where('tbl_voter_info.ps_no',$data['ps_no']);
  $sql->where('tbl_voter_info.bar_code','!=','');
  return $sql->count();

}

public static function get_elector_count($filter = array()){

  $sql = VoterInfoModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info.ac_no"],
  ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info.ps_no"],
    ])->select("tbl_voter_info.ps_no, tbl_voter_info.ac_no, tbl_voter_info.st_code as st_code");

  $sql->where('CONST_TYPE','AC');

  $sql->where("polling_station.booth_app_excp", 0);

  if(!empty($filter['phase_no'])){
    $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
  }

  if(!empty($filter['st_code'])){
    $sql->where('tbl_voter_info.st_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('tbl_voter_info.ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('tbl_voter_info.ps_no',$filter['ps_no']);
  }

  if(!empty($filter['gender'])){
    if($filter['gender'] == 'O'){
      $sql->whereNotIn('tbl_voter_info.gender',['M','F']);
    }else{
      $sql->where('tbl_voter_info.gender',$filter['gender']);
    }
  }

  return $sql->count();

}

  //time interval
  public static function half_hour_times($filter = array()) {
    $start_time = '07:00';
    $end_time   = date('H:i');
    $phase_date = PhaseModel::get_phase_date($filter);
    if( date('Y-m-d') <= $phase_date){
      $end_time   = date('H:i');
    }else{
      $end_time   = '18:00';
    }
  
    if(strtotime($end_time) > strtotime(date('H:i'))){
      $end_time = '18:00';
    }
    $time_slot_label_for_line = [];
    $time_slot_label_for_line[] = $start_time;
    while(strtotime($start_time) < strtotime($end_time)){
      $start_time = date('H:i', strtotime("30 minutes", strtotime($start_time)));
      $time_slot_label_for_line[] = $start_time;
    }
    return $time_slot_label_for_line;

  }

  public static function get_voter_count($filter = array()){

    $sql = VoterInfoModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info.ps_no"],
    ])->join("tbl_voter_info_poll_status","tbl_voter_info_poll_status.epic_no","=","tbl_voter_info.epic_no");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_voter_info.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_voter_info.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_voter_info.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereNull('tbl_voter_info.gender');
      }else{
        $sql->where('tbl_voter_info.gender',$filter['gender']);
      }
    }

    return $sql->count(DB::raw('DISTINCT tbl_voter_info.epic_no'));

  }


  public static function get_aggregate_voters($filter = array()){

    $data = [
      'e_male' => 0,
      'e_female' => 0,
      'e_other' => 0,
      'e_total' => 0,
    ];
    $sql = VoterInfoModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_voter_info.st_code"],
      ["m_election_details.CONST_NO","=","tbl_voter_info.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_voter_info.st_code"],
      ["polling_station.AC_NO","=","tbl_voter_info.ac_no"],
      ["polling_station.PS_NO","=","tbl_voter_info.ps_no"],
    ])->selectRaw("COUNT(IF(tbl_voter_info.gender = 'M',1,NULL)) as e_male, COUNT(IF(tbl_voter_info.gender = 'F',1,NULL)) as e_female, COUNT(IF((tbl_voter_info.gender = 'O' OR tbl_voter_info.gender = 'T'),1,NULL)) as e_other");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_voter_info.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_voter_info.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_voter_info.ps_no',$filter['ps_no']);
    }

    $result = $sql->first();
    
    if($result){
      $data = [
        'e_male' => $result->e_male,
        'e_female' => $result->e_female,
        'e_other' => $result->e_other,
        'e_total' => $result->e_male+$result->e_female+$result->e_other,
      ];
    }

    return $data;
  
  }

  

}