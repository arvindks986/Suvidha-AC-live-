<?php 
namespace App\Http\Controllers\Admin\BoothAppRevamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use App\commonModel;  
use App\models\Admin\BoothAppRevamp\{PollingStation, TblPollSummaryModel, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, TblPwdVoterModel, TblProDiaryModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;

//current

class EvmController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $phase_no       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $filter_role_id  = NULL;
  public $base            = 'roac';
  public $restricted_ps   = [];
  public $no_allowed_po   = 0;
  public $no_allowed_blo  = 2;
  public $no_allowed_pro  = 3;
  public $allowed_acs = [];
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
      $this->ps_no    = $default_values['ps_no'];
      $this->phase_no    = $default_values['phase_no'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id  = $default_values['role_id'];
      $this->base     = $default_values['base'];

      $object_setting         = Common::get_allowed_acs($request);
      $this->allowed_st_code  = $object_setting['allowed_st_code'];
      $this->allowed_dist_no  = $object_setting['allowed_dist_no'];
      $this->allowed_acs      = $object_setting['allowed_acs'];

      return $next($request);
    });
  }

  public function evm_comparision_state_report(Request $request){
    $data                   = [];
    $data['voter_turnouts'] = [];
    $filter = [
      'phase_no'         => $this->phase_no,
      'st_code'         => $this->st_code,
      'allowed_st_code' => $this->allowed_st_code
    ];

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
        'phase_no' => $this->phase_no
      ];

      $evm_sum = TblProDiaryModel::get_pro_diary($filter_for_voters);

      $data['voter_turnouts'][] = [
        'st_name'         => $iterate_state['ST_NAME'],
        'no_of_vote'      => $evm_sum['no_of_vote'],
        'no_of_vote_evm'  => $evm_sum['no_of_vote_evm'],
        'href'            => Common::generate_url("booth-app-revamp/ac/evm-comparision?st_code=".$iterate_state->ST_CODE)
      ];

    }

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/state/evm-comparision");
    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'ac_no'       => false,
      'ps_no'       => false,
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $data['heading_title'] = 'Blo/Pro Turnout';
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters'] = $form_filters;
    $data['user_data']  =   Auth::user();
    $data['heading_title_with_all'] = 'Blo/Pro Turnout';
    $data['drill_level'] = 1;
    return view($this->view.'.evm-comparision-state', $data);

  }


  public function evm_comparision_ac_report(Request $request){
    $data                   = [];
    $data['voter_turnouts'] = [];
    $filter = [
      'st_code'         => $this->st_code,
      'phase_no'         => $this->phase_no,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url('booth-app-revamp/state/evm-comparision'),
      'name' => 'Back',
      'target' => false,
    ];

    $acs_results = AcModel::get_acs($filter);

    foreach ($acs_results as $key => $iterate_ac) {
      $filter_for_voters = [
        'phase_no' => $this->phase_no,
        'st_code' => $iterate_ac['st_code'],
        'ac_no'   => $iterate_ac['ac_no'],
      ];

      $evm_sum = TblProDiaryModel::get_pro_diary($filter_for_voters);

      $st_name = '';
      $state_object = StateModel::get_state_by_code($iterate_ac['st_code']);
      if($state_object){
        $st_name = $state_object['ST_NAME'];
      }

      $data['voter_turnouts'][] = [
        'st_name'         => $st_name,
        'ac_no'           => $iterate_ac['ac_no'],
        'ac_name'         => $iterate_ac['ac_name'],
        'no_of_vote'      => $evm_sum['no_of_vote'],
        'no_of_vote_evm'  => $evm_sum['no_of_vote_evm'],
        'href'			=> Common::generate_url("booth-app-revamp/ps/evm-comparision?st_code=".$iterate_ac['st_code'].'&ac_no='.$iterate_ac['ac_no'])
      ];

    }

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/ac/evm-comparision");
    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'ac_no'       => true,
      'ps_no'       => false,
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $data['heading_title'] = 'Blo/Pro Turnout';
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters'] = $form_filters;
    $data['user_data']  =   Auth::user();
    $data['heading_title_with_all'] = 'Blo/Pro Turnout';
    $data['drill_level'] = 2;

    return view($this->view.'.evm-comparision-ac', $data);
  }

  public function evm_comparision_ps_report(Request $request){

    $data                   = [];
    $data['voter_turnouts'] = [];
    $filter = [
      'phase_no'         => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url('booth-app-revamp/ac/evm-comparision').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no.'&phase_no='.$this->phase_no,
      'name' => 'Back',
      'target' => false,
    ];

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

      $evm_sum = TblProDiaryModel::get_pro_diary($filter_for_voters);

      $st_name = '';
      $state_object = StateModel::get_state_by_code($iterate_p_s['ST_CODE']);
      if($state_object){
        $st_name = $state_object['ST_NAME'];
      }

      $ac_name = '';
      $ac_object = AcModel::get_ac(['phase_no' => $this->phase_no,'state' => $iterate_p_s['ST_CODE'], 'ac_no' => $iterate_p_s['AC_NO']]);
      if($ac_object){
        $ac_name = $ac_object['ac_name'];
      }

      $data['voter_turnouts'][] = [
        'st_name'         => $st_name,
        'ac_no'           => $iterate_p_s['AC_NO'],
        'ac_name'         => $ac_name,
        'ps_no'           => $iterate_p_s['PS_NO'],
        'ps_name'         => $iterate_p_s['PS_NAME_EN'],
        'no_of_vote'      => $evm_sum['no_of_vote'],
        'no_of_vote_evm'  => $evm_sum['no_of_vote_evm'],
        'href'			=> 'javascript:void(0)'
      ];

    }

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/ps/evm-comparision");
    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'ac_no'       => true,
      'ps_no'       => false,
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $data['heading_title'] = 'Blo/Pro Turnout';
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters'] = $form_filters;
    $data['user_data']  =   Auth::user();
    return view($this->view.'.evm-comparision-ps', $data);
  }

  public function get_aggregate_pro_diary(Request $request){

    $data               = [];
    $data['buttons']    = [];
    $filter = [
      'phase_no'         => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'ps_no'           => $this->ps_no,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];

    $data['results']    = [];
    $results = PollingStation::get_polling_stations($filter);
    foreach ($results as $key => $result) {

      $st_name = '';
      $state_object = StateModel::get_state_by_code($result['ST_CODE']);
      if($state_object){
        $st_name = $state_object['ST_NAME'];
      }

      $ac_name = '';
      $ac_object = AcModel::get_ac(['phase_no' => $this->phase_no,'state' => $result['ST_CODE'], 'ac_no' => $result['AC_NO']]);
      if($ac_object){
        $ac_name = $ac_object['ac_name'];
      }

      $pro_diary_data = TblPollSummaryModel::get_aggregate_pro_diary([
        'phase_no' => $this->phase_no,
        'st_code' => $result['ST_CODE'],
        'ac_no'   => $result['AC_NO'],
        'ps_no'   => $result['PS_NO'],
      ]);



      $data['results'][] = [
        'st_name'         => $st_name,
        'ac_no'           => $result['AC_NO'],
        'ac_name'         => $ac_name,
        'ps_no'           => $result['PS_NO'],
        'ps_name'         => $result['PS_NAME_EN'],
        'poll_start_datetime' => $pro_diary_data['poll_start_datetime'], 
        'electors' => $pro_diary_data['electors'], 
        'pro_turn_out' => $pro_diary_data['pro_turn_out'], 
        'blo_turn_out' => $pro_diary_data['blo_turn_out'], 
        'total_turn_out' => $pro_diary_data['total_turn_out'], 
        'total_male_turn_out' => $pro_diary_data['total_male_turn_out'], 
        'total_female_turn_out' => $pro_diary_data['total_female_turn_out'], 
        'total_other_turn_out' => $pro_diary_data['total_other_turn_out'], 
        'scan_qr' => $pro_diary_data['scan_qr'], 
        'scan_srno' => $pro_diary_data['scan_srno'], 
        'scan_epicno' => $pro_diary_data['scan_epicno'], 
        'scan_name' => $pro_diary_data['scan_name'], 
        'scan_mobile' => $pro_diary_data['scan_mobile'], 
        'aver_scan_qr' => $pro_diary_data['aver_scan_qr'], 
        'aver_scan_srno' => $pro_diary_data['aver_scan_srno'], 
        'aver_scan_epic' => $pro_diary_data['aver_scan_epic'], 
        'aver_scan_name' => $pro_diary_data['aver_scan_name'], 
        'aver_scan_mobile' => $pro_diary_data['aver_scan_mobile'], 
        'scan_average_time' => $pro_diary_data['scan_average_time'], 
        'poll_end_datetime' => $pro_diary_data['poll_end_datetime'], 
        'no_of_vote' => $pro_diary_data['no_of_vote'], 
        'no_of_vote_evm' => $pro_diary_data['no_of_vote_evm'], 
        'no_of_agent' => $pro_diary_data['no_of_agent'], 
        'no_of_edc' => $pro_diary_data['no_of_edc'], 
        'no_of_overseas' => $pro_diary_data['no_of_overseas'], 
        'no_of_proxy' => $pro_diary_data['no_of_proxy'], 
        'no_of_tendered' => $pro_diary_data['no_of_tendered']
      ];
    }
   
    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/pro-diary");
    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'ac_no'       => true,
      'ps_no'       => true,
      'designation' => false,
      'allowed_acs'     => $this->allowed_acs,
      'allowed_st_code' => $this->allowed_st_code,
      'allowed_dist_no' => $this->allowed_dist_no,
    ];
    $data['heading_title'] = 'PRO Diary';
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters'] = $form_filters;
    $data['user_data']  =   Auth::user();
    return view($this->view.'.pro-diary', $data);
  }

}  // end class