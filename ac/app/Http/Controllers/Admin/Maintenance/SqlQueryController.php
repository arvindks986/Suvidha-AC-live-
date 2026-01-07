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

class SqlQueryController extends Controller {

  public $folder        = "maintenance";
  public $view          = "admin.maintenance";
  public $action        = "";
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

  public function get_table_data(Request $request){
    $data                   = [];
    $data['menus']          = Common::header($request);
    $database               = NULL;
    $table                  = NULL;
    $table_name             = '';
    $data['role_id']        = $this->role_id;
    $data['databases']        = [];
    $data['decrypt_database'] = NULL;
    $data['tables']           = [];
    $data['decrypt_table']    = NULL;
    $data['record']         = 500;
    $data['page']           = 1;
    $data['order_by']       = [];
    $data['group_by']       = [];
    $data['where']          = [];
    $data['i']              = 0;
    $data['results']        = [];
    $data['operators']      = [
      "equalto"     => "=", 
      "notequalto"  => "!=", 
      "isnull"      => "is null",
      "isnotnull"   => "is not null",
      "like"        => "like",  
      "greaterthan" => ">",
      "greaterthanequal" => ">=",
      "lessthan"      => "<",
      "lessthanequal" => "<=",
      "doublebraces"  => "<>",
    ];

    

      $request_filter         = Common::get_request_filter($request);
      $ac_no                  = $this->ac_no;
      $st_code                = $this->st_code;
      $type                   = 1;
      $data['action']         = Common::generate_url("table");
      $databases              = Common::get_election_data();
      $data['databases'][] =  [
        'value' => encrypt_string('spm'),
        'name'  => strtoupper(str_replace(['_','suvidha'],['-','default'],'spm')),
        'db_name' => 'spm'
      ];
      $data['databases'][] =  [
        'value' => encrypt_string('booth_revamp'),
        'name'  => strtoupper(str_replace(['_','suvidha'],['-','default'],'booth_revamp')),
        'db_name' => 'booth_revamp'
      ];
      foreach($databases as $iterate_database){
        $data['databases'][] =  [
          'value' => encrypt_string($iterate_database['db_name']),
          'name'  => strtoupper(str_replace(['_','suvidha'],['-','default'], $iterate_database['name'])),
          'db_name' => $iterate_database['db_name']
        ];
      }

      if($request->has('database')){
        $database = $request->database;
      }

      if($request->has('table')){
        $table = $request->table;
      }

    
      if($database){
        $database_name            = decrypt_string($database);
        $data['decrypt_database'] = decrypt_string($database);
     
        if(in_array($database_name, ['spm','booth_revamp'])){
          $db_object = DB::connection($database_name);
        }else{
          Config::set('database.connections.mysql.database', $database_name);
          DB::reconnect('mysql');
          DB::purge('mysql');
          DB::setDefaultConnection('mysql');
          $db_object = DB::connection("mysql");
        }

        $fetch_tables_from_db = $db_object->select(DB::raw("SELECT table_name FROM information_schema.tables WHERE table_schema = '".$database_name."'"));
        foreach ($fetch_tables_from_db as $key => $iterate_tables) {
          $data['tables'][] = [
            'name'            => str_replace('_',' ',$iterate_tables->table_name),
            'value'           => encrypt_string($iterate_tables->table_name),
            'oringinal_name'  => $iterate_tables->table_name
          ];
        }
     
        $filter         = [
          'st_code'       => $st_code,
          'ac_no'         => $ac_no
        ];
         

        if($table){

          $table_name     = decrypt_string($request->table);
          $where_query    = [];
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

          $results          = $db_object->select(DB::raw($query));
          $data['sql_query'] = $query;
          foreach ($results as $key => $iterate_res) {
            $data['results'][] = (array)$iterate_res;
          }

          $data['decrypt_table']    = decrypt_string($table);

        }
      }

      $data['database']         = $database;
      $data['table']            = $table;
      
      $data['user_data']        =  Auth::user();
      $data['heading_title']    = str_replace('_', ' ', $table_name);

    try{}catch(\Exception $e){
      return redirect($data['action'].'?type='.$type);
    }
    return view($this->view.'.get_table_data', $data);

  }

}  // end class