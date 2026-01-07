<?php
	namespace App\Http\Controllers\Admin;
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Admin;
	use Session;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Support\Facades\Hash;
    use Carbon\Carbon;
    use DB;
    use Validator;
    use Config;
    use \PDF;
    use Excel;
    use Mail;
    use App\commonModel;
    use App\Helpers\SmsgatewayHelper;
	use App\Helpers\LogNotification;
    
class HomeController1 extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
        {
        //$this->middleware(['auth:admin','auth']);
        $this->commonModel = new commonModel();
        }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard(){
        return Auth::guard('admin');
      }


    public function index(Request $request)
          {  
            $users=Session::get('admin_login_details');


			//dd($users);

              $user = Auth::user();    
             if(session()->has('admin_login')){  
                  $uid=$users->id;
                  $d=$this->commonModel->getunewserbyuserid($uid);
            $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
            $role=$d->role_id;
			$role_level=$d->officerlevel;
			
			$var = '';
			if(!empty($_SERVER["HTTP_USER_AGENT"])){
				
				$agent = $_SERVER["HTTP_USER_AGENT"];
				if( preg_match('/MSIE (\d+\.\d+);/', $agent) ) {
				  $var = "Internet_Explorer";
				} else if (preg_match('/Chrome[\/\s](\d+\.\d+)/', $agent) ) {
				  $var = "Chrome";
				} else if (preg_match('/Edge\/\d+/', $agent) ) {
				  $var = "Edge";
				} else if ( preg_match('/Firefox[\/\s](\d+\.\d+)/', $agent) ) {
				  $var = "Firefox";
				} else if ( preg_match('/OPR[\/\s](\d+\.\d+)/', $agent) ) {
				  echo "Opera";
				}else if(preg_match('/Safari[\/\s](\d+\.\d+)/', $agent) ) {
				  $var = "Safari";
				}
			}
			$brow=Session::put('browser',$var);
			
				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $users->officername ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'ENCORE';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'User_Logged_in';
				$ErrorMessage['TransectionStatus']= 'SUCCESS';
				$ErrorMessage['LogDescription']= 'User Logged In Successfully';
				LogNotification::LogInfo($ErrorMessage);

			
                if($role == 7 || $role == 25 || $role == 26){
                    return Redirect::to('eci/dashboard');
                  }
                  elseif($role == 4 || $role == 23){
                      return Redirect::to('acceo/dashboard');
                  }
                  elseif($role == 5 || $role == 24 || $role_level == 'PCI'){
                       return Redirect::to('acdeo/dashboard');
                  }
                  elseif($role == 19 || $role == 17 || $role == 20 || $role == 21 || $role==36){ 
					  if(\Session::has('DB_id') && \Session::get('DB_id') == '5'){
							if($role == 19 && $d->st_code == 'S24' && $d->ac_no == '228'){
							  return Redirect::to('roac/booth-app/dashboard');
							}
					  }
                       return Redirect::to('roac/dashboard');
                  }elseif($role == '27'){
						 return Redirect::to('eci-index/dashboard');
				  }elseif($role == 28){
                      return Redirect::to('eci-expenditure/expdashboard');
                  }else if($role == 37){
						return Redirect::to('maintenance/dashboard');
				  }else if($role == 39){
					return Redirect::to('etpbs/dashboard');
				  }elseif($role == 46){
                          return Redirect::to('mlc/ro/dashboard');
                  }elseif($role == 44 || $role == 45){  
						return Redirect::to('mparty/dashboard');
				 }


                  else{
					  
				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $users->officername ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'ENCORE';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'User_Logged_in';
				$ErrorMessage['TransectionStatus']= 'FAILURE';
				$ErrorMessage['LogDescription']= 'User login failed';
				LogNotification::LogInfo($ErrorMessage);
					  

                      return Redirect::to('/officer-login');
                  }
                 
                }
              else {  

				$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
				$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
				$ErrorMessage['MobNo']= $users->officername ?? '';
				$ErrorMessage['applicationType']= 'WebApp';
				$ErrorMessage['Module']= 'ENCORE';
				$ErrorMessage['TransectionType']= 'User';
				$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				$ErrorMessage['TransectionAction']= 'User_Logged_in';
				$ErrorMessage['TransectionStatus']= 'FAILURE';
				$ErrorMessage['LogDescription']= 'User login failed';
				LogNotification::LogInfo($ErrorMessage);

			  
                       return redirect('/officer-login');
                  }
              
          }
    
    public function logout(){
		try{
			
		$users=Session::get('admin_login_details');	
		$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
		$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
		$ErrorMessage['MobNo']= $users->officername ?? '';
		$ErrorMessage['applicationType']= 'WebApp';
		$ErrorMessage['Module']= 'ENCORE';
		$ErrorMessage['TransectionType']= 'User';
		$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		$ErrorMessage['TransectionAction']= 'User_Logout';
		$ErrorMessage['TransectionStatus']= 'SUCCESS';
		$ErrorMessage['LogDescription']= 'User Logout Successfully';
		LogNotification::LogInfo($ErrorMessage);
		
		
        \DB::table("officer_login")->where("id",$users->id)->update(["login_flag" => 0]);
        }catch(\Exception $e){
		}	
			if(\Session::has('username')){
		    $lastdata = DB::table('officer_history')->where(['officer_login_id'=>\Session::get('username') ])->orderBy('id', 'desc')->first();
			if(!empty($lastdata->id)){	
			\DB::table("officer_history")
			->where("id", $lastdata->id)
			->update([
				"ipaddress" => $_SERVER['REMOTE_ADDR'],
				"logout_time" => Date('Y-m-d H:i:s')
			    ]);
			  }  
			}
			  
            Auth::logout();
            Session::flush();       
            return Redirect::to('/officer-login');               
        }
    
      // public function refreshCaptcha()
      //       {    
      //           return response()->json(['captcha'=> captcha_img()]);
      //       }
            public function refreshCaptcha()
            {
                return captcha_src('default');
                //return response()->json(['captcha'=> captcha_img()]);
            }
      
}