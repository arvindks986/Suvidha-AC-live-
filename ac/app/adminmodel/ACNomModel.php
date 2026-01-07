<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
    use Illuminate\Support\Facades\Auth;
class ACNomModel extends Model
{
   public function candidatelist($st_code,$const_no)
    {  
    	 $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$st_code) 
             ->where('candidate_nomination_detail.election_id','=',Auth::user()->election_id)
            ->where('candidate_nomination_detail.ac_no','=',$const_no)
            ->where('candidate_nomination_detail.application_status','<>','11')
            ->groupby('candidate_personal_detail.candidate_id') 
            ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_mobile','candidate_personal_detail.candidate_residence_acno','candidate_personal_detail.candidate_father_name','candidate_personal_detail.candidate_id')->get(); 
		 
    	return $list;
    }
	 public function getnominationlist($st_code,$const_no)
	    	{  
	    		$list = DB::table('candidate_nomination_detail') 
	    				->where('candidate_nomination_detail.st_code','=',$st_code) 
	                    ->where('candidate_nomination_detail.ac_no','=',$const_no)
	                     ->where('candidate_nomination_detail.election_id','=',Auth::user()->election_id)
	                    ->where('candidate_nomination_detail.application_status','<>','11')
	                    ->orderBy('candidate_nomination_detail.nom_id', 'ASC')
	                    ->get();
			 
	    	return $list;
	    } 
 
	   public function getaffidavite($st_code,$const_no)
	    {  
	    	$list = DB::table('candidate_affidavit_detail') 
	    				->where('st_code','=',$st_code) 
	                    ->where('ac_no','=',$const_no)
	                    // ->where('candidate_affidavit_detail.election_id','=',Auth::user()->election_id)
	                    ->orderBy('added_create_at', 'ASC')
	                    ->get();
			 
	    	return $list;
	    } 
	   public function getcounteraffidavite($st_code,$const_no)
	    {  
	    	$list = DB::table('candidate_counteraffidavit_detail') 
	    				->where('st_code','=',$st_code) 
	                    ->where('ac_no','=',$const_no)
	                    // ->where('candidate_counteraffidavit_detail.election_id','=',Auth::user()->election_id)
	                    ->orderBy('added_create_at', 'ASC')
	                    ->get();
			 
	    	return $list;
	    } 

	   public function getcounteraffidavitebynomid($st_code,$const_no,$nomid) {  
	    			$list = DB::table('candidate_counteraffidavit_detail')
	    					->where('st_code','=',$st_code)
	    					->where('ac_no','=',$const_no)
                          	->where('nom_id','=',$nomid)
                          	->orderBy('added_create_at', 'ASC')
                          	->get(); 
			 
	    	return $list;
	    	}

	    public function getnominationiscriminal($st_code,$const_no)
	    	{  
			
	    		$list = DB::table('candidate_nomination_detail')
            			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$st_code) 
            ->where('candidate_nomination_detail.election_id','=',Auth::user()->election_id)
            ->where('candidate_nomination_detail.ac_no','=',$const_no)
            ->where('candidate_nomination_detail.application_status','<>','11')
            ->where('candidate_personal_detail.is_criminal','=','1')
            //->groupby('candidate_personal_detail.candidate_id') 
            ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_mobile',
            	'candidate_personal_detail.is_criminal','candidate_personal_detail.candidate_father_name',
            	'candidate_personal_detail.candidate_id','candidate_nomination_detail.nom_id')->get(); 
 
	    	return $list;
	    }  

	    /*public function getnominationiscriminal($st_code,$const_no)
	    	{  
			
	    		$list = DB::table('candidate_nomination_detail')
            			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
            ->where('candidate_nomination_detail.st_code','=',$st_code) 
            ->where('candidate_nomination_detail.election_id','=',Auth::user()->election_id)
            ->where('candidate_nomination_detail.ac_no','=',$const_no)
            ->where('candidate_nomination_detail.application_status','<>','11')
            ->where('candidate_personal_detail.is_criminal','=','1')
            //->groupby('candidate_personal_detail.candidate_id') 
            ->select('candidate_personal_detail.cand_name','candidate_personal_detail.cand_mobile',
            	'candidate_personal_detail.is_criminal','candidate_personal_detail.candidate_father_name',
            	'candidate_personal_detail.candidate_id','candidate_nomination_detail.nom_id','candidate_nomination_detail.date_of_publication','candidate_nomination_detail.newspaper_name','candidate_nomination_detail.paper_cutting_upload_path')->get(); 
 
	    	return $list;
	    }*/


	    

public function getcountffidavitebynomid($st_code, $const_no, $nomid)
	{
		$list = DB::table('candidate_affidavit_detail')->where('st_code', '=', $st_code)->where('ac_no', '=', $const_no)->where('is_deleted', '=', '1')
			->where('nom_id', '=', $nomid)->orderBy('added_create_at', 'ASC')->get();

		return $list;
	}



}
