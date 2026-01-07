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
		use MPDF;
		use App\commonModel;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
    use App\models\Admin\PhaseModel;
     use Illuminate\Support\Facades\Crypt;
     use App\models\Admin\turnout\TurnoutModel;
    
class ECITurnoutController extends Controller
{
    public $base    = 'eci';
    public $folder  = 'turnout';
    public $action    = 'eci/turnout/';
    public $view_path = "admin.turnout.ro";
   
   public function __construct()
        {     
    			$this->middleware('adminsession');
    			$this->middleware(['auth:admin','auth']);
    			$this->middleware('eci');
          $this->commonModel = new commonModel();
          $this->turnout = new TurnoutModel;
    			$this->xssClean = new xssClean;
    			if(!Auth::check()){ 
               return redirect('/officer-login');
              } 
		      }
 
public function index() {
        $data  = []; 
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $allphase=PhaseModel::get_phases();
        foreach ($allphase as $key => $val) {
           $phase[]=$val->PHASE_NO;
           $election[]=$val->ELECTION_ID;
           $schedule[]=$val->SCHEDULENO;
           $polldate[]=$val->DATE_POLL;
        }
        $master = DB::table('pd_schedule_estimated')->where('election_id',$d->election_id)->first();

        $phase=array_unique($phase);
        $election=array_unique($election);
        $schedule=array_unique($schedule);
        $polldate=array_unique($polldate);
        
             $data['user_data']       = $d;
             $data['phase']           = $phase;
             $data['election']        = $election;
             $data['schedule']        = $schedule;
             $data['polldate']        = $polldate;
             $data['master']        = $master;
            // dd($data);
          return view($this->view_path.'.eciestimate-turnout', $data);  
                      
          
        } // end function
	
  public function turnout(Request $request)
      {     
       $data  = []; 
       $user = Auth::user();
        $validator = \Validator::make(
                $request->all(), 
                    [
                      'election_id' => 'required|between:0,99.99',
                      'phase_no' => 'required|between:0,99.99',
                      'sechudle_id' => 'required|between:0,99.99',
                      'poll_date' => 'required',
                    ],
                    [
                      'election_id.required' => 'Please enter election',
                      'election_id.between' => 'Please enter valid value (99.99)',
                      'phase_no.required' => 'Please enter phase',
                      'phase_no.between' => 'Please enter valid value (99.99)',
                      'sechudle_id.required' => 'Please enter Schedule',
                      'sechudle_id.between' => 'Please enter valid value (99.99)',
                      'poll_date.required' => 'Please enter polldate',
                       
                     ]);
        
            if($validator->fails()){
              return \Redirect::back()->withInput($request->all())->withErrors($validator);
              }

            $id =  $request->input('id'); 
            $election_id= $this->xssClean->clean_input($request->input('election_id'));
            $phase_no= $this->xssClean->clean_input($request->input('phase_no'));
            $sechudle_id= $this->xssClean->clean_input($request->input('sechudle_id'));
            $poll_date= $this->xssClean->clean_input($request->input('poll_date'));

                $st = array('election_id'=>$election_id,
                            'phase_no'=>$phase_no,
                            'sechudle_id'=>$sechudle_id,
                            'poll_date'=>$poll_date); 
               // dd($st);
                updatedata('pd_schedule_estimated','id',$id,$st);   
               
                \Session::flash('success_mes', 'Master Estimate successfully Updated');
            
            return Redirect::to('eci/turnout/turnout');
           
        } 
      public function sql() {
        $data  = []; 
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
             
             $data['user_data']       = $d;
            
            // dd($data);
          return view($this->view_path.'.ecisql', $data);  
                      
          
        } // end function 
    public function update_sql(Request $request)
      {     
       $data  = []; 
       $user = Auth::user();
        $validator = \Validator::make(
                $request->all(), 
                    [
                      'usql' => 'required'
                    ],
                    [
                      'usql.required' => 'Please enter sql',
                      ]);
        
            if($validator->fails()){
              return \Redirect::back()->withInput($request->all())->withErrors($validator);
              }

            $usql =  $request->input('usql'); 
             DB::statement($usql);
            \Session::flash('success_mes', ' successfully Updated');
            
            return Redirect::to('eci/turnout/sql');
           
        }   

        public function update_turnout_index(Request $request){
          $user_data = Auth::user();
          $ele_data = DB::table('pd_schedule_exempted')->where('status', '1')->groupBy('st_code', 'ac_no')->get()->toArray();
		  
          $results_data = [];
          if(count($ele_data) > 0){
            foreach($ele_data as $each_data){

              $suvidhaac_turnout_data = $this->turnout->get_turn_out_data([
                'st_code' => $each_data->st_code,
                'ac_no'   => $each_data->ac_no,
              ]);

              $boothaap_data = DB::connection('booth_revamp_test')->table('tbl_analytics_dashboard')
              ->selectRaw('sum(male_electors) as total_male_electors, sum(female_electors) as total_female_electors, sum(other_electors) as total_other_electors,
               sum(male_turnout) as total_male_turnout, sum(female_turnout) as total_female_turnout, sum(other_turnout) as total_other_turnout')
              ->selectRaw('(sum(male_electors) + sum(female_electors) + sum(other_electors)) as total_elector, (sum(male_turnout) + sum(female_turnout) + sum(other_turnout)) as total_turnout')
              ->selectRaw('round((sum(male_turnout) + sum(female_turnout) + sum(other_turnout))*100/(sum(male_electors) + sum(female_electors) + sum(other_electors)),2) as total_turnout_percent')

              ->where([
                'st_code' => $each_data->st_code,
                'ac_no'   => $each_data->ac_no
              ])
              ->get()
              ->toArray();



              $results_data[] = [
                'st_code'                 => $each_data->st_code,
                'st_name'                 => getstatebystatecode($each_data->st_code)->ST_NAME,
                'ac_no'                   => $each_data->ac_no,
                'ac_name'                 => getacbyacno($each_data->st_code, $each_data->ac_no)->AC_NAME,
                'poll_date'               => date('d-m-Y', strtotime($each_data->poll_date)),
                'poll_percent'            => $boothaap_data[0]->total_turnout_percent,
                'total_elector'           => $boothaap_data[0]->total_elector,
                'total_voter'             => $boothaap_data[0]->total_turnout,
                'suvidha_poll_percent'    => $suvidhaac_turnout_data->est_turnout_total,
                'suvidha_total_elector'   => $suvidhaac_turnout_data->electors_total,
                'suvidha_total_voter'     => $suvidhaac_turnout_data->est_voters,
              ];
            }
          }
          if($request->has('final_data')){
            return $results_data;
          }
          return view('admin.turnout.update_turnout.update_trunout', ['user_data'=> $user_data, 'results_data'=>$results_data]);
        }

        public function update_turnout_update(Request $request){
        $ele_details = $this->update_turnout_index($request->merge(['final_data' => 1]));
        $data  = []; 
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        if(count($ele_details)>0){
          foreach($ele_details as $each_data){
            $filter = [
              'st_code'       => $each_data['st_code'],
              'ac_no'         => $each_data['ac_no'],
               ];
  
          $filter = [
              'st_code'       => $each_data['st_code'],
              'ac_no'         => $each_data['ac_no'],
              'const_type'    => 'AC',
              'pc_no'         =>'',
               ];
           $lists1=$this->turnout->get_scheduledetail($filter);
               
          $current_date=date("Y-m-d H:i:s");  
          $p1=date("Y-m-d")." 07:30:00"; $p2=date("Y-m-d")." 07:30:00";
          $p3=date("Y-m-d")." 07:30:00";$p4=date("Y-m-d")." 07:30:00";
          $p5=date("Y-m-d")." 07:30:00";
  
          $estimated_time = DB::table('pd_schedule_estimated')->where('sechudle_id', '5')->first();
            $poll_date=$estimated_time->poll_date;
            // $poll_date='2020-11-02';  
            $p1=$poll_date." ".$estimated_time->poll_st_time1; 
            $p2=$poll_date." ".$estimated_time->poll_st_time2;
            $p3=$poll_date." ".$estimated_time->poll_st_time3;
            $p4=$poll_date." ".$estimated_time->poll_st_time4;
            $p5=$poll_date." ".$estimated_time->poll_st_time5;
  
            $pt1=$poll_date." ".$estimated_time->poll_end_time1; 
            $pt2=$poll_date." ".$estimated_time->poll_end_time2;
            $pt3=$poll_date." ".$estimated_time->poll_end_time3;
            $pt4=$poll_date." ".$estimated_time->poll_end_time4;
            $pt5=$poll_date." ".$estimated_time->poll_end_time5;
            $saverec = 1;
          if($current_date>=$pt1 and $current_date<=$p1){ 
            $saverec = 1;
          }elseif($current_date>=$pt2 and $current_date<=$p2){
            $saverec = 2;
          }elseif($current_date>=$pt3 and $current_date<=$p3){
            $saverec = 3;
          }elseif($current_date>=$pt4 and $current_date<=$p4){
            $saverec = 4;
          }elseif($current_date>=$pt5 and $current_date<=$p5){
            $saverec = 5;
          }elseif($current_date>=$pt4){
            $saverec = 6;
          }
              
        if($saverec == 1 ){
                  $est_turnout_round1 = $each_data['poll_percent'];
                  $est_turnout=$est_turnout_round1;
                  $st = array('updated_at'=>date("Y-m-d H:i:s"),
                              'added_update_at'=>date("Y-m-d"),
                              'updated_by'=>$d->officername,
                              'est_turnout_round1'=>$est_turnout_round1,
                              'update_device_round1'=>'web',
                              'update_at_round1'=>date("Y-m-d H:i:s"),
                              'est_turnout_total'=>$est_turnout_round1); 
                  
                 // updatedata('pd_scheduledetail','id',$id,$st);   
                 DB::table('pd_scheduledetail')->where('st_code',$each_data['st_code'])->where('ac_no',$each_data['ac_no'])->where('election_id','10')->update($st);
                  \Session::flash('success_mes', 'Voter Turnout successfully added');
                   
            }
  
            elseif($saverec ==2){
            
              $est_turnout_round2 = $each_data['poll_percent'];
                   $est_turnout=$est_turnout_round2;
                    $st = array('updated_at'=>date("Y-m-d H:i:s"),
                                'added_update_at'=>date("Y-m-d"),
                                'updated_by'=>$d->officername,
                                'est_turnout_round2'=>$est_turnout_round2,
                                'update_device_round2'=>'web',
                                'update_at_round2'=>date("Y-m-d H:i:s"),
                                'est_turnout_total'=>$est_turnout_round2); 
                   //print_r($st); echo "hello";
                 // updatedata('pd_scheduledetail','id',$id,$st); 
                    DB::table('pd_scheduledetail')->where('st_code',$each_data['st_code'])->where('ac_no',$each_data['ac_no'])->where('election_id','10')->update($st);
                  \Session::flash('success_mes', 'Voter Turnout successfully added');
                   
            }
           elseif($saverec ==3){
            $est_turnout_round3 = $each_data['poll_percent'];
                 $est_turnout=$est_turnout_round3;
                  $st = array('updated_at'=>date("Y-m-d H:i:s"),
                              'added_update_at'=>date("Y-m-d"),
                              'updated_by'=>$d->officername,
                              'est_turnout_round3'=>$est_turnout_round3,
                              'update_device_round3'=>'web',
                              'update_at_round3'=>date("Y-m-d H:i:s"),
                              'est_turnout_total'=>$est_turnout_round3); 
                   
                  //  updatedata('pd_scheduledetail','id',$id,$st);
                   DB::table('pd_scheduledetail')->where('st_code',$each_data['st_code'])->where('ac_no',$each_data['ac_no'])->where('election_id','10')->update($st); 
                  \Session::flash('success_mes', 'Voter Turnout successfully added');
                  
            }
           elseif($saverec ==4){
            $est_turnout_round4 = $each_data['poll_percent'];
                 $est_turnout=$est_turnout_round4;
                  $st = array('updated_at'=>date("Y-m-d H:i:s"),
                              'added_update_at'=>date("Y-m-d"),
                              'updated_by'=>$d->officername,
                              'est_turnout_round4'=>$est_turnout_round4,
                              'update_device_round4'=>'web',
                              'update_at_round4'=>date("Y-m-d H:i:s"),
                              'est_turnout_total'=>$est_turnout_round4); 
                  
                  // updatedata('pd_scheduledetail','id',$id,$st); 
                  DB::table('pd_scheduledetail')->where('st_code',$each_data['st_code'])->where('ac_no',$each_data['ac_no'])->where('election_id','10')->update($st);
                  \Session::flash('success_mes', 'Voter Turnout successfully added');
                  
            }
            elseif($saverec ==5){
              $est_turnout_round5 = $each_data['poll_percent'];
                 $est_turnout=$est_turnout_round5;
                  $st = array('updated_at'=>date("Y-m-d H:i:s"),
                              'added_update_at'=>date("Y-m-d"),
                              'updated_by'=>$d->officername,
                              'est_turnout_round5'=>$est_turnout_round5,
                              'update_device_round5'=>'web',
                              'update_at_round5'=>date("Y-m-d H:i:s"),
                              'est_turnout_total'=>$est_turnout_round5); 
                   
                  //updatedata('pd_scheduledetail','id',$id,$st); 
                  DB::table('pd_scheduledetail')->where('st_code',$each_data['st_code'])->where('ac_no',$each_data['ac_no'])->where('election_id','10')->update($st);
                  \Session::flash('success_mes', 'Voter Turnout successfully added');
                 
            }
            elseif($saverec ==6){ 
              $est_turnout_end = $each_data['poll_percent'];
                 $est_turnout=$est_turnout_end;
                  $st = array('updated_at'=>date("Y-m-d H:i:s"),
                              'added_update_at'=>date("Y-m-d"),
                              'updated_by'=>$d->officername,
                              'close_of_poll'=>$est_turnout_end,
                              'updated_device_close_of_poll'=>'web',
                              'updated_at_close_of_poll'=>date("Y-m-d H:i:s"),
                              'est_turnout_total'=>$est_turnout_end,
                              'est_poll_close'=>1); 
                 
                  //updatedata('pd_scheduledetail','id',$id,$st); 
                  DB::table('pd_scheduledetail')->where('st_code',$each_data['st_code'])->where('ac_no',$each_data['ac_no'])->where('election_id','10')->update($st);
                  
            } 
             $lists = $this->turnout->get_scheduledetail($filter);
             $ele=$this->turnout->getcdacelectorsdetails($filter); 
              $electors_total=0; $est_voter=0;
              if(isset($ele)){
                      $electors_total=$each_data['total_elector'];
                    }
                  if(isset($lists) and $lists->est_turnout_round1!=0)
                    $latest_updated=$lists->est_turnout_round1;
                   if(isset($lists) and $lists->est_turnout_round2!=0)
                    $latest_updated=$lists->est_turnout_round2;
                  if(isset($lists) and $lists->est_turnout_round3!=0)
                    $latest_updated=$lists->est_turnout_round3;
                   if(isset($lists) and $lists->est_turnout_round4!=0)
                    $latest_updated=$lists->est_turnout_round4;
                   if(isset($lists) and $lists->est_turnout_round5!=0)
                    $latest_updated=$lists->est_turnout_round5;
                  if(isset($lists) and $lists->close_of_poll!=0)
                    $latest_updated=$lists->close_of_poll;
                  
                // $est_voter=round(($electors_total*$latest_updated/100),0);
                $est_voter=$each_data['total_voter'];
                $st1 = array('updated_at'=>date("Y-m-d H:i:s"),
                              'added_update_at'=>date("Y-m-d"),
                              'updated_by'=>$d->officername,
                              // 'est_turnout_total'=>$latest_updated,
                              'est_turnout_total'=>$each_data['poll_percent'],
                              'electors_total'=>$electors_total,
                              'est_voters'=>$est_voter);  
              // updatedata('pd_scheduledetail','id',$id,$st1); 
              DB::table('pd_scheduledetail')->where('st_code',$each_data['st_code'])->where('ac_no',$each_data['ac_no'])->where('election_id','10')->update($st1); 
          }
        }
          \Session::flash('success_mes', 'Voter Turnout successfully added');
          return Redirect::to('eci/turnout/update_turnout');
        }
}  // end class  //accepted_candidate  
