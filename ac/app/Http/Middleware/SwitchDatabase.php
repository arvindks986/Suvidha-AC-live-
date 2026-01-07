<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SwitchDatabase 
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
     
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
		if(Session::has('browser')) {
			
		  $cookes_browser=Session::get('browser');
		  if($cookes_browser != $var){
			  Session::flush();
				Auth::logout();
			 
			   return redirect('/officer-login');
		  }
		}
	 

    if(Session::has('DB_DATABASE')) {
        Config::set('database.connections.mysql.database', Session::get('DB_DATABASE'));
        DB::reconnect('mysql');
      }else{
        //Session::flash('error_mes','Please choose a Election type first.');
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::reconnect('mysql');
      }
	  
	  
	  
      return $next($request);
  }
}
