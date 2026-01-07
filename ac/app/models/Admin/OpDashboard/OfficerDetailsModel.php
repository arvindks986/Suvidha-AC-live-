<?php namespace App\models\Admin\OpDashboard;

use Illuminate\Database\Eloquent\Model;
use Auth, Session, Cookie, DB;

class OfficerDetailsModel extends Model
{
    protected $table = 'officer_login';
	
   
	public static function get_district_data(){
		$sql_raw = "dt.*,ms.ST_NAME as state_name";
	    $sql = DB::table('m_district as dt')->leftjoin('m_state as ms',[['dt.ST_CODE', '=','ms.ST_CODE']]);
		$sql->selectRaw($sql_raw);
		$sql->orderBy('dt.ST_CODE', 'ASC');
		return $dist_data =  $sql->get();
	}
	public static function get_all_role_wise(){
		$sql_raw = "COUNT('id') AS ct,designation,role_id";
		$sql = DB::table('officer_login');
		$sql->selectRaw($sql_raw);
		$sql->where('role_id','<>',42);
		$sql->groupBy('role_id');
		return $dist_data =  $sql->get();
	}
	
	public static function get_ac_data(){
		$sql_raw = "mc.*,ms.ST_NAME as state_name";
	    $sql = DB::table('m_ac as mc')->leftjoin('m_state as ms',[['mc.ST_CODE', '=','ms.ST_CODE']]);
		$sql->selectRaw($sql_raw);
		$sql->orderBy('mc.ST_CODE', 'ASC');
		return $dist_data =  $sql->get();
	}
	public static function get_pc_data(){
		$sql_raw = "mc.*,ms.ST_NAME as state_name";
	    $sql = DB::table('m_pc as mc')->leftjoin('m_state as ms',[['mc.ST_CODE', '=','ms.ST_CODE']]);
		$sql->selectRaw($sql_raw);
		$sql->orderBy('mc.ST_CODE', 'ASC');
		return $dist_data =  $sql->get();
	}
	
	public static function get_all_role(){
		$sql_raw = "role_master.role_id,role_master.role_name";
	    $sql = DB::table('role_master');
		$sql->selectRaw($sql_raw);
		$sql->where('role_master.is_active',1);
		$sql->orderBy('role_master.role_id', 'ASC');
		return $dist_data =  $sql->get();
	}
	
	public static function phasewise_nomination_finalized(){
		$EciNominationFinalizedSelect = "SELECT e.ScheduleID AS sid,COUNT(e.CONST_NO) AS total_ac,COUNT(IF(finalized_ac='1',1, NULL)) 'finalized_ac' FROM candidate_finalized_ac c LEFT JOIN m_election_details e ON c.st_code = e.ST_CODE AND c.const_no=e.CONST_NO AND c.const_type = e.CONST_TYPE WHERE c.CONST_TYPE='AC' GROUP BY e.ScheduleID";
		return $EciNominationFinalized = DB::select($EciNominationFinalizedSelect);
	}
	public static function get_average_sum($data = array()){

      $election_id = Auth::user()->election_id;

      $sql_raw  = "IFNULL(ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2),0) as total_percent";
      $sql    = DB::table('pd_scheduledetail as sd1')->join('m_election_details as e',[
            ['e.ST_CODE', '=','sd1.st_code'],
            ['e.CONST_NO', '=','sd1.ac_no']
        ]);
      if(!empty($data['dist_no'])){
          $sql->join('m_ac as ac',[
            ['ac.ST_CODE', '=','e.ST_CODE'],
            ['ac.AC_NO', '=','e.CONST_NO']
        ]);
        }
      $sql->selectRaw($sql_raw);
      if(!empty($data['state'])){
        $sql->where("sd1.st_code", $data['state']);
      }

      if(!empty($data['phase'])){
        $sql->where("sd1.scheduleid", $data['phase']);
      }

      if(!empty($data['dist_no'])){
            $sql->where('ac.DIST_NO_HDQTR',$data['dist_no']);
      }

      if(!empty($data['pc_no'])){
        $sql->where("sd1.pc_no", $data['pc_no']);
      }

      $sql->where('e.CONST_TYPE','AC');
      $sql->where('e.election_status','1');
      $sql->where('e.ELECTION_ID',$election_id);

      if(!empty($data['group_by']) && in_array($data['group_by'],['pc_no','ac_no'])){
          if($data['group_by']=='pc_no'){
            $sql->groupBy("sd1.pc_no")->groupBy("sd1.st_code");
          }else if($data['group_by']=='ac_no'){
            $sql->groupBy("sd1.ac_no")->groupBy("sd1.st_code");
          }else{
          $sql->groupBy("sd1.st_code");
        }
      }

      $query = $sql->first();
      return ($query)?$query->total_percent:0;

    }
}
