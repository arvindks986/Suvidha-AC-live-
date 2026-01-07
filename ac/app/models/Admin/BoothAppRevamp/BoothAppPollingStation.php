<?php namespace App\models\Admin\BoothAppRevamp;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Database\Eloquent\Model;
use DB, Cache;

class BoothAppPollingStation extends Model
{
  protected $table = 'boothapp_polling_station';
  
  public static function get_assign_officers($data = array()){

    $sql = BoothAppPollingStation::join("m_election_details",[
      ["m_election_details.ST_CODE","=","boothapp_polling_station.ST_CODE"],
      ["m_election_details.CONST_NO","=","boothapp_polling_station.AC_no"],
    ])->join("polling_station_officer",[
      ["boothapp_polling_station.ST_CODE","=","polling_station_officer.st_code"],
      ["boothapp_polling_station.AC_No","=","polling_station_officer.ac_no"],
      ["boothapp_polling_station.PS_NO","=","polling_station_officer.ps_no"],
    ])->selectRaw("id, PS_NAME_EN as ps_name, name, mobile_number, role_id, polling_station_officer.st_code, polling_station_officer.district_no, polling_station_officer.ac_no, polling_station_officer.ps_no, role_level, is_active");

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($data['st_code'])){
      $sql->where('polling_station_officer.st_code',$data['st_code']);
    }

    if(!empty($data['ac_no'])){
      $sql->where('polling_station_officer.ac_no',$data['ac_no']);
    }

    if(!empty($data['ps_no'])){
      $sql->where('polling_station_officer.ps_no',$data['ps_no']);
    }

    if(!empty($data['mobile'])){
      $sql->where('polling_station_officer.mobile_number',$data['mobile']);
    }

    if(!empty($data['role_id'])){
      $sql->where('polling_station_officer.role_id',$data['role_id']);
    }

    $sql->orderByRaw("boothapp_polling_station.ST_CODE, boothapp_polling_station.AC_No, CONVERT(boothapp_polling_station.PS_NO, INT) ASC");

    $sql->groupBy(['boothapp_polling_station.ST_CODE','boothapp_polling_station.AC_No','boothapp_polling_station.PS_NO']);

    if(!empty($data['paginate']) && !empty($data['limit']) && $data['limit']>0){
        return $sql->paginate($data['limit']);
    }else{
        return $sql->get()->toArray();
    }
  } 

}