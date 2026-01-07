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
    use MPDF;
    use App\commonModel;  
    use App\adminmodel\CEOModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\models\Expenditure\ExpenditureModel;
    use App\Classes\xssClean;
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\Crypt;
    use App\models\Expenditure\DeoexpenditureModel;
    use App\models\Expenditure\EciExpenditureModel;
	use DateTime;
class ACCeoExpenditureController extends Controller
{
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
            /*config(['database.connections.mysql.host' => '10.247.137.49']);
            config(['database.connections.mysql.database' => $this->expdb]);
            config(['database.connections.mysql.username' => 'gotosuvidha']);
            config(['database.connections.mysql.password' => 'asbhi%supqwe!@1234']);
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
            // /* config(['database.connections.mysql.username' => 'gotosuvidha']);
            // config(['database.connections.mysql.password' => 'asbhi%supqwe!@1234']); */
            // config(['database.connections.mysql.username' => 'suvidhaapp']);
            // config(['database.connections.mysql.password' => 'P7$b&n#367BYaRt91']);
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
        $this->eciexpenditureModel = new EciExpenditureModel();
        $this->xssClean = new xssClean;
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
 * Calculate percetage between the numbers
 */

function get_percentage($total, $number)
  {
  if ( $total > 0 ) {
   return round($number / ($total / 100),2);
  } else {
    return 0;
  }
   }//end number
      /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 07-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return dashboard By CEO fuction     
     */
    public function dashboard(Request $request){ 
       //AC CEO dashboard TRY CATCH STARTS HERE
       try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
           $st_code=$d->st_code;
           $cons_no=$request->input('ac');
           $cons_no=!empty($cons_no) ? $cons_no : '0';
          // echo $st_code.'AC'.$cons_no; 
           if($cons_no =='0'){  
           $totalContestedCandidate = DB::table('candidate_nomination_detail')
           ->selectRaw('count("candidate_nomination_detail.candidate_id") as cnt')
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
           ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
           ->where('candidate_nomination_detail.st_code','=',$st_code)
           //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->Where('candidate_nomination_detail.party_id', '<>', '1180')
           ->groupBy('candidate_nomination_detail.candidate_id')
           ->get();
           $totalContestedCandidate=count($totalContestedCandidate);
           }else{
            $totalContestedCandidate = DB::table('candidate_nomination_detail')
            ->selectRaw('count("candidate_nomination_detail.candidate_id") as cnt')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
            ->where('candidate_nomination_detail.st_code','=',$st_code)
            ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->Where('candidate_nomination_detail.party_id', '<>', '1180')
           ->groupBy('candidate_nomination_detail.candidate_id')
           ->get();
           $totalContestedCandidate=count($totalContestedCandidate);
           }
		  
            //Get Data entry Start Count 
            $startdatacount=$this->eciexpenditureModel->gettotaldataentryStart('AC',$st_code,$cons_no);
            //dd($startdatacount);
           //Get Data entry Start Count %
           $Percent_startdataentry=$this->get_percentage($totalContestedCandidate,$startdatacount);
           
           //Get Data entry finalize Count 
           $finaldatacount=$this->eciexpenditureModel->gettotaldataentryFinal('AC',$st_code,$cons_no);
           //Get Data entry finalize Count %
           $Percent_finaldatacount=$this->get_percentage($totalContestedCandidate,$finaldatacount);
          
           //Get Data entry finalize Count 
           $logedaccount=$this->eciexpenditureModel->gettotallogedAccount('AC',$st_code,$cons_no);
           //Get Data entry finalize Count %
           $Percent_logedaccount=$this->get_percentage($totalContestedCandidate,$logedaccount);

               //Get Data entry finalize Count 
           $notintimeaccount=$this->eciexpenditureModel->gettotalNotinTime('AC',$st_code,$cons_no);
           //Get Data entry finalize Count %
           $Percent_notintimeaccount=$this->get_percentage($totalContestedCandidate,$notintimeaccount);
          

             //Get Defects in format Count 
             $formateDefectscount=$this->eciexpenditureModel->gettotalDefectformats('AC',$st_code,$cons_no);
             //Get Defects in format Count %
             $Percent_formateDefectscount=$this->get_percentage($totalContestedCandidate,$formateDefectscount);

              //Get Defects in format Count 
              $expenseunderstated=$this->eciexpenditureModel->gettotalexpenseUnderStated('AC',$st_code,$cons_no);
              //Get Defects in format Count %
              $Percent_expenseunderstated=$this->get_percentage($totalContestedCandidate,$expenseunderstated);

             //Get total fund from party
             $partyFund=$this->eciexpenditureModel->gettotalPartyfund('AC',$st_code,$cons_no);
             $otherSourcesFund=$this->eciexpenditureModel->gettotalOtherSourcesfund('AC',$st_code,$cons_no);
             $totalFund=($partyFund->total_partyfund + $otherSourcesFund->total_otherSourcesfund);
             //Get party fund %
             $Percent_partyFund=$this->get_percentage($totalFund,$partyFund->total_partyfund);
              //Get OtherSources fund %
             $Percent_OthersourcesFund=$this->get_percentage($totalFund,$otherSourcesFund->total_otherSourcesfund);

            //dd($Percent_startdataentry);
           return view('admin.ac.ceo.Expenditure.dashboard',['user_data' => $d,'startdatacount' => $startdatacount,'Percent_startdataentry' => $Percent_startdataentry,'finaldatacount' => $finaldatacount,'Percent_finaldatacount' => $Percent_finaldatacount,'formateDefectscount' => $formateDefectscount,'Percent_formateDefectscount' => $Percent_formateDefectscount,'expenseunderstated' => $expenseunderstated,'Percent_expenseunderstated' => $Percent_expenseunderstated,'Percent_partyFund' => $Percent_partyFund,'Percent_OthersourcesFund' => $Percent_OthersourcesFund,'edetails'=>$ele_details,'logedaccount'=>$logedaccount,'Percent_logedaccount'=>$Percent_logedaccount,'notintimeaccount'=>$notintimeaccount,'Percent_notintimeaccount'=>$Percent_notintimeaccount,'cons_no'=>$cons_no]); 
          
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
  * @author Devloped Date : 09-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListBydataentryStart By ROPC fuction     
  */
 public function candidateListBydataentryStart(Request $request,$ac){ //dd($request->all());
   //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
          $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));
          $cons_no=!empty($cons_no) ? $cons_no : '0';
		// echo $st_code.'PC'.$cons_no;
        	DB::enableQueryLog();
			if($cons_no !='0'){
          $DataentryStartCandList = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.constituency_no','=',$cons_no) 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
			}else{
				 $DataentryStartCandList = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          //->where('expenditure_reports.constituency_no','=',$cons_no) 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
		    	}
         // dd(DB::getQueryLog());
         $DataentryStartCandListcount =!empty($DataentryStartCandList) ? count($DataentryStartCandList) : 0;
          return view('admin.ac.ceo.Expenditure.dataentrystart-report',['user_data' => $d,'DataentryStartCandList' => $DataentryStartCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>($DataentryStartCandListcount)]); 
         
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
  * @author Devloped Date : 10-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListByfinalizeData By CEO fuction     
  */
 public function candidateListByfinalizeData(Request $request,$ac){
   //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
           $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
         
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));
          $cons_no=!empty($cons_no) ? $cons_no : '0';
     if($cons_no !='0'){
          $finalCandList = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.constituency_no','=',$cons_no) 
          ->where('expenditure_reports.finalized_status','=','1') 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		  ->groupBy('expenditure_reports.candidate_id')
          ->get();
		  }else{
			  $finalCandList = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.finalized_status','=','1') 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
      }
    
      $finalCandListcount = (!empty($finalCandList) ? (count($finalCandList)) : 0);
     
          return view('admin.ac.ceo.Expenditure.finalize-report',['user_data' => $d,'finalCandList' => $finalCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=> $finalCandListcount]); 
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       
       }//PC ROPC candidateListByfinalizeData TRY CATCH ENDS HERE   
    }   // end candidateListByfinalizeData start function

    /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 10-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListBylogedaccount By CEO fuction     
  */
 public function candidateListBylogedaccount(Request $request,$ac){
   //PC ROPC candidateListBylogedaccount TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
           $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));
          $cons_no=!empty($cons_no) ? $cons_no : '0';
          if($cons_no !='0'){
          $logedAccount = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.constituency_no','=',$cons_no) 
          ->where('expenditure_reports.candidate_lodged_acct','=','Yes') 
          ->where('expenditure_reports.finalized_status','=','1') 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
		     }else{
			   $logedAccount = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.candidate_lodged_acct','=','Yes') 
          ->where('expenditure_reports.finalized_status','=','1') 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
         }
        $logedAccountcount = (!empty($logedAccount) ? (count($logedAccount)) : 0);
           //dd($logedAccount);
          return view('admin.ac.ceo.Expenditure.logedaccount-report',['user_data' => $d,'logedAccount' => $logedAccount,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>($logedAccountcount)]); 
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       
       }//PC ROPC candidateListBylogedaccount TRY CATCH ENDS HERE   
    }   // end candidateListBylogedaccount start function

    /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 09-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListBynotintime By ROPC fuction     
  */
 public function candidateListBynotintime(Request $request,$ac){
   //PC ROPC candidateListBynotintime TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
           $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));
          $cons_no=!empty($cons_no) ? $cons_no : '0';
          if($cons_no !='0'){
          $notinTime = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.constituency_no','=',$cons_no) 
          ->where('expenditure_reports.finalized_status','=','1') 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          ->Where('expenditure_reports.account_lodged_time', '=', 'NO')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
		     }else{
			    $notinTime = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.finalized_status','=','1') 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          ->Where('expenditure_reports.account_lodged_time', '=', 'NO')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
         }
         $notinTimecount = (!empty($notinTime) ? (count($notinTime)) : 0);
           //dd($notinTime);
          return view('admin.ac.ceo.Expenditure.notintime-report',['user_data' => $d,'notinTime' => $notinTime,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>($notinTimecount)]); 
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       
       }//PC ROPC candidateListBynotintime TRY CATCH ENDS HERE   
    }   // end candidateListBynotintime start function

    /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 09-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListBydataentryStart By ROPC fuction     
  */
 public function candidateListByformatedefects(Request $request,$ac){
   //PC ROPC candidateListByformatedefects TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
           $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));
          $cons_no=!empty($cons_no) ? $cons_no : '0';
           if($cons_no !='0'){
          $formateDefects = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.constituency_no','=',$cons_no) 
          ->where('expenditure_reports.rp_act','=','No') 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
		     }else {
			    $formateDefects = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.rp_act','=','No') 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
         }
         $formateDefectscount = (!empty($formateDefects) ? (count($formateDefects)) : 0);
           //dd($formateDefects);
          return view('admin.ac.ceo.Expenditure.formatedefects-report',['user_data' => $d,'formateDefects' => $formateDefects,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>($formateDefectscount)]); 
         
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
  * @author Devloped Date : 09-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListByronotagree By ROPC fuction     
  */
 public function candidateListByronotagree(Request $request,$ac){
   //PC ROPC candidateListByronotagree TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
           $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));
          $cons_no=!empty($cons_no) ? $cons_no : '0';
          if($cons_no !='0'){
          $candidateListByronotagree = DB::table('expenditure_reports')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.constituency_no','=',$cons_no) 
          ->get();
		      }else {
		    	$candidateListByronotagree = DB::table('expenditure_reports')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->where('expenditure_reports.ST_CODE','=',$st_code)
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
		     }
         $candidateListByronotagreecount = (!empty($candidateListByronotagree) ? (count($candidateListByronotagree)) : 0);
          return view('admin.ac.ceo.Expenditure.ronotagree-report',['user_data' => $d,'DataentryStartCandList' => $DataentryStartCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>($candidateListByronotagreecount)]); 
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       
       }//PC ROPC candidateListByronotagree TRY CATCH ENDS HERE   
    }   // end candidateListByronotagree start function

    /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 10-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListByunderstatedexpense By CEO fuction     
  */
 public function candidateListByunderstatedexpense(Request $request,$ac){
   //PC ROPC candidateListByunderstatedexpense TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
          $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));
          $cons_no=!empty($cons_no) ? $cons_no : '';
          if($cons_no !='0'){
          $expenseunderstated = DB::table('expenditure_understated')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
          ->where('expenditure_understated.ST_CODE','=',$st_code)
          ->where('expenditure_understated.constituency_no','=',$cons_no) 
          ->where('expenditure_understated.page_no_observation','=',"No") 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_understated.candidate_id')
          ->get();
		     }else{
			    $expenseunderstated = DB::table('expenditure_understated')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
          ->where('expenditure_understated.ST_CODE','=',$st_code)
          //->where('expenditure_understated.constituency_no','=',$cons_no) 
          ->where('expenditure_understated.page_no_observation','=',"No") 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		     ->groupBy('expenditure_understated.candidate_id')
          ->get();
		  }
      $expenseunderstatedcount = (!empty($expenseunderstated) ? (count($expenseunderstated)) : 0);

          return view('admin.ac.ceo.Expenditure.expenseunderstated-report',['user_data' => $d,'expenseunderstated' => $expenseunderstated,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>($expenseunderstatedcount)]); 
         
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
  * @author Devloped Date : 09-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListBydataentrydefects By ROPC fuction     
  */
 public function candidateListBydataentrydefects(Request $request,$ac){
   //PC ROPC candidateListBydataentrydefects TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
          $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          $st_code=$d->st_code;
          $cons_no=$request->input('ac');
          $cons_no=!empty($cons_no) ? $cons_no : '0';
          if($cons_no !=''){
          $candidateListBydataentrydefects = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    

          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.constituency_no','=',$cons_no) 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
		  }else{
			$candidateListBydataentrydefects = DB::table('expenditure_reports')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          //->where('expenditure_reports.constituency_no','=',$cons_no) 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_reports.candidate_id')
          ->get(); 
		  }
      $candidateListBydataentrydefectscount = (!empty($candidateListBydataentrydefects) ? (count($candidateListBydataentrydefects)) : 0);
          return view('admin.ac.ro.Expenditure.dataentrydefect-report',['user_data' => $d,'DataentryStartCandList' => $DataentryStartCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>(count($candidateListBydataentrydefectscount))]); 
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       
       }//PC ROPC candidateListBydataentrydefects TRY CATCH ENDS HERE   
    }   // end candidateListBydataentrydefects start function

    /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 10-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListBypartyfund By CEO fuction     
  */
 public function candidateListBypartyfund(Request $request,$ac){
   //PC ROPC candidateListBypartyfund TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
           $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));
          $cons_no=!empty($cons_no) ? $cons_no : '0';
        if($cons_no !='0'){
          $partyfund = DB::table('expenditure_fund_parties')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id') 
          //->select(DB::raw('IFNULL((political_fund_cash + political_fund_checque + political_fund_kind),0) AS partyfund'))
          ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.candidate_father_name','expenditure_fund_parties.*','m_party.*')
          ->where('expenditure_fund_parties.ST_CODE','=',$st_code)
          ->where('expenditure_fund_parties.constituency_no','=',$cons_no) 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_fund_parties.candidate_id')
          ->get();
	      	}else{
			   $partyfund = DB::table('expenditure_fund_parties')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_parties.candidate_id') 
          //->select(DB::raw('IFNULL((political_fund_cash + political_fund_checque + political_fund_kind),0) AS partyfund'))
          ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.candidate_father_name','expenditure_fund_parties.*','m_party.*')
          ->where('expenditure_fund_parties.ST_CODE','=',$st_code)
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_fund_parties.candidate_id')
          ->get();
		}
          // dd($partyfund);
          return view('admin.ac.ceo.Expenditure.partyfund-report',['user_data' => $d,'partyfund' => $partyfund,'edetails'=>$ele_details,'cons_no'=>$cons_no]); 
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       
       }//PC ROPC candidateListBypartyfund TRY CATCH ENDS HERE   
    }   // end candidateListBypartyfund start function

    /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 10-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListByothersfund By CEO fuction     
  */
 public function candidateListByothersfund(Request $request,$ac){
   //PC ROPC candidateListByothersfund TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
           $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
         
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));
          $cons_no=!empty($cons_no) ? $cons_no : '0';
           if($cons_no !='0'){
          $otherfund = DB::table('expenditure_fund_source')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id') 
          //->select(DB::raw('IFNULL((other_source_amount),0) AS otherSourcesfund'))
          ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.candidate_father_name','expenditure_fund_source.*','m_party.*')
          ->where('expenditure_fund_source.ST_CODE','=',$st_code)
          ->where('expenditure_fund_source.constituency_no','=',$cons_no) 
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		      ->groupBy('expenditure_fund_source.candidate_id')
          ->get();
		     }else{
			    $otherfund = DB::table('expenditure_fund_source')
          ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id') 
          ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_fund_source.candidate_id') 
         // ->select(DB::raw('IFNULL((other_source_amount),0) AS otherSourcesfund'))
          ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_personal_detail.candidate_father_name','expenditure_fund_source.*','m_party.*')
          ->where('expenditure_fund_source.ST_CODE','=',$st_code)
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		   ->groupBy('expenditure_fund_source.candidate_id')
          ->get();
		   }
           //dd($DataentryStartCandList);
          return view('admin.ac.ceo.Expenditure.otherfund-report',['user_data' => $d,'otherfund' => $otherfund,'edetails'=>$ele_details,'cons_no'=>$cons_no]); 
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       
       }//PC ROPC candidateListByothersfund TRY CATCH ENDS HERE   
    }   // end candidateListByothersfund start function

    /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 09-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListByexeedceiling By ROPC fuction     
  */
 public function candidateListByexeedceiling(Request $request,$ac){
   //PC ROPC candidateListByexeedceiling TRY CATCH STARTS HERE
   try{
    if(Auth::check()){
          $user = Auth::user();
           $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
          $st_code=$d->st_code;
          $xss = new xssClean;
          $cons_no= base64_decode($xss->clean_input($ac));

          if($cons_no !='0'){
          $DataentryStartCandList = DB::table('expenditure_reports')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          ->where('expenditure_reports.constituency_no','=',$cons_no) 
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
		      }else{
			    $DataentryStartCandList = DB::table('expenditure_reports')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
          ->where('expenditure_reports.ST_CODE','=',$st_code)
          //->where('expenditure_reports.constituency_no','=',$cons_no) 
		      ->groupBy('expenditure_reports.candidate_id')
          ->get();
		   }
           //dd($DataentryStartCandList);
          return view('admin.ac.ceo.Expenditure.exceedceiling-report',['user_data' => $d,'DataentryStartCandList' => $DataentryStartCandList,'cand_finalize_ceo' =>$cand_finalize_ceo,'cand_finalize_ro' => $cand_finalize_ro,'sechdul' => $sechdul,'sched'=>$seched,'edetails'=>$ele_details,'cons_no'=>$cons_no]); 
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       
       }//PC ROPC candidateListByexeedceiling TRY CATCH ENDS HERE   
    }   // end candidateListByexeedceiling start function

   ########################status dashboard by Niraj 16-05-2019###################
  /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 07-05-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return statusdashboard By CEO fuction     
     */
    public function statusdashboard(Request $request){
      //PC CEO dashboard TRY CATCH STARTS HERE

    try{
    if(Auth::check()){
          $user = Auth::user();
          $uid=$user->id;
          $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
         
          $st_code=$d->st_code;
          $cons_no=$request->input('ac');
          $cons_no=!empty($cons_no) ? $cons_no : '0';
         // echo $st_code.'PC'.$cons_no; 

          $ceoscrutinycandidatecount = DB::table('expenditure_notification')
                  ->leftjoin('candidate_nomination_detail', 'expenditure_notification.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                           ->leftjoin('expenditure_reports','expenditure_reports.candidate_id','=','expenditure_notification.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->where('candidate_personal_detail.cand_name', '<>', 'NOTA')
              ->where('expenditure_notification.st_code','=',$st_code)
              ->where('expenditure_notification.ceo_read_status', '=', '0')
                   ->Where('expenditure_reports.final_by_ro','=','1')
                            ->count();
      //dd($ceoscrutinycandidatecount);
      $request->session()->put('countscrutiny', $ceoscrutinycandidatecount);
          
          if($cons_no =='0'){  
          $totalContestedCandidate = DB::table('candidate_nomination_detail')
          //->select(array('candidate_nomination_detail.*', DB::raw('COUNT(candidate_nomination_detail.candidate_id) as candidate_id')))
          ->selectRaw('count("candidate_nomination_detail.candidate_id") as cnt')
          ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
          ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
          ->where('candidate_nomination_detail.st_code','=',$st_code)
          ->where('candidate_nomination_detail.application_status','=','6')
          ->where('candidate_nomination_detail.finalaccepted','=','1')
          ->where('candidate_nomination_detail.symbol_id','<>','200')
          ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          ->Where('candidate_nomination_detail.party_id', '<>', '1180')
          ->groupBy('candidate_nomination_detail.candidate_id')
          ->get();

          $totalContestedCandidate=count($totalContestedCandidate);
           //dd($totalContestedCandidate);
           // ->groupBy('candidate_nomination_detail.candidate_id');
           // $totalContestedCandidate = $totalContestedCandidate->count(DB::raw("DISTINCT (concat(candidate_nomination_detail.candidate_id,candidate_nomination_detail.ac_no))"));
          //->count("DISTINCT (concat(candidate_nomination_detail.candidate_id,candidate_nomination_detail.ac_no))");

          $totalElectedCandidate=DB::table('winning_leading_candidate')
          ->where('winning_leading_candidate.st_code','=',$st_code)                         
          ->count();
          }else{
           $totalContestedCandidate = DB::table('candidate_nomination_detail')
           ->selectRaw('count("candidate_nomination_detail.candidate_id") as cnt')
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
           ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
           ->where('candidate_nomination_detail.st_code','=',$st_code)
           ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->groupBy('candidate_nomination_detail.candidate_id')
            ->get();
           $totalContestedCandidate=count($totalContestedCandidate);

           $totalElectedCandidate=DB::table('winning_leading_candidate')
           ->where('winning_leading_candidate.st_code','=',$st_code)
           ->where('winning_leading_candidate.ac_no','=',$cons_no)
           ->count();
          }
		  
		  
         
           //Get Data entry Start Count 
           $startdatacount=$this->eciexpenditureModel->gettotaldataentryStart('AC',$st_code,$cons_no);
           //dd($startdatacount);

           //get pending data count


           $pendingdatacount= $totalContestedCandidate- $startdatacount;
           //Get Data entry Start Count %
           $Percent_pendingdatacount=$this->get_percentage($totalContestedCandidate,$pendingdatacount);
          
            //get partially pending data count
            $finalbyDEO=$this->eciexpenditureModel->gettotalfinalbyDEO('AC',$st_code,$cons_no);
            //Get Data entry Start Count %
			 if($finalbyDEO >= 0 ){
        //dd($finalbyDEO);
               $partiallypendingcount= $totalContestedCandidate -($finalbyDEO);
               } 
            //Get Data entry Start Count %
           $Percent_partiallypendingcount=$this->get_percentage($totalContestedCandidate,$partiallypendingcount);

           //Get Data entry finalize Count 
           $finaldatacount=$this->eciexpenditureModel->gettotaldataentryFinal('AC',$st_code,$cons_no);
           //Get Data entry finalize Count %
           $Percent_finaldatacount=$this->get_percentage($totalContestedCandidate,$finaldatacount);
         
            //get partially pending data count
           $defaulter=$this->eciexpenditureModel->getdefaulter('AC',$st_code,$cons_no);
           $defaultercount = (!empty($defaulter) ? (count($defaulter)) : 0);
         
           //Get Data entry Start Count %
           $Percent_defaultercount=$this->get_percentage($totalContestedCandidate,$defaultercount);
           //Get final by ceo Count 
          $finalbyceocount=$this->eciexpenditureModel->gettotalfinalbyceo('AC',$st_code,$cons_no);
          // dd($finalbyceocount);
           //Get Data entry final by ceo %
           $Percent_finalbyceocount=$this->get_percentage($totalContestedCandidate,$finalbyceocount);
 
            //Get final by eci Count 
            $finalbyecicount=$this->eciexpenditureModel->gettotalfinalbyeci('AC',$st_code,$cons_no);
           
            //Getfinal by eci Count %
            $Percent_finalbyecicount=$this->get_percentage($totalContestedCandidate,$finalbyecicount);
			
			//Get noticeatceocount Count 
		 $noticeatceocount = $this->eciexpenditureModel->gettotalnoticeatCEO('AC', $st_code, $cons_no);

		 //Get noticeatceocount  %
		 $Percent_noticeatceocount = $this->get_percentage($totalContestedCandidate, $noticeatceocount);
         
         
// return /non return start here
$totalElectedCandidate=!empty($totalElectedCandidate)?$totalElectedCandidate:0;
$returncount = $this->expenditureModel->gettotalreturn('AC', $st_code, $cons_no,'Returned');
            
$totalNominationCandiate=$totalContestedCandidate-$totalElectedCandidate;

$nonreturncount = $this->expenditureModel->gettotalreturn('AC', $st_code, $cons_no,'Non-Returned');

 $returncount=!empty($returncount)?count($returncount):0;
 $nonreturncount=!empty($nonreturncount)?count($nonreturncount):0; 

//Getfinal by eci Count %
$Percent_returncount = $this->get_percentage($totalElectedCandidate, $returncount);
$Percent_nonreturncount = $this->get_percentage($totalNominationCandiate, $nonreturncount);
// end here return /non return
$all_ac = getacbystate($st_code);

return view('admin.ac.ceo.Expenditure.statusdashboard',['user_data' => $d,
'pendingdatacount' => $pendingdatacount,
'Percent_finaldatacount' => $Percent_finaldatacount,
'finaldatacount' => $finaldatacount,
'Percent_pendingdatacount' => $Percent_pendingdatacount,
'partiallypendingcount' => $partiallypendingcount,
'Percent_partiallypendingcount' => $Percent_partiallypendingcount,
'defaultercount' => $defaultercount,
'Percent_defaultercount'=>$Percent_defaultercount,
'finalbyceocount' => $finalbyceocount,
'Percent_finalbyceocount' => $Percent_finalbyceocount,
'finalbyecicount' => $finalbyecicount,
'Percent_finalbyecicount' => $Percent_finalbyecicount,
'edetails'=>$ele_details,
'returncount'=>$returncount,
'Percent_returncount'=>$Percent_returncount,
'nonreturncount'=>$nonreturncount,
'Percent_nonreturncount'=>$Percent_nonreturncount,
'noticeatceocount' => $noticeatceocount, 
'Percent_noticeatceocount' => $Percent_noticeatceocount,
'all_ac'=>$all_ac,
'cons_no'=>$cons_no]);
         
      }
      else {
          return redirect('/officer-login');
      }
      } catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');
       
       }//AC CEO dashboard TRY CATCH ENDS HERE    
 
      
	}   // end dashboard function

    /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 16-05-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getpendingcandidateList By ACCEO fuction     
 */
public function getpendingcandidateList(Request $request,$ac){ //dd($request->all());
  //PC ROPC candidateListBydataentryStart TRY CATCH STARTS HERE
  try{
   if(Auth::check()){
         $user = Auth::user();
         $uid=$user->id;
         $d=$this->commonModel->getunewserbyuserid($user->id);
         $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        
         $st_code=$d->st_code;
         $xss = new xssClean;
         $cons_no= base64_decode($xss->clean_input($ac));
         $cons_no=!empty($cons_no) ? $cons_no : '0';
         //echo $st_code.'AC'.$cons_no; die;
           DB::enableQueryLog();
		       $candidate_id=array();
           if($cons_no !='0'){
         $startCandList = DB::table('expenditure_reports')->select('candidate_id')
         ->where('expenditure_reports.ST_CODE','=',$st_code)
         ->where('expenditure_reports.constituency_no','=',$cons_no) 
         ->groupBy('expenditure_reports.candidate_id')
         ->get();
         foreach ($startCandList as $startCandListData) {
           $candidate_id[] = $startCandListData->candidate_id;
          }
        $pendingCandList = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')  
            ->where('candidate_nomination_detail.st_code','=',$st_code)
            ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();
           }else{

              $startCandList = DB::table('expenditure_reports')->select('candidate_id')
              ->where('expenditure_reports.ST_CODE','=',$st_code)
              ->groupBy('expenditure_reports.candidate_id')
              ->get();
              $candidate_id=array();
            
              foreach ($startCandList as $startCandListData) {
                $candidate_id[] = $startCandListData->candidate_id;
               
               }
               
             $pendingCandList = DB::table('candidate_nomination_detail')
             ->join('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
                    
           ->join('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
             ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')  
             ->where('candidate_nomination_detail.st_code','=',$st_code)
             ->where('candidate_nomination_detail.application_status','=','6')
             ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')

->where('candidate_nomination_detail.symbol_id','<>','200')
             ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)->get();


          //    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
          // ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
          // ->where('candidate_nomination_detail.st_code','=',$st_code)
          // ->where('candidate_nomination_detail.application_status','=','6')
          // ->where('candidate_nomination_detail.finalaccepted','=','1')
          // ->where('candidate_nomination_detail.symbol_id','<>','200')
          // ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
          // ->count();
             
           }
        // dd(DB::getQueryLog());
        $pendingCandListcount = (!empty($pendingCandList) ? (count($pendingCandList)) : 0);
         return view('admin.ac.ceo.Expenditure.pending-report',['user_data' => $d,'pendingCandList' => $pendingCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>$pendingCandListcount]); 
        
     }
     else {
         return redirect('/officer-login');
     }
     } catch (Exception $ex) {
       return Redirect('/internalerror')->with('error', 'Internal Server Error');
      
      }//ACCEO candidateListBydataentryStart TRY CATCH ENDS HERE   
   }   // end dataentry start function

   
    /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 20-05-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getpartiallypendingcandidateList By ACCEO fuction     
 */
public function getpartiallypendingcandidateList(Request $request,$ac){ //dd($request->all());
  //ACCEO candidateListBydataentryStart TRY CATCH STARTS HERE
  try{
   if(Auth::check()){
         $user = Auth::user();
         $uid=$user->id;
         $d=$this->commonModel->getunewserbyuserid($user->id);
         $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        
         $st_code=$d->st_code;
         $xss = new xssClean;
         $cons_no= base64_decode($xss->clean_input($ac));
         $cons_no=!empty($cons_no) ? $cons_no : '0';
         //echo $st_code.'PC'.$cons_no;
           DB::enableQueryLog();
		   $candidate_id=array();
           if($cons_no !='0'){
			    $finalbyDEO = DB::table('expenditure_reports')
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
                             foreach ($finalbyDEO as $finalbyDEOData) {
                        $candidate_id[] = $finalbyDEOData->candidate_id;
                    }
                  
                     $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
							  ->select('candidate_nomination_detail.*','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no  as constituency_no','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.ac_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();
              
        
             }else{
              $finalbyDEO = DB::table('expenditure_reports')
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
                             foreach ($finalbyDEO as $finalbyDEOData) {
                        $candidate_id[] = $finalbyDEOData->candidate_id;
                    }
                  
                     $partiallyCandList = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
							  ->select('candidate_nomination_detail.*','candidate_nomination_detail.st_code as ST_CODE','candidate_nomination_detail.ac_no  as constituency_no','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            //->where('candidate_nomination_detail.pc_no','=',$cons_no) 
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                            ->groupBy('candidate_nomination_detail.candidate_id')
                        ->get();
           }
        // dd(DB::getQueryLog());
        $partiallyCandListcount = (!empty($partiallyCandList) ? (count($partiallyCandList)) : 0);
         return view('admin.ac.ceo.Expenditure.partiallypending-report',['user_data' => $d,'partiallyCandList' => $partiallyCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>$partiallyCandListcount]); 
        
     }
     else {
         return redirect('/officer-login');
     }
     } catch (Exception $ex) {
       return Redirect('/internalerror')->with('error', 'Internal Server Error');
      
      }//ACCEO getpartiallypendingcandidateList TRY CATCH ENDS HERE   
   }   // end getpartiallypendingcandidateList start function

   
    /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 20-05-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getdefaultercandidateList By ACCEO fuction     
 */
public function getdefaultercandidateList(Request $request,$ac){ //dd($request->all());
  //ACCEO candidateListBydataentryStart TRY CATCH STARTS HERE
  try{
   if(Auth::check()){
         $user = Auth::user();
         $uid=$user->id;
         $d=$this->commonModel->getunewserbyuserid($user->id);
         $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        
         $st_code=$d->st_code;
         $xss = new xssClean;
         $cons_no= base64_decode($xss->clean_input($ac));
         $cons_no=!empty($cons_no) ? $cons_no : '0';
       // echo $st_code.'PC'.$cons_no;
           DB::enableQueryLog();
           if($cons_no !='0'){
            $defaulterCandList = DB::table('expenditure_understated')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')  
            ->select('expenditure_understated.candidate_id','expenditure_understated.ST_CODE','expenditure_understated.constituency_no','candidate_personal_detail.cand_name','m_party.PARTYNAME','candidate_nomination_detail.created_at',
             DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
             DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
             ->having('totalobseramnt','<=','totalcandamnt')
            ->where('expenditure_understated.ST_CODE','=',$st_code)
            ->where('expenditure_understated.constituency_no','=',$cons_no) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->groupBy('expenditure_understated.candidate_id')
            ->get();
           }else{
            $defaulterCandList = DB::table('expenditure_understated')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
            ->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_understated.candidate_id') 
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')  
            ->select('expenditure_understated.candidate_id','expenditure_understated.ST_CODE','expenditure_understated.constituency_no','candidate_personal_detail.cand_name','m_party.PARTYNAME','candidate_nomination_detail.created_at',
             DB::raw('IFNULL(SUM(expenditure_understated.amt_as_per_observation),0) AS totalobseramnt'),
             DB::raw('IFNULL(SUM(expenditure_understated.amt_understated_by_candidate),0) AS totalcandamnt'))
             ->having('totalobseramnt','<=','totalcandamnt')
            ->where('expenditure_understated.ST_CODE','=',$st_code)
            //->where('expenditure_understated.constituency_no','=',$cons_no) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
             ->groupBy('expenditure_understated.candidate_id')
            ->get();
           }
        // dd(DB::getQueryLog());
          //dd($DataentryStartCandList);
         return view('admin.ac.ceo.Expenditure.defaulter-report',['user_data' => $d,'defaulterCandList' => $defaulterCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,'count'=>count($defaulterCandList)]); 
        
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
* @author Devloped Date : 20-05-19
* @author Modified By : 
* @author Modified Date : 
* @author param return candidateListByfiledData By ACCEO fuction     
*/
public function candidateListByfiledData(Request $request,$ac){
//PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
try{
 if(Auth::check()){
       $user = Auth::user();
        $uid=$user->id;
       $d=$this->commonModel->getunewserbyuserid($user->id);
       $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
      
      
       $st_code=$d->st_code;
       $xss = new xssClean;
       $cons_no= base64_decode($xss->clean_input($ac));
       $cons_no=!empty($cons_no) ? $cons_no : '0';
   if($cons_no !='0'){
       $finalCandList = DB::table('expenditure_reports')
       ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
       ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
       ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
       ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
       ->where('expenditure_reports.ST_CODE','=',$st_code)
       ->where('expenditure_reports.constituency_no','=',$cons_no) 
       ->where('expenditure_reports.finalized_status','=','1') 
       ->where('candidate_nomination_detail.application_status','=','6')
       ->where('candidate_nomination_detail.finalaccepted','=','1')
       ->where('candidate_nomination_detail.symbol_id','<>','200')
       ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
   ->groupBy('expenditure_reports.candidate_id')
       ->get();
   }else{
     $finalCandList = DB::table('expenditure_reports')
       ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
       ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
       ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
       ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
       ->where('expenditure_reports.ST_CODE','=',$st_code)
       ->where('expenditure_reports.finalized_status','=','1') 
       ->where('candidate_nomination_detail.application_status','=','6')
       ->where('candidate_nomination_detail.finalaccepted','=','1')
       ->where('candidate_nomination_detail.symbol_id','<>','200')
       ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
       ->groupBy('expenditure_reports.candidate_id')
       ->get();
       }
        //dd($DataentryStartCandList);
        $finalCandListcount = (!empty($finalCandList) ? (count($finalCandList)) : 0);
       return view('admin.ac.ceo.Expenditure.filed-report',['user_data' => $d,'finalCandList' => $finalCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,"count"=>$finalCandListcount]); 
      
   }
   else {
       return redirect('/officer-login');
   }
   } catch (Exception $ex) {
     return Redirect('/internalerror')->with('error', 'Internal Server Error');
    
    }//AC CEO candidateListByfiledData TRY CATCH ENDS HERE   
 }   // end candidateListByfiledData start function
 
 /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 23-05-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return candidateListfinalbyCEO By CEO fuction     
  */
  public function candidateListfinalbyCEO(Request $request,$ac){ 
    //AC CEO candidateListfinalbyCEO TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
           $st_code=$d->st_code;
           $xss = new xssClean;
           $cons_no= base64_decode($xss->clean_input($ac));
           $cons_no=!empty($cons_no) ? $cons_no : '0';
       if($cons_no !='0'){
           $finalbyceoCandList = DB::table('expenditure_reports')
           ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('expenditure_reports.constituency_no','=',$cons_no) 
           ->where('expenditure_reports.final_by_ceo','=','1') 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->groupBy('expenditure_reports.candidate_id')
           ->get();
       }else{
         $finalbyceoCandList = DB::table('expenditure_reports')
           ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->select('expenditure_reports.*','m_party.CCODE','m_party.PARTYNAME','candidate_personal_detail.cand_name')
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('expenditure_reports.final_by_ceo','=','1') 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->groupBy('expenditure_reports.candidate_id')
           ->get();
       }
       $finalbyceoCandListcount = (!empty($finalbyceoCandList) ? (count($finalbyceoCandList)) : 0);
           return view('admin.ac.ceo.Expenditure.finalbyceo-report',['user_data' => $d,'finalbyceoCandList' => $finalbyceoCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,"count"=>$finalbyceoCandListcount]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC CEO candidateListByfinalizeData TRY CATCH ENDS HERE   
     }   // end candidateListByfinalizeData start function
  
     /**
    * @author Devloped By : Niraj Kumar
    * @author Devloped Date : 23-05-19
    * @author Modified By : 
    * @author Modified Date : 
    * @author param return candidateListByfinalizeData By CEO fuction     
    */
   public function candidateListfinalbyECI(Request $request,$ac){
    //PC ROPC candidateListByfinalizeData TRY CATCH STARTS HERE
    try{
     if(Auth::check()){
           $user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
          
           $st_code=$d->st_code;
           $xss = new xssClean;
           $cons_no= base64_decode($xss->clean_input($ac));
           $cons_no=!empty($cons_no) ? $cons_no : '0';
       if($cons_no !='0'){
           $finalbyeciCandList = DB::table('expenditure_reports')
           ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           ->where('expenditure_reports.constituency_no','=',$cons_no) 
           ->where('expenditure_reports.final_by_eci','=','1') 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->groupBy('expenditure_reports.candidate_id')
           ->get();
           }else{
           $finalbyeciCandList = DB::table('expenditure_reports')
           ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
           ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
           ->where('expenditure_reports.ST_CODE','=',$st_code)
           //->where('expenditure_reports.constituency_no','=',$cons_no) 
           ->where('expenditure_reports.final_by_eci','=','1') 
           ->where('candidate_nomination_detail.application_status','=','6')
           ->where('candidate_nomination_detail.finalaccepted','=','1')
           ->where('candidate_nomination_detail.symbol_id','<>','200')
           ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
           ->groupBy('expenditure_reports.candidate_id')
           ->get();
       }
       $finalbyeciCandListcount = (!empty($finalbyeciCandList) ? (count($finalbyeciCandList)) : 0);
           return view('admin.ac.ceo.Expenditure.finalbyeci-report',['user_data' => $d,'finalbyeciCandList' => $finalbyeciCandList,'edetails'=>$ele_details,'cons_no'=>$cons_no,"count"=>$finalbyeciCandListcount]); 
          
       }
       else {
           return redirect('/officer-login');
       }
       } catch (Exception $ex) {
         return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//AC CEO candidateListfinalbyECI TRY CATCH ENDS HERE   
     }   // end candidateListfinalbyECI start function
########################end status dashboard by Niraj 16-05-2019##############
###############################Notice CEO  09-07-2019 Start By Niraj######################################
  /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 09-07-2019
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getnoticeatCEO By ACCEO fuction     
 */
public function getnoticeatCEO(Request $request,$ac){
    //ACCEO  getnoticeatCEO TRY CATCH STARTS HERE
    try{
    if(Auth::check()){
        $user = Auth::user();
        $uid=$user->id;
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		
		$st_code=$d->st_code;
        $xss = new xssClean;
        $cons_no=base64_decode($xss->clean_input($ac));
        $st_code=!empty($st_code) ? $st_code : 0;
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        // echo $st_code.'cons_no'.$cons_no; die;
    
     if($st_code !='0' && $cons_no=='0'){
    $noticeatCEO = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
        ->where('expenditure_reports.ST_CODE','=',$st_code)
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo','0')
        ->where('expenditure_reports.final_by_ro','0')
        ->whereNotNull('expenditure_reports.date_of_issuance_notice')
        ->where(function($q) {
            $q->where('expenditure_reports.final_action','=','Notice Issued')
              ->orWhere('expenditure_reports.final_action','=','Reply Issued')
              ->orWhere('expenditure_reports.final_action','=','Hearing Done');
            })
         ->groupBy('expenditure_reports.candidate_id')
        ->get(); 
    }elseif($st_code !='0' && $cons_no !='0'){
    $noticeatCEO = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
        ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
        ->where('expenditure_reports.ST_CODE','=',$st_code)
        ->where('expenditure_reports.constituency_no','=',$cons_no) 
        ->where('candidate_nomination_detail.application_status','=','6')
        ->where('candidate_nomination_detail.finalaccepted','=','1')
        ->where('candidate_nomination_detail.symbol_id','<>','200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo','0')
         ->where('expenditure_reports.final_by_ro','0')
        ->whereNotNull('expenditure_reports.date_of_issuance_notice')
        ->where(function($q) {
            $q->where('expenditure_reports.final_action','=','Notice Issued')
              ->orWhere('expenditure_reports.final_action','=','Reply Issued')
              ->orWhere('expenditure_reports.final_action','=','Hearing Done');
            })
        ->groupBy('expenditure_reports.candidate_id')
        ->get(); 
    }
        //dd($DataentryStartCandList);
        return view('admin.ac.ceo.Expenditure.noticeatceo',['user_data' => $d,'noticeatCEO' => $noticeatCEO,'edetails'=>$ele_details,'st_code'=>$st_code,'cons_no'=>$cons_no,'count'=>count($noticeatCEO)]); 
        
    }
    else {
        return redirect('/officer-login');
    }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        
        }//ACCEO candidateListByfinalizeData TRY CATCH ENDS HERE   
    }   // end candidateListByfinalizeData start function
    
         /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 09-07-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getnoticeatCEOEXL By ECI fuction     
     */
    //ACCEO getnoticeatCEOEXL EXCEL REPORT STARTS
    public function getnoticeatCEOEXL(Request $request,$ac){  
    //ACCEO getnoticeatCEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
    try{
        if(Auth::check()){
        $user = Auth::user();
        $uid=$user->id;
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $st_code=$d->st_code;
        $xss = new xssClean;
        $cons_no=base64_decode($xss->clean_input($ac));
        $st_code=!empty($st_code) ? $st_code : 0;
        $cons_no=!empty($cons_no) ? $cons_no : 0;
        // echo  $st_code.'pc'.$cons_no; die;
       $cur_time    = Carbon::now();
    
     \Excel::create('NoticeatCEOCandidate_'.'_'.$cur_time, function($excel) use($st_code,$cons_no) { 
     $excel->sheet('Sheet1', function($sheet) use($st_code,$cons_no) {
    
        if($st_code !='0' && $cons_no=='0'){
        $noticeatCEO = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
            ->where('expenditure_reports.ST_CODE','=',$st_code)
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('expenditure_reports.final_by_ceo','0')
             ->where('expenditure_reports.final_by_ro','0')
            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
            ->where(function($q) {
                $q->where('expenditure_reports.final_action','=','Notice Issued')
                  ->orWhere('expenditure_reports.final_action','=','Reply Issued')
                  ->orWhere('expenditure_reports.final_action','=','Hearing Done');
                })
             ->groupBy('expenditure_reports.candidate_id')
            ->get(); 
        }elseif($st_code !='0' && $cons_no !='0'){
        $noticeatCEO = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
            ->select('candidate_nomination_detail.*','candidate_personal_detail.*','expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date','m_party.CCODE','m_party.PARTYNAME') 
            ->where('expenditure_reports.ST_CODE','=',$st_code)
            ->where('expenditure_reports.constituency_no','=',$cons_no) 
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('expenditure_reports.final_by_ceo','0')
             ->where('expenditure_reports.final_by_ro','0')
            ->whereNotNull('expenditure_reports.date_of_issuance_notice')
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
            foreach ($noticeatCEO as $candDetails) {
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
                    $TotalUsers =count($noticeatCEO);
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
 ###############################Notice CEO 09-07-2019 End By Niraj######################################
public function getprofile(Request $request) {
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
                            ->on("m_election_details.CONST_NO", "=", "candidate_nomination_detail.ac_no");
                        })
                        ->where('candidate_nomination_detail.st_code', $stcode)
                         
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
         public function getTrackingByCEOUserId(Request $request) {
    
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
            
          $filterrequest = $request->all(); 
          $year = !empty($filterrequest['year']) ? $filterrequest['year'] : '';       
    $condtition="";
            if(!empty($year))
            {
                $condtition .= " AND YEAR(date_of_declaration)='$year'";
            }
            $data = DB::select(" SELECT
                                C.candidate_id,
                                C.cand_name,
                                C.cand_email,
                                A.AC_NAME ,
                                R.date_of_declaration
                              FROM
                                `expenditure_reports` R 
                              INNER JOIN
                                candidate_personal_detail C ON C.candidate_id = R.candidate_id
                                 INNER JOIN m_ac A ON
                                     A.AC_NO = R.constituency_no AND A.ST_CODE =R.ST_CODE
                              WHERE
                                R.ST_CODE = '$d->st_code' $condtition");  
            $total_rec =count($data);
            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status','1')->get()->toArray();
         
            return view('admin.ac.ceo.Expenditure.tracking', ['user_data' => $d,'ele_details' => $ele_details,"total_rec"=>$total_rec,"electionType"=>$electionType,"expenditureData" => $data]);
        } else {
            return redirect('/officer-login');
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
       $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidateId,$ac_no);
             
 
        $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $expenditure_fund_parties = DB::table('expenditure_fund_parties')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $getExpItem = DB::table('expenditure_items')->get();

  $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)->where('constituency_no', $ac_no)->first();
            
                    $expenseunderstated= DB::table('expenditure_understates')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
 // $download_link1 = !empty($expenseunderstated[3]->comment) ?  $expenseunderstated[3]->comment : '';
 //            $download_link1= !empty($download_link1) && strpos($download_link1,'ExpenditureReportAC') !==false? url($download_link1):
 //            !empty($download_link1) ? url('/uploads/ExpenditureReportAC').'/'.$download_link1:'';

 //             $download_link2 = !empty($expenseunderstated[5]->comment) ? $expenseunderstated[5]->comment : '';
 //             $download_link2= !empty($download_link2) && strpos($download_link2,'ExpenditureReportAC') !==false? url($download_link2):!empty($download_link2) ? url('/uploads/ExpenditureReportAC').'/'.$download_link2:'';

 //            $download_link3=!empty($scrutiny_data->noticefile)? $scrutiny_data->noticefile:'';
 //             $download_link3= !empty($download_link3) && strpos($download_link3,'ExpenditureReportAC') !==false? url($download_link3):!empty($download_link3) ? url('/uploads/ExpenditureReportAC').'/'.$download_link3:'';
 //             $download_link4 = !empty($expenseunderstated[8]->extra_data) ?  $expenseunderstated[8]->extra_data : '';
 //             $download_link4= !empty($download_link4) && strpos($download_link4,'ExpenditureReportAC') !==false? url($download_link4): !empty($download_link4) ? url('/uploads/ExpenditureReportAC').'/'.$download_link4:'';
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
    // manoj end










//////////////manish start////////////////



/////////////////manish////////////
    public function GetTrackingReportData(Request $request)
    {

       if (Auth::check()) {
        $request = (array) $request->all();
        $user = Auth::user();
        $uid = $user->id;
        $namePrefix = \Route::current()->action['prefix'];
        $d=$this->expenditureModel->getunewserbyuserid($user->id,$user->role_id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
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

            if(!empty($_GET['pcname']))
            {
              $pcname = $_GET['pcname'];
              $condtition .= " AND er.constituency_no='$pcname'";
            }



            $ReportData = $this->expenditureModel->GetExpeditureData($user->role_id,$user->pc_no,$user->st_code,$condtition);
            $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status','1')->get()->toArray();
            $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray(); 
             $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray(); 


           return view('admin.expenditure.tracking_pceo', ['user_data' => $d,'ele_details' =>$ele_details,"cand_finalize_ro" =>array(),"electionType"=>$electionType,"expenditureData" => $ReportData,"total_rec"=>count($ReportData),"nature_of_default_ac"=>$nature_of_default_ac,"current_status"=>$current_status]);

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


     public function getscrutinyreport(Request $request)
    {
          $htmlData = '';
          ////get scrutiny report data ///////
          $candidate_id = $_GET['candidate_id'];
          $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id); 
          $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id); 
          $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
          $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);

          if(!empty($scrutinyReportData))
          {
           return view('admin.pc.ceo.Expenditure.GetScrutinyReport', compact('expensesourecefundbyitem','scrutinyReportData','expenseunderstated','expenseunderstatedbyitem'));
          }
          else
          {
            
          }
    }


public function getElectedCandidate($candidate_id){
         $acdetail = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.candidate_id', $candidate_id)
                   // ->where('candidate_nomination_detail.constituency_no', $constituency_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.party_id', '<>', '1180')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->first();        
            $acNo = !empty($acdetail->ac_no) ? $acdetail->ac_no : 0;
            $st_code = !empty($acdetail->st_code) ? $acdetail->st_code : 0;          
           $ELECTION_ID = !empty($acdetail->election_id) ? $acdetail->election_id : 0;
            $countElectedCandidate=DB::table('winning_leading_candidate')->where('st_code', $st_code)
                              ->where('ac_no', $acNo)
                              ->where('election_id', $ELECTION_ID)
                              ->where('candidate_id', $candidate_id)
                              ->count();
        return $countElectedCandidate;
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


public function StoreMisExpenseReport(Request $request) {


  //dd($request constituency_id);
        $request = (array) $request->all();
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $uid = $user->id;
        $role_id = $user->role_id;

        $candidate_id = $request['candidate_id'];
        $constituency_id = $request['constituency_id'];

        $request['user_id'] = $uid;
        $namePrefix = \Route::current()->action['prefix'];
        unset($request['_token']);
        $send_notice_deo = !empty($request['send_notice_deo']) ? $request['send_notice_deo'] : '';
        $comment_by_ceo = !empty($request['comment_by_ceo']) ? $request['comment_by_ceo'] : '';
        $date_sending_notice_service_to_deo = !empty($request['date_sending_notice_service_to_deo'])?$request['date_sending_notice_service_to_deo']:"";
        unset($request['send_notice_deo']);
   // check elected candidate start
        $isElectedCandidate=$this->getElectedCandidate($candidate_id);
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

            $unsetItems = ['candidate_id', 'constituency_no', 'constituency_nos', 'contensting_candiate',
                'date_of_declaration', 'user_id','constituency_id'];
            $dataUpdate = array_diff_key($data_arr, array_flip($unsetItems));

            if ($send_notice_deo == "deo") {
                $dataUpdate['final_by_ro'] = '0';
            }


            $updateStatus = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->where('constituency_no', $constituency_id)->update($dataUpdate);
            ////////////////////////////////// add entry in expenditure action logs/////////////////
               $cdate = date('Y-m-d h:i:s');
               $data_action=array("candidate_id"=>$candidate_id,"ceo_action_date"=>$cdate,"ceo_comment"=>$comment_by_ceo,"ceo_action_sending_date"=>$date_sending_notice_service_to_deo);
               $data_arr_action = array();
                foreach ($data_action as $key => $req_data_action) {
                    $xss = new xssClean;
                    $data_arr_action[$key] = $xss->clean_input($req_data_action);
                }
              // print_r($data_action);die;
               $data_actionInserted = $this->commonModel->updatedata('expenditure_action_logs', 'candidate_id', $candidate_id, $data_arr_action);

              ///////////////////////////////////////// end entry in expenditure logs///////////////////

            // dd($updateStatus);
            if ($updateStatus > 0) {
                Session::put('message', "Saved successfully");
                return redirect($namePrefix . '/editExpenditureReport?candidate_id=' . base64_encode($candidate_id).'&'.'ac_no='.base64_encode($constituency_id));
            } else {
                Session::put('message', "No change");
               // return redirect($namePrefix . '/editExpenditureReport?candidate_id=' . base64_encode($candidate_id));
                  return redirect($namePrefix . '/editExpenditureReport?candidate_id=' . base64_encode($candidate_id).'&'.'ac_no='.base64_encode($constituency_id));
            }
        } catch (\Exception $e) {

            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
    }
        



 public function updateData(Request $request)
    {
        $request = (array)$request->all();
       // print_r($request);die;
        if(!empty($request)){
        $updateTrackData = $this->commonModel->updatedata('expenditure_reports','id',$request['tbid'],array($request['column']=>$request['value']));
        if($updateTrackData)
        {
            return 1;
        }
        else
        {
            return 0;
        }
        }
    }


public function generatePDF($candidate_id)
    {

          $scrutinyReportData = $this->expenditureModel->GetScrutinyReportData($candidate_id); 
          $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidate_id); 
          $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidate_id);
          $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidate_id);
          
          $pdf = MPDF::loadView('admin.pc.ro.Expenditure.ReportPdf', compact('scrutinyReportData', 'expenseunderstated', 'expenseunderstatedbyitem', 'expensesourecefundbyitem'));
           return $pdf->stream('Ro.scrunity-report.pdf');
    }

public function saveComment(Request $request)
{
  $request = (array)$request->all();
 $comment_by_ceo = !empty($request['comment'])?$request['comment']:"";  
  if(!empty($request))
  {
      $insertComment = $this->commonModel->updatedata('expenditure_reports','candidate_id',$request['candidate_id'],array("comment_by_ceo"=>$comment_by_ceo));
      if($insertComment)
      {
        return 1;
      }
      else
      {
        return 0;
      }
  }
}


public function confirmReport()
{
   $candidate_id = !empty($_GET['candidate_id'])?$_GET['candidate_id']:"";
    $ac_no = !empty($_GET['acno'])?$_GET['acno']:"";
   if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);
             $AcData =DB::table('candidate_nomination_detail')
                ->select('*')
                ->join('m_ac',function($join){
                    $join->on('m_ac.ST_CODE','=','candidate_nomination_detail.st_code')
                            ->on('m_ac.AC_NO','=','candidate_nomination_detail.ac_no');
                }) ->where('candidate_nomination_detail.candidate_id',$candidate_id)->first();
          
           $acNo=!empty($AcData->AC_NO)? $AcData->AC_NO:0;
              
          $ELECTION_ID=!empty($AcData->election_id)? $AcData->election_id:0;
       
        $st_code=!empty($ele_details[0]->ST_CODE)?$ele_details[0]->ST_CODE:0;
        $CONST_NO=!empty($ele_details[0]->CONST_NO)?$ele_details[0]->CONST_NO:0;
            //dd($ac_no);
            
            $insertdata=['candidate_id'=>$candidate_id,'st_code'=>$st_code,'constituency_no'=>$acNo,'ceo_action'=>'1'];
                            
        }


               
            


            $insertComment = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->where('constituency_no', $ac_no)->update(['final_by_ceo' => '1']);

           //  $update = DB::table('expenditure_reports')->where('candidate_id', $candidate_id)->where('constituency_no', $ac_no)->update(['ceo_action' => '1']);

   //$insertComment = $this->commonModel->updatedata('expenditure_reports','candidate_id',$candidate_id,array("final_by_ceo"=>'1'));
    $update = $this->commonModel->updatedata('expenditure_notification', 'candidate_id', $candidate_id, array("ceo_action" => '1'));
        
      if($insertComment)
      {
        //$this->commonModel->insertData('expenditure_notification',$insertdata);

        return 1;
      }
      else
      {
        return 0;
      }
}





public function GetProfileCEO(Request $request) {
    try {
       if (Auth::check()) {
           $user = Auth::user();
           $d = $this->commonModel->getunewserbyuserid($user->id);


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
                  // get CEO status
                   
                   $electionType = DB::table('expenditure_election_type')->select('id', 'title', 'status')->where('status', '1')->get()->toArray();
                   $nature_of_default_ac = DB::table('expenditure_nature_of_default_ac')->get()->toArray();
                   $current_status = DB::table('expenditure_mis_current_sataus')->get()->toArray();
                   $ReportSingleData = $this->expenditureModel->GetExpeditureSingleData($candidate_id);
                   if (!empty($ReportSingleData)) {

                       $ReportSingleData = (array) $ReportSingleData[0];
                   }

           return view('admin.expenditure.GetProfileRO', compact('profileData',
                   'ReportSingleData','electionType','nature_of_default_ac','current_status'));   
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
            $d = $this->expenditureModel->getunewserbyuserid($user->id, $user->role_id);
            $mpdf = new \Mpdf\Mpdf();

            $candiatePcName = getpcbypcno($d->st_code, $d->pc_no);
            $candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';

            $candidate_id = base64_decode($candidateId);
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
public function printScrutinyReport($candidateId,$ac_no) {
         if (Auth::check()) {
            $user = Auth::user();
             $mpdf = new \Mpdf\Mpdf();
             $candidateId = base64_decode($candidateId);
              $ac_no = base64_decode($ac_no);
             $d=$this->commonModel->getunewserbyuserid($user->id);             
             $canddetail = DB::table('candidate_nomination_detail')->where('candidate_nomination_detail.candidate_id', $candidateId)
             ->where('candidate_nomination_detail.ac_no', $ac_no)
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
 
        $gexExpReport = DB::table('expenditure_reports')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $getCandidateExpData = DB::table('expenditure_understates')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $expenditure_fund_parties = DB::table('expenditure_fund_parties')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $expenditure_fund_source = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $getSourceFundData = DB::table('expenditure_fund_source')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $getExpData = DB::table('expenditure_understated')->where('candidate_id', $candidateId)->where('constituency_no', $ac_no)->get()->toArray();
        $getExpItem = DB::table('expenditure_items')->get();
        // $expenseunderstated = $this->expenditureModel->GetScrutinyUnderExpData($candidateId);
            $expenseunderstatedbyitem = $this->expenditureModel->GetScrutinyUnderExpByitemData($candidateId,$ac_no);
            $expensesourecefundbyitem = $this->expenditureModel->GetScrutinysourecefundByitemData($candidateId,$ac_no);

           $scrutiny_data=DB::table('expenditure_reports')->select('expenditure_reports.noticefile')
                    ->where('expenditure_reports.candidate_id', '=', $candidateId)->first();
                 
             

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

////

                    
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
                    ->where('expenditure_reports.constituency_no', '=', $ac_no)
                    ->first();
                     
                     $candidateName=!empty($scrutinyReportData[0]->cand_name)? $scrutinyReportData[0]->cand_name:'';
                     $electionType=!empty($scrutinyReportData[0]->election_type)?'General '.$scrutinyReportData[0]->election_type:'';
                    $submitedData=!empty( $submitedData->updated_at)? $submitedData->updated_at:0;
                     
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
            
            $pdf = view('admin.expenditure.pdf_ro', compact('expensesourecefundbyitem','winn_data', 'scrutinyReportData','submitedData', 'expenseunderstated', 'expenseunderstatedbyitem','download_link1','download_link2','download_link3','download_link4','districtDetails','acdetail','electionType','partyname'));
            $mpdf->WriteHTML($pdf);
            $mpdf->Output();
 
        } else {
            return redirect('/officer-login');
        }
       }
       public function getReturn(Request $request,$ac) {
        
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;              
                $d=$this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        
                $xss = new xssClean;
                 $st_code=$d->st_code;
                $cons_no=base64_decode($xss->clean_input($ac));
                $st_code=!empty($st_code) ? $st_code : 0;
                $cons_no=!empty($cons_no) ? $cons_no : 0;              
              
                if (!empty($st_code) && $cons_no == '') {
                     $returnCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
			->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
              
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->where('expenditure_reports.return_status', '=', 'Returned')
                        ->where('expenditure_reports.finalized_status', '=', '1')
                        ->where('expenditure_reports.final_by_ro', '=', '1') 
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                } else if (!empty($st_code) && $cons_no != '') {
                    $returnCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
			->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)                     
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->where('expenditure_reports.return_status', '=', 'Returned')
                        ->where('expenditure_reports.finalized_status', '=', '1')
                        ->where('expenditure_reports.final_by_ro', '=', '1') 
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                } else {             
                    
                        $returnCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
			->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')              
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->where('expenditure_reports.return_status', '=', 'Returned') 
                        ->where('expenditure_reports.finalized_status', '=', '1')
                        ->where('expenditure_reports.final_by_ro', '=', '1')
                        ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                    
                }                
              
                    $count=!empty($returnCandList)?count($returnCandList):0;
              
                
                return view('admin.ac.ceo.Expenditure.return-report', ['user_data' => $d, 'returnCandList' => $returnCandList ,
                    'edetails' => $ele_details, "count" => $count,
                    'st_code'=>$st_code,
                    'cons_no'=>$cons_no
                        ]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ROPC candidateListByfiledData TRY CATCH ENDS HERE   
    }
     public function getNonReturn(Request $request, $ac) {
        
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d=$this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        
                
                $xss = new xssClean;
                $st_code=$d->st_code;
                $cons_no=base64_decode($xss->clean_input($ac));
                $st_code=!empty($st_code) ? $st_code : 0;
                $cons_no=!empty($cons_no) ? $cons_no : 0;              
                
                 if (!empty($st_code) && $cons_no == '') {
                     $nonreturnCandList = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
			->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
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
			->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')
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
			->select('candidate_personal_detail.cand_name','expenditure_reports.*','m_party.CCODE', 'm_party.PARTYNAME')              
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
              
                    $count=!empty($nonreturnCandList)?count($nonreturnCandList):0;
                
                return view('admin.ac.ceo.Expenditure.non-return-report', ['user_data' => $d, 'nonreturnCandList' => $nonreturnCandList ,
                    'edetails' => $ele_details, "count" => $count,
                     'st_code'=>$st_code,
                    'cons_no'=>$cons_no
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
        $data_definalization = array('candidate_id'=>$candidateId,'created_by'=>$uid,'updated_by'=>$uid,'comment'=>$reason,"count_by_ceo"=>'1','log_type'=>'DEFINALIZATION','officer_level'=>'CEO');

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


    public function getcandidateList(request $request) {
        //dd($request->all());
        DB::enableQueryLog();
        if (Auth::check()) {
            $user = Auth::user();
            $uid = $user->id;
            $d = $this->commonModel->getunewserbyuserid($user->id);
           $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
            $state = $user->st_code;
            $conditions="";
            
            if(!empty($_GET['ac'])){
            $ac = $_GET['ac'];
            $conditions .=" and candidate_nomination_detail.ac_no='$ac' ";
              }  

            if(!empty($conditions)){
                         $candList = DB::select("select `candidate_nomination_detail`.*, `candidate_personal_detail`.*, `m_election_details`.*, `expenditure_reports`.`finalized_status`, `expenditure_reports`.`updated_at` as `finalized_date`, `expenditure_reports`.`final_by_ro`, `expenditure_reports`.`date_of_declaration` from `candidate_nomination_detail` left join `candidate_personal_detail` on `candidate_nomination_detail`.`candidate_id` = `candidate_personal_detail`.`candidate_id` inner join `m_election_details` on `m_election_details`.`st_code` = `candidate_nomination_detail`.`st_code` and `m_election_details`.`CONST_NO` = `candidate_nomination_detail`.`ac_no` left join `expenditure_reports` on `expenditure_reports`.`candidate_id` = `candidate_nomination_detail`.`candidate_id` where `candidate_nomination_detail`.`application_status` = 6 and `candidate_nomination_detail`.`party_id` <> 1180 and `candidate_nomination_detail`.`finalaccepted` = '1' and `m_election_details`.`CONST_TYPE` = 'AC' and `expenditure_reports`.`finalized_status` = '1' and candidate_nomination_detail.st_code='$state' $conditions");
            }
            else{  
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
                    ->get();
               }

               if(!empty($candList))
               {
                foreach ($candList as $value) {
                        $getLog = DB::table('expenditure_logs')->where('created_by',$uid)->where('candidate_id',$value->candidate_id)->count();   
                        $value->count_by_ceo = $getLog;
                }
               }
            // dd(DB::getQueryLog());
            // dd($candList);
            return view('admin.ac.ceo.Expenditure.FinalizedcandidateList', ['user_data' => $d, 'ele_details' => $ele_details, 'candList' => $candList]);
        } else {
            return redirect('/officer-login');
        }
    }

   #################################Start MIS Report By Niraj 21-08-2019#####################################

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getOfficersmis By CEO fuction     
     */  
    public function getOfficersmis(Request $request) { 
        //dd($request->all());
        //PC ECI getOfficersmis TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $st_code=$d->st_code;
                $cons_no = $request->input('ac');
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
             // echo  $st_code.'cons_no=>'.$cons_no; die;
                 DB::enableQueryLog();
                if (empty($cons_no)) { 
                  
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.ac_no")
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
                            ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.ac_no")
                            ->get();
                }
                //dd(DB::getQueryLog());
                // dd($totalContestedCandidatedata);
                return view('admin.ac.ceo.Expenditure.mis-officer', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'st_code' => $st_code,'cons_no' => $cons_no, 'count' => count($totalContestedCandidatedata)]);
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
public function getOfficersmisEXL(Request $request,$ac) {
//ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    
    $cons_no = base64_decode($xss->clean_input($ac));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
    // echo  $st_code.'pc'.$cons_no; die;
    // dd($totalContestedCandidate);

    $cur_time = Carbon::now();

    \Excel::create('CEOACMISExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
        $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

            if (empty($cons_no)) { 
                $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                        ->where('candidate_nomination_detail.st_code', '=', $st_code)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                        ->groupBy("candidate_nomination_detail.ac_no")
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
                        ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                        ->groupBy("candidate_nomination_detail.ac_no")
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
			$TotalNotinTime=0;


            $user = Auth::user();
            $count = 1;
            foreach ($totalContestedCandidatedata as $key => $listdata) {
                $cons_no=$listdata->ac_no;
                    //get finalby DEO count
                $finalbyDEO= $this->eciexpenditureModel->gettotalfinalbyDEO('AC',$listdata->st_code,$cons_no);
               
                
                //get partially pending data count
                //  $pendingatRO = $this->eciexpenditureModel->gettotalpartiallypending('PC', $listdata->st_code, $cons_no);
                //Get pendingatCEO Count 
                $pendingatCEO = $this->eciexpenditureModel->gettotalfinalbyceo('AC', $listdata->st_code, $cons_no);
                
                
                //Get pendingatECI Count 
                $pendingatECI = $this->eciexpenditureModel->gettotalfinalbyeci('AC', $listdata->st_code, $cons_no);
                
                //Get filedcount Count 
                $filedcount = $this->eciexpenditureModel->gettotaldataentryStart('AC', $listdata->st_code, $cons_no);
                
                // Get Pending Data Count 
                $notfiledcount= $listdata->totalcandidate - $filedcount;
                

                //Get noticeatDEOCount Count 
                $noticeatDEOCount = $this->eciexpenditureModel->gettotalnoticeatDEO('AC', $listdata->st_code, $cons_no);

                //Get noticeatCEOCount Count 
                $noticeatCEOCount = $this->eciexpenditureModel->gettotalnoticeatCEO('AC', $listdata->st_code, $cons_no);

                //Get finalcompletedcount at CEO Count 
                $finalcompletedcount = $this->eciexpenditureModel->gettotalCompletedbyEci('AC', $listdata->st_code, $cons_no);
				
				//Get notinTime at CEO Count 
			   $notinTime= $this->eciexpenditureModel->gettotalNotinTime('AC',$listdata->st_code,$cons_no);
		      

                $st = getstatebystatecode($listdata->st_code);
                $acbystate=getacbystate($listdata->st_code);
                $account=count($acbystate);
                $acdetails=getacbyacno($listdata->st_code,$listdata->ac_no);
                $Totalac += $account;  
                $distdetails=getdistrictbydistrictno($listdata->st_code,$listdata->district_no);
               
			   //pending at DEO
              //  if($pendingatECI != $listdata->totalcandidate){
               // $pendingatRO=$listdata->totalcandidate-($pendingatCEO+$finalcompletedcount);
               // }  
				 if($finalbyDEO >= 0 ){
			     $pendingatRO=$listdata->totalcandidate-($finalbyDEO);
			     }  	
                $filedcount = $filedcount ?? '0';

                //$filedcount = !empty($filedcount) ? $filedcount : '0';
                $finalbyDEO = !empty($finalbyDEO) ? $finalbyDEO : '0';
                $pendingatRO = !empty($pendingatRO) ? $pendingatRO : '0';
                $pendingatCEO = !empty($pendingatCEO) ? $pendingatCEO : '0';
                $pendingatECI = !empty($pendingatECI) ? $pendingatECI : '0';
                $noticeatDEOCount = !empty($noticeatDEOCount) ? $noticeatDEOCount : '0';
                $noticeatCEOCount = !empty($noticeatCEOCount) ? $noticeatCEOCount : '0';
                $finalcompletedcount = !empty($finalcompletedcount) ? $finalcompletedcount : '0';
                $account = !empty($account) ? $account : '0';
                $notfiledcount = !empty($notfiledcount) ? $notfiledcount : '0';
				$notinTime = !empty($notinTime) ? $notinTime : '0';


                $data = array(
				    $st->ST_NAME,
                    $acdetails->AC_NAME,
                    $listdata->totalcandidate,
                    $filedcount,
                    $notfiledcount,
					$notinTime,
                    $finalbyDEO,
                    $pendingatRO,
                    $pendingatCEO,
					$noticeatCEOCount
                );
                $TotalUsers += $listdata->totalcandidate;
                $TotalPendingatRO += $pendingatRO;
                $TotalPendingatCEO += $pendingatCEO;
                $TotalPendingatECI += $pendingatECI;
                $TotalDEONotice += $noticeatDEOCount;
                $TotalCEONotice += $noticeatCEOCount;
                $Totalfinalcompletedcount += $finalcompletedcount;
                $TotalnotfiledData += $notfiledcount;
                $TotalfiledData += $filedcount;
                $TotalNotinTime += $notinTime;
				$TotalFinalByDEO += $finalbyDEO;
                array_push($arr, $data);
                // }
                $count++;
            }
            $totalvalues = array('Total','', $TotalUsers, $TotalfiledData, $TotalnotfiledData,$TotalNotinTime,$TotalFinalByDEO, $TotalPendingatRO, $TotalPendingatCEO,$TotalCEONotice);
            // print_r($totalvalues);die;
            array_push($arr, $totalvalues);
            $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                'State', 'District','AC Name','Total Candidate','Started', 'Not Started','Not In Time','Finalise By DEO', 'Pending At DEO', 'Pending At CEO', 'Notice At CEO'
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
        //ECI mis-officerPDFhtml PDF REPORT TRY CATCH BLOCK ENDS
    }
//ECI ACTIVE USERS PDF REPORT FUNCTION ENDS


 /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return finalCandidateList By CEO fuction     
     */
    public function finalCandidateList(Request $request,$ac) {
        //dd($request->all());
        //PC CEO finalCandidateList TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : '';
                $cons_no = !empty($cons_no) ? $cons_no : '';
                // echo $st_code.'pc'.$cons_no; die;
                DB::enableQueryLog();

                if (empty($cons_no)) {
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            //->count();
                            ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
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
                            ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                            ->get();
                } 
                //dd(DB::getQueryLog());
                // dd($totalContestedCandidate);
                return view('admin.ac.ceo.Expenditure.candidate-report', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata, 'cons_no' => $cons_no, 'st_code' => $st_code,'count' => count($totalContestedCandidatedata)]);
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
    public function finalCandidateListEXL(Request $request, $ac) {
        //ECI ACTIVE USERS EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CeoCandidateMISExcel_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if (empty($cons_no)) {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
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
                                    ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
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
                            $distdetails=getdistrictbydistrictno($candDetails->st_code,$candDetails->district_no);
                            $data = array(
                                $st->ST_NAME,
                                $distdetails->DIST_NAME,
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
                            'State','District','AC No & Name', 'Candidate Name', 'Party Name'
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
        //ECI finalCandidateList EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //ECI ACTIVE USERS EXCEL REPORT FUNCTION ENDS
    //ECI finalCandidateList PDF REPORT STARTS
    public function finalCandidateListPDF(Request $request, $ac) {
        //ECI finalCandidateList PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if (empty($cons_no)) {
                            $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
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
                                    ->select("candidate_nomination_detail.district_no","candidate_nomination_detail.candidate_id", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", "candidate_nomination_detail.created_at", "candidate_personal_detail.cand_name", "m_party.PARTYNAME")
                                    //->groupBy("candidate_nomination_detail.st_code")
                                    ->orderBy("candidate_nomination_detail.ac_no")
                                    ->get();
                        } 
                $pdf = PDF::loadView('admin.ac.ceo.Expenditure.candidatePDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata]);
                return $pdf->download('CeoCandidateMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.ceo.Expenditure.candidatePDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //CEO All Contested candidates PDF REPORT TRY CATCH BLOCK ENDS
    }
    //CEO candidate PDF REPORT FUNCTION ENDS

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return startedcandidate By CEO fuction     
     */
    public function getStartedcandidateMIS(Request $request, $ac) {
        //PC CEO Ecistartedcandidate TRY CATCH STARTS HERE
                try {
                    if (Auth::check()) {
                        $user = Auth::user();
                        $uid = $user->id;
                        $d = $this->commonModel->getunewserbyuserid($user->id);
                        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        
                        $xss = new xssClean;
                        $st_code = $d->st_code;
                        $cons_no = base64_decode($xss->clean_input($ac));
                        $st_code = !empty($st_code) ? $st_code : 0;
                        $cons_no = !empty($cons_no) ? $cons_no : 0;
                        // echo  $st_code.'pc'.$cons_no; die;
                       
                        if (empty($cons_no)) {
                            $startedcandidate = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                        } elseif ($st_code != '0' && $cons_no != '0') {
                            $startedcandidate = DB::table('expenditure_reports')
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
        
                         //dd($startedcandidate);
                        return view('admin.ac.ceo.Expenditure.mis-startedcandidate', ['user_data' => $d, 'startedcandidate' => $startedcandidate, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($startedcandidate)]);
                    } else {
                        return redirect('/officer-login');
                    }
                } catch (Exception $ex) {
                    return Redirect('/internalerror')->with('error', 'Internal Server Error');
                }//PC CEO Ecistartedcandidate TRY CATCH ENDS HERE   
            }
        
        // end getstartedcandidateMIS start function
       
        /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return filedcandidateDataEXL By CEO fuction     
     */
//CEO getStartedcandidateEXL EXCEL REPORT STARTS
    public function getStartedcandidateMISEXL(Request $request, $ac) {
        //ECI filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code =$d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CEOFiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                       if (empty($cons_no)) {
                            $startedcandidate = DB::table('expenditure_reports')
                                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                                    ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->where('candidate_nomination_detail.application_status', '=', '6')
                                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                             } elseif ($st_code != '0' && $cons_no != '0') {
                            $startedcandidate = DB::table('expenditure_reports')
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

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($startedcandidate as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            // dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                            $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';

                            $TotalUsers = count($startedcandidate);
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
                })->export('csv');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //CEO startedcandidate EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //CEO startedcandidate EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return startedcandidatePDF By CEO fuction     
     */
    //ECI filedcandidateDataPDF PDF REPORT STARTS

    public function getStartedcandidateMISPDF(Request $request,$ac) {
        //CEO filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
               $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
               if (empty($cons_no)) {
                    $filedData = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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
                            ->select('expenditure_reports.*', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
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

                $pdf = PDF::loadView('admin.ac.ceo.Expenditure.mis-startedcandidatePdfhtml', ['user_data' => $d, 'filedData' => $filedData,'st_code' => $st_code]);
                return $pdf->download('CeomiscandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.ceo.Expenditure.mis-startedcandidatePdfhtml');
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
     * @author Devloped Date : 01-07-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return Ecinotstarted By ECI fuction     
     */
    public function getNotstartedMIS(Request $request, $ac) {
        //PC ECI notfiledcandidateData TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                $candidate_id = [];
                if(empty($cons_no)) {
                    $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                    foreach ($startCandList as $startCandListData) {
                        $candidate_id[] = $startCandListData->candidate_id;
                    }
                    $notstarted = DB::table('candidate_nomination_detail')
                                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                    ->where('candidate_nomination_detail.st_code', '=', $st_code)
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
                    $notstarted = DB::table('candidate_nomination_detail')
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
                return view('admin.ac.ceo.Expenditure.mis-notstartedcandidate', ['user_data' => $d, 'notstarted' => $notstarted, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($notstarted)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC Ecinotstarted list TRY CATCH ENDS HERE   
    }
     // end CEO notstarted function

  

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getNotstartedMISEXL By CEO fuction     
     */
//CEO getNotstartedMISEXL EXCEL REPORT STARTS
    public function getNotstartedMISEXL(Request $request, $ac) {
        //CEO filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CEOnotfiledCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
                        $candidate_id = [];
                        if(empty($cons_no)) {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                            foreach ($startCandList as $startCandListData) {
                                $candidate_id[] = $startCandListData->candidate_id;
                            }
                            $notstarted = DB::table('candidate_nomination_detail')
                                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
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
                            $notstarted = DB::table('candidate_nomination_detail')
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

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($notstarted as $candDetails) {
                            $st = getstatebystatecode($candDetails->st_code);
                            // dd($candDetails);
                            $acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
                            $date = new DateTime($candDetails->created_at);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012

                            $TotalUsers = count($notstarted);
                            $data = array(
                                $st->ST_NAME,
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
                           'State', 'AC No & Name', 'Candidate Name', 'Party Name'
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
        //CEO notfiledcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
    }

    //CEO notfiledcandidateData EXCEL REPORT FUNCTION ENDS

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getNotstartedMISPDF By ECI fuction     
     */
    //CEO getNotstartedMISPDF PDF REPORT STARTS

    public function getNotstartedMISPDF(Request $request,$ac) {
        //ECI notfiledcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                $candidate_id = [];
                         if(empty($cons_no)) {
                            $startCandList = DB::table('expenditure_reports')->select('candidate_id')
                                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                                    ->groupBy('expenditure_reports.candidate_id')
                                    ->get();
                            foreach ($startCandList as $startCandListData) {
                                $candidate_id[] = $startCandListData->candidate_id;
                            }
                            $notstarted = DB::table('candidate_nomination_detail')
                                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
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
                            $notstarted = DB::table('candidate_nomination_detail')
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
                $pdf = PDF::loadView('admin.ac.ceo.Expenditure.mis-notstartedPdfhtml', ['user_data' => $d, 'notstarted' => $notstarted,'st_code' => $st_code]);
                return $pdf->download('CeomisnotstartedPdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.ceo.Expenditure.mis-notstartedPdfhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        } //CEO notfiledcandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//CEO notstartedDataPDF PDF REPORT FUNCTION ENDS

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getfinalbyDEO By CEO fuction     
     */
    public function getfinalbyDEO(Request $request, $ac) {
        //PC ECI EcifinalbyDEO TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                if (empty($cons_no)) {
                    $finalbyDEO = DB::table('expenditure_reports')
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
                    $finalbyDEO = DB::table('expenditure_reports')
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
                return view('admin.ac.ceo.Expenditure.finalbydeo-mis', ['user_data' => $d, 'finalbyDEO' => $finalbyDEO, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($finalbyDEO)]);
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
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getfinalbyDEOMISEXL By CEO fuction     
     */
//CEO EcifinalbyDEOMISEXL EXCEL REPORT STARTS
    public function getfinalbyDEOMISEXL(Request $request, $ac) {
//CEO getfinalbyDEOMISEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                //echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CEOPendingatDEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

                        if (empty($cons_no)) {
                            $finalbyDEOMISEXL = DB::table('expenditure_reports')
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
                            $finalbyDEOMISEXL = DB::table('expenditure_reports')
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
                        foreach ($finalbyDEOMISEXL as $candDetails) {
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            //dd($candDetails);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
                            $lodgingDate =!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                            $data = array(
                                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
                            );
                            $TotalUsers = count($finalbyDEOMISEXL);
                            array_push($arr, $data);
                            // }
                            $count++;
                        }
                        $totalvalues = array('Total', $TotalUsers);
                        // print_r($totalvalues);die;
                        array_push($arr, $totalvalues);
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'AC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
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
        //CEO getfinalbyDEOMIS EXCEL REPORT TRY CATCH BLOCK ENDS
    }//CEO EcifinalbyDEOMIS EXCEL REPORT FUNCTION ENDS

    //CEO EcifinalbyDEOMISPDF PDF REPORT STARTS
    public function getfinalbyDEOMISPDF(Request $request, $ac) {
//CEO getfinalbyDEOMISPDF PDF REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                $cur_time = Carbon::now();
                if (empty($cons_no)) {
                    $finalbyDEO = DB::table('expenditure_reports')
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
                    $finalbyDEO = DB::table('expenditure_reports')
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
                $pdf = PDF::loadView('admin.ac.ceo.Expenditure.finalbyDEOPDFhtml', ['user_data' => $d, 'finalbyDEO' => $finalbyDEO]);
                return $pdf->download('CEOfinalbyDEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
                return view('admin.ac.ceo.Expenditure.finalbyDEOPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
//CEO getfinalbyDEOMISPDF PDF REPORT TRY CATCH BLOCK ENDS
    }

//CEO getfinalbyDEOMISPDF PDF REPORT FUNCTION ENDS

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-2019
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatRO By CEO fuction     
     */
    public function getcandidateListpendingatRO(Request $request, $ac) {
        //PC CEO candidateListBydataentryStart TRY CATCH STARTS HERE
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
               // echo $st_code.'cons_no'.$cons_no; die;
                DB::enableQueryLog();
                $candidate_id=array();
                $getcandidateListfinalbyECI=[];
                $pendingatceo=[];
    if(empty($cons_no)) {
        $getcandidateListfinalbyECI = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('expenditure_reports.candidate_id')
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->where('candidate_personal_detail.cand_name', '<>', 'NOTA') 
        ->where('expenditure_reports.final_by_eci','1')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where(function($q) {
          $q->where('expenditure_reports.final_action', 'Closed')
            ->orWhere('expenditure_reports.final_action','Disqualified')
            ->orWhere('expenditure_reports.final_action', 'Case Dropped');
          })
        ->whereNotNull('expenditure_reports.date_of_receipt_eci')
       // ->groupBy('expenditure_reports.candidate_id')
        ->get();

        




        $pendingatceo=DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo', '1')
        ->whereNotNull('expenditure_reports.date_of_receipt')
        ->whereNull('expenditure_reports.date_of_receipt_eci')
        ->get();
        foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
            $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
        }
        foreach ($pendingatceo as $pendingatceoListData) {
            $candidate_id[] = $pendingatceoListData->candidate_id;
        }
       
        $partiallyCandList = DB::table('candidate_nomination_detail')
                ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                ->where('candidate_nomination_detail.st_code', '=', $st_code)
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                ->get();
       
         } elseif ($st_code != '0' && $cons_no != '0') { 
            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->select('expenditure_reports.candidate_id')
            ->where('candidate_nomination_detail.application_status', '=', '6')
            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('final_by_eci','1')
            ->where('expenditure_reports.ST_CODE', '=', $st_code)
            ->where('expenditure_reports.constituency_no', '=', $cons_no)
            ->where(function($q) {
              $q->where('expenditure_reports.final_action', 'Closed')
                ->orWhere('expenditure_reports.final_action','Disqualified')
                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
              })
            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
           // ->groupBy('expenditure_reports.candidate_id')
            ->get();
			
         $pendingatceo=DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where('expenditure_reports.constituency_no', '=', $cons_no)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo', '1')
        ->whereNotNull('expenditure_reports.date_of_receipt')
        ->whereNull('expenditure_reports.date_of_receipt_eci')
        ->get();
        foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
            $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
        }
        foreach ($pendingatceo as $pendingatceoListData) {
            $candidate_id[] = $pendingatceoListData->candidate_id;
        }
		
        $partiallyCandList = DB::table('candidate_nomination_detail')
                ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
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
                ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                ->get();
             }
  
                // dd(DB::getQueryLog());
                return view('admin.ac.ceo.Expenditure.pendingatdeo-mis', ['user_data' => $d, 'partiallyCandList' => $partiallyCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($partiallyCandList)]);
            } else {
                return redirect('/officer-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }//PC ceo getcandidateListpendingatRO TRY CATCH ENDS HERE   
    }

// end getcandidateListpendingatRO function

    /**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 21-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatROEXL By ECI fuction     
     */
//CEO getcandidateListpendingatROEXL EXCEL REPORT STARTS
    public function getcandidateListpendingatROEXL(Request $request, $ac) {
//CEO getcandidateListpendingatROEXL EXCEL REPORT TRY CATCH BLOCK STARTS
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                $xss = new xssClean;
                $st_code = $d->st_code;
                $cons_no = base64_decode($xss->clean_input($ac));
                $st_code = !empty($st_code) ? $st_code : 0;
                $cons_no = !empty($cons_no) ? $cons_no : 0;
                //echo  $st_code.'pc'.$cons_no; die;
                $cur_time = Carbon::now();
                \Excel::create('CeoPendingatDEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                    $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
                    $candidate_id=array();

                    if (empty($cons_no)) {
                        $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->select('expenditure_reports.candidate_id')
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
                        ->where('expenditure_reports.final_by_eci','1')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where(function($q) {
                          $q->where('expenditure_reports.final_action', 'Closed')
                            ->orWhere('expenditure_reports.final_action','Disqualified')
                            ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                          })
                        ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                       // ->groupBy('expenditure_reports.candidate_id')
                        ->get();
                        $pendingatceo=DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->where('expenditure_reports.final_by_ceo', '1')
                        ->whereNotNull('expenditure_reports.date_of_receipt')
                        ->whereNull('expenditure_reports.date_of_receipt_eci')
                        ->get();
                        foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                            $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                        }
                        foreach ($pendingatceo as $pendingatceoListData) {
                            $candidate_id[] = $pendingatceoListData->candidate_id;
                        }
                       
                        $partiallyCandList = DB::table('candidate_nomination_detail')
                                ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                                ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                                ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                                ->where('candidate_nomination_detail.st_code', '=', $st_code)
                                ->where('candidate_nomination_detail.application_status', '=', '6')
                                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                                ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
                                ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                ->get();
                       
                         } elseif ($st_code != '0' && $cons_no != '0') {
                            $getcandidateListfinalbyECI = DB::table('expenditure_reports')
                            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                            ->select('expenditure_reports.candidate_id')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
                            ->where('final_by_eci','1')
                            ->where('expenditure_reports.ST_CODE', '=', $st_code)
                            ->where('expenditure_reports.constituency_no', '=', $cons_no)
                            ->where(function($q) {
                              $q->where('expenditure_reports.final_action', 'Closed')
                                ->orWhere('expenditure_reports.final_action','Disqualified')
                                ->orWhere('expenditure_reports.final_action', 'Case Dropped');
                              })
                            ->whereNotNull('expenditure_reports.date_of_receipt_eci')
                           // ->groupBy('expenditure_reports.candidate_id')
                            ->get();
                         $pendingatceo=DB::table('expenditure_reports')
                        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                        ->where('expenditure_reports.ST_CODE', '=', $st_code)
                        ->where('expenditure_reports.constituency_no', '=', $cons_no)
                        ->where('candidate_nomination_detail.application_status', '=', '6')
                        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                        ->where('expenditure_reports.final_by_ceo', '1')
                        ->whereNotNull('expenditure_reports.date_of_receipt')
                        ->whereNull('expenditure_reports.date_of_receipt_eci')
                        ->get();
                        foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
                            $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
                        }
                        foreach ($pendingatceo as $pendingatceoListData) {
                            $candidate_id[] = $pendingatceoListData->candidate_id;
                        }
                        $partiallyCandList = DB::table('candidate_nomination_detail')
                                ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
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
                                ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
                                ->get();
                             }

                        $arr = array();
                        $TotalUsers = 0;
                        $user = Auth::user();
                        $count = 1;
                        foreach ($partiallyCandList as $candDetails) { 
                            $st = getstatebystatecode($candDetails->ST_CODE);
                            $acDetails = getacbyacno($candDetails->ST_CODE, $candDetails->constituency_no);
                            $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
                            //echo $date->format('d.m.Y'); // 31.07.2012
                            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
							$acno=!empty($acDetails->AC_NO) ?  $acDetails->PC_NO : '';
                            $acname=!empty($acDetails->AC_NAME) ?  $acDetails->AC_NAME : '';
                            $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
                            $data = array(
                                $st->ST_NAME,
                                $acno . '-' . $acname,
                                $candDetails->cand_name,
                                $candDetails->PARTYNAME,
                                $lodgingDate
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
                            'State','AC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging Scrutiny Form'
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
public function getcandidateListpendingatROPDF(Request $request,$ac) {
//ECI getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
$xss = new xssClean;
$st_code = $d->st_code;
$cons_no = base64_decode($xss->clean_input($ac));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 0;
$cur_time = Carbon::now();
 $candidate_id=array();
 if (empty($cons_no)) {
    $getcandidateListfinalbyECI = DB::table('expenditure_reports')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
    ->select('expenditure_reports.candidate_id')
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
    ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
    ->where('expenditure_reports.final_by_eci','1')
    ->where('expenditure_reports.ST_CODE', '=', $st_code)
    ->where(function($q) {
      $q->where('expenditure_reports.final_action', 'Closed')
        ->orWhere('expenditure_reports.final_action','Disqualified')
        ->orWhere('expenditure_reports.final_action', 'Case Dropped');
      })
    ->whereNotNull('expenditure_reports.date_of_receipt_eci')
   // ->groupBy('expenditure_reports.candidate_id')
    ->get();
    $pendingatceo=DB::table('expenditure_reports')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
    ->where('expenditure_reports.ST_CODE', '=', $st_code)
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
    ->where('expenditure_reports.final_by_ceo', '1')
    ->whereNotNull('expenditure_reports.date_of_receipt')
    ->whereNull('expenditure_reports.date_of_receipt_eci')
    ->get();
    foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
        $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
    }
    foreach ($pendingatceo as $pendingatceoListData) {
        $candidate_id[] = $pendingatceoListData->candidate_id;
    }
   
    $pendingatDEOCandList = DB::table('candidate_nomination_detail')
            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
            ->where('candidate_nomination_detail.st_code', '=', $st_code)
            ->where('candidate_nomination_detail.application_status', '=', '6')
            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->whereNotIn('candidate_nomination_detail.candidate_id', $candidate_id)
            ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
            ->get();
   
     } elseif ($st_code != '0' && $cons_no != '0') {
        $getcandidateListfinalbyECI = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('expenditure_reports.candidate_id')
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.date_of_receipt_eci', '!=', 'null : 0000-00-00')
        ->where('final_by_eci','1')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where('expenditure_reports.constituency_no', '=', $cons_no)
        ->where(function($q) {
          $q->where('expenditure_reports.final_action', 'Closed')
            ->orWhere('expenditure_reports.final_action','Disqualified')
            ->orWhere('expenditure_reports.final_action', 'Case Dropped');
          })
        ->whereNotNull('expenditure_reports.date_of_receipt_eci')
       // ->groupBy('expenditure_reports.candidate_id')
        ->get();
     $pendingatceo=DB::table('expenditure_reports')
    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
    ->where('expenditure_reports.ST_CODE', '=', $st_code)
    ->where('expenditure_reports.constituency_no', '=', $cons_no)
    ->where('candidate_nomination_detail.application_status', '=', '6')
    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
    ->where('expenditure_reports.final_by_ceo', '1')
    ->whereNotNull('expenditure_reports.date_of_receipt')
    ->whereNull('expenditure_reports.date_of_receipt_eci')
    ->get();
    foreach ($getcandidateListfinalbyECI as $getcandidateListfinalbyECIData) {
        $candidate_id[] = $getcandidateListfinalbyECIData->candidate_id;
    }
    foreach ($pendingatceo as $pendingatceoListData) {
        $candidate_id[] = $pendingatceoListData->candidate_id;
    }
    $pendingatDEOCandList = DB::table('candidate_nomination_detail')
            ->join('expenditure_reports', 'expenditure_reports.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
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
            ->select('expenditure_reports.created_at','expenditure_reports.last_date_prescribed_acct_lodge','expenditure_reports.updated_at as finalized_date','expenditure_reports.date_orginal_acct','expenditure_reports.date_of_sending_deo','expenditure_reports.date_of_receipt','expenditure_reports.final_by_ro','expenditure_reports.candidate_id','expenditure_reports.ST_CODE','expenditure_reports.constituency_no', 'candidate_personal_detail.candidate_id', 'candidate_personal_detail.cand_name', 'candidate_nomination_detail.candidate_id', 'candidate_nomination_detail.application_status', 'candidate_nomination_detail.finalaccepted', 'm_party.CCODE', 'm_party.PARTYNAME')
            ->get();
         }
$pdf = PDF::loadView('admin.ac.ceo.Expenditure.candidatePendingatDEOPDFhtml', ['user_data' => $d, 'pendingatDEOCandList' => $pendingatDEOCandList]);
return $pdf->download('CeopendingatDEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
return view('admin.ac.ceo.Expenditure.candidatePendingatDEOPDFhtml');
} else {
return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//CEO getcandidateListpendingatROPDF PDF REPORT TRY CATCH BLOCK ENDS
}

//CEO getcandidateListpendingatROPDF PDF REPORT FUNCTION ENDS

/**
     * @author Devloped By : Niraj Kumar
     * @author Devloped Date : 23-08-19
     * @author Modified By : 
     * @author Modified Date : 
     * @author param return getcandidateListpendingatCEO By CEO fuction     
     */
    public function getcandidateListpendingatCEO(Request $request, $ac) {
        //PC CEO getcandidateListpendingatCEO TRY CATCH STARTS HERE
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    $cons_no = base64_decode($xss->clean_input($ac));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
    // echo $st_code.'cons_no'.$cons_no; die;

if(empty($cons_no)) {
$pendingatceoCandList = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo', '0')
        ->whereNotNull('expenditure_reports.date_of_receipt')
        ->whereNull('expenditure_reports.date_of_receipt_eci')
        ->groupBy('expenditure_reports.candidate_id')
        ->get();
        } elseif ($st_code != '0' && $cons_no != '0') {
        $pendingatceoCandList = DB::table('expenditure_reports')
        ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
        ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
        ->where('expenditure_reports.ST_CODE', '=', $st_code)
        ->where('expenditure_reports.constituency_no', '=', $cons_no)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('expenditure_reports.final_by_ceo', '0')
        ->whereNotNull('expenditure_reports.date_of_receipt')
        ->whereNull('expenditure_reports.date_of_receipt_eci')
        ->groupBy('expenditure_reports.candidate_id')
        ->get();
        }
    //dd($pendingatceoCandList);
    return view('admin.ac.ceo.Expenditure.pendingatceo-mis', ['user_data' => $d, 'pendingatceoCandList' => $pendingatceoCandList, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($pendingatceoCandList)]);
} else {
    return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC CEO candidateListByfinalizeData TRY CATCH ENDS HERE   
}

// end candidateListByfinalizeData start function
        
/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 28-05-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return getcandidateListpendingatROEXL By ECI fuction     
 */
//CEO getcandidateListpendingatCEOEXL EXCEL REPORT STARTS
public function getcandidateListpendingatCEOEXL(Request $request, $ac) {
//CEO getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK STARTS
                try {
                    if (Auth::check()) {
                        $user = Auth::user();
                        $uid = $user->id;
                        $d = $this->commonModel->getunewserbyuserid($user->id);
                        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
                        $xss = new xssClean;
                        $st_code = $d->st_code;
                        $cons_no = base64_decode($xss->clean_input($ac));
                        $st_code = !empty($st_code) ? $st_code : 0;
                        $cons_no = !empty($cons_no) ? $cons_no : 0;
                        // echo  $st_code.'pc'.$cons_no; die;
                        $cur_time = Carbon::now();
        
                        \Excel::create('CeoPendingatCEOCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
                            $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {
        
        if (empty($cons_no)) {
            $pendingatCEOCandList = DB::table('expenditure_reports')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.final_by_ceo', '1')
                    ->whereNotNull('expenditure_reports.date_of_receipt')
                    ->whereNull('expenditure_reports.date_of_receipt_eci')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();
        } elseif ($st_code != '0' && $cons_no != '0') {
            $pendingatCEOCandList = DB::table('expenditure_reports')
                    ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                    ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                    ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
                    ->where('expenditure_reports.ST_CODE', '=', $st_code)
                    ->where('expenditure_reports.constituency_no', '=', $cons_no)
                    ->where('candidate_nomination_detail.application_status', '=', '6')
                    ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                    ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                    ->where('expenditure_reports.final_by_ceo', '1')
                    ->whereNotNull('expenditure_reports.date_of_receipt')
                    ->whereNull('expenditure_reports.date_of_receipt_eci')
                    ->groupBy('expenditure_reports.candidate_id')
                    ->get();
        }

        $arr = array();
        $TotalUsers = 0;
        $user = Auth::user();
        $count = 1;
        foreach ($pendingatCEOCandList as $candDetails) {
            $st = getstatebystatecode($candDetails->st_code);
            //dd($candDetails);
            $acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
            $date = new DateTime($candDetails->created_at);
            //echo $date->format('d.m.Y'); // 31.07.2012
            $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
            $data = array(
                $acDetails->AC_NO . '-' . $acDetails->AC_NAME,
                $candDetails->cand_name,
                $candDetails->PARTYNAME,
                $lodgingDate
            );
            $TotalUsers = count($pendingatCEOCandList);
            array_push($arr, $data);
            // }
            $count++;
        }
        $totalvalues = array('Total', $TotalUsers);
        // print_r($totalvalues);die;
        array_push($arr, $totalvalues);
        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
            'AC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
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
//CEO getcandidateListpendingatCEOEXL EXCEL REPORT TRY CATCH BLOCK ENDS
}
        
//CEO getcandidateListpendingatROPDF EXCEL REPORT FUNCTION ENDS
//CEO getcandidateListpendingatCEOPDF PDF REPORT STARTS
public function getcandidateListpendingatCEOPDF(Request $request, $ac) {
//CEO getcandidateListpendingatCEOPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
$user = Auth::user();
$uid = $user->id;
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
$xss = new xssClean;
$st_code = $d->st_code;
$cons_no = base64_decode($xss->clean_input($ac));
$st_code = !empty($st_code) ? $st_code : 0;
$cons_no = !empty($cons_no) ? $cons_no : 0;
$cur_time = Carbon::now();
if (empty($cons_no)) {
    $pendingatCEOCandList = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
            ->where('expenditure_reports.ST_CODE', '=', $st_code)
            ->where('candidate_nomination_detail.application_status', '=', '6')
            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('expenditure_reports.final_by_ceo', '1')
            ->whereNotNull('expenditure_reports.date_of_receipt')
            ->whereNull('expenditure_reports.date_of_receipt_eci')
            ->groupBy('expenditure_reports.candidate_id')
            ->get();
} elseif ($st_code != '0' && $cons_no != '0') {
    $pendingatCEOCandList = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->select('candidate_nomination_detail.*', 'candidate_personal_detail.*', 'expenditure_reports.*', 'expenditure_reports.updated_at as finalized_date', 'm_party.CCODE', 'm_party.PARTYNAME')
            ->where('expenditure_reports.ST_CODE', '=', $st_code)
            ->where('expenditure_reports.constituency_no', '=', $cons_no)
            ->where('candidate_nomination_detail.application_status', '=', '6')
            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('expenditure_reports.final_by_ceo', '1')
            ->whereNotNull('expenditure_reports.date_of_receipt')
            ->whereNull('expenditure_reports.date_of_receipt_eci')
            ->groupBy('expenditure_reports.candidate_id')
            ->get();
}
//dd($totalContestedCandidatedata);
$pdf = PDF::loadView('admin.ac.ceo.Expenditure.candidatePendingatCEOPDFhtml', ['user_data' => $d, 'pendingatCEOCandList' => $pendingatCEOCandList]);
return $pdf->download('CeopendingatCEOCandidateMIS_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
return view('admin.ac.ceo.Expenditure.candidatePendingatCEOPDFhtml');
} else {
return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}
//CEO getcandidateListpendingatCEOPDF PDF REPORT TRY CATCH BLOCK ENDS
}//CEO getcandidateListpendingatCEOPDF PDF REPORT FUNCTION ENDS


/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-08-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return notintimecandidateData By CEO fuction     
 */
public function getnotintimecandidateData(Request $request, $ac) {

//PC CEO notintimecandidateData TRY CATCH STARTS HERE

try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    $cons_no = base64_decode($xss->clean_input($ac));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
     //echo $st_code.'cons_no'.$cons_no; die;
    DB::enableQueryLog();
    $notinTime = [];
    if (empty($cons_no)) {
        $notinTime = DB::table('expenditure_reports')
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->where('expenditure_reports.ST_CODE', '=', $st_code)
                ->where('expenditure_reports.account_lodged_time','No') 
                ->where('expenditure_reports.finalized_status', '=', '1')
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('expenditure_reports.candidate_id')
                ->get();
    } elseif ($st_code != '0' && $cons_no != '0') {
        $notinTime = DB::table('expenditure_reports')
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->where('expenditure_reports.ST_CODE', '=', $st_code)
                ->where('expenditure_reports.constituency_no', '=', $cons_no)
                ->where('expenditure_reports.account_lodged_time','No') 
                ->where('expenditure_reports.finalized_status', '=', '1')
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('expenditure_reports.candidate_id')
                ->get();
    }
//dd(DB::getQueryLog());
    //dd($notinTime);
    return view('admin.ac.ceo.Expenditure.mis-notintimecandidate', ['user_data' => $d, 'notinTime' => $notinTime, 'edetails' => $ele_details, 'st_code' => $st_code, 'cons_no' => $cons_no, 'count' => count($notinTime)]);
} else {
    return redirect('/officer-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
}//PC CEO notintimecandidateData TRY CATCH ENDS HERE   
}

// end notintimecandidateData start function

/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-08-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return notintimecandidateDataEXL By CEO fuction     
 */
//CEO notintimeCandidatesmisEXL EXCEL REPORT STARTS
public function getnotintimecandidateDataEXL(Request $request, $ac) {
//CEO filedcandidateDataEXL EXCEL REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    $cons_no = base64_decode($xss->clean_input($ac));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
    // echo  $st_code.'pc'.$cons_no; die;
    $cur_time = Carbon::now();
    \Excel::create('CeoNotinTimeCandidateMIS_' . '_' . $cur_time, function($excel) use($st_code, $cons_no) {
        $excel->sheet('Sheet1', function($sheet) use($st_code, $cons_no) {

if(empty($cons_no)) {
    $notinTime = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->where('expenditure_reports.ST_CODE', '=', $st_code)
            ->where('expenditure_reports.account_lodged_time','No') 
            ->where('expenditure_reports.finalized_status', '=', '1')
            ->where('candidate_nomination_detail.application_status', '=', '6')
            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->groupBy('expenditure_reports.candidate_id')
            ->get();
   }elseif ($st_code != '0' && $cons_no != '0') {
    $notinTime = DB::table('expenditure_reports')
            ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
            ->where('expenditure_reports.ST_CODE', '=', $st_code)
            ->where('expenditure_reports.constituency_no', '=', $cons_no)
            ->where('expenditure_reports.account_lodged_time','No') 
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
    $st = getstatebystatecode($candDetails->st_code);
    // dd($candDetails);
    $acDetails = getacbyacno($candDetails->st_code, $candDetails->ac_no);
    $date = new DateTime($candDetails->last_date_prescribed_acct_lodge);
    //echo $date->format('d.m.Y'); // 31.07.2012
    $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
    $lodgingDate=!empty($lodgingDate) ?  $lodgingDate : '22-06-2019';
    $TotalUsers = count($notinTime);
    $data = array(
        $st->ST_NAME,
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
    'State','AC No & Name', 'Candidate Name', 'Party Name', 'Date Of Lodging'
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
//ECI filedcandidateData EXCEL REPORT TRY CATCH BLOCK ENDS
}

//CEO filedcandidateData EXCEL REPORT FUNCTION ENDS

/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 23-08-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return filedcandidateDataPDF By ECI fuction     
 */
//CEO notintimecandidateDataPDF PDF REPORT STARTS

public function getnotintimecandidateDataPDF(Request $request, $ac) {
//CEO filedcandidateDataPDF PDF REPORT TRY CATCH BLOCK STARTS
try {
if (Auth::check()) {
    $user = Auth::user();
    $uid = $user->id;
    $d = $this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
    $xss = new xssClean;
    $st_code = $d->st_code;
    $cons_no = base64_decode($xss->clean_input($ac));
    $st_code = !empty($st_code) ? $st_code : 0;
    $cons_no = !empty($cons_no) ? $cons_no : 0;
    $cur_time = Carbon::now();

    if (empty($cons_no)) {
        $notinTime = DB::table('expenditure_reports')
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->where('expenditure_reports.ST_CODE', '=', $st_code)
                ->where('expenditure_reports.account_lodged_time','No') 
                ->where('expenditure_reports.finalized_status', '=', '1')
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('expenditure_reports.candidate_id')
                ->get();
    } elseif ($st_code != '0' && $cons_no != '0') {
        $notinTime = DB::table('expenditure_reports')
                ->join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id')
                ->where('expenditure_reports.ST_CODE', '=', $st_code)
                ->where('expenditure_reports.constituency_no', '=', $cons_no)
                ->where('expenditure_reports.account_lodged_time','No') 
                ->where('expenditure_reports.finalized_status', '=', '1')
                ->where('candidate_nomination_detail.application_status', '=', '6')
                ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                ->groupBy('expenditure_reports.candidate_id')
                ->get();
    }
    //dd($totalContestedCandidatedata);

    $pdf = PDF::loadView('admin.ac.ceo.Expenditure.mis-notintimecandidatePdfhtml', ['user_data' => $d, 'notinTime' => $notinTime]);
    return $pdf->download('CeomisnotintimecandidatePdfhtml' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
    return view('admin.ac.ceo.Expenditure.mis-notintimecandidatePdfhtml');
} else {
    return redirect('/admin-login');
}
} catch (Exception $ex) {
return Redirect('/internalerror')->with('error', 'Internal Server Error');
} //CEO notintimecandidateDataPDF PDF REPORT TRY CATCH BLOCK ENDS
}

//CEO notintimecandidateDataPDF PDF REPORT FUNCTION ENDS


public function getOfficersmisPDF(Request $request, $ac) {
        //ECI getOfficersmisPdf PDF REPORT TRY CATCH BLOCK STARTS
		
		//echo "hello"; die;
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $uid = $user->id;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
                $xss = new xssClean;
				$st_code=$d->st_code;
                //$st_code = base64_decode($xss->clean_input($state));
				//$st_code = base64_decode($xss->clean_input($d->st_code));
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
                    $statelist = getallstate();
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
					
					
					//echo "===!@23".$st_code; die;
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.st_code', '=', $st_code)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id","candidate_nomination_detail.district_no", "candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.ac_no")
                            ->get();
							
							
							//echo "<pre>"; print_r($totalContestedCandidatedata); die;
                } else if (!empty($st_code) && $cons_no != '' && $st_code != 'All') {
					
					//echo "===++"; die;
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
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.district_no","candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.ac_no")
                            ->get();
                } else if (!empty($st_code) && $cons_no == '' && $st_code == 'All') {
					
					//echo "===---"; die;
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->whereIn('candidate_nomination_detail.st_code', $permitstates)
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.district_no","candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.ac_no")
                            ->get();
                } else if ($st_code == '' && $cons_no == '') {
					
					//echo "rockuy"; die;
                    $totalContestedCandidatedata = DB::table('candidate_nomination_detail')
                            ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
                            ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
                            ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
                            ->where('candidate_nomination_detail.application_status', '=', '6')
                            ->where('candidate_nomination_detail.finalaccepted', '=', '1')
                            ->where('candidate_nomination_detail.symbol_id', '<>', '200')
                            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
                            ->select("candidate_nomination_detail.candidate_id", "candidate_nomination_detail.district_no","candidate_nomination_detail.st_code", "candidate_nomination_detail.ac_no", DB::raw("COUNT(candidate_nomination_detail.candidate_id) as totalcandidate"))
                            ->groupBy("candidate_nomination_detail.ac_no")
                            ->get();
                }
				
				//echo "==="; die;
				
				//echo "<pre>"; print_r($d); die;

               // dd($totalContestedCandidatedata);

                $pdf = PDF::loadView('admin.ac.ceo.Expenditure.mis-officerPDFhtml', ['user_data' => $d, 'totalContestedCandidatedata' => $totalContestedCandidatedata,'st_code' => $st_code,'cons_no' => $cons_no]);
                return $pdf->download('CeoOfficerMISPdf_' . trim($st_code) . '_Today_' . $cur_time . '.pdf');
               return view('admin.ac.ceo.Expenditure.mis-officerPDFhtml');
            } else {
                return redirect('/admin-login');
            }
        } catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');
        }
        //ECI mis-officerPDFhtml PDF REPORT TRY CATCH BLOCK ENDS
    }






############################End CEO MIS by Niraj ##############################################


}  // end class