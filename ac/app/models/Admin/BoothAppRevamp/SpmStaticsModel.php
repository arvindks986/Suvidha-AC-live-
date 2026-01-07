<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;

class SpmStaticsModel extends Model
{
protected $table = 'polling_start_end_statics';

protected $connection = 'booth_revamp';

public static function total_statics_count($filter = array()){

  $sql = SpmStaticsModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_start_end_statics.st_code"],
      ["m_election_details.CONST_NO","=","polling_start_end_statics.ac_no"],
    ])->select('polling_start_end_statics.ps_no');

  $sql->where('CONST_TYPE','AC');

  if(!empty($filter['phase_no'])){
    $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
  }

  if(!empty($filter['st_code'])){
    $sql->where('polling_start_end_statics.state_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('polling_start_end_statics.ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('polling_start_end_statics.ps_no',$filter['ps_no']);
  }

  if(!empty($filter['download_time'])){
    $sql->where('polling_start_end_statics.download_time','>',0);
  }

  if(!empty($filter['role_id'])){
      $sql->where('polling_start_end_statics.user_type',$filter['role_id']);
  }

  if(!empty($filter['is_started'])){
    $sql->whereIn('polling_start_end_statics.user_type', ['33','34','35'])->whereNotNull('polling_start_end_statics.poll_start_time');
  }

  if(!empty($filter['is_end'])){
    $sql->whereIn('polling_start_end_statics.user_type', ['34','35'])->whereNotNull('polling_start_end_statics.poll_end_time')->whereNotNull('polling_start_end_statics.qr_search')->whereNotNull('polling_start_end_statics.qr_aver_scan_time');
  }

  if(!empty($filter['event_type'])){
      $sql->where('polling_start_end_statics.event_type',$filter['event_type']);
  }

  return $sql->count(DB::raw('DISTINCT polling_start_end_statics.ps_no'));

}

public static function get_statics($filter = array()){

  $sql = SpmStaticsModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_start_end_statics.st_code"],
      ["m_election_details.CONST_NO","=","polling_start_end_statics.ac_no"],
    ])->selectRaw('user_type as role_id, download_time, ps_no, ac_no, state_code as st_code, event_type, user_unique_id as officer_id');

  $sql->where('CONST_TYPE','AC');

  if(!empty($filter['phase_no'])){
    $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
  }

  if(!empty($filter['st_code'])){
    $sql->where('polling_start_end_statics.state_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('polling_start_end_statics.ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('polling_start_end_statics.ps_no',$filter['ps_no']);
  }

  if(!empty($filter['download_time'])){
    $sql->where('polling_start_end_statics.download_time','>',0);
  }

  if(!empty($filter['role_id'])){
      $sql->where('polling_start_end_statics.user_type',$filter['role_id']);
  }

  if(!empty($filter['event_type'])){
      $sql->where('polling_start_end_statics.event_type',$filter['event_type']);
  }

  if(!empty($filter['role_id'])){
      $sql->where('polling_start_end_statics.user_type',$filter['role_id']);
  }

  if(!empty($filter['is_started'])){
    $sql->whereIn('polling_start_end_statics.user_type', ['33','34','35'])->whereNotNull('polling_start_end_statics.poll_start_time');
  }

  if(!empty($filter['is_end'])){
    $sql->whereIn('polling_start_end_statics.user_type', ['34','35'])->whereNotNull('polling_start_end_statics.poll_end_time')->whereNotNull('polling_start_end_statics.qr_search')->whereNotNull('polling_start_end_statics.qr_aver_scan_time');
  }

  $sql->groupBy('polling_start_end_statics.state_code')->groupBy('polling_start_end_statics.ac_no')->groupBy('polling_start_end_statics.ps_no');
  $sql->orderByRaw("polling_start_end_statics.state_code, polling_start_end_statics.ac_no, polling_start_end_statics.ps_no ASC");

  return $sql->get();

}

public static function get_static($filter = array()){

  $sql = SpmStaticsModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_start_end_statics.st_code"],
      ["m_election_details.CONST_NO","=","polling_start_end_statics.ac_no"],
    ])->select('polling_start_end_statics.*');

  $sql->where('CONST_TYPE','AC');

  if(!empty($filter['phase_no'])){
    $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
  }

  if(!empty($filter['st_code'])){
    $sql->where('polling_start_end_statics.state_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('polling_start_end_statics.ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('polling_start_end_statics.ps_no',$filter['ps_no']);
  }

  if(!empty($filter['download_time'])){
    $sql->where('polling_start_end_statics.download_time','>',0);
  }

  if(!empty($filter['role_id'])){
      $sql->where('polling_start_end_statics.user_type',$filter['role_id']);
  }

  if(!empty($filter['event_type'])){
      $sql->where('polling_start_end_statics.event_type',$filter['event_type']);
  }

  if(!empty($filter['role_id'])){
      $sql->where('polling_start_end_statics.user_type',$filter['role_id']);
  }

  if(!empty($filter['is_started'])){
    $sql->whereIn('polling_start_end_statics.user_type', ['33','34','35'])->whereNotNull('polling_start_end_statics.poll_start_time');
  }

  if(!empty($filter['is_end'])){
    $sql->whereIn('polling_start_end_statics.user_type', ['34','35'])->whereNotNull('polling_start_end_statics.poll_end_time')->whereNotNull('polling_start_end_statics.qr_search')->whereNotNull('polling_start_end_statics.qr_aver_scan_time');
  }

  $query = $sql->first();
  if(!$query){
    return false;
  }
  return $query->toArray();

}

   public static function get_scan_count($filter = array()){

    $sql = SpmStaticsModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_start_end_statics.st_code"],
      ["m_election_details.CONST_NO","=","polling_start_end_statics.ac_no"],
    ])->selectRaw("IFNULL(SUM(polling_start_end_statics.qr_search),0) as total_qr, IFNULL(SUM(polling_start_end_statics.epic_search),0) as total_epic, IFNULL(SUM(polling_start_end_statics.b_slip_search),0) as total_bs, IFNULL(SUM(polling_start_end_statics.name_search),0) as total_name");

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('polling_start_end_statics.state_code',$filter['st_code']);
    }
    if(!empty($filter['ac_no'])){
      $sql->where('polling_start_end_statics.ac_no',$filter['ac_no']);
    }
    if(!empty($filter['ps_no'])){
      $sql->where('polling_start_end_statics.ps_no',$filter['ps_no']);
    }
    if(empty($filter['search_by'])){
      $sql->whereIn('polling_start_end_statics.user_type', ['34','35']);
    }
    $sql->whereNotNull('polling_start_end_statics.qr_search')->whereNotNull('polling_start_end_statics.epic_search')->whereNotNull('polling_start_end_statics.b_slip_search')->whereNotNull('polling_start_end_statics.name_search');
    $query = $sql->first();
    if(!$query){
      return false;
    }
    return [
      'total_qr'    => $query->total_qr,
      'total_epic'  => $query->total_epic,
      'total_bs'    => $query->total_bs,
      'total_name'  => $query->total_name,
    ];


  }


}