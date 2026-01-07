<?php  
		namespace App\Http\Controllers\Admin;
		use Illuminate\Http\Request;
		use App\Http\Controllers\Controller;
		use Session;
		use Illuminate\Support\Facades\Auth;
		use Illuminate\Support\Facades\Input;
		use Illuminate\Support\Facades\Redirect;
		//use Illuminate\Database\MySqlConnection;
		use Carbon\Carbon;
		use DB;
		use Illuminate\Support\Facades\Hash;
		use Validator;
		use Config;
		use \PDF;
		use App\commonModel;
		use App\adminmodel\CountingModel; 
		use Illuminate\Support\Facades\Schema;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
class CountingController extends Controller
{
   public function __construct()
        {
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->CountingModel = new CountingModel();
        $this->xssClean = new xssClean;
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
		    if(!empty($d->AC_NO)) { $con_type="AC"; $con_no=$d->AC_NO;}
		      elseif(!empty($d->PC_NO)) { $con_type="PC"; $con_no=$d->PC_NO;}
		    $crec=$this->CountingModel->getcountingcenter($d->ST_CODE,$con_no,$con_type);
           // dd($crec);
		    if(isset($crec)) $ccenter_id=$crec->ccenter_id; else $ccenter_id='';

             return view('admin.counting.ccenter',['user_data' =>$d,'ccenter_id'=>$ccenter_id,'crec'=>$crec,'showpage'=>'counting']);	           
	        }
	        else {
	              return redirect('/officer-login');
	        	  }
	    }  // end index function     createcenter
	public function createcenter(Request $request)
	    {   
	     if(Auth::check()){
		    $user = Auth::user();
		    $this->validate(
                $request, 
                    [
                      'center_name' => 'required',
                      'center_location' => 'required',
                    ],
                    [
                      'center_name.required' => 'Please enter Center name ',
                      'center_location.required' => 'Please enter Center Location', 
                    ]);
		      $d=$this->commonModel->getunewserbyuserid($user->id);
		      if(!empty($d->AC_NO)) { $con_type="AC"; $con_no=$d->AC_NO;}
		      elseif(!empty($d->PC_NO)) { $con_type="PC"; $con_no=$d->PC_NO;}
		      $ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$con_no,$con_type); 
		     // echo $ele_details->ELECTION_ID;
             //dd($d);

		      $center_name = $this->xssClean->clean_input($request->input('center_name'));
              $center_location = $this->xssClean->clean_input($request->input('center_location'));
              $ccenter_id = $this->xssClean->clean_input($request->input('ccenter_id'));
            $new_data = array('center_name'=>$center_name,'center_location'=>$center_location,'ELECTION_ID'=>$ele_details->ELECTION_ID,'ST_CODE'=>$d->ST_CODE,'CONT_NO'=>$con_no,'CONST_TYPE'=>$con_type); 
            //echo $ccenter_id;
             //dd($new_data);
             if(empty($ccenter_id)) {
            	$this->commonModel->insertData('counting_center_master', $new_data);
            	\Session::flash('success_admin', 'Counting Center Successfully Added');
            	}
             else {
             	$this->commonModel->updatedata('counting_center_master','ccenter_id',$ccenter_id,$new_data);
             	\Session::flash('success_admin', 'Counting Center Successfully Updated');
                }

             return Redirect::to('ro/round-schedule');	           
	        }
	        else {
	              return redirect('/officer-login');
	        	  }
	    }  // end index function    
	function round_schedule()
			{
				if(Auth::check()){
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			    $ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			      	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,'AC'); 
                  if(empty($ele_details)) 
                  	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 

                if($ele_details->CONST_TYPE=="AC") {
                 	$list=$this->CountingModel->roundsechudle($d->ST_CODE,$d->AC_NO,$ele_details->ELECTION_ID);
			    }
			    if($ele_details->CONST_TYPE=="AC") { $con_type="AC"; $con_no=$ele_details->CONST_NO;
                   $cand_data=DB::table('candidate_nomination_detail')->where('ST_CODE',$ele_details->ST_CODE )->where('ac_no',$con_no)->where('ELECTION_ID',$ele_details->ELECTION_ID)->where('application_status','6')->where('finalize','1')->orderBy('new_srno','ASC')->get();
                 	}
			      elseif($ele_details->CONST_TYPE=="PC") { $con_type="PC"; $con_no=$ele_details->CONST_NO;
			      	$cand_data=DB::table('candidate_nomination_detail')->where('ST_CODE',$d->ST_CODE )->where('PC_NO',$ele_details->CONST_NO )->where('ELECTION_ID',$ele_details->ELECTION_ID)->where('application_status','6')->where('finalize','1')->orderBy('new_srno','ASC')->get();  
			  		}
			    if(isset($list)) $rid=$list->ID; else $rid='';

               // dd($list); 
	           return view('admin.counting.roundschedule',['user_data' =>$d,'rid'=>$rid,'list'=>$list,'cand_data'=>$cand_data,'showpage'=>'counting']);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			} 
	public function verifyround(Request $request)
		    {    
		     if(Auth::check()){
			    $user = Auth::user();
			    $this->validate(
	                $request, 
	                    [
	                      'scheduled_round' => 'required|numeric|min:2|max:50',  
	                    ],
	                    [
	                      'scheduled_round.required' => 'Please enter round schedule ',
	                      'scheduled_round.numeric' => 'Please enter numeric value',
	                      'scheduled_round.min' => 'Please enter minimum value 2',
	                      'scheduled_round.max' => 'Please enter maximum value 25',
	                    ]);
			     
			      $d=$this->commonModel->getunewserbyuserid($user->id);
			      $ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			      		$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,'AC'); 
                  if(empty($ele_details)) 
                  		$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
                  
                  $sched_det=$this->commonModel->getschedulebyid($ele_details->ScheduleID);
                   
                 if($ele_details->CONST_TYPE=="AC") { $con_type="AC"; $con_no=$ele_details->CONST_NO;
                     
                    DB::enableQueryLog();
                 	$cand_data=DB::table('candidate_nomination_detail')->where('ST_CODE',$ele_details->ST_CODE )->where('ac_no',$con_no)->where('ELECTION_ID',$ele_details->ELECTION_ID)->where('application_status','6')->where('finalize','1')->orderBy('new_srno','ASC')->get();
                 	}
			      elseif($ele_details->CONST_TYPE=="PC") { $con_type="PC"; $con_no=$ele_details->CONST_NO;
			      	$cand_data=DB::table('candidate_nomination_detail')->where('ST_CODE',$d->ST_CODE )->where('PC_NO',$ele_details->CONST_NO )->where('ELECTION_ID',$ele_details->ELECTION_ID)->where('application_status','6')->where('finalize','1')->orderBy('new_srno','ASC')->get();  
			  		}
                   
                   $crec=$this->CountingModel->getcountingcenter($ele_details->ST_CODE,$con_no,$con_type);
              			     
			     $scheduled_round = $this->xssClean->clean_input($request->input('scheduled_round'));  	
			     $rid = $this->xssClean->clean_input($request->input('rid'));
			    $new_table=strtolower("counting_master_".$ele_details->ST_CODE);
			   $lis_st=$this->commonModel->getstatebystatecode($ele_details->ST_CODE);
			
			if($ele_details->CONST_TYPE=="AC") { 
			    	 $lis_ac=$this->commonModel->getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
               $winn_data=array('election_id'=>$ele_details->ELECTION_ID,'constituency_type'=>$ele_details->CONST_TYPE,'st_code'=>$ele_details->ST_CODE,
               		'st_name'=>$lis_st->ST_NAME,'ac_no'=>$ele_details->CONST_NO,'ac_name'=>$lis_ac->AC_NAME,'created_at'=>date("Y-m-d h:m:s"));
               $check_d=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();
           }  
           elseif($ele_details->CONST_TYPE=="PC") {

           		 $lis_ac=$this->commonModel->getallacbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
               $winn_data=array('election_id'=>$ele_details->ELECTION_ID,'constituency_type'=>$ele_details->CONST_TYPE,'st_code'=>$ele_details->ST_CODE,'st_name'=>$lis_st->ST_NAME,'pc_no'=>$ele_details->CONST_NO,'pc_name'=>$lis_ac->AC_NAME,'created_at'=>date("Y-m-d h:m:s"));
               $check_d=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();
           }
           
           if(!isset($check_d)) {
           			$this->commonModel->insertData('winning_leading_candidate', $winn_data);
           		}
        
			    if (Schema::hasTable($new_table))
						{  //dd($cand_data);
						 foreach($cand_data as $list){
						$cv=$this->commonModel->selectone($new_table,'nom_id',$list->nom_id);  echo 'sac';
						 if(!isset($cv)){
                           	$ca_data = array('nom_id'=>$list->nom_id,'candidate_id'=>$list->candidate_id,'AC_NO'=>$list->ac_no,'PC_NO'=>$list->pc_no,'ELECTION_ID'=>$list->election_id,'created_at'=>date("Y-m-d h:m:s"),'created_by'=>$d->officername); 
                           $this->commonModel->insertData($new_table, $ca_data);
                          	}
						   }
						} else {
						    \DB::statement('CREATE TABLE '.$new_table.' LIKE counting_master_stcode');
                           foreach($cand_data as $list){
                           	$ca_data = array('nom_id'=>$list->nom_id,'candidate_id'=>$list->candidate_id,'AC_NO'=>$list->ac_no,'PC_NO'=>$list->pc_no,'ELECTION_ID'=>$list->election_id,'created_at'=>date("Y-m-d h:m:s"),'created_by'=>$d->officername); 
                           $this->commonModel->insertData($new_table, $ca_data);
                          }
                          //dd($ca_data);
						}
				//dd("hello");
           	    if(isset($crec)) $ccenter_id=$crec->ccenter_id; else $ccenter_id=0;
	            $round_data = array('ST_CODE'=>$ele_details->ST_CODE,'AC_NO'=>$ac_details->AC_NO,'PC_NO'=>$ac_details->PC_NO,'scheduled_round'=>$scheduled_round,'DATE_POLL'=>$sched_det->DATE_POLL,'DATE_COUNT'=>$sched_det->DATE_COUNT,'ELECTION_ID'=>$ele_details->ELECTION_ID,'ELECTION_TYPEID'=>$ele_details->ELECTION_TYPEID,'ccenter_id'=>$ccenter_id,'created_by'=>$d->officername,'iscreated'=>'1','TABLE_NAME'=>$new_table); 
	            	
	            if(empty($rid)) {
		            	$this->commonModel->insertData('round_master', $round_data);
		            	\Session::flash('success_admin', 'Round Schedule Successfully Added');
            		}
             	else {
		             	$this->commonModel->updatedata('round_master','ID',$rid,$round_data);
		             	\Session::flash('success_admin', 'Round Schedule Successfully Updated');
                	}

	             return Redirect::to('ro/counting-data-entry');	           
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
		    }  // end index function   

	function counting_data_entry($rid='')
			{
				if(Auth::check()){
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			     $new_table=strtolower("counting_master_".$d->ST_CODE);
			     $ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			     	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,'AC'); 
                if(empty($ele_details)) 
                   	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
                 
                $round_details=DB::table('round_master')->where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->first();
                if($ele_details->CONST_TYPE=="AC") { 
                
                $winn_data=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();
            }
                elseif($ele_details->CONST_TYPE=="PC") { 
                $winn_data=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();
                }

                if(!isset($round_details)) {
                			\Session::flash('success_admin', 'Round Schedule Not Created! Please Create to roundschedule');
                			 return Redirect::to('ro/round-schedule');
                }   
                if($ele_details->CONST_TYPE=="AC")
                	$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->first();
               else
               		$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->first();
                
               if(!empty($rid)) {
                $field="round".$rid; 
               if($ele_details->CONST_TYPE=="AC")
                	$master_data=DB::table($new_table)->select('*', $field)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->get();
               else
               		$master_data=DB::table($new_table)->select('*', $field)->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->get();
               }
               else {  $field='';
               	if($ele_details->CONST_TYPE=="AC")
                	$master_data=DB::table($new_table)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->get();
               else
               		$master_data=DB::table($new_table)->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->get();
               }
                
           if(!empty($c_data->complete_round)){$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;}
             else {$complete_round=0; $finalized_round=0;}
             //echo $complete_round; print_r($c_data);
			 //echo "<pre>"; echo "ac_details"; print_r($ac_details); echo "ele_details";print_r($ele_details);
			 //echo "round_details"; print_r($round_details);  echo "master_data"; print_r($master_data);
            // echo "new_table"; print_r($new_table);   //echo "c_data->complete_round"; print_r($c_data->complete_round);
             //echo "rid"; print_r($rid);echo "field"; print_r($field); // echo "c_data->finalized_round"; print_r($c_data->finalized_round);  
            // echo "winn_data"; print_r($winn_data);
             // die;
	        return view('admin.counting.dataentrysechudle',['user_data' =>$d,'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'rid'=>$rid,'comp_round'=>$complete_round,'field'=>$field,'finalized_round'=>$finalized_round,'winn_data'=>$winn_data,'showpage'=>'counting']);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			}
	function verifycounting(Request $request)
			{
			 //dd($request);  
			 if(Auth::check()){ 
			    $user = Auth::user();
			    $cschedule = $this->xssClean->clean_input($request->input('cschedule'));
		        $totalround = $this->xssClean->clean_input($request->input('totalround'));
		        $new_table = $this->xssClean->clean_input($request->input('new_table'));
		        $leading_id = $this->xssClean->clean_input($request->input('leading_id'));
		        $ST_CODE = $this->xssClean->clean_input($request->input('ST_CODE'));
		        $CONST_TYPE = $this->xssClean->clean_input($request->input('CONST_TYPE'));
		        $CONST_NO = $this->xssClean->clean_input($request->input('CONST_NO'));
		        $ELECTION_ID=$this->xssClean->clean_input($request->input('ELECTION_ID'));
		        $val = $this->xssClean->clean_input($request->input('val'));
				$input = $request->all();
				if(!empty($cschedule)) $newcschedule=$cschedule+1; else $newcschedule='';
				$rules = ['Please enter all new serial number'];
				for ($i=1; $i<=$val;$i++)
				    {
				    $this->validate($request, ['currentvote'.$i => 'required|integer','cschedule' => 'required|numeric',],
		                [
		                'currentvote'.$i.'required' => 'Please enter current vote ',
		                'currentvote'.$i.'integer' => 'Please enter integer value ',
		                'cschedule.required' => 'Please select select round',
		                ]);	
			        }

 				for ($i=1; $i<=$val;$i++)
			       	{
			       	$mid=$this->xssClean->clean_input($request->input('mid'.$i));
			       	$nom_id=$this->xssClean->clean_input($request->input('nom_id'.$i));
			       	$currentvote=$this->xssClean->clean_input($request->input('currentvote'.$i));
			       	$priviousvote=$this->xssClean->clean_input($request->input('priviousvote'.$i));
			       	$round="round".$cschedule;
			       	$tvot=DB::table($new_table)->where('id', $mid)->first();
			       	
			        $total_vote=$priviousvote+$currentvote;
			       	$n_data = array($round=>$currentvote,'total_vote'=>$total_vote,'complete_round'=>$cschedule); 
			        DB::table($new_table)->where('id',$mid)->update($n_data);	
			        
			       	}
			       $fdata=$this->CountingModel->selectfirsthightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
					    $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
					    $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
					    $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
					    
	                $sdata=$this->CountingModel->selectsecondhightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
	                $trial_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
				    $trial_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
				    $trial_party=$this->commonModel->selectone('m_party','CCODE',$trial_nom->party_id);
				 //print_r($trial_nom); print_r($trial_party); dd($sdata);    
			    $margin=$fdata->max_total-$sdata->max_total;
			    $winn_update=array('candidate_id'=>$fdata->candidate_id,'nomination_id'=>$fdata->nom_id,'lead_cand_name'=>$lead_cand->cand_name,'lead_cand_partyid'=>$lead_party->CCODE,'lead_cand_party'=>$lead_party->PARTYNAME,'lead_party_type'=>$lead_party->PARTYTYPE,'lead_party_abbre'=>$lead_party->PARTYABBRE,'lead_cand_hname'=>$lead_cand->cand_hname,'lead_cand_hparty'=>$lead_party->PARTYHNAME,'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
			    	'trial_candidate_id'=>$sdata->candidate_id,'trial_nomination_id'=>$sdata->nom_id,'trial_cand_name'=>$trial_cand->cand_name,'trial_cand_partyid'=>$trial_party->CCODE,'trial_cand_party'=>$trial_party->PARTYNAME,'trial_party_type'=>$trial_party->PARTYTYPE,'trial_party_abbre'=>$trial_party->PARTYABBRE,'trial_cand_hname'=>$trial_cand->cand_hname,'trial_cand_hparty'=>$trial_party->PARTYHNAME,'trial_hpartyabbre'=>$trial_party->PARTYHABBR,'margin'=>$margin);
			   // dd($winn_update);
			     DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update);
			       

			       \Session::flash('success_mes', 'This Round Successfully Update');
                		return Redirect::to('/ro/counting-data-entry/'.$newcschedule);	        
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
			}
	function counting_evm_finalized(Request $request)
			{
			 if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
                 
			    $new_table=strtolower("counting_master_".$d->ST_CODE);
			    $ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			if($d->officerlevel=="AC"){
            	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel); 
            	if(empty($ele_details)) {
                   	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
            	}
			}
            elseif($d->officerlevel=="PC"){
            	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel);
            	if(empty($ele_details)){  
                   	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'AC'); 
                   }
            }

		$round_details=DB::table('round_master')->where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->first();
       	if($ele_details->CONST_TYPE=="AC") {
                	$master_data=DB::table($new_table)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->get();
                	$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->first();
                	$winn_data=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
               	}
               else {
               		$master_data=DB::table($new_table)->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->get();
               		$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->first();
               		$winn_data=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
               }
        $complete_round=0; $finalized_round=0;
         if(isset($c_data)){
         	$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
            }
             
         
         if($round_details->scheduled_round==$complete_round)
		   {
		   		$date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s');
				$otp = rand(100000,999999);
            $dat = array('mobile_otp' => $otp,'otp_time' => $currentTime,); 
             $this->commonModel->updatedata('officer_login','id',$d->id,$dat); 

              if($d->Phone_no!=""){  
                $mob_message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Counting Portal. Please enter the OTP to proceed.Your OTP will be valid till 10 minutes.Do not share this OTP Team ECI";
                
                 $response = SmsgatewayHelper::sendOtpSMS($mob_message,$d->Phone_no); 
                 
                }
		   return view('admin.counting.evm_vote_finalize',['user_data' =>$d,'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'comp_round'=>$complete_round,'otp'=>$otp,'finalized_round'=>$finalized_round,'winn_data'=>$winn_data,'showpage'=>'counting']);	} 
               	else {
               		\Session::flash('error_mes', 'All rounds not completed, Please Complete your rounds then finalized');
                		return Redirect::to('/ro/counting-data-entry');	      
               		   
               	} 
			   
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
			}
 	function postal_data_entry()
			{
				if(Auth::check()){
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			     $new_table=strtolower("counting_master_".$d->ST_CODE);
			     $ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			     	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,'AC'); 
                if(empty($ele_details)) 
                   	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
                 $v=Session::get('comp_round'); 
                 
                
                $round_details=DB::table('round_master')->where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->first();
                 if($ele_details->CONST_TYPE=="AC") { 
                $winn_data=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();
            }
                elseif($ele_details->CONST_TYPE=="PC") { 
                $winn_data=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();
                }


                 if(!isset($round_details)) {
                			\Session::flash('success_admin', 'Round Schedule Not Created! Please Create to roundschedule');
                			 return Redirect::to('ro/round-schedule');
                }   
                if($ele_details->CONST_TYPE=="AC")
                	$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->first();
               else
               		$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->first();
                 
               	if($ele_details->CONST_TYPE=="AC")
                	$master_data=DB::table($new_table)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->get();
               else
               		$master_data=DB::table($new_table)->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->get();
           if(is_array($c_data)){$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;}
             else {$complete_round=0; $finalized_round=0;}
             
			 
 
	           return view('admin.counting.postaldataentrysechudle',['user_data' =>$d,'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'comp_round'=>$complete_round,'finalized_round'=>$finalized_round,'winn_data'=>$winn_data,'showpage'=>'counting']);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			}
	function verifypostalentry(Request $request)
			{
			 
			 if(Auth::check()){ 
			    $user = Auth::user();
			     //dd($request);
		        $new_table = $this->xssClean->clean_input($request->input('new_table'));
		        $round_id = $this->xssClean->clean_input($request->input('round_id'));
		        $rejectedvotes = $this->xssClean->clean_input($request->input('rejectedvotes'));
		        $totalvotes = $this->xssClean->clean_input($request->input('totalvotes'));
		        $leading_id = $this->xssClean->clean_input($request->input('leading_id'));
		        $ST_CODE = $this->xssClean->clean_input($request->input('ST_CODE'));$CONST_TYPE = trim($request->input('CONST_TYPE'));
		        $CONST_NO = $this->xssClean->clean_input($request->input('CONST_NO'));
		        $ELECTION_ID=$request->input('ELECTION_ID');

		        $val = trim($request->input('val'));
				$input = $request->all();
				$total=0;
				$rules = ['Please enter postal vote'];
				for ($i=1; $i<=$val;$i++)
				    {
				    	$cv=trim($request->input('currentvote'.$i));
				        $total=$total+$cv;
				    $this->validate($request, ['currentvote'.$i => 'required|integer','totalvotes'=> 'required|integer','rejectedvotes'=> 'required|integer',],
		                [
		                'currentvote'.$i.'required' => 'Please enter postal vote ',
		                'totalvotes.required' => 'Please enter Total Votes',
		                'rejectedvotes.required' => 'Please enter Total Rejected Votes',
		                ]);	
			        }
			     $total=$total+$rejectedvotes;
			    if($totalvotes== $total)  {
 				for ($i=1; $i<=$val;$i++)
			       	{
			       	$mid=trim($request->input('mid'.$i));
			       	$nom_id=trim($request->input('nom_id'.$i));
			       	$currentvote=trim($request->input('currentvote'.$i));
			       	$priviousvote=trim($request->input('priviousvote'.$i));
			        
			        $total_vote=$priviousvote+$currentvote;
			       	$n_data = array('total_vote'=>$total_vote,'postalballot_vote'=>$currentvote); 
			        DB::table($new_table)->where('id',$mid)->update($n_data);	
			        
			       	}
			       	$data = array('rejected_votes'=>$rejectedvotes,'postal_total_votes'=>$totalvotes); 
			        DB::table('round_master')->where('ID',$round_id)->update($data);

			         $fdata=$this->CountingModel->selectfirsthightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
					    $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
					    $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
					    $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
					    
	                $sdata=$this->CountingModel->selectsecondhightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
	                $trial_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
				    $trial_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
				    $trial_party=$this->commonModel->selectone('m_party','CCODE',$trial_nom->party_id);
				     
			    $margin=$fdata->max_total-$sdata->max_total;
			    $winn_update=array('candidate_id'=>$fdata->candidate_id,'nomination_id'=>$fdata->nom_id,'lead_cand_name'=>$lead_cand->cand_name,'lead_cand_partyid'=>$lead_party->CCODE,'lead_cand_party'=>$lead_party->PARTYNAME,'lead_party_type'=>$lead_party->PARTYTYPE,'lead_party_abbre'=>$lead_party->PARTYABBRE,'lead_cand_hname'=>$lead_cand->cand_hname,'lead_cand_hparty'=>$lead_party->PARTYHNAME,'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
			    	'trial_candidate_id'=>$sdata->candidate_id,'trial_nomination_id'=>$sdata->nom_id,'trial_cand_name'=>$trial_cand->cand_name,'trial_cand_partyid'=>$trial_party->CCODE,'trial_cand_party'=>$trial_party->PARTYNAME,'trial_party_type'=>$trial_party->PARTYTYPE,'trial_party_abbre'=>$trial_party->PARTYABBRE,'trial_cand_hname'=>$trial_cand->cand_hname,'trial_cand_hparty'=>$trial_party->PARTYHNAME,'trial_hpartyabbre'=>$trial_party->PARTYHABBR,'margin'=>$margin);
			     DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update);

				         \Session::flash('success_mes', 'This Postal Vote Successfully Update');
	                		return Redirect::to('/ro/postal-data-entry');	        
		        }
		        else {
                     \Session::flash('error_mes', 'Total Votes and candidate Vote Miss-Match');
                		return Redirect::to('/ro/postal-data-entry');	
		        }
		    }
		        else {
		              return redirect('/officer-login');
		        	  }
			}
	function counting_results()
			{
				if(Auth::check()){
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			    //dd($d);
			     $new_table=strtolower("counting_master_".$d->ST_CODE);
			     $ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			     	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,'AC'); 
                if(empty($ele_details)) 
                   	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
                 $v=Session::get('comp_round'); 
                 
                
                $round_details=DB::table('round_master')->where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->first();
                 if(!isset($round_details)) {
                   \Session::flash('success_admin', 'Round Schedule Not Created! Please Create to roundschedule');
                   return Redirect::to('ro/round-schedule');
                }   
                
               		
                 
               	if($ele_details->CONST_TYPE=="AC") {
                	$master_data=DB::table($new_table)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->get();
                	$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->first();
                	$winn_data=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
               	}
               else {
               		$master_data=DB::table($new_table)->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->get();
               		$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->first();
               		$winn_data=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
               }
           if(is_array($c_data)){$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;}
             else {$complete_round=0; $finalized_round=0;}
			return view('admin.counting.counting-results',['user_data' =>$d,'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'comp_round'=>$complete_round,'finalized_round'=>$finalized_round,'winn_data'=>$winn_data,'showpage'=>'counting']);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			}
	function counting_finalized()
			{
				if(Auth::check()){
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			     $new_table=strtolower("counting_master_".$d->ST_CODE);
			    $ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			
			if($d->officerlevel=="AC"){
            	  $ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel); 
            	if(empty($ele_details)) {
                $ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
            	}
			}
            elseif($d->officerlevel=="PC"){
            	  $ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel);
            	if(empty($ele_details)){  
                  $ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'AC'); 
                   }
            }

			$round_details=DB::table('round_master')->where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->first();

       	if($ele_details->CONST_TYPE=="AC") {
       			$master_data=DB::table($new_table)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->get();
                 
                $winn_data=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
                $c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->first();
                $m_data=DB::table($new_table)->select('postalballot_vote')->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('postalballot_vote','DESC')->first();	 
               	}
               else {
               	$master_data=DB::table($new_table)->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->get();
               		 
               	$winn_data=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
               	$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->first();
               	$m_data=DB::table($new_table)->select('postalballot_vote')->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('postalballot_vote','DESC')->first();	
               		}
            	
        $complete_round=0; $finalized_round=0;
         if(isset($c_data)){
         	$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
            }
        if($finalized_round==0) {
        	 \Session::flash('error_mes', 'Evm Rounds Not finalized');
             return Redirect::to('/ro/counting-data-entry'); 
        	}   
        if($m_data->postalballot_vote==0) {
        	 \Session::flash('error_mes', 'Postal data not Entered');
             return Redirect::to('/ro/postal-data-entry'); 
        	}
        
            
            $date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s');
				$otp = rand(100000,999999);
            $dat = array('mobile_otp' => $otp,'otp_time' => $currentTime,); 
             $this->commonModel->updatedata('officer_login','id',$d->id,$dat); 

              if($d->Phone_no!=""){  
                $mob_message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Counting Portal. Please enter the OTP to proceed.Your OTP will be valid till 10 minutes.Do not share this OTP Team ECI";
                
                 $response = SmsgatewayHelper::sendOtpSMS($mob_message,$d->Phone_no); 
                 
                }
		   return view('admin.counting.counting_finalize',['user_data' =>$d,'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'comp_round'=>$complete_round,'otp'=>$otp,'finalized_round'=>$finalized_round,'winn_data'=>$winn_data,'showpage'=>'counting']);     
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			}
	function results_declaration(Request $request)
			{
			  
			 if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			     $this->validate(
                  $request, 
                      [
                       'verifyotp' => 'required|numeric',
                      ],
                      [
                       'verifyotp.required' => 'Please enter your valid Otp', 
                       'verifyotp.numeric' => 'Please enter your valid Otp',
                      ]);
           $verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
           $otp = $this->xssClean->clean_input($request->input('otp'));
           $otp_time = $this->xssClean->clean_input($request->input('otp_time'));
            
           $date = Carbon::now()->subMinutes(10);
                $currentTime = $date->format('Y-m-d H:i:s');
           
           if($otp!=$verifyotp) {
             \Session::flash('ro_opt_messsage', 'Your Otp Message Invalide');
                  return Redirect::to('/ro/results-verified');
           }
          if($otp_time<$currentTime) {
             \Session::flash('ro_opt_messsage', 'Your Otp time Expair');
                   return Redirect::to('/ro/results-verified');
           }

			$ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,'AC');

            if(empty($ele_details)) 
               $ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
                
                	$n_data = array('status'=>'declerad'); 
			    
            if($ele_details->CONST_TYPE=="AC")
              $round_details=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
            elseif($ele_details->CONST_TYPE=="AC")
               		 $round_details=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
                 
               	     DB::table('winning_leading_candidate')->where('leading_id',$round_details->leading_id)->update($n_data);	 
              
			     \Session::flash('success_mes', 'This Result is Declered Successfully');
	                		return Redirect::to('/ro/counting-results');     	
		           
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
			}
   public function results_verified(){
            $users=Session::get('admin_login_details');
            $user = Auth::user();   
        if(session()->has('admin_login')){  
            $uid=$user->id;
            $d=$this->commonModel->getunewserbyuserid($uid);
             
			//$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,'AC'); 
           // if(empty($ele_details)) 
            //  $ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
            $date = Carbon::now();
            $currentTime = $date->format('Y-m-d H:i:s');
        
            $otp=rand(100000,999999);
            $mob_message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Counting Portal for results declaration AC . Please enter the OTP to proceed.Your OTP will be valid till 10 minutes.Do not share this OTP,  Team ECI";
     
            //$st = array('mobile_otp'=>$otp,'otp_time' => $currentTime);
           // $i = DB::table('winning_leading_candidate')->where('id',$list->id)->update($st);
            $response = SmsgatewayHelper::sendOtpSMS($mob_message,$d->Phone_no); 

          return view('admin.counting.results-verified',['user_data'=>$d,'otp'=>$otp,'otp_time'=>$currentTime,'showpage'=>'counting']);
             
          }
          else {
              return redirect('/officer-login');
          }    
  
        }   // end candidate_finalize function finalize_ac_counting	
     function finalize_evm_rounds(Request $request)
    		{
    		if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
            $this->validate(
	                $request, 
	                    [
	                     'verifyotp' => 'required|numeric',
	                     ],
	                    [
	                     'verifyotp.required' => 'Please enter your valid Otp', 
	                     'verifyotp.numeric' => 'Please enter your valid Otp',
	                    ]);
		    $verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
		     
		    $date = Carbon::now()->subMinutes(10);
            $currentTime = $date->format('Y-m-d H:i:s');
            
		    if($d->mobile_otp!=$verifyotp) {
		       	 \Session::flash('ro_opt_messsage', 'Your Otp Message Invalide');
                  return Redirect::to('/ro/counting-evm-finalized');
		       }
		      if($d->otp_time<$currentTime) {
		      	 \Session::flash('ro_opt_messsage', 'Your Otp time Expair');
                  return Redirect::to('/ro/counting-evm-finalized');
		       }

			$new_table=strtolower("counting_master_".$d->ST_CODE);
			$ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			if($d->officerlevel=="AC"){
            	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel); 
            	if(empty($ele_details)) {
                   	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
            	}
			}
            elseif($d->officerlevel=="PC"){
            	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel);
            	if(empty($ele_details)){  
                   	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'AC'); 
                   }
            }

		$round_details=DB::table('round_master')->where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->first();
       	if($ele_details->CONST_TYPE=="AC") {
                	//$master_data=DB::table($new_table)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->get();
                	$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->first();
                	//$winn_data=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
               	}
               else {
               		//$master_data=DB::table($new_table)->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->get();
               		$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->orderBy('id')->first();
               		//$winn_data=DB::table('winning_leading_candidate')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
               }
        $complete_round=0; $finalized_round=0;
         if(isset($c_data)){
         	$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
            }
             
         
         if($round_details->scheduled_round==$complete_round)
		   {
		   	$n_data = array('finalized_round'=>'1'); 
			DB::table($new_table)->where('AC_NO',$ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->update($n_data);
			          
		   	 \Session::flash('error_mes', 'Evm Rounds Successfully finalized');
             return Redirect::to('/ro/counting-data-entry'); 
		   	} 
         else {
            \Session::flash('error_mes', 'All rounds not completed, Please Complete your rounds then finalized');
             return Redirect::to('/ro/counting-data-entry');	      
               		   
            } 
			   
		  }
		   else {
		         return redirect('/officer-login');
		         }	
    		}
    function finalize_ac_counting(Request $request)
    		{
    		if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
            $this->validate(
	                $request, 
	                    [
	                     'verifyotp' => 'required|numeric',
	                     ],
	                    [
	                     'verifyotp.required' => 'Please enter your valid Otp', 
	                     'verifyotp.numeric' => 'Please enter your valid Otp',
	                    ]);
		    $verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
		     
		    $date = Carbon::now()->subMinutes(10);
            $currentTime = $date->format('Y-m-d H:i:s');
            
		    if($d->mobile_otp!=$verifyotp) {
		       	 \Session::flash('ro_opt_messsage', 'Your Otp Message Invalide');
                  return Redirect::to('/ro/counting-evm-finalized');
		       }
		      if($d->otp_time<$currentTime) {
		      	 \Session::flash('ro_opt_messsage', 'Your Otp time Expair');
                  return Redirect::to('/ro/counting-evm-finalized');
		       }

			$new_table=strtolower("counting_master_".$d->ST_CODE);
			$ac_details=$this->commonModel->getacbyacno($d->ST_CODE,$d->AC_NO);
			if($d->officerlevel=="AC"){
            	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->AC_NO,$d->officerlevel); 
            	if(empty($ele_details)) {
                   	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'PC'); 
            	}
			}
            elseif($d->officerlevel=="PC"){
            	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$d->PC_NO,$d->officerlevel);
            	if(empty($ele_details)){  
                   	$ele_details=$this->commonModel->election_details_cons($d->ST_CODE,$ac_details->PC_NO,'AC'); 
                   }
            }

		      $n_data = array('finalized_ac'=>'1'); 
			    
                if($ele_details->CONST_TYPE=="AC")
                	 $round_details=DB::table('round_master')->where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->first();
               elseif($ele_details->CONST_TYPE=="AC")
               		 $round_details=DB::table('round_master')->where('ST_CODE', $ele_details->ST_CODE)->where('PC_NO', $ele_details->CONST_NO)->where('ELECTION_ID', $ele_details->ELECTION_ID)->first();
                 
               	     DB::table('round_master')->where('ID',$round_details->ID)->update($n_data);	 
              
			     \Session::flash('success_mes', 'This AC Successfully finalized');
	                		return Redirect::to('/ro/postal-data-entry');
		  
			   
		  }
		   else {
		         return redirect('/officer-login');
		         }	
    		}	
}  // end class results-declaration   
