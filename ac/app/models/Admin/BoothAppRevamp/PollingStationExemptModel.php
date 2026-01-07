<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;

class PollingStationExemptModel extends Model
{
  protected $table = 'boothapp_exempt_polling_station';

  public static function add_exemted($data = array()){
    $officer          = new PollingStationExemptModel();
    $officer->name    = $data['name'];
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

  public static function get_exemted($filter = array()){
     $sql = PollingStationExemptModel::join()->selectRaw('*');

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

}