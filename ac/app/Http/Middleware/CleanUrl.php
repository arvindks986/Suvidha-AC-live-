<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect;
use Illuminate\Support\Facades\Route;

class CleanUrl
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

            $base_decoded_array = [];
          $array = explode('/', Request::path());

          $second_array = explode('/', Route::getFacadeRoot()->current()->uri());

          foreach ($array as $key => $value) {
            if(!in_array($value, $second_array)){
              $base_decoded_array[] = $value;
              if(!base64_decode($value, true)){
                return Redirect::to('logout');
              }
            }
          }

        

        return $next($request);
    }
}
