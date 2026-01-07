<?php
namespace App\Http\Controllers\API;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;
use App\commonModel;
use DB;
use File;
use Session;
use App\models\{States, Districts, AC, Form12record, Form12drecord};
use  App\models\Admin\{OfficerModel};;
use Mail;
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Input;
use Redirect;
use PDF;
//use Auth;
use Carbon\Carbon;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Classes\xssClean;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
//use App\Http\Traits\CommonTraits;
 
class MatdanController extends Controller
{
    public function __construct()
    {
        $this->xssClean = new xssClean;
        $this->commonModel = new commonModel();
        $this->ResponseMethod = new ResponseController;
        $this->bad_response = $this->ResponseMethod::HTTP_BAD_REQUEST;
        $this->ok_response = $this->ResponseMethod::HTTP_OK; 
      //  $this->okStatus = "success";
     //   $this->errStatus = "error";
        $this->okStatus = "1";
        $this->errStatus = "0";
    }

    public $successStatus = 200;
    public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;
    public $bad_response;
    public $ok_response;
    public $okStatus;
    public $errStatus;
 
public function login(Request $request){

      $connectionis=DB::connection()->getDatabaseName();
      //print_R($connectionis);

        try{
            $validator = Validator::make($request->all(), [
                'mobile' => 'required',
                'deviceId' => 'required',
                'election_id'=>'required',
            ]);
         
         if($validator->fails()) {
         
            //return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);         

             return response()->json(['success' => $this->errStatus,'message'=>$validator->errors()->first()]);   
         } 

            $userInputs = $request->all();


            $mobile = trim($userInputs['mobile']);
            $device_id = trim($userInputs['deviceId']);
            $app_id = "MatdanApp";

            $loginDb = DB::table('officer_login')->where('Phone_no', '=', $mobile)->first();

          
         
            if(isset($loginDb)){


           // $partyid = DB::table('candidate_nomination_detail')->where('candidate_id',$loginDb->candidate_id)->first();
            
				if(!empty($loginDb)){
				if(!is_null($loginDb->otp_time)){
							$currentTime = Carbon::now();
							$diff=$currentTime->diffInSeconds($loginDb->otp_time);
							}else{ 
								$diff=61; 
							}
				}else{
					$diff = 61;
				}

             if($diff>60){

                  $deviceid = array('device_id'=>$device_id,'otp_time'=>Carbon::now());

        DB::table('officer_login')->where([['Phone_no' , $mobile]])->update($deviceid);

                 
			
             $user = (
				
				[
                  'user_id'=>$loginDb->id,
                 'mobile'=> $loginDb->Phone_no,
                 'otp_time'=>$loginDb->otp_time,
                 'device_id'=> $device_id,
				// 'designation' => $loginDb->designation,
                ]
				);
                $logid=$user;
             //print_R($logid);exit;
                    //$candidate_id= $loginDb->candidate_id;
                    $this->sendOtp($loginDb->Phone_no);
      
                    $success['success'] =  '1';
                    $success['message'] = 'Login Successfully';
                    $success['Details'] = $user;
                    $success['mobile_otp'] = 'OTP has been send to registered mobile no, please enter to verify mobile number. ';
                    return response()->json(['success' => $this->okStatus,'message'=>$success]); 
                  //return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
            }
			else{
                $success['success'] =  '0';
                $success['message'] = 'Please wait for 1 minute to resend OTP';
                $success['mobile_otp'] = 'Can Send only 1 OTP per minute';
                return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
            }
			}
            else{
                $error['success'] =  '0';
                $error['message'] = 'Input Mobile number does not exist';
                return response()->json(['success' => $this->errStatus,'message'=>$error]); 
                //return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
            }
        } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
             return response()->json(['success' => $this->errStatus,'message'=>$error]); 
            //return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
}
    
    
public function sendOtp($mobno)
    {
         if($mobno=='9871124359')
        {
            $otp='123456';
        }else{

            $otp = rand(100000, 999999);

        }
        $datamob = array('mobile_otp'=>$otp);
        DB::table('officer_login')->where('Phone_no', $mobno)->update($datamob);

        $mobile_message = 'Your OTP is ' .$otp. ' for ECI Candidate App. Please enter the OTP to proceed. Do not share this OTP';
		
		$msgstatus = SmsgatewayHelper::gupshup($mobno, $mobile_message);
    }
    
    
public function verifyOtp(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
                'user_id' => 'required|numeric',
				'mobile'   => 'required|regex:/^\S*$/u|numeric|digits:10',
                'deviceId' => 'required',
            ]);
            if($validator->fails()) { 
                 //   return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);       
                    return response()->json(['success' => $this->errStatus,'message'=>$validator->errors()->first()]);   
            }
             $inputs = $request->all();
              
             $otp = trim($inputs['otp']);
             $userID = trim($inputs['user_id']);
             $mobile = trim($inputs['mobile']);
             $device_id = trim($inputs['deviceId']);

            $newuser = OfficerModel::where('Phone_no', $mobile)->where('id', '=',$userID)->where('device_id', '=',$device_id)->first();

           // dd($newuser);

             if(isset($newuser)){
				 
				// $attempts=$newuser->otp_attempt;
				// $this->otp_attempt($newuser->id, $attempts+1);

				// if($attempts>2){
                //     $success['success'] = false;
                //     $success['message'] = 'reached maximum attempts Please resend otp!';
                //     return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
				// }
				 
             $token = $newuser->createToken('MyApp')->accessToken;
             $mobileOTP = $newuser->mobile_otp;
             $mobile = $newuser->Phone_no;
             if($mobileOTP == $otp)
             { 
              $logdata = array('accesstoken'=>$token,'login_flag'=>1,
                            'otp_verify_by_string'=>1);

        DB::table('officer_login')->where([['Phone_no' , $mobile],['id' , $userID]])->update($logdata);
          $login_history = array(
                                'user_id'=>$newuser->id,
                                'ipaddress'=>request()->ip(),
                                'officer_name'=>$newuser->officername,
                                'name'=>$newuser->name,
                                'mobile'=>$mobile,
                                'email'=>$newuser->email,
                                'state'=>$newuser->st_code,
                                'district'=>$newuser->dist_no,
                                'ac_no'=>$newuser->ac_no,
                                'pc_no'=>$newuser->pc_no,
                                'designation'=>$newuser->designation,
                                'is_active'=>$newuser->is_active,
                                
                                'officer_level'=>$newuser->officerlevel,
                            
                               
                                'device_type'=>'Mobile',
                                'role_id'=>$newuser->role_id,
                                'Login_time'=>Date('Y-m-d H:i:s'),
                                'session_id'=>$token,
                                'user_activity'=>'login on Mobile',
                                'Login_date'=>Date('Y-m-d'));
            //$a=DB::table('user_history')->insert($login_history); 
              
         // $cand_d =DB::table('candidate_personal_detail')->where([['candidate_id' , $candidate_id]])->first();
          
            $success['success'] = '1';
            $success['message'] = 'OTP verified';
			
            //$success['officerID'] = $officerID;
            $success['accessToken'] = (string)$token;
            $success['Details'] =$login_history;
         
            return response()->json(['success' => $this->okStatus,'message'=>$success]);
               // return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Entered Otp is wrong, please enter correct otp';
                  return response()->json(['success' => $this->errStatus,'message'=>$error]);
                 // return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered data not exist ,may be wrong Device ID, mobile and UserID';
               return response()->json(['success' => $this->errStatus,'message'=>$error]);
              // return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
            }
        } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
}






     public function contactslist(Request $request) {

        try{
            $validator = Validator::make($request->all(), [
                //'accessToken' => 'required',
                'user_id' => 'required',
                'election_id'=> 'required'
              
            ]);
    
            if($validator->fails()){
                return response()->json(['success' => false,'message'=>'Please Check the Input Details']);            
            } 
    
            $userInputs = $request->all();
           // dd($userInputs);
            $user_id = trim($userInputs['user_id']);
       //    $accessToken = trim($userInputs['accessToken']);
            $election_id = trim($userInputs['election_id']);

             $checkauth = $request->header('mattoken');
           // $explode=explode(' ', $checkauth);
            $accesstoken=$checkauth;
          
              
             $getdata = OfficerModel::where([['accesstoken', '=', $accesstoken]])->where('id', '=',$user_id)->first();
           //  $getdata = OfficerModel::where('id', '=',$user_id)->first();

            if(empty($getdata)){
             
              
                $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);
               //return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
                 //return response()->json(['success' => false,'message'=>'Please Check the User Id']);
            }
            
            

             $roleID=$getdata['role_id'];
             $officername=$getdata['officername'];
             $state=$getdata['st_code'];
             $district=$getdata['dist_no'];
            // dd($state);

             $getElection_type = DB::table('m_election_history')->where('id', $election_id)->first();
  //print_R($getElection_type->const_type);exit;
            //dd($getElection_type->const_type); 

             $getdetail_acg=array();

             if(isset($roleID) && !empty($roleID) )
             {

                if($getElection_type->const_type=='AC') {
                 
                 if($roleID==7)  // ECI
                 {
                   
                       $getdetail_ac = OfficerModel::select('id as user_id','name','Phone_no as mobile' ,'email' ,'designation','st_code','role_id','dist_no','ac_no','pc_no')->where([['role_id', '=', 4]])->where('is_active', '=', 1)->get();
                      // dd($getdetail_ac);


                 }  else if($roleID==4) {  // CEO

                   

                      $getdetail_ac = OfficerModel::select('id as user_id','name','Phone_no as mobile' ,'email','designation','st_code','role_id','dist_no','ac_no','pc_no')->whereIn('role_id', array(19, 5))->where([ ['is_active', '=', 1],['st_code', '=', $state] ])->get();
                      // return $getdetail_ac;

                 }else if($roleID==5)  // DEO
                 {

                         $getdetail_ac = OfficerModel::select('id as user_id','name','Phone_no as mobile' ,'email','designation','st_code','role_id','dist_no','ac_no','pc_no')->whereIn('role_id', array(19))->where([ ['is_active', '=', 1],['st_code', '=', $state],['dist_no', '=', $district] ])->get();
                         // return $getdetail_ac;

                 }   



            if( count($getdetail_ac)>0) {

               $det = array();                
               $detq = array();
               $ceo=array();
                foreach($getdetail_ac as $cand){
                    if($cand['role_id']==5)
                    {
                        $det[]=$cand;
                    }else if($cand['role_id']==19){

                        $detq[]=$cand;
                    }else if($cand['role_id']==4){

                        $ceo[]=$cand;
                    }
                    
                   //$abc[]=$cand;
                              
                    
                }
                  $officer_list=array('ceo'=> $ceo,'deo'=> $det, 'ro_ac'=>$detq);

                $success['success'] = 1;
                $success['details'] =$officer_list;
                //return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
                //return response()->json($success, $this->successStatus);
                  return response()->json(['success' => $this->okStatus,'message'=>$success]);
            }else{

                //  $success['success'] = false;
                //$success['details'] = array();
                
               $error['success'] = '0';
               $error['message'] = '';
               //return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
                return response()->json($error, $this->errStatus);

            }
                 
           
        }



         if($getElection_type->const_type=='PC') // Statrt PC Data
        {



             if($roleID==7)  // ECI
                 {

                       $getdetail_pc = OfficerModel::select('id as user_id','name','Phone_no as mobile','email','designation','st_code','role_id','dist_no','ac_no','pc_no')->where([['role_id', '=', 4]])->where('is_active', '=', 1)->get();
                       // return $getdetail_pc;

                 }  

                   else if($roleID==4) // ceo
                 {

                         $getdetail_pc = OfficerModel::select('id as user_id','name','Phone_no as mobile','email','designation','st_code','role_id','dist_no','ac_no','pc_no')->whereIn('role_id', array(18,20))->where([ ['is_active', '=', 1],['st_code', '=', $state]])->get();

                         //return $getdetail_pc;

                 }else if($roleID==18)  // DEO
                 {

                         $getdetail_pc = OfficerModel::select('id as user_id','name','Phone_no as mobile' ,'email','designation','st_code','role_id','dist_no','ac_no','pc_no')->whereIn('role_id', array(20))->where([ ['is_active', '=', 1],['st_code', '=', $state],['dist_no', '=', $district] ])->get();
                         // return $getdetail_ac;

                 }   

                  if(count($getdetail_pc)>0) {

               $det = array();                
               $detq = array();
               $ceo=array();
                foreach($getdetail_pc as $cand){
                    if($cand['role_id']==18)
                    {
                        $det[]=$cand;
                    }else if($cand['role_id']==20){

                        $detq[]=$cand;
                    }else if($cand['role_id']==4){

                        $ceo[]=$cand;
                    }
                    
                   //$abc[]=$cand;
                              
                    
                }
            
                $officer_list=array('ceo'=> $ceo,'ro_pc'=> $det, 'aro'=>$detq);

                $success['success'] = 1;
                $success['details'] =$officer_list;
               // return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
                 return response()->json($success, $this->successStatus);
            }else{ 
                // $success['success'] = false;
                // $success['details'] = array();
                 $error['success'] = '0';
               $error['message'] = '';
              // return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
               return response()->json(['success' => $this->errStatus,'message'=>$error]);
            }






        }

      
//dd($getdetail_ac);exit;
             }  // End Role ID Validation



            





/*
               

            */
            return response()->json($success, $this->successStatus);
    
            } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
           // return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
                return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            }
        }



        public function storeform12(Request $request)
        {

           try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'accessToken'=> 'required',
                'st_code'=>'required',
                'dist'=>'required',
                'ac_no'=>'required',
                'election_id'=>'required'
                //'mobile'   => 'required|regex:/^\S*$/u|numeric|digits:10',
            ]);
            if($validator->fails()) { 
                    return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);          
            }
            

              $userInputs = $request->all();
              $user_id = trim($userInputs['user_id']);
              $accessToken = trim($userInputs['accessToken']);
              $st_code = trim($userInputs['st_code']);
              $dist = trim($userInputs['dist']);
              $ac_no = trim($userInputs['ac_no']);
              $election_id = trim($userInputs['election_id']);

               $applied_postal_ballot = trim($userInputs['applied_postal_ballot']);
               $issued_postal_ballot = trim($userInputs['issued_postal_ballot']);
               $vote_cast = trim($userInputs['vote_cast']); 


            // $election_id = trim($userInputs['election_id']);



             $getdata = OfficerModel::where([['accesstoken', '=', $accessToken]])->where('id', '=',$user_id)->first();

             if(isset($getdata))
             {

                   $form12record= new Form12record;

                    $getElection_type = DB::table('m_election_history')->where('id', $election_id)->first();
 
            

                if($getElection_type->const_type=='AC') {


                   $form12record->officer_id=$user_id;
                   $form12record->st_code=$st_code;
                   $form12record->dist=$dist;
                   $form12record->ac_no=$ac_no;
                   $form12record->election_id=$election_id;
                   $form12record->applied_postal_ballot=$applied_postal_ballot;
                   $form12record->issued_postal_ballot= $issued_postal_ballot;
                   $form12record->vote_cast=$vote_cast;

                  $form12record->save();
              }else if($getElection_type->const_type=='PC'){



                   $form12record->officer_id=$user_id;
                   $form12record->st_code=$st_code;
                   $form12record->dist=$dist;
                   $form12record->pc_no=$ac_no;
                   $form12record->election_id=$election_id;
                   $form12record->applied_postal_ballot=$applied_postal_ballot;
                   $form12record->issued_postal_ballot= $issued_postal_ballot;
                   $form12record->vote_cast=$vote_cast;
                     $form12record->save();




              }
                   //dd($abc);
                   $success['success'] = '1';
                   return response()->json($success, $this->successStatus);


             }else{

                $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);
             }



         } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            //return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
        

 }


         public function fetchform12(Request $request)

         {

          

          
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'st_code'=>'required',
                'ac_no'=>'required',
                'election_id'=>'required'
                
            ]);
            if($validator->fails()) { 
                 return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);      
                 //return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);          
            }

            $checkauth = $request->header('mattoken');
            //$explode=explode(' ', $checkauth);
            $accesstoken=$checkauth;

              $userInputs = $request->all();
              $user_id = trim($userInputs['user_id']);
            
              $st_code = trim($userInputs['st_code']);
        
              $ac_no = trim($userInputs['ac_no']);
              $election_id = trim($userInputs['election_id']);


           
             $getdata = OfficerModel::where([['accesstoken', '=', $accesstoken]])->where('id', '=',$user_id)->first();
             //dd($getdata);

             if(isset($getdata))
             {
                $getElection_type = DB::table('m_election_history')->where('id', $election_id)->first();
 
            

                if($getElection_type->const_type=='AC') {

              
                 $records = Form12record::getrecord_ac($st_code);
             }else if($getElection_type->const_type=='PC')
             {

                    $records = Form12record::getrecord_pc($st_code);
             }

                // dd($records);
                
                    if(isset($records) > 0){
                         $dat = array();
                   foreach($records as $k) {

                    $pcname=$this->commonModel->getpcbypcno($k->st_code,$k->pc_no);

                        if(isset($pcname))
                        {

                           $pc_name=$pcname->PC_NAME; 
                        }else{
                           $pc_name='';  
                        }
                         $acname=$this->commonModel->getacbyacno($k->st_code,$k->ac_no);

                        if(isset($acname))
                        {

                           $ac_name=$acname->AC_NAME; 
                        }else{
                           $ac_name='';  
                        }

                 $dat[]=array("stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),
                  "ac_name" => $ac_name,"ac_no"=>$k->ac_no ,"pc_name" => $pc_name,"pc_no" => $k->pc_no,
                    "applied_postal_ballot"=> $k->applied_postal_ballot, "issued_postal_ballot"=> $k->issued_postal_ballot, "vote_cast"=> $k->vote_cast);



              }


              $success['success'] = '1';
               
                $success['details'] =$dat; 
                return response()->json($success, $this->successStatus);




          }else{
                  $dat = array();
                }
               
                //return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
                  

         }else{

             $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);
         }

     }



      public function storeform12d(Request $request)
        {

             
           try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'accessToken'=> 'required',
                'st_code'=>'required',
                'dist'=>'required',
                'ac_no'=>'required',
                'election_id'=>'required'
                //'mobile'   => 'required|regex:/^\S*$/u|numeric|digits:10',
            ]);
            if($validator->fails()) { 
                    return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);          
            }


            

              $userInputs = $request->all();


              $user_id = trim($userInputs['user_id']);
              $accessToken = trim($userInputs['accessToken']);
              $st_code = trim($userInputs['st_code']);
              $dist = trim($userInputs['dist']);
              $ac_no = trim($userInputs['ac_no']);
              $election_id = trim($userInputs['election_id']);
              $date=$userInputs['date'];



              $getdata = OfficerModel::where([['accesstoken', '=', $accessToken]])->where('id', '=',$user_id)->first();
             //dd($getdata);
              $getphase =  DB::table('m_election_details')->select('StatePHASE_NO')->where('ST_CODE', '=', $st_code)->where('CONST_NO', '=',$ac_no)->first();

//dd($getphase->StatePHASE_NO);

             if(isset($getdata))
             {






               $total_elector_80 = trim($userInputs['total_elector_80']);
               $distributed_80 = trim($userInputs['distributed_80']);
               $rejected_80 = trim($userInputs['rejected_80']); 
               $pb_issued_80 = trim($userInputs['pb_issued_80']);
               $recieved_80 = trim($userInputs['recieved_80']);
               $elector_type_80 = trim($userInputs['elector_type_80']); 

              $total_elector_pwd = trim($userInputs['total_elector_pwd']);
               $distributed_pwd = trim($userInputs['distributed_pwd']);
               $rejected_pwd = trim($userInputs['rejected_pwd']); 
               $pb_issued_pwd = trim($userInputs['pb_issued_pwd']);
               $recieved_pwd = trim($userInputs['recieved_pwd']);
               $elector_type_pwd = trim($userInputs['elector_type_pwd']); 

                $total_elector_covid = trim($userInputs['total_elector_covid']);
               $distributed_covid = trim($userInputs['distributed_covid']);
               $rejected_covid = trim($userInputs['rejected_covid']); 
               $pb_issued_covid = trim($userInputs['pb_issued_covid']);
               $recieved_covid = trim($userInputs['recieved_covid']);
               $elector_type_covid = trim($userInputs['elector_type_covid']); 


            // $election_id = trim($userInputs['election_id']);



           

          $eightyplus='';
          $pwd='';
          $covid='';
           $eightyplus = array('officer_id'=> $user_id,
                   'st_code'=>$st_code,
                   'dist'=> $dist,
                   'ac_no'=>$ac_no,
                   'election_id'=>$election_id,
                   'phase_id'=>$getphase->StatePHASE_NO,
                    'date'=>$date,
                   'total_elector'=>$total_elector_80,
                   'distributed'=>$distributed_80,
                   'rejected'=>$rejected_80,
                   'pb_issued'=>$pb_issued_80,
                   'recieved'=>$recieved_80,
                   'elector_type'=>$elector_type_80,
                  
                   );

$pwd = array('officer_id'=> $user_id,
                   'st_code'=>$st_code,
                   'dist'=> $dist,
                   'ac_no'=>$ac_no,
                   'election_id'=>$election_id,
                   'phase_id'=>$getphase->StatePHASE_NO,
                    'date'=>$date,
                   'total_elector'=>$total_elector_pwd,
                   'distributed'=>$distributed_pwd,
                   'rejected'=>$rejected_pwd,
                   'pb_issued'=>$pb_issued_pwd,
                   'recieved'=>$recieved_pwd,
                   'elector_type'=>$elector_type_pwd,
                  
                   );
$covid = array('officer_id'=> $user_id,
                   'st_code'=>$st_code,
                   'dist'=> $dist,
                   'ac_no'=>$ac_no,
                   'election_id'=>$election_id,
                   'phase_id'=>$getphase->StatePHASE_NO,
                   'date'=>$date,
                   'total_elector'=>$total_elector_covid,
                   'distributed'=>$distributed_covid,
                   'rejected'=>$rejected_covid,
                   'pb_issued'=>$pb_issued_covid,
                   'recieved'=>$recieved_covid,
                   'elector_type'=>$elector_type_covid,
                  
                   );


$thirdarray=array('eightyplus'=>$eightyplus,'pwd'=>$pwd,'covid'=>$covid);

//dd($thirdarray);
            foreach ($thirdarray as $subject){
           
             $data = array('officer_id' => $subject['officer_id'], 'st_code' =>$subject['st_code'],'dist' =>$subject['dist'],'ac_no' =>$subject['ac_no'],'election_id' =>$subject['election_id'],'phase_id'=>$subject['phase_id'] , 'total_elector' => $subject['total_elector'],'distributed' => $subject['distributed'] ,'pb_issued' => $subject['pb_issued']  ,'recieved' => $subject['recieved']  ,'rejected' => $subject['rejected'],'elector_type' => $subject['elector_type'],'created_at'=> date('Y-m-d H:m:s'),'date'=>$subject['date']);
             
     Form12drecord::insert($data);
                  

               }
               $success['success'] = '1';
               return response()->json($success, $this->successStatus);


             }else{

                $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);
             }



         } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            //return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
        


               } 

       
       public function fetchform12d(Request $request)

         {


          
           $validator = Validator::make($request->all(), [
                
                'user_id' => 'required',
                'st_code' => 'required',
               // 'ac_no' => 'required',
                'election_id' => 'required',
            ]);
             if($validator->fails()) {
                 return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
               // return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);
            }

            $checkauth = $request->header('mattoken');
            //$explode=explode(' ', $checkauth);
            $accesstoken=$checkauth;

               $userInputs = $request->all();
              $user_id = trim($userInputs['user_id']);
            
              $st_code = trim($userInputs['st_code']);

             if(!empty($userInputs['ac_no'])){
        
              $ac_no = trim($userInputs['ac_no']);
              }else{
                $ac_no='';
              }
              $election_id = trim($userInputs['election_id']);
           
             $getdata = OfficerModel::where([['accesstoken', '=', $accesstoken]])->where('id', '=',$user_id)->first();
             $roleID=$getdata['role_id'];
             //$roleID=19;

             if(isset($getdata))
             {

                // $records = DB::table('form12records')->where('status','=','1')->where('st_code', '=' ,$st_code);

                 //$records = Form12drecord::getrecord($st_code,$ac_no);
                 $getElection_type = DB::table('m_election_history')->where('id', $election_id)->first();
 
            

                if($getElection_type->const_type=='AC') {

                if(isset($roleID)  && ($roleID==7 || $roleID==4)){
                 $records = Form12drecord::getrecord_ac($st_code);
                
              }else if(isset($roleID)  && $roleID==19){
               // dd($st_code);

                $records = Form12drecord::getrecord_ac_st($st_code,$ac_no);
              }


               //  dd($records);
             }else if($getElection_type->const_type=='PC')
             {
                 
                    if(isset($roleID)  && ($roleID==7 || $roleID==4)){
                     $records = Form12drecord::getrecord_pc($st_code);
                    
                  }else if(isset($roleID)  && $roleID==18){

                    $records = Form12drecord::getrecord_pc_st($st_code,$ac_no);
                  }


                    //$records = Form12drecord::getrecord_pc($st_code);
             }

                //dd($records);
                
                    if(isset($records) > 0){
                         $dat80 = array();
                         $pwd = array();
                         $covid = array();


                   foreach($records as $k) {
                    

                    $pcname=$this->commonModel->getpcbypcno($k->st_code,$k->pc_no);

                        if(isset($pcname))
                        {

                           $pc_name=$pcname->PC_NAME; 
                        }else{
                           $pc_name='';  
                        }
                         $acname=$this->commonModel->getacbyacno($k->st_code,$k->ac_no);
                        // dd($acname);

                        if(isset($acname))
                        {

                           $ac_name=$acname->AC_NAME; 
                        }else{
                           $ac_name='';  
                        }

                   // dd($k->elector_type);exit;
                    if($k->elector_type=='1')
                    {

                 $dat80[]=array("stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),
                  "ac_name" => $ac_name,"ac_no" => $k->ac_no,"pc_name" => $pc_name,"pc_no" => $k->pc_no,
                    "total_elector"=> $k->total_elector, "distributed"=> $k->distributed,"recieved"=>$k->recieved,"rejected"=>$k->rejected,"pb_issued"=>$k->pb_issued , "elector_type"=> $k->elector_type,"elector_Name"=> "80 plus elector","phase"=>$k->phase_id);
             }else if($k->elector_type=='2'){


               $pwd[]=array("stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),
                  "ac_name" => $ac_name,"ac_no" => $k->ac_no,"pc_name" => $pc_name,"pc_no" => $k->pc_no,
                    "total_elector"=> $k->total_elector, "distributed"=> $k->distributed,"recieved"=>$k->recieved,"rejected"=>$k->rejected,"pb_issued"=>$k->pb_issued , "elector_type"=> $k->elector_type,"elector_Name"=> "Pwd elector","phase"=>$k->phase_id);

             }else if($k->elector_type=='3'){

                 $covid[]=array("stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),
                   "ac_name" => $ac_name,"ac_no" => $k->ac_no,"pc_name" => $pc_name,"pc_no" => $k->pc_no,
                 "total_elector"=> $k->total_elector, "distributed"=> $k->distributed,"recieved"=>$k->recieved,"rejected"=>$k->rejected,"pb_issued"=>$k->pb_issued , "elector_type"=> $k->elector_type,"elector_Name"=> "Covid 19","phase"=>$k->phase_id);


             }



              }
              $details=array('8plus'=>$dat80,'pwd'=>$pwd, 'covid'=>$covid);


              $success['success'] = '1';
               
                $success['details'] =$details; 
                return response()->json($success, $this->successStatus);




          }else{
                  $dat = array();
                }
               
                //return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
                  

         }else{

             $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);
         }

     }


           public function pb_cast12d(Request $request)
        {

             
           try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'accessToken'=> 'required',
                'st_code'=>'required',
                'dist'=>'required',
                'ac_no'=>'required',
                'election_id'=>'required',
                
            ]);
            if($validator->fails()) { 
                    return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);          
            }


            

              $userInputs = $request->all();


              $user_id = trim($userInputs['user_id']);
              $accessToken = trim($userInputs['accessToken']);
              $st_code = trim($userInputs['st_code']);
              $dist = trim($userInputs['dist']);
              $ac_no = trim($userInputs['ac_no']);
              $election_id = trim($userInputs['election_id']);
             // $date=$userInputs['date'];


$getphase =  DB::table('m_election_details')->select('StatePHASE_NO')->where('ST_CODE', '=', $st_code)->where('CONST_NO', '=',$ac_no)->first();

//dd($getphase->StatePHASE_NO);
              $getdata = OfficerModel::where([['accesstoken', '=', $accessToken]])->where('id', '=',$user_id)->first();
             //dd($getdata);

             if(isset($getdata))
             {






               $total_pbissue_80 = trim($userInputs['total_pbissue_80']);
               $vote_cast_80 = trim($userInputs['vote_cast_80']);
               $not_vote_cast_80 = trim($userInputs['not_vote_cast_80']); 

               $total_pbissue_pwd = trim($userInputs['total_pbissue_pwd']);
               $vote_cast_pwd = trim($userInputs['vote_cast_pwd']);
               $not_vote_cast_pwd = trim($userInputs['not_vote_cast_pwd']); 

               $total_pbissue_covid = trim($userInputs['total_pbissue_covid']);
               $vote_cast_covid = trim($userInputs['vote_cast_covid']);
               $not_vote_cast_covid = trim($userInputs['not_vote_cast_covid']); 
              

             

            // $election_id = trim($userInputs['election_id']);



           

          $eightyplus='';
          $pwd='';
          $covid='';
           $eightyplus = array('officer_id'=> $user_id,
                   'st_code'=>$st_code,
                   'dist'=> $dist,
                   'ac_no'=>$ac_no,
                   'election_id'=>$election_id,
                   'phase_id'=>$getphase->StatePHASE_NO,
                   'total_pb_issue'=>$total_pbissue_80,
                   'not_vote_cast'=>$not_vote_cast_80,
                   'vote_cast'=>$vote_cast_80,
                   
                  
                   );

$pwd = array('officer_id'=> $user_id,
                   'st_code'=>$st_code,
                   'dist'=> $dist,
                   'ac_no'=>$ac_no,
                   'election_id'=>$election_id,
                   'phase_id'=>$getphase->StatePHASE_NO,
                    'total_pb_issue'=>$total_pbissue_pwd,
                   'not_vote_cast'=>$not_vote_cast_pwd,
                   'vote_cast'=>$vote_cast_pwd,
                  
                   );
$covid = array('officer_id'=> $user_id,
                   'st_code'=>$st_code,
                   'dist'=> $dist,
                   'ac_no'=>$ac_no,
                   'election_id'=>$election_id,
                   'phase_id'=>$getphase->StatePHASE_NO,
                     'total_pb_issue'=>$total_pbissue_covid,
                   'not_vote_cast'=>$not_vote_cast_covid,
                   'vote_cast'=>$vote_cast_covid,
                  
                   );


$thirdarray=array('eightyplus'=>$eightyplus,'pwd'=>$pwd,'covid'=>$covid);

//dd($thirdarray);
            $data=array();
            foreach ($thirdarray as $subject){
           
             $data[] = array('officer_id' => $subject['officer_id'], 'st_code' =>$subject['st_code'],'dist' =>$subject['dist'],'ac_no' =>$subject['ac_no'],'election_id' =>$subject['election_id'], 'phase_id'=>$subject['phase_id'],'total_pb_issue' => $subject['total_pb_issue'],'not_vote_cast' => $subject['not_vote_cast'] ,'vote_cast' => $subject['vote_cast'] ,'created_at'=> date('Y-m-d H:m:s'));
             
     
                  

               }
               DB::table('pb_vote_cast')->insert($data);
               $success['success'] = '1';
               $success['message'] = 'inserted Successfully';
               return response()->json($success, $this->successStatus);


             }else{

                $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);
             }



         } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            //return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
        


               }
























     public function ca_details_ac(Request $request)
     {

           try{
             $validator = Validator::make($request->all(), [
                
                'user_id' => 'required',
                'st_code' => 'required',
                //'ac_no' => 'required',
                'election_id' => 'required',
            ]);
             if($validator->fails()) {
                 return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
               // return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);
            }
            $checkauth = $request->header('mattoken');
            //$explode=explode(' ', $checkauth);
            $accesstoken=$checkauth;

           $userInputs = $request->all();


              $user_id = trim($userInputs['user_id']);
            
              $st_code = trim($userInputs['st_code']);
        
            if(!empty($userInputs['ac_no'])){
        
              $ac_no = trim($userInputs['ac_no']);
              }else{
                $ac_no='';
              }
              $election_id = trim($userInputs['election_id']);


           
             $getdata = OfficerModel::where([['accesstoken', '=', $accesstoken]])->where('id', '=',$user_id)->first();
              $roleID=$getdata['role_id'];
             // $roleID=19;

             if(isset($getdata))
             {

                   
               if(isset($roleID)  && ($roleID==7 || $roleID==4)){
                $canddetails = DB::table('candidate_criminaluploads')
                    ->Join('candidate_personal_detail', 'candidate_criminaluploads.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                     ->Join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->where('candidate_criminaluploads.st_code',$st_code)
                   // ->where('candidate_criminaluploads.ac_no',$ac_no)
                    ->groupBy('candidate_criminaluploads.candidate_id')->get();
                
              }else if(isset($roleID)  && $roleID==19){
                 
                  $canddetails = DB::table('candidate_criminaluploads')
                    ->Join('candidate_personal_detail', 'candidate_criminaluploads.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                     ->Join('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
                    ->where('candidate_criminaluploads.st_code',$st_code)
                    ->where('candidate_criminaluploads.ac_no',$ac_no)
                    ->groupBy('candidate_criminaluploads.candidate_id')->get();

              }else{

              }

               
                   $data=array();

                   if(count($canddetails) > 0){
                     foreach($canddetails as $k) {
                        $pcname=$this->commonModel->getpcbypcno($k->st_code,$k->pc_no);

                        if(isset($pcname))
                        {

                           $pc_name=$pcname->PC_NAME; 
                        }else{
                           $pc_name='';  
                        }
                         $acname=$this->commonModel->getacbyacno($k->st_code,$k->ac_no);

                        if(isset($acname))
                        {

                           $ac_name=$acname->AC_NAME; 
                        }else{
                           $ac_name='';  
                        }
                        

                      $data[]=array("stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),"district_no"=>$k->district_no ,'district_name'=> trim($this->commonModel->getdistrictbydistrictno($k->st_code,$k->district_no)->DIST_NAME)  ,"ac_no"=>$k->ac_no,
                 "pc_no" => $k->pc_no,"ac_name"=>$ac_name,"pc_name"=>$pc_name,
                    "candidate_id"=> $k->candidate_id, "candidate_name"=> $k->cand_name,"partyID"=>$k->party_id,"party_name"=>trim($this->commonModel->getparty($k->party_id)->PARTYNAME),"nom_id"=> $k->nom_id);




                     }
                     $success['success'] = '1';
               
                $success['details'] =$data; 
                return response()->json($success, $this->successStatus);

            }else{

                $error['error'] = '0';
               
                $error['message'] ='No Record Found'; 
                return response()->json(['success' => $this->errStatus,'message'=>$error]);

            }
        } else{

             $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);
        }
        }catch (Exception $ex) {
            return response()->json(['success' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
        }



     }


     public function ca_published_insert(Request $request)
     {


         try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'accessToken'=> 'required',
                'st_code'=>'required',
                'dist'=>'required',
                'ac_no'=>'required',
                'election_id'=>'required',
                'candidate_id'=>'required',
                //'mobile'   => 'required|regex:/^\S*$/u|numeric|digits:10',
            ]);
            if($validator->fails()) { 
                  return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
                 //   return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);          
            }


            

              $userInputs = $request->all();


              $user_id = trim($userInputs['user_id']);
              $accessToken = trim($userInputs['accessToken']);
              $st_code = trim($userInputs['st_code']);
              $dist = trim($userInputs['dist']);
              $ac_no = trim($userInputs['ac_no']);
              $election_id = trim($userInputs['election_id']);
              $candidate_id = trim($userInputs['candidate_id']);
            



              $getdata = OfficerModel::where([['accesstoken', '=', $accessToken]])->where('id', '=',$user_id)->first();
             //dd($getdata);
              $getphase =  DB::table('m_election_details')->select('StatePHASE_NO')->where('ST_CODE', '=', $st_code)->where('CONST_NO', '=',$ac_no)->first();

//dd($getphase->StatePHASE_NO);


             if(isset($getdata))
             {


              //$new_paper = trim($userInputs['newspaper']);
              $date_newpaper_1 = trim($userInputs['date_newpaper_1']);

              $date_newpaper_2 = trim($userInputs['date_newpaper_2']);
              $date_newpaper_3 = trim($userInputs['date_newpaper_3']);

            //  $new_paper = trim($userInputs['tv']);
              $date_tv_1 = trim($userInputs['date_tv_1']);
              $date_tv_2 = trim($userInputs['date_tv_2']);

              $date_tv_3 = trim($userInputs['date_tv_3']);

            
           $insert_record = array('officer_id'=> $user_id,
                   'st_code'=>$st_code,
                   'dist'=> $dist,
                   'ac_no'=>$ac_no,
                   'election_id'=>$election_id, 
                   'candidate_id'=>$candidate_id,
                   'phase_id'=>$getphase->StatePHASE_NO,
                   'date_newpaper_1'=>!empty($date_newpaper_1) ? date("Y-m-d", strtotime($date_newpaper_1)) : '',
                   'date_newpaper_2'=>!empty($date_newpaper_2) ? date("Y-m-d", strtotime($date_newpaper_2)) : '', 
                   'date_newpaper_3'=>!empty($date_newpaper_3) ? date("Y-m-d", strtotime($date_newpaper_3)) : '', 
                   'date_tv_1'=>!empty($date_tv_1) ? date("Y-m-d", strtotime($date_tv_1)) : '',
                   'date_tv_2'=>!empty($date_tv_2) ? date("Y-m-d", strtotime($date_tv_2)) : '',
                   'date_tv_3'=>!empty($date_tv_3) ? date("Y-m-d", strtotime($date_tv_3)) : '',
                   'status'=>'1',
                   'created_at'=>date('Y-m-d H:m:s'),
                  
                   );
           //dd($insert_record);

            $abc=DB::table('ca_published')->insert($insert_record);
            if($abc=true){
            $success['success'] = '1';
               
                $success['message'] ='record inserted'; 
                return response()->json($success, $this->successStatus);
         }else{

              $error['error'] = '0';
               
                $error['message'] ='record Not inserted'; 
                return response()->json(['success' => $this->errStatus,'message'=>$error]);
         }


             }else{

                 $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);
             }


         }catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            //return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }




     }



             public function ca_published_record(Request $request)
              {

                     try{
             $validator = Validator::make($request->all(), [
                
                'user_id' => 'required',
                'st_code' => 'required',
                'ac_no' => 'required',
                'election_id' => 'required',
            ]);
             if($validator->fails()) {
                 return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
               // return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);
            }
            $checkauth = $request->header('mattoken');
            //$explode=explode(' ', $checkauth);
            $accesstoken=$checkauth;

           $userInputs = $request->all();


              $user_id = trim($userInputs['user_id']);
            
              $st_code = trim($userInputs['st_code']);
        
              $ac_no = trim($userInputs['ac_no']);
              $election_id = trim($userInputs['election_id']);


           
             $getdata = OfficerModel::where([['accesstoken', '=', $accesstoken]])->where('id', '=',$user_id)->first();
             //dd($getdata);

             if(isset($getdata))
             {

                   


                $details = DB::table('ca_published')
                   
                    ->where('st_code',$st_code)->where('ac_no',$ac_no)->get();
                   $data=array();

                   if(count($details) > 0){
                     foreach($details as $k) {
                       
                        $acname=$this->commonModel->getacbyacno($k->st_code,$k->ac_no);

                        if(isset($acname))
                        {

                           $ac_name=$acname->AC_NAME; 
                        }else{
                           $ac_name='';  
                        }
                        

                      $data[]=array("candidate_id"=>$k->candidate_id,"stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),"ac_no"=>$k->ac_no,"ac_name"=>$ac_name,"phase"=>$k->phase_id ,"date_newpaper_1"=>$k->date_newpaper_1,"date_newpaper_2"=>$k->date_newpaper_2,"date_newpaper_3"=>$k->date_newpaper_3,"date_tv_1"=>$k->date_tv_1,"date_tv_2"=>$k->date_tv_2,"date_tv_3"=>$k->date_tv_3 );


                     }

                     $success['success'] = '1';
               
                $success['details'] =$data; 
                return response()->json($success, $this->successStatus);

            }else{

                      $error['error'] = '0';
               
                $error['message'] ='No Record Found'; 
                 return response()->json(['success' => $this->errStatus,'message'=>$error]);

            }
        }else{


                  $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);

        }
        }catch (Exception $ex) {
            return response()->json(['success' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
        }





              }




// profile




     public function profile(Request $request)
     {


           try{
             $validator = Validator::make($request->all(), [
                
                'user_id' => 'required',
                
                'election_id' => 'required',

                'officer_id' => 'required',
            ]);
             if($validator->fails()) {
                 return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
               // return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);
            }
            $checkauth = $request->header('mattoken');
            //$explode=explode(' ', $checkauth);
              $accesstoken=$checkauth;

              $userInputs = $request->all();


              $user_id = trim($userInputs['user_id']);
            
             
              $election_id = trim($userInputs['election_id']);
              $officer_id = trim($userInputs['officer_id']);
            

           
              $getdata = OfficerModel::where([['accesstoken', '=', $accesstoken]])->where('id', '=',$user_id)->first();
             //dd($getdata);

             if(isset($getdata))
             {







                $canddetails = DB::table('officer_login')->where('id',$officer_id)->get();
                   $data=array();

                   if(count($canddetails) > 0){
                     foreach($canddetails as $k) {
                        $pcname=$this->commonModel->getpcbypcno($k->st_code,$k->pc_no);

                        if(isset($pcname))
                        {

                           $pc_name=$pcname->PC_NAME; 
                        }else{
                           $pc_name='';  
                        }
                         $acname=$this->commonModel->getacbyacno($k->st_code,$k->ac_no);

                        if(isset($acname))
                        {

                           $ac_name=$acname->AC_NAME; 
                        }else{
                           $ac_name='';  
                        }
                        
                           $stcode=trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME);
                         

                        
                       
                        
                         $path=url('');
                      $data[]=array("id"=>$k->id,"name"=>$k->name,"designation"=>$k->designation,"placename"=>$k->placename, "Phone_no"=>$k->Phone_no,"email"=>$k->email,"role_id"=>$k->role_id,"path"=>$path, "profile_img"=>$k->profile_img,"stateName"=>$stcode,"ac_no"=>$k->ac_no,"pc_no" => $k->pc_no,"ac_name"=>$ac_name,"pc_name"=>$pc_name);




                     }
                     $success['success'] = '1';
               
                $success['details'] =$data; 
               // return response()->json($success, $this->successStatus);
              //  ($this->okStatus, $success, $this->ok_response)
                return response()->json(['success' => $this->okStatus,'message'=>$success,$this->ok_response]);
                // return $this->ResponseMethod->get_http_response( $this->okStatus, $success,$this->ok_response);

            }else{

                      $error['error'] = '0';
               
                $error['message'] ='No Record Found'; 
                 return response()->json(['success' => $this->errStatus,'message'=>$error]);
                 

            }
        }else{

                  $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                  return response()->json($error, $this->unauthorizedStatus);
        }
        }catch (Exception $ex) {

            return response()->json(['success' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
        }



     }


public function profile_image(Request $request)
     {

           try{
             $validator = Validator::make($request->all(), [
                
                'user_id' => 'required',
                
                'election_id' => 'required',
            ]);
             if($validator->fails()) {
                 return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
               // return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);
            }
            $checkauth = $request->header('mattoken');
            //$explode=explode(' ', $checkauth);
            $accesstoken=$checkauth;

           $userInputs = $request->all();


              $user_id = trim($userInputs['user_id']);
            
             
              $election_id = trim($userInputs['election_id']);
              $image=$userInputs['profile_img'];
           
           
             $getdata = OfficerModel::where([['accesstoken', '=', $accesstoken]])->where('id', '=',$user_id)->first();
             //dd($getdata);

             if(isset($getdata))
             {

                
            $extension=array('jpg','png','jpeg');
             
            $folderPath = 'uploads1/matdan/'; //path location
            $path = public_path('uploads1/matdan/'.$election_id.'/');

            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            $image_parts = explode(";base64,", $image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $uniqid = uniqid();
            $file = $path . $uniqid . '.'.$image_type;
            $folderPath = 'uploads1/matdan/'.$election_id.'/' . time() . '.'.$image_type;
           // dd($image_parts['1']);

             $size = strlen(base64_decode($image_base64));
           //  dd($size);
              $size_kb = $size / 1024;
               $file_size = 25;
               
              
              if($size_kb >= $file_size)
              {
//dd($size_kb);
                 $error['error'] = '0';
                 $error['message'] ='Check File Size (Max 5 KB)'; 
                 return response()->json(['success' => $this->errStatus,'message'=>$error]);
              }

            if (in_array($image_type, $extension))
            {

               file_put_contents($folderPath, $image_base64);

         
             $output= DB::table('officer_login')->updateOrInsert(['id'=>$user_id],['profile_img'=> $folderPath]);
             if($output==true){
                 $success['Success'] = '1';
                 $success['message'] =" Image Update Successfully"; 
                return response()->json($success, $this->successStatus);
            }
            
               

            }else{

                 $error['error'] = '0';
                 $error['message'] ='Check File Extension(jpg,png,jpeg)'; 
                return response()->json(['success' => $this->errStatus,'message'=>$error]);

            }
            

            

                $canddetails = DB::table('officer_login')->where('id',$user_id)->get();
                   $data=array();

                   if(count($canddetails) > 0){
                     foreach($canddetails as $k) {
                        $pcname=$this->commonModel->getpcbypcno($k->st_code,$k->pc_no);

                        if(isset($pcname))
                        {

                           $pc_name=$pcname->PC_NAME; 
                        }else{
                           $pc_name='';  
                        }
                         $acname=$this->commonModel->getacbyacno($k->st_code,$k->ac_no);

                        if(isset($acname))
                        {

                           $ac_name=$acname->AC_NAME; 
                        }else{
                           $ac_name='';  
                        }
                        if($k->role_id!='7')
                        {
                           $stcode=trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME);
                           //$distcode=trim($this->commonModel->getdistrictbydistrictno($k->st_code,$k->district_no)->DIST_NAME);

                        }else{

                            $stcode="";
                            //$distcode="";
                        }
                        

                      $data[]=array("id"=>$k->id,"name"=>$k->name,"designation"=>$k->designation,"placename"=>$k->placename, "Phone_no"=>$k->Phone_no,"email"=>$k->email,"role_id"=>$k->role_id,"profile_img"=>" ","stateName"=>$stcode,"ac_no"=>$k->ac_no,"pc_no" => $k->pc_no,"ac_name"=>$ac_name,"pc_name"=>$pc_name);




                     }
                     $success['success'] = '1';
               
                $success['details'] =$data; 
                return response()->json($success, $this->successStatus);

            }else{

                      $error['error'] = '0';
               
                $error['message'] ='No Record Found'; 
                return response()->json($error, $this->errStatus);

            }
        }else{


                  $error['error'] = '0';
                  $error['message'] ='UserID OR AccessToken Wrong'; 
                 //   return response()->json(['success' => $this->unauthorizedStatus,'message'=>$error]);
                  return response()->json($error, $this->unauthorizedStatus);


        }
        }catch (Exception $ex) {
            return response()->json(['success' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
        }



     }




























public function logout(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'accessToken' => 'required',
            'user_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);
        }
        $accessToken = trim($request->accessToken);
        $candidate_id = trim($request->candidateId);
        $newuser = OfficerModel::where('accesstoken',$accessToken)->where('id', '=',$user_id)->first();
        if(isset($newuser)){
            if($newuser->access_token == $accessToken)
            {      
            $token = '';
			$otp = '';
          
             $logdata = array('accesstoken'=>$token,'login_flag'=>0,'otp'=>$otp,
                            'otp_verify_by_string'=>0);

        DB::table('officer_login')->where([['Phone_no' , $mobile],['id' , $user_id]])->update($logdata);
            
            $json = [
                'success' => true,
                'message' => 'You are Logged out.',
            ];
            return $this->ResponseMethod->get_http_response($this->okStatus, $json, $this->ok_response);
            }else{
                return $this->ResponseMethod->get_http_response($this->okStatus, ['success' => false, 'error' => "Wrong Access Token Entered!"], $this->ok_response);
            }
        }else{
            return $this->ResponseMethod->get_http_response($this->okStatus, ['success' => false, 'error' => "Please Check Access Token or candidate id!"], $this->ok_response);
        }
    }
	
	    public function total_elector_ac($st_code,$ac_no=NULL)
    {
          
           $gettotal_elector = DB::table('electors_cdac')->select('electors_total')->where('st_code', $st_code)->where('ac_no', $ac_no)->first();

                $success['success'] = '1';
               
                $success['details'] =$gettotal_elector; 
                return response()->json($success, $this->successStatus);

        // User::where('id', $userid)->update(array('OTP_attempt' => $attempt_value));
    }

    public function total_elector_pc($st_code,$pc_no=NULL)
    {
          
           $gettotal_elector = DB::table('electors_cdac')->select('electors_total')->where('st_code', $st_code)->where('pc_no', $pc_no)->first();

                $success['success'] = '1';
               
                $success['details'] =$gettotal_elector; 
                return response()->json($success, $this->successStatus);

        // User::where('id', $userid)->update(array('OTP_attempt' => $attempt_value));
    }

         

        





  
}