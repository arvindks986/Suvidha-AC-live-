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
    
class TempHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('usersession');
        $this->middleware(['auth:web','auth']);
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        // $this->middleware('cand');
    }
    protected function guard(){
       return Auth::guard('web');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{  
	$request_url = url('/');
	//echo $request_url;
		if ($request_url == 'https://suvidha.eci.gov.in/suvidhaac/public') {
		  return redirect('/login');
		}else{
		  return redirect('/officer-login');
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
                    return Redirect::to('/candidate-roletype');
                }else
                {
                  $result=DB::table('user_login')->get();
                  $re=$result[0]->permission_request_status;
                  // dd($re);
                  if($re == 0)
                  {
					$chk =  DB::connection('boothapptest')
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
                    //return Redirect::to('/update profile');
                  }else
                  {
                    return Redirect::to('/create');
                  } 
                }   
            }
            else {    
                 return redirect('/candidate-login');
                }  
            }
			
			public function roletype()
			{
				Auth::guard('web');
				if(Auth::check()){
				$role =getallpartylist();
				// dd($role);
				$role_type = DB::table('user_role')->where('role_level','2')->select('role_id','role_name')->get();
				// dd($role_type);
				return view('auth.dummy-user-auth.RoleType',compact('role_type','role'));
				}else{
					return Redirect::back();
				}
			}
			
			
		public function permissionrole(Request  $request)
		{
			Auth::guard('web');
			if(Auth::check()){
			$data = $request->all();
			$validator = Validator::make($data, [
					'role_id'				=>'required|not_in:0',
					'party_id' 	    		=>'required|not_in:0',
					
			   ]);
			   if ($validator->fails()) {
				  return Redirect::back()
				  ->withErrors($validator)
				  ->withInput();
				}else{
					$users=Session::get('login_details');
					$user = Auth()->user();
					$userid=$user->id;
					$mobile=$user->mobile;
					$role_id = $request->input('role_id');
					$party_id = $request->input('party_id');
					$data = array('role_id'=>$role_id,'party_id'=>$party_id);
					$role_type = DB::table('user_login')->where('id',$userid)->update($data);
					$role= DB::table('user_role')->where('role_id',$role_id)->get();
				    $roletype=$role[0]->role_name;
				    Session::put('Applicant_type', $roletype);
				    // dd(session::get('Applicant_type'));
					$u_data=DB::table('user_data')->where('mobileno',$mobile)->get();
					
					if($user->election_category != '2'){
						//First Login	
						$first_login=DB::connection('boothapptest')->table('user_login')->select('first_login')->where('id', '=', \Auth::id())->value('first_login');
						if($first_login=='' or $first_login==0){	
						 return redirect('/first-login-user-view');
						}
					}
					
					if(count($u_data)>0)
					{
						$result=DB::table('user_data')->where('mobileno',$mobile)->update(['party_id' => $party_id]);
						//return Redirect::to('/update profile');
						return Redirect::to('/dashboard-nomination-new');
					}else{
						//return Redirect::to('/profile'); 
						return Redirect::to('/nomination/apply-nomination-step-1'); 
					}
				
			    
		}
		}else{
			return Redirect::back();
		}
	}

			public function first_login_user_view(){ 
			 return view('/first-login-user-view');
			}
			
			
            public function logout()
            { 
            Auth::logout();
            Session::flush();       
            return Redirect::to('/login');               
           
            }
}
