<?php  
		namespace App\Http\Controllers\Admin\counting;
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
		use Illuminate\Support\Facades\Schema;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
		use App\models\Counting\BoothCountingModel;
		use App\models\Counting\PostalCountingModel;
		use App\models\Counting\PostalBallotResultsPublishModel;
		use App\models\Counting\CountingFinalizePublishModel;
		use App\models\Counting\UsercountingModel; 
		use App\Helpers\LogNotification; 
class PostalCountingController extends Controller
{   
    public $base    = 'roac';
    public $folder  = 'counting';
    public $action    = 'roac/counting/';
    public $view_path = "admin.counting.ro";

    public function __construct()
    	{
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
         $this->middleware('ro_only'); 
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->boothcounting=new BoothCountingModel;
        $this->users=new UsercountingModel;
        $this->postal = new PostalCountingModel();
		        if(!Auth::check()){ 
		          return redirect('/officer-login');
		      }
  		}

  protected function guard(){
  	    return Auth::guard('admin');
	}

    
	function evm_votes_finalized(Request $request) {  
			    $data=[];  
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id); 
			    $new_table=strtolower("counting_master_".$d->st_code);
			    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $data['user_data']=$d;
		        $data['new_table']=$new_table;
		        $data['ele_details']=$ele_details;

				      $filter='';  
 		        		$filter 	= [
	       	    				'st_code' 	=> $ele_details->ST_CODE,
	       	    				'ac_no' 	=> $ele_details->CONST_NO,
	       	    				'election_id' 	=> $ele_details->ELECTION_ID,
	       	    				'order_by' 	=> 'id', 
	       	   				 ];
	       	    		

	       	   	$evm_finalized=evm_votes_finalized($filter);
    			

				$round_details=$this->postal->roundsechudle($filter);
                $winn_data=$this->postal->winn_lead($filter);  
        		$master_data=$this->postal->master_records( $new_table,$filter);  

        		$c_data=DB::table($new_table)->select('complete_round','finalized_round')
        					->where('ac_no', $d->ac_no)
        					->where('election_id',$ele_details->ELECTION_ID)
        					->orderBy('id')->first();
        		if(!isset($round_details)) {
		            \Session::flash('success_admin', 'Round Schedule Not Created! Please Create to roundschedule');
		            return Redirect::to('roac/counting/round-schedule-details');
		           }   
        
                $complete_round=0; $finalized_round=0;
		        if(isset($c_data)){
		         	$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
		        }
		             
		         
		         if($round_details->scheduled_round==$complete_round)
				   {
					   $empty_ps_finalized=empty_ps_finalized($filter);
					   if($empty_ps_finalized==0){
						\Session::flash('error_mes', 'Please verify all empty polling stations before finalize evm votes.');
						return Redirect::to('roac/counting/check-empty-ps-details');
					   }
					   
				 
        			 $st=getstatebystatecode($ele_details->ST_CODE);  
         			 $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);  
                    $data['comp_round']  =$complete_round;
					$data['master_data']  =$master_data;
					$data['winn_data']  =$winn_data;
					$data['st_code']  =$ele_details->ST_CODE;			 
					$data['ac_no']  =$ele_details->CONST_NO;	 
					$data['round_details']  =$round_details; 
			        $data['st_name']   = $st->ST_NAME;
			        $data['ac_name']   = $ac->AC_NAME;
                    $data['evm_finalized']   = $evm_finalized;
                    //dd($data);
					

							if(config('public_config.isCountingLoggerEnable')){
								$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
								$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
								$ErrorMessage['MobNo']= $user->officername ?? '';
								$ErrorMessage['applicationType']= 'WebApp';
								$ErrorMessage['Module']= 'ENCORE';
								$ErrorMessage['TransectionType']= 'Counting';
								$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
								$ErrorMessage['TransectionAction']= 'Evm_Votes_Finalize';
								$ErrorMessage['TransectionStatus']= 'SUCCESS';
								$ErrorMessage['LogDescription']= 'EVM Votes Finalized';
								LogNotification::LogInfo($ErrorMessage);
							}					
					


					
				    return view($this->view_path.'.evm_vote_finalize',$data);	
					} 
		        else {
		           \Session::flash('error_mes', 'All rounds not completed, Please Complete your rounds then finalized');
		            return Redirect::to('/roac/counting/polling-station-wisevote-entry');	      
		               		   
		            } 	   
				        
			}

 	function postal_data_entry(){
		
			$counting = \DB::table('setting')->select('*')->where('key','counting')->first();
			 if($counting->value < 1){
			  \Session::flash('error_mes', 'Counting menu is not enabled now. ');
			  return Redirect::back();
		  }
		
		
		
		
 		$data  = [];
		 
	     $user 			= 	Auth::user();
		 $d 			=	$this->commonModel->getunewserbyuserid($user->id);
         $new_table		=	strtolower("counting_master_".$d->st_code);
		 $ele_details 	=	$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		 
         $data['user_data']  =$d;
         $data['ele_details']  =$ele_details;
         
         $st=getstatebystatecode($ele_details->ST_CODE);  
         $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);

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
			
			$postal_finalized=postal_votes_finalized($filter);	

			$round_details=$this->postal->roundsechudle($filter);
            $winn_data=$this->postal->winn_lead($filter);  
        	$master_data=$this->postal->master_records( $new_table,$filter_m);  

        		if(!isset($round_details)){
				\Session::flash('success_admin', 'Please define rounds schedule to enter counting data. ');
	        	return Redirect::to('roac/counting/round-schedule-details');	
			}
		$data['new_table']  =$new_table;
		$data['master_data']  =$master_data;
		$data['winn_data']  =$winn_data;
		$data['st_code']  =$ele_details->ST_CODE;			 
		$data['ac_no']  =$ele_details->CONST_NO;	 
		$data['round_details']  =$round_details; 
        $data['st_name']   = $st->ST_NAME;
        $data['ac_name']   = $ac->AC_NAME;
        $data['postal_finalized']   = $postal_finalized;
        //dd($data); 
        
		return view($this->view_path.'.postaldataentrysechudle',$data);          
		        
	}
	function verifypostalentry(Request $request)
			{
			  //dd($request);
			 $user = Auth::user();
			 $d=$this->commonModel->getunewserbyuserid($user->id);
                $ele_details 	=	$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
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
				     $this->validate($request, ['roname' => 'required','currentvote'.$i => 'required|numeric|digits_between:0,999999|',
				     	'totalvotes'=> 'required|numeric|digits_between:0,999999',
				     	'rejectedvotes'=> 'required|digits_between:0,999999',
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
		if(str_replace(" ","",$request->input('roname')) <> str_replace(" ","",Auth::user()->name)){
			\Session::flash('error_mes', 'Please enter correct returning officer name.');
            return Redirect::to('/roac/counting/bpostal-data-entry');	
		}
	 if($totalvotes== $total)  {
        DB::beginTransaction();
        	try{
        		$new_table=strtolower("counting_master_".$d->st_code);
 				for ($i=1; $i<=$val;$i++)
			       	{
			       	$mid=trim($request->input('mid'.$i));
			       	$nom_id=trim($request->input('nom_id'.$i));
			       	$currentvote=trim($request->input('currentvote'.$i));
			       	$priviousvote=trim($request->input('priviousvote'.$i));
			    $filter_ele='';
			    $filter_ele = [	'id'=>$mid,
			         			'nom_id'=>$nom_id,
			         			'ac_no'=> $d->ac_no
			         		 ];
				$total_value='';
			    $total_value=$this->postal->grandtotalsum($new_table,'round1',$filter_ele);
                         
				$total_vote   = 0; 
				$postal_vote=0;

           		if(isset($total_value) && $total_value){
				    $total_vote   = $total_value->grant_total;
				            
				}
				       $total_vote= $total_vote+$currentvote;
			          // $total_vote=$priviousvote+$currentvote;
			    $n_data = array('total_vote'=>$total_vote,
			    				'postalballot_vote'=>$currentvote,
			       				'added_update_at'=>date("Y-m-d"),
			       				'updated_at'=>date("Y-m-d h:i:s"),
			       				'postalvote_update_at'=>date("Y-m-d H:i:s"),
			       				'updated_by'=>$d->officername); 
              
                \App\models\Counting\CountingLogModel::clone_record($mid,$d->st_code);
			    DB::table($new_table)->where('id',$mid)->update($n_data);	
				}
	            
	            $data = array('rejected_votes'=>$rejectedvotes,
	            			'postal_total_votes'=>$totalvotes,
	            			'postal'=>'1'); 

			    DB::table('round_master')->where('id',$round_id)->update($data);

			   $sdata=$this->postal->selectsecondhightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$ELECTION_ID);
               $fdata=$this->postal->selectfirsthightvalueofcounting($new_table,$ST_CODE,$CONST_NO,$CONST_NO,$ELECTION_ID);
			    // print_r($sdata); dd($fdata);
			     $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
			     $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
			     $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
					
				 
		        $trail_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
			    $trail_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
			   $trail_party=$this->commonModel->selectone('m_party','CCODE',$trail_nom->party_id);
					 

				$margin=$fdata->max_total-$sdata->max_total;
		    $winn_update=array('candidate_id'=>$fdata->candidate_id,
		    					'nomination_id'=>$fdata->nom_id,
		    					'lead_cand_name'=>str_replace('  ',' ',$lead_cand->cand_name),
		    					'lead_cand_partyid'=>$lead_party->CCODE,
		    					'lead_cand_party'=>$lead_party->PARTYNAME,
		    					'lead_party_type'=>$lead_party->PARTYTYPE,
		    					'lead_party_abbre'=>$lead_party->PARTYABBRE,
		    					'lead_cand_hname'=>$lead_cand->cand_hname,
		    					'lead_cand_hparty'=>$lead_party->PARTYHNAME,
		    					'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
				    			'trail_candidate_id'=>$sdata->candidate_id,
				    			'trail_nomination_id'=>$sdata->nom_id,
				    			'trail_cand_name'=>str_replace('  ',' ',$trail_cand->cand_name),
				    			'trail_cand_partyid'=>$trail_party->CCODE,
				    			'trail_cand_party'=>$trail_party->PARTYNAME,
				    			'trail_party_type'=>$trail_party->PARTYTYPE,
				    			'trail_party_abbre'=>$trail_party->PARTYABBRE,
				    			'trail_cand_hname'=>$trail_cand->cand_hname,
				    			'trail_cand_hparty'=>$trail_party->PARTYHNAME,
				    			'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
				    			'margin'=>$margin,
				    			'lead_total_vote'=>$fdata->max_total,
				    			'trail_total_vote'=>$sdata->max_total,
				    			'added_update_at'=>$currentdate,
				    			'updated_at'=>$currentTime);
				      //dd($winn_update);

				     DB::table('winning_leading_candidate')->where('leading_id',$leading_id)->update($winn_update);
						
					$pubresult=['st_code'=>$ele_details->ST_CODE,
                        'election_id'=>$ele_details->ELECTION_ID,
                        'pc_no'=>0,
                        'ac_no'=>$ele_details->CONST_NO,
                        'certificate'=>"I, ".Auth::user()->name." certify that the postal ballot votes data entered/ updated for has been printed & manually verified by me & the observer and is correct., 
                          I, understand that upon pressing the 'Publish' button below,the postal ballot votes will be immediately published/ updated with the correct data and round-wise data will be  available in public domain. ,
                          I, certify that the postal ballot data publication on the server and at the counting center is done simultaneously.",
                        'name'=>$this->xssClean->clean_input($request->input('roname')),
                        'roname'=>Auth::user()->name,
                        'agree'=>'1',
                        ];
                  PostalBallotResultsPublishModel::add_records($pubresult);
                    }
				    catch(\Exception $e){
		            	DB::rollback();
			            \Session::flash('error_mes', 'Please try again');
			            return Redirect::back();
				        }
				        DB::commit();
			    			


							if(config('public_config.isCountingLoggerEnable')){
								$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
								$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
								$ErrorMessage['MobNo']= $user->officername ?? '';
								$ErrorMessage['applicationType']= 'WebApp';
								$ErrorMessage['Module']= 'ENCORE';
								$ErrorMessage['TransectionType']= 'Counting';
								$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
								$ErrorMessage['TransectionAction']= 'Postal_Vote';
								$ErrorMessage['TransectionStatus']= 'SUCCESS';
								$ErrorMessage['LogDescription']= 'Postal Vote Successfully Updated';
								LogNotification::LogInfo($ErrorMessage);
							}


			    			\Session::flash('success_mes', 'This Postal Vote Successfully Updated.');
	                		return Redirect::to('/roac/counting/bpostal-data-entry');	        
		        }
		        else {
                     \Session::flash('error_mes', 'Total Votes and candidate Vote Miss-Match');
                		return Redirect::to('/roac/counting/bpostal-data-entry');	
		        }
		     
			}


function counting_results(){
				$data=[];
 				$user = Auth::user();
			 	$d=$this->commonModel->getunewserbyuserid($user->id);
			 	$new_table=strtolower("counting_master_".$d->st_code);
	            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,'AC');
		       
		         if($check_finalize->finalized_ac =='0'){
               			\Session::flash('error_mes', 'Cantest Candidate  Not finalized Please finalized first');
                   		return Redirect::to('/roac/counting/prepare-counting-data');
 		       }
 		       	$filter='';
 		        $filter 	= [
	       	    				'st_code' 	=> $ele_details->ST_CODE,
	       	    				'ac_no' 	=> $ele_details->CONST_NO,
	       	    				'election_id' 	=> $ele_details->ELECTION_ID,
	       	    				'order_by' 	=> 'id',
	       	   				 ];
			DB::beginTransaction();
        	try{
	       	   	$evm_finalized=evm_votes_finalized($filter);
    			$postal_finalized=postal_votes_finalized($filter);
    		    $counting_finalized=countingfinalize($filter);
		       
				if($evm_finalized==0){
					 		\Session::flash('error_mes', 'EVM Rounds not finalize! please finalize first.');
		                		return Redirect::to('/roac/counting/polling-station-wisevote-entry');	
					 }
               if($postal_finalized==0){
			 	    \Session::flash('error_mes', 'Postal Votes may not be finalized. Please finalize same in order to Declare the Result.');
                	    return Redirect::to('/roac/counting/bpostal-data-entry');	
			     }
 		      	if($counting_finalized==0){
			 	    \Session::flash('error_mes', 'EVM and Postal Votes may not be finalized. Please finalize same in order to Declare the Result.');
                	    return Redirect::to('/roac/counting/bpostal-data-entry');	
			     }
			  
                 $tendered_vote=$this->postal->getalltendered_vote($filter);
				
				
                 $n_data = array('tended_votes'=>$tendered_vote,
				   					'updated_at'=>date("Y-m-d H:i:s"),
				   					'added_update_at'=>date("Y-m-d"),
				   					'tended'=>1);
									
									
			    DB::table('round_master')->where('st_code',$ele_details->ST_CODE)
			    						  ->where('ac_no',$ele_details->CONST_NO)
			    						  ->where('election_id',$ele_details->ELECTION_ID)
			    						  ->update($n_data);
										 
										 
				$round_details 	=	$this->postal->roundsechudle($filter);
                $winn_data 		=	$this->postal->winn_lead($filter);
        		$master_data	=	$this->postal->master_records( $new_table,$filter);
				
				DB::commit();
				}catch(\Exception $e){
		            	DB::rollback();
			            \Session::flash('error_mes', 'Please try again');
			            return Redirect::back();
				}
				     
				
				
				
				
        		$st=getstatebystatecode($ele_details->ST_CODE);
         		$ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
        		$data['user_data']=$d;
        		$data['ele_details']=$ele_details;
        		$data['master_data']=$master_data;
        		$data['round_details']=$round_details;
        		$data['winn_data']=$winn_data;
        		$data['evm_finalized']=$evm_finalized;
        		$data['counting_finalized']=$counting_finalized;
        		$data['postal_finalized']=$postal_finalized;
        		$data['st_code']=$ele_details->ST_CODE;
        		$data['st_name']=$st->ST_NAME;
        		$data['ac_no']=$ac->AC_NO;
        		$data['ac_name']=$ac->AC_NAME;
                //dd($data)  ;
			return view($this->view_path.'.counting-results',$data);
		 	          
		     
		}
	function counting_finalized()
			{
 				$data=[];
			    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			    $new_table=strtolower("counting_master_".$d->st_code);
			 	$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		         
		         $data['user_data']  =$d;
		         $data['ele_details']  =$ele_details;
		         
		         
		         $st=getstatebystatecode($ele_details->ST_CODE);  
		         $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
                 $data['st_name']  =$st->ST_NAME;
                 $data['ac_name']  =$ac->AC_NAME;
                 $data['st_code']  =$st->ST_CODE;
                 $data['ac_name']  =$ac->AC_NO;
		         
		         $filter='';  
		         $filter_m='';
				$filter = [
	       	    		'st_code' 	=> $ele_details->ST_CODE,
	       	    		'ac_no' 	=> $ele_details->CONST_NO,
	       	    		'election_id' 	=> $ele_details->ELECTION_ID,
	       	            ];
               
               $filter_m= [
	       	     		'ac_no' 	=> $ele_details->CONST_NO,
	       	    		'election_id' 	=> $ele_details->ELECTION_ID,
	       	    		'order_by' 	=> 'id', 
	       	    		];
				$postal_finalized=postal_votes_finalized($filter);

				$round_details=$this->postal->roundsechudle($filter);
                $winn_data=$this->postal->winn_lead($filter);  
        		$master_data=$this->postal->master_records( $new_table,$filter_m);  
				
			   $empty_ps_finalized=empty_ps_finalized($filter);
			   if($empty_ps_finalized==0){
				\Session::flash('error_mes', 'Please verify all empty polling stations before finalize postal ballots.');
				return Redirect::to('roac/counting/check-empty-ps-details');
			   }
				
				$totalPostalVotes = '';
				$total_postal_votes = 0;
				if(!empty($master_data)){
					foreach($master_data as $md){
						$totalPostalVotes = $md->postalvote_update_at;	
						$total_postal_votes = $total_postal_votes + $md->postalballot_vote;
					}  
				}
				if(is_null($totalPostalVotes)){
					\Session::flash('error_mes', 'First you need to enter postal ballot votes then click on finalize button.');
					return Redirect::back();
				}
				if($total_postal_votes ==0){
					\Session::flash('error_mes', 'Postal ballot votes can not be zero.');
					return Redirect::back();
				}
				
				
                $data['master_data']=$master_data;
                $data['winn_data']=$winn_data;
                $data['round_details']=$round_details;
                $data['postal_finalized']=$postal_finalized;
                //dd($data);
				
							if(config('public_config.isCountingLoggerEnable')){
								$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
								$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
								$ErrorMessage['MobNo']= $user->officername ?? '';
								$ErrorMessage['applicationType']= 'WebApp';
								$ErrorMessage['Module']= 'ENCORE';
								$ErrorMessage['TransectionType']= 'Counting';
								$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
								$ErrorMessage['TransectionAction']= 'Postal_Votes_Finalize';
								$ErrorMessage['TransectionStatus']= 'SUCCESS';
								$ErrorMessage['LogDescription']= 'Postal Votes Finalized';
								LogNotification::LogInfo($ErrorMessage);
							}	
				
				
				
				
				
         			 return view($this->view_path.'.counting_finalize',$data);	           
		    }

	// winner candidate name verifying
	public function verify_winner_by_name(Request $request){

		$data = [];
		if(!Auth::user()){
			$data['warning'] = "Please login to continue.bbbb ";
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

		$user_data = [
			'st_code' 	=> Auth::user()->st_code,
			'ac_no'     => Auth::user()->ac_no,
		];

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
				'lead_cand_name' 	=> str_replace('  ',' ',$object_leading->candidate_name), 
				'lead_cand_hname' 	=> $object_leading->candidate_hname,
				'lead_cand_partyid' => $object_leading->party_id,
				'lead_cand_party' 	=> $object_leading->party_name, 
				'lead_cand_hparty' 	=> $object_leading->party_hname, 
				'lead_party_type' 	=> $object_leading->PARTYTYPE,
				'lead_party_abbre' 	=> $object_leading->party_abbre, 
				'lead_hpartyabbre' 	=> $object_leading->party_habbre, 
				'trail_candidate_id' => $object_trailing->candidate_id, 
				'trail_nomination_id' => $object_trailing->nom_id, 
				'trail_cand_name' 	 => str_replace('  ',' ',$object_trailing->candidate_name), 
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
 				$user = Auth::user();
			 	$d=$this->commonModel->getunewserbyuserid($user->id);
	             $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		     
		    	 $filter='';  
			    	
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
                $winn_data=$this->postal->winn_lead($filter);  
       		  
		 		 

		 		$check_finalized_ro = $this->postal->check_finalized_ro([
			 		'state' 		=> $ele_details->ST_CODE,
			 		'ac_no' 		=> $ele_details->CONST_NO,
			 		'election_id' 	=> $ele_details->ELECTION_ID
			 	]);	
             
             
           $round_details=$this->postal->roundsechudle($filter);
               
            if(!isset($round_details)) {
                \Session::flash('success_admin', 'Round Schedule Not Created! Please Create to roundschedule');
                return Redirect::to('roac/counting/round-schedule-details');
               }   
				    
			 
 			$date = Carbon::now()->subMinutes(10);
            $currentTime = $date->format('Y-m-d H:i:s');
            $currentdate = $date->format('Y-m-d');

			 $n_data=array('status'=>'1',
			 				'result_declared_date'=>$currentdate,
			 				'added_update_at'=>$currentdate,
			 				'updated_at'=>$currentTime);
			 
			 $leading_id=$request->input('leading_id');
           DB::beginTransaction();
        		try{  
			 
            		$this->commonModel->updatedata('winning_leading_candidate','leading_id',$leading_id,$n_data);
                    DB::commit();
    		 		}
		        	catch(\Exception $e){
			            DB::rollback();
			           \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
			            return Redirect::back();
		        	}
                     
			    			\Session::flash('success_mes', 'Result is declared successfully.');
	                		return Redirect::to('/roac/counting/boothcounting-results');     	
		           
		        
			}
   
     function finalize_evm(Request $request)
    		{
 				$user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
           		$new_table=strtolower("counting_master_".$d->st_code);
				$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		      
		      $filter='';  
		     
			    	$filter 	= [
			       	    	'st_code' 	=> $ele_details->ST_CODE,
			       	    	'ac_no' 	=> $ele_details->CONST_NO,
			       	    	'election_id' 	=> $ele_details->ELECTION_ID,
			       	    	 
			       	    ];
		     $round_details=$this->postal->roundsechudle($filter);
			 
			 $new_table_ps=strtolower("counting_ps_".$d->st_code);
			 
			 /* Checking if all rounds not published  start */
			 $get_data = DB::table($new_table_ps)->where('ac_no', $d->ac_no)
        						->where('ELECTION_ID',$ele_details->ELECTION_ID)->where('results',0)->first();
			 if(isset($get_data) && !empty($get_data)){
				\Session::flash('error_mes', 'All rounds not published, Please publish your rounds then finalized');
				return Redirect::to('/roac/counting/round-wise-results'); 
				
			 }
			
			 
			 /* Checking if all rounds not published  ends */
								
        $c_data=DB::table($new_table)->select('complete_round','finalized_round')
        						->where('ac_no', $d->ac_no)
        						->where('ELECTION_ID',$ele_details->ELECTION_ID)->orderBy('id')->first();
         
        $complete_round=0; $finalized_round=0;
         if(isset($c_data)){
         	$complete_round=$c_data->complete_round; $finalized_round=$c_data->finalized_round;
            }
        if($round_details->scheduled_round==$complete_round)
		   {  
		DB::beginTransaction();
       try{    
        	$finalize_data = array('finalized_ac'=>'1',
		   						'updated_by'=>$d->officername ,
		   						'updated_at'=>date("Y-m-d H:i:s"),
		   						'finalize_date'=>date("Y-m-d"),
		   						'added_update_at'=>date("Y-m-d")); 
		    
		    DB::table('counting_finalized_ac')->where('st_code',$ele_details->ST_CODE)
			 					->where('ac_no',$d->ac_no)
			 					->where('election_id',$ele_details->ELECTION_ID)
			 					->update($finalize_data); 
			
			
			$pubresult=['st_code'=>$ele_details->ST_CODE,
					'election_id'=>$ele_details->ELECTION_ID,
					'pc_no'=>0,
					'ac_no'=>$ele_details->CONST_NO,
					'finalize_type'=>1,
					'certificate'=>"I, ".Auth::user()->name." allow to finalize the EVM vote count. Upon Finalization Changes can't be done from your end and the same data will be reflected in trends and result Website.., 
					  It has been compared and matches with the Form-20 compiled manually",
					'name'=>Auth::user()->name,
					'roname'=>Auth::user()->name,
					'agree'=>'1'];
			  CountingFinalizePublishModel::add_records($pubresult);
			}
			catch(\Exception $e){
			   DB::rollback();

			   \Session::flash('error_mes', 'Please try again Data  do not inserted');
			   return Redirect::back();
			} 
            DB::commit();

		   	 \Session::flash('success_admin', 'Evm Rounds Successfully finalized');
             return Redirect::to('/roac/counting/bpostal-data-entry'); 
		   	} 
         else {
            \Session::flash('error_mes', 'All rounds not completed, Please Complete your rounds then finalized');
             return Redirect::to('/roac/counting/polling-station-wisevote-entry');	      
               		   
            } 
			   
		   
    }   //end function
     
   	
    
   	
   	function counting_finalized_verify(Request $request)
    		{
 				
 				$user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			     $new_table=strtolower("counting_master_".$d->st_code);
       			 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');

       			 $filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
                DB::beginTransaction();
				try{
						$round_details=$this->postal->roundsechudle($filter);
		     	        $postaldata= array('finalize_by_ro'=>'1',
			     			'finalize_date'=>date("Y-m-d"),
			     			'updated_at_ro'=>date("Y-m-d H:i:s"),
			     			'added_update_at'=>date("Y-m-d"),
			     			'updated_at'=>date("Y-m-d H:i:s") ,
			     			'updated_by'=>$d->officername); 

						DB::table('counting_finalized_ac')
			 				->where('st_code',$ele_details->ST_CODE)
			 				->where('ac_no',$d->ac_no)
			 				->where('election_id',$ele_details->ELECTION_ID)
			 				->update($postaldata);
						$pubresult=['st_code'=>$ele_details->ST_CODE,
							'election_id'=>$ele_details->ELECTION_ID,
							'pc_no'=>0,
							'ac_no'=>$ele_details->CONST_NO,
							'finalize_type'=>2,
							'certificate'=>"I, ".Auth::user()->name." allow to finalize the Postal ballot vote count. Upon Finalization Changes can't be done from your end and the same data will be reflected in trends and result Website.., 
							  It has been compared and matches with the Form-20 compiled manually",
							'name'=>Auth::user()->name,
							'roname'=>Auth::user()->name,
						'agree'=>'1'];
						CountingFinalizePublishModel::add_records($pubresult);
				}
				catch(\Exception $e){
					DB::rollback();

					\Session::flash('error_mes', 'Please try again');
					return Redirect::back();
				}
				DB::commit();

		   	 \Session::flash('success_admin', 'Successfully finalized.');
             return Redirect::to('/roac/counting/bpostal-data-entry'); 
		    }	

    public function prepare_counting_data(){   
    		$data  = [];
	        $heading_title = "To Start Counting Process ROAC Needs to Activate Counting.";
			$user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $new_table=strtolower("counting_master_".$d->st_code);
 			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          
          $data['user_data'] 	= $d;
          $data['ele_details'] 	= $ele_details;	
		  $data['heading_title'] 	= $heading_title; 
		  $data['cand_finalize_ro'] 	= '0'; 
		  $data['cand_finalize_ceo'] 	='0';    
		    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,"AC");
          if(isset( $check_finalize)){
          	$data['cand_finalize_ceo'] 	=$check_finalize->finalize_by_ceo; 
		    $data['cand_finalize_ro'] 	=$check_finalize->finalized_ac; 
		      
           }
		    $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
          $data['byro']=$byro;
       	     
       	        $filter='';   
			    $filter 	= [
	       	      	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	'order_by' 	=> 'id', 
	       	    	];
				$counting_data=$this->postal->master_records($new_table,$filter);  
	 		 $data['counting_data']=$counting_data;
	 		 $data['ac_no']=$ele_details->CONST_NO;
	 	     $data['st_code']=$ele_details->ST_CODE;

		   return view($this->view_path.'.prepear_counting', $data);	           
	        
	    }  // end index function     createcenter	


	 function activate_allac()
   			{  
   			$data  = [];
	        $heading_title = "To Start Counting Process ROAC Needs to Activate Counting.";
			$user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $new_table=strtolower("counting_master_".strtolower($d->st_code));
 			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          
	          $data['user_data'] 			= $d;
	          $data['ele_details'] 			= $ele_details;	
			  $data['heading_title'] 		= $heading_title; 
			  $data['cand_finalize_ro'] 	= '0'; 
			  $data['cand_finalize_ceo'] 	='0';    
		    	$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,"AC");
	          if(isset( $check_finalize)){
	          	$data['cand_finalize_ceo'] 	= $check_finalize->finalize_by_ceo; 
			    $data['cand_finalize_ro'] 	=$check_finalize->finalized_ac; 
			      
	           }
	            
				   $seched=getschedulebyid($ele_details->ScheduleID);
		           $sechdul=checkscheduledetails($seched);
       	        $byro=countingfinalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);
				$date = Carbon::now();
             	$currentTime = $date->format('Y-m-d H:i:s');
             	$currentdate = $date->format('Y-m-d');
	    	 
			  $cand_data=$this->postal->cantestesting_nomination($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->ELECTION_ID);

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
											->where('ac_no',$list->ac_no)
											->where('election_id',$list->election_id)->first();
				if(!isset($check))
				  {
					$can=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
					$p=getpartybyid($list->party_id);
					 
                	$ca_data = array('nom_id'=>$list->nom_id,
			                		'candidate_id'=>$list->candidate_id,
			                		'ac_no'=>$list->ac_no,
			                		'dist_no'=>$list->district_no,
			                        'new_srno'=>$list->new_srno,
			                		'election_id'=>$list->election_id,
			                		'created_at'=>$currentTime,
			                		'created_by'=>$d->officername,
			                		'added_create_at'=>$currentdate,
			                		'candidate_name'=>trim($can->cand_name),
			                		'party_id'=>$list->party_id,
			                		'party_abbre'=>$p->PARTYABBRE,
			                		'party_name'=>$p->PARTYNAME,
			                		'candidate_hname'=>$can->cand_hname,
			                		'party_habbre'=>$p->PARTYHABBR,
			                		'party_hname'=>$p->PARTYHNAME); 
                    			insertData($new_table, $ca_data);
                   }
				 
			 }   
				$lis_st=getstatebystatecode($ele_details->ST_CODE);
        		$lis_ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);

			$check_d=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)
								->where('pc_no',$ele_details->CONST_NO)
								->where('election_id',$ele_details->ELECTION_ID)->first();  

			if(!isset($check_d)){	  
			 $winn_data=array('election_id'=>$ele_details->ELECTION_ID,
			 					'constituency_type'=>$ele_details->CONST_TYPE,
			 					'st_code'=>$ele_details->ST_CODE,
						 		'st_name'=>$lis_st->ST_NAME,
						 		'st_hname'=>$lis_st->ST_NAME_HI,
						 		'ac_no'=>$ele_details->CONST_NO,
						 		'ac_name'=>$lis_ac->AC_NAME,
						 		'ac_hname'=>$lis_ac->AC_NAME_HI,
						 		'dist_no'=>$lis_ac->DIST_NO_HDQTR,
						 		'created_at'=>$currentTime,
						 		'added_create_at'=>$currentdate);
			   					
			   					insertData('winning_leading_candidate', $winn_data);
	          } 
	          DB::commit();  
	           }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
		            return Redirect::back();
		        }
		          
        		\Session::flash('success_admin', 'Counting data Successfully added');
                 return Redirect::to('/roac/counting/prepare-counting-data');
			   
		  	 	
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
				$round_details=$this->postal->roundsechudle($filter);
                 
        		$winn_data=$this->postal->winn_lead($filter);  
        		 
        		$master_data=$this->postal->master_records($new_table,$filter_m);        
                
          
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
				$round_details=$this->postal->roundsechudle($filter);
                 
        		$winn_data=$this->postal->winn_lead($filter);  
        		 
        		$result=$this->postal->master_records( $new_table,$filter_m);   

                 
			     return view('admin.ac.counting.counting-details',['user_data' => $d,'rounds'=>$round_details,'result'=>$result, 'ele_details'=>$ele_details, 'winn_data'=>$winn_data,
			     	'heading_title' => $heading_title
			 ]);	           
		        }
		        else {
		              return redirect('/officer-login');
		        	  }
		    }  // end 


	 

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
            $date = Carbon::now();
            $currentTime = $date->format('Y-m-d H:i:s');
            $currentdate = $date->format('Y-m-d');  
            $name_excel = 'rdf_postal_ballot_st_code'.$d->st_code.'_ac_no'.$data['ac_no'].'_'.date('d-m-Y').'_'.time();

            $data['ac_name']     	= $ac_name;
            $data['round']         	= \Session::get('round');
            $data['heading_title'] 	= 'Postal ballot print';
            $data['st_name'] 		= $st_name;
            $data['election'] 		="AC-".@$ele_details->ELECTION_TYPE;;
            $data['enter_by']       = $d->officername;
            $data['print_date']     = $currentTime;  
            $data['name_excel']     = $name_excel;
            $data['ref_no']         = time();
       	//round to be sum and print previous
             $filter= [
            'st_code'       =>$d->st_code,
            'ac_no'         =>$d->ac_no,
            'ps_no'         =>Session::get('ps_no'),
            'election_id'   =>$ele_details->ELECTION_ID,
        ]; 
           
            
        $mid=$this->boothcounting->maxidoftable($filter);
        $table="counting_master_".strtolower($d->st_code);
        if($mid==0)$mid +=1;
        $log_data = array( 'st_code'=>\Auth::user()->st_code,
                'election_id'=>$ele_details->ELECTION_ID,
                'election_typeid'=>$ele_details->ELECTION_TYPEID, 
                'pc_no'=>'0', 
                'ac_no'=>$d->ac_no, 
                'ps_no'=>'0',
                'doc_type'=>"Postal Ballot Declaration Form",
                'file_name'=>$name_excel.".pdf",
                'table_name'=>$table,
                'table_primary_key'=>$mid, 
                'log_date_time'=>$currentTime,
                'added_create_at'=>$currentdate,
                'ref_no'=> $data['ref_no'],
                'created_by'=>$d->officername);

             $setting_pdf = [
				'margin_top'        => 80,        // Set the page margins for the new document.
				'margin_bottom'     => 10,    
			];
            
            $pdf = \PDF::loadView($this->view_path.'.ballot_pdf',$data, [], $setting_pdf);
            if($request->has('json')){
                return \Response::json([
                    'success' => true
                ]);
            }
             \App\models\Counting\CountingPrintlogModel::clone_record($log_data);

             return $pdf->download($name_excel.'.pdf');
        	
        	}else{
        		return \Redirect::to('/officer-login');
    		}
    }
	
	function ballot_pdf_final(Request $request)
	{
			if(!\Auth::user()){
			return false;
		}
		
		//dd($request->all());
		 
        if($request->has('ac_no') && $request->has('round')){
            //\Session::put('print_table',$request->print_table);
            \Session::put('ac_no',$request->ac_no);
            \Session::put('round',$request->round);
        }

        $d 				= \Auth::user();
		$ele_details 	= $this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');

        if(\Auth::user()){
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
            $date = Carbon::now();
            $currentTime = $date->format('Y-m-d H:i:s');
            $currentdate = $date->format('Y-m-d');  
            $name_excel = 'rdf_postal_ballot_st_code'.$d->st_code.'_ac_no'.$data['ac_no'].'_'.date('d-m-Y').'_'.time();

            $data['ac_name']     	= $ac_name;
            $data['round']         	= \Session::get('round');
            $data['heading_title'] 	= 'Postal ballot print';
            $data['st_name'] 		= $st_name;
            $data['election'] 		="AC-".@$ele_details->ELECTION_TYPE;;
            $data['enter_by']       = $d->officername;
            $data['print_date']     = $currentTime;  
            $data['name_excel']     = $name_excel;
            $data['ref_no']         = time();
       	//round to be sum and print previous
             $filter= [
            'st_code'       =>$d->st_code,
            'ac_no'         =>$d->ac_no,
            'ps_no'         =>Session::get('ps_no'),
            'election_id'   =>$ele_details->ELECTION_ID,
        ]; 
           
            
        $mid=$this->boothcounting->maxidoftable($filter);
        $table="counting_master_".strtolower($d->st_code);
        if($mid==0)$mid++;
        $log_data = array( 'st_code'=>\Auth::user()->st_code,
                'election_id'=>$ele_details->ELECTION_ID,
                'election_typeid'=>$ele_details->ELECTION_TYPEID, 
                'pc_no'=>'0', 
                'ac_no'=>$d->ac_no, 
                'ps_no'=>'0',
                'doc_type'=>"Postal Ballot Declaration Form",
                'file_name'=>$name_excel.".pdf",
                'table_name'=>$table,
                'table_primary_key'=>$mid, 
                'log_date_time'=>$currentTime,
                'added_create_at'=>$currentdate,
                'ref_no'=> $data['ref_no'],
                'created_by'=>$d->officername);
				
			$filter_m 	= [
	       	  	'ac_no' 	=> $ele_details->CONST_NO,
	       	   	'election_id' 	=> $ele_details->ELECTION_ID,
	       	   	'order_by' 	=> 'id', 
	       	];
			$new_table=strtolower("counting_master_".$d->st_code);
			
        	$master_data=$this->postal->master_records( $new_table,$filter_m);
			$data['master_data'] = $master_data;
			
			$filter='';  
			$filter 	= [
					'st_code' 	=> $ele_details->ST_CODE,
					'ac_no' 	=> $ele_details->CONST_NO,
					'election_id' 	=> $ele_details->ELECTION_ID,
					'order_by' 	=> 'id', 
				 ];
			$round_details=$this->postal->roundsechudle($filter);
			$data['round_details']=$round_details;
			//dd($master_data);
             $setting_pdf = [
				'margin_top'        => 80,        // Set the page margins for the new document.
				'margin_bottom'     => 10,    
			];
            
            $pdf = \PDF::loadView($this->view_path.'.ballot_pdf_final',$data, [], $setting_pdf);
            
             \App\models\Counting\CountingPrintlogModel::clone_record($log_data);

             return $pdf->download($name_excel.'.pdf');
        	
        	}else{
        		return \Redirect::to('/officer-login');
    		}
		
	}


    public function tenders_votes(Request $request) {
    			$user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
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
				   $n_data = array('tended_votes'=>$tended_votes,
				   					'updated_at'=>date("Y-m-d H:i:s"),
				   					'added_update_at'=>date("Y-m-d"),
				   					'tended'=>1); 
			       DB::table('round_master')->where('st_code',$st_code)->where('ac_no',$ac_no)->update($n_data);	  
				}
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('unsuccess_insert', 'Please try again');
		            return Redirect::back();
		        }
		        DB::commit();  

				\Session::flash('success_mes', 'Tendered Votes updated Successfully.');
              	return Redirect::to('/roac/counting/boothcounting-results');	         
		         
		    }  // end 
			
			
			
			
		public function roundsechudle($data = array())
            { 
            $sql_raw = "id,scheduled_round,st_code,ac_no,rejected_votes,postal_total_votes,finalized_ac,added_update_at,tended_votes,tended,postal";

                $sql = DB::table('round_master');
                $sql->selectRaw($sql_raw);

                if(isset($data['st_code']) && !empty($data['st_code'])){
                        $sql->where("st_code", $data['st_code']);
                      }

                if(isset($data['ac_no']) && !empty($data['ac_no'])){
                        $sql->where("ac_no", $data['ac_no']);
                      }
                if(isset($data['election_id']) && !empty($data['election_id'])){
                        $sql->where("election_id", $data['election_id']);
                      }
              $query = $sql->first();
              return $query;
             }
			
			
			
		 
}  // end class results-declaration   
