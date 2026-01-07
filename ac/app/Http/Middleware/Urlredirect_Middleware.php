<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect, Auth, DB, Session, Config;
use Illuminate\Support\Facades\Route;

class Urlredirect_Middleware 
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
       $segment = Request::segment(1);
       if(($url=="https://encore.eci.gov.in/ac/public" || $url=="http://encore.eci.gov.in/ac/public") && ($segment=="login") ) {

         return redirect('https://encore.eci.gov.in/ac/public/officer-login');
      }
      elseif(($url=="https://suvidha.eci.gov.in/ac/public" || $url=="http://suvidha.eci.gov.in/ac/public") && ($segment=="officer-login") ) {

         return redirect('https://suvidha.eci.gov.in/ac/public/login');
      }

      
      return $next($request);
  }
}
