<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StateModel extends Model
{
    protected $table = 'm_state';
    
    public static function get_states($filter = array()){

        $sql = StateModel::join("m_election_details",[
          ["m_election_details.ST_CODE","=","m_state.ST_CODE"],
        ])->join('boothapp_enable_acs', [
            ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code']
        ])->select("m_state.*");

        $sql->where('CONST_TYPE','AC');

        if(!empty($filter['phase_no'])){
          $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
        }

        if(!empty($filter['st_code'])){
            $sql->where('m_state.ST_CODE',$filter['st_code']);
        }

        $sql->groupBy("m_state.ST_CODE");

        return $sql->get();
    }

    public static function get_state_by_code($state_code = ''){
        $sql = StateModel::where('ST_CODE',$state_code)->first();
        if(!$sql){
            return false;
        }
        return $sql->toArray();
    }

}
