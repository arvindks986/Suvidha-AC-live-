<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
	use App\Http\helpers;
	use App\Classes\xssClean;
	use App\Helpers\LogNotification;
    
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
        {  
		$request_url = url('/');
		echo $request_url;
if ($request_url == 'https://suvidha.eci.gov.in/suvidhaac/public') {
  return redirect('/login');
}else{
  return redirect('/login');
}
        return view('welcome');
      }
    public function login()
            {
                $users=Session::get('login_details');
                $user = Auth::user();
                if(session()->has('user_login')){ 
                            return Redirect::to('/home');
                        }
                  else{  
                       return Redirect::to('/login');
                      } 
                
            }
    public function RemoveDummyUser(){
            Auth::logout();
            Session::flush();
            return Redirect::to('/login');
    }
    public function deleteuser(){
         if(session()->has('user_login')){ 
              $users=Session::get('login_details');
              $user = Auth()->user();
                //dd($user->mobile);
                        //
            return view('deleteuser',['datanew'=>$user]);
        }else{
            return Redirect::to('/login');
        }
    }
	public function dashboard(){
			$data = [];
			return view('auth.dummy-user-auth.candidate-dashboard',$data);
	}
	 public function userhome(Request $request)
            {   
              $users=Session::get('login_details');
              $user = Auth()->user();
               
              if(session()->has('user_login')){ 
                $getid=$user->id;
                
                if($user->role_id == NULL)
                {
                    return Redirect::to('/roletype');
                }else
                {
                  //$result=DB::table('user_login')->where('id',$getid)->get();
                  //$re=$result[0]->permission_request_status;
                  //if($re == 0)
                  //{ 
						if($user->role_id != 2)
						{
							 return Redirect::to('/update profile');
						}
						else{
						
							$chk =  DB::connection('mysql')
							->table('profile')
							->select('id')
							->where('candidate_id', '=', $user->id)
							->whereNotNull('email')
							->whereNotNull('epic_no')
							->whereNotNull('name')
							->get();			
							if(count($chk) > 0 ){
							  return Redirect::to('/dashboard-nomination-new');
							} else {
							  return Redirect::to('/nomination/apply-nomination-step-1');
							}
						
						}
                  //}else
                  //{
                    //return Redirect::to('/create');
                  //} 
                }   
            }
            else {    
                 return redirect('/login');
                }  
            }
			
            public function logout()
            { 
			
			$users=Session::get('login_details');
			
			//dd($users->mobile);
			
			$ErrorMessage['eventTime']= date('Y-m-d H:i:s');
			$ErrorMessage['serverAdd']= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
			$ErrorMessage['MobNo']= $users->mobile ?? '';
			$ErrorMessage['applicationType']= 'WebApp';
			$ErrorMessage['Module']= 'SUVIDHA';
			$ErrorMessage['TransectionType']= 'User';
			$ErrorMessage['srcIp']= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
			$ErrorMessage['TransectionAction']= 'User_Logout';
			$ErrorMessage['TransectionStatus']= 'SUCCESS';
			$ErrorMessage['LogDescription']= 'User Logout Successfully';
			LogNotification::LogInfo($ErrorMessage);
			
			
            Auth::logout();
            Session::flush();       
            return Redirect::to('/login');               
           
            }
            
}
