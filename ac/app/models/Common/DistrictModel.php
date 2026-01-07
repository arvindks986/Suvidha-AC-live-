<?php namespace App\models\Common;

use Illuminate\Database\Eloquent\Model;

class DistrictModel extends Model
{
  
  protected $table = 'm_district';

	public static function get_districts($data = array()){

		$results = [];

		$sql = DistrictModel::join('m_election_details',[
         ['m_election_details.ST_CODE', '=','m_district.ST_CODE'],
       ])->where('election_status','1');

    if(!empty($data['st_code'])){
      $sql->where('m_district.ST_CODE',$data['st_code']);
    }

    $sql->where('m_election_details.CONST_TYPE','AC');

    $results = $sql->selectRaw("m_district.DIST_NO as district_no, CONCAT(DIST_NAME,'-',DIST_NAME_HI) as district_name, m_district.ST_CODE as st_code")->orderByRaw('m_district.ST_CODE,m_district.DIST_NO ASC')->groupBy('m_district.ST_CODE')->groupBy("m_district.DIST_NO")->get();

    return $results;

	}

  public static function get_district($data = array()){

    $results = [];

    $sql = DistrictModel::selectRaw("m_district.DIST_NO as district_no, DIST_NAME as district_name, m_district.ST_CODE as st_code");

    if(!empty($data['st_code'])){
      $sql->where('m_district.ST_CODE',$data['st_code']);
    }

    if(!empty($data['dist_no'])){
      $sql->where('m_district.DIST_NO',$data['dist_no']);
    }

    $results = $sql->first();
    if(!$results){
      return false;
    }
    return $results->toArray();

  }

}