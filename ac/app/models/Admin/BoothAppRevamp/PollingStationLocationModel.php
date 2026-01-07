<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;

class PollingStationLocationModel extends Model
{
  protected $table = 'polling_station_location';

  public static function get_locations($filter = array()){
    $sql = PollingStationLocationModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_station_location.st_code"],
      ["m_election_details.CONST_NO","=","polling_station_location.ac_no"],
    ])->selectRaw('*');

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('polling_station_location.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('polling_station_location.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('polling_station_location.ac_no',$filter['ac_no']);
    }

    return $sql->get();
  }

  public static function total_location_count($filter = array()){
    $total = [
      'total' => 0,
      'mapped' => 0,
      'unmapped' => 0,
    ];
    $sql = PollingStationLocationModel::join("m_election_details",[
      ["m_election_details.ST_CODE","=","polling_station_location.st_code"],
      ["m_election_details.CONST_NO","=","polling_station_location.ac_no"],
    ])->leftjoin("polling_station_officer as pso",[
      ["pso.st_code","=","polling_station_location.st_code"],
      ["pso.ac_no","=","polling_station_location.ac_no"],
    ])->selectRaw("COUNT(DISTINCT polling_station_location.id) as total, COUNT(DISTINCT (IF(pso.location_id>0, pso.location_id, NULL))) as mapped");

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('polling_station_location.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('polling_station_location.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('polling_station_location.ac_no',$filter['ac_no']);
    }
    $result = $sql->first();
    if($result){
      $total = [
        'total'   => $result->total,
        'mapped'  => $result->mapped,
        'unmapped' => $result->total -  $result->mapped
      ];
    }
    return $total;
  }

  public static function add_location($data = array()){
    if(!empty($data['id'])){
      $officer = PollingStationLocationModel::find($data['id']);
    }else{
      $officer = new PollingStationLocationModel();
    }

    $officer->name = $data['name'];
    $officer->st_code = $data['st_code'];
    $officer->ac_no       = $data['ac_no'];
    $officer->district_no = $data['dist_no'];
    $officer->ps_no       = $data['ps_no_string'];
    $officer->created_by  = \Auth::id();
    if($officer->save()){
      return $officer;
    }
    return false;
  }


  public static function get_unmapped_ps($filter = array()){
    $total  = 0;
    $sql = \DB::table("boothapp_polling_station as p")->join("m_election_details",[
      ["m_election_details.ST_CODE","=","p.ST_CODE"],
      ["m_election_details.CONST_NO","=","p.AC_NO"],
    ])->leftjoin("polling_station_location_to_ps as pl",[
      ['p.ST_CODE','=','pl.st_code'],
      ['p.AC_NO','=','pl.ac_no'],
	  ['p.PS_NO','=','pl.ps_no'],
    ])->join('boothapp_enable_acs', [
            ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code'],
            ['m_election_details.CONST_NO','=','boothapp_enable_acs.ac_no'],
        ])->selectRaw("COUNT(IF(pl.ps_no IS NULL, 1, NULL)) as total");

    $sql->where('CONST_TYPE','AC');

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('boothapp_enable_acs.st_code',$filter['st_code']);
    }

    if(!empty($filter['dist_no'])){
      $sql->where('boothapp_enable_acs.dist_no',$filter['dist_no']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('boothapp_enable_acs.ac_no',$filter['ac_no']);
    }

    $query = $sql->first();
    if($query){
      $total = $query->total;
    }
    return $total;
  }

}