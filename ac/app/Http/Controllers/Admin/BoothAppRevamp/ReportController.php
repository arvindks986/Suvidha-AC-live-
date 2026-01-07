<?php 
namespace App\Http\Controllers\Admin\BoothAppRevamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use App\commonModel;  
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, TblPollSummaryModel, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, TblAnalyticsDashboardModel, StateModel, AcModel, DistrictModel, JsonFile};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;
// use Excel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\Admin\BoothAppRevamp\PollingController;

use App\models\Admin\BoothAppRevamp\PollEventReportExcel;
//current

class ReportController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $ps_no         = NULL;
  public $role_id       = 0;
  public $base          = 'roac';
  public $restricted_ps = [];
  public $allowed_acs = [];
  public $allowed_dist_no = [];
  public $allowed_st_code = [];

  public function __construct(Request $request){
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      if(in_array(Auth::user()->st_code,$this->allowed_st_code) && in_array(Auth::user()->ac_no,$this->allowed_acs) && in_array(Auth::user()->dist_no,$this->allowed_dist_no)){

      }
      $default_values = Common::get_request_filter($request);
      $this->ac_no    = $default_values['ac_no'];
      $this->st_code  = $default_values['st_code'];
      $this->ps_no    = $default_values['ps_no'];
      $this->phase_no    = $default_values['phase_no'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id  = $default_values['role_id'];
      $this->base     = $default_values['base'];

      $object_setting         = Common::get_allowed_acs($request);
      $this->allowed_st_code  = $object_setting['allowed_st_code'];
      $this->allowed_dist_no  = $object_setting['allowed_dist_no'];
      $this->allowed_acs      = $object_setting['allowed_acs'];
	
      return $next($request);
    });
  }


  public function officer_assignment_report($phase_no = 0,Request $request){
	  
		$allowed_st_code = implode(', ', $this->allowed_st_code);
		$allowed_acs = implode(', ', $this->allowed_acs);
	
	
		
	
	
	//try{
					
		$data = [];
		
		$filter = [
			'phase_no'        => $this->phase_no,
			'st_code'         => $this->st_code,
			'ac_no'           => $this->ac_no
		  ];
			
		$results1    =   PollingStation::get_polling_stations_count($filter);
		

		 $query = "SELECT s.st_code,s.st_name,a.AC_NO,ac_name,pso.ps_no,COUNT(DISTINCT(IF(role_id=33,p.ps_no,NULL))) total_blo,COUNT(DISTINCT(IF(role_id=35,p.ps_no,NULL))) total_pro,COUNT(DISTINCT(IF(role_id=34,p.ps_no,NULL))) total_po,COUNT(DISTINCT(IF(role_id=38,pso.ps_no,NULL))) total_sm FROM polling_station_officer p 
		 JOIN m_state s ON p.st_code=s.st_code 
		 JOIN m_election_details ph ON p.st_code=ph.st_code and p.ac_no=ph.CONST_NO
		 JOIN m_ac a ON p.st_code=a.st_code AND p.ac_no=a.ac_no 
		 LEFT JOIN ps_sector_officer pso ON p.st_code=pso.st_code and p.ac_no=pso.ac_no and (FIND_IN_SET(pso.ps_no, p.ps_no) > 0) and pso.is_deleted = '0'
		 WHERE ph.CONST_TYPE = 'AC' AND (s.ST_CODE = '$allowed_st_code' and a.AC_NO IN ($allowed_acs)) ";


		if($phase_no>0){
			$query .= " and ph.phase_no = '$phase_no'";
		}


		if($this->phase_no){			
			$phase_no = $this->phase_no;
			$query .= " and ph.phase_no = '$phase_no'";
		}
		
		

		if($this->st_code){			
			$st_code = $this->st_code;
			$query .= " and s.st_code = '$st_code'";
		}
		
		if($this->ac_no){			
			$ac_no = $this->ac_no;
			$query .= " and a.ac_no = '$ac_no'";
		}

		$query .= " GROUP BY s.st_code,a.AC_NO ORDER BY s.st_code,a.AC_NO ASC";
						
		$results2 = DB::select($query);
		
		//echo '<pre>'; print_r($results1); echo '</pre>';
		//echo '<pre>'; print_r($results2); die;
			
		$arrFinal = array();
		
        foreach($results1 as $key=>$val){
			foreach($results2 as $dataa){
			
				if(($val['ST_CODE']== $dataa->st_code) && ($val['AC_NO']== $dataa->AC_NO)){					
					$arrFinal[] = (object)[ 
						'AC_NO' 	=> $val['AC_NO'],
						'total_ps' 	=> $val['total_ps'],
						'st_name' 	=> @$dataa->st_name,
						'ac_name' 	=> @$dataa->ac_name,
						'total_blo' => @$dataa->total_blo,
						'total_pro' => @$dataa->total_pro,
						'total_po'  => @$dataa->total_po,
						'total_sm'  => @$dataa->total_sm,
						'href' 		=> Common::generate_url('booth-app-revamp/officer-assignment-ps-wise-report').'?st_code='.@$dataa->st_code.'&ac_no='.@$dataa->AC_NO.'&phase_no='.@$this->phase_no
						];					
				}
				
			}
        }
		
		$data['data'] = $arrFinal;
		
		
		$data['user_data'] = Auth::user();
		
		if(Auth::user()->role_id == '19'){
			$prefix 	= 'roac';
		}else if(Auth::user()->role_id == '4'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '5'){
			$prefix 	= 'acdeo';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}
				
		$data['pdf_btn'] = "$prefix/booth-app-revamp/officer-assignment-report-pdf/$phase_no";
		$data['excel_btn'] = "$prefix/booth-app-revamp/officer-assignment-report-xls/$phase_no";

		//form filters
		$data['filter_action'] = Common::generate_url("booth-app-revamp/officer-assignment-report");
		
		$form_filter_array = [
		  'phase_no'     => true,
		  'st_code'     => true,
		  'dist_no'     => false,
		  'ac_no'       => true, 
		  'ps_no'       => false, 
		  'designation'     => false,
		];
		
		$form_filters = Common::get_form_filters($form_filter_array, $request);      
		$data['form_filters'] = $form_filters;

		if($request->path() == "$prefix/booth-app-revamp/officer-assignment-report-pdf/$phase_no"){
			
			$pdf = PDF::loadView($this->view.'.Reports.officer-assignment-report-pdf',$data);
			
			return $pdf->download('Booth App - Officer Assignment Report.pdf');
			
		}else if($request->path() == "$prefix/booth-app-revamp/officer-assignment-report-xls/$phase_no"){
		
			return Excel::create('Booth App - Officer Assignment Report', function($excel) use ($data) {
				
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
				  
					$sheet->mergeCells('A1:F1');	
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('Booth App - Officer Assignment Report');
						$cells->setAlignment('center');
						$cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
					
					$sheet->cell('A3', function($cell) {$cell->setValue('S.N.'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('B3', function($cell) {$cell->setValue('State/UT NAME'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('C3', function($cell) {$cell->setValue('AC No. & AC Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('D3', function($cell) {$cell->setValue('Total PS'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('E3', function($cell) {$cell->setValue('BLO Assigned'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('F3', function($cell) {$cell->setValue('PO Assigned'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('G3', function($cell) {$cell->setValue('PRO Assigned'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('H3', function($cell) {$cell->setValue('SM Assigned'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					
					 if (!empty($data)) {
						foreach ($data['data'] as $key => $result) {
							$i= $key+4;
							$sheet->cell('A'.$i, $key + 1); 
							$sheet->cell('B'.$i, $result->st_name ); 
							$sheet->cell('C'.$i, $result->AC_NO.'-'.$result->ac_name ); 
							$sheet->cell('D'.$i, ($result->total_ps > 0) ? $result->total_ps:'=(0)' ); 
							$sheet->cell('E'.$i, ($result->total_blo > 0) ? $result->total_blo:'=(0)' ); 
							$sheet->cell('F'.$i, ($result->total_po > 0) ? $result->total_po:'=(0)' );
							$sheet->cell('G'.$i, ($result->total_pro > 0) ? $result->total_pro:'=(0)' );
							$sheet->cell('H'.$i, ($result->total_sm > 0) ? $result->total_sm:'=(0)' );
						}
					}
		
				});
			})->download('xls');
		
		
		}else{
			return view($this->view.'.Reports.officer-assignment-report', $data);
		}
		
	 /* }catch(\Exception $e){
	  return Redirect::to($this->base.'/dashboard');
	} */

  }
  
  
  
  public function officer_assignment_ps_wise_report($phase_no = 0,Request $request){
	 
		$allowed_st_code = implode(', ', $this->allowed_st_code);
		$allowed_acs = implode(', ', $this->allowed_acs);
	
	//try{
					
		$data = [];
		
		$filter = [
			'phase_no'        => $this->phase_no,
			'st_code'         => $this->st_code,
			'ac_no'           => $this->ac_no,
			'ps_no'           => $this->ps_no,
		  ];
			
		$results1    =   PollingStation::get_polling_stations_count_ps_wise($filter);
		
		$query = "SELECT s.st_code,s.st_name,a.AC_NO,pso.PS_NO,ac_name,COUNT(DISTINCT(IF(role_id=33,p.ps_no,NULL))) total_blo,COUNT(DISTINCT(IF(role_id=35,p.ps_no,NULL))) total_pro,COUNT(DISTINCT(IF(role_id=34,p.ps_no,NULL))) total_po,COUNT(DISTINCT(IF(role_id=38,p.ps_no,NULL))) total_sm FROM polling_station_officer p 
		JOIN m_state s ON p.st_code=s.st_code 
		JOIN m_election_details ph ON p.st_code=ph.st_code and p.ac_no=ph.CONST_NO
		JOIN m_ac a ON p.st_code=a.st_code AND p.ac_no=a.ac_no 
		LEFT JOIN ps_sector_officer pso ON p.st_code=pso.st_code and p.ac_no=pso.ac_no and (FIND_IN_SET(pso.ps_no, p.ps_no) > 0) and pso.is_deleted = '0'
		WHERE ph.CONST_TYPE = 'AC' AND (s.ST_CODE = '$allowed_st_code' and a.AC_NO IN ($allowed_acs))";
		if($phase_no>0){
			$query .= " and ph.phase_no = '$phase_no'";
		}

		if($this->phase_no){			
			$phase_no = $this->phase_no;
			$query .= " and ph.phase_no = '$phase_no'";
		}
		
		if($this->st_code){			
			$st_code = $this->st_code;
			$query .= " and s.st_code = '$st_code'";
		}
		
		if($this->ac_no){			
			$ac_no = $this->ac_no;
			$query .= " and a.ac_no = '$ac_no'";
		}
		
		if($this->ps_no){			
			$ps_no = $this->ps_no;
			$query .= " and p.ps_no = '$ps_no'";
		}

		$query .= " GROUP BY s.st_code,a.AC_NO,pso.PS_NO ORDER BY s.st_code,a.AC_NO,pso.ps_no+0 ASC";
						
		$results2 = DB::select($query);
		
		//echo '<pre>'; print_r($results1); echo '</pre>';
		//echo '<pre>'; print_r($results2); die;
		
		
		$arrFinal = array();
		
		
        foreach($results1 as $key=>$val){
			foreach($results2 as $dataa){
			
				if(($val['ST_CODE']== $dataa->st_code) && ($val['AC_NO']== $dataa->AC_NO)&& ($val['PS_NO']== $dataa->PS_NO)){	
			
					$arrFinal[] = (object)[ 
						'AC_NO' 		=> $val['AC_NO'],
						'PS_NO' 		=> $val['PS_NO'],
						'PS_NAME_EN' 	=> $val['PS_NAME_EN'],
						'total_ps' 		=> $val['total_ps'],
						'st_name' 		=> @$dataa->st_name,
						'ac_name' 		=> @$dataa->ac_name,
						'total_blo' 	=> @$dataa->total_blo,
						'total_pro' 	=> @$dataa->total_pro,
						'total_po'  	=> @$dataa->total_po,
						'total_sm'  	=> @$dataa->total_sm
						];

					//break;
					continue 2;
				}
			}
			
				$st = getstatebystatecode($val['ST_CODE']);

				$ac = getacbyacno($val['ST_CODE'],$val['AC_NO']);
			
			
					$arrFinal[] = (object)[ 
						'AC_NO' 		=> $val['AC_NO'],
						'PS_NO' 		=> $val['PS_NO'],
						'PS_NAME_EN' 	=> $val['PS_NAME_EN'],
						'st_name' 		=> $st->ST_NAME,
						'ac_name' 		=> $ac->AC_NAME,
						'total_ps' 		=> $val['total_ps'],
						'total_blo' 	=> 0,
						'total_pro' 	=> 0,
						'total_po'  	=> 0,
						'total_sm'  	=> 0
						];	
        }
		
		
		//echo '<pre>'; print_r($arrFinal); die;
		
		
		$data['data'] = $arrFinal;
		
		
		$data['user_data'] = Auth::user();
		
		if(Auth::user()->role_id == '19'){
			$prefix 	= 'roac';
		}else if(Auth::user()->role_id == '4'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '5'){
			$prefix 	= 'acdeo';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}
				
		
		$data['pdf_btn'] = Common::generate_url('booth-app-revamp/officer-assignment-ps-wise-report-pdf').'?st_code='.@$dataa->st_code.'&ac_no='.@$dataa->AC_NO.'&phase_no='.@$this->phase_no;
		$data['excel_btn'] = Common::generate_url('booth-app-revamp/officer-assignment-ps-wise-report-xls').'?st_code='.@$dataa->st_code.'&ac_no='.@$dataa->AC_NO.'&phase_no='.@$this->phase_no;

		//form filters
		$data['filter_action'] = Common::generate_url("booth-app-revamp/officer-assignment-ps-wise-report");
		
		$form_filter_array = [
		  'phase_no'     => true,
		  'st_code'     => true,
		  'dist_no'     => false,
		  'ac_no'       => true, 
		  'ps_no'       => false, 
		  'designation'     => false,
		];
		
		$form_filters = Common::get_form_filters($form_filter_array, $request);      
		$data['form_filters'] = $form_filters;
		
			return view($this->view.'.Reports.officer-assignment-ps-wise-report', $data);

		
	 /* }catch(\Exception $e){
	  return Redirect::to($this->base.'/dashboard');
	} */

  }
  
  public function officer_assignment_ps_wise_report_pdf($phase_no = 0,Request $request){
	 
		$allowed_st_code = implode(', ', $this->allowed_st_code);
		$allowed_acs = implode(', ', $this->allowed_acs);
	
	//try{
					
		$data = [];
		
		$filter = [
			'phase_no'        => $this->phase_no,
			'st_code'         => $this->st_code,
			'ac_no'           => $this->ac_no,
			'ps_no'           => $this->ps_no,
		  ];
			
		$results1    =   PollingStation::get_polling_stations_count_ps_wise($filter);
		
		$query = "SELECT s.st_code,s.st_name,a.AC_NO,pso.PS_NO,ac_name,COUNT(DISTINCT(IF(role_id=33,p.ps_no,NULL))) total_blo,COUNT(DISTINCT(IF(role_id=35,p.ps_no,NULL))) total_pro,COUNT(DISTINCT(IF(role_id=34,p.ps_no,NULL))) total_po,COUNT(DISTINCT(IF(role_id=38,p.ps_no,NULL))) total_sm FROM polling_station_officer p 
		JOIN m_state s ON p.st_code=s.st_code 
		JOIN ps_sector_officer pso ON p.st_code=pso.st_code and p.ac_no=pso.ac_no and (FIND_IN_SET(pso.ps_no, p.ps_no) > 0)
		JOIN m_election_details ph ON p.st_code=ph.st_code and p.ac_no=ph.CONST_NO
		JOIN m_ac a ON p.st_code=a.st_code AND p.ac_no=a.ac_no WHERE ph.CONST_TYPE = 'AC' AND (s.ST_CODE = '$allowed_st_code' and a.AC_NO IN ($allowed_acs)) and pso.is_deleted = '0'";

		if($phase_no>0){
			$query .= " and ph.phase_no = '$phase_no'";
		}

		if($this->phase_no){			
			$phase_no = $this->phase_no;
			$query .= " and ph.phase_no = '$phase_no'";
		}
		
		if($this->st_code){			
			$st_code = $this->st_code;
			$query .= " and s.st_code = '$st_code'";
		}
		
		if($this->ac_no){			
			$ac_no = $this->ac_no;
			$query .= " and a.ac_no = '$ac_no'";
		}
		
		if($this->ps_no){			
			$ps_no = $this->ps_no;
			$query .= " and p.ps_no = '$ps_no'";
		}

		$query .= " GROUP BY s.st_code,a.AC_NO,p.ps_no ORDER BY s.st_code,a.AC_NO,p.ps_no+0 ASC";
						
		$results2 = DB::select($query);
		
		//echo '<pre>'; print_r($results1); echo '</pre>'; die;
		//echo '<pre>'; print_r($results2); die;
		
		
		$arrFinal = array();
		
		
        foreach($results1 as $key=>$val){
			foreach($results2 as $dataa){
			
				if(($val['ST_CODE']== $dataa->st_code) && ($val['AC_NO']== $dataa->AC_NO)&& ($val['PS_NO']== $dataa->PS_NO)){	
			
					$arrFinal[] = (object)[ 
						'AC_NO' 		=> $val['AC_NO'],
						'PS_NO' 		=> $val['PS_NO'],
						'PS_NAME_EN' 	=> $val['PS_NAME_EN'],
						'total_ps' 		=> $val['total_ps'],
						'st_name' 		=> @$dataa->st_name,
						'ac_name' 		=> @$dataa->ac_name,
						'total_blo' 	=> @$dataa->total_blo,
						'total_pro' 	=> @$dataa->total_pro,
						'total_po'  	=> @$dataa->total_po,
						'total_sm'  	=> @$dataa->total_sm
						];

					//break;
					continue 2;
				}
			}
			
				$st = getstatebystatecode($val['ST_CODE']);

				$ac = getacbyacno($val['ST_CODE'],$val['AC_NO']);
			
			
					$arrFinal[] = (object)[ 
						'AC_NO' 		=> $val['AC_NO'],
						'PS_NO' 		=> $val['PS_NO'],
						'PS_NAME_EN' 	=> $val['PS_NAME_EN'],
						'st_name' 		=> $st->ST_NAME,
						'ac_name' 		=> $ac->AC_NAME,
						'total_ps' 		=> $val['total_ps'],
						'total_blo' 	=> 0,
						'total_pro' 	=> 0,
						'total_po'  	=> 0,
						'total_sm'  	=> 0
						];	
        }
		
		
		//echo '<pre>'; print_r($arrFinal); die;
		
		
		$data['data'] = $arrFinal;
		
		
		$data['user_data'] = Auth::user();
		
		if(Auth::user()->role_id == '19'){
			$prefix 	= 'roac';
		}else if(Auth::user()->role_id == '4'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '5'){
			$prefix 	= 'acdeo';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}
				
	
			
			$pdf = PDF::loadView($this->view.'.Reports.officer-assignment-ps-wise-report-pdf',$data);
			
			return $pdf->download('Booth App - Officer Assignment Report.pdf');
			

		
	 /* }catch(\Exception $e){
	  return Redirect::to($this->base.'/dashboard');
	} */

  }
  
  
  public function officer_assignment_ps_wise_report_xls($phase_no = 0,Request $request){
	 
		$allowed_st_code = implode(', ', $this->allowed_st_code);
		$allowed_acs = implode(', ', $this->allowed_acs);
	
	//try{
					
		$data = [];
		
		$filter = [
			'phase_no'        => $this->phase_no,
			'st_code'         => $this->st_code,
			'ac_no'           => $this->ac_no,
			'ps_no'           => $this->ps_no,
		  ];
			
		$results1    =   PollingStation::get_polling_stations_count_ps_wise($filter);
		
		$query = "SELECT s.st_code,s.st_name,a.AC_NO,pso.PS_NO,ac_name,COUNT(DISTINCT(IF(role_id=33,p.ps_no,NULL))) total_blo,COUNT(DISTINCT(IF(role_id=35,p.ps_no,NULL))) total_pro,COUNT(DISTINCT(IF(role_id=34,p.ps_no,NULL))) total_po,COUNT(DISTINCT(IF(role_id=38,p.ps_no,NULL))) total_sm FROM polling_station_officer p 
		JOIN m_state s ON p.st_code=s.st_code 
		JOIN ps_sector_officer pso ON p.st_code=pso.st_code and p.ac_no=pso.ac_no and (FIND_IN_SET(pso.ps_no, p.ps_no) > 0)
		JOIN m_election_details ph ON p.st_code=ph.st_code and p.ac_no=ph.CONST_NO
		JOIN m_ac a ON p.st_code=a.st_code AND p.ac_no=a.ac_no WHERE ph.CONST_TYPE = 'AC' AND (s.ST_CODE = '$allowed_st_code' and a.AC_NO IN ($allowed_acs)) and pso.is_deleted = '0'";

		if($phase_no>0){
			$query .= " and ph.phase_no = '$phase_no'";
		}

		if($this->phase_no){			
			$phase_no = $this->phase_no;
			$query .= " and ph.phase_no = '$phase_no'";
		}
		
		if($this->st_code){			
			$st_code = $this->st_code;
			$query .= " and s.st_code = '$st_code'";
		}
		
		if($this->ac_no){			
			$ac_no = $this->ac_no;
			$query .= " and a.ac_no = '$ac_no'";
		}
		
		if($this->ps_no){			
			$ps_no = $this->ps_no;
			$query .= " and p.ps_no = '$ps_no'";
		}

		$query .= " GROUP BY s.st_code,a.AC_NO,p.ps_no ORDER BY s.st_code,a.AC_NO,p.ps_no+0 ASC";
						
		$results2 = DB::select($query);
		
		//echo '<pre>'; print_r($results1); echo '</pre>'; die;
		//echo '<pre>'; print_r($results2); die;
		
		
		$arrFinal = array();
		
		
        foreach($results1 as $key=>$val){
			foreach($results2 as $dataa){
			
				if(($val['ST_CODE']== $dataa->st_code) && ($val['AC_NO']== $dataa->AC_NO)&& ($val['PS_NO']== $dataa->PS_NO)){	
			
					$arrFinal[] = (object)[ 
						'AC_NO' 		=> $val['AC_NO'],
						'PS_NO' 		=> $val['PS_NO'],
						'PS_NAME_EN' 	=> $val['PS_NAME_EN'],
						'total_ps' 		=> $val['total_ps'],
						'st_name' 		=> @$dataa->st_name,
						'ac_name' 		=> @$dataa->ac_name,
						'total_blo' 	=> @$dataa->total_blo,
						'total_pro' 	=> @$dataa->total_pro,
						'total_po'  	=> @$dataa->total_po,
						'total_sm'  	=> @$dataa->total_sm
						];

					//break;
					continue 2;
				}
			}
			
				$st = getstatebystatecode($val['ST_CODE']);

				$ac = getacbyacno($val['ST_CODE'],$val['AC_NO']);
			
			
					$arrFinal[] = (object)[ 
						'AC_NO' 		=> $val['AC_NO'],
						'PS_NO' 		=> $val['PS_NO'],
						'PS_NAME_EN' 	=> $val['PS_NAME_EN'],
						'st_name' 		=> $st->ST_NAME,
						'ac_name' 		=> $ac->AC_NAME,
						'total_ps' 		=> $val['total_ps'],
						'total_blo' 	=> 0,
						'total_pro' 	=> 0,
						'total_po'  	=> 0,
						'total_sm'  	=> 0
						];	
        }
		
		
		//echo '<pre>'; print_r($arrFinal); die;
		
		
		$data['data'] = $arrFinal;
		
		
		$data['user_data'] = Auth::user();
		
		if(Auth::user()->role_id == '19'){
			$prefix 	= 'roac';
		}else if(Auth::user()->role_id == '4'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '5'){
			$prefix 	= 'acdeo';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}
				
		

		
			return Excel::create('Booth App - Officer Assignment Report', function($excel) use ($data) {
				
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
				  
					$sheet->mergeCells('A1:F1');	
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('Booth App - Officer Assignment Report');
						$cells->setAlignment('center');
						$cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
					
					$sheet->cell('A3', function($cell) {$cell->setValue('S.N.'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('B3', function($cell) {$cell->setValue('State/UT NAME'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('C3', function($cell) {$cell->setValue('AC No. & AC Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('D3', function($cell) {$cell->setValue('PS No. & PS Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('E3', function($cell) {$cell->setValue('BLO Assigned'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('F3', function($cell) {$cell->setValue('PO Assigned'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('G3', function($cell) {$cell->setValue('PRO Assigned'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('H3', function($cell) {$cell->setValue('SM Assigned'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					
					 if (!empty($data)) {
						foreach ($data['data'] as $key => $result) {
							$i= $key+4;
							$sheet->cell('A'.$i, $key + 1); 
							$sheet->cell('B'.$i, $result->st_name ); 
							$sheet->cell('C'.$i, $result->AC_NO.'-'.$result->ac_name ); 
							$sheet->cell('D'.$i, $result->PS_NO.'-'.$result->PS_NAME_EN ); 
							$sheet->cell('E'.$i, ($result->total_blo > 0) ? $result->total_blo:'=(0)' ); 
							$sheet->cell('F'.$i, ($result->total_po > 0) ? $result->total_po:'=(0)' );
							$sheet->cell('G'.$i, ($result->total_pro > 0) ? $result->total_pro:'=(0)' );
							$sheet->cell('H'.$i, ($result->total_sm > 0) ? $result->total_sm:'=(0)' );
						}
					}
		
				});
			})->download('xls');

		
	 /* }catch(\Exception $e){
	  return Redirect::to($this->base.'/dashboard');
	} */

  }
  
  
  
  
  
public function turnout_ac_wise($request){
  $data                   = [];
  $data['voter_turnouts'] = [];
  $filter = [
    'phase_no' => $this->phase_no,
    'st_code'  => $this->st_code
  ];

  $grand_e_male   = 0;
  $grand_e_female = 0;
  $grand_e_other  = 0;
  $grand_e_total  = 0;
  $grand_male   = 0;
  $grand_female = 0;
  $grand_other  = 0;
  $grand_total  = 0;
  $grand_queue  = 0;

  $acs_results = AcModel::get_acs($filter);

  foreach ($acs_results as $key => $iterate_ac) {
    $filter_for_voters = [
      'phase_no'    => $this->phase_no,
      'st_code' => $iterate_ac['st_code'],
      'ac_no'   => $iterate_ac['ac_no'],
    ];
    $stats_sum = TblPollSummaryModel::total_statics_sum($filter_for_voters);

    $male   = $stats_sum['male_voters'];
    $female = $stats_sum['female_voters'];
    $other  = $stats_sum['other_voters'];
    $total  = $male+$female+$other;
    $queue_voters = $stats_sum['queue_voters'];
	
    if($queue_voters <= 0){
		  $queue_voters = 0;
	  }

    $electoral = VoterInfoModel::get_aggregate_voters($filter_for_voters);

    $e_male   = $electoral['e_male'];
    $e_female = $electoral['e_female'];
    $e_other  = $electoral['e_other'];
    $e_total  = $electoral['e_total'];


    $grand_e_male   += $e_male;
    $grand_e_female += $e_female;
    $grand_e_other  += $e_other;
    $grand_e_total  += $e_total;
    $grand_male   += $male;
    $grand_female += $female;
    $grand_other  += $other;
    $grand_total  += $total;

    
    $grand_queue  += $queue_voters;
    

    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    if($queue_voters<10){
      $voters_queue = "less than 10";
    }else if($queue_voters >= 10 && $queue_voters<20){
      $voters_queue = "10 to 20";
    }else if($queue_voters >= 20 && $queue_voters<30){
      $voters_queue = "20 to 30";
    }else if($queue_voters >= 30){
      $voters_queue = "30+";
    }else{
      $voters_queue = '';
    }


	$st = getstatebystatecode($iterate_ac['st_code']);
//dd($st);

    $data['voter_turnouts'][] = [
	  'st_name'         => $st->ST_NAME,
      'name'            => $iterate_ac['ac_no'].'-'.$iterate_ac['ac_name'],
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => $voters_queue,
      'percentage'      => $percentage
    ];

  }

  $grand_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total > 0){
    $grand_percentage = round($grand_total/$grand_e_total*100,2);
  }

  $data['voter_turnouts'][]    = [
    'st_name' => '',
    'name' => 'Total',
    'male'    => $grand_male,
    'female'  => $grand_female,
    'other'   => $grand_other,
    'total'   => $grand_total,
    'e_male'    => $grand_e_male,
    'e_female'  => $grand_e_female,
    'e_other'   => $grand_e_other,
    'e_total'   => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage' => $grand_percentage
  ];

  $data['grand_percentage']         = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;

	return $data;

}

public function turnout_state_wise($request){
  $data                   = [];
  $data['voter_turnouts'] = [];
  $filter = [
    'st_code'     => $this->st_code,
    'phase_no'    => $this->phase_no
  ];

  $grand_e_male   = 0;
  $grand_e_female = 0;
  $grand_e_other  = 0;
  $grand_e_total  = 0;
  $grand_male   = 0;
  $grand_female = 0;
  $grand_other  = 0;
  $grand_total  = 0;
  $grand_queue  = 0;

  $states_results = StateModel::get_states($filter);
  foreach ($states_results as $key => $iterate_state) {

    $filter_for_voters = [
      'st_code' => $iterate_state->ST_CODE,
      'phase_no'    => $this->phase_no
    ];
    $stats_sum = TblPollSummaryModel::total_statics_sum($filter_for_voters);

    $male   = $stats_sum['male_voters'];
    $female = $stats_sum['female_voters'];
    $other  = $stats_sum['other_voters'];
    $total  = $male+$female+$other;
    $queue_voters = $stats_sum['queue_voters'];
	if($queue_voters <= 0){
		$queue_voters = 0;
	}

    $e_male   = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M']));
    $e_female = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F']));
    $e_other  = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O']));
    $e_total  = $e_male+$e_female+$e_other;


    $grand_e_male   += $e_male;
    $grand_e_female += $e_female;
    $grand_e_other  += $e_other;
    $grand_e_total  += $e_total;
    $grand_male   += $male;
    $grand_female += $female;
    $grand_other  += $other;
    $grand_total  += $total;

    
    $grand_queue  += $queue_voters;
    

    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    if($queue_voters<10){
      $voters_queue = "less than 10";
    }else if($queue_voters >= 10 && $queue_voters<20){
      $voters_queue = "10 to 20";
    }else if($queue_voters >= 20 && $queue_voters<30){
      $voters_queue = "20 to 30";
    }else if($queue_voters >= 30){
      $voters_queue = "30+";
    }else{
      $voters_queue = '';
    }

	
    $data['voter_turnouts'][] = [
      'st_name'         => $iterate_state['ST_NAME'],
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => $voters_queue,
      'percentage'      => $percentage
    ];

  }

  $grand_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total > 0){
    $grand_percentage = round($grand_total/$grand_e_total*100,2);
  }

  $data['voter_turnouts'][]    = [
    'st_name' => 'Total',
    'male'    => $grand_male,
    'female'  => $grand_female,
    'other'   => $grand_other,
    'total'   => $grand_total,
    'e_male'    => $grand_e_male,
    'e_female'  => $grand_e_female,
    'e_other'   => $grand_e_other,
    'e_total'   => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage' => $grand_percentage
  ];

  $data['grand_percentage']         = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;

	return $data;

}

public function turnout_ps_wise(Request $request){
	
  $data                   = [];
  $data['voter_turnouts'] = [];
  $filter = [
    'phase_no'        => $this->phase_no,
    'st_code'         => $this->st_code,
    'ac_no'           => $this->ac_no,
    'ps_no'           => $this->ps_no,
  ];

  $grand_e_male   = 0;
  $grand_e_female = 0;
  $grand_e_other  = 0;
  $grand_e_total  = 0;
  $grand_male   = 0;
  $grand_female = 0;
  $grand_other  = 0;
  $grand_total  = 0;
  $grand_queue  = 0;

  $polling_stations = PollingStation::get_polling_stations($filter);

  foreach ($polling_stations as $key => $iterate_p_s) {
    $ps_no = $iterate_p_s['PS_NO'];
    $filter_for_voters = array_merge($filter,['ps_no' => $iterate_p_s['PS_NO']]);

    $stats_sum = TblPollSummaryModel::total_statics_sum($filter_for_voters);

    $male   = $stats_sum['male_voters'];
    $female = $stats_sum['female_voters'];
    $other  = $stats_sum['other_voters'];
    $total  = $male+$female+$other;
    $queue_voters = $stats_sum['queue_voters'];
	$queue_voters = $stats_sum['queue_voters'];
	if($queue_voters <= 0){
		$queue_voters = 0;
	}


    $e_male   = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'M']));
    $e_female = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'F']));
    $e_other  = VoterInfoModel::get_elector_count(array_merge($filter_for_voters,['gender' => 'O']));
    $e_total  = $e_male + $e_female + $e_other;


    $grand_e_male   += $e_male;
    $grand_e_female += $e_female;
    $grand_e_other  += $e_other;
    $grand_e_total  += $e_total;
    $grand_male   += $male;
    $grand_female += $female;
    $grand_other  += $other;
    $grand_total  += $total;

    $is_ps_poll_end = TblPollSummaryModel::total_statics_count(array_merge($filter_for_voters,['is_end' => true]));
    if($is_ps_poll_end){
      $queue_voters = 'Poll End';
    }else{
      $grand_queue  += $queue_voters;
    }

    $percentage = 0;
    if($e_total >= $total && $e_total > 0){
      $percentage = round($total/$e_total*100,2);
    }

    $poll_station_name = $iterate_p_s['PS_NAME_EN'];

    if($queue_voters<10){
      $voters_queue = "less than 10";
    }else if($queue_voters >= 10 && $queue_voters<20){
      $voters_queue = "10 to 20";
    }else if($queue_voters >= 20 && $queue_voters<30){
      $voters_queue = "20 to 30";
    }else if($queue_voters >= 30){
      $voters_queue = "30+";
    }else{
      $voters_queue = '';
    }


$st = getstatebystatecode($iterate_p_s['ST_CODE']);
	$st_name = $st->ST_NAME;

$ac = getacbyacno($iterate_p_s['ST_CODE'],$iterate_p_s['AC_NO']);
	$ac_name = $ac->AC_NO.'-'.$ac->AC_NAME;
    $data['voter_turnouts'][] = [
      'ps_name'         => $poll_station_name,
	  'st_name'         => $st->ST_NAME,
      'ac_name'         => $ac->AC_NO.'-'.$ac->AC_NAME,
      'ps_no'           => $iterate_p_s['PS_NO'],
      'ps_name_and_no'  => $iterate_p_s['PS_NO'].'-'.$poll_station_name,
      'male'            => $male,
      'female'          => $female,
      'other'           => $other,
      'total'           => $total,
      'e_male'          => $e_male,
      'e_female'        => $e_female,
      'e_other'         => $e_other,
      'e_total'         => $e_total,
      'total_in_queue'  => $voters_queue,
      'percentage'      => $percentage
    ];

  }

  $grand_percentage = 0;
  if($grand_e_total >= $grand_total && $grand_e_total > 0){
    $grand_percentage = round($grand_total/$grand_e_total*100,2);
  }

  $data['voter_turnouts'][]    = [
    'ps_name' => 'Total',
	'st_name'   => '',
    'ac_name'   => '',
    'ps_no'   => '',
    'ps_name_and_no' => 'Total',
    'male'    => $grand_male,
    'female'  => $grand_female,
    'other'   => $grand_other,
    'total'   => $grand_total,
    'e_male'    => $grand_e_male,
    'e_female'  => $grand_e_female,
    'e_other'   => $grand_e_other,
    'e_total'   => $grand_e_total,
    'total_in_queue'  => '',//$grand_queue,
    'percentage' => $grand_percentage
  ];


  $data['st_name'] = $st_name;
  $data['ac_name'] = $ac_name;
  $data['grand_percentage'] = $grand_percentage;
  $data['poll_turnout_percentage']  = $grand_percentage;

  return $data;
}
  
  
	public function get_voter_turnout(Request $request){
	  if($this->ac_no && $this->st_code){
		return $this->turnout_ps_wise($request);
	  }else if($this->st_code){
		return $this->turnout_ac_wise($request);
	  }else{
		return $this->turnout_state_wise($request);
	  }
	}
  
  
  public function poll_turnout_report(Request $request){
	    
  $data                         = [];
  $data['role_id']              = $this->role_id;
  $data['action']               = url($this->action);
  $request_array = [];
  if($this->st_code){
    $request_array[] = "st_code=".$this->st_code;
  }
  if($this->ac_no){
    $request_array[] = "ac_no=".$this->ac_no;
  }

	
//form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/poll-turnout-report");
  $form_filter_array = [
    'st_code'     => true,
	'phase_no'     => true,
    'dist_no'     => false,
    'ac_no'       => true, 
    'ps_no'       => true, 
    'designation'     => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);      
  $data['form_filters'] = $form_filters;


	$data['user_data'] = Auth::user();
		
		if(Auth::user()->role_id == '19'){
			$prefix 	= 'roac';
		}else if(Auth::user()->role_id == '4'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '5'){
			$prefix 	= 'acdeo';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}

		$st_code = $this->st_code;
		$ac_no = $this->ac_no;


	$data['pdf_btn'] = "$prefix/booth-app-revamp/poll-turnout-report-pdf?st_code=$st_code&ac_no=$ac_no";
	$data['excel_btn'] = "$prefix/booth-app-revamp/poll-turnout-report-xls?st_code=$st_code&ac_no=$ac_no";
	
$data['data']               	= $this->get_voter_turnout($request);	


//dd($data);

	
 if($this->ac_no && $this->st_code){
	 
		if($request->path() == "$prefix/booth-app-revamp/poll-turnout-report-pdf"){
			
			$pdf = PDF::loadView($this->view.'.Reports.voter_turnout_ps_wise_pdf',$data);
			
			return $pdf->download('Booth App - PS Wise Poll Turnout Report.pdf');
		}else if($request->path() == "$prefix/booth-app-revamp/poll-turnout-report-xls"){ 	
			return Excel::create('Booth App - PS Wise Poll Turnout Report', function($excel) use ($data) {
				
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
				  
					$sheet->mergeCells('A1:K1');	
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('Booth App - PS Wise Poll Turnout Report');
						$cells->setAlignment('center');
						$cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
				
					$sheet->mergeCells('A2:E2');
					$sheet->mergeCells('F2:K2');

					$sheet->cell('A2', function($cells) use($data) {
						$st_name = $data['data']['st_name'];
						$cells->setValue("State/UT Name: $st_name");
						$cells->setAlignment('center');
						$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
					
					$sheet->cell('F2', function($cells) use($data) {
						$ac_name = $data['data']['ac_name'];
						$cells->setValue("AC NO & AC Name: $ac_name");
						$cells->setAlignment('center');
						$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
					
					$sheet->mergeCells('B3:E3');
					$sheet->mergeCells('F3:I3');
					$sheet->cell('A3', function($cell) {$cell->setValue('PS NO & PS Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('B3', function($cell) {$cell->setValue('Electors'); $cell->setAlignment('center'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('F3', function($cell) {$cell->setValue('Voters'); $cell->setAlignment('center'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('J3', function($cell) {$cell->setValue('Total Voters in Queue'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('K3', function($cell) {$cell->setValue('Poll(%)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					
					$sheet->cell('B4', function($cell) {$cell->setValue('(M)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('C4', function($cell) {$cell->setValue('(F)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('D4', function($cell) {$cell->setValue('(TG)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('E4', function($cell) {$cell->setValue('(Total)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });					
					$sheet->cell('F4', function($cell) {$cell->setValue('(M)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('G4', function($cell) {$cell->setValue('(F)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('H4', function($cell) {$cell->setValue('(TG)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('I4', function($cell) {$cell->setValue('(Total)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					
					$i= 5;
					
					
					 if (!empty($data['data']['voter_turnouts'])) {
						foreach ($data['data']['voter_turnouts'] as $key => $iterate_turnout) { 
							$sheet->cell('A'.$i, $iterate_turnout['ps_name_and_no'] ); 
							$sheet->cell('B'.$i, ($iterate_turnout['e_male'] > 0) ? $iterate_turnout['e_male']:'=(0)' ); 
							$sheet->cell('C'.$i, ($iterate_turnout['e_female'] > 0) ? $iterate_turnout['e_female']:'=(0)' ); 
							$sheet->cell('D'.$i, ($iterate_turnout['e_other'] > 0) ? $iterate_turnout['e_other']:'=(0)' ); 
							$sheet->cell('E'.$i, ($iterate_turnout['e_total'] > 0) ? $iterate_turnout['e_total']:'=(0)' ); 
							$sheet->cell('F'.$i, ($iterate_turnout['male'] > 0) ? $iterate_turnout['male']:'=(0)' ); 
							$sheet->cell('G'.$i, ($iterate_turnout['female'] > 0) ? $iterate_turnout['female']:'=(0)' ); 
							$sheet->cell('H'.$i, ($iterate_turnout['other'] > 0) ? $iterate_turnout['other']:'=(0)' ); 
							$sheet->cell('I'.$i, ($iterate_turnout['total'] > 0) ? $iterate_turnout['total']:'=(0)' ); 
							$sheet->cell('J'.$i, ($iterate_turnout['total_in_queue'] > 0) ? $iterate_turnout['total_in_queue']:'=(0)' ); 
							$sheet->cell('K'.$i, ($iterate_turnout['percentage'] > 0) ? $iterate_turnout['percentage']:'=(0)' ); 
							
							$i++;
						}
					}
		
				});
			})->download('xls');
		}else{
			return view($this->view.'.Reports.voter_turnout_ps_wise', $data);
		}
  }else if($this->st_code){
	  if($request->path() == "$prefix/booth-app-revamp/poll-turnout-report-pdf"){
			
			$pdf = PDF::loadView($this->view.'.Reports.voter_turnout_ac_wise_pdf',$data);
			
			return $pdf->download('Booth App - AC Wise Poll Turnout Report.pdf');
		}else if($request->path() == "$prefix/booth-app-revamp/poll-turnout-report-xls"){
			return Excel::create('Booth App - AC Wise Poll Turnout Report', function($excel) use ($data) {
				
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
				  
					$sheet->mergeCells('A1:M1');	
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('Booth App - AC Wise Poll Turnout Report');
						$cells->setAlignment('center');
						$cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
					
				
					$sheet->mergeCells('C3:F3');
					$sheet->mergeCells('G3:J3');
					$sheet->cell('A3', function($cell) {$cell->setValue('State/UT Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('B3', function($cell) {$cell->setValue('AC NO & AC Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('C3', function($cell) {$cell->setValue('Electors'); $cell->setAlignment('center'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('G3', function($cell) {$cell->setValue('Voters'); $cell->setAlignment('center'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('K3', function($cell) {$cell->setValue('Total Voters in Queue'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('L3', function($cell) {$cell->setValue('Poll(%)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					
					$sheet->cell('C4', function($cell) {$cell->setValue('(M)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('D4', function($cell) {$cell->setValue('(F)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('E4', function($cell) {$cell->setValue('(TG)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('F4', function($cell) {$cell->setValue('(Total)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });					
					$sheet->cell('G4', function($cell) {$cell->setValue('(M)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('H4', function($cell) {$cell->setValue('(F)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('I4', function($cell) {$cell->setValue('(TG)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('J4', function($cell) {$cell->setValue('(Total)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					
					$i= 5;
					
					
					 if (!empty($data['data']['voter_turnouts'])) {
						foreach ($data['data']['voter_turnouts'] as $key => $iterate_turnout) {
							
							 $sheet->cell('A'.$i, $iterate_turnout['st_name']); 
							$sheet->cell('B'.$i, $iterate_turnout['name'] );
							$sheet->cell('C'.$i, ($iterate_turnout['e_male'] > 0) ? $iterate_turnout['e_male']:'=(0)' ); 
							$sheet->cell('D'.$i, ($iterate_turnout['e_female'] > 0) ? $iterate_turnout['e_female']:'=(0)' ); 
							$sheet->cell('E'.$i, ($iterate_turnout['e_other'] > 0) ? $iterate_turnout['e_other']:'=(0)' ); 
							$sheet->cell('F'.$i, ($iterate_turnout['e_total'] > 0) ? $iterate_turnout['e_total']:'=(0)' ); 
							$sheet->cell('G'.$i, ($iterate_turnout['male'] > 0) ? $iterate_turnout['male']:'=(0)' ); 
							$sheet->cell('H'.$i, ($iterate_turnout['female'] > 0) ? $iterate_turnout['female']:'=(0)' ); 
							$sheet->cell('I'.$i, ($iterate_turnout['other'] > 0) ? $iterate_turnout['other']:'=(0)' ); 
							$sheet->cell('J'.$i, ($iterate_turnout['total'] > 0) ? $iterate_turnout['total']:'=(0)' ); 
							$sheet->cell('K'.$i, ($iterate_turnout['total_in_queue'] > 0) ? $iterate_turnout['total_in_queue']:'=(0)' ); 
							$sheet->cell('L'.$i, ($iterate_turnout['percentage'] > 0) ? $iterate_turnout['percentage']:'=(0)' ); 
							
							$i++;
						}
					}
		
				});
			})->download('xls');
		}else{
	  
			return view($this->view.'.Reports.voter_turnout_ac_wise', $data);
	  }
  }else{
	  if($request->path() == "$prefix/booth-app-revamp/poll-turnout-report-pdf"){
			
			$pdf = PDF::loadView($this->view.'.Reports.voter_turnout_state_wise_pdf',$data);
			
			return $pdf->download('Booth App - State Wise Poll Turnout Report.pdf');
		}else if($request->path() == "$prefix/booth-app-revamp/poll-turnout-report-xls"){
			return Excel::create('Booth App - State Wise Poll Turnout Report', function($excel) use ($data) {
				
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
				  
					$sheet->mergeCells('A1:M1');	
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('Booth App - State Wise Poll Turnout Report');
						$cells->setAlignment('center');
						$cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
					
				
					$sheet->mergeCells('B3:E3');
					$sheet->mergeCells('F3:I3');
					$sheet->cell('A3', function($cell) {$cell->setValue('State/UT Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('B3', function($cell) {$cell->setValue('Electors'); $cell->setAlignment('center'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('F3', function($cell) {$cell->setValue('Voters'); $cell->setAlignment('center'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('J3', function($cell) {$cell->setValue('Total Voters in Queue'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('K3', function($cell) {$cell->setValue('Poll(%)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					
					$sheet->cell('B4', function($cell) {$cell->setValue('(M)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('C4', function($cell) {$cell->setValue('(F)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('D4', function($cell) {$cell->setValue('(TG)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('E4', function($cell) {$cell->setValue('(Total)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });					
					$sheet->cell('F4', function($cell) {$cell->setValue('(M)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('G4', function($cell) {$cell->setValue('(F)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('H4', function($cell) {$cell->setValue('(TG)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('I4', function($cell) {$cell->setValue('(Total)'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					
					$i= 5;
					
					
					 if (!empty($data['data']['voter_turnouts'])) {
						foreach ($data['data']['voter_turnouts'] as $key => $iterate_turnout) {
							
							$sheet->cell('A'.$i, $iterate_turnout['st_name']);
							$sheet->cell('B'.$i, ($iterate_turnout['e_male'] > 0) ? $iterate_turnout['e_male']:'=(0)' ); 
							$sheet->cell('C'.$i, ($iterate_turnout['e_female'] > 0) ? $iterate_turnout['e_female']:'=(0)' ); 
							$sheet->cell('D'.$i, ($iterate_turnout['e_other'] > 0) ? $iterate_turnout['e_other']:'=(0)' ); 
							$sheet->cell('E'.$i, ($iterate_turnout['e_total'] > 0) ? $iterate_turnout['e_total']:'=(0)' ); 
							$sheet->cell('F'.$i, ($iterate_turnout['male'] > 0) ? $iterate_turnout['male']:'=(0)' ); 
							$sheet->cell('G'.$i, ($iterate_turnout['female'] > 0) ? $iterate_turnout['female']:'=(0)' ); 
							$sheet->cell('H'.$i, ($iterate_turnout['other'] > 0) ? $iterate_turnout['other']:'=(0)' ); 
							$sheet->cell('I'.$i, ($iterate_turnout['total'] > 0) ? $iterate_turnout['total']:'=(0)' ); 
							$sheet->cell('J'.$i, ($iterate_turnout['total_in_queue'] > 0) ? $iterate_turnout['total_in_queue']:'=(0)' ); 
							$sheet->cell('K'.$i, ($iterate_turnout['percentage'] > 0) ? $iterate_turnout['percentage']:'=(0)' ); 
							
							$i++;
						}
					}
		
				});
			})->download('xls');
		}else{	  
			return view($this->view.'.Reports.voter_turnout_state_wise', $data);
		}
  }
	
}

 public function poll_event_report($phase_no = 0,Request $request){

  $data                         = [];
  $data['role_id']              = $this->role_id;
  $data['action']               = url($this->action);
  $request_array = [];
  $is_activated           = NULL;
  if($request->has('event_type')){
	$is_activated = $request->event_type;
  }
  
  
  if($this->phase_no){
    $request_array[] = "phase_no=".$this->phase_no;
  }
  
  if($this->st_code){
    $request_array[] = "st_code=".$this->st_code;
  }
  if($this->ac_no){
    $request_array[] = "ac_no=".$this->ac_no;
  }
  
	$filter = [
			'phase_no'        => $this->phase_no,
			'st_code'         => $this->st_code,
			'ac_no'           => $this->ac_no
		  ];


//form filters
  if(Auth::user()->role_id == '5'){
	$booth_acs = DB::table('boothapp_enable_acs')->select(DB::raw('group_concat(ac_no) as ac_no'))->where('dist_no',Auth::user()->dist_no)->first();
  }else{
	$booth_acs = DB::table('boothapp_enable_acs')->select(DB::raw('group_concat(ac_no) as ac_no'))->first();  
  }
  $allowed_acs = $booth_acs->ac_no;
	
  $data['filter_action'] = Common::generate_url("booth-app-revamp/poll-event-report");
  $form_filter_array = [
    'phase_no'     => true,
    'st_code'     => true,
    'dist_no'     => false,
    'ac_no'       => true, 
    'ps_no'       => false, 
    'designation'     => false,
    'allowed_acs'     => $allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  
 
  
  $form_filters = Common::get_form_filters($form_filter_array, $request);  


  
  $data['form_filters'] = $form_filters;

 //activate filter
 /*  $is_activated_value   = [];
  $is_activated_value[] = [
    'event_type'  => 'Poll Material Received',
    'type_filter'    => '8',
  ];
  $is_activated_value[] = [
    'event_type'  => 'Poll Party Reached',
    'type_filter'    => '1',
  ]; */
  $is_activated_value[] = [
    'event_type'  => 'Mock Poll Done',
    'type_filter'    => '2',
  ];
  $is_activated_value[] = [
    'event_type'  => 'Poll Started',
    'type_filter'    => '3',
  ];
  $is_activated_value[] = [
    'event_type'  => 'Voting Started',
    'type_filter'    => '4',
  ];
  /* $is_activated_value[] = [
    'event_type'  => 'Final Data Sync',
    'type_filter'    => '5',
  ]; */
  $is_activated_value[] = [
    'event_type'  => 'Poll End',
    'type_filter'    => '6',
  ];
 /*  $is_activated_value[] = [
    'event_type'  => 'PRO Diary Submitted',
    'type_filter'    => '9',
  ];
  $is_activated_value[] = [
    'event_type'  => 'Poll Material Submitted',
    'type_filter'    => '7',
  ]; */
  
  
  $is_vote_array = [];
  foreach ($is_activated_value as $iterate_activate) {
    $is_active = false;
    if($is_activated == $iterate_activate['type_filter']){
      $is_active = true;
    }
    $is_vote_array[] = [
      'id'    => $iterate_activate['type_filter'],
      'name'      => $iterate_activate['event_type'],
      'active'  => $is_active
    ];
  }
  
  $form_filters[] = [
    'id'      => 'event_type',
    'name'    => 'Event type',
    'results' => $is_vote_array
  ];
  
  $data['event_filter'] = !empty($request->event_type)?$request->event_type:0;
  
  
  //dd($form_filters);
  $data['form_filters'] = $form_filters;
	$data['user_data'] = Auth::user();
		
		if(Auth::user()->role_id == '19'){
			$prefix 	= 'roac';
		}else if(Auth::user()->role_id == '4'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '5'){
			$prefix 	= 'acdeo';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}

		$st_code = $this->st_code;
		$ac_no = $this->ac_no;

	$data['prefix'] = $prefix;
	
	//dd($allowed_acs);
	$allowed_st_code = implode(', ', $this->allowed_st_code);

	$sql = "SELECT tad.st_code, tad.ac_no,tad.`ps_no`,st_name,AC_NAME_EN AS ac_name,COUNT(IF(poll_started,1,NULL)) AS poll_start, COUNT(IF(poll_ended,1,NULL)) AS poll_end, COUNT(IF(voting,1,NULL)) AS total_voter, 
	(SELECT COUNT(ps_no) FROM polling_station WHERE ST_CODE = tad.st_code AND AC_NO = tad.`ac_no` AND booth_app_excp = 0) AS total_ps,
	COUNT(IF(mockpoll,1,NULL)) AS mock_poll_start, COUNT(IF(final_sync,1,NULL)) AS data_sync,
	COUNT(IF(poll_party_reach,1,NULL)) AS ps_location,COUNT(IF(poll_party_reach,1,NULL)) AS ps_location,
	COUNT(IF(pm_submitted,1,NULL)) AS total_submited,COUNT(IF(pm_received ,1,NULL)) AS total_received,
	COUNT(IF(pro_diary,1,NULL)) AS pro_diary_sub
	FROM tbl_analytics_dashboard AS tad 
	INNER JOIN state_master ON (state_master.ST_CODE = tad.st_code)
	INNER JOIN ac_master ON (ac_master.ST_CODE = tad.st_code  AND ac_master.AC_NO = tad.ac_no)
	inner join m_election_details ph ON (tad.st_code=ph.st_code and tad.ac_no=ph.CONST_NO)
	WHERE ph.CONST_TYPE='AC' ";


	if(!empty($filter['phase_no'])){		
			$phase_no = $filter['phase_no'];
			$sql .= " and ph.phase_no = '$phase_no'";
		}
	if(Auth::user()->role_id == '5'){
		$dist_no = Auth::user()->dist_no;
		$sql .= " and ac_master.DIST_NO_HDQTR = '$dist_no'";
	}


	if(!empty($filter['st_code'])){		
		$st_code = $filter['st_code'];
		$sql .= " and tad.st_code = '$st_code'";
    }

    if(!empty($filter['ac_no'])){
		$ac_no = $filter['ac_no'];
		$sql .= " and ac_master.ac_no = '$ac_no'";
    }
	
	if(!empty($filter['ps_no'])){
		$ps_no = $filter['ps_no'];
		$sql .= " and tad.ps_no = '$ps_no'";
    }

	$sql .= "group by tad.st_code, tad.ac_no order by tad.st_code, tad.ac_no ASC";	
			
			
	$myObj   = DB::connection('booth_revamp_test')->select($sql);
	
	
	$data['results'] = json_decode(json_encode($myObj), true);
	

	$data['pdf_btn'] = $prefix.'/booth-app-revamp/poll-event-report?st_code='.$this->st_code.'&ac_no='.$this->ac_no.'&phase_no='.$this->phase_no.'&event_type='.$is_activated.'&pdf=yes';
	
	$data['excel_btn'] = $prefix.'/booth-app-revamp/poll-event-report?st_code='.$this->st_code.'&ac_no='.$this->ac_no.'&phase_no='.$this->phase_no.'&event_type='.$is_activated.'&xls=yes';

	
	if($request->pdf == "yes"){
			
		$pdf = PDF::loadView($this->view.'.Reports.poll-event-report-pdf',$data);
			
			return $pdf->download('Booth App - Poll Event Report.pdf');
			
		}else if($request->xls == "yes"){
			
			
			return Excel::download(new PollEventReportExcel($data), 'Booth App - Poll Event Report.xlsx');
			
			
			// return Excel::create('Booth App - Poll Event Report', function($excel) use ($data,$request) {
				
			// 	$excel->sheet('mySheet', function($sheet) use ($data,$request)
			// 	{
				  
			// 		$sheet->mergeCells('A1:F1');	
					
			// 		$sheet->cell('A1', function($cells) {
			// 			$cells->setValue('Booth App - Poll Event Report');
			// 			$cells->setAlignment('center');
			// 			$cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
			// 		});
					
			// 		$sheet->mergeCells('D3:E3');
			// 		$sheet->mergeCells('F3:G3');
			// 		$sheet->mergeCells('H3:I3');
			// 		$sheet->mergeCells('J3:K3');
			// 		$sheet->mergeCells('L3:M3');
			// 		$sheet->mergeCells('N3:O3');
			// 		$sheet->mergeCells('P3:Q3');
			// 		$sheet->mergeCells('R3:S3');
			// 		$sheet->mergeCells('T3:U3');
					
			// 		$sheet->cell('A3', function($cell) {$cell->setValue('State/UT Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 		$sheet->cell('B3', function($cell) {$cell->setValue('AC NO & AC Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 		$sheet->cell('C3', function($cell) {$cell->setValue('Total PS'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 		if(empty($request->event_type)){
			// 			/* $sheet->cell('D3', function($cell) {$cell->setValue('Poll Material Received'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('F3', function($cell) {$cell->setValue('Poll Party Reached'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); }); */
						
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Mock Poll Done'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
						
			// 			$sheet->cell('F3', function($cell) {$cell->setValue('Poll Started'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
						
			// 			$sheet->cell('H3', function($cell) {$cell->setValue('Voting Started'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
						
			// 			/* $sheet->cell('J3', function($cell) {$cell->setValue('Final Data Sync'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); }); */
						
			// 			$sheet->cell('J3', function($cell) {$cell->setValue('Poll End'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
						
			// 			/* $sheet->cell('R3', function($cell) {$cell->setValue('PRO Diary Submitted'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('T3', function($cell) {$cell->setValue('Poll Material Submitted'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); }); */
						
			// 		}
			// 		/* else if($request->event_type<>'' && $request->event_type==8){
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Poll Material Received'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 		}
			// 		else if($request->event_type<>'' && $request->event_type==1){
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Poll Party Reached'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 		} */
			// 		else if($request->event_type<>'' && $request->event_type==2){
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Mock Poll Done'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 		}
			// 		else if($request->event_type<>'' && $request->event_type==3){
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Poll Started'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 		}else if($request->event_type<>'' && $request->event_type==4){
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Voting Started'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 		}
			// 		/* else if($request->event_type<>'' && $request->event_type==5){
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Final Data Sync'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 		} */
			// 		else if($request->event_type<>'' && $request->event_type==6){
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Poll End'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 		}
			// 		/* else if($request->event_type<>'' && $request->event_type==9){
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('PRO Diary Submitted'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 		}
			// 		else if($request->event_type<>'' && $request->event_type==7){
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Poll Material Submitted'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 		} */
					
			// 		if(empty($request->event_type)){
			// 			/* $sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 			$sheet->cell('F4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('G4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); }); */
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('F4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('G4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('H4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('I4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });	
						
			// 			/* $sheet->cell('N4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('O4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); }); */
						
			// 			$sheet->cell('J4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('K4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
						
			// 			/* $sheet->cell('R4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('S4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('T4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('U4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); }); */
						
			// 		}
			// 		/* else if($request->event_type<>'' && $request->event_type==8){
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 		}
			// 		else if($request->event_type<>'' && $request->event_type==1){
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 		} */
			// 		else if($request->event_type<>'' && $request->event_type==2){
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 		}else if($request->event_type<>'' && $request->event_type==3){
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 		}else if($request->event_type<>'' && $request->event_type==4){
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 		}
					
			// 		/* else if($request->event_type<>'' && $request->event_type==5){
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 		} */
					
			// 		else if($request->event_type<>'' && $request->event_type==6){
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 		}
			// 		/* else if($request->event_type<>'' && $request->event_type==9){
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 		}
			// 		else if($request->event_type<>'' && $request->event_type==7){
			// 			$sheet->cell('D4', function($cell) {$cell->setValue('Yes'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12)); $cell->setAlignment('center'); });
			// 			$sheet->cell('E4', function($cell) {$cell->setValue('No'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  $cell->setAlignment('center');});
			// 		} */
					
					
			// 		 if (!empty($data)) {
			// 			foreach ($data['results'] as $key => $result) {
			// 				$i= $key+5;
			// 				$sheet->cell('A'.$i, $result['st_name']); 
			// 				$sheet->cell('B'.$i, $result['ac_no'].'-'.$result['ac_name'] ); 
			// 				$sheet->cell('C'.$i,  ($result['total_ps'] > 0) ? $result['total_ps']:'=(0)');
			// 				if(empty($request->event_type)){
			// 					/* $sheet->cell('D'.$i,  ($result['total_received'] > 0) ? $result['total_received']:'=(0)');
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['total_received']) > 0) ? ($result['total_ps']-$result['total_received']):'=(0)' );
			// 					$sheet->cell('F'.$i,  ($result['ps_location'] > 0) ? $result['ps_location']:'=(0)'); 
			// 					$sheet->cell('G'.$i,  (($result['total_ps']-$result['ps_location']) > 0) ? ($result['total_ps']-$result['ps_location']):'=(0)');  */
								
			// 					$sheet->cell('D'.$i,  ($result['mock_poll_start'] > 0) ? $result['mock_poll_start']:'=(0)'); 
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['mock_poll_start']) > 0) ? ($result['total_ps']-$result['mock_poll_start']):'=(0)' ); 
			// 					$sheet->cell('F'.$i,  ($result['poll_start'] > 0) ? $result['poll_start']:'=(0)'); 
			// 					$sheet->cell('G'.$i,  (($result['total_ps']-$result['poll_start']) > 0) ? ($result['total_ps']-$result['poll_start']):'=(0)' ); 
			// 					$sheet->cell('H'.$i,  ($result['total_voter'] > 0) ? $result['total_voter']:'=(0)');
			// 					$sheet->cell('I'.$i,  (($result['total_ps']-$result['total_voter']) > 0) ? ($result['total_ps']-$result['total_voter']):'=(0)' );
								
			// 					/* $sheet->cell('N'.$i,  ($result['data_sync'] > 0) ? $result['data_sync']:'=(0)');
			// 					$sheet->cell('O'.$i,  (($result['total_ps']-$result['data_sync']) > 0) ? ($result['total_ps']-$result['data_sync']):'=(0)' ); */
								
			// 					$sheet->cell('J'.$i,  ($result['poll_end'] > 0) ? $result['poll_end']:'=(0)');
			// 					$sheet->cell('K'.$i,  (($result['total_ps']-$result['poll_end']) > 0) ? ($result['total_ps']-$result['poll_end']):'=(0)' );
								
			// 					/* $sheet->cell('R'.$i,  ($result['pro_diary_sub'] > 0) ? $result['pro_diary_sub']:'=(0)');
			// 					$sheet->cell('S'.$i,  (($result['total_ps']-$result['pro_diary_sub']) > 0) ? ($result['total_ps']-$result['pro_diary_sub']):'=(0)' );
			// 					$sheet->cell('T'.$i,  ($result['total_submited'] > 0) ? $result['total_submited']:'=(0)');
			// 					$sheet->cell('U'.$i,  (($result['total_ps']-$result['total_submited']) > 0) ? ($result['total_ps']-$result['total_submited']):'=(0)' ); */
			// 				}
			// 				/* else if($request->event_type<>'' && $request->event_type==8){
			// 					$sheet->cell('D'.$i,  ($result['total_received'] > 0) ? $result['total_received']:'=(0)');
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['total_received']) > 0) ? ($result['total_ps']-$result['total_received']):'=(0)' );	
			// 				}
			// 				else if($request->event_type<>'' && $request->event_type==1){
			// 					$sheet->cell('D'.$i,  ($result['ps_location'] > 0) ? $result['ps_location']:'=(0)'); 
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['ps_location']) > 0) ? ($result['total_ps']-$result['ps_location']):'=(0)');
			// 				} */
			// 				else if($request->event_type<>'' && $request->event_type==2){
			// 					$sheet->cell('D'.$i,  ($result['mock_poll_start'] > 0) ? $result['mock_poll_start']:'=(0)'); 
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['mock_poll_start']) > 0) ? ($result['total_ps']-$result['mock_poll_start']):'=(0)' ); 
			// 				}
			// 				else if($request->event_type<>'' && $request->event_type==3){
			// 					$sheet->cell('D'.$i,  ($result['poll_start'] > 0) ? $result['poll_start']:'=(0)'); 
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['poll_start']) > 0) ? ($result['total_ps']-$result['poll_start']):'=(0)' ); 
			// 				}else if($request->event_type<>'' && $request->event_type==4){
			// 					$sheet->cell('D'.$i,  ($result['total_voter'] > 0) ? $result['total_voter']:'=(0)');
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['total_voter']) > 0) ? ($result['total_ps']-$result['total_voter']):'=(0)' );
			// 				}
			// 				/* else if($request->event_type<>'' && $request->event_type==5){
			// 					$sheet->cell('D'.$i,  ($result['data_sync'] > 0) ? $result['data_sync']:'=(0)');
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['data_sync']) > 0) ? ($result['total_ps']-$result['data_sync']):'=(0)' );
			// 				} */
			// 				else if($request->event_type<>'' && $request->event_type==6){
			// 					$sheet->cell('D'.$i,  ($result['poll_end'] > 0) ? $result['poll_end']:'=(0)');
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['poll_end']) > 0) ? ($result['total_ps']-$result['poll_end']):'=(0)' );
			// 				}
							
			// 				/* else if($request->event_type<>'' && $request->event_type==9){
			// 					$sheet->cell('D'.$i,  ($result['pro_diary_sub'] > 0) ? $result['pro_diary_sub']:'=(0)');
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['pro_diary_sub']) > 0) ? ($result['total_ps']-$result['pro_diary_sub']):'=(0)' );
			// 				}
			// 				else if($request->event_type<>'' && $request->event_type==7){
			// 					$sheet->cell('D'.$i,  ($result['total_submited'] > 0) ? $result['total_submited']:'=(0)');
			// 					$sheet->cell('E'.$i,  (($result['total_ps']-$result['total_submited']) > 0) ? ($result['total_ps']-$result['total_submited']):'=(0)' );
			// 				} */
			// 			}
			// 		}
		
			// 	});
			// })->download('xls');
		}else{
			return view($this->view.'.Reports.poll-event-report', $data);
		}
	
 }
  
 
  public function poll_event_ps_wise_report(Request $request){
	  
	
	if($this->st_code && $this->ac_no){
		
	}else{
		return Redirect::back()->withErrors(['flash-message', 'Please Select State and AC']);
	}	
		
  $data                         = [];
  $data['role_id']              = $this->role_id;
  $data['action']               = url($this->action);
  $request_array = [];
  if($this->st_code){
    $request_array[] = "st_code=".$this->st_code;
  }
  
  
  if($this->ac_no){
    $request_array[] = "ac_no=".$this->ac_no;
  }
  
  $is_activated           = NULL;
  if($request->has('event_type')){
	$is_activated = $request->event_type;
  }

	$filter = [
			'st_code'         => $this->st_code,
			'ac_no'           => $this->ac_no,
			'ps_no'           => $this->ps_no
		  ];

//form filters
  if(Auth::user()->role_id == '5'){
	$booth_acs = DB::table('boothapp_enable_acs')->select(DB::raw('group_concat(ac_no) as ac_no'))->where('dist_no',Auth::user()->dist_no)->first();
  }else{
	$booth_acs = DB::table('boothapp_enable_acs')->select(DB::raw('group_concat(ac_no) as ac_no'))->first();  
  }
  $allowed_acs = $booth_acs->ac_no;
  $data['filter_action'] = Common::generate_url("booth-app-revamp/poll-event-ps-wise-report");
  $form_filter_array = [
    'phase_no'     => true,
    'st_code'     => true,
    'dist_no'     => false,
    'ac_no'       => true, 
    'ps_no'       => true, 
    'designation'     => false,
    'allowed_acs'     => $allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  
  
  
  $form_filters = Common::get_form_filters($form_filter_array, $request);      
  
  //activate filter
  $is_activated_value   = [];
  
  $is_activated_value[] = [
    'event_type'  => 'Mock Poll Done',
    'type_filter'    => '2',
  ];
  $is_activated_value[] = [
    'event_type'  => 'Poll Started',
    'type_filter'    => '3',
  ];
  $is_activated_value[] = [
    'event_type'  => 'Voting Started',
    'type_filter'    => '4',
  ];
  
  $is_activated_value[] = [
    'event_type'  => 'Poll End',
    'type_filter'    => '6',
  ];
  
  
  $is_vote_array = [];
  foreach ($is_activated_value as $iterate_activate) {
    $is_active = false;
    if($is_activated == $iterate_activate['type_filter']){
      $is_active = true;
    }
    $is_vote_array[] = [
      'id'    => $iterate_activate['type_filter'],
      'name'      => $iterate_activate['event_type'],
      'active'  => $is_active
    ];
  }
  
  $form_filters[] = [
    'id'      => 'event_type',
    'name'    => 'Event type',
    'results' => $is_vote_array
  ];
  $data['form_filters'] = $form_filters;
  $data['event_filter'] = !empty($request->event_type)?$request->event_type:0;

	$data['user_data'] = Auth::user();
		
		if(Auth::user()->role_id == '19'){
			$prefix 	= 'roac';
		}else if(Auth::user()->role_id == '4'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '5'){
			$prefix 	= 'acdeo';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}

		$st_code = $this->st_code;
		$ac_no = $this->ac_no;

		$ps_no = $this->ps_no; 


		$st = getstatebystatecode($st_code);
		$st_name = $st->ST_NAME;
	
		$ac = getacbyacno($st_code,$ac_no);
		$ac_name = $ac->AC_NO.'-'.$ac->AC_NAME;

	$data['st_name'] = $st_name;
	$data['ac_name'] = $ac_name;
	
	$data['pdf_btn'] = $prefix.'/booth-app-revamp/poll-event-ps-wise-report?st_code='.$this->st_code.'&ac_no='.$this->ac_no.'&phase_no='.$this->phase_no.'&event_type='.$is_activated.'&pdf=yes';
	
	$data['excel_btn'] = $prefix.'/booth-app-revamp/poll-event-ps-wise-report?st_code='.$this->st_code.'&ac_no='.$this->ac_no.'&phase_no='.$this->phase_no.'&event_type='.$is_activated.'&xls=yes';
	
	$allowed_st_code = implode(', ', $this->allowed_st_code);

	/* $sql = "select ps.st_code, st_name, ps.ac_no, AC_NAME_EN as ac_name,ps.ps_no, PS_NAME_EN as ps_name, tps.poll_start_datetime as poll_start, tps.poll_end_datetime as poll_end, tps.created_at as total_voter_date,tps.pro_turn_out as total_voter,tmps.start_date_time as mock_poll_start,tss.updated_at as data_sync,tim.indatetime_infra as ps_location,COUNT(IF(poll_material_sub = 'Y',1,NULL)) AS total_submited, COUNT(IF(poll_material_rec = 'Y',1,NULL)) AS total_received,pm_submitted_date as mat_sub_date,pm_received_date as mat_rec_date,COUNT(pdf.id) as pro_diary_sub,pdf.created_at as pro_diary_subdate
   from polling_station as ps
   inner join ac_master on (ac_master.ST_CODE = ps.ST_CODE and ac_master.AC_NO = ps.AC_NO)
   inner join state_master on (state_master.ST_CODE = ps.ST_CODE)
   inner join m_election_details ph ON (ps.st_code=ph.st_code and ps.ac_no=ph.CONST_NO)
   left join pro_diary_final pdf ON (ps.st_code=pdf.st_code and ps.ac_no=pdf.ac_no AND pdf.PS_NO = ps.PS_NO AND pdf.row_status = 'A') 
   left join (select * from tbl_poll_summary group by st_code,ac_no,ps_no) as tps on (tps.ST_CODE = ps.ST_CODE and tps.AC_NO = ps.AC_NO and tps.PS_NO = ps.PS_NO)
   left join  tbl_scan_statistics as tss on (tss.ST_CODE = ps.ST_CODE and tss.AC_NO = ps.AC_NO and tss.PS_NO = ps.PS_NO)
   
   
   left join (select ST_CODE,AC_NO,PS_NO,start_date_time from tbl_mock_poll_status  group by 1,2,3) as tmps on (tmps.ST_CODE = ps.ST_CODE and tmps.AC_NO =  ps.AC_NO and tmps.PS_NO = ps.PS_NO)
   left join (select ST_CODE,AC_NO,PS_NO,indatetime_infra from tbl_infra_mapping group by 1,2,3) as tim on (tim.ST_CODE = ps.ST_CODE and tim.AC_NO = ps.AC_NO and tim.PS_NO = ps.PS_NO)
   where ph.CONST_TYPE = 'AC' AND (ps.ST_CODE = '$allowed_st_code' and ps.AC_NO IN ($allowed_acs))"; */
   
   
   $sql = "SELECT tad.st_code, tad.ac_no,tad.`ps_no`,st_name,AC_NAME_EN AS ac_name,poll_started AS poll_start, 
poll_ended AS poll_end, COUNT(IF(voting,1,NULL)) AS total_voter, 
(SELECT PS_NAME_EN FROM polling_station WHERE ST_CODE = tad.st_code AND AC_NO = tad.`ac_no` AND ps_no = tad.ps_no) AS ps_name,
(SELECT COUNT(ps_no) FROM polling_station WHERE ST_CODE = tad.st_code AND AC_NO = tad.`ac_no` AND booth_app_excp = 0) AS total_ps,
mockpoll AS mock_poll_start, final_sync AS data_sync,voting AS total_voter_date,
COUNT(IF(poll_party_reach,1,NULL)) AS ps_location,COUNT(IF(poll_party_reach,1,NULL)) AS ps_location,
COUNT(IF(pm_submitted,1,NULL)) AS total_submited,COUNT(IF(pm_received ,1,NULL)) AS total_received,pm_received AS mat_rec_date,
pm_submitted AS mat_sub_date,
COUNT(IF(pro_diary,1,NULL)) AS pro_diary_sub, pro_diary AS pro_diary_subdate
FROM tbl_analytics_dashboard AS tad 
INNER JOIN state_master ON (state_master.ST_CODE = tad.st_code)
INNER JOIN ac_master ON (ac_master.ST_CODE = tad.st_code  AND ac_master.AC_NO = tad.ac_no)
 inner join m_election_details ph ON (tad.st_code=ph.st_code and tad.ac_no=ph.CONST_NO)
 WHERE ph.CONST_TYPE='AC' ";

	if(!empty($filter['phase_no'])){		
			$phase_no = $filter['phase_no'];
			$sql .= " and ph.phase_no = '$phase_no'";
		}
		
	if(Auth::user()->role_id == '5'){
		$dist_no = Auth::user()->dist_no;
		$sql .= " and ac_master.DIST_NO_HDQTR = '$dist_no'";
	}

	if(!empty($filter['st_code'])){		
		$st_code = $filter['st_code'];
		$sql .= " and tad.st_code = '$st_code'";
    }

    if(!empty($filter['ac_no'])){
		$ac_no = $filter['ac_no'];
		$sql .= " and ac_master.ac_no = '$ac_no'";
    }

	if(!empty($filter['ps_no'])){
		$ps_no = $filter['ps_no'];
		$sql .= " and tad.ps_no = '$ps_no'";
    }

	$sql .= " group by tad.st_code, tad.ac_no, tad.ps_no order by tad.st_code, tad.ac_no,tad.ps_no+0 ASC";

	
			
	$myObj   = DB::connection('booth_revamp_test')->select($sql);
	//dd($myObj);
	
	$data['results'] = json_decode(json_encode($myObj), true);
	
	

	if($request->pdf == "yes"){
			
		$pdf = PDF::loadView($this->view.'.Reports.poll-event-ps-wise-report-pdf',$data);
			
			return $pdf->download('Booth App - Poll Event PS Wise Report.pdf');
			
		}else if($request->xls == "yes"){
			// dd($_SERVER['HTTP_REFERER']);
			
			$phase_no=NULL;
			$st_code=NULL;
			$ps_no=NULL;
			$ac_no=NULL;
			$ac_no=NULL;
			$event_type=NULL;

			$str = $_SERVER['HTTP_REFERER'];
				$qs = parse_url($str, PHP_URL_QUERY);
				if(!empty($qs)){
					parse_str($qs, $output);
					// TODO check for key existence
					if(array_key_exists('phase_no',$output))
					{
						$phase_no=$output['phase_no']; 
					}
					if(array_key_exists('st_code',$output))
					{
						$st_code=$output['st_code']; 
					}
					if(array_key_exists('ps_no',$output))
					{
						$ps_no=$output['ps_no']; 
					}
					if(array_key_exists('ac_no',$output))
					{
						$ac_no=$output['ac_no']; 
					}
					if(array_key_exists('event_type',$output))
					{
						$event_type=$output['event_type']; 
					}
					 
					 
				}
				// echo $event_type;die;

				$sql = "SELECT tad.st_code, tad.ac_no,tad.`ps_no`,st_name,AC_NAME_EN AS ac_name,poll_started AS poll_start, 
				poll_ended AS poll_end, COUNT(IF(voting,1,NULL)) AS total_voter, 
				(SELECT PS_NAME_EN FROM polling_station WHERE ST_CODE = tad.st_code AND AC_NO = tad.`ac_no` AND ps_no = tad.ps_no) AS ps_name,
				(SELECT COUNT(ps_no) FROM polling_station WHERE ST_CODE = tad.st_code AND AC_NO = tad.`ac_no` AND booth_app_excp = 0) AS total_ps,
				mockpoll AS mock_poll_start, final_sync AS data_sync,voting AS total_voter_date,
				COUNT(IF(poll_party_reach,1,NULL)) AS ps_location,COUNT(IF(poll_party_reach,1,NULL)) AS ps_location,
				COUNT(IF(pm_submitted,1,NULL)) AS total_submited,COUNT(IF(pm_received ,1,NULL)) AS total_received,pm_received AS mat_rec_date,
				pm_submitted AS mat_sub_date,
				COUNT(IF(pro_diary,1,NULL)) AS pro_diary_sub, pro_diary AS pro_diary_subdate
				FROM tbl_analytics_dashboard AS tad 
				INNER JOIN state_master ON (state_master.ST_CODE = tad.st_code)
				INNER JOIN ac_master ON (ac_master.ST_CODE = tad.st_code  AND ac_master.AC_NO = tad.ac_no)
				 inner join m_election_details ph ON (tad.st_code=ph.st_code and tad.ac_no=ph.CONST_NO)
				 WHERE ph.CONST_TYPE='AC' ";
				
						if($phase_no!=NULL)
						{	
							
							$sql .= " and ph.phase_no = '$phase_no'";
						}
						
					if(Auth::user()->role_id == '5'){
						$dist_no = Auth::user()->dist_no;
						$sql .= " and ac_master.DIST_NO_HDQTR = '$dist_no'";
					}
				
					if($st_code!=NULL)
				    {	
						
						$sql .= " and tad.st_code = '$st_code'";
					}
				
					if($ac_no!=NULL)
				    {
						
						$sql .= " and ac_master.ac_no = '$ac_no'";
					}
				
					if($ps_no!=NULL)
				    {
						
						$sql .= " and tad.ps_no = '$ps_no'";
					}
				
					$sql .= " group by tad.st_code, tad.ac_no, tad.ps_no order by tad.st_code, tad.ac_no,tad.ps_no+0 ASC";
				
					
							
					$myObj   = DB::connection('booth_revamp_test')->select($sql);
					//dd($myObj);
					
					$data['results'] = json_decode(json_encode($myObj), true);

				

			$headings[]=[];
			if($event_type!=NULL && $event_type==0)
				{
					$export_data[]=['Sn No','AC No & AC Name','PS No & PS Name','Mock Poll Done','Poll Started','Voting Started','Poll End'];

				}
				elseif($event_type!=NULL && $event_type==2)
				{
					$export_data[]=['Sn No','AC No & AC Name','PS No & PS Name','Mock Poll Done'];

				}
				elseif($event_type!=NULL && $event_type==3)
				{
					$export_data[]=['Sn No','AC No & AC Name','PS No & PS Name','Poll Started'];

				}
				elseif($event_type!=NULL && $event_type==4)
				{
					$export_data[]=['Sn No','AC No & AC Name','PS No & PS Name','Voting Started'];

				}
				elseif($event_type!=NULL && $event_type==6)
				{
					$export_data[]=['Sn No','AC No & AC Name','PS No & PS Name','Poll End'];

				}
				else{
					{
						$export_data[]=['Sn No','AC No & AC Name','PS No & PS Name','Mock Poll Done','Poll Started','Voting Started','Poll End'];
	
					}
				}
			$i=1;
			foreach ($data['results'] as $result) {
				$i++;
				$mock_poll_start=date('d-m-Y h:i A', strtotime($result['mock_poll_start']));
				if($mock_poll_start)
				{
					$mock_poll_start_name='Yes'.'('.$mock_poll_start.')';
				}
				else{
					$mock_poll_start_name='No';
				}

				$poll_start=date('d-m-Y h:i A', strtotime($result['poll_start']));
				if($poll_start)
				{
					$poll_start_name='Yes'.'('.$poll_start.')';
				}
				else{
					$poll_start_name='No';
				}
				$total_voter=date('d-m-Y h:i A', strtotime($result['total_voter_date']));
				if($total_voter)
				{
					$total_voter_name='Yes'.'('.$total_voter.')';
				}
				else{
					$total_voter_name='No';
				}
				$poll_end=date('d-m-Y h:i A', strtotime($result['poll_end']));
				if($total_voter)
				{
					$poll_end_name='Yes'.'('.$poll_end.')';
				}
				else{
					$poll_end_name='No';
				}

				if($event_type!=NULL && $event_type==0)
				{
					$export_data[] = [
						$i,
						$result['ac_no'].'-'.$result['ac_name'],
						$result['ps_no'].'-'.$result['ps_name'],
							$mock_poll_start_name,
							$poll_start_name,
							$total_voter_name,
							$poll_end_name,
					  ];
				}
				elseif($event_type!=NULL && $event_type==2){
					$export_data[] = [
						$i,
						$result['ac_no'].'-'.$result['ac_name'],
						$result['ps_no'].'-'.$result['ps_name'],
						$mock_poll_start_name,
						
					  ];
				}
				elseif($event_type!=NULL && $event_type==3){
					$export_data[] = [
						$i,
						$result['ac_no'].'-'.$result['ac_name'],
						$result['ps_no'].'-'.$result['ps_name'],
						$poll_start_name,
						
					  ];
				}
				elseif($event_type!=NULL && $event_type==4){
					$export_data[] = [
						$i,
						$result['ac_no'].'-'.$result['ac_name'],
						$result['ps_no'].'-'.$result['ps_name'],
						$total_voter_name,
						
					  ];
				}
				elseif($event_type!=NULL && $event_type==6){
					$export_data[] = [
						$i,
						$result['ac_no'].'-'.$result['ac_name'],
						$result['ps_no'].'-'.$result['ps_name'],
						$poll_end_name,
						
					  ];
				}
				else{
					$export_data[] = [
						$i,
						$result['ac_no'].'-'.$result['ac_name'],
						$result['ps_no'].'-'.$result['ps_name'],
							$mock_poll_start_name,
							$poll_start_name,
							$total_voter_name,
							$poll_end_name,
					  ];
				}
				

			
			}
			// echo "<pre>";print_r($data['results']);die;
			$name_excel='Booth App - Poll Event PS Wise Report';
			return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


			// return Excel::create('Booth App - Poll Event PS Wise Report', function($excel) use ($data,$request) {
				
			// 	$excel->sheet('mySheet', function($sheet) use ($data,$request)
			// 	{
					
				  
			// 		$sheet->mergeCells('A1:G1');	
					
			// 		$sheet->cell('A1', function($cells) {
			// 			$cells->setValue('Booth App - Poll Event PS Wise Report');
			// 			$cells->setAlignment('center');
			// 			$cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
			// 		});
					
					
					
			// 		$sheet->mergeCells('B2:G2');

			// 		$sheet->cell('A2', function($cells) use($data) {
			// 			$st_name = $data['st_name'];
			// 			$cells->setValue("State/UT Name: $st_name");
			// 			$cells->setAlignment('center');
			// 			$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
			// 		});
					
			// 		$sheet->cell('B2', function($cells) use($data) {
			// 			$ac_name = $data['ac_name'];
			// 			$cells->setValue("AC NO & AC Name: $ac_name");
			// 			$cells->setAlignment('center');
			// 			$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
			// 		});

			// 		$sheet->cell('A3', function($cell) {$cell->setValue('PS NO & PS Name'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 		if(empty($request->event_type)){
						
			// 			$sheet->cell('B3', function($cell) {$cell->setValue('Mock Poll Done'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 			$sheet->cell('C3', function($cell) {$cell->setValue('Poll Started'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 			$sheet->cell('D3', function($cell) {$cell->setValue('Voting Started'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
						
			// 			$sheet->cell('E3', function($cell) {$cell->setValue('Poll End'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
						
			// 		}
			// 		if($request->event_type<>'' && $request->event_type==2){
			// 			$sheet->cell('B3', function($cell) {$cell->setValue('Mock Poll Done'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 		}if($request->event_type<>'' && $request->event_type==3){
			// 			$sheet->cell('B3', function($cell) {$cell->setValue('Poll Started'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 		}if($request->event_type<>'' && $request->event_type==4){
			// 			$sheet->cell('B3', function($cell) {$cell->setValue('Voting Started'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 		}if($request->event_type<>'' && $request->event_type==6){
			// 			$sheet->cell('B3', function($cell) {$cell->setValue('Poll End'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
			// 		}
					
					
			// 		 if (!empty($data)) {
			// 			foreach ($data['results'] as $key => $result) {
			// 				$i= $key+4;
			// 				$sheet->cell('A'.$i, $result['ps_no'].'-'.$result['ps_name'] );
			// 				if(empty($request->event_type)){
								
			// 					$sheet->cell('B'.$i,  ($result['mock_poll_start']) ? 'Yes ('.date('d-m-Y h:i A', strtotime($result['mock_poll_start'])).')':'No'); 
			// 					$sheet->cell('C'.$i,  ($result['poll_start']) ? 'Yes ('.date('d-m-Y h:i A', strtotime($result['poll_start'])).')':'No');
			// 					$sheet->cell('D'.$i,  ($result['total_voter']) ? 'Yes ('.date('d-m-Y h:i A', strtotime($result['total_voter_date'])).')':'No');
								
			// 					$sheet->cell('E'.$i,  ($result['poll_end']) ? 'Yes ('.date('d-m-Y h:i A', strtotime($result['poll_end'])).')':'No');
								
								
			// 				}if($request->event_type<>'' && $request->event_type==2){
			// 					$sheet->cell('B'.$i,  ($result['mock_poll_start']) ? 'Yes ('.date('d-m-Y h:i A', strtotime($result['mock_poll_start'])).')':'No'); 
			// 				}if($request->event_type<>'' && $request->event_type==3){
			// 					$sheet->cell('B'.$i,  ($result['poll_start']) ? 'Yes ('.date('d-m-Y h:i A', strtotime($result['poll_start'])).')':'No');
			// 				}if($request->event_type<>'' && $request->event_type==4){
			// 					$sheet->cell('B'.$i,  ($result['total_voter']) ? 'Yes ('.date('d-m-Y h:i A', strtotime($result['total_voter_date'])).')':'No');
			// 				}if($request->event_type<>'' && $request->event_type==6){
			// 					$sheet->cell('B'.$i,  ($result['poll_end']) ? 'Yes ('.date('d-m-Y h:i A', strtotime($result['poll_end'])).')':'No');
			// 				}
			// 			}
			// 		}
		
			// 	});
			// })->download('xls');
		}else{
		
			return view($this->view.'.Reports.poll-event-ps-wise-report', $data);
		}
		
  }	
		

	public function poll_event_dashboard(Request $request){
	
	  
	  $data                         = [];
	  $data['role_id']              = $this->role_id;
	  $data['action']               = url($this->action);
	  $request_array = [];
	  if($this->st_code){
		$request_array[] = "st_code=".$this->st_code;
	  }
	  if($this->ac_no){
		$request_array[] = "ac_no=".$this->ac_no;
	  }
	 

		$filter = [
				'phase_no'         => $this->phase_no,
				'st_code'         => $this->st_code,
				'ac_no'           => $this->ac_no,
				'ps_no'           => $this->ps_no,
			  ];
			  


	//form filters
	  $data['filter_action'] = Common::generate_url("booth-app-revamp/poll-event-report");
	 
	  if(Auth::user()->role_id == '5'){
		$booth_acs = DB::table('boothapp_enable_acs')->select(DB::raw('group_concat(ac_no) as ac_no'))->where('dist_no',Auth::user()->dist_no)->first();
	  }else{
		$booth_acs = DB::table('boothapp_enable_acs')->select(DB::raw('group_concat(ac_no) as ac_no'))->first();  
	  }
	
	  $allowed_acs = $booth_acs->ac_no;
	  
	  
		
	  $form_filter_array = [
		'phase_no'     => true,
		'st_code'     => true,
		'dist_no'     => false,
		'ac_no'       => true, 
		'ps_no'       => false, 
		'designation'     => false,
		'allowed_acs'     => $allowed_acs,
		'allowed_st_code' => $this->allowed_st_code,
		'allowed_dist_no' => $this->allowed_dist_no,
	  ];
	  $form_filters = Common::get_form_filters($form_filter_array, $request);      
	  $data['form_filters'] = $form_filters;	
	  $allowed_st_code = implode(', ', $this->allowed_st_code);

	  
	  
		/* $sql = "select ps.st_code, st_name, ps.ac_no,ps.ps_no, AC_NAME_EN as ac_name, count(ps.ps_no) as total_ps, count(IF(tps.poll_start_datetime,1,NULL)) as poll_start, count(IF(tps.poll_end_datetime,1,NULL)) as poll_end, count(IF(tps.pro_turn_out,1,NULL)) as total_voter,count(IF((tmps.mock_poll_start = 'Y'),1,NULL)) as mock_poll_start,count(IF(tss.updated_at,1,NULL)) as data_sync,count(IF(tim.PS_NO,1,NULL)) as ps_location,
		COUNT(IF(poll_material_sub = 'Y',1,NULL)) AS total_submited, COUNT(IF(poll_material_rec = 'Y',1,NULL)) AS total_received,COUNT(pdf.id) as pro_diary_sub
	   from polling_station as ps
	   inner join ac_master on (ac_master.ST_CODE = ps.ST_CODE and ac_master.AC_NO = ps.AC_NO)
	   inner join state_master on (state_master.ST_CODE = ps.ST_CODE)
	   inner join m_election_details ph ON (ps.st_code=ph.st_code and ps.ac_no=ph.CONST_NO)
	   left join pro_diary_final pdf ON (ps.st_code=pdf.st_code and ps.ac_no=pdf.ac_no AND pdf.PS_NO = ps.PS_NO AND pdf.row_status = 'A') 
	   left join (select * from tbl_poll_summary group by st_code,ac_no,ps_no) as tps on (tps.ST_CODE = ps.ST_CODE and tps.AC_NO = ps.AC_NO and tps.PS_NO = ps.PS_NO)
	   left join (select ST_CODE,AC_NO,PS_NO,mock_poll_start from tbl_mock_poll_status  group by 1,2,3) as tmps on (tmps.ST_CODE = ps.ST_CODE and tmps.AC_NO =  ps.AC_NO and tmps.PS_NO = ps.PS_NO)
	   left join  tbl_scan_statistics as tss on (tss.ST_CODE = ps.ST_CODE and tss.AC_NO = ps.AC_NO and tss.PS_NO = ps.PS_NO)
	   left join (select ST_CODE,AC_NO,PS_NO from tbl_infra_mapping group by 1,2,3) as tim on (tim.ST_CODE = ps.ST_CODE and tim.AC_NO = ps.AC_NO and tim.PS_NO = ps.PS_NO)
	   where ph.CONST_TYPE = 'AC' AND (ps.ST_CODE = '$allowed_st_code' and ps.AC_NO IN ($allowed_acs))"; */
	   
	   $sql = "SELECT tad.st_code, tad.ac_no,tad.`ps_no`,st_name,AC_NAME_EN AS ac_name,COUNT(IF(poll_started,1,NULL)) AS poll_start, COUNT(IF(poll_ended,1,NULL)) AS poll_end, COUNT(IF(voting,1,NULL)) AS total_voter, 
	(SELECT COUNT(ps_no) FROM polling_station WHERE ST_CODE = tad.st_code AND AC_NO = tad.`ac_no` AND booth_app_excp = 0) AS total_ps,
	COUNT(IF(mockpoll,1,NULL)) AS mock_poll_start, COUNT(IF(final_sync,1,NULL)) AS data_sync,
	COUNT(IF(poll_party_reach,1,NULL)) AS ps_location,COUNT(IF(poll_party_reach,1,NULL)) AS ps_location,
	COUNT(IF(pm_submitted,1,NULL)) AS total_submited,COUNT(IF(pm_received ,1,NULL)) AS total_received,
	COUNT(IF(pro_diary,1,NULL)) AS pro_diary_sub
	FROM tbl_analytics_dashboard AS tad 
	INNER JOIN state_master ON (state_master.ST_CODE = tad.st_code)
	INNER JOIN ac_master ON (ac_master.ST_CODE = tad.st_code  AND ac_master.AC_NO = tad.ac_no)
	inner join m_election_details ph ON (tad.st_code=ph.st_code and tad.ac_no=ph.CONST_NO)
	where ph.CONST_TYPE='AC' ";


	if(!empty($filter['phase_no'])){		
			$phase_no = $filter['phase_no'];
			$sql .= " and ph.phase_no = '$phase_no'";
		}
		
    if(Auth::user()->role_id == '5'){		
			$dist_no = Auth::user()->dist_no;
			$sql .= " and ac_master.DIST_NO_HDQTR = '$dist_no'";
		}


	if(!empty($filter['st_code'])){		
		$st_code = $filter['st_code'];
		$sql .= " and tad.st_code = '$st_code'";
    }

    if(!empty($filter['ac_no'])){
		$ac_no = $filter['ac_no'];
		$sql .= " and ac_master.ac_no = '$ac_no'";
    }
	
	if(!empty($filter['ps_no'])){
		$ps_no = $filter['ps_no'];
		$sql .= " and tad.ps_no = '$ps_no'";
    }

	$sql .= "group by tad.st_code, tad.ac_no order by tad.st_code, tad.ac_no ASC";	
	
			
	$myObj   =   DB::connection('booth_revamp_test')->select($sql);
	
	
	$data['results'] = json_decode(json_encode($myObj), true);
		
	return view($this->view.'.Reports/poll-event-dashboard', $data);
		
	}	
	
	
	public function getanalyticsdashboard(Request $request){

	$data = [];
	$st_code        = $this->st_code;
	$ac_no          = $this->ac_no;
	$ps_no          = $this->ps_no;

	  $filter = [
		'st_code' => $st_code,
		'ac_no'   => $ac_no,
		'ps_no'   => $ps_no
	  ];

	$data['analytics'] = TblAnalyticsDashboardModel::get_analytics_data($filter);

	//form filters
  $data['filter_action'] = Common::generate_url("booth-app-revamp/dashboard_data_analytics");
  $form_filter_array = [
    'phase_no'  => true,
    'st_code'     => true,
    'ac_no'       => true,
    'ps_no'       => true,
    'designation' => false,
    'allowed_acs'     => $this->allowed_acs,
    'allowed_st_code' => $this->allowed_st_code,
    'allowed_dist_no' => $this->allowed_dist_no,
  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);
  $data['form_filters']   = $form_filters;
  $data['user_data']      = Auth::user();

	return view($this->view.'.Reports/dashboard_data', $data);

	}
	
	public function getdisconnectedps(Request $request){
	
	$st_code        = $this->st_code;
	$ac_no          = $this->ac_no;
	$ps_no          = $this->ps_no;
	$phase_no       = $this->phase_no;

	  $filter = [
		'st_code' => $st_code,
		'ac_no'   => $ac_no,
		'ps_no'   => $ps_no,
		'phase_no' =>$phase_no
	  ];
	
		 $sql = "SELECT temp.*,st.ST_NAME FROM(
	SELECT COUNT(id) AS total_ps, SUM(TIMESTAMPDIFF(MINUTE,tbl_analytics_dashboard.updated_at,NOW()) < 30)  AS is_connected,
	`tbl_analytics_dashboard`.`st_code`,`tbl_analytics_dashboard`.`ac_no`,`tbl_analytics_dashboard`.`ps_no`,polling_station.`PS_NAME_EN`
	FROM `tbl_analytics_dashboard` INNER JOIN `m_election_details`
	ON (`m_election_details`.`st_code` = `tbl_analytics_dashboard`.`st_code`
	AND `m_election_details`.`CONST_NO` = `tbl_analytics_dashboard`.`ac_no`)
	INNER JOIN `polling_station` ON (`polling_station`.`ST_CODE` = `tbl_analytics_dashboard`.`st_code`
	AND `polling_station`.`AC_NO` = `tbl_analytics_dashboard`.`ac_no` AND `polling_station`.`PS_NO` = `tbl_analytics_dashboard`.`ps_no`)
	WHERE `polling_station`.`booth_app_excp` = 0
	AND `m_election_details`.`PHASE_NO` = '$phase_no' AND `tbl_analytics_dashboard`.`status` = 'A'
	GROUP BY `tbl_analytics_dashboard`.`ac_no`,`tbl_analytics_dashboard`.`ps_no`
	)
	temp LEFT JOIN state_master st ON (temp.st_code=st.ST_CODE)
	WHERE temp.is_connected='0' ";
	
	if(!empty($filter['ac_no'])){		
			$ac_no= $filter['ac_no'];
			$sql .= " and temp.ac_no = '$ac_no'";
	}
	
	if(!empty($filter['ps_no'])){		
			$ps_no= $filter['ps_no'];
			$sql .= " and temp.ps_no = '$ps_no'";
	}
	
	$myObj   =   DB::connection('booth_revamp_test')->select($sql);
	
	
	$data['result'] = json_decode(json_encode($myObj), true);
	$data['user_data']      = Auth::user();
	
	$data['filter_action'] = Common::generate_url("booth-app-revamp/disconnected-ps-report");
	  $form_filter_array = [
		'phase_no'  => true,
		'st_code'     => true,
		'ac_no'       => true,
		'ps_no'       => true,
		'designation' => false,
		'allowed_acs'     => $this->allowed_acs,
		'allowed_st_code' => $this->allowed_st_code,
		'allowed_dist_no' => $this->allowed_dist_no,
	  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);
  $data['form_filters']   = $form_filters;
	
	
	return view($this->view.'.Reports/disconnected', $data);
	
	
	}
	
	public function getform49count(Request $request){
	
	$st_code        = $this->st_code;
	$ac_no          = $this->ac_no;
	$ps_no          = $this->ps_no;
	$phase_no       = $this->phase_no;

	  $filter = [
		'st_code' => $st_code,
		'ac_no'   => $ac_no,
		'ps_no'   => $ps_no,
		'phase_no' =>$phase_no
	  ];
	
		 $sql = "SELECT tbl_analytics_dashboard.st_code,tbl_analytics_dashboard.ac_no,tbl_analytics_dashboard.ps_no,form_49_count,polling_station.PS_NAME_EN FROM tbl_analytics_dashboard 
		 INNER JOIN `polling_station` ON (`polling_station`.`ST_CODE` = `tbl_analytics_dashboard`.`st_code`
			AND `polling_station`.`AC_NO` = `tbl_analytics_dashboard`.`ac_no` AND `polling_station`.`PS_NO` = `tbl_analytics_dashboard`.`ps_no`)
		 WHERE form_49_count <> '0' ";
	
	if(!empty($filter['ac_no'])){		
			$ac_no= $filter['ac_no'];
			$sql .= " and tbl_analytics_dashboard.ac_no = '$ac_no'";
	}
	
	if(!empty($filter['st_code'])){		
			$st_code= $filter['st_code'];
			$sql .= " and tbl_analytics_dashboard.st_code = '$st_code'";
	}
	
	if(!empty($filter['ps_no'])){		
			$ps_no= $filter['ps_no'];
			$sql .= " and tbl_analytics_dashboard.ps_no = '$ps_no'";
	}
	
	$myObj   =   DB::connection('booth_revamp_test')->select($sql);
	
	
	$data['result'] = json_decode(json_encode($myObj), true);
	$data['user_data']      = Auth::user();
	
	$data['filter_action'] = Common::generate_url("booth-app-revamp/form49-ps-report");
	  $form_filter_array = [
		'phase_no'  => true,
		'st_code'     => true,
		'ac_no'       => true,
		'ps_no'       => true,
		'designation' => false,
		'allowed_acs'     => $this->allowed_acs,
		'allowed_st_code' => $this->allowed_st_code,
		'allowed_dist_no' => $this->allowed_dist_no,
	  ];
  $form_filters = Common::get_form_filters($form_filter_array, $request);
  $data['form_filters']   = $form_filters;
	
	
	return view($this->view.'.Reports/form49', $data);
	
	
	}
	public function getTenderedcount(Request $request){
	
		$st_code        = $this->st_code;
		$ac_no          = $this->ac_no;
		$ps_no          = $this->ps_no;
		$phase_no       = $this->phase_no;
	
		  $filter = [
			'st_code' => $st_code,
			'ac_no'   => $ac_no,
			'ps_no'   => $ps_no,
			'phase_no' =>$phase_no
		  ];
		
			 $sql = "SELECT tbl_analytics_dashboard.st_code,tbl_analytics_dashboard.ac_no,tbl_analytics_dashboard.ps_no,tendered_count,polling_station.PS_NAME_EN FROM tbl_analytics_dashboard 
			 INNER JOIN `polling_station` ON (`polling_station`.`ST_CODE` = `tbl_analytics_dashboard`.`st_code`
				AND `polling_station`.`AC_NO` = `tbl_analytics_dashboard`.`ac_no` AND `polling_station`.`PS_NO` = `tbl_analytics_dashboard`.`ps_no`)
			 WHERE tendered_count <> '0' ";
		
		if(!empty($filter['ac_no'])){		
				$ac_no= $filter['ac_no'];
				$sql .= " and tbl_analytics_dashboard.ac_no = '$ac_no'";
		}
		
		if(!empty($filter['st_code'])){		
				$st_code= $filter['st_code'];
				$sql .= " and tbl_analytics_dashboard.st_code = '$st_code'";
		}
		
		if(!empty($filter['ps_no'])){		
				$ps_no= $filter['ps_no'];
				$sql .= " and tbl_analytics_dashboard.ps_no = '$ps_no'";
		}
		
		$myObj   =   DB::connection('booth_revamp_test')->select($sql);
		
		
		$data['result'] = json_decode(json_encode($myObj), true);
		$data['user_data']      = Auth::user();
		
		$data['filter_action'] = Common::generate_url("booth-app-revamp/tendered-ps-report");
		  $form_filter_array = [
			'phase_no'  => true,
			'st_code'     => true,
			'ac_no'       => true,
			'ps_no'       => true,
			'designation' => false,
			'allowed_acs'     => $this->allowed_acs,
			'allowed_st_code' => $this->allowed_st_code,
			'allowed_dist_no' => $this->allowed_dist_no,
		  ];
	  $form_filters = Common::get_form_filters($form_filter_array, $request);
	  $data['form_filters']   = $form_filters;
		
		
		return view($this->view.'.Reports/tendered_count', $data);
		
		
		}
		
 
}  // end class