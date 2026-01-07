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
		//use Spatie\MixedContentScanner\MixedContentScanner;
		use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterCeoController extends Controller
{
    //
   public function __construct()
        {   
		//	$this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->middleware('ceo');
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->masterreportmodel = new MasterReportModel();
		//	$this->roacreportmodel = new ROACReportModel();
			$this->xssClean = new xssClean;
			$this->sym = new SymbolMaster();	
		}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
 	    protected function guard(){
        return Auth::guard('admin');
    	}
  
	 /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 15-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return electors-pollingstation List By State fuction     
  */
   
  public function electorspollingstationList(Request $request){
		 //dd($request->all());
		if(Auth::check()){
		 $user = Auth::user();
		 $d=$this->commonModel->getunewserbyuserid($user->id);
		 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		  //dd($ele_details);
			$all_dist=$this->commonModel->getalldistrictbystate($d->st_code);
			return view('admin.ac.ceo.electors-pollingstationlist',['user_data' => $d,'ele_details' => $ele_details,'all_dist' => $all_dist]);
			}else {
			return redirect('/officer-login');
		}   
	 }   // end electorspollingstation List function
/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 15-03-19
 * @author Modified By : 
 * @author Modified Date : 
 * @author param return electors-pollingstation By State fuction     
 */
public function getaclistbyDeo(request $request){ 
	 
	$dist_no = $request['dist_no'];
 if(Auth::check()){ 
	 $user = Auth::user();
	 $d=$this->commonModel->getunewserbyuserid($user->id);
	 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
	 $election_id=$ele_details[0]->ELECTION_ID;
	 $html='';
	 $j=0;
	if($dist_no!=200){ 
			 $acdata = $this->masterreportmodel->getAcByDeo($d->st_code,$dist_no,$election_id);	   
			 $html.='<thead>
			 <tr>
				<th colspan="3"> AC No & AC Name </th>
				<th colspan="4">General Electors</th>
				<th colspan="4">Service Electors</th>
				<th colspan="3">Polling Stations</th>
			 </tr>
				<tr>
				<th size="2">S.No.</th>
				<th>AC No</th>
				<th>AC Name</th>
				<th size="2">Male</th>
				<th size="2">Female</th>
				<th size="2">Third Gender</th>
				<th size="2">Total</th>
 
				<th size="2">Male</th>
				<th size="2">Female</th>
				<th size="2">Third Gender</th>
				<th size="2">Total</th>
 
				<th size="2">Regular</th>
				<th size="2">Auxillary</th>
				<th size="2">Total</th>
				</tr>
		</thead>';
			
		 foreach($acdata as $acdataList){ 
				$j++;  
			$html.='<input type="hidden" name="dist_no" value="'.$acdataList->dist_no.'">
							<input type="hidden" name="st_code" value="'.$acdataList->st_code.'">';
			$html.='<tr>
				<td><input type="hidden"   name=""  value="'.$j.'"  maxlength="5" readonly="readonly" size="2"><span>'.$j.'</span></td> 
				<td><input type="hidden"   name="ac_no[]"  value="'.$acdataList->ac_no.'"  maxlength="8" readonly="readonly"><span>'.$acdataList->AC_NO.'</span></td> 
				<td><input type="hidden"  name="ac_name[]"  value="'.$acdataList->AC_NAME.'" maxlength="8"  readonly="readonly"><span>'.$acdataList->AC_NAME.'</span></td> 
				<td><input type="text"    name="gen_male[]" id="gen_male" value="'.$acdataList->gen_m.'"   size="7" readonly="readonly"></td> 
				<td><input type="text"    name="gen_female[]" id="gen_female" value="'.$acdataList->gen_f.'"  size="7" readonly="readonly"> </td>         
				<td><input type="text"    name="gen_third[]" id="gen_third" value="'.$acdataList->gen_o.'" size="7"  readonly="readonly"> </td>          
				<td><input type="text"   name="gen_total[]" id="gen_total" value="'.$acdataList->gen_t.'" size="7"  readonly="readonly"> </td>  
 
				<td><input type="text" name="ser_male[]" id="ser_male" value="'.$acdataList->ser_m.'" size="7"   readonly="readonly"> </td> 
				<td><input type="text" name="ser_female[]" id="ser_female" value="'.$acdataList->ser_f.'" size="7"   readonly="readonly"> </td>          
				<td><input type="text" name="ser_third[]" id="ser_third" value="'.$acdataList->ser_o.'" size="7" readonly="readonly"> </td> 
				<td><input type="text" name="ser_total[]" id="ser_total" value="'.$acdataList->ser_t.'" size="7" readonly="readonly"> </td> 
				
				<td><input type="text" name="regular[]" id="regular" value="'.$acdataList->polling_reg.'" size="7" readonly="readonly"> </td> 
				<td><input type="text" name="auxillary[]" id="auxillary" value="'.$acdataList->polling_auxillary.'" size="7"   readonly="readonly"> </td> 
				<td><input type="text" name="polling_total[]" id="polling_total" value="'.$acdataList->polling_total.'" size="7"  readonly="readonly"></span> </td> 
				 </tr>';
				}
			}else{ 
		$electorSummary = $this->masterreportmodel->getelectorssummarybyState($d->st_code,$election_id);		 
		$html.='<thead>
		<tr>
		 <th colspan="3"> DistNo & Dist Name </th>
		 <th colspan="4">General Electors</th>
		 <th colspan="4">Service Electors</th>
		 <th colspan="3">Polling Stations</th>
		</tr>

		 <tr>
		 <th size="2">S.No.</th>
		 <th>Dist No</th>
		 <th>Dist Name</th>
		 <th size="2">Male</th>
		 <th size="2">Female</th>
		 <th size="2">Third Gender</th>
		 <th size="2">Total</th>

		 <th size="2">Male</th>
		 <th size="2">Female</th>
		 <th size="2">Third Gender</th>
		 <th size="2">Total</th>

		 <th size="2">Regular</th>
		 <th size="2">Auxillary</th>
		 <th size="2">Total</th>
		 </tr>
 </thead>';
		foreach($electorSummary as $acdataSummary){ 
		 $j++;  
		$html.='<input type="hidden" name="dist_no" value="'.$acdataSummary->DIST_NO.'">
					 <input type="hidden" name="st_code" value="">';
		$html.='<tr>
		 <td><input type="hidden"   name=""  value="'.$j.'"  maxlength="5" readonly="readonly" size="2"><span>'.$j.'</span></td> 
		 <td><input type="hidden"   name="dist_no[]"  value="'.$acdataSummary->DIST_NO.'"  readonly="readonly"><span>'.$acdataSummary->DIST_NO.'</span></td> 
		 <td><input type="hidden"  name="dist_name[]"  value="'.$acdataSummary->DIST_NAME.'"   readonly="readonly"><span>'.$acdataSummary->DIST_NAME.'</span></td> 
		 <td><input type="text"    name="gen_male[]" id="gen_male" value="'.$acdataSummary->total_gen_m.'" size="7" readonly="readonly"></td> 
		 <td><input type="text"    name="gen_female[]" id="gen_female" value="'.$acdataSummary->total_gen_f.'" size="7" readonly="readonly"> </td>         
		 <td><input type="text"    name="gen_third[]" id="gen_third" value="'.$acdataSummary->total_gen_o.'" size="7"  readonly="readonly"> </td>          
		 <td><input type="text"   name="gen_total[]" id="gen_total" value="'.$acdataSummary->total_gen_t.'" size="7"  readonly="readonly"> </td>  

		 <td><input type="text" name="ser_male[]" id="ser_male" value="'.$acdataSummary->total_ser_m.'" size="7"   readonly="readonly"> </td> 
		 <td><input type="text" name="ser_female[]" id="ser_female" value="'.$acdataSummary->total_ser_f.'" size="7"  readonly="readonly"> </td>          
		 <td><input type="text" name="ser_third[]" id="ser_third" value="'.$acdataSummary->total_ser_o.'" size="7"   readonly="readonly"> </td> 
		 <td><input type="text" name="ser_total[]" id="ser_total" value="'.$acdataSummary->total_ser_t.'" size="7"  readonly="readonly"> </td> 
		 
		 <td><input type="text" name="regular[]" id="regular" value="'.$acdataSummary->total_polling_reg.'" size="7"  readonly="readonly"> </td> 
		 <td><input type="text" name="auxillary[]" id="auxillary" value="'.$acdataSummary->total_polling_auxillary.'" size="7"   readonly="readonly"> </td> 
		 <td><input type="text" name="polling_total[]" id="polling_total" value="'.$acdataSummary->total_polling_total.'" size="7" readonly="readonly"></span> </td> 
			</tr>';
		    }
		   }
			 return $html;
	}      
} //end getaclistbyDeo function

/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 18-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return getNominationreport report By PC wise     
  */
	public function getNominationreport(request $request){
		if(Auth::check()){
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
			$sched='';
			if(isset($ele_details)) {
			foreach($ele_details as $ed) {
			$sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
			$const_type=$ed->CONST_TYPE;
			}
			}
      
      $allAcList= DB::table('candidate_nomination_detail')
			->select('*', DB::raw('count(nom_id) as totalnomination'))
			->where('st_code',$d->st_code)->where('party_id','!=','1180')->where('application_status','!=','11')->groupBy('ac_no')->get();
			// dd($allAcList);
			return view('admin.ac.ceo.nomination-report',['user_data' => $d,'allAcList' => $allAcList,'ele_details'=>$ele_details,'sched' =>$sched]);
              }	else {
								return redirect('/officer-login');
							}
		} // end getNominationreport List function
		
		/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 18-01-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return pcList By State fuction
 */

 public function candidateListbyAC(Request $request,$acno){
	if(Auth::check()){
	 $user = Auth::user();


	 $d=$this->commonModel->getunewserbyuserid($user->id);

	 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
	 $AllcandListbyAC=$this->masterreportmodel->getNominatedCandidatebyAC($d->st_code,$acno);
	 $st_code =$d->st_code;
	//  dd($AllcandListbyAC);
	return view('admin.ac.ceo.candidatelist-ac',['st_code'=>$st_code,'user_data' => $d,'ele_details' => $ele_details,'candListbyAC' => $AllcandListbyAC,'ac_no'=>$acno]);
}
else {
		return redirect('/officer-login');
	}
}   // end pclist function


/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 19-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return nominationadatewisereport filter By date     
  */
		public function nominationadatewisereport(Request $request){  
			//dd($request->all());
			if(Auth::check()){ 
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			// dd($d);
			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
			//dd($ele_details);
						$from_date = ($request->from_date);
						$to_date = ($request->to_date); 
						$st_code = $request->st_code;
						$ac_no = $request->ac_no;

						if(isset($from_date)){
							if($from_date=='all' && $to_date=='all'){
								$from_date='';
								$to_date='';
							}
						}
						
						$timeInterval = $from_date.'~'.$to_date;
						
						$fromdate = date('Y-m-d',strtotime($from_date));
						$todate = date('Y-m-d',strtotime($to_date));  

						$datewisenominationreport=$this->masterreportmodel->getDatewisenomination($st_code,$d->pc_no,$fromdate,$todate);
            // dd($datewisenominationreport);
							if(!empty($datewisenominationreport)){  $j=1;
								$canddetailsArray = array();
                $html='';
                $totalg=0;
									foreach ($datewisenominationreport as $listdata) { 
                    $j++;
									    $ac=getacbyacno($listdata->st_code,$listdata->ac_no);
                    // dd($ac);
                     $totalg=$totalg+$listdata->totalnomination;  
						$html.='<tr>
                                <td>'.$ac->AC_NO.'</td>
                                <td><a target="" href="'.url('/suvidhaac/public').'/'.'acceo/datewisecandidatelist/'.base64_encode($ac->AC_NO).'/'.base64_encode($timeInterval).'/'.'.">'.$ac->AC_NAME.'</a></td>
								<td><a target="" href="'.url('/suvidhaac/public').'/'.'acceo/datewisecandidatelist/'.base64_encode($ac->AC_NO).'/'.base64_encode($timeInterval).'/'.'.">'.$listdata->totalnomination.'</a></td>
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
		 }// end nominationadatewisereport List function
     
     /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 18-01-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return pcList By State fuction
 */

 public function datewisecandidatelist(Request $request,$acno,$date){
  $date=trim(base64_decode($request->date));
	$acno=trim(base64_decode($request->acno));
  $date_range = explode('~', $date);
  $from_date=$date_range[0];
  $to_date=$date_range[1];
  $fromdate = date('Y-m-d',strtotime($from_date));
  $todate = date('Y-m-d',strtotime($to_date));
 //echo $fromdate.'==>'.$todate.'==>'.$acno; die('test');
  if(Auth::check()){
   $user = Auth::user();

   
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $st_code =$d->st_code;
	 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $AllcandListbyAC=$this->masterreportmodel->getDatewiseCandidateListbyAC($st_code,$acno,$fromdate,$todate);
  //  dd($AllcandListbyAC);
  return view('admin.ac.ceo.datewisecandidatelist',['date'=>$date,'st_code'=>$st_code,'user_data' => $d,'ele_details' => $ele_details,'candListbyAC' => $AllcandListbyAC,'ac_no'=>$acno]);
}
else {
    return redirect('/officer-login');
  }
}   // end pclist function

public function ViewNominationDetails($nomid)
{ 
	if(Auth::check()){ 
					$user = Auth::user();
					$d=$this->commonModel->getunewserbyuserid($user->id); 
					 $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
					//  dd($ele_details);
				//  $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
				//  if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
				// 	 $cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
				//  }
		//  $seched=getschedulebyid($ele_details->ScheduleID);
				//  $sechdul=checkscheduledetails($seched);
				 
					$nom=getById('candidate_nomination_detail','nom_id',$nomid); 
		$cand=getById('candidate_personal_detail','candidate_id',$nom->candidate_id); 
		 
			
			 
	 
			
		return view('admin.ac.ceo.viewnomination', ['user_data' => $d, 'nomid'=>$nomid,'nomDetails'=>$nom,'persoanlDetails'=>$cand, 'ele_details'=>$ele_details]);	           
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
			$ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);


			$candListbyPC=$this->masterreportmodel->getDatewiseCandidateListbyAC($st_code,$acno,$fromdate,$todate);
			$arr  = array();
			$cand_party_type='Z'; $finalize='1';
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			$allPcList=$this->commonModel->getpcbystate($d->st_code);
		 //print_r($independentCandList);die;
			$count = 1;
			$headings[]=[];
			$export_data[]=[ 'Serial No.','AC Number&Name' ,'Candidate Name','Candidate Name Hindi','Party Name', 'Symbol'];
			foreach ($candListbyPC as $list) {
				$candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
				//  dd($candidatedetails);
				$partyDetails=getById('m_party','CCODE',$list->party_id);
				//  dd($partyDetails->PARTYNAME);
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


									$cur_time = Carbon::now();

									$name_excel='nominated-candidate-detail-excel'.trim($st_code).'_'.$cur_time;
									return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


								// 	\Excel::create('nominated-candidate-detail-excel'.trim($st_code).'_'.$cur_time, function($excel) use($st_code,$acno,$fromdate,$todate) {
								// 		$excel->sheet('Sheet1', function($sheet) use($st_code,$acno,$fromdate,$todate) {

								// 		$candListbyPC=$this->masterreportmodel->getDatewiseCandidateListbyAC($st_code,$acno,$fromdate,$todate);
								// 		$arr  = array();
								// 		$cand_party_type='Z'; $finalize='1';
								// 		$user = Auth::user();
								// 		$d=$this->commonModel->getunewserbyuserid($user->id);
								// 		$allPcList=$this->commonModel->getpcbystate($d->st_code);
								// 	 //print_r($independentCandList);die;
								// 		$count = 1;
								// 		foreach ($candListbyPC as $list) {
								// 			$candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
								// 			//  dd($candidatedetails);
								// 			$partyDetails=getById('m_party','CCODE',$list->party_id);
								// 			//  dd($partyDetails->PARTYNAME);
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
}  // end class  
