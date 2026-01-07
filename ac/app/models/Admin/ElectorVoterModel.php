<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class ElectorVoterModel extends Model
{
    protected $table = 'electors_cdac';

    public $timestamps = false;

    public static function get_records($filter_array = array()){
        if(empty($filter_array['ac_no'])){
            return [];
        }
	    $sql = ElectorVoterModel::join('m_ac',[
            ['m_ac.ac_no','=','electors_cdac.ac_no'],
            ['m_ac.ST_CODE','=','electors_cdac.st_code'],
        ])->where('electors_cdac.ac_no',$filter_array['ac_no'])->where('electors_cdac.st_code',$filter_array['state'])->select('m_ac.AC_NAME as ac_name','electors_cdac.ac_no','electors_cdac.id', 'electors_male', 'electors_female', 'electors_other', 'electors_total', 'gen_electors_male', 'gen_electors_female', 'gen_electors_other', 'nri_male_electors', 'nri_female_electors', 'nri_third_electors', 'service_male_electors', 'service_female_electors', 'service_third_electors', 'updated_by');
        if(!empty($filter_array['year'])){
           // $sql->where('year', $filter_array['year']);
        }


        $query =  $sql->groupBy('ac_no')->get();
	    if(!$query){
	      return [];
	    }
	    return $query->toArray();
	}

	public static function get_years($filter_array = array()){
        $sql = ElectorVoterModel::where('ST_CODE',$filter_array['state'])->select('year')
		->where('year','>=',getElectionYear())
		->groupBy('year')->get();
        if(!$sql){
          return [];
        }
        return $sql->toArray();
    }

    public static function get_acs($filter_array = array()){
        $sql = ElectorVoterModel::join('m_ac',[
            ['m_ac.AC_NO','=','electors_cdac.ac_no'],
            ['m_ac.ST_CODE','=','electors_cdac.st_code'],
        ])->where('electors_cdac.st_code',$filter_array['state'])->select('electors_cdac.ac_no','m_ac.AC_NAME as ac_name')
		//->where('electors_cdac.year', $filter_array['year'])
		->groupBy('electors_cdac.ac_no')->orderBy('ac_no','ASC')->get();
        if(!$sql){
          return [];
        }
        return $sql->toArray();
    }

    public static function get_validate($data = array()){
        return ElectorVoterModel::where($data)->count();
    }

    public static function update_index_card_data($data, $filter){
        ElectorVoterModel::where($filter)->update($data);
    }


    public static function get_finalize_acs($filter_array = array()){
        $sql = ElectorVoterModel::join('m_ac',[
            ['m_ac.AC_NO','=','electors_cdac.ac_no'],
            ['m_ac.ST_CODE','=','electors_cdac.st_code'],
        ])->leftjoin('electors_cdac_other_information as oi',[
            ['m_ac.AC_NO','=','oi.ac_no'],
            ['m_ac.ST_CODE','=','oi.st_code'],
        ])->where('electors_cdac.st_code',$filter_array['state'])->select('oi.id','oi.finalize','oi.finalize_by_ro','oi.finalize_by_ceo','electors_cdac.ac_no','m_ac.AC_NAME as ac_name','electors_cdac.st_code')
		//->where('electors_cdac.year', $filter_array['year'])
		->groupBy('electors_cdac.ac_no')->orderBy('ac_no','ASC')->get();
        if(!$sql){
          return [];
        }
        return $sql->toArray();
    }


}