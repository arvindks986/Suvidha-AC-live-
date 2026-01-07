<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class MasterReportModel extends Model
{
    //Created By Niraj 14-03-19  
	  function getAcByDeo($stcode,$dist_no,$election_id)
		{  DB::enableQueryLog();
	  	$getelectorsData = DB::table('elector_details')->where('election_id',$election_id)->where('st_code', $stcode)->where('dist_no',$dist_no)->get();
		 // dd($getelectorsData);
		if(!empty($getelectorsData)){
		/*$getAcListByPC = DB::table('m_ac')
		   			->leftjoin('elector_details', [['m_ac.PC_NO', '=', 'elector_details.pc_no'],['m_ac.AC_NO', '=', 'elector_details.ac_no']])
				   ->where('m_ac.st_code','=',$stcode) 
		    	 ->where('m_ac.PC_NO','=',$pc_no)
		    	 ->select('elector_details.*','m_ac.AC_NO','m_ac.AC_NAME')->groupBy('m_ac.AC_NO', 'm_ac.AC_NAME','elector_details.ac_no')->get(); */
				 $query="select `elector_details`.*, `m_ac`.`AC_NO`, `m_ac`.`AC_NAME` from `m_ac` left join `elector_details` on (`m_ac`.`DIST_NO_HDQTR` = `elector_details`.`dist_no` and `m_ac`.`AC_NO` = `elector_details`.`ac_no` AND `elector_details`.`st_code`='$stcode') WHERE  `m_ac`.`st_code`= '$stcode' and `m_ac`.`DIST_NO_HDQTR` = $dist_no group by `m_ac`.`AC_NO`, `m_ac`.`AC_NAME`, `elector_details`.`ac_no`";
				   $getAcListByDeo = DB::select(DB::raw($query));
		    }else{
		   $getAcListByDeo = DB::table('m_ac')->where('ST_CODE', $stcode)->where('DIST_NO_HDQTR',$dist_no)->orderBy('AC_NO', 'asc')->get();
		   }
		//dd(DB::getQueryLog());
		//dd($getAcListByPC);
		return $getAcListByDeo;
		}

function getelectorssummarybyState($stcode,$election_id)
{  DB::enableQueryLog();
		
$query ="SELECT 
m_district.DIST_NO,
m_district.DIST_NAME,
SUM(elector_details.gen_m) as total_gen_m ,
SUM(elector_details.gen_f) as total_gen_f ,
SUM(elector_details.gen_o) as total_gen_o ,
SUM(elector_details.gen_t) as total_gen_t ,
SUM(elector_details.ser_m) as total_ser_m ,
SUM(elector_details.ser_f) as total_ser_f ,
SUM(elector_details.ser_o) as total_ser_o,
SUM(elector_details.ser_t) as total_ser_t ,
SUM(elector_details.polling_reg) as total_polling_reg,
SUM(elector_details.polling_auxillary) as total_polling_auxillary,
SUM(elector_details.polling_total) as total_polling_total  
FROM m_district left join elector_details
on m_district.DIST_NO=elector_details.dist_no and m_district.ST_CODE=elector_details.st_code
WHERE m_district.ST_CODE='$stcode' GROUP BY m_district.DIST_NO,m_district.DIST_NAME";
$getelectorssummarybyState = DB::select(DB::raw($query));
		//dd(DB::getQueryLog());
		return $getelectorssummarybyState;
		}
//By Niraj 19-03-19
function getNominatedCandidatebyAC($st_code,$ac_no)
{
  $getNominatedCandidatebyAC =DB::table('candidate_nomination_detail')->where('party_id','!=','1180')->where('application_status','!=','11')->where('st_code',$st_code)->where('ac_no',$ac_no)->get();
  return $getNominatedCandidatebyAC;
}

//By Niraj 19-03-19
function getDatewiseCandidateListbyAC($stcode,$ac_no,$fromdate='', $todate='')
{
	
  if($fromdate!='' & $todate!=''){ 
   $getDatewiseCandidateListbyAC =  DB::table('candidate_nomination_detail')
   ->where('st_code',$stcode)->where('party_id','!=','1180')->where('application_status','!=','11')
  ->where('ac_no',$ac_no)
   ->whereBetween('date_of_submit', [$fromdate, $todate])->get();
  }else{
      $getDatewiseCandidateListbyAC =  DB::table('candidate_nomination_detail')
      ->where('st_code',$stcode)->where('party_id','!=','1180')->where('application_status','!=','11')
      ->where('ac_no',$ac_no)
      ->get();
	}	
	// dd(DB::getQueryLog());
  return $getDatewiseCandidateListbyAC;
}
   
// getnominationByROPC ROPC level by Niraj 19-3-19		
function getDatewisenomination($stcode,$ac_no,$fromdate, $todate)
{  DB::enableQueryLog();
 if($fromdate!='' & $todate!=''){ 
  $getDatewisenomination =  DB::table('candidate_nomination_detail')
  ->select('*', DB::raw('count(nom_id) as totalnomination'))
  ->where('st_code',$stcode)->where('party_id','!=','1180')->where('application_status','!=','11')
  ->groupBy('ac_no')
  ->whereBetween('date_of_submit', [$fromdate, $todate])->get();
 }else{
     $getDatewisenomination =  DB::table('candidate_nomination_detail')
     ->select('*', DB::raw('count(nom_id) as totalnomination'))
     ->where('st_code',$stcode)->where('party_id','!=','1180')->where('application_status','!=','11')
     ->groupBy('ac_no')
     ->get();
 }		  
  //dd(DB::getQueryLog());
  // dd($getDatewisenomination);
  return $getDatewisenomination;
} //end function getDatewisenomination

function getDatewisenomination_at_deo($stcode,$dist_no,$fromdate, $todate)
{
 if($fromdate!='' & $todate!=''){ 
  $getDatewisenomination =  DB::table('candidate_nomination_detail')
  ->select('*', DB::raw('count(nom_id) as totalnomination'))
  ->where('st_code',$stcode)->where('district_no',$dist_no)->where('party_id','!=','1180')->where('application_status','!=','11')
  ->groupBy('ac_no')
  ->whereBetween('date_of_submit', [$fromdate, $todate])->get();
 }else{
     $getDatewisenomination =  DB::table('candidate_nomination_detail')
     ->select('*', DB::raw('count(nom_id) as totalnomination'))
     ->where('st_code',$stcode)->where('district_no',$dist_no)->where('party_id','!=','1180')->where('application_status','!=','11')
     ->groupBy('ac_no')
     ->get();
 }		  
  return $getDatewisenomination;
}		
		

 }