<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class PollingStation extends Model
{

  protected $primaryKey = 'CCODE';
  protected $table = 'polling_station';

  public $timestamps = false;

  protected $fillable  = [
    'ST_CODE',
    'AC_NO',
    'election_id',
    'scheduleid',
    'PART_NO',
    'PS_NO',
    'PART_NAME',
    'PS_NAME_EN',
    'PS_TYPE',
    'PS_CATEGORY',
    'LOCN_TYPE',
    'electors_male',
    'electors_female',
    'electors_other',
    'electors_total',
    'electors_finalize_by_ro',
    'electors_finalize_by_ro_date',
  ];

  public static function get_polling_stations($data = array())
  {

    $sql = PollingStation::select('*');

    if (!empty($data['st_code'])) {
      $sql->where('polling_station.ST_CODE', $data['st_code']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where('polling_station.AC_NO', $data['ac_no']);
    }

    if (!empty($data['restricted_ps'])) {
      $sql->whereIn('polling_station.PS_NO', $data['restricted_ps']);
    }

    $sql->orderByRaw("polling_station.ST_CODE, polling_station.AC_NO, polling_station.PS_NO ASC");

    if (!empty($data['paginate'])) {
      return $sql->paginate(100);
    } else {
      return $sql->get();
    }
  }

  public static function get_polling_station($data = array())
  {

    $sql = PollingStation::select('*');

    if (!empty($data['st_code'])) {
      $sql->where('polling_station.ST_CODE', $data['st_code']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where('polling_station.AC_NO', $data['ac_no']);
    }

    if (!empty($data['ps_no'])) {
      $sql->where('polling_station.PS_NO', $data['ps_no']);
    }

    if (!empty($data['restricted_ps'])) {
      $sql->whereIn('polling_station.PS_NO', $data['restricted_ps']);
    }

    $query = $sql->first();
    if (!$query) {
      return false;
    }
    return $query->toArray();
  }

  public static function getAcPollingStationCount($st_code, $ac_no)
  {
    return PollingStation::where('ST_CODE', $st_code)->where('AC_NO', $ac_no)->count();
  }

  public static function getAcPollingStationFinalizedCount($st_code, $ac_no)
  {
    return PollingStation::where('ST_CODE', $st_code)->where('AC_NO', $ac_no)->where('electors_finalize_by_ro', 1)->count();
  }

  public static function getAcPollingStationEnableForEditCount($st_code, $ac_no)
  {
    return PollingStation::where('ST_CODE', $st_code)->where('AC_NO', $ac_no)->where('electors_enable_edit_by_eci', 1)->count();
  }
}
