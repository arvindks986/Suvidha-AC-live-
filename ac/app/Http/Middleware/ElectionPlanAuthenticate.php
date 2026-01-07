<?php

namespace App\Http\Middleware;

use Closure;
use Session;
class ElectionPlanAuthenticate
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
        $role=Session::get('admin_login_details')->role_id;
         
        if ( $role == 39) {
 
             return $next($request)->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
         }
         
         return redirect('/officer-login');
    }
}
