<?php

namespace App\Http\Controllers\Admin\BoothCountingReport;

use Illuminate\Http\Request;
use Common;
use App\models\Admin\BoothCountingReport\ActiveUserReport;
use Auth;
use App\Http\Controllers\Controller;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class ActiveUserReportController extends Controller
{
    protected $view_path       = 'admin.booth-counting-report.';
    protected $heading_title   = "Active User Report";
    protected $action_ac       = '/booth-counting';

    public function show_active_user(Request $request) {
        $user_data = Auth::user();
        $form_filter_array = [
        'st_code' => true,
        'dist_no' => true,
        'ac_no' => true,
        'ps_no' => false,
        'designation' => true,
        ];
    if($request->has('st_code')){
        $st_code = $request->st_code;
    }elseif(!empty($user_data->st_code)){
        $st_code = $user_data->st_code;
    }else{
        $st_code = '';
    }
    if($request->has('dist_no')){
        $dist_no = $request->dist_no;
    }elseif(!empty($user_data->dist_no)){
        $dist_no = $user_data->dist_no;
    }else{
        $dist_no = '';
    }
    if($request->has('ac_no')){
        $ac_no   = $request->ac_no; 
    }elseif(!empty($user_data->ac_no)){
        $ac_no   = $user_data->ac_no;
    }else{
        $ac_no   = '';
    }
    $baseurl = url()->current();
    $pdfurl = str_replace('active-user-detail', 'export_pdf_report_state', $baseurl);
	$excelurl = str_replace('active-user-detail', 'export_excel_active_users_details', $baseurl);
    $filter_data = Common::get_form_filters($form_filter_array, $request);
    $list_all = ActiveUserReport::get_user_by_filter($st_code,$dist_no,$ac_no,$user_data->designation);
    if ($request->has('is_excel')) {
        return ['list_all' => $list_all, 'filter_data' => $filter_data,
    'heading_title' => $this->heading_title, 'user_data' => $user_data, 'action' => $this->action_ac . '/active-user-report'
    , 'st_code' => $st_code, 'ac_no' => $ac_no, 'dist_no' => $dist_no];
    }
    return view($this->view_path.'activeuser', ['list_all'=>$list_all, 'filter_data'=>$filter_data,
    'heading_title'=>$this->heading_title, 'user_data'=>$user_data,'action'=>$this->action_ac.'/active-user-report'
    ,'st_code'=>$st_code,'ac_no'=>$ac_no,'dist_no'=>$dist_no,'pdf_url'=>$pdfurl,'excel_url'=>$excelurl]);
    }

    public function show_active_user_count(Request $request) {
        $user_data = Auth::user();
        $form_filter_array = [
        'st_code' => true,
        'dist_no' => true,
        'ac_no' => true,
        'ps_no' => false,
        'designation' => true,
        ];

    if($request->has('st_code')){
        $st_code = $request->st_code;
    }elseif(!empty($user_data->st_code)){
        $st_code = $user_data->st_code;
    }else{
        $st_code = '';
    }
    if($request->has('dist_no')){
        $dist_no = $request->dist_no;
    }elseif(!empty($user_data->dist_no)){
        $dist_no = $user_data->dist_no;
    }else{
        $dist_no = '';
    }
    if($request->has('ac_no')){
        $ac_no   = $request->ac_no; 
    }elseif(!empty($user_data->ac_no)){
        $ac_no   = $user_data->ac_no;
    }else{
        $ac_no   = '';
    }
    $baseurl = url()->current();
    $pdfurl = str_replace('active-user-report', 'export_pdf_count', $baseurl);
	$excelurl = str_replace('active-user-report', 'export_excel_active_users', $baseurl);
    $filter_data = Common::get_form_filters($form_filter_array, $request);
    $list_all = ActiveUserReport::get_user_by_count($st_code,$dist_no,$ac_no,$user_data->designation);

    if($request->has('is_excel')){
        return ['list_all'=>$list_all, 'filter_data'=>$filter_data,
    'heading_title'=>$this->heading_title, 'user_data'=>$user_data,'action'=>$this->action_ac.'/active-user-report'
    ,'st_code'=>$st_code,'ac_no'=>$ac_no,'dist_no'=>$dist_no];
    }

    return view($this->view_path.'mainactiveuser', ['list_all'=>$list_all, 'filter_data'=>$filter_data,
    'heading_title'=>$this->heading_title, 'user_data'=>$user_data,'action'=>$this->action_ac.'/active-user-report'
    ,'st_code'=>$st_code,'ac_no'=>$ac_no,'dist_no'=>$dist_no,'pdf_url'=>$pdfurl,'excel_url'=>$excelurl]);
    }

    public function export_pdf_report_state(Request $request){
    $data = $this->show_active_user($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.activeuserpdf',$data); 
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
    }

    public function export_pdf_count(Request $request){
    $data = $this->show_active_user_count($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.mainactiveuserpdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
    }
	
	public function export_excel_active_users(Request $request){
    set_time_limit(6000);
    $data = $this->show_active_user_count($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['Serial_No','State_Name', 'District_Name' ,'Ac_Name','RO_Name','Total_Computer_Assistant'];

    $count = 1;
    $sum   = 0;
    foreach ($data['list_all'] as $lis) { $sum += $lis->totalAsistantent;
      $export_data[] = [
        ($count)?$count:'0',
        getstatebystatecode($lis->st_code)->ST_NAME?getstatebystatecode($lis->st_code)->ST_NAME:'',
        getdistrictbydistrictno($lis->st_code,$lis->dist_no)->DIST_NAME?getdistrictbydistrictno($lis->st_code,$lis->dist_no)->DIST_NAME:'',
        getacbyacno($lis->st_code,$lis->ac_no)->AC_NAME?getacbyacno($lis->st_code,$lis->ac_no)->AC_NAME:'',
        getRonameById($lis->parent_id)->name?getRonameById($lis->parent_id)->name:'',
        ($lis->totalAsistantent)?$lis->totalAsistantent:'0'
      ];
      $count++;
    }
    $export_data[] = ['TOTAL','','','','',$sum ? $sum : '0'];
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    $headings[] = [$data['heading_title']];
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:F1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');
  }
  
  	public function export_excel_active_users_details(Request $request){
    set_time_limit(6000);
    $data = $this->show_active_user($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $export_data[] = ['Serial_No','State_Name', 'District_Name' ,'Ac_Name','Name','User_Name','Designation','Active_Status'];
    $headings[]=[];    
    $count = 1;
    foreach ($data['list_all'] as $lis) {
      $export_data[] = [
        ($count)?$count:'0',
        getstatebystatecode($lis->st_code)->ST_NAME?getstatebystatecode($lis->st_code)->ST_NAME:'',
        getdistrictbydistrictno($lis->st_code,$lis->dist_no)->DIST_NAME?getdistrictbydistrictno($lis->st_code,$lis->dist_no)->DIST_NAME:'',
        getacbyacno($lis->st_code,$lis->ac_no)->AC_NAME?getacbyacno($lis->st_code,$lis->ac_no)->AC_NAME:'',
        $lis->name?$lis->name:'',
		$lis->officername?$lis->officername:'',
		$lis->designation?$lis->designation:'',
        $lis->is_active == 1 ?'Active':'Not Active'
      ];
      $count++;
    }
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:H1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');
  }
}
