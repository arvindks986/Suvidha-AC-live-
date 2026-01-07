<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class InfraMapping extends Model
{
  protected $table = 'tbl_infra_mapping';

  protected $connection = 'booth_revamp_test';


  public static function get_infra_mapping($filter = array()){

    $sql = InfraMapping::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_infra_mapping.st_code"],
      ["m_election_details.CONST_NO","=","tbl_infra_mapping.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_infra_mapping.st_code"],
      ["polling_station.AC_NO","=","tbl_infra_mapping.ac_no"],
      ["polling_station.PS_NO","=","tbl_infra_mapping.ps_no"],
    ])->selectRaw("COUNT(IF(start_date_time IS NOT NULL,1,NULL)) AS start_date_time, COUNT(IF(ramp='Y',1,NULL)) AS ramp, COUNT(IF(toilet_facility='Y',1,NULL)) AS toilet_facility, COUNT(IF(exit_door='Y',1,NULL)) AS exit_door, COUNT(IF(furniture='Y',1,NULL)) AS furniture, COUNT(IF(light='Y',1,NULL)) AS light, COUNT(IF(drinking_water='Y',1,NULL)) AS drinking_water, COUNT(IF(indatetime_infra='Y',1,NULL)) AS indatetime_infra");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_infra_mapping.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_infra_mapping.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_infra_mapping.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['role_id'])){
      $sql->where('tbl_infra_mapping.user_type_id',$filter['role_id']);
    }

    $sql->where('tbl_infra_mapping.row_status','A');

    return $sql->first()->toArray();

  }

   public static function get_reached_gis($filter = array()){

    $sql = InfraMapping::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_infra_mapping.st_code"],
      ["m_election_details.CONST_NO","=","tbl_infra_mapping.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_infra_mapping.st_code"],
      ["polling_station.AC_NO","=","tbl_infra_mapping.ac_no"],
      ["polling_station.PS_NO","=","tbl_infra_mapping.ps_no"],
    ])->selectRaw("tbl_infra_mapping.user_id, tbl_infra_mapping.id, tbl_infra_mapping.ps_location, tbl_infra_mapping.ps_no, tbl_infra_mapping.st_code, tbl_infra_mapping.ac_no");

    $sql->where('CONST_TYPE','AC');
    
    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_infra_mapping.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_infra_mapping.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_infra_mapping.ps_no',$filter['ps_no']);
    }

 
    $sql->whereRaw("tbl_infra_mapping.user_type_id = '34' OR tbl_infra_mapping.user_type_id = '35'");
    
    $sql->where('tbl_infra_mapping.row_status','A');

    return $sql->get();

  }

  


}