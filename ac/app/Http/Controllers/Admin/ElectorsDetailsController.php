<?php
namespace App\Http\Controllers\Admin;
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
use App\adminmodel\ACROModel;
use Illuminate\Support\Facades\Crypt; 
use Maatwebsite\Excel\Excel;

  //INCLUDING CLASSES
use App\Classes\xssClean;
use App\Classes\secureCode;

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;

date_default_timezone_set('Asia/Kolkata');
    

class ElectorsDetailsController extends Controller
{   

    

    //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){ 
        $this->middleware('adminsession');	
        $this->middleware(['auth:admin','auth']);
        $this->middleware('clean_url');
        $this->middleware('ro');
        $this->commonModel = new commonModel();
		$this->romodel = new ACROModel();
        $this->ECIModel = new ECIModel();
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
    */

    protected function guard(){
        return Auth::guard();
    }

 

    //ECI ELECTORS DEATILS  STARTS
    public function ElectorsDetails(Request $request){	
      //ECI ELECTORS DEATILS  TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);
			  
			  $ele_details=$this->commonModel->election_detailsac($user_data->st_code,$user_data->ac_no,$user_data->dist_no,$user_data->id,'AC');

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
              //dd($ele_details);
              $ElectorsWhere=['st_code'=>$user_data->st_code,'ac_no'=>$user_data->ac_no,'year'=>2019];

              $ElectorsDetails = DB::table('electors_cdac')->where($ElectorsWhere)->get();

           //dd($ElectorsDetails);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($AllPartyList);
              
            return view('admin.ac.ro.voting.ElectorsDetails',['user_data' => $user_data,'ElectorsDetails' => $ElectorsDetails,'ScheduleID' => @$ele_details->ScheduleID,'ele_details'=>$ele_details]);
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI ELECTORS DEATILS  TRY CATCH BLOCK ENDS
        
    }
    //ECI ELECTORS DEATILS  FUNCTION ENDS


    //ECI ELECTORS DEATILS UPATE STARTS
    public function ElectorsDetailsUpdate(Request $request){  
      //ECI ELECTORS DEATILS  UPATE TRY CATCH BLOCK STARTS
       try{

          $users=Session::get('admin_login_details');
          $user = Auth::user();   
          if(session()->has('admin_login')){  
              $uid=$user->id;

              $user_data=$this->commonModel->getunewserbyuserid($uid);
			  
			  $ele_details=$this->commonModel->election_detailsac($user_data->st_code,$user_data->ac_no,$user_data->dist_no,$user_data->id,'AC');

              $list_record=$this->ECIModel->getallelectionphasewise();

              $list_state=$this->ECIModel->listcurrentelectionstate();

              $list_phase=$this->ECIModel->listcurrentelectionphase();

              $list_electionid=$this->ECIModel->getallelectionbyid();

              $list=$this->ECIModel->listelectiontype();

              $module=$this->commonModel->getallmodule();

              $cur_time    = Carbon::now();
              
              $ElectorsWhere=['st_code'=>$user_data->st_code,'ac_no'=>$user_data->ac_no,'year'=>2019];

              $ElectorsDetails = DB::table('electors_cdac')->where($ElectorsWhere)->get();

           //dd($ElectorsDetails);

             $cur_time  = Carbon::now();
             $st_code = $user_data->st_code;
             $st_name = $user_data->placename;
              //dd($AllPartyList);


             $validator = Validator::make($request->all(), [ 
                'electors_male'     => 'required|numeric|min:0|integer|between:0,9999999',
                'electors_female'   => 'required|numeric|min:0|integer|between:0,9999999',
                'electors_other'    => 'required|numeric|min:0|integer|between:0,9999999',
                'electors_total'    => 'required|numeric|min:0|integer|between:0,9999999',
                //'service_total'    => 'required|numeric|min:0|integer|between:0,9999999',
            ]);


            if ($validator->fails()) {
               return Redirect::back()
               ->withErrors($validator)
               ->withInput();          
            }

             $xss = new xssClean;

             $request              = $request->all();
             $electors_male        = $xss->clean_input($request['electors_male']);
             $electors_female      = $xss->clean_input($request['electors_female']);
             $electors_other       = $xss->clean_input($request['electors_other']);
             $electors_total       = $xss->clean_input($request['electors_total']);
			 //$service_total       = $xss->clean_input($request['service_total']);

			 //ELECTORS DATA MATCHING STARTS
           if($electors_male + $electors_female + $electors_other != $electors_total){
           
             
            return Redirect('/roac/ElectorsDetails/')->with('error', 'Data Mismatch in Electors Data.');

            }
          //ELECTORS DATA MATCHING ENDS


             $update_fields = array(

                                    'electors_male'      => $electors_male,
                                    'electors_female'    => $electors_female,
                                    'electors_other'     => $electors_other, 
                                    'electors_total'     => $electors_total,
                                    //'electors_service'     => $service_total,
                                );
             
             $ElectorsWhere=['st_code'=>$user_data->st_code,'ac_no'=>$user_data->ac_no,'year'=>2019];
             $Data = DB::table('electors_cdac')->where($ElectorsWhere)->update($update_fields);

             return Redirect('/roac/ElectorsDetails/')->with('success', 'Electrol Data Updated Successfully !');             
                            
            }
            else {
                return redirect('/admin-login');
            } 
            
         
        }catch (Exception $ex) {
            return Redirect('/internalerror')->with('error', 'Internal Server Error');

        }
        //ECI ELECTORS DEATILS  UPATE TRY CATCH BLOCK ENDS
        
    }
    //ECI ELECTORS DEATILS  UPATE FUNCTION ENDS



}  // end class