<?php  
		namespace App\Http\Controllers\Admin\Deo;
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
		use MPDF;
		use App\commonModel;
		use App\adminmodel\CandidateModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\adminmodel\MasterReportModel;
		use App\adminmodel\ROACReportModel;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
class MasterDeoController extends Controller
{

   public function __construct()
        {   
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->masterreportmodel = new MasterReportModel();
			$this->xssClean = new xssClean;
			$this->sym = new SymbolMaster();	
		}
	 
	public function getNominationreport(request $request){
		if(Auth::check()){
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
			$sched='';
			if(isset($ele_details)) {
			foreach($ele_details as $ed) {
				$sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
				$const_type=$ed->CONST_TYPE;
			}
			}
			$st_code =$d->st_code;
			$dist_no =$d->dist_no;
			$allAcList= DB::table('candidate_nomination_detail')
			->select('*', DB::raw('count(nom_id) as totalnomination'))
			->where('st_code',$d->st_code)->where('district_no', $d->dist_no)->where('party_id','!=','1180')->where('application_status','!=','11')->groupBy('ac_no')->get();
			return view('admin.ac.deo.nomination-report',['st_code'=>$st_code,'dist_no'=>$dist_no,'user_data' => $d,'allAcList' => $allAcList,'ele_details'=>$ele_details,'sched' =>$sched]);
              }	else {
								return redirect('/officer-login');
							}
	}

	 public function candidateListbyAC(Request $request,$acno){
		if(Auth::check()){
		 $user = Auth::user();
		 $d=$this->commonModel->getunewserbyuserid($user->id);
		 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
		 $AllcandListbyAC=$this->masterreportmodel->getNominatedCandidatebyAC($d->st_code,$acno);
		 $st_code =$d->st_code;
		 $dist_no =$d->dist_no;
		return view('admin.ac.deo.candidatelist-ac',['st_code'=>$st_code,'dist_no'=>$dist_no,'user_data' => $d,'ele_details' => $ele_details,'candListbyAC' => $AllcandListbyAC,'ac_no'=>$acno]);
	}
	else {
			return redirect('/officer-login');
		}
	}
	
	public function nominationadatewisereport(Request $request){
			if(Auth::check()){ 
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
						$from_date = ($request->from_date);
						$to_date = ($request->to_date); 
						$st_code = $request->st_code;
						$dist_no = $request->dist_no;

						if(isset($from_date)){
							if($from_date=='all' && $to_date=='all'){
								$from_date='';
								$to_date='';
							}
						}
						
						$timeInterval = $from_date.'~'.$to_date;
						
						$fromdate = date('Y-m-d',strtotime($from_date));
						$todate = date('Y-m-d',strtotime($to_date));  

						$datewisenominationreport=$this->masterreportmodel->getDatewisenomination_at_deo($st_code,$d->dist_no,$fromdate,$todate);
							if(!empty($datewisenominationreport)){  $j=1;
								$canddetailsArray = array();
                $html='';
                $totalg=0;
									foreach ($datewisenominationreport as $listdata) { 
                    $j++;
									    $ac=getacbyacno($listdata->st_code,$listdata->ac_no);
                    // dd($ac);
                     $totalg=$totalg+$listdata->totalnomination;
$url = url('acdeo/datewisecandidatelist/'.base64_encode($ac->AC_NO).'/'.base64_encode($timeInterval));					 
						$html.='<tr>
                                <td>'.$ac->AC_NO.'</td>
                                <td><a target="" href="'.$url.'">'.$ac->AC_NAME.'</a></td>
								<td><a target="" href="'.$url.'">'.$listdata->totalnomination.'</a></td>
								</tr>';
                      }
                      $html.='<tr> 
                      <td>Total:- </td>
                      <td> </td> 
                      <td>'.$totalg.'</td>
                     </tr>';   
										}	else{
										 $html .= '<tr><td colspan="3" style="color:red; text-align:center;"><b>No Record Found.</b></td></tr>';
										}
											return $html;
				       	}else {
								return redirect('/officer-login');
							}
		 }

	 public function datewisecandidatelist(Request $request,$acno,$date){
	  $date=trim(base64_decode($request->date));
		$acno=trim(base64_decode($request->acno));
	  $date_range = explode('~', $date);
	  $from_date=$date_range[0];
	  $to_date=$date_range[1];
	  $fromdate = date('Y-m-d',strtotime($from_date));
	  $todate = date('Y-m-d',strtotime($to_date));
	  if(Auth::check()){
	   $user = Auth::user();
	   $d=$this->commonModel->getunewserbyuserid($user->id);
	   $st_code =$d->st_code;
	   $dist_no =$d->dist_no;
	   $ele_details=$this->commonModel->election_detailsac($d->st_code, $d->ac_no, $d->dist_no, $d->id, $d->officerlevel);
	   $AllcandListbyAC=$this->masterreportmodel->getDatewiseCandidateListbyAC($st_code,$acno,$fromdate,$todate);
	  return view('admin.ac.deo.datewisecandidatelist',['date'=>$date,'st_code'=>$st_code,'dist_no'=>$dist_no,'user_data' => $d,'ele_details' => $ele_details,'candListbyAC' => $AllcandListbyAC,'ac_no'=>$acno]);
	}
	else {
		return redirect('/officer-login');
	  }
	}
	
	public function ViewNominationDetails($nomid)
	{ 
		if(Auth::check()){ 
						$user = Auth::user();
						$d=$this->commonModel->getunewserbyuserid($user->id); 
						$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
						$nom=getById('candidate_nomination_detail','nom_id',$nomid); 
						$cand=getById('candidate_personal_detail','candidate_id',$nom->candidate_id); 	
			return view('admin.ac.deo.viewnomination', ['user_data' => $d, 'nomid'=>$nomid,'nomDetails'=>$nom,'persoanlDetails'=>$cand, 'ele_details'=>$ele_details]);	           
		}
		else{
			return redirect('/officer-login');
		}
	}

	public function reportexcelview(Request $request) {
		set_time_limit(6000);
			$date=trim(base64_decode($request->date));
			$acno=trim(base64_decode($request->consti));

			if($date=='all') {
				$fromdate='';
				$todate='';
			}else{
				$date_range = explode('~', $date);
				$from_date=$date_range[0];
				$to_date=$date_range[1];
				$fromdate = date('Y-m-d',strtotime($from_date));
				$todate = date('Y-m-d',strtotime($to_date));
			}
			if(Auth::check()){
				$user = Auth::user();
				$d=$this->commonModel->getunewserbyuserid($user->id);
				$st_code =$d->st_code;
				$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
										$cur_time = Carbon::now();


										$candListbyPC=$this->masterreportmodel->getDatewiseCandidateListbyAC($st_code,$acno,$fromdate,$todate);
											$arr  = array();
											$cand_party_type='Z'; $finalize='1';
											$user = Auth::user();
											$d=$this->commonModel->getunewserbyuserid($user->id);
											$allPcList=$this->commonModel->getpcbystate($d->st_code);
											$count = 1;
											$headings[]=[];
											$export_data[]=['Serial No.','AC Number&Name' ,'Candidate Name','Candidate Name Hindi','Party Name', 'Symbol'];
											foreach ($candListbyPC as $list) {
												$candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
												$partyDetails=getById('m_party','CCODE',$list->party_id);
												$acDetails=getacbyacno($d->st_code,$list->ac_no);
												$symbolDetails=getsymbolbyid($list->symbol_id);
													if(!empty($partyDetails)){ $partyname =$partyDetails->PARTYNAME;} else{ $partyname='-'; }
									if(!empty($symbolDetails)){ $symbolName =$symbolDetails->SYMBOL_DES;} else{ $symbolName='-'; }
													
												$export_data[] = [
													$count,
													$list->ac_no.' - '.$acDetails->AC_NAME,
													$candidatedetails->cand_name,
													$candidatedetails->cand_hname,
													$partyname,
													$symbolName
												];
												$count++;


																	}


										$name_excel = 'nominated-candidate-detail-excel'.trim($st_code).'_'.$cur_time;
                                       return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


									// 	\Excel::create('nominated-candidate-detail-excel'.trim($st_code).'_'.$cur_time, function($excel) use($st_code,$acno,$fromdate,$todate) { 
									// 		$excel->sheet('Sheet1', function($sheet) use($st_code,$acno,$fromdate,$todate) {
											
									// 		$candListbyPC=$this->masterreportmodel->getDatewiseCandidateListbyAC($st_code,$acno,$fromdate,$todate);
									// 		$arr  = array();
									// 		$cand_party_type='Z'; $finalize='1';
									// 		$user = Auth::user();
									// 		$d=$this->commonModel->getunewserbyuserid($user->id);
									// 		$allPcList=$this->commonModel->getpcbystate($d->st_code);
									// 		$count = 1;
									// 		foreach ($candListbyPC as $list) {
									// 			$candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
									// 			$partyDetails=getById('m_party','CCODE',$list->party_id);
									// 			$acDetails=getacbyacno($d->st_code,$list->ac_no);
									// 			$symbolDetails=getsymbolbyid($list->symbol_id);
									// 				if(!empty($partyDetails)){ $partyname =$partyDetails->PARTYNAME;} else{ $partyname='-'; }
									// if(!empty($symbolDetails)){ $symbolName =$symbolDetails->SYMBOL_DES;} else{ $symbolName='-'; }
									// 				$data =  array(
									// 								$count,
									// 								$list->ac_no.' - '.$acDetails->AC_NAME,
									// 								$candidatedetails->cand_name,
									// 								$candidatedetails->cand_hname,
									// 								$partyname,
									// 								$symbolName
									// 											);
									// 								array_push($arr, $data);
									// 								$count++;
									// 								}
									// 					 $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
									// 										 'Serial No.','AC Number&Name' ,'Candidate Name','Candidate Name Hindi','Party Name', 'Symbol'
									// 						 )
									// 				 );
									// 			 });
									// 	})->export('xls');
					}	else {
										return redirect('/officer-login');
									}
								} 
}
