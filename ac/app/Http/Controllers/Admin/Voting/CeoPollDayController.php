<?php namespace App\Http\Controllers\Admin\Voting;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Carbon\Carbon;
    use DB;
    use Illuminate\Support\Facades\Hash;
    use Validator;
    use Config;
    use \PDF;
    use App\commonModel;  
    use App\models\Admin\ReportModel;
    use App\models\Admin\PollDayModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\models\Admin\StateModel;

class CeoPollDayController extends Controller {
  
  public $base    = 'ro';
  public $folder  = 'eci';
  public $action    = 'acceo/voting/list-schedule';
  public $view_path = "admin.ac.ceo";

  public function __construct(){
    $this->middleware('ceo');
    $this->commonModel  = new commonModel();
    $this->report_model = new ReportModel();
    $this->voting_model = new PollDayModel();
    if(!Auth::user()){
      return redirect('/officer-login');
    }
  }

  public function state(Request $request){
    try{
      $data = [];
      $from_date  = NULL;
      $from_to    = NULL;
      
      //first argument must be string, second must be $request object if you want to verify base 64, send that variable in ccode parameter in request object using $request->merge(['ccode' => $somevalue]);
      $request_status = validate_request('',$request);
      if(!$request_status){
        return Redirect::to('logout');
      }
      //end validate request

      $request_array = [];
  
      $data['states'] = [];
      foreach(StateModel::get_states() as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }

      $data['phases'] = $this->report_model->get_phases();
      $data['phase'] = NULL;
      if($request->has('phase')){
        $data['phase'] = $request->phase;
        $request_array[] =  'phase='.$request->phase;
      }

      $data['constituency'] = NULL;
      if($request->has('constituency')){
        $data['constituency'] = $request->constituency;
        $request_array[] =  'constituency='.$request->constituency;
      }

      $data['state'] = Auth::user()->st_code;

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Poll Day Turnout Details';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }
      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      $data['filter_buttons'] = $title_array;
      //end set title

      $data['action']         = url($this->action).'/state';
      $data['redirect_href']  = url($this->action).'/state';

      $results              = [];
      $total_round_1_m= 0;
            $total_round_1_f= 0;
            $total_round_1_o= 0;
            $total_round_1_t= 0;
            $total_round_2_m= 0;
            $total_round_2_f= 0;
            $total_round_2_o= 0;
            $total_round_2_t= 0;
            $total_round_3_m= 0;
            $total_round_3_f= 0;
            $total_round_3_o= 0;
            $total_round_3_t= 0;
            $total_round_4_m= 0;
            $total_round_4_f= 0;
            $total_round_4_o= 0;
            $total_round_4_t= 0;
            $total_round_5_m= 0;
            $total_round_5_f= 0;
            $total_round_5_o= 0;
            $total_round_5_t= 0;
            $total_round_end_m= 0;
            $total_round_end_f= 0;
            $total_round_end_o= 0;
            $total_round_end_t= 0;
      
          $total_round_1_total   = 0;
          $total_round_2_total   = 0;
          $total_round_3_total   = 0;
          $total_round_4_total   = 0;
          $total_round_5_total    = 0;
          $total_round_end_total  = 0;

          $total_e_male     = 0;
          $total_e_female   = 0;
          $total_e_other    = 0;
          $total_e_total    = 0;


      $total_male     = 0;
      $total_female   = 0;
      $total_other    = 0;
      $total_total    = 0;

      $total_voter_male     = 0;
      $total_voter_female   = 0;
      $total_voter_other    = 0;

      $filter_election = [
        'state_code'    => $data['state'],
        'const_type'    => 'AC'
      ];

      $filter_report = array_merge($filter_election,[
        'ac_no'         => $data['constituency'],
        'phase_id'      => $data['phase'],
      ]);


      $lists_all    = $this->voting_model->get_scrutny_report_ceo($filter_election);
      $lists        = $this->voting_model->get_scrutny_report_ceo($filter_report);

      
      foreach ($lists as $lis) {   

        $const_name   = NULL;
        $const        = $this->commonModel->getacname($lis->ST_CODE,$lis->CONST_NO);
        $const_name   = trim($const->AC_NAME);

        $filter_data = [
            'st_code'       => $lis->ST_CODE,
            'const_type'    => $lis->CONST_TYPE,
            'const_no'      => $lis->CONST_NO,
            'phase'         => $data['phase'],
        ];


        $schedule_object  = $this->voting_model->get_schedule_detail($filter_data);

        
        //total
          $total_round        = $this->voting_model->get_total_round($filter_data);
          $total_round_1_total   += $total_round->round_1_total;
          $total_round_2_total   += $total_round->round_2_total;
          $total_round_3_total   += $total_round->round_3_total;
          $total_round_4_total   += $total_round->round_4_total;
          $total_round_5_total   += $total_round->round_5_total;
          $total_round_end_total   += $total_round->round_end_total;

          $total_voter_male     += $total_round->total_voter_male;
          $total_voter_female   += $total_round->total_voter_female;
          $total_voter_other    += $total_round->total_voter_other;

          $total_elector     = $this->voting_model->get_elector_total($filter_data);
          $total_e_male     += $total_elector->gen_m;
          $total_e_female   += $total_elector->gen_f;
          $total_e_other    += $total_elector->gen_o;
          $total_e_total    += $total_elector->gen_t;

          $total_round_1_m += $schedule_object['round_1_m'];
          $total_round_1_f += $schedule_object['round_1_f'];
          $total_round_1_o += $schedule_object['round_1_o'];
          $total_round_1_t += $schedule_object['round_1_t'];
          $total_round_2_m += $schedule_object['round_2_m'];
          $total_round_2_f += $schedule_object['round_2_f'];
          $total_round_2_o += $schedule_object['round_2_o'];
          $total_round_2_t += $schedule_object['round_2_t'];
          $total_round_3_m += $schedule_object['round_3_m'];
          $total_round_3_f += $schedule_object['round_3_f'];
          $total_round_3_o += $schedule_object['round_3_o'];
          $total_round_3_t += $schedule_object['round_3_t'];
          $total_round_4_m += $schedule_object['round_4_m'];
          $total_round_4_f += $schedule_object['round_4_f'];
          $total_round_4_o += $schedule_object['round_4_o'];
          $total_round_4_t += $schedule_object['round_4_t'];
          $total_round_5_m += $schedule_object['round_5_m'];
          $total_round_5_f += $schedule_object['round_5_f'];
          $total_round_5_o += $schedule_object['round_5_o'];
          $total_round_5_t += $schedule_object['round_5_t'];
          $total_round_end_m += $schedule_object['round_end_m'];
          $total_round_end_f += $schedule_object['round_end_f'];
          $total_round_end_o += $schedule_object['round_end_o'];
          $total_round_end_t += $schedule_object['round_end_t']; 

          $total_male   += $schedule_object['total_male'];
          $total_female += $schedule_object['total_female'];
          $total_other  += $schedule_object['total_other'];
          $total_total  += $schedule_object['total']; 

          $results[] = [
            'label'              => $lis->AC_NO.'-'.$lis->AC_NAME,
            'filter'             => implode('&', array_merge($request_array,['ccode' => 'ccode='.base64_encode($lis->CCODE)])),
            'const_no'           => $lis->AC_NO,
            'const_name'         => $lis->AC_NAME,
            'round_1_m'              => $schedule_object['round_1_m'],
            'round_1_f'              => $schedule_object['round_1_f'],
            'round_1_o'              => $schedule_object['round_1_o'],
            'round_1_t'              => $schedule_object['round_1_t'],
            'round_2_m'              => $schedule_object['round_2_m'],
            'round_2_f'              => $schedule_object['round_2_f'],
            'round_2_o'              => $schedule_object['round_2_o'],
            'round_2_t'              => $schedule_object['round_2_t'],
            'round_3_m'              => $schedule_object['round_3_m'],
            'round_3_f'              => $schedule_object['round_3_f'],
            'round_3_o'              => $schedule_object['round_3_o'],
            'round_3_t'              => $schedule_object['round_3_t'],
            'round_4_m'              => $schedule_object['round_4_m'],
            'round_4_f'              => $schedule_object['round_4_f'],
            'round_4_o'              => $schedule_object['round_4_o'],
            'round_4_t'              => $schedule_object['round_4_t'],
            'round_5_m'              => $schedule_object['round_5_m'],
            'round_5_f'              => $schedule_object['round_5_f'],
            'round_5_o'              => $schedule_object['round_5_o'],
            'round_5_t'              => $schedule_object['round_5_t'],
            'round_end_m'            => $schedule_object['round_end_m'],
            'round_end_f'            => $schedule_object['round_end_f'],
            'round_end_o'            => $schedule_object['round_end_o'],
            'round_end_t'            => $schedule_object['round_end_t'], 

            'total_male'             => $schedule_object['total_male'],
            'total_female'           => $schedule_object['total_female'],
            'total_other'            => $schedule_object['total_other'],
            'total'                  => $schedule_object['total'], 


            'href'                   => 'javascript:void(0)',
            'gen_m'             => $total_elector->gen_m,
            'gen_f'             => $total_elector->gen_f,
            'gen_o'             => $total_elector->gen_o,
            'gen_t'             => $total_elector->gen_t,
          ];                      
    }   


    $total_aggregate_elector = $total_e_total;
    $total_aggregate_voter   = $total_round_1_total + $total_round_2_total + $total_round_3_total + $total_round_4_total + $total_round_5_total + $total_round_end_total;

    //total voting
    $data['number_of_voting'] = 0;
    if($total_aggregate_elector > 0){
      $data['number_of_voting'] = round((($total_total*100)/$total_aggregate_elector),1);
    }


    //male percentage
    $data['male_percentage'] = 0;
    if($total_e_male > 0){
      $data['male_percentage'] = round((($total_voter_male*100)/$total_e_male),1);
    }

    $data['female_percentage'] = 0;
    if($total_e_female > 0){
      $data['female_percentage'] = round((($total_voter_female*100)/$total_e_female),1);
    }

    $data['other_percentage'] = 0;
    if($total_e_other > 0){
      $data['other_percentage'] = round((($total_voter_other*100)/$total_e_other),1);
    }

    $data['list_const'] = $lists_all;
    $data['results']    = $results;
    $data['user_data']  = Auth::user();
    $data['from']       = $from_date;
    $data['to']         = $from_to;

    // $data['downlaod_to_excel'] = url($this->action.'/excel').'?'.implode('&', $request_array);
    // $data['downlaod_to_pdf']   = url($this->action.'/pdf').'?'.implode('&', $request_array);

    $data['back_href']         = '';//url($this->action);

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path.'.voting.state_list', $data);

    }catch(\Exception $e){
      return Redirect::to('/acceo/dashboard');
    }
  }

}  // end class