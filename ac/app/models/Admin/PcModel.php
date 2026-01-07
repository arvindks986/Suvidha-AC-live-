<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class PcModel extends Model
{
    protected $table = 'm_pc';

    public static function get_record($filter_array = array()){
	    $sql = PcModel::where('PC_NO',$filter_array['pc_no'])->where('ST_CODE',$filter_array['state'])->select('PC_NAME as pc_name','PC_NO as pc_no')->first();
	    if(!$sql){
	      return '';
	    }
	    return $sql->toArray();
	}

	public static function get_records($data = array()){

		$results = [];

		$sql = PcModel::join('m_election_details',[
            ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
            ['m_election_details.CONST_NO', '=','m_pc.PC_NO']
        ]);

        $sql->where('m_election_details.CONST_TYPE','PC');

        if(!empty($data['state'])){
           $sql->where('m_election_details.ST_CODE',$data['state']);
        }

        if(!empty($data['pc_no'])){
          $sql->where('m_pc.PC_NO',$data['pc_no']);
        }

        if(!empty($data['phase'])){
          $sql->where('m_election_details.PHASE_NO',$data['phase']);
        }
		
		if(!empty($data['election_type'])){
            $sql->where('m_election_details.ELECTION_TYPEID',$data['election_type']);
        }

        $query = $sql->select('m_pc.PC_NO as pc_no','m_pc.PC_NAME as pc_name')->orderByRaw('m_pc.ST_CODE,m_pc.PC_NO ASC')->groupBy('m_pc.PC_NO')->get();

        if(count($query) > 0){
        	$results = $query->toArray();
        }

        return $results;

	}

}