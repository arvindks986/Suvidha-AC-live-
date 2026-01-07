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
use App\models\Admin\BoothAppRevamp\{PollingStation, StateModel, AcModel, DistrictModel, ExemptPollingStationModel, PollingStationExemptWrite, PollingStationOfficerModel};
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class ExemptedPollingStationController extends Controller {

  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $dist_no       = NULL;
  public $role_id       = 0;
  public $ps_no         = NULL;
  public $phase_no      = NULL;
  public $filter_role_id  = NULL;
  public $no_allowed_po   = 2;
  public $no_allowed_blo  = 2;
  public $no_allowed_pro  = 2;
  public $no_allowed_sm   = 2;
  public $cache           = true;

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

  public function exempted(Request $request){
	
      $data = [];
      $request_array = [];

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
      $data['action']     = Common::generate_url($this->action.'/exempted/post');


      $filter_ps = [
        'st_code'   => $this->st_code,
        'ac_no'     => $this->ac_no,
        'paginate'  => false,
        'restricted_ps' => []
      ];

      $data['results'] = [];
      $results = ExemptPollingStationModel::get_exemted($filter_ps);
      
      foreach($results as $result){
        $ps_name = '';
        $poll_station = PollingStation::get_polling_station([
          'st_code' => $result['st_code'],
          'ac_no'   => $result['ac_no'],
          'ps_no'   => $result['ps_no'],
        ]);
        if($poll_station){
          $ps_name = $poll_station['PS_NAME_EN'];
        }

          $data['results'][] = [
            'ps_name'   => $ps_name,
            'ps_no'     => $result['ps_no'],
            'reason'    => $result['reason']
          ];
      }
		
      $data['user_data']  =  Auth::user();
      return view($this->view.'.exempted-polling-station', $data);
    try{
    }catch(\Exception $e){
      return Redirect::to($this->base.'/dashboard');
    }
  }
  
  public function generate_hash(){
		$udata = Auth::user();
		$one = $udata->id;
		$two=$udata->officername;
		$secret_key = 'AILvu7BWrhyDgEp_btapp@2020';
        //$hsh = hash('sha256', $roemail.$roid.$romobile);
		$thash = hash('sha256', $one.'AILvu7BWrhyDgEp_btapp@2020'.$two);
        return $thash;
	}
  
  public function post_exempted(Request $request){
	
    $rules = [
      'ps_no'  => 'required|validstring',
      'reason' => 'required|validstring'
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
	
	

    $merge            = [];
    $merge['st_code'] = $this->st_code;
    $merge['dist_no'] = $this->dist_no;
    $merge['ac_no']   = $this->ac_no;
    $request->merge($merge);
	
	$key = $this->generate_hash();
	
	
	
    DB::beginTransaction();
	
    try{
		
      $data = [
        'ps_no'       => $request->ps_no,
        'ac_no'       => $merge['ac_no'],
        'dist_no'     => $merge['dist_no'],
        'st_code'     => $merge['st_code'],
        'reason'      => $request->reason,
        'created_by'  => Auth::id()
      ];
	  
      ExemptPollingStationModel::add_exemted($data);
	  
     // PollingStationExemptWrite::add_exemted($data);
	  
      PollingStationOfficerModel::add_exemted($data);
	  
	  
	  $data_array =  array(
			"stcode"        => $data['st_code'],
			"acno"         => 	$data['ac_no'],
            "psno"         => 	$data['ps_no'],
            "parity_key"   => 	$key,
            "exempt"         => true
      
	);
	$make_call = $this->callAPI('POST', 'https://boothapp.eci.gov.in/boothapp4/public/ba_exempt', json_encode($data_array));
	$response = json_decode($make_call, true);
	
	}catch(\Exception $e){
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();

    Session::flash('status',1);
    Session::flash('flash-message',"Exempted has been added successfully.");
    return Response::json([
        'success' => true
    ]);
  }
  
  
  function callAPI($method, $url, $data){
   $curl = curl_init();
   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }
   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'APIKEY: 111111111111111111111',
      'Content-Type: application/json',
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}

  //exempted turnout
  public function turnout(Request $request){
    
      $data = [];
      $request_array = [];

      $filter_ps = [
        'st_code'   => $this->st_code,
        'ac_no'     => $this->ac_no,
        'ps_no'     => $this->ps_no,
      ];

      $data['polling_stations'] = [];
      $results    =   PollingStation::get_polling_stations($filter_ps);
      foreach ($results as $result) {
        $data['polling_stations'][] = [
          'ps_no'   => $result['PS_NO'],
          'ps_name' => $result['PS_NAME_EN'],
        ];
      }

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
      $data['action']     = Common::generate_url($this->action.'/exempted/post-turnout');

      $filter_ps = [
        'st_code'   => $this->st_code,
        'ac_no'     => $this->ac_no,
        'paginate'  => false,
        'restricted_ps' => []
      ];

      $data['results'] = [];
      $results = ExemptPollingStationModel::get_exemted($filter_ps);      
      foreach($results as $result){
        $ps_name = '';
        $poll_station = PollingStation::get_polling_station([
          'st_code' => $result['st_code'],
          'ac_no'   => $result['ac_no'],
          'ps_no'   => $result['ps_no'],
        ]);
        if($poll_station){
          $ps_name = $poll_station['PS_NAME_EN'];
        }

          $data['results'][] = array_merge([
            'ps_name'   => $ps_name,
            'ps_no'     => $result['ps_no'],
          ],$result->toArray());
      }

      $data['user_data']  =  Auth::user();
      return view($this->view.'.exempted-turnout-polling-station', $data);
    try{
    }catch(\Exception $e){
      return Redirect::to($this->base.'/dashboard');
    }
  }
  
  public function post_turnout(Request $request){
   
    $rules = [
      'round'   => 'required|in:0,1,2,3,4,5',
      'ps_no'   => 'required|min:0',
      'male'    => 'required|integer|min:0',
      'female'  => 'required|integer|min:0',
      'other'   => 'required|integer|min:0',
      'total'   => 'required|integer|min:0',
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

    $merge            = [];
    $merge['st_code'] = $this->st_code;
    $merge['dist_no'] = $this->dist_no;
    $merge['ac_no']   = $this->ac_no;
    $request->merge($merge);
    DB::beginTransaction();
    try{
      ExemptPollingStationModel::add_voter_turnout($request->all());
    }catch(\Exception $e){
      DB::rollback();
      return Response::json([
        'success' => false,
        'errors'  => ['warning'  => "Please Try Again."]
      ]);
    }
    DB::commit();

    Session::flash('status',1);
    Session::flash('flash-message',"Updated successfully.");
    return Response::json([
        'success' => true
    ]);
  }

}