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

class PhysicalVerificationReportController extends Controller
{
    public $base = '';
    public $folder = '';
    public $view_path = "admin.nomination.onlinenom-report.eci_level.physical_verification.";
    public $view_path1 = "admin.nomination.onlinenom-report.ceo_level.physical_verification.";
    public $view_path2 = "admin.nomination.onlinenom-report.deo_level.physical_verification.";
    public $view_path3 = "admin.nomination.onlinenom-report.payment_details.";

    public function get_report(Request $request)
    {
        $data = [];
        $data['between']            = explode(' - ', $request->date);
        $data['heading_title']      = "Physical Verification Report";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/count-physicalverification-report-export");

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
        $fetch_data = OnlineNomModel::getall_st(array_merge($default_user, $request->all()));

        $results = [];
        $i_count = 1;
        if(count($fetch_data)>0 && $fetch_data[0]['st_code'] != null){
            foreach($fetch_data as $each_data){
                $request_array['st_code']      = "st_code=".$each_data['st_code'];
                $action_url = Common::generate_url('online_nom/dist-wise-physicalverification-report').'?'.implode('&', array_unique($request_array));
                $results[] = [
                    'sno'                               =>  $i_count++,
                    'st_code'                           =>  $each_data['st_code'],
                    'st_name'                           =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'action_url'                        =>  $action_url,
                    'pending_physical_verification'     =>  OnlineNomModel::get_count_physicalverification_wise(array('st_code'=>$each_data['st_code'], 'status'=> 'pend', 'date'=>$request->date)),
                    'done_physical_verification'        =>  OnlineNomModel::get_count_physicalverification_wise(array('st_code'=>$each_data['st_code'], 'status'=> 'done', 'date'=>$request->date)),
                    'total_nomination'                  =>  OnlineNomModel::get_count_physicalverification_wise(array('st_code'=>$each_data['st_code'], 'status'=> '', 'date'=>$request->date)),
                    'pending_physical_verification_url' =>  Common::generate_url('online_nom/listofcandidate').'?'.implode('&', array_unique(array_merge($request_array, ['status'=>'status=pend']))),
                    'done_physical_verification_url'    =>  Common::generate_url('online_nom/listofcandidate').'?'.implode('&', array_unique(array_merge($request_array, ['status'=>'status=done']))),
                    'total_nomination_url'              =>  Common::generate_url('online_nom/listofcandidate').'?'.implode('&', array_unique(array_merge($request_array, ['status'=>'status='])))
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

        return view($this->view_path . 'onlinen_physicalverification_report', $data);
    }

    public function get_report_pdf(Request $request){
        $data = $this->get_report($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path.'.onlinen_physicalverification_report_pdf',$data);
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
        $export_data[] = ["Physical Verification Report ".$date];
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
        $data['heading_title']      = "Physical Verification Report";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/dist-wise-physicalverification-report-export");

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
            $back_url = Common::generate_url("online_nom/count-physicalverification-report");
        }
        $data['buttons'][]  = [
            'name' => 'Back',
            'href' =>  $back_url,
            'target' => false
        ];
        $fetch_data = OnlineNomModel::getall_dist_wise_physicalverification(array_merge($default_user, $request->all()));
        $results = [];
        $i_count = 1;
        if(count($fetch_data)>0 && $fetch_data[0]['st_code'] != null){
            foreach($fetch_data as $each_data){
                $request_array['dist_no']      = "dist_no=".$each_data['dist_no'];    
                $action_url = Common::generate_url('online_nom/ac-wise-physicalverification-report').'?'.implode('&', \array_unique($request_array));

                $results[] = [
                    'sno'                            =>  $i_count++,
                    'st_code'                        =>  $each_data['st_code'],
                    'st_name'                        =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'dist_name'                      =>  $each_data['dist_no'].'-'.trim(getdistrictbydistrictno($each_data['st_code'], $each_data['dist_no'])->DIST_NAME),
                    'action_url'                     =>  $action_url,
                    'pending_physical_verification'     =>  OnlineNomModel::get_count_physicalverification_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'status'=> 'pend', 'date'=>$request->date)),
                    'done_physical_verification'        =>  OnlineNomModel::get_count_physicalverification_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'],'status'=> 'done', 'date'=>$request->date)),
                    'total_nomination'                  =>  OnlineNomModel::get_count_physicalverification_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'],'status'=> '', 'date'=>$request->date)),
                    'pending_physical_verification_url' =>  Common::generate_url('online_nom/listofcandidate').'?'.implode('&', array_unique(array_merge($request_array, ['status'=>'status=pend']))),
                    'done_physical_verification_url'    =>  Common::generate_url('online_nom/listofcandidate').'?'.implode('&', array_unique(array_merge($request_array, ['status'=>'status=done']))),
                    'total_nomination_url'              =>  Common::generate_url('online_nom/listofcandidate').'?'.implode('&', array_unique(array_merge($request_array, ['status'=>'status='])))
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
        return view($this->view_path1 . 'onlinen_physicalverification_report_st', $data);
    }

    public function get_report_dist_pdf(Request $request){
        $data = $this->get_report_dist($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path1.'.onlinen_physicalverification_report_st_pdf',$data);
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
        $export_data[] = ["Physical Verification Report ".$date];
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
        $data['heading_title']      = "Physical Verification Report";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/ac-wise-physicalverification-report-export");

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
            $back_url = Common::generate_url("online_nom/dist-wise-physicalverification-report").'?'.implode('&', $request_array);
        }else{
            $back_url = Common::generate_url("online_nom/dist-wise-physicalverification-report").'?'.implode('&', [$request_array[0]]);
        }
        $data['buttons'][]  = [
            'name' => 'Back',
            'href' =>  $back_url,
            'target' => false
          ];
        
        $fetch_data = OnlineNomModel::getall_ac_wise_physicalverification($request->all());

        $results = [];
        $i_count = 1;
        if(count($fetch_data)>0 && $fetch_data[0]['st_code'] != null){
            foreach($fetch_data as $each_data){    
                $results[] = [
                    'sno'                               =>  $i_count++,
                    'st_code'                           =>  $each_data['st_code'],
                    'st_name'                           =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'dist_name'                         =>  $each_data['dist_no'].'-'.trim(getdistrictbydistrictno($each_data['st_code'], $each_data['dist_no'])->DIST_NAME),
                    'ac_name'                           =>  $each_data['ac_no'].'-'.trim(getacbyacno($each_data['st_code'], $each_data['ac_no'])->AC_NAME),
                    'pending_physical_verification'     =>  OnlineNomModel::get_count_physicalverification_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'],'ac_no'=>$each_data['ac_no'], 'status'=> 'pend', 'date'=>$request->date)),
                    'done_physical_verification'        =>  OnlineNomModel::get_count_physicalverification_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'],'ac_no'=>$each_data['ac_no'],'status'=> 'done', 'date'=>$request->date)),
                    'total_nomination'                  =>  OnlineNomModel::get_count_physicalverification_wise(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'],'ac_no'=>$each_data['ac_no'],'status'=> '', 'date'=>$request->date)),
                    'pending_physical_verification_url' =>  Common::generate_url('online_nom/listofcandidate').'?'.implode('&', array_unique(array_merge($request_array, ['ac_no'=>'ac_no='.$each_data['ac_no'],'status'=>'status=pend']))),
                    'done_physical_verification_url'    =>  Common::generate_url('online_nom/listofcandidate').'?'.implode('&', array_unique(array_merge($request_array, ['ac_no'=>'ac_no='.$each_data['ac_no'],'status'=>'status=done']))),
                    'total_nomination_url'              =>  Common::generate_url('online_nom/listofcandidate').'?'.implode('&', array_unique(array_merge($request_array, ['ac_no'=>'ac_no='.$each_data['ac_no'],'status'=>'status='])))
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

        return view($this->view_path2 . 'onlinen_physicalverification_report_dist', $data);
    }

    public function get_report_ac_pdf(Request $request){
        $data = $this->get_report_ac($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path2.'onlinen_physicalverification_report_dist_pdf',$data);
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
        $export_data[] = ["Physical Verification Report ".$date];
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
}
