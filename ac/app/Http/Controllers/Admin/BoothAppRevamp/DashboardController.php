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
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, PsSectorOfficer, TblPollSummaryModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, JsonFile, TblPwdVoterModel, TblProDiaryModel, InfraMapping, MockPoll, IncidentStatistics,PhaseModel, PollingStationLocationModel, PollingStationLocationToPsModel, ProDiaryFinal, TblAnalyticsDashboardModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;
use App\Events\Admin\BoothAppRevamp\PusherEvent;
use Pusher\Pusher;

ini_set("memory_limit","850M");
set_time_limit('240');
ini_set("pcre.backtrack_limit", "5000000");

//current
class DashboardController extends Controller {

  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $phase_no      = NULL;
  public $filter_role_id  = NULL;
  public $cache           = true;
  public $filter          = NULL;
  public $age_gap = ["18-25", "26-30", "31-40", "41-50", "51-60", "61-70", "71-80", "81-90", "91-100", "101-150"];

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

      $this->filter = [
        'st_code' => $this->st_code,
        'ac_no' => $this->ac_no,
        //'dist_no' => $this->dist_no,
        'ps_no' => $this->ps_no,
        'phase_no' => $this->phase_no
      ];
      $this->request_param   = http_build_query($this->filter);
      return $next($request);
    });


  }


  public function dashboard(Request $request){
	
    //delete after setting
    $polling_station_results   = PollingStationLocationModel::get();
    foreach($polling_station_results as $itr_ps_res){
      foreach(explode(',',$itr_ps_res['ps_no']) as $key){
        PollingStationLocationToPsModel::firstOrNew([
          'ps_no'       => $key,
          'ac_no'       => $itr_ps_res->ac_no,
          'st_code'     => $itr_ps_res->st_code,
          'location_id' => $itr_ps_res->id
        ])->save();
      }
    }
    //always delete after setting

    $data                         = [];
    $data['buttons']              = [];
    if(config('public_config.google_map_api')){
      $officer_locations           = InfraMapping::get_reached_gis([
        'st_code' => 'S27'
      ]);
      $allpsdata = [];
      foreach ($officer_locations as $iterate_off_location) {
        $designation = '';
        $lat_long = explode(',',$iterate_off_location['ps_location']);
        $name = '';
        $mobile = '';
        $st_name = '';
        $state_object = StateModel::get_state_by_code($iterate_off_location['st_code']);
        if($state_object){
          $st_name = $state_object['ST_NAME'];
        }
        $officer = PollingStationOfficerModel::get_officer([
          'id' => $iterate_off_location['user_id']
        ]);
        if($officer){
          $name   = $officer['name'];
          $mobile = $officer['mobile_number'];
        }
        if($officer = '34'){
          $designation = 'PO';
        }
        if($officer = '35'){
          $designation = 'PRO';
        }
        $ps_name = '';
        $poll_station = PollingStation::get_polling_station([
          'st_code' => $iterate_off_location['st_code'],
          'ac_no'   => $iterate_off_location['ac_no'],
          'ps_no'   => $iterate_off_location['ps_no'],
        ]);
        if($poll_station){
          $ps_name = $poll_station['PS_NO'].'-'.$poll_station['PS_NAME_EN'];
        }
        $ac_name = '';
        $ac_object = AcModel::get_ac(['state' => $iterate_off_location['st_code'], 'ac_no' => $iterate_off_location['ac_no']]);
        if($ac_object){
          $ac_name = $ac_object['ac_name'];
        }
        $allpsdata[] = [
          'lat' => $lat_long[0],
          'lng' => $lat_long[1],
          'ps_no_name'  => $ps_name,
          'st_name'     => $st_name,
          'ac_no_name'  => $iterate_off_location['ac_no'].'-'.$ac_name,
          'designation' => $designation,
          'name'    => $name,
          'mobile'  => $mobile
        ];
      }

      $data['allpsdata'] = json_encode($allpsdata);
    }

    $data['role_id'] = $this->role_id;
    $heading_title   = [];

    if($this->phase_no){
      $heading_title[] = "Phase: <span class='badge badge-info'>".$this->phase_no."</span>";
    }

    if($this->st_code){
      $state_object = StateModel::get_state_by_code($this->st_code);
      if($state_object){
        $heading_title[] = "State: <span class='badge badge-info'>".$state_object['ST_NAME']."</span>";
      }
    }

    if($this->ac_no){
      $ac_object = AcModel::get_ac([
        'state' => $this->st_code,
        'ac_no' => $this->ac_no
      ]);
      if($ac_object){
        $heading_title[] = "AC: <span class='badge badge-info'>".$ac_object['ac_name']."</span>";
      }
    }

    if($this->ps_no){
      $poll_station = PollingStation::get_polling_station([
        'st_code' => $this->st_code,
        'ac_no'   => $this->ac_no,
        'ps_no'   => $this->ps_no
      ]);
      if($poll_station){
        $heading_title[] = "PS No: <span class='badge badge-info'>".$this->ps_no."</span>";
      }
    }

    if(count($heading_title)){
      $data['heading_title'] = "Dashboard - ".implode(" ", $heading_title);
    }else{
      $data['heading_title'] = "Dashboard";
    }


    $data['action']               = url($this->action);
    $dashboard_data               = $this->get_dashboard_data($request);
    $data                         = array_merge($dashboard_data, $data);

    $active_tab                   = 'before';
    if($request->has('tab') && in_array($request->tab,['before','after','pollday'])){
      $active_tab = $request->tab;
    }else if(Session::has('active_tab')){
      $active_tab = Session::get('active_tab');
    }
    Session::put('active_tab', $active_tab);
    $data['active_tab'] = $active_tab;

    $request_array = [];
    if($this->phase_no){
      $request_array[] = "phase_no=".$this->phase_no;
    }

    if($this->st_code){
      $request_array[] = "st_code=".$this->st_code;
    }
    if($this->ac_no){
      $request_array[] = "ac_no=".$this->ac_no;
    }
    if($this->ps_no){
      $request_array[] = "ps_no=".$this->ps_no;
    }

    $request_string = $this->request_param;
    $data['request_string']       = $request_string;
    $data['get_voter_turnout']    = Common::generate_url('booth-app-revamp/get_voter_turnout').'?'.$request_string;
    $data['poll_event_dashboard'] = url("eci/booth-app-revamp/poll-event-dashboard").'?'.$request_string;
    $data['referesh_page_url']    = Common::generate_url('booth-app-revamp/get-dashboard-data').'?'.$request_string;
    $data['referesh_age_graph']       = Common::generate_url('booth-app-revamp/referesh_age_graph').'?'.$request_string;
    $data['get_cumulative_time_data'] = Common::generate_url('booth-app-revamp/get_cumulative_time_data').'?'.$request_string;
    $data['get_voters_by_time'] = Common::generate_url('booth-app-revamp/get_voters_by_time').'?'.$request_string;


    $data['get_doughnut_data']    = Common::generate_url('booth-app-revamp/get_doughnut_data').'?'.$request_string;
    $data['get_gender_data']      = Common::generate_url('booth-app-revamp/get_gender_data').'?'.$request_string;
    $data['scan_page_url']        = Common::generate_url('booth-app-revamp/scan-data').'?'.$request_string;
    $data['user_data']            = Auth::user();
    $data['href_polling_station'] = Common::generate_url('booth-app-revamp/polling-station').'?'.$request_string;
    $data['href_officer']         = Common::generate_url('booth-app-revamp/officers').'?'.$request_string;
    $data['href_blo_officer']     = Common::generate_url('booth-app-revamp/officers').'?role_id=33'.'&'.$request_string;
    $data['href_pro_officer']     = Common::generate_url('booth-app-revamp/officers').'?role_id=35'.'&'.$request_string;
    $data['href_po_officer']      = Common::generate_url('booth-app-revamp/officers').'?role_id=34'.'&'.$request_string;
    $data['href_so_officer']      = Common::generate_url('booth-app-revamp/officers').'?role_id=38'.'&'.$request_string;



    $data['href_pro_activate']    = Common::generate_url('booth-app-revamp/officers').'?role_id=35&is_activated=no'.'&'.$request_string;
    $data['href_po_activate']    = Common::generate_url('booth-app-revamp/officers').'?role_id=34&is_activated=no'.'&'.$request_string;
    $data['href_so_activate']    = Common::generate_url('booth-app-revamp/officers').'?role_id=38&is_activated=no'.'&'.$request_string;
    $data['href_blo_activate']    = Common::generate_url('booth-app-revamp/officers').'?role_id=33&is_activated=no'.'&'.$request_string;
    $data['href_e_download']      = Common::generate_url('booth-app-revamp/e-roll-download').'?'.$request_string;
    $data['href_infra']           = Common::generate_url('booth-app-revamp/infra').'?'.$request_string;

    $data['href_not_activate']    = Common::generate_url('booth-app-revamp/officers').'?is_activated=no'.'&'.$request_string;

    if($this->role_id == '7'){
      $poll_detail_href = Common::generate_url('booth-app-revamp/poll-detail/state').'?'.$request_string;
    }else if($this->role_id == '4'){
      $poll_detail_href = Common::generate_url('booth-app-revamp/poll-detail/ac').'?'.$request_string;
    }else{
      $poll_detail_href = Common::generate_url('booth-app-revamp/poll-detail').'?'.$request_string;
    }
    $data['href_poll_detail']       = $poll_detail_href;
    $data['href_connected_status']  = $poll_detail_href;


    if($this->role_id == 7){
      $incident_href = Common::generate_url('booth-app-revamp/incident').'?'.$request_string;
      $href_mock_poll = Common::generate_url('booth-app-revamp/mock-poll').'?'.$request_string;
      $href_infra_poll      = Common::generate_url('booth-app-revamp/infra').'?'.$request_string;
      $href_mapped_location = Common::generate_url('booth-app-revamp/mapped-location-report').'?'.$request_string;
      $href_poll_material = Common::generate_url('booth-app-revamp/poll-material').'?'.$request_string;
    }else if($this->role_id == 4){
      $incident_href = Common::generate_url('booth-app-revamp/incident/ac').'?'.$request_string;
      $href_mock_poll = Common::generate_url('booth-app-revamp/mock-poll/ac').'?'.$request_string;
      $href_infra_poll = Common::generate_url('booth-app-revamp/infra/ac').'?'.$request_string;
      $href_mapped_location = Common::generate_url('booth-app-revamp/mapped-location-report').'?'.$request_string;
      $href_poll_material = Common::generate_url('booth-app-revamp/poll-material/ac').'?'.$request_string;
    }else{
      $incident_href        = Common::generate_url('booth-app-revamp/incident/ac/ps').'?'.$request_string;
      $href_mock_poll       = Common::generate_url('booth-app-revamp/mock-poll/ac/ps').'?'.$request_string;
      $href_infra_poll      = Common::generate_url('booth-app-revamp/infra/ac/ps').'?'.$request_string;
      $href_mapped_location = Common::generate_url('booth-app-revamp/mapped-location-report').'?'.$request_string;
      $href_poll_material = Common::generate_url('booth-app-revamp/poll-material/ac/ps').'?'.$request_string;
    }

    $data['incident_href']  = $incident_href;
    $data['href_mock_poll'] = $href_mock_poll;
    $data['href_infra_poll'] = $href_infra_poll;

    $data['href_infra_poll']        = $href_infra_poll;
    $data['href_mapped_location']  = $href_mapped_location;
    $data['href_poll_material']   = $href_poll_material;


  //form filters
    $data['filter_action']  = Common::generate_url("booth-app-revamp/dashboard");
    $phases_filter          = Common::default_filter_values($request);
    $data['phases']         = $phases_filter['phases'];
    $data['states']         = $phases_filter['states'];
    $data['acs']            = $phases_filter['acs'];

    return view($this->view.'.dashboard', $data);
  }

  public function get_dashboard_data(Request $request){

    $data                   = [];
    $request_string = $this->request_param;
    $filter = [
      'phase_no'        => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'ps_no'           => $this->ps_no,
    ];

    $data['ac_no']    = $this->ac_no;
    $data['st_code']  = $this->st_code;
    $data['ps_no']    = $this->ps_no;
    $data['dist_no']  = $this->dist_no;
    $data['phase_no'] = $this->phase_no;
    $data['total_incident'] = TblAnalyticsDashboardModel::get_incident_count($filter);
	
    $data['infra_start']  = 0;
    $data['infra_ramp']   = 0;
    $data['infra_toilet_facility']  = 0;
    $data['infra_exit_door']      = 0;
    $data['infra_furniture']      = 0;
    $data['infra_light']          = 0;
    $data['infra_drinking_water'] = 0;

    $infra_results            = InfraMapping::get_infra_mapping($filter);
    if($infra_results && isset($infra_results)){
      $data['infra_start']  = $infra_results['start_date_time'];
      $data['infra_ramp']   = $infra_results['ramp'];
      $data['infra_toilet_facility']  = $infra_results['toilet_facility'];
      $data['infra_exit_door']      = $infra_results['exit_door'];
      $data['infra_furniture']      = $infra_results['furniture'];
      $data['infra_light']          = $infra_results['light'];
      $data['infra_drinking_water'] = $infra_results['drinking_water'];
    }
    $data['mock_poll_start']        = 0;
    $data['mock_poll_result_shown'] = 0;
    $data['mock_button_clear']      = 0;
    $data['mock_slip_remove']       = 0;

    $infra_results            = MockPoll::get_mock_poll($filter);
    if($infra_results && isset($infra_results)){
      $data['mock_poll_start']  = $infra_results['mock_poll_start'];
      $data['mock_poll_result_shown'] = $infra_results['mock_poll_result_shown'];
      $data['mock_button_clear']      = $infra_results['button_clear'];
      $data['mock_slip_remove']       = $infra_results['slip_remove'];
    }

   $officer_count = PollingStationOfficerModel::total_officer_by_query($filter);

//for getting total assigned PO. Changes done on 27 feb 2022
    $acs_results = AcModel::get_acs($filter);
    $total_po_assign=0;
    foreach ($acs_results as $key => $iterate_ac) {
      $filter_iterate = [
        'phase_no'  => $this->phase_no,
        'st_code'   => $iterate_ac['st_code'],
        'ac_no'     => $iterate_ac['ac_no'],
      ];
        $officer_count_po_assigned = PollingStationOfficerModel::total_officer_by_query($filter_iterate);
         $total_po_assign   += $officer_count_po_assigned['total_po'];
    }
    //end of total assigned PO
   // dd($officer_count);


    $total_officer_count      = PollingStationOfficerModel::get_total_count($filter);

    if($this->role_id == 7){
      $href_mapped_location = Common::generate_url('booth-app-revamp/poll-material').'?'.$request_string;
      $href_poll_material = Common::generate_url('booth-app-revamp/poll-material').'?'.$request_string;
      $href_not_activated = Common::generate_url('booth-app-revamp/not-activated-officer').'?'.$request_string;
    }else if($this->role_id == 4){
      $href_mapped_location = Common::generate_url('booth-app-revamp/mapped-location-report').'?'.$request_string;
      $href_poll_material = Common::generate_url('booth-app-revamp/poll-material/ac').'?'.$request_string;
      $href_not_activated = Common::generate_url('booth-app-revamp/not-activated-officer/ac').'?'.$request_string;
    }else if($this->role_id == 5){
      $href_mapped_location = Common::generate_url('booth-app-revamp/mapped-location-report').'?'.$request_string;
      $href_poll_material = Common::generate_url('booth-app-revamp/poll-material/ac').'?'.$request_string;
      $href_not_activated = Common::generate_url('booth-app-revamp/not-activated-officer/ac').'?'.$request_string;
    }else{
      $href_mapped_location = Common::generate_url('booth-app-revamp/mapped-location-report').'?'.$request_string;
      $href_poll_material = Common::generate_url('booth-app-revamp/poll-material/ac/ps').'?'.$request_string;
      $href_not_activated = Common::generate_url('booth-app-revamp/not-activated-officer/ac/ps').'?'.$request_string;
    }
    $data['href_mapped_location']  = $href_mapped_location;
    $data['href_poll_material']   = $href_poll_material;
    $data['href_not_activated']   = $href_not_activated;

    //location mapped or unmapped
    $location_object     = PollingStationLocationModel::total_location_count($filter);
    $data['total_polling_location']   = $location_object['total'];
    $data['total_mapped_location']    = $location_object['mapped'];
    $data['total_unmapped_location']  = PollingStationLocationModel::get_unmapped_ps($filter);//$location_object['unmapped'];

    //material received or not
    $total_material       = TblAnalyticsDashboardModel::total_material_count($filter);
    $data['total_received'] = $total_material['total_received'];
    $data['total_submited'] = $total_material['total_submited'];


    $data['total_polling_booth']        = PollingStation::total_poll_station_count($filter);
	
    $data['total_blo_assign']           = $officer_count['total_blo'];
    $data['total_pro_assign']           = $officer_count['total_pro'];
    //$data['total_po_assign']            = $officer_count['total_po'];
	$data['total_po_assign']            = $total_po_assign;
    $data['total_so_assign']            = $officer_count['total_sm'];

    $data['total_blo_pro_assign']       = $data['total_pro_assign']+$data['total_blo_assign']+$data['total_po_assign']+$data['total_so_assign'];
    $data['total_unique_blo'] = PollingStationOfficerModel::get_assign_officer_count(array_merge($filter,['role_id' => 33]));
    $data['total_unique_pro'] = PollingStationOfficerModel::get_assign_officer_count(array_merge($filter,['role_id' => 35]));

    if($data['total_unique_pro'] > $data['total_unique_blo']){
      $data['total_officer_assigned'] = $data['total_unique_blo'];
    }else{
      $data['total_officer_assigned'] = $data['total_unique_pro'];
    }

    $data['total_not_assign_officers']  = $data['total_polling_booth'] - $data['total_officer_assigned'];
    $data['total_officer_count']        = $data['total_blo_assign']+$data['total_pro_assign'];
    $data['total_blo_pro_count']        = $data['total_blo_assign']+$data['total_pro_assign'];
    $data['total_app_downloaded']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_activated' => true]));
    $data['total_blo_not_activated']      = $officer_count['total_blo'] - $officer_count['blo_activated'];
    $data['total_pro_not_activated']      = $officer_count['total_pro'] - $officer_count['pro_activated'];
    $data['total_po_not_activated']       = $officer_count['total_po'] - $officer_count['po_activated'];
    $data['total_so_not_activated']       = $officer_count['total_sm'] - $officer_count['sm_activated'];
    $data['total_e_download']   = TblBoothUserModel::total_e_download($filter);
    $data['total_poll_end']     = TblAnalyticsDashboardModel::total_statics_count(array_merge($filter,['is_end' => true]));
    $data['total_eroll_download_confirmed'] = 1;
    $data['total_poll_started']             = TblAnalyticsDashboardModel::total_statics_count(array_merge($filter,['is_started' => true]));
    $data['poll_percent']                   = 0;
    if($data['total_poll_started']>0 && $data['total_polling_booth']>0){
      $data['poll_percent'] = round($data['total_poll_started']/$data['total_polling_booth']*100);
    }
	$connected_ps_data 						= TblAnalyticsDashboardModel::total_statics_count_connected($filter);
	
	
    $data['total_connected_status']         = $connected_ps_data['connected_count'];
	
    $data['total_disconnected_status']      = $connected_ps_data['total_count'] - $connected_ps_data['connected_count'];


  //pwd voters
    $pwd_data =           TblAnalyticsDashboardModel::get_pwd_voters_electors($filter);

    $data['pwd_male']     = $pwd_data['pwd_v_male_new'];
    $data['pwd_female']   = $pwd_data['pwd_v_female_new'];
    $data['pwd_other']    = $pwd_data['pwd_v_other_new'];
    $data['pwd_total']    = $pwd_data['pwd_v_male_new']+$pwd_data['pwd_v_female_new']+$pwd_data['pwd_v_other_new'];


    $data['pwd_e_male']   = $pwd_data['pwd_e_male_new'];
    $data['pwd_e_female'] =$pwd_data['pwd_e_female_new'];
    $data['pwd_e_other']  = $pwd_data['pwd_e_other_new'];
    $data['pwd_e_total']  = $pwd_data['pwd_e_male_new']+$pwd_data['pwd_e_female_new']+$pwd_data['pwd_e_other_new'];

    $data['pwd_male_percentage'] = 0;
    if($pwd_data['pwd_e_male_new'] > 0 && $pwd_data['pwd_v_male_new'] > 0){
      $data['pwd_male_percentage'] = round(($pwd_data['pwd_v_male_new']/$pwd_data['pwd_e_male_new'])*100,2);
    }
    $data['pwd_female_percentage'] = 0;
    if($pwd_data['pwd_e_female_new'] > 0 && $pwd_data['pwd_v_female_new'] > 0){
      $data['pwd_female_percentage'] = round(($pwd_data['pwd_v_female_new']/$pwd_data['pwd_e_female_new'])*100,2);
    }
    $data['pwd_other_percentage'] = 0;
    if($pwd_data['pwd_e_other_new'] > 0 && $pwd_data['pwd_v_other_new'] > 0){
      $data['pwd_other_percentage'] = round(($pwd_data['pwd_v_other_new']/$pwd_data['pwd_e_other_new'])*100,2);
    }
    $data['pwd_total_percentage'] = 0;
    if($data['pwd_total']>0 && $data['pwd_e_total'] && $data['pwd_e_total'] >= $data['pwd_total']){
      $data['pwd_total_percentage'] = round(($data['pwd_total']/$data['pwd_e_total'])*100,2);
    }

    $data['last_disc_ps_name']            = '';
    $data['last_disc_ac_name']            = '';
    $data['poll_percent_for_css']         = 300;
    if($data['poll_percent'] < 100){
      $data['poll_percent_for_css']       = $data['poll_percent'] * 360/100;
    }

    $max_po                     = [];
    $highest_polling_name       = '';
    $max_percenatge             = [0];
    $data['voter_turnouts']     = [];
    $officers_data              = [];

  //voter turnout
  $voter_turn_out = '';//$this->get_voter_turnout($request);

  //officers
  $officers = PollingStationOfficerModel::get_officers(array_merge($filter,['is_activated' => 'no', 'limit' => 5]));

  foreach($officers as $officer){

    $role = '';
    if($officer['role_id'] == '33'){
      $role = 'BLO';
    }
    if($officer['role_id'] == '35'){
      $role = 'PRO';
    }
    if($officer['role_id'] == '34'){
      $role = 'PO';
    }
    if($officer['role_id'] == '38'){
      $role = 'SM';
    }

    $st_name = '';
    $state_object = StateModel::get_state_by_code($officer['st_code']);
    if($state_object){
      $st_name = $state_object['ST_NAME'];
    }

    $ac_name = '';
    $ac_object = AcModel::get_ac(['state' => $officer['st_code'], 'ac_no' => $officer['ac_no']]);
    if($ac_object){
      $ac_name = $ac_object['ac_name'];
    }


    $poll_station_name = '';
    if($officer['role_id'] == 38 && $officer['ps_no'] != ''){
      $array_ps_name = [];
      foreach (explode(',',$officer['ps_no']) as $iterate_ps_no) {
        $poll_station = PollingStation::get_polling_station([
          'st_code' => $officer['st_code'],
          'ac_no'   => $officer['ac_no'],
          'ps_no'   => $iterate_ps_no
        ]);
        if($poll_station){
          $array_ps_name[] = $poll_station['PS_NAME_EN'];
        }
        $poll_station_name = implode(', ', $array_ps_name);
      }
    }else{
      $poll_station = PollingStation::get_polling_station([
        'st_code' => $officer['st_code'],
        'ac_no'   => $officer['ac_no'],
        'ps_no'   => $officer['ps_no']
      ]);
      if($poll_station){
        $poll_station_name = $poll_station['PS_NAME_EN'];
      }
    }

    $officers_data[] =  [
      'st_code'       => $officer['st_code'],
      'st_name'     => $st_name,
      'ac_no'       => $officer['ac_no'],
      'ac_name'     => $ac_name,
      'ps_no'         => $officer['ps_no'],
      'ps_name'       => $poll_station_name,
      'name'          => $officer['name'],
      'mobile'        => $officer['mobile_number'],
      'designation'   => $role,
    ];
  }

  $stats_sum    = TblAnalyticsDashboardModel::total_statics_sum($this->filter);

  $grand_male   = $stats_sum['male_voters'];
  $grand_female = $stats_sum['female_voters'];
  $grand_other  = $stats_sum['other_voters'];
  $grand_49     = $stats_sum['form_49_count'];
  $tendered_count     = $stats_sum['tendered_count'];
  $grand_total  = $grand_male + $grand_female + $grand_other;

  $electors       = TblAnalyticsDashboardModel::get_aggregate_voters($this->filter);
  $grand_e_male   = $electors['e_male'];
  $grand_e_female = $electors['e_female'];
  $grand_e_other  = $electors['e_other'];
  $grand_e_total  = $grand_e_male + $grand_e_female + $grand_e_other;



  $total_male_percentage = 0;
  if($grand_e_male >= $grand_male && $grand_e_male>0){
    $total_male_percentage = ROUND($grand_male/$grand_e_male*100,2);
  }

  $total_female_percentage = 0;
  if($grand_e_female >= $grand_female && $grand_e_female>0){
    $total_female_percentage = ROUND($grand_female/$grand_e_female*100,2);
  }

  $total_other_percentage = 0;
  if($grand_e_other >= $grand_other && $grand_e_other>0){
    $total_other_percentage = ROUND($grand_other/$grand_e_other*100,2);
  }

  $total_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total>0){
    $total_percentage = ROUND($grand_total/$grand_e_total*100,2);
  }
  
  

  $data['poll_turnout_percentage']  = $total_percentage;
  $data['total_male_percentage']    = $total_male_percentage.'%';
  $data['total_female_percentage']  = $total_female_percentage.'%';
  $data['total_other_percentage']   = $total_other_percentage.'%';
  $data['total_total_percentage']   = $total_percentage.'%';

  $data['total_male']    = $grand_male;
  $data['total_female']  = $grand_female;
  $data['total_other']   = $grand_other;
  $data['total_total']   = $grand_total;
  $data['grand_49']      = $grand_49;
  $data['tendered_count']=$tendered_count;
  $data['grand_e_male']   = $grand_e_male;
  $data['grand_e_female'] = $grand_e_female;
  $data['grand_e_other']  = $grand_e_other;
  $data['grand_e_total']  = $grand_e_total;


  //bar graph
  $age_gap = $this->age_gap;
  $bar_graph = [];
  $data['age_gap']    = json_encode($age_gap);
  $data['bar_graph']  = json_encode($bar_graph);


  //gender wise bar chart
  $gender_label_for_bar = ["Male", "Female", "Other"];
  $gender_data_for_bar  = [0,0,0];
  $data['gender_label_for_bar'] = json_encode($gender_label_for_bar);
  $data['gender_data_for_bar']  = json_encode($gender_data_for_bar);


  //periodic line chart every 15 min
  if(isset($is_time_slot)){
    $time_slot_label_for_line = TblAnalyticsDashboardModel::half_hour_times($this->filter);
	
    foreach ($time_slot_label_for_line as $iterate_time_slot_label) {
      $time_slot_data_for_line[]  = TblPollSummaryModel::get_voter_count(array_merge($filter,['is_cumulative' => $iterate_time_slot_label]));
    }
    $data['time_slot_data_for_line'] =  json_encode($time_slot_data_for_line);
    $data['time_slot_label_for_line'] = json_encode($time_slot_label_for_line);
  }

  //line graph for cumulative
  $grand_e_total = $grand_e_total;
  $cumulative_label_for_line  = TblAnalyticsDashboardModel::half_hour_times($this->filter);
  $cumulative_data_for_line   = [];
  $data['cumulative_data_for_line'] =  json_encode($cumulative_data_for_line);
  $data['cumulative_label_for_line'] = json_encode($cumulative_label_for_line);


  $data['voters_data_by_time'] =  json_encode($cumulative_data_for_line);
  $data['voters_label_by_time'] = json_encode($cumulative_label_for_line);


  $data['officers'] = $officers_data;
  $data['highest_polling_name'] = $highest_polling_name;

  //doughnut_data
  $doughnut_data = [0,0,0,0,0];
  // $doughnut_data = [0,0,0,0]; //remove first 0 for hiding QR option
  $data['doughnut_data'] = json_encode($doughnut_data);

  if($request->has('is_ajax')){
    return \Response::json($data);
  }

  return $data;

}

public function referesh_age_graph(Request $request){
  $data = [];
  $age_gap = $this->age_gap;
  $bar_graph = TblAnalyticsDashboardModel::get_age_group($this->filter);
  $data['age_gap']    = json_encode($age_gap);
  $data['bar_graph']  = json_encode($bar_graph);
  return \Response::json($data);
}

public function get_cumulative_time_data(Request $request){
  $data = [];
  //line graph for cumulative
  $cumulative_label_for_line  = TblAnalyticsDashboardModel::half_hour_times($this->filter);
  
 
  $cumulative_data_for_line = [];
  
  $results =   TblAnalyticsDashboardModel::get_cumulative_time_data(array_merge($this->filter,['is_cumulative' => $cumulative_label_for_line]));
  
  $electors       = TblAnalyticsDashboardModel::get_aggregate_voters($this->filter);
  $grand_e_total  = $electors['e_male'] + $electors['e_female'] + $electors['e_other'];
  foreach($results as $cumulative_data){
    $cumulative_percentage = 0;
    if($grand_e_total >= $cumulative_data && $grand_e_total>0){
      $cumulative_percentage = round($cumulative_data/$grand_e_total*100,2);

    }
    $cumulative_data_for_line[]  = $cumulative_percentage;
  }

  // foreach ($cumulative_label_for_line as $iterate_time_slot_label) {
  //   $cumulative_percentage = 0;
  //   $cumulative_total = VoterInfoPollStatusModel::get_voter_count(array_merge($filter,['is_cumulative' => $iterate_time_slot_label]));
  // half_hour_times  if($grand_e_total >= $cumulative_total && $grand_e_total>0){
  //   if($grand_e_total >= $cumulative_total && $grand_e_total>0){
  //     $cumulative_percentage = round($cumulative_total/$grand_e_total*100,2);

  //   }
  //   $cumulative_data_for_line[]  = $cumulative_percentage;
  // }

  $data['cumulative_data_for_line'] =  json_encode($cumulative_data_for_line);
  $data['cumulative_label_for_line'] = json_encode($cumulative_label_for_line);
  return \Response::json($data);

}

public function get_voters_by_time(Request $request){
  $data = [];
  $cumulative_label_for_line  = TblAnalyticsDashboardModel::half_hour_times($this->filter);
  
  $cumulative_data_for_line   = TblAnalyticsDashboardModel::get_voters_by_time(array_merge($this->filter,['is_cumulative' => $cumulative_label_for_line]));
  
  $data['cumulative_data_for_line'] =  json_encode($cumulative_data_for_line);
  $data['cumulative_label_for_line'] = json_encode($cumulative_label_for_line);
  return \Response::json($data);

}

public function get_doughnut_data(Request $request){
  $data = [];
  $doughnut_data = [0,0,0,0,0];
  //$doughnut_data = [0,0,0,0]; //remove first 0 for hiding QR option
  $doughnut_object = TblAnalyticsDashboardModel::total_statics_sum($this->filter);
  if($doughnut_object){
    $doughnut_data = [$doughnut_object['scan_qr'],$doughnut_object['scan_epicno'],$doughnut_object['scan_srno'],$doughnut_object['scan_name'],$doughnut_object['scan_mobile']];
    // for removing QR
    // $doughnut_data = [$doughnut_object['scan_epicno'],$doughnut_object['scan_srno'],$doughnut_object['scan_name'],$doughnut_object['scan_mobile']];
  }
  $data['doughnut_data'] = json_encode($doughnut_data);
  return \Response::json($data);

}

public function get_gender_data(Request $request){
  $data = [];
  $gender_label_for_bar = ["Male", "Female", "Other"];
  $gender_data_for_bar  = [];
  $gender_data_for_bar_new = TblAnalyticsDashboardModel::get_voter_count(array_merge($this->filter,['gender' => 'M']));
 
  foreach($gender_data_for_bar_new as $itr){
	  
	$gender_data_for_bar[] =   $itr;
  }
  $data['gender_label_for_bar'] = json_encode($gender_label_for_bar);
  $data['gender_data_for_bar']  = json_encode($gender_data_for_bar);
  return \Response::json($data);

}

public function copy_booth(Request $request){
    $i = 0;
      $resuls = DB::connection("booth_revamp")->table("tbl_voter_info_poll_status")->where("user_type",34)->whereRaw("epic_no NOT IN (SELECT epic_no FROM tbl_voter_info)")->paginate(30000);
      foreach ($resuls as $key => $itr_res) {
        DB::connection("booth_revamp")->table("tbl_voter_info")->insert([
          'epic_no' => $itr_res->epic_no,
          'st_code' => $itr_res->st_code,
          'ac_no' => $itr_res->ac_no,
          'ps_no' => $itr_res->ps_no,
          'age' => $itr_res->age,
          'gender' => $itr_res->gender,
          'slnoinpart' => $itr_res->serial_no
        ]);
        echo $i++."<br>";
      }
}



}
