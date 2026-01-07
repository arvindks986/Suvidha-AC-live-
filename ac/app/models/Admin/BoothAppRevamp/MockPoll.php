<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class MockPoll extends Model
{
  protected $table = 'tbl_mock_poll_status';

  protected $connection = 'booth_revamp_test';


  public static function get_mock_poll($filter = array()){

    $sql = MockPoll::join("m_election_details",[
      ["m_election_details.ST_CODE","=","tbl_mock_poll_status.st_code"],
      ["m_election_details.CONST_NO","=","tbl_mock_poll_status.ac_no"],
    ])->join("polling_station",[
      ["polling_station.ST_CODE","=","tbl_mock_poll_status.st_code"],
      ["polling_station.AC_NO","=","tbl_mock_poll_status.ac_no"],
      ["polling_station.PS_NO","=","tbl_mock_poll_status.ps_no"],
    ])->selectRaw("COUNT(IF(mock_poll_start ='Y',1,NULL)) AS mock_poll_start, COUNT(IF(number_of_polling_agent='Y',1,NULL)) AS number_of_polling_agent, COUNT(IF(mock_poll_result_shown='Y',1,NULL)) AS mock_poll_result_shown, COUNT(IF(button_clear ='Y',1,NULL)) AS button_clear, COUNT(IF(slip_remove ='Y',1,NULL)) AS slip_remove");

    $sql->where('CONST_TYPE','AC');

    $sql->where("polling_station.booth_app_excp", 0);

    if(!empty($filter['phase_no'])){
      $sql->where('m_election_details.PHASE_NO',$filter['phase_no']);
    }

    if(!empty($filter['st_code'])){
      $sql->where('tbl_mock_poll_status.st_code',$filter['st_code']);
    }

    if(!empty($filter['ac_no'])){
      $sql->where('tbl_mock_poll_status.ac_no',$filter['ac_no']);
    }

    if(!empty($filter['ps_no'])){
      $sql->where('tbl_mock_poll_status.ps_no',$filter['ps_no']);
    }

    if(!empty($filter['role_id'])){
      $sql->where('tbl_mock_poll_status.user_type_id',$filter['role_id']);
    }

    $sql->where('tbl_mock_poll_status.row_status','A');

    return $sql->first()->toArray();

  }

  


}