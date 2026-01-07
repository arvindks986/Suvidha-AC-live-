<?php

namespace App\models\Admin;

use App\models\AC;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ElectorModel extends Model
{

  protected $table = 'electors_cdac';

  public function ac()
  {
    return $this->belongsTo(AC::class, 'ac_no', 'AC_NO');
  }

  public static function get_sum($data = array())
  {

    $percent = 0;

    $election_id = Auth::user()->election_id;

    $sql_raw = "ROUND(SUM(electors_cdac.voter_total)/SUM(electors_cdac.electors_total)*100,2) as voter_total";

    $sql = ElectorModel::join('pd_scheduledetail as sd1', [
      ['sd1.st_code', '=', 'electors_cdac.st_code'],
      ['sd1.ac_no', '=', 'electors_cdac.ac_no'],
    ])
      ->join('m_election_details as e', [
        ['e.ST_CODE', '=', 'electors_cdac.st_code'],
        ['e.CONST_NO', '=', 'electors_cdac.ac_no']
      ])
      ->selectRaw($sql_raw);

    if (!empty($data['state'])) {
      $sql->where("electors_cdac.st_code", $data['state']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where("electors_cdac.ac_no", $data['ac_no']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sd1.scheduleid", $data['phase']);
    }

    $sql->where('e.CONST_TYPE', 'AC');
    $sql->where('e.election_status', '1');
    $sql->where('e.ELECTION_ID', $election_id);


    if (!empty($data['group_by'])) {
      if ($data['group_by'] == 'ac_no') {
        $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
      } else if ($data['group_by'] == 'state') {
        $sql->groupBy("electors_cdac.st_code");
      } else {
      }
    } else {
      $sql->groupBy("electors_cdac.st_code");
    }

    $query = $sql->first();

    if ($query) {
      $percent = $query->voter_total;
    }
    return $percent;
  }

  public static function getList($data = array())
  {
    $election_id = Auth::user()->election_id;
    $sqlSelect = "electors_cdac.id, electors_cdac.st_code, electors_cdac.ac_no, electors_cdac.election_id, m_state.ST_NAME , m_state.ST_NAME, ac.AC_NAME, electors_cdac.electors_male, electors_cdac.electors_female, electors_cdac.electors_other, electors_cdac.electors_total, electors_cdac.is_fecthed_from_eronet, electors_cdac.fetched_at";
    $sql = ElectorModel::join('m_state', [
      ['m_state.ST_CODE', '=', 'electors_cdac.st_code'],
    ])
      ->join('m_ac as ac', [
        ['ac.AC_NO', '=', 'electors_cdac.ac_no'],
        ['ac.ST_CODE', '=', 'electors_cdac.st_code'],
      ])
      ->selectRaw($sqlSelect);

    if (!empty($data['state'])) {
      $sql->where("electors_cdac.st_code", $data['state']);
    }

    if (!empty($data['ac_no'])) {
      $sql->where("electors_cdac.ac_no", $data['ac_no']);
    }

    if (!empty($data['phase'])) {
      $sql->where("sd1.scheduleid", $data['phase']);
    }
    $sql->where('electors_cdac.election_id', $election_id);
    $query = $sql->get();
    return $query;
  }
}
