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
		 
		use App\Helpers\SmsgatewayHelper;
		 
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
	 	use Illuminate\Support\Facades\Crypt;
        use App\adminmodel\Pollday;
 
class PollDayController extends Controller
{
    //
   public function __construct()
        {   
			$this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->middleware('ro');
			$this->commonModel = new commonModel();
			$this->xssClean = new xssClean;
			$this->pollday = new Pollday;
			 
		}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
 	protected function guard(){
        return Auth::guard('admin');
    	}

    public function index()
	    {     
	    if(Auth::check()){
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		   $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
        $seched=getschedulebyid($ele_details->ScheduleID);
         // dd($seched);
          if($seched->DATE_POLL!="2019-04-11"){
            return Redirect::to('/roac/dashboard'); 
          }
		     $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
           if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
           }
          $list = DB::table('pd_schedulemaster')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)
        					->where('const_type',$ele_details->CONST_TYPE)->first();   
           if(isset($list)) $pd_scheduleid=$list->pd_scheduleid; else $pd_scheduleid='';	
           				
            return view('admin.ac.ro.voting.create-schedule', ['user_data' => $d,'ele_details'=>$ele_details,'cand_finalize_ceo' =>$cand_finalize_ceo,'cand_finalize_ro' =>$cand_finalize_ro,'pd_scheduleid'=>$pd_scheduleid,'list'=>$list]);	           
	        }
	        else {
	              return redirect('/officer-login');
	        	  }
	    }  // end index function

	 public function veryfy_schedule(Request $request)
        {
         if(Auth::check()){
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		    $m_election=getelectiondetailbystcode($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		   
		   
           $this->validate( 
                    $request, 
                    [
                      'stdate' => 'required',
                      'eddate' => 'required',
                      ],
                    [
                      'stdate.required' => 'Please enter a Valid value',
                      'eddate.required' => 'Please enter a Valid value',
                    ]); 
                
                  $stdate =$request->input('stdate');
                  $eddate = $request->input('eddate');
                  $insstdate = $request->input('stdate');
                  $inseddate =$request->input('eddate');
                  
                  $pd_scheduleid = $this->xssClean->clean_input($request->input('pd_scheduleid'));
                   
                  $month = date('m'); 
                  $year = date('Y'); 
                  $check = DB::table('pd_schedulemaster')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)
        					->where('const_type',$ele_details->CONST_TYPE)->first();
                  
                if(isset($check))
	                {
	                $sche_data = array('year'=>$year,'month'=>$month,'start_time'=>$insstdate,'end_time'=>$inseddate,'added_update_at'=>date("Y-m-d"),
                            'updated_at'=>date("Y-m-d h:m:s"),'updated_by'=>$d->officername);   
                     
                    $this->commonModel->updatedata('pd_schedulemaster','pd_scheduleid',$pd_scheduleid,$sche_data);
		               \Session::flash('success_mes', 'You have Successfully updated');
	                }
	             else{
                 $sche_data = array('st_code'=>$ele_details->ST_CODE,'district_no'=>$d->dist_no,'ac_no'=>$ele_details->CONST_NO,
                 	'const_type'=>$ele_details->CONST_TYPE,'schedule_id'=>$ele_details->ScheduleID,'state_phase_no'=>$m_election->StatePHASE_NO,
                    'm_election_detail_ccode'=>$m_election->CCODE,'electionid'=>$ele_details->ELECTION_ID,'election_type_id'=>$ele_details->ELECTION_TYPEID,
                            'year'=>$year,'month'=>$month,'start_time'=>$insstdate,'end_time'=>$inseddate,'added_create_at'=>date("Y-m-d"),
                            'created_at'=>date("Y-m-d h:m:s"),'created_by'=>$d->officername);   
                   
                      $n = DB::table('pd_schedulemaster')->insert($sche_data);
                      $pid=DB::getPdo()->lastInsertId();
                    
                  
                    	$rec = array('pd_scheduleid'=>$pid,'st_code'=>$ele_details->ST_CODE,'ac_no'=>$ele_details->CONST_NO,'added_create_at'=>date("Y-m-d"),'created_at'=>date("Y-m-d h:m:s"),'created_by'=>$d->officername); 
                    $check_ac = DB::table('pd_scheduledetail')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->first();
        		     if(!isset($check_ac))
                         $n = DB::table('pd_scheduledetail')->insert($rec);
                   

                      \Session::flash('success_mes', 'You have Successfully Added. '); 
                  }
                 
                    return Redirect::to('roac/voting/list-schedule');
                
      
            }
	        else {
	              return redirect('/officer-login');
	        	  }
        } 	 
	public function list_schedule(Request $request)
		    {
		     if(Auth::check()){
		        $user = Auth::user();
		        $d=$this->commonModel->getunewserbyuserid($user->id);
		   		$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
           $seched=getschedulebyid($ele_details->ScheduleID);
          //  dd($seched);
          // if($seched->DATE_POLL!="2019-04-11"){
          //   return Redirect::to('/roac/dashboard'); 
          // }
		     $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
           if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
           }
           $droplist=$this->commonModel->selectAll('pd_schedule_round','id','ASC');
           $lists = DB::table('pd_scheduledetail')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->get();
           $master = DB::table('pd_schedulemaster')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)
                  ->where('const_type',$ele_details->CONST_TYPE)->first();
            //print_r( $lists);   //print_r( $master);      
            $filter_election = [
                'st_code'=>$ele_details->ST_CODE,'const_no'=>$ele_details->CONST_NO,'year'=>'2019',
           ];
			      $total_round=$this->pollday->get_total_roundnewac($ele_details->ST_CODE,$d->ac_no);
            
                $total_elector_male   = 0;
                $total_elector_female = 0;
                $total_elector_other  = 0;
                $total_elector_total  = 0;
                $total_electors=$this->pollday->get_elector_totalac($ele_details->ST_CODE,$ele_details->CONST_NO,'2019');

              //print_r( $total_round);
               if(isset($total_electors) && $total_electors){
                $total_elector_male   = $total_electors['electors_male'];
                $total_elector_female = $total_electors['electors_female'];
                $total_elector_other  = $total_electors['electors_other'];
                $total_elector_total  = $total_electors['electors_total'];
                }      
              // print_r($total_round->total); print_r($total_round->total_male); print_r($total_round->total_female); print_r($total_round->total_other); print_r($total_electors['electors_total']); echo "<br><br><br>"; dd($total_electors);
              
             if(isset($total_round)){
             if($total_elector_total!=0) $totalturnout_per=(($total_round->total/$total_elector_total)*100); else  $totalturnout_per=0;
             if($total_elector_male!=0) $maleturnout_per=(($total_round->total_male/$total_elector_male)*100); else  $maleturnout_per=0;
             if($total_elector_female!=0) $femaleturnout_per=(($total_round->total_female/$total_elector_female)*100);else  $femaleturnout_per=0;
             if($total_elector_other!=0) $othersturnout_per=(($total_round->total_other/$total_elector_other)*100); else  $othersturnout_per=0;
             }
             else{
                $totalturnout_per=0; 
                $maleturnout_per=0; 
                $femaleturnout_per=0;  
                $othersturnout_per=0; 
             }
                $totalturnout_per=round( $totalturnout_per,2);
                $maleturnout_per=round( $maleturnout_per,2);
                $femaleturnout_per=round($femaleturnout_per,2);  
                $othersturnout_per=round($othersturnout_per,2); 

           $lists1 = DB::table('elector_details')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->get(); 
           
	        return view('admin.ac.ro.voting.list-schedule', ['user_data' => $d,'ele_details'=>$ele_details,'cand_finalize_ceo' =>$cand_finalize_ceo,'cand_finalize_ro' =>$cand_finalize_ro, 'lists'=>$lists,   'droplist'=>$droplist,'totalturnout_per'=>$totalturnout_per,'maleturnout_per'=>$maleturnout_per,'femaleturnout_per'=>$femaleturnout_per,'othersturnout_per'=>$othersturnout_per,'master'=>$master]);
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
		     
		    }  // end  function   
    public function schedule_entry($round='', Request $request)
		    {     
	    if(Auth::check()){
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		     $cyear='2019';
		   $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		   $seched=getschedulebyid($ele_details->ScheduleID);
            $round= base64_decode($round);
            $round1= base64_encode($round);
         // dd($seched);
          // if($seched->DATE_POLL!="2019-04-11"){
          //   return Redirect::to('/roac/dashboard'); 
          // }
           $droplist=$this->commonModel->selectAll('pd_schedule_round','id','ASC');
          
            $lists = DB::table('pd_scheduledetail')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$d->ac_no)->first();
           
            $master = DB::table('pd_schedulemaster')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$d->ac_no)
                  ->where('const_type',$ele_details->CONST_TYPE)->first();
           $ele=getcdacelectorsdetails($ele_details->ST_CODE,$d->ac_no, '2019');
           $filter_election = [
                'st_code'=>$ele_details->ST_CODE,'const_no'=>$d->ac_no,'year'=>'2019',
           ];
          $total_round=$this->pollday->get_total_roundnewac($ele_details->ST_CODE,$d->ac_no, $cyear);
        
          $total_male   = 0;
          $total_female = 0;
          $total_other  = 0;
          $total_total  = 0;
          if(isset($total_round) && $total_round){
            $total_male   = $total_round->total_male;
            $total_female = $total_round->total_female;
            $total_other  = $total_round->total_other;
            $total_total  = $total_round->total;
          }

         
          $total_elector_male   = 0;
          $total_elector_female = 0;
          $total_elector_other  = 0;
          $total_elector_total  = 0;
          $total_electors = $this->pollday->get_elector_totalac($ele_details->ST_CODE,$d->ac_no,$cyear);

        //print_r( $total_round);
         if(isset($total_electors) && $total_electors){
          $total_elector_male   = $total_electors['electors_male'];
          $total_elector_female = $total_electors['electors_female'];
          $total_elector_other  = $total_electors['electors_other'];
          $total_elector_total  = $total_electors['electors_total'];
          }      
            

          $totalturnout_per = 0;
          $maleturnout_per  = 0;
          $femaleturnout_per  = 0;
          $othersturnout_per = 0;

          if($total_male > 0 && $total_elector_male >0){
            $maleturnout_per = round((($total_male/$total_elector_male)*100),2);
          }
           if($total_female > 0 && $total_elector_female >0){
            $femaleturnout_per = round((($total_female/$total_elector_female)*100),2);
          }
           if($total_other > 0 && $total_elector_other >0){
            $othersturnout_per = round((($total_other/$total_elector_other)*100),2);
          }
           if($total_total > 0 && $total_elector_total >0){
            $totalturnout_per = round((($total_total/$total_elector_total)*100),2);
          }
 
             
           if($round!='')
             {  
              $mr=$round."_voter_male";
              $fr=$round."_voter_female";
              $or=$round."_voter_other";
              $tr=$round."_voter_total";
              $m=$lists->$mr;
               $f=$lists->$fr;
               $o=$lists->$or;
               $t=$lists->$tr;
             }
            else{
              $m='';
              $f='';
              $o='';
              $t='';
            }  			
            return view('admin.ac.ro.voting.aro-schedule-entry', ['user_data' => $d,'ele_details'=>$ele_details, 'lists'=>$lists, 'droplist'=>$droplist, 'round'=>$round, 'm'=>$m, 'f'=>$f, 'o'=>$o,'t'=>$t,'ele'=>$ele,'totalturnout_per'=>$totalturnout_per,'maleturnout_per'=>$maleturnout_per,'femaleturnout_per'=>$femaleturnout_per,'othersturnout_per'=>$othersturnout_per,'master'=>$master,'round'=>$round]);	           
	        }
	        else {
	              return redirect('/officer-login');
	        	  }
	    }  // end   function

	 
	 
	public function aro_schedule_entry(Request $request)
			{
		    if(Auth::check()){
		          $user = Auth::user();
		          $d=$this->commonModel->getunewserbyuserid($user->id);
		      $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
           $cyear='2019';
		     $validator = \Validator::make(
                $request->all(), 
                    [
                      'round' => 'required',
                      'malevoter' => 'required|numeric|integer|digits_between:0,15000000',
                      'femalevoter' => 'required|numeric|integer|digits_between:0,15000000',
                      'othervoter' => 'required|numeric|integer|digits_between:0,15000000',
                      'totalvoter' => 'required|numeric|integer|digits_between:0,15000000',
                     ],
                    [
                      'round.required' => 'Please select round', 
                      'malevoter.numeric' => 'Please enter numeric value',
                      'malevoter.required' => 'Please enter voter',
                      'femalevoter.numeric' => 'Please enter numeric value',
                      'femalevoter.required' => 'Please enter voter',
                      'othervoter.numeric' => 'Please enter numeric value',
                      'othervoter.required' => 'Please enter voter',
                      'totalvoter.numeric' => 'Please enter numeric value',
                      'totalvoter.required' => 'Please enter voter',

                      'malevoter.integer' => 'Please enter numeric value',
                      'femalevoter.integer' => 'Please enter numeric value',
                      'othervoter.integer' => 'Please enter numeric value',
                      'totalvoter.integer' => 'Please enter numeric value',
                      'malevoter.digits_between' => 'Please enter valid value',
                      'femalevoter.digits_between' => 'Please enter valid value',
                      'othervoter.digits_between' => 'Please enter valid value',
                      'totalvoter.digits_between' => 'Please enter valid value',
                     ]);
		 	 	
		 	 	if($validator->fails()){
		 	 		return \Redirect::back()->withInput($request->all())->withErrors($validator);
		 	 	}
              $ele=getcdacelectorsdetails($ele_details->ST_CODE,$d->ac_no,$cyear);
                
                if(isset($ele)){
                  $elector_total=$ele->electors_total;
                  $elector_male=$ele->electors_male;
                  $elector_female=$ele->electors_female;
                  $elector_others=$ele->electors_other;
                }
                else { 
                    $elector_total=0;
                    $elector_male=0;
                    $elector_female=0;
                    $elector_others=0;

                 }

                $id =  $request->input('id'); 
                $round= $this->xssClean->clean_input($request->input('round')); 
                $malevoter = $this->xssClean->clean_input($request->input('malevoter'));
                $femalevoter = $this->xssClean->clean_input($request->input('femalevoter'));
                $othervoter = $this->xssClean->clean_input($request->input('othervoter'));
                $totalvoter = $this->xssClean->clean_input($request->input('totalvoter'));
                $newround= $this->xssClean->clean_input($request->input('newround')); 

                $net=$malevoter+ $femalevoter+ $othervoter;
                if($round!=$newround)
                  {

                         \Session::flash('error_mes', 'Rounds mismatch. '); 
                         return \Redirect::back()->withInput($request->all())->withErrors($validator);
                         return Redirect::to('roac/voting/schedule-entry/'.$round);  
                  }
               if($malevoter >$elector_male)
                  {

                         \Session::flash('error_mes', 'Voter turnout details mismatch. '); 
                         return \Redirect::back()->withInput($request->all())->withErrors($validator);
                         return Redirect::to('roac/voting/schedule-entry/'.$round);    
                  }
                  if($femalevoter >$elector_female)
                  {

                         \Session::flash('error_mes', 'Voter turnout details mismatch. '); 
                         return \Redirect::back()->withInput($request->all())->withErrors($validator);
                         return Redirect::to('roac/voting/schedule-entry/'.$round);   
                  }
                if($othervoter >$elector_others)
                  {

                         \Session::flash('error_mes', 'Voter turnout details mismatch. '); 
                         return \Redirect::back()->withInput($request->all())->withErrors($validator);
                        return Redirect::to('roac/voting/schedule-entry/'.$round);    
                  }
                if($elector_total<$totalvoter)
                  {

                         \Session::flash('error_mes', 'Voter more than Currents Electors. '); 
                         return \Redirect::back()->withInput($request->all())->withErrors($validator);
                         return Redirect::to('roac/voting/schedule-entry/'.$round);    
                  }
                $m= $round."_voter_male";
                $f= $round."_voter_female";
                $o= $round."_voter_other";
                $t= $round."_voter_total";
                
                $st = array($m=>$malevoter,$f=>$femalevoter,$o=>$othervoter,$t=>$totalvoter,'updated_at'=>date("Y-m-d h:m:s"),'added_update_at'=>date("Y-m-d"),'updated_by'=>$d->officername,'total_male'=>$malevoter,'total_female'=>$femalevoter,'total_other'=>$othervoter,'total'=>$totalvoter); 
                 
			    $i = DB::table('pd_scheduledetail')->where('id', $id)->update($st);
			      
		      	 \Session::flash('success_mes', 'Voter Turnout successfully added');
		    		return Redirect::to('roac/voting/schedule-entry');
		     
		         }
	        else {
	              return redirect('/officer-login');
	        	  }
			}
	 
	 	
}  // end class  //accepted_candidate  
