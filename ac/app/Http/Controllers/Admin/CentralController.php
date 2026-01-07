<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB, Common, Validator, Config, \PDF, Excel, Mail, Response;
use App\commonModel;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;

class CentralController extends Controller
{

    public function __construct()
    {   
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
    }


    public function login(){
		
        Session::put('DB_DATABASE', 'encore_master');
        Config::set('database.connections.mysql.database', 'encore_master');
        DB::reconnect('central');
        DB::purge('central');
        DB::setDefaultConnection('central');

        $data                           = [];
        $data['cdatabase'] = '';
        Session::put('is_common', true);
        $data['skip_password_network']  = \App\models\Admin\OfficerModel::skip_password_network();
        $data['action']                 = url("garudapp/post-login");
        $setting = \App\models\Admin\SettingModel::get_setting_cache();
        $users = Session::get('admin_login_details');
        $user = Auth::user();
		$random_string = $this->random_strings(32);
		\Session::put('xyxx', $random_string);
        $data['xyx']           = $random_string;
		$data['xcs'] = createSalt(); 
        return view('admin.central.web.login', $data);
    }

    public function post_login(Request $request)
    {
        $skip_password_network = \App\models\Admin\OfficerModel::skip_password_network();

        $data   = [];
        $rules  = [
            'username' => 'required',
            'lcaptcha' => 'required|captcha',
        ];

        if ($skip_password_network) {
            $rules  = [
                'username' => 'required',
                'lcaptcha' => 'required|captcha',
            ];
        } else {
            $rules  = [
                'username' => 'required',
                'password' =>  'required|min:4',
                'lcaptcha' => 'required|captcha',
            ];
        }

        $messages = [
            'username.required'   => 'Please enter a valid username.',
            'password'            => 'Please enter a valid password.',
            'lcaptcha.required'   => 'Please enter valid captcha code.',
            'lcaptcha.captcha'    => 'Please enter the valid captcha.',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            Session::flash('error_mes', 'Please enter the correct credentials.');
            return Redirect::back()->withInput($request->all())->withErrors($validator);
        }
		
		$get_data = \App\models\Admin\OfficerModel::where('officername','=',$request->username)->where('is_active',1)->first();
		if($get_data){
			if(empty($get_data->password)){
				Session::flash('error_mes','Your account has not been activated, Please contact CEO office.');
				return Redirect::back();
			}
		}
		
		if(!isset($get_data) && empty($get_data)){
				Session::flash('error_mes','Please enter the correct credentials.');
				return Redirect::back();
		}

        $username = $request->username;
        $password = $request->password;

        $data                   = [];
        $data['officername']    = ($username) ? $username : '';
        if ($skip_password_network) {
        } else {
            $data['password']   = ($password) ? $password : '';
        }
        $data['is_active']      = 1;
		
		if($get_data->pass_flag==1){
			$get_salt = Session::get('randnmbr');
			$saltedpasswrd=$password;
			$storedpass= hash('sha256',$get_data->password.$get_salt);
			$options = ['cost' => 10];
			
			// Hashing of the post password
			$hash= password_hash($saltedpasswrd,PASSWORD_DEFAULT, $options);
			if(!password_verify($storedpass,$hash)){
				Session::flash('error_mes','Please enter the correct credentials.');
				return Redirect::back();
			}
			$password = $get_data->password;

		}else{
			$salt=Session::get('xyxx');
			$password = $this->cryptoJsAesDecrypt($salt, $request->password);
			\Session::put('password', $password);
			$data['password']   = ($password)?$password:'';
			//dd($password);
			if (!\App\models\Admin\OfficerModel::authenticate($data)) {
				Session::flash('error_mes', 'Please enter the correct credentials.');
				return Redirect::back();
			}
		}
		
        

        \Session::put('username', $data['officername']);
        if ($skip_password_network) {
        } else {
            \Session::put('password', $data['password']);
        }

        return redirect("garudapp/pin");
    }
	
	public function cryptoJsAesDecrypt($passphrase, $jsonString){
		$jsondata = json_decode($jsonString, true);
		$salt = hex2bin($jsondata["s"]);
		$ct = base64_decode($jsondata["ct"]);
		$iv  = hex2bin($jsondata["iv"]);
		$concatedPassphrase = $passphrase.$salt;
		$md5 = array();
		$md5[0] = md5($concatedPassphrase, true);
		$result = $md5[0];
		for ($i = 1; $i < 3; $i++) {
			$md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
			$result .= $md5[$i];
		}
		$key = substr($result, 0, 32);
		$data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
		return json_decode($data, true);
    }

    public function get_pin(Request $request)
    {
        $data = [];
        $data['action'] = url("garudapp/post-pin");
        return view('admin.central.web.pin', $data);
    }
	
	public function checkstatus(Request $request){
		
		$get_data = \App\models\Admin\OfficerModel::where('officername','=',$request->username)->where('is_active',1)->first();
		if($get_data){
			return \Response::json(['status'=> true,'flag'=> $get_data->pass_flag]);
		}else{
			return \Response::json(['status'=> false,'flag'=> 0]);
		}
	}

    public function post_pin(Request $request)
    {
        $skip_password_network = \App\models\Admin\OfficerModel::skip_password_network();
        $data   = [];
        $rules  = [
            'pin' => 'required|pin',
			'lcaptcha' => 'required|captcha',
        ];
        $messages = [
            'required'   => 'Please enter a valid pin.',
            'pin'        => "please enter a valid 4 digits pin.",
			'lcaptcha.required'   => 'Please enter valid captcha code.',
            'lcaptcha.captcha'    => 'Please enter the valid captcha.'
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            Session::flash('error_mes', 'please enter a valid 4 digits pin.');
            return Redirect::back()->withInput($request->all())->withErrors($validator);
        }

        if ($skip_password_network) {
            if (!\Session::has('username')) {
                Session::flash('error_mes', 'Your session has been expire. Please enter the ');
                return redirect("central");
            }
        } else {
            if (!\Session::has('username') || !\Session::has('password')) {
                Session::flash('error_mes', 'Your session has been expire. Please enter the ');
                return redirect("central");
            }
        }

        $pin    = $request->pin;

        $data                   = [];
        $data['officername']    = \Session::get('username');

        if ($skip_password_network) {
        } else {
            $data['password']       = \Session::get('password');
        }

        $data['two_step_pin']   = $request->pin;
        $data['is_active']      = 1;

        $get_data = \App\models\Admin\OfficerModel::where('officername','=',$data['officername'])->where('is_active',1)->first();
		if(isset($get_data)){
			if($get_data->pass_flag==0){
				if(!\App\models\Admin\OfficerModel::attempt_login($data)){
					Session::flash('error_mes','Please enter the correct pin.');
					return Redirect::back();
				}
			}else{
				if(!\App\models\Admin\OfficerModel::attempt_login_sha256($data)){
					Session::flash('error_mes','Please enter the correct pin.');
					return Redirect::back();
				}
			}
		}else{
				Session::flash('error_mes','Please enter the correct pin.');
				return Redirect::back();
		}

        setcookie('client_ip', \Request::ip(), time() + (86400 * 30), "/");

        return Redirect::to('garudapp/dashboard');
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $uid = $user->id;
		$user = Auth::user();
        $uid = $user->id;
        // if(Auth::user()->role_id==4)
        //    return Redirect::to('dashboard');
        // elseif(Auth::user()->role_id==41)
        //     return Redirect::to('e_plan/dashboard');
        // elseif(Auth::user()->role_id==50)
        //     return Redirect::to('seczonal/dashboard');
        if(Auth::user()->role_id == 44 || Auth::user()->role_id== 45)
                return Redirect::to('mparty/dashboard');
        $data = array();
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'href' => Common::generate_url('dashboard'),
            'name' => 'Dashboard'
        ];
		
		return Redirect::to(Common::generate_url('mis/list-exgratia'));
        $d = $this->commonModel->getunewserbyuserid($uid);
		//dd($d);
		$data['user_data'] = $d;
                $ps_type = DB::table('fm_polling_station_type')->get();
                $data['ps_type'] = $ps_type;
        return view('admin.central.common.dashboard', $data);
    }

    public function validate_election(Request $request)
    {
        $rules = [
            'id'  => 'required'
        ];
        $validator = Validator::make($request->all(), $rules, []);
        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'errors'  => 'Please try again.'
            ]);
        }

        $default = DB::connection("mysql_database_history")->table("m_election_history")->find($request->id);
        if (!$default) {
            return Response::json([
                'success' => false,
                'errors'  => 'Please try again.'
            ]);
        }

        try {
            Config::set('database.connections.central.database', $default->db_name);
            DB::reconnect('central');
            DB::purge('central');
            DB::setDefaultConnection('central');
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'errors'  => 'Database Error.'
            ]);
        }

        $sql = DB::connection("central")->table("m_election_details");
        if (Auth::user()->st_code) {
            $sql->where("ST_CODE", Auth::user()->st_code);
        }
        if (Auth::user()->pc_no) {
            $sql->where("CONST_NO", Auth::user()->pc_no);
        } else if (Auth::user()->ac_no) {
            $sql->where("CONST_NO", Auth::user()->ac_no);
        }
        $result = $sql->first();
        if (!$result) {
            return Response::json([
                'success' => false,
                'errors'  => 'Election data is not available for you.'
            ]);
        }

        // $officer_login = DB::connection("central")->table("officer_login");
        // if ($result->ST_CODE) {
        //     $officer_login->where("ST_CODE", Auth::user()->st_code);
        // }
        // if ($result->CONST_TYPE == 'PC') {
        //     $officer_login->where("pc_no", Auth::user()->pc_no);
        // } else if ($result->CONST_TYPE == 'AC') {
        //     $officer_login->where("ac_no", Auth::user()->ac_no);
        // }
        // $officer_login->where("role_id", Auth::user()->role_id);
        // $officer_login->where("officername", Auth::user()->officername);
        // $user = $officer_login->first();
        // if (!$user) {
        //     return Response::json([
        //         'success' => false,
        //         'errors'  => 'You can not access election.'
        //     ]);
        // }
        
        try {
            Session::put('DB_id', $default->id);
            Session::put('DB_DATABASE', $default->db_name);
            // Auth::logout();
            // Auth::guard('admin')->loginUsingId($user->id);

            return Response::json([
                'success' => true,
                'errors'  => 'You can not access election.'
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'errors'  => 'Please try again.'
            ]);
        }
    }
	
	public function logout(){
		$user = Auth::user();
		$uid=$user->id;   
		$n=array('login_flag'=>0);  
		$a=$this->commonModel->updatedata('officer_login','id', $uid,$n); 
		Auth::logout();
		Session::flush();       
		return Redirect::to('/garudapp/login');               
	}
	function random_strings($length_of_string) 
	{ 
		// String of all alphanumeric character 
		$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
	  
		// Shufle the $str_result and returns substring 
		// of specified length 
		return strtoupper(substr(str_shuffle($str_result), 0, $length_of_string)); 
	} 

}
