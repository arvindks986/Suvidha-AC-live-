<?php

namespace App\Http\Middleware;

use Closure;
//INCLUDING CLASSES
use App\Classes\Secure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SetDB_API_Middleware
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

      $const_type = $m_ele_his->const_type;
      if (!empty($m_ele_his)) {
        if ($const_type == 'PC') {
          $db_name = $m_ele_his->db_name;
          Config::set('database.default', "mysql");
          #Config::set('database.connections.mysql.database', $db_name);
         /*  Config::set('database.connections.mysql.write.host', env('DB_HOST_READ', ''));
          Config::set('database.connections.mysql.read.host', env('DB_HOST_WRITE', '')); */          
          DB::reconnect('mysql');
        } else if ($const_type == 'AC') {
          $db_name = $m_ele_his->db_name;
          Config::set('database.default', "mysql");
          #Config::set('database.connections.mysql.database', $db_name);
	    /* Config::set('database.connections.mysql.read.host', env('DB_HOST_READ', ''));
          Config::set('database.connections.mysql.write.host', env('DB_HOST_WRITE', '')); */
          DB::reconnect('mysql');
        }
      } else {
        return response()->json(['code' => 200, 'status' => 1, 'success' => true, 'message' => 'DB details not found']);
      }
    }
    return $next($request);
  }
}
