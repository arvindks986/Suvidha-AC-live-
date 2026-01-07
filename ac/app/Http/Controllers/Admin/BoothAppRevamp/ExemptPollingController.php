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
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, PsSectorOfficer, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, JsonFile, TblPwdVoterModel, TblProDiaryModel, InfraMapping, MockPoll, IncidentStatistics,ExemptPollingModel,ExemPsCountModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;
use App\Events\Admin\BoothAppRevamp\PusherEvent;
use Pusher\Pusher;

//current

class ExemptPollingController extends Controller {

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

public function exempt_turnout_state_wise($request, $filter = array()){
  $data                   = [];
  $data['voter_turnouts'] = [];
  if(count($filter) == 0){
    $filter = [
      'phase_no' => $this->phase_no,
      'st_code'  => $this->st_code
    ];
  }

  
  $grand_ps   = 0;
  $grand_exempted_ps   = 0;
  $grand_round_1_total  = 0;
  $grand_round_2_total  = 0;
  $grand_round_3_total  = 0;
  $grand_round_4_total  = 0;
  $grand_round_5_total  = 0;
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
	
    $stats_sum = ExemptPollingModel::total_statics_sum($filter_for_voters);
    $ps_data = ExemptPollingModel::total_exempted_ps($filter_for_voters);

    $total_ps       = $ps_data['total_ps'];
    $total_exempted_ps   = $ps_data['total_exempted'];
    $round_1_total  = $stats_sum['round_1_total'];
    $round_2_total  = $stats_sum['round_2_total'];
    $round_3_total  = $stats_sum['round_3_total'];
    $round_4_total  = $stats_sum['round_4_total'];
    $round_5_total  = $stats_sum['round_5_total'];
    $male   = $stats_sum['total_male'];
    $female = $stats_sum['total_female'];
    $other  = $stats_sum['total_other'];
    $total  =$stats_sum['total_total'];

    $e_male   = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M', 'is_exempted' => 1]));
    $e_female = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F','is_exempted' => 1]));
    $e_other  = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O','is_exempted' => 1]));
    $e_total  = $e_male+$e_female+$e_other;


    $grand_e_male         += $e_male;
    $grand_e_female       += $e_female;
    $grand_e_other        += $e_other;
    $grand_e_total        += $e_total;
    $grand_round_1_total  += $round_1_total;
    $grand_round_2_total  += $round_2_total;
    $grand_round_3_total  += $round_3_total;
    $grand_round_4_total  += $round_4_total;
    $grand_round_5_total  += $round_5_total;
    $grand_ps             += $total_ps;
    $grand_exempted_ps    += $total_exempted_ps;
    $grand_male           += $male;
    $grand_female         += $female;
    $grand_other          += $other;
    $grand_total          += $total;

    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    $data['voter_turnouts'][] = [
      'st_name'           => $iterate_state['ST_NAME'],
      'st_code'           => $iterate_state['ST_CODE'],
      'total_ps'          => $total_ps,
      'total_exempted_ps' => $total_exempted_ps,
      'round_1_total'     => $round_1_total,
      'round_2_total'     => $round_2_total,
      'round_3_total'     => $round_3_total,
      'round_4_total'     => $round_4_total,
      'round_5_total'     => $round_5_total,
      'male'              => $male,
      'female'            => $female,
      'other'             => $other,
      'total'             => $total,
      'e_male'            => $e_male,
      'e_female'          => $e_female,
      'e_other'           => $e_other,
      'e_total'           => $e_total,
      'total_in_queue'    => '',
      'percentage'        => $percentage
    ];

  }

  $grand_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total > 0){
    $grand_percentage = round($grand_total/$grand_e_total*100,2);
  }

  $data['voter_turnouts'][]    = [
    'st_name'         => 'Total',
    'st_code'         => '',
    'total_ps'          => $grand_ps,
    'total_exempted_ps' => $grand_exempted_ps,
    'round_1_total'   => $grand_round_1_total,
    'round_2_total'   => $grand_round_2_total,
    'round_3_total'   => $grand_round_3_total,
    'round_4_total'   => $grand_round_4_total,
    'round_5_total'   => $grand_round_5_total,
    'male'            => $grand_male,
    'female'          => $grand_female,
    'other'           => $grand_other,
    'total'           => $grand_total,
    'e_male'          => $grand_e_male,
    'e_female'        => $grand_e_female,
    'e_other'         => $grand_e_other,
    'e_total'         => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage'      => $grand_percentage
  ];

  $data['grand_percentage']         = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;
  return $data;
}

public function exempt_turnout_ac_wise($request, $filter = array()){
  $data                   = [];
  $data['voter_turnouts'] = [];
  if(count($filter) == 0){
    $filter = [
      'phase_no' => $this->phase_no,
      'st_code'  => $this->st_code
    ];
  }
  
  $grand_ps   = 0;
  $grand_exempted_ps   = 0;
  $grand_round_1_total  = 0;
  $grand_round_2_total  = 0;
  $grand_round_3_total  = 0;
  $grand_round_4_total  = 0;
  $grand_round_5_total  = 0;
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
	  'is_exempted'   => 1,
    ];
    $stats_sum = ExemptPollingModel::total_statics_sum($filter_for_voters);
	//dd($stats_sum);
    $ps_data = ExemptPollingModel::total_exempted_ps($filter_for_voters);

    $total_ps       = $ps_data['total_ps'];
    $total_exempted_ps   = $ps_data['total_exempted'];
    $round_1_total  = $stats_sum['round_1_total'];
    $round_2_total  = $stats_sum['round_2_total'];
    $round_3_total  = $stats_sum['round_3_total'];
    $round_4_total  = $stats_sum['round_4_total'];
    $round_5_total  = $stats_sum['round_5_total'];
    $male   = $stats_sum['total_male'];
    $female = $stats_sum['total_female'];
    $other  = $stats_sum['total_other'];
    $total  =$stats_sum['total_total'];
	
    $electoral = VoterInfoModel::get_aggregate_voters($filter_for_voters);

    $e_male   = $electoral['e_male'];
    $e_female = $electoral['e_female'];
    $e_other  = $electoral['e_other'];
    $e_total  = $electoral['e_total'];


    
    $grand_e_male         += $e_male;
    $grand_e_female       += $e_female;
    $grand_e_other        += $e_other;
    $grand_e_total        += $e_total;
    $grand_round_1_total  += $round_1_total;
    $grand_round_2_total  += $round_2_total;
    $grand_round_3_total  += $round_3_total;
    $grand_round_4_total  += $round_4_total;
    $grand_round_5_total  += $round_5_total;
    $grand_ps             += $total_ps;
    $grand_exempted_ps    += $total_exempted_ps;
    $grand_male           += $male;
    $grand_female         += $female;
    $grand_other          += $other;
    $grand_total          += $total;

    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    $data['voter_turnouts'][] = [
      'name'            => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
      'st_code'         => $iterate_ac['st_code'],
      'ac_no'           => $iterate_ac['ac_no'],
      'round_1_total'   => $round_1_total,
      'round_2_total'   => $round_2_total,
      'round_3_total'   => $round_3_total,
      'round_4_total'   => $round_4_total,
      'round_5_total'   => $round_5_total,
      'total_ps'          => $total_ps,
      'total_exempted_ps' => $total_exempted_ps,
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => '',
      'percentage'      => $percentage
    ];

  }

  $grand_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total > 0){
    $grand_percentage = round($grand_total/$grand_e_total*100,2);
  }

  $data['voter_turnouts'][]    = [
    'name'            => 'Total',
    'ac_no'           => '',
    'st_code'         => '',
    'round_1_total'   => $grand_round_1_total,
    'round_2_total'   => $grand_round_2_total,
    'round_3_total'   => $grand_round_3_total,
    'round_4_total'   => $grand_round_4_total,
    'round_5_total'   => $grand_round_5_total,
    'total_ps'          => $grand_ps,
    'total_exempted_ps' => $grand_exempted_ps,
    'male'            => $grand_male,
    'female'          => $grand_female,
    'other'           => $grand_other,
    'total'           => $grand_total,
    'e_male'          => $grand_e_male,
    'e_female'        => $grand_e_female,
    'e_other'         => $grand_e_other,
    'e_total'         => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage'      => $grand_percentage
  ];

  $data['grand_percentage']         = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;

  return $data;
}



public function exempt_turnout_ps_wise(Request $request, $filter = array()){
  $data                   = [];
  $data['voter_turnouts'] = [];
  
  if($filter['exempt']==1){
    $filter = [
      'phase_no'        => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'ps_no'           => $this->ps_no,
      'exempt'          => 1,
    ];
  }else{
    if(count($filter) == 0){
    $filter = [
      'phase_no'        => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'ps_no'           => $this->ps_no,
      'exempt'          => 0,
    ];
  }
  }
  
  $grand_round_1_total  = 0;
  $grand_round_2_total  = 0;
  $grand_round_3_total  = 0;
  $grand_round_4_total  = 0;
  $grand_round_5_total  = 0;
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

    $stats_sum = ExemptPollingModel::total_statics_sum($filter_for_voters);

    $round_1_total  = $stats_sum['round_1_total'];
    $round_2_total  = $stats_sum['round_2_total'];
    $round_3_total  = $stats_sum['round_3_total'];
    $round_4_total  = $stats_sum['round_4_total'];
    $round_5_total  = $stats_sum['round_5_total'];
    $male           = $stats_sum['total_male'];
    $female         = $stats_sum['total_female'];
    $other          = $stats_sum['total_other'];
    $total          = $stats_sum['total_total'];


    $e_male   = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M','is_exempted' => 1]));
    $e_female = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F','is_exempted' => 1]));
    $e_other  = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O','is_exempted' => 1]));
    $e_total  = $e_male + $e_female + $e_other;


    $grand_e_male         += $e_male;
    $grand_e_female       += $e_female;
    $grand_e_other        += $e_other;
    $grand_e_total        += $e_total;
    $grand_round_1_total  += $round_1_total;
    $grand_round_2_total  += $round_2_total;
    $grand_round_3_total  += $round_3_total;
    $grand_round_4_total  += $round_4_total;
    $grand_round_5_total  += $round_5_total;
    $grand_male           += $male;
    $grand_female         += $female;
    $grand_other          += $other;
    $grand_total          += $total;


    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    $poll_station_name = $iterate_p_s['PS_NAME_EN'];

    $data['voter_turnouts'][] = [
      'ps_name'         => $poll_station_name,
      'ps_no'           => $iterate_p_s['PS_NO'],
      'ps_name_and_no'  => $iterate_p_s['PS_NO'].'-'.$poll_station_name,
      'round_1_total'   => $round_1_total,
      'round_2_total'   => $round_2_total,
      'round_3_total'   => $round_3_total,
      'round_4_total'   => $round_4_total,
      'round_5_total'   => $round_5_total,
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => '',
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
    'round_1_total'   => $grand_round_1_total,
    'round_2_total'   => $grand_round_2_total,
    'round_3_total'   => $grand_round_3_total,
    'round_4_total'   => $grand_round_4_total,
    'round_5_total'   => $grand_round_5_total,
    'male'            => $grand_male,
    'female'          => $grand_female,
    'other'           => $grand_other,
    'total'           => $grand_total,
    'e_male'          => $grand_e_male,
    'e_female'        => $grand_e_female,
    'e_other'         => $grand_e_other,
    'e_total'         => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage'      => $grand_percentage
  ];

  $data['grand_percentage'] = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;

  return $data;
}

}  // end class