<?php  
namespace App\Http\Controllers\Admin\counting;
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
use \MPDF;
use \PDF;
use App\commonModel;
use Illuminate\Support\Facades\Schema;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use App\models\Counting\BoothCountingModel;
use App\adminmodel\ACCountingModel;
use App\models\Counting\UsercountingModel;  
use App\models\Admin\StateModel;
use App\models\Admin\AcModel;
use App\models\Counting\BoothDistricts;
use App\models\Counting\BoothCountingTableModel;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class BoothCountingTableReportController  extends Controller{

    public $base          = 'eci';
    public $folder        = 'counting';
    public $action_state  = 'eci/counting/report_state';
    public $action_ac     = 'eci/counting/report_state/state/ac';
    public $view_path     = "admin.counting.reports";

    public function __construct(){  
        $this->middleware(['auth:admin','auth']);
        //$this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->boothcounting=new BoothCountingModel;
        $this->users=new UsercountingModel;
        $this->CountingModel = new ACCountingModel();
        
        if(!Auth::check()){ 
          return redirect('/officer-login');
        }
    }

    protected function guard(){
      return Auth::guard('admin');
    }
    
    //STATE WISE REPORT FOR TABLES STARTS
    public function report_state(Request $request){
            
      //CEO USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if(Auth::user()->role_id == '4'){
        $this->action_state  = 'acceo/counting/report_state';
        $this->action_ac     = 'acceo/counting/report_state/state/ac';
      }

      //DEO USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if(Auth::user()->role_id == '5'){
        $this->action_state  = 'acdeo/counting/report_state/state/ac';
        $this->action_ac     = 'acdeo/counting/report_state/state/ac';
      }

      //RO USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if(Auth::user()->role_id == '19'){
        $this->action_state  = 'acceo/counting/report_state/state/ac';
        $this->action_ac     = 'acceo/counting/report_state/state/ac';
      }

      $data = [];
      $request_array = []; 

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      $data['district'] = NULL;
      if($request->has('district')){
        $data['district'] = base64_decode($request->state);
        $request_array[] = 'district='.$request->district;
      }

      $data['ac_no'] = NULL;
      if($request->has('ac_no')){
        $data['ac'] = base64_decode($request->state);
        $request_array[] = 'ac_no='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Table Scheduled';
      
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }

      if($data['district']){
        $dist_object = BoothDistricts::get_district($data['state'],$data['district']);
        if($dist_object){
          $title_array[]  = "District: ".$dist_object['dist_name'];
        }
      }

      if($data['ac_no']){
        $ac_object = AcModel::get_record($data['state'],$data['ac_no']);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
      }

      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        //'phase' => $data['state']
      ];

      $states = StateModel::get_pc_states_with_filter($filter_for_state); 

      $data['states'] = [];
      //STATE LIST STARTS
      foreach($states as $result){

        //FOR CEO DEO and RO
      if(Auth::user()->role_id == '4' || Auth::user()->role_id == '5' || Auth::user()->role_id == '19'){
        $st_object = StateModel::get_state_by_code(Auth::user()->st_code);
        $data['states'][] = [
          'code' => base64_encode(Auth::user()->st_code),
          'name' => $st_object['ST_NAME'],
        ];
      }else {
       //FOR ECI
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }
      
    }
    //STATE LIST ENDS

    $data['filter']   = implode('&', array_merge($request_array));

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
    ];
    
   
    //CEO RECORD
    if(Auth::user()->role_id == '4'){

     $object    = BoothCountingTableModel::get_reports([
        'state'     => Auth::user()->st_code,
        'group_by'  => 'eci',
        'order_by'  => 'eci'
      ]);

    }else if(Auth::user()->role_id == '5') {
        //DEO RECORDS
        $object    = BoothCountingTableModel::get_reports([
        'state'     => Auth::user()->st_code,
        'district'  => Auth::user()->dist_no,
        'group_by'  => 'dist_no',
        'order_by'  => 'dist_no'
      ]);
    }else if(Auth::user()->role_id == '19') {
        //RO RECORDS
        $object    = BoothCountingTableModel::get_reports([
        'state'     => Auth::user()->st_code,
        'district'  => Auth::user()->dist_no,
        'ac'        => Auth::user()->ac_no,
        'group_by'  => 'ac_no',
        'order_by'  => 'ac_no'
      ]);
    }else {
        //ECI RECORDS

      $object    = BoothCountingTableModel::get_reports([
        'eci'       => 'eci',
        'group_by'  => 'eci',
        'order_by'  => 'eci'
      ]);   
    }
   
    $GrandTotalPs          = 0;
    $GrandTotalTables      = 0;
    $GrandTotalAssigned    = 0;
    $GrandTotalNotAssigned = 0;
    $GrandTotalRound       = 0;
  

    foreach ($object as $result) {

      $individual_filter_array = [];
      $individual_filter_array['state'] = 'state='.base64_encode($result->st_code);
      $individual_filter    = implode('&', $individual_filter_array);

     $assign_table    = BoothCountingTableModel::getallassigntable([
        'st_code'  =>  $result->st_code,
      ]);
           
         $total_not_assigned = $result->total_tables - $assign_table['countassigntable'];

          $results[] = [
            'label'                => $result->state_name,
            "st_code"              => $result->st_code,
            'filter'               => $individual_filter,
            "total_ps"             => ($result->total_ps)?$result->total_ps:'0',
            "total_tables"         => ($result->total_tables)?$result->total_tables:'0',
            "total_assigned"       => $assign_table['countassigntable'],
            "total_not_assigned"   => $total_not_assigned,
            "total_rounds"         => ($result->total_rounds)?$result->total_rounds:'0',
            "href"                 => url($this->action_ac)."?".$individual_filter
          ];      

        $GrandTotalPs          +=   $result->total_ps;
        $GrandTotalTables      +=   $result->total_tables;
        $GrandTotalAssigned    +=   $assign_table['countassigntable'];
        $GrandTotalNotAssigned +=   $total_not_assigned;
        $GrandTotalRound       +=   $result->total_rounds;

      } 

      //$data['GrandTotal'] = array('Total','',$GrandTotalPs,$GrandTotalTables,$GrandTotalAssigned,$GrandTotalRound);

      $data['GrandTotal'] = [
            'label'                  => 'Total',
            "GrandTotalPs"           => $GrandTotalPs,
            "GrandTotalTables"       => $GrandTotalTables,
            "GrandTotalAssigned"     => $GrandTotalAssigned,
            "GrandTotalNotAssigned"  => $GrandTotalNotAssigned,
            "GrandTotalRound"        => $GrandTotalRound,
        ];


    $data['results']    =   $results;

    $data['user_data']  =   Auth::user();

    $data['heading_title_with_all'] = $data['heading_title'];

    if($request->has('is_excel')){
    if(isset($title_array) && count($title_array)>0){
      $data['heading_title'] .= "- ".implode(', ', $title_array);
    }
    return $data;
    }
   // dd($data);
    return view($this->view_path.'.report_state', $data);
    }
    //STATE WISE REPORT FOR TABLES ENDS

    public function export_excel_report_state(Request $request){

    set_time_limit(6000);
    $data = $this->report_state($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['', '','Table','Scheduled','',''];

    $export_data[] = ['State', 'Total Polling Stations','Total Tables','Total Assigned
', 'Total Rounds'];
    
    $GrandTotalPs          = 0;
    $GrandTotalTables      = 0;
    $GrandTotalAssigned    = 0;
    $GrandTotalNotAssigned = 0;
    $GrandTotalRound       = 0;
      $headings[]=[];

    foreach ($data['results'] as $lis) {

      $export_data[] = [
        $lis['label'],
        ($lis['total_ps'])?$lis['total_ps']:'0',
        ($lis['total_tables'])?$lis['total_tables']:'0',
        ($lis['total_assigned'])?$lis['total_assigned']:'0',
         
        ($lis['total_rounds'])?$lis['total_rounds']:'0',
      ];

        $GrandTotalPs         +=   $lis['total_ps'];
        $GrandTotalTables     +=   $lis['total_tables'];
        $GrandTotalAssigned   +=   $lis['total_assigned'];
        $GrandTotalRound      +=   $lis['total_rounds'];
    }


    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:E1');
    //       $sheet->mergeCells('B2:E2');
    //       $sheet->mergeCells('F2:I2');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_state(Request $request){
    $data = $this->report_state($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.report_state_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }


   public function report_ac(Request $request){
    
    //CEO USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if(Auth::user()->role_id == '4'){
        $this->action_state  = 'acceo/counting/report_state/';
        $this->action_ac     = 'acceo/counting/report_state/state/ac';
      }

      //DEO USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if(Auth::user()->role_id == '5'){
        $this->action_state  = 'acdeo/counting/report_state/state/ac';
        $this->action_ac     = 'acdeo/counting/report_state/state/ac';
      }

      //RO USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if(Auth::user()->role_id == '19'){
        $this->action_state  = 'roac/counting/report_state/state/ac';
        $this->action_ac     = 'roac/counting/report_state/state/ac';
      }

      $data = [];
      $request_array = []; 

      $data['state'] = NULL;
      if($request->has('state')){
          $data['state'] = base64_decode($request->state);
          $request_array[] = 'state='.$request->state;
        }


      if(Auth::user()->designation=='CEO'){
        $data['state'] = Auth::user()->st_code;
      }

      $data['district'] = NULL;
      if($request->has('district')){
        $data['district'] = base64_decode($request->district);
        $request_array[] = 'district='.$request->district;
      }

      $data['ac_no'] = NULL;
      if($request->has('ac_no')){
        $data['ac_no'] = base64_decode($request->ac_no);
        $request_array[] = 'ac_no='.$request->ac_no;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Table Scheduled';
      
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }

      if($data['district']){

        $filter_array = [
          'state' => $data['state'],
          'district' => $data['district']
        ];

        $dist_object = BoothDistricts::get_district($data['state'],$data['district']);
        if($dist_object){
          $title_array[]  = "District: ".$dist_object['dist_name'];
        }
      }

      if($data['ac_no']){
        
        $filter_array = [
        'state' => $data['state'],
        'ac_no' => $data['ac_no']
        ];

        $ac_object = AcModel::get_record($filter_array);
        if($ac_object){
          $title_array[]  = "AC: ".$ac_object['ac_name'];
        }
      }

      $data['filter_buttons'] = $title_array;

      $filter_for_state = [
        //'state' => $data['state']
      ];

      $states = StateModel::get_pc_states_with_filter($filter_for_state); 

      $data['states'] = [];
      //STATE LIST STARTS

      //FOR CEO DEO and RO
      //if(Auth::user()->role_id == '4' || Auth::user()->role_id == '5' || Auth::user()->role_id == '19')
      if(Auth::user()->role_id == '4'){
        
        $st_object = StateModel::get_state_by_code(Auth::user()->st_code);
        $data['states'][] = [
          'code' => base64_encode(Auth::user()->st_code),
          'name' => $st_object['ST_NAME'],
        ];
      
      }else if(Auth::user()->role_id == '5'){
        $st_object = StateModel::get_state_by_code(Auth::user()->st_code);
        $data['states'][] = [
          'code' => base64_encode(Auth::user()->st_code),
          'name' => $st_object['ST_NAME'],
        ];

      }else if(Auth::user()->role_id == '19'){
        $st_object = StateModel::get_state_by_code(Auth::user()->st_code);
        $data['states'][] = [
          'code' => base64_encode(Auth::user()->st_code),
          'name' => $st_object['ST_NAME'],
        ];

      }else {
       //FOR ECI
        foreach($states as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
       }
      }
      
    //STATE LIST ENDS
    $data['filter']   = implode('&', array_merge($request_array));

    //buttons
    $data['buttons']    = [];
    
    if(Auth::user()->role_id == '4' || Auth::user()->role_id == '7'){
          $data['buttons'][]  = [
        'name' => 'All States Report',
        'href' =>  url($this->action_state),
        'target' => false
        ];

    }
    
    $data['buttons'][]  = [
    'name' => 'Export Excel',
    'href' =>  url($this->action_ac.'/excel').'?'.implode('&', $request_array),
    'target' => true
    ];

    $data['buttons'][]  = [
    'name' => 'Export Pdf',
    'href' =>  url($this->action_ac.'/pdf').'?'.implode('&', $request_array),
    'target' => true
    ];

    $data['action']         = url($this->action_ac).'';

    $results                = [];

    $filter_election = [
    'state'         => $data['state'],
    ];
    
   
    //CEO RECORD
    if(Auth::user()->role_id == '4'){
     if(!empty($data['ac_no'])){

         $object    = BoothCountingTableModel::get_reports([
            'state'     => Auth::user()->st_code,
            'ac_no'     =>$data['ac_no'],
            'group_by'  => 'ac',
            'order_by'  => 'ac'
          ]);
     }else{

      $object    = BoothCountingTableModel::get_reports([
            'state'     => Auth::user()->st_code,
            'group_by'  => 'state',
            'order_by'  => 'state'
          ]);
     }

    }else if(Auth::user()->role_id == '5') {
        //DEO RECORDS
      if(!empty($data['ac_no'])){
            $object    = BoothCountingTableModel::get_reports([
            'state'     => Auth::user()->st_code,
            'district'  => Auth::user()->dist_no,
            'ac_no'  => $data['ac_no'],
            'group_by'  => 'ac',
            'order_by'  => 'ac'
          ]);
      }else{

        $object    = BoothCountingTableModel::get_reports([
            'state'     => Auth::user()->st_code,
            'district'  => Auth::user()->dist_no,
            'group_by'  => 'dist_no',
            'order_by'  => 'dist_no'
          ]);

      }
    }else if(Auth::user()->role_id == '19') {
        //RO RECORDS
        $object    = BoothCountingTableModel::get_reports([
        'state'     => Auth::user()->st_code,
        'ac_no'     => Auth::user()->ac_no,
        'group_by'  => 'ac',
        'order_by'  => 'ac'
      ]);
    }else {
        //ECI RECORDS
        if(!empty($data['ac_no'])){
          $object    = BoothCountingTableModel::get_reports([
            'state'     => $data['state'],
            'ac_no'     => $data['ac_no'],
            'group_by'  => 'ac',
            'order_by'  => 'ac'
          ]);
        }else{

           $object    = BoothCountingTableModel::get_reports([
            'state'     => $data['state'],
            'group_by'  => 'ac',
            'order_by'  => 'ac'
          ]);
        }
        
    }

    $GrandTotalPs          = 0;
    $GrandTotalTables      = 0;
    $GrandTotalAssigned    = 0;
    $GrandTotalNotAssigned = 0;
    $GrandTotalRound       = 0;
    
            
    
    $data['assemblys'] = null;
    $data['assemblys']  =   AcModel::get_records([
            'state'           =>  $data['state'],
        ]);
    
     if (Auth::user()->role_id == '5') {

       $data['assemblys']  =   AcModel::get_records([
            'state'           =>  Auth::user()->st_code,
            'dist_no'         =>  Auth::user()->dist_no,
        ]);

     }else if (Auth::user()->role_id == '19') {

       $data['assemblys']  =   AcModel::get_records([
            'state'           => Auth::user()->st_code,
            'ac_no'           => Auth::user()->ac_no,
        ]);

     }

    foreach ($object as $result) {
      $individual_filter_array = [];
      $individual_filter_array['state'] = 'state='.base64_encode($result->st_code);
      $individual_filter    = implode('&', $individual_filter_array);

       $assign_table    = BoothCountingTableModel::getallassigntable([
          'st_code'  =>  $result->st_code,//$data['state'],
          'ac_no'    =>  $result->const_no,
        ]);
        
        $total_not_assigned = $result->total_tables - $assign_table['countassigntable'];

        $results[] = [
          'label'                => $result->state_name,
          "st_code"              => $result->st_code,
          "const_no"             => $result->const_no,
          "const_name"           => $result->const_name,
          'filter'               => $individual_filter,
          "total_ps"             => ($result->total_ps)?$result->total_ps:'0',
          "total_tables"         => ($result->total_tables)?$result->total_tables:'0',
          "total_assigned"       => $assign_table['countassigntable'],
         // "total_not_assigned"   => $total_not_assigned,
          "total_rounds"         => ($result->total_rounds)?$result->total_rounds:'0',
          "href"                 => url($this->action_ac)."?".$individual_filter
        ];     


      $GrandTotalPs          +=   $result->total_ps;
      $GrandTotalTables      +=   $result->total_tables;
      $GrandTotalAssigned    +=   $assign_table['countassigntable'];
      //$GrandTotalNotAssigned +=   $total_not_assigned;
      $GrandTotalRound       +=   $result->total_rounds; 

      }

      $data['GrandTotal'] = [
                'label'                  => 'Total',
                "GrandTotalPs"           => $GrandTotalPs,
                "GrandTotalTables"       => $GrandTotalTables,
                "GrandTotalAssigned"     => $GrandTotalAssigned,
               // "GrandTotalNotAssigned"  => $GrandTotalNotAssigned,
                "GrandTotalRound"        => $GrandTotalRound,
            ];

    $data['results']    =   $results;
    $data['user_data']  =   Auth::user();

//dd($data['state']);

    $data['heading_title_with_all'] = $data['heading_title'];

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
    return $data;
    }

      return view($this->view_path.'.report_ac', $data);
  }

  public function export_excel_report_ac(Request $request){

    set_time_limit(6000);
    $data = $this->report_ac($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $headings[] = [$data['heading_title']];
    $export_data[] = ['', '','','Table','Scheduled','','',''];

    $export_data[] = ['State','Const No','Const Name', 'Total Polling Stations','Total Tables','Total Assigned', 'Total Rounds'];


    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['const_no'],
        $lis['const_name'],
        ($lis['total_ps'])?$lis['total_ps']:'0',
        ($lis['total_tables'])?$lis['total_tables']:'0',
        ($lis['total_assigned'])?$lis['total_assigned']:'0',
        ($lis['total_rounds'])?$lis['total_rounds']:'0',
      ];
    }

 /*   $export_data[] = [
      $data['totals']['label'],
      ($data['totals']['old_total_male'])?$data['totals']['old_total_male']:'0',
      ($data['totals']['old_total_female'])?$data['totals']['old_total_female']:'0',
      ($data['totals']['old_total_other'])?$data['totals']['old_total_other']:'0',
      ($data['totals']['old_total'])?$data['totals']['old_total']:'0',

      ($data['totals']['total_male'])?$data['totals']['total_male']:'0',
      ($data['totals']['total_female'])?$data['totals']['total_female']:'0',
      ($data['totals']['total_other'])?$data['totals']['total_other']:'0',
      ($data['totals']['total'])?$data['totals']['total']:'0',
      ($data['totals']['total_percentage'])?$data['totals']['total_percentage']:'0',
    ];*/

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:G1');
    //       $sheet->mergeCells('B2:E2');
    //       $sheet->mergeCells('F2:I2');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function export_pdf_report_ac(Request $request){
    $data = $this->report_ac($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.report_ac_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }


  

}    