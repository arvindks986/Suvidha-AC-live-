<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class ProDiaryFinal extends Model
{
  protected $table = 'pro_diary_final';

  protected $connection = 'booth_revamp_test';

  public static function total_material_count($filter = array()){

    $total = [
      'total_submited' => 0,
      'total_received' => 0
    ];

    $sql = ProDiaryFinal::join("m_election_details",[
      ["m_election_details.ST_CODE","=","pro_diary_final.st_code"],
      ["m_election_details.CONST_NO","=","pro_diary_final.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","pro_diary_final.st_code"],
      ["polling_station.AC_NO","=","pro_diary_final.ac_no"],
      ["polling_station.PS_NO","=","pro_diary_final.ps_no"],
    ])->selectRaw("COUNT(IF(poll_material_sub = 'Y',1,NULL)) AS total_submited, COUNT(IF(poll_material_rec = 'Y',1,NULL)) AS total_received");

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

    $query = $sql->where('pro_diary_final.row_status','A')->first();
    if($query){
      $total = [
        'total_submited' => $query->total_submited,
        'total_received' => $query->total_received
      ];
    }

    return $total;

  }



  public static function get_pro_diary($filter = array()){

    $sql = ProDiaryFinal::join("m_election_details",[
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

    $query = $sql->where('pro_diary_final.row_status','A')->first();
    if(!$query){
      return false;
    }

    return $query->toArray();

  }

}