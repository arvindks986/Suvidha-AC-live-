<?php namespace App\models\Common;

use Illuminate\Database\Eloquent\Model;

class AcModel extends Model
{
  
  protected $table = 'm_ac';

	public static function get_acs($data = array()){

		$results = [];

		$sql = AcModel::join('m_election_details',[
         ['m_election_details.ST_CODE', '=','m_ac.ST_CODE']
       ])->where('election_status','1');
    
      $sql->where('m_election_details.CONST_TYPE','AC');

        if(!empty($data['st_code'])){
           $sql->where('m_ac.ST_CODE',$data['st_code']);
        }

        if(!empty($data['ac_no'])){
          $sql->where('m_ac.AC_NO',$data['ac_no']);
        }
		
        $query = $sql->selectRaw("m_ac.AC_NO as ac_no, m_ac.ST_CODE as st_code, m_ac.DIST_NO_HDQTR as district_no, CONCAT(AC_NAME,'-',AC_NAME_HI) as ac_name, ELECTION_ID as election_id")->groupBy("m_ac.AC_NO")->groupBy("m_ac.ST_CODE")->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->get();

        if(count($query) > 0){
          $results = $query;
        }

        return $results;

	}

  public static function get_record($filter_array = array()){
        $sql = AcModel::where('AC_NO',$filter_array['ac_no'])->where('ST_CODE',$filter_array['state'])->select('AC_NAME as ac_name','AC_NO as ac_no','AC_NAME_V1 as ac_name_v')->first();
        if(!$sql){
          return '';
        }
        return $sql->toArray();
    }

}