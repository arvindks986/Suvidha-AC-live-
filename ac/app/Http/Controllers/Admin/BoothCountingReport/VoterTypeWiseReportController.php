<?php

namespace App\Http\Controllers\Admin\BoothCountingReport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use App\commonModel;
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\adminmodel\ACCountingModel;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;
use App\models\Admin\StateModel;

date_default_timezone_set('Asia/Kolkata');

class VoterTypeWiseReportController extends Controller {

    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $election_id; 

    public function __construct() {
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware(function (Request $request, $next) {

            if (!\Auth::check()) {
                return redirect('login')->with(Auth::logout());
            }

            $this->CountingModel = new ACCountingModel();
            $user = Auth::user();
            switch ($user->role_id) {
                case '7':
                    $this->middleware('eci');
                    break;
                case '4':
                    $this->middleware('ceo');
                    break;
                case '18':
                    $this->middleware('ro');
                    break;
                default:
                    $this->middleware('eci');
            }

            $this->election_id  = Auth::user()->election_id;

            return $next($request);
        });

        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

    public function reportIndex() {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($uid);
                $list_record = $this->ECIModel->getallelectionphasewise();
                $list_phase = $this->ECIModel->listcurrentelectionphase();
                $list_electionid = $this->ECIModel->getallelectionbyid();
                $list = $this->ECIModel->listelectiontype();

                $list_party = array();
                $ac_list = array();
                $list_state = array();

                //echo "<pre>";print_r($user);die;

                /*$list_state = DB::table('m_state')
                ->select('m_state.ST_CODE', 'm_state.ST_NAME')
                ->join('m_election_details',[
                  ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
                 ])
                ->where('m_election_details.CONST_TYPE','AC')
                ->where('m_election_details.election_status','1')
                ->where('m_election_details.ELECTION_ID',$this->election_id)
                ->orderBy('m_state.ST_CODE', 'ASC')
                ->get();*/

               $list_state = StateModel::get_states();

                if ($user->role_id == '18' || $user->role_id == '20' || $user->role_id == '4') {

                    /* $list_state = DB::table('m_state')
                                ->select('m_state.ST_CODE', 'm_state.ST_NAME')
                                ->join('m_election_details',[
                                  ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
                                 ])
                                ->where('m_election_details.CONST_TYPE','AC')
                                ->where('m_election_details.election_status','1')
                                ->where('m_election_details.ELECTION_ID',$this->election_id)
                                ->where('m_state.ST_CODE', '=', $user->st_code)
                                ->orderBy('m_state.ST_CODE', 'ASC')
                                ->get();  */

                    $list_state = StateModel::get_states(['st_code'=>$user->st_code]);

                    
                    $ac_list = DB::table('m_ac')
                              ->select('m_ac.AC_NO', 'm_ac.AC_NAME')
                              ->leftjoin('m_election_details as election',[
                                      ['election.CONST_NO', '=','m_ac.AC_NO'],
                                      ['election.ST_CODE', '=','m_ac.ST_CODE'],
                                ])
                              ->where('election.CONST_TYPE', '=', 'AC')
                              ->where('election.ELECTION_ID', '=', $this->election_id)
                              ->where('m_ac.ST_CODE', '=', $user->st_code)
                              ->where('m_ac.AC_NO', '=', $user->ac_no)
                              ->orderBy('m_ac.AC_NO', 'ASC')
                              ->get();

                    $list_party = DB::table('counting_master_' . strtolower($user->st_code))->select('candidate_id', 'candidate_name', 'party_abbre')->where('ac_no', '=', $user->ac_no)->groupBy('candidate_name')->orderBy('candidate_name', 'ASC')->get();
                }
                $ele_details = array();
                if ($user->role_id == '7') {

                    /*$list_state = DB::table('m_state')
                    ->select('m_state.ST_CODE', 'm_state.ST_NAME')
                    ->join('m_election_details',[
                      ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
                     ])
                    ->where('m_election_details.CONST_TYPE','AC')
                    ->where('m_election_details.election_status','1')
                    ->where('m_election_details.ELECTION_ID',$this->election_id)
                    ->orderBy('m_state.ST_CODE', 'ASC')
                    ->get();*/

                    $list_state = StateModel::get_states(['st_code' => $user->st_code]);

                } else if ($user->role_id == '4') {

                    /*$list_state = DB::table('m_state')
                    ->select('m_state.ST_CODE', 'm_state.ST_NAME')
                    ->join('m_election_details',[
                      ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
                     ])
                    ->where('m_election_details.CONST_TYPE','AC')
                    ->where('m_election_details.election_status','1')
                    ->where('m_election_details.ELECTION_ID',$this->election_id)
                    ->where('m_state.ST_CODE', '=', $user->st_code)
                    ->orderBy('m_state.ST_CODE', 'ASC')
                    ->get();*/

                    $list_state = StateModel::get_states(['st_code' => $user->st_code]);

                    $ac_list = DB::table('m_ac')
                    ->select('m_ac.AC_NO', 'm_ac.AC_NAME')
                    ->leftjoin('m_election_details as election',[
                          ['election.CONST_NO', '=','m_ac.AC_NO'],
                          ['election.ST_CODE', '=','m_ac.ST_CODE'],
                    ])
                    ->where('m_ac.ST_CODE', '=', $user->st_code)
                    ->where('election.CONST_TYPE','AC')
                    ->where('election.election_status','1')
                    ->where('election.ELECTION_ID',$this->election_id)
                    ->orderBy('m_ac.AC_NO', 'ASC')
                    ->get();

                    $list_party = DB::table('counting_master_' . strtolower($user->st_code))->select('candidate_id', 'candidate_name', 'party_abbre')->groupBy('candidate_name')->orderBy('candidate_name', 'ASC')->get();

                } else if ($user->role_id == '5') {

                   /* $list_state = DB::table('m_state')
                                ->select('m_state.ST_CODE', 'm_state.ST_NAME')
                                 ->join('m_election_details',[
                                  ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
                                 ])
                                ->where('m_state.ST_CODE', '=', $user->st_code)
                                ->where('m_election_details.CONST_TYPE','AC')
                                ->where('m_election_details.election_status','1')
                                ->where('m_election_details.ELECTION_ID',$this->election_id)
                                ->orderBy('m_state.ST_CODE', 'ASC')
                                ->get();*/

                    $list_state = StateModel::get_states(['st_code' => $user->st_code]);

                    $ac_list = DB::table('m_ac')
                                ->select('m_ac.AC_NO', 'm_ac.AC_NAME')
                                ->leftjoin('m_election_details as election',[
                                      ['election.CONST_NO', '=','m_ac.AC_NO'],
                                      ['election.ST_CODE', '=','m_ac.ST_CODE'],
                                ])
                                ->where('m_ac.ST_CODE', '=', $user->st_code)
                                ->where('m_ac.DIST_NO_HDQTR', '=', $user->dist_no)
                                ->where('election.CONST_TYPE','AC')
                                ->where('election.election_status','1')
                                ->where('election.ELECTION_ID',$this->election_id)
                                ->orderBy('m_ac.AC_NO', 'ASC')
                                ->get();

                    $list_party = DB::table('counting_master_' . strtolower($user->st_code))->select('candidate_id', 'candidate_name', 'party_abbre')->groupBy('candidate_name')->orderBy('candidate_name', 'ASC')->get();

                } else if ($user->role_id == '19') {
                    $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

                    $list_state = StateModel::get_states(['st_code' => $user->st_code]);

                    $ac_list = DB::table('m_ac')
                                ->select('AC_NO', 'AC_NAME')
                                ->leftjoin('m_election_details as election',[
                                      ['election.CONST_NO', '=','m_ac.AC_NO'],
                                      ['election.ST_CODE', '=','m_ac.ST_CODE'],
                                ])
                                ->where('m_ac.ST_CODE', '=', $user->st_code)
                                ->where('m_ac.AC_NO', '=', $user->ac_no)
                                ->where('election.CONST_TYPE','AC')
                                ->where('election.election_status','1')
                                ->where('election.ELECTION_ID',$this->election_id)
                                ->orderBy('m_ac.AC_NO', 'ASC')
                                ->get();

                    $list_party = DB::table('counting_master_' . strtolower($user->st_code))->select('candidate_id', 'candidate_name', 'party_abbre')->where('ac_no', '=', $user->ac_no)->groupBy('candidate_name')->orderBy('candidate_name', 'ASC')->get();
                }
                $module = $this->commonModel->getallmodule();
                $votes_record = array();
                return view('admin.countingReport.votetypereport.voter-type-wise-report', ['ele_details' => $ele_details, 'user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_state' => $list_state, 'list_phase' => $list_phase, 'list_electionid' => $list_electionid, 'list' => $list, 'list_state' => $list_state, 'list_party' => $list_party, 'ac_list' => $ac_list, 'votes_record' => $votes_record]);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

// end dashboard function

    function getAcByState($state_code) {
        if (Auth::check()) {
            try {
                $pc_array = array();
                $party_array = array();
                $pc_list = DB::table('m_ac')
                ->select('m_ac.AC_NO', 'm_ac.AC_NAME')
                ->leftjoin('m_election_details as election',[
                      ['election.CONST_NO', '=','m_ac.AC_NO'],
                      ['election.ST_CODE', '=','m_ac.ST_CODE'],
                ])
                ->where('m_ac.ST_CODE', '=', $state_code)
                ->where('election.CONST_TYPE', '=', 'AC')
                ->where('election.ELECTION_ID', '=', $this->election_id)
                ->orderBy('m_ac.AC_NO', 'ASC')
                ->get();

                if(count($pc_list)>0){
                    foreach ($pc_list as $dcode => $dval) {
                        $pc_array['id'][] = $dval->AC_NO;
                        $pc_array['val'][] = $dval->AC_NAME;
                    }
                }
                return json_encode(array("ac_arr" => $pc_array, "party_arr" => $party_array));
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /**
     * Get all party by pc no
     */
    function getPartyByAc($acno, $state_code) {
        if (Auth::check()) {
            try {
                $array = array();
                $st_tbl = 'counting_master_' . strtolower($state_code);
                $acno = explode(",", $acno);
                $list_party = DB::table($st_tbl)
                ->select('candidate_id', 'candidate_name', 'party_abbre')
                ->whereIn('ac_no', $acno)
                ->groupBy('candidate_name')
                ->orderBy('candidate_name', 'ASC')
                ->get();
                if(count($list_party)>0){
                    foreach ($list_party as $dcode => $dval) {
                        $array['id'][] = $dval->candidate_id;
                        $array['val'][] = $dval->candidate_name . ' ( ' . $dval->party_abbre . ' )';
                    }
                }
                
                return json_encode($array);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getSearchReport(Request $request) {

        $stateid = $request->stateid;
        $acno = $request->acno;
        $party_id = $request->party;
        if (Auth::check()) {
            try {
                $user = Auth::user();
                $user_data = $this->commonModel->getunewserbyuserid($user->id);

                //$list_state = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->orderBy('ST_CODE', 'ASC')->get();
                $list_state = StateModel::get_states();
                $state_name = '';

                if ($stateid <> '' && count($acno) > 0 && count($party_id) > 0) {
                    $st_tbl = 'counting_master_' . strtolower($stateid);
                    $list_record = DB::table($st_tbl)->select('*')->whereIn('ac_no', $acno)->whereIn('candidate_id', $party_id)->orderBy('ac_no', 'ASC')->get();
                    $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', '=', $stateid)->first();
                    if($state_name){
                        $state_name= $state_name->ST_NAME;
                    }

                    $totevm = 0;
                    $str = '';
                    $template = '';
                    $template_end = "</tbody></table>";
                    $i = 1;
                    $evm_vote = 0;
                    if (count($list_record) > 0) {
                        foreach ($list_record as $dcode => $dval) {
                            $ac_name = DB::table('m_ac')->select('AC_NO', 'AC_NAME')->where('ST_CODE', $stateid)->where('AC_NO', '=', $dval->ac_no)->first();
                            $evm_vote = $dval->total_vote - $dval->postalballot_vote;
                            $str .= '<tr>
                                    <td>' . $i . '</td>
                                    <td>' . $state_name . '</td>
                                    <td>' . $ac_name->AC_NO.'-'.$ac_name->AC_NAME. '</td>
                                    <td>' . $dval->candidate_name . '</td>
                                    <td>' . $dval->party_name . ' (' . $dval->party_abbre . ')' . '</td>
                                    <td>' . $evm_vote . '</td>
                                    <td>' . $dval->postalballot_vote . '</td>
                                    <td>' . $dval->total_vote . '</td>
                                </tr>';
                            $i++;
                        }
                    } else {
                        $str .= '<tr colspan="8"><td colspan="8" style="text-align:center;">No record found</td></tr>';
                    }
                    $template .= "<table id='example' class='table table-bordered' style='width:100%'>"
                            . "<thead><th>SL No</th><th>State Name</th><th>AC Name</th><th>Candidate Name</th><th>Party Name</th><th>EVM Vote</th><th>Postal Vote</th><th>Total Vote</th></thead><tbody>";
                    return $template . $str . $template_end . '|||' . count($list_record);
                }
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /**
     * Get report pdf
     */
    function getReportPdf(Request $request) {
        $input = $request->all();
        $stateid = $request->statevalue;
        $acno = explode(",", $request->acvalue);
        $party_id = explode(",", $request->partyvalue);
        $user = Auth::user();
        $state_name  = '';
        if ($stateid != '' && $acno != '' && $party_id != '') {
            $st_tbl = 'counting_master_' . strtolower($stateid);
            $list_record = DB::table($st_tbl)->select('*')->whereIn('ac_no', $acno)->whereIn('candidate_id', $party_id)->orderBy('ac_no', 'ASC')->get();
            $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', '=', $stateid)->first();
            if($state_name){
                $state_name= $state_name->ST_NAME;
            }

            $evm_vote = 0;
            if (count($list_record) > 0) {
                foreach ($list_record as $dcode => $dval) {
                    $evm_vote = $dval->total_vote - $dval->postalballot_vote;
                    $ac_name = DB::table('m_ac')->select('AC_NO', 'AC_NAME')->where('ST_CODE', $stateid)->where('AC_NO', '=', $dval->ac_no)->first();
                    $evm_vote = $dval->total_vote - $dval->postalballot_vote;
                    $array[] = array('state_name' => $state_name, 'ac_name' => $ac_name->AC_NO.'-'.$ac_name->AC_NAME, 'candidate_name' => $dval->candidate_name, 'party_name' => $dval->party_name . ' (' . $dval->party_abbre . ')', 'evm_vote' => $evm_vote, 'postal_vote' => $dval->postalballot_vote, 'total_vote' => $dval->total_vote);
                }
                ini_set("pcre.backtrack_limit", "5000000");
                $date = date('Y-m-d');
                $pdf = PDF::loadView('admin.countingReport.votetypereport.voter-type-wise-report-pdf', ['array' => $array, 'user_data' => $user]);
                return $pdf->download($date.'-candidate-wise-report.' . 'pdf');
            }
        }
    }

    /**
     * Get report excel
     */
    function getReportExcel(Request $request) {
        $input = $request->all();
        $stateid = $request->statevalue;
        $acno = explode(",", $request->acvalue);
        $party_id = explode(",", $request->partyvalue);
        $user = Auth::user();
        $state_name = '';
        if (Auth::check()) {
            try {
                if ($stateid != '' && $acno != '' && $party_id != '') {
                    $st_tbl = 'counting_master_' . strtolower($stateid);
                    $list_record = DB::table($st_tbl)->select('*')->whereIn('ac_no', $acno)->whereIn('candidate_id', $party_id)->orderBy('ac_no', 'ASC')->get();
                    $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', '=', $stateid)->first();
                    if($state_name){
                        $state_name= $state_name->ST_NAME;
                    }
                    $i = 1;
                    $evm_vote = 0;
                    if (count($list_record) > 0) {
                        foreach ($list_record as $dcode => $dval) {
                            $evm_vote = $dval->total_vote - $dval->postalballot_vote;
                            $ac_name = DB::table('m_ac')->select('AC_NO', 'AC_NAME')->where('ST_CODE', $stateid)->where('AC_NO', '=', $dval->ac_no)->first();
                            $evm_vote = $dval->total_vote - $dval->postalballot_vote;
                            $mArray[] = array('SL.No' => $i, 'State Name' => $state_name, 'AC Name' => $ac_name->AC_NO.'-'.$ac_name->AC_NAME, 'Candidate Name' => $dval->candidate_name,'Party Name'=>$dval->party_name . ' (' . $dval->party_abbre . ')', 'EVM Vote' => "$evm_vote", 'Postal Vote' => "$dval->postalballot_vote", 'Total Vote' => "$dval->total_vote");
                            $i++;
                        }
                        $data = json_decode(json_encode($mArray), true);
                        $date = date('Y-m-d');
                        return Excel::create($date.'-candidate-wise-report', function($excel) use ($data) {
                                    $excel->sheet('mySheet', function($sheet) use ($data) {
                                        $sheet->fromArray($data);
                                    });
                                })->download('xls');
                    }
                }
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /*     * ***********************************  Form 21 Generate  ********************************************** */

    public function getForm21() {
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($uid);
                $list_record = $this->ECIModel->getallelectionphasewise();
                $list_phase = $this->ECIModel->listcurrentelectionphase();
                $list_electionid = $this->ECIModel->getallelectionbyid();
                $list = $this->ECIModel->listelectiontype();

                $ac_list = array();

                $acno = $user->ac_no;
                $stateid = $user->st_code;
                $cnt_list = array();
                $state_name = '';
                $ac_name = '';
                $winning_candidate = array();
                $totelctroll = 0;
                $totelservicectroll = 0;
                $validpolled = '';
                $round_details = array();
                $ele_details = array();

                $module = array();
                if($d){
                    $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, 'AC');
                }
                
                $table = 'counting_master_' . strtolower($stateid);
                $cnt_list = DB::table($table)
                        ->select('*')
                        ->where('ac_no', '=', $acno)
                        ->where('candidate_name', '<>', 'NOTA')
                        ->orderBy('id', 'ASC')
                        ->get();
                $array = array();
                $nota_count = 0;
                $nota_count = DB::table($table)
                            ->select('total_vote')
                            ->where('ac_no', '=', $acno)
                            ->where('candidate_name', '=', 'NOTA')
                            ->first();
       
                if(count($nota_count) >0){
                   if($nota_count){
                        $total_nota = $nota_count->total_vote;
                   }
                }
                

                $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $stateid)->first();
                if($state_name){
                    $state_name = $state_name->ST_NAME;
                }
                
                $ac_name = DB::table('m_ac')->select('AC_NO', 'AC_NAME','AC_TYPE')->where('ST_CODE', $stateid)->where('AC_NO', '=', $acno)->first();
                if($ac_name){
					if($ac_name->AC_TYPE<>'GEN'){
						$ac_name = $ac_name->AC_NO.'-'.$ac_name->AC_NAME.' ('.$ac_name->AC_TYPE.')' ;
					}else{
						$ac_name = $ac_name->AC_NO.'-'.$ac_name->AC_NAME;
					}
                    
                }

                $totelctroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_total) AS totelectors"))->where('ac_no', '=', $acno)->where('st_code', '=', $stateid)->where('year','=','2019')->first();
                if($totelctroll){
                    $totelctroll = $totelctroll->totelectors;
                }
                
                $totelservicectroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_service) AS totserviceelectors"))->where('ac_no', '=', $acno)->where('st_code', '=', $stateid)->where('year', '=', '2019')->first();
                if($totelservicectroll){
                    $totelservicectroll = $totelservicectroll->totserviceelectors;
                }
                
                $totpolled_votes = 0;

                $can_district='';$cand_state='';
                
                if (count($cnt_list) > 0) {
                    foreach ($cnt_list as $k => $dval) {
                        $array[] = array('candidate_name' => $dval->candidate_name, 'party_name' => $dval->party_name, 'total_vote' => $dval->total_vote, 'rejectedvote' => 0, 'candidate_id' => $dval->candidate_id);
                        $totpolled_votes = $totpolled_votes + $dval->total_vote;
                    }
                    $date = date('Y-m-d');
                    $validpolled = $totelctroll - $totpolled_votes;
                    $filter='';  
                    if($ele_details){
                        $filter     = [
                        'st_code'   => $stateid,
                        'ac_no'     => $acno,
                            'election_id'   => $ele_details->ELECTION_ID,

                    ];
					
                    $round_details=$this->CountingModel->roundsechudle($filter);
                    }
                    

                    $winning_candidate = DB::table('winning_leading_candidate as wincan')
                    ->leftJoin('candidate_personal_detail as can_perd', 'wincan.candidate_id', '=', 'can_perd.candidate_id')
                    ->select('wincan.lead_cand_name','wincan.status', 'wincan.lead_cand_party','can_perd.candidate_residence_districtno','can_perd.candidate_residence_stcode', 'can_perd.candidate_id', 'can_perd.candidate_residence_address')
                    ->where('wincan.st_code', '=', $stateid)->where('wincan.ac_no', '=', $acno)
                    ->first();

                    if($winning_candidate){
                        if($winning_candidate->status=='1'){
                                $can_district = DB::table('m_district')->select('DIST_NAME')->where('ST_CODE', '=', $winning_candidate->candidate_residence_stcode)->where('DIST_NO', '=', $winning_candidate->candidate_residence_districtno)->first();
                                if($can_district){
                                    $can_district = $can_district->DIST_NAME;
                                }
                                $cand_state = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $winning_candidate->candidate_residence_stcode)->first();
                                if($cand_state){
                                    $cand_state = $cand_state->ST_NAME;
                                }
                        }
                    }
                }

            $module = $this->commonModel->getallmodule();
            
            $votes_record = array();
                return view('admin.countingReport.votetypereport.form21-report', ['ele_details' => $ele_details, 'user_data' => $d, 'module' => $module, 'list_record' => $list_record,'list_phase' => $list_phase, 'list_electionid' => $list_electionid, 'list' => $list, 'votes_record' => $votes_record, 'tot_electrol' => $totelctroll, 'total_validpol' => $totpolled_votes, 'state' => $state_name,'candstate' => $cand_state,'dist'=>$can_district, 'acname' => $ac_name, 'win_can' => $winning_candidate, 'array' => $array,'round_details'=>$round_details,'total_nota'=>$total_nota,'service_vote'=>$totelservicectroll]);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getForm21Pdf() {
        if (Auth::check()) {
            
            try {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($uid);
                $list_record = $this->ECIModel->getallelectionphasewise();
                $list_phase = $this->ECIModel->listcurrentelectionphase();
                $list_electionid = $this->ECIModel->getallelectionbyid();
                $list = $this->ECIModel->listelectiontype();
                
                $acno = $user->ac_no;
                $stateid = $user->st_code;
                $can_district='';
                $cand_state='';
   
                $cnt_list = array();
                $state_name = '';
                $ac_name = '';
                $winning_candidate = array();
                $totelctroll = 0;
                $totelservicectroll = 0;
                $validpolled = 0;
                $round_details = array();
                $ele_details = array();

                $module = array();
                
                if ($stateid != '' && $acno != '') {
                    $table = 'counting_master_' . strtolower($stateid);
                    $cnt_list = DB::table($table)
                            ->select('*')
                            ->where('ac_no', '=', $acno)
                            ->where('candidate_name', '<>', 'NOTA')
                            ->orderBy('id', 'ASC')
                            ->get();
                    
                    $array = array();
                    $state_name = '';
                    $ac_name = '';
                    
                    $nota_count = 0;
                    $nota_count = DB::table($table)
                                ->select('total_vote')
                                ->where('ac_no', '=', $acno)
                                ->where('candidate_name', '=', 'NOTA')
                                ->first();

                    if($nota_count){
                       if($nota_count){
                            $total_nota = $nota_count->total_vote;
                       }
                    }

                    
                    if($d){
                        $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, 'AC');
                    }
                    
                    $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $stateid)->first();
                    if($state_name){
                        $state_name = $state_name->ST_NAME;
                    }
                    $ac_name = DB::table('m_ac')->select('AC_NO', 'AC_NAME','AC_TYPE')->where('ST_CODE', $stateid)->where('AC_NO', '=', $acno)->first();
                    if($ac_name){
                        if($ac_name->AC_TYPE<>'GEN'){
						$ac_name = $ac_name->AC_NO.'-'.$ac_name->AC_NAME.' ('.$ac_name->AC_TYPE.')' ;
						}else{
							$ac_name = $ac_name->AC_NO.'-'.$ac_name->AC_NAME;
						}
                    }
                    
                    $totelctroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_total) AS totelectors"))->where('ac_no', '=', $acno)->where('st_code', '=', $stateid)->where('year','=','2019')->first();
                    if($totelctroll){
                        $totelctroll = $totelctroll->totelectors;
                    }
                    
                    $totelservicectroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_service) AS totserviceelectors"))->where('ac_no', '=', $acno)->where('st_code', '=', $stateid)->where('year', '=', '2019')->first();
                    if($totelservicectroll){
                        $totelservicectroll = $totelservicectroll->totserviceelectors;
                    }

                    $totpolled_votes = 0;
                    if (count($cnt_list) > 0) {
                        foreach ($cnt_list as $k => $dval) {
                                $array[] = array('candidate_name' => $dval->candidate_name, 'party_name' => $dval->party_name , 'total_vote' => $dval->total_vote, 'rejectedvote' => 0, 'candidate_id' => $dval->candidate_id);
                            
                            $totpolled_votes = $totpolled_votes + $dval->total_vote;
                        }
                        $filter='';  
                        if($ele_details){
                            $filter     = [
                            'st_code'   => $stateid,
                            'ac_no'     => $acno,
                                'election_id'   => $ele_details->ELECTION_ID,

                        ];
                        $round_details=$this->CountingModel->roundsechudle($filter);
                        }
                        $date = date('Y-m-d');

                        $winning_candidate = DB::table('winning_leading_candidate as wincan')
                        ->leftJoin('candidate_personal_detail as can_perd', 'wincan.candidate_id', '=', 'can_perd.candidate_id')
                        ->select('wincan.lead_cand_name','wincan.status', 'wincan.lead_cand_party', 'can_perd.candidate_id','can_perd.candidate_residence_districtno','can_perd.candidate_residence_stcode', 'can_perd.candidate_residence_address')
                        ->where('wincan.st_code', '=', $stateid)->where('wincan.ac_no', '=', $acno)
                        ->first();

                        if($winning_candidate){
                            //if($winning_candidate->status=='1'){
                                $can_district = DB::table('m_district')->select('DIST_NAME')->where('ST_CODE', '=', $winning_candidate->candidate_residence_stcode)->where('DIST_NO', '=', $winning_candidate->candidate_residence_districtno)->first();
                                if($can_district){
                                    $can_district = $can_district->DIST_NAME;
                                }
                                $cand_state = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $winning_candidate->candidate_residence_stcode)->first();
                                if($cand_state){
                                    $cand_state = $cand_state->ST_NAME;
                                }
                            //}
                        }
                        
                        $validpolled = $totelctroll - $totpolled_votes;
                        $pdf = PDF::loadView('admin.countingReport.votetypereport.form21-report-pdf', ['array' => $array, 'user_data' => $user, 'tot_electrol' => $totelctroll, 'total_validpol' => $totpolled_votes, 'state' => $state_name,'candstate' => $cand_state,'dist'=>$can_district, 'acname' => $ac_name, 'win_can' => $winning_candidate,'round_details'=>$round_details,'total_nota'=>$total_nota,'service_vote'=>$totelservicectroll]);
                        return $pdf->download($date . '-form-21' . $ac_name . '.' . 'pdf');
                    }
                }
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

}

// end class
