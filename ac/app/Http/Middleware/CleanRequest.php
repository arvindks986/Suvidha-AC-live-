<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect;
use Illuminate\Support\Facades\Route;

class CleanRequest
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

        $status = true;
        foreach($request->except(['_token']) as $key => $value){
          if(in_array($key, ['from','to'])){
            if(!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$value)) {
              $status = false;
            }
          }else if($key=='ccode'){
            if(!base64_decode($value, true)){
              $status = false;
            }
          }else{
            if(!preg_match('/^[a-zA-Z0-9]+$/', $value)){
                    $status = false;
                }
          }
        }
        if(!$status){
          return Redirect::to('logout');
        }

        return $next($request);
    }
}
