<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\commonModel;
use App\adminmodel\ECIModel;
use App\Classes\xssClean;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

ini_set("memory_limit", "1500M");
set_time_limit('2400');
ini_set("pcre.backtrack_limit", "10000000");


class PublishController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

	public $commonModel = null;
	public $ECIModel = null;
	public $xssClean = null;
	public function __construct()
	{
		$this->commonModel = new commonModel();
		$this->ECIModel = new ECIModel();
		$this->xssClean = new xssClean;
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	/*     protected function guard(){
        return Auth::guard();
    } */


	public function update_turnout_index(Request $request)
	{
		try {
			$date = date('Y-m-d');
			$schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
				->where('DATE_POLL', '=', $date)
				->orderBy('DATE_POLL', 'DESC')
				->get();
			foreach ($schedule as $key => $value) {
				if ($value) {
					$scheduleid = $value->SCHEDULEID;
					$dataArr = DB::table('pd_scheduledetail')
						->where('scheduleid', $scheduleid)
						->get();

					foreach ($dataArr as $raw) {
						DB::table('pd_scheduledetail_publish')
							->where('st_code', $raw->st_code)
							->where('ac_no', $raw->ac_no)
							->where('scheduleid', $scheduleid)
							->update([
								'est_turnout_round1' 	=> $raw->est_turnout_round1,
								'est_turnout_round2' 	=> $raw->est_turnout_round2,
								'est_turnout_round3' 	=> $raw->est_turnout_round3,
								'est_turnout_round4' 	=> $raw->est_turnout_round4,
								'est_turnout_round5' 	=> $raw->est_turnout_round5,
								'est_turnout_total' 	=> $raw->est_turnout_total,
								'close_of_poll' 		=> $raw->close_of_poll,
								'electors_total' 		=> $raw->electors_total,
								'est_voters' 			=> $raw->est_voters
							]);

						//echo $raw->st_code.'-AC-'.$raw->ac_no.'-Phase-'.$raw->scheduleid.'<br>';				
					}
				} else {
					echo 'No Schedule found <br/>';
				}
			}


			if (count($schedule) > 0) {
				$getRecord = DB::table('app_vtr_message')->where('status', '1')->first();
				$current_id = $getRecord->id + 1;
				DB::table('app_vtr_message')->update(['status' => '0']);
				DB::table('app_vtr_message')->where('id', $current_id)->update(['status' => '1']);
				echo 'Updation done';
			}
		} catch (Exception $ex) {
			Log::error($ex);
			die($ex->getMessage());
		}
	}



	public function show_turnout_index(Request $request)
	{
		$date = date('Y-m-d');

		$schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
			->where('DATE_POLL', '=', $date)
			->orderBy('DATE_POLL', 'DESC')
			->get();
		if ($schedule) {
			$scheduleids = [];
			foreach ($schedule as $key => $value) {
				$scheduleids[] = $value->SCHEDULEID;
			}
			$results_data = [];
			$results_data = DB::table('pd_scheduledetail as pds')
				->join('pd_scheduledetail_publish as pds_temp', [['pds.st_code', '=', 'pds_temp.st_code'], ['pds.ac_no', '=', 'pds_temp.ac_no']])
				->select('pds.st_code', 'pds.ac_no', 'pds.scheduleid', 'pds.est_turnout_total', 'pds.electors_total', 'pds.est_voters', 'pds_temp.est_turnout_total as est_turnout_total_temp', 'pds_temp.electors_total as electors_total_temp', 'pds_temp.est_voters as est_voters_temp')
				->whereIn('pds.scheduleid', $scheduleids)
				->groupBy('pds.st_code', 'pds.ac_no')
				->get()->toArray();

			return view('admin.turnout.update_turnout.update_trunout_app', ['results_data' => $results_data]);
		} else {
			echo 'No Schedule found';
		}
	}

	function closedMissedEntryWindows()
	{
		try {
			$date = date('Y-m-d');

			$schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
				->where('DATE_POLL', '=', $date)
				->orderBy('DATE_POLL', 'DESC')
				->get();
			foreach ($schedule as $key => $value) {
				if ($value) {
					$scheduleid = $value->SCHEDULEID;
					$dataArr = DB::table('pd_scheduledetail')
						->where('scheduleid', $scheduleid)
						->where(function ($q) {
							$q->orWhere('missed_status_round1', 1);
							$q->orWhere('missed_status_round2', 1);
							$q->orWhere('missed_status_round3', 1);
							$q->orWhere('missed_status_round4', 1);
							$q->orWhere('missed_status_round5', 1);
							$q->orWhere('missed_status_round6', 1);
						})
						->get();

					foreach ($dataArr as $raw) {
						DB::table('pd_scheduledetail')
							->where('id', $raw->id)
							->update([
								'missed_status_round1' 	=> 0,
								'missed_status_round2' 	=> 0,
								'missed_status_round3' 	=> 0,
								'missed_status_round4' 	=> 0,
								'missed_status_round5' 	=> 0,
								'missed_status_round6' 	=> 0,
							]);
					}
				} else {
					echo 'No Schedule found <br/>';
				}
			}


			if (count($schedule) > 0) {
				echo 'Missed Entry Blocked done';
			}
		} catch (Exception $ex) {
			Log::error($ex);
			die($ex->getMessage());
		}
	}

	function closedModifyEntryWindows()
	{
		try {
			$date = date('Y-m-d');

			$schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
				->where('DATE_POLL', '=', $date)
				->orderBy('DATE_POLL', 'DESC')
				->get();
			foreach ($schedule as $key => $value) {
				if ($value) {
					$scheduleid = $value->SCHEDULEID;
					$dataArr = DB::table('pd_scheduledetail')
						->where('scheduleid', $scheduleid)
						->where(function ($q) {
							$q->orWhere('modification_status_round1', 1);
							$q->orWhere('modification_status_round2', 1);
							$q->orWhere('modification_status_round3', 1);
							$q->orWhere('modification_status_round4', 1);
							$q->orWhere('modification_status_round5', 1);
							$q->orWhere('modification_status_round6', 1);
						})
						->get();

					foreach ($dataArr as $raw) {
						DB::table('pd_scheduledetail')
							->where('id', $raw->id)
							->update([
								'modification_status_round1' 	=> 0,
								'modification_status_round2' 	=> 0,
								'modification_status_round3' 	=> 0,
								'modification_status_round4' 	=> 0,
								'modification_status_round5' 	=> 0,
								'modification_status_round6' 	=> 0,
							]);
					}
				} else {
					echo 'No Schedule found <br/>';
				}
			}


			if (count($schedule) > 0) {
				echo 'Missed Entry Blocked done';
			}
		} catch (Exception $ex) {
			Log::error($ex);
			die($ex->getMessage());
		}
	}
}  // end class
