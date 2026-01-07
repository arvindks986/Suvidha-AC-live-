<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use \PDF;
use App\commonModel;
use App\adminmodel\ACCEOModel;
use App\adminmodel\ACCEOReportModel;

date_default_timezone_set('Asia/Kolkata');

use App\Http\Controllers\Admin\turnout\MissingTurnoutController;
use App\Classes\xssClean;
use App\Classes\secureCode;
use App\Http\Traits\CommonTraits;
use App\Exports\ExcelExport;
use App\Helpers\LogNotification;
use App\models\AC;
use App\models\Admin\ElectorModel;
use App\models\Admin\EndOfPollFinaliseModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\polling_station\ExemptedAcWithPollingstationCheckModel;
use App\models\Admin\PollingStation;
use App\models\Admin\turnout\TurnoutModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class PCCeoReportNewController extends Controller
{

  //USING TRAIT FOR COMMON FUNCTIONS
  use CommonTraits;
  /**
   * Create a new controller instance.
   *
   * @return void
   */

  public $commonModel = null;
  public $ceomodel = null;
  public $pcceoreportModel = null;
  public $MissingTurnoutModel = null;
  public $turnout = null;

  public function __construct()
  {
    //date_default_timezone_set('Asia/Kolkata');    
    $this->middleware(['auth:admin', 'auth']);
    // $this->middleware('ceo');
    $this->commonModel = new commonModel();
    $this->ceomodel = new ACCEOModel();
    $this->pcceoreportModel = new ACCEOReportModel();
    $this->MissingTurnoutModel = new MissingTurnoutController;
    $this->turnout = new TurnoutModel();
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


  //PC CEO COUNTING RESULT DATA REPORT STARTS
  public function CountingStatus(Request $request)
  {
    //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code = $user_data->st_code;
        $st_name = $user_data->placename;


        $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
                                          IF(lead_cand_name!='null','STARTED','NOT STARTED') AS counting ,
                                          IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
                                          FROM winning_leading_candidate w RIGHT JOIN m_ac a ON w.st_code=a.ST_CODE 
                                          AND w.ac_no=a.AC_NO RIGHT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 WHERE a.ST_CODE='" . $st_code . "' AND e.`election_id`=" . $user->election_id . "";

        $CountingStatus = DB::select($PcCeoCountingSelectData);


        return view('admin.ac.ceo.ceo_counting.CountingStatus', ['user_data' => $user_data, 'CountingStatus' => $CountingStatus]);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK ENDS

  }
  //PC CEO COUNTING RESULT DATA REPORT FUNCTION ENDS


  //PC CEO COUNTING RESULT DATA EXCEL REPORT STARTS
  public function CountingStatusExcel(Request $request)
  {
    //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code = $user_data->st_code;
        $st_name = $user_data->placename;

        $export_data[] = ['AC No', 'AC Name', 'Counting Status', 'Result Status'];
        $headings[] = [];
        $user = Auth::user();
        $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
              IF(lead_cand_name!='null','STARTED','NOT STARTED') AS counting ,
              IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
              FROM winning_leading_candidate w RIGHT JOIN m_ac a ON w.st_code=a.ST_CODE 
              AND w.ac_no=a.AC_NO RIGHT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 WHERE a.ST_CODE='" . $st_code . "' AND e.`election_id`=" . $user->election_id . "";

        $PcCeoCountingData = DB::select($PcCeoCountingSelectData);

        foreach ($PcCeoCountingData as $CountingData) {

          $export_data[] = [
            $CountingData->ano,
            $CountingData->aac,
            $CountingData->counting,
            $CountingData->res_declare,



          ];
        }

        $name_excel = 'CountingStatus_' . trim($st_name) . '_' . $cur_time;
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');


        Excel::create('CountingStatus_' . trim($st_name) . '_' . $cur_time, function ($excel) use ($st_code) {
          $excel->sheet('Sheet1', function ($sheet) use ($st_code) {

            $user = Auth::user();

            $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
                                          IF(lead_cand_name!='null','STARTED','NOT STARTED') AS counting ,
                                          IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
                                          FROM winning_leading_candidate w RIGHT JOIN m_ac a ON w.st_code=a.ST_CODE 
                                          AND w.ac_no=a.AC_NO RIGHT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 WHERE a.ST_CODE='" . $st_code . "' AND e.`election_id`=" . $user->election_id . "";

            $PcCeoCountingData = DB::select($PcCeoCountingSelectData);
            //dd($PcCeoCountingData);  

            $arr  = array();

            $user = Auth::user();

            foreach ($PcCeoCountingData as $CountingData) {

              $data =  array(
                $CountingData->ano,
                $CountingData->aac,
                $CountingData->counting,
                $CountingData->res_declare,
              );
              array_push($arr, $data);
              // }
            }


            $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(
              array(
                'AC No', 'AC Name', 'Counting Status', 'Result Status'
              )

            );
          });
        })->export('xls');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO COUNTING RESULT DATA EXCEL REPORT  TRY CATCH BLOCK ENDS

  }
  //PC CEO COUNTING RESULT DATA EXCEL REPORT FUNCTION ENDS

  //PC CEO COUNTING RESULT PDF DATA REPORT STARTS
  public function CountingStatusPdf(Request $request)
  {
    //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code = $user_data->st_code;
        $st_name = $user_data->placename;


        $PcCeoCountingSelectData = "SELECT w.st_name ,w.ac_name AS wac, a.AC_NO AS ano , a.AC_NAME AS aac ,
                                          IF(lead_cand_name!='null','STARTED','NOT STARTED') AS counting ,
                                          IF(STATUS='1','DECLARED','NOT DECLARED') AS res_declare 
                                          FROM winning_leading_candidate w RIGHT JOIN m_ac a ON w.st_code=a.ST_CODE 
                                          AND w.ac_no=a.AC_NO RIGHT JOIN m_election_details e ON e.st_code=w.ST_CODE AND w.ac_no=e.`CONST_NO` AND `CONST_TYPE`='AC' AND `election_status`=1 WHERE a.ST_CODE='" . $st_code . "' AND e.`election_id`=" . $user->election_id . "";

        $CountingStatus = DB::select($PcCeoCountingSelectData);


        $pdf = PDF::loadView('admin.ac.ceo.ceo_counting.CountingStatusPdf', ['user_data' => $user_data, 'CountingStatus' => $CountingStatus]);
        return $pdf->download('CountingStatusPdf' . trim($st_name) . '_Today_' . $cur_time . '.pdf');
        return view('admin.ac.ceo.ceo_counting.CountingStatusPdf');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO COUNTING RESULT PDF DATA REPORT TRY CATCH BLOCK ENDS

  }
  //PC CEO COUNTING RESULT PDF DATA REPORT FUNCTION ENDS


  //AC CEO ELECTION SCHEDULE DATA REPORT STARTS
  public function CeoElectionSchedule(Request $request)
  {
    //AC CEO ELECTION SCHEDULE DATA REPORT TRY CATCH BLOCK STARTS
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;

        //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
        $GetAllElectionSchedule = $this->GetAllElectionSchedule();
        Session::put('ScheduleList', $GetAllElectionSchedule);
        //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
        //dd($GetAllElectionSchedule);

        $ScheduleData =   "SELECT e.ScheduleID AS sid, e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
								 a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,
								 s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
								 s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
								 FROM m_election_details e 
								 RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO
								 RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
								 WHERE e.CONST_TYPE = 'AC' AND a.ST_CODE='" . $st_code . "' 
                                 ORDER BY sid ,cno";

        $ScheduleSelectData = DB::select($ScheduleData);

        if ($request->has('is_excel')) {

          return $ScheduleSelectData;
        }

        return view('admin.ac.ceo.CeoElectionSchedule', ['user_data' => $user_data, 'ScheduleSelectData' => $ScheduleSelectData]);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //AC CEO ELECTION SCHEDULE DATA REPORT TRY CATCH BLOCK ENDS

  }
  //AC CEO ELECTION SCHEDULE DATA REPORT FUNCTION ENDS


  ///AC CEO ELECTION SCHEDULE DATA REPORT pdf function ends
  public function CeoElectionSchedulePdf(Request $request)
  {
    set_time_limit(6000);
    $user = Auth::user();
    $uid = $user->id;

    $user_data = $this->commonModel->getunewserbyuserid($uid);
    $cur_time    = Carbon::now();
    $st_code     = $user_data->st_code;
    $st_name     = $user_data->placename;

    $ScheduleSelectData = $this->CeoElectionSchedule($request->merge(['is_excel' => 1]));
    $pdf = PDF::loadView('admin.ac.ceo.CeoElectionSchedulePdf', ['user_data' => $user_data, 'ScheduleSelectData' => $ScheduleSelectData]);
    return $pdf->download('CeoElectionSchedulePdf_' . trim($st_name) . '_Today_' . $cur_time . '.pdf');
  }
  ///AC CEO ELECTION SCHEDULE DATA REPORT pdf function ends


  //AC CEO ELECTION SCHEDULE EXCEL DATA REPORT STARTS
  public function CeoElectionScheduleExcel(Request $request)
  {
    //AC CEO ELECTION SCHEDULE EXCEL DATA REPORT TRY CATCH BLOCK STARTS
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;

        Excel::create('CeoElectionScheduleExcelData_' . trim($st_name) . '_' . $cur_time, function ($excel) use ($st_code) {
          $excel->sheet('Sheet1', function ($sheet) use ($st_code) {

            $ScheduleExcelData =   "SELECT e.ScheduleID AS sid, e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
									 a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,
									 s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
									 s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
									 FROM m_election_details e 
									 RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO
									 RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
									 WHERE e.CONST_TYPE = 'AC' AND a.ST_CODE='" . $st_code . "' 
									 ORDER BY sid ,cno";

            $ScheduleSelectExcelData = DB::select($ScheduleExcelData);
            //dd($ScheduleSelectExcelData);  

            $arr  = array();

            $user = Auth::user();
            foreach ($ScheduleSelectExcelData as $ScheduleData) {

              $data =  array(
                $ScheduleData->sid,
                $ScheduleData->nac,
                $ScheduleData->cno,
                GetReadableDate($ScheduleData->start_nomi_date),
                GetReadableDate($ScheduleData->last_nomi_date),
                GetReadableDate($ScheduleData->dt_nomi_scr),
                GetReadableDate($ScheduleData->last_wid_date),
                GetReadableDate($ScheduleData->poll_date),
              );
              array_push($arr, $data);
              // }
            }
            $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(
              array(
                'Phase No', 'AC Name', 'AC No', 'Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'
              )

            );
          });
        })->export('xls');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //AC CEO ELECTION SCHEDULE EXCEL DATA REPORT TRY CATCH BLOCK ENDS

  }
  //AC CEO ELECTION SCHEDULE EXCEL DATA REPORT FUNCTION ENDS

  //AC CEO ELECTION FILTER FUNCTION STARTS
  public function CeoCustomReportFilter(Request $request)
  {
    //AC CEO ELECTION FILTER TRY CATCH STARTS HERE
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {

        $validator = Validator::make($request->all(), [
          'ScheduleList'   => 'nullable|numeric|regex:/^\S*$/u',
          /*'startDate'    => 'required|date',
                    'endDate'        => 'required|date|after_or_equal:startDate',*/

        ]);

        if ($validator->fails()) {
          return Redirect::back()
            ->withErrors($validator)
            ->withInput();
        }

        $xss = new xssClean;

        $ScheduleList        = $xss->clean_input($request['ScheduleList']);


        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;

        //dd($ScheduleList);
        return redirect('/acceo/CeoCustomReportFilterGet/' . base64_encode($ScheduleList));
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //AC CEO ELECTION FILTER TRY CATCH ENDS HERE
  }
  //AC CEO ELECTION FILTER FUNCTION ENDS

  //AC CEO ELECTION FILTER FUNCTION STARTS
  public function CeoCustomReportFilterGet(Request $request, $ScheduleList = null)
  {
    //AC CEO ELECTION FILTER TRY CATCH STARTS HERE
    try {

      //$input = $request->all();
      //echo '<pre>'.print_r(base64_decode($ScheduleList));die;


      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {

        $xss    = new xssClean;
        $secure = new secureCode;


        $ScheduleList      = base64_decode($ScheduleList);

        //CHECKING URL VARIABLES FOR VALUES STARTS
        if (!$ScheduleList) {
          $ScheduleList = "";
        } else {
          $ScheduleList = $ScheduleList;
        }
        //CHECKING URL VARIABLES FOR VALUES ENDS


        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;


        $FilterData =   "SELECT e.ScheduleID AS sid, e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
							 a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,
							 s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
							 s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
							 FROM m_election_details e 
							 RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO
							 RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
							 WHERE e.CONST_TYPE = 'AC' AND a.ST_CODE='" . $st_code . "' 
                             AND e.ScheduleID='" . $ScheduleList . "'
                             ORDER BY sid ,cno";

        $FilterSelectData = DB::select($FilterData);

        //dd($FilterSelectData);       
        return view('admin.ac.ceo.CeoCustomReportFilterGet', ['user_data' => $user_data, 'FilterSelectData' => $FilterSelectData, 'ScheduleList' => $ScheduleList]);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //AC CEO ELECTION FILTER TRY CATCH ENDS HERE
  }
  //AC CEO ELECTION FILTER FUNCTION ENDS

  //AC CEO ELECTION FILTER FUNCTION STARTS
  public function CeoCustomReportFilterGetExcel(Request $request, $ScheduleList = null)
  {
    //AC CEO ELECTION FILTER TRY CATCH STARTS HERE
    try {

      //$input = $request->all();
      //echo '<pre>'.print_r(base64_decode($ScheduleList));die;


      $users = Session::get('admin_login_details');
      $user = Auth::user();
      if (session()->has('admin_login')) {

        $xss    = new xssClean;
        $secure = new secureCode;


        $ScheduleList      = base64_decode($ScheduleList);

        //CHECKING URL VARIABLES FOR VALUES STARTS
        if (!$ScheduleList) {
          $ScheduleList = "";
        } else {
          $ScheduleList = $ScheduleList;
        }
        //CHECKING URL VARIABLES FOR VALUES ENDS


        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;

        $ScheduleList = Session::put('ScheduleList', $ScheduleList);

        Excel::create('CeoElectionScheduleFilterExcelData_' . trim($st_name) . '_' . $cur_time, function ($excel) use ($st_code) {
          $excel->sheet('Sheet1', function ($sheet) use ($st_code) {


            $ScheduleList = Session::get('ScheduleList');

            $FilterDataExcel =   "SELECT e.ScheduleID AS sid, e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
							    	a.AC_NO AS acno , a.AC_NAME AS nac , s.DT_ISS_NOM AS start_nomi_date,
								    s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
								    s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
								    FROM m_election_details e 
								    RIGHT JOIN m_ac a ON e.st_code=a.ST_CODE AND e.CONST_NO=a.AC_NO
								    RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
								    WHERE e.CONST_TYPE = 'AC' AND a.ST_CODE='" . $st_code . "' 
								    AND e.ScheduleID='" . $ScheduleList . "'
								    ORDER BY sid ,cno";

            $ScheduleSelectExcelData = DB::select($FilterDataExcel);
            //dd($ScheduleSelectExcelData);  

            $arr  = array();

            $user = Auth::user();
            foreach ($ScheduleSelectExcelData as $ScheduleData) {

              $data =  array(
                $ScheduleData->sid,
                $ScheduleData->nac,
                $ScheduleData->cno,
                GetReadableDate($ScheduleData->start_nomi_date),
                GetReadableDate($ScheduleData->last_nomi_date),
                GetReadableDate($ScheduleData->dt_nomi_scr),
                GetReadableDate($ScheduleData->last_wid_date),
                GetReadableDate($ScheduleData->poll_date),
              );
              array_push($arr, $data);
              // }
            }
            $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(
              array(
                'Phase No', 'AC Name', 'AC No', 'Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'
              )

            );
          });
        })->export('xls');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //AC CEO ELECTION FILTER TRY CATCH ENDS HERE
  }
  //AC CEO ELECTION FILTER FUNCTION ENDS

  public function getAcsListForMissedEntry(Request $request)
  {

    //AC CEO ESTIMATE POLL TRUNOUT MISSED AC  REPORT NEW TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $st_code     = $user_data->st_code;
      $request->merge([
        'is_excel' => 1,
        'state' => base64_encode($st_code)
      ]);
      $data = $this->MissingTurnoutModel->get_enable_acs_for_update($request);
      //buttons
      $data['buttons']    = [];
      $data['action']         = url('acceo/turnout/enable-acs-for-missed-and-modification');
      $results = [];
      foreach ($data['results'] as $key => $result) {
        $results[] = [

          'label'                     => $result['label'],
          'ac_no'                     => $result['ac_no'],
          'ac_name'                   => $result['ac_name'],
          'name'                      => $result['name'],
          'Phone_no'                  => $result['Phone_no'],
          "est_turnout_round1"        => $result['est_turnout_round1'],
          "est_turnout_round2"        => $result['est_turnout_round2'],
          "est_turnout_round3"        => $result['est_turnout_round3'],
          "est_turnout_round4"        => $result['est_turnout_round4'],
          "est_turnout_round5"        => $result['est_turnout_round5'],
          "est_turnout_round6"        => $result['est_turnout_round6'],
          "missed_status_round1"      => $result['missed_status_round1'],
          "missed_status_round2"      => $result['missed_status_round2'],
          "missed_status_round3"      => $result['missed_status_round3'],
          "missed_status_round4"      => $result['missed_status_round4'],
          "missed_status_round5"      => $result['missed_status_round5'],
          "missed_status_round6"      => $result['missed_status_round6'],
          "modification_status_round1"  => $result['modification_status_round1'],
          "modification_status_round2"  => $result['modification_status_round2'],
          "modification_status_round3"  => $result['modification_status_round3'],
          "modification_status_round4"  => $result['modification_status_round4'],
          "modification_status_round5"  => $result['modification_status_round5'],
          "modification_status_round6"  => $result['modification_status_round6'],
          'href'                      => 'javascript:void(0)',
        ];
      }

      $data['st_code'] = $st_code;
      $data['results'] = $results;
      $filter = [
        'st_code'       => $data['st_code'],
        'ac_no'         => $data['ac_no'],
        'election_id'   => $user->election_id,
        'phase_no'      => $data['phase'],
        'pc_no'         => '',
      ];
      $estimated_time = $this->turnout->get_scheduletime($filter);
      $data['estimated_time'] = $estimated_time;
      if (session()->has('admin_login')) {

        $xss = new xssClean;
        return view('admin.turnout.missed.enable-acs-for-missed-modify', $data);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  //AC CEO ESTIMATE POLL TRUNOUT MISSED AC REPORT NEW  FUNCTION STARTS
  public function AcCeoMissedAc(Request $request)
  {
    //AC CEO ESTIMATE POLL TRUNOUT MISSED AC  REPORT NEW TRY CATCH STARTS HERE
    try {

      $users = Session::get('admin_login_details');
      $user = Auth::user();
      $uid = $user->id;


      $user_data = $this->commonModel->getunewserbyuserid($uid);

      $cur_time    = Carbon::now();
      $st_code     = $user_data->st_code;
      $st_name     = $user_data->placename;


      $request->merge([
        'is_excel' => 1,
        'state' => base64_encode($st_code)
      ]);



      $data = $this->MissingTurnoutModel->get_missed($request);
      //dd($data);
      //buttons
      $data['buttons']    = [];
      /*$data['buttons'][]  = [
          'name' => 'Export Excel',
          'href' =>  url('acceo/turnout/AcCeoMissedAcExcel').'?'.$data['filter'],
          'target' => false
        ];
        $data['buttons'][]  = [
          'name' => 'Export Pdf',
          'href' =>  url('acceo/turnout/AcCeoMissedAcPdf').'?'.$data['filter'],
          'target' => false
        ];

        $data['buttons'][]  = [
          'name' => 'AC Wise Report',
          'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
          'target' => false
        ];*/

      $data['action']         = url('acceo/turnout/AcCeoMissedAc');

      $results = [];

      foreach ($data['results'] as $key => $result) {

        $individual_filter    = implode('&', [
          'state' => 'state=' . base64_encode($result['st_code']),
          'phase' => 'phase=' . $data['phase']
        ]);


        $results[] = [

          'label'                 => $result['label'],
          'ac_no'                 => $result['ac_no'],
          'ac_name'               => $result['ac_name'],
          'name'                  => $result['name'],
          'Phone_no'              => $result['Phone_no'],
          "est_turnout_round1"    => $result['est_turnout_round1'],
          "est_turnout_round2"    => $result['est_turnout_round2'],
          "est_turnout_round3"    => $result['est_turnout_round3'],
          "est_turnout_round4"    => $result['est_turnout_round4'],
          "est_turnout_round5"    => $result['est_turnout_round5'],
          "missed_status_round1"  => $result['missed_status_round1'],
          "missed_status_round2"  => $result['missed_status_round2'],
          "missed_status_round3"  => $result['missed_status_round3'],
          "missed_status_round4"  => $result['missed_status_round4'],
          "missed_status_round5"  => $result['missed_status_round5'],
          "missed_status_round6"  => $result['missed_status_round6'],
          'href'                  => 'javascript:void(0)',
        ];
      }


      $data['results'] = $results;

      //dd($data);
      if (session()->has('admin_login')) {

        $xss = new xssClean;
        return view('admin.turnout.missed.AcCeoMissedAc', $data);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //AC CEO ESTIMATE POLL TRUNOUT MISSED AC NEW REPORT TRY CATCH ENDS HERE
  }
  //AC CEO ESTIMATE POLL TRUNOUT MISSED AC NEW REPORT FUNCTION ENDS


  //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW WISE Excel REPORT  FUNCTION STARTS
  public function AcCeoMissedAcExcel(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW WISE Excel REPORT TRY CATCH STARTS HERE
    try {


      if (session()->has('admin_login')) {

        $xss = new xssClean;

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;

        $this->AcCeoMissedAc($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //AC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW WISE Excel REPORT TRY CATCH ENDS HERE
  }
  //AC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW WISE Excel REPORT FUNCTION ENDS



  //AC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW PDF REPORT  FUNCTION STARTS
  public function AcCeoMissedAcPdf(Request $request)
  {
    //AC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW PDF REPORT TRY CATCH STARTS HERE
    try {


      if (session()->has('admin_login')) {

        $xss = new xssClean;

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;


        $request->merge([
          'state' => base64_encode($st_code)
        ]);

        $this->AcCeoMissedAc($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //AC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW PDF REPORT TRY CATCH ENDS HERE
  }

  public function enableAcs(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $xss = new xssClean;
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        //dd($user);

        $state_code = $user->st_code;
        if ($request->has('st_code')) {
          $state_code = $request->input('st_code');
        }
        $phase_no = $request->input('phase_no');
        $round_no = $request->input('round_no');
        $ac_no = $request->input('ac_no');
        $data_option = $request->input('data_option');
        if ($data_option == 'on') {
          $flagval = 1;
          $message = 'enabled';
        } else {
          $message = 'disabled';
          $flagval = 0;
        }

        if (!empty($phase_no) && !empty($round_no) && !empty($ac_no)) {
          $missed_flag = 'missed_status_round' . $round_no;
          DB::table('pd_scheduledetail')->where('st_code', $state_code)->where('ac_no', $ac_no)->update([$missed_flag => $flagval]);
          Session::flash('success_mes', 'Option ' . $message . ' successfully.');
          return Redirect::back();
        } else {
          Session::flash('error_mes', 'Please try again');
          return Redirect::back();
        }
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function AcCeoPSElectoralDefinalzied(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $data = [];
        $data['user_data'] = $user_data;
        $data['election_type'] = ($request->has('election_type')) ?? $request->election_type;
        $data['phases'] = PhaseModel::get_phases(['election_type' => $data['election_type']]);
        $phase = PhaseModel::get_state_phase(['st_code' => $user->st_code]);
        $data['phase'] = ($request->has('phase')) ? $request->phase : $phase->sechudle_id;

        $acsForSelectedPhase = EndOfPollFinaliseModel::where('schedule_id',  $data['phase'])->where('st_code', $user_data->st_code)->pluck('ac_no');
        $data['results'] = AC::whereIn('AC_NO', $acsForSelectedPhase)->select(["AC_NO", "AC_NAME"])->where('ST_CODE', $user_data->st_code)->orderBy('AC_NO')->get()->map(function ($item, $key) use ($request, $user_data) {
          $temp = $item;
          $total_ps = PollingStation::getAcPollingStationCount($user_data->st_code, $item->AC_NO);
          $total_ps_finalized = PollingStation::getAcPollingStationFinalizedCount($user_data->st_code, $item->AC_NO);
          $temp['AC_NO'] = $item['AC_NO'];
          $temp['AC_NAME'] = $item['AC_NAME'];
          if ($request->has('excel') && $request->input('excel') == 'download') {
            $temp['ps_finalized'] = (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) ? 'Finalized' : 'Not Yet Finalize';
          } else {
            $temp['ps_finalized'] = (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) ? 1 : 0;
          }
          return $temp;
        });
        $filter = [
          'st_code'       => $user_data->st_code,
          'election_id'   => $user->election_id,
          'pc_no'         => '',
        ];
        if ($data['phase'] != 1) {
          $filter['phase_no'] = $data['phase'];
        }
        $estimated_time = $this->turnout->get_scheduletime($filter);
        $data['showDefinalizeBtn'] = (date('Y-m-d') < $estimated_time->poll_date) ? true : false;
        $data['heading_title'] = 'AC List with Polling Station Electorals Finalized Status';
        if ($request->has('excel') && $request->input('excel') == 'download') {
          $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], "Polling_Station_Electoral_Finalize_" . $user_data->st_code . "_Report"));
          $headings = [
            "AC No",
            "AC Name",
            "Status",
          ];
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report Imports';
          $ErrorMessage['TransectionStatus'] = 'Success';
          $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report Imports done for state " . $user_data->st_code;
          LogNotification::LogInfo($ErrorMessage);
          return Excel::download(new ExcelExport($headings, $data['results']), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
        } else {
          return view('admin.turnout.AcCeoPSElectoralDefinalzied', $data);
        }
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report';
      $ErrorMessage['TransectionStatus'] = 'Failed';
      $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report failed ";
      LogNotification::LogInfo($ErrorMessage);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function AcCeoPSElectoralDefinalziedUpdate(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $validator = Validator::make($request->all(), [
          'ac_no'   => 'required',
        ]);

        if ($validator->fails()) {
          return Redirect::back()
            ->withErrors($validator)
            ->withInput();
        }
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $data['user_data'] = $user_data;
        $update = [
          'electors_finalize_by_ro' => 0,
          'electors_finalize_by_ro_date' => date('Y-m-d H:i:s', time())
        ];
        PollingStation::where('ST_CODE', $user_data->st_code)->where('AC_NO', $request->ac_no)->update($update);
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral definalized';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral definalized successfully for state " . $user_data->st_code . " AC " . $request->ac_no;
        LogNotification::LogInfo($ErrorMessage);
        return redirect()->back()->with("success", "Polling Station Electorals is definalized");
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral definalized';
      $ErrorMessage['TransectionStatus'] = 'Failed';
      $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral definalized failed for AC " . $request->ac_no;
      LogNotification::LogInfo($ErrorMessage);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function AcCeoAcElectoralReport(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $data = [];
        $data['user_data'] = $user_data;
        $data['election_type'] = ($request->has('election_type')) ?? $request->election_type;
        $data['phases'] = PhaseModel::get_phases(['election_type' => $data['election_type']]);
        $phase = PhaseModel::get_state_phase(['st_code' => $user->st_code]);
        $data['phase'] = ($request->has('phase')) ? $request->phase : $phase->sechudle_id;
        $data['results'] = ElectorModel::with(['ac' => function ($q) use ($user_data) {
          $q->where('ST_CODE', $user_data->st_code);
        }])->where('st_code', $user_data->st_code)->where('scheduledid', $data['phase'])->get()->map(function ($item, $key) use ($request, $user_data) {
          // $temp = $item;
          $total_ps = PollingStation::getAcPollingStationCount($user_data->st_code, $item->ac_no);
          $total_ps_finalized = PollingStation::getAcPollingStationFinalizedCount($user_data->st_code, $item->ac_no);
          $temp['ac_no'] = $item['ac_no'];
          $temp['ac_name'] = $item['ac']['AC_NAME'];
          $temp['electors_male'] = $item['electors_male'];
          $temp['electors_female'] = $item['electors_female'];
          $temp['electors_other'] = $item['electors_other'];
          $temp['electors_total'] = $item['electors_total'];
          $temp['electors_service'] = $item['electors_service'];
          $temp['electors_gt'] = $item['electors_service'] + $item['electors_total'];
          if ($request->has('excel') && $request->input('excel') == 'download') {
            if (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) {
              $temp['ps_finalized'] =  'Verified By RO';
            } else if (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps != $total_ps_finalized) {
              $temp['ps_finalized'] =  'Not Verify By RO';
            } else {
              $temp['ps_finalized'] =  'Data not entered';
            }
          } else {
            if (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) {
              $temp['ps_finalized'] =  2;
            } else if (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps != $total_ps_finalized) {
              $temp['ps_finalized'] =  1;
            } else {
              $temp['ps_finalized'] =  0;
            }
          }
          return $temp;
        })->toArray();
        // dd( $data['results']);
        $filter = [
          'st_code'       => $user_data->st_code,
          'election_id'   => $user->election_id,
          'pc_no'         => '',
        ];
        if ($data['phase'] != 1) {
          $filter['phase_no'] = $data['phase'];
        }
        // $estimated_time = $this->turnout->get_scheduletime($filter);
        // $data['showDefinalizeBtn'] = (date('Y-m-d') < $estimated_time->poll_date ) ? true : false;
        $data['heading_title'] = 'AC wise Electors Report';
        if ($request->has('excel') && $request->input('excel') == 'download') {
          $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], "AC_wise_Electors_" . $user_data->st_code . "_Report"));
          $headings = [
            "AC No",
            "AC Name",
            "Electors Male",
            "Electors Female",
            "Electors Other",
            "Electors Total",
            "Electors Service",
            "Electors Grand Total",
            "Status",
          ];
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report Imports';
          $ErrorMessage['TransectionStatus'] = 'Success';
          $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report Imports done for state " . $user_data->st_code;
          LogNotification::LogInfo($ErrorMessage);
          return Excel::download(new ExcelExport($headings, $data['results']), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
        } else {
          return view('admin.turnout.AcCeoAcElectoralReport', $data);
        }
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report';
      $ErrorMessage['TransectionStatus'] = 'Failed';
      $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report failed ";
      LogNotification::LogInfo($ErrorMessage);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function ExemptACWithNoPollingPS(Request $request)
  {
    try {
      $user = Auth::user();
      $data['user_data'] = $user;
      $data['acs'] = AC::where('ST_CODE', $user->st_code)->get();
      $data['results'] = ExemptedAcWithPollingstationCheckModel::with(['ac' => function ($q) use ($user) {
        $q->where('ST_CODE', $user->st_code);
      }])->where('st_code', $user->st_code)->get();
      $data['selectedAc'] =
        ExemptedAcWithPollingstationCheckModel::where('st_code', $user->st_code)->pluck('ac_no')->toArray();
      return view('admin.turnout.ExemptACWithNoPollingPS', $data);
    } catch (Exception $ex) {
      Log::error($ex);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function AddExemptACWithNoPollingPS(Request $request)
  {
    try {
      $user = Auth::user();
      $validator = Validator::make($request->all(), [
        'ac' => 'required',
      ], []);
      if ($validator->fails()) {
        return Redirect::back()->withInput($request->all())->withErrors($validator);
      }
      ExemptedAcWithPollingstationCheckModel::create([
        'st_code' => $user->st_code,
        'ac_no' => $request->input('ac'),
        'election_id' => $user->election_id,
      ]);
      return Redirect::back()->with('success', 'AC is exempted successfully');
    } catch (Exception $ex) {
      dd($ex);
      Log::error($ex);
      return Redirect::back()->with('error', 'unable to exempted AC');
    }
  }

  public function RemoveExemptACWithNoPollingPS(Request $request)
  {
    try {
      $user = Auth::user();
      $validator = Validator::make($request->all(), [
        'id' => 'required',
      ], []);
      if ($validator->fails()) {
        return Redirect::back()->withInput($request->all())->withErrors($validator);
      }
      $row = ExemptedAcWithPollingstationCheckModel::where('id', $request->input('id'))->first();
      if ($row && $row->st_code == $user->st_code) {
        $row->delete();
      } else {
        return Redirect::back()->with('error', 'invalid id used for removal of ac');
      }
      return Redirect::back()->with('success', 'AC is removed from exempted list successfully');
    } catch (Exception $ex) {
      Log::error($ex);
      return Redirect::back()->with('error', 'unable to removed AC from exempted list');
    }
  }
}  // end class