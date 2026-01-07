<?php

namespace App\models\Admin\polling_station;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollingStationModel extends Model
{

  protected $table = 'polling_station';

  public static function get_ps_finalize_ceo_data($data = array())
  {

    $role_id = Auth::user()->role_id;

    $sql = DB::table('polling_station as ps');
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }


    $sql->where("ps_finalize", 0);



    if ($sql->count() > 0) {
      return 1;
    } else {
      return 0;
    }
  }

  public static function get_ps_finalize_data_ro($data = array())
  {
    $role_id = Auth::user()->role_id;

    $sql = DB::table('polling_station as ps');
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }
    $sql->select("ro_ps_finalize");
    $query = $sql->get();
    return $query;
  }
  public static function get_ps_finalize_data_deo($data = array())
  {
    $role_id = Auth::user()->role_id;

    $sql = DB::table('polling_station as ps');
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }
    $sql->select("deo_ps_finalize");
    $query = $sql->get();
    return $query;
  }

  public static function get_ps_finalize_data_ceo($data = array())
  {
    $role_id = Auth::user()->role_id;

    $sql = DB::table('polling_station as ps');
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }
    $sql->select("ps_finalize");
    $query = $sql->get();
    return $query;
  }
  public static function get_ps_finalize_data($data = array())
  {

    $role_id = Auth::user()->role_id;

    $sql = DB::table('polling_station as ps');
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }

    if ($role_id === 4) {
      $sql->where("ps_finalize", 1);
    } else {
      $sql->where("ps_finalize", 0);
    }


    if ($sql->count() > 0) {
      return 1;
    } else {
      return 0;
    }
  }

  //GET POLLING STATION DATA FUNCTION STARTS
  public static function get_ps_data($data = array())
  {

    $sql_raw = "ac.AC_NAME AS acn, ac.AC_NO AS acn,state.ST_NAME AS state_name, ps.*";

    $sql = DB::table('polling_station as ps')
      ->join('m_ac as ac', [
        ['ac.AC_NO', '=', 'ps.AC_NO'],
        ['ac.ST_CODE', '=', 'ps.ST_CODE'],
      ])
      ->leftjoin('m_state as state', [
        ['state.ST_CODE', '=', 'ac.ST_CODE']
      ]);



    $sql->selectRaw($sql_raw);


    //CHECKING STATE CODE
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }

    //CHECKING PHASE
    /*if(!empty($data['phase'])){
          $sql->where("ps.schedule_id", $data['phase']);
    }
*/

    //GROUP BY STARTS
    //$sql->groupBy("ps.AC_NO");
    //GROUP BY ENDS


    //ORDER BY STARTS
    $sql->orderByRaw("ABS(ps.PS_NO) ASC");
    $sql->orderByRaw("ps.PS_TYPE DESC");
    //ORDER BY ENDS


    $query = $sql->get();

    return $query;
  }
  //GET POLLING STATION DATA FUNCTION ends



  //GET POLLING STATION DATA FUNCTION STARTS
  public static function get_ac_data($data = array())
  {

    $sql_raw = "sum(electors_male) as electors_male,sum(electors_female) as electors_female,sum(electors_other) as electors_other,sum(electors_total) as electors_total,sum(voter_male) as voter_male,sum(voter_female) as voter_female,sum(voter_other) as voter_other,sum(voter_total) as voter_total";

    $sql = DB::table('polling_station as ps');

    $sql->selectRaw($sql_raw);


    //CHECKING STATE CODE
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }

    //CHECKING PHASE
    /*if(!empty($data['phase'])){
          $sql->where("ps.schedule_id", $data['phase']);
    }
*/

    //GROUP BY STARTS
    $sql->groupBy("ps.AC_NO");
    //GROUP BY ENDS


    //ORDER BY STARTS
    // $sql->orderByRaw("CONVERT(ps.PS_NO,INT) ASC");
    // $sql->orderByRaw("ps.PS_TYPE DESC");
    //ORDER BY ENDS


    $query = $sql->first();

    return $query;
  }

  public static function getCurrentJobCount()
  {
    $sql = DB::table('jobs')->count();
    return $sql;
  }

  public static function get_ps_data_for_electoral($data = array())
  {

    $sql_raw = "ac.AC_NAME AS acn, ac.AC_NO AS acno,state.ST_NAME AS state_name, ps.*";

    $sql = DB::table('polling_station as ps')
      ->join('m_ac as ac', [
        ['ac.AC_NO', '=', 'ps.AC_NO'],
        ['ac.ST_CODE', '=', 'ps.ST_CODE'],
      ])
      ->leftjoin('m_state as state', [
        ['state.ST_CODE', '=', 'ac.ST_CODE']
      ]);



    $sql->selectRaw($sql_raw);


    //CHECKING STATE CODE
    if (!empty($data['state'])) {
      $sql->where("ps.ST_CODE", $data['state']);
    }

    //CHECKING AC CODE
    if (!empty($data['ac_no'])) {
      $sql->where("ps.AC_NO", $data['ac_no']);
    }

    //CHECKING PHASE
    if (!empty($data['phase'])) {
      $sql->where("ps.scheduleid", $data['phase']);
    }

    //GROUP BY STARTS
    //$sql->groupBy("ps.AC_NO");
    //GROUP BY ENDS


    //ORDER BY STARTS
    $sql->orderByRaw("ps.AC_NO ASC");
    // $sql->orderByRaw("CONVERT(ps.PS_NO,INT) ASC");
    // $sql->orderByRaw("ps.PS_TYPE DESC");
    //ORDER BY ENDS


    $query = $sql->get();

    return $query;
  }
}
