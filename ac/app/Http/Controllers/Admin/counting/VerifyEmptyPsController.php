<?php  
namespace App\Http\Controllers\Admin\counting;
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
use \MPDF;
use \PDF;
use App\commonModel;
use Illuminate\Support\Facades\Schema;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use App\models\Counting\BoothCountingModel;
use App\adminmodel\ACCountingModel;
use App\models\Counting\UsercountingModel;
use App\models\Counting\PostalCountingModel; 
use App\models\Counting\CountingResultsPublishModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;  
class VerifyEmptyPsController  extends Controller
{

    public $base    = 'roac';
    public $folder  = 'counting';
    public $action    = 'roac/counting/';
    public $view_path = "admin.counting.ro";

    public function __construct()
    {
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->boothcounting=new BoothCountingModel;
        $this->users=new UsercountingModel;
        $this->CountingModel = new ACCountingModel();
        $this->postal = new PostalCountingModel();
        if(!Auth::check()){ 
          return redirect('/officer-login');
      }
  }

  protected function guard(){

    return Auth::guard('admin');
}
 
	function checkEmptyPs(Request $request){
				 
	   $data  = [];
	   $data['round_id'] = '';
	   $data['table_id'] = '';
	   $data['user_data'] = '';
	   $data['ele_details'] = '';
	   $data['new_table'] = '';
	   $data['total_no_ps'] = '';
	   $data['total_no_tables'] = '';
	   $data['complete_rounds'] =0;
	   $data['current_rounds']  =1;
	   $data['complete_table']  ='';
	   $data['master_table'] = '';
	   $data['master_data'] = '';
	   $data['counting_pstabledeails'] = '';
	   $data['counting_ps_evmvote']=array();
	   $data['scheduled_round']='';
		
	   $user = Auth::user();
		
	   $d=$this->commonModel->getunewserbyuserid($user->id);
	   $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		
	   $new_table=strtolower("counting_ps_".$d->st_code);
	   $new_table_master=strtolower("counting_master_".$d->st_code);

	   $filter = [
		'st_code' => $ele_details->ST_CODE,
		'pc_no'   =>'',
		'election_id' => $ele_details->ELECTION_ID,
		'ac_no'   =>$d->ac_no,
		'table'   =>"counting_master_".strtolower($ele_details->ST_CODE), 
	];
		
		$round_details=$this->postal->roundsechudle($filter);
		
		
		$filter_postal_votes = [
	       	    'st_code' 	=> $ele_details->ST_CODE,
	       	    'ac_no' 	=> $ele_details->CONST_NO,
	       	    'election_id' 	=> $ele_details->ELECTION_ID,	 
	       	];
		$postal_finalized=postal_votes_finalized($filter_postal_votes);	
		$evm_finalized= evm_votes_finalized($filter_postal_votes);

		$data['evm_finalized']   = $evm_finalized; 
		$data['postal_finalized']   = $postal_finalized; 
		
		$c_data=DB::table($new_table_master)->select('complete_round','finalized_round')
        					->where('ac_no', $d->ac_no)
        					->where('election_id',$ele_details->ELECTION_ID)
        					->orderBy('id')->first();

		if(!isset($round_details)) {
		  if($d->role_id=="19") { 
					\Session::flash('success_admin', 'Round Schedule Not Created! Please ask  RO to Create roundschedule');
					return Redirect::to('roac/counting/round-schedule-details');
				  }
			elseif($d->role_id=="36") {   
				 \Session::flash('error_mes', 'Round Schedule Not Created! Please Create to roundschedule');
					return Redirect::to('/roac/dashboard');
			}
		}   
		
		$complete_round=0; $finalized_round=0;
		if(isset($c_data)){
			$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
		}
		
		if($round_details->scheduled_round != $complete_round)
	    {
			\Session::flash('error_mes', 'All rounds not completed, Please Complete your all rounds first.');
		    return Redirect::to('/roac/counting/polling-station-wisevote-entry');
	    }
		
		
	$assigntable=$this->users->getallassigntable($filter);
		$checkuser=$this->boothcounting->checkmasterrecords($filter);
		if(!isset($checkuser)){
		   \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
		   return Redirect::to('roac/counting/prepare-counting-data');
		}
	//dd($request->round_id);
	 $ctype=$request->ctype;  
	//echo "==".$ctype;
	 if(Auth::user()->role_id==19)
	if(!empty($request->round_id))$round_id=base64_decode($request->round_id);  else $round_id=$checkuser->complete_round+1;
	else
	  $round_id=$checkuser->complete_round+1;

	if(!empty($request->table_id))$table_id=base64_decode($request->table_id);  else $table_id='';
	if($round_id>$round_details->scheduled_round) { $round_id=$round_details->scheduled_round; 
	  $data['current_rounds']=$round_details->scheduled_round; } 
	//   echo $data['current_rounds']; 
	// dd($round_id);
	$filter_table = [
		'st_code'       => '',
		'pc_no'         =>'',
		'election_id'   => $ele_details->ELECTION_ID, 
		'ac_no'         =>$d->ac_no,
		'table_name'    =>$new_table,
		'round_id'      =>$round_id,
	];

	
	   $st=getstatebystatecode($ele_details->ST_CODE);  
	   $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 
	   
	   
		$sqllist = DB::select( DB::raw("SELECT ps.* FROM `polling_station` AS ps
				LEFT JOIN ".$new_table." AS cps ON ps.`AC_NO` = cps.ac_no AND ps.ps_no = cps.ps_no
				WHERE cps.ps_no IS NULL AND ps.`ST_CODE` = '$ele_details->ST_CODE' AND ps.ac_no = '$ele_details->CONST_NO'") );
		//dd($sqllist);
		
		$margin_sql = DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();
		
		$rejected_vote_sql = DB::table('round_master')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();

		$vote_margin = 0;
		$rejected_votes = 0;
		$postal_total_votes = 0;
		
		if($margin_sql){
			$vote_margin = $margin_sql->margin;
		}
		
		if($rejected_vote_sql){
			$rejected_votes = $rejected_vote_sql->rejected_votes;
			$postal_total_votes = $rejected_vote_sql->postal_total_votes;
		}

		if($postal_total_votes==0){
			\Session::flash('error_mes', 'Postal Votes not entered. Please enter postal ballot votes here.');
				return Redirect::to('/roac/counting/bpostal-data-entry');	
		 }
		
		
		$data['empty_ps_list'] = $sqllist;
		$data['votes_margin'] = $vote_margin;
		$data['votes_margin_data'] = $margin_sql;
		$data['rejected_votes'] = $rejected_votes;
		
		//dd($margin_sql);
	 
		   $data['st_name']   = $st->ST_NAME;
		   $data['ac_name']   = $ac->AC_NAME;   
		  $data['round_id']   = $round_id;
		  $data['round']      = $round_id;
		  $data['table_id']   = $table_id;
		  $data['st_code']    = $ele_details->ST_CODE;
		  $data['ac_no']      = $d->ac_no;
		  $data['user_data']  = $d;
		  $data['ele_details'] = $ele_details;
		  $data['new_table']  = $new_table;
		  $data['ctype']  = $ctype;
 
		return view($this->view_path.'.check-empty-polling-station', $data);
	}
	
	
	function finalizeEmptyPs(Request $request){
		$user = Auth::user(); 

		 $d=$this->commonModel->getunewserbyuserid($user->id);
		 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');


          $ST_CODE =$ele_details->ST_CODE;  
          $CONST_TYPE =$ele_details->CONST_TYPE;   //$this->xssClean->clean_input($request->input('CONST_TYPE'));
          $CONST_NO = $ele_details->CONST_NO;  //$this->xssClean->clean_input($request->input('CONST_NO'));
          $ELECTION_ID=$ele_details->ELECTION_ID;  //$this->xssClean->clean_input($request->input('ELECTION_ID'));
		  
		DB::beginTransaction();
        try{
			
			//dd($request->all());
			if($request->vfps){
				$updb = DB::table("counting_finalized_ac")->where("st_code",$ST_CODE)->where("ac_no",$CONST_NO)->where("election_id",$ELECTION_ID)->update(["verify_empty_ps"=>1,"verication_date_ps"=>date('Y-m-d H:i:s')]);
				DB::commit();
				if($updb){
					//\Session::flash('success_mes', 'Pre-finalize successfully.you can finalize evm votes now.');
					return Redirect::to('roac/counting/evm-votes-finalized');
				}else{
					\Session::flash('error_mes', 'Please try again.');
					return Redirect::back();
				}
			}
			
		}catch(\Exception $e){
		   DB::rollback();
		   \Session::flash('error_mes', 'Please try again Data  do not inserted');
		   return Redirect::back();
		}
	}

}  // end class results-declaration    
