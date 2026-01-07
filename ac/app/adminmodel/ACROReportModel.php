<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class ACROReportModel extends Model
{
    //
		  
	  function getAcByPC($stcode,$pc_no,$election_id)
		{  DB::enableQueryLog();
		$getelectorsData = DB::table('elector_details')->where('election_id',$election_id)->where('st_code', $stcode)->where('pc_no',$pc_no)->get();
		
		
		if(!empty($getelectorsData)){
		$getAcListByPC = DB::table('m_ac')
		   			->leftjoin('elector_details', [['m_ac.PC_NO', '=', 'elector_details.pc_no'],['m_ac.AC_NO', '=', 'elector_details.ac_no']])
				   ->where('m_ac.st_code','=',$stcode) 
		    	 ->where('m_ac.PC_NO','=',$pc_no)
		    	 ->select('elector_details.*','m_ac.AC_NO','m_ac.AC_NAME')->groupBy('m_ac.AC_NO', 'm_ac.AC_NAME','elector_details.ac_no')->get(); 
		    }else{
		   $getAcListByPC = DB::table('m_ac')->where('ST_CODE', $stcode)->where('PC_NO',$pc_no)->orderBy('AC_NO', 'asc')->get();
		   }
		//dd(DB::getQueryLog());
		return $getAcListByPC;
		}

	
		function electiondetailsbystatecode($st_code,$consttype,$const='')
		{
			if($const=='undefined' || $const=='all') {
				$const='';
			}
			if($const=='' && $consttype=="AC") {
			$rec =DB::table('m_election_details')
			->join('m_pc',[
				['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
				['m_election_details.CONST_NO', '=','m_pc.PC_NO']
				])
			->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
			->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
			->select('m_election_details.*','m_pc.*')->get();
			// dd($rec);
		// dd(DB::getQueryLog());
		}
			elseif ($const=='' && $consttype=="PC") {
				// DB::enableQueryLog();
				$rec =DB::table('m_election_details')
				->join('m_pc',[
					['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
					['m_election_details.CONST_NO', '=','m_pc.PC_NO']
					])
				->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
				->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
				->select('m_election_details.*','m_pc.*')->get();
				// dd(DB::getQueryLog());
				// dd($rec);
			}
			else {
			if($const!='' && $consttype=="AC") {
				// dd("hello");
				$rec =DB::table('m_election_details')
				->join('m_ac',[
					['m_election_details.ST_CODE', '=','m_ac.ST_CODE'],
					['m_election_details.CONST_NO', '=','m_ac.AC_NO']
					])
				->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
				->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
				->select('m_election_details.*','m_ac.*')->get(); }

				elseif ($const!=='' && $consttype=="PC") {
					$rec =DB::table('m_election_details')
					->join('m_pc',[
						['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
						['m_election_details.CONST_NO', '=','m_pc.PC_NO']
						])
					->where('m_election_details.ST_CODE',$st_code)->where('m_election_details.CONST_NO',$const)
					->where('m_election_details.CONST_TYPE',$consttype)->orderBy('m_election_details.CONST_NO', 'ASC')
					->select('m_election_details.*','m_pc.*')->get();
				} }
			return $rec;
	}

	function gettotalnominationcnt($const_type,$st_code,$const_no, $fromdate='', $todate='')
	{
		DB::enableQueryLog();
	if($fromdate==''){
		if($const_type=="PC") {
			$rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->get()->count();
			} 
			elseif($const_type=="AC"){
			$rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->get()->count(); 
			}
	} else{
		if($const_type=="PC") {
			$rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count();
			} 
			elseif($const_type=="AC"){ 
			$rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count(); 
			}
	}
				// dd($rec);
			return $rec;	
	}

	function gettotalnominationcntbystatus($status, $const_type,$st_code,$const_no, $fromdate, $todate)
      		{
            // dd($todate);
            if($status == 1) {
              if($fromdate=='') {
                if($const_type=="PC") {
                  $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status',1)->orwhere('application_status',2)->orwhere('application_status',3)->get()->count();
                  } 
                elseif($const_type=="AC"){
                  $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status',$status)->get()->count(); 
                  } 
              }else {
                if($const_type=="PC") {
                  $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status',$status)->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count();
                  } 
                elseif($const_type=="AC"){
                  $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status',$status)->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count(); 
                  } 
              }
            }else{
              if($fromdate=='') {
                if($const_type=="PC") {
                  $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status',$status)->get()->count();
                  } 
                elseif($const_type=="AC"){
                  $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status',$status)->get()->count(); 
                  } 
              }else {
                if($const_type=="PC") {
                  $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('pc_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status',$status)->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count();
                  } 
                elseif($const_type=="AC"){
                  $rec =DB::table('candidate_nomination_detail')->where('ST_CODE',$st_code)->where('ac_no',$const_no)->where('party_id', '!=' ,'1180')->where('application_status',$status)->whereBetween('date_of_submit', [$fromdate, $todate])->get()->count(); 
                  } 
              }
            }
              // dd(DB::getQueryLog());
              return $rec;	
              // dd($rec);
      		}
		
// Officer Details ROPC level by Niraj 19-2-19		
	function getOfficerlistByROPC($stcode,$pc_no='')
		{  DB::enableQueryLog();
		 if($pc_no!=''){
		  $getOfficerlistByROPC =  DB::table('officer_login')->where('st_code',$stcode)->where('pc_no',$pc_no)->get();
		 }else{
			  $getOfficerlistByROPC =  DB::table('officer_login')->where('st_code',$stcode)->get();
		 }		  
		  //dd(DB::getQueryLog());
		  
		  return $getOfficerlistByROPC;
		} //end function getOfficerlistByROPC
		
		
	 
 }