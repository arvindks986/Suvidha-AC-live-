<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;

class SpmPollingStation extends Model
{
protected $table = 'polling_station';

protected $connection = 'booth_revamp';

public static function get_slip_path($filter = array()){

  $sql = SpmPollingStation::select('slip_path');

  if(!empty($filter['st_code'])){
    $sql->where('st_code',$filter['st_code']);
  }

  if(!empty($filter['ac_no'])){
    $sql->where('ac_no',$filter['ac_no']);
  }

  if(!empty($filter['ps_no'])){
    $sql->where('ps_no',$filter['ps_no']);
  }

  $query = $sql->first();

  if(!$query){
    return false;
  }

  return $query->toArray();

}


}