<?php
namespace App\Http\Middleware;
use Session;
use Closure;
use Illuminate\Support\Facades\Auth;

class EciExpenditureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    { 
        if(\Auth::user() && \Auth::user()->role_id != '28'){ 

            return redirect('/officer-login');
        }

        return $next($request);
    }
}
