<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class ACCEOModel extends Model
{
    //
   public function Allcandidatelist($user,$status="all",$search='',$constituency='',$const_type)
    		{  
    			DB::enableQueryLog();
          if($const_type=="AC") { 
                        $v= 'candidate_nomination_detail.ac_no'; $m=$constituency; 
                        }
        elseif($const_type=="PC") { 
              $v= 'candidate_nomination_detail.pc_no'; $m=$constituency;
            }
       // else 
      //  { $v=''; $m=''; }
    		 $a='1'; $a1='2';$a2='3';$a3='4'; $a4='5';$a5='6'; 
         
    		if($search=='' and $constituency=='') {
  			if($status=="all" || $status=="") {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
				  	->where('candidate_nomination_detail.st_code','=',$user->st_code) 
		    		->where(function($query1) use ($a,$a1,$a2,$a3,$a4,$a5){
                    		$query1->where('candidate_nomination_detail.application_status','=',$a)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a1)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a2)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a3)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a4)
                   			->orWhere('candidate_nomination_detail.application_status','=',$a5);
              			})
			  		->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get();  
    		   }
    		   else {  
    		   		$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
					
		    	->where('candidate_nomination_detail.st_code','=',$user->st_code) 
		    	->where('candidate_nomination_detail.application_status','=',$status)
		    	 ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get();  
    		   }	
    		   }
    		elseif($constituency=='') {
    			$list = DB::table('candidate_nomination_detail')
		   			->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
				   ->where('candidate_nomination_detail.st_code','=',$user->st_code) 
		    	 ->where('candidate_nomination_detail.qrcode','=',$search)
		    	 ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get(); 
    		}
        elseif($search=='') {
          $list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id')
           ->where('candidate_nomination_detail.st_code','=',$user->st_code)->where($v,'=',$m) 
           ->select('candidate_nomination_detail.*','candidate_personal_detail.cand_name','candidate_personal_detail.candidate_father_name')->limit(100)->get(); 
        }
    		   return $list;
    		 
    		}
     function electiondetailsbystatecode($st_code,$const='')
            {
              if($const=='')
              $rec =DB::table('m_election_details')->where('st_code',$st_code)->orderBy('const_no', 'ASC')->get();
              else
               $rec =DB::table('m_election_details')->where('st_code',$st_code)->where('const_no', $const)->get();  
              return $rec;
            }		

      function gettotalnominationcntbystatus($status, $const_type,$st_code,$const_no, $fromdate, $todate)
      		{
      		if($const_type=="PC") {
      		  $rec =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('pc_no',$const_no)->where('application_status',$status)->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count();
      		  } 
      		elseif($const_type=="AC"){
      		  $rec =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('ac_no',$const_no)->where('application_status',$status)->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count(); 
      		  } 
              return $rec;	
      		}
     function gettotalnominationcnt($const_type,$st_code,$const_no, $fromdate, $todate)
      		{   
      			DB::enableQueryLog();
      		if($const_type=="PC") {
      		  $rec =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('pc_no',$const_no)->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count();
      		  } 
      		  elseif($const_type=="AC"){
      		  $rec =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('ac_no',$const_no)->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count(); 
      		  } 
      		    //$query = DB::getQueryLog();
				//$query = $query;
				//dd($query);
              return $rec;	
      		}
    function Allcandidate_finaliselist($st_code)
          {  
           $rec =DB::table('candidate_finalized_ac')->where('st_code',$st_code)->get();
            return $rec;
          }
    function get_candidate_finalizeac($st_code,$ac_no,$actype)
          {
            $rec =DB::table('candidate_finalized_ac')->where('st_code',$st_code)->where('const_no',$ac_no)->where('const_type',$actype)->first();
            return $rec;
          }
     public function definalize_candidate_ac($st,$ac,$actype,$dat)
        {
         $udata = array('finalize'=>'0'); 
         DB::table('candidate_finalized_ac')->where('st_code',$st)->where('const_no',$ac)->where('const_type',$actype)->update($dat);
         if($actype=="AC") {$field="ac_no"; $val=$ac; } elseif($actype=="PC"){$field="pc_no"; $val=$ac; }

        DB::table('candidate_nomination_detail')->where('st_code',$st)->where($field,$val)->update($udata);
          
         return true;
        }
     public function public_affidavit_ac($st,$ac)
        {
         $udata = array('affidavit_public'=>'yes'); 
          DB::table('candidate_nomination_detail')->where('st_code',$st)->where('ac_no',$ac)->update($udata);
          return true;
        }
 }