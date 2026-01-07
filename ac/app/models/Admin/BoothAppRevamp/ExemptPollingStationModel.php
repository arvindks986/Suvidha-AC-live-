<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;

class ExemptPollingStationModel extends Model
{
  protected $table = 'boothapp_exempt_polling_station';

  protected $fillable = ['id', 'st_code', 'district_no', 'ac_no', 'ps_no', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_exempted'];

  public static function add_exemted($data = array()){
    $officer    = ExemptPollingStationModel::firstOrNew([
      'st_code' => $data['st_code'],
      'ac_no'   => $data['ac_no'],
      'district_no' => $data['dist_no'],
      'ps_no'   => $data['ps_no'],
    ]);
    $officer->reason      = $data['reason'];
    $officer->is_exempted = 1;
    $officer->created_by  = \Auth::id();
    if($officer->save()){
      return $officer;
    }
    return false;
  }

  public static function get_exemted($filter = array()){
   $sql = ExemptPollingStationModel::selectRaw('*');

   if(!empty($filter['st_code'])){
    $sql->where('st_code',$filter['st_code']);
  }

  if(!empty($filter['dist_no'])){
    $sql->where('dist_no',$filter['dist_no']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('ps_no',$filter['ps_no']);
  }
  return $sql->get();
}

public static function add_voter_turnout($data = array()){
  $object    = ExemptPollingStationModel::firstOrNew([
    'st_code'     => $data['st_code'],
    'ac_no'       => $data['ac_no'],
    'district_no' => $data['dist_no'],
    'ps_no'       => $data['ps_no'],
  ]);

  if($data['round'] == 1){
    $object->round_1_male   = $data['male'];
    $object->round_1_female = $data['female'];
    $object->round_1_other  = $data['other'];
    $object->round_1_total  = $data['total'];
  }
  if($data['round'] == 2){
    $object->round_2_male   = $data['male'];
    $object->round_2_female = $data['female'];
    $object->round_2_other  = $data['other'];
    $object->round_2_total  = $data['total'];
  }
  if($data['round'] == 3){
    $object->round_3_male   = $data['male'];
    $object->round_3_female = $data['female'];
    $object->round_3_other  = $data['other'];
    $object->round_3_total  = $data['total'];
  }
  if($data['round'] == 4){
    $object->round_4_male   = $data['male'];
    $object->round_4_female = $data['female'];
    $object->round_4_other  = $data['other'];
    $object->round_4_total  = $data['total'];
  }
  if($data['round'] == 5){
    $object->round_5_male   = $data['male'];
    $object->round_5_female = $data['female'];
    $object->round_5_other  = $data['other'];
    $object->round_5_total  = $data['total'];
  }
  if($data['round'] == 0){
    $object->round_male   = $data['male'];
    $object->round_female = $data['female'];
    $object->round_other  = $data['other'];
    $object->round_total  = $data['total'];
  }

  $object->total_male   = $data['male'];
  $object->total_female = $data['female'];
  $object->total_other  = $data['other'];
  $object->total_total  = $data['total'];

  if($object->save()){
    return $object;
  }
  return false;
}

}