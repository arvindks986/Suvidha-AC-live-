<?php  
		namespace App\Http\Controllers\Admin\CandNomination;
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
		use App\adminmodel\ROPCModel;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		use Illuminate\Support\Facades\Crypt;

 
class FormreportController extends Controller
{
    //
   public function __construct()
        {   
			$this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->middleware('ro');
			$this->middleware('clean_url');
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->romodel = new ROPCModel();
			$this->xssClean = new xssClean;
			$this->sym = new SymbolMaster();
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

    public function form_3a_report(request $request)
	    {      
	       if(!Auth::check()){ 
        		return redirect('/officer-login');
        	}
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
                
                $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
				$state=getstatebystatecode($ele_details->ST_CODE);
             $cur_date = $request->input('cur_date');  

		    if(isset($ele_details))
		            $ele_seched=getschedulebyid($ele_details->ScheduleID);
		            else 
		            	$ele_seched='';

		      $date_str = array();

           $st='';
          $filter = [
					'CONST_TYPE' 	=>'PC',
					'ST_CODE' 	=>$ele_details->ST_CODE,
					'ELECTION_ID'	=> $ele_details->ELECTION_ID,
					'CONST_NO'=>$ele_details->CONST_NO
			     ]; 
               
            
		    if(isset($ele_seched)){
		    		$st_date=$ele_seched['DT_ISS_NOM'];  $lt_date=$ele_seched['LDT_IS_NOM'];
		    		for($i=$st_date; $i<=$lt_date; $i++)
		    			{
		    			 $date_str[]=$i;  
		    			}
		    	}
		     $newdate=base64_encode($cur_date); 
            

		      $result= $this->romodel->form3areportsdetails($filter,$cur_date);  
		            $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['date_str']=$date_str;
                    $data['ele_seched']=$ele_seched;
                    $data['result']=$result;
		            $data['state_name']=$state->ST_NAME;
                    $data['const_name']=$pc->PC_NAME;
                    $data['cur_date']=$cur_date;
                    $data['newdate']=$newdate;
	     return view('admin.pc.nomination.form3aview', $data); 

	    }  // end index function  
 public function download_form_3a_report($newdate)
	    {      
          

	       if(!Auth::check()){ 
        		return redirect('/officer-login');
        	}
        	if($newdate=='')
	        	{
                 return redirect('/ropc/form-3A-report');
	        	}  //
	       $cur_date=base64_decode($newdate);
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
                
                $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
				$state=getstatebystatecode($ele_details->ST_CODE);

			$filter = [
					'CONST_TYPE' 	=>'PC',
					'ST_CODE' 	=>$ele_details->ST_CODE,
					'ELECTION_ID'	=> $ele_details->ELECTION_ID,
					'CONST_NO'=>$ele_details->CONST_NO
			     ]; 
               
           
		      $result= $this->romodel->form3areportsdetails($filter,$cur_date);  

		            $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['result']=$result;
		            $data['state_name']=$state->ST_NAME;
                    $data['const_name']=$pc->PC_NAME;
                    $data['cur_date']=$cur_date;
             

	     view()->share($data);
           $pdf = MPDF::loadView('admin.pc.nomination.downloadform3Aview',compact($data));
           return $pdf->download('downloadform3Aview.pdf');
     
           return view('downloadform3Aview'); 

	    }  // end index function  
	public function form_4_report()
	    {     
	     if(!Auth::check()){ 
        		return redirect('/officer-login');
        	}
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
               
                $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
				$state=getstatebystatecode($ele_details->ST_CODE);
		    $a='N'; $a1='S';  $a2='U'; $a3='0'; $a4='Z';

		    $candn =$this->romodel->form4reportsdetails($ele_details,$a,$a);   
    	    $cands = $this->romodel->form4reportsdetails($ele_details,$a1,$a1);  
            $candu =$this->romodel->form4reportsdetails($ele_details,$a2,$a3); 
            $candz =$this->romodel->form4reportsdetails($ele_details,$a4,$a4); 
                   
                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['candn']=$candn;
                    $data['cands']=$cands;
                    $data['candu']=$candu;
                    $data['candz']=$candz;
                    $data['state_name']=$state->ST_NAME;
                    $data['const_name']=$pc->PC_NAME;
                     
           // echo "<pre>  N"; print_r($candn); echo "</pre>"; echo "<pre> S";print_r($cands); echo "</pre>"; echo "<pre> U";
           // print_r($candu);  echo "</pre>"; echo "<pre> Z"; print_r($candz); echo "</pre>";  die;
		 
		    return view('admin.pc.nomination.form4view', $data);	             
	        
	    }  // end index function  	 
	public function download_form_4_report()
	    {   
	    	if(!Auth::check()){ 
        		return redirect('/officer-login');
        	}  
	        $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
               
                $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
				$state=getstatebystatecode($ele_details->ST_CODE);
		    $a='N'; $a1='S';  $a2='U'; $a3='0'; $a4='Z';

		    $candn =$this->romodel->form4reportsdetails($ele_details,$a,$a);   
    	    $cands = $this->romodel->form4reportsdetails($ele_details,$a1,$a1);  
            $candu =$this->romodel->form4reportsdetails($ele_details,$a2,$a3); 
            $candz =$this->romodel->form4reportsdetails($ele_details,$a4,$a4); 
                   
                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['candn']=$candn;
                    $data['cands']=$cands;
                    $data['candu']=$candu;
                    $data['candz']=$candz;
                    $data['state_name']=$state->ST_NAME;
                    $data['const_name']=$pc->PC_NAME;
                     
           // echo "<pre>  N"; print_r($candn); echo "</pre>"; echo "<pre> S";print_r($cands); echo "</pre>"; echo "<pre> U";
           //  print_r($candu);  echo "</pre>"; echo "<pre> Z"; print_r($candz); echo "</pre>";  die;
		 
		   view()->share($data);
           $pdf = MPDF::loadView('admin.pc.nomination.downloadform4view',compact($data));
           return $pdf->download('downloadform4view.pdf');
     
           return view('downloadform4view');	             
	        
	    }  // end index function  download_form_4_report 
}  // end class  //accepted_candidate  
