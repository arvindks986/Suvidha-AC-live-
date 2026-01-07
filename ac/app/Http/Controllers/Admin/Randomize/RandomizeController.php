<?php namespace App\Http\Controllers\Admin\Randomize;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use DB, Validator, Session, \PDF, Auth, Response;
use App\Classes\xssClean;
use App\models\Admin\Randomize\RandomizeModel;

class RandomizeController extends Controller {

  public function __construct(){  
    $this->xssClean = new xssClean;
  }

  public function index(Request $request){

    $data                   = [];
    $data['heading_title']  = "Randomize/Dispatched Details";
    $data['action']			= url('roac/randomize-details/post');


    $randomize = RandomizeModel::get_randomization([
    	'st_code' => Auth::user()->st_code,
    	'ac_no' => Auth::user()->ac_no,
    ]);
  
    $data['have_record']      = ($randomize)?'1':'0';
  	$data['randomize_date'] 	= ($randomize)?date('d/m/Y',strtotime($randomize->randomize_date)):'';
  	$data['randomize_time'] 	= ($randomize)?$randomize->randomize_time:'';
  	$data['dispatched_date'] 	= ($randomize)?date('d/m/Y',strtotime($randomize->dispatched_date)):'';
  	$data['dispatched_time'] 	= ($randomize)?$randomize->dispatched_time:'';


    $data['user_data'] 		= Auth::user();
    return view('admin.randomize.randomize_detail', $data);

  }

  public function post(Request $request){
  	$rules = [
      'randomize_date'   	=> 'required|date_format:d/m/Y',
      'randomize_time' 		=> 'required|date_format:H:i:s',
      'dispatched_date'   	=> 'required|date_format:d/m/Y',
      'dispatched_time' 	=> "required|date_format:H:i:s",
    ];
    $messages = [];
    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails())
    {
        return Response::json([
          'success' => false,
          'errors'  => $validator->getMessageBag()->toArray()
        ]);
    }

    try{
	    $filter = [
	    	'st_code' => Auth::user()->st_code,
	    	'ac_no' => Auth::user()->ac_no,
	    ];
	    $data = [
	    	'randomize_date' 	=> $request->randomize_date,
	    	'randomize_time' 	=> $request->randomize_time,
	    	'dispatched_date' 	=> $request->dispatched_date,
	    	'dispatched_time'  	=> $request->dispatched_time
	    ];
	    RandomizeModel::add_or_update($filter,$data);

    }catch(\Exception $e){
  		return Response::json([
	  		'success' => false,
	  		'errors' => ["warning" => "Please try again."]
	  	]);
    }

  	Session::flash('status',1);
  	Session::flash('flash-message',"Updated successfully.");
  	return Response::json([
  		'success' => true,
  	]);
  }

}  // end class