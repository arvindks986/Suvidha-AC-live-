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
		use \PDF;
		use App\commonModel;
		use Illuminate\Support\Facades\Schema;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
		use App\models\Counting\BoothCountingModel;
		use App\adminmodel\ACCountingModel;
		use App\Helpers\LogNotification;
class CountingCenterDetailsController  extends Controller
{

	public $base    = 'roac';
  	public $folder  = 'counting';
  	public $action    = 'roac/counting/verify-counting-center-details';
 	public $view_path = "admin.counting.ro";

   public function __construct()
        {
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->middleware('ro_only'); 
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->boothcounting=new BoothCountingModel;
        $this->CountingModel = new ACCountingModel();
          if(!Auth::check()){ 
        	 return redirect('/officer-login');
        }


        }

    protected function guard(){

        return Auth::guard('admin');
    	}
    public function index()
	    {    
	   	  $data  = [];
	      $user = Auth::user();
		  $d=$this->commonModel->getunewserbyuserid($user->id);
          $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
          
		  
		  $counting_preparation = \DB::table('setting')->select('*')->where('key','counting_preparation')->first();
		  
		  if($counting_preparation->value < 1){
			  \Session::flash('error_mes', 'Counting Preparation menu is not enabled now. ');
			  return Redirect::back();
		  }
		  
		  
		  
		  
          $data['user_data'] 	= $d;
          $data['ele_details'] 	= $ele_details;
          $data['action']       = url($this->action);
                $filter='';
            
	             $filter = [
					'st_code' 		=> $ele_details->ST_CODE,
					'ac_no' 		=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'const_type' => $ele_details->CONST_TYPE,
					'table'			=>"counting_master_".strtolower($ele_details->ST_CODE), 
			     ];

			 $countingstart=checkcountingstart($filter);
			 $evmfinalized=evm_votes_finalized($filter);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
			if($check_finalize->finalized_ac==0){
               \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
                return Redirect::to('roac/counting/prepare-counting-data');
                }    
			$records=getcountingtype($filter);
        
		    $date = Carbon::now();
		    $currentTime = $date->format('Y-m-d H:i:s');
		    $currentdate = $date->format('Y-m-d');      
			if($records==false) {  
				 $record_i = array('st_code'=>$d->st_code,
	    				'ac_no'=>$d->ac_no,
	    			    'const_type'=>"AC",
	    				'election_id'=>$ele_details->ELECTION_ID,
	    				'election_typeid'=>$ele_details->ELECTION_TYPEID,
	    				'counting_type'=>'0',
	    				'created_by'=>$d->officername,
	    				'added_create_at'=>$currentdate,
	    				'created_at'=>$currentTime); 
            $this->commonModel->insertData('counting_ro_type',$record_i);
		    }
            
               
				$table_details=$this->boothcounting->get_table_master_details($filter);

				$noofps=$this->boothcounting->countpollingstation($filter);
				 
                if(isset($table_details)){
                	if($table_details->total_no_ps!=NULL)
                	$noofps = $table_details->total_no_ps;
                  }
				 $data['table_details'] = $table_details;
			     $data['noofps'] = $noofps;
			     $data['evmfinalized'] = $evmfinalized;
			     $data['countingstart'] = $countingstart;
			     //dd($data);
			 return view($this->view_path.'.countingcenterdetails', $data);
        }
	    	
     function verify_counting_center_details(Request $request){
     		$user = Auth::user();
     		$this->validate(
	        $request, 
	          [
	             'total_no_ps' => 'required|digits_between:1,9999',
	             'total_no_tables' => 'required|digits_between:1,99',
	             'total_no_rounds' => 'required|digits_between:1,130',  
	          ],
	          [
	           'total_no_ps.required' => 'Please enter number of polling station',
	           'total_no_ps.digits_between' => 'Please enter  number of PS 1 and 9999',
	            'total_no_tables.required' => 'Please enter number of table ',
	            'total_no_tables.digits_between' => 'Please enter number of table 1 and 99',
	            'total_no_rounds.required' => 'Please enter number of rounds ',
	            'total_no_rounds.digits_between' => 'Please enter number of rounds 1 and 99',
	        ]);
			     
			      $d=$this->commonModel->getunewserbyuserid($user->id);
			      $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
               $totalps= $this->xssClean->clean_input(Check_Input($request->input('total_no_ps'))); 
               $total_table=  $this->xssClean->clean_input(Check_Input($request->input('total_no_tables')));  
               $rounds_no=  $this->xssClean->clean_input(Check_Input($request->input('total_no_rounds')));  
              // $rounds_no= ceil($totalps/$total_table);
            
             $filter='';
            
	             $filter = [
					'st_code' 		=> $ele_details->ST_CODE,
					'pc_no' 		=>'',
					'election_id'	=> $ele_details->ELECTION_ID,
					'ac_no'			=>$d->ac_no,
			     ]; 
        
				$table_details=$this->boothcounting->get_table_master_details($filter);  
				$new_table=strtolower("counting_master_".$ele_details->ST_CODE);
					DB::beginTransaction();
		         	try{
		        		$date = Carbon::now();
		             	$currentTime = $date->format('Y-m-d H:i:s');
		                $currentdate = $date->format('Y-m-d');      
					    $sql_raw = "id,  total_no_ps,total_no_tables,total_no_rounds,complete_table,created_at";
			    		$new_data = array('st_code'=>$d->st_code,
			    								'ac_no'=>$d->ac_no,
			    								'pc_no'=>$d->pc_no,
			    								'election_id'=>$ele_details->ELECTION_ID,
			    								'election_typeid'=>$ele_details->ELECTION_TYPEID,
			    								'total_no_ps'=>$totalps,
			    								'total_no_tables'=>$total_table,
			    								'total_no_rounds'=>$rounds_no,
			    								'created_by'=>$d->officername,
			    								'created_at'=>$currentTime,
			    								'dist_no'=>$d->dist_no,
			    								 'added_create_at'=>$currentdate);
			    		$round_data = array('st_code'=>$d->st_code,
	    								'ac_no'=>$d->ac_no,
	    								 'scheduled_round'=>$rounds_no,
	    								 'election_id'=>$ele_details->ELECTION_ID,
	    								 'election_typeid'=>$ele_details->ELECTION_TYPEID,
	    								 'ccenter_id'=>0,
	    								 'created_by'=>$d->officername,
	    								 'iscreated'=>'1',
	    								 'dist_no'=>$d->dist_no,
	    								 'table_name'=>$new_table,
	    								 'added_create_at'=>$currentdate,
	    								 'created_at'=>$currentTime); 

			          // $this->boothcounting->manage_table_rounds($total_table,$rounds_no,$ele_details);	
		             
				    if(!isset($table_details)) {
					        $this->commonModel->insertData('table_master', $new_data);
							
							if(config('public_config.isCountingLoggerEnable')){
								$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
								$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
								$ErrorMessage['MobNo']= $user->officername ?? '';
								$ErrorMessage['applicationType']= 'WebApp';
								$ErrorMessage['Module']= 'ENCORE';
								$ErrorMessage['TransectionType']= 'CountingPrepration';
								$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
								$ErrorMessage['TransectionAction']= 'Counting_Center_Details';
								$ErrorMessage['TransectionStatus']= 'SUCCESS';
								$ErrorMessage['LogDescription']= 'Counting Center Details Successfully Added';
								LogNotification::LogInfo($ErrorMessage);
							}
							
							
					       \Session::flash('success_admin', 'Counting Center Details Successfully Added');
			            }
			         else {
					        $this->commonModel->updatedata('table_master','id',$table_details->id,$new_data);
					        
							
							if(config('public_config.isCountingLoggerEnable')){
								$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
								$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
								$ErrorMessage['MobNo']= $user->officername ?? '';
								$ErrorMessage['applicationType']= 'WebApp';
								$ErrorMessage['Module']= 'ENCORE';
								$ErrorMessage['TransectionType']= 'CountingPrepration';
								$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
								$ErrorMessage['TransectionAction']= 'Counting_Center_Details';
								$ErrorMessage['TransectionStatus']= 'SUCCESS';
								$ErrorMessage['LogDescription']= 'Counting Center Details Successfully Updated';
								LogNotification::LogInfo($ErrorMessage);
							}
							
							
							
							
					        \Session::flash('success_admin', 'Counting Center Details Successfully Updated');
			              }
			        $round_details=$this->boothcounting->roundsechudle($filter); 
			        if(!isset($round_details)) {
			        			insertData('round_master', $round_data);
			         
	            			}
	         		else {
			        			updatedata('round_master','id',$round_details->id,$round_data);
			               }
			         DB::commit();
			     }
		 		catch(\Exception $e){
				            DB::rollback();
				    
				            \Session::flash('error_mes', 'Please try again');
				            return Redirect::back();
				        }

				 return Redirect::to('roac/counting/round-schedule-details');
     		}  // end function
	 function round_schedule() {
		 
		$counting_preparation = \DB::table('setting')->select('*')->where('key','counting_preparation')->first();
		  
		  if($counting_preparation->value < 1){
			  \Session::flash('error_mes', 'Counting Preparation menu is not enabled now. ');
			  return Redirect::back();
		  }
		 
		 
				$data  = [];
			    $user = Auth::user();
			   	$d=$this->commonModel->getunewserbyuserid($user->id);
				$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
		        $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,'AC');
		    $data['user_data']=$d;
		    $data['ele_details']=$ele_details;
		    if($check_finalize->finalized_ac==0){
               \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
                return Redirect::to('roac/counting/prepare-counting-data');
                }    
		      $filter='';
            
	             $filter = [
					'st_code' 		=> $ele_details->ST_CODE,
					'ac_no' 		=>$ele_details->CONST_NO,
					'election_id'	=> $ele_details->ELECTION_ID,
					'const_type' => $ele_details->CONST_TYPE,
					'table'			=>"counting_master_".strtolower($ele_details->ST_CODE), 
			     ];
			//$records=getcountingtype($filter);
           
		     $checkuser=$this->boothcounting->checkmasterrecords($filter);
		      
                 if(!isset($checkuser)){
                             \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
                             return Redirect::to('roac/counting/prepare-counting-data');
                        }
		      
		 		$new_table=strtolower("counting_master_".strtolower($d->st_code));
		 		$filter='';  
			    	$filter 	= [
	       	    	'st_code' 	=> $ele_details->ST_CODE,
	       	    	'ac_no' 	=> $ele_details->CONST_NO,
	       	    	'election_id' 	=> $ele_details->ELECTION_ID,
	       	    	 
	       	    ];
              $evmfinalized=evm_votes_finalized($filter);   
			    
		$list=$this->boothcounting->roundsechudle($filter);
        if(isset($list)) $rid=$list->id; else $rid='';
 
 		if(!empty($checkuser->complete_round)){
           			$complete_round=$checkuser->complete_round; 
           			$finalized_round=$checkuser->finalized_round;
           		}
             else {$complete_round=0; $finalized_round=0;}
         $data['list']=$list; 
 		 $data['rid']=$rid;
 		 $data['finalized_round']=$evmfinalized;
 			 
 
	        return view($this->view_path.'.roundschedule',$data);	           
		}
		 
	 
	public function verifyround(Request $request)
		    {    
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
			    $c_data=DB::table($new_table)
			    			->select('complete_round','finalized_round')
							->where('ac_no', $d->ac_no)
							->where('election_id',$ele_details->ELECTION_ID)->first();

			if($c_data->complete_round>$scheduled_round and $scheduled_round1>$scheduled_round)
			   		  {
					   \Session::flash('error_mes', 'No of Rounds can not be less than completed rounds');
					    return Redirect::to('roac/counting/round-schedule-details');	
					 }
					 $scheduled_round1=$scheduled_round+1;
		    $newdata=DB::table("counting_ps_".strtolower($ele_details->ST_CODE))
			    			 ->where('ac_no', $d->ac_no)
							->where('election_id',$ele_details->ELECTION_ID)
							->where('round_id',$scheduled_round1)
							 ->first();

			if(isset($newdata))
			   		  {
					   \Session::flash('error_mes', 'No of Rounds can not be less than current rounds');
					    return Redirect::to('roac/counting/round-schedule-details');	
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
	    								'dist_no'=>$d->dist_no,
	    								'table_name'=>$new_table,
	    								'added_create_at'=>$currentdate); 
	           	
       
		    if(!isset($round_details)) {
			        $this->commonModel->insertData('round_master', $round_data);
			        DB::commit();
					
							if(config('public_config.isCountingLoggerEnable')){
								$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
								$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
								$ErrorMessage['MobNo']= $user->officername ?? '';
								$ErrorMessage['applicationType']= 'WebApp';
								$ErrorMessage['Module']= 'ENCORE';
								$ErrorMessage['TransectionType']= 'CountingPrepration';
								$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
								$ErrorMessage['TransectionAction']= 'Round_Schedule';
								$ErrorMessage['TransectionStatus']= 'SUCCESS';
								$ErrorMessage['LogDescription']= 'Round Schedule Successfully Added';
								LogNotification::LogInfo($ErrorMessage);
							}					
					
					
			        \Session::flash('success_admin', 'Round Schedule Successfully Added');
	            }
	         else {
			        $this->commonModel->updatedata('round_master','id',$round_details->id,$round_data);

							if(config('public_config.isCountingLoggerEnable')){
								$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
								$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
								$ErrorMessage['MobNo']= $user->officername ?? '';
								$ErrorMessage['applicationType']= 'WebApp';
								$ErrorMessage['Module']= 'ENCORE';
								$ErrorMessage['TransectionType']= 'CountingPrepration';
								$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
								$ErrorMessage['TransectionAction']= 'Round_Schedule';
								$ErrorMessage['TransectionStatus']= 'SUCCESS';
								$ErrorMessage['LogDescription']= 'Round Schedule Successfully Updated';
								LogNotification::LogInfo($ErrorMessage);
							}
			        
			        \Session::flash('success_admin', 'Round Schedule Successfully Updated');
	              }
	        
	         DB::commit();
	     }
 		catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('error_mes', 'Please try again');
		            return Redirect::back();
		        }
		    
 
	          return Redirect::to('roac/counting/round-schedule-details');	           
		} // end index function 

		     
	   function polling_station_wisevote_entry(Request $request){
	   		    $data  = [];
	    	    $user = Auth::user();
			    $d=$this->commonModel->getunewserbyuserid($user->id);
                $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
                $new_table=strtolower("counting_ps_".$d->st_code);
                 $round_id=base64_decode($request->round_id); 
                 $table_id=base64_decode($request->table_id);     
                  
                  $filter = [
					'st_code' 		=> $ele_details->ST_CODE,
					'pc_no' 		=>'',
					'election_id'	=> $ele_details->ELECTION_ID,
					'ac_no'			=>$d->ac_no,
			     ];

			     $filter_table = [
					'st_code' 		=> '',
					'pc_no' 		=>'',
					'election_id'	=> $ele_details->ELECTION_ID,
					'ac_no'			=>$d->ac_no,
					'table_name'	=>$new_table,
					'round_id'		=>$round_id,
			     ];
			     $table_details=$this->boothcounting->get_table_master_details($filter); 
                 $round_details=$this->boothcounting->roundsechudle($filter);

                 $list_table=$this->boothcounting->getcompletetables($filter_table);  
                 $data['round_id'] = $round_id;
                 $data['table_id'] = $table_id;
                 $data['user_data'] = $d;
                 $data['ele_details'] = $ele_details;
                 $data['new_table'] = $new_table;
                 $data['total_no_ps'] = $table_details->total_no_ps;
                 $data['total_no_tables'] = $table_details->total_no_tables;
                 $data['scheduled_round'] = $round_details->scheduled_round;
                 $data['complete_rounds'] = $table_details->complete_rounds; 
                 $data['current_rounds']  = $table_details->complete_rounds+1;
                 $data['complete_table']  = $list_table;  
     
                $master_table=strtolower("counting_master_".$d->st_code);
                $filter_m 	= [
				       	    'ac_no' 	=> $ele_details->CONST_NO,
				       	    'election_id' 	=> $ele_details->ELECTION_ID,
				       	    'order_by' 	=> 'id', 
	       	    		];
	       	    $filter_ps = [
					'pc_no' 		=>'',
					'election_id'	=> $ele_details->ELECTION_ID,
					'ac_no'			=>$d->ac_no,
					'table_name'	=>$new_table,
					'round_id'		=>$round_id,
					'table_id'		=>$table_id,
			     ];
                $master_data=$this->CountingModel->master_records($master_table,$filter_m);
                
                $counting_pstabledeails  =$this->boothcounting->getpollingstationgroupby($filter_ps);
                $counting_ps_evmvote   =$this->boothcounting->getvotedetailsbyroundid($filter_ps);

                $data['master_table'] = $master_table;
                $data['master_data'] = $master_data;
                $data['counting_pstabledeails'] = $counting_pstabledeails;
                $data['counting_ps_evmvote'] = $counting_ps_evmvote;
                 
                // dd($data);

                return view($this->view_path.'.polling-station-wisevote-entry', $data);
	   }
    function verifypolling_station_wisevote_entry(Request $request) {
     		    $user = Auth::user(); 
     		    
			    $d=$this->commonModel->getunewserbyuserid($user->id);
			    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
			   
		          
		        $ST_CODE =$ele_details->ST_CODE;  
		        $CONST_TYPE =$ele_details->CONST_TYPE;   //$this->xssClean->clean_input($request->input('CONST_TYPE'));
		        $CONST_NO = $ele_details->CONST_NO;  //$this->xssClean->clean_input($request->input('CONST_NO'));
		        $ELECTION_ID=$ele_details->ELECTION_ID;  //$this->xssClean->clean_input($request->input('ELECTION_ID'));
		        $round_id=$this->xssClean->clean_input($request->input('round_id')); 
		        $table_id=$this->xssClean->clean_input($request->input('table_id'));  
		        $cu_no=$this->xssClean->clean_input($request->input('cu_no'));
		        $vvpat_no=$this->xssClean->clean_input($request->input('vvpat_no'));  
		        $ps_no=$this->xssClean->clean_input($request->input('ps_no'));    //
		        $val = $this->xssClean->clean_input($request->input('val'));
		          
		          if($request->input('cu_defect_id')=='on') $cu_defect_id =1; else $cu_defect_id =0;
		          if($request->input('vvpat_defect_id')=='on') $vvpat_defect_id =1; else $vvpat_defect_id =0;
		         // $vvpat_defect_id = $this->xssClean->clean_input($request->input('vvpat_defect_id'));
				$input = $request->all();
 				 
					$date = Carbon::now();
             		$currentTime = $date->format('Y-m-d H:i:s');
             		$currentdate = $date->format('Y-m-d');  
				   

				$rules = ['Please enter all new serial number'];
				$total_voters = 0;

				for ($i=1; $i<=$val;$i++){  

				    $this->validate($request, [
					    	'currentvote'.$i => 'required|digits_between:0,999999',
					    ],
		                [
			                'currentvote'.$i.'required' => 'Please enter current vote ',
			                'currentvote'.$i.'numeric' => 'Please enter integer value ',
			                'currentvote'.$i.'digits_between' => 'Please enter integer value max 999999 ',
			                'currentvote'.$i.'integer' => 'Please enter integer value ',
			                'currentvote'.$i.'regex' => 'Please enter integer value ',
			                 
		                ]);	

				  	$total_voters += $input['currentvote'.$i];

			    }

			    if($total_voters != $request->total){
			    	\Session::flash('error_mes', 'Total value is wrong.');
			    	return Redirect::back()->withInput($request->all());
		        }
          DB::beginTransaction();
       			try{
       		    $new_table=strtolower("counting_ps_".$d->st_code);
 				for ($i=1; $i<=$val;$i++)
			       	{
				       	$mid=$this->xssClean->clean_input($request->input('mid'.$i));
				       	$nom_id=$this->xssClean->clean_input($request->input('nom_id'.$i));
				       	$candidate_id=$this->xssClean->clean_input($request->input('candidate_id'.$i));
				       	$party_id=$this->xssClean->clean_input($request->input('party_id'.$i));
				       	$currentvote=$this->xssClean->clean_input($request->input('currentvote'.$i));
				        
				        $filter1 = ['election_id'	=> $ele_details->ELECTION_ID,
										'ac_no'			=>$d->ac_no,
										'table_name'	=>$new_table,
										'round_id'		=>$round_id,
										'table_id'		=>$table_id,
										'nom_id'		=>$nom_id,
										'candidate_id'	=>$candidate_id,
								     ];
				        $records=$this->boothcounting->getpswiserecord($filter1); 
				       if(!isset($records)){
			         	$n_data = array('nom_id'=>$nom_id,'candidate_id'=>$candidate_id,'party_id'=>$party_id, 'ac_no'=>$CONST_NO ,
			         		'election_id'=>$ELECTION_ID, 'election_typeid'=>$ele_details->ELECTION_TYPEID , 'month'=>date("m"), 
			         		'year'=>date("Y"),'ps_no'=>$ps_no, 'cu_no'=>$cu_no, 'vvpat_no'=>$vvpat_no,
			         		'table_id'=>$table_id,	'round_id'=>$round_id, 'evm_vote'=>$currentvote,
			         		'cu_defect_id'=>$cu_defect_id, 'vvpat_defect_id'=>$vvpat_defect_id,
			         		'added_create_at'=>$currentdate,'created_at'=>$currentTime,'created_by'=>$d->officername); 
			             
			             		$this->commonModel->insertData($new_table, $n_data);
			         		}
			         	else {
			         		$u_data = array( 'month'=>date("m"),'year'=>date("Y"),'ps_no'=>$ps_no, 'cu_no'=>$cu_no, 
			         		'vvpat_no'=>$vvpat_no, 'evm_vote'=>$currentvote,
			         		'cu_defect_id'=>$cu_defect_id, 'vvpat_defect_id'=>$vvpat_defect_id,
			         		 'updated_at'=>$currentTime,'updated_by'=>$d->officername); 
			             
			             		$this->commonModel->updatedata($new_table,'id',$records->id,$u_data);
			         	}
				      
			        }
 
				  DB::commit();
			          
			    }
		        catch(\Exception $e){
		            DB::rollback();
		    
		            \Session::flash('error_mes', 'Please try again Data  do not inserted');
		            return Redirect::back();
		        }
		       

			       \Session::flash('success_mes', 'This record was successfully saved.');
                    return Redirect::to('roac/counting/polling-station-wisevote-entry');

     		}  // end function
	    
	 public function counting_type()
	    {    
	    	$data  = [];
	    	$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
            $data['user_data'] = $d;
            $data['ele_details'] = $ele_details;
              
            $filter = [
				'st_code' 	  => $ele_details->ST_CODE,
				'ac_no' 	  =>$ele_details->CONST_NO,
				'election_id' => $ele_details->ELECTION_ID,
				'election_id' => $ele_details->ELECTION_ID,
				'const_type' => $ele_details->CONST_TYPE,
				'table'		  =>"counting_master_".strtolower($d->st_code), 
			     ]; 
            $checkuser=$this->boothcounting->checkmasterrecords($filter);
            
            if(!isset($checkuser)){
                \Session::flash('error_mes', 'To Start Counting Process ROAC Needs to Activate Counting.');
                return Redirect::to('roac/counting/prepare-counting-data');
              }
            $st=getstatebystatecode($ele_details->ST_CODE);  
            $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);
            $data['st_code'] = $ele_details->ST_CODE;
            $data['st_name'] = $st->ST_NAME;
            $data['ac_no'] = $ele_details->CONST_NO;
            $data['ac_name'] = $ac->AC_NAME;

            $counting_type=getcountingtype($filter);
			$data['counting_type'] = $counting_type; 
            
			 return view($this->view_path.'.counting_type', $data);
        } 

    function verifycounting_type(Request $request){
    	    $user = Auth::user();
     		$this->validate(
	          $request, 
	            [
	              'counting_type'=> 'required|in:0,1,2',
	            ],
	            [
	            'counting_type.required' => 'Please select counting type',
	            'counting_type.in' => 'Please select counting type',
	            ]);
			
			$counting_type=$request->input('counting_type');     
			$d=$this->commonModel->getunewserbyuserid($user->id);
			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
            
             $filter = [
				'st_code' 	  => $ele_details->ST_CODE,
				'ac_no' 	  =>$ele_details->CONST_NO,
				'election_id' => $ele_details->ELECTION_ID,
				'election_id' => $ele_details->ELECTION_ID,
				'const_type' => $ele_details->CONST_TYPE,
			];

			$records=getcountingtype($filter);
            
		  DB::beginTransaction();
		    try{
		         $date = Carbon::now();
		         $currentTime = $date->format('Y-m-d H:i:s');
		         $currentdate = $date->format('Y-m-d');      
				if($records==false) {  
				 $record_i = array('st_code'=>$d->st_code,
	    				'ac_no'=>$d->ac_no,
	    			    'const_type'=>"AC",
	    				'election_id'=>$ele_details->ELECTION_ID,
	    				'election_typeid'=>$ele_details->ELECTION_TYPEID,
	    				'counting_type'=>$counting_type,
	    				'created_by'=>$d->officername,
	    				'added_create_at'=>$currentdate,
	    				'dist_no'=>$d->dist_no,
	    				'created_at'=>$currentTime); 
                 $this->commonModel->insertData('counting_ro_type',$record_i);
					\Session::flash('success_admin', 'RO Counting Type Successfully Added');
                 }
                else{
				 $record_u = array('st_code'=>$d->st_code,
	    				'ac_no'=>$d->ac_no,
	    			    'const_type'=>"AC",
	    				'election_id'=>$ele_details->ELECTION_ID,
	    				'election_typeid'=>$ele_details->ELECTION_TYPEID,
	    				'counting_type'=>$counting_type,
	    				'updated_by'=>$d->officername,
	    				'dist_no'=>$d->dist_no,
	    				'added_update_at'=>$currentdate,
	    				'updated_at'=>$currentTime);
				    
				    $this->commonModel->updatedata('counting_ro_type','id',$records->id,$record_u);
					        
					\Session::flash('success_admin', 'RO Counting Type Successfully Updated');
                    }
			     
			      DB::commit();
			     }
		 		catch(\Exception $e){
				     DB::rollback();
				    \Session::flash('error_mes', 'Please try again');
				     return Redirect::back();
				}

			   return Redirect::to('roac/counting/counting-center-details');
     		}  // end function
	       
}  // end class results-declaration    
