<?php namespace App\Http\Controllers\Admin\Etpbs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session, Common;
use App\commonModel;
   
class DashboardController extends Controller
{
  public function __construct(Request $request){

  }

   public function dashboard(Request $request){
      $data = [];
      $data['user_data'] = Auth::user();
      return view('admin.etpbs.dashboard', $data);
   }
}