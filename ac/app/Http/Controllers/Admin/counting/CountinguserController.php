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
		use Illuminate\Support\Facades\URL;
		use Illuminate\Support\Facades\Crypt;
		use App\models\Counting\UsercountingModel;  
		use App\models\Counting\BoothCountingModel; 
		use App\models\Counting\PostalCountingModel;	
		use App\Helpers\LogNotification; 
class CountinguserController  extends Controller
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
        $this->users=new UsercountingModel;
        $this->boothcounting=new BoothCountingModel;
         $this->postal = new PostalCountingModel();
         if(!Auth::check()){ 
        	 return redirect('/officer-login');
        	}
        }

    protected function guard(){

        return Auth::guard('admin');
    	}
    //

    public function createcounting_user(Request $request) { 
         $input=$request->input();
		 
		 
		 //dd($input);
		 
          $user = Auth::user();
         
         $d=$this->commonModel->getunewserbyuserid($user->id);
         $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');

 		 $this->validate(
                $request, 
                    [
                     'name' => 'required',
                      //'email' => 'email',
                      'Phone_no'=>'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|numeric|digits:10|unique:officer_login',
                      'password'=>'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                      'two_step_pin'=>'required|pin|digits:6',
                     ],
                    [
                    'name.required' => 'Please enter name', 
                    // 'email.required' => 'Please enter your email',
                     'email.email' => 'Please enter valid email',
                      'Phone_no.required'=>'Please enter valid mobile no.',
                      'Phone_no.min'=>'Mobile number have minimum 10 digit',
                      'Phone_no.digits'=>'Mobile number have minimum 10 digit',
                      'Phone_no.numeric'=>'Please enter valid mobile no.',
                      'Phone_no.unique'=>'Mobile number already exist!',
                      'password.required'=>'Please enter password',
                      'password.regex'=>'The attribute must be more than 8 characters, should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character(#?!@$%^&*-).',
                       'two_step_pin.required'=>'Please enter pin',
                      'two_step_pin.pin'=>'Pin must have minimum 6 digit',
                     ]);
	      $user = Auth::user();
		  $d=$this->commonModel->getunewserbyuserid($user->id);
		  $filter = [
              'st_code'       => $ele_details->ST_CODE,
              'ac_no'         => $ele_details->CONST_NO,
              'election_id'   => $ele_details->ELECTION_ID,
              'id'            => $d->id,
               ];
       $countuser=$this->users->countcountinguser($filter);
       if($countuser>=5){
                \Session::flash('error_mes', 'You can create maximum five users.');
                return Redirect::back();
       }
		$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
             DB::beginTransaction();
          try{
               $name=$this->xssClean->clean_input(Check_Input($request->input('name')));
               $Phone_no=$this->xssClean->clean_input(Check_Input($request->input('Phone_no')));
               $email=$this->xssClean->clean_input(Check_Input($request->input('email')));
               $password=$this->xssClean->clean_input(Check_Input($request->input('password')));
               $two_step_pin=$this->xssClean->clean_input(Check_Input($request->input('two_step_pin')));
               $date = Carbon::now();
               $currentTime = $date->format('Y-m-d H:i:s'); 
               $code = Hash::make(str_random(10));
               $mobile_otp =rand(100000,999999);
			   
              //$count=countrecords('officer_login','parent_id',$user->id);
			  
			  $count = DB::table('officer_login')->select('id')->where('parent_id',$user->id)->where('role_id', 36)->count();

          
              $count++;
               $v =$d->officername."C".$count; 
              $record = array(
                          'st_code'=>$d->st_code,
                         'ac_no'=>$d->ac_no,
                         'pc_no'=>$d->pc_no,
                         'parent_id'=>$d->id,
                         'dist_no'=>$d->dist_no,
                         'password'=> hash('sha256',$password),
                         'two_step_pin'=> bcrypt($two_step_pin),
                         'officername'=>$v,
                         'designation'=>'RO-Computer Assistant', //ROAC OFFICE
                         'placename'=>$d->placename,
                         'role_id'=>'36', //21
                         'officerlevel'=>'AC',
                         'added_at'=>date('Y-m-d'),
                         'created_at'=>date('Y-m-d h:i:s'),
                         'election_id'=>$ele_details->ELECTION_ID,
                         'is_active'=>'1',
                         'password_flag'=>'1',
                         'pass_flag'=>'1',
                          'name'=>$name,
                          'Phone_no'=>$Phone_no,
                          'email'=>$email,
                          'mobile_otp' => $mobile_otp,
                          'otp_time' => $currentTime,
                          'auth_token' => $code,
             );
   
        
          $ch= DB::table('officer_login')->where('officername',$v)->first();
            
        if(!isset($ch)){
           		$this->commonModel->insertData('officer_login',$record); 
        		  $id = DB::getPdo()->lastInsertId();
		            $encodeid= encrypt_string($id);
                $passcreaturl = URL::to("/updateprofile/$encodeid");
              $html = "Dear ".$name.",\n\n";
                                  $html .= "Your account has been updated in ENCORE Portal"
                                      . "Your account must be activated before you use it. For activating your account and updating your particular, please click on the following link. Alternatively, you could copy and paste the link in your browser.\n\n";
                                  $html .= $passcreaturl."\n\n";
                                  $html .= "OTP: ".$mobile_otp."\n\n";
                                  $html .= "Login ID:  ".$v."\n\n";
                                  $html .= "Password:  ".$password."\n\n";
                                  $html .= "Login Pin:  ".$two_step_pin."\n\n";
                                  $html .= "For verifying  your account,  kindly enter OTP ".$mobile_otp." and this OTP has also sent on your registered mobile no.:\n\n";
                                  
                                  $html .= "Thanks & Regards,\n\n";
                                  $html .= "ENCORE Team,\n\n";

                                $html = strip_tags($html);
                                 //sendotpmail($email,'UserLogin Credential',$html);  
                                // mail ($email, 'UserLogin Credential',$html,'suvidha.eci.gov.in');
                            
                
          if($Phone_no!=""){
            $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." and Login ID: ".$v." , Password: ".$password." and Login Pin: ".$two_step_pin." for ENCORE Portal.Activation link has been sent on your email. ".$passcreaturl." Please enter that link and enter OTP to proceed. Do not share this OTP Team ECI";
              $response = SmsgatewayHelper::gupshup($Phone_no,$mob_message);
            }
			
			
			
			if(config('public_config.isCountingLoggerEnable')){
				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $user->officername ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'ENCORE';
				$ErrorMessage['TransectionType']= 'CountingPrepration';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'Create_User';
				$ErrorMessage['TransectionStatus']= 'SUCCESS';
				$ErrorMessage['LogDescription']= 'User Created Successfully';
				LogNotification::LogInfo($ErrorMessage);
			}
			
			
            \Session::flash('success_admin','You have successfully created');
          }
       }
      catch(\Exception $e){
                DB::rollback();
        
                \Session::flash('error_mes', 'Please try again!!!');
                return Redirect::back();
            } 
          DB::commit();  
          
		          
           
               return Redirect::back();
        }
	public function counting_user(){
		
		
		$counting_preparation = \DB::table('setting')->select('*')->where('key','counting_preparation')->first();
			 if($counting_preparation->value < 1){
			  \Session::flash('error_mes', 'Counting preparation menu is not enable. ');
			  return Redirect::back();
		  }
		
		
		
			   $data  = [];
	    	$user = Auth::user();
	    	 
			   $d=$this->commonModel->getunewserbyuserid($user->id);
			   $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
            
            $st=getstatebystatecode($ele_details->ST_CODE);  
            $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 

             $data['user_data']      = $d;
             $data['ele_details']    = $ele_details;
             $data['st_code']        = $ele_details->ST_CODE;
             $data['ac_no']          = $ele_details->CONST_NO;
             $data['ac_name']        = $ac->AC_NAME;
             $data['st_name']        = $st->ST_NAME;
 

          $filter = [
      				'st_code'       => $ele_details->ST_CODE,
      				'ac_no' 		    => $ele_details->CONST_NO,
      				'election_id'	  => $ele_details->ELECTION_ID,
      				'id'	          => $d->id,
      				 ];
		  
            $lists=$this->users->getalluserbyparentid($filter);
      		  $countuser=$this->users->countcountinguser($filter);
            $countingStart=checkcountingstart($filter);
			
            
      		  $data['lists']=$lists;
      		  $data['countuser']=$countuser;
            $data['countingStart']=$countingStart;

            
		      return view($this->view_path.'.counting_user', $data);  
	}    	
  // update section   sachchida
  public function update_counting_user(Request $request) { 
         $input=$request->input();
		 
		 
		 
		 
         $this->validate(
                $request,[
                     'name1' => 'required',
                      //'email1' => 'email',
                      'Phone_no1'=>'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|numeric|digits:10',
                      'password1'=>'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                      'two_step_pin1'=>'required|pin|digits:6',
                     ],
                    [
                    'name1.required' => 'Please enter name', 
                     //'email1.required' => 'Please enter your email',
                     'email1.email' => 'Please enter valid email',
                      'Phone_no1.required'=>'Please enter valid mobile no.',
                      'Phone_no1.min'=>'Mobile number should have 10 digit',
                      'Phone_no1.digits'=>'Mobile number should have 10 digit',
                      'Phone_no1.numeric'=>'Please enter valid mobile no.',
                       
                      'password1.required'=>'Please enter password',
                      'password1.regex'=>'The attribute must be more than 8 characters, should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character(#?!@$%^&*-).',
                       'two_step_pin1.required'=>'Please enter pin',
                      'two_step_pin1.pin'=>'Pin should have 6 digit',
                     ]);
					 
				//dd($input);	 
					 
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
               $off_id=$this->xssClean->clean_input(Check_Input($request->input('off_id')));
               $name=$this->xssClean->clean_input(Check_Input($request->input('name1')));
               $Phone_no=$this->xssClean->clean_input(Check_Input($request->input('Phone_no1')));
               $email=$this->xssClean->clean_input(Check_Input($request->input('email1')));
               $password=$this->xssClean->clean_input(Check_Input($request->input('password1')));
               $two_step_pin=$this->xssClean->clean_input(Check_Input($request->input('two_step_pin1')));
               $date = Carbon::now();
               $currentTime = $date->format('Y-m-d H:i:s'); 
               $code = Hash::make(str_random(10));
               $mobile_otp =rand(100000,999999);
               $v =$d->officername.$Phone_no; 
           DB::beginTransaction();
          try{
              $record = array(
                          'password'=> hash('sha256',$password),
                          'two_step_pin'=> bcrypt($two_step_pin),
                          'name'=>$name,
                          'Phone_no'=>$Phone_no,
                          'email'=>$email,
                          'mobile_otp' => $mobile_otp,
                          'otp_time' => $currentTime,
                          'auth_token' => $code,
                        );
              $ch= DB::table('officer_login')->where('id',$off_id)->first();

              updatedata('officer_login','id',$off_id,$record);
              
              $id =$off_id;
                $encodeid= encrypt_string($id);
                $passcreaturl = URL::to("/updateprofile/$encodeid");
              $html = "Dear ".$name.",\n\n";
                                  $html .= "Your account has been updated in ENCORE Portal"
                                      . "Your account must be activated before you use it. For activating your account and updating your particular, please click on the following link. Alternatively, you could copy and paste the link in your browser.\n\n";
                                  $html .= $passcreaturl."\n\n";
                                  $html .= "OTP: ".$mobile_otp."\n\n";
                                  $html .= "Login ID:  ".$ch->officername."\n\n";
                                  $html .= "Password:  ".$password."\n\n";
                                  $html .= "Login Pin:  ".$two_step_pin."\n\n";
                                  $html .= "For verifying  your account,  kindly enter OTP ".$mobile_otp." and this OTP has also sent on your registered mobile no.:\n\n";
                                  
                                  $html .= "Thanks & Regards,\n\n";
                                  $html .= "ENCORE Team,\n\n";

                                $html = strip_tags($html);
                                 //sendotpmail($email,'UserLogin Credential',$html);  
                                // mail ($email, 'UserLogin Credential',$html,'suvidha.eci.gov.in');
                            
                
          if($Phone_no!=""){
            $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." and Login ID: ".$v.", Password: ".$password." and Login Pin: ".$two_step_pin." for ENCORE Portal.Activation link has been sent on your email. ".$passcreaturl." Please enter that link and enter OTP to proceed. Do not share this OTP Team ECI";
              $response = SmsgatewayHelper::gupshup($Phone_no,$mob_message);
            }   
          }
      catch(\Exception $e){
                DB::rollback();
        
                \Session::flash('error_mes', 'Please try again!!!');
                return Redirect::back();
            } 
          DB::commit(); 
              \Session::flash('success_admin','You have successfully updated.');
               return Redirect::back();
        }

  public  function user_assign_table(){
  			$data  = [];
	    	$user = Auth::user();
	    	$d=$this->commonModel->getunewserbyuserid($user->id);
			  $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
            
            $st=getstatebystatecode($ele_details->ST_CODE);  
            $ac=getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO); 

             $data['user_data']      = $d;
             $data['ele_details']    = $ele_details;
             $data['st_code']        = $ele_details->ST_CODE;
             $data['ac_no']          = $ele_details->CONST_NO;
             $data['ac_name']        = $ac->AC_NAME;
             $data['st_name']        = $st->ST_NAME;

            
              $filter = [
            				    'st_code'       => $ele_details->ST_CODE,
            				    'ac_no' 		    => $ele_details->CONST_NO,
            				    'election_id'	  => $ele_details->ELECTION_ID,
            				    'id'	          => $d->id,
            				 ];
           $countingstart=checkcountingstart($filter);
           $evmfinalized=evm_votes_finalized($filter);
          $data['evmfinalized']=$evmfinalized;
       // if(isset($countingstart)){
       //          \Session::flash('error_mes', 'Counting is start, no any Changes');
       //             return Redirect::to('roac/counting/round-schedule-details');
       //      }
           $round_details=$this->postal->roundsechudle($filter);
               
            if(!isset($round_details)) {
                \Session::flash('error_mes', 'Rounds not scheduled! Please schedule rounds.');
                return Redirect::to('roac/counting/round-schedule-details');
               }   
		$lists=$this->users->getalluser($filter); //
		$table_details=$this->boothcounting->get_table_master_details($filter);
    $results=$this->users->getalluserbytablesdetails($filter);
    $assigntable=$this->users->getallassigntable($filter);
    if(!isset($table_details)) {
       \Session::flash('error_mes', 'Please enter counting center details.');
                return Redirect::to('roac/counting/counting-center-details');
    }
    if($table_details->total_no_ps==0 || $table_details->total_no_tables==0 || $table_details->total_no_rounds==0){
       \Session::flash('error_mes', 'Please enter counting center details.');
                return Redirect::to('roac/counting/counting-center-details');
    }

    
       
        $data['lists'] = $lists;
        $data['usercount'] = count($lists);
        $data['total_no_ps'] = $table_details->total_no_ps;
        $data['total_no_tables'] = $table_details->total_no_tables;
        $data['total_no_rounds'] = $table_details->total_no_rounds;
        $data['results'] = $results;
        $data['listassigntable'] = $assigntable['assigntable'];
        $data['totalassigntable'] = $assigntable['countassigntable'];
        $data['total_unassigntable'] = $data['total_no_tables']-$assigntable['countassigntable'];
         $data['countingstart'] = $countingstart;
		    
        return view($this->view_path.'.user_assign_table', $data);  
  }   
    public function verify_user_assign(Request $request){
        $input =$request->all();
       // dd($input);
		    $rules = [];
		  $this->validate($request,[
        		'users'=>'required',
        		'tables'=>'required',
        	],
			    [
	          'users.required' => 'Please select user',
	          'tables.required' => 'Please select tables',
	        ]);
       $users=$request->input('users');
       $tables=$request->input('tables');
       $newtable='';
        foreach ($tables as $key => $value) {
			if($key!=0){
				$newtable=$newtable.",".$value;
			}else{
				$newtable=$newtable.$value;
			}
        }
       $user = Auth::user();
	   $d=$this->commonModel->getunewserbyuserid($user->id);
	   $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
        $insdata = array('st_code'=>$d->st_code,
                         'ac_no'=>$d->ac_no,
                         'election_id'=>$ele_details->ELECTION_ID,
                         'election_typeid'=>$ele_details->ELECTION_TYPEID,
                         'users_name'=>$users,
                         'table_no'=>$newtable,
                         'added_create_at'=>date('Y-m-d'),
                         'created_at'=>date('Y-m-d H:i:s'),
                         'dist_no'=>$d->dist_no,
                         'deleted'=>'0',
                         'created_by'=>$d->officername,);
						 //dd($users);
		foreach($tables as $k=>$v){
			$query_check = DB::table('counting_users_table_details')->whereRaw("find_in_set($v,table_no)")->where('ac_no','=',$d->ac_no)->where('st_code','=',$d->st_code)->where('deleted','=',0)->get();
			if(count($query_check)>0){
				\Session::flash('error_mes', 'Tables already assigned to another user. Please un-assigned to make changes.');
                return Redirect::back();	
			}
		}
		//die();
		
		
        $ch= DB::table('counting_users_table_details')->where('users_name',$users)->first();
        if(isset($ch))
          {
                    updatedata('counting_users_table_details','id',$ch->id,$insdata); 	
          }
        else{
                   insertData('counting_users_table_details',$insdata); 
          }
		  		  
				if(config('public_config.isCountingLoggerEnable')){
					$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
					$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
					$ErrorMessage['MobNo']= $user->officername ?? '';
					$ErrorMessage['applicationType']= 'WebApp';
					$ErrorMessage['Module']= 'ENCORE';
					$ErrorMessage['TransectionType']= 'CountingPrepration';
					$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
					$ErrorMessage['TransectionAction']= 'User_Assign';
					$ErrorMessage['TransectionStatus']= 'SUCCESS';
					$ErrorMessage['LogDescription']= 'User Assign Successfully';
					LogNotification::LogInfo($ErrorMessage);
				}
		  
        \Session::flash('success_admin','You have successfully saved');
           return Redirect::back();		 
    }  



public function remove_counting_users(Request $request) 
        {
			
			$user = Auth::user();
	    	$d=$this->commonModel->getunewserbyuserid($user->id);
			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
            
              $filter = [
						'st_code'       => $ele_details->ST_CODE,
						'ac_no' 		    => $ele_details->CONST_NO,
						'election_id'	  => $ele_details->ELECTION_ID,
						'id'	          => $d->id,
					];
					
           $countingstart=checkcountingstart($filter);
           $evmfinalized=evm_votes_finalized($filter);

			
			if($evmfinalized==0){  
                if(!isset($countingstart)){  

             $nid=decrypt_string($request->input('id'));
             $remov = array('deleted'=>'1',
                           'added_update_at'=>date('Y-m-d'),
                           'updated_at'=>date('Y-m-d h:i:s'),
                           'updated_by'=>Auth::user()->officername,);
            updatedata('counting_users_table_details','id',$nid,$remov); 
            \Session::flash('success_admin','you have Successfully Removed');
             return Redirect::back();  
			 
				}else{
					\Session::flash('error_mes','Counting has been started so now you can not un-assign Tables.');
					return Redirect::back(); 
				} 
			}else{
				\Session::flash('error_mes','Counting has been started so now you can not un-assign Tables.');
				return Redirect::back(); 
			}  
			
        }    



  /* public static function remove_counting_users(Request $request) 
          {
             $nid=decrypt_string($request->input('id'));
             $remov = array('deleted'=>'1',
                           'added_update_at'=>date('Y-m-d'),
                           'updated_at'=>date('Y-m-d h:i:s'),
                           'updated_by'=>Auth::user()->officername,);
            updatedata('counting_users_table_details','id',$nid,$remov); 
            \Session::flash('success_admin','you have Successfully Removed');
             return Redirect::back();  
          }  */   
}  // end class results-declaration    
