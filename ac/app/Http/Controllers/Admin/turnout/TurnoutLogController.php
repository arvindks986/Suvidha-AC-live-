<?php 
namespace App\Http\Controllers\Admin\turnout;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use \PDF;
use App\commonModel;  
use App\models\Admin\PollDayModel;
use App\models\Admin\ElectorModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;
use App\Classes\xssClean;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class TurnoutLogController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/turnout/turnout-log';
  public $action_ac     = 'eci/turnout/turnout-log';
  public $view_path     = "admin.turnout";

  public function __construct(){
    //$this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->xssClean = new xssClean;
    $this->middleware(function ($request, $next) {
        
        return $next($request);
    });
  }

  

  public function turnout_log(Request $request){


      $data = [];
      $default_phase = PhaseModel::get_current_phase();

      $request_array = []; 
	  
	  
	  $data['election_type'] = NULL;
	  if($request->has('election_type')){     
        $data['election_type'] = $this->xssClean->clean_input($request->election_type);
        $request_array[] =  'election_type='.$this->xssClean->clean_input($request->election_type);
      }
	  
	  
	  $filter_for_phases = [
        'election_type' => $data['election_type']
      ];
	  
      $data['phases'] = PhaseModel::get_phases($filter_for_phases);

      $data['phase'] = NULL;
      if($request->has('phase')){
        if($this->xssClean->clean_input($request->phase) != 'all'){
          $data['phase'] = $this->xssClean->clean_input($request->phase);
        }
        $request_array[] =  'phase='.$this->xssClean->clean_input($request->phase);
      }else{
        $data['phase']    = $default_phase;
        $request_array[]  =  'phase='.$default_phase; 
      }

      $data['state'] = NULL;
      if($request->has('state')){

        //valid a state is exist in the current filter phase
        $is_state_valid = StateModel::get_pc_states_with_filter([
          'state' => base64_decode($request->state),
          'phase' => $data['phase']
        ]);

        if(count($is_state_valid)>0){
          $data['state'] = base64_decode($request->state);
          $request_array[] = 'state='.$request->state;
        }

      }
	  
      //set title
      $title_array  = [];
      $data['heading_title'] = 'Turnout Log';

      // if($data['phase']){
      //   $title_array[] = "Phase: ".$data['phase'];
      // }

      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }


      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        'election_type' => $data['election_type'],
        'phase' => $data['phase']
      ];

      $states = StateModel::get_pc_states_with_filter($filter_for_state); 

      $data['states'] = [];
      //STATE LISTR STARTS


       //FOR ECI
        foreach($states as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
       }

    //STATE LIST ENDS

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
	  
	  
	  
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_ac).'?excel=yes&'.implode('&', $request_array),
        'target' => true
      ];
      /* $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_ac).'?pdf=yes&'.implode('&', $request_array),
        'target' => true
      ]; */

      $data['action']         = url($this->action_ac);

      $data['consituencies']  = AcModel::get_records([
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase']
      ]);

      $results                = [];

      $filter_election = [
        'election_type'         => $data['election_type'],
        'state'         => $data['state'],
        'phase'         => $data['phase'],
        'group_by'      => 'ac_no',
        'order_by'      => 'ac_no',
      ];


	$state = $phase = '';
	$state = $data['state'];
	
	$phase = PhaseModel::select('DATE_POLL')->leftjoin('m_schedule as ms',[
              ['ms.SCHEDULENO', '=','PHASE_NO']
        ])->where('PHASE_NO',$data['phase'])->first();
	
	
	$phase = @$phase->DATE_POLL;


	$resultData= DB::select("SELECT a1.st_code,a1.ac_no,
	a1.b est_turnout_total_3_TO_5PM,a1.a est_log_date_3_TO_5PM,
	a2.b est_turnout_total_530_TO_7PM,a2.a est_log_date_530_TO_7PM,
	a3.b est_turnout_total_5_TO_7PM,a3.a est_log_date_5_TO_7PM,
	a4.b est_turnout_total_7_TO_9PM,a4.a est_log_date_7_TO_9PM,
	a5.b est_turnout_total_9_TO_11PM,a5.a est_log_date_9_TO_11PM,
	a6.b est_turnout_total_11_TO_12PM,a6.a est_log_date_11_TO_12PM FROM
(SELECT st_code,ac_no,c.a,c.b FROM(
SELECT st_code,ac_no,MAX(log_update_date_time)AS a,MAX(est_turnout_total)AS b FROM pd_scheduledetail_log WHERE st_code='$state'
AND log_update_date_time>='$phase 15:00:00' AND log_update_date_time<='$phase 17:00:00'
GROUP BY log_update_date_time,est_turnout_total ORDER BY est_turnout_total DESC, log_update_date_time DESC )AS c GROUP BY ac_no) AS a1
LEFT JOIN
(SELECT ac_no,c.a,c.b FROM(
SELECT ac_no,MAX(log_update_date_time)AS a,MAX(est_turnout_total)AS b FROM pd_scheduledetail_log WHERE st_code='$state'
AND log_update_date_time>='$phase 15:00:00' AND log_update_date_time<='$phase 17:30:00'
GROUP BY log_update_date_time,est_turnout_total ORDER BY est_turnout_total DESC, log_update_date_time DESC )AS c GROUP BY ac_no) AS a2
ON a1.ac_no=a2.ac_no  LEFT JOIN
 (SELECT ac_no,c.a,c.b FROM(
SELECT ac_no,MAX(log_update_date_time)AS a,MAX(est_turnout_total)AS b FROM pd_scheduledetail_log WHERE st_code='$state'
AND log_update_date_time>='$phase 15:00:00' AND log_update_date_time<='$phase 19:00:00'
GROUP BY log_update_date_time,est_turnout_total ORDER BY est_turnout_total DESC, log_update_date_time DESC )AS c GROUP BY ac_no) AS a3
ON a1.ac_no=a3.ac_no  LEFT JOIN
(SELECT ac_no,c.a,c.b FROM(
SELECT ac_no,MAX(log_update_date_time)AS a,MAX(est_turnout_total)AS b FROM pd_scheduledetail_log WHERE st_code='$state'
AND log_update_date_time>='$phase 15:00:00' AND log_update_date_time<='$phase 21:00:00'
GROUP BY log_update_date_time,est_turnout_total ORDER BY est_turnout_total DESC, log_update_date_time DESC )AS c GROUP BY ac_no ) AS a4
ON a1.ac_no=a4.ac_no  LEFT JOIN
(SELECT ac_no,c.a,c.b FROM(
SELECT ac_no,MAX(log_update_date_time)AS a,MAX(est_turnout_total)AS b FROM pd_scheduledetail_log WHERE st_code='$state'
AND log_update_date_time>='$phase 15:00:00' AND log_update_date_time<='$phase 23:00:00'
GROUP BY log_update_date_time,est_turnout_total ORDER BY est_turnout_total DESC, log_update_date_time DESC )AS c GROUP BY ac_no ) AS a5
ON a1.ac_no=a5.ac_no  LEFT JOIN
(SELECT ac_no,c.a,c.b FROM(
SELECT ac_no,MAX(log_update_date_time)AS a,MAX(est_turnout_total)AS b FROM pd_scheduledetail_log WHERE st_code='$state'
AND log_update_date_time>='$phase 15:00:00' AND log_update_date_time<='$phase 23:59:59'
GROUP BY log_update_date_time,est_turnout_total ORDER BY est_turnout_total DESC, log_update_date_time DESC )AS c GROUP BY ac_no ) AS a6
ON a1.ac_no=a6.ac_no");

//$data['result'] = $resultData;


//dd($resultData);

 
         foreach ($resultData as $result) { 


          $ac_name    = '';
          $get_ac     = AcModel::get_record([
            'state' => $result->st_code,
            'ac_no' => $result->ac_no
          ]);

          if($get_ac){
            $ac_name = $get_ac['ac_name'];
          }

          $state_name = '';
          $state_object = StateModel::get_state_by_code($result->st_code);
          if($state_object){
            $state_name = $state_object['ST_NAME'];
          }

          $results[] = [
            'label'                 => $state_name,
            'const_no'              => $result->ac_no,
            'const'                 => $ac_name,
            "est_turnout_total_3_TO_5PM"      => $result->est_turnout_total_3_TO_5PM,
            "est_log_date_3_TO_5PM"      => $result->est_log_date_3_TO_5PM,
            "est_turnout_total_530_TO_7PM"      => $result->est_turnout_total_530_TO_7PM,
            "est_log_date_530_TO_7PM"      => $result->est_log_date_530_TO_7PM,
            "est_turnout_total_5_TO_7PM"      => $result->est_turnout_total_5_TO_7PM,
            'est_log_date_5_TO_7PM'         => $result->est_log_date_5_TO_7PM,
            "est_turnout_total_7_TO_9PM"             => $result->est_turnout_total_7_TO_9PM,
            "est_log_date_7_TO_9PM"          => $result->est_log_date_7_TO_9PM,
            "est_turnout_total_9_TO_11PM"        => $result->est_turnout_total_9_TO_11PM,
            "est_log_date_9_TO_11PM"            => $result->est_log_date_9_TO_11PM,
            "est_turnout_total_11_TO_12PM"            => $result->est_turnout_total_11_TO_12PM,
            "est_log_date_11_TO_12PM"            => $result->est_log_date_11_TO_12PM

          ];      

      }   
	    
      if($data['state']){
        $group_by = 'state';
      }else{
        $group_by = NULL;
      }

 
      $data['results']    =   $results;

      $data['user_data']  =   Auth::user();

       $data['heading_title_with_all'] = $data['heading_title'];



		if($request->excel == 'yes'){
			
			$export_data = [];
			$export_data[] = [$data['heading_title']];
			
			 $export_data[] = ['State','AC No' ,'AC Name', 'Turnout 3 PM TO 5 PM ','Time 3 PM TO 5 PM','Turnout 5 PM TO 5:30 PM ','Time 5 PM TO 5:30 PM','Turnout 5:30 PM TO 7 PM ','Time 5:30 PM TO 7 PM','Turnout 7 PM TO 9 PM ','Time 7 PM TO 9 PM','Turnout 9 PM TO 11 PM ','Time 9 PM TO 11 PM','Turnout 11 PM TO 12 AM ','Time 11 PM TO 12 AM'];
			$headings[]=[];
			foreach ($data['results'] as $lis) {
			  $export_data[] = [
				$lis['label'],
				$lis['const_no'],
				$lis['const'],
				$lis['est_turnout_total_3_TO_5PM'],
				$lis['est_log_date_3_TO_5PM'],
				$lis['est_turnout_total_530_TO_7PM'],
				$lis['est_log_date_530_TO_7PM'],
				$lis['est_turnout_total_5_TO_7PM'],
				$lis['est_log_date_5_TO_7PM'],
				$lis['est_turnout_total_7_TO_9PM'],
				$lis['est_log_date_7_TO_9PM'],
				$lis['est_turnout_total_9_TO_11PM'],
				$lis['est_log_date_9_TO_11PM'],
				$lis['est_turnout_total_11_TO_12PM'],
				$lis['est_log_date_11_TO_12PM']
			  ];
			}

		//dd($export_data);
			$name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

			return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
			
		}




      return view($this->view_path.'.log.turnout-log', $data);

    try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }
  }

 
  //export AC's
  public function export_excel_report_comparision(Request $request){

    set_time_limit(6000);
    $data = $this->report_comparision($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    //$export_data[] = ['State', 'PC No' ,'PC Name','AC No' ,'AC Name','Turnout % (2014)', 'Round1 %(Poll Start to 9:00 AM)','Round2 %(Poll Start to 11:00 AM)','Round3 %(Poll Start to 1:00 PM)', 'Round4 %(Poll Start to 3:00 PM)','Round5 %(Poll Start to 5:00 PM)', 'Latest Updated Poll %(2019)','Change from 2014'];

     $export_data[] = ['State','AC No' ,'AC Name', 'Previous Election TURNOUT (in %)','2020 Total Elector','2020 Total Voter', '2020 TURNOUT (in %)','Change from Previous Election'];
    $headings[]=[];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['const_no'],
        $lis['const'],
        ($lis['old_total_percentage'])?$lis['old_total_percentage']:'0',
        ($lis['electors_total'])?$lis['electors_total']:'0',
        ($lis['est_voters'])?$lis['est_voters']:'0',
        ($lis['est_total'])?$lis['est_total']:'0',
        ($lis['difference'])?$lis['difference']:'0',
      ];
    }

//dd($export_data);
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:I1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_comparision(Request $request){
    $data = $this->report_comparision($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.comparision.end_of_poll_comparision_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }


  


}  // end class