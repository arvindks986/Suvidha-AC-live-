<?php 

namespace App\Http\Controllers\Admin\PollingStation;
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
use App\models\Admin\EndOfPollModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;

//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Classes\secureCode;


//POLLING STATION MODELS
use App\models\Admin\polling_station\PollingStationModel;

//current

class AroPollingStationController extends Controller {
  

  public function __construct(){
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->PollingStationModel = new PollingStationModel();
    if(!Auth::user()){
      return redirect('/officer-login');
    }
  }


  //ECI PS WISE DEATILS  STARTS
    public function AroPsWiseDetails(Request $request){  
      //ECI PS WISE DEATILS  TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
              
              $PsWiseDetailsWhere=['st_code'=>$user_data->st_code,'ac_no'=>$user_data->ac_no];

              $PsWiseDetails = DB::table('polling_station')->where($PsWiseDetailsWhere)
                               ->orderByRaw("CONVERT(`PS_NO`,INT) ASC")
                               ->get();

           //dd($PsWiseDetails);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($AllPartyList);

            return view('admin.pc.ro.voting.PsWiseDetails',['user_data' => $user_data,'PsWiseDetails' => $PsWiseDetails]);
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI PS WISE DEATILS  TRY CATCH BLOCK ENDS
        
    }
    //ECI PS WISE DEATILS  FUNCTION ENDS



}  // end class