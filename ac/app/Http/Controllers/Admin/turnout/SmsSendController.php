<?php 
namespace App\Http\Controllers\Admin\turnout;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use \PDF;
use App\commonModel;  
use App\models\Admin\PollDayModel;
use App\models\Admin\PollDayComparisionModel;
use App\models\Admin\ElectorModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;
use App\models\Admin\SmsOfficerLog;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;

class SmsSendController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action     = 'send-sms';
  public $view_path     = "admin.turnout";

  public function __construct(){
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->xssClean = new xssClean;
    $this->middleware(function ($request, $next) {
        
        return $next($request);
    });
  }

  

  public function index(Request $request){

      $data = [];
      $data['heading_title'] = 'Send Sms';
      $data['action'] = $this->action;
	  $data['user_data']  =   Auth::user();
      $data['results'] = DB::table('officer_for_sms')
	  //->where('status','1')
	  ->get();

      return view($this->view_path.'.template.send_sms', $data);

    try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }
  
 public function send(Request $request){

   // try{
		
	  $validator = Validator::make($request->all(), [
				'environment' => 'required',
				'message' => 'required'
				]);
				
				if($validator->fails()){
					return back()->with('error','Message is required field.');            
				} 
		
      $data = [];
      $data['heading_title'] = 'Send Sms';
      $data['action'] = $this->action;
	  $data['user_data']  =   Auth::user();
      $data['results'] = DB::table('officer_for_sms')->where('status','1')->get();
	  
		$mobile_message = $request->message;

		//dd($mobile_message);

		$i = 0;

		if($request->environment == '1'){
			$env = 'Test';
			$mobno = $request->mobile;
			$i++;
			$msgstatus = SmsgatewayHelper::gupshup($mobno, $mobile_message);
			
				$insert = new SmsOfficerLog;
	            $insert->mobile 			= $mobno;
	            $insert->message 			= $mobile_message;
				$insert->env 			= $env;
	            $insert->save();
			
			
		}else if($request->environment == '2'){
			$env = 'Live';
			foreach($data['results'] as $row){
				$mobno = $row->mobile;
				$i++;
				$msgstatus = SmsgatewayHelper::gupshup($mobno, $mobile_message);
				
				$insert = new SmsOfficerLog;
	            $insert->mobile 			= $mobno;
	            $insert->message 			= $mobile_message;
	            $insert->env 			= $env;
	            $insert->save();
			}
			
		}
		
      return redirect('eci/turnout/send-sms')->with('success',"Sms sent successfully to $i users for $env data .");
		
	/* }catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    } */
  }
  


}  // end class