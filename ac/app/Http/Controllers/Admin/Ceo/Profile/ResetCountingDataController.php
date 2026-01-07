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
use App\models\Admin\ResetCoutingDataModel;

class ResetCountingDataController extends Controller {

  public $action = "acceo/officer/reset-couting";
  public function __construct(){
    $this->commonModel  = new commonModel();
  }

  public function index(Request $request){
    $data 					= [];
    $data['buttons']        = [];
    $data['heading_title']  = "List of Officers(RO)";
    $data['action']         = url($this->action);
    $data['user_data']      = Auth::user();
    $data['results'] 		= [];
    $results = ResetCoutingDataModel::get_users([
    	'st_code' => Auth::user()->st_code,
    	'role_id' => [19]
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
    return view("admin/ac/ceo/profile/reset-couting", $data);
  }

  public function reset_counting_data(Request $request){

  		$id = 0;
  		if($request->has('reset_path')){
  			$id = base64_decode($request->reset_path);
  		}

  	
      $object = ResetCoutingDataModel::get_officer($id);

      if (!$object){
        return \Response::json([
          'status' => false,
          'message' => "Officer not exist. Please referesh and try again." 
        ]);
      }

      $data = [
        'st_code' => strtolower(Auth::user()->st_code),
        'ac_no'   => $object['ac_no'],
      ];

        DB::beginTransaction();
        try{   
          ResetCoutingDataModel::reset_counting_data_by_ro($data);
          DB::commit();
        }catch(\Exception $e){
          DB::rollback();
          \Response::json([
            'status'    => false,
            'message'   => "Please try again."
          ]);
        }

        return \Response::json([
            'status'    => true,
            'message'   => "Data has been cleared."
        ]);
  }

  public function reset_counting_state(Request $request){

      $data = [
        'st_code' => strtolower(Auth::user()->st_code),
      ];

        DB::beginTransaction();
        try{ 
          ResetCoutingDataModel::reset_counting_state($data);
          DB::commit();
        }catch(\Exception $e){
          DB::rollback();
          \Response::json([
            'status'    => false,
            'message'   => "Please try again."
          ]);
        }

        return \Response::json([
            'status'    => true,
            'message'   => "Data has been cleared."
        ]);
  }


}