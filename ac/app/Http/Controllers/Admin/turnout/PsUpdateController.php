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
use App\models\Admin\BoothAppRevamp\TblAnalyticsDashboardModel;
use App\models\Admin\polling_station\PollingStationModel;

class PsUpdateController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action     = 'update-ps-data-boothapp';
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

  
  
 public function updatepsdataboothapp(Request $request){

   // try{
		
		
      $data = [];
      $data['heading_title'] = 'Update Ps Wise Data';
      $data['action'] = $this->action;
	  $data['user_data']  =   Auth::user();
      $boothapp = TblAnalyticsDashboardModel::whereNotNull('poll_ended')->where('st_code','S14')->get();
	  
	  $i = 0;
	  
	  
	  if(count($boothapp) > 0){
		
		foreach($boothapp as $raw){
			
			$i++;
			
			$update = PollingStationModel::where('st_code',$raw->st_code)
							->where('ac_no',$raw->ac_no)
							->where('ps_no',$raw->ps_no)
		  ->update([
			'voter_male' => @$raw->male_turnout,
			'voter_female' => @$raw->female_turnout,
			'voter_other' => @$raw->other_turnout,
			'voter_total' => @$raw->male_turnout + @$raw->female_turnout + @$raw->other_turnout,
			'ps_poll_percentage' => round((@$raw->male_turnout + @$raw->female_turnout + @$raw->other_turnout)*100/(@$raw->male_electors+@$raw->female_electors+@$raw->other_electors),2)
		  ]);
		}
	
	  }else{
		  echo 'No record Found'; die();
	  }
						
	  
		echo $i.' Record Updated'; die();

		
	/*  }catch(\Exception $e){
      return Redirect::back()->with('error','Something went Wrong! Please again.');
    } */ 
  }
  


}  // end class