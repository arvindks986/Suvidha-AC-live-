<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DistrictModel extends Model
{
    protected $table = 'm_district';

    public static function get_district($filter_array = array()){
        $sql = DistrictModel::where('DIST_NO',$filter_array['dist_no'])->where('ST_CODE',$filter_array['st_code'])->select('DIST_NAME as dist_name','DIST_NO as dist_no','DIST_NAME_V1 as virnicular_dist_name','ST_CODE as st_code')->first();
        if(!$sql){
          return '';
        }
        return $sql->toArray();
    }

    public static function get_districts($filter = array()){

        $results = [];

        $sql = DistrictModel::join("m_election_details",[
          ["m_election_details.ST_CODE","=","m_ac.ST_CODE"],
          ["m_election_details.CONST_NO","=","m_ac.AC_NO"],
        ])->join('boothapp_enable_acs', [
            ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code'],
            ['m_election_details.CONST_NO','=','boothapp_enable_acs.ac_no'],
        ])->select("m_district.*");

        $sql->where('CONST_TYPE','AC');

        if(!empty($filter['phase_no'])){
          $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
        }

        if(!empty($filter['st_code'])){
          $sql->where('m_district.ST_CODE',$filter['st_code']);
        }

        if(!empty($filter['dist_no'])){
          $sql->where('m_district.DIST_NO',$filter['dist_no']);
        }
       
        $query = $sql->select('m_district.DIST_NAME as dist_name','m_district.DIST_NO as dist_no','m_district.DIST_NAME_V1 as virnicular_dist_name','m_district.ST_CODE as st_code')->orderByRaw('m_district.ST_CODE,m_district.DIST_NO ASC')->groupBy('m_district.DIST_NO')->get();

        if(count($query) > 0){
            $results = $query->toArray();
        }

        return $results;

    }


}