<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect, Auth, DB, Session, Config;
use Illuminate\Support\Facades\Route;

class Url_Middleware 
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
       $url=url('/'); 
      if($url=="https://encore.eci.gov.in/ac/public" || $url=="https://encore.eci.gov.in/ac/public"){
        return $next($request);
      }elseif($url=="https://suvidha.eci.gov.in/ac/public"|| $url=="http://suvidha.eci.gov.in/ac/public"){
        return $next($request);
        
      }
      else{
          return redirect('https://encore.eci.gov.in/ac/public/officer-login');
      } 
      
      
  }
}
