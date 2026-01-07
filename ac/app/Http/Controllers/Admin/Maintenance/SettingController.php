<?php namespace App\Http\Controllers\Admin\Maintenance;
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
use App\Http\Requests\Admin\Setting\SettingRequest;

class SettingController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action        = 'maintenance/setting/setting/save';
  public $action_broadcast  = 'eci/setting/broadcast/save';
  public $view_path     = "admin.maintenance";

  public function index(Request $request){

    
      $data = [];
      $request_array = []; 

      //set title
      $title_array  = [];
      $data['heading_title'] = "Setting";

      $data['filter_buttons'] = $title_array;
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
      }else if(isset($object) && $object['auto_logout_after'] == 0){
        $data['auto_logout_after']  =  0;
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

      return view($this->view_path.'.setting_form', $data);

   try{ }catch(\Exception $e){
      return Redirect::to('/maintenance/dashboard');
    }
  }

  public function save(SettingRequest $request){
    $data           = array();
    
      SettingModel::add_record('setting',$request);  
      DB::commit();  
    DB::beginTransaction();
    try{}
    catch(\Exception $e){
      DB::rollback();
      Session::flash('error_mes',"Please try again....");  
      return Redirect::back();
    } 
    SettingModel::generate_cache();
    Session::flash('success_mes',"Setting has been updated.");   
    return Redirect::back();
 
  }  

}  // end class