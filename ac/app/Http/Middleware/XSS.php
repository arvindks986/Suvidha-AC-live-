<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect, Auth, DB, Session, Config;
use Illuminate\Support\Facades\Route;
//INCLUDING CLASSES
use App\Classes\xssClean;

class XSS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function __construct() {
        $this->xss = new xssClean();
    }
    
    public function handle($request, Closure $next)
    {
        $xss = new xssClean;
        $input = $request->all();
        array_walk_recursive($input, function(&$input) {
            //$input = strip_tags($input);
            $input =  $this->xss->clean_input($input);
           //dd($input);
        });
        $request->merge($input);
        return $next($request);
    }
}
