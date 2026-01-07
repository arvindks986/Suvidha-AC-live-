<?php
namespace App\Http\Middleware;
use Closure;

class CheckuserSession
{

  public function handle($request, Closure $next)
    {  
	  
	  if($request->session()->has('user_login'))
		{ 
			return $next($request)->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		}

        return redirect('/');
    }

}