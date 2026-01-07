<?php

namespace App\models\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB;

class OnlineNomModel extends Model
{
	protected $table 	= 'nomination_application';
    protected $guarded  = [];

	public static function get_stwise_count($filter=array()) {

		$fetch_data = OnlineNomModel::select('st_code', 'apply_date' ,DB::raw('COUNT("nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']]);
		$fetch_data->where([['party_id', '!=', '1180']])
		->where('finalize_after_payment', '=','1')
		->where('application_type', '=','2')
		->where('finalize', '=', '1');
		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [$date[0], $date[1]]);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('st_code', $filter['st_code']);
			$fetch_data->groupBy('st_code');
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('DIST_NO_HDQTR', $filter['dist_no']);
			$fetch_data->groupBy('st_code','DIST_NO_HDQTR');
		}
		if(!empty($filter['ac_no'])){
			$fetch_data->where('ac_no', $filter['ac_no']);
			$fetch_data->groupBy('st_code','DIST_NO_HDQTR','ac_no');
		}
		$final_data = $fetch_data->get()->toArray();

		return $final_data;
	}

	public static function get_count_offline($filter=array()) {
		$fetch_data = OnlineNomModel::select('nomination_application.st_code', 'm_ac.DIST_NO_HDQTR as dist_no','nomination_application.ac_no' ,DB::raw('COUNT("nomination_application.nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']])
		->join('m_election_details', [['nomination_application.st_code','=','m_election_details.st_code'],['nomination_application.ac_no','=','m_election_details.CONST_NO']]);
		$fetch_data->where([['party_id', '!=', '1180']])
		->where('finalize_after_payment', '=','1')
		->where('application_type', '=','2')
		->where('finalize', '=', '1')
		->where('m_election_details.CONST_TYPE', 'AC');
		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['election_type_id'])){
			$fetch_data->where('nomination_application.election_type_id', $filter['election_type_id']);
		}
		if(!empty($filter['election_phase'])){
			$fetch_data->where('m_election_details.StatePHASE_NO', $filter['election_phase']);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('DIST_NO_HDQTR', $filter['dist_no']);
			$fetch_data->groupBy('nomination_application.st_code','DIST_NO_HDQTR');
		}
		if(!empty($filter['ac_no'])){
			$fetch_data->where('nomination_application.ac_no', $filter['ac_no']);
			// $fetch_data->groupBy('st_code','dist_no','ac_no');
		}
		$final_data = $fetch_data->groupBy('nomination_application.st_code')->get()->toArray();
		return $final_data;
	}

	public static function get_count_offline_dist($filter=array()) {
		$fetch_data = OnlineNomModel::select('nomination_application.st_code', 'm_ac.DIST_NO_HDQTR as dist_no' ,DB::raw('COUNT("nomination_application.nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']])
		->join('m_election_details', [['nomination_application.st_code','=','m_election_details.st_code'],['nomination_application.ac_no','=','m_election_details.CONST_NO']])
		->where([['party_id', '!=', '1180']])
		->where('finalize_after_payment', '=','1')
		->where('application_type', '=','2')
		->where('finalize', '=', '1')
		->where('m_election_details.CONST_TYPE', 'AC');
		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['election_type_id'])){
			$fetch_data->where('nomination_application.election_type_id', $filter['election_type_id']);
		}
		if(!empty($filter['election_phase'])){
			$fetch_data->where('m_election_details.ScheduleID', $filter['election_phase']);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
			$fetch_data->groupBy('nomination_application.st_code','DIST_NO_HDQTR');
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('DIST_NO_HDQTR', $filter['dist_no']);
		}
		$final_data = $fetch_data->get()->toArray();
		return $final_data; 
	}

	public static function get_count_offline_ac($filter=array()) {
		$fetch_data = OnlineNomModel::select('nomination_application.st_code', 'm_ac.DIST_NO_HDQTR as dist_no','nomination_application.ac_no' ,DB::raw('COUNT("nomination_application.nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']])
		->join('m_election_details', [['nomination_application.st_code','=','m_election_details.st_code'],['nomination_application.ac_no','=','m_election_details.CONST_NO']]);
		$fetch_data->where([['party_id', '!=', '1180']])
		->where('finalize_after_payment', '=','1')
		->where('application_type', '=','2')
		->where('finalize', '=', '1')
		->where('m_election_details.CONST_TYPE', 'AC');
		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['election_type_id'])){
			$fetch_data->where('nomination_application.election_type_id', $filter['election_type_id']);
		}
		if(!empty($filter['election_phase'])){
			$fetch_data->where('m_election_details.ScheduleID', $filter['election_phase']);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
			$fetch_data->groupBy('nomination_application.st_code','m_ac.DIST_NO_HDQTR','nomination_application.ac_no');
		}

		$final_data = $fetch_data->get()->toArray();

		return $final_data;
	}

	public static function get_count_only($filter=array()) { 
		$fetch_data = OnlineNomModel::select('1')
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']])
		->join('m_election_details', [['nomination_application.st_code','=','m_election_details.st_code'],['nomination_application.ac_no','=','m_election_details.CONST_NO']])
		->where([['party_id', '!=', '1180']])
		->where('finalize_after_payment', '=','1')
		->where('application_type', '=','2')
		->where('finalize', '=', '1')
		->where('m_election_details.CONST_TYPE', 'AC');
		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}

		if(!empty($filter['election_type_id'])){
			$fetch_data->where('nomination_application.election_type_id', $filter['election_type_id']);
		}
		if(!empty($filter['election_phase'])){
			$fetch_data->where('m_election_details.StatePHASE_NO', $filter['election_phase']);
		}

		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
		}
		if(!empty($filter['ac_no'])){
			$fetch_data->where('nomination_application.ac_no', $filter['ac_no']);
		}

		$final_data = $fetch_data->count();

		return $final_data;
	}

	public static function get_count_offline_nom($filter=array()){
		$fetch_data = DB::table('candidate_nomination_detail')
		->join('m_ac', [['candidate_nomination_detail.st_code','=','m_ac.st_code'],['candidate_nomination_detail.ac_no','=','m_ac.ac_no']])
		->whereNotIn('nomination_no', function($query){
			$query->select('nomination_no')->from('nomination_application');
		});
		if(!empty($filter['st_code'])){
			$fetch_data->where('candidate_nomination_detail.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
		}
		if(!empty($filter['ac_no'])){
			$fetch_data->where('candidate_nomination_detail.ac_no', $filter['ac_no']);
		}
		$result = $fetch_data->where('rosubmit_date', '!=', null)->count();
		return $result;
	}
	
	public static function get_physical_verification_count($filter=array()) {
		$fetch_data = DB::table('candidate_nomination_detail')->select('candidate_nomination_detail.nomination_no')
		->join('m_ac', [['candidate_nomination_detail.st_code','=','m_ac.st_code'],['candidate_nomination_detail.ac_no','=','m_ac.ac_no']])
		->where('rosubmit_date', '!=', null);
		if(!empty($filter['st_code'])){
			$fetch_data->where('candidate_nomination_detail.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
		}
		if(!empty($filter['ac_no'])){
			$fetch_data->where('candidate_nomination_detail.ac_no', $filter['ac_no']);
		}
		$result = $fetch_data->orderBy('candidate_nomination_detail.ac_no')->get()->count();
		return $result;
	}

	################################ Party Wise Report Function #################################

	public static function getall_st_party_wise($filter=array()) {

		$fetch_data = OnlineNomModel::select('st_code' ,DB::raw('COUNT("nomination_no") as total_count'));
		$fetch_data->where([['party_id', '!=', '1180']]);

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('st_code', $filter['st_code']);
		}

		$final_data = $fetch_data->groupBy('st_code')->get()->toArray();

		return $final_data;
	}

	public static function getall_dist_party_wise($filter=array()) { 

		$fetch_data = OnlineNomModel::select('nomination_application.st_code', 'm_ac.DIST_NO_HDQTR as dist_no' ,DB::raw('COUNT("nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']]);
		$fetch_data->where([['party_id', '!=', '1180']]);

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
			$fetch_data->groupBy('nomination_application.st_code','dist_no');
		}

		if(!empty($filter['dist_no'])){
			$fetch_data->where('dist_no', $filter['dist_no']);
		}

		$final_data = $fetch_data->get()->toArray();

		return $final_data;
	}

	public static function getall_ac_party_wise($filter=array()){ 
		$fetch_data = OnlineNomModel::select('st_code', 'm_ac.DIST_NO_HDQTR as dist_no','ac_no' ,DB::raw('COUNT("nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']]);
		$fetch_data->where([['party_id', '!=', '1180']]);

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		
		if(!empty($filter['st_code'])){
			$fetch_data->where('st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('dist_no', $filter['dist_no']);
			$fetch_data->groupBy('st_code','dist_no','ac_no');
		}

		$final_data = $fetch_data->get()->toArray();

		return $final_data;
	}

	public static function get_count_party_wise($filter=array()) {
		$fetch_data = OnlineNomModel::select('1')
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']]);
		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
		}
		if(!empty($filter['ac_no'])){
			$fetch_data->where('nomination_application.ac_no', $filter['ac_no']);
		}

		if(isset($filter['recognized_party'])){
			$fetch_data->whereIn('recognized_party', $filter['recognized_party']);
		}

		$final_data = $fetch_data->distinct()->count();

		return $final_data;
	}

	################################ Payment Wise Report Function #################################

	public static function getall_st_payment_wise($filter=array()) {

		$fetch_data = OnlineNomModel::select('st_code' ,DB::raw('COUNT("nomination_no") as total_count'));
		$fetch_data->where([['party_id', '!=', '1180']])
		->where('application_type', '=','2')
		->where('finalize', '=', '1');

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('st_code', $filter['st_code']);
		}

		$final_data = $fetch_data->groupBy('st_code')->get()->toArray();

		return $final_data;
	}

	public static function getall_dist_wise_payment($filter=array()) { 

		$fetch_data = OnlineNomModel::select('nomination_application.st_code', 'm_ac.DIST_NO_HDQTR as dist_no' ,DB::raw('COUNT("nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']]);
		$fetch_data->where([['party_id', '!=', '1180']])
		->where('application_type', '=','2')
		->where('finalize', '=', '1');

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
			$fetch_data->groupBy('nomination_application.st_code','DIST_NO_HDQTR');
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('DIST_NO_HDQTR', $filter['dist_no']);
		}

		$final_data = $fetch_data->get()->toArray();

		return $final_data;
	}

	public static function getall_ac_wise_payment($filter=array()){ 
		$fetch_data = OnlineNomModel::select('nomination_application.st_code', 'm_ac.DIST_NO_HDQTR as dist_no','nomination_application.ac_no' ,DB::raw('COUNT("nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']]);
		$fetch_data->where([['party_id', '!=', '1180']])->where('application_type', '=','2')
		->where('finalize', '=', '1');

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
			$fetch_data->groupBy('nomination_application.st_code','m_ac.DIST_NO_HDQTR','nomination_application.ac_no');
		}

		$final_data = $fetch_data->get()->toArray();

		return $final_data;
	}

	public static function get_count_payment_wise($filter=array()) {
		$fetch_data = OnlineNomModel::select('nomination_application.*')
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']])
		->where([['party_id', '!=', '1180']])
		->where('application_type', '=','2')
		->where('finalize', '=', '1');

		if($filter['mode']=='online'){
			$fetch_data->join('payment_details_common', [
				['nomination_application.st_code', '=', 'payment_details_common.st_code'], 
				['nomination_application.candidate_id', '=', 'payment_details_common.candidate_id']
			])->where('payment_details_common.bank_transaction_status', '=', '1');
		}

		if($filter['mode']==''){
			$finalize = ($filter['finalize']=='1') ? '1' : '0' ;
			$fetch_data->where('nomination_application.finalize_after_payment', '=', $finalize);
		}

		if($filter['mode']=='challan'){
			$fetch_data->select('challan_payment.challan_receipt')->join('challan_payment', [
				['nomination_application.st_code', '=', 'challan_payment.st_code'], 
				['nomination_application.candidate_id', '=', 'challan_payment.candidate_id']
			])->where('challan_receipt', '<>', '');
		}

		if($filter['mode']=='cash'){
			$fetch_data->select('challan_payment.payByCash', 'challan_payment.pay_by_cash_paid', 'challan_payment.date_time_of_pbc')->join('challan_payment', [
				['nomination_application.st_code', '=', 'challan_payment.st_code'], 
				['nomination_application.candidate_id', '=', 'challan_payment.candidate_id']
			])
			->where('payByCash', '=', '1')
			->where('pay_by_cash_paid', '=', '1')
			->where('date_time_of_pbc', '!=', null);
		}

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
		}
		if(!empty($filter['ac_no'])){
			$fetch_data->where('nomination_application.ac_no', $filter['ac_no']);
		}

		$final_data = $fetch_data->groupBy('nomination_application.candidate_id')->get()->count(); //dd($final_data);
		return $final_data;
	}

	################################ Physical Verification Report Function #################################

	public static function getall_st($filter=array()) {

		$fetch_data = OnlineNomModel::select('st_code' ,DB::raw('COUNT("nomination_no") as total_count'));
		$fetch_data->where([['party_id', '!=', '1180']])
		->where('finalize_after_payment', '=','1')
		->where('application_type', '=','2')
		->where('finalize', '=', '1');

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('st_code', $filter['st_code']);
		}

		$final_data = $fetch_data->groupBy('st_code')->get()->toArray();

		return $final_data;
	}

	public static function getall_dist_wise_physicalverification($filter=array()) { 

		$fetch_data = OnlineNomModel::select('nomination_application.st_code', 'm_ac.DIST_NO_HDQTR as dist_no' ,DB::raw('COUNT("nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']]);
		$fetch_data->where([['party_id', '!=', '1180']])
		->where('application_type', '=','2')
		->where('finalize_after_payment', '=','1')
		->where('finalize', '=', '1');

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
			$fetch_data->groupBy('nomination_application.st_code','DIST_NO_HDQTR');
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('DIST_NO_HDQTR', $filter['dist_no']);
		}

		$final_data = $fetch_data->get()->toArray();

		return $final_data;
	}

	public static function getall_ac_wise_physicalverification($filter=array()){ 
		$fetch_data = OnlineNomModel::select('nomination_application.st_code', 'm_ac.DIST_NO_HDQTR as dist_no','nomination_application.ac_no' ,DB::raw('COUNT("nomination_no") as total_count'))
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']]);
		$fetch_data->where([['party_id', '!=', '1180']])
		->where('application_type', '=','2')
		->where('finalize_after_payment', '=','1')
		->where('finalize', '=', '1');

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
			$fetch_data->groupBy('nomination_application.st_code','m_ac.DIST_NO_HDQTR','nomination_application.ac_no');
		}

		$final_data = $fetch_data->get()->toArray();

		return $final_data;
	}

	public static function get_count_physicalverification_wise($filter=array()) {
		$fetch_data = OnlineNomModel::select('nomination_application.*')
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']])
		->where([['party_id', '!=', '1180']])
		->where('application_type', '=','2')
		->where('finalize', '=', '1')
		->where('finalize_after_payment', '=','1');

		if($filter['status']=='done'){
			$fetch_data->where('is_physical_verification_done', '=', '1');
		}elseif($filter['status']=='pend'){
			$fetch_data->where('is_physical_verification_done', '=', '0');
		}

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
		}
		if(!empty($filter['ac_no'])){
			$fetch_data->where('nomination_application.ac_no', $filter['ac_no']);
		}

		$final_data = $fetch_data->distinct()->count();
		return $final_data;
	}

	################################ Appointment Report Function #################################

	public static function get_count_appointment_wise($filter=array()) {
		$fetch_data = OnlineNomModel::select('nomination_application.*')
		->join('m_ac', [['nomination_application.st_code','=','m_ac.st_code'],['nomination_application.ac_no','=','m_ac.ac_no']])
		->where([['party_id', '!=', '1180']])
		->where('application_type', '=','2')
		->where('finalize', '=', '1')
		->where('finalize_after_payment', '=','1')
		->join('appointment_schedule_date_time', [
			['nomination_application.candidate_id', '=', 'appointment_schedule_date_time.candidate_id'],
			['nomination_application.st_code', '=', 'appointment_schedule_date_time.st_code'],
			['nomination_application.ac_no', '=', 'appointment_schedule_date_time.ac_no']]);

		if($filter['appointment']=='done'){
			$fetch_data->where('is_ro_acccept', '1');
		}elseif($filter['appointment']=='pend'){
			$fetch_data->where('is_ro_acccept', '0');
		}

		if(!empty($filter['date'])){
			$date = explode(' - ' ,$filter['date']);
			$fetch_data->whereBetween('apply_date', [date('Y-m-d',strtotime($date[0])), date('Y-m-d', strtotime($date[1]))]);
		}
		if(!empty($filter['st_code'])){
			$fetch_data->where('nomination_application.st_code', $filter['st_code']);
		}
		if(!empty($filter['dist_no'])){
			$fetch_data->where('m_ac.DIST_NO_HDQTR', $filter['dist_no']);
		}
		if(!empty($filter['ac_no'])){
			$fetch_data->where('nomination_application.ac_no', $filter['ac_no']);
		}

		$final_data = $fetch_data->groupBy('appointment_schedule_date_time.candidate_id')->get()->count();
		return $final_data;
	}

	public static function get_count_appointment_com($filter=array()){
		$result=NominationApplicationModel::join('m_ac', [['nomination_application.st_code', '=', 'm_ac.ST_CODE'], ['nomination_application.ac_no', '=', 'm_ac.AC_NO']])
		->where('application_type','2')
		->where('finalize', '=', '1')
		->where('finalize_after_payment', '1')
		->join('appointment_schedule_date_time', [
			['nomination_application.candidate_id', '=', 'appointment_schedule_date_time.candidate_id'],
			['nomination_application.st_code', '=', 'appointment_schedule_date_time.st_code'],
			['nomination_application.ac_no', '=', 'appointment_schedule_date_time.ac_no']]);
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
            $result->where('is_ro_acccept', '1');
        }elseif($filter['fil_status'] == '2'){
            $result->where('is_ro_acccept', '0');
        }
		$results = $result->get()->groupBy('nomination_application.candidate_id')->count();
		return $results;
	}
}
