<?php

namespace App\Http\Controllers\Affidavit;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\models\States;
use App\models\Districts;
use App\models\Affidavit\AffCandDetail;
use App\models\Nomination\NominationApplicationModel;

use App\models\Affidavit\AffPanDetail;
use App\models\Affidavit\AffRelationTypeModel;

use App\models\Affidavit\AffEducation;


use Exception;
use Log;
use App\Classes\xssClean;
use Carbon\Carbon;

use App\commonModel;
use App\adminmodel\CandidateModel;

use App\Helpers\LogNotification;

class EducationController extends Controller
{
	public function __construct(){   
        $this->commonModel = new commonModel();
		$this->CandidateModel = new CandidateModel();
    }
		
	public function education()
    {



       
    	$user=Auth::user();
    	$message=array();
		$nomID=(Session::get('nomination_id'));
        $message['MobNo']= $user->mobile ?? '';
	    $message['applicationType']= 'WebApp';
	    $message['Module']= 'SUVIDHA';
	    $message['TransectionType']= 'Online Nomination';
        $message['TransectionAction']= 'Affidavit Profession Details ';
        if(isset($nomID) && !empty($nomID))
        {
        $get_nominattion_detail = NominationApplicationModel::get_nomination_application($nomID); 
	    $nomination_num= ' Nomination_Num: '.$get_nominattion_detail['nomination_no'];
	    }else{
	    	$nomination_num='';
	    }







		
		if(Session::get('affidavit_id')){
			$data = AffCandDetail::where('affidavit_id',Session::get('affidavit_id'))->first();
			if($data->finalized == '1' && Auth::user()->role_id != '19'){
				return redirect()->to('part-a-detailed-report')->with('success','Your Affidavit is allready Finalized.');
			}
		}else{
			return redirect()->to('affidavitdashboard')->with('success','Please select State and constituency.');
		}
		
    	try {
			
			
	    	$data = AffPanDetail::select("aff_pan_details.name","aff_pan_details.id", "aff_pan_details.candidate_id")
	    	//->where("aff_pan_details.candidate_id", Auth::user()->id)
			->where('affidavit_id',Session::get('affidavit_id'))
	    	->groupBy("aff_pan_details.candidate_id")
	    	->first();
			
			
			//dd($data);

	    	$education = AffEducation::select("aff_cand_qualification.*")
	    	//->where("aff_cand_qualification.candidate_id", Auth::user()->id)
			->where('affidavit_id',Session::get('affidavit_id'))
			->where('is_deleted','0')
	    	->get();
			
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);

			  if(config('public_config.nomination_log'))
           {
	          $message['LogDescription']= 'Affidavit ID: '.Session::get('affidavit_id'). $nomination_num;
	          $message['TransectionStatus']= 'SUCCESS';

	        LogNotification::LogInfo($message);
	         }
			
	    	return view('affidavit.affidavit_education', ['data'=>$data,'user_data'=>$d, 'education'=>$education]);
    	 }catch (Exception $e){

    	 	
    	 	if(config('public_config.nomination_log'))
           {
	          $message['LogDescription']= 'Something went to wrong '.$e->getMessage();
	          $message['TransectionStatus']= 'Failed';

	        LogNotification::LogInfo($message);
	         }
    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
            Session::flash('status',0);
      		Session::flash('flash-message', "Something went wrong, please try again after sometime.");
    		//return redirect('AffidavitDashboard');
		}

    }
	
	public function save_education(Request $request)
    {
    	$xss = new xssClean;
		
			//dd($request->all());
			
    		try {
    			
	    		$insert = new AffEducation;
	            $insert->candidate_id 			= Auth::user()->id;
				$insert->affidavit_id			= Session::get('affidavit_id');
	            $insert->qualification 			= $xss->clean_input($request->qualification);
	            $insert->full_form_course 		= $xss->clean_input($request->full_form_course);
	            $insert->school_college 		= $xss->clean_input($request->school_college);
	            $insert->board_univ 			= $xss->clean_input($request->board_univ);
	            $insert->q_year 				= $xss->clean_input($request->q_year);
	            $insert->added_create_at 		= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();

				//dd($insert);


	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		 } catch (Exception $e) {
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    }
	
	
	public function update_education(Request $request)
    {
    	$xss = new xssClean;
    		try {
    			
				
				$insert = AffEducation::find($request->id);
	            $insert->qualification 			= $xss->clean_input($request->qualification);
	            $insert->full_form_course 		= $xss->clean_input($request->full_form_course);
	            $insert->school_college 		= $xss->clean_input($request->school_college);
	            $insert->board_univ 			= $xss->clean_input($request->board_univ);
	            $insert->q_year 				= $xss->clean_input($request->q_year);
	            $insert->updated_at 			= Carbon::now();
	            $insert->added_update_at 		= Carbon::now();
	            $insert->save();
				
	            $result = array("id" =>$insert->id);
	        	echo json_encode($result);
    		} 
	    	catch (Exception $e) 
	    	{
	    		Log::channel('customlog')->info(date("Y-m-d")."-".$e->getMessage());
	    		echo 0;
			}
    }


	public function delete_education(Request $request)
    {
    	$xss = new xssClean;
    	if(!empty($request->id))
    	{
    		try {
	    		//AffEducation::destroy($request->id);
				AffEducation::where('id',$request->id)->update(['is_deleted' => '1']);
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

}
