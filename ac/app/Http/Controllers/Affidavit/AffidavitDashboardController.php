<?php

namespace App\Http\Controllers\Affidavit;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Session;
use App\models\States;
use App\models\Districts;
use App\models\Nomination\ProfileModel;
use App\models\Affidavit\AffCandDetail;
use App\models\Affidavit\AffCashInHand;
use App\models\Affidavit\AffPanDetail;
use App\models\Affidavit\MParty;
use App\models\Affidavit\AffMCandRelation;
use App\models\Affidavit\AffRelationTypeModel;
use App\models\Affidavit\AffSocialMedia;
use App\models\Affidavit\AffCandSocialMedia;
use App\models\Affidavit\AffPendingCriminalCase;
use App\models\Affidavit\AffImprisonmentCriminalCase;
use App\models\Nomination\NominationApplicationModel;
use Carbon\Carbon;
use App\Classes\xssClean;
use DB;
use Auth;
use Response;
use File;
use App;

use App\commonModel;
use App\adminmodel\CandidateModel;
use App\Helpers\LogNotification;

class AffidavitDashboardController extends Controller
{   
    public $successStatus = 200;


	public function __construct(){   
        $this->commonModel = new commonModel();
		$this->CandidateModel = new CandidateModel();
    }

    public function AffidavitEFile(){
		
        Session::forget('affidavit_id');
        return view('affidavit.affidavit-e-file');
    }

    public function MyAffidavit(){
		
		
        $getcan_details = AffCandDetail::where('candidate_id',Auth::user()->id)->whereNull('finalized')->orderBy('id','DESC')->get();

        $affidavit_finalized = AffCandDetail::where('candidate_id',Auth::user()->id)->where('finalized',1)->orderBy('id','DESC')->get();

        return view('affidavit.my-affidavit',['getcan_details'=>$getcan_details,'affidavit_finalized'=>$affidavit_finalized]);
    }

    public function AffidavitDashboard(Request $request, $Id=""){
		
		
        if (Auth::check()):

        	$Id=json_decode(base64_decode($Id), true);
        	 //$Id=  Crypt::decrypt($Id);
		
			$getData = AffCandDetail::find($Id);
			
				if($Id){					
					Session::put('affidavit_id',$getData->affidavit_id);
					$user_profile_data = ProfileModel::where('candidate_id',$getData->candidate_id)->first();
				}else{
					$user_profile_data = ProfileModel::where('candidate_id', Auth::id())->first();
				}
				
				if(Session::get('affidavit_id')){					
					$cand_data = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();
					$user_profile_data = ProfileModel::where('candidate_id',$cand_data->candidate_id)->first();
				}else{
					$user_profile_data = ProfileModel::where('candidate_id', Auth::id())->first();
				}
				
		
            $getState = DB::table('m_state')->orderBy('m_state.ST_NAME','ASC')->get();
						

			if(!empty($user_profile_data->epic_no)):
			
				$session_data = array();
				if(Session::get('affidavit_id')){
					$session_data = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();
					if($session_data->finalized == '1'  && Auth::user()->role_id != '19' ){
						return redirect()->to('part-a-detailed-report')->with('success','Your Affidavit is allready Finalized.');
					}
					
				}else{
					$session_data = DB::table('nomination_application')->where('candidate_id',Auth::user()->id)->first();
				}
				
				if(Auth::user()->role_id == '19'){
					$action = 'roac/InitialDetails';
				}else{
					$action = 'InitialDetails';
				}
				
				
				$data  = [];
				$user = Auth::user();
				$d=$this->commonModel->getunewserbyuserid($user->id);
				$data['user_data']=$d;
				
				//dd($data);
				
								
    	    	return view('affidavit.affidavitdashboard',['getState'=>$getState,'getData'=>$getData,'user_profile_data'=>$user_profile_data,'session_data'=>$session_data,'action' => $action,'data' => $data]);
				
			else:		
				return redirect()->to('nomination/apply-nomination-step-1');	
            endif;        
	    else:
		
	    	Auth::logout();
	    endif;	
    }

    public function InitialDetails( Request $request ){
		
//dd('11');
    	try {
        $xss = new xssClean;
        $inp = $request->all();
        request()->validate([
            'candidate_name'=>'required|max:100',
            'state_name'=>'required',
            //'district_name'=>'required',
            'ac_name'=>'required',
            ]);
				
				

        if(!empty(Auth::user())):
		
			$checkr = app(App\Http\Controllers\Nomination\NominationController::class)->getdateNom($inp['state_name'], $inp['ac_name']);
		
			//dd($checkr);
		
			if($checkr == 1){
				return redirect()->back()->with('error','Nomination closed for this Constituency.');
			}
		
		
            if(empty(Session::get('affidavit_id'))){
				
				//dd(session()->get('locale'));

                $matchId = 0;
                $get_can_Data = AffCandDetail::max('id');
                if($get_can_Data){
                    $matchId = $get_can_Data+1;
                } else {
                    $count = 1;
                    $matchId = $matchId + $count;
                }
                $generate_affidavit_no = 'AFF'.$inp['state_name'].'A'.$inp['ac_name'].'000'.$matchId;
				
                $inser_cand_Data = new AffCandDetail;
                $inser_cand_Data->candidate_id = Auth::user()->id;
                $inser_cand_Data->affidavit_id = $generate_affidavit_no;
                $inser_cand_Data->election_id = '0';
                $inser_cand_Data->modified_on = Carbon::now();
                $inser_cand_Data->usertype = $xss->clean_input("Candidate");
                $inser_cand_Data->name_on_epic = $xss->clean_input($inp['name_on_epic']);
                $inser_cand_Data->cand_name = $xss->clean_input($inp['candidate_name']);
                $inser_cand_Data->st_code = $xss->clean_input($inp['state_name']);
               // $inser_cand_Data->dist_no = $xss->clean_input($inp['district_name']);
                $inser_cand_Data->ac_no = $xss->clean_input($inp['ac_name']);
                $inser_cand_Data->affidavit_language = ((session()->get('locale'))?session()->get('locale'):'en-US');
                $inser_cand_Data->save();

                $get_affidavit_Id = AffCandDetail::select('affidavit_id')->where('id',$inser_cand_Data->id)->first();
                if(!empty($get_affidavit_Id->affidavit_id))
                {
                $insert_cash_Data = new AffCashInHand;
                $insert_cash_Data->affidavit_id = $get_affidavit_Id->affidavit_id;
                $insert_cash_Data->candidate_id = $xss->clean_input(Auth::user()->id);
                $insert_cash_Data->relation_type_code = '1';
                $insert_cash_Data->modified_on = Carbon::now();
                $insert_cash_Data->save();
            }
				
				$user_profile_data = ProfileModel::where('candidate_id', Auth::id())->first();
				
				
				//dd($user_profile_data->pan_number);

				$decoded = $encoded = '';

				$encoded = @$user_profile_data->pan_number;
				$decoded = "";
				 for( $i = 0; $i < strlen($encoded); $i++ ) {
					$b = ord($encoded[$i]);
					$a = $b ^ 123;
					$decoded .= chr($a);
				}
				 

                 if(!empty($get_affidavit_Id->affidavit_id))
                {
                $insert_pan_Data = new AffPanDetail;
                $insert_pan_Data->affidavit_id = $get_affidavit_Id->affidavit_id;
                $insert_pan_Data->candidate_id = Auth::user()->id;
                $insert_pan_Data->name = $xss->clean_input($inp['candidate_name']);
                $insert_pan_Data->relation_type_code = '1';
                $insert_pan_Data->relation_code = 'sf';
                $insert_pan_Data->pan = $decoded;
                $insert_pan_Data->modified_on = Carbon::now();
                $insert_pan_Data->save();
            

                /* set the affidavit session */
                Session::put('affidavit_id',$get_affidavit_Id->affidavit_id);
                /* end */
            }
                
            } else { // end of the empty session //
							
                $inser_cand_Data = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();
                $inser_cand_Data->modified_on = Carbon::now();
                $inser_cand_Data->name_on_epic = $xss->clean_input($inp['name_on_epic']);
                $inser_cand_Data->cand_name = $xss->clean_input($inp['candidate_name']);
                $inser_cand_Data->st_code = $xss->clean_input($inp['state_name']);
                //$inser_cand_Data->dist_no = $xss->clean_input($inp['district_name']);
                $inser_cand_Data->ac_no = $xss->clean_input($inp['ac_name']);
                $inser_cand_Data->save();

            }
            Session::put('cand_name',$xss->clean_input($inp['candidate_name']));
            Session::put('state_code',$xss->clean_input($inp['state_name']));
            //Session::put('district_no',$xss->clean_input($inp['district_name']));
            Session::put('ac_no',$xss->clean_input($inp['ac_name']));
            Session::put('TblId',$inser_cand_Data->id);

            Session::put('st_code_by_epic',$xss->clean_input($inp['st_code_by_epic']));
            Session::put('st_name_by_epic',$xss->clean_input($inp['st_name_by_epic']));

            Session::put('dist_no_by_epic',$xss->clean_input($inp['dist_no_by_epic']));
            Session::put('dist_name_by_epic',$xss->clean_input($inp['dist_name_by_epic']));

            Session::put('ac_no_by_epic',$xss->clean_input($inp['ac_no_by_epic']));
            Session::put('ac_name_by_epic',$xss->clean_input($inp['ac_name_by_epic']));

            Session::put('part_number_by_epic',$xss->clean_input($inp['part_number_by_epic']));
            Session::put('serial_no_by_epic',$xss->clean_input($inp['serial_no_by_epic']));

		
			if(Auth::user()->role_id == '19'){
				return redirect()->to('roac/affidavit/candidatedetails')->with('Init','Initial Details has been successfully saved');
			}else{
				return redirect()->to('affidavit/candidatedetails')->with('Init','Initial Details has been successfully saved');
			}
		
		
            
        else:
            return redirect()->route('affidavit.dashboard')->with('Init','your request is not completed.');
        endif;


    }catch (Exception $e) 
	    	{

                 return redirect()->route('affidavit.dashboard')->with('Init','your request is not completed.');
           }


    }


	public function upload_files(Request $request){
		if(!Session::has('affidavit_id')){
		  return Response::json([
			'success' => false,
			'errors' => "Please referesh and try again.",
		  ]);
		}
		$id = Session::get('affidavit_id');
		$user_nomination = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();
		if(!$user_nomination){
		  return Response::json([
			'success' => false,
			'errors' => "Please referesh and try again.",
		  ]);
		}
		
		
		    $destination_path = '';
				$destination_path = 'affidavit/uploads';
				
				//dd($destination_path);
				
				//$common = new \App\Http\Controllers\Common\CandidatOnlineNomination();
				
				$results = $this->upload($request, 2048, 'image', $destination_path);
				
			// dd($results);
		
		return $results;
  }


    public function CandidateDetails(Request $request){
		
		if(Session::get('affidavit_id')){
			$data = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();
			if($data->finalized == '1'  && Auth::user()->role_id != '19'){
				return redirect()->to('part-a-detailed-report')->with('success','Your Affidavit is allready Finalized.');
			}
		}else{
			return redirect()->to('affidavitdashboard')->with('success','Please select State and constituency.');
		}
		
		
        // dd(Session::get('affidavit_id'));
        if (Auth::check()):
            $party_name = MParty::whereIn('PARTYTYPE',['S','U','N'])->orderBy('PARTYNAME')->get();
            $statename = States::orderBy('ST_NAME')->get();
            $get_relation_type = AffRelationTypeModel::whereNotIn('id',[1])->orderBy('id','ASC')->get();
            $get_cand_relation = AffMCandRelation::whereNotIn('id',[1])->orderBy('id','ASC')->get();
            $get_cand_relation_self = AffMCandRelation::orderBy('id','ASC')->get();
            $get_social_media = AffSocialMedia::orderBy('id','ASC')->get();
            $cand_social_account = AffCandSocialMedia::where('affidavit_id',Session::get('affidavit_id'))->orderBy('id','DESC')->get();

            $get_candidate_detail_data = AffPanDetail::leftjoin("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
                    ->leftjoin("aff_m_cand_relation", "aff_pan_details.relation_code", "=", "aff_m_cand_relation.relation_code")
                    ->select("aff_pan_details.*", "aff_m_relation_type.relation_type", "aff_m_cand_relation.relation")
                    //->where('candidate_id',Auth::user()->id)
                    ->where('affidavit_id',Session::get('affidavit_id'))
                    ->where('aff_pan_details.relation_type_code','!=','1')
                    ->where('aff_pan_details.relation_code','!=','sf')
					->where('is_deleted','0')
                    ->orderBy('aff_pan_details.id','ASC')
                    ->get();

            $getPANData = AffPanDetail::leftjoin("aff_m_relation_type", "aff_pan_details.relation_type_code", "=", "aff_m_relation_type.relation_type_code")
                    ->leftjoin("aff_m_cand_relation", "aff_pan_details.relation_code", "=", "aff_m_cand_relation.relation_code")
                    ->select("aff_pan_details.*", "aff_m_relation_type.relation_type", "aff_m_cand_relation.relation")
                    //->where('candidate_id',Auth::user()->id)
					->where('is_deleted','0')
                    ->where('affidavit_id',Session::get('affidavit_id'))
                    ->orderBy('aff_pan_details.id','ASC')
                    ->get();
            // dd($getPANData);

            $user_profile_data = ProfileModel::where('candidate_id', $data->candidate_id)->first();

            $session_data = array();
			$nomination_data = array();
            if(Session::get('affidavit_id')){
                $session_data = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();			
				if(@$session_data->partytype){				
					$session_data['partyData'] = MParty::where('PARTYTYPE',$session_data->partytype)->get();
				}else{
					$session_data['partyData'] = array();
					$nomination_data = NominationApplicationModel::where('candidate_id',$data->candidate_id)->first();
					if(@$nomination_data->party_id){				
						$nomination_data['PARTYTYPE'] = MParty::where('CCODE',@$nomination_data->party_id)->first();
						
						$nomination_data['partyData'] = MParty::where('PARTYTYPE',$nomination_data['PARTYTYPE']->PARTYTYPE)->get();
					}else{
						$nomination_data['partyData'] = array();
					}
				}
            }
			
			if(Auth::user()->role_id == '19'){
					$action = 'roac/save-candidate-details';
				}else{
					$action = 'save-candidate-details';
				}


			//dd($nomination_data['partyData']);
			
			$data  = [];
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			$data['user_data']=$d;
			
			//dd($data);
			
        	return view('affidavit.affidavit_candidate_details',['user_data'=>$d,'party_name'=>$party_name,'statename'=>$statename,'get_relation_type'=>$get_relation_type,'get_cand_relation'=>$get_cand_relation,'get_social_media'=>$get_social_media,'get_candidate_detail_data'=>$get_candidate_detail_data, 'getPANData'=>$getPANData,'cand_social_account'=>$cand_social_account,'user_profile_data'=>$user_profile_data,'get_cand_relation_self'=>$get_cand_relation_self,'session_data'=>$session_data,'nomination_data'=>$nomination_data,'action'=>$action]);
        else:
            //Auth::logout();
        endif;    
    }

    function AffidavitAddData(Request $request){
        if($request->ajax()){
            $xss = new xssClean;
            $relation_type  = $request->relation_type;
            $relation       = $request->relation;
            $name           = $request->name;

            $insert_cash_Data = new AffCashInHand;
            $insert_cash_Data->affidavit_id = Session::get('affidavit_id');
            $insert_cash_Data->candidate_id = Auth::user()->id;
            $insert_cash_Data->relation_type_code = $xss->clean_input($relation_type);
            $insert_cash_Data->modified_on = Carbon::now();
            $insert_cash_Data->save();
            
            $insert_pan_Data = new AffPanDetail;
            $insert_pan_Data->affidavit_id = Session::get('affidavit_id');
            $insert_pan_Data->candidate_id = Auth::user()->id;
            $insert_pan_Data->relation_type_code = $xss->clean_input($relation_type);
            $insert_pan_Data->name = $xss->clean_input($name);
            $insert_pan_Data->relation_code = $xss->clean_input($relation);
            $insert_pan_Data->modified_on = Carbon::now();
            $insert_pan_Data->save();

            $user_profile_data = ProfileModel::select('pan_number')->where('candidate_id', Auth::id())->first();

            //AffPanDetail::where(['relation_type_code'=>1,'relation_code'=>'sf'])->update(['pan' => $xss->clean_input($user_profile_data->pan_number)]);

            if(!empty($insert_pan_Data)):
                return response()->json(['error'=>false, 'status' =>$this->successStatus,'result'=>$insert_pan_Data->id]);
            else:
                $msg = "Something went wrong Please try again";
                return response()->json(['error'=>false, 'status' =>401,'msg'=>$msg]);
            endif;

        } else {

        }
    }    

    public function AffidavitSocialMediaData(Request $request){
        $xss = new xssClean;
        if($request->ajax()){
            $social_media_code   = $request->social_media;
            $social_account = $request->social_account;
            $media_name = $request->social_name;
            $social_media_code = strtolower($social_media_code);

            $dataSave = new AffCandSocialMedia;
            $dataSave->affidavit_id = Session::get('affidavit_id');
            $dataSave->candidate_id = Auth::user()->id;
            $dataSave->social_media_code = $social_media_code;
            $dataSave->other_account_name = $social_account;
            $dataSave->media_account = $media_name;
            $dataSave->modified_on = Carbon::now();
            $dataSave->save();

            $data_save = AffCandSocialMedia::select('id')->where('id',$dataSave->id)->where('affidavit_id',Session::get('affidavit_id'))->first();

            if(!empty($data_save)):
                return response()->json(['error'=>false, 'status' =>$this->successStatus,'result'=>$data_save->id]);
            else:
                $msg = "Something went wrong Please try again";
                return response()->json(['error'=>false, 'status' =>401,'msg'=>$msg]);
            endif;    
        }
    }

    public function UpdatePersonalDetails(Request $request){
		
		
		//dd('aaaaa');
		
        $xss = new xssClean;
        if($request->ajax()){
            $tblPersonaId = $request->tblPersonaId;
            $other_account_update = $request->relation_code_update;
            $social_media_code = $request->relation_type_update;
            $media_account = $request->name_update;
            // dd($other_account_update . $social_media_code . $media_account);

            $updateCashInHand = AffCashInHand::where('id',$tblPersonaId)->update([
                            'relation_type_code' => $xss->clean_input($social_media_code),
                            'modified_on' => $xss->clean_input($media_account),
                        ]);

            $updatePanDetail = AffPanDetail::where('id',$tblPersonaId)->update([
                            'relation_type_code' => $xss->clean_input($social_media_code),
                            'name' => $xss->clean_input($media_account),
                            'relation_code' => $xss->clean_input($other_account_update),
                            'modified_on' => $xss->clean_input($media_account),
                        ]);

            if($updateCashInHand && $updatePanDetail):
                $msg = "Record has been successfully updated";
                return response()->json(['error'=>false, 'status' =>$this->successStatus,'msg'=>$msg]);
            else:
                $msg = "Something went wrong Please try again.";    
                return response()->json(['error'=>false, 'status' =>401,'msg'=>$msg]);
            endif;
        }
    }    

    public function UpdateSocialMedia(Request $request){
        $xss = new xssClean;
        if($request->ajax()){
            $tblId = $request->tblId;
            $other_account_update = $request->other_account_update;
            $social_media_code = $request->social_media_code;
            $media_account = $request->media_account;

            $updateSocial = AffCandSocialMedia::where('id',$tblId)->update([
                            'social_media_code' => $xss->clean_input($social_media_code),
                            'media_account' => $xss->clean_input($media_account),
                            'other_account_name' => $xss->clean_input($other_account_update),
                        ]);

            if($updateSocial):
                $msg = "Record has been successfully updated";
                return response()->json(['error'=>false, 'status' =>$this->successStatus,'msg'=>$msg]);
            else:
                $msg = "Something went wrong Please try again.";    
                return response()->json(['error'=>false, 'status' =>401,'msg'=>$msg]);
            endif;
        }
    }

    public function UpdatePANDetails( Request $request ){
        $xss = new xssClean;
        if($request->ajax()){
            $PANtblId           = $request->PANtblId;
            $pan_name           = $request->pan_name;
            $relation_code      = $request->relation_code;
            $pan                = $request->pan;
            $financial_year     = $request->financial_year;
            //$total_income_shown = base64_decode($request->total_income_shown);
            $financialyr1       = $request->financialyr1;
            $financialyr2       = $request->financialyr2;
            $financialyr3       = $request->financialyr3;
            $financialyr4       = $request->financialyr4;
            $financialyr5       = $request->financialyr5;

            $updatePAN = AffPanDetail::where('id',$PANtblId)->update([
                'name' => $xss->clean_input($pan_name),
                'relation_code' => $xss->clean_input($relation_code),
                'pan' => $xss->clean_input($pan),
                'financial_year' => $xss->clean_input($financial_year),
                //'total_income_shown' => $xss->clean_input($total_income_shown),
                'financialyr1' => $xss->clean_input($financialyr1),
                'financialyr2' => $xss->clean_input($financialyr2),
                'financialyr3' => $xss->clean_input($financialyr3),
                'financialyr4' => $xss->clean_input($financialyr4),
                'financialyr5' => $xss->clean_input($financialyr5),
                'modified_on'  => Carbon::now(),
            ]);
            if($updatePAN):
                $msg = "Record has been successfully updated";
                return response()->json(['error'=>false, 'status' =>$this->successStatus,'msg'=>$msg]);
            else:
                $msg = "Something went wrong Please try again.";    
                return response()->json(['error'=>false, 'status' =>401,'msg'=>$msg]);
            endif;
        }
    }
	
	
	public function delete_spouse(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffOtherImmovableAssets::destroy($request->id);
				AffPanDetail::where('id',$request->id)->update(['is_deleted' => '1']);
	        	echo 1;
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    	}
    	else 
    		echo 0;
    }

    public function SaveCandidateDetails(Request $request){
		
		
		//dd($request->all());

		$message=array();
        $user=Auth::user();
		$nomID=(Session::get('nomination_id'));
        $message['MobNo']= $user->mobile ?? '';
        $message['applicationType']= 'WebApp';
         $message['Module']= 'SUVIDHA';
        $message['TransectionType']= 'Online Nomination';
        $message['TransectionAction']= 'Affidavit Candidate Details ';
        if(isset($nomID) && !empty($nomID))
        {
        $get_nominattion_detail = NominationApplicationModel::get_nomination_application($nomID); 
	    $nomination_num= ' Nomination_Num: '.$get_nominattion_detail['nomination_no'];
	    }else{
	    	$nomination_num='';
	    }
		
        $xss = new xssClean;
        $fileName = $request->userImage;
		

        if($request->dateofborth_remember==1){
            $request->validate([
                'date_of_birth' => 'required',
            ]);
            $date_of_bith = explode('-', $request->date_of_birth);
            $date_of_bith = $date_of_bith[2].'-'.$date_of_bith[1].'-'.$date_of_bith[0];    
        } elseif($request->dateofborth_remember==2) {
            $request->validate([
                'age' => 'required',
            ]);
            $date_of_bith = '';
        } else {
            $date_of_bith = '';
        }
    
            $request->validate([
                'son_daughter_wife_of' => 'required',
                'postal_address'=> 'required|max:200',
                'mobile_number'=>'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10'
            ]);

        if($request->candidate_setup_party==1){
            $request->validate([
                'party_type' => 'required',
                'political_party' => 'required', 
            ]);
        }

        if($request->email_account == 1){
            $request->validate([
                'email_address' => 'required|email',
            ]);
        }


        /*$get_can_Data = AffCandDetail::select('st_code','ac_no_by_epic')->where('id',$request->TblId)->first();
        $generate_affidavit_no = 'AFF'.$get_can_Data->st_code.'A'.$get_can_Data->ac_no.$request->TblId;*/
        // dd($generate_affidavit_no);
        $update_candidate = AffCandDetail::FirstOrNew(['affidavit_id'=>$request->TblId]);
        // $update_candidate->affidavit_id = $generate_affidavit_no;
        $update_candidate->relation_type_of = '1';
        $update_candidate->son_daughter_wife_of = $xss->clean_input($request->son_daughter_wife_of);
        $update_candidate->relation_name = $xss->clean_input($request->relation_name);
        $update_candidate->dob = $xss->clean_input($date_of_bith);
        $update_candidate->age = $xss->clean_input($request->age);
        $update_candidate->postal_address = $xss->clean_input($request->postal_address);
        $update_candidate->partytype = $xss->clean_input($request->party_type);
        $update_candidate->partyabbre = $xss->clean_input($request->political_party);
        $update_candidate->election_id = '0';
        $update_candidate->state_enrolled = $xss->clean_input($request->st_code_from_epic);
        $update_candidate->dist_no_enrolled = $xss->clean_input($request->district_code_from_epic);
        $update_candidate->constituency_enrolled = $xss->clean_input($request->ac_code_from_epic);
        $update_candidate->serial_no_enrolled = $xss->clean_input($request->serial_no);
        $update_candidate->part_no_enrolled = $xss->clean_input($request->part_number);
        $update_candidate->phoneno_1 = $xss->clean_input($request->mobile_number);
        $update_candidate->std_code = $xss->clean_input($request->std_code);
        $update_candidate->phoneno_2 = $xss->clean_input($request->landline_number);
        $update_candidate->emailid = $xss->clean_input($request->email_address);
        $update_candidate->cimage = $fileName;
        $update_candidate->modified_on = Carbon::now();
        $update_candidate->save();

        Session::forget('st_code_by_epic');
        Session::forget('st_name_by_epic');

        Session::forget('dist_no_by_epic');
        Session::forget('dist_name_by_epic');

        Session::forget('ac_no_by_epic');
        Session::forget('ac_name_by_epic');

        Session::forget('part_number_by_epic');
        Session::forget('serial_no_by_epic');

		

        if($update_candidate){
		
			if(Auth::user()->role_id == '19'){
				return redirect()->to('roac/affidavit/pending-criminal-cases')->with('Init','Contact Details has been successfully saved');
			}else{
				return redirect()->to('affidavit/pending-criminal-cases')->with('Init','Contact Details has been successfully saved');
			}

               if(config('public_config.nomination_log'))
	           {
	          $message['LogDescription']= 'Affidavit ID: '.$request->TblId.$nomination_num;
	          $message['TransectionStatus']= 'SUCCESS';

	        LogNotification::LogInfo($message);
	         }


		    }
            else{

        	if(config('public_config.nomination_log'))
	           {
	          $message['LogDescription']= 'Request Not completed';
	          $message['TransectionStatus']= 'Failed';

	        LogNotification::LogInfo($message);
	         }

            return redirect()->route('affidavit.candidate.details')->with('Init','your request is not completed.');
        } 
    }

    public function PendingCriminalCases(){
		
		if(Session::get('affidavit_id')){
			$data = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();
			if($data->finalized == '1' && Auth::user()->role_id != '19'){
				return redirect()->to('part-a-detailed-report')->with('success','Your Affidavit is allready Finalized.');
			}
		}else{
			return redirect()->to('affidavitdashboard')->with('success','Please select State and constituency.');
		}
		
		
        $statename = States::orderBy('ST_NAME')->get();
        $get_criminal_cases = AffPendingCriminalCase::where('affidavit_id',Session::get('affidavit_id'))
		->whereNull('not_applicable')
		->where('is_deleted','0')
		->orderBy('id','DESC')->get();

        $get_criminal_cases_applicable = AffPendingCriminalCase::where('affidavit_id',Session::get('affidavit_id'))
		->whereNotNull('not_applicable')
		->where('is_deleted','0')
		->get();

        $get_conviction_cases = AffImprisonmentCriminalCase::where('affidavit_id',Session::get('affidavit_id'))
		->whereNull('not_applicable')
		->where('is_deleted','0')
		->get();
		
		//dd($get_conviction_cases);

        $get_conviction_cases_applicable = AffImprisonmentCriminalCase::where('affidavit_id',Session::get('affidavit_id'))
		->whereNotNull('not_applicable')
		->where('is_deleted','0')
		->get();
		
		if(Auth::user()->role_id == '19'){
				$action = 'roac/affidavit/save-final-pending-criminal-conviction-cases';
			}else{
				$action = 'affidavit/save-final-pending-criminal-conviction-cases';
			}
		
		
		$data  = [];
		$user = Auth::user();
		$d=$this->commonModel->getunewserbyuserid($user->id);
		$data['user_data']=$d;

        return view('affidavit.pending_criminal_cases',['statename'=>$statename,'get_criminal_cases'=>$get_criminal_cases,'get_conviction_cases'=>$get_conviction_cases,'get_conviction_cases_applicable'=>$get_conviction_cases_applicable,'get_criminal_cases_applicable'=>$get_criminal_cases_applicable,'action'=>$action,'data'=>$data]);
    }

    public function SavePendingCriminalCases(Request $request){
		
		//dd($request->all());
		
		
        $xss = new xssClean;
        if($request->convictionType == 1){
            $str = 'NOT APPLICABLE';
            if(strtoupper($request->not_applicable) != $str){
                return redirect()->route('pending.criminal.cases')->with('msgerror','Please enter "NOT APPLICABLE" if you are not convicted');
            } else {
                // $save_criminal = new AffPendingCriminalCase;
                $save_criminal = AffPendingCriminalCase::FirstOrNew(['affidavit_id'=>Session::get('affidavit_id')]);
                $save_criminal->not_applicable = $xss->clean_input(strtoupper($request->not_applicable));
                $save_criminal->affidavit_id = Session::get('affidavit_id');				
                $save_criminal->modified_on = Carbon::now();
                $save_criminal->save();
                if($save_criminal):
                    return redirect()->route('pending.criminal.cases')->with('Init','Criminal cases details has been successfully saved');
                else:
                    return redirect()->route('pending.criminal.cases')->with('msgerror','Something went wrong please try again');
                endif;
            }
        } elseif($request->convictionType == 2) {
                $validatedData = $request->validate([
                    'fir_no' => 'required',
                    'st_name' => 'required',
                    'district_name' => 'required',
                    'police_station' => 'required',
                    'police_station_address' => 'required',
                    'case_number' => 'required',
                    'name_court' => 'required',
                    'acts' => 'required',
                    'section' => 'required',
                    'short_description' => 'required',
                ]);
                if($request->court_framed_the_charge==1){
                    $date = explode('-', $request->date);
                    $date = $date[2].'-'.$date[1].'-'.$date[0]; 
                    // dd($date);
                } else {
                    $date = '';
                }
                
                // dd($date);
                $save_criminal = new AffPendingCriminalCase;
                $save_criminal->affidavit_id = Session::get('affidavit_id');
                $save_criminal->candidate_id = Auth::user()->id;
                $save_criminal->fir_no = $xss->clean_input($request->fir_no);
                $save_criminal->st_code = $xss->clean_input($request->st_name);
                $save_criminal->dist_no = $xss->clean_input($request->district_name);
                $save_criminal->police_station = $xss->clean_input($request->police_station);
                $save_criminal->police_station_address = $xss->clean_input($request->police_station_address);
                $save_criminal->case_no = $xss->clean_input($request->case_number);
                $save_criminal->name_court_cognizance = $xss->clean_input($request->name_court);
                $save_criminal->acts = $xss->clean_input($request->acts);
                $save_criminal->sections = $xss->clean_input($request->section);
                $save_criminal->offence_description = $xss->clean_input($request->short_description);
                $save_criminal->framed_charge = $xss->clean_input($request->court_framed_the_charge);
                $save_criminal->date_charges = $xss->clean_input($date);
                $save_criminal->appeal_application = $xss->clean_input($request->appeal_application);
                $save_criminal->modified_on = Carbon::now();
                $save_criminal->save();
                if($save_criminal):
                    return redirect()->route('pending.criminal.cases')->with('Init','Criminal cases details has been successfully saved');
                else:
                    return redirect()->route('pending.criminal.cases')->with('msgerror','Something went wrong please try again');
                endif;    
        } else {
             return redirect()->route('pending.criminal.cases')->with('msgerror','Internal Server Error. please try again');
        }
    }
    
    public function SaveCaseConvictionCases(Request $request){
		
		//dd($request->all());
		
        $xss = new xssClean;
        if($request->convictionType_step_2 == 1){
            $str = 'NOT APPLICABLE';
            if(strtoupper($request->not_applicable_step_2) != $str){
                return redirect()->route('pending.criminal.cases')->with('msgerror','Please enter "NOT APPLICABLE" if you are not convicted');
            } else {
                $save_convicted = AffImprisonmentCriminalCase::FirstOrNew(['affidavit_id'=>Session::get('affidavit_id')]);
                $save_convicted->not_applicable = $xss->clean_input(strtoupper($request->not_applicable_step_2));
				$save_convicted->affidavit_id = Session::get('affidavit_id');
                $save_convicted->modified_on = Carbon::now();
                $save_convicted->save();
                if($save_convicted):
                    return redirect()->route('pending.criminal.cases')->with('Init','Criminal cases details has been successfully saved');
                else:
                    return redirect()->route('pending.criminal.cases')->with('msgerror','Something went wrong please try again');
                endif;
            }
        } elseif($request->convictionType_step_2 == 2){
            $date = explode('-', $request->date_of_order_conviction);
            $date = $date[2].'-'.$date[1].'-'.$date[0];
            if($request->conviction_order_conviction==1){
                $appeal = $request->details_present_appeal_conviction;
            } else {
                $appeal = '';
            }
			
			//dd($appeal);
			
            $save_convicted = new AffImprisonmentCriminalCase;
            $save_convicted->affidavit_id = Session::get('affidavit_id');
            $save_convicted->candidate_id = Auth::user()->id;
            $save_convicted->case_no = $xss->clean_input($request->conviction_case_no);
            $save_convicted->convicting_court = $xss->clean_input($request->name_of_the_court_conviction);
            $save_convicted->acts = $xss->clean_input($request->acts_conviction);
            $save_convicted->sections = $xss->clean_input($request->section_conviction);
            $save_convicted->offence_description = $xss->clean_input($request->brief_description_conviction);
            $save_convicted->order_date = $xss->clean_input($date);
            $save_convicted->punish = $xss->clean_input($request->punishment_imposed_conviction);
            $save_convicted->appeal_filed = $xss->clean_input($request->conviction_order_conviction);
            $save_convicted->appeal = $appeal;
            $save_convicted->modified_on = Carbon::now();
            $save_convicted->save();
            if($save_convicted):
                return redirect()->route('pending.criminal.cases')->with('Init','Convicted court cases has been successfully saved');
            else:
                return redirect()->route('pending.criminal.cases')->with('msgerror','Something went wrong please try again');
            endif;    
        }
    }

    public function SaveFinalPendingCriminalConvictionCases(Request $request){


       $message=array(); 
        $user=Auth::user();
		$nomID=(Session::get('nomination_id'));
        $message['MobNo']= $user->mobile ?? '';
	    $message['applicationType']= 'WebApp';
	    $message['Module']= 'SUVIDHA';
	    $message['TransectionType']= 'Online Nomination';
        $message['TransectionAction']= 'Affidavit Court Cases Details ';
        if(isset($nomID) && !empty($nomID))
        {
        $get_nominattion_detail = NominationApplicationModel::get_nomination_application($nomID); 
	    $nomination_num= ' Nomination_Num: '.$get_nominattion_detail['nomination_no'];
	    }else{
	    	$nomination_num='';
	    }






        if($request->checkedbox == 1){
			
			$count_pending = AffPendingCriminalCase::where('affidavit_id',Session::get('affidavit_id'))->where('is_deleted','0')->count();
			$count_imprisonment = AffImprisonmentCriminalCase::where('affidavit_id',Session::get('affidavit_id'))->where('is_deleted','0')->count();
			
			if($count_pending == 0){						
				$save_criminal = AffPendingCriminalCase::FirstOrNew(['affidavit_id'=>Session::get('affidavit_id')]);
                $save_criminal->not_applicable = 'NOT APPLICABLE';
                $save_criminal->affidavit_id = Session::get('affidavit_id');				
                $save_criminal->modified_on = Carbon::now();
                $save_criminal->save();
			}
			
			if($count_imprisonment == 0){
				$save_convicted = AffImprisonmentCriminalCase::FirstOrNew(['affidavit_id'=>Session::get('affidavit_id')]);
                $save_convicted->not_applicable = 'NOT APPLICABLE';
				$save_convicted->affidavit_id = Session::get('affidavit_id');
                $save_convicted->modified_on = Carbon::now();
                $save_convicted->save();
			}
			
			if(config('public_config.nomination_log'))
           {
	          $message['LogDescription']= 'Affidavit ID: '.Session::get('affidavit_id').$nomination_num;
	          $message['TransectionStatus']= 'SUCCESS';

	        LogNotification::LogInfo($message);
	         }
			
			
			if(Auth::user()->role_id == '19'){
				return redirect()->to('roac/Affidavit/MovableAssets')->with('Init','Court cases has been successfully saved');
			}else{
				return redirect()->to('Affidavit/MovableAssets')->with('Init','Court cases has been successfully saved');
			}
			
			
           // return redirect()->route('affidavit.movable_asset')->with('Init','Court cases has been successfully saved');
        } else {
        	if(config('public_config.nomination_log'))
           {
	          $message['LogDescription']= 'Something went wrong please try again';
	          $message['TransectionStatus']= 'Failed';

	        LogNotification::LogInfo($message);
	         }
            return redirect()->route('pending.criminal.cases')->with('msgerror','Something went wrong please try again');
        }
    }

    public function destroy($id){
        //AffPendingCriminalCase::find($id)->delete($id);
        AffPendingCriminalCase::where('id',$id)->update(['is_deleted' => '1']);
        $url = route('pending.criminal.cases');
        return response()->json([
            'success' => 'Record deleted successfully!','url'=>$url
        ]);
    }

    public function ConvictionDestroy($id){
        //AffImprisonmentCriminalCase::find($id)->delete($id);
        AffImprisonmentCriminalCase::where('id',$id)->update(['is_deleted' => '1']);
        $url = route('pending.criminal.cases');
        return response()->json([
            'success' => 'Record deleted successfully!','url'=>$url
        ]);
    }
    

    public function CriminalDataAvailableNull(){
        $getdata = AffPendingCriminalCase::where('affidavit_id',Session::get('affidavit_id'))
		->whereNotNull('not_applicable')
		->where('is_deleted','0')
		->count();
        if($getdata > 0){
            return response()->json(['error'=>false, 'status' =>$this->successStatus]);
        } else {
            return response()->json(['error'=>true, 'status' =>400]);
        }
    }

    public function CriminalDataAvailableNotNull(){
        $getdata = AffPendingCriminalCase::where('affidavit_id',Session::get('affidavit_id'))
		->whereNull('not_applicable')
		->where('is_deleted','0')
		->count();
        // dd($getdata);
        if($getdata > 0){
            return response()->json(['error'=>false, 'status' =>$this->successStatus]);
        } else {
            return response()->json(['error'=>true, 'status' =>400]);
        }
    }

    public function ConvictionDataAvailableNotNull(){
        $getdata = AffImprisonmentCriminalCase::where('affidavit_id',Session::get('affidavit_id'))
		->whereNull('not_applicable')
		->where('is_deleted','0')
		->count();
        if($getdata > 0){
            return response()->json(['error'=>false, 'status' =>$this->successStatus]);
        } else {
            return response()->json(['error'=>true, 'status' =>400]);
        }
    }

    public function ConvictionDataAvailableNull(){
        $getdata = AffImprisonmentCriminalCase::where('affidavit_id',Session::get('affidavit_id'))
		->whereNotNull('not_applicable')
		->where('is_deleted','0')
		->count();
        if($getdata > 0){
            return response()->json(['error'=>false, 'status' =>$this->successStatus]);
        } else {
            return response()->json(['error'=>true, 'status' =>400]);
        }
    }
	
	
	public function upload($request, $size = 2048, $is_image = 'image', $destination_path = ''){

        // ini_set('max_execution_time', 0);
        // ini_set("pcre.backtrack_limit", "50000000000000000000000");
        // ini_set('memory_limit', '-1');


		$xss = new xssClean;


        if(!$request->has('file')){
            return Response::json([
                'success'   => false,
                'errors'    => "Please upload a file less than ".$allowed_size."MB size."
            ]);
        }

        $tmp_folder = '';
        $destination_path = 'uploads1/'.$destination_path;
        foreach (explode('/',$destination_path) as $itr_folder) {
            if(empty($tmp_folder)){
                $tmp_folder = $itr_folder;
            }else{
                $tmp_folder = $tmp_folder.'/'.$itr_folder;
            }
            if (!file_exists($tmp_folder)) {
              mkdir($tmp_folder, 0777, true);
            }
        }

        try{
            $file       =   $request->file('file');
            $filename   =   time().$xss->clean_input($file->getClientOriginalName());
            $filetype   =   $file->getMimeType();
         }catch(\Exception $e){
            return Response::json([
                'success'   => false,
                'errors'    => "Your file permission is readonly. Please upload a valid image file."
            ]);
        }


        $allowed_size = $size/1024;
        if($file->getSize() > $size*1024){
            return Response::json([
        		'success' 	=> false,
        		'errors' 	=> "Please upload a file less than ".$allowed_size."MB size."
        	]);
        }

        if($is_image == 'image'){
            $allowed_mime = array(
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/x-png',
            );
            $allowed_error = "Please upload a valid jpeg, jpg, png file.";
        }else if($is_image == 'pdf'){
            $allowed_mime = array(
                'application/pdf',
            );
            $allowed_error = "Please upload a valid jpeg, jpg, png file.";
        }

        if (!in_array($filetype, $allowed_mime)) {
            return Response::json([
        		'success' 	=> false,
        		'errors' 	=> "File Type Not Allowed"
        	]);
        }

        if (!file_exists($destination_path)) {
            mkdir($destination_path, 0777, true);
        }
        
        try{
           $file->move($destination_path,$filename);
        }catch(\Exception $e){
        	return Response::json([
        		'success' 	=> false,
        		'errors' 	=> "Destination path does not exist."
        	]);
        }
      	
      	return Response::json([
        	'success' 	=> true,
        	'path' 	=> $destination_path.'/'.$filename
        ]);
        
  }
	
	
	
}