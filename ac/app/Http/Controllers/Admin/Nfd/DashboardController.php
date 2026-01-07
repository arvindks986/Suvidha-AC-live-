<?php
namespace App\Http\Controllers\Admin\Nfd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use DB, Validator, Config, Session, Common;
use App\commonModel;  
use App\models\Admin\StateModel;
use App\models\Admin\AcModel;

class DashboardController extends Controller
{

    public $folder        = "nfd";
    public $view          = "admin.nfd";
    public $action        = "nfd";
    public $ac_no         = NULL;
    public $st_code       = NULL;
    public $role_id       = 0;
    public $base          = '';

    public function __construct(){
        $this->middleware(function ($request, $next) {
          $request_filter   = Common::get_request_filter($request);
          $this->ac_no      = $request_filter['ac_no'];
          $this->st_code    = $request_filter['st_code'];
          $this->role_id    = $request_filter['role_id'];
          return $next($request);
        });
    }

    public function dashboard(Request $request){
        $data               = [];
        $data['user_data']  = Auth::user();
        return view('admin.nfd.dashboard', $data);
    }

}