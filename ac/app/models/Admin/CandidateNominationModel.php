<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class CandidateNominationModel extends Model
{
    protected $table = 'candidate_nomination_detail';
	
    public static function get_nomination_status($filter = array()){
        $data = array();
        $sql =  CandidateNominationModel::join('m_election_details', [['candidate_nomination_detail.st_code','=','m_election_details.st_code'],['candidate_nomination_detail.ac_no','=','m_election_details.CONST_NO']])
		->where('m_election_details.CONST_TYPE', 'AC')
		->where('candidate_id', $filter['candidate_id'])
		->where('party_id', '!=', '1180')
		->where('application_status', '!=','11');
        if(!empty($filter['state'])){
            $sql->where('candidate_nomination_detail.st_code',$filter['state']);
        }
        if(!empty($filter['ac_no'])){
            $sql->where('ac_no',$filter['ac_no']);
        }
		if(!empty($filter['election_type_id'])){
			$sql->where('m_election_details.ELECTION_TYPEID', $filter['election_type_id']);
		}
		if(!empty($filter['election_phase'])){
			$sql->where('m_election_details.StatePHASE_NO', $filter['election_phase']);
		}
        $query = $sql->select('application_status','finalaccepted','symbol_id')->get();
        
        return $query;
    }
	

}
