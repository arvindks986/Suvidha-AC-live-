<?php

namespace App\models\Admin;

use App\models\States;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EndOfPollFinaliseModel extends Model
{

    protected $table = 'pd_schedulemaster';

    public function state()
    {
        return $this->belongsTo(States::class, 'st_code', 'ST_CODE');
    }

    //GET AC FINALIZE DATA FUNCTION STARTS
    public static function get_eop_finalise_data($data = array())
    {

        $election_id = Auth::user()->election_id;
        $sql_raw = "ms.ST_NAME AS state_name,ms.ST_CODE AS st_code,pm.st_code, COUNT(DISTINCT(pm.ac_no)) AS total_const, COUNT(DISTINCT(IF(pd.end_of_poll_finalize=1,pm.ac_no,NULL))) const_finalised";

        $sql = DB::table('pd_schedulemaster as pm')
            ->join('pd_scheduledetail as pd', [
                ['pm.st_code', '=', 'pd.st_code'],
                ['pm.ac_no', '=', 'pd.ac_no'],
                ['pm.pd_scheduleid', '=', 'pd.pd_scheduleid']
            ])
            ->join('m_election_details', [
                ['m_election_details.ST_CODE', '=', 'pd.st_code'],
                ['m_election_details.CONST_NO', '=', 'pd.ac_no']
            ])
            ->leftjoin('m_state as ms', [
                ['ms.ST_CODE', '=', 'pd.st_code']
            ]);


        $sql->selectRaw($sql_raw);


        //CHECKING PHASE
        if (!empty($data['phase'])) {
            $sql->where("pd.scheduleid", $data['phase']);
        }


        if (!empty($data['st_code'])) {
            $sql->where('pm.st_code', $data['st_code']);
        }

        if (!empty($data['dist_no'])) {
            $sql->where('pm.dist_no', $data['dist_no']);
        }


        if (!empty($data['ac_no'])) {
            $sql->where('pm.ac_no', $data['ac_no']);
        }

        if (!empty($data['election_type'])) {
            $sql->where('m_election_details.ELECTION_TYPEID', $data['election_type']);
        }

        $sql->where('m_election_details.CONST_TYPE', 'AC');
        $sql->where('election_status', '1');
        $sql->where('m_election_details.ELECTION_ID', $election_id);


        //GROUP BY STARTS
        if (!empty($data['group_by'])) {
            if ($data['group_by'] == 'ac_no') {
                $sql->groupBy("pm.ac_no");
            } else if ($data['group_by'] == 'state') {
                $sql->groupBy("pm.st_code");
            }
        };
        //GROUP BY ENDS


        //ORDER BY STARTS
        if (!empty($data['order_by'])) {
            if ($data['order_by'] == 'ac_no') {
                $sql->orderByRaw("pm.ac_no ASC");
            } else if ($data['order_by'] == 'ac_no') {
                $sql->orderByRaw("pm.st_code, pm.ac_no ASC");
            }
        } else {
            $sql->orderByRaw("pm.st_code ASC");
        };

        //ORDER BY ENDS


        $query = $sql->get();

        return $query;
    }
    //GET AC FINALIZE DATA FUNCTION ends

    //AC FINALIZE LIST FUNCTION STARTS
    public static function get_eop_finalise_list($data = array())
    {

        $election_id = Auth::user()->election_id;
        $sql_raw = "ms.ST_NAME AS state_name,ms.ST_CODE AS st_code, ac.AC_NO AS const_no,ac.AC_NAME AS const, IF(pd.end_of_poll_finalize=1,'Yes','No') AS finalized_const";

        $sql = DB::table('m_ac as ac')
            ->join('pd_schedulemaster as pm', [
                ['pm.st_code', '=', 'ac.ST_CODE'],
                ['pm.ac_no', '=', 'ac.AC_NO'],
            ])
            ->join('pd_scheduledetail as pd', [
                ['pd.st_code', '=', 'ac.ST_CODE'],
                ['pd.ac_no', '=', 'ac.AC_NO'],
                ['pm.pd_scheduleid', '=', 'pd.pd_scheduleid']
            ])
            ->join('m_election_details as e', [
                ['e.ST_CODE', '=', 'ac.ST_CODE'],
                ['e.CONST_NO', '=', 'ac.AC_NO']
            ])
            ->leftjoin('m_state as ms', [
                ['ms.ST_CODE', '=', 'pd.st_code']
            ]);


        $sql->selectRaw($sql_raw);


        //CHECKING PHASE
        if (!empty($data['phase'])) {
            $sql->where("pd.scheduleid", $data['phase']);
        }
        if (!empty($data['election_type'])) {
            $sql->where('e.ELECTION_TYPEID', $data['election_type']);
        }

        if (!empty($data['st_code'])) {
            $sql->where('ac.ST_CODE', $data['st_code']);
        }

        if (!empty($data['dist_no'])) {
            $sql->where('ac.DIST_NO_HDQTR', $data['dist_no']);
        }


        if (!empty($data['ac_no'])) {
            $sql->where('ac.AC_NO', $data['ac_no']);
        }

        $sql->where('e.CONST_TYPE', 'AC');
        $sql->where('e.election_status', '1');
        $sql->where('e.ELECTION_ID', $election_id);

        if (!empty($data['group_by'])) {
            if ($data['group_by'] == 'ac_no') {
                $sql->groupBy("ac.AC_NO");
            } else if ($data['group_by'] == 'state') {
                $sql->groupBy("ac.ST_CODE");
            }
        } else {
            $sql->groupBy("ac.AC_NO");
        };

        if (!empty($data['order_by'])) {
            if ($data['order_by'] == 'ac_no') {
                $sql->orderByRaw("ac.AC_NO ASC");
            } else if ($data['order_by'] == 'state') {
                $sql->orderByRaw("ac.ST_CODE,ac.AC_NO ASC");
            }
        } else {
            $sql->orderByRaw("ac.AC_NO ASC");
        };

        $query = $sql->get();

        return $query;
    }
}
