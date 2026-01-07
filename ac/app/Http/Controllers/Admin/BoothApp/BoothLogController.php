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
    $request_filter   = Common::get_request_filter($request);
    $ac_no            = $this->ac_no;
    $st_code          = $this->st_code;
    $type             = 1;
    $data['action'] = Common::generate_url("booth-app/table");
    try{
      $data['record'] = 500;
      $data['order_by']       = [];
      $data['group_by']       = [];
      $data['where']          = [];
      $data['i']      = 0;
      $filter         = [
        'st_code'       => $st_code,
        'ac_no'         => $ac_no,
        'restricted_ps' => $this->restricted_ps
      ];
      $data['operators'] = [
        "equalto"  => "=", 
        "notequalto" => "!=", 
        "isnull" => "is null",
        "isnotnull" => "is not null",
        "like" => "like",  
        "greaterthan" => ">",
        "greaterthanequal" => ">=",
        "lessthan" => "<",
        "lessthanequal" => "<=",
        "doublebraces" => "<>",
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
        $type = $request->type;
      }

      $data['type']             = $type;
      $data['user_data']        =  Auth::user();
      $data['heading_title']    = str_replace('_', ' ', $table_name);

      if(!$request->has('connection')){
        $sql                      = DB::connection("spm");
      }else{
        if($request->has('type')){
          if($request->type == '1'){
            $table_name = 'officer_login';
          }else if($request->type == '2'){
            $table_name = 'electors_cdac_other_information';
          }else if($request->type == '3'){
            $table_name = 'electors_cdac';
          }
          $type = $request->type;
        }
        $sql                      = DB::select('*');
      }

      $where_query              = [];
      $query = "SELECT * FROM ".$table_name;
      if($request->has('where')){
        foreach (explode('/', base64_decode($request->where)) as $key => $iterate_where) {
          $query_break = explode(' ', $iterate_where);
          if(array_key_exists($query_break[1], $data['operators']) && count($query_break)>1){
            $data['where'][] = [
              'condition' => $query_break[0],
              'operator'  => $query_break[1],
              'value'     => $query_break[2],
            ];
            if($query_break[1] == 'isnull' || $query_break[1] == 'isnotnull'){
              $where_query[] = $query_break[0]." ".$data['operators'][$query_break[1]];
            }else{
              $where_query[] = $query_break[0]." ".$data['operators'][$query_break[1]]." '".$query_break[2]."'";
            }
          }
        }
        $query .= " WHERE ".implode(' AND ', $where_query);
      }
   
      if($request->has('group_by')){
        $query .= " GROUP BY ".$request->group_by;
        $data['group_by']       = explode(',', $request->group_by);    
      }
      if($request->has('order_by')){
        $query .= " ORDER BY ".$request->order_by ." ASC";
        $data['order_by']       = explode(',', $request->order_by);
      }
      if($request->has('record')){
        $data['record'] = (int)$request->record;
      }
      $page = 0;
      if($request->has('page') && (int)$request->page>0){
        $page = (int)$request->page * $data['record'] - $data['record'];
      }
      $data['page'] = $page;
      $query .= " LIMIT ".$page.",".$data['record'];

      $results          = $sql->select(DB::raw($query));
      $data['sql_query'] = $query;
      $data['results']  = [];
      foreach ($results as $key => $iterate_res) {
        $data['results'][] = (array)$iterate_res;
      }
    }catch(\Exception $e){
      return redirect($data['action'].'?type='.$type);
    }
    return view($this->view.'.get_table_data', $data);

}

}  // end class