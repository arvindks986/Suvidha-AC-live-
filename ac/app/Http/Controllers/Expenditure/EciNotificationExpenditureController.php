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
use App\models\Expenditure\ExpenditureModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\adminmodel\ECIModel;
use App\Classes\xssClean;
use Illuminate\Support\Facades\URL;
use App\models\Expenditure\DeoexpenditureModel;
use MPDF;
use App\models\Expenditure\EciExpenditureModel;
class EciNotificationExpenditureController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public  $expdb;
    public function __construct(){   
     ##############Connect with Expenditure DataBase#############
         $this->middleware(function ($request, $next){
           $DB_DATABASE = strtolower(Session::get('DB_DATABASE'));
          $m_election_history = DB::connection("mysql_database_history")->table("m_election_history")->where("db_name", $DB_DATABASE)->first();
            $this->expdb=$m_election_history->exp_db_name;
           config(['database.connections.mysql.host' => '10.247.137.43']);
            config(['database.connections.mysql.database' => $this->expdb]);
			 ################Add by niraj for exp_alter DB ###########
	    Session::put('DB_ELECTION_ID',$m_election_history->election_id);
        Session::put('DB_MONTH',$m_election_history->month);
        Session::put('DB_YEAR',$m_election_history->year);
        Session::put('DB_CONS_TYPE',$m_election_history->const_type);
        Session::put('DB_ELE_TYPE',$m_election_history->elect_type);
		################end#####################################
           /* config(['database.connections.mysql.username' => 'gotosuvidha']);
            config(['database.connections.mysql.password' => 'asbhi%supqwe!@1234']); */
			config(['database.connections.mysql.username' => 'suvidhaapp']);
            config(['database.connections.mysql.password' => 'P7$b&n#367BYaRt91']);
			config(['database.connections.mysql.options' =>[\PDO::ATTR_EMULATE_PREPARES =>true]]);
           // DB::reconnect('mysql');
            DB::purge('mysql');
            DB::connection('mysql');
           return $next($request); 
       });
        ############################################################ 
        $this->middleware(['auth:admin','auth']);
       // $this->middleware('eci');
	   $this->middleware(function (Request $request, $next) {
          if (!\Auth::check()) {
              return redirect('login')->with(Auth::logout());
          }

          $user = Auth::user();
          /* User Wise Access Start By Niraj-24-07-2019 */
		   switch($user->officername) {
            case 'EAST-1':
             /* Sh.Devesh Kumar*/
            $this->accessstate = array(
                'S04',
                'S27',
                'S26'
            );
                break;
            case 'EAST-2':
            /* Ms.Shalini Sharma */
                $this->accessstate = array(
                    'S18',
                    'S25'
                );
                    break;
            case 'NORTHEAST-1':
            /*  Sh.Vijay Gupta */
                $this->accessstate = array(
                    'S16',
                    'S17',
                    'S21',
                    'S23'
                );
                    break;
            case 'NORTHEAST-2':
              /*  Sh.Karambir */
            $this->accessstate = array(
                'S02',
                'S03',
                'S14',
                'S15'
            );
                break;
            case 'NORTH-1':
              /*  Sh.Sheesh Ram */
            $this->accessstate = array(
                'S08',
                'S09',
                'S19',
                'S20'
            );
                break;
            case 'NORTH-2':
              /*  Sh.Raj Lal Rai */
            $this->accessstate = array(
                'S07',
                'U31',
                'U34',
                'S28'
            );
                break;
            case 'NORTH-3':
            /*  Sh.Rajiv Kumar Sinha */
             $this->accessstate = array(
             'S24'
             );
                break;
            case 'SOUTH-1':
              /*  Sh.Manna Lal Meena */
            $this->accessstate = array(
                'S22',
                'U36'
             );
                break;
              case 'SOUTH-2':
               /*  Smt. Deepmala */
              $this->accessstate = array(
                'S10',
                'S11'
            );
            break;
            case 'SOUTH-3':
            /*  Sh.Sanjay Kumar */
           $this->accessstate = array(
             'S01',
             'S29',
             'U30',
             'U35'
         );
         break;
         case 'WEST-1':
         /*  Sh.Sunil Kumar Vidyarthi */
         $this->accessstate = array(
          'S06',
          'S12',
          'U33'
          );
         break;
         case 'WEST-2':
         /*  Sh.Niranjan Kr.Sharma */
         $this->accessstate = array(
          'S05',
          'S13',
          'U32'
          );
         break;
         case 'ECIEXPEND':
         $this->accessstate = '0';
            break;
            default:
            $this->accessstate = '';
        }
		   /* User Wise Access End By Niraj-24-07-2019 */
		  
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
        $this->ECIModel = new ECIModel();
        $this->expenditureModel = new ExpenditureModel();
        $this->eciexpenditureModel = new EciExpenditureModel();
		
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

// end MasterData function  
    /**
     * @author Devloped By : Shishir Sharma
     * @author Devloped Date : 22-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return notificationmessage By DEO fuction     
     */
	 
	  public function scrutiny(Request $request) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
		
		DB::enableQueryLog();
		
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $d->st_code;
               
                $scrutinycandidate = DB::table('expenditure_notification')
					        ->leftjoin('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->leftjoin('expenditure_reports','expenditure_reports.candidate_id','expenditure_notification.candidate_id','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ceo')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
							             ->Where('expenditure_notification.eci_read_status', '=', '0')
                          ->Where('expenditure_reports.final_by_ceo', '=', '1')
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
				$request->session()->put('ecicountscrutiny', '0');
				$data=array("eci_read_status"=>1);
				$this->commonModel->updatedata('expenditure_notification','eci_read_status',0,$data);
                //dd(DB::getQueryLog());
                //echo "<pre>";
				//print_r($totalContestedCandidate);
				//die;
				return view('admin.ac.eci.Expenditure.eciscrutiny',['user_data' => $d,'scrutinycandidate' => $scrutinycandidate,"check_filter"=>"0"]); 
          
                
              } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE   
    }
	
	
		public function allscrutiny(Request $request) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
		DB::enableQueryLog(); // Enable query log
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                 // $st_code = $d->st_code;
               $conditions = "";
                if(!empty($_GET['state']))
                {
                  $state = $_GET['state'];
                  $conditions .="AND candidate_nomination_detail.st_code='$state' ";
                }

                if(!empty($_GET['ac']))
                {
                  $ac = $_GET['ac'];
                  $conditions .="AND candidate_nomination_detail.ac_no='$ac' ";
                }

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username=$user->officername;
            $state = $request->input('state');
               // $permitstate=$this->accessstate;
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
                   $statelist = getallstate();
                }
                if(!empty($state)){
                    $state=$state;
                }elseif(empty($state) && !empty($permitstate)){
                    $state=array_values($permitstate)[0];
                }else {
                    $state=0;
                }
               
             #########################Code For State Wise Access#####################

                if(!empty($conditions))
                {
                  $scrutinycandidate = DB::select("select `candidate_nomination_detail`.`candidate_id`,`expenditure_reports`.`created_at`,`expenditure_reports`.`created_at` as form_fill_start, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`report_submitted_date`,
                     `expenditure_reports`.`created_at`, `expenditure_reports`.`last_date_prescribed_acct_lodge`, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`final_by_ro`, `expenditure_reports`.`date_of_receipt_eci`, `expenditure_reports`.`final_action`, `expenditure_reports`.`final_by_eci`, `expenditure_reports`.`candidate_id`, `expenditure_reports`.`ST_CODE` as `st_code`, `expenditure_reports`.`constituency_no` as `ac_no`, `candidate_personal_detail`.`candidate_id`, `candidate_personal_detail`.`cand_name`, `candidate_nomination_detail`.`candidate_id`, `candidate_nomination_detail`.`application_status`, `candidate_nomination_detail`.`finalaccepted`, `m_party`.`CCODE`, `m_party`.`PARTYNAME` from `expenditure_notification` inner join `candidate_nomination_detail` on `expenditure_notification`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` inner join `candidate_personal_detail` on `candidate_personal_detail`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` inner join `m_party` on `candidate_nomination_detail`.`party_id` = `m_party`.`CCODE` left join `m_symbol` on `candidate_nomination_detail`.`symbol_id` = `m_symbol`.`SYMBOL_NO` inner join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = '6' and `candidate_nomination_detail`.`finalaccepted` = '1' and (`expenditure_reports`.`final_action` = 'Notice Issued' or `expenditure_reports`.`final_action` = 'Reply Issued' or `expenditure_reports`.`final_action` = 'Hearing Done' or `expenditure_reports`.`final_by_ceo` = '1') $conditions group by `expenditure_reports`.`candidate_id`");
                }
                else{
                $scrutinycandidate = DB::table('expenditure_notification')
                ->select('candidate_nomination_detail.candidate_id',
                    'expenditure_reports.created_at','expenditure_reports.created_at as form_fill_start','expenditure_reports.finalized_status',
                                 'expenditure_reports.report_submitted_date',
                                 'expenditure_reports.last_date_prescribed_acct_lodge',
                                'expenditure_reports.report_submitted_date','expenditure_reports.finalized_status','expenditure_reports.final_by_ro','expenditure_reports.date_of_receipt_eci','expenditure_reports.final_action','expenditure_reports.final_by_eci','expenditure_reports.candidate_id','expenditure_reports.ST_CODE as st_code','expenditure_reports.constituency_no as ac_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                  ->join('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                           ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                             ->join('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('expenditure_reports.ST_CODE', '=', $state)
                           ->where(function($q) {
                                $q->where('expenditure_reports.final_action', 'Notice Issued') 
                                  ->orWhere('expenditure_reports.final_action','Reply Issued')
                                  ->orWhere('expenditure_reports.final_action', 'Hearing Done')
                                  ->orWhere('expenditure_reports.final_by_ceo', '1');
                                })
                           ->groupBY('expenditure_reports.candidate_id')
                            ->get();
                      } 
                    //print_r($scrutinycandidate);DIE;     
				//dd(DB::getQueryLog()); // Show results of log
				
				$request->session()->put('ecicountscrutiny', '0');
				$data=array("eci_read_status"=>1);
				$this->commonModel->updatedata('expenditure_notification','st_code',$state,$data);
				
				return view('admin.ac.eci.Expenditure.eciscrutiny',['user_data' => $d,'scrutinycandidate' => $scrutinycandidate,"check_filter"=>"1",'statelist' => $statelist,'st_code' => $state]); 
          
                //echo "<pre>";
				//print_r($totalContestedCandidate);
				//die;
              } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE   
    }
    public function allscrutinyByPass(Request $request) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
		DB::enableQueryLog(); // Enable query log
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                 // $st_code = $d->st_code;
               $conditions = "";
                if(!empty($_GET['state']))
                {
                  $state = $_GET['state'];
                  $conditions .="AND candidate_nomination_detail.st_code='$state' ";
                }

                if(!empty($_GET['ac']))
                {
                  $ac = $_GET['ac'];
                  $conditions .="AND candidate_nomination_detail.ac_no='$ac' ";
                }

                #########################Code For State Wise Access By Niraj date 23-07-2019#####################
            $username=$user->officername;
            $state = $request->input('state');
               // $permitstate=$this->accessstate;
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
                   $statelist = getallstate();
                }
                if(!empty($state)){
                    $state=$state;
                }elseif(empty($state) && !empty($permitstate)){
                    $state=array_values($permitstate)[0];
                }else {
                    $state=0;
                }
               
             #########################Code For State Wise Access#####################

                if(!empty($conditions))
                {
                  $scrutinycandidate = DB::select("select `candidate_nomination_detail`.`candidate_id`,`expenditure_reports`.`created_at`,`expenditure_reports`.`created_at` as form_fill_start, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`report_submitted_date`,
                     `expenditure_reports`.`created_at`, `expenditure_reports`.`last_date_prescribed_acct_lodge`, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`final_by_ro`, `expenditure_reports`.`date_of_receipt_eci`, `expenditure_reports`.`final_action`, `expenditure_reports`.`final_by_eci`, `expenditure_reports`.`candidate_id`, `expenditure_reports`.`ST_CODE` as `st_code`, `expenditure_reports`.`constituency_no` as `ac_no`, `candidate_personal_detail`.`candidate_id`, `candidate_personal_detail`.`cand_name`, `candidate_nomination_detail`.`candidate_id`, `candidate_nomination_detail`.`application_status`, `candidate_nomination_detail`.`finalaccepted`, `m_party`.`CCODE`, `m_party`.`PARTYNAME` from `expenditure_notification` inner join `candidate_nomination_detail` on `expenditure_notification`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` inner join `candidate_personal_detail` on `candidate_personal_detail`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` inner join `m_party` on `candidate_nomination_detail`.`party_id` = `m_party`.`CCODE` left join `m_symbol` on `candidate_nomination_detail`.`symbol_id` = `m_symbol`.`SYMBOL_NO` inner join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = '6' and `candidate_nomination_detail`.`finalaccepted` = '1' and (`expenditure_reports`.`final_action` = 'Notice Issued' or `expenditure_reports`.`final_action` = 'Reply Issued' or `expenditure_reports`.`final_action` = 'Hearing Done' or `expenditure_reports`.`final_by_ro` = '1'  ) $conditions group by `expenditure_reports`.`candidate_id`");
                }
                else{
                $scrutinycandidate = DB::table('expenditure_notification')
                ->select('candidate_nomination_detail.candidate_id',
                    'expenditure_reports.created_at','expenditure_reports.created_at as form_fill_start','expenditure_reports.finalized_status',
                                 'expenditure_reports.report_submitted_date',
                                 'expenditure_reports.last_date_prescribed_acct_lodge',
                                'expenditure_reports.report_submitted_date','expenditure_reports.finalized_status','expenditure_reports.final_by_ro','expenditure_reports.date_of_receipt_eci','expenditure_reports.final_action','expenditure_reports.final_by_eci','expenditure_reports.candidate_id','expenditure_reports.ST_CODE as st_code','expenditure_reports.constituency_no as ac_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                  ->join('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                           ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                             ->join('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->Where('expenditure_reports.ST_CODE', '=', $state)
                           ->where(function($q) {
                                $q->where('expenditure_reports.final_action', 'Notice Issued') 
                                  ->orWhere('expenditure_reports.final_action','Reply Issued')
                                  ->orWhere('expenditure_reports.final_action', 'Hearing Done')
                                  ->orWhere('expenditure_reports.final_by_ro', '1');
              
                                })
                           ->groupBY('expenditure_reports.candidate_id')
                            ->get();
                      } 
                    //print_r($scrutinycandidate);DIE;     
				//dd(DB::getQueryLog()); // Show results of log
				
				$request->session()->put('ecicountscrutiny', '0');
				$data=array("eci_read_status"=>1);
				$this->commonModel->updatedata('expenditure_notification','st_code',$state,$data);
				
				return view('admin.ac.eci.Expenditure.eciscrutiny-byepass',['user_data' => $d,'scrutinycandidate' => $scrutinycandidate,"check_filter"=>"1",'statelist' => $statelist,'st_code' => $state]); 
          
                //echo "<pre>";
				//print_r($totalContestedCandidate);
				//die;
              } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE   
    }
	
    public function proceedprofile(Request $request) {
		
        try {
            if (Auth::check()) {
                $user = Auth::user();
				
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $stcode = $d->st_code;
               
                $candidate_id = $_GET['candidate_id'];
				
                $profileData = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                        ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.pc_no");
                        })
                        
                         
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                        ->where('m_election_details.CONST_TYPE', '=', 'PC')
                        ->get(); 

						
                         return view('admin.expenditure.proceedprofileeci', compact('profileData'));
                  
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }
	
	 public function updatecomment(Request $request) {
		 $candidateid=$request->candidateid;
		 $comment=$request->comment;
		
		 $this->commonModel->updatedata('expenditure_notification', 'candidate_id', $candidateid, array("eci_message" => $comment));
		 $request->session()->flash('success', 'Comment done successfully');
		 return Redirect('/eci/eciallscrutiny');
	 }
	 


	 public function printScrutinyReport($candidateId) {

       if (Auth::check()) {
           $user = Auth::user();

           $candidate_id = base64_decode($candidateId);
//           $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
//        $candiatePcName =  !empty($candiatePcName)? $candiatePcName->PC_NAME:'---';

           $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id);


           $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id);
           $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
           $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

           $pdf = MPDF::loadView('admin.expenditure.pdf_ro', compact('expensesourecefundbyitem', 'scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem'));
           return $pdf->stream('Ro.scrunity-report.pdf');

           //return view('admin.expenditure.pdf_ro');
       } else {
           return redirect('/officer-login');
       }
   }
  // received notification
  public function receivedNotification(Request $request) { 
    try {
          
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);              
            $state = !empty($_GET['state'])?($_GET['state']):0;
            $ac = !empty($_GET['ac'])? trim($_GET['ac']):0;
             $case = !empty($_GET['case'])? trim($_GET['case']):"";
             $queryString = !empty($state) ? '?state='.$state : "?state";
              
             if(empty($state)){
                $queryString=empty($state)? '?ac='.trim($ac) : "?ac=";
             }
             elseif(!empty($state) && !empty($pc)){
                $queryString = !empty($state) && !empty($ac) ? $queryString.'&ac='.trim($ac) : "&ac=";
             }else{

             }
             
           



             $queryString = !empty($case) ? $queryString.'&case='.trim($case) : "";
            Session::put("queryString", $queryString);
            if(!empty($state) && empty($ac) )
            {
                        $scrutinycandidate = DB::table('expenditure_notification')
                        ->select('expenditure_reports.created_at','expenditure_reports.created_at as form_fill_start','expenditure_reports.finalized_status',
                                         'expenditure_reports.report_submitted_date',
                                        'expenditure_reports.report_submitted_date','expenditure_reports.finalized_status','expenditure_reports.final_by_ro','expenditure_reports.date_of_receipt_eci','expenditure_reports.final_action','expenditure_reports.final_by_eci','expenditure_reports.candidate_id','expenditure_reports.ST_CODE as st_code','expenditure_reports.constituency_no as ac_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->join('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                   ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                     ->join('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('expenditure_reports.ST_CODE', '=', $state)
                                   ->where(function($q) {
                                        $q->where('expenditure_reports.final_action', 'Notice Issued') 
                                          ->orWhere('expenditure_reports.final_action','Reply Issued')
                                          ->orWhere('expenditure_reports.final_action', 'Hearing Done')
                                          ->orWhere('expenditure_reports.final_by_ceo', '1');
                                        })
                                   ->groupBY('expenditure_reports.candidate_id')
                                    ->get();
            }
             elseif(!empty($state) && !empty($ac) )
            {
               
                        $scrutinycandidate = DB::table('expenditure_notification')
                        ->select('expenditure_reports.created_at','expenditure_reports.created_at as form_fill_start','expenditure_reports.finalized_status',
                                         'expenditure_reports.report_submitted_date',
                                        'expenditure_reports.report_submitted_date','expenditure_reports.finalized_status','expenditure_reports.final_by_ro','expenditure_reports.date_of_receipt_eci','expenditure_reports.final_action','expenditure_reports.final_by_eci','expenditure_reports.candidate_id','expenditure_reports.ST_CODE as st_code','expenditure_reports.constituency_no as ac_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->join('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                   ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                     ->join('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->Where('expenditure_reports.ST_CODE', '=', $state) 
                                    ->Where('expenditure_reports.constituency_no', '=', $ac) 
                                   ->where(function($q) {
                                        $q->where('expenditure_reports.final_action', 'Notice Issued') 
                                          ->orWhere('expenditure_reports.final_action','Reply Issued')
                                          ->orWhere('expenditure_reports.final_action', 'Hearing Done')
                                          ->orWhere('expenditure_reports.final_by_ceo', '1');
                                        })
                                   ->groupBY('expenditure_reports.candidate_id')
                                    ->get();
              
              
            }
            else{
                $scrutinycandidate = DB::table('expenditure_notification')
                ->select('expenditure_reports.created_at','expenditure_reports.created_at as form_fill_start','expenditure_reports.finalized_status',
                                 'expenditure_reports.report_submitted_date',
                                'expenditure_reports.report_submitted_date','expenditure_reports.finalized_status','expenditure_reports.final_by_ro','expenditure_reports.date_of_receipt_eci','expenditure_reports.final_action','expenditure_reports.final_by_eci','expenditure_reports.candidate_id','expenditure_reports.ST_CODE as st_code','expenditure_reports.constituency_no as ac_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                            ->join('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                           ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                             ->join('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                           ->where(function($q) {
                                $q->where('expenditure_reports.final_action', 'Notice Issued') 
                                  ->orWhere('expenditure_reports.final_action','Reply Issued')
                                  ->orWhere('expenditure_reports.final_action', 'Hearing Done')
                                  ->orWhere('expenditure_reports.final_by_ceo', '1');
                                })
                           ->groupBY('expenditure_reports.candidate_id')
                            ->get();
              }
          
            
            
            return view('admin.ac.eci.Expenditure.receivedscrutiny',['user_data' => $d,'scrutinycandidate' => $scrutinycandidate]); 
          
          } else {
            return redirect('/officer-login');
        }
    } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
    } 
}
public function updateReceived(Request $request) {
              
        $request = (array) $request->all(); 
         $queryString=!empty(Session::get('queryString'))? Session::get('queryString'):'';
        $namePrefix = \Route::current()->action['prefix'];              
         $checkArrayData=[];
         $actionStatus='';
         unset($request["_token"]);
         
                  $final_action=!empty($request['final_action'])?$request['final_action']:'';  
         //$request['received'] = explode(',',$request['received']);
       // print_r($final_action); 
         $checkArrayDateids="";
        if(!empty($request['received'])){  
            foreach($request['received'] as $candidateId){
               
                if($final_action=='Received'){
                    $check_data_recieved = DB::table('expenditure_reports')->where('candidate_id',$candidateId)
                      ->where(function($query) {
                      $query->whereNull('expenditure_reports.date_of_receipt_eci');
                      $query->orwhere('expenditure_reports.date_of_receipt_eci', '=','');
                      })
                      ->first();
                    if(!empty($check_data_recieved)){ 
                    $checkArrayDateids=$candidateId; 
                    $checkArrayData['date_of_receipt_eci']=date('Y-m-d');
                    $checkArrayData['date_of_receipt']=date('Y-m-d');
                    $checkArrayData['date_of_sending_ceo']=date('Y-m-d'); 
                    }     

                }
                else{ 
                      $check_data_recieved = DB::table('expenditure_reports')->where('candidate_id',$candidateId)->whereNotNull('expenditure_reports.date_of_receipt_eci')->first();
                      //print_r($check_data_recieved);
                      if(!empty($check_data_recieved)){
                        $checkArrayDateids=$candidateId;
                      $checkArrayData['final_action'] = $final_action;
                      $checkArrayData['final_by_eci']='1';
                    }
                } 

                
                if(!empty($checkArrayData)){
              $actionStatus = DeoexpenditureModel::updateDatacandidate($checkArrayData, $checkArrayDateids);
                }

              }

                
                
            
              }else{
    //Session::put('error', "Please Checked At Least One.");
   echo 'Please Checked At Least One.';die;
              //  return redirect($namePrefix . '/receivedNotification'.$queryString);
              
}

            if(!empty($actionStatus)){
                echo'Saved successfully';die;
                // Session::put('message', "Saved successfully");
               // return redirect($namePrefix . '/receivedNotification'.$queryString);
            }else{
                echo'Already action done.';die;
             // Session::put('message', "No change");
              //  return redirect($namePrefix . '/receivedNotification'.$queryString);
            }
               // return redirect($namePrefix . '/receivedNotification'.$queryString);
    
 
              
    }
// end here 
}