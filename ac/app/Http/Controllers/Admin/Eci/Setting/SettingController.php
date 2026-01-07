<?php namespace App\Http\Controllers\Admin\Eci\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use \PDF;
use App\models\Admin\{SettingModel, AcModel, DistrictModel, StateModel};
use App\models\Admin\BoothAppRevamp\{BoothEnableAcsModel};
use App\Http\Requests\Admin\Setting\SettingRequest;


class SettingController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action        = 'eci/setting/setting/save';
  public $action_broadcast  = 'eci/setting/broadcast/save';
  public $view_path     = "admin.ac.eci";

  public function __construct(){
    if(!Auth::user()){
      return redirect('/officer-login');
    }
  }

  public function index(Request $request){

    try{ 
      $data = [];
      $request_array = []; 

      //set title
      $title_array  = [];
      $data['heading_title'] = "Setting";

      $data['filter_buttons'] = $title_array;
      $data['states'] = [];
      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['action']     = url($this->action);
      $object             = SettingModel::get_records('setting'); 
   
      if($request->old('two_step')){
        $data['two_step']  = $request->old('two_step');
      }else if(isset($object) && !empty($object['two_step'])){
        $data['two_step']  =  $object['two_step'];
      }else{
        $data['two_step']  =  '';
      }

      if($request->old('auto_logout_after')){
        $data['auto_logout_after']  = $request->old('auto_logout_after');
      }else if(isset($object) && !empty($object['auto_logout_after'])){
        $data['auto_logout_after']  =  $object['auto_logout_after'];
      }else{
        $data['auto_logout_after']  =  '';
      }

      if($request->old('two_step_login')){
        $data['two_step_login']  = $request->old('two_step_login');
      }else if(isset($object) && !empty($object['two_step_login'])){
        $data['two_step_login']  =  $object['two_step_login'];
      }else{
        $data['two_step_login']  =  '';
      }

      if($request->old('concurrent_login')){
        $data['concurrent_login']  = $request->old('concurrent_login');
      }else if(isset($object) && !empty($object['concurrent_login'])){
        $data['concurrent_login']  =  $object['concurrent_login'];
      }else{
        $data['concurrent_login']  =  '';
      }

      if($request->old('skip_password_network')){
        $data['skip_password_network']  = $request->old('skip_password_network');
      }else if(isset($object) && !empty($object['skip_password_network'])){
        $data['skip_password_network']  =  $object['skip_password_network'];
      }else{
        $data['skip_password_network']  =  '';
      }
	  
	  //booth app
      $data['i'] = 0;

      if($request->old('booth_app')){
        $data['booth_app']  = $request->old('booth_app');
      }else if(isset($object) && !empty($object['booth_app'])){
        $data['booth_app']  =  $object['booth_app'];
      }else{
        $data['booth_app']  =  [];
      }


      $data['states'] = [];
      $states = StateModel::get_states();
      foreach ($states as $key => $iterage_state) {
        $data['states'][] = [
          'st_code'     => $iterage_state['ST_CODE'],
          'st_name'     => $iterage_state['ST_NAME']
        ];
      }

      $data['districts'] = [];
      $districts = DistrictModel::get_districts();
      foreach ($districts as $key => $district_iterage) {
        $data['districts'][] = [
          'dist_no'     => $district_iterage['dist_no'],
          'dist_name'   => $district_iterage['dist_name'],
          'st_code'     => $district_iterage['st_code'],
        ];
      }
  
      $data['acs'] = [];
      $acs = AcModel::get_acs();
      foreach ($acs as $key => $ac_iterage) {
        $data['acs'][] = [
          'ac_no'         => $ac_iterage->ac_no,
          'ac_name'       => $ac_iterage->ac_name,
          'st_code'       => $ac_iterage->st_code,
          'dist_no'       => $ac_iterage->dist_no
        ];
      } 

      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];

      return view($this->view_path.'.setting.setting_form', $data);

    }catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }

  public function save(SettingRequest $request){
	  
	
	
    $data           = array();
	
    DB::beginTransaction();
    try{
      SettingModel::add_record('setting',$request);  

         
      if($request->has('booth_app')){
		  
        $data_booth_app = []; 
        foreach($request->booth_app as $iterate_booth_app){
          foreach ($iterate_booth_app['acs'] as $ac_no) {
            $data_booth_app[] = [
              'st_code' => $iterate_booth_app['states'],
              'dist_no' => $iterate_booth_app['districts'],
              'ac_no' => $ac_no
            ];
          }
        }
		
        foreach ($data_booth_app as $data_booth) {
          BoothEnableAcsModel::firstOrCreate($data_booth);
        }
      }

      DB::commit();  
	  
    }
    catch(\Exception $e){
	
      DB::rollback();
      Session::flash('error_mes',"Please try again.");  
      
    } 
    SettingModel::generate_cache();
    Session::flash('success_mes',"Setting has been updated.");   
    return Redirect::back();
 
  }

	
	public function broadcast(Request $request){

     try{
      $data = [];
      $request_array = []; 

      //set title
      $title_array  = [];
      $data['heading_title'] = "Broadcast Message to officers";

      $data['filter_buttons'] = $title_array;
      $data['states'] = [];
      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['action']     = url($this->action_broadcast);
      $object             = SettingModel::get_first_result('config'); 
   
      if($request->old('message')){
        $data['message']  = $request->old('message');
      }else if(isset($object) && !empty($object['message'])){
        $data['message']  =  $object['message'];
      }else{
        $data['message']  =  '';
      }

      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];

      return view($this->view_path.'.setting.broadcast_form', $data);

    }catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }


  public function save_broadcast(Request $request){
    $data  = $request->all();
    $rules = [
      'message' => 'required|validstring|max:400'
    ];

    $validator = Validator::make($data, $rules, []);
    if ($validator->fails())
    {
        return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

   try{
      SettingModel::add_broadcast($request->all());  
     }
    catch(\Exception $e){
      Session::flash('error_mes',"Please try again.");  
      return Redirect::back();
    } 
    Session::flash('success_mes',"Setting has been updated.");   
    return Redirect::back();
  }
  

}  // end class