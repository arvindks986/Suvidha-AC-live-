<?php /// 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function get_election_history_details($const)
{
	
	$n = DB::connection('mysql_database_history')->table('m_election_history')->where('const_type', $const)->where('officer_active_status', '1')->groupby('election_id')->orderBy('election_id', 'DESC')->get();
	
	//dd($const);
	
	return $n;
}
function get_election_history_byelectionid($ele_id)
{
	$n = DB::connection('mysql_database_history')->table('m_election_history')->where('election_id', $$ele_id)->first();
	return $n;
}
function get_max_electionid()
{
	$n = DB::connection('mysql_database_history')->table('m_election_history')->max('election_id');
	return $n;
}
function insertData($table, $data)
{
	$add = DB::table($table)->insert($data);
	return $add;
}
function updatedata($table, $field, $fieldvalue, $ndata)
{
	$g = DB::table($table)->where($field, $fieldvalue)->update($ndata);
	return ($g);
}
function get_name($where, $table, $select)
{
	$name = DB::table($table)->where($where)->select($select)->first();
	return $name;
}  /// sachchidanand   get name
function getElectionDetail($electiontype_id)
{
	$elecData = DB::table('m_election_details')->where('ELECTION_TYPEID', $electiontype_id)->get();
	return $elecData;
}  /// sachchidanand   election details
function countrecords($table, $field, $fieldvalue)
{
	$cnt = DB::table($table)->select('*')->where($field, $fieldvalue)->get()->count();
	return ($cnt);
}   /// sachchidanand   count any records
function getById($table, $field, $fieldvalue)
{
	$r = DB::table($table)->where($field, $fieldvalue)->first();
	return ($r);
}   /// sachchidanand   get records id
function getAllrecords($table, $field, $fieldvalue)
{
	$r = DB::table($table)->where($field, $fieldvalue)->get();
	return ($r);
}   /// sachchidanand   get Alll records id

function getallpartylist()
{
	$r = DB::table('m_party')->where('deleteflag', 'N')->orderBy('PARTYNAME', 'ASC')->get();
	return ($r);
}  /// sachchidanand   get Alll party list
function getallpartylistpartytype($t)
{
	$r = DB::table('m_party')->where('PARTYTYPE', $t)->where('deleteflag', 'N')->orderBy('PARTYNAME', 'ASC')->get();
	return ($r);
}  /// sachchidanand   get Alll party list
/// sachchidanand   get one party records by party id
function getpartybyid($party_id)
{
	$r = DB::table('m_party')->where('CCODE', $party_id)->first();
	return ($r);
}
/// sachchidanand   get one symbol records by symbol no
function getsymbolbyid($sid)
{
	$r = DB::table('m_symbol')->where('SYMBOL_NO', $sid)->first();
	return ($r);
}
/// sachchidanand   get  all symbol 
function getsymbollist()
{
	$r = DB::table('m_symbol')->orderBy('SYMBOL_DES', 'ASC')->get();
	return ($r);
}	 /// sachchidanand   get All Status


function getallElectionYear()
{

	$election_id = Auth::user()->election_id;

	//echo $election_id."=="; die;

	$g = DB::table('m_state')
		->join('m_election_details', [
			['m_election_details.ST_CODE', '=', 'm_state.ST_CODE'],
		])
		->where('m_election_details.CONST_TYPE', 'AC')
		->where('m_election_details.election_status', '1')
		->where('m_election_details.ELECTION_ID', $election_id)
		->groupby('m_state.ST_CODE')
		->orderBy('m_state.ST_CODE', 'ASC')->get();
	return ($g);
}

function getsymboltypelist($type)
{
	$r = DB::table('m_symbol')->where('Ind_Symbol', $type)->orderBy('SYMBOL_DES', 'ASC')->get();
	return ($r);
}
function allstatus()
{
	$get = DB::table('m_status')->select('*')->orderBy('id', 'ASC')->get();;
	return ($get);
}
/// sachchidanand   get status by status id
function getnameBystatusid($id)
{
	$g = DB::table('m_status')->select('status')->where('id', $id)->first();
	return ($g->status);
}
/// sachchidanand   geat all state
function getallstate()
{
	$election_id = Auth::user()->election_id;

	$g = DB::table('m_state')
		->join('m_election_details', [
			['m_election_details.ST_CODE', '=', 'm_state.ST_CODE'],
		])
		->where('m_election_details.CONST_TYPE', 'AC')
		->where('m_election_details.election_status', '1')
		->where('m_election_details.ELECTION_ID', $election_id)
		->groupby('m_state.ST_CODE')
		->orderBy('m_state.ST_CODE', 'ASC')->get();
	return ($g);
}
/// sachchidanand   geat  state by state code
function getstatebystatecode($st)
{
	$g = DB::table('m_state')->where('ST_CODE', $st)->first();
	return ($g);
}
/// sachchidanand   geat all district by state code
function getalldistrictbystate($st)
{
	$g = DB::table('m_district')->where('ST_CODE', $st)->orderBy('DIST_NO', 'ASC')->get();
	return ($g);
}
/// sachchidanand   get district by st code and district no
function getdistrictbydistrictno($st, $disno)
{
	$g = DB::table('m_district')->where('ST_CODE', $st)->where('DIST_NO', $disno)->first();
	return ($g);
}
/// sachchidanand   get all ac by st code
function getacbystate($st)
{
	$g = DB::table('m_ac')->where('ST_CODE', $st)->orderBy('AC_NO', 'ASC')->get();
	return ($g);
}
// get AC By District
function getacbydist($stateID, $districtID)
{
	$g = DB::table('m_ac')->where('ST_CODE', $stateID)->where('DIST_NO_HDQTR', $districtID)->get();
	return ($g);
}

/// sachchidanand   get one ac by ac noand stcode
function getacbyacno($st, $acno)
{
	$g = DB::table('m_ac')->where('ST_CODE', $st)->where('AC_NO', $acno)->first();
	return ($g);
}
/// sachchidanand   get all ac by pc no

function getallacbypcno($st, $pcno)
{
	$g = DB::table('m_ac')->where('ST_CODE', $st)->where('PC_NO', $pcno)->get();
	return ($g);
}
/// sachchidanand   get all pc by pc no.
function statepcwisedist($st, $pc)
{
	$g = DB::table('dist_pc_mapping')->where('ST_CODE', $st)->where('PC_NO', $pc)->orderBy('dc_id_id', 'ASC')->first();
	return ($g);
}
function getpcbystate($st)
{
	$g = DB::table('m_pc')->where('ST_CODE', $st)->orderBy('PC_NO', 'ASC')->get();
	return ($g);
}
/// sachchidanand   get  All pc by pc no and st code
function getpcbypcno($st, $pcno)
{
	$g = DB::table('m_pc')->where('ST_CODE', $st)->where('PC_NO', $pcno)->first();
	return ($g);
}

/// sachchidanand   get   all ac by st code and district number
function getacbystdistrict($stcode, $disttno)
{
	$getAclist = DB::table('m_ac')->where('ST_CODE', $stcode)->where('DIST_NO_HDQTR', $disttno)->orderBy('AC_NO', 'asc')->get();
	return $getAclist;
}
/// sachchidanand   get  one ac by ac no and st code
function getacname($st, $acno)
{
	$g = DB::table('m_ac')->where('ST_CODE', $st)->where('AC_NO', $acno)->first();
	return ($g);
}
function getpassword($string)
{
	return bcrypt($string);
}
function pass_encrypt($string)
{
	return base64_encode($string);
}

function pass_decrypt($string)
{

	$string =  explode("(Sb$|||@", base64_decode($string));
	$string =  explode("|||@", base64_decode($string[1]));
	return $string[1];
}

function getuser_role($user_id)
{
	$get = DB::table('user_master')->where('user_id', $user_id)->first();
	return ($get);
}
function Check_Input($input)
{
	return  $datainput = strip_tags(trim($input));
}

function CandidateECIMail($to_email, $name, $ac, $marks, $html)
{
	$email_id_to = $to_email;
	$to_name = $name;
	$data = array('name' => $to_name, 'ac' => $to_name, 'marks' => $marks, "body" => $html);
	Mail::send('email.mail', $data, function ($message) use ($to_name, $to_email) {
		$message->to($to_email, $to_name);
		$message->subject('Your Application Status');
		$message->from('sachchida.eci@gmail.com', 'Eci');
		//$message->setBody('text/html');
	});
}
function sendotpmail($to_email, $to_name, $html)
{

	$email_id_to = $to_email;
	$to_name = $to_name;
	$data = array('name' => $to_name, "body" => $html);
	Mail::send('email.otp', $data, function ($message) use ($to_name, $to_email) {
		$message->to($to_email, $to_name);
		$message->subject('Your Application OTP');
		$message->from('sachchida.eci@gmail.com', 'Eci');
		//$message->setBody('text/html');
	});
}
function sendlevelmail($to_email, $to_name, $ac, $html)
{
	$email_id_to = $to_email;
	$to_name = $to_name;
	$data = array('name' => $to_name, 'ac' => $ac, "body" => $html);
	Mail::send('email.finalize', $data, function ($message) use ($to_name, $to_email) {
		$message->to($to_email, $to_name);
		$message->subject('Finalized AC ');
		$message->from('sachchida.eci@gmail.com', 'Eci');
		//$message->setBody('text/html');
	});
}
function getBrowserType()
{
	$browser_agent = '';
	if (!empty($_SERVER['HTTP_USER_AGENT'])) {
		$browser_agent = $_SERVER['HTTP_USER_AGENT'];
	} else if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
		$browser_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
	}
	return $browser_agent;
}

function selfURL()
{
	//$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$s = "";
	$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
	return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}

function strleft($s1, $s2)
{
	return substr($s1, 0, strpos($s1, $s2));
}
function getallschedule()
{
	$get = DB::table('m_schedule')->select('*')->where('CURRENTELECTION', 'Y')->get();
	return ($get);
}

// filter in ceo role


function getallschedule_ceofilter($st_code)
{
	$get = DB::table('m_schedule')
		->join('m_election_details', [
			['m_election_details.ScheduleID', '=', 'm_schedule.SCHEDULEID'],
		])
		->join('m_state', [
			['m_election_details.ST_CODE', '=', 'm_state.ST_CODE'],
		])

		->select('m_schedule.*', 'm_election_details.ST_CODE', 'm_state.ST_NAME')->where('m_schedule.CURRENTELECTION', 'Y')->where('m_election_details.ST_CODE', $st_code)
		->groupby('m_schedule.SCHEDULEID')
		->get();
	return ($get);
}

// End






function getschedulebyid($id)
{
	$result = [
		'SCHEDULEID' => '',
		'DT_ISS_NOM' => '',
		'LDT_IS_NOM' => '',
		'DT_SCR_NOM' => '',
		'LDT_WD_CAN' => '',
		'SCHEDULENO' => '',
		'CURRENTELECTION' => '',
		'DATE_POLL' => '',
		'DATE_COUNT' => '',
		'DTB_EL_COM' => '',
		'DT_PRESS_ANNC' => '',
		'INSERTION_DATE' => '',
		'YEAR' => '',
		'MONTH' => '',
		'ELECTION_ID' => ''
	];

	$get = DB::table('m_schedule')->select('*')->where('CURRENTELECTION', 'Y')->where('SCHEDULEID', $id)->first();
	if (isset($get)) {
		$result = [
			'SCHEDULEID' => $get->SCHEDULEID,
			'DT_ISS_NOM' => $get->DT_ISS_NOM,
			'LDT_IS_NOM' => $get->LDT_IS_NOM,
			'DT_SCR_NOM' => $get->DT_SCR_NOM,
			'LDT_WD_CAN' => $get->LDT_WD_CAN,
			'SCHEDULENO' => $get->SCHEDULENO,
			'CURRENTELECTION' => $get->CURRENTELECTION,
			'DATE_POLL' => $get->DATE_POLL,
			'DATE_COUNT' => $get->DATE_COUNT,
			'DTB_EL_COM' => $get->DTB_EL_COM,
			'DT_PRESS_ANNC' => $get->DT_PRESS_ANNC,
			'INSERTION_DATE' => $get->INSERTION_DATE,
			'YEAR' => $get->YEAR,
			'MONTH' => $get->MONTH,
			'ELECTION_ID' => $get->ELECTION_ID
		];
	}

	return ($result);
}


function checkscheduledetails($dat)
{
	$date = Carbon::now();
	$currentdate = $date->format('Y-m-d');
	$st_nom = 0;
	$lt_nom = 0;
	$nom_scr = 0;
	$nom_wd = 0;
	$nom_poll = 0;
	$nom_count = 0;
	$nom_final = 0;
	if ($dat['DT_PRESS_ANNC'] <= $currentdate) {
		$st_nom = 1;
		$lt_nom = 1;
		$nom_scr = 1;
		$nom_wd = 1;
		$nom_poll = 1;
		$nom_count = 1;
		$nom_final = 1;
	}
	if ($dat['DT_ISS_NOM'] <= $currentdate) {
		$st_nom = 0;
	}
	if ($dat['LDT_IS_NOM'] < $currentdate) {
		$lt_nom = 0;
	}
	if ($dat['DT_SCR_NOM'] < $currentdate) {
		$nom_scr = 0;
	}
	if ($dat['LDT_WD_CAN'] < $currentdate) {
		$nom_wd = 0;
	}
	if ($dat['DATE_POLL'] < $currentdate) {
		$nom_poll = 0;
	}
	if ($dat['DATE_COUNT'] < $currentdate) {
		$nom_count = 0;
	}
	if ($dat['DTB_EL_COM'] < $currentdate) {
		$nom_final = 0;
	}
	$n_data = array('st_nom' => $st_nom, 'lt_nom' => $lt_nom, 'nom_scr' => $nom_scr, 'nom_wd' => $nom_wd, 'nom_poll' => $nom_poll,		'nom_count' => $nom_count, 'nom_final' => $nom_final);
	return $n_data;
}
function candidate_finalizebyro($st, $ac, $actype)
{
	$r = DB::table('candidate_finalized_ac')->where('ST_CODE', $st)->where('const_no', $ac)->where('const_type', $actype)->first();
	return $r;
}
function getloginuser($st, $ac, $actype, $deg)
{
	if ($actype == "AC")
		$r = DB::table('officer_login')->where('st_code', $st)->where('ac_no', $ac)->where('officerlevel', $actype)->where('designation', $deg)->first();
	elseif ($actype == "PC")
		$r = DB::table('officer_login')->where('st_code', $st)->where('pc_no', $ac)->where('officerlevel', $actype)->where('designation', $deg)->first();
	elseif ($actype == "DEO")
		$r = DB::table('officer_login')->where('st_code', $st)->where('dist_no', $ac)->where('officerlevel', $actype)->where('designation', $deg)->first();
	elseif ($actype == "CEO")
		$r = DB::table('officer_login')->where('st_code', $st)->where('officerlevel', $actype)->where('designation', $deg)->first();
	return $r;
}
function counting_finalize($st, $ac, $eleid)
{
	$r = DB::table('counting_finalized_ac')->where('ST_CODE', $st)->where('ac_no', $ac)->where('election_id', $eleid)->first();
	return $r;
}
function countingfinalizebyro($st, $ac, $eleid)
{
	$r = DB::table('counting_finalized_ac')->where('ST_CODE', $st)->where('ac_no', $ac)->where('election_id', $eleid)->where('finalize_by_ro', '0')->first();

	if (isset($r))
		return 1;
	else
		return 0;
}

function validate_pdf_file($file)
{

	if ($file->getMimeType() == 'application/pdf') {
		return true;
	}
	return false;
}

//PRADEEP CODES STARTS HERE

//PARSING DATE AND TIME INTO READABLE FORM STARTS
// date with time
function GetReadableDateForm($date)
{

	return Carbon::parse($date)->format('d-m-Y h:i:sa');
}
//only date
function GetReadableDate($date)
{

	return Carbon::parse($date)->format('d-m-Y');
}
//only format 01-April-2019
function GetReadableDateFormat($date)
{

	return Carbon::parse($date)->format('d-M-Y');
}
//PARSING DATE AND TIME INTO READABLE FORM ENDS

//manoj start here
function GetDateFormat($date)
{

	return Carbon::parse($date)->format('d-m-Y');
}
// manoj here
//PRADEEP CODES ENDS HERE	

function validate_request($string = '', $request=null)
{

	$base_decoded = [
		'state',
		'ccode'
	];

	$status = true;
	if ($string != '') {
		if (!preg_match('/^[a-zA-Z0-9]+$/', $string)) {
			$status = false;
		}
	}
	foreach ($request->except(['_token']) as $key => $value) {
		if (in_array($key, ['from', 'to'])) {
			if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $value)) {
				$status = false;
			}
		} else if (in_array($key, $base_decoded)) {
			if (!base64_decode($value, true)) {
				$status = false;
			}
		} else {
			if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
				$status = false;
			}
		}
	}
	return $status;
}

function validate_base64($value)
{
	if (!base64_decode($value, true)) {
		return false;
	}
	return true;
}
function getelectiondetailbystcode($st, $const_no, $const_type)
{
	$record = DB::table('m_election_details')->where('ST_CODE', $st)->where('CONST_NO', $const_no)->where('CONST_TYPE', $const_type)->first();
	return ($record);
}
function getcdacelectorsdetails($st, $acno, $year)
{
	$record = DB::table('electors_cdac')->where('st_code', $st)->where('ac_no', $acno)->where('year', $year)->first();
	return ($record);
}
function evm_votes($table, $id, $nom_id, $data = array())
{
	$sql_raw = "id, SUM(IFNULL(round1,0)+IFNULL(round2,0)+IFNULL(round3,0)+
                     	IFNULL(round4,0)+IFNULL(round5,0)+IFNULL(round6,0)+
                                IFNULL(round7,0)+IFNULL(round8,0)+IFNULL(round9,0)+
                                IFNULL(round10,0)+IFNULL(round11,0)+IFNULL(round12,0)+
                                IFNULL(round13,0)+IFNULL(round14,0)+IFNULL(round15,0)+
                                IFNULL(round16,0)+IFNULL( round17,0)+IFNULL(round18,0)+ 
                                IFNULL(round19,0)+IFNULL(round20,0)+IFNULL(round21,0)+
                                IFNULL(round22,0)+IFNULL(round23,0)+IFNULL(round24,0)+
                                IFNULL(round25,0)+IFNULL(round26,0)+IFNULL(round27,0)+
                                IFNULL(round28,0)+IFNULL(round29,0)+IFNULL(round30,0)+
                                IFNULL(round31,0)+IFNULL(round32,0)+IFNULL(round33,0)+
                                IFNULL(round34,0)+IFNULL(round35,0)+IFNULL(round36,0)+
                                IFNULL(round37,0)+IFNULL(round38,0)+IFNULL(round39,0)+
                                IFNULL(round40,0)+IFNULL(round41,0)+IFNULL(round42,0)+
                                IFNULL(round43,0)+IFNULL(round44,0)+IFNULL(round45,0)+
                                IFNULL(round46,0)+IFNULL(round47,0)+IFNULL(round48,0)+
                                IFNULL(round49,0)+IFNULL(round50,0)+IFNULL(round51,0)+
                                IFNULL(round52,0)+IFNULL(round53,0)+IFNULL(round54,0)+
                                IFNULL(round55,0)+IFNULL(round56,0)+IFNULL(round57,0)+
                                IFNULL(round58,0)+IFNULL(round59,0)+IFNULL(round60,0)+
                                IFNULL(round61,0)+IFNULL(round62,0)+
                                IFNULL(round63,0)+IFNULL(round64,0)+
                                IFNULL(round65,0)+IFNULL(round66,0)+IFNULL(round67,0)+
                                IFNULL(round68,0)+IFNULL(round69,0)+IFNULL(round70,0)+
                                IFNULL(round71,0)+IFNULL(round72,0)+IFNULL(round73,0)+
                                IFNULL(round74,0)+IFNULL(round75,0)+IFNULL(round76,0)+
                                IFNULL(round77,0)+IFNULL(round78,0)+IFNULL(round79,0)+IFNULL(round80,0)+
                                IFNULL(round81,0)+IFNULL(round82,0)+IFNULL(round83,0)+IFNULL(round84,0)+
                                IFNULL(round85,0)+IFNULL(round86,0)+IFNULL(round87,0)+IFNULL(round88,0)+
                                IFNULL(round89,0)+IFNULL(round90,0)+IFNULL(round91,0)+IFNULL(round92,0)+
                                IFNULL(round93,0)+IFNULL(round94,0)+IFNULL(round95,0)+IFNULL(round96,0)+
                                IFNULL(round97,0)+IFNULL(round98,0)+IFNULL(round99,0)+IFNULL(round100,0)+
                                IFNULL(round101,0)+IFNULL(round102,0)+IFNULL(round103,0)+IFNULL(round104,0)+
                                IFNULL(round105,0)+IFNULL(round106,0)+IFNULL(round107,0)+IFNULL(round108,0)+
                                IFNULL(round109,0)+IFNULL(round110,0)+IFNULL(round111,0)+IFNULL(round112,0)+
                                IFNULL(round113,0)+IFNULL(round114,0)+IFNULL(round115,0)+IFNULL(round116,0)+
                                IFNULL(round117,0)+IFNULL(round118,0)+IFNULL(round119,0)+IFNULL(round120,0)+
                                IFNULL(round121,0)+IFNULL(round122,0)+IFNULL(round123,0)+IFNULL(round124,0)+
                                IFNULL(round125,0)+IFNULL(round126,0)+IFNULL(round127,0)+IFNULL(round128,0)+
                                IFNULL(round129,0)+IFNULL(round130,0)) AS grant_total";

	$sql = DB::table($table);
	$sql->selectRaw($sql_raw);
	$sql->where("id", $id);
	$sql->where("nom_id", $nom_id);
	$query = $sql->first();

	return $query;
}



//Custom function

function completeRound($s_code = "", $ac_no = "")
{
	$ac = trim($ac_no);
	$year = 2019;
	if ($s_code) {
		$table = 'counting_master_' . strtolower(trim($s_code));
		if (!empty($ac_no)) {
			$select = DB::raw("SELECT complete_round FROM $table AS CM WHERE CM.ac_no=$ac GROUP BY CM.ac_no");
		} else {
			$select = DB::raw("SELECT SUM(temp.sss) as complete_round FROM( SELECT complete_round AS sss FROM $table AS CM GROUP BY CM.ac_no)temp");
		}
		$result = DB::select($select);
		if ($result) {
			return $result[0]->complete_round;
		} else {
			return 0;
		}
	}
}

function completeRound_ac_total($s_code = "")
{
	if ($s_code) {
		$select = DB::raw("SELECT COUNT(*) as round_scheduled FROM `round_master` WHERE `st_code` = '$s_code' ");
	}
	$result = DB::select($select);
	if ($result) {
		return $result[0]->round_scheduled;
	} else {
		return 0;
	}
}


function getAcListDropdown($s_code = "", $ac_no = "")
{
	if ($s_code) {
		$election_id = Auth::user()->election_id;

		$ac = DB::table('m_ac as ac')
			->leftjoin('m_election_details as election', [
				['election.CONST_NO', '=', 'ac.AC_NO'],
				['election.ST_CODE', '=', 'ac.ST_CODE'],
			])
			->select('ac.AC_NO AS ac_no', 'ac.AC_NAME AS ac_name')
			->where('ac.ST_CODE', '=', $s_code)
			->where('election.CONST_TYPE', '=', 'AC')
			->where('election.ELECTION_ID', '=', $election_id)
			->orderByRaw('ac.AC_NO')
			->get();
		$myData = '';
		$myData .= '<option value="0"';
		if ($ac_no == 0) {
			$myData .= 'selected';
		}
		$myData .= '>Select All</option>';
		foreach ($ac as $data) {
			$myData .= '<option value="' . $data->ac_no . '"';
			if ($ac_no == $data->ac_no) {
				$myData .= 'selected';
			}
			$myData .= '>' . $data->ac_no . ' -' . $data->ac_name . '</option>';
		}
		$myData .= '</select>';
		return $myData;
	} else {
		$myData = '';
		$myData = '<option value="0">Select All</option></select>';
		return $myData;
	}
}

function format_digit($num, $format = false)
{
	if (!$format) {
		return $num;
	}
	$explrestunits = "";
	if (strlen($num) > 3) {
		$lastthree = substr($num, strlen($num) - 3, strlen($num));
		$restunits = substr($num, 0, strlen($num) - 3); // extracts the last three digits
		$restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
		$expunit = str_split($restunits, 2);
		for ($i = 0; $i < sizeof($expunit); $i++) {
			// creates each of the 2's group and adds a comma to the end
			if ($i == 0) {
				$explrestunits .= (int)$expunit[$i] . ","; // if is first value , convert into integer
			} else {
				$explrestunits .= $expunit[$i] . ",";
			}
		}
		$thecash = $explrestunits . $lastthree;
	} else {
		$thecash = $num;
	}
	return $thecash; // writes the final format where $currency is the currency symbol.
}
function updateEvmByIdAc($data)
{
	// $mongo_data_array = [];
	// //$data = ["add_evm_vote"=>1000, "total_vote"=>60000, "nom_id"=>3021, "st_code"=>"S01", "pc_no"=>7];
	// $mongo_data_array[] = ["add_evm_vote"=>1000, "total_vote"=>7000, "nom_id"=>1355, "st_code"=>"S01", "ac_no"=>4];
	// $mongo_data_array[] =["add_evm_vote"=>12000, "total_vote"=>13000, "nom_id"=>1355, "st_code"=>"S01", "ac_no"=>4];


	$url = "https://resultapi.eci.gov.in/v1/apiRoutes/updateEvmByIdAc";
	//$url ="http://localhost:3000/v1/apiRoutes/updateEvmByIdAc";
	//Andhra Pradesh, Amalapuram, MORTHA SIVA RAMA KRISHNA   

	$fields["mongo_data"] = $data;

	$headers = array(
		"x-access-token: 4AAQSkZJRgABAQAAAQABAAD2wBDAAEBAQEBAQeweEBAQEBAQEBAQsdfsdfaEBAQEBAQEBAQEBAQEBAQEBPmDG9y8aiU3Hv2Fkx8AHEQcSw3G2DyZW1FelRLieg3RlNYhrQswWGRBsodFHE58CMPrrrrHNrIIutQI8SU7HZhV4YgJHhwOK23tYEwHk92HAbjx3PrbXSt5ktNB858o2RDbYpqDxV4f2SE0N2bYLcOxGMS3zux6Lsio1tjmin9EoymydOZfd1TFJD6EyKI",
		"content-type: application/json"
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	print_r($result);
	return true;
}

function updatePostalByIdAc($data)
{
	// $mongo_data_array = [];


	//  $mongo_data_array[] = ["add_postal_vote"=>1000, "total_vote"=>7000, "nom_id"=>1355, "st_code"=>"S23", "ac_no"=>4];
	// $mongo_data_array[] =["add_postal_vote"=>12000, "total_vote"=>13000, "nom_id"=>1355, "st_code"=>"S23", "ac_no"=>4];


	$url = "https://resultapi.eci.gov.in/v1/apiRoutes/updatePostalByIdAc";
	//$url ="http://localhost:3000/v1/apiRoutes/updatePostalByIdAc";

	//$fields = $data;
	$fields["mongo_data"] = $data;

	$headers = array(
		"x-access-token: 4AAQSkZJRgABAQAAAQABAAD2wBDAAEBAQEBAQeweEBAQEBAQEBAQsdfsdfaEBAQEBAQEBAQEBAQEBAQEBPmDG9y8aiU3Hv2Fkx8AHEQcSw3G2DyZW1FelRLieg3RlNYhrQswWGRBsodFHE58CMPrrrrHNrIIutQI8SU7HZhV4YgJHhwOK23tYEwHk92HAbjx3PrbXSt5ktNB858o2RDbYpqDxV4f2SE0N2bYLcOxGMS3zux6Lsio1tjmin9EoymydOZfd1TFJD6EyKI",
		"content-type: application/json"
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	print_r($result);
	return true;
}

function updateWinningLeadingAc($data)
{

	//  $data = [
	//    "st_code" => "S01",
	//     "ac_no"  => "20",
	//     "candidate_id"  => 0,
	//     "nomination_id"  => 0,
	//     "lead_cand_name"  => "",
	//     "lead_cand_hname" => "ddddddddddddddddddddd",
	//     "lead_cand_partyid" => null,
	//     "lead_cand_party" => null,
	//     "lead_cand_hparty" => null,
	//     "lead_party_type"  => null,
	//     "lead_party_abbre"  => null,
	//     "lead_hpartyabbre"  => null,
	//     "trail_candidate_id"  => 0,
	//     "trail_nomination_id"  => 0,
	//     "trail_cand_name"  => null,
	//     "trail_cand_hname"  => null,
	//     "trail_cand_partyid"  => null,
	//     "trail_cand_party"  => null,
	//     "trail_cand_hparty"  => null,
	//     "trail_party_type"  => null,
	//     "trail_party_abbre"  => null,
	//     "trail_hpartyabbre" => null,
	//     "lead_total_vote"  => 12000,
	//     "trail_total_vote"  => 0,
	//     "margin"  => 0


	// ];
	$url = "https://resultapi.eci.gov.in/v1/apiRoutes/updateWinningLeadingAc";
	//$url ="http://localhost:3000/v1/apiRoutes/updateWinningLeadingAc";

	$fields = $data;

	$headers = array(
		"x-access-token: 4AAQSkZJRgABAQAAAQABAAD2wBDAAEBAQEBAQeweEBAQEBAQEBAQsdfsdfaEBAQEBAQEBAQEBAQEBAQEBPmDG9y8aiU3Hv2Fkx8AHEQcSw3G2DyZW1FelRLieg3RlNYhrQswWGRBsodFHE58CMPrrrrHNrIIutQI8SU7HZhV4YgJHhwOK23tYEwHk92HAbjx3PrbXSt5ktNB858o2RDbYpqDxV4f2SE0N2bYLcOxGMS3zux6Lsio1tjmin9EoymydOZfd1TFJD6EyKI",
		"content-type: application/json"
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	print_r($result);
	return true;
}

function updateWinningLeadingStatusAc($data)
{


	// $data = [  
	//      "st_code" => "S01",
	//      "ac_no" => 20,
	//      //"nomination_id" => 0,
	//      "status" => "1"
	//      ];


	$url = "https://resultapi.eci.gov.in/v1/apiRoutes/updateWinningLeadingStatusAc";
	//$url ="http://localhost:3000/v1/apiRoutes/updateWinningLeadingStatusAc";

	$fields = $data;

	$headers = array(
		"x-access-token: 4AAQSkZJRgABAQAAAQABAAD2wBDAAEBAQEBAQeweEBAQEBAQEBAQsdfsdfaEBAQEBAQEBAQEBAQEBAQEBPmDG9y8aiU3Hv2Fkx8AHEQcSw3G2DyZW1FelRLieg3RlNYhrQswWGRBsodFHE58CMPrrrrHNrIIutQI8SU7HZhV4YgJHhwOK23tYEwHk92HAbjx3PrbXSt5ktNB858o2RDbYpqDxV4f2SE0N2bYLcOxGMS3zux6Lsio1tjmin9EoymydOZfd1TFJD6EyKI",
		"content-type: application/json"
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	print_r($result);
	return true;
}



function uploadedACTotal($state_code = "")
{
	$state = trim($state_code);
	if ($state) {
		$select = DB::raw("SELECT COUNT(*) AS TOTAL FROM counting_form21_detail AS FRM WHERE FRM.st_code='$state' GROUP BY FRM.st_code");
		$result = DB::select($select);
		if (count($result) > 0) {
			$data = $result[0]->TOTAL;
		} else {
			$data = 0;
		}
		return $data;
	}
	return 0;
}

function notUploadedACTotal($state_code = "")
{
	$state = trim($state_code);
	if ($state) {
		$result = DB::select(DB::raw("SELECT COUNT(*) AS TOTAL FROM counting_form21_detail AS FRM 
				WHERE FRM.st_code='$state' GROUP BY FRM.st_code"));
		$totalAC = DB::select(DB::raw("SELECT COUNT(*) AS TOTAL_AC FROM winning_leading_candidate AS 
				FRM WHERE FRM.st_code='$state'"));
		if (count($result) > 0) {
			$data = $totalAC[0]->TOTAL_AC - $result[0]->TOTAL;
		} else {
			$data = $totalAC[0]->TOTAL_AC;
		}
		return $data;
	}
	return 0;
}

function getElectionYear()
{
	$get = DB::table('m_election_details')->select('YEAR')->where('CONST_TYPE', 'AC')->first();
	return ($get->YEAR);
}

function getElectionType($st_code, $ac_no)
{
	$get = DB::table('m_election_details')->select('ELECTION_TYPE')
		->where('ST_CODE', $st_code)
		->where('CONST_NO', $ac_no)
		->where('CONST_TYPE', 'AC')
		->first();
	return ($get->ELECTION_TYPE);
}

function encrypt_string($string)
{
	return Crypt::encryptString($string);
}

function decrypt_string($string)
{
	return Crypt::decryptString($string);
}

function generate_url($path)
{
	$url = '/';
	if (\Auth::user() && \Auth::user()->role_id == '19') {
		$url .= 'roac';
	} else if (\Auth::user() && \Auth::user()->role_id == '4') {
		$url .= 'acceo';
	} else if (\Auth::user() && \Auth::user()->role_id == '7') {
		$url .= 'eci';
	} else if (\Auth::user() && \Auth::user()->role_id == '27') {
		$url .= 'eci-index';
	} else {
		$url .= '';
	}
	return url($url . '/' . $path);
}
function getcountingtype($data = array())
{

	$sql_raw = "id, st_code,election_id, election_typeid,ac_no,pc_no,const_type,counting_type,created_at";

	$sql = DB::table('counting_ro_type')->selectRaw($sql_raw);

	if (!empty($data['election_id'])) {
		$sql->where("election_id", $data['election_id']);
	}
	if (!empty($data['st_code'])) {
		$sql->where("st_code", $data['st_code']);
	}

	if (!empty($data['const_type'])) {
		if ($data['const_type'] == "PC") {
			$sql->where("pc_no", $data['pc_no']);
		}
		if ($data['const_type'] == "AC") {
			$sql->where("ac_no", $data['ac_no']);
		}
	}

	$query = $sql->first();

	if (isset($query))
		return $query;
	else
		return false;
}
function candidate_definalize($data = array())
{
	// $filter = [
	// 	'st_code'       =>'',
	// 	'const_no' 		=>'',
	// 	'election_id'	=>'',
	// 	'const_type'	=>'',
	// 	'finalize_by'	=>'',
	// 	'message'	=>'', 
	// 	 ];
	$candData = array('indexcard_finalize' => '0', 'indexcard_date' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => Auth::user()->officername);
	//$finalize = array( 'finalize'=>'0','updated_at'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d"),'updated_by'=>Auth::user()->officername);

	$finalize = array();


	$definalize = array(
		'st_code' => $data['st_code'], 'pc_no' => '0',
		'ac_no' => $data['const_no'], 'doc_type' => 'Candidate',
		'message' => $data['message'], 'created_at' => date("Y-m-d H:i:s"),
		'added_update_at' => date("Y-m-d"), 'created_by' => Auth::user()->officername
	);

	/*  DB::table('candidate_nomination_detail')
         			->where('st_code',$data['st_code'])
         			->where('ac_no',$data['const_no'])
         			->where('election_id',$data['election_id'])
         			->update($finalize); */

	DB::table('definalized_logs')->insert($definalize);
	$a = DB::table('candidate_finalized_ac')
		->where('st_code', $data['st_code'])
		->where('const_no', $data['const_no'])
		->where('const_type', $data['const_type'])
		->where('election_id', $data['election_id'])
		->update($candData);
	return  $a;
}

function counting_definalize($data = array())
{
	// $filter = [
	// 'st_code'       =>'',
	// 'const_no' 		=>'',
	// 'election_id'	=>'',
	// 'const_type'	=>'', AC or PC
	// 'finalize_by'	=>'',
	// 	'message'	=>'',  
	//  ];
	$table = "counting_master_" . strtolower($data['st_code']);
	$winning = array('status' => '0',);
	$finalize = array('finalized_round' => '0', 'updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => Auth::user()->officername);
	$pcfinalize = array('finalize' => '0', 'updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => Auth::user()->officername);
	$counting = array('finalized_round' => '0', 'updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => Auth::user()->officername);

	$countingfinalize = array('finalized_ac' => '0', 'finalize_by_ro' => '0', 'updated_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'updated_by' => Auth::user()->officername);
	$definalize = array(
		'st_code' => $data['st_code'], 'ac_no' => $data['const_no'],
		'pc_no' => '0', 'doc_type' => 'counting', 'message' => $data['message'], 'created_at' => date("Y-m-d H:i:s"), 'added_update_at' => date("Y-m-d"), 'created_by' => Auth::user()->officername, 'election_id' => $data['election_id']
	);
	DB::table('winning_leading_candidate')
		->where('st_code', $data['st_code'])
		->where('ac_no', $data['const_no'])
		->where('election_id', $data['election_id'])
		->update($winning);

	DB::table($table)
		//->where('st_code',$data['st_code'])
		->where('ac_no', $data['const_no'])
		->where('election_id', $data['election_id'])
		->update($finalize);


	DB::table('definalized_logs')->insert($definalize);
	$a = DB::table('counting_finalized_ac')
		->where('st_code', $data['st_code'])
		->where('ac_no', $data['const_no'])
		->where('election_id', $data['election_id'])
		->update($countingfinalize);
	return  $a;
}

function generate_unique_string($id = 0)
{
	$length = 40;
	$token = substr_replace(str_random($length), $id, 10, 0) . time() . rand(0000, 9999);
	return $token;
}

function total_evm_votes($data = array())
{
	$sql_raw = "id," . $data['round'] . ", SUM(IFNULL(round1,0)+IFNULL(round2,0)+IFNULL(round3,0)+
                     	IFNULL(round4,0)+IFNULL(round5,0)+IFNULL(round6,0)+
                                IFNULL(round7,0)+IFNULL(round8,0)+IFNULL(round9,0)+
                                IFNULL(round10,0)+IFNULL(round11,0)+IFNULL(round12,0)+
                                IFNULL(round13,0)+IFNULL(round14,0)+IFNULL(round15,0)+
                                IFNULL(round16,0)+IFNULL( round17,0)+IFNULL(round18,0)+ 
                                IFNULL(round19,0)+IFNULL(round20,0)+IFNULL(round21,0)+
                                IFNULL(round22,0)+IFNULL(round23,0)+IFNULL(round24,0)+
                                IFNULL(round25,0)+IFNULL(round26,0)+IFNULL(round27,0)+
                                IFNULL(round28,0)+IFNULL(round29,0)+IFNULL(round30,0)+
                                IFNULL(round31,0)+IFNULL(round32,0)+IFNULL(round33,0)+
                                IFNULL(round34,0)+IFNULL(round35,0)+IFNULL(round36,0)+
                                IFNULL(round37,0)+IFNULL(round38,0)+IFNULL(round39,0)+
                                IFNULL(round40,0)+IFNULL(round41,0)+IFNULL(round42,0)+
                                IFNULL(round43,0)+IFNULL(round44,0)+IFNULL(round45,0)+
                                IFNULL(round46,0)+IFNULL(round47,0)+IFNULL(round48,0)+
                                IFNULL(round49,0)+IFNULL(round50,0)+IFNULL(round51,0)+
                                IFNULL(round52,0)+IFNULL(round53,0)+IFNULL(round54,0)+
                                IFNULL(round55,0)+IFNULL(round56,0)+IFNULL(round57,0)+
                                IFNULL(round58,0)+IFNULL(round59,0)+IFNULL(round60,0)+
                                IFNULL(round61,0)+IFNULL(round62,0)+
                                IFNULL(round63,0)+IFNULL(round64,0)+
                                IFNULL(round65,0)+IFNULL(round66,0)+IFNULL(round67,0)+
                                IFNULL(round68,0)+IFNULL(round69,0)+IFNULL(round70,0)+
                                IFNULL(round71,0)+IFNULL(round72,0)+IFNULL(round73,0)+
                                IFNULL(round74,0)+IFNULL(round75,0)+IFNULL(round76,0)+
                                IFNULL(round77,0)+IFNULL(round78,0)+IFNULL(round79,0)+IFNULL(round80,0)+
                                IFNULL(round81,0)+IFNULL(round82,0)+IFNULL(round83,0)+IFNULL(round84,0)+
                                IFNULL(round85,0)+IFNULL(round86,0)+IFNULL(round87,0)+IFNULL(round88,0)+
                                IFNULL(round89,0)+IFNULL(round90,0)+IFNULL(round91,0)+IFNULL(round92,0)+
                                IFNULL(round93,0)+IFNULL(round94,0)+IFNULL(round95,0)+IFNULL(round96,0)+
                                IFNULL(round97,0)+IFNULL(round98,0)+IFNULL(round99,0)+IFNULL(round100,0)+
                                IFNULL(round101,0)+IFNULL(round102,0)+IFNULL(round103,0)+IFNULL(round104,0)+
                                IFNULL(round105,0)+IFNULL(round106,0)+IFNULL(round107,0)+IFNULL(round108,0)+
                                IFNULL(round109,0)+IFNULL(round110,0)+IFNULL(round111,0)+IFNULL(round112,0)+
                                IFNULL(round113,0)+IFNULL(round114,0)+IFNULL(round115,0)+IFNULL(round116,0)+
                                IFNULL(round117,0)+IFNULL(round118,0)+IFNULL(round119,0)+IFNULL(round120,0)+
                                IFNULL(round121,0)+IFNULL(round122,0)+IFNULL(round123,0)+IFNULL(round124,0)+
                                IFNULL(round125,0)+IFNULL(round126,0)+IFNULL(round127,0)+IFNULL(round128,0)+
                                IFNULL(round129,0)+IFNULL(round130,0)) AS grant_total, postalballot_vote,complete_round";

	$sql = DB::table($data['table']);
	$sql->selectRaw($sql_raw);
	$sql->where("ac_no", $data['ac_no']);
	$sql->where("nom_id", $data['nom_id']);
	$query = $sql->first();

	return $query;
}

//03_10_2019_mayank
function getAffidvitById($nom_id, $cand_id)
{
	$get = DB::table('candidate_affidavit_detail')->select('candidate_affidavit_id', 'affidavit_path', 'st_code', 'pc_no', 'ac_no')
		->where('nom_id', $nom_id)
		->where('candidate_id', $cand_id)
		->first();
	return $get;
}

function getRonameById($parent_id)
{
	$get = DB::table('officer_login')->select('id', 'name', 'parent_id', 'designation', 'placename', 'st_code', 'dist_no', 'ac_no')
		->where('id', $parent_id)->first();
	return $get;
}

function checkcountingstart($data = array())
{
	if (empty($data['st_code'])) {
		return false;
	}
	$sql_row = "*";
	$sql = DB::table("counting_ps_" . strtolower($data['st_code']))->selectRaw($sql_row);

	if (!empty($data['ac_no'])) {
		$sql->where('ac_no', $data['ac_no']);
	}
	if (!empty($data['election_id'])) {
		$sql->where('election_id', $data['election_id']);
	}

	$query = $sql->first();

	return  $query;
}
//
function evm_votes_finalized($data = array())
{
	$sql_row = "id,st_code,ac_no,election_id,finalized_ac,finalize_by_ro";
	$sql = DB::table('counting_finalized_ac')->selectRaw($sql_row);

	if (!empty($data['st_code'])) {
		$sql->where('st_code', $data['st_code']);
	}
	if (!empty($data['ac_no'])) {
		$sql->where('ac_no', $data['ac_no']);
	}
	if (!empty($data['election_id'])) {
		$sql->where('election_id', $data['election_id']);
	}

	$query = $sql->first();

	return  $query->finalized_ac;
}
function empty_ps_finalized($data = array())
{
	$sql_row = "id,st_code,ac_no,election_id,verify_empty_ps";
	$sql = DB::table('counting_finalized_ac')->selectRaw($sql_row);

	if (!empty($data['st_code'])) {
		$sql->where('st_code', $data['st_code']);
	}
	if (!empty($data['ac_no'])) {
		$sql->where('ac_no', $data['ac_no']);
	}
	if (!empty($data['election_id'])) {
		$sql->where('election_id', $data['election_id']);
	}

	$query = $sql->first();

	return  $query->verify_empty_ps;
}
function postal_votes_finalized($data = array())
{
	$sql_row = "id,st_code,ac_no,election_id,finalized_ac,finalize_by_ro";
	$sql = DB::table('counting_finalized_ac')->selectRaw($sql_row);

	if (!empty($data['st_code'])) {
		$sql->where('st_code', $data['st_code']);
	}
	if (!empty($data['ac_no'])) {
		$sql->where('ac_no', $data['ac_no']);
	}
	if (!empty($data['election_id'])) {
		$sql->where('election_id', $data['election_id']);
	}

	$query = $sql->first();

	return  $query->finalize_by_ro;
}
function countingfinalize($data = array())
{
	$sql_row = "id,st_code,ac_no,election_id,finalized_ac,finalize_by_ro";
	$sql = DB::table('counting_finalized_ac')->selectRaw($sql_row);

	if (!empty($data['st_code'])) {
		$sql->where('st_code', $data['st_code']);
	}
	if (!empty($data['ac_no'])) {
		$sql->where('ac_no', $data['ac_no']);
	}
	if (!empty($data['election_id'])) {
		$sql->where('election_id', $data['election_id']);
	}
	$query = $sql->first();

	if ($query->finalize_by_ro == 0 || $query->finalized_ac == 0) {
		return 0;
	} else {
		return 1;
	}
}
/// sachchidanand   geat  state by state code
function getstatelanguage($st)
{
	$rec = DB::table('m_state_language')->where('ST_CODE', $st)->first();
	return ($rec->vname);
}

function getpollingstationname($st, $ac, $psno)
{
	$result = [
		'PS_NAME_EN' => '',
	];
	$records = DB::table('polling_station')->selectRaw('PS_NAME_EN')
		->where('ST_CODE', $st)->where('AC_NO', $ac)
		->where('PS_NO', $psno)->first();
	if (isset($records)) {
		$result = ['PS_NAME_EN' => $records->PS_NAME_EN,];
	}
	return $result;
}
/// sachchidanand   geat all district by state code

//Function to verify added by praveen for index card report
function verifyreport($report_no, $st_code)
{

	$verified =  DB::table('statical_report_verification_details')->select('verifiat_date', 'is_verified')
		->where('report_no', $report_no)
		->where('st_code', $st_code)
		->get();

	if (isset($verified[0])) {
		if (($verified[0]->is_verified) != 0) {
			return $verified[0]->verifiat_date;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}


//get ip address

function get_client_ip()
{
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP']))
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_X_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if (isset($_SERVER['REMOTE_ADDR']))
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}

function getreportsequence($report_no, $st_code)
{
	$verified =  DB::table('statical_report_verification_details')->select('report_sequence')
		->where('report_no', $report_no)
		->where('st_code', $st_code)
		->get();
	if (isset($verified[0])) {
		return $verified[0]->report_sequence;
	} else {
		return 0;
	}
}

function is_valid_indexcard()
{
	$object = DB::table("m_schedule")->select("DATE_COUNT")->orderBy('DATE_COUNT', 'DESC')->first();
	if (!$object) {
		return 0;
	}
	if (strtotime($object->DATE_COUNT . ' - 30 days') < strtotime(date('Y-m-d'))) {
		return 1;
	}
	return 0;
}
function verifyreport_index($st_code, $ac_no)
{

	$verified =  DB::table('bye_election_report_verify')->select('verifiat_date', 'is_verified')
		->where('ac_no', $ac_no)
		->where('st_code', $st_code)
		->get();

	if (isset($verified[0])) {
		if (($verified[0]->is_verified) != 0) {
			return $verified[0]->verifiat_date;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}
function getcdacelector($st, $acno, $election_id)
{
	$record = DB::table('electors_cdac')->where('st_code', $st)->where('ac_no', $acno)->where('election_id', $election_id)->first();
	return ($record);
}
/* Sanjay code random salt for authentication */
function createSalt()
{
	$Alpha22 = range("A", "Z");
	$Alpha12 = range("A", "Z");
	$alpha22 = range("a", "z");
	$alpha12 = range("a", "z");
	$num22 = range(1000, 9999);
	$num12 = range(1000, 9999);
	$numU22 = range(99999, 10000);
	$numU12 = range(99999, 10000);
	$AlphaB22 = array_rand($Alpha22);
	$AlphaB12 = array_rand($Alpha12);
	$alphaS22 = array_rand($alpha22);
	$alphaS12 = array_rand($alpha12);
	$Num22 = array_rand($num22);
	$NumU22 = array_rand($numU22);
	$Num12 = array_rand($num12);
	$NumU12 = array_rand($numU12);
	$res22 = $Alpha22[$AlphaB22] . $num22[$Num22] . $Alpha12[$AlphaB12] . $numU22[$NumU22] . $alpha22[$alphaS22] . $num12[$Num12];
	$text22 = str_shuffle($res22);
	$get_salt = $text22;
	Session::put('randnmbr', $text22);
	return $get_salt;
}


function wb_payment_encrypt($data, $algo, $key, $iv)
{
	$cipherText = openssl_encrypt(
		$data,
		$algo,
		$key,
		OPENSSL_RAW_DATA,
		$iv
	);
	$cipherText = base64_encode($cipherText);
	return $cipherText;
}
function wb_payment_decrypt($data, $algo, $key, $iv)
{
	$cipherText = base64_decode($data);
	$plaintext = openssl_decrypt(
		$cipherText,
		$algo,
		$key,
		OPENSSL_RAW_DATA,
		$iv
	);
	return $plaintext;
}
function wb_hash256($input)
{
	$hash = hash("sha256", utf8_encode($input));
	$output = "";
	foreach (str_split($hash, 2) as $key => $value) {
		if (strpos($value, "0") === 0) {
			$output .= str_replace("0", "", $value);
		} else {
			$output .= $value;
		}
	}
	return $output;
}
function generate_pd_string($input, $strength = 16)
{
	$input_length = strlen($input);
	$random_string = '';
	for ($i = 0; $i < $strength; $i++) {
		$random_character = $input[mt_rand(0, $input_length - 1)];
		$random_string .= $random_character;
	}

	return $random_string;
}



function goa_payment_encrypt($data)
{

	//Generate a 128-bit encryption key
	$key = file_get_contents(storage_path("key/Election.KEY"));
	$encrypted_data = openssl_encrypt($data, "AES-128-CBC", $key, $raw_output = false, $key);
	//echo '---------->'.$encrypted_data;
	return $encrypted_data;
}

function goa_payment_decrypt($data)
{
	//Generate a 128-bit decryption key	
	$key = file_get_contents(storage_path("key/Election.KEY"));
	$decrypted_data = openssl_decrypt($data, "AES-128-CBC", $key, $raw_output = false, $key);

	return $decrypted_data;
}

function CheckSum($data)
{
	//demo
	//$key = 'UATEFK9tEbMS';

	//live
	$key = 'YzvDB2JLgv8w';

	$sign  = hash_hmac('sha512', $data, $key);
	return $sign;
}

function encrypt_punjab($data)
{
	$method = "AES-128-CBC";
	//demo
	//$key = 'UATAxMgkyV3majMS';   
	// $iv = 'UATAxMgkyV3majMS';

	//live
	$key = 'UxFA1OG4Qx8xOhtC';
	$iv = 'UxFA1OG4Qx8xOhtC';


	$ciphertext = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
	$hash = hash_hmac('sha256', $ciphertext . $iv, $key, true);
	return base64_encode($ciphertext);
}

function get_ca_count($st_code, $party_id, $is_criminal, $phase, $app_status)
{
	$count = 0;
	$res = DB::table('candidate_nomination_detail')
		->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
		->where('st_code', $st_code)
		->where('party_id', $party_id)
		->where('is_criminal', $is_criminal)
		->whereNotIn("candidate_nomination_detail.application_status", array(7, 11))
		->where("candidate_nomination_detail.party_id", "!=",  1180)
		->where("candidate_nomination_detail.candidate_id", "!=",  1513)
		->where("candidate_nomination_detail.finalize", '1')
		->where("candidate_nomination_detail.symbol_id", "!=",  '200')
		->groupBy("candidate_nomination_detail.candidate_id")
		->groupBy('candidate_nomination_detail.party_id');

	if ($phase != "0") {
		$res->where('candidate_nomination_detail.scheduleid', $phase);
	}
	if ($app_status != "0") {
		$res->where('candidate_nomination_detail.application_status', $app_status);
		if ($app_status == 6)
			$res->where('candidate_nomination_detail.finalaccepted', 1);
	}
	$result = $res->get();

	if ($result) {
		$res_pdf = count($result);
	}

	return $res_pdf;
}


function get_acwise_count($st_code, $ac_no, $is_criminal, $phase, $app_status)
{
	$count = 0;
	$res = DB::table('candidate_nomination_detail')
		->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
		->where('st_code', $st_code)
		->where('ac_no', $ac_no)
		->where('is_criminal', $is_criminal)
		->whereNotIn("candidate_nomination_detail.application_status", array(7, 11))
		->where("candidate_nomination_detail.party_id", "!=",  1180)
		->where("candidate_nomination_detail.candidate_id", "!=",  1513)
		->where("candidate_nomination_detail.finalize", '1')
		->where("candidate_nomination_detail.symbol_id", "!=",  '200')
		->groupBy("candidate_nomination_detail.candidate_id")
		->groupBy('candidate_nomination_detail.ac_no');

	if ($phase != "0") {
		$res->where('candidate_nomination_detail.scheduleid', $phase);
	}
	if ($app_status != "0") {
		$res->where('candidate_nomination_detail.application_status', $app_status);
		if ($app_status == 6)
			$res->where('candidate_nomination_detail.finalaccepted', 1);
	}
	$result = $res->get();

	if ($result) {
		$res_pdf = count($result);
	}

	return $res_pdf;
}




// functions for  Himachal

function getKey($keyPath)
{
	$key = file_get_contents($keyPath);

	$keyByte = substr($key, 0, 16);


	return $keyByte;
}




function checkSumHimachal($data)
{
	return md5($data);;
}

function encrypt_himachal($str)
{

	$method = "AES-128-CBC";

	$keydata = file_get_contents(storage_path("key/echallan.key"));

	$key = substr($keydata, 0, 16);

	$iv = $key;

	$ciphertext = openssl_encrypt($str, $method, $key, $options = OPENSSL_RAW_DATA, $iv);
	// Base 64 encode the encrypted session ID.
	$encryptedSessionId = base64_encode($ciphertext);


	return $encryptedSessionId;
}


function decrypt_himachal($str)
{

	$method = "AES-128-CBC";

	$keydata = file_get_contents(storage_path("key/echallan.key"));

	$key = substr($keydata, 0, 16);

	$iv = $key;

	$plaintext = base64_decode($str);

	$data = openssl_decrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);

	return $data;
}


function encrypt_kar($data, $key, $iv)
{
	return base64_encode(mcrypt_encrypt(
		MCRYPT_RIJNDAEL_128,
		$key,
		pkcs5_pad($data),
		MCRYPT_MODE_CBC,
		$iv
	));
}


function pkcs5_pad($text)
{
	$blocksize = mcrypt_enc_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$pad = $blocksize - (strlen($text) % $blocksize);
	return $text . str_repeat(chr($pad), $pad);
}



function encrypt_mp($str){
		
		$method = "AES-128-ECB";
		$key=file_get_contents(storage_path("key/KEY_ECI.key"));
	
	
		//dd($key);		
				
				
		$iv='';
	$ciphertext = openssl_encrypt($str, $method, $key, $options=OPENSSL_RAW_DATA, $iv);
	
	//$encrypted_data = openssl_encrypt($str, "AES-128-CBC", $key, $raw_output = false, $key);
	
	
    // Base 64 encode the encrypted session ID.
    $dataEnc = base64_encode($ciphertext);
    return $dataEnc;
  }
  	function decrypt_mp($str){
		
		$method = "AES-128-ECB";
		$key=file_get_contents(storage_path("key/KEY_ECI.key"));
	
	
		//dd($key);		
				
				
		$iv='';
		
		$dataDec = base64_decode($str);
	$ciphertext  = openssl_decrypt($dataDec, $method, $key, $options=OPENSSL_RAW_DATA, $iv);
	
	//$encrypted_data = openssl_encrypt($str, "AES-128-CBC", $key, $raw_output = false, $key);
	
	
    // Base 64 encode the encrypted session ID.
    return $ciphertext;
  }

  function getlastwithdrawl_stateACwise($st_code,$ac_no)
{
	$get = DB::table('m_schedule')
		->join('m_election_details', [
			['m_election_details.ScheduleID', '=', 'm_schedule.SCHEDULEID'],
		])
		->join('m_state', [
			['m_election_details.ST_CODE', '=', 'm_state.ST_CODE'],
		])

		->select('m_schedule.LDT_WD_CAN','m_schedule.DATE_COUNT','m_schedule.DT_SCR_NOM','m_schedule.LDT_IS_NOM')->where('m_schedule.CURRENTELECTION', 'Y')->where('m_election_details.ST_CODE', $st_code)->where('m_election_details.CONST_NO', $ac_no)
//		->groupby('m_schedule.SCHEDULEID')
		->first();
	return ($get);
}


function countrecordsaffidavit($table, $field, $fieldvalue)
{
	$cnt = DB::table($table)->select('*')->where($field, $fieldvalue)->where('is_deleted', '1')->get()->count();
	return ($cnt);
}

 function decryptData($data)
    {
    $COD = 'AES-128-ECB';
    $key = 'AwdL2cXoGHtULolvWERioSDF';

    return openssl_decrypt($data, $COD, $key);
    //dd($data,);
    }
    // function encryptData($data)
    // {
    // $COD = 'AES-128-ECB';
    // $key = 'AwdL2cXoGHtULolv';
    //  //$input=Crypt::encrypt($input);
    // return openssl_encrypt($data, $COD, $key);
    // }

    function checkpassword($mobile)
      {
	$get = DB::table('user_login')->select('*')->where('mobile', $mobile)->first();
	return $get;
     }