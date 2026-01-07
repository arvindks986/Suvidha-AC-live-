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
use App\models\Counting\MissingFlagsReportModel;

class MissingFlagsReportController  extends Controller{

    public $base          = 'eci';
    public $folder        = 'counting';
    public $action_state  = 'eci/counting/missing-flags-state';
    public $action_ac     = 'eci/counting/missing-flags-state/state/ac';
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
        $this->action_state  = 'acceo/counting/missing-flags-state';
        $this->action_ac     = 'acceo/counting/missing-flags-state/state/ac';
      }

      //DEO USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if(Auth::user()->role_id == '5'){
        $this->action_state  = 'acceo/counting/missing-flags-state/state/district';
        $this->action_ac     = 'acceo/counting/missing-flags-state/state/ac';
      }

      //RO USER TYPE AND SETTING VARIABLES FOR IT STARTS
      if(Auth::user()->role_id == '19'){
        $this->action_state  = 'acceo/counting/missing-flags-state/state/ac';
        $this->action_ac     = 'acceo/counting/missing-flags-state/state/ac';
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
      $data['heading_title'] = 'Missing Flags';
      
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

     $object    = MissingFlagsReportModel::get_reports([
        'state'     => Auth::user()->st_code,
        'group_by'  => 'eci',
        'order_by'  => 'eci'
      ]);

    }else if(Auth::user()->role_id == '5') {
        //DEO RECORDS
        $object    = MissingFlagsReportModel::get_reports([
        'state'     => Auth::user()->st_code,
        'district'  => Auth::user()->dist_no,
        'group_by'  => 'dist_no',
        'order_by'  => 'dist_no'
      ]);
    }else if(Auth::user()->role_id == '19') {
        //RO RECORDS
        $object    = MissingFlagsReportModel::get_reports([
        'state'     => Auth::user()->st_code,
        'district'  => Auth::user()->dist_no,
        'ac'        => Auth::user()->ac_no,
        'group_by'  => 'ac_no',
        'order_by'  => 'ac_no'
      ]);
    }else {
        //ECI RECORDS
      
      if($data['state']){
          $object    = MissingFlagsReportModel::get_reports([
          'state'       => $data['state'],
          'eci'       => 'eci',
          'group_by'  => 'eci',
          'order_by'  => 'eci'
        ]);
      }else{

        $object    = MissingFlagsReportModel::get_reports([
        'eci'       => 'eci',
        'group_by'  => 'eci',
        'order_by'  => 'eci'
      ]); 

      }     

    }
   
    $GrandEvmFinal      = 0;
    $GrandPostalFinal   = 0;
  
    foreach ($object as $result) {

      $individual_filter_array = [];
      $individual_filter_array['state'] = 'state='.base64_encode($result->st_code);
      $individual_filter    = implode('&', $individual_filter_array);

          $results[] = [
            'label'            => $result->state_name,
            "st_code"          => $result->st_code,
            "const_name"       => $result->const_name,
            "const_no"         => $result->const_no,
            'filter'           => $individual_filter,
            "evm_finalized"    => ($result->evm_finalized)?$result->evm_finalized:'0',
            "postal_finalize"  => ($result->postal_finalize)?$result->postal_finalize:'0',
            "href"             => 'javascript:void(0)',
          ];      

        /*$GrandEvmFinal         +=   $result->evm_finalized;
        $GrandPostalFinal      +=   $result->postal_finalize;*/

      } 

      /*$data['GrandTotal'] = [
            'label'               => 'Total',
            "GrandEvmFinal"       => $GrandEvmFinal,
            "GrandPostalFinal"    => $GrandPostalFinal,
        ];*/


    $data['results']    =   $results;

    $data['user_data']  =   Auth::user();

    $data['heading_title_with_all'] = $data['heading_title'];

    if($request->has('is_excel')){
    if(isset($title_array) && count($title_array)>0){
      $data['heading_title'] .= "- ".implode(', ', $title_array);
    }
    return $data;
    }

    return view($this->view_path.'.missing_flag', $data);
    }
    //STATE WISE REPORT FOR TABLES ENDS

    public function export_excel_report_state(Request $request){

    set_time_limit(6000);
    $data = $this->report_state($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    $export_data[] = ['','Missing','Flags',''];

    $export_data[] = ['State', 'Const No','Const Name','EVM Finalized', 'Postal Finalized'];
    
    /*$GrandEvmFinal      = 0;
    $GrandPostalFinal   = 0;*/


    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['const_no'],
        $lis['const_name'],
        ($lis['evm_finalized'])?$lis['evm_finalized']:'0',
        ($lis['postal_finalize'])?$lis['postal_finalize']:'0',
      ];

        /*$GrandEvmFinal         +=   $lis['evm_finalized'];
        $GrandPostalFinal      +=   $lis['postal_finalize'];*/
    }


    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:E1');
          $sheet->mergeCells('B2:E2');
          $sheet->mergeCells('F2:I2');
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
    $pdf = \PDF::loadView($this->view_path.'.missing_flag_pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }


  

}    