<?php
namespace App\Http\Controllers\Db_Connectivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ConnectionDb extends Controller
{

  public function index()
  {  
    try {
      dd(DB::connection("boothapp")->table("voter_info")->limit(1)->get());
    } catch (\Exception $e) {
      die("Could not connect to the database.  Please check your configuration. error:" . $e );
    }
  }

}
