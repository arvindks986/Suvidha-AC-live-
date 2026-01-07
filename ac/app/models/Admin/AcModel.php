<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AcModel extends Model
{
  protected $table = 'm_ac';

  public static function get_record($filter_array = array())
  {
    $sql = AcModel::where('AC_NO', $filter_array['ac_no'])->where('ST_CODE', $filter_array['state'])->select('AC_NAME as ac_name', 'AC_NO as ac_no', 'AC_NAME_V1 as ac_name_v')->first();
    if (!$sql) {
      return '';
    }
    return $sql->toArray();
  }

  public static function get_records($data = array())
  {

    $results = [];
    $election_id = Auth::user()->election_id;

    $sql = AcModel::join('m_election_details', [
      ['m_election_details.ST_CODE', '=', 'm_ac.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_ac.AC_NO']
    ]);

    $sql->where('m_election_details.CONST_TYPE', 'AC');
    $sql->where('m_election_details.election_status', '1');
    $sql->where('m_election_details.ELECTION_ID', $election_id);

    if (!empty($data['state'])) {
      $sql->where('m_election_details.ST_CODE', $data['state']);
    }

    if (!empty($data['st_code'])) {
      $sql->where('m_election_details.ST_CODE', $data['st_code']);
    }

    if (!empty($data['dist_no'])) {
      $sql->where('m_ac.DIST_NO_HDQTR', $data['dist_no']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where('m_ac.AC_NO', $data['ac_no']);
    }

    if (!empty($data['phase'])) {
      $sql->where('m_election_details.PHASE_NO', $data['phase']);
    }

    if (!empty($data['election_type'])) {
      $sql->where('m_election_details.ELECTION_TYPEID', $data['election_type']);
    }

    $query = $sql->select('m_ac.AC_NO as ac_no', 'm_ac.AC_NAME as ac_name', 'm_ac.ST_CODE as st_code', 'm_ac.DIST_NO_HDQTR as dist_no', 'm_ac.CCODE as CCODE')->orderByRaw('m_ac.ST_CODE,m_ac.AC_NAME ASC')->groupBy('m_ac.AC_NO')->get();

    if (count($query) > 0) {
      $results = $query->toArray();
    }

    return $results;
  }




  public static function get_acs()
  {
    $election_id = Auth::user()->election_id;
    $query = AcModel::join('m_election_details', [
      ['m_election_details.ST_CODE', '=', 'm_ac.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_ac.AC_NO']
    ])->where('m_election_details.CONST_TYPE', 'AC')
      ->where('m_election_details.election_status', '1')
      ->where('m_election_details.ELECTION_ID', $election_id)
      ->select('m_ac.AC_NO as ac_no', 'm_ac.AC_NAME as ac_name', 'm_ac.ST_CODE as st_code', 'm_ac.DIST_NO_HDQTR as dist_no')
      ->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')
      ->groupBy('m_ac.AC_NO')
      ->groupBy("m_ac.ST_CODE")
      ->get();
    return $query;
  }

  public static function get_distinct_acs($data = array())
  {

    $results = [];
    $election_id = Auth::user()->election_id;

    $sql = AcModel::join('m_election_details', [
      ['m_election_details.ST_CODE', '=', 'm_ac.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_ac.AC_NO']
    ]);

    $sql->where('m_election_details.CONST_TYPE', 'AC');
    $sql->where('m_election_details.election_status', '1');
    $sql->where('m_election_details.ELECTION_ID', $election_id);

    if (!empty($data['st_code'])) {
      $sql->where('m_election_details.ST_CODE', $data['st_code']);
    }

    if (!empty($data['dist_no'])) {
      $sql->where('m_ac.DIST_NO_HDQTR', $data['dist_no']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where('m_ac.AC_NO', $data['ac_no']);
    }

    if (!empty($data['phase'])) {
      $sql->where('m_election_details.PHASE_NO', $data['phase']);
    }



    $query = $sql->select('m_ac.AC_NO as ac_no', 'm_ac.AC_NAME as ac_name', 'm_ac.ST_CODE as st_code', 'm_ac.DIST_NO_HDQTR as dist_no')->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->groupBy(['m_ac.ST_CODE', 'm_ac.AC_NO'])->get();

    if (count($query) > 0) {
      $results = $query->toArray();
    }

    return $results;
  }


  public static function get_distinct_acs_with_state_name($data = array())
  {

    $results = [];
    $election_id = Auth::user()->election_id;

    $sql = AcModel::join('m_election_details', [
      ['m_election_details.ST_CODE', '=', 'm_ac.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_ac.AC_NO']
    ])->join('m_state', [
      ['m_state.ST_CODE', '=', 'm_ac.ST_CODE'],
    ]);

    $sql->where('m_election_details.CONST_TYPE', 'AC');
    $sql->where('m_election_details.election_status', '1');
    $sql->where('m_election_details.ELECTION_ID', $election_id);

    if (!empty($data['st_code'])) {
      $sql->where('m_election_details.ST_CODE', $data['st_code']);
    }

    if (!empty($data['dist_no'])) {
      $sql->where('m_ac.DIST_NO_HDQTR', $data['dist_no']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where('m_ac.AC_NO', $data['ac_no']);
    }

    if (!empty($data['phase'])) {
      $sql->where('m_election_details.StatePHASE_NO', $data['phase']);
    }



    $query = $sql->select('m_ac.AC_NO as ac_no', 'm_ac.AC_NAME as ac_name', 'm_ac.ST_CODE as st_code', 'm_ac.DIST_NO_HDQTR as dist_no', 'm_state.ST_NAME as state_name')->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->groupBy(['m_ac.ST_CODE', 'm_ac.AC_NO'])->get();

    if (count($query) > 0) {
      $results = $query->toArray();
    }

    return $results;
  }
}
