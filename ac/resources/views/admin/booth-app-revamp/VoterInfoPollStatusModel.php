<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class VoterInfoPollStatusModel extends Model
{
  protected $table = 'tbl_voter_info_poll_status';

  protected $connection = 'booth_revamp';

  
  public static function get_voter_count($filter = array()){

    $sql = VoterInfoPollStatusModel::select("id");

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_no',$filter['ac_no']);
    }

    $where_raw = Common::get_common_query(true);
    $sql->whereRaw($where_raw);

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereRaw("gender is NULL OR gender = 'O'");
      }else{
        $sql->where('gender',$filter['gender']);
      }
    }

    if(!empty($filter['time_between'])){
      $date = date('Y-m-d');
      if($date > '2019-10-21'){
        $date = '2019-10-21';
      }
      $in_time  = $date.' '.date('H:i:s', strtotime("-30 minutes", strtotime($filter['time_between'])));
      $out_time = $date.' '.date('H:i:s', strtotime($filter['time_between']));
      $sql->whereRaw("in_out_time >= '".$in_time."' and in_out_time <= '".$out_time."'");
    }

    if(!empty($filter['is_cumulative'])){
      $date = date('Y-m-d');
      if($date > '2019-10-21'){
        $date = '2019-10-21';
      }
      $in_time  = $date.' '.date('H:i:s', strtotime("-30 minutes", strtotime($filter['is_cumulative'])));
      $out_time = $date.' '.date('H:i:s', strtotime($filter['is_cumulative']));
      $sql->whereRaw("in_out_time <= '".$out_time."'");
    }

    $sql->where("user_type",'35');

    $sql->where('row_status','A');

    return $sql->count(DB::raw('DISTINCT tbl_voter_info_poll_status.epic_no'));

  }

  public static function get_voters($filter = array()){

    $sql = VoterInfoPollStatusModel::selectRaw("id, serial_no, epic_no, in_out_time, scan_type, st_code, ac_no, ps_no, gender, age");

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_no',$filter['ac_no']);
    }

    $where_raw = Common::get_common_query(true);
    $sql->whereRaw($where_raw);

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereNull('gender');
      }else{
        $sql->where('gender',$filter['gender']);
      }
    }

    if(!empty($filter['time_between'])){
      $date = date('Y-m-d');
      if($date > '2019-10-21'){
        $date = '2019-10-21';
      }
      $in_time  = $date.' '.date('H:i:s', strtotime("-30 minutes", strtotime($filter['time_between'])));
      $out_time = $date.' '.date('H:i:s', strtotime($filter['time_between']));
      $sql->whereRaw("in_out_time >= '".$in_time."' and in_out_time <= '".$out_time."'");
    }

    if(!empty($filter['is_cumulative'])){
      $date = date('Y-m-d');
      if($date > '2019-10-21'){
        $date = '2019-10-21';
      }
      $in_time  = $date.' '.date('H:i:s', strtotime("-30 minutes", strtotime($filter['is_cumulative'])));
      $out_time = $date.' '.date('H:i:s', strtotime($filter['is_cumulative']));
      $sql->whereRaw("in_out_time <= '".$out_time."'");
    }

    $sql->where("user_type",'35');

    $sql->where('row_status','A');

    return $sql->get();

  }

  //writen again
  public static function get_elector_by_age($filter = array()){

    $sql = VoterInfoPollStatusModel::selectRaw("id, serial_no, epic_no, in_out_time, scan_type, st_code, ac_no, ps_no, gender, age");

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_no',$filter['ac_no']);
    }

    $where_raw = Common::get_common_query(true);
    $sql->whereRaw($where_raw);

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      $sql->where('gender',$filter['gender']);
    }

    if(!empty($filter['age_between'])){
      $sql->whereBetween('age',explode('-',$filter['age_between']));
    }
    
    $sql->where("user_type",'35');

    $sql->where('row_status','A');

    return $sql->count(DB::raw('DISTINCT tbl_voter_info_poll_status.epic_no'));
  }



}