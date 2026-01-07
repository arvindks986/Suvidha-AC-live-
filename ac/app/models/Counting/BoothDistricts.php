<?php

namespace App\models\Counting;

use Illuminate\Database\Eloquent\Model;

class BoothDistricts extends Model
{
    protected $table = 'm_district';
	

	//GETTING SINGLE RECORD OF DISTRICT STARTS
	public static function get_district($filter_array = array()){
	    $sql = BoothDistricts::where('DIST_NO',$filter_array['dist_no'])->where('ST_CODE',$filter_array['state'])->select('DIST_NAME as dist_name','DIST_NO as dist_no')->first();
	    if(!$sql){
	      return '';
	    }
	    return $sql->toArray();
	}
	//GETTING SINGLE RECORD OF DISTRICT ENDS
     
    //GETTING ALL RECORDS OF DISTRICT IN STATE STARTS
	public static function get_districts($data = array()){

		$results = [];

		$sql = BoothDistricts::join('m_state',[
            ['m_state.ST_CODE', '=','m_district.ST_CODE'],
           
        ]);

        if(!empty($data['state'])){
           $sql->where('m_district.ST_CODE',$data['state']);
        }

       $query = $sql->select('m_district.DIST_NO as dist_no','m_district.DIST_NAME as dist_name')->orderByRaw('m_district.ST_CODE,m_district.DIST_NO ASC')->groupBy('m_district.DIST_NO')->get();

        if(count($query) > 0){
        	$results = $query->toArray();
        }

		return $results;

	}
	//GETTING ALL RECORDS OF DISTRICT IN STATE ENDS


	public static function get_all_districts(){
        $query = BoothDistricts::select('m_district.DIST_NO as pc_no','m_district.DIST_NAME as dist_name','m_district.ST_CODE as st_code')->orderByRaw('m_district.ST_CODE,m_district.DIST_NO ASC')->groupBy('m_district.DIST_NO')->groupBy("m_district.ST_CODE")->get();
        return $query;
    }


}
