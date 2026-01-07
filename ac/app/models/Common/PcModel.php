<?php namespace App\models\Common;

use Illuminate\Database\Eloquent\Model;

class PcModel extends Model
{
    protected $table = 'm_pc';
	
	public static function get_pcs($filter = array()){
        $sql = PcModel::join('m_election_details',[
         ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
         ['m_election_details.CONST_NO', '=','m_pc.PC_NO'],
       ])
        ->where('election_status','1')
        ->selectRaw("m_pc.PC_NO as pc_no, CONCAT(m_pc.PC_NAME,'-',PC_NAME_HI) as pc_name , m_pc.ST_CODE as st_code, m_election_details.ELECTION_ID as election_id");
        if(!empty($filter['st_code']) && isset($filter['st_code'])){
            $sql->where('ST_CODE',$filter['st_code']);
        }
        if(!empty($filter['pc_no']) && isset($filter['pc_no'])){
            $sql->where('PC_NO',$filter['pc_no']);
        }
        $query = $sql->orderByRaw('m_pc.ST_CODE,m_pc.PC_NO ASC')->groupBy('m_pc.PC_NO')->groupBy("m_pc.ST_CODE")->get();
        return $query;
    }

}