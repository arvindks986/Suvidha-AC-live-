<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;

class TblBoothUserModel extends Model
{
  protected $table = 'tbl_booth_user';

  protected $connection = 'booth_revamp_test';

  public static function get_download_time($filter = array()){

    $sql = TblBoothUserModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_booth_user.st_code"],
      ["m_election_details.CONST_NO","=","tbl_booth_user.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_booth_user.st_code"],
      ["polling_station.AC_NO","=","tbl_booth_user.ac_no"],
      ["polling_station.PS_NO","=","tbl_booth_user.ps_no"],
    ])->selectRaw("id, st_code, ac_no, ps_no, user_type, download_time");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_booth_user.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_booth_user.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_booth_user.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['role_id'])){
      $sql->where('tbl_booth_user.user_type',$filter['role_id']);
    }

    $sql->where('tbl_booth_user.download_time','>','0');

    $sql->where('tbl_booth_user.row_status','A');

    $sql->select("tbl_booth_user.*");

    return $sql->get();
   
  }

  public static function total_e_download($filter = array()){
    $i = 0;
    $sql = TblBoothUserModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_booth_user.st_code"],
      ["m_election_details.CONST_NO","=","tbl_booth_user.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_booth_user.st_code"],
      ["polling_station.AC_NO","=","tbl_booth_user.ac_no"],
      ["polling_station.PS_NO","=","tbl_booth_user.ps_no"],
    ])->select('id');

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_booth_user.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_booth_user.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_booth_user.ps_no',$filter['ps_no']);
    }
    if(!empty($filter['role_id'])){
      $sql->where('tbl_booth_user.user_type',$filter['role_id']);
    }
    $sql->where('tbl_booth_user.download_time','>','0');
    $sql->where('tbl_booth_user.row_status','A');
    return $sql->count("tbl_booth_user.id");
  }

}