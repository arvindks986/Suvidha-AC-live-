<?php namespace App\models\Nomination;
use Illuminate\Database\Eloquent\Model;
use DB, Auth, Session;
use App\models\Nomination\{NominationProposerModel,NominationPoliceCaseModel, ProfileModel}; 
class PreScrutinyModel extends Model

{
    protected $table = 'candidate_prescrutiny_detail';
    protected $guarded  = [];

	public static function getall_list($filter=array()){
		$query_data = DB::table('nomination_application')->select('id','nomination_no', 'st_code', 'dist_no', 'ac_no',
						 'serial_no', 'part_no', 'is_apply_prescrutiny', 'prescrutiny_apply_datetime',
						 'prescrutiny_status', 'name', 'election_id', 'gender', 'age', 'image', 'address','epic_no')
						 ->where('finalize', '=', '1');
						//  ->where('is_apply_prescrutiny', '=', '1')
						//  ->where('prescrutiny_status', '=', '0');

		if(count($filter)>0){
			$query_data->where(array_only($filter, array('st_code', 'ac_no')));
		}
		if(count($filter['between'])>1){
			$query_data->whereBetween('prescrutiny_apply_datetime', [
				date('Y-m-d',strtotime($filter['between'][0])),
				date('Y-m-d',strtotime($filter['between'][1]))
			]);
		}
		if(!empty($filter['prescrutiny_status']) && $filter['prescrutiny_status'] != 'all'){
			$query_data->whereIn('prescrutiny_status', ($filter['prescrutiny_status']=='true' ? [1, 2] : [0]));
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

		$query_data = PreScrutinyModel::select('id', 'form_name', 'form_part_no', 'column_name', 'defect', 'remark', 'status', 'created_at', 'is_defect_resolved', 'defect_resolved_datetime')
					->where('nomination_id', '=', $nomination_id)->get()->toArray();
		return $query_data;
	}
}