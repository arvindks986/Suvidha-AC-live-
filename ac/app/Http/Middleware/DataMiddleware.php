<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Session;
use Closure, Request, Redirect, Auth;
use Illuminate\Support\Facades\Route;

class DataMiddleware 
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
	
      if(\App\models\Admin\OfficerModel::remove_concurrent_session()){
        return Redirect::to('logout');
      }

      return $next($request);
    }
}
