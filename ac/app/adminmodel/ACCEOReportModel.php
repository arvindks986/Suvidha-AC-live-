<?php
namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;
    use DB;
class ACCEOReportModel extends Model
{

	function duplicateSymboleCandidate($stcode)
    {  
   $query = "SELECT  cand.party_id,cand.st_code,cand.pc_no,cand.`symbol_id`,cand.candidate_id FROM candidate_nomination_detail as cand INNER JOIN( SELECT `symbol_id` FROM candidate_nomination_detail GROUP BY `symbol_id` HAVING COUNT(`symbol_id`) >1 )temp ON cand.`symbol_id`= temp.`symbol_id` WHERE cand.st_code='$stcode'
";
            $results = DB::select(DB::raw($query));
            
            //foreach ($results as $result) {
              //  $symbol_id =$result->symbol_id;
            	//$finlArray[$i]['symbol_id'] =  $symbol_id;
            	
            	
            	//$finlArray[$i]['cand_result']  = $cand_result;
            	//print_r($results);die;
            	//$i++;
            //}
            return $results;
    }

    function getduplicatenominationparty($st_code)
             { //DB::enableQueryLog();
            
 $query ="SELECT cand.st_code,cand.pc_no,cand.party_id,cand.candidate_id FROM candidate_nomination_detail as cand INNER JOIN(SELECT party_id FROM candidate_nomination_detail GROUP BY party_id HAVING COUNT(party_id) >1)temp ON cand.party_id=temp.party_id WHERE cand.st_code='$st_code'";
           $getduplicatenominationparty = DB::select(DB::raw($query));
             // dd(DB::getQueryLog());
          return $getduplicatenominationparty;    
             }
             function getCandidateListbyPC($st_code,$pc_no)
         {

            //$getCandidateListbyPC =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('application_status',6)->get();
           $getCandidateListbyPC =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('pc_no',$pc_no)->where('application_status',6)->get();
           return $getCandidateListbyPC;
         }
          
           function totalnominationcntbystatus($status,$pc_no)
             {  //DB::enableQueryLog();
               $totalnominationcntbystatus =DB::table('candidate_nomination_detail')->where('pc_no',$pc_no)->where('application_status',$status)->get()->count();
              // dd(DB::getQueryLog());
             return $totalnominationcntbystatus;    
             }
             function independentcandidatelist($st_code,$cand_party_type,$finalize)
       {
            DB::enableQueryLog();
            $independentcandidatelist =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('finalize',$finalize)->get();
            //dd(DB::getQueryLog());
            return $independentcandidatelist;
       }

       function ceosymbolno_200pdf($st_code,$finalize)
       {
            DB::enableQueryLog();
            //$independentcandidatelist =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('finalize',$finalize)->where('cand_party_type',$cand_party_type)->get();
            $independentcandidatelist =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('symbol_id',200)->where('finalize',$finalize)->get();
            //dd(DB::getQueryLog());
            return $independentcandidatelist;
       }

      


function getCountStatus($st_code,$pcno,$status)
       {
        //DB::enableQueryLog();
               $count =DB::table('candidate_nomination_detail')->where('st_code',$st_code)->where('pc_no',$pcno)->where('application_status',$status)->get()->count();
              // dd(DB::getQueryLog());
            return $count;
       }


function getelectorssummarybyState($stcode,$election_id)
		{ //Created by Niraj 18-2-19
		 DB::enableQueryLog();
		
$query ="SELECT 
m_pc.PC_NO,
m_pc.PC_NAME,
count(elector_details.gen_m) as total_gen_m ,
count(elector_details.gen_f) as total_gen_f ,
count(elector_details.gen_o) as total_gen_o ,
count(elector_details.gen_t) as total_gen_t ,
count(elector_details.ser_m) as total_ser_m ,
count(elector_details.ser_f) as total_ser_f ,
count(elector_details.ser_o) as total_ser_o,
count(elector_details.ser_t) as total_ser_t ,
count(elector_details.polling_reg) as total_polling_reg,
count(elector_details.polling_auxillary) as total_polling_auxillary,
count(elector_details.polling_total) as total_polling_total  
FROM m_pc left join elector_details
on m_pc.PC_NO=elector_details.pc_no and m_pc.ST_CODE=elector_details.st_code
WHERE m_pc.ST_CODE='$stcode' GROUP BY m_pc.PC_NO,m_pc.PC_NAME";
		   $getelectorssummarybyState = DB::select(DB::raw($query));
		    
		//dd(DB::getQueryLog());
		return $getelectorssummarybyState;
		}
function getAcByPC($stcode,$pc_no,$election_id)
		{  DB::enableQueryLog();
		$getelectorsData = DB::table('elector_details')->where('election_id',$election_id)->where('st_code', $stcode)->where('pc_no',$pc_no)->get();
		//dd($getelectorsData);
		
		if(!empty($getelectorsData)){
		$getAcListByPCNo = DB::table('m_ac')
		   			->leftjoin('elector_details', [['m_ac.PC_NO', '=', 'elector_details.pc_no'],['m_ac.AC_NO', '=', 'elector_details.ac_no']])
				   ->where('m_ac.st_code','=',$stcode) 
		    	 ->where('m_ac.PC_NO','=',$pc_no)
		    	 ->select('elector_details.*','m_ac.AC_NO','m_ac.AC_NAME')->groupBy('m_ac.AC_NO', 'm_ac.AC_NAME','elector_details.ac_no')->get(); 
		    }else{
		   $getAcListByPCNo = DB::table('m_ac')->where('ST_CODE', $stcode)->where('PC_NO',$pc_no)->orderBy('AC_NO', 'asc')->get();
		   }
		//dd(DB::getQueryLog());
		return $getAcListByPCNo;
		}
			
		
		
}
?>