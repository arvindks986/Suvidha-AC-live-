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
use App\models\Admin\BoothAppRevamp\{PollingStation, StateModel, AcModel, DistrictModel, ProDiaryFinal,TblAnalyticsDashboardModel};
use App\Classes\xssClean;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class PollMaterialController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $filter_role_id  = NULL;
  public $base            = 'roac';
  public $restricted_ps   = [];
  public $no_allowed_po   = 2;
  public $no_allowed_blo  = 2;
  public $no_allowed_pro  = 2;
  public $no_allowed_sm  = 2;
  public $allowed_acs     = [];
  public $allowed_dist_no = [];
  public $allowed_st_code = [];
  public $cache = true;

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
      $this->ps_no          = $default_values['ps_no'];      
      return $next($request);
    });
}


public function state(Request $request){
  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Poll Material";
  $filter = [
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
  ];
  //buttons
  $data['buttons']    = [];
  if($this->role_id == '7'){
    $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code='.$this->st_code;
  }else{
    $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code='.$this->st_code;
  }
  $data['buttons'][]    = [
    'href' => $back_href,
    'name' => 'Back',
    'target' => false,
  ];
  $grand_received = 0;
  $grand_submited = 0;
  $states_results = StateModel::get_states($filter);
  foreach ($states_results as $key => $iterate_state) {
    $filter_for_state = [
      'st_code' => $iterate_state->ST_CODE
    ];
    $obj_result     = TblAnalyticsDashboardModel::total_material_count($filter_for_state);
    $total_received = $obj_result['total_received'];
    $total_submited = $obj_result['total_submited'];
    $grand_received += $total_received;
    $grand_submited += $total_submited;
    $data['results'][] = [
      'st_name'         => $iterate_state['ST_NAME'],
      'total_received'  => $total_received,
      'total_submited'  => $total_submited,
      'href'              => Common::generate_url('booth-app-revamp/poll-material/ac').'?st_code='.$iterate_state->ST_CODE
    ];
  }

  $data['results'][]    = [
    'st_name'           => 'Total',
    'total_received'    => $grand_received,
    'total_submited'    => $grand_submited,
    'href'              => "javascript::void(0)"
  ];

  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/poll-material");
  $form_filter_array = [
    'st_code'     => true,
    'dist_no'     => false,
    'ac_no'       => false, 
    'ps_no'       => false, 
    'designation' => false,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);      
  $data['form_filters'] = $form_filters;
  $data['user_data']    = Auth::user();
  return view($this->view.'.poll-material.state', $data);
}

public function ac(Request $request){
  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Poll Material";
  $filter = [
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
  ];
  //buttons
  $data['buttons']    = [];
  if($this->role_id == '7' || $this->role_id == '4'){
    $back_href = Common::generate_url('booth-app-revamp/poll-material').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no;
  }else{
    $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code='.$this->st_code;
  }
  $data['buttons'][] = [
    'href' => $back_href,
    'name' => 'Back',
    'target' => false,
  ];
  $grand_received = 0;
  $grand_submited = 0;
  $acs_results = AcModel::get_acs($filter);
  foreach ($acs_results as $key => $iterate_ac) {
    $filter_iterate = [
      'st_code' => $iterate_ac['st_code'],
      'ac_no'   => $iterate_ac['ac_no'],
    ];
    $st_name = '';
    $state_object = StateModel::get_state_by_code($iterate_ac['st_code']);
    if($state_object){
      $st_name  = $state_object['ST_NAME'];
    }
    $ac_name = '';
    $ac_object = AcModel::get_ac(['state' => $iterate_ac['st_code'], 'ac_no' => $iterate_ac['ac_no']]);
    if($ac_object){
      $ac_name  = $ac_object['ac_name'];
    }
    $obj_result     = TblAnalyticsDashboardModel::total_material_count($filter_iterate);
    $total_received = $obj_result['total_received'];
    $total_submited = $obj_result['total_submited'];
    $grand_received += $total_received;
    $grand_submited += $total_submited;
    $data['results'][] = [
      'st_name'         => $st_name,
      'ac_name'         => $iterate_ac['ac_no']."-".$ac_name,
      'total_received'  => $total_received,
      'total_submited'  => $total_submited,
      'href'            => Common::generate_url('booth-app-revamp/poll-material/ac/ps').'?st_code='.$iterate_ac['st_code'].'&ac_no='.$iterate_ac['ac_no']
    ];
  }
  $data['results'][]    = [
    'st_name'           => 'Total',
    'ac_name'           => '',
    'total_received'    => $grand_received,
    'total_submited'    => $grand_submited,
    'href'              => "javascript::void(0)"
  ];
  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/poll-material/ac");
  $form_filter_array = [
    'st_code'     => true,
    'dist_no'     => false,
    'ac_no'       => true, 
    'ps_no'       => false, 
    'designation'     => false,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);      
  $data['form_filters'] = $form_filters;
  $data['user_data']      = Auth::user();
  return view($this->view.'.poll-material.ac', $data);
}


public function ps(Request $request){
  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Mock Poll";

  $filter = [
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no
  ];

  //buttons
  $data['buttons']    = [];
  if($this->role_id == '7' || $this->role_id == '4'){
    $back_href = Common::generate_url('booth-app-revamp/poll-material/ac').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no;
  }else{
    $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code='.$this->st_code;
  }
  $data['buttons'][]    = [
    'href' => $back_href,
    'name' => 'Back',
    'target' => false,
  ];

  $grand_received = 0;
  $grand_submited = 0;

  $polling_stations = PollingStation::get_polling_stations($filter);

  foreach ($polling_stations as $key => $iterate_p_s) {
    $filter_iterate = array_merge($filter,['ps_no' => $iterate_p_s['PS_NO']]);
    $st_name = '';
    $state_object = StateModel::get_state_by_code($iterate_p_s['ST_CODE']);
    if($state_object){
      $st_name  = $state_object['ST_NAME'];
    }
    $ac_name = '';
    $ac_object = AcModel::get_ac(['state' => $iterate_p_s['ST_CODE'], 'ac_no' => $iterate_p_s['AC_NO']]);
    if($ac_object){
      $ac_name  = $ac_object['ac_name'];
    }
    $poll_station_name = $iterate_p_s['PS_NAME_EN'];
    $obj_result     = TblAnalyticsDashboardModel::total_material_count($filter_iterate);
    $total_received = $obj_result['total_received'];
    $total_submited = $obj_result['total_submited'];
    $grand_received += $total_received;
    $grand_submited += $total_submited;
    $data['results'][] = [
      'st_name'         => $st_name,
      'ac_name'         => $iterate_p_s['AC_NO']."-".$ac_name,
      'ps_name'         => $iterate_p_s['PS_NO'].'-'.$poll_station_name,
      'total_received'  => $total_received,
      'total_submited'  => $total_submited,
      'href'            => "javascript::void()"
    ];
  }

  $data['results'][]    = [
    'st_name'           => 'Total',
    'ac_name'           => '',
    'ps_name'           => '',
    'total_received'    => $grand_received,
    'total_submited'    => $grand_submited,
    'href'              => "javascript::void(0)"
  ];

  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/poll-material/ac/ps");
  $form_filter_array = [
    'st_code'     => true,
    'dist_no'     => false,
    'ac_no'       => true, 
    'ps_no'       => true, 
    'designation' => false,
  ];
  $form_filters         = Common::get_form_filters($form_filter_array, $request);      
  $data['form_filters'] = $form_filters;
  $data['user_data']    = Auth::user();
  return view($this->view.'.poll-material.ps', $data);
}

}  // end class