<?php namespace App\Http\Controllers\Candidate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class CommonController extends Controller {
  
  public static function get_header($request){
      $data                   = [];
      return view('candidate.common.header', $data);
  }

  public static function get_footer($request){
      $data                   = [];
      return view('candidate.common.footer', $data);
  }

}  // end class