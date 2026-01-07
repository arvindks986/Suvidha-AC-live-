<?php

namespace App\models\Admin;

use App\models\AC;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\models\States;
use Illuminate\Support\Facades\DB;

class EndOfPollModel extends Model
{
    protected $table = 'pd_scheduledetail';

    public function state()
    {
        return $this->belongsTo(States::class, 'st_code', 'ST_CODE');
    }

    public function ac()
    {
        return $this->belongsTo(AC::class, 'ac_no', 'AC_NO');
    }

    public function phase()
    {
        return $this->belongsTo(ElectionModel::class, 'scheduleid', 'ScheduleID');
    }

    public function legislativeAssemblyVt()
    {
        return $this->belongsTo(LegislativeAssemblyVt::class, 'ac_no', 'ac_no');
    }

    public function loksabhaElectionVt()
    {
        return $this->belongsTo(LoksabhaElectionVt::class, 'ac_no', 'ac_no');
    }

    public static function get_reports($data = array())
    {

        $election_id = Auth::user()->election_id;

        $sql_raw = "IFNULL(SUM(total_male),0) AS total_male, IFNULL(SUM(total_female),0) AS total_female, IFNULL(SUM(total_other),0) AS total_other, IFNULL(SUM(total),0) AS total, m_ac.st_code, sd1.ac_no as ac_no, state.ST_NAME as st_name,IF(sd1.end_of_poll_finalize=1,'Yes','No') AS finalized_const";

        $sql = DB::table('pd_scheduledetail as sd1')
            ->join('pd_schedulemaster as sm1', [
                ['sd1.pd_scheduleid', '=', 'sm1.pd_scheduleid']
            ])
            ->join('m_ac as m_ac', [
                ['m_ac.AC_NO', '=', 'sd1.ac_no'],
                ['m_ac.ST_CODE', '=', 'sd1.st_code'],
            ])
            ->join('m_election_details as m_e', [
                ['m_e.CONST_NO', '=', 'm_ac.AC_NO'],
                ['m_e.ST_CODE', '=', 'm_ac.st_code'],
            ])
            ->leftjoin('m_state as state', [
                ['state.ST_CODE', '=', 'm_ac.ST_CODE']
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
            if ($data['phase'] != 'all') {
                $sql->where("sm1.state_phase_no", $data['phase']);
            } else {
                $sql->whereIn("sm1.state_phase_no", [1, 2, 3]);
            }
        }

        if (!empty($data['group_by'])) {
            if ($data['group_by'] == 'ac_no') {
                $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
            } else if ($data['group_by'] == 'national') {
            }
        } else {
            $sql->groupBy("sd1.st_code");
        }

        if (!empty($data['order_by'])) {
            if ($data['order_by'] == 'ac_no') {
                $sql->orderByRaw("state.ST_NAME, m_ac.ac_no, m_ac.AC_NAME ASC");
            }
        } else {
            $sql->orderByRaw("state.ST_NAME, m_ac.ac_no, m_ac.AC_NAME ASC");
        }

        $query = $sql->get();

        return $query;
    }

    public static function get_total_elector($data = array())
    {

        $election_id = Auth::user()->election_id;

        $result = [
            'old_total_male'    => 0,
            'old_total_female'  => 0,
            'old_total_other'   => 0,
            'old_total'         => 0,
        ];

        $sql_raw = "IFNULL(SUM(electors_cdac.electors_male),0) AS old_total_male, IFNULL(SUM(electors_cdac.electors_female),0) AS old_total_female, IFNULL(SUM(electors_cdac.electors_other),0) AS old_total_other, IFNULL(SUM(electors_cdac.electors_total),0) AS old_total";

        $sql = EndOfPollModel::join('electors_cdac', [
            ['pd_scheduledetail.st_code', '=', 'electors_cdac.st_code'],
            ['pd_scheduledetail.ac_no', '=', 'electors_cdac.ac_no'],
        ])
            ->join('m_election_details as m_e', [
                ['m_e.CONST_NO', '=', 'pd_scheduledetail.ac_no'],
                ['m_e.ST_CODE', '=', 'pd_scheduledetail.st_code'],
            ])
            ->join('pd_schedulemaster as sm1', [
                ['pd_scheduledetail.pd_scheduleid', '=', 'sm1.pd_scheduleid']
            ])
            ->selectRaw($sql_raw);

        $sql->where("m_e.CONST_TYPE", "AC");

        if (!empty($data['state'])) {
            $sql->where("electors_cdac.st_code", $data['state']);
        }

        if (!empty($data['ac_no'])) {
            $sql->where("electors_cdac.ac_no", $data['ac_no']);
        }

        if (!empty($data['election_type'])) {
            $sql->where('m_e.ELECTION_TYPEID', $data['election_type']);
        }

        // if (!empty($data['phase'])) {
        //     if ($data['phase'] != 'all') {
        //         $sql->where("pd_scheduledetail.scheduleid", $data['phase']);
        //     } else {
        //         $sql->whereIn("pd_scheduledetail.scheduleid", [1, 2, 3]);
        //     }
        // }
        if (!empty($data['phase'])) {
            if ($data['phase'] != 'all') {
                $sql->where("sm1.state_phase_no", $data['phase']);
            } else {
                $sql->whereIn("sm1.state_phase_no", [1, 2, 3]);
            }
        }

        /*if(!empty($data['year'])){
          $sql->where("electors_cdac.year", Auth::user()->election_id);
        }*/

        $sql->where('electors_cdac.election_id', $election_id);

        if (!empty($data['group_by'])) {
            if ($data['group_by'] == 'ac_no') {
                $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
            } else if ($data['group_by'] == 'national') {
            } else {
                $sql->groupBy("electors_cdac.st_code");
            }
        } else {
            $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
        }

        if (!empty($data['order_by'])) {
            if ($data['order_by'] == 'ac_no') {
                $sql->orderByRaw("electors_cdac.ac_no ASC");
            }
        } else {
            $sql->orderByRaw("electors_cdac.ac_no ASC");
        }

        $query = $sql->first();

        if ($query) {
            $result = $query->toArray();
        }
        return $result;
    }

    public static function get_percentage_2019($data = array())
    {

        $election_id = Auth::user()->election_id;

        $result = [
            'total_elector_male'    => 0,
            'total_elector_female'  => 0,
            'total_elector_other'   => 0,
            'total_elector_total'   => 0,
            'total_voter_male'      => 0,
            'total_voter_female'    => 0,
            'total_voter_other'     => 0,
            'total_voter_total'     => 0,
            'total_percentage'      => 0
        ];

        $sql_raw = "IFNULL(SUM(electors_cdac.electors_male),0) AS total_elector_male, IFNULL(SUM(electors_cdac.electors_female),0) AS total_elector_female, IFNULL(SUM(electors_cdac.electors_other),0) AS total_elector_other, IFNULL(SUM(electors_cdac.electors_total),0) AS total_elector_total, ROUND(SUM(pd_scheduledetail.total)/SUM(electors_cdac.electors_total)*100,2) as total_percentage, IFNULL(SUM(pd_scheduledetail.total_male),0) AS total_voter_male, IFNULL(SUM(pd_scheduledetail.total_female),0) AS total_voter_female, IFNULL(SUM(pd_scheduledetail.total_other),0) AS total_voter_other, IFNULL(SUM(pd_scheduledetail.total),0) AS total_voter_total";

        $sql = EndOfPollModel::join('electors_cdac', [
            ['pd_scheduledetail.st_code', '=', 'electors_cdac.st_code'],
            ['pd_scheduledetail.ac_no', '=', 'electors_cdac.ac_no'],
        ])
            ->join('m_election_details as m_e', [
                ['m_e.CONST_NO', '=', 'pd_scheduledetail.ac_no'],
                ['m_e.ST_CODE', '=', 'pd_scheduledetail.st_code'],
            ])
            ->join('pd_schedulemaster as sm1', [
                ['pd_scheduledetail.pd_scheduleid', '=', 'sm1.pd_scheduleid']
            ])
            ->selectRaw($sql_raw);

        $sql->where("m_e.CONST_TYPE", "AC");

        //$sql->where("electors_cdac.year", 2019);
        $sql->where('electors_cdac.election_id', $election_id);

        if (!empty($data['state'])) {
            $sql->where("electors_cdac.st_code", $data['state']);
        }

        if (!empty($data['ac_no'])) {
            $sql->where("electors_cdac.ac_no", $data['ac_no']);
        }
        if (!empty($data['election_type'])) {
            $sql->where('m_e.ELECTION_TYPEID', $data['election_type']);
        }

        // if (!empty($data['phase'])) {
        //     if ($data['phase'] != 'all') {
        //         $sql->where("pd_scheduledetail.scheduleid", $data['phase']);
        //     } else {
        //         $sql->whereIn("pd_scheduledetail.scheduleid", [1, 2, 3]);
        //     }
        // }

        if (!empty($data['phase'])) {
            if ($data['phase'] != 'all') {
                $sql->where("sm1.state_phase_no", $data['phase']);
            } else {
                $sql->whereIn("sm1.state_phase_no", [1, 2, 3]);
            }
        }

        if (!empty($data['group_by'])) {
            if ($data['group_by'] == 'ac_no') {
                $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
            } else if ($data['group_by'] == 'national') {
            } else {
                $sql->groupBy("electors_cdac.st_code");
            }
        } else {
            $sql->groupBy("electors_cdac.ac_no")->groupBy("electors_cdac.st_code");
        }

        if (!empty($data['order_by'])) {
            if ($data['order_by'] == 'ac_no') {
                $sql->orderByRaw("electors_cdac.ac_no ASC");
            }
        } else {
            $sql->orderByRaw("electors_cdac.ac_no ASC");
        }

        $query = $sql->first();

        if ($query) {
            $result = $query->toArray();
        }
        return $result;
    }
}
