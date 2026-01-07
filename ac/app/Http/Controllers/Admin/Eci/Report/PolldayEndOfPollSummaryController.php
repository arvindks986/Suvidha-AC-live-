<?php namespace App\Http\Controllers\Admin\Eci\Report;
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
use App\models\Admin\EndOfPollSummaryModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;

//current

class PolldayEndOfPollSummaryController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/report/voting/end-of-poll-summary';
  public $action_pc     = 'eci/report/voting/end-of-poll-summary/state';
  public $action_ac     = 'eci/report/voting/end-of-poll-summary/state/pc';
  public $view_path     = "admin.ac.eci";

  public function __construct(){
    $this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    if(!Auth::user()){
      return redirect('/officer-login');
    }
  }

  public function report_state(Request $request){
    
      $data                     = [];
      $data['number_of_voting'] = 0;
      $data['phase']            = NULL;
      $data['active_phases']    = config('public_config.phases');

      $data['years'] = [];
      $data['years'][] = [
        'id' => '',
        'name' => 'Select',
      ];
      $data['years'][] = [
        'id' => 'all',
        'name' => 'Comparision 2014-2019',
      ];
      $data['years'][] = [
        'id' => 2014,
        'name' => '2014',
      ];
      $data['years'][] = [
        'id' => 2019,
        'name' => '2019',
      ];

      $request_array = []; 
    
      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      $data['year'] = NULL;
      if($request->has('year')){
        $data['year'] = $request->year;
        $request_array[] = 'year='.$request->year;
      }

      
      //set title
      $title_array  = [];
      $data['heading_title'] = 'Phasewise total Summary';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }
      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }

      if($data['year']){
        if($data['year']=='all'){
          $title_array[] = "Year: Comparision 2014-2019";
        }else{
          $title_array[] = "Year: ".$data['year'];
        }
      }
    
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
    
      $data['filter_buttons'] = $title_array;

      $states = StateModel::get_phasewise_states(); 

      $data['states'] = [];
      foreach($states as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];

      $data['action']         = url($this->action_state);

      $results                = [];
      $filter_election = [
        'state'         => $data['state'],
        'phase'         => $data['phase'],
      ];

      foreach ($data['states'] as $result) {

          $individual_filter_array          = [];
          $individual_filter_array['state'] = 'state='.$result['code'];
          $individual_filter                = implode('&', $individual_filter_array);
          
          $percentage_phase = [];
          $percentage_phase['label'] = $result['name']; 
          foreach($data['active_phases'] as $i) {

            $phase_2014    = EndOfPollSummaryModel::get_percentage_2014([
              'state' => base64_decode($result['code']),
              'phase'     => $i,
              'group_by'  => 'state'
            ]);

            $phase_2019    = EndOfPollSummaryModel::get_percentage_2019([
              'state' => base64_decode($result['code']),
              'phase'     => $i,
              'group_by'  => 'state'
            ]);

            $percentage_phase['phase'.$i]['2014'] = $phase_2014['total_percentage'];
            $percentage_phase['phase'.$i]['2019'] = $phase_2019['total_percentage'];

          }

          //total phase aggregate
          $phase_2014    = EndOfPollSummaryModel::get_percentage_2014([
            'state'     => base64_decode($result['code']),
            'group_by'  => 'state'
          ]);

          $phase_2019    = EndOfPollSummaryModel::get_percentage_2019([
              'state'     => base64_decode($result['code']),
              'group_by'  => 'state'
          ]);

          $percentage_phase['total']['2014'] = $phase_2014['total_percentage'];
          $percentage_phase['total']['2019'] = $phase_2019['total_percentage'];
         
          $results[] = array_merge([
            'filter'              => $individual_filter,
            "href"                => url($this->action_pc)."?".$individual_filter
          ],$percentage_phase);   

      }   
 
      //calculate total
      $percentage_phase = [];
      $percentage_phase['label'] = "Total"; 
      foreach($data['active_phases'] as $i) {
        $phase_2014    = EndOfPollSummaryModel::get_percentage_2014([
          'phase'     => $i,
          'group_by'  => 'national'
        ]);
        $phase_2019    = EndOfPollSummaryModel::get_percentage_2019([
          'phase'     => $i,
          'group_by'  => 'national'
        ]);
        $percentage_phase['phase'.$i]['2014'] = $phase_2014['total_percentage'];
        $percentage_phase['phase'.$i]['2019'] = $phase_2019['total_percentage'];
      }
      //total phase aggregate
      $phase_2014    = EndOfPollSummaryModel::get_percentage_2014([
        'group_by'  => 'national'
      ]);
      $phase_2019    = EndOfPollSummaryModel::get_percentage_2019([
        'group_by'  => 'national'
      ]);
      $percentage_phase['total']['2014'] = $phase_2014['total_percentage'];
      $percentage_phase['total']['2019'] = $phase_2019['total_percentage'];

      $data['totals'] = array_merge([
        'filter'              => '',
        'href'                => ''
      ],$percentage_phase);

      $data['number_of_voting'] = $phase_2019['total_percentage'];
      //end total

      $data['results']    =   $results;
      $data['user_data']  =   Auth::user();
      $data['heading_title_with_all'] = $data['heading_title'];
      
      $data['phase_2014'] = false;
      $data['phase_2019'] = false;

      if($request->has('year') && $request->year == 2014){
        $data['phase_2014'] = true;
        $data['phase_2019'] = false;
      }else if($request->has('year') && $request->year == 2019){
        $data['phase_2014'] = false;
        $data['phase_2019'] = true;
      }else if($request->has('year') && $request->year == 'all'){
        $data['phase_2014'] = true;
        $data['phase_2019'] = true;
      }else{
        $data['phase_2019'] = true;
      }

      if(!$request->has('year')){
        $data['phase_2019'] = true;
      }

      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.pollday.end_of_poll_summary.state', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }

  public function export_excel_report_state(Request $request){

    set_time_limit(6000);
    $data = $this->report_state($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $labels = [];
    $labels[] = "State";
    foreach ($data['active_phases'] as $i) {
      if(isset($data['phase_2014']) && $data['phase_2014']){
        $labels[] = "2014 Voting Phase ".$i. "(in %)";
      }
      if(isset($data['phase_2019']) && $data['phase_2019']){
        $labels[] = "2019 Voting Phase ".$i. "(in %)";
      }
    }

    if(isset($data['phase_2014']) && $data['phase_2014']){
        $labels[] = "Total 2014 Voting Phase ".$i. "(in %)";
      }
      if(isset($data['phase_2019']) && $data['phase_2019']){
        $labels[] = "Total 2019 Voting Phase ".$i. "(in %)";
      }

    
    $export_data[] = $labels;

    foreach ($data['results'] as $lis) {
      $labels = [];
      $labels[] = $lis['label'];
      foreach ($data['active_phases'] as $i) {
        if(isset($data['phase_2014']) && $data['phase_2014']){
          $labels[] = $lis['phase'.$i]['2014'];
        }
        if(isset($data['phase_2019']) && $data['phase_2019']){
          $labels[] = $lis['phase'.$i]['2019'];
        }
      }
      if(isset($data['phase_2014']) && $data['phase_2014']){
        $labels[] = $lis['total']['2014'];
      }
      if(isset($data['phase_2019']) && $data['phase_2019']){
        $labels[] = $lis['total']['2019'];
      }
      $export_data[] = $labels;
    }

    $labels = [];
    $labels[] = $data['totals']['label'];
    foreach ($data['active_phases'] as $i) {
      if(isset($data['phase_2014']) && $data['phase_2014']){
        $labels[] = $data['totals']['phase'.$i]['2014'];
      }
      if(isset($data['phase_2019']) && $data['phase_2019']){
        $labels[] = $data['totals']['phase'.$i]['2019'];
      }
    }
    if(isset($data['phase_2014']) && $data['phase_2014']){
      $labels[] = $data['totals']['total']['2014'];
    }
    if(isset($data['phase_2019']) && $data['phase_2019']){
      $labels[] = $data['totals']['total']['2019'];
    }
    
    $export_data[] = $labels;

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:I1');
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');

  }

  public function export_pdf_report_state(Request $request){
    $data = $this->report_state($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.pollday.end_of_poll_summary.state_pdf',$data, 
        [], 
        [ 
          'title' => '', 
          'format' => 'A4-L',
          'orientation' => 'L'
        ]);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }

}  // end class