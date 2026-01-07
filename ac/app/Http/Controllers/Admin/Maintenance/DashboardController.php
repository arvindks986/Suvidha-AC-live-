<?php 
namespace App\Http\Controllers\Admin\Maintenance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session, Common;
use App\commonModel;  
use App\models\Admin\StateModel;
use App\models\Admin\AcModel;

class DashboardController extends Controller {

  public $folder        = "maintenance";
  public $view          = "admin.maintenance";
  public $action        = "maintenance";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $role_id       = 0;
  public $base          = '';

  public function __construct(){
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      $request_filter   = Common::get_request_filter($request);
      $this->ac_no      = $request_filter['ac_no'];
      $this->st_code    = $request_filter['st_code'];
      $this->role_id    = $request_filter['role_id'];
      return $next($request);
    });
  }

  public function get_dashboard(Request $request){

    $data               = [];
    $data['menus']      = Common::header($request);
    $data['user_data']  = Auth::user();
    return view($this->view.'.dashboard', $data);

  }

}  // end class