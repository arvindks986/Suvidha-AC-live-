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
use Validator;
use Config;
use \PDF;
use App\commonModel;  
use App\models\Admin\ReportModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\PhaseModel;
use App\models\Admin\AcModel;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller {
  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct(){
    $this->commonModel  = new commonModel();
    $this->report_model = new ReportModel();
  }

  public function get_report(Request $request){


    //first argument must be string, second must be $request object if you want to verify base 64, send that variable in ccode parameter in request object using $request->merge(['ccode' => $somevalue]);
    $request_status = validate_request('',$request);
    if(!$request_status){
      return Redirect::to('logout');
    }
    //end validate request

      $base   = '';
      $folder = '';
      $data = [];
      $from_date  = NULL;
      $from_to    = NULL;
      $request_array = [];
      

      if(!Auth::user()){
        return redirect('/officer-login');
      }

      $data['phases'] = $this->report_model->get_phases();
      $d              = Auth::user();

      $data['action'] = url('roac/report/scrutiny');


      $ele_details    = $this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);


      $check_finalize = candidate_finalizebyro(@$ele_details->ST_CODE,@$ele_details->CONST_NO,@$ele_details->CONST_TYPE);
      $seched         = getschedulebyid(@$ele_details->ScheduleID);
      $sechdul        = checkscheduledetails($seched);  

      if($request->has('from') && $request->has('to')){
        $from_date  = date('Y-m-d',strtotime($request->from));
        $from_to        = date('Y-m-d',strtotime($request->to));
        $request_array[] = 'from='.$request->from;
        $request_array[] = 'to='.$request->to;
      }

      if(isset($ele_details->ScheduleID)) {
        $sched      = $this->commonModel->getschedulebyid(@$ele_details->ScheduleID);
        $const_type = @$ele_details->CONST_TYPE;
      }else {
        $sched      = '';
      }

      $filter_election = [
        'state_code' => Auth::user()->st_code,
        'const_type' => $const_type,
        'pc_no'      => Auth::user()->ac_no
      ];

       $data['heading_title'] = 'Scrutiny report';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }

      //$lists = $this->report_model->election_details($filter_election);

       $data['list_const'] = NULL;

      if(Auth::user()->role_id == '4'){
 
        //ASSEMBLY LIST

         $data['list_const'] =  AcModel::get_records([
          'state'           => Auth::user()->st_code,
        ]);

         $lists =  AcModel::get_records([
          'state'   => Auth::user()->st_code,
        ]);

      }else if(Auth::user()->role_id == '19'){

        //ASSEMBLY LIST
         $data['list_const'] =  AcModel::get_records([
          'state'           => Auth::user()->st_code,
          'ac_no'           => Auth::user()->ac_no,
        ]);   

        $lists =  AcModel::get_records([
          'state'           => Auth::user()->st_code,
          'ac_no'           => Auth::user()->ac_no,
        ]);     

      }


      $results = [];
      $total           = 0;
      $total_withdraw  = 0;
      $total_rejected  = 0;
      $total_accepted  = 0;
      $total_verify_by_ro  = 0;
      $total_receipt       = 0;
      $total_applied       = 0;
      $total_contested     = 0;


      foreach ($lists as $lis) {   
          $const_name = $lis['ac_name'];
          /*if($lis->CONST_TYPE=='AC') {
            $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
            $const_name=$const->AC_NAME;
          }*/

         /* if($lis->CONST_TYPE=='PC') {
            $const=$this->commonModel->getpcname($lis->ST_CODE,$lis->CONST_NO);
            $const_name=trim($const->PC_NAME);
          }*/

          $filter_data = [
            'from_date'     => $from_date,
            'to_date'       => $from_to,
            'st_code'       => $lis['st_code'],
            //'const_type'    => $lis->CONST_TYPE,
            'const_type'    => 'AC',
            'const_no'      => $lis['ac_no'],
          ];

          $count_total        = $this->report_model->get_total_nomination(0, $filter_data);
          $count_withdraw     = $this->report_model->get_total_nomination(5, $filter_data);
          $count_rejected     = $this->report_model->get_total_nomination(4, $filter_data);
          $count_accepted     = $this->report_model->get_total_nomination(6, $filter_data);
          $count_verify_by_ro = $this->report_model->get_total_nomination(2, $filter_data);
          $count_receipt      = $this->report_model->get_total_nomination(3, $filter_data);
          $count_applied      = $this->report_model->get_total_nomination(1, $filter_data);
          $count_contested    = $this->report_model->get_total_nomination(6, array_merge($filter_data,['final_accepted' => 1, 'symbol_excluded' => 1]));
          //$count_contested    = $this->report_model->get_total_nomination(6, array_merge($filter_data,['final_accepted' => 1]));

          $total              += $count_total;
          $total_withdraw     += $count_withdraw;
          $total_rejected     += $count_rejected;
          $total_accepted     += $count_accepted;
          $total_verify_by_ro += $count_verify_by_ro;
          $total_receipt      += $count_receipt;
          $total_applied      += $count_applied;
          $total_contested    += $count_contested;


          $results[] = [
            'label'              => $lis['ac_no'].'-'.$const_name,
            'filter'             => implode('&', array_merge($request_array,['ccode' => 'ccode='.base64_encode($lis['CCODE'])])),
            'const_no'           => $lis['ac_no'],
            'const_name'         => $const_name,
            'total'              => $count_total,
            'total_withdraw'     => $count_withdraw,
            'total_rejected'     => $count_rejected,
            'total_accepted'     => $count_accepted,
            'total_verify_by_ro' => $count_verify_by_ro,
            'total_receipt'      => $count_receipt,
            'total_applied'      => $count_applied,
            'total_contested'    => $count_contested,
          ];                        
    }   

    $data['totals'] = [
      'label'              => 'Total',
      'filter'             => '',
      'const_no'           => '',
      'const_name'         => 'Total',
      'total'              => $total,
      'total_withdraw'     => $total_withdraw,
      'total_rejected'     => $total_rejected,
      'total_accepted'     => $total_accepted,
      'total_verify_by_ro' => $total_verify_by_ro,
      'total_receipt'      => $total_receipt,
      'total_applied'      => $total_applied,
      'total_contested'    => $total_contested,
      'href'               => 'javascript:void(0)'
    ]; 

    $data['results']    =  $results;
   
    $data['user_data']  = Auth::user();

    $data['cand_finalize_ceo']  = @$check_finalize->finalize_by_ceo;
    $data['cand_finalize_ro']   = @$check_finalize->finalized_ac;
    $data['sechdul']            = $sechdul;
    $data['ele_details']        = $ele_details;
    $data['sched']              = $sched;
    $data['from']               = $from_date;
    $data['to']               = $from_to;

    $data['downlaod_to_excel'] = url('roac/report/scrutiny/excel').'?'.implode('&', $request_array);
    $data['downlaod_to_pdf']   = url('roac/report/scrutiny/pdf').'?'.implode('&', $request_array);

    if($request->has('is_excel')){
      return $data;
    }
    
    return view('admin.ac.ro.report.date_wise_report', $data);   
  }    

  public function detail($id,Request $request){
    $data = [];

    $final_accepted = NULL;
    /*if($id=='accepted'){
      $status = 6;
    }else if($id=='withdraw'){
      $status = 5;
    }else if($id=='rejected'){
      $status = 4;
    }else if($id=='contested'){
      $status = 6;
      $final_accepted = 1;
      $symbol_excluded = 1;
    }else{
      $status = 0;
    }*/
    $symbol_excluded = NULL;
    if($id=='accepted'){
      $status = 6;
    }else if($id=='withdraw'){
      $status = 5;
    }else if($id=='rejected'){
      $status = 4;
    }else if($id=='contested'){
      $status = 6;
      $final_accepted = 1;
      $symbol_excluded = 1;
    }else if($id=='validated'){
      $status = 6;
      $final_accepted = 1;
    }else{
      $status = 0;
    }

    if($id=='contested'){
      $status_name = 'Contesting';
    }else{
      $status_name = $id;
    }


    //first argument must be string, second must be $request object if you want to verify base 64, send that variable in ccode parameter in request object using $request->merge(['ccode' => $somevalue]);
    $request_status = validate_request('',$request);
    if(!$request_status){
      return Redirect::to('logout');
    }
    //end validate request


      $data['heading_title'] = "Scrutiny Reports of ".$id." candidate(s)";

      
      $from_date  = NULL;
      $from_to    = NULL;

      $data['action'] = url('/roac/report/scrutiny');

      if(!Auth::user()){
        return redirect('/officer-login');
      }

      if(Auth::user()->designation=='CEO'){
        $base = 'pcceo';
        $folder = 'ceo';
      }else{
        $base = 'roac';
        $folder = 'ro';
      }


      $d              = Auth::user();

      $ele_details    = $this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

      $check_finalize = candidate_finalizebyro(@$ele_details->ST_CODE,@$ele_details->CONST_NO,@$ele_details->CONST_TYPE);
      $seched         = getschedulebyid(@$ele_details->ScheduleID);
      $sechdul        = checkscheduledetails($seched);  

      if($request->has('from') && $request->has('to')){
        $from_date  = date('Y-m-d',strtotime($request->from));
        $from_to    = date('Y-m-d',strtotime($request->to));
      }

      if(isset($ele_details->ScheduleID)) {
        $sched      = $this->commonModel->getschedulebyid(@$ele_details->ScheduleID);
        $const_type = $ele_details->CONST_TYPE;
      }else {
        $sched      = '';
      }



      $filter_election = [
        'state_code' => Auth::user()->st_code,
        'const_type' => $const_type,
        'pc_no'      => Auth::user()->ac_no
      ];

      if($request->has('ccode')){
        $ccode = base64_decode($request->ccode);
        $filter_election['ccode'] = $ccode;
      }

      
      


      //$lis      = $this->report_model->election_detail($filter_election);

       $lis = NULL;

      if(Auth::user()->role_id == '4'){
 
        //ASSEMBLY LIST
         $lis =  AcModel::get_records([
          'state'   => Auth::user()->st_code,
        ]);

      }else if(Auth::user()->role_id == '19'){

        //ASSEMBLY LIST
        $lis =  AcModel::get_records([
          'state'           => Auth::user()->st_code,
          'ac_no'           => Auth::user()->ac_no,
        ]);     

      }

      if(!$lis){
        return Redirect::to($data['action']);
      }
      
      $const_name = NULL;
      foreach ($lis as $lis) { 
        $const_name = $lis['ac_name'];
      }
     
      
      /*if($lis->CONST_TYPE=='AC') {
        $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
        $const_name=$const->AC_NAME;
      }
      if($lis->CONST_TYPE=='PC') {
        $const=$this->commonModel->getpcname($lis->ST_CODE,$lis->CONST_NO);
        $const_name=trim($const->PC_NAME);
      }*/

      $filter_data = [
            'from_date'     => $from_date,
            'to_date'       => $from_to,
            'st_code'       => $lis['st_code'],
            //'const_type'    => $lis->CONST_TYPE,
            'const_type'     => 'AC',
            'const_no'       => $lis['ac_no'],
            'final_accepted' => $final_accepted,
            'symbol_excluded' => $symbol_excluded
      ];
      $pcs               = $this->report_model->get_ac_detail($filter_data);

      $candidates        = $this->report_model->get_nominations($status, $filter_data);

      $index = 0;
      $results = [];
      foreach ($candidates as $candidate) {

        /*if($candidate->finalaccepted == 1){
         // $status_name = $candidate->status_name. ' & Contesting';
          $status_name = $candidate->status_name;
        }else{
          $status_name = $candidate->status_name;
        }*/

        if($candidate->finalaccepted == 1 && $status == 6){
            $status_name = 'Contesting';
          }else{
            $status_name = $candidate->status_name;
          }
          

          $name = $candidate->cand_name;
          $results[] = [
            'index'          => $candidate->new_srno,
            'pc_no_name'     => ($pcs)?$pcs->AC_NO.'-'.$pcs->AC_NAME:'',
            'candidate_id'   => $candidate->candidate_id,
            'name'           => $name,
            'h_name'         => $candidate->cand_hname,
            'email'          => $candidate->cand_email,
            'mobile'         => $candidate->cand_mobile,
            'status'         => $status_name,
            'party_name'     => $candidate->PARTYNAME,
            'party_symbol'   => ($candidate->SYMBOL_DES)?$candidate->SYMBOL_DES:'Not Alloted',
            'href'           => url($base.'/candidate/detail-by-nomination/'.base64_encode($candidate->nomination_id))
          ]; 
      }


    $data['results']            =  $results;
    $data['user_data']          = Auth::user();
    $data['ele_details']        = $ele_details;

    $data['cand_finalize_ceo']  = @$check_finalize->finalize_by_ceo;
    $data['cand_finalize_ro']   = @$check_finalize->finalized_ac;
    
    return view('admin.ac.'.$folder.'.report.date_wise_report_name', $data);     
  }  

  public function downlaod_to_excel(Request $request){
 
    set_time_limit(6000);
    $data = $this->get_report($request->merge(['is_excel' => 1]));
    $headings[] = [$data['heading_title']];
    $export_data = [];
    $export_data[] = ['Constituency Name', 'Total Nomination','Accepted','Withdrawn', 'Rejected','Contested'];

    
    foreach ($data['results'] as $lis) {

      

      $export_data[] = [
            'label'              => $lis['label'],
            'total_applied'      => $lis['total_applied'],
            'total_accepted'     => $lis['total_accepted'],
            'total_rejected'     => $lis['total_rejected'],
            'total_withdraw'     => $lis['total_withdraw'],
            'total_contested'    => $lis['total_contested'],
      ];
    }
 
    $export_data[] = [
            'label'              => $data['totals']['label'],
            'total_applied'      => $data['totals']['total_applied'],
            'total_accepted'     => $data['totals']['total_accepted'],
            'total_rejected'     => $data['totals']['total_rejected'],
            'total_withdraw'     => $data['totals']['total_withdraw'],
            'total_contested'    => $data['totals']['total_contested'],
    ];


    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], "scrutiny"));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


    // \Excel::create('scrutiny'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $headers = ['Constituency Name', 'Total Nomination','Accepted','Withdrawn', 'Rejected','Contested'];
    //       $sheet->fromArray($export_data,null,'A1',false,false)->prependRow($headers);
    //     });
    // })->export('xls');

  }

  public function pdf(Request $request){
    $data = $this->get_report($request->merge(['is_excel' => 1]));
    $pdf = \PDF::loadView('admin.ac.ro.report.pdf',$data);
    return $pdf->download('scrutiny_report_'.date('d-m-Y').'_'.time().'.pdf');
  }

}  // end class