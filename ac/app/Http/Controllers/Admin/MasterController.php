<?php  
		namespace App\Http\Controllers\Admin;
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
		use App\adminmodel\CandidateModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\adminmodel\MasterReportModel;
		use App\adminmodel\ROACReportModel;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		//use Spatie\MixedContentScanner\MixedContentScanner;
class MasterController extends Controller
{
    //
   public function __construct()
        {   
			$this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->middleware('deo');
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->masterreportmodel = new MasterReportModel();
		//	$this->roacreportmodel = new ROACReportModel();
			$this->xssClean = new xssClean;
			$this->sym = new SymbolMaster();	
		}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
 	protected function guard(){
        return Auth::guard('admin');
    	}
   
   /**
   * @author Devloped By : Niraj Kumar
   * @author Devloped Date : 14-03-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return electors-pollingstation List By State fuction     
   */
	
   public function electorsDeopollingstationList(Request $request){  	
	 if(Auth::check()){
	  $user = Auth::user();
	  $d=$this->commonModel->getunewserbyuserid($user->id);
	 //dd($d);
		$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
		//dd($ele_details);
	/*	$check_finalize=candidate_finalizebyro($ele_details[0]->ST_CODE,$ele_details[0]->CONST_NO,$ele_details[0]->CONST_TYPE);
		if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
		$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
		}
     $seched=getschedulebyid($ele_details->ScheduleID);
     $sechdul=checkscheduledetails($seched);*/
	 $election_id= $ele_details[0]->ELECTION_ID;
	 $dist_no= $d->dist_no;
	 $st_code= $d->st_code;
	 $acdata = $this->masterreportmodel->getAcByDeo($st_code,$dist_no,$election_id);
	//dd($acdata);
	  return view('admin.ac.deo.electors-deopollingstationlist',['user_data' => $d,'ele_details' => $ele_details,'acdata'=> $acdata]);
	  }else {
	   return redirect('/officer-login');
	 }   
	}   // end electorspollingstation List function
	
	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 14-01-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return electors-pollingstation Store By State fuction     
  */
   
  public function electorsDeoPollingstationStore(Request $request){
	 //echo '<pre>'; print_r($request->all()); exit;
	 //dd($request->all());
	 if(Auth::check()){
	  $user = Auth::user();
	  $d=$this->commonModel->getunewserbyuserid($user->id);
	  $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);

	 $election_id=$ele_details[0]->ELECTION_ID;
	 $CONST_TYPE=$ele_details[0]->CONST_TYPE;
	 $dist_no= $request->dist_no;
	 $st_code= $request->st_code;
 
	 $added_create_at = date('Y-m-d');
	 $created_at = date('Y-m-d H:i:s');
	 $totalchecked = count($request->checkbox);

	 //Ac No
	 $acno_arr = $request->ac_no;
	 $acname_arr = $request->ac_name;
	 $gen_male =   $request->gen_male;
	 $gen_female = $request->gen_female;
	 $gen_third = $request->gen_third;
	 $gen_total = $request->gen_total;

	 $ser_male =   $request->ser_male;
	 $ser_female = $request->ser_female;
	 $ser_third = $request->ser_third;
	 $ser_total = $request->ser_total;

	 $regular_arr =   $request->regular;
	 $auxillary_arr = $request->auxillary;
	 $polling_total_arr = $request->polling_total;
	 
	 DB::enableQueryLog();

	 for($i=0;$i<$totalchecked;$i++)
	 {  
		 $ac_no=$request->checkbox[$i];
		 $gen_m = $gen_male[$ac_no];
		 $gen_f = $gen_female[$ac_no];
		 $gen_o = $gen_third[$ac_no];
		// $gen_t = $gen_total[$ac_no];
			$gen_t = $gen_m+$gen_f+$gen_o;
			
		 $ser_m = $ser_male[$ac_no];
		 $ser_f = $ser_female[$ac_no];
		 $ser_o = $ser_third[$ac_no];
		// $ser_t = $ser_total[$ac_no];
		 $ser_t = $ser_m+$ser_f+$ser_o;
		 $polling_reg = $regular_arr[$ac_no];
		 $polling_auxillary = $auxillary_arr[$ac_no];
		// $polling_total = $polling_total_arr[$ac_no];
		 $polling_total = $polling_reg+$polling_auxillary;
		
		  $elector_data = array(
			'election_id'=>$election_id,
		/*	'const_no'=>$request->ac_no[$i],*/
			'const_type'=>$CONST_TYPE,
			'st_code'=>$st_code,
			'ac_no'=>$ac_no,
			'dist_no'=>$request->dist_no,
			'gen_m'=>$gen_m,
			'gen_f'=>$gen_f,
			'gen_o'=>$gen_o,
			'gen_t'=>$gen_t,
			'ser_m'=>$ser_m,
			'ser_f'=>$ser_f,
			'ser_o'=>$ser_o,
			'ser_t'=>$ser_t,
			'polling_reg'=>$polling_reg,
			'polling_auxillary'=>$polling_auxillary,
			'polling_total'=>$polling_total,
			'poll_date'=>$added_create_at,
			'added_create_at'=>$added_create_at,
			'created_at'=>$created_at,
			'created_by'=>$user->id
			 );
		  //dd($elector_data);
		 $checkelectorData = DB::table('elector_details')->where('st_code', $st_code)->where('ac_no', $ac_no)->first();
		 //dd($checkelectorData);
		 if(!empty($checkelectorData)){
			$n = DB::table('elector_details')->where('st_code', $st_code)->where('ac_no', $ac_no)->update($elector_data);
		   }else{
           $n = DB::table('elector_details')->insert($elector_data);
		}
		//dd(DB::getQueryLog());
	 }
	 \Session::flash('success_admin', 'You have Successfully saved Schedule. '); 
	 return redirect()->back();
	 /*if($n=="true"){
	   \Session::flash('success_admin', 'You have Successfully Added Schedule. '); 
		// return Redirect::to('/admin/pc/ceo/electors-pollingstationlist');
		return redirect()->back();
		 }else {
		 \Session::flash('error_mes', 'Data Insertion/Updation Unsuccessfull. '); 
		 return redirect()->back();
	  }  */
	 } 
	}   // end electorspollingstationData function
	
    
}  // end class  
