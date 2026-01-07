<?php namespace App\models\Admin\Mis;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class ExGratiaDeoModel extends Model
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
	
	public function getAllCases($st_code,$dist_no){
		$sql= ExGratiaDeoModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
		->leftJoin('m_district', 'm_district.DIST_NO', '=', 'mis_exgratia_details.dist_no')
		->where('m_district.st_code',$st_code)
		->where('mis_exgratia_details.st_code',$st_code)
		->where('mis_exgratia_details.dist_no',$dist_no)
		->select('mis_exgratia_details.*','m_state.ST_NAME','m_district.DIST_NAME')->get();
		return $sql;
	}
	public function getPendingCases($st_code,$dist_no){
		$sql= ExGratiaDeoModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
		->leftJoin('m_district', 'm_district.DIST_NO', '=', 'mis_exgratia_details.dist_no')
		->where('m_district.st_code',$st_code)
		->where('mis_exgratia_details.st_code',$st_code)->where('mis_exgratia_details.application_status','pending')
		->where('mis_exgratia_details.dist_no',$dist_no)
		->select('mis_exgratia_details.*','m_state.ST_NAME','m_district.DIST_NAME')->get();
		return $sql;
	}
	public function getGrantedCases($st_code,$dist_no){
		$sql= ExGratiaDeoModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
		->leftJoin('m_district', 'm_district.DIST_NO', '=', 'mis_exgratia_details.dist_no')
		->where('m_district.st_code',$st_code,$dist_no)
		->where('mis_exgratia_details.st_code',$st_code)->where('mis_exgratia_details.application_status','granted')
		->where('mis_exgratia_details.dist_no',$dist_no)
		->select('mis_exgratia_details.*','m_state.ST_NAME','m_district.DIST_NAME')->get();
		return $sql;
	}
	public function getRejectedCases($st_code,$dist_no){
		$sql= ExGratiaDeoModel::leftJoin('m_state', 'm_state.ST_CODE', '=', 'mis_exgratia_details.st_code')
		->leftJoin('m_district', 'm_district.DIST_NO', '=', 'mis_exgratia_details.dist_no')
		->where('m_district.st_code',$st_code)
		->where('mis_exgratia_details.st_code',$st_code)->where('mis_exgratia_details.application_status','rejected')
		->where('mis_exgratia_details.dist_no',$dist_no)
		->select('mis_exgratia_details.*','m_state.ST_NAME','m_district.DIST_NAME')->get();
		return $sql;
	}
	
		
}