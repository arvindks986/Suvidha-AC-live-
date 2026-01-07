<?php namespace App\Http\Controllers\Admin\Nomination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Common;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use App\commonModel;  
use App\models\Admin\{ReportModel,AcModel};
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;

class ScrutinyController extends Controller {

  public $base          = '';
  public $folder        = '';
  public $view_path     = "admin.nomination";

  public function __construct(){
    $this->report_model = new ReportModel();
  }

  public function get_report(Request $request){

    $data                   = [];
    $data['heading_title']  = "Scrutiny report";
    $request_filter         = Common::get_request_filter($request);
    $ac_no          = $request_filter['ac_no'];
    $st_code        = $request_filter['st_code'];
    $dist_no        = $request_filter['dist_no'];
    $ps_no          = $request_filter['ps_no'];

    $filter = [
      'st_code' => $st_code,
      'ac_no'   => $ac_no,
      'dist_no' => $dist_no,
      'ps_no'   => $ps_no,
    ];

    $data['action'] = Common::generate_url('nomination/scrutiny');

    $request_array = []; 
    $title_array = [];
    $data['filter_buttons'] = $title_array;
    $data['filter']   = implode('&', array_merge($request_array));
    //end set title

    //buttons
    $data['buttons']    = [];
  

    $from_date  = NULL;
    $from_to    = NULL;
    $request_array = [];
    
    if(isset($from_date) && isset($from_to)){
      $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
    }

    if($filter['st_code']){
      $filter['state'] = $filter['st_code'];
    }

    $results = AcModel::get_records($filter);

      $data['results'] = [];
      $total           = 0;
      $total_withdraw  = 0;
      $total_rejected  = 0;
      $total_accepted  = 0;
      $total_verify_by_ro  = 0;
      $total_receipt       = 0;
      $total_applied       = 0;
      $total_contested     = 0;

      foreach ($results as $iterate_result) {   

          $filter_data = [
            'from_date'     => $from_date,
            'to_date'       => $from_to,
            'st_code'       => $iterate_result['st_code'],
            'const_type'    => 'AC',
            'const_no'      => $iterate_result['ac_no'],
          ];

          $count_total        = $this->report_model->get_total_nomination(0, $filter_data);
          $count_withdraw     = $this->report_model->get_total_nomination(5, $filter_data);
          $count_rejected     = $this->report_model->get_total_nomination(4, $filter_data);
          $count_accepted     = $this->report_model->get_total_nomination(6, $filter_data);
          $count_verify_by_ro = $this->report_model->get_total_nomination(2, $filter_data);
          $count_receipt      = $this->report_model->get_total_nomination(3, $filter_data);
          $count_applied      = $this->report_model->get_total_nomination(1, $filter_data);
          $count_contested    = $this->report_model->get_total_nomination(6, array_merge($filter_data,['final_accepted' => 1]));

          $total              += $count_total;
          $total_withdraw     += $count_withdraw;
          $total_rejected     += $count_rejected;
          $total_accepted     += $count_accepted;
          $total_verify_by_ro += $count_verify_by_ro;
          $total_receipt      += $count_receipt;
          $total_applied      += $count_applied;
          $total_contested    += $count_contested;

          $request_array = [
            'st_code' => 'st_code='.$iterate_result['st_code'],
            'dist_no' => 'dist_no='.$iterate_result['dist_no'],
            'ac_no'   => 'ac_no='.$iterate_result['ac_no'],
          ];

          $data['results'][] = [
            'label'              => $iterate_result['ac_no'].'-'.$iterate_result['ac_name'],
            'filter'             => implode('&', $request_array),
            'const_no'           => $iterate_result['ac_no'],
            'const_name'         => $iterate_result['ac_name'],
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

    //form filters
    $data['filter_action'] = Common::generate_url("nomination/iterate_resultt-of-nomination");
    $form_filter_array = [
      'st_code'     => true,
      'dist_no'     => true,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation' => false
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);
    $data['form_filters']   = $form_filters;
    $data['user_data']              = Auth::user();
    $data['heading_title_with_all'] = $data['heading_title'];
    $data['from']                   = $from_date;
    $data['to']                     = $from_to;

    $data['downlaod_to_excel'] = url('roac/report/scrutiny/excel').'?'.implode('&', $request_array);

    if($request->has('is_excel')){
      return $data;
    } 
    return view($this->view_path.'.scrutiny', $data); 
  }    

  public function detail($id,Request $request){
    $data = [];
    $final_accepted = NULL;
    if($id=='accepted'){
      $status = 6;
    }else if($id=='withdraw'){
      $status = 5;
    }else if($id=='rejected'){
      $status = 4;
    }else if($id=='contested'){
      $status = 6;
      $final_accepted = 1;
    }else{
      $status = 0;
    }

    $data['heading_title'] = "Scrutiny Reports of ".$id." candidate(s)";
    $from_date  = NULL;
    $from_to    = NULL;

    $data['action'] = Common::generate_url('nomination/scrutiny');

    if($request->has('from') && $request->has('to')){
      $from_date  = date('Y-m-d',strtotime($request->from));
      $from_to    = date('Y-m-d',strtotime($request->to));
    }

  
    $request_filter   = Common::get_request_filter($request);
    $ac_no            = $request_filter['ac_no'];
    $st_code          = $request_filter['st_code'];
    $dist_no          = $request_filter['dist_no'];
    $ps_no            = $request_filter['ps_no'];

    $filter = [
      'st_code' => $st_code,
      'state'   => $st_code,
      'ac_no'   => $ac_no,
      'dist_no' => $dist_no,
      'ps_no'   => $ps_no,
    ];

     
    $const_name = '';
    $const =  AcModel::get_record($filter);

    if($const){
      $const_name = $const['ac_name'];
    }

    $filter_data = [
      'from_date'     => $from_date,
      'to_date'       => $from_to,
      'st_code'       => $filter['st_code'],
      'const_type'    => 'AC',
      'const_no'      => $filter['ac_no'],
      'final_accepted' => $final_accepted
    ];


    $candidates        = $this->report_model->get_nominations($status, $filter_data);


    
      $index = 0;
      $results = [];
      foreach ($candidates as $candidate) {

        if($candidate->finalaccepted == 1){
          $status_name = $candidate->status_name. ' & Contested';
        }else{
          $status_name = $candidate->status_name;
        }

          $name = $candidate->cand_name;
          $results[] = [
            'index'          => $candidate->new_srno,
            'pc_no_name'     => $ac_no.'-'.$const_name,
            'candidate_id'   => $candidate->candidate_id,
            'name'           => $name,
            'h_name'         => $candidate->cand_hname,
            'email'          => $candidate->cand_email,
            'mobile'         => $candidate->cand_mobile,
            'status'         => $status_name,
            'party_name'     => $candidate->PARTYNAME,
            'party_symbol'   => ($candidate->SYMBOL_DES)?$candidate->SYMBOL_DES:'Not Alloted',
            'href'           => Common::generate_url('candidate/detail-by-nomination/'.base64_encode($candidate->nomination_id))
          ]; 
      }


    $data['results']            =  $results;
    $data['user_data']          = Auth::user();


    $data['cand_finalize_ceo']  = @$check_finalize->finalize_by_ceo;
    $data['cand_finalize_ro']   = @$check_finalize->finalized_ac;
    

    //return view('admin.ac.'.$folder.'.report.date_wise_report_name', $data);     
    return view($this->view_path.'.scrutiny_candidate', $data);
  }  

  public function downlaod_to_excel(Request $request){
    set_time_limit(6000);
    $data = $this->get_report($request->merge(['is_excel' => 1]));

    $export_data = [];
    foreach ($data['results'] as $iterate_result) {
      $export_data[] = [
            'label'              => $iterate_result['label'],
            'total_applied'      => $iterate_result['total_applied'],
            'total_accepted'     => $iterate_result['total_accepted'],
            'total_rejected'     => $iterate_result['total_rejected'],
            'total_withdraw'     => $iterate_result['total_withdraw'],
            'total_contested'    => $iterate_result['total_contested'],
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


    \Excel::create('scrutiny'.time(), function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $headers = ['Constituency Name', 'Total Nomination','Accepted','Withdrawn', 'Rejected','Contested'];
          $sheet->fromArray($export_data,null,'A1',false,false)->prependRow($headers);
        });
    })->export('xls');

  }

}  // end class