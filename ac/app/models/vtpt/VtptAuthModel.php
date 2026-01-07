<?php

namespace App\models\vtpt;

use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class VtptAuthModel extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'polling_station_officer_vtpt';

    protected $guarded = [];

    public static function otp_attempt($userid,$attempt_value){
      VtptAuthModel::where('id', $userid)->update(array('OTP_attempt' => $attempt_value));
    }

    public static function verify_otp($userid,$mobno,$datamob){
      VtptAuthModel::where(['id' =>  $userid, 'mobile_number' => $mobno])->update($datamob);
    }
	
	public static function get_polldate_by_acno($state_code,$ac_no){
		$sql = DB::table('m_st_schedule')
            ->join('m_schedule', 'm_st_schedule.SCHEDULEID', '=', 'm_schedule.SCHEDULEID')
			->where('m_st_schedule.ST_CODE','=',$state_code)
			->where('m_st_schedule.CONST_NO','=',$ac_no)
            ->select('m_schedule.DATE_POLL')
            ->first();
		return $sql;
	}
	
	public static function get_mapped_ps_sectorid($st_code,$ac_no,$dist_no,$sector_id){
		$sql = DB::table('sector_ac_ps_mapping')
			->where('st_code','=',$st_code)
			->where('ac_no','=',$ac_no)
			//->where('dist_no','=',$dist_no)
			->where('sector_id','=',$sector_id)
            ->implode('ps_no',',');
		return $sql;
	}
	
	public static function get_election_typeid($st_code,$ac_no){
		$sql=DB::table('m_election_details')
		->where('ST_CODE','=',$st_code)
		->where('CONST_NO','=',$ac_no)
		->select('ELECTION_TYPEID')
		->first();
		return $sql;
	} 

}
