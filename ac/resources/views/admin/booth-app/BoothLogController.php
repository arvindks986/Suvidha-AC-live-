<?php 
namespace App\Http\Controllers\Admin\BoothApp;
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

class BoothLogController extends Controller {

  public $folder        = "booth-app";
  public $view          = "admin.booth-app";
  public $action        = "booth-app";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $role_id       = 0;
  public $base          = 'roac';
  public $restricted_ps = ['91','99','128','129','189'];

  public function __construct(){
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      $request_filter = Common::get_request_filter($request);
      $this->ac_no      = $request_filter['ac_no'];
      $this->st_code    = $request_filter['st_code'];
      $this->role_id    = $request_filter['role_id'];
      return $next($request);
    });
  }

  public function get_table_data(Request $request){
    $data                   = [];
    $data['role_id']        = $this->role_id;
    $request_filter = Common::get_request_filter($request);
    $ac_no          = $this->ac_no;
    $st_code        = $this->st_code;

    $filter = [
      'st_code' => $st_code,
      'ac_no'   => $ac_no,
      'restricted_ps' => $this->restricted_ps
    ];
    $table_name = "voter_info_poll_status";
    if($request->has('type')){
      if($request->type == '1'){
        $table_name = 'voter_info_poll_status';
      }else if($request->type == '2'){
        $table_name = 'polling_start_end_statics';
      }else if($request->type == '3'){
        $table_name = 'voter_info';
      }
    }
    $data['user_data']  =  Auth::user();
    $data['heading_title']    = str_replace('_', ' ', $table_name);
    $sql          = DB::connection("spm")->table($table_name);
    if($request->has('order_by')){
      $sql->orderBy(DB::raw(str_replace('-',',',$request->order_by)),'ASC');
    }
    if($request->has('group_by')){
      $sql->groupBy(DB::raw(str_replace('-',',',$request->group_by)));
    }
    $results = $sql->paginate(1000)->toArray();
    $data['results'] = [];
    foreach ($results['data'] as $key => $iterate_res) {
      $data['results'][] = (array)$iterate_res;
    }
    return view($this->view.'.get_table_data', $data);

}

}  // end class