<?php

namespace App\Http\Controllers\Expenditure;

ini_set('memory_limit', '-1');

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
use App\models\Expenditure\EciExpenditureModel;
use App\models\Expenditure\ExpenditureModel;
use Maatwebsite\Excel\Excel;
//INCLUDING CLASSES
use App\Classes\xssClean;
//INCLUDING CLASSES
use DateTime;
use App\models\Expenditure\DeoexpenditureModel;

class EciExpenditureController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $accessstate;
    public  $expdb;
    public function __construct() {
          ##############Connect with Expenditure DataBase#############
         $this->middleware(function ($request, $next){
           $DB_DATABASE = strtolower(Session::get('DB_DATABASE'));
          $m_election_history = DB::connection("mysql_database_history")->table("m_election_history")->where("db_name", $DB_DATABASE)->first();

          $this->expdb=$m_election_history->exp_db_name;
        //  dd($m_election_history);
		   //Session::put('ELECTION_ID',$m_election_history->election_id);
		   //Session::put('ELECTION_TYPE',$m_election_history->elect_type);
		   ################Add by niraj for exp_alter DB ###########
	    Session::put('DB_ELECTION_ID',$m_election_history->election_id);
        Session::put('DB_MONTH',$m_election_history->month);
        Session::put('DB_YEAR',$m_election_history->year);
        Session::put('DB_CONS_TYPE',$m_election_history->const_type);
        Session::put('DB_ELE_TYPE',$m_election_history->elect_type);
		Session::put('ELE_TYPE_DESC',str_replace('-',' ',$m_election_history->description));
		################end#####################################

            
            
             config(['database.connections.mysql.host' => '10.247.137.43']);
            config(['database.connections.mysql.database' => $this->expdb]);
            config(['database.connections.mysql.username' => 'suvidhaapp']);
            config(['database.connections.mysql.password' => 'P7$b&n#367BYaRt91']);
            config(['database.connections.mysql.options' =>[\PDO::ATTR_EMULATE_PREPARES =>true]]);
           // DB::reconnect('mysql');
            DB::purge('mysql');
            DB::connection('mysql');
           return $next($request); 
       });
        ############################################################
        $this->middleware(['auth:admin', 'auth']);
        //$this->middleware('eci');
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
                return redirect('login')->with(Auth::logout());
            }

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
                case '27':
                    $this->middleware('eci_index');
                    break;
                case '28':
                    $this->middleware('eci_expenditure');
                    break;

                default:
                    $this->middleware('eci');
            }
            return $next($request);
        });
        $this->middleware('adminsession');

        $this->commonModel = new commonModel();
        $this->eciexpenditureModel = new EciExpenditureModel();
        $this->expenditureModel = new ExpenditureModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

    /**
     * Calculate percetage between the numbers
     */
    function getaclist(request $request) {
        // dd($request->all());
        if (Auth::check()) {
            $user = Auth::user();

            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $stcode = $request->input('state');

            // $all_pc = $this->commonModel->getpcbystate($stcode);
            $all_ac = DB::table('m_ac')
                            ->where('ST_CODE', $stcode)->orderBy('AC_NO', 'asc')->get();
        }
        return $all_ac;
    }

    function get_percentage($total, $number) {
        if ($total > 0) {
            return round($number / ($total / 100), 2);
        } else {
            return 0;
        }
    }

//end number

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 17-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return dashboard By ECI fuction     
     */
    public function dashboard(Request $request) {
        //dd(DB::connection()->getDatabaseName());
        //dd($request->all());
        //AC ECI dashboard TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $st_code = $request->input('state');
                $zonestate = $this->eciexpenditureModel->getzonestate($username);
				
				$electionYear = getallElectionYear();
				
                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    $st_code = array_values($permitstate)[0];
                } else {
                    $st_code = 0;
                }

                #########################Code For State Wise Access#####################
                $cons_no = $request->input('ac');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                if (!empty($st_code) && empty($cons_no)) {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->count();
                    $totalElectedCandidate = DB::table('winning_leading_candidate')
                            ->where('winning_leading_candidate.st_code', '=', $st_code)
                            ->count();
                } else if (!empty($st_code) && $cons_no != '') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->count();
                    $totalElectedCandidate = DB::table('winning_leading_candidate')
                            ->where('winning_leading_candidate.st_code', '=', $st_code)
                            ->where('winning_leading_candidate.ac_no', '=', $cons_no)
                            ->count();
                } else {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            //->where('candidate_nomination_detail.st_code','=',$st_code)
                            //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->count();
                    $totalElectedCandidate = DB::table('winning_leading_candidate')
                            ->count();
                }

                /////////////////////////////-------start notification -------------------////////          
                //shishir
                $eciscrutinycandidatecount = DB::table('expenditure_notification')
                        ->leftjoin('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_notification.candidate_id')
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->Where('expenditure_notification.eci_read_status', '=', '0')
                        ->Where('expenditure_reports.final_by_ceo', '=', '1')
                        ->count();

                $request->session()->put('ecicountscrutiny', $eciscrutinycandidatecount);
                //shishir
/////////////////-----------end notification -----------------//////////////
                //Get Data entry Start Count 
                $startdatacount = $this->eciexpenditureModel->gettotaldataentryStart('AC', $st_code, $cons_no);
                // dd($startdatacount);
                //Get Data entry Start Count %
                $Percent_startdataentry = $this->get_percentage($totalContestedCandidate, $startdatacount);
                //dd($Percent_startdataentry);
                //Get Data entry finalize Count 
                $finaldatacount = $this->eciexpenditureModel->gettotaldataentryFinal('AC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);

                //Get Data entry finalize Count 
                $logedaccount = $this->eciexpenditureModel->gettotallogedAccount('AC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_logedaccount = $this->get_percentage($totalContestedCandidate, $logedaccount);

                //Get Data entry finalize Count 
                $notintimeaccount = $this->eciexpenditureModel->gettotalNotinTime('AC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_notintimeaccount = $this->get_percentage($totalContestedCandidate, $notintimeaccount);


                //Get Defects in format Count 
                $formateDefectscount = $this->eciexpenditureModel->gettotalDefectformats('AC', $st_code, $cons_no);
                //Get Defects in format Count %
                $Percent_formateDefectscount = $this->get_percentage($totalContestedCandidate, $formateDefectscount);

                //Get Defects in format Count 
                $expenseunderstated = $this->eciexpenditureModel->gettotalexpenseUnderStated('AC', $st_code, $cons_no);
                //Get Defects in format Count %
                $Percent_expenseunderstated = $this->get_percentage($totalContestedCandidate, $expenseunderstated);

                //Get total fund from party
                $partyFund = $this->eciexpenditureModel->gettotalPartyfund('AC', $st_code, $cons_no);
                $otherSourcesFund = $this->eciexpenditureModel->gettotalOtherSourcesfund('AC', $st_code, $cons_no);

                $totalFund = ($partyFund->total_partyfund + $otherSourcesFund->total_otherSourcesfund);
                //Get party fund %
                $Percent_partyFund = $this->get_percentage($totalFund, $partyFund->total_partyfund);
                //Get OtherSources fund %
                $Percent_OthersourcesFund = $this->get_percentage($totalFund, $otherSourcesFund->total_otherSourcesfund);
                // return /non return start here
                $totalElectedCandidate = !empty($totalElectedCandidate) ? $totalElectedCandidate : 0;
                $returncount = $this->expenditureModel->gettotalreturn('AC', $st_code, $cons_no, 'Returned');

                $totalNominationCandiate = $totalContestedCandidate - $totalElectedCandidate;

                $nonreturncount = $this->expenditureModel->gettotalreturn('AC', $st_code, $cons_no, 'Non-Returned');

                $returncount = !empty($returncount) ? count($returncount) : 0;
                $nonreturncount = !empty($nonreturncount) ? count($nonreturncount) : 0;

                //Getfinal by eci Count %
                $Percent_returncount = $this->get_percentage($totalElectedCandidate, $returncount);
                $Percent_nonreturncount = $this->get_percentage($totalNominationCandiate, $nonreturncount);
                // end here return /non return
                return view('admin.ac.eci.Expenditure.dashboard', ['user_data' => $d,
                    'startdatacount' => $startdatacount, 'Percent_startdataentry' => $Percent_startdataentry,
                    'finaldatacount' => $finaldatacount, 'Percent_finaldatacount' => $Percent_finaldatacount,
                    'formateDefectscount' => $formateDefectscount,
                    'Percent_formateDefectscount' => $Percent_formateDefectscount,
                    'expenseunderstated' => $expenseunderstated,
                    'Percent_expenseunderstated' => $Percent_expenseunderstated,
                    'Percent_partyFund' => $Percent_partyFund,
                    'Percent_OthersourcesFund' => $Percent_OthersourcesFund, 'edetails' => $ele_details,
                    'logedaccount' => $logedaccount, 'Percent_logedaccount' => $Percent_logedaccount,
                    'notintimeaccount' => $notintimeaccount,
                    'Percent_notintimeaccount' => $Percent_notintimeaccount,
                    'returncount' => $returncount,
                    'Percent_returncount' => $Percent_returncount,
                    'nonreturncount' => $nonreturncount,
                    'Percent_nonreturncount' => $Percent_nonreturncount,
                    'totalContestedCandidate' => $totalContestedCandidate,
                    'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist,'electionYear' => $electionYear]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI dashboard TRY CATCH ENDS HERE    
    }

// end dashboard function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 17-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentryStart By ECI fuction     
     */
    public function candidateListBydataentryStart(Request $request, $state, $ac) {
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                //echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.dataentrystart-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//AC ECI candidateListBydataentryStart TRY CATCH ENDS HERE   
    }

// end dataentry start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 17-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByfinalizeData By ECI fuction     
     */
    public function candidateListByfinalizeData(Request $request, $state, $ac) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;

                if ($st_code == '0' && $cons_no == '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.finalize-report', ['user_data' => $d, 'finalCandList' => $finalCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE   
    }

// end candidateListByfinalizeData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBylogedaccount By ECI fuction     
     */
    public function candidateListBylogedaccount(Request $request, $state, $ac) {
        //AC ECI candidateListBylogedaccount TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;

                if ($st_code == '0' && $cons_no == '0') {
                    $logedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.candidate_lodged_acct', '=', 'Yes')
                            // ->where('expenditure_reports.finalized_status','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $logedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.candidate_lodged_acct', '=', 'Yes')
                            // ->where('expenditure_reports.finalized_status','=','1') 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $logedAccount = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.candidate_lodged_acct', '=', 'Yes')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.logedaccount-report', ['user_data' => $d, 'logedAccount' => $logedAccount, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBylogedaccount TRY CATCH ENDS HERE   
    }

// end candidateListBylogedaccount start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 17-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBynotintime By ECI fuction     
     */
    public function candidateListBynotintime(Request $request, $state, $ac) {
        //PC ECI candidateListBynotintime TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;

                if ($st_code == '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.account_lodged_time', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //  ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.account_lodged_time', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.account_lodged_time', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.notintime-report', ['user_data' => $d, 'notinTime' => $notinTime, 'edetails' => $ele_details]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListBynotintime TRY CATCH ENDS HERE   
    }

// end candidateListBynotintime start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 17-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentryStart By ECI fuction     
     */
    public function candidateListByformatedefects(Request $request, $state, $ac) {
        //AC ECI candidateListByformatedefects TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;
                if ($st_code == '0' && $cons_no == '0') {
                    $formateDefects = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.rp_act', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $formateDefects = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.rp_act', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $formateDefects = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.rp_act', '=', 'No')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.formatedefects-report', ['user_data' => $d, 'formateDefects' => $formateDefects, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByformatedefects TRY CATCH ENDS HERE   
    }

// end candidateListByformatedefects start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 17-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByronotagree By ECI fuction     
     */
    public function candidateListByronotagree(Request $request, $state, $ac) {
        //PC ECI candidateListByronotagree TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;

                $DataentryStartCandList = DB::table('expenditure_reports')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        // ->where('expenditure_reports.ST_CODE','=',$st_code)
                        // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.ronotagree-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByronotagree TRY CATCH ENDS HERE   
    }

// end candidateListByronotagree start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByunderstatedexpense By ECI fuction     
     */
    public function candidateListByunderstatedexpense(Request $request, $state, $ac) {
        //PC ECI candidateListByunderstatedexpense TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;
                if ($st_code == '0' && $cons_no == '0') {
                    $expenseunderstated = DB::table('expenditure_understates')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understates.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understates.candidate_id')
                            ->where('expenditure_understates.understated_type_id', '=', '1')
                            ->where('expenditure_understates.status', '=', 'no')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $expenseunderstated = DB::table('expenditure_understates')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understates.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understates.candidate_id')
                            ->where('expenditure_understates.ST_CODE', '=', $st_code)
                            ->where('expenditure_understates.understated_type_id', '=', '1')
                            ->where('expenditure_understates.status', '=', 'no')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //    ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $expenseunderstated = DB::table('expenditure_understates')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understates.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understates.candidate_id')
                            ->where('expenditure_understates.ST_CODE', '=', $st_code)
                            ->where('expenditure_understates.constituency_no', '=', $cons_no)
                            ->where('expenditure_understates.understated_type_id', '=', '1')
                            ->where('expenditure_understates.status', '=', 'no')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.expenseunderstated-report', ['user_data' => $d, 'expenseunderstated' => $expenseunderstated, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByunderstatedexpense TRY CATCH ENDS HERE   
    }

// end candidateListByunderstatedexpense start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 10-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBydataentrydefects By ECI fuction     
     */
    public function candidateListBydataentrydefects(Request $request, $state, $ac) {
        //PC ECI candidateListBydataentrydefects TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;
                if ($st_code == '0' && $cons_no == '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $DataentryStartCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.dataentrydefect-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListBydataentrydefects TRY CATCH ENDS HERE   
    }

// end candidateListBydataentrydefects start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 17-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListBypartyfund By ECI fuction     
     */
    public function candidateListBypartyfund(Request $request, $state, $ac) {
        //PC ROPC candidateListBypartyfund TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;
                if ($st_code == '0' && $cons_no == '0') {
							 $partyfund = DB::table('candidate_nomination_detail')
					        ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA') 
                          ->select('candidate_nomination_detail.candidate_id','candidate_nomination_detail.ac_no as constituency_no','candidate_nomination_detail.st_code as ST_CODE','candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.political_fund_cash','expenditure_fund_parties.political_fund_checque','expenditure_fund_parties.political_fund_kind', 'm_party.PARTYNAME')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
					 $partyfund = DB::table('candidate_nomination_detail')
					        ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA') 
                          ->select('candidate_nomination_detail.candidate_id','candidate_nomination_detail.ac_no as constituency_no','candidate_nomination_detail.st_code as ST_CODE','candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.political_fund_cash','expenditure_fund_parties.political_fund_checque','expenditure_fund_parties.political_fund_kind', 'm_party.PARTYNAME')
                            ->get();
                  
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $partyfund = DB::table('candidate_nomination_detail')
					        ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
							->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA') 
                            ->select('candidate_nomination_detail.candidate_id','candidate_nomination_detail.ac_no as constituency_no','candidate_nomination_detail.st_code as ST_CODE','candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_parties.political_fund_cash','expenditure_fund_parties.political_fund_checque','expenditure_fund_parties.political_fund_kind', 'm_party.PARTYNAME')
                            ->get();
                }
                // dd($partyfund);
                return view('admin.ac.eci.Expenditure.partyfund-report', ['user_data' => $d, 'partyfund' => $partyfund, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//AC ECI candidateListBypartyfund TRY CATCH ENDS HERE   
    }

// end candidateListBypartyfund start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 17-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByothersfund By ECI fuction     
     */
    public function candidateListByothersfund(Request $request, $state, $ac) { //dd($request->all());
        //AC ECI candidateListByothersfund TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                 
                $query = DB::table('expenditure_fund_source')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id')
                
                            ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_hname', 'candidate_personal_detail.candidate_father_name', 'expenditure_fund_source.*', 'm_party.*')
                
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');
                            
                
                if ($st_code != '0' && $cons_no == '0') {
                
                           
                            $query->where('expenditure_fund_source.ST_CODE', '=', $st_code);
                
                } elseif ($st_code != '0' && $cons_no != '0') {
                            $query->where('expenditure_fund_source.ST_CODE', '=', $st_code);
                            $query->where('expenditure_fund_source.constituency_no', '=', $cons_no);
                
                }
                 $query->select('candidate_personal_detail.cand_name',
                         'candidate_personal_detail.candidate_id',
                         'candidate_personal_detail.candidate_father_name',
                         DB::raw('IFNULL(sum(expenditure_fund_source.other_source_amount),0 )as other_source_amount'),
                         'm_party.CCODE','m_party.PARTYNAME','expenditure_fund_source.ST_CODE',
                         'expenditure_fund_source.constituency_no');
                $query->groupBy('expenditure_fund_source.candidate_id');
                $otherfund=$query->get();
                return view('admin.ac.eci.Expenditure.otherfund-report', ['user_data' => $d, 'otherfund' => $otherfund, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//AC ECI candidateListByothersfund TRY CATCH ENDS HERE   
    }

// end candidateListByothersfund start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 17-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByexeedceiling By ECI fuction     
     */
    public function candidateListByexeedceiling(Request $request, $state, $ac) {
        //PC ECI candidateListByexeedceiling TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                $DataentryStartCandList = DB::table('expenditure_reports')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        //->where('expenditure_reports.ST_CODE','=',$st_code)
                        //->where('expenditure_reports.constituency_no','=',$cons_no) 
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.exceedceiling-report', ['user_data' => $d, 'DataentryStartCandList' => $DataentryStartCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//AC ECI candidateListByexeedceiling TRY CATCH ENDS HERE   
    }

// end candidateListByexeedceiling start function
    #########################Start status dashboard by Niraj 16-05-2019###################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 20-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return statusdashboard By ECI fuction     
     */
    public function statusdashboard(Request $request) {
        //dd($request->all());
        //PC ECI statusdashboard TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $st_code = $request->input('state');
                // $permitstate=$this->accessstate;
                $zonestate = $this->eciexpenditureModel->getzonestate($username);
                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    $st_code = array_values($permitstate)[0];
                } else {
                    $st_code = 0;
                }

                #########################Code For State Wise Access#####################
                $cons_no = $request->input('ac');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
				$query=DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                           // ->where('candidate_nomination_detail.st_code', '=', $st_code)
                           // ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');
                if (!empty($st_code) && $cons_no == '') {
					$query->where('candidate_nomination_detail.st_code', '=', $st_code);
                    $totalContestedCandidate = $query->count();
                } else if (!empty($st_code) && $cons_no != '') {
					$query->where('candidate_nomination_detail.st_code', '=', $st_code);
					$query->where('candidate_nomination_detail.ac_no', '=', $cons_no);
                    $totalContestedCandidate = $query->count();
                } else {
                    $totalContestedCandidate = $query->count();
                }



                //Get Data entry Start Count 
                $startdatacount = $this->eciexpenditureModel->gettotaldataentryStart('AC', $st_code, $cons_no);
              
                //Get Data entry Start Count %
                $Percent_startdatacount = $this->get_percentage($totalContestedCandidate, $startdatacount);

                // Get Pending Data Count 
                $pendingdatacount = $totalContestedCandidate - $startdatacount;
                //Get Data entry Start Count %
                $Percent_pendingdatacount = $this->get_percentage($totalContestedCandidate, $pendingdatacount);

                //get partially pending data count
                $partiallypending = $this->eciexpenditureModel->gettotalfinalbyDEO('AC', $st_code, $cons_no);

                if($partiallypending >=0){
					$partiallypendingcount=$totalContestedCandidate-$partiallypending;
				}
                
                //Get Data entry Start Count %
                $Percent_partiallypendingcount = $this->get_percentage($totalContestedCandidate, $partiallypendingcount);

                //Get Data entry finalize Count 
                $finaldatacount = $this->eciexpenditureModel->gettotaldataentryFinal('AC', $st_code, $cons_no);
				
                //Get Data entry finalize Count %
                $Percent_finaldatacount = $this->get_percentage($totalContestedCandidate, $finaldatacount);
                $Percent_partiallypendingcount = !empty($Percent_partiallypendingcount) ? $Percent_partiallypendingcount : 0;
                //get partially pending data count
               

                 //get defaulter data
                $defaulter = $this->eciexpenditureModel->getdefaulter('AC', $st_code, $cons_no);
                  
                if (empty($defaulter))
                $defaulter = [];
                //dd($defaulter);
                $defaultercount = count($defaulter);

                //Get Data entry Start Count %
                $Percent_defaultercount = $this->get_percentage($totalContestedCandidate, $defaultercount);
                //Get Data entry finalize Count 
              
                //Get Data entry finalize Count 
                $finalbyecicount = $this->eciexpenditureModel->gettotalfinalbyeci('AC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_finalbyecicount = $this->get_percentage($totalContestedCandidate, $finalbyecicount);
                //Get noticeatceocount Count 
                $noticeatceocount = $this->eciexpenditureModel->gettotalnoticeatCEO('AC', $st_code, $cons_no);

                //Get noticeatceocount  %
                $Percent_noticeatceocount = $this->get_percentage($totalContestedCandidate, $noticeatceocount);

                //Get noticeatdeocount Count 
                $noticeatdeocount = $this->eciexpenditureModel->gettotalnoticeatDEO('AC', $st_code, $cons_no);

                //Get noticeatdeocount Count %
                $Percent_noticeatdeocount = $this->get_percentage($totalContestedCandidate, $noticeatdeocount);
				
                $finalbyDEO=$this->eciexpenditureModel->gettotalfinalbyDEO('AC',$st_code,$cons_no);
                $finalcompletedcount=$this->eciexpenditureModel->gettotalCompletedbyEci('AC',$st_code,$cons_no);
	            $disqualifiedcount=$this->eciexpenditureModel->gettotalDisqualifiedbyEci('AC',$st_code,$cons_no);
       
	   //pending at CEO	
		if($finalbyDEO >=  0 && $finalbyecicount >=0 && $finalcompletedcount >=0 &&  $disqualifiedcount >=0 &&  $noticeatceocount >=0){
		 $finalbyceocount = $finalbyDEO-($finalbyecicount + $finalcompletedcount + $disqualifiedcount + $noticeatceocount);
		}
		//  $finalbyceocount = $this->eciexpenditureModel->gettotalfinalbyceo('AC', $st_code, $cons_no);
                //Get Data entry finalize Count %
                $Percent_finalbyceocount = $this->get_percentage($totalContestedCandidate, $finalbyceocount);


                return view('admin.ac.eci.Expenditure.statusdashboard', ['user_data' => $d, 'startdatacount' => $startdatacount, 'Percent_startdatacount' => $Percent_startdatacount, 'pendingdatacount' => $pendingdatacount, 'Percent_finaldatacount' => $Percent_finaldatacount, 'finaldatacount' => $finaldatacount, 'Percent_pendingdatacount' => $Percent_pendingdatacount, 'partiallypendingcount' => $partiallypendingcount, 'Percent_partiallypendingcount' => $Percent_partiallypendingcount, 'defaultercount' => $defaultercount, 'Percent_defaultercount' => $Percent_defaultercount, 'finalbyceocount' => $finalbyceocount, 'Percent_finalbyceocount' => $Percent_finalbyceocount, 'finalbyecicount' => $finalbyecicount, 'Percent_finalbyecicount' => $Percent_finalbyecicount, 'noticeatceocount' => $noticeatceocount, 'Percent_noticeatceocount' => $Percent_noticeatceocount, 'noticeatdeocount' => $noticeatdeocount, 'Percent_noticeatdeocount' => $Percent_noticeatdeocount, 'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI dashboard TRY CATCH ENDS HERE    
    }

// end dashboard function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 20-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getpendingcandidateList By ECI fuction     
     */
    public function getpendingcandidateList(Request $request, $state, $ac) {
        //AC ECI getpendingcandidateList TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;
                $candidate_id = array();
                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    // ->where('candidate_nomination_detail.st_code','=',$st_code)
                                    // ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    // ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.ac.eci.Expenditure.pending-report', ['user_data' => $d, 'pendingCandList' => $pendingCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($pendingCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//AC ECI pending candidate list TRY CATCH ENDS HERE   
    }

// end pending dataentry function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getpartiallypendingcandidateList By ECI fuction     
     */
    public function getpartiallypendingcandidateList(Request $request, $state, $ac) {
        //PC ECI candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                              $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
							  //->where('candidate_nomination_detail.st_code', '=', $st_code)
                              //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
							$candidate_id=[];
							if(!empty($EcifinalbyDEO) && count($EcifinalbyDEO)>0){
							foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                            $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                           }
							}
                  
							 $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
							 ->select('candidate_personal_detail.candidate_id','candidate_nomination_detail.ST_CODE as ST_CODE','candidate_nomination_detail.ac_no as constituency_no','candidate_nomination_detail.created_at',  'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                           // ->where('candidate_nomination_detail.st_code', '=', $st_code)
                           // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                     $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
							  ->where('candidate_nomination_detail.st_code', '=', $st_code)
                              //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
							$candidate_id=[];
							if(!empty($EcifinalbyDEO) && count($EcifinalbyDEO)>0){
							foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                            $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                               }	
							}
							
                  
							 $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
							 ->select('candidate_personal_detail.candidate_id','candidate_nomination_detail.ST_CODE as ST_CODE','candidate_nomination_detail.ac_no as constituency_no','candidate_nomination_detail.created_at',  'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                           // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                     $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
							  ->where('candidate_nomination_detail.st_code', '=', $st_code)
                              ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
							$candidate_id=[];
							if(!empty($EcifinalbyDEO) && count($EcifinalbyDEO)>0){
							foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                            $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                           }
							}
                  
							 $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
							 ->select('candidate_personal_detail.candidate_id','candidate_nomination_detail.ST_CODE as ST_CODE','candidate_nomination_detail.ac_no as constituency_no','candidate_nomination_detail.created_at',  'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.ac.eci.Expenditure.partiallypending-report', ['user_data' => $d, 'partiallyCandList' => $partiallyCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($partiallyCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI pending candidate list TRY CATCH ENDS HERE   
    }

// end dataentry start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 16-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getpendingcandidateList By ECI fuction     
     */
    public function getdefaultercandidateList(Request $request, $state, $ac) {
        //PC ECI defaulter TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            //->where('expenditure_understated.ST_CODE','=',$st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            ->where('expenditure_understated.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.ac.eci.Expenditure.defaulter-report', ['user_data' => $d, 'defaulterCandList' => $defaulterCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($defaulterCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI defaulter list TRY CATCH ENDS HERE   
    }

// end defaulter start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 18-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListByfiledData By ECI fuction     
     */
    public function candidateListByfiledData(Request $request, $state, $ac) {
        //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;

                if ($st_code == '0' && $cons_no == '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $finalCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.created_at', 'expenditure_reports.final_by_ro', 'expenditure_reports.candidate_id', 'expenditure_reports.ST_CODE', 'expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.filed-report', ['user_data' => $d, 'finalCandList' => $finalCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($finalCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByfiledData TRY CATCH ENDS HERE   
    }

// end candidateListByfiledData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return candidateListfinalbyCEO By ECI fuction     
     */
    public function candidateListfinalbyCEO(Request $request, $state, $ac) {
        //PC ROPC candidateListfinalbyCEO TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;

                $candidate_id=[];
                 if ($st_code == '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
								$query->whereNull('expenditure_reports.final_action');
								$query->orwhere('expenditure_reports.final_action', '=','');
							  }) 
                            ->get();

                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
							
							$getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
							->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                            #######################Notice add extra #######
                           $noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                           // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################

                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }

                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                         
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
								$query->whereNull('expenditure_reports.final_action');
								$query->orwhere('expenditure_reports.final_action', '=','');
							  }) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                       
                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
							 
							$getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
							->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                            #######################Notice add extra #######
                           $noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################
  
                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }

                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
							
                          //  echo '<pre>'; print_r( $candidate_id);
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
								$query->whereNull('expenditure_reports.final_action');
								$query->orwhere('expenditure_reports.final_action', '=','');
							  }) 
                             ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
							
							 $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
							->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                            #######################Notice add extra #######
                           $noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################
                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }
							
                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.finalbyceo-report', ['user_data' => $d, 'finalbyceoCandList' => $finalbyceoCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($finalbyceoCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE   
    }

// end candidateListByfinalizeData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getclosedbyECI By ECI fuction     
     */
    public function getclosedbyECI(Request $request, $state, $ac) {
        //PC ROPC getclosedbyECI TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));

                $st_code = !empty($st_code) ? $st_code : '0';
                $cons_no = !empty($cons_no) ? $cons_no : '0';
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $closedbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_action','==','Closed')
                            ->where('expenditure_reports.final_by_eci', '1')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Closed')
                                ->orWhere('expenditure_reports.final_action', '=', 'Disqualified')
                                ->orWhere('expenditure_reports.final_action', '=', 'Case Dropped');
                            })
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $closedbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci', '1')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Closed')
                                ->orWhere('expenditure_reports.final_action', '=', 'Disqualified')
                                ->orWhere('expenditure_reports.final_action', '=', 'Case Dropped');
                            })
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $closedbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            //->where('expenditure_notification.eci_action','0')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci', '1')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Closed')
                                ->orWhere('expenditure_reports.final_action', '=', 'Disqualified')
                                ->orWhere('expenditure_reports.final_action', '=', 'Case Dropped');
                            })
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                //dd($getclosedbyECI);
                return view('admin.ac.eci.Expenditure.closedbyeci-mis', ['user_data' => $d, 'closedbyECI' => $closedbyECI, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($closedbyECI)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getclosedbyECI TRY CATCH ENDS HERE   
    }

// end getclosedbyECI start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param getclosedListfinalbyECI By ECI fuction     
     */
    //ECI getclosedListfinalbyECI EXCEL REPORT STARTS
    public function getclosedbyECIEXL(Request $request, $state, $ac) {
        //ECI getcandidateListpendingatECIEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                // dd($totalContestedCandidate);

                $cur_time = Carbon::now();

                \Excel::create('ECIClosedCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if ($st_code == '0' && $cons_no == '0') {
                            $closedbyECIEXL = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    //->where('expenditure_reports.final_action','==','Closed')
                                    ->where('expenditure_reports.final_by_eci', '1')
                                    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                                    ->where(function($q) {
                                        $q->where('expenditure_reports.final_action', '=', 'Closed')
                                        ->orWhere('expenditure_reports.final_action', '=', 'Disqualified')
                                        ->orWhere('expenditure_reports.final_action', '=', 'Case Dropped');
                                    })
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $closedbyECIEXL = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    // ->where('expenditure_notification.eci_action','0')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    // ->where('expenditure_reports.final_action','==','Closed')
                                    ->where('expenditure_reports.final_by_eci', '1')
                                    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                                    ->where(function($q) {
                                        $q->where('expenditure_reports.final_action', '=', 'Closed')
                                        ->orWhere('expenditure_reports.final_action', '=', 'Disqualified')
                                        ->orWhere('expenditure_reports.final_action', '=', 'Case Dropped');
                                    })
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $closedbyECIEXL = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    //->where('expenditure_notification.eci_action','0')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    //->where('expenditure_reports.final_action','==','Closed')
                                    ->where('expenditure_reports.final_by_eci', '1')
                                    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                                    ->where(function($q) {
                                        $q->where('expenditure_reports.final_action', '=', 'Closed')
                                        ->orWhere('expenditure_reports.final_action', '=', 'Disqualified')
                                        ->orWhere('expenditure_reports.final_action', '=', 'Case Dropped');
                                    })
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($closedbyECIEXL as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                            $data = array(
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
                            );
                            $TotalUsers = count($closedbyECIEXL);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'AC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging A/C By Candidate'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getclosedListfinalbyECI EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI getclosedListfinalbyECI EXCEL REPORT FUNCTION ENDS
########################End status dashboard by Niraj 16-05-2019 #####################
#################################Start MIS Report By Niraj 28-05-2019#####################################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersmisDetails By ECI fuction     
     */
	 
	 public function getOfficersmisDetails(Request $request) {
        //dd($request->all());
        //PC ECI getOfficersmisDetails TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $st_code = $request->input('state');
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = $request->input('ac');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                  $totalContestedCandidatedata = '';
                // echo  $st_code.'pc'.$cons_no; die;
                // DB::enableQueryLog();
                if (!empty($st_code) && $cons_no == '0' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '0' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (($st_code == '' && $cons_no == '')  || ($st_code == '0' && $cons_no == '0')) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }

                // dd(DB::getQueryLog());
                // dd($totalContestedCandidatedata);
                // return view('admin.ac.eci.Expenditure.mis-officer-details', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist, 'count' => count($totalContestedCandidatedata)]);
                  return view('admin.ac.eci.Expenditure.mis-officer-details', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist, 'count' => count($totalContestedCandidatedata)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getOfficersmis TRY CATCH ENDS HERE    
    }

// end getOfficersmisDetails function

	 
	 
    public function getOfficersmis(Request $request) {
        //dd($request->all());
        //PC ECI getOfficersmis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $st_code = $request->input('state');
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = $request->input('ac');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $totalContestedCandidatedata='';
                // echo  $st_code.'Ac'.$cons_no; die;
                // DB::enableQueryLog();
                if (!empty($st_code) && $cons_no == '0' && $st_code != 'All') {
                    // dd("first");
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '0' && $st_code != 'All') {
                     // dd("second");
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '0' && $st_code == 'All') {
                       dd("Third");
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (($st_code == '' && $cons_no == '')  || ($st_code == '0' && $cons_no == '0')){
                    // dd("fourt");
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }

                // dd(DB::getQueryLog());
                // dd($totalContestedCandidatedata);
                // return view('admin.ac.eci.Expenditure.mis-officer', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist, 'count' => count($totalContestedCandidatedata)]);
                 return view('admin.ac.eci.Expenditure.mis-officer', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist, 'count' => count($totalContestedCandidatedata)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getOfficersmis TRY CATCH ENDS HERE    
    }

// end getOfficersmis function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersmis By ECI fuction     
     */
    //ECI getOfficersmis EXCEL REPORT STARTS
    public function getOfficersmisEXL(Request $request, $state, $ac) {
        //ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'ac'.$cons_no; die;
                // dd($totalContestedCandidate);

                $cur_time = Carbon::now();

                \Excel::create('EciACMISExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no, $permitstates) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no, $permitstates) {

                        if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        } else if ($st_code == '' && $cons_no == '') {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $TotalPendingatRO = 0;
                        $TotalPendingatCEO = 0;
                        $TotalPendingatECI = 0;
                        $TotalfiledData = 0;
                        $TotalnotfiledData = 0;
                        $Totalac = 0;
                        $TotalDEONotice = 0;
                        $TotalCEONotice = 0;
                        $Totalfinalcompletedcount = 0;
                        $TotalFinalByDEO = 0;
                        $pendingatRO = 0;
                        $Totaldisqualifiedcount=0;

                        $user = Auth::user();
                        $count = 1;
                        foreach ($totalContestedCandidatedata as $key => $listdata) {

                            //get finalby DEO count
                            $finalbyDEO = $this->eciexpenditureModel->gettotalfinalbyDEO('AC', $listdata->st_code, $cons_no);
                            //get partially pending data count
                           // $pendingatROold = $this->eciexpenditureModel->gettotalpartiallypending('AC', $listdata->st_code, $cons_no);
                            //Get Data entry finalize Count 
                            //$pendingatCEO = $this->eciexpenditureModel->gettotalfinalbyceo('AC', $listdata->st_code, $cons_no);

                            //Get pendingatDEO Count 
                           // $pendingatRO = $listdata->totalcandidate - $pendingatCEO;

                            //Get Data entry finalize Count 
                            $pendingatECI = $this->eciexpenditureModel->gettotalfinalbyeci('AC', $listdata->st_code, $cons_no);

                            //Get filedcount Count 
                            $filedcount = $this->eciexpenditureModel->gettotaldataentryStart('AC', $listdata->st_code, $cons_no);

                            // Get Pending Data Count 
                            $notfiledcount = $listdata->totalcandidate - $filedcount;
                           

                            //Get noticeatDEOCount Count 
                            $noticeatDEOCount = $this->eciexpenditureModel->gettotalnoticeatDEO('AC', $listdata->st_code, $cons_no);
                                 
                            //Get noticeatCEOCount Count 
                            $noticeatCEOCount = $this->eciexpenditureModel->gettotalnoticeatCEO('AC', $listdata->st_code, $cons_no);

                            //Get finalcompletedcount Count 
                            $finalcompletedcount = $this->eciexpenditureModel->gettotalCompletedbyEci('AC', $listdata->st_code, $cons_no);
                            
                            $disqualifiedcount=$this->eciexpenditureModel->gettotalDisqualifiedbyEci('AC',$listdata->st_code,$cons_no);


                            $st = getstatebystatecode($listdata->st_code);
                            $acbystate = getacbystate($listdata->st_code);
							$acdetails=getacbyacno($listdata->st_code,$listdata->ac_no);
							$election_id=Session::get('DB_ELECTION_ID');
		                    $currelectionbyeid=$this->eciexpenditureModel->expcurrentelectiondetails('AC',$listdata->st_code,$election_id,'');
                            $account = count($currelectionbyeid);
                            $Totalac += $account;
							
							
							  //pending at DEO
							  if($finalbyDEO >= 0 ){
								$pendingatRO=$listdata->totalcandidate-($finalbyDEO);
								if($pendingatRO >= 0 ){$TotalPendingatRO += $pendingatRO;}
								}  
							 //pending at CEO	
							 if($finalbyDEO >= 0 && $pendingatECI >=0 && $finalcompletedcount >=0){
							 $pendingatCEO = $finalbyDEO-($pendingatECI + $finalcompletedcount+$disqualifiedcount);
							 if($pendingatCEO >= 0) { $TotalPendingatCEO += $pendingatCEO; }
							}

                            $filedcount = !empty($filedcount) ? $filedcount : '0';
                            $finalbyDEO = !empty($finalbyDEO) ? $finalbyDEO : '0';
                            $pendingatRO = !empty($pendingatRO) ? $pendingatRO : '0';
                            $pendingatCEO = !empty($pendingatCEO) ? $pendingatCEO : '0';
                            $pendingatECI = !empty($pendingatECI) ? $pendingatECI : '0';
                            $noticeatDEOCount = !empty($noticeatDEOCount) ? $noticeatDEOCount : '0';
                            $noticeatCEOCount = !empty($noticeatCEOCount) ? $noticeatCEOCount : '0';
                            $finalcompletedcount = !empty($finalcompletedcount) ? $finalcompletedcount : '0';
                            $disqualifiedcount = !empty($disqualifiedcount) ? $disqualifiedcount : '0';
                            $account = !empty($account) ?  $account : '0';
                            $notfiledcount = (!empty($notfiledcount) || $notfiledcount <= 0) ? $notfiledcount : '0';

                            $data = array(
                                $st->ST_NAME,
                                $account,
                                $listdata->totalcandidate,
                                $finalbyDEO,
                                $pendingatRO,
                                $noticeatDEOCount,
                                $pendingatCEO,
                                $noticeatCEOCount,
                                $pendingatECI,
                                $finalcompletedcount,
                                $disqualifiedcount
                            );
                           
							$TotalUsers += $listdata->totalcandidate;
                           // $TotalPendingatRO += $pendingatRO; 
                            $TotalFinalByDEO += $finalbyDEO;
                           // $TotalPendingatCEO += $pendingatCEO;
                            $TotalPendingatECI += $pendingatECI;
                            $TotalDEONotice += $noticeatDEOCount;
                            $TotalCEONotice += $noticeatCEOCount;
                            $Totalfinalcompletedcount += $finalcompletedcount;
                            $Totaldisqualifiedcount += $disqualifiedcount;
                            $TotalnotfiledData += $notfiledcount;
                            $TotalfiledData += $filedcount;
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        
                        $totalvalues = array('Total', $Totalac, $TotalUsers,$TotalFinalByDEO, $TotalPendingatRO,$TotalDEONotice, $TotalPendingatCEO,$TotalCEONotice, $TotalPendingatECI, $Totalfinalcompletedcount,$Totaldisqualifiedcount);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State Name', 'Total AC', 'Total Candidate', 'Finalise By DEO', 'Pending-DEO','Notice-DEO','Pending- CEO', 'Notice-CEO','Pending - ECI', 'Closed/Case Dropped','Disqualified'
                                )
                        );
                    });
                })->export('csv');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getOfficersmisEXL EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI ACTIVE USERS EXCEL REPORT FUNCTION ENDS
    //ECI getOfficersmis PDF REPORT STARTS
    public function getOfficersmisPDF(Request $request, $state, $ac) {
        //ECI getOfficersmisPdf PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
				  // echo  $st_code.'ac'.$cons_no; die;
                $totalContestedCandidatedata='';
                $cur_time = Carbon::now();
                if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (($st_code == '' && $cons_no == '')|| ($st_code == '' && $cons_no == '')) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.ac.eci.Expenditure.mis-officerPDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata,'st_code' => $st_code,'cons_no' => $cons_no]);
                return $pdf->download('EciOfficerMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.mis-officerPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI mis-officerPDFhtml PDF REPORT TRY CATCH BLOCK ENDS
    }
//ECI ACTIVE USERS PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return finalCandidateList By ECI fuction     
     */
	 
	  public function getOfficersmisPDFDetails(Request $request, $state, $ac) {
        //ECI getOfficersmisPdf PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
				  // echo  $st_code.'ac'.$cons_no; die;
                $cur_time = Carbon::now();
                if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (($st_code == '' && $cons_no == '') ||($st_code == '0' && $cons_no == '0')) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.ac.eci.Expenditure.mis-officerPDFDetailshtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata,'st_code' => $st_code,'cons_no' => $cons_no]);
                return $pdf->download('EciOfficerMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.mis-officerPDFDetailshtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI mis-officerPDFhtml PDF REPORT TRY CATCH BLOCK ENDS
    }
//ECI ACTIVE USERS PDF REPORT FUNCTION ENDS


    public function finalCandidateList(Request $request, $state, $ac) {
        //dd($request->all());
        //PC ECI finalCandidateList TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                //$st_code = base64_decode($request->input('state'));
                // $permitstate=$this->accessstate;
                $zonestate = $this->eciexpenditureModel->getzonestate($username);
                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    $st_code = array_values($permitstate)[0];
                } else {
                    $st_code = 0;
                }

                //echo $st_code;die;
                #########################Code For State Wise Access#####################
                // echo $st_code.'pc'.$cons_no; die;
                DB::enableQueryLog();
                if (!empty($st_code) && $cons_no == '') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            //->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            //->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            //->where('candidate_nomination_detail.st_code','=',$st_code)
                            //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            //->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }
                //dd(DB::getQueryLog());
                // dd($totalContestedCandidate);
                return view('admin.ac.eci.Expenditure.candidate-report', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code, 'count' => count($totalContestedCandidatedata), "statelist" => $statelist]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getOfficersmis TRY CATCH ENDS HERE    
    }

// end getOfficersmis function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersmis By ECI fuction     
     */
    //ECI getOfficersmis EXCEL REPORT STARTS
    public function finalCandidateListEXL(Request $request, $state, $ac) {
        //ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('ECICandidateMISExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if (!empty($st_code && $cons_no == '')) {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    //->count();
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                                    // ->groupBy("candidate_nomination_detail.st_code")
                                    ->orderBy("candidate_nomination_detail.ac_no")
                                    ->get();
                        } else if (!empty($st_code) && $cons_no != '') {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                                    //->groupBy("candidate_nomination_detail.st_code")
                                    ->orderBy("candidate_nomination_detail.ac_no")
                                    ->get();
                        } else {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    //->where('candidate_nomination_detail.st_code','=',$st_code)
                                    //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                                    //->groupBy("candidate_nomination_detail.st_code")
                                    ->orderBy("candidate_nomination_detail.ac_no")
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($totalContestedCandidatedata as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                            $data = array(
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME
                            );
                            $TotalUsers = count($totalContestedCandidatedata);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'AC No & Name', 'Candidate Name', 'Party Name'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI finalCandidateList EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI ACTIVE USERS EXCEL REPORT FUNCTION ENDS
    //ECI finalCandidateList PDF REPORT STARTS
    public function finalCandidateListPDF(Request $request, $state, $ac) {
        //ECI finalCandidateList PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if (!empty($st_code && $cons_no == '')) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            ->orderBy("candidate_nomination_detail.ac_no")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            ->orderBy("candidate_nomination_detail.ac_no")
                            ->get();
                } else {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            //->where('candidate_nomination_detail.st_code','=',$st_code)
                            //->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            // ->count();
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            ->orderBy("candidate_nomination_detail.ac_no")
                            ->get();
                }
                $pdf = PDF::loadView('admin.ac.eci.Expenditure.candidatePDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata]);
                return $pdf->download('EciCandidateMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.candidatePDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI mis-officerPDFhtml PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI candidate PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatRO By ECI fuction     
     */
    public function getcandidateListpendingatRO(Request $request, $state, $ac) {
        //PC ECI candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                // DB::enableQueryLog();
                $candidate_id = array();
                if ($st_code == '0' && $cons_no == '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                   // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                   // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.final_by_ro', '1')
                    ->where('expenditure_reports.finalized_status', '1')
                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();

                    foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                        $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                    }
                    $partiallyCandList = DB::table('candidate_nomination_detail')
                    ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                    //->where('candidate_nomination_detail.st_code', '=', $st_code)
                   // ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                    ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')    
                    ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                     ->where('expenditure_reports.ST_CODE', '=', $st_code)
                   // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.final_by_ro', '1')
                    ->where('expenditure_reports.finalized_status', '1')
                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();

                    foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                        $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                    }
                    $partiallyCandList = DB::table('candidate_nomination_detail')
                    ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                   // ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                    ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')    
                    ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.final_by_ro', '1')
                    ->where('expenditure_reports.finalized_status', '1')
                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();

                    foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                        $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                    }
                    $partiallyCandList = DB::table('candidate_nomination_detail')
                    ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                    ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                    ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')    
                    ->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.ac.eci.Expenditure.pendingatdeo-mis', ['user_data' => $d, 'partiallyCandList' => $partiallyCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($partiallyCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getcandidateListpendingatRO TRY CATCH ENDS HERE   
    }

// end getcandidateListpendingatRO function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatROEXL By ECI fuction     
     */
//ECI getcandidateListpendingatROEXL EXCEL REPORT STARTS
    public function getcandidateListpendingatROEXL(Request $request, $state, $ac) {
//ECI getcandidateListpendingatROEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
//echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('ECIPendingatDEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        $candidate_id = array();
                        if ($st_code == '0' && $cons_no == '0') {
                            $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                           // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
        
                            foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                                $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                            }
                            $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            //->where('candidate_nomination_detail.st_code', '=', $st_code)
                           // ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')    
                            ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
        
                            foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                                $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                            }
                            $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                           // ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')    
                            ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
        
                            foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                                $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                            }
                            $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')    
                            ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                       // dd($partiallyCandList);
                        foreach ($partiallyCandList as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            
                           // $lastdate =!empty($candDetails->last_date_prescribed_acct_lodge)? new DateTime($candDetails->last_date_prescribed_acct_lodge):;
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = !empty($candDetails->last_date_prescribed_acct_lodge) ? date('d-m-Y',strtotime($candDetails->last_date_prescribed_acct_lodge)):'N/A'; // 31-07-2012
                           
                          // $scrutinysubmit = new DateTime($candDetails->report_submitted_date);
                           $scrutinyreportsubmitdate = !empty($candDetails->report_submitted_date) ? date('d-m-Y',strtotime($candDetails->report_submitted_date)):'N/A'; // 31-07-2012
                           //$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
                          // $candidatelodging = new DateTime($candDetails->date_orginal_acct);
                           $candidatelodgingdate = !empty($candDetails->date_orginal_acct) ? date('d-m-Y',strtotime($candDetails->date_orginal_acct)):'N/A'; // 31-07-2012

                          // $sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
                           $ceosendingdate = !empty($candDetails->date_of_sending_deo) ? date('d-m-Y',strtotime($candDetails->date_of_sending_deo)):'N/A'; // 31-07-2012
                   
                          // $ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
                           $ceoreceivedate = !empty($candDetails->date_of_receipt) ? date('d-m-Y',strtotime($candDetails->date_of_receipt)):'N/A'; // 31-07-2012
                           
                          // $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                           
                            
                            $scrutinyreportsubmitdate = (!empty($scrutinyreportsubmitdate) && $scrutinyreportsubmitdate !='30-11--0001')  ?  $scrutinyreportsubmitdate : 'N/A';
							$candidatelodgingdate =  (!empty($candidatelodgingdate) && $candidatelodgingdate !='30-11--0001')  ?  $candidatelodgingdate : 'N/A' ;
							$ceosendingdate =  (!empty($ceosendingdate) && $ceosendingdate !='30-11--0001' && $candDetails->final_by_ro=='1')  ?  $ceosendingdate : 'N/A' ; 
							//$ceoreceivedate = (!empty($ceoreceivedate) && $ceoreceivedate !='30-11--0001')  ?  $ceoreceivedate : 'N/A' ; 

                            $acno = !empty($acDetails->AC_NO) ? $acDetails->AC_NO : '';
                            $acname = !empty($acDetails->AC_NAME) ? $acDetails->AC_NAME : '';
                            $data = array(
                                $st->ST_NAME,
                                $acno . '-' . $acname,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate,
								$scrutinyreportsubmitdate,
								$candidatelodgingdate,
								$ceosendingdate,
								//$ceoreceivedate
                            );
                            $TotalUsers = count($partiallyCandList);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }

                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State','AC No & Name', 'Candidate Name', 'Party Name','Last Date Of Lodging','Date of Scrutiny Report Submission','Date of Lodging A/C By Candidate','Date of Sending to the CEO'
                            )
                        );
                    });
                })->export('csv');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getcandidateListpendingatROPDF EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI getcandidateListpendingatROPDF EXCEL REPORT FUNCTION ENDS
    //ECI getcandidateListpendingatROPDF PDF REPORT STARTS
    public function getcandidateListpendingatROPDF(Request $request, $state, $ac) {
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                $candidate_id = array();
                if ($st_code == '0' && $cons_no == '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                   // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                   // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.final_by_ro', '1')
                    ->where('expenditure_reports.finalized_status', '1')
                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();

                    foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                        $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                    }
                    $partiallyCandList = DB::table('candidate_nomination_detail')
                    ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                    //->where('candidate_nomination_detail.st_code', '=', $st_code)
                   // ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                    ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')    
                    ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                     ->where('expenditure_reports.ST_CODE', '=', $st_code)
                   // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.final_by_ro', '1')
                    ->where('expenditure_reports.finalized_status', '1')
                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();

                    foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                        $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                    }
                    $partiallyCandList = DB::table('candidate_nomination_detail')
                    ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                   // ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                    ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')    
                    ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.final_by_ro', '1')
                    ->where('expenditure_reports.finalized_status', '1')
                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();

                    foreach ($EcifinalbyDEO as $EcifinalbyDEOData) {
                        $candidate_id[] = $EcifinalbyDEOData->candidate_id;
                    }
                    $partiallyCandList = DB::table('candidate_nomination_detail')
                    ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                    ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                    ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')    
                    ->get();
                }
                $pdf = PDF::loadView('admin.ac.eci.Expenditure.candidatePendingatDEOPDFhtml', ['user_data' => $d, 'pendingatDEOCandList' => $partiallyCandList]);
                return $pdf->download('EcipendingatDEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.candidatePendingatDEOPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI getcandidateListpendingatROPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatCEO By ECI fuction     
     */
    public function getcandidateListpendingatCEO(Request $request, $state, $ac) {
//PC ECI getcandidateListpendingatCEO TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
				
				
				////////////////////////////////////////////////////////
				
				 $candidate_id=[];
                 if ($st_code == '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
								$query->whereNull('expenditure_reports.final_action');
								$query->orwhere('expenditure_reports.final_action', '=','');
							  }) 
                            ->get();

                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
							
							$getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
							->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                            #######################Notice add extra #######
                           $noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                           // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################

                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }

                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                         
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
								$query->whereNull('expenditure_reports.final_action');
								$query->orwhere('expenditure_reports.final_action', '=','');
							  }) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                       
                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
							 
							$getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
							->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                             #######################Notice add extra #######
                      $noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################
  
                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }

                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
							
                          //  echo '<pre>'; print_r( $candidate_id);
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
								$query->whereNull('expenditure_reports.final_action');
								$query->orwhere('expenditure_reports.final_action', '=','');
							  }) 
                             ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
							
							 $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
							->where('expenditure_reports.finalized_status','1')
							->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                               #######################Notice add extra #######
$noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                           ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################
                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }
							
                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
				///////////////////////////////////////////////////////
             
                return view('admin.ac.eci.Expenditure.pendingatceo-mis', ['user_data' => $d, 'finalbyceoCandList' => $finalbyceoCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($finalbyceoCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE   
    }

// end candidateListByfinalizeData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatROEXL By ECI fuction     
     */
//ECI getcandidateListpendingatCEOEXL EXCEL REPORT STARTS
    public function getcandidateListpendingatCEOEXL(Request $request, $state, $ac) {
//ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();

                \Excel::create('ECIPendingatCEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        $candidate_id=[];
                          if ($st_code == '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
                                $query->whereNull('expenditure_reports.final_action');
                                $query->orwhere('expenditure_reports.final_action', '=','');
                              }) 
                            ->get();

                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                            
                            $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                            #######################Notice add extra #######
                           $noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                           // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################

                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }

                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                         
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
                                $query->whereNull('expenditure_reports.final_action');
                                $query->orwhere('expenditure_reports.final_action', '=','');
                              }) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                       
                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                             
                            $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                             #######################Notice add extra #######
                      $noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################
  
                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }

                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                            
                          //  echo '<pre>'; print_r( $candidate_id);
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
                                $query->whereNull('expenditure_reports.final_action');
                                $query->orwhere('expenditure_reports.final_action', '=','');
                              }) 
                             ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                            
                             $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                               #######################Notice add extra #######
$noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                           ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################
                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }
                            
                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($finalbyceoCandList as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012
                           
                           $scrutinysubmit = new DateTime($candDetails->report_submitted_date);
                            $scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012
                           //$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
                           $candidatelodgingdate= date('d-m-Y',strtotime($candDetails->date_orginal_acct));
                           
                           $sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
                           $ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012
                   
                           $ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
                           $ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012
                           
                          // $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                           
                             $lodgingDate =$lodgingDate ??  '22-06-2019';
                             $scrutinyreportsubmitdate =$scrutinyreportsubmitdate ??  'N/A';
                             $candidatelodgingdate =$candidatelodgingdate ??  'N/A';
                             $ceosendingdate =$ceosendingdate ??  'N/A';
                             $ceoreceivedate = (strtotime($candDetails->date_of_receipt) > 0 && !empty($candDetails->date_of_receipt)) ? $ceoreceivedate : 'N/A'; 
                            $data = array(
                                $st->ST_NAME,
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate,
								$scrutinyreportsubmitdate,
								$candidatelodgingdate,
								$ceosendingdate,
								$ceoreceivedate
                            );
                            $TotalUsers = count($finalbyceoCandList);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                           'State', 'AC No & Name', 'Candidate Name', 'Party Name','Last Date Of Submission','Date Of Scrutiny Report Submission','Date Of Lodging A/C By Candidates','Date Of Sending To CEO','Date Of Received By CEO'
                            )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI getcandidateListpendingatROPDF EXCEL REPORT FUNCTION ENDS
//ECI getcandidateListpendingatCEOPDF PDF REPORT STARTS
    public function getcandidateListpendingatCEOPDF(Request $request, $state, $ac) {
//ECI getcandidateListpendingatCEOPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                $candidate_id=[];
                 if ($st_code == '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
                                $query->whereNull('expenditure_reports.final_action');
                                $query->orwhere('expenditure_reports.final_action', '=','');
                              }) 
                            ->get();

                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                            
                            $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                            #######################Notice add extra #######
                           $noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                           // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################

                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }

                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                         
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
                                $query->whereNull('expenditure_reports.final_action');
                                $query->orwhere('expenditure_reports.final_action', '=','');
                              }) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                       
                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                             
                            $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                             #######################Notice add extra #######
                      $noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################
  
                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }

                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                            
                          //  echo '<pre>'; print_r( $candidate_id);
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $pendingateciCandlist = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->where(function($query) {
                                $query->whereNull('expenditure_reports.final_action');
                                $query->orwhere('expenditure_reports.final_action', '=','');
                              }) 
                             ->groupBy('expenditure_reports.candidate_id')
                            ->get();

                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                            
                             $getdisqualifiedcandidateListbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                             ->select('expenditure_reports.candidate_id')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                           ->where('expenditure_reports.final_by_eci','1')
                            ->where('expenditure_reports.finalized_status','1')
                            ->where('expenditure_reports.final_action', 'Disqualified')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                               #######################Notice add extra #######
$noticeatCEO=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                           ->select('expenditure_reports.candidate_id')
                           ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            })
                              ->groupBy('expenditure_reports.candidate_id')
                              ->get();

                                foreach ($noticeatCEO as $noticeatCEOData) {
                                $candidate_id[] = $noticeatCEOData->candidate_id;
                            }


                            ##############################################
                            foreach ($getdisqualifiedcandidateListbyECI as $getdisqualifiedcandidateListbyECIData) {
                                $candidate_id[] = $getdisqualifiedcandidateListbyECIData->candidate_id;
                            }
                            
                            foreach ($pendingateciCandlist as $pendingateciCandlistData) {
                                $candidate_id[] = $pendingateciCandlistData->candidate_id;
                            }
                            foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                                $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                            }
                            $finalbyceoCandList = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.created_at','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','candidate_nomination_detail.candidate_id','expenditure_reports.report_submitted_date','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no as constituency_no', 'candidate_personal_detail.cand_name','candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                             ->where('expenditure_reports.ST_CODE', '=', $st_code)
                             ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.ac.eci.Expenditure.candidatePendingatCEOPDFhtml', ['user_data' => $d, 'pendingatCEOCandList' => $finalbyceoCandList]);
                return $pdf->download('EcipendingatCEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.candidatePendingatCEOPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
//ECI getcandidateListpendingatCEOPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI getcandidateListpendingatCEOPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 05-07-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatECIReport By ECI fuction     
     */
    public function getcandidateListpendingatECIReport(Request $request, $state, $ac) {
//PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                ###################################################
                  $query=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                           ->where(function($q) {
                           $q->whereNull('expenditure_reports.final_action')
                            ->orWhere('expenditure_reports.final_action','=','');
                          });
                if ($st_code != '0' && $cons_no == '0') {
                    $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                    
                } elseif ($st_code != '0' && $cons_no != '0') {
                     $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                      $query->where('expenditure_reports.constituency_no', '=', $cons_no);
                }
                $getcandidateListpendingatECIReport=$query->get();
                ###################################################
               
                //dd($getcandidateListpendingatECIReport);
                return view('admin.ac.eci.Expenditure.finalbyeci-report', ['user_data' => $d, 'getcandidateListpendingatECIReport' => $getcandidateListpendingatECIReport, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($getcandidateListpendingatECIReport)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getcandidateListpendingatECIReport TRY CATCH ENDS HERE   
    }

// end getcandidateListpendingatECIReport start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatECI By ECI fuction     
     */
    public function getcandidateListpendingatECI(Request $request, $state, $ac) {
//PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                $query=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                           ->where(function($q) {
                           $q->whereNull('expenditure_reports.final_action')
                            ->orWhere('expenditure_reports.final_action','=','');
                          });
                if ($st_code != '0' && $cons_no == '0') {
                    $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                    
                } elseif ($st_code != '0' && $cons_no != '0') {
                     $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                      $query->where('expenditure_reports.constituency_no', '=', $cons_no);
                }
                $pendingateciCandlist=$query->get();
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.pendingateci-mis', ['user_data' => $d, 'pendingateciCandlist' => $pendingateciCandlist, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($pendingateciCandlist)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getcandidateListpendingatECI TRY CATCH ENDS HERE   
    }

// end getcandidateListpendingatECI start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 28-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatECIEXL By ECI fuction     
     */
//ECI getcandidateListpendingatECIEXL EXCEL REPORT STARTS
    public function getcandidateListpendingatECIEXL(Request $request, $state, $ac) {
//ECI getcandidateListpendingatECIEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
// echo  $st_code.'pc'.$cons_no; die;
                // dd($totalContestedCandidate);

                $cur_time = Carbon::now();

                \Excel::create('ECIPendingatECICandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        $query=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                           ->where(function($q) {
                           $q->whereNull('expenditure_reports.final_action')
                            ->orWhere('expenditure_reports.final_action','=','');
                          });
                if ($st_code != '0' && $cons_no == '0') {
                    $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                    
                } elseif ($st_code != '0' && $cons_no != '0') {
                     $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                      $query->where('expenditure_reports.constituency_no', '=', $cons_no);
                }
                $pendingatECICandList=$query->get();

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($pendingatECICandList as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                            $data = array(
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
                            );
                            $TotalUsers = count($pendingatECICandList);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'AC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging A/C By Candidate'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
//ECI getcandidateListpendingatECIEXL EXCEL REPORT TRY CATCH BLOCK ENDS
    }

//ECI getcandidateListpendingatECIEXL EXCEL REPORT FUNCTION ENDS
//ECI getcandidateListpendingatECIPDF PDF REPORT STARTS
    public function getcandidateListpendingatECIPDF(Request $request, $state, $ac) {
//ECI getcandidateListpendingatECIPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                $query=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                           ->where(function($q) {
                           $q->whereNull('expenditure_reports.final_action')
                            ->orWhere('expenditure_reports.final_action','=','');
                          });
                if ($st_code != '0' && $cons_no == '0') {
                    $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                    
                } elseif ($st_code != '0' && $cons_no != '0') {
                     $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                      $query->where('expenditure_reports.constituency_no', '=', $cons_no);
                }
                $pendingatECICandList=$query->get();

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.ac.eci.Expenditure.candidatePendingatECIPDFhtml', ['user_data' => $d, 'pendingatECICandList' => $pendingatECICandList]);
                return $pdf->download('EcipendingatECICandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.candidatePendingatECIPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI getcandidateListpendingatECIPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI getcandidateListpendingatECIPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getCandidatemis By ECI fuction     
     */
    public function getCandidatemis(Request $request) {
        //dd($request->all());
        //PC ECI getCandidatemis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $st_code = $request->input('state');
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = $request->input('ac');
                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';
                // echo  $st_code.'pc'.$cons_no; die;
               // DB::enableQueryLog();

                if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if ($st_code == '' && $cons_no == '') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.ac.eci.Expenditure.mis-candidate', ['user_data' => $d, 'totalContestedCandidate' => $totalContestedCandidate, 'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist, 'count' => count($totalContestedCandidate)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getCandidatemis TRY CATCH ENDS HERE    
    }

// end getCandidatemis function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getCandidatemisEXL By ECI fuction     
     */
//ECI getCandidatesmisEXL EXCEL REPORT STARTS
    public function getCandidatesmisEXL(Request $request, $state, $ac) {
        //ECI getCandidatesmisEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                // dd($totalContestedCandidate);

                $cur_time = Carbon::now();

                \Excel::create('ECICandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no, $permitstates) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no, $permitstates) {

                        if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                            $totalContestedCandidate = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                            $totalContestedCandidate = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                            $totalContestedCandidate = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        } else if ($st_code == '' && $cons_no == '') {
                            $totalContestedCandidate = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($totalContestedCandidate as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
                            //$date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            // $lodgingDate=$date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = $candDetails->totalcandidate;

                            $stdetails = getstatebystatecode($candDetails->st_code);
                            $filedcount = $this->eciexpenditureModel->gettotaldataentryStart('AC', $candDetails->st_code, $cons_no);

                            // Get Pending Data Count 
                            $notfiledcount = $TotalUsers - $filedcount;
                            // echo $TotalUsers.'filedcount=>'.$filedcount.'notfiledcount=>'.$notfiledcount; die('test');
                            $defaulter = $this->eciexpenditureModel->getdefaulter('AC', $candDetails->st_code, $cons_no);
                            //dd($defaulter);
                            $defaultercount = !empty($defaulter) ? count($defaulter) : '0';
                            $notinTime = $this->eciexpenditureModel->gettotalNotinTime('AC', $candDetails->st_code, $cons_no);
                            if (empty($filedcount))
                                $filedcount = '0';
                            if (empty($notfiledcount))
                                $notfiledcount = '0';
                            if (empty($notinTime))
                                $notinTime = '0';
                            if (empty($defaultercount))
                                $defaultercount = '0';
                            $data = array(
                                $st->ST_NAME,
                                $filedcount,
                                $notfiledcount,
                                $notinTime,
                                $defaultercount
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State Name', 'Total Filed Candidate', 'Not Filed Candidate', 'Not In Time Candidate', 'Defaulter Candidate'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getCandidatesmisEXL EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI getCandidatesmisEXL EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getCandidatemisPDF By ECI fuction     
     */
    //ECI getCandidatemisPDF PDF REPORT STARTS

    public function getCandidatemisPDF(Request $request, $state, $ac) {
        //ECI getcandidateListpendingatECIPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if ($st_code == '' && $cons_no == '') {
                    $totalContestedCandidate = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.ac.eci.Expenditure.mis-candidatePdfhtml', ['user_data' => $d, 'totalContestedCandidate' => $totalContestedCandidate]);
                return $pdf->download('EcimiscandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.mis-candidatePdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI getcandidateListpendingatECIPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI getcandidateListpendingatECIPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateData By ECI fuction     
     */
    public function filedcandidateData(Request $request, $state, $ac) {
//PC ECI candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                if ($st_code == '0' && $cons_no == '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                // dd($filedData);
                return view('admin.ac.eci.Expenditure.mis-filedcandidate', ['user_data' => $d, 'filedData' => $filedData, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($filedData)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI filedcandidateData TRY CATCH ENDS HERE   
    }

// end filedcandidateData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateDataEXL By ECI fuction     
     */
//ECI getCandidatesmisEXL EXCEL REPORT STARTS
    public function filedcandidateDataEXL(Request $request, $state, $ac) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('ECIFiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if ($st_code == '0' && $cons_no == '0') {
                            $filedData = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $filedData = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $filedData = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($filedData as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            // dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = count($filedData);
                            $data = array(
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'AC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI filedcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI filedcandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateDataPDF By ECI fuction     
     */
    //ECI filedcandidateDataPDF PDF REPORT STARTS

    public function filedcandidateDataPDF(Request $request, $state, $ac) {
        //ECI filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if ($st_code == '0' && $cons_no == '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->orderBy('expenditure_reports.constituency_no')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->orderBy('expenditure_reports.constituency_no')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->orderBy('expenditure_reports.constituency_no')
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.ac.eci.Expenditure.mis-filedcandidatePdfhtml', ['user_data' => $d, 'filedData' => $filedData]);
                return $pdf->download('EcimiscandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.mis-filedcandidatePdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI filedcandidateDataPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notfiledcandidateData By ECI fuction     
     */
    public function notfiledcandidateData(Request $request, $state, $ac) {
        //PC ECI notfiledcandidateData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                $candidate_id = [];
                if ($st_code == '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    // ->where('candidate_nomination_detail.st_code','=',$st_code)
                                    // ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    // ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->get();
                }
                //  dd(DB::getQueryLog());
                return view('admin.ac.eci.Expenditure.mis-notfiledcandidate', ['user_data' => $d, 'pendingCandList' => $pendingCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($pendingCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECInotfiledcandidateData list TRY CATCH ENDS HERE   
    }

// end notfiledcandidateData function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notfiledcandidateDataEXL By ECI fuction     
     */
//ECI notfiledCandidatesmisEXL EXCEL REPORT STARTS
    public function notfiledcandidateDataEXL(Request $request, $state, $ac) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('ECInotfiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
                        $candidate_id = array();
                        if ($st_code == '0' && $cons_no == '0') {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    //->where('expenditure_reports.ST_CODE','=',$st_code)
                                    //->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                            foreach ($startCandList as $startCandListData) {
                                $candidate_id[] = $startCandListData->candidate_id;
                            }
                            $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    // ->where('candidate_nomination_detail.st_code','=',$st_code)
                                    // ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                    ->orderBy('candidate_nomination_detail.ac_no')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    //->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                            foreach ($startCandList as $startCandListData) {
                                $candidate_id[] = $startCandListData->candidate_id;
                            }
                            $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    // ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                    ->orderBy('candidate_nomination_detail.ac_no')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                            foreach ($startCandList as $startCandListData) {
                                $candidate_id[] = $startCandListData->candidate_id;
                            }
                            $pendingCandList = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                    ->orderBy('candidate_nomination_detail.ac_no')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($pendingCandList as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            // dd($candDetails);
                            $acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = count($pendingCandList);
                            $data = array(
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'AC No & Name', 'Candidate Name', 'Party Name'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI notfiledcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI notfiledcandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notfiledcandidateDataPDF By ECI fuction     
     */
    //ECI notfiledcandidateDataPDF PDF REPORT STARTS

    public function notfiledcandidateDataPDF(Request $request, $state, $ac) {
        //ECI notfiledcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                $candidate_id = array();
                if ($st_code == '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            // ->where('candidate_nomination_detail.st_code','=',$st_code)
                            // ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->orderBy('candidate_nomination_detail.ac_no')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            // ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->orderBy('candidate_nomination_detail.ac_no')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $pendingCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->orderBy('candidate_nomination_detail.ac_no')
                            ->get();
                }
                $pdf = PDF::loadView('admin.ac.eci.Expenditure.mis-notfiledcandidatePdfhtml', ['user_data' => $d, 'pendingCandList' => $pendingCandList]);
                return $pdf->download('EcimisnotfiledcandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.mis-notfiledcandidatePdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI notfiledcandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI notfiledcandidateDataPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notintimecandidateData By ECI fuction     
     */
    public function notintimecandidateData(Request $request, $state, $ac) {

        //PC ECI notintimecandidateData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                $notinTime = [];
                if ($st_code == '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.account_lodged_time', 'No')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.account_lodged_time', 'No')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code == '0' && $cons_no != '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.account_lodged_time', 'No')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.mis-notintimecandidate', ['user_data' => $d, 'notinTime' => $notinTime, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($notinTime)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI notintimecandidateData TRY CATCH ENDS HERE   
    }

// end notintimecandidateData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notintimecandidateDataEXL By ECI fuction     
     */
//ECI notintimeCandidatesmisEXL EXCEL REPORT STARTS
    public function notintimecandidateDataEXL(Request $request, $state, $ac) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('ECIFiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if ($st_code == '0' && $cons_no == '0') {
                            $notinTime = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('expenditure_reports.account_lodged_time', 'No')
                                    ->where('expenditure_reports.finalized_status', '=', '1')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $notinTime = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.account_lodged_time', 'No')
                                    ->where('expenditure_reports.finalized_status', '=', '1')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code == '0' && $cons_no != '0') {
                            $notinTime = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    ->where('expenditure_reports.account_lodged_time', 'No')
                                    ->where('expenditure_reports.finalized_status', '=', '1')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($notinTime as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            // dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = count($notinTime);
                            $data = array(
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'AC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI filedcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI filedcandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateDataPDF By ECI fuction     
     */
    //ECI notintimecandidateDataPDF PDF REPORT STARTS

    public function notintimecandidateDataPDF(Request $request, $state, $ac) {
        //ECI filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if ($st_code == '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.account_lodged_time', 'No')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->orderBy('expenditure_reports.constituency_no')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.account_lodged_time', 'No')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->orderBy('expenditure_reports.constituency_no')
                            ->get();
                } elseif ($st_code == '0' && $cons_no != '0') {
                    $notinTime = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('expenditure_reports.account_lodged_time', 'No')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->orderBy('expenditure_reports.constituency_no')
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.ac.eci.Expenditure.mis-notintimecandidatePdfhtml', ['user_data' => $d, 'notinTime' => $notinTime]);
                return $pdf->download('EcimisnotintimecandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.mis-notintimecandidatePdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI notintimecandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI notintimecandidateDataPDF PDF REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return defaultercandidateData By ECI fuction     
     */
    public function defaultercandidateData(Request $request, $state, $ac) {

        //PC ECI defaulter TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            //->where('expenditure_understated.ST_CODE','=',$st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            ->where('expenditure_understated.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.ac.eci.Expenditure.mis-defaultercandidate', ['user_data' => $d, 'defaulterCandList' => $defaulterCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($defaulterCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI defaultercandidateData list TRY CATCH ENDS HERE   
    }

// end defaultercandidateData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return defaultercandidateDataEXL By ECI fuction     
     */
//ECI defaulterCandidatesmisEXL EXCEL REPORT STARTS
    public function defaultercandidateDataEXL(Request $request, $state, $ac) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('ECIdefaulterCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
                        if ($st_code == '0' && $cons_no == '0') {
                            $defaulterCandList = DB::table('expenditure_understated')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                                    ->having('totalobseramnt', '<=', 'totalcandamnt')
                                    //->where('expenditure_understated.ST_CODE','=',$st_code)
                                    // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_understated.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $defaulterCandList = DB::table('expenditure_understated')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                                    ->having('totalobseramnt', '<=', 'totalcandamnt')
                                    ->where('expenditure_understated.ST_CODE', '=', $st_code)
                                    // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_understated.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $defaulterCandList = DB::table('expenditure_understated')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                            DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                                    ->having('totalobseramnt', '<=', 'totalcandamnt')
                                    ->where('expenditure_understated.ST_CODE', '=', $st_code)
                                    ->where('expenditure_understated.constituency_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_understated.candidate_id')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($defaulterCandList as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            // dd($candDetails);
                            $acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = count($defaulterCandList);
                            $data = array(
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME
                            );

                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        // $totalvalues = array('Total',$TotalUsers);
                        // print_r($totalvalues);die;
                        // array_push($arr,$totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'AC No & Name', 'Candidate Name', 'Party Name'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI defaultercandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI defaultercandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 30-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return defaultercandidateDataPDF By ECI fuction     
     */
    //ECI defaultercandidateDataPDF PDF REPORT STARTS

    public function defaultercandidateDataPDF(Request $request, $state, $ac) {
        //ECI filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if ($st_code == '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            //->where('expenditure_understated.ST_CODE','=',$st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            // ->where('expenditure_understated.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $defaulterCandList = DB::table('expenditure_understated')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_understated.candidate_id', 'expenditure_understated.ST_CODE', 'expenditure_understated.constituency_no', 'candidate_personal_detail.cand_name', 'm_party.PARTYNAME', 'candidate_nomination_detail.created_at',
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
                                    DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
                            ->having('totalobseramnt', '<=', 'totalcandamnt')
                            ->where('expenditure_understated.ST_CODE', '=', $st_code)
                            ->where('expenditure_understated.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_understated.candidate_id')
                            ->get();
                }
                $pdf = PDF::loadView('admin.ac.eci.Expenditure.mis-defaultercandidatePdfhtml', ['user_data' => $d, 'defaulterCandList' => $defaulterCandList]);
                return $pdf->download('EcimisdefaultercandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.mis-defaultercandidatePdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //ECI defaultercandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI defaultercandidateDataPDF PDF REPORT FUNCTION ENDS

    ///////Tracking Status by Niraj 15-06-2019////////////////////////
    public function getCandTracking(request $request, $candidate_id) {
        // Get the full URL for the previous request...
        $routesegment=array_slice(explode('/', url()->previous()), -3, 1);

       $html = '';
       if (Auth::check()) {
           $user = Auth::user();
           $d = $this->commonModel->getunewserbyuserid($user->id);
           DB::enableQueryLog();
           $CandidatStatus = DB::table('expenditure_reports')
                   ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                   ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                   ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                   ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                   ->where('candidate_nomination_detail.application_status', '=', '6')
                   ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                   ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                   ->where('expenditure_reports.candidate_id', $candidate_id)
                   ->groupBy('expenditure_reports.candidate_id')
                   ->get();
           // dd(DB::getQueryLog());
           // print_r( $CandidatStatus);
           
           if (($CandidatStatus[0]->date_orginal_acct == '0000-00-00') || empty($CandidatStatus[0]->date_orginal_acct)) {
               $candlogedAcc = 'N/A';
           } else {
               $candlogedAcc = date('d-m-Y', strtotime($CandidatStatus[0]->date_orginal_acct));
           }
           if (($CandidatStatus[0]->date_of_receipt == '0000-00-00') || empty($CandidatStatus[0]->date_of_receipt)) {
               $recieptbyceo = 'N/A';
           } else {
               $recieptbyceo = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_receipt));
           }

           if (($CandidatStatus[0]->final_by_ceo == 1)) {
               $finalbyceo = 'Finalize';
           } else {
               $finalbyceo = 'Not Finalize';
           }
           if (($CandidatStatus[0]->final_by_eci == 1)) {
               $finalbyeci = 'Finalize';
           } else {
               $finalbyeci = 'Not Finalize';
           }
           if ((strtotime($CandidatStatus[0]->date_of_receipt_eci) == 0 || empty($CandidatStatus[0]->date_of_receipt_eci))) {
               $recieptbyeci = 'N/A';
           } else {
               $recieptbyeci = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_receipt_eci));
           }


           ################################Notice Section By Niraj 13-09-2019##################
           if ((strtotime($CandidatStatus[0]->date_of_issuance_notice) == 0 || empty($CandidatStatus[0]->date_of_issuance_notice))) {
               $noticeissuedatebyeci = 'N/A';
           } else {
               $noticeissuedatebyeci = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_issuance_notice));
           }

           if ((strtotime($CandidatStatus[0]->date_of_receipt_notice_service) == 0 || empty($CandidatStatus[0]->date_of_receipt_notice_service))) {
               $noticereceiveddatebyceo = 'N/A';
           } else {
               $noticereceiveddatebyceo = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_receipt_notice_service));
           }

           if ((strtotime($CandidatStatus[0]->date_sending_notice_service_to_deo) == 0 || empty($CandidatStatus[0]->date_sending_notice_service_to_deo))) {
               $noticesendingdateceotodeo = 'N/A';
           } else {
               $noticesendingdateceotodeo = date('d-m-Y', strtotime($CandidatStatus[0]->date_sending_notice_service_to_deo));
           }

           if ((strtotime($CandidatStatus[0]->date_of_receipt_represetation) == 0 || empty($CandidatStatus[0]->date_of_receipt_represetation))) {
               $noticereceiveddatebydeo = 'N/A';
           } else {
               $noticereceiveddatebydeo = date('d-m-Y', strtotime($CandidatStatus[0]->date_of_receipt_represetation));
           }

           
           if ((strtotime($CandidatStatus[0]->date_sending_supplimentary) == 0 || empty($CandidatStatus[0]->date_sending_supplimentary))) {
               $noticereplieddatebydeo = 'N/A';
           } else {
               $noticereplieddatebydeo = date('d-m-Y', strtotime($CandidatStatus[0]->date_sending_supplimentary));
           }

           ################################End Notice Section By Niraj 13-09-2019##################

           $html .= '<div class="scroll-tracks">
          <div class="bs-vertical-wizard">
          <p class="text-left h6 pb-3 pt-4 Orange_text" style="margin-left: -50px;"><strong>Tracking Status :' . $CandidatStatus[0]->cand_name . '</strong></p>
          <div class="clearfix"></div>
              <ul>
                  <li class="complete">
                      <a href="#">
                      <i class="ico ico-green">DEO</i> 									
                      <span>
                          <div class="contentBox">
                              <div class="date h6 text-success"><strong>Finalize:' . date('d-m-Y', strtotime($CandidatStatus[0]->created_at)) . ' </strong></div>
                              <p class="graySquire"> Account Loged By Candidate :' . $candlogedAcc . ' </p>
                              <p class="greenSquire">Scrutiny submit :' . date('d-m-Y', strtotime($CandidatStatus[0]->created_at)) . ' </p>
                              <p class="yellowSquire">Send To CEO :' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_deo)) . ' </p>';	
                             if($routesegment[0]=='noticeatdeo') { 
                               $html .='<p class="yellowSquire">Notice Send by CEO : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>	
                              <p class="yellowSquire">Notice Send by ECI : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>	
                              <p class="yellowSquire">Notice Received : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>	
                              <p class="yellowSquire">Notice Reply : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';
                             }
                             $html .= '</div>							
                      </span>
                      </a>
                      <p class="dateleft">0 - 38&nbspDays</p>									
                      <div class="clearfix"></div>	
                  </li>
  
                  <li class="complete prev-step">
                      <a href="#"> 
                      <i class="ico ico-green">CEO</i>
                          <span class="desc">	
                          <div class="contentBox">
                              <div class="date h6 text-success"><strong>Finalize: ' . $recieptbyeci . '</strong></div>
                              <p class="graySquire"> Received: ' . $recieptbyceo . '</p>
                              <p class="greenSquire">Action : ' . $finalbyceo . '</p>
                              <p class="yellowSquire">Send to ECI : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';		
                              if($routesegment[0]=='noticeatceo') { 
                               $html .='<p class="yellowSquire">Notice Received : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>	
                              <p class="yellowSquire">Notice Send to DEO : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';	
                              }
                              $html .='</div>
                          </span>
                      </a>
                      <p class="dateleft">0 - 45&nbspDays</p>
                  </li>								
                  <li class="current">
                      <a href="#">
                      <i class="ico ico-green">ECI</i> 
                          <span class="desc">										
                              <div class="contentBox">
                              <div class="date h6 text-warning"><strong>Finalize : ' . $recieptbyeci . '</strong></div>
                              <p class="graySquire"> Received: ' . $recieptbyeci . '</p>
                              <p class="greenSquire">Action :' . $finalbyeci . '</p>
                              <p class="yellowSquire">Action Date : ' . $recieptbyeci . '</p>';	
                             if($noticeissuedatebyeci !='N/A'){
                               $html .='<p class="yellowSquire">Notice Issued : ' . date('d-m-Y', strtotime($CandidatStatus[0]->date_of_sending_ceo)) . '</p>';	
                             }
                             $html .='</div>								
                          </span>										
                      </a>
                  </li>
              </ul>
          </div>
          </div>
          </div>
      </div>';
       }

       return $html;
   }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 18-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return finalcandlistECI By ECI fuction     
     */
    public function getcandidateListfinalbyECI(Request $request, $state, $ac) {
        //PC ROPC getcandidateListfinalbyECI TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                #####################################################
                $query=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                           ->where(function($q) {
                           $q->whereNull('expenditure_reports.final_action')
                            ->orWhere('expenditure_reports.final_action','=','');
                          });
                if ($st_code != '0' && $cons_no == '0') {
                    $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                    
                } elseif ($st_code != '0' && $cons_no != '0') {
                     $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                      $query->where('expenditure_reports.constituency_no', '=', $cons_no);
                }
                $getcandidateListfinalbyECI=$query->get();
                #####################################################
                if ($st_code == '0' && $cons_no == '0') {
                    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci', '1')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                            })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci', '1')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                            })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_eci', '1')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action', 'Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                            })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                //dd($getcandidateListfinalbyECI);
                return view('admin.ac.eci.Expenditure.finalbyeci-mis', ['user_data' => $d, 'getcandidateListfinalbyECI' => $getcandidateListfinalbyECI, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($getcandidateListfinalbyECI)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getcandidateListfinalbyECI TRY CATCH ENDS HERE 
    }

// end getcandidateListfinalbyECI start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 18-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param returngetcandidateListfinalbyECIEXL By ECI fuction     
     */
    //ECI getcandidateListpendingatECIEXL EXCEL REPORT STARTS
    public function getcandidateListfinalbyECIEXL(Request $request, $state, $ac) {
        //ECI getcandidateListpendingatECIEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                // dd($totalContestedCandidate);

                $cur_time = Carbon::now();

                \Excel::create('ECIFinalCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if ($st_code == '0' && $cons_no == '0') {
                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_eci', '1')
                                    ->where(function($q) {
                                        $q->where('expenditure_reports.final_action', 'Closed')
                                        ->orWhere('expenditure_reports.final_action', 'Disqualified')
                                        ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                                    })
                                    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    // ->join('expenditure_notification', 'expenditure_notification.candidate_id', '=', 'expenditure_reports.candidate_id') 
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_eci', '1')
                                    ->where(function($q) {
                                        $q->where('expenditure_reports.final_action', 'Closed')
                                        ->orWhere('expenditure_reports.final_action', 'Disqualified')
                                        ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                                    })
                                    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_eci', '1')
                                    ->where(function($q) {
                                        $q->where('expenditure_reports.final_action', 'Closed')
                                        ->orWhere('expenditure_reports.final_action', 'Disqualified')
                                        ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                                    })
                                    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($getcandidateListfinalbyECI as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                             $lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
//echo $date->format('d.m.Y'); // 31.07.2012
$lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012

$scrutinysubmit = new DateTime($candDetails->report_submitted_date);
$scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012
//$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
$candidatelodging = new DateTime($candDetails->date_orginal_acct);
$candidatelodgingdate = $candidatelodging->format('d-m-Y'); // 31-07-2012

$sendingdatetodeo = new DateTime($candDetails->date_of_sending_deo);
$deosendingdate = $sendingdatetodeo->format('d-m-Y'); // 

$sendingdatetoceo = new DateTime($candDetails->date_of_sending_ceo);
$ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012

$ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
$ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012

// $lodgingDate =!empty($lodgingDate) ? $lodgingDate : '22-06-2019';

$lodgingDate =$lodgingDate ?? 'N/A';
$scrutinyreportsubmitdate = (!empty($scrutinyreportsubmitdate) && $scrutinyreportsubmitdate !='30-11--0001') ? $scrutinyreportsubmitdate : 'N/A';
$candidatelodgingdate = (!empty($candidatelodgingdate) && $candidatelodgingdate !='30-11--0001') ? $candidatelodgingdate : 'N/A' ;
$deosendingdate = (!empty($deosendingdate) && $deosendingdate !='30-11--0001') ? $deosendingdate : 'N/A' ; 
$ceosendingdate = (!empty($ceosendingdate) && $ceosendingdate !='30-11--0001') ? $ceosendingdate : 'N/A' ; 
$ceoreceivedate = (!empty($ceoreceivedate) && $ceoreceivedate !='30-11--0001') ? $ceoreceivedate : 'N/A' ; 
#############################################
$data = array(
$acDetails->AC_NO . '-' . $acDetails->AC_NAME,
$candDetails->cand_name,
$candDetails->PARTYNAME,
$lastdate,
$scrutinyreportsubmitdate,
$candidatelodgingdate,
$deosendingdate,
$ceosendingdate,
$ceoreceivedate
);
                            $TotalUsers = count($getcandidateListfinalbyECI);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                         $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
					'AC No & Name', 'Candidate Name', 'Party Name', 'Last Date of Submission','Date of Scrutiny Report Submission','Date Of Lodging A/C By Candidate','Date of Sending to the DEO','Date of Sending to the CEO','Date of Receipt By CEO'
					)
					);
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getcandidateListpendingatECIEXL EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI getcandidateListpendingatECIEXL EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 02-07-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return Ecistartedcandidate By ECI fuction     
     */
    public function Ecistartedcandidate(Request $request, $state, $ac) {
        //PC ECI Ecistartedcandidate TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                if ($st_code == '0' && $cons_no == '0') {
                    $Ecistartedcandidate = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $Ecistartedcandidate = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $Ecistartedcandidate = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                // dd($filedData);
                return view('admin.ac.eci.Expenditure.mis-startedcandidate', ['user_data' => $d, 'Ecistartedcandidate' => $Ecistartedcandidate, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($Ecistartedcandidate)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI Ecistartedcandidate TRY CATCH ENDS HERE   
    }

    // end Ecistartedcandidate start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 02-07-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return Ecinotstarted By ECI fuction     
     */
    public function Ecinotstarted(Request $request, $state, $ac) {
        //PC ECI notfiledcandidateData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                $candidate_id = [];
                if ($st_code == '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            //->where('expenditure_reports.ST_CODE','=',$st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $Ecinotstarted = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    // ->where('candidate_nomination_detail.st_code','=',$st_code)
                                    // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            //->where('expenditure_reports.constituency_no','=',$cons_no) 
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $Ecinotstarted = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    // ->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $Ecinotstarted = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.pc_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->get();
                }
                //  dd(DB::getQueryLog());
                return view('admin.ac.eci.Expenditure.mis-notstartedcandidate', ['user_data' => $d, 'Ecinotstarted' => $Ecinotstarted, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($Ecinotstarted)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC Ecinotstarted list TRY CATCH ENDS HERE   
    }

// end Ecinotstarted function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 02-07-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return EcifinalbyDEO By ECI fuction     
     */
    public function EcifinalbyDEO(Request $request, $state, $ac) {
        //PC ECI EcifinalbyDEO TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if ($st_code == '0' && $cons_no == '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $EcifinalbyDEO = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                // dd(DB::getQueryLog());
                return view('admin.ac.eci.Expenditure.finalbydeo-mis', ['user_data' => $d, 'EcifinalbyDEO' => $EcifinalbyDEO, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($EcifinalbyDEO)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI EcifinalbyDEO TRY CATCH ENDS HERE   
    }

// end getcandidateListpendingatRO function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 02-07-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return EcifinalbyDEOMISEXL By ECI fuction     
     */
//ECI EcifinalbyDEOMISEXL EXCEL REPORT STARTS
    public function EcifinalbyDEOMISEXL(Request $request, $state, $ac) {
//ECI getcandidateListpendingatROEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                //echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('ECIPendingatDEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if ($st_code == '0' && $cons_no == '0') {
                            $EcifinalbyDEOMISEXL = DB::table('expenditure_reports')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_ro', '1')
                                    ->where('expenditure_reports.finalized_status', '1')
                                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no == '0') {
                            $EcifinalbyDEOMISEXL = DB::table('expenditure_reports')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    // ->where('expenditure_reports.constituency_no','=',$cons_no) 
                                    //->where('expenditure_notification.deo_action','0')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_ro', '1')
                                    ->where('expenditure_reports.finalized_status', '1')
                                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $EcifinalbyDEOMISEXL = DB::table('expenditure_reports')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                                    //->where('expenditure_notification.deo_action','0')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->where('expenditure_reports.final_by_ro', '1')
                                    ->where('expenditure_reports.finalized_status', '1')
                                    ->whereNotNull('expenditure_reports.date_of_sending_deo')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($EcifinalbyDEOMISEXL as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $ecireceiveddate = new DateTime($candDetails->date_of_receipt_eci);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $ecireceiveddate = $ecireceiveddate->format('d-m-Y'); // 31-07-2012
							 $lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
				 //echo $date->format('d.m.Y'); // 31.07.2012
				 $lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012
				
				$scrutinysubmit = new DateTime($candDetails->report_submitted_date);
				$scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012
				
			 if( !empty($candDetails->date_orginal_acct) && isset($candDetails->date_orginal_acct) && strtotime($candDetails->date_orginal_acct) > 0){
                      $candidatelodging = new DateTime($candDetails->date_orginal_acct);
				      $candidatelodgingdate = $candidatelodging->format('d-m-Y'); // 31-07-2012
					
				 }else { echo 'N/A'; }
			  
				
				$sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
				$ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012
		
				$ceoreceiveddate = new DateTime($candDetails->date_of_receipt);
				$ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012
				
			   // $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
				
				  $lodgingDate =$lodgingDate ??  '22-06-2019';
				  $scrutinyreportsubmitdate =$scrutinyreportsubmitdate ??  'N/A';
				  $candidatelodgingdate =$candidatelodgingdate ??  'N/A';
				  $ceosendingdate =$ceosendingdate ??  'N/A';
				  $ceoreceivedate =$ceoreceivedate ??  'N/A';
                  $ecireceiveddate =$ecireceiveddate ??  'N/A';
                 
                            $data = array(
                                $st->ST_NAME,
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate,
								$scrutinyreportsubmitdate,
								$candidatelodgingdate,
								$ceosendingdate,
								$ceoreceivedate
                            );
                            $TotalUsers = count($EcifinalbyDEOMISEXL);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State','AC No & Name', 'Candidate Name', 'Party Name', 'Last Date Of Lodging','Date Of Scrutiny Report Submission','Date Of Lodging A/C By Candidates','Date Of Sending To CEO','Date Of Received By CEO'
                            )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getcandidateListpendingatROPDF EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI EcifinalbyDEOMISPDF EXCEL REPORT FUNCTION ENDS
    //ECI EcifinalbyDEOMISPDF PDF REPORT STARTS
    public function EcifinalbyDEOMISPDF(Request $request, $state, $ac) {
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if ($st_code == '0' && $cons_no == '0') {
                    $EcifinalbyDEOMISPDF = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $EcifinalbyDEOMISPDF = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $EcifinalbyDEOMISPDF = DB::table('expenditure_reports')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            //->where('expenditure_notification.deo_action','0')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.final_by_ro', '1')
                            ->where('expenditure_reports.finalized_status', '1')
                            ->whereNotNull('expenditure_reports.date_of_sending_deo')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }
                $pdf = PDF::loadView('admin.ac.eci.Expenditure.finalbyDEOPDFhtml', ['user_data' => $d, 'pendingatDEOCandList' => $EcifinalbyDEOMISPDF]);
                return $pdf->download('EcifinalbyDEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.finalbyDEOPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
//ECI EcifinalbyDEOMISPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECI EcifinalbyDEOMISPDF PDF REPORT FUNCTION ENDS
#################################End MIS Report by Niraj##############################
    #################################Start Report Section By Niraj 13-06-2019#####################################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 13-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersreport By ECI fuction     
     */
    public function getOfficersreport(Request $request) {
        //dd($request->all());
        //PC ECI getOfficersmis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $st_code = $request->input('state');
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################

                $cons_no = $request->input('ac');
                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';
                $totalContestedCandidatedata='';
                // echo  $st_code.'pc'.$cons_no; die;
                // DB::enableQueryLog();
                if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (($st_code == '' && $cons_no == '') || ($st_code == '0' && $cons_no == '0')) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }
                // dd(DB::getQueryLog());
                // dd($totalContestedCandidatedata);
                return view('admin.ac.eci.Expenditure.report-officer', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code, 'statelist' => $statelist, 'count' => count($totalContestedCandidatedata)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getOfficersmis TRY CATCH ENDS HERE    
    }

// end getOfficersmis function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 14-06--19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficers report By ECI fuction     
     */
//ECI getOfficers EXCEL REPORT STARTS
public function getOfficersreportEXL(Request $request, $state, $ac) {
//ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('EciOfficerReportExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no, $permitstates) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no, $permitstates) {
                        if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        } else if ($st_code == '' && $cons_no == '') {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                    ->groupBy("candidate_nomination_detail.st_code")
                                    ->get();
                        }
                        $arr = array();
                        $TotalUsers = 0;
                        $TotalfiledData = 0;
                        $TotalnotfiledData = 0;
                        $Totalfinalcompletedcount = 0;
                        $Totalac = 0;

                        $user = Auth::user();
                        $count = 1;
                        foreach ($totalContestedCandidatedata as $key => $listdata) {
                            //get filedcount data entry start data count
                            $filedcount = $this->eciexpenditureModel->gettotaldataentryStart('AC', $listdata->st_code, $cons_no);
                            // Get Pending Data Count 
                            $notfiledcount = $listdata->totalcandidate - $filedcount;

                            //Get Data entry finalize Count 
                            $finalcompletedcount = $this->eciexpenditureModel->gettotalCompletedbyEci('AC', $listdata->st_code, $cons_no);

                            $stdetails = getstatebystatecode($listdata->st_code);
                            $acbystate = getacbystate($listdata->st_code);
                            $account = count($acbystate);
                            $Totalac += $account;

                            $TotalUsers += $listdata->totalcandidate;
                            $TotalfiledData += $filedcount;
                            $TotalnotfiledData += $notfiledcount;
                            $Totalfinalcompletedcount += $finalcompletedcount;

                            $filedcount = !empty($filedcount) ? $filedcount : '0';
                            $notfiledcount = !empty($notfiledcount) ? $notfiledcount : '0';
                            $finalcompletedcount = !empty($finalcompletedcount) ? $finalcompletedcount : '0';

                            $data = array(
                                $stdetails->ST_NAME,
                                $account,
                                $listdata->totalcandidate,
                                $finalcompletedcount,
                                $filedcount,
                                $notfiledcount
                            );
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $Totalac, $TotalUsers, $Totalfinalcompletedcount, $TotalfiledData, $TotalnotfiledData);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State Name', 'Total AC', 'Total Candidate', 'Completed', 'InProgress', 'NotStarted'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getOfficersmisEXL EXCEL REPORT TRY CATCH BLOCK ENDS
    }

//ECI getOfficersreport USERS EXCEL REPORT FUNCTION ENDS
    //ECI getOfficersmis PDF REPORT STARTS
    public function getOfficersreportPDF(Request $request, $state, $ac) {
        //ECI getOfficersmisPdf PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';
                $totalContestedCandidatedata='';
                $cur_time = Carbon::now();
                if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if ($st_code == '' && $cons_no == '') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                }

                //dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.ac.eci.Expenditure.report-officerPDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata]);
                return $pdf->download('EciOfficerReportPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.report-officerPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECIgetOfficersreportPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//ECIgetOfficersreportPDF PDF REPORT FUNCTION ENDS

	

/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 30-12-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getbreachAmntMis on expenditure By ECI fuction     
 */
public function getbreachAmntMis(Request $request)
   {  
    //dd($request->all());
      DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
             $cur_time = Carbon::now();
            $conditions="";
            if(!empty($_GET['state'])){
            $st_code = $_GET['state'];
           // $conditions .=" and cnd.st_code='$st_code' ";
              }
       
            if(!empty($_GET['ac'])){
            $cons_no = $_GET['ac'];
           // $conditions .=" and cnd.pc_no='$pc' ";
              }  

            $returnType=$request->input('returnType');
          if($returnType=='return'){
            $returnType = 'Returned';
            }elseif ($returnType=='non-return') {
              $returnType = 'Non-Returned';
            } 

             ##########Code For State Wise Access By Niraj date 23-07-2019################
            $username=$user->officername;
            $st_code = $request->input('state');
              $zonestate = $this->eciexpenditureModel->getzonestate($username);
              if($zonestate->isEmpty()){
                $permitstates = '';
              }else{
                $permitstates = explode(',',$zonestate[0]->assign_state);
              }
            
              $permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;
            
                if(!empty($permitstate)){
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                }else{
                   $statelist = $this->commonModel->getallstate();
                }
                if(!empty($st_code)){
                    $st_code=$st_code;
                }elseif(empty($st_code) && !empty($permitstate)){
                    $st_code=array_values($permitstate)[0];
                }else {
                    $st_code=0;
                }
                $cons_no = $request->input('ac');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $returnType = !empty($returnType) ?  $returnType : 0;
               // echo 'st_code'.$st_code.'cons_no'.$cons_no.'returnType'.$returnType; die('test');
             ###################Code For State Wise Access#####################
//  dd($conditions);

$query = DB::table('candidate_nomination_detail')
->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
->select('candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_id','candidate_nomination_detail.party_id', DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
->where('candidate_nomination_detail.application_status', '=', '6')
->where('candidate_nomination_detail.finalaccepted', '=', '1')
->where('candidate_nomination_detail.symbol_id', '<>', '200')
->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');

if(!empty($st_code) && empty($cons_no)) {
  $query->where('candidate_nomination_detail.st_code', '=', $st_code);
  $query->groupBy('candidate_nomination_detail.st_code');  
  }else if (!empty($st_code) && !empty($cons_no)) {
  $query->where('candidate_nomination_detail.st_code', '=', $st_code);
  $query->where('candidate_nomination_detail.ac_no', '=', $cons_no);
   $query->groupBy('candidate_nomination_detail.st_code');  
}else if (empty($st_code) && empty($cons_no)) {
 $query->groupBy('candidate_nomination_detail.st_code');  
} 
$candList=$query->get();
//dd(DB::getQueryLog());
//$count=!empty($candList)?count($candList):0;

//  dd(DB::getQueryLog());
if(!empty($_GET['pdf']) && $_GET['pdf']="yes"){ 
    ////// code for pdf generation//////
$pdf = PDF::loadView('admin.ac.eci.Expenditure.misbreach-reportPDFhtml', ['user_data' => $d, 'candList' => $candList,'st_code' => $st_code,'cons_no' => $cons_no]);
return $pdf->download('BreachingAmntReportPdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf'); 
return view('admin.ac.eci.Expenditure.misbreach-reportPDFhtml');  
}
elseif (!empty($_GET['exl']) && $_GET['exl']="yes") {
  //////////export exel //////////////
// Initialize the array which will be passed into the Excel
// generator.
$candidateArray = []; 

// Define the Excel spreadsheet headers
$candidateArray[] = ['S.NO','STATE NAME','TOTAL AC','Total Candidates','Total Candidates Whos Expenditure is Breaching','Total Candidates Without Breaching Amount'];

// Convert each member of the returned collection into an array,
// and append it to the payments array.
$i=1;
foreach ($candList as $canwise) { 
    $breachcount=$this->expenditureModel->gettotalbreaching('AC',$canwise->st_code,$cons_no);
    $breachcount=$breachcount[0]->breachcount;
	 //without breaching amount
		  if($breachcount >= 0 ){
			$withoutBreach=$canwise->totalcandidate-($breachcount);
			}  
    
$acdetails=getacbyacno($canwise->st_code,$canwise->ac_no);  
$st=getstatebystatecode($canwise->st_code);
  $acbystate=getacbystate($canwise->st_code);
   $account=count($acbystate);
   $total_candidate=!empty($canwise->totalcandidate) ? $canwise->totalcandidate : 0;
  $candidateArr[$i]['S.no'] = $i;
  $candidateArr[$i]['state_name'] = $st->ST_NAME;
  $candidateArr[$i]['ac_no'] = $account;
  $candidateArr[$i]['total_candidate'] = $total_candidate;
  $candidateArr[$i]['breachCandidate'] =!empty($breachcount) ? $breachcount : '0';
  $candidateArr[$i]['withoutBreach'] =!empty($withoutBreach) ? $withoutBreach : '0';
  $i++;
}

foreach ($candidateArr as $candidate) {
       $candidateArray[] = $candidate;
       }

               // Generate and return the spreadsheet
                \Excel::create('MisBreachingReport', function($excel) use ($candidateArray) {
                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Mis Breaching Report');
                    $excel->setCreator('Eci')->setCompany('Election Commission Of India');
                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('MisBreachingReport', function($sheet) use ($candidateArray) {
                        $sheet->fromArray($candidateArray, null, 'A1', false, false);
                    });
                    })->download('csv');
                 }
                 else
                 {
                   return view('admin.ac.eci.Expenditure.misbreach-report', ['user_data' => $d, 'ele_details' => $ele_details, 'candList' => $candList,'statelist'=>$statelist,'st_code' => $st_code,'cons_no' => $cons_no]);
                 }
            // dd(DB::getQueryLog());
          // dd($candList);
            
        } else {
            return redirect('/officer-login');
        }
  } // end breaching amount mis
/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-12-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getbreachAmnt on expenditure By ECI fuction     
 */
public function getbreachAmnt(Request $request ,$state, $ac) {
        //ECI getbreachAmnt TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;  
    //dd($request->all());
     
            $returnType=$request->input('returnType');
          if($returnType=='return'){
            $returnType = 'Returned';
            }elseif ($returnType=='non-return') {
              $returnType = 'Non-Returned';
            } 
 $cur_time = Carbon::now();
            
//  dd($conditions);
$query = DB::table('expenditure_reports')
->join(DB::raw('(SELECT * FROM expenditure_understated  GROUP BY date_understated, expenditure_type,amt_understated_by_candidate,candidate_id ORDER BY candidate_id)
               resultunderstated'), 
        function($join)
        {
           $join->on('resultunderstated.candidate_id', '=', 'expenditure_reports.candidate_id');
        })
->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
->select(DB::raw('YEAR(STR_TO_DATE(resultunderstated.date_understated, "%m/%d/%Y")) AS YEAR'),'expenditure_reports.election_type','expenditure_reports.constituency_no','resultunderstated.ST_CODE','candidate_personal_detail.cand_hname','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_id','expenditure_reports.finalized_status','expenditure_reports.updated_at as finalized_date','expenditure_reports.final_by_ro','expenditure_reports.date_of_declaration','expenditure_reports.grand_total_election_exp_by_cadidate',DB::raw('SUM(resultunderstated.amt_as_per_observation) as amt_as_per_observation'),DB::raw('SUM(resultunderstated.amt_understated_by_candidate) as amt_understated_by_candidate'))
->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');
//->where('expenditure_reports.finalized_status', '=', '1')
//->where('expenditure_reports.final_by_ro', '=', '1');

if(!empty($st_code) && empty($cons_no) && empty($returnType)) {
$query->where('expenditure_reports.ST_CODE', '=', $st_code);
 
  }else if (!empty($st_code) && empty($cons_no) && !empty($returnType)) {
$query->where('expenditure_reports.return_status', '=', $returnType); 
$query->where('expenditure_reports.ST_CODE', '=', $st_code);
 
} else if (!empty($st_code) && !empty($cons_no) && empty($returnType)) {
  $query->where('expenditure_reports.ST_CODE', '=', $st_code);
  $query->where('expenditure_reports.constituency_no', '=', $cons_no);
  
}else if (!empty($st_code) && !empty($cons_no) && !empty($returnType)) {
  $query->where('expenditure_reports.return_status', '=', $returnType) ;
  $query->where('expenditure_reports.ST_CODE', '=', $st_code);
  $query->where('expenditure_reports.constituency_no', '=', $cons_no); 
}  
$query->groupBy('resultunderstated.candidate_id');
$candList=$query->get();
//dd(DB::getQueryLog());

if(!empty($_GET['pdf']) && $_GET['pdf']="yes"){
    ////// code for pdf generation//////
$pdf = PDF::loadView('admin.ac.eci.Expenditure.breach-reportPdfhtml', ['user_data' => $d, 'candList' => $candList]);
return $pdf->download('BreachingAmntReportPdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf'); 
return view('admin.ac.eci.Expenditure.breach-reportPdfhtml');  
}
elseif (!empty($_GET['exl']) && $_GET['exl']="yes") {
  //////////export exel //////////////
// Initialize the array which will be passed into the Excel
// generator.
$candidateArray = []; 

// Define the Excel spreadsheet headers
$candidateArray[] = ['S.NO','CANDIDATE NAME', 'STATE NAME','AC NO & AC NAME','YEAR','ELECTION TYPE','TOTAL EXPENDITURE DECLARED BY CANDIDATE(Rs.)','TOTAL EXPENDITURE ASSESSED BY DEO(Rs.)','TOTAL BREACHING AMOUNT(Rs.)'];

// Convert each member of the returned collection into an array,
// and append it to the payments array.
$i=1;
$grandTotal = 0;
$grandTotalAssessbyDEO=0;
$avgTotalbycand=0;
$avgbyAssessbyDEO=0;
$grandTotalBreachAmnt=0;
$count=1;
foreach ($candList as $canwise) {
		$candidate_id=$canwise->candidate_id;
		//$candUnderStatasDetails=$this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
		$totalamntassesbyDEO=$canwise->amt_as_per_observation;
		 $grandTotalAssessbyDEO += $totalamntassesbyDEO;
		$totalamount = !empty($canwise->grand_total_election_exp_by_cadidate)? $canwise->grand_total_election_exp_by_cadidate : 0; 
		$grandTotal += $totalamount;
		$BreachAmnt=0;
		if(!empty($totalamntassesbyDEO) && ($totalamount != $totalamntassesbyDEO)){ 
		$BreachAmnt=$totalamntassesbyDEO-$totalamount;
		}
		if(!empty($BreachAmnt) && $BreachAmnt > 0){
		$BreachAmnt = '+'.$BreachAmnt;
		}elseif(!empty($BreachAmnt) && $BreachAmnt < 0){
		$BreachAmnt = $BreachAmnt;
		}else{
		$BreachAmnt = 0;
		}


$acdetails=getacbyacno($canwise->ST_CODE,$canwise->constituency_no); 
$st=getstatebystatecode($canwise->ST_CODE);
  $candidateArr[$i]['S.no'] = $i;
  $candidateArr[$i]['cand_name'] = $canwise->cand_name;
  $candidateArr[$i]['state_name'] = $st->ST_NAME;
  $candidateArr[$i]['ac_no'] = !empty($acdetails) ? $acdetails->AC_NO.' - '.$acdetails->AC_NAME : '0';
  $candidateArr[$i]['year'] = $canwise->YEAR;
  $candidateArr[$i]['election_type'] = $canwise->election_type;
  $candidateArr[$i]['grand_total_election_exp_by_cadidate'] =!empty($canwise->grand_total_election_exp_by_cadidate) ? $canwise->grand_total_election_exp_by_cadidate : 0;
  $candidateArr[$i]['grand_total_assessed_by_deo'] =!empty($totalamntassesbyDEO) ? $totalamntassesbyDEO : 0;
  $candidateArr[$i]['BreachAmnt'] =!empty($BreachAmnt) ? $BreachAmnt : 0;
 // $candidateArr[$i]['total_expenditure'] = $this->expenditureModel->getcandidatetotalexpenditure($canwise->candidate_id);
 // $candidateArr[$i]['total_expenditure'] = !empty($candidateArr[$i]['total_expenditure']) ? 'Rs. '.$candidateArr[$i]['total_expenditure']:0;
  $i++;
}

foreach ($candidateArr as $candidate) {
       $candidateArray[] = $candidate;
       }

               // Generate and return the spreadsheet
                \Excel::create('CandidateWiseBreachingReport', function($excel) use ($candidateArray) {
                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Candidate Wise Expenditure');
                    $excel->setCreator('Eci')->setCompany('Election Commission Of India');
                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('CandidateWiseExpenditure', function($sheet) use ($candidateArray) {
                        $sheet->fromArray($candidateArray, null, 'A1', false, false);
                    });
                    })->download('csv');
                 }
                 else
                 {
                   return view('admin.ac.eci.Expenditure.breach-report', ['user_data' => $d, 'ele_details' => $ele_details, 'candList' => $candList,"cons_no"=>$cons_no,"st_code"=>$st_code]);
                 }
            // dd(DB::getQueryLog());
          // dd($candList);
            
        } else {
            return redirect('/officer-login');
        }
      } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//PC ECI breaching report TRY CATCH ENDS HERE   
  } // end breaching report


    ############################################End Report Section ######################
########################End status dashboard by Niraj 16-05-2019 #####################
###############################Notice CEO & DEO 23-06-2019 Start By Niraj######################################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-06--19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getnoticeatCEO By ECI fuction     
     */
    public function getnoticeatCEO(Request $request, $state, $ac) {
        //PC ECI getnoticeatCEO TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                $query=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                           // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                           // ->where('expenditure_reports.final_by_ro', '0')
                            //->whereNotNull('expenditure_reports.date_of_issuance_notice')
                             ->whereIn('expenditure_reports.date_sending_notice_service_to_deo',['0000-00-00',''])
                           ->where('expenditure_reports.date_of_issuance_notice' ,'<>','0000-00-00')
                           ->where('expenditure_reports.date_of_issuance_notice' ,'<>','')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            });

                if ($st_code == '0' && $cons_no == '0') {
                $query->groupBy('expenditure_reports.candidate_id');
                $noticeatCEO = $query->get();

                } elseif ($st_code != '0' && $cons_no == '0') {
                $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                $query->groupBy('expenditure_reports.candidate_id');
                $noticeatCEO = $query->get();

                } elseif ($st_code != '0' && $cons_no != '0') {
                $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                $query->where('expenditure_reports.constituency_no', '=', $cons_no);
                $query->groupBy('expenditure_reports.candidate_id');
                $noticeatCEO = $query->get();
                }
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.noticeatceo', ['user_data' => $d, 'noticeatCEO' => $noticeatCEO, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($noticeatCEO)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE
    }

// end candidateListByfinalizeData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getnoticeatCEOEXL By ECI fuction     
     */
    //ECI getnoticeatCEOEXL EXCEL REPORT STARTS
    public function getnoticeatCEOEXL(Request $request, $state, $ac) {
        //ECI getnoticeatCEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();

                \Excel::create('ECINoticeatCEOCandidate_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        $query=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                           // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereIn('expenditure_reports.date_sending_notice_service_to_deo',['0000-00-00',''])
                           ->where('expenditure_reports.date_of_issuance_notice' ,'<>','0000-00-00')
                           ->where('expenditure_reports.date_of_issuance_notice' ,'<>','')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            });
                if ($st_code == '0' && $cons_no == '0') {
                $query->groupBy('expenditure_reports.candidate_id');
                $noticeatCEO = $query->get();

                } elseif ($st_code != '0' && $cons_no == '0') {
                $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                $query->groupBy('expenditure_reports.candidate_id');
                $noticeatCEO = $query->get();

                } elseif ($st_code != '0' && $cons_no != '0') {
                $query->where('expenditure_reports.ST_CODE', '=', $st_code);
                $query->where('expenditure_reports.constituency_no', '=', $cons_no);
                $query->groupBy('expenditure_reports.candidate_id');
                $noticeatCEO = $query->get();
                }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($noticeatCEO as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
                            $lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012
                           
                           $scrutinysubmit = new DateTime($candDetails->report_submitted_date);
                            $scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012
                           //$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
                           
                           $candidatelodgingdata = new DateTime($candDetails->date_orginal_acct);
                           $candidatelodgingdate = $candidatelodgingdata->format('d-m-Y'); // 31-07-2012
                           
                           $sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
                           $ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012
                   
                           $ceoreceiveddate = new DateTime($candDetails->date_sending_notice_service_to_deo);
                           $ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012
                          // $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                           
                             $lodgingDate =$lodgingDate ??  '22-06-2019';
                             $scrutinyreportsubmitdate = !empty($scrutinyreportsubmitdate && $scrutinyreportsubmitdate !='30-11--0001')  ?  $scrutinyreportsubmitdate : 'N/A';
                             $candidatelodgingdate =  !empty($candidatelodgingdate && $candidatelodgingdate !='30-11--0001')  ?  $candidatelodgingdate : 'N/A' ;
                             $ceosendingdate =  !empty($ceosendingdate && $ceosendingdate !='30-11--0001')  ?  $ceosendingdate : 'N/A' ; 
                             $ceoreceivedate = !empty($ceoreceivedate && $ceoreceivedate !='30-11--0001')  ?  $ceoreceivedate : 'N/A' ; 
 
                            $data = array(
                                $st->ST_NAME,
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate,
                                $scrutinyreportsubmitdate,
                                $candidatelodgingdate,
                                $ceosendingdate,
                                $ceoreceivedate
                            );
                            $TotalUsers = count($noticeatCEO);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State', 'AC No & Name', 'Candidate Name', 'Party Name','Last Date Of Lodging', 'Date Of Submit Scrutiny Report','Date of Lodging A/C By Candidate','Date Of Sending To CEO','Receiving Date Of CEO'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getnoticeatDEO By ECI fuction     
     */
    public function getnoticeatDEO(Request $request, $state, $ac) {
        //PC ECI getcandidateListpendingatCEO TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                  // Get the current URL without the query string...
        // echo $namePrefix = \Route::current()->action['prefix'];
         //$segments = explode('/', $_SERVER['REQUEST_URI']);
      // echo   $nameSuffix = $segments['2'];
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                $query=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                           // ->where('expenditure_reports.ST_CODE', '=', $st_code)
                           // ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->where('expenditure_reports.final_by_ceo', '0')
                            //->where('expenditure_reports.final_by_ro', '0')
                            //->whereNotNull('expenditure_reports.date_sending_notice_service_to_deo')
                             ->where('date_sending_notice_service_to_deo' ,'<>','0000-00-00')
                             ->where('date_sending_notice_service_to_deo' ,'<>','')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            });
        if ($st_code == '0' && $cons_no == '0') {
            $query->groupBy('expenditure_reports.candidate_id');
            $noticeatDEO = $query->get();
        } elseif ($st_code != '0' && $cons_no == '0') {
            $query->where('expenditure_reports.ST_CODE', '=', $st_code);
            $query->groupBy('expenditure_reports.candidate_id');
            $noticeatDEO = $query->get();
            
        } elseif ($st_code != '0' && $cons_no != '0') {
            $query->where('expenditure_reports.ST_CODE', '=', $st_code);
            $query ->where('expenditure_reports.constituency_no', '=', $cons_no);
            $query->groupBy('expenditure_reports.candidate_id');
            $noticeatDEO = $query->get();
        }
                //dd($DataentryStartCandList);
                return view('admin.ac.eci.Expenditure.noticeatdeo', ['user_data' => $d, 'noticeatDEO' => $noticeatDEO, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($noticeatDEO)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE     
    }

// end candidateListByfinalizeData start function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getnoticeatDEOEXL By ECI fuction     
     */
    //ECI getnoticeatDEOEXL EXCEL REPORT STARTS
    public function getnoticeatDEOEXL(Request $request, $state, $ac) {
        //ECI getnoticeatDEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();

                \Excel::create('ECINoticeatDEOCandidate_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                         $query=DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('date_sending_notice_service_to_deo' ,'<>','0000-00-00')
                           ->where('date_sending_notice_service_to_deo' ,'<>','')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', '=', 'Notice Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Reply Issued')
                                ->orWhere('expenditure_reports.final_action', '=', 'Hearing Done');
                            });
        if ($st_code == '0' && $cons_no == '0') {
            $query->groupBy('expenditure_reports.candidate_id');
            $noticeatDEO = $query->get();
        } elseif ($st_code != '0' && $cons_no == '0') {
            $query->where('expenditure_reports.ST_CODE', '=', $st_code);
            $query->groupBy('expenditure_reports.candidate_id');
            $noticeatDEO = $query->get();
            
        } elseif ($st_code != '0' && $cons_no != '0') {
            $query->where('expenditure_reports.ST_CODE', '=', $st_code);
            $query ->where('expenditure_reports.constituency_no', '=', $cons_no);
            $query->groupBy('expenditure_reports.candidate_id');
            $noticeatDEO = $query->get();
        }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($noticeatDEO as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
                            $lastdate = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $lastdate->format('d-m-Y'); // 31-07-2012
                           
                           $scrutinysubmit = new DateTime($candDetails->report_submitted_date);
                            $scrutinyreportsubmitdate = $scrutinysubmit->format('d-m-Y'); // 31-07-2012
                           //$scrutinyreportsubmitdate= date('d-m-Y',strtotime($candDetails->report_submitted_date));
                           
                           $candidatelodgingdata = new DateTime($candDetails->date_orginal_acct);
                           $candidatelodgingdate = $candidatelodgingdata->format('d-m-Y'); // 31-07-2012
                           
                           $sendingdatetoceo = new DateTime($candDetails->date_of_sending_deo);
                           $ceosendingdate = $sendingdatetoceo->format('d-m-Y'); // 31-07-2012
                   
                           $ceoreceiveddate = new DateTime($candDetails->date_sending_notice_service_to_deo);
                           $ceoreceivedate = $ceoreceiveddate->format('d-m-Y'); // 31-07-2012
                          // $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                           
                             $lodgingDate =$lodgingDate ??  'N/A';
                             $scrutinyreportsubmitdate = !empty($scrutinyreportsubmitdate && $scrutinyreportsubmitdate !='30-11--0001')  ?  $scrutinyreportsubmitdate : 'N/A';
                             $candidatelodgingdate =  !empty($candidatelodgingdate && $candidatelodgingdate !='30-11--0001')  ?  $candidatelodgingdate : 'N/A' ;
                             $ceosendingdate =  !empty($ceosendingdate && $ceosendingdate !='30-11--0001')  ?  $ceosendingdate : 'N/A' ; 
                             $ceoreceivedate = (!empty($ceoreceivedate) && ($ceoreceivedate !='30-11--0001'))  ?$ceoreceivedate : 'N/A'; 
                             
                            $data = array(
                                $st->ST_NAME,
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate,
                                $scrutinyreportsubmitdate,
                                $candidatelodgingdate,
                                $ceoreceivedate
                            );
                            $TotalUsers = count($noticeatDEO);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'State', 'AC No & Name', 'Candidate Name', 'Party Name','Last Date Of Lodging', 'Date Of Submit Scrutiny Report','Date of Lodging A/C By Candidate','Date Of Sending To DEO'
                                )
                        );
                    });
                })->export('xls');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
    }

###############################End Notice CEO & DEO ###########################################################
/////by manish

    public function getscrutinyreport(Request $request) {
        $htmlData = '';
        ////get scrutiny report data ///////
        $candidate_id = $_GET['candidate_id'];
        $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);
        $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
        $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
        $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

        if (!empty($scrutinyReportData)) {
            return view('admin.ac.eci.Expenditure.GetScrutinyReport', compact('expensesourecefundbyitem', 'scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem'));
        } else {
            
        }
    }

    public function saveComment(Request $request) {
        $request = (array) $request->all();
        $comment_by_ceo = !empty($request['comment']) ? $request['comment'] : "";
        if (!empty($request)) {
            $insertComment = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $request['candidate_id'], array("comment_by_eci" => $comment_by_ceo));
            if ($insertComment) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function confirmReport() {
        $candidate_id = !empty($_GET['candidate_id']) ? $_GET['candidate_id'] : "";
        $insertComment = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidate_id, array("final_by_eci" => '1'));
        if ($insertComment) {
            return 1;
        } else {
            return 0;
        }
    }

    public function generatePDF($candidate_id) {

        $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);
        $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
        $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
        $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

        $pdf = MPDF::loadView('admin.ac.ro.Expenditure.ReportPdf', compact('scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem', 'expensesourecefundbyitem'));
        return $pdf->stream('Ro.scrunity-report.pdf');
    }

// start manoj here
    public function getprofile(Request $request) {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $d = $this->commonModel->getunewserbyuserid($user->id);


                $candidate_id = $_GET['candidate_id'];
                $profileData = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                        ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                        })
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                        ->where('m_election_details.CONST_TYPE', '=', 'AC')
                        ->get();
                return view('admin.expenditure.GetProfile', compact('profileData'));
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }






 public function printScrutinyReport($candidateId,$acno) {
         if (Auth::check()) {
            $user = Auth::user();
             $mpdf = new \Mpdf\Mpdf();
             $candidateId = base64_decode($candidateId);
             $constituency_no = base64_decode($acno);
             $d=$this->commonModel->getunewserbyuserid($user->id);             
             $canddetail = DB::table('candidate_nomination_detail')
             ->where('candidate_nomination_detail.candidate_id', $candidateId)
             ->where('candidate_nomination_detail.ac_no', $constituency_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->first();
               
               
        
            $ac_no = !empty($canddetail->ac_no) ? $canddetail->ac_no : 0;
            $st_code = !empty($canddetail->st_code) ? $canddetail->st_code : 0;
            
            $acdetail =  getacbyacno($st_code, $ac_no);
 

            $district_no = !empty($canddetail->district_no) ? $canddetail->district_no : 0;

           $districtDetails = getdistrictbydistrictno($st_code, $district_no);        
            $electionTypeId = !empty($canddetail->election_type_id) ? $canddetail->election_type_id : 0;
           
        // get CEO status cand_name ELECTION_TYPE
            
        $party_id = !empty($canddetail->party_id) ? $canddetail->party_id : 0;
        $partyname = getpartybyid($party_id);
        $partyname = !empty($partyname) ? $partyname->PARTYNAME : '';
        
     
        $ELECTION_ID = !empty($canddetail->election_id) ? $canddetail->election_id : 0;

        // echo $pcNO, $ELECTION_ID, $st_code;die;
        $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('ac_no', $ac_no)->where('election_id', $ELECTION_ID)->first();
 
        $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)->where('constituency_no', $constituency_no)->get()->toArray();
        $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)->where('constituency_no', $constituency_no)->get()->toArray();
        $expenditure_fund_parties = DB::table('expenditure_fund_parties')->where('candidate_id', $candidateId)->where('constituency_no', $constituency_no)->get()->toArray();
        $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->where('constituency_no', $constituency_no)->get()->toArray();
        $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->where('constituency_no', $constituency_no)->get()->toArray();
        $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)->where('constituency_no', $constituency_no)->where('status', '1')->get()->toArray();
        $getExpItem = DB::table('expenditure_items')->get();
         $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidateId,$constituency_no);
            $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidateId,$constituency_no);
            $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidateId,$constituency_no);

             $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)
                    ->where('expenditure_reports.constituency_no', '=', $constituency_no)
                    ->first();
             
                     $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidateId)
                      ->where('constituency_no', '=', $constituency_no)
                     ->get()->toArray();

            //  $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
            // $download_link1= !empty($download_link1) && strpos($download_link1,'ExpenditureReportAC') !==false? url($download_link1):
            // !empty($download_link1) ? url('/uploads/ExpenditureReportAC').'/'.$download_link1:'';

            //  $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';
            //  $download_link2= !empty($download_link2) && strpos($download_link2,'ExpenditureReportAC') !==false? url($download_link2):!empty($download_link2) ? url('/uploads/ExpenditureReportAC').'/'.$download_link2:'';

            // $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
            //  $download_link3= !empty($download_link3) && strpos($download_link3,'ExpenditureReportAC') !==false? url($download_link3):!empty($download_link3) ? url('/uploads/ExpenditureReportAC').'/'.$download_link3:'';
            //  $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : '';
            //  $download_link4= !empty($download_link4) && strpos($download_link4,'ExpenditureReportAC') !==false? url($download_link4): !empty($download_link4) ? url('/uploads/ExpenditureReportAC').'/'.$download_link4:'';

                     
  ////////////// file path start ///////
   
             $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
             if(strpos($download_link1,'ExpenditureReportAC') !==false) { 
                        
                   $download_link1= url($download_link1);              
            }            
            else if(!empty($download_link1) && strpos($download_link1,'ExpenditureReportAC') ==false) {
               
               $download_link1 = url('/uploads/ExpenditureReportAC').'/'.$download_link1;

            } 

             $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';

              if(strpos($download_link2,'ExpenditureReportAC') !==false) { 
                        
                   $download_link2= url($download_link2);              
            }            
            else if(!empty($download_link2) && strpos($download_link2,'ExpenditureReportAC') ==false) {
               
               $download_link2 = url('/uploads/ExpenditureReportAC').'/'.$download_link2;

            } 

            $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
              if(strpos($download_link3,'ExpenditureReportAC') !==false) { 
                        
                   $download_link3= url($download_link3);              
            }            
            else if(!empty($download_link3) && strpos($download_link3,'ExpenditureReportAC') ==false) {
               
               $download_link3 = url('/uploads/ExpenditureReportAC').'/'.$download_link3;

            } 


               $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : ''; 
            if(strpos($download_link4,'ExpenditureReportAC') !==false) { 
                        
                   $download_link4= url($download_link4);              
            }            
            else if(!empty($download_link4) && strpos($download_link4,'ExpenditureReportAC') ==false) {
               
               $download_link4 = url('/uploads/ExpenditureReportAC').'/'.$download_link4;

            } 
            ////////////// file path end ///////
          
        
  $scrutinyReportData = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                    })
                     ->leftjoin("expenditure_reports", function($leftjoin) {
                        $leftjoin->on("expenditure_reports.candidate_id", "=", "candidate_nomination_detail.candidate_id")
                        ->on("expenditure_reports.constituency_no", "=", "candidate_nomination_detail.ac_no");
                    })

                    // ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')

///
                    ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')

                    ->leftjoin('expenditure_understates', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')

                      ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'expenditure_reports.ST_CODE')
                       

                       ->join("m_ac", function($join) {
                        $join->on("m_ac.AC_NO", "=", "expenditure_reports.constituency_no")
                        ->on("m_ac.ST_CODE", "=", "expenditure_reports.st_code");
                    })
                    ->where('candidate_nomination_detail.st_code', $st_code)
                    ->where('candidate_nomination_detail.ac_no', $ac_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')                      
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidateId)
                    ->where('m_election_details.CONST_TYPE', '=', 'AC')
                    ->get();
                    
                    $submitedData=DB::table('expenditure_reports')->select('expenditure_reports.updated_at')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)
                    ->where('expenditure_reports.constituency_no', '=', $constituency_no)
                    ->first();
                       
                     $candidateName=!empty($scrutinyReportData[0]->cand_name)? $scrutinyReportData[0]->cand_name:'';
                   // $electionType=!empty($scrutinyReportData[0]->election_type)?'General '.$scrutinyReportData[0]->election_type:'';

                    $electionType='General AC';
                    $submitedData=!empty($submitedData->updated_at)? $submitedData->updated_at:0;
                    
            $date = date('d-m-Y');
            $year=date('Y');
            $title = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($candidateName . ' | ' . $electionType . ' '.$year.' | ' . $partyname);

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');
           
            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');
            
            $pdf = view('admin.expenditure.pdf_ro', compact('expensesourecefundbyitem','winn_data', 'scrutinyReportData','submitedData', 'expenseunderstated', 'expenseunderstatedbyitem','download_link1','download_link2','download_link3','download_link4','districtDetails','acdetail','electionType','partyname','getExpData'));
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();
 
        } else {
            return redirect('/officer-login');
        }
       }
















/*
    public function printScrutinyReport($candidateId) {
        if (Auth::check()) {
            $user = Auth::user();
            $mpdf = new \Mpdf\Mpdf();
            $candidateId = base64_decode($candidateId);
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $canddetail = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.candidate_id', $candidateId)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->first();



            $ac_no = !empty($canddetail->ac_no) ? $canddetail->ac_no : 0;
            $st_code = !empty($canddetail->st_code) ? $canddetail->st_code : 0;

            $acdetail = getacbyacno($st_code, $ac_no);


            $district_no = !empty($canddetail->district_no) ? $canddetail->district_no : 0;

            $districtDetails = getdistrictbydistrictno($st_code, $district_no);
            $electionTypeId = !empty($canddetail->election_type_id) ? $canddetail->election_type_id : 0;

            // get CEO status cand_name ELECTION_TYPE

            $party_id = !empty($canddetail->party_id) ? $canddetail->party_id : 0;
            $partyname = getpartybyid($party_id);
            $partyname = !empty($partyname) ? $partyname->PARTYNAME : '';


            $ELECTION_ID = !empty($canddetail->election_id) ? $canddetail->election_id : 0;

            // echo $pcNO, $ELECTION_ID, $st_code;die;
            $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('ac_no', $ac_no)->where('election_id', $ELECTION_ID)->first();

            $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)->get()->toArray();
            $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)->get()->toArray();
            $expenditure_fund_parties = DB::table('expenditure_fund_parties')->where('candidate_id', $candidateId)->get()->toArray();
            $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->get()->toArray();
            $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->get()->toArray();
            $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)->get()->toArray();
            $getExpItem = DB::table('expenditure_items')->get();
           // $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidateId);
            $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidateId);
            $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidateId);

           $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)->first();
            
             
                    $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidateId)->get()->toArray();

            //   $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
            // $download_link1= !empty($download_link1) && strpos($download_link1,'ExpenditureReportAC') !==false? url($download_link1):
            // !empty($download_link1) ? url('/uploads/ExpenditureReportAC').'/'.$download_link1:'';

            //  $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';
            //  $download_link2= !empty($download_link2) && strpos($download_link2,'ExpenditureReportAC') !==false? url($download_link2):!empty($download_link2) ? url('/uploads/ExpenditureReportAC').'/'.$download_link2:'';

            // $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
            //  $download_link3= !empty($download_link3) && strpos($download_link3,'ExpenditureReportAC') !==false? url($download_link3):!empty($download_link3) ? url('/uploads/ExpenditureReportAC').'/'.$download_link3:'';
            //  $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : '';
            //  $download_link4= !empty($download_link4) && strpos($download_link4,'ExpenditureReportAC') !==false? url($download_link4): !empty($download_link4) ? url('/uploads/ExpenditureReportAC').'/'.$download_link4:'';

////////////// file path start ///////
     
             $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
             if(strpos($download_link1,'ExpenditureReportAC') !==false) { 
                        
                   $download_link1= url($download_link1);              
            }            
            else if(!empty($download_link1) && strpos($download_link1,'ExpenditureReportAC') ==false) {
               
               $download_link1 = url('/uploads/ExpenditureReportAC').'/'.$download_link1;

            } 

             $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';

              if(strpos($download_link2,'ExpenditureReportAC') !==false) { 
                        
                   $download_link2= url($download_link2);              
            }            
            else if(!empty($download_link2) && strpos($download_link2,'ExpenditureReportAC') ==false) {
               
               $download_link2 = url('/uploads/ExpenditureReportAC').'/'.$download_link2;

            } 

            $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
              if(strpos($download_link3,'ExpenditureReportAC') !==false) { 
                        
                   $download_link3= url($download_link3);              
            }            
            else if(!empty($download_link3) && strpos($download_link3,'ExpenditureReportAC') ==false) {
               
               $download_link3 = url('/uploads/ExpenditureReportAC').'/'.$download_link3;

            } 


               $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : ''; 
            if(strpos($download_link4,'ExpenditureReportAC') !==false) { 
                        
                   $download_link4= url($download_link4);              
            }            
            else if(!empty($download_link4) && strpos($download_link4,'ExpenditureReportAC') ==false) {
               
               $download_link4 = url('/uploads/ExpenditureReportAC').'/'.$download_link4;

            } 
            ////////////// file path end ///////
 


            $scrutinyReportData = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')

///
                    ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('expenditure_understates', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'expenditure_reports.ST_CODE')
                    ->join("m_ac", function($join) {
                        $join->on("m_ac.AC_NO", "=", "expenditure_reports.constituency_no")
                        ->on("m_ac.ST_CODE", "=", "expenditure_reports.st_code");
                    })
                    ->where('candidate_nomination_detail.st_code', $st_code)
                    ->where('candidate_nomination_detail.ac_no', $ac_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidateId)
                    ->where('m_election_details.CONST_TYPE', '=', 'AC')
                    ->get();
            $submitedData = DB::table('expenditure_reports')->select('expenditure_reports.updated_at')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)
                    ->first();


            $candidateName = !empty($scrutinyReportData[0]->cand_name) ? $scrutinyReportData[0]->cand_name : '';
            $electionType = !empty($scrutinyReportData[0]->election_type) ? 'General ' . $scrutinyReportData[0]->election_type : '';
            $submitedData = !empty($submitedData->updated_at) ? $submitedData->updated_at : 0;

            $date = date('d-m-Y');
            $year = date('Y');
            $title = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($candidateName . ' | ' . $electionType . ' ' . $year . ' | ' . $partyname);

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');

            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

            $pdf = view('admin.expenditure.pdf_ro', compact('expensesourecefundbyitem', 'winn_data', 'scrutinyReportData', 'submitedData', 'expenseunderstated', 'expenseunderstatedbyitem', 'download_link1', 'download_link2', 'download_link3','download_link4', 'districtDetails', 'acdetail', 'electionType', 'partyname'));
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();
        } else {
            return redirect('/officer-login');
        }
    }

    */

//end manoj


    public function MasterDataListing(Request $request) {
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (session()->has('admin_login')) {
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($uid);
            $list_record = $this->ECIModel->getallelectionphasewise();
            $list_state = $this->ECIModel->listcurrentelectionstate();
            $list_phase = $this->ECIModel->listcurrentelectionphase();
            $list_electionid = $this->ECIModel->getallelectionbyid();
            $list = $this->ECIModel->listelectiontype();
            $MasterData = $this->expenditureModel->GetMasterEntry();
            $module = $this->commonModel->getallmodule();
            return view('admin.ac.eci.Expenditure.MasterDataListing', ['user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_state' => $list_state, 'list_phase' => $list_phase, 'list_electionid' => $list_electionid, 'list' => $list, "MasterData" => $MasterData]);
        } else {
            return redirect('/admin-login');
        }
    }

    public function masterEntry(Request $request) {
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (session()->has('admin_login')) {
            $uid = $user->id;
            $MID = base64_decode(!empty($_GET['id']) ? $_GET['id'] : "");
            $d = $this->commonModel->getunewserbyuserid($uid);
            $list_record = $this->ECIModel->getallelectionphasewise();
            $list_state = $this->ECIModel->listcurrentelectionstate();
            $list_phase = $this->ECIModel->listcurrentelectionphase();
            $list_electionid = $this->ECIModel->getallelectionbyid();
            $list = $this->ECIModel->listelectiontype();
            $singleMaster = $this->commonModel->selectone('expenditure_master_entry', 'id', $MID);

            $module = $this->commonModel->getallmodule();
            return view('admin.ac.eci.Expenditure.entryform', ['user_data' => $d, 'module' => $module, 'list_record' => $list_record, 'list_state' => $list_state, 'list_phase' => $list_phase, 'list_electionid' => $list_electionid, 'list' => $list, "singleMaster" => $singleMaster]);
        } else {
            return redirect('/admin-login');
        }
    }

    public function storeMasterEntry(Request $request) {
        $request = (array) $request->all();
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $uid = $user->id;
        $role_id = $user->role_id;
        $master_id = !empty($request['master_id']) ? $request['master_id'] : "";
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);

        $st_code = $request['st_code'];

        $GetMasterEntry = DB::select("select id from expenditure_master_entry where id='$master_id' and st_code='$st_code'");
        if (empty($GetMasterEntry)) {
            $GetMasterEntrys = DB::select("select id from expenditure_master_entry where st_code='$st_code'");

            if (!empty($GetMasterEntrys)) {
                Session::put('message', "You have already added record from this state");
                return redirect($namePrefix . '/masterEntry?id=' . base64_encode($master_id));
            }
        }

        try {
            $datas = [];

            $data_arr = array();
            foreach ($request as $key => $req_data) {
                $xss = new xssClean;
                $data_arr[$key] = $xss->clean_input($req_data);
            }

            // print_r($request);die;

            if (empty($request['master_id'])) {
                unset($request['master_id']);
                $dataInserted = $this->commonModel->insertData('expenditure_master_entry', $request);
            } else {

                //  echo $dataInserted = $this->commonModel->updatedata('expenditure_master_entry','id',$master_id,$request); 
                $dataInserted = DB::table('expenditure_master_entry')->where('id', $master_id)->update(array('result_declaration_date' => $request['result_declaration_date'], "type_of_election" => $request['type_of_election'], "st_code" => trim($request['st_code']), "ceiling_amt" => $request['ceiling_amt'], "lodged_date" => $request['lodged_date']));
            }


            if ($dataInserted) {
                Session::put('message', "Record Add successfully.");
                return redirect($namePrefix . '/MasterDataListing');
            } else {
                Session::put('message', " Internal Server Error");
                return redirect($namePrefix . '/masterEntry?id=' . base64_encode($master_id));
            }
        } catch (\Exception $e) {

            Session::put('message', "Internal Server Error");
            return redirect($namePrefix . '/masterEntry?id=' . base64_encode($master_id));
        }
    }

    public function getElectedCandidate($candidate_id) {
        $acdetail = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.candidate_id', $candidate_id)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.party_id', '<>', '1180')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->first();
        $acNo = !empty($acdetail->ac_no) ? $acdetail->ac_no : 0;
        $st_code = !empty($acdetail->st_code) ? $acdetail->st_code : 0;
        $ELECTION_ID = !empty($acdetail->election_id) ? $acdetail->election_id : 0;
        $countElectedCandidate = DB::table('winning_leading_candidate')->where('st_code', $st_code)
                ->where('ac_no', $acNo)
                ->where('election_id', $ELECTION_ID)
                ->where('candidate_id', $candidate_id)
                ->count();
        return $countElectedCandidate;
    }

/////manish

    public function editExpenditureReport(Request $request) {

        if (Auth::check()) {
                        
            ///-------------
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
            // add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj 
            
            $candidate_id = !empty($_GET['candidate_id']) ? base64_decode($_GET['candidate_id']) : 0;
            $constituency_no = !empty($_GET['ac_no']) ? base64_decode($_GET['ac_no']) : 0;
            
            
             // nomination detail
                     $nomicnationdetails= DB::table('candidate_nomination_detail')
                            ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                        })                            
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                        ->where('candidate_nomination_detail.ac_no', '=', $constituency_no)
                        ->where('m_election_details.CONST_TYPE', '=', 'AC')
                        ->first();
                        $st_code=!empty($nomicnationdetails->st_code)?$nomicnationdetails->st_code:0 ;
                        $election_id=!empty($nomicnationdetails->st_code)?$nomicnationdetails->election_id:0 ;
                        $dist_no=!empty($nomicnationdetails->district_no)? $nomicnationdetails->district_no:0;
                        $ac_no=!empty($nomicnationdetails->ac_no)?$nomicnationdetails->ac_no:0;
                        $party_id=!empty($nomicnationdetails->party_id)?$nomicnationdetails->party_id:0;
                        $CONST_NO=!empty($nomicnationdetails->CONST_NO)?$nomicnationdetails->CONST_NO:0;
                        $CONST_TYPE=!empty($nomicnationdetails->CONST_TYPE)?$nomicnationdetails->CONST_TYPE:0;
                        $ELECTION_TYPE=!empty($nomicnationdetails->ELECTION_TYPE)?$nomicnationdetails->ELECTION_TYPE:0;
            
             
            $candidate_data = $this->expenditureModel->getunewserbyuserid_uid_ceo($candidate_id);   
            $cand_data = DB::table('candidate_personal_detail')->select('candidate_id','cand_name')->where('candidate_id',$candidate_id)->first();
            $ele_details=$this->commonModel->election_detailsac($st_code,$ac_no,$dist_no,$d->id,$d->officerlevel);
           
            
            $check_finalize = candidate_finalizebyro($st_code, $CONST_NO, $CONST_TYPE);
            if ($check_finalize == '') {
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
            } else {
                $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                $cand_finalize_ro = $check_finalize->finalized_ac;
            }
            $seched = [];
            $sechdul = [];
            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
            $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
  /////////////////////////////////// for ac
            
   
                
                
                
                 // nomination detail
                    
                                   $Acdetail=DB::table('m_ac')
                                
                                ->where('m_ac.ST_CODE',$st_code)
                                ->where('m_ac.AC_NO',$ac_no)
                                ->first();
            
            // for ac=====================
            try {

                $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id,$constituency_no);
                if (!empty($ReportSingleData)) {
                    $ReportSingleData = (array) $ReportSingleData[0];
                } else {
                    $ReportSingleData = array();
                } 
                $countElectedCandidate=$this->getElectedCandidate($candidate_id);

             
                return view('admin.expenditure.createmisexpensereport', ['cand_data'=>$cand_data,'user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "ReportSingleData" => $ReportSingleData, "nature_of_default_ac" => $nature_of_default_ac, "candidate_data" => (array) $candidate_data,'Acdetail'=>$Acdetail,'countElectedCandidate'=>$countElectedCandidate,'countElectedCandidate'=>$countElectedCandidate,'resultDeclarationDate'=>$resultDeclarationDate,'CONST_NO'=>$CONST_NO]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /*
    public function editExpenditureReport(Request $request) {
        if (Auth::check()) {

            ///-------------
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);
             // add 24/10/2019 manoj
              $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
               // end 24/10/2019 manoj 

            $candidate_id = !empty($_GET['candidate_id']) ? base64_decode($_GET['candidate_id']) : 0;

            // nomination detail
            $nomicnationdetails = DB::table('candidate_nomination_detail')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                    })
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                    ->where('m_election_details.CONST_TYPE', '=', 'AC')
                    ->first();
            $st_code = !empty($nomicnationdetails->st_code) ? $nomicnationdetails->st_code : 0;
            $election_id = !empty($nomicnationdetails->st_code) ? $nomicnationdetails->election_id : 0;
            $dist_no = !empty($nomicnationdetails->district_no) ? $nomicnationdetails->district_no : 0;
            $ac_no = !empty($nomicnationdetails->ac_no) ? $nomicnationdetails->ac_no : 0;
            $party_id = !empty($nomicnationdetails->party_id) ? $nomicnationdetails->party_id : 0;
            $CONST_NO = !empty($nomicnationdetails->CONST_NO) ? $nomicnationdetails->CONST_NO : 0;
            $CONST_TYPE = !empty($nomicnationdetails->CONST_TYPE) ? $nomicnationdetails->CONST_TYPE : 0;
            $ELECTION_TYPE = !empty($nomicnationdetails->ELECTION_TYPE) ? $nomicnationdetails->ELECTION_TYPE : 0;


            $candidate_data = $this->expenditureModel->getunewserbyuserid_uid_ceo($candidate_id);
            $cand_data = DB::table('candidate_personal_detail')->select('candidate_id', 'cand_name')->where('candidate_id', $candidate_id)->first();
            $ele_details = $this->commonModel->election_detailsac($st_code, $ac_no, $dist_no, $d->id, $d->officerlevel);


            $check_finalize = candidate_finalizebyro($st_code, $CONST_NO, $CONST_TYPE);
            if ($check_finalize == '') {
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
            } else {
                $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                $cand_finalize_ro = $check_finalize->finalized_ac;
            }
            $seched = [];
            $sechdul = [];
            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
            $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
            /////////////////////////////////// for ac
            // nomination detail

            $Acdetail = DB::table('m_ac')
                    ->where('m_ac.ST_CODE', $st_code)
                    ->where('m_ac.AC_NO', $ac_no)
                    ->first();

            // for ac=====================
            try {

                $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id);
                if (!empty($ReportSingleData)) {
                    $ReportSingleData = (array) $ReportSingleData[0];
                } else {
                    $ReportSingleData = array();
                }
                $countElectedCandidate = $this->getElectedCandidate($candidate_id);

                return view('admin.expenditure.createmisexpensereport', ['cand_data' => $cand_data, 'user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "ReportSingleData" => $ReportSingleData, "nature_of_default_ac" => $nature_of_default_ac, "candidate_data" => (array) $candidate_data, 'Acdetail' => $Acdetail, 'countElectedCandidate' => $countElectedCandidate,'resultDeclarationDate'=>$resultDeclarationDate]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            return redirect('/officer-login');
        }
    }
    */

    public function StoreMisExpenseReport(Request $request) {
        $request = (array) $request->all();
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $uid = $user->id;
        $role_id = $user->role_id;
        //$report_id = $request['report_id'];
        $candidate_id = $request['candidate_id'];
        $request['user_id'] = $uid;
        $final_action = $request['final_action'];
        $notice_send_to = $request['notice_send_to'];
        $comment_by_eci = $request['comment_by_eci'];
        $date_of_receipt_eci = $request['date_of_receipt_eci'];
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        // check elected candidate start
        $isElectedCandidate = $this->getElectedCandidate($candidate_id);
        if ($isElectedCandidate > 0) {
            $request['return_status'] == 'Returned';
        } else {
            $request['return_status'] == 'Non-Returned';
        }
        // check elected candidate end
        try {
            $data_arr = array();
            foreach ($request as $key => $req_data) {
                $xss = new xssClean;
                $data_arr[$key] = $xss->clean_input($req_data);
            }



            $unsetItems = ['candidate_id', 'constituency_no', 'constituency_nos', 'contensting_candiate',
                'date_of_declaration', 'user_id', 'notice_send_to'];
            $dataUpdate = array_diff_key($data_arr, array_flip($unsetItems));

            //date_of_sending_deo

            $updateStatus = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->update($dataUpdate);
            ###############ECI NOTICE FINAL#########################
            //echo $final_action.'notice_send_to'.$notice_send_to;
            if ($final_action == 'Closed' || $final_action == 'Disqualified' || $final_action == 'Case Dropped') {
                $finalbyeci = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->update(['final_by_eci' => '1','final_by_ceo' => '1','final_by_ro' => '1']);
                Session::put('message', "Saved successfully");
                return redirect($namePrefix . '/eciallscrutiny');
            } elseif ($final_action == 'Notice Issued' || $final_action == 'Reply Issued' || $final_action == 'Hearing Done') {

                ////////////////////////////////// add entry in expenditure action logs/////////////////

                $cdate = date('Y-m-d h:i:s');
                $data_action = array("candidate_id" => $candidate_id, "deo_action" => $final_action, "ceo_action" => $final_action, "eci_action" => $final_action, "eci_action_date" => $cdate, "eci_comment" => $comment_by_eci, "created_by" => $uid, "eci_action_sending_date" => $cdate, "eci_action_receive_date" => $date_of_receipt_eci);

                $data_arr_action = array();
                foreach ($data_action as $key => $req_data_action) {
                    $xss = new xssClean;
                    $data_arr_action[$key] = $xss->clean_input($req_data_action);
                }

                $check_exits_log = DB::table('expenditure_action_logs')->where('eci_action', '!=', "")->where('candidate_id', $candidate_id)->first();
                if (!empty($check_exits_log) && is_array($check_exits_log) && count($check_exits_log) > 0) {
                    $data_actionInserted = $this->commonModel->updatedata('expenditure_action_logs', 'candidate_id', $candidate_id, $data_arr_action);
                } else {
                    $data_actionInserted = $this->commonModel->insertData('expenditure_action_logs', $data_arr_action);
                }
                ///////////////////////////////////////// end entry in expenditure logs///////////////////


                if ($notice_send_to == 'ceo') {
                    $pendencybyceo = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->update(['final_by_ceo' => '0', 'final_by_eci' => '0']);
                }
            }
            //dd(DB::getQueryLog());
            ################ECI NOTICE ENDS########################
            // dd($updateStatus);
            if ($updateStatus > 0) {
                Session::put('message', "Saved successfully");
                return redirect($namePrefix . '/editExpenditureReport?candidate_id=' . base64_encode($candidate_id));
            } else {
                Session::put('message', "No change");
                return redirect($namePrefix . '/editExpenditureReport?candidate_id=' . base64_encode($candidate_id));
            }
        } catch (\Exception $e) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function GetProfileECI(Request $request) {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $d = $this->commonModel->getunewserbyuserid($user->id);


                $candidate_id = $_GET['candidate_id'];
                $profileData = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                        ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                        })
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                        ->where('m_election_details.CONST_TYPE', '=', 'AC')
                        ->get();
                // get CEO status

                $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
                $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
                $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();
                $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id);
                if (!empty($ReportSingleData)) {

                    $ReportSingleData = (array) $ReportSingleData[0];
                }

                return view('admin.expenditure.GetProfileRO', compact('profileData',
                                'ReportSingleData', 'electionType', 'nature_of_default_ac', 'current_status'));
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function getcandidateList(request $request) {
        //dd($request->all());
        // DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            $conditions = "";
            if (!empty($_GET['state'])) {
                $st_code = $_GET['state'];
                $conditions .= "AND candidate_nomination_detail.st_code='$st_code' ";
            }

            if (!empty($_GET['ac'])) {
                $ac = $_GET['ac'];
                $conditions .= "AND candidate_nomination_detail.ac_no='$ac' ";
            }


            #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username = $user->officername;
            $st_code = $request->input('state');
            // $permitstate=$this->accessstate;
            $zonestate = $this->eciexpenditureModel->getzonestate($username);
            if ($zonestate->isEmpty()) {
                $permitstates = '';
            } else {
                $permitstates = explode(',', $zonestate[0]->assign_state);
            }

            $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

            if (!empty($permitstate)) {
                $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
            } else {
                $statelist = $this->commonModel->getallstate();
            }
            if (!empty($st_code)) {
                $st_code = $st_code;
            } elseif (empty($st_code) && !empty($permitstate)) {
                $st_code = array_values($permitstate)[0];
            } else {
                $st_code = 0;
            }

            #########################Code For State Wise Access#####################

            if (!empty($conditions)) {
                $candList = DB::select("select `candidate_nomination_detail`.*, `candidate_personal_detail`.*, `m_election_details`.*, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`updated_at` as `finalized_date`, `expenditure_reports`.`final_by_ro`, `expenditure_reports`.`date_of_declaration` from `candidate_nomination_detail` left join `candidate_personal_detail` on `candidate_nomination_detail`.`candidate_id` = `candidate_personal_detail`.`candidate_id` inner join `m_election_details` on `m_election_details`.`st_code` = `candidate_nomination_detail`.`st_code` and `m_election_details`.`CONST_NO` = `candidate_nomination_detail`.`ac_no` left join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = '6' and `candidate_nomination_detail`.`party_id` <> 1180 and `candidate_nomination_detail`.`finalaccepted` = '1' and `m_election_details`.`CONST_TYPE` = 'AC' and `expenditure_reports`.`finalized_status` = '1' and expenditure_reports.final_by_eci='0' $conditions");
            } else {
                $candList = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                        ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                        })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.finalized_status', 'expenditure_reports.updated_at as finalized_date', 'expenditure_reports.final_by_ro', 'expenditure_reports.date_of_declaration')
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('m_election_details.CONST_TYPE', '=', 'AC')
                        ->where('expenditure_reports.finalized_status', '=', '1')
                        ->where('expenditure_reports.final_by_eci', '=', '0')
                        ->where('expenditure_reports.st_code', '=', $st_code)
                        ->get();
            }
            // dd(DB::getQueryLog());
            // dd($candList);
            return view('admin.ac.eci.Expenditure.FinalizedcandidateList', ['user_data' => $d, 'candList' => $candList, 'statelist' => $statelist, 'st_code' => $st_code]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function printTrackingStatus($candidateId) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->expenditureModel->getunewserbyuserid($user->id, $user->role_id);
            $mpdf = new \Mpdf\Mpdf();

            $candiateAcName = getacbyacno($d->st_code, $d->ac_no);
            $candiateAcName = !empty($candiateAcName) ? $candiateAcName->AC_NAME : '---';

            $candidate_id = base64_decode($candidateId);
            $profileData = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                    })
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                    ->where('m_election_details.CONST_TYPE', '=', 'AC')
                    ->get();
            // get CEO status cand_name ELECTION_TYPE
            $candidateName = !empty($profileData[0]) ? $profileData[0]->cand_name : '';
            $ELECTION_TYPE = !empty($profileData[0]) ? $profileData[0]->ELECTION_TYPE : '';
            $party_id = !empty($profileData[0]) ? $profileData[0]->party_id : '';
            $partyname = getpartybyid($party_id);
            $partyname = !empty($partyname) ? $partyname->PARTYNAME : '---';

            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
            $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
            $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();
            $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id);
            if (!empty($ReportSingleData)) {

                $ReportSingleData = (array) $ReportSingleData[0];
            }

            $date = date('d-m-Y');
            $title = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($candidateName . ' | ' . $ELECTION_TYPE . ' | ' . $partyname);

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');
            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

            $pdf = view('admin.expenditure.pdf_ro_tracking', compact('profileData',
                            'ReportSingleData', 'electionType', 'nature_of_default_ac', 'current_status'));
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();
            // return view('admin.expenditure.pdf_eci_tracking', compact('profileData',
            //                 'ReportSingleData', 'electionType', 'nature_of_default_ac', 'current_status'));
        } else {
            return redirect('/officer-login');
        }
    }

    public function updateStatusReport(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;

            $candidateId = $_GET['candidate_id'];
            $reason = $_GET['reason'];

            $data_definalization = array('candidate_id' => $candidateId, 'created_by' => $uid, 'updated_by' => $uid, 'comment' => $reason, "count_by_eci" => '1', 'log_type' => 'DEFINALIZATION', 'officer_level' => 'ECI');
            if ($candidateId) {
                $updateStatus = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidateId, array("finalized_status" => "0", "final_by_ro" => '0'));
                $insertLog = $this->commonModel->insertData('expenditure_logs', $data_definalization);

                if ($updateStatus) {
                    Session::put('message', "Permission sent for the updation of scrutiny report successfully.");

                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function viewByCandidateId($candidateId,$ac_no) {
        $candidateId = base64_decode($candidateId);
        $ac_no = base64_decode($ac_no);

        $candidateData = DeoexpenditureModel::viewById($candidateId,$ac_no);


        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
 
        
        
     
     $nomicnationdetails= DB::table('candidate_nomination_detail')
                            ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                        })                            
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidateId)
                        ->where('candidate_nomination_detail.ac_no', '=', $ac_no)
                        ->where('m_election_details.CONST_TYPE', '=', 'AC')
                        ->first();
                        $st_code=!empty($nomicnationdetails->st_code)?$nomicnationdetails->st_code:0 ;
                        $election_id=!empty($nomicnationdetails->st_code)?$nomicnationdetails->election_id:0 ;
                        $dist_no=!empty($nomicnationdetails->district_no)? $nomicnationdetails->district_no:0;
                        $ac_no=!empty($nomicnationdetails->ac_no)?$nomicnationdetails->ac_no:0;
                        $party_id=!empty($nomicnationdetails->party_id)?$nomicnationdetails->party_id:0;
                        $CONST_NO=!empty($nomicnationdetails->CONST_NO)?$nomicnationdetails->CONST_NO:0;
                         $CONST_TYPE=!empty($nomicnationdetails->CONST_TYPE)?$nomicnationdetails->CONST_TYPE:0; 
         $ele_details=$this->commonModel->election_detailsac($st_code,$ac_no,$dist_no,$d->id,$d->officerlevel);
                          $acdetail =  getacbyacno($st_code, $ac_no);
      $district_details=getdistrictbydistrictno($st_code,$dist_no);
                        $Acdetail=DB::table('m_ac')
                                 
                                ->where('m_ac.ST_CODE',$st_code)
                                ->where('m_ac.AC_NO',$ac_no)
                                ->first();
                       
                        
        
        $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('ac_no', $CONST_NO)->where('election_id', $election_id)->first();
        
        $check_finalize = candidate_finalizebyro($st_code, $CONST_NO, $CONST_TYPE);
        if ($check_finalize == '') {
            $cand_finalize_ceo = 0;
            $cand_finalize_ro = 0;
        } else {
            $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
            $cand_finalize_ro = $check_finalize->finalized_ac;
        }
        $seched = [];
        $sechdul = [];
       $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidateId);
             
 
        $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)
         ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $expenditure_fund_parties = DB::table('expenditure_fund_parties')
        ->where('candidate_id', $candidateId)
        ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)
          ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)
          ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)
         ->where('constituency_no', $ac_no)->where('status','1')
        ->get()->toArray();
        $getExpItem = DB::table('expenditure_items')->get();
 

                            $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)->where('expenditure_reports.constituency_no', $ac_no)->first();
             
                         $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();

            //   $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
            // $download_link1= !empty($download_link1) && strpos($download_link1,'ExpenditureReportAC') !==false? url($download_link1):
            // !empty($download_link1) ? url('/uploads/ExpenditureReportAC').'/'.$download_link1:'';

            //  $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';
            //  $download_link2= !empty($download_link2) && strpos($download_link2,'ExpenditureReportAC') !==false? url($download_link2):!empty($download_link2) ? url('/uploads/ExpenditureReportAC').'/'.$download_link2:'';

            // $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
            //  $download_link3= !empty($download_link3) && strpos($download_link3,'ExpenditureReportAC') !==false? url($download_link3):!empty($download_link3) ? url('/uploads/ExpenditureReportAC').'/'.$download_link3:'';
            //  $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : '';
            //  $download_link4= !empty($download_link4) && strpos($download_link4,'ExpenditureReportAC') !==false? url($download_link4): !empty($download_link4) ? url('/uploads/ExpenditureReportAC').'/'.$download_link4:'';


  ////////////// file path start ///////
   
             $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
             if(strpos($download_link1,'ExpenditureReportAC') !==false) { 
                        
                   $download_link1= url($download_link1);              
            }            
            else if(!empty($download_link1) && strpos($download_link1,'ExpenditureReportAC') ==false) {
               
               $download_link1 = url('/uploads/ExpenditureReportAC').'/'.$download_link1;

            } 

             $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';

              if(strpos($download_link2,'ExpenditureReportAC') !==false) { 
                        
                   $download_link2= url($download_link2);              
            }            
            else if(!empty($download_link2) && strpos($download_link2,'ExpenditureReportAC') ==false) {
               
               $download_link2 = url('/uploads/ExpenditureReportAC').'/'.$download_link2;

            } 

            $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
              if(strpos($download_link3,'ExpenditureReportAC') !==false) { 
                        
                   $download_link3= url($download_link3);              
            }            
            else if(!empty($download_link3) && strpos($download_link3,'ExpenditureReportAC') ==false) {
               
               $download_link3 = url('/uploads/ExpenditureReportAC').'/'.$download_link3;

            } 


               $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : ''; 
            if(strpos($download_link4,'ExpenditureReportAC') !==false) { 
                        
                   $download_link4= url($download_link4);              
            }            
            else if(!empty($download_link4) && strpos($download_link4,'ExpenditureReportAC') ==false) {
               
               $download_link4 = url('/uploads/ExpenditureReportAC').'/'.$download_link4;

            } 
            ////////////// file path end ///////

      
        return view('admin.expenditure.viewdeoForm', ['user_data' => $d, 'candidateData' => $candidateData,
            "getCandidateExpData" => $getCandidateExpData, "expenditure_fund_source" => $expenditure_fund_source, "expenditure_fund_parties" => $expenditure_fund_parties, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'ele_details' => $ele_details, "getSourceFundData" => $getSourceFundData, "getExpData" => $getExpData, "getExpItem" => $getExpItem, "gexExpReport" => $gexExpReport,'winn_data'=>$winn_data,'scrutinyReportData'=> $scrutinyReportData,
'district_details'=>$district_details,'acdetail'=>$acdetail,
'download_link1'=>$download_link1,
'download_link2'=>$download_link2,
'download_link3'=>$download_link3,
'download_link4'=>$download_link4
]);
    }



/*
    public function viewByCandidateId($candidateId,$ac_no) {
        $candidateId = base64_decode($candidateId);
        $ac_no = base64_decode($ac_no);
        $candidateData = DeoexpenditureModel::viewById($candidateId,$ac_no);

        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);




        $nomicnationdetails = DB::table('candidate_nomination_detail')
                ->join("m_election_details", function($join) {
                    $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                    ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                })
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.party_id', '<>', '1180')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.candidate_id', '=', $candidateId)
                ->where('candidate_nomination_detail.ac_no', '=', $ac_no)
                ->where('m_election_details.CONST_TYPE', '=', 'AC')
                ->first();
        $st_code = !empty($nomicnationdetails->st_code) ? $nomicnationdetails->st_code : 0;
        $election_id = !empty($nomicnationdetails->st_code) ? $nomicnationdetails->election_id : 0;
        $dist_no = !empty($nomicnationdetails->district_no) ? $nomicnationdetails->district_no : 0;
        $ac_no = !empty($nomicnationdetails->ac_no) ? $nomicnationdetails->ac_no : 0;
        $party_id = !empty($nomicnationdetails->party_id) ? $nomicnationdetails->party_id : 0;
        $CONST_NO = !empty($nomicnationdetails->CONST_NO) ? $nomicnationdetails->CONST_NO : 0;
        $CONST_TYPE = !empty($nomicnationdetails->CONST_TYPE) ? $nomicnationdetails->CONST_TYPE : 0;
        $ele_details = $this->commonModel->election_detailsac($st_code, $ac_no, $dist_no, $d->id, $d->officerlevel);
        $acdetail = getacbyacno($st_code, $ac_no);
        $district_details = getdistrictbydistrictno($st_code, $dist_no);
        $Acdetail = DB::table('m_ac')
                ->where('m_ac.ST_CODE', $st_code)
                ->where('m_ac.AC_NO', $ac_no)
                ->first();



        $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('ac_no', $CONST_NO)->where('election_id', $election_id)->first();

        $check_finalize = candidate_finalizebyro($st_code, $CONST_NO, $CONST_TYPE);
        if ($check_finalize == '') {
            $cand_finalize_ceo = 0;
            $cand_finalize_ro = 0;
        } else {
            $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
            $cand_finalize_ro = $check_finalize->finalized_ac;
        }
        $seched = [];
        $sechdul = [];
        $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidateId);


        $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)->get()->toArray();
        $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)->get()->toArray();
        $expenditure_fund_parties = DB::table('expenditure_fund_parties')->where('candidate_id', $candidateId)->get()->toArray();
        $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->get()->toArray();
        $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->get()->toArray();
        $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)->get()->toArray();
        $getExpItem = DB::table('expenditure_items')->get();


        $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)->first();
             

                     $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidateId)->get()->toArray();
                     
                       $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)
         ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $expenditure_fund_parties = DB::table('expenditure_fund_parties')
        ->where('candidate_id', $candidateId)
        ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)
          ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)
          ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)
         ->where('constituency_no', $ac_no)->where('status','1')
        ->get()->toArray();
        $getExpItem = DB::table('expenditure_items')->get();
 

         $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)->where('expenditure_reports.constituency_no', $ac_no)->first();
             
        $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();


            //  $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
            // $download_link1= !empty($download_link1) && strpos($download_link1,'ExpenditureReportAC') !==false? url($download_link1):
            // !empty($download_link1) ? url('/uploads/ExpenditureReportAC').'/'.$download_link1:'';

            //  $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';
            //  $download_link2= !empty($download_link2) && strpos($download_link2,'ExpenditureReportAC') !==false? url($download_link2):!empty($download_link2) ? url('/uploads/ExpenditureReportAC').'/'.$download_link2:'';

            // $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
            //  $download_link3= !empty($download_link3) && strpos($download_link3,'ExpenditureReportAC') !==false? url($download_link3):!empty($download_link3) ? url('/uploads/ExpenditureReportAC').'/'.$download_link3:'';
            //  $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : '';
            //  $download_link4= !empty($download_link4) && strpos($download_link4,'ExpenditureReportAC') !==false? url($download_link4): !empty($download_link4) ? url('/uploads/ExpenditureReportAC').'/'.$download_link4:'';

                     ////////////// file path start ///////
     
             $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
             if(strpos($download_link1,'ExpenditureReportAC') !==false) { 
                        
                   $download_link1= url($download_link1);              
            }            
            else if(!empty($download_link1) && strpos($download_link1,'ExpenditureReportAC') ==false) {
               
               $download_link1 = url('/uploads/ExpenditureReportAC').'/'.$download_link1;

            } 

             $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';

              if(strpos($download_link2,'ExpenditureReportAC') !==false) { 
                        
                   $download_link2= url($download_link2);              
            }            
            else if(!empty($download_link2) && strpos($download_link2,'ExpenditureReportAC') ==false) {
               
               $download_link2 = url('/uploads/ExpenditureReportAC').'/'.$download_link2;

            } 

            $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
              if(strpos($download_link3,'ExpenditureReportAC') !==false) { 
                        
                   $download_link3= url($download_link3);              
            }            
            else if(!empty($download_link3) && strpos($download_link3,'ExpenditureReportAC') ==false) {
               
               $download_link3 = url('/uploads/ExpenditureReportAC').'/'.$download_link3;

            } 


               $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : ''; 
            if(strpos($download_link4,'ExpenditureReportAC') !==false) { 
                        
                   $download_link4= url($download_link4);              
            }            
            else if(!empty($download_link4) && strpos($download_link4,'ExpenditureReportAC') ==false) {
               
               $download_link4 = url('/uploads/ExpenditureReportAC').'/'.$download_link4;

            } 
            ////////////// file path end ///////
 



        return view('admin.expenditure.viewdeoForm', ['user_data' => $d, 'candidateData' => $candidateData,
            "getCandidateExpData" => $getCandidateExpData, "expenditure_fund_source" => $expenditure_fund_source, "expenditure_fund_parties" => $expenditure_fund_parties, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'sechdul' => $sechdul, 'sched' => $seched, 'ele_details' => $ele_details, "getSourceFundData" => $getSourceFundData, "getExpData" => $getExpData, "getExpItem" => $getExpItem, "gexExpReport" => $gexExpReport, 'winn_data' => $winn_data, 'scrutinyReportData' => $scrutinyReportData,
            'district_details' => $district_details, 'acdetail' => $acdetail,
            'download_link1'=>$download_link1,
            'download_link2'=>$download_link2,
            'download_link3'=>$download_link3,
            'download_link4'=>$download_link4
        ]);
    }

    */

 public function getReturn(Request $request, $state, $ac) {

        try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

    $xss = new xssClean;
    $st_code = base64_decode($xss->clean_input($state));
    $cons_no = base64_decode($xss->clean_input($ac));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
   //echo 'st_code=>'.$st_code.'cons_no=>'.$cons_no;
  $query=DB::table('expenditure_reports')
->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
->select('candidate_personal_detail.cand_name', 'expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME')
->where('candidate_nomination_detail.application_status', '=', '6')
->where('candidate_nomination_detail.finalaccepted', '=', '1')
->where('candidate_nomination_detail.symbol_id', '<>', '200')
->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
->where('expenditure_reports.return_status', '=', 'Returned')
->where('expenditure_reports.finalized_status', '=', '1')
->where('expenditure_reports.final_by_ro', '=', '1');

    if (!empty($st_code) && $cons_no == '') {
    $query->where('expenditure_reports.ST_CODE', '=', $st_code);
    $query->groupBy('expenditure_reports.candidate_id');
    } else if (!empty($st_code) && $cons_no != '') {
    $query->where('expenditure_reports.ST_CODE', '=', $st_code);
    $query->where('expenditure_reports.constituency_no', '=', $cons_no);
     $query->groupBy('expenditure_reports.candidate_id');
    }else{
        $query->groupBy('expenditure_reports.candidate_id'); 
    }
 
  $returnCandList=$query->get();

    $count = !empty($returnCandList) ? count($returnCandList) : 0;
    
if (!empty($_GET['exl']) && $_GET['exl']="yes") {
//////////export exel //////////////
// Initialize the array which will be passed into the Excel
// generator.
$candidateArray = []; 

// Define the Excel spreadsheet headers
$candidateArray[] = ['S.NO', 'STATE NAME','PC NO & PC NAME','CANDIDATE NAME','PARTYNAME','LAST LODGING DATE','TOTAL RECEIVED FUND(Rs.)','TOTAL EXPENDITURE DECLARED BY CANDIDATE(Rs.)'];

// Convert each member of the returned collection into an array,
// and append it to the payments array.
$i=1;
foreach ($returnCandList as $canwise) { // dd($canwise);

$totalexpen= !empty($canwise->grand_total_election_exp_by_cadidate) ? $canwise->grand_total_election_exp_by_cadidate : '0';

$candreceieved = $this->expenditureModel->getcandidatetotalexpenditure($canwise->candidate_id);
$acdetails=getacbyacno($canwise->ST_CODE,$canwise->constituency_no); 
$st=getstatebystatecode($canwise->ST_CODE);
$candidateArr[$i]['S.no'] = $i;
$candidateArr[$i]['state_name'] = $st->ST_NAME;
$candidateArr[$i]['ac_no'] = $acdetails->AC_NO.' - '.$acdetails->AC_NAME;
$candidateArr[$i]['cand_name'] = $canwise->cand_name;
$candidateArr[$i]['partyname'] = $canwise->PARTYNAME;
$candidateArr[$i]['lastlodgingdate'] = !empty($canwise->last_date_prescribed_acct_lodge)  ? date('d-m-Y',strtotime($canwise->last_date_prescribed_acct_lodge)) : 'N/A';
$candidateArr[$i]['candreceieved'] =!empty($candreceieved) ? $candreceieved : '0';
$candidateArr[$i]['$totalexpen'] =!empty($totalexpen) ? $totalexpen : '0';

$i++;
}

            foreach ($candidateArr as $candidate) {
            $candidateArray[] = $candidate;
            }
       // Generate and return the spreadsheet
        \Excel::create('ReturnACCandidateReport', function($excel) use ($candidateArray) {
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Elected Candidate Wise Expenditure');
            $excel->setCreator('Eci')->setCompany('Election Commission Of India');
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('ReturnACCandidateReport', function($sheet) use ($candidateArray) {
                $sheet->fromArray($candidateArray, null, 'A1', false, false);
            });
           })->download('csv');
         }
         else
         {
        return view('admin.ac.eci.Expenditure.return-report', ['user_data' => $d, 'returnCandList' => $returnCandList,
        'edetails' => $ele_details, "count" => $count,
        'st_code' => $st_code,
        'cons_no' => $cons_no
    ]);
   } 
} else {
    return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC ECI getreturn TRY CATCH ENDS HERE      
    }

    
public function getElectedcand(Request $request,$state, $ac) {
    try {
    if (Auth::check()) {
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

        $xss = new xssClean;
        $st_code=base64_decode($xss->clean_input($state));
        $cons_no=base64_decode($xss->clean_input($ac));
        $st_code=!empty($st_code) ? $st_code : 0;
        $cons_no=!empty($cons_no) ? $cons_no : 0; 
        
        
    #####Code For State Wise Access By Niraj date 23-07-2019#####################
      $username=$user->officername;
      
      $zonestate = $this->eciexpenditureModel->getzonestate($username);
      if($zonestate->isEmpty()){
        $permitstates = '';
      }else{
        $permitstates = explode(',',$zonestate[0]->assign_state);
      }

      $permitstate=($zonestate->isEmpty()) ?  '0' : $permitstates;

        if(!empty($permitstate)){
            $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
        }else{
           $statelist = $this->commonModel->getallstate();
        }
        if(!empty($st_code)){
            $st_code=$st_code;
        }elseif(empty($st_code) && !empty($permitstate)){
            $st_code=array_values($permitstate)[0];
        }else {
            $st_code=0;
        }
                   
        #########################Code For State Wise Access#####################
$query = DB::table('winning_leading_candidate')
  ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'winning_leading_candidate.candidate_id')
  ->join('m_party', 'winning_leading_candidate.lead_cand_partyid', '=', 'm_party.CCODE')
  ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'winning_leading_candidate.candidate_id')
  ->select('winning_leading_candidate.candidate_id','winning_leading_candidate.st_code','winning_leading_candidate.ac_no as constituency_no','candidate_personal_detail.cand_name','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.grand_total_election_exp_by_cadidate','expenditure_reports.created_at','expenditure_reports.final_by_ro','m_party.PARTYNAME');

if(!empty($st_code) && empty($cons_no)) {
   $query->where('winning_leading_candidate.st_code', '=', $st_code);
 } else if (!empty($st_code) && !empty($cons_no)) {
  $query->where('winning_leading_candidate.st_code', '=', $st_code);
  $query->where('winning_leading_candidate.pc_no', '=', $cons_no);
}
$query->groupBy('winning_leading_candidate.candidate_id');

$electedCandList=$query->get();
                
$count=!empty($electedCandList) ? count($electedCandList): '0';

if (!empty($_GET['exl']) && $_GET['exl']="yes") {
  //////////export exel //////////////
// Initialize the array which will be passed into the Excel
// generator.
$candidateArray = []; 

// Define the Excel spreadsheet headers
$candidateArray[] = ['S.NO', 'STATE NAME','AC NO & AC NAME','CANDIDATE NAME','PARTYNAME','LAST LODGING DATE','TOTAL RECEIVED FUND(Rs.)','TOTAL EXPENDITURE DECLARED BY CANDIDATE(Rs.)'];

// Convert each member of the returned collection into an array,
// and append it to the payments array.
$i=1;
foreach ($electedCandList as $canwise) {
   $candidate_id=$canwise->candidate_id;
   $totalexpen=$this->expenditureModel->getcandidatetotalexpenditure($candidate_id);
    
$acdetails=getacbyacno($canwise->st_code,$canwise->constituency_no); 
$st=getstatebystatecode($canwise->st_code);
  $candidateArr[$i]['S.no'] = $i;
  $candidateArr[$i]['state_name'] = $st->ST_NAME;
  $candidateArr[$i]['ac_no'] = $acdetails->AC_NO.' - '.$acdetails->AC_NAME;
  $candidateArr[$i]['cand_name'] = $canwise->cand_name;
  $candidateArr[$i]['partyname'] = $canwise->PARTYNAME;
  $candidateArr[$i]['lastlodgingdate'] = !empty($canwise->last_date_prescribed_acct_lodge)  ? date('d-m-Y',strtotime($canwise->last_date_prescribed_acct_lodge)) : 'N/A';
  $candidateArr[$i]['totalexpen'] =!empty($totalexpen) ? $totalexpen : '0';
  $candidateArr[$i]['grand_total_election_exp_by_cadidate'] =!empty($canwise->grand_total_election_exp_by_cadidate) ? $canwise->grand_total_election_exp_by_cadidate : '0';
  $i++;
}

                foreach ($candidateArr as $candidate) {
                       $candidateArray[] = $candidate;
                }
               // Generate and return the spreadsheet
                \Excel::create('ElectedACCandidateReport', function($excel) use ($candidateArray) {

                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Elected Candidate Wise Expenditure');
                    $excel->setCreator('Eci')->setCompany('Election Commission Of India');
                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('ElectedACCandidateReport', function($sheet) use ($candidateArray) {
                        $sheet->fromArray($candidateArray, null, 'A1', false, false);
                    });
                   })->download('csv');
                 }
                 else
                 {
              return view('admin.ac.eci.Expenditure.electedcandidate-report', ['user_data' => $d, 'electedCandList' => $electedCandList ,
          'edetails' => $ele_details, "count" => $count,
          'st_code'=>$st_code,
          'cons_no'=>$cons_no
                        ]);
            }
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC Elected Candidate TRY CATCH ENDS HERE   
    } // end Function Elected Candidate

    public function getNonReturn(Request $request, $state, $ac) {

        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);


                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;

                if (!empty($st_code) && $cons_no == '') {
                    $nonreturnCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.return_status', '=', 'Non-Returned')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } else if (!empty($st_code) && $cons_no != '') {
                    $nonreturnCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.return_status', '=', 'Non-Returned')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } else {

                    $nonreturnCandList = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('candidate_personal_detail.cand_name', 'expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.return_status', '=', 'Non-Returned')
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->where('expenditure_reports.final_by_ro', '=', '1')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                $count = !empty($nonreturnCandList) ? count($nonreturnCandList) : 0;

                return view('admin.ac.eci.Expenditure.non-return-report', ['user_data' => $d, 'nonreturnCandList' => $nonreturnCandList,
                    'edetails' => $ele_details, "count" => $count,
                    'st_code' => $st_code,
                    'cons_no' => $cons_no
                ]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

         public function candidate_wise_expenditure(Request $request) {

        // DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $cur_time = Carbon::now();
            $conditions = "";
            if (!empty($_GET['state'])) {
                $st_code = $_GET['state'];
                $conditions .= " and candidate_nomination_detail.st_code='$st_code' ";
            }

            if (!empty($_GET['ac'])) {
                $ac = $_GET['ac'];
                $conditions .= " and candidate_nomination_detail.ac_no='$ac' ";
            }


            #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username = $user->officername;
            $st_code = $request->input('state');
            // $permitstate=$this->accessstate;
            $zonestate = $this->eciexpenditureModel->getzonestate($username);
            if ($zonestate->isEmpty()) {
                $permitstates = '';
            } else {
                $permitstates = explode(',', $zonestate[0]->assign_state);
            }

            $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

            if (!empty($permitstate)) {
                $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
            } else {
                $statelist = $this->commonModel->getallstate();
            }
            if (!empty($st_code)) {
                $st_code = $st_code;
            } elseif (empty($st_code) && !empty($permitstate)) {
                $st_code = array_values($permitstate)[0];
            } else {
                $st_code = 0;
            }

            #########################Code For State Wise Access#####################


            if (!empty($conditions)) {
                $candList = DB::select("select m_election_details.YEAR,m_election_details.ELECTION_TYPE,candidate_personal_detail.cand_hname,candidate_nomination_detail.ac_no,candidate_nomination_detail.st_code,candidate_nomination_detail.district_no,candidate_nomination_detail.party_id,candidate_personal_detail.cand_name,candidate_personal_detail.candidate_id, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`updated_at` as `finalized_date`, `expenditure_reports`.`final_by_ro`, `expenditure_reports`.`date_of_declaration`,
                    `expenditure_reports`.`grand_total_election_exp_by_cadidate`
                 from `candidate_nomination_detail` left join `candidate_personal_detail` on `candidate_nomination_detail`.`candidate_id` = `candidate_personal_detail`.`candidate_id` inner join `m_election_details` on `m_election_details`.`st_code` = `candidate_nomination_detail`.`st_code` and `m_election_details`.`CONST_NO` = `candidate_nomination_detail`.`ac_no` left join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = 6 and `candidate_nomination_detail`.`party_id` <> 1180 and `candidate_nomination_detail`.`finalaccepted` = '1' and `m_election_details`.`CONST_TYPE` = 'AC' and expenditure_reports.date_of_declaration !='' $conditions order by expenditure_reports.grand_total_election_exp_by_cadidate desc");
            } else {

                $candList = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                        ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                        })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->select('m_election_details.ELECTION_TYPE', 'm_election_details.YEAR', 'candidate_personal_detail.cand_hname', 'candidate_nomination_detail.ac_no', 'candidate_nomination_detail.st_code', 'candidate_nomination_detail.district_no', 'candidate_nomination_detail.party_id', 'candidate_personal_detail.cand_name', 'expenditure_reports.finalized_status', 'expenditure_reports.updated_at as finalized_date', 'expenditure_reports.final_by_ro', 'expenditure_reports.date_of_declaration','expenditure_reports.grand_total_election_exp_by_cadidate',
                            'candidate_personal_detail.candidate_id')                        
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('m_election_details.CONST_TYPE', '=', 'AC')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('expenditure_reports.date_of_declaration', '!=', '')->orderBy('expenditure_reports.grand_total_election_exp_by_cadidate', 'desc')
                        ->get();
 

            }
 
             //dd($candList);die;
            if (!empty($_GET['pdf']) && $_GET['pdf'] = "yes") {
                ////// code for pdf generation//////
                $pdf = PDF::loadView('admin.ac.eci.Expenditure.CandidateWisePdf', ['user_data' => $d, 'candList' => $candList]);
                return $pdf->download('CandidateWisePdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.CandidateWisePdf');
            } elseif (!empty($_GET['exl']) && $_GET['exl'] = "yes") {
                //////////export exel //////////////
                // Initialize the array which will be passed into the Excel
                // generator.
                $candidateArray = [];

                // Define the Excel spreadsheet headers
                $candidateArray[] = ['S.NO','CANDIDATE NAME', 'STATE NAME', 'AC NO & AC NAME', 'YEAR', 'ELECTION TYPE', 'TOTAL EXPENDITURE DECLARED BY CANDIDATE(Rs.)'];

                // Convert each member of the returned collection into an array,
                // and append it to the payments array.
                $i = 1;

                foreach ($candList as $canwise) { 
                    $acdetails = getacbyacno($canwise->st_code, $canwise->ac_no);
                    $st = getstatebystatecode($canwise->st_code);
                    $candidateArr[$i]['S.no'] = $i;
                    $candidateArr[$i]['cand_name'] = $canwise->cand_name;
                    $candidateArr[$i]['state_name'] = $st->ST_NAME;
                    $candidateArr[$i]['ac_no'] = $acdetails->AC_NO . ' - ' . $acdetails->AC_NAME;
                    $candidateArr[$i]['year'] = $canwise->YEAR;
                    $candidateArr[$i]['election_type'] = $canwise->ELECTION_TYPE;
                    $candidateArr[$i]['grand_total_election_exp_by_cadidate'] =!empty($canwise->grand_total_election_exp_by_cadidate) ? $canwise->grand_total_election_exp_by_cadidate : '0';
                    //$candidateArr[$i]['total_expenditure'] = !empty($candidateArr[$i]['total_expenditure']) ? 'Rs. ' . $candidateArr[$i]['total_expenditure'] : 0;

                    $i++;
                }

                foreach ($candidateArr as $candidate) {
                    $candidateArray[] = $candidate;
                }

                // Generate and return the spreadsheet
                \Excel::create('CandidateWiseExpenditure', function($excel) use ($candidateArray) {

                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Candidate Wise Expenditure');
                    $excel->setCreator('Eci')->setCompany('Election Commission Of India');
                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('CandidateWiseExpenditure', function($sheet) use ($candidateArray) {
                        $sheet->fromArray($candidateArray, null, 'A1', false, false);
                    });
                })->download('csv');
            } else {
                return view('admin.ac.eci.Expenditure.candidate_wise_expenditure', ['user_data' => $d, 'ele_details' => $ele_details, 'candList' => $candList, "statelist" => $statelist, "st_code" => $st_code]);
            }
            // dd(DB::getQueryLog());
            // dd($candList);
        } else {
            return redirect('/officer-login');
        }
    }

    public function getPartyWiseExpenditure(Request $request) {
        // DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $cur_time = Carbon::now();
            $conditions = "";
            if (!empty($_GET['party'])) {
                $party = $_GET['party'];
                $conditions .= " and candidate_nomination_detail.party_id='$party' ";
            }

            if (!empty($_GET['state'])) {
                $state = $_GET['state'];
                $conditions .= " and candidate_nomination_detail.st_code='$state' ";
            } else {
                $state = "";
            }

            if (!empty($_GET['ac'])) {
                $ac = $_GET['ac'];
                $conditions .= " and candidate_nomination_detail.ac_no='$ac' ";
            } else {
                $ac = "";
            }

            #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username = $user->officername;
            $st_code = $request->input('state');
            // $permitstate=$this->accessstate;
            $zonestate = $this->eciexpenditureModel->getzonestate($username);
            if ($zonestate->isEmpty()) {
                $permitstates = '';
            } else {
                $permitstates = explode(',', $zonestate[0]->assign_state);
            }

            $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

            if (!empty($permitstate)) {
                $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
            } else {
                $statelist = $this->commonModel->getallstate();
            }
            if (!empty($st_code)) {
                $st_code = $st_code;
            } elseif (empty($st_code) && !empty($permitstate)) {
                $st_code = array_values($permitstate)[0];
            } else {
                $st_code = 0;
            }

            #########################Code For State Wise Access#####################

            if (!empty($conditions)) {
                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');

                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
            } else {

                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');
                // print_r($partyids);die;
                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1'");

                //$partylist = DB::select("SELECT * FROM m_party WHERE 1 and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
            }


            if (!empty($_GET['pdf']) && $_GET['pdf'] = "yes") {
                ////// code for pdf generation//////
                $pdf = PDF::loadView('admin.ac.eci.Expenditure.getPartyWisePDF', ['user_data' => $d, 'partylist' => $partylist]);
                return $pdf->download('PartyWisePdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.getPartyWisePDF');
            } elseif (!empty($_GET['exl']) && $_GET['exl'] == "yes") {

                if (!empty($state)) {
                    $st = getstatebystatecode($state);
                    $stateName = !empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
                } else {
                    $stateName = "ALL";
                    $state = "";
                }

                if (!empty($ac)) {
                    $acdetails = getacbyacno($state, $ac);
                    $acName = !empty($acdetails->AC_NAME) ? $acdetails->AC_NAME : 'ALL';
                } else {
                    $acName = "ALL";
                    $ac = "";
                }


                // Initialize the array which will be passed into the Excel
                // generator.
                $partyArray = [];

                // Define the Excel spreadsheet headers
                $partyArray[] = ['S.no', 'State', 'AC Name', 'Party Name', 'Total Expenditure'];

                // Convert each member of the returned collection into an array,
                // and append it to the payments array.
                $i = 1;
                foreach ($partylist as $party) {
                    $partyArr[$i]['S.no'] = $i;
                    $partyArr[$i]['state'] = $stateName;
                    $partyArr[$i]['ac_name'] = $acName;
                    $partyArr[$i]['party_name'] = $party->PARTYABBRE . ' - ' . $party->PARTYNAME;
                    $partyArr[$i]['total_expenditure'] = $this->expenditureModel->getpartytotalexpenditure($party->CCODE, $state, $ac);
                    $partyArr[$i]['total_expenditure'] = !empty($partyArr[$i]['total_expenditure']) ? $partyArr[$i]['total_expenditure'] : 0;

                    $i++;
                }

                foreach ($partyArr as $pay) {
                    $partyArray[] = $pay;
                }

                // Generate and return the spreadsheet
                \Excel::create('PartyWiseExpenditure', function($excel) use ($partyArray) {

                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Party Wise Expenditure');
                    $excel->setCreator('Eci')->setCompany('Election Commission Of India');
                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('PartyWiseExpenditure', function($sheet) use ($partyArray) {
                        $sheet->fromArray($partyArray, null, 'A1', false, false);
                    });
                })->download('csv');
            } else {
                return view('admin.ac.eci.Expenditure.party_wise_expenditure', ['user_data' => $d, 'ele_details' => $ele_details, 'partylist' => $partylist, "statelist" => $statelist, "st_code" => $st_code]);
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function getNationlPartyWiseExpenditure(Request $request) {
        // DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $cur_time = Carbon::now();
            $cur_time = Carbon::now();
            $conditions = "";
            if (!empty($_GET['party'])) {
                $party = $_GET['party'];
                $conditions .= " and candidate_nomination_detail.party_id='$party' ";
            }

            if (!empty($_GET['state'])) {
                $state = $_GET['state'];
                $conditions .= " and candidate_nomination_detail.st_code='$state' ";
            }

            if (!empty($_GET['ac'])) {
                $ac = $_GET['ac'];
                $conditions .= " and candidate_nomination_detail.ac_no='$ac' ";
            }

            #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username = $user->officername;
            $st_code = $request->input('state');
            $zonestate = $this->eciexpenditureModel->getzonestate($username);
            if ($zonestate->isEmpty()) {
                $permitstates = '';
            } else {
                $permitstates = explode(',', $zonestate[0]->assign_state);
            }

            $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

            if (!empty($permitstate)) {
                $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
            } else {
                $statelist = $this->commonModel->getallstate();
            }
            if (!empty($st_code)) {
                $st_code = $st_code;
            } elseif (empty($st_code) && !empty($permitstate)) {
                $st_code = array_values($permitstate)[0];
            } else {
                $st_code = 0;
            }

            #########################Code For State Wise Access#####################


            if (!empty($conditions)) {
                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');

                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
            } else {
                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE cand_party_type ='N'");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');
                // print_r($partyids);die;
                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE ='N'");

                //$partylist = DB::select("SELECT * FROM m_party WHERE 1 and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
            }


            if (!empty($_GET['pdf']) && $_GET['pdf'] = "yes") {
                ////// code for pdf generation//////
                $pdf = PDF::loadView('admin.ac.eci.Expenditure.fund-nationalpartiesPDF', ['user_data' => $d, 'partylist' => $partylist]);
                return $pdf->download('PartyWisePdf_' . trim($_GET['pdf']) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.fund-nationalpartiesPDF');
            } elseif (!empty($_GET['exl']) && $_GET['exl'] == "yes") {

                if (!empty($state)) {
                    $st = getstatebystatecode($state);
                    $stateName = !empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
                } else {
                    $stateName = "ALL";
                    $state = "";
                }

                if (!empty($ac)) {
                    $acdetails = getacbyacno($state, $ac);
                    $acName = !empty($acdetails) ? $acdetails->AC_NAME : 'ALL';
                } else {
                    $acName = "ALL";
                    $ac = "";
                }

                // Initialize the array which will be passed into the Excel
                // generator.
                $partyArray = [];


                // Define the Excel spreadsheet headers
                //  $partyArray[] = ['S.no','State','AC Name','Party Name','Total Expenditure'];
                // Convert each member of the returned collection into an array,
                // and append it to the payments array.
                $i = 1;
                foreach ($partylist as $party) {
                    $partyArr[$i]['S.no'] = $i;
                    $partyArr[$i]['state'] = $stateName;
                    $partyArr[$i]['ac_name'] = $acName;
                    $partyArr[$i]['party_name'] = $party->PARTYABBRE . ' - ' . $party->PARTYNAME;
                    $partyArr[$i]['total_expenditure'] = $this->expenditureModel->getpartytotalexpenditure($party->CCODE, $state, $ac);
                    $partyArr[$i]['total_expenditure'] = !empty($partyArr[$i]['total_expenditure']) ? $partyArr[$i]['total_expenditure'] : 0;
                    $i++;
                }

                foreach ($partyArr as $pay) {
                    $partyArray[] = $pay;
                }
                $amount = array_column($partyArray, 'total_expenditure');
                array_multisort($amount, SORT_DESC, $partyArray);
                $headingpartyArray[] = ['S.no', 'State', 'AC Name', 'Party Name', 'Total Expenditure'];
                // array_shift($partyArray,array('S.no','State','AC Name','Party Name','Total Expenditure'));
                $partyArray2 = $headingpartyArray + $partyArray;
                // Generate and return the spreadsheet
                \Excel::create('PartyWiseExpenditure', function($excel) use ($partyArray2) {

                    // Set the spreadsheet title, creator, and description
                    $excel->setTitle('Party Wise Expenditure');
                    $excel->setCreator('Eci')->setCompany('Election Commission Of India');
                    // Build the spreadsheet, passing in the payments array
                    $excel->sheet('PartyWiseExpenditure', function($sheet) use ($partyArray2) {
                        $sheet->fromArray($partyArray2, null, 'A1', false, false);
                    });
                })->download('csv');
            } else {
                return view('admin.ac.eci.Expenditure.fund-nationalparties', ['user_data' => $d, 'ele_details' => $ele_details, 'partylist' => $partylist, "statelist" => $statelist, "st_code" => $st_code]);
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function trackingReport(Request $request) {
        try {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $uid = $user->id;
            #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username = $user->officername;

            $st_code = $request->input('state');
            $ac_no = $request->input('ac');
            $zonestate = $this->eciexpenditureModel->getzonestate($username);
            if ($zonestate->isEmpty()) {
                $permitstates = '';
            } else {
                $permitstates = explode(',', $zonestate[0]->assign_state);
            }

            $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

            if (!empty($permitstate)) {
                $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
            } else {
                $statelist = $this->commonModel->getallstate();
            }
            if (!empty($st_code)) {
                $st_code = $st_code;
                $aclist = getacbystate($st_code);
            } elseif (empty($st_code)) {
                $st_code = !empty($statelist[0]->ST_CODE) ? $statelist[0]->ST_CODE : '';
                $aclist = getacbystate($st_code);
                $ac_no = !empty($aclist[0]->AC_NO) ? $aclist[0]->AC_NO : '';
            } else {
                $st_code = 0;
            }

            $election = getelectiondetailbystcode($st_code, $ac_no, 'AC');
            $ELECTION_ID = !empty($election->ELECTION_ID) ? $election->ELECTION_ID : 0;


            $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('ac_no', $ac_no)->where('election_id', $ELECTION_ID)->first();



            $stateDetail = getstatebystatecode($st_code);
            $Acdetail = getacbyacno($st_code, $ac_no);

            $AcName = !empty($Acdetail) ? $Acdetail->AC_NAME : '';
            $AcNo = !empty($Acdetail->PC_NO) ? $Acdetail->AC_NO : '';


            $candList = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                    })

                    // ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')

                      ->leftjoin("expenditure_reports", function($join) {
                        $join->on("expenditure_reports.candidate_id", "=", "candidate_nomination_detail.candidate_id")
                        ->on("expenditure_reports.constituency_no", "=", "candidate_nomination_detail.ac_no");
                    })





                    ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
                    ->leftjoin("expenditure_understates", function($join) {
                        $join->on("expenditure_understates.candidate_id", "=", "candidate_nomination_detail.candidate_id")
                        ->where("expenditure_understates.understated_type_id", "=", "8");
                    })->select('expenditure_fund_parties.*', 'expenditure_understates.*', 'candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.*', 'm_party.PARTYNAME')
                    ->where('candidate_nomination_detail.st_code', $st_code)
                    ->where('candidate_nomination_detail.ac_no', $ac_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('m_election_details.CONST_TYPE', '=', 'AC')
                    ->groupBy('candidate_nomination_detail.candidate_id')
                    ->get();

            if (!empty($candList)) {
                $i = 0;
                foreach ($candList as $cand) {
                    $expenditure_understates = DB::table('expenditure_understates')->where('candidate_id', $cand->candidate_id)->where('ST_CODE', $st_code)->where('constituency_no', $AcNo)->where('understated_type_id', '9')->first();
                    $other_source_cc = DB::table('expenditure_fund_source')->where('candidate_id', $cand->candidate_id)->where('ST_CODE', $st_code)->where('constituency_no', $AcNo)
                                    ->whereIn('other_source_payment_mode', array('Cheque', 'Cash'))->sum('other_source_amount');
                    $other_source_kind = DB::table('expenditure_fund_source')->where('candidate_id', $cand->candidate_id)->where('ST_CODE', $st_code)->where('constituency_no', $AcNo)
                                    ->whereIn('other_source_payment_mode', array('In Kind'))->sum('other_source_amount');
                    $candList[$i]->comment_9 = !empty($expenditure_understates->comment) ? $expenditure_understates->comment : "";
                    $candList[$i]->understated_type_id_9 = !empty($expenditure_understates->understated_type_id) ? $expenditure_understates->understated_type_id : "";
                    $candList[$i]->other_source_amt_cc = !empty($other_source_cc) ? $other_source_cc : "0";
                    $candList[$i]->other_source_amt_kind = !empty($other_source_kind) ? $other_source_kind : "0";
                    $i++;
                }
            }


 // add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj


            return view('admin.expenditure.summary_report_eci', ['user_data' => $d,
                "cand_finalize_ro" => array(),
                'candList' => $candList,
                'Acdetail' => $Acdetail, 'stateDetail' => $stateDetail,
                'winn_data' => $winn_data,
                'statelist' => $statelist,
                'st_code' => $st_code,
                'ac_no' => $ac_no,
                'resultDeclarationDate'=>$resultDeclarationDate
            ]);
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

    public function trackingReportprint(Request $request, $state, $ac) {
        try {
            $mpdf = new \Mpdf\Mpdf();
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $uid = $user->id;
            $username = $user->officername;
            $st_code = !empty($state) ? base64_decode($state) : 0;
            $ac_no = !empty($ac) ? base64_decode($ac) : 0;
            $statelist = $this->commonModel->getallstate();
            if (!empty($st_code)) {
                $st_code = $st_code;
            } elseif (empty($st_code)) {
                $st_code = !empty($statelist[0]->ST_CODE) ? $statelist[0]->ST_CODE : '';
                $aclist = getpcbystate($st_code);
                $ac_no = !empty($aclist[0]->AC_NO) ? $aclist[0]->AC_NO : '';
            } else {
                $st_code = 0;
            }

            //  echo'-'.$st_code.'-'.$pc_no;die;
            $election = getelectiondetailbystcode($st_code, $ac_no, 'AC');
            $ELECTION_ID = !empty($election->ELECTION_ID) ? $election->ELECTION_ID : 0;


            $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('ac_no', $ac_no)->where('election_id', $ELECTION_ID)->first();



            $stateDetail = getstatebystatecode($st_code);
            $stateName = !empty($stateDetail->ST_NAME) ? $stateDetail->ST_NAME : '';
            $acdetail = getacbyacno($st_code, $ac_no);

            $AcName = !empty($acdetail) ? $acdetail->AC_NAME : '';
            $AcNo = !empty($acdetail->AC_NO) ? $acdetail->AC_NO : '';
            $date = date('d-m-Y');

            /* $ELECTION_TYPE = !empty($ele_details->ELECTION_TYPE) ? $ele_details->ELECTION_TYPE : ''; */
            $ELECTION_TYPE = "General AC";
            $date = date('d-m-Y');
            $year = '2019';
            $title = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($AcName . ' | ' . $ELECTION_TYPE . ' ' . $year . ' | ' . $stateName);

            $mpdf->SetFooter($date . '|' . "Election Commission of India" . '|{PAGENO}');

            $mpdf->SetProtection(array('print'));
            $mpdf->SetTitle($title);
            $mpdf->SetAuthor("Election Commission of India");
            $mpdf->SetWatermarkText("Election Commission of India");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');

            $candList = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                    })->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->leftjoin('expenditure_fund_parties', 'expenditure_fund_parties.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
                    ->leftjoin("expenditure_understates", function($join) {
                        $join->on("expenditure_understates.candidate_id", "=", "candidate_nomination_detail.candidate_id")
                        ->where("expenditure_understates.understated_type_id", "=", "8");
                    })->select('expenditure_fund_parties.*', 'expenditure_understates.*', 'candidate_nomination_detail.*', 'candidate_personal_detail.*', 'm_election_details.*', 'expenditure_reports.*', 'm_party.PARTYNAME')
                    ->where('candidate_nomination_detail.st_code', $st_code)
                    ->where('candidate_nomination_detail.ac_no', $ac_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('m_election_details.CONST_TYPE', '=', 'AC')
                    ->groupBy('candidate_nomination_detail.candidate_id')
                    ->get();

            if (!empty($candList)) {
                $i = 0;
                foreach ($candList as $cand) {
                    $expenditure_understates = DB::table('expenditure_understates')->where('candidate_id', $cand->candidate_id)->where('ST_CODE', $st_code)->where('constituency_no', $AcNo)->where('understated_type_id', '9')->first();
                    $other_source_cc = DB::table('expenditure_fund_source')->where('candidate_id', $cand->candidate_id)->where('ST_CODE', $st_code)->where('constituency_no', $AcNo)
                                    ->whereIn('other_source_payment_mode', array('Cheque', 'Cash'))->sum('other_source_amount');
                    $other_source_kind = DB::table('expenditure_fund_source')->where('candidate_id', $cand->candidate_id)->where('ST_CODE', $st_code)->where('constituency_no', $AcNo)
                                    ->whereIn('other_source_payment_mode', array('In Kind'))->sum('other_source_amount');
                    $candList[$i]->comment_9 = !empty($expenditure_understates->comment) ? $expenditure_understates->comment : "";
                    $candList[$i]->understated_type_id_9 = !empty($expenditure_understates->understated_type_id) ? $expenditure_understates->understated_type_id : "";
                    $candList[$i]->other_source_amt_cc = !empty($other_source_cc) ? $other_source_cc : "0";
                    $candList[$i]->other_source_amt_kind = !empty($other_source_kind) ? $other_source_kind : "0";
                    $i++;
                }
            }

         // add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj

            $pdf = view('admin.expenditure.pdf_tracking_report', compact('candList', 'stateDetail', 'acdetail', 'winn_data','resultDeclarationDate'));
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }

// start fund graph 
    public function getNationlPartyWiseExpendituregraph(Request $request) {
        // DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $cur_time = Carbon::now();
            $cur_time = Carbon::now();
            $conditions = "";
            if (!empty($_GET['party'])) {
                $party = $_GET['party'];
                $conditions .= " and candidate_nomination_detail.party_id='$party' ";
            }

            if (!empty($_GET['state'])) {
                $state = $_GET['state'];
                $conditions .= " and candidate_nomination_detail.st_code='$state' ";
            }

            if (!empty($_GET['ac'])) {
                $ac = $_GET['ac'];
                $conditions .= " and candidate_nomination_detail.ac_no='$ac' ";
            }

            #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username = $user->officername;
            $st_code = $request->input('state');
            $zonestate = $this->eciexpenditureModel->getzonestate($username);
            if ($zonestate->isEmpty()) {
                $permitstates = '';
            } else {
                $permitstates = explode(',', $zonestate[0]->assign_state);
            }

            $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

            if (!empty($permitstate)) {
                $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
            } else {
                $statelist = $this->commonModel->getallstate();
            }
            if (!empty($st_code)) {
                $st_code = $st_code;
            } elseif (empty($st_code) && !empty($permitstate)) {
                $st_code = array_values($permitstate)[0];
            } else {
                $st_code = 0;
            }

            #########################Code For State Wise Access#####################


            if (!empty($conditions)) {
                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');

                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
            } else {
                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE cand_party_type ='N'");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');
                // print_r($partyids);die;
                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE ='N'");

                //$partylist = DB::select("SELECT * FROM m_party WHERE 1 and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
            } 
                return view('admin.ac.eci.Expenditure.fund-nationalpartiesGraph', ['user_data' => $d, 'ele_details' => $ele_details, 'partylist' => $partylist, "statelist" => $statelist, "st_code" => $st_code]);
                
        } else {
            return redirect('/officer-login');
        }
    }

    public function getNationlPartyWiseExpenditureNationGraph(Request $request) {
        // DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);


            $conditions = "";
            if (!empty($_GET['party'])) {
                $party = $_GET['party'];
                $conditions .= " and candidate_nomination_detail.party_id='$party' ";
            }

            if (!empty($_GET['state'])) {
                $state = $_GET['state'];
                $conditions .= " and candidate_nomination_detail.st_code='$state' ";
            }
            $ac = 0;
            if (!empty($_GET['ac'])) {
                $ac = $_GET['ac'];
                $conditions .= " and candidate_nomination_detail.ac_no='$ac' ";
            }

            #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username = $user->officername;
            $st_code = $request->input('state');
            $zonestate = $this->eciexpenditureModel->getzonestate($username);
            if ($zonestate->isEmpty()) {
                $permitstates = '';
            } else {
                $permitstates = explode(',', $zonestate[0]->assign_state);
            }

            $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

            if (!empty($permitstate)) {
                $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
            } else {
                $statelist = $this->commonModel->getallstate();
            }
            if (!empty($st_code)) {
                $st_code = $st_code;
            } elseif (empty($st_code) && !empty($permitstate)) {
                $st_code = array_values($permitstate)[0];
            } else {
                $st_code = 0;
            }

            #########################Code For State Wise Access#####################


            if (!empty($conditions)) {
                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');

                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
            } else {

                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');
                // print_r($partyids);die;
                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE ='N'");
            }






            $partyArray = [];



            $data = [
                ['National Parties funds', 'No. of candidate to Whom National Parties gave funds'],
            ];
            $i = 1;
            if (count($partylist) > 0) {
                foreach ($partylist as $party) {


                    $totalcandidates = $this->expenditureModel->getcandidatesbyparties($party->CCODE, $st_code, $ac);
                    $countPartywiseCandidate = count(explode(',', $totalcandidates));
                    $data[] = [$party->PARTYABBRE, $countPartywiseCandidate];
                }
            } else {
                $data[] = ['No Data', 0];
            }
            return json_encode($data);
        } else {
            return redirect('/officer-login');
        }
    }

    public function getNationlPartyWiseExpenditureAvgGraph(Request $request) {
        // DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);

            $conditions = "";
            if (!empty($_GET['party'])) {
                $party = $_GET['party'];
                $conditions .= " and candidate_nomination_detail.party_id='$party' ";
            }

            if (!empty($_GET['state'])) {
                $state = $_GET['state'];
                $conditions .= " and candidate_nomination_detail.st_code='$state' ";
            }

            $ac = 0;
            if (!empty($_GET['ac'])) {
                $ac = $_GET['ac'];
                $conditions .= " and candidate_nomination_detail.ac_no='$ac' ";
            }

            #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username = $user->officername;
            $st_code = $request->input('state');
            $zonestate = $this->eciexpenditureModel->getzonestate($username);
            if ($zonestate->isEmpty()) {
                $permitstates = '';
            } else {
                $permitstates = explode(',', $zonestate[0]->assign_state);
            }

            $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

            if (!empty($permitstate)) {
                $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
            } else {
                $statelist = $this->commonModel->getallstate();
            }
            if (!empty($st_code)) {
                $st_code = $st_code;
            } elseif (empty($st_code) && !empty($permitstate)) {
                $st_code = array_values($permitstate)[0];
            } else {
                $st_code = 0;
            }

            #########################Code For State Wise Access#####################


            if (!empty($conditions)) {
                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail WHERE 1 $conditions");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');

                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
            } else {

                $partyids = DB::select("SELECT distinct party_id FROM candidate_nomination_detail");
                if (!empty($partyids)) {
                    foreach ($partyids as $value) {
                        $partyID[] = $value->party_id;
                    }

                    $partyids = implode(',', $partyID);
                }

                //print_r($partyids);die; 
                $partyids = !empty($partyids) ? $partyids : 0;
                $partyids = rtrim(implode(',', array_unique(explode(',', $partyids))), ',');
                // print_r($partyids);die;
                $partylist = DB::select("SELECT * FROM m_party WHERE CCODE IN ($partyids) and PARTYTYPE ='N'");

                //$partylist = DB::select("SELECT * FROM m_party WHERE 1 and PARTYTYPE !='Z' and PARTYTYPE !='Z1' order by PARTYNAME asc");
            }


            $partyArray = [];


            $data = [
                ['National Parties funds', 'Average funds given to a candidate by national parties'],
            ];


            if (count($partylist) > 0) {
                foreach ($partylist as $party) {
                    $grandTotal = 0;

                    $totalcandidates = $this->expenditureModel->getcandidatesbyparties($party->CCODE, $st_code, $ac);
                    $countPartywiseCandidate = count(explode(',', $totalcandidates));
                    $totalexpen = $this->expenditureModel->getpartyExp($totalcandidates);
                    $grandTotal += $totalexpen;
                    $avgexpencandidatewise = round($totalexpen / $countPartywiseCandidate, 2);
                    $data[] = [$party->PARTYABBRE, $avgexpencandidatewise];
                }
            } else {
                $data[] = ['No Data', 0];
            }
            return json_encode($data);
        } else {
            return redirect('/officer-login');
        }
    }

// end fund graph
    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 29-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersmis By ECI fuction     
     */
    public function getDistrictReport(Request $request) {
        //dd($request->all());
        //PC ECI getOfficersmis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $xss = new xssClean;
                $st_code = $xss->clean_input($request->input('state'));
                $cons_no = $xss->clean_input($request->input('ac'));
                $district = $xss->clean_input($request->input('district'));
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################

                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';
                $district = !empty($district) ? $district : '';
                $districts = DB::table('m_district')->select('DIST_NAME', 'DIST_NO')->where('ST_CODE', $st_code)->get();


                // DB::enableQueryLog();
                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');

                if (!empty($st_code) && empty($cons_no) && $st_code != 'All' && empty($district)) {
                    $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
                }if (!empty($st_code) && !empty($cons_no) && $st_code != 'All' && empty($district)) {
                    $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
                    $totalContestedCandidate->where('candidate_nomination_detail.ac_no', '=', $cons_no);
                } else if (!empty($st_code) && !empty($district) && empty($cons_no) && $st_code != 'All') {
                    $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);

                    $totalContestedCandidate->join("m_ac", function($join) {
                        $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                                ->on("m_ac.AC_NO", "=", "candidate_nomination_detail.ac_no");
                    });

                    $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
                } else if (!empty($st_code) && !empty($district) && !empty($cons_no) && $st_code != 'All') {

                    $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
                    $totalContestedCandidate->join("m_ac", function($join) {
                        $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                                ->on("m_ac.AC_NO", "=", "candidate_nomination_detail.ac_no");
                    });
                    $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
                    $totalContestedCandidate->where('candidate_nomination_detail.ac_no', '=', $cons_no);
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {

                    $totalContestedCandidate->whereIn('candidate_nomination_detail.st_code', $permitstates);
                }
                //dd(DB::getQueryLog());
                $result = $totalContestedCandidate->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                        ->groupBy("candidate_nomination_detail.st_code", 'candidate_nomination_detail.ac_no')
                        ->get();
                if (!empty($district)) {
                    $all_ac = DB::table('m_ac')
                            ->where('ST_CODE', $st_code)
                            ->where('DIST_NO_HDQTR', $district)
                            ->orderBy('AC_NAME')
                            ->get();
                } else {
                    $all_ac = DB::table('m_ac')
                            ->where('ST_CODE', $st_code)
                            ->orderBy('AC_NAME')
                            ->get();
                }



                return view('admin.ac.eci.Expenditure.district-report', ['user_data' => $d,
                    'totalContestedCandidate' => $result,
                    'cons_no' => $cons_no,
                    'st_code' => $st_code,
                    'statelist' => $statelist,
                    'district' => $district,
                    'districts' => $districts,
                    'all_ac' => $all_ac,
                    'permitstates' => $permitstates,
                    'count' => count($result)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getOfficersmis TRY CATCH ENDS HERE    
    }

    public function getDistrictReportPdf(Request $request, $state, $district, $pc) {
        //dd($request->all());
        //PC ECI getOfficersmis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $xss = new xssClean;
                $st_code = $xss->clean_input(base64_decode($state));
                $cons_no = $xss->clean_input(base64_decode($pc));
                $district = $xss->clean_input(base64_decode($district));
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################

                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';
                $district = !empty($district) ? $district : '';
                $districts = DB::table('m_district')->select('DIST_NAME', 'DIST_NO')->where('ST_CODE', $st_code)->get();


                // DB::enableQueryLog();
                $totalContestedCandidate = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');

                if (!empty($st_code) && empty($cons_no) && $st_code != 'All' && empty($district)) {
                    $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
                }if (!empty($st_code) && !empty($cons_no) && $st_code != 'All' && empty($district)) {
                    $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
                    $totalContestedCandidate->where('candidate_nomination_detail.ac_no', '=', $cons_no);
                } else if (!empty($st_code) && !empty($district) && empty($cons_no) && $st_code != 'All') {
                    $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);

                    $totalContestedCandidate->join("m_ac", function($join) {
                        $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                                ->on("m_ac.AC_NO", "=", "candidate_nomination_detail.ac_no");
                    });

                    $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
                } else if (!empty($st_code) && !empty($district) && !empty($cons_no) && $st_code != 'All') {

                    $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
                    $totalContestedCandidate->join("m_ac", function($join) {
                        $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                                ->on("m_ac.AC_NO", "=", "candidate_nomination_detail.ac_no");
                    });
                    $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
                    $totalContestedCandidate->where('candidate_nomination_detail.ac_no', '=', $cons_no);
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {

                    $totalContestedCandidate->whereIn('candidate_nomination_detail.st_code', $permitstates);
                }
                //dd(DB::getQueryLog());
                $result = $totalContestedCandidate->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                        ->groupBy("candidate_nomination_detail.st_code", 'candidate_nomination_detail.ac_no')
                        ->get();
                if (!empty($district)) {
                    $all_ac = DB::table('m_ac')
                            ->where('ST_CODE', $st_code)
                            ->where('DIST_NO_HDQTR', $district)
                            ->orderBy('AC_NAME')
                            ->get();
                } else {
                    $all_ac = DB::table('m_ac')
                            ->where('ST_CODE', $st_code)
                            ->orderBy('AC_NAME')
                            ->get();
                }


                $pdf = PDF::loadView('admin.ac.eci.Expenditure.district-reportPDFhtml', ['user_data' => $d,
                            'totalContestedCandidate' => $result,
                            'cons_no' => $cons_no,
                            'st_code' => $st_code,
                            'statelist' => $statelist,
                            'district' => $district,
                            'districts' => $districts,
                            'all_ac' => $all_ac,
                            'permitstates' => $permitstates,
                            'count' => count($result)
                ]);
                $cur_time = Carbon::now();
                return $pdf->download('DistrictreportPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.eci.Expenditure.district-reportPDFhtml');
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getOfficersmis TRY CATCH ENDS HERE    
    }

    public function getDistrictReportExl(Request $request, $state, $district, $pc) {
        //dd($request->all());
        //PC ECI getOfficersmis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $xss = new xssClean;
                $st_code = $xss->clean_input(base64_decode($state));
                $cons_no = $xss->clean_input(base64_decode($pc));
                $district = $xss->clean_input(base64_decode($district));
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################

                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';
                $district = !empty($district) ? $district : '';
                $districts = DB::table('m_district')->select('DIST_NAME', 'DIST_NO')->where('ST_CODE', $st_code)->get();


                // DB::enableQueryLog();


                $cur_time = Carbon::now();

                \Excel::create('DistrictActiveUsersReportExcel_' . '_' . $cur_time, function($excel) use($st_code, $district, $cons_no, $permitstates) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $district, $cons_no, $permitstates) {
                        $totalContestedCandidate = DB::table('candidate_nomination_detail')
                                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                ->where('candidate_nomination_detail.application_status', '=', '6')
                                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA');

                        if (!empty($st_code) && empty($cons_no) && $st_code != 'All' && empty($district)) {
                            $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
                        }if (!empty($st_code) && !empty($cons_no) && $st_code != 'All' && empty($district)) {
                            $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
                            $totalContestedCandidate->where('candidate_nomination_detail.ac_no', '=', $cons_no);
                        } else if (!empty($st_code) && !empty($district) && empty($cons_no) && $st_code != 'All') {
                            $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);

                            $totalContestedCandidate->join("m_ac", function($join) {
                                $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                                        ->on("m_ac.AC_NO", "=", "candidate_nomination_detail.ac_no");
                            });

                            $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
                        } else if (!empty($st_code) && !empty($district) && !empty($cons_no) && $st_code != 'All') {

                            $totalContestedCandidate->where('candidate_nomination_detail.st_code', '=', $st_code);
                            $totalContestedCandidate->join("m_ac", function($join) {
                                $join->on("m_ac.ST_CODE", "=", "candidate_nomination_detail.st_code")
                                        ->on("m_ac.AC_NO", "=", "candidate_nomination_detail.ac_no");
                            });
                            $totalContestedCandidate->where('m_ac.DIST_NO_HDQTR', '=', $district);
                            $totalContestedCandidate->where('candidate_nomination_detail.ac_no', '=', $cons_no);
                        } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {

                            $totalContestedCandidate->whereIn('candidate_nomination_detail.st_code', $permitstates);
                        }
                        //dd(DB::getQueryLog());
                        $result = $totalContestedCandidate->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                                ->groupBy("candidate_nomination_detail.st_code", 'candidate_nomination_detail.ac_no')
                                ->get();


                        $arr = array();
                        $TotalUsers = 0;
                        $TotalPendingatRO = 0;
                        $TotalPendingatCEO = 0;
                        $TotalPendingatECI = 0;
                        $TotalfiledData = 0;
                        $TotalnotfiledData = 0;
                        $Totalac = 0;
                        $TotalDEONotice = 0;
                        $TotalCEONotice = 0;
                        $Totalfinalcompletedcount = 0;
                        $TotalFinalByDEO = 0;


                        $user = Auth::user();
                        $count = 1;
                        foreach ($result as $key => $listdata) {
                            $cons_no = $listdata->ac_no;
                            //get finalby DEO count
                            $finalbyDEO = $this->eciexpenditureModel->gettotalfinalbyDEO('AC', $listdata->st_code, $cons_no);
                            $TotalFinalByDEO += $finalbyDEO;
                            //get partially pending data count
                            $pendingatROold = $this->eciexpenditureModel->gettotalpartiallypending('AC', $listdata->st_code, $cons_no);
                            //Get Data entry finalize Count 
                            $pendingatCEO = $this->eciexpenditureModel->gettotalfinalbyceo('AC', $listdata->st_code, $cons_no);

                            //Get pendingatDEO Count 
                            $pendingatRO = $listdata->totalcandidate - $pendingatCEO;

                            //Get Data entry finalize Count 
                            $pendingatECI = $this->eciexpenditureModel->gettotalfinalbyeci('AC', $listdata->st_code, $cons_no);

                            //Get filedcount Count 
                            $filedcount = $this->eciexpenditureModel->gettotaldataentryStart('AC', $listdata->st_code, $cons_no);

                            // Get Pending Data Count 
                            $notfiledcount = $listdata->totalcandidate - $filedcount;
                           // $TotalnotfiledData += $notfiledcount;

                            //Get noticeatDEOCount Count 
                            $noticeatDEOCount = $this->eciexpenditureModel->gettotalnoticeatDEO('AC', $listdata->st_code, $cons_no);

                            //Get noticeatCEOCount Count 
                            $noticeatCEOCount = $this->eciexpenditureModel->gettotalnoticeatCEO('AC', $listdata->st_code, $cons_no);

                            //Get finalcompletedcount Count 
                            $finalcompletedcount = $this->eciexpenditureModel->gettotalCompletedbyEci('AC', $listdata->st_code, $cons_no);

                            $st = getstatebystatecode($listdata->st_code);
                            $acbystate = getacbystate($listdata->st_code);
                            $account = count($acbystate);
                            // $Totalac += $account;
                            $acdetails = getacbyacno($listdata->st_code, $listdata->ac_no);
                            $acnoname = $acdetails->AC_NO . '-' . $acdetails->AC_NAME;

                            $st_code = !empty($st_code) ? $st_code : $listdata->st_code;
                            $allStates[] = [
                                'st_code' => $st_code,
                                'ac_no' => $listdata->ac_no,
                            ];

                            // get district start here
                            $detriectdetails = DB::table('m_ac')
                                    ->where('ST_CODE', $listdata->st_code)
                                    ->where('AC_NO', $listdata->ac_no)
                                    ->groupBy('m_ac.DIST_NO_HDQTR')
                                    ->get();
                            $districtids = [];
                            if (!empty($detriectdetails)) {
                                foreach ($detriectdetails as $item) {
                                    $districtids[] = $item->DIST_NO_HDQTR;
                                }
                            }

                            $allDistrict = '';
                            if (!empty($districtids)) {
                                foreach ($districtids as $id) {
                                    $district = getdistrictbydistrictno($listdata->st_code, $id);
                                    $allDistrict .= $district->DIST_NAME . ' ,';
                                }
                            }
                            $alldistricts1 = rtrim($allDistrict, ',');
                            if (empty($alldistricts1) && $alldistricts1 == '') {
                                $districtName = 'N/A';
                            } else {
                                $districtName = $alldistricts1;
                            }


                            // get district end here 



                            $filedcount = !empty($filedcount) ? $filedcount : '0';
                            $finalbyDEO = !empty($finalbyDEO) ? $finalbyDEO : '0';
                            $pendingatRO = !empty($pendingatRO) ? $pendingatRO : '0';
                            $pendingatCEO = !empty($pendingatCEO) ? $pendingatCEO : '0';
                            $pendingatECI = !empty($pendingatECI) ? $pendingatECI : '0';
                            $noticeatDEOCount = !empty($noticeatDEOCount) ? $noticeatDEOCount : '0';
                            $noticeatCEOCount = !empty($noticeatCEOCount) ? $noticeatCEOCount : '0';
                            $finalcompletedcount = !empty($finalcompletedcount) ? $finalcompletedcount : '0';
                            $account = !empty($account) ? $account : '0';
                            $notfiledcount = (!empty($notfiledcount) || $notfiledcount <= 0) ? $notfiledcount : '0';

                            $data = array($count,
                                $st->ST_NAME,
                                $districtName,
                                $acnoname,
                                $listdata->totalcandidate,
                                $filedcount,
                                $notfiledcount,
                                $finalbyDEO,
                                $pendingatRO,
                                $pendingatCEO,
                                $pendingatECI,
                                $finalcompletedcount
                            );
                            $TotalUsers += $listdata->totalcandidate;
                             if ($pendingatECI > 0 || $pendingatCEO >= 0 || $finalcompletedcount > 0) {
                            $pendingatRO = $listdata->totalcandidate - ($pendingatCEO + $pendingatECI + $finalcompletedcount);
                            $TotalPendingatRO += $pendingatRO;
                        }
                           
                            $TotalPendingatCEO += $pendingatCEO;
                            $TotalPendingatECI += $pendingatECI;
                            $TotalDEONotice += $noticeatDEOCount;
                            $TotalCEONotice += $noticeatCEOCount;
                            $Totalfinalcompletedcount += $finalcompletedcount;
                            $TotalnotfiledData += $notfiledcount;
                            $TotalfiledData += $filedcount;
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                         
                       


                        // all state list here
                        if (!empty($allStates)) {

                            if (!empty($allStates[0]['st_code']) && $allStates[0]['st_code'] == "All") {
                                foreach ($permitstates as $item) {
                                    $Totalac += DB::table('m_ac')
                                            ->where('ST_CODE', $item)
                                            ->count();
                                }
                            } else {
                                foreach ($allStates as $item) {
                                    $Totalac += DB::table('m_ac')
                                            ->where('ST_CODE', $item['st_code'])
                                            ->where('AC_NO', $item['ac_no'])
                                            ->count();
                                }
                            }
                        }

                        // end all state here

                        $totalvalues = array(
                            'Total',
                            '',
                            '',
                            $Totalac,
                            $TotalUsers,
                            $TotalfiledData,
                            $TotalnotfiledData,
                            $TotalFinalByDEO, 
                            $TotalPendingatRO,
                            $TotalPendingatCEO,
                            $TotalPendingatECI,
                            $Totalfinalcompletedcount);
                        
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'S.No.:',
                            'State Name',
                            'District Name',
                            'AC NO AND AC NAME',
                            'Total Candidate',
                            'Started',
                            'Not Started',
                            'Finalise By DEO',
                            'Pending At DEO',
                            'Pending At CEO',
                            'Pending At ECI',
                            'Closed/Disqualified/Case Dropped')
                        );
                    });
                })->export('csv');
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI getOfficersmis TRY CATCH ENDS HERE    
    }

// end getOfficersmis function

    public function Alldistrict($stcode) {

        $districts = DB::table('m_district')
                ->select('DIST_NAME', 'DIST_NO')
                ->where('ST_CODE', $stcode)
                ->orderBy('DIST_NAME')
                ->get();

        return $districts;
    }

    // get all ac by state code and district no Start

    function getAllACs(Request $request) {
        if (Auth::check()) {
            $xss = new xssClean;
            $stcode = $xss->clean_input($request->input('state'));
            $district = $xss->clean_input($request->input('district'));
            if (!empty($district)) {
                $all_ac = DB::table('m_ac')
                        ->where('ST_CODE', $stcode)
                        ->where('DIST_NO_HDQTR', $district)
                        ->orderBy('AC_NAME')
                        ->get();
            } else {
                $all_ac = DB::table('m_ac')
                        ->where('ST_CODE', $stcode)
                        ->orderBy('AC_NAME')
                        ->get();
            }
        }
        return $all_ac;
    } // get all ac by state code and district no end
	
	
###############Start Summary Analytical Dash Board Date 16-09-2019 by Niraj ####################
/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 18-09-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getanalyticsummary By ECI fuction     
     */  
    public function getanalyticsummary(Request $request) { 

          // Get the current URL without the query string...
          $namePrefix = \Route::current()->action['prefix'];
          $segments = explode('/', $_SERVER['REQUEST_URI']);
          //dd($segments);
          $nameSuffix = $segments['2'];
           // Get the full URL for the previous request...
           $routesegment=array_slice(explode('/', url()->previous()), -2, 2);

        //AC ECI getOfficersmis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
                $username = $user->officername;
                $st_code = $request->input('state');
                $zonestate = $this->eciexpenditureModel->getzonestate($username);

                if ($zonestate->isEmpty()) {
                    $permitstates = '';
                } else {
                    $permitstates = explode(',', $zonestate[0]->assign_state);
                }

                $permitstate = ($zonestate->isEmpty()) ? '0' : $permitstates;

                if (!empty($permitstate)) {
                    $statelist = $this->eciexpenditureModel->getpermitstate($permitstate);
                } else {
                    $statelist = $this->commonModel->getallstate();
                }
                if ($permitstates != '') {
                    $permitstates[] = "All";
                }

                if (!empty($st_code)) {
                    $st_code = $st_code;
                } elseif (empty($st_code) && !empty($permitstate)) {
                    // $st_code=array_values($permitstate)[0];
                    $st_code = end($permitstates);
                    $allstate = array_pop($permitstates);
                } else {
                    $st_code = 0;
                }
                #########################Code For State Wise Access#####################
                $cons_no = $request->input('ac');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $totalContestedCandidatedata='';
                // echo  $st_code.'pc'.$cons_no; die;
                // DB::enableQueryLog();
                if (!empty($st_code) && $cons_no == '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                } else if (($st_code == '' && $cons_no == '') ||($st_code == '0' && $cons_no == '0')) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.st_code")
                            ->get();
                    }
       
                        // return view('admin.ac.eci.Expenditure.summary-analytical', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist,'nameSuffix' => $nameSuffix, 'count' => count($totalContestedCandidatedata)]);

                          return view('admin.ac.eci.Expenditure.summary-analytical', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'statelist' => $statelist,'nameSuffix' => $nameSuffix,]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//AC ECI getanalyticsummary TRY CATCH ENDS HERE    
    }

// end getanalyticsummary function


####################end Summary Analytical Dashboard #####################################



    public function definalizedcandidate(Request $request, $state, $ac) {
//PC ECI candidateListByfinalizeData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

                $xss = new xssClean;
                $st_code = base64_decode($xss->clean_input($state));
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                if ($st_code == '0' && $cons_no == '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no == '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                } elseif ($st_code != '0' && $cons_no != '0') {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'm_party.CCODE', 'm_party.PARTYNAME', 'candidate_personal_detail.cand_name')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                }

                // dd($filedData);
                return view('admin.ac.eci.Expenditure.de-finalizecandidate', ['user_data' => $d, 'filedData' => $filedData, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($filedData)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ECI filedcandidateData TRY CATCH ENDS HERE   
    }


    public function get_definalize_data(Request $request) { 

        
        //AC ECI getOfficersmis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();

                $id = base64_decode($request->id);
                
                $data = DB::table('expenditure_reports')
                        ->where('candidate_id', '=', $id) 
                        ->get();

                if(count($data) == 0) {
                    return response()->json(['error' => true, 'status' => 401, 'message' => 'Details Not Found !!']); 
                } else {

                    $definal = DB::table('expenditure_reports')
                            ->where('candidate_id', '=', $id)  
                            ->limit(1)  
                            ->update(array('finalized_status' => '0', 'final_by_ro' => '0')); 
                    if($definal) {
                        return response()->json(['error' => false, 'status' => 401, 'message' => 'Success: Candidate Has Been De-finalized !!']); 
                    } else {
                        return response()->json(['error' => true, 'status' => 401, 'message' => 'Something Went Wrong !!']); 
                    }
                }
       
                
            } else {
                return response()->json(['error' => true, 'status' => 402, 'message' => 'User Not Logged-in. !!']); 
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//AC ECI getanalyticsummary TRY CATCH ENDS HERE 

    }


    

}

// end class