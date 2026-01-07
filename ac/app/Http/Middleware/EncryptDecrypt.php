<?php

namespace App\Http\Middleware;
use Illuminate\Contracts\Encryption\DecryptException;
use Closure,Response,Redirect,Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;

class EncryptDecrypt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {


        $input = $request->all();
        array_walk_recursive($input, function(&$input) {
             $input=Crypt::encrypt($input);
            
        });
        $request->merge($input);
        return $next($request);
       
    }
}
