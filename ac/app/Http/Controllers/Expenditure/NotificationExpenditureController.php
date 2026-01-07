<?php

namespace App\Http\Controllers\Expenditure;

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
use App\Classes\xssClean;
use Illuminate\Support\Facades\URL;
use App\models\Expenditure\DeoexpenditureModel;
use MPDF;

class NotificationExpenditureController extends Controller {

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
			 ################Add by niraj for exp_alter DB ###########
	    Session::put('DB_ELECTION_ID',$m_election_history->election_id);
        Session::put('DB_MONTH',$m_election_history->month);
        Session::put('DB_YEAR',$m_election_history->year);
        Session::put('DB_CONS_TYPE',$m_election_history->const_type);
        Session::put('DB_ELE_TYPE',$m_election_history->elect_type);
		################end#####################################
           /* config(['database.connections.mysql.host' => '10.247.137.49']);
            config(['database.connections.mysql.database' => $this->expdb]);
           config(['database.connections.mysql.username' => 'gotosuvidha']);
            config(['database.connections.mysql.password' => 'asbhi%supqwe!@1234']); 
			config(['database.connections.mysql.username' => 'suvidhaapp']);
            config(['database.connections.mysql.password' => 'P7$b&n#367BYaRt91']); */


            config(['database.connections.mysql.host' => '10.247.137.43']);
            config(['database.connections.mysql.database' => $this->expdb]);
           
            config(['database.connections.mysql.username' => 'suvidhaapp']);
            config(['database.connections.mysql.password' => 'P7$b&n#367BYaRt91']);
			config(['database.connections.mysql.options' =>[\PDO::ATTR_EMULATE_PREPARES =>true]]);

             

            // config(['database.connections.mysql.host' => '10.247.219.232']);
            // config(['database.connections.mysql.database' => $this->expdb]);
            
            // config(['database.connections.mysql.username' => 'etsuser']);
            // config(['database.connections.mysql.password' => 'Ets@123#']);
            // config(['database.connections.mysql.options' =>[\PDO::ATTR_EMULATE_PREPARES =>true]]);




           // DB::reconnect('mysql');
            DB::purge('mysql');
            DB::connection('mysql');
           return $next($request); 
       });
        ############################################################ 
         $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->expenditureModel = new ExpenditureModel();
        $this->xssClean = new xssClean;
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
                            ->leftjoin('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
							->Where('expenditure_notification.ceo_read_status', '=', '0')
                            ->Where('expenditure_notification.st_code', '=',$st_code)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
				$request->session()->put('countscrutiny', '0');
				$data=array("ceo_read_status"=>1);
				$this->commonModel->updatedata('expenditure_notification','st_code',$st_code,$data);
				
				return view('admin.ac.ceo.Expenditure.scrutiny',['user_data' => $d,'scrutinycandidate' => $scrutinycandidate,"check_filter"=>"0"]); 
          
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
	
	
    public function allscrutiny(Request $request) { //dd($request->all());
        //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
		
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);

                $st_code = $d->st_code;
				$conditions = "";
               
                if(!empty($_GET['ac']))
                {
                  $ac = $_GET['ac'];
                  $conditions .="AND expenditure_reports.constituency_no='$ac' ";
                  
                }
                 $receivefilter=!empty($_GET['receivefilter'])?trim($_GET['receivefilter']):'';
                 if ($receivefilter=='y'  ) {                     
                    $conditions .= " AND date_of_receipt !=''";                     
                    }
                 else if ($receivefilter=='n') {                     
                    $conditions .= " AND date_of_receipt =''";                     
                    }
                     else{
                        $conditions .= "";  
                     }

 
                if(!empty($conditions))
                {

                    $scrutinycandidate = DB::select("select `candidate_personal_detail`.`cand_name`, `candidate_personal_detail`.`candidate_id`, `m_party`.`PARTYNAME`, `candidate_nomination_detail`.`st_code`, `expenditure_reports`.`constituency_no`, `expenditure_reports`.`created_at`, `expenditure_reports`.`final_by_ro`,`expenditure_reports`.`date_of_receipt`, `expenditure_reports`.`report_submitted_date`, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`final_by_ceo`, `expenditure_reports`.`final_action` from `expenditure_notification` inner join `candidate_nomination_detail` on `expenditure_notification`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` inner join `candidate_personal_detail` on `candidate_personal_detail`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` inner join `m_party` on `candidate_nomination_detail`.`party_id` = `m_party`.`CCODE` inner join `m_symbol` on `candidate_nomination_detail`.`symbol_id` = `m_symbol`.`SYMBOL_NO` inner join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`st_code` = '$st_code' and `candidate_nomination_detail`.`application_status` = '6' and `candidate_nomination_detail`.`finalaccepted` = '1' and `candidate_nomination_detail`.`symbol_id` <> '200' and `candidate_personal_detail`.`cand_name` <> 'NOTA' and (`expenditure_reports`.`final_action` = 'Notice Issued' or `expenditure_reports`.`final_action` = 'Reply Issued' or `expenditure_reports`.`final_action` = 'Hearing Done' or `expenditure_reports`.`final_by_ro` = '1') $conditions group by `expenditure_reports`.`candidate_id`");
                    //dd($scrutinycandidate);
                }
                else{

				 $scrutinycandidate = DB::table('expenditure_notification')
                            ->join('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->join('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            // ->join('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                           // ->join('m_ac','m_ac.AC_NO','=','candidate_nomination_detail.ac_no')
                              ->leftjoin("expenditure_reports",function($leftjoin){
          $leftjoin->on("expenditure_reports.candidate_id","=","candidate_nomination_detail.candidate_id")
              ->on("expenditure_reports.constituency_no","=","candidate_nomination_detail.ac_no");
             })
                            ->select('m_party.PARTYNAME','candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.constituency_no',
                                     'expenditure_reports.finalized_status',
                                    'expenditure_reports.date_of_receipt',
                                    'expenditure_reports.report_submitted_date',
                                    'expenditure_reports.final_action',
                                    'expenditure_reports.final_by_ro', 
                                    'expenditure_reports.final_by_ceo',
                                    'expenditure_reports.final_by_eci'
                                    )
                            ->where('expenditure_reports.finalized_status', '=', '1')
                            ->Where('expenditure_notification.st_code', '=',$st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where(function($q) {
                                $q->where('expenditure_reports.final_action', 'Notice Issued')
                                  ->orWhere('expenditure_reports.final_action','Reply Issued')
                                  ->orWhere('expenditure_reports.final_action', 'Hearing Done')
                                  ->orWhere('expenditure_reports.final_by_ro', '1');
                                })
                            ->groupBY('expenditure_reports.candidate_id','expenditure_reports.constituency_no')
                            ->get();

                           

                        }
                
                 //  echo "<pre>";
                 // print_r($scrutinycandidate);
                 // die;

                
				return view('admin.ac.ceo.Expenditure.scrutiny',['user_data' => $d,'scrutinycandidate' => $scrutinycandidate,"check_filter"=>"1"]); 
          //SANJUBEN SOHANLAL REGAR
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
                        ->where('candidate_nomination_detail.st_code', $stcode)
                         
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                       // ->where('m_election_details.CONST_TYPE', '=', 'PC')
                          ->where('m_election_details.CONST_TYPE', '=', 'AC')
                        ->get();               
                         return view('admin.expenditure.proceedprofile', compact('profileData'));
                  
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }
	
	 public function updatecomment(Request $request) {
		 $candidateid=$request->candidateid;
		 $ceo_message=$request->comment;
		
       // dd("updatecomment");
		 $this->commonModel->updatedata('expenditure_notification', 'candidate_id', $candidateid, array("ceo_message" => $ceo_message));
		 $request->session()->flash('success', 'Comment done successfully');
		 return Redirect('/pcceo/allscrutiny');
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






               public function updateReceived(Request $request) {

    $request = (array) $request->all();

    $checkArrayData = [];
    $actionStatus = '';
    unset($request["_token"]);
    $final_action = !empty($request['final_action']) ? $request['final_action'] : '';
    $checkArrayDateids = "";
    if (!empty($request['received'])) {
        foreach ($request['received'] as $candidateId) {
            $exp=explode('-',$candidateId);

            if ($final_action == 'Received') {
    $check_data_recieved = DB::table('expenditure_reports')->where('candidate_id', $exp[0])->where('constituency_no', $exp[1])
                        ->where(function($query) {
                            $query->whereNull('expenditure_reports.date_of_receipt');
                            $query->orwhere('expenditure_reports.date_of_receipt', '=', '');
                        })
                        ->first();
                        //dd($check_data_recieved);
                if (!empty($check_data_recieved)) {
                    $checkArrayDateids = $exp[0];
                     $const_no = $exp[1];
                    $checkArrayData['date_of_receipt'] = date('Y-m-d');
                }
            } else {
                $check_data_recieved = DB::table('expenditure_reports')->where('candidate_id', $exp[0])
                    ->where('constituency_no', $exp[1])
                    ->whereNotNull('expenditure_reports.date_of_receipt')->first();
                //print_r($check_data_recieved);
                if (!empty($check_data_recieved)) {
                    //$checkArrayDateids = $candidateId;
                     $checkArrayDateids = $exp[0];
                     $const_no = $exp[1];
                    $checkArrayData['final_action'] = $final_action;
                    $checkArrayData['final_by_ceo'] = '1';
                }
            }


            if (!empty($checkArrayData)) {
                $actionStatus = DeoexpenditureModel::updateData($checkArrayData, $checkArrayDateids,$const_no);
            }
        }
    } else {

        echo 'Please Checked At Least One.';
        die;
    }

    if (!empty($actionStatus)) {
        echo'Saved successfully';
        die;
    } else {
        echo'Already action done.';
        die;
    }
           }






           public function definalizedcandidate(Request $request) { //dd($request->all());
            //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
            
            try {
                if (Auth::check()) {
                    $user = Auth::user();
                    $uid = $user->id;
                    $d = $this->commonModel->getunewserbyuserid($user->id);
        
                    $st_code = $d->st_code;
                    $conditions = "";
                   
                    if(!empty($_GET['ac']))
                    {
                      $ac = $_GET['ac'];
                      
                      $conditions .="AND expenditure_reports.constituency_no='$ac' ";
                    }
                     $receivefilter=!empty($_GET['receivefilter'])?trim($_GET['receivefilter']):'';
                     if ($receivefilter=='y'  ) {                     
                        $conditions .= " AND date_of_receipt !=''";                     
                        }
                     else if ($receivefilter=='n') {                     
                        $conditions .= " AND date_of_receipt =''";                     
                        }
                         else{
                            $conditions .= "";  
                         }
        
        
                    if(!empty($conditions))
                    {
                        
                        $scrutinycandidate = DB::select("select `candidate_personal_detail`.`cand_name`, `candidate_personal_detail`.`candidate_id`, `m_party`.`PARTYNAME`, `candidate_nomination_detail`.`st_code`, `candidate_nomination_detail`.`ac_no`, `expenditure_reports`.`created_at`, `expenditure_reports`.`final_by_ro`,`expenditure_reports`.`date_of_receipt`, `expenditure_reports`.`report_submitted_date`, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`final_by_ceo`, `expenditure_reports`.`final_action` from `expenditure_notification` 
                            inner join `candidate_nomination_detail` on `expenditure_notification`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` 
                            inner join `candidate_personal_detail` on `candidate_personal_detail`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` 
                             inner join `m_party` on `candidate_nomination_detail`.`party_id` = `m_party`.`CCODE` 
                             inner join `m_symbol` on `candidate_nomination_detail`.`symbol_id` = `m_symbol`.`SYMBOL_NO` 




                             
                             inner join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id`

                             where `candidate_nomination_detail`.`st_code` = '$st_code' and `candidate_nomination_detail`.`application_status` = '6' and `candidate_nomination_detail`.`finalaccepted` = '1' and `candidate_nomination_detail`.`symbol_id` <> '200' and `candidate_personal_detail`.`cand_name` <> 'NOTA' and (`expenditure_reports`.`final_action` = 'Notice Issued' or `expenditure_reports`.`final_action` = 'Reply Issued' or `expenditure_reports`.`final_action` = 'Hearing Done' or `expenditure_reports`.`final_by_ro` = '1') $conditions group by `expenditure_reports`.`candidate_id`");


                         //dd($scrutinycandidate);
                    }
                    else{

                           
                     $scrutinycandidate = DB::table('expenditure_notification')
                                ->join('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                ->join('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')


                                // ->join('expenditure_reports','expenditure_reports.candidate_id','=','candidate_nomination_detail.candidate_id')
                                 ->join('m_ac','m_ac.AC_NO','=','candidate_nomination_detail.ac_no')


                             ->leftjoin("expenditure_reports",function($leftjoin){
          $leftjoin->on("expenditure_reports.candidate_id","=","candidate_nomination_detail.candidate_id")
              ->on("expenditure_reports.constituency_no","=","candidate_nomination_detail.ac_no");
             })



                                ->select('m_party.PARTYNAME','candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.constituency_no','m_ac.AC_NAME',
                                         'expenditure_reports.finalized_status',
                                        'expenditure_reports.date_of_receipt',
                                        'expenditure_reports.report_submitted_date',
                                        'expenditure_reports.final_action',
                                        'expenditure_reports.final_by_ro', 
                                        'expenditure_reports.final_by_ceo',
                                        'expenditure_reports.final_by_eci'
                                        )
                                ->where('expenditure_reports.finalized_status', '=', '1')
                                ->Where('expenditure_notification.st_code', '=',$st_code)
                                ->where('candidate_nomination_detail.application_status', '=', '6')
                                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                ->where(function($q) {
                                    $q->where('expenditure_reports.final_action', 'Notice Issued')
                                      ->orWhere('expenditure_reports.final_action','Reply Issued')
                                      ->orWhere('expenditure_reports.final_action', 'Hearing Done')
                                      ->orWhere('expenditure_reports.final_by_ro', '1');
                                      
                                    })
                                ->groupBY('expenditure_reports.candidate_id','expenditure_reports.constituency_no' )
                                ->get();

        
                            }

                    
                    return view('admin.ac.ceo.Expenditure.definalized',['user_data' => $d,'scrutinycandidate' => $scrutinycandidate,"check_filter"=>"1"]); 
              
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
        
        
        
        public function UpdateStatusData(Request $request) {
        
            $request = (array) $request->all();
           // dd($request);
          
            $checkArrayData = [];
            $actionStatus = '';
            unset($request["_token"]);
            
            $checkArrayDateids = "";
            if (!empty($request['candid'])) {

                $valueis=explode('-',$request['candid']);
                //dd($valueis[0]);
              //  foreach ($request['received'] as $candidateId) {
        
                    
            $check_data_recieved = DB::table('expenditure_reports')->where('candidate_id', $valueis[0])
            ->where('constituency_no', $valueis[1])
            ->first();
                        if (!empty($check_data_recieved)) {
                            $checkArrayDateids = $valueis[0];
                             $const_no = $valueis[1];
                           // $checkArrayData['date_of_receipt'] = date('Y-m-d');
                            $checkArrayData['finalized_status'] = '0';
                        }
                     
        
        
                    if (!empty($checkArrayData)) {
                        $actionStatus = DeoexpenditureModel::updateData($checkArrayData, $checkArrayDateids,$const_no);
                        
        
                        if($actionStatus)
                        {
                            return 1;
                        }
                    }
               // }
            } else {
        
               alert('Please Checked Candidate ');
                die;
            }
        
           
        }
        
        
        
        
        
        













}