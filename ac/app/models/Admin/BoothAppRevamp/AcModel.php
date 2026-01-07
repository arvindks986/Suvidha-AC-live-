<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AcModel extends Model
{
    protected $table = 'm_ac';

    public static function get_ac($filter_array = array()){
        $sql = AcModel::where('AC_NO',$filter_array['ac_no'])->where('ST_CODE',$filter_array['state'])->select('AC_NAME as ac_name','AC_NO as ac_no','AC_NAME_V1 as ac_name_v')->first();
        if(!$sql){
          return '';
        }
        return $sql->toArray();
    }

    public static function get_acs($filter = array()){

        $results = [];
      
        $sql = AcModel::join("m_election_details",[
          ["m_election_details.ST_CODE","=","m_ac.ST_CODE"],
          ["m_election_details.CONST_NO","=","m_ac.AC_NO"],
        ])->join('boothapp_enable_acs', [
            ['m_election_details.ST_CODE','=','boothapp_enable_acs.st_code'],
            ['m_election_details.CONST_NO','=','boothapp_enable_acs.ac_no'],
        ])->select("m_ac.*");

        $sql->where('CONST_TYPE','AC');

        if(!empty($filter['phase_no'])){
          $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
        }

        if(!empty($filter['st_code'])){
           $sql->where('m_ac.ST_CODE',$filter['st_code']);
        }

        if(!empty($filter['dist_no'])){
          $sql->where('m_ac.DIST_NO_HDQTR',$filter['dist_no']);
        }

        if(!empty($filter['ac_no'])){
          $sql->where('m_ac.AC_NO',$filter['ac_no']);
        }

        $query = $sql->select('m_ac.AC_NO as ac_no','m_ac.AC_NAME as ac_name','m_ac.ST_CODE as st_code','m_ac.DIST_NO_HDQTR as dist_no')->orderByRaw('m_ac.ST_CODE,m_ac.AC_NO ASC')->groupBy('m_ac.AC_NO')->get();

        if(count($query) > 0){
            $results = $query->toArray();
        }

        return $results;

    }
}