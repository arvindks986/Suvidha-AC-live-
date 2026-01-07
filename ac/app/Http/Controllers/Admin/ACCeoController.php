<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Mail\UserCredentialDetailsMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\commonModel;
use App\Helpers\SmsgatewayHelper;
use App\adminmodel\ACCEOModel;
use App\adminmodel\ACCEOReportModel;
use App\Classes\xssClean;
use App\Exports\ExcelExport;
use Illuminate\Support\Facades\URL;
use App\models\Admin\mparty\{MPartyModel, SymbolModel};
use App\models\Admin\StatepartyModel;
use App\models\Admin\StateSymbolModel;
use App\Helpers\LogNotification;
use App\models\Admin\OfficerModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use \PDF;
  use MPDF;
use Validator;
use Config;

class ACCeoController extends Controller
{

  public $commonModel;
  public $ceomodel;
  public $acceoreportModel;
  public $xssClean;
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {

    $this->middleware('adminsession');
    $this->middleware(['auth:admin', 'auth']);
    $this->middleware('ceo');

    $this->commonModel = new commonModel();
    $this->ceomodel = new ACCEOModel();
    $this->acceoreportModel = new ACCEOReportModel();
    $this->xssClean = new xssClean;
  }
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */

  protected function guard()
  {
    return Auth::guard();
  }


  public function dashboard(Request $request)
  {
    $data = [];
    if (Auth::check()) {
      $user = Auth::user();
      $uid = $user->id;
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $d = $this->commonModel->getunewserbyuserid($uid);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
      //dd($ele_details);
      $data['user_data'] = $d;
      $data['ele_details'] = $ele_details;
      $sched = '';
      $search = '';
      $status = $this->commonModel->allstatus();
      if (isset($ele_details) and ($ele_details)) {
        $i = 0;

        foreach ($ele_details as $ed) {
          $data['sched'] = getschedulebyid($ed->ScheduleID);

          $const_type = $ed->CONST_TYPE;
        }
      }
      if (isset($data['sched']) and ($data['sched'])) {
        return view('admin.ac.ceo.dashboard', $data);
      } else {

        $data['user_data'] = Auth::user();
        $data['totalparties'] = MPartyModel::countpartiesbytype();
        $data['national'] = MPartyModel::countpartiesbytype('N');
        $data['state'] = MPartyModel::countpartiesbytype('S');
        $data['unreconized'] = MPartyModel::countpartiesbytype('U');
        $st_code = Auth::user()->st_code;
        //dd("hello");
        $party = StatepartyModel::where('st_code', $st_code)->where('party_vname', '')->first();
        if (isset($party)) {
          Session::flash('error_messsage', 'enter Party Name vernacular');
          return redirect('/mparty/ceo/state-party-list');
        }
        $symb = StateSymbolModel::where('st_code', $st_code)->where('symbol_vname', '')->first();
        if (isset($symb)) {
          Session::flash('error_messsage', 'enter Symbol Name vernacular');
          return redirect('/mparty/ceo/symbol-list');
        }
        $filter = '';
        $filter = [
          'freesymbol' => '',
          'symbol_img' => '',
        ];
        $lists = SymbolModel::get_allsymbol($filter);
        $data['totalsymbol'] = count($lists);
        $filter = [
          'freesymbol' => 'PARTY',
          'symbol_img' => '',
        ];
        $lists = SymbolModel::symbolallotedtoparty($filter);
        $data['allotedtoparties'] = SymbolModel::symbolallotedtoparty();

        $filter = '';
        $filter = [
          'symbol_img' => '',
          'freesymbol' => 'T',
        ];
        $data['freesymbol'] = SymbolModel::countfreesymbol($filter); //count($lists);
        $filter = [
          'symbol_img' => '',
          'freesymbol' => 'F',
        ];
        $data['reservesymbol'] = SymbolModel::countfreesymbol($filter);

        return view('admin.ac.ceo.dashboardnonelection', $data);
      }
    } else {
      return redirect('/officer-login');
    }
  }   // end dashboard function
  public function edituser($eid = '', Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $uid = $user->id;
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $d = $this->commonModel->getunewserbyuserid($uid);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

      $rec = getById('officer_login', 'id', $eid);
      //dd($rec);   
      return view('admin.ac.ceo.updateprofile', ['user_data' => $d, 'offrecords' => $rec]);
    } else {
      return redirect('/officer-login');
    }
  }   // end dashboard function
  public function updateuser(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $uid = $user->id;
      $d = $this->commonModel->getunewserbyuserid($uid);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
      $this->validate(
        $request,
        [
          'name' => 'required',
          'email' => 'required|email',
          'Phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|numeric|digits:10',
         // 'address1' => 'required',
         // 'address2' => 'required',
         // 'zip' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|numeric|digits:6',
        ],
        [
          'name.required' => 'Please enter name',
          'email.required' => 'Please enter your email',
          'email.email' => 'Please enter valid email',
          'Phone_no.required' => 'Please enter validate mobileno',
          'Phone_no.min' => 'Mobile Number minimum 10 digit',
          'Phone_no.digits' => 'Mobile Number minimum 10 digit',
          'Phone_no.numeric' => 'Please enter validate mobileno',
         // 'zip.digits' => 'Zip Code minimum 6 digit',
          //'zip.numeric' => 'Please enter validate Zip Code',
          //'address1.required' => 'Please enter Address line1',
         // 'address2.required' => 'Please enter Address line2',
        ]
      );

      $id = $this->xssClean->clean_input(Check_Input($request->input('profileUpdate')));
      $name = $this->xssClean->clean_input(Check_Input($request->input('name')));
      $mobile = $this->xssClean->clean_input(Check_Input($request->input('Phone_no')));
      $email = $this->xssClean->clean_input(Check_Input($request->input('email')));
      $address1 = $this->xssClean->clean_input(Check_Input($request->input('address1')));
      $address2 = $this->xssClean->clean_input(Check_Input($request->input('address2')));
      $zip = $this->xssClean->clean_input(Check_Input($request->input('zip')));

      $date = Carbon::now();
      $currentTime = $date->format('Y-m-d H:i:s');
      $code = Hash::make(str_random(10));
      $mobile_otp = rand(100000, 999999);
      $rec = getById('officer_login', 'id', $id);
      $record = array(
        'name' => $name,
        //'password'=>'',
        'Phone_no' => $mobile,
        'email' => $email,
        //'mobile_otp' => $mobile_otp,
        //'otp_time' => $currentTime,
        //'auth_token' => $code,
        'ro_address_l1' => $address1,
        'ro_address_l2' => $address2,
        'ro_address_pin_code' => $zip,
      );
      OfficerModel::where('id', $id)->update($record);
	  
      //$encodeid = encrypt_string($id);
	 /*  $encodeid=base64_encode($id);
      $passcreaturl = URL::to("/updateprofile/$encodeid");
      $html = "Dear $name,\n\n";
      $html .= "Your account has been updated in Suvidha Portal"
        . "Your account must be activated before you use it. For activating your account and updating your particular, please click on the following link. Alternatively, you could copy and paste the link in your browser.\n\n";
      $html .= $passcreaturl . "\n\n";
      $html .= "OTP: " . $mobile_otp . "\n\n";
      $html .= "Login ID:  " . $rec->officername . "\n\n";
      $html .= "For verifying  your account,  kindly enter OTP " . $mobile_otp . " and this OTP has also sent on your registered mobile no.:\n\n";

      $html .= "Thanks & Regards,\n\n";
      $html .= "Suvidha Team,\n\n";

      $html = strip_tags($html);
    
	  
	  $headers = array();
	  $headers[] = 'From: Encore <no-reply@eci.gov.in>';
	  
	  
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


      if ($mobile != "") {
        $mob_message = "Dear Sir/Madam, your OTP is " . $mobile_otp . " and Login ID: " . $rec->officername . " for SUVIDHA Portal.Activation link has been sent on your email. Please enter that link and enter OTP to proceed. Do not share this OTP Team ECI";
        
        $response = SmsgatewayHelper::gupshup($mobile, $mob_message);
      } */
	  
	  
	  

      Session::flash('success_mes', 'officer profile updated successfully');  //
      return Redirect::to('/acceo/officer-details');
    } else {
      return redirect('/officer-login');
    }
  }   // end dashboard function
  public function showdashboard($cand_status = '', $constituency = '', $search = '')
  {
    //dd($request);
    $users = Session::get('admin_login_details');
    $user = Auth::user();
    if (session()->has('admin_login')) {
      $uid = $user->id;
      $d = $this->commonModel->getunewserbyuserid($uid);
      $edetails = $this->commonModel->election_details_cons($d->st_code, '', '', 'CEO');
      $sched = '';
      if ($cand_status == 'null') $cand_status = '';
      if ($constituency == 'null') $constituency = '';
      if ($search == 'null') $search = '';
      if (isset($edetails)) {
        $i = 0;
        foreach ($edetails as $ed) {
          $sched = $this->commonModel->getschedulebyid($ed->ScheduleID);
          $const_type = $ed->ConstType;
        }
      }

      $list1 = $this->ceomodel->Allcandidatelist($d, $cand_status, $search, $constituency, $const_type);
      // dd( $list1);
      $str = '';
      if (count($list1) > 0) {

        foreach ($list1 as $lis) {
          $s = $this->commonModel->getnameBystatusid($lis->application_status);
          if ($const_type == 'AC') {
            $const = $this->commonModel->getacbyacno($lis->st_code, $lis->ac_no);
            $const_name = $const->AC_NAME;
          } elseif ($const_type == 'PC') {
            $const = $this->commonModel->getallacbypcno($lis->st_code, $lis->pc_no);
            $const_name = $const->PC_NAME;
          }
          echo  $str .= "<tr><td>" . $lis->qrcode . "</td><td>" . $lis->cand_name . "</td> <td>" . $const_name . "</td><td>" . $s . "</td> </tr>";
        }
      } else {

        echo $str .= '<tr><td colspan="4" style="color:red; text-align:center;"><b>No Record Found.</b></td></tr>';
      }
    } else {
      return redirect('/officer-login');
    }
  }   // end dashboard function
  public function datewisereport(Request $request)
  {

    $users = Session::get('admin_login_details');
    $user = Auth::user();
    if (session()->has('admin_login')) {
      $uid = $user->id;
      $d = $this->commonModel->getunewserbyuserid($uid);
      $edetails = $this->commonModel->election_details_cons($d->st_code, '', '', 'CEO');
      $sched = '';
      $search = '';

      if (isset($edetails)) {
        $i = 0;
        foreach ($edetails as $ed) {
          $sched = $this->commonModel->getschedulebyid($ed->ScheduleID);
        }
      }
      $list = $this->ceomodel->electiondetailsbystatecode($d->st_code);
      $fromdate = date('d-m-Y');
      $todate = date('d-m-Y');
      $timeInterval = $fromdate . '~' . $todate;
      $fromdate = date('Y-m-d');
      $todate = date('Y-m-d');
      if (!empty($list)) {
        $i = 0;
        $allTypeCountArr = array();
        foreach ($list as $lis) {
          $i++;
          if ($lis->CONST_TYPE == 'AC') {
            $const = $this->commonModel->getacbyacno($lis->st_code, $lis->CONST_NO);
            $const_name = $const->AC_NAME;
          }
          if ($lis->CONST_TYPE == 'PC') {
            $const = $this->commonModel->getallacbypcno($lis->st_code, $lis->CONST_NO);
            $const_name = $const->PC_NAME;
          }


          $total = $this->ceomodel->gettotalnominationcnt($lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate); // ALL list
          $totw = $this->ceomodel->gettotalnominationcntbystatus('5', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $totr = $this->ceomodel->gettotalnominationcntbystatus('4', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $totacc = $this->ceomodel->gettotalnominationcntbystatus('6', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $totv = $this->ceomodel->gettotalnominationcntbystatus('2', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $totrec = $this->ceomodel->gettotalnominationcntbystatus('3', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $tota = $this->ceomodel->gettotalnominationcntbystatus('1', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          // $totfor=$this->ceomodel->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 

          $allTypeCountArr[$i]['const_no'] = $lis->CONST_NO;
          $allTypeCountArr[$i]['const_name'] = $const_name;
          $allTypeCountArr[$i]['total'] = $total;
          $allTypeCountArr[$i]['totalw'] = $totw;
          $allTypeCountArr[$i]['totalr'] = $totr;
          $allTypeCountArr[$i]['totalacc'] = $totacc;
          $allTypeCountArr[$i]['totalv'] = $totv;
          $allTypeCountArr[$i]['totalrec'] = $totrec;
          $allTypeCountArr[$i]['totala'] = $tota;
        }
      }

      // dd($allTypeCountArr);
      return view('admin.ceo.datewisereport', ['user_data' => $d, 'list_const' => $list, 'sched' => $sched, 'allTypeCountArr' => $allTypeCountArr, 'timeInterval' => $timeInterval]);
    } else {
      return redirect('/officer-login');
    }
  }   // end dashboard function
  public function datewisereport_range(Request $request)
  {

    $users = Session::get('admin_login_details');
    $user = Auth::user();
    if (session()->has('admin_login')) {
      $uid = $user->id;
      $d = $this->commonModel->getunewserbyuserid($uid);
      $edetails = $this->commonModel->election_details_cons($d->ST_CODE, '', '', 'CEO');
      $sched = '';
      $search = '';

      if (isset($edetails)) {
        $i = 0;
        foreach ($edetails as $ed) {
          $sched = $this->commonModel->getschedulebyid($ed->ScheduleID);
        }
      }
      $from_date = ($request->from_date);
      $to_date = ($request->to_date);
      $const = trim($request->const);
      $list = $this->ceomodel->electiondetailsbystatecode($d->ST_CODE, $const);

      $timeInterval = $from_date . '~' . $to_date;

      $fromdate = date('Y-m-d', strtotime($from_date));
      $todate = date('Y-m-d', strtotime($to_date));

      if (!empty($list)) {
        $i = 0;
        $allTypeCountArr = array();
        foreach ($list as $lis) {
          $i++;
          if ($lis->CONST_TYPE == 'AC') {
            $const = $this->commonModel->getacbyacno($lis->ST_CODE, $lis->CONST_NO);
            $const_name = $const->AC_NAME;
          }
          if ($lis->CONST_TYPE == 'PC') {
            $const = $this->commonModel->getallacbypcno($lis->ST_CODE, $lis->CONST_NO);
            $const_name = $const->PC_NAME;
          }


          $total = $this->ceomodel->gettotalnominationcnt($lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate); // ALL list
          $totw = $this->ceomodel->gettotalnominationcntbystatus('5', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $totr = $this->ceomodel->gettotalnominationcntbystatus('4', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $totacc = $this->ceomodel->gettotalnominationcntbystatus('6', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $totv = $this->ceomodel->gettotalnominationcntbystatus('2', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $totrec = $this->ceomodel->gettotalnominationcntbystatus('3', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          $tota = $this->ceomodel->gettotalnominationcntbystatus('1', $lis->CONST_TYPE, $lis->ST_CODE, $lis->CONST_NO, $fromdate, $todate);
          //$totfor=$this->ceomodel->gettotalnominationcntbystatus('formsubmited', $lis->CONST_TYPE,$lis->ST_CODE,$lis->CONST_NO, $fromdate, $todate); 

          $allTypeCountArr[$i]['const_no'] = $lis->CONST_NO;
          $allTypeCountArr[$i]['const_name'] = $const_name;
          $allTypeCountArr[$i]['total'] = $total;
          $allTypeCountArr[$i]['totalw'] = $totw;
          $allTypeCountArr[$i]['totalr'] = $totr;
          $allTypeCountArr[$i]['totalacc'] = $totacc;
          $allTypeCountArr[$i]['totalv'] = $totv;
          $allTypeCountArr[$i]['totalrec'] = $totrec;
          $allTypeCountArr[$i]['totala'] = $tota;
        }
      }

      $str = '';
      if (count($allTypeCountArr) > 0) {
        $i = 0;
        $totalag = 0;
        $totalvg = 0;
        $totalrecg = 0;
        $totalwg = 0;
        $totalaccg = 0;
        $totalrg = 0;
        $totalg = 0;

        foreach ($allTypeCountArr as $list) {

          $totalag = $totalag + $list['totala'];
          $totalvg = $totalvg + $list['totalv'];
          $totalrecg = $totalrecg + $list['totalrec'];
          $totalwg = $totalwg + $list['totalw'];
          $totalrg = $totalrg + $list['totalr'];
          $totalaccg = $totalaccg + $list['totalacc'];
          $totalg = $totalg + $list['total'];

          $str .= "<tr><td>" . $list['const_no'] . "-" . $list['const_name'] . "</td><td>" . $list['totala'] . "</td> <td>" . $list['totalv'] . "</td><td>" . $list['totalrec'] . "</td><td>" . $list['totalw'] . "</td><td>" . $list['totalr'] . "</td><td>" . $list['totalacc'] . "</td><td>" . $list['total'] . "</td> </tr>";
        }
        echo $str .= "<tr><td>Total:- </td><td>" . $totalag . "</td> <td>" . $totalvg . "</td><td>" . $totalrecg . "</td><td>" . $totalwg . "</td><td>" . $totalrg . "</td><td>" . $totalaccg . "</td><td>" . $totalg . "</td> </tr>";
      } else {

        echo $str .= '<tr><td colspan="7" style="color:red; text-align:center;"><b>No Record Found.</b></td></tr>';
      }
    } else {
      return redirect('/officer-login');
    }
  }   // end dashboard function 
  public function candidate_finalize(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $uid = $user->id;
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $d = $this->commonModel->getunewserbyuserid($uid);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
      $st_code = $ele_details[0]->ST_CODE;
      $list = $this->ceomodel->Allcandidate_finaliselist($st_code);

      return view('admin.ac.ceo.candidate-finalise', ['user_data' => $d, 'lists' => $list, 'st_code' => $st_code, 'ele_details' => $ele_details]);
    } else {
      return redirect('/officer-login');
    }
  }   // end candidate_finalize function
  public function candidate_definalize($ac_no, $actype)
  {

    if (Auth::check()) {
      $user = Auth::user();
      $uid = $user->id;
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $d = $this->commonModel->getunewserbyuserid($uid);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
      $st_code = $ele_details[0]->ST_CODE;

      $list = $this->ceomodel->get_candidate_finalizeac($st_code, $ac_no, $actype);

      $date = Carbon::now();
      $currentTime = $date->format('Y-m-d H:i:s');

      $otp = "123456"; //rand(100000,999999);
      $mob_message = "Dear Sir/Madam, your OTP is " . $otp . " for ECI Candidate Portal for de-finalized AC . Please enter the OTP to proceed.Your OTP will be valid till 30 minutes.Do not share this OTP,  Team ECI";

      $st = array('mobile_otp' => $otp, 'otp_time' => $currentTime);
      $i = DB::table('candidate_finalized_ac')->where('id', $list->id)->update($st);
      //$response = SmsgatewayHelper::sendOtpSMS($mob_message,$d->Phone_no); 

      return view('admin.ac.ceo.candidate-definalise', ['user_data' => $d, 'ac_no' => $ac_no, 'st_code' => $st_code, 'actype' => $actype, 'list' => $list, 'otp' => $otp, 'otp_time' => $currentTime, 'ele_details' => $ele_details]);
    } else {
      return redirect('/officer-login');
    }
  }   // end candidate_finalize function
  function definalizevalidation(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();

      $message = array();
      $message['MobNo'] = $user->officername ?? '';
      $message['applicationType'] = 'WebApp';
      $message['Module'] = 'ENCORE';
      $message['TransectionType'] = 'Definalize CEO';
      $message['TransectionAction'] = 'AC Definalize by CEO ';




      $d = $this->commonModel->getunewserbyuserid($user->id);
      $definalized_message = $this->xssClean->clean_input(Check_Input($request->input('definalized_message')));
      if (empty($definalized_message)) {
        Session::flash('error_messsage', 'Message empty');
        return Redirect::to('/acceo/candidate-finalize');
      }
      $id = $this->xssClean->clean_input(Check_Input($request->input('id')));
      $cons_no = $this->xssClean->clean_input(Check_Input($request->input('ac_no')));
      $st_code = $this->xssClean->clean_input(Check_Input($request->input('st_code')));
      $actype = $this->xssClean->clean_input(Check_Input($request->input('actype')));

      //   $this->validate(
      //         $request, 
      //             [
      //              //'verifyotp' => 'required|numeric',
      //              'definalized_message' => 'required',
      //              ],
      //             [
      //              'verifyotp.required' => 'Please enter your valid Otp', 
      //              'verifyotp.numeric' => 'Please enter your valid Otp',
      //              'definalized_message.required' => 'Please enter message',
      //              ]);
      //  $verifyotp = Check_Input($request->input('verifyotp'));
      //  $definalized_message = Check_Input($request->input('definalized_message'));
      //  $id = Check_Input($request->input('id'));
      //  $cons_no = Check_Input($request->input('ac_no'));
      //  $st_code = Check_Input($request->input('st_code'));
      //  $actype = Check_Input($request->input('actype'));
      // // $ELECTION_ID = Check_Input($request->input('ELECTION_ID'));
      //  $otp = Check_Input($request->input('otp'));
      //  $otp_time = Check_Input($request->input('otp_time'));

      //  $date = Carbon::now()->subMinutes(30);
      //       $currentTime = $date->format('Y-m-d H:i:s');

      //  if($otp!=$verifyotp) {
      //    Session::flash('ro_opt_messsage', 'Your Otp Message Invalide');
      //         return Redirect::to('/ceo/candidate-definalize/'.$cons_no.'/'.$actype);
      //  }
      // if($otp_time<$currentTime) {
      //    Session::flash('ro_opt_messsage', 'Your Otp time Expair');
      //          return Redirect::to('/ceo/candidate-definalize/'.$cons_no.'/'.$actype);
      //  }
      $ins_data = array(
        'finalized_ac' => '0',
        'definalized_message' => $definalized_message,
        'definalize_date' => date('Y-m-d')
      );
      $this->ceomodel->definalize_candidate_ac($st_code, $cons_no, $actype, $ins_data);
      if (config('public_config.nomination_log')) {
        $message['LogDescription'] = 'Definalize State-code: ' . $st_code . ' Const_no: ' . $cons_no;
        $message['TransectionStatus'] = 'SUCCESS';

        LogNotification::LogInfo($message);
      }
      Session::flash('success_mes', 'De-finalize Successfully');
      return Redirect::to('/acceo/candidate-finalize');
    } else {
      return Redirect::to('/officer-login');
    }
  }


  public function electorspollingstationList(Request $request)
  {
    //dd($request->all());
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
      $all_pc = $this->commonModel->getpcbystate($d->st_code);
      return view('admin.ac.ceo.electors-pollingstationlist', ['user_data' => $d, 'ele_details' => $ele_details, 'all_pc' => $all_pc]);
    } else {
      return redirect('/officer-login');
    }
  }   // end electorspollingstation List function

  public function getaclistbyPC(request $request)
  {
    //dd($request->all()); 

    $pc_no = $request['pc_no'];

    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
      $election_id = $ele_details[0]->ELECTION_ID;
      // dd($election_id);
      //dd($ele_details);
      if ($pc_no != 90) {
        // $election_id=$ele_details[$request->pc_no-1]->ELECTION_ID;
        // $CONST_TYPE=$ele_details[$request->pc_no-1]->CONST_TYPE;
        $acdata = $this->acceoreportModel->getAcByPC($d->st_code, $pc_no, $election_id);
      } else {
        foreach ($ele_details as $ele_detailsList) {
          // $election_id=$ele_detailsList->ELECTION_ID;
        }
        $electorSummary = $this->acceoreportModel->getelectorssummarybyState($d->st_code, $election_id);
      }
      //dd($acdata);
      $html = '';
      $j = 0;
      if (!empty($acdata)) {
        $html .= '<thead>
<tr>
<th colspan="3"> AC No & AC Name </th>
<th colspan="4">General Electors</th>
<th colspan="4">Service Electors</th>
<th colspan="3">Polling Stations</th>
</tr>

<tr>
<th size="2">S.No.</th>
<th>AC No</th>
<th>AC Name</th>
<th size="2">Male</th>
<th size="2">Female</th>
<th size="2">Third Gender</th>
<th size="2">Total</th>

<th size="2">Male</th>
<th size="2">Female</th>
<th size="2">Third Gender</th>
<th size="2">Total</th>

<th size="2">Regular</th>
<th size="2">Auxillary</th>
<th size="2">Total</th>
</tr>
</thead>';

        foreach ($acdata as $acdataList) {
          $j++;
          $html .= '<input type="hidden" name="pc_no" value="' . $acdataList->pc_no . '">
<input type="hidden" name="st_code" value="' . $acdataList->st_code . '">';
          $html .= '<tr>
<td><input type="hidden"   name=""  value="' . $j . '"  maxlength="5" readonly="readonly" size="2"><span>' . $j . '</span></td> 
<td><input type="hidden"   name="ac_no[]"  value="' . $acdataList->ac_no . '"  maxlength="8" readonly="readonly"><span>' . $acdataList->AC_NO . '</span></td> 
<td><input type="hidden"  name="ac_name[]"  value="' . $acdataList->AC_NAME . '" maxlength="8"  readonly="readonly"><span>' . $acdataList->AC_NAME . '</span></td> 
<td><input type="text"    name="gen_male[]" id="gen_male" value="' . $acdataList->gen_m . '"   size="7" readonly="readonly"></td> 
<td><input type="text"    name="gen_female[]" id="gen_female" value="' . $acdataList->gen_f . '"  size="7" readonly="readonly"> </td>         
<td><input type="text"    name="gen_third[]" id="gen_third" value="' . $acdataList->gen_o . '" size="7"  readonly="readonly"> </td>          
<td><input type="text"   name="gen_total[]" id="gen_total" value="' . $acdataList->gen_t . '" size="7"  readonly="readonly"> </td>  

<td><input type="text" name="ser_male[]" id="ser_male" value="' . $acdataList->ser_m . '" size="7"   readonly="readonly"> </td> 
<td><input type="text" name="ser_female[]" id="ser_female" value="' . $acdataList->ser_f . '" size="7"   readonly="readonly"> </td>          
<td><input type="text" name="ser_third[]" id="ser_third" value="' . $acdataList->ser_o . '" size="7" readonly="readonly"> </td> 
<td><input type="text" name="ser_total[]" id="ser_total" value="' . $acdataList->ser_t . '" size="7" readonly="readonly"> </td> 

<td><input type="text" name="regular[]" id="regular" value="' . $acdataList->polling_reg . '" size="7" readonly="readonly"> </td> 
<td><input type="text" name="auxillary[]" id="auxillary" value="' . $acdataList->polling_auxillary . '" size="7"   readonly="readonly"> </td> 
<td><input type="text" name="polling_total[]" id="polling_total" value="' . $acdataList->polling_total . '" size="7"  readonly="readonly"></span> </td> 
</tr>';
        }
      } elseif (!empty($electorSummary)) {
        # code...
        $html .= '<thead>
<tr>
<th colspan="3"> PCNo & PC Name </th>
<th colspan="4">General Electors</th>
<th colspan="4">Service Electors</th>
<th colspan="3">Polling Stations</th>
</tr>

<tr>
<th size="2">S.No.</th>
<th>PC No</th>
<th>PC Name</th>
<th size="2">Male</th>
<th size="2">Female</th>
<th size="2">Third Gender</th>
<th size="2">Total</th>

<th size="2">Male</th>
<th size="2">Female</th>
<th size="2">Third Gender</th>
<th size="2">Total</th>

<th size="2">Regular</th>
<th size="2">Auxillary</th>
<th size="2">Total</th>
</tr>
</thead>';
        foreach ($electorSummary as $acdataSummary) {
          $j++;
          $html .= '<input type="hidden" name="pc_no" value="' . $acdataSummary->PC_NO . '">
  <input type="hidden" name="st_code" value="">';
          $html .= '<tr>
<td><input type="hidden"   name=""  value="' . $j . '"  maxlength="5" readonly="readonly" size="2"><span>' . $j . '</span></td> 
<td><input type="hidden"   name="pc_no[]"  value="' . $acdataSummary->PC_NO . '"  readonly="readonly"><span>' . $acdataSummary->PC_NO . '</span></td> 
<td><input type="hidden"  name="pc_name[]"  value="' . $acdataSummary->PC_NAME . '"   readonly="readonly"><span>' . $acdataSummary->PC_NAME . '</span></td> 
<td><input type="text"    name="gen_male[]" id="gen_male" value="' . $acdataSummary->total_gen_m . '" size="7" readonly="readonly"></td> 
<td><input type="text"    name="gen_female[]" id="gen_female" value="' . $acdataSummary->total_gen_f . '" size="7" readonly="readonly"> </td>         
<td><input type="text"    name="gen_third[]" id="gen_third" value="' . $acdataSummary->total_gen_o . '" size="7"  readonly="readonly"> </td>          
<td><input type="text"   name="gen_total[]" id="gen_total" value="' . $acdataSummary->total_gen_t . '" size="7"  readonly="readonly"> </td>  

<td><input type="text" name="ser_male[]" id="ser_male" value="' . $acdataSummary->total_ser_m . '" size="7"   readonly="readonly"> </td> 
<td><input type="text" name="ser_female[]" id="ser_female" value="' . $acdataSummary->total_ser_f . '" size="7"  readonly="readonly"> </td>          
<td><input type="text" name="ser_third[]" id="ser_third" value="' . $acdataSummary->total_ser_o . '" size="7"   readonly="readonly"> </td> 
<td><input type="text" name="ser_total[]" id="ser_total" value="' . $acdataSummary->total_ser_t . '" size="7"  readonly="readonly"> </td> 

<td><input type="text" name="regular[]" id="regular" value="' . $acdataSummary->total_polling_reg . '" size="7"  readonly="readonly"> </td> 
<td><input type="text" name="auxillary[]" id="auxillary" value="' . $acdataSummary->total_polling_auxillary . '" size="7"   readonly="readonly"> </td> 
<td><input type="text" name="polling_total[]" id="polling_total" value="' . $acdataSummary->total_polling_total . '" size="7" readonly="readonly"></span> </td> 
</tr>';
        }
      }
      return $html;
    }
  }
  public function changepassword(request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

      return view('admin.ac.ceo.change-password', ['user_data' => $d, 'ele_details' => $ele_details]);
    }
  } //@end changepassword function


  public function changePasswordStore(request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      // $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
      //dd($user);
      if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
        // The passwords matches
        return redirect()->back()->with("error", "Your current password does not matches with the password you provided. Please try again.");
      }
      if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
        //Current password and new password are same
        return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password.");
      }
      $validatedData = $request->validate([
        'current-password' => 'required',
        'new-password' => 'required|string|min:8|required_with:new-password-confirm|same:new-password-confirm',
        'new-password-confirm' => 'required|string|min:8',
      ]);
      //Change Password
      $user = Auth::user();
      $user->password = bcrypt($request->get('new-password'));
      $user->save();
      return redirect()->back()->with("success", "Password changed successfully !");
    } //@end Auth::check()

  } //@end changePasswordStore function




  public function officerList(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

      $officerlist = OfficerModel::where('st_code', $d->st_code)->whereIn('role_id', [5, 19])->get();
      return view('admin.ac.ceo.officer-details', ['user_data' => $d, 'ele_details' => $ele_details, 'officerlist' => $officerlist]);
    } else {
      return redirect('/officer-login');
    }
  }

  public function officerListExcel(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $officerlist = OfficerModel::where('st_code', $user->st_code)->whereIn('role_id', [5, 19])->get()->map(function($item){
        return  [
          $item->officername,
          $item->designation,
          $item->placename,
          $item->name,
          $item->email,
          ' '.(string)$item->Phone_no,
          ($item->password) ? 'Yes' : 'No',
        ];
      });
      $headings=['User Id', 'Designation', 'Place', 'Officer Name', 'Email', 'Mobile', 'Account Activated'];
      return Excel::download(new ExcelExport($headings, $officerlist), 'Officer_list_'.$user->st_code.'_'.date('d-m-Y').'_'.time().'.xlsx');
    } else {
      return redirect('/officer-login');
    }
  }


  public function officerProfileUpdate(Request $request, $id = '')
  {

    //  dd($request->all());

    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

      if (!empty($_POST['profileUpdate'])) {
        // dd($request);
        $validator = $this->validate(
          $request,
          [
            'name' => 'required',
            'email' => 'required',
            'Phone_no' => 'required|string|min:10|numeric|digits:10|unique:officer_login',

          ],
          [
            'name.required' => 'Please enter your name',
            'email.required' => 'Please enter your email',
            'Phone_no.required' => 'Please enter mobile number',
            'Phone_no.digits' => 'Please enter 10 digit mobile number',
            'Phone_no.unique' => 'This mobile number already exist',
          ]
        );

        // if ($validator->passes()) {
        if ($validator) {
          if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['Phone_no'])) {

            $name =  strip_tags($_POST['name']);
            $email =  strip_tags($_POST['email']);
            $Phone_no = strip_tags($_POST['Phone_no']);
            // $Phone_no = $this->xssClean($_POST['profileUpdate']);
            $officerdata = array(
              'name' => $name,
              'email' => $email,
              'Phone_no' => $Phone_no,
              /*'modified_by' => $d->id,*/
              'added_update_at' => date('Y-m-d'),
              'updated_at' => date('Y-m-d H:i:s')
            );
            // dd($officerdata);
            $where = array('id' => $_POST['profileUpdate']);
            OfficerModel::where($where)->update($officerdata);

            Session::flash('success_success', 'You have Successfully Updated!. ');
            // return redirect()->back();
            return redirect('/acceo/officer-details');
          }
        } else {
          Session::flash('success_error', 'You have some Error!. ');
          return redirect('/acceo/officer-details');
          //  return redirect()->back()->withErrors($validator, 'error');
        }
      } else {
        $decryptedid = decrypt($id);
        $rec = getById('officer_login', 'id', $decryptedid);
        return view('admin.ac.ceo.officer-profile')->with(array('user_data' => $d, 'getofficerdetails' => $rec, 'ele_details' => $ele_details));
      }
    } else {
      return redirect('/officer-login');
    }
  }



  public function psinfoList(Request $request)
  {
    //dd($request->all());

    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
      $all_state = $this->commonModel->getallstate();
      $all_dist = $this->commonModel->getalldistrictbystate($d->st_code);
      $all_ac = $this->commonModel->getacbystate($d->st_code);
      // $officerlist =DB::table('officer_login')->where('st_code',$d->st_code)->get();
      // print_r($officerlist);  die;
      return view('admin.ac.ceo.psinfo', ['user_data' => $d, 'ele_details' => $ele_details, 'all_state' => $all_state, 'all_dist' => $all_dist, 'all_ac' => $all_ac]);
    } else {
      return redirect('/officer-login');
    }
  }   // end candidateListbyPC function  


  public function getaclist(request $request)
  {
    //dd($request->all());
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);

      $district = $request->input('district');
      $stcode = $d->st_code;
      $acdata = $this->commonModel->getAcByst($stcode, $district);
    }
    return $acdata;
  }

  public function psresultList(Request $request)
  {
    //dd($request->all());
    $url = 'http://eronetservices.ecinet.in/api/ERONet/GetPSDetailsAcWise';
    $st_code = $request->st_code;
    $ac_no = $request->ac;
    // $st_code='S11';
    //$ac_no='2';
    //$secureKey = "ABCD1234#123521GISTECIKEY";
    $method = 'POST';
    $resultData = $this->acceoreportModel->ComputeSha512Hash($st_code, $ac_no);
    // dd($resultData);
    $data = array(
      "ST_CODE" => $st_code,
      "ac_no" => $ac_no,
      "Client_HASHCode" => $resultData,
    );
    $data_string = json_encode($data);
    $jsonResult = $this->acceoreportModel->callAPI($method, $url, $data_string);
    $dist_no = $request->district;
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);


      return view('admin.ac.ceo.psinfo', ['user_data' => $d, 'dist_no' => $dist_no, 'ac_no' => $ac_no, 'st_code' => $st_code, 'jsonResult' => $jsonResult, 'ele_details' => $ele_details]);
    } else {
      return redirect('/officer-login');
    }
  }   // end candidateListbyPC function  


  // download cantesting candidate
  public function download_contesting_candidate($cons_no)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $st = $d->st_code;

      $candn = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
        ->where('candidate_nomination_detail.st_code', '=', $st)->where('candidate_nomination_detail.ac_no', '=', $cons_no)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        //->where('candidate_nomination_detail.finalize','=','1')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        //->where('m_party.PARTYTYPE','=','N')
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_image', 'candidate_personal_detail.candidate_residence_address', 'candidate_nomination_detail.*', 'm_party.PARTYNAME', 'm_party.PARTYABBRE', 'm_party.PARTYTYPE', 'm_symbol.SYMBOL_DES', 'candidate_personal_detail.candidate_residence_address', 'candidate_personal_detail.candidate_residence_stcode', 'candidate_personal_detail.candidate_residence_districtno', 'candidate_personal_detail.candidate_residence_acno')->get();

      $a = 'N';
      $a1 = 'S';
      $cands = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
        ->where('candidate_nomination_detail.st_code', '=', $st)->where('candidate_nomination_detail.ac_no', '=', $cons_no)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->where('candidate_nomination_detail.finalize','=','1')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where(function ($query1) use ($a, $a1) {
          $query1->where('candidate_nomination_detail.cand_party_type', '=', $a)
            ->orWhere('candidate_nomination_detail.cand_party_type', '=', $a1);
        })
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_image','candidate_personal_detail.candidate_residence_address', 'candidate_nomination_detail.*', 'm_party.PARTYNAME', 'm_party.PARTYABBRE', 'm_party.PARTYTYPE', 'm_symbol.SYMBOL_DES', 'candidate_personal_detail.candidate_residence_address', 'candidate_personal_detail.candidate_residence_stcode', 'candidate_personal_detail.candidate_residence_districtno', 'candidate_personal_detail.candidate_residence_acno')->get();


      $candu = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
        ->where('candidate_nomination_detail.st_code', '=', $st)->where('candidate_nomination_detail.ac_no', '=', $cons_no)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
        ->where('candidate_nomination_detail.finalize','=','1')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_nomination_detail.cand_party_type', '=', 'U')
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_image', 'candidate_personal_detail.candidate_residence_address', 'candidate_nomination_detail.*', 'm_party.PARTYNAME', 'm_party.PARTYABBRE', 'm_party.PARTYTYPE', 'm_symbol.SYMBOL_DES', 'candidate_personal_detail.candidate_residence_address', 'candidate_personal_detail.candidate_residence_stcode', 'candidate_personal_detail.candidate_residence_districtno', 'candidate_personal_detail.candidate_residence_acno')->get();

      $candz = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')
        ->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
        ->where('candidate_nomination_detail.st_code', '=', $st)->where('candidate_nomination_detail.ac_no', '=', $cons_no)
        ->where('candidate_nomination_detail.application_status', '=', '6')
        ->where('candidate_nomination_detail.finalaccepted', '=', '1')
        ->where('candidate_nomination_detail.symbol_id', '<>', '200')
         ->where('candidate_nomination_detail.finalize','=','1')
        ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
        ->where('candidate_nomination_detail.cand_party_type', '=', 'Z')
        ->orderBy('candidate_nomination_detail.new_srno', 'asc')
        ->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_image','candidate_personal_detail.candidate_residence_address', 'candidate_nomination_detail.*', 'm_party.PARTYNAME', 'm_party.PARTYABBRE', 'm_party.PARTYTYPE', 'm_symbol.SYMBOL_DES', 'candidate_personal_detail.candidate_residence_address', 'candidate_personal_detail.candidate_residence_stcode', 'candidate_personal_detail.candidate_residence_districtno', 'candidate_personal_detail.candidate_residence_acno')->get();


      $ac = '';

      $ac = getacbyacno($st, $cons_no);
      $state = getstatebystatecode($st);

      view()->share('candn', $candn, 'cands', $cands, 'candu', $candu, 'candz', $candz, 'st', $state, 'ac', $ac);
      /*$pdf = Mpdf::loadView('admin.cantesting-candidate', compact('candn', $candn, 'cands', $cands, 'candu', $candu, 'candz', $candz, 'state', $state, 'ac', $ac)); */
      $pdf = Mpdf::loadView('admin.cantesting-candidate', compact('candn','cands','candu','candz','state','ac'));
      return $pdf->download('cantesting-candidate.pdf');

      return view('cantesting-candidate');
    } else {
      return redirect('/officer-login');
    }
  }



       
/* Criminal Report */










          public function contested_criminal_report(request $request)
          {

            //dd($request->input('nomid'));
            if(Auth::check()){ 
               $data  = [];
                  $user = Auth::user();
                  $d=$this->commonModel->getunewserbyuserid($user->id); 
                //  dd($d);
                   $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
                   //dd($d);
               // $record = DB::table('m_election_details')->where('ST_CODE', $ele_details->ST_CODE)->where('CONST_NO', $ele_details->CONST_NO)
               //            ->where('CONST_TYPE', 'AC')->first(); 


                  // $Schedule_details=$this->commonModel->getschedulebyid($record->ScheduleID);
                     if(!empty($request->receivefilter) && $request->receivefilter==1) { 
                       $bal='1'; 
                      $v='candidate_criminal_report.status'; $m=$bal; 
                     
                    }
                elseif(!empty($request->receivefilter) && $request->receivefilter==2) { 
                      $bal='0'; 
                     
                     $v='candidate_criminal_report.status'; $m=$bal;
                   }
                    

                 
                   if(!empty($request->ac) && !empty($request->receivefilter)){
                   $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
            ->where('candidate_nomination_detail.ac_no','=', $request->ac)
              ->where($v,'=',$m)
             ->where('candidate_nomination_detail.election_id','=',$d->election_id)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_criminal_report.nom_id','candidate_criminal_report.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_1_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_3_date'
            )->get(); 
           }elseif(!empty($request->ac) || !empty($request->receivefilter)){
           

                if(isset($request->ac) && !empty($request->ac)){     $v='candidate_nomination_detail.ac_no'; $m=$request->ac; }


              
                if(!empty($request->receivefilter) && $request->receivefilter==1) { 
                       $bal='1'; 
                      $v='candidate_criminal_report.status'; $m=$bal; 
                     
                    }
                elseif(!empty($request->receivefilter) && $request->receivefilter==2) { 
                      $bal='0'; 
                     
                     $v='candidate_criminal_report.status'; $m=$bal;
                   }

                 $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
           // ->where('candidate_nomination_detail.ac_no','=', $request->ac)
              ->where($v,'=',$m)
             ->where('candidate_nomination_detail.election_id','=',$d->election_id)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_criminal_report.nom_id','candidate_criminal_report.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_1_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_3_date'  )->get(); 


           }else{
                    
                    $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
            //->where('candidate_nomination_detail.ac_no','=', $ele_details->CONST_NO)
        
             ->where('candidate_nomination_detail.election_id','=',$d->election_id)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_criminal_report.nom_id','candidate_criminal_report.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_1_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_3_date'
            )->get(); 



           }
           
               $data['list']=$lists;
               $data['user_data']=$user;
            $data['nomidone'] = $request->input('nomid');

                //dd($data);
       
               }
               return view('admin.ac.ceo.ca_report_list', $data);
                // return view($this->view_path.'.ca_report_list', $data);
               
                

    }



    public function contesting_candidate_list_pdf($ac_no,$sts)
    {
     // print_r($ac_no);echo "----";print_r($sts);die();
    //$sts='';
           if(Auth::check()){ 
               $data  = [];
                  $user = Auth::user();
                  $d=$this->commonModel->getunewserbyuserid($user->id); 
                   //$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
             //  $record = DB::table('m_election_details')->where('ST_CODE', $ele_details->ST_CODE)->where('CONST_NO', $ele_details->CONST_NO)
                         // ->where('CONST_TYPE', 'AC')->first(); 
 $ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);


                if(!empty($sts) && $sts==1) { 
                       $bal='1'; 
                      $v='candidate_criminal_report.status'; $m=$bal; 
                     
                    }
                elseif(!empty($sts) && $sts==2) { 
                      $bal='0'; 
                     
                     $v='candidate_criminal_report.status'; $m=$bal;
                   }
                    

                 
                   if(!empty($ac_no) && !empty($sts)){


//if(isset($ac_no) && $ac_no > 0){
                   $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
            ->where('candidate_nomination_detail.ac_no','=', $ac_no)
              ->where($v,'=',$m)
             ->where('candidate_nomination_detail.election_id','=',$d->election_id)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_criminal_report.nom_id','candidate_criminal_report.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_1_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_3_date'
            )->get(); 
           }elseif(!empty($ac_no) || !empty($sts)){
            
                if(isset($ac_no) && !empty($ac_no)){     $v='candidate_nomination_detail.ac_no'; $m=$ac_no; }


              
                if(isset($sts) && (!empty($sts) && $sts==1)) { 
                       $bal='1'; 
                      $v='candidate_criminal_report.status'; $m=$bal; 
                     
                    }
                elseif(isset($sts) && (!empty($sts) && $sts==2)) { 
                      $bal='0'; 
                     
                     $v='candidate_criminal_report.status'; $m=$bal;
                   }

                   $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
           // ->where('candidate_nomination_detail.ac_no','=', $ac_no)
            ->where($v,'=',$m)
             ->where('candidate_nomination_detail.election_id','=',$d->election_id)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
                ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_criminal_report.nom_id','candidate_criminal_report.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_1_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_3_date'
            )->get(); 


           }else{


                    
                    $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
            //->where('candidate_nomination_detail.ac_no','=', $ele_details->CONST_NO)
        
             ->where('candidate_nomination_detail.election_id','=',$d->election_id)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_criminal_report.nom_id','candidate_criminal_report.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_1_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_3_date'
            )->get(); 



           }
               $data['list']=$lists;
               $data['user_data']=$user;
               $data['ceo']=1;
               //dd($data);

             //  $pdf = Mpdf::loadView('admin.ac.ro.criminalpdfview', compact('user',$user,'lists',$lists));
     // return $pdf->download('contesting-criminal-candidates.pdf');

        $pdf = Mpdf::loadView('admin.ac.ro.criminalpdfview', compact('user','lists'));
       return $pdf->download('contesting-criminal-candidates.pdf');
       

       // $pdf = PDF::loadView('admin.ac.ro.criminalpdfview',compact(['user','lists']));
       //          return $pdf->download('contesting-criminal-candidates.pdf');
       
         // $pdf = PDF::loadView('admin.ac.ro.criminalpdfview',compact('user',$user,'lists',$lists));
         //        return $pdf->download('contesting-criminal-candidates.pdf');
               }         

       
        //AC NOMINATION FINALIZED PDF REPORT TRY CATCH ENDS HERE



    }

    public function contesting_candidate_list_excel($ac_no,$sts)
    {




              if(Auth::check()){ 
               $data  = [];
                  $user = Auth::user();
                  $d=$this->commonModel->getunewserbyuserid($user->id); 
                 //  $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
               // $record = DB::table('m_election_details')->where('ST_CODE', $ele_details->ST_CODE)->where('CONST_NO', $ele_details->CONST_NO)
               //            ->where('CONST_TYPE', 'AC')->first(); 
$ele_details = $this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);

                  if(!empty($sts) && $sts==1) { 
                       $bal='1'; 
                      $v='candidate_criminal_report.status'; $m=$bal; 
                     
                    }
                elseif(!empty($sts) && $sts==2) { 
                      $bal='0'; 
                     
                     $v='candidate_criminal_report.status'; $m=$bal;
                   }
                    

                 
                   if(!empty($ac_no) && !empty($sts)){


//if(isset($ac_no) && $ac_no > 0){
                   $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
            ->where('candidate_nomination_detail.ac_no','=', $ac_no)
              ->where($v,'=',$m)
             ->where('candidate_nomination_detail.election_id','=',$d->election_id)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_criminal_report.nom_id','candidate_criminal_report.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_1_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_3_date'
            )->get(); 
           }elseif(!empty($ac_no) || !empty($sts)){
            
                if(isset($ac_no) && !empty($ac_no)){     $v='candidate_nomination_detail.ac_no'; $m=$ac_no; }


              
                if(isset($sts) && (!empty($sts) && $sts==1)) { 
                       $bal='1'; 
                      $v='candidate_criminal_report.status'; $m=$bal; 
                     
                    }
                elseif(isset($sts) && (!empty($sts) && $sts==2)) { 
                      $bal='0'; 
                     
                     $v='candidate_criminal_report.status'; $m=$bal;
                   }

                   $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
           // ->where('candidate_nomination_detail.ac_no','=', $ac_no)
            ->where($v,'=',$m)
             ->where('candidate_nomination_detail.election_id','=',$d->election_id)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
                ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_criminal_report.nom_id','candidate_criminal_report.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_1_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_3_date'
            )->get(); 


           }else{


                    
                    $lists = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->leftjoin('candidate_criminal_report', 'candidate_nomination_detail.candidate_id', '=', 'candidate_criminal_report.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$d->st_code)
            //->where('candidate_nomination_detail.ac_no','=', $ele_details->CONST_NO)
        
             ->where('candidate_nomination_detail.election_id','=',$d->election_id)
            ->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
            ->where('candidate_nomination_detail.application_status','=','6')
            ->where('candidate_nomination_detail.finalaccepted','=','1')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
               ->where('candidate_personal_detail.is_criminal','=','1')
               ->where('candidate_criminal_report.finalaccept_ca','=','1')
            //->Where('candidate_personal_detail.cand_name', 'like', '%'.$search.'%')
            //->orderBy('candidate_nomination_detail.new_srno', 'ASC') 
             ->select('candidate_criminal_report.nom_id','candidate_criminal_report.candidate_id','finalaccepted',
            'candidate_nomination_detail.party_id','candidate_nomination_detail.symbol_id',
            'candidate_nomination_detail.election_id','candidate_nomination_detail.ac_no','candidate_nomination_detail.st_code','candidate_nomination_detail.pc_no','candidate_nomination_detail.election_id',
            'candidate_nomination_detail.date_of_submit','candidate_nomination_detail.application_status',
            'candidate_personal_detail.cand_name','candidate_personal_detail.cand_hname',
            'candidate_criminal_report.check_1','candidate_criminal_report.candidate_id as CA_Report_candid','candidate_criminal_report.check_2','candidate_criminal_report.check_3','candidate_criminal_report.check_1_date','candidate_criminal_report.check_2_date','candidate_criminal_report.check_3_date'
            )->get(); 



           }
               $data['list']=$lists;
               $data['user_data']=$user;
              // dd($lists);
               $k=1;
               $status='pending';
                $export_data = [];
                $headings[] = [];
               
               
                   $export_data[] = ['SN', 'Candidate Name', 'NominationID','State','AC Name' ,'1st Publication','1st Publication Date', '2nd Publication','2nd Publication Date','3rd Publication','3rd Publication Date','Publication Status'];
             
                 foreach ($lists as $lis) {  
                  if( $lis->check_1==1){ $check1='Yes';}else{$check1='No';}
                  if( $lis->check_2==1){ $check2='Yes';}else{$check2='No';}
                  if( $lis->check_3==1){ $check3='Yes';}else{$check3='No';}

                   if(!empty($lis->check_1_date)){$check1_date=date('d-m-Y',strtotime($lis->check_1_date));}else{$check1_date="N/A"; }
                if(!empty($lis->check_2_date)){$check2_date=date('d-m-Y',strtotime($lis->check_2_date));}else{$check2_date="N/A"; }
                if(!empty($lis->check_3_date) ){$check3_date=date('d-m-Y',strtotime($lis->check_3_date));}else{$check3_date="N/A"; }

                
                   if($lis->check_1 == 1 && $lis->check_2 == 1 && $lis->check_3 == 1)
                  {
                    $status_is="Completed";
                  }else{
                    $status_is="Pending";
                  }
                  $st=getstatebystatecode($lis->st_code); 
                  $ac=getacname($lis->st_code,$lis->ac_no);
                if(isset($st))   $st_name=$st->ST_NAME; 
                if(isset($ac))  $ac_name=$ac->AC_NAME;  

                 $export_data[] = [
                                $k++,
                             $lis->cand_name,
                                $lis->nom_id,
                                $st_name,
                                 $ac_name,
                                $check1,
                                 $check1_date,
                                $check2,
                                 $check2_date,
                                $check3,
                                 $check3_date,
                                $status_is,
                                
                        ];
                   
                }
                $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], "Nomination_report"));
                    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

}


    }
      













/* End Criminal Report */































  //end 
}  // end class