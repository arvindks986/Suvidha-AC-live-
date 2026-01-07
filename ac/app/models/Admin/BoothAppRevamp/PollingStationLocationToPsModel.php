<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;

class PollingStationLocationToPsModel extends Model
{
  protected $table = 'polling_station_location_to_ps';

  protected $fillable = ['ps_no','ac_no','location_id','st_code','created_by'];


  public static function validate_ps($filter = array()){

    $sql = PollingStationLocationToPsModel::select('polling_station_location_to_ps.ps_no')->where('st_code',$filter['st_code'])->where('ac_no',$filter['ac_no'])->whereIn('ps_no',$filter['ps_no'])->where('polling_station_location_to_ps.is_deleted', 0);
    if(!empty($filter['id'])){
      $sql->where('location_id','!=',$filter['id']);
    }
    return $sql->get();

  }

  public static function add_location_to_ps($data = array()){
    $officer                = new PollingStationLocationToPsModel();
    $officer->st_code       = $data['st_code'];
    $officer->ac_no         = $data['ac_no'];
    $officer->ps_no         = $data['ps_no'];
    $officer->location_id   = $data['location_id'];
    $officer->created_by    = $data['created_by'];
    return $officer->save();
  }

  public static function delete_location_to_ps($location_id){
    PollingStationLocationToPsModel::where('location_id', $location_id)->delete();
  }

}