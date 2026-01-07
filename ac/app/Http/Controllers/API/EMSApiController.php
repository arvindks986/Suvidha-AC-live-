<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\commonModel;
use App\Classes\xssClean;
use App\Http\Controllers\API\ResponseController;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EMSApiController extends Controller
{

	public $xssClean = null;
	public $commonModel = null;
	public $ResponseMethod = null;
	public function __construct()
	{
		$this->xssClean = new xssClean;
		$this->commonModel = new commonModel();
		$this->ResponseMethod = new ResponseController;
		$this->bad_response = $this->ResponseMethod::HTTP_BAD_REQUEST;
		$this->ok_response = $this->ResponseMethod::HTTP_OK;
		$this->okStatus = "success";
		$this->errStatus = "error";
	}

	public $successStatus = 200;
	public $createdStatus = 201;
	public $nocontentStatus = 204;
	public $notmodifiedStatus = 304;
	public $badrequestStatus = 400;
	public $unauthorizedStatus = 401;
	public $notfoundStatus = 404;
	public $intservererrorStatus = 500;
	public $bad_response;
	public $ok_response;
	public $okStatus;
	public $errStatus;
	//public $pc_db = 'suvidha_pc_2024_05_e24';
	// public $pc_host= 'localhost';  //Local
	//public $pc_host= '10.247.219.232';  //Demo
	//public $pc_host= '10.247.137.77'; // Live


	public function GetElectionSchedule(Request $request)
	{

		try {

			$data = [];

			$header = $request->header('securityKey');

			if (empty($header)) {
				return response()->json(['status' => false, 'message' => 'Unauthenticated request']);
			} else if ($header) {

				if (date('dmY') != base64_decode($header)) {
					return response()->json(['status' => false, 'message' => 'Token Mismatched']);
				}
			}


			$validator = Validator::make($request->all(), [
				'election_type' => 'required',
				'state_cd' => 'required'

			]);


			if ($validator->fails()) {
				return response()->json(['status' => false, 'message' => 'Please Check the Input Details']);
			}



			//dd(11);

			if ($request->election_type == '2') {

				DB::reconnect('suvidhapc');
				DB::purge('suvidhapc');
				DB::setDefaultConnection('suvidhapc');
				Session::put('DB_DATABASE', DB::connection()->getDatabaseName());
				Config::set('database.connections.mysql.database', DB::connection()->getDatabaseName());




				$election_type = 1;
			} else if ($request->election_type == '4') {


				DB::reconnect('suvidhapc');
				DB::purge('suvidhapc');
				DB::setDefaultConnection('suvidhapc');
				Session::put('DB_DATABASE', DB::connection()->getDatabaseName());
				Config::set('database.connections.mysql.database', DB::connection()->getDatabaseName());

				$election_type = 2;
			} else if ($request->election_type == '1') {

				$election_type = 3;
			} else if ($request->election_type == '3') {
				$election_type = 4;
			} else {
				return response()->json(['status' => false, 'message' => 'Please Check the Election Type Details']);
			}




			$data_raw = DB::table('m_election_details as med')
				->join('m_schedule as ms', 'ms.SCHEDULEID', 'med.ScheduleID')
				->select('med.StatePHASE_NO as phase', 'CONST_NO as ac_or_pc_no', 'ms.DATE_POLL as election_date', 'DATE_COUNT')
				->where('ST_CODE', $request->state_cd)
				->where('ELECTION_TYPEID', $election_type)
				->where('med.CURRENTELECTION', 'Y')
				->groupBy('ST_CODE', 'StatePHASE_NO')
				->get()
				->toArray();

			//dd($data_raw);
			if (count($data_raw) == 0) {
				return response()->json(['status' => false, 'message' => 'Please Check the State Code Details']);
			} else if (count($data_raw) > 1) {
				$data['election_type'] = 'Multi Phase';
			} else {
				$data['election_type'] = 'Single Phase';
			}

			//$data['election_type'] = $request->election_type;
			$data['state_cd'] = $request->state_cd;
			$data['district_cd'] = $request->district_cd;

			$data['election_details'] = [];
			foreach ($data_raw as $key => $value) {

				$data['election_details'][$key]['phase'] = (string)$value->phase;
				$data_ac = DB::table('m_election_details as med')
					->select(DB::Raw("GROUP_CONCAT(CONST_NO) as ac_or_pc_no"))
					->where('ST_CODE', $request->state_cd)
					->where('ELECTION_TYPEID', $election_type)
					->where('med.CURRENTELECTION', 'Y')
					->where('med.StatePHASE_NO', $value->phase)
					->get()
					->toArray();

				$data['election_details'][$key]['ac_or_pc_no'] = explode(',', $data_ac[0]->ac_or_pc_no);

				$data['election_details'][$key]['election_date'] = $value->election_date;
				$counting_date = $value->DATE_COUNT;
			}

			$data['counting_date'] = $counting_date;
			return response()->json($data, $this->successStatus);
		} catch (Exception $ex) {
			return response()->json(['status' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
		}
	}

	public function GetCandidateCount(Request $request)
	{


		// dd(base64_encode(date('dmY')));

		try {

			$data = [];

			$header = $request->header('securityKey');

			if (empty($header)) {
				return response()->json(['status' => false, 'message' => 'Unauthenticated request']);
			} else if ($header) {

				if (date('dmY') != base64_decode($header)) {
					return response()->json(['status' => false, 'message' => 'Token Mismatched']);
				}
			}


			$validator = Validator::make($request->all(), [
				'election_type' => 'required',
				'state_cd' => 'required',
				'ac_or_pc_no' => 'required',

			]);


			if ($validator->fails()) {
				return response()->json(['status' => false, 'message' => 'Please Check the Input Details']);
			}

			if ($request->election_type == '2') {
				DB::reconnect('suvidhapc');
				DB::purge('suvidhapc');
				DB::setDefaultConnection('suvidhapc');
				Session::put('DB_DATABASE', DB::connection()->getDatabaseName());
				Config::set('database.connections.mysql.database', DB::connection()->getDatabaseName());

				$election_type = 1;
			} else if ($request->election_type == '4') {
				DB::reconnect('suvidhapc');
				DB::purge('suvidhapc');
				DB::setDefaultConnection('suvidhapc');
				Session::put('DB_DATABASE', DB::connection()->getDatabaseName());
				Config::set('database.connections.mysql.database', DB::connection()->getDatabaseName());

				$election_type = 2;
			} else if ($request->election_type == '1') {
				$election_type = 3;
			} else if ($request->election_type == '3') {
				$election_type = 4;
			} else {
				return response()->json(['status' => false, 'message' => 'Please Check the Election Type Details']);
			}



			$data_count = 0;

			$data['election_type'] = $request->election_type;
			$data['state_cd'] = $request->state_cd;
			$data['district_cd'] = $request->district_cd;
			$data['ac_or_pc_no'] = $request->ac_or_pc_no;


			$sql = DB::table('candidate_nomination_detail as cnd');

			if ($election_type == '3'  || $election_type == '4') {
				$sql->where('ac_no', $request->ac_or_pc_no);
			} else {
				$sql->where('pc_no', $request->ac_or_pc_no);
			}
			$data_count = $sql->where('st_code', $request->state_cd)
				->where('election_type_id', $election_type)
				->where('application_status', '6')
				->where('finalaccepted', '1')
				->where('finalize', '1')
				->where('party_id', '!=', '1180')
				->where('symbol_id', '!=', '200')
				->count();

			//dd($data_raw);
			if ($data_count == 0) {
				return response()->json(['status' => false, 'message' => 'No Record found. Please Check the Input Details']);
			}

			$add_one_count = $data_count + 1;
			$data['candidates_count'] = (string)$add_one_count;

			return response()->json($data, $this->successStatus);
		} catch (Exception $ex) {
			return response()->json(['status' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
		}
	}
}
