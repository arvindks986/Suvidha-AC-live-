<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect, Auth, DB, Session, Config;

class SetCurrentElectionDB_Middleware
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
        Config::set('database.default', "mysql");
        Config::set('database.connections.mysql.database', "suvidha_ac_2019_10_e4");
        Config::set('database.connections.mysql.host', "10.247.137.18");
        DB::reconnect('mysql');
        return $next($request);
    }
}
