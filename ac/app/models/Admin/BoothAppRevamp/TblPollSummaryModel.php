<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class TblPollSummaryModel extends Model
{
  protected $table = 'tbl_poll_summary';

  protected $connection = 'booth_revamp_test';
  

  public static function total_statics_count($filter = array()){

    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_poll_summary.st_code"],
      ["polling_station.AC_NO","=","tbl_poll_summary.ac_no"],
      ["polling_station.PS_NO","=","tbl_poll_summary.ps_no"],
    ])->select("id");

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_poll_summary.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('tbl_poll_summary.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_poll_summary.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_poll_summary.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['is_started'])){
      $sql->whereNotNull('tbl_poll_summary.poll_start_datetime');
    }

    if(!empty($filter['is_connected'])){
      $sql->whereNotNull('tbl_poll_summary.poll_start_datetime')->whereNull('tbl_poll_summary.poll_end_datetime');
    }



    if(!empty($filter['is_end'])){
      $sql->whereNotNull('tbl_poll_summary.poll_start_datetime')->whereNotNull('tbl_poll_summary.poll_end_datetime');
    }

    if(!empty($filter['role_id'])){
      $sql->where('tbl_poll_summary.role_id',$filter['role_id']);
    }

    $sql->where('tbl_poll_summary.row_status','A');
    return $sql->count("id");

  }

  public static function get_voter_count($filter = array()){

    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_poll_summary.st_code"],
      ["polling_station.AC_NO","=","tbl_poll_summary.ac_no"],
      ["polling_station.PS_NO","=","tbl_poll_summary.ps_no"],
    ])->selectRaw("IFNULL(SUM(total_male_turn_out),0) as male, IFNULL(SUM(total_female_turn_out),0) as female,
                  IFNULL(SUM(total_other_turn_out),0) as other");

    $sql->where("polling_station.booth_app_excp", 0);

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_poll_summary.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_poll_summary.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_poll_summary.ps_no',$filter['ps_no']);
    }

   

    $sql->where('tbl_poll_summary.row_status','A');

	$query =  $sql->first();
    if(!$query){
      return false;
    }
    return $query->toArray();

    

  }

  //sum total stats
  public static function get_poll_summary($filter = array()){

    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_poll_summary.st_code"],
      ["polling_station.AC_NO","=","tbl_poll_summary.ac_no"],
      ["polling_station.PS_NO","=","tbl_poll_summary.ps_no"],
    ])->where('CONST_TYPE','AC')->selectRaw("tbl_poll_summary.id, tbl_poll_summary.st_code, tbl_poll_summary.pc_no, tbl_poll_summary.ac_no, tbl_poll_summary.ps_no, tbl_poll_summary.dist_no, poll_start_datetime, electors, pro_turn_out, blo_turn_out, total_turn_out, total_male_turn_out, total_female_turn_out, total_other_turn_out, scan_qr, scan_srno, scan_epicno, scan_name, scan_mobile, poll_end_datetime");

    $sql->where("polling_station.booth_app_excp", 0);


    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_poll_summary.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('tbl_poll_summary.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_poll_summary.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_poll_summary.ps_no',$filter['ps_no']);
    }

    $sql->where('tbl_poll_summary.row_status','A');

    $query =  $sql->first();
    if(!$query){
      return false;
    }
    return $query->toArray();
  }

  //sum total stats
  public static function total_statics_sum($filter = array()){
    $data_stats = [
      'pro_turn_out'  => 0,
      'queue_voters'  => 0,
      'male_voters'   => 0,
      'female_voters' => 0,
      'other_voters'  => 0,
      'scan_qr'       => 0,
      'scan_srno'     => 0,
      'scan_epicno'   => 0,
      'scan_name'     => 0,
      'scan_mobile'   => 0,
    ];

    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_poll_summary.st_code"],
      ["polling_station.AC_NO","=","tbl_poll_summary.ac_no"],
      ["polling_station.PS_NO","=","tbl_poll_summary.ps_no"],
    ])->selectRaw("IFNULL(SUM(pro_turn_out),0) as pro_turn_out, (IFNULL(SUM(blo_turn_out),0) - IFNULL(SUM(pro_turn_out),0)) as queue_voters, IFNULL(SUM(total_male_turn_out),0) as male_voters, IFNULL(SUM(total_female_turn_out),0) as female_voters, IFNULL(SUM(total_other_turn_out),0) as other_voters, IFNULL(SUM(scan_qr),0) as scan_qr, IFNULL(SUM(scan_srno),0) as scan_srno, IFNULL(SUM(scan_epicno),0) as scan_epicno, IFNULL(SUM(scan_name),0) as scan_name, IFNULL(SUM(scan_mobile),0) as scan_mobile");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_poll_summary.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('tbl_poll_summary.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_poll_summary.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_poll_summary.ps_no',$filter['ps_no']);
    }

    $sql->where('tbl_poll_summary.row_status','A');

    $query =  $sql->first();
    if($query){
      $data_stats = [
        'pro_turn_out'  => $query->pro_turn_out,
        'queue_voters'  => $query->queue_voters,
        'male_voters'   => $query->male_voters,
        'female_voters' => $query->female_voters,
        'other_voters'  => $query->other_voters,
        'scan_qr'       => $query->scan_qr,
        'scan_srno'     => $query->scan_srno,
        'scan_epicno'   => $query->scan_epicno,
        'scan_name'     => $query->scan_name,
        'scan_mobile'   => $query->scan_mobile,
      ];
    }

    return $data_stats;
  }

  //get voters for turn out report
  public static function get_voters_for_turnout($filter = array()){

    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_poll_summary.st_code"],
      ["polling_station.AC_NO","=","tbl_poll_summary.ac_no"],
      ["polling_station.PS_NO","=","tbl_poll_summary.ps_no"],
    ])->selectRaw("IFNULL(SUM(pro_turn_out),0) as voter, tbl_poll_summary.st_code, tbl_poll_summary.ac_no");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    $sql->where('tbl_poll_summary.row_status','A');

    return $sql->groupBy(['tbl_poll_summary.st_code','tbl_poll_summary.ac_no'])->get()->toArray();

  }


  //BLO and PRO turnout
  //sum total stats
  public static function total_blo_pro_turnout_statics($filter = array()){
    $data_stats = [
      'pro_turn_out'  => 0,
      'blo_turn_out'  => 0
    ];

    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_poll_summary.st_code"],
      ["polling_station.AC_NO","=","tbl_poll_summary.ac_no"],
      ["polling_station.PS_NO","=","tbl_poll_summary.ps_no"],
    ])->selectRaw("IFNULL(SUM(pro_turn_out),0) as pro_turn_out, (IFNULL(SUM(blo_turn_out),0)) as blo_turn_out");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_poll_summary.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('tbl_poll_summary.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_poll_summary.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_poll_summary.ps_no',$filter['ps_no']);
    }

    $sql->where('tbl_poll_summary.row_status','A');

    $query =  $sql->first();
    if($query){
      $data_stats = [
        'pro_turn_out'  => $query->pro_turn_out,
        'blo_turn_out'  => $query->blo_turn_out
      ];
    }


    return $data_stats;
  }

  public static function get_aggregate_pro_diary($filter = array()){

    $data = [
      'poll_start_datetime' => 0,
      'electors' => 0,
      'pro_turn_out' => 0,
      'blo_turn_out' => 0,
      'total_turn_out' => 0,
      'total_male_turn_out' => 0,
      'total_female_turn_out' => 0,
      'total_other_turn_out' => 0,
      'scan_qr' => 0,
      'scan_srno' => 0,
      'scan_epicno' => 0,
      'scan_name' => 0,
      'scan_mobile' => 0,
      'aver_scan_qr' => 0,
      'aver_scan_srno' => 0,
      'aver_scan_epic' => 0,
      'aver_scan_name' => 0,
      'aver_scan_mobile' => 0,
      'scan_average_time' => 0,
      'poll_end_datetime' => 0,
      'no_of_vote' => 0,
      'no_of_vote_evm' => 0,
      'no_of_agent' => 0,
      'no_of_edc' => 0,
      'no_of_overseas' => 0,
      'no_of_proxy' => 0,
      'no_of_tendered' => 0
    ];

    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.st_code","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_poll_summary.st_code"],
      ["polling_station.AC_NO","=","tbl_poll_summary.ac_no"],
      ["polling_station.PS_NO","=","tbl_poll_summary.ps_no"],
    ])->leftjoin("pro_diary_final",[
      ["pro_diary_final.st_code","=","tbl_poll_summary.st_code"],
      ["pro_diary_final.ac_no","=","tbl_poll_summary.ac_no"],
      ["pro_diary_final.ps_no","=","tbl_poll_summary.ps_no"]
    ])->selectRaw("poll_start_datetime, electors, pro_turn_out, blo_turn_out, total_turn_out, total_male_turn_out, total_female_turn_out, total_other_turn_out, scan_qr, scan_srno, scan_epicno, scan_name, scan_mobile, aver_scan_qr, aver_scan_srno, aver_scan_epic, aver_scan_name, aver_scan_mobile, scan_average_time, poll_end_datetime, IFNULL(SUM(no_of_elector),0) as no_of_vote, IFNULL(SUM(total_votes_recorded),0) as no_of_vote_evm, IFNULL(SUM(total_polling_agents),0) as no_of_agent, IFNULL(SUM(total_voted_on_duty_certificate),0) as no_of_edc, IFNULL(SUM(no_of_overseas_voted_electors),0) as no_of_overseas, IFNULL(SUM(no_of_voters_proxy),0) as no_of_proxy, IFNULL(SUM(no_of_tenderd_votes),0) as no_of_tendered");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['st_code'])){
      $sql->where('tbl_poll_summary.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_poll_summary.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_poll_summary.ps_no',$filter['ps_no']);
    }

    $sql->where('tbl_poll_summary.row_status','A');

    $query = $sql->first();

    if($query){
      $data = $query->toArray();
    }
    return $data;
  }

 public static function get_voters_for_suvidha_update($filter = array()){

    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_poll_summary.st_code"],
      ["polling_station.AC_NO","=","tbl_poll_summary.ac_no"],
      ["polling_station.PS_NO","=","tbl_poll_summary.ps_no"],
    ])->join("m_schedule","m_schedule.SCHEDULEID","=","m_election_details.ScheduleID")->selectRaw("IFNULL(SUM(pro_turn_out),0) as voter, tbl_poll_summary.st_code, tbl_poll_summary.ac_no");

    $sql->where('CONST_TYPE','AC');


    $sql->where("polling_station.booth_app_excp", 0);

    //$date_poll = date('Y-m-d');

    //$sql->where('DATE_POLL', $date_poll);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    $sql->where('tbl_poll_summary.row_status','A');

    return $sql->groupBy(['tbl_poll_summary.st_code','tbl_poll_summary.ac_no'])->get()->toArray();

  }


  //BLO and PRO turnout
  //sum total stats
  public static function total_blo_pro_zero($filter = array()){

    $sql = TblPollSummaryModel::join('polling_station',[
      ["polling_station.st_code","=","tbl_poll_summary.st_code"],
      ["polling_station.ac_no","=","tbl_poll_summary.ac_no"],
      ["polling_station.ps_no","=","tbl_poll_summary.ps_no"],
    ])->join("m_election_details",[
      ["m_election_details.st_code","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->selectRaw("polling_station.ST_CODE,polling_station.AC_NO,polling_station.PS_NO,polling_station.PS_NAME_EN,IFNULL(SUM(pro_turn_out),0) as pro_turn_out, (IFNULL(SUM(blo_turn_out),0)) as blo_turn_out");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_poll_summary.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('tbl_poll_summary.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_poll_summary.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_poll_summary.ps_no',$filter['ps_no']);
    }

	if(!empty($filter['is_vote'])){
		if($filter['is_vote'] == '1'){
			$sql->where('tbl_poll_summary.blo_turn_out','0');
		}else if($filter['is_vote'] == '2'){
			$sql->where('tbl_poll_summary.pro_turn_out','0');
		}else if($filter['is_vote'] == '3'){
			$sql->where([['tbl_poll_summary.blo_turn_out','0'],['tbl_poll_summary.pro_turn_out','0']]);
		}
    }


    $sql->where('tbl_poll_summary.row_status','A');
    $sql->groupBy('tbl_poll_summary.st_code', 'tbl_poll_summary.ac_no','tbl_poll_summary.ps_no');
	$sql->orderByRaw('tbl_poll_summary.st_code', 'tbl_poll_summary.ac_no','tbl_poll_summary.ps_no');

    $query =  $sql->get()->toArray();



    return $query;
  }

}
