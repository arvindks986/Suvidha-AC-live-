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
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, TblPollSummaryModel, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, JsonFile, TblPwdVoterModel, OfficerAssignmentPsModel,TblAnalyticsDashboardModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;

//current

class StaticticsController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $filter_role_id  = NULL;
  public $is_activated    = NULL;
  public $base            = 'roac';
  public $restricted_ps   = [];
  public $allowed_acs = [];
  public $allowed_dist_no = [];
  public $allowed_st_code = [];
  public $cache = true;
  public $action_state  = 'eci/booth-app-revamp/state/blo-pro-difference';
  public $action_ac     = 'eci/booth-app-revamp/ac/blo-pro-difference';

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
  
  //blo_pro_state_report 
  public function blo_pro_state_report(Request $request){
    $data                   = [];
    $data['voter_turnouts'] = [];
    $title_array  = [];
    $request_array = [];
    $data['user_data']  =   Auth::user();

    if($this->phase_no){
      $request_array[]  =  'phase_no='.$this->phase_no;
    }
    if($this->st_code){
       $request_array[]  =  'st_code='.$this->st_code;
    }
    if($this->dist_no){
      $request_array[]  =  'dist_no='.$this->dist_no;
    }
    if($this->ac_no){
      $request_array[]  =  'ac_no='.$this->ac_no;
    }

    $filter = [
      'phase_no'         => $this->phase_no,
      'st_code'         => $this->st_code,
      'allowed_st_code' => $this->allowed_st_code
    ];

    $this->action_state =Common::generate_url('booth-app-revamp/state/blo-pro-difference');
    $this->action_ac =Common::generate_url('booth-app-revamp/ac/blo-pro-difference');

    //GET STATE NAME, DIST NAME AND AC NAMECODE STARTS
    if($this->st_code){
        $state_object = StateModel::get_state_by_code($this->st_code);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
    }
    if($this->dist_no){
        $dist_object = DistrictModel::get_district([
          'dist_no' => $this->dist_no,
          'st_code' => $this->st_code
        ]);
        if($dist_object){
          $title_array[]  = "District: ".$dist_object['dist_name'];
        }
    }
    if($this->ac_no){
        $ac_object = AcModel::get_ac([
          'ac_no' => $this->ac_no,
          'state' => $this->st_code
        ]);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
    }
    //GET STATE NAME, DIST NAME AND AC NAME CODE ENDS

    //SETTING TITILE OF THE PAGE 
    $data['heading_title']  = 'Booth App - Blo/Pro Turnout';
    $data['filter_buttons'] = $title_array;
    $data['filter']   = implode('&', array_merge($request_array));
    $data['action']         = url($this->action_state);

    $grand_e_male   = 0;
    $grand_e_female = 0;
    $grand_e_other  = 0;
    $grand_e_total  = 0;
    $grand_male   = 0;
    $grand_female = 0;
    $grand_other  = 0;
    $grand_total  = 0;
    $grand_queue  = 0;

    //SETTING BUTTONS FOR REPORTS STARTS
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];
    //SETTING BUTTONS FOR REPORTS ENDS

    $states_results = StateModel::get_states($filter);
    foreach ($states_results as $key => $iterate_state) {

      $filter_for_voters = [
        'st_code' => $iterate_state->ST_CODE,
        'phase_no' => $this->phase_no	
      ];
      $stats_sum = TblAnalyticsDashboardModel::total_blo_pro_turnout_statics($filter_for_voters);

      $data['voter_turnouts'][] = [
        'st_name'         => $iterate_state['ST_NAME'],
        'blo_turnout'     => (int)$stats_sum['blo_turn_out'],
        'pro_turnout'     => (int)$stats_sum['pro_turn_out'],
        'difference'      => $stats_sum['blo_turn_out'] - $stats_sum['pro_turn_out'],
        'href'      => Common::generate_url("booth-app-revamp/ac/blo-pro-difference?st_code=".$iterate_state->ST_CODE)
      ];

    }

    

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/state/blo-pro-difference");
    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'ac_no'       => false,
      'ps_no'       => false,
      'designation' => false,
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters'] = $form_filters;
    $data['heading_title_with_all'] = 'Blo/Pro Turnout';
    $data['drill_level'] = 1;

    if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
     }
    
    return view($this->view.'.blo-pro-difference-state', $data);

  }
  //blo_pro_state_report ends

  //blo_pro_state_report excel function starts
    public function blo_pro_state_report_excel(Request $request){

    set_time_limit(6000);
    $data = $this->blo_pro_state_report($request->merge(['is_excel' => 1]));
    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['State/UT Name','Total BLO Scan','Total PO Scan','Total Difference'];

    $blo_turnout = '0';
    $pro_turnout = '0';
    $difference = '0';
 
    foreach ($data['voter_turnouts'] as $lis) {

            $export_data[] = [
                                  
                                  $lis['st_name'],
                                  ($lis['blo_turnout'])?$lis['blo_turnout']:'0',
                                  ($lis['pro_turnout'])?$lis['pro_turnout']:'0',
                                  ($lis['difference'])?$lis['difference']:'0',
                            ];

            $blo_turnout +=   $lis['blo_turnout'];
            $pro_turnout +=   $lis['pro_turnout'];
            $difference  +=   $lis['difference'];

    }

    $totalvalues = array('Total',$blo_turnout,$pro_turnout,$difference);
    array_push($export_data,$totalvalues);

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:D1');
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');

  }
  //blo_pro_state_report excel function ends

  //blo_pro_state_report pdf function ends
  public function blo_pro_state_report_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->blo_pro_state_report($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view.'.blo-pro-difference-state-pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  //blo_pro_state_report pdf function ends

  //blo_pro_ac_report starts
  public function blo_pro_ac_report(Request $request){
    $data                   = [];
    $data['voter_turnouts'] = [];

    $request_array = [];
    $data['user_data']  =   Auth::user();

    if($this->phase_no){
      $request_array[]  =  'phase_no='.$this->phase_no;
    }
    if($this->st_code){
       $request_array[]  =  'st_code='.$this->st_code;
    }
    if($this->dist_no){
      $request_array[]  =  'dist_no='.$this->dist_no;
    }
    if($this->ac_no){
      $request_array[]  =  'ac_no='.$this->ac_no;
    }

    $filter = [
      'phase_no'         => $this->phase_no,
      'st_code'         => $this->st_code,
      'allowed_st_code' => $this->allowed_st_code
    ];

    $this->action_state =Common::generate_url('booth-app-revamp/state/blo-pro-difference');
    $this->action_ac =Common::generate_url('booth-app-revamp/ac/blo-pro-difference');

    //GET STATE NAME, DIST NAME AND AC NAMECODE STARTS
    if($this->st_code){
        $state_object = StateModel::get_state_by_code($this->st_code);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
    }
    if($this->dist_no){
        $dist_object = DistrictModel::get_district([
          'dist_no' => $this->dist_no,
          'st_code' => $this->st_code
        ]);
        if($dist_object){
          $title_array[]  = "District: ".$dist_object['dist_name'];
        }
    }
    if($this->ac_no){
        $ac_object = AcModel::get_ac([
          'ac_no' => $this->ac_no,
          'state' => $this->st_code
        ]);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
    }
    //GET STATE NAME, DIST NAME AND AC NAME CODE ENDS

    //SETTING TITILE OF THE PAGE 
    $data['heading_title']  = 'Booth App - Blo/Pro Turnout';
    $data['filter_buttons'] = $title_array;
    $data['filter']   = implode('&', array_merge($request_array));
    $data['action']         = url($this->action_ac);


    //SETTING BUTTONS FOR REPORTS STARTS
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_ac.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_ac.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];
    //SETTING BUTTONS FOR REPORTS ENDS
      $states = StateModel::get_states();

      $data['states'] = [];

    foreach($states as $result){

        //FOR CEO 
        if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' => $result->ST_NAME,
          ];
        }
  
        //FOR ECI
        if(Auth::user()->role_id == '7'){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name'    => $result->ST_NAME,
          ];
        }

        //FOR DISTRICT NODAL OFFICER 
        if(Auth::user()->role_id == '5' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' =>    $result->ST_NAME,
          ];
        }

        //FOR RO OFFICER 
        if(Auth::user()->role_id == '19' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'st_code' => $result->ST_CODE,
              'name' =>    $result->ST_NAME,
          ];
        }


     }

    $filter_for_voters = [
        'phase_no' => $this->phase_no,
        'st_code' => $this->st_code,
        'dist_no' => $this->dist_no,
        'ac_no' => $this->ac_no,
      ];

    //STATE LOOP STARTS
    foreach ($data['states'] as $state_result) {


    //STATE NAME
    if($state_result){
        $state_name = $state_result['name'];
    }

    $filter_election = [
      'phase_no' => $this->phase_no,
      'st_code' => $this->st_code,
      'dist_no' => $this->dist_no,
      'ac_no' => $this->ac_no,
    ];

      
      //AC LOOP STARTS
      $acs_results = AcModel::get_acs($filter_election);
       
         foreach ($acs_results as $key => $iterate_ac) {

            $state_name = '';
            //PS COUNT
            $filter_for_voters = [
              'phase_no' => $this->phase_no,
              'st_code' => $this->st_code,
              'dist_no' => $this->dist_no,
              'ac_no' => $iterate_ac['ac_no'],
            ];
            $stats_sum = TblAnalyticsDashboardModel::total_blo_pro_turnout_statics($filter_for_voters);

          $individual_filter_array          = [];
          $individual_filter_array['st_code'] = 'st_code='.$state_result['st_code'];
          
          if($this->dist_no){
            $individual_filter_array['dist_no'] = 'dist_no='.$this->dist_no;
          }
          if($this->ac_no){
            $individual_filter_array['ac_no'] = 'ac_no='.$this->ac_no;
          }else{
            $individual_filter_array['ac_no'] = 'ac_no='.$iterate_ac['ac_no'];
          }
          if($this->phase_no){
            $individual_filter_array['phase_no'] = 'phase_no='.$this->phase_no;
          }
          $individual_filter                = implode('&', $individual_filter_array);

            //SETTING DATABASE RESULTS FOR 
             $data['voter_turnouts'][] = [
              'st_name'         => $state_result['name'],
              'st_code'         => $state_result['st_code'],
              'ac_no'           => $iterate_ac['ac_no'],
              'ac_name'         => $iterate_ac['ac_name'],
              'blo_turnout'     => (int)$stats_sum['blo_turn_out'],
              'pro_turnout'     => (int)$stats_sum['pro_turn_out'],
              'difference'      => $stats_sum['blo_turn_out'] - $stats_sum['pro_turn_out'],
              'href'      => Common::generate_url("booth-app-revamp/ps/blo-pro-difference?st_code=".$state_result['st_code'].'&ac_no='.$iterate_ac['ac_no'])
            ]; 

            }//AC LOOP ENDS

    }//STATE LOOP ENDS

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/ac/blo-pro-difference");
    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'ac_no'       => true,
      'ps_no'       => false,
      'designation' => false,
    ];

    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters'] = $form_filters;
    $data['user_data']  =   Auth::user();
    $data['heading_title_with_all'] = 'Blo/Pro Turnout';
    $data['drill_level'] = 2;

    if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
     }

    return view($this->view.'.blo-pro-difference-ac', $data);
  }
  //blo_pro_ac_report ends

  //blo_pro_ac_report excel function starts
    public function blo_pro_ac_report_excel(Request $request){

    set_time_limit(6000);
    $data = $this->blo_pro_ac_report($request->merge(['is_excel' => 1]));
    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['State/UT Name','AC No','AC Name','Total BLO Scan','Total PO Scan','Total Difference'];

    $blo_turnout = '0';
    $pro_turnout = '0';
    $difference = '0';
 
    foreach ($data['voter_turnouts'] as $lis) {

            $export_data[] = [
                                  
                                  $lis['st_name'],
                                  $lis['ac_no'],
                                  $lis['ac_name'],
                                  ($lis['blo_turnout'])?$lis['blo_turnout']:'0',
                                  ($lis['pro_turnout'])?$lis['pro_turnout']:'0',
                                  ($lis['difference'])?$lis['difference']:'0',
                            ];

            $blo_turnout +=   $lis['blo_turnout'];
            $pro_turnout +=   $lis['pro_turnout'];
            $difference  +=   $lis['difference'];

    }

    $totalvalues = array('Total','','',$blo_turnout,$pro_turnout,$difference);
    array_push($export_data,$totalvalues);

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:F1');
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');

  }
  //blo_pro_ac_report excel function ends

  //blo_pro_ac_report pdf function ends
  public function blo_pro_ac_report_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->blo_pro_ac_report($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view.'.blo-pro-difference-ac-pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  //blo_pro_ac_report pdf function ends

  public function blo_pro_ps_report_old(Request $request){

    $data                   = [];
    $data['voter_turnouts'] = [];
    $filter = [
      'phase_no'         => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
    ];

    //buttons
    $data['buttons']    = [];
    $data['buttons'][]    = [
      'href' => Common::generate_url('booth-app-revamp/ac/blo-pro-difference').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no.'&phase_no='.$this->phase_no,
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
      $filter_for_voters = array_merge(['phase_no' => $filter['phase_no'],'st_code' => $filter['st_code'],'ac_no' => $iterate_p_s['AC_NO'],'ps_no' => $iterate_p_s['PS_NO']]);
	  
	  //dd($filter_for_voters);

      $stats_sum = TblPollSummaryModel::total_blo_pro_turnout_statics($filter_for_voters);

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
        'blo_turnout'     => (int)$stats_sum['blo_turn_out'],
        'pro_turnout'     => (int)$stats_sum['pro_turn_out'],
        'difference'      => $stats_sum['blo_turn_out'] - $stats_sum['pro_turn_out'],
        'href'			=> 'javascript:void(0)'
      ];

    }
		
	//echo '<pre>'; print_r($data['voter_turnouts']); die;

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/ps/blo-pro-difference");
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
    $data['drill_level'] = 3;

    return view($this->view.'.blo-pro-difference-ps', $data);
  }


   
   //blo_pro_ps_report starts
    public function blo_pro_ps_report(Request $request){
 
    $data                   = [];
    $data['voter_turnouts'] = [];

    $request_array = [];
    $data['user_data']  =   Auth::user();

    if($this->phase_no){
      $request_array[]  =  'phase_no='.$this->phase_no;
    }
    if($this->st_code){
       $request_array[]  =  'st_code='.$this->st_code;
    }
    if($this->dist_no){
      $request_array[]  =  'dist_no='.$this->dist_no;
    }
    if($this->ac_no){
      $request_array[]  =  'ac_no='.$this->ac_no;
    }

    $is_activated           = NULL;
	if($request->has('is_vote')){
		$is_activated = $request->is_vote;
		$request_array[] = 'is_vote='.$this->is_activated;
	  }

    //buttons
    $data['buttons']    = [];
   /* $data['buttons'][]    = [
      'href' => Common::generate_url('booth-app-revamp/ps/blo-pro-difference').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no.'&phase_no='.$this->phase_no.'&is_vote='.$is_activated.'&pdf=yes',
      'name' => 'Download PDF',
      'target' => '_blank',
    ];*/

    //SETTING BUTTONS FOR REPORTS STARTS
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  Common::generate_url('booth-app-revamp/ps/blo-pro-difference/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  Common::generate_url('booth-app-revamp/ps/blo-pro-difference/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];
    //SETTING BUTTONS FOR REPORTS ENDS

    $grand_e_male   = 0;
    $grand_e_female = 0;
    $grand_e_other  = 0;
    $grand_e_total  = 0;
    $grand_male   = 0;
    $grand_female = 0;
    $grand_other  = 0;
    $grand_total  = 0;
    $grand_queue  = 0;

    $filter = [
      'phase_no'        => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'ps_no'           => $this->ps_no,
	    'is_vote'        => $is_activated
    ];


//dd($filter);

	$stats_sum = TblAnalyticsDashboardModel::total_blo_pro_zero($filter);
  
    //$polling_stations = PollingStation::get_polling_stations($filter);

    foreach ($stats_sum as $key => $iterate_p_s) {
      $ps_no = $iterate_p_s['PS_NO'];
      	  
	  //dd($filter_for_voters);

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
	    
				
		/*$blo_information = \App\models\Admin\BoothAppRevamp\PollingStationOfficerModel::where([
		'ST_CODE'=>$iterate_p_s['ST_CODE'],
		'AC_NO' => $iterate_p_s['AC_NO'],
		'PS_NO' => $iterate_p_s['PS_NO'], 
		'role_id' => '33'
		])->orderBy('role_level')->first();*/


    $filter_blo = [
      'phase_no' => $this->phase_no,
      'st_code'  => $this->st_code,
      'ac_no'    => $this->ac_no,
      'ps_no'    => $ps_no,
      'role_id' => '33'
    ];

    $blo_information = OfficerAssignmentPsModel::get_officer($filter_blo);

    $filter_po = [
      'phase_no' => $this->phase_no,
      'st_code'  => $this->st_code,
      'ac_no'    => $this->ac_no,
      'ps_no'    => $ps_no,
      'role_id' => '34'
    ];
	
		$po_information = OfficerAssignmentPsModel::get_officer($filter_po);

    $filter_pro = [
      'phase_no' => $this->phase_no,
      'st_code'  => $this->st_code,
      'ac_no'    => $this->ac_no,
      'ps_no'    => $ps_no,
      'role_id' => '35'
    ];		
		
		$pro_information = OfficerAssignmentPsModel::get_officer($filter_pro); 
		
		//dd($blo_information);
	
			
			  $data['voter_turnouts'][] = [
				'st_name'         => $st_name,
				'ac_no'           => $iterate_p_s['AC_NO'],
				'ac_name'         => $ac_name,
				'ps_no'           => $iterate_p_s['PS_NO'],
				'ps_name'         => $iterate_p_s['PS_NAME_EN'],
				'blo_name'        => $blo_information['name'],
				'blo_mobile'      => $blo_information['mobile_number'],
				'po_name'         => $po_information['name'],
				'po_mobile'       => $po_information['mobile_number'],
				'pro_name'        => $pro_information['name'],
				'pro_mobile'      => $pro_information['mobile_number'],
				'blo_turnout'     => (int)$iterate_p_s['blo_turn_out'],
				'pro_turnout'     => (int)$iterate_p_s['pro_turn_out'],
				'difference'      => $iterate_p_s['blo_turn_out'] - $iterate_p_s['pro_turn_out'],
				'href'			=> 'javascript:void(0)'
			  ];
			
    }
	//die();
		
	//echo '<pre>'; print_r($data['voter_turnouts']); die;

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/ps/blo-pro-difference");
	
    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'ac_no'       => true,
      'ps_no'       => false,
      'designation' => false
    ];
    $data['heading_title'] = 'BLO/PO Turnout';
    $form_filters = Common::get_form_filters($form_filter_array, $request);
	
	//activate filter
  $is_activated_value   = [];
  $is_activated_value[] = [
    'is_vote'  => 'BLO Votes 0',
    'type_filter'    => '1',
  ];
  $is_activated_value[] = [
    'is_vote'  => 'PO Votes 0',
    'type_filter'    => '2',
  ];
  $is_activated_value[] = [
    'is_vote'  => 'Both Votes 0',
    'type_filter'    => '3',
  ];
  $is_vote_array = [];
  foreach ($is_activated_value as $iterate_activate) {
    $is_active = false;
    if($is_activated == $iterate_activate['type_filter']){
      $is_active = true;
    }
    $is_vote_array[] = [
      'id'    => $iterate_activate['type_filter'],
      'name'      => $iterate_activate['is_vote'],
      'active'  => $is_active
    ];
  }
  
  $form_filters[] = [
    'id'      => 'is_vote',
    'name'    => 'Is Votes',
    'results' => $is_vote_array
  ];
  	
	
    $data['form_filters'] = $form_filters;
    $data['user_data']  =   Auth::user();
    $data['heading_title_with_all'] = 'Blo/Pro Turnout';
    $data['drill_level'] = 3;

    if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
     }
	
    return view($this->view.'.blo-pro-difference-ps', $data);
  }
  //blo_pro_ps_report ends


  //blo_pro_ps_report excel function starts
    public function blo_pro_ps_report_excel(Request $request){

    set_time_limit(6000);
    $data = $this->blo_pro_ps_report($request->merge(['is_excel' => 1]));
  
    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['State/UT Name','AC No','AC Name','PS No','PS Name','Blo Name','Blo Mobile','PO Name','PO Mobile','PRO Name','PRO Mobile','Total Blo Scan ','Total PO Scan','Total Difference'];

    $bloscan = '0';
    $poscan = '0';
    $difference = '0';
 
    foreach ($data['voter_turnouts'] as $lis) {

            $export_data[] = [
                                  
                                  $lis['st_name'],
                                  $lis['ac_no'],
                                  $lis['ac_name'],
                                  $lis['ps_no'],
                                  $lis['ps_name'],
                                  $lis['blo_name'],
                                  $lis['blo_mobile'],
                                  $lis['po_name'],
                                  $lis['po_mobile'],
                                  $lis['pro_name'],
                                  $lis['pro_mobile'],
                                  ($lis['blo_turnout'])?$lis['blo_turnout']:'0',
                                  ($lis['pro_turnout'])?$lis['pro_turnout']:'0',
                                  ($lis['difference'])?$lis['difference']:'0',
                            ];

            $bloscan +=   $lis['blo_turnout'];
            $poscan +=   $lis['pro_turnout'];
            $difference  +=   $lis['difference'];

    }

    $totalvalues = array('Total','','','','','','','','','','',$bloscan,$poscan,$difference);
    array_push($export_data,$totalvalues);

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:F1');
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');

  }
  //blo_pro_ps_report excel function ends

  //blo_pro_ps_report pdf function ends
  public function blo_pro_ps_report_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->blo_pro_ps_report($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view.'.blo-pro-difference-ps-pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  //blo_pro_ps_report pdf function ends
  

}  // end class