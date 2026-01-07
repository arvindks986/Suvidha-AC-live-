<?php
namespace App\Http\Middleware;
use Session;
use Closure;
use Illuminate\Support\Facades\Auth;
use Config,DB;

class PasswordChangeHash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
	 
	 protected $openRoutes = [
     'profile/password','profile/password/validate','logout','profile/password/update', 'profile/pin/update'
	 ];
	 
	public function handle($request, Closure $next)
	{
		$user_details=Session::get('admin_login_details');
		if(!empty($user_details)){
			$change_flag = DB::table('officer_login')->where('id',$user_details->id)->first();
			$change_flag = $change_flag->pass_flag;
			if((!in_array($request->path(), $this->openRoutes)) && ($change_flag == 0)){
			   return redirect('/profile/password');
			}       
			
			return $next($request);
		}else{
			return $next($request);
		}
	}
   
}
