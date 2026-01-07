<?php namespace App\models\Admin\Mis;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class ExGratiaEciModel extends Model
{
    
    protected $table = 'mis_exgratia_details';

    public $fillable = ['officer_id','officer_role','election_type','election_year'];

	
	
	public function getAcByst($stcode, $disttno) {
        $getAclist = DB::table('m_ac')->where('ST_CODE', $stcode)->where('DIST_NO_HDQTR', $disttno)->orderBy('AC_NO', 'asc')->get();
        return $getAclist;
    }
    public function getPcByst($stcode) {
        $getPclist = DB::table('m_pc')->where('ST_CODE', $stcode)->orderBy('PC_NO', 'asc')->get();
        return $getPclist;
    }
	
	public function getAllCases($st_code,$status=""){
		if(!empty($status)){
			$sql= ExGratiaEciModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
			->whereIn('mis_exgratia_details.st_code',explode(",",$st_code))
			->where('mis_exgratia_details.application_status',$status)
			->select('mis_exgratia_details.*','m_state.ST_NAME')->get();
		}else{
			$sql= ExGratiaEciModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
			->whereIn('mis_exgratia_details.st_code',explode(",",$st_code))
			->select('mis_exgratia_details.*','m_state.ST_NAME')->get();
		}
		
		return $sql;
	}
	public function getAllECICases($status=""){
		if(!empty($status)){
			$sql= ExGratiaEciModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
			->where('mis_exgratia_details.application_status',$status)
			->select('mis_exgratia_details.*','m_state.ST_NAME')->get();
		}else{
			$sql= ExGratiaEciModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
			->select('mis_exgratia_details.*','m_state.ST_NAME')->get();
		}
		
		return $sql;
	}
	
	public function getAllECICountReport($data=array()){
		$sql = DB::table('m_state')->leftjoin("mis_exgratia_details",[
					["m_state.ST_CODE","=","mis_exgratia_details.st_code"],
					])->leftjoin("mis_exgratia_non_cases",[
					["m_state.ST_CODE","=","mis_exgratia_non_cases.st_code"],
					])
					->selectRaw("m_state.st_code,m_state.ST_NAME as state_name,mis_exgratia_non_cases.nocases,mis_exgratia_details.updated_at,mis_exgratia_non_cases.record_date ,COUNT(mis_exgratia_details.id) AS cnt,
					SUM(IF(mis_exgratia_details.application_status = 'pending', 1, 0)) AS total_pending,
					SUM(IF((mis_exgratia_details.injury_details = '2' AND mis_exgratia_details.application_status= 'pending'), 1, 0)) AS total_death,
					SUM(IF((mis_exgratia_details.accident_reason = '2' AND mis_exgratia_details.application_status= 'pending'), 1, 0)) AS total_violent_act,
					SUM(IF((mis_exgratia_details.injury_details = '3' AND mis_exgratia_details.application_status= 'pending'), 1, 0)) AS total_permanent_disability");

		if(!empty($data['role'])){
		  if($data['role']==50){
			  $sql->whereIn('mis_exgratia_details.st_code',explode(",",$data['state']));
		  }else if($data['role']==7){
			  //$sql->where('mis_exgratia_details.st_code',$data['state']);
		  }else if($data['role']==4){
			  $sql->where('mis_exgratia_details.st_code',$data['state']);
		  }else if($data['role']==5){
			  $sql->where('mis_exgratia_details.st_code',$data['state']);
			  $sql->where('mis_exgratia_details.dist_no',$data['dist']);
		  }
		}
		//$sql->where('mis_exgratia_details.application_status','pending');
		$sql->groupBy('m_state.st_code');
		$sql->orderBy('m_state.st_name');
		$query =  $sql->get();
		//dd($query);
		return $query;
	}
		
}
