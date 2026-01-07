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
use App\models\Admin\BoothAppRevamp\{PollingStation, StateModel, AcModel, DistrictModel, IncidentStatistics};
use App\Classes\xssClean;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use \PDF;

//current

class IncidentController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $phase_no      = NULL;
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
  public $cache           = true;
  public $request_param   = '';

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

public function get_incidents(Request $request){
  if($this->ac_no && $this->st_code){
    return $this->ps($request);
  }else if($this->st_code){
    return $this->ac($request);
  }else{
    return $this->state($request);
  }
}

public function state(Request $request){

  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Incident States";
	
  $filter = [
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
    'phase_no'        => $this->phase_no,
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

  $grand_incident = 0;
  $states_results = StateModel::get_states($filter);
 
  foreach ($states_results as $key => $iterate_state) {

    $filter_for_state = [
      'st_code' => $iterate_state->ST_CODE
    ];
    $total_incident = IncidentStatistics::get_incident_count($filter_for_state);
    $grand_incident    += $total_incident;

    $data['results'][] = [
      'st_name'         => $iterate_state['ST_NAME'],
      'total_incident'  => $total_incident,
      'href'            => Common::generate_url('booth-app-revamp/incident/ac').'?st_code='.$iterate_state->ST_CODE
    ];

  }

  $data['results'][]    = [
    'st_name'           => 'Total',
    'total_incident'    => $grand_incident,
    'href'              => "javascript::void(0)"
  ];

  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/incident");
  $form_filter_array = [
    'phase_no'      => true,
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
  return view($this->view.'.incident.state', $data);
}

public function ac(Request $request){

  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Incident States";

  $filter = [
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
    'phase_no'           => $this->phase_no,
  ];

  //buttons
  $data['buttons']    = [];
  if($this->role_id == '7' || $this->role_id == '4'){
    $back_href = Common::generate_url('booth-app-revamp/incident').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no;
  }else{
    $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code='.$this->st_code;
  }


  $data['buttons'][]    = [
    'href' => $back_href,
    'name' => 'Back',
    'target' => false,
  ];

  $grand_incident = 0;

  $acs_results = AcModel::get_acs($filter);

  foreach ($acs_results as $key => $iterate_ac) {
    $filter_iterate = [
      'st_code' => $iterate_ac['st_code'],
      'ac_no'   => $iterate_ac['ac_no'],
    ];
    $total_incident = IncidentStatistics::get_incident_count($filter_iterate);
    $grand_incident    += $total_incident;

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
      'total_incident'  => $total_incident,
      'href'            => Common::generate_url('booth-app-revamp/incident/ac/ps').'?st_code='.$iterate_ac['st_code'].'&ac_no='.$iterate_ac['ac_no'].'&phase_no='.$this->phase_no
    ];
  }

  $data['results'][]    = [
    'st_name'           => 'Total',
    'ac_name'           => '',
    'total_incident'    => $grand_incident,
    'href'              => "javascript::void(0)"
  ];

  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/incident/ac");
  $form_filter_array = [
    'phase_no'      => true,
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
  return view($this->view.'.incident.ac', $data);

}


public function ps(Request $request){
   $data                   = [];
  $data['buttons']        = [];
  $data['results']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Incident States";

  $filter = [
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
    'phase_no'           => $this->phase_no,
  ];

  //buttons
  $data['buttons']    = [];
  if($this->role_id == '7' || $this->role_id == '4'){
    $back_href = Common::generate_url('booth-app-revamp/incident/ac').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no;
  }else{
    $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code='.$this->st_code;
  }
  $data['buttons'][]    = [
    'href' => $back_href,
    'name' => 'Back',
    'target' => false,
  ];
  $data['buttons'][]    = [
    'href' => Common::generate_url('booth-app-revamp/incident/ac/ps/download')."?".$this->request_param,
    'name' => 'Download PDF',
    'target' => true,
  ];

  $grand_incident = 0;

  $incidents_object = IncidentStatistics::get_incidents($filter);

  foreach ($incidents_object as $key => $iterate_p_s) {

    $filter_iterate = array_merge($filter,['ps_no' => $iterate_p_s->ps_no]);

    $poll_station_name = '';
    $poll_station = PollingStation::get_polling_station([
      'st_code' => $this->st_code,
      'ac_no'   => $this->ac_no,
      'ps_no'   => $request->ps_no
    ]);
    if($poll_station){
      $poll_station_name              = $poll_station['PS_NAME_EN'];
    }

    $st_name = '';
    $state_object = StateModel::get_state_by_code($iterate_p_s->st_code);
    if($state_object){
      $st_name  = $state_object['ST_NAME'];
    }

    $ac_name = '';
    $ac_object = AcModel::get_ac(['state' => $iterate_p_s->st_code, 'ac_no' => $iterate_p_s->ac_no]);
    if($ac_object){
      $ac_name  = $ac_object['ac_name'];
    }

    $data['results'][] = [
      'st_name'         => $st_name,
      'ac_name'         => $iterate_p_s->ac_no."-".$ac_name,
      'ps_name'         => $iterate_p_s->ps_no.'-'.$poll_station_name,
      'incident_detail'  => $iterate_p_s->incident_detail,
      'incident_type'   => $iterate_p_s->incident_type,
      'description'     => $iterate_p_s->description,
      'created_at'      => date('d-m-Y H:i:s',strtotime($iterate_p_s->created_at)),
      'href'            => Common::generate_url('booth-app-revamp/incident/ac/ps').'?st_code='.$iterate_p_s->st_code.'&ac_no='.$iterate_p_s->ac_no.'&phase_no='.$this->phase_no
    ];
  }

  if($request->has('is_excel')){
    return $data;
  }

  //form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/incident/ac/ps");
  $form_filter_array = [
    'phase_no'      => true,
    'st_code'       => true,
    'dist_no'       => false,
    'ac_no'         => true, 
    'ps_no'         => true, 
    'designation'     => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);      
  $data['form_filters'] = $form_filters;

  $data['user_data']      = Auth::user();
  return view($this->view.'.incident.ps', $data);

}

public function ps_download(Request $request){
  $data = [];
  if($this->phase_no){
    $data['phase_no'] = $this->phase_no;
  }
  $request->merge(['is_excel' => 1]);
  $object = $this->ps($request);
  $data['total_incidents'] = count($object['results']);
  $data['results']        = $object['results'];
  $name_excel             = 'Incident Report';
  $data['heading_title']  = $name_excel;
  $data['user_data']      = Auth::user();
  $pdf = PDF::loadView($this->view.'.incident.ps_pdf',$data);
  return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');

}


}  // end class