<?php 
namespace App\Http\Controllers\Admin\BoothAppRevamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session, Response;
use App\commonModel;  
use App\models\Admin\BoothAppRevamp\{PollingStation, StateModel, AcModel, DistrictModel, PollingStationLocationModel, PollingStationLocationToPsModel, PollingStationOfficerModel};
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class PollingStationLocationController extends Controller {

  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $phase_no      = NULL;
  public $filter_role_id  = NULL;
  public $no_allowed_po   = 2;
  public $no_allowed_blo  = 2;
  public $no_allowed_pro  = 2;
  public $no_allowed_sm   = 2;
  public $cache           = true;

  public function __construct(Request $request){
      $this->commonModel  = new commonModel();
      $this->middleware(function ($request, $next) {

      $default_values = Common::get_request_filter($request);
      $this->ac_no    = $default_values['ac_no'];
      $this->st_code  = $default_values['st_code'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id        = $default_values['role_id'];
      $this->filter_role_id = $default_values['filter_role_id'];
      $this->base           = $default_values['base'];
      $this->ps_no           = $default_values['ps_no'];
      $this->phase_no        = $default_values['phase_no'];

      $this->request_param   = http_build_query([
        'st_code' => $this->st_code,
        'ac_no' => $this->ac_no,
        'dist_no' => $this->dist_no,
        'ps_no' => $this->ps_no,
        'phase_no' => $this->phase_no
      ]);
      return $next($request);
    });
  }

  public function location(Request $request){
    
      $data = [];
      $request_array = [];

      $filter_ps = [
        'st_code'   => $this->st_code,
        'ac_no'     => $this->ac_no,
      ];

      
      $data['heading_title'] = '';

      if($this->st_code){
        $state_object = StateModel::get_state_by_code($this->st_code);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      if($this->ac_no){
        $ac_object = AcModel::get_ac(['state' => $this->st_code, 'ac_no' => $this->ac_no]);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
      }
      $data['filter_buttons'] = $title_array;
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['action']     = Common::generate_url($this->action.'/location/post');

      $data['results']  = [];
      $assigned_ps      = [];
      $results = PollingStationLocationModel::get_locations($filter_ps);
      foreach($results as $result){
          $data['results'][] = [
            'name'      => $result['name'],
            'ps_no'     => $result['ps_no'],
            'edit_id'   => encrypt_string($result['id'])
          ];
          $assigned_ps = array_merge($assigned_ps, explode(',', $result['ps_no']));
      }

      $assigned_ps = array_unique($assigned_ps);

      $data['polling_stations'] = [];
      $results    =   PollingStation::get_polling_stations($filter_ps);
      foreach ($results as $result) {
        $is_assigned = false;
        if(in_array($result['PS_NO'], $assigned_ps)){
          $is_assigned = true;
        }
        $data['polling_stations'][] = [
          'ps_no'   => $result['PS_NO'],
          'ps_name' => $result['PS_NAME_EN'],
          'is_assigned' => $is_assigned
        ];
      }

      $data['user_data']  =  Auth::user();
      return view($this->view.'.polling-station-location', $data);
    try{
    }catch(\Exception $e){
      return Redirect::to($this->base.'/dashboard');
    }
  }
  
  public function post_location(Request $request){
   
    $rules = [
      'name'   => 'required',
      'ps_no'  => 'required|array'
    ];

    try{
      if($request->has('id')){
        $id           = decrypt_string($request->id);
        $request->merge(['id' => $id]);
      }
    }catch(\Exception $e){
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
   
    $messages = [
      'ps_no.array'     => "Please select a PS",
      'ps_no.required'  => "Please select a PS"
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails())
    {
        return Response::json([
          'success' => false,
          'errors'  => $validator->getMessageBag()->toArray()
        ]);
    }

    $merge            = [];
    $merge['st_code'] = $this->st_code;
    $merge['dist_no'] = $this->dist_no;
    $merge['ac_no']           = $this->ac_no;
    $merge['ps_no_string']    = implode(',',$request->ps_no);
    $request->merge($merge);

    $exist_ps = PollingStationLocationToPsModel::validate_ps($request->all());
    if(count($exist_ps)>0){
      $exist_ps_no = [];
      foreach($exist_ps as $ex_ps){
        $exist_ps_no[] = $ex_ps->ps_no;
      }
      usort($exist_ps_no,function($a,$b){
        return $a - $b;
      });
      return Response::json([
        'success' => false,
        'errors'  => ["ps_no" => ["Polling Station with ps no ".implode(', ',$exist_ps_no)." is already asigned to other location."]]
      ]);
    }

    if(isset($id)){
      $is_assign = PollingStationOfficerModel::where('location_id', $id)->count();
      if($is_assign){
        return Response::json([
          'success' => false,
          'errors'  => ['warning'  => "The location is already assigned to BLO and not editable."]
        ]);
      }
    }

    
      $result   = PollingStationLocationModel::add_location($request->all());
      PollingStationLocationToPsModel::delete_location_to_ps($result->id);
      foreach($request->ps_no as $key){
        PollingStationLocationToPsModel::add_location_to_ps([
          'ps_no'       => $key,
          'ac_no'       => $merge['ac_no'],
          'st_code'     => $merge['st_code'],
          'location_id' => $result->id,
          'created_by'  => Auth::id()
        ]);
      }
    DB::beginTransaction();
    try{}catch(\Exception $e){
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();

    Session::flash('status',1);
    Session::flash('flash-message',"Location has been added successfully.");
    return Response::json([
        'success' => true
    ]);
  }

}