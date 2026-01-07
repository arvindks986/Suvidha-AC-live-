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
    use Illuminate\Support\Facades\Mail;
    use Validator;
    use Config;
    use \PDF;
    use MPDF;
    use App\commonModel;  
    use App\adminmodel\ECIModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\CandidateModel;
    use App\adminmodel\PartyMaster;
    use App\adminmodel\CandidateNomination;
    use App\adminmodel\ACCountingModel; 
	use App\models\Admin\ElectionScheduleModel;
    use App\Classes\xssClean;
	use App\Mail\UserCredentialDetailsMail;
use App\Http\Controllers\Admin\BoothApp\PollingController;
use App\models\Admin\CandidatecriminalModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
  ini_set("memory_limit","1500M");
    set_time_limit('6000');
    ini_set("pcre.backtrack_limit", "10000000");
   
class EciController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){   
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('eci');
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
        $this->xssClean = new xssClean;
         $this->CountingModel = new ACCountingModel();

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
    */

    protected function guard(){
        return Auth::guard();
    }

   public function dashboard(Request $request){ 
        if(Auth::check()){
        $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);  
            
            $list_record=$this->ECIModel->getallelectionphasewise();
            $list_state=$this->ECIModel->listcurrentelectionstate();			
            $list_phase=$this->ECIModel->listcurrentelectionphase();
            $list_electionid=$this->ECIModel->getallelectionbyid();
            $list=$this->ECIModel->listelectiontype();

            $polling_station = new PollingController();
			
			if(\Session::has('DB_id') && \Session::get('DB_id') == '5'){
		 
				$booth_dashboard = $polling_station->dashboard($request);
			
			}else{
				$booth_dashboard = '';
			}

      $schdule = $this->ECIModel->getschedule();
      //dd($schdule);
      $state_name = '';
      $total_dist = 0;
      $total_ac = 0;
      $total_polling_station = 0;
      $total_electors = 0;

      $list_details = array();
      $dataArr = array();
	  
	  
	  $list = DB::table('m_election_details')->select('ST_CODE')->where('ELECTION_TYPEID','3')
                      ->groupBy('m_election_details.ST_CODE')->orderBy('ST_CODE','ASC')->get();
        
	  
	  
	  
	  
      if(isset($d->election_id)){
	  
	  foreach($list as $key=>$raw){
        $list_details = $this->ECIModel->getstatebyelectionid($d->election_id);
		
		//dd($list_details);
		
        if(isset($list_details) &&  $list_details <>''){
          $st_code = $list_details->ST_CODE;
          $state_record = $this->commonModel->getstatebystatecode($raw->ST_CODE);
           if(isset($state_record)  && $state_record <>''){
             $state_name = $state_record->ST_NAME;
           }
          $dataArr[$key]['state_name'] = $state_name;
          $dataArr[$key]['total_dist'] = $this->ECIModel->gettotaldistbystate($raw->ST_CODE);
          $dataArr[$key]['total_ac'] = $this->ECIModel->gettotalacbystate($raw->ST_CODE);
          $dataArr[$key]['total_polling_station'] = $this->ECIModel->gettotalpsbystate($raw->ST_CODE);
          $dataArr[$key]['total_electors'] = $this->ECIModel->gettotalelectorsbystate($raw->ST_CODE,$d->election_id);
        }
		
		}
		
      }
	  
	  
	//  dd($dataArr);
	  
	  $filter_election = [
            'group_by'  => 'state',
            'order_by'  => 'state'
          ];
	  
	  $object   = ElectionScheduleModel::state_schedule($filter_election);
	  $results =[];
	       foreach ($object as $result) {

           
                

            //checking dates for election events
            
            //START NOMINATION DATE DIFF
            $start_nomi_class   = ElectionScheduleModel::date_diff($result['start_nomi_date']);

            //LAST NOMINATION DATE DIFF
            $last_nomi_class   = ElectionScheduleModel::date_diff($result['last_nomi_date']);

            //SCRUTINY DATE DIFF
            $scr_date_class   = ElectionScheduleModel::date_diff($result['dt_nomi_scr']);
            
            //LAST WIDRAWL DATE DIFF
            $wid_date_class   = ElectionScheduleModel::date_diff($result['last_wid_date']);

            //POLL DATE DIFF
            $poll_date_class   = ElectionScheduleModel::date_diff($result['poll_date']);

            //COUNT DATE DIFF
            $count_date_class   = ElectionScheduleModel::date_diff($result['count_date']);

            //COMPLETE DATE DIFF
            $comp_date_class   = ElectionScheduleModel::date_diff($result['complete_date']);
                

                $results[] = [
                  'label'                    => $result['state'],
                  'st_code'                  => $result['st_code'],
                  'sid'                      => $result['sid'],
                  'acs'                      => $result['acs'],
                  'start_nomi_class'         => $start_nomi_class,
                  'start_nomi_date'          => $result['start_nomi_date'],
                  'last_nomi_class'          => $last_nomi_class,
                  'last_nomi_date'           => $result['last_nomi_date'],
                  'nomi_scr_class'           => $scr_date_class,
                  'dt_nomi_scr'              => $result['dt_nomi_scr'],
                  'last_wid_class'           => $wid_date_class,
                  'last_wid_date'            => $result['last_wid_date'],
                  'poll_date_class'          => $poll_date_class,
                  'poll_date'                => $result['poll_date'],
                  'count_date_class'         => $count_date_class,
                  'count_date'               => $result['count_date'],
                  'complete_date_class'      => $comp_date_class,
                  'complete_date'            => $result['complete_date']
                ];      

            } 
	  
     // dd($results);
            return view('admin.ac.eci.dashboard', ['user_data' => $d,'list_record' => $list_record,'list_state'=>$list_state,'list_phase'=>$list_phase,'list_electionid'=>$list_electionid,'list'=>$list, 'booth_dashboard' => $booth_dashboard,'schdule'=>$schdule,'state_name'=>$state_name,'total_ac'=>$total_ac,'total_dist'=>$total_dist,'total_ps'=>$total_polling_station,'total_electors'=>$total_electors,'st_arr'=>$dataArr,'results'=>$results]);
             
          }
          else {
               return redirect('/officer-login');
              }
           
  
        }   // end dashboard function
		
	 
  public function insertdpartydetails()
            {
              $ch = curl_init();
        // $headers = array(
        //     'Accept: application/json',
        //     'Content-Type: application/json',

        // );
        curl_setopt($ch, CURLOPT_URL, "http://164.100.128.74/Mparty/api/DPartyData/GetAllPartylist");
       // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         $authToken = curl_exec($ch);
         $json = json_decode($authToken);
         foreach($json  as $j) {    //print_r($j);   
              $dat=date("Y-m-d");
              $acdata = array('CCODE'=>$j->ccode,'PARTYABBRE'=>$j->PARTYABBRE,'PARTYSYM'=>$j->PARTYSYM,
                              'ST_CODE'=>$j->ST_CODE,'added_update_at'=>date('Y-m-d'));
               print_r($acdata); 
            $check3= DB::table('d_party')->where('st_code',$j->ST_CODE)->where('CCODE',$j->ccode)->first();
            if(!isset($check3)){           
                 $this->commonModel->insertData('d_party',$acdata); 
               }    
                 
                 
             }
            }
     public function sendnominationmessage()
            {
             $nom_details =DB::table('candidate_personal_detail')->where('cand_mobile','<>','')->get();
             foreach($nom_details as $nom)
                        { set_time_limit(0);

                          if($nom->cand_mobile!='') {
                            $mob_message="Now you can check your nomination/ permission status through suvidha candidate android app. Download from here https://goo.gl/YGoMmM and login using this mobile number.";
                            echo count($mob_message)."<br>";
                            $response = SmsgatewayHelper::gupshup($nom->cand_mobile,$mob_message);
                            //echo $nom->candidate_id."=".$mob_message;
                          }   
                        }
            }
    public function updatesymbole()
          {
           $nomdetails = DB::table('candidate_nomination_detail')->where('cand_party_type','=','S')->get();
            //```
          foreach( $nomdetails as $nom){
                      $partyDetails = DB::table('m_party')
                          ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
                          ->where('m_party.PARTYTYPE','=','S')
                          ->where('d_party.ST_CODE','=',$nom->st_code)
                          ->where('m_party.CCODE','=',$nom->party_id)
                          ->select('m_party.*')->first();
                          if(isset($partyDetails)){
                                $partytype = $partyDetails->PARTYTYPE;
                               }
                               else{
                                   $partytype ='U';
                               }
                    $can = array('cand_party_type'=> $partytype);
                    $n = DB::table('candidate_nomination_detail')->where('nom_id', $nom->nom_id)->update($can);
                }
          }
      function generate_counting_data()
        {
        $list_state=getallstate();
         
        foreach($list_state as $st)
          {  set_time_limit(0);
                     
                    $listallac=getacbystate($st->ST_CODE);
                    
        foreach($listallac as $ac)
            { 
               set_time_limit(0);
                 $new_table=strtolower("counting_master_".$st->ST_CODE);
                  $date = Carbon::now();
                  $currentTime = $date->format('Y-m-d H:i:s');
                  $currentdate = $date->format('Y-m-d');
                  $ele_details=getelectiondetailbystcode($st->ST_CODE,$ac->AC_NO,"AC");
          if(isset( $ele_details)){ 
                  $record=$this->CountingModel->getallacbypcno($st->ST_CODE,$ac->AC_NO);
                  $cand_data=$this->CountingModel->cantestesting_nomination($st->ST_CODE,$ac->AC_NO,$ele_details->ELECTION_ID);
           
       DB::beginTransaction();
            try{ 
         foreach($cand_data as $list){
          $check = DB::table($new_table)->where('nom_id',$list->nom_id)->where('ac_no',$list->ac_no)->where('election_id',$list->election_id)->first();
        if(!isset($check))
          {
          $can=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
          $p=getpartybyid($list->party_id);
          //dd($p);
                  $ca_data = array('nom_id'=>$list->nom_id,'candidate_id'=>$list->candidate_id,
                    'ac_no'=>$list->ac_no,'election_id'=>$list->election_id,'created_at'=>$currentTime,
                    'created_by'=>'ECI','added_create_at'=>$currentdate,
                    'candidate_name'=>$can->cand_name,'party_id'=>$list->party_id,'party_abbre'=>$p->PARTYABBRE,
                    'party_name'=>$p->PARTYNAME,'candidate_hname'=>$can->cand_hname,
                    'party_habbre'=>$p->PARTYHABBR,'party_hname'=>$p->PARTYHNAME); 
                    $this->commonModel->insertData($new_table, $ca_data);
                   }
         
       }
            $lis_st=$this->commonModel->getstatebystatecode($ele_details->ST_CODE);
            $lis_ac=$this->commonModel->getacbyacno($ele_details->ST_CODE,$ele_details->CONST_NO);

            $check_d=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('ac_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();   
      if(!isset($check_d)){   
             $winn_data=array('election_id'=>$ele_details->ELECTION_ID,'constituency_type'=>$ele_details->CONST_TYPE,
                'st_code'=>$ele_details->ST_CODE,'st_name'=>$lis_st->ST_NAME,'st_hname'=>$lis_st->ST_NAME_HI,
                'ac_no'=>$ele_details->CONST_NO,'ac_name'=>$lis_ac->AC_NAME,'ac_hname'=>$lis_ac->AC_NAME_HI,
                'created_at'=>$currentTime,'added_create_at'=>$currentdate);
              $this->commonModel->insertData('winning_leading_candidate', $winn_data);
            } 
            DB::commit();  
             }
            catch(\Exception $e){
                DB::rollback();
        
                \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
                return Redirect::back();
            }

          }
         
          }
        }
      
        
        } 
        public function send_link()
         {
           $userinfo = DB::table('officer_login')
          ->where('password', '=', '')
           ->where('email','<>','')
           ->where('Phone_no','<>','')
           ->get();
        foreach ($userinfo as   $val) {
        // if($val->email!=''){
              $date = Carbon::now();
               $currentTime = $date->format('Y-m-d H:i:s');
               $code = Hash::make(str_random(10));
               $mobile_otp =rand(100000,999999);
             $record = array(
               'name'=>$val->name,
               //'password'=>'',
               'Phone_no'=>$val->Phone_no,
               'email'=>$val->email,
               'mobile_otp' => $mobile_otp,
               'otp_time' => $currentTime,
               'auth_token' => $code,
            );

             //print_r($record);
             $n = DB::table('officer_login')->where('id', $val->id)->update($record);
               $encodeid=encrypt_string($val->id);
               $passcreaturl = url("/updateprofile/".$encodeid);
           $html = "Dear ".$val->name.",\n\n";
                                 $html .= "Your account has been updated in Suvidha Portal"
                                     . "Your account must be activated before you use it. For activating your account and updating your particular, please click on the following link. Alternatively, you could copy and paste the link in your browser.\n\n";
                                 $html .= $passcreaturl." \n\n";
                                 $html .= "OTP: ".$mobile_otp." \n\n";
                                 $html .= "Login ID: ".$val->officername." \n\n";
                                 $html .= "For verifying  your account,  kindly enter OTP ".$mobile_otp." and this OTP has also sent on your registered mobile no.: \n\n";
                                 $html .= "Thanks & Regards,\n\n";
                                 $html .= "Suvidha Team,\n\n";
                    $html1 = strip_tags($html);
                            
                                 mail ($val->email, 'UserLogin Credential',$html1,'suvidha.eci.gov.in');
         if($val->Phone_no!=""){
           $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." and Login ID: ".$val->officername." for SUVIDHA Portal.Activation link has been sent on your email. ".$passcreaturl." Please enter that link and enter OTP to proceed. Do not share this OTP Team ECI";
             $response = SmsgatewayHelper::gupshup($val->Phone_no,$mob_message);
           }
         //}
         }
   } // end function


   public function officerList(Request $request)
  {
    if(Auth::check())
    {
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);

      $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);

      $officerlist =DB::table('officer_login')->where('role_id', 4)->get();
      return view('admin.ac.eci.officer-details',['user_data' => $d,'ele_details' => $ele_details,'officerlist' => $officerlist]);
    }
    else 
    {
      return redirect('/officer-login');
    }   
  }




public function officerProfileUpdate(Request $request,$id='') {
//  dd($request->all());
if(Auth::check()){
$user = Auth::user();
$d=$this->commonModel->getunewserbyuserid($user->id);
$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
if (!empty($_POST['profileUpdate'])) {
// dd($request);
$validator = $this->validate(
$request,
  [
    'name' => 'required',
    'email' => 'required',
    'Phone_no' => 'required|string|min:10|numeric|digits:10',
   // 'zip' => 'required|min:6|numeric|digits:6',
   ],
  [
   'name.required' => 'Please enter your name',
   'email.required' => 'Please enter your email',
   'Phone_no.required' => 'Please enter mobile number',
   'Phone_no.digits' => 'Please enter 10 digit mobile number',
   'Phone_no.unique' => 'This mobile number already exist',
   ]);
// if ($validator->passes()) {
if ($validator) {
  if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['Phone_no'])) {
      $name =  strip_tags($_POST['name']);
      $email =  strip_tags($_POST['email']);
      $mobile = strip_tags($_POST['Phone_no']);
	  $id = $_POST['profileUpdate'];
	 
				$date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s');
                $code = Hash::make(str_random(10));
                $mobile_otp =rand(100000,999999);
                $rec=getById('officer_login','id',$id);
	 
	 
	 
     // $Phone_no = $this->xssClean($_POST['profileUpdate']);
      $officerdata = array(
        'name' => $name,
         'email' => $email,
         'Phone_no' => $mobile,
         'ro_address_l1' => $request->address1,
         'ro_address_l2' => $request->address2,
         'ro_address_pin_code' => $request->zip,
         'added_update_at' => date('Y-m-d'),
         'updated_at' => date('Y-m-d H:i:s'),
		 /* 'mobile_otp' => $mobile_otp,
         'otp_time' => $currentTime,
         'auth_token' => $code,
		 'is_active' => 0 */
        );
		
		
      $where = array('id' => $id);
      $result = DB::table('officer_login')->where($where)->update($officerdata);
	 
	  /* $encodeid=base64_encode($id);
 
              $passcreaturl = url("/updateprofile/".$encodeid);;
			 
			 
              $html = "Dear $name,\n\n";
                                  $html .= "Your account has been updated in Encore Portal. Your account must be activated before you use it. For activating your account and updating your particular, please click on the following link. Alternatively, you could copy and paste the link in your browser.\n\n";
                                  $html .= "$passcreaturl\n\n";
                                  $html .= "OTP: $mobile_otp\n\n";
                                  $html .= "Login ID:  $rec->officername\n\n";
                                  $html .= "For verifying  your account,  kindly enter OTP $mobile_otp and this OTP has also sent on your registered mobile no.:\n\n";
                                  $html .= "Thanks & Regards,\n\n";
                                  $html .= "Encore Team,\n\n";
                                $html = strip_tags($html);

			try{
								  $mailinfo = new \stdClass();
								  $mailinfo->from = 'no-reply@eci.gov.in';
								  $mailinfo->subject = 'User Login Credential';
								  $mailinfo->team = 'Encore | Election Commission Of India';
								  $mailinfo->name = $name;
								  $mailinfo->mobile_otp = $mobile_otp;
								  $mailinfo->passcreaturl = $passcreaturl;
								  $mailinfo->officername = $rec->officername;
								  Mail::to($email)->send(new UserCredentialDetailsMail($mailinfo));
								 
							  }catch(\Exception $ex){
								dd($ex);
							  }
			
			
          if($mobile!=""){
			$mob_message = $mobile_otp." is OTP to login for Encore Portal. Your username is ".$rec->officername.". Activation link has been sent to your email. ECI";
			
              $response = SmsgatewayHelper::gupshup($mobile,$mob_message);

            } */
	 
	 
	 
	 
	 
	 
	 
	 
      \Session::flash('success_success', 'You have Successfully Updated!. ');
     // return redirect()->back();
     return redirect('/eci/ceo-profile-details/');
        }
}
else
{
  \Session::flash('success_error', 'You have some Error!. ');
  return redirect('/eci/ceo-profile-details/');
//  return redirect()->back()->withErrors($validator, 'error');
}
} else {
$decryptedid = decrypt($id);
$rec=getById('officer_login','id',$decryptedid);
return view('admin.ac.eci.officer-profile')->with(array('user_data' => $d,'getofficerdetails' => $rec,'ele_details'=>$ele_details));
}
} else {
return redirect('/officer-login');
}
}



/*
public function get_ca_cand_list(Request $request)
  {
    if(Auth::check())
    {
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id); 
        $list_details = array();
        $dataArr = array();

        $phase_list = DB::table('m_election_details')
        ->groupBy('m_election_details.StatePHASE_NO')
        ->get(); 


        $list = DB::table('m_election_details')->select('ST_CODE')
        ->whereIn('ELECTION_TYPEID', [3, 4])
        ->groupBy('m_election_details.ST_CODE')
        ->orderBy('ST_CODE','ASC')
        ->get();
        $st_list = array();
        foreach ($list as $key) 
        {
          array_push($st_list, $key->ST_CODE);
        }

        $res = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->leftjoin('m_st_schedule', 'm_st_schedule.ST_SCHEDULE', '=', 'candidate_nomination_detail.state_phase_no')
        ->leftjoin('m_election_details', 'm_election_details.StatePHASE_NO', '=', 'm_st_schedule.ST_SCHEDULE')
        ->join("m_ac",function($join){
            $join->on("m_ac.ST_CODE","=","candidate_nomination_detail.st_code")
                ->on("m_ac.AC_NO","=","candidate_nomination_detail.ac_no");
        })
        ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","candidate_nomination_detail.st_code");
            })
        ->select('nom_id', 'cand_name','candidate_father_name','cand_email','cand_mobile','PARTYABBRE','PARTYNAME','candidate_nomination_detail.st_code', 'm_state.ST_NAME', 'candidate_nomination_detail.candidate_id', 'candidate_personal_detail.is_criminal', 'm_ac.AC_NAME','DIST_NO', 'DIST_NAME', 'm_st_schedule.ST_SCHEDULE','m_election_details.StatePHASE_NO', DB::raw('(CASE 
          WHEN candidate_nomination_detail.application_status = 1 THEN "Applied"
          WHEN candidate_nomination_detail.application_status = 2 THEN "Submited and verified by RO"
          WHEN candidate_nomination_detail.application_status = 3 THEN "Receipt Generated "
          WHEN candidate_nomination_detail.application_status = 4 THEN "Rejected"
          WHEN candidate_nomination_detail.application_status = 5 THEN "Withdrawn"
            WHEN  candidate_nomination_detail.finalaccepted = 1 AND candidate_nomination_detail.application_status = 6  THEN "Contesting"
          WHEN candidate_nomination_detail.application_status = 6 AND candidate_nomination_detail.finalaccepted = 0 THEN "Accepted"

        
          ELSE "None" END) AS application_status'))
        ->where("candidate_nomination_detail.party_id", "!=",  1180)
        ->where("candidate_nomination_detail.application_status", "!=", 11);
        //->where("candidate_nomination_detail.finalize", '1')
       // ->where("candidate_nomination_detail.symbol_id", "!=",  '200');
//dd($request->phase);
        $state_list = array();
        if(!empty($request->phase))
        {
            $res->where('candidate_nomination_detail.scheduleid', $request->phase);
            $state_list = DB::table('m_st_schedule')->where('ST_SCHEDULE', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.ST_SCHEDULE')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();
        }
        else
        {
          $state_list = DB::table('m_state')->select('ST_CODE','ST_NAME')->whereIn('ST_CODE', $st_list)->get();
        }

        $district_list = array();
        if(!empty($request->state_id) && !empty($request->phase))
        {
            $res->where('candidate_nomination_detail.st_code', $request->state_id);


            $district_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
            ->where('m_st_schedule.ST_SCHEDULE', $request->phase)
            ->select('DIST_NO', 'DIST_NAME')
            ->groupBy('DIST_NO')
            ->get();
        }
        else if(!empty($request->state_id))
        {
          $res->where('candidate_nomination_detail.st_code', $request->state_id);
          $district_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
            ->select('DIST_NO', 'DIST_NAME')
            ->groupBy('DIST_NO')
            ->get();
        }else if(!empty($request->phase))
        {

            $res->where('candidate_nomination_detail.state_phase_no', $request->phase);

        }

        $ac_list = array();
        if($request->district  && !empty($request->phase))
        {
          $res->where('m_ac.DIST_NO_HDQTR', $request->district);
          $ac_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
            ->where('m_st_schedule.ST_SCHEDULE', $request->phase)
            ->where('m_ac.DIST_NO_HDQTR', $request->district)
            ->select('AC_NO', 'AC_NAME')
            ->get();
        }
        else if($request->district)
        {
          $res->where('m_ac.DIST_NO_HDQTR', $request->district);
          $ac_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
            ->where('m_ac.DIST_NO_HDQTR', $request->district)
            ->select('AC_NO', 'AC_NAME')
            ->get();
        }



         if (!empty($request->symbol_search) && $request->symbol_search==1 ){

        $res->where("candidate_nomination_detail.symbol_id", "!=",  200);
      } else if(!empty($request->symbol_search) && $request->symbol_search==2)
      {

         $res->where("candidate_nomination_detail.symbol_id", "=",  200);
      }
      else {

         
      }

        if(!empty($request->ac_id))
            $res->where('candidate_nomination_detail.ac_no', $request->ac_id);

        if(!empty($request->party_id))
            $res->where('candidate_nomination_detail.party_id', $request->party_id);

        if(!empty($request->cand_type))
        {
            if($request->cand_type==2)
              $cand_type = '0';
            else
              $cand_type = '1';

            $res->where('candidate_personal_detail.is_criminal', $cand_type);   
        }

        // if(!empty($request->app_status))
        // {
        //     $res->where('candidate_nomination_detail.application_status', $request->app_status);
        //     if($request->app_status==6)
        //         $res->where('candidate_nomination_detail.finalaccepted', 1);
        // }

        if (!empty($request->app_status)) {

        if ($request->app_status == 12){
         $res->where('candidate_nomination_detail.application_status', '6');
         $res->where('candidate_nomination_detail.finalaccepted', 1);
         }else if($request->app_status == 6){
            $res->where('candidate_nomination_detail.application_status', '6');
             $res->where('candidate_nomination_detail.finalaccepted', 0);
         }else{

        $res->where('candidate_nomination_detail.application_status', $request->app_status);
      }
        
      }

        $res->groupBy("candidate_nomination_detail.candidate_id");
        $res->groupBy("candidate_nomination_detail.party_id");
        $res->orderBy("candidate_nomination_detail.scheduleid");
        $res->orderBy("candidate_nomination_detail.st_code");
        $data = $res->get();

        $party_list = DB::table('m_party')->select('CCODE','PARTYABBRE','PARTYNAME')->orderBy('PARTYNAME')->get();
        $status_list = DB::table('m_status')->select('status','id')->orderBy('status')
        ->whereNotIn("id", array(7,11))
        ->get();

        // echo "<pre>";
        // print_r($data);
        // die;
        return view('admin.ac.eci.ca_list')->with(array('user_data' => $d,'phase_list'=>$phase_list,'district_list'=>$district_list, 'data'=>$data,'state_list'=>$state_list,'party_list'=>$party_list,'ac_list'=>$ac_list,'status_list'=>$status_list));
    } 
    else 
    {
      return redirect('/officer-login');
    }
  }

*/

  public function get_ca_cand_list(Request $request)
  {
    if(Auth::check())
    {
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id); 
        $list_details = array();
        $dataArr = array();

       // $phase_list = DB::table('m_schedule')->select('SCHEDULEID')->get();   
         $phase_list = DB::table('m_election_details')
        ->groupBy('m_election_details.StatePHASE_NO')
        ->get(); 


        $list = DB::table('m_election_details')->select('ST_CODE')
        ->whereIn('ELECTION_TYPEID', [3, 4])
        ->groupBy('m_election_details.ST_CODE')
        ->orderBy('ST_CODE','ASC')
        ->get();
        $st_list = array();
        foreach ($list as $key) 
        {
          array_push($st_list, $key->ST_CODE);
        }

        $res = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->leftjoin('m_schedule', 'm_schedule.SCHEDULEID', '=', 'candidate_nomination_detail.scheduleid')
        ->leftjoin('m_election_details', 'm_election_details.ScheduleID', '=', 'm_schedule.SCHEDULEID')
        ->join("m_ac",function($join){
            $join->on("m_ac.ST_CODE","=","candidate_nomination_detail.st_code")
                ->on("m_ac.AC_NO","=","candidate_nomination_detail.ac_no");
        })
        ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","candidate_nomination_detail.st_code");
            })
        ->select('nom_id', 'cand_name','candidate_father_name','cand_email','cand_mobile','PARTYABBRE','PARTYNAME','candidate_nomination_detail.st_code', 'm_state.ST_NAME', 'candidate_nomination_detail.candidate_id', 'candidate_personal_detail.is_criminal', 'm_ac.AC_NAME','DIST_NO', 'DIST_NAME', 'm_schedule.SCHEDULEID','m_election_details.StatePHASE_NO', DB::raw('(CASE 
          WHEN candidate_nomination_detail.application_status = 1 THEN "Applied"
          WHEN candidate_nomination_detail.application_status = 2 THEN "Submited and verified by RO"
          WHEN candidate_nomination_detail.application_status = 3 THEN "Receipt Generated "
          WHEN candidate_nomination_detail.application_status = 4 THEN "Rejected"
          WHEN candidate_nomination_detail.application_status = 5 THEN "Withdrawn"
            WHEN   candidate_nomination_detail.finalaccepted = 1  AND candidate_nomination_detail.application_status = 6   THEN "Contesting"
          WHEN candidate_nomination_detail.application_status = 6 AND candidate_nomination_detail.finalaccepted = 0 THEN "Accepted"

        
          ELSE "None" END) AS application_status'))
        ->where("candidate_nomination_detail.party_id", "!=",  1180)
        ->where("candidate_nomination_detail.application_status", "!=", 11);
        //->where("candidate_nomination_detail.finalize", '1')
       // ->where("candidate_nomination_detail.symbol_id", "!=",  '200');

        $state_list = array();
        if(!empty($request->phase))
        {
            $res->where('candidate_nomination_detail.state_phase_no', $request->phase);
            $state_list = DB::table('m_st_schedule')->where('ST_SCHEDULE', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.ST_SCHEDULE')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();
        }
        else
        {
          $state_list = DB::table('m_state')->select('ST_CODE','ST_NAME')->whereIn('ST_CODE', $st_list)->get();
        }

        $district_list = array();
        if(!empty($request->state_id) && !empty($request->phase))
        {
            $res->where('candidate_nomination_detail.st_code', $request->state_id);

            $district_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
            ->where('m_st_schedule.SCHEDULEID', $request->phase)
            ->select('DIST_NO', 'DIST_NAME')
            ->groupBy('DIST_NO')
            ->get();
        }
        else if(!empty($request->state_id))
        {
          $res->where('candidate_nomination_detail.st_code', $request->state_id);
          $district_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
            ->select('DIST_NO', 'DIST_NAME')
            ->groupBy('DIST_NO')
            ->get();
        }

        $ac_list = array();
        if($request->district  && !empty($request->phase))
        {
          $res->where('m_ac.DIST_NO_HDQTR', $request->district);
          $ac_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
            ->where('m_st_schedule.SCHEDULEID', $request->phase)
            ->where('m_ac.DIST_NO_HDQTR', $request->district)
            ->select('AC_NO', 'AC_NAME')
            ->get();
        }
        else if($request->district)
        {
          $res->where('m_ac.DIST_NO_HDQTR', $request->district);
          $ac_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
            ->where('m_ac.DIST_NO_HDQTR', $request->district)
            ->select('AC_NO', 'AC_NAME')
            ->get();
        }



         if (!empty($request->symbol_search) && $request->symbol_search==1 ){

        $res->where("candidate_nomination_detail.symbol_id", "!=",  200);
      } else if(!empty($request->symbol_search) && $request->symbol_search==2)
      {

         $res->where("candidate_nomination_detail.symbol_id", "=",  200);
      }
      else {

         
      }

        if(!empty($request->ac_id))
            $res->where('candidate_nomination_detail.ac_no', $request->ac_id);

        if(!empty($request->party_id))
            $res->where('candidate_nomination_detail.party_id', $request->party_id);

        if(!empty($request->cand_type))
        {
            if($request->cand_type==2)
              $cand_type = '0';
            else
              $cand_type = '1';

            $res->where('candidate_personal_detail.is_criminal', $cand_type);   
        }

        // if(!empty($request->app_status))
        // {
        //     $res->where('candidate_nomination_detail.application_status', $request->app_status);
        //     if($request->app_status==6)
        //         $res->where('candidate_nomination_detail.finalaccepted', 1);
        // }

        if (!empty($request->app_status)) {

        if ($request->app_status == 12){
         $res->where('candidate_nomination_detail.application_status', '6');
         $res->where('candidate_nomination_detail.finalaccepted', 1);
         }else if($request->app_status == 6){
            $res->where('candidate_nomination_detail.application_status', '6');
             $res->where('candidate_nomination_detail.finalaccepted', 0);
         }else{

        $res->where('candidate_nomination_detail.application_status', $request->app_status);
      }
        
      }

        $res->groupBy("candidate_nomination_detail.candidate_id");
        $res->groupBy("candidate_nomination_detail.party_id");
        $res->orderBy("candidate_nomination_detail.scheduleid");
        $res->orderBy("candidate_nomination_detail.st_code");
        $data = $res->get();

        $party_list = DB::table('m_party')->select('CCODE','PARTYABBRE','PARTYNAME')->orderBy('PARTYNAME')->get();
        $status_list = DB::table('m_status')->select('status','id')->orderBy('status')
        ->whereNotIn("id", array(7,11))
        ->get();

         // echo "<pre>";
         // print_r($data);
         // die;
        return view('admin.ac.eci.ca_list')->with(array('user_data' => $d,'phase_list'=>$phase_list,'district_list'=>$district_list, 'data'=>$data,'state_list'=>$state_list,'party_list'=>$party_list,'ac_list'=>$ac_list,'status_list'=>$status_list));
    } 
    else 
    {
      return redirect('/officer-login');
    }
  }
  public function get_ac(Request $request)
    {
       $res = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
            ->where('m_ac.DIST_NO_HDQTR', $request->district_id)
            ->select('AC_NO', 'AC_NAME');

            if(!empty($request->schedule_id))
                $res->where('m_st_schedule.ST_SCHEDULE', $request->schedule_id);

          $acs = $res->get();
          $data = array();
        for($i=0;$i<count($acs);$i++)
        {
           $data[] = array('id'=>$acs[$i]->AC_NO,'name'=>$acs[$i]->AC_NAME);
        }
        $output  = $data;
        echo json_encode($output);
    }

    public function get_state(Request $request)
    {
      if($request->id!=0)
      {
         $states = DB::table('m_st_schedule')->where('ST_SCHEDULE', $request->id)
              ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
              ->select('m_state.ST_CODE', 'm_state.ST_NAME')
              ->orderBy('m_state.ST_CODE', 'ASC')
              ->groupBy('m_st_schedule.ST_SCHEDULE')
              ->groupBy('m_st_schedule.ST_CODE')
              ->get();
              $data = array();
          for($i=0;$i<count($states);$i++)
          {
             $data[] = array('id'=>$states[$i]->ST_CODE,'name'=>$states[$i]->ST_NAME);
          }
          $output  = $data;
          echo json_encode($output);
      }
      else
      {
        $list = DB::table('m_election_details')->select('ST_CODE')
        ->whereIn('ELECTION_TYPEID', [3, 4])
        ->groupBy('m_election_details.ST_CODE')
        ->orderBy('ST_CODE','ASC')
        ->get();
        $st_list = array();
        foreach ($list as $key) 
        {
          array_push($st_list, $key->ST_CODE);
        }
        $state_list = DB::table('m_state')->select('ST_CODE','ST_NAME')->whereIn('ST_CODE', $st_list)->get();
        for($i=0;$i<count($state_list);$i++)
          {
             $data[] = array('id'=>$state_list[$i]->ST_CODE,'name'=>$state_list[$i]->ST_NAME);
          }
          $output  = $data;
          echo json_encode($output);
      }
    }

    public function get_district(Request $request)
        {
            $res = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->id)            
            ->select('DIST_NO', 'DIST_NAME')
            ->groupBy('DIST_NO');

            if(!empty($request->schedule_id))
                $res->where('m_st_schedule.ST_SCHEDULE', $request->schedule_id);

            $districts = $res->get();

            $data = array();
            for($i=0;$i<count($districts);$i++)
            {
               $data[] = array('id'=>$districts[$i]->DIST_NO,'name'=>$districts[$i]->DIST_NAME);
            }
            $output  = $data;

           // dd($output);
            echo json_encode($output);
        }

    public function get_ca_cand_list_pdf(Request $request)
    {
      if(Auth::check())
      {
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id); 
        $list_details = array();
        $dataArr = array();   
        
         $phase_list = DB::table('m_election_details')
        ->groupBy('m_election_details.StatePHASE_NO')
        ->get();   

        $res = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->leftjoin('m_schedule', 'm_schedule.SCHEDULEID', '=', 'candidate_nomination_detail.scheduleid')
        ->leftjoin('m_election_details', 'm_election_details.ScheduleID', '=', 'm_schedule.SCHEDULEID')
        ->leftjoin('m_symbol', 'm_symbol.SYMBOL_NO', '=', 'candidate_nomination_detail.symbol_id')
        ->join("m_ac",function($join){
            $join->on("m_ac.ST_CODE","=","candidate_nomination_detail.st_code")
                ->on("m_ac.AC_NO","=","candidate_nomination_detail.ac_no");
        })
        ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","candidate_nomination_detail.st_code");
            })
        ->select('nom_id', 'cand_name','candidate_father_name','cand_email','cand_mobile','PARTYABBRE','PARTYNAME','candidate_nomination_detail.st_code', 'candidate_nomination_detail.state_phase_no','m_state.ST_NAME', 'm_state.ST_CODE', 'candidate_nomination_detail.candidate_id', 'candidate_personal_detail.is_criminal',  'candidate_personal_detail.cand_gender', 'candidate_personal_detail.cand_age','candidate_personal_detail.cand_category', 'm_ac.AC_NAME','DIST_NO', 'DIST_NAME','m_ac.AC_NO', 'm_schedule.SCHEDULEID', 'm_symbol.SYMBOL_DES','m_election_details.StatePHASE_NO', DB::raw('(CASE 
          WHEN candidate_nomination_detail.application_status = 1 THEN "Applied"
          WHEN candidate_nomination_detail.application_status = 2 THEN "Submited and verified by RO"
          WHEN candidate_nomination_detail.application_status = 3 THEN "Receipt Generated "
          WHEN candidate_nomination_detail.application_status = 4 THEN "Rejected"
          WHEN candidate_nomination_detail.application_status = 5 THEN "Withdrawn"
           WHEN  candidate_nomination_detail.finalaccepted = 1  AND candidate_nomination_detail.application_status = 6  THEN "Contesting"
          WHEN candidate_nomination_detail.application_status = 6 AND candidate_nomination_detail.finalaccepted = 0 THEN "Accepted"

          ELSE "None" END) AS application_status'))
        ->where("candidate_nomination_detail.party_id", "!=",  1180)
        ->where("candidate_nomination_detail.application_status", "!=", 11);
        //->where("candidate_nomination_detail.finalize", '1')
        //->where("candidate_nomination_detail.symbol_id", "!=",  '200');

        $state_list = array();
        if(!empty($request->phase))
        {
            $res->where('candidate_nomination_detail.state_phase_no', $request->phase);

            $state_list = DB::table('m_st_schedule')->where('ST_SCHEDULE', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.ST_SCHEDULE')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();

            $data['phase_name'] = "Phase ".$request->phase;
        }
        else
        {
          $phase_name_list = array();
           foreach ($phase_list as $keys) 
            {
              array_push($phase_name_list, "Phase ".$keys->StatePHASE_NO);
            }            
            $data['phase_name'] = implode(",", $phase_name_list);
            $state_list = DB::table('m_election_details')->select('ST_CODE')
            ->whereIn('ELECTION_TYPEID', [3, 4])
            ->groupBy('m_election_details.ST_CODE')
            ->orderBy('ST_CODE','ASC')
            ->get();
        }

        if(!empty($request->state_id))
        {
            $res->where('candidate_nomination_detail.st_code', $request->state_id);
            $state_data = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->first();
            $data['state_list_pdf'] = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->get();
            $data['state_name'] = $state_data->ST_NAME;

        }
        else
        {
            $st_list = array();
            foreach ($state_list as $key) 
            {
              array_push($st_list, $key->ST_CODE);
            }
            $res->whereIn('candidate_nomination_detail.st_code', $st_list);
            $state_list = DB::table('m_state')->select('ST_CODE','ST_NAME')->whereIn('ST_CODE', $st_list)->get();
            $st_name_list = array();
            foreach ($state_list as $keys) 
            {
              array_push($st_name_list, $keys->ST_NAME);
            }            
            $data['state_list_pdf'] = $state_list;
            $data['state_name'] = implode(",", $st_name_list);
        }

        if(!empty($request->district))
        {
          $res->where('m_ac.DIST_NO_HDQTR', $request->district);
        }

        if(!empty($request->ac_id_report))
            $res->where('candidate_nomination_detail.ac_no', $request->ac_id_report);

        if(!empty($request->party_id))
            $res->where('candidate_nomination_detail.party_id', $request->party_id);

        if(!empty($request->cand_type))
        {
            if($request->cand_type==2)
              $cand_type = '0';
            else
              $cand_type = '1';

            $res->where('candidate_personal_detail.is_criminal', $cand_type);   
        }

       /* if(!empty($request->app_status))
        {
            $res->where('candidate_nomination_detail.application_status', $request->app_status);            
            if($request->app_status==6)
                  $res->where('candidate_nomination_detail.finalaccepted', 1);
        } */




      if (!empty($request->symbol_search) && $request->symbol_search==1 ){

        $res->where("candidate_nomination_detail.symbol_id", "!=",  200);
      } else if(!empty($request->symbol_search) && $request->symbol_search==2)
      {

         $res->where("candidate_nomination_detail.symbol_id", "=",  200);
      }
      else {

         
      }




        if (!empty($request->app_status)) {

        if ($request->app_status == 12){
         $res->where('candidate_nomination_detail.application_status', '6');
         $res->where('candidate_nomination_detail.finalaccepted', 1);
         }else if($request->app_status == 6){
            $res->where('candidate_nomination_detail.application_status', '6');
             $res->where('candidate_nomination_detail.finalaccepted', 0);
         }else{

        $res->where('candidate_nomination_detail.application_status', $request->app_status);
      }
        
      }

        $res->groupBy("candidate_nomination_detail.candidate_id");
        $res->groupBy("candidate_nomination_detail.party_id");
        $res->orderBy("candidate_nomination_detail.scheduleid");
        $res->orderBy("candidate_nomination_detail.st_code");
        $data['results'] = $res->get();
        /*echo "<pre>";
        print_r($data['results']);
        die;*/
        $name_pdf = "Candidate_CA_Report";
        //return view('admin.ac.eci.ca_list_summary_pdf', ['data'=>$data]);
        $pdf = \PDF::loadView('admin.ac.eci.ca_list_pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
      }
    }

    public function get_ca_cand_list_excel(Request $request)
    {
      if(Auth::check())
      {
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id); 
        $list_details = array();
        $dataArr = array();   
        
          $phase_list = DB::table('m_election_details')
        ->groupBy('m_election_details.StatePHASE_NO')
        ->get();   

        $res = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->leftjoin('m_schedule', 'm_schedule.SCHEDULEID', '=', 'candidate_nomination_detail.scheduleid')
        ->leftjoin('m_election_details', 'm_election_details.ScheduleID', '=', 'm_schedule.SCHEDULEID')
        ->leftjoin('m_symbol', 'm_symbol.SYMBOL_NO', '=', 'candidate_nomination_detail.symbol_id')
        ->join("m_ac",function($join){
            $join->on("m_ac.ST_CODE","=","candidate_nomination_detail.st_code")
                ->on("m_ac.AC_NO","=","candidate_nomination_detail.ac_no");
        })
        ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","candidate_nomination_detail.st_code");
            })
        ->select('nom_id', 'cand_name','candidate_father_name','cand_email','cand_mobile','PARTYABBRE','PARTYNAME','candidate_nomination_detail.st_code', 'm_state.ST_NAME',  'm_state.ST_CODE', 'candidate_nomination_detail.candidate_id', 'candidate_personal_detail.is_criminal', 'candidate_nomination_detail.state_phase_no', 'candidate_personal_detail.cand_gender',  'candidate_personal_detail.cand_age','candidate_personal_detail.cand_category', 'm_ac.AC_NAME','m_ac.AC_NO','DIST_NO', 'DIST_NAME', 'm_schedule.SCHEDULEID', 'm_symbol.SYMBOL_DES','m_election_details.StatePHASE_NO', DB::raw('(CASE 
          WHEN candidate_nomination_detail.application_status = 1 THEN "Applied"
          WHEN candidate_nomination_detail.application_status = 2 THEN "Submited and verified by RO"
          WHEN candidate_nomination_detail.application_status = 3 THEN "Receipt Generated "
          WHEN candidate_nomination_detail.application_status = 4 THEN "Rejected"
          WHEN candidate_nomination_detail.application_status = 5 THEN "Withdrawn"
           WHEN  candidate_nomination_detail.finalaccepted = 1  AND candidate_nomination_detail.application_status = 6  THEN "Contesting"
          WHEN candidate_nomination_detail.application_status = 6 AND candidate_nomination_detail.finalaccepted = 0 THEN "Accepted"

          ELSE "None" END) AS application_status'))
        ->where("candidate_nomination_detail.party_id", "!=",  1180)
        ->where("candidate_nomination_detail.application_status", "!=", 11);
      //  ->where("candidate_nomination_detail.finalize", '1')
       // ->where("candidate_nomination_detail.symbol_id", "!=",  '200');

        $state_list = array();
        if(!empty($request->phase))
        {
            $res->where('candidate_nomination_detail.state_phase_no', $request->phase);

            $state_list = DB::table('m_st_schedule')->where('ST_SCHEDULE', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.ST_SCHEDULE')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();

            $data['phase_name'] = "Phase ".$request->phase;
        }
        else
        {
          $phase_name_list = array();
           foreach ($phase_list as $keys) 
            {
              array_push($phase_name_list, "Phase ".$keys->StatePHASE_NO);
            }            
            $data['phase_name'] = implode(",", $phase_name_list);
            $state_list = DB::table('m_election_details')->select('ST_CODE')
            ->whereIn('ELECTION_TYPEID', [3, 4])
            ->groupBy('m_election_details.ST_CODE')
            ->orderBy('ST_CODE','ASC')
            ->get();
        }

        if(!empty($request->state_id))
        {
            $res->where('candidate_nomination_detail.st_code', $request->state_id);
            $state_data = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->first();
            $data['state_list_pdf'] = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->get();
            $data['state_name'] = $state_data->ST_NAME;

        }
        else
        {
            $st_list = array();
            foreach ($state_list as $key) 
            {
              array_push($st_list, $key->ST_CODE);
            }
            $res->whereIn('candidate_nomination_detail.st_code', $st_list);
            $state_list = DB::table('m_state')->select('ST_CODE','ST_NAME')->whereIn('ST_CODE', $st_list)->get();
            $st_name_list = array();
            foreach ($state_list as $keys) 
            {
              array_push($st_name_list, $keys->ST_NAME);
            }            
            $data['state_list_pdf'] = $state_list;
            $data['state_name'] = implode(",", $st_name_list);
        }

        if(!empty($request->district))
        {
          $res->where('m_ac.DIST_NO_HDQTR', $request->district);
        }

        if(!empty($request->ac_id_report))
            $res->where('candidate_nomination_detail.ac_no', $request->ac_id_report);

        if(!empty($request->party_id))
            $res->where('candidate_nomination_detail.party_id', $request->party_id);

        if(!empty($request->cand_type))
        {
            if($request->cand_type==2)
              $cand_type = '0';
            else
              $cand_type = '1';

            $res->where('candidate_personal_detail.is_criminal', $cand_type);   
        }

      /*  if(!empty($request->app_status))
        {
            $res->where('candidate_nomination_detail.application_status', $request->app_status);            
            if($request->app_status==6)
                  $res->where('candidate_nomination_detail.finalaccepted', 1);
        }
        */


      if (!empty($request->symbol_search) && $request->symbol_search==1 ){

        $res->where("candidate_nomination_detail.symbol_id", "!=",  200);
      } else if(!empty($request->symbol_search) && $request->symbol_search==2)
      {

         $res->where("candidate_nomination_detail.symbol_id", "=",  200);
      }
      else {

         
      }

        if (!empty($request->app_status)) {

        if ($request->app_status == 12){
         $res->where('candidate_nomination_detail.application_status', '6');
         $res->where('candidate_nomination_detail.finalaccepted', 1);
         }else if($request->app_status == 6){
            $res->where('candidate_nomination_detail.application_status', '6');
             $res->where('candidate_nomination_detail.finalaccepted', 0);
         }else{

        $res->where('candidate_nomination_detail.application_status', $request->app_status);
      }
        
      }

        $res->groupBy("candidate_nomination_detail.candidate_id");
        $res->groupBy("candidate_nomination_detail.party_id");
        $res->orderBy("candidate_nomination_detail.scheduleid");
        $res->orderBy("candidate_nomination_detail.st_code");
        $data['results'] = $res->get();
        /*echo "<pre>";
        print_r($data['results']);
        die;*/
        //return view('admin.ac.eci.ca_list_summary_pdf', ['data'=>$data]);
        $export_data = [];
        $headings[] = ["Phase(s): ".$data['phase_name']."\n State: ".$data['state_name']."\n Date: ".date("d-m-Y")];
        
        $export_data[] = ['Phase' ,'NOMINATION ID', 'CANDIDATE NAME', 'SON/HUSBAND OF', 'GENDER', 'AGE', 'CATEGORY', 'STATE NO', 'STATE','DISTRICT NO','DISTRICT','AC NO','AC', 'PARTY','SYMBOL','IS CRIMINAL','IS CRIMINAL FLAG','STATUS'];

         foreach ($data['results'] as $lis) {
          if($lis->is_criminal==1)
            $is_criminal =  "Yes";
          else
            $is_criminal =  "No";

          $export_data[] = [
          "Phase: ".$lis->state_phase_no,
          $lis->nom_id,
          $lis->cand_name,
          $lis->candidate_father_name,
          $lis->cand_gender,
          $lis->cand_age,
          $lis->cand_category,
          $lis->ST_CODE,
          $lis->ST_NAME,
          $lis->DIST_NO,
          $lis->DIST_NAME,
          $lis->AC_NO,
          $lis->AC_NAME,
          $lis->PARTYNAME,
          $lis->SYMBOL_DES,
          $is_criminal,
          $is_criminal,
          $lis->application_status
          ];
        }
        $name_excel = "Candidate_CA_Report";

        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
      }
    }

    public function get_ca_cand_list_summary_pdf(Request $request)
    {
      if(Auth::check())
      {
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id); 
        $list_details = array();
        $dataArr = array();   
    
        $phase_list = DB::table('m_schedule')->select('SCHEDULEID')->get();   

        $res = DB::table('candidate_nomination_detail')
        ->leftjoin('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->select('candidate_nomination_detail.st_code','party_id', 'PARTYABBRE','PARTYNAME', 'scheduleid', 'm_state.ST_NAME')
        
        ->where("candidate_nomination_detail.party_id", "!=", 1180);

        $state_list = array();
        if(!empty($request->phase))
        {
            $res->where('candidate_nomination_detail.scheduleid', $request->phase);
            $state_list = DB::table('m_st_schedule')->where('SCHEDULEID', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.SCHEDULEID')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();

            $data['phase_name'] = "Phase ".$request->phase;
            $data['phase_list_pdf'] = $request->phase;
        }
        else
        {
          $state_list = DB::table('m_election_details')->select('ST_CODE')
          ->where('ELECTION_TYPEID','3')
          ->groupBy('m_election_details.ST_CODE')
          ->orderBy('ST_CODE','ASC')
          ->get();

          $phase_name_list = array();
           foreach ($phase_list as $keys) 
            {
              array_push($phase_name_list, "Phase ".$keys->SCHEDULEID);
            }            
            $data['phase_name'] = implode(",", $phase_name_list);
            $data['phase_list_pdf'] = "0";
        }

        if(!empty($request->state_id))
        {
            $res->where('candidate_nomination_detail.st_code', $request->state_id);
            $state_data = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->first();
            $data['state_list_pdf'] = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->get();
            $data['state_name'] = $state_data->ST_NAME;

        }
        else
        {
            $st_list = array();
            foreach ($state_list as $key) 
            {
              array_push($st_list, $key->ST_CODE);
            }
            $res->whereIn('candidate_nomination_detail.st_code', $st_list);
            $state_list = DB::table('m_state')->select('ST_CODE','ST_NAME')->whereIn('ST_CODE', $st_list)->get();
            $st_name_list = array();
            foreach ($state_list as $keys) 
            {
              array_push($st_name_list, $keys->ST_NAME);
            }            
            $data['state_list_pdf'] = $state_list;
            $data['state_name'] = implode(",", $st_name_list);
        }

        $data['app_status']=0;
        if(!empty($request->app_status))
        {
            $data['app_status']=$request->app_status;            
        }

        $res->groupBy("candidate_nomination_detail.st_code");
        $res->groupBy("candidate_nomination_detail.party_id");
        $res->orderBy("candidate_nomination_detail.scheduleid");
        $data['results'] = $res->get();
       /* echo "<pre>";
        print_r($data['results']);
        die;*/
        $name_pdf = "Candidate_CA_Report";
        //return view('admin.ac.eci.ca_list_summary_pdf', ['data'=>$data]);
        $pdf = \PDF::loadView('admin.ac.eci.ca_list_summary_pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
      }
    }

    public function get_ca_cand_list_summary_excel(Request $request)
    {
      if(Auth::check())
      {
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id); 
        $list_details = array();
        $dataArr = array();   
    
        $phase_list = DB::table('m_schedule')->select('SCHEDULEID')->get();   

        $res = DB::table('candidate_nomination_detail')
        ->leftjoin('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->select('candidate_nomination_detail.st_code','party_id', 'PARTYABBRE','PARTYNAME', 'scheduleid', 'm_state.ST_NAME')
        ->where("candidate_nomination_detail.party_id", "!=", 1180);

        $state_list = array();
        if(!empty($request->phase))
        {
            $res->where('candidate_nomination_detail.scheduleid', $request->phase);
            $state_list = DB::table('m_st_schedule')->where('SCHEDULEID', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.SCHEDULEID')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();

            $data['phase_name'] = "Phase ".$request->phase;
            $data['phase_list_pdf'] = $request->phase;
        }
        else
        {
          $state_list = DB::table('m_election_details')->select('ST_CODE')
          ->where('ELECTION_TYPEID','3')
          ->groupBy('m_election_details.ST_CODE')
          ->orderBy('ST_CODE','ASC')
          ->get();

          $phase_name_list = array();
           foreach ($phase_list as $keys) 
            {
              array_push($phase_name_list, "Phase ".$keys->SCHEDULEID);
            }            
            $data['phase_name'] = implode(",", $phase_name_list);
            $data['phase_list_pdf'] = "0";
        }

        if(!empty($request->state_id))
        {
            $res->where('candidate_nomination_detail.st_code', $request->state_id);
            $state_data = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->first();
            $data['state_list_pdf'] = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->get();
            $data['state_name'] = $state_data->ST_NAME;

        }
        else
        {
            $st_list = array();
            foreach ($state_list as $key) 
            {
              array_push($st_list, $key->ST_CODE);
            }
            $res->whereIn('candidate_nomination_detail.st_code', $st_list);
            $state_list = DB::table('m_state')->select('ST_CODE','ST_NAME')->whereIn('ST_CODE', $st_list)->get();
            $st_name_list = array();
            foreach ($state_list as $keys) 
            {
              array_push($st_name_list, $keys->ST_NAME);
            }            
            $data['state_list_pdf'] = $state_list;
            $data['state_name'] = implode(",", $st_name_list);
        }

        $app_status=0;
        if(!empty($request->app_status))
            $app_status=$request->app_status;

        $res->groupBy("candidate_nomination_detail.st_code");
        $res->groupBy("candidate_nomination_detail.party_id");
        $res->orderBy("candidate_nomination_detail.scheduleid");
        $res->orderBy("candidate_nomination_detail.st_code");
        $data['results'] = $res->get();
       /* echo "<pre>";
        print_r($data['results']);
        die;*/
        $export_data = [];
        $headings[] = ["Phase(s): ".$data['phase_name']."\n State: ".$data['state_name']."\n Date: ".date("d-m-Y")];
        
        $export_data[] = ['STATE', 'PARTY', 'PHASE', 'NO OF CANDIDATE WITH CA - YES', 'NO OF CANDIDATE WITH CA - NO', 'TOTAL'];

          $total_yes = 0; 
          $total_no = 0; 
          $total = 0; 
         foreach ($data['results'] as $lis) {
          $yes = '0';
          $no = '0'; 
          
          $yes = get_ca_count($lis->st_code,$lis->party_id,'1', $data['phase_list_pdf'], $app_status);
          $no = get_ca_count($lis->st_code,$lis->party_id,'0', $data['phase_list_pdf'], $app_status);
          if($yes+$no==0)
            continue;
          else
          {
              $total_yes = $total_yes+$yes; 
              $total_no = $total_no+$no; 
              $total = $total+$yes+$no; 
              $export_data[] = [
                $lis->st_code."-".$lis->ST_NAME,
                $lis->PARTYNAME,
                $lis->scheduleid,
                $yes,
                $no,
                $yes+$no
                ];
          }
        }
        $export_data[] = ['Total','','',$total_yes,$total_no,$total];
        $name_excel = "Candidate_CA_Summary_Report";

        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
      }
    }

    public function get_ca_cand_list_acwisesummary_pdf(Request $request)
    {
      if(Auth::check())
      {
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id); 
        $list_details = array();
        $dataArr = array();   
    
        $phase_list = DB::table('m_schedule')->select('SCHEDULEID')->get();   

        $res = DB::table('candidate_nomination_detail')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->join("m_ac",function($join){
            $join->on("m_ac.ST_CODE","=","candidate_nomination_detail.st_code")
                ->on("m_ac.AC_NO","=","candidate_nomination_detail.ac_no");
        })
        ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","candidate_nomination_detail.st_code");
            })
        ->select('candidate_nomination_detail.st_code','candidate_nomination_detail.ac_no','scheduleid', 'm_state.ST_NAME', 'DIST_NO', 'DIST_NAME','m_ac.AC_NAME')        
        ->where("candidate_nomination_detail.party_id", "!=", 1180);

        $state_list = array();
        if(!empty($request->phase))
        {
            $res->where('candidate_nomination_detail.scheduleid', $request->phase);
            $state_list = DB::table('m_st_schedule')->where('SCHEDULEID', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.SCHEDULEID')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();

            $data['phase_name'] = "Phase ".$request->phase;
            $data['phase_list_pdf'] = $request->phase;
        }
        else
        {
          $state_list = DB::table('m_election_details')->select('ST_CODE')
          ->where('ELECTION_TYPEID','3')
          ->groupBy('m_election_details.ST_CODE')
          ->orderBy('ST_CODE','ASC')
          ->get();

          $phase_name_list = array();
           foreach ($phase_list as $keys) 
            {
              array_push($phase_name_list, "Phase ".$keys->SCHEDULEID);
            }            
            $data['phase_name'] = implode(",", $phase_name_list);
            $data['phase_list_pdf'] = "0";
        }

        if(!empty($request->state_id))
        {
            $res->where('candidate_nomination_detail.st_code', $request->state_id);
            $state_data = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->first();
            $data['state_list_pdf'] = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->get();
            $data['state_name'] = $state_data->ST_NAME;

        }
        else
        {
            $st_list = array();
            foreach ($state_list as $key) 
            {
              array_push($st_list, $key->ST_CODE);
            }
            $res->whereIn('candidate_nomination_detail.st_code', $st_list);
            $state_list = DB::table('m_state')->select('ST_CODE','ST_NAME')->whereIn('ST_CODE', $st_list)->get();
            $st_name_list = array();
            foreach ($state_list as $keys) 
            {
              array_push($st_name_list, $keys->ST_NAME);
            }            
            $data['state_list_pdf'] = $state_list;
            $data['state_name'] = implode(",", $st_name_list);
        }

        $data['app_status']=0;
        if(!empty($request->app_status))
        {
            $data['app_status']=$request->app_status;            
        }

        $res->groupBy("candidate_nomination_detail.st_code");
        $res->groupBy("candidate_nomination_detail.ac_no");
        $res->orderBy("candidate_nomination_detail.scheduleid");
        $res->orderBy("DIST_NO");
        $res->orderBy("candidate_nomination_detail.ac_no");
        $data['results'] = $res->get();
        /*echo "<pre>";
        print_r($data['results']);
        die;*/
        $name_pdf = "Candidate_ACWise_CA_Report";
        //return view('admin.ac.eci.ca_list_summary_pdf', ['data'=>$data]);
        $pdf = \PDF::loadView('admin.ac.eci.ca_list_acwisesummary_pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
      }
    }

    public function get_ca_cand_list_acwisesummary_excel(Request $request)
    {
      if(Auth::check())
      {
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id); 
        $list_details = array();
        $dataArr = array();   
    
        $phase_list = DB::table('m_schedule')->select('SCHEDULEID')->get();   

        $res = DB::table('candidate_nomination_detail')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->join("m_ac",function($join){
            $join->on("m_ac.ST_CODE","=","candidate_nomination_detail.st_code")
                ->on("m_ac.AC_NO","=","candidate_nomination_detail.ac_no");
        })
        ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","candidate_nomination_detail.st_code");
            })
        ->select('candidate_nomination_detail.st_code','candidate_nomination_detail.ac_no','scheduleid', 'm_state.ST_NAME', 'DIST_NO', 'DIST_NAME','m_ac.AC_NAME')        
        ->where("candidate_nomination_detail.party_id", "!=", 1180);

        $state_list = array();
        if(!empty($request->phase))
        {
            $res->where('candidate_nomination_detail.scheduleid', $request->phase);
            $state_list = DB::table('m_st_schedule')->where('SCHEDULEID', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.SCHEDULEID')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();

            $data['phase_name'] = "Phase ".$request->phase;
            $data['phase_list_pdf'] = $request->phase;
        }
        else
        {
          $state_list = DB::table('m_election_details')->select('ST_CODE')
          ->where('ELECTION_TYPEID','3')
          ->groupBy('m_election_details.ST_CODE')
          ->orderBy('ST_CODE','ASC')
          ->get();

          $phase_name_list = array();
           foreach ($phase_list as $keys) 
            {
              array_push($phase_name_list, "Phase ".$keys->SCHEDULEID);
            }            
            $data['phase_name'] = implode(",", $phase_name_list);
            $data['phase_list_pdf'] = "0";
        }

        if(!empty($request->state_id))
        {
            $res->where('candidate_nomination_detail.st_code', $request->state_id);
            $state_data = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->first();
            $data['state_list_pdf'] = DB::table('m_state')->select('ST_CODE','ST_NAME')->where('ST_CODE', $request->state_id)->get();
            $data['state_name'] = $state_data->ST_NAME;

        }
        else
        {
            $st_list = array();
            foreach ($state_list as $key) 
            {
              array_push($st_list, $key->ST_CODE);
            }
            $res->whereIn('candidate_nomination_detail.st_code', $st_list);
            $state_list = DB::table('m_state')->select('ST_CODE','ST_NAME')->whereIn('ST_CODE', $st_list)->get();
            $st_name_list = array();
            foreach ($state_list as $keys) 
            {
              array_push($st_name_list, $keys->ST_NAME);
            }            
            $data['state_list_pdf'] = $state_list;
            $data['state_name'] = implode(",", $st_name_list);
        }

        $app_status=0;
        if(!empty($request->app_status))
            $app_status=$request->app_status;

        $res->groupBy("candidate_nomination_detail.st_code");
        $res->groupBy("candidate_nomination_detail.ac_no");
        $res->orderBy("candidate_nomination_detail.scheduleid");
        $res->orderBy("DIST_NO");
        $res->orderBy("candidate_nomination_detail.ac_no");
        $data['results'] = $res->get();
        /*echo "<pre>";
        print_r($data['results']);
        die;*/
        $export_data = [];
        $headings[] = ["Phase(s): ".$data['phase_name']."\n State: ".$data['state_name']."\n Date: ".date("d-m-Y")];
        
        $export_data[] = ['PHASE', 'STATE', 'DISTRICT', 'AC',  'NO OF CANDIDATE WITH CA - YES', 'NO OF CANDIDATE WITH CA - NO', 'TOTAL'];

          $total_yes = 0; 
          $total_no = 0; 
          $total = 0; 
         foreach ($data['results'] as $lis) {
          $yes = '0';
          $no = '0'; 
          
          $yes = get_acwise_count($lis->st_code,$lis->ac_no,'1', $data['phase_list_pdf'], $app_status);
          $no = get_acwise_count($lis->st_code,$lis->ac_no,'0', $data['phase_list_pdf'], $app_status);
          if($yes+$no==0)
            continue;
          else
          {
              $total_yes = $total_yes+$yes; 
              $total_no = $total_no+$no; 
              $total = $total+$yes+$no; 
              $export_data[] = [
                $lis->scheduleid,
                $lis->st_code."-".$lis->ST_NAME,
                $lis->DIST_NO."-".$lis->DIST_NAME,
                $lis->ac_no."-".$lis->AC_NAME,
                $yes,
                $no,
                $yes+$no
                ];
          }
        }
        $export_data[] = ['Total','','','',$total_yes,$total_no,$total];
        $name_excel = "Candidate_ACWise_CA_Report";

        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
      }
    }


            
   public function ca_candidate_list_log_affidavit(){

   
   $data  = [];
    

      if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
           $lists = DB::table('candidate_affidavit_detail_log')
           
            ->leftjoin('candidate_nomination_detail', 'candidate_affidavit_detail_log.nom_id', '=', 'candidate_nomination_detail.nom_id')
             ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
           
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
           'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname','candidate_affidavit_detail_log.affidavit_path','candidate_affidavit_detail_log.created_at','candidate_affidavit_detail_log.log_updated_at','candidate_affidavit_detail_log.log_updated_by','candidate_affidavit_detail_log.affidavit_name')->get(); 

             
            $state_list = DB::table('m_st_schedule')
            //->where('SCHEDULEID', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.SCHEDULEID')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();

              //$data['user_data']=$d;
              $data['lists']=$lists;
               $data['state_list']=$state_list;

               $ac_list = array();
        if(isset($request->state_id) && $request->state_id)
        {
         // $res->where('m_ac.DIST_NO_HDQTR', $request->district);
          $ac_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
           
            ->select('AC_NO', 'AC_NAME')
            ->get();
        }
       
//dd($data);
return view('admin.ac.eci.affidavit_log_report')->with(array('user_data' => $d, 'lists' => $lists,'state_list'=>$state_list,'ac_list'=>$ac_list));

   }else {
        return redirect('/officer-login');
       }

     }

     


public function get_ac_base_state(Request $request)
        {
           
             $res = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
          
            ->where('m_st_schedule.ST_CODE', $request->id)
            //->where('m_ac.DIST_NO_HDQTR', $request->district_id)
            ->select('AC_NO', 'AC_NAME');

          
            $aclist = $res->get();
//dd($aclist);
            $data = array();
            for($i=0;$i<count($aclist);$i++)
            {
               $data[] = array('id'=>$aclist[$i]->AC_NO,'name'=>$aclist[$i]->AC_NAME);
            }
            $output  = $data;
            echo json_encode($output);
        }

        public function log_candidate_list(Request $request)
        {
        // dd($request->all());
          //$lists='';
               if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 

            if(!empty($request->state_id) && !empty($request->ac_id) ){

           $lists = DB::table('candidate_affidavit_detail_log')
           
            ->leftjoin('candidate_nomination_detail', 'candidate_affidavit_detail_log.nom_id', '=', 'candidate_nomination_detail.nom_id')
             ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
             ->where("candidate_affidavit_detail_log.st_code", "=", $request->state_id)
              ->where("candidate_affidavit_detail_log.ac_no", "=", $request->ac_id)

             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
          'candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_personal_detail.cand_name','candidate_affidavit_detail_log.affidavit_path','candidate_affidavit_detail_log.created_at','candidate_affidavit_detail_log.log_updated_at','candidate_affidavit_detail_log.log_updated_by','candidate_affidavit_detail_log.affidavit_name')
             ->get(); 
             
            }elseif(!empty($request->state_id) && empty($request->ac_id)){
            
                $lists = DB::table('candidate_affidavit_detail_log')
           
            ->leftjoin('candidate_nomination_detail', 'candidate_affidavit_detail_log.nom_id', '=', 'candidate_nomination_detail.nom_id')
             ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
           

                 ->where("candidate_affidavit_detail_log.st_code", "=", $request->state_id)
           
             

            ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
          'candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_personal_detail.cand_name','candidate_affidavit_detail_log.affidavit_path','candidate_affidavit_detail_log.created_at','candidate_affidavit_detail_log.log_updated_at','candidate_affidavit_detail_log.log_updated_by','candidate_affidavit_detail_log.affidavit_name')
             ->get(); 

            }else{

                $lists = DB::table('candidate_affidavit_detail_log')
           
            ->leftjoin('candidate_nomination_detail', 'candidate_affidavit_detail_log.nom_id', '=', 'candidate_nomination_detail.nom_id')
             ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            

             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
          'candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_personal_detail.cand_name','candidate_affidavit_detail_log.affidavit_path','candidate_affidavit_detail_log.created_at','candidate_affidavit_detail_log.log_updated_at','candidate_affidavit_detail_log.log_updated_by','candidate_affidavit_detail_log.affidavit_name')
             ->get(); 
            }


             // if(!empty($request->state_id){

             //  $lists->where("candidate_affidavit_detail_log.st_code", "=", $request->state_id);
             // }
            
             $state_list = DB::table('m_st_schedule')
            //->where('SCHEDULEID', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.SCHEDULEID')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();
          


              //$data['user_data']=$d;
              $data['lists']=$lists;
               $data['state_list']=$state_list;

               $ac_list = array();
        if(isset($request->state_id) && $request->state_id)
        {
         // $res->where('m_ac.DIST_NO_HDQTR', $request->district);
          $ac_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            ->leftjoin("m_district",function($join){
                $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
                    ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
           
            ->select('AC_NO', 'AC_NAME')
            ->get();
        }
       
//dd($data);
return view('admin.ac.eci.affidavit_log_report')->with(array('user_data' => $d, 'lists' => $lists,'state_list'=>$state_list,'ac_list'=>$ac_list));

             }else{

             }




        }


        // Export in Excel

          public function log_candidate_list_excel($state, $ac)
        {
        // print_r($state);echo "-----";print_r($ac);die();
          //$lists='';
               if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 

            if(!empty($state) && !empty($ac) ){

           $lists = DB::table('candidate_affidavit_detail_log')
           
            ->leftjoin('candidate_nomination_detail', 'candidate_affidavit_detail_log.nom_id', '=', 'candidate_nomination_detail.nom_id')
             ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
             ->where("candidate_affidavit_detail_log.st_code", "=", $state)
              ->where("candidate_affidavit_detail_log.ac_no", "=", $ac)

             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
          'candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_personal_detail.cand_name','candidate_affidavit_detail_log.affidavit_path','candidate_affidavit_detail_log.created_at','candidate_affidavit_detail_log.log_updated_at','candidate_affidavit_detail_log.log_updated_by','candidate_affidavit_detail_log.affidavit_name')
             ->get(); 
             
            }elseif(!empty($state) && empty($ac)){
            
                $lists = DB::table('candidate_affidavit_detail_log')
           
            ->leftjoin('candidate_nomination_detail', 'candidate_affidavit_detail_log.nom_id', '=', 'candidate_nomination_detail.nom_id')
             ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
           

                 ->where("candidate_affidavit_detail_log.st_code", "=", $state)
           
             

            ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
          'candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_personal_detail.cand_name','candidate_affidavit_detail_log.affidavit_path','candidate_affidavit_detail_log.created_at','candidate_affidavit_detail_log.log_updated_at','candidate_affidavit_detail_log.log_updated_by','candidate_affidavit_detail_log.affidavit_name')
             ->get(); 

            }else{

                $lists = DB::table('candidate_affidavit_detail_log')
           
            ->leftjoin('candidate_nomination_detail', 'candidate_affidavit_detail_log.nom_id', '=', 'candidate_nomination_detail.nom_id')
             ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            

             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
          'candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_personal_detail.cand_name','candidate_affidavit_detail_log.affidavit_path','candidate_affidavit_detail_log.created_at','candidate_affidavit_detail_log.log_updated_at','candidate_affidavit_detail_log.log_updated_by','candidate_affidavit_detail_log.affidavit_name')
             ->get(); 
            }

 $export_data = [];
        $headings[] = ["State: Affidavit Log"."\n Date: ".date("d-m-Y")];
        
         $export_data[] = [ 'Candidate Name','Nomination ID','STATE', 'AC Name','Created At','Updated At','Updated By'];

         foreach ($lists as $lis) {
            $ac=getacname($lis->st_code,$lis->ac_no);
                if(isset($ac))  $ac_name=$ac->AC_NAME;  
               
               $st=getstatebystatecode($lis->st_code);   
               if(isset($st))   $st_name=$st->ST_NAME; 
             
              $export_data[] = [
                $lis->cand_name,
                $lis->nom_id,
                $st_name,
                $ac_name,
              
                date('d-m-Y h:m:s',strtotime($lis->created_at)),
                date('d-m-Y h:m:s',strtotime($lis->log_updated_at)),
                $lis->log_updated_by,
               
                ];
          
        }
     //   $export_data[] = ['Total','','','',$total_yes,$total_no,$total];
        $name_excel = "Affidavit_Log_files";

        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


             }else{

             }


















   } // End Export Excel








  public function candidate_ca_upload(){


   $data  = [];
    

      if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 
           $lists = DB::table('candidate_personal_detail')
           
            ->leftjoin('candidate_nomination_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
             //->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
             ->where("candidate_nomination_detail.application_status", "=", '6')
             ->where("candidate_nomination_detail.finalaccepted", "=", '1')
          //      ->where("candidate_nomination_detail.symbol_id", "!=", 200)
            ->where("candidate_nomination_detail.party_id", "!=", 1180)
             ->where("candidate_personal_detail.is_criminal", "=", '0')
           
             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
           'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code',
            'candidate_personal_detail.candidate_father_name','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname')->get(); 

             
            $state_list = DB::table('m_st_schedule')
            //->where('SCHEDULEID', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
           // ->groupBy('m_st_schedule.SCHEDULEID')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();

              //$data['user_data']=$d;
              $data['lists']=$lists;
               $data['state_list']=$state_list;

               $ac_list = array();
        if(isset($request->state_id) && $request->state_id)
        {
         // $res->where('m_ac.DIST_NO_HDQTR', $request->district);
          $ac_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            
            ->where('m_st_schedule.ST_CODE', $request->state_id)
           
            ->select('AC_NO', 'AC_NAME')
            ->get();
        }
       
//dd($request->state_id);
return view('admin.ac.eci.candidatecafileupload')->with(array('user_data' => $d, 'lists' => $lists,'state_list'=>$state_list,'ac_list'=>$ac_list));

   }else {
        return redirect('/officer-login');
       }




    }

     public function ca_candidate_list(Request $request)
        {
        //dd($request->all());
          //$lists='';
               if(Auth::check()){ 
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id); 

            if(!empty($request->state_id) && !empty($request->ac_id) ){
                 
           $lists = DB::table('candidate_personal_detail')
          ->leftjoin('candidate_nomination_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
             ->where("candidate_nomination_detail.st_code", "=", $request->state_id)
              ->where("candidate_nomination_detail.ac_no", "=", $request->ac_id)
              ->where("candidate_nomination_detail.application_status", "=", '6')
             ->where("candidate_nomination_detail.finalaccepted", "=", '1')
           // ->where("candidate_nomination_detail.symbol_id", "!=", 200)
            ->where("candidate_nomination_detail.party_id", "!=", 1180)
             ->where("candidate_personal_detail.is_criminal", "=", '0')

              ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
           'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code',
            'candidate_personal_detail.candidate_father_name','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname')->get();
             
            }elseif(!empty($request->state_id) && empty($request->ac_id)){
         
                 $lists = DB::table('candidate_personal_detail')
          ->leftjoin('candidate_nomination_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
             ->where("candidate_nomination_detail.st_code", "=", $request->state_id)
             ->where("candidate_nomination_detail.application_status", "=", '6')
             ->where("candidate_nomination_detail.finalaccepted", "=", '1')
              //->where("candidate_nomination_detail.symbol_id", "!=", 200)
               ->where("candidate_nomination_detail.party_id", "!=", 1180)
                ->where("candidate_personal_detail.is_criminal", "=", '0')
             // ->where("candidate_nomination_detail.pc_no", "=", $request->ac_id)
           

            
             

             ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
           'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code',
            'candidate_personal_detail.candidate_father_name','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname')->get();

            }else{
           

                $lists = DB::table('candidate_personal_detail')
          ->leftjoin('candidate_nomination_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
            ->where("candidate_nomination_detail.application_status", "=", '6')
             ->where("candidate_nomination_detail.finalaccepted", "=", '1')
              //  ->where("candidate_nomination_detail.symbol_id", "!=", 200)
            ->where("candidate_nomination_detail.party_id", "!=", 1180)
             ->where("candidate_personal_detail.is_criminal", "=", '0')

               ->select('candidate_nomination_detail.nom_id','candidate_nomination_detail.candidate_id',
           'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code',
            'candidate_personal_detail.candidate_father_name','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname')->get();
            }


             // if(!empty($request->state_id){

             //  $lists->where("candidate_affidavit_detail_log.st_code", "=", $request->state_id);
             // }
            
             /*$state_list = DB::table('m_st_schedule')
            //->where('SCHEDULEID', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
            ->groupBy('m_st_schedule.SCHEDULEID')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();
            */

           // $state_list = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->get();
           $state_list = DB::table('m_st_schedule')
            //->where('SCHEDULEID', $request->phase)
            ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
            ->select('m_state.ST_CODE', 'm_state.ST_NAME')
            ->orderBy('m_state.ST_CODE', 'ASC')
           // ->groupBy('m_st_schedule.SCHEDULEID')
            ->groupBy('m_st_schedule.ST_CODE')
            ->get();

//dd($state_list);

              //$data['user_data']=$d;
              $data['lists']=$lists;
               $data['state_list']=$state_list;

               $ac_list = array();
        if(isset($request->state_id) && $request->state_id)
        {
         // $res->where('m_ac.DIST_NO_HDQTR', $request->district);
          $ac_list = DB::table('m_st_schedule')
            ->leftjoin("m_ac",function($join){
                $join->on("m_ac.ST_CODE","=","m_st_schedule.ST_CODE")
                    ->on("m_ac.AC_NO","=","m_st_schedule.CONST_NO");
            })
            // ->leftjoin("m_district",function($join){
            //     $join->on("m_district.DIST_NO","=","m_ac.DIST_NO_HDQTR")
            //         ->on("m_district.ST_CODE","=","m_st_schedule.ST_CODE");
            // })
            ->where('m_st_schedule.ST_CODE', $request->state_id)
           
            ->select('AC_NO', 'AC_NAME')
            ->get();
        }
       
//dd($data);
return view('admin.ac.eci.candidatecafileupload')->with(array('user_data' => $d, 'lists' => $lists,'state_list'=>$state_list,'ac_list'=>$ac_list));

             }else{

             }




        }

        public function uploaddocumnetca(Request $request)
        {


         //dd($request);

          try{
            $cid = $this->xssClean->clean_input($request->input('candidate_id'));
            $nom_id = $this->xssClean->clean_input($request->input('nom_id'));
            $stateid = $this->xssClean->clean_input($request->input('state'));
            $cons_no = $this->xssClean->clean_input($request->input('acno'));
            $electionid = $this->xssClean->clean_input($request->input('electionid'));
            $getdetails_by_ro = getById('candidate_nomination_detail', 'nom_id', $nom_id);


            $getAffidavitDetails = getById('candidate_criminaluploads', 'candidate_id', $cid);
          //  dd($getAffidavitDetails);
            //dd($getAffidavitDetails);
                $file = $request->file('criminalfile');
             if ($request->file('criminalfile')) {
             // dd("sdsd");
            //Move Uploaded File
            $newfile = $stateid . '_' . $cid . '_' . date('Ymdhis');
            $fileNewName = $newfile . '.' . $request->file('criminalfile')->getClientOriginalExtension();
           // dd($fileNewName);
            //edited by waseem paste it before move function
            if (!validate_pdf_file($request->file('criminalfile'))) {
              \Session::flash('error_mes', 'Only Pdf File uploaded');

return Redirect::to('/eci/candidate-ca-upload');
            }
            //end by waseem   
            $destinationPath = 'uploads1/criminaluploads/E' . $electionid . '/' . $stateid . '/' . $cons_no;

            $file->move($destinationPath, $fileNewName);

            $affidavitName = "Criminal Affidavit";
            $affidavit_path = $destinationPath . '/' . $fileNewName;
            if (!file_exists($affidavit_path)) {
              \Session::flash('error_mes', 'File is not uploaded. Please try again.');
              return Redirect::back()->withInput($request->all());
            }

            if (isset($getAffidavitDetails) ) {
              // if ($request->file('criminalfile') != '') {
              //   dd("update");
              //   $updateNomDetail = DB::update('update candidate_criminaluploads set path ="' . $affidavit_path . '" where candidate_id = ' . $cid);
              // }
            } else {
                //dd("insert");
               $criminalis='1';
             $updateNomDetail = DB::update('update candidate_personal_detail set is_criminal ="' . $criminalis . '" where candidate_id = ' . $cid);

            
              $insData = array(
                'election_id' => $electionid,
                'candidate_id' => $cid,
                'nom_id' => $nom_id,
                'name' => $affidavitName,
                'path' => $affidavit_path,
                'st_code' => $stateid,
                'ac_no' => $cons_no,
                'created_by' => $getdetails_by_ro->created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'added_create_at' => date('Y-m-d'),

              );
              CandidatecriminalModel::create($insData);
            

             $ins =DB::table('candidate_criminal_report')->insert(array(
                  'candidate_id'      =>$cid, 
                  'nom_id'            =>$nom_id,
                  'st_code'           =>$stateid,
                  'ac_no'             =>$cons_no,
                  'election_id'       =>$electionid,
                  'check_1'           =>'0',
                  'check_2'           =>'0', 
                  'check_3'           =>'0',
                  'status'            =>'0',
                  'finalaccept_ca'    =>'1',
                  'created_at'   => date('Y-m-d H:i:s', time()),

                )); 

             Session::flash('success_mes', 'File uploaded Successfully');
             return Redirect::to('/eci/candidate-ca-upload');

                    }


          }
        }catch(\Exception $e){
                DB::rollback();
        
                \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
                return Redirect::back();
            }


        }


















}  // end class