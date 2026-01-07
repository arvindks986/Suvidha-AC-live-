<?php

namespace App\Http\Controllers\Admin\turnout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\commonModel;
use App\adminmodel\ECIModel;
use App\adminmodel\ACROModel;

//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Exports\ExcelExport;
use App\Helpers\LogNotification;
//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;
use App\Imports\PollingStationImport;
use App\models\Admin\ElectorModel;
use App\models\Admin\PollingStation;
use App\models\Admin\turnout\TurnoutModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

date_default_timezone_set('Asia/Kolkata');


class ElectorsDetailsController extends Controller
{
  use CommonTraits;
  public $base    = 'roac';
  public $folder  = 'turnout';
  public $action    = 'roac/turnout/';
  public $view_path = "admin.turnout.ro";
  public $commonModel = null;
  public $romodel = null;
  public $ECIModel = null;
  public $turnout = null;

  public function __construct()
  {
    $this->middleware('adminsession');
    $this->middleware(['auth:admin', 'auth']);
    $this->middleware('clean_url');
    $this->middleware('ro');
    $this->commonModel = new commonModel();
    $this->romodel = new ACROModel();
    $this->ECIModel = new ECIModel();
    $this->turnout = new TurnoutModel;
    if (!Auth::check()) {
      return redirect('/officer-login');
    }
  }
  protected function guard()
  {
    return Auth::guard();
  }



  //ECI ELECTORS DEATILS  STARTS
  public function ElectorsDetails(Request $request)
  {
    $data  = [];
    //ECI ELECTORS DEATILS  TRY CATCH BLOCK STARTS
    try {
      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($user_data->st_code, $user_data->ac_no, $user_data->dist_no, $user_data->id, 'AC');
      $st = getstatebystatecode($ele_details->ST_CODE);
      $ac = getacbyacno($ele_details->ST_CODE, $ele_details->CONST_NO);
      $filter = [
        'st_code'       => $ele_details->ST_CODE,
        'ac_no'         => $ele_details->CONST_NO,
        'election_id'   => $ele_details->ELECTION_ID,
        'const_type'    => $ele_details->CONST_TYPE,
        'pc_no'         => '',
      ];

      $ElectorsDetails = $this->turnout->getcdacelectorsdetails($filter);
      $data['user_data']      = $user_data;
      $data['ele_details']    = $ele_details;
      $data['st_code']        = $ele_details->ST_CODE;
      $data['ac_no']          = $ele_details->CONST_NO;
      $data['ac_name']        = $ac->AC_NAME;
      $data['st_name']        = $st->ST_NAME;
      $data['ScheduleID']     = $ele_details->ScheduleID;
      $data['ElectorsDetails'] = $ElectorsDetails;

      $data['results'] = PollingStation::where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->orderBy('PART_NO')->get();
      $data['electorCdac'] = ElectorModel::select('electors_male', 'electors_female', 'electors_other', 'electors_service', 'electors_total')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
      $totalPsElectoralDataFinalizedCount = 0;
      $modificationEnabledByECI = 0;
      foreach ($data['results'] as $key => $item) {
        if ($item->electors_finalize_by_ro == 1) {
          $totalPsElectoralDataFinalizedCount++;
        }
        if ($item->electors_enable_edit_by_eci == 1) {
          $modificationEnabledByECI++;
        }
      }
      $data['totalPsElectoralDataFinalized'] = $totalPsElectoralDataFinalizedCount;
      return view($this->view_path . '.ElectorsDetails', $data);
    } catch (Exception $ex) {
      Log::error($ex);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //ECI ELECTORS DEATILS  TRY CATCH BLOCK ENDS

  }
  //ECI ELECTORS DEATILS  FUNCTION ENDS


  //ECI ELECTORS DEATILS UPATE STARTS
  public function ElectorsDetailsUpdate(Request $request)
  {
    try {

      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($user_data->st_code, $user_data->ac_no, $user_data->dist_no, $user_data->id, 'AC');

      $validator = Validator::make($request->all(), [
        'electors_male'     => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_female'   => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_other'    => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_total'    => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_services' => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_gtotal'   => 'required|numeric|min:0|integer|between:0,9999999',
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
      $electors_services    = $xss->clean_input($request['electors_services']);
      $electors_gtotal      = $xss->clean_input($request['electors_gtotal']);

      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Electors data Update';
      //ELECTORS DATA MATCHING STARTS
      if ($electors_male + $electors_female + $electors_other != $electors_total) {
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Sum of Male, Female and Other is not equal to total electoral for AC " . $user_data->ac_no . " STATE " . $user_data->st_code;
        LogNotification::LogInfo($ErrorMessage);
        return Redirect('/roac/turnout/ElectorsDetails/')->with('error', 'Data Mismatch in Electors Data.');
      }
      if ($electors_total + $electors_services != $electors_gtotal) {
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Sum of total and services electoral is not equal to grand total electoral for AC " . $user_data->ac_no . " STATE " . $user_data->st_code;
        LogNotification::LogInfo($ErrorMessage);
        return Redirect('/roac/turnout/ElectorsDetails/')->with('error', 'Data Mismatch in Electors Data with service voter.');
      }
      //ELECTORS DATA MATCHING ENDS
      $update_fields = array(
        'electors_male'      => $electors_male,
        'electors_female'    => $electors_female,
        'electors_other'     => $electors_other,
        'electors_total'     => $electors_total,
        'electors_service'   => $electors_services,
        'updated_by'         => $user_data->officername
      );
      $elec_fields = array('electors_total'     => $electors_total);
      //
      $ElectorsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $user_data->ac_no, 'election_id' => $ele_details->ELECTION_ID];
      $ErrorMessage['TransectionStatus'] = 'Success';
      $ErrorMessage['LogDescription'] = "Electoral data is updated for AC " . $user_data->ac_no . " STATE " . $user_data->st_code;
      LogNotification::LogInfo($ErrorMessage);
      DB::table('electors_cdac')->where($ElectorsWhere)->update($update_fields);
      DB::table('pd_scheduledetail')->where($ElectorsWhere)->update($elec_fields);

      return Redirect('/roac/turnout/ElectorsDetails/')->with('success', 'Electrol Data Updated Successfully !');
    } catch (Exception $ex) {
      Log::error($ex);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //ECI ELECTORS DEATILS  UPATE TRY CATCH BLOCK ENDS

  }
  //ECI ELECTORS DEATILS  UPATE FUNCTION ENDS

  public function PollingStationElectorsDetails(Request $request)
  {
    $data  = [];
    try {
      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $data['user_data']    = $user_data;
      $ele_details = $this->commonModel->election_detailsac($user_data->st_code, $user_data->ac_no, $user_data->dist_no, $user_data->id, 'AC');
      $statename = getstatebystatecode($ele_details->ST_CODE);
      $acame = getacbyacno($ele_details->ST_CODE, $ele_details->CONST_NO);
      $title_array[] = "State: " . $statename->ST_NAME;
      $title_array[] = "AC: " . $acame->AC_NAME;
      $data['seched'] = getschedulebyid($ele_details->ScheduleID);
      $data['filter_buttons'] = $title_array;
      $data['psTotalElectorMale'] = 0;
      $data['psTotalElectorFemale'] = 0;
      $data['psTotalElectorOther'] = 0;
      $data['psTotalElector'] = 0;
      $data['results'] = PollingStation::where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->orderBy('PART_NO')->get();
      $data['electorCdac'] = ElectorModel::select('electors_male', 'electors_female', 'electors_other', 'electors_service', 'electors_total')->where('st_code', $ele_details->ST_CODE)->where('ac_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
      $totalPsElectoralDataFinalizedCount = 0;
      $modificationEnabledByECI = 0;
      foreach ($data['results'] as $key => $item) {
        $data['psTotalElectorMale'] = $data['psTotalElectorMale'] + $item->electors_male;
        $data['psTotalElectorFemale'] = $data['psTotalElectorFemale'] + $item->electors_female;
        $data['psTotalElectorOther'] = $data['psTotalElectorOther'] + $item->electors_other;
        $data['psTotalElector'] = $data['psTotalElector'] + $item->electors_total;
        if ($item->electors_finalize_by_ro == 1) {
          $totalPsElectoralDataFinalizedCount++;
        }
        if ($item->electors_enable_edit_by_eci == 1) {
          $modificationEnabledByECI++;
        }
      }
      $data['totalPsElectoralDataFinalized'] = $totalPsElectoralDataFinalizedCount;
      $data['modificationEnabledByECI'] = $modificationEnabledByECI;
      $data['heading_title']    = "Polling Station Electors Details";
      $data['diabledPSModifications']    = (date('Y-m-d') >= $data['seched']['DATE_POLL']) ? true : false;
      $data['importPollingStationStatus'] = true;
      if (count($data['results']) > 0 && count($data['results']) == $totalPsElectoralDataFinalizedCount) {
        $data['importPollingStationStatus'] = false;
      }
      if ($data['diabledPSModifications']) {
        $data['importPollingStationStatus'] = false;
      }
      return view($this->view_path . '.polling_station.PollingStationElectorsDetails', $data);
    } catch (Exception $ex) {
      Log::error($ex);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }


  public function PollingStationElectorsDetailsUpdate(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'psccode'     => 'required',
        'PS_NAME_EN'     => 'required',
        'PART_NAME'     => 'required',
        'electors_male'   => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_female'    => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_other'    => 'required|numeric|min:0|integer|between:0,9999999',
        'electors_total' => 'required|numeric|min:0|integer|between:0,9999999',
      ]);


      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Electors data Update';
      if (($request->electors_male + $request->electors_female + $request->electors_other) !=  $request->electors_total) {
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station Sum of Male, Female and Other is not equal to total electoral for psccode " . $request->psccode;
        LogNotification::LogInfo($ErrorMessage);
        return Redirect('/roac/turnout/polling-station-electors-details')->with('error', 'Electors Total not equal to the sum of male, female and other electors.');
      }
      $update = $request->all();
      unset($update['_token']);
      unset($update['psccode']);
      $ErrorMessage['TransectionStatus'] = 'Success';
      $ErrorMessage['LogDescription'] = "Polling Station Sum of Male, Female and Other is not equal to total electoral for psccode " . $request->psccode;
      LogNotification::LogInfo($ErrorMessage);
      PollingStation::where('CCODE', $request->psccode)->update($update);
      return Redirect('/roac/turnout/polling-station-electors-details')->with('success', 'Polling Station Updated Successfully !');
    } catch (Exception $ex) {
      Log::error($ex);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }


  public function PollingStationImport(Request $request)
  {
    try {
      $validator = Validator::make(
        [
          'excel'      => $request->excel,
          'extension' => strtolower($request->excel->getClientOriginalExtension()),
        ],
        [
          'excel'          => 'required',
          'extension'      => 'required|in:xlsx,xml',
        ]
      );
      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($user_data->st_code, $user_data->ac_no, $user_data->dist_no, $user_data->id, 'AC');
      Excel::import(new PollingStationImport($ele_details), request()->file('excel'));
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station Imports';
      $ErrorMessage['TransectionStatus'] = 'Success';
      $ErrorMessage['LogDescription'] = "Polling Station impoted for AC " . $user_data->ac_no . " State " . $user_data->st_code;
      LogNotification::LogInfo($ErrorMessage);
      return Redirect('/roac/turnout/polling-station-electors-details')->with('success', 'Your request is processed successfully');
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
      $failures = $e->failures();
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station Imports';
      $ErrorMessage['TransectionStatus'] = 'Failed';
      $ErrorMessage['LogDescription'] = "Polling Station impots failed";
      LogNotification::LogInfo($ErrorMessage);
      foreach ($failures as $failure) {
        $failure->row(); // row that went wrong
        $failure->attribute(); // either heading key (if using heading row concern) or column index
        $failure->errors(); // Actual error messages from Laravel validator
        $failure->values(); // The values of the row that has failed.
        // dd($failure->errors());
      }
      return Redirect('/roac/turnout/polling-station-electors-details')->with('error', $failures);
    } catch (Exception $ex) {
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station Imports';
      $ErrorMessage['TransectionStatus'] = 'Failed';
      $ErrorMessage['LogDescription'] = "Polling Station impots failed due to exception";
      LogNotification::LogInfo($ErrorMessage);
      Log::error($ex);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function PollingStationElectorsFinalized(Request $request)
  {
    try {
      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $update = [
        'electors_finalize_by_ro' => 1,
        'electors_enable_edit_by_eci' => 0,
        'electors_finalize_by_ro_date' => date('Y-m-d H:i:s', time())
      ];
      PollingStation::where('ST_CODE', $user_data->st_code)->where('AC_NO', $user_data->ac_no)->update($update);
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station electors finalzied';
      $ErrorMessage['TransectionStatus'] = 'Success';
      $ErrorMessage['LogDescription'] = "Polling Station electorals finalzied AC " . $user_data->ac_no . " State " . $user_data->st_code;
      LogNotification::LogInfo($ErrorMessage);
      return Redirect('/roac/turnout/polling-station-electors-details')->with('success', 'Polling Station Updated Successfully !');
    } catch (Exception $ex) {
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station electors finalzied';
      $ErrorMessage['TransectionStatus'] = 'Failed';
      $ErrorMessage['LogDescription'] = "Polling Station impots failed due to exception";
      LogNotification::LogInfo($ErrorMessage);
      Log::error($ex);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function PollingStationElectorsDetailsExport(Request $request)
  {
    try {
      $user = Auth::user();
      $user_data = $this->commonModel->getunewserbyuserid($user->id);
      $ele_details = $this->commonModel->election_detailsac($user_data->st_code, $user_data->ac_no, $user_data->dist_no, $user_data->id, 'AC');
      $export_data = PollingStation::where('ST_CODE', $ele_details->ST_CODE)->where('AC_NO', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->select(["PART_NO", "PART_NAME", "PS_NO", "PS_NAME_EN", "PS_TYPE", "PS_CATEGORY", "LOCN_TYPE", "electors_male", "electors_female", "electors_other", "electors_total"])->orderBy('PART_NO')->get();
      $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], "Polling_Station_State_" . $ele_details->ST_CODE . "_AC_" . $ele_details->CONST_NO . "_Report"));
      $headings = [
        "Part No",
        "Part Name",
        "PS No",
        "PS Name EN",
        "PS Type",
        "PS Category",
        "Location Type",
        "Electors Male",
        "Electors Female",
        "Electors Other",
        "Electors Total",
      ];
      return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
    } catch (Exception $ex) {
      Log::error($ex);
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }
}  // end class