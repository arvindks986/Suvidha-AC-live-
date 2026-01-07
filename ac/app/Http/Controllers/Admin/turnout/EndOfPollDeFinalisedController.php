<?php
namespace App\Http\Controllers\Admin\turnout;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use App\commonModel;  
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\ReportModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\StateModel;
use App\models\Admin\ScheduleDetailModel;

use Maatwebsite\Excel\Excel;


//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Classes\secureCode;

//END OF POLL FINALISE MODAL
use App\models\Admin\EndOfPollFinaliseModel;
use App\models\Admin\PhaseModel;

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;

date_default_timezone_set('Asia/Kolkata');
    

class EndOfPollDeFinalisedController extends Controller
{   

    public $folder        = 'eci';
    public $action_state  = 'eci/turnout/EndOfPollDeFinalised';
    public $action_ac     = 'eci/turnout/EndOfPollDeFinalisedList';
    public $view_path     = "admin.turnout";


    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){   
       //$this->middleware(['auth:admin','auth']);
       // $this->middleware('clean_url');
        //$this->middleware('clean_request');
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
       // $this->voting_model = new PollDayModel();
       // $this->EopFinalisedModal = new EndOfPollFinaliseModel();
        $this->xssClean = new xssClean;
        $this->middleware(function ($request, $next) {
            return $next($request);
        });

       // if(!Auth::user()){
        //  return redirect('/officer-login');
       // }
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
    */

    protected function guard(){
        return Auth::guard();
    }


    //END OF POLL FINALSED LIST REPORT STARTS
    public function EndOfPollDeFinalisedList(Request $request,$st_code='',$ac_no=''){  
      // END OF POLL FINALSED LIST REPORT TRY CATCH BLOCK STARTS

          $users=Session::get('admin_login_details');
          $user = Auth::user();   

         // if(session()->has('admin_login')){ 


			if($st_code && $ac_no){
				


				$update = ScheduleDetailModel::where('st_code',$st_code)
							->where('ac_no',$ac_no)
			  ->update([
				'end_of_poll_finalize' => 0,
				'updated_at_finalize' => Null
			  ]);
			  
				return redirect()->back()->withInput()->with('success','Definalized Successfully.');
				
			}


             
               //CHECKING FOR USER TYPE AND SETTING VARIABLES FOR IT STARTS
              if(Auth::user()->role_id == '7' || Auth::user()->role_id == '26'){

                $this->action_state  = 'eci/turnout/EndOfPollDeFinalisedList';
                $this->action_ac     = 'eci/turnout/EndOfPollDeFinalisedList';
              }

              $uid=$user->id;
              $user_data=$this->commonModel->getunewserbyuserid($uid);

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();

              $cur_time  = Carbon::now();
              $st_code = $user_data->st_code;
              $st_name = $user_data->placename;
              //dd($AllPartyList);

              $data['user_data']  =   Auth::user();  

          $default_phase = PhaseModel::get_current_phase();

          $request_array = []; 

          //SETTING STATE VARIABLE TO NULL IN STARTING
          $data['state'] = NULL;

          //CHECKING IF THE REQUEST CONTAINS THE STATE DATA OR NOT
          if($request->has('state')){
            $data['state'] = base64_decode($request->state);
            $request_array[] = 'state='.$request->state;
          }
		  
		  $data['election_type'] = NULL;
		  if($request->has('election_type')){     
			$data['election_type'] = $request->election_type;
			$request_array[] =  'election_type='.$request->election_type;
		  }
			  
		  $filter_for_phases = [
			'election_type' => $data['election_type']
		  ];
	  
		$data['phases'] = PhaseModel::get_phases($filter_for_phases);

         // $data['phases'] = PhaseModel::get_phases();
          $data['phase'] = NULL;
          if($request->has('phase')){
            if($request->phase != 'all'){
              $data['phase'] = $request->phase;
            }
            $request_array[] =  'phase='.$request->phase;
          }else{
            $data['phase']    = $default_phase;
            $request_array[]  =  'phase='.$default_phase; 
          }
        
          /*if($data['phase']==1){      
            $data['phase']    = 1;
            $data['phases'] =  [];
          }*/

          //set title
          $title_array  = [];
          $data['heading_title'] = 'End of Poll AC Finalised List';

          //GET STATE NAME BY STATE CODE STARTS
          if($data['state']){
            $state_object = StateModel::get_state_by_code($data['state']);
            if($state_object){
              $title_array[]  = "State: ".$state_object['ST_NAME'];
            }
          }
          //GET STATE NAME BY STATE CODE ENDS

         
          //SETTING STATE CODE IF USER IS CEO
          if(Auth::user()->role_id == '4'){
            $data['state']  = Auth::user()->st_code;
          }
          //SETTING STATE CODE IF USER IS ECI
          if($request->has('state')){
            $data['state'] = $request->state;
            $state_object = StateModel::get_state_by_code($data['state']);
            if($state_object){
              $title_array[]  = "State: ".$state_object['ST_NAME'];
            }
            
          }
          // if($data['phase']){
          //   $title_array[] = "Phase: ".$data['phase'];
          // }

          //LISTING ALL STATES FOR DATABASE RESULTS
          $states = StateModel::get_states();
          $data['states'] = [];



          foreach($states as $result){

             //FOR CEO 
            if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
              $data['states'][] = [
                'st_code' => $result->ST_CODE,
                'name' => $result->ST_NAME,
              ];
            }

              //FOR ECI
            if(Auth::user()->role_id == '7' || Auth::user()->role_id == '26'){
              $data['states'][] = [
                'st_code' => $result->ST_CODE,
                'name'    => $result->ST_NAME,
              ];
            }

        }
        //GET STATE NAME BY STATE CODE ENDS

        $data['filter']   = implode('&', array_merge($request_array));
        
        $data['filter_buttons'] = $title_array;

        //buttons
        $data['buttons']    = [];
       
        
        $data['action']         = url($this->action_state);

        $results                = [];


        if(Auth::user()->role_id == '4'){

           $filter_election = [
            'st_code'         => Auth::user()->st_code,
            'election_type'           => $data['election_type'],
            'phase'           => $data['phase'],
            'order_by'        => 'ac_no',
            'group_by'        => 'ac_no',
          ];
        }

        if(Auth::user()->role_id == '7' || Auth::user()->role_id == '26'){
           
           if($data['state']){
              $filter_election = [
                  'election_type'         => $data['election_type'],
                  'phase'         => $data['phase'],
                  'st_code'       => $data['state'],
                  'group_by'      => 'ac_no',
                  'order_by'      => 'ac_no',
                ];
           }else{
                $filter_election = [
                'election_type'         => $data['election_type'],
                'phase'         => $data['phase'],
                'st_code'       => $data['state'],
                'group_by'      => 'ac_no',
                'order_by'      => 'state',
              ];
           }
           
        }


          $object_states = EndOfPollFinaliseModel::get_eop_finalise_list($filter_election);
//dd($object_states);
          foreach ($object_states as $result) {

				//dd($result);

                  $results[] = [
                    'label'                => $result->state_name,
                    "const_no"             => $result->const_no,
                    "const"                => $result->const,
                    "finalized_const"      => $result->finalized_const,
                    "action"      		   => url($this->action_state.'/finalized/'.$result->st_code.'/'.$result->const_no),
                  ];   

              }
          
        $data['results']    =   $results;
        $data['heading_title_with_all'] = $data['heading_title'];

         return view($this->view_path.'.end_of_poll.EciEndOfPollDeFinalisedList', $data);

/*
        return view('admin.pc.eci.EciEndOfPollFinalised',['user_data' => $user_data,'EciEndOfPollFinalised' => $EciEndOfPollFinalised]);*/
                        
      //  }
      //  else {
       //     return redirect('/admin-login');
       // } 
        
     
    /*try{}catch (Exception $ex) {
        return Redirect('/internalerror')->with('error', 'Internal Server Error');

    }*/
        //ECI END OF POLL FINALSED LIST REPORT TRY CATCH BLOCK ENDS
        
}
    //ECI END OF POLL FINALSED LIST REPORT FUNCTION ENDS


}  // end class