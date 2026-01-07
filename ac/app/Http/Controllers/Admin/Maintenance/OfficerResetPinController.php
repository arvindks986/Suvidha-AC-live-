<?php
namespace App\Http\Controllers\Admin\Maintenance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB,Hash,Common;
use Validator;
use Config;
use App\commonModel;

class OfficerResetPinController extends Controller {

  public $folder        = "maintenance";
  public $view          = "admin.maintenance";
  public $action        = "maintenance/officer/reset-password";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $role_id       = 0;
  public $base          = '';

  public function __construct(){
    $this->commonModel  = new commonModel();
  }

  public function index(Request $request){
    $data 					= [];
    $data['buttons']        = [];
    $data['heading_title']  = "List of Officers";
    $data['action']         = url($this->action);
    $data['user_data']      = Auth::user();
    $data['pin_action']       = Common::generate_url("officer/update-pin");
    $data['password_action']  = Common::generate_url("officer/update-password");
    $data['menus']            = Common::header($request);
    $data['results'] 		      = [];
    $results = \App\models\Admin\OfficerModel::get_users([
    	'role_id' => [19,4,5]
    ]);
    foreach ($results as $key => $result) {
    	$data['results'][] 	= [
    		'id' 			=> $result['id'],
    		'officername' 	=> $result['officername'],
    		'designation' 	=> $result['designation'],
    		'email' 		=> $result['email'],
    		'mobile' 		   => $result['Phone_no'],
        'state_name'   => $result['state_name'],
    		'hash_id'  		=> base64_encode($result['id']),
    	];
    }
  
    return view($this->view.'.officers-list', $data);
  }

  public function update_pin(Request $request){

  		$id = 0;
  		if($request->has('reset_path')){
  			$id = $request->reset_path;
  		}
  		$request->merge([
  			'id' => base64_decode($id)
  		]);
  	
        $validator = Validator::make($request->all(),[
            'pin'              => 'required|confirmed|pin',
            'pin_confirmation' => 'required|pin',
            'id'			   => 'required|exists:officer_login,id'

        ],[
            'regex'     => 'Please enter only numeric value.',
            'numeric'   => 'Please enter only numeric value.',
            'pin'       => 'Please enter a valid 4 digit number.'
        ]);

        if ($validator->fails()){
            return \Response::json([
                'status' => false,
                'errors' => $validator->errors()->getMessageBag()
            ]);
        }

        $data           = [];
        $data['pin']    	= $request->pin;
        $data['user_id']    = $request->id;

        try{
            \App\models\Admin\OfficerModel::update_pin_by_state($data);
        }catch(\Exception $e){
            \Response::json([
                'status'    => false,
                'message'   => "Please try again."
            ]);
        }
        return \Response::json([
            'status'    => true,
            'message'   => "Pin has been updated successfully. Please login to continue"
        ]);
  }


   public function update_password(Request $request){

      $id = 0;
      if($request->has('reset_path')){
        $id = $request->reset_path;
      }
      $request->merge([
        'id' => base64_decode($id)
      ]);
    
        $validator = Validator::make($request->all(),[
            'password'              => 'required|confirmed|password',
            'password_confirmation' => 'required',
            'id'                    => 'required|exists:officer_login,id'

        ],[
            'regex'     => 'Please enter only numeric value.',
            'numeric'   => 'Please enter only numeric value.',
            'pin'       => 'Please enter a valid 4 digit number.'
        ]);

        if ($validator->fails()){
            return \Response::json([
                'status' => false,
                'errors' => $validator->errors()->getMessageBag()
            ]);
        }

        $data               = [];
        $data['password']   = $request->password;
        $data['user_id']    = $request->id;

        try{
            \App\models\Admin\OfficerModel::update_password_by_eci($data);
        }catch(\Exception $e){
            \Response::json([
                'status'    => false,
                'message'   => "Please try again."
            ]);
        }
        return \Response::json([
            'status'    => true,
            'message'   => "Password has been updated successfully. Please login to continue"
        ]);
  }


  

}