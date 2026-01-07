<?php 
	/////////////////////////////////////////////////////
	//  Code By Chanderkant for Suvidha Admin Booth App
	//////////////////////////////////////////////////////
	namespace App\Http\Controllers\API;
	
	
	
	namespace App\Http\Controllers\API;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Validation\Rule;
	use DB;
	use App\commonModel;
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Response;
	use App\Classes\xssClean;
	
	
	class ba_SuvidhaAdminController extends Controller
	{
		
		public $successStatus = 200;
		public $createdStatus = 201;
		public $nocontentStatus = 204;
		public $intservererrorStatus = 500;
		public $notfoundStatus = 400;
		
		public function __construct(Request $request)
		{
			$this->commonModel  = new commonModel();
		}
		
		
		public function ba_get_schedule(Request $request) 
		{
			
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);          
				} 
				
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				$summary=array();
				$summary['success'] = false;
				$summary['endpoint']="Booth App Scheduled Booth";
				$summary['message'] = "Login Failed";
				$summary['data'] = array();
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						if($udata->accesstoken == $actoken)	
						{
							$summary['success'] = true;
							$summary['message'] = "";
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							$summary['success'] = false;
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
					}
					if($summary['success'])
					{
						$stlist= DB::connection('booth_revamp')->table('tbl_poll_summary')->select('st_code','ac_no','ps_no')->orderby('ac_no')->get();
						foreach($stlist as $srec)
						{
							//print_r($srec);
							if(!isset($summary['data'][$srec->st_code]))
							{
								$tarr=array();
								$tarr['state_code']=$srec->st_code;
								$stdata=$this->commonModel->getstatebystatecode($srec->st_code);
								$tarr['state_name_en']=$stdata->ST_NAME;
								$tarr['state_name_hi']=$stdata->ST_NAME_HI;
								$tarr['state_name_reg']=$stdata->ST_NAME_V1;
								$tarr['ac_list']=array();
								$summary['data'][$srec->st_code]=$tarr;
							}
							if(!isset($summary['data'][$srec->st_code]['ac_list'][$srec->ac_no]))
							{
								$tarr=array();
								$tarr['ac_no']=$srec->ac_no;
								$adrec=$this->commonModel->getacbyacno($srec->st_code,$srec->ac_no);
								if($adrec)
								$tarr['ac_name']=$adrec->AC_NAME;
								else
								$tarr['ac_name']="";
								$tarr['ps_list']=array();
								$summary['data'][$srec->st_code]['ac_list'][$srec->ac_no]=$tarr;
							}
							if(!isset($summary['data'][$srec->st_code]['ac_list'][$srec->ac_no]['ps_list'][$srec->ps_no]))
							{
								$tarr=array();
								$tarr['ps_no']=$srec->ps_no;
								$psdata=DB::connection('booth_revamp')->table('polling_station')->select('PS_NAME_EN','PS_NAME_V1')->where('ST_CODE',$srec->st_code)->where('AC_NO',$srec->ac_no)->where('PS_NO',$srec->ps_no)->first();
								if($psdata)
								{
									$tarr['ps_name_eng']=$psdata->PS_NAME_EN;
									$tarr['ps_name_reg']=$psdata->PS_NAME_V1;
									$summary['data'][$srec->st_code]['ac_list'][$srec->ac_no]['ps_list'][$srec->ps_no]=$tarr;
								}
							}
						}//$summary['data']=$stlist;
						$tdata=$summary['data'];
						$tsdata=array();
						foreach($tdata as $stdata)
						{
							$tstdata=array();
							$tstdata['state_code']=$stdata['state_code'];
							$tstdata['state_name_en']=$stdata['state_name_en'];
							$tstdata['state_name_hi']=$stdata['state_name_hi'];
							$tstdata['state_name_reg']=$stdata['state_name_reg'];
							$tstdata['ac_list']=array();
							foreach($stdata['ac_list'] as $tacdata)
							{
								$tsac=array();
								$tsac['ac_no']=$tacdata['ac_no'];
								$tsac['ac_name']=$tacdata['ac_name'];
								$tsac['ps_list']=array();
								foreach($tacdata['ps_list'] as $tpslist)
								{
									$tpsdata=array();
									$tpsdata['ps_no']=$tpslist['ps_no'];
									$tpsdata['ps_name_eng']=$tpslist['ps_name_eng'];
									$tpsdata['ps_name_reg']=$tpslist['ps_name_reg'];
									$tsac['ps_list'][]=$tpsdata;
								}
								$tstdata['ac_list'][]=$tsac;
							}
							$tsdata[]=$tstdata;
						}
						$summary['data']=$tsdata;
					}///EndSummary(success)
				}///EndEmptyToken
				return response()->json($summary, $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		public function ba_get_prepolldata(Request $request) 
		{
			try{
				
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);          
				} 
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				if(isset($userInputs['state_code']))
				$stcode=trim($userInputs['state_code']);
				else
				$stcode="";
				if(isset($userInputs['acno']))
				$acno=trim($userInputs['acno']);
				else
				$acno=0;
				
				if(isset($userInputs['psno']))
				$psno=trim($userInputs['psno']);
				else
				$psno=0;
				
				
				
				$summary=array();
				$summary['success'] = false;
				$summary['endpoint']="Booth App PRE Poll Data";
				$summary['message'] = "Login Failed";
				$summary['data'] = array();
				$ppres=array();
				$ppres['total_sm'] = 0;
				$ppres['na_sm'] = 0;
				$ppres['total_pro'] = 0;
				$ppres['na_pro'] = 0;
				$ppres['total_po'] = 0;
				$ppres['na_po'] = 0;
				$ppres['total_blo'] = 0;
				$ppres['na_blo'] = 0;
				$ppres['total_eroll_downloaded'] = 0;
				$ppres['infra_ramp'] = 0;
				$ppres['infra_toilet'] = 0;
				$ppres['infra_exit'] = 0;
				$ppres['infra_furniture'] = 0;
				$ppres['infra_elex'] = 0;
				$ppres['infra_water'] = 0;
				$ppres['mock_started'] = 0;
				$ppres['mock_shown'] = 0;
				$ppres['mock_cleared'] = 0;
				$ppres['mock_removed'] = 0;
				$ppres['ps_count'] = 0;
				$ppres['inactive_officers'] = array();
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						if($udata->accesstoken == $actoken)	
						{
							$summary['success'] = true;
							$summary['message'] = "";
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							$summary['success'] = false;
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
					}
					if($summary['success'])
					{
						
						$iquery=DB::connection('booth_revamp')->table('tbl_infra_mapping')->select("st_code","ac_no","ps_no","ramp","toilet_facility","exit_door","furniture","light","drinking_water");
						$psquery=DB::connection('booth_revamp')->table('tbl_poll_summary')->select("st_code","ac_no","ps_no");
						$buquery=DB::connection('booth_revamp')->table('tbl_booth_user')->select("st_code","ac_no","ps_no","download_time");
						$mpquery=DB::connection('booth_revamp')->table('tbl_mock_poll_status')->select("st_code","ac_no","ps_no","mock_poll_start","mock_poll_result_shown","button_clear","slip_remove");
						
						if($stcode!="")
						{
							$iquery=$iquery->where('st_code',$stcode);
							$psquery=$psquery->where('st_code',$stcode);
							$mpquery=$mpquery->where('st_code',$stcode);
							$buquery=$buquery->where('st_code',$stcode);
						}
						if($acno!=0)
						{
							$psquery=$psquery->where('ac_no',$acno);
							$iquery=$iquery->where('ac_no',$acno);
							$mpquery=$mpquery->where('ac_no',$acno);
							$buquery=$buquery->where('ac_no',$acno);
						}
						if($psno!=0)
						{
							$iquery=$iquery->where('ps_no',$psno);
							$psquery=$psquery->where('ps_no',$psno);
							$mpquery=$mpquery->where('ps_no',$psno);
							$buquery=$buquery->where('ps_no',$psno);
						}
						
						
						$psdata=$psquery->orderby('ac_no','ps_no')->get();
						$indata=$iquery->orderby('ac_no','ps_no')->get();
						$mpdata=$mpquery->orderby('ac_no','ps_no')->get();
						$budata=$buquery->orderby('ac_no','ps_no')->get();
						foreach($indata as $irec)
						{
							if($irec->ramp=='Y')
							$ppres['infra_ramp']++;
							if($irec->toilet_facility=='Y')
							$ppres['infra_toilet']++;
							if($irec->exit_door=='Y')
							$ppres['infra_exit']++;
							if($irec->furniture=='Y')
							$ppres['infra_furniture']++;
							if($irec->light=='Y')
							$ppres['infra_elex']++;
							if($irec->drinking_water=='Y')
							$ppres['infra_water']++;
							
						}
						foreach($psdata as $prec)
						{
							$podata=DB::table('polling_station_officer')->select("st_code","ac_no","ps_no","id","name","mobile_number","role_id","role_level","login_time")->where('st_code',$prec->st_code)->where('ac_no',$prec->ac_no)->whereRaw('FIND_IN_SET("'.$prec->ps_no.'",ps_no)')->orderby('st_code','ac_no','ps_no')->get();
							$ppres['ps_count']++;
							$psrec=DB::connection('booth_revamp')->table('polling_station')->select('PS_NAME_EN','PS_NAME_V1')->where('ST_CODE',$prec->st_code)->where('AC_NO',$prec->ac_no)->where('PS_NO',$prec->ps_no)->first();
							foreach($podata as $porec)
							{
								if(!$porec->login_time)
								{
									$ioff=array();
									$ioff['stcode']=$prec->st_code;
									$ioff['acno']=$prec->ac_no;
									$ioff['psno']=$prec->ps_no;
									$ioff['id']=$porec->id;
									$ioff['role_id']=$porec->role_id;
									$ioff['designation']="";
									if($porec->role_id == 33)
									{
										$ioff['designation']="BLO";
									}
									if($porec->role_id == 34)
									{
										$ioff['designation']="PO".$porec->role_level;
									}
									if($porec->role_id == 35)
									{
										$ioff['designation']="PRO";
									}
									if($porec->role_id == 38)
									{
										$ioff['designation']="SM";
									}
									$ioff['name']=$porec->name;
									$ioff['mobile']=$porec->mobile_number;
									$doflag=0;
									foreach($ppres['inactive_officers'] as $iapo)
									{
										if($iapo['id'] == $porec->id)
										$doflag=1;
									}
									if($doflag==0)
									
									
									if($psrec)
									$ioff['ps_details']=$psrec->PS_NAME_EN;
									else
									$ioff['ps_details']="";
									$ppres['inactive_officers'][]= $ioff;
								}
								if($porec->role_id ==38)
								{
									$ppres['total_sm']++;
									if(!$porec->login_time)
									$ppres['na_sm']++;	
									
								}
								if($porec->role_id ==35)
								{
									$ppres['total_pro']++;
									if(!$porec->login_time)
									$ppres['na_pro']++;
								}
								if($porec->role_id ==34)
								{
									$ppres['total_po']++;
									if(!$porec->login_time)
									$ppres['na_po']++;
								}
								if($porec->role_id ==33)
								{
									$ppres['total_blo']++;
									if(!$porec->login_time)
									$ppres['na_blo']++;
								}
							}
						}
						foreach($budata as $burec)
						{
							if($burec->download_time>0)
							$ppres['total_eroll_downloaded']++;
						}
						
						foreach($mpdata as $mprec)
						{
							if($mprec->mock_poll_result_shown == 'Y')
							$ppres['mock_shown']++;
							if($mprec->button_clear == 'Y')
							$ppres['mock_cleared']++;
							if($mprec->slip_remove == 'Y')
							$ppres['mock_removed']++;
							if($mprec->mock_poll_start == 'Y')
							$ppres['mock_started']++;
						}
						$summary['data']=$ppres;	
						
					}///EndSummary(success)
				}///EndEmptyToken
				return response()->json($summary, $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		public function ba_get_polldata(Request $request) 
		{
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);          
				} 
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				if(isset($userInputs['state_code']))
				$stcode=trim($userInputs['state_code']);
				else
				$stcode="";
				if(isset($userInputs['acno']))
				$acno=trim($userInputs['acno']);
				else
				$acno=0;
				
				if(isset($userInputs['psno']))
				$psno=trim($userInputs['psno']);
				else
				$psno=0;
				$summary=array();
				$summary['success'] = false;
				$summary['endpoint']="Booth App Poll Day Data";
				$summary['message'] = "Login Failed";
				$summary['data'] = array();
				$ppres=array();
				$ppres['poll_started'] = 0;
				$ppres['total_poll'] = 0;
				$ppres['poll_ended'] = 0;
				$ppres['connected'] = 0;
				$ppres['disconnected'] = 0;
				$ppres['incidents'] = 0;
				$ppres['interruptions'] = 0;
				$ppres['pwd_male'] = 0;
				$ppres['pwd_female'] = 0;
				$ppres['pwd_others'] = 0;
				$ppres['pwd_total'] = 0;
				$ppres['poll_male'] = 0;
				$ppres['poll_female'] = 0;
				$ppres['poll_others'] = 0;
				$ppres['poll_total'] = 0;
				$ppres['scan_qr'] = 0;
				$ppres['scan_epic'] = 0;
				$ppres['scan_slip'] = 0;
				$ppres['scan_name'] = 0;
				
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						if($udata->accesstoken == $actoken)	
						{
							$summary['success'] = true;
							$summary['message'] = "";
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							$summary['success'] = false;
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
					}
					if($summary['success'])
					{
						
						$iquery=DB::connection('booth_revamp')->table('tbl_incident_statistics')->select("incident_id");
						$psquery=DB::connection('booth_revamp')->table('tbl_poll_summary')->select("st_code","ac_no","ps_no","poll_start_datetime","poll_end_datetime","total_turn_out","total_male_turn_out","total_female_turn_out","total_other_turn_out","scan_qr","scan_epicno","scan_name","scan_srno","updated_at");
						$date = now();
						$date->modify('-15 minutes');
						$formatted_date = $date->format('Y-m-d H:i:s');
						if($stcode!="")
						{
							$iquery=$iquery->where('st_code',$stcode);
							$psquery=$psquery->where('st_code',$stcode);
							
						}
						if($acno!=0)
						{
							$psquery=$psquery->where('ac_no',$acno);
							$iquery=$iquery->where('ac_no',$acno);
							
						}
						if($psno!=0)
						{
							$iquery=$iquery->where('ps_no',$psno);
							$psquery=$psquery->where('ps_no',$psno);
							
						}
						if( $stcode != "" && $acno != 0  && $psno!=0 )
						$pwddata=DB::connection('booth_revamp')->select('SELECT count("M.id") as cnt, M.gender, M.epic_no FROM tbl_voter_info_poll_status M Right Join tbl_voter_pwd P ON M.epic_no = P.epic_no where( ((M.gender = "M") or ( M.gender = "F") or (M.gender = "T")) and ( M.st_code = "'.$stcode.'") and ( M.ac_no = '.$acno.') and ( M.ps_no = "'.$psno.'")) group by M.gender ') ;
						elseif($stcode != "" && $acno != 0 )
						$pwddata=DB::connection('booth_revamp')->select('SELECT count("M.id") as cnt, M.gender, M.epic_no FROM tbl_voter_info_poll_status M Right Join tbl_voter_pwd P ON M.epic_no = P.epic_no where( ((M.gender = "M") or ( M.gender = "F") or (M.gender = "T")) and ( M.st_code = "'.$stcode.'") and ( M.ac_no = '.$acno.')) group by M.gender ') ;
						elseif($stcode != "")
						$pwddata=DB::connection('booth_revamp')->select('SELECT count("M.id") as cnt, M.gender, M.epic_no FROM tbl_voter_info_poll_status M Right Join tbl_voter_pwd P ON M.epic_no = P.epic_no where( ((M.gender = "M") or ( M.gender = "F") or (M.gender = "T")) and ( M.st_code = "'.$stcode.'")) group by M.gender ') ;
						else
						$pwddata=DB::connection('booth_revamp')->select('SELECT count("M.id") as cnt, M.gender, M.epic_no FROM tbl_voter_info_poll_status M Right Join tbl_voter_pwd P ON M.epic_no = P.epic_no where((M.gender = "M") or ( M.gender = "F") or (M.gender = "T")) group by M.gender ') ;
						
						$indata=$iquery->orderby('ac_no','ps_no')->get();
						$psdata=$psquery->orderby('ac_no','ps_no')->get();
						
						foreach($psdata as $prec)
						{
							if($prec->poll_start_datetime == NULL)
							$ppres['poll_started']++;
							$ppres['total_poll']++;
							if($prec->poll_end_datetime == NULL)
							$ppres['poll_ended']++;
							if($prec->updated_at >= $formatted_date)
							$ppres['connected']++;
							else
							$ppres['disconnected']++;
							$ppres['poll_male']+= $prec->total_male_turn_out;
							$ppres['poll_female']+= $prec->total_female_turn_out;
							$ppres['poll_others']+= $prec->total_other_turn_out;
							$ppres['poll_total']+= $prec->total_turn_out;
							$ppres['scan_qr']+= $prec->scan_qr;
							$ppres['scan_epic']+= $prec->scan_epicno;
							$ppres['scan_slip']+= $prec->scan_srno;
							$ppres['scan_name']+= $prec->scan_name;
						}
						$ppres['incidents'] = count($indata);
						foreach($pwddata as $pwrec)
						{
							if($pwrec->gender = "M")
							$ppres['pwd_male'] = $pwrec->cnt;
							elseif($pwrec->gender = "F")
							$ppres['pwd_female'] = $pwrec->cnt;
							else
							$ppres['pwd_others'] = $pwrec->cnt;
							
							$ppres['pwd_total'] += $pwrec->cnt;
						}
						$summary['data']=$ppres;	
						
					}///EndSummary(success)
				}///EndEmptyToken
				
				return response()->json($summary, $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		public function ba_get_pollturnout(Request $request) 
		{
			try{
				$validator = Validator::make($request->all(), [
				'user_id' => 'required','ac_token' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);          
				} 
				$userInputs = $request->all();
				$actoken=trim($userInputs['ac_token']);
				$userid=trim($userInputs['user_id']);
				if(isset($userInputs['state_code']))
				$stcode=trim($userInputs['state_code']);
				else
				$stcode="";
				if(isset($userInputs['acno']))
				$acno=trim($userInputs['acno']);
				else
				$acno=0;
				
				if(isset($userInputs['psno']))
				$psno=trim($userInputs['psno']);
				else
				$summary=array();
				$summary['success'] = false;
				$summary['endpoint']="Booth App Poll Turnout";
				$summary['message'] = "Login Failed";
				$summary['data'] = array();
				$ppres=array();
				$ppres['turnout'] = 0;
				$ppres['ps_count'] = 0;
				$ppres['pswise']=array();
				$telec=0;
				$tvoter=0;
				$qsize=0;
				if(!empty($userid) && !empty($actoken))
				{
					$udata=DB::table('officer_login')->where('id',$userid)->first();
					if(isset($udata->officername))
					{
						if($udata->accesstoken == $actoken)	
						{
							$summary['success'] = true;
							$summary['message'] = "";
						}
						else
						{
							$summary['message'] = "Access Token Mismatched";
							$summary['success'] = false;
						}
					}
					else
					{
						$summary['message'] = "User_id not found";
					}
					if($summary['success'])
					{
						
						$psquery=DB::connection('booth_revamp')->table('tbl_poll_summary')->select("st_code","ac_no","ps_no","total_turn_out","total_male_turn_out","total_female_turn_out","total_other_turn_out","electors","blo_turn_out","pro_turn_out");
						if($stcode!="")
						$psquery=$psquery->where('st_code',$stcode);
						if($acno!=0)
						$psquery=$psquery->where('ac_no',$acno);
						if($psno!=0)
						$psquery=$psquery->where('ps_no',$psno);
						
						$psdata=$psquery->orderby('ac_no','ps_no')->get();
						
						foreach($psdata as $prec)
						{
							$ppres['ps_count']++;
							$tarr=array();
							$tarr['ps_no']=$prec->ps_no;
							$psrec=DB::connection('booth_revamp')->table('polling_station')->select('PS_NAME_EN','PS_NAME_V1')->where('ST_CODE',$prec->st_code)->where('AC_NO',$prec->ac_no)->where('PS_NO',$prec->ps_no)->first();
							if($psrec)
							$tarr['ps_details']=$psrec->PS_NAME_EN;
							else
							$tarr['ps_details']="";
							$gdata=DB::connection('booth_revamp')->select('SELECT count("M.id") as cnt, M.gender FROM tbl_voter_info M  where( ( M.st_code = "'.$prec->st_code.'") and ( M.ac_no = '.$prec->ac_no.') and ( M.ps_no = "'.$prec->ps_no.'")) group by M.gender ') ;
							foreach($gdata as $grec)
							{
								if($grec->gender == "M")
								$tarr['male']=$grec->cnt;
								elseif($grec->gender == "F")
								$tarr['female']=$grec->cnt;
								else
								$tarr['others']=$grec->cnt;
							}
							$tarr['male_tot']=$prec->total_male_turn_out;
							$tarr['female_tot']=$prec->total_female_turn_out;
							$tarr['others_tot']=$prec->total_other_turn_out;
							$tarr['queue']=$prec->blo_turn_out - $prec->pro_turn_out;;
							$tarr['percent']=number_format(($prec->total_turn_out * 100)/ $prec->electors,2,".","");
							$telec+=$prec->electors;
							$tvoter+=$prec->total_turn_out;
							$ppres['pswise'][]=$tarr;
						}
						$ppres['turnout']=number_format(($tvoter * 100)/ $telec,2,".","");
						$summary['data']=$ppres;	
						
					}///EndSummary(success)
				}///EndEmptyToken
				
				return response()->json($summary, $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		public function vha_pollturnout(Request $request) 
		{
			try{
				$validator = Validator::make($request->all(), [
				'stcode' => 'required','acno' => 'required',
				]);
				
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);          
				} 
				$userInputs = $request->all();
				$stcode=trim($userInputs['stcode']);
				$acno=trim($userInputs['acno']);
				
				$summary=array();
				$summary['success'] = false;
				$summary['message']="BoothApp Poll Turnout for VoterHelpline";
				$summary['success'] = false;
				$summary['st_code'] = "";
				$summary['st_name'] = "";
				$summary['acno'] = "";
				$summary['ac_name'] = "";
				$summary['electors'] = 0;
				$summary['voters'] = 0;
				$summary['turnout'] = 0;
				
				$telec=0;
				$tvoter=0;
				$psdata=DB::connection('booth_revamp')->table('tbl_poll_summary')->select("st_code","ac_no","ps_no","total_turn_out","electors")->where('st_code',$stcode)->where('ac_no',$acno)->get();
				
				if(count($psdata)>0)
				{
					$summary['success'] = true;
					foreach($psdata as $prec)
					{
						$telec+=$prec->electors;
						$tvoter+=$prec->total_turn_out;
					}
					
					$summary['st_code'] = $stcode;
					$summary['st_name'] = $this->commonModel->getstatebystatecode($stcode)->ST_NAME;
					$summary['acno'] = $acno;
					$acrec=$this->commonModel->getacbyacno($stcode,$acno);
					if($acrec)
					$summary['ac_name'] =$acrec->AC_NAME;
					$summary['electors'] = $telec;
					$summary['voters'] = $tvoter;
					$summary['turnout']=number_format(($tvoter * 100)/ $telec,2,".","");
				}
				
				
				return response()->json($summary, $this->successStatus);
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		public function AcPT(Request $request) {
			
			try{
				$validator = Validator::make($request->all(), [
				'electiontype' => 'required',
				'electionphase' => 'required',
				
				'statecode' => 'required',
				'acno' => 'required',
				]);
				//'election_id' => 'required',
				if($validator->fails()){
					return response()->json($validator->errors(), $this->notfoundStatus);             
				} 
				
				$userInputs = $request->all();
				$scheduleid = trim($userInputs['electionphase']);
				$electiontypeid = trim($userInputs['electiontype']);
				$stcode=trim($userInputs['statecode']);
				$acno=trim($userInputs['acno']);
				
				//print_r($pdilist);die;
				$summary=array();
				$eldata=$this->commonModel->getecctionBYid($electiontypeid);
				//print_r($eldata);
				$summary['success'] = true;
				$summary['message'] = "Poll TurnOut ACwise";
				$summary['phase'] = $scheduleid;
				$esubtype=$eldata->election_type;
				$result=array();
				
				$stlist= DB::table('pd_scheduledetail')->select("st_code","ac_no","electors_total","est_voters")->where('st_code', $stcode)->where('ac_no', $acno)->first();
				$summary['acno']=$acno;
				$acrec=$this->commonModel->getacbyacno($stcode,$acno);
				if($acrec)
				$summary['ac_name']=$acrec->AC_NAME;
				else
				$summary['ac_name']="";
				$summary['st_code']=$stcode;
				$summary['st_name']=$this->commonModel->getstatebystatecode($stcode)->ST_NAME;
				$summary['electors']=	$telec = $stlist->electors_total;
				$summary['voters']=	$tvoter = $stlist->est_voters;
				$summary['turnout']= 0;
				if(($tvoter>0) && ($telec>0))
				$summary['turnout']=number_format(($tvoter * 100)/ $telec,2,".","");
				$tempdata = DB::table('pd_scheduledetail')->orderBy('updated_at','DESC')->first();
				$summary['last_update_time']=$tempdata->updated_at;
				return response()->json($summary, $this->successStatus);
				
			}
			catch (Exception $ex) 
			{
				return response()->json(['success' => false,'error'=>'Internal Server Error'], $this->intservererrorStatus);
			}
			
		}
		
		
		
	}																							