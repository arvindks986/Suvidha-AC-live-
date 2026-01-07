<?php namespace App\models\Admin\BoothAppRevamp;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use Illuminate\Database\Eloquent\Model;
use DB, Cache;

class PollingStationExemptWrite extends Model
{
  protected $table = 'polling_station';

  protected $connection = 'booth_revamp_write';

  protected $fillable = ['booth_app_excp'];

  public $timestamps = false;

  public static function add_exemted($filter = array()){
    $object = PollingStationExemptWrite::where('ST_CODE', $filter['st_code'])->where('AC_NO', $filter['ac_no'])->where('PS_NO', $filter['ps_no'])->update([
      'booth_app_excp' => 1
    ]);
  }


}