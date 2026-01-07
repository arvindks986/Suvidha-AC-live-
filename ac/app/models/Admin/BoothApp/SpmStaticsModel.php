<?php namespace App\models\Admin\BoothApp;

use Illuminate\Database\Eloquent\Model;
use DB;

class SpmStaticsModel extends Model
{
protected $table = 'polling_start_end_statics';

protected $connection = 'spm';

public static function total_statics_count($filter = array()){

  $sql = SpmStaticsModel::select('ps_no');

  if(!empty($filter['st_code'])){
    $sql->where('state_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('ps_no',$filter['ps_no']);
  }

  if(!empty($filter['download_time'])){
    $sql->where('download_time','>',0);
  }

  if(!empty($filter['role_id'])){
      $sql->where('user_type',$filter['role_id']);
  }

  if(!empty($filter['is_started'])){
    $sql->whereIn('user_type', ['33','35'])->whereNotNull('poll_start_time');
  }

  if(!empty($filter['is_end'])){
    $sql->where('user_type', '35')->whereNotNull('poll_end_time')->whereNotNull('qr_search')->whereNotNull('qr_aver_scan_time');
  }

  if(!empty($filter['event_type'])){
      $sql->where('event_type',$filter['event_type']);
  }

  return $sql->count(DB::raw('DISTINCT ps_no'));

}

public static function get_statics($filter = array()){

  $sql = SpmStaticsModel::selectRaw('user_type as role_id, download_time, ps_no, ac_no, state_code as st_code, event_type, user_unique_id as officer_id');

  if(!empty($filter['st_code'])){
    $sql->where('state_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('ps_no',$filter['ps_no']);
  }

  if(!empty($filter['download_time'])){
    $sql->where('download_time','>',0);
  }

  if(!empty($filter['role_id'])){
      $sql->where('user_type',$filter['role_id']);
  }

  if(!empty($filter['event_type'])){
      $sql->where('event_type',$filter['event_type']);
  }

  if(!empty($filter['role_id'])){
      $sql->where('user_type',$filter['role_id']);
  }

  if(!empty($filter['is_started'])){
    $sql->whereIn('user_type', ['33','35'])->whereNotNull('poll_start_time');
  }

  if(!empty($filter['is_end'])){
    $sql->where('user_type', '35')->whereNotNull('poll_end_time')->whereNotNull('qr_search')->whereNotNull('qr_aver_scan_time');
  }

  $sql->groupBy('state_code')->groupBy('ac_no')->groupBy('ps_no');
  $sql->orderByRaw("state_code, ac_no, ps_no ASC");

  return $sql->get();

}

public static function get_static($filter = array()){

  $sql = SpmStaticsModel::select('*');

  if(!empty($filter['st_code'])){
    $sql->where('state_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('ps_no',$filter['ps_no']);
  }

  if(!empty($filter['download_time'])){
    $sql->where('download_time','>',0);
  }

  if(!empty($filter['role_id'])){
      $sql->where('user_type',$filter['role_id']);
  }

  if(!empty($filter['event_type'])){
      $sql->where('event_type',$filter['event_type']);
  }

  if(!empty($filter['role_id'])){
      $sql->where('user_type',$filter['role_id']);
  }

  if(!empty($filter['is_started'])){
    $sql->whereIn('user_type', ['33','35'])->whereNotNull('poll_start_time');
  }

  if(!empty($filter['is_end'])){
    $sql->where('user_type', '35')->whereNotNull('poll_end_time')->whereNotNull('qr_search')->whereNotNull('qr_aver_scan_time');
  }

  $query = $sql->first();
  if(!$query){
    return false;
  }
  return $query->toArray();

}

   public static function get_scan_count($filter = array()){

    $sql = SpmStaticsModel::selectRaw("IFNULL(SUM(qr_search),0) as total_qr, IFNULL(SUM(epic_search),0) as total_epic, IFNULL(SUM(b_slip_search),0) as total_bs, IFNULL(SUM(name_search),0) as total_name");
    if(!empty($filter['st_code'])){
      $sql->where('state_code',$filter['st_code']);
    }
    if(!empty($filter['ac_no'])){
      $sql->where('ac_no',$filter['ac_no']);
    }
    if(!empty($filter['ps_no'])){
      $sql->where('ps_no',$filter['ps_no']);
    }
    if(empty($filter['search_by'])){
      $sql->where('user_type','35');
    }
    $sql->whereNotNull('qr_search')->whereNotNull('epic_search')->whereNotNull('b_slip_search')->whereNotNull('name_search');
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