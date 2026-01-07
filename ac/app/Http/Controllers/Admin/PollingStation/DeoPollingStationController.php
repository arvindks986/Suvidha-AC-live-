<?php

namespace App\Http\Controllers\Admin\PollingStation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use \PDF;
use App\commonModel;
use App\models\Admin\PollDayModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;
use App\adminmodel\ACCEOModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use App\models\Admin\turnout\TurnoutModel;
//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Helpers\LogNotification;
//POLLING STATION MODELS
use App\models\Admin\polling_station\PollingStationModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//current

class DeoPollingStationController extends Controller
{

  public $action        = 'acdeo/turnout/DeoPsWiseDetails';
  public $view_path     = "admin.ac.deo";


  public function __construct()
  {
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->PollingStationModel = new PollingStationModel();
    $this->turnout = new TurnoutModel;
    $this->ceomodel = new ACCEOModel();
    if (!Auth::user()) {
      return redirect('/officer-login');
    }
  }



  public function DeoPsWiseDetails(Request $request)
  {

    $data = [];

    $xss = new xssClean;
    $default_phase = PhaseModel::get_current_phase();

    $request_array = [];

    $data['phases'] = PhaseModel::get_phases();

    //set title
    $title_array  = [];
    $data['heading_title'] = "PS Wise Voter Turnout";

    $data['ac_id'] = NULL;
    if ($request->has('ac_id')) {
      $data['ac_id'] = $request->ac_id;
    }


    $data['election_type'] = NULL;
    if ($request->has('election_type')) {
      $data['election_type'] = $request->election_type;
      $request_array[] =  'election_type=' . $request->election_type;
    }


    $filter_for_phases = [
      'election_type' => $data['election_type']
    ];

    $data['phases'] = PhaseModel::get_phases($filter_for_phases);
    $data['phase'] = NULL;
    if ($request->has('phase')) {
      if ($request->phase != 'all') {
        $data['phase'] = $request->phase;
      }
      $request_array[] =  'phase=' . $request->phase;
    } else {
      $data['phase']    = $default_phase;
      $request_array[]  =  'phase=' . $default_phase;
    }


    //end set title
    $data['user_data']  =   Auth::user();

    if (Auth::user()->designation == 'DEO') {
      $data['state'] = Auth::user()->st_code;
      $data['dist_no'] = Auth::user()->dist_no;
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


    //dd($data['phase']);

    $data['consituencies']  = AcModel::get_records([
      'state'         => $data['state'],
      'dist_no'         => $data['dist_no'],
      // 'phase' 		  => $data['phase']
    ]);
    //dd($data['consituencies']);

    //CHECKING REQUEST VARIABES STARTS
    if ($request->has('ac_id')) {

      $validator = Validator::make($request->all(), [
        //'ac_id'          => 'required|numeric',
      ]);

      if ($validator->fails()) {
        return Redirect::back()
          ->withErrors($validator)
          ->withInput();
      }

      $title_array[] = "State: " . $statename->ST_NAME;
      $title_array[] = "AC: " . $acame->AC_NAME;


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

      //$data['is_definalize']  = PollingStationModel::get_ps_finalize_ceo_data($filter_election);

      $is_finalize  = PollingStationModel::get_ps_finalize_data_deo($filter_election);
      if (count($is_finalize) > 0) {
        foreach ($is_finalize as $k => $v) {
          if ($v->deo_ps_finalize == 0) {
            $data['is_finalize'] = 0;
          } else {
            $data['is_finalize'] = 1;
          }
        }
      } else {
        $data['is_finalize'] = 0;
      }

      $is_finalize_ro  = PollingStationModel::get_ps_finalize_data_ro($filter_election);
      if (count($is_finalize_ro) > 0) {
        foreach ($is_finalize_ro as $k => $v) {
          if ($v->ro_ps_finalize == 0) {
            $data['is_finalize_ro'] = 0;
          } else {
            $data['is_finalize_ro'] = 1;
          }
        }
      } else {
        $data['is_finalize_ro'] = 0;
      }


      $is_finalize_ceo  = PollingStationModel::get_ps_finalize_data_ceo($filter_election);
      if (count($is_finalize_ceo) > 0) {
        foreach ($is_finalize_ceo as $k => $v) {
          if ($v->ps_finalize == 0) {
            $data['is_finalize_ceo'] = 0;
          } else {
            $data['is_finalize_ceo'] = 1;
          }
        }
      } else {
        $data['is_finalize_ceo'] = 0;
      }
      //dd($data['is_finalize']);


      $filter = [
        'st_code'       => $data['state'],
        'ac_no'         => $ac_id
      ];

      $lists = $this->turnout->get_scheduledetail($filter);


      $data['results']    =   $object;
      $data['ac_data'] = $ac_data;
      $data['lists'] = $lists;
    } else {


      $filter = [
        'st_code'       => $data['state'],
        'ac_no'         => $ac_id
      ];

      $lists = $this->turnout->get_scheduledetail($filter);




      $data['lists'] = $lists;
      $data['is_finalize'] = 0;
      $data['is_finalize_ceo'] = 0;
      $data['is_finalize_ro'] = 0;


      $data['buttons']    = [];
      $data['action']         = url($this->action);
      $data['results'] = [];

      $data['is_finalize']  = NULL;
      $data['is_definalize'] = NULL;

      $data['consituencies']  = AcModel::get_records([
        'state'         => $data['state'],
        'dist_no'    => $data['dist_no'],
      ]);
    }


    if ($request->has('is_excel')) {
      if (isset($title_array) && count($title_array) > 0) {
        $data['heading_title'] .= "- " . implode(', ', $title_array);
      }

      return $data;
    }

    //dd($data);
    return view($this->view_path . '.polling_station.DeoPsWiseDetails', $data);
  }


  //EXCEL REPORT STARTS
  public function DeoPsWiseDetailsExcel(Request $request)
  {

    set_time_limit(6000);
    $data = $this->DeoPsWiseDetails($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
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
    $totalvalues[] = [];
    $headings[] = [];
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


  public function DeoPsWiseDetailsPdf(Request $request)
  {
    $data = $this->DeoPsWiseDetails($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path . '.polling_station.DeoPsWiseDetailsPdf', $data);
    return $pdf->download($name_excel . '_' . date('d-m-Y') . '_' . time() . '.pdf');
  }




  public function DeoPsWiseDetailsUpdate(Request $request)
  {

    $users = Session::get('admin_login_details');
    $user = Auth::user();
    if (session()->has('admin_login')) {
      $uid = $user->id;

      $user_data = $this->commonModel->getunewserbyuserid($uid);

      $cur_time    = Carbon::now();

      //dd($ElectorsDetails);

      $cur_time  = Carbon::now();
      $st_code = $user_data->st_code;
      $st_name = $user_data->placename;
      //dd($AllPartyList);


      $validator = Validator::make($request->all(), [
        'electors_male'     => 'required|numeric|min:0|integer|between:0,9999',
        'electors_female'   => 'required|numeric|min:0|integer|between:0,9999',
        'electors_other'    => 'required|numeric|min:0|integer|between:0,9999',
        'electors_total'    => 'required|numeric|min:0|integer|between:0,9999',
        'voter_male'        => 'required|numeric|min:0|integer|between:0,9999',
        'voter_female'      => 'required|numeric|min:0|integer|between:0,9999',
        'voter_other'       => 'required|numeric|min:0|integer|between:0,9999',
        'voter_total'       => 'required|numeric|min:0|integer|between:0,9999',
        'ac_no'             => 'required|numeric',

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
      $voter_male           = $xss->clean_input($request['voter_male']);
      $voter_female         = $xss->clean_input($request['voter_female']);
      $voter_other          = $xss->clean_input($request['voter_other']);
      $voter_total          = $xss->clean_input($request['voter_total']);
      $psno                 = $xss->clean_input($request['psnoinput']);
      $ccode                = $xss->clean_input($request['psccode']);
      $ac_no                = $xss->clean_input($request['ac_no']);

      //ELECTORS DATA MATCHING STARTS
      /*if($voter_male > $electors_male){
           
             return Redirect::back()->with('error', 'Male Voter Data Should Be Equal or Less than Electors Male Data.');

            }

            if($voter_female > $electors_female){
           
              return Redirect::back()->with('error', 'Female Voter Data Should Be Equal or Less than Electors Female Data.');

            }

            if($voter_other > $electors_other){
           
             return Redirect::back()->with('error', 'Other Voter Data Should Be Equal or Less than Electors Other Data.');

            }

            if($voter_total > $electors_total){
           
             return Redirect::back()->with('error', 'Total Voter Data Should Be Equal or Less than Electors Total Data.');

            }*/

      if ($electors_male + $electors_female + $electors_other != $electors_total) {


        return Redirect::back()->with('error', 'Data Mismatch in Electors Data.');
      }
      //ELECTORS DATA MATCHING ENDS

      //VOTERS DATA MATCHING STARTS
      if ($voter_male + $voter_female + $voter_other != $voter_total) {

        return Redirect::back()->with('error', 'Data Mismatch in Voters Data.');
      }
      //VOTERS DATA MATCHING ENDS


      $update_fields = array(
        'electors_male'      => $electors_male,
        'electors_female'    => $electors_female,
        'electors_other'     => $electors_other,
        'electors_total'     => $electors_total,
        'voter_male'         => $voter_male,
        'voter_female'       => $voter_female,
        'voter_other'        => $voter_other,
        'voter_total'        => $voter_total,

      );

      $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no, 'PS_NO' => $psno, 'CCODE' => $ccode];

      $Data = DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);

      return Redirect::back()->with('error', 'Polling Station Data Updated Successfully !');
    } else {
      return redirect('/admin-login');
    }
  }


  public function DeoPsDefinalizeUpdate(Request $request)
  {
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $request     = $request->all();
      $ac_no = $request['ac_no'];

      $update_fields = array(
        'ro_ps_finalize_date'   => NULL,
        'ro_ps_finalize'        => 0,
        'deo_ps_finalize'        => 0,
      );
      $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no];
      DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station Wise Voter Tournout DEO Definalize';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = 'Polling Station Wise Voter Tournout is Definalize by DEO for AC ' . $ac_no;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back()->with('error', 'Polling Station Data definalized Successfully !');
    } catch (Exception $e) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station Wise Voter Tournout DEO Definalize';
        $ErrorMessage['TransectionStatus'] = 'FAILED';
        $ErrorMessage['LogDescription'] = $e;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back()->with('error', 'Polling Station Data definalized failed !');
    }
  }


  public function DeoPsFinalizeUpdate(Request $request)
  {
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $request     = $request->all();
      $ac_no = $request['ac_no'];
      $update_fields = array(
        'deo_ps_finalize'        => 1,
        'deo_ps_finalize_date'        => now()
      );
      $PsWiseDetailsWhere = ['st_code' => $user_data->st_code, 'ac_no' => $ac_no];
      DB::table('polling_station')->where($PsWiseDetailsWhere)->update($update_fields);
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station Wise Voter Tournout DEO Finalize';
        $ErrorMessage['TransectionStatus'] = 'Success';
        $ErrorMessage['LogDescription'] = 'Polling Station Wise Voter Tournout is Finalize by DEO for AC ' . $ac_no;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back()->with('error', 'Polling Station Data finalized Successfully !');
    } catch (Exception $e) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station Wise Voter Tournout DEO Finalize';
        $ErrorMessage['TransectionStatus'] = 'FAILED';
        $ErrorMessage['LogDescription'] = $e;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect::back()->with('error', 'Polling Station Data finalized failed !');
    }
  }
}  // end class