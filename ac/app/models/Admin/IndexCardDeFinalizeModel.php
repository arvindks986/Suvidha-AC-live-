<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class IndexCardDeFinalizeModel extends Model
{

	protected $table = 'electors_cdac_other_information';

    public static function get_list($filter = array()){
      $sql = DB::table('m_ac')
      ->join('m_election_details',[
            ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
            ['m_election_details.CONST_NO', '=','m_ac.AC_NO']
        ]);
		
		$sql->join('m_state',[
            ['m_state.ST_CODE', '=','m_ac.ST_CODE']
        ]);

        $sql->where('m_election_details.CONST_TYPE','AC');
        $sql->where('m_election_details.election_status','1');
		
		
        if(!empty($filter['st_code']) && isset($filter['st_code'])){
            $sql->where('m_ac.ST_CODE',$filter['st_code']);
        }
        if(!empty($filter['ac_no']) && isset($filter['ac_no'])){
            $sql->where('m_ac.AC_NO',$filter['ac_no']);
        }
        $query = $sql->select('m_state.ST_Name as st_name','m_ac.AC_NO as ac_no','m_ac.AC_NAME as ac_name','m_ac.ST_CODE as st_code')->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->groupBy('m_ac.AC_NO')->groupBy("m_ac.ST_CODE")->get()->toArray();
				
		return $query;
    }

	public static function definalize_status($filter = array()){
      $object = IndexCardDeFinalizeModel::where($filter)->first();
      $object->finalize         = 0;
      $object->finalize_by_ro   = 0;
      $object->finalize_by_ceo  = 0;
      $object->finalize_by_eci  = 0;
      return $object->save();

    }
	
	public static function definalize_acs(){
		
	  return DB::select("select b.st_name,c.ac_no,c.ac_name, a.type_finalize,max(created_at) as created_at from indexcard_log a join m_state b on a.st_code=b.st_code join m_ac c on a.ac_no=c.ac_no and a.st_code=c.st_code where submitted_by = 'dirmm' group by b.st_name,c.ac_name, a.type_finalize order by created_at desc");
	  
	  
    }
    
}