<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhaseModel extends Model
{

    protected $table = 'm_election_details';

    public static function get_current_phase()
    {
        $election_id = Auth::user()->election_id;
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d");

        $sql_raw = "e.PHASE_NO, e.ELECTION_ID, e.election_status, e.ELECTION_TYPEID, ms.SCHEDULENO";

        $sql = DB::table('m_election_details as e')
            ->join('m_schedule as ms', [
                ['ms.SCHEDULENO', '=', 'e.PHASE_NO']
            ]);

        $sql->selectRaw($sql_raw);

        $sql->where('ms.DATE_POLL', '<=', $date);
        $sql->where('e.CONST_TYPE', 'AC');
        $sql->where('e.election_status', '1');
        $sql->where('e.ELECTION_ID', $election_id);

        $sql->groupBy("e.PHASE_NO");
        $sql->orderByRaw("ms.DATE_POLL DESC");

        $query = $sql->first();

        //$query = PhaseModel::where('DATE_POLL','<=',$date)->orderBy('DATE_POLL','DESC')->first();

        if (!$query) {
            return "";
        }
        return $query->PHASE_NO;
        //return 1;
    }

    public static function get_phase($phase_id)
    {

        $election_id = Auth::user()->election_id;

        $sql_raw = "e.PHASE_NO, e.ELECTION_ID, e.election_status, e.ELECTION_TYPEID, ms.SCHEDULENO, ms.DATE_POLL";

        $sql = DB::table('m_election_details as e')
            ->leftjoin('m_schedule as ms', [
                ['ms.SCHEDULENO', '=', 'e.PHASE_NO']
            ]);
        $sql->selectRaw($sql_raw);

        $sql->where('SCHEDULENO', $phase_id);
        $sql->where('e.CONST_TYPE', 'AC');
        $sql->where('e.election_status', '1');
        $sql->where('e.ELECTION_ID', $election_id);

        $sql->groupBy("e.PHASE_NO");
        $sql->orderByRaw("e.PHASE_NO ASC");
        return $sql->first();
    }

    public static function get_active_phases()
    {
        $election_id = Auth::user()->election_id;
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d");

        $sql_raw = "e.PHASE_NO, e.ELECTION_ID, e.election_status, e.ELECTION_TYPEID, ms.SCHEDULENO";

        $sql = DB::table('m_election_details as e')
            ->leftjoin('m_schedule as ms', [
                ['ms.SCHEDULENO', '=', 'e.PHASE_NO']
            ]);

        $sql->selectRaw($sql_raw);

        $sql->where('ms.DATE_POLL', '<=', $date);
        $sql->where('e.CONST_TYPE', 'AC');
        $sql->where('e.election_status', '1');
        $sql->where('e.ELECTION_ID', $election_id);

        $sql->groupBy("e.PHASE_NO");
        $sql->orderByRaw("e.PHASE_NO ASC");

        return $sql->get();

        //return PhaseModel::where('DATE_POLL','<=',$date)->get();

    }

    public static function get_phases($data = array())
    {

        $election_id = Auth::user()->election_id;
        $state_id = Auth::user()->st_code;
        $role_id = Auth::user()->role_id;

        $sql_raw = "e.StatePHASE_NO as PHASE_NO,e.statePHASE_NO, e.ELECTION_ID, e.election_status, e.ELECTION_TYPEID, ms.SCHEDULENO,ms.DATE_POLL";

        $sql = DB::table('m_election_details as e')
            ->join('m_schedule as ms', [
                ['ms.SCHEDULENO', '=', 'e.PHASE_NO']
            ]);

        $sql->selectRaw($sql_raw);

        if (!empty($data['election_type'])) {
            $sql->where('e.ELECTION_TYPEID', $data['election_type']);
        }

        $sql->where('e.CONST_TYPE', 'AC');
        $sql->where('e.election_status', '1');
        $sql->where('e.ELECTION_ID', $election_id);
        if (isset($data['state'])) {
            $sql->where('e.ST_CODE', $data['state']);
        } else {
            if ($role_id == 4 || $role_id == 5) {
                $sql->where('e.ST_CODE', $state_id);
            }
        }

        $sql->groupBy("e.StatePHASE_NO");
        $sql->orderByRaw("e.StatePHASE_NO ASC");

        return $sql->get();

        //return PhaseModel::whereIn('SCHEDULEID',[1,2,3,4,5,6,7])->get();

    }


    //phase 1
    public static function get_phases_for_phase1()
    {

        $election_id = Auth::user()->election_id;

        $sql_raw = "e.PHASE_NO, e.ELECTION_ID, e.election_status, e.ELECTION_TYPEID, ms.SCHEDULENO";

        $sql = DB::table('m_election_details as e')
            ->leftjoin('m_schedule as ms', [
                ['ms.SCHEDULENO', '=', 'e.PHASE_NO']
            ]);

        $sql->selectRaw($sql_raw);

        $sql->where('e.CONST_TYPE', 'AC');
        $sql->where('e.election_status', '1');
        $sql->where('e.ELECTION_ID', $election_id);
        $sql->where('ms.SCHEDULENO', '1');

        $sql->groupBy("e.PHASE_NO");
        $sql->orderByRaw("e.PHASE_NO ASC");

        return $sql->get();

        //return PhaseModel::where('PHASE_NO',1)->get();
    }


    public static function get_state_phase($data)
    {
        $election_id = Auth::user()->election_id;


        $sql  = DB::table('pd_schedule_estimated')->where('election_id', $election_id);
        if (isset($data['st_code'])) {
            $sql->where('ST_CODE', $data['st_code']);
        }

        return $sql->first();
    }
}
