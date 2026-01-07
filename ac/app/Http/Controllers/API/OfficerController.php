<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\adminmodel\OfficerApiModel;
use Illuminate\Support\Facades\Response;
use App\OfficerApiModel as AppOfficerApiModel;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use App\commonModel;

class OfficerController extends Controller
{
		public function __construct() {
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

        public function authenticate(Request $request)
            {  
			
                $username = trim($request['username']);
                $password = trim($request['password']);
				
				$userob =  DB::table('officer_login')->where([
                'officername'  => $username,
                'is_active'   => 1
				])->first();
				
				

                if (auth()->guard('admin')->attempt(['officername' => $username, 'password' => $password,'is_active'=>1]))
                   {

                       $user_data=Auth()->guard('admin')->user();
                       $d = OfficerApiModel::find($user_data->id, ['id', 'officername', 'designation', 'placename',
                            'name','st_code','dist_no','ac_no','pc_no', 'Phone_no','email','officerlevel']);
						
					   $desig=$d['officerlevel']; $lst=$d['st_code']; $lpc=$d['pc_no'];$lac=$d['ac_no'];
				
						if($lpc==0)
						{
							$lpc=1;
						}
						if($lac==0)
						{
							$lac=1;
						}
						if($lst=='')
						{
							$lst="S01";
						}
						
						$d['ac_name']=$this->commonModel->getacbyacno($lst,$lac)->AC_NAME;
						$d['pc_name']=$this->commonModel->getpcbypcno($lst,$lpc)->PC_NAME;
						$d['st_name']=$this->commonModel->getstatebystatecode($lst)->ST_NAME;
						
						
						
                       $token = $d->createToken('MyApp')->accessToken;
                       $id = $user_data->id;
                       $officername = $user_data->officername;
                       
                       $array = array('accesstoken'=> $token);
                       DB::table('officer_login')->where([['id' , $id],['officername' , $officername]])->update($array);
                       $success['success'] = true;
                       $success['message'] = 'You Are Successfully Logged In';
                       $success['userdetails'] = $d;
                       $success['token'] = $token;
                       return response()->json($success, $this->successStatus);
                 }elseif($userob){
					 $stor_pass_new = $userob->password;
					 $user_pass_input = hash('sha256',$password);
					 
					 if($stor_pass_new === $user_pass_input){
					  
					  $user_data = Auth::guard('admin')->loginUsingId($userob->id);
					  
                       $d = OfficerApiModel::find($user_data->id, ['id', 'officername', 'designation', 'placename',
                            'name','st_code','dist_no','ac_no','pc_no', 'Phone_no','email','officerlevel']);
					   $desig=$d['officerlevel']; $lst=$d['st_code']; $lpc=$d['pc_no'];$lac=$d['ac_no'];
				       
						if($lpc==0)
						{
							$lpc=1;
						}
						if($lac==0)
						{
							$lac=1;
						}
						if($lst=='')
						{
							$lst="S01";
						}
						
						$d['ac_name']=$this->commonModel->getacbyacno($lst,$lac)->AC_NAME;
						$d['pc_name']=$this->commonModel->getpcbypcno($lst,$lpc)->PC_NAME;
						$d['st_name']=$this->commonModel->getstatebystatecode($lst)->ST_NAME;
						
                       $token = $d->createToken('MyApp')->accessToken;
                       $id = $user_data->id;
                       $officername = $user_data->officername;
                       
                       $array = array('accesstoken'=> $token);
                       DB::table('officer_login')->where([['id' , $id],['officername' , $officername]])->update($array);
                       $success['success'] = true;
                       $success['message'] = 'You Are Successfully Logged In';
                       $success['userdetails'] = $d;
                       $success['token'] = $token;
                       return response()->json($success, $this->successStatus);
						 
					 }else{
						$success['success'] = false;
						$success['message'] = 'Invalid user please check the username or password';
						return response()->json($success, $this->successStatus);
					}
				 }
             else{
                $success['success'] = false;
                $success['message'] = 'Invalid user please check the username or password';
                return response()->json($success, $this->successStatus);
                }
            }

        public function logout(Request $request) {
            $username = trim($request['username']);
            $password = trim($request['password']);

            if (auth()->guard('admin')->attempt(['officername' => $username, 'password' => $password,'is_active'=>1]))
            {
                $user_data=Auth()->guard('admin')->user();
                $d = OfficerApiModel::find($user_data->id, ['id', 'officername', 'designation', 'placename',
                     'name','st_code','dist_no','ac_no','pc_no', 'Phone_no','email','officerlevel']);
                $token = "";
                $id = $user_data->id;
                $officername = $user_data->officername;
                
                $array = array('accesstoken'=> $token);
                DB::table('officer_login')->where([['id' , $id],['officername' , $officername]])->update($array);
                $success['success'] = true;
                $success['message'] = 'You Are Successfully Logged Out';
                return response()->json($success, $this->successStatus);
            }
      else{
         $success['success'] = false;
         $success['message'] = 'Invalid user please check the username or password';
         return response()->json($success, $this->successStatus);
         }
        }
		
				public function officerlogout(Request $request) {
                try {
				$validator = Validator::make($request->all(), [
					'accesstoken' => 'required|string',
				]);

				if ($validator->fails()) {
					return response()->json(['success' => false, 'message' => 'invalid accessToken!']);
				}
				$accessToken = trim($request['accesstoken']);
				$officer = DB::table('officer_login')->where('accesstoken','=' , $accessToken)->get()->count();
				if($officer > 0){
                $token = "";   
                $array = array('accesstoken'=> $token);
                DB::table('officer_login')->where('accesstoken','=' , $accessToken)->update($array);
                $success['success'] = true;
                $success['message'] = 'You Are Successfully Logged Out';
                return response()->json($success, $this->successStatus);
				}else{
					return response()->json(['success' => false, 'message' => "accessToken entered is not correct or you are already logout"]);
				}
				} catch (Exception $ex) {
				return response()->json(['success' => false, 'error' => 'Internal Server Error'], $this->intservererrorStatus);
			}
        }
		
		
}
