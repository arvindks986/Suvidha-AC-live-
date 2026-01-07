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
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel,PollingStationOfficerReadModel, PsSectorOfficer, TblPollSummaryModel, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, JsonFile, TblPwdVoterModel, TblProDiaryModel, InfraMapping, MockPoll, IncidentStatistics, ProDiaryFinal, TblAnalyticsDashboardModel,TblAnalyticsDashboardReadModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;
use App\Events\Admin\BoothAppRevamp\PusherEvent;
use Pusher\Pusher;

//current

ini_set("memory_limit","850M");
set_time_limit('240');
ini_set("pcre.backtrack_limit", "5000000");

class PollingController extends Controller {

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

  public function get_polling_station(Request $request){
	
    $data = [];
    $request_array = [];

    //set title
    $title_array  = [];
    $data['heading_title'] = 'List of Polling Officers';

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


    $data['filter']   = implode('&', array_merge($request_array));
    //end set title

    //buttons
    $data['buttons']    = [];
    $data['action']     = url($this->action.'/officer-list');
    $data['reset_otp_link']  = Common::generate_url('booth-app-revamp/reset_otp');
    $data['reset_pin_link']  = Common::generate_url('booth-app-revamp/reset_pin');
    $results                = [];
    $filter_election = [
      'st_code'   => $this->st_code,
      'ac_no'     => $this->ac_no,
      'paginate'  => false,
      'restricted_ps' => NULL
    ];

    $data['add_new_url'] = Common::generate_url($this->action.'/officer-list/add/0');
    $data['add_sm_url'] = Common::generate_url($this->action.'/assign-so-new');


    $max_po           = [];
    $max_blo          = [];
    $max_sm           = [];
    $data['results']  = [];
	
	
    
    $results   =   PollingStation::get_polling_stations($filter_election);
   
    $data['pag_results'] = $results;
    foreach ($results as $result) {
      $blo  = [];
      $pro  = [];
      $po   = [];
      $sm   = [];
	
      $officers = PollingStationOfficerModel::get_officers(array_merge($filter_election,['ps_no' => $result['PS_NO']]));
      foreach($officers as $officer){

        //      if($officer['role_id'] == '33'){
        //      $blo[$officer['role_level']] = [
        //      'name'    => $officer['name'],
        //      'mobile'  => $officer['mobile_number'],
        //      'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($officer['id'])),
        //      'is_active' => ($officer['is_active'])?'Enable':'Disable',
        //      'role_level' => $officer['role_level']
        //   ];
        // }
       
        if($officer['role_id'] == '35'){
          $pro[$officer['role_level']] = [
            'name'    => $officer['name'],
            'mobile'  => $officer['mobile_number'],
            'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($officer['id'])),
            'is_active' => ($officer['is_active'])?'Enable':'Disable',
            'role_level' => $officer['role_level']
          ];
        }
        if($officer['role_id'] == '34'){
          $po[$officer['role_level']] = [
            'name'    => $officer['name'],
            'mobile'  => $officer['mobile_number'],
            'pin'  => $officer['pin'],
            'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($officer['id'])),
            'is_active' => ($officer['is_active'])?'Enable':'Disable',
            'role_level' => $officer['role_level']
          ];
        }
      }

      $sm_officers = PollingStationOfficerModel::get_sos(array_merge($filter_election,['ps_no' => $result['PS_NO']]));
		
      foreach ($sm_officers as $iterate_sm) {
        $sm[$iterate_sm['role_level']] = [
            'name'    => $iterate_sm['name'],
            'mobile'  => $iterate_sm['mobile_number'],
            'href'    => Common::generate_url($this->action.'/officer-list/add/'.encrypt_string($iterate_sm['id'])),
            'is_active' => ($iterate_sm['is_active'])?'Enable':'Disable',
            'role_level' => $iterate_sm['role_level']
        ];
      }


      uasort($po, function($a, $b){
        return $a['role_level'] - $b['role_level'];
      });

      uasort($blo, function($a, $b){
        return $a['role_level'] - $b['role_level'];
      });

      uasort($pro, function($a, $b){
        return $a['role_level'] - $b['role_level'];
      });

      uasort($sm, function($a, $b){
        return $a['role_level'] - $b['role_level'];
      });

      $max_po[]     = count($po);
      $max_blo[]    = count($blo);
      $max_pro[]    = count($pro);
      $max_sm[]     = count($sm);
      $data['results'][] = [
        'ps_no'   => $result['PS_NO'],
        'ps_name' => $result['PS_NAME_EN'],
        'blo'     => $blo,
        'pro'     => $pro,
        'po'      => $po,
        'sm'      => $sm
      ];
    }


$data['max_po']     =  $this->no_allowed_po;//max($max_po);
$data['max_blo']    =  $this->no_allowed_blo;//max($max_po);
$data['max_pro']    =  $this->no_allowed_pro;//max($max_po);
$data['max_sm']     =  $this->no_allowed_sm;//max($max_po);

$data['user_data']  =  Auth::user();

$data['heading_title_with_all'] = $data['heading_title'];

if($request->has('is_excel')){
  if(isset($title_array) && count($title_array)>0){
    $data['heading_title'] .= "- ".implode(', ', $title_array);
  }
  return $data;
}



return view($this->view.'.officer-list', $data);

try{}catch(\Exception $e){
  return Redirect::to($this->base.'/dashboard');
}

}

//exempted turnout start

public function turnout_exempted_state_wise($request, $filter = array()){
  

  $data                   = [];
  $data['voter_turnouts'] = [];
  if(count($filter) == 0){
    $filter = [
      'phase_no' => $request['phase_no'],
      'st_code'  => $this->st_code
    ];
  }

  $grand_e_male   = 0;
  $grand_e_female = 0;
  $grand_e_other  = 0;
  $grand_e_total  = 0;
  $grand_male   = 0;
  $grand_female = 0;
  $grand_other  = 0;
  $grand_total  = 0;
  $grand_queue  = 0;
  
  $states_results = StateModel::get_states($filter);
  
  foreach ($states_results as $key => $iterate_state) {

    $filter_for_voters = [
      'st_code' => $iterate_state->ST_CODE,
      'phase_no'    => $request['phase_no'],
	  'booth_exemp_status' =>1
    ];
	
    $stats_sum = TblAnalyticsDashboardModel::total_statics_sum_exem_new($filter_for_voters);

    $male   = $stats_sum['male_voters'];
    $female = $stats_sum['female_voters'];
    $other  = $stats_sum['other_voters'];
    $total  = $male+$female+$other;
    $queue_voters = $stats_sum['queue_voters'];
  if($queue_voters <= 0){
    $queue_voters = 0;
  }

    $electors   = TblAnalyticsDashboardModel::get_aggregate_voters_exem_new($filter_for_voters);
	$e_male   = $electors['e_male'];
	$e_female = $electors['e_female'];
	$e_other  = $electors['e_other'];
    $e_total  = $e_male+$e_female+$e_other;


    $grand_e_male   += $e_male;
    $grand_e_female += $e_female;
    $grand_e_other  += $e_other;
    $grand_e_total  += $e_total;
    $grand_male   += $male;
    $grand_female += $female;
    $grand_other  += $other;
    $grand_total  += $total;


    $grand_queue  += $queue_voters;


    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }
    if($queue_voters<0){
      $voters_queue = "No Information";
    }else if($queue_voters<10){
      $voters_queue = "less than 10";
    }else if($queue_voters >= 10 && $queue_voters<20){
      $voters_queue = "10 to 20";
    }else if($queue_voters >= 20 && $queue_voters<30){
      $voters_queue = "20 to 30";
    }else if($queue_voters >= 30){
      $voters_queue = "30+";
    }else{
      $voters_queue = '';
    }

    $data['voter_turnouts'][] = [
      'st_name'         => $iterate_state['ST_NAME'],
      'st_code'         => $iterate_state['ST_CODE'],
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => $voters_queue,
      'percentage'      => $percentage
    ];

  }

  $grand_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total > 0){
    $grand_percentage = round($grand_total/$grand_e_total*100,2);
  }

  $data['voter_turnouts'][]    = [
    'st_name' => 'Total',
    'st_code' => '',
    'male'    => $grand_male,
    'female'  => $grand_female,
    'other'   => $grand_other,
    'total'   => $grand_total,
    'e_male'    => $grand_e_male,
    'e_female'  => $grand_e_female,
    'e_other'   => $grand_e_other,
    'e_total'   => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage' => $grand_percentage
  ];

  $data['grand_percentage']         = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;
  
  
return $data;
  
}

public function turnout_ac_wise_exempt($request, $filter = array()){
  $data                   = [];
  $data['voter_turnouts'] = [];
  if(count($filter) == 0){
    $filter = [
      'phase_no' => $this->phase_no,
      'st_code'  => $this->st_code
    ];
  }

  $grand_e_male   = 0;
  $grand_e_female = 0;
  $grand_e_other  = 0;
  $grand_e_total  = 0;
  $grand_male   = 0;
  $grand_female = 0;
  $grand_other  = 0;
  $grand_total  = 0;
  $grand_queue  = 0;

  $acs_results = AcModel::get_acs($filter);
  

  foreach ($acs_results as $key => $iterate_ac) {
    $filter_for_voters = [
      'phase_no'    => $this->phase_no,
      'st_code' => $iterate_ac['st_code'],
      'ac_no'   => $iterate_ac['ac_no'],
	  'booth_exemp_status'=> 1
    ];
	
    $stats_sum = TblAnalyticsDashboardModel::total_statics_sum_exem_new($filter_for_voters);

    $male   = $stats_sum['male_voters'];
    $female = $stats_sum['female_voters'];
    $other  = $stats_sum['other_voters'];
    $total  = $male+$female+$other;
    $queue_voters = $stats_sum['queue_voters'];

    if($queue_voters <= 0){
		  $queue_voters = 0;
	  }

    //$electoral = VoterInfoModel::get_aggregate_voters($filter_for_voters);
	$electoral       = TblAnalyticsDashboardModel::get_aggregate_voters_exem_new($filter_for_voters);

    $e_male   = $electoral['e_male'];
    $e_female = $electoral['e_female'];
    $e_other  = $electoral['e_other'];
    $e_total  = $electoral['e_total'];


    $grand_e_male   += $e_male;
    $grand_e_female += $e_female;
    $grand_e_other  += $e_other;
    $grand_e_total  += $e_total;
    $grand_male   += $male;
    $grand_female += $female;
    $grand_other  += $other;
    $grand_total  += $total;


    $grand_queue  += $queue_voters;


    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    if($queue_voters<0){
      $voters_queue = "No Information";
    }else if($queue_voters<10){
      $voters_queue = "less than 10";
    }else if($queue_voters >= 10 && $queue_voters<20){
      $voters_queue = "10 to 20";
    }else if($queue_voters >= 20 && $queue_voters<30){
      $voters_queue = "20 to 30";
    }else if($queue_voters >= 30){
      $voters_queue = "30+";
    }else{
      $voters_queue = '';
    }

    $data['voter_turnouts'][] = [
      'name'            => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
      'st_code'         => $iterate_ac['st_code'],
      'ac_no'           => $iterate_ac['ac_no'],
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => $voters_queue,
      'percentage'      => $percentage
    ];

  }

  $grand_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total > 0){
    $grand_percentage = round($grand_total/$grand_e_total*100,2);
  }

  $data['voter_turnouts'][]    = [
    'name'      => 'Total',
    'ac_no'     => '',
    'st_code'   => '',
    'male'      => $grand_male,
    'female'    => $grand_female,
    'other'     => $grand_other,
    'total'     => $grand_total,
    'e_male'    => $grand_e_male,
    'e_female'  => $grand_e_female,
    'e_other'   => $grand_e_other,
    'e_total'   => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage' => $grand_percentage
  ];

  $data['grand_percentage']         = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;
  return $data;
  
}

public function turnout_ps_wise_exempted(Request $request, $filter = array()){
  $data                   = [];
  $data['voter_turnouts'] = [];
  if(count($filter) == 0){
    $filter = [
      'phase_no'        => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'ps_no'           => $this->ps_no,
    ];
  }

  $grand_e_male   = 0;
  $grand_e_female = 0;
  $grand_e_other  = 0;
  $grand_e_total  = 0;
  $grand_male   = 0;
  $grand_female = 0;
  $grand_other  = 0;
  $grand_total  = 0;
  $grand_queue  = 0;

  $polling_stations = PollingStation::get_polling_stations_exem_new($filter);
  
  foreach ($polling_stations as $key => $iterate_p_s) {
    $ps_no = $iterate_p_s['PS_NO'];
    $filter_for_voters = array_merge($filter,['ps_no' => $iterate_p_s['PS_NO'],'booth_exemp_status' => 1]);
    
    $stats_sum = TblAnalyticsDashboardModel::total_statics_sum_exem_new($filter_for_voters);
    
    $male   = $stats_sum['male_voters'];
    $female = $stats_sum['female_voters'];
    $other  = $stats_sum['other_voters'];
	$last_sync = $stats_sum['last_sync'];
    $total  = $male+$female+$other;
    $queue_voters = $stats_sum['queue_voters'];
	//$queue_voters = $stats_sum['queue_voters'];
	if($queue_voters <= 0){
		$queue_voters = 0;
	}

	$electors       = TblAnalyticsDashboardModel::get_aggregate_voters_exem_new($filter_for_voters);
	$e_male   = $electors['e_male'];
	$e_female = $electors['e_female'];
	$e_other  = $electors['e_other'];
	
    /* $e_male   = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M']));
    $e_female = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F']));
    $e_other  = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O'])); */
    $e_total  = $e_male + $e_female + $e_other;


    $grand_e_male   += $e_male;
    $grand_e_female += $e_female;
    $grand_e_other  += $e_other;
    $grand_e_total  += $e_total;
    $grand_male   += $male;
    $grand_female += $female;
    $grand_other  += $other;
    $grand_total  += $total;

    $is_ps_poll_end = TblAnalyticsDashboardModel::total_statics_count(array_merge($filter_for_voters,['is_end' => true]));
    if($is_ps_poll_end){
      $queue_voters = 'Poll End';
    }else{
      $grand_queue  += $queue_voters;
    }

    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    $poll_station_name = $iterate_p_s['PS_NAME_EN'];

    if($queue_voters<0){
      $voters_queue = "No Information";
    }else if($queue_voters<10){
      $voters_queue = "less than 10";
    }else if($queue_voters >= 10 && $queue_voters<20){
      $voters_queue = "10 to 20";
    }else if($queue_voters >= 20 && $queue_voters<30){
      $voters_queue = "20 to 30";
    }else if($queue_voters >= 30){
      $voters_queue = "30+";
    }else{
      $voters_queue = '';
    }

    $data['voter_turnouts'][] = [
      'ps_name'         => $poll_station_name,
      'ps_no'           => $iterate_p_s['PS_NO'],
      'st_code'           => $iterate_p_s['ST_CODE'],
      'ps_name_and_no'  => $iterate_p_s['PS_NO'].'-'.$poll_station_name,
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => $voters_queue,
      'percentage'      => $percentage,
      'last_sync'      => $last_sync
    ];

  }

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
    'total_in_queue'  => '',//$grand_queue,
    'percentage' => $grand_percentage,
	'last_sync' => ''
  ];

  $data['grand_percentage'] = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;
  
  return $data;

  //return view($this->view.'.voter_turnout_ps_wise_exempted', $data);
}
//exempted turnout ends

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
  $data['heading_title'] = 'Add/Edit Officer Form';

  if($data['st_code']){
    $state_object = StateModel::get_state_by_code($data['st_code']);
    if($state_object){
      $title_array[]  = "State: ".$state_object['ST_NAME'];
    }
  }
  if($data['ac_no']){
    $ac_object = AcModel::get_ac(['state' => $data['st_code'], 'ac_no' => $data['ac_no']]);
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
    'name' => 'List of Officers',
    'target' => false,
  ];
  $data['action']         = Common::generate_url($this->action.'/officer-list/post');
  $filter_election = [
    'st_code'   => $data['st_code'],
    'ac_no'     => $data['ac_no'],
    'paginate'  => true,
    'restricted_ps' => []
  ];


  $results   = [];
  $filter_ps = [
    'st_code'   => $this->st_code,
    'ac_no'     => $this->ac_no,
    'paginate'  => false,
    'restricted_ps' => []
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

  if($request->old('is_pro_right')){
    $data['is_pro_right']  = $request->old('is_pro_right');
  }else if(isset($object) && $object){
    $data['is_pro_right']  = $object['pro_override'];
  }else{
    $data['is_pro_right']  = '';
  }

  if($request->old('is_testing')){
    $data['is_testing']  = $request->old('is_testing');
  }else if(isset($object) && $object){
    $data['is_testing']  = $object['is_testing'];
  }else{
    $data['is_testing']  = 0;
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

  if($request->old('role_level')){
    $data['role_level']  = $request->old('role_level');
  }else if(isset($object) && $object){
    $data['role_level']  = $object['role_level'];
  }else{
    if($request->has('role_level')){
      $data['role_level']  = $request->role_level;
    }else{
      $data['role_level']  = 0;
    }
  }

  if($request->old('pin')){
    $data['pin']  = $request->old('pin');
  }else if(isset($object) && $object){
    $data['pin']  = $object['pin'];
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
// dd($this->view);
  return view($this->view.'.officer-list-form', $data);

  try{}catch(\Exception $e){
    return Redirect::to($this->base.'/dashboard');
  }

}

public function post_officer(OfficerRequest $request){

  $no_allowed_po     = $this->no_allowed_po;
  $no_allowed_blo    = $this->no_allowed_blo;
  $no_allowed_pro    = $this->no_allowed_pro;
  $merge            = [];
  $merge['st_code'] = \Auth::user()->st_code;
  $merge['dist_no'] = \Auth::user()->dist_no;
  $merge['ac_no']   = \Auth::user()->ac_no;

  if(in_array($request->role_id,['33','35'])){
    $merge['pin'] = '';
  }
  $request->merge($merge);

  $no_active_officer = PollingStationOfficerModel::count_officer($request->all());

  if(in_array($request->role_id,['35']) && $no_active_officer > $no_allowed_pro){
    Session::flash('status',0);
    Session::flash('flash-message',"You can only add 3 Polling Party to a Polling Station.");
    return Redirect::back()->withInput($request->all());
  }

  if(in_array($request->role_id,['33']) && $no_active_officer > $no_allowed_blo){
    Session::flash('status',0);
    Session::flash('flash-message',"You can only add ".$this->no_allowed_blo." BLO to a Polling Station.");
    return Redirect::back()->withInput($request->all());
  }

  if($request->role_id == '34' && $no_active_officer > $no_allowed_po){
    Session::flash('status',0);
    Session::flash('flash-message',"You can only add ".$no_allowed_po." PO to a Polling Station.");
    return Redirect::back()->withInput($request->all());
  }

  DB::beginTransaction();
  try{
    $result   = PollingStationOfficerModel::add_officer($request->all());
  }catch(\Exception $e){
    DB::rollback();
    Session::flash('status',0);
    Session::flash('flash-message',"Please Try Again.");
    return Redirect::back();
  }
  DB::commit();

  $role = '';
  $app_link = config('public_config.booth_app_link');
  if($request->role_id == '33'){
    $role = 'BLO';
  }else if($request->role_id == '35'){
    $role = 'PRO';
  }else if($request->role_id == '34'){
    $role = 'PO';
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
  return redirect($this->base.'/booth-app-revamp/officer-list');
}

public function get_voter_turnout(Request $request){
  if($this->ac_no && $this->st_code){
    return $this->turnout_ps_wise($request, []);
  }else if($this->st_code){
    return $this->turnout_ac_wise($request, []);
  }else{
    return $this->turnout_state_wise($request,[]);
  }
}

public function turnout_state_wise($request, $filter = array()){
  $data                   = [];
  $data['voter_turnouts'] = [];
  if(count($filter) == 0){
    $filter = [
      'phase_no' => $this->phase_no,
      'st_code'  => $this->st_code
    ];
  }

  $grand_e_male   = 0;
  $grand_e_female = 0;
  $grand_e_other  = 0;
  $grand_e_total  = 0;
  $grand_male   = 0;
  $grand_female = 0;
  $grand_other  = 0;
  $grand_total  = 0;
  $grand_queue  = 0;

  $states_results = StateModel::get_states($filter);
  foreach ($states_results as $key => $iterate_state) {

    $filter_for_voters = [
      'st_code' => $iterate_state->ST_CODE,
      'phase_no'    => $this->phase_no
    ];
    $stats_sum = TblAnalyticsDashboardModel::total_statics_sum($filter_for_voters);

    $male   = $stats_sum['male_voters'];
    $female = $stats_sum['female_voters'];
    $other  = $stats_sum['other_voters'];
    $total  = $male+$female+$other;
    $queue_voters = $stats_sum['queue_voters'];
  if($queue_voters <= 0){
    $queue_voters = 0;
  }

    $electors       = TblAnalyticsDashboardModel::get_aggregate_voters($filter_for_voters);
	$e_male   = $electors['e_male'];
	$e_female = $electors['e_female'];
	$e_other  = $electors['e_other'];
    $e_total  = $e_male+$e_female+$e_other;


    $grand_e_male   += $e_male;
    $grand_e_female += $e_female;
    $grand_e_other  += $e_other;
    $grand_e_total  += $e_total;
    $grand_male   += $male;
    $grand_female += $female;
    $grand_other  += $other;
    $grand_total  += $total;


    $grand_queue  += $queue_voters;


    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }
    if($queue_voters<0){
      $voters_queue = "No Information";
    }else if($queue_voters<10){
      $voters_queue = "less than 10";
    }else if($queue_voters >= 10 && $queue_voters<20){
      $voters_queue = "10 to 20";
    }else if($queue_voters >= 20 && $queue_voters<30){
      $voters_queue = "20 to 30";
    }else if($queue_voters >= 30){
      $voters_queue = "30+";
    }else{
      $voters_queue = '';
    }

    $data['voter_turnouts'][] = [
      'st_name'         => $iterate_state['ST_NAME'],
      'st_code'         => $iterate_state['ST_CODE'],
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => $voters_queue,
      'percentage'      => $percentage
    ];

  }

  $grand_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total > 0){
    $grand_percentage = round($grand_total/$grand_e_total*100,2);
  }

  $data['voter_turnouts'][]    = [
    'st_name' => 'Total',
    'st_code' => '',
    'male'    => $grand_male,
    'female'  => $grand_female,
    'other'   => $grand_other,
    'total'   => $grand_total,
    'e_male'    => $grand_e_male,
    'e_female'  => $grand_e_female,
    'e_other'   => $grand_e_other,
    'e_total'   => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage' => $grand_percentage
  ];

  $data['grand_percentage']         = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;

  return view($this->view.'.voter_turnout_state_wise', $data);
}

public function turnout_ac_wise($request, $filter = array()){
  $data                   = [];
  $data['voter_turnouts'] = [];
  if(count($filter) == 0){
    $filter = [
      'phase_no' => $this->phase_no,
      'st_code'  => $this->st_code
    ];
  }

  $grand_e_male   = 0;
  $grand_e_female = 0;
  $grand_e_other  = 0;
  $grand_e_total  = 0;
  $grand_male   = 0;
  $grand_female = 0;
  $grand_other  = 0;
  $grand_total  = 0;
  $grand_queue  = 0;

  $acs_results = AcModel::get_acs($filter);

  foreach ($acs_results as $key => $iterate_ac) {
    $filter_for_voters = [
      'phase_no'    => $this->phase_no,
      'st_code' => $iterate_ac['st_code'],
      'ac_no'   => $iterate_ac['ac_no'],
    ];
    $stats_sum = TblAnalyticsDashboardModel::total_statics_sum($filter_for_voters);

    $male   = $stats_sum['male_voters'];
    $female = $stats_sum['female_voters'];
    $other  = $stats_sum['other_voters'];
    $total  = $male+$female+$other;
    $queue_voters = $stats_sum['queue_voters'];

    if($queue_voters <= 0){
		  $queue_voters = 0;
	  }

    //$electoral = VoterInfoModel::get_aggregate_voters($filter_for_voters);
	$electoral       = TblAnalyticsDashboardModel::get_aggregate_voters($filter_for_voters);

    $e_male   = $electoral['e_male'];
    $e_female = $electoral['e_female'];
    $e_other  = $electoral['e_other'];
    $e_total  = $electoral['e_total'];


    $grand_e_male   += $e_male;
    $grand_e_female += $e_female;
    $grand_e_other  += $e_other;
    $grand_e_total  += $e_total;
    $grand_male   += $male;
    $grand_female += $female;
    $grand_other  += $other;
    $grand_total  += $total;


    $grand_queue  += $queue_voters;


    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    if($queue_voters<0){
      $voters_queue = "No Information";
    }else if($queue_voters<10){
      $voters_queue = "less than 10";
    }else if($queue_voters >= 10 && $queue_voters<20){
      $voters_queue = "10 to 20";
    }else if($queue_voters >= 20 && $queue_voters<30){
      $voters_queue = "20 to 30";
    }else if($queue_voters >= 30){
      $voters_queue = "30+";
    }else{
      $voters_queue = '';
    }

    $data['voter_turnouts'][] = [
      'name'            => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
      'st_code'         => $iterate_ac['st_code'],
      'ac_no'           => $iterate_ac['ac_no'],
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => $voters_queue,
      'percentage'      => $percentage
    ];

  }

  $grand_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total > 0){
    $grand_percentage = round($grand_total/$grand_e_total*100,2);
  }

  $data['voter_turnouts'][]    = [
    'name'      => 'Total',
    'ac_no'     => '',
    'st_code'   => '',
    'male'      => $grand_male,
    'female'    => $grand_female,
    'other'     => $grand_other,
    'total'     => $grand_total,
    'e_male'    => $grand_e_male,
    'e_female'  => $grand_e_female,
    'e_other'   => $grand_e_other,
    'e_total'   => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage' => $grand_percentage
  ];

  $data['grand_percentage']         = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;

  return view($this->view.'.voter_turnout_ac_wise', $data);
}



public function turnout_ps_wise(Request $request, $filter = array()){
  $data                   = [];
  $data['voter_turnouts'] = [];
  if(count($filter) == 0){
    $filter = [
      'phase_no'        => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'ps_no'           => $this->ps_no,
    ];
  }

  $grand_e_male   = 0;
  $grand_e_female = 0;
  $grand_e_other  = 0;
  $grand_e_total  = 0;
  $grand_male   = 0;
  $grand_female = 0;
  $grand_other  = 0;
  $grand_total  = 0;
  $grand_queue  = 0;

  $polling_stations = PollingStation::get_polling_stations($filter);

  foreach ($polling_stations as $key => $iterate_p_s) {
    $ps_no = $iterate_p_s['PS_NO'];
    $filter_for_voters = array_merge($filter,['ps_no' => $iterate_p_s['PS_NO']]);

    $stats_sum = TblAnalyticsDashboardModel::total_statics_sum($filter_for_voters);

    $male   = $stats_sum['male_voters'];
    $female = $stats_sum['female_voters'];
    $other  = $stats_sum['other_voters'];
    $total  = $male+$female+$other;
    $queue_voters = $stats_sum['queue_voters'];
	//$queue_voters = $stats_sum['queue_voters'];
	if($queue_voters <= 0){
		$queue_voters = 0;
	}

	$electors       = TblAnalyticsDashboardModel::get_aggregate_voters($filter_for_voters);
	$e_male   = $electors['e_male'];
	$e_female = $electors['e_female'];
	$e_other  = $electors['e_other'];
	
    /* $e_male   = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M']));
    $e_female = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F']));
    $e_other  = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O'])); */
    $e_total  = $e_male + $e_female + $e_other;


    $grand_e_male   += $e_male;
    $grand_e_female += $e_female;
    $grand_e_other  += $e_other;
    $grand_e_total  += $e_total;
    $grand_male   += $male;
    $grand_female += $female;
    $grand_other  += $other;
    $grand_total  += $total;

    $is_ps_poll_end = TblAnalyticsDashboardModel::total_statics_count(array_merge($filter_for_voters,['is_end' => true]));
    if($is_ps_poll_end){
      $queue_voters = 'Poll End';
    }else{
      $grand_queue  += $queue_voters;
    }

    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    $poll_station_name = $iterate_p_s['PS_NAME_EN'];

    if($queue_voters<0){
      $voters_queue = "No Information";
    }else if($queue_voters<10){
      $voters_queue = "less than 10";
    }else if($queue_voters >= 10 && $queue_voters<20){
      $voters_queue = "10 to 20";
    }else if($queue_voters >= 20 && $queue_voters<30){
      $voters_queue = "20 to 30";
    }else if($queue_voters >= 30){
      $voters_queue = "30+";
    }else{
      $voters_queue = '';
    }

    $data['voter_turnouts'][] = [
      'ps_name'         => $poll_station_name,
      'ps_no'           => $iterate_p_s['PS_NO'],
      'st_code'           => $iterate_p_s['ST_CODE'],
      'ps_name_and_no'  => $iterate_p_s['PS_NO'].'-'.$poll_station_name,
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => $voters_queue,
      'percentage'      => $percentage
    ];

  }

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
    'total_in_queue'  => '',//$grand_queue,
    'percentage' => $grand_percentage
  ];

  $data['grand_percentage'] = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;

  return view($this->view.'.voter_turnout_ps_wise', $data);
}


//get officers
public function get_officers(Request $request){

  $data                   = [];
  $data['role_id']        = NULL;
  $is_activated           = NULL;
  if($request->has('is_activated')){
    $is_activated = $request->is_activated;
  }

  $role_level           = NULL;
  if($request->has('role_level')){
    $role_level = $request->role_level;
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
    $ac_object = AcModel::get_ac(['state' => $st_code, 'ac_no' => $ac_no]);
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

  $data['filter']  = implode('&', array_merge($request_array));
  $filter_election = [
    'st_code'   => $st_code,
    'ac_no'     => $ac_no,
    'ps_no'     => $ps_no,
    'phase_no'  => $this->phase_no,
    'paginate'  => false,
    'restricted_ps'   => [],
    'role_id'         => $this->filter_role_id,
    'is_activated'    => $is_activated,
    'role_level'      => $role_level
  ];

  $data['results']        = [];
  $officers_data          = [];

  

  if($filter_election['st_code']){

    $results                =   PollingStationOfficerModel::get_officers($filter_election);

    $data['pag_results']    = $results;

    
    foreach ($results as $officer) {

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
      $ac_object = AcModel::get_ac(['state' => $officer['st_code'], 'ac_no' => $officer['ac_no'] ]);
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



      $is_login = false;
      if(strtotime($officer['login_time'])){
        $is_login = true;
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
        'role_level'    => $officer['role_level'],
        'is_login'      =>  $is_login
      ];
    }
  }else{
    $data['no_record'] = "Please select state.";
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
  $data['filter_action'] = Common::generate_url("booth-app-revamp/officers");
  $form_filter_array = [
    'phase_no'  => true,
    'phase_no'  => true,
    'st_code'     => true,
    'ac_no'       => true,
    'ps_no'       => true,
    'designation' => true,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
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

  //activate filter
  $is_role_levels   = [];
  $is_role_levels[] = [
    'name'  => 'Role Level 1',
    'id'    => '1',
  ];
  $is_role_levels[] = [
    'name'  => 'Role Level 2',
    'id'    => '2',
  ];

  $role_level_array = [];
  foreach ($is_role_levels as $iterate_role) {
    $is_active = false;
    if($role_level == $iterate_role['id']){
      $is_active = true;
    }
    $role_level_array[] = [
      'name'    => $iterate_role['name'],
      'id'      => $iterate_role['id'],
      'active'  => $is_active
    ];
  }


  $form_filters[] = [
    'id'      => 'role_level',
    'name'    => 'Role Level',
    'results' => $role_level_array
  ];




  $data['form_filters'] = $form_filters;
  return view($this->view.'.officers', $data);

}

//polling station
public function polling_station(Request $request){
  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "List of Polling Station";

  $ac_no          = $this->ac_no;
  $st_code        = $this->st_code;

  $filter = [
    'st_code' => $st_code,
    'ac_no'   => $ac_no
  ];

//buttons
  $data['buttons']    = [];
  $data['buttons'][]    = [
    'href' => Common::generate_url($this->action.'/dashboard'),
    'name' => 'Back',
    'target' => false,
  ];

  $data['results']     = [];

  if($filter['st_code']){
    $ps_results = PollingStation::get_polling_stations($filter);
	
	

    foreach ($ps_results as $key => $iterate_ps) {

      $iterate_restricted = $iterate_ps['PS_NO'];

      $filter_for_voters = [
        'st_code' => $iterate_ps['ST_CODE'],
        'ac_no' => $iterate_ps['AC_NO'],
        'ps_no' => $iterate_ps['PS_NO']
      ];

      $poll_station_name = '';
      $poll_station = PollingStation::get_polling_station($filter_for_voters);
      if($poll_station){
        $poll_station_name = $poll_station['PS_NAME_EN'];
      }

      $ac_name = '';
      $ac_no = '';
      $ac_object = AcModel::get_ac([
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
  }else{
    $data['no_record'] = "Please select a state & ac";
  }

//form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/polling-station");
  $form_filter_array = [
    'phase_no'  => true,
    'st_code'     => true,
    'ac_no'       => true,
    'ps_no'       => false,
    'designation' => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);
  $data['form_filters']   = $form_filters;
  $data['user_data']      = Auth::user();
  

  return view($this->view.'.no-of-polling-station', $data);
}


//e-roll download
public function get_e_roll_download(Request $request){
  $data                = [];
	//buttons
  $data['buttons']    = [];
  $data['buttons'][]    = [
    'href' => Common::generate_url($this->action.'/dashboard'),
    'name' => 'Back',
    'target' => false,
  ];

  $data['heading_title']  = "List of E-Download";

  $request_array = [];
//set title
  $title_array  = [];
  if($this->st_code){
    $state_object = StateModel::get_state_by_code($this->st_code);
    if($state_object){
      $title_array[]  = "State: ".$state_object['ST_NAME'];
    }
  }
  if($this->ac_no && $this->st_code){
    $ac_object = AcModel::get_ac(['state' => $this->st_code, 'ac_no' => $this->ac_no]);
    if($ac_object){
      $title_array[]  = "AC: ".$ac_object['ac_name'];
    }
  }
  if($this->ac_no && $this->st_code && $this->ps_no){
    $poll_station_name = '';
    $poll_station = PollingStation::get_polling_station([
      'state' => $this->st_code,
      'ac_no' => $this->ac_no,
      'ps_no'   => $this->ps_no
    ]);
    if($poll_station){
      $title_array[]  = "PS: ".$poll_station['PS_NAME_EN'];
    }
  }
  $data['filter_buttons'] = $title_array;

  $data['filter']   = implode('&', array_merge($request_array));
  $filter_election = [
    'st_code'   => $this->st_code,
    'ac_no'     => $this->ac_no,
    'ps_no'     => $this->ps_no,
    'paginate'  => false,
    'role_id'       => $this->filter_role_id
  ];

  $data['results']        = [];
  $officers_data          = [];

  if($filter_election['st_code']){
    $results                = TblBoothUserModel::get_download_time($filter_election);
    foreach ($results as $officer) {

      $role = '';
      if($officer->user_type == '33'){
        $role = 'BLO';
      }
      if($officer->user_type == '35'){
        $role = 'Polling Party';
      }
      if($officer->user_type == '34'){
        $role = 'PO';
      }

      $st_name = '';
      $state_object = StateModel::get_state_by_code($officer->st_code);
      if($state_object){
        $st_name = $state_object['ST_NAME'];
      }

      $ac_name = '';
      $ac_object = AcModel::get_ac(['state' => $officer->st_code, 'ac_no' => $officer->ac_no]);
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
      $officer_detail = PollingStationOfficerModel::get_officer(['id' => $officer->id]);
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
	

  }else{
    $data['no_record'] = "Please select state.";
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
  $data['filter_action'] = Common::generate_url("booth-app-revamp/e-roll-download");
  $form_filter_array = [
    'phase_no'  => true,
    'st_code'     => true,
    'ac_no'       => true,
    'ps_no'       => true,
    'designation' => true,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);

  $data['form_filters'] = $form_filters;
  return view($this->view.'.e-roll-download', $data);
}


//get scan data
public function get_scan_data(Request $request){
	
  if($this->ac_no && $this->st_code){
    $data = $this->get_ps_wise_scan_data($request);
    return view($this->view.'.scan_data_ps_wise', $data);
  }else if($this->st_code){
    $data = $this->get_ac_wise_scan_data($request);
    return view($this->view.'.scan_data_ac_wise', $data);
  }else{
    $data = $this->get_state_wise_scan_data($request);
    return view($this->view.'.scan_data_state_wise', $data);
  }
}

public function get_state_wise_scan_data(Request $request){
  $data                   = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Scan Electoral Report";

  $filter = [
    'phase_no' => $this->phase_no,
  ];

  $data['results']     = [];

  $results = StateModel::get_states($filter);

  foreach ($results as $key => $iterate_state) {

    $doughnut_object = TblAnalyticsDashboardModel::total_statics_sum(array_merge($filter,[
      'st_code' => $iterate_state->ST_CODE
    ]));

    //get data ps wise
    $total_qr   = 0;
    $total_epic = 0;
    $total_bs   = 0;
    $total_name = 0;
    $total_mobile = 0;

    if($doughnut_object){
      $total_qr   = $doughnut_object['scan_qr'];
      $total_epic = $doughnut_object['scan_epicno'];
      $total_bs   = $doughnut_object['scan_srno'];
      $total_name = $doughnut_object['scan_name'];
      $total_mobile = $doughnut_object['scan_mobile'];
    }

    $data['results'][] = [
      'st_name' => $iterate_state->ST_NAME,
      'total_qr'     => $total_qr,
      'total_epic'   => $total_epic,
      'total_name'   => $total_name,
      'total_booth_id'   => $total_bs,
      'total_mobile'    => $total_mobile
    ];
  }

  $doughnut_object = TblAnalyticsDashboardModel::total_statics_sum($filter);
  if($doughnut_object){
      $total_qr   = $doughnut_object['scan_qr'];
      $total_epic = $doughnut_object['scan_epicno'];
      $total_bs   = $doughnut_object['scan_srno'];
      $total_name = $doughnut_object['scan_name'];
      $total_mobile = $doughnut_object['scan_mobile'];
    }

    $data['total'] = [
      'st_name' => 'Total',
      'total_qr'     => $total_qr,
      'total_epic'   => $total_epic,
      'total_name'   => $total_name,
      'total_booth_id'   => $total_bs,
      'total_mobile'    => $total_mobile
    ];
    return $data;
}

public function get_ac_wise_scan_data(Request $request){
  $data                   = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Scan Electoral Report";


  $filter = [
    'st_code' => $this->st_code,
    'phase_no' => $this->phase_no
  ];

  $data['results']     = [];

  $results = AcModel::get_acs($filter);

  foreach ($results as $key => $iterate_ac) {

    $doughnut_object = TblAnalyticsDashboardModel::total_statics_sum(array_merge($filter,[
      'ac_no' => $iterate_ac['ac_no']
    ]));

    $filter_for_voters = array_merge($filter,['ac_no' => $iterate_ac['ac_no']]);

    $st_name = '';
    $st_object = StateModel::get_state_by_code($iterate_ac['st_code']);
    if($st_object){
      $st_name = $st_object['ST_NAME'];
    }

    //get data ps wise
    $total_qr   = 0;
    $total_epic = 0;
    $total_bs   = 0;
    $total_name = 0;
    $total_mobile = 0;


    if($doughnut_object){
      $total_qr   = $doughnut_object['scan_qr'];
      $total_epic = $doughnut_object['scan_epicno'];
      $total_bs   = $doughnut_object['scan_srno'];
      $total_name = $doughnut_object['scan_name'];
      $total_mobile = $doughnut_object['scan_mobile'];
    }

    $data['results'][] = [
      'st_name' => $st_name,
      'ac_no'   => $iterate_ac['ac_no'],
      'ac_name' => $iterate_ac['ac_name'],
      'total_qr'     => $total_qr,
      'total_epic'   => $total_epic,
      'total_name'   => $total_name,
      'total_booth_id'   => $total_bs,
      'total_mobile'    => $total_mobile
    ];
  }

  $doughnut_object = TblAnalyticsDashboardModel::total_statics_sum($filter);
  if($doughnut_object){
      $total_qr   = $doughnut_object['scan_qr'];
      $total_epic = $doughnut_object['scan_epicno'];
      $total_bs   = $doughnut_object['scan_srno'];
      $total_name = $doughnut_object['scan_name'];
      $total_mobile = $doughnut_object['scan_mobile'];
    }

    $data['total'] = [
      'st_name' => 'Total',
      'ac_no'   => '',
      'ac_name' => '',
      'total_qr'     => $total_qr,
      'total_epic'   => $total_epic,
      'total_name'   => $total_name,
      'total_booth_id'   => $total_bs,
      'total_mobile'    => $total_mobile
    ];
    return $data;

}

public function get_ps_wise_scan_data(Request $request){
  $data                   = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Scan Electoral Report";

  $filter = [
    'phase_no' => $this->phase_no,
    'st_code' => $this->st_code,
    'ac_no'   => $this->ac_no,
    'ps_no'   => $this->ps_no
  ];


  $data['results']     = [];

  $ps_results = PollingStation::get_polling_stations($filter);

  foreach ($ps_results as $key => $iterate_ps) {

    $iterate_restricted = $iterate_ps['PS_NO'];

    $doughnut_object = TblAnalyticsDashboardModel::total_statics_sum(array_merge($filter,[
      'ps_no' => $iterate_ps['PS_NO']
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
    $ac_object = AcModel::get_ac([
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
    $total_mobile = 0;


    if($doughnut_object){
      $total_qr   = $doughnut_object['scan_qr'];
      $total_epic = $doughnut_object['scan_epicno'];
      $total_bs   = $doughnut_object['scan_srno'];
      $total_name = $doughnut_object['scan_name'];
      $total_mobile = $doughnut_object['scan_mobile'];
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
      'total_mobile'    => $total_mobile
    ];
  }

  $doughnut_object = TblAnalyticsDashboardModel::total_statics_sum($filter);
  if($doughnut_object){
      $total_qr   = $doughnut_object['scan_qr'];
      $total_epic = $doughnut_object['scan_epicno'];
      $total_bs   = $doughnut_object['scan_srno'];
      $total_name = $doughnut_object['scan_name'];
      $total_mobile = $doughnut_object['scan_mobile'];
    }

    $data['total'] = [
      'st_name' => 'Total',
      'ac_no' => '',
      'ac_name' => '',
      'ps_no' => '',
      'ps_name' => '',
      'total_qr'     => $total_qr,
      'total_epic'   => $total_epic,
      'total_name'   => $total_name,
      'total_booth_id'   => $total_bs,
      'total_mobile'    => $total_mobile
    ];

  return $data;

}





//send sms to user
public function send_sms_to_boothapp(Request $request){

  $app_link = config('public_config.booth_app_link');
  $officers = PollingStationOfficerModel::get_officers();
  $i = 0;
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
      $sms_message = "Your number has been registered for Booth App as a ".$role." for Polling station no. ".$officer['ps_no']." by the Returning officer.  Please download Booth App ".$app_link;
    }else{
      $poll_station_name = '';
      $poll_station = PollingStation::get_polling_station([
        'st_code' => $officer['st_code'],
        'ac_no'   => $officer['ac_no'],
        'ps_no'   => $officer['ps_no']
      ]);
      if($poll_station){
        $poll_station_name = $poll_station['PS_NAME_EN'];
      }
      $sms_message = "Your number has been registered for Booth App as a ".$role." for Polling station no. ".$officer['ps_no'].'-'.$poll_station_name." by the Returning officer.  Please download Booth App ".$app_link;
    }



    try{
      $msgstatus = SmsgatewayHelper::gupshup($officer['mobile_number'], $sms_message);
      $i++;
    }catch(\Exception $e){

    }
  }

  echo "Link sent to ". $i. " officers";
}

public function reset_otp(Request $request){

  $data   = [
    'otp'     => $request->otp,
    'mobile'  => $request->mobile
  ];
  $rules = [
    "mobile"  => "required|mobile",
    "otp"     => "required|regex:/^[0-9]{6}$/"
  ];

  $messages = [
      'mobile'  => 'Please enter valid a valid mobile number',
      'otp'     => 'Please enter a valid 6 digit number.',
      'regex'   => 'Please enter a valid 6 digit number.',
  ];

  $validator = Validator::make($data, $rules, $messages);
  if ($validator->fails())
  {
    return \Response::json([
        'status' => false,
        'errors' => $validator->errors()->getMessageBag()->first()
    ]);
  }

  $is_valid_mobile = PollingStationOfficerModel::get_officer(['mobile' => $request->mobile]);
  
 
  if(!$is_valid_mobile){
    return \Response::json([
        'status' => false,
        'errors' => "Please enter a valid mobile number"
    ]);
  }

  try{
    PollingStationOfficerModel::update_otp([
      'mobile'  => $request->mobile,
      'otp'     => $request->otp,
    ]);
  }catch(\Exception $e){
    return \Response::json([
        'status' => false,
        'errors' => "Please try again."
    ]);
  }

  return \Response::json([
    'status'  => true,
    'message' => "OTP has been updated."
  ]);
}

public function reset_pin(Request $request){
  try{
    $officer = PollingStationOfficerModel::where('mobile_number', $request->mobile)->first();
    $officer->pin = $request->pin;
    $officer->save();

  }catch(\Exception $e){
    return \Response::json([
        'status' => false,
        'errors' => "Please try again."
    ]);
  }
  return \Response::json([
    'status'  => true,
    'message' => "PIN has been updated."
  ]);
}
public function download_pro_diary(Request $request){

  if(!$this->st_code || !$this->ac_no || !$this->ps_no){
    return Redirect::back();
  }

   $filter = [
      'st_code'     => $this->st_code,
      'ac_no'       => $this->ac_no,
      'ps_no'       => $this->ps_no
    ];
    $data = [];
    $ac_name = '';
    $ac_object = AcModel::get_ac([
      'state' => $this->st_code,
      'ac_no' => $this->ac_no
    ]);
    if($ac_object){
      $ac_name = $ac_object['ac_name'];
    }

    $st_name = '';
    $st_object = StateModel::get_state_by_code($request->st_code);
    if($st_object){
      $st_name = $st_object['ST_NAME'];
    }

    $poll_station_name = '';
    $poll_station = PollingStation::get_polling_station($filter);
    if($poll_station){
      $poll_station_name = $poll_station['PS_NAME_EN'];
    }

    $itr_pro = ProDiaryFinal::get_pro_diary($filter);
    if(!$itr_pro){
      return Redirect::to(Common::generate_url('dashboard'));
    }

    $data['st_code'] = $this->st_code;
    $data['st_name'] = $st_name;
    $data['ac_no']   = $this->ac_no;
    $data['ac_name'] = $ac_name;
    $data['ps_no']   = $this->ps_no;
    $data['ps_name'] = $poll_station_name;
    $data_of_polling = '';
    $date_of_poll = \DB::table("m_election_details")->join("m_schedule","m_schedule.SCHEDULEID","=","m_election_details.ScheduleID")->where("ST_CODE", $this->st_code)->where("CONST_NO", $this->ac_no)->select("DATE_POLL")->first();
    if($date_of_poll){
      $data_of_polling = $date_of_poll->DATE_POLL;
    }
    $data['data_of_polling'] = $data_of_polling;

    $data['results'] = [];

    $i = 0;

    $data['diary'] = $itr_pro;

    $stats_sum          = TblPollSummaryModel::total_statics_sum($filter);
    $total_voter_man    = $stats_sum['male_voters'];
    $total_voter_woman  = $stats_sum['female_voters'];
    $total_voter_third  = $stats_sum['other_voters'];
    $total_voter  = $total_voter_man + $total_voter_woman + $total_voter_third;

    $data['total_voter_man'] = $total_voter_man;
    $data['total_voter_woman'] = $total_voter_woman;
    $data['total_voter_third'] = $total_voter_third;
    $data['total_voter'] = $total_voter;

    $cumulative_label_for_line  = ["07:00","09:00","11:00","13:00","15:00"];
    $data['is_time_slap']   = VoterInfoPollStatusModel::get_voters_by_time(array_merge($filter,['is_time_slap' => $cumulative_label_for_line]));

    $name_excel             = 'Pro_Diary_Report';
    $data['heading_title']  = $name_excel;
    $data['user_data']      = Auth::user();
    $pdf = PDF::loadView($this->view.'.download_pro_diary',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
}

//form 17 a
public function get_form_17_a(Request $request){

  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Polling Station";

  $filter = [
    'st_code' => $this->st_code,
    'ac_no'   => $this->ac_no
  ];

  //buttons
  $data['buttons']    = [];
  $data['results']     = [];

  if($filter['st_code']){
    $ps_results = PollingStation::get_polling_stations($filter);

    foreach ($ps_results as $key => $iterate_ps) {

      $ps_no    = $iterate_ps['PS_NO'];
      $st_code  = $iterate_ps['ST_CODE'];
      $ac_no    = $iterate_ps['AC_NO'];

      $filter_for_voters = [
        'st_code' => $st_code,
        'ac_no'   => $ac_no,
        'ps_no'   => $ps_no
      ];


      $poll_station_name = $iterate_ps['PS_NAME_EN'];

      $ac_name = '';
      $ac_object = AcModel::get_ac([
        'state' => $iterate_ps['ST_CODE'],
        'ac_no' => $iterate_ps['AC_NO']
      ]);
      if($ac_object){
        $ac_name = $ac_object['ac_name'];
      }

      $st_name = '';
      $st_object = StateModel::get_state_by_code($iterate_ps['ST_CODE']);
      if($st_object){
        $st_name = $st_object['ST_NAME'];
      }

      $data['results'][]    = [
        'st_name' => $st_name,
        'ac_no'   => $ac_no,
        'ac_name' => $ac_name,
        'ps_no'   => $ps_no,
        'ps_name' => $poll_station_name,
        'download_href' => Common::generate_url("booth-app-revamp/download-form-17-a")."?st_code=".$st_code."&ac_no=".$ac_no."&ps_no=".$ps_no,
        'download_pro_href' => Common::generate_url("booth-app-revamp/download-pro-diary")."?st_code=".$st_code."&ac_no=".$ac_no."&ps_no=".$ps_no,
      ];
    }
  }else{
    $data['no_record'] = "Please select a state & ac";
  }
//form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/get-form-17-a");
  $form_filter_array = [
    'phase_no'  => true,
    'st_code'     => true,
    'ac_no'       => true,
    'ps_no'       => false,
    'designation' => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);
  $data['form_filters']   = $form_filters;
  $data['user_data']      = Auth::user();

  return view($this->view.'.get-form-17-a', $data);
}

  public function download_17_a_form(Request $request){
    $filter = [
      'st_code'     => $this->st_code,
      'ac_no'       => $this->ac_no,
      'ps_no'       => $this->ps_no
    ];
    $data = [];

    $ac_name = '';
    $ac_object = AcModel::get_ac([
      'state' => $this->st_code,
      'ac_no' => $this->ac_no
    ]);
    if($ac_object){
      $ac_name = $ac_object['ac_name'];
    }

    $st_name = '';
    $st_object = StateModel::get_state_by_code($this->st_code);
    if($st_object){
      $st_name = $st_object['ST_NAME'];
    }

	$data['st_name'] = $st_name;

    $poll_station_name = '';
    $poll_station = PollingStation::get_polling_station($filter);
    if($poll_station){
      $poll_station_name = $poll_station['PS_NAME_EN'];
    }

    $data['st_code'] = $this->st_code;
    $data['st_name'] = $st_name;
    $data['ac_no']   = $this->ac_no;
    $data['ac_name'] = $ac_name;
    $data['ps_no']   = $this->ps_no;
    $data['ps_name'] = $poll_station_name;

    $data['results'] = [];

    $i = 0;
    $results = VoterInfoPollStatusModel::get_voters($filter);
    foreach ($results as $key => $iterate_result) {
      $data['results'][] = [
        'sr_no'         => ++$i,
        'elector_sr_no' => $iterate_result['serial_no']
      ];
    }

    $name_excel             = 'form-17-a';
    $data['heading_title']  = $name_excel;
    $data['user_data']      = Auth::user();
    $setting_pdf = [
      'margin_top'        => 50,
      'margin_bottom'     => 50,
    ];
    $pdf = PDF::loadView($this->view.'.download_17_a_form',$data,[],$setting_pdf);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }


  //poll details
  public function poll_detail_state(Request $request){
    $data                   = [];
    $data['buttons']        = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "Polling Station Stats";

    $filter = [
      'st_code' => $this->st_code,
	   'phase_no' =>$this->phase_no,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_acs' => $this->allowed_acs,
      'allowed_dist_no' => $this->allowed_dist_no
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
    $data['results']     = [];

    $results = StateModel::get_states($filter);
    foreach ($results as $key => $iterate_state) {

      $start_count = TblAnalyticsDashboardModel::total_statics_count([
        'st_code'     => $iterate_state['ST_CODE'],
        'is_started'  => true
      ]);

      $end_count = TblAnalyticsDashboardModel::total_statics_count([
        'st_code' => $iterate_state['ST_CODE'],
        'is_end'  => true
      ]);

      $request_array = [];
      $request_array[] = "st_code=".$iterate_state['ST_CODE'];

      $data['results'][]    = [
        'st_name'      => $iterate_state['ST_NAME'],
        'href'         => Common::generate_url("booth-app-revamp/poll-detail/ac")."?".implode('&', $request_array),
        'total_start'  => $start_count,
        'total_end'    => $end_count,
      ];
    }
    $start_count = TblAnalyticsDashboardModel::total_statics_count([
      'is_started'  => true,
	  'st_code'     => $this->st_code,
	  'phase_no'   => $this->phase_no
    ]);

    $end_count = TblAnalyticsDashboardModel::total_statics_count([
      'is_end'  => true,
	  'st_code'     => $this->st_code,
	  'phase_no'   => $this->phase_no
    ]);
    $data['total']    = [
      'st_name'      => "Total",
      'href'         => "javascript:void(0)",
      'total_start'  => $start_count,
      'total_end'    => $end_count,
    ];
	
	//form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/poll-detail/state");
    $form_filter_array = [
      'phase_no'  => true,
      'st_code'     => true,
      'ac_no'       => false,
      'ps_no'       => false,
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;

    $data['user_data']      = Auth::user();

    return view($this->view.'.poll-detail-state', $data);
  }

  public function poll_detail_ac(Request $request){
    $data                   = [];
    $data['buttons']        = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "Polling Station Stats";

    $filter = [
      'phase_no' =>$this->phase_no,
	  'ac_no' => $this->ac_no,
      'st_code' => $this->st_code,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_acs' => $this->allowed_acs,
      'allowed_dist_no' => $this->allowed_dist_no
    ];

    //buttons
    $data['buttons']    = [];
    if($this->role_id == '4'){
      $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code'.$this->st_code;
    }else{
      $back_href = Common::generate_url('booth-app-revamp/poll-detail/state').'?st_code'.$this->st_code;
    }
    $data['buttons'][]    = [
      'href' => $back_href,
      'name' => 'Back',
      'target' => false,
    ];
    $data['results']     = [];

    $st_name = '';
    $st_object = StateModel::get_state_by_code($this->st_code);
    if($st_object){
      $st_name = $st_object['ST_NAME'];
    }

    $results = AcModel::get_acs($filter);
    foreach ($results as $key => $iterate_ac) {

      $start_count = TblAnalyticsDashboardModel::total_statics_count([
        'st_code'     => $this->st_code,
        'ac_no'       => $iterate_ac['ac_no'],
        'is_started'  => true
      ]);

      $end_count = TblAnalyticsDashboardModel::total_statics_count([
        'st_code' => $this->st_code,
        'ac_no'   => $iterate_ac['ac_no'],
        'is_end'  => true
      ]);

      $request_array = [];
      $request_array[] = "st_code=".$this->st_code;
      $request_array[] = "ac_no=".$iterate_ac['ac_no'];
      $data['results'][]    = [
        'st_name'      => $st_name,
        'ac_name'      => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
        'href'         => Common::generate_url("booth-app-revamp/poll-detail")."?".implode('&', $request_array),
        'total_start'  => $start_count,
        'total_end'    => $end_count,
      ];
    }
    $start_count = TblAnalyticsDashboardModel::total_statics_count([
      'st_code'     => $this->st_code,
	  'phase_no'   => $this->phase_no,
      'is_started'  => true
    ]);

    $end_count = TblAnalyticsDashboardModel::total_statics_count([
      'st_code'     => $this->st_code,
	  'phase_no'   => $this->phase_no,
      'is_end'  => true
    ]);
    $data['total']    = [
      'st_name'      => "Total",
      'href'         => "javascript:void(0)",
      'total_start'  => $start_count,
      'total_end'    => $end_count,
    ];
	
	$data['filter_action'] = Common::generate_url("booth-app-revamp/poll-detail/ac");
    $form_filter_array = [
      'phase_no'  => true,
      'st_code'     => true,
      'ac_no'       => true,
      'ps_no'       => false,
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;

    $data['user_data']      = Auth::user();
	

    return view($this->view.'.poll-detail-ac', $data);
  }

  public function get_poll_detail(Request $request){
    $data                   = [];
    $data['buttons']        = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "List of Polling Station";
    $ac_no          = $this->ac_no;
    $st_code        = $this->st_code;

    $filter = [
      'st_code' => $st_code,
      'ac_no'   => $ac_no,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_acs' => $this->allowed_acs,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];

    //buttons
    $data['buttons']    = [];
    if($this->role_id == '19'){
      $back_href = Common::generate_url('booth-app-revamp/dashboard');
    }else{
      $back_href = Common::generate_url('booth-app-revamp/poll-detail/ac').'?st_code='.$this->st_code;
    }
    $data['buttons'][]    = [
      'href' => $back_href,
      'name' => 'Back',
      'target' => false,
    ];

    $data['results']     = [];

    if($filter['st_code']){
      $ps_results = PollingStation::get_polling_stations($filter);

      foreach ($ps_results as $key => $iterate_ps) {

        $filter = [
          'ac_no' => $iterate_ps['AC_NO'],
          'st_code' => $iterate_ps['ST_CODE']
        ];
        $iterate_restricted = $iterate_ps['PS_NO'];

        $poll_summary_object = TblAnalyticsDashboardModel::get_poll_summary(array_merge($filter,[
          'ps_no' => $iterate_ps['PS_NO'],
          'is_started' => true
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
        $ac_object = AcModel::get_ac([
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
          'is_start'  => isset($poll_summary_object['poll_started'])?Date("d-m-Y H:i:s",strtotime($poll_summary_object['poll_started'])):'No',
          'is_end'    => isset($poll_summary_object['poll_ended'])?Date("d-m-Y H:i:s",strtotime($poll_summary_object['poll_ended'])):'No',
        ];
		
      }
    }else{
      $data['no_record'] = "Please select a state and ac.";
    }

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/poll-detail");
    $form_filter_array = [
      'phase_no'  => true,
      'st_code'     => true,
      'ac_no'       => true,
      'ps_no'       => false,
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;
    $data['user_data']      = Auth::user();
	
	

    return view($this->view.'.poll-detail', $data);
  }


  public function event_pusher(Request $request){

    $param = [
      'data' => [
        'broadcast' => true,
        'title'   => 'Test Broadcast',
        'message' => 'Test from PHP',
        'st_code' => 'S27',
        'dist_no' => '15',
        'ac_no'   => '63',
        'ps_no'   => '1',
        'ps_name' => 'PS Name'
      ],
      'name' => 'my-event',
      'channel' => 'my-channel',
    ];
    $auth_data = [
      'body_md5' => '2c99321eeba901356c4c7998da9be9e0',
      'auth_version' => '1.0',
      'auth_key' => config('broadcasting.connections.pusher.key'),
      'auth_timestamp' => time(),
      'auth_signature' => hash_hmac('sha256',json_encode($param),config('broadcasting.connections.pusher.secret'))
    ];

    // $query = http_build_query($auth_data);
    // $header = array(
    //   "cache-control:no-cache",
    //   "accept: application/json",
    //   "Content-Type:application/json"
    // );
    // //dd("https://api-ap2.pusher.com/apps/904707/events?".$query);
    // $ch = curl_init("https://api-ap2.pusher.com/apps/904707/events?".$query);
    // curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // $result = curl_exec($ch); // This is the result from the API
    // dd($result);
    // curl_close($ch);

    // return $result;




    // New Pusher instance with our config data
    $postData = [
        'broadcast' => true,
        'title'   => '1 message receive',
        'message' => "Message Full",
        'st_code' => 'S27',
        'dist_no' => '17',
        'ac_no'   => '63',
        'ps_no'   => '1',
        'ps_name' => 'PS Name'
      ];
    $pusher = new Pusher(config('broadcasting.connections.pusher.key'), config('broadcasting.connections.pusher.secret'), config('broadcasting.connections.pusher.app_id'), config('broadcasting.connections.pusher.options'));
    $pusher->trigger( 'my-channel', 'my-event', $postData);
    return Response::json([
      "message" => "Event has been sent."
    ]);
  }
  
  
  
  // testing function added for dummy data entry
  
  public function insertpollingstation(Request $request){
	  $data = DB::table('polling_station')->select('*')->where('st_code', 'S14')->where('ac_no',60)->get();
	  
	  foreach($data as $val){
		  $data_insert = [
		  'CCODE' => $val->CCODE,
		  'ST_CODE' => $val->ST_CODE,
		  'AC_NO' => $val->AC_NO,
		  'PART_NAME' => $val->PART_NAME,
		  'PART_NO' =>$val->PART_NO,
		  'PS_NO' =>$val->PS_NO,
		  'PS_NAME_EN'=>$val->PS_NAME_EN,
		  'PS_NAME_V1' =>$val->PS_NAME_V1,
		  'PS_TYPE'=>$val->PS_TYPE,
		  'PS_CATEGORY'=>$val->PS_CATEGORY,
		  'LOCN_TYPE' =>$val->LOCN_TYPE,
		  'PSL_NO' =>$val->PSL_NO,
		  'lattitude'=>$val->lattitude,
		  'longitude' =>$val->longitude,
		  'UpdatedDate'=>$val->UpdatedDate,
		  'is_update_api'=>$val->is_update_api,
		  'UPDATE_USR_ID' =>$val->UPDATE_USR_ID,
		  'ps_no_user_made' =>$val->ps_no_user_made,
		  
		  'slip_path' =>'',
		  'booth_app_excp' =>0,
		  ];
		  
		  DB::table('boothapp_polling_station')->insert($data_insert);
	  }
	  
	  
	  
  }

  //controller added for turnout exemption
  public function turnout_new(Request $request){
  
  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Polling Station Marked exempted Turnout";

  $filter = [
    'st_code' => Auth::user()->st_code,
	'ac_no' =>   Auth::user()->ac_no
    
  ];

  //buttons
  $data['buttons']    = [];
  $data['results']     = [];

  if($filter['st_code']){
    $ps_results = PollingStation::get_polling_stations_zero_turnout($filter);
  
    foreach ($ps_results as $key => $iterate_ps) {

      $ps_no    = $iterate_ps['PS_NO'];
      $st_code  = $iterate_ps['ST_CODE'];
      $ac_no    = $iterate_ps['AC_NO'];

      $filter_for_voters = [
        'st_code' => $st_code,
        'ac_no'   => $ac_no,
        'ps_no'   => $ps_no
      ];


      $poll_station_name = $iterate_ps['PS_NAME_EN'];
    $exep_status = $iterate_ps['booth_exemp_status'];

      $ac_name = '';
      $ac_object = AcModel::get_ac([
        'state' => $iterate_ps['ST_CODE'],
        'ac_no' => $iterate_ps['AC_NO']
      ]);
      if($ac_object){
        $ac_name = $ac_object['ac_name'];
      }

      $st_name = '';
      $st_object = StateModel::get_state_by_code($iterate_ps['ST_CODE']);
      if($st_object){
        $st_name = $st_object['ST_NAME'];
      }

      $data['results'][]    = [
        'st_name' => $st_name,
        'ac_no'   => $ac_no,
        'ac_name' => $ac_name,
        'ps_no'   => $ps_no,
        'ps_name' => $poll_station_name,
        'exep_status' => $exep_status,
    
        'male_electors' => $iterate_ps['male_electors'],
        'female_electors' => $iterate_ps['female_electors'],
        'other_electors' => $iterate_ps['other_electors'],
        'male_turnout' => $iterate_ps['male_turnout'],
        'female_turnout' => $iterate_ps['female_turnout'],
        'other_turnout' => $iterate_ps['other_turnout'],
        'last_sync' => $iterate_ps['updated_at'],
        
      ];
    }
  }else{
    $data['no_record'] = "Please select a state & ac";
  }
//form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/get-form-17-a");
  $data['action']     = Common::generate_url($this->action.'/exempted-boothapp-pollingstation/post');
  $form_filter_array = [
    'phase_no'  => true,
    'st_code'     => true,
    'ac_no'       => true,
    'ps_no'       => false,
    'designation' => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);
  $data['form_filters']   = $form_filters;
  $data['user_data']      = Auth::user();

  return view($this->view.'.exempted-ps_list', $data);
  }  
  
  
  public function turnout_new_ajax(Request $request){
	$date = date("Y-m-d h:i:sa");
	
    $user_data = Auth::user();
    $st_code = $user_data['st_code'];
    $ac_no = $user_data['ac_no'];
    if(isset($request['st_code'])){
    $update = TblAnalyticsDashboardReadModel::where('st_code',$request['st_code'])->where('male_turnout', 0)
          ->where('female_turnout', 0)->where('other_turnout', 0)->where('ac_no',$ac_no)
          ->update(['booth_exemp_status' => 1,'booth_exemp_time'=>$date]);
      
      if($update){
        return response()->json(['success'=>$update.' PS Updated Successfully']);
    }
    }
      
  }
  
  public function update_turnout(Request $request){
    
    $user_data = Auth::user();
    $st_code = $user_data['st_code'];
    $ac_no = $user_data['ac_no'];
	$date = date("Y-m-d h:i:sa");
	
	$insert_array = [
			'st_code' => $user_data['st_code'],
			'ac_no' => $user_data['ac_no'],
			'ps_no' => $request['psnoinput'],
			'male_turnout' =>$request['voter_male'],
			'female_turnout' => $request['voter_female'],
			'other_turnout' => $request['voter_other'],
			'created_by' => $user_data['officername'],
			'created_at' =>$date,
		];
    
    $update_new = TblAnalyticsDashboardReadModel::where('st_code',$st_code)->where('ac_no',$ac_no)
          ->where('ps_no',$request['psnoinput'])->where('booth_exemp_status', 1)
      ->update([
      'male_turnout' => $request['voter_male'],
      'female_turnout' => $request['voter_female'], 
      'other_turnout' => $request['voter_other']
      ]);
      
     
      
      if($update_new){
	    DB::table('booth_app_turnout_log')->insert($insert_array);
        return redirect('roac/booth-app-revamp/exempted-boothapp-pollingstation')->with('success', 'Turnout updated successfully');
      }
    
  }
  
  
  public function view_turnout_new(Request $request){
  
  $data                   = [];
  $data['buttons']        = [];
  $data['role_id']        = $this->role_id;
  $data['heading_title']  = "Polling Station with Zero Turnout";
  
  if(isset($request['turnout_type'])){
  $filter = [
    'st_code' => Auth::user()->st_code,
    'ac_no' => Auth::user()->ac_no,
    'turnout_type' => $request['turnout_type']
  ];
  }
  else{
   $filter = [
    'st_code' => Auth::user()->st_code,
    'ac_no' => Auth::user()->ac_no
  ];
  }

  

  //buttons
  $data['buttons']    = [];
  $data['results']     = [];

  if($filter['st_code']){
    $ps_results = PollingStation::get_polling_stations_zero_turnout_new($filter);
  
    foreach ($ps_results as $key => $iterate_ps) {

      $ps_no    = $iterate_ps['PS_NO'];
      $st_code  = $iterate_ps['ST_CODE'];
      $ac_no    = $iterate_ps['AC_NO'];

      $filter_for_voters = [
        'st_code' => $st_code,
        'ac_no'   => $ac_no,
        'ps_no'   => $ps_no
      ];


      $poll_station_name = $iterate_ps['PS_NAME_EN'];
    $exep_status = $iterate_ps['booth_exemp_status'];

      $ac_name = '';
      $ac_object = AcModel::get_ac([
        'state' => $iterate_ps['ST_CODE'],
        'ac_no' => $iterate_ps['AC_NO']
      ]);
      if($ac_object){
        $ac_name = $ac_object['ac_name'];
      }

      $st_name = '';
      $st_object = StateModel::get_state_by_code($iterate_ps['ST_CODE']);
      if($st_object){
        $st_name = $st_object['ST_NAME'];
      }

      $data['results'][]    = [
        'st_name' => $st_name,
        'ac_no'   => $ac_no,
        'ac_name' => $ac_name,
        'ps_no'   => $ps_no,
        'ps_name' => $poll_station_name,
        'exep_status' => $exep_status,
    
        'male_electors' => $iterate_ps['male_electors'],
        'female_electors' => $iterate_ps['female_electors'],
        'other_electors' => $iterate_ps['other_electors'],
        'male_turnout' => $iterate_ps['male_turnout'],
        'female_turnout' => $iterate_ps['female_turnout'],
        'other_turnout' => $iterate_ps['other_turnout'],
    'booth_exemp_status' => $iterate_ps['booth_exemp_status'],
        
      ];
    }
  }else{
    $data['no_record'] = "Please select a state & ac";
  }
//form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/get-form-17-a");
  $data['action']     = Common::generate_url($this->action.'/exempted-boothapp-pollingstation/post');
  $form_filter_array = [
    'phase_no'  => true,
    'st_code'     => true,
    'ac_no'       => true,
    'ps_no'       => false,
    'designation' => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);
  $data['form_filters']   = $form_filters;
  $data['user_data']      = Auth::user();
  $data['turnout_data']   = isset($request['turnout_type'])?$request['turnout_type']:'';
  
  return view($this->view.'.view-exempted-ps_list', $data);
    
  }

  public function exempt_ps_wise(Request $request){
        $date =  date("Y-m-d h:i:s");
		
        if(isset($request['st_code'])){
			$updb = array('updated_at'=>date("Y-m-d H:i:s"),'booth_app_excp'=>'1');
			$update_exm_ps = DB::table('polling_station_officer')->where('st_code',$request['st_code'])->where('ac_no',$request['ac_no'])
			  ->where('ps_no',$request['ps_no'])->update($updb);
					
			$update = TblAnalyticsDashboardReadModel::where('st_code',$request['st_code'])->where('ac_no',$request['ac_no'])
				  ->where('ps_no',$request['ps_no'])
			  ->update(['booth_exemp_status' => 1,'booth_exemp_time'=>$date]);
			  
			if($update){
				return response()->json(['success'=>$update.' PS Updated Successfully']);
			}
        }
  } 
  
  public function delete_user_pso(Request $request){
	  $st_code = $request['st_code'];
	  $ac_no = $request['ac_no'];
	  if(isset($st_code) && isset($ac_no)){
		  
		  $delete = PollingStationOfficerReadModel::where('st_code',$st_code)->where('ac_no',$ac_no)->delete();
	  }
	  
	  
	  if($delete){
			  return response()->json(['success'=>$delete.' PS Updated Successfully']);
		}
	  
  }

}  // end class
