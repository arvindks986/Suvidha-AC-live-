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
		//use App\adminmodel\ROACModel;
		use App\adminmodel\ROACReportModel;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		//use Spatie\MixedContentScanner\MixedContentScanner;
class RoACReportController extends Controller
{
    //
   public function __construct()
        {   
			$this->middleware('adminsession');
			$this->middleware('ro');
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			//$this->romodel = new ROACModel();
			$this->roacreportmodel = new ROACReportModel();
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
   * @author Devloped Date : 16-02-19
   * @author Modified By : 
   * @author Modified Date : 
   * @author param return electors-pollingstation List By State fuction     
   */
	
   public function electorsropollingstationList(Request $request){ 
	 if(Auth::check()){
	  $user = Auth::user();
	  $d=$this->commonModel->getunewserbyuserid($user->id);
	  $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
               $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
               if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
                $cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
               }
           $seched=getschedulebyid($ele_details->ScheduleID);
               $sechdul=checkscheduledetails($seched);
	 $election_id= $ele_details->ELECTION_ID;
	 $pc_no= $ele_details->CONST_NO;
	 $st_code= $ele_details->ST_CODE;
	 $acdata = $this->roacreportmodel->getAcByPC($st_code,$pc_no,$election_id);
	//dd($acdata);
	  return view('admin.ac.ro.electors-ropollingstationlist',['user_data' => $d,'cand_finalize_ceo' =>$cand_finalize_ceo,'cand_finalize_ro' =>$cand_finalize_ro,'sechdul' => $sechdul,'ele_details' => $ele_details,'acdata'=> $acdata]);
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
   
  public function electorsropollingstationStore(Request $request){
	 //echo '<pre>'; print_r($request->all()); exit;
	 //dd($request->all());
	 if(Auth::check()){
	  $user = Auth::user();
	  $d=$this->commonModel->getunewserbyuserid($user->id);
	 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
               $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
               if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
                $cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
               }
           $seched=getschedulebyid($ele_details->ScheduleID);
               $sechdul=checkscheduledetails($seched);
       	    $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
	
	 $election_id=$ele_details->ELECTION_ID;
	 $CONST_TYPE=$ele_details->CONST_TYPE;
	 $pc_no= $ele_details->CONST_NO;
	 $st_code= $ele_details->ST_CODE;
 
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
			'const_no'=>$request->ac_no[$i],
			'const_type'=>$CONST_TYPE,
			'st_code'=>$request->st_code,
			'ac_no'=>$ac_no,
			'pc_no'=>$request->pc_no,
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
		 $checkelectorData = DB::table('elector_details')->where('ac_no', $ac_no)->first();
		 //dd($checkelectorData);
		 if(!empty($checkelectorData)){
			$n = DB::table('elector_details')->where('ac_no', $ac_no)->update($elector_data);
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
	
         
	function public_affidavit()
			{  
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		         
                $list=$this->romodel->public_affidavit_ac($d->st_code,$d->ac_no);
		          \Session::flash('success_mes', 'After Scrutiny All affidavit Public');
                  return Redirect::to('/ro/contested-application');
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
				
			}
	 	
		    
     

	public function roOfficerLogindetailsList(request $request){
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
               $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
               if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
                $cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
               }
           $seched=getschedulebyid($ele_details->ScheduleID);
               $sechdul=checkscheduledetails($seched);
            $officerDetails=$this->ropcreportmodel->getOfficerlistByROPC($d->st_code,$d->pc_no);
           
            
            return view('admin.ac.ro.roofficer-logindetails', ['user_data' => $d,'officerDetails'=>$officerDetails,'ele_details'=>$ele_details]);
        }
        else {
            return redirect('/officer-login');
        }
	} 

	public function logindetailpdf(request $request){
		//echo "test";die;
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
                
          $st_code=$d->st_code;
            $officerDetails=$this->ropcreportmodel->getOfficerlistByROPC($d->st_code,$d->pc_no);
           $allUsers =DB::table('officer_login')->where('st_code',$d->st_code)->get();
$pdf = PDF::loadView('admin.pc.ro.ropcOfficerDetailHtml', compact('st_code','officerDetails'));
            return $pdf->download($st_code."-user-login-detail-report".".pdf");
            
            //return view('admin.pc.ro.ropcOfficerDetailHtml', ['user_data' => $d,'officerDetails'=>$officerDetails,'ele_details'=>$ele_details]);
        }
        else {
            return redirect('/officer-login');
        }
	} 
		public function loginDetailExcel(request $request){
		//echo "test";die;
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $st_code =$d->st_code;
                    $cur_time    = Carbon::now(); 
            \Excel::create('officer-login-detail'.trim($st_code).'_'.$cur_time, function($excel) use($st_code) { 
      $excel->sheet('Sheet1', function($sheet) use($st_code) {
      $arr  = array();
      //$cand_party_type='Z'; 
      $finalize='1';
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
         $officerDetails=$this->ropcreportmodel->getOfficerlistByROPC($d->st_code,$d->pc_no);
         $j=0;
      foreach ($officerDetails as $officerDetailsList) {
        $j++;
          $data =  array(
                  $j,
                  $officerDetailsList->name,
                  $officerDetailsList->designation,
                  $officerDetailsList->officername,
                  'demo@1234'
                        );
                  array_push($arr, $data); 
                  }
   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                       'Sr. No.', 'Officer Name', 'Designation', 'User Id','Password'
               )

           );

         });

    })->export('xls');
        }
        else {
            return redirect('/officer-login');
        }
	} 
    public function changepassword(request $request){ 
				if(Auth::check()){ 
					$user = Auth::user();
					$d=$this->commonModel->getunewserbyuserid($user->id); 
			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
               $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
               if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
                $cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
               }
           $seched=getschedulebyid($ele_details->ScheduleID);
               $sechdul=checkscheduledetails($seched);
					return view('admin.pc.ro.changepassword', ['user_data' => $d,'cand_finalize_ceo' =>$cand_finalize_ceo,'cand_finalize_ro' =>$cand_finalize_ro,'sechdul' => $sechdul]);
				}
			} //@end changepassword function

	/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 20-02-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return changePasswordStore By RO     
  */
  public function changePasswordStore(request $request){ 
		if(Auth::check()){ 
					$user = Auth::user();
					$d=$this->commonModel->getunewserbyuserid($user->id); 
					 
					if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
						// The passwords matches
						return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
				}
				if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
						//Current password and new password are same
						return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
				}
				$validatedData = $request->validate([
						'current-password' => 'required',
						'new-password' => 'required|string|min:8',
						'new-password-confirm' => 'required|string|min:8',
				]);
				//Change Password
				$user = Auth::user();
				$user->password = bcrypt($request->get('new-password'));
				$user->save();
				 return redirect()->back()->with("success","Password changed successfully !");
		     }//@end Auth::check()

	} //@end changePasswordStore function
}  // end class  
