<?php
namespace App\Http\Controllers\Admin\Nomination\Report;

use App\Http\Controllers\Controller;

use App\models\Admin\AcModel;
use App\models\Admin\CandidateModel;
use App\models\Admin\CandidateNominationModel;
use App\models\Admin\DistrictModel;
use App\models\Admin\StateModel;
use App\models\Nomination\OnlineNomModel;
use Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\models\Admin\Nomination\NominationApplicationModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class PaymentReportControlller extends Controller
{
    public $base = '';
    public $folder = '';
    public $view_path = "admin.nomination.onlinenom-report.eci_level.payment.";
    public $view_path1 = "admin.nomination.onlinenom-report.ceo_level.payment.";
    public $view_path2 = "admin.nomination.onlinenom-report.deo_level.payment.";
    public $view_path3 = "admin.nomination.onlinenom-report.payment_details.";

    public function get_report(Request $request)
    {
        $data = [];
        $data['between']            = explode(' - ', $request->date);
        $data['heading_title']      = "Nomination Payment Details";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/count-payment-report-export");

        $filter = [
            'st_code'   => $st_code,
            'ac_no'     => $ac_no,
            'dist_no'   => $dist_no
        ];
        $title_array    = [];
        $request_array  = [];
        $default_user   = [];
        if(isset($data['between'][0]) && !empty($data['between'][0])){
            $request_array[]          = "date=".$request->date;
        }
        if (isset($st_code) && !empty($st_code)) {
            $statename                = getstatebystatecode($st_code);
            $title_array[]            = "State: " . $statename->ST_NAME;
            $request_array[]          =  'st_code='.$st_code;
            $default_user             = ['st_code'=> $st_code];
            if (isset($dist_no) && !empty($dist_no)) {
                $distname = getdistrictbydistrictno($st_code, $dist_no);
                $title_array[]        = "District: " . $distname->DIST_NAME;
                $request_array[]      =  'dist_no='.$dist_no;
                $default_user         = array_merge($default_user, ["dist_no"=>$dist_no]);
            }
            if (isset($ac_no) && !empty($ac_no)) {
                $acame = getacbyacno($st_code, $ac_no);
                $title_array[]        = "AC: " . $acame->AC_NAME;
                $request_array[]      =  'ac_no='.$ac_no;
                $default_user         = array_merge($default_user, ["ac_no"=>$ac_no]);
            }
        }
        $data['filter_buttons'] = $title_array;

        //buttons
        $data['buttons']    = [];
        // $data['buttons'][]  = [
        //   'name' => 'Export Excel',
        //   'href' =>  url($action.'/excel').'?'.implode('&', $request_array),
        //   'target' => true
        // ];
        $data['buttons'][]  = [
          'name' => 'Export Pdf',
          'href' =>  url($action.'/pdf').'?'.implode('&', $request_array),
          'target' => true
        ];
        $fetch_data = OnlineNomModel::getall_st_payment_wise(array_merge($default_user, $request->all()));
        $results = [];
        $i_count = 1;
        if(count($fetch_data)>0 && $fetch_data[0]['st_code'] != null){ 
            foreach($fetch_data as $each_data){
                $request_array['st_code']      = "st_code=".$each_data['st_code'];
                $action_url = Common::generate_url('online_nom/dist-wise-payment-report').'?'.implode('&', array_unique($request_array));
                $payment_online = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'mode'=> 'online', 'date'=>$request->date));
                $payment_challan = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'mode'=> 'challan', 'date'=>$request->date));
                $payment_cash = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'mode'=> 'cash', 'date'=>$request->date));
                $finalize_after_payment_pend = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'mode'=> '', 'finalize'=> '0', 'date'=>$request->date));
                $finalize_after_payment = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'mode'=> '', 'finalize'=> '1','date'=>$request->date));
                
                $results[] = [
                    'sno'                            =>  $i_count++,
                    'st_code'                        =>  $each_data['st_code'],
                    'st_name'                        =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'action_url'                     =>  $action_url,
                    'payment_online'                 =>  $payment_online,
                    'Payemnt_challan'                =>  $payment_challan,
                    'payment_cash'                   =>  $payment_cash,
                    'finalize_after_payment'         =>  $finalize_after_payment,
                    'finalize_after_payment_pend'    =>  $finalize_after_payment_pend,
                    'payment_done'                   =>  ($payment_online+$payment_challan+$payment_cash),
                    'payment_pending'                =>  (($finalize_after_payment_pend+$finalize_after_payment)-($payment_online+$payment_challan+$payment_cash)),
                    'payment_online_url'             =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=online', 'status'=>'status=done']))),
                    'Payemnt_challan_url'            =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=challan', 'status'=>'status=done']))),
                    'payment_cash_url'               =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=cash', 'status'=>'status=done']))),
                    'payment_pending_url'            =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=', 'status'=>'status=pending']))),
                    'all_payment_url'                =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=', 'status'=>'status=all'])))
                ];
            }
        }
        $data['results'] = $results;
        //form filters
        $data['filter_action'] = $action;
        $form_filter_array = [
            'st_code' => true,
            'dist_no' => true,
            'ac_no' => true,
            'ps_no' => false,
            'designation' => false,
        ];
        $form_filters = Common::get_form_filters($form_filter_array, $request);
        $data['form_filters'] = $form_filters;

        $data['user_data'] = Auth::user();
        $data['heading_title_with_all'] = $data['heading_title'];

        if ($request->has('is_export')) {
            if (isset($title_array) && count($title_array) > 0) {
                $data['heading_title'] .= "- " . implode(', ', $title_array);
            }
            return $data;
        }

        return view($this->view_path . 'onlinen_payment_report', $data);
    }

    public function get_report_pdf(Request $request){
        $data = $this->get_report($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path.'.onlinen_payment_report_pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
    }

    public function get_report_excel(Request $request){
        set_time_limit(6000);
        $data = $this->get_report($request->merge(['is_export' => 1]));
        if(!empty($data['between'][0])) {
            $date = $data['between'][0].' - '.$data['between'][1] ;
        }else{
            $date = " ";
        }
        $export_data = [];
        $export_data[] = ["Nomination Payment Details ".$date];
        $export_data[] = ['SNO','State Name', 'Recognized Party' ,'Unrecognized Party','Independent Party','Total Nomination'];
        foreach($data['results'] as $each_result){
            $export_data[] = array_except($each_result, ['st_code', 'action_url']);
        }
        $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], "Datewise_Online_Nomination"));
        // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
        //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
        //       $sheet->mergeCells('A1:F1');
        //       $sheet->cell('A1', function($cell) {
        //         $cell->setAlignment('center');
        //         $cell->setFontWeight('bold');
        //       });
        //       $sheet->fromArray($export_data,null,'A1',true,false);
        //     });
        // })->export('xls');
        return \Excel::download($export_data, 'invoices.xlsx');
    }

############################### For_dist_wise_report #######################################

    public function get_report_dist(Request $request)
    {
        $data = [];
        $data['between']            = explode(' - ', $request->date);
        $data['heading_title']      = "Nomination Payment Details";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/dist-wise-payment-report-export");

        $filter = [
            'st_code'   => $st_code,
            'ac_no'     => $ac_no,
            'dist_no'   => $dist_no
        ];
        $title_array    = [];
        $request_array  = [];
        $default_user   = [];
        if(isset($data['between'][0]) && !empty($data['between'][0])){
            $request_array[]          = "date=".$request->date;
        }
        if (isset($st_code) && !empty($st_code)) {
            $statename                = getstatebystatecode($st_code);
            $title_array[]            = "State: " . $statename->ST_NAME;
            $request_array[]          =  'st_code='.$st_code;
            $default_user             = ['st_code'=> $st_code];
            if (isset($dist_no) && !empty($dist_no)) {
                $distname = getdistrictbydistrictno($st_code, $dist_no);
                $title_array[]        = "District: " . $distname->DIST_NAME;
                $request_array[]      =  'dist_no='.$dist_no;
                $default_user         = array_merge($default_user, ["dist_no"=>$dist_no]);
            }
            if (isset($ac_no) && !empty($ac_no)) {
                $acame = getacbyacno($st_code, $ac_no);
                $title_array[]        = "AC: " . $acame->AC_NAME;
                $request_array[]      =  'ac_no='.$ac_no;
                $default_user         = array_merge($default_user, ["ac_no"=>$ac_no]);
            }
        }
        $data['filter_buttons'] = $title_array;

        //buttons
        $data['buttons']    = [];
        // $data['buttons'][]  = [
        // 'name' => 'Export Excel',
        // 'href' =>  url($action.'/excel').'?'.implode('&', $request_array),
        // 'target' => true
        // ];
        $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($action.'/pdf').'?'.implode('&', $request_array),
        'target' => true
        ];
        if(Auth::user()->designation=='DEO'){
            $back_url = '#';
        }else{
            $back_url = Common::generate_url("online_nom/count-payment-report");
        }
        $data['buttons'][]  = [
            'name' => 'Back',
            'href' =>  $back_url,
            'target' => false
        ];
        $fetch_data = OnlineNomModel::getall_dist_wise_payment(array_merge($default_user, $request->all()));
        $results = [];
        $i_count = 1;
        if(count($fetch_data)>0 && $fetch_data[0]['st_code'] != null){
            foreach($fetch_data as $each_data){
                $request_array['dist_no']      = "dist_no=".$each_data['dist_no'];    
                $action_url = Common::generate_url('online_nom/ac-wise-payment-report').'?'.implode('&', \array_unique($request_array));
                $payment_online = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'mode'=> 'online', 'date'=>$request->date));
                $payment_challan = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'mode'=> 'challan', 'date'=>$request->date));
                $payment_cash   = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'mode'=> 'cash', 'date'=>$request->date));
                $finalize_after_payment_pend = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'mode'=> '', 'finalize'=> '0', 'date'=>$request->date));
                $finalize_after_payment = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'mode'=> '', 'finalize'=> '1','date'=>$request->date));
                // dd($finalize_after_payment_pend, $finalize_after_payment, $payment_online, $payment_challan);
                $results[] = [
                    'sno'                            =>  $i_count++,
                    'st_code'                        =>  $each_data['st_code'],
                    'st_name'                        =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'dist_name'                      =>  $each_data['dist_no'].'-'.trim(getdistrictbydistrictno($each_data['st_code'], $each_data['dist_no'])->DIST_NAME),
                    'action_url'                     =>  $action_url,
                    'payment_online'                 =>  $payment_online,
                    'Payemnt_challan'                =>  $payment_challan,
                    'payment_cash'                   =>  $payment_cash,
                    'finalize_after_payment'         =>  $finalize_after_payment,
                    'finalize_after_payment_pend'    =>  $finalize_after_payment_pend,
                    'payment_done'                   =>  ($payment_online+$payment_challan),
                    'payment_pending'                =>  (($finalize_after_payment_pend+$finalize_after_payment)-($payment_online+$payment_challan)),
                    'payment_online_url'             =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=online', 'status'=>'status=done']))),
                    'Payemnt_challan_url'            =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=challan', 'status'=>'status=done']))),
                    'payment_cash_url'               =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=cash', 'status'=>'status=done']))),
                    'payment_pending_url'            =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=', 'status'=>'status=pending']))),
                    'all_payment_url'                =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['mode'=> 'mode=', 'status'=>'status=all'])))
                ];
            }
        }
        $data['results'] = $results;
        //form filters
        $data['filter_action'] = $action;
        $form_filter_array = [
            'st_code' => true,
            'dist_no' => true,
            'ac_no' => true,
            'ps_no' => false,
            'designation' => false,
        ];
        $form_filters = Common::get_form_filters($form_filter_array, $request);
        $data['form_filters'] = $form_filters;

        $data['user_data'] = Auth::user();
        $data['heading_title_with_all'] = $data['heading_title'];

        if ($request->has('is_export')) {
            if (isset($title_array) && count($title_array) > 0) {
                $data['heading_title'] .= "- " . implode(', ', $title_array);
            }
            return $data;
        }
        return view($this->view_path1 . 'onlinen_payment_report_st', $data);
    }

    public function get_report_dist_pdf(Request $request){
        $data = $this->get_report_dist($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path1.'.onlinen_payment_report_st_pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
    }

    public function get_report_dist_excel(Request $request){
        set_time_limit(6000);
        $data = $this->get_report_dist($request->merge(['is_export' => 1])); dd($data);
        if(!empty($data['between'][0])) {
            $date = $data['between'][0].' - '.$data['between'][1] ;
        }else{
            $date = " ";
        }
        $export_data = [];
        $export_data[] = ["Nomination Payment Details ".$date];
        $export_data[] = ['SNO','State Name', 'District Name', 'Recognized Party' ,'Unrecognized Party','Independent Party','Total Nomination'];
        foreach($data['results'] as $each_result){
            $export_data[] = array_except($each_result, ['st_code', 'action_url']);
        }
        $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], "Datewise_Online_Nomination"));
        \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
            $excel->sheet('Sheet1', function($sheet) use($export_data) {
            $sheet->mergeCells('A1:G1');
            $sheet->cell('A1', function($cell) {
                $cell->setAlignment('center');
                $cell->setFontWeight('bold');
            });
            $sheet->fromArray($export_data,null,'A1',true,false);
            });
        })->export('xls');
    }

    ############################### For_dist_wise_report #######################################

    public function get_report_ac(Request $request)
    {
        $data = [];
        $data['between']            = explode(' - ', $request->date);
        $data['heading_title']      = "Nomination Payment Details";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/ac-wise-payment-report-export");

        $filter = [
            'st_code'   => $st_code,
            'ac_no'     => $ac_no,
            'dist_no'   => $dist_no
        ];
        $title_array    = [];
        $request_array  = [];
        $default_user   = [];
        if(isset($data['between'][0]) && !empty($data['between'][0])){
            $request_array[]          = "date=".$request->date;
        }
        if (isset($st_code) && !empty($st_code)) {
            $statename                = getstatebystatecode($st_code);
            $title_array[]            = "State: " . $statename->ST_NAME;
            $request_array[]          =  'st_code='.$st_code;
            if (isset($dist_no) && !empty($dist_no)) {
                $distname = getdistrictbydistrictno($st_code, $dist_no);
                $title_array[]        = "District: " . $distname->DIST_NAME;
                $request_array[]      =  'dist_no='.$dist_no;
            }
            if (isset($ac_no) && !empty($ac_no)) {
                $acame = getacbyacno($st_code, $ac_no);
                $title_array[]        = "AC: " . $acame->AC_NAME;
                $request_array[]      =  'ac_no='.$ac_no;
            }
        }
        $data['filter_buttons'] = $title_array;

        //buttons
        $data['buttons']    = [];
        // $data['buttons'][]  = [
        // 'name' => 'Export Excel',
        // 'href' =>  url($action.'/excel').'?'.implode('&', $request_array),
        // 'target' => true
        // ];
        $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($action.'/pdf').'?'.implode('&', $request_array),
        'target' => true
        ];
        if(Auth::user()->designation=='DEO'){
            $back_url = Common::generate_url("online_nom/dist-wise-payment-report").'?'.implode('&', $request_array);
        }else{
            $back_url = Common::generate_url("online_nom/dist-wise-payment-report").'?'.implode('&', [$request_array[0]]);
        }
        $data['buttons'][]  = [
            'name' => 'Back',
            'href' =>  $back_url,
            'target' => false
          ];
        
        $fetch_data = OnlineNomModel::getall_ac_wise_payment($request->all());

        $results = [];
        $i_count = 1;
        if(count($fetch_data)>0 && $fetch_data[0]['st_code'] != null){
            foreach($fetch_data as $each_data){    
                $payment_online = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'ac_no'=>$each_data['ac_no'], 'mode'=> 'online', 'date'=>$request->date));
                $payment_challan = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'ac_no'=>$each_data['ac_no'], 'mode'=> 'challan', 'date'=>$request->date));
                $payment_cash   = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'ac_no'=>$each_data['ac_no'], 'mode'=> 'cash', 'date'=>$request->date));
                $finalize_after_payment_pend = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'ac_no'=>$each_data['ac_no'], 'mode'=> '', 'finalize'=> '0', 'date'=>$request->date));
                $finalize_after_payment = OnlineNomModel::get_count_payment_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'ac_no'=>$each_data['ac_no'], 'mode'=> '', 'finalize'=> '1','date'=>$request->date));
                $results[] = [
                    'sno'                               =>  $i_count++,
                    'st_code'                           =>  $each_data['st_code'],
                    'st_name'                           =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'dist_name'                         =>  $each_data['dist_no'].'-'.trim(getdistrictbydistrictno($each_data['st_code'], $each_data['dist_no'])->DIST_NAME),
                    'ac_name'                           =>  $each_data['ac_no'].'-'.trim(getacbyacno($each_data['st_code'], $each_data['ac_no'])->AC_NAME),
                    // 'action_url'                        =>  $action_url,
                    'payment_online'                    =>  $payment_online,
                    'Payemnt_challan'                   =>  $payment_challan,
                    'payment_cash'                      =>  $payment_cash,
                    'finalize_after_payment'            =>  $finalize_after_payment,
                    'finalize_after_payment_pend'       =>  $finalize_after_payment_pend,
                    'payment_done'                      =>  ($payment_online+$payment_challan),
                    'payment_pending'                   =>  (($finalize_after_payment_pend+$finalize_after_payment)-($payment_online+$payment_challan)),
                    'payment_online_url'                =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['ac_no'=>'ac_no='.$each_data['ac_no'],'mode'=> 'mode=online', 'status'=>'status=done']))),
                    'Payemnt_challan_url'               =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['ac_no'=>'ac_no='.$each_data['ac_no'],'mode'=> 'mode=challan', 'status'=>'status=done']))),
                    'payment_cash_url'                  =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['ac_no'=>'ac_no='.$each_data['ac_no'],'mode'=> 'mode=cash', 'status'=>'status=done']))),
                    'payment_pending_url'               =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['ac_no'=>'ac_no='.$each_data['ac_no'],'mode'=> 'mode=', 'status'=>'status=pending']))),
                    'all_payment_url'                   =>  Common::generate_url('online_nom/candidate_payment_details').'?'.implode('&', array_unique(array_merge($request_array, ['ac_no'=>'ac_no='.$each_data['ac_no'],'mode'=> 'mode=', 'status'=>'status=all'])))
                ];
            }
        }
        $data['results'] = $results;
        //form filters
        $data['filter_action'] = $action;
        $form_filter_array = [
            'st_code' => true,
            'dist_no' => true,
            'ac_no' => true,
            'ps_no' => false,
            'designation' => false,
        ];
        $form_filters = Common::get_form_filters($form_filter_array, $request);
        $data['form_filters'] = $form_filters;

        $data['user_data'] = Auth::user();
        $data['heading_title_with_all'] = $data['heading_title'];

        if ($request->has('is_export')) {
            if (isset($title_array) && count($title_array) > 0) {
                $data['heading_title'] .= "- " . implode(', ', $title_array);
            }
            return $data;
        }

        return view($this->view_path2 . 'onlinen_payment_report_dist', $data);
    }

    public function get_report_ac_pdf(Request $request){
        $data = $this->get_report_ac($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path2.'onlinen_payment_report_dist_pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
    }

    public function get_report_ac_excel(Request $request){
        set_time_limit(6000);
        $data = $this->get_report_ac($request->merge(['is_export' => 1]));
        if(!empty($data['between'][0])) {
            $date = $data['between'][0].' - '.$data['between'][1] ;
        }else{
            $date = " ";
        }
        $export_data = [];
        $export_data[] = ["Nomination Payment Details ".$date];
        $export_data[] = ['SNO','State Name', 'District Name', 'AC Name', 'Recognized Party' ,'Unrecognized Party','Independent Party','Total Nomination'];
        foreach($data['results'] as $each_result){
            $export_data[] = array_except($each_result, ['st_code', 'action_url']);
        }
        $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], "Datewise_Online_Nomination"));
        \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
            $excel->sheet('Sheet1', function($sheet) use($export_data) {
            $sheet->mergeCells('A1:H1');
            $sheet->cell('A1', function($cell) {
                $cell->setAlignment('center');
                $cell->setFontWeight('bold');
            });
            $sheet->fromArray($export_data,null,'A1',true,false);
            });
        })->export('xls');
    }

    public function candidate_payment_details(Request $request) {
        $data  = [];
        $data['filter']               = $request->all();
        $data['encrypt_id']           = '';
        $data['status_filter']        = $request->status;
        $user = Auth::user();
        $fil_status = $data['status_filter']; 
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/candidate_payment_details");
        $title_array    = [];
        $request_array  = [];
        $default_user   = [];
        if(isset($data['between'][0]) && !empty($data['between'][0])){
            $request_array[]          = "date=".$request->date;
        }
        if (isset($st_code) && !empty($st_code)) {
            $statename                = getstatebystatecode($st_code);
            $title_array[]            = "State: " . $statename->ST_NAME;
            $request_array[]          =  'st_code='.$st_code;
            if (isset($dist_no) && !empty($dist_no)) {
                $distname = getdistrictbydistrictno($st_code, $dist_no);
                $title_array[]        = "District: " . $distname->DIST_NAME;
                $request_array[]      =  'dist_no='.$dist_no;
            }
            if (isset($ac_no) && !empty($ac_no)) {
                $acame = getacbyacno($st_code, $ac_no);
                $title_array[]        = "AC: " . $acame->AC_NAME;
                $request_array[]      =  'ac_no='.$ac_no;
            }
            if($request->has('mode') && $request->has('status')){
                $request_array[]      =  'mode='.$request->mode;
                $request_array[]      =  'status='.$request->status;
            }
        }
        $data['filter_buttons'] = $title_array;
        $data['between']            = explode(' - ', $request->date);
        $data['heading_title']      = "Payment Details";
        //buttons
        $data['buttons']    = [];
        // $data['buttons'][]  = [
        // 'name' => 'Export Excel',
        // 'href' =>  url($action.'/excel').'?'.implode('&', $request_array),
        // 'target' => true
        // ];
        $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($action.'/pdf').'?'.implode('&', $request_array),
        'target' => true
        ];
        
        $data['buttons'][]  = [
            'name' => 'Back',
            'href' =>  Common::generate_url("online_nom/dist-wise-payment-report").'?'.implode('&', [$request_array[0]]),
            'target' => false
          ];

        $data['filter_action'] = $action;
        $form_filter_array = [
            'st_code' => true,
            'dist_no' => true,
            'ac_no' => true,
            'ps_no' => false,
            'designation' => false,
        ];
        $request_array  = [];
        if (isset($st_code) && !empty($st_code)) {
            $request_array[]          =  'st_code='.$st_code;
            if (isset($dist_no) && !empty($dist_no)) {
                $request_array[]      =  'dist_no='.$dist_no;
            }
            if (isset($ac_no) && !empty($ac_no)) {
                $request_array[]      =  'ac_no='.$ac_no;
            }
        }
        $form_filters = Common::get_form_filters($form_filter_array, $request);
        $data['form_filters'] = $form_filters;
        $result=NominationApplicationModel::select('nomination_application.*', 'm_ac.DIST_NO_HDQTR as dist_no', 'challan_ref_id', 'bank_transtimestamp', 'challan_no', 'challan_date', 'pay_by_cash_paid', 'date_time_of_pbc', 'payByCash')
        ->join('m_ac', [['nomination_application.st_code', '=', 'm_ac.ST_CODE'], ['nomination_application.ac_no', '=', 'm_ac.AC_NO']])
        ->leftjoin('challan_payment', [['nomination_application.st_code', '=', 'challan_payment.st_code'],
        ['nomination_application.ac_no', '=', 'challan_payment.ac_no'], 
        ['nomination_application.candidate_id', '=', 'challan_payment.candidate_id']])
        ->leftjoin('payment_details_common', [['nomination_application.st_code', '=', 'payment_details_common.st_code'],
        ['nomination_application.ac_no', '=', 'payment_details_common.ac_no'], 
        ['nomination_application.candidate_id', '=', 'payment_details_common.candidate_id']])
        ->where('finalize', '=', '1')
        ->where('application_type','2');
        if(!empty($data['filter']['st_code'])){
            $result->where('nomination_application.st_code', '=', $data['filter']['st_code']);
        }
        
        if(!empty($data['filter']['dist_no'])){
            $result->where('m_ac.DIST_NO_HDQTR', '=', $data['filter']['dist_no']);
        }
        
        if(!empty($data['filter']['ac_no'])){
            $result->where('nomination_application.ac_no', '=', $data['filter']['ac_no']);
        }
		
		
        if(isset($data['filter']['status']) && $data['filter']['status']=='done'){
            if($data['filter']['mode'] == 'online'){
                $result->where('payment_details_common.bank_transaction_status', '=', '1');
            }elseif($data['filter']['mode'] == 'challan'){
                $result->whereRaw("challan_receipt != ''");;
            }elseif($data['filter']['mode'] == 'cash'){
                $result->where('pay_by_cash_paid', '=', '1')->where('date_time_of_pbc', '!=', null);
            }
        }elseif(isset($data['filter']['status']) && $data['filter']['status']=='pending'){
            $result->where(function($join){
                $join->where('challan_ref_id', '=', '')->orWhere('challan_ref_id', '=', null)->orWhere('challan_ref_id', '=', 'null');
            }); 

            $result->where(function($join2){
                $join2->where('challan_no', '=', '')->orWhere('challan_no', '=', null);
            }); 

            $result->where(function($join3){
                $join3->where('pay_by_cash_paid', '=', '')->orWhere('pay_by_cash_paid', '=', null)->orWhere('pay_by_cash_paid', '=', 0);
            }); 

        }

        $results = $result->groupBy('nomination_application.candidate_id')->get()->toarray(); //dd($results);
        $modi_data = [];
        $i_count = 1;
        if(count($results)>0){
            foreach($results as $each_data){ 
                $modi_data[] = [
                    'sno'                               =>  $i_count++,
                    'st_code'                           =>  $each_data['st_code'],
                    'st_name'                           =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'dist_name'                         =>  $each_data['dist_no'].'-'.trim(getdistrictbydistrictno($each_data['st_code'], $each_data['dist_no'])->DIST_NAME),
                    'ac_name'                           =>  $each_data['ac_no'].'-'.trim(getacbyacno($each_data['st_code'], $each_data['ac_no'])->AC_NAME),
                    'candidate_name'                    =>  $each_data['name'],
                    'nomination_no'                     =>  $each_data['nomination_no'],
                    'transaction_id'                    =>  !empty($each_data['challan_ref_id']) ? $each_data['challan_ref_id'] : '-',
                    'transaction_date'                  =>  !empty($each_data['bank_transtimestamp']) ? date('d-m-Y h:i A', strtotime($each_data['bank_transtimestamp'])) : '-',
                    'challan_no'                        =>  !empty($each_data['challan_no']) ? $each_data['challan_no'] : '-',
                    'challan_date'                      =>  !empty($each_data['challan_no']) ? date('d-m-Y h:i A', strtotime($each_data['challan_date'])) : '-',
                    'is_cash_paid'                      =>  ($each_data['pay_by_cash_paid']=='1') ? 'YES' : '-',
                    'cash_date'                         =>  !empty($each_data['date_time_of_pbc']) ? date('d-m-Y h:i A', strtotime($each_data['date_time_of_pbc'])) : '-',
                ];
            }
        }
        $data['user_data']=$user;
        $data['results']=$modi_data;  
        $data['heading_title']="Payment Details"; 

        if ($request->has('is_export')) {
            if (isset($title_array) && count($title_array) > 0) {
                $data['heading_title'] .= "- " . implode(', ', $title_array);
            }
            return $data;
		}
        return view($this->view_path3.'paymentdetails_report', $data);
    }

    public function candidate_payment_details_pdf(Request $request) {
        $data = $this->candidate_payment_details($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path3.'paymentdetails_report_pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
    }
}
