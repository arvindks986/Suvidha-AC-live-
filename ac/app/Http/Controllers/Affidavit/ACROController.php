<?php  
		namespace App\Http\Controllers\Affidavit;
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
		use App\models\Affidavit\AffCandDetail;
		use App\Helpers\SmsgatewayHelper;
		use App\adminmodel\ACROModel;
		use App\Classes\xssClean;
		use Illuminate\Support\Facades\Crypt; 
class ACROController extends Controller
{
	
	public function __construct(){
       $this->middleware(function (Request $request, $next) {

           $user = Auth::user();
           switch ($user->role_id) {
               case '19':
                   $this->middleware('ro');
				   
                   break;
               default:
				   $this->middleware('usersession');
           }
           return $next($request);
       });
	   
        $this->commonModel = new commonModel();
		$this->CandidateModel = new CandidateModel();
		$this->romodel = new ACROModel();
        $this->xssClean = new xssClean;
    }
	
  
		    
    public function index(request $request){
		
         
        	$data  = [];
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
            $seched=getschedulebyid($ele_details->ScheduleID);
             
	        $cand_finalize_ro =0;
	        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
	           if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
	           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
	           }  
            $val=$this->romodel->checkfinalize_acbyro($ele_details->ST_CODE,$ele_details->CONST_NO,'AC');
            
          
            $list = AffCandDetail::select()
					->where('st_code',$ele_details->ST_CODE)
					->where('ac_no',$ele_details->CONST_NO)
					->where('finalized','1')
					->get();
                   
				   
                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['checkval']=$val;
                    $data['lists']=$list; 
                    $data['cand_finalize_ro']=$cand_finalize_ro ;
            return view('affidavit.ro.list',$data);
         
    } 	 
    
}  // end class  
