<?php namespace App\models\Admin;

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
        $election_id = Auth::user()->election_id;

        $sql = DistrictModel::join('m_election_details',[
            ['m_election_details.ST_CODE', '=','m_district.ST_CODE'],
        ])->join('m_ac',[
            ['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
            ['m_election_details.CONST_NO', '=','m_ac.AC_NO'],
            ['m_district.DIST_NO', '=','m_ac.DIST_NO_HDQTR'],
        ]);

        $sql->where('m_election_details.CONST_TYPE','AC');
        $sql->where('m_election_details.election_status','1');

        if(!empty($filter['st_code'])){
           $sql->where('m_election_details.ST_CODE',$filter['st_code']);
        }

        if(!empty($filter['dist_no'])){
           $sql->where('m_district.DIST_NO',$filter['dist_no']);
        }
        
        $query = $sql->select('DIST_NAME as dist_name','DIST_NO as dist_no','DIST_NAME_V1 as virnicular_dist_name','m_district.ST_CODE as st_code')->orderByRaw('m_district.ST_CODE,m_district.DIST_NO ASC')->groupBy('m_district.DIST_NO')->get();

        if(count($query) > 0){
            $results = $query->toArray();
        }

        return $results;

    }


}