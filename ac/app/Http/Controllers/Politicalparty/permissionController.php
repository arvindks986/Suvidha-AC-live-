<?php

namespace App\Http\Controllers\politicalparty;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use \PDF;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use App\models\Permission\PermissionModel;
use App\models\Permission\User_dataModel;
use App\commonModel;
use App\models\{States, Districts, AC};
use App\Helpers\LogNotification;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Helpers\SendNotification;
use App\Jobs\Messgup;
use Illuminate\Support\Facades\Response;

class permissionController extends Controller
{

	// AdD Auth 
	public $commonModel = null;
	public $xssClean = null;

	public function __construct()
	{
		$this->middleware('usersession');
		$this->middleware(['auth:web', 'auth']);
		$this->commonModel = new commonModel();
		$this->xssClean = new xssClean;
		// $this->middleware('cand');
	}
	protected function guard()
	{
		return Auth::guard('web');
	}
	// 
	public function index()
	{
		Auth::guard('web');
		if (Auth::check()) {

			$users = Session::get('login_details');
			$user = Auth()->user();
			$id = $user->id;
			//dd($user);
			$applied_permission = DB::table('permission_request')
				->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
				->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
				->where('user_id', '=', $id)
				->select('permission_request.*', 'permission_request.id as permission_id', 'm.permission_name', 'permission_request.permission_mode')
				->groupBy('permission_id')
				->get();
			$data = DB::table('permission_request')
				->select(DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status !=1 ) THEN 1 ELSE 0 END) as Pending'), DB::raw('sum(CASE WHEN (approved_status = 2 && cancel_status !=1) THEN 1 ELSE 0 END) as Accepted'), DB::raw('sum(CASE WHEN (approved_status = 1 &&  cancel_status !=1) THEN 1 ELSE 0 END) as Inprogress'), DB::raw('sum(CASE WHEN (approved_status = 3 && cancel_status !=1)THEN 1 ELSE 0 END) as Rejected'), DB::raw('count(approved_status) as total'), DB::raw('sum(CASE WHEN (cancel_status = 1) THEN 1 ELSE 0 END) as cancle'))
				->where('user_id', '=', $id)
				// ->groupBy('approved_status')
				->get()->toArray();
			// dd($data);

			return view('politicalparty.permissionone', ['total' => $data, 'permissionDetails' => $applied_permission, 'user_data' => $user]);
		} else {
			return Redirect::back();
		}
	}

	public function permissionrole(Request  $request)
	{
		Auth::guard('web');
		if (Auth::check()) {
			$data = $request->all();
			$validator = Validator::make($data, [
				'role_id'				=> 'required|not_in:0',
				//'party_id' 	    		=>'required|not_in:0',

			]);
			if ($validator->fails()) {
				return Redirect::back()
					->withErrors($validator)
					->withInput();
			} else {
				$users = Session::get('login_details');
				$user = Auth()->user();
				$userid = $user->id;
				$mobile = $user->mobile;
				$role_id = $request->input('role_id');
				//$party_id = $request->input('party_id');
				//$data = array('role_id'=>$role_id,'party_id'=>$party_id);
				$data = array('role_id' => $role_id);
				$role_type = DB::table('user_login')->where('id', $userid)->update($data);
				$role = DB::table('user_role')->where('role_id', $role_id)->get();
				$roletype = $role[0]->role_name;
				Session::put('Applicant_type', $roletype);
				Session::put('role_id', $role_id);
				// dd(session::get('Applicant_type'));
				$u_data = DB::table('user_data')->where('mobileno', $mobile)->get();


				if ($user->election_category != '2') {
					//First Login	
					$first_login = DB::connection('mysql')->table('user_login')->select('first_login')->where('id', '=', \Auth::id())->value('first_login');
					if ($first_login == '' or $first_login == 0) {
						return redirect('/first-login-user-view');
					}
				}

				if (count($u_data) > 0) {



					if ($role_id != 2) {
						//$result=DB::table('user_data')->where('mobileno',$mobile)->update(['party_id' => $party_id]);
						return Redirect::to('/update profile');
					} else {



						//$result=DB::table('user_data')->where('mobileno',$mobile)->update(['party_id' => $party_id]);
						return Redirect::to('/dashboard-nomination-new');
					}
				} else {
					//					if($user->election_category == '2'){
					//						return Redirect::to('/nomination/mlc/apply-nomination-step-1'); 
					//					}
					if ($role_id != 2) {
						return Redirect::to('/profile');
					} else {
						return Redirect::to('/nomination/apply-nomination-step-1');
					}
				}
			}
		} else {
			return Redirect::back();
		}
	}

	public function create()
	{
		Auth::guard('web');
		if (Auth::check()) {
			$users = Session::get('login_details');
			$user = Auth()->user();
			$userid = $user->id;
			$mobile = $user->mobile;
			// put applicant type in session
			$userrole = $user->role_id;
			$type = DB::table('user_role')->where('role_id', $userrole)->select('role_name')->get();
			$role_type = $type[0]->role_name;
			Session::put('Applicant_type', $role_type);
			// end applicant type in session
			$res = DB::table('user_data')->where('mobileno', $mobile)->where('user_login_id', $userid)->get();
			//dd($res);
			//if($res[0]->election_id == 0 ||$res[0]->election_id == null){
			//	return Redirect::to('/profile')->with('msg', 'Please Update Election Id in Profile Details !');		}

			try {
				if (config('public_config.permission_log')) {

					$message = array();
					$message['MobNo'] = Auth::user()->name ?? '';
					$message['applicationType'] = 'WebApp/' . $mobile;
					$message['Module'] = 'ENCORE';
					$message['TransectionType'] = 'Permission';
					$message['TransectionAction'] = 'User Data Submit';
					$message['TransectionStatus'] = 'SUCCESS';
					$message['LogDescription'] = 'Permission Add Successfully';
					LogNotification::LogInfo($message);
				}

				if (count($res) > 0) {

					$state = DB::table('m_state')->get();
					// get user information
					$user_details = DB::table('user_data')->join('m_party', 'm_party.CCODE', 'user_data.party_id')->join('m_state as e', 'e.ST_CODE', '=', 'user_data.state_id')
						// ->join('m_district as f',function ($join){$join->on('f.DIST_NO','=','user_data.district_id')
						// 	->on('f.ST_CODE', '=', 'e.ST_CODE');})
						->where('m_party.deleteflag', 'N')
						->where('user_data.user_login_id', $userid)
						->select('user_data.user_login_id', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'e.ST_CODE', 'e.ST_NAME', 'm_party.CCODE', 'm_party.PARTYNAME', 'user_data.election_id')
						->get();
					// dd($user_details);//,'g.AC_NO','g.AC_NAME' ,'f.DIST_NO','f.DIST_NAME'
					// get location detail
					$user_details_location = DB::table('user_data')->join('m_state as e', 'e.ST_CODE', '=', 'user_data.state_id')
						->join('m_district as f', function ($join) {
							$join->on('f.DIST_NO', '=', 'user_data.district_id')
								->on('f.ST_CODE', '=', 'e.ST_CODE');
						})
						->join('m_ac as g', function ($join) {
							$join->on('g.AC_NO', '=', 'user_data.ac_id')
								->on('g.ST_CODE', '=', 'e.ST_CODE')->on('g.DIST_NO_HDQTR', '=', 'f.DIST_NO');
						})
						->join('location_master as l', function ($join) {
							$join->on('l.ac_no', '=', 'user_data.ac_id')->on('g.AC_NO', '=', 'user_data.ac_id')
								->on('g.ST_CODE', '=', 'e.ST_CODE')->on('g.DIST_NO_HDQTR', '=', 'f.DIST_NO')->on('user_data.state_id', '=', 'l.st_code');
						})
						->where('user_data.user_login_id', $userid)
						->select('l.location_name', 'l.id')->orderBy('l.location_name', 'ASC')
						->get();
					// dd($user_details_location);
					// get police station location
					$user_details_police = DB::table('user_data')->orderBy('police_st_name')->join('m_state as e', 'e.ST_CODE', '=', 'user_data.state_id')->join('police_station_master as p', function ($join) {
						$join->on('p.ST_CODE', '=', 'e.ST_CODE')->on('p.ac_no', '=', 'user_data.ac_id');
					})->where('user_data.user_login_id', $userid)->select('p.police_st_name', 'p.police_station_address', 'p.id')->get();

					// get permission Type
					$st = $res[0]->state_id;

					$permission_type = DB::table('permission_type')
						->join('permission_master', 'permission_master.id', 'permission_type.permission_type_id')
						->leftjoin('restriction_day_master', 'restriction_day_master.permission_type_id', 'permission_type.permission_type_id')
						->orderBy('permission_master.permission_name')
						->where('permission_master.status', '1')
						->where('permission_type.st_code', $st)
						->where('restriction_day_master.st_code', $st)
						->select('restriction_day_master.restriction_day', 'permission_master.permission_name', 'permission_type.id as permsn_id', 'permission_type.permission_type_id')
						//			->select('permission_master.permission_name','permission_master.id')
						->groupBy('permission_type_id')
						->get();
					// dd($permission_type);
					if (!empty($user_details)) {

						return view('politicalparty.createpermission', compact('permission_type', 'user_details', 'user_details_location', 'user_details_police', 'state'));
					} else {
						return Redirect::to('/profile')->with('msg', 'Please Update Profile Details First to Apply Permission!');
					}
				} else {
					return Redirect::to('/profile')->with('msg', 'Please Fill Profile Details First to Apply Permission!');
				}
			} catch (Exception $e) {
				if (config('public_config.permission_log')) {
					$message = array();
					$message['MobNo'] = Auth::user()->name ?? '';
					$message['applicationType'] = 'WebApp/' . $mobile;
					$message['Module'] = 'ENCORE';
					$message['TransectionType'] = 'Permission';
					$message['TransectionAction'] = 'User Data Submit';
					$message['TransectionStatus'] = 'Failed';
					$message['LogDescription'] = 'Something went to wrong ' . $e->getMessage();

					LogNotification::LogInfo($message);
				}
			}
		} else {
			return Redirect::back(); //Redirect::back()
		}
	}

	public function store(Request $request)
	{

		DB::beginTransaction();
		try {
			Auth::guard('web');
			if (Auth::check()) {

				$users = Session::get('login_details');
				$user = Auth()->user();
				$userid = $user->id;
				$userstate = DB::table('user_data')->where('user_login_id', $userid)->get()[0];


				$data = $request->all();

				$doc_data = $request->file('permsndoc');
				$doc_name = '';
				if (!empty($doc_data) && count($doc_data) > 0) {
					sort($doc_data);
				} else {
					$doc_data;
				}
				$sid = $userstate->state_id;
				$kyk = $this->statedatevalidation($sid);
				//$semi_chart_data = $kyk['data'][0];
				foreach ($kyk as $item) {
					$khs = $item->restriction_status;
				}


				$document = $request->input('doc');
				$ptypeid = $request->permission_type;

				$permission_type_role = DB::table('permission_type')
					->where('permission_type.status', '1')
					->where('st_code', $sid)
					->where('id', $request->permission_type)
					->select('permission_type.role_id', 'permission_type.permission_type_id')
					->first();

				if (!$permission_type_role) {
DB::rollback();
					return Redirect::back()->with('message', 'Please select Valid Permission Type.');
				}
				if ($permission_type_role == null) {
					$role_type_id = null;
				} else {
					$role_type_id = $permission_type_role->role_id;
				}

				$PermissionCount = DB::table('permission_request')
					->where('user_id', $userid)
					->where('st_code', $sid)
					->where('permission_type_id', $permission_type_role->permission_type_id)
					->where('created_at', '>=', Carbon::now()->subMinutes(10)) // Filter by the last 1 hour
					->count();
				// dd( $PermissionCount);
				if ($PermissionCount >= 5) {
DB::rollback();
					return Redirect::back()->with('message', 'You cannot apply for the same permission more than 5 times in 10 minutes.');
				}





				$pdata = explode('#', $ptypeid);
				$common_rules = [
					'permission_type'  => 'required|not_in:0',
					'start'             => 'required|date',
					'end'               => 'required|date|after_or_equal:start',
					'permsndoc.*.p_doc' => 'mimes:pdf|max:5120',
				];

				$common_messages = [
					'permission_type' => 'Please Select Permission Type !',
					'start.date'      => 'Event Start date should be after 48 hours',
					'end.date'        => 'Event end time should be greater than Event start time!',
					'permsndoc.*.p_doc.mimes' => 'Please Upload Only PDF File!',
				];

				// Initialize the base rules based on role type
				$base_rules = [
					'district'         => 'required|numeric|not_in:0|min:1|max:99',
					'ac'               => 'required|numeric|not_in:0|min:1|max:500',
					'police_station'   => 'required|numeric|not_in:0|min:1|max:99999',
					'location1'        => ['required', 'regex:/^\d+$|^other$/', 'max:99999'],
					'other'            => 'nullable|required_if:location1,other|regex:/^[a-zA-Z0-9\s\-.]+$/',
				];

				// Adjust rules based on role type
				if ($role_type_id == 4) {
					// Role 4: No district, ac, police_station, or location1 required
					$base_rules = array_merge($base_rules, [
						'district' => 'nullable',
						'ac' => 'nullable',
						'police_station' => 'nullable',
						'location1' => 'nullable',
					]);
				} elseif ($role_type_id == 5) {

					$base_rules = array_merge($base_rules, [
						'ac' => 'nullable',
						'police_station' => 'nullable',
						'location1' => 'nullable',
					]);
				} elseif ($role_type_id == 19) {

					$police_stationdb = DB::table('police_station_master')
						->where('st_code', $sid)
						->where('ac_no', $request->ac)
						->where('id', $request->police_station)
						->exists();

					if (!$police_stationdb) {
						DB::rollback();
						return Redirect::back()->with('message', 'Please select Valid Police Station');
					}
				}

				// Merge the common rules and the role-specific base rules
				$rules = array_merge($common_rules, $base_rules);

				// Custom error messages (merged with common ones)
				$messages = array_merge($common_messages, [
					'district.required' => 'Please Select District Name!',
					'ac.required' => 'Please Select Assembly Constituency!',
					'police_station.required' => 'Please Select Police Station Type!',
					'location1.required' => 'Please Select Event Place Name!',
					'other.required_if' => 'Please specify the other location!',
					'other.regex' => 'The other location must only contain letters, numbers, and spaces!',
				]);

				// Perform the validation
				$validator = Validator::make($data, $rules, $messages);





				$ed = date_create($request->electiondate);
				//dd($ed);
				$checkElectionDate = DB::table('m_schedule')
					->where('ELECTION_ID', $userstate->election_id)
					->where('DATE_POLL', $ed)
					->exists();


				if (!$checkElectionDate) {
					DB::rollback();
					return Redirect::back()->with('message', 'Please select a valid election schedule date');
				}

				if ($ed != false) {
					$election_date = date_sub($ed, date_interval_create_from_date_string("2 days"));
					$final_election_date = date_format($election_date, "d-m-Y");

					if (strtotime($final_election_date) <  strtotime(date('d-m-Y', strtotime($request->end)))) {
						DB::rollback();
						return Redirect::back()->with('message', 'You can not apply for permission because election schedule date is ' . $request->electiondate . ' .');
					}
				} else {
					DB::rollback();
					return Redirect::back()->with('message', 'You can not apply for permission as ' . $request->electiondate . ' .');
				}

				$startdate = $request->start;
				$enddate = $request->end;
				$cur_timenow = Carbon::now();
				$addtimestart = $cur_timenow->copy()->addHours(48);

				$addtime1 = $addtimestart->format('d-m-Y H:i');

				if ($khs != '0') {

					if (strtotime($startdate) < strtotime($addtime1)) {
						DB::rollback();
						return Redirect::back()->with('message', 'Permission to be applied 48 hour before!');
					}
				}

				$pidarr = explode('#', $request->permission_type);
				$pid_type_id = (int)$pidarr[2];

				if ($pid_type_id <= 3) {
					$pid_type_id = 7;
				}

				// Get and parse the start and end dates
				$startdate1 = Carbon::parse($request->start);
				$enddate1 = Carbon::parse($request->end);
				$cur_timenow1 = Carbon::now(); // Get the current date and time


				$daysDifference = $cur_timenow1->diffInDays($enddate1);
				// if ($startdate1->isSameDay($enddate1)) {
				// 	DB::rollback();
				// 	return Redirect::back()->with('message', 'Submission is not possible because the Start Date & Time and End Date & Time cannot be the same.');
				// }

				// Check if both start and end dates are less than the current date
				if ($startdate < $cur_timenow1 && $enddate1 < $cur_timenow1) {
					DB::rollback();
					return Redirect::back()->with('message', 'Submission is not possible because both the start Date and end Date are earlier than the current date.');
				}

				// Check conditions for submission
				if ($daysDifference < $pid_type_id) {
				} else {


					DB::rollback();
					return Redirect::back()->with('message', 'Submission is not possible because the count of days from the current date to the end date is ' . $daysDifference . ', which is greater than the permissions valid days of ' . $pid_type_id . '.');
				}

				if ($validator->fails()) {
					DB::rollback();
					return Redirect::back()
						->withErrors($validator)
						->withInput();
				} else {

					if (!empty($doc_data) && count($doc_data) > 0) {

						foreach ($doc_data as $key => $file) {


							$contentFileS = file_get_contents($file['p_doc']);
							$checkArrF = ["<script", "<?php"];
							foreach ($checkArrF as $checkArrv) {
								if (stristr(strtolower($contentFileS), $checkArrv)) {
									DB::rollback();
									return Redirect::back()->with('message', 'Invalid file format, Please attach .pdf file.');
								}
							}
							$extMme = explode(".", $file['p_doc']->getClientOriginalName());

							if (count($extMme) > 2) {
								DB::rollback();
								return Redirect::back()->with('message', 'Invalid file format, Please attach .pdf file.');
							}

							// $doc =  str_replace(' ', '', $file['p_doc']->getClientOriginalName());
							// $name = str_replace(' ', '', $file['p_doc']->getClientOriginalName());
							$doc = preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file['p_doc']->getClientOriginalName());
							$name = pathinfo($doc, PATHINFO_FILENAME);
							$name = preg_replace('/[^A-Za-z0-9\-_]/', '', $name);
							$size = $file['p_doc']->getSize();
							$time = Carbon::now()->timestamp;


							$extension = $file['p_doc']->getClientOriginalExtension();

							$newFilename = 'uploads1/userdoc/permission-document/' . $userstate->election_id . '/' . $sid . '/' . $userid . '_' . $sid . '_' . $time . '_' . mt_rand(6, 1000000) . '.' . $extension;

							$destinationPath3 = public_path('/uploads1/userdoc/permission-document/' . $userstate->election_id . '/' . $sid);
							$file['p_doc']->move($destinationPath3, $newFilename);
							$doc_name .= $newFilename . ',';
						}
					}


					$permission = new PermissionModel;
					$data['user_id'] 				= $userid;
					$data['st_code'] 				= $sid;
					$data['dist_no']				= $request->district;
					$data['ac_no'] 					= $request->ac;
					$data['pc_no'] 					= '0';
					$data['party_id'] 					= $userstate->party_id;
					$data['permission_type_id'] 	= $pdata[0];
					/* insert filename */
					if (($data['required_files'] = $doc_name) != null) {
						$data['required_files'] = $doc_name;
					} else {
						$data['required_files'] = 'null';
					}
					/* insert fileserver path */
					$data['fileserver_dir']			= 'uploads1';
					/* insert location */
					if (!empty($request->location1)) {
						$data['location_id']  = strip_tags($request->location1);
					} else {
						$data['location_id']  = '0';
					}

					//$data['location_id'] 			= $request->location1;

					$data['Other_location'] 		= strip_tags($request->other);

					if (($data['Other_location']) != null) {
						$data['Other_location'] = strip_tags($request->other);
					} else {
						$data['Other_location'] = 'NULL';
					}
					$data['latitude'] 				= 'NULL';
					$data['longitude'] 				= 'NULL';
					$timestamp = date('Y-m-d H:i:s', strtotime($request->start));
					$timestamp1 = date('Y-m-d H:i:s', strtotime($request->end));
					$data['date_time_start'] 		= $timestamp;

					$data['date_time_end'] 			= $timestamp1;
					$data['permission_request.assigned_police_st_id'] 	= $request->police_station;
					$data['draft_status'] 			= '0';
					$data['approved_status'] 		= '0';
					$data['permission_mode']		= '1';
					/* $today=explode(' ',Carbon::today()); */
					$data['added_at']				= Carbon::now();
					$data['created_at'] 			= Carbon::now();
					$data['updated_at'] 			= Carbon::now();
					$data['created_by'] 			= $userid;
					$data['updated_by'] 			= '0';
					$data['election_id']			= $userstate->election_id;
					$data['device_name']      = 'WebApp';

					// update permission_request_status field in user_login table
					$update_request_status = DB::table('user_login')->where('id', $userid)->update(['permission_request_status' => '1']);
					$res = $permission->create($data);
					$LastInsertId = $res->id;
					$encryptedParam1 = Crypt::encrypt($LastInsertId);
					$query = DB::table('permission_request')->where('id', $LastInsertId)->get();
					$query_ptype = DB::table('permission_type')->select('role_id')->where('id', $query[0]->permission_type_id)->get();
					$query_officer = DB::table('officer_login');
					if ($query[0]->ac_no) {
						if ($query_ptype[0]->role_id == 5) {
							$query_officer->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code)->where('dist_no', $query[0]->dist_no);
						} else if ($query_ptype[0]->role_id == 4) {
							$query_officer->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code);
						} else {
							$query_officer->where('ac_no', $query[0]->ac_no)->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code)->where('dist_no', $query[0]->dist_no);
						}
					} else {
						if ($query_ptype[0]->role_id == 5) {
							$query_officer->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code)->where('dist_no', $query[0]->dist_no);
						} else {
							$query_officer->where('role_id', $query_ptype[0]->role_id)->where('st_code', $query[0]->st_code);
						}
					}
					$recorrd = $query_officer->first();

					$a = PermissionModel::where('id', $LastInsertId)->update([
						'reference_id' => $userstate->election_id . 'AC' . $LastInsertId
					]);

					if (!empty($recorrd) && $recorrd != null) {

						if ($recorrd->Phone_no != '') {
							$permsn_details = DB::table('permission_request as a')
								->join('permission_type as b', 'b.id', '=', 'a.permission_type_id')
								->join('permission_master as c', 'c.id', '=', 'b.permission_type_id')
								->where('a.id', $LastInsertId)
								->select('a.reference_id', 'a.added_at', 'c.permission_name')
								->get()->first();
							//dd($permsn_details);
							$mob_message = "A New Request has been received for " . $permsn_details->permission_name . "-" . $permsn_details->reference_id . " " . $permsn_details->added_at . ", ECI";
							Messgup::dispatch($recorrd->Phone_no, $mob_message)->onQueue('Messgup');
						}
					}


					if (!empty($LastInsertId)) {

						$pidarr = explode('#', $request->permission_type);
						$pid_type_id = $pidarr[0];

						$data1 = DB::table('permission_required_doc')
							->select('authority_type_id')
							->where('permission_id', $pid_type_id)
							->get()
							->toArray();

						foreach ($data1 as $doc_auth) {

							$nodaldetails1 = DB::table('authority_masters as a')
								->Join('authority_masters_mapping as b', 'a.id', '=', 'b.authority_masters_id')
								->select('a.id', 'a.name', 'b.auth_type_id')
								->where('a.st_code', $sid)
								->where('b.auth_type_id', $doc_auth->authority_type_id)
								->where('b.is_active', 1);

							// Handle conditions based on role_type_id
							if ($role_type_id == '4') {
								$nodaldetails1->where('b.dist_no', 0)->where('b.ac_no', 0);
							} elseif ($role_type_id == '5') {
								$nodaldetails1->where('b.dist_no', $request->district)->where('b.ac_no', 0);
							} elseif ($role_type_id == '19') {
								$nodaldetails1->where('b.ac_no', $request->ac);
							}

							// Execute the query and convert the result to an array
							$nodaldetails1 = $nodaldetails1->get()->toArray();


							if (!empty($nodaldetails1)) {
								for ($i = 0; $i < count($nodaldetails1); $i++) {
									if (!empty($nodaldetails1[$i])) {
										$today = explode(' ', Carbon::today());
										$nodaldata = array(
											'permission_request_id' => $LastInsertId,
											'authority_id' => $nodaldetails1[$i]->id,
											'accept_status' => 0,
											'added_at' => $today[0],
											'created_at' => Carbon::now(),
											'updated_at' => Carbon::now()
										);
										$insert = DB::table('permission_assigned_auth')->insert($nodaldata);
									}
								}
							}
						}
					}
				}


				DB::commit();
				return redirect('/Receiptper/' . urlencode($encryptedParam1));
			} else {
				return Redirect::back('/login');
			}
		} catch (\Exception $e) {
			//dd($e);
			DB::rollback();
			//\Session::flash('error_mes', 'Please try again Data  do not inserted');
			return Redirect::back()->with('message', 'Please try again Data  do not inserted');
		}
	}
	public function getprintreciept($id)
	{

		Auth::guard('web');
		if (Auth::check()) {
			$users = Session::get('login_details');
			//dd($users);
			$user = Auth()->user();
			$userid = $user->id;
			$mobile = $user->mobile;
			$param1 = Crypt::decrypt(urldecode($id));

			$detaildata = DB::table('permission_request')
				->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
				->leftjoin('location_master', 'location_master.id', '=', 'permission_request.location_id')
				->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
				->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
				->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
				->leftjoin('m_district as district', function ($join) {
					$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
				})
				->leftjoin('m_ac as ac', function ($join) {
					$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
				})
				->where('permission_request.id', '=', $param1)
				->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'm.permission_name', 'location_master.location_name', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'user_data.address', 'permission_request.id', 'permission_request.date_time_start', 'permission_request.date_time_end', '.permission_request.Other_location', 'permission_request.id', 'permission_request.location_id', 'location_master.location_details', 'permission_request.reference_id')
				->groupBy('user_data.name')
				->get();



			return view('politicalparty.receipt', compact('detaildata'));
		}
	}
	public function getSelectDetails(Request $request)
	{
		Auth::guard('web');
		if (Auth::check()) {
			$users = Session::get('login_details');
			$user = Auth()->user();
			$userid = $user->id;
			$mobile = $user->mobile;

			$res = DB::table('user_data')->where('mobileno', $mobile)->where('user_login_id', $userid)->get();
			$stcode = $res[0]->state_id;
			if (!empty($request->permsn_id)) {
				$getPermissionDetails = DB::table('permission_required_doc')
					->select('*')->where('permission_id', $request->permsn_id)->where('st_code', $stcode)->get()->toArray();
				foreach ($getPermissionDetails as $permission) {
					// Assuming you want to encrypt a specific column (e.g., 'column_name')
					$permission->file_name = Crypt::encryptString($permission->file_name);
				}
				if (!empty($getPermissionDetails)) {
					return $getPermissionDetails;
				} else {
					return '0';
				}
			}
		} else {
			return Redirect::back();
		}
	}
	public function detaildata($data)
	{

		$d = session()->all();
		$number = $d['userID'];
		$r = getUserDetails($d['userID']);
		// $id=$r->id;

		$detaildata = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')->join('location_master', 'location_master.id', '=', 'permission_request.location_id')->join('permission_request_comment', 'permission_request_comment.permission_request_id', '=', 'permission_request.id')->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')->where('permission_request.id', '=', $data)->get();
		return $detaildata;
	}

	// Controller Add By Divya
	public function getlatlongs(Request $request)
	{
		$locationid = $request->input('locationid');
		$locationdetails = DB::table('location_master')->where('id', $locationid)
			->get();
		return json_encode($locationdetails);
	}
	public function getlocationList(Request $request)
	{
		$state = $request->input('stcode');
		$ac = $request->input('ac');
		$getACLists = DB::table('location_master')->orderBy('location_name', 'DESC')->where('ST_CODE', $state)
			->where('AC_NO', '=', $ac)
			->get();
		return json_encode($getACLists);
	}
	public function roletype()
	{
		Auth::guard('web');
		if (Auth::check()) {
			$role = getallpartylist();
			$user_data = Auth()->user();
			// dd($role);
			$role_type = DB::table('user_role')->where('role_level', '2')->select('role_id', 'role_name')->get();
			// dd($role_type);
			return view('politicalparty.RoleType', compact('role_type', 'role', 'user_data'));
		} else {
			return Redirect::back();
		}
	}
	public function getDistrictsval(Request $request)
	{
		//print_r($_REQUEST);  exit;
		$state = $request->input('stcode');
		$getDistricts = DB::table('m_district')->orderBy('DIST_NAME')->where('ST_CODE', $state)->get();
		return $getDistricts;
	}
	public function getACListsval(Request $request)
	{
		$state = $request->input('stcode');
		$district = $request->input('district');
		$getACList = DB::table('m_ac')->orderBy('AC_NAME')->where('ST_CODE', $state)
			->where('DIST_NO_HDQTR', '=', $district)
			->get();
		return json_encode($getACList);
	}

	public function profile()
	{
		Auth::guard('web');
		if (Auth::check()) {
			$users = Session::get('login_details');
			$user = Auth()->user();
			$userid = $user->id;
			$mobile = $user->mobile;
			$party = $user->party_id;
			//		$party_name=DB::table('m_party')->where('CCODE',$party)->select('CCODE','PARTYNAME')->get();
			//	$allParty = DB::table('m_party')->select('*')->where('deleteflag', 'N')->orderBy('PARTYNAME')->get()->toArray();
			$allParty = DB::table('m_party')->select('*')
				->where('CCODE', '<>', '1180')
				->where('PARTYSYM', '<>', '-1')
				->where('deleteflag', 'N')
				->orderBy('PARTYNAME')->get()->toArray();

			//		if(count($party_name)>0)
			//		{
			$result = DB::table('m_election_history')->where('const_type', 'AC')->max('election_id');
			// dd($result);

			$state = DB::table('m_cur_elec')->join('m_state', 'm_state.ST_CODE', 'm_cur_elec.ST_CODE')->where('ConstType', 'AC')->select('m_state.ST_CODE', 'm_state.ST_NAME')->groupBy('ST_CODE')->orderBy('m_state.ST_NAME', 'ASC')->get();
			//dd($state);
			return view('politicalparty.profile', ['allParty' => $allParty, 'getStates' => $state, 'mobile' => $mobile, 'user_login_id' => $userid, 'elc_id' => $result, 'user_data' => $user]);
			//		}else{
			//			return Redirect::to('/roletype');
			//		}
		} else {
			return Redirect::back();
		}
	}
	public function addprofile(Request $request)
	{
		Auth::guard('web');
		if (Auth::check()) {
			$users = Session::get('login_details');
			$user = Auth()->user();

			$mobile = $user->mobile;
			// dd($request->all());
			$data = $request->all();
			$validator = Validator::make($data, [
				'name'				=> 'required|max:100|regex:/^[a-zA-Z. ]+$/u',
				'father_name' 	    => 'required|max:100|regex:/^[a-zA-Z. ]+$/u',
				'email'				=> 'required|email|max:100',
				'gender' => ['required', 'regex:/^(male|female|other)$/'],
				'state' => [
					'required',
					'regex:/^[A-Za-z][0-9]{2}$/'
				],
				//'district'     	    => 'required|max:100|not_in:0',
				//'ac'              	=> 'required|max:100|not_in:0',
				'mobile' => 'required|numeric|min:10|regex:/[0-9]{10}/|unique:user_data,mobileno',

				'dob' => 'required|date|before:-18 years', //|before:2000-01-01
				//'Address1'			=> 'required|max:200',
				'party_master' => 'required|numeric|not_in:0|min:1|max:9999|exists:m_party,CCODE',


			]);
			if ($validator->fails()) {
				return Redirect::back()
					->withErrors($validator)
					->withInput();
			} else {
				$users = Session::get('login_details');
				$user = Auth()->user();
				//dd($user);
				// dd($request);
				$mobile = $user->mobile;
				$result = DB::table('m_election_history')->where('const_type', 'AC')->max('election_id');


				$permission = new User_dataModel;
				$data['user_login_id']		= $user->id;
				$data['name'] 				= strip_tags($request->name);
				$data['fathers_name']		= strip_tags($request->father_name);
				$data['email'] 				= strip_tags($request->email);
				$data['mobileno'] 			= $request->mobile;
				$data['gender'] 			= strip_tags($request->gender);
				$data['epic_no']         	= 'NULL';
				$data['part_no'] 			= '0';
				$data['slno'] 				= '0';
				$data['dob'] 				= $request->dob;
				$data['party_id']			= $request->party_master;
				//$data['address'] 			= $request->Address1;
				$data['state_id'] 			= strip_tags($request->state);
				//	$data['district_id'] 		= $request->district;
				///	$data['ac_id'] 				= $request->ac;
				$data['religion'] 			= '0';
				$data['caste'] 				= '0';
				$data['mark_as_delete'] 	= '0';
				$data['added_at']			= carbon::now();
				$data['created_at']			= carbon::now();
				$data['added_update_at']	= carbon::now();
				$data['updated_at']			= 'NULL';
				$data['election_id']		= $result;
				// dd($data);
				$updateDetails = array(
					'registration_type' => '1',
					'is_profile_update' => '1',
					'isActive' => '1',
					'login_access'		=>	'1',
					'email'				=> strip_tags($request->email),
					'created_at' 		=> Carbon::now(),
					'election_id'		=> $result,
					'party_id'		=> $request->party_master
				);

				try {
					DB::beginTransaction();
					$res = $permission->create($data);
					$id = DB::getPdo()->lastInsertId();
					$updatelogin = DB::table('user_login')->where('mobile', $mobile)->update($updateDetails);
					DB::commit();
				} catch (Exception $e) {
					DB::rollBack();
					return $e;
				}
				return Redirect::to('/permission')->with('msg', 'Profile Successfully Saved!');
				// return Redirect::to('/permission')->with('msg','Profile Successfully Saved!');
			}
		} else {
			return Redirect::back();
		}
	}
	public function updateprofile()
	{
		Auth::guard('web');
		if (Auth::check()) {
			$users = Session::get('login_details');
			$user = Auth()->user();
			$mobile = $user->mobile;
			$id = $user->id;

			$data  				= [];
			$data['state']		= [];
			$data['dist']		= [];
			$data['ac']			= [];
			$dsta['profile']	= [];
			$data['election']	= [];

			$queryElection = DB::table('m_election_history')->where('const_type', 'AC')->max('election_id');
			$data['election']	= $queryElection;

			// $queryDist	= DB::table('m_district')->select('DIST_NAME', 'DIST_NO', 'ST_CODE')->orderBy('DIST_NAME', 'ASC')->get();
			// foreach ($queryDist as $value) {
			// 	$data['dist'][] = [
			// 		'name'		=> $value->DIST_NAME,
			// 		'code'		=> $value->DIST_NO,
			// 		'st_code'	=> $value->ST_CODE,
			// 	];
			// }
			$queryState	= DB::table('m_state')->select('ST_NAME', 'ST_CODE')->get();
			foreach ($queryState as $value) {
				$data['state'][] = [
					'name'		=> $value->ST_NAME,
					'code'		=> $value->ST_CODE,
				];
			}
			// dd($data['state']);
			// $queryAc  = DB::table('m_ac')->select('AC_NO', 'ST_CODE', 'AC_NAME', 'DIST_NO_HDQTR')->get();
			// foreach ($queryAc as $value) {
			// 	$data['ac'][]	= [
			// 		'name'		=> $value->AC_NAME,
			// 		'code'		=> $value->AC_NO,
			// 		'st_code'	=> $value->ST_CODE,
			// 		'dist_code'	=> $value->DIST_NO_HDQTR,
			// 	];
			// }
			$queryProfile = DB::table('user_data')->join('m_party', 'm_party.CCODE', 'user_data.party_id')
				->join('user_login', 'user_login.mobile', 'user_data.mobileno')
				->join('m_state', 'user_data.state_id', 'm_state.ST_CODE')
				->select('user_data.name', 'user_data.fathers_name', 'user_data.email', 'user_data.gender', 'user_data.mobileno', 'user_data.dob',  'user_data.state_id', 'user_login.permission_request_status', 'm_party.CCODE', 'm_party.PARTYNAME', 'user_data.user_login_id')
				->where('mobileno', $mobile)->get();
			foreach ($queryProfile as $value) {
				$data['profile'][]	= [
					'name' 			=> $value->name,
					'f_name'		=> $value->fathers_name,
					'email'			=> $value->email,
					'gender'		=> $value->gender,
					'mobile'		=> $value->mobileno,
					'dob'			=> $value->dob,
					'state'			=> $value->state_id,

					'status'		=> $value->permission_request_status,
					'login_id'		=> $value->user_login_id,
					'party_code'	=> $value->CCODE,
					'party_name'	=> $value->PARTYNAME,
					//	'dist_id'		=> $value->district_id,
					//	'ac'			=> $value->ac_id,
				];
			}
			// dd($data['profile']);

			// put applicant type in session
			$userrole = $user->role_id;
			$type = DB::table('user_role')->where('role_id', $userrole)->select('role_name')->get();
			$role_type = $type[0]->role_name;
			Session::put('Applicant_type', $role_type);
			// end applicant type in session
			$res = DB::table('user_data')->where('mobileno', $mobile)->where('user_login_id', $id)->get();

			if (count($res) > 0) {
				// dd($data);
				return view('politicalparty.update_profile', $data);
			} else {
				return Redirect::to('/profile');
			}
		} else {
			return Redirect::back();
		}
	}
	public function update(Request $request)
	{
		Auth::guard('web');
		if (Auth::check()) {
			$data = $request->all();
			// dd($data);
			$users = Session::get('login_details');
			$user = Auth()->user();
			$mobile = $user->mobile;
			$user_id = $users->id;
			// dd($user);
			$data = $request->all();
			// dd($data);
			$validator = Validator::make($data, [
				'name'				=> 'required|max:100|regex:/^[a-zA-Z. ]+$/u',
				'father_name' 	    => 'required|max:100|regex:/^[a-zA-Z. ]+$/u',
				'email'				=> 'required|email|max:100',
				'radio_stacked'		=> 'required',
				// 'state'         	=>'required|max:100|not_in:0',
				//'district'     	    => 'required|max:100|not_in:0',
				//'ac'              	=> 'required|max:100|not_in:0',
				'dob' => 'required|date|before:-18 years',
				//'Address1'			=> 'required|max:200'
			]);
			if ($validator->fails()) {
				return Redirect::back()
					->withErrors($validator)
					->withInput();
			} else {
				if ($user->permission_request_status == 1) {
					$updateDetails = array(
						'email'				=> strip_tags($request->email),
						'gender'			=> strip_tags($request->radio_stacked),
						'dob'				=> $request->dob,
						//'address'			=> $request->Address1,
						//'district_id'		=> $request->district,
						//'ac_id'				=> $request->ac,
					);
					// dd($updateDetails);
				} else {
					$updateDetails = array(
						'name' 				=> strip_tags($request->name),
						'fathers_name'      => strip_tags($request->father_name),
						'email'				=> strip_tags($request->email),
						'gender'			=> strip_tags($request->radio_stacked),
						'dob'				=> $request->dob,
						//	'address'			=> $request->Address1,
						'state_id'			=> strip_tags($request->state),
						//'district_id'		=> $request->district,
						//'ac_id'				=> $request->ac,
						'party_id'			=> $request->party_master,
						'election_id'		=> $user->election_id,
					);
				}
				// dd($updateDetails);

				$update = DB::table('user_data')->where('user_login_id', $user_id)->where('mobileno', $mobile)->update($updateDetails);
				return redirect()->back()->with('message', 'Updated Successfully!');
			}
		} else {
			return Redirect::back();
		}
	}
	public function Privacy()
	{
		return view('politicalparty.Privacy_Policy');
	}
	public function Content()
	{
		return view('politicalparty.Content_Copyright');
	}
	public function Terms()
	{
		return view('politicalparty.Terms_Condition');
	}
	public function Abbreviations()
	{
		return view('politicalparty.Abbreviations');
	}

	public function getpermissiondetails($id, $status, $location)
	{
		Auth::guard('web');
		if (Auth::check()) {
			//dd($id.' '.$status.' '.$location);
			$id = Crypt::decryptString($id);
			$status = Crypt::decryptString($status);
			$location = Crypt::decryptString($location);
			$users = Session::get('login_details');
			$user = Auth()->user();
			$mobile = $user->mobile;
			$user_id = $users->id;
			// dd($location);
			$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
				->join('location_master', 'location_master.id', '=', 'permission_request.location_id')
				->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
				->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
				->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
				->join('m_district as district', function ($join) {
					$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
				})
				->join('m_ac as ac', function ($join) {
					$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
				})
				->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.location_id' => $location, 'permission_request.user_id' => $user_id,])
				->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'location_master.location_name', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.reference_id', 'permission_request.assigned_police_st_id', 'location_master.location_details', 'permission_request.fileserver_dir', 'permission_request.dist_no')
				->groupBy('user_data.name')
				->get();
			// dd($result);//'location_master.location_details'
			$pdf = DB::table('permission_request_comment')->where('permission_request_id', $id)->get();
			//dd($pdf);
			// dd($result);       	
			if ($status == 0) {
				if ($location == 'other' || $location == '0' || $location == 'null') {
					$result = DB::table('permission_request')->where('id', $id)->first();
					$ac 			= $result->ac_no;
					$dist			= $result->dist_no;
					if (($dist == null || $dist == 0) && ($ac == null || $ac == 0)) {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
							->where(['permission_request.approved_status' => $status, 'permission_request.user_id' => $user_id, 'permission_request.id' => $id])
							->select('m_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir', 'permission_request.dist_no', 'permission_request.dist_no')
							->get();
						//dd($result);    	
						return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
						// dd($ac);
					} else if ($ac == null || $ac == 0) {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
							->join('m_district as district', function ($join) {
								$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
							})
							->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
							->select('district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.ac_no', 'permission_request.reference_id', 'permission_request.fileserver_dir', 'permission_request.dist_no')
							->groupBy('user_data.name')
							->get();
						//dd($result); // ,'permission_request.ac_no'  	
						return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
					} else {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
							->join('m_district as district', function ($join) {
								$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
							})
							->join('m_ac as ac', function ($join) {
								$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
							})
							->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
							->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.cancel_status', 'permission_request.assigned_police_st_id', 'permission_request.ac_no', 'permission_request.reference_id', 'permission_request.fileserver_dir', 'permission_request.dist_no')
							->groupBy('user_data.name')
							->get();
						//dd($result);
						//,'location_master.location_name','location_master.location_details'
						//->join('location_master','location_master.id','=','permission_request.location_id')

						return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
					}
				} else {
					return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
				}
			} else if ($status == 2) {
				if ($location == 'other' || $location == '0') {
					$result = DB::table('permission_request')->where('id', $id)->first();
					$dist = $result->dist_no;
					$ac = $result->dist_no;
					//dd();
					$state =
						$ac 			= $result->ac_no;
					if (($dist == null || $dist == 0) && ($ac == null || $ac == 0)) {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
							->where(['permission_request.approved_status' => $status, 'permission_request.user_id' => $user_id, 'permission_request.id' => $id])
							->select('m_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir', 'permission_request.dist_no')
							->get();
						//dd($result);    	
						return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
					} else if ($ac == null || $ac == 0) {

						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
							->join('m_district as district', function ($join) {
								$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
							})
							->where(['permission_request.approved_status' => $status, 'permission_request.user_id' => $user_id, 'permission_request.id' => $id])
							->select('district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir', 'permission_request.dist_no')
							->get();
						//dd($result);    	
						return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
					} else {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')

							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
							->join('m_district as district', function ($join) {
								$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
							})
							->join('m_ac as ac', function ($join) {
								$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
							})
							->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
							->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.cancel_status', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir', 'permission_request.dist_no')
							->groupBy('user_data.name')
							->get();
						//->join('location_master','location_master.id','=','permission_request.location_id'),'location_master.location_name','location_master.location_details'
						return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
					}
				} else {
					return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
				}
			} else {
				if ($location == 'other' || $location == '0') {
					$result = DB::table('permission_request')->where('id', $id)->first();
					$ac 			= $result->ac_no;
					$dist			= $result->dist_no;
					if (($dist == null || $dist == 0) && ($ac == null || $ac == 0)) {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
							->where(['permission_request.approved_status' => $status, 'permission_request.user_id' => $user_id, 'permission_request.id' => $id])
							->select('m_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir', 'permission_request.dist_no')
							->get();
						//dd($result);    	
						return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
						// dd($ac);
					} else if ($ac == null || $ac == 0) {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
							->join('m_district as district', function ($join) {
								$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
							})
							->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
							->select('district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir', 'permission_request.dist_no')
							->groupBy('user_data.name')
							->get();
						// dd($result);    	
						return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
					} else {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
							->join('m_district as district', function ($join) {
								$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
							})
							->join('m_ac as ac', function ($join) {
								$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
							})
							->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
							->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.cancel_status', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir', 'permission_request.dist_no')
							->groupBy('user_data.name')
							->get();
						//dd(result);
						//->join('location_master','location_master.id','=','permission_request.location_id'),'location_master.location_name','location_master.location_details'
						return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
					}
				} else {
					return view('politicalparty.getpermissiondetail', compact('result', 'pdf'));
				}
			}
		} else {
			return Redirect::back();
		}
	}

	public function permissiondistrict($st)
	{
		// get district
		$dist = DB::table('m_district')->where('ST_CODE', $st)->orderBy('DIST_NAME', 'ASC')->get();
		return $dist;
	}
	public function permissionAC($stateID, $districtID)
	{

		$acdata = DB::table('m_ac')->where('ST_CODE', $stateID)->where('DIST_NO_HDQTR', $districtID)->orderBy('AC_NAME')->get();
		return $acdata;
	}

	public function policeAC($stateID, $acID)
	{
		$police = DB::table('police_station_master')->where('ST_CODE', $stateID)->where('ac_no', $acID)->orderBy('police_st_name')->get();
		// dd($police);
		return $police;
	}

	// for download permission
	public function downloadprint($status, $id, $location)
	{
		// dd($location);
		Auth::guard('web');
		if (Auth::check()) {
			// dd($id.' '.$status.' '.$location);
			$users = Session::get('login_details');
			$user = Auth()->user();
			$mobile = $user->mobile;
			$user_id = $users->id;
			// dd($location);
			// $pdf = PDF::loadView('admin.pc.ro.Permission.Reciept',['getDetails'=>$getDetailsview]);

			// return $pdf->download('mypdf.pdf');
			$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
				->join('location_master', 'location_master.id', '=', 'permission_request.location_id')
				->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
				->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
				->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
				->join('m_district as district', function ($join) {
					$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
				})
				->join('m_ac as ac', function ($join) {
					$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
				})
				->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.location_id' => $location, 'permission_request.user_id' => $user_id,])
				->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'location_master.location_name', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'location_master.location_details', 'permission_request.reference_id', 'permission_request.fileserver_dir')
				->groupBy('user_data.name')
				->get();
			$pdf = DB::table('permission_request_comment')->where('permission_request_id', $id)->get();
			// dd($result);       	
			if ($status == 0) {
				if ($location == 'other' || $location == '0') {
					$result = DB::table('permission_request')->where('id', $id)->first();
					$ac 			= $result->ac_no;
					$district 			= $result->dist_no;
					if ($district == null || $district == 0) {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')

							->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
							->select('m_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
							->groupBy('user_data.name')
							->get();
						// dd($result);    	
						$downloadpdf = PDF::loadView('politicalparty.printpermissionceo', compact('result', 'pdf'));
						return $downloadpdf->download('permission.pdf');
					} else {
						if ($ac == null || $ac == 0) {
							$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
								->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
								->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
								->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
								->join('m_district as district', function ($join) {
									$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
								})
								->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
								->select('district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
								->groupBy('user_data.name')
								->get();
							// dd($result);    	
							$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
							return $downloadpdf->download('permission.pdf');
						} else {
							$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')

								->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
								->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
								->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
								->join('m_district as district', function ($join) {
									$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
								})
								->join('m_ac as ac', function ($join) {
									$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
								})
								->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
								->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.cancel_status', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
								->groupBy('user_data.name')
								->get();
							//->join('location_master','location_master.id','=','permission_request.location_id'),'location_master.location_name','location_master.location_details'
							$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
							return $downloadpdf->download('permission.pdf');
						}
					}
				} else {
					$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
					return $downloadpdf->download('permission.pdf');
				}
			}
			if ($status == 1) {
				if ($location == 'other' || $location == '0') {
					$result = DB::table('permission_request')->where('id', $id)->first();
					$ac 			= $result->ac_no;
					$district 			= $result->dist_no;
					if ($district == null || $district == 0) {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')

							->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
							->select('m_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
							->groupBy('user_data.name')
							->get();
						// dd($result);    	
						$downloadpdf = PDF::loadView('politicalparty.printpermissionceo', compact('result', 'pdf'));
						return $downloadpdf->download('permission.pdf');
					} else {
						if ($ac == null || $ac == 0) {
							$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
								->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
								->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
								->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
								->join('m_district as district', function ($join) {
									$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
								})
								->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
								->select('district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
								->groupBy('user_data.name')
								->get();
							// dd($result);    	
							$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
							return $downloadpdf->download('permission.pdf');
						} else {
							$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')

								->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
								->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
								->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
								->join('m_district as district', function ($join) {
									$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
								})
								->join('m_ac as ac', function ($join) {
									$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
								})
								->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
								->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.cancel_status', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
								->groupBy('user_data.name')
								->get();
							//->join('location_master','location_master.id','=','permission_request.location_id'),'location_master.location_name','location_master.location_details'
							$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
							return $downloadpdf->download('permission.pdf');
						}
					}
				} else {
					$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
					return $downloadpdf->download('permission.pdf');
				}
			} else if ($status == 2) {
				if ($location == 'other' || $location == '0') {
					$result = DB::table('permission_request')->where('id', $id)->first();
					$ac 			= $result->ac_no;
					$district 			= $result->dist_no;
					if ($district == null || $district == 0) {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')

							->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
							->select('m_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
							->groupBy('user_data.name')
							->get();
						// dd($result);    	
						$downloadpdf = PDF::loadView('politicalparty.printpermissionceo', compact('result', 'pdf'));
						return $downloadpdf->download('permission.pdf');
					} else {
						if ($ac == null || $ac == 0) {
							$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
								->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
								->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
								->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
								->join('m_district as district', function ($join) {
									$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
								})
								->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
								->select('district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
								->groupBy('user_data.name')
								->get();
							// dd($result);    	
							$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
							return $downloadpdf->download('permission.pdf');
						} else {
							$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')

								->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
								->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
								->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
								->join('m_district as district', function ($join) {
									$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
								})
								->join('m_ac as ac', function ($join) {
									$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
								})
								->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
								->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.cancel_status', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
								->groupBy('user_data.name')
								->get();
							//->join('location_master','location_master.id','=','permission_request.location_id'),'location_master.location_name','location_master.location_details'
							$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
							return $downloadpdf->download('permission.pdf');
						}
					}
				} else {
					$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
					return $downloadpdf->download('permission.pdf');
				}
			} else {
				if ($location == 'other' || $location == '0') {
					$result = DB::table('permission_request')->where('id', $id)->first();
					$ac 			= $result->ac_no;
					$district 			= $result->dist_no;
					if ($district == null || $district == 0) {
						$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
							->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
							->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
							->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')

							->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
							->select('m_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
							->groupBy('user_data.name')
							->get();
						// dd($result);    	
						$downloadpdf = PDF::loadView('politicalparty.printpermissionceo', compact('result', 'pdf'));
						return $downloadpdf->download('permission.pdf');
					} else {
						if ($ac == null || $ac == 0) {
							$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')
								->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
								->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
								->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
								->join('m_district as district', function ($join) {
									$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
								})
								->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
								->select('district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.cancel_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
								->groupBy('user_data.name')
								->get();
							// dd($result);    	
							$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
							return $downloadpdf->download('permission.pdf');
						} else {
							$result = DB::table('permission_request')->join('user_data', 'user_data.user_login_id', '=', 'permission_request.user_id')

								->join('permission_type', 'permission_type.id', '=', 'permission_request.permission_type_id')
								->join('permission_master as m', 'm.id', '=', 'permission_type.permission_type_id')
								->join('m_state', 'm_state.ST_CODE', '=', 'permission_request.st_code')
								->join('m_district as district', function ($join) {
									$join->on('district.DIST_NO', '=', 'permission_request.dist_no')->on('district.ST_CODE', '=', 'permission_request.st_code');
								})
								->join('m_ac as ac', function ($join) {
									$join->on('ac.AC_NO', '=', 'permission_request.ac_no')->on('ac.ST_CODE', '=', 'permission_request.st_code')->on('ac.DIST_NO_HDQTR', '=', 'permission_request.dist_no');
								})
								->where(['permission_request.approved_status' => $status, 'permission_request.id' => $id, 'permission_request.user_id' => $user_id,])
								->select('ac.AC_NAME', 'district.DIST_NAME', 'm_state.ST_NAME', 'user_data.name', 'user_data.email', 'user_data.mobileno', 'user_data.gender', 'user_data.dob', 'permission_request.id as permission', 'permission_request.date_time_start', 'permission_request.date_time_end', 'permission_request.Other_location', 'permission_request.id', 'm.permission_name', 'permission_request.required_files', 'permission_request.st_code', 'permission_request.permission_mode', 'permission_request.approved_status', 'permission_request.location_id', 'permission_request.ac_no', 'permission_request.cancel_status', 'permission_request.assigned_police_st_id', 'permission_request.reference_id', 'permission_request.fileserver_dir')
								->groupBy('user_data.name')
								->get();
							//->join('location_master','location_master.id','=','permission_request.location_id'),'location_master.location_name','location_master.location_details'
							$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
							return $downloadpdf->download('permission.pdf');
						}
					}
				} else {
					$downloadpdf = PDF::loadView('politicalparty.printpermission', compact('result', 'pdf'));
					return $downloadpdf->download('permission.pdf');
				}
			}
		} else {
			return Redirect::back();
		}
	}
	// for AC election
	public function getpconac($sid, $acid, $districtID)
	{
		$type = 'AC';
		if ($acid != 0) {
			$schedule = DB::table('m_election_details')->where([['CONST_NO', $acid], ['ST_CODE', $sid], ['CONST_TYPE', $type]])->orderBy('ScheduleID', 'desc')->get();
		} else {
			$schedule = DB::table('m_election_details as a')
				->join('m_ac as g', function ($join) {
					$join->on('g.AC_NO', '=', 'a.CONST_NO')
						->on('g.ST_CODE', '=', 'a.ST_CODE');
				})
				->join('m_schedule as m', function ($join) {
					$join->on('m.SCHEDULEID', '=', 'a.ScheduleID');
				})
				->where([['m.DATE_POLL', '>', Carbon::now()->format('Y-m-d')], ['g.DIST_NO_HDQTR', $districtID], ['a.ST_CODE', $sid], ['a.CONST_TYPE', $type]])->get();
		}
		if (count($schedule) == 0) {
			return "Election date is not available";
		} else {
			$sechedule_id = $schedule[0]->ScheduleID;
			$pollday = DB::table('m_schedule')->where([['SCHEDULEID', $sechedule_id]])->orderBy('DATE_POLL', 'desc')->get();
			$poll_day = GetReadableDate($pollday[0]->DATE_POLL);
			// dd($poll_day);
			return $poll_day;
		}
		// dd($acid);
		//	 	$type = 'AC';
		//	 	$schedule = DB::table('m_election_details')->where([['CONST_NO',$acid],['ST_CODE',$sid],['CONST_TYPE',$type]])->get();
		//	 	if(count($schedule) == 0)
		//	 	{
		//	 		return "Election date is not available";
		//	 	}else{
		//	 	$sechedule_id = $schedule[0]->ScheduleID;
		//	 	$pollday = DB::table('m_schedule')->where([['SCHEDULEID',$sechedule_id]])->get();
		//	 	$poll_day= GetReadableDate($pollday[0]->DATE_POLL);
		//	 	// dd($poll_day);
		//	 	return $poll_day;
		//	 	}
	}

	public function pollonstate($StateId)
	{
		$type = 'AC';
		$schedule = DB::table('m_election_details')->where([['ST_CODE', $StateId], ['CONST_TYPE', $type]])->get();
		if (count($schedule) == 0) {
			return "Election date is not available";
		} else {
			$sechedule_id = $schedule[0]->ScheduleID;
			$pollday = DB::table('m_schedule')->where([['SCHEDULEID', $sechedule_id]])->get();
			$poll_day = GetReadableDate($pollday[0]->DATE_POLL);
			return $poll_day;
		}
	}

	public function statedatevalidation($StateId)
	{
		$data = DB::table('restriction_master')->where('st_code', $StateId)->select('st_code', 'restriction_status')->get();
		return $data;
	}
	public function getrole_iddetails(Request $request)
	{
		Auth::guard('web');
		if (Auth::check()) {
			$users = Session::get('login_details');
			$user = Auth()->user();
			$userid = $user->id;
			$mobile = $user->mobile;


			$res = DB::table('user_data')->where('mobileno', $mobile)->where('user_login_id', $userid)->get();
			$stcode = $res[0]->state_id;
			if (!empty($request->permsn_id)) {
				$permission_type_role = DB::table('permission_required_doc')
					->join('permission_type', 'permission_type.id', '=', 'permission_required_doc.permission_id')
					->where('permission_type.status', '1')
					->where('permission_required_doc.permission_id', $request->permsn_id)
					->where('permission_required_doc.st_code', $stcode)
					->select('permission_type.role_id', 'permission_type.st_code')
					->distinct()
					->get()->toArray();

				if (!empty($permission_type_role)) {
					//dd($getPermissionDetails);
					return $permission_type_role;
				} else {
					return '0';
				}
			}
			//dd($getPermissionDetails);
		} else {
			return Redirect::back();
		}
	}
	public function getpolldayss($std_code)
	{

		$schedule = DB::table('m_election_details')->where([['ST_CODE', $std_code]])->orderBy('ScheduleID', 'desc')->get();
		$sechedule_id = $schedule[0]->ScheduleID;
		$pollday = DB::table('m_schedule')->where([['SCHEDULEID', $sechedule_id]])->orderBy('DATE_POLL', 'desc')->get();
		$poll_day = GetReadableDate($pollday[0]->DATE_POLL);
		return $poll_day;
	}

	public function getdttconac($std_code, $distno)
	{

		$poll_day = DB::table('pd_scheduledetail as pds')
			->select(
				'pds.st_code',
				'mst.st_name',
				'pds.dist_no',
				'mdt.dist_name',
				'pds.ac_no',
				'mac.ac_name',
				'pds.scheduleid',
				DB::raw('MAX(mch.date_poll) as max_date_poll'),
				'mch.date_count'
			)
			->join('m_state as mst', 'mst.st_code', '=', 'pds.st_code')
			->join('m_district as mdt', function ($join) {
				$join->on('mdt.st_code', '=', 'pds.st_code')
					->on('mdt.dist_no', '=', 'pds.dist_no');
			})
			->join('m_ac as mac', function ($join) {
				$join->on('mac.st_code', '=', 'pds.st_code')
					->on('mac.ac_no', '=', 'pds.ac_no');
			})
			->join('m_schedule as mch', 'mch.scheduleno', '=', 'pds.scheduleid')
			->where('pds.st_code', $std_code)
			->where('pds.dist_no', $distno)
			->groupBy('pds.st_code', 'pds.dist_no')
			->get()->toArray();
		return $poll_day;
	}
	public function Downloadspdf($filename)
	{


		try {
			Auth::guard('web');
			if (Auth::check()) {
				$realFileName = Crypt::decryptString($filename);
				$file = public_path($realFileName);

				$customFileName = 'SuvidhaPermissionfile.pdf';

				if (!file_exists($file)) {

					abort("404");
				} else {
					return Response::make(file_get_contents($file), 200, [
						'content-type' => 'application/pdf',
						'Content-Disposition' => 'inline; filename="' . $customFileName . '"',
					]);
				}
			} else {
				return redirect('/login');
			}
		} catch (\Exception $e) {

			return response()->json(['error' => 'An error occurred while processing the request'], 500);
		}
	}
}
