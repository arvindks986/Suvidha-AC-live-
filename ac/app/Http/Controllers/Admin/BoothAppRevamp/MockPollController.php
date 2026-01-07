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
use App\models\Admin\BoothAppRevamp\{PollingStation, StateModel, AcModel, DistrictModel, MockPoll};
use App\Classes\xssClean;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;

//current

class MockPollController extends Controller {

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
      if(in_array(Auth::user()->st_code,$this->allowed_st_code) && in_array(Auth::user()->ac_no,$this->allowed_acs) && in_array(Auth::user()->dist_no,$this->allowed_dist_no)){

      }
      $default_values = Common::get_request_filter($request);
      $this->ac_no    = $default_values['ac_no'];
      $this->st_code  = $default_values['st_code'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id        = $default_values['role_id'];
      $this->filter_role_id = $default_values['filter_role_id'];
      $this->base           = $default_values['base'];
      $this->ps_no          = $default_values['ps_no'];
      $object_setting         = Common::get_allowed_acs($request);
      $this->allowed_st_code  = $object_setting['allowed_st_code'];
      $this->allowed_dist_no  = $object_setting['allowed_dist_no'];
      $this->allowed_acs      = $object_setting['allowed_acs'];
      
      return $next($request);
    });
}


public function state(Request $request){
  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Mock Poll";

  $filter = [
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
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

 
  $grand_mock_poll_start        = 0;
  $grand_mock_poll_result_shown = 0;
  $grand_mock_button_clear      = 0;
  $grand_mock_slip_remove       = 0;


  $states_results = StateModel::get_states($filter);
  foreach ($states_results as $key => $iterate_state) {

    $filter_for_state = [
      'st_code' => $iterate_state->ST_CODE
    ];
    
    $mock_poll_start        = 0;
    $mock_poll_result_shown = 0;
    $mock_button_clear      = 0;
    $mock_slip_remove       = 0;

    $infra_results            = MockPoll::get_mock_poll($filter_for_state);
    if($infra_results && isset($infra_results)){
      $mock_poll_start        = $infra_results['mock_poll_start'];
      $mock_poll_result_shown = $infra_results['mock_poll_result_shown'];
      $mock_button_clear      = $infra_results['button_clear'];
      $mock_slip_remove       = $infra_results['slip_remove'];
    }

    $grand_mock_poll_start        += $mock_poll_start;
    $grand_mock_poll_result_shown += $mock_poll_result_shown;
    $grand_mock_button_clear      += $mock_button_clear;
    $grand_mock_slip_remove       += $mock_slip_remove;

    $data['results'][] = [
      'st_name'   => $iterate_state['ST_NAME'],
      'poll_start'        => $mock_poll_start,
      'poll_result_shown' => $mock_poll_result_shown,
      'button_clear'      => $mock_button_clear,
      'slip_remove'       => $mock_slip_remove,
      'href'              => Common::generate_url('booth-app-revamp/mock-poll/ac').'?st_code='.$iterate_state->ST_CODE
    ];

  }

  $data['results'][]    = [
    'st_name'           => 'Total',
    'poll_start'        => $grand_mock_poll_start,
    'poll_result_shown' => $grand_mock_poll_result_shown,
    'button_clear'      => $grand_mock_button_clear,
    'slip_remove'       => $grand_mock_slip_remove,
    'href'              => "javascript::void(0)"
  ];

  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/mock-poll");
  $form_filter_array = [
    'st_code'     => true,
    'dist_no'     => false,
    'ac_no'       => false, 
    'ps_no'       => false, 
    'designation'     => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);      
  $data['form_filters'] = $form_filters;

  $data['user_data']      = Auth::user();
  return view($this->view.'.mock-poll.state', $data);
}

public function ac(Request $request){

  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Mock Poll";

  $filter = [
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];

  //buttons
  $data['buttons']    = [];
  if($this->role_id == '7' || $this->role_id == '4'){
    $back_href = Common::generate_url('booth-app-revamp/mock-poll').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no;
  }else{
    $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code='.$this->st_code;
  }


  $data['buttons'][]    = [
    'href' => $back_href,
    'name' => 'Back',
    'target' => false,
  ];

  $grand_mock_poll_start        = 0;
  $grand_mock_poll_result_shown = 0;
  $grand_mock_button_clear      = 0;
  $grand_mock_slip_remove       = 0;

  $acs_results = AcModel::get_acs($filter);

  foreach ($acs_results as $key => $iterate_ac) {
    $filter_iterate = [
      'st_code' => $iterate_ac['st_code'],
      'ac_no'   => $iterate_ac['ac_no'],
    ];
    
    $mock_poll_start        = 0;
    $mock_poll_result_shown = 0;
    $mock_button_clear      = 0;
    $mock_slip_remove       = 0;

    $infra_results            = MockPoll::get_mock_poll($filter_iterate);
    if($infra_results && isset($infra_results)){
      $mock_poll_start        = $infra_results['mock_poll_start'];
      $mock_poll_result_shown = $infra_results['mock_poll_result_shown'];
      $mock_button_clear      = $infra_results['button_clear'];
      $mock_slip_remove       = $infra_results['slip_remove'];
    }

    $grand_mock_poll_start        += $mock_poll_start;
    $grand_mock_poll_result_shown += $mock_poll_result_shown;
    $grand_mock_button_clear      += $mock_button_clear;
    $grand_mock_slip_remove       += $mock_slip_remove;

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

    $data['results'][] = [
      'st_name'         => $st_name,
      'ac_name'         => $iterate_ac['ac_no']."-".$ac_name,
      'poll_start'        => $mock_poll_start,
      'poll_result_shown' => $mock_poll_result_shown,
      'button_clear'      => $mock_button_clear,
      'slip_remove'       => $mock_slip_remove,
      'href'            => Common::generate_url('booth-app-revamp/mock-poll/ac/ps').'?st_code='.$iterate_ac['st_code'].'&ac_no='.$iterate_ac['ac_no']
    ];
  }

  $data['results'][]    = [
    'st_name'           => 'Total',
    'ac_name'           => '',
    'poll_start'        => $grand_mock_poll_start,
    'poll_result_shown' => $grand_mock_poll_result_shown,
    'button_clear'      => $grand_mock_button_clear,
    'slip_remove'       => $grand_mock_slip_remove,
    'href'              => "javascript::void(0)"
  ];

  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/mock-poll/ac");
  $form_filter_array = [
    'st_code'     => true,
    'dist_no'     => false,
    'ac_no'       => true, 
    'ps_no'       => false, 
    'designation'     => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);      
  $data['form_filters'] = $form_filters;

  $data['user_data']      = Auth::user();
  return view($this->view.'.mock-poll.ac', $data);

}


public function ps(Request $request){
   $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Mock Poll";

  $filter = [
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];

  //buttons
  $data['buttons']    = [];
  if($this->role_id == '7' || $this->role_id == '4'){
    $back_href = Common::generate_url('booth-app-revamp/mock-poll/ac').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no;
  }else{
    $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code='.$this->st_code;
  }
  $data['buttons'][]    = [
    'href' => $back_href,
    'name' => 'Back',
    'target' => false,
  ];

  $grand_mock_poll_start        = 0;
  $grand_mock_poll_result_shown = 0;
  $grand_mock_button_clear      = 0;
  $grand_mock_slip_remove       = 0;

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

    $mock_poll_start        = 0;
    $mock_poll_result_shown = 0;
    $mock_button_clear      = 0;
    $mock_slip_remove       = 0;

    $infra_results            = MockPoll::get_mock_poll($filter_iterate);
    if($infra_results && isset($infra_results)){
      $mock_poll_start        = $infra_results['mock_poll_start'];
      $mock_poll_result_shown = $infra_results['mock_poll_result_shown'];
      $mock_button_clear      = $infra_results['button_clear'];
      $mock_slip_remove       = $infra_results['slip_remove'];
    }

    $grand_mock_poll_start        += $mock_poll_start;
    $grand_mock_poll_result_shown += $mock_poll_result_shown;
    $grand_mock_button_clear      += $mock_button_clear;
    $grand_mock_slip_remove       += $mock_slip_remove;

    $data['results'][] = [
      'st_name'         => $st_name,
      'ac_name'         => $iterate_p_s['AC_NO']."-".$ac_name,
      'ps_name'         => $iterate_p_s['PS_NO'].'-'.$poll_station_name,
      'poll_start'        => $mock_poll_start,
      'poll_result_shown' => $mock_poll_result_shown,
      'button_clear'      => $mock_button_clear,
      'slip_remove'       => $mock_slip_remove,
      'href'            => Common::generate_url('booth-app-revamp/mock-poll/ac/ps').'?st_code='.$iterate_p_s['ST_CODE'].'&ac_no='.$iterate_p_s['AC_NO']
    ];
  }

  $data['results'][]    = [
    'st_name'           => 'Total',
    'ac_name'           => '',
    'ps_name'           => '',
    'poll_start'        => $grand_mock_poll_start,
    'poll_result_shown' => $grand_mock_poll_result_shown,
    'button_clear'      => $grand_mock_button_clear,
    'slip_remove'       => $grand_mock_slip_remove,
    'href'              => "javascript::void(0)"
  ];

  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/mock-poll/ac/ps");
  $form_filter_array = [
    'st_code'     => true,
    'dist_no'     => false,
    'ac_no'       => true, 
    'ps_no'       => true, 
    'designation'     => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);      
  $data['form_filters'] = $form_filters;

  $data['user_data']      = Auth::user();
  return view($this->view.'.mock-poll.ps', $data);

}

}  // end class