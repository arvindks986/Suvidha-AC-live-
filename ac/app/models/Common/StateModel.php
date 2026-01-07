<?php namespace App\models\Common;

use Illuminate\Database\Eloquent\Model;

class StateModel extends Model
{
    protected $table = 'm_state';

    public static function get_states($filter = array()){
        /*$sql = StateModel::join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
        ])->where('m_election_details.CONST_TYPE','AC')->where('election_status','1')->selectRaw("CONCAT(ST_NAME,'-',ST_NAME_HI) as st_name, m_state.ST_CODE as st_code")->orderBy('m_state.ST_NAME','ASC')->groupBy('m_state.ST_CODE');
        */
		$sql = StateModel::join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
        ])->where('m_election_details.CONST_TYPE','AC')->where('election_status','1')->selectRaw("CONCAT(ST_NAME) as st_name, m_state.ST_CODE as st_code, ELECTION_ID as election_id")->orderBy('m_state.ST_NAME','ASC')->groupBy('m_state.ST_CODE');		
		
		return $sql->get();
    }

    public static function get_state($st_code){
    	return StateModel::where('ST_CODE', $st_code)->first();
    }

}