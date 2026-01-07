<?php namespace App\models\Admin\BoothApp;

use Illuminate\Database\Eloquent\Model;
use DB;

class SpmVoterInfo extends Model
{
  protected $table = 'voter_info_poll_status';

  protected $connection = 'spm';

  public static function total_count($filter = array()){

    $sql = SpmVoterInfo::select('id');

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_number',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no_ag',$filter['ps_no']);
    }

    if(!empty($filter['is_connected'])){
      $sql->whereRaw("in_time > NOW() - INTERVAL 15 MINUTE");
    }

    if(!empty($filter['is_disconnected'])){
      $sql->whereRaw("in_time < NOW() - INTERVAL 15 MINUTE");
    }

    $sql->whereNotNull('search_type_by_pro');

    $sql->where('row_status','A');


    return $sql->count(DB::raw('DISTINCT ps_no_ag'));

  }

  public static function get_last_disconnected_ps($filter = array()){

    $sql = SpmVoterInfo::select('st_code','ac_number as ac_no','ps_no_ag as ps_no');

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_number',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no_ag',$filter['ps_no']);
    }

    if(!empty($filter['is_connected'])){
      $sql->whereRaw("in_time > NOW() - INTERVAL 15 MINUTE");
    }

    if(!empty($filter['is_disconnected'])){
      $sql->whereRaw("in_time < NOW() - INTERVAL 15 MINUTE");
    }

    $sql->where('row_status','A');

    $query = $sql->orderByRaw('st_code, ac_number, ps_no_ag')->first();
    if(!$query){
      return false;
    }
    return $query->toArray();

  }

  public static function get_voters_count($filter = array()){

    $sql = SpmVoterInfo::selectRaw("count(case when gender='M' then 1 end) as male, count(case when gender='F' then 1 end) as female, count(case when gender='O' then 1 end) as other, count(gender) as total, ps_no_ag as ps_no, ac_number as ac_no, st_code");

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_number',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no_ag',$filter['ps_no']);
    }

    if(!empty($filter['is_connected'])){
      $sql->whereRaw("in_time > NOW() - INTERVAL 15 MINUTE");
    }

    if(!empty($filter['is_disconnected'])){
      $sql->whereRaw("in_time < NOW() - INTERVAL 15 MINUTE");
    }

    $sql->where('row_status','A');

    $sql->whereNotNull('search_type_by_pro');

    $sql->groupBy('st_code')->groupBy('ac_number')->groupBy('ps_no_ag');

    return $sql->get();

  }

  public static function get_voter_count($filter = array()){

    //$sql = SpmVoterInfo::selectRaw("count(case when gender='M' then 1 end) as male, count(case when gender='F' then 1 end) as female, count(case when gender='O' then 1 end) as other, count(gender) as total, ps_no_ag as ps_no, ac_number as ac_no, st_code");

    $sql = SpmVoterInfo::select("ps_no, ac_no, state_code as st_code");

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_number',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no_ag',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereNull('gender');
      }else{
        $sql->where('gender',$filter['gender']);
      }
    }

    if(!empty($filter['is_connected'])){
      $sql->whereRaw("in_time > NOW() - INTERVAL 15 MINUTE");
    }

    if(!empty($filter['is_disconnected'])){
      $sql->whereRaw("in_time < NOW() - INTERVAL 15 MINUTE");
    }

    if(!empty($filter['is_queue'])){
      $sql->whereNotNull("in_time")->whereNull("out_time");
    }else {
      $sql->whereNotNull('search_type_by_pro');
    }
	
	if(!empty($filter['time_between'])){
      $in_time = date('H:i:s', strtotime("-30 minutes", strtotime($filter['time_between'])));
      $out_time = date('H:i:s', strtotime($filter['time_between']));
      $sql->whereRaw("time(out_time) between '".$in_time."' and '".$out_time."' AND DATE(out_time) = '2019-09-23'");
    }

    $sql->where('row_status','A');

    return $sql->count(DB::raw('DISTINCT st_code, ac_number, ps_no_ag, unique_generated_id'));

  }

  public static function get_elector_by_age($filter = array()){
    $sql = SpmVoterInfo::select("ps_no, ac_no, state_code as st_code");

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_number',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no_ag',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereNull('gender');
      }else{
        $sql->where('gender',$filter['gender']);
      }
    }

    if(!empty($filter['is_connected'])){
      $sql->whereRaw("in_time > NOW() - INTERVAL 15 MINUTE");
    }

    if(!empty($filter['is_disconnected'])){
      $sql->whereRaw("in_time < NOW() - INTERVAL 15 MINUTE");
    }

    if(!empty($filter['is_queue'])){
      $sql->whereNotNull("in_time")->whereNull("out_time")->where("user_status",'1');
    }

    if(!empty($filter['age_between'])){
      $sql->whereBetween('age',explode('-',$filter['age_between']));
    }

    $sql->whereNotNull('search_type_by_pro');
    
    $sql->where('row_status','A');

    return $sql->count(DB::raw('DISTINCT st_code, ac_number, ps_no_ag, unique_generated_id'));
  }

  public static function get_average_time($filter = array()){

    // $sql = SpmVoterInfo::selectRaw("avg(TIMESTAMPDIFF(SECOND, in_time, out_time)) as average_time");
    // if(!empty($filter['st_code'])){
    //   $sql->where('st_code',$filter['st_code']);
    // }
    // if(!empty($filter['ac_no'])){
    //   $sql->where('ac_number',$filter['ac_no']);
    // }
    // if(!empty($filter['ps_no'])){
    //   $sql->where('ps_no_ag',$filter['ps_no']);
    // }
    // $sql->where('row_status','A');
    // $sql->whereNotNull('out_time');
    // $query = $sql->first();
    // if(!$query){
    //   return 0;
    // }

    $sql = SpmVoterInfo::select("out_time");
    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }
    if(!empty($filter['ac_no'])){
      $sql->where('ac_number',$filter['ac_no']);
    }
    if(!empty($filter['ps_no'])){
      $sql->where('ps_no_ag',$filter['ps_no']);
    }
    $sql->where('row_status','A');
    $sql->whereNotNull('out_time')->orderBy("out_time","DESC");
    $query = $sql->first();
    if(!$query){
      return 0;
    }

    $sql_15 = SpmVoterInfo::select("out_time");
    if(!empty($filter['st_code'])){
      $sql_15->where('st_code',$filter['st_code']);
    }
    if(!empty($filter['ac_no'])){
      $sql_15->where('ac_number',$filter['ac_no']);
    }
    if(!empty($filter['ps_no'])){
      $sql_15->where('ps_no_ag',$filter['ps_no']);
    }
    $sql_15->where('row_status','A');
    $sql_15->whereRaw("time(out_time) < '".date('H:i:s', strtotime($query->out_time))."'");
    $sql_15->whereNotNull('out_time')->orderBy("out_time","DESC");
    $query_15 = $sql_15->first();
    if(!$query_15 && !$query){
      return 0;
    }

    $difference_time = 0;
    $time = strtotime($query->out_time) - strtotime($query_15->out_time);
    return $time;

  }
  
  //time interval
  public static function half_hour_times() {
    $start_time = '07:00';
    $end_time   = date('H:i',strtotime("-30 minutes"));
	
	if( date('Y-m-d') == '2019-09-23'){
      $end_time   = date('H:i',strtotime("-30 minutes"));
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
  
  
  public static function get_scan_count_from_poll_table($filter = array()){

    $sql = SpmVoterInfo::selectRaw("count(case when search_type_by_pro = '1' then 1 end) as total_qr, count(case when search_type_by_pro = '2' then 1 end) as total_epic, count(case when search_type_by_pro = '3' then 1 end) as total_bs,count(case when search_type_by_pro = '4' then 1 end) as total_name");

    if(!empty($filter['st_code'])){
      $sql->where('st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('ac_number',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('ps_no_ag',$filter['ps_no']);
    }

    $sql->where('row_status','A');

    $sql->groupBy('st_code')->groupBy('ac_number');

    $query = $sql->first();
    if(!$query){
      return false;
    }
    return $query->toArray();
  }

}