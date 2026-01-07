<?php
namespace App\Http\Controllers\Admin\Eci\Profile;

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

class DefinalizeController extends Controller {

  public $action = "eci/officer/de-finalize";
  public function __construct(){
    $this->commonModel  = new commonModel();
  }

  public function index(Request $request){
    $data 					= [];
    $data['buttons']        = [];
    $data['heading_title']  = "List of PC's";
    $data['action']         = url($this->action);
    $data['user_data']      = Auth::user();
    $data['results'] 		= [];
    $results = \App\models\Admin\OfficerModel::get_users([
    	'role_id' => [19]
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
    return view("admin/ac/eci/profile/de-finalize-list", $data);
  }

  public function de_finalize_ro($id, Request $request){

  		if(\Auth::id() != '1'){
        return Redirect::back();
      } 
      $id    = base64_decode($id);
      $user = \App\models\Admin\OfficerModel::select('st_code','pc_no','ac_no')->find($id);
      if(!$user){
        \Session::flash('error_mes','PC not found');
        return Redirect::back();
      }

        DB::beginTransaction();
        try{  
          DB::table("counting_finalized_ac")->where('st_code', $user->st_code)->where('ac_no', $user->ac_no)->update([
            'finalize_by_ro'  => '0',
          ]);
          DB::commit(); 
        }catch(\Exception $e){
          DB::rollback();
          \Session::flash('error_mes','Please try again.');
          return Redirect::back();
        }

      \Session::flash('success_mes','De-finalized successfully.');
      return Redirect::back();
  }

  public function de_finalize_result($id, Request $request){
    if(\Auth::id() != '1'){
        return Redirect::back();
      } 
      $id    = base64_decode($id);
      $user = \App\models\Admin\OfficerModel::select('st_code','pc_no','ac_no')->find($id);
      if(!$user){
        \Session::flash('error_mes','PC not found');
        return Redirect::back();
      }

      DB::beginTransaction();
        try{
          DB::table("counting_finalized_ac")->where('st_code', $user->st_code)->where('ac_no', $user->ac_no)->update([
            'finalize_by_ro'  => '0',
          ]);
          DB::table("winning_leading_candidate")->where('st_code', $user->st_code)->where('ac_no', $user->ac_no)->update([
            'status'      => '0',
            'is_lottery'  => '0'
          ]);
          DB::commit(); 
        }catch(\Exception $e){
          DB::rollback();
          \Session::flash('error_mes','Please try again.');
          return Redirect::back();
        }

      \Session::flash('success_mes','De-finalized successfully.');
      return Redirect::back();
  }

}