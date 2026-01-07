<?php
    namespace App\Http\Controllers\Expenditure;
	ini_set('memory_limit', '-1');
    use Illuminate\Http\Request;
    use Carbon\CarbonPeriod;
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
    use MPDF;
    use File;
    use App\commonModel; 
    use App\models\Expenditure\ExpenditureModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\Classes\xssClean;
    use Illuminate\Support\Facades\URL;
    use App\models\Expenditure\DeoexpenditureModel;
	
class DeoExpenditureController extends Controller {
  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public static $fileLocation;
  public static $fileName;
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
         /*  config(['database.connections.mysql.host' => '10.247.137.49']);
           config(['database.connections.mysql.username' => 'suvidhaapp']);
            config(['database.connections.mysql.password' => 'P7$b&n#367BYaRt91']);

            */
            
                config(['database.connections.mysql.host' => '10.247.137.43']);
            config(['database.connections.mysql.database' => $this->expdb]);
         
            config(['database.connections.mysql.username' => 'suvidhaapp']);
            config(['database.connections.mysql.password' => 'P7$b&n#367BYaRt91']);
			
			config(['database.connections.mysql.options' =>[\PDO::ATTR_EMULATE_PREPARES =>true]]);


            // config(['database.connections.mysql.host' => '10.247.137.49']);
            // config(['database.connections.mysql.database' => $this->expdb]);
           
            // config(['database.connections.mysql.username' => 'suvidhaapp']);
            // config(['database.connections.mysql.password' => 'P7$b&n#367BYaRt91']);
            // config(['database.connections.mysql.options' =>[\PDO::ATTR_EMULATE_PREPARES =>true]]);







           // DB::reconnect('mysql');
            DB::purge('mysql');
            DB::connection('mysql');
           return $next($request); 
       });
        ############################################################ 
    $this->middleware(['auth:admin','auth']);
        $this->middleware('deo');
        $this->commonModel = new commonModel();
        $this->expenditureModel = new ExpenditureModel();
        $this->xssClean = new xssClean;
        self::$fileLocation=public_path() . '/uploads1/ExpenditureReportAC/';
        self::$fileName='/uploads1/ExpenditureReportAC/';

          $path = self::$fileLocation;
          if(!File::exists($path)){
        File::makeDirectory($path, 0777, true, true);
         }

     }

  /**
  * Show the application dashboard.
  *
  * @return \Illuminate\Http\Response
  */

   protected function guard(){
        return Auth::guard();
    }   
  
  /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 01-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return MasterData By DEO fuction     
  */
   
  public function getmasterdata(Request $request){
    if(Auth::check()){

   $date = Carbon::parse('20-12-2022');
 


     $user = Auth::user();
     $d=$this->commonModel->getunewserbyuserid($user->id);
     $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
     //dd($d->election_id);
    /* $all_ac =DB::table('dist_pc_mapping')
     ->join("m_ac",function($join){
      $join->on("m_ac.ST_CODE","=","dist_pc_mapping.ST_CODE")
          ->on("m_ac.AC_NO","=","dist_pc_mapping.AC_NO");
        })
     ->where('dist_pc_mapping.st_code',$d->st_code)
	   ->where('dist_pc_mapping.dist_no',$d->dist_no)
    // ->groupBy('PC_NAME_EN')
     ->get();*/
	  $all_ac =DB::table('m_ac')
     ->leftjoin("candidate_nomination_detail",function($join){
      $join->on("candidate_nomination_detail.ST_CODE","=","m_ac.ST_CODE")
          ->on("candidate_nomination_detail.ac_no","=","m_ac.AC_NO");
        })
     ->where('m_ac.ST_CODE',$d->st_code)
	 ->where('m_ac.DIST_NO_HDQTR',$d->dist_no)
     ->groupBy('candidate_nomination_detail.ac_no')
     ->get();

     if($d->election_id=='18') {
      if (Carbon::parse($date)->gte(Carbon::today() ))
   {
   
   return view('admin.ac.deo.Expenditure.testpage',['user_data' => $d,'ele_details' => $ele_details,'all_ac' => $all_ac]);
   }
}
 
    return view('admin.ac.deo.Expenditure.scrutinyExpenditure',['user_data' => $d,'ele_details' => $ele_details,'all_ac' => $all_ac]);
  }
  else {
      return redirect('/officer-login');
    }   
  }   // end MasterData function  
/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 02-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return getcandidateListbyDeo By DEO fuction     
  */
  public function getcandidateListbyDeo(request $request,$ac=''){  
   //dd($request->all());
    // DB::enableQueryLog();
   if(Auth::check()){
    $user = Auth::user();
    $uid = $user->id;
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
  
    $xss = new xssClean;
    $cons_no=base64_decode($xss->clean_input($ac));
    $cons_no=!empty($cons_no) ? $cons_no : '';
	
	$district = $request->input('dist_no');
    $stcode = $d->st_code;
    $ac_no = $request->input('ac');
	$ac_no=!empty($cons_no) ? $cons_no : $ac_no;
	 
    $candList =DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id') 
        ->join("m_election_details",function($join){
          $join->on("m_election_details.st_code","=","candidate_nomination_detail.st_code")
              ->on("m_election_details.CONST_NO","=","candidate_nomination_detail.ac_no");
             })
         ->leftjoin("expenditure_reports",function($leftjoin){
          $leftjoin->on("expenditure_reports.candidate_id","=","candidate_nomination_detail.candidate_id")
              ->on("expenditure_reports.constituency_no","=","candidate_nomination_detail.ac_no");
             })

      


                     ->select('candidate_nomination_detail.*','candidate_personal_detail.*','m_election_details.*','expenditure_reports.finalized_status','expenditure_reports.updated_at as finalized_date','expenditure_reports.final_by_ro','expenditure_reports.date_of_declaration','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_receipt','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.date_of_receipt_eci','expenditure_reports.date_of_sending_deo','expenditure_reports.report_submitted_date', 'expenditure_reports.final_action') 
        ->where('candidate_nomination_detail.st_code', $stcode)
        ->where('candidate_nomination_detail.ac_no', $ac_no)
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.party_id','<>','1180')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('m_election_details.CONST_TYPE','=','AC')
        ->groupBy('candidate_nomination_detail.candidate_id')
        ->get();

        if(!empty($candList))
                       {
                        foreach ($candList as $value) {
                                $getLog = DB::table('expenditure_logs')->where('created_by',$uid)->where('candidate_id',$value->candidate_id)->count();   
                                $value->count_by_ro = $getLog;
                        }
                       }

        // add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj
   return view('admin.ac.deo.Expenditure.scrutinyExpenditure',['user_data' => $d,'ele_details' => $ele_details,'candList' => $candList,'ac_no' => $ac_no,'resultDeclarationDate'=>$resultDeclarationDate]);
    }else {
     return redirect('/officer-login');
   }   
  } //end getcandidateListbyDeo


  /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 02-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return getcandidateDetails By Candidate_id fuction     
  */
   
  public function getcandidateDetails(Request $request){
//dd($request->all());
    if(Auth::check()){
     $user = Auth::user();
     $d=$this->commonModel->getunewserbyuserid($user->id);
     $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
    // dd($ele_details);
    /* $all_ac =DB::table('dist_pc_mapping')
     ->join("m_ac",function($join){
      $join->on("m_ac.ST_CODE","=","dist_pc_mapping.ST_CODE")
          ->on("m_ac.AC_NO","=","dist_pc_mapping.AC_NO");
        })
     ->where('dist_pc_mapping.st_code',$d->st_code)
	   ->where('dist_pc_mapping.dist_no',$d->dist_no)
    // ->groupBy('PC_NAME_EN')
     ->get();*/
   //dd($all_ac);
    return view('admin.ac.deo.Expenditure.ExpDeoReport',['user_data' => $d,'ele_details' => $ele_details]);
  }
  else {
      return redirect('/officer-login');
    }   
  }   // end MasterData function  
  
  /**
     * Calculate percetage between the numbers
     *    
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 07-05-19
     */
    public function get_percentage($total, $number) {
        if ($total > 0) {

            return round($number / ($total / 100), 2);
        } else {
            return 0;
        }
    }

//end number
     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return dashboard By AC DEO fuction     
   */
  public function dashboard(Request $request){
     //AC DEO dashboard TRY CATCH STARTS HERE
     try{
      if(Auth::check()){
            $user = Auth::user();
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
            
            $st_code=$d->st_code;
            $cons_no=$d->ac_no;
            $dist_no=$d->dist_no;
            $totalContestedCandidate = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
            ->where('candidate_nomination_detail.st_code','=',$st_code)
            ->where('candidate_nomination_detail.district_no','=',$dist_no) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->count();
            $totalElectedCandidate=DB::table('winning_leading_candidate')
            ->join('candidate_nomination_detail',
                    'candidate_nomination_detail.candidate_id',
                    '=',
                    'winning_leading_candidate.candidate_id')
            ->where('winning_leading_candidate.st_code','=',$st_code)                             
            ->where('candidate_nomination_detail.district_no','=',$dist_no)
            ->count();
             //Get Data entry Start Count 
            $startdatacount=$this->expenditureModel->gettotaldataentryStart('AC',$st_code,$dist_no);
             //dd($startdatacount);
            //Get Data entry Start Count %
            $Percent_startdataentry=$this->get_percentage($totalContestedCandidate,$startdatacount);
            
            //Get Data entry finalize Count 
            $finaldatacount=$this->expenditureModel->gettotaldataentryFinal('AC',$st_code,$dist_no);
            //Get Data entry finalize Count %
            $Percent_finaldatacount=$this->get_percentage($totalContestedCandidate,$finaldatacount);
           
            //Get Data entry finalize Count 
            $logedaccount=$this->expenditureModel->gettotallogedAccount('AC',$st_code,$dist_no);
            //Get Data entry finalize Count %
            $Percent_logedaccount=$this->get_percentage($totalContestedCandidate,$logedaccount);

                //Get Data entry finalize Count 
            $notintimeaccount=$this->expenditureModel->gettotalNotinTime('AC',$st_code,$dist_no);
            //Get Data entry finalize Count %
            $Percent_notintimeaccount=$this->get_percentage($totalContestedCandidate,$notintimeaccount);
           

            //Get Defects in format Count 
            $formateDefectscount=$this->expenditureModel->gettotalDefectformats('AC',$st_code,$dist_no);
            //Get Defects in format Count %
            $Percent_formateDefectscount=$this->get_percentage($totalContestedCandidate,$formateDefectscount);

              //Get Defects in format Count 
            $expenseunderstated=$this->expenditureModel->gettotalexpenseUnderStated('AC',$st_code,$dist_no);
      
      $expenseunderstated=count($expenseunderstated);
              //Get Defects in format Count %
          //  dd($totalContestedCandidate);
            $Percent_expenseunderstated=$this->get_percentage($totalContestedCandidate,$expenseunderstated);

            //Get total fund from party
            $partyFund=$this->expenditureModel->gettotalPartyfund('AC',$st_code,$dist_no);
        
            $otherSourcesFund=$this->expenditureModel->gettotalOtherSourcesfund('AC',$st_code,$dist_no);
            //dd($otherSourcesFund);
           
            $totalFund=($partyFund->total_partyfund + $otherSourcesFund->total_otherSourcesfund);
            //Get party fund %
            $Percent_partyFund=$this->get_percentage($totalFund,$partyFund->total_partyfund);
              //Get OtherSources fund %
            $Percent_OthersourcesFund=$this->get_percentage($totalFund,$otherSourcesFund->total_otherSourcesfund);
// return /non return start here
$totalElectedCandidate=!empty($totalElectedCandidate)?$totalElectedCandidate:0;
$returncount = $this->expenditureModel->gettotalreturnByDistrict('AC', $st_code, $dist_no,'Returned');
            
$totalNominationCandiate=$totalContestedCandidate-$totalElectedCandidate;

$nonreturncount = $this->expenditureModel->gettotalreturnByDistrict('AC', $st_code, $dist_no,'Non-Returned');

 $returncount=!empty($returncount)?count($returncount):0;
 $nonreturncount=!empty($nonreturncount)?count($nonreturncount):0; 

//Getfinal by eci Count %
$Percent_returncount = $this->get_percentage($totalElectedCandidate, $returncount);
$Percent_nonreturncount = $this->get_percentage($totalNominationCandiate, $nonreturncount);
// end here return /non return
return view('admin.ac.deo.Expenditure.dashboard',['user_data' => $d,
'startdatacount' => $startdatacount,
'Percent_startdataentry' => $Percent_startdataentry,
'finaldatacount' => $finaldatacount,
'Percent_finaldatacount' => $Percent_finaldatacount,
'formateDefectscount' => $formateDefectscount,
'Percent_formateDefectscount' => $Percent_formateDefectscount,
'expenseunderstated' => $expenseunderstated,
'Percent_expenseunderstated' => $Percent_expenseunderstated,
'Percent_partyFund' => $Percent_partyFund,
'Percent_OthersourcesFund' => $Percent_OthersourcesFund,
'edetails'=>$ele_details,'logedaccount'=>$logedaccount,
'Percent_logedaccount'=>$Percent_logedaccount,
'notintimeaccount'=>$notintimeaccount,
'Percent_notintimeaccount'=>$Percent_notintimeaccount,
'returncount'=>$returncount,
'Percent_returncount'=>$Percent_returncount,
'nonreturncount'=>$nonreturncount,
'Percent_nonreturncount'=>$Percent_nonreturncount,
    ]); 
           
        }
        else {
            return redirect('/officer-login');
        }
        } catch (Exception $ex) {
          return Redirect('/internalerror')->with('error', 'Internal Server Error');
         
         }//AC DEO dashboard TRY CATCH ENDS HERE    

      }   // end dashboard function

    /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListBydataentryStart By AC DEO fuction     
   */
  public function candidateListBydataentryStart(Request $request){
    //AC DEO candidateListBydataentryStart TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
            $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           
           $DataentryStartCandList = DB::table('expenditure_reports')
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')   
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no',$dist_no)
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
		   ->groupBy('expenditure_reports.id')
           ->get();
           //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.dataentrystart-report',['user_data' => $d,'DataentryStartCandList' => $DataentryStartCandList,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListBydataentryStart TRY CATCH ENDS HERE   
    }   // end dataentry start function
   
     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListByfinalizeData By ROPC fuction     
   */
  public function candidateListByfinalizeData(Request $request){
    //AC DEO candidateListByfinalizeData TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           
          
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $finalCandList = DB::table('expenditure_reports')
		    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no',$dist_no)
           ->where('candidate_nomination_detail.application_status','=','6')
             ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->where('expenditure_reports.finalized_status','=','1') 
		    //->groupBy('expenditure_reports.candidate_id')
            ->groupBy('expenditure_reports.id')
           ->get();
            //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.finalize-report',['user_data' => $d,'finalCandList' => $finalCandList,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListByfinalizeData TRY CATCH ENDS HERE   
    }   // end candidateListByfinalizeData start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 09-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListBylogedaccount By ROPC fuction     
   */
  public function candidateListBylogedaccount(Request $request){
    //AC DEO candidateListBylogedaccount TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $logedAccount = DB::table('expenditure_reports')
		    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
            ->where('expenditure_reports.ST_CODE','=',$st_code)
            ->where('candidate_nomination_detail.district_no',$dist_no)
              ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->where('expenditure_reports.candidate_lodged_acct','=','Yes') 
		     ->groupBy('expenditure_reports.id')
             ->get();
            //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.logedaccount-report',['user_data' => $d,'logedAccount' => $logedAccount,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListBylogedaccount TRY CATCH ENDS HERE   
    }   // end candidateListBylogedaccount start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListBynotintime By AC DEO fuction     
   */
  public function candidateListBynotintime(Request $request){
    //AC DEO candidateListBynotintime TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
            $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $notinTime = DB::table('expenditure_reports')
		   ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no',$dist_no)
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->where('expenditure_reports.account_lodged_time','=','No') 
		   ->groupBy('expenditure_reports.candidate_id')
           ->get();
            //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.notintime-report',['user_data' => $d,'notinTime' => $notinTime,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListBynotintime TRY CATCH ENDS HERE   
     }   // end candidateListBynotintime start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListBydataentryStart By AC DEO fuction     
   */
  public function candidateListByformatedefects(Request $request){
    //AC DEO candidateListByformatedefects TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $formateDefects = DB::table('expenditure_reports')
		   ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no',$dist_no)
              ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->where('expenditure_reports.rp_act','=','No') 
		    ->groupBy('expenditure_reports.candidate_id')
           ->get();
            //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.formatedefects-report',['user_data' => $d,'formateDefects' => $formateDefects,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//PC ROPC candidateListByformatedefects TRY CATCH ENDS HERE   
     }   // end candidateListByformatedefects start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListByronotagree By ROPC fuction     
   */
  public function candidateListByronotagree(Request $request){
    //AC DEO candidateListByronotagree TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
            $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           
          
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
         
           $DataentryStartCandList = DB::table('expenditure_reports')
		       ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('expenditure_reports.constituency_no','=',$cons_no) 
           ->get();
            //dd($DataentryStartCandList);
           return view('admin.pc.ro.Expenditure.ronotagree-report',['user_data' => $d,'DataentryStartCandList' => $DataentryStartCandList,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListByronotagree TRY CATCH ENDS HERE   
     }   // end candidateListByronotagree start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListByunderstatedexpense By AC DEO fuction     
   */
  public function candidateListByunderstatedexpense(Request $request){
    //AC DEO candidateListByunderstatedexpense TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $expenseunderstated = DB::table('expenditure_understated')
		   ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
           ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'expenditure_understated.candidate_id') 
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_understated.ST_CODE','=',$st_code)
           ->where('expenditure_understated.district_no',$dist_no)
           ->where('expenditure_understated.status','=','1')
           ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->where('expenditure_understated.page_no_observation','=',"No") 
		   ->groupBy('expenditure_understated.candidate_id')
           ->get();
            //dd($expenseunderstated);
           return view('admin.ac.deo.Expenditure.expenseunderstated-report',['user_data' => $d,'expenseunderstated' => $expenseunderstated,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//PC ROPC candidateListByunderstatedexpense TRY CATCH ENDS HERE   
    }   // end candidateListByunderstatedexpense start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListBydataentrydefects By AC DEO fuction     
   */
  public function candidateListBydataentrydefects(Request $request){
    //AC DEO candidateListBydataentrydefects TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           
           $DataentryStartCandList = DB::table('expenditure_reports')
		       ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    

           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('expenditure_reports.constituency_no','=',$cons_no) 
		       ->groupBy('expenditure_reports.candidate_id')
           ->get();
            //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.dataentrydefect-report',['user_data' => $d,'DataentryStartCandList' => $DataentryStartCandList,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListBydataentrydefects TRY CATCH ENDS HERE   
     }   // end candidateListBydataentrydefects start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListBypartyfund By AC DEO fuction     
   */
  public function candidateListBypartyfund(Request $request){
    //AC DEO candidateListBypartyfund TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $partyfund = DB::table('expenditure_fund_parties')
           ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id') 
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id') 
           ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.candidate_father_name','expenditure_fund_parties.*')
           ->where('expenditure_fund_parties.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no',$dist_no)
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
		   ->groupBy('expenditure_fund_parties.candidate_id')
           ->get();
          // dd($partyfund);
           return view('admin.ac.deo.Expenditure.partyfund-report',['user_data' => $d,'partyfund' => $partyfund,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListBypartyfund TRY CATCH ENDS HERE   
     }   // end candidateListBypartyfund start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListByothersfund By AC DEO fuction     
   */
  public function candidateListByothersfund(Request $request){
    //AC DEO candidateListByothersfund TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
          
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $otherfund = DB::table('expenditure_fund_source')
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id') 
           ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id') 
           // ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.candidate_father_name',DB::raw('IFNULL((other_source_amount),0) AS otherSourcesfund'))
           ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.candidate_father_name','expenditure_fund_source.*')
           ->where('expenditure_fund_source.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no',$dist_no)
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
		   ->groupBy('expenditure_fund_source.candidate_id')
           ->get();
            //dd($otherfund);
           return view('admin.ac.deo.Expenditure.otherfund-report',['user_data' => $d,'otherfund' => $otherfund,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListByothersfund TRY CATCH ENDS HERE   
     }   // end candidateListByothersfund start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 15-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListByexeedceiling By AC DEO fuction     
   */
  public function candidateListByexeedceiling(Request $request){
    //AC DEO candidateListByexeedceiling TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
         
           $DataentryStartCandList = DB::table('expenditure_reports')
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('expenditure_reports.constituency_no','=',$cons_no) 
		       ->groupBy('expenditure_reports.candidate_id')
           ->get();
            //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.exceedceiling-report',['user_data' => $d,'DataentryStartCandList' => $DataentryStartCandList,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListByexeedceiling TRY CATCH ENDS HERE   
     }   // end candidateListByexeedceiling start function
	 
	  ########################Current Status Dashboard  Start By Niraj 20-05-19########################
 /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 20-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return status dashboard By ACDEO fuction     
   */
  public function statusdashboard(Request $request){
    //PC ROPC dashboard TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $totalContestedCandidate = DB::table('candidate_nomination_detail')
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
           ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
           ->where('candidate_nomination_detail.st_code','=',$st_code)
           ->where('candidate_nomination_detail.district_no','=',$dist_no) 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->count();
          
            //Get Data entry Start Count 
           $startdatacount=$this->expenditureModel->gettotaldataentryStart('AC',$st_code,$dist_no);
           // dd($startdatacount);
           //Get Data entry Start Count %
           $Percent_startdataentry =$this->get_percentage($totalContestedCandidate,$startdatacount);
          
          //Get Data entry finalize Count 
          $finaldatacount=$this->expenditureModel->gettotaldataentryFinal('AC',$st_code,$dist_no);
          //Get Data entry finalize Count %
          $Percent_finaldatacount=$this->get_percentage($totalContestedCandidate,$finaldatacount);
        
          //Get pending Count 
          $pendingdataentrycount=$totalContestedCandidate-$startdatacount;
        
          //Get pending Count %
          $Percent_pendingdataentrycount=$this->get_percentage($totalContestedCandidate,$pendingdataentrycount);
          
           //Get Data entry finalize Count 
           $partiallypendingcount=$this->expenditureModel->gettotalpartiallypending('AC',$st_code,$dist_no);
          
           //Get Data entry finalize Count %
           $Percent_partiallypendingcount=$this->get_percentage($totalContestedCandidate,$partiallypendingcount);

               //Get Data entry defaultercount Count 
           $defaulter=$this->expenditureModel->getdefaulter('AC',$st_code,$dist_no);
           $defaultercount=count($defaulter);
         
           //Get Data entry defaultercount Count %
           $Percent_defaultercount=$this->get_percentage($totalContestedCandidate,$defaultercount);
          
           //Get final by ceo Count 
          $finalbyceocount=$this->expenditureModel->gettotalfinalbyceo('AC',$st_code,$dist_no);
         // dd($finalbyceocount);
          //Get Data entry final by ceo %
          $Percent_finalbyceocount=$this->get_percentage($totalContestedCandidate,$finalbyceocount);

           //Get final by eci Count 
           $finalbyecicount=$this->expenditureModel->gettotalfinalbyeci('AC',$st_code,$dist_no);
           //Getfinal by eci Count %
           $Percent_finalbyecicount=$this->get_percentage($totalContestedCandidate,$finalbyecicount);
		   
		    //Get noticeatdeocount Count 
		  $noticeatdeocount = $this->expenditureModel->gettotalnoticeatDEO('AC', $st_code, $dist_no);
		  //Get noticeatdeocount Count %
		  $Percent_noticeatdeocount = $this->get_percentage($totalContestedCandidate, $noticeatdeocount);

            //dd($Percent_startdataentry);
           return view('admin.ac.deo.Expenditure.statusdashboard',['user_data' => $d,'totalContestedCandidatecount' => $totalContestedCandidate,'pendingdataentrycount' => $pendingdataentrycount,'Percent_pendingdataentrycount' => $Percent_pendingdataentrycount,'finaldatacount' => $finaldatacount,'Percent_finaldatacount' => $Percent_finaldatacount,'partiallypendingcount' => $partiallypendingcount,'Percent_partiallypendingcount' => $Percent_partiallypendingcount,'defaultercount' => $defaultercount,'Percent_defaultercount' => $Percent_defaultercount,'finalbyceocount' => $finalbyceocount,'Percent_finalbyceocount' => $Percent_finalbyceocount,'finalbyecicount' => $finalbyecicount,'Percent_finalbyecicount' => $Percent_finalbyecicount,'noticeatdeocount' => $noticeatdeocount, 'Percent_noticeatdeocount' => $Percent_noticeatdeocount,'edetails'=>$ele_details]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//PC CEO dashboard TRY CATCH ENDS HERE    

     
  }   // end dashboard function

   /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 21-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return getpendingcandidateList  By ACDEO fuction     
  */
 public function getpendingcandidateList (Request $request){
   //ACDEO getpendingcandidateList  TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
           $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
          $st_code=$d->st_code;
          $cons_no=$d->ac_no;
          $dist_no=$d->dist_no;
          $candidate_id=array();
          $startCandList = DB::table('expenditure_reports')->select('candidate_id')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.dist_no','=',$dist_no) 
          ->groupBy('expenditure_reports.candidate_id')
          ->get();
          foreach ($startCandList as $startCandListData) {
            $candidate_id[] = $startCandListData->candidate_id;
           }
         $pendingCandList = DB::table('candidate_nomination_detail')
         ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
         ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')  
         ->where('candidate_nomination_detail.st_code','=',$st_code)
         ->where('candidate_nomination_detail.district_no','=',$dist_no) 
         ->where('candidate_nomination_detail.application_status','=','6')
         ->where('candidate_nomination_detail.finalaccepted','=','1')
         ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
         ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
          // dd($pendingCandList);
          return view('admin.ac.deo.Expenditure.pending-report',['user_data' => $d,'pendingCandList' => $pendingCandList,'edetails'=>$ele_details,'count'=>count($pendingCandList)]); 
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       }//PC ROPC candidateListBydataentryStart TRY CATCH ENDS HERE  
 
    }   // end dataentry start function

    /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 20-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return getpartiallypendingcandidateList  By ACDEO fuction     
  */
 public function getpartiallypendingcandidateList (Request $request){
    //PC ROPC getpartiallypendingcandidateList  TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
            $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
         
           $partiallyCandList = DB::table('expenditure_reports')
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')  
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('expenditure_reports.finalized_status','=','1') 
           ->where('expenditure_reports.final_by_ro','1')
           ->whereNotNull('expenditure_reports.date_of_sending_deo')
           ->where(function($query) {
             $query->whereNull('expenditure_reports.date_of_receipt');
              $query->orwhere('expenditure_reports.date_of_receipt', '=','');
               })
           ->where('candidate_nomination_detail.district_no','=',$dist_no) 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->groupBy('expenditure_reports.candidate_id')
           ->get();
          
           return view('admin.ac.deo.Expenditure.partiallypending-report',['user_data' => $d,'partiallyCandList' => $partiallyCandList,'edetails'=>$ele_details,'count'=>count($partiallyCandList)]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC getpartiallypendingcandidateList TRY CATCH ENDS HERE  
  
     }   // end getpartiallypendingcandidateList start function

     /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 20-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return getdefaultercandidateList  By ACDEO fuction     
  */
 public function getdefaultercandidateList (Request $request){
    //ACDEO getdefaultercandidateList  TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           DB::enableQueryLog();
           $defaulterCandList = DB::table('expenditure_understated')
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
           ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
           ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')  
           ->select('expenditure_understated.candidate_id','expenditure_understated.ST_CODE','expenditure_understated.constituency_no','candidate_personal_detail.cand_name','m_party.PARTYNAME','candidate_nomination_detail.created_at',
            DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
            DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
            ->having('totalobseramnt','<=','totalcandamnt')
           ->where('expenditure_understated.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no','=',$dist_no) 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->groupBy('expenditure_understated.candidate_id')
           ->get();
         
          // dd($defaulterCandList);
            //dd(DB::getQueryLog());
           return view('admin.ac.deo.Expenditure.defaulter-report',['user_data' => $d,'defaulterCandList' => $defaulterCandList,'edetails'=>$ele_details,'count'=>count($defaulterCandList)]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC CEO getdefaultercandidateList TRY CATCH ENDS HERE  
  
     }   // end getdefaultercandidateList start function

      /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 21-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListByfinalizeData By ACDEO fuction     
   */
  public function candidateListByfiledData(Request $request){
    //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $finalCandList = DB::table('expenditure_reports')
		   ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no','=',$dist_no) 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->where('expenditure_reports.finalized_status','=','1') 
		    ->groupBy('expenditure_reports.candidate_id')
           ->get();
            //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.filed-report',['user_data' => $d,'finalCandList' => $finalCandList,'edetails'=>$ele_details,"count"=>count($finalCandList)]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//ACDEO candidateListByfiledData TRY CATCH ENDS HERE   
     }   // end candidateListByfiledData start function
	 
	 /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 23-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListfinalbyCEO By ACDEO fuction     
   */
  public function candidateListfinalbyCEO(Request $request){
    //AC DEO candidateListByfinalizeData TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
         
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $finalbyceoCandList = DB::table('expenditure_reports')
		    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no','=',$dist_no) 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->where('expenditure_reports.final_by_ceo','=','1') 
           ->whereNotNull('expenditure_reports.date_of_receipt')
           ->whereNull('expenditure_reports.date_of_receipt_eci')
		    ->groupBy('expenditure_reports.candidate_id')
           ->get();
            //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.finalbyceo-report',['user_data' => $d,'finalbyceoCandList' => $finalbyceoCandList,'edetails'=>$ele_details,"count"=>count($finalbyceoCandList)]); 
          } else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListfinalbyCEO TRY CATCH ENDS HERE   
     }   // end candidateListfinalbyCEO start function

     /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 21-05-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return candidateListfinalbyECI By ACDEO fuction     
   */
  public function candidateListfinalbyECI(Request $request){
    //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
          
           $st_code=$d->st_code;
           $cons_no=$d->ac_no;
           $dist_no=$d->dist_no;
           $finalbyeciCandList = DB::table('expenditure_reports')
		    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('candidate_nomination_detail.district_no','=',$dist_no) 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->where('expenditure_reports.final_by_eci','1')
            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
		    ->groupBy('expenditure_reports.candidate_id')
           ->get();
            //dd($DataentryStartCandList);
           return view('admin.ac.deo.Expenditure.finalbyeci-report',['user_data' => $d,'finalbyeciCandList' => $finalbyeciCandList,'edetails'=>$ele_details,"count"=>count($finalbyeciCandList)]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC DEO candidateListfinalbyECI TRY CATCH ENDS HERE   
     }   // end candidateListfinalbyECI start function
######################end Current Status Dashboard ##############################################
###############################Start Notice DEO by Niraj 09-07-2019###########################################################

/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 09-07-2019
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getnoticeatDEO By ECI fuction     
 */
public function getnoticeatDEO(Request $request){
    //ACDEO getcandidateListpendingatCEO TRY CATCH STARTS HERE
    try{
    if(Auth::check()){
        $user = Auth::user();
        $uid=$user->id;
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $xss = new xssClean;
        $st_code=$d->st_code;
        $dist_no=$d->dist_no;
        $cons_no=$d->ac_no;
        $st_code=!empty($st_code) ? $st_code : 0;
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        $dist_no=!empty($dist_no) ? $dist_no : 0;
        // echo $st_code.'cons_no'.$cons_no; die;
    
        if($st_code !='0' && $dist_no !='0'){
        $noticeatDEO = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
        ->where('candidate_nomination_detail.st_code','=',$st_code)
        ->where('candidate_nomination_detail.district_no','=',$dist_no) 
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo','0')
        ->where('expenditure_reports.final_by_ro','0')
       ->whereNotNull('expenditure_reports.date_sending_notice_service_to_deo')
       ->where(function($q) {
           $q->where('expenditure_reports.final_action','=','Notice Issued')
             ->orWhere('expenditure_reports.final_action','=','Reply Issued')
             ->orWhere('expenditure_reports.final_action','=','Hearing Done');
           })
        ->groupBy('expenditure_reports.candidate_id')
        ->get(); 
    }
        //dd($DataentryStartCandList);
        return view('admin.ac.deo.Expenditure.noticeatdeo',['user_data' => $d,'noticeatDEO' => $noticeatDEO,'edetails'=>$ele_details,'st_code'=>$st_code,'cons_no'=>$cons_no,'count'=>count($noticeatDEO)]); 
        
    }
    else {
        return redirect('/officer-login');
    }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//PC ECI candidateListByfinalizeData TRY CATCH ENDS HERE   
    }   // end candidateListByfinalizeData start function
    
         /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-06-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getnoticeatDEOEXL By ECI fuction     
     */
    //ECI getnoticeatDEOEXL EXCEL REPORT STARTS
    public function getnoticeatDEOEXL(Request $request){  
    //ECI getnoticeatDEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
    try{
        if(Auth::check()){
        $user = Auth::user();
        $uid=$user->id;
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $xss = new xssClean;
        $st_code=$d->st_code;
        $dist_no=$d->dist_no;
        $cons_no=$d->ac_no;
        $st_code=!empty($st_code) ? $st_code : 0;
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        $dist_no=!empty($dist_no) ? $dist_no : 0;
        // echo  $st_code.'pc'.$cons_no; die;
       $cur_time    = Carbon::now();
    
    \Excel::create('ECINoticeatDEOCandidate_'.'_'.$cur_time, function($excel) use($st_code,$dist_no) { 
    $excel->sheet('Sheet1', function($sheet) use($st_code,$dist_no) {
    
        if($st_code !='0' && $dist_no !='0'){
            $noticeatDEO = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
            ->where('candidate_nomination_detail.st_code','=',$st_code)
            ->where('candidate_nomination_detail.district_no','=',$dist_no) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('expenditure_reports.final_by_ceo','0')
            ->where('expenditure_reports.final_by_ro','0')
           ->whereNotNull('expenditure_reports.date_sending_notice_service_to_deo')
           ->where(function($q) {
               $q->where('expenditure_reports.final_action','=','Notice Issued')
                 ->orWhere('expenditure_reports.final_action','=','Reply Issued')
                 ->orWhere('expenditure_reports.final_action','=','Hearing Done');
               })
            ->groupBy('expenditure_reports.candidate_id')
            ->get(); 
        }
    
            $arr  = array();
            $TotalUsers = 0;
            $user = Auth::user();
            $count = 1;
            foreach ($noticeatDEO as $candDetails) {
                $st=getstatebystatecode($candDetails->st_code);
                //dd($candDetails);
                $acDetails=getacbyacno($candDetails->st_code,$candDetails->ac_no);
                $date = new DateTime($candDetails->finalized_date);
                //echo $date->format('d.m.Y'); // 31.07.2012
                $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
                $data =  array(
                $acDetails->AC_NO.'-'.$acDetails->AC_NAME,
                $candDetails->cand_name,
                $candDetails->PARTYNAME,
                $lodgingDate
                    );
                    $TotalUsers =count($noticeatDEO);
                    array_push($arr, $data);
                            // }
                            $count++;
                        }
                $totalvalues = array('Total',$TotalUsers);
                // print_r($totalvalues);die;
                array_push($arr,$totalvalues);
                    $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                                'AC No & Name', 'Candidate Name', 'Party Name', 'Date Of Submit Scrutiny Form'
                        )
                    );
                });
            })->export('xls');
            }else {
                return redirect('/admin-login');
            } 
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
    
        }
        //ECI getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
        
    }

###############################End Notice CEO & DEO ###########################################################
////////////////////////////////////////////// start manoj here /////////////////////////////////////////////////////////////
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
      public function tracking_status(Request $request) {
        
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
        return view('admin.expenditure.tracking-status', ['user_data' => $d,   'ele_details' => $ele_details]);
    }
    public function deoformview($candidateId,$ac_no) {
          $candidateId = base64_decode($candidateId);
          $ac_no = base64_decode($ac_no);
         $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
         // add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj 
        $max_declaration_date= date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 2 days '));
      
        $min_date_of_account_rec_meetng=date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 1 days '));
        $max_date_of_account_rec_meetng=date('Y-m-d',strtotime($resultDeclarationDate['start_result_declared_date'].' + 30 days '));
     
        
        $period = CarbonPeriod::create($resultDeclarationDate['start_result_declared_date'], $max_declaration_date);
        $period_date_of_account_rec_meetng=CarbonPeriod::create($min_date_of_account_rec_meetng, $max_date_of_account_rec_meetng);
      
                $date_of_decl=array();
                foreach ($period as $date) {
                $date_of_decl[]= $date->format('Y-m-d');
                }
                // date differnce of date_of_account_rec_meetng
                $date_of_account_rec_meetng_arr=array();
                foreach ($period_date_of_account_rec_meetng as $date) {
                
                $date_of_account_rec_meetng_arr[]= $date->format('Y-m-d');
                }
       

        $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
        ////////////////////////////////////////
        
               $acdetail = DB::table('candidate_nomination_detail')
               ->where('candidate_nomination_detail.candidate_id', $candidateId)
               ->where('candidate_nomination_detail.ac_no', $ac_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->first();
               
               
        
            $ac_no = !empty($acdetail->ac_no) ? $acdetail->ac_no : 0;
            $st_code = !empty($acdetail->st_code) ? $acdetail->st_code : 0;
            
            $acData =  getacbyacno($st_code, $ac_no);
 

            $district_no = !empty($acdetail->district_no) ? $acdetail->district_no : 0;

        $districtDetails = getdistrictbydistrictno($st_code, $district_no);
        
            $electionTypeId = !empty($acdetail->election_type_id) ? $acdetail->election_type_id : 0;
            
        // get CEO status cand_name ELECTION_TYPE
            
        $party_id = !empty($acdetail->party_id) ? $acdetail->party_id : 0;
        $partyname = getpartybyid($party_id);
        $partyname = !empty($partyname) ? $partyname->PARTYNAME : '';
        
        $ELECTION_ID = !empty($acdetail->election_id) ? $acdetail->election_id : 0;

        // echo $pcNO, $ELECTION_ID, $st_code;die;
        $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $st_code)->where('ac_no', $ac_no)->where('election_id', $ELECTION_ID)->first();
 
        $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)

        ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)
         ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $expenditure_fund_parties = DB::table('expenditure_fund_parties')->where('candidate_id', $candidateId)
        ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)
         ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)
        ->where('constituency_no', $ac_no)
        ->get()->toArray();
        $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)
        ->where('constituency_no', $ac_no)->where('status', '1')
        ->get()->toArray();
        $getExpItem = DB::table('expenditure_items')->get();
       //  $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidateId);
            $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidateId,$ac_no);
            $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidateId,$ac_no);
            
             $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)
                    ->where('expenditure_reports.constituency_no', $ac_no)
                    ->first();
             
      $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidateId)
         ->where('constituency_no', $ac_no)
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


        

         $candidateData = DB::table('candidate_nomination_detail')
                    ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->join("m_election_details", function($join) {
                        $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                        ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");

                    })

 ->leftjoin("expenditure_reports", function($leftjoin) {
                        $leftjoin->on("expenditure_reports.constituency_no", "=", "candidate_nomination_detail.ac_no")
                        ->on("expenditure_reports.candidate_id", "=", "candidate_nomination_detail.candidate_id");

                    })

                    // ->leftjoin('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                    ->join('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*','candidate_personal_detail.candidate_id as c_id', 'm_election_details.*', 'expenditure_reports.*', 'm_party.PARTYNAME')
                    ->where('candidate_nomination_detail.st_code', $st_code)
                    ->where('candidate_nomination_detail.ac_no', $ac_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                     ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                 
                    ->where('candidate_nomination_detail.candidate_id', '=', $candidateId)
                    ->where('m_election_details.CONST_TYPE', '=', 'AC')
                    ->first();                   
 
        return view('admin.ac.deo.Expenditure.deoForm',['user_data' => $d, 'candidateData' => $candidateData,
            "getCandidateExpData" => $getCandidateExpData, 
            "expenditure_fund_source" => $expenditure_fund_source,
            "expenditure_fund_parties" => $expenditure_fund_parties, 
              'ele_details' => $ele_details, 
            "getSourceFundData" => $getSourceFundData, "getExpData" => $getExpData,
            "getExpItem" => $getExpItem, "gexExpReport" => $gexExpReport,'winn_data'=>$winn_data,
             
            'acdetail'=>$acData,
            'download_link1'=>$download_link1, 
            'download_link2'=>$download_link2, 
            'download_link3'=>$download_link3,
            'download_link4'=>$download_link4,'resultDeclarationDate'=>$resultDeclarationDate,
            'date_of_decl'=>$date_of_decl,'date_of_account_rec_meetng_arr'=>$date_of_account_rec_meetng_arr]);
       
    }

   public function updateAccountDeoForm(Request $request) {
        $response = [];
        $request = (array) $request->all();
        $response = [
            'status' => false,
            'message' => false,
            'data' => []
        ];
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        $checkArrayData = [];

        foreach ($request as $key => $req_data) {
            $xss = new xssClean;
            $checkArrayData[$key] = $xss->clean_input($req_data);
        } 

        //dd($request);

        $ac_nois=$request['constituency_no'];
        $candidateId = !empty($checkArrayData['candidate_id']) ? $checkArrayData['candidate_id'] : 0;
      //  $ac_nois=!empty($checkArrayData['ac_no']) ? $checkArrayData['ac_no'] : 0;
       
        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);
        $checkArrayData['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
        
        //$checkArrayData['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
         $checkArrayData['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
        $checkArrayData['dist_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
        $checkArrayData['created_by'] = $uid;
        $checkArrayData['updated_by'] = $uid;
        $checkArrayData['election_type'] = "General";
		$checkArrayData['election_id'] = !empty($user->election_id)?$user->election_id:0;
        $isexist = DeoexpenditureModel::isCandidate($candidateId,$ac_nois);
        unset($checkArrayData['_token']);
        unset($checkArrayData['example_length']);
        unset($checkArrayData['candidate_id_base']);
         unset($checkArrayData['const_id_base']);
//                // check result exist or not 
        if ($isexist) { // update new record
            unset($checkArrayData['candidate_id']);
            // unset($checkArrayData['candidate_id']);
            unset($checkArrayData['_token']);
          //  dd($checkArrayData);
            $actionStatus = DeoexpenditureModel::updateData($checkArrayData, $candidateId,$ac_nois);
            $response = [
                'status' => true,
                'message' => "Account Details updated successfully.",
                'data' => $checkArrayData
            ];
        } else { // add new record               
            $actionStatus = DeoexpenditureModel::add($checkArrayData);
            $response = [
                'status' => true,
                'message' => "Account Details saved successfully.",
                'data' => $checkArrayData
            ];
        }
        echo json_encode($response);
    }

    public function updateDefectDeoForm(Request $request) {

        //dd($request);
        $response = [];
        $request = (array) $request->all();
        $response = [
            'status' => false,
            'message' => false,
            'data' => []
        ];
        $user = Auth::user();
        $uid = $user->id;

        $namePrefix = \Route::current()->action['prefix'];
        $checkArrayData = [];

        foreach ($request as $key => $req_data) {
            $xss = new xssClean;
            $checkArrayData[$key] = $xss->clean_input($req_data);
        }
        $ac_nois=$request['constituency_no'];

        $candidateId = !empty($checkArrayData['candidate_id']) ? $checkArrayData['candidate_id'] : 0;
        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);
        $checkArrayData['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
        $checkArrayData['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
        $checkArrayData['dist_no'] =!empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
        $checkArrayData['created_by'] = $uid;
        $checkArrayData['updated_by'] = $uid;
        $checkArrayData['election_type'] = "General";
		$checkArrayData['election_id'] = !empty($user->election_id)?$user->election_id:0;
        $checkArrayData['noticefile'] = Session::get('noticefile');
        $isexist = DeoexpenditureModel::isCandidate($candidateId,$ac_nois);
        unset($checkArrayData['_token']);
//                // check result exist or not 
        if ($isexist) { // update new record
            unset($checkArrayData['candidate_id']);
            unset($checkArrayData['_token']);
            $actionStatus = DeoexpenditureModel::updateData($checkArrayData, $candidateId,$ac_nois);
            $response = [
                'status' => true,
                'message' => "Defect Details updated successfully.",
                'data' => $checkArrayData
            ];
        } else { // add new record               
            $actionStatus = DeoexpenditureModel::add($checkArrayData);
            $response = [
                'status' => true,
                'message' => "Defect Details saved successfully.",
                'data' => $checkArrayData
            ];
        }
        echo json_encode($response);
    }

///////////////////////////////////// end manoj here///////////////////////////////
    ////////////////////////////////////// code by manish /////////////////////
    /*
      SOTORE UNDERSTAED EXPENSES DATA FOR PARTICULAR CANDIDATE
     */

    // (ii) If Yes, then Annexe copies of all the notices issued relating to Discrepancies with English Translation (If it is in regional language) and mention Date of Notice.
    public function update_understated_file1(Request $request) {



        if (!empty($_FILES)) {

            $file_name = $_FILES[4]['name']['understated']['comment'];
            $file_size = $_FILES[4]['size']['understated']['comment'];
            $file_tmp = $_FILES[4]['tmp_name']['understated']['comment'];
            $file_type = $_FILES[4]['type']['understated']['comment'];
            $name = rand(100000, 999999) . '_' . $file_name;

            if (move_uploaded_file($file_tmp, self::$fileLocation.$name)) {
                Session::put("comment17ii", self::$fileName.$name);
                 return 1;
            } else {
                Session::put("comment17ii", "");
                 return 0;
            }
        } else {
            Session::put("comment17ii", "");
            return 0;
        }
    }

    // (ii) If Yes, then Annexe copies of all the notices issued relating to Discrepancies with English Translation (If it is in regional language) and mention Date of Notice.
      public function update_understated_file2(Request $request) {
        if (!empty($_FILES)) {

            $file_name = $_FILES[6]['name']['understated']['comment'];
            $file_size = $_FILES[6]['size']['understated']['comment'];
            $file_tmp = $_FILES[6]['tmp_name']['understated']['comment'];
            $file_type = $_FILES[6]['type']['understated']['comment'];
            $name = rand(100000, 999999) . '_' . $file_name;

            if (move_uploaded_file($file_tmp, self::$fileLocation.$name)) {
                Session::put("comment17iv", self::$fileName.$name);
                return 1;
            } else {
                Session::put("comment17iv", "");
                 return 0;
            }
        } else {
            Session::put("comment17iv", "");
            return 0;
        }
    }

    public function update_understated_file4(Request $request) {

        if (!empty($_FILES)) {

            $file_name = $_FILES[9]['name']['understated']['comment'];
            $file_size = $_FILES[9]['size']['understated']['comment'];
            $file_tmp = $_FILES[9]['tmp_name']['understated']['comment'];
            $file_type = $_FILES[9]['type']['understated']['comment'];
            $name = rand(100000, 999999) . '_' . $file_name;

            if (move_uploaded_file($file_tmp, self::$fileLocation.$name)) {
                Session::put("comment23iv", self::$fileName.$name);
                 return 1;
            } else {
                Session::put("comment23iv", "");
                 return 0;
            }
        } else {
            Session::put("comment23iv", "");
            return 0;
        }
    }
        public function updateNoticeFile(Request $request) {
        if (!empty($_FILES)) {

            $file_name = $_FILES[6]['name']['understated']['comment'];
            $file_size = $_FILES[6]['size']['understated']['comment'];
            $file_tmp = $_FILES[6]['tmp_name']['understated']['comment'];
            $file_type = $_FILES[6]['type']['understated']['comment'];
            $name = rand(100000, 999999) . '_' . $file_name;

            if (move_uploaded_file($file_tmp, self::$fileLocation.$name)) {
                Session::put("noticefile", self::$fileName.$name);
                return 1;
            } else {
                Session::put("noticefile", "");
                 return 0;
            }
        } else {
            Session::put("noticefile", "");
            return 0;
        }
    }

   
   /*
     public function updateUnderstatedDetail(Request $request) {
        $request = (array) $request->all();
        $candidateId = $request['candidate_id'];
         $ac_nois = $request['constituency_no'];
        $count_data =!empty($request['datas']['expenditure_type'])? count($request['datas']['expenditure_type']):0;
        //$request = $_POST;
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        $countdata = count($request);
        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);

        $filePath=DB::table('expenditure_understates')
                       ->select('understated_type_id','comment','extra_data')
                       ->where('candidate_id', $candidateId)
                       ->get()->toArray(); 
        $filePath1= !empty($filePath[3]->comment)? $filePath[3]->comment:'';
        $filePath2= !empty($filePath[5]->comment)?$filePath[5]->comment:'';
        $filePath3= !empty($filePath[8]->extra_data)?$filePath[8]->extra_data:'';
        $comment17ii = !empty(Session::get("comment17ii"))?Session::get("comment17ii"):$filePath1;    
        $comment17iv = !empty(Session::get("comment17iv"))?Session::get("comment17iv"):$filePath2;    
        $comment23iv = !empty(Session::get("comment23iv"))?Session::get("comment23iv"):$filePath3;    
          
       // error_log(" DELETE ISSUES-1929");
         
        try {
            $datas = [];
            $rules = []; 
               
              $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)
              ->where('constituency_no', $ac_nois)
              ->get()->toArray();

            
            for ($i = 1; $i <= 9; $i++) {
                $xss = new xssClean;

                $req['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
                // $req['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
                $req['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
                
                $req['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
                $req['created_by'] = $uid;
                $req['updated_by'] = $uid;
                $req['election_type'] = "AC";
                $req['candidate_id'] = $candidateId;
                $req['election_id'] = !empty($user->election_id)?$user->election_id:0;
                $req['understated_type_id'] = $i;
                $req['status'] = !empty($request[$i]['understated']['status']) ? $xss->clean_input($request[$i]['understated']['status']) : "";
                $req['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : "";

                if ($i == 2 || $i==8 || $i==3) {

                  $req['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : "";

                }
                if ($i == 4) {
                    $req['comment'] = !empty($comment17ii) ? $comment17ii : "";
                    $req['extra_data'] = !empty($request[$i]['understated']['extra_data']) ? $xss->clean_input($request[$i]['understated']['extra_data']) : "";
                    Session::forget("comment17ii");
                }
                if ($i == 6) {
                    $req['comment'] = !empty($comment17iv) ? $comment17iv : "";
                    Session::forget("comment17iv");
                }
                if ($i == 9) {
                    $req['extra_data'] = !empty($comment23iv) ? $comment23iv : "";
                    Session::forget("comment23iv");
                }

                  if (!empty($getCandidateExpData) && count($getCandidateExpData)>0) {
                        //error_log(" DELETE ISSUES-1977");
 
               $updatedata=[];
                $updatedata['updated_by'] = $uid; 
                $updatedata['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
                //$updatedata['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
                $updatedata['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
                $updatedata['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";             
                 
                $updatedata['status'] = !empty($request[$i]['understated']['status']) ? $xss->clean_input($request[$i]['understated']['status']) : $getCandidateExpData[$i-1]->status;
                $updatedata['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : $getCandidateExpData[$i-1]->comment;



               if ($i == 2 || $i == 8 ||  $i==3) {

                  $updatedata['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : "";

                }


                if ($i == 4) {
                    $updatedata['comment'] = !empty($comment17ii) ? $comment17ii : $getCandidateExpData[$i-1]->comment;
                    $updatedata['extra_data'] = !empty($request[$i]['understated']['extra_data']) ? $xss->clean_input($request[$i]['understated']['extra_data']) : $getCandidateExpData[$i-1]->extra_data;
                    Session::forget("comment17ii");
                }
                if ($i == 6) {
                    $updatedata['comment'] = !empty($comment17iv) ? $comment17iv : "";
                    Session::forget("comment17iv");
                }
                if ($i == 9) {
                    $updatedata['extra_data'] = !empty($comment23iv) ? $comment23iv : "";
                    Session::forget("comment23iv");
                }
				   // error_log(" DELETE ISSUES-2000");
                     $updatunderstates = DB::table('expenditure_understates')
                     ->where('candidate_id','=', $candidateId)
                      ->where('id','=', $getCandidateExpData[$i-1]->id)
                      ->where('constituency_no','=', $ac_nois)
                      
                     ->update($updatedata);
                     }else{
						     //error_log(" DELETE ISSUES-2006");
                      $dataInserted = $this->commonModel->insertData('expenditure_understates', $req);
                     }
               
                
            }


            if ($count_data > 0) {
              $sno19Data = DB::table('expenditure_understated')->where('candidate_id','=', $candidateId)
              ->where('constituency_no','=', $ac_nois)
               ->get()->toArray();
			       //error_log(" DELETE ISSUES-2017");
                $requestData = array();
                for ($i = 0; $i < $count_data; $i++) {                     
                        $requestData['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code :"";
                       // $requestData['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
                         $requestData['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
                      // $req['district_no'] = !empty($request['district_no']) ? $request['district_no'] :"";
                       $req['district_no'] =!empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
                        $requestData['created_by'] = $uid;
                        $requestData['updated_by'] = $uid;
                        $requestData['election_type'] = "AC";
                        $requestData['expenditure_type'] = !empty($request['datas']['expenditure_type'][$i])? $request['datas']['expenditure_type'][$i]:"";
                        $requestData['date_understated'] = !empty($request['datas']['date_understated'][$i])?$request['datas']['date_understated'][$i]:"";
                        $requestData['page_no_observation'] = !empty($request['datas']['page_no_observation'][$i])?$request['datas']['page_no_observation'][$i]:"";
                        $requestData['amt_as_per_observation'] =!empty($request['datas']['amt_as_per_observation'][$i])?$request['datas']['amt_as_per_observation'][$i]:"";
                        $requestData['amt_as_per_candidate'] = !empty($request['datas']['amt_as_per_candidate'][$i])?$request['datas']['amt_as_per_candidate'][$i]:"";
                        $requestData['amt_understated_by_candidate'] = !empty($request['datas']['amt_understated_by_candidate'][$i])?$request['datas']['amt_understated_by_candidate'][$i]:"";
                        $requestData['description'] = !empty($request['datas']['description'][$i])?$request['datas']['description'][$i]:"";
                        $requestData['candidate_id'] = $candidateId;
                        if(!empty($sno19Data) && count($sno19Data)>0){


                              $updatedata=[];      
                               $updatedata['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
               // $updatedata['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
                $updatedata['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
                $updatedata['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";                      
                        $updatedata['updated_by'] = $uid;                        
                        $updatedata['expenditure_type'] = !empty($request['datas']['expenditure_type'][$i])? $request['datas']['expenditure_type'][$i]:$sno19Data[$i]->expenditure_type;
                        $updatedata['date_understated'] = !empty($request['datas']['date_understated'][$i])?$request['datas']['date_understated'][$i]:$sno19Data[$i]->date_understated;
                        $updatedata['page_no_observation'] = !empty($request['datas']['page_no_observation'][$i])?$request['datas']['page_no_observation'][$i]:$sno19Data[$i]->page_no_observation;
                        $updatedata['amt_as_per_observation'] =!empty($request['datas']['amt_as_per_observation'][$i])?$request['datas']['amt_as_per_observation'][$i]:$sno19Data[$i]->amt_as_per_observation;
                        $updatedata['amt_as_per_candidate'] = !empty($request['datas']['amt_as_per_candidate'][$i])?$request['datas']['amt_as_per_candidate'][$i]:$sno19Data[$i]->amt_as_per_candidate;
                        $updatedata['amt_understated_by_candidate'] = !empty($request['datas']['amt_understated_by_candidate'][$i])?$request['datas']['amt_understated_by_candidate'][$i]:$sno19Data[$i]->amt_understated_by_candidate;
                        $updatedata['description'] = !empty($request['datas']['description'][$i])?$request['datas']['description'][$i]:$sno19Data[$i]->description;
                          $updatesno19 = DB::table('expenditure_understated')
						      //error_log(" DELETE ISSUES-2050");
                                       ->where('id','=', $sno19Data[$i]->id)
                                       ->where('candidate_id','=', $candidateId)
                                        ->where('constituency_no','=', $ac_nois)
                                       ->update($updatedata);
                            }else{
								    //error_log(" DELETE ISSUES-1956");
                          $dataInserted = $this->commonModel->insertData('expenditure_understated', $requestData);
                        }

                          //error_log(" DELETE ISSUES-1959");  
                    
                }
            }
            return 1;
        } catch (\Exception $e) {
          return $e->getMessage();

            return 0;
        }
    } */

// update file


    public function updateUnderstatedDetail(Request $request) {
        $request = (array) $request->all();
        //dd($request);
        $candidateId = $request['candidate_id'];
         $ac_nois = $request['constituency_no'];
        $count_data =!empty($request['datas']['expenditure_type'])? count($request['datas']['expenditure_type']):0;
        $count_data_new =!empty($request['datass']['expenditure_type'])? count($request['datass']['expenditure_type']):0;
   // print_R($count_data); echo "----";print_r($count_data_new);exit;
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        $countdata = count($request);
        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);

        $filePath=DB::table('expenditure_understates')
                       ->select('understated_type_id','comment','extra_data')
                       ->where('candidate_id', $candidateId)
                       ->get()->toArray(); 
        $filePath1= !empty($filePath[3]->comment)? $filePath[3]->comment:'';
        $filePath2= !empty($filePath[5]->comment)?$filePath[5]->comment:'';
        $filePath3= !empty($filePath[8]->extra_data)?$filePath[8]->extra_data:'';
        $comment17ii = !empty(Session::get("comment17ii"))?Session::get("comment17ii"):$filePath1;    
        $comment17iv = !empty(Session::get("comment17iv"))?Session::get("comment17iv"):$filePath2;    
        $comment23iv = !empty(Session::get("comment23iv"))?Session::get("comment23iv"):$filePath3;    
          
       // error_log(" DELETE ISSUES-1929");
         
        try {
            $datas = [];
            $rules = []; 
               
              $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)
              ->where('constituency_no', $ac_nois)
              ->get()->toArray();

            
            for ($i = 1; $i <= 9; $i++) {
                $xss = new xssClean;

                $req['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
                // $req['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
                $req['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
                
                $req['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
                $req['created_by'] = $uid;
                $req['updated_by'] = $uid;
                $req['election_type'] = "AC";
                $req['candidate_id'] = $candidateId;
                $req['election_id'] = !empty($user->election_id)?$user->election_id:0;
                $req['understated_type_id'] = $i;
                $req['status'] = !empty($request[$i]['understated']['status']) ? $xss->clean_input($request[$i]['understated']['status']) : "";
                $req['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : "";

                if ($i == 2 || $i==8 || $i==3) {

                  $req['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : "";

                }
                if ($i == 4) {
                    $req['comment'] = !empty($comment17ii) ? $comment17ii : "";
                    $req['extra_data'] = !empty($request[$i]['understated']['extra_data']) ? $xss->clean_input($request[$i]['understated']['extra_data']) : "";
                    Session::forget("comment17ii");
                }
                if ($i == 6) {
                    $req['comment'] = !empty($comment17iv) ? $comment17iv : "";
                    Session::forget("comment17iv");
                }
                if ($i == 9) {
                    $req['extra_data'] = !empty($comment23iv) ? $comment23iv : "";
                    Session::forget("comment23iv");
                }

                  if (!empty($getCandidateExpData) && count($getCandidateExpData)>0) {
                        //error_log(" DELETE ISSUES-1977");
 
               $updatedata=[];
                $updatedata['updated_by'] = $uid; 
                $updatedata['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
                //$updatedata['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
                $updatedata['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
                $updatedata['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";             
                 
                $updatedata['status'] = !empty($request[$i]['understated']['status']) ? $xss->clean_input($request[$i]['understated']['status']) : $getCandidateExpData[$i-1]->status;
                $updatedata['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : $getCandidateExpData[$i-1]->comment;



               if ($i == 2 || $i == 8 ||  $i==3) {

                  $updatedata['comment'] = !empty($request[$i]['understated']['comment']) ? $xss->clean_input($request[$i]['understated']['comment']) : "";

                }


                if ($i == 4) {
                    $updatedata['comment'] = !empty($comment17ii) ? $comment17ii : $getCandidateExpData[$i-1]->comment;
                    $updatedata['extra_data'] = !empty($request[$i]['understated']['extra_data']) ? $xss->clean_input($request[$i]['understated']['extra_data']) : $getCandidateExpData[$i-1]->extra_data;
                    Session::forget("comment17ii");
                }
                if ($i == 6) {
                    $updatedata['comment'] = !empty($comment17iv) ? $comment17iv : "";
                    Session::forget("comment17iv");
                }
                if ($i == 9) {
                    $updatedata['extra_data'] = !empty($comment23iv) ? $comment23iv : "";
                    Session::forget("comment23iv");
                }
           // error_log(" DELETE ISSUES-2000");
                     $updatunderstates = DB::table('expenditure_understates')
                     ->where('candidate_id','=', $candidateId)
                      ->where('id','=', $getCandidateExpData[$i-1]->id)
                      ->where('constituency_no','=', $ac_nois)
                      
                     ->update($updatedata);
                     }else{
                 //error_log(" DELETE ISSUES-2006");
                      $dataInserted = $this->commonModel->insertData('expenditure_understates', $req);
                     }
               
                
            }


            if ($count_data > 0) {
              $sno19Data = DB::table('expenditure_understated')->where('candidate_id','=', $candidateId)
              ->where('constituency_no','=', $ac_nois)->where('status', '1')
               ->get()->toArray();
             //error_log(" DELETE ISSUES-2017");
                $requestData = array();
                $idid=array();
                for ($i = 0; $i < $count_data; $i++) {                     
                        $requestData['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code :"";
                       // $requestData['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
                         $requestData['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
                      // $req['district_no'] = !empty($request['district_no']) ? $request['district_no'] :"";
                       $req['district_no'] =!empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
                        $requestData['created_by'] = $uid;
                        $requestData['updated_by'] = $uid;
                        $requestData['election_type'] = "AC";
                        $requestData['expenditure_type'] = !empty($request['datas']['expenditure_type'][$i])? $request['datas']['expenditure_type'][$i]:"";
                        $requestData['date_understated'] = !empty($request['datas']['date_understated'][$i])?$request['datas']['date_understated'][$i]:"";
                        $requestData['page_no_observation'] = !empty($request['datas']['page_no_observation'][$i])?$request['datas']['page_no_observation'][$i]:"";
                        $requestData['amt_as_per_observation'] =!empty($request['datas']['amt_as_per_observation'][$i])?$request['datas']['amt_as_per_observation'][$i]:"";
                        $requestData['amt_as_per_candidate'] = !empty($request['datas']['amt_as_per_candidate'][$i])?$request['datas']['amt_as_per_candidate'][$i]:"";
                        $requestData['amt_understated_by_candidate'] = !empty($request['datas']['amt_understated_by_candidate'][$i])?$request['datas']['amt_understated_by_candidate'][$i]:"";
                        $requestData['description'] = !empty($request['datas']['description'][$i])?$request['datas']['description'][$i]:"";
                        $requestData['candidate_id'] = $candidateId;
                        if(!empty($sno19Data) && count($sno19Data)>0){

   

                               $updatedata=[];      
                               $updatedata['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
               // $updatedata['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
                $updatedata['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
                $updatedata['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";                      
                        $updatedata['updated_by'] = $uid;                        
                        $updatedata['expenditure_type'] = !empty($request['datas']['expenditure_type'][$i])? $request['datas']['expenditure_type'][$i]:$sno19Data[$i]->expenditure_type;
                        $updatedata['date_understated'] = !empty($request['datas']['date_understated'][$i])?$request['datas']['date_understated'][$i]:$sno19Data[$i]->date_understated;
                        $updatedata['page_no_observation'] = !empty($request['datas']['page_no_observation'][$i])?$request['datas']['page_no_observation'][$i]:$sno19Data[$i]->page_no_observation;
                        $updatedata['amt_as_per_observation'] =!empty($request['datas']['amt_as_per_observation'][$i])?$request['datas']['amt_as_per_observation'][$i]:$sno19Data[$i]->amt_as_per_observation;
                        $updatedata['amt_as_per_candidate'] = !empty($request['datas']['amt_as_per_candidate'][$i])?$request['datas']['amt_as_per_candidate'][$i]:$sno19Data[$i]->amt_as_per_candidate;
                        $updatedata['amt_understated_by_candidate'] = !empty($request['datas']['amt_understated_by_candidate'][$i])?$request['datas']['amt_understated_by_candidate'][$i]:$sno19Data[$i]->amt_understated_by_candidate;
                        $updatedata['description'] = !empty($request['datas']['description'][$i])?$request['datas']['description'][$i]:$sno19Data[$i]->description;

                        //if(!empty ($sno19Data[$i]->id) ){
                            
                            // $idid[]=$sno19Data[$i]->id;

                          $updatesno19 = DB::table('expenditure_understated')
                  //error_log(" DELETE ISSUES-2050");
                                       ->where('id','=', $sno19Data[$i]->id)
                                       ->where('candidate_id','=', $candidateId)
                                        ->where('constituency_no','=', $ac_nois)
                                       ->update($updatedata);
                               

                            }else{

                        //  $dataInserted = $this->commonModel->insertData('expenditure_understated', $requestData);
                        }



                       

                          //error_log(" DELETE ISSUES-1959");  
                    
                }

               
            }
                        if ($count_data_new > 0) {

                                        
                $requestDatas = array();
                for ($l = 0; $l < $count_data_new; $l++) {
                  //  exit;
                    if (!empty($request['datass']['page_no_observation'][$l])) {
                        $requestDatas['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code :"";
                        $requestDatas['constituency_no'] = !empty($ac_nois) ? $ac_nois : "";
                        $requestDatas['created_by'] = $uid;
                        $requestDatas['updated_by'] = $uid;
                        $requestDatas['election_id'] = !empty($user->election_id)?$user->election_id:0;
                        $requestDatas['election_type'] = "General";
                        $requestDatas['expenditure_type'] = $request['datass']['expenditure_type'][$l];
                        $requestDatas['date_understated'] = $request['datass']['date_understated'][$l];
                        $requestDatas['page_no_observation'] = $request['datass']['page_no_observation'][$l];
                        $requestDatas['amt_as_per_observation'] = $request['datass']['amt_as_per_observation'][$l];
                        $requestDatas['amt_as_per_candidate'] = $request['datass']['amt_as_per_candidate'][$l];
                        $requestDatas['amt_understated_by_candidate'] = $request['datass']['amt_understated_by_candidate'][$l];
                        $requestDatas['description'] = $request['datass']['description'][$l];
                        $requestDatas['candidate_id'] = $candidateId;
                       
                        $dataInsertedsd = $this->commonModel->insertData('expenditure_understated', $requestDatas);
                    }else{
                        //dd("sss");
                    }
                }
            }
            return 1;
        } catch (\Exception $e) {

          return $e->getMessage();

            return 0;
        }
    }








  public function UpdateSourceFundData(Request $request) {
        $request = (array) $request->all();
        //$count_data =  !empty($request['data']['other_souce_name'])? count($request['data']['other_souce_name']):'';
        $candidateId = $request['candidate_id'];
        $constituency_no=$request['constituency_no'];

       // dd($constituency_no);
        $request = $_POST;
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
        $uid = $user->id;
 
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);

        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);

        try {
            $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)
            ->where('constituency_no', $constituency_no)
            ->get()->toArray();

                                 $cashinsertData=['ST_CODE'=>!empty($candidateDetail->st_code) ? $candidateDetail->st_code : "",
                                                  // 'constituency_no'=>!empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "",
                                                   'constituency_no'=>!empty($constituency_no) ? $constituency_no : "",
                                                  'district_no'=>!empty($candidateDetail->district_no) ? $candidateDetail->district_no : "",
                                                  'created_by'=>$uid,
                                                  'updated_by'=>$uid,
                                                  'election_type'=>Session::get('DB_ELE_TYPE'),         
                                                  'other_souce_name'=>$request['other_souce_name_cash'],
                                                  'other_source_payment_mode'=>'Cash',
                                                  'other_source_amount'=>$request['other_source_amount_cash'],
                                                  'candidate_id'=>$candidateId,
                                                  'election_id'=> !empty($user->election_id)?$user->election_id:0
                                                  ];
                                $chequeinsertData = ['ST_CODE'=>!empty($candidateDetail->st_code) ? $candidateDetail->st_code : "",
                                                  // 'constituency_no'=>!empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "",
                                'constituency_no'=>!empty($constituency_no) ? $constituency_no : "",
                                                  
                                                  'district_no'=>!empty($candidateDetail->district_no) ? $candidateDetail->district_no : "",
                                                  'created_by'=>$uid,
                                                  'updated_by'=>$uid,
                                                  'election_type'=>Session::get('DB_ELE_TYPE'),
                                                  'other_souce_name'=>$request['other_souce_name_cheque'],
                                                  'other_source_payment_mode'=>'Cheque',
                                                  'other_source_amount'=>$request['other_source_amount_cheque'],
                                                  'candidate_id'=>$candidateId,
                                                  'election_id'=> !empty($user->election_id)?$user->election_id:0
                                                  ];
                                $kindinsertData= ['ST_CODE'=>!empty($candidateDetail->st_code) ? $candidateDetail->st_code : "",
                                                  // 'constituency_no'=>!empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "",
                                                  'constituency_no'=>!empty($constituency_no) ? $constituency_no : "",
                                                  'district_no'=>!empty($candidateDetail->district_no) ? $candidateDetail->district_no : "",
                                                  'created_by'=>$uid,
                                                  'updated_by'=>$uid,
                                                  'election_type'=>Session::get('DB_ELE_TYPE'),
                                                  'other_souce_name'=>$request['other_souce_name_kind'],
                                                  'other_source_payment_mode'=>'In Kind',
                                                  'other_source_amount'=>$request['other_source_amount_kind'],
                                                  'candidate_id'=>$candidateId,
                                                  'election_id'=> !empty($user->election_id)?$user->election_id:0
                                                  ];
                                            


                          
                                if(!empty($getSourceFundData) &&count($getSourceFundData)>0){
                                     
                                             $cashDataUpdate=
                                                  [ 
                                                  'updated_by'=>$uid,
                                                  'other_souce_name'=>$request['other_souce_name_cash'],
                                                  'other_source_payment_mode'=>'Cash',
                                                  'other_source_amount'=>$request['other_source_amount_cash']
                                                  ];
                                  $chequeDataUpdate=[ 
                                                  'updated_by'=>$uid,
                                                  'other_souce_name'=>$request['other_souce_name_cheque'],
                                                  'other_source_payment_mode'=>'Cheque',
                                                  'other_source_amount'=>$request['other_source_amount_cheque']
                                                  ];
                                 $kindDataUpdate= [
                                                  'updated_by'=>$uid,
                                                  'other_souce_name'=>$request['other_souce_name_kind'],
                                                  'other_source_payment_mode'=>'In Kind',
                                                  'other_source_amount'=>$request['other_source_amount_kind']
                                                  ];
                          $cashrecord=DB::table('expenditure_fund_source')
                          ->where('candidate_id','=',$candidateId)
                          ->where('constituency_no','=',$constituency_no)
                          
                          ->where('other_source_payment_mode','=','Cash')->first();

                              if(!empty($cashrecord)){
                                $updateFundstatus=DB::table('expenditure_fund_source')
                                ->where('candidate_id','=',$candidateId)
                                ->where('constituency_no','=',$constituency_no)
                                ->where('other_source_payment_mode','=','Cash')
                                ->update($cashDataUpdate); 
                                 
                              }else{
         
                                      $updateFundstatus=DB::table('expenditure_fund_source')->insert($cashinsertData);
 
                              }

                           $Chequerecord=DB::table('expenditure_fund_source')
                          ->where('candidate_id','=',$candidateId)
                          ->where('constituency_no','=',$constituency_no)
                          ->where('other_source_payment_mode','=','Cheque')->first();
                              if(!empty($Chequerecord)){
                                $updateFundstatus=DB::table('expenditure_fund_source')
                                ->where('candidate_id','=',$candidateId)
                                ->where('constituency_no','=',$constituency_no)
                                ->where('other_source_payment_mode','=','Cheque')
                                ->update($chequeDataUpdate); 
                              }else{
     
                                      $updateFundstatus=DB::table('expenditure_fund_source')->insert($chequeinsertData);

                              }
                           $kindrecord=DB::table('expenditure_fund_source')
                          ->where('candidate_id','=',$candidateId)
                          ->where('constituency_no','=',$constituency_no)
                          ->where('other_source_payment_mode','=','In Kind')->first();
                            if(!empty($kindrecord)){
                              $updateFundstatus=DB::table('expenditure_fund_source')
                              ->where('candidate_id','=',$candidateId)
                              ->where('constituency_no','=',$constituency_no)
                              ->where('other_source_payment_mode','=','In Kind')
                              ->update($kindDataUpdate); 
                            }else{

                                    $updateFundstatus=DB::table('expenditure_fund_source')->insert($kindinsertData);

                            } 


                  }else{       
                        $updateFundstatus=DB::table('expenditure_fund_source')->insert([$cashinsertData,$chequeinsertData,$kindinsertData]); 
                  }     
                  return 1;        

            // if (!empty($updateFundstatus)) {
            //      return 1;
            // }else{
            //   return 0;
            // }
        } catch (\Exception $e) {

            return 0;
        }
    }
     public function UpdatePartyFundData(Request $request) {

        $request = (array) $request->all();
        $candidateId = $request['candidate_id'];
        $constituency_no = $request['constituency_no'];
        
        $request = $_POST;
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        unset($request['overallsum_source_political']);
        $request['candidate_id'] = $candidateId;

        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);

        $request['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
        //$request['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
         $request['constituency_no'] = !empty($constituency_no) ? $constituency_no : "";
        $request['created_by'] = $uid;
        $request['district_no'] = !empty($candidateDetail->district_no) ? $candidateDetail->district_no : "";
        $request['updated_by'] = $uid;
        $request['election_id'] = !empty($user->election_id)?$user->election_id:0;

        $request['election_type'] = "General";
        try {
            $getSourceFundData = DB::table('expenditure_fund_parties')->where('candidate_id', $candidateId)
            ->where('constituency_no', $constituency_no)
            ->get()->toArray();
            
           if (!empty($getSourceFundData)) {                 
                 
                $updatestate=DB::table('expenditure_fund_parties')
                ->where('candidate_id','=', $candidateId) 
                 ->where('constituency_no','=', $constituency_no)                   
                 ->update($request); 
                 return 1;
            }else{
              $dataInserted = $this->commonModel->insertData('expenditure_fund_parties', $request);
              return 0;
            }
            // if (!empty($request)) {
            //     $dataInserted = $this->commonModel->insertData('expenditure_fund_parties', $request);
            //     if ($dataInserted) {
            //         return 1;
            //     } else {
            //         return 0;
            //     }
            // }
        } catch (\Exception $e) {

            return 0;
        }
    }

    public function SaveExpenseData(Request $request) {

        $request = (array) $request->all();
        $count_data = count($request['datas']['expenditure_type']);
       
        $candidateId = $request['candidate_id'];
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        $candidateDetail = $this->commonModel->selectone('candidate_nomination_detail', 'candidate_id', $candidateId);

        try {
            $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)->get()->toArray();

            if ($count_data > 0) {
                $requestData = array();
                for ($i = 0; $i < $count_data; $i++) {
                    if (!empty($request['datas']['page_no_observation'][$i])) {
                        $requestData['ST_CODE'] = !empty($candidateDetail->st_code) ? $candidateDetail->st_code : "";
                        $requestData['constituency_no'] = !empty($candidateDetail->ac_no) ? $candidateDetail->ac_no : "";
                        $requestData['created_by'] = $uid;
                        $requestData['updated_by'] = $uid;
						$requestData['election_id'] = !empty($user->election_id)?$user->election_id:0;
                        $requestData['election_type'] = "General";
                        $requestData['expenditure_type'] = $request['datas']['expenditure_type'][$i];
                        $requestData['date_understated'] = $request['datas']['date_understated'][$i];
                        $requestData['page_no_observation'] = $request['datas']['page_no_observation'][$i];
                        $requestData['amt_as_per_observation'] = $request['datas']['amt_as_per_observation'][$i];
                        $requestData['amt_as_per_candidate'] = $request['datas']['amt_as_per_candidate'][$i];
                        $requestData['amt_understated_by_candidate'] = $request['datas']['amt_understated_by_candidate'][$i];
                        $requestData['description'] = $request['datas']['description'][$i];
                        $requestData['candidate_id'] = $candidateId;
                     //   dd($requestData);
                       // $dataInserted = $this->commonModel->insertData('expenditure_understated', $requestData);
                    }
                }
            }

            if (!empty($dataInserted)) {
                if ($dataInserted) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } catch (\Exception $e) {

            return 0;
        }
    }

    public function updateData(Request $request) {
        $request = (array) $request->all();
        if (!empty($request)) {
            $updateTrackData = $this->commonModel->updatedata('expenditure_reports', 'id', $request['tbid'], array($request['column'] => $request['value']));
            if ($updateTrackData) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function DeleteSourceFundData(Request $request) {
        try {
            $delId = $_POST['delID'];
            if (!empty($delId)) {
                $deleteRecord = $this->commonModel->updatedata('expenditure_understated', 'id', $delId,array("status" => "0"));
                if ($deleteRecord) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } catch (\Exception $e) {
            return 0;
        }
    } 

    public function FinalizedData(Request $request) {

        $candidateId = $_POST['candidate_id'];
         $const_no = base64_decode($_POST['const_no']);
   // dd($const_no);
    // shishir
    if (Auth::check()) {
      $user = Auth::user();
      $uid = $user->id;
      $d = $this->commonModel->getunewserbyuserid($user->id);
      
      $st_code=$d->st_code;
      $district_no=$d->dist_no;
      
      $candidate_details=DB::table('candidate_nomination_detail')
      ->select('candidate_nomination_detail.AC_NO')
            ->where('candidate_nomination_detail.candidate_id','=',$candidateId)
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
            ->where('candidate_nomination_detail.district_no','=',$d->dist_no)->first();
       
      $ac_no=!empty($candidate_details->ac_no)?$candidate_details->ac_no:0;   
      $insertdata=['candidate_id'=>$candidateId,'st_code'=>$st_code,'constituency_no'=>$const_no,
      'district_no'=>$district_no];
      
    }
    // shishir end

        try {

           //$updateFinalized = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidateId, array("finalized_status" => '1'));
           // $dataUpdate=array(["finalized_status" => '1']);

           // $updateFinalized = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->where('constituency_no', $const_no)->update($dataUpdate);
            $updateFinalized=DB::table('expenditure_reports')
         ->where('candidate_id', $candidateId)
         ->where('constituency_no', $const_no)
         ->update(['finalized_status' => '1']);
           
            if ($updateFinalized) {
        // shishir
              //$dd=  $this->commonModel->insertData('expenditure_notification',$insertdata);
              //dd($dd);
        // shishir end
        
                return 1;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

 /*   public function DeleteUnderStatedData(Request $request) {
        try {
            $delId = $_POST['delID'];
            if (!empty($delId)) {
                $deleteRecord = $this->commonModel->removerecord('expenditure_understated', 'id', $delId);
                if ($deleteRecord) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } catch (\Exception $e) {
            return 0;
        }
    } */

    //////////////////////////////// end here Manish ////////////////////////////////////////////
     public function GetTrackingReportData(Request $request)
    {
    	 if (Auth::check()) {
    	$request = (array) $request->all();
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        $d = $this->expenditureModel->getunewserbyuserid($user->id,$user->role_id);
        $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray(); 

        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
        if ($check_finalize == '') {
            $cand_finalize_ceo = 0;
            $cand_finalize_ro = 0;
        } else {
            $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
            $cand_finalize_ro = $check_finalize->finalized_ac;
        }
        $seched = getschedulebyid($ele_details->ScheduleID);
        $sechdul = checkscheduledetails($seched);
        try {
            $condtition = "";
            if(!empty($_GET['year']))
            {
                $year =$_GET['year'];
                 $condtition .= " AND YEAR(er.date_of_declaration)='$year'";
            }

            if(!empty($_GET['electionType']))
            {
            	$electype = $_GET['electionType'];
            	$condtition .= " AND er.election_type='$electype'";

            }

        	$ReportData = $this->expenditureModel->GetExpeditureData($user->role_id,$user->pc_no,$user->st_code,$condtition);
            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status','1')->get()->toArray();

        	 return view('admin.expenditure.tracking_ro', ['user_data' => $d,'ele_details' => $ele_details,"cand_finalize_ro" =>array(),"electionType"=>$electionType,"expenditureData" => $ReportData,"total_rec"=>count($ReportData),"nature_of_default_ac"=>$nature_of_default_ac]);

        }
        catch (\Exception $e) {
    return $e->getMessage();
		}

	}
	else
	{
		return redirect('/officer-login');
	}


    }
     public function editExpenditureData(Request $request,$ReportID)
    {
         if (Auth::check()) {
        $request = (array) $request->all();
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        $d = $this->expenditureModel->getunewserbyuserid($user->id);

        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
        if ($check_finalize == '') {
            $cand_finalize_ceo = 0;
            $cand_finalize_ro = 0;
        } else {
            $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
            $cand_finalize_ro = $check_finalize->finalized_ac;
        }
        $seched = getschedulebyid($ele_details->ScheduleID);
        $sechdul = checkscheduledetails($seched);
        $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status','1')->get()->toArray();
        $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray(); 

        try{



            $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData(base64_decode($ReportID));
            //print_r($ReportSingleData);die;
            /////check last data inserted for preview/////
                          $PreviewData[0]=array();
                        if ( isset( $_GET['id'] ) && !empty( $_GET['id'] ) ){
                            $lastInserted = base64_decode($_GET['id']);
                            $PreviewData = $this->expenditureModel->singledata($lastInserted);
                        }
return view('admin.expenditure.createmisexpensereport', ['user_data' => $d,'ele_details' => $ele_details,"cand_finalize_ro" =>array(),"electionType"=>$electionType,"ReportSingleData" => $ReportSingleData[0],"nature_of_default_ac"=>$nature_of_default_ac,"PreviewData"=>$PreviewData[0]]);

        }
        catch (\Exception $e) {
            return $e->getMessage();
        }




        }
        else
        {
          return redirect('/officer-login');  
        }


    }
    public function getElectedCandidate($candidate_id,$ac_no){
        $user = Auth::user();
        $acdetail = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.candidate_id', $candidate_id)
        ->where('candidate_nomination_detail.ac_no', $ac_no)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.party_id', '<>', '1180')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->first();
         $ac_no = !empty($acdetail->ac_no) ? $acdetail->ac_no : 0;
         $st_code = !empty($acdetail->st_code) ? $acdetail->st_code : 0; 
        $ELECTION_ID = !empty($acdetail->election_id) ? $acdetail->election_id : 0;
        $countElectedCandidate=DB::table('winning_leading_candidate')->where('st_code', $st_code)
                              ->where('ac_no', $ac_no)
                              ->where('election_id', $ELECTION_ID)
                              ->where('candidate_id', $candidate_id)
                              ->count();
        return $countElectedCandidate;
    }
     public function StoreMisExpenseReport(Request $request) {

       
        $request = (array) $request->all();
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $uid = $user->id;
        $role_id = $user->role_id;
        $candidate_id = $request['candidate_id'];
        $constituency_id = $request['constituency_id'];
        $comment_by_ro = $request['comment_by_ro'];
        $request['user_id'] = $uid;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        // check elected candidate start
        $isElectedCandidate=$this->getElectedCandidate($candidate_id,$constituency_id);
        if($isElectedCandidate>0){
           
            $request['return_status']=='Returned';           
            
        }else{
            
            $request['return_status']=='Non-Returned';            
            
        }
        // check elected candidate end
  
        try {
            $data_arr = array();
            foreach ($request as $key => $req_data) {
                $xss = new xssClean;
                $data_arr[$key] = $xss->clean_input($req_data);
            }
 
            $isexistData = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->where('constituency_no', $constituency_id)->count();

                if($isexistData>0){
                      $unsetItems = ['candidate_id', 'constituency_no', 'constituency_nos', 'contensting_candiate',
                        'date_of_declaration', 'user_id','constituency_id'];
                         $dataUpdate = array_diff_key($data_arr, array_flip($unsetItems));

                         //  dd($dataUpdate);
                     $status = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->where('constituency_no', $constituency_id)->update($dataUpdate);
                    ////////////////////////////////// add entry in expenditure action logs/////////////////
                   
               $cdate = date('Y-m-d h:i:s');
               $data_action=array("candidate_id"=>$candidate_id,"deo_action_date"=>$cdate,"deo_comment"=>$comment_by_ro);

               // $data_arr_action = array();
               //  foreach ($data_action as $key => $req_data_action) {
               //      $xss = new xssClean;
               //      $data_arr_action[$key] = $xss->clean_input($req_data_action);
               //  }

               //  $data_actionInserted = $this->commonModel->updatedata('expenditure_action_logs', 'candidate_id', $candidate_id, $data_arr_action);

               // $check_exits_log = DB::table('expenditure_action_logs')->where('deo_action_date','!=',"")->where('candidate_id',$candidate_id)->first();
               // if(count($check_exits_log)>0){
               //     $data_actionInserted = $this->commonModel->updatedata('expenditure_action_logs', 'candidate_id', $candidate_id, $data_arr_action);
               //  }
               //  else{
               //   $data_actionInserted = $this->commonModel->insertData('expenditure_action_logs', $data_arr_action);
               //  }
              ///////////////////////////////////////// end entry in expenditure logs///////////////////

                }else{
                   
                        $unsetItems = [ 'constituency_nos','user_id'];
                        $dataUpdate = array_diff_key($data_arr, array_flip($unsetItems));
                         
                      $status=1;
                       //$status = DB::table('expenditure_reports')->insert($dataUpdate);
                }
           

            if ($status > 0) {

                Session::put('message', "Saved successfully");
                return redirect($namePrefix . '/editExpenditureReport?candidate_id=' . base64_encode($candidate_id).'&ac_no='.base64_encode($constituency_id));
            } else {
                Session::put('message', "No change");
                return redirect($namePrefix . '/editExpenditureReport?candidate_id=' . base64_encode($candidate_id).'&ac_no='.base64_encode($constituency_id));
            }
        } catch (\Exception $e) {

            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }
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
            //dd($cand_data); 
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

                $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id,$CONST_NO);

                //dd($ReportSingleData);
                if (!empty($ReportSingleData)) {
                    $ReportSingleData = (array) $ReportSingleData[0];
                } else {
                    $ReportSingleData = array();
                } 
                $countElectedCandidate=$this->getElectedCandidate($candidate_id,$CONST_NO);
              
                return view('admin.expenditure.createmisexpensereport', ["cand_data"=>$cand_data,'user_data' => $d, 'ele_details' => $ele_details, "cand_finalize_ro" => array(), "electionType" => $electionType, "ReportSingleData" => $ReportSingleData, "nature_of_default_ac" => $nature_of_default_ac, "candidate_data" => (array) $candidate_data,'Acdetail'=>$Acdetail,'countElectedCandidate'=>$countElectedCandidate,'resultDeclarationDate'=>$resultDeclarationDate]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            return redirect('/officer-login');
        }
    }
 public function confirmReport()
    {
   // dd("ssds");
        $candidate_id = !empty($_GET['candidate_id'])?$_GET['candidate_id']:"";
        $constituency_no = !empty($_GET['acno'])?$_GET['acno']:"";


        if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $st_code=$d->st_code;
                $pc_no=$d->pc_no;
                $insertdata=['candidate_id'=>$candidate_id,'st_code'=>$st_code,'constituency_no'=>$constituency_no,'deo_action'=>'1'];
                                
            }

      //  $insertComment = $this->commonModel->updatedata('expenditure_reports','candidate_id',$candidate_id,array("final_by_ro"=>'1'));
         $insertComment=DB::table('expenditure_reports')
         ->where('candidate_id', $candidate_id)
         ->where('constituency_no', $constituency_no)
         ->update(['final_by_ro' => '1']);


          if($insertComment)
          {
            $this->commonModel->insertData('expenditure_notification',$insertdata);
    
            return 1;
          }
          else
          {
            return 0;
          }
    }
    public function GetProfileRO(Request $request) {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $d = $this->commonModel->getunewserbyuserid($user->id);
               $candidate_id = !empty($_GET['candidate_id'])? $_GET['candidate_id']:0;
             $candiatePcName = DB::table('candidate_nomination_detail')                     
                     ->join('m_ac',function($join){
                 $join->on('candidate_nomination_detail.st_code','=','m_ac.ST_CODE')
                         ->on('candidate_nomination_detail.ac_no','=','m_ac.AC_NO');                
                 
             })
               ->select('m_ac.AC_NAME','m_ac.AC_NO')
                     ->where('candidate_nomination_detail.candidate_id','=',$candidate_id)
                     ->where('candidate_nomination_detail.st_code','=',$d->st_code)
                     ->where('candidate_nomination_detail.district_no','=',$d->dist_no)->first();
                
             

                
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
                                'ReportSingleData', 'electionType', 'nature_of_default_ac', 'current_status','candiatePcName'));
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }
     public function getProfile(Request $request) {

        try {
            if (Auth::check()) {
                $user = Auth::user();
                $d = $this->commonModel->getunewserbyuserid($user->id);

               $candidate_id = !empty($_GET['candidate_id'])? $_GET['candidate_id']:0;
               $ac_id = !empty($_GET['ac_no'])? $_GET['ac_no']:0;

                //dd($candidate_id);
             $candiatePcName = DB::table('candidate_nomination_detail')                     
                     ->join('m_ac',function($join){
                 $join->on('candidate_nomination_detail.st_code','=','m_ac.ST_CODE')
                         ->on('candidate_nomination_detail.ac_no','=','m_ac.AC_NO');                
                 
             })
               ->select('m_ac.AC_NAME','m_ac.AC_NO')
                     ->where('candidate_nomination_detail.candidate_id','=',$candidate_id)
                     ->where('candidate_nomination_detail.st_code','=',$d->st_code)

                     ->where('candidate_nomination_detail.district_no','=',$d->dist_no)->first();
                
             

                
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
                         ->where('candidate_nomination_detail.ac_no','=',$ac_id)
                        ->where('m_election_details.CONST_TYPE', '=', 'AC')
                        ->get();
                // get CEO status
                    $nomicnationdetails= DB::table('candidate_nomination_detail')
                            ->join("m_election_details", function($join) {
                            $join->on("m_election_details.st_code", "=", "candidate_nomination_detail.st_code")
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                        })                            
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.party_id', '<>', '1180')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.candidate_id', '=', $candidate_id)
                         ->where('candidate_nomination_detail.ac_no','=',$ac_id)
                        ->where('m_election_details.CONST_TYPE', '=', 'AC')
                        ->first();
                        $st_code=!empty($nomicnationdetails->st_code)?$nomicnationdetails->st_code:0 ;
                        $dist_no=!empty($nomicnationdetails->dist_no)? $nomicnationdetails->dist_no:0;
                        $ac_no=!empty($nomicnationdetails->ac_no)?$nomicnationdetails->ac_no:0;
                        $party_id=!empty($nomicnationdetails->party_id)?$nomicnationdetails->party_id:0;
                        $Acdetail=DB::table('m_ac')
                                ->select('m_ac.AC_NO','m_ac.AC_NAME')
                                ->where('m_ac.ST_CODE',$st_code)
                                ->where('m_ac.AC_NO',$ac_no)
                                ->first();
                    
                $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
                $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
                $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();
                $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id,$ac_no);
                if (!empty($ReportSingleData)) {

                    $ReportSingleData = (array) $ReportSingleData[0];
                }
           
                return view('admin.expenditure.GetProfile', compact('profileData',
                                'ReportSingleData', 'electionType', 'nature_of_default_ac', 'current_status','candiatePcName','Acdetail','party_id','st_code'));
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }
public function printTrackingStatus($candidateId) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $mpdf = new \Mpdf\Mpdf();
             $candidate_id = base64_decode($candidateId);
              $candiatePcName = DB::table('candidate_nomination_detail')                     
                     ->join('m_ac',function($join){
                 $join->on('candidate_nomination_detail.st_code','=','m_ac.ST_CODE')
                         ->on('candidate_nomination_detail.ac_no','=','m_ac.AC_NO');                
                 
             })
               ->select('m_ac.AC_NAME','m_ac.AC_NO')
                     ->where('candidate_nomination_detail.candidate_id','=',$candidate_id)
                     ->where('candidate_nomination_detail.st_code','=',$d->st_code)
                     ->where('candidate_nomination_detail.district_no','=',$d->dist_no)->first();

           
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
//            $ELECTION_TYPE = !empty($profileData[0]) ? $profileData[0]->ELECTION_TYPE : '';
             $ELECTION_TYPE = "AC";
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
            $year=date('Y');
            $title = $date . '_' . "Election Commission of India";
            $mpdf->setHeader($candidateName . ' | ' . $ELECTION_TYPE . ' '.$year.' | ' . $partyname);

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
                            'ReportSingleData', 'electionType', 'nature_of_default_ac', 'current_status','candiatePcName'));
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();
             
        } else {
            return redirect('/officer-login');
        }
    }
    // new added by manoj
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
         ->where('constituency_no', $ac_no)->where('status', '1')
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
    
  // manoj
  public function trackingReport(Request $request) {
    try {
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
      
   
       $ELECTION_ID=!empty($ele_details[0]->ELECTION_ID)?$ele_details[0]->ELECTION_ID:0;
     //  $CONST_NO=!empty($ele_details[0]->CONST_NO)?$ele_details[0]->CONST_NO:0;
       
         
        $dist_no = $request->input('dist_no');
        $stcode = $request->input('st_code');
        $ac_no = $request->input('ac');
        $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $stcode)->where('ac_no', $ac_no)->where('election_id', $ELECTION_ID)->first();
       
        $stateDetail = getstatebystatecode($stcode);
         
        $acdetail = getacbyacno($stcode, $ac_no);

        $districtDetails = getdistrictbydistrictno($stcode, $dist_no);

        

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
                ->where('candidate_nomination_detail.st_code', $stcode)
                ->where('candidate_nomination_detail.ac_no', $ac_no)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.party_id', '<>', '1180')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('m_election_details.CONST_TYPE', '=', 'AC')
                ->groupBy('candidate_personal_detail.candidate_id')
                ->get();
                
               
      
        
      if(!empty($candList)){
                  $i=0;
               foreach($candList as $cand){
                  $expenditure_understates = DB::table('expenditure_understates')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$ac_no)->where('understated_type_id','9')->first();
                 // print_r($expenditure_understates);die;
                  /* $other_source_cc = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$ac_no)
                          ->whereIn('other_source_payment_mode',array('Cheque','Cash'))->sum('other_source_amount'); */
						  
/* $other_source_cc = DB::select("select sum(A.other_source_amount)as other_source_amount
FROM expenditure_fund_source A
INNER JOIN
(
 select  id,candidate_id,other_source_payment_mode,other_source_amount,
    Row_number() over(PARTITION BY candidate_id,other_source_payment_mode ORDER BY id desc) AS RN
 from expenditure_fund_source
 where candidate_id='".$cand->candidate_id."'
 and other_source_payment_mode in('Cash','Cheque')

) as B ON A.id=B.id AND B.RN=1
GROUP BY A.candidate_id"); */


$other_source_cc = DB::select("select  sum(other_source_amount)as other_source_amount,temp.candidate_id
from
(
 select  candidate_id,other_souce_name,other_source_payment_mode,other_source_amount
 from expenditure_fund_source
 where candidate_id='".$cand->candidate_id."'
 and other_source_payment_mode in('Cash','Cheque')
ORDER BY id desc LIMIT 2
)temp
group by candidate_id");


                  $other_source_kind = DB::table('expenditure_fund_source')->
                  where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->
                  where('constituency_no',$ac_no)
                          ->whereIn('other_source_payment_mode',array('In Kind'))->sum('other_source_amount');
                  $candList[$i]->comment_9 = !empty($expenditure_understates->comment)?$expenditure_understates->comment:"";
                  $candList[$i]->understated_type_id_9 = !empty($expenditure_understates->understated_type_id)?$expenditure_understates->understated_type_id:"";
                  //$candList[$i]->other_source_amt_cc = !empty($other_source_cc)?$other_source_cc:"0";
				  $candList[$i]->other_source_amt_cc = !empty($other_source_cc[0]->other_source_amount)?$other_source_cc[0]->other_source_amount:"0";
                  $candList[$i]->other_source_amt_kind = !empty($other_source_kind)?$other_source_kind:"0";
                  $i++;
               }
              }

       // add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj 

        return view('admin.expenditure.tracking_report', ['user_data' => $d,
            'ele_details' => $ele_details, "cand_finalize_ro" => array(),
            'candList' => $candList,
            'acdetail' => $acdetail,'ac_no'=>$ac_no,'dist_no'=>$dist_no, 'stateDetail' => $stateDetail, "districtDetails" => $districtDetails, 'winn_data' => $winn_data,'resultDeclarationDate'=>$resultDeclarationDate]);
    } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
}

public function trackingReportprint(Request $request,$acno) {
    try {
        $mpdf = new \Mpdf\Mpdf();
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ac_no = !empty($acno)? base64_decode($acno):0;
        $stcode = $d->st_code;
        $dist_no=$d->dist_no;
        
       
        $ele_details = $this->commonModel->election_detailsac($d->st_code, $ac_no, $dist_no, $d->id, $d->officerlevel);
      
   
       $ELECTION_ID=!empty($ele_details[0]->ELECTION_ID)?$ele_details[0]->ELECTION_ID:0;
       
       
        $winn_data = DB::table('winning_leading_candidate')->select('leading_id', 'st_code', 'ac_no', 'nomination_id', 'candidate_id', 'trail_nomination_id', 'trail_candidate_id', 'lead_total_vote', 'trail_total_vote', 'margin', 'status', 'lead_cand_name', 'lead_cand_hname', 'lead_cand_party', 'lead_cand_hparty', 'trail_cand_name', 'trail_cand_hname', 'trail_cand_party', 'trail_cand_hparty')->where('st_code', $stcode)->where('ac_no', $ac_no)->where('election_id', $ELECTION_ID)->first();
    
        $stateDetail = getstatebystatecode($stcode);
         
        $acdetail = getacbyacno($stcode, $ac_no);
        
        $acName=!empty($acdetail->AC_NAME)?$acdetail->AC_NAME:'';
        $stateName=!empty($stateDetail->ST_NAME)?$stateDetail->ST_NAME:'';

        $districtDetails = getdistrictbydistrictno($stcode, $dist_no);

        

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
                ->where('candidate_nomination_detail.st_code', $stcode)
                ->where('candidate_nomination_detail.ac_no', $ac_no)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.party_id', '<>', '1180')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('m_election_details.CONST_TYPE', '=', 'AC')
                ->groupBy('candidate_personal_detail.candidate_id')
                ->get();
                
               
      
        
              
        
        $date = date('d-m-Y');

        $ELECTION_TYPE = !empty($ele_details[0]->ELECTION_TYPE) ? $ele_details[0]->ELECTION_TYPE : '';
        $date = date('d-m-Y');
        $year = !empty($ele_details[0]->YEAR)?$ele_details[0]->YEAR:'';
        $title = $date . '_' . "Election Commission of India";
        $mpdf->setHeader($acName . ' | ' . $ELECTION_TYPE . ' AC ' . $year . ' | ' . $stateName);

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
                ->where('candidate_nomination_detail.st_code', $stcode)
                ->where('candidate_nomination_detail.ac_no', $ac_no)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.party_id', '<>', '1180')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('m_election_details.CONST_TYPE', '=', 'AC')
                ->groupBy('candidate_personal_detail.candidate_id')
                ->get();
                if(!empty($candList)){
                    $i=0;
                 foreach($candList as $cand){
                    $expenditure_understates = DB::table('expenditure_understates')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$ac_no)->where('understated_type_id','9')->first();
                   // print_r($expenditure_understates);die;

                   $other_source_cc = DB::select("select  sum(other_source_amount)as other_source_amount,temp.candidate_id
from
(
 select  candidate_id,other_souce_name,other_source_payment_mode,other_source_amount
 from expenditure_fund_source
 where candidate_id='".$cand->candidate_id."'
 and other_source_payment_mode in('Cash','Cheque')
ORDER BY id desc LIMIT 2
)temp
group by candidate_id");
                   // $other_source_cc = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$ac_no)
                       //     ->whereIn('other_source_payment_mode',array('Cheque','Cash'))->sum('other_source_amount');
                    $other_source_kind = DB::table('expenditure_fund_source')->where('candidate_id',$cand->candidate_id)->where('ST_CODE',$stcode)->where('constituency_no',$ac_no)
                            ->whereIn('other_source_payment_mode',array('In Kind'))->sum('other_source_amount');
                            
                    $candList[$i]->comment_9 = !empty($expenditure_understates->comment)?$expenditure_understates->comment:"";
                    $candList[$i]->understated_type_id_9 = !empty($expenditure_understates->understated_type_id)?$expenditure_understates->understated_type_id:"";
                   // $candList[$i]->other_source_amt_cc = !empty($other_source_cc)?$other_source_cc:"0";
                    $candList[$i]->other_source_amt_cc = !empty($other_source_cc[0]->other_source_amount)?$other_source_cc[0]->other_source_amount:"0";
                    $candList[$i]->other_source_amt_kind = !empty($other_source_kind)?$other_source_kind:"0";
                    $i++;
                 }
                }
// add 24/10/2019 manoj
        $resultDeclarationDate = $this->expenditureModel->getResultDeclarationDate();
        // end 24/10/2019 manoj 


        $pdf = view('admin.expenditure.pdf_tracking_report', compact('candList', 'stateDetail', 'districtDetails', 'acdetail', 'winn_data','resultDeclarationDate'));
        $mpdf->WriteHTML($pdf);
        $mpdf->Output();
    } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
}
public function getSummary(Request $request) {
   
    if (Auth::check()) {
        $user = Auth::user();
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
         
        // $all_ac = DB::table('dist_pc_mapping')
        //         ->join("m_ac", function($join) {
        //             $join->on("m_ac.ST_CODE", "=", "dist_pc_mapping.ST_CODE")
        //             ->on("m_ac.AC_NO", "=", "dist_pc_mapping.AC_NO");
        //         })
        //         ->where('dist_pc_mapping.st_code', $d->st_code)
        //         ->where('dist_pc_mapping.dist_no', $d->dist_no)
        //         // ->groupBy('PC_NAME_EN')
        //         ->get();
        $all_ac =DB::table('m_ac')
        ->leftjoin("candidate_nomination_detail",function($join){
         $join->on("candidate_nomination_detail.ST_CODE","=","m_ac.ST_CODE")
             ->on("candidate_nomination_detail.ac_no","=","m_ac.AC_NO");
           })
        ->where('m_ac.ST_CODE',$d->st_code)
        ->where('m_ac.DIST_NO_HDQTR',$d->dist_no)
        ->groupBy('candidate_nomination_detail.ac_no')
        ->get();
        //dd($all_ac);
        return view('admin.ac.deo.Expenditure.SummaryReport', ['user_data' => $d, 'ele_details' => $ele_details, 'all_ac' => $all_ac]);
    } else {
        return redirect('/officer-login');
    }
}
public function getReturn(Request $request) {
        
    try {
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;              
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
     
            $st_code=$d->st_code;
            $dist_no=$d->dist_no; 
             
             
                          
          
             $returnCandList = DB::table('expenditure_reports')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('candidate_nomination_detail.district_no',$dist_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.return_status', '=', 'Returned') 
                    ->where('expenditure_reports.finalized_status', '=', '1')
                    ->where('expenditure_reports.final_by_ro', '=', '1')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();         
          
                $count=!empty($returnCandList)?count($returnCandList):0;
          
            
            return view('admin.ac.deo.Expenditure.return-report', ['user_data' => $d, 'returnCandList' => $returnCandList ,
                'edetails' => $ele_details, "count" => $count,
                
                    ]);
        } else {
            return redirect('/officer-login');
        }
    } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }//PC ROPC candidateListByfiledData TRY CATCH ENDS HERE   
}
 public function getNonReturn(Request $request) {
    
    try {
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
            $st_code=$d->st_code;
            $dist_no=$d->dist_no; 
            
                $nonreturnCandList = DB::table('expenditure_reports')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('candidate_nomination_detail.district_no',$dist_no)              
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.return_status', '=', 'Non-Returned')
                    ->where('expenditure_reports.finalized_status', '=', '1')
                    ->where('expenditure_reports.final_by_ro', '=', '1')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();
          
                $count=!empty($nonreturnCandList)?count($nonreturnCandList):0;
            
            return view('admin.ac.deo.Expenditure.non-return-report', ['user_data' => $d, 'nonreturnCandList' => $nonreturnCandList ,
                'edetails' => $ele_details, "count" => $count,
                  
                ]);
        } else {
            return redirect('/officer-login');
        }
    } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
    } 
}


public function updateStatusReport(Request $request) {
         if (Auth::check()) {
         $user = Auth::user();
         $uid = $user->id;

        $candidateId = $_GET['candidate_id'];
        $reason = $_GET['reason'];

       // $getLog = DB::table('expenditure_logs')->where('created_by',$uid)->where('candidate_id',$candidateId)->first();
        // $countByCEO = !empty($getLog)?$getLog->count_by_ceo:0;
        // $count_by_ceo = $countByCEO + 1;
        $data_definalization = array('candidate_id'=>$candidateId,'created_by'=>$uid,'updated_by'=>$uid,'comment'=>$reason,"count_by_ro"=>'1','log_type'=>'DEFINALIZATION','officer_level'=>'DEO');

        if ($candidateId){
            $updateStatus = $this->commonModel->updatedata('expenditure_reports', 'candidate_id', $candidateId, array("finalized_status" => "0","final_by_ro"=>'0'));
            $insertLog = $this->commonModel->insertData('expenditure_logs', $data_definalization);

            if ($updateStatus){
                Session::put('message', "Permission sent for the updation of scrutiny report successfully.");
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
        }
        else
        {
            return 0;
        }
    }



}  // end class