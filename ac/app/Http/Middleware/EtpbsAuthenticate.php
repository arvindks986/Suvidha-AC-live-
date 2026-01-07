<?php
namespace App\Http\Middleware;
use Session, Closure, Config;
use Illuminate\Support\Facades\Auth;

class EtpbsAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if(Session::has('admin_login_details') && Session::get('admin_login_details')->role_id == '39'){
            return $next($request)->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');    
        }
        return redirect('/logout');
    }
}
