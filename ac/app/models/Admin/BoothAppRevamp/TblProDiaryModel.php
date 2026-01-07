<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class TblProDiaryModel extends Model
{
  protected $table = 'pro_diary_final';

  protected $connection = 'booth_revamp_test';

  public static function get_pro_count($filter = array()){

    $sql = TblProDiaryModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","pro_diary_final.st_code"],
      ["m_election_details.CONST_NO","=","pro_diary_final.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","pro_diary_final.st_code"],
      ["polling_station.AC_NO","=","pro_diary_final.ac_no"],
      ["polling_station.PS_NO","=","pro_diary_final.ps_no"],
    ])->select("pro_diary_final.id");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('pro_diary_final.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('pro_diary_final.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('pro_diary_final.ps_no',$filter['ps_no']);
    }

    $sql->where('pro_diary_final.row_status','A');

    return $sql->count(DB::raw('DISTINCT pro_diary_final.st_code, pro_diary_final.ac_no, pro_diary_final.ps_no'));

  }

  public static function get_pro_diaries($filter = array()){

    $sql = TblProDiaryModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","pro_diary_final.st_code"],
      ["m_election_details.CONST_NO","=","pro_diary_final.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","pro_diary_final.st_code"],
      ["polling_station.AC_NO","=","pro_diary_final.ac_no"],
      ["polling_station.PS_NO","=","pro_diary_final.ps_no"],
    ])->selectRaw("pro_diary_final.*");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('pro_diary_final.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('pro_diary_final.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('pro_diary_final.ps_no',$filter['ps_no']);
    }

    $sql->where('pro_diary_final.row_status','A');

    return $sql->get();

  }

  public static function get_pro_diary($filter = array()){

    $data = [
      'no_of_vote' => 0,
      'no_of_vote_evm' => 0,
    ];

    $sql = TblProDiaryModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","pro_diary_final.st_code"],
      ["m_election_details.CONST_NO","=","pro_diary_final.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","pro_diary_final.st_code"],
      ["polling_station.AC_NO","=","pro_diary_final.ac_no"],
      ["polling_station.PS_NO","=","pro_diary_final.ps_no"],
    ])->selectRaw("IFNULL(SUM(no_of_elector),0) as no_of_vote, IFNULL(SUM(total_votes_recorded),0) as no_of_vote_evm");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('pro_diary_final.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('pro_diary_final.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('pro_diary_final.ps_no',$filter['ps_no']);
    }

    $sql->where('pro_diary_final.row_status','A');

    $query = $sql->first();

    if($query){
      $data = [
        'no_of_vote' => $query->no_of_vote,
        'no_of_vote_evm' => $query->no_of_vote_evm,
      ];
    }
    return $data;
  }
  
  public static function get_po_sum($filter = array()){

    $data = [
      'no_of_vote' => 0
    ];
    $sql = TblPollSummaryModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_poll_summary.st_code"],
      ["m_election_details.CONST_NO","=","tbl_poll_summary.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_poll_summary.st_code"],
      ["polling_station.AC_NO","=","tbl_poll_summary.ac_no"],
      ["polling_station.PS_NO","=","tbl_poll_summary.ps_no"],
    ])->selectRaw("IFNULL(SUM(pro_turn_out),0) as no_of_vote");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

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

    $query = $sql->first();
	//dd($query);

    if($query){
      $data = [
        'no_of_vote' => $query->no_of_vote,
      ];
    }
    return $data;
  }
  
  public static function get_evm_sum($filter = array()){

    $data = [
      'no_of_vote_evm' => 0,
    ];
    $sql = TblProDiaryModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","pro_diary_final.st_code"],
      ["m_election_details.CONST_NO","=","pro_diary_final.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","pro_diary_final.st_code"],
      ["polling_station.AC_NO","=","pro_diary_final.ac_no"],
      ["polling_station.PS_NO","=","pro_diary_final.ps_no"],
    ])->selectRaw("IFNULL(SUM(total_votes_recorded),0) as no_of_vote_evm");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('pro_diary_final.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('pro_diary_final.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('pro_diary_final.ps_no',$filter['ps_no']);
    }

    $sql->where('pro_diary_final.row_status','A');

    $query = $sql->first();
	//dd($query);

    if($query){
      $data = [
        'no_of_vote_evm' => $query->no_of_vote_evm,
      ];
    }
    return $data;
  }

}