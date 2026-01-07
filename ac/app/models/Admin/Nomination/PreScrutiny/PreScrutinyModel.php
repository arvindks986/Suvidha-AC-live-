<?php

namespace App\models\Admin\Nomination\PreScrutiny;
use DB;
use Auth;
use App\models\Admin\Nomination\NominationApplicationModel;
use Illuminate\Database\Eloquent\Model;

class PreScrutinyModel extends Model
{
    protected $table = 'candidate_prescrutiny_detail';
    protected $guarded  = [];

	public static function getall_list($filter=array()){
		$query_data = DB::table('nomination_application')->select('id','nomination_no', 'st_code', 'dist_no', 'ac_no',
						 'serial_no', 'part_no', 'is_apply_prescrutiny', 'prescrutiny_apply_datetime', 'prescrutiny_status_date',
						 'prescrutiny_status', 'name', 'hname', 'party_id', 'father_name', 'father_hname','election_id', 'gender', 'age', 'image', 'address','epic_no')
						 ->where('finalize', '=', '1');
						//  ->where('is_apply_prescrutiny', '=', '1')
						//  ->where('prescrutiny_status', '=', '0');

		if(count($filter)>0){
			$query_data->where(array_only($filter, array('st_code', 'ac_no')));
		}
		if(count($filter['between'])>1){
			$query_data->whereBetween('prescrutiny_apply_datetime', [
				date('Y-m-d',strtotime($filter['between'][0]))." 00:00:00",
				date('Y-m-d',strtotime($filter['between'][1]))." 23:59:59"
			]);
		}
		if(!empty($filter['prescrutiny_status']) && $filter['prescrutiny_status'] != 'all'){
			if($filter['prescrutiny_status']=='true'){
				$query_data->whereIn('prescrutiny_status', [1, 2]);
			}else{
				$query_data->whereNull('prescrutiny_status')->orWhereIn('prescrutiny_status', [0]);
			}
		}

		if(!empty($filter['prescrutiny_status_clear']) && $filter['prescrutiny_status_clear'] != 'all'){
			$query_data->where('prescrutiny_status', ($filter['prescrutiny_status_clear']=='true' ? 1 : 2));
		}
		$response_data = $query_data->latest()->get()->all();
		$response_data = array_map(function ($value) {
			return (array)$value;
		}, (Array)$response_data);
		return $response_data;
	}

	public static function get_all_appointment_request($filter=array()){
		$user_state_code = Auth::user()->st_code;
		$user_ac_no = Auth::user()->ac_no;
		$query_data = DB::table('nomination_application')->select('id', 'candidate_id', 'st_code', 'dist_no', 'ac_no',
						 'serial_no', 'part_no',
						 'prescrutiny_status', 'name', 'hname', 'father_name', 'father_hname','election_id', 'gender', 'age', 'image', 'address','epic_no')
						 ->where('finalize', '=', '1')
						 ->where('finalize_after_payment', '1')
						 ->whereIn('candidate_id',function($query)use($user_state_code, $user_ac_no){
							$query->select('candidate_id')->from('appointment_schedule_date_time')
							->where(['st_code' => $user_state_code,
									'ac_no'   => $user_ac_no,
									'status'  => '1'			
							]);
						 });

		if(count($filter)>0){
			$query_data->where(array_only($filter, array('st_code', 'ac_no')));
		}

		// if(count($filter['between'])>1){
		// 	$query_data->whereBetween('prescrutiny_apply_datetime', [
		// 		date('Y-m-d',strtotime($filter['between'][0]))." 00:00:00",
		// 		date('Y-m-d',strtotime($filter['between'][1]))." 23:59:59"
		// 	]);
		// }

		$response_data = $query_data->latest()->groupBy('candidate_id')->get()->all();
		$response_data = array_map(function ($value) {
			return (array)$value;
		}, (Array)$response_data);
		return $response_data;
	}

	public static function get_count_appointment($filter=array()){
		$user_state_code = Auth::user()->st_code;
		$user_ac_no = Auth::user()->ac_no;
		$query_data = DB::table('nomination_application')->select('id', 'candidate_id', 'st_code', 'dist_no', 'ac_no',
						 'serial_no', 'part_no',
						 'prescrutiny_status', 'name', 'hname', 'father_name', 'father_hname','election_id', 'gender', 'age', 'image', 'address','epic_no')
						 ->where('finalize', '=', '1')
						 ->where('finalize_after_payment', '1')
						 ->whereIn('candidate_id',function($query)use($user_state_code, $user_ac_no, $filter){
							$query->select('candidate_id')->from('appointment_schedule_date_time')
							->where(['st_code' => $user_state_code,
									'ac_no'   => $user_ac_no			
							]);
							if($filter['is_ro_acccept'] == '1'){
								$query->where('is_ro_acccept', '1');
							}elseif($filter['is_ro_acccept'] == '2'){
								$query->where('is_ro_acccept', '0');
							}
						 });

		if(count($filter)>0){
			$query_data->where(array_only($filter, array('st_code', 'ac_no')));
		}

		$response_data = $query_data->latest()->groupBy('candidate_id')->get()->count();
		return $response_data;
	}

	public static function get_count_application($filter=array()){
		$result=DB::table('nomination_application')->where('st_code', $filter['ele_details']->ST_CODE)
        ->where('election_id', $filter['ele_details']->ELECTION_ID)
        ->where('ac_no', $filter['ele_details']->CONST_NO)
		->where('application_type','2')
		->where('finalize', '=', '1')
		->where('finalize_after_payment', '1');
        if($filter['fil_status'] == '1'){
            $result->where('is_physical_verification_done', '1');
        }elseif($filter['fil_status'] == '2'){
            $result->where('is_physical_verification_done', '0');
        }
		$results = $result->get()->count();
		return $results;
	}

	public static function get_count_application_com($filter=array()){
		$result=NominationApplicationModel::join('m_ac', [['nomination_application.st_code', '=', 'm_ac.ST_CODE'], ['nomination_application.ac_no', '=', 'm_ac.AC_NO']])
		->join('m_election_details', [['nomination_application.st_code','=','m_election_details.st_code'],['nomination_application.ac_no','=','m_election_details.CONST_NO']])
		->where('application_type','2')
		->where('finalize', '=', '1')
		->where('finalize_after_payment', '1')
		->where('m_election_details.CONST_TYPE', 'AC');
		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$result->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['election_type_id'])){
			$result->where('nomination_application.election_type_id', $filter['election_type_id']);
		}
		if(!empty($filter['election_phase'])){
			$result->where('m_election_details.ScheduleID', $filter['election_phase']);
		}
		if(!empty($filter['st_code'])){
			$result->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
            $result->where('m_ac.DIST_NO_HDQTR', '=', $filter['dist_no']);
        }
		if(!empty($filter['ac_no'])){
			$result->where('nomination_application.ac_no', $filter['ac_no']);
		}
        if($filter['fil_status'] == '1'){
            $result->where('is_physical_verification_done', '1');
        }elseif($filter['fil_status'] == '2'){
            $result->where('is_physical_verification_done', '0');
        }
		$results = $result->get()->count();
		return $results;
	}

	public static function get_all_nomination_details($candidate_id){
		$user_state_code = Auth::user()->st_code;
		$user_ac_no = Auth::user()->ac_no;
		$query_data = DB::table('nomination_application')->select('id', 'nomination_no', 'recognized_party', 'party_id', 'party_id2', 'prescrutiny_status', 'prescrutiny_status_date','candidate_id')
		->where('finalize', '=', '1')->where('candidate_id', $candidate_id)
		->where(['st_code' => $user_state_code,
				 'ac_no'   => $user_ac_no			
		])->latest()->get();

		return $query_data;
	}

	public static function get_all_appointment_details($candidate_id){ 
		$user_state_code = Auth::user()->st_code;
		$user_ac_no = Auth::user()->ac_no;
		$query_data = DB::table('appointment_schedule_date_time')->select('candidate_id', 'appointment_date', 'appointment_time', 'st_code', 'ac_no', 'is_ro_acccept')
		->where('status', '=', '1')
		->where('candidate_id', $candidate_id)
		->where(['st_code' => $user_state_code,
				 'ac_no'   => $user_ac_no			
		])->latest()->get();

		return $query_data;
	}

	public static function getelectionnamebyid($id){
		$query_data = DB::table('m_election_history')->select('description')->where('election_id', $id)->first();
		return !empty($query_data) ? $query_data->description : '';
	}

	public static function get_nominations(){
		$result =  DB::table('nomination_application')->join('m_election_details as election',[
		  ['election.ST_CODE','=','nomination_application.st_code']
		])->join('m_state','m_state.ST_CODE','=','nomination_application.st_code')->join('m_ac',[
		  ['m_ac.ST_CODE','=','nomination_application.st_code'],
		  ['m_ac.AC_NO','=','nomination_application.ac_no'],
		])->where('candidate_id' , Auth::id())->where('election.election_status','1')->selectRaw("nomination_application.*, CONCAT(election.ELECTION_TYPE,'-',election.YEAR) as election_name, m_state.ST_NAME as st_name, m_ac.AC_NAME as ac_name, finalize, nomination_no")
		->groupBy('nomination_application.id')->orderBy('id','desc')->get()->toArray();
		return $result;
	}

	public static function get_nomination($id){
		$data = [];
		$object = DB::table('nomination_application')->join('profile','profile.candidate_id','=','nomination_application.candidate_id')
		->where('nomination_application.id',$id)
		->select('nomination_application.*','nomination_application.id as nomination_id')
		->first();
		if(!$object){
		  return false;
		}
		return (Array)$object;
	}

	public static function get_last_nomination_application(){
		return DB::table('nomination_application')->where('candidate_id', Auth::id())->latest('id')->first()->toArray();
	  }
	
	  public static function get_nomination_application($id){
		$object = DB::table('nomination_application')->where('id', $id)->first();
		if(!$object){
		  return false;
		}
		return (Array)$object;
	  }
	  public static function count_nomination_application($data = array()){ 
	  
		$sql = DB::table('nomination_application')->where([
			'candidate_id' => Auth::id(),
			'st_code' => $data['st_code'], 
			'ac_no' => $data['ac_no'],
		]);	
		
		if(isset($data['nomination_id'])){ 
			//$req  = decrypt_String($data['nomination_id']); 
			//$sql->where('id', '!=', $req);
		}
		
		if(Session::has('nomination_id')){ 
			$sql->where('id', '!=', Session::get('nomination_id'));
		}
		
		return $sql->count();
	  }

	  public static function getformated($single_str) {
		if($single_str == 'I'){
			return 1;
		}
		elseif($single_str == 'II'){
			return 2;
		}
		elseif($single_str == 'III'){
			return 3;
		}
		elseif($single_str == 'IIIA'){
			return 4;
		}
		elseif($single_str == 'affidavit'){
			return 5;
		}
		elseif($single_str == 'finalize'){
			return 6;
		}
		elseif($single_str == 'prs_det'){
			return 0;
		}
	  }

	  public static function getformated_part($single_str) {
		if($single_str == '1'){
			return 'I';
		}
		elseif($single_str == '2'){
			return 'II';
		}
		elseif($single_str == '3'){
			return 'III';
		}
		elseif($single_str == '4'){
			return 'IIIA';
		}
	  }

	public static function get_comment_data_by_id($nomination_id){

		$query_data = PreScrutinyModel::select('form_name', 'form_part_no', 'column_name', 'defect', 'remark', 'status', 'is_defect_resolved', 'defect_resolved_datetime', 'created_at')
					->where('nomination_id', '=', $nomination_id)->get()->toArray();
		return $query_data;
	}
}