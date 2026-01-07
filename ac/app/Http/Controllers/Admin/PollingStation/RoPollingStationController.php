<?php

namespace App\Http\Controllers\Admin\PollingStation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use \PDF;
use App\commonModel;
use App\adminmodel\ACROModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;

//INCLUDING CLASSES
use App\Classes\xssClean;
//POLLING STATION MODELS
use App\models\Admin\polling_station\PollingStationModel;
//current
use App\models\Admin\turnout\TurnoutModel;
use App\Exports\ExcelExport;
use App\Helpers\LogNotification;
use App\models\Admin\PollingStation;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class RoPollingStationController extends Controller
{

  public $view_path     = "admin.ac.ro";
  public $action        = 'roac/RoPsWiseDetails';



  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->PollingStationModel = new PollingStationModel();

    $this->middleware('adminsession');
    $this->middleware(['auth:admin', 'auth']);
    $this->middleware('ro');
    $this->commonModel = new commonModel();
    $this->turnout = new TurnoutModel;

    $this->romodel = new ACROModel();
    $this->xssClean = new xssClean;

    if (!Auth::user()) {
      return redirect('/officer-login');
    }
  }



  public function RoPsWiseDetails(Request $request)
  {



    $data = [];

    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];

    $data['phases'] = PhaseModel::get_phases();


    //PHASE FILTER
    /*$data['phase'] = NULL;
      if($request->has('phase')){
        if($request->phase != 'all'){
          $data['phase'] = $request->phase;
        }
        $request_array[] =  'phase='.$request->phase;
      }else{
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase='.$default_phase; 
      }*/

    //set title
    $title_array  = [];
    $data['heading_title'] = "PS Wise Voter Turnout";

    $data['ac_id'] = NULL;
    if ($request->has('ac_id')) {
      $data['ac_id'] = $request->ac_id;
    }


    //end set title
    $data['user_data']  =   Auth::user();

    $ele_details = $this->commonModel->election_detailsac(Auth::user()->st_code, Auth::user()->ac_no, Auth::user()->dist_no, Auth::user()->id, 'AC');


    $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
    if ($check_finalize == '') {
      $cand_finalize_ceo = 0;
      $cand_finalize_ro = 0;
    } else {
      $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
      $cand_finalize_ro = $check_finalize->finalized_ac;
    }

    $seched = getschedulebyid($ele_details->ScheduleID);
    $sechdul = checkscheduledetails($seched);

    $data['ele_details']         = $ele_details;
    $data['seched']              = $seched;
    $data['sechdul']             = $sechdul;
    $data['cand_finalize_ceo']   = $cand_finalize_ceo;
    $data['cand_finalize_ro']    = $cand_finalize_ro;

    if (Auth::user()->designation == 'ROAC') {
      $data['state'] = Auth::user()->st_code;
      $data['ac_id'] = Auth::user()->ac_no;
    }


    $xss = new xssClean;
    //CHECKING REQUEST VARIABES STARTS
    if ($request->has('ac_id')) {

      $validator = Validator::make($request->all(), [
        'ac_id'          => 'required|numeric',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $ac_id    = $xss->clean_input($request->ac_id);


      $filter_election = [
        'state'         => $data['state'],
        'ac_no'         => $ac_id,
      ];



      $request_array[] =  'state=' . $data['state'];
      $request_array[] =  'ac_id=' . $ac_id;

      $statename = getstatebystatecode($data['state']);
      $acame = getacbyacno($data['state'], $ac_id);


      $title_array[] = "State: " . $statename->ST_NAME;
      $title_array[] = "AC: " . $acame->AC_NAME;

      $data['consituencies']  = AcModel::get_records([
        'state'         => $data['state']
      ]);


      $data['filter_buttons'] = $title_array;

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action . '/excel') . '?' . implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action . '/pdf') . '?' . implode('&', $request_array),
        'target' => true
      ];


      $data['action']         = url($this->action);

      $results                = [];




      $ac_data         = PollingStationModel::get_ac_data($filter_election);
      $object         = PollingStationModel::get_ps_data($filter_election);
      $is_finalize  = PollingStationModel::get_ps_finalize_data_ro($filter_election);
      if (count($is_finalize) > 0) {
        foreach ($is_finalize as $k => $v) {
          if ($v->ro_ps_finalize == 0) {
            $data['is_finalize'] = 0;
          } else {
            $data['is_finalize'] = 1;
          }
        }
      } else {
        $data['is_finalize'] = 0;
      }

      //dd($object);

      $filter = [
        'st_code'       => $ele_details->ST_CODE,
        'ac_no'         => $ele_details->CONST_NO,
        'election_id'   => $ele_details->ELECTION_ID,
        'const_type'    => $ele_details->CONST_TYPE,
        'pc_no'         => '',
      ];
      $lists = $this->turnout->get_scheduledetail($filter);


      // dd($ac_data);

      $data['ac_data']    =   $ac_data;
      $data['results']    =   $object;
      $data['lists'] = $lists;
    } else {

      $data['buttons']    = [];
      $data['action']         = url($this->action);
      $data['results'] = [];

      $data['consituencies']  = AcModel::get_records([
        'state'         => $data['state'],
      ]);
    }



    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }

      return $data;
    }

    $data['showFinalizeBtn'] = false;
    $data['showTableColumns'] = false;

    if ($data['lists']->end_of_poll_finalize == 0 && $data['is_finalize'] == 0 && count($data['results']) > 0) {
      // $close_of_poll_data = DB::table('pd_scheduledetail_publish')
      // ->where('st_code', $data['state'])
      // ->where('ac_no', $ac_id)
      // ->first();
      // if($data['seched']['DATE_POLL'] == date('Y-m-d') && $close_of_poll_data->close_of_poll > 0){
      if ($data['seched']['DATE_POLL'] == date('Y-m-d')) {
        $data['showFinalizeBtn'] = true;
        $data['showTableColumns'] = true;
      } else if ($data['seched']['DATE_POLL'] < date('Y-m-d')) {
        $data['showFinalizeBtn'] = true;
        $data['showTableColumns'] = true;
      }
    } else if ($data['is_finalize'] == 1) {
      $data['showTableColumns'] = true;
    }

    // dd($data);

    return view($this->view_path . '.polling_station.RoPsWiseDetails', $data);
  }


  //EXCEL REPORT STARTS
  public function RoPsWiseDetailsExcel(Request $request)
  {


    set_time_limit(6000);
    $data = $this->RoPsWiseDetails($request->merge(['is_excel' => 1]));


    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $headings[] = [$data['heading_title']];
    $export_data[] = ['PS No', 'PS Name', 'Location Type', 'PS Type', 'Electors Male', 'Electors Female', 'Electors Other', 'Electors Total', 'Voter Male', 'Voter Female', 'Voter Other', 'Voter Total'];

    $arr  = array();
    $TotalElectorMale = 0;
    $TotalElectorFeMale = 0;
    $TotalElectorOther = 0;
    $TotalElector = 0;
    $TotalVoterMale = 0;
    $TotalVoterFeMale = 0;
    $TotalVoterOther = 0;
    $TotalVoter = 0;

    $totalvalues = [];

    foreach ($data['results'] as $lis) {
      $export_data[] = [
        ($lis->PS_NO) ? ($lis->PS_NO) : '0',
        ($lis->PS_NAME_EN) ? ($lis->PS_NAME_EN) : '0',
        ($lis->LOCN_TYPE) ? ($lis->LOCN_TYPE) : '0',
        ($lis->PS_TYPE) ? ($lis->PS_TYPE) : '0',
        ($lis->electors_male) ? ($lis->electors_male) : '0',
        ($lis->electors_female) ? ($lis->electors_female) : '0',
        ($lis->electors_other) ? ($lis->electors_other) : '0',
        ($lis->electors_total) ? ($lis->electors_total) : '0',
        ($lis->voter_male) ? ($lis->voter_male) : '0',
        ($lis->voter_female) ? ($lis->voter_female) : '0',
        ($lis->voter_other) ? ($lis->voter_other) : '0',
        ($lis->voter_total) ? ($lis->voter_total) : '0',

      ];

      $TotalElectorMale   += $lis->electors_male;
      $TotalElectorFeMale += $lis->electors_female;
      $TotalElectorOther  += $lis->electors_other;
      $TotalElector       += $lis->electors_total;
      $TotalVoterMale     += $lis->voter_male;
      $TotalVoterFeMale   += $lis->voter_female;
      $TotalVoterOther    += $lis->voter_other;
      $TotalVoter         += $lis->voter_total;

      $totalvalues = array('Total', '', '', '', $TotalElectorMale, $TotalElectorFeMale, $TotalElectorOther, $TotalElector, $TotalVoterMale, $TotalVoterFeMale, $TotalVoterOther, $TotalVoter);
    }

    $export_data[] = $totalvalues;

    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:L1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }
  //EXCEL REPORT ENDS


  public function RoPsWiseDetailsPdf(Request $request)
  {
    $data = $this->RoPsWiseDetails($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.polling_station.RoPsWiseDetailsPdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }

  public function RoPsWiseDetailsUpdate(Request $request)
  {
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $validationArray = [
          'voter_male'     => 'required|numeric|min:0|integer|between:0,9999',
          'voter_female'   => 'required|numeric|min:0|integer|between:0,9999',
          'voter_other'    => 'required|numeric|min:0|integer|between:0,9999',
          'voter_total'    => 'required|numeric|min:0|integer|between:0,9999',
          'ac_no'             => 'required|numeric',
          'psnoinput'             => 'required',
          'psccode'             => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $validationArray);
        if ($validator->fails()) {
          return Redirect::back()
            ->withErrors($validator)
            ->withInput();
        }
        $xss = new xssClean;
        $request              = $request->all();
        $voter_male           = $xss->clean_input($request['voter_male']);
        $voter_female         = $xss->clean_input($request['voter_female']);
        $voter_other          = $xss->clean_input($request['voter_other']);
        $voter_total          = $xss->clean_input($request['voter_total']);
        $psno                 = $xss->clean_input($request['psnoinput']);
        $ccode                = $xss->clean_input($request['psccode']);
        $ac_no                = $xss->clean_input($request['ac_no']);

        $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no, 'PS_NO' => $psno, 'CCODE' => $ccode];

        $currentPollingStation = PollingStation::where($PsWiseDetailsWhere)->first();
        // if($voter_male >  $currentPollingStation->electors_male || $voter_female >  $currentPollingStation->electors_female || $voter_other >  $currentPollingStation->electors_other){
        //   return Redirect::back()->with('error', 'Male, Female and Other Voters must be less than or equal to Electors Male, Female and Other respectively.');
        // }

        if (!in_array($user_data->st_code, ['S01', 'S18'])) {
          if ($voter_total >  $currentPollingStation->electors_total) {
            return Redirect::back()->with('error', 'Total Voters must be less than or equal to Electors total.');
          }
        }

        if ($voter_male + $voter_female + $voter_other != $voter_total) {
          return Redirect::back()->with('error', 'Data Mismatch in Voters Data.');
        }
        $update_fields = array(
          'voter_male'         => $voter_male,
          'voter_female'      => $voter_female,
          'voter_other'    => $voter_other,
          'voter_total'     => $voter_total,
        );

        PollingStation::where($PsWiseDetailsWhere)->update($update_fields);

        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station wise Voter Data Update';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = "Polling Station wise Voter Data Update for AC " . $ac_no . " PS NO " . $psno . " CCode " . $ccode;
        return Redirect::back()->with('success', 'Polling Station Data Updated Successfully !');
      } else {
        return redirect('/admin-login');
      }
    } catch (\Throwable $th) {
      $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
      $ErrorMessage['applicationType'] = 'WebApp';
      $ErrorMessage['Module'] = 'ENCORE';
      $ErrorMessage['TransectionType'] = 'VoterTurnout';
      $ErrorMessage['TransectionAction'] = 'Polling Station wise Voter Data Update';
      $ErrorMessage['TransectionStatus'] = 'Failed';
      $ErrorMessage['LogDescription'] = "Polling Station wise Voter Data Update is failed";
      return Redirect::back()->with('error', 'Polling Station Data Updated Failed !');
    }
  }


  public function RoPsFinalizeUpdate(Request $request)
  {
    try {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      if (session()->has('admin_login')) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $ac_no = $user_data->ac_no;
        $filter_election = [
          'state'         => $d->st_code,
          'ac_no'         => $d->ac_no,
        ];
        $ac_data         = PollingStationModel::get_ac_data($filter_election);
        $round = 'end';
        $m = $round . "_voter_male";
        $f = $round . "_voter_female";
        $o = $round . "_voter_other";
        $t = $round . "_voter_total";

        if ($ac_data) {
          $st = array(
            $m => $ac_data->voter_male,
            $f => $ac_data->voter_female,
            $o => $ac_data->voter_other,
            $t => $ac_data->voter_total,
            'updated_at' => date("Y-m-d H:i:s"),
            'added_update_at' => date("Y-m-d"),
            'updated_by' => $d->officername,
            'total_male' => $ac_data->voter_male,
            'total_female' => $ac_data->voter_female,
            'total_other' => $ac_data->voter_other,
            'total' => $ac_data->voter_total
          );
          DB::table('pd_scheduledetail')->where('st_code', $d->st_code)->where('ac_no', $d->ac_no)->update($st);
          $update_fields = array(
            'ro_ps_finalize_date'   => now(),
            'ro_ps_finalize'        => 1,
          );
          $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no];
          DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
          if (config("public_config.vt_log")) {
            $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
            $ErrorMessage['applicationType'] = 'WebApp';
            $ErrorMessage['Module'] = 'ENCORE';
            $ErrorMessage['TransectionType'] = 'VoterTurnout';
            $ErrorMessage['TransectionAction'] = 'Polling Station Wise Voter Tournout RO Finalize';
            $ErrorMessage['TransectionStatus'] = 'Success';
            $ErrorMessage['LogDescription'] = 'Polling Station Wise Voter Tournout is Finalize by RO';
            LogNotification::LogInfo($ErrorMessage);
          }
          return Redirect::back()->with('error', 'Polling Station Data Finalized Successfully !');
        } else {
          if (config("public_config.vt_log")) {
            $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
            $ErrorMessage['applicationType'] = 'WebApp';
            $ErrorMessage['Module'] = 'ENCORE';
            $ErrorMessage['TransectionType'] = 'VoterTurnout';
            $ErrorMessage['TransectionAction'] = 'Polling Station Wise Voter Tournout RO Finalize';
            $ErrorMessage['TransectionStatus'] = 'FAILED';
            $ErrorMessage['LogDescription'] = 'Polling Station Wise Voter Tournout is Finalize is Failed';
            LogNotification::LogInfo($ErrorMessage);
          }
          return Redirect::back()->with('error', 'Voter not not present, please update the voter data before finalize!');
        }
      } else {
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Polling Station Wise Voter Tournout RO Finalize';
          $ErrorMessage['TransectionStatus'] = 'FAILED';
          $ErrorMessage['LogDescription'] = 'User is not admin';
          LogNotification::LogInfo($ErrorMessage);
        }
        return redirect('/admin-login');
      }
    } catch (Exception $e) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station Wise Voter Tournout RO Finalize';
        $ErrorMessage['TransectionStatus'] = 'FAILED';
        $ErrorMessage['LogDescription'] = $e;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back()->with('error', 'Internal Server Error');
    }
  }
}  // end class