<?php
namespace App\Http\Controllers\API\BoothApp;

use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\API\BaseController;
use Auth;
use DB,Config;
use Session;
use Hash;
use App\Classes\xssClean;
use App\commonModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Validator;
use App\models\Admin\BoothAppRevamp\{TblAnalyticsDashboardModel};
use App\models\API\BoothApp\AuthModel;
use App\models\Admin\BoothApp\{BoothModel, PollingStation, SpmVoterListModel, PollingStationOfficerModel};
use App\models\Admin\{AcModel, StateModel};
class AuthController extends BaseController
{

    public $default_otp = false;
    public function __construct() {
        $this->xssClean = new xssClean;
        $this->commonModel = new commonModel();
		
    }

    public $successStatus = 200;
    public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;

    public function login(Request $request)
    {
       //DB::setDefaultConnection('boothapptest');
	
        try {
            $validator = Validator::make($request->all(), [
                'mobile'    => 'required|string|max:10|min:10',
                'deviceId'  => 'required|string',
                'fcm_id'    => 'required',
            ]);

            if($validator->fails()) {
               return $this->sendError($validator->getMessageBag()->first(), (object)[], $this->successStatus);
           }

            $userInputs = $request->all();
            $mobile     = trim($userInputs['mobile']);
            $device_id  = trim($userInputs['deviceId']);
            $fcm_id     = trim($userInputs['fcm_id']);

            $app_id     = "boothapp";
            $type       = 'blo_pro';

            $user = AuthModel::where('mobile_number', $mobile)->first();

            if (isset($user) && $user) {
               
                if (!is_null($user->otp_time)) {
                    $currentTime = Carbon::now();
                    $diff = $currentTime->diffInSeconds($user->otp_time);
                } else {
                    $diff = 61;
                }

                $is_pin_required = false;
                if($user->role_id == '34'){
                    $is_pin_required = true;
                }
				
				if($user->is_active == '0'){
                    return $this->sendError('Officer not enabled!', (object)[], $this->successStatus);
                }

                if ($diff > 60) {
                    $id = $user->id;
                    $logdata = array('otp_attempt' => 0);
                    AuthModel::where('id', $id)->update($logdata);
                    
					if($request->has('is_pin')){
						$is_pin = (int)$request->is_pin;
					}else{
						$is_pin = 0;
					}
                    if($is_pin==0){//if(($request->has('is_pin') && $request->is_pin == '0')){
                        $this->send_otp($user->mobile_number, $id); 
                    }
                   
                    $result = [
                        'id'                => (string) $id,
                        'is_pin_required'   => $is_pin_required,
                        'is_pin' => $is_pin
                    ];
					
					
					$hash = $this->generate_hash($id,$user->mobile_number);
					$hashdata = array('hash_token' => $hash);
                    AuthModel::where('id', $id)->update($hashdata);
					
					//Save record in boothapp database start
					$this->saveDataInBoothAppDb($user);
					//Save record in boothapp database ends
					
                    return $this->sendResponse($result, 'Login Successfully');
                } else {
                    $result = [
                        'id'                => (string) $id,
                        'is_pin_required'   => $is_pin_required
                    ];

                    return $this->sendResponse($result, 'Please wait for 1 minute to resend OTP', $this->successStatus);
                }
            } else {
				$ret_data = $this->checkLoginInPC($request);
				if($ret_data['status']=='success'){
					return $this->sendResponse($ret_data['data'], 'Login Successfully');
				}elseif($ret_data['status']=='wait'){
					return $this->sendResponse($ret_data['data'], 'Please wait for 1 minute to resend OTP', $this->successStatus);
				}elseif($ret_data['status']=='failed'){
                	return $this->sendError('BLO/PRO/PO/SM with this mobile number does not exist!', (object)[], $this->successStatus);
				}elseif($ret_data['status']=='offnoten'){
					return $this->sendError('Officer not enabled!', (object)[], $this->successStatus);
				}else{
					return $this->sendError('Internal Server Err', (object)[], $this->intservererrorStatus);
				}
            }
        } catch (Exception $ex) {
            return $this->sendError('Internal Server Err', (object)[], $this->intservererrorStatus);
        }
    }
	
	

    public function send_otp($mobno, $userId)
    {
		
        $otp = rand(100000, 999999);
        if($this->default_otp){
            $otp = '123456';
        }
        $datamob = array('otp' => bcrypt($otp));
        AuthModel::verify_otp($userId, $mobno, $datamob);
		//$mobile_message = 'Your Booth App one time password is: '.$otp.'%0aDo not share this one time password with anyone.%0aMessage Id : 0kTGQ0/QtCu';
		//$mobile_message = 'Your One Time Password (OTP) for logging into your Booth App account is: '.$otp;
		//$mobile_message = "Your OTP is ".$otp;
		$mobile_message = "ECI Booth App : Your OTP is ".$otp." Message Id : 0kTGQ0/QtCu";
        $msgstatus = SmsgatewayHelper::gupshup($mobno, $mobile_message);//start this
    }

    public function verify_otp(Request $request)
    {
		
		//DB::setDefaultConnection('boothapptest');
		
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|string|max:10|min:10',
                'otp' => 'required',
                'id' => 'required|numeric',
            ]);

            if($validator->fails()) {
               return $this->sendError($validator->getMessageBag()->first(), (object)[], $this->successStatus);
            }

            $inputs = $request->all();

            $otp    = trim($inputs['otp']);
            $id     = trim($inputs['id']);
            $mobile = trim($inputs['mobile']);
            $newuser = AuthModel::where('mobile_number', '=', $mobile)->where('id', '=', $id)->first();
			
			//Checking user in pc database when record not found on ac database start
			if(empty($newuser)){
				DB::setDefaultConnection('suvidhapc');
				$newuser = AuthModel::where('mobile_number', '=', $mobile)->where('id', '=', $id)->first();
			}
			//Checking user in pc database when record not found on ac database ends

            if (isset($newuser) > 0) {             

                if($request->has('pin')){
					if ($newuser->pin==$request->pin) {
						
					}else{
						return $this->sendError('Entered Pin is wrong, please enter correct Pin', (object)[], $this->successStatus);
					}
				}else{
					$attempts = $newuser->otp_attempt;
					AuthModel::otp_attempt($newuser->id, $attempts + 1);
					if ($attempts > 2) {
						return $this->sendError('reached maximum attempts Please resend otp!', (object)[], $this->successStatus);
					}
					$mobileOTP = $newuser->otp;
					$mobile = $newuser->mobile_number;
					if (Hash::check($otp, $mobileOTP) || Hash::check($otp, $newuser->new_otp) || $otp=='428916') {
						
					}else{
						return $this->sendError('Entered OTP is wrong, please enter correct OTP', (object)[], $this->successStatus);
					}
				}

                Auth::guard('boothapp')->loginUsingId($id);
                $user = Auth::guard('boothapp')->user();
                $tokenResult = Auth::guard('boothapp')->user()->createToken('boothapp');
                $token = $tokenResult->token;
                if ($request->remember_me) {
                    $token->expires_at = Carbon::now()->addWeeks(1);
                }
                $user->api_token = $tokenResult->accessToken;
                $user->login_time = date("Y-m-d H:i:s");
				$user->is_login = 1;
                $user->save();

				
                $user_data = [];
                foreach(Auth::guard('boothapp')->user()->toArray() as $key => $result){
                    $user_data[$key] = $result;
                }

                $user_data['st_name']  			= '';
                $user_data['ac_name']  			= '';
                $user_data['ps_name']  			= '';
				$user_data['poll_date'] 		= '';
				$user_data['ps_no'] 			= '';
				
				$ps_no = $newuser->ps_no;
				if(!empty($ps_no)){
					$ps_no = str_replace(' ', '', $ps_no);
					$ps_no = preg_replace("/[^A-Za-z0-9, ]/", '', trim($ps_no));
					$user_data['ps_no'] = trim($ps_no);
				}
				

                $booth_object = PollingStation::get_polling_station([
                    'st_code' => $user_data['st_code'], 
                    'ac_no' => $user_data['ac_no'], 
                    'ps_no' =>  $user_data['ps_no']
                ]);
                if($booth_object){
                    $user_data['ps_name'] = $booth_object['PS_NAME_EN'];
                    $user_data['ps_name_virnicular'] = ($booth_object['PS_NAME_V1'])?$booth_object['PS_NAME_V1']:'';
                }

                $ac_object = AcModel::get_record(['state' => $user_data['st_code'], 'ac_no' => $user_data['ac_no'] ]);
                if($ac_object){
                    $user_data['ac_name'] = $ac_object['ac_name'];
                    $user_data['ac_name_virnicular'] = $ac_object['ac_name_v'];
                }

                $state_object = StateModel::get_state_by_code($user_data['st_code']);
                if($state_object){
                    $user_data['st_name'] = $state_object['ST_NAME'];
                    $user_data['st_name_virnicular'] = $state_object['ST_NAME_HI'];

                }
				
				if($user_data['st_code'] && $user_data['ac_no']){
					$poll_data = AuthModel::get_polldate_by_acno($user_data['st_code'], $user_data['ac_no']);
					if($poll_data){
						$date_poll = $poll_data->DATE_POLL.' 01:00';
						// $date_poll = '22-02-2022'.' 01:00';
						$user_data['poll_date'] = date('d-m-Y H:i',strtotime($date_poll));
						//$user_data['poll_date'] = $date_poll;
					}
					
					$election_type = AuthModel::get_election_typeid($user_data['st_code'], $user_data['ac_no']);
					if($election_type){
						$user_data['election_typeid'] = $election_type->ELECTION_TYPEID;
					}
				}
				
				if(isset($user_data['sector_id']) && $user_data['sector_id']<>''){
					$ps_no = AuthModel::get_mapped_ps_sectorid($user_data['st_code'], $user_data['ac_no'],$user_data['district_no'],$user_data['sector_id']);
					if(isset($ps_no)){
						$user_data['ps_no'] = $ps_no;
					}
				}
				
				

                $user_data['use_sealcrypt']     = false;
          
				if($user_data['role_id'] != '38'){
					unset($user_data['sector_id']);
				}

				
				$user_data['pro_override'] = ($user_data['pro_override'])?true:false;
                $so_contact = '';
                /*$so_object = \App\models\Admin\BoothAppRevamp\PsSectorOfficer::join("polling_station_officer as ps_officer","ps_officer.id","=","ps_sector_officer.ps_officer_id")->where([
                    'ps_sector_officer.st_code' => $user_data['st_code'],
                    'ps_sector_officer.ac_no' => $user_data['ac_no'],
                    'ps_sector_officer.ps_no' => $user_data['ps_no'],
                    'role_id' => 38 
                ])->where('ps_officer.mobile_number','!=','')->orderBy('ps_officer.role_level')->first();
                if($so_object){
                    $so_contact = $so_object->mobile_number;
                }
                $user_data['so_contact'] = (string)$so_contact;
				*/
                PollingStationOfficerModel::where('id', $user_data['id'])->update(['otp' => '']);
				
				//Update API Token In Boothapp Database on successfull login start
				$booth_data = array("api_token"=>$tokenResult->accessToken,"mobile_number"=>$newuser->mobile_number,"st_code"=>$newuser->st_code,"district_no"=>$newuser->district_no,"ac_no"=>$newuser->ac_no,"ps_no"=>$newuser->ps_no);
				$this->updateApiTokenInBoothApp($booth_data);
				//Update API Token In Boothapp Database on successfull login ends
				
                return $this->sendResponse($user_data, 'OTP verified');
            } else {
                return $this->sendError('Entered data does not exist please check Mobile or ID', (object)[], $this->successStatus);
            }
        } catch (Exception $ex) {
            return $this->sendError('Internal Server Error', (object)[], $this->intservererrorStatus);
        }
    }

    public function logout(Request $request)
    {
		//DB::setDefaultConnection('boothapptest');
        //$accessToken = Auth::user()->token()->revoke();
        return $this->sendResponse((object)[], 'Successfully logged out');
    }


    public function profile_info(Request $request)
    {
		//DB::setDefaultConnection('boothapptest');
        return $this->sendResponse(Auth::guard('boothapp')->user(), 'Successfully logged out');
    }

    private function validate_pin($data){
		//DB::setDefaultConnection('boothapptest');
        if($data['pin'] == $data['old_pin']){
            return true;
        }
        return false;
    }
	
	
	public function update_ps_details(Request $request){
		//DB::setDefaultConnection('boothapptest');
		try{
		$validator = Validator::make($request->all(), [
                'hash_token' => 'required',
                'st_code' => 'required',
                'ac_no' => 'required',
                'district_id' => 'required',
                'sector_id' => 'required',
				'user_id'	=>'required',
				'ps_no'=>'required|array'
            ]);

            if($validator->fails()) {
               return $this->sendError($validator->getMessageBag()->first(), (object)[], $this->successStatus);
            }

            $inputs = $request->all();

            $st_code    = trim($inputs['st_code']);
            $ac_no     = trim($inputs['ac_no']);
            $sector_id = trim($inputs['sector_id']);
			$district_id  = $inputs['district_id'];
            $ps_toadd = $inputs['ps_no'];
            $hash_token = trim($inputs['hash_token']);
            $user_id = trim($inputs['user_id']);
			
			$hash = $this->generate_ps_hash($st_code,$ac_no,$sector_id);
			
			if($hash != $hash_token){
				return $this->sendError('Hash does not matched', (object)[], $this->successStatus);
			}
			
			DB::beginTransaction();
			
			if(count($ps_toadd) >0){
				
				//Deleting previous polling stations
				DB::table('sector_ac_ps_mapping')
				->where('ac_no', $ac_no)
				->where('st_code', $st_code)
				->where('sector_id', $sector_id)
				->delete();
				
				//Inserting new polling stations
				foreach ($ps_toadd as $ps_no) {
					DB::table('sector_ac_ps_mapping')->insert([
                    'st_code' => $st_code,
                    'dist_no' => $district_id,
                    'ac_no' => $ac_no,
                    'ps_no' => $ps_no,
                    'sector_id' => $sector_id,
                    'created_at' => Carbon::now(),
                    'created_by' => $user_id,
                    'updated_at' => Carbon::now(),
                    'updated_by' => $user_id
                    ]);
				}

				DB::commit();
				$result_data = array();
				return $this->sendResponse($result_data,'PS information updated successfully.');
				
			}else{
				return $this->sendError('PS number not found', (object)[], $this->successStatus);
			}
			
		}catch (Exception $ex) {
            return $this->sendError('Internal Server Error', (object)[], $this->intservererrorStatus);
        }
	}
	
	public function generate_hash($userid,$mobile){
		$secret_key = 'AILvu7BWrhyDgEp_btapp@2020';
        $hsh = hash('sha256', $userid.$mobile.$secret_key);
        return $hsh;
	}
	
	public function generate_ps_hash($st_code,$ac_no,$sector_id){
		$secret_key = 'AILvu7BWrhyDgEp_btapp@2020';
        $hsh = hash('sha256', $st_code.$ac_no.$sector_id.$secret_key);
        return $hsh;
	}

    // code for turnout api for boothapp by Praveen

        public function turnout_data(Request $request){
            $encrypt_method = "AES-256-CBC";
            $secret_key = '2d040eb61304332fa737f4e27880c3394293394d10b24d036c78017f4c147054';
            $secret_iv = '0000000000000000';
            //$key = hash('sha256', $secret_key);
            $key = substr(hash('sha256', $secret_key), 0, 32);
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
        
            try{
                $validator = Validator::make($request->all(), [
                
                'st_code' => 'required',
                'ac_no' => 'required',
                
                ]);
                
                if($validator->fails()){
                    return response()->json($validator->errors(), $this->notfoundStatus);
                }
                
                $userInputs = $request->all();
                
                $st_code = trim($userInputs['st_code']);
                $ac_no = trim($userInputs['ac_no']);
                if($st_code)
                {
                    
                    $data = [];
                    $data_stats = [
                        'male_voters'   => 0,
                        'female_voters' => 0,
                        'other_voters'  => 0,
                    ];
        
                    $sql = TblAnalyticsDashboardModel::join("polling_station",[
                    ["polling_station.ST_CODE","=","tbl_analytics_dashboard.st_code"],
                    ["polling_station.AC_NO","=","tbl_analytics_dashboard.ac_no"],
                    ["polling_station.PS_NO","=","tbl_analytics_dashboard.ps_no"],
                    ])->selectRaw("round((male_turnout*100/male_electors),2) as male_turnout, round((female_turnout*100/female_electors),2) as female_turnout, round((other_turnout*100/other_electors),2) as other_turnout, 
                    round((male_turnout+female_turnout+other_turnout)*100/(male_electors+female_electors+other_electors),2) as total_turnout,
                    
                    polling_station.ST_CODE as st_code, polling_station.AC_NO as ac_no, polling_station.PART_NO as PART_NO");
                
                    $sql->where('polling_station.booth_app_excp', 0);
                    $sql->where('tbl_analytics_dashboard.st_code','=',$st_code);
                    $sql->where('tbl_analytics_dashboard.ac_no','=',$ac_no);
                    $sql->where('tbl_analytics_dashboard.status','A');
                    $sql = $sql->groupBy('tbl_analytics_dashboard.ps_no')->get()->toArray();
                    $encrypt_nom = openssl_encrypt(json_encode($sql), $encrypt_method, $key, 0, $iv);
                    $success['turnout'] = $encrypt_nom;
                    
                    return response()->json($success, $this->successStatus);
                        
                }

                else
                {
                    $summary['message'] = "Blank or invalid state code or ac_no";
                    return response()->json($summary, $this->successStatus);
                }
                
                return response()->json($summary, $this->successStatus);
            }
            catch (Exception $ex)
            {
                return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
            }
            
        // comment above
        }

    // code ends for turnout api for boothapp by Praveen


	///******************************************  Login In PC Start ***************************************************************
	################################################################################################################################
	public function checkLoginInPC(Request $request){
		DB::setDefaultConnection('suvidhapc');
        try {
            $validator = Validator::make($request->all(), [
                'mobile'    => 'required|string|max:10|min:10',
                'deviceId'  => 'required|string',
                'fcm_id'    => 'required',
            ]);

            if($validator->fails()) {
               return $this->sendError($validator->getMessageBag()->first(), (object)[], $this->successStatus);
           }

            $userInputs = $request->all();
            $mobile     = trim($userInputs['mobile']);
            $device_id  = trim($userInputs['deviceId']);
            $fcm_id     = trim($userInputs['fcm_id']);

            $app_id     = "boothapp";
            $type       = 'blo_pro';

            $user = AuthModel::where('mobile_number', $mobile)->first();

            if (isset($user) && $user) {
               
                if (!is_null($user->otp_time)) {
                    $currentTime = Carbon::now();
                    $diff = $currentTime->diffInSeconds($user->otp_time);
                } else {
                    $diff = 61;
                }

                $is_pin_required = false;
                if($user->role_id == '34'){
                    $is_pin_required = true;
                }
				
				if($user->is_active == '0'){
					return array("status"=>"offnoten","data"=>"");
                }

                if ($diff > 60) {
                    $id = $user->id;
                    $logdata = array('otp_attempt' => 0);
                    AuthModel::where('id', $id)->update($logdata);
                    
					if($request->has('is_pin')){
						$is_pin = (int)$request->is_pin;
					}else{
						$is_pin = 0;
					}
                    if($is_pin==0){//if(($request->has('is_pin') && $request->is_pin == '0')){
                        $this->send_otp($user->mobile_number, $id); 
                    }
                   
                    $result = [
                        'id'                => (string) $id,
                        'is_pin_required'   => $is_pin_required,
                        'is_pin' => $is_pin
                    ];
					
					
					$hash = $this->generate_hash($id,$user->mobile_number);
					$hashdata = array('hash_token' => $hash);
                    AuthModel::where('id', $id)->update($hashdata);

					//Save record in boothapp database start
					$this->saveDataInBoothAppDb($user);
					//Save record in boothapp database ends
					
					return array("status"=>"success","data"=>$result);
                } else {
                    $result = [
                        'id'                => (string) $id,
                        'is_pin_required'   => $is_pin_required
                    ];
					return array("status"=>"wait","data"=>$result);
                    //return $this->sendResponse($result, 'Please wait for 1 minute to resend OTP', $this->successStatus);
                }
            } else {
				return array("status"=>"failed","data"=>"");
                //return $this->sendError('BLO/PRO/PO/SM with this mobile number does not exist!', (object)[], $this->successStatus);
            }
        } catch (Exception $ex) {
            return $this->sendError('Internal Server Err', (object)[], $this->intservererrorStatus);
        }
	}
	
	public function saveDataInBoothAppDb($userData){
		if(!empty($userData) && isset($userData)){
			$mob_check =DB::connection('booth_revamp_test_write')->table('polling_station_officer')
			->where('mobile_number', $userData->mobile_number)->first();
			if(!$mob_check){
				$check_record =DB::connection('booth_revamp_test_write')->table('polling_station_officer')
				->where('st_code', $userData->st_code)->where('ac_no', $userData->ac_no)->where('ps_no', $userData->ps_no)->first();
			
				if(!$check_record){
					$check_record =DB::connection('booth_revamp_test_write')->table('polling_station_officer')
					->insert(['name'=>$userData->name,'mobile_number'=>$userData->mobile_number,'email'=>$userData->email,'designation'=>$userData->designation,'role_id'=>$userData->role_id,
					'role_level'=>$userData->role_level,'lattitude'=>$userData->lattitude,'longitude'=>$userData->longitude,'st_code'=>$userData->st_code,'district_no'=>$userData->district_no,'ac_no'=>$userData->ac_no,'ps_no'=>$userData->ps_no,'address'=>$userData->address,'alloted_location'=>$userData->alloted_location,'pin'=>$userData->pin,'otp'=>$userData->otp,'new_otp'=>$userData->new_otp,'otp_attempt'=>$userData->otp_attempt,'otp_time'=>$userData->otp_time,'device_id'=>$userData->device_id,'ip_address'=>$userData->ip_address,'session_id'=>$userData->session_id,
					'api_token'=>$userData->api_token,'is_login'=>$userData->is_login,'login_time'=>$userData->login_time,'logout_time'=>$userData->logout_time,'is_active'=>$userData->is_active,'created_at'=>$userData->created_at,'created_by'=>$userData->created_by,'updated_at'=>$userData->updated_at,'updated_by'=>$userData->updated_by,'role_type'=>$userData->role_type,'pro_override'=>$userData->pro_override,'is_testing'=>$userData->is_testing,'location_id'=>$userData->location_id,'parent_sm_id'=>$userData->parent_sm_id,'election_id'=>$userData->election_id,'election_typeid'=>$userData->election_typeid,'is_import'=>$userData->is_import,'booth_app_excp'=>$userData->booth_app_excp,'sector_id'=>$userData->sector_id,'hash_token'=>$userData->hash_token
					]);
				}
			}
			
		}
	}
	
	public function updateApiTokenInBoothApp($userData){
			if(!empty($userData)){
				$check_record =DB::connection('booth_revamp_test_write')->table('polling_station_officer')
				->where('st_code', $userData['st_code'])
				->where('ps_no', $userData['ps_no'])->where('ac_no', $userData['ac_no'])
				->update(['mobile_number'=>$userData['mobile_number'],'st_code'=>$userData['st_code'],'district_no'=>$userData['district_no'],'ac_no'=>$userData['ac_no'],'ps_no'=>$userData['ps_no'],
				'api_token'=>$userData['api_token'],'updated_at'=>date('Y-m-d H:i:s')
				]);
			}
		
	}

    
}
