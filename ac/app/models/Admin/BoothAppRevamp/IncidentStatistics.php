<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class IncidentStatistics extends Model
{
  protected $table = 'tbl_incident_statistics';

  protected $connection = 'booth_revamp_test';


  public static function get_incident_count($filter = array()){

    $sql = IncidentStatistics::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_incident_statistics.st_code"],
      ["m_election_details.CONST_NO","=","tbl_incident_statistics.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_incident_statistics.st_code"],
      ["polling_station.AC_NO","=","tbl_incident_statistics.ac_no"],
      ["polling_station.PS_NO","=","tbl_incident_statistics.ps_no"],
    ])->select("tbl_incident_statistics.id");
    
    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_incident_statistics.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_incident_statistics.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_incident_statistics.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['role_id'])){
      $sql->where('tbl_incident_statistics.user_type_id',$filter['role_id']);
    }

    $sql->where('tbl_incident_statistics.row_status','A');

    return $sql->count("tbl_incident_statistics.id");

  }

  public static function get_incidents($filter = array()){

    $sql = IncidentStatistics::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_incident_statistics.st_code"],
      ["m_election_details.CONST_NO","=","tbl_incident_statistics.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_incident_statistics.st_code"],
      ["polling_station.AC_NO","=","tbl_incident_statistics.ac_no"],
      ["polling_station.PS_NO","=","tbl_incident_statistics.ps_no"],
    ])->join("tbl_incident","tbl_incident.id","=","tbl_incident_statistics.incident_id")
    ->join("tbl_incident_type","tbl_incident_type.id","=","tbl_incident.incident_type_id")->selectRaw("tbl_incident_statistics.*, incident_detail, incident_type, description, tbl_incident_statistics.created_at");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_incident_statistics.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_incident_statistics.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_incident_statistics.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['role_id'])){
      $sql->where('tbl_incident_statistics.user_type_id',$filter['role_id']);
    }

    $sql->where('tbl_incident_statistics.row_status','A');

    return $sql->get();

  }

  


}