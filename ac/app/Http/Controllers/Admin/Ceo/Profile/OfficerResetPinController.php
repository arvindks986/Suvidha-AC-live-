<?php
namespace App\Http\Controllers\Admin\Ceo\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB,Hash;
use Validator;
use Config;
use App\commonModel;

class OfficerResetPinController extends Controller {

  public $action = "pcceo/officer/reset-password";
  public function __construct(){
    $this->commonModel  = new commonModel();
  }

  public function index(Request $request){
    $data 					= [];
    $data['buttons']        = [];
    $data['heading_title']  = "List of Officers";
    $data['action']         = url($this->action);
    $data['user_data']      = Auth::user();
    $data['results'] 		= [];
    $results = \App\models\Admin\OfficerModel::get_users([
    	'st_code' => Auth::user()->st_code,
    	'role_id' => [19,5]
    ]);
    foreach ($results as $key => $result) {
    	$data['results'][] 	= [
    		'id' 			=> $result['id'],
    		'officername' 	=> $result['officername'],
    		'designation' 	=> $result['designation'],
    		'email' 		=> $result['email'],
    		'mobile' 		=> $result['Phone_no'],
    		'hash_id'  		=> base64_encode($result['id']),
    	];
    }
    return view("admin/ac/ceo/profile/officers-list", $data);
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
            'pin'              => 'required|confirmed|digits:6',
            'pin_confirmation' => 'required|digits:6',
            'id'			   => 'required|exists:officer_login,id'

        ],[
            'regex'     => 'Please enter only numeric value.',
            'numeric'   => 'Please enter only numeric value.',
            'digits'       => 'Please enter a valid 6 digit number.'
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


}