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
use App\models\Admin\BoothApp\{BoothModel, PollingStation, SpmVoterListModel, PollingStationOfficerModel, SpmVoterInfo, SpmStaticsModel, SpmPollingStation};
use App\Http\Requests\Admin\BoothApp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;

//current

class PollingController extends Controller {

  public $folder        = 'booth-app';
  public $view          = "admin.booth-app";
  public $action        = "booth-app";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $role_id       = 0;
  public $base          = 'roac';
  public $restricted_ps = ['91','99','128','129','189'];
  public $no_allowed_po   = 4;
  public $no_allowed_blo  = 4;
  public $no_allowed_pro  = 1;

  public function __construct(){
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      $this->role_id = Auth::user()->role_id;
      if(Auth::user() && $this->role_id == '19'){
        $this->ac_no    = Auth::user()->ac_no;
        $this->st_code  = Auth::user()->st_code;
        $this->role_id  = Auth::user()->role_id;
        $this->base     = 'roac';
      }else if(Auth::user() && $this->role_id == '5'){
        $this->ac_no    = 0;
        $this->st_code  = Auth::user()->st_code;
        $this->role_id  = Auth::user()->role_id;
        $this->base     = 'acdeo';
      }else if(Auth::user() && $this->role_id == '4'){
        $this->ac_no    = 0;
        $this->st_code  = Auth::user()->st_code;
        $this->role_id  = Auth::user()->role_id;
        $this->base     = 'acceo';
      }else{
        $this->ac_no    = '228';
        $this->st_code  = 'S24';
        $this->role_id  = Auth::user()->role_id;
        $this->base     = 'eci';
      }
      return $next($request);
    });


  }

  public function dashboard(Request $request){
    $data                         = [];
    $data['buttons']              = [];
    $data['role_id']              = $this->role_id;
    $data['heading_title']        = "Change Pin";
    $data['action']               = url($this->action);
    $data['referesh_page_url']    = Common::generate_url('booth-app/get-dashboard-data');
    $data['scan_page_url']        = Common::generate_url('booth-app/scan-data');
    $data['user_data']            = Auth::user();
    $data['href_polling_station'] = Common::generate_url('booth-app/polling-station');
    $data['href_officer']         = Common::generate_url('booth-app/officers');
    $data['href_blo_officer']     = Common::generate_url('booth-app/officers').'?role_id=33';
    $data['href_pro_officer']     = Common::generate_url('booth-app/officers').'?role_id=35';
    $data['href_pro_activate']    = Common::generate_url('booth-app/officers').'?role_id=35&is_activated=no';
    $data['href_blo_activate']    = Common::generate_url('booth-app/officers').'?role_id=33&is_activated=no';
    $data['href_e_download']      = Common::generate_url('booth-app/e-roll-download');;
    $data['href_poll_detail']       = Common::generate_url('booth-app/poll-detail');
    $data['href_connected_status']  = Common::generate_url('booth-app/poll-detail');
    $dashboard_data                 = $this->get_dashboard_data($request);
    $data                           = array_merge($dashboard_data, $data);
    $data['active_tab']             = 'after';
    if($request->has('tab') && $request->tab == 'after'){
      $data['active_tab'] = $request->tab;
    }
    return view($this->view.'.mis-dashboard', $data);
  }

  public function get_dashboard_data(Request $request){

    $data                   = [];
    $filter = [
      'st_code' => $this->st_code,
      'ac_no'   => $this->ac_no,
      'restricted_ps' => $this->restricted_ps
    ];

    $data['total_polling_booth']   = SpmVoterListModel::total_poll_station_count($filter);
    $data['total_blo_assign']           = PollingStationOfficerModel::total_officer_count(array_merge($filter,['role_id' => '33']));
    $data['total_pro_assign']           = PollingStationOfficerModel::total_officer_count(array_merge($filter,['role_id' => '35']));
    $data['total_po_assign']            =  PollingStationOfficerModel::total_officer_count(array_merge($filter,['role_id' => '34']));
    $data['total_blo_pro_assign']       = $data['total_pro_assign']+$data['total_blo_assign'];

    $data['total_not_assign_officers']  = ($this->no_allowed_blo*$data['total_polling_booth'])+($this->no_allowed_pro*$data['total_polling_booth']) - $data['total_blo_pro_assign'];


    $data['total_officer_count']        = $data['total_blo_assign']+$data['total_pro_assign']+$data['total_po_assign'];
    $data['total_blo_pro_count']        = $data['total_blo_assign']+$data['total_pro_assign'];
    $data['total_app_downloaded']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_activated' => true]));

    // $data['total_blo_activated']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_activated' => true, 'role_id' => 33]));
    // $data['total_pro_activated']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_activated' => true, 'role_id' => 35]));
    // $data['total_po_activated']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_activated' => true, 'role_id' => 34]));

    $data['total_blo_not_activated']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_not_activated' => true, 'role_id' => 33]));
    $data['total_pro_not_activated']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_not_activated' => true, 'role_id' => 35]));
    $data['total_po_not_activated']       = PollingStationOfficerModel::total_officer_count(array_merge($filter,['is_not_activated' => true, 'role_id' => 34]));
    $data['total_e_download']   = SpmStaticsModel::total_statics_count(array_merge($filter,['download_time' => true]));
    $data['total_poll_end']   = SpmStaticsModel::total_statics_count(array_merge($filter,['is_end' => true]));
    $data['total_eroll_download_confirmed'] = 1;
    $data['total_poll_started']             = SpmStaticsModel::total_statics_count(array_merge($filter,['is_started' => true]));
    $data['poll_percent']                   = 0;
    if($data['total_poll_started']>0 && $data['total_polling_booth']>0){
      $data['poll_percent'] = round($data['total_poll_started']/$data['total_polling_booth']*100);
    }
    $data['total_connected_status']         = SpmStaticsModel::total_statics_count(array_merge($filter,['is_started' => true]));
    $data['total_disconnected_status']      = SpmStaticsModel::total_statics_count(array_merge($filter,['is_end' => true]));

    $data['total_connected_status'] = $data['total_connected_status'] - $data['total_disconnected_status'];

    $voter_turnouts                         = SpmVoterInfo::get_last_disconnected_ps(array_merge($filter,['is_disconnected' => true]));
    $data['last_disc_ps_name']            = '';
    $data['last_disc_ac_name']            = '';
    $data['poll_percent_for_css']         = 300;
    if($data['poll_percent'] < 100){
      $data['poll_percent_for_css']       = $data['poll_percent'] * 360/100;
    }

    if($voter_turnouts){
      $poll_station = PollingStation::get_polling_station($voter_turnouts);
      if($poll_station){
        $data['last_disc_ps_name']              = $poll_station['PS_NAME_EN'];
      }
    }

    $ac_object = AcModel::get_record([
      'state' => $filter['st_code'],
      'ac_no' => $filter['ac_no']
    ]);
    if($ac_object){
      $data['last_disc_ac_name'] = $ac_object['ac_name'];
    }

    $max_po                     = [];
    $highest_polling_name       = '';
    $max_percenatge             = [0];
    $data['voter_turnouts']     = [];
    $officers_data              = [];

    $grand_e_male   = 0;
    $grand_e_female = 0;
    $grand_e_other  = 0;
    $grand_e_total  = 0;
    $grand_male   = 0;
    $grand_female = 0;
    $grand_other  = 0;
    $grand_total  = 0;
    $grand_queue  = 0;

    foreach ($this->restricted_ps as $key => $iterate_restricted) {

      $filter_for_voters = array_merge($filter,['ps_no' => $iterate_restricted]);

      $poll_station_name = '';

      $poll_station = PollingStation::get_polling_station($filter_for_voters);
      if($poll_station){
        $poll_station_name = $poll_station['PS_NAME_EN'];
      }

      $male   = SpmVoterInfo::get_voter_count(array_merge($filter_for_voters,['gender' => 'M']));
      $female = SpmVoterInfo::get_voter_count(array_merge($filter_for_voters,['gender' => 'F']));
      $other  = SpmVoterInfo::get_voter_count(array_merge($filter_for_voters,['gender' => 'O']));
      $total  = $male+$female+$other;

      $e_male   = SpmVoterListModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M']));
      $e_female = SpmVoterListModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F']));
      $e_other  = SpmVoterListModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O']));
      $e_total  = $e_male+$e_female+$e_other;

      $queue_electors = SpmVoterInfo::get_voter_count(array_merge($filter_for_voters,['is_queue' => true]));

      $grand_e_male   += $e_male;
      $grand_e_female += $e_female;
      $grand_e_other  += $e_other;
      $grand_e_total  += $e_total;
      $grand_male   += $male;
      $grand_female += $female;
      $grand_other  += $other;
      $grand_total  += $total;

      $is_ps_poll_end = SpmStaticsModel::total_statics_count(array_merge($filter_for_voters,['is_end' => true]));
      if($is_ps_poll_end){
        $queue_electors = 'Poll End';
      }else{
        $grand_queue  += $queue_electors;
      }

      $percentage = 0;
      if($e_total >= $total && $e_total > 0){
        $percentage = round($total/$e_total*100,2);
      }

      if($percentage > max($max_percenatge)){
        $max_percenatge[] = $percentage;
        $highest_polling_name = $poll_station_name;
      }

      $data['voter_turnouts'][] = [
        'ps_name'         => $poll_station_name,
        'ps_no'           => $iterate_restricted,
        'ps_name_and_no'  => $iterate_restricted.'-'.$poll_station_name,
        'male'            => $male,
        'female'          => $female,
        'other'           => $other,
        'total'           => $total,
        'e_male'    => $e_male,
        'e_female'  => $e_female,
        'e_other'   => $e_other,
        'e_total'   => $e_total,
        'total_in_queue'  => $queue_electors,
        'percentage' => $percentage
      ];

      //officers
      $officers = PollingStationOfficerModel::get_officers(array_merge($filter_for_voters,['is_activated' => 'no']));
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

        $st_name = '';
        $state_object = StateModel::get_state_by_code($officer['st_code']);
        if($state_object){
          $st_name = $state_object['ST_NAME'];
        }

        $ac_name = '';
        $ac_object = AcModel::get_record(['state' => $officer['st_code'], 'ac_no' => $officer['ac_no']]);
        if($ac_object){
          $ac_name = $ac_object['ac_name'];
        }

        $officers_data[] =  [
          'st_code'       => $officer['st_code'],
          'st_name'     => $st_name,
          'ac_no'       => $officer['ac_no'],
          'ac_name'     => $ac_name,
          'ps_no'         => $iterate_restricted,
          'ps_name'       => $poll_station_name,
          'name'          => $officer['name'],
          'mobile'        => $officer['mobile_number'],
          'designation'   => $role,
        ];
      }

    }

    $average_timing = 0;
    $average_timing_object = SpmVoterInfo::get_average_time($filter);

    if($average_timing_object > 60){
      $average_timing = ROUND($average_timing_object/60,2);
      $average_unit = "Minutes";
    }else{
      $average_timing = $average_timing_object;
      $average_unit = "Seconds";
    }

    $average_timing_html    = "<span>".$average_timing."</span>";
    $average_timing_html    .= "<br/> <small>".$average_unit."</small>";
    $data['average_timing'] = $average_timing_html;

    //total rows

    $grand_percentage = 0;
    if($grand_e_total >= $grand_total && $grand_e_total > 0){
      $grand_percentage = round($grand_total/$grand_e_total*100,2);
    }

    

    $data['voter_turnouts'][]    = [
      'ps_name' => 'Total',
      'ps_no'   => '',
      'ps_name_and_no' => 'Total',
      'male'    => $grand_male,
      'female'  => $grand_female,
      'other'   => $grand_other,
      'total'   => $grand_total,
      'e_male'    => $grand_e_male,
      'e_female'  => $grand_e_female,
      'e_other'   => $grand_e_other,
      'e_total'   => $grand_e_total,
      'total_in_queue'  => $grand_queue,
      'percentage' => $grand_percentage
    ];
    $data['poll_turnout_percentage'] = $grand_percentage;
    //End total rows
	
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

    $data['total_male_percentage']    = $total_male_percentage.'%';
    $data['total_female_percentage']  = $total_female_percentage.'%';
    $data['total_other_percentage']   = $total_other_percentage.'%';
    $data['total_total_percentage']   = $grand_percentage.'%';
	
	$data['total_male']    = $grand_male;
    $data['total_female']  = $grand_female;
    $data['total_other']   = $grand_other;
    $data['total_total']   = $grand_total;


    //bar graph
    $age_gap = ["18-25", "26-30", "31-40", "41-50", "51-60", "61-70", "71-80", "81-90", "91-100", "101-150"];
    $bar_graph  = [];
    foreach($age_gap as $iterate_age_gap){
      $bar_graph[] = SpmVoterInfo::get_elector_by_age(array_merge($filter,['age_between' => $iterate_age_gap]));
    }
    $data['age_gap']    = json_encode($age_gap);
    $data['bar_graph']  = json_encode($bar_graph);


    //gender wise bar chart 
    $gender_label_for_bar = ["Male", "Female", "Other"];
    $gender_data_for_bar  = [];
    $gender_data_for_bar[] = SpmVoterInfo::get_voter_count(array_merge($filter,['gender' => 'M']));
    $gender_data_for_bar[] = SpmVoterInfo::get_voter_count(array_merge($filter,['gender' => 'F']));
    $gender_data_for_bar[] = SpmVoterInfo::get_voter_count(array_merge($filter,['gender' => 'O']));
   
    $data['gender_label_for_bar'] = json_encode($gender_label_for_bar);
    $data['gender_data_for_bar']  = json_encode($gender_data_for_bar);
	
	//periodic line chart every 15 min
    $time_slot_label_for_line = SpmVoterInfo::half_hour_times(); 
    foreach ($time_slot_label_for_line as $iterate_time_slot_label) {
      $time_slot_data_for_line[]  = SpmVoterInfo::get_voter_count(array_merge($filter,['time_between' => $iterate_time_slot_label]));
    }
    
    $data['time_slot_data_for_line'] =  json_encode($time_slot_data_for_line); 
    $data['time_slot_label_for_line'] = json_encode($time_slot_label_for_line);


    $data['officers'] = $officers_data;
    $data['highest_polling_name'] = $highest_polling_name;

    //doughnut_data
    $doughnut_data = [0,0,0,0];
    /*
    enable to get data from stat table
    $doughnut_object = SpmStaticsModel::get_scan_count([
      'st_code' => $filter['st_code'],
      'ac_no'   => $filter['ac_no'],
    ]);
    */

    $doughnut_object = SpmVoterInfo::get_scan_count_from_poll_table([
      'st_code' => $filter['st_code'],
      'ac_no'   => $filter['ac_no'],
    ]);
    if($doughnut_object){
      $doughnut_data = [$doughnut_object['total_qr'],$doughnut_object['total_epic'],$doughnut_object['total_bs'],$doughnut_object['total_name']];
    }
    $data['doughnut_data'] = json_encode($doughnut_data);

    if($request->has('is_ajax')){
      return \Response::json($data);
    }

    return $data;

  }

  public function get_polling_station(Request $request){
    try{
      $data = [];
      $request_array = [];

      $data['st_code']  = NULL;
      $data['ac_no']    = NULL;
      if($request->has('st_code')){
        $data['st_code'] = base64_decode($request->st_code);
        $request_array[] = 'state='.$request->st_code;
      }
      if($request->has('ac_no')){
        $data['ac_no'] = base64_decode($request->ac_no);
        $request_array[] = 'ac_no='.$request->ac_no;
      }


      if($this->role_id == '19'){
        $data['st_code'] = $this->st_code;
        $data['ac_no'] = $this->ac_no;
      }

//set title
      $title_array  = [];
      $data['heading_title'] = 'BLO/PRO List';

      if($data['st_code']){
        $state_object = StateModel::get_state_by_code($data['st_code']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      if($data['ac_no']){
        $ac_object = AcModel::get_record(['state' => $data['st_code'], 'ac_no' => $data['ac_no']]);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
      }
      $data['filter_buttons'] = $title_array;

      if(Auth::user()->role_id == '4'){
        $data['state']  = Auth::user()->st_code;
      }

      $states = StateModel::get_states();
      $data['states'] = [];
      foreach($states as $result){
        if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
          ];
        }

        if(Auth::user()->role_id == '7' || Auth::user()->role_id == '27'){
          $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
          ];
        }
      }

      $data['filter']   = implode('&', array_merge($request_array));
//end set title

//buttons
      $data['buttons']    = [];
// $data['buttons'][]    = [
//   'href' => generate_url($this->action.'/officer-list/add/0'),
//   'name' => 'Add New BLO/PRO',
//   'target' => false,
// ];
      $data['action']         = url($this->action.'/officer-list');

      $results                = [];
      $filter_election = [
        'st_code'   => $data['st_code'],
        'ac_no'     => $data['ac_no'],
        'paginate'  => true,
        'restricted_ps' => ['99','128','129','91','189']
      ];

      $data['add_new_url'] = Common::generate_url($this->action.'/officer-list/add/0');

      $max_po           = [];
      $max_blo          = [];
      $data['results']  = [];
      $results    =   PollingStation::get_polling_stations($filter_election);
      $data['pag_results'] = $results;
      foreach ($results as $result) {
        $blo  = [];
        $pro  = [];
        $po   = [];
        $i    = 1;
        $j    = 1;
        $officers = PollingStationOfficerModel::get_officers(array_merge($filter_election,['ps_no' => $result['PS_NO']]));
        foreach($officers as $officer){
    
          if($officer['role_id'] == '33'){
            $blo[$j] = [
              'name'    => $officer->name,
              'mobile'  => $officer->mobile_number,
              'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($officer->id)),
              'is_active' => ($officer->is_active)?'Enable':'Disable'
            ];
            $j++;
          }
          if($officer['role_id'] == '35'){
            $pro = [
              'name'    => $officer->name,
              'mobile'  => $officer->mobile_number,
              'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($officer->id)),
              'is_active' => ($officer->is_active)?'Enable':'Disable'
            ];
          }
          if($officer['role_id'] == '34'){
            $po[$i] = [
              'name'    => $officer->name,
              'mobile'  => $officer->mobile_number,
              'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($officer->id)),
              'is_active' => ($officer->is_active)?'Enable':'Disable'
            ];
            $i++;
          }
        }
        $max_po[]   = count($po);
        $max_blo[]  = count($blo);
        $data['results'][] = [
          'ps_no'   => $result['PS_NO'],
          'ps_name' => $result['PS_NAME_EN'],
          'blo'     => $blo,
          'pro'     => $pro,
          'po'      => $po,
        ];
      }

$data['max_po']     =  $this->no_allowed_po;//max($max_po);
$data['max_blo']    =  $this->no_allowed_blo;//max($max_po);
$data['user_data']  =  Auth::user();

$data['heading_title_with_all'] = $data['heading_title'];

if($request->has('is_excel')){
  if(isset($title_array) && count($title_array)>0){
    $data['heading_title'] .= "- ".implode(', ', $title_array);
  }
  return $data;
}

return view($this->view.'.officer-list', $data);

}catch(\Exception $e){
  return Redirect::to($this->base.'/dashboard');
}

}

public function add_officer($id = 0, Request $request){

  $data = [];
  $request_array = [];

  $data['st_code']  = NULL;
  $data['ac_no']    = NULL;
  if($request->has('st_code')){
    $data['st_code'] = base64_decode($request->st_code);
    $request_array[] = 'state='.$request->st_code;
  }
  if($request->has('ac_no')){
    $data['ac_no'] = base64_decode($request->ac_no);
    $request_array[] = 'ac_no='.$request->ac_no;
  }


  if($this->role_id == '19'){
    $data['st_code'] = $this->st_code;
    $data['ac_no'] = $this->ac_no;
  }

//set title
  $title_array  = [];
  $data['heading_title'] = 'BLO/PRO List';

  if($data['st_code']){
    $state_object = StateModel::get_state_by_code($data['st_code']);
    if($state_object){
      $title_array[]  = "State: ".$state_object['ST_NAME'];
    }
  }
  if($data['ac_no']){
    $ac_object = AcModel::get_record(['state' => $data['st_code'], 'ac_no' => $data['ac_no']]);
    if($ac_object){
      $title_array[]  = "AC: ".$ac_object['ac_name'];
    }
  }
  $data['filter_buttons'] = $title_array;

  if(Auth::user()->role_id == '4'){
    $data['state']  = Auth::user()->st_code;
  }

  $states = StateModel::get_states();
  $data['states'] = [];
  foreach($states as $result){
    if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
      $data['states'][] = [
        'code' => base64_encode($result->ST_CODE),
        'name' => $result->ST_NAME,
      ];
    }

    if(Auth::user()->role_id == '7' || Auth::user()->role_id == '27'){
      $data['states'][] = [
        'code' => base64_encode($result->ST_CODE),
        'name' => $result->ST_NAME,
      ];
    }
  }

  $data['filter']   = implode('&', array_merge($request_array));
//end set title

//buttons
  $data['buttons']    = [];
  $data['buttons'][]    = [
    'href' => Common::generate_url($this->action.'/officer-list'),
    'name' => 'List of BLO/PRO/PO',
    'target' => false,
  ];
  $data['action']         = Common::generate_url($this->action.'/officer-list/post');
  $filter_election = [
    'st_code'   => $data['st_code'],
    'ac_no'     => $data['ac_no'],
    'paginate'  => true,
    'restricted_ps' => ['99','128','129','91','189']
  ];


  $results   = [];
  $filter_ps = [
    'st_code'   => $data['st_code'],
    'ac_no'     => $data['ac_no'],
    'paginate'  => false,
    'restricted_ps' => ['99','128','129','91','189']
  ];

  $data['polling_stations'] = [];
  $results    =   PollingStation::get_polling_stations($filter_ps);
  foreach ($results as $result) {
    $data['polling_stations'][] = [
      'ps_no'   => $result['PS_NO'],
      'ps_name' => $result['PS_NAME_EN'],
    ];
  }

  if($id != '0'){
    $data['encrpt_id'] = $id;
    $id     = decrypt_string($id);
    $object = PollingStationOfficerModel::get_officer(['id' => $id]);
  }

  $data['id'] = $id;

  if($request->old('ps_no')){
    $data['ps_no']  = $request->old('ps_no');
  }else if(isset($object) && $object){
    $data['ps_no']  = $object['ps_no'];
  }else{
    if($request->has('ps_no')){
      $data['ps_no']  = $request->ps_no;
    }else{
      $data['ps_no']  = '';
    }
  }

  if($request->old('name')){
    $data['name']  = $request->old('name');
  }else if(isset($object) && $object){
    $data['name']  = $object['name'];
  }else{
    $data['name']  = ''; 
  }

  if($request->old('mobile')){
    $data['mobile']  = $request->old('mobile');
  }else if(isset($object) && $object){
    $data['mobile']  = $object['mobile_number'];
  }else{
    $data['mobile']  = ''; 
  }

  if($request->old('status')){
    $data['status']  = $request->old('status');
  }else if(isset($object) && $object){
    $data['status']  = $object['is_active'];
  }else{
    $data['status']  = ''; 
  }

  if($request->old('role_id')){
    $data['role_id']  = $request->old('role_id');
  }else if(isset($object) && $object){
    $data['role_id']  = $object['role_id'];
  }else{
    if($request->has('role_id')){
      $data['role_id']  = $request->role_id;
    }else{
      $data['role_id']  = '';
    }
  }

  if($request->old('pin')){
    $data['pin']  = $request->old('pin');
  }else{
    $data['pin']  = ''; 
  }
  if($request->old('pin_confirmation')){
    $data['pin_confirmation']  = $request->old('name');
  }else{
    $data['pin_confirmation']  = ''; 
  }

  $data['roles']      = [
    [
      'role_id' => '',
      'name'    => 'Select',
    ],
    [
      'role_id' => '33',
      'name'    => 'BLO',
    ],
    [
      'role_id' => '34',
      'name'    => 'PO',
    ],
    [
      'role_id' => '35',
      'name'    => 'PRO',
    ]
  ];
  $data['user_data']  =  Auth::user();
  $data['heading_title_with_all'] = $data['heading_title'];
  return view($this->view.'.officer-list-form', $data);

  try{}catch(\Exception $e){
    return Redirect::to($this->base.'/dashboard');
  }

}

public function post_officer(OfficerRequest $request){

  $no_allowed_office = $this->no_allowed_po;
  $no_allowed_blo    = $this->no_allowed_blo;
  $merge            = [];
  $merge['st_code'] = \Auth::user()->st_code;
  $merge['ac_no']   = \Auth::user()->ac_no;

  if(in_array($request->role_id,['33','35'])){
    $merge['pin'] = '';
  }
  $request->merge($merge);

  $no_active_officer = PollingStationOfficerModel::count_officer($request->all());

  if(in_array($request->role_id,['35']) && $no_active_officer > 0){
    Session::flash('status',0);
    Session::flash('flash-message',"You can only add 1 PRO to a Polling Station.");
    return Redirect::back()->withInput($request->all());
  }

  if(in_array($request->role_id,['33']) && $no_allowed_blo > 4){
    Session::flash('status',0);
    Session::flash('flash-message',"You can only add ".$this->no_allowed_blo." BLO to a Polling Station.");
    return Redirect::back()->withInput($request->all());
  }

  if($request->role_id == '34' && $no_active_officer > $no_allowed_office){
    Session::flash('status',0);
    Session::flash('flash-message',"You can only add ".$no_allowed_office." PO to a Polling Station.");
    return Redirect::back()->withInput($request->all());
  }

  DB::beginTransaction();
  try{  
    $result   = PollingStationOfficerModel::add_officer($request->all());
  }
  catch(\Exception $e){
    DB::rollback();
    Session::flash('status',0);
    Session::flash('flash-message',"Please Try Again.");
    return Redirect::back();
  }
  DB::commit();

  $role = '';
  $app_link = 'https://resultapi.eci.gov.in/boothlevel/public/apk/BoothApp_rc1.apk';
  if($request->role_id == '34'){
    $role = 'PO';
  }else if($request->role_id == '33'){
    $role = 'BLO';
  }else if($request->role_id == '35'){
    $role = 'PRO';
  }
  $polling_name = '';
  $poll_station = PollingStation::get_polling_station([
    'st_code' => $this->st_code,
    'ac_no'   => $this->ac_no,
    'ps_no'   => $request->ps_no
  ]);
  if($poll_station){
    $polling_name              = $poll_station['PS_NAME_EN'];
  }

  try{
    $sms_message = "Your number has been registered for Booth App as a ".$role." for Polling station no. ".$request->ps_no.'-'.$polling_name." by the Returning officer.  Please download Booth App ".$app_link;
    $msgstatus = SmsgatewayHelper::gupshup($request->mobile, $sms_message);
  }catch(\Exception $e){

  }


  Session::flash('status',1);
  Session::flash('flash-message',"Profile has been updated successfully.");
  return redirect($this->base.'/booth-app/officer-list');
}


public function get_voter_list(Request $request){

  $data = [];
  $request_array = [];

  $data['st_code']  = NULL;
  $data['ac_no']    = NULL;
  $data['ps_no']    = NULL;

  if($request->has('st_code')){
    $data['st_code'] = base64_decode($request->st_code);
    $request_array[] = 'state='.$request->st_code;
  }
  if($request->has('ac_no')){
    $data['ac_no'] = base64_decode($request->ac_no);
    $request_array[] = 'ac_no='.$request->ac_no;
  }
  if($request->has('ps_no')){
    $data['ps_no']    = $request->ps_no;
    $request_array[]  = 'ps_no='.$request->ps_no;
  }

  if($this->role_id == '19'){
    $data['st_code'] = $this->st_code;
    $data['ac_no'] = $this->ac_no;
  }

//set title
  $title_array  = [];
  $data['heading_title'] = 'Electoral List';

  if($data['st_code']){
    $state_object = StateModel::get_state_by_code($data['st_code']);
    if($state_object){
      $title_array[]  = "State: ".$state_object['ST_NAME'];
    }
  }
  if($data['ac_no']){
    $ac_object = AcModel::get_record(['state' => $data['st_code'], 'ac_no' => $data['ac_no']]);
    if($ac_object){
      $title_array[]  = "AC: ".$ac_object['ac_name'];
    }
  }
  if($data['ps_no']){
    $ps_object = SpmVoterListModel::get_polling_station(['st_code' => $data['st_code'], 'ac_no' => $data['ac_no'], 'ps_no' => $data['ps_no']]);
    if($ps_object){
      $title_array[]  = "PS: ".$ps_object['ps_name_en'];
    }
  }

  $data['filter_buttons'] = $title_array;



  $data['filter']   = implode('&', array_merge($request_array));
//end set title
  $data['action']     = Common::generate_url($this->action.'/voter-list');


//dd($data['action']);
//booth-app/voter-list

  $results                = [];
  $filter_for_ps = [
    'st_code'  => $data['st_code'],
    'ac_no'    => $data['ac_no'],
    'paginate'  => false
  ];

  $data['results_ps'] = [];
  $results_ps    =   SpmVoterListModel::get_polling_stations($filter_for_ps);
  foreach ($results_ps as $result) {
    $data['results_ps'][] = [
      'id'      => $result['ps_no'],
      'ps_no'   => $result['ps_no'],
      'ps_name' => $result['ps_no'].'-'.$result['ps_name_en'],
    ];
  }

//buttons
  $data['buttons']    = [];
  $filter_for_voter = array_merge($filter_for_ps,['ps_no' => $data['ps_no']]);
  $button_filter = [];
  foreach($filter_for_voter as $key => $value){
    if(trim($value) && !empty($value) && !in_array($key,['paginate'])){
      $button_filter[] = $key.'='.$value;
    }
  }


  if($data['ps_no']){
    $slip_path = SpmPollingStation::get_slip_path([
      'st_code' => $this->st_code,
      'ac_no' => $this->ac_no,
      'ps_no' => $data['ps_no'],
    ]);

    if($slip_path){
      $data['add_download_log'] = Common::generate_url('booth-app/add_download_log/0');
      $data['slip_download_button'] = $slip_path['slip_path'];
    }
  }



  $data['results']      = [];
  if($data['ps_no']){
    $results              = SpmVoterListModel::get_vooter_list($filter_for_voter);
    foreach ($results as $ierate_res) {
      $gender = '';
      if(strtolower($ierate_res->gender) == 'm'){
        $gender = "Male";
      }else if(strtolower($ierate_res->gender) == 'f'){
        $gender = "Female";
      }else{
        $gender = $ierate_res->gender;
      }
      $data['results'][] = [
        'epic_no' => $ierate_res->epic_no,
        'name'    => $ierate_res->name_en,
        'gender'              => $gender,
        'voter_serial_no'     => $ierate_res->voter_serial_no,
        'href'                => Common::generate_url('booth-app/download-electoral-list/'.$ierate_res->id),
      ];
    }
  }


  $data['user_data']  =   Auth::user();
  $data['heading_title_with_all'] = $data['heading_title'];

  return view($this->view.'.voting-list', $data);

  try{}catch(\Exception $e){
    return Redirect::to($this->base.'/dashboard');
  }

}

public function polling_station(Request $request){
    $data                   = [];
    $data['buttons']        = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "List of Polling Station";

    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];

    $filter = [
      'st_code' => $st_code,
      'ac_no'   => $ac_no,
      'restricted_ps' => $this->restricted_ps
    ];

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url($this->action.'/dashboard'),
      'name' => 'Back',
      'target' => false,
    ];
    
    $data['results']     = [];

    $ps_results = PollingStation::get_polling_stations($filter);

    foreach ($ps_results as $key => $iterate_ps) {
      $iterate_restricted = $iterate_ps['PS_NO'];

        $filter_for_voters = array_merge($filter,['ps_no' => $iterate_restricted]);

        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station($filter_for_voters);
        if($poll_station){
          $poll_station_name = $poll_station['PS_NAME_EN'];
        }

        $ac_name = '';
        $ac_no = '';
        $ac_object = AcModel::get_record([
          'state' => $poll_station['ST_CODE'],
          'ac_no' => $poll_station['AC_NO']
        ]);
        if($ac_object){
          $ac_name = $ac_object['ac_name'];
          $ac_no = $ac_object['ac_no'];
        }

        $st_name = '';
        $st_object = StateModel::get_state_by_code($poll_station['ST_CODE']);
        if($st_object){
          $st_name = $st_object['ST_NAME'];
        }

        $data['results'][]    = [
          'st_name' => $st_name,
          'ac_no'   => $ac_no,
          'ac_name' => $ac_name,
          'ps_no'   => $iterate_restricted,
          'ps_name' => $poll_station_name,
        ];
    }
    //form filters
    $data['filter_action'] = Common::generate_url("booth-app/polling-station");
    $form_filter_array = [
      'st_code'     => true,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation' => false
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;
    $data['user_data']      = Auth::user();

    return view($this->view.'.no-of-polling-station', $data);   
}

public function get_officers(Request $request){
  $data                   = [];
  $data['role_id']        = NULL;
  $is_activated           = NULL;
  if($request->has('is_activated')){
    $is_activated = $request->is_activated;
  }

  //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url($this->action.'/dashboard'),
      'name' => 'Back',
      'target' => false,
    ];

    $data['heading_title']  = "List of Officers";
    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];
    $ps_no          = $request_filter['ps_no'];
    $role_id         = $request_filter['role_id'];

      $request_array = [];
      //set title
      $title_array  = [];
      if($st_code){
        $state_object = StateModel::get_state_by_code($st_code);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      if($ac_no && $st_code){
        $ac_object = AcModel::get_record(['state' => $st_code, 'ac_no' => $ac_no]);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
      }
      if($ac_no && $st_code && $ps_no){
        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station([
          'state' => $st_code, 
          'ac_no' => $ac_no,
          'ps_no'   => $ps_no
        ]);
        if($poll_station){
          $title_array[]  = "PS: ".$poll_station['PS_NAME_EN'];
        }
      }

      $data['filter']   = implode('&', array_merge($request_array));
      $filter_election = [
        'st_code'   => $st_code,
        'ac_no'     => $ac_no,
        'ps_no'     => $ps_no,
        'paginate'  => true,
        'restricted_ps' => ['99','128','129','91','189'],
        'role_id'       => $role_id,
        'not_po'        => true,
        'is_activated'   => $is_activated
      ];

      $data['results']        = [];
      $officers_data          = [];
      $results                =   PollingStationOfficerModel::get_officers($filter_election);
      $data['pag_results']    = $results;
      foreach ($results as $officer) {

        $role = '';
        if($officer->role_id == '33'){
          $role = 'BLO';
        }
        if($officer->role_id == '35'){
          $role = 'PRO';
        }
        if($officer->role_id == '34'){
          $role = 'PO';
        }

        $st_name = '';
        $state_object = StateModel::get_state_by_code($officer->st_code);
        if($state_object){
          $st_name = $state_object['ST_NAME'];
        }

        $ac_name = '';
        $ac_object = AcModel::get_record(['state' => $officer->st_code, 'ac_no' => $officer->ac_no]);
        if($ac_object){
          $ac_name = $ac_object['ac_name'];
        }

        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station([
          'st_code' => $officer->st_code,
          'ac_no'   => $officer->ac_no,
          'ps_no'   => $officer->ps_no
        ]);
        if($poll_station){
          $poll_station_name = $poll_station['PS_NAME_EN'];
        }

        $is_login = false;
        if(strtotime($officer->login_time)){
          $is_login = true;
        }

        $officers_data[] =  [
          'st_code'       => $officer->st_code,
          'st_name'     => $st_name,
          'ac_no'       => $officer->ac_no,
          'ac_name'     => $ac_name,
          'ps_no'         => $officer->ps_no,
          'ps_name'       => $poll_station_name,
          'name'          => $officer->name,
          'mobile'        => $officer->mobile_number,
          'designation'   => $role,
          'is_login'      =>  $is_login
        ];
      }

      $data['user_data']  = Auth::user();
      $data['results']    = $officers_data;

      $data['heading_title_with_all'] = $data['heading_title'];

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }
      
      //form filters
      $data['filter_action'] = Common::generate_url("booth-app/officers");
      $form_filter_array = [
        'st_code'     => true,
        'ac_no'       => true, 
        'ps_no'       => true, 
        'designation' => true
      ];
      $form_filters = Common::get_form_filters($form_filter_array, $request);

      //activate filter
      $is_activated_value   = [];
      $is_activated_value[] = [
        'name'  => 'Activated',
        'id'    => 'yes',
      ];
      $is_activated_value[] = [
        'name'  => 'Not-Activated',
        'id'    => 'no',
      ];

      $activated_array = [];
      foreach ($is_activated_value as $iterate_activate) {
        $is_active = false;
        if($is_activated == $iterate_activate['id']){
          $is_active = true;
        }
        $activated_array[] = [
          'name'    => $iterate_activate['name'],
          'id'      => $iterate_activate['id'],
          'active'  => $is_active
        ];
      }

      $form_filters[] = [
        'id'      => 'is_activated',
        'name'    => 'Status',
        'results' => $activated_array
      ];

      
      $data['form_filters'] = $form_filters;
      return view($this->view.'.officers', $data);
 
}

//e-roll download
public function get_e_roll_download(Request $request){

    $data                   = [];   
    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url($this->action.'/dashboard'),
      'name' => 'Back',
      'target' => false,
    ];

    $data['heading_title']  = "List of E-Download";
    
    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];
    $ps_no          = $request_filter['ps_no'];
    $role_id         = $request_filter['role_id'];

      $request_array = [];
      //set title
      $title_array  = [];
      if($st_code){
        $state_object = StateModel::get_state_by_code($st_code);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      if($ac_no && $st_code){
        $ac_object = AcModel::get_record(['state' => $st_code, 'ac_no' => $ac_no]);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
      }
      if($ac_no && $st_code && $ps_no){
        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station([
          'state' => $st_code, 
          'ac_no' => $ac_no,
          'ps_no'   => $ps_no
        ]);
        if($poll_station){
          $title_array[]  = "PS: ".$poll_station['PS_NAME_EN'];
        }
      }
      $data['filter_buttons'] = $title_array;

      $data['filter']   = implode('&', array_merge($request_array));
      $filter_election = [
        'st_code'   => $st_code,
        'ac_no'     => $ac_no,
        'ps_no'     => $ps_no,
        'paginate'  => true,
        'restricted_ps' => ['99','128','129','91','189'],
        'role_id'       => $role_id
      ];
  
      $data['results']        = [];
      $officers_data          = [];
      $results                = SpmStaticsModel::get_statics(array_merge($filter_election,['download_time' => true]));

      foreach ($results as $officer) {

        $role = '';
        if($officer->role_id == '33'){
          $role = 'BLO';
        }
        if($officer->role_id == '35'){
          $role = 'PRO';
        }
        if($officer->role_id == '34'){
          $role = 'PO';
        }

        $st_name = '';
        $state_object = StateModel::get_state_by_code($officer->st_code);
        if($state_object){
          $st_name = $state_object['ST_NAME'];
        }

        $ac_name = '';
        $ac_object = AcModel::get_record(['state' => $officer->st_code, 'ac_no' => $officer->ac_no]);
        if($ac_object){
          $ac_name = $ac_object['ac_name'];
        }

        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station([
          'st_code' => $officer->st_code,
          'ac_no'   => $officer->ac_no,
          'ps_no'   => $officer->ps_no
        ]);
        if($poll_station){
          $poll_station_name = $poll_station['PS_NAME_EN'];
        }

        $name = '';
        $mobile = '';
        $officer_detail = PollingStationOfficerModel::get_officer(['id' => $officer->officer_id]);

        if($officer_detail){
          $name   = $officer_detail['name'];
          $mobile = $officer_detail['mobile_number'];
        }

        $download_time = '';
        if($officer->download_time){
          $download_time = round($officer->download_time/60,2).' minute';
        }

        $officers_data[] =  [
          'st_code'       => $officer->st_code,
          'st_name'     => $st_name,
          'ac_no'       => $officer->ac_no,
          'ac_name'     => $ac_name,
          'ps_no'         => $officer->ps_no,
          'ps_name'       => $poll_station_name,
          'name'          => $name,
          'mobile'        => $mobile,
          'designation'   => $role,
          'download_time' =>  $download_time
        ];
      }

      $data['user_data']  = Auth::user();
      $data['results']    = $officers_data;

      $data['heading_title_with_all'] = $data['heading_title'];

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }
      
      //form filters
      $data['filter_action'] = Common::generate_url("booth-app/e-roll-download");
      $form_filter_array = [
        'st_code'     => true,
        'ac_no'       => true, 
        'ps_no'       => true, 
        'designation' => true
      ];
      $form_filters = Common::get_form_filters($form_filter_array, $request);
      
      $data['form_filters'] = $form_filters;
      return view($this->view.'.e-roll-download', $data);
}


//poll details
public function get_poll_detail(Request $request){
    $data                   = [];
    $data['buttons']        = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "List of Polling Station";

    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];

    $filter = [
      'st_code' => $st_code,
      'ac_no'   => $ac_no,
      'restricted_ps' => $this->restricted_ps
    ];

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url($this->action.'/dashboard'),
      'name' => 'Back',
      'target' => false,
    ];
    
    $data['results']     = [];

    $ps_results = PollingStation::get_polling_stations($filter);

    foreach ($ps_results as $key => $iterate_ps) {

      $iterate_restricted = $iterate_ps['PS_NO'];

      $is_started = SpmStaticsModel::get_static(array_merge($filter,[
        'ps_no' => $iterate_ps['PS_NO'], 
        'is_started' => true
      ]));

      $is_end = SpmStaticsModel::get_static(array_merge($filter,[
        'ps_no' => $iterate_ps['PS_NO'],
        'is_end' => true
      ]));

      $iterate_restricted = $iterate_ps['PS_NO'];

        $filter_for_voters = array_merge($filter,['ps_no' => $iterate_restricted]);

        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station($filter_for_voters);
        if($poll_station){
          $poll_station_name = $poll_station['PS_NAME_EN'];
        }

        $ac_name = '';
        $ac_no = '';
        $ac_object = AcModel::get_record([
          'state' => $poll_station['ST_CODE'],
          'ac_no' => $poll_station['AC_NO']
        ]);
        if($ac_object){
          $ac_name = $ac_object['ac_name'];
          $ac_no = $ac_object['ac_no'];
        }

        $st_name = '';
        $st_object = StateModel::get_state_by_code($poll_station['ST_CODE']);
        if($st_object){
          $st_name = $st_object['ST_NAME'];
        }

        $data['results'][]    = [
          'st_name' => $st_name,
          'ac_no'   => $ac_no,
          'ac_name' => $ac_name,
          'ps_no'   => $iterate_restricted,
          'ps_name'   => $poll_station_name,
          'is_start'  => ($is_started['poll_start_time'])?$is_started['poll_start_time']:'No',
		   'is_end'    => ($is_end['poll_start_time'])?$is_end['poll_end_time']:'No',
        ];
    }
    //form filters
    $data['filter_action'] = Common::generate_url("booth-app/polling-station");
    $form_filter_array = [
      'st_code'     => true,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation' => false
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;
    $data['user_data']      = Auth::user();

    return view($this->view.'.poll-detail', $data);   
}


//chandrabushan code
public  function download_electoral_list($id, Request $request) {

  error_reporting(0);
  ini_set('max_execution_time', 0);
  ini_set("pcre.backtrack_limit", "50000000000000000000000");
  ini_set('memory_limit', '-1');
  $xss = new xssClean;
  $container = [];

  $getStatusName = DB::connection("spm")->table('voter_info')->where("id", $id)->first();
  $m_language  =   DB::connection("spm")->table('m_language')->where("st_code", Auth::user()->st_code)->first();
  $master_header = DB::connection("spm")->table('master_header')->where("state_code", Auth::user()->st_code)->where("language_id", $m_language->id)->first();


  $hindi_gender = ["M" => "पु", "F"=> "म"];

  $psname_gender = ["91"=> "प्राथमिक विद्यालय बदनपुर",
        "99" => "आर्य समाज महर्षि दयानंद विद्यालय हमीरपुर क.न.- 2",
        "128" => "पूर्व माध्यमिक विद्यालय कुछेछा क0न0- 1",
        "129" => "पूर्व माध्यमिक विद्यालय कुछेछा क0न0- 2",
        "189" => "प्राथमिक विद्यालय नरायनपुर"];

  if(count($getStatusName) > 0){
    $value = $getStatusName;
    $ps_name_hindi = '';
    $gender_hindi = '';
    if(!empty($psname_gender[$value->ps_no])){
      $ps_name_hindi = $psname_gender[$value->ps_no];
    }
    if(!empty($hindi_gender[$value->gender])){
      $gender_hindi = $hindi_gender[$value->gender];
    }
    $data["id"] = $value->id;
    $data["qr_code"] = $value->bar_code;
    $data["name_en"] = trim($xss->clean_input($value->name_en));
    $data["name_regional"] = trim($xss->clean_input($value->name_regional));
    $data["epic_no"] = $value->epic_no;
    $data["father_name"] = trim($xss->clean_input($value->father_name));
    $data["father_name_regional"] = trim($xss->clean_input($value->father_name_regional));
    $data["qr_code_url"] = $value->qr_code_url;



    $data["husband_name"] = $value->husband_name;
    $data["husband_name_regional"] = $value->husband_name_regional;


    $polling_station = DB::connection("spm")->table('polling_station')->where("st_code", $value->state_code)
    ->where("ac_no", $value->ac_no)->where("ps_no", $value->ps_no)->first();

    $state_master = DB::connection("spm")->table('state_master')->where("st_code", $value->state_code)->first();

    $data["state_name_en"] = $state_master->st_name ?? null;
    $data["state_name_regional"] = $state_master->st_name_hi ?? null;


    $data["ps_name_en"] = $polling_station->ps_name_en ?? null;
    $data["ps_name_regional"] = $ps_name_hindi;/////////////
    $data["part_no"] = $value->part_no;
    $data["voter_serial_no"] = $value->voter_serial_no;
    $data["polling_date"] = $value->polling_date;
    $data["ceo_website"] = $value->ceo_website;
    $data["poll_start_time"] = $value->poll_start_time;
    $data["poll_end_time"] = $value->poll_end_time;
    $data["call_center_toll_free_no"] = $value->call_center_toll_free_no;
    $data["ps_no"] = $value->ps_no;
    $data["unique_generated_id"] = $value->unique_generated_id;
    $data["age"] = $value->age;
    $data["gender"] = $value->gender;
    $data["gender_hindi"] = $gender_hindi;/////////////
    $hexAsString = $value->image;
    assert( '0x' == substr( $hexAsString, 0, 2 ));
    $hexDigits = substr( $hexAsString, 2, strlen( $hexAsString ) -2 );
    $imageData1 =pack('H*', $hexDigits);
    $imageData1 = base64_encode($imageData1);
    $data["image"] = $imageData1;
    $container = (object) $data;

  }

  $data = [
    'getResults' =>$container,
    "m_language"=>$m_language,
    "master_header"=>$master_header,
    'spm_url' => config("public_config.spm_url")
  ];

  //add download log
  $request->merge(['ps_no' => $value->ps_no]);
  $this->add_download_log($id, $request);
  

  $currentdate = Carbon::today()->toDateString();
  $timestamp = now()->timestamp;
  $name_excel = 'electoral_slip';
  $pdf = \MPDF::loadView($this->view.'.individual_electoral_pdf',$data, [], ['format' => 'A4']);
  return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');



}

//get scan data

public function get_sacn_data(Request $request){
    $data                   = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "Scan Electoral Report";

    $request_filter = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];

    $filter = [
      'st_code' => $st_code,
      'ac_no'   => $ac_no,
      'restricted_ps' => $this->restricted_ps
    ];

    
    $data['results']     = [];

    $ps_results = PollingStation::get_polling_stations($filter);

    foreach ($ps_results as $key => $iterate_ps) {

      $iterate_restricted = $iterate_ps['PS_NO'];

      $is_started = SpmStaticsModel::get_static(array_merge($filter,[
        'ps_no' => $iterate_ps['PS_NO'], 
        'is_started' => true
      ]));

      $is_end = SpmStaticsModel::get_static(array_merge($filter,[
        'ps_no' => $iterate_ps['PS_NO'],
        'is_end' => true
      ]));

      $iterate_restricted = $iterate_ps['PS_NO'];

        $filter_for_voters = array_merge($filter,['ps_no' => $iterate_restricted]);

        $poll_station_name = '';
        $poll_station = PollingStation::get_polling_station($filter_for_voters);
        if($poll_station){
          $poll_station_name = $poll_station['PS_NAME_EN'];
        }

        $ac_name = '';
        $ac_no = '';
        $ac_object = AcModel::get_record([
          'state' => $poll_station['ST_CODE'],
          'ac_no' => $poll_station['AC_NO']
        ]);
        if($ac_object){
          $ac_name = $ac_object['ac_name'];
          $ac_no = $ac_object['ac_no'];
        }

        $st_name = '';
        $st_object = StateModel::get_state_by_code($poll_station['ST_CODE']);
        if($st_object){
          $st_name = $st_object['ST_NAME'];
        }

        //get data ps wise
        $total_qr   = 0;
        $total_epic = 0;
        $total_bs   = 0;
        $total_name = 0;
       
		
		//$doughnut_object = SpmStaticsModel::get_scan_count($filter_for_voters);
        $doughnut_object = SpmVoterInfo::get_scan_count_from_poll_table($filter_for_voters);
		
        if($doughnut_object){
          $total_qr   = $doughnut_object['total_qr'];
          $total_epic = $doughnut_object['total_epic'];
          $total_bs   = $doughnut_object['total_bs'];
          $total_name = $doughnut_object['total_name'];
        }

        $data['results'][] = [
          'st_name' => $st_name,
          'ac_no'   => $ac_no,
          'ac_name' => $ac_name,
          'ps_no'   => $iterate_restricted,
          'ps_name'   => $poll_station_name,
          'total_qr'     => $total_qr,
          'total_epic'   => $total_epic,
          'total_name'   => $total_name,
          'total_booth_id'   => $total_bs,
        ];
    }
    //form filters
    $data['filter_action'] = Common::generate_url("booth-app/polling-station");
    $form_filter_array = [
      'st_code'     => true,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation' => false
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;

    return \Response::json($data);  

}

public function add_download_log($elector_id = 0, Request $request){
  try{
    $data = [];
    $data['st_code']  = $this->st_code;
    $data['ac_no']    = $this->ac_no;
    $data['ps_no']    = 0;
    if($request->has('ps_no')){
      $data['ps_no'] = $request->ps_no;
    }
    $data['created_by'] = Auth::id();
    $data['updated_by'] = Auth::id();
    $data['role_id']    = Auth::user()->role_id;
    $data['created_at'] = date("Y:m:d h:s:i");
    $data['updated_at'] = date("Y:m:d h:s:i");
    $data['name']           = Auth::user()->officername;
    $data['elector_id'] = $elector_id;
    DB::table("download_booth_sliplogs")->insert($data);
  }catch(\Exception $e){

  }
}



}  // end class