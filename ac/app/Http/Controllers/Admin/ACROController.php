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
		use App\adminmodel\ACROModel;
		use App\Classes\xssClean;
		use Illuminate\Support\Facades\Crypt; 
		use App\Exports\ExcelExport;
		use Maatwebsite\Excel\Facades\Excel;
		
		
	ini_set("memory_limit","1500M");
    set_time_limit('240');
    ini_set("pcre.backtrack_limit", "100000000");
		
		
		
class ACROController extends Controller
{
    //
     public $base    = 'roac';
    public $folder  = 'ro';
    public $action    = 'roac/';
    public $view_path = "admin.ac.ro";
   public function __construct()
        {   
			$this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->middleware('ro');
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->romodel = new ACROModel();
			$this->xssClean = new xssClean;
			if(!Auth::check()){ 
           return redirect('/officer-login');
          }
		}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
 	protected function guard(){
        return Auth::guard('admin');
    	}

    public function index() {      
	        $data  = [];
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);  
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		    if($ele_details=='') 
		      	{
		      	\Session::flash('error_mes', 'Election has not assigned');
                return Redirect::to('/logout');
		      	}
           $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
           if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
           }
		   
		   
		   $seched=getschedulebyid($ele_details->ScheduleID);
		   
		   //dd($seched);
        //    $sechdul=checkscheduledetails($seched);
                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    // $data['sechdul']=$sechdul;
                    $data['sched']=$seched; 
                    $data['cand_finalize_ro']=$cand_finalize_ro; 


			//dd($data);
					
					
					
					
					
					
					
	$totrej=CandidateNomination::where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->count();
	
	 $data['totrej']=$totrej;
	
    $totalwith= CandidateNomination::where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->count() ;
    
	$data['totalwith']=$totalwith;
	
	
	
    $totaccepted=CandidateNomination::where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->count();
	
	$data['totaccepted']=$totaccepted;
	
	
    $total=CandidateNomination::where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->count();
	
	$data['total']=$total;
	
	
    try {
      // $total_prescrutiny = \app(App\models\Admin\Nomination\NominationApplicationModel::class)::where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('is_apply_prescrutiny', '1')->count();
      $total_pen_verification = DB::table('nomination_application')
      ->where(['st_code' =>$ele_details->ST_CODE,'ac_no' =>$ele_details->CONST_NO,
      'election_id' =>$ele_details->ELECTION_ID])
      ->where('finalize', '=', '1')
      ->where('finalize_after_payment', '=','1')
      ->where('is_physical_verification_done', '=', '0')
      ->get()->count();
	  	$data['total_pen_verification']=$total_pen_verification;
	  
      $appointment_pend = DB::table('nomination_application')->join('appointment_schedule_date_time', [
      ['nomination_application.candidate_id', '=', 'appointment_schedule_date_time.candidate_id'],
      ['nomination_application.st_code', '=', 'appointment_schedule_date_time.st_code'],
      ['nomination_application.ac_no', '=', 'appointment_schedule_date_time.ac_no']])
      ->where(['nomination_application.st_code' =>$ele_details->ST_CODE,'nomination_application.ac_no' =>$ele_details->CONST_NO])
      ->where('finalize', '=', '1')
      ->where('appointment_schedule_date_time.status', '=', '1')
      ->where('finalize_after_payment', '=','1')
      ->where('appointment_schedule_date_time.is_ro_acccept', '=', '0')
      ->groupBy('nomination_application.candidate_id')->get()->count();
	  
	  
	  	  	$data['appointment_pend']=$appointment_pend;
	  
	  
      $prescrutiny_url = url('/roac/listallapplicant_prescrutiny');
	  	  	  	$data['prescrutiny_url']=$prescrutiny_url;
	  
      $phyical_verification = url('/roac/listallapplicant');
	  
	  $data['phyical_verification']=$phyical_verification;
	  
    } catch (\Throwable $th) {
      $prescrutiny_url = '#';
      $appointment_tot = 0;
      $phyical_verification = '#';
      $appointment_pend = 0;
      $total_prescrutiny = 0;
      $total_pen_verification = 0;
	  
	  
	  $data['prescrutiny_url']=$prescrutiny_url;
	  $data['appointment_tot']=$appointment_tot;
	  $data['phyical_verification']=$phyical_verification;
	  $data['appointment_pend']=$appointment_pend;
	  $data['total_prescrutiny']=$total_prescrutiny;
	  $data['total_pen_verification']=$total_pen_verification;
	  
	  
	  
	  
    }
		
					
            return view($this->view_path.'.dashboard', $data);	           
	        
            
	    }  // end index function

		 
	public function listallcandidate(Request $request)
		    {
		         $data  = [];

		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		   		 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		         $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		         if($check_finalize=='') { 
		         			$cand_finalize_ceo=0; $cand_finalize_ro=0;
		         		} else {
		           		$cand_finalize_ceo=$check_finalize->finalize_by_ceo; 
		           		$cand_finalize_ro=$check_finalize->finalized_ac;
		             }
				    
		        
		        $val=$this->romodel->checkfinalize_acbyro($d->st_code,$d->ac_no,$d->officerlevel);
		     	$cand_status = $request->input('cand_status');
		     	$search = $request->input('search');
             	$list=$this->romodel->Allcandidatelist($ele_details,$cand_status,$search);
		     	$status=allstatus();
		     	    
		     	    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['status_list']=$status;
                    $data['checkval']=$val;
                    $data['status']=$cand_status;
                    $data['lists']=$list;
                    $data['cand_finalize_ro']=$cand_finalize_ro;  

	              return view($this->view_path.'.listallcandidate', $data);
		         
		     
		    }  // end  function   
    public function withdrawn_candidates(Request $request)
		    {
		     if(Auth::check()){
		     	$data  = [];
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		   		$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		         if($check_finalize=='') { 
		         			$cand_finalize_ceo=0; $cand_finalize_ro=0;
		         		} else {
		           		$cand_finalize_ceo=$check_finalize->finalize_by_ceo; 
		           		$cand_finalize_ro=$check_finalize->finalized_ac;
		             }
		         
		        $val=$this->romodel->checkfinalize_acbyro($d->st_code,$d->ac_no,$d->officerlevel);
		     	$cand_status = $request->input('cand_status');
		     	$search = $request->input('search');
		     	$list=$this->romodel->withdrawn($ele_details,$cand_status,$search);
		     	$status=allstatus();
		            
		            $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['status_list']=$status;
                    $data['checkval']=$val;
                    $data['status']=$cand_status;
                    $data['lists']=$list;
                    $data['cand_finalize_ro']=$cand_finalize_ro;  

	              return view($this->view_path.'.withdrawn_candidates', $data);
	       
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function   withdrawn_candidates

	 
	 
	public function statusvalidation(Request $request)
			{
				if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		       

		     $this->validate(
                $request, 
                    [
                     //'verifyotp' => 'required|numeric',
                     //'affidavit' => 'required',
                      'rejection_message' => 'required',
                     ],
                    [
                    // 'verifyotp.required' => 'Please enter your valid Otp', 
                     //'verifyotp.numeric' => 'Please enter your valid Otp',
                     //'affidavit.required' => 'Please check the affidavit',
                     'rejection_message.required' => 'Please enter Message',
                     ]);
		 		//$verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
                $candidate_id = $this->xssClean->clean_input($request->input('candidate_id')); 
                $nom_id = $this->xssClean->clean_input($request->input('nom_id')); 
                $marks = $this->xssClean->clean_input($request->input('marks'));
                $rejection_message = $this->xssClean->clean_input($request->input('rejection_message'));
                //$affidavit = $this->xssClean->clean_input($request->input('affidavit'));  
                $st = array('rejection_message'=>$rejection_message,'application_status'=>$marks,'affidavit_public'=>'yes'); 
			    $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			    \Session::flash('ro_admin', 'Action successfully Change' ); 
				 
				$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',
										$nom_id,'application_status','receipt_generated',$marks,
										request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));

		      	 \Session::flash('success_mes', 'Candidate status successfully changed');
		    		return Redirect::to('roac/scrutiny-candidates');
		     
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
			}
	public function withstatusvalidation(Request $request)
			{
				if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		       

		     $this->validate(
                $request, 
                    [
                     //'verifyotp' => 'required|numeric',
                     //'affidavit' => 'required',
                      'rejection_message' => 'required',
                     ],
                    [
                    // 'verifyotp.required' => 'Please enter your valid Otp', 
                     //'verifyotp.numeric' => 'Please enter your valid Otp',
                     //'affidavit.required' => 'Please check the affidavit',
                     'rejection_message.required' => 'Please enter Message',
                     ]);
		 		//$verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
                $candidate_id = $this->xssClean->clean_input($request->input('candidate_id')); 
                $nom_id = $this->xssClean->clean_input($request->input('nom_id')); 
                $marks = $this->xssClean->clean_input($request->input('marks'));
                $rejection_message = $this->xssClean->clean_input($request->input('rejection_message'));
                //$affidavit = $this->xssClean->clean_input($request->input('affidavit'));  
                $st = array('rejection_message'=>$rejection_message,'application_status'=>$marks,'affidavit_public'=>'yes'); 
			    $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			    \Session::flash('ro_admin', 'Action successfully Change' ); 
				 
				$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'application_status','receipt_generated',$marks,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		      	\Session::flash('success_mes', 'Candidate withdrawn status successfully changed');
                 
		    		return Redirect::to('roac/withdrawn-candidates');
		     
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
			}
	public function accepted_application(Request $request)
			{
			if(Auth::check()){
				$data  = [];
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		   		$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		         if($check_finalize=='') { 
		         			$cand_finalize_ceo=0; $cand_finalize_ro=0;
		         		} else {
		           		$cand_finalize_ceo=$check_finalize->finalize_by_ceo; 
		           		$cand_finalize_ro=$check_finalize->finalized_ac;
		             }
		           
		        $this->romodel->update_newsequence($ele_details);
		   		$val=$this->romodel->checkfinalize_acbyro($d->st_code,$d->ac_no,$d->officerlevel);
		     	 
		     	$search = $request->input('search');
		    	$list=$this->romodel->contestingcandidate($ele_details,$search);
		    	    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['checkval']=$val;
                    
                    $data['lists']=$list;
                    $data['cand_finalize_ro']=$cand_finalize_ro;  

	              return view($this->view_path.'.listaccepted', $data);
		    	 
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
			}
	public function change_sequence(Request $request)
			{
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		       $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
            
                //dd($request);
		        $noval = $this->xssClean->clean_input($request->input('noval'));
		        $v = $this->xssClean->clean_input($request->input('totalvalue'));
				$input = $request->all();
				$rules = ['Please enter all new serial number'];
		for ($i=1; $i<$noval;$i++)
		    {
		    $this->validate($request, ['newsrno'.$i => 'required|integer',],
                [
                'newsrno'.$i.'required' => 'Please enter all new serial number ',
                ]);	
		    }
		for ($i=1; $i<$noval;$i++)
		    { $k=$i+1;
		      $s=$this->xssClean->clean_input($request->input('newsrno'.$i));
		      $s1=$this->xssClean->clean_input($request->input('newsrno'.$k));
		      if($s>$v) 
		      	{
		      	\Session::flash('error_mes', 'Enter valid new serial number ');
                return Redirect::to('/roac/contested-application');
		      	}
		       if($s==$s1) 
		      	{
		      	\Session::flash('error_mes1', 'Dublicate Sr. number ');
                return Redirect::to('/roac/contested-application');
		      	}
		       if($s==0) 
		      	{
		      	\Session::flash('error_mes1', 'please not entry zero');
                return Redirect::to('/roac/contested-application');
		      	}
		    }
		   $rec= DB::table('candidate_nomination_detail')->where('party_id','1180')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();
		   //dd($rec);	
		  for ($i=1; $i<$noval;$i++)
		       	{
		       	$s=trim($request->input('newsrno'.$i));
		       	$candidate_id=trim($request->input('nom_id'.$i));
		       	$no = array('new_srno'=>$s); 
		        DB::table('candidate_nomination_detail')->where('nom_id', $candidate_id)->update($no);	
		         $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$candidate_id,'new_srno','NO',$s,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		       	}
		       	 	//$n=$noval;
		       
            if(isset($rec)){
            		$no = array('new_srno'=>$noval);
            	 DB::table('candidate_nomination_detail')->where('nom_id', $rec->nom_id)->update($no);
               }
		       \Session::flash('success_mes', 'Candidate New sr.no successfully Updated');
                return Redirect::to('/roac/contested-application');
		         }
	        else {
	              return Redirect::to('/officer-login');
	        	  }	
			}
	public function pdfview(Request $request)
		    {
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		       $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
           
		    if($ele_details->CONST_TYPE=="AC") { 
		    			$v= 'candidate_nomination_detail.ac_no'; $m=$ele_details->CONST_NO; 
		    		}
  			elseif($ele_details->CONST_TYPE=="PC") {  
  						$v= 'candidate_nomination_detail.pc_no'; $m=$ele_details->CONST_NO;   
  					}

		   $candn = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
		    //->where('candidate_nomination_detail.finalize','=','1')
		    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		    //->where('m_party.PARTYTYPE','=','N')
		    ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get(); 

    	  
    		$a='N'; $a1='S';
			$cands = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
		    //->where('candidate_nomination_detail.finalize','=','1')
		     ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		    ->where(function($query1) use ($a,$a1){
                    		  $query1->where('candidate_nomination_detail.cand_party_type','=',$a)
                        ->orWhere('candidate_nomination_detail.cand_party_type','=',$a1);
              			})
		    ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get(); 
            $a2='U'; $a3='0';
			$candu = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
		    //->where('candidate_nomination_detail.finalize','=','1')
		     ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		     ->where(function($query1) use ($a2,$a3){
                    		  $query1->where('candidate_nomination_detail.cand_party_type','=',$a2)
                        ->orWhere('candidate_nomination_detail.cand_party_type','=',$a3);
              			})
		     ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get(); 

    		$candz = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')    
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
            ->where('candidate_nomination_detail.symbol_id','<>','200')
		    //->where('candidate_nomination_detail.finalize','=','1')
		    ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
		    ->where('candidate_nomination_detail.cand_party_type','=','U')
		    ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.candidate_residence_address','candidate_nomination_detail.*', 'm_party.PARTYNAME','m_party.PARTYABBRE','m_party.PARTYTYPE','m_symbol.SYMBOL_DES','candidate_personal_detail.candidate_residence_address','candidate_personal_detail.candidate_residence_stcode','candidate_personal_detail.candidate_residence_districtno','candidate_personal_detail.candidate_residence_acno')->get(); 


		     $pc=''; $ac='';
		          if(!empty($d->ac_no))
		        	$ac=getacbyacno($d->st_code,$d->ac_no);
		        if(!empty($d->pc_no))
    				$pc=getpcbypcno($d->st_code,$d->pc_no);
				
				$state=getstatebystatecode($d->st_code);
	    	 view()->share('candn',$candn,'cands',$cands,'candu',$candu,'candz',$candz,'state',$state,'ac',$ac);
              
		        if($request->has('download')){
		            $pdf = PDF::loadView('admin.pdfview',compact('candn',$candn,'cands',$cands,'candu',$candu,'candz',$candz,'state',$state,'ac',$ac));
		            return $pdf->download('contesting-candidates.pdf');
		        }


		        return view('contesting-candidates');
			 }
	        else {
	              return redirect('/officer-login');
	        	  }
		    }
    public function symbol_upload()  
			{
			if(Auth::check()){
		         $data  = [];
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		   		$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		         if($check_finalize=='') { 
		         			$cand_finalize_ceo=0; $cand_finalize_ro=0;
		         		} else {
		           		$cand_finalize_ceo=$check_finalize->finalize_by_ceo; 
		           		$cand_finalize_ro=$check_finalize->finalized_ac;
		             }

		   		$val=$this->romodel->checkfinalize_acbyro($d->st_code,$d->ac_no,$d->officerlevel);

		        $list=$this->romodel->Symbolcandidate($ele_details);
		        
		        $sym = DB::table("m_symbol")->Where('SYMBOL_NO', '<>', '-1')
		        				->whereNOTIn('SYMBOL_NO',function($query){
					               $query->select('PARTYSYM')->from('m_party')->where('PARTYTYPE',['N','S','Z']);
					            })->get();
		        
		     	  
		    	    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['checkval']=$val;
                    $data['sym']=$sym;
                    $data['lists']=$list;
                    $data['cand_finalize_ro']=$cand_finalize_ro;  

	              return view($this->view_path.'.symboldetails', $data);
                
		    	 
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
			}    
   //  public function assign_symbol($nom_id) 
			// {
			// if(Auth::check()){
		 //        $user = Auth::user();
		 //        $d=$this->commonModel->getunewserbyuserid($user->id);
   //          if(!empty($nom_id)) {    
		 //        $list=$this->romodel->Symbolassign($nom_id);
		    
		 //    	return view('admin.ac.ro.symbolassign',['user_data' => $d,'lists'=>$list,'showpage'=>'candidate']);
		 //    	}
		 //    else {
		 //    	 return Redirect::to('/roac');
		 //        }
		 //         }
	  //       else {
	  //        	return Redirect::to('/officer-login');
	  //       	  }	
			// }
	public function updatesymbol(Request $request) 
			{
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
                 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
           
		        $this->validate(
                $request, 
                    [
                     'symbol' => 'required',
                    ],
                    [
                     'symbol.required' => 'Please select symbol', 
                    ]);
                  $candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
                  $nom_id = $this->xssClean->clean_input($request->input('nom_id'));
                  $symbol = $this->xssClean->clean_input($request->input('symbol'));
                  $check = DB::table('candidate_nomination_detail')->where('symbol_id',$symbol)->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->first();
                 // $check=getById('candidate_nomination_detail','symbol_id',$symbol);
                  $udata = array('symbol_id'=> $symbol); 
                  //echo $candidate_id;  echo "=".$nom_id; candidate_nomination_detail.ST_CODE
		          
		          if(!isset($check)){
			          $n=$this->commonModel->updatedata('candidate_nomination_detail','nom_id',$nom_id,$udata);
			           $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'symbol_id','NO',$symbol,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		         
		         
				          \Session::flash('success_mes', 'Symbol successfully Assign');
		                  return Redirect::to('/roac/symbol-upload');
                   }
                  else {
                  	\Session::flash('error_mes', 'Symbol Already Assign choose another');
		                  return Redirect::to('/roac/symbol-upload');
                  }
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
			}  
	function finalize_ac()
			{
			 if(Auth::check()){
			  $data  = [];  
		      $user = Auth::user();
		      $d=$this->commonModel->getunewserbyuserid($user->id);
		      $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
              
	          

          $check_ac = DB::table('candidate_finalized_ac')->where('st_code',$ele_details->ST_CODE)
        		->where('const_no',$ele_details->CONST_NO)
        		->where('const_type',$ele_details->CONST_TYPE)
        		->where('election_id',$ele_details->ELECTION_ID)->first();      

           
        	$date = Carbon::now();
        	$currentTime = $date->format('Y-m-d H:i:s');
		 
		      //$otp= "123456";
		     $otp= rand(100000,999999);
		      $mob_message = "Dear Sir/Madam Your OTP is ".$otp." for finalized AC in Suvidha Portal.Please enter the OTP  to proceed.This OTP is valid only for 10 minutes.Do not share this OTP. Regards  
		          Team ICT  ";
		     if($d->Phone_no!='')
		            $response = SmsgatewayHelper::gupshup($d->Phone_no,$mob_message);

		if(!isset($check_ac)) {
			$st = array('st_code'=>$ele_details->ST_CODE,
							'const_no'=>$ele_details->CONST_NO,
							'const_type'=>$ele_details->CONST_TYPE,
							'election_id'=>$ele_details->ELECTION_ID,
							'finalized_ac'=>'0','mobile_otp'=>$otp,
							'otp_time' => $currentTime,
							'created_at'=>date("Y-m-d H:i:s"),
							'created_by'=>$d->officername); 
			    $r=$this->commonModel->insertData('candidate_finalized_ac',$st);
			     $check_ac = DB::table('candidate_finalized_ac')
			  					->where('st_code',$ele_details->ST_CODE)
			  					->where('const_no',$ele_details->CONST_NO)
			  					->where('const_type',$ele_details->CONST_TYPE)
			  					->where('election_id',$ele_details->ELECTION_ID)->first();
            }
        else{  
        	$st = array('mobile_otp'=>$otp,'otp_time' => $currentTime);
        	$i = DB::table('candidate_finalized_ac')->where('id',$check_ac->id)->update($st);
         	}
       
		   $html =$otp; 
		   if(!empty($d->email)) {  
						      //sendotpmail($d->email,'Otp details',$html);  
                             mail ($d->email, 'otp details',$html,'suvidha.eci.gov.in'); 
						}
				$response = SmsgatewayHelper::gupshup($d->Phone_no,$mob_message);
				    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['stcode']=$ele_details->ST_CODE;
                    $data['constno']=$ele_details->CONST_NO;
                    $data['lists']=$check_ac;
                    $data['otp']=$otp;
                    $data['otp_time']=$currentTime;
                    
                   return view($this->view_path.'.finalize-ac',$data); 
				 
		         }
	        else {
	         		return Redirect::to('/officer-login');
	        	 }		
			}
	function finalize_candidate(Request $request)
			{  
			if(Auth::check()){

				 DB::beginTransaction();
             try{
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          
		        $this->validate(
	                $request, 
	                    [
	                     'verifyotp' => 'required|numeric',
	                     //'finalized_message' => 'required',
	                     ],
	                    [
	                     'verifyotp.required' => 'Please enter your valid Otp', 
	                     'verifyotp.numeric' => 'Please enter your valid Otp',
	                     //'finalized_message.required' => 'Please check the affidavit',
	                     ]);
		       $verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
		       $finalized_message = $this->xssClean->clean_input($request->input('finalized_message'));
		       $id = $this->xssClean->clean_input($request->input('id'));
		       $cons_no = $this->xssClean->clean_input($request->input('cons_no'));
		       $st_code = $this->xssClean->clean_input($request->input('st_code'));
		       $CONS_TYPE = $this->xssClean->clean_input($request->input('CONS_TYPE'));
		       $ELECTION_ID = $this->xssClean->clean_input($request->input('ELECTION_ID'));
		       $otp = $this->xssClean->clean_input($request->input('otp'));
		       $otp_time = $this->xssClean->clean_input($request->input('otp_time'));
		        
		       $date = Carbon::now()->subMinutes(10);
                $currentTime = $date->format('Y-m-d H:i:s');
                //echo $currentTime; echo $otp_time;
		       if($otp!=$verifyotp) {
		       	 \Session::flash('ro_opt_messsage', 'Your Otp Message Invalide');
                  return Redirect::to('/roac/finalize-ac');
		       }
		      if($otp_time<$currentTime) {
		      	 \Session::flash('ro_opt_messsage', 'Your Otp time Expair');
                  return Redirect::to('/roac/finalize-ac');
		       }
		       $ins_data = array('finalized_ac'=>'1','indexcard_finalize'=>'1','finalized_message'=>$finalized_message,'finalize_date'=>date('Y-m-d'));
				$state=$this->commonModel->getstatebystatecode($d->st_code);
	    		$ac=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
				$ddeo = DB::table('officer_login')->where('st_code',$d->st_code)
						->where('dist_no',$d->dist_no)->where('officerlevel','DEO')->first();
				$cceo = DB::table('officer_login')->where('st_code',$d->st_code)
						->where('officerlevel','CEO')->first();

		 

                $list=$this->romodel->finalize_candidate_ac($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE,$ins_data); 
               
		        $html =$state->ST_NAME; 
				 
				DB::commit();
		          \Session::flash('success_mes', 'Finalized successfully');
                  return Redirect::to('/roac/contested-application');
                 
                  }catch (Exception $e) {
                   DB::rollback();

                      }
		         }
		         
	        else {
	         	return Redirect::to('/officer-login');
	        	  }	
			}	    
	 
	public function ballotpaperpdfview(Request $request)
		    {
			if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		     $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
           

		    if($ele_details->CONST_TYPE=="AC") { 
		    			$v= 'candidate_nomination_detail.ac_no'; $m=$ele_details->CONST_NO; 
		    		}
  			elseif($ele_details->CONST_TYPE=="PC") {  
  						$v= 'candidate_nomination_detail.pc_no'; $m=$ele_details->CONST_NO;   
  					}

		    $cand = DB::table('candidate_nomination_detail')
		   	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id') 
		    ->leftjoin('m_symbol','candidate_nomination_detail.symbol_id','=','m_symbol.SYMBOL_NO')
		    ->where('candidate_nomination_detail.st_code','=',$d->st_code)->where($v,'=',$m) 
		    ->where('candidate_nomination_detail.application_status','=','6')
		    ->orderBy('candidate_nomination_detail.new_srno', 'asc')
    		->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_image','candidate_nomination_detail.new_srno','m_symbol.*')->get(); 
			
		    view()->share('cand',$cand);

 			//if($request->has('download')){
		    $pdf = MPDF::loadView('admin.ballotview',compact('cand',$cand));
			return $pdf->download('dadmin.ballotview.pdf');
		    //$pdf = PDF::loadView('admin.ballotview',compact('cand',$cand));
		    //return $pdf->download('admin.ballotview.pdf');
		    ///}
			  return view('admin.ballotview');
			 }
	        else {
	              return redirect('/officer-login');
	        	  }
		    }	
		    
    public function listnomination(request $request){
         
        	$data  = [];
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
            $seched=getschedulebyid($ele_details->ScheduleID);
            if($seched['DATE_POLL']<=date("Y-m-d")) {
		            	$data['poll_val']=1;
		            }
		     else{
		     	$data['poll_val']=0;
		     }
             
	        $cand_finalize_ro =0;
	        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
	           if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
	           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
	           }  
            $val=$this->romodel->checkfinalize_acbyro($ele_details->ST_CODE,$ele_details->CONST_NO,'AC');
            
            $cand_status=''; $search='';
            $cand_status = $request->input('cand_status');
            $search = $request->input('search');
            $status=allstatus();
            $list=$this->romodel->Allcandidatelist($ele_details,$cand_status,$search);
                   
                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['checkval']=$val;
                    $data['status_list']=$status; 
                    $data['lists']=$list; 
                    $data['status']=$cand_status; 
                    $data['cand_finalize_ro']=$cand_finalize_ro ;
					//Indexcard flag check start
					$indexcard_finalize=0;
					if(!empty($check_finalize)){
						$indexcard_finalize=@$check_finalize->indexcard_finalize;
					}
                    $data['indexcard_finalize']=$indexcard_finalize ;
					//Indexcard flag check ends
            return view($this->view_path.'.listnomination',$data);
         
    } 	 
   public function reports(request $request){
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
	           $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
	           if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
	           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
	           }
			   $seched=getschedulebyid($ele_details->ScheduleID);
	           $sechdul=checkscheduledetails($seched);
            $val=$this->romodel->checkfinalize_acbyro($ele_details->ST_CODE,$ele_details->CONST_NO,'PC');
           // dd($request);
            $cand_status=''; $search='';
            $cand_status = $request->input('cand_status');
            $search = $request->input('search');
            $status=allstatus();
            $list=$this->romodel->Allcandidatelist($ele_details,$cand_status,$search);

            return view('admin.ac.ro.reports', ['user_data' => $d,'cand_finalize_ceo' =>$cand_finalize_ceo,'cand_finalize_ro' =>$cand_finalize_ro,'sechdul' => $sechdul,'sched'=>$seched, 'lists'=>$list,'status'=>$cand_status,'checkval'=>$val,'showpage'=>'candidate','status_list'=>$status,'ele_details'=>$ele_details]);
        }
        else {
            return redirect('/officer-login');
        }
    } 
    public function viewnomination($nomid)
			{      
				if(Auth::check()){ 
					 $data  = [];
		            $user = Auth::user();
		            $d=$this->commonModel->getunewserbyuserid($user->id); 
		             $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		            
		             $nomid   = Crypt::decrypt($nomid); 
 
		            $nom=getById('candidate_nomination_detail','nom_id',$nomid); 
					$cand=getById('candidate_personal_detail','candidate_id',$nom->candidate_id); 
				    $partyd= getpartybyid($nom->party_id);   
					$symb= getsymbolbyid($nom->symbol_id);
					$st= getstatebystatecode($cand->candidate_residence_stcode);
					$dist= getdistrictbydistrictno($cand->candidate_residence_stcode,$cand->candidate_residence_districtno);
					$ac= getacbyacno($cand->candidate_residence_stcode,$cand->candidate_residence_acno);
				   
				    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['nomDetails']=$nom;
                    $data['persoanlDetails']=$cand;
 					$data['partyd']=$partyd;
 					$data['symb']=$symb;
 					$data['st']=$st;
 					$data['ac']=$ac;
 					$data['dist']=$dist;
					return view($this->view_path.'.viewnomination', $data);	           
				}
				else{
					return redirect('/officer-login');
				}
			} 
	 public function multiplenomination()
        {      
          if(Auth::check()){ 
          	         $data  = [];
            		$user = Auth::user();
            		$d=$this->commonModel->getunewserbyuserid($user->id); 
 			 		$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
 			 		$seched=getschedulebyid($ele_details->ScheduleID);
	            if($seched['DATE_POLL']<date("Y-m-d")) {
	                //  \Session::flash('error_mes', 'Poll Date Completed');
	                //  return Redirect::to('/roac/listnomination');  
	                
	                }
		           	$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		           if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
		           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
		           }
				    
            if($cand_finalize_ro=='1')
                {
                 \Session::flash('finalize_mes', 'Candidate Nomination is Finalize');
                  return Redirect::to('/roac/listnomination');  
                }
             
             $list =$this->romodel->candidatelist($ele_details->ST_CODE,$ele_details->CONST_NO); 
             	$st=getstatebystatecode($ele_details->ST_CODE);
		 		$ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 
		       
		        $partyd=getallpartylist();
		        $symb=getsymbollist();
				$symb1=getsymboltypelist('T');
            
                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['lists']=$list;
                    $data['stcode']=$ele_details->ST_CODE;
                    $data['constno']=$ele_details->CONST_NO;
                    $data['st']=$st;
                    $data['ac']=$ac;
                    $data['partyd']=$partyd;
                    $data['symb']=$symb;
                    $data['symb1']=$symb1;
            return view($this->view_path.'.multiplenomination',$data); 

          }
          else {
                  return redirect('/officer-login');
                }
        } 
  public function insertmultiplenomination(request $request)
        {      
        if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
             $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		     $record = DB::table('m_election_details')->where('ST_CODE', $ele_details->ST_CODE)->where('CONST_NO', $ele_details->CONST_NO)
                    ->where('CONST_TYPE', 'AC')->first();  
            
            $this->validate( 
                $request, 
                [
                  'party_id' => 'required',
                  'symbol_id' => 'required',
                  'candidate_name'=>'required',
                 ],
                [ 
                  'party_id.required' => 'Please select party',
                  'symbol_id.required' => 'Please select symbol', 
                  'candidate_name.required'=>'Please select candidate name',
                ]
            ); 
 			$cid = $request->input('candidate_name');
 			$cnt = DB::table('candidate_nomination_detail')
                ->where('candidate_id','=',$cid)->where('application_status','<>','11')->get()->count();
			 
            $totalnom=$cnt;
            if($totalnom>=4)
              {
                \Session::flash('error_mes', 'Candidate multiple nominations can not be more than 4 ');
                return Redirect::to('/roac/multiplenomination'); 
              }
            $party=$this->commonModel->getparty($request->input('party_id'));
             
             
            if($party->PARTYTYPE=="S"){ 
             $partyDetails = DB::table('m_party')
                    ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
                    ->where('m_party.PARTYTYPE','=','S')
                    ->where('d_party.ST_CODE','=',$ele_details->ST_CODE)
                    ->where('m_party.CCODE','=',$party->CCODE)
                    ->select('m_party.*')->first();
            if(isset($partyDetails)){
                  $partytype = $party->PARTYTYPE;
             }
             else{
                 $partytype ='U';
             }
           } 
           else {
                 $partytype = $party->PARTYTYPE;
           }
             $g = DB::table('candidate_nomination_detail')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->get();

            $mslno=$g->max('cand_sl_no'); $mslno++;
             
            $randno = rand(1000,9999);
            $cid = $request->input('candidate_name');
            if($cid != ''){
                    $ccode = $d->st_code . $cid . $randno . date('Ymd');
                $candNomData = array(
                    'election_id'=>$ele_details->ELECTION_ID,
                    'party_id'=>$request->input('party_id'),
                    'cand_sl_no'=>$mslno,
                    'new_srno'=>$mslno,
                    'symbol_id'=>$request->input('symbol_id'),
                    'ac_no'=>$ele_details->CONST_NO,
                    'ST_CODE'=>$ele_details->ST_CODE,
                    'candidate_id'=>$cid,
                    'district_no'=>$d->dist_no,
                    'date_of_submit'=>date('Y-m-d'),
                    'qrcode'=>$ccode,
                    'created_by'=>$d->officername,
                    'created_at'=>date('Y-m-d h:i:s'),
                    'added_create_at'=>date('Y-m-d'),
                    'application_status'=>'1',
                    'cand_party_type'=> $partytype,
                    'scheduleid'=>$record->ScheduleID,
                    'election_type_id'=>$record->ELECTION_TYPEID,
                    'state_phase_no'=>$record->StatePHASE_NO,
                    'm_election_detail_ccode'=>$record->CCODE
                );
                $n = DB::table('candidate_nomination_detail')->insert($candNomData);
                $lastid=DB::getPdo()->lastInsertId();
 
                \Session::flash('success_mes', 'Candidate nomination successfully added');
                return Redirect::to('/roac/candidateaffidavit/'.$lastid);
            }
        }
        else{
            return redirect('/officer-login');
        }
    } 
    public function duplicate_drop(Request $request)
			{   
			if(Auth::check()){
		       $user = Auth::user();
		       $d=$this->commonModel->getunewserbyuserid($user->id);
		        
		        $candidate_id = $this->xssClean->clean_input($request->input('candidate_id')); 
                $nom_id = $this->xssClean->clean_input($request->input('nom_id')); 
                $marks = $this->xssClean->clean_input($request->input('marks'));
                if($marks==11){
                $st = array('application_status'=>$marks); 
			    $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			     
				$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'application_status','duplicate_drop',$marks,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		      	\Session::flash('success_mes', 'Candidate Duplicate drop successfully changed');
                  return Redirect::to('roac/listnomination');
		        }
		        else {
		        	\Session::flash('error_mes', 'please select duplicate status');
                  return Redirect::to('roac/listnomination');
		         }
		        }
	        else {
	              return redirect('/officer-login');
	        	  }
			}
	public function accepted_candidate(Request $request) {   
			if(Auth::check()){ 
				 $data  = [];
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
				$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
			    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			     if($check_finalize=='') { 
			     							$cand_finalize_ceo=0; $cand_finalize_ro=0;
			     						} else {
		           							$cand_finalize_ro=$check_finalize->finalized_ac;
		           						}
				   
	         
				$search="";
		   		$val=$this->romodel->checkfinalize_acbyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		        //$search = $request->input('search');
		    	$list=$this->romodel->acceptedcandidate($ele_details,$search);
                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['cand_finalize_ro']=$cand_finalize_ro;
                    $data['lists']=$list;
                    $data['checkval']=$val;
                    
		    	    return view($this->view_path.'.accepted-candidate', $data);
		         }
	        else {
	         	return Redirect::to('/officer-login');
	        	  }
			}
    public function finalaccepted(Request $request)
			{
				if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		       $this->validate(
                $request, 
                    [
                     'marks' => 'required',
                     ],
                    [
                     'marks.required' => 'Please select option',
                     ]);
		 		$candidate_id = $this->xssClean->clean_input($request->input('candidate_id')); 
                $nom_id = $this->xssClean->clean_input($request->input('nom_id')); 
                $marks = $this->xssClean->clean_input($request->input('marks'));
                $rec= DB::table('candidate_nomination_detail')->where('candidate_id',$candidate_id)->get();
              
                $nominatioID=array();
                foreach ($rec as  $value) {

                	$nominatioID[] = $value->nom_id;  	
                }
               
				if (($key = array_search($nom_id, $nominatioID)) !== false) {
				    unset($nominatioID[$key]);
				}
				  if($marks==1)
                {

                    $st = array('finalaccepted'=>$marks,); 
			       $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			       if(isset($nominatioID) && !empty($nominatioID )){
			       $stno = array('finalaccepted'=> 0);
			       $validno = DB::table('candidate_nomination_detail')->whereIn('nom_id', $nominatioID)->update($stno);
			        }

			        
			         $record = DB::table('candidate_criminal_report')->where('ST_CODE', $d->st_code)
                    ->where('ac_no', $d->ac_no)
                    ->where('candidate_id', $candidate_id)->first();
                    $check_ca_exit = DB::table('candidate_personal_detail')->where('is_criminal','=', '1')
                    ->where('candidate_id', $candidate_id)->first();
                    if(!empty($check_ca_exit)){
                 
		            if(!empty($record))
		            {       


		            	  $sts_ca = array('finalaccept_ca' =>'1','nom_id'=>$nom_id);
                           $ins = DB::table('candidate_criminal_report')->where('candidate_id', $candidate_id)->update($sts_ca);
		            }else{

                            $ins =DB::table('candidate_criminal_report')->insert(array(
							    'candidate_id'      =>$candidate_id, 
							    'nom_id'    =>$nom_id,
							    'st_code'      =>$d->st_code,
							    'ac_no'        =>$d->ac_no,
							    'election_id'      =>$d->election_id,
							    'check_1' =>'0',
							    'check_2' =>'0', 
							    'check_3' =>'0',
							    'status'=>'0',
							    'finalaccept_ca'=>'1',
							    'created_at'   => date('Y-m-d H:i:s', time()),

								));	



		            }
		        }//end if check_ca_exit





                }else{
                 
                   $st = array('finalaccepted'=>$marks,); 
			       $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);

	     		}

                 
               // $st = array('finalaccepted'=>$marks,); 
			  //  $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			    \Session::flash('ro_admin', 'Contesting Status successfully Changed' ); 
				 
				$this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',$nom_id,'finalaccepted','finalaccepted',$marks,request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));
		      	 \Session::flash('success_mes', 'Contesting Status successfully Changed' ); 
		    		return Redirect::to('roac/accepted-candidate');
		     
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
			}  
 	 



          

     /////////////////// Reject Status    //////////////////////



			public function statusvalidation_reject(Request $request)
			{
				//dd($request);
				if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		           $message=array();
				    $message['MobNo']= $user->officername ?? '';
				    $message['State_ID']= $user->st_code ?? '';
		            $message['applicationType']= 'WebApp';
		            $message['Module']= 'ENCORE';
		            $message['TransectionType']= 'Offline Nomination';
		            $message['TransectionAction']= 'Reject Nomination';
		       

		     $this->validate(
                $request, 
                    [
                    
                      'rejection_message' => 'required',
                     ],
                    [
                   
                     'rejection_message.required' => 'Please enter Message',
                     ]);
		 		
                $candidate_id = $this->xssClean->clean_input($request->input('candidate_id')); 
                $nom_id = $this->xssClean->clean_input($request->input('nom_id')); 
                $marks = $this->xssClean->clean_input($request->input('marks'));
                $rejection_message = $this->xssClean->clean_input($request->input('rejection_message'));
             
                $st = array('rejection_message'=>$rejection_message,'application_status'=>$marks,'affidavit_public'=>'yes'); 
                //dd($nom_id);
			    $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			    \Session::flash('ro_admin', 'Action successfully Change' ); 
				 
				// $this->commonModel->Audit_log_data('0',$d->id,'candidate_nomination_detail',
				// 						$nom_id,'application_status','receipt_generated',$marks,
				// 						request()->ip(),'NA','N/A','3','Complete',date("Y-m-d"));

				// if(config('public_config.nomination_log'))
		        //    {
		        //   $message['LogDescription']= 'Application Status Changed  NominationID: '.$nom_id. ' application_status '.$marks;
		        //   $message['TransectionStatus']= 'SUCCESS';

		        // LogNotification::LogInfo($message);
		        //   }

		      	 \Session::flash('success_mes', 'Candidate status successfully changed');
		    		return Redirect::to('roac/scrutiny-candidates');
		     
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
			}


			//////// End ////////


// Start CA Report

			  

/* Start Criminal Report */

					    public function contested_criminal_report(request $request)
			    {
			    	
			    	//dd($request->input('nomid'));
			    	if(Auth::check()){ 
			    		 $data  = [];
			            $user = Auth::user();
			            $d=$this->commonModel->getunewserbyuserid($user->id); 
			             $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
					     $record = DB::table('m_election_details')->where('ST_CODE', $ele_details->ST_CODE)->where('CONST_NO', $ele_details->CONST_NO)
                          ->where('CONST_TYPE', 'AC')->first(); 


                   $Schedule_details=$this->commonModel->getschedulebyid($record->ScheduleID);
                   $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$ele_details->ST_CODE)
            ->where('candidate_nomination_detail.ac_no','=', $ele_details->CONST_NO)
        
             ->where('candidate_nomination_detail.election_id','=',$ele_details->ELECTION_ID)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             //->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_3_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_1_date'
            )->get(); 
           
               $data['list']=$lists;
               $data['user_data']=$user;
               $data['nomidone'] = $request->input('nomid');
               $data['Schedule_details']=$Schedule_details;
              //  dd($data);
       
               }
                 return view($this->view_path.'.ca_report_list', $data);
               
              	

		}

		public function save_contestd_criminal_report(Request $request)
		{



			$cand_nom=$request->cand_nom;
             $exp=explode('_',$cand_nom);

            $record = DB::table('candidate_criminal_report')->where('ST_CODE', $request->stcode)
            ->where('ac_no', $request->ac_no)
            ->where('candidate_id', $exp[1])->get();

            $count=count($record);
            if($count> 0)
            {

               $st = array('check_1' =>$request->first,'check_2' =>$request->second,'check_3' =>$request->third,'check_1_date' =>$request->first_date,'check_2_date' =>$request->second_date,'check_3_date' =>$request->third_date,
                	'updated_at'=> date('Y-m-d H:i:s', time())); 
                //dd($nom_id);
			    $ins = DB::table('candidate_criminal_report')->where('candidate_id', $exp[1])->update($st);

                $record = DB::table('candidate_criminal_report')->where('candidate_id', $exp[1])->first();
               if($record->check_1=='1' && $record->check_2=='1' && $record->check_3=='1')
			{
				$status='1';
				 $sts = array('status' =>$status);
				$updt = DB::table('candidate_criminal_report')->where('candidate_id', $exp[1])->update($sts);
			}else{

                   $status='0';
				 $sts = array('status' =>$status);
				$updt = DB::table('candidate_criminal_report')->where('candidate_id', $exp[1])->update($sts);

			}


            }else{
			
			if($request->first=='1' && $request->second=='1' && $request->third=='1')
			{
				$status='1';
			}else{
				$status='0';
			}
        // $ins =DB::table('candidate_criminal_report')->insert(array(
	    // 'candidate_id'      =>$exp[1], 
	    // 'nom_id'    =>$exp[0],
	    // 'st_code'      =>$request->stcode,
	    // 'ac_no'        =>$request->ac_no,
	    // 'election_id'      =>$request->election_id,
	    // 'check_1' =>$request->first,
	    // 'check_2' =>$request->second, 
	    // 'check_3' =>$request->third,
	    // 'status'=>$status,
	    // 'created_at'   => date('Y-m-d H:i:s', time()),

		// ));	

    }

if($ins){
		return 1;
	}  else {
		return 0;
	} 
		}

		public function contesting_candidate_list_pdf(Request $request)
		{

           if(Auth::check()){ 
			    		 $data  = [];
			            $user = Auth::user();
			            $d=$this->commonModel->getunewserbyuserid($user->id); 
			             $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
					     $record = DB::table('m_election_details')->where('ST_CODE', $ele_details->ST_CODE)->where('CONST_NO', $ele_details->CONST_NO)
                          ->where('CONST_TYPE', 'AC')->first(); 


                   $Schedule_details=$this->commonModel->getschedulebyid($record->ScheduleID);
                   $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$ele_details->ST_CODE)
            ->where('candidate_nomination_detail.ac_no','=', $ele_details->CONST_NO)
        
             ->where('candidate_nomination_detail.election_id','=',$ele_details->ELECTION_ID)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_3_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_1_date'
            )->get(); 
           
               $data['list']=$lists;
               $data['user_data']=$user;
                $data['ro']=1;
          
               //dd($data);
       
        // $pdf = PDF::loadView('admin.ac.ro.criminalpdfview',compact('user',$user,'lists',$lists));
		      //      return $pdf->download('contesting-criminal-candidates.pdf');
		             $pdf = PDF::loadView('admin.ac.ro.criminalpdfview',compact(['user','lists']));
		            return $pdf->download('contesting-criminal-candidates.pdf');
               }         

       
        //AC NOMINATION FINALIZED PDF REPORT TRY CATCH ENDS HERE



		}

		public function contesting_candidate_list_excel(Request $request)
		{




              if(Auth::check()){ 
			    		 $data  = [];
			            $user = Auth::user();
			            $d=$this->commonModel->getunewserbyuserid($user->id); 
			             $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
					     $record = DB::table('m_election_details')->where('ST_CODE', $ele_details->ST_CODE)->where('CONST_NO', $ele_details->CONST_NO)
                          ->where('CONST_TYPE', 'AC')->first(); 


                   $Schedule_details=$this->commonModel->getschedulebyid($record->ScheduleID);
                   $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$ele_details->ST_CODE)
            ->where('candidate_nomination_detail.ac_no','=', $ele_details->CONST_NO)
        
             ->where('candidate_nomination_detail.election_id','=',$ele_details->ELECTION_ID)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_3_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_1_date'
            )->get(); 
           
               $data['list']=$lists;
               $data['user_data']=$user;
              // dd($lists);
               $k=1;
               $status='pending';
                $export_data = [];
                $headings[] = [];
                 
                     $export_data[] = ['SN', 'Candidate Name', 'NominationID','State','AC Name' ,'1st Publication','1st Publication Date', '2nd Publication','2nd Publication Date','3rd Publication','3rd Publication Date','Publication Status'];
                 foreach ($lists as $lis) {  
              
                   if( $lis->check_1==1){ $check1='Yes';}else{$check1='No';}
                  if( $lis->check_2==1){ $check2='Yes';}else{$check2='No';}
                  if( $lis->check_3==1){ $check3='Yes';}else{$check3='No';}

                   if(!empty($lis->check_1_date)){$check1_date=date('d-m-Y',strtotime($lis->check_1_date));}else{$check1_date="N/A"; }
                if(!empty($lis->check_2_date)){$check2_date=date('d-m-Y',strtotime($lis->check_2_date));}else{$check2_date="N/A"; }
                if(!empty($lis->check_3_date) ){$check3_date=date('d-m-Y',strtotime($lis->check_3_date));}else{$check3_date="N/A"; }
                
                   if($lis->check_1 == 1 && $lis->check_2 == 1 && $lis->check_3 == 1)
                  {
                    $status_is="Completed";
                  }else{
                    $status_is="Pending";
                  }
                     $st=getstatebystatecode($lis->st_code); 
                  $ac=getacname($lis->st_code,$lis->ac_no);
                if(isset($st))   $st_name=$st->ST_NAME; 
                if(isset($ac))  $ac_name=$ac->AC_NAME;  

                 $export_data[] = [
                                $k++,
                                $lis->cand_name,
                                $lis->nom_id,
                                $st_name,
                                $ac_name,
                                $check1,
                                $check1_date,
                                $check2,
                                $check2_date,
                                $check3,
                                $check3_date,
                                $status_is,
                                
                        ];
                   
                }
                    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], "Nomination_report"));
                    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

}


		}
			



			

/* End Criminal Report */











}  // end class  
