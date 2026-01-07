<?php

namespace App\Http\Middleware;

use App\Classes\Secure;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class UatMiddleware
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
    if (!empty($request->election_id)) {

      if (is_numeric($request->election_id)) {
        $election_id = $request->election_id;
      } else {
        $cipher = new Secure();
        $election_id = $cipher->encrypt_decrypt('decrypt', $request->election_id);
      }

      $m_ele_his = DB::connection('mysql')->table('m_election_history')->where('id', '=', $election_id)->first();
      if (!empty($m_ele_his)) {
        $const_type = $m_ele_his->const_type;
        if ($const_type == 'AC') {
          Config::set('database.default', "suivhdaaclivetest");
          DB::reconnect('suivhdaaclivetest');
        } else if ($const_type == 'PC') {
          Config::set('database.default', "suvidhapc");
          DB::reconnect('suvidhapc');
          
        }
      } else {
        return response()->json(['code' => 200, 'status' => 1, 'success' => true, 'message' => 'DB details not found']);
      }
    }
    return $next($request);
  }
}
