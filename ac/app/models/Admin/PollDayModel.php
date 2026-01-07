<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollDayModel extends Model
{


  public function get_scrutny_report_ceo($data = array())
  {

    $election_id = Auth::user()->election_id;

    $sql = DB::table('m_election_details')->join('m_ac', [
      ['m_election_details.ST_CODE', '=', 'm_ac.ST_CODE'],
      ['m_election_details.CONST_NO', '=', 'm_ac.AC_NO']
    ]);

    if (!empty($data['state_code'])) {
      $sql->where('m_election_details.ST_CODE', $data['state_code']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where('m_election_details.CONST_NO', $data['ac_no']);
    }

    if (!empty($data['const_type'])) {
      $sql->where('m_election_details.CONST_TYPE', $data['const_type']);
    }

    if (!empty($data['phase_id'])) {
      $sql->where('m_election_details.PHASE_NO', $data['phase_id']);
    }

    $sql->where('m_election_details.CONST_TYPE', 'AC');
    $sql->where('m_election_details.election_status', '1');
    $sql->where('m_election_details.ELECTION_ID', $election_id);

    return $sql->orderBy('m_ac.AC_NO', 'ASC')->orderBy('m_ac.AC_NAME', 'ASC')
      ->select('m_election_details.*', 'm_ac.*', 'm_election_details.CONST_NO as CCODE', 'm_election_details.ST_CODE as st_code')->groupBy('m_election_details.CCODE')->get();
  }

  //not delete  
  function get_total_by_state($data = array())
  {
    $election_id = Auth::user()->election_id;
    $result = [
      'round_1_m'              => 0,
      'round_1_f'              => 0,
      'round_1_o'              => 0,
      'round_1_t'              => 0,
      'round_2_m'              => 0,
      'round_2_f'              => 0,
      'round_2_o'              => 0,
      'round_2_t'              => 0,
      'round_3_m'              => 0,
      'round_3_f'              => 0,
      'round_3_o'              => 0,
      'round_3_t'              => 0,
      'round_4_m'              => 0,
      'round_4_f'              => 0,
      'round_4_o'              => 0,
      'round_4_t'              => 0,
      'round_5_m'              => 0,
      'round_5_f'              => 0,
      'round_5_o'              => 0,
      'round_5_t'              => 0,
      'round_end_m'            => 0,
      'round_end_f'            => 0,
      'round_end_o'            => 0,
      'round_end_t'            => 0,
      'total_male'             => 0,
      'total_female'           => 0,
      'total_other'            => 0,
      'total'                  => 0,
    ];
    $sql = "IFNULL(SUM(round1_voter_male),0) as round_1_m, IFNULL(SUM(round1_voter_female),0) as round_1_f, IFNULL(SUM(round1_voter_other),0) as round_1_o, IFNULL(SUM(round1_voter_total),0) as round_1_t, IFNULL(SUM(round2_voter_male),0) as round_2_m, IFNULL(SUM(round2_voter_female),0) as round_2_f, IFNULL(SUM(round2_voter_other),0) as round_2_o, IFNULL(SUM(round2_voter_total),0) as round_2_t, IFNULL(SUM(round3_voter_male),0) as round_3_m, IFNULL(SUM(round3_voter_female),0) as round_3_f, IFNULL(SUM(round3_voter_other),0) as round_3_o, IFNULL(SUM(round3_voter_total),0) as round_3_t, IFNULL(SUM(round4_voter_male),0) as round_4_m, IFNULL(SUM(round4_voter_female),0) as round_4_f, IFNULL(SUM(round4_voter_other),0) as round_4_o, IFNULL(SUM(round4_voter_total),0) as round_4_t, IFNULL(SUM(round5_voter_male),0) as round_5_m, IFNULL(SUM(round5_voter_female),0) as round_5_f, IFNULL(SUM(round5_voter_other),0) as round_5_o, IFNULL(SUM(round5_voter_total),0) as round_5_t, IFNULL(SUM(end_voter_male),0) as round_end_m, IFNULL(SUM(end_voter_female),0) as round_end_f, IFNULL(SUM(end_voter_other),0) as round_end_o, IFNULL(SUM(end_voter_total),0) as round_end_t, IFNULL(SUM(total_male),0) as total_male, IFNULL(SUM(total_female),0) as total_female, IFNULL(SUM(total_other),0) as total_other, IFNULL(SUM(total),0) as total";

    $sql = DB::table('pd_scheduledetail as ps')->join('pd_schedulemaster', 'pd_schedulemaster.pd_scheduleid', '=', 'ps.pd_scheduleid')
      ->join('m_ac', [
        ['m_ac.AC_NO', '=', 'ps.ac_no'],
        ['m_ac.ST_CODE', '=', 'ps.st_code']
      ])
      ->join('m_election_details as e', [
        ['e.ST_CODE', '=', 'ps.st_code'],
        ['e.CONST_NO', '=', 'ps.ac_no']
      ])
      ->selectRaw($sql);
    if (!empty($data['st_code'])) {
      $sql->where('ps.st_code', $data['st_code']);
    }
    if (!empty($data['phase'])) {
      $sql->where('pd_schedulemaster.schedule_id', $data['phase']);
    }
    $sql->where('ps.ac_no', '!=', '0')->where('ps.ac_no', '!=', NULL);
    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);


    $query = $sql->first();

    if ($query) {
      $result = [
        'round_1_m'              => $query->round_1_m,
        'round_1_f'              => $query->round_1_f,
        'round_1_o'              => $query->round_1_o,
        'round_1_t'              => $query->round_1_t,
        'round_2_m'              => $query->round_2_m,
        'round_2_f'              => $query->round_2_f,
        'round_2_o'              => $query->round_2_o,
        'round_2_t'              => $query->round_2_t,
        'round_3_m'              => $query->round_3_m,
        'round_3_f'              => $query->round_3_f,
        'round_3_o'              => $query->round_3_o,
        'round_3_t'              => $query->round_3_t,
        'round_4_m'              => $query->round_4_m,
        'round_4_f'              => $query->round_4_f,
        'round_4_o'              => $query->round_4_o,
        'round_4_t'              => $query->round_4_t,
        'round_5_m'              => $query->round_5_m,
        'round_5_f'              => $query->round_5_f,
        'round_5_o'              => $query->round_5_o,
        'round_5_t'              => $query->round_5_t,
        'round_end_m'            => $query->round_end_m,
        'round_end_f'            => $query->round_end_f,
        'round_end_o'            => $query->round_end_o,
        'round_end_t'            => $query->round_end_t,
        'total_male'             => $query->total_male,
        'total_female'           => $query->total_female,
        'total_other'            => $query->total_other,
        'total'                  => $query->total,
      ];
    }

    return $result;
  }

  public function get_total_round($data = array())
  {


    $election_id = Auth::user()->election_id;


    $total = [
      'round_1_total' => 0,
      'round_2_total' => 0,
      'round_3_total' => 0,
      'round_4_total' => 0,
      'round_5_total' => 0,
      'round_end_total' => 0,
      'total_voter_male'      => 0,
      'total_voter_female'    => 0,
      'total_voter_other'     => 0,
    ];
    $sql = "IFNULL(SUM(round1_voter_male+round2_voter_male+round3_voter_male+round4_voter_male+round5_voter_male+end_voter_male),0) as total_voter_male,IFNULL(SUM(round1_voter_female+round2_voter_female+round3_voter_female+round4_voter_female+round5_voter_female+end_voter_female),0) as total_voter_female, IFNULL(SUM(round1_voter_other+round2_voter_other+round3_voter_other+round4_voter_other+round5_voter_other+end_voter_other),0) as total_voter_other, IFNULL(SUM(round1_voter_total),0) as round_1_total, IFNULL(SUM(round2_voter_total),0) as round_2_total, IFNULL(SUM(round3_voter_total),0) as round_3_total, IFNULL(SUM(round4_voter_total),0) as round_4_total, IFNULL(SUM(round5_voter_total),0) as round_5_total, IFNULL(SUM(end_voter_total),0) as round_end_total, IFNULL(SUM(total_male),0) as total_male, IFNULL(SUM(total_female),0) as total_female, IFNULL(SUM(total_other),0) as total_other, IFNULL(SUM(total),0) as total";
    $sql = DB::table('pd_scheduledetail as ps')->join('pd_schedulemaster', 'pd_schedulemaster.pd_scheduleid', '=', 'ps.pd_scheduleid')
      ->join('m_ac', [
        ['m_ac.AC_NO', '=', 'ps.ac_no'],
        ['m_ac.ST_CODE', '=', 'ps.st_code']
      ])
      ->join('m_election_details as e', [
        ['e.ST_CODE', '=', 'ps.st_code'],
        ['e.CONST_NO', '=', 'ps.ac_no']
      ])
      ->selectRaw($sql);
    if (!empty($data['st_code'])) {
      $sql->where('ps.st_code', $data['st_code']);
    }
    if (!empty($data['const_no'])) {
      $sql->where('ps.ac_no', $data['const_no']);
    }
    if (!empty($data['phase'])) {
      $sql->where('pd_schedulemaster.schedule_id', $data['phase']);
    }
    $sql->where('ps.ac_no', '!=', '0')->where('ps.ac_no', '!=', NULL);
    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);

    $query = $sql->first();
    if ($query) {
      $total = $query;
    }
    return $total;
  }


  public function get_elector_total($data = array())
  {

    $election_id = Auth::user()->election_id;

    $total = [
      'gen_m' => 0,
      'gen_f' => 0,
      'gen_o' => 0,
      'gen_t' => 0,
    ];
    $sql = "IFNULL(SUM(electors_male),0) as gen_m, IFNULL(SUM(electors_female),0) as gen_f, IFNULL(SUM(electors_other),0) as gen_o, IFNULL(SUM(electors_total),0) as gen_t";
    $sql = DB::table('electors_cdac as ed')->join('m_ac', [
      ['m_ac.AC_NO', '=', 'ed.ac_no'],
      ['m_ac.ST_CODE', '=', 'ed.st_code']
    ])
      ->join('m_election_details as e', [
        ['e.ST_CODE', '=', 'ed.st_code'],
        ['e.CONST_NO', '=', 'ed.ac_no']
      ])
      ->selectRaw($sql);
    if (!empty($data['st_code'])) {
      $sql->where('ed.st_code', $data['st_code']);
    }
    if (!empty($data['const_no'])) {
      $sql->where('ed.ac_no', $data['const_no']);
    }
    if (!empty($data['phase'])) {
      $sql->where('ed.scheduledid', $data['phase']);
    }
    $sql->where('ed.ac_no', '!=', '0')->where('ed.ac_no', '!=', NULL);
    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('ed.election_id', $election_id);
    $query = $sql->first();
    if ($query) {
      $total = $query;
    }
    return $total;
  }


  public function get_schedule_detail($data)
  {

    $election_id = Auth::user()->election_id;

    $total = [
      'round_1_m'              => 0,
      'round_1_f'              => 0,
      'round_1_o'              => 0,
      'round_1_t'              => 0,
      'round_2_m'              => 0,
      'round_2_f'              => 0,
      'round_2_o'              => 0,
      'round_2_t'              => 0,
      'round_3_m'              => 0,
      'round_3_f'              => 0,
      'round_3_o'              => 0,
      'round_3_t'              => 0,
      'round_4_m'              => 0,
      'round_4_f'              => 0,
      'round_4_o'              => 0,
      'round_4_t'              => 0,
      'round_5_m'              => 0,
      'round_5_f'              => 0,
      'round_5_o'              => 0,
      'round_5_t'              => 0,
      'round_end_m'            => 0,
      'round_end_f'            => 0,
      'round_end_o'            => 0,
      'round_end_t'            => 0,
      'total_male'            => 0,
      'total_female'          => 0,
      'total_other'           => 0,
      'total'                 => 0,
    ];
    $sql = DB::table('pd_scheduledetail')->join('pd_schedulemaster', 'pd_schedulemaster.pd_scheduleid', '=', 'pd_scheduledetail.pd_scheduleid')
      ->join('m_ac', [
        ['m_ac.AC_NO', '=', 'pd_scheduledetail.ac_no'],
        ['m_ac.ST_CODE', '=', 'pd_scheduledetail.st_code']
      ])
      ->join('m_election_details as e', [
        ['e.ST_CODE', '=', 'pd_scheduledetail.st_code'],
        ['e.CONST_NO', '=', 'pd_scheduledetail.ac_no']
      ]);
    if (!empty($data['st_code'])) {
      $sql->where('pd_scheduledetail.st_code', $data['st_code']);
    }
    if (!empty($data['const_no'])) {
      $sql->where('pd_scheduledetail.ac_no', $data['const_no']);
    }
    if (!empty($data['phase'])) {
      $sql->where('pd_schedulemaster.schedule_id', $data['phase']);
    }
    $sql->where('pd_scheduledetail.ac_no', '!=', '0')->where('pd_scheduledetail.ac_no', '!=', NULL);
    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);

    $query = $sql->first();
    if ($query) {
      $total = [
        'round_1_m'              => $query->round1_voter_male,
        'round_1_f'              => $query->round1_voter_female,
        'round_1_o'              => $query->round1_voter_other,
        'round_1_t'              => $query->round1_voter_total,
        'round_2_m'              => $query->round2_voter_male,
        'round_2_f'              => $query->round2_voter_female,
        'round_2_o'              => $query->round2_voter_other,
        'round_2_t'              => $query->round2_voter_total,
        'round_3_m'              => $query->round3_voter_male,
        'round_3_f'              => $query->round3_voter_female,
        'round_3_o'              => $query->round3_voter_other,
        'round_3_t'              => $query->round3_voter_total,
        'round_4_m'              => $query->round4_voter_male,
        'round_4_f'              => $query->round4_voter_female,
        'round_4_o'              => $query->round4_voter_other,
        'round_4_t'              => $query->round4_voter_total,
        'round_5_m'              => $query->round5_voter_male,
        'round_5_f'              => $query->round5_voter_female,
        'round_5_o'              => $query->round5_voter_other,
        'round_5_t'              => $query->round5_voter_total,
        'round_end_m'            => $query->end_voter_male,
        'round_end_f'            => $query->end_voter_female,
        'round_end_o'            => $query->end_voter_other,
        'round_end_t'            => $query->end_voter_total,
        'total_male'            => $query->total_male,
        'total_female'          => $query->total_female,
        'total_other'           => $query->total_other,
        'total'                 => $query->total,
      ];
    }
    return $total;
  }


  public static function get_reports($data = array())
  {

    $election_id = Auth::user()->election_id;

    $sql_raw = "IFNULL(ROUND(AVG(est_turnout_round1),2),0) as est_total_round1, IFNULL(ROUND(AVG(est_turnout_round2),2),0) as est_total_round2, IFNULL(ROUND(AVG(est_turnout_round3),2),0) as est_total_round3, IFNULL(ROUND(AVG(est_turnout_round4),2),0) as est_total_round4, IFNULL(ROUND(AVG(est_turnout_round5),2),0) as est_total_round5, IFNULL(ROUND(AVG(close_of_poll),2),0) as close_of_poll, IFNULL(ROUND(AVG(est_turnout_total),2),0) as est_total, COUNT(*) as total_record, IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percentage, ac.AC_NO as const_no, ac.AC_NAME as const, ac.st_code, state.ST_NAME as st_name, sd1.ac_no as ac_no, sum(electors_total) as electors_total,sum(est_voters) as est_voters";

    $sql = DB::table('pd_scheduledetail as sd1')
      ->join('pd_schedulemaster as sm1', [
        ['sd1.pd_scheduleid', '=', 'sm1.pd_scheduleid']
      ])
      ->join('m_ac as ac', [
        ['ac.AC_NO', '=', 'sd1.ac_no'],
        ['ac.ST_CODE', '=', 'sd1.st_code'],
      ])
      ->leftjoin('m_state as state', [
        ['state.ST_CODE', '=', 'ac.ST_CODE']
      ])
      ->join('m_election_details as e', [
        ['e.ST_CODE', '=', 'sd1.st_code'],
        ['e.CONST_NO', '=', 'sd1.ac_no']
      ]);

    $sql->selectRaw($sql_raw);


    if (!empty($data['election_type'])) {
      $sql->where('e.ELECTION_TYPEID', $data['election_type']);
    }


    if (!empty($data['state'])) {
      $sql->where("sd1.st_code", $data['state']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sm1.state_phase_no", $data['phase']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where("sm1.ac_no", $data['ac_no']);
    }

    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);

    if (!empty($data['group_by'])) {
      if ($data['group_by'] == 'ac_no') {
        $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
      } else if ($data['group_by'] == 'state') {
        $sql->groupBy("sd1.st_code");
      } else {
      }
    } else {
      $sql->groupBy("sd1.st_code");
    }

    if (!empty($data['order_by'])) {
      if ($data['order_by'] == 'ac_no') {
        $sql->orderByRaw("state.ST_NAME, ac.AC_NO, ac.AC_NAME, sd1.ac_no ASC");
      } else if ($data['order_by'] == 'state') {
        $sql->orderByRaw("state.ST_NAME ASC");
      } else {
      }
    } else {
      $sql->orderByRaw("state.ST_NAME, ac.AC_NO, pc.AC_NAME ASC");
    }

    $query = $sql->get();

    return $query;
  }



  public static function get_report($data = array())
  {

    $election_id = Auth::user()->election_id;

    $result = [
      "est_total_round1"      =>  0,
      "est_total_round2"      =>  0,
      "est_total_round3"      =>  0,
      "est_total_round4"      =>  0,
      "est_total_round5"      =>  0,
      "close_of_poll"         =>  0,
      "est_total"             =>  0,
      "total_record"          =>  0,
      "total_percentage"      =>  0,
      "pc_no"                 =>  "",
      "pc_name"               =>  "",
      "st_code"               =>  "",
    ];



    $sql_raw = "IFNULL(ROUND(AVG(est_turnout_round1),2),0) as est_total_round1, IFNULL(ROUND(AVG(est_turnout_round2),2),0) as est_total_round2, IFNULL(ROUND(AVG(est_turnout_round3),2),0) as est_total_round3, IFNULL(ROUND(AVG(est_turnout_round4),2),0) as est_total_round4, IFNULL(ROUND(AVG(est_turnout_round5),2),0) as est_total_round5, IFNULL(ROUND(AVG(close_of_poll),2),0) as close_of_poll, IFNULL(ROUND(AVG(est_turnout_total),2),0) as est_total, COUNT(*) as total_record, IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percentage, pc.PC_NO as pc_no, pc.PC_NAME as pc_name, pc.st_code, sd1.ac_no as ac_no";

    $sql = DB::table('pd_scheduledetail as sd1')
      ->join('pd_schedulemaster as sm1', [
        ['sd1.pd_scheduleid', '=', 'sm1.pd_scheduleid']
      ])
      ->join('m_pc as pc', [
        ['pc.PC_NO', '=', 'sd1.pc_no'],
        ['pc.ST_CODE', '=', 'sd1.st_code'],
      ])
      ->join('m_state as state', [
        ['state.ST_CODE', '=', 'pc.ST_CODE']
      ])->join('m_election_details as e', [
        ['e.ST_CODE', '=', 'sd1.st_code'],
        ['e.CONST_NO', '=', 'sd1.ac_no']
      ]);

    $sql->selectRaw($sql_raw);

    if (!empty($data['state'])) {
      $sql->where("sd1.st_code", $data['state']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sm1.schedule_id", $data['phase']);
    }

    if (!empty($data['pc_no'])) {
      $sql->where("sm1.pc_no", $data['pc_no']);
    }

    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);

    if (!empty($data['group_by']) && in_array($data['group_by'], ['pc_no', 'ac_no'])) {
      if ($data['group_by'] == 'pc_no') {
        $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
      } else if ($data['group_by'] == 'ac_no') {
        $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
      }
    } else {
      $sql->groupBy("sd1.st_code");
    }

    if (!empty($data['order_by']) && in_array($data['order_by'], ['pc_no', 'ac_no'])) {
      if ($data['order_by'] == 'pc_no') {
        $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
      } else if ($data['order_by'] == 'ac_no') {
        $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME, sd1.ac_no ASC");
      }
    } else {
      $sql->orderByRaw("state.ST_NAME, pc.pc_no, pc.PC_NAME ASC");
    }

    $query = $sql->first();

    if ($query) {
      $result = [
        "est_total_round1"      =>  $query->est_total_round1,
        "est_total_round2"      =>  $query->est_total_round2,
        "est_total_round3"      =>  $query->est_total_round3,
        "est_total_round4"      =>  $query->est_total_round4,
        "est_total_round5"      =>  $query->est_total_round5,
        "close_of_poll"         =>  $query->close_of_poll,
        "est_total"             =>  $query->est_total,
        "total_record"          =>  $query->total_record,
        "total_percentage"      =>  $query->total_percentage,
        "pc_no"                 =>  $query->pc_no,
        "pc_name"               =>  $query->pc_name,
        "st_code"               =>  $query->st_code,
      ];
    }
    return $result;
  }


  public static function get_average_sum($data = array())
  {

    $election_id = Auth::user()->election_id;

    $sql_raw  = "IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percent";
    $sql    = DB::table('pd_scheduledetail as sd1')->join('m_election_details as e', [
      ['e.ST_CODE', '=', 'sd1.st_code'],
      ['e.CONST_NO', '=', 'sd1.ac_no']
    ])->join('pd_schedulemaster as sm1', [
      ['sd1.pd_scheduleid', '=', 'sm1.pd_scheduleid']
    ])
      ->selectRaw($sql_raw);


    if (!empty($data['election_type'])) {
      $sql->where('e.ELECTION_TYPEID', $data['election_type']);
    }


    if (!empty($data['state'])) {
      $sql->where("sd1.st_code", $data['state']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sm1.state_phase_no", $data['phase']);
    }

    if (!empty($data['pc_no'])) {
      $sql->where("sd1.pc_no", $data['pc_no']);
    }

    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);

    if (!empty($data['group_by']) && in_array($data['group_by'], ['pc_no', 'ac_no'])) {
      if ($data['group_by'] == 'pc_no') {
        $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
      } else if ($data['group_by'] == 'ac_no') {
        $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
      } else {
        $sql->groupBy("sd1.st_code");
      }
    }

    $query = $sql->first();

    return ($query) ? $query->total_percent : 0;
  }



  public static function get_average_sum_roundwise($data = array())
  {

    $election_id = Auth::user()->election_id;

    if ($data['round'] == 1)
      $round_column = 'est_turnout_round1';
    if ($data['round'] == 2)
      $round_column = 'est_turnout_round2';
    if ($data['round'] == 3)
      $round_column = 'est_turnout_round3';
    if ($data['round'] == 4)
      $round_column = 'est_turnout_round4';
    if ($data['round'] == 5)
      $round_column = 'est_turnout_round5';
    if ($data['round'] == 6)
      $round_column = 'close_of_poll';

    //dd($round_column);

    //$est_voter=round(($electors_total*$latest_updated/100),0);


    $sql_raw = '';
    $sql_raw  = "IFNULL(ROUND((SUM(round((electors_total*$round_column/100),0)) * 100 )/SUM(electors_total),2),0) as total_percent";

    // dd($sql_raw);

    $sql    = DB::table('pd_scheduledetail as sd1')->join('m_election_details as e', [
      ['e.ST_CODE', '=', 'sd1.st_code'],
      ['e.CONST_NO', '=', 'sd1.ac_no']
    ])->join('pd_schedulemaster as sm1', [
      ['sd1.pd_scheduleid', '=', 'sm1.pd_scheduleid']
    ])
      ->selectRaw($sql_raw);


    if (!empty($data['election_type'])) {
      $sql->where('e.ELECTION_TYPEID', $data['election_type']);
    }


    if (!empty($data['state'])) {
      $sql->where("sd1.st_code", $data['state']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sm1.state_phase_no", $data['phase']);
    }

    if (!empty($data['pc_no'])) {
      $sql->where("sd1.pc_no", $data['pc_no']);
    }

    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);

    if (!empty($data['group_by']) && in_array($data['group_by'], ['pc_no', 'ac_no'])) {
      if ($data['group_by'] == 'pc_no') {
        $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
      } else if ($data['group_by'] == 'ac_no') {
        $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
      } else {
        $sql->groupBy("sd1.st_code");
      }
    }

    $query = $sql->first();

    return ($query) ? $query->total_percent : 0;
  }

  public static function get_district_reports($data = array())
  {

    $election_id = Auth::user()->election_id;

    $sql_raw = "IFNULL(ROUND(AVG(est_turnout_round1),2),0) as est_total_round1, IFNULL(ROUND(AVG(est_turnout_round2),2),0) as est_total_round2, IFNULL(ROUND(AVG(est_turnout_round3),2),0) as est_total_round3, IFNULL(ROUND(AVG(est_turnout_round4),2),0) as est_total_round4, IFNULL(ROUND(AVG(est_turnout_round5),2),0) as est_total_round5, IFNULL(ROUND(AVG(close_of_poll),2),0) as close_of_poll, IFNULL(ROUND(AVG(est_turnout_total),2),0) as est_total, COUNT(*) as total_record, IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percentage, dist.DIST_NO as dist_no, dist.DIST_NAME as dist, dist.ST_CODE as st_code, state.ST_NAME as st_name, sd1.dist_no as dist_no, sum(electors_total) as electors_total,sum(est_voters) as est_voters";

    $sql = DB::table('pd_scheduledetail as sd1')
      ->join('pd_schedulemaster as sm1', [
        ['sd1.pd_scheduleid', '=', 'sm1.pd_scheduleid']
      ])
      ->join('m_district as dist', [
        ['dist.DIST_NO', '=', 'sd1.dist_no'],
        ['dist.ST_CODE', '=', 'sd1.st_code'],
      ])
      ->leftjoin('m_state as state', [
        ['state.ST_CODE', '=', 'dist.ST_CODE']
      ])
      ->join('m_election_details as e', [
        ['e.ST_CODE', '=', 'sd1.st_code']
      ]);

    $sql->selectRaw($sql_raw);


    if (!empty($data['election_type'])) {
      $sql->where('e.ELECTION_TYPEID', $data['election_type']);
    }


    if (!empty($data['state'])) {
      $sql->where("sd1.st_code", $data['state']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sm1.schedule_id", $data['phase']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where("sm1.ac_no", $data['ac_no']);
    }

    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);

    if (!empty($data['group_by'])) {
      if ($data['group_by'] == 'dist_no') {
        $sql->groupBy("sd1.dist_no")->groupBy("sd1.st_code");
      } else if ($data['group_by'] == 'state') {
        $sql->groupBy("sd1.st_code");
      } else {
      }
    } else {
      $sql->groupBy("sd1.st_code");
    }

    if (!empty($data['order_by'])) {
      if ($data['order_by'] == 'ac_no') {
        $sql->orderByRaw("state.ST_NAME, dist.DIST_NO, dist.DIST_NAME, sd1.dist_no ASC");
      } else if ($data['order_by'] == 'state') {
        $sql->orderByRaw("state.ST_NAME ASC");
      } else {
      }
    } else {
      $sql->orderByRaw("state.ST_NAME, dist.DIST_NO, pc.AC_NAME ASC");
    }

    $query = $sql->get();

    return $query;
  }

  public static function get_average_sum_roundwise_district($data = array())
  {

    $election_id = Auth::user()->election_id;

    if ($data['round'] == 1)
      $round_column = 'est_turnout_round1';
    if ($data['round'] == 2)
      $round_column = 'est_turnout_round2';
    if ($data['round'] == 3)
      $round_column = 'est_turnout_round3';
    if ($data['round'] == 4)
      $round_column = 'est_turnout_round4';
    if ($data['round'] == 5)
      $round_column = 'est_turnout_round5';
    if ($data['round'] == 6)
      $round_column = 'close_of_poll';

    //dd($round_column);

    //$est_voter=round(($electors_total*$latest_updated/100),0);


    $sql_raw = '';
    $sql_raw  = "IFNULL(ROUND((SUM(round((electors_total*$round_column/100),0)) * 100 )/SUM(electors_total),2),0) as total_percent";

    // dd($sql_raw);

    $sql    = DB::table('pd_scheduledetail as sd1')->join('m_election_details as e', [
      ['e.ST_CODE', '=', 'sd1.st_code']
    ])
      ->selectRaw($sql_raw);


    if (!empty($data['election_type'])) {
      $sql->where('e.ELECTION_TYPEID', $data['election_type']);
    }


    if (!empty($data['state'])) {
      $sql->where("sd1.st_code", $data['state']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sd1.scheduleid", $data['phase']);
    }

    if (!empty($data['pc_no'])) {
      $sql->where("sd1.pc_no", $data['pc_no']);
    }

    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);

    if (!empty($data['group_by']) && in_array($data['group_by'], ['pc_no', 'dist_no'])) {
      if ($data['group_by'] == 'pc_no') {
        $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
      } else if ($data['group_by'] == 'dist_no') {
        $sql->groupBy("sd1.dist_no")->groupBy("sd1.st_code");
      } else {
        $sql->groupBy("sd1.st_code");
      }
    }

    $query = $sql->first();

    return ($query) ? $query->total_percent : 0;
  }
}
