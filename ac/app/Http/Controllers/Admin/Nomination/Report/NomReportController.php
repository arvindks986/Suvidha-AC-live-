<?php
namespace App\Http\Controllers\Admin\Nomination\Report;

use App\Http\Controllers\Controller;

use App\models\Admin\AcModel;
use App\models\Admin\CandidateModel;
use App\models\Admin\CandidateNominationModel;
use App\models\Admin\Nomination\NominationApplicationModel;
use App\models\Admin\DistrictModel;
use App\models\Admin\StateModel;
use App\models\Nomination\OnlineNomModel;
use App\models\Admin\Nomination\PreScrutiny\PreScrutinyModel;
use Common;
use App\commonModel;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class NomReportController extends Controller
{
    public $base = '';
    public $folder = '';
    public $view_path = "admin.nomination.onlinenom-report.eci_level.";
    public $view_path1 = "admin.nomination.onlinenom-report.ceo_level.";
    public $view_path2 = "admin.nomination.onlinenom-report.deo_level.";
    public $view_path3 = "admin.nomination.onlinenom-report.";

    public function _construct() {
        $this->commonModel = new commonModel();
    }

    public function get_report(Request $request)
    {

        $data = [];
        $data['between']            = explode(' - ', $request->date);   
        $data['election_phase']     = isset($request->election_phase) ? $request->election_phase : '';
        $data['election_type_id']   = isset($request->election_type_id) ? $request->election_type_id : '';
        $data['heading_title']      = "Total No. of Online Nomination";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/count-report-export");

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
        if(isset($data['election_phase']) && !empty($data['election_phase'])){
            $request_array[]          = "election_phase=".$request->election_phase;
        }
        if(isset($data['election_type_id']) && !empty($data['election_type_id'])){
            $request_array[]          = "election_type_id=".$request->election_type_id;
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
        $fetch_data = OnlineNomModel::get_count_offline(array_merge($default_user, $request->all())); //dd($fetch_data);
        $results = [];
        $i_count = 1;
        if(count($fetch_data)>0 && $fetch_data[0]['st_code'] != null){
            foreach($fetch_data as $each_data){
                $request_array['st_code']      = "st_code=".$each_data['st_code'];
                $action_url = Common::generate_url('online_nom/dist-wise-report').'?'.implode('&', array_unique($request_array)); 
                $online_nom                        =  OnlineNomModel::get_count_only(array('st_code'=>$each_data['st_code'], 'date'=>$request->date, 'election_type_id'=>$data['election_type_id'], 'election_phase'=>$data['election_phase']));
                $results[] = [
                    'sno'                               =>  $i_count++,
                    'st_code'                           =>  $each_data['st_code'],
                    'st_name'                           =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'action_url'                        =>  $action_url,
                    'online_nom'                        =>  $online_nom,
                    'candidate_list_url'                =>  Common::generate_url("online_nom/listofcandidate").'?'.implode('&', $request_array)
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

        return view($this->view_path . 'onlinenom_count_report', $data);
    }

    public function get_report_pdf(Request $request){
        $data = $this->get_report($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path.'.onlinenom_count_report_pdf',$data);
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
        $export_data[] = ["Datewise Online Nomination ".$date];
        $export_data[] = ['SNO','State Name', 'Offline Nomination' ,'Online Nomination','NFU Nomination','Total Nomination'];
        foreach($data['results'] as $each_result){
            $export_data[] = array_except($each_result, ['st_code', 'action_url']);
        }
        $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], "Datewise_Online_Nomination"));
        \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
            $excel->sheet('Sheet1', function($sheet) use($export_data) {
              $sheet->mergeCells('A1:F1');
              $sheet->cell('A1', function($cell) {
                $cell->setAlignment('center');
                $cell->setFontWeight('bold');
              });
              $sheet->fromArray($export_data,null,'A1',true,false);
            });
        })->export('xls');
    }

############################### For_dist_wise_report #######################################

    public function get_report_dist(Request $request)
    {
        $data = [];
        $data['between']            = explode(' - ', $request->date);
        $data['election_phase']     = isset($request->election_phase) ? $request->election_phase : '';
        $data['election_type_id']   = isset($request->election_type_id) ? $request->election_type_id : '';
        $data['heading_title']      = "Total No. of Online Nomination";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/dist-wise-report-export");

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
        if(isset($data['election_phase']) && !empty($data['election_phase'])){
            $request_array[]          = "election_phase=".$request->election_phase;
        }
        if(isset($data['election_type_id']) && !empty($data['election_type_id'])){
            $request_array[]          = "election_type_id=".$request->election_type_id;
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
        if(in_array(Auth::user()->designation, ['CEO','ECI'])){
            $data['buttons'][]  = [
                'name' => 'Back',
                'title' => 'Back',
                'href' =>  Common::generate_url("online_nom/count-report"),
                'target' => false
            ];
        }
        
        $fetch_data = OnlineNomModel::get_count_offline_dist(array_merge($default_user, $request->all()));
        $results = [];
        $i_count = 1;
        if(count($fetch_data)>0 && $fetch_data[0]['st_code'] != null){
            foreach($fetch_data as $each_data){
                $request_array['dist_no']      = "dist_no=".$each_data['dist_no']; 
                $action_url = Common::generate_url('online_nom/ac-wise-report').'?'.implode('&', \array_unique($request_array));
                    $online_nom                        =  OnlineNomModel::get_count_only(array('st_code'=>$each_data['st_code'], 'dist_no'=>$each_data['dist_no'], 'date'=>$request->date));
                $results[] = [
                    'sno'                               =>  $i_count++,
                    'st_code'                           =>  $each_data['st_code'],
                    'st_name'                           =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'dist_name'                         =>  $each_data['dist_no'].'-'.trim(getdistrictbydistrictno($each_data['st_code'], $each_data['dist_no'])->DIST_NAME),
                    'action_url'                        =>  $action_url,
                    'online_nom'                        =>  $online_nom,
                    'candidate_list_url'                =>  Common::generate_url("online_nom/listofcandidate").'?'.implode('&', array_merge($request_array, ['dist_no'=> 'dist_no='.$each_data['dist_no']]))
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

        return view($this->view_path1 . 'onlinenom_count_st_report', $data);
    }

    public function get_report_dist_pdf(Request $request){
        $data = $this->get_report_dist($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path1.'.onlinenom_count_report_st_pdf',$data);
        return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
    }

    public function get_report_dist_excel(Request $request){
        set_time_limit(6000);
        $data = $this->get_report_dist($request->merge(['is_export' => 1]));
        if(!empty($data['between'][0])) {
            $date = $data['between'][0].' - '.$data['between'][1] ;
        }else{
            $date = " ";
        }
        $export_data = [];
        $export_data[] = ["Datewise Online Nomination ".$date];
        $export_data[] = ['SNO','State Name', 'District Name', 'Offline Nomination' ,'Online Nomination','NFU Nomination','Total Nomination'];
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
        $data['election_phase']     = isset($request->election_phase) ? $request->election_phase : '';
        $data['election_type_id']   = isset($request->election_type_id) ? $request->election_type_id : '';
        $data['heading_title']      = "Total No. of Online Nomination";
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/ac-wise-report-export");

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
        if(isset($data['election_phase']) && !empty($data['election_phase'])){
            $request_array[]          = "election_phase=".$request->election_phase;
        }
        if(isset($data['election_type_id']) && !empty($data['election_type_id'])){
            $request_array[]          = "election_type_id=".$request->election_type_id;
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
            $back_url = Common::generate_url("online_nom/dist-wise-report").'?'.implode('&', $request_array);
        }else{
            $back_url = Common::generate_url("online_nom/dist-wise-report").'?'.implode('&', [$request_array[0]]);
        }
        $data['buttons'][]  = [
            'name' => 'Back',
            'title' => 'Back',
            'href' =>  $back_url,
            'target' => false
          ];
        
        $fetch_data = OnlineNomModel::get_count_offline_ac($request->all());

        $results = [];
        $i_count = 1;
        if(count($fetch_data)>0 && $fetch_data[0]['st_code'] != null){
            foreach($fetch_data as $each_data){
                $online_nom                        =  OnlineNomModel::get_count_only(array('st_code'=>$each_data['st_code'],'dist_no'=>$each_data['dist_no'],'ac_no'=>$each_data['ac_no'], 'date'=>$request->date));
                $results[] = [
                    'sno'                               =>  $i_count++,
                    'st_code'                           =>  $each_data['st_code'],
                    'st_name'                           =>  getstatebystatecode($each_data['st_code'])->ST_NAME,
                    'dist_name'                         =>  $each_data['dist_no'].'-'.trim(getdistrictbydistrictno($each_data['st_code'], $each_data['dist_no'])->DIST_NAME),
                    'ac_name'                           =>  $each_data['ac_no'].'-'.trim(getacbyacno($each_data['st_code'], $each_data['ac_no'])->AC_NAME),
                    // 'action_url'    =>  $action_url,
                    'online_nom'                        =>  $online_nom,
                    'candidate_list_url'                =>  Common::generate_url("online_nom/listofcandidate").'?'.implode('&', array_merge($request_array, ['ac_no'=> 'ac_no='.$each_data['ac_no']]))
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

        return view($this->view_path2 . 'onlinenom_count_dist_report', $data);
    }

    public function get_report_ac_pdf(Request $request){
        $data = $this->get_report_ac($request->merge(['is_export' => 1]));
        $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
        $pdf = \PDF::loadView($this->view_path2.'onlinenom_count_dist_report_pdf',$data);
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
        $export_data[] = ["Datewise Online Nomination ".$date];
        $export_data[] = ['SNO','State Name', 'District Name', 'AC Name', 'Offline Nomination' ,'Online Nomination','NFU Nomination','Total Nomination'];
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

    public function list_of_applicatiant(Request $request) {
        $data  = [];
        $data['between']            = explode(' - ', $request->date);
        $data['election_phase']     = isset($request->election_phase) ? $request->election_phase : '';
        $data['election_type_id']   = isset($request->election_type_id) ? $request->election_type_id : '';
        $data['filter']               = $request->all();
        $data['encrypt_id']           = '';
        $data['status_filter']        = $request->status;
        $user = Auth::user();
        $fil_status = $data['status_filter']; 
        $request_filter             = Common::get_request_filter($request);
        $ac_no                      = $request_filter['ac_no'];
        $st_code                    = $request_filter['st_code'];
        $dist_no                    = $request_filter['dist_no'];
        $action                     = Common::generate_url("online_nom/listofcandidate");
        $data['filter_action'] = $action;
        $form_filter_array = [
            'st_code' => true,
            'dist_no' => true,
            'ac_no' => true,
            'ps_no' => false,
            'designation' => false,
        ];
        $request_array  = [];
        if(isset($data['between'][0]) && !empty($data['between'][0])){
            $request_array[]          = "date=".$request->date;
        }
        if(isset($data['election_phase']) && !empty($data['election_phase'])){
            $request_array[]          = "election_phase=".$request->election_phase;
        }
        if(isset($data['election_type_id']) && !empty($data['election_type_id'])){
            $request_array[]          = "election_type_id=".$request->election_type_id;
        }
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
        $result=NominationApplicationModel::select('nomination_application.*', 'm_ac.DIST_NO_HDQTR')
        ->join('m_ac', [['nomination_application.st_code', '=', 'm_ac.ST_CODE'], ['nomination_application.ac_no', '=', 'm_ac.AC_NO']])
        ->join('m_election_details', [['nomination_application.st_code','=','m_election_details.st_code'],['nomination_application.ac_no','=','m_election_details.CONST_NO']])
        ->where('finalize', '=', '1')
        ->where('finalize_after_payment','1')
        ->where('application_type','2')
        ->where('m_election_details.CONST_TYPE', 'AC');
        
        if(!empty($data['filter']['election_type_id'])){
			$result->where('nomination_application.election_type_id', $data['filter']['election_type_id']);
		}
		if(!empty($data['filter']['election_phase'])){
			$result->where('m_election_details.ScheduleID', $data['filter']['election_phase']);
        }
        
        if(!empty($data['filter']['st_code'])){
            $result->where('nomination_application.st_code', '=', $data['filter']['st_code']);
        }
        
        if(!empty($data['filter']['dist_no'])){
            $result->where('m_ac.DIST_NO_HDQTR', '=', $data['filter']['dist_no']);
        }
        
        if(!empty($data['filter']['ac_no'])){
            $result->where('nomination_application.ac_no', '=', $data['filter']['ac_no']);
        }

        if($request['status']=='done'){
			$result->where('is_physical_verification_done', '=', '1');
		}elseif($request['status']=='pend'){
			$result->where('is_physical_verification_done', '=', '0');
        }

		if($request['appointment']=='done'){
			$result->join('appointment_schedule_date_time', [
                ['nomination_application.candidate_id', '=', 'appointment_schedule_date_time.candidate_id'],
                ['nomination_application.st_code', '=', 'appointment_schedule_date_time.st_code'],
                ['nomination_application.ac_no', '=', 'appointment_schedule_date_time.ac_no']])->where('is_ro_acccept', '1')->groupBy('appointment_schedule_date_time.candidate_id');
		}elseif($request['appointment']=='pend'){
			$result->join('appointment_schedule_date_time', [
                ['nomination_application.candidate_id', '=', 'appointment_schedule_date_time.candidate_id'],
                ['nomination_application.st_code', '=', 'appointment_schedule_date_time.st_code'],
                ['nomination_application.ac_no', '=', 'appointment_schedule_date_time.ac_no']])->where('is_ro_acccept', '0')->groupBy('appointment_schedule_date_time.candidate_id');
		}

        if($fil_status == 'cleared'){
            $result->where('is_physical_verification_done', '=', '1');
        }elseif($fil_status == 'pending'){
            $result->where('is_physical_verification_done', '=', '0');
        }
        $data['print_pdf_url'] = Common::generate_url("online_nom/listofcandidate-pdf").'?'.implode('&', $request_array);
        $results = $result->orderBy('nomination_application.id', 'asc')->get()->toarray();
        $data['user_data']=$user;
        $data['results']=$results;  
        $data['heading_title']="List of All online Nomination"; 

        $data['application_count'] = [
			'total_application'		=> PreScrutinyModel::get_count_application_com(array_merge($data['filter'], ['fil_status'=> ''])),
			'application_done' 	=> PreScrutinyModel::get_count_application_com(array_merge($data['filter'], ['fil_status'=> '1'])),
			'application_pending'	=> PreScrutinyModel::get_count_application_com(array_merge($data['filter'], ['fil_status'=> '2'])),
        ];
        
        $data['application_appointment_count'] = [
			'total_appointment'		=> OnlineNomModel::get_count_appointment_com(array_merge($data['filter'], ['fil_status'=> ''])),
			'appointment_done' 	=> OnlineNomModel::get_count_appointment_com(array_merge($data['filter'], ['fil_status'=> '1'])),
			'appointment_pending'	=> OnlineNomModel::get_count_appointment_com(array_merge($data['filter'], ['fil_status'=> '2'])),
		];

        if ($request->has('is_export')) {
            if (isset($title_array) && count($title_array) > 0) {
                $data['heading_title'] .= "- " . implode(', ', $title_array);
            }
            return $data;
		}
        return view($this->view_path3.'list-all-applicant', $data);	           
}  // end index function

public function list_of_applicatiant_pdf(Request $request){
    $data = $this->list_of_applicatiant($request->merge(['is_export' => 1]));
    $name_pdf = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path3.'.list-all-applicant-pdf',$data);
    return $pdf->download($name_pdf.'_'.date('d-m-Y').'_'.time().'.pdf');
}
}
