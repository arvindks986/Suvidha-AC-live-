<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use \PDF;


//POLL TURNOUT MODELS
use App\commonModel;
use App\models\Admin\EndOfPollModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;




use App\Http\Controllers\Admin\Eci\Report\PolldayEndOfPollController;
 

//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Classes\secureCode;

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;

  date_default_timezone_set('Asia/Kolkata');

class AcCeoEndOfPollController extends Controller
{     

    public $base          = 'ceo';
    public $folder        = 'ceo';
    public $action_state  = 'ceo/report/voting/end-of-poll';
    public $action_ac     = 'ceo/report/voting/end-of-poll/state/ac';
    public $view_path     = "admin.ac.ceo";

     //USING TRAIT FOR COMMON FUNCTIONS
    use CommonTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
			$this->middleware('clean_request');
			$this->commonModel    = new commonModel();
			$this->EndOfPollModel = new PolldayEndOfPollController;
			
			if(!Auth::user()){
			  return redirect('/officer-login');
			}
	}
/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    protected function guard(){
        return Auth::guard();
    }


     //AC CEO END OF POLL REPORT FUNCTION STARTS
    public function AcCeoEndOfPoll(Request $request){ 
      //AC CEO END OF POLL  REPORT TRY CATCH STARTS HERE
      

        $users=Session::get('admin_login_details');
        $user = Auth::user(); 
        $uid=$user->id;  
         
        $user_data=$this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;
      
              
        $request->merge([
          'is_excel' => 1,

        ]);
        

        $data = $this->EndOfPollModel->report_ac($request);
        //dd($data);
/*
        $data['consituencies']     = AcModel::get_records([
            'state' => $user_data->st_code,
            'pc_no' => $user_data->pc_no
          ]);
  */

        //buttons
        $data['buttons']    = [];
        $data['buttons'][]  = [
          'name' => 'Export Excel',
          'href' =>  url('acceo/AcCeoEndOfPollExcel').'?'.$data['filter'],
          'target' => false
        ];
        $data['buttons'][]  = [
          'name' => 'Export Pdf',
          'href' =>  url('acceo/AcCeoEndOfPollPdf').'?'.$data['filter'],
          'target' => false
        ];

         $results = [];
        foreach ($data['results'] as $key => $result) {

          $individual_filter    = implode('&', [
            'ac_no' => 'ac_no='.$result['ac_no'],
            'state' => 'state='.base64_encode($result['st_code']),
            'phase' => 'phase='. $data['phase']
          ]);
          

          $results[] = [
            'label'               => $result['label'],
            'filter'              => $individual_filter,
            "ac_no"               => $result['ac_no'],
            "ac_name"             => $result['ac_name'],
            "st_code"             => $result['st_code'],
            "old_total_male"      => $result['old_total_male'],
            "old_total_female"    => $result['old_total_female'],
            "old_total_other"     => $result['old_total_other'],
            "old_total"           => $result['old_total'],
            "total_male"          => $result['total_male'],
            "total_female"        => $result['total_female'],
            "total_other"         => $result['total_other'],
            "total"               => $result['total'],
            "total_percentage"    => $result['total_percentage'],
			      "href"                => 'javascript:void(0)',
            //"href"                => url('acceo/AcCeoEndOfPollAc')."?".$individual_filter
			
          ];    
        
        }


         $data['results'] = $results;

        $data['action']         = url('acceo/AcCeoEndOfPoll');     

        if(session()->has('admin_login')){ 

          $xss = new xssClean;

          //dd($PcCeoEstimatePollTurnoutPc);       
          return view('admin.ac.ceo.pollday.AcCeoEndOfPollAc',$data);
             
        }else {
              return redirect('/admin-login');
          }             

       try{ } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC CEO END OF POLL REPORT TRY CATCH ENDS HERE
    }
    //AC CEO END OF POLL REPORT FUNCTION ENDS


    //AC CEO END OF POLL Excel REPORT  FUNCTION STARTS
    public function AcCeoEndOfPollExcel(Request $request){ 
    
      //AC CEO END OF POLL Excel REPORT TRY CATCH STARTS HERE
      try{

        
        if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $users=Session::get('admin_login_details');
            $user = Auth::user(); 
            $uid=$user->id;  
             
            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
        
            $this->EndOfPollModel->export_excel_report_ac($request);
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC CEO  END OF POLL Excel REPORT TRY CATCH ENDS HERE
    }
    //AC CEO  END OF POLL Excel REPORT FUNCTION ENDS


     //AC CEO END OF POLL  PDF REPORT  FUNCTION STARTS
    public function AcCeoEndOfPollPdf(Request $request){ 
      //AC CEO END OF POLL  PDF REPORT TRY CATCH STARTS HERE
      try{

        
        if(session()->has('admin_login')){ 

            $xss = new xssClean;

            $users=Session::get('admin_login_details');
            $user = Auth::user(); 
            $uid=$user->id;  
             
            $user_data=$this->commonModel->getunewserbyuserid($uid);

            $cur_time    = Carbon::now();
            $st_code     = $user_data->st_code;
            $st_name     = $user_data->placename;
          
        
            $this->EndOfPollModel->export_pdf_report_ac($request);
               
            }
            else {
                return redirect('/admin-login');
            }             

        } catch (Exception $ex) {
                   
                   return Redirect('/internalerror')->with('error', 'Internal Server Error');
                  
           }
        //AC CEO END OF POLL  PDF REPORT TRY CATCH ENDS HERE
    }
    //AC CEO END OF POLL  PDF REPORT FUNCTION ENDS

   
 
}  // end class