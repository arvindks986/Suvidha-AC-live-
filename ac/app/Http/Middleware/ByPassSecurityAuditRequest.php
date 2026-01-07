<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect, Crypt;
use Illuminate\Support\Facades\Route;

class ByPassSecurityAuditRequest
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

        $status = false;
        foreach($request->except(['_token']) as $key => $value){
          if(!preg_match('/^[a-zA-Z0-9-_ ]+$/', $value)){
            $status = true;
          }else{
            $status = false;
          }
        }
        if($status){
          return Redirect::to('logout');
        }

        return $next($request);
    }
}
