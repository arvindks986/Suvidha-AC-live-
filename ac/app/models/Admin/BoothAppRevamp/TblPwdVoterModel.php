<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB, Cache;
use App\models\Admin\BoothAppRevamp\PollingStation;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class TblPwdVoterModel extends Model
{
  protected $table = 'tbl_voter_pwd as pwd_voter';

  protected $connection = 'booth_revamp_test';

  public static function get_pwd_voters($filter = array()){

    $sql = TblPwdVoterModel::join("tbl_voter_info_poll_status as v","v.epic_no","=","pwd_voter.epic_no")->join("m_election_details",[
      ["m_election_details.ST_CODE","=","v.st_code"],
      ["m_election_details.CONST_NO","=","v.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","pwd_voter.st_code"],
      ["polling_station.AC_NO","=","pwd_voter.ac_no"],
      ["polling_station.PS_NO","=","pwd_voter.ps_no"],
    ])->selectRaw('id');

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('v.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('v.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('v.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereRaw("v.gender is NULL OR v.gender = 'O'");
      }else{
        $sql->where('v.gender',$filter['gender']);
      }
    }

    $sql->whereIn('v.user_type', ['34','35']);

    $sql->where('v.row_status','A');

    return $sql->count(DB::raw('DISTINCT v.epic_no'));
}


public static function get_pwd_electors($filter = array()){

    // $sql = TblPwdVoterModel::join("tbl_voter_info as v","v.epic_no","=","pwd_voter.epic_no")->join("m_election_details",[
    //   ["m_election_details.ST_CODE","=","v.st_code"],
    //   ["m_election_details.CONST_NO","=","v.ac_no"],
    // ])->selectRaw('id');

    // $sql->where('CONST_TYPE','AC');

    // if(!empty($filter['phase_no'])){
    //   $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    // }

    $sql = TblPwdVoterModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","pwd_voter.st_code"],
      ["m_election_details.CONST_NO","=","pwd_voter.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","pwd_voter.st_code"],
      ["polling_station.AC_NO","=","pwd_voter.ac_no"],
      ["polling_station.PS_NO","=","pwd_voter.ps_no"],
    ])->join("tbl_voter_info as v","v.epic_no","=","pwd_voter.epic_no");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('pwd_voter.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('pwd_voter.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('pwd_voter.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['gender'])){
      if($filter['gender'] == 'O'){
        $sql->whereRaw("v.gender is NULL OR v.gender = 'O'");
      }else{
        $sql->where('v.gender',$filter['gender']);
      }
    }

    $sql->where('pwd_voter.row_status','A');

    return $sql->count(DB::raw('DISTINCT pwd_voter.epic_no'));
}


}