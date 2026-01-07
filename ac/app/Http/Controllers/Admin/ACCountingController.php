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
		use App\adminmodel\ACCountingModel; 
		use Illuminate\Support\Facades\Schema;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
class ACCountingController extends Controller
{   
 public $mongo_sync = false;

   public function __construct()
        {
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->CountingModel = new ACCountingModel();
        $this->xssClean = new xssClean;
        }

  protected function guard(){
        return Auth::guard('admin');
    	}

     
	    // ALL Module and logic develop by Sachchidanand
	function round_schedule()
			{
				if(Auth::check()){
			    $user = Auth::user();
			   	$d=$this->commonModel->getunewserbyuserid($user->id);
				   $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		           $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		     
		          
		 		$new_table=strtolower("counting_master_".$d->st_code);
		 		$filter='';  
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
                 
			    
		$list=$this->CountingModel->roundsechudle($filter);
 		$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no', $ele_details->CONST_NO)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->first();
 		if(!isset($c_data)){
            \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
		    return Redirect::to('roac/counting/prepare-counting');
 		}
 		if(!empty($c_data->complete_round)){
           			$complete_round=$c_data->complete_round; 
           			$finalized_round=$c_data->finalized_round;
           		}
             else {$complete_round=0; $finalized_round=0;}

 			 if(isset($list)) $rid=$list->id; else $rid='';
 
	        return view('admin.ac.counting.roundschedule',['user_data' => $d,'rid'=>$rid,'list'=>$list,'finalized_round'=>$finalized_round,'ele_details'=>$ele_details]);	           
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
	                      'scheduled_round' => 'required|numeric|min:1|max:130',  
	                    ],
	                    [
	                      'scheduled_round.required' => 'Please enter round schedule ',
	                      'scheduled_round.numeric' => 'Please enter numeric value',
	                      'scheduled_round.min' => 'Please enter minimum value 1',
	                      'scheduled_round.max' => 'Please enter maximum value 130',
	                    ]);
			     
			      $d=$this->commonModel->getunewserbyuserid($user->id);
			      $ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
			     $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		         $seched=getschedulebyid($ele_details->ScheduleID);
		           $new_table=strtolower("counting_master_".$ele_details->ST_CODE);
            
        
	        $scheduled_round = Check_Input($this->xssClean->clean_input($request->input('scheduled_round')));
	        $scheduled_round1 = Check_Input($this->xssClean->clean_input($request->input('scheduled_round1')));	
			$rid = $this->xssClean->clean_input($request->input('rid'));
		 		$filter='';  
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
                 
			    
						

		 DB::beginTransaction();
        	try{
        		  
		        $round_details=$this->CountingModel->roundsechudle($filter);
			$c_data=DB::table($new_table)->select('complete_round','finalized_round')
											->where('ac_no', $d->ac_no)
											->where('election_id',$ele_details->ELECTION_ID)->first();

			if($c_data->complete_round>$scheduled_round and $scheduled_round1>$scheduled_round)
			   		  {
					   \Session::flash('error_mes', 'No of Rounds can not be less than completed rounds');
					    return Redirect::to('roac/counting/round-schedule');	
					 }
		 
		
		     $date = Carbon::now();
             $currentTime = $date->format('Y-m-d H:i:s');
             $currentdate = $date->format('Y-m-d');      
			 
				$ccenter_id=0;   
	    			$round_data = array('st_code'=>$d->st_code,
	    								'ac_no'=>$d->ac_no,
	    								'pc_no'=>$d->pc_no,
	    								'scheduled_round'=>$scheduled_round,
	    								'date_poll'=>$seched['DATE_POLL'],
	    								'date_count'=>$seched['DATE_COUNT'],
	    								'election_id'=>$ele_details->ELECTION_ID,
	    								'election_typeid'=>$ele_details->ELECTION_TYPEID,
	    								'ccenter_id'=>$ccenter_id,
	    								'created_by'=>$d->officername,
	    								'iscreated'=>'1',
	    								'table_name'=>$new_table,
	    								'added_create_at'=>$currentdate); 
	           	
       
		    if(!isset($round_details)) {
			        $this->commonModel->insertData('round_master', $round_data);
			        DB::commit();
			        \Session::flash('success_admin', 'Round Schedule Successfully Added');
	            }
	         else {
			        $this->commonModel->updatedata('round_master','id',$round_details->id,$round_data);
			        
			        \Session::flash('success_admin', 'Round Schedule Successfully Updated');
	              }
	        
	         DB::commit();
	     }
 		catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('error_mes', 'Please try again');
		            return Redirect::back();
		        }
		    



 
	          return Redirect::to('roac/counting/counting-data-entry');	           
		}
		else {
		              return redirect('/officer-login');
		        	  }
		    }  // end index function   

	function counting_data_entry($rid1='')
			{    // dd($rid);
				if(Auth::check()){
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			    $new_table=strtolower("counting_master_".$d->st_code);
			    $ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
				$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $rid= base64_decode($rid1); 
		        $filter='';  $filter_m='';
			    	
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
               
               $filter_m 	= [
	       	     
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' 	=> 'id', 
	       	    		];
				
				$round_details=$this->CountingModel->roundsechudle($filter);
                 
        		$winn_data=$this->CountingModel->winn_lead($filter);  
        		  
             
              if(!isset($round_details)) {
                			\Session::flash('success_admin', 'Please define rounds schedule to enter counting data.');
                			 return Redirect::to('roac/counting/round-schedule');
                }   
             $c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->orderBy('id')->first();
             
             if(!empty($c_data->complete_round)){  
           			$complete_round=$c_data->complete_round; 
           			$finalized_round=$c_data->finalized_round;
           			$n=$complete_round+1;
           		 }
             else {$complete_round=0; $finalized_round=0;  $n=$complete_round+1;}
             if($rid!=''){ $n=$rid; }
             $field="round".$n;
             
             $master_data=$this->CountingModel->master_records( $new_table,$filter_m);   
             
	        return view('admin.ac.counting.dataentrysechudle',['user_data' => $d, 'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'rid'=>$rid,'comp_round'=>$complete_round,'field'=>$field,'finalized_round'=>$finalized_round,'winn_data'=>$winn_data,'ac_no'=>$d->ac_no]);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
			}
	function counting_data_entry_edit(Request $request)
			{    
				$rid =$request->input('rid');
				if($rid!=''){
 					$nrid= base64_encode($rid);
				 
				return Redirect::to('roac/counting/counting-data-entry/'.$nrid);
			}
			else {
				\Session::flash('error_mes', '  Please Select   roundschedule');
		         return Redirect::to('roac/counting/counting-data-entry');
			}

				 
			}
	function verifycounting(Request $request)
			{ 
			 if(Auth::check()){ 
			    $user = Auth::user(); 
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			   
		        $val = $this->xssClean->clean_input($request->input('val'));
				$input = $request->all();
					$date = Carbon::now();
             		$currentTime = $date->format('Y-m-d H:i:s');
             		$currentdate = $date->format('Y-m-d');  
				 if(!empty($cschedule)) $newcschedule=$cschedule+1; else $newcschedule='';
				$rules = ['Please enter all new serial number'];
				for ($i=1; $i<=$val;$i++)
				    {
				    $this->validate($request, ['currentvote'.$i => 'required|numeric|digits_between:0,999999','cschedule' => 'required|numeric',],
		                [
		                'currentvote'.$i.'required' => 'Please enter current vote ',
		                'currentvote'.$i.'numeric' => 'Please enter integer value ',
		                'currentvote'.$i.'digits_between' => 'Please enter integer value ',
		                'cschedule.required' => 'Please select select round',
		                ]);	
			        }
                // dd($input);
                $cschedule = $this->xssClean->clean_input($request->input('cschedule'));
		        $totalround = $this->xssClean->clean_input($request->input('totalround'));
		         
		        $leading_id = $this->xssClean->clean_input($request->input('leading_id'));
		        $ST_CODE = $this->xssClean->clean_input($request->input('ST_CODE'));
		        $CONST_TYPE = $this->xssClean->clean_input($request->input('CONST_TYPE'));
		        $CONST_NO = $this->xssClean->clean_input($request->input('CONST_NO'));
		        $ELECTION_ID=$this->xssClean->clean_input($request->input('ELECTION_ID'));
		        $nrid=$this->xssClean->clean_input($request->input('nrid'));   
 				$new_table=strtolower("counting_master_".$d->st_code);

 				  DB::beginTransaction();
        	 try{
        		    $mango_db_array=[];
 				  for ($i=1; $i<=$val;$i++)
			       	{
				       	$mid=$this->xssClean->clean_input($request->input('mid'.$i));
				       	$nom_id=$this->xssClean->clean_input($request->input('nom_id'.$i));
				       	$currentvote=$this->xssClean->clean_input($request->input('currentvote'.$i));
				        $priviousvote=$this->xssClean->clean_input($request->input('priviousvote'.$i));
				       	$round="round".$cschedule;
				        

				       $filter_ele = ['id'=>$mid,'nom_id'=>$nom_id,'ac_no'=> $d->ac_no];
				       $total_value='';
				       $total_value=$this->CountingModel->grandtotalsum($new_table,$round,$filter_ele);
                         
				        $total_vote   = 0; 
				        $round_vote=0;
						$total_vote1=0;
           			    if(isset($total_value) && $total_value){
				            $total_vote   = $total_value->grant_total;
							$total_vote1 = $total_value->grant_total;
				            $round_vote=$total_value->$round;
				            $postal_vote=$total_value->postalballot_vote;
				          }
				       $total_vote= ($total_vote-$round_vote)+$currentvote+$postal_vote;
				      // echo $postal_vote."-"; echo $currentvote."="; echo $total_vote."=";  echo $round_vote."=";   
				      // dd($total_value);  die;
 					if($nrid==0){
			         	$n_data = array($round=>$currentvote,
			         					'total_vote'=>$total_vote,'complete_round'=>$cschedule,
			         					'added_update_at'=>$currentdate,'updated_at'=>$currentTime,
			         					'updated_by'=>$d->officername); 
			       }
			       else { $nr="round".$nrid;
			          $n_data = array($round=>$currentvote,'total_vote'=>$total_vote,
			          					'added_update_at'=>$currentdate,'updated_at'=>$currentTime,
			          					'updated_by'=>$d->officername);
			       } 
 					   
				      DB::table($new_table)->where('id',$mid)->update($n_data);	
			       $data22 = ["add_evm_vote"=>$total_vote1,'total_vote'=>$total_vote, "nom_id"=>$nom_id, "st_code"=>$ST_CODE, "ac_no"=>$d->ac_no];
	              	  $mango_db_array[]=$data22;

	              }
	                // $mongo_data_array[] =["add_evm_vote"=>12000, "total_vote"=>13000, "nom_id"=>1355, "st_code"=>"S01", "ac_no"=>4];
	                 //dd($pcentry);
	              if( $this->mongo_sync){  
	              	  updateEvmByIdAc($mango_db_array);
				      //End API
				  }

			         $sdata=$this->CountingModel->selectsecondhightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
                     $fdata=$this->CountingModel->selectfirsthightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
			       
			       //if(isset($fdata) and isset($sdata) and ($fdata->max_total !=$sdata->max_total)){
						    $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
						    $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
						    $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
					
				 
		                $trail_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
					    $trail_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
					    $trail_party=$this->commonModel->selectone('m_party','CCODE',$trail_nom->party_id);
					 

				    $margin=$fdata->max_total-$sdata->max_total;
				    $winn_update=array('candidate_id'=>$fdata->candidate_id,'nomination_id'=>$fdata->nom_id,'lead_cand_name'=>$lead_cand->cand_name,'lead_cand_partyid'=>$lead_party->CCODE,'lead_cand_party'=>$lead_party->PARTYNAME,'lead_party_type'=>$lead_party->PARTYTYPE,'lead_party_abbre'=>$lead_party->PARTYABBRE,'lead_cand_hname'=>$lead_cand->cand_hname,'lead_cand_hparty'=>$lead_party->PARTYHNAME,'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    	'trail_candidate_id'=>$sdata->candidate_id,'trail_nomination_id'=>$sdata->nom_id,'trail_cand_name'=>$trail_cand->cand_name,'trail_cand_partyid'=>$trail_party->CCODE,'trail_cand_party'=>$trail_party->PARTYNAME,'trail_party_type'=>$trail_party->PARTYTYPE,'trail_party_abbre'=>$trail_party->PARTYABBRE,'trail_cand_hname'=>$trail_cand->cand_hname,'trail_cand_hparty'=>$trail_party->PARTYHNAME,'trail_hpartyabbre'=>$trail_party->PARTYHABBR,'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,'trail_total_vote'=>$sdata->max_total,'added_update_at'=>$currentdate,'updated_at'=>$currentTime);
				     	 //dd($winn_update);
				     	DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update);
				    	// }
         //              else{
         //              	 $winn_update=array('candidate_id'=>0,'nomination_id'=>0,'lead_cand_name'=>'','lead_cand_partyid'=>0,'lead_cand_party'=>'','lead_party_type'=>'','lead_party_abbre'=>'','lead_cand_hname'=>'','lead_cand_hparty'=>'','lead_hpartyabbre'=>'',
				    	// 	'trail_candidate_id'=>0,'trail_nomination_id'=>0,'trail_cand_name'=>'','trail_cand_partyid'=>0,'trail_cand_party'=>'',
				    	// 	'trail_party_type'=>'','trail_party_abbre'=>'','trail_cand_hname'=>'','trail_cand_hparty'=>'','trail_hpartyabbre'=>'',
				    	// 	'margin'=>0,'lead_total_vote'=>0,'trail_total_vote'=>0,'added_update_at'=>$currentdate,'updated_at'=>$currentTime);
				     // 	 //dd($winn_update);
				     // 	DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update);
         //              }
				     	 	 if( $this->mongo_sync){
                     // API of Mango Node JS 
				     $winn_update1=array('st_code'=>$ST_CODE,'ac_no'=>$CONST_NO,'candidate_id'=>$fdata->candidate_id,
				     					'nomination_id'=>$fdata->nom_id,
				     					'lead_cand_name'=>$lead_cand->cand_name,
				     					'lead_cand_hname'=>$lead_cand->cand_hname,
				     					'lead_cand_partyid'=>$lead_party->CCODE,
				     					'lead_cand_party'=>$lead_party->PARTYNAME,
				     					'lead_party_type'=>$lead_party->PARTYTYPE,
				     					'lead_party_abbre'=>$lead_party->PARTYABBRE,
				     					'lead_cand_hparty'=>$lead_party->PARTYHNAME,
				     					'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    					'trail_candidate_id'=>$sdata->candidate_id,
				    					'trail_nomination_id'=>$sdata->nom_id,
				    					'trail_cand_name'=>$trail_cand->cand_name,'trail_cand_hname'=>$trail_cand->cand_hname,
				    					'trail_cand_partyid'=>$trail_party->CCODE,'trail_cand_party'=>$trail_party->PARTYNAME,
				    					'trail_party_type'=>$trail_party->PARTYTYPE,'trail_party_abbre'=>$trail_party->PARTYABBRE,
				    					'trail_cand_hparty'=>$trail_party->PARTYHNAME,'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    					'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,'trail_total_vote'=>$sdata->max_total);
				     updateWinningLeadingAc($winn_update1);
				     //End API
                     }
			         }
				     catch(\Exception $e){
		            	DB::rollback();
		    
			             \Session::flash('error_mes', 'Please try again');
			             return Redirect::back();
				        }
				        DB::commit();

			         \Session::flash('success_mes', 'This Round Successfully Updated.');
                	 return Redirect::to('/roac/counting/counting-data-entry');	        
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
                 
			    $new_table=strtolower("counting_master_".$d->st_code);
			    $ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
				 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		          
				$filter='';  $filter_m='';
			    	
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
               
               $filter_m 	= [
	       	     
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' 	=> 'id', 
	       	    		];
				
				$round_details=$this->CountingModel->roundsechudle($filter);
                $winn_data=$this->CountingModel->winn_lead($filter);  
        		$master_data=$this->CountingModel->master_records( $new_table,$filter_m);  

        		$c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no', $d->ac_no)->where('election_id',$ele_details->ELECTION_ID)->orderBy('id')->first();
        		       

		        if(!isset($round_details)) {
		                	\Session::flash('success_admin', 'Round Schedule Not Created! Please Create to roundschedule');
		                	return Redirect::to('roac/counting/round-schedule');
		                }   
        
                $complete_round=0; $finalized_round=0;
		         if(isset($c_data)){
		         	$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
		            }
		             
		         
		         if($round_details->scheduled_round==$complete_round)
				   {
				   		 
				 return view('admin.ac.counting.evm_vote_finalize',['user_data' => $d, 'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'comp_round'=>$complete_round,'finalized_round'=>$finalized_round,'winn_data'=>$winn_data]);	
					} 
		             else {
		               	\Session::flash('error_mes', 'All rounds not completed, Please Complete your rounds then finalized');
		                return Redirect::to('/roac/counting/counting-data-entry');	      
		               		   
		               	} 
					   
				        }
				        else {
				              return redirect('/officer-login');
				        	  }
			}

 	function postal_data_entry(){


		if(!Auth::check()){
			return redirect('/officer-login');
		}


	    $user 		= 	Auth::user();
		 $d 		=	$this->commonModel->getunewserbyuserid($user->id);
        $new_table		=	strtolower("counting_master_".$d->st_code);
		$ele_details 	=	$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		      
       	    $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
       	    $finalize=counting_finalize($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
            
            $filter 	= '';  
            $filter_m 	= '';
			    	
			$filter = [
	       	    'st_code' 	=> $ele_details->ST_CODE,
	       	    'ac_no' 	=> $ele_details->CONST_NO,
	       	    'election_id' 	=> $ele_details->ELECTION_ID,	 
	       	];
               
            $filter_m 	= [
	       	  	'ac_no' 	=> $ele_details->CONST_NO,
	       	   	'election_id' 	=> $ele_details->ELECTION_ID,
	       	   	'order_by' 	=> 'id', 
	       	];
				
			$round_details=$this->CountingModel->roundsechudle($filter);
            $winn_data=$this->CountingModel->winn_lead($filter);  
        	$master_data=$this->CountingModel->master_records( $new_table,$filter_m);  

        		if(!isset($round_details)){
				\Session::flash('success_admin', 'Please define rounds schedule to enter counting data. ');
	        	return Redirect::to('roac/counting/round-schedule');	
			}
			 
			// if(!$round_details || $round_details->finalized_ac == '0'){
			// 	\Session::flash('error_mes', 'Evm votes not finalize');
	  //       	return Redirect::to('roac/counting/counting-data-entry');	
			// }
			 
		 
		return view('admin.ac.counting.postaldataentrysechudle',['user_data' => $d, 'roac'=>$byro,'new_table'=>$new_table,'ele_details'=>$ele_details,
					'master_data'=>$master_data,'finalize'=>$finalize->finalize_by_ro, 'winn_data'=>$winn_data,'st_code'=>$ele_details->ST_CODE,
					'ac_no'=>$ele_details->CONST_NO,'round_details'=>$round_details]);          
		        
			}
	function verifypostalentry(Request $request)
			{
			// dd($request);
			 if(Auth::check()){ 
			    $user = Auth::user();
			        $user = Auth::user();
			 		$d=$this->commonModel->getunewserbyuserid($user->id);
             	 
		        //$new_table = $this->xssClean->clean_input($request->input('new_table'));
		        $round_id = $this->xssClean->clean_input($request->input('round_id'));
		        $rejectedvotes = $this->xssClean->clean_input($request->input('rejectedvotes'));
		        $totalvotes = $this->xssClean->clean_input($request->input('totalvotes'));
		        $leading_id = $this->xssClean->clean_input($request->input('leading_id'));
		        $ST_CODE = $this->xssClean->clean_input($request->input('ST_CODE'));
		        $CONST_TYPE = trim($request->input('CONST_TYPE'));
		        $CONST_NO = $this->xssClean->clean_input($request->input('CONST_NO'));
		        $ELECTION_ID=$request->input('ELECTION_ID');
  				$val = trim($request->input('val'));
  				$totalvotes = $this->xssClean->clean_input($request->input('totalvotes'));
 				 $tended_votes = $this->xssClean->clean_input($request->input('tended_votes'));
 				 $input = $request->all();
				$date = Carbon::now();
             		$currentTime = $date->format('Y-m-d H:i:s');
             		$currentdate = $date->format('Y-m-d');   

				$total=0;
				$rules = ['Please enter postal vote'];
				for ($i=1; $i<=$val;$i++)
				    {
				     $this->validate($request, ['currentvote'.$i => 'required|numeric|digits_between:0,999999|','totalvotes'=> 'required|numeric|digits_between:0,999999','rejectedvotes'=> 'required|digits_between:0,999999',
				     	'tended_votes'=> 'required|digits_between:0,999999'],
		                [
		                'currentvote'.$i.'required' => 'Please enter postal vote ',
		                'currentvote'.$i.'regex' => 'Please enter valide votes',
		                'currentvote'.$i.'numeric' => 'Please enter valide votes',
		                'currentvote'.$i.'integer' => 'Please enter valide votes',
		                'currentvote'.$i.'digits_between' => 'Please enter valide votes',
		                'totalvotes.required' => 'Please enter Total Votes',
		                'totalvotes.digits_between' => 'Please enter valide votes',
		                'rejectedvotes.required' => 'Please enter Total Rejected Votes',
		                'rejectedvotes.digits_between' => 'Please enter valide votes',
		                'tended_votes.required' => 'Please enter Total tended  Votes',
		                'tended_votes.digits_between' => 'Please enter valide votes',
		                ]);	
			        }
			    for ($i=1; $i<=$val;$i++)
				    {
				    	$cv=trim($request->input('currentvote'.$i));
				        $total=$total+$cv;
				     }
			      $total=$total+$rejectedvotes;

			    if($totalvotes== $total)  {
        DB::beginTransaction();
        	try{
        		$mango_db_array=[];
			    	$new_table=strtolower("counting_master_".$d->st_code);
 				for ($i=1; $i<=$val;$i++)
			       	{
			       	$mid=trim($request->input('mid'.$i));
			       	$nom_id=trim($request->input('nom_id'.$i));
			       	$currentvote=trim($request->input('currentvote'.$i));
			       	$priviousvote=trim($request->input('priviousvote'.$i));
			       		$filter_ele='';
			         	$filter_ele = 	[	'id'=>$mid,
			         						'nom_id'=>$nom_id,
			         						'ac_no'=> $d->ac_no
			         					];
				       $total_value='';
			         $total_value=$this->CountingModel->grandtotalsum($new_table,'round1',$filter_ele);
                         
				        $total_vote   = 0; 
				        $postal_vote=0;

           			    if(isset($total_value) && $total_value){
				            $total_vote   = $total_value->grant_total;
				            
				          }
				       $total_vote= $total_vote+$currentvote;
			          // $total_vote=$priviousvote+$currentvote;
			       		$n_data = array('total_vote'=>$total_vote,'postalballot_vote'=>$currentvote,
			       						'added_update_at'=>date("Y-m-d"),'updated_at'=>date("Y-m-d h:i:s"),
			       						'updated_by'=>$d->officername); 

			        DB::table($new_table)->where('id',$mid)->update($n_data);	

			      	 $data22 = ["add_postal_vote"=>$currentvote,'total_vote'=>$total_vote, "nom_id"=>$nom_id, "st_code"=>$ST_CODE, "ac_no"=>$d->ac_no];
	              	  $mango_db_array[]=$data22;

	              }
	                // $mongo_data_array[] =["add_postal_vote"=>12000, "total_vote"=>13000, "nom_id"=>1355, "st_code"=>"S23", "ac_no"=>4];

	                 //dd($pcentry);
	              if( $this->mongo_sync){  
	              	  updatePostalByIdAc($mango_db_array);
				      //End API
				  }

			        $data = array('rejected_votes'=>$rejectedvotes,'postal_total_votes'=>$totalvotes,'tended_votes'=>$tended_votes); 

			        DB::table('round_master')->where('id',$round_id)->update($data);

 
			         $sdata=$this->CountingModel->selectsecondhightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
                     $fdata=$this->CountingModel->selectfirsthightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$CONST_TYPE,$ELECTION_ID);
			      // if(isset($fdata) and isset($sdata) and ($fdata->max_total !=$sdata->max_total)){
						    $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
						    $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
						    $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
					
				 
		                $trail_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
					    $trail_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
					    $trail_party=$this->commonModel->selectone('m_party','CCODE',$trail_nom->party_id);
					 

				    $margin=$fdata->max_total-$sdata->max_total;
				    $winn_update=array('candidate_id'=>$fdata->candidate_id,'nomination_id'=>$fdata->nom_id,'lead_cand_name'=>$lead_cand->cand_name,'lead_cand_partyid'=>$lead_party->CCODE,'lead_cand_party'=>$lead_party->PARTYNAME,'lead_party_type'=>$lead_party->PARTYTYPE,'lead_party_abbre'=>$lead_party->PARTYABBRE,'lead_cand_hname'=>$lead_cand->cand_hname,'lead_cand_hparty'=>$lead_party->PARTYHNAME,'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    	'trail_candidate_id'=>$sdata->candidate_id,'trail_nomination_id'=>$sdata->nom_id,'trail_cand_name'=>$trail_cand->cand_name,'trail_cand_partyid'=>$trail_party->CCODE,'trail_cand_party'=>$trail_party->PARTYNAME,'trail_party_type'=>$trail_party->PARTYTYPE,'trail_party_abbre'=>$trail_party->PARTYABBRE,'trail_cand_hname'=>$trail_cand->cand_hname,'trail_cand_hparty'=>$trail_party->PARTYHNAME,'trail_hpartyabbre'=>$trail_party->PARTYHABBR,'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,'trail_total_vote'=>$sdata->max_total,'added_update_at'=>$currentdate,'updated_at'=>$currentTime);
				      //dd($winn_update);

				     DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update);
				    // }
				    //  else{
        //               	 $winn_update=array('candidate_id'=>0,'nomination_id'=>0,'lead_cand_name'=>'','lead_cand_partyid'=>0,'lead_cand_party'=>'','lead_party_type'=>'','lead_party_abbre'=>'','lead_cand_hname'=>'','lead_cand_hparty'=>'','lead_hpartyabbre'=>'',
				    // 		'trail_candidate_id'=>0,'trail_nomination_id'=>0,'trail_cand_name'=>'','trail_cand_partyid'=>0,'trail_cand_party'=>'',
				    // 		'trail_party_type'=>'','trail_party_abbre'=>'','trail_cand_hname'=>'','trail_cand_hparty'=>'','trail_hpartyabbre'=>'',
				    // 		'margin'=>0,'lead_total_vote'=>0,'trail_total_vote'=>0,'added_update_at'=>$currentdate,'updated_at'=>$currentTime);
				    //  	 //dd($winn_update);
				    //  	DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update);
        //               }

				       if( $this->mongo_sync){
                     // API of Mango Node JS 
				     $winn_update1=array('st_code'=>$ST_CODE,'ac_no'=>$CONST_NO,'candidate_id'=>$fdata->candidate_id,
				     					'nomination_id'=>$fdata->nom_id,
				     					'lead_cand_name'=>$lead_cand->cand_name,
				     					'lead_cand_hname'=>$lead_cand->cand_hname,
				     					'lead_cand_partyid'=>$lead_party->CCODE,
				     					'lead_cand_party'=>$lead_party->PARTYNAME,
				     					'lead_party_type'=>$lead_party->PARTYTYPE,
				     					'lead_party_abbre'=>$lead_party->PARTYABBRE,
				     					'lead_cand_hparty'=>$lead_party->PARTYHNAME,
				     					'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    					'trail_candidate_id'=>$sdata->candidate_id,
				    					'trail_nomination_id'=>$sdata->nom_id,
				    					'trail_cand_name'=>$trail_cand->cand_name,'trail_cand_hname'=>$trail_cand->cand_hname,
				    					'trail_cand_partyid'=>$trail_party->CCODE,'trail_cand_party'=>$trail_party->PARTYNAME,
				    					'trail_party_type'=>$trail_party->PARTYTYPE,'trail_party_abbre'=>$trail_party->PARTYABBRE,
				    					'trail_cand_hparty'=>$trail_party->PARTYHNAME,'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    					'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,'trail_total_vote'=>$sdata->max_total);
				     updateWinningLeadingAc($winn_update1);
				     //End API
                     }
                    }
				    catch(\Exception $e){
		            	DB::rollback();
		    
			            \Session::flash('error_mes', 'Please try again');
			            return Redirect::back();
				        }
				        DB::commit();
			    			

			    			\Session::flash('success_mes', 'This Postal Vote Successfully Updated.');
	                		return Redirect::to('/roac/counting/postal-data-entry');	        
		        }
		        else {
                     \Session::flash('error_mes', 'Total Votes and candidate Vote Miss-Match');
                		return Redirect::to('/roac/counting/postal-data-entry');	
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
			 	$new_table=strtolower("counting_master_".$d->st_code);
	            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		           
       	        $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
       	        $round_details=DB::table('round_master')->select('finalized_ac')->where('st_code', $ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
              
				if((!isset($round_details)) || $round_details->finalized_ac==0){
					 		\Session::flash('error_mes', 'EVM Rounds not finalize! please finalize first.');
		                		return Redirect::to('/roac/counting/counting-data-entry');	
					 }
              

               if($check_finalize->finalized_ac =='0'){
               			\Session::flash('error_mes', 'Cantest Candidate  Not finalized Please finalized first');
                   		return Redirect::to('/roac/counting/prepare-counting');
 		       }


 		       $val = $this->CountingModel->checkallacfinalize($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
           
 		    	$check_finalized_ro = $this->CountingModel->check_finalized_ro([
			 		'state' 		=> $ele_details->ST_CODE,
			 		'ac_no' 		=> $ele_details->CONST_NO,
			 		'election_id' 	=> $ele_details->ELECTION_ID
			 	]);	

                

 		      	if($val == 1 || $check_finalized_ro){
			 	    \Session::flash('error_mes', 'EVM and Postal Votes may not be finalized. Please finalize same in order to Declare the Result.');
                	return Redirect::to('/roac/counting/postal-data-entry');	
			    }
			   
               	$filter='';  $filter_m='';
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    ];
               
               	$filter_m 	= [
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' 	=> 'id', 
	       	    ];
				
				$round_details 	=	$this->CountingModel->roundsechudle($filter);
                $winn_data 		=	$this->CountingModel->winn_lead($filter);  
        		$master_data	=	$this->CountingModel->master_records( $new_table,$filter_m);  

                //dd($master_data);
              	if(!isset($round_details)) {
                   \Session::flash('success_admin', 'Round Schedule Not Created! Please Create to roundschedule');
                   return Redirect::to('roac/counting/round-schedule');
                }   
           
			return view('admin.ac.counting.counting-results',['user_data' => $d, 'roac'=>$byro,'ele_details'=>$ele_details,'master_data'=>$master_data, 'val'=>$val,'winn_data'=>$winn_data,'finalize'=>$check_finalized_ro,'round_details'=>$round_details]);
		 	           
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
			    $new_table=strtolower("counting_master_".$d->st_code);
			 	 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		          
       	        $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
			  
		         $filter='';  $filter_m='';
			    	
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
               
               $filter_m 	= [
	       	     
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' 	=> 'id', 
	       	    		];
				
				$round_details=$this->CountingModel->roundsechudle($filter);
                $winn_data=$this->CountingModel->winn_lead($filter);  
        		$master_data=$this->CountingModel->master_records( $new_table,$filter_m);  
        

         
			   //if($round_details->finalized_ac=='0')
			   	//	{
				 //			\Session::flash('error_mes', 'Evm votes not finalize');
	            //    			return Redirect::to('roac/counting/counting-data-entry');	
				//  	}
		         // if($round_details->postal_total_votes==0) {
		        //	 \Session::flash('error_mes', 'Postal data not Entered');
		         //    return Redirect::to('/roac/postal-data-entry'); 
		        //	}
         
	     
			return view('admin.ac.counting.counting_finalize',['user_data' => $d, 'roac'=>$byro,'ele_details'=>$ele_details,'master_data'=>$master_data, 'otp'=>'0','finalize'=>$round_details->finalized_ac,'winn_data'=>$winn_data,'st_code'=>$ele_details->ST_CODE,'ac_no'=>$ele_details->CONST_NO]);	           
		           
		        }
		        else {
		              return redirect('/officer-login');
		        	 }
	}

	// winner candidate name verifying
	public function verify_winner_by_name(Request $request){
		$data = [];
		if(!Auth::user()){
			$data['warning'] = "Please login to continue.";
		}		

		if(!$request->has('winner_name') || trim($request->winner_name) == ""){
			$data['warning'] = "Please enter the winner name.";
		}

		if(count($data)>0){
			return \Response::json([
				'status'  => false,
				'message' => $data['warning']
			]);
		}
		
		$filter = [
			'st_code' 	=> Auth::user()->st_code,
			'ac_no' 	=> Auth::user()->ac_no,
			'status'	=> '0',
		];

		$result = DB::table("winning_leading_candidate")->where($filter)->first();

		if(!isset($result) && !$result){
			return \Response::json([
				'status'  => false,
				'message' => "Please try again."
			]);
		}

		if(strtolower(trim($request->winner_name)) != strtolower(trim($result->lead_cand_name))){
			return \Response::json([
				'status'  => false,
				'message' => "Winner name incorrect. Please enter the correct winner name."
			]);
		}

		return \Response::json([
			'status'  => true,
			'message' => ""
		]);

	}

	public function result_declared_by_lottery(Request $request){

		if(!$request->has('draw_leading_nomination_id') || !$request->has('draw_trailing_nomination_id')){
			return \Response::json([
				'status'  => false,
				'message' => "Winner and loser both are required."
			]);
		}

		if(trim($request->draw_leading_nomination_id) == ''){
			return \Response::json([
				'status'  => false,
				'message' => "Please select a winner."
			]);
		}

		if(trim($request->draw_trailing_nomination_id) == ''){
			return \Response::json([
				'status'  => false,
				'message' => "Please select a loser."
			]);
		}

		if($request->draw_leading_nomination_id == $request->draw_trailing_nomination_id){
			return \Response::json([
				'status'  => false,
				'message' => "Winner and loser can't be same."
			]);
		}

		

		$ele_details = $this->commonModel->election_detailsac(Auth::user()->st_code, Auth::user()->ac_no, Auth::user()->dist_no, Auth::id(),'AC');

		if(!$ele_details){
			return \Response::json([
				'status'  => false,
				'message' => "Please refresh page and try again."
			]);
		}

		$user_data 	= [
			'ac_no' 		=> Auth::user()->ac_no,
			'election_id' 	=> $ele_details->ELECTION_ID,
		];

		$table_name = 'counting_master_'.strtolower(Auth::user()->st_code);

		$object_leading = DB::table($table_name.' as t1')->leftJoin('m_party','m_party.CCODE','=','t1.party_id')->where(array_merge($user_data,[
			'nom_id' => $request->draw_leading_nomination_id
		]))->select('t1.*','m_party.PARTYTYPE')->first();

		$object_trailing = DB::table($table_name.' as t1')->leftJoin('m_party','m_party.CCODE','=','t1.party_id')->where(array_merge($user_data,[
			'nom_id' => $request->draw_trailing_nomination_id
		]))->select('t1.*','m_party.PARTYTYPE')->first();

		if(!$object_leading || !$object_trailing){
			return \Response::json([
				'status'  => false,
				'message' => "Please refresh page and try again."
			]);
		}

		if($object_leading->party_id == '1180' || $object_trailing->party_id == '1180'){
			return \Response::json([
				'status'  => false,
				'message' => "Nota can't be in winner or traling."
			]);
		}

		$user_data_for_update = [
			'st_code' 	=> Auth::user()->st_code,
			'ac_no'     => Auth::user()->ac_no,
			'election_id' 	=> $ele_details->ELECTION_ID,
		];

			DB::table('winning_leading_candidate')->where($user_data_for_update)->update([
				'candidate_id' 		=> $object_leading->candidate_id, 
				'nomination_id' 	=> $object_leading->nom_id, 
				'lead_cand_name' 	=> $object_leading->candidate_name, 
				'lead_cand_hname' 	=> $object_leading->candidate_hname,
				'lead_cand_partyid' => $object_leading->party_id,
				'lead_cand_party' 	=> $object_leading->party_name, 
				'lead_cand_hparty' 	=> $object_leading->party_hname, 
				'lead_party_type' 	=> $object_leading->PARTYTYPE,
				'lead_party_abbre' 	=> $object_leading->party_abbre, 
				'lead_hpartyabbre' 	=> $object_leading->party_habbre, 
				'trail_candidate_id' => $object_trailing->candidate_id, 
				'trail_nomination_id' => $object_trailing->nom_id, 
				'trail_cand_name' 		=> $object_trailing->candidate_name, 
				'trail_cand_hname' 		=> $object_trailing->candidate_hname, 
				'trail_cand_partyid' 	=> $object_trailing->party_id, 
				'trail_cand_party' 		=> $object_trailing->party_name, 
				'trail_cand_hparty' 	=> $object_trailing->party_hname, 
				'trail_party_type' 		=> $object_trailing->PARTYTYPE, 
				'trail_party_abbre' 	=> $object_trailing->party_abbre, 
				'trail_hpartyabbre' 	=> $object_trailing->party_habbre, 
				'lead_total_vote' 	=> $object_leading->total_vote, 
				'trail_total_vote' 	=> $object_trailing->total_vote, 
				'margin' => 0, 
				'status' => '1', 
				'is_lottery' => 1
			]);
		try{}catch(\Exception $e){
			return \Response::json([
				'status'  => false,
				'message' => "Please refresh page and try again."
			]);
		}

		\Session::flash('success_mes', 'Result successfully updated.');
		return \Response::json([
			'status'  => true,
			'message' => "Result successfully updated."
		]);

	}


	//end of winner candidate verifying

	function results_declaration(Request $request){

		if(Auth::check()){
			    $user = Auth::user();
			 	$d=$this->commonModel->getunewserbyuserid($user->id);
	             $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		      
		    	 $filter='';  
			    	
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
                $winn_data=$this->CountingModel->winn_lead($filter);  
       		  
		 		$val=$this->CountingModel->checkallacfinalize($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);

		 		$check_finalized_ro = $this->CountingModel->check_finalized_ro([
			 		'state' 		=> $ele_details->ST_CODE,
			 		'ac_no' 		=> $ele_details->CONST_NO,
			 		'election_id' 	=> $ele_details->ELECTION_ID
			 	]);	

				    
			 if($val==1 || $check_finalized_ro){
			 	\Session::flash('error_mes', 'All AC Not finalized Please finalized first');
                		return Redirect::to('/roac/counting/postal-data-entry');	
			 }
 			$date = Carbon::now()->subMinutes(10);
            $currentTime = $date->format('Y-m-d H:i:s');
            $currentdate = $date->format('Y-m-d');
			 $n_data=array('status'=>'1','added_update_at'=>$currentdate,'updated_at'=>$currentTime);
			 
			 $leading_id=$request->input('leading_id');
           DB::beginTransaction();
        		try{  
			 
            $this->commonModel->updatedata('winning_leading_candidate','leading_id',$leading_id,$n_data);
            

             $filter11 = [
			'st_code' 	=> $ele_details->ST_CODE,
			'ac_no' 	=>$ele_details->CONST_NO,
			'status'	=> '1'
		     ];
		      if( $this->mongo_sync){
				     // API of Mango Node JS 	
                    updateWinningLeadingStatusAc($filter11);
                   // End Api
                }
              DB::commit();
    		 }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
		            return Redirect::back();
		        }
            
			     \Session::flash('success_mes', 'Result is declared successfully.');
	                		return Redirect::to('/roac/counting/counting-results');     	
		           
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
			}
   
     function finalize_evm_rounds(Request $request)
    		{

    			

    		if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
           

			$new_table=strtolower("counting_master_".$d->st_code);
			$ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
			 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		     

		$filter='';  $filter_m='';
			    	
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
               
              
				
				$round_details=$this->CountingModel->roundsechudle($filter);
                 
        $c_data=DB::table($new_table)->select('complete_round','finalized_round')->where('ac_no', $d->ac_no)->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->first();
         
        $complete_round=0; $finalized_round=0;
         if(isset($c_data)){
         	$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
            }
        if($round_details->scheduled_round==$complete_round)
		   {  
		   	$n_data = array('finalized_round'=>'1','updated_by'=>$d->officername ,'updated_at'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d"));
		   	$c = array('finalized_ac'=>'1','updated_by'=>$d->officername ,'updated_at'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d")); 
		   	 $c1 = array('finalized_ac'=>'1');  
			 DB::table('counting_finalized_ac')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$d->ac_no)->where('election_id',$ele_details->ELECTION_ID)->update($c1); 
			DB::table($new_table)->where('ac_no',$d->ac_no)->where('ELECTION_ID',$ele_details->ELECTION_ID)->update($n_data);
			DB::table('round_master')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$d->ac_no)->where('ELECTION_ID',$ele_details->ELECTION_ID)->update($c);
			 
              
		   	 \Session::flash('success_admin', 'Evm Rounds Successfully finalized');
             return Redirect::to('/roac/counting/counting-data-entry'); 
		   	} 
         else {
            \Session::flash('error_mes', 'All rounds not completed, Please Complete your rounds then finalized');
             return Redirect::to('/roac/counting/counting-data-entry');	      
               		   
            } 
			   
		  }
		   else {
		         return redirect('/officer-login');
		         }	
    		}
     
   	
   	function round_wise_entry()
   			{
   			if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
                $new_table=strtolower("counting_master_".$d->st_code);
			    $ac_details=$this->commonModel->getacbyacno($d->st_code,$d->ac_no);
				
				 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $filter='';  $filter_m='';
			    	
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
               
               $filter_m 	= [
	       	     
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' 	=> 'id', 
	       	    		];
				
				$round_details=$this->CountingModel->roundsechudle($filter);
                $winn_data=$this->CountingModel->winn_lead($filter);  
        		$master_data=$this->CountingModel->master_records( $new_table,$filter_m); 

              if(!isset($round_details)) {
                			\Session::flash('success_admin', 'Round Schedule Not Created! Please Create to roundschedule');
                			 return Redirect::to('roac/counting/round-schedule');
                }  
        		 
        	 
        		        

         
		 		return view('admin.ac.counting.round-wise-entry',['user_data' => $d, 'ac_details'=>$ac_details,'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'new_table'=>$new_table,'winn_data'=>$winn_data]);	
			 
               }
		        else {
		              return redirect('/officer-login');
		        	  }	
   			}	
   	
   	function counting_finalized_verify(Request $request)
    		{

    			


    		if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			     $new_table=strtolower("counting_master_".$d->st_code);
       			 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
       		 	$round_details=DB::table('round_master')->where('st_code', $ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
              
		// if($round_details->finalized_ac==0){
		// 	 		\Session::flash('error_mes', 'EVM Rounds not finalize! please finalize first.');
  //               		return Redirect::to('/roac/counting/counting-data-entry');	
		// 	 }
	 
		//if($round_details->postal_total_votes==0) {
        //	 \Session::flash('error_mes', 'Postal data not Entered');
          //   return Redirect::to('/roac/postal-data-entry'); 
        //	}
		  
		     $c1 = array('finalize_by_ro'=>'1','finalize_date'=>date("Y-m-d"),'updated_at_ro'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d"),'updated_at'=>date("Y-m-d H:i:s") ,'updated_by'=>$d->officername);  
			 DB::table('counting_finalized_ac')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->update($c1);
		   	 \Session::flash('success_admin', 'Successfully finalized.');
             return Redirect::to('/roac/counting/postal-data-entry'); 
		    
          
			   
		  }
		   else {
		         return redirect('/officer-login');
		         }	
    	}	

    public function prepare_counting(){   

	    $heading_title = "To Start Counting Process ROAC Needs to Activate Counting.";
	    
	    if(Auth::check()){



		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $new_table=strtolower("counting_master_".$d->st_code);



			  $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		      $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		       if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
		           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
		           }
		      $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);

       	     
       	      $filter='';  $filter_m='';
			    $filter_m 	= [
	       	      	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' 	=> 'id', 
	       	    	];
				$counting_data=$this->CountingModel->master_records( $new_table,$filter_m);  
	 		 
		    return view('admin.ac.counting.prepear_counting',['user_data' => $d,'cand_finalize_ceo' =>$cand_finalize_ceo,'cand_finalize_ro' =>$cand_finalize_ro, 
		    	'byro'=>$byro,'counting_data'=>$counting_data,'ac_no'=>$ele_details->CONST_NO,'st_code'=>$ele_details->ST_CODE,'ele_details'=>$ele_details,
		    	'heading_title' => $heading_title
		    ]);	           
	        }
	        else {
	              return redirect('/officer-login');
	        	  }
	    }  // end index function     createcenter	


	 function activate_allac()
   			{   
   			if(Auth::check()){ 
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
            	 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		       
				   $seched=getschedulebyid($ele_details->ScheduleID);
		           $sechdul=checkscheduledetails($seched);
       	        $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
				
		    	$new_table=strtolower("counting_master_".$d->st_code);
		    	
		    	$date = Carbon::now();
             	$currentTime = $date->format('Y-m-d H:i:s');
             	$currentdate = $date->format('Y-m-d');
	    	if($ele_details->CONST_TYPE!='AC') {
	    		\Session::flash('error_mes', 'Election Sechedule not define');
	             return Redirect::to('/roac/counting/prepare-counting'); 
	    		}
			 
			$record=$this->CountingModel->getallacbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
			  
			$cand_data=$this->CountingModel->cantestesting_nomination($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);

	 		if (Schema::hasTable($new_table))
				{   
				  //echo "ok";
				} 
			else {
			    \DB::statement('CREATE TABLE '.$new_table.' LIKE counting_master_stcode');
                }
         DB::beginTransaction();
        		try{ 
         foreach($cand_data as $list){
			    $check = DB::table($new_table)->where('nom_id',$list->nom_id)
					->where('ac_no',$list->ac_no)->where('election_id',$list->election_id)->first();
					$lis_ac=$this->commonModel->getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
				if(!isset($check))
				  {
					$can=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
					$p=getpartybyid($list->party_id);
					//dd($p);
                	$ca_data = array('nom_id'=>$list->nom_id,
                		'candidate_id'=>$list->candidate_id,
                		'ac_no'=>$list->ac_no,
                		'dist_no'=>$lis_ac->DIST_NO_HDQTR,
                        'new_srno'=>$list->new_srno,
                		'election_id'=>$list->election_id,
                		'created_at'=>$currentTime,
                		'created_by'=>$d->officername,
                		'added_create_at'=>$currentdate,
                		'candidate_name'=>$can->cand_name,
                		'party_id'=>$list->party_id,
                		'party_abbre'=>$p->PARTYABBRE,
                		'party_name'=>$p->PARTYNAME,
                		'candidate_hname'=>$can->cand_hname,
                		'party_habbre'=>$p->PARTYHABBR,
                		'party_hname'=>$p->PARTYHNAME); 
                    $this->commonModel->insertData($new_table, $ca_data);
                   }
				 
			 }
				$lis_st=$this->commonModel->getstatebystatecode($ele_details->ST_CODE);
        		$lis_ac=$this->commonModel->getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);

			$check_d=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();   
			if(!isset($check_d)){	  
			 $winn_data=array('election_id'=>$ele_details->ELECTION_ID,
			 	'constituency_type'=>$ele_details->CONST_TYPE,
			 		'st_code'=>$ele_details->ST_CODE,
			 		'st_name'=>$lis_st->ST_NAME,
			 		'st_hname'=>$lis_st->ST_NAME_HI,
			 		'ac_no'=>$ele_details->CONST_NO,
			 		'dist_no'=>$lis_ac->DIST_NO_HDQTR,
			 		'ac_name'=>$lis_ac->AC_NAME,
			 		'ac_hname'=>$lis_ac->AC_NAME_HI,
			 		'created_at'=>$currentTime,
			 		'added_create_at'=>$currentdate);
			   $this->commonModel->insertData('winning_leading_candidate', $winn_data);
	          } 
	          DB::commit();  
	           }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
		            return Redirect::back();
		        }
		          
        		\Session::flash('success_admin', 'Counting data Successfully added');
                 return Redirect::to('/roac/counting/prepare-counting');
			   
		  		}
		   else {
		         return redirect('/officer-login');
		         }	
    		}	

    public function counting_dashboard(){

    	$heading_title = "Please Activate Counting in order to see the Counting Data.";

    		if(Auth::check()){ 
			     $user = Auth::user();
			     $d=$this->commonModel->getunewserbyuserid($user->id);
                 $new_table=strtolower("counting_master_".$d->st_code);
			     $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');


			    if(isset($ele_details)){
			    	$filter=''; $filter_m='';
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
                $filter_m 	= [
	       	     
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' 	=> 'id', 
	       	    ];
				$round_details=$this->CountingModel->roundsechudle($filter);
                 
        		$winn_data=$this->CountingModel->winn_lead($filter);  
        		 
        		$master_data=$this->CountingModel->master_records($new_table,$filter_m);        
                
          
		 		return view('admin.ac.counting.counting-dashboard',['user_data' =>$d, 'ele_details'=>$ele_details,'round_details'=>$round_details,'master_data'=>$master_data,'winn_data'=>$winn_data,
		 			'new_table' 	=>$new_table,
		 			'heading_title' => $heading_title
		 		]);	
			    }
		        else {
		              return redirect('/logout');
		        	  }	
               }
		        else {
		              return redirect('/officer-login');
		        	  }	
    			}

    	public function round_wise_details(){

    		$heading_title = "Please Activate Counting in order to see the Counting Data.";

		     if(Auth::check()){
			     $user = Auth::user();
			     $d=$this->commonModel->getunewserbyuserid($user->id);
				 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
				 $new_table=strtolower("counting_master_".$d->st_code);
				  
				 $filter=''; $filter_m='';
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
                $filter_m 	= [
	       	     
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' 	=> 'id', 
	       	    ];
				$round_details=$this->CountingModel->roundsechudle($filter);
                 
        		$winn_data=$this->CountingModel->winn_lead($filter);  
        		 
        		$result=$this->CountingModel->master_records( $new_table,$filter_m);   

                 
			     return view('admin.ac.counting.counting-details',['user_data' => $d,'rounds'=>$round_details,'result'=>$result, 'ele_details'=>$ele_details, 'winn_data'=>$winn_data,
			     	'heading_title' => $heading_title
			 ]);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
		    }  // end 


	public function pdf(Request $request){

		if(!\Auth::user()){
			return false;
		}

        if($request->has('print_table') && $request->has('ac_no') && $request->has('round')){
            \Session::put('print_table',$request->print_table);
            \Session::put('ac_no',$request->ac_no);
            \Session::put('round',$request->round);
        }

        $d 				= \Auth::user();
		$ele_details 	= $this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');

        if(\Session::has('print_table') && \Auth::user()){
        	$st_name = '';
        	$state_object = \App\models\Admin\StateModel::get_state_by_code(\Auth::user()->st_code);
        	if($state_object){
        		$st_name = $state_object['ST_NAME'];
        	}
            $data = [];
            $data['table']  = \Session::get('print_table');
            $data['ac_no']  = \Session::get('ac_no');

            $get_ac 		= $this->commonModel->getacbyacno(\Auth::user()->st_code,$data['ac_no']);
            $ac_name = '';
            if($get_ac){
            	$ac_name = $get_ac->AC_NAME;
            }

            $data['ac_name']       	= $ac_name;
            $data['round']         	= \Session::get('round');
            $data['heading_title'] 	= '';
            $data['st_name'] 		= $st_name;
            $data['election'] 		= "AC-".@$ele_details->ELECTION_TYPE;
            $data['st_code']        = \Auth::user()->st_code;
            

            $name_excel = 'round'.$data['round'].'_'.$data['ac_no'];
           	//round to be sum and print previous
            $object = $this->CountingModel->get_previous_total($data);
        	
        	$nominator = [];
        	foreach (explode(',',$data['table']) as $key => $value) {
        		$explode_array = explode('_', $value);
        		$nominator[$explode_array[0]] = [
        			'nom_id' => $explode_array[0],
        			'vote'   => $explode_array[1]
        		];
        	}

        	$i = 1;
        	$aggregate_total 			= 0;
        	$aggregate_previous_total 	= 0;
        	$aggregate_current_total 	= 0;
        	foreach ($object as $result) {
        		$current_total 	= 0;
        		$total 			= 0;
        		if(isset($nominator[$result->nom_id])){
        			$current_total 	= $nominator[$result->nom_id]['vote'];
        			$total 			= $result->previous_total+$nominator[$result->nom_id]['vote'];
        		}
        		$results[] = [
        			'sr_no' 			=> $i,
        			'candidate_name' 	=> $result->candidate_name,
        			'party_name'  		=> $result->party_name,
        			'total'  			=> format_digit($total),
        			'previous_total'  	=> format_digit($result->previous_total),
        			'current_total'  	=> format_digit($current_total),
        		];
        		$aggregate_total 			+= $total;
        		$aggregate_previous_total 	+= $result->previous_total;
        		$aggregate_current_total 	+= $current_total;
        		$i++;
        	}

        

        	$results[] = [
        		'sr_no' 			=> '',
        		'candidate_name' 	=> '',
        		'party_name'  		=> 'Total',
        		'total'  			=> format_digit($aggregate_total),
        		'previous_total'  	=> format_digit($aggregate_previous_total),
        		'current_total'  	=> format_digit($aggregate_current_total),
        	];

        	$data['results'] = $results;

             $setting_pdf = [
				'margin_top'        => 80,        // Set the page margins for the new document.
				'margin_bottom'     => 10,    
			];


            $pdf = \PDF::loadView('admin.ac.counting.pdf',$data,[], $setting_pdf);

          
            if($request->has('json')){
                return \Response::json([
                    'success' => true
                ]);
            }
            return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
        }else{
            return \Redirect::to('/officer-login');
        }
    }

    public function ballot_pdf(Request $request){

    	if(!\Auth::user()){
			return false;
		}
		
        if($request->has('print_table') && $request->has('ac_no') && $request->has('round')){
            \Session::put('print_table',$request->print_table);
            \Session::put('ac_no',$request->ac_no);
            \Session::put('round',$request->round);
        }

        $d 				= \Auth::user();
		$ele_details 	= $this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');

        if(\Session::has('print_table') && \Auth::user()){
        	$st_name = '';
        	$state_object = \App\models\Admin\StateModel::get_state_by_code(\Auth::user()->st_code);
        	if($state_object){
        		$st_name = $state_object['ST_NAME'];
        	}

            $data = [];
            $data['table']  = \Session::get('print_table');
            $data['ac_no']  = \Session::get('ac_no');
            $get_ac 		= $this->commonModel->getacbyacno(\Auth::user()->st_code,$data['ac_no']);
            $ac_name = '';
            if($get_ac){
            	$ac_name = $get_ac->AC_NAME;
            }

            $data['ac_name']     	= $ac_name;
            $data['round']         	= \Session::get('round');
            $data['heading_title'] 	= '';
            $data['st_name'] 		= $st_name;
            $data['election'] 		="AC-".@$ele_details->ELECTION_TYPE;;

            $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
             $setting_pdf = [
				'margin_top'        => 80,        // Set the page margins for the new document.
				'margin_bottom'     => 10,    
			];

            $pdf = \PDF::loadView('admin.ac.counting.ballot_pdf',$data, [], $setting_pdf);
            if($request->has('json')){
                return \Response::json([
                    'success' => true
                ]);
            }
            return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
        }else{
            return \Redirect::to('/officer-login');
        }
    }


    public function tenders_votes(Request $request)
    		   {
    			 if(Auth::check()){
    		    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
				 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
				$this->validate(
	                $request, 
	                    [
	                      'tended_votes'=> 'required|digits_between:0,999999',
	                    ],
	                    [
	                      'tended_votes.required' => 'Please enter round schedule ',
	                      'tended_votes.digits_between' => 'Please enter numeric value',
	                      
	                    ]);  
				$st_code=$d->st_code;
				$ac_no=$d->ac_no;
				$tended_votes=$this->xssClean->clean_input($request->input('tended_votes'));
				 DB::beginTransaction();
        		try{ 
				   $n_data = array('tended_votes'=>$tended_votes,'updated_at'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d"),'tended'=>1); 
			       DB::table('round_master')->where('st_code',$st_code)->where('ac_no',$ac_no)->update($n_data);	  
				DB::commit(); 
				}
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Please try again');
		            return Redirect::back();
		        }
		         

				  \Session::flash('success_mes', 'Tendered Votes updated Successfully.');
              	  return Redirect::to('/roac/counting/counting-results');	         
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
		    }  // end 
		 
}  // end class results-declaration   
