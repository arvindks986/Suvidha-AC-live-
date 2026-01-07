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
use App\models\Admin\BoothAppRevamp\{PollingStation, VoterInfoModel, StateModel, AcModel, ElectorsVerificationByPsModel, TblAnalyticsDashboardModel};
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;

class ElectorsVerificationByPsController extends Controller {

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

  public function index(Request $request){
    
      $data = [];
      $request_array = [];

      $filter_ps = [
        'st_code'   => $this->st_code,
        'ac_no'     => $this->ac_no,
      ];

      $data['heading_title'] = '';

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
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['action']     = Common::generate_url($this->action.'/electors-verification-by-ps/post');
      $data['results'] = [];
      $filter = [
        'phase_no'        => $this->phase_no,
        'st_code'         => $this->st_code,
        'ac_no'           => $this->ac_no,
      ];

      $grand_e_male   = 0;
      $grand_e_female = 0;
      $grand_e_other  = 0;
      $grand_e_total  = 0;
      $total_verified = 0;
      $total_unverified = 0;
      $polling_stations     = PollingStation::get_polling_stations($filter);
      foreach ($polling_stations as $key => $iterate_p_s) {
        $ps_no = $iterate_p_s['PS_NO'];
        $filter_for_voters = array_merge($filter,['ps_no' => $iterate_p_s['PS_NO']]);
        $electors   = TblAnalyticsDashboardModel::get_aggregate_voters($filter_for_voters);

        $e_total  = $electors['e_male'] + $electors['e_female'] + $electors['e_other'];
        $grand_e_male   += $electors['e_male'];
        $grand_e_female += $electors['e_female'];
        $grand_e_other  += $electors['e_other'];
        $grand_e_total  += $e_total;

        $have_record = ElectorsVerificationByPsModel::total_count_record(array_merge($filter_for_voters,['is_verify' => 1]));
        if($have_record){
          $is_verify = true;
          $status = "Verified";
          $total_verified++;
        }else{
          $is_verify = false;
          $status = "Not Verified";
          $total_unverified++;
        }

        $data['results'][] = [
          'ps_name'         => $iterate_p_s['PS_NAME_EN'],
          'ps_no'           => $iterate_p_s['PS_NO'],
          'ps_name_and_no'  => $iterate_p_s['PS_NO'].'-'.$iterate_p_s['PS_NAME_EN'],
          'male'            => $electors['e_male'],
          'female'          => $electors['e_female'],
          'other'           => $electors['e_other'],
          'total'           => $e_total,
          'is_verify'       => $is_verify,
          'status'          => $status
        ];
      }
      $data['male']    = $grand_e_male;
      $data['female']  = $grand_e_female;
      $data['other']   = $grand_e_other;
      $data['total']   = $grand_e_total;
      $data['total_ps']         = count($polling_stations);
      $data['total_verified']   = $total_verified;
      $data['total_unverified'] = $total_unverified;
      $data['user_data']  =  Auth::user();
      return view($this->view.'.electors-verification-by-ps', $data);
  }
  
  public function post(Request $request){
   
    $rules = [
      'ps_no'  => 'required|validstring',
      'is_verify'  => 'required|in:0,1',
    ];
    
    $messages = [
      'ps_no.array'     => "Please select a PS",
      'ps_no.required'  => "Please select a PS"
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails())
    {
        return Response::json([
          'success' => false,
          'errors'  => $validator->getMessageBag()->toArray()
        ]);
    }

    if($request->ps_no == 'all'){
      $ps_nos = PollingStation::select('PS_NO as ps_no')->where([
          'ST_CODE'   => $this->st_code,
          'AC_NO'     => $this->ac_no
      ])->get()->toArray();
    }else{
      $ps_nos = [];
      $ps_nos[] = ['ps_no' => $request->ps_no];
    }

    DB::beginTransaction();
    try{
      foreach ($ps_nos as $key => $itr_ps_no) {
        ElectorsVerificationByPsModel::add_record([
          'st_code'   => $this->st_code,
          'dist_no'   => $this->dist_no,
          'ac_no'     => $this->ac_no,
          'ps_no'     => $itr_ps_no['ps_no'],
          'is_verify' => $request->is_verify,
        ]);
      }
   }catch(\Exception $e){
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();
    return Response::json([
        'success' => true,
        'messages' => "Updated successfully"
    ]);
  }

}  // end class