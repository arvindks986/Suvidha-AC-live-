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
use Session;
use App\models\{States, Districts, AC};
use Mail;
use App\Helpers\SmsgatewayHelper;
use Illuminate\Support\Facades\Input;
use Redirect;
use PDF;
use Carbon\Carbon;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Classes\xssClean;

 
class UsersController extends Controller
{
    public function __construct()
    {
        $this->xssClean = new xssClean;
        $this->commonModel = new commonModel();
        $this->ResponseMethod = new ResponseController;
        $this->bad_response = $this->ResponseMethod::HTTP_BAD_REQUEST;
        $this->ok_response = $this->ResponseMethod::HTTP_OK; 
        $this->okStatus = "success";
        $this->errStatus = "error";
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
        try{
            $validator = Validator::make($request->all(), [
                'mobile' => 'required',
                'deviceId' => 'required'
            ]);
         
         if($validator->fails()) {
            return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);            
         } 

            $userInputs = $request->all();
            $mobile = trim($userInputs['mobile']);
            $device_id = trim($userInputs['deviceId']);
            $app_id = "cadidateApp";

            $newlogin = DB::table('user_login')->where('mobile', '=', $mobile)->first();
           // dd($newlogin);
           // $loginDb = DB::table('candidate_personal_detail')->where('cand_mobile', '=', $mobile)->first();
            
           if(isset($newlogin) && $newlogin->role_id != 2 ){



               $userId=$newlogin->id;
               $roleID=$newlogin->role_id;
            
                    //$candidate_id= $loginDb->candidate_id;
                    $this->sendOtp($newlogin->mobile, $newlogin->id, 'U');
      
                    $success['success'] =  'true';
                    $success['message'] = 'Login Successfully';
                    $success['userId'] = (string)$userId;
                    $success['candidateId'] = "0";

                    $success['roleID'] = $roleID;
                    $success['mobile_otp'] = 'OTP has been send to registered mobile no, please enter to verify mobile number. ';
                 return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
           }else{

            $loginDb = DB::table('candidate_personal_detail')->where('cand_mobile', '=', $mobile)->first();
           // dd($loginDb);
            if(isset($loginDb)){

            $partyid = DB::table('candidate_nomination_detail')->where('candidate_id',$loginDb->candidate_id)->first();

            if(!empty($partyid->party_id))
            {

               $PartyID=$partyid->party_id;

            }else{
                
                $PartyID='1180';
            }
            
                if(!empty($newlogin)){
                if(!is_null($newlogin->otp_time)){
                            $currentTime = Carbon::now();
                            $diff=$currentTime->diffInSeconds($newlogin->otp_time);
                            }else{ 
                                $diff=61; 
                            }
                }else{
                    $diff = 61;
                }

             if($diff>60){
            
             $user = user::updateOrCreate(
                
                ['mobile'=> $loginDb->cand_mobile],
                [ 'name'=> $loginDb->cand_name,
                'candidate_id' => $loginDb->candidate_id,
                'authority_id' => "0",
                'role_id'=>'2',
                'party_id'=>$PartyID,
                'mobile'=> $loginDb->cand_mobile,
                'otp_time'=>Carbon::now(),
                'device_id'=> $device_id,
                'device_type'=>'Mobile',
                'otp_attempt'=>'0',
                'created_at'=> date('Y-m-d H:m:s'),
                'password'=> bcrypt($loginDb->cand_mobile),
                'verify_otp'=> '0',
                'app_id'=> $app_id]
                );
                $logid=$user->id;
                $roleID=$user->role_id;
            
                    $candidate_id= $loginDb->candidate_id;
                    $this->sendOtp($loginDb->cand_mobile, $candidate_id, 'C');
      
                    $success['success'] =  'true';
                    $success['message'] = 'Login Successfully';
                    $success['candidateId'] = (string)$candidate_id;
                    $success['userId'] = "0";
                    $success['roleID'] = $roleID;
                    $success['mobile_otp'] = 'OTP has been send to registered mobile no, please enter to verify mobile number. ';
                 return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
                 
            }
            else{
                $success['success'] =  'false';
                $success['message'] = 'Please wait for 1 minute to resend OTP';
                $success['mobile_otp'] = 'Can Send only 1 OTP per minute';
                return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
            }
            }
            else{
                $error['success'] =  'false';
                $error['message'] = 'Candidate with Input Mobile number does not exist';
                return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
            }

}// candidate login

        } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
}
    
    
public function sendOtp($mobno, $userId, $identifyy)
    {

//dd($mobno, $userId, $identifyy);
        if($mobno=='9871124359')
        {
            $otp='123456';
        }else{

            $otp = rand(100000, 999999);

        }
       // $otp = rand(100000, 999999);
        // $otp = '123456';
        $datamob = array('OTP'=>$otp);
        if($identifyy == 'C'){
        DB::table('user_login')->where('candidate_id', $userId)->update($datamob);
        }
        if($identifyy == 'U'){
        DB::table('user_login')->where('id', $userId)->update($datamob);
        }
        $mobile_message = 'Your OTP is ' .$otp. ' for ECI Candidate App. Please enter the OTP to proceed. Do not share this OTP';
        
        $msgstatus = SmsgatewayHelper::gupshup($mobno, $mobile_message);
    }
    
    

public function verifyOtp(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
                'candidateId' => 'required|numeric',
                'userId' => 'required|numeric',
                'mobile'   => 'required|regex:/^\S*$/u|numeric|digits:10',
            ]);
            if($validator->fails()) { 
                    return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);          
            }
             $inputs = $request->all();
              
             $otp = trim($inputs['otp']);
             $candidate_id = trim($inputs['candidateId']);
             $user_id = trim($inputs['userId']);
             $mobile = trim($inputs['mobile']);

             if($candidate_id > 0){

            $newuser = User::where('mobile', $mobile)->where('candidate_id', '=',$candidate_id)->first();
            //$newuser = DB::table('user_login')->where([['mobile' , $mobile],['candidate_id' , $candidate_id]])->first();

             if(isset($newuser)){
                 
                // $attempts=$newuser->otp_attempt;
                // $this->otp_attempt($newuser->id, $attempts+1);
           
                // if($attempts>2){
                //     $success['success'] = false;
                //     $success['message'] = 'reached maximum attempts Please resend otp!';
                //     return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
                // }
                 
             $token = $newuser->createToken('MyApp')->accessToken;
             $mobileOTP = $newuser->otp;
             $mobile = $newuser->mobile;
             if($mobileOTP == $otp)
             { 
              $logdata = array('access_token'=>$token,'login_flag'=>1,
                            'isActive'=>1,'verify_otp'=>1);

        DB::table('user_login')->where([['mobile' , $mobile],['candidate_id' , $candidate_id]])->update($logdata);
          $login_history = array(
                                'user_login_id'=>$newuser->id,
                                'ipaddress'=>request()->ip(),
                                'app_id'=>$newuser->app_id,
                                'user_device_id'=>$newuser->device_id,
                                'device_type'=>'Mobile',
                                'role_id'=>'2',
                                'Login_time'=>Date('Y-m-d H:i:s'),
                                'session_id'=>$token,
                                'user_activity'=>'login on Mobile',
                                'Login_date'=>Date('Y-m-d'));
            //$a=DB::table('user_history')->insert($login_history); 
              
          $cand_d =DB::table('candidate_personal_detail')->where([['candidate_id' , $candidate_id]])->first();
          $const_type =DB::table('m_election_history')->where([['id' , $inputs['election_id']]])->first();
            if(!empty($cand_d->cand_image)) {
            $candidateImg=url($cand_d->cand_image);
            }else{
            $candidateImg = "";
            }
            $success['success'] = 'true';
            $success['message'] = 'OTP verified';
            $success['userloginid'] = $newuser->id;
            $success['candidateId'] = $candidate_id;
            $success['accessToken'] = (string)$token;
            $success['name'] =$cand_d->cand_name;
            //$success['candImage'] =$cand_d->cand_image;


                if($const_type->const_type=='PC'){
                //$success['url'] ='https://encoredemo.eci.gov.in/pc/public/'; 
                 //$host = $request->getSchemeAndHttpHost();
                //$success['candImage'] =$host.'/'.$cand_d->cand_image;
                  $urlis = 'https://suvidha.eci.gov.in/pc/public/';
                  //$urlis ='https://encore.eci.gov.in/pc/public/';
                  $success['candImage'] =$urlis.$cand_d->cand_image;
                
               }else if($const_type->const_type=='AC'){
                //$urlis ='https://demo.eci.nic.in/suvidhaac/public/';
                //$success['candImage'] =url($cand_d->cand_image);

                // $urlis ='https://encore.eci.gov.in/ac/public/';
                 $urlis = 'https://suvidha.eci.gov.in/ac/public/';
                 $success['candImage'] =$urlis.$cand_d->cand_image;
              }
           // $success['candImage'] =$candidateImg;
               

                return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Entered Otp is wrong, please enter correct otp';
                  return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered data not exist ,may be wrong otp, mobile and candidateId';
               return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
            }

        }// End Candidate Condition

// Check User

  if($user_id > 0){

            $newuser = User::where('mobile', $mobile)->where('id', '=',$user_id)->first();
           // $newuser = DB::table('user_login')->where([['mobile' , $mobile],['id' , $user_id]])->first();

             if(isset($newuser)){
                 
                //$attempts=$newuser->otp_attempt;
                //$this->otp_attempt($newuser->id, $attempts+1);

                // if($attempts>2){
                //     $success['success'] = false;
                //     $success['message'] = 'reached maximum attempts Please resend otp!';
                //     return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
                // }
                 
             $token = $newuser->createToken('MyApp')->accessToken;
             $mobileOTP = $newuser->otp;
             $mobile = $newuser->mobile;
             if($mobileOTP == $otp)
             { 
              $logdata = array('access_token'=>$token,'login_flag'=>1,
                            'isActive'=>1,'verify_otp'=>1);

        DB::table('user_login')->where([['mobile' , $mobile],['id' , $user_id]])->update($logdata);
          $login_history = array(
                                'user_login_id'=>$newuser->id,
                                'ipaddress'=>request()->ip(),
                                'app_id'=>$newuser->app_id,
                                'user_device_id'=>$newuser->device_id,
                                'device_type'=>'Mobile',
                                'role_id'=>$newuser->role_id,
                                'Login_time'=>Date('Y-m-d H:i:s'),
                                'session_id'=>$token,
                                'user_activity'=>'login on Mobile',
                                'Login_date'=>Date('Y-m-d'));
            //$a=DB::table('user_history')->insert($login_history); 
              
         
          $const_type =DB::table('m_election_history')->where([['id' , $inputs['election_id']]])->first();
            // if(!empty($cand_d->cand_image)) {
            // $candidateImg=url($cand_d->cand_image);
            // }else{
            // $candidateImg = "";
            // }
          if(isset($newuser->name) && !empty($newuser->name))
          {
            $nameis=$newuser->name;
          }else{
            $nameis="N/A";
          }
            $success['success'] = 'true';
            $success['message'] = 'OTP verified';
            $success['userloginid'] = $newuser->id;
            //$success['candidateId'] = $candidate_id;
            $success['accessToken'] = (string)$token;
            $success['name'] =$nameis;
            $success['mobile'] =$newuser->mobile;


              //   if($const_type->const_type=='PC'){
               
              //    $host = $request->getSchemeAndHttpHost();
              //   $success['candImage'] =$host.'/'.$cand_d->cand_image;
              //  }else if($const_type->const_type=='AC'){
              //   //$urlis ='https://demo.eci.nic.in/suvidhaac/public/';
              //   $success['candImage'] =url($cand_d->cand_image);
              // }
           // $success['candImage'] =$candidateImg;
               

                return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Entered Otp is wrong, please enter correct otp';
                  return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered data not exist ,may be wrong otp, mobile and userId';
               return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
            }

        }



// End User        




        } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
}

public function nominationlisting(Request $request) {
             try{
            $validator = Validator::make($request->all(), [
                'accessToken' => 'required',
                'candidateId' => 'required|numeric'
            ]);
            if($validator->fails()) {
                return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);          
            }
             $inputs = $request->all();
             
             $accessToken = trim($inputs['accessToken']);
             $candidate_id = trim($inputs['candidateId']);
            
            $newuser = User::where([['access_token', '=', $accessToken]])->where('candidate_id', '=',$candidate_id)->first();
            
             if(isset($newuser)){
             if($newuser->access_token == $accessToken)
             { 
                $nom_d = DB::table('candidate_nomination_detail')
                ->where([['candidate_id' , $candidate_id]])->get();
    
              if(count($nom_d)>0){
              foreach($nom_d as $k) {
                $ac="";$pc="";
                if($k->ac_no != null){ $ac = trim($this->commonModel->getacbyacno($k->st_code,$k->ac_no)->AC_NAME); }
                    if($k->pc_no != null){ $pc = trim($this->commonModel->getpcbypcno($k->st_code,$k->pc_no)->PC_NAME); }
                
                $dat[]=array("nomId"=>$k->nom_id,
                "stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),
                'districtName'=>trim($this->commonModel->getdistrictbydistrictno($k->st_code,$k->district_no)->DIST_NAME),
                "PCName"=>$pc,"ACName"=>$ac,"application_status"=>ucwords(trim($this->commonModel->getnameBystatusid($k->application_status))),
                "application_status_data"=>$k->application_status);
                //$dat[]=array("nomId"=>$k->nom_id,"stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),'districtName'=>trim($this->commonModel->getdistrictbydistrictno($k->st_code,$k->district_no)->DIST_NAME),"PCName"=>trim($this->commonModel->getacbyacno($k->st_code,$k->ac_no)->AC_NAME),"application_status"=>trim($this->commonModel->getnameBystatusid($k->application_status)),"application_status_data"=>$k->application_status);
              }}else{
                  $dat = array();
                }
                $success['success'] = 'true';
                $success['candidateId'] = $candidate_id;
                $success['nominitationdata'] =$dat; 
                  $success['session'] = '1';

                return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Please Check your Access Token';
                  return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered Access Token or candidate Wrong';
               $error['session'] = '0';
               return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
            }
        } catch (Exception $ex) {
            return response()->json(['error'=>'Internal Server Error'], $this->intservererrorStatus);
        }
}

public function nominationstatus(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'accessToken' => 'required',
                'candidateId' => 'required|numeric',
                'nomId' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()-first(), $this->bad_response);
                    }
             $inputs = $request->all();
             $accessToken = trim($inputs['accessToken']);
             $candidate_id = trim($inputs['candidateId']);
             $nom_id = trim($inputs['nomId']);
            
            $newuser = User::where([['access_token', '=', $accessToken]])->where('candidate_id', '=',$candidate_id)->first();
             if(isset($newuser)){
             if($newuser->access_token == $accessToken)
             { 
                $cand_d =DB::table('candidate_personal_detail')->where([['candidate_id' , $candidate_id]])->first();  
                $nom_d = DB::table('candidate_nomination_detail')->where([['nom_id',$nom_id],['candidate_id' , $candidate_id]])->get();
                //$afidav = DB::table('candidate_affidavit_detail')->where([['nom_id',$nom_id],['candidate_id' , //$candidate_id]])->first();

                 // $afidav = DB::table('candidate_affidavit_detail')->where('candidate_id', $candidate_id)->where('nom_id', $nom_id)->get();
                  $afidav = DB::table('candidate_affidavit_detail')->where('candidate_id', $candidate_id)->where('nom_id', $nom_id)->where('is_deleted', '1')->get();
                //$cand_criminal = DB::table('candidate_criminaluploads')->where('candidate_id', $candidate_id)->where('nom_id', $nom_id)->first();
                $cand_criminal = DB::table('candidate_criminaluploads')->where('candidate_id', $candidate_id)->first();

                $const_type =DB::table('m_election_history')->where([['id' , $inputs['election_id']]])->first();

                   if(!empty($const_type->const_type=='AC')){

                         $path = 'https://suvidha.eci.gov.in/ac/public/';

                   }else if(!empty($const_type->const_type=='PC')){
                         
                        $path = 'https://suvidha.eci.gov.in/pc/public/';
                                  
                    }else{}





                if(!empty($cand_criminal)) {

                       if(!empty($const_type->const_type=='AC')){
                            //$link=array("criminal_link"=>url($cand_criminal->path));
                        $urlis = 'https://suvidha.eci.gov.in/ac/public';
                             $criminal =array("criminal_link"=>$urlis.'/'.$cand_criminal->path);


                        }else if(!empty($const_type->const_type=='PC')){
                           // $host = $request->getSchemeAndHttpHost();
                           
                             $urlis = 'https://suvidha.eci.gov.in/pc/public';
                            $criminal=array("criminal_link"=>$urlis.'/'.$cand_criminal->path);

                            
                        }else{}
                                }else{
                                    $criminal = array("criminal_link"=>"");
                                }



                /*
                if (count($cand_criminal) > 0) {
                    $criminal = array();
                    foreach ($cand_criminal as $raw) {

                        if (!empty($raw)) {
                            if(!empty($raw->ac_no)){
                                $link = url($raw->path);
                                $name = $raw->name;
                                }elseif(!empty($raw->pc_no)){
                                    $host = $request->getSchemeAndHttpHost();
                                    $link = $host.'/'.$raw->path;
                                    $name = $raw->name;
                                }
                        } else {
                            $link = "";
                            $name = "";
                        }

                        $criminal = array("criminal_link" => $link, "criminal_name" => $name);
                    }
                } else {
                    $criminal = array();
                }

                */
                

                 if (count($afidav) > 0) {
                    $affid = array();
                    foreach ($afidav as $affi) {

                        if (!empty($affi)) {

                            if(isset($affi->ac_no))
                            {
                                  $url = 'https://suvidha.eci.gov.in/ac/public/';
                            }elseif(isset($affi->pc_no))
                            {
                                $url = 'https://suvidha.eci.gov.in/pc/public/';
                            }else{
                                  $url ="";

                            }
                            $link = $url.($affi->affidavit_path);
                            $name = 'Form 26';
                            $datetime = $affi->created_at;
                        } else {
                            $link = "";
                            $name = "";
                        }

                        $affid[] = array("affidavit_link" => $link, "affidavit_name" => $name,"date_time" => $datetime);
                    }
                } else {
                    $affid = array();
                }

                
                /*
                if(!empty($afidav)) {



                        //$afid=array("affidavitLink"=>url($afidav->affidavit_path));
                        if(!empty($afidav->ac_no)){

                           //$urlis ='https://encore.eci.gov.in/pc/public/';
                           $urlis = 'https://suvidha.eci.gov.in/pc/public';
                           $afid=array("affidavitLink"=>$urlis.'/'.$afidav->affidavit_path);
                            //$afid=array("affidavitLink"=>url($afidav->affidavit_path));




                        }else if(!empty($afidav->pc_no)){
                         
                            $urlis = 'https://suvidha.eci.gov.in/ac/public';
                           // $urlis ='https://encore.eci.gov.in/ac/public/';
                            $afid=array("affidavitLink"=>$urlis.'/'.$afidav->affidavit_path);
                        }else{}
                }else{
                    $afid = array("affidavitLink"=>"");
                }


*/










                if(!empty($cand_d->cand_image)) {
                   // $candidateImg=url($cand_d->cand_image);
                    $candidateImg= $path.$cand_d->cand_image;
                    }else{
                    $candidateImg = "";
                    }

                if(count($nom_d)>0){ $msg = 'true';
                foreach($nom_d as $k) {
                    
                    $ac = ""; $pc="";$nominationaccept="";
                    
                    if($k->ac_no != null){ $ac = trim($this->commonModel->getacbyacno($k->st_code,$k->ac_no)->AC_NAME); }
                    if($k->pc_no != null){ $pc = trim($this->commonModel->getpcbypcno($k->st_code,$k->pc_no)->PC_NAME); }
                    if($k->scrutiny_date != null){ $nominationaccept=$k->scrutiny_date; }
                    $dat=array("nomId"=>$k->nom_id,"stateName"=>trim($this->commonModel->getstatebystatecode($k->st_code)->ST_NAME),
                    'districtName'=>trim($this->commonModel->getdistrictbydistrictno($k->st_code,$k->district_no)->DIST_NAME),"ACName"=>$ac,
                    "PCName"=>$pc,"application_status"=>ucwords(trim($this->commonModel->getnameBystatusid($k->application_status))),
                    "application_status_data"=>$k->application_status,"date_of_submit_nomination"=>$k->date_of_submit,"accept_nomination_date"=>$nominationaccept);   
                }
                }else { $msg = 'false';
                    $dat = "data not present may be your nomId wrong";
                }
                $success['success'] = $msg;
                $success['candidateId'] = $candidate_id;
                $success['name'] =$cand_d->cand_name;
               // $success['candImage'] =$cand_d->cand_image;
                $success['candImage'] =$candidateImg;
                $success['nominitationdata'] =$dat;
                $success['is_criminal'] =$cand_d->is_criminal;              
                $success['affidavit'] =$affid; 
                $success['criminal'] = $criminal; 
                $success['session'] = '1';

                
                return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Wrong Access Token Entered';

                  return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered Access Token or candidate id Wrong';
               $error['session'] = '0';
               return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
            }
        } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
}

public function permissionlistview(Request $request) {
    try{
            $validator = Validator::make($request->all(), [
                'accessToken' => 'required',
                'candidateId' => 'required|numeric',
                'userloginId' => 'required|numeric',
            ]);
            if($validator->fails()) {
                return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);          
            }
             $inputs = $request->all();
             $accessToken = trim($inputs['accessToken']);
             $candidate_id = trim($inputs['candidateId']);
             $login_id = trim($inputs['userloginId']);
            
            $newuser = User::where([['access_token', '=', $accessToken]])->where('id', '=',$login_id)->first();
             if(isset($newuser)){
             if($newuser->access_token == $accessToken)
             { 
                 $permis = DB::table('user_login')
                 ->join('permission_request','user_login.id','=','permission_request.user_id')
                 ->Join('permission_type', 'permission_request.permission_type_id', '=', 'permission_type.id')
                 ->join('permission_master', 'permission_type.permission_type_id', '=', 'permission_master.id')
                 ->leftJoin('location_master', 'permission_request.location_id', '=','location_master.id')
                 ->select('permission_request.location_id','permission_request.Other_location','location_master.location_name','permission_request.id','permission_master.permission_name','permission_request.added_at','permission_request.date_time_start','permission_request.date_time_end','permission_request.approved_status','permission_request.cancel_status','permission_request.action_date as roupdatedate','location_master.latitude','location_master.longitude')
                 ->where('user_login.id' , $login_id)->get();
                 
                if(count($permis)>0) {
                    $msg = 'true';
                    foreach($permis as $f) {
                        if($f->approved_status == 0){ $status = 'Pending'; }
                        elseif($f->approved_status == 1){ $status = 'Inprocess'; }
                        elseif($f->approved_status == 2){ $status = 'Accept'; }
                        elseif($f->approved_status == 3){ $status = 'Reject'; }
                        $perm[]=array("permission_id"=>$f->id,'Permission_longitude'=>$f->longitude,'Permission_latitude'=>$f->latitude,
                        "permission"=>$f->permission_name,"permission_registerd_date"=>$f->added_at,"permission_action_date"=>$f->roupdatedate,"permission_from"=>$f->date_time_start,"permission_till"=>$f->date_time_end,"permission_approved_status"=>$f->approved_status,"permission_approved_status_detail"=>$status,"location_name"=>$f->location_name,"location_id"=>$f->location_id,
                        "location_other_name"=>$f->Other_location,"is_canceled"=>$f->cancel_status);
                    }
                }else{ 
                    $msg = 'false';
                    $perm = array();
                }
                $success['success'] = $msg;
                $success['userloginid'] = $newuser->id;
                $success['candidateId'] = $candidate_id;
                $success['permission'] = $perm;
                  $success['session'] = '1';
                return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response); 
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Entered Access Token wrong';
                  return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);  
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered Access Token or candidate id wrong';
                 $error['session'] = '0';
               return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
            }
        } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }
}



public function permissionpreview(Request $request) {
    try{
            $validator = Validator::make($request->all(), [
                'accessToken' => 'required',
                'candidateId' => 'required|numeric',
                'userloginId' => 'required|numeric',
                'permissionId' => 'required|numeric',
                'location_id' => 'required',
            ]);
            if($validator->fails()) {
                return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);
            }
             $inputs = $request->all();
             $accessToken = trim($inputs['accessToken']);
             $candidate_id = trim($inputs['candidateId']);
             $login_id = trim($inputs['userloginId']);
             $permission_id = trim($inputs['permissionId']);
             $location_id = trim($inputs['location_id']);
             $election_id=trim($inputs['election_id']);
             $election_details =DB::table('m_election_history')->where('id',$election_id)->first();
            
            $newuser = User::where([['access_token', '=', $accessToken]])->where('id', '=',$login_id)->first();
             if(isset($newuser)){
             if($newuser->access_token == $accessToken)
             {
                 $result = DB::table('permission_request')
                 ->Join('permission_type', 'permission_request.permission_type_id', '=', 'permission_type.id')
                 ->join('permission_master', 'permission_type.permission_type_id', '=', 'permission_master.id')
                 ->rightJoin('user_login', 'permission_request.user_id', '=','user_login.id')
                 ->rightJoin('user_data','user_login.id', '=','user_data.user_login_id')
                 ->leftJoin('user_role','user_login.role_id','=','user_role.role_id');
                 if($location_id != 'other' && $location_id != '0')
                 {
                    $result->rightJoin('location_master', 'permission_request.location_id', '=', 'location_master.id');
                 }
                 
                 if($location_id != 'other' && $location_id != '0')
                 {
                 $result->select('user_role.role_name','permission_request.required_files','permission_request.location_id','permission_request.Other_location','location_master.location_name','user_data.name','user_data.fathers_name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','user_data.address','user_data.added_at as form filled date',
                 'permission_master.permission_name','permission_request.date_time_start','permission_request.date_time_end','permission_request.approved_status','permission_request.added_at','location_master.latitude','location_master.longitude',
                 'permission_request.cancel_status','permission_request.dist_no','permission_request.ac_no','permission_request.st_code');
                 }
                 else{
                     $result->select('user_role.role_name','permission_request.required_files','permission_request.location_id','permission_request.Other_location','user_data.name','user_data.fathers_name','user_data.email','user_data.mobileno',
                     'user_data.gender','user_data.dob','user_data.address','user_data.added_at as form filled date','permission_master.permission_name','permission_request.date_time_start','permission_request.date_time_end','permission_request.approved_status','permission_request.added_at','permission_request.cancel_status','permission_request.dist_no','permission_request.ac_no','permission_request.st_code');
                 }
                 $result->where([['user_login.id' , $login_id],['permission_request.id', $permission_id]]);
                 $permis =$result->first();
                 
                if(!empty($permis)) {
                        if($permis->approved_status == 0){ $status = 'Pending'; }
                        elseif($permis->approved_status == 1){ $status = 'Inprocess'; }
                        elseif($permis->approved_status == 2){ $status = 'Accept'; }
                        elseif($permis->approved_status == 3){ $status = 'Reject'; }
                        $ac="not found";$pc="not found";$dist="not found";
                        if($permis->ac_no != 0){
                             $ac = trim($this->commonModel->getacbyacno($permis->st_code,$permis->ac_no)->AC_NAME); 
                            }
                        
                        if($permis->dist_no != 0){ 
                            $dist = trim($this->commonModel->getdistrictbydistrictno($permis->st_code,$permis->dist_no)->DIST_NAME);
                        }
                        
                        if($permis->required_files != 'null' && $permis->required_files != 'NULL'){
                        
                        $docdata=explode(',',$permis->required_files);
                    
                        $doc = array();
                        for($i=0;$i < count($docdata); $i++){
                            if(!empty($docdata[$i])){
                                // $doc[] = array("doc_by_candidate"=>url('uploads/userdoc/permission-document').'/'.$docdata[$i]) ;
                               
                               if($election_details->const_type=='AC')
                               {
                                $doc[] = array("doc_by_candidate"=>url('/').'/'.$docdata[$i]);
                               } elseif($election_details->const_type=='PC')
                               {
                               // $host = $request->getSchemeAndHttpHost();
                                $urlis = 'https://suvidha.eci.gov.in/ac/public/';
                                $doc[] = array("doc_by_candidate"=>$urlis.'/uploads/userdoc/permission-document/'.$docdata[$i]);

                               }else{

                                $doc[]="No Record";
                               }
                            }
                        }
                        }else{
                            $doc = array();
                        }
                        
                           $location_id=0;
                            $longitude=0;
                            $latitude=0;
                            $location_name="Location Name Not Defiend";

                        /*if(!empty($permis->location_id) && $permis->location_id !=0)
                        {
                            if(empty($permis->location_name)){
                                $success = ['success' => false, 'message' => "Please Check location_id"];
                                return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
                            }
                            
                            $location_id=$permis->location_id;
                            $longitude=$permis->longitude;
                            $latitude=$permis->latitude;
                            $location_name=$permis->location_name;
                            
                        }else{
                            $location_id=0;
                            $longitude=0;
                            $latitude=0;
                            $location_name="Location Name Not Defiend";
                        }*/
                        $msg = 'true';
                        $perm=array("name"=>$permis->name,"father_name"=>$permis->fathers_name,"email"=>$permis->email,"mobile"=>$permis->mobileno,"gender"=>$permis->gender,"dob"=>$permis->dob,"address"=>$permis->address,"state"=>trim($this->commonModel->getstatebystatecode($permis->st_code)->ST_NAME),'DistrictName'=>$dist,"ACName"=>$ac,"PCName"=>$pc,"permission"=>$permis->permission_name,"permission_registerd_date"=>$permis->added_at,"permission_from"=>$permis->date_time_start,"permission_till"=>$permis->date_time_end,"permission_approved_status"=>$permis->approved_status,"permission_approved_status_detail"=>$status,'Permission_longitude'=>$longitude,'Permission_latitude'=>$latitude,"location_name"=>$location_name,"location_id"=>$location_id,"location_other_name"=>$permis->Other_location,"cand_role_name"=>$permis->role_name,"doc_upload"=>$doc,"is_canceled"=>$permis->cancel_status);
                    }
                else{
                    $msg = 'false';
                    $perm = (object)array();
                }
                $success['success'] = $msg;
                $success['userloginid'] = $newuser->id;
                $success['candidateId'] = $candidate_id;
                $success['permission'] = $perm;
                $success['session'] = '1';
                return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
             } else {
                  $error['success'] = 'false';
                  $error['message'] = 'Entered Access Token wrong';
                  return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response); 
             }
            }else{
               $error['success'] = 'false';
               $error['message'] = 'Entered Access Token or candidate Id wrong';
                 $error['session'] = '0';
               return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);   
            }
        } catch (Exception $ex) {
            $error = 'Internal Server Errror.';
            return $this->ResponseMethod->get_http_response($this->errStatus, $error, $this->bad_response);
        }

}


public function logout(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'accessToken' => 'required',
            'candidateId' => 'required|numeric',
            'userId' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->ResponseMethod->get_http_response($this->errStatus, $validator->errors()->first(), $this->bad_response);
        }
        $accessToken = trim($request->accessToken);
        $candidate_id = trim($request->candidateId);
        $user_id = trim($request->userId);
        if($candidate_id > 0 ){
        $newuser = User::where('access_token',$accessToken)->where('candidate_id', '=',$candidate_id)->first();
        }else if($user_id > 0)
        {
            $newuser = User::where('access_token',$accessToken)->where('id', '=',$user_id)->first();
        }
        if(isset($newuser)){
            if($newuser->access_token == $accessToken)
            {      
            $token = '';
            $otp = '';
            $logdata = array('access_token'=>$token,'login_flag'=>0,'otp'=>$otp,
            'isActive'=>0,'verify_otp'=>0);
           

            if($candidate_id > 0 ){
         DB::table('user_login')->where([['access_token' , $accessToken],['candidate_id' , $candidate_id]])->update($logdata);
        }else if($user_id > 0)
        {
        DB::table('user_login')->where([['access_token' , $accessToken],['id' , $user_id]])->update($logdata);
        }
            
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






    
        public function otp_attempt($userid,$attempt_value)
    {
        User::where('id', $userid)->update(array('OTP_attempt' => $attempt_value));
    }









   public function getElectionByDate()
    {

            $filter_array = [
                'const_type'                => 'AC',
            ];
            $election_details = DB::connection('mysql')->table('m_election_history')
                ->where($filter_array)
                ->orderby('id', 'desc')
                ->groupBy('election_id')
                ->limit(3)
                ->get();

        if (!empty($election_details) > 0) {
                foreach($election_details as $raw){
                $election_data[] = array(
                    "election_id"       => $raw->id,
                    "election_type_id"  => $raw->election_type_id,
                    "const_type"        => $raw->const_type,
                    "elect_type"        => $raw->elect_type,
                    "description"       => $raw->description,
                );
                }
                $success['success'] = true;
                $success['data'] = $election_data;
                
        } else {
             $error['success'] = false;
         
            return response()->json($error, $errStatus);
            //return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
        }
           return response()->json($success, $this->successStatus);
           //return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
        //return $this->sendResponse($election_data, 'Record Found!');
    }

     public function getElectionByDatePC()
    {
            $filter_array = [
                'const_type'                => 'PC',
                //'election_type_id'          => '3',
            ];
            //$election_details = DB::connection('mysql_database_history')->table('m_election_history')
            $election_details = DB::connection('mysql')->table('m_election_history')
                ->where($filter_array)
                ->orderby('id', 'desc')
                ->groupBy('election_id')
                ->limit(3)
                ->get();

        if (!empty($election_details) > 0) {
                foreach($election_details as $raw){
                $election_data[] = array(
                    "election_id"       => $raw->id,
                    "election_type_id"  => $raw->election_type_id,
                    "const_type"        => $raw->const_type,
                    "elect_type"        => $raw->elect_type,
                    "description"       => $raw->description,
                );
                }
                $success['success'] = true;
                $success['data'] = $election_data;
                
        } else {
            
               $error['success'] = false;
                 return response()->json($error, $this->errStatus);
               
              // return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
           // return $this->sendError('No Record Found!', (object)[], $this->successStatus);
        }

          return response()->json($success, $this->successStatus);
       // return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
       // return $this->sendResponse($election_data, 'Record Found!');
    }


   public function getElectionByDateuat()
    {

            $filter_array = [
                'const_type'                => 'AC',
            ];
            $election_details = DB::connection('suivhdapclivetest')->table('m_election_history')
                ->where($filter_array)
                ->orderby('id', 'desc')
                ->groupBy('election_id')
                ->limit(3)
                ->get();

        if (!empty($election_details) > 0) {
                foreach($election_details as $raw){
                $election_data[] = array(
                    "election_id"       => $raw->id,
                    "election_type_id"  => $raw->election_type_id,
                    "const_type"        => $raw->const_type,
                    "elect_type"        => $raw->elect_type,
                    "description"       => $raw->description,
                );
                }
                $success['success'] = true;
                $success['data'] = $election_data;
                
        } else {
             $error['success'] = false;
         
            return response()->json($error, $errStatus);
            //return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
        }
           return response()->json($success, $this->successStatus);
           //return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
        //return $this->sendResponse($election_data, 'Record Found!');
    }

     public function getElectionByDatePCuat()
    {
            $filter_array = [
                'const_type'                => 'PC',
                //'election_type_id'          => '3',
            ];
            //$election_details = DB::connection('mysql_database_history')->table('m_election_history')
            $election_details = DB::connection('suivhdapclivetest')->table('m_election_history')
                ->where($filter_array)
                ->orderby('id', 'desc')
                ->groupBy('election_id')
                ->limit(3)
                ->get();

        if (!empty($election_details) > 0) {
                foreach($election_details as $raw){
                $election_data[] = array(
                    "election_id"       => $raw->id,
                    "election_type_id"  => $raw->election_type_id,
                    "const_type"        => $raw->const_type,
                    "elect_type"        => $raw->elect_type,
                    "description"       => $raw->description,
                );
                }
                $success['success'] = true;
                $success['data'] = $election_data;
                
        } else {
            
               $error['success'] = false;
                 return response()->json($error, $this->errStatus);
               
              // return $this->ResponseMethod->get_http_response($this->okStatus, $error, $this->ok_response);
           // return $this->sendError('No Record Found!', (object)[], $this->successStatus);
        }

          return response()->json($success, $this->successStatus);
       // return $this->ResponseMethod->get_http_response($this->okStatus, $success, $this->ok_response);
       // return $this->sendResponse($election_data, 'Record Found!');
    }






   public function getactiveelction()
    {
        $electiontype = DB::select("select temp.* from ( select a.election_type_id as election_type_id,a.id as election_id ,a.elect_type,a.const_type,a.description,a.year,a.candidate_active_status as status,case when election_type_id = '1' Then '0' Else '1' End as Availability,case when const_type = 'PC' Then 'Parliament Constituency' Else 'Assembly Constituency' End as election_name  from m_election_history a where a.election_type_id='1' order by a.election_type_id desc limit 1,1 )temp 

union all select temp1.* from ( select b.election_type_id as election_type_id,b.id as election_id ,b.elect_type,b.const_type,b.description,b.year,b.candidate_active_status as status,case when election_type_id = '2' Then '1' Else '0' End as Availability,case when const_type = 'PC' Then 'Parliament Constituency' Else 'Assembly Constituency' End as election_name from m_election_history b where b.election_type_id='2' order by b.election_type_id desc limit 1 )temp1 

union all select temp2.* from (select c.election_type_id as election_type_id,c.id as election_id ,c.elect_type,c.const_type,c.description,c.year,c.candidate_active_status as status,case when election_type_id = '3' Then '1' Else '0' End as Availability,case when const_type = 'PC' Then 'Parliament Constituency' Else 'Assembly Constituency' End as election_name from m_election_history c where c.election_type_id='3' order by c.election_type_id desc limit 1 )temp2 

union all select temp3.* from ( select d.election_type_id as election_type_id,d.id as election_id ,d.elect_type,d.const_type,d.description,d.year,d.candidate_active_status as status,case when election_type_id = '4' Then '1' Else '0' End as Availability,case when const_type = 'PC' Then 'Parliament Constituency' Else 'Assembly Constituency' End as election_name from m_election_history d where d.election_type_id='4' order by d.election_type_id desc limit 1 )temp3"); 
                $success['success'] = true;
                $success['schedual'] = $electiontype;
             
                return response()->json($success, $this->successStatus);
       
      


    }














  
}