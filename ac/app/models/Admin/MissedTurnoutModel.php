<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\models\Admin\StateModel;

class MissedTurnoutModel extends Model
{
  protected $table = 'pd_scheduledetail';

  public static function get_reports($data = array())
  {
    $sql = DB::table('pd_scheduledetail as sd1')
      ->join('pd_schedulemaster as sm1', [
        ['sd1.pd_scheduleid', '=', 'sm1.pd_scheduleid']
      ])
      ->join('m_ac as ac', [
        ['ac.AC_NO', '=', 'sd1.ac_no'],
        ['ac.ST_CODE', '=', 'sd1.st_code'],
      ])
      ->join('m_election_details as m_e', [
        ['m_e.CONST_NO', '=', 'ac.AC_NO'],
        ['m_e.ST_CODE', '=', 'sd1.st_code'],
      ])
      ->leftjoin('m_state as state', [
        ['state.ST_CODE', '=', 'ac.ST_CODE']
      ])
      ->leftjoin('officer_login as officer', [
        ['officer.st_code', '=', 'sd1.st_code'],
        ['officer.ac_no', '=', 'sd1.ac_no'],
      ])
      ->select('est_turnout_round1', 'est_turnout_round2', 'est_turnout_round3', 'est_turnout_round4', 'est_turnout_round5', 'close_of_poll', 'sd1.st_code', 'sd1.ac_no', 'ac.AC_NAME', 'officer.name', 'officer.Phone_no', 'missed_status_round1', 'missed_status_round2', 'missed_status_round3', 'missed_status_round4', 'missed_status_round5', 'missed_status_round6', 'modification_status_round1', 'modification_status_round2', 'modification_status_round3', 'modification_status_round4', 'modification_status_round5', 'modification_status_round6', 'est_poll_close', 'est_turnout_total');

    $sql->where("m_e.CONST_TYPE", "AC");

    if (!empty($data['state'])) {
      $sql->where("sd1.st_code", $data['state']);
    }

    if (!empty($data['election_type'])) {
      $sql->where('m_e.ELECTION_TYPEID', $data['election_type']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sm1.state_phase_no", $data['phase']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where("sm1.ac_no", $data['ac_no']);
    }

    if (!empty($data['round']) && $data['level'] == 'ceo') {

      if ($data['round'] == 1) {
        $sql->where("sd1.est_turnout_round1", 0);
      }
      if ($data['round'] == 2) {
        $sql->where("sd1.est_turnout_round2", 0);
      }
      if ($data['round'] == 3) {
        $sql->where("sd1.est_turnout_round3", 0);
      }
      if ($data['round'] == 4) {
        $sql->where("sd1.est_turnout_round4", 0);
      }
      if ($data['round'] == 5) {
        $sql->where("sd1.est_turnout_round5", 0);
      }
      if ($data['round'] == 6) {
        $sql->where("sd1.close_of_poll", 0);
      }
    }

    $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");

    $sql->orderByRaw("state.ST_NAME, ac.AC_NO, ac.AC_NAME, sd1.ac_no ASC");

    return $sql->get();
  }

  public static function get_missed_reports($data = array())
  {

    $sql_raw = "IFNULL(ROUND(AVG(est_turnout_round1),2),0) as est_total_round1, IFNULL(ROUND(AVG(est_turnout_round2),2),0) as est_total_round2, IFNULL(ROUND(AVG(est_turnout_round3),2),0) as est_total_round3, IFNULL(ROUND(AVG(est_turnout_round4),2),0) as est_total_round4, IFNULL(ROUND(AVG(est_turnout_round5),2),0) as est_total_round5, IFNULL(ROUND(AVG(close_of_poll),2),0) as close_of_poll, IFNULL(ROUND(AVG(est_turnout_total),2),0) as est_total, COUNT(*) as total_record, ROUND(IFNULL(AVG(est_turnout_total),0),2) as total_percentage, ac.AC_NO as const_no, ac.AC_NAME as const_name, ac.st_code, sd1.ac_no as ac_no, state.ST_NAME as st_name";

    $sql = DB::table('pd_scheduledetail as sd1')
      ->join('pd_schedulemaster as sm1', [
        ['sd1.pd_scheduleid', '=', 'sm1.pd_scheduleid']
      ])
      ->join('m_ac as ac', [
        ['ac.AC_NO', '=', 'sd1.ac_no'],
        ['ac.ST_CODE', '=', 'sd1.st_code'],
      ])
      ->join('m_election_details as m_e', [
        ['m_e.CONST_NO', '=', 'ac.AC_NO'],
        ['m_e.ST_CODE', '=', 'sd1.st_code'],
      ])
      ->leftjoin('m_state as state', [
        ['state.ST_CODE', '=', 'ac.ST_CODE']
      ]);

    $sql->selectRaw($sql_raw);

    $sql->where("m_e.CONST_TYPE", "AC");

    if (!empty($data['state'])) {
      $sql->where("sd1.st_code", $data['state']);
    }

    if (!empty($data['election_type'])) {
      $sql->where('m_e.ELECTION_TYPEID', $data['election_type']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sm1.schedule_id", $data['phase']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where("sm1.ac_no", $data['ac_no']);
    }



    if (!empty($data['filter_by'])) {
      if ($data['filter_by'] == 2) {
        $sql->where(function ($sql) {
          $sql->where('est_turnout_round1', 0)->orWhere('est_turnout_round2', 0)->orWhere('est_turnout_round3', 0)->orWhere('est_turnout_round4', 0)->orWhere('est_turnout_round5', 0)->orWhere('close_of_poll', 0);
        });
      }
      if ($data['filter_by'] == 1) {
        $sql->where(function ($sql) {
          $sql->where('est_turnout_round1', '!=', 0)->where('est_turnout_round2', '!=', 0)->where('est_turnout_round3', '!=', 0)->where('est_turnout_round4', '!=', 0)->where('est_turnout_round5', '!=', 0)->where('close_of_poll', '!=', 0);
        });
      }
    }

    if (!empty($data['group_by']) && in_array($data['group_by'], ['ac_no'])) {
      if ($data['group_by'] == 'ac_no') {
        $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
      }
    } else {
      $sql->groupBy("sd1.st_code");
    }

    if (!empty($data['order_by']) && in_array($data['order_by'], ['ac_no'])) {
      if ($data['order_by'] == 'ac_no') {
        $sql->orderByRaw("state.ST_NAME, ac.AC_NO, ac.AC_NAME ASC");
      } else if ($data['order_by'] == 'ac_no') {
        $sql->orderByRaw("state.ST_NAME, ac.AC_NO, acc.AC_NAME, sd1.ac_no ASC");
      }
    } else {
      $sql->orderByRaw("state.ST_NAME, ac.AC_NO, ac.AC_NAME ASC");
    }

    $query = $sql->get();

    return $query;
  }
}
